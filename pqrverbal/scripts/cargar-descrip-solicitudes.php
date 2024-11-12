<?php
include("clases/class.sqlsrv.php");
include("clases/class.combos.php");
$solicitudes = new selects();
$solicitudes->code = $_GET["code"];
$datos = $solicitudes->cargarDescripSolicitudes();
$i=0;
foreach($datos as $key=>$value) {
	echo "<b>$key</b><br/>$value.<br />";
}
?>