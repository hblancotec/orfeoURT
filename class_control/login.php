<?php

session_start();

$ruta_raiz = "..";
include_once("$ruta_raiz/config.php");

if (isset($_POST['krd'])) {    //SOLITUD DE CAMBIO DE TRD
    if (isset($_POST['drd'])) {
        
        $krd = $_POST['krd'];
        $drd = $_POST['drd'];
        
        session_write_close();
        include $ruta_raiz . "/session_orfeo.php";
        
        if ($ValidacionKrd == "Si") {
            echo 1;
        } else {
            echo 0;
        }
    }
}
