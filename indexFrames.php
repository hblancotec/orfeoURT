<?php

// chequear si se llama directo al script.
if ($_SERVER['HTTP_REFERER'] == "") {
    session_start();
    if (count($_SESSION) == 0) {
        die(include "./sinacceso.php");
        exit;
    } else if (isset($_SESSION['krd'])) {
        $krd = $_SESSION["login"];
    } else $krd = $_REQUEST['krd'];
}

require_once ("./_conf/constantes.php");
require_once ("./config.php");
require_once (PEARPATH . "HTML/Template/IT.php");

// Crea una instancia para utilizar el motor de plantilla
$tpl = new HTML_Template_IT(ORFEOPATH . "tpl");
session_start();
// Coloca el login en mayuscula para realizar consulta
$krd = strtoupper($krd);
$fechah = date("ymd") . "_" . time("hms");
$topArchivo = "./f_top.php";
$leftArchivo = "./correspondencia.php";
$mainArchivo = "./cuerpo.php";
$nombreSession = session_name();
$idSession = session_id();
$datosSesion = $nombreSession . "=" . $idSession . "&krd=" . $krd . "&fechah=" . $fechah;
$datosSesionLog = $datosSesion . "&swLog=" . $swLog;

if (!isset($_SESSION['dependencia'])) {
    include (ORFEOPATH . "rec_session.php");
}

if (!isset($_SESSION['dependencia'])) {
    die(include "./sinacceso.php");
}
$tpl->loadTemplatefile("indexFrames.tpl");
$tpl->setVariable("NOMBRESISTEMA", NOMBRESISTEMA);
$tpl->setVariable("TOP_ARCHIVO", $topArchivo);
$tpl->setVariable("LEFT_ARCHIVO", $leftArchivo);
$tpl->setVariable("MAIN_ARCHIVO", $mainArchivo);
$tpl->setVariable("DATOS_SESION", $datosSesion);
$tpl->setVariable("DATOS_SESION_LOG", $datosSesionLog);
$tpl->show();
?>
