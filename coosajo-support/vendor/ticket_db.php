<?php
    require_once("global_vars.php"); 

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

    // Imprimir opciones de incidencias
    function PrintIncidencias($tipoIncidencia = "all", $incidencia="", $hiddenAll="no", $otros="no"){ 
        if($incidencia==="" && $hiddenAll==="no"){
            if($tipoIncidencia=="all"){
                echo "<option value='{\"id\":\"all\",\"name\":\"all\"}' selected >Todas las incidencias</option>";
            } else{
                echo "<option value='{\"id\":\"all\",\"name\":\"all\"}'>Todas las incidencias</option>";
            }
        }        
        
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM incidencia WHERE id_incidencia_tipo = ? AND estado=1 ORDER BY nombre_incidencia ASC";                
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("s", $tipoIncidencia);
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
            //echo "Error: La ejecución de la consulta falló debido a: \n";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            return;
        }        

        $i=0;
        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {   
            $datos = array('id'=>$row["id"], 'name'=>$row["nombre_incidencia"]);
            $json = json_encode($datos);
            if($i==0 && $incidencia=="" && $hiddenAll=="yes" & $otros=="no"){
                echo "<option value='".$json."' selected >" . $row["nombre_incidencia"] . "</option>"; 
            }else if($incidencia == $row["id"]) {
                echo "<option value='".$json."' selected >" . $row["nombre_incidencia"] . "</option>"; 
            }else{
                echo "<option value='".$json."' >" . $row["nombre_incidencia"] . "</option>"; 
            }
            $i++;            
        } 
        if($otros=="yes") echo "<option value='{\"id\":\"". ID_OTROS ."\",\"name\":\"". NAME_OTROS ."\"}'>". NAME_OTROS ."</option>";       
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        return;
    }

    // Imprimir opciones de tipo de incidencias
    function PrintIncidenciasTipo($incidencia = "all"){  
        $fristId = 0;      
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM incidencia_tipo WHERE estado=1 ORDER BY nombre_tipo_incidencia ASC";                
        $stmt = $conn->prepare($sql); 
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
            return 0;
        }
        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {            
            //echo "Error: La ejecución de la consulta falló debido a: \n";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            return 0;
        }     
        $i =0;   
        // Imprimir los datos        
        while ($row = $result->fetch_assoc())
        {    
            if($i==0) $fristId = $row["id"];
            $i++;
            $datos = array('id'=>$row["id"], 'name'=>$row["nombre_tipo_incidencia"]);
            $json = json_encode($datos);
            if($row["id"]==$incidencia){
                echo "<option value='". $json ."' selected  >" . $row["nombre_tipo_incidencia"] . "</option>";            
            } else{
                echo "<option value='". $json ."'>" . $row["nombre_tipo_incidencia"] . "</option>";            
            }                     
        }        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
        return $fristId;
    }

    // Inserte del nuevo ticket
    function TicketGenerate(){
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "INSERT INTO ticket(cif, id_incidencia, id_tipo_incidencia, otra_incidencia, descripcion, fecha_creacion, id_estado, id_subestado) VALUES(?,?,?,?,?,now(),1,1)";
        $stmt = $conn->prepare($sql);       
        $ji = json_decode( $_POST["incidencia"]);
        $jt = json_decode( $_POST["tipo_incidencia"]);

        $otra_incidencia="";
        if($ji->id == ID_OTROS && !empty($_POST["other"])){
            $otra_incidencia = $_POST["other"];
        } else if($ji->id == ID_OTROS){
            $otra_incidencia = NAME_OTROS;
        }
        
        $stmt->bind_param("siiss", $_POST["CIF"], $ji->id, $jt->id, $otra_incidencia,$_POST["description"]);
        $stmt->execute();
        $result = $stmt->insert_id;
        
        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);
                
        return $result;                
    }

    // Obtener el nombre de la incidencia
    function GetNameIncidencia($id){
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "SELECT * FROM incidencia WHERE id = ? ";        
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $id);
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

            return "none";
        }

        // Verificar la obtencion de datos en la consulta
        if ($result->num_rows === 0) {
            echo "Lo sentimos. No se encontraron resultados para la consulta.";
            // Cerrar prepared statement
            $stmt->close();
            // Terminar la conexion
            mysqli_close($conn);

            return "none";
        }


        $name = "";
        // Imprimir los datos
        while ($row = $result->fetch_assoc())
        {            
            $name = $row["nombre_incidencia"];
        }

        // Cerrar prepared statement
        $stmt->close();
        // Terminar la conexion
        mysqli_close($conn);

        return $name;
    }

    // imprimri ticket recien creado
    function TicketPrint($isNew="no", $token=0){
        // Obtener la conexión
        $conn = TicketConexion();

        // Script de la ocnsulta
        $sql = "SELECT ticket.id, ticket.cif, ticket.cif_asignado, ticket.descripcion, ticket.id_incidencia, ticket.otra_incidencia, incidencia_tipo.nombre_tipo_incidencia, ticket_estado.nombre_estado, ticket_subestado.nombre_subestado ";
        $sql = $sql . "FROM ticket ";
        //$sql = $sql . "INNER JOIN incidencia ON incidencia.id = ticket.id_incidencia ";
        $sql = $sql . "INNER JOIN incidencia_tipo ON incidencia_tipo.id = ticket.id_tipo_incidencia ";
        $sql = $sql . "INNER JOIN ticket_estado ON ticket_estado.id_estado = ticket.id_estado ";
        $sql = $sql . "INNER JOIN ticket_subestado ON ticket_subestado.id = ticket.id_subestado ";
        $sql = $sql . "WHERE ticket.id=?";                

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
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">Información del ticket</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            echo "\t<p class=\"card-text\">Tu número de ticket es invalido, porfavor verificalo.</p>";                          
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
            if($row["id_incidencia"] == ID_OTROS){

            }
            
            // INFORMACION DEL TICKET
            echo "<div class=\"card bg-dark mt-4 text-white\">";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">Información del ticket " . $row["id"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";
            if($isNew==="yes") echo "\t<p class=\"card-text\">Porfavor guarde su numero de token para podelo consultar posteriormente</p>";
            echo "\t<p class=\"card-text\">Número de ticket: ".$row["id"]."</p>";
            echo "\t<p class=\"card-text\">CIF solicitante: ".$row["cif"]."</p>";  
            echo "\t\t<p class=\"card-text\">Descripción: " . $row["descripcion"] . "</p>";  
            
            $incidenciaName = "";
            if($row["id_incidencia"] == ID_OTROS){
                echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". NAME_OTROS . " -> ". $row["otra_incidencia"] ."</small></p>"; 
            }else{
                echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_tipo_incidencia"] ." -> ". GetNameIncidencia($row["id_incidencia"]) . "</small></p>"; 
            }            
                
            // ESTADO DEL TICKET
            echo "\t</div>";
            echo "\t<div class=\"card-header \">";
            echo "\t\t<h5 class=\"card-title inline\">Estado del ticket " . $row["id"] . "</h5>";            
            echo "\t</div>";
            echo "\t<div class=\"card-body\">";

            // Get persona asignada
            if(empty($row["cif_asignado"])){
                echo "\t<p class=\"card-text\">CIF asignado: Este ticket no tiene una persona asignada</p>";  
            }else{
                echo "\t<p class=\"card-text\">CIF asignado: ". $row["cif_asignado"] ."</p>";  
            }
            
            echo "\t\t<p class=\"card-text\"><small class=\"text-muted\">" . $row["nombre_estado"] ." -> ". $row["nombre_subestado"] ."</samll></p>";
                                              
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