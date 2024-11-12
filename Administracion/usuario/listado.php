<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else {
	$krd = $_SESSION["login"];
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
$isql = "select USUARIO.USUA_CODI,USUARIO.USUA_NOMB, USUARIO.USUA_LOGIN, DEPENDENCIA.DEPE_NOMB, DEPENDENCIA.DEPE_CODI from USUARIO INNER JOIN dependencia on dependencia.depe_codi = usuario.depe_codi ";
if ($nombre)
	$isql = "select USUARIO.USUA_CODI,USUARIO.USUA_NOMB, USUARIO.USUA_LOGIN, DEPENDENCIA.DEPE_NOMB, DEPENDENCIA.DEPE_CODI from USUARIO INNER JOIN dependencia on dependencia.depe_codi = usuario.depe_codi order by USUA_NOMB ASC";
else if ($usuarios)
	$isql = "select USUARIO.USUA_CODI,USUARIO.USUA_NOMB, USUARIO.USUA_LOGIN, DEPENDENCIA.DEPE_NOMB, DEPENDENCIA.DEPE_CODI from USUARIO INNER JOIN dependencia on dependencia.depe_codi = usuario.depe_codi order by USUA_LOGIN ASC";
else if ($dependencia)
	$isql = "select USUARIO.USUA_CODI,USUARIO.USUA_NOMB, USUARIO.USUA_LOGIN, DEPENDENCIA.DEPE_NOMB, DEPENDENCIA.DEPE_CODI from USUARIO INNER JOIN dependencia on dependencia.depe_codi = usuario.depe_codi order by DEPE_NOMB ASC";
	
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
<table width="100%" border="1" cellspacing="0" cellpadding="4" bordercolor="#CCCCCC" align="center">
  <tr>
     <td colspan="6" align="center" class="Estilo3">
      Administración de Usuarios
    </td>
  </tr>
  <tr>
     <td colspan="6" align="center" class="Estilo2"><strong>
      Usuarios
    </strong></td>
  </tr>
  <tr>
    <td width="23%" class="vbmenu_control" align="center">
      <strong><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href='listado.php?nombre=1&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>Nombre</a></font> </strong>
    </td>
    <td width="10%" class="vbmenu_control" align="center">
		<span class="Estilo1"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href='listado.php?usuarios=1&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>Usuario</a>        </font> </span>
    </td>
    <td width="19%" class="vbmenu_control" align="center">
      	<span class="Estilo1"><font face="Verdana" size="2"><a href='listado.php?dependencia=1&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>Dependencia
        </a> </font> </span>
    </td>
    <td width="10%" colspan="2" class="tfoot" align="center">
    <font face="Verdana" size="2" color="FFFFFF"><b><a href='admin_usu_perfiles.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>Registrar usuario</a></b></font></td>
  </tr>
<?php
while(!$rs->EOF)
{
?>
	<tr>
		<td width="23%" class="alt1"><?=$rs->fields["USUA_NOMB"]?></td>
		<td width="10%" class="alt1"><?=$rs->fields["USUA_LOGIN"]?></td>
		<td width="19%" class="alt1"><?=$rs->fields["DEPE_NOMB"]?></td>		
		<td width="4%"> <font face="Verdana" size="2" color="#000000"><a href='perfiles.php?codigo=<?=$rs->fields["USUA_CODI"]?>&dependencia=<?=$rs->fields["DEPE_CODI"]?>&editar=1&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'><B>Editar</B></a></font>		
		<td width="5%"> <font face="Verdana" size="2" color="#000000"><a href='historico.php?codigo=<?=$rs->fields["USUA_CODI"]?>&dependencia1=<?=$rs->fields["DEPE_CODI"]?>&<?=session_name()."=".session_id()?>&krd=<?=$krd?>'><B>Histórico</B></a></font>				
	</tr>
<?php
$rs->MoveNext();
}
?>
</table>
</body>
</html>
