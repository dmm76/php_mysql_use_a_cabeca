<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
   <div class="container-fluid container">
      <a class="navbar-brand" href="index.php">Sistemas Hospitalar</a> <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button> 
      <div class="collapse navbar-collapse" id="navbarCollapse">
         <ul class="navbar-nav me-auto mb-2 mb-md-0">
            <?php

               if ($_SESSION['nivel']=='recepcao') {
                  echo '<li class="nav-item"><a class="nav-link" aria-current="page" href="index.php">Home</a></li>';
                  echo '<li class="nav-item"><a class="nav-link" aria-current="page" href="pacientes.php">Pacientes</a></li>';
                  echo '<li class="nav-item"><a class="nav-link" aria-current="page" href="atendimentos.php">Atendimentos</a></li>';
               }

               if ($_SESSION['nivel']=='enfermeiro') {
                  echo '<li class="nav-item"> <a class="nav-link" aria-current="page" href="preconsulta.php">Pré-consulta</a></li>';
               }

               if ($_SESSION['nivel']=='medico') {
                  echo '<li class="nav-item"> <a class="nav-link" aria-current="page" href="consulta.php">Consulta</a></li>';
               }

               if ($_SESSION['nivel']=='adm') {
                  echo '<li class="nav-item"><a class="nav-link" aria-current="page" href="index.php">Home</a></li>
                        <li class="nav-item"> <a class="nav-link" aria-current="page" href="usuarios.php">Usuários</a></li>
                        <li class="nav-item"> <a class="nav-link" aria-current="page" href="pacientes.php">Pacientes</a></li>
                        <li class="nav-item"> <a class="nav-link" aria-current="page" href="atendimentos.php">Atendimentos</a></li>
                        <li class="nav-item"> <a class="nav-link" aria-current="page" href="preconsulta.php">Pré-consulta</a></li>
                        <li class="nav-item"> <a class="nav-link" aria-current="page" href="consulta.php">Consulta</a></li>';
               }


            ?>
            
            <li class="nav-item"> <a class="nav-link" aria-current="page" href="includes/sair.php">Sair</a></li>
         </ul>
         <form class="d-flex" role="search"> <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"> <button class="btn btn-outline-success" type="submit">Search</button> </form>
      </div>
   </div>
</nav>