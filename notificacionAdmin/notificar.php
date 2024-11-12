<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

if ($_SESSION['usua_perm_notifAdmin'] != 1) {
    die(include "../sinpermiso.php");
    exit();
}

$ruta_raiz = "..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$db->conn->debug = false;

// ## SE VALIDA SI SELLECIONO UN REGISTRO
if ($checkValue) {
    $num = count($checkValue);
    if ($num == 1) {
        $rad = key($checkValue);
    }
    
    $sql = "SELECT	R.RADI_NUME_RADI AS RADICADO,
						R.RADI_PATH AS PATH,
						D.SGD_DIR_NOMREMDES AS DESTINATARIO,
						D.SGD_DIR_DOC AS IDENTIFICACION,
						R.RADI_NOTIF_FIJACION AS FECHA_FIJACION,
						R.RADI_NOTIF_DESFIJACION AS FECHA_DESFIJACION
				FROM	RADICADO R
						JOIN SGD_DIR_DRECCIONES D ON
							D.RADI_NUME_RADI = R.RADI_NUME_RADI AND
							D.SGD_DIR_TIPO = 1
				WHERE	R.RADI_NUME_RADI =" . $rad;
    $rs = $db->conn->Execute($sql);
    
    $img = false;
    $ext = substr($rs->fields['PATH'], - 3);
    $nom = $rs->fields['DESTINATARIO'];
    $doc = trim($rs->fields['IDENTIFICACION']);
    $fecha_ini = substr($rs->fields['FECHA_FIJACION'], 0, 10);
    $fecha_fin = substr($rs->fields['FECHA_DESFIJACION'], 0, 10);
    
    if ($ext == 'pdf' or $ext == 'tif') {
        $img = true;
    }
} else {
    echo 'Por favor regrese a la pagina anterior y seleccione un solo registro';
}
// ##########################################################################

if ($img) {
?>
<html>
<head>
<title>Notificar Radicado</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body>
	<div id="spiffycalendar" class="text"></div>
	<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
	<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
	<script language="javascript">
					window.onload;

					var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formulario", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
					var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formulario", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);

					var img = "<?php echo $img;?>";

					function cancelar() 
					{
						window.location.href = "index.php?<?=session_name().'='.session_id().'&krd=$krd'?>";
					}

					function validar() 
					{
						if (!img){
							alert("El radicado seleccionado no tiene imagen y no puede continuar con esta acción");
						}

						else if (document.formulario.txtNom.value == ''){
							alert("El nombre del notificado es obligatorio");
							document.formulario.file.focus();
						}

						else if (document.formulario.txtDoc.value == ''){
							alert("El documento de identificacion es obligatorio");
							document.formulario.file.focus();
						}

						else if (document.formulario.txtObs.value == ''){
							alert("La observacion es obligatoria");
							document.formulario.file.focus();
						}

						else if (!(document.getElementById('fecha_ini').value)){
							alert("La fecha de fijación es obligatoria");
							document.formulario.file.focus();
						}

						else if (!(document.getElementById('fecha_fin').value)){
							alert("La fecha de des-fijación es obligatoria");
							document.formulario.file.focus();
						}

						else if (document.formulario.file.value.length == 0){
							alert("Debe seleccionar una imagen de notificación");
							document.formulario.file.focus();
						}

						else {
							document.formulario.submit()
						}
					}
				</script>

	<form name=formulario method="post" action="notificaTx.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>" enctype="multipart/form-data">
		<br>
		<br>
		<table align="center" width="30%" cellspacing="8" class="borde_tab">
			<tr>
				<td class="titulos4" colspan=2 align="center">Radicado No. <?php echo $rad;?></td>
			</tr>
			<tr>
				<td width="35%" style="font-size: 12" class="listado2">Nombre:</td>
				<td width="65%">
					<input type="text" name="txtNom" size="33" class="tex_area" value="<?=$nom?>"></td>
			</tr>
			<tr>
				<td width="35%" style="font-size: 12" class="listado2">No. Identificaci&oacute;n:</td>
				<td width="65%"><input type="text" name="txtDoc" size="20" class="tex_area" value="<?=$doc?>"></td>
			</tr>
			<tr>
				<td width="35%" style="font-size: 12" class="listado2">Fecha Fijaci&oacute;n:</td>
				<td width="65%">
					<script language="javascript">
					dateAvailable.writeControl();
					dateAvailable.dateFormat="yyyy/MM/dd";
					</script>
				</td>
			</tr>
			<tr>
				<td width="35%" style="font-size: 12" class="listado2">Fecha Desfijaci&oacute;n:</td>
				<td width="65%">
					<script language="javascript">
					dateAvailable2.writeControl();
					dateAvailable2.dateFormat="yyyy/MM/dd";
					</script>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-size: 12" class="listado2">Observaci&oacute;n:</td>
			</tr>
			<tr>
				<td colspan="2" style="font-size: 13"><textarea name="txtObs" cols="45" rows="4"></textarea></td>
			</tr>
			<tr>
				<td colspan="2"><input type="file" name="file" size="47"></td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input class="botones" type="button" name="cancelar" value="cancelar" onclick="cancelar();">
					&nbsp;&nbsp; 
					<input class="botones" type="button" name="notificar" value="Notificar" onclick="validar();"></td>
			</tr>
			<tr>
				<td align="center" colspan="2" style="font-size: 10">* Todos los campos son obligatorios *</td>
			</tr>
<?php
    if (! $img) {
?>
			<tr>
				<td align="center" colspan="2" style="font-size: 12; color: red">El
					radicado seleccionado no tiene imagen .tif ó .pdf y no podra
					continuar con esta acción
				</td>
			</tr>
<?php
    }
?>
			</table>
<?php
    echo "<input type='hidden' name='rad' value='$rad'>";
    echo "<input type='hidden' name='nomHid' value='$nom'>";
    echo "<input type='hidden' name='docHid' value='$doc'>";
    echo "<input type='hidden' name='fijHid' value='$fecha_ini'>";
    echo "<input type='hidden' name='desHid' value='$fecha_fin'>";
?>
	</form>
</body>
</html>
<?php
} else {
    header("Location: index.php?session_name()=session_id()&krd=$krd&sin=9");
    die();
}
?>