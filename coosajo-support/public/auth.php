<!-- ARCHIVOS CON FUNCIONALIDADES NECESARIAS -->
<?php
    session_start();

    $message_failedLogin = FALSE;
    if(isset($_POST["username"]) && isset($_POST["password"])){
        $message_failedLogin = TRUE;
        // Conexión a la BD de suporte técnico
        require '../vendor/support_db.php';
        // Validar los datos de inicio se sesión,
        // Si los datos son correctos se crearan variables de sesion
        // Y se iniciara login_user como TRUE
        SupportLogin();        
    }

    // Si la sesión ya cuenta con un usuario logeado, debe redirigirse a la pagina de administrador
    if(isset($_SESSION["login_user"])) { 
        if($_SESSION["login_user"]==TRUE) {
            header('Location: admin-index.php');
        } 
    }
  
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPPORT</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="./lib/bootstrap-4.5.0/css/bootstrap.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">SUPPORT</a>
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
                            <a class="dropdown-item" href="index.php">Problemas técnicos</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/links">Consultar Ticket</a>
                            <a class="dropdown-item" href="/links/add">Solicitar Ticket</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="auth.php">Autenticarme</a>
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

    <!-- Información de la seeción acutal -->
    <div class="jumbotron d-none d-sm-none d-md-block">
        <div class="container">
            <h1 class="display-4">Autenticación</h1>
            <p class="lead">Ingresa tus datos para entrar al modo administrador.</p>
        </div>
    </div>

    <?php
        if($message_failedLogin==TRUE){
    ?>
        <div class="container ">            
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Los datos de autenticación no son correctos.
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
            <div class="card bg-dark mt-4 text-white col-12">
                <div class="card-header pl-4 pt-4">
                    <h5 class="card-title">Datos de Autenticación</h5>
                </div>
                <div class="card-body p-4">
                    <form action="auth.php" method="POST">
                        <div class="form-group">
                            <label for="inputUsername">Ingresa el nombre de usuario</label>
                            <input type="test" class="form-control" id="inputUsername" name="username" placeholder="Nombre de ususario">
                        </div>
                        <div class="form-group">
                            <label for="inputPassword4">Ingresa la contraseña</label>
                            <input type="password" class="form-control" id="inputPassword4" name="password" placeholder="Contraseña">
                        </div>                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-4 mt-3">Iniciar sesión</button>
                        </div>                        
                    </form>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap -->
    <script src="./lib/jquery-3.5.1/jquery-3.5.1.slim.min.js"></script>
    <script src="./lib/popper-1.16.0/popper.min.js"></script>
    <script src="./lib/bootstrap-4.5.0/js/bootstrap.min.js"></script>
</body>

</html>