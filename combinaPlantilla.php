<?php
ini_set('default_socket_timeout', 10);
require 'config.php';
$ori = BODEGAPATH.'PlantillaGeneral.docx';
$des = BODEGAPATH.'PlantillaGeneral'.date("YmdHis").'.docx';
	
$datos[] = array("*RAD_S*" , "24680", "*MPIO_R*" , "Sabanalarga", "*DEPTO_R*", "Atlantico", "*DEP_NOMB*", "OFICINA DE INFORMATICA" );
$datos[] = array("*RAD_S*" , "13579", "*MPIO_R*" , "Cartagena"  , "*DEPTO_R*", "Bolivar", "*DEP_NOMB*", "OFICINA DE INFORMATICA" );
	
$arregloDatos = array(	'rutaOrigen' => $ori,
			'rutaDestino' => $des,
			'variables' => $datos
		   );

try {
	$client = new SoapClient($wsdlOffice,
			array(
					'trace' => 1,
					'exceptions' => true,
					'cache_wsdl' => WSDL_CACHE_NONE,
					'soap_version' => SOAP_1_1
					)
	);
	$result = $client->combinaPlantilla( $arregloDatos );
	echo $result->combinaPlantillaResult;
} catch (Exception $e) {
	echo "Excp. => ".var_dump($e);
}
?>