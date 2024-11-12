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
$ruta_raiz = ".";
include_once ("$ruta_raiz/_conf/constantes.php");
require_once ('HTML/Template/IT.php');
require_once (ORFEOPATH . "include/db/ConnectionHandler.php");

session_start();
if (! isset($_SESSION['dependencia']))
    include (ORFEOPATH . "rec_session.php");
$contStyle = 1;
$ADODB_COUNTRECS = false;
$depeNombre = '';
$krd = $_GET['krd'];
$codigoCarp = $_GET['carpetaInf'];
$usuaDoc = $_SESSION['usua_doc'];
$usuaLogin = $krd;
$depeCodi = $_SESSION['dependencia'];
$tituloForm = 'CARPETA DE INFORMADOS';
$krdOld = $krd;
$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
$subCarpetaNombre = '';
$estiloRow = array();
$estiloRow[] = 'listado1';
$estiloRow[] = 'listado2';
$estiloLeido = array();
$estiloLeido[] = 'no_leidos';
$estiloLeido[] = 'leidos';
$tipoRadicado = 'INFORMADOS';
$variables = ''; // variables de de session que se envian por GET
$idNameSession = session_name() . "=" . session_id();

$db = new ConnectionHandler(ORFEOPATH);
$tpl = new HTML_Template_IT(TPLPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$sqlDep = "SELECT DEPE_NOMB FROM DEPENDENCIA WHERE DEPE_CODI = $depeCodi";

$sqlCarp = "SELECT INFDIR.SGD_INFDIR_NOMBRE,
                        INFDIR.SGD_INFDIR_CODIGO
                    FROM SGD_INFDIR_INFORMADOSDIR INFDIR
                    WHERE INFDIR.USUA_DOC = '$usuaDoc' AND
                            INFDIR.USUA_LOGIN = '$krd' AND
                            INFDIR.DEPE_CODI = $depeCodi AND
                            INFDIR.SGD_INFDIR_CODIGO <> $codigoCarp
                    ORDER BY INFDIR.SGD_INFDIR_NOMBRE";

$sqlCarInf = "SELECT SGD_INFDIR_NOMBRE
                    FROM SGD_INFDIR_INFORMADOSDIR 
                    WHERE SGD_INFDIR_CODIGO = $codigoCarp AND
                            DEPE_CODI = $depeCodi";
// No se puede cambiar la forma en que se muestra la fecha
// por que realiza mal el calculo del numero de dias restantes
$sqlFecha = $db->conn->SQLDate("d-m-Y H:i A", "RAD.RADI_FECH_RADI");
$sqlOffset = $db->conn->OffsetDate("TPR.SGD_TPR_TERMINO", "RAD.RADI_FECH_RADI");
$systemDate = $db->conn->sysTimeStamp;
$redondeo = "dbo.diashabilestramite($sqlOffset, $systemDate)";
// $redondeo = $db->conn->round($sqlOffset."-".$systemDate);

/**
 * Ordenamiento por seleccion
 */

if ($orden_cambio == 1) {
    if (! $orderTipo) {
        $orderTipo = " DESC";
    } else {
        $orderTipo = "";
    }
}

if (! $orno) {
    $orno = 1;
    $ascdesc = $orderTipo;
}

$imagen = "flechadesc.gif";

$img1 = "";
$img2 = "";
$img3 = "";
$img4 = "";
$img5 = "";
$img6 = "";

if ($ordcambio) {
    if ($ascdesc == "") {
        $ascdesc = "DESC";
        $imagen = "flechadesc.gif";
    } else {
        $ascdesc = "";
        $imagen = "flechaasc.gif";
    }
} else if ($ascdesc == "DESC")
    $imagen = "flechadesc.gif";
else
    $imagen = "flechaasc.gif";

if ($orno == 1) {
    $order = " RADI_NUME_RADI  $ascdesc";
    $img1 = "<img src='./iconos/$imagen' border=0 >";
}
if ($orno == 2) {
    $order = " RADI_FECH_RADI  $ascdesc";
    $img2 = "<img src='./iconos/$imagen' border=0 >";
}
if ($orno == 3) {
    $order = " INF.INFO_DESC $ascdesc";
    $img3 = "<img src='./iconos/$imagen' border=0 >";
}
if ($orno == 4) {
    $order = " TPR.SGD_TPR_DESCRIP $ascdesc";
    $img4 = "<img src='./iconos/$imagen' border=0 >";
}
if ($orno == 5) {
    $order = " DIAS_RESTANTES  $ascdesc";
    $img5 = "<img src='./iconos/$imagen' border=0 >";
}
if ($orno == 6) {
    $order = " INFORMADOR  $ascdesc";
    $img6 = "<img src='./iconos/$imagen' border=0 >";
}
/**
 * fin de ordenamiento por seleccion
 */

$sqlInf = "SELECT CONVERT(VARCHAR(15),RAD.RADI_NUME_RADI) RADI_NUME_RADI,
                        $sqlFecha AS RADI_FECH_RADI,
                        RAD.RADI_PATH,
                        INF.INFO_DESC,
                        TPR.SGD_TPR_DESCRIP,
                        $redondeo as DIAS_RESTANTES,
                        USUA.USUA_NOMB INFORMADOR,
                        INF.INFO_LEIDO
                    FROM RADICADO RAD,
                            INFORMADOS INF,
                            SGD_TPR_TPDCUMENTO TPR,
                            USUARIO USUA
                    WHERE RAD.RADI_NUME_RADI = INF.RADI_NUME_RADI AND
                            RAD.TDOC_CODI = TPR.SGD_TPR_CODIGO AND
                            USUA.USUA_DOC = INF.INFO_CODI AND
                            INF.SGD_INFDIR_CODIGO = $codigoCarp
                    ORDER BY $order ";
if (! $krd)
    $krd = $krdOsld;

if (! $carpeta)
    $carpeta = $carpetaOld;
$tpl->loadTemplatefile('listaCarpetaInf.tpl');

$rsDep = $db->conn->Execute($sqlDep);

if (! $rsDep->EOF) {
    $depeNombre = $rsDep->fields['DEPE_NOMB'];
}
$rsDep->MoveNext();

// Capturando nombre de la carpeta
$rsCarInf = $db->conn->Execute($sqlCarInf);
if (! $rsCarInf->EOF) {
    $subCarpetaNombre = $rsCarInf->fields['SGD_INFDIR_NOMBRE'];
} else {
    var_dump("Error no llego identificador de la carpeta");
    exit();
}

// Capturando y asignado a la plantilla las carpetas del usuario
$rsCarp = $db->conn->Execute($sqlCarp);
while (! $rsCarp->EOF) {
    $tpl->setCurrentBlock('carpetas');
    $tpl->setVariable('CODIGO_DIR', $rsCarp->fields['SGD_INFDIR_CODIGO']);
    $tpl->setVariable('NOMBRE_DIR', $rsCarp->fields['SGD_INFDIR_NOMBRE']);
    $tpl->parseCurrentBlock('carpetas');
    $rsCarp->MoveNext();
}
$rsCarp->Close();

// Capturando radicados informados de la carpeta
$rsInf = $db->conn->Execute($sqlInf);
while (! $rsInf->EOF) {
    $variables = "verrad=" . $rsInf->fields['RADI_NUME_RADI'] . "&$idNameSession&krd=$krd";
    $tpl->setVariable('RADI_NUME_RADI', $rsInf->fields['RADI_NUME_RADI']);
    $tpl->setVariable('VARIABLES', $variables);
    $tpl->setVariable('RUTA_RADICADO', BODEGAURL . $rsInf->fields['RADI_PATH']);
    $tpl->setVariable('RADI_FECH_RADI', $rsInf->fields['RADI_FECH_RADI']);
    $tpl->setVariable('INFO_DESC', $rsInf->fields['INFO_DESC']);
    $tpl->setVariable('RADI_FECH_RADI', $rsInf->fields['RADI_FECH_RADI']);
    $tpl->setVariable('USUA_DOC', $usuaDoc);
    $tpl->setVariable('TIPO_DOC', $rsInf->fields['SGD_TPR_DESCRIP']);
    $tpl->setVariable('DIAS_RESTANTES', $rsInf->fields['DIAS_RESTANTES']);
    $tpl->setVariable('INFORMADOR', $rsInf->fields['INFORMADOR']);
    $tpl->setVariable('ESTILO_ROW', $estiloRow[$cont % 2]);
    $tpl->setVariable('ESTILO_LEIDO', $estiloLeido[$rsInf->fields['INFO_LEIDO']]);
    $tpl->parse('row');
    $rsInf->MoveNext();
    $cont ++;
}

$rsInf->Close();

$tpl->setVariable('TITULO_FORM', $tituloForm);
$tpl->setVariable('DEPE_NOMBRE', $depeNombre);
$tpl->setVariable('USUA_LOGIN', $usuaLogin);
$tpl->setVariable('TITULO_FORM', $tituloForm);
$tpl->setVariable('TIPO_RADICADO', $tipoRadicado);
$tpl->setVariable('CARPETA_INF', strtoupper($subCarpetaNombre));
$encabezado = "listarContCarpInf.php?" . $idNameSession . "&krd=" . $krd . "&ascdesc=" . $ascdesc . "&ordcambio=1" . "&carpetaInf=" . $codigoCarp . "&usuaDoc=" . $usuaDoc;
$ordenamiento1 = $encabezado . "&orno=1";
$ordenamiento2 = $encabezado . "&orno=2";
$ordenamiento3 = $encabezado . "&orno=3";
$ordenamiento4 = $encabezado . "&orno=4";
$ordenamiento5 = $encabezado . "&orno=5";
$ordenamiento6 = $encabezado . "&orno=6";
$tpl->setVariable('ORDENAR_RADICADO_URL', $ordenamiento1);
$tpl->setVariable('IMAG1', $img1);
$tpl->setVariable('RADICADO_URL', $ordenamiento2);
$tpl->setVariable('IMAG2', $img2);
$tpl->setVariable('ORDENAR_ASUNTO_URL', $ordenamiento3);
$tpl->setVariable('IMAG3', $img3);
$tpl->setVariable('ORDENAR_TIPODOC_URL', $ordenamiento4);
$tpl->setVariable('IMAG4', $img4);
$tpl->setVariable('ORDERNAR_DIAS', $ordenamiento5);
$tpl->setVariable('IMAG5', $img5);
$tpl->setVariable('ORDERNAR_INF', $ordenamiento6);
$tpl->setVariable('IMAG6', $img6);
$tpl->show();
?>
