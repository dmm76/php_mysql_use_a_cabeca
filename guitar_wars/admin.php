<?php
require_once('utils/authorize.php');
require_once('utils/appvars.php');
require_once('utils/connectvars.php');

$bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$sql = "SELECT * FROM guitarwars ORDER BY id, data ASC";
$data = mysqli_query($bd, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Página de Administração</title>
</head>

<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title mb-3">Página de Administração</h4>
                <p class="text-muted">
                    Marque uma ou mais opções para remover ou aprovar um score.
                </p>
                <a href="index.php" class="btn btn-danger">Voltar</a>
                <hr />

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <th>Data</th>
                                <th>Score</th>
                                <th>Screenshot</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_array($data)) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['data']) ?></td>
                                    <td><?= htmlspecialchars($row['score']) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($row['screenshot']) ?>" target="_blank">Ver</a>
                                    </td>
                                    <td>
                                        <a href="removescore.php?id=<?= urlencode($row['id']) ?>&data=<?= urlencode($row['data']) ?>&name=<?= urlencode($row['name']) ?>&score=<?= urlencode($row['score']) ?>&screenshot=<?= urlencode($row['screenshot']) ?>"
                                            class="btn btn-sm btn-danger">
                                            Remover
                                        </a>
                                        <?php if ($row['approved'] == 0) : ?>
                                            <a href="aprovarscore.php?id=<?= urlencode($row['id']) ?>&data=<?= urlencode($row['data']) ?>&name=<?= urlencode($row['name']) ?>&score=<?= urlencode($row['score']) ?>&screenshot=<?= urlencode($row['screenshot']) ?>"
                                                class="btn btn-sm btn-success">
                                                Aprovar
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>

</html>