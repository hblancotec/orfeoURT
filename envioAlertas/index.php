<?php
    define('ORFEOPATH', '/var/www/orfeo36/');
    define('ALERTASDIR', ORFEOPATH . 'envioAlertas/');
	require(ALERTASDIR . 'class.phpmailer.php');
	require(ALERTASDIR . 'capturaDatos.php');
	
	// Variables para la configuracion de la cuenta de envio de correo
	//$emailOrigen 	= "cparra@dnp.gov.co";
	$servidorSmtp 	= "ILPOSTINO.dnp.ad";
	//$usuarioSmtp 	= "cparra";
	//$passSmtp	= "cmadnp";
	$webOrfeo 	= "https://embera.dnp.gov.co/orfeo_3.6.0/";
	
	function enviarCorreo($usuario = array(),
				$emailOrigen = null,
				$servidorSmtp = null,
				$usuarioSmtp = null,
				$passSmtp = null,
				$web = null) {
		$nombreUsuario 	= $usuario["NOMBRE_USUARIO"];
		$emailDestino	= $usuario["EMAIL"];
		$expediente 	= $usuario["NUM_EXPEDIENTE"];
		$nombreFlujo 	= $usuario["NOMBRE_FLUJO"];
		$nombreEtapa 	= $usuario["NOMBRE_ETAPA"];
		$numDias	= $usuario["NUM_DIAS"];
		$fechaFin	= $usuario["FECHA_FINALIZACION"];
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
		$mail->From 	= $emailOrigen;
		$mail->FromName = "Sistema Orfeo";
		$mail->Host 	= $servidorSmtp;
		$mail->Mailer   = "smtp";
		$mail->Password = $passSmtp;
		$mail->Username = $usuarioSmtp;
		$mail->Subject 	= "Alerta Sistemas Orfeo (Proceso por finalizarse)";
		$mail->SMTPAuth =  "true";
		$mail->Body 	= $cuerpo;
		$mail->IsHTML(true);
		$mail->AddAddress($emailDestino,$nombreUsuario);
		$mail->AddReplyTo($emailOrigen,$nombreOrfeo);
		
		if (!$mail->Send()) {
			echo "<p>There was an error in sending mail, please try again at a later time</p>";
			return false;
		}

		$mail->ClearAddresses();
		$mail->ClearAttachments();
		
		return true;
	}
	
	// si por lo menos tiene un usuario al cual enviar correo
	// $enviarAlerta viene del archivo capturaDatos.php
	if (!empty($enviarAlerta[0])) {
		foreach ($enviarAlerta as $usuario) {
			$envio = enviarCorreo($usuario,
						$emailOrigen,
						$servidorSmtp,
						$usuarioSmtp,
						$passSmtp,
						$web);
			if ($envio) {
				// Si envio almacenar en archivo temporal de envio exitoso
				//echo "Envio exitoso";
			} else {
				// Si no envio almacenar en archivo temporal de no exitoso
				echo "Falla en envio";
			}
		}
	}
?>
