<?php
 include_once '../config.php';
 include("connectPop3.php");
 require_once $PEAR_PATH."Mail\\IMAPv2.php";
 if($_SESSION['passwdEmail']) $passwdEmail=$_SESSION['passwdEmail'];
 if($_SESSION['usuaEmail']) $usuaEmail = $_SESSION['usuaEmail'];
 $usuaDoc = $_SESSION['usua_doc'];
  if($_SESSION['usuario_mail'] and !$usuaEmail) $usuario_mail=$_SESSION['usuario_mail'];
 if($usua_email) $usuario_mail = $usuaEmail;
 if($_SESSION['servidor_mail']) $servidor_mail = $_SESSION['servidor_mail'];
 if($_SESSION['puerto_mail']) $puerto_mail = $_SESSION['puerto_mail'];
 if($_SESSION['protocolo_mail']) $protocolo_mail = $_SESSION['protocolo_mail'];
 $tmpNameEmail = "tmpEmail_".$usuaDoc."_".md5(date("dmy hms")).".html";
 $_SESSION['tmpNameEmail'] = $tmpNameEmail;
 $tmpNameEmail = $_SESSION['tmpNameEmail'];
 if($_GET["passwdEmail"]) $passwdEmail = $_GET["passwdEmail"];
 if(!$_SESSION['eMailPid'])
 {
  $_SESSION['eMailAmp']=$_GET['mid'];
  $_SESSION['eMailPid']=$_GET['pid'];
  $eMailPid = $_GET['pid'];
  $eMailMid = $_GET['mid'];
  
 }else{
  $eMailPid = $_SESSION['eMailPid'];
  $eMailMid = $_SESSION['eMailMid'];
  $eMailAmp = $_SESSION['eMailAmp'];
 }
 $usuaEmail=$_SESSION['usua_email'];
 if(!$usuaEmail and $_GET["usuaEmail"]) $usuaEmail = $_GET["usuaEmail"];
 list($a,$b)=explode("@",$usuaEmail);
 $usuario_mail=$a;
 $buzon_mail = $_SESSION['buzon_mail'];
 //$connection = "$protocolo_mail://$usuario_mail:$passwdEmail@$servidor_mail:$puerto_mail/$buzon_mail#$opciones_mail";
 $connection = "pop3s://$usuario_mail:$passwdEmail@$servidor_mail:995/INBOX#novalidate-cert";
 //echo "<hr>--->".$connection;
  $msg = new Mail_IMAPv2();
 
//$msgMng = new Mail_IMAPv2_ManageMB($connection);
 // Open up a mail connection
 // echo $connection;
 if (!$msg->connect($connection,false)) 
 {
  echo "<span style='font-weight: bold;'>Error:</span> No se pudo realizar la conexion al serv. de correo.";
 }
//print_r($msg);
?>