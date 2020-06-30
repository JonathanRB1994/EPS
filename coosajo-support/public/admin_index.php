<!-- ARCHIVOS CON FUNCIONALIDADES NECESARIAS -->
<?php
    session_start();
    // Si la sesión ya cuenta con un usuario logeado, debe redirigirse a la pagina de administrador
    if(isset($_SESSION["login_user"])) { 
        if($_SESSION["login_user"]==FALSE) {
            header('Location: index.php');
        }
    }else{
        header('Location: index.php');
    }

    $isAdmin=FALSE;
    if($_SESSION["login_user_role"]==="admin"){
        $isAdmin=TRUE;
    } 

    // Conexión a la BD de suporte técnico
    require '../vendor/admin_support_db.php';

    // Mensajes
    $message_deleteSupport=FALSE;
    $message_failedDeleteSupport=FALSE;    
    $message_deleteStep=FALSE;
    $message_failedDeleteStep=FALSE;

    // Eliminar un problema tecnico con pasos
    if(isset($_GET["delete_support_id"])){
        $operacion = AdminDeleteSupport();        
        if($operacion==TRUE){
            $message_deleteSupport=TRUE;
        }else{
            $message_failedDeleteSupport=TRUE; 
        }
        unset($_GET["delete_support_id"]);
    }

    // Eliminar un paso especifico
    if(isset($_GET["support_id"]) && isset($_GET["delete_step_id"])){        
        $operacion = AdminDeleteStep();   
        if($operacion==TRUE){
            $message_deleteStep=TRUE;
        }else{
            $message_failedDeleteStep=TRUE; 
        } 
        unset($_GET["support_id"]);    
        unset($_GET["delete_step_id"]);
    }    
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-SUPPORT</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="lib/bootstrap-4.5.0/css/bootstrap.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_index.php">ADMIN-SUPPORT</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Opciones
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="admin_index.php">Problemas técnicos</a>
                            <a class="dropdown-item" href="admin_add_support.php">Nuevo problema</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="ticket.php">Consultar Ticket</a>
                            <a class="dropdown-item" href="ticket.php?new_ticket=TRUE">Solicitar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_images.php">Imagenes almacenadas</a>
                            <?php 
                                if ($isAdmin) {
                            ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_users.php">Gestión de ususarios</a>
                            <?php
                                }
                            ?>
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $_SESSION["login_user_username"]; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">                                
                            <a class="dropdown-item" href="#"><?php echo $_SESSION["login_user_fullname"]; ?></a>
                            <a class="dropdown-item" href="#"><?php if($isAdmin) {echo "Administrador";} else {echo "Técnico";} ?></a>
                            <a class="dropdown-item" href="auth.php?logout=TRUE">Cerrar sesión</a>
                        </div>
                    </li>
                </ul>
                <form class="form-inline" method="POST" action="admin_index.php">
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

    <!-- Mensjaes -->
    <!-- Alerta de ERROR actualizar SOPORTE -->
    <?php
        if($message_failedDeleteSupport==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se puedo eliminar el problema técnico con sus pasos.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de actualizar SOPORTE -->
    <?php
        if($message_deleteSupport==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                El problema técnico ha sido eliminado con sus pasos.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de ERROR actualizar PASO -->
    <?php
        if($message_failedDeleteStep==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se puedo eliminar el paso.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de actualizar PASO -->
    <?php
        if($message_deleteStep==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                El paso ha sido eliminado con sus pasos.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>

    <!-- Contenedor de las tarjetas -->
    <div class="main">
        <div class="container">
            <div class="cards">
                <?php
                    if(isset($_POST["search"])){
                ?>
                <!-- Agregar nuevo problema -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline">Agregar un nuevo problema técnico</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Si desea agregar un nuevo problema haga click en el boton.
                        </p>

                        <div class="text-center">
                            <a href="admin_add_support.php" class="btn btn-success px-4">Agregar nuevo problema</a>
                        </div>
                    </div>
                </div>
                <?php
                        // Imprimir tarjetas de la busqueda
                        AdminSupportPrintSearchSupportCards();
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
                <!-- Impresión de las tarjetas -->
                <?php
                    }else if( isset($_GET["support_id"]) ){     
                        AdminSupportPrintSupportCard();          
                        // Imprimir los pasos de solución y por ultimo consultar si se resolvio el problema
                        AdminSupportPrintStepCards("admin_index");
                ?>
                <!-- Agregar nuevo paso -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline">Agregar un nuevo paso.</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Si desea agregar un nuevo paso para solucionar el problema haga click en el boton.
                        </p>

                        <div class="text-center">
                            <a href="admin_add_support.php?support_id=<?php echo $_GET["support_id"]; ?>" class="btn btn-success px-4">Agregar nuevo paso</a>
                        </div>
                    </div>
                </div>
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
                ?>
                <!-- Agregar nuevo problema -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline">Agregar un nuevo problema técnico</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Si desea agregar un nuevo problema haga click en el boton.
                        </p>

                        <div class="text-center">
                            <a href="admin_add_support.php" class="btn btn-success px-4">Agregar nuevo problema</a>
                        </div>
                    </div>
                </div>
                <?php            
                    // mostrar soluciones y luego preguntar si encontro su problema
                    AdminSupportPrintSupportCards();
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