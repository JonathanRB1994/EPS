<!-- ARCHIVOS CON FUNCIONALIDADES NECESARIAS -->
<!-- Esta seccion es solo para administradores -->
<?php
    session_start();
    
    // Si la sesión ya cuenta con un usuario logeado, debe redirigirse a la pagina de administrador
    if(isset($_SESSION["login_user"])) { 
        if($_SESSION["login_user"]==FALSE || $_SESSION["login_user_role"]!=="admin") {
            header('Location: index.php');
        }
    }else{
        header('Location: index.php');
    }
    // Arriba se verifico que el ususario es administrador
    

    // Conexión a la BD de suporte técnico
    require '../vendor/admin_support_db.php';

    // Mensajes    
    $message_addUser=FALSE;
    $message_failedAddUser=FALSE;
    $message_editUser=FALSE;
    $message_failedEditUser=FALSE;
    $message_deleteUser=FALSE;
    $message_failedDeleteUser=FALSE;

    

    // Agregar ususario
    if(isset($_GET["add_user"]) && isset($_POST["username"]) && isset($_POST["fullname"]) && isset($_POST["password"]) && isset($_POST["role"])){    
        if($_GET["add_user"]==TRUE){
            if(AdminAddUser()==TRUE){
                $message_addUser=TRUE;
            }else{
                $message_failedAddUser=TRUE; 
            }
            unset($_POST["username"]);
            unset($_POST["fullname"]);
            unset($_POST["password"]);
            unset($_GET["add_user"]);
        }                    
    }

    // Editar ususario
    if(isset($_GET["edit_user_id"]) && isset($_POST["username"]) && isset($_POST["fullname"]) && isset($_POST["password"]) && isset($_POST["role"])){    
        if(AdminEditUser()==TRUE){
            $message_editUser=TRUE;
        }else{
            $message_failedEditUser=TRUE; 
        }
        unset($_POST["username"]);
        unset($_POST["fullname"]);
        unset($_POST["password"]); 
        unset($_GET["edit_user_id"]);
    }

    // Eliminar un usuario
    if(isset($_GET["delete_user_id"])){                
        if(AdminDeleteUser()==TRUE){
            $message_deleteUser=TRUE;
        }else{
            $message_failedDeleteUser=TRUE; 
        }
        unset($_GET["delete_user_id"]);
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

    <!-- tablesorter -->    
    <link rel="stylesheet" href="lib/tablesorter/css/theme.bootstrap_4.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="css/styles.css">

    <!--  FONT AWESOME  -->
    <script src="https://kit.fontawesome.com/d36702c8eb.js" crossorigin="anonymous"></script>
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
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_users.php">Gestión de ususarios</a>
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
                            <a class="dropdown-item" href="#">Administrador</a>
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

    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Gestion de ususario</h1>
            <p class="lead">Está sección muestra los distintos usuarios, permitiendo agregar nuevos, editar existentes o eliminarlos.</p>
        </div>
    </div>


    <!-- Mensjaes -->
    <!-- Alerta de ERROR eliminar usuario -->
    <?php
        if($message_failedDeleteUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se puedo eliminar el usuario.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de eliminar ususario -->
    <?php
        if($message_deleteUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Se ha eliminado el ususario seleccionado.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Mensjaes -->
    <!-- Alerta de Agregar eliminar usuario -->
    <?php
        if($message_failedAddUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se puedo agregar el usuario.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de agregar ususario -->
    <?php
        if($message_addUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Se ha creado en nuevo ususario.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>

    <!-- Mensjaes -->
    <!-- Alerta de ERROR editar usuario -->
    <?php
        if($message_failedEditUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No se actualizo la información del usuario.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                
        </div>
    <?php
        }    
    ?>
    <!-- Alerta de editar ususario -->
    <?php
        if($message_editUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Se ha actualizado la información del ususario.
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
                    if(isset($_GET["edit_user_id"])){
                        AdminSupportPrintUserEditForm();
                    }else{
                ?>
                <!-- Imprimir formulario de nuevo susuario -->
                <div class="card bg-dark mt-4 text-white col-12">
                    <div class="card-header pl-4 pt-4">
                        <h5 class="card-title">Datos del nuevo usuario</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="admin_users.php?add_user=TRUE" method="POST">
                            <div class="form-group">
                                <label for="username">Ingrese el nombre de ususario</label>
                                <input type="text" class="form-control" id="username" name="username" required="required" placeholder="Nombre de ususario">
                            </div>
                            <div class="form-group">
                                <label for="fullname">Ingrese el nombre completo</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required="required" placeholder="Nombre completo">
                            </div>      
                            <div class="form-group">
                                <label for="password">Ingrese la contraseña de acceso</label>
                                <input type="text" class="form-control" id="password" name="password" required="required" placeholder="Contraseña de acceso">
                            </div> 
                            <p class="card-text">¿Cual será su rol?</p>
                            <div class="form-group text-center">
                                <div class="form-check form-check-inline px-3">
                                    <input class="form-check-input" type="radio" name="role" id="role1" value="admin">
                                    <label class="form-check-label" for="role1">Administrador</label>
                                </div>
                                <div class="form-check form-check-inline px-3">
                                    <input class="form-check-input" type="radio" name="role" id="role2" value="technical" checked>
                                    <label class="form-check-label" for="role2">Técnico</label>
                                </div>
                            </div>                       
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4 mt-3">Agregar nuevo ususario</button>
                            </div>                        
                        </form>
                    </div>
                </div> 
                <?php
                    }
                ?>
                              

                <!-- Imprimir tabla de ususarios -->
                <div class="card bg-dark  mt-4 text-white col-12 mb-4">
                    <div class="card-header pl-4 pt-4">
                        <h5 class="card-title">Datos de los ususario</h5>
                    </div>
                    <div class="table-responsive">
                    <?php
                        AdminSupportPrintUsersTable();
                    ?>
                    </div>
                    
                    
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
    <script src="lib/tablesorter/js/jquery.tablesorter.widgets.min.js"></script

    <!-- Functions -->
    <script src="js/functions.js"></script>
</body>

</html>