<?php

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
/*include 'parser2/Smalot/PdfParser/Parser.php';
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
include 'parser2/tecnickcom/tcpdf/tcpdf_parser.php';*/

include 'email2/javanile/php-imap2/src/Acl.php';
include 'email2/javanile/php-imap2/src/BodyStructure.php';
include 'email2/javanile/php-imap2/src/Connection.php';
include 'email2/javanile/php-imap2/src/Errors.php';
include 'email2/javanile/php-imap2/src/Functions.php';
include 'email2/javanile/php-imap2/src/HeaderInfo.php';
include 'email2/javanile/php-imap2/src/ImapHelpers.php';
include 'email2/javanile/php-imap2/src/Mail.php';
include 'email2/javanile/php-imap2/src/Mailbox.php';
include 'email2/javanile/php-imap2/src/Message.php';
include 'email2/javanile/php-imap2/src/Thread.php';
include 'email2/javanile/php-imap2/src/Timeout.php';
include 'email2/javanile/php-imap2/src/Roundcube/Charset.php';
include 'email2/javanile/php-imap2/src/Roundcube/ImapClient.php';
include 'email2/javanile/php-imap2/src/Roundcube/MessageHeader.php';
include 'email2/javanile/php-imap2/src/Roundcube/MessageHeaderSorter.php';
include 'email2/javanile/php-imap2/src/Roundcube/Mime.php';
include 'email2/javanile/php-imap2/src/Roundcube/ResultIndex.php';
include 'email2/javanile/php-imap2/src/Roundcube/Utils.php';
include 'email2/javanile/php-imap2/bootstrap.php';

$CLIENT_ID = "6701f011-7fdb-40c6-9b68-5969512c05e1";
$CLIENT_SECRET = "igk8Q~Aqq0vTFA_8pme5D3FTEYEfinBFGcwRSclk";
$TENANT = "04260e20-234c-4c9f-a9dd-79286b1b70ac";
//$SCOPE = "openid%20profile%20offline_access%20https%3A%2F%2Foutlook.office365.com%2FSMTP.Send%20https%3A%2F%2Foutlook.office365.com%2FPOP.AccessAsUser.All%20https%3A%2F%2Foutlook.office365.com%2FIMAP.AccessAsUser.All";
$SCOPE = "https://outlook.office365.com/IMAP.AccessAsUser.All";
$REDIRECT_URI="https://orfeo.dnp.gov.co/alarma_Certimail.php";

$url2 = "https://login.microsoftonline.com/".$TENANT."/oauth2/v2.0/authorize?response_type=code&scope=".$SCOPE."&redirect_uri=".$REDIRECT_URI."&client_id=".$CLIENT_ID."&client_secret=".$CLIENT_SECRET."&state=IAFjZEc-Gbx5pPSOj2jnjc-MSiNTyiydFB-rO_BxcuA&prompt=login&response_mode=query";

if (!isset($_GET['code'])) {
    header('Location: ' . $url2);
    exit;   
} elseif (empty($_GET['state'])) {
    exit('Invalid state');
} else {

    $CODE = $_GET['code'];
    $SESSION = $_GET['session_state'];
    $STATE = $_GET['state'];
    
    $url = "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";
    
    //$data = "client_id=".$CLIENT_ID."&redirect_uri=".$REDIRECT_URI."&client_secret=".$CLIENT_SECRET."&code=".$CODE."&grant_type=authorization_code&scope=$SCOPE";
    $data = "client_id=".$CLIENT_ID."&redirect_uri=".$REDIRECT_URI."&client_secret=".$CLIENT_SECRET."&code=".$CODE."&session_state=".$SESSION."&grant_type=authorization_code&scope=$SCOPE";
    try
    {
        //echo $data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded' ));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
    }
    catch (Exception $exception)
    {
        var_dump($exception);
    }
    
    $out2 = json_decode($output, true);
    $get_access_token = $out2['access_token'];
    $get_refresh_token = $out2['refresh_token'];


    //$debug = (isset($_GET['debug']) ? $_GET['debug'] : FALSE);
    $debug = false;
    if ($debug) {
        $fyh = BODEGAPATH . "debug_" . date('Ymd_His') . ".html";
        debug($fyh, "inicio script" . $fyh . "<br>");
    }
    
    require ORFEOPATH . "include/tx/Historico.php";
    $sql = "SELECT USUA_CODI, DEPE_CODI, USUA_DOC, USUA_LOGIN FROM USUARIO WHERE USUA_LOGIN='$usrComodin'";
    $rsU = $conn->Execute($sql);
    
    $dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert/readonly}INBOX";
    $mbox = imap2_open($dsn, "notificaciones_sgdorfeo@dnp.gov.co", $get_access_token, OP_XOAUTH2) or die('Fall� en la conexi�n: ' . imap2_last_error());
    //$n_msgs = imap2_num_msg($mbox);
    
    //$dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert}";
    ///$mbox = imap_open($dsn . $carpetaLecturaCertimail, $correo_certimail, $passwd_certimal);
    if ($debug) {
        debug($fyh, "<br/><b>imap_open<b/> " . date('Y-m-d h:i:s') . "<br>");
        debug($fyh, "<br/>N&uacute;mero Total de mensajes carpeta $carpetaLecturaCertimail: " . imap2_num_msg($mbox) . "<br/>");
        debug($fyh, "imap_listmailbox " . date('Y-m-d h:i:s') . "<br>");
        debug($fyh, "<h1>Leyendo cada correo</h1><br/>");
    }
    
    $cntEmails = imap2_num_msg($mbox);
    for ($index = 1; $index <= $cntEmails; $index++) {
        $uid = imap2_uid($mbox, $index);
        $header = imap2_headerinfo($mbox, imap2_msgno($mbox, $uid));
    
        $fromInfo = $header->from[0];
        $replyInfo = $header->reply_to[0];
    
        try 
        {
        
            $details = array(
                "fromAddr" => (isset($fromInfo->mailbox) && isset($fromInfo->host)) ? $fromInfo->mailbox . "@" . $fromInfo->host : "",
                "fromName" => (isset($fromInfo->personal)) ? $fromInfo->personal : "",
                "replyAddr" => (isset($replyInfo->mailbox) && isset($replyInfo->host)) ? $replyInfo->mailbox . "@" . $replyInfo->host : "",
                "replyName" => (isset($replyTo->personal)) ? $replyto->personal : "",
                "subject" => (isset($header->subject)) ? $header->subject : "",
                "udate" => (isset($header->udate)) ? $header->udate : date_timestamp_get(date('Y-m-d H:i:s'.substr((string)microtime(), 1, 8))) );
        
            //Recepción del acuse de recibo del correo electrónico certificado certimail. Puede venir la información de la apertura.
            if ($details["fromAddr"] == "receipt@rpost.net") {
                $mailStruct = imap2_fetchstructure($mbox, imap2_msgno($mbox, $uid), FT_UID);
                $attachments = getAttachments($mbox, imap2_msgno($mbox, $uid), $mailStruct, "");
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
                //$mailStruct = imap2_fetchstructure($mbox, $uid, FT_UID);
                $asuntoDecodificado = iconv_mime_decode($details["subject"]);
        		
                if ( (strpos($asuntoDecodificado, "Prueba de entrega") || strpos($asuntoDecodificado, "OPENED")) == 0) {
        			$CodTx = ((strpos($asuntoDecodificado, "Prueba de entrega")=== false) ? 49 : 43);	
        			
        			$overview = imap2_fetch_overview($mbox, imap2_msgno($mbox, $uid), 0);
        			$message = imap2_fetchbody($mbox, imap2_msgno($mbox, $uid), 2);
        			$mailStruct = imap2_fetchstructure($mbox, imap2_msgno($mbox, $uid));
        			
        			$attachments = array();
        			
                    //$attachments = getAttachments($mbox, $uid, $mailStruct, "");
                    $rutaHTM = NULL;
                    //foreach ($attachments as $attachment) {
                        if ($debug) {
                            debug($fyh, 'Correo con uid = ' . $uid . ', part=' . $attachment["partNum"] . ', enc=' . $attachment["enc"] . ' y nombre de anexo ' . parsearString($attachment["name"]) . "<br/>");
                        }
                                                
                        if(isset($mailStruct->parts) && count($mailStruct->parts))
                        {
                            for($i = 0; $i < count($mailStruct->parts); $i++)
                            {
                                $attachments[$i] = array(
                                    'is_attachment' => false,
                                    'filename' => '',
                                    'name' => '',
                                    'attachment' => ''
                                );
                                
                                if($mailStruct->parts[$i]->ifdparameters)
                                {
                                    foreach($mailStruct->parts[$i]->dparameters as $object)
                                    {
                                        if(strtolower($object->attribute) == 'filename')
                                        {
                                            $attachments[$i]['is_attachment'] = true;
                                            $attachments[$i]['filename'] = $object->value;
                                        }
                                    }
                                }
                                
                                if($mailStruct->parts[$i]->ifparameters)
                                {
                                    foreach($mailStruct->parts[$i]->parameters as $object)
                                    {
                                        if(strtolower($object->attribute) == 'name')
                                        {
                                            $attachments[$i]['is_attachment'] = true;
                                            $attachments[$i]['name'] = $object->value;
                                        }
                                    }
                                }
                                
                                if($attachments[$i]['is_attachment'])
                                {
                                    $attachments[$i]['attachment'] = imap2_fetchbody($mbox, imap2_msgno($mbox, $uid), $i+1);
                                    
                                    /* 3 = BASE64 encoding */
                                    if($mailStruct->parts[$i]->encoding == 3)
                                    {
                                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                    }
                                    /* 4 = QUOTED-PRINTABLE encoding */
                                    elseif($mailStruct->parts[$i]->encoding == 4)
                                    {
                                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                    }
                                }
                            }
                        }
                        
                        foreach($attachments as $attachment)
                        {
                            if($attachment['is_attachment'] == 1)
                            {
                                $filename = $attachment['name'];
                                if(empty($filename)) $filename = $attachment['filename'];
                                                              
                                $radCopia = substr($asuntoDecodificado, strpos($asuntoDecodificado, "(") + 1, strpos($asuntoDecodificado, ")")-1-strpos($asuntoDecodificado, "("));
                                $radCopia = explode("_", $radCopia);
                                if (is_array($radCopia) && count($radCopia) == 2) {
                                    
                                    $rutaHTM = date('Y', $details["udate"] )."/acusecorreoelectronico/" . uniqid($radCopia[0] . "_" . $radCopia[1] . "_") . ".pdf";
                                    $fp = fopen(BODEGAPATH . $rutaHTM, 'w');
                                    fwrite($fp, $attachment['attachment']);
                                    fclose($fp);
                                    
                                    $sql = "insert into SGD_HIST_CERTIMAIL (RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, ID_TTR_HCTM, FECHA) values (" .
                                        $radCopia[0] . ", '$rutaHTM', '" . $rsU->Fields('USUA_DOC') . "', '" . $rsU->Fields('USUA_LOGIN') . "', $CodTx, '".date('Y-m-d H:i:s', $details["udate"])."')";
                                    $okh = $conn->Execute($sql);
                                    if ($debug) {
                                        debug($fyh, "Radicado: " . $radCopia[0] . " y copia:" . $radCopia[1] . "<br/>");
                                    }
                                    $sql = "SELECT SGD_RENV_MAIL FROM SGD_RENV_REGENVIO WHERE RADI_NUME_SAL=" . $radCopia[0] . "  AND SGD_DIR_TIPO=" . $radCopia[1];
                                    $emailE = $conn->GetOne($sql);
                                    $sql="insert into hist_eventos (RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_CODI_DEST,DEPE_CODI_DEST,USUA_DOC,HIST_DOC_DEST,SGD_TTR_CODIGO,HIST_OBSE,HIST_FECH) values (".
                                        $radCopia[0].",".$rsU->Fields('DEPE_CODI').", ".$rsU->Fields('USUA_CODI').", ".$rsU->Fields('USUA_CODI').", ".$rsU->Fields('DEPE_CODI').", '6758493021', '6758493021', $CodTx,".
                                        "'Destinatario ".$emailE."', '".date('Y-m-d H:i:s', $details["udate"])."')";
                                    $okh = $conn->Execute($sql);
                                }
                            }
                        }
                        
                        //$fileD = downloadAttachment($mbox, $uid, $attachment["partNum"], $attachment["enc"], $path);
                        
                        /*$radCopia = substr($asuntoDecodificado, strpos($asuntoDecodificado, "(") + 1, strpos($asuntoDecodificado, ")")-1-strpos($asuntoDecodificado, "("));
                        $radCopia = explode("_", $radCopia);
                        if (is_array($radCopia) && count($radCopia) == 2) {
                            
                            $rutaHTM = date('Y', $details["udate"] )."/acusecorreoelectronico/" . uniqid($radCopia[0] . "_" . $radCopia[1] . "_") . ".pdf";
                            $fp = fopen(BODEGAPATH . $rutaHTM, 'w');
                            fwrite($fp, $fileD);
                            fclose($fp);
                            
                            $sql = "insert into SGD_HIST_CERTIMAIL (RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, ID_TTR_HCTM, FECHA) values (" .
        						$radCopia[0] . ", '$rutaHTM', '" . $rsU->Fields('USUA_DOC') . "', '" . $rsU->Fields('USUA_LOGIN') . "', $CodTx, '".date('Y-m-d H:i:s', $details["udate"])."')";
        				    $okh = $conn->Execute($sql);
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
                                                ".$radCopia[0].", 'Devolucion (19-DEVOLUCI�N CORREO ELECTR�NICO). $error',
                                                NULL, '".$rsU->Fields('USUA_DOC')."', 28)";
                                $rshl = $conn->Execute($isql_hl);
                                
                                //ENVIAR NOTIFICACION
                                $sql1 ="SELECT USUA_EMAIL FROM USUARIO WHERE DEPE_CODI = ".$result->fields['DEPE']."
        						          AND USUA_CODI = 1";
                                $rs1 = $db->conn->execute($sql1);
                                require_once (dirname(__FILE__) . "\\class_control\\correoElectronico.php");
                                
                                $result = "-1";
                                $asunto = "OrfeoDNP Alerta de radicado devuelto ";
                                $cuerpo = "Sr. (a) Usuario (a): ".$nombre."<br><br> Los siguientes radicados
                            				se encuentran en su poder en el SGD Orfeo y a&uacute;n no est&aacute;n vinculados
                            				a ning&uacute;n expediente, por favor ingrese a Orfeo e incluya cada
                            				uno de estos radicados al respectivo expediente.<br><br><b>".$noRad."
                            				</b> <br><br> Cualquier inquietud por favor comunicarse con la mesa de
                            				ayuda de Orfeo Ext: 4043-4054-4070-4071-4074-4077.";
                                
                                $cco[] = "ajmartinez@dnp.gov.co";
                                $usuaCC[] = '';
                                $objMail = new correoElectronico(".");
                                $objMail->FromName = "Notificaciones Orfeo";
                                $result = $objMail->enviarCorreo(array($mail), $usuaCC, $cco, $asunto, $cuerpo);
                                $mail = null;
                                $objMail = null;
                                $cco = null;
                            }
                        }*/
                    //}
                }
            }
        }
        catch(Exception $e) {
            debug($fyh, "Error: " . $e->getMessage() . "<br/>");
            //echo $e->getMessage();
        }
    
        //Error de Microsoft Outlook en radicado de salida
        if ($details["fromName"] == "Microsoft Outlook") {
            $mailStruct = imap2_fetchstructure($mbox, $index, FT_UID);
            $asunto = iconv_mime_decode($details["subject"]);
            if (strpos($asunto, "Prueba de entrega") !== FALSE) {
                
            }
        }
        
        // Recepción de Certicamara de la notificación y posterior reenvío al destinatario.
        if ($details["fromAddr"] == "acknowledge@rpost.net") {
            
        }
    
        //Recepción de Rpost del uso del servicio de correo electrónico certificado
        if ($details["fromAddr"] == "support@rpost.com") {
            $overview = imap2_fetch_overview($mbox, imap2_msgno($mbox, $uid), FT_UID);
            $structure = imap2_fetchstructure($mbox, imap2_msgno($mbox, $uid), FT_UID);
            $message = getBody(imap2_msgno($mbox, $uid), $mbox);
    
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
        if (imap2_mail_move($mbox, $uid, $carpetaGestionCertimail, CP_UID)) {
            if ($debug) {
                debug($fyh, "<br/><span style='color:green'>Moviendo correo con index $index, uid $uid a la carpeta $carpetaGestionCertimail</span>");
            }
            $cntEmails -= 1;
        } else {
            if ($debug) {
                debug($fyh, "<br/><span style='color:red'>Error Moviendo correo con index $index, uid $uid a la carpeta $carpetaGestionCertimail</span>");
            }
        }
        
        if ($cntEmails > 1) {
            $index -= 1;
        }
    }
    
    $okE = imap2_expunge($mbox);
    if ($debug) {
        debug($fyh, "<br/>imap_expunge dio $okE");
    }
    
    imap2_close($mbox, CL_EXPUNGE);
    if ($debug) {
        debug($fyh, "<br/>imap_close " . date('Y-m-d h:i:s') . "<br>");
    }
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
            $partStruct = imap2_bodystruct($imap, imap2_msgno($imap, $mailNum), $partNum);
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
    $partStruct = imap2_bodystruct($imap, imap2_msgno($imap, $uid), $partNum);

    $filename = $partStruct->dparameters[0]->value;
    $message = imap2_fetchbody($imap, $uid, $partNum, FT_UID);

    switch ($encoding) {
        case 0:
        case 1:
            $message = imap2_8bit($message);
            break;
        case 2:
            $message = imap2_binary($message);
            break;
        case 3:
            $message = imap2_base64($message);
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
        $structure = imap2_fetchstructure($imap, $uid, FT_UID);
    }
    if ($structure) {
        if ($mimetype == get_mime_type($structure)) {
            if (!$partNumber) {
                $partNumber = 1;
            }
            $text = imap2_fetchbody($imap, $uid, $partNumber, FT_UID);
            switch ($structure->encoding) {
                case 3: return imap2_base64($text);
                case 4: return imap2_qprint($text);
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
    $strCodec = imap2_mime_header_decode($cad);
    foreach ($strCodec as $key => $arrCodec) {
        switch (strtolower($arrCodec->charset)) {
            case 'iso-8859-1': {
                $ret .= iconv($arrCodec->charset, 'UTF-8', $arrCodec->text);
                }break;
            case 'default':
            case 'utf-8': {
                    $ret .= $arrCodec->text;
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