<?php
require_once "Database.php";



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $email = $_POST['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $nome = $_GET['nome'];
    $sobrenome = $_GET['sobrenome'];
    $email = $_GET['email'];
}

$con = new Database();
$con->insert($nome, $sobrenome, $email);


$res = $con->exibir();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Formulario de Email</title>
</head>

<body>

    <div class="container">
        <div class="card mt-3">

            <form action="" method="POST">
                <div class="card-body">
                    <h2>Formulario de Cadastro</h2>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Primeiro Nome:</label>
                            <input class="form-control" type="text" name="nome" placeholder="digite seu nome" required>
                        </div>
                        <div class="col-md-3">
                            <label for="">Sobrenome:</label>
                            <input class="form-control" type="text" name="sobrenome" placeholder="Digite seu sobrenome">
                        </div>
                        <div class="col-md-3">
                            <label for="">Email:</label>
                            <input class="form-control" type="email" name="email" placeholder="Digite seu email">
                        </div>
                        <div class="col-md-3 mt-4">
                            <button type="submit">Enviar</button>
                        </div>
                    </div>
                    <table class="table mt-3">
                        <thead>
                            <tr>

                                <th>Nome</th>
                                <th>Sobrenome</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($res as $row): ?>
                            <tr>

                                <td><?= $row['first_name'] ?></td>
                                <td><?= $row['last_name'] ?></td>
                                <td><?= $row['email'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>


                </div>
            </form>
        </div>
    </div>
</body>

</html>