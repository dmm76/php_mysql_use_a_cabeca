<?php
/* Evita cache */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/** Sessão + título + header */
require_once 'startsession.php';
$page_title = 'MisMatch - Login';
require_once 'header.php';

/** Constantes e navegação */
require_once 'includes/appvars.php';
require_once 'includes/connectvars.php';
require_once 'navmenu.php';

/* Se já está logado, manda pra home */
if (!empty($_COOKIE['user_id'])) {
    header('Location: index.php');
    exit;
}

/* Mensagens */
$erro_msg = '';
$user_username = '';
$flash = isset($_GET['msg']) ? trim($_GET['msg']) : '';

/* Processa submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$bd) {
        $erro_msg = 'Erro ao conectar ao banco.';
    } else {
        mysqli_set_charset($bd, 'utf8mb4');

        $user_username = trim($_POST['username'] ?? '');
        $user_password = trim($_POST['password'] ?? '');

        if ($user_username !== '' && $user_password !== '') {
            $sql = "SELECT user_id, username
                FROM mismatch_user
               WHERE username = ?
                 AND password = SHA1(?)";
            $stmt = mysqli_prepare($bd, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $user_username, $user_password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);

                // Cookies por 1 dia (path '/' para funcionar no site todo)
                setcookie('user_id',  $row['user_id'],  time() + 86400, '/', '', false, true);
                setcookie('username', $row['username'], time() + 86400, '/', '', false, true);

                mysqli_stmt_close($stmt);
                mysqli_close($bd);

                header('Location: index.php');
                exit;
            } else {
                $erro_msg = 'Nome de usuário ou senha inválidos.';
            }

            if ($result) mysqli_free_result($result);
            mysqli_stmt_close($stmt);
        } else {
            $erro_msg = 'Digite seu nome de usuário e senha.';
        }

        mysqli_close($bd);
    }
}
?>

<main class="container-lg flex-grow-1 py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-3">Entrar no MisMatch</h1>

                    <?php if ($flash): ?>
                        <div class="alert alert-success py-2"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>

                    <?php if ($erro_msg): ?>
                        <div class="alert alert-danger py-2"><?= htmlspecialchars($erro_msg, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                        <fieldset>
                            <legend class="fs-6 text-secondary">Login</legend>

                            <div class="mb-3">
                                <label for="username" class="form-label">Nome do Usuário</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= htmlspecialchars($user_username, ENT_QUOTES, 'UTF-8') ?>" required
                                    autocomplete="username">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    autocomplete="current-password">
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary w-100">Entrar</button>
                        </fieldset>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>