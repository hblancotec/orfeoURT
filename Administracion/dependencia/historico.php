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
	if ($codigo && $_SESSION['dependencia'])
	{
		$isql = "SELECT SGD_ADMIN_DEPE_HISTORICO.*, SGD_ADMIN_DEPE_HISTORICO.DEPENDENCIA_CODIGO_ADMINISTRADOR AS DEPENDENCIA, SGD_ADMIN_OBSERVACION.DESCRIPCION_OBSERVACION FROM SGD_ADMIN_DEPE_HISTORICO INNER JOIN SGD_ADMIN_OBSERVACION ON SGD_ADMIN_DEPE_HISTORICO.ADMIN_OBSERVACION_CODIGO = SGD_ADMIN_OBSERVACION.CODIGO_OBSERVACION WHERE DEPENDENCIA_MODIFICADA = $codigo";
		$rs1 = $db->conn->Execute($isql);
		$isql = "SELECT * FROM DEPENDENCIA WHERE DEPE_CODI = $codigo";
		$rs2 = $db->conn->Execute($isql);
		$isql = "SELECT USUA_NOMB FROM USUARIO WHERE USUA_CODI = ". $rs1->fields["USUARIO_CODIGO_ADMINISTRADOR"]." AND DEPE_CODI = ".$rs1->fields["DEPENDENCIA"];
		$rs3 = $db->conn->Execute($isql);
		$isql = "SELECT DEPE_NOMB FROM DEPENDENCIA WHERE DEPE_CODI =".$rs1->fields["DEPENDENCIA"];
		$rs4 = $db->conn->Execute($isql);
		}
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="estilo.css">
<style type="text/css">
<!--
.Estilo1 {font-weight: bold}
-->
</style>
</head>
<body style="background-color:#FFFFFF">
<table width="100%" border="1" cellspacing="0" cellpadding="3" bordercolor="#CCCCCC" align="left">
  <tr>
    <td colspan="4"><div align="center"><strong>Administraci&oacute;n de Dependencias</strong></div></td>
  </tr>
  <tr>
    <td colspan="4"><div align="center"><strong>Consulta Hist&oacute;rica de Dependencia</strong></div></td>
  </tr>
  <tr>
    <td colspan="4"><strong>Datos Históricos</strong></td>
  </tr>
  <tr>
    <td width="11%" align="left"><span class="Estilo1">Dependencia</span></td>
    <td width="22%" align="left"><?=$rs2->fields["USUA_LOGIN"]?></td>
    <td width="32%" align="left"><strong>C&oacute;digo</strong></td>
    <td width="35%" align="left"><?=$rs2->fields["USUA_NOMB"]?></td>
  </tr>
  <tr>
    <td width="11%" align="left"><strong>Fecha</strong></td>
    <td width="22%" align="left"><strong>Administrador</strong></td>
    <td width="32%" align="left"><strong>Dependencia</strong></td>
    <td width="35%" align="left"><strong>Observación</strong></td>
  </tr>
<?php
while(!$rs1->EOF)
{
?>
  <tr>
    <td width="11%" align="left"><?=$rs1->fields["ADMIN_FECHA_EVENTO"]?></td>
    <td width="22%" align="left"><?=$rs3->fields["USUA_NOMB"]?></td>
    <td width="32%" align="left"><?=$rs4->fields["DEPE_NOMB"]?></td>
    <td width="35%" align="left"><?=$rs1->fields["DESCRIPCION_OBSERVACION"]?></td>
  </tr>

<?php
	$rs1->MoveNext();
	}
?>
</table>
</body>
</html>
