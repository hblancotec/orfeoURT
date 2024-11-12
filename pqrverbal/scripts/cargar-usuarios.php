<?php
include("clases/class.sqlsrv.php");
include("clases/class.combos.php");
$usuarios = new selects();
$usuarios->code = $_GET["code"];
$datos = $usuarios->cargarUsuarios();
$i=0;
foreach($datos as $key=>$value)
{	if ($i==0){
		echo "<option value=\"\">Seleccione Usuario</option>";
		$i++;
	}
	echo "<option value=\"$key\">$value</option>";
}
?>