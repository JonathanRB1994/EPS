<!-- ARCHIVOS CON FUNCIONALIDADES NECESARIAS -->
<?php
    // Conexión a la BD de suporte técnico
    require '../vendor/ticket_db.php';        


    // Imprimir tarjetas deacuerdo a la busqueda en la categoria
    if( isset($_POST["tipoIncidencia"]) && $_POST["hiddenAll"] && $_POST["other"]) {
        PrintIncidencias($_POST["tipoIncidencia"], "", $_POST["hiddenAll"], $_POST["other"]);
        return;
    }        
?>
