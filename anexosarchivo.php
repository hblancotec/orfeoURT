<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "./sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}

require "config.php";
require "include/db/ConnectionHandler.php";
$db = new ConnectionHandler(".");

$radi   = $_GET['radi'];
$idanex = $_GET['idanex'];
$accion = $_GET['acutamanex'];

$sql = "select sum(anex_tamano) from anexos where anex_radi_nume=$radi and anex_marcar_envio_email=1";
$tamTodosSelec = (integer) $db->conn->GetOne($sql);
$sql = "select anex_tamano from anexos where anex_codigo='$idanex'";
$tamAnexoSelec = (integer) $db->conn->GetOne($sql);
$band = 0;
//Vamos a intentar incluir el anexo para envio
if ($accion == 1) {
    if ( ($tamTodosSelec + $tamAnexoSelec) <= $tamAnexosCorreo ) {
        // Se procede con la activacion en BD
        $sql = "update anexos set anex_marcar_envio_email=1 where anex_codigo='$idanex'";
        if ($db->conn->Execute($sql)) {
            $msg = "Registro actualizado.";
            $band = 1;
        } else {
            $msg = "No se pudo marcar el registro. Contacte al administrador.";
        }
    } else {
        $msg = "No se puede adicionar, excede el limite maximo (".($tamTodosSelec + $tamAnexoSelec)." > ".$tamAnexosCorreo." )";
    }
} else {
    // Se procede con la inactivacion en BD
    $sql = "update anexos set anex_marcar_envio_email=0 where anex_codigo='$idanex'";
    if ($db->conn->Execute($sql)) {
        $msg = "Registro actualizado.";
    }
}
echo $band . "|" .$msg;
?>