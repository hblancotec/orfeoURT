<?php
$krd = $_GET['krd'];
//var_dump( $_GET );
//var_dump( $_POST );
//var_dump( $_SESSION );
$session = $_GET['PHPSESSID'];
?>
<html>
<head>
<title>Creacion grafica de flujos</title>
<meta http-equiv="Content-Type" content="text/html" charset="iso-8859-1">
<meta HTTP-EQUIV="expires" CONTENT="0">
</head>
<body>

<APPLET  
ARCHIVE="jgraph.jar"
CODE=co.gov.superservicios.orfeo.flujos.java.editorFlujos.class
width=800 height=1000>
<param 	name="usuario" value="<?=$krd?>" />
<param 	name="ses" value="<?=$session?>" />
</APPLET>
</body>
</html>
