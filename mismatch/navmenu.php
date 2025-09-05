<?php
$logged = !empty($_SESSION['user_id']) || !empty($_COOKIE['user_id']);
$username = $_SESSION['username'] ?? ($_COOKIE['username'] ?? 'Visitante');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">MisMatch</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nmv" aria-controls="nmv"
            aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="nmv" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($logged): ?>
                <li class="nav-item"><a class="nav-link" href="responder.php">Preferências</a></li>
                <li class="nav-item"><a class="nav-link" href="matches.php">MisMatches</a></li>
                <li class="nav-item"><a class="nav-link" href="viewprofile.php">Meu Perfil</a></li>
                <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Entrar</a></li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <span class="navbar-text text-white-50 small d-none d-md-inline">Olá,
                    <?= htmlspecialchars($username) ?></span>
                <?php if ($logged): ?>
                <form action="logout.php" method="post" class="m-0">
                    <button class="btn btn-sm btn-light">Sair</button>
                </form>
                <?php else: ?>
                <a class="btn btn-sm btn-outline-light" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Hero sutil em todas as páginas (opcional) -->
<section class="hero-band">
    <div class="container py-4">
        <h1 class="display-6 mb-0"><?= htmlspecialchars($page_title) ?></h1>
        <p class="text-white-50 mb-0">Onde os opostos se atraem.</p>
    </div>
</section>