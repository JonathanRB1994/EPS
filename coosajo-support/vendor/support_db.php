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
            $link =  "index.php?steps=" . $row["id"];
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
    function SupportPrintStepCards($support_id)
    {
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
            exit;
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header\">";
            echo "\t\t<h5 class=\"card-title\">" . $row["step"] . ". " . $row["title"] . "</h5>";
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t</div>";
            echo "\t<img src=\"...\" class=\"card-img-top\" alt=\"...\">";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

    }

?>