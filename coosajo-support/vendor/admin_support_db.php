<?php    
    // Datos de conexión al servidor de soporte técnico
    // Este ususrio debe tener permisos de lectura y escritura
    define("ADMIN_SUPPORT_HOSTNAME","localhost");
    define("ADMIN_SUPPORT_USERNAME","root");
    define("ADMIN_SUPPORT_PASSWORD","");
    define("ADMIN_SUPPORT_DBNAME","support_db");
    
    // Conexión al servidor DB de soporte técnico
    function AdminSupportConexion()
    {
        // Conexión al servidor DB de soporte técnico
        $conn = mysqli_connect(ADMIN_SUPPORT_HOSTNAME, ADMIN_SUPPORT_USERNAME, ADMIN_SUPPORT_PASSWORD, ADMIN_SUPPORT_DBNAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }  
    
    
    function AdminAddSupport()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "INSERT INTO support(title, description, keywords) VALUES(?,?,?)";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("sss", $_POST["title"], $_POST["description"], $_POST["keywords"]);
        $stmt->execute();
        $result = $stmt->insert_id;
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if (!$result) return FALSE;
        
        header('Location: admin_add_support.php?steps='.$result);
    }

    function AdminAddSupportStep()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        $image = "none";
        if(!empty($_POST["image"])){
            $image=$_POST["image"];
        }        


        $sql="INSERT INTO step(step, title, image, description, support_id) VALUES(?,?,?,?,?)";                
        $stmt = $conn->prepare($sql);     
        $stmt->bind_param("sssss", $_POST["number"], $_POST["title"], $image, $_POST["description"], $_GET["steps"]);
        
        $stmt->execute();
        $result = $stmt->insert_id;  

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

        if (!$result) return FALSE;
        return TRUE;        
    }
    
    // Imprimir cartas de soporte
    function AdminSupportPrintSupportCard()
    {
        $total_steps = 0;
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT count(*) as total_steps, support.id, support.title, support.description, support.keywords FROM support, step WHERE support.id=? and support.id=step.support_id";        
        //$sql = "SELECT *FROM support WHERE id=?"; 
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $_GET["steps"]);
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

            header('Location: admin_index.php');
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            header('Location: admin_index.php');
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            $total_steps = $row["total_steps"];
            if(empty($row['id'])) header('Location: admin_index.php');

            $link =  "admin-index.php?steps=" . $row["id"];
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">" . $row["title"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["keywords"] . "</small></p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t\t</div>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

        return $total_steps;
    }
    
    // Imprimir cartas de soporte
    function AdminSupportPrintSupportCards()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

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
            $link =  "admin-index.php?steps=" . $row["id"];
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">" . $row["title"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["keywords"] . "</small></p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-success mr-lg-3 mb-2 mb-md-0 px-4\"> Ver solución </a>";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-warning mr-lg-3 ml-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
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
    function AdminSupportPrintStepCards()
    {
        $support_id = $_GET["steps"];

        // Obtener la conexión
        $conn = AdminSupportConexion();

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
            AdminSupportPrintSupportCards();            
            return;
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
            echo "\t<div class=\"text-center mt-2 mb-3\">";        
            echo "\t\t<a href=\"\" class=\"btn btn-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t<a href=\"\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

    }

?>