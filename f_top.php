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

if (!isset($_SESSION['dependencia'])) {
	die(include "./sinacceso.php");
}
$ruta_raiz = ".";
require_once($ruta_raiz . "/" . "_conf/constantes.php");
require_once(ORFEOPATH . "include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$fechah = date("Ymdhms");
?>
<html>
 <head>
  <script language="JavaScript" type="text/JavaScript">
	function cerrar_session() 
	{
		if (confirm('Esta seguro de Cerrar Sesion?')) {
			fecha = <?=date("Ymdhms") ?>;
			<?php $fechah = date("Ymdhms"); ?>
			nombreventana="ventanaBorrar"+fecha;
			url="login.php?adios=chao";
			document.form_cerrar.submit();
		}
	}

	function cambContrasena() 
	{
		url = 'contraxx.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?>';
		document.form_cerrar.action=url;
		document.form_cerrar.submit();
	}
  </script>
  <script language="JavaScript" type="text/JavaScript">
   <!--
	function MM_swapImgRestore() 
	{ //v3.0
		var i,x,a=document.MM_sr;
		for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
	}
	
	function MM_preloadImages() 
	{ //v3.0
		var d=document; 
		if(d.images){ 
			if(!d.MM_p)
				d.MM_p=new Array();
			var i,j=d.MM_p.length,a=MM_preloadImages.arguments;
			for(i=0; i<a.length; i++)
				if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];
			}
		}
	}
	
	function MM_findObj(n, d) 
	{ //v4.01
		var p,i,x;  if(!d) d=document;
		if((p=n.indexOf("?"))>0&&parent.frames.length) {
			d=parent.frames[n.substring(p+1)].document;
			n=n.substring(0,p);
		}
		if(!(x=d[n])&&d.all) 
			x=d.all[n];
		for (i=0;!x&&i<d.forms.length;i++) 
			x=d.forms[i][n];
		for(i=0;!x&&d.layers&&i<d.layers.length;i++) 
			x=MM_findObj(n,d.layers[i].document);
	  	if(!x && d.getElementById) 
			x=d.getElementById(n);
		return x;
	}
	
	function MM_swapImage() 
	{ //v3.0
		var i,j=0,x,a=MM_swapImage.arguments;
		document.MM_sr=new Array;
		for(i=0;i<(a.length-2);i+=3)
		if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
	}

   //-->
  </script>
 </head>
 <body topmargin="0" leftmargin="0" bgcolor="#ffffff">
  <table width="101%" height="76"  border="0" cellpadding="0" cellspacing="0">
   <tr>
	<td width="206">
            <a href="#">
                <img name="cabezote_r1_c1" src="imagenes/logo.gif" width="206" height="76" border="0" >
            </a>
	</td>
	<td>
            <a href="#">
                <img name="cabezote_r1_c2" src="imagenes/cabezote_r1_c2.gif" width="100%" height="76" border="0">
            </a>
	</td>
	<td width="300">
            <a href="#">
             <img name="sistema-gestion" src="imagenes/sistema-gestion.gif" width="300" height="76" border="0">
            </a>
	</td>

	<td width="61">
		<a href="consultaExpedientes.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?>" target=mainFrame onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image6','','imagenes/overExp.jpg',1)">
			<img src="imagenes/Exp.jpg" name="Image6" width="61" height="76" border="0">
		</a>
	</td>

	<td width="61">
	 <a href="./Manuales/index.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?>" target=mainFrame onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image7','','imagenes/overPlantillas.jpg',1)">
	  <img src="imagenes/Plantillas.jpg" name="Image7" width="61" height="76" border="0">
	 </a>
	</td>
	<td width="60">
	 <a href="mod_datos.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd&info=false"?>" target=mainFrame onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image9','','imagenes/overInfo.gif',1)">
	  <img src="imagenes/info.gif" name="Image9" width="60" height="76" border="0">
	 </a>
	</td>
	<td width="61">
	 <a href="menu/creditos.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd&info=false"?>" target=mainFrame onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image12','','imagenes/overCreditos.gif',1)">
	  <img src="imagenes/creditos.gif" name="Image12" width="61" height="76" border="0">
	 </a>
	</td>
	<td width="61">
	 <?php
	  if($_SESSION["autentica_por_LDAP"] == 0){
	 ?>
	 <a href=javascript:cambContrasena() onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image10','','imagenes/overContrasena.gif',1)">
	  <img src="imagenes/contrasena.gif" name="Image10" width="63" height="76" border="0">
	 </a>
	 <?php 
	  }
	  else if($_SESSION["autentica_por_LDAP"] == 1) {
	 ?>
	 <a href="" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image10','','imagenes/cabezote_over_r1_c2.gif',1)">
	  <img src="imagenes/cabezote_r1_c2.gif" name="Image10" width="63" height="76" border="0">
	 </a>
	 <?php
	  }
	 ?>
	</td>
	<td width="66">
	 <a href="./estadisticas/vistaFormConsulta.php?<?=session_name()."=".trim(session_id())."&fechah=$fechah&krd=$krd"?>" target=mainFrame onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image11','','imagenes/overEstadistic.gif',1)">
	  <img src="imagenes/estadistic.gif" name="Image11" width="66" height="76" border="0">
	 </a>
	</td>
	<td width="54"><a href='#' onClick="cerrar_session();">
	 <img name="cabezote_r1_c8" src="imagenes/salir.gif" width="54" height="76" border="0" alt="">
         </a>
	</td>
   </tr>
  </table>
  <form name="form_cerrar" action="cerrar_session.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?>" target="_parent" method="post">
  </form>
 </body>
</html>
