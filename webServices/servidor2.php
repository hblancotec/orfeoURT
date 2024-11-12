<?php
/**********************************************************************************
Diseno de un Web Service que permita la interconexion de aplicaciones con Orfeo
**********************************************************************************/

/**
 * @author German Mahecha
 * @author Aquiles Canto (modificacion del archivo original y adicion de funcionalidad)
 * @author Donaldo Jinete Forero
 */

//Llamado a la clase nusoap

$ruta_raiz = "../";
define('RUTA_RAIZ','../');

require_once "nusoap/lib/nusoap.php";
include_once RUTA_RAIZ."include/db/ConnectionHandler.php";
//require_once RUTA_RAIZ."flujo/vistaFlujo.php";
//require_once RUTA_RAIZ."flujo/variables/flujo.php";

//Asignacion del namespace  
$ns="webServiceOrfeo";

//Creacion del objeto soap_server
$server = new soap_server();

$server->configureWSDL('Sistema de Gestion Documental Orfeo-Internas',$ns);

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
$server->register('getUsuarioCorreo',
	array(
	'correo'=> 'xsd:string'
	),
	array('return'=>'tns:Vector'),
	$ns
);
$server->register('crearAnexo',  								//nombre del servicio                 
    array('radiNume' => 'xsd:string',									//numero de radicado	
     'file' => 'xsd:base64binary',										//archivo en base 64
     'filename' => 'xsd:string',										//nombre original del archivo
     'correo' => 'xsd:string',									       //correo electronico
     'descripcion'=>'xsd:string',										//descripcion del anexo
     ),																//fin parametros del servicio        	
    array('return' => 'xsd:string'),   								//retorno del servicio
    $ns                     									 	//Elemento namespace para el metod       
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

$server->register('solicitarAnulacion',
	array(
		'radiNume'=>'xsd:string',
		'descripcion'=>'xsd:string'
	),
	array(
		'return'=>'tns:string'
	),
	$ns
);

// Servicio que realiza una radicacion en Orfeo
$server->register('radicarDocumento',
	array(
		'file' => 'xsd:base64binary',										//archivo en base 64
     	'fileName' => 'xsd:string',
     	'correo' => 'xsd:string',	
		'destinatario'=>'tns:Destinatario',
		'predio'=>'tns:Destinatario',
		'esp'=>'tns:Destinatario',
		'asu'=>'xsd:string',
		'med'=>'xsd:string',
		'ane'=>'xsd:string',
		'coddepe'=>'xsd:string',
		'tpRadicado'=>'xsd:string',
		'cuentai'=>'xsd:string',
		'radi_usua_actu'=>'xsd:string',
		'tip_rem'=>'xsd:string',
		'tdoc'=>'xsd:string',
		'tip_doc'=>'xsd:string',
		'carp_codi'=>'xsd:string',
		'carp_per'=>'xsd:string',
		'usuaLogin'=>'xsd:string'
	),
	array(
		'return' => 'xsd:string'
	),
	$ns,
	$ns."#radicarDocumento",
	'rpc',
	'encoded',
	'Radicacion de un documento en Orfeo'
);


//Servicio para anular radicacion de Orfeo
$server->register('anularRadicado',
	array(
		'checkValue'=>'tns:Vector',
		'dependencia'=>'xsd:string',
		'usua_doc'=>'xsd:string',
		'observa'=>'xsd:string',
		'codusuario'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'),
	$ns,
	$ns."#anularRadicado",
	'rpc',
	'encoded',
	'Anular radicacion de un documento en Orfeo'
);


$server->register('anexarExpediente',
	array(
		'numRadicado'=>'xsd:string',
		'numExpediente'=>'xsd:string',
		'usuaLogin'=>'xsd:string',
		'observa'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns."#anexarExpediente",
	'rpc',
	'encoded',
	'Anexar un radicado a un expediente'	
);

// Modificado SSPD 28-Noviembre-2008
// Se agregó 'hist'=>'xsd:string'
$server->register('cambiarImagenRad',
	array(
		'numRadicado'=>'xsd:string',
		'ext'=>'xsd:string',
		'file'=>'xsd:base64binary',
		'hist'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns."#cambiarImagenRad",
	'rpc',
	'encoded',
	'Cambiar imagen a un radicado'
);


$server->register('getInfoUsuario',
	array(
		'usuaLoginMail'=>'xsd:string'
	),
	array(
		'return'=>'tns:Vector'
	),
	$ns,
	$ns.'#getInfoUsuario',
	'rpc',
	'encoded',
	'Obtener informacion de un usuario a partir del correo electronico'
);

$server->register('asociarObjetoFlujo',
	array(
		'nuRad'=>'xsd:string',
		'usuaEmail'=>'xsd:string',
		'tflujo'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns.'#asociarObjetoFlujo',
	'rpc',
	'encoded',
	'Asociar un objeto a un flujo'
);
$server->register('cambioDeEtapaFlujo',
	array(
		'objDoc'=>'xsd:string',
		'flujo'=>'xsd:string',
		'etapa'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns.'#cambioDeEtapaFlujo',
	'rpc',
	'encoded',
	'Cambio de etapa en un flujo'
);
$server->register('informacionJefe',
	array(
		'usuaEmail'=>'xsd:string'
	),
	array(
		'return'=>'tns:Vector'
	),
	$ns,
	$ns.'#informacionJefe',
	'rpc',
	'encoded',
	'Informacion del Jefe de un usuario'
);


//registro de servicios nuevos
include_once "validarRadicado/regServicios.php";
include_once "devolucion/regServicios.php";
// Modificado SSPD 01-Diciembre-2008
// Registro del servicio Web notificar
include_once "notificar/regServicios.php";
include_once "reasignarRadicado/regServicios.php";
// Modificado SSPD 16-Octubre-2008
// Registro de los servicios tipificarDocumento, isDocumentoTipificado,
// anexoRadicadoToRadicado
include_once( "tipificar/regServicios.php" );
include_once( "anexo/regServicios.php" );
//Modificacion 20  octubre servicio modificarRadicado
include_once "radicado/regServicios.php";
//Fin 20  octubre
//Modificacion 13 feb 2009 servicio cambiar imagen2
//include_once "cambiarImagen/regServicios.php";
//Fin 13 feb 2009
/**********************************************************************************
Se registran los tipos complejos y/o estructuras de datos
***********************************************************************************/

//Tipo complejo destinatario
$server->wsdl->addComplexType(
	'Destinatario',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'documento' => array('name' => 'documento','type' => 'xsd:string'),
		'cc_documento' => array('name' => 'cc_documento','type' => 'xsd:string'),
		'tipo_emp' => array('name' => 'tipo_emp','type' => 'xsd:string'),
		'nombre' => array('name' => 'nombre','type' => 'xsd:string'),
		'prim_apel' => array('name' => 'prim_apell','type' => 'xsd:string'),
		'seg_apel' => array('name' => 'seg_apell','type' => 'xsd:string'),
		'telefono' => array('name' => 'telefono','type'=>'xsd:string'),
		'direccion' => array('name' => 'direccion','type' => 'xsd:string'),
		'mail' => array('name' => 'mail','type'=>'xsd:string'),
		'otro' => array('name' => 'mail','type'=>'xsd:string'),
		'idcont' => array('name' => 'idcont','type'=>'xsd:string'),
		'idpais' => array('name' => 'idpais','type'=>'xsd:string'),
		'codep' => array('name' => 'codep','type'=>'xsd:string'),
		'muni' => array('name' => 'muni','type' => 'xsd:string')
		)
);

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
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
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
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($usuaEmail != ''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where UPPER(USUA_EMAIL) = UPPER('$usuaEmail')";
	}elseif ($usuaDoc !=''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where USUA_DOC = $usuaDoc";
	}else {
		return "Favor proveer datos";
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
    //$var = explode(".",$filename);
	//try{
		//direccion donde se quiere guardar los archivos
		$path = getPath($filename);
		if(!$fp = fopen("$path", "w")){
			die("fallo");
		}
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
	$path = RUTA_RAIZ."bodega/";
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
		
	include_once(RUTA_RAIZ."include/tx/Expediente.php");
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
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
/**
 * funcion que rescata los valores de un usuario de orfeo 
 * a partir del correo electonico
 *
 * @param string $correo mail del usuario en orfeo
 * @return array resultado de la consulta;
 */
function getUsuarioCorreo($correo){
	global $ruta_raiz;
	$consulta="SELECT USUA_LOGIN,DEPE_CODI,USUA_EMAIL,CODI_NIVEL,USUA_CODI,USUA_DOC
	           FROM USUARIO WHERE USUA_EMAIL='$correo' AND USUA_ESTA=1";
	$salida=array();
	if(verificarCorreo($correo)){
	 $db = new ConnectionHandler($ruta_raiz,'WS');
	 // Modificado SSPD 01-Diciembre-2008
	// Cambie USUA_EMAIL='".trim($correo)."' por ".$db->conn->upperCase."( USUA_EMAIL )='".trim( strtoupper( $correo ) )."'
	 $consulta="SELECT USUA_LOGIN,DEPE_CODI,USUA_EMAIL,CODI_NIVEL,USUA_CODI,USUA_DOC
	           FROM USUARIO WHERE ".$db->conn->upperCase."( USUA_EMAIL )='".trim( strtoupper( $correo ) )."' AND USUA_ESTA=1";
	 $rs = $db->conn->Execute($consulta);
	 
	 if (!$rs->EOF){
		 $salida['email'] = $rs->fields['USUA_EMAIL'];
		 $salida['codusuario']  = $rs->fields['USUA_CODI'];
		 $salida['dependencia'] = $rs->fields['DEPE_CODI'];
		 $salida['documento'] =  $rs->fields['USUA_DOC'];
		 $salida['nivel'] = $rs->fields['CODI_NIVEL'];
		 $salida['login'] = $rs->fields['USUA_LOGIN'];
	   } else {
	   	$salida['error']="El ususario no existe o se encuentra deshabilitado";
	   }
	}else{
		$salida["error"]="el mail no corresponde a un email valido";
	}
	
	return $salida;
}
/**
 * funcion que verifica que un correo electronico cumpla con 
 * un patron estandar
 *
 * @param strig $correo correo a verificar
 * @return boolean
 */
function verificarCorreo($correo){
	 $expresion=preg_match("(^\w+([\.-] ?\w+)*@\w+([\.-]?\w+)*(\.\w+)+)",$correo);
	 return $expresion;
}
/**
 * funcion encargada regenerar un archivo enviado en base64
 *
 * @param string $ruta ruta donde se almacenara el archivo 
 * @param base64 $archivo archivo codificado en base64
 * @param string $nombre nombre del archivo
 * @return boolean retorna si se pudo decodificar el archivo
 */
function subirArchivo($ruta,$archivo,$nombre){
		//try{
		//direccion donde se quiere guardar los archivos
		$fp = @fopen("{$ruta}{$nombre}", "w");
		$bytes=base64_decode($archivo);

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
	return $salida;		
}
/**
 * funcion que crea un Anexo, y ademas decodifica el anexo enviasdo en base 64
 *
 * @param string  $radiNume numero del radicado al cual se adiciona el anexo
 * @param base64 $file archivo codificado en base64
 * @param string $filename nombre original del anexo, con extension
 * @param string $correo correo electronico del usuario que adiciona el anexo
 * @param string $descripcion descripcion del anexo
 * @return string mensaje de error en caso de fallo o el numero del anexo en caso de exito
 */
function crearAnexo($radiNume,$file,$filename,$correo,$descripcion){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	$usuario=getUsuarioCorreo($correo);
	$error=(isset($usuario['error']))?true:false;
	$ruta=RUTA_RAIZ."bodega/".substr($radiNume,0,4)."/".substr($radiNume,4,3)."/docs/";
	$numAnexos=numeroAnexos($radiNume,$db)+1;
	$maxAnexos=maxRadicados($radiNume,$db)+1;
	$extension=substr($filename,strrpos($filename,".")+1);	
	$numAnexo=($numAnexos > $maxAnexos)?$numAnexos:$maxAnexos;
	$nombreAnexo=$radiNume.substr("00000".$numAnexo,-5);
	$subirArchivo=subirArchivo($ruta,$file,$nombreAnexo.".".$extension);
	$tamanoAnexo = $subirArchivo / 1024; //tamano en kilobytes
	$error=($error && !$subirArchivo)?true:false;
	$fechaAnexado= $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	$tipoAnexo=tipoAnexo($extension,$db);
	if(!$error){
		$tipoAnexo=($tipoAnexo)?$tipoAnexo:"NULL";
		$consulta="INSERT INTO ANEXOS (ANEX_CODIGO,ANEX_RADI_NUME,ANEX_TIPO,ANEX_TAMANO,ANEX_SOLO_LECT,ANEX_CREADOR,
		            ANEX_DESC,ANEX_NUMERO,ANEX_NOMB_ARCHIVO,ANEX_ESTADO,SGD_REM_DESTINO,ANEX_FECH_ANEX, ANEX_BORRADO) 
		            VALUES('$nombreAnexo',$radiNume,$tipoAnexo,$tamanoAnexo,'n','".$usuario['login']."','$descripcion'
		            ,$numAnexo,'$nombreAnexo.$extension',0,1,$fechaAnexado, 'N')";

		
		$error=$db->conn->Execute($consulta);
		
		$consultaVerificacion = "SELECT ANEX_CODIGO FROM ANEXOS WHERE ANEX_CODIGO = '$nombreAnexo'";
		$rs=$db->conn->Execute($consultaVerificacion);
		$cod = $rs->fields['ANEX_CODIGO'];
	}
	return $cod ? 'Anexo Creado' : 'Error en la adicion verifique: ' . $nombreAnexo;
}
/**
 * funcion que calculcula el numero de anexos que tiene un radicado
 *
 * @param int  $radiNume radicado al cual se realiza se adiciona el anexo
 * @param ConectionHandler $db
 * @return int numero de anexos del radicado
 */
function numeroAnexos($radiNume,$db){
	$consulta="SELECT COUNT(1) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume}";
	$salida=0;	
	$rs=& $db->conn->Execute($consulta);
		if($rs && !$rs->EOF)
			$salida=$rs->fields['NUM_ANEX'];
		return  $salida;	
}
/**
 * funcioncion que rescata el maxido del anexo de los radicados 
 *
 * @param int $radiNume numero del radicado
 * @param ConnectionHandler $db conexion con la db
 * @return int maximo
 */
function maxRadicados($radiNume,$db){
	$consulta="SELECT max(ANEX_NUMERO) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume}";
		$rs=& $db->conn->Execute($consulta);
		if($rs && !$rs->EOF)
			$salida=$rs->fields['NUM_ANEX'];
		return  $salida;	
}
/**
 * funcion que consulta el tipo de anexo que se esta generando
 * 
 *
 * @param sting $extension extencion del archivo
 * @param ConnectionHandler $db conexion con la DB
 * @return int
 */
function tipoAnexo($extension,$db){
	$consulta="SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT='".strtolower($extension)."'";
	$salida=null;
	$rs=& $db->conn->Execute($consulta);
		if($rs && !$rs->EOF)
			$salida=$rs->fields['ANEX_TIPO_CODI'];
	return $salida;		
}
/**
 * funcion que genera la solicitud de anulacion de un numero de radicado
 * de forma automatica
 *
 * @param string $radiNume numero de radicado
 * @param string $descripcion causa por la cula se solicita la anulacion
 * @return string en caso de fallo retorna error 
 */
function solicitarAnulacion( $radiNume, $descripcion, $correo ){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	//Se traen los datos del usuario que solicita anulacion
	$usuario=getUsuarioCorreo( $correo );
	
	$verificacionSolicitud = verificaSolAnulacion( $radiNume , $usuario['login'] );
	if( $verificacionSolicitud ){
		$actualizaRadAnulado = "UPDATE radicado SET SGD_EANU_CODIGO=1 WHERE radi_nume_radi = $radiNume";
		$rs=$db->conn->Execute( $actualizaRadAnulado );
		
		$insertaEnAnulados = "insert into sgd_anu_anulados (RADI_NUME_RADI, SGD_EANU_CODI, SGD_ANU_SOL_FECH, 
							  DEPE_CODI , USUA_DOC, SGD_ANU_DESC , USUA_CODI) values ( $radiNume , 1 , 
							  (SYSDATE+0) , " . $usuario[ 'dependencia' ] . ", " . $usuario[ 'documento' ] . " , 
							  'Solicitud Anulacion.pruebas webservice orfeo', ) " . $usuario[ 'codusuario' ] ;
		$rs=$db->conn->Execute( $insertaEnAnulados );
		
		//Consulta de insercion historico para la anulacion
		//22418400 = Documento sra Superintendente EvaMaria U
		$insertaHistorico = "insert into HIST_EVENTOS(RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_CODI_DEST,
							 DEPE_CODI_DEST,USUA_DOC,HIST_DOC_DEST,SGD_TTR_CODIGO,HIST_OBSE,HIST_FECH) 
							 values ( $radiNume , " . $usuario[ 'dependencia' ] . ", " . $usuario[ 'codusuario' ] . 
							 " , 1 , 100 , " . $usuario[ 'documento' ] . " , 22418400, 25 ,
							 'Anulacion de Radicado desde Webservice',(SYSDATE+0))"; 
							 
		
		
		return "Exito Solicitando Anulacion";
	}else {
		return "Error Solicitando Anulacion";
	}
}

function verificaSolAnulacion ( $radiNume, $usuaLogin ){
	
	$consultaPermiso = "SELECT SGD_PANU_CODI FROM USUARIO WHERE USUA_LOGIN = '$usuaLogin";
	$rs=$db->conn->Execute( $consultaPermiso );
	$permisoAnulacion = $rs->fields[ 'SGD_PANU_CODI' ];
	
	if ( $permisoAnulacion == 0) {
		return false;
	}
	
    $consultaYaAnulado =	"SELECT r.RADI_NUME_RADI FROM radicado r, SGD_TPR_TPDCUMENTO c where r.radi_nume_radi is not null 
    and substr(r.radi_nume_radi, 5, 3)=905 and substr(r.radi_nume_radi, 14, 1) not in ( 2 ) 
    and r.tdoc_codi=c.sgd_tpr_codigo and r.sgd_eanu_codigo is null and 
    ( r.SGD_EANU_CODIGO = 9 or r.SGD_EANU_CODIGO = 2 or r.SGD_EANU_CODIGO IS NULL )";  
    
    /*
    $consultaYaAnulado2 = 'SELECT  to_char(b.RADI_NUME_RADI) 
    "IMG_Numero Radicado" , b.RADI_PATH "HID_RADI_PATH" , to_char(b.RADI_NUME_DERI) "Radicado Padre" , 
    b.RADI_FECH_RADI "HOR_RAD_FECH_RADI" , b.RADI_FECH_RADI "Fecha Radicado" , b.RA_ASUN "Descripcion" , 
    c.SGD_TPR_DESCRIP "Tipo Documento" , b.RADI_NUME_RADI "CHK_CHKANULAR" from radicado b, SGD_TPR_TPDCUMENTO c 
    where b.radi_nume_radi is not null and substr(b.radi_nume_radi, 5, 3)=905 and 
    substr(b.radi_nume_radi, 14, 1) in (1, 3, 5, 6) and b.tdoc_codi=c.sgd_tpr_codigo and 
    sgd_eanu_codigo is null and  ( b.SGD_EANU_CODIGO = 9 or b.SGD_EANU_CODIGO = 2 or b.SGD_EANU_CODIGO IS NULL ) 
    order by 4 ';*/
    
	$rs=$db->conn->Execute($consultaYaAnulado);
	$numRadicado = $rs->fields['RADI_NUME_RADI'];
	if ( !$numRadicado ) {
		return  false;
	}
	return true;
}

/**
 * Esta funcion permite radicar un documento en Orfeo
 * @param $usuEmail, este parametro es el correo electronico del usuario
 * @param $file, Archivo asociado al radicado codificado en Base64 
 * @param $filename, Nombre del archivo que se radica
 * @param $correo, Correo del usuario
 * @param $destinos, arreglo de destinatarios destinatarios,predio,esp
 * @param $asu, Asunto del radicado
 * @param $med, Medio de radicacion
 * @param $ane, descripcion de anexos
 * @param $coddepe, codigo de la dependencia
 * @param $tpRadicado, tipo de radicado
 * @param $cuentai, cuenta interna del radicado
 * @param $radi_usua_actu, 
 * @param $tip_rem
 * @param $tdoc
 * @param $tip_doc
 * @param $carp_codi
 * @param $carp_per 
 * @author Donaldo Jinete Forero
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */

function radicarDocumento($file,$filename,$correo,$destinatarioOrg,$predioOrg,$espOrg,$asu,$med,$ane,$coddepe,
$tpRadicado,$cuentai,$radi_usua_actu,$tip_rem,$tdoc,$tip_doc,$carp_codi,$carp_per,$usuaLogin)
{
	//Conversiones de datos para compatibilidad con aplicaciones internas
	$destinatario = array(
	'documento'=>$destinatarioOrg['documento'],
	'cc_documento'=>$destinatarioOrg['cc_documento'],
	'tipo_emp'=>$destinatarioOrg['tipo_emp'],
	'nombre'=>$destinatarioOrg['nombre'],
	'prim_apel'=>$destinatarioOrg['prim_apel'],
	'seg_apel'=>$destinatarioOrg['seg_apel'],
	'telefono'=>$destinatarioOrg['telefono'],
	'direccion'=>$destinatarioOrg['direccion'],
	'mail'=>$destinatarioOrg['mail'],
	'otro'=>$destinatarioOrg['otro'],
	'idcont'=>$destinatarioOrg['idcont'],
	'idpais'=>$destinatarioOrg['idpais'],
	'codep'=>$destinatarioOrg['codep'],
	'muni'=>$destinatarioOrg['muni']
	);
	$predio = array(
	'documento'=>$predioOrg['documento'],
	'cc_documento'=>$predioOrg['cc_documento'],
	'tipo_emp'=>$predioOrg['tipo_emp'],
	'nombre'=>$predioOrg[3],
	'prim_apel'=>$predioOrg[4],
	'seg_apel'=>$predioOrg[5],
	'telefono'=>$predioOrg[6],
	'direccion'=>$predioOrg['Direccion'],
	'mail'=>$predioOrg['mail'],
	'otro'=>$predioOrg[9],
	'idcont'=>$predioOrg[10],
	'idpais'=>$predioOrg[11],
	'codep'=>$predioOrg[12],
	'muni'=>$predioOrg[13]	
	);
	$esp = array(
	'documento'=>$espOrg['documento'],
	'cc_documento'=>$espOrg['cc_documento'],
	'tipo_emp'=>$espOrg['tipo_emp'],
	'nombre'=>$espOrg[3],
	'prim_apel'=>$espOrg[4],
	'seg_apel'=>$espOrg[5],
	'telefono'=>$espOrg[6],
	'direccion'=>$espOrg[7],
	'mail'=>$espOrg[8],
	'otro'=>$espOrg[9],
	'idcont'=>$espOrg[10],
	'idpais'=>$espOrg[11],
	'codep'=>$espOrg[12],
	'muni'=>$espOrg[13]
	);
	
	
	try {
		$infoUsuario = getInfoUsuarioLogin($radi_usua_actu);
		$radi_usua_actu = $infoUsuario["usua_codi"];
		
		$infoUsuario = getInfoUsuarioLogin($usuaLogin);
		$usuaCodiRadicador = $infoUsuario["usua_codi"];
		
		//return "$usuaLogin-".$infoUsuario."--->".$radi_usua_actu . $infoUsuario["usua_login"];
		//$coddepe = getInfoUsuario($coddepe);
		//$coddepe = trim($coddepe['usua_depe']);
	}catch (Exception $e){
		return $e->getMessage();
	}
	
	
	// Fin
	
	global $ruta_raiz;
	
	include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );
	include(RUTA_RAIZ."include/tx/Tx.php") ;
	include(RUTA_RAIZ."include/tx/Radicacion.php") ;
	include(RUTA_RAIZ."class_control/Municipio.php") ;
	include_once(RUTA_RAIZ."include/tx/Historico.php");

	$db = new ConnectionHandler($ruta_raiz,'WS') ;
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$hist = new Historico($db);
	$tmp_mun = new Municipio($db) ;
	$rad = new Radicacion($db) ;
	
	$tmp_mun->municipio_codigo($destinatario["codep"],$destinatario["muni"]) ;
	$rad->radiTipoDeri = $tpRadicado ;
	$rad->radiCuentai = "'".trim($cuentai)."'";
	$rad->eespCodi =  $esp["documento"] ;
	$rad->mrecCodi =  $med;
	$rad->radiFechOfic =  date("Y-m-d");
	if(!$radicadopadre)  $radicadopadre = null;
	$rad->radiNumeDeri = trim($radicadopadre) ;
	$rad->radiPais =  $tmp_mun->get_pais_codi() ;
	$rad->descAnex = $ane ;
	$rad->raAsun = $asu ;
	$rad->radiDepeActu = $coddepe ;
	$rad->radiDepeRadi = $coddepe ;
	$rad->usuaDoc = $infoUsuario["usua_doc"];
	$rad->radiUsuaActu = $usuaCodiRadicador ;
	$rad->trteCodi =  $tip_rem ;
	$rad->tdocCodi=$tdoc ;
	$rad->tdidCodi=$tip_doc ;
	$rad->carpCodi = $carp_codi ;
	$rad->carPer = $carp_per ;
	$rad->trteCodi=$tip_rem ;
	$rad->radiPath = 'null';
	if (strlen(trim($aplintegra)) == 0)
			$aplintegra = "0" ;
	$rad->sgd_apli_codi = $aplintegra ;
	$codTx = 2 ;
	$flag = 1 ;
	// Modificado SSPD 09-Diciembre-2008
	//$rad->usuaCodi=14 ;
	$rad->usuaCodi=$radi_usua_actu;
	$rad->dependencia=trim($coddepe) ;
	
	// Modificado SSPD 09-Diciembre-2008
	// Se consulta la secuencia que se debe utilizar para generar el consecutivo del tipo de radicado $tpRadicado,
	// asignada a la dependencia a la que pertenece el usuario.
	//$noRad = $rad->newRadicado($tpRadicado,$coddepe) ;
	$sqlSecuencia  = 'SELECT DEPE_RAD_TP'.$tpRadicado.' AS DEPE_RAD_TP';
	$sqlSecuencia .= ' FROM DEPENDENCIA D, USUARIO U';
	$sqlSecuencia .= ' WHERE U.DEPE_CODI = D.DEPE_CODI';
	$sqlSecuencia .= ' AND U.USUA_CODI = '.$radi_usua_actu;
	$sqlSecuencia .= ' AND D.DEPE_CODI = '.$coddepe;
	$sqlSecuencia .= ' AND U.USUA_ESTA = 1';
	// Modificado SSPD 10-Diciembre-2008
	// Por solicitud del Grupo de Aplicaciones Internas no se valida que el usuario
	// tenga permiso para generar un tipo de radicado espec�fico (en especial
	// resoluciones) hasta desligar Sancionados de Orfeo.
	// Plazo: Primer trimestre 2009.
	//$sqlSecuencia .= ' AND U.USUA_PRAD_TP'.$tpRadicado.' <> 0';
	
	$rsSecuencia = $db->conn->Execute( $sqlSecuencia );
	//return "Prueba 1 - $insertSQL <hr> $sqlSecuencia" .$infoUsuario["usua_codi"] ."<hr>". $rsSecuencia->fields[1];
	//return "<hr> $sqlSecuencia <hr>Radicado --> $noRad ---> ".$rsSecuencia->fields['DEPE_RAD_TP'];
	$noRad = $rad->newRadicado( $tpRadicado, $rsSecuencia->fields['DEPE_RAD_TP'] );
	
	//return $noRad;
	$nurad = trim($noRad) ;
	
	$sql_ret = $rad->updateRadicado($nurad,"/".date("Y")."/".$coddepe."/".$noRad.".pdf");
	
	if ($noRad=="-1")
	{
		return "ERROR: no genero un Numero de Secuencia o Inserto el radicado";		
	}
	
	$radicadosSel[0] = $noRad;
	$hist->insertarHistorico($radicadosSel,  $coddepe , $radi_usua_actu, $coddepe, $radi_usua_actu, " ", $codTx);
	$sgd_dir_us2=2;
	
	$conexion = $db;
	
	/*
		Preparacion de variables para llamar el codigo del
		archivo grb_direcciones.php
	*/
	// Modificado 18-Diciembre-2008
	$tipo_emp_us1=trim($destinatario['tipo_emp']);
	$tipo_emp_us2=trim($predio['tipo_emp']);
	
	$muni_us1 = trim($destinatario['muni']);
	$muni_us2 = trim($predio['muni']);
	$muni_us3 = trim($esp['muni']);
	
	$codep_us1 = trim($destinatario['codep']);
	$codep_us2 = trim($predio['codep']);
	$codep_us3 = trim($esp['codep']);
	
	$grbNombresUs1 = trim($destinatario['nombre']) . " " . trim($destinatario['prim_apel']) . " ". trim($destinatario['seg_apel']);
	$grbNombresUs2 = trim($predio['nombre']) . " " . trim($predio['prim_apel']) . " ". trim($predio['seg_apel']);
	
	$cc_documento_us1 = trim($destinatario['cc_documento']);
	$cc_documento_us2 = trim($predio['cc_documento']);
	
	$documento_us1 = trim($destinatario['documento']);
	$documento_us2 = trim($predio['documento']);
	
	$direccion_us1 = trim($destinatario['direccion']);
	$direccion_us2 = trim($predio['direccion']);
	
	$telefono_us1 = trim($destinatario['telefono']);
	$telefono_us2 = trim($predio['telefono']);
	
	$mail_us1 = trim($destinatario['mail']);
	$mail_us2 = trim($predio['mail']);
	
	$otro_us1 = trim($destinatario['otro']);
	$otro_us2 = trim($predio['otro']);
	
	//************** INSERTAR DIRECCIONES *******************************
	
	if (!$muni_us1) $muni_us1 = NULL;
	if (!$muni_us2) $muni_us2 = NULL;
	if (!$muni_us3) $muni_us3 = NULL;
	
	// Creamos las valores del codigo del dpto y mcpio desglozando el valor del <SELECT> correspondiente.
	if (!is_null($muni_us1))
	{
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us1,$muni_us1);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp1 = explode("-",$muni_us1);
		switch (count($muni_tmp1))
		{	
			case 4:
			{
				$idcont1 = $muni_tmp1[0];
				$idpais1 = $muni_tmp1[1];
				$dpto_tmp1 = $muni_tmp1[2];
				$muni_tmp1 = $muni_tmp1[3];

			}
			break;
		case 3:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $muni_tmp1[0];
				$dpto_tmp1 = $muni_tmp1[1];
				$muni_tmp1 = $muni_tmp1[2];
			}
			break;
		case 2:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $tmp_idpais;
				$dpto_tmp1 = $muni_tmp1[0];
				$muni_tmp1 = $muni_tmp1[1];
			}
			break;
					case 1:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $tmp_idpais;
				$dpto_tmp1 = $codep_us1;
				$muni_tmp1 = $muni_us1;
			}
			break;
		}
		unset($tmp_mun);
		unset($tmp_idcont);
		unset($tmp_idpais);
	}

	if (!is_null($muni_us2))
	{	
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us2,$muni_us2);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp2 = explode("-",$muni_us2);
		switch (count($muni_tmp2))
		{	
			case 4:
			{	
				$idcont2 = $muni_tmp2[0];
				$idpais2 = $muni_tmp2[1];
				$dpto_tmp2 = $muni_tmp2[2];
				$muni_tmp2 = $muni_tmp2[3];
			}
			break;
		case 3:
			{
				$idcont2 = $tmp_idcont;
				$idpais2 = $muni_tmp2[0];
				$dpto_tmp2 = $muni_tmp2[1];
				$muni_tmp2 = $muni_tmp2[2];
			}
			break;
		case 2:
			{
				$idcont2 = $tmp_idcont;
				$idpais2 = $tmp_idpais;
				$dpto_tmp2 = $muni_tmp2[0];
				$muni_tmp2 = $muni_tmp2[1];
			}
			break;
			case 1:
			{
				$idcont2 = $tmp_idcont;
				$idpais2 = $tmp_idpais;
				$dpto_tmp2 = $codep_us2;
				$muni_tmp2 = $muni_us2;
			}
			break;			
		}
		unset($tmp_mun);unset($tmp_idcont);unset($tmp_idpais);
	}	
	if (!is_null($muni_us3))
	{	
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us3,$muni_us3);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp3 = explode("-",$muni_us3);
		switch (count($muni_tmp3))
		{	
			case 4:
			{	
				$idcont3 = $muni_tmp3[0];
				$idpais3 = $muni_tmp3[1];
				$dpto_tmp3 = $muni_tmp3[2];
				$muni_tmp3 = $muni_tmp3[3];
			}
			break;
			case 3:
			{
				$idcont1 = $tmp_idcont;
				$idpais3 = $muni_tmp3[0];
				$dpto_tmp3 = $muni_tmp3[1];
				$muni_tmp3 = $muni_tmp3[2];
			}
			break;
		case 2:
			{
				$idcont3 = $tmp_idcont;
				$idpais3 = $tmp_idpais;
				$dpto_tmp3 = $muni_tmp3[0];
				$muni_tmp3 = $muni_tmp3[1];
			}
			break;
			case 1:
			{
				$idcont3 = $tmp_idcont;
				$idpais3 = $tmp_idpais;
				$dpto_tmp4 = $codep_us1;
				$muni_tmp4 = $muni_us1;
			}
			break;			
		}
		unset($tmp_mun);unset($tmp_idcont);unset($tmp_idpais);
	}
	
	$newId = false;
	if(!$modificar)
	{
   		$nextval=$conexion->nextId("sec_dir_direcciones");
	}
	if ($nextval==-1)
	{
		return "ERROR: No se encontro la secuencia sec_dir_direcciones ";
	}
	global $ADODB_COUNTRECS;
	
	//return $documento_us1 . "$cc_documento_us1";
	if($documento_us1!='' and $cc_documento_us1!='')
	{
		$sgd_ciu_codigo=0;
		$sgd_oem_codigo=0;
		$sgd_esp_codigo=0;
		$sgd_fun_codigo=0;
		
  		if($tipo_emp_us1==1)
  		{	
  			$sgd_ciu_codigo=$documento_us1;
			$sgdTrd = "1";
		}
		if($tipo_emp_us1==3)
		{	
			if($documento_us1) $sgd_esp_codigo=$documento_us1;
			
			$sgdTrd = "3";
		}
		if($tipo_emp_us1==2)
		{	
			$sgd_oem_codigo=$documento_us2;
			$sgdTrd = "2";
		}
		if($tipo_emp_us1==4)
		{	
			$sgd_fun_codigo=$documento_us4;
			$sgdTrd = "4";
		}
		//return $documento_us1 . " - " . $sgd_esp_codigo;
		$ADODB_COUNTRECS = true;
		$record = array();
		$record['SGD_TRD_CODIGO'] = $sgdTrd;
		$record['SGD_DIR_NOMREMDES'] = "'".$grbNombresUs1."'";
		$record['SGD_DIR_DOC'] = "'".$cc_documento_us1."'";
		$record['MUNI_CODI'] = $muni_tmp1;
		$record['DPTO_CODI'] = $dpto_tmp1;
		$record['ID_PAIS'] = $idpais1;
		$record['ID_CONT'] = $idcont1;
		$record['SGD_DOC_FUN'] = $sgd_fun_codigo;
		$record['SGD_OEM_CODIGO'] = $sgd_oem_codigo;
		$record['SGD_CIU_CODIGO'] = "'".$sgd_ciu_codigo."'";
		$record['SGD_OEM_CODIGO'] = "'".$sgd_oem_codigo."'";
		$record['SGD_ESP_CODI'] = "'".$sgd_esp_codigo."'";
		$record['RADI_NUME_RADI'] = "'".$nurad."'";
		$record['SGD_SEC_CODIGO'] = 0;
		$record['SGD_DIR_DIRECCION'] = "'".$direccion_us1."'";
		$record['SGD_DIR_TELEFONO'] = "'".trim($telefono_us1)."'";
		$record['SGD_DIR_MAIL'] = "'".$mail_us1."'";
		$record['SGD_DIR_TIPO'] = 1;
		$record['SGD_DIR_CODIGO'] = $nextval;
		if($record['SGD_DIR_NOMBRE']) $record['SGD_DIR_NOMBRE'] = $otro_us1;
		
	
	$insertSQL = $conexion->insert("SGD_DIR_DRECCIONES", $record);
	//return $conexion->querySql;
    
  switch ($insertSQL)
  {
	case 1:	{	//Insercion Exitosa
	 $dir_codigo_new = $nextval;
	 $newId=true;
	 return $nurad;
	}break;
	case 2:{	//Update Exitoso
	 $newId = false;
	}break;
	case 0:{	//Error Transaccion.
	 return  "ERROR: No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES UNO -- $conexion->querySql -- $res ";
	}break;
 }
 unset($record);
 $ADODB_COUNTRECS = false;
}
	// ***********************  us2
if($documento_us2!='')
{
	$sgd_ciu_codigo=0;
    $sgd_oem_codigo=0;
    $sgd_esp_codigo=0;
		$sgd_fun_codigo=0;
  if($tipo_emp_us2==0){
		$sgd_ciu_codigo=$documento_us2;
		$sgdTrd = "1";
	}
	if($tipo_emp_us2==1){
		$sgd_esp_codigo=$documento_us2;
		$sgdTrd = "3";
	}
	if($tipo_emp_us2==2){
		$sgd_oem_codigo=$documento_us2;
		$sgdTrd = "2";
	}
	if($tipo_emp_us2==6){
		$sgd_fun_codigo=$documento_us2;
		$sgdTrd = "4";
	}
	$isql = "select * from sgd_dir_drecciones where radi_nume_radi=$nurad and sgd_dir_tipo=2";
	$rsg=$conexion->query($isql);

    if 	($rsg->EOF)
	{
		//if($newId==true)
			//{
			   $nextval=$conexion->nextId("sec_dir_direcciones");
			//}
			if ($nextval==-1)
			{
				//$db->conn->RollbackTrans();
				echo "<span class='etextomenu'>No se encontr&oacute; la secuencia sec_dir_direcciones ";
			}

		$isql = "insert into SGD_DIR_DRECCIONES(SGD_TRD_CODIGO, SGD_DIR_NOMREMDES, SGD_DIR_DOC, DPTO_CODI, MUNI_CODI,
      			id_pais, id_cont, SGD_DOC_FUN, SGD_OEM_CODIGO, SGD_CIU_CODIGO, SGD_ESP_CODI, RADI_NUME_RADI, SGD_SEC_CODIGO,
      			SGD_DIR_DIRECCION, SGD_DIR_TELEFONO, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_CODIGO, SGD_DIR_NOMBRE)
	  			values('$sgdTrd', '$grbNombresUs2', '$cc_documento_us2', $dpto_tmp2, $muni_tmp2, $idpais2, $idcont2,
	  			$sgd_fun_codigo, $sgd_oem_codigo, $sgd_ciu_codigo, $sgd_esp_codigo, $nurad, 0,'".trim($direccion_us2).
	  			"', '".trim($telefono_us2)."', '$mail_us2', 2, $nextval, '$otro_us2')";
   	  $dir_codigo_new = $nextval;
   	  $newId=true;
    }
	 else
	{
	  $newId = false;
		$isql = "update SGD_DIR_DRECCIONES
				set MUNI_CODI=$muni_tmp2, DPTO_CODI=$dpto_tmp2, id_pais=$idpais2, id_cont=$idcont2
				,SGD_OEM_CODIGO=$sgd_oem_codigo
				,SGD_CIU_CODIGO=$sgd_ciu_codigo
				,SGD_ESP_CODI=$sgd_esp_codigo
				,SGD_DOC_FUN=$sgd_fun_codigo
				,SGD_SEC_CODIGO=0
				,SGD_DIR_DIRECCION='$direccion_us2'
				,SGD_DIR_TELEFONO='$telefono_us2'
				,SGD_DIR_MAIL='$mail_us2'
				,SGD_DIR_NOMBRE='$otro_us2'
				,SGD_DIR_NOMREMDES='$grbNombresUs2'
				,SGD_DIR_DOC='$cc_documento_us2'
				,SGD_TRD_CODIGO='$sgdTrd'
			 	where radi_nume_radi=$nurad and SGD_DIR_TIPO=2 ";
	}
	return "Entrando a Direcciones . . . .";

	$rsg=$conexion->query($isql);

	if (!$rsg){
		return "ERROR: No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES DOS -- $isql --";
	}

	}

if($documento_us1!='' and $cc!='')
{
	$sgd_ciu_codigo=0;
	$sgd_oem_codigo=0;
	$sgd_esp_codigo=0;
	$sgd_fun_codigo=0;

	echo "--$sgd_emp_us1--";
	  if($tipo_emp_us1==0){
		$sgd_ciu_codigo=$documento_us1;
		$sgdTrd = "1";
	}
	if($tipo_emp_us1==1){
		$sgd_esp_codigo=$documento_us1;
		$sgdTrd = "3";
	}
	if($tipo_emp_us1==2){
		$sgd_oem_codigo=$documento_us1;
		$sgdTrd = "2";
	}
	if($tipo_emp_us1==6){
		$sgd_fun_codigo=$documento_us1;
		$sgdTrd="4";
	}
	if($newId==true)
		{
		   $nextval=$conexion->nextId("sec_dir_direcciones");
		}
		if ($nextval==-1)
		{
			//$db->conn->RollbackTrans();
			return "ERROR: No se encontrasena la secuencia sec_dir_direcciones ";
		}
  $num_anexos=$num_anexos+1;
  $str_num_anexos = substr("00$num_anexos",-2);
  $sgd_dir_tipo = "7$str_num_anexos" ;
	$isql = "insert into SGD_DIR_DRECCIONES (SGD_TRD_CODIGO, SGD_DIR_NOMREMDES, SGD_DIR_DOC, MUNI_CODI, DPTO_CODI,
			id_pais, id_cont, SGD_DOC_FUN, SGD_OEM_CODIGO, SGD_CIU_CODIGO, SGD_ESP_CODI, RADI_NUME_RADI, SGD_SEC_CODIGO,
			SGD_DIR_DIRECCION, SGD_DIR_TELEFONO, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_CODIGO, SGD_ANEX_CODIGO, SGD_DIR_NOMBRE) ";
	$isql .= "values ('$sgdTrd', '$grbNombresUs1', '$cc_documento_us1', $muni_tmp1, $dpto_tmp1, $idpais1, $idcont1,
						$sgd_fun_codigo, $sgd_oem_codigo, $sgd_ciu_codigo, $sgd_esp_codigo, $nurad, 0, '$direccion_us1',
						'".trim($telefono_us1)."', '$mail_us1', $sgd_dir_tipo, $nextval, '$codigo', '$otro_us7' )";
  $dir_codigo_new = $nextval;
  $nextval++;
  $rsg=$conexion->query($isql);
  	return "Hola";
	if (!$rsg)
	{
		//$conexion->conn->RollbackTrans();
		return "ERROR: No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES TRES -- $isql --";
	}
}

	//*********************** FIN INSERTAR DIRECCIONES **********************
	

	$retval .=$noRad;
	
	if($filename!=''){
		$ext=explode('.',$filename);
		cambiarImagenRad($retval,$ext[1],$file);
	}
	
	return $retval;
}

/**
 * Esta funcion permite anular un  radicado  en Orfeo
 * @param $checkValue, es un arreglo con los numeros de radicado
 * @author Donaldo Jinete Forero
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */
function anularRadicado($checkValue,$dependencia,$usua_doc,$observa,$codusuario)
{
	/*  RADICADOS SELECCIONADOS
	 *  @$setFiltroSelect  Contiene los valores digitados por el usuario separados por coma.
	 *  @$filtroSelect Si SetfiltoSelect contiene algun valor la siguiente rutina 
	 *  realiza el arreglo de la condificacion para la consulta a la base de datos y lo almacena en whereFiltro.
	 *  @$whereFiltro  Si filtroSelect trae valor la rutina del where para este filtro es almacenado aqui.
	 */
	$radicadosXAnular = "";
	include_once(RUTA_RAIZ."include/db/ConnectionHandler.php");
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	if($checkValue) {
		$num = count($checkValue);
		$i = 0;
		while ($i < $num) {
			$estaRad   = false;
			$record_id = $checkValue[$i];
			// Consulta para verificar el estado del radicado del radicado en sancionados
			$querySancionados = "SELECT ESTADO 
						FROM SANCIONADOS.SAN_RESOLUCIONES 
						WHERE nro_resol = '$record_id'";
			$rs = $db->conn->Execute($querySancionados);
			
			// Si esta el radicado
			if (!$rs->EOF) {
				$estado = $rs->fields["ESTADO"];
				if ($estado != "V") {
					$vigente = false;
				}
				$estaRad = true;
			}
			
			// Si esta el radicado entonces verificar vigencia
			if ($estaRad) {
				// Si se encuentra vigente entonces no se puede anular
				if($vigente) {
					$arregloVigentes[] = $record_id;
				} else {
					$setFiltroSelect .= $record_id;
                                        $radicadosSel[] = $record_id;
					$radicadosXAnular .= "'" . $record_id . "'";
				}
			} else {
				$setFiltroSelect .= $record_id;
				$radicadosSel[] = $record_id;
			}
			
			if($i<=($num-2)) {
				if (!$vigente || !$estaRad) {
					$setFiltroSelect .= ",";
				}
				if ($estaRad && !empty($radicadosXAnular)) {
					$radicadosXAnular .= ",";
				}
			}
  			next($checkValue);
			$i++;
			// Inicializando los valores de comprobacion
			$estaRad = false;
			$vigente = true;
		}
		if ($radicadosSel) {
			$whereFiltro = " and b.radi_nume_radi in($setFiltroSelect)";
		}
	}
	$systemDate = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	include(RUTA_RAIZ.'config.php');
	include_once (RUTA_RAIZ.'anulacion/Anulacion.php');
	include_once (RUTA_RAIZ.'include/tx/Historico.php');
	// Se vuelve crear el objeto por que saca un error con el anterior 
	$db = new ConnectionHandler($ruta_raiz,'WS');
	$Anulacion = new Anulacion($db);
	$observa = "Solicitud Anulacion.$observa";
	
	/* Sentencia para consultar en sancionados el estado en que se encuentra el radicado
	 * A = Anulado, V = Vigente, B = Estado temporal 
	 * Si el estado del radicado en sancionados es diferente de V puede realizar la sancion
	 */
	// Si por lo menos hay un radicado por anular
	
	$retval.= "<br> radicadosSel = ". $radicadosSel[0];
	
	if (!empty($radicadosSel[0])) {
		$retval .= "<br>Anulacion
					<br>dependencia = $dependencia
					<br>usua_doc = $usua_doc
					<br>observa = $observa
					<br>codusuario = $codusuario
					<br>systemDate = $systemDate";
		$radicados = $Anulacion->solAnulacion($radicadosSel,
						$dependencia,
						$usua_doc,
						$observa,
						$codusuario,
						$systemDate);
		if (!empty($radicadosXAnular)) {
			$sqlSancionados = "update SGD_APLMEN_APLIMENS 
						set SGD_APLMEN_DESDEORFEO = 2 
						where SGD_APLMEN_REF in($radicadosXAnular)";
			$rs = $db->conn->Execute($sqlSancionados);
		}
		$fecha_hoy =date("Y-m-d");
		$dateReplace = $db->conn->SQLDate("Y-m-d","$fecha_hoy");
		$Historico = new Historico($db);
		/** 
		 * Funcion Insertar Historico 
		 * insertarHistorico($radicados,  
		 * 			$depeOrigen, 
		 *			$usCodOrigen,
		 *			$depeDestino,
		 *			$usCodDestino,
		 *			$observacion,
		 *			$tipoTx)
		 */
		$depe_codi_territorial = $dependencia;
		
		$radicados = $Historico->insertarHistorico($radicadosSel,
								$dependencia,
								$codusuario,
								$depe_codi_territorial,
								1,
								$observa,
								25); 
	}
	return $retval;
}

function anexarExpediente($numRadicado,$numExpediente,$usuaLoginMail,$observa){
		global $ruta_raiz;
		$db = new ConnectionHandler($ruta_raiz,'WS');
		include_once (RUTA_RAIZ.'include/tx/Historico.php');
        $estado=estadoRadicadoExpediente($numRadicado,$numExpediente);
        $usua=getInfoUsuario($usuaLoginMail);
        $tipoTx = 53;
    	$Historico = new Historico( $db );
    	$fecha=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
    	try{
        switch ($estado){
                case 0:
                        throw new Exception("El documento con numero de radicado  {$numRadicado} ya fue anexado al expediente {$numExpediente}");
                case 1:
                        throw new Exception("El documento con numero de radicado {$numRadicado} ya fue anexado al expediente {$numExpediente} y archivado fisicamente");
                case 2: 
                        $consulta="UPDATE SGD_EXP_EXPEDIENTE SET SGD_EXP_ESTADO=0,SGD_EXP_FECH={$fecha},USUA_CODI=".$usua['usua_codi'].",USUA_DOC='".$usua['usua_doc']."'
                                ,DEPE_CODI=".$usua['usua_depe']." WHERE RADI_NUME_RADI={$numRadicado} 
                                                AND SGD_EXP_NUMERO='{$numExpediente}'";
                                break;
                default:
                        $consulta="INSERT INTO SGD_EXP_EXPEDIENTE (SGD_EXP_NUMERO,RADI_NUME_RADI,SGD_EXP_FECH,SGD_EXP_ESTADO,USUA_CODI,USUA_DOC,DEPE_CODI)
                                          VALUES ('{$numExpediente}',{$numRadicado},{$fecha},0,".$usua['usua_codi'].",'".$usua['usua_doc']."',".$usua['usua_depe'].")";
                        break;
        }
    	}
    	catch (Exception $e){
    		return $e->getMessage();
    	}
        if($db->conn->Execute($consulta)){
        		$radicados = array($numRadicado);
                $radicados = $Historico->insertarHistoricoExp( $numExpediente, $radicados, $usua['usua_depe'], $usua['usua_codi'], $observa, $tipoTx, 0);
                return $radicados[0];
                
        }else{ 
                throw new Exception("Error y no se realizo la operacion");
        }
}


/*
function cambiarImagenRad($numRadicado,$ext,$file){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	$sql="SELECT RAPI_DEPE_RADI,RADI_FECH_OFIC FROM RADICADO WHERE RADI_NUME_RADI='{$numRadicado}'";
	$rs=$db->conn->Execute($sql);
	if(!$rs->EOF){
		$year=substr($numRadicado,0,4);
		$depe=substr($numRadicado,4,3);
		$path="/{$year}/{$depe}/docs/{$numRadicado}.{$ext}";
		$update="UPDATE RADICADO SET RADI_PATH='{$path}' where RADI_NUME_RADI='{$numRadicado}'";
		if(UploadFile($file,$numRadicado.'.'.$ext)=='exito'){
			$db->conn->Execute($update);
			return "OK";
		}else{
			throw new Exception("ERROR no se puede copiar el archivo");
		}
	}else{
			throw new Exception("ERROR El radicado no existe");
	}
}
*/


function estadoRadicadoExpediente($numRadicado,$numExpediente){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	$salida=-1;
	$consulta="SELECT SGD_EXP_ESTADO FROM SGD_EXP_EXPEDIENTE WHERE RADI_NUME_RADI={$numRadicado} AND SGD_EXP_NUMERO='{$numExpediente}'";
	$resultado=$db->conn->Execute($consulta);
	if($resultado && !$resultado->EOF){
		$salida=$resultado->fields['SGD_EXP_ESTADO'];
	}
	return $salida;
}

function getInfoUsuario($usuaLoginMail){
		global $ruta_raiz;
		//$db = new ConnectionHandler($ruta_raiz);
		$db = new ConnectionHandler($ruta_raiz,'WS');
		$upperMail=strtoupper($usuaLoginMail);
		$lowerMail=strtolower($usuaLoginMail);
        $sql="SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB FROM USUARIO 
                        WHERE  USUA_EMAIL='{$usuaLoginMail}@superservicios.gov.co' OR USUA_EMAIL='{$upperMail}@superservicios.gov.co' OR USUA_EMAIL='{$lowerMail}@superservicios.gov.co' ";
        $rs=$db->conn->Execute($sql);
                if($rs && !$rs->EOF){
                		$salida['usua_login']=($rs->fields["USUA_LOGIN"]);
                        $salida['usua_doc'] =($rs->fields["USUA_DOC"]);
                        $salida['usua_depe'] =($rs->fields["DEPE_CODI"]);
                        $salida['usua_nivel'] =($rs->fields["CODI_NIVEL"]);
                        $salida['usua_codi'] =($rs->fields["USUA_CODI"]);
                        $salida['usua_nomb'] =($rs->fields["USUA_NOMB"]);
        }else{
        	throw new Exception("El usuario $usuaLoginMail no existe $sql");
        }
        
        return $salida;
}

function getInfoUsuarioLogin($usuaLogin){
 global $ruta_raiz;
 //$db = new ConnectionHandler($ruta_raiz);
 $db = new ConnectionHandler($ruta_raiz,'WS');
 $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
 $upperMail=strtoupper($usuaLogin);
 //$lowerMail=strtolower($usuaLogin);
 $sql="SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB 
       FROM USUARIO 
       WHERE  USUA_LOGIN='{$usuaLogin}'";
  $rs=$db->conn->Execute($sql);
      
  //return "<hr>Codigo Us--> ".$rs->fields["usua_codi"]. "$sql";
  if($rs){
	$salida['usua_login']=($rs->fields["USUA_LOGIN"]);
	$salida['usua_doc'] =($rs->fields["USUA_DOC"]);
	$salida['usua_depe'] =($rs->fields["DEPE_CODI"]);
	$salida['usua_nivel'] =($rs->fields["CODI_NIVEL"]);
	$salida['usua_codi'] =($rs->fields["USUA_CODI"]);
	$salida['usua_nomb'] =($rs->fields["USUA_NOMB"]);
	//return "-->* " .$rs->fields["DEPE_CODI"];
   }else{
   	throw new Exception("El usuario $usuaLoginMail no existe $sql");
 }
 return $salida;
}


function asociarObjetoFlujo($nuRad,$usuaEmail,$tflujo){
	$info = getInfoUsuario($usuaEmail);
	try{
		$flujo= new flujo($nuRad,$info['usua_login'],1);
		$flujo->asociarFlujo($tflujo);
	}catch(Exception $e){
		return $e->getMessage();
	}
	return "OK";
}

function cambioDeEtapaFlujo($objDoc,$flujo,$etapa){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	try{
		$value = flujo::cambiarEtapa($db,$objDoc,$flujo,$etapa);
	}catch (Exception $e){
		return $e->getMessage();
	}
	return $value;	
}

function informacionJefe($usuaEmail){
	global $ruta_raiz;
	try{
		$info = getInfoUsuario($usuaEmail);
	}catch (Exception $e){
		return array($e->getMessage());
	}
	$db = new ConnectionHandler($ruta_raiz,'WS');
	$consulta = "SELECT USUA_NOMB,USUA_EMAIL FROM USUARIO WHERE DEPE_CODI = '{$info['usua_depe']}' AND USUA_CODI='1' ";
	$rs=$db->conn->Execute($consulta);
	if($rs->EOF){
		return array("No se puede obtener informacion del jefe de la dependencia {$info['usua_depe']}");	
	}
	return array($rs->fields['USUA_NOMB'],$rs->fields['USUA_EMAIL']);
}


//Function de servicios nuevos
include_once "validarRadicado/funServicios.php";
include_once "devolucion/funServicios.php";
// Modificado SSPD 01-Diciembre-2008
// Implementacion del servicio Web notificar
include_once "notificar/funServicios.php";
include_once "reasignarRadicado/funServicios.php";
// Modificado SSPD 16-Octubre-2008
// Implementación de las operaciones tipificarDocumento, isDocumentoTipificado,
// anexoRadicadoToRadicado
include_once("tipificar/funServicios.php");
include_once("anexo/funServicios.php");
//Modificacion 20  octubre servicio modificarRadicado
include_once "radicado/funServicios.php";
//Fin 20  octubre
//Modificacion 13 feb 2009 servicio cambiar imagen2
include_once "cambiarImagen/funServicios.php";
//Fin 13 feb 2009
$server->service($HTTP_RAW_POST_DATA);
?>
