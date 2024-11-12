<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET);
    extract($_POST);
    extract($_SESSION);
}

$verradOld=$verrad;
error_reporting(7);
$ruta_raiz = ".."; 
$verrad =$verradOld;
if (!$verrad) {
	$verrad= $rad;
}

if($verrad){
	$ent = substr($verrad,-1);
}

include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug=true;

define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
include_once "$ruta_raiz/include/tx/Historico.php";
$objHistorico= new Historico($db);
$arrayRad = array();
$arrayRad[]=$verrad;
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
if (is_array($recordSet) && (count($recordSet)>0) ) {
	array_splice($recordSet, 0);
}
if (is_array($recordWhere) && (count($recordWhere)>0) ) {
	array_splice($recordWhere, 0);
}
	
$encabezadol = $_SERVER['PHP_SELF']."?".session_name()."=".session_id()."&krd=$krd&verrad=$verrad&dependencia=$dependencia&codusuario=$codusuario&depende=$depende&ent=$ent&numRadi=$numRadi&codiTRDEli=$codiTRDEli&tipVinDocto=$tipVinDocto&mostrar_opc_envio=$mostrar_opc_envio&nomcarpeta=$nomcarpeta&carpeta=$carpeta&datoVer=$datoVer&leido=$leido"; 
?>

<html>
	<head>
		<title>Vinculaci&oacute;n de Respuesta</title>
		<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
		<script>

			function regresar(){   	
				document.VincDocu.submit();
			}
		</script>
	</head>
	<body bgcolor="#FFFFFF">
		<form method="post" action="<?=$encabezadol?>" name="VincDocu"> 

<?php
//Incluye una nueva vinculacion entre dos Radicados o Modifica una existente
if ($insertar_registro && $numRadi !='' ){
	//Verificar la existencia del Radicado con el cual se va a realizar la vinculacion del documento
    $isqlB = "	SELECT	radi_nume_radi
				FROM 	radicado
				WHERE	radi_nume_radi = $numRadi";
	$rsB=$db->conn->Execute($isqlB);
	$numRadiBusq = $rsB->fields["RADI_NUME_RADI"];
	if($numRadiBusq==''){
		$mensaje = "<hr><center><b>
			<span class='alarmas'>No se encontro el radicado de respuesta, por favor verifique que sea un radicado de salida e intente de nuevo</span>
		</center></b></hr>";  
	}
	else{
		$isqlM ="SELECT	radi_nume_radi, 
						radi_respuesta
				FROM 	radicado
	            WHERE	radi_nume_radi = $verrad";
		$rsM=$db->conn->Execute($isqlM);
		
		$numRadiBusq = $rsM->fields["RADI_NUME_RADI"];
		
		if($numRadiBusq != ''){
			if (is_array($recordSet) && (count($recordSet)>0) )
				array_splice($recordSet, 0);  		
	   		if (is_array($recordWhere) && (count($recordWhere)>0) )
				  array_splice($recordWhere, 0);
			$radiDeriAnte = $rsM->fields["RADI_RESPUESTA"];
				
			//Actualiza el vinculo de documentos en la Tabla Radicados
			$recordSet["RADI_RESPUESTA"] = $numRadi;
				
			$recordWhere["RADI_NUME_RADI"] = $verrad;	  
			$ok = $db->update("RADICADO", $recordSet,$recordWhere);
				
			array_splice($recordSet, 0);  		
			array_splice($recordWhere, 0);	  
				
	        if($ok){
	        	$mensaje = "<hr> <center> <b> <span class=info>Vinculaci&oacute;n Documento Actualizado </span> </center> </b> </hr>";
				if ($radiDeriAnte==''){
					$observa = "Se incluyo vinculaci&oacute;n de respuesta con el Radicado No. $numRadi";
				}
				else{
		           	$observa = "*Cambio de Vinculaci&oacute;n de Respuesa* Anterior($radiDeriAnte) por ($numRadi)";
				}
				$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);	
				$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 99);
			}
		}
		else{
			$mensaje = "<hr><center><b><span class=info>No se pudo actualizar el Radicado</span></center></b></hr>";
		}
	}
}
?>  
			<table border=0 width=70% align="center" class="borde_tab" cellspacing="0">
				<tr align="center" class="titulos2">
					<td height="15" class="titulos2">VINCULACI&Oacute;N RESPUESTA DE RADICADO</td>
				</tr>
			</table> 
			<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
				<tr>
					<td class="titulos5" >No. de Radicado</td>
					<td class=listado5 >
						<input name="numRadi" type="text" size="20" class="tex_area" value="<?=$numRadi?>">
					</td>
				</tr>
			</table>
			<br>
			<table border=0 width=70% align="center" class="borde_tab">
				<tr align="center">
					<td width="33%" height="25" class="listado2" align="center">
						<center><input name="insertar_registro" type=submit class="botones_funcion" value="Grabar Cambio "></center>
					</td>
					
					<td width="33%" class="listado2" height="25">
						<center><input name="aceptar" type="button" class="botones_funcion" id="envia22" onClick=" opener.regresar();window.close();"value=" Cerrar"></center>
					</td>
				</tr>
			</table>
			<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
				<tr align="center">
					<td>
						<?php
						include_once "$ruta_raiz/vinculacion/lista_tiposRespuesta.php";
						?>
					</td>
				</tr>
			</table>
			<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
				<tr> 
					<td colspan="2" class='celdaGris' >
						<?php 
							echo $mensaje;
						?>
					</td>
				</tr>
			</table>
			<script>
				function borrarArchivo(anexo,linkarch){
					if (confirm('Esta seguro de borrar este Registro ?')){
						nombreventana="ventanaBorrarVin";
						url="mod_respuestaTx.php?borrar=1&usua=<?=$krd?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&verrad=<?=$verrad?>&codiVinEli="+anexo+"&linkarchivo="+linkarch;
						window.open(url,nombreventana,'height=250,width=300');
					}
					return;
				}
				function procModificar(){
					nombreventana="ventanaBusqAV";
					url="../busqueda/busquedaPiloto.php?indiVinculo=1&etapa=1&krd=<?=$krd?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&carpeAnt=<?=$carpeta?>&verrad=<?=$verrad?>&s_Listado=VerListado&fechah=$fechah&mostrar_opc_envio=<?=$mostrar_opc_envio?>&nomcarpeta=<?=$nomcarpeta?>&datoVer=<?=$datoVer?>&leido=<?=$leido?>";
					window.open(url,nombreventana,'height=600,width=770,scrollbars=yes');
					return;
				}
			</script>
		</form>
		<span> <p> <?=$mensaje_err?> </p> </span>
	</body>
</html>