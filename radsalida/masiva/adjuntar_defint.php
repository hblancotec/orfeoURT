<?php
/**
 * Programa controlador que coordina la combinacion de correspondencia definitiva, una vez se ha generado la pruena desde  adjuntar_masiva.php
 * @author      Sixto Angel Pinz�n
 * @version     1.0
 */
set_time_limit(0);
$tipoOld = $tipo;
session_id($PHPSESSID);
session_start($PHPSESSID);
if (!$tipo) $tipo = $tipoOld;
$ruta_raiz = "../..";
require_once "$ruta_raiz/jhrtf/jhrtf.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once("$ruta_raiz/class_control/CombinaError.php");
$conexion = new ConnectionHandler($ruta_raiz);
$conexion->conn->StartTrans();
//$conexion->conn->debug = true;
$conexion->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//Si no llega la dependencia recupera la sesion
if(!$_SESSION['dependencia'])
	include "$ruta_raiz/rec_session.php";
//variable con elementos de sesion
$phpsession = session_name()."=".session_id();
//$conexion->conn->debug=true;
//variable con elementos de sesion
$params=$phpsession."&krd=$krd&codiTRD=$codiTRD&dependencia=$dependencia&depe_codi_territorial=$depe_codi_territorial&usua_nomb=$usua_nomb&depe_nomb=$depe_nomb&tipo=$tipo";
//print ("luego de sesion");
//$debug = true;
//Función que calcula el tiempo transcurrido
 function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../estilos/orfeo.css">
<title>Prueba Masiva</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
function regresar()
{
   document.formDefinitivo.action="menu_masiva.php?"+'<?=$params?>';
   document.formDefinitivo.submit();
}
function abrirArchivoaux(url){
			       nombreventana='Documento';
			       window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
			       return; }
</script>
</head>
<body>
<form action="adjuntar_defint.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formDefinitivo">
<input type="hidden" name="pNodo" value='<?=$pNodo?>'>
<input type="hidden" name="codProceso" value='<?=$codProceso?>'>
<input type="hidden" name="tipoRad" value='<?=$tipoRad?>'>
<?php
  echo "<center><span class='etextomenu' align='left'>";
  echo "<table border='0' width='60%' cellpadding='0' cellspacing='5' class='borde_tab'>
        <tr align='left'><td width='20%' class='titulos2'>DEPENDENCIA :</td><td class='listado2'>".$_SESSION['depe_nomb']."</td>
        <tr align='left'><td class='titulos2'>USUARIO RESPONSABLE :</td><td class='listado2'>".$_SESSION['usua_nomb']."</td>
        <tr ALIGN='left'><td class='titulos2'>FECHA :</td><td class='listado2'>" . date("d-m-Y - h:mi:s") ."</td></tr>
        </table>";
?>
<center>
<span class="info">Se ha realizado la combinaci&oacute;n de correspondencia DEFINITIVA</span><BR>
</center>
<?php

$time_start = microtime_float();
//Verifica que el objeto de combinacion masiva se encuentre en la sesion, refrenciado como 'masiva'
$masiva = new jhrtf($archInsumo,$ruta_raiz,$arcPDF,$conexion);
$masiva->cargar_csv();
$masiva->validarArchs();

if(!$masiva)
{
   echo("ERROR ! NO LLEGA LA INFORMACION DE RADICACION MASIVA");
}
else
{
   $hora=date("H")."_".date("i")."_".date("s");
   $ddate=date('d');
   $mdate=date('m');
   $adate=date('Y');
   $fecha=$adate."_".$mdate."_".$ddate;
   $ano = date("Y") ;

   $masiva->setConexion($conexion);
   $masiva->setDefinitivo("si");

   error_reporting(7);
   echo "<span class='leidos'>";
   $masiva->codProceso=$codProceso;
   list ($masiva->codFlujo, $masiva->codArista) = explode('-', $pNodo);
   $masiva->tipoDocto=$tipo;

   $nombrebase    = ORFEOCFG . "\\bodega\\masiva\\" . $usua_doc;
   $rutaPlantilla = ORFEOCFG . "\\bodega\\masiva\\" . $masiva->arcPlantilla;
   $rutabase      = $usua_doc."_".$fecha."_".$hora . ".zip";
   $rutaZipFile   = ORFEOCFG . "\\bodega\\" . $ano . "\\" . $dependencia . "\\docs\\" . $rutabase;

   // nombre de archivo de salida
   $nombrebaseF   = ORFEOCFG . "\\bodega\\" . $ano . "\\" . $dependencia . "\\docs\\" ;
   $nombreF       = $fecha . "_" . $hora . "_final" . ".rtf";
   $archivoFinalF = $nombrebaseF . $nombreF;

   $linkfile = "../../bodega/" . $ano . "/" . $dependencia . "/docs/" . $nombreF;
   $masiva->linkfile = $linkfile;

   $masiva->arcVincular =  "/../" . $ano . "/" . $dependencia . "/docs/" . $nombreF;
   $masiva->combinar_csv($dependencia, $codusuario, $usua_doc, $usua_nomb, $depe_codi_territorial, $codiTRD, $tipoRad);
   $archInsumo = $masiva->getInsumo();
   include("$ruta_raiz/config.php");
   //El include del servlet hace que se altere el valor de la variable  $estadoTransaccion como 0 si se pudo procesar el documento,
   // -1 de lo contrario
   //$estadoTransaccion=-1;
   $estadoTransaccion = 0;

   $masiva->generaOfficeFiles($nombrebase, $rutaPlantilla, $dependencia, $archivoFinalF, False);
   $estadoTransaccion = 0;
   $estadoTrans=$masiva->confirmarMasiva();
   $linkfile = "../../bodega/" . $ano . "/" . $dependencia . "/docs/" . $nombreF;

      if  ($estadoTrans)
      {
	 $_SESSION["masiva"]=$masiva;
        // echo "-----<br/>";
	 echo "<BR><span class='info'><a  class='vinculos' href=javascript:abrirArchivoaux('$linkfile')>GuardarArchivo</a>";
	 echo "</span>";
	 echo "<span class='info'>";
	 echo "<BR><a class='vinculos' href=javascript:abrirArchivoaux('$arcPDF')> Abrir Listado</a>";
	 echo "</span>";
	 //Contabilizamos tiempo final
	 $time_end = microtime_float();
	 $time = $time_end - $time_start;
	 echo "<span class='info'>";
	 echo "<br><b>Se demor&oacute;: $time segundos la Operaci&oacute;n total.</b>";
	 echo "</span>";
	 echo "<input type=hidden name=masiva2 value=$masiva>";
      }
      else
      {
	 echo "<BR><span class='alarmas'>Se gener&oacute; un problema al alctualizar la base de datos, intente mas tarde($estadoTrans)";
	 echo "</span>";
      }
}

?>
</form>
</body>
</html>
