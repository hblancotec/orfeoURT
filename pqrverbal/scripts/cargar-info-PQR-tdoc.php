<?php
include("clases/class.sqlsrv.php");
include("clases/class.combos.php");
$infoPqrTdoc = new selects();
$infoPqrTdoc->code = $_GET["idTdoc"];
$datos = $infoPqrTdoc->cargarDatosPqrTdoc();
echo $datos;
?>