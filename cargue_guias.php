<?php
set_time_limit(0);
ini_set('display_errors', 0);
echo "\n" . "Inicia Alarmas: " . date('Y/m/d_h:i:s') . "\n";

$ruta_raiz = ".";
include_once dirname(__FILE__) . "\\config.php";
//include_once dirname(__FILE__) . "\\include\\db\\ConnectionHandler.php";

require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
#############################################################################
try {
    $conn = NewADOConnection($dsn);
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    echo dirname(__FILE__);
    
//try {
//    $db = new ConnectionHandler($ruta_raiz);
//    echo dirname(__FILE__);
//    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}

///  PRIMER PASO ///
$count = 0;
$guias = "";
$radiGuias = array();

$isql = "SELECT R.RADI_NUME_SAL, R.SGD_RENV_PLANILLA, R.SGD_RENV_NUMGUIA
         FROM SGD_RENV_REGENVIO R INNER JOIN SGD_FENV_FRMENVIO F ON R.SGD_FENV_CODIGO = F.SGD_FENV_CODIGO
         WHERE SGD_RENV_NUMGUIA IS NOT NULL AND SGD_RENV_NUMGUIA <> '0' AND SGD_RENV_NUMGUIA <> '1'
            AND F.SGD_FENV_ORIGEN = 0 AND SGD_RENV_FECH BETWEEN '2020-01-01 00:00:00' AND replace(convert(varchar, getdate(), 111), '/','-') + ' 23:59:59'
			AND R.RADI_NUME_SAL NOT IN (SELECT RADICADO FROM CONTROL_GUIAS WHERE RADICADO = R.RADI_NUME_SAL)
         ORDER BY SGD_RENV_FECH DESC ";

$result = $conn->Execute($isql);
if ($result && ! $result->EOF) {
    while (! $result->EOF) {

        $radicado = $result->fields['RADI_NUME_SAL'];
        $guia = $result->fields['SGD_RENV_NUMGUIA'];
        
        if ($count < 1000) {
            $radiGuias[] = "$guia,$radicado";        
    
            if (strlen($guias) > 1) {
                $guias .= ',';
            }
            $guias .= $guia;
        } else {
            $sqlSel = "SELECT IDCONTROL, NUMERO_GUIA, TRANSACCION, RADICADO, ESTADO, RUTA
                    FROM CONTROL_GUIAS
                    WHERE RADICADO = $radicado";
            $rsSel = $conn->Execute($sqlSel);
            if ($rsSel && ! $rsSel->EOF) {
                echo "Ya se proceso el radicado " . $radicado . "\n";
            } else {
                $sqlIns = " INSERT INTO CONTROL_GUIAS (NUMERO_GUIA, TRANSACCION, RADICADO, ESTADO)
                    VALUES ('$guia', 0, $radicado, 0)";
                $rsIns = $conn->Execute($sqlIns);
            }
        }
        
        $count += 1;
        $result->MoveNext();
    }
}

if ($count < 1000) {
    $sqlSel = "SELECT IDCONTROL, NUMERO_GUIA, TRANSACCION, RADICADO, ESTADO, RUTA
                FROM CONTROL_GUIAS
                WHERE ESTADO = 0 AND TRANSACCION = 0 ";
    $rsSel = $conn->Execute($sqlSel);
    if ($rsSel && ! $rsSel->EOF) {
        while (! $rsSel->EOF) {
            $radicado = $rsSel->fields['RADICADO'];
            $guia = $rsSel->fields['NUMERO_GUIA'];
            
            if ($count < 1000) {
                $radiGuias[] = "$guia,$radicado";
                
                if (strlen($guias) > 1) {
                    $guias .= ',';
                }
                $guias .= $guia;
            } 
            
            $count += 1;
            $rsSel->MoveNext();
        }
    } 
}

$raw_xml = '{ "cargaPruebasEntrega": {
                "nitEmpresa": 900062917-9,
                    "listaGuias": {
                        "numerosGuia": [
                            ' . $guias . '
                         ]
                     }
                 }
            }';
echo $raw_xml . '\n';

$url = "$rutaWSguias?Usuario=$usuarioWS&Contraseña=$contrasenaWS";
$headers = array(
    'Content-type: application/json',
    'Accept: application/json',
    'Authorization: Bearer Bearer 403875e9e57440874702902c9e687dd9',
    'Accept-Encoding: gzip, deflate, br',
    'Connection: keep-alive'
);

$ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw_xml);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
        
    $json = json_decode($response, true);
    if ($json[cargaPruebasEntregaRS][respuesta][codigoRespuesta] == "00") {
        $transaccion = $json[cargaPruebasEntregaRS][identificadorTransaccion];
        
        for ($i=0; $i < count($radiGuias); $i++) {
            $dats = explode(',', $radiGuias[$i]);
            $gui = $dats[0];
            $rad = $dats[1];
            
            $sqlSel = "SELECT IDCONTROL, NUMERO_GUIA, TRANSACCION, RADICADO, ESTADO, RUTA
                    FROM CONTROL_GUIAS
                    WHERE RADICADO = $rad AND NUMERO_GUIA = $gui ";
            $rsSel = $conn->Execute($sqlSel);
            if ($rsSel && ! $rsSel->EOF) {
                $sqlUpd = " UPDATE CONTROL_GUIAS SET TRANSACCION = $transaccion, ESTADO = 1
                        WHERE RADICADO = $rad AND NUMERO_GUIA = $gui ";
                $rsUpd = $conn->Execute($sqlUpd);
            } else {
                $sqlIns = " INSERT INTO CONTROL_GUIAS (NUMERO_GUIA, TRANSACCION, RADICADO, ESTADO)
                        VALUES ('$gui', $transaccion, $rad, 1)";
                $rsIns = $conn->Execute($sqlIns);
            }
        }
    }
    
    echo "Transaccion: ". $transaccion."</br>";
    echo 'Victory!!!! </br>';
    echo $response . '</br>';
} else {
    echo $response. '</br>';
    die();
}

// ############################################################################
echo "\n" . "Finaliza Alarmas: " . date('Y/m/d_h:i:s') . "\n";
?>