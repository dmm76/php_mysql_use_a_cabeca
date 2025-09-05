<?php

if (isset($_POST['submit'])) {
    // include_once("Ex01/includes/menu.php");

    $bd = mysqli_connect("localhost", "root", "Debase33@", "elvis_store") or die("Erro ao conectar ao banco");

    $from = 'elmer@makeelvis.com';
    $subject = $_POST['subject'];
    $text = $_POST['elvismail'];
    $output_form = false;

    if (empty($subject) && empty($text)) {
        echo "Você esqueceu o assunto e o corpo da mensagem <br>";
        $output_form = true;
    }

    if (empty($subject) && (!empty($text))) {
        echo "Você esqueceu o assunto da mensagem <br>";
        $output_form = true;
    }

    if ((!empty($subject)) && empty($text)) {
        echo "Você esqueceu o texto da mensagem <br>";
        $output_form = true;
    }

    if ((!empty($subject)) && (!empty($text))) {
        $sql = "select * from email_list";

        $result = mysqli_query($bd, $sql);

        while ($row = mysqli_fetch_array($result)) {
            $nome = $row['first_name'];
            $sobrenome = $row['last_name'];
            $to = $row['email'];

            $msg = "Dear $nome $sobrenome, obrigado, eu te amo, sinto muito, eu agradeço";

            mail($to, $subject, $text, 'From: ' . $from);
            echo "Email send to $to <br>";
        }

        echo "Tudo certo";

        mysqli_close($bd);
    }
} else {
    $output_form = true;
}

if ($output_form) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
        <title>Envia Email</title>
    </head>

    <body>

        <div class="container">
            <div class="card mt-3">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="card-body">
                        <h2>MakeElvis.Com</h2>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="subject">Assunto:</label>
                                <input class="form-control" type="text" name="subject" placeholder="digite o assunto"
                                    value="<?php if (isset($_POST['submit'])) echo $subject; ?>" size="60" />
                            </div>
                            <div class="col-md-8">
                                <label for="">Corpo do Texto:</label>
                                <textarea class="form-control" name="elvismail" rows="8" cols="60"
                                    placeholder="Digite seu texto"><?php if (isset($_POST['submit'])) echo $text; ?></textarea><br />
                            </div>

                            <div class="col-md-3 mt-4">
                                <input type="submit" name="submit" value="Submit" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>

    </html>

<?php
}
?>