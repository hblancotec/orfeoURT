<?php 
require_once('nusoap/lib/nusoap.php');

$wsdl="http://localhost/orfeoDNP/webServices/wsRadicado.php?wsdl"; 

$client=new soapclient2($wsdl, 'wsdl');  
//$extension = explode(".",$archivo_name);
//copy($archivo, "../bodega/tmp/visitas/".$archivo_name);

//$destinatarios = array();
//21094677
$destinatarios="21094677,52146987,79802120";

print_r($destinatarios);
$radiNume = '20096000017682';
$inf[0] = $radiNume;
$inf[1] = "52146987";  // Cedula de scasas
$inf[2] = $destinatarios;  //Cedula de ep
//$arregloDatos[1] = "21094677";  //Cedula de ep
$inf[3] = "Prueba Multiple Reasignacion en Linea.";
$a = $client->call( 'reasignarMRadicadoXDoc', $inf );
echo "<hr> Termnino Reasignar ---> " . $a ."<hr>";
print_r($a);
?>