<?php

set_time_limit(0);
ini_set('max_execution_time', '10800');
$ruta_raiz = ".";
require dirname(__FILE__) . "\\config.php";
//require ORFEOPATH . "include/db/ConnectionAlarmas.php";

/*require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
#############################################################################
$conn = NewADOConnection($dsnn);
$conn->SetFetchMode(ADODB_FETCH_ASSOC);*/

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

require "class_control/EmailMessage.php";    

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
$REDIRECT_URI="https://orfeojfzr.dnp.gov.co/alarma_Certimail.php";

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
    
    /*require ORFEOPATH . "include/tx/Historico.php";
    $sql = "SELECT USUA_CODI, DEPE_CODI, USUA_DOC, USUA_LOGIN FROM USUARIO WHERE USUA_LOGIN='$usrComodin'";
    $rsU = $conn->Execute($sql);*/
    
    //$dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert/readonly}INBOX/ASIGNAR HOY/SISBEN/REGISTRO SOCIAL NACIONAL";
    $dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert/readonly}INBOX";
    $mbox = imap2_open($dsn, "radicacionorfeo@dnp.gov.co", $get_access_token, OP_XOAUTH2) or die('Falló en la conexión: ' . imap2_last_error());
    //$n_msgs = imap2_num_msg($mbox);
    
    //$dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert}";
    ///$mbox = imap_open($dsn . $carpetaLecturaCertimail, $correo_certimail, $passwd_certimal);
    if ($debug) {
        debug($fyh, "<br/><b>imap_open<b/> " . date('Y-m-d h:i:s') . "<br>");
        debug($fyh, "<br/>N&uacute;mero Total de mensajes carpeta $carpetaLecturaCertimail: " . imap2_num_msg($mbox) . "<br/>");
        debug($fyh, "imap_listmailbox " . date('Y-m-d h:i:s') . "<br>");
        debug($fyh, "<h1>Leyendo cada correo</h1><br/>");
    }
    
    //$folders = imap2_listmailbox($mbox, "{-}", "INBOX/ASIGNAR HOY/SISBEN.*");  //  REGISTRO SOCIAL NACIONAL
    
    $cntEmails = imap2_num_msg($mbox);
    imap2_headers($mbox);
    
    for ($index = 1; $index <= $cntEmails; $index++) {
        $msgId = imap2_uid($mbox, $index);
        //$header = imap2_headerinfo($mbox, imap2_msgno($mbox, $msgId));
        
        //$fromInfo = $header->from[0];
        //$replyInfo = $header->reply_to[0];
        
        try
        {
            $fileMsg = BODEGAPATH . "tmp". DIRECTORY_SEPARATOR. $msgId . ".eml";
            
            try {
                
                $resp = imap2_savebody($mbox, $fileMsg, $msgId, '', FT_UID);
                
                $objEmailMessage = new EmailMessage($mbox, $fileMsg);
                $ok = $objEmailMessage->fetch();
            } catch (Exception $e) {
                echo 'Excepción capturada: ', $e->getMessage(), "\n";
                imap2_close($mbox);
            }
            
           /*if ($ok) {
                
                $eMailRemitente = stripos($objEmailMessage->mailRemitente, '<') ? substr($objEmailMessage->mailRemitente, stripos($objEmailMessage->mailRemitente, '<') + 1, stripos($objEmailMessage->mailRemitente, '>') - (stripos($objEmailMessage->mailRemitente, '<') + 1)) : $objEmailMessage->mailRemitente;
                $eMailNombreRemitente = parsearString(substr($objEmailMessage->mailRemitente, 0, stripos($objEmailMessage->mailRemitente, '<')));
                $eMailAsunto = parsearString($objEmailMessage->mailAsunto);
                $eMailDestino = $objEmailMessage->mailDestinos;
                $mailDate = $objEmailMessage->mailFechaEnv;
                
                $_SESSION['eMailRemitente'] = $eMailRemitente;
                $_SESSION['eMailNombreRemitente'] = $eMailNombreRemitente;
                
                $headHTML = "<TABLE width='100%' cellspacing='7' border='0' cellpadding='0'>
                            <tr><td width=60%>&nbsp;</td>
                                <td><div class='ClJ'>*$numeroRadicado*</div><br>Radicado No. $numeroRadicado<br>Fecha : " . date("Y/m/d") . "</td>
                          </tr>
                        </TABLE>";
                
                $headSENDER = "<TABLE width='100%'>
                    <tr><td width='10%'><b>De:</b></td>
    					<td width='85%'>" . htmlentities(parsearString($objEmailMessage->mailRemitente)) . "</td>
    					<td width='5%'></td>
    				</tr>
                    <tr><td width='10%'><b>Enviado el:</b></td>
    					<td width='85%'>$mailDate</td>
    					<td width='5%'></td>
    				</tr>
                    <tr><td width='10%'><b>Para:</b></td>
    					<td width='85%'>" . htmlentities(parsearString($eMailDestino)) . "</td>
    					<td width='5%'></td>
    				</tr>
                    <tr><td width='10%'><b>Asunto:</b></td>
    					<td width='85%'>$eMailAsunto</td>
    					<td width='5%'></td>
    				</tr>";
                $encabezado = "krd=$krd&PHPSESSID=" . session_id() . "&eMailMid=$msgId&ent=2&tipoMedio=eMail&mailFrom=" . $eMailRemitente . "&mailAsunto=" . str_replace("#", " ", $eMailAsunto)."&access_token=".$access_token;
                
                // ----------------------------------------------------------------------------//
                $cuerpoMail = "";
                $MailAdjuntos = "";
                if ($numeroRadicado) {
                    
                    include_once "../include/db/ConnectionHandler.php";
                    include_once "../class_control/AplIntegrada.php";
                    $db = new ConnectionHandler("..");
                    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                    
                    $codigoAnexo = $numeroRadicado . str_pad(1, 5, "0", STR_PAD_LEFT);
                    $iSql = "SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT = 'eml'";
                    $rs = $db->conn->Execute($iSql);
                    $anexTipo = $rs->fields["ANEX_TIPO_CODI"];
                    if (! $anexTipo)
                        $anexTipo = 0;
                        $iSql = "SELECT RADI_FECH_RADI FROM RADICADO WHERE RADI_NUME_RADI= $numeroRadicado";
                        $rs = $db->conn->Execute($iSql);
                        $fechaRad = $rs->fields["RADI_FECH_RADI"];
                        
                        $tmpNameEmail = $numeroRadicado . "_" . str_pad(1, 5, "0", STR_PAD_LEFT) . ".eml";
                        if (strlen($numeroRadicado) == 14) {
                            $directorio = date('Y') . "/" . substr($numeroRadicado, 4, 3) . "/docs/";
                        } elseif (strlen($numeroRadicado) == 15) {
                            $strRad = ltrim(substr($numeroRadicado, 4, 4), '0');
                            $directorio = date('Y') . "/" . $strRad . "/docs/";
                        }
                        $fileEmailMsg = BODEGAPATH . $directorio . $tmpNameEmail;
                        
                        // Guardamos el email en formato original en bodega y luego lo subimos como anexo
                        if (rename($fileMsg, $fileEmailMsg)) {
                            $record["ANEX_RADI_NUME"] = $numeroRadicado;
                            $record["ANEX_CODIGO"] = $codigoAnexo;
                            $record["ANEX_TAMANO"] = "'" . filesize($fileEmailMsg) . "'";
                            $record["ANEX_SOLO_LECT"] = "'S'";
                            $record["ANEX_CREADOR"] = "'" . $krd . "'";
                            $record["ANEX_DESC"] = "' Archivo: ." . $fname . "'";
                            $record["ANEX_NUMERO"] = 1;
                            $record["ANEX_NOMB_ARCHIVO"] = "'" . $tmpNameEmail . "'";
                            $record["ANEX_BORRADO"] = "'N'";
                            $record["ANEX_DEPE_CREADOR"] = $_SESSION['dependencia'];
                            $record["SGD_TPR_CODIGO"] = '0';
                            $record["ANEX_TIPO"] = $anexTipo;
                            $record["ANEX_FECH_ANEX"] = $db->conn->sysTimeStamp;
                            $okAnexo = $db->insert("anexos", $record, "true");
                            
                            // ************ INICIAMOS CREACION PDF ************
                            require ORFEOCFG.'/lib/tcpdf/tcpdf.php';
                            // create new PDF document
                            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, false);
                            // set image scale factor
                            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                            // set document information
                            $pdf->SetCreator(PDF_CREATOR);
                            $pdf->SetAuthor('SGD Orfeo');
                            $pdf->SetTitle("Radicado $numeroRadicado");
                            $pdf->SetSubject($eMailAsunto);
                            $pdf->SetKeywords("DNP, Radicación Correo Electrónico, $numeroRadicado");
                            // add a page
                            $pdf->AddPage();
                            
                            $style = array(
                                'position' => '',
                                'align' => 'L',
                                'stretch' => false,
                                'fitwidth' => true,
                                'cellfitalign' => '',
                                'border' => false,
                                'hpadding' => 'auto',
                                'vpadding' => 'auto',
                                'fgcolor' => array(
                                    0,
                                    0,
                                    0
                                ),
                                'bgcolor' => false, // array(255,255,255),
                                'text' => true,
                                'font' => 'helvetica',
                                'fontsize' => 12
                            );
                            $pdf->write1DBarcode($numeroRadicado, 'C39', 100, 10, null, 12, 0.2, $style, 'N');
                            $pdf->Ln();
                            // set font
                            $pdf->SetFont('helvetica', '', 10);
                            $pdf->SetXY(100, 20);
                            $pdf->MultiCell(0, 0, "Fecha Rad.: $fechaRad", 0, 'L', 0, 1, '', '', true);
                            $pdf->Ln();
                            $pdf->MultiCell(30, 5, 'De:', 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(155, 5, $objEmailMessage->mailRemitente, 0, 'L', 0, 1, '', '', true);
                            $pdf->MultiCell(30, 5, 'Enviado el:', 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(155, 5, $mailDate, 0, 'L', 0, 1, '', '', true);
                            $pdf->MultiCell(30, 5, 'Para:', 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(155, 5, parsearString($eMailDestino), 0, 'L', 0, 1, '', '', true);
                            $pdf->MultiCell(30, 5, 'Asunto:', 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(155, 5, $eMailAsunto, 0, 'L', 0, 1, '', '', true);
                        } else {
                            echo "No se pudo renombrar archivo a $fileMsg a $tmpNameEmail";
                        }
                }
                
                $cuerpoHtml = ($objEmailMessage->bodyHTML ? $objEmailMessage->bodyHTML : $objEmailMessage->bodyPlain);
                if (empty($cuerpoHtml))
                    $cuerpoHtml = @imap2_qprint(imap2_fetchtext($mbox, $msgId, FT_UID));
                    $archivoRadicado = $cuerpoHtml;
                    // buscamos las imagenes dentro del cuerpo del correo --NO COMO ANEXOS--
                    preg_match_all('/src="cid:(.*)"/Uims', $cuerpoHtml, $matches);
                    $anexosHTML = "";
                    
                    $iAnexo = 0;
                    $arrAnexosParaBorrar = array();
                    
                    if (is_array($objEmailMessage->attachments) || is_object($objEmailMessage->attachments)) {
                        foreach ($objEmailMessage->attachments as $attachment) {
                            $key = $attachment['id_content'];
                            if (empty($attachment['filename']) && $attachment['subtype'] == 'application/octet-stream') {
                                $attachment['filename'] = uniqid("correo_anexo_") . ".msg";
                            }
                            $fname = $attachment['filename'];
                            $extAnex = substr(strrchr($fname, '.'), 1);
                            $tmpNameFile = trim(uniqid("email_") . $fname);
                            
                            if (! $numeroRadicado) {
                                if (in_array($key, $matches[1], true)) {
                                    $cadBuscar = "src=\"cid:$key\"";
                                    $cadRempla = "src=\"data:" . strtolower($attachment['subtype']) . ";base64," . ($attachment['data']) . "\"";
                                    $cuerpoHtml = str_replace($cadBuscar, $cadRempla, $cuerpoHtml, $cntReemplzados);
                                } else {
                                    $url = ORFEOURL . "email2/downAttach.php?msgNo=$msgId&id=" . $attachment['id'] . "&" . session_name() . "=" . session_id();
                                    $flechaDown = "<td><a href='$url'><img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAAQCAYAAADAvYV+AAAAh0lEQVQokc3QLQoCURSG4WclIpgMNotpmsnmFsR1iMkluJUBF2IwCgbxBxlQk5YZuF6Y67X5wZvOy+E7h88ssQ5YSGSHV8D2T+U5VjWXSD4Fsxn0cIikmD26zfYBzi3iEf24zghVJN4wbDtwjGct3lGkvgFTPDD5Jjbp5IrJVJlcYZNJ+VOFN4DISoWxRlncAAAAAElFTkSuQmCC' /></a></td>";
                                    
                                    if ($iAnexo === 0) {
                                        $anexosHTML .= "<tr><td width='20%'><b>Datos adjuntos:</b></td><td width='60%'>" . parsearString($fname) . "</td>$flechaDown</tr>";
                                    } else {
                                        $anexosHTML .= "<tr><td width='20%'></td><td width='60%'>" . parsearString($fname) . "</td>$flechaDown</tr>";
                                    }
                                    $iAnexo ++;
                                }
                            } else {
                                if (in_array($key, $matches[1], true)) {
                                    $cadBuscar = "src=\"cid:$key\"";
                                    // $cadRempla = "src=\"data:image/".strtolower($attachment['subtype']) .";base64,". base64_encode($attachment['data'])."\"";
                                    
                                    // Se supone que el archivo descargó cuando se previsualizó el correo en en if linea 187
                                    if (! file_exists(BODEGAPATH . "tmp/" . $tmpNameFile)) {
                                        $imagen = BODEGAPATH . "tmp/" . $tmpNameFile;
                                        $fp = fopen($imagen, 'w');
                                        fwrite($fp, imap_base64($attachment['data']));
                                        fclose($fp);
                                    }
                                    // $cadRempla = "src=\"".BODEGAURL."tmp\\$tmpNameFile\"";
                                    $cadRempla = "src=\"" . "../bodega/tmp/$tmpNameFile\"";
                                    $cuerpoHtml = str_replace($cadBuscar, $cadRempla, $cuerpoHtml, $cntReemplzados);
                                    $arrAnexosParaBorrar[] = $imagen;
                                } else {
                                    
                                    $posYparaClip = $pdf->GetY();
                                    if ($iAnexo === 0) {
                                        $pdf->MultiCell(30, 5, 'Datos adjuntos:', 0, 'L', 0, 0, '', '', true);
                                        $anexosHTML .= "<tr><td width='10%'><b>Datos adjuntos:</b></td><td width='90%'>" . parsearString($fname) . "</td></tr><tr><td></td></tr>";
                                    } else {
                                        $pdf->MultiCell(30, 5, '', 0, 'L', 0, 0, '', '', true);
                                        $anexosHTML .= "<tr><td width='10%'></td><td width='90%'>" . parsearString($fname) . "</td></tr><tr><td></td></tr>";
                                    }
                                    $iAnexo ++;
                                    $pdf->MultiCell(155, 5, $fname, 0, 'L', 0, 1, '', '', true);
                                    $tmpNameFile = $fname;
                                    
                                    // Se supone que el archivo descargó cuando se previsualizó el correo en en if linea 187
                                    if (! file_exists(BODEGAPATH . "tmp/" . $tmpNameFile)) {
                                        $imagen = BODEGAPATH . "tmp/" . $tmpNameFile;
                                        $fp = fopen($imagen, 'w');
                                        if ( $fp ) {
                                            fwrite($fp, imap_base64($attachment['data']));
                                            fclose($fp);
                                        }
                                    }
                                    
                                    $pdf->Annotation(37, $posYparaClip, 1, 2, $fname, array(
                                        'Subtype' => 'FileAttachment',
                                        'Name' => 'Paperclip',
                                        'FS' => $imagen
                                    ));
                                    
                                    $arrAnexosParaBorrar[] = BODEGAPATH . "tmp/" . $tmpNameFile;
                                }
                            }
                        }
                    }
                    
                    $headSENDER .= $anexosHTML . "</TABLE>";
                    
                    $charsetBody = mb_detect_encoding($cuerpoHtml, 'UTF-8, windows-1251, ISO-8859-1, us-ascii');
                    
                    switch (strtolower($charsetBody)) {
                        case 'iso-8859-1':
                            $correo = iconv($charsetBody, 'UTF-8', $cuerpoHtml);
                            break;
                        case 'UTF-8':
                            $correo = $cuerpoHtml;
                            break;
                        default:
                            $correo = $cuerpoHtml; //htmlspecialchars($cuerpoHtml, ENT_QUOTES, 'utf-8'); //iconv($charsetBody, 'utf-8', $cuerpoHtml);
                            break;
                    }
                    $correo = trim($correo);
                    $xtmp = strpos($correo, '<html');
                    if ($xtmp !== false) {
                        $correo = substr($correo, strpos($correo, ">", 5) + 1);
                    }
                    $xtmp = strpos($correo, '<body');
                    if ($xtmp !== false) {
                        $tmpCorreo = strpos($correo, ">", $xtmp);
                        $correo = substr($correo, 0, $xtmp - 1) . substr($correo, $tmpCorreo + 1);
                    }
                    $correo = str_replace(array(
                        'iso-8859-1',
                        'windows-1251',
                        'us-ascii'
                    ), 'utf-8', $correo);
                    $correo = str_replace('<p class="MsoNormal"><o:p>&nbsp;</o:p></p>', "", $correo);
                    $correo = str_replace(array(
                        '</html>',
                        '</body>',
                        '<head>',
                        '</head>',
                        '</body>',
                        '<o:p></o:p>'
                    ), "", $correo);
                    $correo = str_replace('<span style="font-family:&quot;Tahoma&quot;,sans-serif;color:gray;mso-fareast-language:ES-CO"><o:p></o:p></span>', "", $correo);
                    $correo = str_replace('<p class="MsoNormal"><span style="font-family:&quot;Century Gothic&quot;,sans-serif"><o:p>&nbsp;</o:p></span></p>', "", $correo);
                    $correo = str_replace('<p class="MsoNormal" style="background:white"><b><span lang="ES" style="font-size:10.0pt;color:#212121;mso-fareast-language:ES-CO"><o:p>&nbsp;</o:p></span></b></p>', "", $correo);
                    $correo = str_replace('<span style="color:#212121;mso-fareast-language:ES-CO"><o:p></o:p></span>', "", $correo);
                    $correo = str_replace("<div><br></div>", "", $correo);
                    $correo = str_replace(array(
                        "<div>\\r\\n</div>",
                        "<div>\\r</div>",
                        "<div>\\n</div>",
                        "<div></div>",
                        "<div>&nbsp;</div>",
                        "<div><div>",
                        "</div></div>",
                        "<p class=\"MsoNormal\"><span style=\"font-size:12.0pt;color:black\"><o:p>&nbsp;</o:p></span></p>",
                        "<p class=\"MsoNormal\"><span style=\"font-size:12.0pt;color:black\">&nbsp;</span></p>"
                    ), "", $correo);
                    $correo = str_replace('<p style="margin-top:0;margin-bottom:0"><br></p>', "", $correo);
                    
                    $cuerpoMail = "<TABLE class='borde_tab' WIDTH='100%'><tr><td>" . $correo . "</td></tr></table>";
                    
                    if ($numeroRadicado) {
                        
                        $tmpNameEmail = $numeroRadicado;
                        if (strlen($numeroRadicado) == 14) {
                            $directorio = date('Y') . "/" . substr($numeroRadicado, 4, 3) . "/";
                        } elseif (strlen($numeroRadicado) == 15) {
                            $strRad = ltrim(substr($numeroRadicado, 4, 4), '0');
                            $directorio = date('Y') . "/" . $strRad . "/";
                        }
                        $fileRadicado = BODEGAPATH . $directorio . $tmpNameEmail;
                        
                        // output the HTML content
                        $charsetCuerpoHtml = mb_detect_encoding($correo);
                        $tidy = new Tidy();
                        $tidy_config = array(
                            'clean' => true,
                            'indent' => false,
                            'output-xhtml' => false,
                            'input-xhtml' => false,
                            'show-body-only' => false,
                            'output-encoding' => 'utf8',
                            'quote-nbsp' => true,
                            // 'doctype' => 'loose',
                            'drop-empty-paras' => true,
                            'drop-proprietary-attributes' => true,
                            'word-2000' => true,
                            'wrap-attributes' => false,
                            'wrap' => 0
                        );
                        $tidy->getStatus();
                        $tidy->parseString(($charsetCuerpoHtml === 'UTF-8') ? $correo : iconv($charsetCuerpoHtml, "UTF-8", $correo), $tidy_config, 'utf8');
                        $tidy->cleanRepair();
                        $cuerpoTidy = tidy_get_output($tidy);
                        
                        $pdf->writeHTMLCell('', '', 10, $pdf->GetY(), $cuerpoTidy, 1, 1, 0, 1, '');
                        
                        // Close and output PDF document =
                        $pdf->Output(ORFEOCFG . "bodega\\" . $directorio . $numeroRadicado . ".pdf", 'F');
                        
                        try {
                            $isqlRadicado = "update radicado set RADI_PATH = '" . $directorio . $tmpNameEmail . ".pdf" . "' where radi_nume_radi = $numeroRadicado";
                            // $db->conn->debug = true;
                            $rs = $db->conn->Execute($isqlRadicado);
                            
                            if (! $rs) // Si actualizo BD correctamente
                                echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
                                else {
                                    $radicadosSel[] = $numeroRadicado;
                                    $codTx = 42; // Codigo de la transaccion
                                    $noRadicadoImagen = $numeroRadicado;
                                    $observa = "Mail(" . $mailAsunto . ")";
                                    include "$ruta_raiz/include/tx/Historico.php";
                                    $hist = new Historico($db);
                                    $hist->insertarHistorico($radicadosSel, $dependencia, $codusuario, $dependencia, $codusuario, $observa, $codTx);
                                    include "enviarMail.php";
                                }
                                // Borramos el archivo recien anexado al pdf
                                foreach ($arrAnexosParaBorrar as $key => $ruta)
                                    @unlink($ruta);
                        } catch (Exception $e) {
                            echo $e;
                            exit();
                        }
                    }
            } else {
                die("<table align='center' border='1'><tr><td>Correo Electr&oacute;nico no disponible.</td></tr></table>");
            }*/
            
        }
        catch(Exception $e) {
            debug($fyh, "Error: " . $e->getMessage() . "<br/>");
            //echo $e->getMessage();
        }
        
                  
        
        //movemos el correo electronico a la carpeta de gestionados.
        /*if (imap2_mail_move($mbox, $uid, $carpetaGestionCertimail, CP_UID)) {
            if ($debug) {
                debug($fyh, "<br/><span style='color:green'>Moviendo correo con index $index, uid $uid a la carpeta $carpetaGestionCertimail</span>");
            }
            $cntEmails -= 1;
        } else {
            if ($debug) {
                debug($fyh, "<br/><span style='color:red'>Error Moviendo correo con index $index, uid $uid a la carpeta $carpetaGestionCertimail</span>");
            }
        }*/
        
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