<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COOSAJO R.L.</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="./lib/bootstrap-4.5.0/css/bootstrap.min.css">    

    <!-- Styles -->
    <link rel="stylesheet" href="./css/styles.css">   
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="/">COOSAJO R.L.</a> 
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">                
        <ul class="navbar-nav mr-auto">          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Opciones
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="/links">Consultar Ticket</a>            
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/links/add">Solicitar Ticket</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/links/add">Autenticarme</a>
            </div>
          </li>                         
        </ul>
        <form class="form-inline">
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </div>
  </nav>  

  <!-- ARCHIVOS CON FUNCIONALIDADES NECESARIAS -->
  <?php
    // Conexión a la BD de suporte técnico
    require '../vendor/support_db.php';
  ?>

  <div class="main">
    <div class="container">
      <div class="cards">
        <!-- Impresión de las tarjetas -->
        <?php
          if( isset($_GET["steps"]) ){
            SupportPrintStepCards($_GET["steps"]);
          }else{            
            SupportPrintSupportCards();
          }
          
        ?>      
      </div>      
    </div>
  </div>
    <!-- Bootstrap -->
    <script src="./lib/jquery-3.5.1/jquery-3.5.1.slim.min.js"></script>
    <script src="./lib/popper-1.16.0/popper.min.js"></script>
    <script src="./lib/bootstrap-4.5.0/js/bootstrap.min.js"></script>
</body>
</html>