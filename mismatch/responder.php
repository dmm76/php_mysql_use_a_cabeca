<?php
session_start();
if (empty($_COOKIE['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = (int)$_COOKIE['user_id'];
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST: responses[topic_id] = 0|1
    if (!empty($_POST['responses']) && is_array($_POST['responses'])) {
        $stmt = $mysqli->prepare("INSERT INTO mismatch_response (user_id, topic_id, response)
                              VALUES (?, ?, ?)
                              ON DUPLICATE KEY UPDATE response = VALUES(response)");
        foreach ($_POST['responses'] as $topicId => $val) {
            $resp = (int)!!$val;
            $tid  = (int)$topicId;
            $stmt->bind_param('iii', $userId, $tid, $resp);
            $stmt->execute();
        }
        $stmt->close();
    }
    header('Location: matches.php');
    exit;
}

// GET: listar tópicos
$topics = $mysqli->query("SELECT t.topic_id, t.name,
 COALESCE(r.response, -1) AS current
 FROM mismatch_topic t
 LEFT JOIN mismatch_response r
   ON r.topic_id = t.topic_id AND r.user_id = $userId
 ORDER BY t.topic_id");
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Responder tópicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h1 class="h4 mb-3">Suas preferências</h1>
        <form method="post">
            <div class="list-group">
                <?php while ($row = $topics->fetch_assoc()): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div><?= htmlspecialchars($row['name']) ?></div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="responses[<?= $row['topic_id'] ?>]"
                                id="d<?= $row['topic_id'] ?>" value="0" <?= $row['current'] === '0' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="d<?= $row['topic_id'] ?>">Não curto</label>

                            <input type="radio" class="btn-check" name="responses[<?= $row['topic_id'] ?>]"
                                id="l<?= $row['topic_id'] ?>" value="1" <?= $row['current'] === '1' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="l<?= $row['topic_id'] ?>">Curto</label>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <button class="btn btn-primary mt-3">Salvar</button>
            <a class="btn btn-outline-secondary mt-3" href="index.php">Voltar</a>
        </form>
    </div>
</body>

</html>