<?php
/* Evita cache (senão o navegador mostra página antiga ao voltar) */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/* Se não tem cookie de login, manda para login */
if (empty($_COOKIE['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_COOKIE['username'] ?? '';

//inicia a sesssao
require_once('startsession.php');

//insere o cabeçalho na pagina
$page_title = 'Onde os opostos se atraem';
require_once('header.php');

require_once('includes/appvars.php');
require_once('includes/connectvars.php');

//mostra a menu de navegacao
require_once('navmenu.php');

//conecta ao banco de dados
$bd = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//OBTEM OS DADOS DO USUARIO ATRAVEZ DO MYSQL
$sql = "select user_id, first_name, picture from mismatch_user where first_name is not null order by join_date desc limit 5";

$data = mysqli_query($bd, $sql);

//faz um loop atravez do array de dados formatando com html
echo '<h4>Membros mais novos:</h4>';
echo '<table>';

while ($row = mysqli_fetch_array($data)) {
    if (is_file(MM_UPLOADPATH . $row['picture']) && filesize(MM_UPLOADPATH . $row['picture']) > 0) {
        echo '<tr><td><img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="' . $row['first_name'] . '"/></td>';
    } else {
        echo '<tr><td><img src="' . MM_UPLOADPATH . 'nopic.jpg' . '" alt="' . $row['first_name'] . '"/></td>';
    }
    if (isset($_SESSION['user_id'])) {
        echo '<td><a href="viewprofile.php?user_id=' . $row['user_id'] . '">' . $row['first_name'] . '</a></td></tr>';
    } else {
        echo '<td>' . $row['first_name'] . '</td></tr>';
    }
}

echo '</table>';
?>
<?php
//insere o rodape

require_once('footer.php');
?>