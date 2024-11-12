<?php

$t_current_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
$t_nusoap_dir  = $t_current_dir . 'lib';

# includes nusoap classes
chdir( $t_nusoap_dir );
require_once( 'nusoap.php' );
chdir( $t_current_dir );

include 'adodb/adodb.inc.php';


// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$t_namespace = 'http://orfeo.accionsocial.gov.co/webservice';

$server->debug_flag = false;
$server->configureWSDL('orfeo', $t_namespace);
$server->wsdl->schemaTargetNamespace = $t_namespace;
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


### ObjectRef Departamento
$server->wsdl->addComplexType(
		'ObjectRef',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'id'	=>	array( 'name' => 'id',	'type' => 'xsd:integer'),
			'nombre'	=>	array( 'name' => 'nombre',	'type' => 'xsd:string')
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
			'id'	=>	array( 'name' => 'id',	'type' => 'xsd:integer'),
			'detalle'	=>	array( 'name' => 'detalle',	'type' => 'xsd:string')
		)
	);


### ObjectRef Municipio
$server->wsdl->addComplexType(
		'ObjectRefMuni',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'iddpto'	=>	array( 'name' => 'iddpto',	'type' => 'xsd:integer'),
			'idmuni'	=>	array( 'name' => 'idmuni',	'type' => 'xsd:integer'),
			'nombre'	=>	array( 'name' => 'nombre',	'type' => 'xsd:string')
		)
	);


### ObjectRef Registro de Envios
$server->wsdl->addComplexType(
		'ObjectRefENV',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'Item'			=>	array( 'name' => 'Item',		 'type' => 'xsd:integer'),
			'Radicado'		=>	array( 'name' => 'Radicado',	 'type' => 'xsd:string'),
			'Destinatario'	=>	array( 'name' => 'Destinatario', 'type' => 'xsd:string'),
			'Remitente'		=>	array( 'name' => 'Remitente',	 'type' => 'xsd:string'),
			'Municipio'		=>	array( 'name' => 'Municipio',	 'type' => 'xsd:string'),
			'Depto'			=>	array( 'name' => 'Depto',		 'type' => 'xsd:string'),
			'Peso'			=>	array( 'name' => 'Peso',		 'type' => 'xsd:string'),
			'Valor'			=>	array( 'name' => 'Valor',	     'type' => 'xsd:string')
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

### ObjectReGrupofArray Manejo de Dependencias
 $server->wsdl->addComplexType(
		'ObjectRefGrupoArray',
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

### ObjectRefArray Departamento
 $server->wsdl->addComplexType(
		'ObjectRefArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRef[]'
		)),
		'tns:ObjectRef'
	);

	### ObjectRefArray para los Municipios
 $server->wsdl->addComplexType(
		'ObjectRefMuniArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefMuni[]'
		)),
		'tns:ObjectRefMuni'
	);



### ObjectRefDest para los Destinatarios 
$server->wsdl->addComplexType(
		'ObjectRefDest',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'iddpto'	=>	array( 'name' => 'iddpto',	'type' => 'xsd:integer'),
			'idmuni'	=>	array( 'name' => 'idmuni',	'type' => 'xsd:integer'),
			'nombre'	=>	array( 'name' => 'nombre',	'type' => 'xsd:string'),
			'apellidos'	=>	array( 'name' => 'apellidos',	'type' => 'xsd:string'),
			'nroducumento'	=>	array( 'name' => 'nroducumento',	'type' => 'xsd:string'),
			'direccion'	=>	array( 'name' => 'direccion',	'type' => 'xsd:string'),
			'telefono'	=>	array( 'name' => 'telefono',	'type' => 'xsd:string'),
			'email'	=>	array( 'name' => 'email',	'type' => 'xsd:string')
		)
	);

### ObjectReDestfArray para los Destinatarios
 $server->wsdl->addComplexType(
		'ObjectReDestfArray',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array(),
		array(array(
			'ref'				=> 'SOAP-ENC:arrayType',
			'wsdl:arrayType'	=> 'tns:ObjectRefDest[]'
		)),
		'tns:ObjectRefDest'
	);

// Definición para devoluciòn del numero de radicado para el metodo de radicacion
$server->wsdl->addComplexType(
	'Numradicado',
	'complexType',
	'struct',
	'all',
	'',
	array( 'Nroradicado' => array('name' => 'Nroradicado', 'type' => 'xsd:string')
  )
);


// Definición para devoluciòn del numero de radicado para el metodo anexar archivo
$server->wsdl->addComplexType(
	'Radsalida',
	'complexType',
	'struct',
	'all',
	'',
	array( 'Nrosalida' => array('name' => 'Nrosalida', 'type' => 'xsd:string'),
		   'Nombrearchivo' => array('name' => 'Nombrearchivo', 'type' => 'xsd:string')
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



// Definición para la lista de radicados utilizado por el metodo de chequear Radicados
$server->wsdl->addComplexType(
	'ObjectRefLista',
	'complexType',
	'struct',
	'all',
	'',
	array( 'Fecha' => array('name' => 'Fecha', 'type' => 'xsd:string'),
		   'radi_nume_radi' => array('name' => 'radi_nume_radi', 'type' => 'xsd:string'),
		   'ra_asun' => array('name' => 'ra_asun', 'type' => 'xsd:string'),
		   'sgd_tpr_descrip' => array('name' => 'sgd_tpr_descrip', 'type' => 'xsd:string'),
   		   'sgd_tpr_codigo' => array('name' => 'sgd_tpr_codigo', 'type' => 'xsd:string'),
		   'sgd_tpr_termino' => array('name' => 'sgd_tpr_termino', 'type' => 'xsd:string'),
		   'radi_tipo_deri' => array('name' => 'radi_tipo_deri', 'type' => 'xsd:string'),
		   'sgd_dir_nomremdes' => array('name' => 'sgd_dir_nomremdes', 'type' => 'xsd:string'),
		   'sgd_dir_tipo' => array('name' => 'sgd_dir_tipo', 'type' => 'xsd:string'),
		   'sgd_dir_nombre' => array('name' => 'sgd_dir_nombre', 'type' => 'xsd:string'),
		   'radi_cuentai' => array('name' => 'radi_cuentai', 'type' => 'xsd:string'),
		   'sgd_exp_numero' => array('name' => 'sgd_exp_numero', 'type' => 'xsd:string')
  )
);


### ObjectRefListaArray Manejo de Lista de Radicados utilizado en Chequear Radicados
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


//*************************************************************************************************************
// REGSITRO DE LOS METODOS
//*************************************************************************************************************

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
//Este metodo permite la radicacion de tipos de documentos de salida y memorando radicado sin asociado
// trdcodigo = 1 y trdcodigo = 3  
$server->register('radicacion',				// method name
	 array(	'username'		=>	'xsd:string',
			'password'		=>	'xsd:string',
			'tiporadicado'	=>	'xsd:int',
			'dependestino'	=>	'xsd:int',
			'usuadestino'	=>	'xsd:int',
			'asunto'		=>	'xsd:string',
			'desanexo'		=>	'xsd:string',
			'radiFechOfic'  =>  'xsd:string',
			'trdcodigo'		=>	'xsd:int',
			'nombredes'		=>	'xsd:string',
			'docnro'		=>	'xsd:string',
			'codimuni'		=>	'xsd:int',
			'codidpto'		=>	'xsd:int',
			'idpais'		=>	'xsd:int',
			'idcontiente'	=>	'xsd:int',
			'direccion'		=>	'xsd:string',
			'telefono'		=>	'xsd:string',
			'mail'			=>	'xsd:string',
			'nombre'		=>	'xsd:string'),
	array('return' => 'tns:Numradicado'),	// output parameters
	$t_namespace,							// namespace
	'urn:orfeoconnect#radicacion',			// soapaction
	'rpc',									// style
	'encoded',								// use
	'Metodo para el registro de radicados tipos de documentos de salida(1) y memorando(3) '	// documentation
);

// Este metodo permite anexar un archivo a un radicado 
$server->register('anexararchivo',				// method name
	 array(	'username'		=>	'xsd:string',
			'password'		=>	'xsd:string',
			'nroradicado'   =>  'xsd:string',
			'anextipo'      =>  'xsd:int',
			'tamano'        =>  'xsd:double',
			'solectura'     =>  'xsd:string',
			'anexdesc'      =>  'xsd:string',
			'anexomasiva'   =>  'xsd:string'),
	array('return' => 'tns:Radsalida'),	// output parameters
	$t_namespace,							// namespace
	'urn:orfeoconnect#anexararchivo',		// soapaction
	'rpc',									// style
	'encoded',								// use
	'Metodo para el anexar archivos a un radicado'	// documentation
);



	### Metodo: Departamentos
$server->register( 'departamentos',
		array(
			'username'	=>	'xsd:string',
			'password'	=>	'xsd:string'
		),
		array(
			'return'	=>	'tns:ObjectRefArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera los departamentos de Orfeo.'
	);

	### Metodo: Departamentos
$server->register( 'municipios',
		array(
			'username'	=>	'xsd:string',
			'password'	=>	'xsd:string',
			'idpto'  	=>	'xsd:int'
		),
		array(
			'return'	=>	'tns:ObjectRefMuniArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera los municipios  de Orfeo.'
	);


	### Metodo: Destinatarios
$server->register( 'destinatarios',
		array(
			'username'	=>	'xsd:string',
			'password'	=>	'xsd:string',
			'idpto'  	=>	'xsd:int',
			'idmpio'  	=>	'xsd:int',
			'criterio'	=>	'xsd:string',
			'tipodestinario' =>'xsd:int'
		),
		array(
			'return'	=>	'tns:ObjectReDestfArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera los destinatarios de acuerdo a un criterio  de Orfeo.'
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
$server->register( 'tipificar',
		array(
			'username'  	=>	'xsd:string',
			'password'	    =>	'xsd:string',
			'nroradicado'  	=>	'xsd:string',
			'codiserie'	    =>	'xsd:int',
			'codisubserie'	=>	'xsd:int',
			'tipodoc'	    =>	'xsd:int'
		),
		array(
			'return'	=>	'xsd:string'
		),
		$t_namespace,
		false, false, false,
		'Aplica la tabla de retencion a un radicado dado'
	);

### Metodo: Marcar radicado para el envio del documento
$server->register( 'generarimpreso',
		array(
			'username'  	=>	'xsd:string',
			'password'	    =>	'xsd:string',
			'nroradicado'  	=>	'xsd:string'
		),
		array(
			'return'	=>	'xsd:string'
		),
		$t_namespace,
		false, false, false,
		'Marca el radicado para el proceso de envio '
	);



### Metodo: Recuperar los envios 
$server->register( 'enviosplanilla',
		array(
			'username'  	=>	'xsd:string',
			'password'	    =>	'xsd:string',
			'codplanilla'  	=>	'xsd:int',
			'fechaplanilla'	=>	'xsd:string',
			'zona'			=>	'xsd:string'
		),
		array(
			'return'	=>	'tns:ObjectRefEnvArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera los envios generados a partir del proceso de impresion.'
	);



### Metodo: Grupos Dependencias
$server->register( 'grupos',
		array(
			'username'			=>	'xsd:string',
			'password'			=>	'xsd:string',
			'idpto' 	=>	'xsd:int',
			'idmpio'	=>	'xsd:int'

		),
		array(
			'return'	=>	'tns:ObjectRefGrupoArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera las Subseries de Tablas de Retención de una dependencia y una serie de Orfeo.'
	);


### Metodo: Metodo para la recuperacion de radicados para chequear
$server->register( 'chequearadicado',
		array(
			'username'	=>	'xsd:string',
			'password'	=>	'xsd:string',
			'nroradicado'		=>	'xsd:string',
			'nroidentificacion'	=>	'xsd:string',
			'fechaini'			=>	'xsd:string',
			'fechafin'			=>	'xsd:string'

		),
		array(
			'return'	=>	'tns:ObjectRefListaArray'
		),
		$t_namespace,
		false, false, false,
		'Recupera los radicados a partir de un criterio (radicado, Identificacion y rango de fecha.'
	);


//*************************************************************************************************************
// DEFINICIO DE FUNCIONES
//*************************************************************************************************************
// Define the method as a PHP function
function login($username, $password ) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
//	$db->Connect('172.20.2.14', 'fldoc', 'accion', 'red_orcl');
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');
	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		return array('UsuarioNombre'=>$rs->fields["USUA_NOMB"],
					 'UsuarioLogin'=>$rs->fields["USUA_LOGIN"], 
			         'DocIdent'=>$rs->fields["USUA_DOC"],
					 'UsaCodigo'=>$rs->fields["USUA_CODI"],
					 'DepCodigo'=>$rs->fields["DEPE_CODI"],
					 'PerRad'=>$rs->fields["PERM_RADI"]);
	}
    else return array();
}


// Define the method as a PHP function
// tiporadicado es el tipo de destinatario 1= Personas Naturales , 2y3 Entidades y Empresas, 4 Funcionarios
function radicacion($username, $password, $tiporadicado, $dependestino, $usuadestino, $asunto, $desanexo, $radiFechOfic,
					$trdcodigo, $nombredes, $docnro, $codimuni, $codidpto, $idpais, $idcontiente,  $direccion, $telefono, $mail, $nombre )
{

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');
	// 
	
	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		$query= "SELECT depe_rad_tp1 FROM dependencia WHERE  depe_codi =".$rs->fields["DEPE_CODI"];
		$rsDepen = $db->Execute($query);
		$SecName = "SECR_TP$trdcodigo"."_".$rsDepen->fields["DEPE_RAD_TP1"];
		$secNew=$db->nextId($SecName);
		// Se arma el numero de radicado año+dependencia+6 digitos y el tipo de radicado	
		$newRadicado = date("Y") . $rs->fields["DEPE_CODI"] . str_pad($secNew,6,"0", STR_PAD_LEFT) . $trdcodigo;

		// Se arma el arreglo para la inclusion del radicado
		$recordR =array();
		$recordR["RADI_NUME_RADI"]	   =  $newRadicado;
		$recordR["RADI_FECH_RADI"]	   =  $db->OffsetDate(0,$db->sysTimeStamp);
		$recordR["RADI_FECH_OFIC"]	   =  $db->DBDate($radiFechOfic);
		$recordR["TDOC_CODI"]		   =  0;
		$recordR["CODI_NIVEL"]		   =  $rs->fields["CODI_NIVEL"];
		$recordR["RADI_DEPE_ACTU"]	   =  $dependestino;
        $recordR['MUNI_CODI']	       =  $codimuni;
        $recordR['DPTO_CODI']		   =  $codidpto;		
		$recordR["RADI_DEPE_RADI"]	   =  $dependestino; // Codigo de la dependencia que radica 
		$recordR["RADI_USUA_ACTU"]	   =  $usuadestino;
		$recordR["RADI_USUA_RADI"]	   =  $usuadestino;
		$recordR["CARP_CODI"]		   =  $trdcodigo;
		$recordR["CARP_PER"]		   =  0;
		$recordR["RA_ASUN"]			   =  "'".$asunto."'";
		$recordR["RADI_DESC_ANEX"]	   = "'". $desanexo."'";

		$insertSQL = $db->Replace("RADICADO", $recordR, "", false);


		If(!$insertSQL)
		{
			return array();		
		}
		// Se registra archivo en historico
		mci_registrohistorico($newRadicado,$rs->fields["DEPE_CODI"],$rs->fields["USUA_CODI"],$rs->fields["USUA_DOC"],"Radicación Externa");
	    
		// Se arma el arreglo para la insercion de la direccion
	   $nextval=$db->nextId("sec_dir_direcciones");
	   if ($nextval==-1) return array('Nroradicado'=>$newRadicado);	
	   
	   $recordD =array();
       $recordD['SGD_DIR_CODIGO']     = $nextval;
       $recordD['SGD_DIR_TIPO']       = 1;
       $recordD['RADI_NUME_RADI']     = $newRadicado;
       $recordD['MUNI_CODI']	      = $codimuni;
       $recordD['DPTO_CODI']		  = $codidpto;
       $recordD['SGD_DIR_DIRECCION']  = "'".$direccion."'";
       $recordD['SGD_DIR_TELEFONO']   = "'".$telefono."'";
       $recordD['SGD_DIR_MAIL']       = "'".$mail."'";
       $recordD['SGD_DIR_NOMBRE']     = "'".$nombre."'";
	   if ($tiporadicado == 4)  $recordD['SGD_DOC_FUN']        = $docnro;
       $recordD['SGD_DIR_NOMREMDES']  = "'"."$nombredes"."'";
       $recordD['SGD_TRD_CODIGO']     = $trdcodigo;
       $recordD['ID_PAIS']			  = $idpais;
       $recordD['ID_CONT']			  = $idcontiente;

	   $insertSQL = $db->Replace("SGD_DIR_DRECCIONES", $recordD, false);
	   
		return array('Nroradicado'=>$newRadicado);
	}
    else return array();
}


// Define the method as a PHP function
function anexararchivo($username, $password, $nroradicado, $anextipo, $tamano, $solectura, $anexdesc, $anexomasiva )
{

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');
	// 
	
	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		// Se genera el nro de documentos que se han anexado al un radicado
		$query = 'SELECT count(*) as "CANTRADICADOS" FROM anexos WHERE anex_radi_nume =' .$nroradicado;
		$rscant = $db->Execute($query);
		$numanex = $rscant->fields["CANTRADICADOS"] + 1;
	
		// Se construe el valor de anex_codigo
		$anexcodigo = $nroradicado .str_pad($numanex,5,"0", STR_PAD_LEFT); 

		$anexsalida = 1 ;
        $radsalida =  $nroradicado;
        // Se arma el radicado de salida si es requerido

		if (substr($nroradicado,-1,1)==2)	 {
			$query= "SELECT depe_rad_tp1 FROM dependencia WHERE  depe_codi =".$rs->fields["DEPE_CODI"];
			$rsDepen = $db->Execute($query);
			$SecName = "SECR_TP1"."_".$rsDepen->fields["DEPE_RAD_TP1"];
			$secNew=$db->nextId($SecName);
			$radsalida = date("Y") . $rs->fields["DEPE_CODI"] . str_pad($secNew,6,"0", STR_PAD_LEFT) ."1";
        }

		$nombarchivo = "1".$nroradicado."_".str_pad($numanex,5,"0", STR_PAD_LEFT).".doc" ;
		// Se arma el arreglo para la inclusion del anexo
		$recordA =array();
		$recordA["ANEX_RADI_NUME"]	   =  $nroradicado;
		$recordA["ANEX_CODIGO"]		   =  $anexcodigo; //Variable Calculada
		$recordA["ANEX_TIPO"]		   =  $anextipo; 
		$recordA["ANEX_TAMANO"]		   =  $tamano;
		$recordA["ANEX_SOLO_LECT"]	   =  "'".$solectura."'";
		$recordA["ANEX_CREADOR"]	   =  "'".$username."'";  //Variable de usuario
		$recordA["ANEX_DESC"]	       =  "'".$anexdesc."'";
		$recordA["ANEX_NUMERO"]	       =  $numanex;  //Variable calculada
		$recordA["ANEX_NOMB_ARCHIVO"]  =  "'".$nombarchivo."'";
		$recordA["ANEX_BORRADO"]	   =  "'N'";
		$recordA["ANEX_ORIGEN"]		   =  0;
		$recordA["ANEX_SALIDA"]		   =  $anexsalida; //variable calculada
		$recordA["RADI_NUME_SALIDA"]   =  $radsalida;
		$recordA["ANEX_RADI_FECH"]     =  $db->OffsetDate(0,$db->sysTimeStamp);
		$recordA["ANEX_ESTADO"]		   =  2;
		$recordA["ANEX_DEPE_CREADOR"]  =  $rs->fields["DEPE_CODI"];
		$recordA["SGD_DIR_TIPO"]	   =  1;


		$insertSQL = $db->Replace("ANEXOS", $recordA, "", false);

		If(!$insertSQL)
		{
			return array();		
		}

		// Se arma el arreglo para la actualizacion del radicado de anexo path
		$recordR =array();
		$recordR["RADI_NUME_RADI"]	   =  $nroradicado;
		if (empty($anexomasiva)) {
       		$recordR["RADI_PATH"]		   = "'/". date("Y")."/".$rs->fields["DEPE_CODI"]."/docs/".$nombarchivo."'";
        } else {
			$nombarchivo= $anexomasiva;
       		$recordR["RADI_PATH"]		   = "'/". date("Y")."/".$rs->fields["DEPE_CODI"]."/masiva/".$anexomasiva."'";
        }
		$insertSQL = $db->Replace("RADICADO", $recordR, "RADI_NUME_RADI", false);

		return array('Nrosalida'=>$radsalida,
			         'Nombrearchivo' => $nombarchivo);
	}
    else return array();
}

	
	# --------------------
	# return user_id if successful, otherwise false.
	function mci_registrohistorico ($nroradicado, $dependencia, $codiusua, $usuadoc, $observacion ) {

		$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
		//$db->debug = true;
		$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

		$recordH =array();
		$recordH["DEPE_CODI"]	   = $dependencia;
		$recordH["HIST_FECH"]	   = $db->OffsetDate(0,$db->sysTimeStamp); //Valor calculado
		$recordH["USUA_CODI"]	   = $codiusua;
		$recordH["RADI_NUME_RADI"] = $nroradicado;
		$recordH["HIST_OBSE"]      = "'".$observacion."'" ;
		$recordH["USUA_CODI_DEST"] = $codiusua;
		$recordH["USUA_DOC"]       = $usuadoc;
		$recordH["SGD_TTR_CODIGO"] = 2;
		$recordH["HIST_DOC_DEST"]  = $usuadoc;
		$recordH["DEPE_CODI_DEST"]  = $dependencia;

		$insertSQL = $db->Replace("HIST_EVENTOS", $recordH, "", false);
        return;
	}

// Define the method as a PHP function
function departamentos($username, $password) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
	// 
	  	$query= "SELECT DPTO_CODI, DPTO_NOMB FROM departamento";
		$rsdepto = $db->Execute($query);
		$result= array();
		while (!$rsdepto->EOF) {
			$result[] = array('id'=> $rsdepto->fields["DPTO_CODI"],
			'nombre'=> $rsdepto->fields["DPTO_NOMB"]);
			$rsdepto->MoveNext();
		}

        return $result;

	}
	return 0;
}

// Define the method as a PHP function
function municipios($username, $password, $idpto) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
	// 
		if ($idpto!=1 )  $sqlWhere.= "DPTO_CODI = ".$idpto." and ";
		$sqlWhere.="1=1 ";
	  	$query= "SELECT DPTO_CODI, MUNI_CODI, MUNI_NOMB FROM municipio WHERE $sqlWhere ORDER BY MUNI_NOMB";
		$rs = $db->Execute($query);
		$result= array();
		while (!$rs->EOF) {
			$result[] = array('iddpto'=> $rs->fields["DPTO_CODI"],
							  'idmuni'=> $rs->fields["MUNI_CODI"],
							  'nombre'=> $rs->fields["MUNI_NOMB"]);
			$rs->MoveNext();
		}

        return $result;

	}
	return 0;
}

function destinatarios($username, $password, $idpto, $idmpio, $criterio, $tipodestinario) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {

		switch ($tipodestinario)
		{	case '1':
			{   if (strlen($criterio)) $sqlWhere.=" (SGD_CIU_NOMBRE like '%$criterio%' or SGD_CIU_APELL1 like '%$criterio%') and ";
				//Si se desea realizar la búsqueda por departamento
				if ($idpto!=1 )  $sqlWhere.= "DPTO_CODI = ".$idpto." and ";
				//Si se desea realizar la búsqueda por municipio
				if ($idmpio!=999)	$sqlWhere.= "MUNI_CODI = ".$idmpio." and ";
				$sqlWhere.="1=1 ";
				$query = "SELECT  DPTO_CODI, MUNI_CODI, SGD_CIU_NOMBRE,SGD_CIU_APELL1 || ' ' ||  SGD_CIU_APELL2 APELLIDOS,
					  SGD_CIU_CEDULA, SGD_CIU_DIRECCION, SGD_CIU_TELEFONO, SGD_CIU_EMAIL
					  FROM SGD_CIU_CIUDADANO WHERE $sqlWhere
					  ORDER BY  DPTO_CODI asc, MUNI_CODI asc, SGD_CIU_NOMBRE asc ";
		

		}break;
		case '3':
		   {  if (strlen($criterio)) {
  		     	$sqlWhere.=" (NOMBRE_DE_LA_EMPRESA like '%$criterio%' or SIGLA_DE_LA_EMPRESA like '%$criterio%') and ";
  		     	$sqlWhere1.=" (SGD_OEM_OEMPRESA like '%$criterio%' or SGD_OEM_SIGLA like '%$criterio%') and ";  		     	
		  		 }
				//Si se desea realizar la búsqueda por departamento
				if ($idpto!=1 ) {
					$sqlWhere.= "CODIGO_DEL_DEPARTAMENTO = ".$idpto." and ";
					$sqlWhere1.= "DPTO_CODI = ".$idpto." and ";					
				}
				//Si se desea realizar la búsqueda por municipio
				if ($idmpio!=999) {
					$sqlWhere.= "CODIGO_DEL_MUNICIPIO = $idmpio and ";
					$sqlWhere1.= "MUNI_CODI = $idmpio and ";					
				}
				$sqlWhere.="1=1 ";
				$sqlWhere1.="1=1 ";
				$query = "SELECT NOMBRE_DE_LA_EMPRESA SGD_CIU_NOMBRE ,SIGLA_DE_LA_EMPRESA APELLIDOS, CODIGO_DEL_DEPARTAMENTO DPTO_CODI, CODIGO_DEL_MUNICIPIO MUNI_CODI,
					NIT_DE_LA_EMPRESA SGD_CIU_CEDULA, DIRECCION SGD_CIU_DIRECCION, TELEFONO_1 SGD_CIU_TELEFONO,  EMAIL SGD_CIU_EMAIL
					FROM BODEGA_EMPRESAS WHERE $sqlWhere
					UNION
				    SELECT SGD_OEM_OEMPRESA, SGD_OEM_REP_LEGAL, to_CHAR(DPTO_CODI), To_CHAR(MUNI_CODI),SGD_OEM_NIT,SGD_OEM_DIRECCION,SGD_OEM_TELEFONO,''
					FROM SGD_OEM_OEMPRESAS WHERE $sqlWhere1 
					ORDER BY  DPTO_CODI asc, MUNI_CODI asc, SGD_CIU_NOMBRE asc";
				
        }break;
		case '4':
			{   if (strlen($criterio)) $sqlWhere.=" USUA_NOMB like '%$criterio%' and ";
				//Si se desea realizar la búsqueda por dependencia
				if ($idpto!=1 )  $sqlWhere.= "USUARIO.DEPE_CODI = ".$idpto." and ";
				
				$sqlWhere.="1=1 ";
				$query ="SELECT USUA_NOMB SGD_CIU_NOMBRE, '' APELLIDOS, DEPENDENCIA.DPTO_CODI DPTO_CODI, DEPENDENCIA.MUNI_CODI MUNI_CODI,
					USUA_DOC SGD_CIU_CEDULA, USUARIO.DEPE_CODI || '  '  || DEPENDENCIA.DEPE_NOMB SGD_CIU_DIRECCION,
					USUA_EXT SGD_CIU_TELEFONO, USUA_EMAIL  SGD_CIU_EMAIL
					FROM USUARIO, DEPENDENCIA  WHERE USUARIO.DEPE_CODI = DEPENDENCIA.DEPE_CODI and 
					$sqlWhere ORDER by USUARIO.DEPE_CODI, USUARIO.USUA_NOMB ";	
        }break;        

    }
        $rs = $db->Execute($query);
		$result= array();
		while (!$rs->EOF) {
					$result[] = array('iddpto'=> $rs->fields["DPTO_CODI"],
							  'idmuni'=> $rs->fields["MUNI_CODI"],
							  'nombre'=> $rs->fields["SGD_CIU_NOMBRE"],
							  'apellidos'=> $rs->fields["APELLIDOS"] ,
							  'nroducumento'=> $rs->fields["SGD_CIU_CEDULA"],	
							  'direccion'=> $rs->fields["SGD_CIU_DIRECCION"],	
							  'telefono'=> $rs->fields["SGD_CIU_TELEFONO"],	
  							  'email'=> $rs->fields["SGD_CIU_EMAIL"]  );
					$rs->MoveNext();
		 }
         return $result;
  }
   return 0;
}

function series($username, $password, $codidepe) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		
		$fecha_hoy = Date("Y-m-d");
		$sqlFechaHoy=$db->DBDate($fecha_hoy);
	
	  	$query= "SELECT DISTINCT s.SGD_SRD_DESCRIP DETALLE, s.SGD_SRD_CODIGO 
	  	FROM SGD_MRD_MATRIRD m, SGD_SRD_SERIESRD s
	  	WHERE m.DEPE_CODI = $codidepe and s.SGD_SRD_CODIGO = m.SGD_SRD_CODIGO and "
	  	. $sqlFechaHoy ." between s.SGD_SRD_FECHINI and s.SGD_SRD_FECHFIN ORDER BY DETALLE ";

	  	$rsserie = $db->Execute($query);
		$result= array();
		while (!$rsserie->EOF) {
			$result[] = array('id'=> $rsserie->fields["SGD_SRD_CODIGO"],
			'detalle'=> $rsserie->fields["DETALLE"]);
			$rsserie->MoveNext();
		}

        return $result;

	}
	return 0;
}
		

function subseries($username, $password, $codidepe, $codiserie) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		
		$fecha_hoy = Date("Y-m-d");
		$sqlFechaHoy=$db->DBDate($fecha_hoy);
	
	  	$query= "SELECT DISTINCT su.SGD_SBRD_DESCRIP DETALLE, su.SGD_SBRD_CODIGO 
	  	       FROM SGD_MRD_MATRIRD m, SGD_SBRD_SUBSERIERD su
	  	       WHERE m.DEPE_CODI = $codidepe  AND m.SGD_SRD_CODIGO = $codiserie AND su.SGD_SRD_CODIGO = $codiserie
	  	       AND su.SGD_SBRD_CODIGO = m.SGD_SBRD_CODIGO AND "
	  	       .$sqlFechaHoy. " BETWEEN su.SGD_SBRD_FECHINI AND su.SGD_SBRD_FECHFIN ORDER BY DETALLE ";

	  	$rssubserie = $db->Execute($query);
		$result= array();
		while (!$rssubserie->EOF) {
			$result[] = array('id'=> $rssubserie->fields["SGD_SBRD_CODIGO"],
			'detalle'=> $rssubserie->fields["DETALLE"]);
			$rssubserie->MoveNext();
		}

        return $result;

	}
	return 0;
}

 

function tipodocumentos($username, $password, $codidepe, $codiserie, $codisubserie) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
	
	  	$query= "SELECT DISTINCT t.SGD_TPR_DESCRIP AS DETALLE, t.SGD_TPR_CODIGO 
				FROM SGD_MRD_MATRIRD m, SGD_TPR_TPDCUMENTO t 
				WHERE m.DEPE_CODI =  $codidepe AND m.SGD_SRD_CODIGO = $codiserie AND m.SGD_SBRD_CODIGO = $codisubserie 
					AND t.SGD_TPR_CODIGO = m.SGD_TPR_CODIGO AND t.SGD_TPR_TP1='1' ORDER BY SGD_TPR_CODIGO  ";

	  	$rstipdoc = $db->Execute($query);
		$result= array();
		while (!$rstipdoc->EOF) {
			$result[] = array('id'=> $rstipdoc->fields["SGD_TPR_CODIGO"],
			'detalle'=> $rstipdoc->fields["DETALLE"]);
			$rstipdoc->MoveNext();
		}

        return $result;

	}
	return 0;
}


function tipificar($username, $password, $nroradicado, $codiserie, $codisubserie, $tipodoc) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {


			$query = "SELECT SGD_MRD_CODIGO FROM SGD_MRD_MATRIRD 
					where DEPE_CODI = ". $rs->fields["DEPE_CODI"] .
				 	  " and SGD_SRD_CODIGO = '$codiserie'
				       and SGD_SBRD_CODIGO = '$codisubserie'
					   and SGD_TPR_CODIGO = '$tipodoc'";

			$rsTRD = $db->Execute($query);
			if ($rsTRD->RecordCount( ) > 0) {
				$record =array();
				$record["RADI_NUME_RADI"] = $nroradicado;
				$record["DEPE_CODI"]      = $rs->fields["DEPE_CODI"];
				$record["USUA_CODI"]      = $rs->fields["USUA_CODI"];
				$record["USUA_DOC"]       = $rs->fields["USUA_DOC"];
				$record["SGD_MRD_CODIGO"] = $rsTRD->fields["SGD_MRD_CODIGO"];
				$record["SGD_RDF_FECH"]   = $db->OffsetDate(0,$db->sysTimeStamp);
				# Mandar como parametro el recordset vacio y el arreglo conteniendo los datos a insertar
				# a la funcion GetInsertSQL. Esta procesara los datos y regresara un enunciado SQL
				# para procesar el INSERT.
				$insertSQL = $db->Replace("SGD_RDF_RETDOCF", $record, "true");

	
				$recordR =array();
				$recordR["RADI_NUME_RADI"]	   =  $nroradicado;
				$recordR["TDOC_CODI"]	       =  $tipodoc;

				$insertSQL = $db->Replace("RADICADO", $recordR, "RADI_NUME_RADI", false);
			} else return 0;
	}
	return 0;
}


function generarimpreso($username, $password, $nroradicado) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		$recordA =array();
		$recordA["RADI_NUME_SALIDA"]   =  $nroradicado;
		$recordA["ANEX_ESTADO"]	       =  3;
		$recordA["SGD_FECH_IMPRES"]	   =  $db->OffsetDate(0,$db->sysTimeStamp);
		$recordA["ANEX_FECH_ENVIO"]	   =  $db->OffsetDate(0,$db->sysTimeStamp);

		$insertSQL = $db->Replace("ANEXOS", $recordA, "RADI_NUME_SALIDA", false);
	}
	return 0;
}


function grupos($username, $password, $idpto, $idmpio) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		
		 if ($idpto!=1 )  $sqlWhere.= "DPTO_CODI = ".$idpto." and ";
			//Si se desea realizar la búsqueda por municipio
		 if ($idmpio!=999)	$sqlWhere.= "MUNI_CODI = ".$idmpio." and ";
		$sqlWhere.="1=1 ";	
	    $query= "SELECT DEPE_CODI, DEPE_NOMB  FROM DEPENDENCIA
	  	       WHERE $sqlWhere  ORDER BY DEPE_CODI ";

	  	$rsdepe = $db->Execute($query);
		$result= array();
		while (!$rsdepe->EOF) {
			$result[] = array('id'=> $rsdepe->fields["DEPE_CODI"],
			'detalle'=> $rsdepe->fields["DEPE_NOMB"]);
			$rsdepe->MoveNext();
		}

        return $result;

	}
	return 0;
}



function enviosplanilla($username, $password, $codplanilla, $fechaplanilla, $zona) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.14', 'fldoc', 'accion', 'red_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		
		// Si la planilla 
		 if ($zona=='D.C.') {
			 $sqlWhere.= " SGD_RENV_DEPTO ='D.C.' AND ";
		 } else  $sqlWhere.= " SGD_RENV_DEPTO <> 'D.C.' AND ";

		$query = "SELECT SGD_RENV_CANTIDAD  Item, RADI_NUME_SAL Radicado, SGD_RENV_NOMBRE Destinatario, SGD_RENV_MPIO Municipio, SGD_RENV_DEPTO Depto,  
					SGD_RENV_PESO Peso, SGD_RENV_VALOR Valor , DEPENDENCIA.DEPE_NOMB Remitente
                    From SGD_RENV_REGENVIO INNER JOIN DEPENDENCIA on (SGD_RENV_REGENVIO.SGD_DEPE_GENERA = DEPENDENCIA.DEPE_CODI)
                    Where $sqlWhere  SGD_FENV_CODIGO = $codplanilla and TO_CHAR(SGD_RENV_FECH,'YYYY-MM-DD') = '$fechaplanilla' ";
          
	  	$rsenvios = $db->Execute($query);
		$result= array();
		while (!$rsenvios->EOF) {
			$result[] = array('Item'=> $rsenvios->fields["ITEM"],
							  'Radicado'=> $rsenvios->fields["RADICADO"],
							  'Destinatario'=> $rsenvios->fields["DESTINATARIO"],
							  'Municipio'=> $rsenvios->fields["MUNICIPIO"],
							  'Remitente'=> $rsenvios->fields["REMITENTE"],
							  'Depto'=> $rsenvios->fields["DEPTO"],
							  'Peso'=> $rsenvios->fields["PESO"],
						      'Valor'=> $rsenvios->fields["VALOR"]);
			$rsenvios->MoveNext();
		}

        return $result;

	}
	return 0;
}




function chequearadicado($username, $password, $nroradicado, $nroidentificacion, $fechaini, $fechafin) {

	$db = ADONewConnection('oci8'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect('172.20.2.6', 'orfeopru', 'orfeopru', 'acs_orcl');

	$query= "select * from usuario WHERE  usua_login='".$username."' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		
		if ($nroradicado!='' )  $sqlWhere.= "a.RADI_NUME_RADI like '%$nroradicado' and ";
		if ($nroidentificacion!='' )  $sqlWhere.= "a.RADI_NUME_IDEN like '%$nroidentificacion' and ";
		$sqlWhere.="1=1 ";

		$query = "SELECT   a.RADI_NUME_RADI  , a.RA_ASUN,  TO_CHAR(a.RADI_FECH_RADI,'DD-MM-YYYY HH24:MI') AS FECHA , b.SGD_TPR_DESCRIP , b.SGD_TPR_CODIGO , b.SGD_TPR_TERMINO, RADI_TIPO_DERI , RADI_NUME_DERI , d.SGD_DIR_NOMREMDES, d.SGD_DIR_TIPO, d.SGD_DIR_NOMBRE, a.RADI_CUENTAI, g.SGD_EXP_NUMERO 
			FROM RADICADO a, SGD_TPR_TPDCUMENTO b, SGD_DIR_DRECCIONES d, SGD_EXP_EXPEDIENTE g 
			WHERE a.RADI_NUME_RADI =d.RADI_NUME_RADI AND a.RADI_NUME_RADI =g.RADI_NUME_RADI (+) AND a.TDOC_CODI=b.SGD_TPR_CODIGO AND d.SGD_TRD_CODIGO IN (1,2,3,4) and
			$sqlWhere and (a.RADI_FECH_RADI >= TO_DATE('$fechaini','DD-MM-YYYY') and a.RADI_FECH_RADI <= TO_DATE('$fechafin','DD-MM-YYYY')) and rownum <= 200 order by RADI_FECH_RADI desc ";
		
          
	  	$rsradicados = $db->Execute($query);
		$result= array();
		while (!$rsradicados->EOF) {
			$result[] = array('Fecha'=> $rsradicados->fields["FECHA"],
							  'radi_nume_radi'=> $rsradicados->fields["RADI_NUME_RADI"],
							  'ra_asun'=> $rsradicados->fields["RA_ASUN"],
							  'sgd_tpr_descrip'=> $rsradicados->fields["SGD_TPR_DESCRIP"],
							  'sgd_tpr_codigo'=> $rsradicados->fields["SGD_TPR_CODIGO"],
							  'sgd_tpr_termino'=> $rsradicados->fields["SGD_TPR_TERMINO"],
							  'radi_tipo_deri'=> $rsradicados->fields["RADI_TIPO_DERI"],
						      'radi_nume_deri'=> $rsradicados->fields["RADI_NUME_DERI"],
						      'sgd_dir_nomremdes'=> $rsradicados->fields["SGD_DIR_NOMREMDES"],
						      'sgd_dir_tipo'=> $rsradicados->fields["SGD_DIR_TIPO"],
						      'sgd_dir_nombre'=> $rsradicados->fields["SGD_DIR_NOMBRE"],
						      'radi_cuentai'=> $rsradicados->fields["RADI_CUENTAI"],
						      'sgd_exp_numero'=> $rsradicados->fields["SGD_EXP_NUMERO"]);
			$rsradicados->MoveNext();
		}

        return $result;

	}
	return 0;
}


// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);


?>
