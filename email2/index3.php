<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
} 

if ($_SESSION['usuaPermRadEmail'] != 1) {
    die(include "../sinpermiso.php");
    exit();
}

$tipo_carp = $tipo_carpp;
$encabezado = session_name() . "=" . session_id() . "&krd=$krd";
?>
<html>
<head>
<title>Email Entrante - OrfeoGPL.org</title>
</head>
<frameset rows="30%,70%" border="10" name="filas">
	<frame name="radicar" src="blanco.html" resize=true />
	<frameset cols="50%,*">
		<frame name="formulario" src="browse_mailbox.php?<?= $encabezado ?>" resize=true />
		<frame name="image" src="image.php?<?= $encabezado ?>" />
	</frameset>
</frameset>
</html>