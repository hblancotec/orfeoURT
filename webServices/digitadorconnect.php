<?php

$t_current_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
$t_nusoap_dir  = $t_current_dir . 'lib/';

# includes nusoap classes
chdir( $t_nusoap_dir );
require_once 'nusoap.php';
chdir( $t_current_dir );

//Parametros para configuracion servidor
define('SERVIDOR_DB', '172.16.1.120');
define('USUARIO_DB',  'orfeo');
define('PASSW_DB',    'orfeoDNP');
define('NOMBRE_DB',   'GdOrfeo');

include 'adodb/adodb.inc.php';


// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$t_namespace = "webServices/noap";

$server->debug_flag = false;
$server->configureWSDL('orfeo metodos para digitalizador', $t_namespace);
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


//*************************************************************************************************************
// DEFINICIO DE FUNCIONES
//*************************************************************************************************************

// Define the method as a PHP function
// Funcion para el ingreso al sistema 
function login($username, $password ) {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

	$query= "select * from usuario WHERE  usua_login='$username' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		return array('UsuarioNombre'=>$rs->fields["USUA_NOMB"],
							 'UsuarioLogin'=>$rs->fields["USUA_LOGIN"], 
				             'DocIdent'=>$rs->fields["USUA_DOC"],
     					     'UsaCodigo'=>$rs->fields["USUA_CODI"],
  					         'DepCodigo'=>$rs->fields["DEPE_CODI"],
 	        				 'PerRad'=>$rs->fields["PERM_RADI"]);
   	}    else return array();
}

// Metodo para la recuperacion del usuario por medio del login
function usuario($username ) {
	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query= "select * from usuario WHERE  usua_login='$username'";
   $rs = $db->Execute($query);
   if ($rs->RecordCount( ) > 0) {
			return array('UsuarioNombre'=>$rs->fields["USUA_NOMB"],
							 'UsuarioLogin'=>$rs->fields["USUA_LOGIN"], 
				             'DocIdent'=>$rs->fields["USUA_DOC"],
     					     'UsaCodigo'=>$rs->fields["USUA_CODI"],
  					         'DepCodigo'=>$rs->fields["DEPE_CODI"],
 	        				 'PerRad'=>$rs->fields["PERM_RADI"]);
    }  else return array();

}

// Define the method as a PHP function
function radicados_usuario($username, $inifecha, $finfecha, $criterio ) {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

	$query= "select USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_DOC, USUA_ESTA, PERM_RADI from usuario WHERE  usua_login='$username'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		$usuaEstado = $rs->fields['USUA_ESTA']; // El usuario esta activo ?
		$permRadicado = $rs->fields['PERM_RADI']; // Tiene pemiso de radicación ?
		$usuaDependencia = $rs->fields['DEPE_CODI']; // Codigo de la dependencia del usuario digitalizador ?
		$usuaCodigo = $rs->fields['USUA_CODI']; // Codigo del usuario digitalizador ?

	    if (($usuaEstado==1) && ($permRadicado==1)){

	         switch ($criterio{0})
	              {   case 'U': {
	              	         $tiporad = $criterio{1} ; 
			                 $sqlWhere = " r.RADI_NUME_RADI like '%$tiporad' AND 
							               substr(r.RADI_NUME_RADI,5,3) = $usuaDependencia AND 
										   r.RADI_USUA_RADI = $usuaCodigo AND 
										   r.RADI_PATH IS NULL  ";
			                 } break;
					  case 'N': {
						     $tiporad = $criterio{1} ; 
					  	     $nroradicado = substr($criterio, 2); 
							 if ($tiporad==0) {
				 				 $sqlWhere = " RADI_NUME_RADI like '%$nroradicado' ";
                             } else  $sqlWhere = " RADI_NUME_RADI like '%$nroradicado' ";
					  } break ;
					  case 'D': {
						     $tiporad = $criterio{1} ; 
					  	     $coddep = substr($criterio, 2); 
							 $sqlWhere = " RADI_NUME_RADI like '%$tiporad' AND substr(r.RADI_NUME_RADI,5,3) = $coddep  ";
                	  } break ;
				   };

		   $query= "SELECT r.RADI_NUME_RADI, to_char(r.RADI_FECH_RADI, 'DD-MM-YYYY HH12:MI:SS AM') as FECHA, r.RADI_DEPE_ACTU, r.RA_ASUN, r.RADI_NUME_HOJA, d.DEPE_NOMB
			        FROM radicado r, dependencia d
					WHERE  r.RADI_DEPE_ACTU =  d.DEPE_CODI AND
                           TRUNC(r.RADI_FECH_RADI) between  TO_DATE('$inifecha','dd/mm/yyyy') and  TO_DATE('$finfecha','dd/mm/yyyy') AND 
						   $sqlWhere  AND rownum <= 200 ORDER BY r.RADI_NUME_RADI ";
			$rs = $db->Execute($query);
			$result= array();
			while (!$rs->EOF) {
			   $result[] = array('nroradicado'     =>$rs->fields["RADI_NUME_RADI"],
 							             'fechradicado'    =>$rs->fields["FECHA"], 
				                   'depradicado'     =>$rs->fields["RADI_DEPE_ACTU"],
   				                 'depnombradicado' =>$rs->fields["DEPE_NOMB"],
     					             'asunradicado'    =>$rs->fields["RA_ASUN"],
  					               'nrohojasradicado'=>$rs->fields["RADI_NUME_HOJA"]);
			   $rs->MoveNext();
 		    }
			return $result;
	   }    
       else return array();
  }
  else return array();
}



// Define the method as a PHP function para la actualizacion del digitalizador
function registrar($pathimagen, $nropaginas, $nroradicado, $usudigitalizador ) {

	//Verifico que el archivo (imagen) se encuentre en el servidor
	 $rutImagen = "../bodega/".$pathimagen;
	if (!file_exists($rutImagen)) {
        return array('error'=> '00',
			 'mensaje'=> 'No se encuentra imagen del radicado en el servidor.. debe volver a subir la imagen');
	} 


	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    

	// Actualiza en la tabla de radicados el numero de hojas y la ubicacion del la imagen
	// Se arma el arreglo para la actualizacion del radicado de anexo path
	$recordR =array();
	
    $recordR["RADI_NUME_RADI"]	   =  $nroradicado;
	$recordR["RADI_PATH"]	       ="'$pathimagen'";
	$recordR["RADI_NUME_HOJA"]	   =  $nropaginas;
		
  $insertSQL = $db->Replace("RADICADO", $recordR, "RADI_NUME_RADI", false);
		//Se comprueba que la actualizacion fue exitosa
  If(!$insertSQL)	{
        return array('error'=> '01',
					 'mensaje'=> 'No se actualizo la tabla de radicados ') ;
  }		
		
	// Recuperamos informacion del digitalizador para registralo en la tabla de historico
	$query= "select USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_DOC from usuario WHERE  usua_login='$usudigitalizador'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		 $usuaDependencia = $rs->fields['DEPE_CODI']; // Codigo de la dependencia del usuario digitalizador ?
		 $usuaCodigo      = $rs->fields['USUA_CODI']; // Codigo del usuario digitalizador ?
         $usuadoc         = $rs->fields['USUA_DOC']; // Documento del digitalizador ?
     // LLenamos el arreglo para registrar el evento   
		 $recordH =array();
		 $recordH["DEPE_CODI"]	    = $usuaDependencia;
		 $recordH["HIST_FECH"]	    = $db->OffsetDate(0,$db->sysTimeStamp); //Valor calculado
		 $recordH["USUA_CODI"]	    = $usuaCodigo;
		 $recordH["RADI_NUME_RADI"] = $nroradicado;
		 $recordH["HIST_OBSE"]      = "'Se digitalizaron ".$nropaginas." Página(s) del documento'" ;
		 $recordH["USUA_CODI_DEST"] = $usuaCodigo;
		 $recordH["USUA_DOC"]       = $usuadoc;
		 $recordH["SGD_TTR_CODIGO"] = 22;  // TRD Codigo de digitalización
		 $recordH["HIST_DOC_DEST"]  = $usuadoc;
		 $recordH["DEPE_CODI_DEST"] = $usuaDependencia;

		 $insertSQL = $db->Replace("HIST_EVENTOS", $recordH, "", false);
		 // se comprueba que la insercion fue exitosa
		If(!$insertSQL) { 
             return array('error'=> '02',
				 	 'mensaje'=> 'No se registro el evento en la tabla de historicos') ;
      }
        return array('error'=> 'OK',
					 'mensaje'=> 'La operacion fue exitosa');
   }
   return array('error'=> '03',
  				 'mensaje'=> 'No se encontro informacion del digitalizador');
           
}


// Recupera las series para una depedencia
function series($username, $password, $codidepe) {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	
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

// Recupera las subseries para una  serie y depedencia
function subseries($username, $password, $codidepe, $codiserie) {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

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

// Recupera los tipos de documentos de uns serie,subseries para una  depedencia
function tipodocumentos($username, $password, $codidepe, $codiserie, $codisubserie) {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	
  /*	$query= "SELECT DISTINCT t.SGD_TPR_DESCRIP AS DETALLE, t.SGD_TPR_CODIGO 
				FROM SGD_MRD_MATRIRD m, SGD_TPR_TPDCUMENTO t 
				WHERE m.DEPE_CODI =  $codidepe AND m.SGD_SRD_CODIGO = $codiserie AND m.SGD_SBRD_CODIGO = $codisubserie 
					AND t.SGD_TPR_CODIGO = m.SGD_TPR_CODIGO AND t.SGD_TPR_TP1='1' ORDER BY SGD_TPR_CODIGO  ";*/

       $query =  "SELECT DISTINCT t.SGD_TPR_DESCRIP AS DETALLE, t.SGD_TPR_CODIGO 
	   			  FROM  SGD_TPR_TPDCUMENTO t Order by DETALLE " ;

	  	$rstipdoc = $db->Execute($query);
		$result= array();
		while (!$rstipdoc->EOF) {
			$result[] = array('id'=> $rstipdoc->fields["SGD_TPR_CODIGO"],
			'detalle'=> $rstipdoc->fields["DETALLE"]);
			$rstipdoc->MoveNext();
		}

        return $result;
}

//Tipifica el nro de radicado
function tipificar($username, $nroradicado, $codiserie, $codisubserie, $tipodoc) {

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	
	$query= "select * from usuario WHERE  usua_login='$username'";
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
				If(!$insertSQL) { 
 				    return array('error'=> 'OK',
					             'mensaje'=> 'La operacion fue exitosa');
				}
			} else return 0;
	}
	return 0;
}

	
function anexararchivo( $username, $nroradicado, $anextipo, $tamano, $solectura, $codTrd, $anexdesc )
{

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

	$query= "select * from usuario WHERE  usua_login='$username'";
	$rs = $db->Execute($query);
	if ($rs->RecordCount( ) > 0) {
		// Se genera el nro de documentos que se han anexado al un radicado
		$query = 'SELECT count(*) as "CANTRADICADOS" FROM anexos WHERE anex_radi_nume =' .$nroradicado;
		$rscant = $db->Execute($query);
		$numanex = $rscant->fields["CANTRADICADOS"] + 1;
	
	   // Se construe el valor de anex_codigo
		$anexcodigo = $nroradicado .str_pad($numanex,5,"0", STR_PAD_LEFT); 
		$nombarchivo = $nroradicado."_".str_pad($numanex,5,"0", STR_PAD_LEFT).".tif" ;
		// Se arma el arreglo para la inclusion del anexo
		$recordA =array();
		$recordA["SGD_TPR_CODIGO"]     =  $codTrd;
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
//		$recordA["ANEX_RADI_FECH"]     =  $db->OffsetDate(0,$db->sysTimeStamp);
		$recordA["ANEX_FECH_ANEX"]     =  $db->OffsetDate(0,$db->sysTimeStamp);
		$recordA["ANEX_ESTADO"]		   =  1;
		$recordA["ANEX_DEPE_CREADOR"]  =  $rs->fields["DEPE_CODI"];
		$recordA["SGD_DIR_TIPO"]	   =  1;

		$insertSQL = $db->Replace("ANEXOS", $recordA, "", false);

		If($insertSQL) {
 		    return array('error'=> 'OK',
					             'mensaje'=> 'La operacion fue exitosa');
		} Else  return array('error'=> '01',
					             'mensaje'=> 'No se registro el anexo');

	} else   return array();
}

// Genera el nombre del archivo para el anexo
function nombreanexo ($nroradicado)
{

	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	// 
	$query = 'SELECT count(*) as "CANTRADICADOS" FROM anexos WHERE anex_radi_nume =' .$nroradicado;
	$rscant = $db->Execute($query);
	if ($rscant->RecordCount( ) > 0) {
		$numanex = $rscant->fields["CANTRADICADOS"] + 1;
	   // Se construe el valor de anex_codigo
	    $anexcodigo = $nroradicado .str_pad($numanex,5,"0", STR_PAD_LEFT); 
    	$nombarchivo = $nroradicado."_".str_pad($numanex,5,"0", STR_PAD_LEFT).".tif" ;
         return array('error'=> 'OK',
					            'mensaje'=> $nombarchivo);
    } else     return array('error'=> '01',
					            'mensaje'=> ' No se genero el nombre del archivo');
}

function noty_prestamos($nroradicado,$usuanotifica) {
	$db = ADONewConnection('mssqlnative'); # ej. 'mysql' o 'oci8' 
	//$db->debug = true;
	$db->Connect(SERVIDOR_DB,USUARIO_DB, PASSW_DB, NOMBRE_DB);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
	// 
	
   $query = "select t.radi_nume_radi,
             usuario.usua_nomb,
	         usuario.usua_email,
		     radicado.radi_fech_radi,
	         radicado.ra_asun,
		     radicado.radi_path
		  from prestamo t,
		       usuario,
		       radicado
	 where usuario.usua_login = t.usua_login_actu and 
		    t.radi_nume_radi = radicado.radi_nume_radi and
			t.radi_nume_radi = $nroradicado " ;
       
	$rs = $db->Execute($query);

	if ($rs->RecordCount( ) > 0) {

	// Recuperamos informacion del usuario que notifica para registralo en la tabla de historico
	$query= "select USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_DOC from usuario WHERE  usua_login='$usuanotifica'";
	$rshis = $db->Execute($query);
	if ($rshis->RecordCount( ) > 0) {
		 $usuaDependencia = $rshis->fields['DEPE_CODI']; // Codigo de la dependencia del usuario digitalizador ?
		 $usuaCodigo      = $rshis->fields['USUA_CODI']; // Codigo del usuario digitalizador ?
         $usuadoc         = $rshis->fields['USUA_DOC']; // Documento del digitalizador ?
     // LLenamos el arreglo para registrar el evento   

          $usunombre =  $rs->fields["USUA_NOMB"];

		 $recordH =array();
		 $recordH["DEPE_CODI"]	    = $usuaDependencia;
		 $recordH["HIST_FECH"]	    = $db->OffsetDate(0,$db->sysTimeStamp); //Valor calculado
		 $recordH["USUA_CODI"]	    = $usuaCodigo;
		 $recordH["RADI_NUME_RADI"] = $nroradicado;
		 $recordH["HIST_OBSE"]      = "'Se notifico al usuario : $usunombre '" ;
		 $recordH["USUA_CODI_DEST"] = $usuaCodigo;
		 $recordH["USUA_DOC"]       = $usuadoc;
		 $recordH["SGD_TTR_CODIGO"] = 50;  // TTR Cidogo de notificacion por correo
		 $recordH["HIST_DOC_DEST"]  = $usuadoc;
		 $recordH["DEPE_CODI_DEST"] = $usuaDependencia;

		 $insertSQL = $db->Replace("HIST_EVENTOS", $recordH, "", false);
		 // se comprueba que la insercion fue exitosa
		If(!$insertSQL) { 
             return array('error'=> '02',
			 	      'mensaje'=> 'No se registro el evento en la tabla de historicos') ;
      }
    	  
		  return array('error' =>'OK',
               	 'mensaje' => 'Operacion Exitosa',
			           'nroradi' =>$rs->fields["RADI_NUME_RADI"],  
			           'usunom'  => $usunombre, 
	               'usuemail'=>$rs->fields["USUA_EMAIL"],
					       'fecrad'  =>$rs->fields["RADI_FECH_RADI"],
                 'radiasun'=>$rs->fields["RA_ASUN"],
                 'radipath'=>$rs->fields["RADI_PATH"]);
		   
     }
        return array('error'=> '03',
  				   'mensaje'=> 'No se encontro informacion del notificador');

  }
      return array('error'=> '04',
  				   'mensaje'=> 'El radicado puede no estar en solicitud de prestamo para su envio de correo ');

}

// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);


?>
