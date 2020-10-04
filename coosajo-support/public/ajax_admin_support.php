<!-- ARCHIVOS CON FUNCIONALIDADES NECESARIAS -->
<?php
    // Conexión a la BD de suporte técnico
    require '../vendor/admin_support_db.php';        

    // Imprimir tarjetas deacuerdo a la busqueda en la categoria
    if(isset($_POST["tendencia"]) && isset($_POST["tipoIncidencia"]) && isset($_POST["incidencia"])) {
        AdminSupportPrintSupportCards($_POST["tendencia"], $_POST["tipoIncidencia"], $_POST["incidencia"]);
        return;
    }        
?>