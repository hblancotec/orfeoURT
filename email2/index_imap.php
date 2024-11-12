<?php
ini_set('show_errors', 1);

session_start();
$ruta_raiz = ".";
include_once "$ruta_raiz/config.php"; 			// incluir configuracion.

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

$CLIENT_ID = "fcf56d33-1e66-4894-b57c-6269498967af";
$CLIENT_SECRET = "pzp8Q~O0JZ0Wq8VOE.h7kNGD_2ijSufc6sk9Zder";

//$CLIENT_ID = "6701f011-7fdb-40c6-9b68-5969512c05e1";
//$CLIENT_SECRET = "igk8Q~Aqq0vTFA_8pme5D3FTEYEfinBFGcwRSclk";
$TENANT = "04260e20-234c-4c9f-a9dd-79286b1b70ac";
//$SCOPE = "openid%20profile%20offline_access%20https%3A%2F%2Foutlook.office365.com%2FSMTP.Send%20https%3A%2F%2Foutlook.office365.com%2FPOP.AccessAsUser.All%20https%3A%2F%2Foutlook.office365.com%2FIMAP.AccessAsUser.All";
$SCOPE = "https://outlook.office365.com/IMAP.AccessAsUser.All";
$REDIRECT_URI="https://orfeojfzr.dnp.gov.co/email2/index.php";

//$url2 = "https://login.microsoftonline.com/04260e20-234c-4c9f-a9dd-79286b1b70ac/oauth2/v2.0/authorize?response_type=code&scope=".$SCOPE."&redirect_uri=".$REDIRECT_URI."&client_id=".$CLIENT_ID."&client_secret=".$CLIENT_SECRET."&state=IAFjZEc-Gbx5pPSOj2jnjc-MSiNTyiydFB-rO_BxcuA&prompt=login";
$url2 = "https://login.microsoftonline.com/04260e20-234c-4c9f-a9dd-79286b1b70ac/oauth2/v2.0/authorize?response_type=code&scope=openid%20profile%20offline_access%20https%3A%2F%2Foutlook.office365.com%2FSMTP.Send%20https%3A%2F%2Foutlook.office365.com%2FPOP.AccessAsUser.All%20https%3A%2F%2Foutlook.office365.com%2FIMAP.AccessAsUser.All&redirect_uri=https%3A%2F%2Forfeojfzr.dnp.gov.co%2Femail2%2Findex.php&client_id=".$CLIENT_ID."&client_secret=igk8Q~Aqq0vTFA_8pme5D3FTEYEfinBFGcwRSclk&state=IAFjZEc-Gbx5pPSOj2jnjc-MSiNTyiydFB-rO_BxcuA&prompt=login";

if (!isset($_GET['code'])) {
    //If we don't have an authorization code then get one
    //$authUrl = $provider->getAuthorizationUrl($options);
    //$_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $url2);
    exit;
    //Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state'])) {
    unset($_SESSION['oauth2state']);
    unset($_SESSION['provider']);
    exit('Invalid state');
} else {
    unset($_SESSION['provider']);
    
    $CODE = $_GET['code'];
    $SESSION = $_GET['session_state'];
    $STATE = $_GET['state'];
    
    $url = "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";
      
    $resource_id = "https://api.office.com/discovery/";
    //$data = "client_id=".$CLIENT_ID."&redirect_uri=".$REDIRECT_URI."&client_secret=".$CLIENT_SECRET."&code=".$CODE."&grant_type=authorization_code&scope=$SCOPE";
    $data = "client_id=".$CLIENT_ID."&redirect_uri=".$REDIRECT_URI."&client_secret=".$CLIENT_SECRET."&code=".$CODE."&session_state=".$SESSION."&grant_type=authorization_code&scope=$SCOPE";
    try
    {
        echo $data;
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
    $arraytoreturn = Array(
        'access_token' => $out2['access_token'],
        'refresh_token' => $out2['refresh_token'],
        'expires_in' => $out2['expires_in']
    );
    //echo "Get access toke and refresh token in office 365 using PHP<br>";
    //echo "access token :: ".$get_access_token."<br>";
    //echo "refresh token :: ".$get_refresh_token."<br>";
      
    $dsn = "{" . "outlook.office365.com" . ":" . 993 . "/imap/ssl/novalidate-cert/readonly}INBOX";
    $imap = imap2_open($dsn, "notificaciones_sgdorfeo@dnp.gov.co", $get_access_token, OP_XOAUTH2) or die('Falló en la conexión: ' . imap2_last_error());
    $n_msgs = imap2_num_msg($imap);

    imap2_headers($imap);
    
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
                        $ret .= htmlspecialchars($arrCodec->text, ENT_QUOTES, 'utf-8');
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
    
    $s = microtime(true);
    ?>
<html>
<head>
<title>WebMail OrfeoGpl.org</title>
<link rel="stylesheet" href="/estilos/orfeo.css" />
</head>
<body>
	<form name='frmBrowseMailbox' action='mensaje.php' method="POST">
		<table class="borde_tab" width="100%" cellpadding="0" cellspacing="0">
			<tr class=titulo1>
				<th colspan='6'>Buz&oacute;n de <?= $usuario_mail ?> (<?= $n_msgs?> Mensajes)</th>
			</tr>
			<tr class='titulo1'>
				<th>No</th>
				<th>Fecha</th>
				<th>Asunto</th>
				<th>Remite</th>
				<th>Para</th>
			</tr>
<?php
for ($i = 1; $i <= $n_msgs; $i ++) {
    $header = imap2_header($imap, $i);
    $uid = imap2_uid($imap, $i);
    if ((fmod($i, 2) == 0)) {
        $claseLines = "listado1";
    } else {
        $claseLines = "listado2";
    }
    // Fri, 15 Jun 2018 17:00:24 +0000
    $date = date_create($header->date, new DateTimeZone("America/Bogota"));
    
    //$date = DateTime::createFromFormat('D, d M Y H:i:s P', $header->date);
    //$date->setTimeZone(new DateTimeZone(ini_get('date.timezone')));
    ?>
	<tr class='<?php echo $claseLines; ?>'>
				<td width='1%'><?php echo $i; ?></td>
				<td width='9%'><a
					href="mensaje.php?msgNo=<?php echo htmlentities($uid); ?>&krd=<?= $krd ?>"
					target='image'><?php echo $date->format('Y-m-d H:i:s');?></a></td>
				<td width='50%'><?= parsearString($header->subject); ?></td>
				<td width='20%'><?php echo parsearString($header->fromaddress); ?></td>
				<td width='20%'><?php echo parsearString($header->toaddress); 
				$strCodec = imap_mime_header_decode($header->toaddress);
				foreach ($strCodec as $key => $arrCodec) {
				    echo $arrCodec->charset . " - ";
				} ?></td>
			</tr>
	</table>
	</form>
<?php
}
$e = microtime(true);
imap_close($imap);

}
?>