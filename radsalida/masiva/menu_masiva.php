<?php
session_start();
$ruta_raiz = "../..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

if ($_SESSION['usua_masiva'] != 1){
    die(include $ruta_raiz."/sinpermiso.php");
	exit;
}
error_reporting(7);
require_once "$ruta_raiz/include/db/ConnectionHandler.php";
//Si no llega la dependencia recupera la sesion
if(!isset($_SESSION['dependencia']))
{	include "$ruta_raiz/rec_session.php";	}
if (!$db)	$db = new ConnectionHandler($ruta_raiz);
$phpsession = session_name()."=".session_id(); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../estilos/orfeo.css">
<script language="JavaScript">
<!--
function Start(URL, WIDTH, HEIGHT)
{
 windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=";
 windowprops += WIDTH + ",height=" + HEIGHT;

 preview = window.open(URL , "preview", windowprops);
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
<table width="47%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
<tr>
	<td height="25" class="titulos4">RADICACI&Oacute;N MASIVA DE DOCUMENTOS</td>
</tr>
<tr align="center">
	<td class="listado2" >
		<a href="../cuerpo_masiva_recuperar_listado.php?<?=$phpsession ?>&krd=<?=$krd?>" class="vinculos">
		Recuperar Listado
		</a>
	</td>
</tr>
<tr align="center">
	<td class="listado2" >
		<a href='consulta_depmuni.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "fechah=$fechah"; ?>' class="vinculos" target='mainFrame'>
		Consultar Divisi&oacute;n Pol&iacute;tica Administrativa de Colombia (DIVIPOLA)</a>
	</td>
</tr>
<tr align="center">
	<td class="listado2" >
		<a href='consultaESP.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "fechah=$fechah"; ?>' target='mainFrame' class="vinculos">
		Consulta y Selecci&oacute;n destinatarios masiva
		</a>
	</td>
</tr>
</table>
<?php
	include "administradorSecuencias.php";
?>
</body>
</html>