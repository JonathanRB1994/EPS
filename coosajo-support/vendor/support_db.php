<?php    
    require_once("global_vars.php"); 

    // ICONOS
    const ICON_EJE = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-eye-fill\" fill=\"grey\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z\"/><path fill-rule=\"evenodd\" d=\"M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z\"/></svg>";
    
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
            return;
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
            $_SESSION["login_user_role"] = $user["role"];
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

    // Eliminar datos de la session al cerrarla
    function SupportLogout()
    {        
        // Autenticacion verificada, Iniciar la session
        $_SESSION["login_user"]=FALSE;
        unset($_SESSION["login_user"]);
        unset($_SESSION["login_user_id"]);
        unset($_SESSION["login_user_role"]);
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
            echo "\t<div class=\"card-body \">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". $row["nombre_incidencia"] . "</small></p>";          
            echo "\t\t<p class=\"card-text text-center mb-3\">";
            echo "<span class=\" pr-1 \">". ICON_EJE."</span>";
            echo "<small class=\"text-muted\"> ". $row["views"] ." vistas";
            echo "</small>";
            echo "</p>";
            echo "\t</div>";            
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

    // Imprime las tarjetas relacionadas con la busqueda
    function SupportPrintSearchSupportCards(){
        // Obtener la conexión
        $conn = SupportConexion();

        // Script de la ocnsulta
        $sql="SELECT * FROM support WHERE title LIKE ?";
        $stmt = $conn->prepare($sql); 
        $param = "%".$_POST["search"]."%";
        $stmt->bind_param("s", $param);   
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
            return;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            //echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
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
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". $row["nombre_incidencia"] . "</small></p>";
            echo "\t\t<p class=\"card-text text-center mb-3\">";
            echo "<span class=\" pr-1 \">". ICON_EJE."</span>";
            echo "<small class=\"text-muted\"> ". $row["views"] ." vistas";
            echo "</small>";
            echo "</p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-outline-primary px-4\"> Ver solución </a>";
            echo "\t\t</div>";            
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

    // Imprimir cartas de soporte en la pagina de inicio
    function SupportPrintSupportCards($tendencia = "views", $tipoIncidencia="all", $incidencia="all")
    {
        if($tendencia==="last") $tendencia = "date_create";

        // Obtener la conexión
        $conn = SupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM support ";

        // clasificacion
        if($incidencia!=="all"){
            // Definimos el tipo de incidencia, este viene integrado en support TABLE
            $sql = $sql . " WHERE id_incidencia = ? ";
        }else{
            // seleccionamos todos los q coincidan con la categoria
            if($tipoIncidencia!=="all"){
                $sql = $sql . " WHERE id_tipo_incidencia = ? "; // debo ir a traer los datos a la otra DB e imprimirlos como array
            }
        }

        // Ordenamiento
        $sql = $sql . " ORDER BY ".$tendencia." DESC, title ASC LIMIT ".LIMIT_SELECT;
        
        $stmt = $conn->prepare($sql); 
        
        if($incidencia!=="all"){
            // Definimos el tipo de incidencia, este viene integrado en support TABLE
            $stmt->bind_param("s", $incidencia);
        }else{
            // seleccionamos todos los q coincidan con la categoria
            if($tipoIncidencia!=="all"){
                $stmt->bind_param("s", $tipoIncidencia);
            }
        }        
        $stmt->execute();
        $result = $stmt->get_result();  
        if (!$result) 
        {            
            // cómo obtener información del error
            echo "Error: La ejecución de la consulta falló debido a: \n";
            echo "Query: " . $sql . "\n";
            echo "Errno: " . $conn->errno . "\n";
            echo "Error: " . $conn->error . "\n";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            //echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
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
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". $row["nombre_incidencia"] . "</small></p>";
            echo "\t\t<p class=\"card-text text-center mb-3\">";
            echo "<span class=\" pr-1 \">". ICON_EJE."</span>";
            echo "<small class=\"text-muted\"> ". $row["views"] ." vistas";
            echo "</small>";
            echo "</p>";
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-outline-primary px-4\"> Ver solución </a>";
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
            return;
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

        // Insertar la vista
        $sql_add_view = "UPDATE support SET views=(views+1) WHERE id=?";
        $stmt_add_view = $conn->prepare($sql_add_view);
        $stmt_add_view->bind_param("i", $support_id);
        $stmt_add_view->execute();

        // Cerrar prepared statement
        $stmt_add_view->close();
        
        // Terminar la conexion
        mysqli_close($conn);

    }

?>