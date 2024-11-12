<?php
session_start();
include_once "./config.php";
// Inicio de sesion
$ruta_raiz = ".";
if (! isset($_SESSION['dependencia'])) {
    include "./rec_session.php";
}
if (! isset($_GET['carpeta'])) {
    $_GET['carpeta'] = 2;
}
if (! isset($_GET['tipo_carpt'])) {
    if (isset($_GET['tipo_carp'])) {
        $_GET['tipo_carpt'] = $_GET['tipo_carp'];
    } else {
        $_GET['tipo_carpt'] = 0;
    }
}
if (isset($_REQUEST['chkCarpeta'])) {
    $_GET['tipo_carpt'] = - 1;
    $_GET['carpeta'] = - 1;
}
include 'config.php';
define('PATHMVC', './orfeo.api/');
$_GET['pathMVC'] = PATHMVC;
include 'include/class/ConectorMVC.class.php';
$obj = new ConectorMVC();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="estilos/orfeo.css">
</head>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
<?php
include "./envios/paEncabeza.php";
include_once "./include/db/ConnectionHandler.php";
require_once ORFEOPATH . "class_control/Mensaje.php";
if (! $db)
    $db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
if ($_REQUEST['busqRadicados']) {
    $_GET['NoRadicado'] = $_REQUEST['busqRadicados'];
}
$encabezado = "" . session_name() . "=" . session_id();
$encabezado .= "&krd=$krd&depeBuscada=$depeBuscada";
$encabezado .= "&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
$encabezado .= "&carpeta=" . $_GET['carpeta'] . "&tipo_carp=" . $_GET['tipo_carpt'] . "&chkCarpeta=$chkCarpeta";
$encabezado .= "&busqRadicados=$busqRadicados&nomcarpeta=$nomcarpeta&agendado=$agendado&";
$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";
$encabezado = "" . session_name() . "=" . session_id();
$encabezado .= "&adodb_next_page=1&krd=$krd&depeBuscada=$depeBuscada";
$encabezado .= "&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
$encabezado .= "&carpeta=" . $_GET['carpeta'] . "&tipo_carp=" . $_GET['tipo_carpt'] . "&nomcarpeta=$nomcarpeta";
$encabezado .= "&agendado=$agendado&orderTipo=$orderTipo&orderNo=";
?>
<form name="form_busq_rad" id="form_busq_rad" action='<?=$_SERVER['PHP_SELF']?>?<?=$encabezado?>' method="post">
	<table width="100%" align="center" cellspacing="0" cellpadding="0" class="borde_tab">
	<tr class="tablas">
		<td>
			<span class="etextomenu"> Buscar radicado(s) (Separados por	coma) 
				<span class="etextomenu"> 
					<input name="busqRadicados" type="text" size="40" class="tex_area" value="<?=$_REQUEST['busqRadicados']?>">
					<input type=submit value='Buscar ' name=Buscar valign='middle' class='botones'>
				</span>
				<input type="checkbox" name="chkCarpeta" value="1" <?=$chkValue?>> Todas las carpetas
			</span>
		</td>
	</tr>
	</table>
</form>
<form name="form1" id="form1" action="./tx/formEnvio.php?<?=$encabezado?>" method="POST">
	<input type="hidden" name="seleccionados" id="seleccionados" /> 
	<input type="hidden" name="noraiz" id="noraiz" /> 
	<input type="hidden" name="noidraiz" id="noidraiz" />
	<script type="text/javascript">
    <?php
    $obj->setPathFuncionalidad("_anexo/obtenerExtensionTiposAnexoJS");
    $obj->init();
    ?>
    </script>
<?php
    $controlAgenda = 1;
            
    if ($carpeta == 11 and ! $tipo_carp and $codusuario != 1) {} else {
        include "./tx/txOrfeo.php";
    }
    $obj->setPathFuncionalidad("carpeta");
    $obj->init();
?>
	</form>
</body>
</html>