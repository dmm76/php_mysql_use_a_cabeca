<?php
require_once('utils/appvars.php');
require_once('utils/connectvars.php');

$bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$sql = $sql = "SELECT * FROM guitarwars WHERE approved = 1 ORDER BY score DESC";
$data = mysqli_query($bd, $sql);

// consulta separada só para o top score
$sqlTop = "SELECT name, score FROM guitarwars WHERE approved = 1 ORDER BY score DESC LIMIT 1";;
$topRes = mysqli_query($bd, $sqlTop);
$topRow = $topRes ? mysqli_fetch_assoc($topRes) : null;
$topName  = $topRow['name']  ?? '';
$topScore = (int)($topRow['score'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>GuitarWars</title>
</head>

<body class="bg-info">
    <!-- <?php include_once("utils/menu.php"); ?> -->
    <div class="container">
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Guitar Wars</h5>
                <p class="card-text">Bem vindo, intrépido guitarrista! Voçê é bom o suficiente para entrar na lista de
                    recordes do Guitar Wars? Se for, clique aqui para adicionar a sua pontuacao.</a></p>
                <a href="addscore.php" class="btn btn-primary" name='submit'>Adicionar Score</a>
                <a href="admin.php" class="btn btn-danger" name='submit'>Admin Score</a>
                <hr />
                <div class="alert alert-success text-center fw-bold">
                    🏆 TOP SCORE: <?= $topScore ?> — <?= htmlspecialchars($topName) ?>
                </div>
                <div class="row">
                    <table class="table table-bordered table-hover table-sm mt-3">

                        <tr>
                            <th class="text-center">Score:</th>
                            <th class="text-center">Nome:</th>
                            <th class="text-center">Data</th>
                            <th class="text-center">Imagem</th>
                            <th class="text-center">Ações</th>
                        </tr>
                        <?php while ($row = mysqli_fetch_assoc($data)) : ?>
                            <tr>
                                <td class="text-center"><?= (int)$row['score'] ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="text-center">
                                    <?= htmlspecialchars($row['data']) ?></td>
                                <td class="text-center">
                                    <?php
                                    $img = $row['screenshot'];
                                    if (!empty($img) && is_file($img) && filesize($img) > 0) {
                                        echo '<img src="' . htmlspecialchars($img) . '" alt="Score Image" style="width:75px;height:75px" class="img-thumbnail">';
                                    } else {
                                        echo '<img src="img/score01.jpg" alt="Score Image" style="width:75px;height:75px" class="img-thumbnail">';
                                    }
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile;
                        mysqli_close($bd);
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>