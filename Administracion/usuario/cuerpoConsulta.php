<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1) {
    die(include "../../sinpermiso.php");
    exit();
}

$ruta_raiz = "../..";
if (! isset($_SESSION['dependencia']))
    include "../../rec_session.php";

$ano_ini = date("Y");
$mes_ini = substr("00" . (date("m") - 1), - 2);
if ($mes_ini == 0) {
    $ano_ini = $ano_ini - 1;
    $mes_ini = "12";
}
$dia_ini = date("d");
$ano_ini = date("Y");
if (! $fecha_ini)
    $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
$fecha_fin = date("Y/m/d");
$where_fecha = "";
$radSelec = "";
// $tpDepeRad = "NADA";
$ruta_raiz = "../..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$dep_sel = !empty($_REQUEST['dep_sel']) ? $_REQUEST['dep_sel'] : $_SESSION['dependencia'];
$nomcarpeta = "Consulta Usuarios";

if ($busq_radicados) {
    $busq_radicados = trim($busq_radicados);
    $textElements = explode(",", $busq_radicados);
    $newText = "";
    $i = 0;
    foreach ($textElements as $item) {
        $item = trim($item);
        if (strlen($item) != 0) {
            $i ++;
            if ($i > 1)
                $busq_and = " and ";
            else
                $busq_and = " ";
            $busq_radicados_tmp .= " $busq_and radi_nume_sal like '%$item%' ";
        }
    }
    $dependencia_busq1 .= " and $busq_radicados_tmp ";
} else {
    $sql_masiva = "";
}

if ($_GET['orden_cambio'] == 1) {
    if (! $orderTipo) {
        $orderTipo = "desc";
    } else {
        $orderTipo = "";
    }
}

$encabezado = session_name() . "=" . session_id();
$encabezado .= "&krd=$krd&pagina_sig=$pagina_sig";
$encabezado .= "&accion_sal=$accion_sal&radSelec=$radSelec";
$encabezado .= "&dependencia=" . $_SESSION['dependencia'] . "&dep_sel=" . $_GET['dep_sel'];
$encabezado .= "&selecdoc=$selecdoc&nomcarpeta=" . $_GET['nomcarpeta'] . "&orderTipo=" . $_GET['orderTipo'] . "&orderNo=" . $_GET['orderNo'];
$linkPagina = "$PHP_SELF?$encabezado&radSelec=$radSelec";
$linkPagina .= "&accion_sal=$accion_sal&nomcarpeta=" . $_GET['nomcarpeta'];
$linkPagina .= "&orderTipo=" . $_GET['orderTipo'] . "&orderNo=" . $_GET['orderNo'];
$carpeta = "nada";
$swBusqDep = "si";
$pagina_actual = "../usuario/cuerpoConsulta.php";
include "../paEncabeza.php";
$varBuscada = "u.usua_login";
$tituloBuscar = "Buscar Usuario(s) (Separados por coma)";
include "../paBuscar.php";
$pagina_sig = "../usuario/consultaDatosGrales.php";
$accion_sal = "Consultar";
include "../paOpciones.php";

if ($busq_radicados_tmp) {
    $where_fecha = " ";
} else {
    $fecha_ini = mktime(00, 00, 00, substr($fecha_ini, 5, 2), substr($fecha_ini, 8, 2), substr($fecha_ini, 0, 4));
    $fecha_fin = mktime(23, 59, 59, substr($fecha_fin, 5, 2), substr($fecha_fin, 8, 2), substr($fecha_fin, 0, 4));
    $where_fecha = " (a.SGD_RENV_FECH >= " . $db->conn->DBTimeStamp($fecha_ini) . " and a.SGD_RENV_FECH <= " . $db->conn->DBTimeStamp($fecha_fin) . ") ";
    $dependencia_busq1 .= " $where_fecha and ";
}
/*
 * GENERACION LISTADO DE RADICADOS
 * Aqui utilizamos la clase adodb para generar el listado de los radicados
 * Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
 * el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
 */
?>
<html>
<head>
<title>Envio de Documentos. Orfeo...</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../estilos/orfeo.css">
</head>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
	<div id="spiffycalendar" class="text"></div>
	<link rel="stylesheet" type="text/css" href="js/spiffyCal/spiffyCal_v2_1.css">
	<form name='formEnviar' action='consultaDatosGrales.php?<?=$encabezado?>&usModo=2' method='post'>
<?php
$orderNo = $_GET['orderNo'];
$orderTipo = $_GET['orderTipo'];
if ($orderNo == 98 or $orderNo == 99) {
    $order = 1;
    if ($orderNo == 98)
        $orderTipo = "desc";
    if ($orderNo == 99)
        $orderTipo = "";
} else {
    if (! $orderNo) {
        $orderNo = 0;
    }
    $order = $orderNo + 1;
}
$sqlChar = $db->conn->SQLDate("d-m-Y H:i A", "SGD_RENV_FECH");
//$sqlConcat = $db->conn->Concat("a.radi_nume_sal", "'-'", "a.sgd_renv_codigo", "'-'", "a.sgd_fenv_codigo", "'-'", "a.sgd_renv_peso");
include "$ruta_raiz/include/query/administracion/queryCuerpoConsulta.php";
$rs = $db->conn->Execute($isql);
$nregis = $rs->fields["NOMBRE"];
if (! $nregis) {
    echo "<hr><center><b>NO se encontro nada con el criterio de busqueda</center></b></hr>";
} else {
    $pager = new ADODB_Paginacion($db, $isql, 'adodb', true, $orderNo, $orderTipo);
    $pager->toRefLinks = $linkPagina;
    $pager->toRefVars = $encabezado;
    $pager->Render($rows_per_page = 20, $linkPagina, $checkbox = chkEnviar);
}
?>
  </form>
</body>
</html>