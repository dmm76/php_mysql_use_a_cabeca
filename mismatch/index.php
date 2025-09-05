<?php
/* Evita cache (senão o navegador mostra página antiga ao voltar) */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/** Sessão + cabeçalho **/
require_once('startsession.php');
$page_title = 'Onde os opostos se atraem';
require_once('header.php');

require_once('includes/appvars.php');
require_once('includes/connectvars.php');

/** Navbar (uma única vez) **/
require_once('navmenu.php');

/** Conexão DB **/
$bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$bd) {
    echo '<main class="container py-4"><div class="alert alert-danger">Erro ao conectar ao banco.</div></main>';
    require_once('footer.php');
    exit;
}

/** Consulta: últimos membros com first_name definido */
$sql = "SELECT user_id, first_name, picture
          FROM mismatch_user
         WHERE first_name IS NOT NULL
         ORDER BY join_date DESC
         LIMIT 8";
$data = mysqli_query($bd, $sql);
?>

<main class="container-lg flex-grow-1 py-5">
    <h4 class="mb-3">Membros mais novos</h4>

    <?php if ($data && mysqli_num_rows($data) > 0): ?>
        <div class="row g-3">
            <?php while ($row = mysqli_fetch_assoc($data)):
                $first = htmlspecialchars($row['first_name']);
                $uid   = (int)$row['user_id'];
                $pic   = $row['picture'] ?? '';
                $imgPath = (is_file(MM_UPLOADPATH . $pic) && filesize(MM_UPLOADPATH . $pic) > 0)
                    ? (MM_UPLOADPATH . $pic)
                    : (MM_UPLOADPATH . 'nopic.jpg');
            ?>
                <div class="col-6 col-md-3">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($imgPath) ?>" class="card-img-top" alt="<?= $first ?>">
                        <div class="card-body">
                            <h6 class="card-title mb-2"><?= $first ?></h6>
                            <?php if (!empty($_SESSION['user_id'])): ?>
                                <a class="btn btn-sm btn-outline-primary" href="viewprofile.php?user_id=<?= $uid ?>">Ver perfil</a>
                            <?php else: ?>
                                <a class="btn btn-sm btn-primary w-100" href="login.php">Fazer login</a>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Ainda não há membros para exibir.</div>
    <?php endif; ?>
</main>

<?php
if ($bd) {
    mysqli_close($bd);
}
require_once('footer.php');
