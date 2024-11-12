<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "./sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
require_once "./_conf/constantes.php";
define('ADODB_ASSOC_CASE', 1);

if (! $_SESSION['dependencia'])
    include ORFEOPATH . "rec_session.php";
$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
$archivoExec = './crearDirectorioInf.php';
$archivoBorr = './formEliminarDirectorioInf.php';
$tituloForm = 'CREAR CARPETA INFORMADOS';
$sessionId = trim(session_id());
$phpSession = session_name() . "=" . trim(session_id());
$action = $archivoExec . "?" . $phpSession . "&krd=" . $krd;
$hrefBorrado = $archivoBorr . "?" . $phpSession . "&krd=" . $krd;
$textoBorrado = 'Borrar Carpetas Informados';
require_once ORFEOPATH . "include/db/ConnectionHandler.php";
require_once "HTML/Template/IT.php";

$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$tpl = new HTML_Template_IT(TPLPATH);
$tpl->loadTemplatefile('formCrearDirectorio.tpl');
$tpl->setVariable('TITULO_FORM', $tituloForm);
$tpl->setVariable('TITULO_INF', $tituloForm);
$tpl->setVariable('ACTION_FORM', $action);
$tpl->setVariable('HREF_BORRADO', $hrefBorrado);
$tpl->setVariable('PHPSESS_ID', $sessionId);
$tpl->setVariable('CARPETA_ELIMINADA', '&nbsp;');
$tpl->setVariable('KRD', $krd);
$tpl->show();
?>