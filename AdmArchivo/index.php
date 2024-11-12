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

if ($_SESSION['usua_admin_archivo'] != 1){
	die(include "../sinpermiso.php");
	exit;
}

	if (!(isset($krd))){
		$login = $_REQUEST['login'];
	}
	else {
		$login = $krd;
	}
	
	include_once ("../include/db/ConnectionHandler.php");
	$db = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	$isql2 = "	SELECT	COUNT(*) AS CONTADOR
				FROM	SGD_EXP_EXPEDIENTE
				WHERE	SGD_EXP_ESTADO = 0 AND
						( SGD_EXP_UFISICA = 2 OR SGD_EXP_UFISICA IS NULL) AND
						RADI_NUME_RADI > 20130000000000";
	$rs2 = $db->conn->Execute($isql2);
	$num_exp2 = $rs2->fields['CONTADOR'];
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../estilos/orfeo.css">
	</head>
	<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
		<table width="25%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
			<tr>
				<td height="25" class="titulos4" align="center">
				  ADMINISTRACI&Oacute;N  DE ARCHIVO
				</td>
			</tr>
			<tr align="center">
				<td class="listado2"> 
					<a href='Estructura/menu_estructura.php' class="vinculos" target='mainFrame'> 
						1. Estructura F&iacute;sica 
					</a>
				</td>
			</tr>
			<tr align="center">
				<td class="listado2">
					<a href='Archivar/consultar.php?login=<?= $login ?>' class="vinculos" target='mainFrame'> 
						2. Radicados para Archivar  ( <?php echo $num_exp2; ?>)
					</a>
				</td>
			</tr>
		</table>
	</body>
</html>