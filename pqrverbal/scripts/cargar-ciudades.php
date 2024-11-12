<?php
ini_set('display_errors', 1);
include("clases/class.sqlsrv.php");
include("clases/class.combos.php");
$ciudades = new selects();
$ciudades->code = $_POST["code"];
$datos = $ciudades->cargarCiudades();
$i=0;
foreach($datos as $key=>$value)
{	if ($i==0){
		echo "<option value=\"\">Seleccione Ciudad</option>";
		$i++;
	}
	echo "<option value=\"$key\">$value</option>";
}
?>