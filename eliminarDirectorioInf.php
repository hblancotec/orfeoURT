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
require_once ORFEOPATH . 'include/db/ConnectionHandler.php';
require_once 'HTML/Template/IT.php';

define('ADODB_ASSOC_CASE', 1);
$carpetaCodigo = $_POST['carpetaCodigo'];
$carpetaNombre = '';
$msgExito = 'Eliminaci&oacute;n exitosa de la carpeta ';

if (empty($_SESSION['dependencia']))
    include (ORFEOPATH . 'rec_session.php');

$usuaDoc = (! empty($_SESSION['usua_doc'])) ? $_SESSION['usua_doc'] : null;
$depeCodi = $_SESSION['dependencia'];

$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$tpl = new HTML_Template_IT(TPLPATH);
$tpl->loadTemplatefile('formEliminarDirectorioInf.tpl');

if (empty($usuaDoc) || empty($carpetaCodigo)) {
    $errorSession = "Error en la sesi&oacute;n del usuario";
    var_dump($errorSession);
    exit(1);
}

$sqlVer = "SELECT COUNT(INF.RADI_NUME_RADI) AS TOTAL_RADICADOS,
                        INFDIR.SGD_INFDIR_NOMBRE
                FROM SGD_INFDIR_INFORMADOSDIR INFDIR LEFT JOIN INFORMADOS INF ON
                    INFDIR.SGD_INFDIR_CODIGO =  INF.SGD_INFDIR_CODIGO 
                WHERE INFDIR.SGD_INFDIR_CODIGO = $carpetaCodigo
                GROUP BY INFDIR.SGD_INFDIR_NOMBRE";

if ($carpetaCodigo != 'N') {
    $rsVer = $db->conn->Execute($sqlVer);
    
    // Verifica si la carpeta no contiene radicados
    if (! $rsVer->EOF) {
        $carpetaNombre = $rsVer->fields['SGD_INFDIR_NOMBRE'];
        if ($rsVer->fields['TOTAL_RADICADOS']) {
            $error = "La carpeta tiene todavia informados por favor mueva los radicados a otra carpeta";
            var_dump($error);
            exit();
        }
    }
    $rsVer->Close();
    
    $sqlDel = "DELETE FROM SGD_INFDIR_INFORMADOSDIR
                    WHERE SGD_INFDIR_CODIGO = $carpetaCodigo AND
                            USUA_DOC = '$usuaDoc' AND
                            DEPE_CODI = $depeCodi";
    
    $rsDel = $db->conn->Execute($sqlDel);
    // Si hubo un error en el borrado de la carpeta
    if ($rsDel === false) {
        echo $db->conn->ErrorMsg();
        var_dump("Error en el borrado");
        exit();
    }
    $rsDel->Close();
} else {
    $errorCarpeta = "No seleccion&oacute; ninguna subcarpeta a borrar";
}

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

$tpl->setVariable('CARPETA_ELIMINADA', (! empty($carpetaNombre)) ? $msgExito . $carpetaNombre : $errorCarpeta);
$tpl->show();
?>
