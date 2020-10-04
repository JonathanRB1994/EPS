<?php
    // Definicio de variables globales a utilizar en la aplicacion
    const TITLE_PAGE = "<title>COOSAJO R.L.</title>";
    const FAV_ICON = "<link rel=\"icon\" type=\"image/png\" href=\"logos/coosajo.png\" sizes=\"32x32\">";
    
    // Variables de conexion a la base de datos de soporte tecnico
    define("SUPPORT_HOSTNAME","localhost");
    define("SUPPORT_USERNAME","root");
    define("SUPPORT_PASSWORD","");
    define("SUPPORT_DBNAME","support_db");

    // Variables de conexion a la base de datos de ticket
    define("TICKET_HOSTNAME","localhost");
    define("TICKET_USERNAME","root");
    define("TICKET_PASSWORD","");
    define("TICKET_DBNAME","ticket_db");

    // Directorio donde se almacenan las imágenes, el directorio debe existir
    const IMAGES_PATH = "img/";

    // Cuantas rows obtener de la db
    const LIMIT_SELECT = 10;   
    
    const ID_OTROS = 255;    
    const NAME_OTROS = "Otros";

    // Habilitar o deshabilitar subir Imágenes
    const UPLOAD_IMAGES = true;

    // Nombres de interfaz
    const TITLE_USER = "SOPORTE";
    const TITLE_TECHNICAL = "TÉCNICO";
    const TITLE_ADMIN = "ADMINISTRADOR";
?>