<?php
session_start();
$ruta_raiz = ".";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
require_once $ruta_raiz . "/_conf/constantes.php";

$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;

if (! $_SESSION['dependencia'])
    include ORFEOPATH . "rec_session.php";
$verrad = "";
include_once ORFEOPATH . "include/db/ConnectionHandler.php";
$db = new ConnectionHandler(ORFEOPATH);
?>
<html>
<head>
<title>Eliminar Carpetas</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link rel="stylesheet" href="estilos/orfeo.css">
</head>
<body>
	<center>
		<table width="98%" border="0" cellpadding="0" cellspacing="5"
			class="borde_tab">
			<tr>
				<TD width='97%' class="titulos4" align="center">ELIMINAR CARPETAS PERSONALES</TD>			
			</tr>
		</table>
		<BR>
		<form name='form1' method='post' action='eliminar_carpeta.php?=<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>
<?php
if ($BorrarCarp) {
    $isql = "select count(*) Num from radicado
			 where carp_per=1 and carp_codi=$borrcarpeta and radi_depe_actu=$dependencia and radi_usua_actu=$codusuario ";
    $rs = $db->conn->Execute($isql);
    $numerot = $rs->fields[0];
    if ($rs == - 1)
        die("<center>No se han encontrado carpetas vacias <br> ");
    if ($numerot == 0) {
        $isql = "delete from  carpeta_per where depe_codi=$dependencia and usua_codi=$codusuario and codi_carp=$borrcarpeta ";
        $rs = $db->conn->Execute($isql);
        if ($rs == - 1)
            die("<p align='center' class='alarmas'><center>No se ha podido Borrar la carpeta,<br>");
        echo "<span align='center' class='info'>Se ha borrado la Carpeta  Con exito</span>";
    } else {
        echo "<br><span class='alarmas'>La carpeta no se ha podido borrar por que contiene $numerot documentos,<br> La carpeta debe estar vacia para poder ser borrada </span>";
    }
}
?>
<table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
<tr>
	<TD width='97%' class="listado2_center">
	<p align='center' class='etextomenu'>Solo se pueden eliminar las carpertas que se encuentren vacias</p>
	<p align='center' class='etextomenu'>Usted tiene estas carpetas:</p>
	</TD>
</tr>
</table>
<?php
$descripcionSql = $db->conn->concat('NOMB_CARP', "' - '", 'DESC_CARP');
$isql = "select $descripcionSql , CODI_CARP
			from carpeta_per where usua_codi=$codusuario and depe_codi=$dependencia
			order by codi_carp  ";
$rs = $db->conn->Execute($isql);
print $rs->GetMenu2("borrcarpeta", "$borrcarpeta", "0:-- Seleccione la carpeta Personal--", false, "", "onChange='procEst(formulario,18,$i )' class='select'");
$row = array();
?>
	<br>
			<br>
			<input type=submit name='BorrarCarp' Value='Borrar Carpeta'
				class='botones'>
			<p>&nbsp;</p>
		</form>
</body>
</html>