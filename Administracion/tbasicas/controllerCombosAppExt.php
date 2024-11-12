<?php


$_tipo = $_GET['tipo'];
$app=$_GET['app'];
$id=$_GET['id'];
$ruta_fuente = $_GET['ruta_fuente'] ? $_GET['ruta_fuente'] : ".";
include("$ruta_fuente/config.php");
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php';
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $servicio;
$conn = NewADOConnection($dsn);
include($ruta_fuente.'/include/class/enlaceAplicativos.class.php');
include($ruta_fuente.'/include/class/tipoRadicado.class.php');
include_once("$ruta_fuente/include/db/ConnectionHandler.php");
$obj_tmp = new enlaceAplicativos($conn); 
$conn1 = new ConnectionHandler("$ruta_fuente");
$obj_trad= new TipRads($conn1);
$a="No trae nada! x q' no ingresa a ninguna opcion";
switch ($_tipo) {
    case "camposapp":
       $a = $obj_tmp->getComboCamposExt(true,false,$app,"cmbCampExt[$id]");
        break;
    case "tipoRad":
        $a = $obj_trad->getComboTipoRad(true, "","cmbTiposRad[$id]");
        break;
    case "campoOrfeo":
        $a = $obj_tmp->getCamposOrfeo("cmbCamposOrfeo[$id]", "0:&lt;&lt; Seleccione &lt;&lt;",false, "onchange='activa($id)'");
        break;
    case "accionesExt":
        $a = $obj_tmp->getComboAccionesExt(true, false,$app,"accionExt[$id]");  
        break;
     case "metodosOrfeo":
        $a = $obj_tmp->getComboMetodos(true, false," metodoOrfeo[$id]",false,$app);
        break;  
         
}
echo $a;
?>