<?php
session_start();
session_id($PHPSESSID);
$dependencia = $_SESSION["dependencia"];
extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);
$ruta_raiz = "..";
if(!isset($_SESSION['dependencia']))
	include "../rec_session.php";
include '../config.php';
$servidor_mail = "ilpostimo.dnp.ad";
$puerto_mail = "995";
?>

<HTML>
 <HEAD>
  <link rel="stylesheet" href="../estilos/orfeo.css">
  <STYLE TYPE="text/css">
    #flotante { position: absolute; top:100; left: 550px; visibility: visible;}
  </STYLE>
  <SCRIPT>
  function asociarMail()
  {
	numeroRad = parent.frames['formulario'].document.getElementById('numeroRadicado').value;
	if(numeroRad>=1){
		document.getElementById('numeroRadicado').value = numeroRad;
		document.getElementById('formAsociarMail').submit();
	}
	else{
		alert(" ¡ No se generado un Radicado ! ");
	}
  }
  </SCRIPT>
 </HEAD>
 <BODY>
  <FORM method=GET name=formAsociarMail id=formAsociarMail action='mensaje.php'>
   <input type=hidden name=numeroRadicado id=numeroRadicado>
   <input type=hidden name=passwdEmail  value=<?=urldecode($passwdEmail)?> >
   <input type=hidden name=usuaEmail  value=<?=$usuaEmail?> >
   <input type=hidden name=msgNo  value=<?=$msgNo?> >
   <input type=hidden name=krd  value=<?=$krd?> >
   <input type=hidden name=PHPSESSID  value=<?=$PHPSESSID?> >
   <input type=hidden name=dependencia  value=<?=$dependencia?> >
  </FORM>

<?php
$ruta_raiz = "..";
include($ruta_raiz.'/config.php');
include "connectIMAP2.php";

//----------Funcion Suprime caracteres no imprimibles--------------------//
function sup_tilde($str)
{
	$stdchars= array("@","a","e","i","o","u","n","A","E","I","O","U","N"," "," ");
	$tildechars= array("@","=E1","=E9","=ED","=F3","=FA","=F1","=C1","=C9","=CD","=D3","=DA","=D1","=?iso-8859-1?Q?","?=");
	return str_replace($tildechars,$stdchars, $str);
}

//-----------------------------------------------------------------------//
//if(isset($_GET['mid'])&&isset($_GET['pid'])){
if($msgNo){
	//$body =$msg->getBody($_GET['mid'], $_GET['pid']);
	//lectura cabeceras----
	$datos = $msg->getHeaders($msgNo);
	$msgPid = $msg->structure[$msgNo]["pid"];
	$pidMail = "";

	//-----------------Encabezado de email-------------------------//
	$contenidoEmail = $head;
	$datos = $msg->getHeaders($msgNo,$pidMail);
	//print_r($msg);
	$eMailRemitente = sup_tilde($msg->header[$msgNo]['from'][0]);
	$eMailNombreRemitente = sup_tilde($msg->header[$msgNo]['from_personal'][0]);
	$_SESSION['eMailRemitente']=$eMailRemitente;
	$_SESSION['eMailNombreRemitente']=$eMailNombreRemitente;
	$mailAsunto="";
	$mailFecha="";
	$pop3->RetrieveMessage($msgNo,$headers,$body,100);
	
	//print_r($headers);
	for($iK=0; $iK<=count($headers);$iK++){
		if(substr(trim($headers[$iK]),0,8)=="Subject:") {
			$mailAsunto = $headers[$iK];
			$mailAsunto = str_replace("Subject:","",$mailAsunto);
			$mailAsunto = iconv_mime_decode($mailAsunto,0, "ISO-8859-1");
		}
		if(substr(trim($headers[$iK]),0,5)=="Date:") {
			$mailDate = $headers[$iK];
			$mailDate = str_replace("Date:","",$mailAsunto);
			$mailDate = iconv_mime_decode($mailDate,0, "ISO-8859-1");
		}
		if(substr(trim($headers[$iK]),0,5)=="From:") {
			$mailFrom = substr($headers[$iK],0,150);
			$mailFrom = htmlentities(str_replace("From:","",$mailFrom));
		}
		if(substr(trim($headers[$iK]),0,9)=="Received:") {
			$mailReceived = substr($headers[$iK],0,150);
			$mailReceived = htmlentities(str_replace("Received:","",$mailReceived));
		}
		if(substr(trim($headers[$iK]),0,3)=="To:") {
			$mailTo = iconv_mime_decode($headers[$iK],0, "ISO-8859-1");
			$mailToArray = array();
			$mailToArray= explode(", ",$mailTo,100);
			$mailto = "";
			$value="";
		  
			foreach ($mailToArray as $key =>$value) {
				if($key>=0) { 
					$mailToF .= htmlentities($mailToArray[$key] ) ."<br>" ;
				}
			}
			$mailToF = substr($mailToF,0,150);
			$mailToF = str_replace("To:","",$mailToF);
		}
	}

	$html = "<b>texto en negrita</b><a href=hola.html>Haz clic sobre mí</a>";
	$headRadicado = "<TABLE width=\"80%\" cellspacing=\"7\" border=\"0\" cellpadding=\"0\" class=\"borde_tab\" >
		  <tr> 	<td width=60%>&nbsp;</td>
				<td> <FONT face='free3of9,FREE3OF9, FREE3OF9X,Free 3 of 9' SIZE=12>*$numeroRadicado*</FONT><br>
					Radicado No. $numeroRadicado<br>
					Fecha : ".date("Y/m/d")."
				</td>
		  </tr>
		</TABLE>";
	$mailFromD = explode(' ', $mailFrom);
	$countC = count($mailFromD);
	if( $countC >=2 ) {
		$mailFromD = $mailFromD[($countC-1)];
		$mailFromD = str_replace("<","",trim($mailFromD));
		$mailFromD = str_replace(">","",$mailFromD);
	}
	else{
		$mailFromD = trim($mailFrom);
	}
	$encabezado = "krd=$krd&PHPSESSID=".session_id()."&eMailMid=$msgNo&ent=2&eMailMid=$msgNo&datoP=".md5($krd)."&rtb=".md5("aa22")."&tipoMedio=eMail&usuaEmail=$usuaEmail&passwdEmail=".urlencode($passwdEmail)."&mailFrom=$mailFromD?>&mailAsunto=".str_replace("#"," ",htmlentities($mailAsunto));
?>

   <table width="100%" class="borde_tab">
	<tr class=titulos2>
	 <td align=right>
	  <font size=1>
	   <a href='../radicacion/chequear.php?<?=$encabezado?>' target='formulario'>Radicar Este Correo</a>
	   &nbsp;-&nbsp;
	   <a href='#' onClick="asociarMail();">Asociar Mail a Radicado</a> 
	   &nbsp;-&nbsp;
	   <a href='browse_mailbox.php?<?="krd=$krd&PHPSESSID=".session_id()?>' target='formulario'>Volver a Inbox</a>
	  </font>
	 </td>
	</tr>
   </table>

<?php
	$head .=$headRadicado;
	$head .="<TABLE> <tr> <td> </td> </tr> </TABLE> 
			<TABLE class=borde_tab width=80%>
			 <tr> <td CLASS=titulos2 width=15%>De </td> <td CLASS=LISTADO2> $mailFrom </td> </tr>
			 <tr> <td CLASS=titulos2>Asunto </td> <td CLASS=LISTADO2> $mailAsunto </td> </tr>
			 <tr> <td CLASS=titulos2>Para </td> <td CLASS=LISTADO2> $mailToF </td> </tr>
			 <tr> <td CLASS=titulos2>Datos Envio </td> <td CLASS=LISTADO2> $mailReceived </td> </tr>
			</TABLE>";
	//----------------------------------------------------------------------------//
	$cuerpoMail = "";
	$MailAdjuntos = "";
	$iAnexo = 0;
	foreach($msgPid as $key => $value){
		$entro = 2;
		$body = $msg->getBody($msgNo,$value);
		//print_r($body);
		if($body["ftype"]=="text/html" and !$cuerpoMail) {
			$cuerpoMail = $body["message"];
			$entro = 1;
		}
		if($body["ftype"]=="text/plain") {
			$cuerpoMailPlain = $body["message"];
		}
		if($body["ftype"]=="image/jpeg" or $body["ftype"]=="image/gif" or $body["ftype"]=="image/png"){
			//$mailAsunto = iconv_mime_decode($mailAsunto,0, "ISO-8859-1");
			$fname = explode('.',$body["fname"],2);
			$buscarReg = '/cid:'.$fname[0].'(.*[a-z0-9])@(.*)"/';
			preg_match($buscarReg, "<br>".$cuerpoMail, $parts);
			$imagen = "../bodega/tmp/".iconv_mime_decode($body["fname"],0, "ISO-8859-1");
			$imagenMail = $parts[0];
			$imagenMailX = explode('"',$imagenMail,2);
			$imagenMail = $imagenMailX[0];
			$cuerpoMail = str_replace(str_replace('"','',$imagenMail),$imagen, $cuerpoMail);
			//echo "<hr>$imagenMail ---> $imagen";
			$file = fopen($imagen,"w");
			if(!fputs($file,$body["message"])) 
				echo "<hr> No se guardo Imagen.  $imagen";;
			fclose($file);
		}
		if($numeroRadicado){
			include_once "../include/db/ConnectionHandler.php";
			include_once "../class_control/AplIntegrada.php";
			$db = new ConnectionHandler("..");
			$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		}
		if($entro==2 and $body["fname"]){
			$iAnexo++;
			$fname = iconv_mime_decode($body["fname"],0, "ISO-8859-1");
			$nameAdj = explode(".", $fname);
			$imagen = "../bodega/tmp/".$fname;
			if(!$numeroRadicado){
				$file = fopen($imagen,"w");
				if(!fputs($file,$body["message"])) 
					echo "<hr> No se guardo Archivo.  $imagen";;
				fclose($file);
				$mailAdjuntos .= "<a href='$imagen'>".$fname."</a><br>";
			}
			else{
				$aExtension = substr($fname,-5,5);
				$aExt = explode(".",$fname,2);
				$codigoAnexo = $numeroRadicado."000$iAnexo";
				$bExt = str_replace(".","",$aExt[1]);
				$iSql ="SELECT	ANEX_TIPO_CODI
						FROM	ANEXOS_TIPO
						WHERE	ANEX_TIPO_EXT = '".$nameAdj[1]."'";
				$rs = $db->conn->Execute($iSql);
				$anexTipo = $rs->fields["ANEX_TIPO_CODI"];
				if(!$anexTipo)
					$anexTipo = 0;
				$tmpNameEmail = $numeroRadicado."_000".$iAnexo.".".$nameAdj[1];
				//echo "<br>acá va tmpNameEmail".$tmpNameEmail;

				$directorio = substr($numeroRadicado,0,4) ."/". substr($numeroRadicado,4,3)."/docs/";
				$fileEmailMsg = "../bodega/$directorio".$tmpNameEmail;
				$file = fopen($fileEmailMsg,"w");
				if(!fputs($file,$body["message"]))
					echo "<hr> No se guardo Archivo.  $imagen";;
				fclose($file);
				$mailAdjuntos .= "<a href='$fileEmailMsg'>".$fname."</a><br>";
				$cuerpoMail =  str_ireplace($imagen,$fileEmailMsg,$cuerpoMail);
				$fecha_hoy = Date("Y-m-d");
				if(!$db->conn)
					echo "No hay conexion";
				$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
				$record["ANEX_RADI_NUME"]	= $numeroRadicado;
				$record["ANEX_CODIGO"] 		= $codigoAnexo;
				// $record["ANEX_TAMANO"] 	= "'".$anexoTamano."'";
				$record["ANEX_SOLO_LECT"] 	= "'S'";
				$record["ANEX_CREADOR"] 	= "'".$krd."'";
				$record["ANEX_DESC"] 		= "' Archivo: .". $fname."'";
				$record["ANEX_NUMERO"] 		= $iAnexo;
				$record["ANEX_NOMB_ARCHIVO"]= "'".$tmpNameEmail."'";
				$record["ANEX_BORRADO"] 	= "'N'";
				$record["ANEX_DEPE_CREADOR"]= $dependencia;
				$record["SGD_TPR_CODIGO"] 	= '0';
				$record["ANEX_TIPO"] 		= $anexTipo;
				$record["ANEX_FECH_ANEX"] 	= $sqlFechaHoy;
				$db->insert("anexos", $record, "true");
			}
		}
		//image/jpeg
		//image/gif
		//print_r($body);
		if(sup_tilde($msg->header[$msgNo]['from'][0]) and !$pidMail) 
			$pidMail=$value;
	}
	echo $head;
	$cuerpoMail =  "<TABLE class=borde_tab WIDTH=80%><tr><td>".$cuerpoMail."</td></tr></table>";
	if($cuerpoMail) echo $cuerpoMail; else $cuerpoMailPlain;
	//$pidMail = "4";
	if($mailAdjuntos){
		$adjuntosHtml = "<TABLE> <tr> <td> </td> </tr> </TABLE> <TABLE class=borde_tab width=80%> <tr> <td class=titulos2>
			Archivos Adjuntos </td> </tr> <tr> <td class=listado2> $mailAdjuntos </td> </tr> </TABLEtable>";
		echo $adjuntosHtml;
	}
	if($numeroRadicado){
		$archivoRadicado = "";
		$tmpNameEmail = $numeroRadicado.".html";
		$directorio = substr($numeroRadicado,0,4) ."/". substr($numeroRadicado,4,3)."/";
		$fileRadicado = "../bodega/$directorio".$tmpNameEmail;
		$archivoRadicado = "<HTML> <HEAD> <link rel='stylesheet' href='../estilos/orfeo.css'> </HEAD>
								<BODY>". $head . "". $cuerpoMail. "" ."<hr>".$adjuntosHtml ."</BODY></HTML>";
		$archivoRadicado = str_replace("../","../../../",$archivoRadicado);
		$file1=fopen($fileRadicado,'w');
		fputs($file1,$archivoRadicado);
		fclose($file1);
		str_replace('..','',$fileRadicado);
		$isqlRadicado = "update radicado set RADI_PATH = '$fileRadicado' where radi_nume_radi = $numeroRadicado";
		//$db->conn->debug = true;
		$rs=$db->conn->Execute($isqlRadicado);
		//print("Ha efectuado la transaccion($isql)($dependencia)");
		if (!$rs)	//Si actualizo BD correctamente 
			echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
		else{
			$radicadosSel[] = $numeroRadicado;
			$codTx = 42; //Código de la transacción
			$noRadicadoImagen = $numeroRadicado;
			$observa = "Mail(".$mailAsunto.")";
			include "$ruta_raiz/include/tx/Historico.php";
			$hist = new Historico($db);
			$hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
			include "enviarMail.php";
		}
	}
	error_reporting(7);
}
else{
 	print("No hay Correo disponible");
}
//--Variable con la Cabecera en formato html----------------------------------//
error_reporting(7);
?>

 </BODY>
</HTML>