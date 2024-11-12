<?php

$t_current_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
$t_nusoap_dir  = $t_current_dir . 'lib/';

# includes nusoap classes
chdir( $t_nusoap_dir );
require_once 'nusoap.php';
chdir( $t_current_dir );

//Parametros para configuracion servidor
define('SERVIDOR_DB', 'basesdnp');
define('USUARIO_DB',  'prueba');
define('PASSW_DB',    'prueba');
define('NOMBRE_DB',   'GdOrfeo');

//Parametros para configuracion servidor de produccion
//define('SERVIDOR_DB', 'datumbasis');
//define('USUARIO_DB',  'orfeo');
//define('PASSW_DB',    'orfeoDNP');
//define('NOMBRE_DB',   'GdOrfeo');

include 'adodb/adodb.inc.php';


// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$t_namespace = "webservices/noap";

$server->debug_flag = false;
$server->configureWSDL('orfeo', $t_namespace);
$server->wsdl->schemaTargetNamespace = $t_namespace;
//Definicion del los valores del oojeto a ser devuelto


### ObjectRef Registro de Envios
$server->wsdl->addComplexType(
		'ObjectRefENV',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'Item'			=>	array( 'name' => 'Item',		 'type' => 'xsd:string'),
			'Radicado'		=>	array( 'name' => 'Radicado',	 'type' => 'xsd:string'),
			'Destinatario'		=>	array( 'name' => 'Destinatario', 'type' => 'xsd:string'),
			'Direccion'		=>	array( 'name' => 'Direccion',	 'type' => 'xsd:string'),
			'Remitente'		=>	array( 'name' => 'Remitente',	 'type' => 'xsd:string'),
			'Municipio'		=>	array( 'name' => 'Municipio',	 'type' => 'xsd:string'),
			'Depto'			=>	array( 'name' => 'Depto',		 'type' => 'xsd:string'),
			'Peso'			=>	array( 'name' => 'Peso',		 'type' => 'xsd:string'),
			'Valor'			=>	array( 'name' => 'Valor',	     'type' => 'xsd:string')
		)
	);



### ObjectRefEnvArray Manejo de Envios
 $server->wsdl->addComplexType(
		'ObjectRefEnvArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefENV[]'
		)),
		'tns:ObjectRefENV'
	);


### ObjectRef Definicion para la tabla de envios
$server->wsdl->addComplexType(
		'ObjectRefTEnv',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'id'	     =>	array( 'name' => 'id',   	  'type' => 'xsd:string'),
			'detalle'  =>	array( 'name' => 'detalle',	'type' => 'xsd:string')
		)
	);


### ObjectReSubseriefArray Manejo de SubSeries Tablas de Retencion
 $server->wsdl->addComplexType(
		'ObjectReftiposEnvArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefTEnv[]'
		)),
		'tns:ObjectRefTEnv'
	);




//*************************************************************************************************************
// REGSITRO DE LOS METODOS
//*************************************************************************************************************


// Register the method to expose
### Metodo: Recuperar los envios 
$server->register( 'enviosplanilla',
		array('codplanilla'  	=>	'xsd:int',
			  'fechaini'	=>	'xsd:string',
			  'fechafin'	=>	'xsd:string',
			  'zona'	=>	'xsd:string',
			  'alcance'	=>	'xsd:string'
		),
		array(
			'return'	=>	'tns:ObjectRefEnvArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera los envios generados a partir del proceso de impresion.'
	);

### Metodo: tipoenvios
$server->register( 'tipoenvios',
		array(),
		array(
			'return'	=>	'tns:ObjectReftiposEnvArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera las series de Tablas de Retención de una dependencia de Orfeo.'
	);


//*************************************************************************************************************
// DEFINICIO DE FUNCIONES
//*************************************************************************************************************
// Define the method as a PHP function


// Define the method as a PHP function
function enviosplanilla( $codplanilla, $fechaini, $fechafin, $zona, $alcance) {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
       $db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		// Si la planilla 
		if (!empty($zona) or $zona <> "")  {
		   if ($alcance==0) $sqlWhere.= " SGD_RENV_DEPTO IN ('$zona') AND ";
		   else $sqlWhere.= " SGD_RENV_DEPTO NOT IN ('$zona') AND ";
		}
	    if ($codplanilla <> 0)  $sqlWhere.= "SGD_FENV_CODIGO = $codplanilla AND ";

	     $query = "SELECT SGD_RENV_CANTIDAD  ITEM, 
                              RADI_NUME_SAL RADICADO,
                              SGD_RENV_NOMBRE DESTINATARIO, 
                              SGD_RENV_DIR DIRECCION,
		              SGD_RENV_MPIO MUNICIPIO, 
                              SGD_RENV_DEPTO DEPTO,
                              SGD_RENV_PESO PESO, 
                              SGD_RENV_VALOR VALOR 
                    From SGD_RENV_REGENVIO INNER JOIN DEPENDENCIA on (SGD_RENV_REGENVIO.SGD_DEPE_GENERA = DEPENDENCIA.DEPE_CODI)
                    Where $sqlWhere SGD_RENV_FECH between CONVERT(datetime,'$fechaini',103) and  CONVERT(datetime,'$fechafin',103)  ";
          
	
	  	$rsenvios = $db->Execute($query);
		$result= array();
		while (!$rsenvios->EOF) {
			$result[] = array('Item'=> $rsenvios->fields["ITEM"],
							  'Radicado'=> $rsenvios->fields["RADICADO"],
							  'Destinatario'=> $rsenvios->fields["DESTINATARIO"],
							  'Direccion'=> $rsenvios->fields["DIRECCION"],
							  'Municipio'=> $rsenvios->fields["MUNICIPIO"],
							  'Remitente'=> $rsenvios->fields["REMITENTE"],
							  'Depto'=> $rsenvios->fields["DEPTO"],
							  'Peso'=> $rsenvios->fields["PESO"],
						          'Valor'=> $rsenvios->fields["VALOR"]);
			$rsenvios->MoveNext();
		}

        return $result;
}

function tipoenvios() {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	// 
	
	
	  $query= "SELECT SGD_FENV_CODIGO, SGD_FENV_DESCRIP	FROM SGD_FENV_FRMENVIO ";
  	$rstipo = $db->Execute($query);

		$result= array();
		while (!$rstipo->EOF) {
			$result[] = array('id'=> $rstipo->fields["SGD_FENV_CODIGO"],
			'detalle'=> $rstipo->fields["SGD_FENV_DESCRIP"]);
			$rstipo->MoveNext();
		}
        return $result;
}



// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);


?>
