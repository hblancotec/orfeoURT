<?php
session_start();
$ruta_raiz = ".";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
require_once $ruta_raiz."/_conf/constantes.php";
require_once ORFEOPATH . "include/db/ConnectionHandler.php";
require_once "HTML/Template/IT.php";
    
define('ADODB_ASSOC_CASE', 1);

$archivoExec= './crearDirectorioInf.php';
$archivoBorr= './formEliminarDirectorioInf.php';
$tituloForm = 'CREAR CARPETA INFORMADOS';
$sessionId  = trim(session_id());
$phpSession = session_name() . "=" . trim(session_id());
$action     = $archivoExec . "?" . $phpSession . "&krd=" . $krd;
$hrefBorrado= $archivoBorr . "?" . $phpSession . "&krd=" . $krd;
$errorMsg   = "Error debe Colocar El nombre de la Carpeta y la Descripci&oacute;n";
    
if(empty($_SESSION['dependencia'])) include (ORFEOPATH . "rec_session.php");
$usuaDoc = (!empty($_SESSION['usua_doc'])) ? $_SESSION['usua_doc'] : null;
$depeCodi= $_SESSION['dependencia'];

if (empty($usuaDoc)) {
    $errorSession = "Error en la sesi&oacute;n del usuario";
    var_dump($errorSession);
    exit();
}

$carpetaOld = $carpeta;
$tipoCarpOld= $tipo_carp;
$archivoExec= './crearDirectorioInf.php';
$archivoBorr= './formEliminarDirectorioInf.php';
$tituloForm = 'CREAR CARPETA INFORMADOS';

$action     = $archivoExec . "?" . $phpSession . "&krd=" . $krd;
$hrefBorrado= $archivoBorr . "?" . $phpSession . "&krd=" . $krd;
$textoBorrado = 'Borrar Carpetas Informados';
    
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$tpl = new HTML_Template_IT(TPLPATH);
    
$carpetaInf = array();
$carpetaInf = $_POST['carpetaInf'];
$datosVacio = false;

foreach ($carpetaInf as $dato) {
	if (empty($dato)){
        $datosVacio = true;
        break;
    }
}

if ($datosVacio) {
    // Mostrar error
    $tpl->loadTemplatefile('formCrearDirectorio.tpl');
    $tpl->setVariable('TITULO_FORM', $tituloForm);
    $tpl->setVariable('TITULO_INF', $tituloForm);
    $tpl->setVariable('ACTION_FORM', $action);
    $tpl->setVariable('HREF_BORRADO', $hrefBorrado);
    $tpl->setVariable('PHPSESS_ID', $sessionId);
    $tpl->setVariable('KRD', $krd);
    $tpl->setVariable('ERROR_CREACION', $errorMsg);
    $tpl->show();
    exit();
}

$errorIns = '';
$nombreCarpInf = $_POST['carpetaInf']['nombre'];
$descCarpInf = $_POST['carpetaInf']['descripcion'];
// Buscando Directorios con el mismo nombre
$sqlInf = "SELECT SGD_INFDIR_CODIGO FROM SGD_INFDIR_INFORMADOSDIR
           WHERE USUA_DOC = '$usuaDoc' AND
                 USUA_LOGIN = '$krd' AND
                 SGD_INFDIR_NOMBRE LIKE '$nombreCarpInf'";
    
$rsInf = $db->conn->Execute($sqlInf);

// Si encontro un directorio con el mismo nombre entonces error
if (!$rsInf->EOF) {
    var_dump("hay un directorio con el mismo nombre");
    exit();
} else {
    // insertar carpeta
    $sqlIns = "INSERT INTO SGD_INFDIR_INFORMADOSDIR ".
              "(USUA_DOC, USUA_LOGIN, SGD_INFDIR_NOMBRE, SGD_INFDIR_DESCRIPCION, DEPE_CODI) ".
              "VALUES ('$usuaDoc', '$krd', '$nombreCarpInf', '$descCarpInf', $depeCodi)";
        
    $rsIns = $db->conn->Execute($sqlIns);

    // Si hubo error en la insercion
    if ($rsIns === false) {
        $errorIns = "error al insertar el registro" . $db->conn->ErrorMsg();
        exit();
    }
}
    
$tpl->loadTemplatefile('verificacionCreacionDir.tpl');
$tpl->setVariable('TITULO_FORM', $tituloForm);
$tpl->setVariable('TITULO_INF', $tituloForm);
$tpl->setVariable('ACTION_FORM', $action);
$tpl->setVariable('HREF_BORRADO', $hrefBorrado);
$tpl->setVariable('PHPSESS_ID', $sessionId);
$tpl->setVariable('KRD', $krd);
$tpl->setVariable('CARPETA_INF', $nombreCarpInf);
$tpl->show();
?>