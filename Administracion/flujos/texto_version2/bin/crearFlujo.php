<?php
	$krd = $_GET['krd'];
	$session = $_GET['PHPSESSID'];
?>
<html>
<head>
<title>Creacion grafica de flujos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta <META HTTP-EQUIV="expires" CONTENT="0">
</head>
<body>

<APPLET  
ARCHIVE="jgraph.jar"
CODE=co.gov.superservicios.orfeo.flujos.java.editorFlujos.class
width=800 height=600>
<param 	name="usuario" value="<?=$_GET['krd']?>"/>
<param 	name="ses" value="<?=$_GET['PHPSESSID']?>"/>
</APPLET>
<form name="usuario" method="POST">
<input type="hidden" name="krd" value="<?=$_GET['krd']?>">
</form>
</body>
</html>
