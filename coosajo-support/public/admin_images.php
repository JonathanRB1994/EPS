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

    require_once '../vendor/global_vars.php';

    $isAdmin=FALSE;
    if($_SESSION["login_user_role"]==="admin"){
        $isAdmin=TRUE;
    }

    // Mensajes
    $message_deleteImage=FALSE;
    $message_failedDeleteImage=FALSE;    
    $message_deleteImageDB=FALSE;
    $message_failedDeleteImageDB=FALSE;
    $message_incorrectFile=FALSE;
    $message_uploadFile=FALSE;

    // Insertar paso de soporte tecnico
    if(isset($_POST["addImage"])){  
        // Ver si vamos agregar una imagen
        if($_POST["addImage"]==="yes"){
            $message_incorrectFile=TRUE;
            // Verificar si viene la imagen
            if(isset($_FILES["image"])){
                // Verificar si la imagen se subio sin ningun error
                if($_FILES["image"]["error"]===0){                
                    // Verificar si es una imgaen
                    if (($_FILES["image"]["type"] === "image/gif")
                    || ($_FILES["image"]["type"] === "image/jpeg")
                    || ($_FILES["image"]["type"] === "image/jpg")
                    || ($_FILES["image"]["type"] === "image/png")){
                        // Obtener la ruta en archivos temporales
                        $tmp_name = $_FILES["image"]["tmp_name"];
                        // Carpeta donde guardaremos las Imágenes
                        $dir = IMAGES_PATH;
                        $nombre_img = basename($_FILES["image"]["name"]);
                        $subido = move_uploaded_file($tmp_name, $dir.$nombre_img);
                        // Verificar si se subio y guardo correctamente la imagen
                        if($subido === TRUE){
                            // Guardar la direccion de la imagen en el servidor
                            $message_incorrectFile=FALSE;
                            $message_uploadFile=TRUE;
                        }                        
                    }
                }
            }
        }
    }

    // Eliminar un problema tecnico con pasos
    if(isset($_POST["imagePath"])){
        if(unlink($_POST["imagePath"])==TRUE){
            $message_deleteImage=TRUE;
        }else{
            $message_failedDeleteImage=TRUE;    
        }
    }

    if(isset($_POST["imagePath"]) && $message_deleteImage==TRUE){
        if(AdminDeleteImageReference($_POST["imagePath"])==TRUE){
            $message_deleteImageDB=TRUE;
        }else{
            $message_failedDeleteImageDB=TRUE;
        }
    }
   
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
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="admin_index.php"><?php if($_SESSION["login_user_role"]==="technical") {echo TITLE_TECHNICAL;} else {echo TITLE_ADMIN;} ?></a>
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
                            <a class="dropdown-item" href="ticket.php?new_ticket=TRUE">Generar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_images.php">Imágenes Almacenadas</a>
                            <?php 
                                if ($isAdmin) {
                            ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_users.php">Gestión de Usuarios</a>
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
                    <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Información de la seeción acutal -->
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Imágenes Almacenadas</h1>
            <p class="lead">Esta sección muestra las imágenes almacenadas, sus URL y permite eliminarlas.</p>
            <p class="lead">Cuando eliminas una imagen, las referencias a está también serán eliminadas.</p>
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

    <!-- Alerta de ERROR subir archivo -->
    <?php
        if($message_incorrectFile==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se pudo subir la imagen, verifica su peso y su extensión (jpg, jpeg, png, gif).
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>

    <!-- Alerta de ERROR subir archivo -->
    <?php
        if($message_uploadFile==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                La imagen se subió correctamente.
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

            <?php if(UPLOAD_IMAGES){ ?>
            <div class="row p-2" >                
                <!-- Imprimir formulario para agregar images -->
                <div class="card bg-dark px-0 text-white col-12">
                    <div class="card-header  pt-4">
                        <h5 class="card-title">Agregar una imagen</h5>
                    </div>
                    <div class="card-body text-left">
                        <form action="admin_images.php" enctype="multipart/form-data" method="POST">                                                                                                     
                        <input type="hidden" name="addImage" value="yes"> 
                            <div id="divFileInput">                                
                                <label for="content-image">Ingresa una imagen para mostrar</label>
                                <div class="custom-file" id="content-image">
                                    <input type="file" class="custom-file-input" id="fileImage" name="image" lang="es">
                                    <label class="custom-file-label" for="fileImage">Seleccionar imagen</label>
                                </div>
                            </div>
                                                                             
                            <div class="text-center">
                                <button type="submit" class="btn btn-outline-primary px-4 mt-3">Subir imagen</button>
                            </div>                        
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="cards row">
                <?php 
                    $folder_path = IMAGES_PATH; 
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
                    <div class="card bg-dark text-white card-image">
                        <div class="card-header">
                            <h5 class="card-title inline">Imagen <?php echo ++$number; ?></h5>
                        </div>
                        <div class="card-body">                    
                            <p class="card-text">URL de la imagen:</p>
                            <p class="card-text"><?php echo $file_path; ?></p>
                            <input type="hidden" name="imagePath" value="<?php echo $file_path; ?>" >
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
    <script src="js/admin_functions.js"></script>
</body>

</html>