<?php
session_start();
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);
$tipo_carp = $tipo_carpp;
$encabezado = session_name()."=".session_id()."&krd=$krd";
//echo $encabezado;
?>
<html>
<head>
<title>Email Entrante - OrfeoGPL.org</title>
</head>
<frameset rows="30%,70%" border="10" name="filas">
<frame name="image" src="./image.php?<?=$encabezado?>" name="columnas">
<frame name="formulario" src="login_email.php?<?=$encabezado?>" parent="secundario" resize=true>
</html>
