<?php
// login.php
session_start();

$erro_msg = "";
$user_username = "";

/* Se já está logado, manda pra index */
if (!empty($_COOKIE['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = mysqli_connect("localhost", "root", "Debase33@", "mismatch");

    if (!$db) {
        $erro_msg = "Erro ao conectar ao banco.";
    } else {
        $user_username = trim($_POST['username'] ?? '');
        $user_password = trim($_POST['password'] ?? '');

        if ($user_username !== '' && $user_password !== '') {
            $sql = "SELECT user_id, username
                      FROM mismatch_user
                     WHERE username = ?
                       AND password = SHA1(?)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $user_username, $user_password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);

                // Cookies por 1 dia — **path '/'** para garantir que logout apague em qualquer subpasta
                setcookie('user_id',  $row['user_id'],  time() + 86400, '/', '', false, true);
                setcookie('username', $row['username'], time() + 86400, '/', '', false, true);

                header('Location: index.php');
                exit;
            } else {
                $erro_msg = "Nome de usuário ou senha inválidos.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $erro_msg = "Digite seu nome de usuário e senha.";
        }

        mysqli_close($db);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>MisMatch - Login</title>
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="h4 mb-3">MisMatch</h1>

                        <?php if ($erro_msg): ?>
                            <div class="alert alert-danger py-2" role="alert">
                                <?= htmlspecialchars($erro_msg, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <fieldset>
                                <legend class="fs-6 text-secondary">Login</legend>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Nome do Usuário</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="<?= htmlspecialchars($user_username, ENT_QUOTES, 'UTF-8'); ?>" required
                                        autocomplete="username" />
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="password" name="password" required
                                        autocomplete="current-password" />
                                </div>

                                <button type="submit" name="submit" class="btn btn-primary w-100">Entrar</button>
                            </fieldset>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>