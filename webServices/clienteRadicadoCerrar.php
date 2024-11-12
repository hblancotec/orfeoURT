<?php
session_start();

//require_once('../webServices/2nusoap/lib/nusoap.php');
require_once('nusoap/lib/nusoap.php');

//$wsdl="http://p34036/WSolicitud_dev_2010/WsIntegraORFEO.asmx?wsdl";
//$wsdl="http://vsuifp:89/WSolicitud/wsintegraorfeo.asmx?wsdl";
$wsdl="http://wssuifp.dnp.gov.co:89/WSolicitud/WsIntegraORFEO.asmx?wsdl";		//PRODUCCION
//$wsdl="http://wssuifppruebas/WSolicitud/WsIntegraORFEO.asmx?wsdl";				//PRUEBAS

$client=new nusoap_client($wsdl,true);  

$arregloDatos['numeroRadicado'] = "$noRadicadoImagen";
$arregloDatos['login'] = "ORFEO";
$arregloDatos['clave'] = "G311113";

$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
//$a = $client->call('CerrarRadicado',array('parameters' => $arregloDatos),  '', '', true, true);
$a = $client->call('CerrarRadicadoValidacion',array('parameters' => $arregloDatos),  '', '', true, true);
?>
