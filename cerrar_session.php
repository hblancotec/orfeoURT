<?php
session_start();

require "config.php";
include 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);
if ($conn) {
    $fecha = "'FIN  " . date("Y:m:d H:m:i:s") . "'";
    $isql = "update usuario set usua_sesion=" . $fecha . " where USUA_SESION = '" . substr(session_id(), 0, 30) . "'";
    $conn->Execute($isql);
    
    require "funcGetIp.php";
    
    $sql = "INSERT INTO SGD_HIST_AUTENTICACION (USUA_LOGIN, USUA_DOC, DIR_IP, SGD_TTR_CODIGO, AGENTE, OBSERVACION) VALUES ('" .
    $_SESSION['krd'] . "','" . $_SESSION['usua_doc'] . "','" . getIpClient() . "', 46, '" . $_SERVER['HTTP_USER_AGENT'] . "', NULL)";
    $isql = $conn->Execute($sql);
    //exit();
}

$_SESSION = array();
if (session_destroy()) {
    session_unset();   
    
    header("Location: ".SERVIDOR."login.php");
}
?>