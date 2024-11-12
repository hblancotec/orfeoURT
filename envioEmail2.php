<?php

// ########################################################################################
// ## FUNCION QUE GENERA EL ENVIO DE ALERTAS A TRAVES DEL ENVIO DE CORREOS ELECTRONICOS

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require 'phpmailer/src/Exception.php';
// require 'phpmailer/src/PHPMailer.php';
// require 'phpmailer/src/SMTP.php';

/*
 * require "PHPMailer/PHPMailer.php";
 * require "PHPMailer/SMTP.php";
 * use PHPMailer\PHPMailer\PHPMailer;
 */
require dirname(__FILE__) . "/lib/PHPMailer-6.4.1/src/PHPMailer.php";
require dirname(__FILE__) . "/lib/PHPMailer-6.4.1/src/SMTP.php";
use PHPMailer\PHPMailer\PHPMailer;

class correo extends PHPMailer
{

    public function __construct()
    {
        parent::__construct();
        include "./config.php"; 
        //$this = new PHPMailer();

        $this->IsHTML(true);
        $this->isSMTP();
        $this->SMTPSecure = $tls_mail_2;
        $this->SMTPAuth = $auth_mail_2;
        $this->Username = $correo_mail_2;
        $this->Password = $passwd_mail_2;
        $this->Host = $server_mail_2;
        $this->Port = $port_mail_2;
        $this->Mailer = $protocolo_mail_2;
        $this->Password = $passwd_mail_2;
        $this->FromName = $usuario_mail_2;
        $this->From = $correo_mail_2;
        $this->SMTPDebug = 0;
        // $this->Timeout = 180;
        //$this->setFrom($correo_mail_2, "Notificaciones DNP (Orfeo)");

        $this->AltBody = "Para visualizar este correo utilice un cliente grafico que permita leer correos HTML.";
        $this->SetLanguage("es", ORFEOCFG . "/lib/PHPMailer/language/");

        $this->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }

    public function enviarCorreo($para, $cc, $cco, $cuerpo, $asunto)
    {
        $result = array();
        /*$this->addAddress($email);
        $this->addCC('notificaservicioalciudadano@dnp.gov.co');
        $this->addBCC('ajmartinez@dnp.gov.co');*/
        $this->Subject = $asunto;
        $this->Body = $cuerpo;

        if (is_array($para)) {
            foreach ($para as $key => $dest)
                $this->addAddress($dest);
        }

        if (is_array($cc)) {
            foreach ($cc as $key => $dest)
                $this->AddCC($dest);
        }

        if (is_array($cco)) {
            foreach ($cco as $key => $dest)
            $this->AddBCC($dest);
        }

        // $result = ($this->Send() === TRUE) ? TRUE : $this->ErrorInfo;
        // send the message, check for errors
        
        try {
            $result[] = $this->Send();
            if (!$result[0]) {
                $result[] = "Mailer Error: " . $this->ErrorInfo;
            } else {
                $result[] = "Message sent!";
            }
        } catch (Exception $e) {
            $result[] = false;
            $result[] = $e->getMessage();
            //$result = false;
        }
        
        $this->ClearAllRecipients();
        $this->ClearAttachments();
        
        return $result;
    }
}

?>
