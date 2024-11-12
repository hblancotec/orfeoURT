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
require_once './_conf/constantes.php';
require_once ORFEOPATH . "include/db/ConnectionHandler.php";
require_once "HTML/Template/IT.php";
define('ADODB_ASSOC_CASE', 1);

$krd = strtoupper($krd);
$carpetasNumInfo = array();

$archivoExec = './eliminarDirectorioInf.php';
$sessionId = trim(session_id());

if (empty($_SESSION['dependencia']))
    include (ORFEOPATH . "rec_session.php");
$usuaDoc = (! empty($_SESSION['usua_doc'])) ? $_SESSION['usua_doc'] : null;
$depeCodi = $_SESSION['dependencia'];

if (empty($usuaDoc)) {
    $errorSession = "Error en la sessi&oacute;n del usuario";
    var_dump($errorSession);
    exit();
}

$phpSession = session_name() . '=' . session_id();
$action = $archivoExec . '?' . $phpSession . '&krd=' . $krd;

$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$tpl = new HTML_Template_IT(TPLPATH);
$tpl->loadTemplatefile('formEliminarDirectorioInf.tpl');
$tpl->setVariable('ACTION_FORM', $action);
$tpl->setVariable('PHPSESS_ID', $sessionId);
$tpl->setVariable('USUA_LOGIN', $krd);
$sqlCarp = "SELECT INFDIR.SGD_INFDIR_CODIGO,
                            INFDIR.SGD_INFDIR_NOMBRE
                                FROM SGD_INFDIR_INFORMADOSDIR INFDIR LEFT JOIN INFORMADOS INF ON
                                    INFDIR.SGD_INFDIR_CODIGO = INF.SGD_INFDIR_CODIGO
                    WHERE INFDIR.USUA_DOC = '$usuaDoc' AND
                            INFDIR.USUA_LOGIN = '$krd' AND
                            INFDIR.DEPE_CODI = $depeCodi AND
                            INF.RADI_NUME_RADI IS NULL
                    ORDER BY INFDIR.SGD_INFDIR_NOMBRE";

$rsCarp = $db->conn->Execute($sqlCarp);

// Asignado al select el nombre de las carpetas
while (! $rsCarp->EOF) {
    $tpl->setCurrentBlock('subcarpetas');
    $tpl->setVariable('VALUE_CARPETA', $rsCarp->fields['SGD_INFDIR_CODIGO']);
    $tpl->setVariable('NOMBRE_CARPETA', $rsCarp->fields['SGD_INFDIR_NOMBRE']);
    $tpl->parseCurrentBlock('subcarpetas');
    $rsCarp->MoveNext();
}
$rsCarp->Close();
$tpl->show();
?>
