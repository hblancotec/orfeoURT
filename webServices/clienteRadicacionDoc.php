<?php
session_start();
?>
<!-- OrfeoGPL es un Software con Licencia GNU/GPL.  
	Admnistrado por la Fundacion Correlibere. 
    Pagina Web, http://www.orfeogpl.org - http://www.correlibre.org
    2009
    -->
<HTML>
<BODY>

Inicio de Cliente WebService 
<hr>
<?php
/**
  * Envio de Documentos entre Entidades TeleOrfeo
  * @auto Orlando Burgos  SuperServicios
  * @fecha 200808 
  */

//if $krd = $_SESSION["krd"];
//$dependencia = $_SESSION["dependencia"];
//$usua_doc = $_SESSION["usua_doc"];
// $codusuario = $_SESSION["codusuario"];

error_reporting(7);
require_once('../webServices/nusoap/lib/nusoap.php');

//$wsdl="http://panche/webServices/wsRadicado.php?wsdl";
$wsdl="http://volimpo/webServices/wsRadicado.php?wsdl"; 

echo "wsdl --->". $wsdl."<hr>";

$client=new soapclient2($wsdl, 'wsdl');  

$destinatario1 = array(
	'documento'=>'1579',                // Nuemro de Doc
	'cc_documento'=>"1",                    // tipo de Documento.
	'tipo_emp'=>'3',     // Si es Ciudadano 1, OEM 2, Entidad 3 
	'nombre'=>'xxxXXJAIRO',
	'prim_apel'=>'LOSADA',
	'seg_apel'=>'CARDONA',
	'telefono'=>'3005711233',
	'direccion'=>'DG 15 B NO 104 46 CS 242',
	'mail'=>'jlosada@gmail.com',
	'otro'=>'',
	'idcont'=>'1',
	'idpais'=>'170',
	'codep'=>'11',
	'muni'=>'1'
	);

$arregloDatos = array();
$arregloDatos[0] = "jlosada@dnp.gov.co";
$arregloDatos[1] = $destinatario1;
$arregloDatos[2] = $destinatario2;
$arregloDatos[3] = $destinatario3;
$arregloDatos[4] = "Prueba Web Service DNP -jh";
$arregloDatos[5] = "1";
$arregloDatos[6] = "1";
$arregloDatos[7] = "900"; // Codigo Dependencia Radicadora
$arregloDatos[8] = "1";  
$arregloDatos[9] = "2";  // Tipo de Radicado 2 Entrada, 3 Salida, ...
$arregloDatos[10] = "52146987";  // Codigo Usuario Actual o al que ira el Radicado
$arregloDatos[11] = "1";  
$arregloDatos[12] = "1";  // Tipo de remitente
$arregloDatos[13] = "1";  // Tipo de Documento
$arregloDatos[14] = "0";  // Tipo de remitente
$arregloDatos[15] = "0";  // Carpeta Codigo "0" Bandeja de Entrada
$arregloDatos[16] = "52146987"; // Usuario radicador

 
?>
<hr>  Inicio de Cliente WebService  222 </hr>
<?php 

print_r($arregloDatos);

error_reporting(7);
$a = $client->call('radicarXUsuaDoc',$arregloDatos);
echo "Resultado del Cliente <hr>  ".$a."---- ??<hr>";

print_r($a);

if($a) 
{
	echo "<hr> Entro $a <hr>";
}else{
	 echo "<font color=red><hr> Entro $a <hr></font>"; 
}
?>
</BODY>
</HTML>

