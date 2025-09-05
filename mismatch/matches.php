<?php
session_start();
if (empty($_COOKIE['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = (int)$_COOKIE['user_id'];
require 'db.php';

/* 
Score = soma de respostas onde eles discordam:
- r1 = respostas de OUTROS usuários
- r2 = SUAS respostas
*/
$sql = "
SELECT u.user_id, u.username,
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
LIMIT 20";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $userId, $userId);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Seus MisMatches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h1 class="h4 mb-3">Seus melhores “opostos”</h1>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Diferenças</th>
                        <th>Respondidos em comum</th>
                        <th>% Diferenças</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $res->fetch_assoc()):
                        $pct = $row['total_topics'] ? round(100 * $row['mismatch_score'] / $row['total_topics']) : 0; ?>
                        <tr>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= (int)$row['mismatch_score'] ?></td>
                            <td><?= (int)$row['total_topics'] ?></td>
                            <td><?= $pct ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a class="btn btn-outline-secondary" href="responder.php">Editar preferências</a>
        <a class="btn btn-outline-primary ms-2" href="index.php">Home</a>
    </div>
</body>

</html>