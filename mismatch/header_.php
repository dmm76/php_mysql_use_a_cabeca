<?php
if (!isset($page_title)) $page_title = 'MisMatch';
$base = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // ex.: /php_use_a_cabeca/mismatch
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($page_title) ?> — MisMatch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/css/app.css"> <!-- 👈 caminho relativo correto -->
</head>

<body class="bg-body">