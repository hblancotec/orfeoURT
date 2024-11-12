<?php
session_start();
?>
<html>
<head>
<title>WebMail OrfeoGpl.org</title>
<link rel="stylesheet" href="../estilos/orfeo.css" />
</head>
<body>
<?php
include("connectPop3.php");
if(!$connect){
	}else{
		echo "Error en los datos de Acceso....";
		include "login_email.php";
		die(" . . .");
	}
	$result=$pop3->ListMessages("",1);
	?>
	<table  class="borde_tab" width="100%" cellpadding="0" cellspacing="0">
	<tr class=titulo1>
		<th colspan=5>Buzon de <?=$user?> (<?=$numMsqs?> Mensajes)<br></th>
	</tr>  
	<tr class=titulo1>
	<th>No</th>
	<th>Fecha</th>
	<th>Asunto</th>
	<th>Remite</th>
	<th>Para</th>
	<th>Ad</th>
	</tr>
	<?php
	for($i=1; $i<=count($result);$i++){
		$mailAsunto="";
		$mailFecha="";
		$mailFrom="";
		$mailToF="";
		$mailAttach="";
		$pop3->RetrieveMessage($i,$headers,$body,12);
		//print_r($body);
		
		$mailAtach= "";
		for($iK=1; $iK<=count($headers);$iK++){
			if(substr(trim($headers[$iK]),0,8)=="Subject:") {
				$mailAsunto = $headers[$iK];$mailAsunto = str_replace("Subject:","",$mailAsunto);
				$mailAsunto = iconv_mime_decode($mailAsunto,0, "ISO-8859-1");
				}
			if(substr(trim($headers[$iK]),0,5)=="From:") {
				$mailFrom = substr($headers[$iK],0,150);
				$mailFrom = htmlentities(str_replace("From:","",$mailFrom));
			}
			if(substr(trim($headers[$iK]),0,3)=="To:") {
				$mailTo = iconv_mime_decode($headers[$iK],0, "ISO-8859-1");
				$mailToArray = array();
				$mailToArray= explode(", ",$mailTo,100);
				$mailto = "";
				$value="";
				
				foreach ($mailToArray as $key =>$value) {
					 if($key>=0) { $mailToF .= htmlentities($mailToArray[$key] ) ."<br>" ;}
				}
				$mailToF = substr($mailToF,0,150);
				$mailToF = str_replace("To:","",$mailToF);
			}
			if(substr($headers[$iK],0,5)=="Date:") { $mailFecha = $headers[$iK];$mailFecha = str_replace("Date:","",$mailFecha);}
			if(substr($headers[$iK],0,20)=="X-MS-Has-Attach: yes") {$mailAttach= "<img src='../imagenes/correo.gif'>";}
			if(substr($headers[$iK],0,11)=="Message-ID:") { $mailID = $headers[$iK];$mailID = str_replace("Message-ID:","",$mailID);}
		}
		$mailRemite = $headers[0];
		$b=2;
		if((fmod($i,2)==0 )){  $claseLines = "listado1"; }else{ $claseLines = "listado2";}
	?>
		<tr class=<?=$claseLines?>>
			<td width=10><?=$i?></td>
			<td width=50><?=$mailFecha?></td>
			<td width=200><a href="mensaje.php?PHPSESSID=<?=session_id()?>&msgNo=<?=$i?>&krd=<?=$krd?>&usuaEmail=<?=$usuaEmail?>&passwdEmail=<?=$passwdEmail?>" target=image><?=$mailAsunto?></a></td>
			<td width=200><?=$mailFrom?></td>
			<td width=300><?=$mailToF?> </td>
			<td width=10><?=$mailAttach?></td>
		</tr>
		<?php
	}
	?>
	</table>
	</BODY>
</HTML>