<!-- ARCHIVOS CON FUNCIONALIDADES NECESARIAS -->
<?php
    session_start();
    // Si la sesión ya cuenta con un usuario logeado, debe redirigirse a la pagina de administrador
    if(isset($_SESSION["login_user"])) { 
        if($_SESSION["login_user"]==TRUE) {
            header('Location: admin_index.php');
        }
    }

    // Conexión a la BD de suporte técnico
    require '../vendor/support_db.php';    
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPPORT</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="lib/bootstrap-4.5.0/css/bootstrap.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">SUPPORT</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Opciones
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="index.php">Problemas técnicos</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="ticket.php">Consultar Ticket</a>
                            <a class="dropdown-item" href="ticket.php?new_ticket=TRUE">Solicitar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="auth.php">Autenticarme</a>
                        </div>
                    </li>
                </ul>
                <form class="form-inline" method="POST" action="index.php">
                    <input class="form-control mr-sm-2 typeahead" type="search" placeholder="Buscar" name="search" id="search" aria-label="Search" autocomplete="off">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Información de la seeción acutal -->
    <?php
        if( isset($_GET["support_id"]) ){
    ?>
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Pasos para solucionar tu problema</h1>
            <p class="lead">Está sección muestra los pasos que debes seguir para solucionar tu problema.</p>
        </div>
    </div>
    <?php
        }else{
    ?>
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Problemas técnicos</h1>
            <p class="lead">Está sección muestran los problemas que es podible solucionar con esta aplicación.</p>
        </div>
    </div>
    <?php                  
        }
    ?>

    <!-- Contenedor de las tarjetas -->
    <div class="main">
        <div class="container">
            <div class="cards">
                <!-- Impresión de las tarjetas -->
                <?php
                    if(isset($_POST["search"])){
                        SupportPrintSearchSupportCards(); 
                ?>
                <!-- Tarjeta para crear o consultar un ticket -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline"> ¿Encontro su problema? </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Sí tu problema no aparece, crea una ticket para ser atendido por un
                            técnico. </p>

                        <div class="text-center">
                            <a href="" class="btn btn-success px-4 mr-lg-5 mb-2 mb-md-0"> Crear un ticket</a>
                            <a href="" class="btn btn-success px-4 ml-lg-5 mb-2 mb-md-0"> Consultar un ticket</a>
                        </div>
                    </div>
                </div>
                <?php
                    }else if( isset($_GET["support_id"]) ){
                        // Imprimir los pasos de solución y por ultimo consultar si se resolvio el problema
                        SupportPrintSupportCard();
                        SupportPrintStepCards();
                ?>
                <!-- Tarjeta para crear o consultar un ticket -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline">¿Resolvió su problema? </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Sí tu problema persiste, crea una ticket para ser atendido por un técnico.
                        </p>

                        <div class="text-center">
                            <a href="" class="btn btn-success px-4 mr-lg-5 mb-2 mb-md-0"> Crear un ticket</a>
                            <a href="" class="btn btn-success px-4 ml-lg-5 mb-2 mb-md-0"> Consultar un ticket</a>
                        </div>
                    </div>
                </div>
                <?php
                    }else{            
                        // mostrar soluciones y luego preguntar si encontro su problema
                        SupportPrintSupportCards();
                ?>
                <!-- Tarjeta para crear o consultar un ticket -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline"> ¿Encontro su problema? </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Sí tu problema no aparece, crea una ticket para ser atendido por un
                            técnico. </p>

                        <div class="text-center">
                            <a href="" class="btn btn-success px-4 mr-lg-5 mb-2 mb-md-0"> Crear un ticket</a>
                            <a href="" class="btn btn-success px-4 ml-lg-5 mb-2 mb-md-0"> Consultar un ticket</a>
                        </div>
                    </div>
                </div>
                <?php
                    }          
                ?>

            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="lib/jquery-3.5.1/jquery-3.5.1.min.js"></script>
    <script src="lib/popper-1.16.0/popper.min.js"></script>
    <script src="lib/bootstrap-4.5.0/js/bootstrap.min.js"></script>

    <!-- Typeahead -->
    <script src="lib/typeahead.js/bootstrap-typeahead.min.js"></script>
    
    <!-- Functions -->
    <script src="js/functions.js"></script>

</body>

</html>