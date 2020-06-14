<?php    
    // Datos de conexión al servidor de soporte técnico
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

    

?>