<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz = "../..";
if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad'] or !$_SESSION['codusuario']) include "$ruta_raiz/rec_session.php";
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	$db = new ConnectionHandler("$ruta_raiz");
	//$db->conn->debug = true;
	error_reporting(0);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$sqlFechaHoy=$db->conn->DBTimeStamp(time());
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.Estilo1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
-->
</style>
</head>
<SCRIPT LANGUAGE="JavaScript" SRC="CalendarPopup.js"></SCRIPT>
<body style="background-color:#FFFFFF">
<?php
$encabezado = "krd=$krd&usModo=$usModo&nivel=$nivel&dep_sel=$dep_sel&perfil=$perfil&cedula=$cedula&usuLogin=$usuLogin&nombre=$nombre&entrada=$entrada&dia=$dia&mes=$mes&ano=$ano&ubicacion=$ubicacion&piso=$piso&extension=$extension&email=$email";
?>
<center>
<form name="frmPermisos" action='grabar.php?<?=session_name()."=".session_id()."&$encabezado"?>' method="post">
<tr>
<td>
<table border=1 width=80% class=t_bordeGris>
	<tr>
    <td colspan="2">
	<center>
	<p><B><span class=etexto>ADMINISTRACI&Oacute;N DE USUARIOS Y PERFILES</span></B> </p>
	<p><B><span class=etexto>Asignacion de Permisos</span></B> </p></center>
	</td>
  	</tr>
<?php
	if ($usModo ==2) {
	} else {
		$usua_activo = 1;
		$usua_nuevo = 0;
	}
?>
  	<tr>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="entrada" value="$entrada" <?php if ($entrada) echo "checked"; else echo "";?>>
      Radicaci&oacute;n de Entrada</td>
    <td align="left" class="vbmenu_control"> <input name="modificaciones" type="checkbox" value="$modificaciones" <?php if ($modificaciones) echo "checked"; else echo "";?>>
      Modificaciones</td>
	</tr>

  	<tr>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="salida" value="$salida" <?php if ($salida)  echo "checked"; else echo "";?>>
      Radicaci&oacute;n de Salida </td>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="masiva" value="$masiva" <?php if ($masiva) echo "checked"; else echo "";?>>
      Radicacion Masiva</td>
	</tr>

	<tr>
    <td align="left" class="vbmenu_control"><input type="checkbox" name="impresion" value="$impresion" <?php if ($impresion) echo "checked"; else echo "";?>>
      Impresion </td>
    <td align="left">&nbsp;</td>
  	</tr>

  	<tr>
    <td align="left" class="vbmenu_control"><input type="checkbox" name="exp_temas" value="$exp_temas" <?php if ($exp_temas) echo "checked"; else echo "";?>>
      Temas Expedientes (permisos.php) </td>
    <td align="left">&nbsp;</td>
  	</tr>
	
	<!--inicio Acciones masivas-->
	<tr>
    <td align="left" class="vbmenu_control"><input type="checkbox" name="accMasiva_trd" 		value="$accMasiva_trd" <?php if ($accMasiva_trd) echo "checked"; else echo "";?>>
      Masiva TRD* </td>
    <td align="left">&nbsp;</td>
  	</tr>
	<tr>
    <td align="left" class="vbmenu_control"><input type="checkbox" name="accMasiva_incluir" 	value="$accMasiva_incluir" <?php if ($accMasiva_incluir) echo "checked"; else echo "";?>>
      Masiva Incluir </td>
    <td align="left">&nbsp;</td>
  	</tr>
	<tr>
    <td align="left" class="vbmenu_control"><input type="checkbox" name="accMasiva_prestamo" 	value="$accMasiva_prestamo" <?php if ($accMasiva_prestamo) echo "checked"; else echo "";?>>
      Masiva Prestamo </td>
    <td align="left">&nbsp;</td>
  	</tr>
	<tr>
    <td align="left" class="vbmenu_control"><input type="checkbox" name="accMasiva_temas" 		value="$accMasiva_temas" <?php if ($accMasiva_temas) echo "checked"; else echo "";?>>
      Masiva Temas </td>
    <td align="left">&nbsp;</td>
  	</tr>
	<!--Fin Acciones masivas-->
	
	<tr>
    <td align="left" class="vbmenu_control"><input type="checkbox" name="ccalarmas" value="$ccalarmas" <?php if ($ccalarmas) echo "checked"; else echo "";?>>
      Alerta con copia c.c (permisos.php) </td>
    <td align="left">&nbsp;</td>
  	</tr>

	<tr>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="memorandos" value="$memorandos" <?php if ($memorandos) echo "checked"; else echo "";?>>
      Radicaci&oacute;n Memorandos</td>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="resoluciones" value="$resoluciones" <?php if ($resoluciones) echo "checked"; else echo "";?>>
      Radicacion Resoluciones</td>
  	</tr>

	<tr>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="s_anulaciones" value="$s_anulaciones" <?php if ($s_anulaciones) echo "checked"; else echo "";?>>
      Solicitud de Anulaciones</td>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="anulaciones" value="$anulaciones" <?php if ($anulaciones) echo "checked"; else echo "";?>>
      Anulaciones</td>
	</tr>

  	<tr>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="adm_archivo" value="$adm_archivo" <?php if ($adm_archivo) echo "checked"; else echo "";?>>
      Administrador de Archivo</td>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="dev_correo" value="$dev_correo" <?php if ($dev_correo) echo "checked"; else echo "";?>>
      Devoluciones de Correo</td>
  	</tr>

  	<tr>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="adm_sistema" value="$adm_sistema" <?php if ($adm_sistema) echo "checked"; else echo "";?>>
      Administrador del Sistema</td>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="env_correo" value="$env_correo" <?php if ($env_correo) echo "checked"; else echo "";?>>
      Envios de Correo</td>
  	</tr>
</table>
</td>
</tr>

<tr>
<td>
<table border=1 width=80% class=t_bordeGris>
	<tr>
    <td width="50%" align="left" class="vbmenu_control"> <input type="checkbox" name="usua_activo" value="$usua_activo" <?php if ($usua_activo == 1) echo "checked"; else echo "";?>>
      Usuario Activo</td>
    <td align="left" class="vbmenu_control">Nivel de Seguridad</td>
  	</tr>

  	<tr>
    <td align="left" class="vbmenu_control"> <input type="checkbox" name="usua_nuevo" value="$usua_nuevo" <?php if ($usua_nuevo != 1) echo "checked"; else echo "";?>>
      Usuario Nuevo</td>
    <td align="left" class="vbmenu_control">
	<?php
	$contador = 1;
	while($contador <= 5)
	{
		echo "<input name='nivel' type='radio' value=$contador ";
		if ($rs->fields["CODI_NIVEL"] == $contador) echo "checked"; else echo "";
		echo " >".$contador;
		$contador = $contador + 1;
	}
?>
	</td>
  	</tr>
    <td colspan="2"><div align="center">
		<input name="login" type="hidden" value='<?=$usuLogin?>'>
          <input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
          <input name="krd" type="hidden" value='<?=$krd?>'>
          <input name="cedula" type="hidden" value='<?=$cedula?>'>
          <input type="submit" name="Submit3" value="Grabar">
          <input type="reset" name="Submit4" value="Cancelar">
    </div></td>
</table>
</td>
</tr>

 </form>

</body>
</html>
