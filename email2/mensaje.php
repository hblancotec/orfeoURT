<?php
session_start();
if (count($_SESSION) == 0) {
    //die(include "../../sinacceso.php");
    //exit;
}

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);
 
set_time_limit(0);
ini_set('max_execution_time', '10800');
$ruta_raiz = "..";

require "$ruta_raiz/config.php";
require "../class_control/EmailMessage.php";

include 'javanile/php-imap2/src/Acl.php';
include 'javanile/php-imap2/src/BodyStructure.php';
include 'javanile/php-imap2/src/Connection.php';
include 'javanile/php-imap2/src/Errors.php';
include 'javanile/php-imap2/src/Functions.php';
include 'javanile/php-imap2/src/HeaderInfo.php';
include 'javanile/php-imap2/src/ImapHelpers.php';
include 'javanile/php-imap2/src/Mail.php';
include 'javanile/php-imap2/src/Mailbox.php';
include 'javanile/php-imap2/src/Message.php';
include 'javanile/php-imap2/src/Thread.php';
include 'javanile/php-imap2/src/Timeout.php';
include 'javanile/php-imap2/src/Roundcube/Charset.php';
include 'javanile/php-imap2/src/Roundcube/ImapClient.php';
include 'javanile/php-imap2/src/Roundcube/MessageHeader.php';
include 'javanile/php-imap2/src/Roundcube/MessageHeaderSorter.php';
include 'javanile/php-imap2/src/Roundcube/Mime.php';
include 'javanile/php-imap2/src/Roundcube/ResultIndex.php';
include 'javanile/php-imap2/src/Roundcube/Utils.php';
include 'javanile/php-imap2/bootstrap.php';

function parsearString($cad)
{
    $ret = "";
    $strCodec = imap_mime_header_decode($cad);
    foreach ($strCodec as $key => $arrCodec) {
        switch (strtolower($arrCodec->charset)) {
            case 'iso-8859-1':
                {
                    $ret .= iconv($arrCodec->charset, 'utf-8', $arrCodec->text);
                }
                break;
            case 'default':
                {
                    $ret .= $arrCodec->text;
                }
                break;
            case 'utf-8':
                {
                    $ret .= $arrCodec->text;
                }
                break;
            default:
                {
                    $ret .= iconv($arrCodec->charset, 'utf-8', $arrCodec->text);
                }
                break;
        }
    }
    return $ret;
}

$ok = false;
$msgId = $_GET['msgNo'];
if ($msgId) {
    //$dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert/readonly}INBOX";
    //$mbox = imap2_open($dsn, $correo_mail_incoming, $passwd_mail_incoming);
    $dsn = "{" . "outlook.office365.com" . ":" . 993 . "/imap/ssl/novalidate-cert/readonly}INBOX";
    $mbox = imap2_open($dsn, "radicacionorfeo@dnp.gov.co", $access_token, OP_XOAUTH2) or die('Falló en la conexión: ' . imap2_last_error());
    if ($mbox === FALSE)
        die("No hay conexi&oacute;n al buz&oacute;n $correo_mail_incoming");
    
    //$fileMsg = BODEGAPATH . date('Y') . '/' . $_SESSION['dependencia'] . "/docs/" . $msgId . ".eml";
    $fileMsg = BODEGAPATH . "tmp". DIRECTORY_SEPARATOR. $msgId . ".eml";
    
    try {                  
        $objEmailMessage = new EmailMessage($mbox, $fileMsg);
        $ok = $objEmailMessage->fetch();
    } catch (Exception $e) {
        echo 'Excepción capturada: ', $e->getMessage(), "\n";
        imap2_close($mbox);
    }
    imap2_close($mbox);
    if ($ok) {
        
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
                
                // ************ INICIAMOS CREACION PDF ************//
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
        // Guardamos el cuerpo del correo para 'trabajar' con el internamente.
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
        $correo = str_replace("<div><br>
</div>", "", $correo);
        $correo = str_replace(array(
            "<div>\\r\\n</div>",
            "<div>\\r</div>",
            "<div>\\n</div>",
            "<div></div>",
            "<div>&nbsp;</div>",
        	"<div>
<div>",
        	"</div>
</div>",
        	"<p class=\"MsoNormal\"><span style=\"font-size:12.0pt;color:black\"><o:p>&nbsp;</o:p></span></p>",
        	"<p class=\"MsoNormal\"><span style=\"font-size:12.0pt;color:black\">&nbsp;</span></p>"
        ), "", $correo);
        $correo = str_replace('<p style="margin-top:0;margin-bottom:0"><br>
    	</p>', "", $correo);
        
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
    }
} else {
    die("<table align='center' border='1'><tr><td>No hay informaci&oacyte;n del correo recibido.</td></tr></table>");
}
?>
<HTML>
<HEAD>
<link rel="stylesheet" href="../estilos/orfeo.css">
<STYLE TYPE="text/css">
#flotante {
	position: absolute;
	top: 100;
	left: 550px;
	visibility: visible;
}

div.ClJ {
	font-family: Code3of9;
	font-size: 25px;
}
</STYLE>
<SCRIPT>
            function asociarMail()
            {
                if (parent.frames['radicar'].document.getElementById('numeroRadicado'))
                {
                    numeroRad = parent.frames['radicar'].document.getElementById('numeroRadicado').value;
                    if( (numeroRad!="") && (numeroRad>=1) ) {
                        document.getElementById('numeroRadicado').value = numeroRad;
                        document.getElementById('formAsociarMail').submit();
                        parent.frames['radicar'].location.href='blanco.html';
                    }
                } else {
                   	alert(" No se ha generado un Radicado ! ");
                }
            }
        </SCRIPT>
</HEAD>
<BODY>
	<FORM method='GET' name='formAsociarMail' id='formAsociarMail'
		action='mensaje.php'>
		<input type='hidden' name='numeroRadicado' id='numeroRadicado'>
		<input type='hidden' name='mailFrom' id='mailFrom' value='<?= $eMailRemitente ?>'> <input type='hidden' name='msgNo' value='<?= htmlentities($msgNo) ?>'>
	</FORM>
	<table width="100%" class="borde_tab">
		<tr class=titulos2>
			<td align=right><font size=1> 
				<a href='../radicacion/chequear.php?<?= $encabezado ?>' target='radicar'>Radicar Este Correo</a> &nbsp;-&nbsp; 
				<a href='#' onClick="asociarMail();">Asociar Mail a Radicado</a> &nbsp;-&nbsp;
				<a href='browse_mailbox.php?krd=<?= $krd ?>&PHPSESSID=<?= session_id() ?>' target='formulario'>Actualizar Inbox</a>
			</font></td>
		</tr>
	</table>
<?php
echo $headHTML;
echo $headSENDER;
echo $cuerpoMail;
?>
    </BODY>
</HTML>