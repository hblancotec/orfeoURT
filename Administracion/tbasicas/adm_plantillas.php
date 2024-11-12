<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

if(!isset($_SESSION['dependencia']))include "../rec_session.php";
include("../../config.php");
include("../../include/class/Plantillas.class.php");
include_once "../../include/db/ConnectionHandler.php";
$db = new ConnectionHandler("../..");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objPln = new Plantilla($db);

$fec=$_GET['fec']?$_GET['fec']:$_POST['fec'];
$tipoAyuda=$_GET['tipoAyuda']?$_GET['tipoAyuda']:$_POST['tipoAyuda'];
switch ($fec)
{
	case 1:
		$objPln->borraArchivo($_GET['rut']);
		echo $objPln->vistaDir(BODEGAPATH . "Ayuda/", 1, "../..");
		if($objPln->error)$error=$objPln->error;
		break;
	case 2:
		$objPln->agregaArchivo($tipoAyuda);
		echo $objPln->vistaDir(BODEGAPATH . "Ayuda/", 1, "../..");
		if($objPln->error)$error=$objPln->error;
		include("./vPlantillas.php");
		break;
	default:
		$tbl=$objPln->vistaDir(BODEGAPATH . "Ayuda/", 1, "../..");
		include("./vPlantillas.php");
		break;
}
?>