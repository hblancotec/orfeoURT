<?php
session_start();
// Obtenemos la información a modificar
$id = $_POST['id'];             //id field from POST above
$mail = $_POST['sgd_dir_mail']; //correo modificado enviado via POST
$radCopia = explode("_", $_POST['id']);
$radicado = $radCopia[0];
$copia =  $radCopia[1];
$ruta_raiz = "..";
require_once $ruta_raiz."/include/tx/Historico.php";

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) return json_encode ($ruta_raiz);

// connect to the database server
require_once $ruta_raiz."/config.php";
require "adodb/adodb.inc.php";
$dsn = $driver . "://$usuario:$contrasena@$servidor/$servicio";
$conn = NewADOConnection($dsn);
if ($conn->connect()) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
} else
    die("Error al conectar BD");

if($_POST['oper']=='edit') {
    $sql= "UPDATE SGD_DIR_DRECCIONES SET SGD_DIR_MAIL='$mail' WHERE RADI_NUME_RADI=$radicado AND SGD_DIR_TIPO='";
    $sql .= ((int)$copia > 0) ? "7$copia'" : "1'";
    $conn->Execute($sql);
    $objHist = new Historico();
    $objHist->Historico($conn);
    $observa = "Actualizacion datos destinatario.";
    $objHist->insertarHistorico(array($radicado), $_SESSION['dependencia'], $_SESSION['codusuario'], $_SESSION['dependencia'], $_SESSION['codusuario'], $observacion, $tipoTx);
}
?>
