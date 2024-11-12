<?php 
require_once('nusoap/lib/nusoap.php');

$wsdl="http://localhost/orfeo/webServices/servidor.php?wsdl"; 

$client=new soapclient2($wsdl, 'wsdl');  
//$extension = explode(".",$archivo_name);
//copy($archivo, "../bodega/tmp/visitas/".$archivo_name);

$arregloDatos = array();
//21094677
$radiNume = '20099000026571';
$arregloDatos[0] = $radiNume;
$arregloDatos[2] = "52146987";  // Cedula de scasas
$arregloDatos[1] = "21094677";  //Cedula de ep
$arregloDatos[3] = "Prueba Reasignacion en Linea.";
$a = $client->call( 'reasignarRadicadoXDoc', $arregloDatos );
echo "<hr> Termnino Reasignar ---> " . $a ."<hr>";

var_dump( $a );

?>