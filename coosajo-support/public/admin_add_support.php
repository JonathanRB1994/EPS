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

    // Mensajes de alertas
    $message_failedAddSupport = FALSE;
    $message_failedAddStep = FALSE;
    $message_deleteStep=FALSE;
    $message_failedDeleteStep=FALSE;
    $message_incorrectFile=FALSE;

    // Variables globales
    $total_steps = 0;

    // Insertar problema de soporte tecnico
    if(!isset($_GET["support_id"]) && isset($_POST["title"]) && isset($_POST["description"])){                
        // Agregar a la base de datos,        
        if(AdminAddSupport()==FALSE){
            $message_failedAddSupport = TRUE;
        }
        unset($_POST["title"]);
        unset($_POST["description"]);
        unset($_POST["keywords"]);
    }

    $imgDir="";
    // Insertar paso de soporte tecnico
    if(isset($_GET["support_id"]) && isset($_POST["number"]) && isset($_POST["title"]) && isset($_POST["description"]) && isset($_POST["addImage"]) && isset($_POST["addURL"])){  
        // Ver si vamos agregar una imagen
        if($_POST["addImage"]==="yes" && $_POST["addURL"]==="image"){
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
        } else if($_POST["addURL"]==="URL" && isset($_POST["imageURL"])){
            $imgDir=$_POST["imageURL"];
        }               
        
        
        // Agregar a la base de datos,        
        if($message_incorrectFile==FALSE){
            if(AdminAddSupportStep($imgDir)==FALSE){
                $message_failedAddStep = TRUE;
            }
        }
        
        unset($_POST["number"]);
        unset($_POST["title"]);
        unset($_POST["description"]);        
        unset($_POST["image"]);
    }              

    // Eliminar un paso especifico
    if(isset($_GET["support_id"]) && isset($_GET["delete_step_id"])){
        $operacion = AdminDeleteStep();   
        if($operacion==TRUE){
            $message_deleteStep=TRUE;
        }else{
            $message_failedDeleteStep=TRUE; 
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
            <h1 class="display-4">Pasos de solución</h1>
            <p class="lead">Agregar los pasos para la solución del problema técnico.</p>
        </div>
    </div>    
    <?php
        }else{
    ?>
    <!-- Información de la seeción acutal -->
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Agregar nuevo problema técnico</h1>
            <p class="lead">Para agregar un nuevo problema técnico debe completar todos los datos del formulario.</p>
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

    <!-- Alerta ERROR subir soporte -->
    <?php
        if($message_failedAddSupport==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se pudo agregar el nuevo problema.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de ERROR agregar PASO -->
    <?php
        if($message_failedAddStep==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se pudo agregar el paso de solución.
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
        <?php
            if( isset($_GET["support_id"]) ){
            //if( TRUE ){
                // Imprimir tarjeta del problema técnico y obtener numero de pasos agreagados
                $total_steps = AdminSupportPrintSupportCard();
                if($total_steps>0)
                    AdminSupportPrintStepCards("admin_add_support");
        ?>        
            <!-- Imprimir formulario para agregar un paso -->
            <div class="card bg-dark mt-4 text-white col-12">
                <div class="card-header  pt-4">
                    <h5 class="card-title">Agregar paso para solucionar el problema técnico</h5>
                </div>
                <div class="card-body">
                    <form action="admin_add_support.php?support_id=<?php echo $_GET["support_id"]; ?>" enctype="multipart/form-data" method="POST">
                        <div class="form-group">
                            <label for="inputNumber">Ingresa el número del paso a agregar</label>
                            <input type="number" class="form-control" id="inputNumber" name="number" placeholder="Número" required="required" value="<?php echo ($total_steps+1); ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputTitle">Ingresa el título del paso</label>
                            <input type="text" class="form-control" id="inputTitle" name="title" required="required" placeholder="Título del problema técnico">
                        </div>
                        <div class="form-group">
                            <label for="inputDescription">Ingresa su descripción</label>
                            <textarea class="form-control" id="inputDescription" name="description" required="required" placeholder="Descripción" rows="3"></textarea>
                        </div>                        

                        <p class="card-text">¿Desea agregar imagen al paso?</p>
                        <div class="form-group text-center">
                            <div class="form-check form-check-inline px-3">
                                <input class="form-check-input" type="radio" name="addImage" id="inlineRadio1" value="yes">
                                <label class="form-check-label" for="inlineRadio1">Si</label>
                            </div>
                            <div class="form-check form-check-inline px-3">
                                <input class="form-check-input" type="radio" name="addImage" id="inlineRadio2" value="no" checked>
                                <label class="form-check-label" for="inlineRadio2">No</label>
                            </div>
                        </div>                                               
                        
                        <div class="form-group" id="divImage" hidden>
                            <p class="card-text">¿Subira una imagen o una URL?</p>
                            <div class="form-group text-center">
                                <div class="form-check form-check-inline px-3">
                                    <input class="form-check-input" type="radio" name="addURL" id="inlineRadio3" value="URL">
                                    <label class="form-check-label" for="inlineRadio3">URL</label>
                                </div>    
                                <div class="form-check form-check-inline px-3">
                                    <input class="form-check-input" type="radio" name="addURL" id="inlineRadio4" value="image" checked>
                                    <label class="form-check-label" for="inlineRadio4">Imagen</label>
                                </div>
                                
                            </div>

                            <div class="form-group" id="divImageOrURL" hidden>
                                <label for="inputImage">Ingresa el URL de la imagen a mostrar</label>
                                <input type="text" class="form-control" id="inputImage" name="imageURL" placeholder="URL de la imagen" rows="3">
                            </div>  

                            <div id="divFileInput">
                                <label for="content-image">Ingresa una imagen para mostrar</label>
                                <div class="custom-file" id="content-image">
                                    <input type="file" class="custom-file-input" id="fileImage" name="image" lang="es">
                                    <label class="custom-file-label" for="fileImage">Seleccionar imagen</label>
                                </div>
                            </div>
                            
                        </div>                          
                                                
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-4 mt-3">Agregar paso de solución</button>
                        </div>                        
                    </form>
                </div>
            </div>
        <?php
                
            }else{
        ?>
            <!-- Imprimir formulario para agregar un problema tecnico -->
            <div class="card bg-dark mt-4 text-white col-12">
                <div class="card-header pl-4 pt-4">
                    <h5 class="card-title">Datos del problema técnico</h5>
                </div>
                <div class="card-body p-4">
                    <form action="admin_add_support.php" method="POST">
                        <div class="form-group">
                            <label for="inputTitle">Ingresa el título del problema técnico</label>
                            <input type="text" class="form-control" id="inputTitle" name="title" required="required" placeholder="Título del problema técnico">
                        </div>
                        <div class="form-group">
                            <label for="inputDescription">Ingresa su descripción</label>
                            <textarea class="form-control" id="inputDescription" name="description" required="required" placeholder="Descripción" rows="3"></textarea>
                        </div>                      
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-4 mt-3">Agregar problema técnico</button>
                        </div>                        
                    </form>
                </div>

            </div>
        <?php
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

    <!-- tablesorter -->
    <script src="lib/tablesorter/js/jquery.tablesorter.min.js"></script>
    <script src="lib/tablesorter/js/jquery.tablesorter.widgets.min.js"></script>

    <!-- Functions -->
    <script src="js/functions.js"></script>
</body>

</html>