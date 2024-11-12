<?php

session_start();
include("../config.php");
include '../include/class/mime.class.php';
//$ruta = base64_decode($_GET['ruta']);
$ruta = $_GET['ruta'];
if (file_exists($ruta)) {
        $nombre = substr($ruta, strripos($ruta, "/") + 1);
        $tipo = Mime::tipoMime($ruta);
        header("Content-type: $tipo");
        header('Content-Disposition: inline; filename="' . $nombre . '"');
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($ruta));
        readfile($ruta);
}
else
    header("Location: error/HTTP_NOT_FOUND.html");
?>
