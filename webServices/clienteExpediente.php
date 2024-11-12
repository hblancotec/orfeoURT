<?php 
require_once('nusoap/lib/nusoap.php');
error_reporting(7);
$wsdl="http://localhost/orfeoDNP/webServices/wsExpediente.php?wsdl"; 

$client=new soapclient2($wsdl, 'wsdl');  
//$extension = explode(".",$archivo_name);
//copy($archivo, "../bodega/tmp/visitas/".$archivo_name);

$arregloDatos = array();
//21094677
$radiNume = '20099000027211';
//print_r($radiNume);
$arregloDatos[0] = $radiNume;
$arregloDatos[1] = 'SCASAS';
$arregloDatos[2] = '2009';
$arregloDatos[3] = '2009/10/20';
$arregloDatos[4] = 1;
$arregloDatos[5] = 1;
$arregloDatos[6] = 0;
//$arregloDatos[2] = "52146987";  // Cedula de scasas
//$arregloDatos[1] = "21094677";  //Cedula de ep
//$arregloDatos[3] = "Prueba Reasignacion en Linea.";
$a = $client->call( 'crearExpediente', $arregloDatos );
echo "<hr> Termnino Call Expediente --->" . $a ."<hr>";

var_dump( $a );

?>