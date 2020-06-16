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

    // Esta seccion requiere el id del soporte o del paso, de lo contrario redirigir
    if(!isset($_GET["support_id"]) && !isset($_GET["step_id"])  ){
        header('Location: index.php');
    }

    // Conexión a la BD de suporte técnico
    require '../vendor/admin_support_db.php';         

    // Mensajes de alertas
    $message_failedEditSupport = FALSE;
    $message_editSupport = FALSE;    
    $message_failedEditStep = FALSE;
    $message_editStep = FALSE; 

    // Actualizar problema de soporte técnico en la BD
    if(isset($_GET["support_id"]) && isset($_POST["title"]) && isset($_POST["description"]) && isset($_POST["keywords"])){                
        // Agregar a la base de datos,  
        $operacion=AdminEditSupport();      
        if($operacion==TRUE){
            $message_editSupport = TRUE;
        }else{
            $message_failedEditSupport = TRUE;
        }
        unset($_POST["title"]);
        unset($_POST["description"]);
        unset($_POST["keywords"]);
    }

    // Actualizar paso de problema de soporte técnico en la BD
    if(isset($_GET["step_id"]) && isset($_POST["number"]) && isset($_POST["title"]) && isset($_POST["description"]) ){                
        $operacion=AdminEditStep();      
        if($operacion==TRUE){
            $message_editStep = TRUE;
        }else{
            $message_failedEditStep = TRUE;
        }
        unset($_POST["number"]);
        unset($_POST["title"]);
        unset($_POST["description"]);        
        unset($_POST["image"]);
    }      
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-SUPPORT</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="./lib/bootstrap-4.5.0/css/bootstrap.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">ADMIN-SUPPORT</a>
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
                        <a class="dropdown-item" href="admin_index.php">Problemas técnicos</a>
                        <a class="dropdown-item" href="admin_add_support.php">Nuevo problema</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/links">Consultar Ticket</a>
                            <a class="dropdown-item" href="/links/add">Solicitar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="auth.php">Nuevo usuario</a>
                            <a class="dropdown-item" href="auth.php?logout=TRUE">Cerrar sesión</a>
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

    <?php
        if( isset($_GET["support_id"]) ){
    ?>
    <!-- Información de la seeción acutal -->
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Editar problema técnico</h1>
            <p class="lead">Edita los valores del problema y haz click en actualizar problema.</p>
        </div>
    </div>
    <?php
        }else if($_GET["step_id"]){
    ?>
    <!-- Información de la seeción acutal -->
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Editar paso de solución</h1>
            <p class="lead">Edita los valores del paso y haz click en actualizar paso.</p>
        </div>
    </div>
    <?php
        }
    ?>

    <!-- Alerta de ERROR actualizar SOPORT -->
    <?php
        if($message_failedEditSupport==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se pudo actualizar los datos del problema.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de actualizar SOPORT -->
    <?php
        if($message_editSupport==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Los datos del problema fueron actualizados.
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
        if($message_failedEditStep==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se pudo actualizar los datos del paso.
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
        if($message_editSupport==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Los datos del paso fueron actualizados.
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
        <?php
            if( isset($_GET["support_id"]) ){
            //if( TRUE ){
                // Imprimir tarjeta del problema técnico y obtener numero de pasos agreagados
                AdminSupportEditSupportForm();                
            }else if(isset($_GET["step_id"])){
                AdminSupportEditStepForm();
            }
        ?>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="./lib/jquery-3.5.1/jquery-3.5.1.slim.min.js"></script>
    <script src="./lib/popper-1.16.0/popper.min.js"></script>
    <script src="./lib/bootstrap-4.5.0/js/bootstrap.min.js"></script>
</body>

</html>