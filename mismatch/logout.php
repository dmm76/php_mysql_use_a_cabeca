<?php
session_start();

/* Evita cache da página anterior */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/* Apaga cookies com o MESMO path usado no login */
setcookie('user_id',  '', time() - 3600, '/', '', false, true);
setcookie('username', '', time() - 3600, '/', '', false, true);

/* Limpa sessão (se estiver usando) */
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

/* Também remove da superglobal para esta requisição */
unset($_COOKIE['user_id'], $_COOKIE['username']);

/* Redireciona ao login */
header('Location: login.php?msg=Você saiu do sistema.');
exit;
