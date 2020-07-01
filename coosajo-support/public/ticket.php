<!-- Archivos necesarios -->
<?php
    session_start();

    $isAuth=FALSE;
    $isAdmin=FALSE;
    $isTechnical=FALSE;
    // Si la sesión ya cuenta con un usuario logeado, debe redirigirse a la pagina de administrador
    if(isset($_SESSION["login_user"])) { 
        if($_SESSION["login_user"]==TRUE) {
            $isAuth = TRUE;
        }

        if($_SESSION["login_user_role"]==="admin"){
            $isAdmin=TRUE;
        }
    
        
        if($_SESSION["login_user_role"]==="technical"){
            $isTechnical=TRUE;
        }
    }

    
    
    
    // Conexión a la BD de suporte técnico
    require '../vendor/ticket_db.php';

    // Mensajes
    $message_failedGenerateTicket=FALSE;
    $message_generateTicket=FALSE;

    $token = 0;
    if(isset($_GET["new_ticket"]) && isset($_POST["description"])){
        $token = TicketGenerate();
        if($token==0) {
            $message_failedGenerateTicket=TRUE;
        }else{
            $message_generateTicket=TRUE;
        }                
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
            <a class="navbar-brand" href="admin_index.php"><?php if($isAdmin || $isTechnical) { echo "ADMIN-SUPPORT";} else {echo "SUPPORT";}?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav <?php if(!$isAuth) echo "mr-auto"; ?>">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Opciones
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php 
                                if($isAdmin || $isTechnical){
                            ?>                            
                            <a class="dropdown-item" href="admin_index.php">Problemas técnicos</a>
                            <a class="dropdown-item" href="admin_add_support.php">Nuevo problema</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="ticket.php">Consultar Ticket</a>
                            <a class="dropdown-item" href="ticket.php">Solicitar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_images.php">Imagenes almacenadas</a>
                            <?php 
                                } 
                                if($isAdmin) {
                            ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_users.php">Gestión de ususarios</a>
                            <?php 
                                } 
                                if(!$isAdmin && !$isTechnical) {
                            ?>
                            <a class="dropdown-item" href="index.php">Problemas técnicos</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="ticket.php">Consultar Ticket</a>
                            <a class="dropdown-item" href="ticket.php?new_ticket=TRUE">Solicitar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="auth.php">Autenticarme</a>
                            <?php
                                }
                            ?>
                        </div>
                    </li>
                </ul>
                <?php
                    if($isAuth){
                ?>
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
                <?php 
                    }
                ?>
                <form class="form-inline" method="POST" action="<?php if($isAdmin || $isTechnical) echo "admin_"; ?>index.php">
                    <input class="form-control mr-sm-2 typeahead" type="search" placeholder="Buscar" name="search" id="search" aria-label="Search" autocomplete="off">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <?php
        if(isset($_GET["new_ticket"])){
    ?>
    <!-- Información de la seeción acutal -->    
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Solicitar un ticket</h1>
            <p class="lead">Sí no encontraste tú problema o este no fue solucionado, genera un ticket.</p>
        </div>
    </div>
    <?php
        }else{
    ?>
    <!-- Información de la seeción acutal -->    
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Consulta de ticket</h1>
            <p class="lead">Consulta tu ticket de soporte técnico.</p>
        </div>
    </div>
    <?php
        }
    ?>
    

    <!-- Alerta de ERROR actualizar PASO -->
    <?php
        if($message_failedGenerateTicket==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se puedo generar el ticket.
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
        if($message_generateTicket==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                El ticket ha sido generado.
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
                    // Nuevo ticket
                    if(isset($_GET["new_ticket"])){
                        // Imprimir formulario de nuevo ticket
                        if($token==0){
                ?>
                <!-- Formulario de solicitud de ticket -->
                <div class="card bg-dark mt-4 text-white col-12">
                    <div class="card-header  pt-4">
                        <h5 class="card-title">Generar un ticket de soporte técnico</h5>
                    </div>
                    <div class="card-body">
                        <form action="ticket.php?new_ticket=TRUE" method="POST">
                            <div class="form-group">
                                <label for="inputDescription">Ingresa la descripción de tu problema</label>
                                <textarea class="form-control" id="inputDescription" name="description" placeholder="Descripción" rows="3" required="required"></textarea>
                            </div>                      
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4 mt-3">Generar un token de soporte</button>
                            </div>                        
                        </form>
                    </div>
                </div>
                <?php
                        // El ticket fue creado
                        // Mostrar informacion del ticket
                        }else{
                            TicketNewInfo($token);
                        }
                    //Imprimir formulario de buscar informacion del ticket
                    }else{
                ?>
                <!-- Formulario de busqueda de ticket -->
                <div class="card bg-dark mt-4 text-white">
                    <div class="card-header">
                        <h5 class="card-title inline">Consulta de ticket</h5>
                    </div>
                    <div class="card-body">
                        <form action="ticket.php" method="POST">
                            <div class="form-group">
                                <label for="inputTicket">Ingresa tu código de ticket</label>
                                <input type="text" class="form-control" id="inputTicket" name="ticket" required="required" placeholder="Código de ticket" value="<?php if(isset($_POST["ticket"])) { echo $_POST["ticket"];} else { echo "1"; } ?>">
                            </div>                     
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4 mt-3">Buscar</button>
                            </div>   
                        </form>
                    </div>
                </div>
                <?php
                        // Sí se realizo la busqueda, imprimir el resultado del ticket buscado
                        if(isset($_POST["ticket"])){
                            // Buscar la ticket
                            TicketPrint();
                            //unset($_POST["ticket"]);
                        }
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

    <!-- tablesorter -->
    <script src="lib/tablesorter/js/jquery.tablesorter.min.js"></script>
    <script src="lib/tablesorter/js/jquery.tablesorter.widgets.min.js"></script>

    <!-- Functions -->
    <script src="js/functions.js"></script>
</body>

</html>