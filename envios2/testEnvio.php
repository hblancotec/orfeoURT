<?php
require_once "E:/Apache2.2/htdocs/orfeo/config.php";
require_once "E:/Apache2.2/htdocs/PHPMailer_5.2.4/class.phpmailer.php";

error_reporting(E_STRICT);
date_default_timezone_set('America/Bogota');

$mail             = new PHPMailer();
//$body             = file_get_contents('contents.html');
$body             = preg_replace('/[\]/','',$body);

$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = $server_mail;			// SMTP server
$mail->SMTPDebug  = 2;						// enables SMTP debug information (for testing)
											// 1 = errors and messages
											// 2 = messages only
$mail->SMTPAuth   = true;					// enable SMTP authentication
//$mail->Port       = 26;					// set the SMTP port for the GMAIL server
$mail->Username   = $correo_mail;			// SMTP account username
$mail->Password   = $passwd_mail;			// SMTP account password

//$mail->SetFrom('name@yourdomain.com', 'First Last');

$mail->AddReplyTo("hollmanlp@gmail.com","Hollman Alberto Ladino Paredes");

$mail->Subject    = "PHPMailer Test Subject via smtp, basic with authentication";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML("<html><head></head><body>Cuerpo en <b>html.<br/>Env&ioacute;o de notificaci&oacute;n.</b></body></html>");

$address = "hollmanladinop@yahoo.com.ar";
$mail->AddAddress($address, "John Doe");
?>
<html>
<head>
<title>PHPMailer - SMTP basic test with authentication</title>
</head>
<body>
<?php
if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "1";
}
?>
</body>
</html>