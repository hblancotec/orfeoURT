<?php
set_time_limit(0);
ini_set('display_errors', 0);
echo "\n" . "Inicia Cargue Guias 2: " . date('Y/m/d_h:i:s') . "</br>";

$ruta_raiz = ".";
require dirname(__FILE__) . "\\config.php";
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;

try {
    $conn = NewADOConnection($dsn);
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
} catch (Exception $e) {
    echo $e->getMessage();
}

///  SEGUNDO PASO ///

$isqlt = "SELECT IDCONTROL, NUMERO_GUIA, TRANSACCION, RADICADO, ESTADO
        FROM CONTROL_GUIAS
        WHERE ESTADO = 1 ";
$rst = $conn->Execute($isqlt);
if ($rst && ! $rst->EOF) {
    $transaccion = "";
    $transac = array();
    while (! $rst->EOF) {
        $transaccion = $rst->fields['TRANSACCION'];
        if (!in_array($transaccion, $transac)) {
            $transac[] = $transaccion;
        }
        $rst->MoveNext();
    }
    
    for ($i=0; $i < count($transac); $i++) {
        
        echo "Transaccion: ". $transac[$i]."\n";
        $url = "$rutaWSTransaccion/$transac[$i]?Usuario=$usuarioWS&Contraseña=$contrasenaWS";
        $headers = array(
            'Content-type: application/json',
            'Accept: application/json',
            'Authorization: Bearer Bearer 403875e9e57440874702902c9e687dd9',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive'
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $valida = false;
        if ($http_code == 200) {           
            $json = json_decode($response, true);
            if ($json[estadoCargaRS][respuesta][codigoRespuesta] == "00") {
                $estadocarga = $json[estadoCargaRS][estadoCarga][codigoEstadoCarga];
                if ($estadocarga != 1 && $estadocarga != 3) {
                    $archivo = $json[estadoCargaRS][estadoCarga][nombreArchivoGenerado];
                    $nomarchivo = explode(".", $archivo)[0];
                    
                    $valida = true;
                }
                if ($estadocarga == 3) {
                    $sqlUpd = " UPDATE CONTROL_GUIAS SET TRANSACCION = 0, ESTADO = 0 WHERE TRANSACCION = $transac[$i]";
                    $rsUpd = $conn->Execute($sqlUpd);
                    
                    $valida = false;
                }
            }
            
            if ($valida) {
                $local_file = BODEGAPATH ."tmp/". $archivo;
                $server_file = $archivo;
                
                $conn_id = ftp_connect($ftp_server);
                $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                
                if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
                    echo "Successfully written to $local_file \n";
                }
                else {
                    echo "There was a problem \n";
                    die("Error descargando el archivo del ftp ");
                }
                ftp_close($conn_id);
                
                $zip = new ZipArchive;
                $comprimido= $zip->open($local_file);
                if ($comprimido === TRUE) {
                    $zip->extractTo(BODEGAPATH ."guias/");
                    $zip->close();
                    echo 'El fichero se descomprimio correctamente! \n';
                } else {
                    echo 'Error descomprimiendo el archivo zip \n';
                    die("Error descomprimiendo el archivo zip ");
                }
                unlink($local_file);
                
                $sqlUpd = " UPDATE CONTROL_GUIAS SET RUTA = '$nomarchivo', ESTADO = 2 WHERE TRANSACCION = $transac[$i]";
                $rsUpd = $conn->Execute($sqlUpd);
                
                echo $response;
                echo 'Victory!!!! \n';
            } else {
                echo "Estado = " . $json[estadoCargaRS][estadoCarga][nombreEstadoCarga] ."\n";
            }
        } else {
            echo $response;
            die();
        }
    }
}

///  FIN SEGUNDO PASO ///

// ############################################################################
echo "\n" . "Finaliza Cargue Guias 2: " . date('Y/m/d_h:i:s') . "\n";
?>