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

if ($_SESSION['usua_admin_archivo'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

	$ruta_raiz = "../..";

?>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<link rel="stylesheet" href="../../estilos/orfeo.css">
		</head>
		<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
			<table width="30%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
				<tr>
					<td height="25" class="titulos4" align="center">
						ADMINISTRACI&Oacute;N ESTRUCTURA F&Iacute;SICA DE ARCHIVO
					</td>
				</tr>
				<tr align="center">
					<td class="listado2"> 
						<a href='./adm_edificios.php' class="vinculos" target='mainFrame'> 1. Edificios </a>
					</td>
				</tr>
				<tr align="center">
					<td class="listado2">
						<a href='./adm_seccion.php' class="vinculos" target='mainFrame'> 2. Pisos / Secciones </a>
					</td>
				</tr>
				<tr align="center">
					<td class="listado2">
						<a href='./adm_estantes.php' class="vinculos" target='mainFrame'> 3. Estantes </a>
					</td>
				</tr>
				<tr>
					<td align="center">
						<input class='botones' type='button' name='Atras' value='Men&uacute; Principal' onclick='atras();'>
					</td>
				</tr>
			</table>
			<script language="javascript">
				function atras() 
				{
					window.location.href = "../index.php";
				}
			</script>
		</body>
	</html>
