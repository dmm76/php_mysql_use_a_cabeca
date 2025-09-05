<?php
require_once('startsession.php');
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Editar perfil';
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

$user_id = (int)$_SESSION['user_id'];
$err = '';
$ok = '';

/** Carrega dados atuais */
$stmt = mysqli_prepare($bd, "SELECT username, first_name, last_name, gender, birthdate, city, state, picture FROM mismatch_user WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$current = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$current) {
    echo '<div class="container py-4"><div class="alert alert-warning">Usuário não encontrado.</div></div>';
    require_once('footer.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? $current['username']);
    $first    = trim($_POST['first_name'] ?? '');
    $last     = trim($_POST['last_name'] ?? '');
    $gender   = $_POST['gender'] ?? 'O';
    $birth    = trim($_POST['birthdate'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $state    = trim($_POST['state'] ?? '');
    $newpass  = trim($_POST['new_password'] ?? '');

    // valida data (YYYY-MM-DD) ou vazio
    if ($birth !== '') {
        $ts = strtotime($birth);
        if ($ts === false) $err = 'Data inválida. Use AAAA-MM-DD.';
        else $birth = date('Y-m-d', $ts);
    } else {
        $birth = null;
    }

    // Upload opcional
    $newPicRel = null;
    if (!$err && isset($_FILES['picture']) && $_FILES['picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['picture'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $err = 'Falha no upload (código ' . $f['error'] . ').';
        } elseif ($f['size'] > MM_MAXFILESIZE) {
            $err = 'Imagem maior que o limite (' . (int)(MM_MAXFILESIZE / 1024 / 1024) . 'MB).';
        } else {
            $info = @getimagesize($f['tmp_name']);
            $mimeOk = $info && in_array($info['mime'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true);
            if (!$mimeOk) {
                $err = 'A imagem deve ser JPG, PNG, GIF ou WEBP.';
            } else {
                // garante diretório
                if (!is_dir(MM_UPLOADPATH)) @mkdir(MM_UPLOADPATH, 0775, true);
                $ext = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp'
                ][$info['mime']];
                $name = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $dest = MM_UPLOADPATH . $name;

                if (!move_uploaded_file($f['tmp_name'], $dest)) {
                    $err = 'Não foi possível salvar a imagem.';
                } else {
                    $newPicRel = $name;
                    // remove antiga se houver
                    if (!empty($current['picture']) && is_file(MM_UPLOADPATH . $current['picture'])) {
                        @unlink(MM_UPLOADPATH . $current['picture']);
                    }
                }
            }
        }
    }

    if (!$err) {
        $sql = "UPDATE mismatch_user
               SET username=?, first_name=?, last_name=?, gender=?, birthdate=?, city=?, state=?";
        $types = 'sssssss';
        $vals  = [$username, $first, $last, $gender, $birth, $city, $state];

        if ($newPicRel !== null) {
            $sql .= ", picture=?";
            $types .= 's';
            $vals[] = $newPicRel;
            $current['picture'] = $newPicRel;
        }
        if ($newpass !== '') {
            // compatível com o livro (SHA1). Ideal migrar p/ password_hash().
            $sql .= ", password=SHA1(?)";
            $types .= 's';
            $vals[] = $newpass;
        }

        $sql .= " WHERE user_id=?";
        $types .= 'i';
        $vals[] = $user_id;

        $stmt = mysqli_prepare($bd, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$vals);

        if (mysqli_stmt_execute($stmt)) {
            $ok = 'Perfil atualizado com sucesso.';
            // se username mudou, mantenha coerência com a UI que usa cookie
            if ($username !== ($current['username'] ?? '')) {
                setcookie('username', $username, time() + 86400, '/', '', false, true);
            }
            // atualiza base local para repintar
            $current['username']   = $username;
            $current['first_name'] = $first;
            $current['last_name']  = $last;
            $current['gender']     = $gender;
            $current['birthdate']  = $birth;
            $current['city']       = $city;
            $current['state']      = $state;
        } else {
            $err = 'Não foi possível salvar. ' . $bd->error;
        }
        mysqli_stmt_close($stmt);
    }
}
$imgPath = (!empty($current['picture']) && is_file(MM_UPLOADPATH . $current['picture']))
    ? (MM_UPLOADPATH . $current['picture'])
    : (MM_UPLOADPATH . 'nopic.jpg');
?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-3">Editar meu perfil</h1>

                    <?php if ($err): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
                    <?php elseif ($ok): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($ok) ?></div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="Foto"
                            style="width:120px;height:120px;object-fit:cover;border-radius:50%;border:3px solid #eee;">
                        <div class="text-muted small">Foto atual</div>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control"
                                    value="<?= htmlspecialchars($current['username']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nova senha (opcional)</label>
                                <input type="password" name="new_password" class="form-control"
                                    placeholder="Deixe em branco para manter">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nome</label>
                                <input type="text" name="first_name" class="form-control"
                                    value="<?= htmlspecialchars($current['first_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sobrenome</label>
                                <input type="text" name="last_name" class="form-control"
                                    value="<?= htmlspecialchars($current['last_name'] ?? '') ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Gênero</label>
                                <select name="gender" class="form-select">
                                    <?php $g = $current['gender'] ?? 'O'; ?>
                                    <option value="M" <?= $g === 'M' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="F" <?= $g === 'F' ? 'selected' : '' ?>>Feminino</option>
                                    <option value="O" <?= $g === 'O' ? 'selected' : '' ?>>Outro/Não informar</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nascimento</label>
                                <input type="date" name="birthdate" class="form-control"
                                    value="<?= htmlspecialchars($current['birthdate'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Foto (JPG/PNG/GIF/WEBP, até
                                    <?= (int)(MM_MAXFILESIZE / 1024 / 1024) ?>MB)</label>
                                <input type="file" name="picture" class="form-control"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="city" class="form-control"
                                    value="<?= htmlspecialchars($current['city'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado (UF)</label>
                                <input type="text" name="state" class="form-control"
                                    value="<?= htmlspecialchars($current['state'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-primary">Salvar</button>
                            <a class="btn btn-outline-secondary" href="viewprofile.php">Ver meu perfil</a>
                            <a class="btn btn-outline-secondary" href="index.php">Home</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
mysqli_close($bd);
require_once('footer.php');
