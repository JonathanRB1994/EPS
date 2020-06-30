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
    
    function AdminDeleteStep(){
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Eliminar pasos correspondientes al problema tecnico
        $sql = "DELETE FROM step WHERE id=?";        
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $_GET["delete_step_id"]);
        $stmt->execute();                
        $delete_step = $stmt->affected_rows;
        
        // Cerrar prepared statement
        $stmt->close();          
        // Terminar la conexion
        mysqli_close($conn);
        if($delete_step>0) return TRUE;
        return FALSE;  
    }

    function AdminDeleteSupport()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Eliminar pasos correspondientes al problema tecnico
        $sql = "DELETE FROM step WHERE support_id=?";        
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $_GET["delete_support_id"]);
        $delete_steps = $stmt->execute();                       
        // Cerrar prepared statement
        $stmt->close();

        if($delete_steps ==TRUE){
            // Eliminar problema tecnico
            $sql2 = "DELETE FROM support WHERE id=?";
            $stmt2 = $conn->prepare($sql2); 
            $stmt2->bind_param("i", $_GET["delete_support_id"]);
            $stmt2->execute(); 
            $delete_support = $stmt2->affected_rows;     
            // Terminar la conexion
            mysqli_close($conn);            
            if($delete_support>0) return TRUE;
            return FALSE;
        }
            
        // Terminar la conexion
        mysqli_close($conn);

        return FALSE;        
    }

    function AdminDeleteUser()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Eliminar pasos correspondientes al problema tecnico
        $sql = "DELETE FROM users WHERE id=?";        
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $_GET["delete_user_id"]);
        $stmt->execute(); 
        $delete_users = $stmt->affected_rows;                      
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

        if($delete_users>0) return TRUE;                    
        return FALSE;        
    }
    
    function AdminAddSupport()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "INSERT INTO support(title, description) VALUES(?,?)";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("ss", $_POST["title"], $_POST["description"]);
        $stmt->execute();
        $result = $stmt->insert_id;
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if (!$result) return FALSE;
        
        header('Location: admin_add_support.php?support_id='.$result);
    }

    function AdminAddUser()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "INSERT INTO users(username, fullname, password, role) VALUES(?,?,?,?)";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("ssss", $_POST["username"], $_POST["fullname"], $_POST["password"], $_POST["role"]);
        $stmt->execute();
        $result = $stmt->insert_id;
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if (!$result) return FALSE;
        return TRUE;
    }

    function AdminEditUser()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "UPDATE users SET username=?, fullname=?, password=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("ssssi", $_POST["username"], $_POST["fullname"], $_POST["password"], $_POST["role"], $_GET["edit_user_id"]);
        $status = $stmt->execute();        
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if ($status === FALSE) return FALSE;        
        return TRUE;
    }

    function AdminEditSupport()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "UPDATE support SET title=?, description=? WHERE id=?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("ssi", $_POST["title"], $_POST["description"], $_GET["support_id"]);
        $status = $stmt->execute();        
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if ($status === FALSE) return FALSE;
        
        return TRUE;
    }

    function AdminEditStep($imgDir, $isChangeImage)
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        $status = FALSE;
        // Script de la ocnsulta
        if($isChangeImage==="yes"){
            $sql = "UPDATE step SET step=?, title=?, description=?, image=? WHERE id=?";
            $stmt = $conn->prepare($sql); 
            $stmt->bind_param("isssi", $_POST["number"], $_POST["title"], $_POST["description"], $imgDir, $_GET["step_id"]);
            $status = $stmt->execute();  
            // Cerrar prepared statement
            $stmt->close(); 
        }else{
            $sql = "UPDATE step SET step=?, title=?, description=? WHERE id=?";
            $stmt = $conn->prepare($sql); 
            $stmt->bind_param("issi", $_POST["number"], $_POST["title"], $_POST["description"], $_GET["step_id"]);
            $status = $stmt->execute(); 
            // Cerrar prepared statement
            $stmt->close();  
        }
                             
        // Terminar la conexion
        mysqli_close($conn);
        
        if ($status === FALSE) return FALSE;
        
        return TRUE;
    }

    function AdminAddSupportStep($imgDir)
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();    

        $sql="INSERT INTO step(step, title, image, description, support_id) VALUES(?,?,?,?,?)";                
        $stmt = $conn->prepare($sql);     
        $stmt->bind_param("sssss", $_POST["number"], $_POST["title"], $imgDir, $_POST["description"], $_GET["support_id"]);
        
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
            echo "\t\t\t\t<input type=\"text\" class=\"form-control\" id=\"inputTitle\" name=\"title\" required=\"required\" value=\"".$row["title"]."\" placeholder=\"Título del problema técnico\">";
            echo "\t\t\t</div>";
            echo "\t\t\t<div class=\"form-group\">";
            echo "\t\t\t\t<label for=\"inputDescription\">Ingresa su descripción</label>";
            echo "\t\t\t\t<textarea class=\"form-control\" id=\"inputDescription\" name=\"description\" required=\"required\" placeholder=\"Descripción\" rows=\"3\">".$row["description"]."</textarea>";
            echo "\t\t\t</div>";
            echo "\t\t\t<div class=\"text-center\">";
            echo "\t\t\t\t<button type=\"submit\" class=\"btn btn-primary px-4 mt-3\">Actualizar problema técnico</button>";
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
            echo "\t\t<p class=\"card-text\">Descripción:</p>";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t</div>";
            if(isset($row["image"]) && !empty($row["image"])){
                echo "\t<div class=\"card-body\">";
                echo "\t\t<p class=\"card-text\">URL Imagen:</p>";
                echo "\t\t<p class=\"card-text\">".$row["image"]."</p>";
                echo "\t</div>";
                echo "\t<img src=\"".$row["image"]."\" class=\"card-img-top mb-2\" alt=\"...\">";                
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
            echo "<form action=\"admin_edit_support.php?step_id=".$_GET["step_id"]."\" enctype=\"multipart/form-data\" method=\"POST\">";
            echo "<div class=\"form-group\">";
            echo "<label for=\"inputNumber\">Ingresa el número del paso</label>";
            echo "<input type=\"number\" class=\"form-control\" id=\"inputNumber\" name=\"number\" required=\"required\" placeholder=\"Número\" value=\"".$row["step"]."\">";
            echo "</div>";
            echo "<div class=\"form-group\">";
            echo "<label for=\"inputTitle\">Ingresa el título del paso</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"inputTitle\" name=\"title\" required=\"required\" value=\"".$row["title"]."\" placeholder=\"Título del problema técnico\">";
            echo "</div>";
            echo "<div class=\"form-group\">";
            echo "<label for=\"inputDescription\">Ingresa su descripción</label>";
            echo "<textarea class=\"form-control\" id=\"inputDescription\" name=\"description\" required=\"required\" placeholder=\"Descripción\" rows=\"3\">".$row["description"]."</textarea>";
            echo "</div>";


            echo "<p class=\"card-text\">¿Desea cambiar la imagen al paso?</p>";
            echo "<div class=\"form-group text-center\">";
            echo "<div class=\"form-check form-check-inline px-3\">";
            echo "<input class=\"form-check-input \" type=\"radio\" name=\"addImage\" id=\"inlineRadio1\" value=\"yes\">";
            echo "<label class=\"form-check-label\" for=\"inlineRadio1\">Si</label>";
            echo "</div>";
            echo "<div class=\"form-check form-check-inline px-3\">";
            echo "<input class=\"form-check-input\" type=\"radio\" name=\"addImage\" id=\"inlineRadio2\" value=\"no\" checked>";
            echo "<label class=\"form-check-label\" for=\"inlineRadio2\">No</label>";
            echo "</div>";
            echo "</div>"; 

            echo "<div class=\"form-group\" id=\"divImage\" hidden>";
            echo "<p class=\"card-text\">¿Desea eliminar la imagen del paso?</p>";
            echo "<div class=\"form-group text-center\">";
            echo "<div class=\"form-check form-check-inline px-3\">";
            echo "<input class=\"form-check-input\" type=\"radio\" name=\"delImage\" id=\"inlineRadio3\" value=\"yes\">";
            echo "<label class=\"form-check-label\" for=\"inlineRadio1\">Si</label>";
            echo "</div>";
            echo "<div class=\"form-check form-check-inline px-3\">";
            echo "<input class=\"form-check-input\" type=\"radio\" name=\"delImage\" id=\"inlineRadio4\" value=\"no\" checked>";
            echo "<label class=\"form-check-label\" for=\"inlineRadio2\">No</label>";
            echo "</div>";
            echo "</div>"; 

            echo "<div id=\"divAddSourceImage\">";
            echo "<p class=\"card-text\">¿Subira una imagen o una URL?</p>";
            echo "<div class=\"form-group text-center\">";
            echo "<div class=\"form-check form-check-inline px-3\">";
            echo "<input class=\"form-check-input\" type=\"radio\" name=\"addURL\" id=\"inlineRadio5\" value=\"URL\">";
            echo "<label class=\"form-check-label\" for=\"inlineRadio1\">URL</label>";
            echo "</div>";
            echo "<div class=\"form-check form-check-inline px-3\">";
            echo "<input class=\"form-check-input\" type=\"radio\" name=\"addURL\" id=\"inlineRadio6\" value=\"image\" checked>";
            echo "<label class=\"form-check-label\" for=\"inlineRadio2\">Imagen</label>";
            echo "</div>";
            echo "</div>"; 

            echo "<div class=\"form-group\" id=\"divImageOrURL\" hidden>";
            echo "<label for=\"inputImage\">Ingresa el URL de la imagen a mostrar</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"inputImage\" value=\"".$row["image"]."\" name=\"imageURL\" placeholder=\"URL de la imagen\">";
            echo "</div>";

            echo "<div id=\"divFileInput\">";
            echo "<label for=\"content-image\">Ingresa una imagen para mostrar</label>";
            echo "<div class=\"custom-file\" id=\"content-image\">";
            echo "<input type=\"file\" class=\"custom-file-input\" id=\"fileImage\" name=\"image\" lang=\"es\">";
            echo "<label class=\"custom-file-label\" for=\"fileImage\">Seleccionar imagen</label>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
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

    function AdminSupportPrintUserEditForm(){
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM users WHERE id=?";        
        //$sql = "SELECT *FROM support WHERE id=?"; 
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $_GET["edit_user_id"]);
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
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
        }

        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            // Imprimir formulario para editar problema tecnico
            echo "<div class=\"card bg-dark mt-4 text-white col-12\">";
            echo "\t<div class=\"card-header pl-4 pt-4\">";
            echo "\t\t<h5 class=\"card-title\">Actualización de datos del ususario</h5>";
            echo "\t</div>";
            echo "<div class=\"card-body\">";
            echo "<form action=\"admin_users.php?edit_user_id=".$_GET["edit_user_id"]."\" enctype=\"multipart/form-data\" method=\"POST\">";
            echo "<div class=\"form-group\">";
            echo "<label for=\"username\">Ingrese el nombre de ususario</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" required=\"required\" placeholder=\"Nombre de ususario\" value=\"".$row["username"]."\">";
            echo "</div>";
            echo "<div class=\"form-group\">";
            echo "<label for=\"fullname\">Ingrese el nombre completo</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"fullname\" name=\"fullname\" required=\"required\" placeholder=\"Nombre completo\" value=\"".$row["fullname"]."\">";
            echo "</div>";
            echo "<div class=\"form-group\">";
            echo "<label for=\"password\">Ingrese la nueva contraseña</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"password\" name=\"password\" required=\"required\" placeholder=\"Contraseña de acceso\" value=\"".$row["password"]."\">";
            echo "</div>";
            echo "<p class=\"card-text\">¿Cual será su rol?</p>";
            echo "<div class=\"form-group text-center\">";
            echo "<div class=\"form-check form-check-inline px-3\">";

            if($row["role"]==="admin") {
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role1\" value=\"admin\" checked>";
            }else{
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role1\" value=\"admin\">";
            }
            
            echo "<label class=\"form-check-label\" for=\"role1\">Administrador</label>";
            echo "</div>";
            echo "<div class=\"form-check form-check-inline px-3\">";

            if($row["role"]==="technical") {
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role2\" value=\"technical\" checked>";
            }else{
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"role\" id=\"role2\" value=\"technical\">";
            }
            
            echo "<label class=\"form-check-label\" for=\"role2\">Técnico</label>";
            echo "</div>";
            echo "</div>";
            echo "<div class=\"text-center\">";
            echo "<button type=\"submit\" class=\"btn btn-primary px-4 mt-3\">Actualizar datos del ususario</button>";
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
        $sql = "SELECT count(*) as total_steps, support.id, support.title, support.description FROM support, step WHERE support.id=? and support.id=step.support_id";        
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
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=" . $row["id"]."\" class=\"btn btn-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"admin_index.php?delete_support_id=".$row["id"]."\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
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
    function AdminSupportPrintSearchSupportCards()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

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
            // echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
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
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_index.php?support_id=" . $row["id"]."\" class=\"btn btn-success mr-lg-3 mb-2 mb-md-0 px-4\"> Ver solución </a>";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=".$row["id"]."\" class=\"btn btn-warning mr-lg-3 ml-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"admin_index.php?delete_support_id=".$row["id"]."\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t\t</div>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
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
            return;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            // echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
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
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_index.php?support_id=" . $row["id"]."\" class=\"btn btn-success mr-lg-3 mb-2 mb-md-0 px-4\"> Ver solución </a>";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=".$row["id"]."\" class=\"btn btn-warning mr-lg-3 ml-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"admin_index.php?delete_support_id=".$row["id"]."\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
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
    /*
        $mode opciones
        1- admin_index
        2- admin_edit_support
        3- admin_add_support
     */
    function AdminSupportPrintStepCards($mode="admin_index")
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
            return;
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
            echo "\t\t<p class=\"card-text\">Descripción:</p>";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t</div>";
            // PRINT IMAGE JPG, PNG, GIF
            if(isset($row["image"]) && !empty($row["image"])){
                echo "\t<div class=\"card-body\">";
                echo "\t\t<p class=\"card-text\">URL Imagen:</p>";
                echo "\t\t<p class=\"card-text\">".$row["image"]."</p>";
                echo "\t</div>";
                echo "\t<img src=\"".$row["image"]."\" class=\"card-img-top mb-2\" alt=\"...\">";                
            }              
            // if(TRUE){
            //     echo "\t<img src=\"img/prueba4.gif\" class=\"card-img-top mb-2\" alt=\"...\">";
            // }              

            //  PRINT VIDEO YOUTUBE
            // if(TRUE){
            //     echo "<div class=\"embed-responsive embed-responsive-16by9\">";
            //     echo "\t<iframe class=\"embed-responsive-item\" src=\"https://www.youtube.com/embed/zpOULjyy-n8?rel=0\" allowfullscreen></iframe>";
            //     echo "</div>";
            // }

            //  PRINT VIDEO MP4
            // if(TRUE){
            //     echo "<video class=\"video-fluid\" autoplay loop muted>";
            //     echo "\t<source type=\"video/mp4\" src=\"img/prueba5.mp4\" />";
            //     echo "</video>";
            // }

            echo "\t<div class=\"text-center mt-2 mb-3\">";        
            echo "\t\t<a href=\"admin_edit_support.php?step_id=".$row["id"]."\" class=\"btn btn-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t<a href=\"".$mode.".php?delete_step_id=".$row["id"]."&support_id=".$_GET["support_id"]."\" class=\"btn btn-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

    }

    function AdminDeleteImageReference($imagePath){
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "UPDATE step SET image=? WHERE image=?";
        $stmt = $conn->prepare($sql); 
        $empty="";
        $stmt->bind_param("ss", $empty, $imagePath);
        $status = $stmt->execute();        
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if ($status === FALSE) return FALSE;
        
        return TRUE;
    }

    function AdminSupportPrintUsersTable(){
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM users ORDER BY fullname ASC";
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
            return;
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            // echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);
            return;
        }

        echo "<table class=\"table tablesorter\" id=\"tableUsers\">";
        echo "\t<thead class=\"thead-dark\">";
        echo "\t\t<tr>";
        echo "\t\t\t<th class=\"min-w200\" scope=\"col\">Nombre</th>";
        echo "\t\t\t<th scope=\"col\">Rol</th>";
        echo "\t\t\t<th scope=\"col\">Usuario</th>";
        echo "\t\t\t<th scope=\"col\">Contraseña</th>";
        echo "\t\t\t<th class=\"min-w150\" scope=\"col\">Acciones</th>";
        echo "\t\t</tr>";
        echo "\t</thead>";
        echo "\t<tbody>";
        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {
            
            echo "\t\t<tr>";
            echo "\t\t\t<td>".$row["fullname"]."</td>";
            echo "\t\t\t<td>".$row["role"]."</td>";
            echo "\t\t\t<td>".$row["username"]."</td>";
            echo "\t\t\t<td>".$row["password"]."</td>";
            echo "\t\t\t<td>";            
            echo "\t\t\t\t<a href=\"admin_users.php?edit_user_id=".$row["id"]."\" class=\"btn btn-success\"><i class=\"fas fa-edit\"></i></a>";
            echo "\t\t\t\t<a href=\"admin_users.php?delete_user_id=".$row["id"]."\" class=\"btn btn-danger\"><i class=\"far fa-trash-alt\"></i></a>";
            echo "\t\t\t</td>";
            echo "\t\t</tr>";
            
        }
        echo "\t</tbody>";
        echo "</table>";

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

?>