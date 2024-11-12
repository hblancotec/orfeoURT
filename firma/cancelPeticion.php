<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

if (!$dependencia || !$usua_doc )
    include "../rec_session.php";

if (! $ruta_raiz)
    $ruta_raiz = "..";

include_once $ruta_raiz."/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");

$idcfirmante = isset($_GET['idcfirmante']) ? $_GET['idcfirmante'] : $_POST['idcfirmante'];
$idcfirmante = explode(";", $idcfirmante);
if (count($idcfirmante) > 0) {
    $idcfirmante = $idcfirmante[0];
}
$msgSalida="";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require $ruta_raiz.'/class_control/firmaRadicado.php';
    $objFirma = new firmaRadicado($db);
    $res = $objFirma->solicitudModificacionCiclo($idcfirmante, $_SESSION['usua_doc'], $_SESSION['login'], $_POST['slsMotivoCancel'], $_POST['txtObservacion']);
    $valObservacion = $_POST['txtObservacion'];
    $msgSalida = $res['mensaje']; 
}
//Si soy solicitante agregaré una opción al combo
$opcEvaluador = "";
$sql="select * from sgd_ciclofirmadomaster where idcf=".$idcfirmante." and usua_doc='".$_SESSION['usua_doc']."' and usua_login='".$_SESSION['login']."'";
$soyfirmante = $db->conn->Execute($sql);
if ($soyfirmante && !$soyfirmante->EOF) {
    $opcEvaluador = "<option value='3'>Soy solicitante y socializo modificaciones.</option><option value='4'>Soy solicitante y cancelo ciclo de firmado.</option>";
}

$encabezado = session_name() . "=" . session_id();
?>
<html>
<head>
<title>Solicitar Cambios Plantilla.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
<script src="./js/jquery-1.12.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

    var max_chars = 1000;

    $('#max').html(max_chars);

    $('#txtObservacion').keyup(function() {
        var chars = $(this).val().length;
        var diff = max_chars - chars;
        $('#contador').html(diff);   
    });
});
</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
<form method="POST" name='frmCancelSign' action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?'.$encabezado?>'>
<table align="center" class="borde_tab" cellspacing="0">
<tr class="titulos2"><th colspan="2" height="15" class="titulos2">Solicitar cambios</th></tr>
<tr>
	<td class="titulos5">Raz&oacute;n</td>
	<td class="titulos5">
		<select id='slsMotivoCancel' name='slsMotivoCancel' required>
			<option value=''>&lt;&lt; Seleccione &gt;&gt;</option>
			<option value='1' <?php echo ($_POST['slsMotivoCancel']=='1')? ' selected':'';?>>Aparezco como firmante y no soy firmante</option>
			<option value='2'<?php echo ($_POST['slsMotivoCancel']=='2')? ' selected':'';?>>Soy firmante y deseo hacer observaciones</option>
			<?php echo $opcEvaluador;?>
		</select>
	</td>
</tr>
<tr>
	<td class='titulos5'>Observaci&oacute;n</td>
	<td class='titulos5'>
		<textarea rows="4" cols="35" id='txtObservacion' name='txtObservacion' maxlength='1000'><?php echo $valObservacion; ?></textarea>
		<div id='contador'></div>
	</td>
</tr>
<tr>
	<td colspan='2' class='titulos5'>
	<?php echo $msgSalida; ?>
	<input type="hidden" value="<?php echo $idcfirmante; ?>" name='idcfirmante'>
	</td>
</tr>
<tr>
	<td class='listado5' colspan='2'>
	<center><input type="submit" value='Actualizar' name='btnActualizar' class='botones' />
	&nbsp;&nbsp;
	<input type="button" value='Cerrar' class='botones' name='btnCerrar' onClick="opener.regresar();window.close();" />
	</center>
	</td></tr>
</table>
</form>
</body>
</html>