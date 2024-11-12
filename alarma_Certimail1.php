<?php

ini_set('display_errors', true);
set_time_limit(0);
$ruta_raiz = ".";
require dirname(__FILE__) . "\\config.php";
//require ORFEOPATH . "include/db/ConnectionAlarmas.php";

require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
#############################################################################
$conn = NewADOConnection($dsnn);
$conn->SetFetchMode(ADODB_FETCH_ASSOC);

// Include Composer autoloader if not already done.
include 'parser2/Smalot/PdfParser/Parser.php';
include 'parser2/Smalot/PdfParser/PDFObject.php';
include 'parser2/Smalot/PdfParser/Pages.php';
include 'parser2/Smalot/PdfParser/Document.php';
include 'parser2/Smalot/PdfParser/Element.php';
include 'parser2/Smalot/PdfParser/Element/ElementBoolean.php';
include 'parser2/Smalot/PdfParser/Element/ElementString.php';
include 'parser2/Smalot/PdfParser/Element/ElementArray.php';
include 'parser2/Smalot/PdfParser/Element/ElementDate.php';
include 'parser2/Smalot/PdfParser/Element/ElementHexa.php';
include 'parser2/Smalot/PdfParser/Element/ElementName.php';
include 'parser2/Smalot/PdfParser/Element/ElementMissing.php';
include 'parser2/Smalot/PdfParser/Element/ElementNumeric.php';
include 'parser2/Smalot/PdfParser/Element/ElementXRef.php';
include 'parser2/Smalot/PdfParser/Font.php';
include 'parser2/Smalot/PdfParser/Header.php';
include 'parser2/Smalot/PdfParser/Page.php';
include 'parser2/Smalot/PdfParser/XObject/Form.php';
include 'parser2/Smalot/PdfParser/XObject/Image.php';
include 'parser2/tecnickcom/tcpdf/tcpdf_parser.php';

//$debug = (isset($_GET['debug']) ? $_GET['debug'] : FALSE);
$debug = true;
if ($debug) {
    $fyh = BODEGAPATH . "debug_" . date('Ymd_His') . "1.html";
    debug($fyh, "inicio script" . $fyh . "<br>");
}

require ORFEOPATH . "include/tx/Historico.php";
$sql = "SELECT USUA_CODI, DEPE_CODI, USUA_DOC, USUA_LOGIN FROM USUARIO WHERE USUA_LOGIN='$usrComodin'";
$rsU = $conn->Execute($sql);

$dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert}";
$mbox = imap_open($dsn . $carpetaLecturaCertimail, $correo_mail_0365, $passwd_mail_0365);

//$mbox = imap_open("{outlook.office365.com:993/imap/ssl/novalidate-cert/authuser=$correo_certimail/user=$correo_certimail}", $correo_certimail, $passwd_certimal);
//$mbox = imap_open("{outlook.office365.com:993/imap/ssl/authuser=notificaciones_sgdorfeo@dnp.gov.co}", "notificaciones_sgdorfeo@dnp.gov.co", "QY^SQngGQm+>AP");
if ($debug) {
    debug($fyh, "<br/><b>imap_open<b/> " . $mbox . " - " . date('Y-m-d h:i:s') . "<br>");
    debug($fyh, "<br/>N&uacute;mero Total de mensajes carpeta $carpetaLecturaCertimail: " . imap_num_msg($mbox) . "<br/>");
    debug($fyh, "imap_listmailbox " . date('Y-m-d h:i:s') . "<br>");
    debug($fyh, "<h1>Leyendo cada correo</h1><br/>");
}

$cntEmails = imap_num_msg($mbox);
for ($index = 1; $index <= $cntEmails; $index++) {
    $uid = imap_uid($mbox, $index);
    $header = imap_headerinfo($mbox, imap_msgno($mbox, $uid));

    $fromInfo = $header->from[0];
    $replyInfo = $header->reply_to[0];

    $details = array(
        "fromAddr" => (isset($fromInfo->mailbox) && isset($fromInfo->host)) ? $fromInfo->mailbox . "@" . $fromInfo->host : "",
        "fromName" => (isset($fromInfo->personal)) ? $fromInfo->personal : "",
        "replyAddr" => (isset($replyInfo->mailbox) && isset($replyInfo->host)) ? $replyInfo->mailbox . "@" . $replyInfo->host : "",
        "replyName" => (isset($replyTo->personal)) ? $replyto->personal : "",
        "subject" => (isset($header->subject)) ? $header->subject : "",
        "udate" => (isset($header->udate)) ? $header->udate : date_timestamp_get(date('Y-m-d H:i:s'.substr((string)microtime(), 1, 8))) );

    //Recepción del acuse de recibo del correo electrónico certificado certimail. Puede venir la información de la apertura.
    if ($details["fromAddr"] == "receipt@rpost.net") {
        $mailStruct = imap_fetchstructure($mbox, $uid, FT_UID);
        $attachments = getAttachments($mbox, $uid, $mailStruct, "");
        $rutaHTM = NULL;
        foreach ($attachments as $attachment) {
            if ($debug) {
                debug($fyh, 'Correo con uid = ' . $uid . ', part=' . $attachment["partNum"] . ', enc=' . $attachment["enc"] . ' y nombre de anexo ' . parsearString($attachment["name"]) . "<br/>");
            }
            if ($attachment["name"] == 'DeliveryReceipt.xml') {
                $fileD = downloadAttachment($mbox, $uid, $attachment["partNum"], $attachment["enc"], $path);
                $xml = simplexml_load_string($fileD);
                $radCopia = $xml->MessageClientCode;
                $radCopia = explode("_", $radCopia);
                $rutaHTM = date('Y', $details["udate"] )."/acusecorreoelectronico/" . uniqid($radCopia[0] . "_" . $radCopia[1] . "_") . ".pdf";
                if (is_array($radCopia) && count($radCopia) == 2) {
                    if ($debug) {
                        debug($fyh, "Radicado: " . $radCopia[0] . " y copia:" . $radCopia[1] . "<br/>");
                    }
                    $sql = "SELECT SGD_RENV_MAIL FROM SGD_RENV_REGENVIO WHERE RADI_NUME_SAL=" . $radCopia[0] . "  AND SGD_DIR_TIPO=" . ($radCopia[1] == "00" ? "1" : "7" . $radCopia[1]);
                    $emailE = $conn->GetOne($sql);
                    $objHist = new Historico($conn);
                    $ok = $objHist->insertarHistorico(array($radCopia[0]), $rsU->Fields('DEPE_CODI'), $rsU->Fields('USUA_CODI'), $rsU->Fields('DEPE_CODI'), $rsU->Fields('USUA_CODI'), "Destinatario " . $emailE, 43);
                }
            }
            if ($attachment["name"] == 'HtmlReceipt.htm') {
                $fileD = downloadAttachment($mbox, $uid, $attachment["partNum"], $attachment["enc"], $path);
                $fp = fopen(BODEGAPATH . $rutaHTM, 'w');
                fwrite($fp, $fileD);
                fclose($fp);
                $sql = "insert into SGD_HIST_CERTIMAIL (RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, ID_TTR_HCTM) values (" .
                        $radCopia[0] . ", '$rutaHTM', '" . $rsU->Fields('USUA_DOC') . "', '" . $rsU->Fields('USUA_LOGIN') . "', 43)";
                $conn->Execute($sql);
            }
        }
    }

    //Recepción del acuse de recibo del correo electrónico certificado 4-72. Puede venir la información de la apertura.
    if ($details["fromAddr"] == "no-reply@certificado.4-72.com.co") {
        $mailStruct = imap_fetchstructure($mbox, $uid, FT_UID);
		$asuntoDecodificado = iconv_mime_decode($details["subject"]);
		
        if ( (strpos($asuntoDecodificado, "Prueba de entrega") || strpos($asuntoDecodificado, "OPENED")) == 0) {
			$CodTx = ((strpos($asuntoDecodificado, "Prueba de entrega")=== false) ? 49 : 43);															
            $attachments = getAttachments($mbox, $uid, $mailStruct, "");
            $rutaHTM = NULL;
            foreach ($attachments as $attachment) {
                if ($debug) {
                    debug($fyh, 'Correo con uid = ' . $uid . ', part=' . $attachment["partNum"] . ', enc=' . $attachment["enc"] . ' y nombre de anexo ' . parsearString($attachment["name"]) . "<br/>");
                }
                
                $fileD = downloadAttachment($mbox, $uid, $attachment["partNum"], $attachment["enc"], $path);
                

                $radCopia = substr($asuntoDecodificado, strpos($asuntoDecodificado, "(") + 1, strpos($asuntoDecodificado, ")")-1-strpos($asuntoDecodificado, "("));
                $radCopia = explode("_", $radCopia);
                if (is_array($radCopia) && count($radCopia) == 2) {
                    
                    $rutaHTM = date('Y', $details["udate"] )."/acusecorreoelectronico/" . uniqid($radCopia[0] . "_" . $radCopia[1] . "_") . ".pdf";
                    $fp = fopen(BODEGAPATH . $rutaHTM, 'w');
                    fwrite($fp, $fileD);
                    fclose($fp);
                    
                    $sql = "insert into SGD_HIST_CERTIMAIL (RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, ID_TTR_HCTM, FECHA) values (" .
						$radCopia[0] . ", '$rutaHTM', '" . $rsU->Fields('USUA_DOC') . "', '" . $rsU->Fields('USUA_LOGIN') . "', $CodTx, '".date('Y-m-d H:i:s', $details["udate"])."')";
                    $conn->Execute($sql);
                    if ($debug) {
                        debug($fyh, "Radicado: " . $radCopia[0] . " y copia:" . $radCopia[1] . "<br/>");
                    }
                    $sql = "SELECT SGD_RENV_MAIL FROM SGD_RENV_REGENVIO WHERE RADI_NUME_SAL=" . $radCopia[0] . "  AND SGD_DIR_TIPO=" . $radCopia[1];
                    $emailE = $conn->GetOne($sql);
					$sql="insert into hist_eventos (RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_CODI_DEST,DEPE_CODI_DEST,USUA_DOC,HIST_DOC_DEST,SGD_TTR_CODIGO,HIST_OBSE,HIST_FECH) values (".
                        $radCopia[0].",".$rsU->Fields('DEPE_CODI').", ".$rsU->Fields('USUA_CODI').", ".$rsU->Fields('USUA_CODI').", ".$rsU->Fields('DEPE_CODI').", '6758493021', '6758493021', $CodTx,".
                        "'Destinatario ".$emailE."', '".date('Y-m-d H:i:s', $details["udate"])."')";
                    $okh = $conn->Execute($sql);
                    //$objHist = new Historico($conn);
                    //$ok = $objHist->insertarHistorico(array($radCopia[0]), $rsU->Fields('DEPE_CODI'), $rsU->Fields('USUA_CODI'), $rsU->Fields('DEPE_CODI'), $rsU->Fields('USUA_CODI'), "Destinatario " . $emailE, 43);
                    
                    /*if (strpos($asuntoDecodificado, 'No entregado') !== false) {
                        if ($debug) {
                            debug($fyh, 'Correo No entregado: '.$radCopia[0]);
                        }
                        
                        // Parse pdf file and build necessary objects.
                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf= $parser->parseFile(BODEGAPATH . $rutaHTM);
                        $pages  = $pdf->getPages();
                        $numpage = 1;
                        $error = "";
                        foreach ($pages as $page) {
                            if ($numpage == 1) {
                                $textopage = $page->getText();
                                $mensaje = strstr($textopage, "Mensaje no entregado");
                                if ($mensaje) {
                                    $parentesis1 = stripos($mensaje, "(");
                                    $parentesis2 = stripos($mensaje, ")");
                                    $largo = $parentesis2 - $parentesis1;
                                    $error = substr($mensaje, $parentesis1, $largo);
                                }
                                break;
                            }
                        }
                        
                        $systemDate = $conn->OffsetDate(0,$conn->sysTimeStamp);
                        $isqlu = "update sgd_renv_regenvio set sgd_deve_fech = $systemDate,
                                    sgd_deve_codigo = 19,
                                    sgd_renv_observa = '$error'
                                where radi_nume_sal = " .$radCopia[0];
                        $rsreg = $conn->Execute($isqlu);
                        
                        $isqla = "update anexos set anex_estado = 3, sgd_deve_fech = $systemDate,
                                sgd_deve_codigo = 19 where sgd_dir_tipo = " .$radCopia[1] ."
                                and radi_nume_salida = " .$radCopia[0];
                        $rsa = $conn->Execute($isqla);
                        
                        $isql_hl= "insert into hist_eventos(DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI,
                                        HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO)
                                   values (".$rsU->Fields('DEPE_CODI').", $systemDate,". $rsU->Fields('USUA_CODI') .",
                                        ".$radCopia[0].", 'Devolución (19-DEVOLUCI�N CORREO ELECTRONICO). $error',
                                        NULL, '".$rsU->Fields('USUA_DOC')."', 28)";
                        $rshl = $conn->Execute($isql_hl);
                    }*/
                }
            }
        }
    }

    //Error de Microsoft Outlook en radicado de salida
    if ($details["fromName"] == "Microsoft Outlook") {
        $mailStruct = imap_fetchstructure($mbox, $uid, FT_UID);
        $asunto = iconv_mime_decode($details["subject"]);
        if (strpos($asunto, "Prueba de entrega") !== FALSE) {
            
        }
    }
    
    // Recepción de Certicamara de la notificación y posterior reenvío al destinatario.
    if ($details["fromAddr"] == "acknowledge@rpost.net") {
        
    }

    //Recepción de Rpost del uso del servicio de correo electrónico certificado
    if ($details["fromAddr"] == "support@rpost.com") {
        $overview = imap_fetch_overview($mbox, $uid, FT_UID);
        $structure = imap_fetchstructure($mbox, $uid, FT_UID);
        $message = getBody($uid, $mbox);

        $subject = $overview[0]->subject;
        $from = $overview[0]->from;
        $fromEmail = $header->from[0]->mailbox . "@" . $header->from[0]->host;
        $body = $message;

        $sql = "SELECT USUA_EMAIL FROM USUARIO WHERE USUA_REP_MAILCERT=1 AND USUA_EMAIL IS NOT NULL";
        $ADODB_COUNTRECS = TRUE;
        $conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rsx = $conn->Execute($sql);
        if ($rsx->RecordCount() > 0) {
            while ($arr = $rsx->FetchRow()) {
                $emailU[] = $arr['USUA_EMAIL'];
            }
            require_once ORFEOPATH . "class_control/correoElectronico.php";
            $objMail = new correoElectronico($ruta_raiz);
            $objMail->FromName = "Notificaciones";
            $enviarCorreo = $objMail->enviarCorreo($emailU, $cc, $cco, parsearString($subject), $message);
            $objMail->SmtpClose();
        }
        $ADODB_COUNTRECS = FALSE;
    }

    //movemos el correo electronico a la carpeta de gestionados.
    if (imap_mail_move($mbox, $uid, $carpetaGestionCertimail, CP_UID)) {
        if ($debug) {
            debug($fyh, "<br/><span style='color:green'>Moviendo correo con index $index, uid $uid a la carpeta $carpetaGestionCertimail</span>");
        }
    } else {
        if ($debug) {
            debug($fyh, "<br/><span style='color:red'>Error Moviendo correo con index $index, uid $uid a la carpeta $carpetaGestionCertimail</span>");
        }
    }
}

$okE = imap_expunge($mbox);
if ($debug) {
    debug($fyh, "<br/>imap_expunge dio $okE");
}

imap_close($mbox, CL_EXPUNGE);
if ($debug) {
    debug($fyh, "<br/>imap_close " . date('Y-m-d h:i:s') . "<br>");
}

function getAttachments($imap, $mailNum, $part, $partNum) {
    $attachments = array();

    if (isset($part->parts)) {
        foreach ($part->parts as $key => $subpart) {
            if ($partNum != "") {
                $newPartNum = $partNum . "." . ($key + 1);
            } else {
                $newPartNum = ($key + 1);
            }
            $result = getAttachments($imap, $mailNum, $subpart, $newPartNum);
            if (count($result) != 0) {
                array_push($attachments, $result);
            }
        }
    } else if (isset($part->disposition)) {
        if (strtoupper($part->disposition) == "ATTACHMENT") {
            $partStruct = imap_bodystruct($imap, imap_msgno($imap, $mailNum), $partNum);
            //$partStruct1 = imap_fetchstructure($imap, $mailNum, FT_UID);
            $attachmentDetails = array(
                "name" => $part->dparameters[0]->value,
                "partNum" => $partNum,
                "enc" => $partStruct->encoding
            );
            return $attachmentDetails;
        }
    }
    return $attachments;
}

function downloadAttachment($imap, $uid, $partNum, $encoding, $path) {
    $partStruct = imap_bodystruct($imap, imap_msgno($imap, $uid), $partNum);

    $filename = $partStruct->dparameters[0]->value;
    $message = imap_fetchbody($imap, $uid, $partNum, FT_UID);

    switch ($encoding) {
        case 0:
        case 1:
            $message = imap_8bit($message);
            break;
        case 2:
            $message = imap_binary($message);
            break;
        case 3:
            $message = imap_base64($message);
            break;
        case 4:
            $message = quoted_printable_decode($message);
            break;
    }
    return $message;
}

function getBody($uid, $imap) {
    $body = get_part($imap, $uid, "TEXT/HTML");
    // if HTML body is empty, try getting text body
    if ($body == "") {
        $body = get_part($imap, $uid, "TEXT/PLAIN");
    }
    return $body;
}

function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false) {
    if (!$structure) {
        $structure = imap_fetchstructure($imap, $uid, FT_UID);
    }
    if ($structure) {
        if ($mimetype == get_mime_type($structure)) {
            if (!$partNumber) {
                $partNumber = 1;
            }
            $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
            switch ($structure->encoding) {
                case 3: return imap_base64($text);
                case 4: return imap_qprint($text);
                default: return $text;
            }
        }

        // multipart
        if ($structure->type == 1) {
            foreach ($structure->parts as $index => $subStruct) {
                $prefix = "";
                if ($partNumber) {
                    $prefix = $partNumber . ".";
                }
                $data = get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                if ($data) {
                    return $data;
                }
            }
        }
    }
    return false;
}

function get_mime_type($structure) {
    $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

    if ($structure->subtype) {
        return $primaryMimetype[(int) $structure->type] . "/" . $structure->subtype;
    }
    return "TEXT/PLAIN";
}

function parsearString($cad) {
    $ret = "";
    $strCodec = imap_mime_header_decode($cad);
    foreach ($strCodec as $key => $arrCodec) {
        switch (strtolower($arrCodec->charset)) {
            case 'iso-8859-1': {
                    $ret .= ($arrCodec->text);
                }break;
            case 'default':
            case 'utf-8': {
                    $ret .= htmlentities(iconv('UTF-8', 'ISO-8859-1//IGNORE', $arrCodec->text));
                }break;
            default: {
                    $ret .= (iconv($arrCodec->charset, 'UTF-8', $arrCodec->text));
                }break;
        }
    }
    return $ret;
}

function debug($filename, $data) {
    file_put_contents($filename, $data, FILE_APPEND);
}

?>