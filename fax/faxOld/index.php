<?php
   error_reporting(0);
   session_start();
   //$dependencia = "";

   if(!$_SESSION['dependencia'] or !$_SESSION['krd']) include "rec_session.php";
   $carpeta=$carpetano;
   $tipo_carp = $tipo_carpp;
?>
<html>
<head>
	<title>Fax</title>
</head>
<frameset rows="40%,60%" border="0" name="filas">
<frame name="image" src="image.php" name="columnas">
	<frameset cols="150,947" name="secundario">
		<frame name="lista" src="lista.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>&fechah=<?=$fechah?>" parent="secundario">
		<frame name="formulario" src="form.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>&fechah=<?=$fechah?>" parent="secundario">
	</frameset>
<frame src="UntitledFrame-3"></frameset><noframes></noframes>
</html>