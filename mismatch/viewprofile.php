<?php
require_once('startsession.php');
$page_title = 'Perfil';
require_once('header.php');

require_once('includes/appvars.php');
require_once('includes/connectvars.php');
require_once('navmenu.php');

$bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$bd) {
    echo '<div class="container py-4"><div class="alert alert-danger">Erro ao conectar ao banco.</div></div>';
    require_once('footer.php');
    exit;
}

$viewer_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$view_id   = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $viewer_id;

$stmt = mysqli_prepare($bd, "SELECT user_id, username, first_name, last_name, gender, birthdate, city, state, picture, join_date FROM mismatch_user WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $view_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$user) {
    echo '<div class="container py-4"><div class="alert alert-warning">Perfil não encontrado.</div></div>';
    require_once('footer.php');
    exit;
}

$full = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$full = $full !== '' ? $full : $user['username'];
$pic  = $user['picture'] ?? '';
$img  = (is_file(MM_UPLOADPATH . $pic) && filesize(MM_UPLOADPATH . $pic) > 0)
    ? (MM_UPLOADPATH . $pic)
    : (MM_UPLOADPATH . 'nopic.jpg');

function labelGender($g)
{
    if ($g === 'M') return 'Masculino';
    if ($g === 'F') return 'Feminino';
    return 'Outro/Não informar';
}
?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Foto de perfil"
                            style="width:140px;height:140px;object-fit:cover;border-radius:50%;border:3px solid #eee;">
                        <div>
                            <h1 class="h4 mb-1"><?= htmlspecialchars($full) ?></h1>
                            <div class="text-muted">@<?= htmlspecialchars($user['username']) ?></div>
                            <div class="small text-muted">Membro desde
                                <?= $user['join_date'] ? date('d/m/Y', strtotime($user['join_date'])) : '—' ?></div>
                        </div>
                    </div>

                    <hr>

                    <dl class="row">
                        <dt class="col-sm-3">Gênero</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars(labelGender($user['gender'])) ?></dd>

                        <dt class="col-sm-3">Nascimento</dt>
                        <dd class="col-sm-9">
                            <?= $user['birthdate'] ? date('d/m/Y', strtotime($user['birthdate'])) : '—' ?></dd>

                        <dt class="col-sm-3">Cidade/UF</dt>
                        <dd class="col-sm-9">
                            <?= htmlspecialchars(($user['city'] ?: '—') . (($user['state'] ?? '') ? ' / ' . $user['state'] : '')) ?>
                        </dd>
                    </dl>

                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-secondary" href="index.php">Home</a>
                        <?php if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$user['user_id']): ?>
                            <a class="btn btn-primary" href="editprofile.php">Editar meu perfil</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
mysqli_close($bd);
require_once('footer.php');
