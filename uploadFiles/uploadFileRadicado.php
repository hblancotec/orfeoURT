<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
$ruta_raiz = "..";
if (! isset($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";
error_reporting(7);
$verrad = "";

/*
 * PROGRAMA DE CARGA DE IMAGENES DE RADICADOS
 * @author JAIRO LOSADA - DNP - SSPD
 * @version Orfeo 3.5.1
 *
 * @param $varBuscada sTRING Contiene el nombre del campo que buscara
 * @param $krd string Trae el Login del Usuario actual
 * @param $isql strig Variable temporal que almacena consulta
 */
?>

<HTML>
<head>
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
</head>
<BODY>
	<FORM ACTION="<?=$_SERVER['PHPSELF']?>?krd=<?=$krd?>&<?=session_name()?>=<?=session_id()?>" method="POST">
<?php
/**
 *
 * @param $varBuscada string
 *            Contiene el nombre del campo que buscara
 * @param $busq_radicados_tmp sting
 *            Almacena cadena de busqueda de radicados generada por pagina paBuscar.php
 */
$varBuscada = "RADI_NUME_RADI";
include "$ruta_raiz/envios/paEncabeza.php";
include "$ruta_raiz/envios/paBuscar.php";
$encabezado = "" . session_name() . "=" . session_id() . "&krd=$krd&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion&carpeta=$carpeta&tipo_carp=$tipo_carp&chkCarpeta=$chkCarpeta&busqRadicados=$busqRadicados&nomcarpeta=$nomcarpeta&agendado=$agendado&";
$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";
$encabezado = "" . session_name() . "=" . session_id() . "&adodb_next_page=1&krd=$krd&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion&carpeta=$carpeta&tipo_carp=$tipo_carp&nomcarpeta=$nomcarpeta&agendado=$agendado&orderTipo=$orderTipo&orderNo=";
?>
	</FORM>
	<FORM ACTION="formUpload.php?krd=<?=$krd?>&<?=session_name()?>=<?=session_id()?>" method="POST">
		<center>
			<input type="submit" value="Asociar Imagen del Radicado"
				name=asocImgRad class="botones_largo">
		</center>
<?php
if ($Buscar and $busq_radicados_tmp) {
    include "$ruta_raiz/include/query/uploadFile/queryUploadFileRad.php";
    $rs = $db->conn->Execute($query);
    if ($rs->EOF) {
        echo "<hr> <center> <b>
								<span class='alarmas'>No se encuentra ningun radicado con el criterio de busqueda</span>
							  </b> </center> </hr>";
    } else {
        $rs3 = $db->conn->Execute($query3);
        $tpRad = substr($busqRadicados, - 1);
        // ## VERIFICA SI EL RADICADO ES DE SALIDA O MEMORANDO y
        // ## VERIFICA SI NO TRAE NADA EL RESULTADO DEL QUERY
        if (($tpRad == 1 || $tpRad == 3) && ($rs3->EOF)) {
            echo "<hr> <center> <b>
								<span class='alarmas'>El radicado no tiene asociada 
								la plantilla, por favor verifique</span>
							  </b> </center> </hr>";
        } else {
            $orderNo = 1;
            $orderTipo = " Desc ";
            $pager = new ADODB_Paginacion($db, $query, 'adodb', true, $orderNo, $orderTipo);
            $pager->checkAll = false;
            $pager->checkTitulo = true;
            $pager->toRefLinks = $linkPagina;
            $pager->toRefVars = $encabezado;
            $pager->descCarpetasGen = $descCarpetasGen;
            $pager->descCarpetasPer = $descCarpetasPer;
            $pager->Render(100, $linkPagina, 'chkAnulados');
        }
    }
}
?>
	</FORM>
</BODY>
</HTML>