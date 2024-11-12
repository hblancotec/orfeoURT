<?php
session_start();
$ruta_raiz = "../..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_FILES, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
include_once $ruta_raiz."/include/db/ConnectionHandler.php";
require_once $ruta_raiz."/class_control/CombinaError.php";
if(!isset($_SESSION['dependencia']))
	include "$ruta_raiz/rec_session.php";
(!$db) ? $conexion = new ConnectionHandler($ruta_raiz) : $conexion = $db;
//$conexion->conn->debug = true;
$conexion->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$hora=date("H")."_".date("i")."_".date("s");
// var que almacena el dia de la fecha
$ddate=date('d');
// var que almacena el mes de la fecha
$mdate=date('m');
// var que almacena el ano de la fecha
$adate=date('Y');
// var que almacena  la fecha formateada
$fecha=$adate."_".$mdate."_".$ddate;
/*//Almacena la extesion del archivo entrante
$extension = trim(substr($archivoPlantilla_name,strpos($archivoPlantilla_name,".")+1,strlen($archivoPlantilla_name)-strpos($archivoPlantilla_name,".")));
//var que almacena el nombre que tendr� la pantilla
$arcPlantilla=$usua_doc."_".$fecha."_".$hora.".$extension";
*/
//var que almacena el nombre que tendra el CSV
$arcCsv=$usua_doc."_".$fecha."_".$hora.".csv";
//var que almacena el path hacia el PDF final
$arcPDF="$ruta_raiz/bodega/masiva/"."tmp_".$usua_doc."_".$fecha."_".$hora.".pdf";
$phpsession = session_name()."=".session_id();
//var que almacena los parametros de sesion
$params=$phpsession."&krd=$krd&dependencia=$dependencia&codiTRD=$codiTRD&depe_codi_territorial=$depe_codi_territorial&usua_nomb=$usua_nomb&tipo=$tipo&"
				."depe_nomb=$depe_nomb&usua_doc=$usua_doc&codusuario=$codusuario";


 //Funcion que calcula el tiempo transcurrido
 function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
?>
<html>
<head>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../estilos/orfeo.css">
<script>
/**
* Confirma la generacion definitiva
*/
function enviar() {
	if ( confirm ('Confirma la generacion de un radicado por cada registro del archivo CSV?'))
		document.formDefinitivo.submit();
}

function regresar() {
	document.formDefinitivo.action="menu_masiva.php?"+'<?=$params?>';
	document.formDefinitivo.submit();
}

/**
 * Envia el formulario, a consultar divipola
*/
function divipola() {
	document.formDefinitivo.action="consulta_depmuni.php?"+ document.formDefinitivo.params.value;
	document.formDefinitivo.submit();
}

/**
* Cancela el proceso y devuelve el control a menu masiva
*/
function cancelar(){
	document.formDefinitivo.action='menu_masiva.php?'+ document.formDefinitivo.params.value;
	document.formDefinitivo.submit();
}

function abrirArchivoaux(url){
	nombreventana='Documento';
	window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
	return;
}
</script>
</head>
<body>
<form action="adjuntar_defintExcel.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formDefinitivo">
	<input type=hidden name=pNodo value='<?=$pNodo?>'>
	<input type=hidden name=codProceso value='<?=$codProceso?>'>
	<input type=hidden name=tipoRad value='<?=$tipoRad?>'>
<?php
$time_start = microtime_float();

if ($archivoPlantilla['size'] >= 10000000 ) {	
	echo "el tama&nacute;o de los archivos no es correcto. <br><br><table><tr><td><li>se permiten archivos de 100 Kb m&aacute;ximo.</td></tr></table>";
}
else {
	if( !copy( $archivoPlantilla['tmp_name'], BODEGAPATH."masiva/".$arcCsv ) ) {
		echo "<br>error al copiar los archivos";
	} else {
		error_reporting(7);
		echo "<center><span class=etextomenu align=left>";
		echo "<TABLE border=0 width 60% cellpadding='0' cellspacing='5' class='borde_tab'>
		<TR ALIGN=LEFT><TD width=20% class='titulos2' >DEPENDENCIA :</td><td class='listado2'> ".$_SESSION['depe_nomb']."</TD>	<TR ALIGN=LEFT><TD class='titulos2' >USUARIO RESPONSABLE :</td><td class='listado2'>".$_SESSION['usua_nomb']."</TD>
		<TR ALIGN=LEFT><TD class='titulos2' >FECHA :</td><td class='listado2'>" . date("d-m-Y - h:mi:s") ."</TD></TR></TABLE>";
		require "$ruta_raiz/jhrtf/jhrtfExcel.php";
		$ano = date("Y") ;
		
		$ruta_raiz = "../..";
		$definitivo="si";

		// Se crea el objeto de masiva
		$masiva = new jhrtf($arcCsv,$ruta_raiz,$arcPDF,$conexion);
		$masiva->cargar_csv();
		$masiva->validarArchs();

		if ($masiva->hayError()) {	
			$masiva->mostrarError();
		} else {
            $masiva->setTipoDocto($tipo);
            $_SESSION["masiva"]=$masiva;
		 	
		 	$masiva->combinar_csv($dependencia,$codusuario,$usua_doc,$usua_nomb,$depe_codi_territorial,$codiTRD,$tipoRad);

			error_reporting(0);
			include "$ruta_raiz/config.php";

			//El include del servlet hace que se altere el valor de la variable  $estadoTransaccion como 0 si se pudo procesar el documento, -1 de lo
			// contrario
			$estadoTransaccion=-1;

			echo ("<br>$archInsumo");

			if ( !file_exists("$ruta_raiz/bodega/masiva/$archInsumo.ok")) {	
				$objError = new CombinaError (NO_DEFINIDO);
				echo ($objError->getMessage());
				die;
			}
			else {	
				echo  "<center><span class=info><br>Se llev&oacute; a cabo la radicaci&oacute;n masiva.<br> ";
				echo "<span class='info'>";
		   		echo "<BR><a class='vinculos' href=javascript:abrirArchivoaux('$arcPDF')> Abrir Listado</a>";
		   		echo "</span>";
			}
		}
	}
	//Contabilizamos tiempo final
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	echo "<br><b>Se demor&oacute;: $time segundos la Operaci&oacute;n total.</b>";
}
?>
	<input name='archivo' type='hidden' value='<?=$archivoFinal?>'>
	<input name='arcPDF' type='hidden'  value='<?=$arcPDF ?>'>
	<input name='tipoRad' type='hidden' value='<?=$tipoRad?>'>
	<input name='pNodo' type='hidden' value='<?=$pNodo?>'>
	<input name='params' type='hidden'  value="<?=$params?>">
	<input name='archInsumo' type='hidden'  value="<?=$archInsumo?>">
	<input name='extension' type='hidden'  value="<?=$extension?>">
	<input name='arcPlantilla' type='hidden' value='<?=$arcPlantilla?>'>
	</form>
</body>
</html>