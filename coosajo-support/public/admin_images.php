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

    // Conexión a la BD de suporte técnico
    require '../vendor/admin_support_db.php';

    $isAdmin=FALSE;
    if($_SESSION["login_user_role"]==="admin"){
        $isAdmin=TRUE;
    }

    // Mensajes
    $message_deleteImage=FALSE;
    $message_failedDeleteImage=FALSE;    
    $message_deleteImageDB=FALSE;
    $message_failedDeleteImageDB=FALSE;

    // Eliminar un problema tecnico con pasos
    if(isset($_POST["imagePath"])){
        $pathImg = "img/";
        if(unlink($pathImg . $_POST["imagePath"])==TRUE){
            $message_deleteImage=TRUE;
        }else{
            $message_failedDeleteImage=TRUE;    
        }
    }

    if(isset($_POST["imagePath"]) && $message_deleteImage==TRUE){
        if(AdminDeleteImageReference($_POST["imagePath"])==TRUE){
            $message_deleteImageDB=TRUE;
        }else{
            $message_failedDeleteImageDB=FALSE;
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
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Imagenes almacenadas</h1>
            <p class="lead">Está sección muestran las imagenes almacenadas, sus URL y permite eliminarlas.</p>
            <p class="lead">Cuando eliminas una imagen, las referencias a está tambien seran eliminadas.</p>
        </div>
    </div>


    <!-- Mensjaes -->
    <!-- Alerta de ERROR actualizar SOPORTE -->
    <?php
        if($message_failedDeleteImage==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se puedo eliminar la imagen.
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
        if($message_deleteImage==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                La imagen fue eliminada.
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
        if($message_failedDeleteImageDB==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se puedo eliminar la URL de la imagen en la base de datos.
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
        if($message_deleteImageDB==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Se ha eliminado la URL de la imagen en la base de datos.
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
            <div class="cards row">
                <?php 
                    $folder_path = 'img/'; 
                    $num_files = glob($folder_path . "*.{JPG,jpeg,gif,png}", GLOB_BRACE);
                    $folder = opendir($folder_path); 
                    if($num_files > 0){
                        $number=0;
                        while(false !== ($file = readdir($folder)))  {
                            $file_path = $folder_path.$file;
                            $extension = strtolower(pathinfo($file ,PATHINFO_EXTENSION));
                            if($extension==='jpg' || $extension ==='png' || $extension === 'gif' || $extension === 'bmp') {
                ?>

                <!-- Tarjeta para crear o consultar un ticket -->
                <form action="admin_images.php" class="p-2 col-sm-12 col-lg-3" method="POST">

                
                    <div class="card bg-dark text-white">
                        <div class="card-header">
                            <h5 class="card-title inline">Imagen <?php echo ++$number; ?></h5>
                        </div>
                        <div class="card-body">                    
                            <p class="card-text">URL Imagen:</p>
                            <p class="card-text"><?php echo $file_path; ?></p>
                            <input type="text" name="imagePath" value="<?php echo $file; ?>" hidden>
                        </div>
                    
                        <img src="<?php echo $file_path; ?>" class="card-img-top mb-2" alt="...">
                            
                        <div class="text-center">                            
                            <button type="submit" class="btn btn-danger px-4 mb-2">Eliminar Imagen</button>
                        </div>                    
                    </div> 
                </form>               
                
                <?php
                    }}}
                    closedir($folder);
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