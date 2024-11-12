<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../sinpermiso.php");
	exit;
}

	$ruta_raiz = "..";
	if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad']) include "$ruta_raiz/rec_session.php";	
	$phpsession = session_name()."=".session_id();
?>
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<table width="40%"  border="1" align="center">
  <tr>
    <td colspan="2"><div align="center"><strong>Modulo de Administraci&oacute;n</strong></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="6%">1.</td>
    <td width="94%"><a href='usuario/listado.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "fechah=$fechah"; ?>' target='mainFrame'>Usuarios y Perfiles</a></td>
  </tr>
  <tr>
    <td>2.</td>
    <td width="94%"><a href='dependencia/listado.php?>&krd=<?=$krd?>&<?php echo "fechah=$fechah"; ?>' target='mainFrame'>Dependencias</a></td>
  </tr>
  <tr>
    <td>3.</td>
    <td>Carpetas</td>
  </tr>
  <tr>
    <td>4.</td>
    <td>Env&iacute;os de Correspondencia </td>
  </tr>
  <tr>
    <td>5.</td>
    <td>Tipos Documentales </td>
  </tr>
  <tr>
    <td>6.</td>
    <td>Servicios</td>
  </tr>
  <tr>
    <td>7.</td>
    <td>Tipos de Radicaci&oacute;n </td>
  </tr>
</table>
</body>
</html>
