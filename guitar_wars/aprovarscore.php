<?php
require_once('utils/authorize.php');
require_once('utils/appvars.php');
require_once('utils/connectvars.php');

$erro = '';
$mensagem = '';
$registro = [
    'id' => null,
    'name' => '',
    'score' => '',
];

// Se veio por GET (primeiro acesso), capturamos dados para exibir a confirmação
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $registro['id']    = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $registro['name']  = isset($_GET['name']) ? $_GET['name'] : '';
    $registro['score'] = isset($_GET['score']) ? $_GET['score'] : '';

    if (!$registro['id']) {
        $erro = 'ID inválido ou ausente.';
    }
}

// Se veio por POST (confirmação submetida), aprovamos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';

    if (!$id) {
        $erro = 'ID inválido.';
    } elseif ($confirm !== 'yes') {
        $erro = 'Ação cancelada pelo usuário.';
    } else {
        $bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (!$bd) {
            $erro = 'Falha na conexão com o banco: ' . mysqli_connect_error();
        } else {
            $stmt = mysqli_prepare($bd, "UPDATE guitarwars SET approved = 1 WHERE id = ?");
            if (!$stmt) {
                $erro = 'Erro ao preparar comando SQL: ' . mysqli_error($bd);
            } else {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                if (mysqli_stmt_execute($stmt)) {
                    // Opcional: recuperar name/score para mensagem bonita
                    $res = mysqli_query($bd, "SELECT name, score FROM guitarwars WHERE id = " . (int)$id);
                    if ($res && $row = mysqli_fetch_assoc($res)) {
                        $mensagem = 'A pontuação de ' . htmlspecialchars($row['score']) .
                            ' para ' . htmlspecialchars($row['name']) .
                            ' foi aprovada com sucesso.';
                    } else {
                        $mensagem = 'Pontuação aprovada com sucesso.';
                    }
                } else {
                    $erro = 'Erro ao aprovar: ' . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($bd);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Aprovar Score - GuitarWars</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title mb-3">Aprovar Pontuação</h4>

                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                    <a href="admin.php" class="btn btn-secondary">Voltar à página Admin</a>

                <?php elseif ($mensagem): ?>
                    <div class="alert alert-success"><?= $mensagem ?></div>
                    <a href="admin.php" class="btn btn-primary">Voltar à página Admin</a>

                <?php else: ?>
                    <p>Você tem certeza que deseja aprovar esta pontuação?</p>
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> <?= (int)$registro['id'] ?></li>
                        <li><strong>Nome:</strong> <?= htmlspecialchars($registro['name']) ?></li>
                        <li><strong>Score:</strong> <?= htmlspecialchars($registro['score']) ?></li>
                    </ul>

                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="id" value="<?= (int)$registro['id'] ?>">
                        <button type="submit" name="confirm" value="yes" class="btn btn-success">Sim, aprovar</button>
                        <a href="admin.php" class="btn btn-outline-secondary">Cancelar</a>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>

</html>