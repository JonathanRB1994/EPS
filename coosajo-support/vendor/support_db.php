<?php    
    // Datos de conexión al servidor de soporte técnico
    // Este ususrio solo debe tener permisos de lectura
    define("SUPPORT_HOSTNAME","localhost");
    define("SUPPORT_USERNAME","root");
    define("SUPPORT_PASSWORD","");
    define("SUPPORT_DBNAME","support_db");
    
    // Conexión al servidor DB de soporte técnico
    function SupportConexion()
    {
        // Conexión al servidor DB de soporte técnico
        $conn = mysqli_connect(SUPPORT_HOSTNAME, SUPPORT_USERNAME, SUPPORT_PASSWORD, SUPPORT_DBNAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }    

    // Validar los datos de sessión
    function SupportLogin()
    {
        // Obtener la conexión
        $conn = SupportConexion();

        // Script de la ocnsulta
        $sql="SELECT * FROM users WHERE username=? AND password=?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("ss", $_POST["username"], $_POST["password"]);        
        $stmt->execute();
        $result = $stmt->get_result();  
        if (!$result) 
        {
            // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
            // cómo obtener información del error
            echo "Error: La ejecución de la consulta falló debido a: \n";
            echo "Query: " . $sql . "\n";
            echo "Errno: " . $conn->errno . "\n";
            echo "Error: " . $conn->error . "\n";            
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            exit;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
        }

        // Imprimir los datos
        $user = $result->fetch_assoc();
        if ($user["username"]===$_POST["username"] && $user["password"]===$_POST["password"]){
            // Autenticacion verificada, Iniciar la session
            $_SESSION["login_user"] = TRUE;
            $_SESSION["login_user_id"] = $user["id"];
            $_SESSION["login_user_username"] = $user["username"];
            $_SESSION["login_user_fullname"] = $user["fullname"];
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn); 
            header('Location: admin_index.php');
        }
           
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn); 

    }

    function SupportLogout()
    {        
        // Autenticacion verificada, Iniciar la session
        $_SESSION["login_user"]=FALSE;
        unset($_SESSION["login_user"]);
        unset($_SESSION["login_user_id"]);
        unset($_SESSION["login_user_username"]);
        unset($_SESSION["login_user_fullname"]);            
    }
    
    // Imprimir tarjeta de soporte seleccionada
    function SupportPrintSupportCard()
    {
        // Obtener la conexión
        $conn = SupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM support WHERE id=?";        
        //$sql = "SELECT *FROM support WHERE id=?"; 
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $_GET["support_id"]);
        $stmt->execute();
        $result = $stmt->get_result();  
        if (!$result) 
        {
            // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
            // cómo obtener información del error
            echo "Error: La ejecución de la consulta falló debido a: \n";
            echo "Query: " . $sql . "\n";
            echo "Errno: " . $conn->errno . "\n";
            echo "Error: " . $conn->error . "\n";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            header('Location: index.php');
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            header('Location: index.php');
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            if(empty($row['id'])) header('Location: index.php');

            $link =  "admin_index.php?support_id=" . $row["id"];
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">" . $row["title"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

    // Imprimir cartas de soporte
    function SupportPrintSupportCards()
    {
        // Obtener la conexión
        $conn = SupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM support";
        $stmt = $conn->prepare($sql); 
        $stmt->execute();
        $result = $stmt->get_result();  
        if (!$result) 
        {
            // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
            // cómo obtener información del error
            echo "Error: La ejecución de la consulta falló debido a: \n";
            echo "Query: " . $sql . "\n";
            echo "Errno: " . $conn->errno . "\n";
            echo "Error: " . $conn->error . "\n";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            exit;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            exit;
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            $link =  "index.php?support_id=" . $row["id"];
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">" . $row["title"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            //echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["keywords"] . "</small></p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-success px-4\"> Ver solución </a>";
            echo "\t\t</div>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

    // Imprimir pasos para solucion de soporte
    function SupportPrintStepCards()
    {
        $support_id = $_GET["support_id"];
        // Obtener la conexión
        $conn = SupportConexion();

        // Script de la ocnsulta
        $sql="SELECT * FROM step WHERE support_id=? ORDER BY step ASC";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $support_id);
        $stmt->execute();
        $result = $stmt->get_result();  
        if (!$result) 
        {
            // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
            // cómo obtener información del error
            echo "Error: La ejecución de la consulta falló debido a: \n";
            echo "Query: " . $sql . "\n";
            echo "Errno: " . $conn->errno . "\n";
            echo "Error: " . $conn->error . "\n";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            exit;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            // Si no se encontraron datos imprimir las cartas de soporte
            SupportPrintSupportCards();            
            return;
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header\">";
            echo "\t\t<h5 class=\"card-title\">Paso " . $row["step"] . ". " . $row["title"] . "</h5>";
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t</div>";
            if(isset($row["image"]) && !empty($row["image"])){
                echo "\t<img src=\"".$row["image"]."\" class=\"card-img-top\" alt=\"...\">";
            }  
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

    }

?>