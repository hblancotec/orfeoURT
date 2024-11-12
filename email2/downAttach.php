<?php
session_start();
set_time_limit(0);
ini_set('max_execution_time', '600');
extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);
$ruta_raiz = "..";
if (! isset($_SESSION['dependencia']))
    include "../rec_session.php";
require "$ruta_raiz/config.php";

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

$msgId = $_GET['msgNo'];
if ($msgId) {
    $fileMsg = BODEGAPATH . "/tmp/". $msgId . ".eml";
    
    if (!file_exists($fileMsg) || (filesize($fileMsg) == 0) ) {
        $dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert/readonly}INBOX";
        $mbox = imap2_open($dsn, $correo_mail_incoming, $passwd_mail_incoming);
        //$dsn = "{" . "outlook.office365.com" . ":" . 993 . "/imap/ssl/novalidate-cert/readonly}INBOX";
        //$mbox = imap2_open($dsn, "radicacionorfeo@dnp.gov.co", $access_token, OP_XOAUTH2) or die('Falló en la conexión: ' . imap2_last_error());       
        if ($mbox === FALSE) {
            die("No hay conexi&oacute;n al buz&oacute;n $correo_mail_incoming");
        } else {
            if (! imap2_savebody($mbox, $fileMsg, $msgId, '', FT_UID)) {
                echo 'No se pudo descargar correo ' . $msgmid;
            }
            imap2_close($mbox);
        }
    }
    $cfc = file_get_contents($fileMsg);
    $cxf = mailparse_msg_parse_file($fileMsg);
    $part = mailparse_msg_get_part($cxf, $id);
    $part_data = mailparse_msg_get_part_data($part);
    $start = $part_data['starting-pos-body'];
    $end = $part_data['ending-pos-body'];
    $salida = imap2_base64(substr($cfc, $start, $end - $start));
    mailparse_msg_free($cxf);
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . $part_data['disposition-filename'] . "\"");
    echo $salida;
} else {
    echo "Faltan datos de entrada";
}
?>