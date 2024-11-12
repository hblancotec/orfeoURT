<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "./sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

$ruta_raiz = ".";
require_once($ruta_raiz . "/" . "_conf/constantes.php");
require_once(ORFEOPATH . "include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;">
		<link rel="stylesheet" href="estilos/orfeo.css">
	</head>
	<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
		<br>
		<form method="post" action="<?= $sActionFileName ?>?<?=session_name()."=".session_id()?>&krd=<?=$krd?>">
			<table width="25%" border="0" cellpadding="0" cellspacing="5" align="center" class='borde_tab'>
				<tr> 
					<td class="titulos4"><center>CONSULTA DE EXPEDIENTES</center></td> 
				</tr>
				<tr>
					<td class="listado2">
						<A href="consultaExpCont.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?>"> CONTRATOS</A>
					</td>
				</tr>
				
				<?php	
					## Se valida si el usuario logeado no tiene ROL Auditor (3)
					$query = "	SELECT	SGD_ROL_CODIGO
								FROM	USUARIO
								WHERE	USUA_LOGIN = '$krd'";
					$rol = $db->conn->Getone($query);
					if ($rol != 3){
				?>
				
				<tr>
					<td class="listado2">
						<A href="consultaExpOcad.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?>"> SGR OCAD</A>
					</td>
				</tr>
				
				<?php
					}
				?>
				
				<tr>
					<td class="listado2">
						<A href="consultaExp.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?>"> EXPEDIENTES</A>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>