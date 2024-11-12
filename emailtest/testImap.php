<?php

$func = (!empty($_GET["func"])) ? $_GET["func"] : "view";
$folder = (!empty($_GET["folder"])) ? $_GET["folder"] : "INBOX";
$uid = (!empty($_GET["uid"])) ? $_GET["uid"] : 0;


echo "Inicio script " . date('Y-m-d h:i:s') . "<br>";
require "./config.php";
$dsn = "{" . $server_mail_incoming . ":" . $port_mail_incoming . "/imap/ssl/novalidate-cert}$carpetaLecturaCertimail";
$mbox = imap_open($dsn, $correo_mail_incoming, $passwd_mail_incoming, OP_READONLY);
echo "imap_open " . date('Y-m-d h:i:s') . "<br>";

echo "<h1>Buzones en $server_mail_incoming de $correo_mail_incoming</h1>\n";

$carpetas = imap_listmailbox($mbox, $dsn, "*");
echo "imap_listmailbox " . date('Y-m-d h:i:s') . "<br>";

if ($carpetas == false) {
    echo "Llamada fallida<br />\n";
} else {
    foreach ($carpetas as $val) {
        echo $val . "<br />\n";
    }
}

echo "N&uacute;mero de mensajes: " . imap_num_msg($mbox) . "<br>";
$arrCorreos = imap_search($mbox, "FROM Receipt", SE_UID);
echo "N&uacute;mero de mensajes de Receipt &lt;receipt@rpost.net&gt; : " . count($arrCorreos) . "<br>";
for ($index1 = 0; $index1 <= count($arrCorreos); $index1++) {
    $header = imap_header($mbox, imap_msgno($mbox, $arrCorreos[$index1]));

    $fromInfo = $header->from[0];
    $replyInfo = $header->reply_to[0];

    $details = array(
        "fromAddr" => (isset($fromInfo->mailbox) && isset($fromInfo->host)) ? $fromInfo->mailbox . "@" . $fromInfo->host : "",
        "fromName" => (isset($fromInfo->personal)) ? $fromInfo->personal : "",
        "replyAddr" => (isset($replyInfo->mailbox) && isset($replyInfo->host)) ? $replyInfo->mailbox . "@" . $replyInfo->host : "",
        "replyName" => (isset($replyTo->personal)) ? $replyto->personal : "",
        "subject" => (isset($header->subject)) ? $header->subject : "",
        "udate" => (isset($header->udate)) ? $header->udate : ""
    );

    $uid = $arrCorreos[$index1];
    $mailStruct = imap_fetchstructure($mbox, $uid, FT_UID);
    $attachments = getAttachments($mbox, $uid, $mailStruct, "");

    echo "<ul>";
    echo "<li><strong>From:</strong>" . $details["fromName"];
    echo " " . $details["fromAddr"] . "</li>";
    echo "<li><strong>Subject:</strong> " . $details["subject"] . "</li>";
    echo '<li><a href="testImap.php?folder=' . $folder . '&uid=' . $uid . '&func=read">Read</a>';
    echo " | ";
    echo '<a href="testImap.php?folder=' . $folder . '&uid=' . $uid . '&func=delete">Delete</a></li>';
    echo "</ul>";

    echo "Attachments: ";
    foreach ($attachments as $attachment) {

        echo '<a href="testImap.php?func=' . $func . '&folder=' . $folder . '&uid=' . $uid .
        '&part=' . $attachment["partNum"] . '&enc=' . $attachment["enc"] . '">' .
        $attachment["name"] . "</a><br/>";
        if ($attachment["name"]=='DeliveryReceipt.xml'){
            $que = downloadAttachment($mbox, $uid, $attachment["partNum"], $attachment["enc"], $path);
            $xml = simplexml_load_string($que);
            $radCopia=$xml->MessageClientCode;
            echo (!empty($radCopia)) ? $radCopia."<br/>" : "";
        }
    }
}
print_r($arrCorreos);

echo "<h1>Cabeceras en INBOX</h1>\n";
$cabeceras = imap_headers($mbox);

if ($cabeceras == false) {
    echo "Llamada fallida<br />\n";
} else {
    foreach ($cabeceras as $val) {
        echo $val . "<br />\n";
    }
}
echo "imap_listmailbox " . date('Y-m-d h:i:s') . "<br>";
echo "<h1>Leyendo cada correo</h1>";

for ($index = 0; $index <= imap_num_msg($mbox); $index++) {
    $header = imap_header($mbox, $index);

    $fromInfo = $header->from[0];
    $replyInfo = $header->reply_to[0];

    $details = array(
        "fromAddr" => (isset($fromInfo->mailbox) && isset($fromInfo->host)) ? $fromInfo->mailbox . "@" . $fromInfo->host : "",
        "fromName" => (isset($fromInfo->personal)) ? $fromInfo->personal : "",
        "replyAddr" => (isset($replyInfo->mailbox) && isset($replyInfo->host)) ? $replyInfo->mailbox . "@" . $replyInfo->host : "",
        "replyName" => (isset($replyTo->personal)) ? $replyto->personal : "",
        "subject" => (isset($header->subject)) ? $header->subject : "",
        "udate" => (isset($header->udate)) ? $header->udate : ""
    );

    $uid = imap_uid($mbox, $index);

    echo "<ul>";
    echo "<li><strong>From:</strong>" . $details["fromName"];
    echo " " . $details["fromAddr"] . "</li>";
    echo "<li><strong>Subject:</strong> " . $details["subject"] . "</li>";
    echo '<li><a href="testImap.php?folder=' . $folder . '&uid=' . $uid . '&func=read">Read</a>';
    echo " | ";
    echo '<a href="testImap.php?folder=' . $folder . '&uid=' . $uid . '&func=delete">Delete</a></li>';
    echo "</ul>";
}
//bool imap_mail_move ( resource $imap_stream , string $msglist , string $mailbox [, int $options = 0 ] )

imap_close($mbox);
echo "imap_close " . date('Y-m-d h:i:s') . "<br>";

function getAttachments($imap, $mailNum, $part, $partNum) {
    $attachments = array();

    if (isset($part->parts)) {
        foreach ($part->parts as $key => $subpart) {
            if ($partNum != "") {
                $newPartNum = $partNum . "." . ($key + 1);
            } else {
                $newPartNum = ($key + 1);
            }
            $result = getAttachments($imap, $mailNum, $subpart, $newPartNum);
            if (count($result) != 0) {
                array_push($attachments, $result);
            }
        }
    } else if (isset($part->disposition)) {
        if (strtoupper($part->disposition) == "ATTACHMENT") {
            $partStruct = imap_bodystruct($imap, imap_msgno($imap, $mailNum), $partNum);
            //$partStruct1 = imap_fetchstructure($imap, $mailNum, FT_UID);
            $attachmentDetails = array(
                "name" => $part->dparameters[0]->value,
                "partNum" => $partNum,
                "enc" => $partStruct->encoding
            );
            return $attachmentDetails;
        }
    }
    return $attachments;
}

function downloadAttachment($imap, $uid, $partNum, $encoding, $path) {
    $partStruct = imap_bodystruct($imap, imap_msgno($imap, $uid), $partNum);

    $filename = $partStruct->dparameters[0]->value;
    $message = imap_fetchbody($imap, $uid, $partNum, FT_UID);

    switch ($encoding) {
        case 0:
        case 1:
            $message = imap_8bit($message);
            break;
        case 2:
            $message = imap_binary($message);
            break;
        case 3:
            $message = imap_base64($message);
            break;
        case 4:
            $message = quoted_printable_decode($message);
            break;
    }
    return $message;
}

?>