<?php
// include_once("Ex01/includes/menu.php");
$bd = mysqli_connect("localhost", "root", "Debase33@", "elvis_store") or die("Erro ao conectar ao banco");

$nome = $_POST['nome'];
$sobrenome = $_POST['sobrenome'];
$email = $_POST['email'];

if (!empty($nome)) {
    if (!empty($sobrenome)) {
        if (!empty($email)) {
            $sql = "insert into email_list(first_name, last_name, email) values ('$nome', '$sobrenome', '$email')";

            mysqli_query($bd, $sql);

            echo 'Cliente adicionado';

            mysqli_close($bd);
        }
    }
} else {
    echo "Preencha todos os campos";
}
