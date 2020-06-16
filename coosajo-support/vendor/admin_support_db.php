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
        
        header('Location: admin_add_support.php?support_id='.$result);
    }

    function AdminEditSupport()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "UPDATE support SET title=?, description=?, keywords=? WHERE id=?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("sssi", $_POST["title"], $_POST["description"], $_POST["keywords"], $_GET["support_id"]);
        $status = $stmt->execute();        
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if ($status == FALSE) return FALSE;
        
        return TRUE;
    }

    function AdminEditStep()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "UPDATE step SET step=?, title=?, description=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("isssi", $_POST["number"], $_POST["title"], $_POST["description"], $_POST["image"], $_GET["step_id"]);
        $status = $stmt->execute();        
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if ($status == FALSE) return FALSE;
        
        return TRUE;
    }

    function AdminAddSupportStep()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();    

        $sql="INSERT INTO step(step, title, image, description, support_id) VALUES(?,?,?,?,?)";                
        $stmt = $conn->prepare($sql);     
        $stmt->bind_param("sssss", $_POST["number"], $_POST["title"], $_POST["image"], $_POST["description"], $_GET["support_id"]);
        
        $stmt->execute();
        $result = $stmt->insert_id;  

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

        if (!$result) return FALSE;
        return TRUE;        
    }
    
    function AdminSupportEditSupportForm(){
        // Obtener la conexión
        $conn = AdminSupportConexion();

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
            if(empty($row['id'])) header('Location: admin_index.php');

            $link =  "admin_index.php?support_id=" . $row["id"];
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">" . $row["title"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["keywords"] . "</small></p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-danger mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t\t</div>";
            echo "\t</div>";
            echo "</div>";

            // Imprimir formulario para editar problema tecnico
            echo "<div class=\"card bg-dark mt-4 text-white col-12\">";
            echo "\t<div class=\"card-header pl-4 pt-4\">";
            echo "\t\t<h5 class=\"card-title\">Datos del problema técnico</h5>";
            echo "\t</div>";
            echo "\t<div class=\"card-body p-4\">";
            echo "\t\t<form action=\"admin_edit_support.php?support_id=".$_GET["support_id"]."\" method=\"POST\">";
            echo "\t\t\t<div class=\"form-group\">";
            echo "\t\t\t\t<label for=\"inputTitle\">Ingresa el título del problema técnico</label>";
            echo "\t\t\t\t<input type=\"text\" class=\"form-control\" id=\"inputTitle\" name=\"title\" value=\"".$row["title"]."\" placeholder=\"Título del problema técnico\">";
            echo "\t\t\t</div>";
            echo "\t\t\t<div class=\"form-group\">";
            echo "\t\t\t\t<label for=\"inputDescription\">Ingresa su descripción</label>";
            echo "\t\t\t\t<textarea class=\"form-control\" id=\"inputDescription\" name=\"description\" placeholder=\"Descripción\" rows=\"3\">".$row["description"]."</textarea>";
            echo "\t\t\t</div>";
            echo "\t\t\t<div class=\"form-group\">";
            echo "\t\t\t\t<label for=\"inputKeywords\">Ingresa las palabras clave para la búsqueda</label>";
            echo "\t\t\t\t<textarea class=\"form-control\" id=\"inputKeywords\" name=\"keywords\" placeholder=\"Palabras clave\" rows=\"3\">".$row["keywords"]."</textarea>";
            echo "\t\t\t</div>";
            echo "\t\t\t<div class=\"text-center\">";
            echo "\t\t\t\t<button type=\"submit\" class=\"btn btn-primary px-4 mt-3\">Agregar problema técnico</button>";
            echo "\t\t\t</div>";
            echo "\t\t</form>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

    function AdminSupportEditStepForm(){
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM step WHERE id=?";        
        //$sql = "SELECT *FROM support WHERE id=?"; 
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $_GET["step_id"]);
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
            if(empty($row['id'])) header('Location: admin_index.php');

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
            echo "\t<div class=\"text-center mt-2 mb-3\">";        
            echo "\t\t<a href=\"\" class=\"btn btn-danger mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t</div>";
            echo "</div>";

            // Imprimir formulario para editar problema tecnico
            echo "<div class=\"card bg-dark mt-4 text-white col-12\">";
            echo "\t<div class=\"card-header pl-4 pt-4\">";
            echo "\t\t<h5 class=\"card-title\">Datos del paso del problema técnico</h5>";
            echo "\t</div>";
            echo "<div class=\"card-body\">";
            echo "<form action=\"admin_edit_support.php?step_id=".$_GET["step_id"]."\" method=\"POST\">";
            echo "<div class=\"form-group\">";
            echo "<label for=\"inputNumber\">Ingresa el número del paso</label>";
            echo "<input type=\"number\" class=\"form-control\" id=\"inputNumber\" name=\"number\" placeholder=\"Número\" value=\"".$row["step"]."\">";
            echo "</div>";
            echo "<div class=\"form-group\">";
            echo "<label for=\"inputTitle\">Ingresa el título del paso</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"inputTitle\" name=\"title\" value=\"".$row["title"]."\" placeholder=\"Título del problema técnico\">";
            echo "</div>";
            echo "<div class=\"form-group\">";
            echo "<label for=\"inputDescription\">Ingresa su descripción</label>";
            echo "<textarea class=\"form-control\" id=\"inputDescription\" name=\"description\" placeholder=\"Descripción\" rows=\"3\">".$row["description"]."</textarea>";
            echo "</div>";
            echo "<div class=\"form-group\">";
            echo "<label for=\"inputImage\">Ingresa el URL de la imagen a mostrar</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"inputImage\" name=\"image\" placeholder=\"URL de la imagen\" value=\"".$row["image"]."\" rows=\"3\"></textarea>";
            echo "</div>";
            echo "<div class=\"text-center\">";
            echo "<button type=\"submit\" class=\"btn btn-primary px-4 mt-3\">Actualizar paso</button>";
            echo "</div>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

    // Imprimir tarjeta de soporte seleccionada
    function AdminSupportPrintSupportCard()
    {
        $total_steps = 0;
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT count(*) as total_steps, support.id, support.title, support.description, support.keywords FROM support, step WHERE support.id=? and support.id=step.support_id";        
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
            
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">" . $row["title"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["keywords"] . "</small></p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=" . $row["id"]."\" class=\"btn btn-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
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
    
    // Imprimir todas las tarjetas de soporte
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
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">" . $row["title"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["keywords"] . "</small></p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_index.php?support_id=" . $row["id"]."\" class=\"btn btn-success mr-lg-3 mb-2 mb-md-0 px-4\"> Ver solución </a>";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=".$row["id"]."\" class=\"btn btn-warning mr-lg-3 ml-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
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
        $support_id = $_GET["support_id"];

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
            echo "\t\t<h5 class=\"card-title\">Paso " . $row["step"] . ". " . $row["title"] . "</h5>";
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t</div>";
            if(isset($row["image"]) && !empty($row["image"])){
                echo "\t<img src=\"".$row["image"]."\" class=\"card-img-top\" alt=\"...\">";
            }              
            echo "\t<div class=\"text-center mt-2 mb-3\">";        
            echo "\t\t<a href=\"admin_edit_support.php?step_id=".$row["id"]."\" class=\"btn btn-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
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