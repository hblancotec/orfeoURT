<?php 
require_once('nusoap/lib/nusoap.php');

$wsdl="http://localhost/orfeo/webServices/servidor.php?wsdl"; 

$client=new soapclient2($wsdl, 'wsdl');  
$arregloDatos = array();

$filename = '799822761_2007_08_27_14_30_14.doc';
$strFile =  file_get_contents ( $filename );
$strFileEncoded64 = base64_encode($strFile);
//var_dump($strFileEncoded64);
$radiNume = '20099000033061';
$correo = 'jlosada@gmail.com';
$descripcion = 'Prueba de Webservice Creación anexo.';
$arregloDatos[0] = $radiNume;
$arregloDatos[1] = "doc";
$arregloDatos[2] = $strFileEncoded64;
$a = $client->call( 'uploadImagenRadicado', $arregloDatos );
echo "<hr>". $a ."<hr>" ;

?>