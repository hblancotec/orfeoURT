<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "./sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

    $krdOld = $krd;
    $carpetaOld = $carpeta;
    $tipoCarpOld = $tipo_carp;
    //session_start();
    if(!$krd) $krd = $krdOsld;
    $ruta_raiz = ".";
    if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php";

    $verrad = "";
    include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    $db = new ConnectionHandler($ruta_raiz);	 
?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es requerido.\n'; }
  } if (errors) alert('Asegurese de entrar el password Correcto, \N No puede ser Vacio:\n');
  document.MM_returnValue = (errors == '');
}

function valpwdstrong() {
	var bandera = false;
	var x = document.getElementById("contradrd");
	var y = document.getElementById("contraver");
	if ( x.value == y.value ) {
		var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
	    if(strongRegex.test(x.value)) {
	    	bandera = true;
	    } else {
	        alert( 'Contrase\xf1a d\xe9bil. Debe tener:\n- Longitud m\xednima de 8 caracteres.\n- Al menos una letra may\xfascula.\n' +
	                '- Al menos una letra min\xfascula.\n- Al menos un digito.\n- Un caracter especial (&#@$%!*)');
	    }
	} else {
		alert('Contrase\xf1as diferentes.');
	}
	return bandera;
}
</script>
<title>Cambio de Contrase&ntilde;as</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="estilos/orfeo.css">
</head>
<?php 
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	
$numeroa = 0;
$numero  = 0;
$numeros = 0;
$numerot = 0;
$numerop = 0;
$numeroh = 0;

$isql = "SELECT a.*,b.DEPE_NOMB FROM USUARIO a,
                DEPENDENCIA b,
                SGD_USD_USUADEPE USD
            WHERE a.USUA_LOGIN ='$krd' AND a.USUA_ESTA=1 AND a.USUA_NUEVO=0 AND a.USUA_AUTH_LDAP=0 AND
                    a.USUA_LOGIN = USD.USUA_LOGIN AND
                    USD.SGD_USD_DEFAULT = 1 AND
                    USD.depe_codi = b.depe_codi";
$rs = $db->conn->Execute($isql);

if ($rs === false) {
    echo "En sentencia de base de datos";
    echo $db->conn->ErrorMsg();
    exit(1);
}

//echo $row["usuario"].$krd;
 echo "<font size='1' face='verdana'>";
 $contraxx = $rs->fields["USUA_PASW"];
if (trim($rs->fields["USUA_LOGIN"])==trim($krd)) {
	$dependencia = $rs->fields["DEPE_CODI"];
	$dependencianomb = $rs->fields["DEPE_NOMB"];
	$codusuario = $rs->fields["USUA_CODI"];
	$contraxx = $rs->fields["USUA_PASW"];
	$nivel = $rs->fields["CODI_NIVEL"];
	$iusuario = " and us_usuario='$krd'";
	$perrad = $rs->fields["PERM_RADI"];
	?>
	<body bgcolor="#207385">
	<CENTER>
	<IMG src='<?=$ruta_raiz?>/imagenes/logo2.gif'>
	<form action='usuarionuevo.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>' method=post onSubmit="MM_validateForm('contradrd','','R','contraver','','R');return document.MM_returnValue">
	<?php 
	 echo "<center><B><FONT color=white face='Verdana, Arial, Helvetica, sans-serif' SIZE=4 >CAMBIO DE CONTRASE&Ntilde;A USUARIOS
	 </font> </CENTER>\n";
	 echo "<P><P><center><FONT face='Verdana, Arial, Helvetica, sans-serif' SIZE=3 color=white >Por favor introduzca la nueva contrase&ntilde;a</font><p></p>\n";
	 echo "<table border=0 class='borde_tab'>\n";
	 echo "<tr ><td class='titulos2'>\n";						 
	 echo "<CENTER><input type=hidden name='usuarionew' value=$krd><B>USUARIO </td>
	 <td class=listado2>$krd</td></tr>\n";
	 echo "<td class=titulos2><center>CONTRASE&Ntilde;A </td>
	 <td class=listado2 ><input id='contradrd' type='password' name='contradrd' value='' class='tex_area'><br></td>\n";
	 echo "</tr>";
	 echo "<tr ><td class=titulos2><center>RE-ESCRIBA LA CONTRASE&Ntilde;A </td>
	 <td class=listado2><input id='contraver' type='password' name='contraver' class='tex_area' value=''></td>\n";
	 echo "</tr>";							 
	 echo "</table></p></p>\n";
	 echo "";
	 echo "";
	 echo "<center>\n";
	 $isql = "select DEPE_CODI,DEPE_NOMB from DEPENDENCIA ORDER BY DEPE_NOMB";
	 $rs = $db->conn->Execute($isql);
	 $numerot = $rs->RecordCount();
	 echo "<br><input type='submit' value='Aceptar' class='botones' >\n";  //onclick='return valpwdstrong()'
	 echo "<br><input type='hidden' value='$depsel' name='depsel'>\n";
?>	 
	 </form>
<?php
} else {
		echo "<b>No esta Autorizado para entrar </b>";
}					
?>


</body>
</html>
