<?php 
session_start();
if (count($_SESSION) == 0) {
	die(include "sinacceso.php");
	exit;
}
$radicado = htmlspecialchars($_GET['radi']);
$anexo = htmlspecialchars($_GET['anex']);
include("adodb/tohtml.inc.php");
require_once("include/db/ConnectionHandler.php");
if (!$db)
	$db = new ConnectionHandler('.');
if ($db) {
	//$db->conn->debug = true;
	$sql = "SELECT 	A.ANEX_FECH_ANEX AS FECHA,
					U.USUA_NOMB AS NOMBRE,
					'FECHA ANEXADO' AS DESCR
			FROM	ANEXOS A 
					INNER JOIN USUARIO U ON A.ANEX_CREADOR=U.USUA_LOGIN 
			WHERE	ANEX_RADI_NUME=$radicado 
					AND ANEX_CODIGO=$anexo
			UNION 
			SELECT	H.HIST_FECH AS FECHA,
					U1.USUA_NOMB AS NOMBRE,
					T.SGD_TTR_DESCRIP AS DESCR
			FROM	HIST_EVENTOS_ANEXOS H 
					INNER JOIN USUARIO U1 ON H.USUA_DOC = U1.USUA_DOC
					INNER JOIN SGD_TTR_TRANSACCION T ON H.SGD_TTR_CODIGO=T.SGD_TTR_CODIGO
			WHERE	ANEX_RADI_NUME = $radicado 
					AND ANEX_CODIGO = $anexo";
	$rs = $db->conn->Execute($sql);
}
?>

<HTML>
 <HEAD>
  <TITLE>Hist&oacute;rico de Anexos</TITLE>
  <link rel="stylesheet" href="estilos/orfeo.css">
 </HEAD>
 <BODY>
  <Form>
  <div style="height:380px;overflow-y:auto;overflow-x:hidden;">
  <Table border='1' cellpanding='2' cellspacing='0' class='borde_tab' valign='top' align='center' width='90%' scroll='yes'>
   <tr>
	<th class='titulos3'>Fecha Transacci&oacute;n </th>
	<th class='titulos3'>Funcionario </th>
	<th class='titulos3'>Transacci&oacute;n</th>
   </tr>
   
<?php
	$css = 1; 
	while ($arr = $rs->FetchRow()) { 
?>

   <tr class='listado<?$css?>' style="font:normal 11px Arial;">
    <td> <?php echo $arr[0]; ?> </td>
	<td> <?php echo $arr[1]; ?> </td>
	<td> <?php echo $arr[2]; ?> </td>
   </tr>

<?php
		$css = ($css==1) ? 2 : 1;
	}
?>

  </Table>
  <Table align="center">
   <tr>
	<td>
	 <input align="center" name="button" type="button" class="botones_largo" onClick="window.close()" value="CERRAR">
	</td>
   </tr>
  </Table>
 </Form>
 </div>
 </BODY>
</HTML>