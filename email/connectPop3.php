<?php
 include_once '../config.php';
 	if (!$_SESSION['dependencia']) {
	 include ("../rec_session.php");
	 }
 if($_SESSION['passwdEmail']) $passwdEmail=$_SESSION['passwdEmail'];
 if($_SESSION['usuaEmail']) $usuaEmail = $_SESSION['usuaEmail'];
 //$usuaEmail = $_SESSION['usua_email'];
 if($_POST["passwd_mail"]){
   $_SESSION['passwdEmail'] = $_POST["passwd_mail"];
   $passwdEmail = $_POST["passwd_mail"];
 }elseIF($_SESSION["passwdEmail"]){
  $passwdEmail = $_SESSION['passwdEmail'];
 }
 
  $usuaDoc = $_SESSION['usua_doc'];
 if($_SESSION['usua_email']) $usuaEmail=$_SESSION['usua_email'];
 
 if($_SESSION['servidor_mail']) $servidor_mail = $_SESSION['servidor_mail'];
 if(!$_SESSION['servidor_mail'] && $servidor_mail) $_SESSION['servidor_mail'] = $servidor_mail;
  if($_SESSION['puerto_mail']) $puerto_mail = $_SESSION['puerto_mail'];
  if(!$_SESSION['puerto_mail'] && $servidor_mail) $_SESSION['puerto_mail'] = $puerto_mail;
 if($_SESSION['protocolo_mail']) $protocolo_mail = $_SESSION['protocolo_mail'];
 if(!$_SESSION['protocolo_mail'] && $servidor_mail) $_SESSION['protocolo_mail'] = $protocolo_mail;
  if(!$usuaEmail and $_GET["usuaEmail"]) $usuaEmail = $_GET["usuaEmail"];
 
 //$usuaEmail = "jlosada@dnp.gov.co";
 list($a,$b)=explode("@",$usuaEmail);
 $usuaEmail1=$a;
//require('includes/mime_parser.php');
//require('includes/rfc822_addresses.php');
require("pop3.php");
$servidor_mail = "ilpostimo.dnp.ad";
$puerto_mail = "995";
/* Uncomment when using SASL authentication mechanisms */
/*
require("sasl.php");
*/ 

stream_wrapper_register('pop3', 'pop3_stream');  /* Register the pop3 stream handler class */
 //$usuaEmail1 = "jlosada@dnp.gov.co";
$pop3=new pop3_class;
$pop3->hostname=$servidor_mail;          /* POP 3 server host name                      */
$pop3->port=$puerto_mail;                /* POP 3 server host port,
                                            usually 110 but some servers use other ports
                                            Gmail uses 995                              */
$pop3->tls=1;                            /* Establish secure connections using TLS      */
$user=$usuaEmail1;                       /* Authentication user name                    */
$password=$passwdEmail;                  /* Authentication password                     */
$pop3->realm="";                         /* Authentication realm or domain              */
$pop3->workstation="";                   /* Workstation for NTLM authentication         */
$apop=0;                                 /* Use APOP authentication                     */
$pop3->authentication_mechanism="USER";  /* SASL authentication mechanism               */
$pop3->debug=0;                          /* Output debug information                    */
$pop3->html_debug=0;                     /* Debug information is in HTML                */
$pop3->join_continuation_header_lines=1; /* Concatenate headers split in multiple lines */
$pop3->Open();

//echo "$user,$password,$apop,$passwdEmail,$usuaEmail1,$servidor_mail, $puerto_mail";
//$user = 'jlosada';

$connect = $pop3->Login($user,$password,$apop);
?>
 