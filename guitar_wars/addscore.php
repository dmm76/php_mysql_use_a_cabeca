<?php
require_once('utils/appvars.php');
require_once('utils/connectvars.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $score = trim($_POST['score'] ?? '');
    $f     = $_FILES['screenshot'] ?? null;

    if ($name !== '' && $score !== '' && $f) {
        // 1) cheque o erro de upload antes de tudo
        if ($f['error'] !== UPLOAD_ERR_OK) {
            echo "<p class='error'>Erro no upload (código {$f['error']}).</p>";
            exit;
        }

        // (opcional, mas útil) garante que é imagem
        $gi = @getimagesize($f['tmp_name']);
        if ($gi === false) {
            echo "<p class='error'>O arquivo enviado não é uma imagem válida.</p>";
            exit;
        }

        // 2) destino e nome do arquivo
        if (!is_dir(__DIR__ . '/' . GW_UPLOADPATH)) {
            mkdir(__DIR__ . '/' . GW_UPLOADPATH, 0775, true);
        }
        $nomeOriginal = basename($f['name']);
        $targetRel = GW_UPLOADPATH . $nomeOriginal;      // caminho salvo no BD
        $targetAbs = __DIR__ . '/' . $targetRel;         // caminho físico

        // (simples) se quiser evitar sobrescrever, acrescente um sufixo:
        // $ext = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        // $base = pathinfo($nomeOriginal, PATHINFO_FILENAME);
        // $targetRel = GW_UPLOADPATH . $base . '_' . time() . '.' . $ext;
        // $targetAbs = __DIR__ . '/' . $targetRel;

        // 3) move e grava no BD
        if (move_uploaded_file($f['tmp_name'], $targetAbs)) {
            $bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            // salve o CAMINHO (targetRel), não só o nome
            $sql = "INSERT INTO guitarwars (name, score, screenshot) 
                    VALUES ('$name', '$score', '$targetRel')";
            mysqli_query($bd, $sql);
            mysqli_close($bd);

            echo '<p>Obrigado por adicionar o seu score</p>';
            echo '<p><strong>NOME:</strong> ' . htmlspecialchars($name) . '<br/>';
            echo '<strong>SCORE:</strong> ' . (int)$score . '</p>';
            echo '<strong>IMAGEM:</strong> ' . htmlspecialchars($targetRel) . '</p>';
            echo '<p><a href="index.php">&lt;&lt; Voltar para a lista de records.</a></p>';
        } else {
            echo '<p class="error">Falha ao mover o arquivo (verifique permissões da pasta img/).</p>';
        }
    } else {
        echo '<p class="error">Preencha nome, score e selecione uma imagem.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Score</title>
</head>

<body class="bg-info">
    <div class="container">
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Guitar Wars - Add Score</h5>
                <p class="card-text">Bem vindo, adicione seu score.</a></p>
                <a href="index.php" class="btn btn-secondary" name='submit'>Ver Score</a>
                <hr />
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <input type="hidden" name="MAX_FILE_SIZE" value="GW_MAXFILESIZE" />
                        <div class="col">
                            <label for="name">Nome:</label>
                            <input type="text" class="form-control" name="name" placeholder="Digite seu nome"
                                aria-label="Nome">
                        </div>
                        <div class="col">
                            <label for="score">Score:</label>
                            <input type="text" class="form-control" name="score" placeholder="Digite seu score"
                                aria-label="Score">
                        </div>
                        <div class="col">
                            <label for="screenshot">Captura de Tela:</label>
                            <input type="file" class="form-control" id="screenshot" name="screenshot" />
                        </div>
                    </div>
                    <div class="row">

                        <div class="col mt-3">
                            <input type="submit" class="btn btn-primary" value="Adicionar" name='submit'>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</body>

</html>