<?php 
require_once('nusoap/lib/nusoap.php');

$wsdl="http://localhost/orfeo/webServices/servidor.php?wsdl"; 

$client=new soapclient2($wsdl, 'wsdl');  
//$extension = explode(".",$archivo_name);
//copy($archivo, "../bodega/tmp/visitas/".$archivo_name);

$arregloDatos = array();

//$a = $client->call('darUsuario',$arregloDatos);
/*$arregloDatos[0] = 'jgonzal@superservicios.gov.co';
$correo = 'jgonzal@superservicios.gov.co';

print_r($client->call( 'getUsuarioCorreo', $correo ));
$a = $client->call( 'getUsuarioCorreo', $correo );
*/

$filename = '799822761_2007_08_27_14_30_14.doc';
//$filename = '799822761_2007_08_10_18_04_05.odt';
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
echo "<hr> Termnino Call uploadImagenRadicado --->" . $a ."<hr>";
// Display the result
//print_r($a);



var_dump( $a );

// Display the request and response
/*echo '<h2>Request:</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response:</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
*/
//var_dump( $a );
//die($a);

?>