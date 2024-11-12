<?php

$cnt = 50;
$cnt = 1;
$ori = "D:\\PlantillaGeneral.docx";
$archivoSerializado = (fread(fopen($ori, "r"), filesize($ori)));

$datos[] = array("*RAD_S*" , "24680", "*MPIO_R*" , "Sabanalarga", "*DEPTO_R*", utf8_encode("Atlántico"), "*DEP_SIGLA*", "sIGLa1" );
$datos[] = array("*RAD_S*" , "13579", "*MPIO_R*" , "Cartagena"  , "*DEPTO_R*", utf8_encode("Bolívar"),  "*DEP_SIGLA*", "sIGLa XX");
	
for ($x = 0; $x < $cnt; $x++) {
	$fecha = date("Ymd_His");
	$des = "E:\\combinaPlantillaSerializada_" . $fecha . ".doc";
	try {
		
		$arregloDatos = array(	'archivoOrigen' => $archivoSerializado,
				'nombreArchivo' => "$fecha.doc",
				'variables' => $datos
		);

		$client = new SoapClient("http://officewspr.dnp.gov.co/officeWcfService/officeWcfService.svc?wsdl",
				array(
						'trace' => 1,
						'exceptions' => true,
						'cache_wsdl' => WSDL_CACHE_NONE,
						'soap_version' => SOAP_1_1
						)
		);
		$result = $client->combinaPlantillaSerializada( $arregloDatos );
		$ifp = fopen( $des, "wb" );
		
		fwrite( $ifp, $result->combinaPlantillaSerializadaResult );
		fclose( $ifp );
		echo "<h2>Result $x </h2><pre><a href='file://$des' target='_blank'>archivo convertido</a></pre>";
		
	} catch (Exception $e) {
		echo $client->__getLastResponse();
	}
}
?>