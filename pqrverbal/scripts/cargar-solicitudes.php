<?php
include("clases/class.sqlsrv.php");
include("clases/class.combos.php");
$solicitudes = new selects();
$solicitudes->code = $_GET["code"];
$datos = $solicitudes->cargarSolicitudes();
$i=0;
foreach($datos as $key=>$value)
{	if ($i==0){
		echo "<option value=\"\">Seleccione Solicitud</option>";
		$i++;
	}
	echo "<option value=\"$key\">$value</option>";
}
?>