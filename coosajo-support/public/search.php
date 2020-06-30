<?php    
    include "../vendor/support_db.php";


    if(isset($_POST["query"])){

        // Obtener la conexión
        $conn = SupportConexion();


        // Script de la ocnsulta
        $sql="SELECT * FROM support WHERE title LIKE ?";
        $stmt = $conn->prepare($sql); 
        $param = "%".$_POST["query"]."%";
        $stmt->bind_param("s", $param);         
        $stmt->execute();
        $result = $stmt->get_result();  
        if (!$result) 
        {
            echo json_encode("Error en la busqueda");
            // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
            // cómo obtener información del error
            // echo "Error: La ejecución de la consulta falló debido a: \n";
            // echo "Query: " . $sql . "\n";
            // echo "Errno: " . $conn->errno . "\n";
            // echo "Error: " . $conn->error . "\n";            
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo json_encode("No se encontraron resultados");
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
        }

        $data = array();
        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            $data[] = $row["title"];
        }
        echo json_encode($data);
           
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn); 
    }
?>