<?php

echo '<hr>';
if (isset($_SESSION['username'])) {
    echo '<a href="index.php">HOME</a> &#10084';
    echo '<a href="viewprofile.php">Ver Perfil</a> &#10084';
    echo '<a href="editprofile.php">Editar Perfil</a> &#10084';
    echo '<a href="logout.php">Sair (' . $_SESSION['username'] . ')</a>';
} else {
    echo '<a href="login.php">Log in</a> &#10084';
    echo '<a href="signup.php">Cadastrar-se</a> &#10084';
}
echo '<hr>';
