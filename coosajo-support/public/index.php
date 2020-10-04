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

    // Conexion a la BD de tickets para obtener los tipos de incidencias
    require '../vendor/ticket_db.php';  

    require_once("../vendor/global_vars.php"); 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo TITLE_PAGE; ?>
    <?php echo FAV_ICON; ?>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap --> 
    <link rel="stylesheet" href="lib/bootstrap-4.5.0/css/bootstrap.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Barra de navegacion -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo TITLE_USER; ?></a>
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
                            <a class="dropdown-item" href="index.php">Problemas Técnicos</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="ticket.php">Consultar Ticket</a>
                            <a class="dropdown-item" href="ticket.php?new_ticket=TRUE">Generar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="auth.php">Autenticarme</a>
                        </div>
                    </li>
                </ul>
                <form class="form-inline" method="POST" action="index.php">
                    <input class="form-control mr-sm-2 typeahead" type="search" placeholder="Buscar" name="search" id="search" aria-label="Search" autocomplete="off">
                    <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Información de la seeción acutal -->    
    <?php
        // Jumbotron para mostrar pasos
        if( isset($_GET["support_id"]) ){
    ?>
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Pasos para solucionar tu problema</h1>
            <p class="lead">Esta sección muestra los pasos que debes seguir para solucionar tu problema.</p>
        </div>
    </div>
    <?php
        // Jumbotron para mostrar los problemas
        }else{
    ?>
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Problemas Técnicos</h1>
            <p class="lead">En esta sección se muestran los problemas que es posible solucionar con la aplicación.</p>
        </div>
    </div>  
    <?php                  
        }
    ?>

    <!-- Contenedor de las tarjetas -->
    <div class="main">
        <div class="container">
            <div class="cards pb-5">
                <!-- Impresión de las tarjetas -->
                <?php
                    // Imprimir problemas tras haber realizado una busqueda
                    if(isset($_POST["search"])){                        
                ?>
                <!-- Tarjeta para crear o consultar un ticket -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline"> ¿Encontró su problema? </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Si tu problema no aparece, crea un ticket para ser atendido por un técnico.</p>

                        <div class="text-center">
                            <a href="" class="btn btn-outline-primary px-4 mr-lg-5 mb-2 mb-md-0"> Crear un ticket</a>
                            <a href="" class="btn btn-outline-primary px-4 ml-lg-5 mb-2 mb-md-0"> Consultar un ticket</a>
                        </div>
                    </div>
                </div>
                <?php
                        SupportPrintSearchSupportCards(); 
                    // Imprimir pasos de un problema
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
                            <a href="ticket.php?new_ticket=TRUE" class="btn btn-outline-primary px-4 mr-lg-5 mb-2 mb-md-0"> Crear un ticket</a>
                            <a href="ticket.php" class="btn btn-outline-primary px-4 ml-lg-5 mb-2 mb-md-0"> Consultar un ticket</a>
                        </div>
                    </div>
                </div>
                <?php
                    // imprimir problemas
                    }else{            
                        // preguntar si Encontró su problema y luego mostrar soluciones                        
                ?>
                <!-- Tarjeta para crear o consultar un ticket -->
                <div class="card bg-dark mt-4 text-white mb-4">
                    <div class="card-header">
                        <h5 class="card-title inline"> ¿Encontró su problema? </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Si tu problema no aparece, crea un ticket para ser atendido por un técnico.</p>

                        <div class="text-center">
                            <a href="ticket.php?new_ticket=TRUE" class="btn btn-outline-primary px-4 mr-lg-5 mb-2 mb-md-0"> Crear un ticket</a>
                            <a href="ticket.php" class="btn btn-outline-primary px-4 ml-lg-5 mb-2 mb-md-0"> Consultar un ticket</a>
                        </div>
                    </div>
                </div>

                <!-- TARJETA DE FILTROS -->
                <div class="card bg-dark text-white">
                    <div class="card-header">
                        <h5 class="card-title inline title-filtro3"> Filtrar problemas</h5>
                        <button class="btn float-right" id="btn_hidden_filters">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-down-fill" fill="black"
                                xmlns="http://www.w3.org/2000/svg">
                                <path id="path_down_icon"
                                    d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                                <path id="path_up_icon"
                                    d="M7.247 4.86l-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"
                                    hidden />
                            </svg>
                        </button>
                    </div>
                    <div class="card-body text-left" id="div_filters" hidden>
                        <input type="hidden" name="hiddenAll" id="hiddenAll" value="no">
                        <div class="form-group">
                            <label for="tendencias">Tendencia</label>
                            <select class="form-control" id="select_tendencias">
                                <option value="views" selected>Los <?php echo LIMIT_SELECT; ?> problemas más vistos
                                </option>
                                <option value="last">Los últimos <?php echo LIMIT_SELECT; ?> problemas agregados
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tipoIncidencia">Tipo de incidencia</label>
                            <select class="form-control" id="select_tipo_incidencia">
                                <option value='{"id":"all","name":"all"}' selected>Todos los tipos de incidencia</option>
                                <?php PrintIncidenciasTipo(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="incidencia">Incidencia</label>
                            <select class="form-control" id="select_incidencia">
                                <option value='{"id":"all","name":"all"}' selected>Todas las incidencias</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- DIV print cards filtered  -->
                <div id="div_print_cards">
                    <?php 
                            SupportPrintSupportCards("views","all","all"); 
                        }          
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="lib/jquery-3.5.1/jquery-3.5.1.min.js"></script>
    <script src="lib/popper-1.16.0/popper.min.js"></script>
    <script src="lib/bootstrap-4.5.0/js/bootstrap.min.js"></script>

    <!-- Typeahead -->    
    <script src="lib/typeahead.js/bootstrap-typeahead.min.js"></script>

    <!-- tablesorter -->
    <script src="lib/tablesorter/js/jquery.tablesorter.min.js"></script>
    <script src="lib/tablesorter/js/jquery.tablesorter.widgets.min.js"></script>

    <!-- Functions -->
    <script src="js/functions.js"></script>

</body>

</html>