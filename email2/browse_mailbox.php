<?php
session_start();
if (count($_SESSION) == 0) {
    //die(include "../sinacceso.php");
    //exit();
} 

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

set_time_limit(0);
require "../config.php";

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

$dsn = "{" . "outlook.office365.com" . ":" . 993 . "/imap/ssl/novalidate-cert/readonly}INBOX";
$imap = imap2_open($dsn, "radicacionorfeo@dnp.gov.co", $access_token, OP_XOAUTH2) or die('Falló en la conexión: ' . imap2_last_error());
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
    
    $fileMsg = BODEGAPATH . "tmp". DIRECTORY_SEPARATOR. $uid . ".eml";
    
    if (! file_exists ( $fileMsg ) || filesize ( $fileMsg ) == 0) {
        $headers = imap2_fetchheader($imap, $i, FT_PREFETCHTEXT);
        $body = imap2_body($imap, $i);
        file_put_contents($fileMsg, $headers . "\n" . $body);
    }
    $date = date_create($header->MailDate, new DateTimeZone("America/Bogota"));
    
    //$date = DateTime::createFromFormat('D, d M Y H:i:s P', $header->date);
    //$date->setTimeZone(new DateTimeZone(ini_get('date.timezone')));
    ?>
	<tr class='<?php echo $claseLines; ?>'>
				<td width='1%'><?php echo $i; ?></td>
				<td width='9%'>
					<a href='mensaje.php?msgNo=<?php echo htmlentities($uid); ?>&krd=<?= $krd ?>' target='image'><?php echo $date->format('Y-m-d H:i:s');?></a></td>
				<td width='50%'><?= parsearString($header->subject); ?></td>
				<td width='20%'><?php echo parsearString($header->fromaddress); ?></td>
				<td width='20%'><?php echo parsearString($header->toaddress); 
				$strCodec = imap_mime_header_decode($header->toaddress);
				foreach ($strCodec as $key => $arrCodec) {
				    echo $arrCodec->charset . " - ";
				} ?></td>
			</tr>
<?php
}
$e = microtime(true);
imap_close($imap);
?>
	</table>
		<input type="hidden" name="krd" id="krd" value="<?= $krd ?>" />
	</form>
	<?php //echo ($e - $s);?>
</body>
</html>