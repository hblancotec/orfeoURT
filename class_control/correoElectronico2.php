<?php

/**
 * Permite Enviar un correo con sus adjuntos.
 * Utiliza la libreria PHPMAILER.
 *
 * @autor	Hollman Ladino Paredes	hollmanlp@gmail.com
 * 			Carlos Eduardo Campos Garcia  careduc@gmail.com
 *
 * @Copyright GNU/GPL v3
 */
require "PHPMailer/PHPMailer.php";
require "PHPMailer/SMTP.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class correoElectronico extends PHPMailer {

    private $esCertificado = false;

    /**
     *
     */
    public function __construct($ruta_raiz, $esCertificado=FALSE, $usoVrelay=true) {
        parent::__construct();
        include $ruta_raiz."/config.php";
        $this->IsHTML(true);
        //$this->Priority = 1;
        //$this->esCertificado = $esCertificado;
        $this->isSMTP();
        $this->SMTPSecure = $tls_mail;
        $this->SMTPAuth = $auth_mail;
        if ($esCertificado) {
            $this->Username = $correo_certimail;
            $this->Password = $passwd_certimal;
            $this->Subject = $asunto_certimail;
            $this->Body = $cuerpo_certimail;
        } else {
            $this->Username = $correo_mail;
            $this->Password = $passwd_mail;
        }
        if ($usoVrelay){
            $this->Host = $server_mail;
            $this->Mailer = $protocolo_mail;
        } else {
            $this->SMTPSecure = $tls_mail_0365;
            $this->SMTPAuth = $auth_mail_0365;
            $this->Host = $server_mail_0365;
            $this->Port= $port_mail_0365;
            $this->Mailer = $protocolo_mail_0365;
            $this->Password = $passwd_mail_0365;
            $this->FromName = $correo_mail_0365;
            $this->From = $correo_mail_0365;
        }
        $this->SMTPDebug = 0;
        $this->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        //$this->Timeout = 180;
        $this->setFrom($correo_mail_0365, "Notificaciones URT (Orfeo)");
        
        //$this->From = $this->Username;
        $this->AltBody = "Para visualizar este correo utilice un cliente grafico que permita leer correos HTML.";
        $this->SetLanguage("es", ORFEOCFG . "/lib/PHPMailer/language/");
    }
    
    /**
     * Agrega un archivo adjunto al correo electronico.
     * @param string $rutaFisica
     * @param string $nombreAvisualizar
     */
    public function agregarAdjunto($rutaFisica, $nombreAvisualizar) {
        return $this->AddAttachment($rutaFisica, $nombreAvisualizar);
    }

    /**
     * Envia correo electronico.
     * @param Array String		$para 	$para[0]="ccampo@dnp.gov.co"  $cc[1] = "fulanito@hotmail.com"
     * @param Array String		$cc   	$cc[0]="alguien@correo.com.co"  $cc[1] = "otromas@gmail.com"
     * @param Array String		$cco  	$cco[0]="guasarapo@outlook.com" $cco[1] = "pepito_perez@yahoo.com.ar"
     * @param string 			$asunto
     * @param string 			$cuerpo	"<p>Cuerpo debe tener formato <b>HTML</b></p>";
     * @return bool				true Exito	false Fallo
     */
    public function enviarCorreo($para, $cc, $cco, $asunto, $cuerpo) {
        $result = null;

        if (is_array($para)) {
            foreach ($para as $key => $dest)
                $this->addAddress(($this->esCertificado) ? $dest.".rpost.org" : $dest, $key);
        }

        if (is_array($cc)) {
            foreach ($cc as $key => $dest)
                $this->AddCC(($this->esCertificado) ? $dest.".rpost.org" : $dest);
        }

        if (is_array($cco)) {
            foreach ($cco as $key => $dest)
                $this->AddBCC(($this->esCertificado) ? $dest.".rpost.org" : $dest);
        }

        $this->Subject = $asunto;
        $this->Body = empty($this->Body) ? $cuerpo : $this->Body;

        $result = ($this->Send() === TRUE) ? TRUE : $this->ErrorInfo;
        $this->ClearAllRecipients();
        $this->ClearAttachments();

        return $result;
    }

}

?>