<?php
    // Datos de conexión al servidor de soporte técnico
    // Este ususrio debe tener permisos de lectura y escritura
    define("TICKET_HOSTNAME","localhost");
    define("TICKET_USERNAME","root");
    define("TICKET_PASSWORD","");
    define("TICKET_DBNAME","support_db");
    
    // Conexión al servidor DB de soporte técnico
    function TicketConexion()
    {
        // Conexión al servidor DB de soporte técnico
        $conn = mysqli_connect(TICKET_HOSTNAME, TICKET_USERNAME, TICKET_PASSWORD, TICKET_DBNAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    function TicketPrint(){
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM ticket WHERE id=?";                
        $stmt = $conn->prepare($sql); 
        $ticket = $_POST["ticket"];
        strtoupper($ticket);
        $stmt->bind_param("s", $ticket);
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
            return FALSE;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">Información del ticket " . $_POST["ticket"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">El ticket no fue encontrado, revisa tu código de ticket</p>";            
            echo "\t</div>";
            echo "</div>";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            return FALSE;
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {  
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">Información del ticket " . $row["id"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">Token: ".$row["id"]."</p>";  
            echo "\t\t<p class=\"card-text\">Descripción:</p>";  
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";            
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        return TRUE;
    }

    function TicketGenerate(){
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "INSERT INTO ticket(token, description) VALUES(?,?)";
        $stmt = $conn->prepare($sql);       
        $token="ABCDEFG";
        $stmt->bind_param("ss", $token,$_POST["description"]);
        $stmt->execute();
        $result = $stmt->insert_id;
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
                
        return $result;                
    }

    function TicketNewInfo($token){
        $total_steps = 0;
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM ticket WHERE id=?";                
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $token);
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

            return FALSE;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            return FALSE;
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {            
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">Información de su nuevo ticket " . $row["id"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t<p class=\"card-text\">Porfavor guarde su numero de token para podelo consultar posteriormente</p>";
            echo "\t<p class=\"card-text\">Token: ".$row["id"]."</p>";
            echo "\t<p class=\"card-text\">Descriptión:</p>";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";            
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

        return TRUE;
    }
?>