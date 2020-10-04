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
    // Arriba se verifico que el Usuarios es administrador
    

    // Conexión a la BD de suporte técnico
    require '../vendor/admin_support_db.php';

    require_once '../vendor/global_vars.php';

    // Mensajes    
    $message_addUser=FALSE;
    $message_failedAddUser=FALSE;
    $message_editUser=FALSE;
    $message_failedEditUser=FALSE;
    $message_deleteUser=FALSE;
    $message_failedDeleteUser=FALSE;

    

    // Agregar Usuarios
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

    // Editar Usuarios
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
    <?php echo TITLE_PAGE; ?>
    <?php echo FAV_ICON; ?>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

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
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_users.php">Gestión de Usuarios</a>
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
                    <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Gestión de Usuario</h1>
            <p class="lead">Esta sección muestra los distintos usuarios, permitiendo agregar nuevos, editar existentes o eliminarlos.</p>
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
    <!-- Alerta de eliminar usuario -->
    <?php
        if($message_deleteUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Se ha eliminado el usuario seleccionado.
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
    <!-- Alerta de agregar usuario -->
    <?php
        if($message_addUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Se ha creado en nuevo usuario.
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
    <!-- Alerta de editar usuario -->
    <?php
        if($message_editUser==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Se ha actualizado la información del usuario.
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
                <div class="card bg-dark mt-4 px-0 text-white col-12">
                    <div class="card-header">
                    <h5 class="card-title inline title-filtro3">Crear nuevo usuario</h5>
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
                    <div class="card-body p-4 text-left" id="div_filters" hidden>
                        <form action="admin_users.php?add_user=TRUE" method="POST">
                            <div class="form-group">
                                <label for="username">Ingrese el nombre de usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required="required" placeholder="Nombre de usuario">
                            </div>
                            <div class="form-group">
                                <label for="fullname">Ingrese el nombre completo</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required="required" placeholder="Nombre completo">
                            </div>      
                            <div class="form-group">
                                <label for="password">Ingrese la contraseña de acceso</label>
                                <input type="text" class="form-control" id="password" name="password" required="required" placeholder="Contraseña de acceso">
                            </div> 
                            <p class="card-text">¿Cuál será su rol?</p>
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
                                <button type="submit" class="btn btn-outline-primary px-4 mt-3">Agregar nuevo usuario</button>
                            </div>                        
                        </form>
                    </div>
                </div> 
                <?php
                    }
                ?>
                              

                <!-- Imprimir tabla de Usuarios -->
                <div class="card bg-dark px-0  mt-4 text-white col-12 mb-4">
                    <div class="card-header pl-4 pt-4">
                        <h5 class="card-title">Datos de los usuarios</h5>
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
    <script src="lib/tablesorter/js/jquery.tablesorter.widgets.min.js"></script>

    <!-- Functions -->
    <script src="js/admin_functions.js"></script>
</body>

</html>