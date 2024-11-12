<?php

/**
 * Permite Enviar un correo con sus adjuntos.
 * Utiliza la librerÃ­a PHPMAILER.
 *
 * @autor	Hollman Ladino Paredes	hollmanlp@gmail.com
 * 			Carlos Eduardo Campos Garcia  careduc@gmail.com
 *
 * @Copyright GNU/GPL v3
 */
require "../lib/PHPMailer-6.4.1/src/PHPMailer.php";
require "../lib/PHPMailer-6.4.1/src/SMTP.php";
require "../lib/PHPMailer-6.4.1/src/Exception.php";
use PHPMailer\PHPMailer\PHPMailer;

class CorreoElectronico extends PHPMailer {

    private $esCertificado = false;
	
    /**
     * Envio de correo desde librería PHPMAILER en ORFEO.API
     * Cuendo se desee enviar correos desde el MVC del SGD Orfeo.
     * 
     * @param string $ruta_raiz
     * @param boolean $usoVelay
     */
    public function __construct($ruta_raiz, $usoVelay=TRUE) {
        parent::__construct();
        include $ruta_raiz."/config.php";
              
        $this->IsHTML(true);
        $this->Priority = 1;
        $this->IsSMTP();
        $this->Timeout = 60;
        $this->AltBody = "Para visualizar este correo utilice un cliente grafico que permita leer correos HTML.";
        $this->SetLanguage("es", ORFEOCFG . "/lib/PHPMailer/language/");
        $this->SMTPDebug = 0;
        $this->Body = $cuerpoMail;
        if ($usoVelay){
        	$this->SMTPSecure = $tls_mail;
        	$this->SMTPAuth = $auth_mail;
        	$this->Username = $correo_mail;
        	$this->Password = $passwd_mail;
        	$this->Host = $server_mail;
        	$this->From = $this->Username;
        	$this->FromName = $usuario_mail;
        	$this->Mailer = $protocolo_mail;
        	$this->Port = $port_mail;
        } else {
        	$this->SMTPSecure = $tls_mail_0365;
        	$this->SMTPAuth = $auth_mail_0365;
        	$this->Username = $correo_mail_0365;
        	$this->Password = $passwd_mail_0365;
        	$this->Host = $server_mail_0365;
        	$this->From = $this->Username;
        	$this->FromName = $usuario_mail_0365;
        	$this->Mailer = $protocolo_mail_0365;
        	$this->Port = $port_mail_0365;
        }
        $this->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => false
            )
        );
        /*$this->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => false,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
            )
        );*/
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
        $result = array();

        if (is_array($para)) {
            foreach ($para as $key => $dest)
                $this->AddAddress($dest);
        }

        if (is_array($cc)) {
            foreach ($cc as $key => $dest)
                $this->AddCC($dest);
        }

        if (is_array($cco)) {
            foreach ($cco as $key => $dest)
                $this->AddBCC($dest);
        }

        $this->Subject = $asunto;
        $this->Body = empty($cuerpo) ? $this->Body : $cuerpo;
        
        try {
            $result[] = $this->Send();
            if (! $result[0]) {
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