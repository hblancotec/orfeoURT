<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$ruta_raiz = ".";
//include "$ruta_raiz/config.php";
require_once $ruta_raiz."/envioEmail.php";
echo enviarCorreo(null, array("jzabala@dnp.gov.co"), null, "cuerpo", "Anulacion automatica de radicados.");
?>


