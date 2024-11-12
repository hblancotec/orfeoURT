<?php
session_start();
$ruta_raiz = ".";
if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php";
$krd=$_SESSION['krd'];
$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
if(!$krd) $krd=$krdOld;
include_once  "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);	
?>
<html>
<title>Adm - Contrase&ntilde;as - ORFEO 3 </title>
<HEAD>
<link rel="stylesheet" href="estilos/orfeo.css">
</HEAD>
	<body bgcolor="#207385">
	<CENTER>
	<a href="<?=$ruta_raiz?>/login.php">
	<img border="0" src="<?=$ruta_raiz?>/imagenes/logo2.gif">
	</a>
<?php
$depsel = isset($_POST['depsel']) ? $_POST['depsel'] : $_SESSION['dependencia'];
if(isset($_POST['aceptar']) && $_POST['aceptar']=="grabar") {
    $isql = "update usuario set usua_nuevo='1', usua_pasw='" . substr(md5($_POST['contraver']),1,26) . "', " .
            "USUA_SESION='CAMBIO PWD(".date("Ymd")."' where usua_login='".$_POST['usuarionew']."'";
	$rs = $db->conn->Execute($isql);
	if($rs==-1) {
		echo "<P><P><center><font color=white>No se ha podido cambiar la contrase&ntilde;a, Verifique los datoe e intente de nuevo</center>";
	} else {
		echo "<center><font color=white>Su contrase&ntilde;a ha sido cambiada correctamente</center><p>";
		session_destroy();
	}
} else {
   if($_POST['contradrd']==$_POST['contraver']) {
    ?>
	</p>
   <form action=usuarionuevo.php?krd=<?=$krd?>&<?=session_name()?>=<?=session_id()?>" method='post'><CENTER>
   <table border = 0>
		<tr><td ><center><FONT color=white face='Verdana, Arial, Helvetica, sans-serif' SIZE=4 ><B>CONFIRMAR DATOS</font>  </td></tr>
	</TABLE>
	<BR>
	<table border = 0 class='borde_tab'>
		 
		<tr><td class=titulos2><center>Usuario <?=$_POST['usuarionew'] ?></td></tr>
		 <input type='hidden' id='usuarionew' name='usuarionew' value='<?=$_POST['usuarionew']?>'></td></tr>
		 <input type='hidden' id='contraver' name='contraver' value='<?=$_POST['contraver']?>'></td></tr>
		 <input type='hidden' name='depsel' value='<?=$_POST['depsel']?>'></td></tr>		 		 
		<tr><td class='titulos2'><center><center>Dependencia <?=$_POST['depsel']?> </td></tr>
		<tr><td class='titulos2'><center><center>Esta Seguro de estos datos ? </td></tr>
	 </table>
		<input type=submit value='grabar' name='aceptar' class='botones'> 
	 </form>
<?php
	 } else {
?>
	  <span class="alarmas">
	   <center></p>CONTRASE&Ntilde;AS NO COINCIDEN </P><b>Por favor oprima  atras y repita la operaci&oacute;n<b></P>
	 	 <CENTER><B>GRACIAS</span>
<?php
		}
	}
?>
</body>
</html>