<?php
/* Evita cache */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once('startsession.php');
$page_title = 'Suas preferências';
require_once('header.php');

require_once('includes/appvars.php');
require_once('includes/connectvars.php');
require_once('navmenu.php');

/* exige login (opcional usar includes/authorize.php) */
if (empty($_SESSION['user_id'])) {
    echo '<div class="container py-4"><div class="alert alert-warning">Faça login para responder suas preferências.</div></div>';
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

/* Salvar (POST) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['responses']) && is_array($_POST['responses'])) {
    $stmt = mysqli_prepare(
        $bd,
        "INSERT INTO mismatch_response (user_id, topic_id, response)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE response = VALUES(response)"
    );
    foreach ($_POST['responses'] as $topic_id => $val) {
        $tid  = (int)$topic_id;
        $resp = (int)!!$val; // 0/1
        mysqli_stmt_bind_param($stmt, 'iii', $user_id, $tid, $resp);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
    header('Location: matches.php');
    exit;
}

/* Listar tópicos + resposta atual */
$sql = "SELECT t.topic_id, t.name,
               COALESCE(r.response, -1) AS current
          FROM mismatch_topic t
     LEFT JOIN mismatch_response r
            ON r.topic_id = t.topic_id AND r.user_id = $user_id
      ORDER BY t.topic_id";
$data = mysqli_query($bd, $sql);
?>
<div class="container py-4">
    <h4 class="mb-3">Suas preferências</h4>

    <?php if ($data && mysqli_num_rows($data) > 0): ?>
        <form method="post" class="card shadow-sm">
            <div class="list-group list-group-flush">
                <?php while ($row = mysqli_fetch_assoc($data)): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="me-3"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="btn-group" role="group" aria-label="Escolha">
                            <?php
                            $noChecked  = ((string)$row['current'] === '0') ? 'checked' : '';
                            $yesChecked = ((string)$row['current'] === '1') ? 'checked' : '';
                            $tid = (int)$row['topic_id'];
                            ?>
                            <input class="btn-check" type="radio" name="responses[<?= $tid ?>]" id="no<?= $tid ?>" value="0"
                                <?= $noChecked ?>>
                            <label class="btn btn-outline-secondary" for="no<?= $tid ?>">Não curto</label>

                            <input class="btn-check" type="radio" name="responses[<?= $tid ?>]" id="yes<?= $tid ?>" value="1"
                                <?= $yesChecked ?>>
                            <label class="btn btn-outline-primary" for="yes<?= $tid ?>">Curto</label>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="card-body">
                <button class="btn btn-primary">Salvar</button>
                <a class="btn btn-outline-secondary ms-2" href="index.php">Cancelar</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-info">Nenhum tópico cadastrado.</div>
    <?php endif; ?>
</div>
<?php
if ($data) mysqli_free_result($data);
mysqli_close($bd);
require_once('footer.php');
