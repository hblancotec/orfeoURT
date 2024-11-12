<?php 
session_start();
$ctx = $_POST['tx'];
$radicadosSel = explode('_',$_POST['rads']);
$observa = $_POST['obse'];
$nombreEmisor = $_POST['usr'];
$nombdepend = $_POST['dep'];
$destinatariosSel = explode(';',$_POST['mails']);
error_reporting(E_ERROR | E_PARSE);
require "../class_control/correoElectronico.php";
$objMail = new correoElectronico("..", FALSE);

include_once "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler("..");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//Mensaje que se envia por defecto						
$mensaje1 = "No est&aacute; configurada una Cuenta de correo del remitente. --";
$mensaje2 = "No est&aacute; configurada una Cuenta de correo de destino. --";
$mensaje3 = "El destinatario tiene un radicado con el mismo numero. --";
$mensaje4 = "El destinatario no permite notificación por correo electronico. --";
$tmp_rad = array();
foreach ($radicadosSel as $noRadicado) {
    if (strstr($noRadicado, '-')) {
        $tmp = explode('-', $noRadicado);
        $tmp = $tmp[1];
    } else {
        $tmp = $noRadicado;
    }
    $tmp_rad[] = $tmp;
}
        
switch($ctx) {
	case 9: {
		//Reasignar
		Foreach ($tmp_rad as $key => $radicado) {
                    /*
                    $query1 = "SELECT U.USUA_EMAIL AS EMAIL FROM USUARIO U 
                            INNER JOIN RADICADO R ON U.USUA_LOGIN=R.RADI_USU_ANTE
                            WHERE R.RADI_NUME_RADI = $radicado";
                    $email_rsf = $db->conn->Execute($query1);
                    $correoEmisor = trim($email_rsf->fields["EMAIL"]);
                    */
                    Foreach ($destinatariosSel as $key => $email) {

                        $correoUsuaDes = trim($email);
                        if (empty($correoUsuaDes) || !(filter_var($correoUsuaDes, FILTER_VALIDATE_EMAIL)) ) {
                            $email_res .= $email." ".$mensaje4." <b>O</b> ".$mensaje2;
                        } else {
                            $email_men = "Se envio correo electronico: $correoUsuaDes --";
                            $listRad = is_array($tmp_rad) ? implode(',', $tmp_rad) : $tmp_rad;
                            $asunto = "ORFEO: Asignacion de radicado(s) " . $listRad;
                            $objMail->FromName = $nombreEmisor;
                            $cuerpo = "<center/>El usuario <strong>" . iconv('UTF-8','ISO-8859-1//IGNORE',$nombreEmisor) . "</strong> de la dependencia <strong>" .
                                        iconv('UTF-8','ISO-8859-1//IGNORE',$nombdepend) . "</strong> le reasign&oacute; el(los) documento(s) (" . $listRad . ") 
                                        a trav&eacute;s del sistema Orfeo, con el siguiente comentario:	<br/> <br/> <strong>'" .
                                        iconv('UTF-8','ISO-8859-1//IGNORE',$observa) . "'</strong></center>";
                            //$result = $objMail->enviarCorreo(array($correoUsuaDes), array($correoEmisor), null, $asunto, $cuerpo);
                            $result = $objMail->enviarCorreo(array($correoUsuaDes), null, null, $asunto, $cuerpo);
                            
                            if (!$result) {
                                    $email_men = "<br/>Envio de correo: " . $correoUsuaDes . " : <br/>"
                                    . $objMail->ErrorInfo .
                                    "<br/>Enviado Por:" . $nombreEmisor .
                                    "<br/>Con el correo:" . $correoEmisor;
                            }
                            $objMail->ClearAddresses();
                            $objMail->ClearAttachments();
                            $email_res .= $email_men;
                        }
                    }
		}
	} break;
	case 12: {
		//devolver
		Foreach ($radicadosSel as $key => $radicado) {
			$enviarCorreo = TRUE;
			$nombTx = "Devolver Radicado";

			//Envio de correo si la transacion fue exitosa
			$query1 = "SELECT U.USUA_EMAIL AS EMAIL FROM USUARIO U 
						INNER JOIN RADICADO R ON U.USUA_CODI=R.RADI_USUA_ACTU AND U.DEPE_CODI=R.RADI_DEPE_ACTU
						WHERE R.RADI_NUME_RADI = $radicado";
			$email_rsf = $db->conn->Execute($query1);
			$correoUsuaDes = trim($email_rsf->fields["EMAIL"]);
			if (empty($correoUsuaDes) || !(filter_var($correoUsuaDes, FILTER_VALIDATE_EMAIL)) ) {
				$email_res .= $mensaje2;
				$enviarCorreo = FALSE;
			}
			if ($enviarCorreo) {
				//$listRad = is_array($tmp_rad) ? implode('_', $tmp_rad) : $tmp_rad;
			    $asunto = "ORFEO: Devolucion del radicado " . $radicado;
				$objMail->FromName = $nombreEmisor;
				$cuerpo = "<center>El usuario <b>" . iconv('UTF-8','ISO-8859-1//IGNORE',$nombreEmisor) . 
                                            "</b> de la dependencia <b>" . iconv('UTF-8','ISO-8859-1//IGNORE',$nombdepend) . "</b> 
                                            le regres&oacute; el documento (" . $radicado . ") al sistema Orfeo, con el 
                                            siguiente comentario:<br /><br /><b>'" . iconv('UTF-8','ISO-8859-1//IGNORE',$observa) . "'</b></center>";
                                
				$result = $objMail->enviarCorreo(array($correoUsuaDes), null, null, $asunto, $cuerpo);
                                
				if ($result) {
					$email_men = "Se envi&oacute; correo electr&oacute;nico: $correoUsuaDes. Enviado Por: ".$nombreEmisor;
				} else {
					$email_men = "Problemas enviando correo electronico a $correoUsuaDes error: " . $objMail->ErrorInfo;
				}
				$objMail->ClearAddresses();
				$objMail->ClearAttachments();
				$email_res .= $email_men;
			}
		}
		
	} break;
}
echo $email_res;
?>