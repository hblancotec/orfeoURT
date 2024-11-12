<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}

if (!$_SESSION['dependencia'] || !$_SESSION['usua_doc'] )
    include "../rec_session.php";
    
if (!$ruta_raiz)
    $ruta_raiz="..";

require $ruta_raiz.'/config.php';
//Se crea la conexion con la base de datos
require_once $ruta_raiz."/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$sql = "select h.fecha_evento, u.usua_nomb as usuario, h.detalle as observacion ".
        "from sgd_hist_ciclofirmado h ". 
        "inner join usuario u on u.usua_doc=h.usua_doc and u.usua_login=h.usua_login ".
        "where h.idcf=".$_REQUEST['idcfirmante']." ".
        "order by h.fecha_evento";
$rs = $db->conn->Execute($sql);
?>
<html>
<head>
	<title>Registro de Solicitud de Firma</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body>
<Form>
	<Table border='1' cellpanding='2' cellspacing='0' class='borde_tab' valign='top' align='center' width='100%' scroll='yes'>
    <tr>
		<th class='titulos3'>Fecha Transacci&oacute;n </th>
		<th class='titulos3'>Funcionario </th>
		<th class='titulos3'>Observaci&oacute;n</th>
	</tr>
<?php
$css = 1;
while ($arr = $rs->FetchRow()) {
?>
	<tr class='listado<?php $css ?>' style="font:normal 11px Arial;">
		<td> <?php echo $arr['fecha_evento']; ?> </td>
		<td> <?php echo $arr['usuario']; ?> </td>
		<td> <?php echo $arr['observacion']; ?> </td>
    </tr>
<?php
    $css = ($css == 1) ? 2 : 1;
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
</body>
</html>