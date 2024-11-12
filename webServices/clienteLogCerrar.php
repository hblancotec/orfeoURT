<?php session_start();
?>
<HTML>
<BODY>

Inicio de Cliente WebService 
<hr>
<?php

//if $krd = $_SESSION["krd"];
//$dependencia = $_SESSION["dependencia"];
//$usua_doc = $_SESSION["usua_doc"];
// $codusuario = $_SESSION["codusuario"];

error_reporting(7);
require_once('../webServices/nusoap/lib/nusoap.php');

$wsdl="http://panche/webServices/wsRadicado.php?wsdl";
//$wsdl="http://volimpo/webServices/wsRadicado.php?wsdl"; 

echo "wsdl --->". $wsdl."<hr>";

$client=new soapclient2($wsdl, 'wsdl');  


$arregloDatos = array();
$arregloDatos[0] = "0";
$arregloDatos[1] = "2010-08-01"; // aRREGLO con datos de destinatario 1
$arregloDatos[2] = "2011-12-31";// aRREGLO con datos de Copia 1


?>
<hr>  Inicio de Cliente WebService  222 </hr>
<?php 

print_r($arregloDatos);

echo "<br>";
error_reporting(7);

$a = $client->call('logRadicadosCerrados',$arregloDatos);
echo '<h1>Error: ' . $client->getError() . '</h1>';
//print_r($client);
echo "<br>Resultado del Cliente --->  ".$a."<---- ??<hr>";


if($a) 
{
	echo "<hr> Entro >$a< <hr>";
}else{
	 echo "<font color=red><hr> Entro >$a< <hr></font>"; 
}

echo "<hr>Imresion de arreglo<hr>";
print_r($a);
?>
</BODY>
</HTML>

