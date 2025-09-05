<?php
/* Evita cache */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once('startsession.php');
$page_title = 'Seus MisMatches';
require_once('header.php');

require_once('includes/appvars.php');
require_once('includes/connectvars.php');
require_once('navmenu.php');

/* exige login */
if (empty($_SESSION['user_id'])) {
    echo '<div class="container py-4"><div class="alert alert-warning">Faça login para ver seus MisMatches.</div></div>';
    require_once('footer.php');
    exit;
}

/* Conexão */
$bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$bd) {
    echo '<div class="container py-4"><div class="alert alert-danger">Erro ao conectar ao banco.</div></div>';
    require_once('footer.php');
    exit;
}
mysqli_set_charset($bd, 'utf8mb4');

$user_id = (int)$_SESSION['user_id'];

/* Ranking de diferenças (XOR) */
$sql = "
SELECT u.user_id,
       u.username,
       SUM(r1.response XOR r2.response) AS mismatch_score,
       COUNT(*) AS total_topics
  FROM mismatch_response r2
  JOIN mismatch_response r1
    ON r1.topic_id = r2.topic_id
  JOIN mismatch_user u
    ON u.user_id = r1.user_id
 WHERE r2.user_id = ?
   AND r1.user_id <> ?
 GROUP BY u.user_id, u.username
HAVING total_topics > 0
 ORDER BY mismatch_score DESC, total_topics DESC, u.username ASC
 LIMIT 50";

$stmt = mysqli_prepare($bd, $sql);
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>
<div class="container py-4">
    <h4 class="mb-3">Seus melhores “opostos”</h4>

    <?php if ($res && mysqli_num_rows($res) > 0): ?>
        <div class="table-responsive card shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuário</th>
                        <th>Diferenças</th>
                        <th>Tópicos em comum</th>
                        <th>% Diferenças</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)):
                        $score = (int)$row['mismatch_score'];
                        $total = (int)$row['total_topics'];
                        $pct   = $total ? round(100 * $score / $total) : 0;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= $score ?></td>
                            <td><?= $total ?></td>
                            <td><?= $pct ?>%</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary"
                                    href="viewprofile.php?user_id=<?= (int)$row['user_id'] ?>">
                                    Ver perfil
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            Responda alguns tópicos em <a href="responder.php">Preferências</a> para ver seus MisMatches!
        </div>
    <?php endif; ?>
</div>
<?php
if ($res) mysqli_free_result($res);
mysqli_stmt_close($stmt);
mysqli_close($bd);
require_once('footer.php');
