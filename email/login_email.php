<?php
session_start();
/**
 * Funcion de Radicacion de correos Electronicos en Orfeo
 * @autor Jairo Losada htttp://www.correlibre.org 01/2009  Modificado de ejempolo e Imap2 de pear.php.net
 * @licencia GNU/GPL v3
 *
 **/
extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);
$ruta_raiz = "..";
if(!isset($_SESSION['dependencia']))	include "../rec_session.php";
extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);
$usuaEmail =  $_SESSION["usua_email"];
$krd =  $_SESSION["krd"];
$dependencia =  $_SESSION["dependencia"];
$encabezado = session_name()."=".session_id()."";
if($passwd_mail) {
	$passwdEmail=$passwd_mail;
	$dominioEmail=$_SESSION['dominioEmail']; 
}
if($_SESSION['passwdEmail'])
{
 $passwdEmail =$_SESSION['passwdEmail'];
 
}
?>
<html>
<head>
<title>..Vista Previa..</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
</head>
<body>

<h2 align="center">
<?php
if($err==1)
echo "No se pudo establecer coneccion con el Servidor."; 
?>
</h2>
<table border="1"  align="center" background="../imagenes/orfeopasswd.jpg">
<tr><td width="360">
<form action="browse_mailbox.php?PHPSESSID=<?=session_id()?>" METHOD=POST>
<table width="350" border="0" align="center">
        <tr> 
          <td colspan="2" align="right"><font color="#FFFFFF">Ingrese su Clave de Correo para: <br><?=$usuaEmail?><br></font></td>
        </tr>
        <tr> 
          <td width="182" align="center" ><p>&nbsp; </p>
            <p>&nbsp; </p></td>
          <td width="144" align="center" ><input type="password" name="passwd_mail" /></td>
        </tr>
        <tr> 
          <td colspan="2" align="center" ><input name="Submit" type="submit" class="botones" value="INGRESAR"></td>
        </tr>
      </table>
</td></tr>

</table>
</form>
</body>
</html>

