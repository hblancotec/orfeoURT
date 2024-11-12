<?php

//Agregamos nusoap y Adodb a la variable include_path para su instanciacion.
ini_set('include_path', dirname(__FILE__)."/lib;".dirname(__FILE__)."/lib/adodb;");
//Cargamos variables de conexion a BD
require("../config.php");

//Parametros para configuracion servidor de produccion
define('SERVIDOR_DB', $servidor);
define('USUARIO_DB',  $usuario);
define('PASSW_DB',    $contrasena );
define('NOMBRE_DB',   $servicio);


// incluimos la clase NuSOAP
require("nusoap.php");

// Declaramos el namespace, el cual sera utilizado al momento de crear el WS.
$ns = "https://orfeo.dnp.gov.co/webServices";
// instanciamos el objeto server, brindado por la clase soap_server
$server = new soap_server();
$server->setDebugLevel(0);
$server->debug_flag=false;
// wsdl generation
$server->configureWSDL('DigitalizadorWebService', $ns);
$server->wsdl->schemaTargetNamespace = $ns;

/////////////////////////////*****************************/////////////////////
//////////////////////////// Definicion de tipos de datos /////////////////////

$server->wsdl->addComplexType(
    'datosUsuario',
    'complexType',
    'struct',
    'all',
    '',
    array(
    'Nombre'        => array('name' => 'Nombre', 'type' => 'xsd:string'),
    'Login'         => array('name' => 'Login', 'type' => 'xsd:string'),
    'Documento'     => array('name' => 'Documento', 'type' => 'xsd:string'),
    'UsuaCodigo'    => array('name' => 'UsuaCodigo', 'type' => 'xsd:int'),
    'Dependencia'   => array('name' => 'Dependencia', 'type' => 'xsd:int'),
    'PerRad'        => array('name' => 'PerRad', 'type' => 'xsd:int'),
    'Estado'        => array('name' => 'Estado', 'type' => 'xsd:int')
    )
);

$server->wsdl->addComplexType(
    'datosBasicos',
    'complexType',
    'struct',
    'all',
    '',
    array(
    'IDS'       =>  array( 'name' => 'IDS',     'type' => 'xsd:int'),
    'DETALLE'   =>  array( 'name' => 'DETALLE',	'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'datosRadicado',
    'complexType',
    'struct',
    'all',
    '',
    array(
    'RADICADO'  =>  array( 'name' => 'RADICADO',    'type' => 'xsd:long'),
    'NUMHOJAS'  =>  array( 'name' => 'NUMHOJAS',    'type' => 'xsd:short'),
    'RUTA'      =>  array( 'name' => 'RUTA',        'type' => 'xsd:string'),
    'FECHARAD'  =>  array( 'name' => 'FECHARAD',    'type' => 'xsd:string'),
    'ASUNTO'    =>  array( 'name' => 'ASUNTO',      'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'matrizDatosBasicos',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array( array(   'ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:datosBasicos[]' ) ),
    'tns:datosBasicos'
);

$server->wsdl->addComplexType(
    'matrizDatosRadicado',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array( array(   'ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:datosRadicado[]' ) ),
    'tns:datosRadicado'
);

////////////////////////////******************************////////////////////
////////////////////////////    Registramos metodos       ////////////////////

$server->register('HolaMundo',
    array('nombre' => 'xsd:string'),
    array('return' => 'xsd:string'),
    $ns,false,false,false,
    "Metodo de prueba. Para probar conexion rapida a DigitalizadorWebService");

$server->register('getUsuario',                  // method name
    array(  'username'  => 'xsd:string', 'password' => 'xsd:string'),
    array(  'return'    => 'tns:datosUsuario'),     // output parameters
    $ns,                                    // namespace
    $false,                                 // soapaction
    $false,                                 // style
    $false,                                 // use
    'Metodo para autenticacion de Usuario'  // documentation
);

$server->register('getSeries',
    array( 'dependencia' => 'xsd:int'),
    array( 'return'      => 'tns:matrizDatosBasicos'),
    $ns,
    false, false, false,
    'Recupera las series activas de una dependencia dada.'
);

$server->register('getDependencias',
    array( 'soloActivas' => 'xsd:boolean'),
    array( 'return'      => 'tns:matrizDatosBasicos'),
    $ns,
    false, false, false,
    'Recupera las dependencias activas/desactivas seg�n par�metro dado de la entidad.'
);

$server->register('getSubSeries',
    array( 'dependencia' => 'xsd:int', 'serie' => 'xsd:int'),
    array( 'return'      => 'tns:matrizDatosBasicos'),
    $ns,
    false, false, false,
    'Recupera las subseries activas de una dependencia y serie dada.'
);

$server->register('getTiposDocumentales',
    '',
    array( 'return'      => 'tns:matrizDatosBasicos'),
    $ns,
    false, false, false,
    'Recupera los tipos documentales activos de una dependencia, serie y subserie dada.'
);

$server->register('getRadicadosDependencia',
    array( 'dependencia' => 'xsd:int', 'fechaIni' => 'xsd:string', 'fechaFin' => 'xsd:string'),
    array( 'return'      => 'tns:matrizDatosRadicado'),
    $ns,
    false, false, false,
    'Recupera los radicados sin imagen pertenecientes a una dependencia y entre fechas (yyyy/mm/dd) de radicación suministrada.'
);

$server->register('getRadicadosUsuario',
    array( 'username'  => 'xsd:string', 'password' => 'xsd:string', 'fechaIni' => 'xsd:string', 'fechaFin' => 'xsd:string'),
    array( 'return'      => 'tns:matrizDatosRadicado'),
    $ns,
    false, false, false,
    'Recupera los radicados sin imagen pertenecientes radicados por un usuario y entre fechas (yyyy/mm/dd) de radicación suministrada.'
);

$server->register('getRadicadosCodigo',
    array( 'radicado'  => 'xsd:long', 'fechaIni' => 'xsd:string', 'fechaFin' => 'xsd:string'),
    array( 'return'      => 'tns:matrizDatosRadicado'),
    $ns,
    false, false, false,
    'Recupera los radicados según numero y entre fechas (yyyy/mm/dd) de radicación suministrada.'
);

$server->register('setRadicadoPrincipal',
    array( 'username'  => 'xsd:string', 'password' => 'xsd:string', 'radicado' => 'xsd:long', 'numHojas' => 'xsd:int', 'rutaArchivo'=>'xsd:string'),
    array( 'return'      => 'xsd:boolean'),
    $ns,
    false, false, false,
    'Actualiza la información de un radicado digitalizado suministrado.'
);

$server->register('setRadicadoAnexo',
    array( 'username'  => 'xsd:string', 'password' => 'xsd:string', 'radicado' => 'xsd:long', 'numHojas' => 'xsd:int', 'rutaArchivo'=>'xsd:string', 'tipodocumental' => 'xsd:int', 'observacion'=>'xsd:string'),
    array( 'return'      => 'xsd:boolean'),
    $ns,
    false, false, false,
    'Creación de anexos con datos de digitalizacion.'
);
////////////////////////////******************************////////////////////
///////////////////////////    implementamos metodos      ////////////////////


/**
 * Funcion de comprobacion de conexion a WebService
 *
 * @param <string> $nombre Nombre de la persona. Obligatorio.
 * @return <string> Concatenacion "Hola" + $nombre.
 */
function HolaMundo ($nombre) {
    if (!empty($nombre))
        return "Hola ".$nombre;
    else {
        return new soap_fault("client", "", "Faltan datos basicos.");
    }
}

/**
 * Funcion que valida el login y contraseña suministradas retornando una matriz con datos de usuario
 * @param <string> $username
 * @param <string> $password
 * @return <array>
 */
function getUsuario($username, $password ) {
    if (empty($username) ||  empty($password)) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    } else {
        global $ADODB_COUNTRECS;
        if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
        require "adodb/adodb.inc.php";
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
        $db = NewADOConnection($dsn);
        if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $u = $db->qstr(strtoupper($username),get_magic_quotes_gpc());
        $query= "select * from usuario WHERE usua_login='$username' AND usua_pasw='".SUBSTR(md5($password),1,26)."'";
        $ADODB_COUNTRECS = true;
        $rs = $db->Execute($query);
        $ADODB_COUNTRECS = false;
        if ($rs->RecordCount() > 0) {
            return array(   'Nombre'=>$rs->fields["USUA_NOMB"],
            'Login'=>$rs->fields["USUA_LOGIN"],
            'Documento'=>$rs->fields["USUA_DOC"],
            'UsuaCodigo'=>$rs->fields["USUA_CODI"],
            'Dependencia'=>$rs->fields["DEPE_CODI"],
            'PerRad'=>$rs->fields["PERM_RADI"],
            'Estado'=>$rs->fields["USUA_ESTA"]
            );
        } else  return new soap_fault("Server", "", "No se hallaron registros.");
    }
}

/**
 * 
 * Funcion que retorna un listado de dependencias.
 * @param boolean $soloActivas
 */
function getDependencias($soloActivas=true){
	global $ADODB_COUNTRECS;
        if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
        require "adodb/adodb.inc.php";
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
        $db = NewADOConnection($dsn);
        if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        //inactiva=1   activa=2
        $where = ($soloActivas) ? "WHERE DEPENDENCIA_ESTADO = 2" : "" ;
        $query =    "SELECT DISTINCT d.DEPE_CODI AS IDS, d.DEPE_NOMB AS DETALLE
                    FROM DEPENDENCIA d $where ORDER BY d.DEPE_NOMB ";
        $ADODB_COUNTRECS = true;
        $rs = $db->Execute($query);
        $ADODB_COUNTRECS = false;
        $i = 0;
        if ($rs->RecordCount() > 0) {
            $result= array();
            while (!$rs->EOF) {
                $result[$i] = array('IDS'=> $rs->fields["IDS"], 'DETALLE'=> $rs->fields["DETALLE"]);
                $rs->MoveNext();
                $i++;
            }
            $rs->Close();
            return $result;
        } else  return new soap_fault("Server", "", "No se hallaron registros.");
}

/**
 * Funcion que retorna una matriz con las series activas a la fecha de la dependencia suministrada.
 * @param <int> $dependencia
 * @return <array>
 */
function getSeries($dependencia) {
    if (empty($dependencia)) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    } else {
        global $ADODB_COUNTRECS;
        if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
        require("adodb.inc.php");
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
        $db = NewADOConnection($dsn);
        if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $fecha_hoy = Date("Y-m-d");
        $sqlFechaHoy = $db->DBDate($fecha_hoy);
        $query =    "SELECT DISTINCT s.SGD_SRD_CODIGO AS IDS, s.SGD_SRD_DESCRIP AS DETALLE
                    FROM SGD_MRD_MATRIRD m, SGD_SRD_SERIESRD s
                    WHERE m.DEPE_CODI = $dependencia and s.SGD_SRD_CODIGO = m.SGD_SRD_CODIGO and "
            . $sqlFechaHoy . " between s.SGD_SRD_FECHINI and s.SGD_SRD_FECHFIN ORDER BY DETALLE ";
        $ADODB_COUNTRECS = true;
        $rs = $db->Execute($query);
        $ADODB_COUNTRECS = false;
        $i = 0;
        if ($rs->RecordCount() > 0) {
            $result= array();
            while (!$rs->EOF) {
                $result[$i] = array('IDS'=> $rs->fields["IDS"], 'DETALLE'=> $rs->fields["DETALLE"]);
                $rs->MoveNext();
                $i++;
            }
            $rs->Close();
            return $result;
        } else  return new soap_fault("Server", "", "No se hallaron registros.");
    }
}

/**
 * Funcion que retorna una matriz con las subseries activas a la fecha de la dependencia y serie suministrada.
 * @param <int> $dependencia
 * @param <int> $serie
 * @return <array>
 */
function getSubSeries($dependencia, $serie) {
    if (empty($dependencia) || empty($serie)) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    } else {
        global $ADODB_COUNTRECS;
        if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
        require("adodb.inc.php");
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
        $db = NewADOConnection($dsn);
        if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $fecha_hoy = Date("Y-m-d");
        $sqlFechaHoy = $db->DBDate($fecha_hoy);
        $query =    "SELECT DISTINCT b.SGD_SBRD_DESCRIP AS DETALLE, b.SGD_SBRD_CODIGO AS IDS
                    FROM    SGD_MRD_MATRIRD AS m INNER JOIN
                            SGD_SRD_SERIESRD AS s ON m.SGD_SRD_CODIGO = s.SGD_SRD_CODIGO INNER JOIN
                            SGD_SBRD_SUBSERIERD AS b ON s.SGD_SRD_CODIGO = b.SGD_SRD_CODIGO AND m.SGD_SBRD_CODIGO = b.SGD_SBRD_CODIGO
                    WHERE (m.DEPE_CODI = $dependencia) AND (m.SGD_SRD_CODIGO = $serie) AND (".$sqlFechaHoy. " BETWEEN b.SGD_SBRD_FECHINI AND b.SGD_SBRD_FECHFIN )
                    ORDER BY b.SGD_SBRD_DESCRIP";
        $ADODB_COUNTRECS = true;
        $rs = $db->Execute($query);
        $ADODB_COUNTRECS = false;
        $i = 0;
        if ($rs->RecordCount() > 0) {
            $result= array();
            while (!$rs->EOF) {
                $result[$i] = array('IDS'=> $rs->fields["IDS"], 'DETALLE'=> $rs->fields["DETALLE"]);
                $rs->MoveNext();
                $i++;
            }
            $rs->Close();
            return $result;
        } else  return new soap_fault("Server", "", "No se hallaron registros.");
    }
}

/**
 * Funcion que retorna una matriz con todos los tipos documentales.
 * @return <array>
 */
function getTiposDocumentales() {
    global $ADODB_COUNTRECS;
    if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
    require("adodb.inc.php");
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
    $db = NewADOConnection($dsn);
    if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    $fecha_hoy = Date("Y-m-d");
    $sqlFechaHoy = $db->DBDate($fecha_hoy);
    $query = "SELECT SGD_TPR_DESCRIP AS DETALLE, SGD_TPR_CODIGO AS IDS
                    FROM SGD_TPR_TPDCUMENTO ORDER BY DETALLE";
    $ADODB_COUNTRECS = true;
    $rs = $db->Execute($query);
    $ADODB_COUNTRECS = false;
    $i = 0;
    if ($rs->RecordCount() > 0) {
        $result= array();
        while (!$rs->EOF) {
            $result[$i] = array('IDS'=> $rs->fields["IDS"], 'DETALLE'=> $rs->fields["DETALLE"]);
            $rs->MoveNext();
            $i++;
        }
        $rs->Close();
        return $result;
    } else  return new soap_fault("Server", "", "No se hallaron registros.");
}

/**
 * Funcion que retorna una matriz con radicados sin imagen pertenecientes a la dependencia y entre fechas de radicación suministrada.
 * @param <int> $dependencia
 * @param <date> $fechaIni Formato yyyy/mm/dd
 * @param <date> $fechaFin Formato yyyy/mm/dd
 * @return <array>
 */
function getRadicadosDependencia($dependencia, $fechaIni, $fechaFin) {
    if (empty($dependencia) || empty($fechaIni) || empty($fechaFin)) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    }
    if (is_valid_date($fechaIni) && is_valid_date($fechaIni)) {
        global $ADODB_COUNTRECS;
        if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
        require("adodb.inc.php");
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
        $db = NewADOConnection($dsn);
        if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $sqlFechaHoy = $db->DBDate($fecha_hoy);
        $query =    "SELECT RADI_NUME_RADI AS RADICADO, ".$db->IfNull('RADI_NUME_HOJA' , 0)." AS NUMHOJAS,
                            RADI_PATH AS RUTA, ".$db->SQLDate('d-m-Y h:i:s A', 'RADI_FECH_RADI')." AS FECHARAD, RA_ASUN AS ASUNTO
                    FROM    RADICADO r
                    WHERE (r.RADI_PATH IS NULL) AND ".$db->substr."(CAST(r.RADI_NUME_RADI AS VARCHAR),5,3)= $dependencia
                        AND ".$db->SQLDate("Y/m/d","r.RADI_FECH_RADI")." >= '$fechaIni' AND ".
            $db->SQLDate("Y/m/d","r.RADI_FECH_RADI")." <= '$fechaFin'
                    ORDER BY r.RADI_NUME_RADI";
        $ADODB_COUNTRECS = true;
        $rs = $db->Execute($query);
        $ADODB_COUNTRECS = false;
        $i = 0;
        if ($rs->RecordCount() > 0) {
            $result= array();
            while (!$rs->EOF) {
                $result[$i] = array(    'RADICADO'=> $rs->fields["RADICADO"],
                    'NUMHOJAS'=> $rs->fields["NUMHOJAS"],
                    'RUTA'=> $rs->fields["RUTA"],
                    'FECHARAD'=> $rs->fields["FECHARAD"],
                    'ASUNTO'=> $rs->fields["ASUNTO"]  );
                $rs->MoveNext();
                $i++;
            }
            $rs->Close();
            return $result;
        } else  return new soap_fault("Server", "", "No se hallaron registros.");
    }  else  return new soap_fault("Client", "", "Formato no valido.");
}

/**
 * Funcion que retorna una matriz con radicados sin imagen radicados por un usuario y entre fechas de radicación suministrada.
 * @param <string> $username
 * @param <string> $password
 * @param <date> $fechaIni Formato yyyy/mm/dd
 * @param <date> $fechaFin Formato yyyy/mm/dd
 * @return <array>
 */
function getRadicadosUsuario($username, $password, $fechaIni, $fechaFin) {
    if (empty($fechaIni) || empty($fechaFin) || empty($username) || empty($password)) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    }
    if (is_valid_date($fechaIni) && is_valid_date($fechaIni)) {
        $datosUsuario = getUsuario($username, $password);
        global $ADODB_COUNTRECS;
        if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
        require("adodb.inc.php");
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
        $db = NewADOConnection($dsn);
        if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $sqlFechaHoy = $db->DBDate($fecha_hoy);
        $query =    "SELECT RADI_NUME_RADI AS RADICADO, ".$db->IfNull('RADI_NUME_HOJA' , 0)." AS NUMHOJAS,
                            RADI_PATH AS RUTA, RADI_FECH_RADI AS FECHARAD, RA_ASUN AS ASUNTO
                    FROM    RADICADO r
                    WHERE (r.RADI_PATH IS NULL) AND r.RADI_USUA_RADI=".$datosUsuario['UsuaCodigo']." AND
                        r.RADI_DEPE_RADI=".$datosUsuario['Dependencia']." AND ".
            $db->SQLDate("Y/m/d","r.RADI_FECH_RADI")." >= '$fechaIni' AND ".
            $db->SQLDate("Y/m/d","r.RADI_FECH_RADI")." <= '$fechaFin'
                    ORDER BY r.RADI_NUME_RADI";
        $ADODB_COUNTRECS = true;
        $rs = $db->Execute($query);
        $ADODB_COUNTRECS = false;
        $i = 0;
        if ($rs->RecordCount() > 0) {
            $result= array();
            while (!$rs->EOF) {
                $result[$i] = array(    'RADICADO'=> $rs->fields["RADICADO"],
                    'NUMHOJAS'=> $rs->fields["NUMHOJAS"],
                    'RUTA'=> $rs->fields["RUTA"],
                    'FECHARAD'=> $rs->fields["FECHARAD"],
                    'ASUNTO'=> $rs->fields["ASUNTO"]  );
                $rs->MoveNext();
                $i++;
            }
            $rs->Close();
            return $result;
        } else  return new soap_fault("Server", "", "No se hallaron registros.");
    }  else  return new soap_fault("Client", "", "Formato no valido.");
}

/**
 * Funcion que retorna una matriz con radicados sin imagen radicados por un usuario y entre fechas de radicación suministrada.
 * @param <long> $radicado
 * @param <date> $fechaIni Formato yyyy/mm/dd
 * @param <date> $fechaFin Formato yyyy/mm/dd
 * @return <array>
 */
function getRadicadosCodigo($radicado, $fechaIni, $fechaFin) {
    if (empty($fechaIni) || empty($fechaFin) || empty($radicado)) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    }
    if (is_valid_date($fechaIni) && is_valid_date($fechaIni)) {
    //$datosUsuario = getUsuario($username, $password);
        global $ADODB_COUNTRECS;
        if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
        require("adodb.inc.php");
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
        $db = NewADOConnection($dsn);
        if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        if (strlen($radicado) == 14) {
            $tmpWhere = " RADI_NUME_RADI=$radicado ";
        } else {
            $tmpWhere = " RADI_NUME_RADI like '%$radicado%' AND ".
                        $db->SQLDate("Y/m/d","r.RADI_FECH_RADI")." >= '$fechaIni' AND ".
                        $db->SQLDate("Y/m/d","r.RADI_FECH_RADI")." <= '$fechaFin'";
        }
        $query =    "SELECT RADI_NUME_RADI AS RADICADO, ".$db->IfNull('RADI_NUME_HOJA' , 0)." AS NUMHOJAS,
                            RADI_PATH AS RUTA, RADI_FECH_RADI AS FECHARAD, RA_ASUN AS ASUNTO
                    FROM    RADICADO r
                    WHERE   $tmpWhere ORDER BY r.RADI_NUME_RADI";
        $ADODB_COUNTRECS = true;
        $rs = $db->Execute($query);
        $ADODB_COUNTRECS = false;
        $i = 0;
        if ($rs->RecordCount() > 0) {
            $result= array();
            while (!$rs->EOF) {
                $result[$i] = array(    'RADICADO'=> $rs->fields["RADICADO"],
                    'NUMHOJAS'=> $rs->fields["NUMHOJAS"],
                    'RUTA'=> $rs->fields["RUTA"],
                    'FECHARAD'=> $rs->fields["FECHARAD"],
                    'ASUNTO'=> $rs->fields["ASUNTO"]  );
                $rs->MoveNext();
                $i++;
            }
            $rs->Close();
            return $result;
        } else  return new soap_fault("Server", "", "No se hallaron registros.");
    }  else  return new soap_fault("Client", "", "Formato no valido.");
}

/**
 * Funcion que actualiza un radicado con datos de digitalizacion
 * @param <string> $username
 * @param <string> $password
 * @param <long> $radicado
 * @param <int> $numHojas
 * @param <string> $rutaArchivo.
 * @param <string> $observacion. Si el radicado ya posee archivo debe enviarse motivo de la sobre-escritura.
 * @return boolean
 */
function setRadicadoPrincipal($username, $password, $radicado, $numHojas, $rutaArchivo, $observacion) {
    if (empty($username) || empty($password) || empty($radicado) || !isset($numHojas) || empty($rutaArchivo)) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    }
    //Traemos datos del usuario
    $tmpUser = getUsuario($username, $password );

    //Traemos datos del radicado
    if (strlen($radicado)==14) {
        $tmpRadicado = getRadicadosCodigo($radicado, "2005/09/15", date("Y/m/d"));
        $codTTR = 22; //Digitalizar Imagen; Por defecto es esta la transaccion para historico
        if (strlen($tmpRadicado[0]['RUTA']) >0) {
            if (empty($observacion)) {
            //Si ya tiene imagen y no envian observacion .. ERROR
                return new soap_fault("Client", "", "Faltan datos basicos (observacion).");
            } else {
                $codTTR = 23; //Modificacion Imagen;
            }
        }
    } else return new soap_fault("Client", "", "Numero de radicado no valido.");

    global $ADODB_COUNTRECS;
    if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
    require("adodb.inc.php");
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
    $db = NewADOConnection($dsn);
    if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    $db->BeginTrans();
    $sql = "UPDATE radicado set RADI_PATH='$rutaArchivo', RADI_NUME_HOJA=$numHojas WHERE RADI_NUME_RADI=$radicado";
    $ok = $db->Execute($sql);
    $hoy = $db->sysTimeStamp;
    $sql = "INSERT INTO hist_eventos
            (DEPE_CODI, USUA_CODI, USUA_DOC, RADI_NUME_RADI, HIST_OBSE, HIST_FECH, SGD_TTR_CODIGO, DEPE_CODI_DEST, USUA_CODI_DEST, HIST_DOC_DEST )
            VALUES (".
        $tmpUser['Dependencia'].",".$tmpUser['UsuaCodigo'].", '".$tmpUser['Documento']."', $radicado, '".
        "Digitalizacion - Inicial [$numHojas Paginas]"."', $hoy, $codTTR,".
        $tmpUser['Dependencia'].",".$tmpUser['UsuaCodigo'].", '".$tmpUser['Documento']."' )";
    if ($ok) $ok = $db->Execute($sql);
    if ($ok) {
        $db->CommitTrans();
        $band = true;
    } else {
        $db->RollbackTrans();
        $band = false;
    }
    return $band;
}

/**
 * Funcion que crea un anexo con datos de digitalizacion
 * @param <string> $username
 * @param <string> $password
 * @param <long> $radicado
 * @param <string> $rutaArchivo
 * @param <int> $tipodocumental
 * @param <int> $tamanoBytes.	Tamano en bytes del archivo a subir.
 * @param <boolean> $soloLectura.
 * @param <string> $descripcion.
 * @return boolean
 */
function setAnexo($username, $password, $radicado, $rutaArchivo, $tipodocumental, $tamanoBytes, $soloLectura=true, $descripcion ) {
    if (empty($username) || empty($password) || empty($radicado) || empty($rutaArchivo) ||
    	!isset($tipodocumental) || !isset($tamanoBytes) ) {
        return new soap_fault("Client", "", "Faltan datos basicos.");
    }
    //Traemos datos del usuario
    $tmpUser = getUsuario($username, $password );

    //Traemos datos del radicado
    if (strlen($radicado)==14) {
        $tmpRadicado = getRadicadosCodigo($radicado, "2005/09/15", date("Y/m/d"));
        $codTTR = 29; //Digitalizacion de Anexo
    } else return new soap_fault("Client", "", "Numero de radicado no valido.");

    global $ADODB_COUNTRECS;
    if (!defined('ADODB_ASSOC_CASE'))	define('ADODB_ASSOC_CASE', 1);
    require("adodb.inc.php");
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $dsn = "mssql://".USUARIO_DB.":".PASSW_DB."@".SERVIDOR_DB."/".NOMBRE_DB;
    $db = NewADOConnection($dsn);
    if ($db===false)  return new soap_fault("Server", "", "Error de Conexion a BD.");
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    
    
    $db->BeginTrans();

    $sql = "select max( a.anex_numero ) AS a from Anexos a where a.anex_radi_nume = $radicado";
    $secuencia = $db->GetOne($sql);
    $secuencia++;

    $infoFile = pathinfo($rutaArchivo);
    
    $sql = "SELECT anex_tipo_codi from ANEXOS_TIPO WHERE anex_tipo_ext='".strtolower($infoFile['extension'])."'";
    $tmpExt = $db->GetOne($sql);
    $codExtension = ($tmpExt == null) ? 0 : $tmpExt;
    $anexcodigo = $radicado .str_pad($secuencia,5,"0", STR_PAD_LEFT);
    $nombarchivo = "1".$radicado."_".str_pad($secuencia,5,"0", STR_PAD_LEFT).".".strtolower($infoFile['extension']);
    
    $hoy = $db->sysTimeStamp;
    
    $u = $db->qstr(strtoupper($username),get_magic_quotes_gpc());
    
    $d = trim($descripcion);
    $d = (strlen($d)>0) ? substr($db->qstr(trim($descripcion),get_magic_quotes_gpc()), 0, 500): "''";
    
    $datAnex["SGD_TPR_CODIGO"]      =  $tipodocumental;
    $datAnex["ANEX_RADI_NUME"]      =  $radicado;
    $datAnex["ANEX_CODIGO"]         =  $anexcodigo; //Variable Calculada
    $datAnex["ANEX_TIPO"]           =  $codExtension;
    $datAnex["ANEX_TAMANO"]         =  $tamanoBytes;
    $datAnex["ANEX_SOLO_LECT"]      =  "'".(($soloLectura) ? 'S' : 'N')."'";
    $datAnex["ANEX_CREADOR"]        =  "$u";  //Variable de usuario
    $datAnex["ANEX_DESC"]           =  "$d";
    $datAnex["ANEX_NUMERO"]         =  $secuencia;  //Variable calculada
    $datAnex["ANEX_NOMB_ARCHIVO"]   =  "'".$nombarchivo."'";
    $datAnex["ANEX_BORRADO"]        =  "'N'";
    $datAnex["ANEX_ORIGEN"]         =  0;
    $datAnex["ANEX_FECH_ANEX"]      =  $hoy;
    $datAnex["ANEX_ESTADO"]         =  1;
    $datAnex["ANEX_DEPE_CREADOR"]   =  $tmpUser["Dependencia"];
    $datAnex["SGD_DIR_TIPO"]        =  1;

    $insertSQL = $db->Replace("ANEXOS", $datAnex, array('ANEX_RADI_NUME','ANEX_CODIGO'), false);
    
    $sql = "INSERT INTO hist_eventos
            (DEPE_CODI, USUA_CODI, USUA_DOC, RADI_NUME_RADI, HIST_OBSE, HIST_FECH, SGD_TTR_CODIGO, DEPE_CODI_DEST, USUA_CODI_DEST, HIST_DOC_DEST )
            VALUES (".
        $tmpUser['Dependencia'].",".$tmpUser['UsuaCodigo'].", '".$tmpUser['Documento']."', $radicado, '".
        "Anexo creado via WebService"."', $hoy, $codTTR,".
        $tmpUser['Dependencia'].",".$tmpUser['UsuaCodigo'].", '".$tmpUser['Documento']."' )";

    if ($insertSQL) $ok = $db->Execute($sql);
    if ($ok) {
        $db->CommitTrans();
        $band = true;
    } else {
        $db->RollbackTrans();
        $band = false;
    }
    return $band;
}

/**
 * Tomado de los snippets de la funcion date() en php.net
 * Checks date if matches given format and validity of the date.
 * Examples:
 * <code>
 * is_date('22.22.2222', 'mm.dd.yyyy'); // returns false
 * is_date('11/30/2008', 'mm/dd/yyyy'); // returns true
 * is_date('30-01-2008', 'dd-mm-yyyy'); // returns true
 * is_date('2008 01 30', 'yyyy mm dd'); // returns true
 * </code>
 * @param string $value the variable being evaluated.
 * @param string $format Format of the date. Any combination of <i>mm<i>, <i>dd<i>, <i>yyyy<i>
 * with single character separator between.
 */
function is_valid_date($value, $format = 'yyyy/mm/dd') {
    if(strlen($value) >= 6 && strlen($format) == 10) {
    // find separator. Remove all other characters from $format
        $separator_only = str_replace(array('m','d','y'),'', $format);
        $separator = $separator_only[0]; // separator is first character
        if($separator && strlen($separator_only) == 2) {
        // make regex
            $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
            $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
            $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
            $regexp = str_replace($separator, "\\" . $separator, $regexp);
            if($regexp != $value && preg_match('/'.$regexp.'\z/', $value)) {
            // check date
                $arr=explode($separator,$value);
                $day=$arr[2];
                $month=$arr[1];
                $year=$arr[0];
                if(@checkdate($month, $day, $year))
                    return true;
            }
        }
    }
    return false;
}

if (isset($HTTP_RAW_POST_DATA)) {
    $input = $HTTP_RAW_POST_DATA;
}
else {
    $input = implode("\r\n", file('php://input'));
}
$server->service($input);
exit;

//$algo = getDependencias(false);
//var_dump($algo);
?>