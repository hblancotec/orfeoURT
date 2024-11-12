<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit;
}
else if (isset($_SESSION['krd'])) {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
$ruta_raiz = "..";

// Confirmar existencia de session
if (! isset($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";

// inicio de de ododb
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy = $db->conn->DBDate($fecha_hoy);

require 'smarty/libs/Smarty.class.php';
// Se configuran los parametros de smarty
$smarty = new Smarty();
$smarty->template_dir = ORFEOPATH . 'accionesMasivas/templates';
$smarty->compile_dir = BODEGAPATH . 'tmp';
$smarty->left_delimiter = '<!--{';
$smarty->right_delimiter = '}-->';

$dependencia = trim($_SESSION['depecodi']);
$codusuario = trim($_SESSION['codusuario']);
$usua_doc = trim($_SESSION['usua_doc']);

$smarty->assign("krd", $_SESSION['krd']); // recarga de session con el krd
?>
