<?php
set_time_limit(0);
//$ruta_raiz = ".";
require dirname(__FILE__) . "/config.php";
//require ORFEOPATH . "include/db/ConnectionHandler.php";

$fyh = BODEGAPATH . "debug_" . date('Ymd_His') . ".txt";
debug($fyh, "inicio script" . $fyh . "\n");
//$conn = new ConnectionHandler(ORFEOPATH);
//$conn->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$ruta_raiz = ".";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");

$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy = $db->conn->DBDate($fecha_hoy);

/*$sqlPrincipal = "SELECT radi_nume_radi, sgd_ciclo_fechasol, M.usua_login, M.usua_doc, M.estado
                    ,M.rutapdf, M.detalle, F.sgd_firma_detalle
                FROM SGD_CICLOFIRMADOMASTER M INNER JOIN SGD_CICLOFIRMADODETALLE F ON M.idcf = F.idcf
                WHERE M.estado = 3 AND F.estado = 1 AND ".$db->conn->SQLDate('Y/m/d', 'F.sgd_firma_detalle')." BETWEEN '2021/03/01' AND '2021/03/09' ";
$rsPr = $db->conn->Execute($sqlPrincipal);
if ($rsPr && !$rsPr->EOF) {
    while (!$rsPr->EOF) {
        
        $ruta = BODEGAPATH . $rsPr->fields['rutapdf'];*/
		$ruta = BODEGAPATH . "dav/2021/120216630151122_5863_00002d.pdf";
        debug($fyh, $ruta. "\n");
        sendToAres($db, $ruta, $fyh);
        
        /*$rsPr->MoveNext();
    }
} else {
    debug($fyh, "No existen archivos" . "<br>");
}*/
    
function getProperty($db, $propertyName)
{
    try
    {
        $selectSql = "SELECT VALOR FROM  PROPIEDADES WHERE NOMBRE LIKE '" . $propertyName . "'";
        $rs = $db->conn->Execute($selectSql);
        
        if(!$rs->EOF)
        {
            if (trim ($rs->fields[0]) !== '')
                return $rs->fields[0];
                else
                    return $rs->fields['VALOR'];
        }
        return "";
    }
    catch (Exception $e)
    {
        return "";
    }
}

function sendToAres($db, $file, $fyh)
{
    
    $aresWsdl = getProperty($db, "ARES_WSDL") . "/ARES_Server-ARES_Server/WebServiceIntegrator?wsdl" ;
    
    if ($aresWsdl !== "")
    {
        $absoluteBasePath = str_replace("orfeo-server-logic", "", dirname(__FILE__));
        $basePath = $absoluteBasePath . '/orfeo-server/';
        $currentSignCommand = '"' .$basePath . 'orfeo-server.jar"';
        
        $currentCommand = "ares";
        $aresUser = getProperty($db, "ARES_USER");
        $aresPassword = getProperty($db, "ARES_PASSWORD");
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $command = 'start /B cmd /C java -jar ' . $currentSignCommand . ' ' . $currentCommand . ' "' . $file . '" "' . $aresUser . '" "' . $aresPassword . '" "' . $aresWsdl  . '" >NUL 2>NUL';
            pclose(popen($command, 'r'));
            debug($fyh, $command. "\n");
        }
        else
        {
            shell_exec('java -jar ' . $currentSignCommand . ' ' . $currentCommand . ' "' . $file . '" "'. $aresUser . '" "' . $aresPassword . '" "' . $aresWsdl . '" > /dev/null &');
        }
    }
}

function debug($filename, $data)
{
    file_put_contents($filename, $data, FILE_APPEND);
}

?>