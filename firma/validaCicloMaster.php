<?php
/**
 * Validar los estados de conversi�n de archivos de DOC a PDF
 *
 * @version 1.0
 * @author hladino
 *
 * @version 1.1
 * @author cesgomez
 */
session_start();

// Configuraci�n para que la petici�n ajax pueda retornarse de tipo JSON
header('Content-Type: application/json;charset=utf-8');

// Creaci�n de la variable llamada result_json de tipo array para almacenar todos los eventos del archivo
// con el atributo pagina si es igual 1 genera el c�digo QR en el PDF de lo contrario NO
$result_json = array(
    "estado" => - 1,
    "mensaje" => "",

    "radicado" => "",
    "procesoMaster" => "",
    "procesoDetalle" => "",

    "ruta" => "",
    "rutafile" => "",

    "rutaOrigenDoc" => "",
    "rutaDestinoPDF" => "",
    "pagina" => 0
);

// Creaci�n de la variable llamada session_ok de tipo boolean para saber el estado de la conexi�n
$session_ok = true;
// Validaci�n de la sessi�n si caduc�
if (count($_SESSION) == 0) {
    $msgError = "Su sessi�n caduc�.";
    $session_ok = false;
    $filePdfOk = false;

    // Asignaci�n en el arreglo result_json para mensaje y estado
    // Se usa la funci�n utf8_encode para que no generar conflicto con car�cteres especiales
    $result_json["mensaje"] = $msgError;
    $result_json["estado"] = - 1;
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}


function insertHistoric($db, $radicado, $comments, $tx)
{
    $insertStateSql = "INSERT INTO HIST_EVENTOS (DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI, HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO, HIST_DOC_DEST, DEPE_CODI_DEST)
            VALUES (" . $_SESSION['dependencia'] . ", " . $db->conn->sysTimeStamp . " , " . $_SESSION['codusuario'] . ", " .$radicado.  ", '" .$comments. "' , " . $_SESSION['codusuario'] . ", '" . $_SESSION['usua_doc'] . "' , " . $tx . " , '" .$_SESSION['usua_doc']. "' , " . $_SESSION['dependencia'] . ")";
    $db->conn->Execute($insertStateSql );
}

function finlizaProceso($db, $idcf) {
    $pathSuffix = BODEGAPATH;
    $pathSuffix = str_replace ("/" , "\\", $pathSuffix);
    // 0=Solicitado 1=Firmado 2=Modificacion 3=Rechazado 4=FinalizadoPorRechazo
    $updateStateSql = "UPDATE d set d.estado = 1 FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . " and d.usua_doc = '"  . $_SESSION['usua_doc'] . "' and d.usua_login = '" . $_SESSION['login'] . "'";
    $ok1 = $db->conn->Execute($updateStateSql);
    // Get to total number of signers
    $totalSignersSQL = "SELECT COUNT(1) FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . "";
    $totalSigners = $db->conn->GetOne($totalSignersSQL);
    // Get the current number of signers successfully applied
    $currentSignersSQL =  "select COUNT(1) from SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . " and d.estado = 1";
    $currentSigners = $db->conn->GetOne($currentSignersSQL);
    // Insertamos en el hist�rico
    $sqlRadHist = "select radi_nume_radi, rutapdf from SGD_CICLOFIRMADOMASTER where idcf = " . $idcf . "";
    $rsdt = $db->conn->query($sqlRadHist);
    if ($rsdt && !$rsdt->EOF) {
        $radicadoActual = $rsdt->fields['radi_nume_radi'];
        $file = $rsdt->fields['rutapdf'];
    }
    
    $file = str_replace($pathSuffix, "", $file);
    if ($totalSigners === $currentSigners)
    {
        // Actualizar el pdf al radicado.
        //$sql = "select r.radi_depe_radi, r.radi_nume_radi from radicado r where r.radi_nume_radi = (select m.radi_nume_radi from SGD_CICLOFIRMADOMASTER m where m.idcf = " . $idcf . " and m.estado = 1)";
        $sql = "select r.radi_depe_radi, m.radi_nume_radi
                from SGD_CICLOFIRMADOMASTER m INNER JOIN radicado r ON m.radi_nume_radi = r.radi_nume_radi
                where m.idcf = " . $idcf . " and m.estado = 1 ";
        $rs = $db->conn->Execute($sql);
        if ($rs && !$rs->EOF)
        {
            $tmpRadi = $rs->fields['radi_nume_radi'];
            $tmpdepe = $rs->fields['radi_depe_radi'];
            $nombrear = $tmpRadi."_".rand(1, 99999).".pdf";
            $TmpFile = substr($tmpRadi,0,4).'\\'.$tmpdepe.'\\'.$nombrear;
            $TmpFileAnex = substr($tmpRadi,0,4).'/'.$tmpdepe.'/docs/'.$nombrear;
            // 1=Iniciado 2=Solicitud Cambio 3=Finalizado Bien 4=Finalizado Cancelado
            $updateStateMasterSql = "UPDATE SGD_CICLOFIRMADOMASTER set estado = 3 where idcf = " . $idcf . "";
            $rsC = $db->conn->Execute($updateStateMasterSql);
            // Copie el pdf firmado al radicado respectivo.
            if (rename(BODEGAPATH.$file, BODEGAPATH.$TmpFile))
            {
                //if ($anexNum > 1) {
                $sql1 = "update radicado set radi_path = '$TmpFile' where radi_nume_radi = $tmpRadi";
                $rsRad = $db->conn->Execute($sql1);
                // Insertamos en el hist�rico evento de cambio de imagen
                insertHistoric($db, $tmpRadi, "Circuito de firma finalizado satisfactoriamente.", 23);
                $resultado = "1";
                //copy(BODEGAPATH.$TmpFile, BODEGAPATH.$TmpFileAnex);
            }
        }
    } else {
        $resultado = "1";
    }
}


if ($session_ok) {
    $ruta_raiz = '..';
    if (! $_SESSION['dependencia'] || ! $_SESSION['usua_doc']) {
        include "../rec_session.php";
    }

    require $ruta_raiz . '/config.php';
    require $ruta_raiz . '/include/db/ConnectionHandler.php';
    // Se crea la conexion con la base de datos
    $db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    // $idc = $_POST['idCiclo'];
    $idcs = explode("|", $_POST['idCiclo']);
    foreach ($idcs as $idc) {

        // Inicializaci�n de las variables
        $filePdfOk = false;
        $msgError = "No hay radicados para firmar";

        // Asignaci�n en el arreglo result_json para mensaje y estado
        $result_json["mensaje"] = $msgError;
        $result_json["estado"] = - 1;
        // validamos que el usuario actual sea firmante y que el ciclo no est� bajo modificacion(estado=2)
        // se agreg� el radicado, y el estado de la tabla SGD_CICLOFIRMADODETALLE
        $sql = "select m.rutapdf, m.radi_nume_radi, m.estado, d.estado as estadoDetalle, d.usua_login, d.iddcf, d.usua_doc " . "from sgd_ciclofirmadomaster m " . "inner join SGD_CICLOFIRMADODETALLE d on m.idcf=d.idcf and d.usua_doc='" . $_SESSION['usua_doc'] . "' and d.usua_login='" . $_SESSION['login'] . "' " . "where m.idcf=$idc and m.estado=1";
        $rs = $db->conn->Execute($sql);
        if ($rs && !$rs->EOF) {
            while (!$rs->EOF) {
            //while ($arr = $rs->FetchRow()) {
                $rutaFile = BODEGAPATH . $rs->fields['rutapdf'];
                $file = $serverCompartida . $rs->fields['rutapdf'];
                
                $info = pathinfo($rs->fields['rutapdf']);
                
                $rutaTemp = "E:\\TmpOrfeo\\" . $info['filename'] . ".docx";
                $filePdf = $info['filename'] . ".pdf";
                $usua_doc = $rs->fields['usua_doc'];
                
                // ---------------------------------------
                // Asignaci�n en el arreglo result_json para radicado, procesoMaster, procesoDetalle y rutafile
                // ---------------------------------------
                $result_json["radicado"] = $rs->fields['radi_nume_radi'];
                if ($rs->fields['estado'] == 1) {
                    $result_json["procesoMaster"] = "Iniciado";
                }
                if ($rs->fields['estado'] == 2) {
                    $result_json["procesoMaster"] = "Solicitud Cambio";
                }
                if ($rs->fields['estado'] == 3) {
                    $result_json["procesoMaster"] = "Finalizado Bien";
                }
                if ($rs->fields['estado'] == 4) {
                    $result_json["procesoMaster"] = "Finalizado Cancelado";
                }

                if ($rs->fields['estadoDetalle'] == 0) {
                    $result_json["procesoDetalle"] = "Solicitado";
                }
                if ($rs->fields['estadoDetalle'] == 1) {
                    $result_json["procesoDetalle"] = "Firmado";
                }
                if ($rs->fields['estadoDetalle'] == 2) {
                    $result_json["procesoDetalle"] = "Modificaci�n";
                }
                if ($rs->fields['estadoDetalle'] == 3) {
                    $result_json["procesoDetalle"] = "Rechazado";
                }
                if ($rs->fields['estadoDetalle'] == 4) {
                    $result_json["procesoDetalle"] = "Finalizado Por Rechazo";
                }

                $result_json["rutafile"] = $rutaFile;
                // ---------------------------------------
                //
                // ---------------------------------------

                switch ($info['extension']) {
                    case 'doc':
                    case 'docx':
                        {

                            if (file_exists($rutaFile)) {

                                if (file_exists("$ruta_raiz/bodega/firmas/$usua_doc.jpg")) {

                                    // Creaci�n de etiqueta volver_procesar para ser usado con la palabra reservada goto
                                    //volver_procesar:
                                    $filePdfOk = file_exists(BODEGAPATH . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf);
                                    if (! $filePdfOk) {

                                        $usuarios = "";
                                        $sql = "select USUA_CODI, USUA_DOC, USUA_EMAIL, USUA_CARGO, USUA_NOMB  from usuario where USUA_DOC = '$usua_doc'";
                                        $rsu = $db->conn->query($sql);
                                        if ($rsu && ! $rsu->EOF) {
                                            $usuarios = $rsu->fields['USUA_DOC']; // . $rsu->fields['USUA_NOMB'] ."%". $rsu->fields['USUA_CARGO'];
                                            $usuario = $rsu->fields['USUA_DOC'];
                                        }

                                        $ADODB_COUNTRECS = true;
                                        $valFirma = 0;
                                        $sqlc = "SELECT M.RADI_NUME_RADI, M.sgd_ciclo_fechasol, M.usua_login, M.usua_doc, M.estado, M.rutapdf, D.usua_login, D.usua_doc, D.sgd_firma_detalle, D.estado, D.tiposol
                                            FROM SGD_CICLOFIRMADOMASTER M INNER JOIN SGD_CICLOFIRMADODETALLE D ON M.idcf = D.idcf
                                            WHERE M.idcf = $idc AND D.estado = 0";
                                        $rsc = $db->conn->query($sqlc);
                                        if ($rsc && ! $rsc->EOF) {
                                            $radicado = $rsc->fields['RADI_NUME_RADI'];
                                            $recordCount = $rsc->recordCount();
                                            if ($recordCount == 1) {
                                                $valFirma = 1;
                                            }
                                        }

                                        // Asignaci�n en el arreglo result_json para rutaOrigenDoc y rutaDestinoPDF
                                        $result_json["rutaOrigenDoc"] = $rutaFile;
                                        $result_json["rutaDestinoPDF"] = BODEGAPATH . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf;
                                        try {

                                            /*$client = new SoapClient($wsdlOffice, array(
                                                'trace' => 1,
                                                'exceptions' => true,
                                                'cache_wsdl' => WSDL_CACHE_NONE,
                                                'soap_version' => SOAP_1_1,
                                                'features' => SOAP_WAIT_ONE_WAY_CALLS
                                            ));

                                            $arregloDatos = array(
                                                'rutaOrigenDoc' => $file,
                                                'texto' => '*firmaUsuario*',
                                                'usuarios' => $usuarios,
                                                'varias' => $valFirma,
                                                'rutaDestinoPDF' => $serverCompartida . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf
                                            );*/
                                            
                                            $rutaFirma = "E:\\OI_OrfeoPHP7_64\\orfeo\\bodega\\firmas\\".$usuario.".jpg";
                                            //$rutaFirma = "E:\\TmpOrfeo\\firmas\\".$usuario.".png";
                                            //copy($file, $rutaTemp);
                                            
                                            $arregloDatos = array(
                                                'rutaOrigenDoc' => $file,
                                                'rutafirma' => $rutaFirma
                                            );

                                            $body = implode(";", $arregloDatos);
                                            $sqli = "insert into log_firma(radi_nume_radi,datos_send,date_send, estado) values ($radicado,'$body', GETDATE(), 1)";
                                            $rsIn = $db->conn->Execute($sqli);

                                            //$result = $client->firmaToPdf($arregloDatos);
                                            $validaEjec = false;
                                            try {
                                                
                                                /*$fechaact = date("YmdHis");
                                                $bat_filename = "E:\\OI_OrfeoPHP7_64\\orfeo\\firma\\alarma_firma.bat";
                                                $bat_log_filename = "E:\\logs\\alarma_firma_".$fechaact.".log";
                                                $bat_file = fopen($bat_filename, "w");
                                                if($bat_file) {
                                                    fwrite($bat_file, "@ECHO OFF"."\n");
                                                    //fwrite($bat_file, 'E:\php-7.2.16-Win32-VC15-x64\php-win.exe -f E:\OI_OrfeoPHP7_64\orfeo\firma\combina.exe '.$file.' '.$radicado.' E:\\OI_OrfeoPHP7_64\\orfeo\\bodega\\firmas\\'.$usuario.'.png >> '.$bat_log_filename."\n");
                                                    fwrite($bat_file, 'E:\OI_OrfeoPHP7_64\orfeo\firma\combina.exe '.$file.' '.$radicado.' E:\\OI_OrfeoPHP7_64\\orfeo\\bodega\\firmas\\'.$usuario.'.png >> '.$bat_log_filename."\n");
                                                    fwrite($bat_file, "echo End proces >> ".$bat_log_filename."\n");
                                                    fwrite($bat_file, "EXIT"."\n");
                                                    fclose($bat_file);
                                                }
                                                $exe = "start /b ".$bat_filename;
                                                if( pclose(popen($exe, 'r')) ) {
                                                    $validaEjec = true;
                                                }*/
                                                                                                
                                                $cmd = 'E:\\OI_OrfeoPHP7_64\\orfeo\\firma\\combina.exe '.$file.' '.$radicado.' E:\\OI_OrfeoPHP7_64\\orfeo\\bodega\\firmas\\'.$usuario.'.jpg ';
                                                $handle = popen("start /B ". $cmd, "r");
                                                $read = fread($handle, 2096);
                                                pclose($handle);
                                                $validaEjec = true;
                                                
                                            } catch (Exception $ex) {
                                                $filePdfOk = false;
                                                $msgError = utf8_encode("Hubo un error en firmar el documento. Comuníquese con soporte técnico! " . $ex->getMessage());
                                                
                                                $result_json["mensaje"] = $msgError;
                                                $result_json["estado"] = - 1;
                                            }

                                            $sqlu = "update log_firma set response = '" . $cmd . "', date_recibe = GETDATE() where radi_nume_radi = $radicado ";
                                            $rsUp = $db->conn->Execute($sqlu);

                                            if ($validaEjec) {
                                                   
                                                //$result_json["mensaje"] = "Proceso de firma iniciado correctamente !!";
                                                //$result_json["estado"] = "11";
                                                
                                                //unlink($file);
                                                if (file_exists($serverCompartida . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf)) {
                                                    
                                                    $filePdfOk = true;
                                                    
                                                    $sql = "update sgd_ciclofirmadomaster set rutapdf = 'dav/" . date('Y') . "/" . $filePdf . "' where idcf=$idc";
                                                    $rs1 = $db->conn->Execute($sql);
                                                    if ($rs1 !== false) {
                                                        $rs1->Close();
                                                    }
                                                    finlizaProceso($db, $idc);
                                                    
                                                    $result_json["mensaje"] = "Proceso finalizado correctamente !!";
                                                    $result_json["estado"] = "11";
                                                } else {
                                                    $filePdfOk = false;
                                                    
                                                    $msgError = utf8_encode("Hubo un error al convertir el documento. Comuníquese con soporte técnico! ");
                                                    
                                                    $result_json["mensaje"] = $msgError;
                                                    $result_json["estado"] = - 1;
                                                }

                                                /*
                                                 * } else {
                                                 * $filePdfOk = false;
                                                 * // Asignaci�n en el arreglo result_json para mensaje y estado
                                                 * $msgError = utf8_encode("Hubo un error en convertir la plantilla Word a PDF. Comuníquese con soporte técnico!");
                                                 *
                                                 * // Asignaci�n en el arreglo result_json para mensaje y estado
                                                 * $result_json["mensaje"] = $msgError;
                                                 * $result_json["estado"] = -1;
                                                 * }
                                                 */
                                            } else {
                                                
                                                $sql = "SELECT lower(rtrim(RADI_PATH)) as EXT FROM RADICADO WHERE RADI_NUME_RADI = " . $radicado;
                                                $ruta = $db->conn->GetOne($sql);
                                                $info = pathinfo($ruta);
                                                if (file_exists(BODEGAPATH . $ruta)) {
                                                    if (file_exists(BODEGAPATH . 'dav/' . date('Y') . '/' . $info['filename'] . '.' . $info['extension'])) {
                                                        unlink(BODEGAPATH . 'dav/' . date('Y') . '/' . $info['filename'] . '.' . $info['extension']);
                                                    }
                                                    copy(BODEGAPATH . $ruta, BODEGAPATH . 'dav/' . date('Y') . '/' . $info['filename'] . '.' . $info['extension']);
                                                    $filePdf = $info['filename'] . '.pdf';
                                                    if (file_exists(BODEGAPATH . 'dav/' . date('Y') . '/' . $filePdf)) {
                                                        //unlink(BODEGAPATH . 'dav/' . date('Y') . '/' . $filePdf);
                                                        $sql = "update sgd_ciclofirmadomaster set rutapdf = 'dav/" . date('Y') . "/" . $filePdf . "' where idcf=$idc";
                                                        $rs1 = $db->conn->Execute($sql);
                                                    }
                                                }

                                                $filePdfOk = false;
                                                $msgError = utf8_encode("Hubo un error en firmar el documento. Comuníquese con soporte técnico! " . $result->firmaMecanicaResult);

                                                $result_json["mensaje"] = $msgError;
                                                $result_json["estado"] = - 1;
                                            }
                                        } catch (Exception $e) {
                                            $filePdfOk = false;

                                            $sqlu = "update log_firma set response = '" . $e->getMessage() . "', date_recibe = GETDATE() where radi_nume_radi = $radicado ";
                                            $rsUp = $db->conn->Execute($sqlu);
                                            // Asignaci�n en el arreglo result_json para mensaje y estado
                                            $msgError = utf8_encode("Comuníquese con soporte técnico, No hay conexión al servicio de conversión de documentos. " . $e->getMessage());
                                            $result_json["mensaje"] = $msgError;
                                            $result_json["estado"] = - 1;
                                        }
                                    }
                                    if ($filePdfOk) {
                                        try {
                                            $sql = "update sgd_ciclofirmadomaster set rutapdf = 'dav/" . date('Y') . "/" . $filePdf . "' where idcf=$idc";
                                            $rs1 = $db->conn->Execute($sql);
                                            if ($rs1 !== false)
                                                $rs1->Close();
                                        } catch (Exception $e) {
                                            $filePdfOk = false;

                                            // Asignaci�n en el arreglo result_json para mensaje y estado
                                            $msgError = utf8_encode("Comuníquese con soporte técnico, No hay conexión al servicio de conversión de documentos. " . $e->getMessage());
                                            $result_json["mensaje"] = $msgError;
                                            $result_json["estado"] = - 1;
                                        }
                                    } else {
                                        $ciclo = false;
                                    }
                                } else {
                                    $ciclo = false;
                                    $msgError = utf8_encode("Comuníquese con soporte t�cnico, No se encuentra la firma en el directorio.");

                                    // Asignaci�n en el arreglo result_json para mensaje y estado
                                    $result_json["mensaje"] = $msgError;
                                    $result_json["estado"] = - 1;
                                }
                            } else {
                                $ciclo = false;
                                $msgError = utf8_encode("Comuníquese con soporte técnico, No se encuentra el documento en el directorio.");

                                // Asignaci�n en el arreglo result_json para mensaje y estado
                                $result_json["mensaje"] = $msgError;
                                $result_json["estado"] = - 1;
                            }
                        }
                        break;
                    case 'pdf':
                        {
                            $ciclo = true;
                            $filePdfOk = file_exists(BODEGAPATH . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf);
                            if (file_exists(BODEGAPATH . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf)) {

                                // Asignaci�n en el arreglo result_json para ruta, mensaje y estado
                                $result_json["ruta"] = BODEGAPATH . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf;
                                if ($arr['estadoDetalle'] == 1) {
                                    $result_json["mensaje"] = "El documento ya se encuentra firmado por " . $arr['usua_login'];
                                    $result_json["estado"] = 1;
                                    $result_json["pagina"] = 0;
                                } else {
                                    
                                    $filePdfOk = true;
                                    // unlink($file);
                                    $sql = "update sgd_ciclofirmadomaster set rutapdf = 'dav/" . date('Y') . "/" . $filePdf . "' where idcf=$idc";
                                    $rs1 = $db->conn->Execute($sql);
                                    if ($rs1 !== false) {
                                        $rs1->Close();
                                    }
                                        
                                    finlizaProceso($db, $idc);
                                        
                                    // Asignación en el arreglo result_json para mensaje y estado
                                    $result_json["mensaje"] = "Proceso finalizado correctamente !!";
                                    $result_json["estado"] = "11";
                                        
                                    /*$result_json["mensaje"] = "Ya se encuentra el documento para ser firmado";
                                    $result_json["estado"] = 1;
                                    $result_json["pagina"] = - 1;*/
                                }
                            } else {

                                // Asignaci�n en el arreglo result_json para mensaje y estado
                                $result_json["estado"] = - 1;
                                $result_json["mensaje"] = utf8_encode("Comuníquese con soporte técnico. No se encuentra el documento en el directorio asignado");

                                // se usa la palabra reservada goto para volver hacer el proceso de conversi�n de DOC a PDF
                                //goto volver_procesar;
                            }
                        }
                        break;
                    default:
                        {
                            $ciclo = false;
                        }
                        break;
                }
                
                $rs->MoveNext();
            }
        } else {
            
            $sql = "select m.rutapdf, m.radi_nume_radi, m.estado, d.estado as estadoDetalle, d.usua_login, d.iddcf, d.usua_doc " . "from sgd_ciclofirmadomaster m " . "inner join SGD_CICLOFIRMADODETALLE d on m.idcf=d.idcf and d.usua_doc='" . $_SESSION['usua_doc'] . "' and d.usua_login='" . $_SESSION['login'] . "' " . "where m.idcf=$idc ";
            $rs = $db->conn->Execute($sql);
            if ($rs && !$rs->EOF) {
                
                $result_json["estado"] = 11;
                $result_json["mensaje"] = utf8_encode("El documento debe ser modificado !!");
                
            }
        }
    }
}
// Visualizac�n de la variable result_json para formatear el arreglo
echo json_encode($result_json, JSON_PRETTY_PRINT);
?>