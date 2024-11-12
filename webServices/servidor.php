<?php
/**********************************************************************************
Diseno de un Web Service que permita la interconexion de aplicaciones con Orfeo
**********************************************************************************/

//Llamado a la clase nusoap
require_once("nusoap/lib/nusoap.php");

//Asignacion del namespace  
//http://wiki.superservicios.gov.co:81/~wduarte/br3.6.0/
$ns="webServices/noap";

//Creacion del objeto soap_server
$server = new soap_server();

include 'adodb/adodb.inc.php';

$server->configureWSDL('orfeo metodos para digitalizador',$ns);


//Definicion del los valores del oojeto a ser devuelto
$server->wsdl->addComplexType(
	'Usuario',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'UsuarioNombre' => array('name' => 'UsuarioNombre', 'type' => 'xsd:string'),
		'UsuarioLogin' => array('name' => 'UsuarioLogin', 'type' => 'xsd:string'),
 		'DocIdent'=> array('name' => 'DocIdent', 'type' => 'xsd:string'),
		'UsaCodigo' => array('name' => 'UsaCodigo', 'type' => 'xsd:int'),
		'DepCodigo' => array('name' => 'DepCodigo', 'type' => 'xsd:int'),
		'PerRad' => array('name' => 'PerRad', 'type' => 'xsd:int')
	)
);


// Definición para la lista de radicados utilizado por el metodo de usuarios Radicados
// Esta definicion se encadena con la denificion del arreglo para ser devueltos
$server->wsdl->addComplexType(
	'ObjectRefLista',
	'complexType',
	'struct',
	'all',
	'',
	array(  'nroradicado'      => array('name' => 'nroradicado',      'type' => 'xsd:string'),
		  	'fechradicado'     => array('name' => 'fechradicado',     'type' => 'xsd:string'),
			'depradicado'      => array('name' => 'depradicado',      'type' => 'xsd:string'),
			'depnombradicado'  => array('name' => 'depnombradicado',  'type' => 'xsd:string'),
		    'asunradicado'     => array('name' => 'asunradicado',     'type' => 'xsd:string'),
   		    'nrohojasradicado' => array('name' => 'nrohojasradicado', 'type' => 'xsd:string')
  )
);


### ObjectRefListaArray Manejo de Lista de Radicados utilizado en usuario Radicados
 $server->wsdl->addComplexType(
		'ObjectRefListaArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefLista[]'
		)),
		'tns:ObjectRefLista'
	);

// definicion del objeto de datos para la recuperacion de la informacion del metodo de registro_digitalizador
$server->wsdl->addComplexType(
	'datdigitalizador',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'error'     => array('name' => 'error', 'type' => 'xsd:string'),
		'mensaje'   => array('name' => 'mensaje', 'type' => 'xsd:string')
	)
);

### ObjectRef Definicion para la tabla de retencion
$server->wsdl->addComplexType(
		'ObjectRefTRD',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'id'	   =>	array( 'name' => 'id',   	'type' => 'xsd:integer'),
			'detalle'  =>	array( 'name' => 'detalle',	'type' => 'xsd:string')
		)
	);

### ObjectReSeriefArray Manejo de Series Tablas de Retencion
 $server->wsdl->addComplexType(
		'ObjectRefSerieArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefTRD[]'
		)),
		'tns:ObjectRefTRD'
	);

### ObjectReSubseriefArray Manejo de SubSeries Tablas de Retencion
 $server->wsdl->addComplexType(
		'ObjectRefSubserieArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefTRD[]'
		)),
		'tns:ObjectRefTRD'
	);


### ObjectRefTipdocArray Manejo de Tipos de Documentos  Tablas de Retencion
 $server->wsdl->addComplexType(
		'ObjectRefTipdocArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefTRD[]'
		)),
		'tns:ObjectRefTRD'
	);



// Definición para notificar un radicado  
$server->wsdl->addComplexType(
	'radinotifica',
	'complexType',
	'struct',
	'all',
	'',
	array( 'error'     => array('name' => 'error', 'type' => 'xsd:string'),
		   'mensaje'   => array('name' => 'mensaje', 'type' => 'xsd:string'),
           'nroradi'   =>	array('name' => 'nroradi',	'type' => 'xsd:string'),
		   'usunom'	   =>	array('name' => 'usunom',	'type' => 'xsd:string'),	
		   'usuemail'  =>	array('name' => 'usuemail',	  'type' => 'xsd:string'),
           'fecrad'	   =>	array('name' => 'fecrad','type' => 'xsd:string'),
		   'radiasun'  =>	array('name' => 'radiasun',	  'type' => 'xsd:string'),
           'radipath'	 =>	array('name' => 'radipath','type' => 'xsd:string')
                           		   
  )
);


/*********************************************************************************
Se registran los servicios que se van a ofrecer, el metodo register tiene los sigientes parametros
**********************************************************************************/

//Servicio de transferir archivo
$server->register('UploadFile',  									 //nombre del servicio                 
    array('bytes' => 'xsd:base64binary', 'filename' => 'xsd:string'),//entradas        
    array('return' => 'xsd:string'),   								 // salidas
    $ns,                         									 //Elemento namespace para el metodo
    $ns . '#UploadFile',   											 //Soapaction para el metodo	
    'rpc',                 											 //Estilo
  	'encoded',             
    'Upload a File'        
);

//Servicio para crear un expediente
$server->register('crearExpediente',  								//nombre del servicio                 
    array('nurad' => 'xsd:string',									//numero de radicado	
     'usuario' => 'xsd:string',										//usuario que genero la radicacion
     'anoExp' => 'xsd:string',										//ano del expediente
     'fechaExp' => 'xsd:string',									//fecha expediente
     'codiSRD'=>'xsd:string',										//Serie del Expediendte
     'codiSBRD'=>'xsd:string',										//Subserie del expediente
     'codiProc'=>'xsd:string',										//Codigo del proceso
     'digCheck'=>'xsd:string',
     'tmr'=>'xsd:string',										//digCheck	
     ),																//entradas        	
    array('return' => 'xsd:string'),   								// salidas
    $ns                     									 	//Elemento namespace para el metod       
);

//Servicio que entrega todos los usuarios de Orfeo
$server->register('darUsuario',
	array(),
	array('return'=>'tns:Matriz'),
	$ns
);

//Servicio que entrega un usuario especifico de Orfeo
$server->register('darUsuarioSelect',
	array(
	'usuaEmail'=> 'xsd:string',
	'usuaDoc' => 'xsd:string'
	),
	array('return'=>'tns:Vector'),
	$ns
);


// Register the method to expose
$server->register('login',				// method name
	 array(	'username'	=>	'xsd:string',
	        'password'	=>	'xsd:string'),
	array('return' => 'tns:Usuario'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#login',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Metodo para autenticacion de Usuario'			// documentation
);


// Register the method to expose
$server->register('usuario',				// method name
	array(	'username'	=>	'xsd:string'),
	array('return' => 'tns:Usuario'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#login',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Metodo para recuperacion del Usuario'			// documentation
);
// Register the method to expose
$server->register('radicados_usuario',				// method name
    array(	'username'	=>	'xsd:string', // Input parameters 
            'inifecha'  =>  'xsd:string',
            'finfecha'  =>  'xsd:string',
			'criterio'  =>  'xsd:string'), 
	array('return' => 'tns:ObjectRefListaArray'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#login',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Metodo para la recuperacion de radicados digitalizados por el usuario activo de windows'			// documentation
);


// Register the method to expose
$server->register('registrar',				// method name
    array(	'pathimagen'       =>	'xsd:string', // Input parameters 
            'nropaginas'       => 'xsd:int',
            'nroradicado'      => 'xsd:string',
		   	'usudigitalizador' => 'xsd:string'), 
	array('return' => 'tns:datdigitalizador'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#login',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Metodo para la actualizacion en el proceso de digitalizacion Tablas radicado y historico'			// documentation
);

### Metodo: Series
$server->register( 'series',
		array(
			'username'	=>	'xsd:string',
			'password'	=>	'xsd:string',
			'codidepe' 	=>	'xsd:int'
		),
		array(
			'return'	=>	'tns:ObjectRefSerieArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera las series de Tablas de Retención de una dependencia de Orfeo.'
	);

### Metodo: SubSeries
$server->register( 'subseries',
		array(
			'username'	=>	'xsd:string',
			'password'	=>	'xsd:string',
			'codidepe' 	=>	'xsd:int',
			'codiserie'	=>	'xsd:int'
		),
		array(
			'return'	=>	'tns:ObjectRefSubserieArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera las Subseries de Tablas de Retención de una dependencia y una serie de Orfeo.'
	);

### Metodo: Tipos de Documentos
$server->register( 'tipodocumentos',
		array(
			'username'  	=>	'xsd:string',
			'password'	    =>	'xsd:string',
			'codidepe'   	=>	'xsd:int',
			'codiserie'	    =>	'xsd:int',
			'codisubserie'	=>	'xsd:int'
		),
		array(
			'return'	=>	'tns:ObjectRefTipdocArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera los Tipos  de documento  de una dependencia y una serie de Orfeo.'
	);

### Metodo: Tipificar -- Aplicar a la tabla de retencion a  un radicado
$server->register('tipificar',				// method name
    array(  'username'  	=>	'xsd:string',
	        'nroradicado'   => 'xsd:string',
			'codiserie'	    =>	'xsd:int',
			'codisubserie'	=>	'xsd:int',
			'tipodoc'	    =>	'xsd:int'), 
	array('return' => 'tns:datdigitalizador'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#login',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Aplica la tabla de retencion a un radicado dado'			// documentation
);


### Metodo: Anexar un archivo 
$server->register('anexararchivo',				// method name
	 array( 'username'  	=>	'xsd:string',
		    'nroradicado'   =>  'xsd:string',
			'anextipo'      =>  'xsd:int',
			'tamano'        =>  'xsd:double',
			'solectura'     =>  'xsd:string',
			'codTrd'        =>  'xsd:string',
			'anexdesc'      =>  'xsd:string'),
	array('return' => 'tns:datdigitalizador'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#login',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Aplica la tabla de retencion a un radicado dado'			// documentation
);

### Metodo: Para la generacion del nombre del archivo del anexo de un radicado 
$server->register('nombreanexo',				// method name
	 array(  'nroradicado'   =>  'xsd:string'),
	array('return' => 'tns:datdigitalizador'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#login',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Metodo: Para la generacion del nombre del archivo del anexo de un radicado '			// documentation
);


// Register the method to expose la consulta de radicados de un usuario de una dependencia. Recibe como parametro el codigo de usuario y el codigo de la dependencia
$server->register('noty_prestamos',				// method name
	 array(	'nroradicado'	=>	'xsd:string',
          'usunotifica'  => 'xsd:string' ),
	array('return' => 'tns:radinotifica'),	// output parameters
	$t_namespace,						// namespace
	'urn:orfeoconnect#noty_prestamos',			// soapaction
	'rpc',								// style
	'encoded',							// use
	'Metodo la consulta de radicadios a ser notificados por correo electronico'			// documentation
);



/**********************************************************************************
Se registran los tipos complejos y/o estructuras de datos
***********************************************************************************/


//Adicionando un tipo complejo MATRIZ
 
$server->wsdl->addComplexType(
    'Matriz',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
	array(),
    array(
    array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Vector[]')
    ),
    'tns:Vector'
);

//Adicionando un tipo complejo VECTOR

$server->wsdl->addComplexType(
    'Vector',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
	array(),
    array(
    array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'xsd:string[]')
    ),
    'xsd:string'
);

/******************************************************************************
 Servicios  que se ofrecen
******************************************************************************/


/**
 * Esta funcion pretende almacenar todos los usuarios de orfeo, con la informacion
 * de correo, cedula, dependencia y codigo del usuario
 * @author German A. Mahecha
 * @return Matriz con todos los usuarios de Orfeo
 */
function darUsuario(){
	$ruta_raiz = "..";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario";
	
	$rs = $db->getResult($sql);
	$i =0;
	while (!$rs->EOF){
			 $usuario[$i]['email'] = $rs->fields['USUA_EMAIL'];
			 $usuario[$i]['codusuario']  = $rs->fields['USUA_CODI'];
			 $usuario[$i]['dependencia'] = $rs->fields['DEPE_CODI'];
			 $usuario[$i]['documento'] =  $rs->fields['USUA_DOC'];
			 $i=$i+1;
			 $rs->MoveNext();
	}
	return $usuario;
}

/**
 * Nos retorna un vector con la informacion de un usuario en particular de Orfeo
 * @param $usuaEmail, correo electronico que tiene en LDAP
 * @param $usuaDoc,   cedula o documento de un usuario
 * @author German A. Mahecha
 * @return 0, si no encuentra el usuario. 
 */
function darUsuarioSelect ($usuaEmail='',$usuaDoc=''){
	$ruta_raiz = "..";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($usuaEmail != ''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where UPPER(USUA_EMAIL) = UPPER('$usuaEmail')";
	}elseif ($usuaDoc !=''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where USUA_DOC = $usuaDoc";
	}else {
		return $usuario;
	}
	
	$rs = $db->getResult($sql);
	while (!$rs->EOF){
			 $usuario['email'] = $rs->fields['USUA_EMAIL'];
			 $usuario['codusuario']  = $rs->fields['USUA_CODI'];
			 $usuario['dependencia'] = $rs->fields['DEPE_CODI'];
			 $usuario['documento'] =  $rs->fields['USUA_DOC'];
			 $rs->MoveNext();
	}
	return $usuario;
}


/**
 * UploadFile es una funcion que permite almacenar cualquier tipo de archivo en el lado del servidor
 * @param $bytes 
 * @param $filename es el nombre del archivo con que queremos almacenar en el servidor
 * @author German A. Mahecha
 * @return Retorna un String indicando si la operacion fue satisfactoria o no
 */
function UploadFile($bytes, $filename){
	$var = explode(".",$filename);
	//try{
		//direccion donde se quiere guardar los archivos
		$path = getPath($filename);
		$fp = fopen("$path", "w") or die("fallo");
		// decodificamos el archivo 
		$bytes=base64_decode($bytes);
		$salida=true;
		if( is_array($bytes) ){
			foreach($bytes as $k => $v){
				$salida=($salida && fwrite($fp,$bytes));
			}
		}else{
			$salida=fwrite($fp,$bytes);
		}
		fclose($fp);
	/*}catch (Exception $e){
		return "error";  
	}*/
	if ($salida){
	return "exito";
	}else{
	return "error";	
	}

}
/**
 * Esta funcion permite obtener el path donde se debe almacenar el archivo
 * @param $filename, el nombre del archivo 
 * @author German A. Mahecha
 * @return Retorna el path
 */
function getPath($filename){
	$var = explode(".",$filename);
	$path = "../bodega/";
	$path .= substr($var[0],0,4);
	$path .= "/".substr($var[0],4,3);
	$path .= "/docs/".$filename;
	return  $path;
}

/**
 * Esta funcion permite crear un expediente a partir de un radicado
 * @param $nurad, este parametro es el numero de radicado
 * @param $usuario, este parametro es el usuario que crea el expediente, es el usuario de correo
 * @author German A. Mahecha
 * @return El numero de expediente para asignarlo en aplicativo de contribuciones AI 
 */
function crearExpediente($nurad,$usuario,$anoExp,$fechaExp,$codiSRD,$codiSBRD,$codiProc,$digCheck,$tmr){
		
	$ruta_raiz = "..";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	include_once("$ruta_raiz/include/tx/Expediente.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$expediente = new Expediente($db);
	
	//Aqui busco la informacion necesaria del usuario para la creacion de expedientes
	$sql= "select USUA_CODI,DEPE_CODI,USUA_DOC from usuario where upper(usua_email) = upper ('".$usuario."@superservicios.gov.co')";
	$rs = $db->conn->Execute($sql);
	while (!$rs->EOF){
			 $codusuario  = $rs->fields['USUA_CODI'];
			 $dependencia = $rs->fields['DEPE_CODI'];
			 $usua_doc =  $rs->fields['USUA_DOC'];
			 $usuaDocExp = $usua_doc; 
			 $rs->MoveNext();
	} 
	
	//Insercion para el TMR
    $sql =	"insert into sgd_rdf_retdocf (sgd_mrd_codigo,radi_nume_radi,depe_codi,usua_codi,usua_doc,sgd_rdf_fech)";
    $sql .= " values ($tmr,$nurad,$dependencia,$codusuario,'$usua_doc',SYSDATE)";
    
    $db->conn->Execute($sql);
   
		
	$trdExp = substr("00".$codiSRD,-2) . substr("00".$codiSBRD,-2);
	$secExp = $expediente->secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp);
	$consecutivoExp = substr("00000".$secExp,-5);
	$numeroExpediente = $anoExp . $dependencia . $trdExp . $consecutivoExp . $digCheck;
										
													                                                                                                               
	$numeroExpedienteE = $expediente->crearExpediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc,$usuaDocExp,$codiSRD,$codiSBRD,'false',$fechaExp,$codiProc);
	
	$insercionExp = $expediente->insertar_expediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc);	

	return $numeroExpedienteE;
}




$server->service($HTTP_RAW_POST_DATA);

?>
