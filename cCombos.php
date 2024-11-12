<?php

$_continente = $_GET['continente'];
$_pais = $_GET['pais'];
$_departamento = $_GET['departamento'];
$_municipio = $_GET['municipio'];
$_tipo = $_GET['tipo'];
$ruta_fuente = $_GET['ruta_fuente'] ? $_GET['ruta_fuente'] : ".";
$dep= $_GET['dep'];
$usu= $_GET['usu'];
include("./include/class/Combos.Class.php");
$obj = new Combos(".");

switch ($_tipo) {
    case "pais":
        $a = $obj->getPaises($_continente, $_val);
        break;
    case "depto":
        $a = $obj->getDepartamentos($_continente, $_pais, $_val);
        break;
    case "mnpio":
        $a = $obj->getMunicipios($_continente, $_pais, $_departamento, $_val);
        break;
    case "todos":
        $a.=$obj->getPaises($_continente, $_pais) . "@1";
        $a.=$obj->getDepartamentos($_continente, $_pais, $_departamento) . "@2";
        $a.=$obj->getMunicipios($_continente, $_pais, $_departamento, $municipio);
        break;
    case "usuarios":
        $a= $obj->getUsuariosDep($dep,$usu);
        break;
}
echo $a;
?>