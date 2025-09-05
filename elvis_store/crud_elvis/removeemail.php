<?php

$bd = mysqli_connect("localhost", "root", "Debase33@", "elvis_store") or die("Erro ao conectar ao banco");

if (isset($_POST['submit'])) {

    if (isset($_POST['toDelete'])) {
        foreach ($_POST['toDelete'] as $delete_id) {
            $sql = "delete from email_list where id=$delete_id";

            mysqli_query($bd, $sql);
        }
        echo "Usuario removido com sucesso";
    }
}

$query = "SELECT * FROM email_list";
$result = mysqli_query($bd, $query);
mysqli_close($bd);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <title>Remove Email</title>
</head>

<body>
    <div class="container">
        <div class="card mt-3">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="card-body">
                    <h2>MakeElvis.Com</h2>
                    <div class="row">
                        <h4>Marque uma ou mais caixa de dialogo para apagar o email desejado</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <?php
                                while ($row = mysqli_fetch_array($result)) {
                                    echo '<input type="checkbox" value="' . $row['id'] . '" name="toDelete[]"  />' . ' ';
                                    echo ' ' . $row['first_name'];
                                    echo ' ' . $row['last_name'];
                                    echo ' ' . $row['email'];
                                    echo '<br/>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3 mt-4">
                            <button type="submit" name="submit">Enviar</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>

</html>