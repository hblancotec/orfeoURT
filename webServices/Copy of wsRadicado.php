<?php
$RUTA_RAIZ = "../";
define('RUTA_RAIZ','../');

require_once "lib/nusoap.php";
include_once RUTA_RAIZ."include/db/ConnectionHandler.php";

//Asignacion del namespace  
$ns = "http://".$_SERVER['SERVER_NAME']."/webServiceOrfeo";

//Creacion del objeto soap_server
$server = new soap_server();
//$server->soap_defencoding = $ENCini->variable( 'CharacterSettings', 'utf-8' );
$server->soap_defencoding = "utf-8";
$server->charencoding = "utf-8";
$server->configureWSDL('OrfeoGPL',$ns);
 
/*********************************************************************************
Se registran los servicios que se van a ofrecer, el metodo register tiene los sigientes parametros
**********************************************************************************/

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

// servicio para suiffp radicacion de documentos 
$server->register('proscSuiffp',
    array('numusua' => 'xsd:string',
    'bpin'        => 'xsd:string',
    'empre'       => 'xsd:string',
    'accion'      => 'xsd:string',
    'nomProyect'  => 'xsd:string',
    'sector'      => 'xsd:string',
    'noSoliSuifp' => 'xsd:string',
    'tiSoliSuifp' => 'xsd:string',
    'numradPadre' => 'xsd:string',
    'htmlfile'    => 'xsd:string'
    ),
    array('return' => 'xsd:string'), 
    $ns,
    $ns."#radicarDocumento",
    'rpc',
    'encoded',
    'Radicacion de un documento en Orfeo'
);

// ver las imaganes de los radicados
$server->register('radiPath',
    array('numusua' => 'xsd:string',
    'radicado'      => 'xsd:string',
    'fechini'       => 'xsd:string',
    'fechfin'       => 'xsd:string'
    ),
    array('return' => 'tns:imags'), 
    $ns,
    $ns."#radiPath",
    'rpc',
    'encoded',
    'Ver las imagenes de los radicados'
);

// servicio para suiffp reasignar radicados 
$server->register('reasigarSuifp',
    array('radNo'    => 'xsd:string',
          'usuDesti' => 'xsd:string',
          'tdoc'     => 'xsd:string'
    ),
    array('return' => 'xsd:string'), 
    $ns,
    $ns."#reasignarDocumentos",
    'rpc',
    'encoded',
    'Reasignación de radicados de un documento en Orfeo'
);

// Servicio que realiza una radicacion en Orfeo
$server->register('modificarRad'
	,array('nuRad' => 'xsd:string'
	,'destinatarioOrg' => 'tns:Destinatario'
	,'predioOrg' => 'tns:Destinatario'
	,'espOrg' => 'tns:Destinatario'
	,'asu' => 'xsd:string'
	,'med' => 'xsd:string'
	,'ane' => 'xsd:string'
	,'cuentai' => 'xsd:string'
	,'usuaDoc' => 'xsd:string'
	,'entidad' => 'xsd:string'
	),
    array(
        'return' => 'tns:respuesta'
    ),
    $ns,
    $ns."#modificarRad",
    'rpc',
    'encoded',
    'Modificacion de Un Documento en OrfeoGPL - Correlibre.org'
);


/**
 *Servicio que Radica un Documento recibiendo como parametros
 *Documentos de Identificacion de los Usuarios Radicadores y destino.
 *@autor Jairo Losada - 2009/09 En DNP.
 */

$server->register('radicarXUsuaDoc',
    array(
        'correo'              => 'xsd:string',
        'destinatario'        => 'tns:Destinatario',
        'predio'              => 'tns:Destinatario',
        'esp'                 => 'tns:Destinatario',
        'asu'                 => 'xsd:string',
        'med'                 => 'xsd:string',
        'ane'                 => 'xsd:string',
        'coddepe'             => 'xsd:string',
        'tpRadicado'          => 'xsd:string',
        'cuentai'             => 'xsd:string',
        'docUsuarioDestino'   => 'xsd:string',
        'tip_rem'             => 'xsd:string',
        'tdoc'                => 'xsd:string',
        'tip_doc'             => 'xsd:string',
        'carp_codi'           => 'xsd:string',
        'carp_per'            => 'xsd:string',
        'docUsuarioRadicador' => 'xsd:string',
        'radicadoAsociado'    => 'xsd:string',
        'numeroExpediente'    => 'xsd:string',
        'htmlfile'            => 'xsd:string'
    ),
    array(
        'return' => 'tns:respuesta'
    ),
    $ns,
    $ns."#radicarXUsuaDoc",
    'rpc',
    'encoded',
    'Radicacion de un documento en Orfeo'
);

/**
 *Servicio que Busca y devuelve Las acciones en historico de radicados cerrados
 *@autor Jairo Losada - 2009/02 Fundacion Correlibre - jairo losada
 *@autor Jairo Losada - 2009/09 En DNP.
 */

$server->register('logRadicadosCerrados',
    array(
    'numeroRadicado' => 'xsd:string',
	'fechaInicial'   => 'xsd:string',
	'fechaFinal'     => 'xsd:string'
    ),
    array(
            'return' => 'tns:respuesta'
    ),
    $ns,
    $ns."#logRadicadosCerrados",
    'rpc',
    'encoded',
    'Consulta de radicados cerrados desde orfeo'                                         
);

/**
 *Servicio que Busca y devuelve los datos de un radicado especifico
 *@autor Jairo Losada - 2009/02 Fundacion Correlibre - jairo losada
 *@autor Jairo Losada - 2009/09 En DNP.
 */

$server->register('buscarRadicados',
    array(
    'numeroRadicado' => 'xsd:string',
	'tipoBusqueda' => 'xsd:string'
    ),
    array(
            'return' => 'tns:respuesta'
    ),
    $ns,
    $ns."#buscarRadicados",
    'rpc',
    'encoded',
    'Radicacion de un documento en Orfeo'
);




// Servicio de transferir archivo



/**
 * uploadImagenRadicado
 * Permite enviar un archivo y asociarlo como imagen del Radicado.
 * @autor Jairo Losada - DNP
 * @version OrfeoGPL 3.8
 * http://www.correlibre.org - http://www.orfeogpl.org
 * @var string numeroRadicado Numero de Radicado al Cual se Asocia la Imagen o Archivo
 **/

$server->register('uploadImagenRadicado',
    array(
     'numRadicado'=>'xsd:string',
     'extension'=>'xsd:string',
     'file'=>'xsd:base64binary'
    ),
    array(
    'return'=>'xsd:string'
    ),
    $ns,
    $ns."#uploadImagenRadicado",
    'rpc',
    'encoded',
    'Subir o Cambiar imagen a un radicado'
);

include_once "reasignarRadicado/regServicios.php";
/**********************************************************************************
Se registran los tipos complejos y/o estructuras de datos
***********************************************************************************/

$server->wsdl->addComplexType(
    'imag',
    'complexType',
    'struct',
    'all',
    '',
    array(
    'numradi' => array('name' => 'numradi',
         'type' => 'xsd:string'),
    'path'    => array('name' => 'path',
         'type' => 'xsd:string'),
    'error'   => array('name' => 'error',
         'type' => 'xsd:string')
    )
);


$server->wsdl->addComplexType(
    'imags',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:imag[]')
    ),
    'tns:imag'
);


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

//Tipo complejo listaRadicados
$server->wsdl->addComplexType(
    'listaRadicados',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'radicado' => array('name' => 'documento','type' => 'xsd:string'),
        'expediente' => array('name' => 'cc_documento','type' => 'xsd:string'),
        'asunto' => array('name' => 'tipo_emp','type' => 'xsd:string'),
        'fecha' => array('name' => 'nombre','type' => 'xsd:string'),
        'paht' => array('name' => 'prim_apell','type' => 'xsd:string'),
        'dependencia' => array('name' => 'seg_apell','type' => 'xsd:string')
        )
);


//Tipo complejo arreglo de respuesta con sus  errores
$server->wsdl->addComplexType(
    'respuesta',
    'complexType',
    'struct',
    'all',
    '',
    array(
     'numeroRadicado' => array('name' => 'numeroRadicado','type' => 'xsd:string'),
	 'textoLog' => array('name' => 'textoLog','type' => 'xsd:string'),
	 'textoSql' => array('name' => 'textoLog','type' => 'xsd:string'),
     'errorNumeroRadicado' => array('name' => 'errorNumeroRadicado','type' => 'xsd:string'),
     'numeroExpediente' => array('name' => 'numeroExpediente','type' => 'xsd:string'),
     'infNumeroExpediente' => array('name' => 'infNumeroExpediente','type' => 'xsd:string'),
     'infNumeroExpedientePadre' => array('name' => 'infNumeroExpedientePadre','type' => 'xsd:string'),
     'datosEnviadosGenRadicado' => array('name' => 'datosEnviadosGenRadicado','type' => 'xsd:string'),
     'datosEnviadosIncluirEnExpediente' => array('name' => 'datosEnviadosIncluirEnExpediente','type' => 'xsd:string'),
     'datosAnexoRadicado' => array('name' => 'datosAnexoRadicado','type' => 'xsd:string'),
     'datosAnexoCopia1' => array('name' => 'datosAnexoCopia1','type' => 'xsd:string'),
     'datosAnexoCopia2' => array('name' => 'datosAnexoCopia2','type' => 'xsd:string'),
     'error' => array('name' => 'error','type' => 'xsd:string'),
     'descError' => array('name' => 'descError','type' => 'xsd:string')
     
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
    //return  "Entro a Upload File<hr>":
    //return "<hr>El path que llega a Upload File es  $filename<hr>";
    $path = getPath($filename);
    
    if(!$fp = fopen($filename, "w")){
        return "<font color=red>Error Al Grabar Fila</font>";
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
function crearAnexo($radiNume,$radicadoPadre, $file,$filename,$correo,$descripcion,$loginUsuario=null
                    ,$dirTipo=1,$anexSalida=0,$descripcion=""){
    global $ruta_raiz;
    //return "Fin";
    $db = new ConnectionHandler($ruta_raiz,'WS');
    if($correo) $usuario=getUsuarioCorreo($correo);
    //$error=(isset($usuario['error']))?true:false;
    if($loginUsuario)
    {
        $usuario['login']=$loginUsuario;
        $error= "";
    }
        
    $numAnexo=numeroAnexos($radicadoPadre,$db);
    $numAnexo++;
    //$maxAnexos=maxRadicados($radicadoPadre,$db)+1;
    //$numAnexo=($numAnexos > $maxAnexos)?$numAnexos:$maxAnexos;
    $nombreAnexo=$radicadoPadre.substr("00000".$numAnexo,-5);
    $fechaAnexado= $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
    //return "<hr>$correo $loginUsuario entro * * * ....-->$error<--<hr>";
    if($filename){
      $ruta=$ruta_raiz."bodega/".substr($radicadoPadre,0,4)."/".substr($radicadoPadre,4,3)."/docs/";
      $extension=substr($filename,strrpos($filename,".")+1);	
      $subirArchivo=subirArchivo($ruta,$file,$nombreAnexo.".".$extension);
      $tamanoAnexo = $subirArchivo / 1024; //tamano en kilobytes
      $error=($error && !$subirArchivo)?true:false;
      $tipoAnexo=tipoAnexo($extension,$db);
      $anexNombArchivo = "$nombreAnexo.$extension";
    }else{
      $tipoAnexo = 0;
      $tamanoAnexo = 0;
      $anexNombArchivo = "''";
    }
    
    if(!$error){
        $tipoAnexo=($tipoAnexo)?$tipoAnexo:"1";
        $consulta="INSERT INTO ANEXOS (ANEX_CODIGO,ANEX_RADI_NUME,ANEX_TIPO,ANEX_TAMANO,ANEX_SOLO_LECT,ANEX_CREADOR,
                    ANEX_DESC,ANEX_NUMERO,ANEX_NOMB_ARCHIVO,ANEX_ESTADO,SGD_REM_DESTINO,ANEX_FECH_ANEX
                    , ANEX_BORRADO,SGD_DIR_TIPO,ANEX_SALIDA,RADI_NUME_SALIDA)  
                    VALUES('$nombreAnexo','$radicadoPadre',$tipoAnexo,$tamanoAnexo,'N','".$usuario['login']."','$descripcion'
                    ,$numAnexo,'',2,1,$fechaAnexado, 'N',$dirTipo,$anexSalida,$radiNume)";
                    //return $consulta . "--->$tipoAnexo";
        $error=$db->conn->Execute($consulta);
        $consultaVerificacion = "SELECT ANEX_CODIGO FROM ANEXOS WHERE ANEX_CODIGO = '$nombreAnexo'";
        //return $consultaVerificacion;
        $rs=$db->conn->Execute($consultaVerificacion);
        $cod = $rs->fields[0];
    }
    return $cod ? "Anexo Creado $cod" : 'Error en la adicion verifique: ' . $nombreAnexo."<$consulta>";
}
/**
 * funcion que calculcula el numero de anexos que tiene un radicado
 *
 * @param int  $radiNume radicado al cual se realiza se adiciona el anexo
 * @param ConectionHandler $db
 * @return int numero de anexos del radicado
 */
function numeroAnexos($radiNume,$db){
    $consulta="SELECT ANEX_NUMERO  FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume} ORDER BY ANEX_NUMERO DESC";
    $salida=0;	
    $rs=& $db->conn->Execute($consulta);
    if($rs && !$rs->EOF)
        $salida=$rs->fields[0];
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
    $consulta="SELECT max(ANEX_NUMERO) FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume} ORDER BY NUM_ANEX DESC";
    $rs=& $db->conn->Execute($consulta);
    if($rs && !$rs->EOF)
        $salida=$rs->fields[0];
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
	
    $consultaYaAnulado ="SELECT r.RADI_NUME_RADI FROM radicado r, SGD_TPR_TPDCUMENTO c where r.radi_nume_radi is not null 
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
 * @param $usuarioDocDestino, Documento Identificacion Usuario Radicador 
 * @param $tip_rem
 * @param $tdoc  // Cedula, Nit... 
 * @param $tip_doc // Tipo de DOcumneto
 * @param $carp_codi
 * @param $carp_per
 * @param string Usuario Radicador
  * @author Jairo Losada en DNP
 * @return String Numero de Radicado
 */

function radicarXUsuaDoc(
    $correo,
    $destinatarioOrg,
    $predioOrg,
    $copia2,
    $asu ,
    $med=1,
    $ane,
    $coddepe,
    $tpRadicado,
    $cuentai,
    $docUsuarioDestino,
    $tip_rem,
    $tdoc,
    $tip_doc,
    $carp_codi,
    $carp_per,
    $docUsuarioRadicador,
    $radicadoAsociado=0,
    $numeroExpediente,
    $htmlfile){

    global $ruta_raiz;
    $respuesta = array();

    if(!trim($asu)) $asu = "Sin Asunto";
    if(!trim($med)) $med = 1;

    $adate    = date("Y");
    $radano   = $adate;

    $ruta   = $radano.$coddepe."suifp_".rand(10000, 99999)."_".time().'.html'; //ruta anexos
    $ruta2  = "bodega/$radano/$coddepe/docs/$ruta"; //donde se guarda el archivo 
    $ruta3  = "$radano/$coddepe/docs/$ruta"; //ruta radicado

    if(empty($htmlfile)){
        $respuesta['error']     = "8";
        $respuesta['descError'] = "Error, no llego la imagen del radicado ";
        return $respuesta;
    };

    file_put_contents('../'.$ruta2, $htmlfile);

    include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );
    $db = new ConnectionHandler($ruta_raiz,'WS') ;
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $consultaUsuario ="select usua_login from usuario where usua_doc='".$docUsuarioDestino."' and depe_codi = $coddepe and usua_esta=1";
    $rs1=$db->conn->Execute( $consultaUsuario );
    $loginUsuarioDestino=$rs1->fields["USUA_LOGIN"];
    $respuesta['error'] = "0";

    if(!$loginUsuarioDestino) {
        $respuesta['error']     = "1";
        $respuesta['descError'] = "(Error, No se encontro Un Usuario Radicador En Orfeo Con Documento $docUsuarioRadicador)";
        return $respuesta;
    }

    if(trim($radicadoAsociado)){
        $consultaRadicado   = "select RADI_NUME_RADI from radicado where radi_nume_radi = $radicadoAsociado";
        $rs1                = $db->conn->Execute( $consultaRadicado );
        $radicadoAsociadoDb = $rs1->fields["RADI_NUME_RADI"];

        //return "Login usuario Dest. --> ".$loginUsuarioDestino;
        if($radicadoAsociadoDb != $radicadoAsociado){
            $respuesta['error']     = "2";
            $respuesta['descError'] = "(Error, No se encontro el Radicado No $radicadoAsociado (sql: $consultaRadicado)) ";
            return $respuesta;
        } 

        //Consultamos si el radicado tiene un concepto, si es 
        //asi no genera otro.
        $cnsulConce ="SELECT 
            COUNT(1) AS CANT
            FROM 
            ANEXOS
            WHERE 
            ANEX_RADI_NUME=$radicadoAsociado
            AND ANEX_BORRADO='N'
            AND SUBSTRING(CONVERT(VARCHAR(15), RADI_NUME_SALIDA), 14 , 1 ) = 6";

        $rs11       = $db->conn->Execute($cnsulConce);
        $anexConcep = $rs11->fields["CANT"];

        if(!empty($anexConcep)){
            $respuesta['error']     = "5";
            $respuesta['descError'] = "(A la solicitud No. $radicadoAsociado se le genero un concepto, verifique en orfeo)";
            return $respuesta;
        }
    }

    $consultaUsuario ="select usua_login, USUA_CODI from usuario where usua_doc='".$docUsuarioRadicador."' and depe_codi = $coddepe and usua_esta=1";
    $rs1=$db->conn->Execute( $consultaUsuario);
    $loginUsuarioRadicador=$rs1->fields["USUA_LOGIN"];
    $usuaCodi=$rs1->fields["USUA_CODI"];
    if(!$loginUsuarioRadicador) {
        $respuesta['error']       = "3";
        $respuesta['descError'] .= "[Error, No se encontro Un Usuario Destino nn Orfeo Con Documento $docUsuarioDestino] ";
        return $respuesta;
    }
    /**  EN ESTA PARTE SE GENERA EL NUMERO DE RADICADO 
     * @autor 2010 jloasda- Correlibre.org - Modificacion  JAIRO LOSADA-DNP 2010 - 
     */
    $noRadicado =  radicarDocumento(" ", " ",$correo,$destinatarioOrg,$predioOrg,$copia2,$asu,$med,$ane,$coddepe,
        $tpRadicado,$cuentai,$loginUsuarioDestino,$tip_rem,$tdoc,$tip_doc,$carp_codi,$carp_per,$loginUsuarioRadicador,$radicadoAsociado);


    if(!$radicadoAsociado) $radicadoAsociado = $noRadicado;

    $sqlE = "UPDATE RADICADO
        SET 
        RADI_PATH = ('$ruta3')
        WHERE 
        RADI_NUME_RADI = $noRadicado";

    $resul = $db->conn->Execute($sqlE);

    if(empty($resul)){
        $respuesta['error'] = "15";
        $respuesta['descError'] = 'ERROR: No se anexo archivo';
        return $respuesta;
    }

    $descripcion = trim($asu) ;

    $creoAnexo = crearAnexo($noRadicado,$radicadoAsociado,"","","","",$loginUsuarioDestino,1,1,$descripcion, $ruta);
    $respuesta['datosAnexoRadicado'] = "$creoAnexo . [$descripcion]";

    $numeroCopia = 701;
    $nombreDestCopia1 = trim($predioOrg['nombre']) . " " . trim($predioOrg['prim_apel']). " ". trim($predioOrg['seg_apel']);
    if(!trim($nombreDestCopia1)){
        $respuesta['datosAnexoCopia1'] = "No llegan datos Copia1 . [$descripcion2][$creoCopia2]";
    }else{
        $descripcion1 = trim($asu) . "(cc. ".substr($nombreDestCopia1,0,35).")";
        $creoAnexo1 = crearAnexo($noRadicado,$radicadoAsociado,"","","","",$loginUsuarioDestino,701,1,$descripcion1, $ruta);
        $creoCopia1 = crearOtroDestino($db,$noRadicado,$loginUsuarioDestino
            ,$nombreDestCopia1,$predioOrg['documento'],$predioOrg['muni'],$predioOrg['codep']
            ,$predioOrg['idpais'],$predioOrg['idcont']
            ,$predioOrg['direccion'],$predioOrg['telefono'],$predioOrg['mail'],0,0,0,0,$numeroCopia,$nombreDestCopia1);
        $respuesta['datosAnexoCopia1'] = "$creoAnexo1 . [$descripcion1][$creoCopia1], ";
        $numeroCopia++;
    }

    $nombreDestCopia2 = trim($copia2['nombre']) . " " . trim($copia2['prim_apel']). " ". trim($copia2['seg_apel']);
    if(!trim($nombreDestCopia2)){
        $respuesta['datosAnexoCopia2'] = "No llegan daots Copia2 . [$descripcion2] [$creoCopia2]";
    }else{
        $descripcion2 = trim($asu) . "[cc. ".substr($nombreDestCopia2,0,35)."]";
        $creoAnexo2 = crearAnexo($noRadicado,$radicadoAsociado,"","","","",$loginUsuarioDestino,702,1, $descripcion2, $ruta);
        $creoCopia2 = crearOtroDestino($db,$noRadicado,$loginUsuarioDestino
            ,$nombreDestCopia2,$copia2['documento'],$copia2['muni'],$copia2['codep']
            ,$copia2['idpais'],$copia2['idcont']
            ,$copia2['direccion'],$copia2['telefono'],$copia2['mail'],0,0,0,0,$numeroCopia,$nombreDestCopia2);                
        $respuesta['datosAnexoCopia2'] = "$creoAnexo2 . [$descripcion2] [$creoCopia2]";
    }
 
    //$respuesta['numeroExpediente'] = $numeroExpediente;
     
    if($numeroExpediente){
        include_once("../include/tx/Expediente.php");
        $db = new ConnectionHandler("..",'WS');
        $expediente = new Expediente($db);
        $secRadicado = substr($noRadicado, -6, -1);
        
        if($numeroExpediente==1) $numeroExpediente = date('Y').'260227001'.$secRadicado.'E';
        $respuesta['datosEnviadosIncluirEnExpediente'] = "NumeroExp [$numeroExpediente], NoRadicado [$noRadicado],CodDependencia [$coddepe] ,CodUsuairo [$usuaCodi],DocUsuario [$docUsuarioDestino]";
        $insertExpediente = $expediente->insertar_expediente($numeroExpediente,$noRadicado,$coddepe,$usuaCodi,$docUsuarioDestino);
           // return $respuesta;
        $respuesta['numeroExpediente'] = $numeroExpediente;
        $respuesta['infNumeroExpediente'] = "$noRadicado [".$expediente->getDescError()."]";
        if($radicadoAsociado>=2){
          $insertExpediente = $expediente->insertar_expediente($numeroExpediente,$radicadoAsociado,$coddepe,$usuaCodi,$docUsuarioDestino);
          $respuesta['infNumeroExpedientePadre'] = "$radicadoAsociado [".$insertExpediente."]";        
        }
    }

    $respuesta['numeroRadicado'] = $noRadicado;
    return $respuesta;
}


/**
 * Esta Busca uno o varios radicado y devuelve la respectiva informacion
 * @param $numeroRadicado, este parametro es el correo electronico del usuario
 * @param $tipoBusqueda, Indica si Busca radicados de un Expediente especifico
 * @author Fundacion Correlibre
 * @return String Numero de Radicado
 */

function buscarRadicados($numeroRadicado, $tipoBusqueda)
{
 //$respuesta["numeroRadicado"] = "201055555,$numeroRadicado";
 //return ($respuesta);
 global $ruta_raiz;
 //$respuesta = array();
 //return "Entro a radicar $numeroExpediente";
 
 
	 if(trim($numeroRadicado)) {
	 //$respuesta["numeroRadicado"] = "201055555,xxxxxx, $numeroRadicado";
	 //return $respuesta;
	 include_once( "../include/db/ConnectionHandler.php" );
	 $db = new ConnectionHandler("..",'WS') ;
	 $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	 if($tipoBusqueda=="Expediente"){
	 $consultaRadicado ="select r.RADI_NUME_RADI
	 						, r.RADI_PATH
							, r.RADI_DEPE_RADI
							, r.RA_ASUN
							, r.RADI_FECH_RADI
							, exp.SGD_EXP_NUMERO
						  FROM RADICADO r, SGD_EXP_EXPEDIENTE exp
						  WHERE 
						  r.RADI_NUME_RADI=exp.RADI_NUME_RADI AND
						  exp.SGD_EXP_NUMERO LIKE '%".$numeroRadicado."%'";
	 }else{
	 $consultaRadicado ="select r.RADI_NUME_RADI
	 						, r.RADI_PATH
							, r.RADI_DEPE_RADI
							, r.RA_ASUN
							, r.RADI_FECH_RADI
						  FROM RADICADO r
						  WHERE 
						  r.RADI_NUME_RADI LIKE '%".$numeroRadicado."%'";
	}
	 
	    //return $respuesta;
	 $rs1=$db->conn->Execute( $consultaRadicado );
	 //$radicadoAsunto=$rs1->fields["RA_ASUN"];
	 //$radicadoNumero=$rs1->fields["RADI_NUME_RADI"];
	 while(!$rs1->EOF){
	 	$respuesta["numeroRadicado"].=$rs1->fields["RADI_NUME_RADI"] .",";	
		$respuesta["numeroRadicado"].=$rs1->fields["RADI_FECH_RADI"] .",";	
		$respuesta["numeroRadicado"].=$rs1->fields["RADI_DEPE_RADI"] .",";	
		$respuesta["numeroRadicado"].=str_replace(","," ",$rs1->fields["RA_ASUN"]) .",";	
		$respuesta["numeroRadicado"].=$rs1->fields["RADI_NUME_HOJA"].",";	
		$respuesta["numeroRadicado"].="'".$rs1->fields["SGD_EXP_NUMERO"] ."',";	
	    $rs1->MoveNext();
	 }
	 $respuesta['error'] = "0";
	 if(!$radicadoNumero) {
	    $respuesta['error'] = "1";
	    $respuesta['descError'] = "(Error, No se encontro Un documento  En Orfeo connumero $numeroRadicado)";
	    return $respuesta;
	 }else{
	 	$respuesta["asunto"] = $radicadoAsunto;
		$respuesta["radicadoNumero"] = $radicadoNumero;
	    return $respuesta;
	 }
 
 }
 return $respuesta;
 
}

/**
 * Metodo crearOtroDestino  (En envios serian las copias)
 * Crea registros para otros destinatarios a un radicado,
 * invoca el metodo crearAnexo para incluir el documento en anexo.
 * Los tipos (SGD_DIR_TIPO) se marcan con 701, 702, . . .
 *
 * @autor Jairo Losada - Correlibre.org 2009/11, Modificacion 2010/03 DNP JHLC
 * @licencia GNU/GPL Version 3.0
 * 
 * @param objeto $db Objeto de la conexion a la base de datos en ADODB.
 * @param int $noRadicado Numero de Radicado al cual se le genera la copia.
 * @param string $loginUsuario Login del Usuario que Radica.
 * @param string $nombreDestinatario Nombre Destinatario o dignatario.
 * @param string $docDestinatario Documento de identidad del destinatario.
 * @param int $muniCodi Codigo municipio o Ciudad.
 * @param int $dptoCodi Codigo departamento o provincia.
 * @param int $idPais Codigo del pais destinatario.
 * @param int $idCont Codigo del Continente.
 * @param string $direccion Direccion de Destinatario.
 * @param string $tel,$mail,$fun=0,$oem=0,$esp=0,$sgdDirTipo,$nombre Datos del Remitente.
 * @return string Retorna el valor true si se ha creado correctamente.
 * 
 **/
function crearOtroDestino ($db,$noRadicado, $loginUsuario
                           , $nombreDestinatario,$docDestinatario,$muniCodi,$dptoCodi,$idPais,$idCont
                           ,$direccion,$tel,$mail,$fun=0,$oem=0,$esp=0,$ciu,$sgdDirTipo,$nombre="-"){
  $ADODB_COUNTRECS = true;
  global $ruta_raiz;
 if(!trim($asu)) $asu = "Sin Asunto";
 if(!trim($med)) $med = 1;
 
 include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );
 //return "Entro... $docUsuarioDestino";
 //return "-->".$docUsuarioRadicador;
    
 $db = new ConnectionHandler($ruta_raiz,'WS') ;
 $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  
  $record = array();
  //$record['SGD_TRD_CODIGO'] = $sgdTrd;
  $record['SGD_DIR_NOMREMDES'] = "'".$nombreDestinatario."'";
  $record['SGD_DIR_DOC'] = "'".$docDestinatario."'";
  $record['MUNI_CODI'] = $muniCodi;
  $record['DPTO_CODI'] = $dptoCodi;
  $record['ID_PAIS'] = $idPais;
  $record['ID_CONT'] = $idCont;
  $record['SGD_DOC_FUN'] = $fun;
  $record['SGD_OEM_CODIGO'] = $oem;
  $record['SGD_CIU_CODIGO'] = $ciu;
  $record['SGD_ESP_CODI'] = "'".$esp."'";
  $record['RADI_NUME_RADI'] = "'".$noRadicado."'";
  $record['SGD_SEC_CODIGO'] = 0;
  $record['SGD_DIR_DIRECCION'] = "'".$direccion."'";
  $record['SGD_DIR_TELEFONO'] = "'".trim($telefono)."'";
  $record['SGD_DIR_MAIL'] = "'".$mail."'";
  $record['SGD_DIR_TIPO'] = $sgdDirTipo;
  $nextval=$db->conn->nextId("sec_dir_direcciones");
  $record['SGD_DIR_CODIGO'] = $nextval;
  $i=0;
  foreach($record as $key => $valor) {
    if($i>=1) $campos = $campos.",";
    if($i>=1) $valores = $valores.",";
    $i++; 
    $campos = $campos .$key;
    $valores = $valores .$valor;
    
  }
  $sql = "insert into SGD_DIR_DRECCIONES($campos) VALUES($valores)"; 
  //return $sql;
  //return $campos;
  //return $db->conn->insert("SGD_DIR_DRECCIONES", $record);
  //$insertSQL = $db->conn->insert("SGD_DIR_DRECCIONES", $record);
  
  $insertRs = $db->conn->Execute($sql);
  //return "Rta <$sql>--> ".$insertRs;
  if($insertRs==-1){
    return "-1, Error";
  }else{
    return "1";
  }
  
}

/**
 * Esta funcion permite anular un  radicado  en Orfeo
 * @param $checkValue, es un arreglo con los numeros de radicado
 * @author Donaldo Jinete Forero
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */
function logRadicadosCerrados($noRadicado,$fechaInicial,$fechaFinal){
	//return "sdss <hr><<<<";  
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
	$iSql = "Select *
				from HIST_EVENTOS WHERE
				SGD_TTR_CODIGO = 77 ";
	if($noRadicado>=1000){
	  $iSql .= " AND RADI_NUME_RADI=$noRadicado ";
	}
	if(strlen($fechaInicial) >=5 and strlen($fechaFinal)>=5){
	  $iSql .= " AND HIST_FECH>='$fechaInicial 00:00' AND HIST_FECH<='$fechaFinal 23:59' ";
	}
	$iSql .= " ORDER BY HIST_FECH ";
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs  = $db->conn->Execute($iSql);
	if(!$rs->EOF && $rs){
		while(!$rs->EOF){
		 $i++;
		 $textLog .= "<$i>". $rs->fields["HIST_FECH"]. ", ". $rs->fields["RADI_NUME_RADI"]. ", ". $rs->fields["USUA_DOC"]. ",". $rs->fields["DEPE_CODI"].", ". str_replace("<br>","", $rs->fields["HIST_OBSE"]) . "</$i>\n";
		 $rs->MoveNext();
		}
	}else{
		$textLog = "No se encontraron Resultados";
	}
	$respuesta['textoLog']=$textLog;
	return $respuesta;
}

/**
 * Funcion permite modificar un radicado en Orfeo
 * @param $destinos, arreglo de destinatarios destinatarios,predio,esp
 * @param $asu, Asunto del radicado
 * @param $med, Medio de radicacion
 * @param $ane, descripcion de anexos
 * @param $coddepe, codigo de la dependencia
 * @param $tpRadicado, tipo de radicado
 * @param $cuentai, cuenta interna del radicado
 * @param $tip_rem
 * @param $tdoc  // Cedula, Nit... 
 * @param $tip_doc // Tipo de DOcumneto
 * @author Jairo Losada  - DNP 2010
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */

function modificarRad($nuRad
	,$destinatarioOrg
	,$predioOrg
	,$espOrg
	,$asu
	,$med
	,$ane
	,$cuentai
	,$usuaDoc
	,$entidad
	){

    global $ruta_raiz;
    $ruta_raiz = "../";
    include_once( $ruta_raiz."/include/db/ConnectionHandler.php" );
    include($ruta_raiz."/include/tx/Tx.php") ;
    include($ruta_raiz."/include/tx/Radicacion.php") ;
    include($ruta_raiz."/class_control/Municipio.php") ;
    include_once($ruta_raiz."/include/tx/Historico.php");
    
    $db = new ConnectionHandler($ruta_raiz,'WS') ;
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $hist = new Historico($db);
    $tmp_mun = new Municipio($db) ;
    $rad = new Radicacion($db) ;
    
    $tmp_mun->municipio_codigo($destinatario["codep"],$destinatario["muni"]) ;
    $rad->radiCuentai = "'".trim($cuentai)."'";
    //$rad->eespCodi =  $esp["documento"] ;
    $rad->eespCodi =  0 ;
    $rad->mrecCodi =  $med;
    //$rad->radiFechOfic =  date("Y-m-d");
    $rad->descAnex = $ane ;
    $rad->usuaDoc = $infoUsuario["usua_doc"];
    $codTx = 2 ;
    $flag = 1 ;
    $rad->usuaCodi=$radi_usua_actu;
    $rad->dependencia=trim($coddepe) ;
	$nombre = $destinatarioOrg['nombre'] ." ".$destinatarioOrg['prim_apel'];
	if($entidad) $rad->eespCodi = $entidad; else $rad->eespCodi = 0;
	$sql_ret = $rad->updateRadicado($nuRad);
    if($destinatarioOrg){
	 $rad->grbNombresUs="'".$destinatarioOrg['nombre'] ." ".$destinatarioOrg['prim_apel']. " ".$destinatarioOrg['seg_apel']."'";
	 $rad->dirNombre = "'".$destinatarioOrg['nombre'] ." ".$destinatarioOrg['prim_apel']."'";
	 $rad->ccDocumento = $destinatarioOrg['cc_documento'];
	 $rad->muniCodi = $destinatarioOrg['muni'];
	 $rad->dpto_tmp1 = $destinatarioOrg['codep'];
	 $rad->idPais = $destinatarioOrg['idPais'];
	 $rad->idCont = $destinatarioOrg['idCont'];
	 $rad->direccion = "'".$destinatarioOrg['direccion']."'";
	 $resUp1 =  $rad->insertDireccion($nuRad, 1,1);
	}
	if($predioOrg){
	
	 $rad->grbNombresUs="'".$destinatarioOrg['nombre'] ." ".$destinatarioOrg['prim_apel']. " ".$destinatarioOrg['seg_apel']."'";
	 $rad->dirNombre = "'".$destinatarioOrg['nombre'] ." ".$destinatarioOrg['prim_apel']."'";
	 $rad->ccDocumento = $destinatarioOrg['cc_documento'];
	 $rad->muniCodi = $destinatarioOrg['muni'];
	 $rad->dpto_tmp1 = $destinatarioOrg['codep'];
	 $rad->idPais = $destinatarioOrg['idPais'];
	 $rad->idCont = $destinatarioOrg['idCont'];
	 $rad->direccion = "'".$destinatarioOrg['direccion']."'";
	 
	 $resUp2 =  $rad->insertDireccion($nuRad, 1,2);
	}
	if($espOrg){
	 $rad->grbNombresUs="'".$espOrg['nombre'] ." ".$espOrg['prim_apel']. " ".$espOrg['seg_apel']."'";
	 $rad->dirNombre = "'".$espOrg['nombre'] ." ".$espOrg['prim_apel']."'";
	 $rad->ccDocumento = $espOrg['cc_documento'];
	 $rad->muniCodi = $espOrg['muni'];
	 $rad->dpto_tmp1 = $espOrg['codep'];
	 $rad->idPais = $espOrg['idPais'];
	 $rad->idCont = $espOrg['idCont'];
	 $rad->direccion = "'".$espOrg['direccion']."'";
	 //$resUp3 =  $rad->insertDireccion($nuRad, 1,3);
	}
	
    // Modificado SSPD 09-Enero-2010  Jaior Losada Correlibre
	if($sql_ret!=1) $msgRet = "Fallida"; else $msgRet = "Ok";
	if($resUp1!=1) $msgRes .= "(Fallida)"; else $msgRes = "(Ok)";
	if($resUp2!=1) $msgRes .= "(Fallida)"; else $msgRes .= "(Ok)";
	//$respuesta['error'] = "ERROR: Actualizacion Radicado($msgRet) Actualizacion Datos Remitentes$msgRes";
    $respuesta["radicado"] = $nuRad;
	//return $respuesta;
    if ($sql_ret=="-1"){
	 $respuesta['error'] = "ERROR: no genero un Numero de Secuencia o Inserto el radicado (($resUp1))";
    
    }else{
	  $consultaUsuario ="select USUA_CODI, DEPE_CODI from usuario where usua_doc='".$usuaDoc."' and usua_esta=1";
 	  $rs1=$db->conn->Execute( $consultaUsuario );
 	  $usuaCodi=$rs1->fields["USUA_CODI"];
	  $depeCodi=$rs1->fields["DEPE_CODI"];
	  $respuesta['error'] = "ERROR: Actualizacion Radicado($msgRet) Actualizacion Datos Remitentes$msgRes";
	  $radicadosSel[0] = $nuRad;
	  $codTx = 78;
	  $servidorIP = $_SERVER['REMOTE_ADDR'];
	  $datosMod = "$asu
		,Med: $med
		,Anexos: $ane
		,Ref:$cuentai
		,Ent: $entidad ,D1:". $nombre;
	  $comentario =  substr("(Desde IP $servidorIP)<br>".htmlspecialchars($datosMod) ,1 ,400);
	  $radHist = $hist->insertarHistorico($radicadosSel,  $depeCodi , $usuaCodi, $depeCodi, $usuaCodi, $comentario, $codTx);
	}
	return $respuesta;
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
 * @param $tdoc  // Cedula, Nit... 
 * @param $tip_doc // Tipo de DOcumneto
 * @param $carp_codi
 * @param $carp_per 
 * @author Donaldo Jinete Forero
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */

function radicarDocumento($file,$filename,$correo,$destinatarioOrg,$predioOrg,$espOrg,$asu,$med,$ane,$coddepe,
$tpRadicado,$cuentai,$radi_usua_actu,$tip_rem,$tdoc,$tip_doc,$carp_codi,$carp_per,$usuaLogin,$radicadoAsociado=0)
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
    $ruta_raiz = "../";
    include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );
    include(RUTA_RAIZ."include/tx/Tx.php") ;
    include(RUTA_RAIZ."include/tx/Radicacion.php") ;
    include(RUTA_RAIZ."class_control/Municipio.php") ;
    //include(RUTA_RAIZ."class_control/anexo.php") ;
    include_once(RUTA_RAIZ."include/tx/Historico.php");
    
    $db = new ConnectionHandler($ruta_raiz,'WS') ;
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $hist = new Historico($db);
    $tmp_mun = new Municipio($db) ;
    $rad = new Radicacion($db) ;
    
    $tmp_mun->municipio_codigo($destinatario["codep"],$destinatario["muni"]) ;
    $rad->radiTipoDeri = 0;
    $rad->radiCuentai = "'".trim($cuentai)."'";
    //$rad->eespCodi =  $esp["documento"] ;
    $rad->eespCodi =  $destinatario["documento"];
    $rad->mrecCodi =  $med;
    $rad->radiFechOfic =  date("Y-m-d");
    if(!$radicadopadre)  $radicadopadre = null;
    $rad->radiNumeDeri = $radicadoAsociado;
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
    $rad->radiPath = "NULL";
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
    // tenga permiso para generar un tipo de radicado especï¿½fico (en especial
    // resoluciones) hasta desligar Sancionados de Orfeo.
    // Plazo: Primer trimestre 2009.
    //$sqlSecuencia .= ' AND U.USUA_PRAD_TP'.$tpRadicado.' <> 0';
	
    $rsSecuencia = $db->conn->Execute( $sqlSecuencia );
    
    //return "Prueba 1 - $insertSQL <hr> $sqlSecuencia ->" .$infoUsuario["usua_codi"] ."<hr>". $rsSecuencia->fields[1];
    //return $tpRadicado ." --- ". $rsSecuencia->fields['DEPE_RAD_TP'];
    //return "<hr> $sqlSecuencia <hr>Radicado --> $noRad ---> ".$rsSecuencia->fields['DEPE_RAD_TP'];
    //return "--->".$noRad."<----";
    
    $noRad = $rad->newRadicado( $tpRadicado, $rsSecuencia->fields['DEPE_RAD_TP'] );
    
    
    //return $noRad;
    $nurad = trim($noRad) ;
    
    //$sql_ret = $rad->updateRadicado($nurad,"/".date("Y")."/".$coddepe."/".$noRad.".pdf");
    
    if ($noRad=="-1")
    {
     return "ERROR: no genero un Numero de Secuencia o Inserto el radicado";		
    }
    
    $radicadosSel[0] = $noRad;
    $codTx = 64;
    $servidorIP = $_SERVER['REMOTE_ADDR'];
    $comentario =  "(Desde IP $servidorIP)";
    $hist->insertarHistorico($radicadosSel,  $coddepe , $radi_usua_actu, $coddepe, $radi_usua_actu, "$comentario", $codTx);
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
	  $servidorIP = $_SERVER['REMOTE_ADDR'];
	  //return $nurad . " Desde PC ->" . $servidorIP;
	  return $nurad;
    }break;
    case 2:{	//Update Exitoso
      $newId = false;
    }break;
    case 0:{	//Error Transaccion.
      return  "$nurad  esp-> $sgd_esp_codigo ERROR: No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES UNO -- $conexion->querySql -- $res ";
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
  if ($rsg->EOF) {
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
    }else{
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

if($documento_us1!='' and $cc!=''){
    $sgd_ciu_codigo=0;
    $sgd_oem_codigo=0;
    $sgd_esp_codigo=0;
    $sgd_fun_codigo=0;

    
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
    $servidorIP = $_SERVER['REMOTE_ADDR'];
    return $retval . " Desde PC ->" . $servidorIP;
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

function uploadImagenRadicado($numRadicado,$ext,$file){
    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz,'WS');
    $sql="SELECT RAPI_DEPE_RADI,RADI_FECH_OFIC FROM RADICADO WHERE RADI_NUME_RADI='{$numRadicado}'";
    $rs=$db->conn->Execute($sql);
    if(!$rs->EOF){
        $year=substr($numRadicado,0,4);
        $depe=substr($numRadicado,4,3);
        $path="../bodega/{$year}/{$depe}/{$numRadicado}.{$ext}";
        $update="UPDATE RADICADO SET RADI_PATH='{$path}' where RADI_NUME_RADI='{$numRadicado}'";
        //error_reporting(7);
        //return "<hr> El path que se va es $path";
        $resGrabarArchivo = UploadFile($file,$path);
        //return $resGrabarArchivo ."<---> {$path}";
        if($resGrabarArchivo=='exito'){
        if($db->conn->Execute($update))
        {
         $p = pathinfo($PHP_SELF);
         //return "Ok ". $p["dirname"] . "----".$pathActual['dirname'];
         return "Fila Asociada Correctamente a Radicado No. $numRadicado";
        }else{
            
        }
        }else{
          return " No se Pudo copiar el Archivo $file , $numRadicado";
          throw new Exception("ERROR no se puede copiar el archivo");
        }
    }else{
         return " Fallo al Cargar Fila - No se Encontro El Numero de Radicado {$numRadicado}<hr>";
         throw new Exception("ERROR El radicado no existe");
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
 //return 0;
 //$db = new ConnectionHandler($ruta_raiz);
 $db = new ConnectionHandler($ruta_raiz,'WS');
 $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
 $upperMail=strtoupper($usuaLogin);
 //$lowerMail=strtolower($usuaLogin);
 $sql="SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB 
       FROM USUARIO 
       WHERE  USUA_LOGIN='{$usuaLogin}' and usua_esta=1";
       //return $sql;
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

#========================================================================
# INICIO funciones para generacion de radicados de suifp
#========================================================================

// Consulta si el radicado esta incluido en el expediente.
function validaExisteEnExp(
    $expediente,
    $radicado,
    $numExpe){
	$existeEnExp = $expediente->expedientesRadicado($radicado);
	foreach ($existeEnExp as $value){			
		if ($value == $numExpe){
			return true; 	//existe en el expediente
		}				
	}
	return false; //No existe en el expediente
}	

function creExpeSuift(
      $ccusua 
    , $nurad
    , $bpin
    , $nomProyect
    , $entidad
    , $sector
    , $accion = null
    ){
	$ruta_raiz = RUTA_RAIZ;
	include_once(RUTA_RAIZ."include/db/ConnectionHandler.php");
    include_once(RUTA_RAIZ."include/tx/Expediente.php");
	include_once(RUTA_RAIZ."include/tx/Historico.php");

    $serie      = '008'; //Conceptos
    $subSerie   = '025'; //Conceptos Tecnicos sobre proyectos de Inversión.
    $date1      = date("n/j/Y");
    $ano        = date('Y');
    $mensaje1   = "Creado desde suifp ws: ";
    $db         = new ConnectionHandler(RUTA_RAIZ);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $expediente = new Expediente($db);
    $Historico  = new Historico($db);

    //*******************************************************
    //*****************  validar datos **********************
    //*******************************************************
    
    //validar campos en blanco
    if(empty($ccusua))
        return 'ERROR: Argumentos en blanco (usuario) creExpeSuift';
    if(empty($bpin)) 
        return 'ERROR: Argumentos en blanco (bpin) creExpeSuift';
    if(empty($nurad)) 
        return 'ERROR: Argumentos en blanco (radicado) creExpeSuift';
    if(empty($nomProyect)) 
        return 'ERROR: Argumentos en blanco (nombre proyecto) creExpeSuift';
    if(empty($entidad)) 
        return 'ERROR: Argumentos en blanco (entidad) creExpeSuift';
    if(empty($sector))
        return 'ERROR: Argumentos en blanco (sector) creExpeSuift';

    $sqlus      = " SELECT 
                        USUA_LOGIN, DEPE_CODI, USUA_CODI
                    FROM 
                        USUARIO
                    WHERE 
                        USUA_DOC = '$ccusua'";

    $resul     = $db->conn->Execute($sqlus);

    if(!$resul){
        return 'ERROR: No existe el usuario creExpeSuift';
    }

    $depen       = $resul->fields["DEPE_CODI"];
    $codusua     = $resul->fields["USUA_CODI"];
    $login       = $resul->fields["USUA_LOGIN"];
    $radicados[] = $nurad;
    $rad_anex    = array_filter($radicados);

    //validar variable $ccusua 
    $isq_sect = "SELECT 
                    COUNT(*) as contador
                 FROM 
                    SGD_EXP_SECTORES
                 WHERE 
                    EXP_SECT_ID = $sector";

    $resul     = $db->conn->Execute($isq_sect);
    if(empty($resul->fields["contador"])){ 
        return 'ERROR: No existe el sector CreExpSuifp';
    }

	$sqlT  = "
		SELECT 
			SGD_EXP_NUMERO AS EXPEDIENTE
		FROM 
				SGD_SEXP_SECEXPEDIENTES
		WHERE 
				";

    if($accion == '5'){
        $sqlT .= "SGD_SEXP_PAREXP1 like '$nomProyect'"; 
    }else{
        $sqlT .= "SEXP_BPIN = '$bpin'";
    }

	$ressal   = $db->conn->Execute($sqlT);
	$numExpe  = $ressal->fields["EXPEDIENTE"];

    unset($sqlT);

	//si no existe creamos el expediente
	if(empty($numExpe)){
        if($accion == '5')unset($bpin);
		/**********************************************
		 * VALIDAR CREACION DEL EXPEDIENTE
		 **********************************************/
		$secExp	  = $expediente->secExpediente($depen
												,$serie
												,$subSerie
												,date(Y));

		if(!$secExp){
			return 'ERROR: No se genero consecutivo expediente';
		}

		while(strlen($secExp) < 5){	$secExp = '0'.$secExp;}

		//Numero del expediente si es automatico
		$numExpe = $ano.$depen.$serie.$subSerie.$secExp.'E';
		$numeroExpedienteE =
				$expediente->crearExpediente($numExpe,
												$nurad,
												$depen,
												$codusua,
												$ccusua,
												$ccusua,
												$serie,
												$subSerie,
												'false',
												"'$date1'",
												0,
												null,
												null);

		if(empty($numeroExpedienteE)){
			return 'ERROR: No se creo el expediente';
		}

		//cambiar el nombre del expediente				
		$insercioNomExp =
			$expediente->insert_ExpedienteNomb($numExpe, $nomProyect);	

        if($accion != '5'){
            //Agregar referencia bpin
            $expediente->insert_suifp($numExpe, $bpin, $sector, $entidad);	
        }


		//Al crear solo vamos a guardar el historico que indica que el
		//expediente entra a la primera etapa del proceso

		$observa     = $mensaje1.$serie."/".$subSerie ."/ Nombre: ".$nomProyect;
		$tipoTx      = 51;

		$Historico->insertarHistoricoExp($numExpe
											, $radicados
											, $depen
											, $codusua
											, $observa
											, $tipoTx
											,0);
    }

	foreach ($rad_anex as $actual){
		$existeEn = validaExisteEnExp($expediente, $actual, $numExpe);							
		if ($existeEn == false){
			$saliExp = $expediente->insertar_expediente($numExpe, $actual, $depen, $codusua, $codusua);								
            $rad_histo[] = $actual;
		}else{
			return 'ERROR: El radicado ya existe en el expediente';
		}
	}

	//si existen algun radicado grabado lo registramos
	//en el historico
	if(!empty($rad_histo)){
		$observa = "Se agrego el radicado al expediente desde suifp";                    
		$tipoTx = 53;			
		$Historico->insertarHistoricoExp($numExpe
										,$rad_histo
										,$depen
										,$codusua
										,$observa
										,$tipoTx
										,0);		         				
	}

	return $numExpe;
}

function radiDocuSuifp(
    $ccusua,
    $bpin,
    $empre,
    $accion,
    $numradPadre = NULL,
    $noSoliSuifp,
    $tiSoliSuifp,
    $htmlfile
    ){

    $ruta_raiz = RUTA_RAIZ;
    include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );
	include_once( RUTA_RAIZ."include/tx/Historico.php");	
    include_once( RUTA_RAIZ."include/tx/Tx.php");
    include_once( RUTA_RAIZ."include/tx/Radicacion.php");
    include_once( RUTA_RAIZ."class_control/Municipio.php");
    include_once( RUTA_RAIZ."class_control/anexo.php");
	include_once( RUTA_RAIZ."class_control/TipoDocumental.php");

    $db = new ConnectionHandler(RUTA_RAIZ);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $sqlFechaHoy = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
    $hist        = new Historico($db);
    $anexo       = new anexo($db);
    $rs          = new Tx($db);
    $trd         = new TipoDocumental($db);
    $Historico   = new Historico($db);

    $fecha_hoy 	 = Date("Y-m-d");
    $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);

    //Array para describir la accion
    //Descrip tipo documental - tipo documental - tipo de radicado
    $evento = array(array("Concepto", 3104, '3'),
                    array("Proyecto Registrado - Actualizado", 3040, '2'),
                    array("Proyecto Devuelto",1533, '1'),
                    array("Proyecto Reasignado a Tics", 3039, '3'),
                    array("Archivado", 3103, '2'),
                    array("Solicitud de tramite",0, '2'));

    //*******************************************************
    //*************  validar datos **************************
    //*******************************************************

    //validar campos en blanco
    if(empty($ccusua) || empty($bpin) || empty($empre) || 
        empty($noSoliSuifp) || empty($tiSoliSuifp) || empty($htmlfile)){
        return 'ERROR: Argumentos en blanco radiDocuSuifp';
    };

    //validar variable $accion 
    if(empty($evento[$accion])){
        return "ERROR: la accion no existe";
    };

    //validar variable $ccusua 
    $sqlus      = " SELECT 
                        USUA_LOGIN, 
                        DEPE_CODI, 
                        USUA_CODI,
                        USUA_NOMB,
                        USUA_DOC
                    FROM 
                        USUARIO
                    WHERE 
                        USUA_DOC = '$ccusua'";

    $resul     = $db->conn->Execute($sqlus);

    if($resul->EOF){ 
        return 'ERROR: No existe el usuario';
    }

    $coddepe       = $resul->fields["DEPE_CODI"];
    $radi_usua_actu= $resul->fields["USUA_CODI"];
    $login         = $resul->fields["USUA_LOGIN"];
    $usuanom       = $resul->fields["USUA_NOMB"];
    $usuadoc       = $resul->fields["USUA_DOC"];

    //validar variable $empre 
    $isql_consec   = " SELECT 
                            NOMBRE_DE_LA_EMPRESA, 
                            NIT_DE_LA_EMPRESA, 
                            DIRECCION, 
                            TELEFONO_1, 
                            EMAIL 
                        FROM 
                            BODEGA_EMPRESAS
                        WHERE 
                            IDENTIFICADOR_EMPRESA = $empre";

    $consult1 		= $db->conn->Execute($isql_consec);

    if($consult1->EOF){ 
        return 'ERROR: No existe la empresa';
    }
    $emp 	= $consult1->fields["NOMBRE_DE_LA_EMPRESA"];
    $ced 	= $consult1->fields["NIT_DE_LA_EMPRESA"];
    $dir 	= $consult1->fields["DIRECCION"];
    $tel 	= $consult1->fields["TELEFONO_1"];
    $email 	= $consult1->fields["EMAIL"];

    //validar radicado padre $numradPadre
    if(!empty($numradPadre)){
      $isql_radi = "SELECT 
                        COUNT(1) as contador
                      FROM 
                        RADICADO
                      WHERE 
                        RADI_NUME_RADI  = $numradPadre";

        $consult4 		= $db->conn->Execute($isql_radi);
        if(empty($consult4->fields["contador"])){ 
            return 'ERROR: No existe el radicado padre';
        }
    }


    //validar el tipo de solicitud
    $sqlus      = " SELECT 
                        EXP_SOLI_TIPO,
                        SGD_TPR_CODIGO
                    FROM 
                        SGD_EXP_SOLICITUDES
                    WHERE 
                        EXP_SOLI_ID = '$tiSoliSuifp'";


    $resul      = $db->conn->Execute($sqlus);
    $nomTipoSoli= $resul->fields["EXP_SOLI_TIPO"];
    $tipcod     = $resul->fields["SGD_TPR_CODIGO"];


    if($resul->EOF){ 
        return 'ERROR: No existe el tipo de solicitud radiDocuSuifp';
    }

    //Datos Necesario para generar un radicado
    $pais     = 170; //OK, codigo pais
    $cont     = 1; //id del continente
    $dep      = 11;
    $muni     = 1;
    $ced      = $ccusua; //cedula
    $sigla    = 'null';
    $mrecCodi = 1;
    $ddate    = date("d");
    $mdate    = date("m");
    $adate    = date("Y");
    $tipra    = $ent = $evento[$accion][2];
    $serie    = 8;
    $subserie = 25;
    $tdoco    = $evento[$accion][1];
    $docfun   = "''";
    
    if($accion == 5){
        if(empty($tipcod)){
            return 'ERROR: No existe el tipo de documento en sgd_tpr_codigo de radiDocuSuifp';
        }
        $tdoco    = $tipcod; 
        $serie    = 227;
        $subserie = 1;
    }

    // RADICADO
    $rad 				= new Radicacion($db);
    $rad->radiNumeDeri 	= empty($numradPadre)? 'null' : $numradPadre; //radicado padre
    $rad->radiTipoDeri 	= empty($numradPadre)? 1 : 0 ; // ok ????
    $rad->radiCuentai 	= "'$bpin'"; //Interna, Referencia
    $rad->mrecCodi 		= $mrecCodi; //3 internet
    $rad->radiFechOfic 	= "$ddate/$mdate/$adate"; //fecha radicado;
    $rad->radiPais 		= $pais; //codigo pais
    $rad->descAnex 		= '.'; //anexos
    $rad->raAsun 		= $evento[$accion][0].' '.$nomTipoSoli; //asunto
    $rad->ra_asun 		= htmlspecialchars($evento[$accion][0].' => '.$nomTipoSoli);
    $rad->despla 		= 0; //despla
    $rad->radiDepeActu 	= '999';//dependencia actual responsable
    $rad->radiUsuaActu 	= 1; //usuario actual responsable
    $rad->radiDepeRadi 	= $coddepe; //dependencia que radica
    $rad->usuaCodi 		= $radi_usua_actu; //usuario actual responsable
    $rad->dependencia 	= $coddepe; //dependencia que radica
    $rad->trteCodi 		= 0; //tipo de codigo de remitente
    $rad->tdocCodi 		= $tdoco; //tipo documental
    $rad->tdidCodi 		= 0; //ok, ????
    $rad->carpCodi 		= 0; //ok, carpeta entradas
    $rad->carPer 		= 0; //ok, carpeta personal
    $rad->trteCodi 		= 0; //ok, $tip_rem;
    $rad->radiPath 		= 'null';
    $rad->sgd_apli_codi = '0';
    $rad->usuaDoc 		= $ccusua;
    $codTx 				= 61;


    //Para crear el numero de radicado se realiza el siguiente procedimiento
    $isql_consec = "SELECT 
                        DEPE_RAD_TP$ent as secuencia 
                    FROM 
                        DEPENDENCIA 
                    WHERE 
                        DEPE_CODI = $coddepe";

    $creaNoRad   = $db->conn->Execute($isql_consec);
    $tpDepeRad   = $creaNoRad->fields["secuencia"];

    $noRad 				= $rad->newRadicado($ent, $tpDepeRad);

    $adate       = date("Y");

    if($numradPadre){
        $radano    = substr($numradPadre,0,4);
        $coddepe1   = substr($numradPadre,4,3);
    }else{
        $radano   = $adate;
        $coddepe1 = $coddepe;
    }

    $ruta   = $radano.$coddepe1."suifp_".rand(10000, 99999)."_".time().'.html'; //ruta anexos
    $ruta2  = "bodega/$radano/$coddepe1/docs/$ruta"; //donde se guarda el archivo 
    $ruta3  = "$radano/$coddepe1/docs/$ruta"; //ruta radicado

    if($noRad != -1){
        $isqlMunDep 		= " UPDATE 
                                    RADICADO 
                                SET DPTO_CODI   = $dep, 
                                    MUNI_CODI   = $muni,
                                    exp_soli_id = $tiSoliSuifp, 
                                    radi_nume_solicitud = $noSoliSuifp
                                WHERE  (RADI_NUME_RADI = $noRad)";

        $rsgMunDep 			= $db->conn->Execute($isqlMunDep);
        
        $nextval		= $db->nextId("sec_dir_direcciones");

        //Si la accion es 3 coloca como destinatario a informatica
        if($accion == 3){
            $isql_consec = "SELECT 
                                USUA_DOC 
                            FROM 
                                USUARIO 
                            WHERE 
                                DEPE_CODI     = 120
                                AND USUA_CODI = 1";

            $coRad       = $db->conn->Execute($isql_consec);
            $docfun      = $coRad->fields["USUA_DOC"];
            $empre       = 0; 
        }

        $isql 		    = "INSERT INTO SGD_DIR_DRECCIONES(	
                                    SGD_TRD_CODIGO,
                                    SGD_DIR_DOC,
                                    DPTO_CODI,
                                    MUNI_CODI,
                                    ID_PAIS,
                                    ID_CONT,
                                    SGD_DOC_FUN,
                                    SGD_OEM_CODIGO,
                                    SGD_ESP_CODI,
                                    RADI_NUME_RADI,
                                    SGD_SEC_CODIGO,
                                    SGD_DIR_DIRECCION,
                                    SGD_DIR_TELEFONO,
                                    SGD_DIR_MAIL,
                                    SGD_DIR_TIPO,
                                    SGD_DIR_CODIGO,
                                    SGD_DIR_NOMREMDES)
                            VALUES('1',
                                    '$ced',
                                     $dep,
                                     $muni,
                                     $pais,
                                     $cont,
                                     $docfun,
                                     NULL,
                                     $empre,
                                     $noRad,
                                     0,
                                    '$dir',
                                    '$tel',
                                    '$email',
                                     1,
                                     $nextval,
                                    '$emp')";

        $rsg = $db->conn->Execute($isql);
        if(!$rsg){
            return 'ERROR: No se registro dirección';
        }

        if(!empty($numradPadre)){

            $isqlMunDep 		= " UPDATE 
                                        RADICADO 
                                    SET DPTO_CODI= $dep, 
                                        MUNI_CODI = $muni
                                    WHERE  (RADI_NUME_RADI = $noRad)";

            $rsgMunDep 			= $db->conn->Execute($isqlMunDep);

            $auxnumero         = $anexo->obtenerMaximoNumeroAnexo($noRad);
            $archivoconversion = trim("1").trim(trim($noRad)."_".trim($auxnumero).".".trim($ext));

            do {
                $auxnumero+=1;
                $codigo=trim($numradPadre).trim(str_pad($auxnumero,5,"0",STR_PAD_LEFT));
            } while ($anexo->existeAnexo($codigo));
            $isql = "insert into anexos (   ANEX_RADI_NUME,
                                            RADI_NUME_SALIDA,
                                            ANEX_ESTADO,
                                            ANEX_CODIGO,
                                            ANEX_TIPO,
                                            ANEX_TAMANO,
                                            ANEX_SOLO_LECT,
                                            ANEX_CREADOR,
                                            ANEX_DESC,
                                            ANEX_NUMERO,
                                            ANEX_NOMB_ARCHIVO,
                                            ANEX_BORRADO,
                                            ANEX_SALIDA,
                                            SGD_DIR_TIPO,
                                            ANEX_DEPE_CREADOR,
                                            SGD_TPR_CODIGO,
                                            ANEX_FECH_ANEX,
                                            SGD_TRAD_CODIGO)
                                    values ($numradPadre,
                                            $noRad,
                                            3,
                                            $codigo,
                                             1,
                                             0,
                                            'S',
                                            '$login',
                                            'Anexo generado desde suifp',
                                            $auxnumero,
                                            '$ruta',
                                            'N',
                                             1,
                                             1,
                                            $coddepe,
                                            32,
                                            $sqlFechaHoy,
                                            $tipra)";

            $consult 		= $db->conn->Execute($isql);
        }

        $radicadosSel[0] = $noRad;
        $hist->insertarHistorico($radicadosSel, 
            $coddepe, 
            $radi_usua_actu, 
            $coddepe, 
            $radi_usua_actu, 
            "Se creo desde la IP: ".$_SERVER['REMOTE_ADDR']
            ." suiffp ", 64);

        $sqlE = "UPDATE RADICADO
                SET 
                    RADI_PATH = ('$ruta3')
                WHERE 
                    RADI_NUME_RADI = $noRad";

        $resul = $db->conn->Execute($sqlE);

        if(empty($resul)){
            return 'ERROR: No se anexo archivo';
        }

        file_put_contents('../'.$ruta2, $htmlfile);

        //TRD Para el nuevo radicado
        //Buscamos en la matriz el valor que une a la dependencia, serie, subserie, tipoDoc.
        $isqlTRD = "
                    select 
                        SGD_MRD_CODIGO
                    from 
                        SGD_MRD_MATRIRD
                    where 
                        DEPE_CODI 			= $coddepe
                        and SGD_SRD_CODIGO 	= $serie
                        and SGD_SBRD_CODIGO = $subserie
                        and SGD_TPR_CODIGO 	= $tdoco";
        
        $rsTRD = $db->conn->Execute($isqlTRD);			
            
        //Se crean dos variables por que la clase esta creada de esta manera
        //y no se cambiara en este momento.
        $codiTRDS[] = $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];    

        if(empty($codiTRD)){
            return "ERROR: La trd no existe para esta dependencia radiDocuSuifp "; 
        }

        $trd->insertarTRD($codiTRDS, $codiTRD, $noRad, $coddepe, $radi_usua_actu);			
        //guardar el registro en el historico de tipo documental.
        //permite controlar cambios del TD de un radicado
        
        $queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(											
                                        SGD_HMTD_FECHA,
                                        RADI_NUME_RADI,
                                        USUA_CODI,
                                        SGD_HMTD_OBSE,
                                        USUA_DOC,
                                        DEPE_CODI,
                                        SGD_MRD_CODIGO)
                            VALUES(
                                $sqlFechaHoy,
                                $noRad,
                                $radi_usua_actu,
                                'El usuario: $usuanom Cambio el tipo de documento',
                                $usuadoc,
                                $coddepe,
                                '$codiTRD')";
        
        $db->conn->Execute($queryGrabar);


        //Actulizar la TD en el radicado					
        $upRadiTdoc	=	"UPDATE 
                            RADICADO
                        SET  
                            TDOC_CODI = $tdoco
                        WHERE 
                            radi_nume_radi = $noRad";
        
        $db->conn->Execute($upRadiTdoc);

        $observa 	= "	TRD por: Usuario: $usuanom
                        desde la IP: ".
                        $_SERVER['REMOTE_ADDR']." suiffp";
        
        $radicadoArr[] = $noRad;

        $radiModi  = $Historico->insertarHistorico(	$radicadoArr,
                                                    $coddepe,
                                                    $radi_usua_actu,
                                                    $coddepe,
                                                    $radi_usua_actu,
                                                    $observa,
                                                    32);	


        if($accion == 0 || $accion == 2 || $accion == 3){
            $hist->insertarHistorico($radicadosSel, 
                $coddepe, 
                $radi_usua_actu, 
                $coddepe, 
                $radi_usua_actu, 
                "Marcado como impreso desde  la IP: ".$_SERVER['REMOTE_ADDR']
                ." suiffp ", 58);
        }

        $hist->insertarHistorico($radicadosSel, 
            $coddepe, 
            $radi_usua_actu, 
            $coddepe, 
            $radi_usua_actu, 
            "Se archivo documento desde la IP: ".$_SERVER['REMOTE_ADDR']
            ." suiffp ", 13);


    }else{
            return 'ERROR: No se creo radicado';
    }

    $serverURL     = "http://".$_SERVER['SERVER_NAME']."/$ruta2";
    return $serverURL.' | '.$noRad; 
}

function reasigarSuifp(
    $radNo,
    $usuDesti,
    $tdoc = NULL){ 

    $ruta_raiz = RUTA_RAIZ;
	include_once(RUTA_RAIZ."include/db/ConnectionHandler.php");
	include_once(RUTA_RAIZ."/include/tx/Historico.php");	
	include_once(RUTA_RAIZ."/class_control/TipoDocumental.php");
	include_once(RUTA_RAIZ."include/tx/Tx.php");

    $v             = 0;
    $serie         = 227;
    $subSerie      = 1;
    $radicadoArr[] = $radNo;

    if(empty($usuDesti) || empty($radNo) ){
        return 'ERROR: Argumentos en blanco ReasignarSuifp';
    };   

    $db        = new ConnectionHandler(RUTA_RAIZ);
    $rs        = new Tx($db);
    $trd       = new TipoDocumental($db);
    $Historico = new Historico($db);

    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $sqUsuAc = "SELECT 
                    U.USUA_LOGIN, 
                    U.USUA_CODI, 
                    U.DEPE_CODI,
                    U.USUA_NOMB,
                    U.USUA_DOC
                FROM 
                    RADICADO R, 
                    USUARIO U
                WHERE 
                    RADI_NUME_RADI = $radNo
                    AND R.RADI_USUA_ACTU = U.USUA_CODI
                    AND R.RADI_DEPE_ACTU = U.DEPE_CODI";
    
    $resActu    = $db->conn->Execute($sqUsuAc);

    if($resActu->EOF){
        return "ERROR: No existe el radicado $radNo";
    }

    $Acoddepe    = $resActu->fields["DEPE_CODI"];
    $AcodusuaO   = $resActu->fields["USUA_CODI"];
    $Alogin      = $resActu->fields["USUA_LOGIN"];
    $Asuanom     = $resActu->fields["USUA_NOMB"];
    $Asuadoc     = $resActu->fields["USUA_DOC"];

    $prge   = " SELECT 
                    COUNT(1) AS EXISTE
                FROM 
                    SGD_RDF_RETDOCF
                WHERE 
                    RADI_NUME_RADI = $radNo";

    $resprge   = $db->conn->Execute($prge);
    $resuprge  = $resprge->fields["EXISTE"];
    
    if(empty($tdoc) && empty($resuprge)){
        return "ERROR: No tiene trd, no se puede reasignar $radNo";
    }

    if(!empty($tdoc)){
        $fecha_hoy 		= Date("Y-m-d");
        $sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);

        $sqlhis		="	SELECT
                             SE.SGD_SRD_DESCRIP +
                             '/'+ SU.SGD_SBRD_DESCRIP +
                             '/'+TD.SGD_TPR_DESCRIP AS TRD_ANTERIOR
                            
                        FROM
                             SGD_RDF_RETDOCF      SG
                            ,SGD_MRD_MATRIRD      MR
                            ,SGD_SBRD_SUBSERIERD  SU
                            ,SGD_SRD_SERIESRD     SE
                            ,SGD_TPR_TPDCUMENTO   TD
                            
                        WHERE
                            SG.RADI_NUME_RADI      = $radNo
                            AND MR.SGD_MRD_CODIGO  = SG.SGD_MRD_CODIGO
                            AND MR.SGD_SBRD_CODIGO = SU.SGD_SBRD_CODIGO
                            AND MR.SGD_SRD_CODIGO  = SU.SGD_SRD_CODIGO
                            AND MR.SGD_SRD_CODIGO  = SE.SGD_SRD_CODIGO
                            AND MR.SGD_TPR_CODIGO  = TD.SGD_TPR_CODIGO";
        
        $resultHis	= $db->conn->Execute($sqlhis);			
        $histTrd 	= $resultHis->fields['TRD_ANTERIOR'];			

        //Buscamos en la matriz el valor que une a la dependencia, serie, subserie, tipoDoc.
        $isqlTRD = "
                    select 
                        SGD_MRD_CODIGO
                    from 
                        SGD_MRD_MATRIRD
                    where 
                        DEPE_CODI 			= $Acoddepe
                        and SGD_SRD_CODIGO 	= $serie
                        and SGD_SBRD_CODIGO = $subSerie
                        and SGD_TPR_CODIGO 	= $tdoc";
        
        $rsTRD = $db->conn->Execute($isqlTRD);			
            
        //Se crean dos variables por que la clase esta creada de esta manera
        //y no se cambiara en este momento.
        $codiTRDS[] = $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];    
        
        $trd->insertarTRD($codiTRDS, $codiTRD, $radNo, $Acoddepe, $AcodusuaO);			
        //guardar el registro en el historico de tipo documental.
        //permite controlar cambios del TD de un radicado
        
        $queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(											
                                        SGD_HMTD_FECHA,
                                        RADI_NUME_RADI,
                                        USUA_CODI,
                                        SGD_HMTD_OBSE,
                                        USUA_DOC,
                                        DEPE_CODI,
                                        SGD_MRD_CODIGO)
                            VALUES(
                                $sqlFechaHoy,
                                $radNo,
                                $AcodusuaO,
                                'El usuario: $Asuanom Cambio el tipo de documento',
                                $Asuadoc,
                                $Acoddepe,
                                '$codiTRD')";
        
        $db->conn->Execute($queryGrabar);
 

        //Actulizar la TD en el radicado					
        $upRadiTdoc	=	"UPDATE 
                            RADICADO
                        SET  
                            TDOC_CODI = $tdoc
                        WHERE 
                            radi_nume_radi = $radNo";
        
        $db->conn->Execute($upRadiTdoc);

	 	$observa 	= "	Cambio TRD por: Usuario: $Asuanom
						TRD Anterior: $histTrd desde la IP: ".
                      $_SERVER['REMOTE_ADDR']." suiffp";
        
        $radiModi  = $Historico->insertarHistorico(	$radicadoArr,
                                                    $Acoddepe,
                                                    $AcodusuaO,
                                                    $Acoddepe,
                                                    $AcodusuaO,
                                                    $observa,
                                                    32);	
    }

    //Se recorren los destinatarios
    $desti = explode("@", $usuDesti); 

    foreach($desti as $usuDesti){
        $usuDesti   = trim($usuDesti);
        $sqlus      = " SELECT 
                            USUA_LOGIN, 
                            DEPE_CODI, 
                            USUA_CODI
                        FROM 
                            USUARIO
                        WHERE 
                            USUA_DOC = '$usuDesti'";

        $resul     = $db->conn->Execute($sqlus);

        if(!$resul){ 
            return 'ERROR: No existe el usuario';
        }else{
            $Dcoddepe    = $resul->fields["DEPE_CODI"];
            $DcodusuaO   = $resul->fields["USUA_CODI"];
            $Dlogin      = $resul->fields["USUA_LOGIN"];

            $carp_codi      = 0;
            $codTx          = 9;
            $observa        = "Webservice suifp desde 
                               la IP: ".$_SERVER['REMOTE_ADDR'];
            $tomarNivel     = "no";

            # busca si el usuario tiene el raiz del documento
            $destinoEnRaiz = $rs->busca_asignados_raiz($radicadoArr, $Dcoddepe, $DcodusuaO);
            # busca si el usuario tiene un derivado del documento
            $destinoEnDerivado = $rs->busca_asignados_derivado($radicadoArr, $Dcoddepe, $DcodusuaO);

            if(empty($v)){
                if (!$destinoEnRaiz){
                    $rs->reasignar($radicadoArr, $Alogin, $Dcoddepe, $Acoddepe, $DcodusuaO, $AcodusuaO, $tomarNivel, $observa, $codTx, $carp_codi);
                }else{
                    $obs = "Finaliza derivado por que el usuario ya tenia uno. ".$observa;
                    $rs->hist_derivado($radicadoArr, $Dcoddepe, $Acoddepe, $DcodusuaO, $AcodusuaO, $obs, $codTx);
                } 
            }else{
                if(!$destinoEnDerivado){
                    $rs->crea_derivado($radicadoArr, $Dcoddepe, $Acoddepe, $DcodusuaO, $AcodusuaO, $observa, $codTx);
                }else{
                    $obs = "Finaliza derivado por que el usuario ya tenia uno. ".$observa;
                    $rs->hist_derivado($radicadoArr, $Dcoddepe, $Acoddepe, $DcodusuaO, $AcodusuaO, $obs, $codTx);
                } 
            }
        }
        $v++;
    }
    return "ok: se realizo el traslado de radicados";
}

function proscSuiffp(
    $numusua,
    $bpin,
    $empre,
    $accion,
    $nomProyect,
    $sector,
    $noSoliSuifp,
    $tiSoliSuifp,
    $numradPadre,
    $htmlfile 
    ){

    $ruta_raiz = RUTA_RAIZ;
    include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );

    $db = new ConnectionHandler(RUTA_RAIZ);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    #Funcion crear radicado
    $nodeBpin = explode('@', $bpin);      
    $nodeNomb = explode('@', $nomProyect);      

	if(count($nodeBpin) != count($nodeNomb)){
        return "ERROR: El numero de bpin y de nombres no es igual"; 
		die;
    }

    $desc = str_replace("\t", "", str_replace("\r", "", str_replace("\n", "", str_replace("@", " - ", $bpin))));

    //validar campos en blanco
	if(empty($numusua)){
        return 'ERROR: Argumentos en blanco (usuario)';
		die;
	}
	if(empty($bpin)){
        return 'ERROR: Argumentos en blanco (bpin)';
		die;
	} 
	if(empty($empre)){
        return 'ERROR: Argumentos en blanco (entidad)';
		die;	
	} 
	if(empty($sector)){
        return 'ERROR: Argumentos en blanco (sector)';
		die;
	}

    $sqlus      = " SELECT 
                        USUA_LOGIN, DEPE_CODI, USUA_CODI
                    FROM 
                        USUARIO
                    WHERE 
                        USUA_DOC = '$numusua'";

    $resul     = $db->conn->Execute($sqlus);

    if(!$resul){
        return 'ERROR: No existe el usuario';
		die;
    }

    //Para la accion 5 no se recibe usuario radicador
    //por esta razon se creo uno y se asigna para
    //generar el radicado automaticamente.
    if($accion == '5'){
        $numusua = 22222222; 
    }

    $radica   = radiDocuSuifp($numusua, $desc, $empre, $accion, trim($numradPadre), $noSoliSuifp, $tiSoliSuifp, $htmlfile);
    $splitra  = explode('|', $radica);      
    $radicado = preg_replace("[^0-9]", "", $splitra[1]);


    if(empty($radicado)){
        return $radica;
		die;
    }
    
	for ($i = 0; $i < count($nodeBpin); $i++) {
		if(empty($expediente)){
			$expediente = creExpeSuift($numusua, $radicado, trim($nodeBpin[$i]), trim($nodeNomb[$i]) ,$empre, $sector);
            //para las acciones numero 5 se crea un expediente adicional con el nombre de expediente
            //igual al tipo de solicitud
            if($accion == '5'){
                $noSoliSuifp = 'Solicitud de tramite No. '.$noSoliSuifp;
			    $expediente .= " ".creExpeSuift($numusua, $radicado, 'NINGUNO', $noSoliSuifp, $empre, $sector,$accion);
            }
		}else{
			$expediente .= " ". creExpeSuift($numusua, $radicado, trim($nodeBpin[$i]), trim($nodeNomb[$i]) ,$empre, $sector);
		}
		if(ereg('ERROR',$expediente)){
			return $expediente;
			die;
		} 
	}
    
	return $radica . ' | '  . $expediente;
}

function radiPath(
    $ccusua,
    $radicado,
    $fechini,
    $fechfin){
    
    $sali      = array();
    $ruta_raiz = RUTA_RAIZ;
    include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );

    $db = new ConnectionHandler(RUTA_RAIZ);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    //validar variable $ccusua 
    $sqlus      = " SELECT 
                        USUA_LOGIN, 
                        DEPE_CODI, 
                        USUA_CODI,
                        USUA_NOMB,
                        USUA_DOC
                    FROM 
                        USUARIO
                    WHERE 
                        USUA_DOC = '$ccusua'";

    $resul     = $db->conn->Execute($sqlus);

    if($resul->EOF){ 
        $sali[] =  array('error' => 'ERROR: No existe el usuario');
        return $sali; 
    }

    if( (empty($ccusua) && empty($radicado) ) || 
        (empty($ccusua) && empty($fechini) && empty($fechini) ) ){
        $sali[] =  array('error' => 'ERROR: Argumentos en blanco');
        return $sali; 
    };

    if(!empty($fechini)){
        $aDate_parts = preg_split("[-]", $fechini);
        if(!checkdate($aDate_parts[1], $aDate_parts[2], $aDate_parts[0])){
            $sali[] =  array('error' => 'El formato de la fecha de inicio es erronea ano - mes - dia');
            return $sali; 
        }

        $aDate_parts = preg_split("/[\s-]+/", $fechini);
        if(!checkdate($aDate_parts[1], $aDate_parts[2], $aDate_parts[0])){
            $sali[] = array('error' => 'El formato de la fecha fin es erronea  es: año - mes - dia');
            return $sali;
        }
    }
    
    if($radicado){
        $isql = "SELECT  
                    RADI_NUME_RADI,
                    RADI_PATH 
                FROM 
                    RADICADO R 
                WHERE 
                    R.RADI_NUME_RADI = $radicado";
        
        $insertRs = $db->conn->Execute($isql);


        while (!$insertRs->EOF){
            $sali[] = array('numradi'   => $insertRs->fields['RADI_NUME_RADI'],
                            'path'      => $insertRs->fields['RADI_PATH']);
            $insertRs->MoveNext();
        }
    }else{
        $isql = "SELECT  
                    RADI_NUME_RADI,
                    RADI_PATH 
                FROM 
                    RADICADO R 
                WHERE 
                    R.RADI_FECH_RADI>='$fechini 12:00:00AM' 
                    AND R.RADI_FECH_RADI<='$fechfin 11:59:59PM'";
        
        $insertRs = $db->conn->Execute($isql);


        while (!$insertRs->EOF){
            $sali[] = array('numradi'   => $insertRs->fields['RADI_NUME_RADI'],
                            'path'      => $insertRs->fields['RADI_PATH']);
            $insertRs->MoveNext();
        }
    }
    return $sali;
}

#========================================================================
# FIN funciones para generacion de radicados de suifp
#========================================================================

//Function de servicios nuevos
include_once "validarRadicado/funServicios.php";
include_once "devolucion/funServicios.php";
// Modificado SSPD 01-Diciembre-2008
// Implementacion del servicio Web notificar
include_once "notificar/funServicios.php";
include_once "reasignarRadicado/funServicios.php";
// Modificado SSPD 16-Octubre-2008
// ImplementaciÃ³n de las operaciones tipificarDocumento, isDocumentoTipificado,
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
