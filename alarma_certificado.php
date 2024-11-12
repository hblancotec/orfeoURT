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
    preg_match_all("|<ns1:ObtenerTokenResponse>(.*)</ns1:ObtenerTokenResponse>|s", $texto, $items);
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

require ORFEOPATH . "include/tx/Historico.php";
$sql = "SELECT USUA_CODI, DEPE_CODI, USUA_DOC, USUA_LOGIN FROM USUARIO WHERE USUA_LOGIN='$usrComodin'";
$rsU = $conn->Execute($sql);
if ($rsU) {
    debug($fyh, "Usuario comodin " . $rsU->Fields('USUA_LOGIN') . "\r\n");
} else {
    debug($fyh, "Error: Usuario Comodin No encontrado. " . "\r\n");
}
$sqlMen = "SELECT ID, RADI_NUME_RADI, BODY, DATE_SEND, RESPONSE, DATE_RECIBE, STATUS, IDMENSAJE, OBSERVACION, CORREO 
            FROM [log_servicio] WHERE [idMensaje] IS NOT NULL AND [status] = 200 
                AND [date_recibe] BETWEEN '2024-09-25T00:00:00' AND '2024-10-02T23:59:59'
                --and idResponse is null 
                and idMensaje > 0 
                --and [radi_nume_radi] = 202421300458211  
            ORDER BY [date_send] ";
$rsMen = $conn->Execute($sqlMen);
if ($rsU) {
    debug($fyh, "Consulta: " . $rsMen->Fields('RADI_NUME_RADI') . "\r\n");
} else {
    debug($fyh, "Error: $sqlMen. " . "\r\n");
}
while ($rsMen && ! $rsMen->EOF) {

    try {
        
        if (intval($rsMen->fields['IDMENSAJE']) > 0 ) 
        {
            $xml = '<soapenv:Envelope xmlns:seal="http://www.sealmail.co/" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                    <soapenv:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"><wsse:UsernameToken wsu:Id="UsernameToken-56A25A9935843352F317056112217587"><wsse:Username>ventanillabogota@urt.gov.co</wsse:Username><wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">6FsqxAa7Ll85isw+xuVUsbaKbv8=</wsse:Password><wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">9fzEzlUzKEc4JNmOHuNyWw==</wsse:Nonce><wsu:Created>2024-01-18T20:53:41.758Z</wsu:Created></wsse:UsernameToken></wsse:Security></soapenv:Header>
                       <soapenv:Body>
                          <seal:ObtenerTokenRequest>
                             <seal:idUsuario>ventanillabogota@urt.gov.co</seal:idUsuario>
                             <seal:idMensaje>' . $rsMen->fields['IDMENSAJE'] . '</seal:idMensaje>
                             <seal:generarPDF>true</seal:generarPDF>
                          </seal:ObtenerTokenRequest>
                       </soapenv:Body>
                    </soapenv:Envelope>';
    
            $ch = curl_init($wsdl472);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
                "Content-Type: text/xml"
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
            $respBody = curl_exec($ch);
            //debug($fyh, "Respuesta: $respBody. " . "\r\n");
            $info = curl_getinfo($ch);
            $code = $info['http_code'];
            curl_close($ch);
    
            $mensaje = readXML($respBody);
            
            $idMensaje1 = explode("\n", $mensaje)[0];
            $observa1 = explode("\n", $mensaje)[1];
            $token1 = explode("\n", $mensaje)[2];
    
            $idResponse = explode("=", $idMensaje1)[1];
            $observa = explode("=", $observa1)[1];
            $token = explode("=", $token1)[1];
            
            //debug($fyh, "Response: " . $token);
            
            if ($idResponse > 0) {
                
                $sqlval = "SELECT id, radi_nume_radi, body, date_send, response, date_recibe, status, idMensaje, observacion, idResponse, observaResponse, correo
                                FROM log_servicio
                                WHERE radi_nume_radi = ".$rsMen->fields['RADI_NUME_RADI']." and correo = '".$rsMen->fields['CORREO']."'
                                    and idMensaje = ".$rsMen->fields['IDMENSAJE']." and idResponse = $idResponse ";
                $rsval = $conn->Execute($sqlval);
                if ($rsval && !$rsval->EOF) {
                    debug($fyh, "Valida: $sqlval. " . "\r\n");
                } else {
                
                    $sqlu = "update log_servicio set idResponse = $idResponse, observaResponse = '$observa' 
                            where radi_nume_radi = " . $rsMen->fields['RADI_NUME_RADI'] . " and idMensaje = " . $rsMen->fields['IDMENSAJE'];
                    $rs = $conn->Execute($sqlu);
            
                    if (! empty($code) and $code == 200) {
                        
                        $rutaPdf = substr($rsMen->fields['DATE_RECIBE'], 0, 4) . "/acusecorreoelectronico/" . $rsMen->fields['RADI_NUME_RADI'] . "_" . date('dmYHis') . ".pdf";
                        
                        $decoded = base64_decode($token);
                        $resp = file_put_contents(BODEGAPATH . $rutaPdf, $decoded);
                        if ($resp === false || $resp == -1) {
                            debug($fyh, "Error: No se genero el archivo. " . "\r\n");
                        } else {
                            debug($fyh, "Archivo Generado !! " . "\r\n");
                        }
                        
                        if ($idResponse == 7) {
                            $codtx = 49;
                        } elseif ($idResponse == 3) {
                            $codtx = 43;
                        } elseif ($idResponse == 2) {
                            $codtx = 112;
                        } elseif ($idResponse == 51) {
                            $codtx = 114;
                        } elseif ($idResponse == 52) {
                            $codtx = 115;
                        } elseif ($idResponse == 53) {
                            $codtx = 116;
                        } elseif ($idResponse == 54) {
                            $codtx = 117;
                        } elseif ($idResponse == 55) {
                            $codtx = 118;
                        } elseif ($idResponse == 56) {
                            $codtx = 119;
                        } elseif ($idResponse == 57) {
                            $codtx = 120;
                        } elseif ($idResponse == 58) {
                            $codtx = 121;
                        } elseif ($idResponse == 1) {
                            $codtx = 123;
                        } elseif ($idResponse == 31) {
                            $codtx = 124;
                        } else {
                            $codtx = 122;
                        }
                                            
                        $sqlh = "insert into SGD_HIST_CERTIMAIL (RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, ID_TTR_HCTM, FECHA, CORREO) 
                                values (" . $rsMen->fields['RADI_NUME_RADI'] . ", '$rutaPdf', '" . $rsU->Fields('USUA_DOC') . "', '" . $rsU->Fields('USUA_LOGIN') . "', $codtx, GETDATE(), '".$rsMen->fields['CORREO']."')";
                        $okh = $conn->Execute($sqlh);
                        if ($okh) {
                            debug($fyh, "Cargue exitoso: " . $rsMen->fields['RADI_NUME_RADI'] . "\r\n");
                        } else {
                            debug($fyh, "Cargue fallido: " . $sqlh . "\r\n");
                        }
                    } else {
                        
                        debug($fyh, "Error: " . $code . "\r\n");
                    }
                }
            } else {
                
                debug($fyh, "Error: " . $observa . "\r\n");
            }
        } else {
            debug($fyh, "Error Id: " . $rsMen->fields['IDMENSAJE'] . "\r\n");
        }
        
    } catch (Exception $e) {
        debug($fyh, "Error: " . $e->getMessage() . "\r\n");
        // echo $e->getMessage();
    }
    
    $rsMen->MoveNext();
}

?>