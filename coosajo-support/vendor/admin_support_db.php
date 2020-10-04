<?php    
    require_once("global_vars.php");   
    
    // ICONOS
    const ICON_EJE = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-eye-fill\" fill=\"grey\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z\"/><path fill-rule=\"evenodd\" d=\"M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z\"/></svg>";

    // Conexión al servidor DB de soporte técnico
    function AdminSupportConexion()
    {
        // Conexión al servidor DB de soporte técnico
        $conn = mysqli_connect(SUPPORT_HOSTNAME, SUPPORT_USERNAME, SUPPORT_PASSWORD, SUPPORT_DBNAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }  

    // Elimina las referencias a una imagen en la base de datos
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
    
    // ####################### GESTION DE USUARIOS ##############################
    // Insertar un usuario
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

    // Editar un usuario
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

    // Eliminar usuario
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

    // Imprimir formulacio para editar un usuario
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
            echo "<div class=\"card bg-dark px-0 mt-4 text-white col-12\">";
            echo "\t<div class=\"card-header pl-4 pt-4\">";
            echo "\t\t<h5 class=\"card-title\">Actualización de datos del usuario</h5>";
            echo "\t</div>";
            echo "<div class=\"card-body text-left\">";
            echo "<form action=\"admin_users.php?edit_user_id=".$_GET["edit_user_id"]."\" enctype=\"multipart/form-data\" method=\"POST\">";
            echo "<div class=\"form-group\">";
            echo "<label for=\"username\">Ingrese el nombre de usuario</label>";
            echo "<input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" required=\"required\" placeholder=\"Nombre de usuario\" value=\"".$row["username"]."\">";
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
            echo "<button type=\"submit\" class=\"btn btn-outline-primary px-4 mt-3\">Actualizar datos del usuario</button>";
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

    // Imprimir tabla de usuarios
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
            echo "\t\t\t\t<a href=\"admin_users.php?edit_user_id=".$row["id"]."\" class=\"btn btn-outline-primary\"><i class=\"fas fa-edit\"></i></a>";
            echo "\t\t\t\t<a href=\"admin_users.php?delete_user_id=".$row["id"]."\" class=\"btn btn-outline-danger\"><i class=\"far fa-trash-alt\"></i></a>";
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

    // ####################### GESTION DE PROBLEMAS TECNICOS ##############################                
    // Insertar un problema tecnico
    function AdminAddSupport()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "INSERT INTO support(title, description, id_tipo_incidencia, nombre_tipo_incidencia, id_incidencia, nombre_incidencia) VALUES(?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql); 

        // Incidencia y tipo incidencia son JSON
        $jt = json_decode( $_POST["tipo_incidencia"]);
        $ji = json_decode( $_POST["incidencia"]);        

        $stmt->bind_param("ssisis", $_POST["title"], $_POST["description"], $jt->id, $jt->name, $ji->id, $ji->name);
        $stmt->execute();
        $result = $stmt->insert_id;
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if (!$result) return FALSE;
        
        header('Location: admin_add_support.php?support_id='.$result);
    }
    
    // Editar problema tecnico
    function AdminEditSupport()
    {
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "UPDATE support SET title=?, description=?, id_tipo_incidencia=?, nombre_tipo_incidencia=?, id_incidencia=?, nombre_incidencia=? WHERE id=?";
        $stmt = $conn->prepare($sql); 

        $jt = json_decode( $_POST["tipo_incidencia"]);
        $ji = json_decode( $_POST["incidencia"]);  

        $stmt->bind_param("ssisisi", $_POST["title"], $_POST["description"], $jt->id, $jt->name, $ji->id, $ji->name, $_GET["support_id"]);
        $status = $stmt->execute();        
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        
        if ($status === FALSE) return FALSE;
        
        return TRUE;
    }

    // Eliminar problema tecnico con sus pasos
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

    // Imprimir formulario para editar problema tecnico
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

            // tarjeta de vista
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
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"" . $link . "\" class=\"btn btn-outline-danger mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t\t</div>";
            echo "\t</div>";
            echo "</div>";


            // Imprimir formulario para editar problema tecnico
            echo "<div class=\"card bg-dark my-4 px-0 text-white col-12\">";
            echo "\t<div class=\"card-header pl-4 pt-4\">";
            echo "\t\t<h5 class=\"card-title\">Datos del problema técnico</h5>";
            echo "\t</div>";
            echo "\t<div class=\"card-body p-4 text-left\">";
            echo "\t\t<form action=\"admin_edit_support.php?support_id=".$_GET["support_id"]."\" method=\"POST\">";

            // IR A TICKET DB E IMPRIMIR LOS OPTIONS DE TIPO INCIDENCIA
            echo "<input type=\"hidden\" name=\"hiddenAll\" id=\"hiddenAll\" value=\"yes\">";
            echo "<div class=\"form-group\">";
            echo "<label for=\"tipoIncidencia\">Tipo de incidencia</label>";
            echo "<select class=\"form-control\" id=\"select_tipo_incidencia\" name=\"tipo_incidencia\">";
            PrintIncidenciasTipo($row["id_tipo_incidencia"]);
            echo "</select>";
            echo "</div>";

            // IR A TICKET DB E IMPRIMIR LOS OPTIONS DE INCIDENCIA
            echo "<div class=\"form-group\">";
            echo "<label for=\"incidencia\">Incidencia</label>";
            echo "<select class=\"form-control\" id=\"select_incidencia\" name=\"incidencia\">";
            PrintIncidencias($row["id_tipo_incidencia"], $row["id_incidencia"]);
            echo "</select>";
            echo "</div>";


            echo "\t\t\t<div class=\"form-group\">";
            echo "\t\t\t\t<label for=\"inputTitle\">Ingresa el título del problema técnico</label>";
            echo "\t\t\t\t<input type=\"text\" class=\"form-control\" id=\"inputTitle\" name=\"title\" required=\"required\" value=\"".$row["title"]."\" placeholder=\"Título del problema técnico\">";
            echo "\t\t\t</div>";
            echo "\t\t\t<div class=\"form-group\">";
            echo "\t\t\t\t<label for=\"inputDescription\">Ingresa su descripción</label>";
            echo "\t\t\t\t<textarea class=\"form-control\" id=\"inputDescription\" name=\"description\" required=\"required\" placeholder=\"Descripción\" rows=\"3\">".$row["description"]."</textarea>";
            echo "\t\t\t</div>";
            echo "\t\t\t<div class=\"text-center\">";
            echo "\t\t\t\t<button type=\"submit\" class=\"btn btn-outline-primary px-4 mt-3\">Actualizar problema técnico</button>";
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

    // Imprimir tarjeta de soporte seleccionada
    function AdminSupportPrintSupportCard()
    {
        $total_steps = 0;
        // Obtener la conexión
        $conn = AdminSupportConexion();

        // Script de la ocnsulta
        $sql = "SELECT count(*) as total_steps, support.id, support.title, support.description, support.views, support.nombre_incidencia, support.nombre_tipo_incidencia FROM support, step WHERE support.id=? and support.id=step.support_id";        
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
            echo "\t<div class=\"card-body \">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". $row["nombre_incidencia"] . "</small></p>"; 
            echo "\t\t<p class=\"card-text text-center mb-3\">";
            echo "<span class=\" pr-1 \">". ICON_EJE."</span>";
            echo "<small class=\"text-muted\"> ". $row["views"] ." vistas";
            echo "</small>";
            echo "</p>";           
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=" . $row["id"]."\" class=\"btn btn-outline-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"admin_index.php?delete_support_id=".$row["id"]."\" class=\"btn btn-outline-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
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
    
    // Imprimir tarjetas de soporte que coincidan con la busqueda
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
            echo "\t<div class=\"card-body \">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". $row["nombre_incidencia"] . "</small></p>"; 
            echo "\t\t<p class=\"card-text text-center mb-3\">";
            echo "<span class=\" pr-1 \">". ICON_EJE."</span>";
            echo "<small class=\"text-muted\"> ". $row["views"] ." vistas";
            echo "</small>";
            echo "</p>";           
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_index.php?support_id=" . $row["id"]."\" class=\"btn btn-outline-primary mr-lg-3 mb-2 mb-md-0 px-4\"> Ver solución </a>";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=".$row["id"]."\" class=\"btn btn-outline-warning mr-lg-3 ml-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"admin_index.php?delete_support_id=".$row["id"]."\" class=\"btn btn-outline-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
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
    function AdminSupportPrintSupportCards($tendencia = "views", $tipoIncidencia="all", $incidencia="all")
    {        
        if($tendencia==="last") $tendencia = "date_create";

        // Obtener la conexión
        $conn = AdminSupportConexion();

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
            echo "\t<div class=\"card-body \">";
            echo "\t\t<p class=\"card-text\">" . $row["description"] . "</p>";
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". $row["nombre_incidencia"] . "</small></p>"; 
            echo "\t\t<p class=\"card-text text-center mb-3\">";
            echo "<span class=\" pr-1 \">". ICON_EJE."</span>";
            echo "<small class=\"text-muted\"> ". $row["views"] ." vistas";
            echo "</small>";
            echo "</p>";           
            echo "\t\t<div class=\"text-center\">";
            echo "\t\t\t<a href=\"admin_index.php?support_id=" . $row["id"]."\" class=\"btn btn-outline-primary mr-lg-3 mb-2 mb-md-0 px-4\"> Ver solución </a>";
            echo "\t\t\t<a href=\"admin_edit_support.php?support_id=".$row["id"]."\" class=\"btn btn-outline-warning mr-lg-3 ml-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t\t<a href=\"admin_index.php?delete_support_id=".$row["id"]."\" class=\"btn btn-outline-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t\t</div>";
            echo "\t</div>";
            
            
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
    }

    // ####################### GESTION DE PASOS DE SOLUCION ##############################    
    // Editar un paso de solucion
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

    // Insertar paso de solucion
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
    
    // Eliminar paso de una solucion
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

    // Imprimir formulario para editar un paso de solucion
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
            echo "\t\t<p class=\"card-text\">Descripción: " . $row["description"] . "</p>";
            echo "\t</div>";
            if(isset($row["image"]) && !empty($row["image"])){
                echo "\t<div class=\"card-body\">";                
                echo "\t\t<p class=\"card-text\">URL de la imagen: ".$row["image"]."</p>";
                echo "\t</div>";
                echo "\t<img src=\"".$row["image"]."\" class=\"card-img-top mb-2\" alt=\"...\">";                
            }            
            echo "\t<div class=\"text-center mt-2 mb-3\">";        
            echo "\t\t<a href=\"\" class=\"btn btn-outline-danger mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t</div>";
            echo "</div>";

            // Imprimir formulario para editar problema tecnico
            echo "<div class=\"card bg-dark mt-4 px-0 text-left text-white col-12\">";
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

            if(UPLOAD_IMAGES){
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
            } else {
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
                echo "<div class=\"form-group text-center\" hidden>";
                echo "<div class=\"form-check form-check-inline px-3\">";
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"addURL\" id=\"inlineRadio5\" value=\"URL\" checked>";
                echo "<label class=\"form-check-label\" for=\"inlineRadio1\">URL</label>";
                echo "</div>";
                echo "<div class=\"form-check form-check-inline px-3\">";
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"addURL\" id=\"inlineRadio6\" value=\"image\">";
                echo "<label class=\"form-check-label\" for=\"inlineRadio2\">Imagen</label>";
                echo "</div>";
                echo "</div>"; 

                echo "<div class=\"form-group\" id=\"divImageOrURL\">";
                echo "<label for=\"inputImage\">Ingresar URL</label>";
                echo "<label for=\"inputImage\">Ingresa el URL de la imagen a mostrar</label>";
                echo "<input type=\"text\" class=\"form-control\" id=\"inputImage\" value=\"".$row["image"]."\" name=\"imageURL\" placeholder=\"URL de la imagen\">";
                echo "</div>";

                echo "<div id=\"divFileInput\" hidden>";
                echo "<label for=\"content-image\">Ingresa una imagen para mostrar</label>";
                echo "<div class=\"custom-file\" id=\"content-image\">";
                echo "<input type=\"file\" class=\"custom-file-input\" id=\"fileImage\" name=\"image\" lang=\"es\">";
                echo "<label class=\"custom-file-label\" for=\"fileImage\">Seleccionar imagen</label>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }


            echo "<div class=\"text-center\">";
            echo "<button type=\"submit\" class=\"btn btn-outline-primary px-4 mt-3\">Actualizar paso</button>";
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

    // Imprimir pasos de solucion, de un problema seleccionado
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
            echo "\t\t<p class=\"card-text\">Descripción: " . $row["description"] . "</p>";
            echo "\t</div>";
            // PRINT IMAGE JPG, PNG, GIF
            if(isset($row["image"]) && !empty($row["image"])){
                echo "\t<div class=\"card-body\">";                
                echo "\t\t<p class=\"card-text\">URL de la imagen: ".$row["image"]."</p>";
                echo "\t</div>";
                echo "\t<img src=\"".$row["image"]."\" class=\"card-img-top mb-2\" alt=\"...\">";                
            }              

            echo "\t<div class=\"text-center mt-2 mb-3\">";        
            echo "\t\t<a href=\"admin_edit_support.php?step_id=".$row["id"]."\" class=\"btn btn-outline-warning mr-lg-3 mb-2 mb-md-0 px-4\"> Editar </a>";
            echo "\t\t<a href=\"admin_index.php?delete_step_id=".$row["id"]."&support_id=".$_GET["support_id"]."\" class=\"btn btn-outline-danger ml-lg-3 mb-2 mb-md-0 px-4\"> Eliminar </a>";
            echo "\t</div>";
            echo "</div>";
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

    }        
?>