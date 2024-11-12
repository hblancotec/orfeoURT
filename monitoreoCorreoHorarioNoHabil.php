<?php

/*
 * La idea es este script es solucionar la HU014-16 MONITOREO DE CUENTA RADICACIONORFEO@DNP.GOV.CO HORARIO NO LABORAL.
 *      ___________________________________________________
 * >>>> Editar en editor de texto con codificación UTF-8  <<<<
 *      ---------------------------------------------------
 * 
 * @link http://vtfs13app:8080/tfs/web/wi.aspx?pcguid=25901daa-7194-43ad-98ba-e6dd48b1215b&id=19591
 * @author Hollman Ladino Paredes
 */

$ruta_raiz = dirname(__FILE__);
require $ruta_raiz . '/config.php';
$msgRespuesta = "<html><head></head><body><table width='80%'>" .
        "<tr><td><img src='" . ORFEOURL . "img/escudo.jpg' alt='Logo DNP' width='140'></td><td><b>Departamento Nacional de Planeaci&oacute;n</b></td></tr><tr>" .
        "<td colspan='2'></td></tr>" .
		"<td colspan='2'>Estimado usuario:</td></tr>" .
        "<td colspan='2'>El Departamento Nacional de Planeaci&oacute;n le informa que su solicitud con asunto *** ser&aacute; radicada al siguiente d&iacute;a h&aacute;bil a partir de las 08:30 a.m.</td></tr>" .
        "<td colspan='2'></td></tr>" .
		"<td colspan='2'></td></tr>" .
        "</table></b></body></html>";
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $ADODB_COUNTRECS = true;
    $sql = "SELECT SGD_FESTIVO FROM SGD_DIAS_FESTIVOS WHERE SGD_FESTIVO='" . date('Y/m/d') . "'";
    $rs = $conn->Execute($sql);
    $ADODB_COUNTRECS = false;
//Validamos condicionales de fecha hora
//Si es festivo, sabado/domingo u hora actual no laboral.... entra.
    if (($rs->recordCount() > 0) || (date('N') >= 6) ||
            ( date('N') > 0 && date('N') < 6 && (
            (date('H:i:s') >= "00:00:01") && (date('H:i:s') <= "08:30:00") ||
            (date('H:i:s') >= "17:00:00") && (date('H:i:s') <= "23:59:59")
            )
            )
    ) {
//Leemos el buzón institucional y buscamos palabras clave en el asunto
//Las palabras deben ir sin tilde, virgulilla, diéresis, etc
        $claves = array("accion de tutela", "admite", "vinculacion", "avoca");
        $buscar = "àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ";
        $rempla = "aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY";

//Traemos los correos a quienes se les hará el reenvio del correo
        $sql = "SELECT USUA_EMAIL FROM USUARIO WHERE USUA_PERM_REENVIO_EMAILNOHABIL=1 AND USUA_ESTA=1";
        $rs = $conn->Execute($sql);
        while ($arr = $rs->FetchRow()) {
            $correos[] = $arr['USUA_EMAIL'];
        }
        if (count($correos) > 0) {
        
            $dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert}";
            $mbox = imap_open($dsn . "INBOX", $correo_mail_incoming, $passwd_mail_incoming);
            $uids = imap_search($mbox, "UNSEEN", SE_UID);

            if ($uids) {
                require_once $ruta_raiz . "/class_control/correoElectronico.php";
                require_once $ruta_raiz . "/class_control/EmailMessage.php";

                $emails = imap_fetch_overview($mbox, implode(',', $uids), FT_UID);
                foreach ($emails as $email) {
                    if (substr($email->subject, 0, 2) == "=?") {
                        $tmp = imap_mime_header_decode($email->subject);
                        $asunto = iconv($tmp[0]->charset, "ISO-8859-1", $tmp[0]->text);
                    } else {
                        $asunto = $email->subject;
                    }

                    $objMail = new correoElectronico(".");
                    $objMail->FromName = "Notificaciones Orfeo";

                    $objEmailMessage = new EmailMessage($mbox, $email->uid);
                    $objEmailMessage->fetch();
                    $body01 = (empty($objEmailMessage->bodyPlain) ? $objEmailMessage->bodyHTML : $objEmailMessage->bodyPlain);
                    $listCharset = mb_list_encodings();
                    $bodyCharset = mb_detect_encoding($body01, $listCharset);
                    $cuerpo = iconv($bodyCharset, "ISO-8859-1", $body01);

                    foreach ($claves as $clave) {
                        //pasamos a SIN TILDE/VIRGULILLA/DIERESIS el asunto/cuerpo y buscamos ocurrencia de palabras clave.
                        if (strripos(strtr($asunto . " " . $cuerpo, $buscar, $rempla), $clave)) {
                            //Si encontró la clave en el asunto
                            $fileAttached = array();
                            foreach ($objEmailMessage->attachments as $key => $attachment) {
                                //Emparapete cuando ienen anexos tipo msg/correo electronico
                                if (empty($attachment['filename']) && $attachment['subtype'] == 'RFC822') {
                                    $attachment['filename'] = uniqid("correo_anexo_") . ".msg";
                                }

                                $fname = iconv_mime_decode($attachment['filename'], 0, "ISO-8859-1");
                                if (!empty($attachment['filename'])) {
                                    $fileD = $attachment['data'];
                                    $imagen = BODEGAPATH . "tmp/" . $fname;
                                    $fp = fopen($imagen, 'w');
                                    fwrite($fp, $fileD);
                                    fclose($fp);
                                    $objMail->AddAttachment($imagen, basename($imagen));
                                    $fileAttached[] = $imagen;
                                }
                            }

                            ini_set('SMTP', $server_mail);
                            ini_set('smtp_port', $port_mail);
                            $headers = "From: radicacionorfeo@dnp.gov.co" . "\r\n";
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                            if (imap_mail($email->from, "Respuesta programada a correos en horario no hábil.", str_replace("***", "<i><b>&quot;$asunto&quot;</b></i>", $msgRespuesta), $headers)) {
                                echo "Correo con asunto $asunto enviado a " . $email->from . "</br>";
                            }

                            $cuerpo = "<b>ASUNTO</b>: " . $asunto . "<br/><br/><b>CUERPO</b>:" . $cuerpo;
                            $result = $objMail->enviarCorreo($correos, null, null, "Reenvio Programado de Correos Especiales.", $cuerpo);
                            if ($result) {
                                echo "Correo con asunto $asunto reenviado a " . implode(', ', $correos) . "   Fecha y Hora: " . date('Y-m-d H:i:s') . "</br>";
                                //una vez enviado el correo a los respectivos funcionarios, marcamos ese correo en el inbox para no volver a tratarlo.
                                imap_setflag_full($mbox, $email->uid, "\Seen", SE_UID);
                                //Borramos cada posible anexo creado
                                foreach ($fileAttached as $value) {
                                    @unlink($value);
                                }
                            }
                            $objEmailMessage = null;
                            unset($objEmailMessage);
                            $objMail->ClearAllRecipients();
                            unset($objMail);
                            //Acá como se supone entra porque encuentra una palabra clave, le hacemos break para que no valide el resto dentro del foreach
                            break;
                        } else {
                            echo "<br>No encontro palabra clave $clave en correo ".$email->uid;
                        }
                    }
                }
            } else {
                die("<b>No hay correos marcados como UNSEEN.</b>");
            }
        } else {
            die("<b>No hay correos configurados.</b>");
        }
        imap_close($mbox, CL_EXPUNGE);
    }
} else {
    die("<b>No hay conexion a BD</b>");
}
?>