<?php
set_time_limit(0);
$ruta_raiz = ".";
require dirname(__FILE__) . "\\config.php";
// require ORFEOPATH . "include/db/ConnectionAlarmas.php";

require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
# ############################################################################
$conn = NewADOConnection($dsnn);
$conn->SetFetchMode(ADODB_FETCH_ASSOC);

function readXML($texto)
{
    /*$posini1 = strpos($texto, "idMensaje=");
     $posini = strpos($texto, "</ns1:hash>");
     $posfin = strlen($texto);
     $large = ($posfin - $posini) - $posini1;
     $resta = $posfin - $large;
     $nodes = substr($texto, strpos($texto, "idMensaje="), $resta);*/
    preg_match_all("|<ns1:RegistrarMensajesResponse>(.*)</ns1:RegistrarMensajesResponse>|s", $texto, $items);
    $nodes = array();
    foreach ($items[1] as $key => $item) {
        preg_match("|<ns1:hash>(.*)</ns1:hash>|s", $item, $mensaje);
        
        $nodes = $mensaje[1];
    }
    
    return $nodes;
}

function debug($filename, $data)
{
    file_put_contents($filename, $data, FILE_APPEND);
}

// $debug = (isset($_GET['debug']) ? $_GET['debug'] : FALSE);
$debug = true;
if ($debug) {
    $fyh = BODEGAPATH . "debugCertificado_" . date('Ymd_His') . ".txt";
    debug($fyh, "inicio script" . $fyh . "\r\n");
}

$mensaje = '<soapenv:Envelope xmlns:seal="http://www.sealmail.co/" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
            <soapenv:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"><wsse:UsernameToken wsu:Id="UsernameToken-A418C01C43271E55F516857150194471"><wsse:Username>dnp@dnp.gov.co</wsse:Username><wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">h1buv6kRoUffJikMqrqYMVKzD0c=</wsse:Password><wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">UbbSjpm9Fo+6S+9/iRKmkA==</wsse:Nonce><wsu:Created>2023-06-02T14:10:19.440Z</wsu:Created></wsse:UsernameToken></wsse:Security></soapenv:Header>
            <soapenv:Body>
                <seal:RegistrarMensajesRequest>
                <seal:idUsuario>dnp@dnp.gov.co</seal:idUsuario>
                <seal:datos>[';
$json = "";
$arrResp = array();

$archivo = fopen("Intermitencia.csv", "r");
while (($datos = fgetcsv($archivo, 0, ";")) == true) {
    $num = count($datos);
    $radicado = "";
    $correo = "";
    $nombre = "";
    $radPath = "";
    $body = "";
    
    for ($columna = 0; $columna < $num; $columna ++) {
        if ($columna == 0) {
            $radicado = trim($datos[$columna]);
        }
        if ($columna == 1) {
            $correo = trim($datos[$columna]);
        }
    }
    
    if ($radicado != "") {
 
        $asunto = iconv('iso_8859-1', 'utf-8', "Envío de notificación radicado " . $radicado);
        $cuerpo = iconv('iso_8859-1', 'utf-8', "Comunicación Oficial.\r\n El Departamento Nacional de Planeación le envía este oficio mediante notificación certificada, De acuerdo a la Directiva Presidencial No. 04 de abril 3 del año 2012 y de conformidad con lo previsto en la Ley 1437 de 2011, por el cual se expide el Código de Procedimiento Administrativo y de lo Contencioso. \r\n ***Importante: Por favor no responda a este correo electrónico. Esta cuenta no permite recibir correo.");
        
        $sqlMen = "SELECT E.SGD_RENV_NOMBRE, R.RADI_PATH 
                    FROM SGD_RENV_REGENVIO E INNER JOIN RADICADO R ON E.RADI_NUME_SAL = R.RADI_NUME_RADI
                    WHERE RADI_NUME_SAL = $radicado ";
        $rsMen = $conn->Execute($sqlMen);
        if ($rsMen && !$rsMen->EOF) {
            if ($rsMen->fields['SGD_RENV_NOMBRE'] == "" )
            {
                $nombre = "N/A";
            } 
            else 
            {
                $nombre = iconv('iso_8859-1', 'utf-8', str_replace("&", "Y", $rsMen->fields['SGD_RENV_NOMBRE']));
            }
            $radPath = $rsMen->fields['RADI_PATH'];
        }
                 
        $filename = BODEGAPATH."/tmp/Adjuntos_".$radicado.".zip";
        
        $zip = new ZipArchive();
        if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
            debug($fyh, "No se creo el archivo zip " . $fyh . "\r\n");
            exit();
        }
        
        $tmpRuta = BODEGAPATH . $radPath;
        $strFile = file_get_contents($tmpRuta);
        if ($strFile) {
            $nomFile = basename($tmpRuta);
            $zip->addFile($tmpRuta, $nomFile);
        }
        
        $sql = "select anex_nomb_archivo from anexos where anex_radi_nume = ".$radicado." and anex_marcar_envio_email = 1";
        $rs = $conn->Execute($sql);
        while($rs && !$rs->EOF) {
            if (strlen($radicado) == 14) {
                $tmpAnexoFile = BODEGAPATH.substr($radicado, 0, 4)."/".substr($radicado, 4, 3)."/docs/".$rs->fields['anex_nomb_archivo'];
            } else {
                $tmpAnexoFile = BODEGAPATH.substr($radicado, 0, 4)."/".substr($radicado, 4, 4)."/docs/".$rs->fields['anex_nomb_archivo'];
            }
            
            $nomFile = basename($tmpAnexoFile);
            $zip->addFile($tmpAnexoFile, $nomFile);
            $rs->MoveNext();
        }
        
        $resultZip = $zip->close();
        
        if ($resultZip) {
            $strFile = file_get_contents($filename);
            if ($strFile) {
                $data = base64_encode($strFile);
            }
        }
        
        if($json != "") $json .= ",";
        
        $json .= '"{\"Asunto\":\"'.$asunto.'\",
                    \"Texto\":\"'.$cuerpo.'\",
                    \"NombreDestinatario\":\"'.$nombre.'\",
                    \"CorreoDestinatario\":\"'.$correo.'\",
                    \"Adjunto\":\"'. $data .'\",
                    \"NombreArchivo\":\"'. $radicado . ".zip" .'\",
                    \"Alertas\":\"False\"
                    }"';
        
        $body = "<idUsuario>dnp@dnp.gov.co</idUsuario><Asunto>$asunto</Asunto><Texto>$cuerpo</Texto><NombreDestinatario>$nombre</NombreDestinatario><CorreoDestinatario>$correo</CorreoDestinatario><NombreArchivo>$radicado.zip</NombreArchivo>";
        
        $sqli = "insert into log_servicio(radi_nume_radi, body, date_send, correo) values ($radicado,'$body', GETDATE(), '$correo')";
        $rs = $conn->Execute($sqli);
                
        array_push($arrResp, $radicado . "-". $correo);
    }
    
    $rsMen->MoveNext();
}

try {
    
    $mensaje .= $json;
    
    $mensaje .= ']</seal:datos>
                </seal:RegistrarMensajesRequest>
                </soapenv:Body>
                </soapenv:Envelope>';
    
    $ch = curl_init($wsdl472);
    curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
        "Content-Type: text/xml"
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mensaje);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $respBody = curl_exec($ch);
    $info = curl_getinfo($ch);
    $code = $info['http_code'];
    curl_close($ch);
    
    $mensaje = readXML($respBody);
        
    $idMensaje1 = explode("\n", $mensaje)[0];
    $observa1 = explode("\n", $mensaje)[1];
    
    $idMensaje = explode("=", $idMensaje1)[1];
    $observa = explode("=", $observa1)[1];
    
    $string = json_decode($observa, TRUE);
    
    $j = 0;
    foreach ($arrResp as $value) {
        $vars = explode("-", $value);
        
        $varMen = json_decode($string[$j], TRUE);
        if ($varMen == "") {
            $idmensaje = 0;
            $observacion = "";
        } else {
            $idmensaje = $varMen['idSealmail'];
            $observacion = $varMen['Observacion'];
        }
    
        $sqlu = "update log_servicio set response = '".$observacion." | " . implode(",", $info) . "', date_recibe = GETDATE(), status = '$code', idMensaje = ".$idmensaje.", observacion = '".$observacion."' where radi_nume_radi = ".$vars[0]." and correo = '".$vars[1]."'";
        $rs = $conn->Execute($sqlu);
        
        $j = $j + 1;
    }
    
    if (! empty($code) and $code == 200) {
        debug($fyh, "Ejecutado correctamente !!" . $fyh . "\r\n");
    } else {
        debug($fyh, "Error en el servicio web." . $fyh . "\r\n");
    }
    
    
} catch (Exception $e) {
    debug($fyh, "Error: " . $e->getMessage() . "\r\n");
    echo $e->getMessage();
}








?>