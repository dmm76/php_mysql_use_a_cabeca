<?php
require_once('utils/authorize.php');
require_once('../guitar_wars/utils/appvars.php');
require_once('../guitar_wars/utils/connectvars.php');

if (isset($_GET['id']) && isset($_GET['data']) && isset($_GET['name']) && isset($_GET['score']) && isset($_GET['screenshot'])) {

    $id = $_GET['id'];
    $data = $_GET['data'];
    $name = $_GET['name'];
    $score = $_GET['score'];
    $screenshot = $_GET['screenshot'];
} else if (isset($_POST['id']) && isset($_POST['data']) && isset($_POST['name']) && isset($_POST['score'])) {

    $id = $_POST['id'];
    $data = $_POST['data'];
    $name = $_POST['name'];
    $score = $_POST['score'];
} else {
    echo '<p classe="error">Desculpe, nenhuma pontuação foi especificada para ser removida</p>';
}

if (isset($_POST['submit'])) {
    if ($_POST['confirm'] == 'Yes') {

        @unlink(GW_UPLOADPATH . $screenshot);

        $bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "DELETE FROM guitarwars WHERE id = $id LIMIT 1";

        mysqli_query($bd, $sql);

        mysqli_close($bd);
        echo "<p>A pontuacao de $name foi removida com sucesso!</p>";
    } else {
        echo "<p class='error'>A pontuacao não removida!</p>";
    }
}


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

<body>
    <div class="container">
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Guitar Wars - Remove a High Score</h5>
                <p class="card-text">Texto posterior</p>
                <hr />

                <p>Tem certeza que deseja apagar a pontuação abaixo?</p>
                <p><strong>Nome: </strong> '<?php $name ?>' <br /> <strong>Date: </strong> '<?php $data ?>'<br>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="radio" name="confirm" value="Yes" />YES
                    <input type="radio" name="confirm" value="No" checked="checked" />NO <br>
                    <input type="submit" value="submit" name="submit">
                    <input type="" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="" name="data" value="<?= htmlspecialchars($data) ?>">
                    <input type="" name="name" value="<?= htmlspecialchars($name) ?>">
                    <input type="" name="score" value="<?= htmlspecialchars($score) ?>">
                    <input type="" name="screenshot" value="<?= htmlspecialchars($screenshot) ?>">
                </form>

                <p><a href="admin.php">$lt$lt; Voltar para a página de admin</a></p>;
            </div>
        </div>
    </div>
</body>

</html>