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

    // Mensajes de alertas
    $message_failedAddSupport = FALSE;
    $message_failedAddStep = FALSE;
    $message_failedAddSupportStep = FALSE;

    // Variables globales
    $total_steps = 0;

    // Insertar problema de soporte tecnico
    if(isset($_POST["title"]) && isset($_POST["description"]) && isset($_POST["keywords"])){                
        // Agregar a la base de datos,        
        if(AdminAddSupport()==FALSE){
            $message_failedAddSupport = TRUE;
        }
        unset($_POST["title"]);
        unset($_POST["description"]);
        unset($_POST["keywords"]);
    }

    // Insertar problema de soporte tecnico
    if(isset($_GET["support_id"]) && isset($_POST["number"]) && isset($_POST["title"]) && isset($_POST["description"]) ){                
        // Agregar a la base de datos,        
        if(AdminAddSupportStep()==FALSE){
            $message_failedAddSupportStep = TRUE;
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

    <?php
        if($message_failedAddStep==TRUE){
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

    <?php
        if($message_failedAddSupportStep==TRUE){
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
    
    <!-- Contenedor de las tarjetas -->
    <div class="main">
        <div class="container">
        <?php
            if( isset($_GET["support_id"]) ){
            //if( TRUE ){
                // Imprimir tarjeta del problema técnico y obtener numero de pasos agreagados
                $total_steps = AdminSupportPrintSupportCard();
                if($total_steps>0)
                    AdminSupportPrintStepCards();
        ?>        
            <!-- Imprimir formulario para agregar un paso -->
            <div class="card bg-dark mt-4 text-white col-12">
                <div class="card-header  pt-4">
                    <h5 class="card-title">Agregar paso para solucionar el problema técnico</h5>
                </div>
                <div class="card-body">
                    <form action="admin_add_support.php?support_id=<?php echo $_GET["support_id"]; ?>" method="POST">
                        <div class="form-group">
                            <label for="inputNumber">Ingresa el número del paso a agregar</label>
                            <input type="number" class="form-control" id="inputNumber" name="number" placeholder="Número" value="<?php echo ($total_steps+1); ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputTitle">Ingresa el título del paso</label>
                            <input type="text" class="form-control" id="inputTitle" name="title" placeholder="Título del problema técnico">
                        </div>
                        <div class="form-group">
                            <label for="inputDescription">Ingresa su descripción</label>
                            <textarea class="form-control" id="inputDescription" name="description" placeholder="Descripción" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="inputImage">Ingresa el URL de la imagen a mostrar</label>
                            <input type="text" class="form-control" id="inputImage" name="image" placeholder="URL de la imagen" rows="3"></textarea>
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
                            <input type="text" class="form-control" id="inputTitle" name="title" placeholder="Título del problema técnico">
                        </div>
                        <div class="form-group">
                            <label for="inputDescription">Ingresa su descripción</label>
                            <textarea class="form-control" id="inputDescription" name="description" placeholder="Descripción" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="inputKeywords">Ingresa las palabras clave para la búsqueda</label>
                            <textarea class="form-control" id="inputKeywords" name="keywords" placeholder="Palabras clave" rows="3"></textarea>
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
    <script src="./lib/jquery-3.5.1/jquery-3.5.1.slim.min.js"></script>
    <script src="./lib/popper-1.16.0/popper.min.js"></script>
    <script src="./lib/bootstrap-4.5.0/js/bootstrap.min.js"></script>
</body>

</html>