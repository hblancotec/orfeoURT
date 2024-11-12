<?php

/*require('envioAlertas/class.phpmailer.php');
require('envioAlertas/class.smtp.php');

        $nombreOrfeo 	= "Sistema Orfeo";
        $emailOrigen 	= "orfeo@dnp.gov.co";
        $url 		= "https://embera.dnp.gov.co/orfeo_3.6.0/";
        $cuerpo		= "<p><b>Se&ntilde;or(a):</b> $nombreUsuario</p><p></p>";
        $cuerpo		.= "<p>El <b>Sistema Orfeo</b> quiere informale que tiene en este momento el expediente ($expediente) ";
        $cuerpo		.= "el cual va en la etapa <b><i>'$nombreEtapa'</i></b> y que finaliza en <b>($numDias)</b> d&iacute;as ";
        $cuerpo		.= "en la fecha <b>($fechaFin)</b></p>";
        $cuerpo		.= "<p><b><i>Por favor se le recomienda realizar la actividad pendiente y as&iacute; realizar<br>";
        $cuerpo		.= "el cambio a la siguiente etapa. Gracias!!</i></b></p>";
        
        $mail 		= new PHPmailer();
        $mail->SetLanguage("en", "phpmailer/language");
        $mail->SMTPSecure = "tls";
        $mail->SMTPDebug = 2;
        $mail->From 	= "jzabala";
        $mail->FromName = "Sistema Orfeo";
        $mail->Host 	= "smtp.office365.com:587";
        $mail->Mailer   = "smtp";
        $mail->Password = "42W6528+";
        $mail->Username = "notificaciones_sgdorfeo@dnp.gov.co";
        $mail->Subject 	= "Alerta Sistemas Orfeo (Proceso por finalizarse)";
        $mail->SMTPAuth =  "true";
        $mail->Body 	= $cuerpo;
        $mail->IsHTML(true);
        $mail->AddAddress("jzabala@dnp.gov.co","Fredy Zabala");
        //$mail->AddReplyTo($emailOrigen,$nombreOrfeo);
        
        try {
            $result = ($mail->Send() === TRUE) ? TRUE : $mail->ErrorInfo;
        } catch (Exception $e) {
            echo $e->getMessage(); 
        }
        
        $mail->ClearAddresses();
        $mail->ClearAttachments();*/
/*set_time_limit(0);
echo "\n"."Inicia Alarmas: ".date('Y/m/d_h:i:s')."\n";

include_once ("config.php");
require_once ("class_control/correoElectronico.php");

$objMail = new correoElectronico(".");

$usuaCC[] = '';
$usuaCCO[] = '';
$asunto = "OrfeoDNP Alerta de radicados sin expediente";
$cuerpo = "Sr. (a) Usuario (a): ".$nombre."<br><br> Los siguientes radicados
				se encuentran en su poder en el SGD Orfeo y a&uacute;n no est&aacute;n vinculados
				a ning&uacute;n expediente, por favor ingrese a Orfeo e incluya cada
				uno de estos radicados al respectivo expediente.<br><br><b>".$noRad."
				</b> <br><br> Cualquier inquietud por favor comunicarse con la mesa de
				ayuda de Orfeo Ext: 4043-4054-4070-4071-4074-4077.";
        
$objMail->FromName = "Notificaciones Orfeo";
$result = $objMail->enviarCorreo(array("jzabala@dnp.gov.co"), $usuaCC, $usuaCCO, $asunto, $cuerpo);
        
echo "<br>" .$result;*/

require "PHPMailer-6.4.1/src/PHPMailer.php";
require "PHPMailer-6.4.1/src/SMTP.php";
use PHPMailer\PHPMailer\PHPMailer;
echo "inicio";

include dirname(__FILE__) . "\config.php";
$mail = new PHPMailer();

$mail->IsHTML(true);
// $this->Priority = 1;
// $this->esCertificado = $esCertificado;
$mail->isSMTP();
$mail->SMTPSecure = $tls_mail_2;
$mail->SMTPAuth = $auth_mail_2;
$mail->Username = $correo_mail_2;
$mail->Password = $passwd_mail_2;
$mail->Host = $server_mail_2;
$mail->Port = $port_mail_2;
$mail->Mailer = $protocolo_mail_2;
$mail->Password = $passwd_mail_2;
$mail->FromName = $usuario_mail_2;
$mail->From = $correo_mail_2;
$mail->SMTPDebug = 2;
// $this->Timeout = 180;
$mail->setFrom($correo_mail_2, "Notificaciones DNP (Orfeo)");

$mail->AltBody = "Para visualizar este correo utilice un cliente grafico que permita leer correos HTML.";
$mail->SetLanguage("es", ORFEOCFG . "/lib/PHPMailer/language/");
$mail->Body = "Esto es una prueba de envío de correo";

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

// Set who the message is to be sent to
$mail->addAddress("jzabala@dnp.gov.co");
//$mail->addCC('ajmartinez@dnp.gov.co');
//$mail->addBCC('notificaservicioalciudadano@dnp.gov.co');
$mail->Subject = "PRUEBA CORREO";
echo "Listo";

// send the message, check for errors
$result = ($mail->Send() === TRUE) ? TRUE : $mail->ErrorInfo;
if (! $result) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
    // Section 2: IMAP
    // Uncomment these to save your message in the 'Sent Mail' folder.
    // if (save_mail($mail)) {
    // echo "Message saved!";
    // }
}


#############################################################################
##	ARCHIVOS REQUERIDOS PARA EJECUTAR ESTE SCRIPT
/*$ruta_raiz = "";
include_once ("config.php");
require_once ("class_control/correoElectronico.php");
$result = "-1";

$cuerpo = "PRUEBAS DE CORREO ELECTRONICO";

    if (strlen($cuerpo) > 10) {
       
        $asunto = "OrfeoDNP PRUEBAS ";
        
        $objMail = new correoElectronico(".");
        
        $cc = 'jzabala@dnp.gov.co';
        $objMail->FromName = "Notificaciones Orfeo";
        $result = $objMail->enviarCorreo(array("jzabala@dnp.gov.co"), array($cc), "", $asunto, $cuerpo);
        echo $result;
        unset($cc);
        return $result;
    }*/

?>