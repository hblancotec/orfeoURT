<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

	$ruta_raiz = "../..";
	if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad']) include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";

$db = new ConnectionHandler("$ruta_raiz");	
//$db->conn->debug = true;
error_reporting(0);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$isql = "select DEPENDENCIA.DEPE_CODI,DEPENDENCIA.DEPE_NOMB FROM DEPENDENCIA";
if ($codigo)
	$isql = "select DEPENDENCIA.DEPE_CODI,DEPENDENCIA.DEPE_NOMB FROM DEPENDENCIA order by DEPE_CODI ASC";
else if ($nombre)
	$isql = "select DEPENDENCIA.DEPE_CODI,DEPENDENCIA.DEPE_NOMB FROM DEPENDENCIA order by DEPE_NOMB ASC";

$rs = $db->conn->Execute($isql);		
?>	      	   	
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.Estilo1 {font-weight: bold}
.Estilo2 {font-family: Verdana, Arial, Helvetica, sans-serif}
.Estilo3 {font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; }
-->
</style>
</head>
<body>
<table width="75%" border="1" cellspacing="0" cellpadding="4" bordercolor="#CCCCCC" align="left">
  <tr>
     <td colspan="6" align="center" class="Estilo3">
      Administración de Dependencias
    </td>
  </tr>
  <tr>
     <td colspan="6" align="center" class="Estilo2"><strong>
      Dependencias</strong></td>
  </tr>
  <tr>
    <td width="5%" class="vbmenu_control" align="center">
      <strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href='listado.php?codigo=1&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>Codigo</a></font> </strong>
    </td>
    <td width="60%" class="vbmenu_control" align="center">
		<span class="Estilo1"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href='listado.php?nombre=1&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>Nombre Dependencia</a></font></span>
    </td>
    <td width="10%" colspan="2" class="tfoot" align="center">
    <font face="Verdana" size="2" color="FFFFFF"><b><a href='perfiles.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>Registrar Dependencia</a></b></font></td>
  </tr>
<?php
while(!$rs->EOF)
{
?>
	<tr>
		<td width="5%" class="alt1"><?=$rs->fields["DEPE_CODI"]?></td>
		<td width="60%" class="alt1"><?=$rs->fields["DEPE_NOMB"]?></td>
		<td width="5%"> <font face="Verdana" size="2" color="#000000"><a href='perfiles.php?codigo=<?=$rs->fields["DEPE_CODI"]?>&editar=1&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'><B>Editar</B></a></font>		
		<td width="5%"> <font face="Verdana" size="2" color="#000000"><a href='historico.php?codigo=<?=$rs->fields["DEPE_CODI"]?>&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'><B>Histórico</B></a></font>				
	</tr>
<?php
$rs->MoveNext();
}
?>
</table>
</body>
</html>
