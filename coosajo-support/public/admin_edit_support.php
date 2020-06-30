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
        header('Location: admin_index.php');
    }

    $isAdmin=FALSE;
    if($_SESSION["login_user_role"]==="admin"){
        $isAdmin=TRUE;
    } 

    // Conexión a la BD de suporte técnico
    require '../vendor/admin_support_db.php';         

    // Mensajes de alertas
    $message_failedEditSupport = FALSE;
    $message_editSupport = FALSE;    
    $message_failedEditStep = FALSE;
    $message_editStep = FALSE; 
    $message_incorrectFile=FALSE;

    // Actualizar problema de soporte técnico en la BD
    if(isset($_GET["support_id"]) && isset($_POST["title"]) && isset($_POST["description"]) ){                
        // Agregar a la base de datos,      
        if(AdminEditSupport()==TRUE){
            $message_editSupport = TRUE;
        }else{
            $message_failedEditSupport = TRUE;
        }
        unset($_POST["title"]);
        unset($_POST["description"]);
        unset($_POST["keywords"]);
    }

    $imgDir="";
    // Insertar paso de soporte tecnico
    if(isset($_GET["step_id"]) && isset($_POST["number"]) && isset($_POST["title"]) && isset($_POST["description"])  && isset($_POST["addImage"]) && isset($_POST["delImage"]) && isset($_POST["addURL"])){  
        $imgDir="";        

        // Ver si vamos agregar una imagen
        if($_POST["addImage"]==="yes" && $_POST["delImage"]==="no" && $_POST["addURL"]==="image"){
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
                        // Carpeta donde guardaremos las imagenes
                        $dir = "img/";
                        $nombre_img = basename($_FILES["image"]["name"]);
                        $subido = move_uploaded_file($tmp_name, $dir.$nombre_img);
                        // Verificar si se subio y guardo correctamente la imagen
                        if($subido === TRUE){
                            // Guardar la direccion de la imagen en el servidor
                            $imgDir = $dir.$nombre_img;
                            $message_incorrectFile=FALSE;
                        }                        
                    }
                }
            }
        } else if($_POST["addImage"]==="yes" && $_POST["delImage"]==="no" && $_POST["addURL"]==="URL" && isset($_POST["imageURL"])) {
            $imgDir=$_POST["imageURL"];
        }    
        // Actualizar paso de problema de soporte técnico en la BD
        if($message_incorrectFile==FALSE){      
            // var 1, directorio de la imagen en el server, vacio si se desea eliminar  
            // var 2, indica si se desea cambiar la imagen o no    
            if(AdminEditStep($imgDir, $_POST["addImage"])==TRUE){
                $message_editStep = TRUE;
            }else{
                $message_failedEditStep = TRUE;
            }
            unset($_POST["number"]);
            unset($_POST["title"]);
            unset($_POST["description"]);        
            unset($_POST["image"]);
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
            <a class="navbar-brand" href="index.php">ADMIN-SUPPORT</a>
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
    <!-- Alerta de ERROR subir archivo -->
    <?php
        if($message_incorrectFile==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se pudo subir la imagen, verifica su peso y su extención (jpg, jpeg, png, gif).
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
        if($message_editStep==TRUE){
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
    <script src="lib/jquery-3.5.1/jquery-3.5.1.min.js"></script>
    <script src="lib/popper-1.16.0/popper.min.js"></script>
    <script src="lib/bootstrap-4.5.0/js/bootstrap.min.js"></script>

    <!-- Typeahead -->
    <script src="lib/typeahead.js/bootstrap-typeahead.min.js"></script>
    
    <!-- Functions -->
    <script src="js/functions.js"></script>
</body>

</html>