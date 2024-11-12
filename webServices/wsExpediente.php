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
$ns="wsExpediente";

//Creacion del objeto soap_server
$server = new soap_server();
//$server->soap_defencoding = $ENCini->variable( 'CharacterSettings', 'utf-8' );
$server->soap_defencoding = "utf-8";
$server->charencoding = "utf-8";
$server->configureWSDL('OrfeoGPL',$ns);
 
/**
 * Servicio para crear un expediente
 * Recibe Radicado, serie, subserie y datos de creacion de radicados
 */
$server->register('crearExpediente',  	//nombre del servicio                 
    array('nurad' => 'xsd:string',	//numero de radicado	
     'usuario' => 'xsd:string',		//usuario que genero la radicacion
     'anoExp' => 'xsd:string',		//ano del expediente
     'fechaExp' => 'xsd:string',	//fecha expediente
     'codiSRD'=>'xsd:string',		//Serie del Expediendte
     'codiSBRD'=>'xsd:string',		//Subserie del expediente
     'codiProc'=>'xsd:string'		//Codigo del proceso
     ),																//entradas        	
    array('return' => 'xsd:string'),   	// salidas
    $ns,                     		//Elemento namespace para el metod
    $ns."#crearExpediente",
	'rpc',
	'encoded',
	'Creacion de Un Expediente.  por http://www.correlibre.org'
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

//registro de servicios nuevos
//include_once "validarRadicado/regServicios.php";
//include_once "devolucion/regServicios.php";
// Modificado SSPD 01-Diciembre-2008
// Registro del servicio Web notificar
//include_once "notificar/regServicios.php";
//include_once "reasignarRadicado/regServicios.php";
// Modificado SSPD 16-Octubre-2008
// Registro de los servicios tipificarDocumento, isDocumentoTipificado,
// anexoRadicadoToRadicado
//include_once( "tipificar/regServicios.php" );
//include_once( "anexo/regServicios.php" );
//Modificacion 20  octubre servicio modificarRadicado
//include_once "radicado/regServicios.php";
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
 * Creacion de Expedientes en Orfeo
 * @param $nurad, este parametro es el numero de radicado
 * @param $usuario, este parametro es el usuario que crea el expediente, es el usuario de correo
 * @return El numero de expediente  
 */
function crearExpediente($nurad,$usuario,$anoExp,$fechaExp,$codiSRD,$codiSBRD,$codiProc){
	//return $nurad.",$usuario,$anoExp,$fechaExp,$codiSRD,$codiSBRD,$codiProc";
        include_once(RUTA_RAIZ."include/tx/Expediente.php");
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz,'WS');
	$expediente = new Expediente($db);
        
	$sql= "select USUA_CODI,DEPE_CODI,USUA_DOC from usuario where USUA_LOGIN = upper ('".$usuario."')";
	$rs = $db->conn->Execute($sql);
        //return "sss".$rs->fields["USUA_CODI"] . "<BR> $sql";
	while (!$rs->EOF){
			 $codusuario  = $rs->fields['USUA_CODI'];
			 $dependencia = $rs->fields['DEPE_CODI'];
			 $usua_doc =  $rs->fields['USUA_DOC'];
			 $usuaDocExp = $usua_doc; 
			 $rs->MoveNext();
	} 
	

		
	$trdExp = substr("00".$codiSRD,-2) . substr("00".$codiSBRD,-2);
	$secExp = $expediente->secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp);
        //return $secExp;
	$consecutivoExp = substr("00000".$secExp,-5);
	$numeroExpediente = $anoExp . $dependencia . $trdExp . $consecutivoExp . "E";
        //return $numeroExpediente;
										
													                                                                                                               
	$numeroExpedienteE = $expediente->crearExpediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc,$usuaDocExp,$codiSRD,$codiSBRD,'false',$fechaExp,$codiProc);
	return $numeroExpedienteE;
	$insercionExp = $expediente->insertar_expediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc);	

	return $numeroExpedienteE;
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


//Function de servicios nuevos
//include_once "validarRadicado/funServicios.php";
//include_once "devolucion/funServicios.php";
// Modificado SSPD 01-Diciembre-2008
// Implementacion del servicio Web notificar
//include_once "notificar/funServicios.php";
//include_once "reasignarRadicado/funServicios.php";
// Modificado SSPD 16-Octubre-2008
// Implementación de las operaciones tipificarDocumento, isDocumentoTipificado,
// anexoRadicadoToRadicado
//include_once("tipificar/funServicios.php");
//include_once("anexo/funServicios.php");
//Modificacion 20  octubre servicio modificarRadicado
//include_once "radicado/funServicios.php";
//Fin 20  octubre
//Modificacion 13 feb 2009 servicio cambiar imagen2
//include_once "cambiarImagen/funServicios.php";
//Fin 13 feb 2009
$server->service($HTTP_RAW_POST_DATA);
?>
