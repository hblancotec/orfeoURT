<link rel="stylesheet" href="../estilos/orfeo.css">
<script>
	function noPermiso(tip)
	{
		if(tip==0)
			alert ("No puede acceder por su nivel de seguridad");
		if(tip==1)
			alert ("No puede acceder por el nivel de privacidad");
	}
</script>


<table width="100%"  border="0" cellpadding="0" cellspacing="5" class="borde_tab">
	<tr>
		<td class="titulos3" width="1"> # </td>

<?php
	//echo "-";
	$ruta_raiz = "../";
	$igualesRadAne = false; // variable para controlar contador de columna
	$check=1;
	$fieldCount = $rsE->FieldCount();
	if($ascdesc=="") { $ascdesc = " desc "; }
		else $ascdesc = "";

	// Variable que se encarga de controlar si exite algun documento en el anexo
		$existeArchivo = false;
			
	for($iE=0; $iE<=$fieldCount-1; $iE++) {
		$fld = $rsE->FetchField($iE);
		// El siguiente "if" Omite las columnas que venga con encabezado HID
		if(substr($fld->name,0,3)!="HID") {
?>
	
		<td class="titulos3">
			<!-- //<?php $linkPaginaActual = $_SERVER['PHP_SELF'];	?>
			//<a href='<?=$linkPaginaActual?>?<?=$datosaenviar?>&ascdesc=<?=$ascdesc?>&orno=<?=($iE+1)?>&generarOrfeo=Busquedasss&genDetalle=<?=$genDetalle?>&genTodosDetalle=<?=$genTodosDetalle?>&fenvCodi=<?=$fenvCodi?>&expediente=$expedientes&tipoDocumento=<?=$tipoDocumento?>' > -->
			<?php	echo $fld->name; ?>
			<!-- </a> -->
		</td>
			
<?php
		}
	}
	if(!$genDetalle) {
?>
		
		<td class="titulos3"> </td>
<?php
	}
?>

	</tr>
	
<?php
	$iRow = 1;
	$datosCod = 0;
	$radicadoAnt = "";

	/**  INICIO CICLO RECORRIDO DE LOS REGISTROS
	  *	 En esta seccion se recorre todo el query solicitado
	  *  @numListado Int Variable que almacena 1 O 2 dependiendo de la clase requerida.(Resultado de modulo con doos )
	  */
	while(!$rsE->EOF) {
		$numListado = fmod($iRow,2);
		if($numListado==0) {
			$numListado = 2;
		}
?>
	
	<tr class='listado<?=$numListado?>'>
		<td width="1"><?=$iRow?></td>
	
<?php
		$fieldCount = $rsE->FieldCount();
		$noWrap = '';
		for($iE=0; $iE<=$fieldCount-1; $iE++) {
			$fld = $rsE->FetchField($iE);
			if(substr($fld->name,0,3)!="HID") {
				if($iE == 0){
					$noWrap = 'nowrap';
				} 
				else {
					$noWrap = '';
				}
?>
		
		<td <?php echo $noWrap; ?>>
		
<?php
				$pathImg = "";
				if($fld->name == "RADICADO") {
					$sesion = session_name() . "=" . session_id();
					$radicado = $rsE->fields["RADICADO"];
					$datosEnvioEst = "verrad=$radicado&$sesion&krd=$krd&carpeta=1&tipo_carp=0";
					$pathImg = $rsE->fields["HID_RADI_PATH"];
					$verRadicado =  $ruta_raiz."/bodega/".$pathImg;

					if (trim($pathImg)){

						### SE VERIFICA SI EL USUARIO TIENE ACCESO AL RADICADO Y DE ACUERDO A ELLO SE ARMAN LOS ENLACES.
						switch ($rsE->fields["HID_PERMISO"]){
							case 1:
								$verRadicado = 'javascript:noPermiso(1)';
								break;
							case 2:
								$verRadicado = 'javascript:noPermiso(0)';
								break;
						}

						$enlaceRadicado = "	<a class='vinculos' href='$verRadicado' alt='Ver Radicado' title='Ver Radicado'> 
												<img src='../img/documento.png' width='11' height='13' border='0'>
											</a>";

						$linkDocto = "<a class='vinculos' href='javascript:noPermiso(0)' > $rad_nume </a>";
						echo $enlaceRadicado;
						echo "&nbsp;";
					}
				}

				if($fld->name == "RADICADO") {

					### SE VERIFICA SI EL USUARIO TIENE ACCESO AL RADICADO Y DE ACUERDO A ELLO SE ARMAN LOS ENLACES.
					switch ($rsE->fields["HID_PERMISO"]){
						case 0:
							$pathImg = $rsE->fields["HID_RADI_PATH"];
							if(trim($pathImg)) {
								echo "<a href='$ruta_raiz/bodega/$pathImg'>";
							}
							break;
						case 1:
							echo "<a href='javascript:noPermiso(1)'>";
							break;
						case 2:
							echo "<a href='javascript:noPermiso(0)'>";
							break;
					}
				}

				if ($fld->name == "FEC_RAD_E") {
					$fechaRadicado = $rsE->fields["FEC_RAD_E"];
					$radicado = $rsE->fields["RAD_ENTRADA"];
					$sesion = session_name() . "=" . session_id();
					$datosEnvioEst = "verrad=$radicado&$sesion&krd=$krd&carpeta=1";
					$verRadicado = $ruta_raiz . "verradicado.php?$datosEnvioEst";
					$enlaceRadicado = "<a href='$verRadicado'>$fechaRadicado</a>";
					echo $enlaceRadicado;
					$rsE->fields[$fld->name] = "";
				}

				if ($fld->name == "FECHA_RADICADO") {
					$fechaRadicado = $rsE->fields["FECHA_RADICADO"];
					$radicado = $rsE->fields["RADICADO"];
					$sesion = session_name() . "=" . session_id();
					$datosEnvioEst = "verrad=$radicado&$sesion&krd=$krd&carpeta=1&tipo_carp=0&nomcarpeta=Salida&agendado=&orderTipo=DESC&orderNo=15";

					### SE VERIFICA SI EL USUARIO TIENE ACCESO AL RADICADO Y DE ACUERDO A ELLO SE ARMAN LOS ENLACES.
					switch ($rsE->fields["HID_PERMISO"]){
						case 0:
							$verRadicado = $ruta_raiz."verradicado.php?$datosEnvioEst";
							break;
						case 1:
							$verRadicado = 'javascript:noPermiso(1)';
							break;
						case 2:
							$verRadicado = 'javascript:noPermiso(0)';
							break;
					}							
					
					### SE REALIZA LA SIGUIENTE VALIDACIÓN PARA QUE EL ENLACE O LINK SE ACTIVE UNICAMENTE
					### CUANDO SE TRATE DE LA FECHA DE UN RADICADO Y NO DE LA FECHA DE UN ANEXO DE EXPEDIENTE.
					$flag = strpos($radicado, '.');
					if ($flag){
						$enlaceRadicado = $fechaRadicado;
					}
					else{
						$enlaceRadicado = "<a href='$verRadicado'>$fechaRadicado</a>";
					}
					
					echo $enlaceRadicado;
				}

				if (($fld->name == "RESPUESTA") && $rsE->fields["RESPUESTA"]) {
					$respuesta = $rsE->fields["RESPUESTA"];  

					$isql	= "SELECT	re.radi_path as HID_RADI_PATH
							   FROM		radicado re 
							   WHERE	re.radi_nume_radi = $respuesta";								
					$sal	= $db->conn->Execute($isql);				

					$pathImg2 		= $sal->fields["HID_RADI_PATH"];				

					if(trim($pathImg2)) {
						echo "<a href='$ruta_raiz/bodega/$pathImg2'>$respuesta</a>";
					}
					else{
						echo $respuesta;
					}
					$rsE->fields[$fld->name] = "";					   
				}

				if($fld->name == "FECHA_RESPUESTA"){				
					$respuesta = $rsE->fields["RESPUESTA"];  
					$isql	= "SELECT	RE.RADI_FECH_RADI  AS FECHA_RADICADO
							   FROM		RADICADO RE 
							   WHERE	RE.RADI_NUME_RADI = $respuesta";																
					$sal	= $db->conn->Execute($isql);				

					$fechaRadicado 	= $sal->fields["FECHA_RADICADO"];				

					$sesion = session_name() . "=" . session_id();
					$datosEnvioEst = "verrad=$respuesta&$sesion&krd=$krd&carpeta=1&tipo_carp=0&nomcarpeta=Salida&agendado=&orderTipo=DESC&orderNo=15";
					$verRadicado = $ruta_raiz."verradicado.php?$datosEnvioEst";
					echo "<a href='$verRadicado'>$fechaRadicado</a>";				
					$rsE->fields[$fld->name] = "";	
				}

				if ($fld->name == "FECHA_RADICA") {
					$fechaRadicado = $rsE->fields["FECHA_RADICADO"];
					$radicado = $rsE->fields["RADICADO"];
					$sesion = session_name() . "=" . session_id();
					$datosEnvioEst = "verrad=$radicado&$sesion&krd=$krd&carpeta=1&tipo_carp=0&nomcarpeta=Salida&agendado=&orderTipo=DESC&orderNo=15";
					$verRadicado = $ruta_raiz."verradicado.php?$datosEnvioEst";
					$enlaceRadicado = "<a href='$verRadicado'>$fechaRadicado</a>";
					echo $enlaceRadicado;
				}

				// Asignando archivo que viene en el anexo
				$existeArchivo = (!empty($rsE->fields["HID_NOMBRE_ARCHIVO"])) ? true : false ;

				// busca el campo de numero de expediente para asignar en ver detalles
				if ($fld->name == "No_EXPEDIENTE"){
					$expedientes = $rsE->fields["$fld->name"];
				}

				if ($fld->name == "ANEXO") {
					if (empty($rsE->fields["$fld->name"]) && $existeArchivo){
						$rsE->fields["$fld->name"] = "Anexo sin radicar";
					}
					if ($rsE->fields["$fld->name"] == $rsE->fields["RADICADOS"]) {
						$rsE->fields["$fld->name"] = "";
						$igualesRadAne = true;
					}
					else {
						$igualesRadAne = false;
					}
				}

				if ($fld->name == "RADICADOS" && $genDetalle) {
					if ($radicadoAnt != $rsE->fields["$fld->name"]){
						$radicadoAnt = $rsE->fields["$fld->name"];
					} 
					else {
						$rsE->fields["$fld->name"] = "";
					}
				}

				if ($fld->name != "FECHA_RADICADO") {
					echo $rsE->fields["$fld->name"];
				}

				if(trim($pathImg)) {
					echo '</a>';
				}

				if ($fld->name == "ULTIMA_OBSERVACION") {
					$sql = "select hist_obse from hist_eventos where radi_nume_radi=".$rsE->fields['RADICADO']." and sgd_ttr_codigo in (9,13) order by hist_fech desc";
					$rsE->fields['ULTIMA_OBSERVACION'] = $db->conn->GetOne($sql);				
				}
?>
	
		</td>

<?php
			} // fin del if que omite las columnas con hid_

			if($fld->name=="HID_COD_USUARIO") {
				$datosEnvioDetalle="codUs=".$rsE->fields["$fld->name"];
			}

			if($fld->name=="USUARIO") {
				$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
				$nombXAxis = "USUARIO";
			}

			if($fld->name=="MEDIO_RECEPCION") {
				$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
				$nombXAxis = "MED RECEPCION";
			}

			if($fld->name=="MEDIO_ENVIO") {
				$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
				$nombXAxis = "MED ENVIO";
			}

			if($fld->name=="RADICADOS") {
				$data1y[($iRow-1)]=$rsE->fields["$fld->name"];
				$nombYAxis = "RADICADOS";
			}

			if($fld->name=="TOTAL_ENVIADOS") {
				$data1y[($iRow-1)]=$rsE->fields["$fld->name"];
				$nombYAxis = "RADICADOS";
			}

			if($fld->name=="HOJAS_DIGITALIZADAS") {
				$data2y[($iRow-1)]=$rsE->fields["$fld->name"];
				$nombYAxis .= " / HOJAS DIGITALIZADAS";
			}

			if($fld->name=="HID_MREC_CODI"){
				$datosEnvioDetalle.="&mrecCodi=".$rsE->fields["$fld->name"];
			}	

			if($fld->name=="HID_CODIGO_ENVIO"){
				$datosEnvioDetalle.="&fenvCodi=".$rsE->fields["$fld->name"];
			}	

			if($fld->name=="HID_TPR_CODIGO"){
				$datosEnvioDetalle.="&tipoDOCumento=".$rsE->fields["$fld->name"];
			}

			if($fld->name=="HID_DEPE_USUA"){
				$datosEnvioDetalle.="&depeUs=".$rsE->fields["$fld->name"];
			}

			if($fld->name=="HID_FECH_SELEC"){
				$datosEnvioDetalle.="&fecSel=".$rsE->fields["$fld->name"];
			}

			if($fld->name=="HID_FECH_MAX"){
				$datosEnvioDetalle.="&fechMax=".$rsE->fields["$fld->name"];
			}	

			if($fld->name=="HID_FECH_MIN"){
				$datosEnvioDetalle.="&fechMin=".$rsE->fields["$fld->name"];
			}

			if($fld->name=="HID_USUA_DOC"){
				$datosEnvioDetalle.="&usuDoc=".$rsE->fields["$fld->name"];
			}
			if($fld->name=="EXPEDIENTE"){
			    $datosaenviar.="&exp=".$rsE->fields["$fld->name"];
			}
		}

		if(!$genDetalle) {
			if($genTodosDetalle==1) {
?>
			
		<td align="center">
			<a href="genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>"></a>
		</td>
		
<?php
			}
			else {
				if ($tipoEstadistica == 13 ) {
					$datosaenviarCodExp = "fechaf=$fechaf&tipoEstadistica=$tipoEstadistica&codus=$usuadoc[$datosCod]&krd=$krd&dependencia_busq=$dependencia_busq&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento&depCodigo=$dependencias[$datosCod]&expediente=$expedientes";	

					###SE CONSULTA EL ESTADO DE PRIVACIDAD DEL EXPEDIENTE
					$sqlPriv ="	SELECT	SGD_SEXP_PRIVADO,
										DEPE_CODI
								FROM	SGD_SEXP_SECEXPEDIENTES
								WHERE	SGD_EXP_NUMERO = '$expedientes'";
					$rsPriv = $db->conn->Execute($sqlPriv);
					if (($rsPriv->fields['SGD_SEXP_PRIVADO'] == 1) && ($rsPriv->fields['DEPE_CODI'] != $_SESSION['dependencia'])){
?>
						
		<td align="center">
			<a class='vinculos' href='javascript:noPermiso(1)'> VER DETALLES</a>
		</td>

<?php
					}
					else {
?>

		<td align="center">
			<a href="genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviarCodExp?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">VER DETALLES</a>
		</td>

<?php
					}
				}
				elseif (isset($usuadoc)) {
					$datosaenviarCod = "fechaf=$fechaf&tipoEstadistica=$tipoEstadistica&codus=$usuadoc[$datosCod]&krd=$krd&dependencia_busq=$dependencia_busq&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento&depCodigo=$dependencias[$datosCod]";
					$datosCod++;
?>

		<td align="center">
			<a href="genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviarCod?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">VER DETALLES</a>
		</td>
		
<?php
				}
				else {
?>
		
		<td align="center">
			<a href="genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">VER DETALLES</a>
		</td>
			
<?php
				}
			}
		}
?>

	</tr>

<?php
		if($check<=20){
			$check=$check+1;
		}

		$rsE->MoveNext();
		// Fin ciclo recorrido de los registros
		$iRow++;
		$datosEnvioDetalle="";
	}
	$_SESSION["data1y"] = $data1y;
	$_SESSION["nombUs"] = $nombUs;
	$noRegs = is_array($data1y) ? count($data1y) : 0;
?>

</table>

<?php
	$nombreGraficaTmp = "E_$krd.png";
	$rutaImagen = $nombreGraficaTmp;
	$notaSubtitulo = $subtituloE[$tipoEstadistica]."\n";
	$tituloGraph = $tituloE[$tipoEstadistica];
?>

<br>
<span class="listado5">
	Items <?=($iRow-1)?>
</span>

<?php
	if($tipoEstadistica == 15){
		unset($rsA->fields['HID_RADI_PATH']);
		unset($rsA->fields['RUTA_ANEXO']);
		require ("$ruta_raiz/include/rs2xml.php");
		$obj = new rs2xml();
		$txtRs2Xml = $obj->getXML($rsA);
		$archivo = $krd."_".rand(10000, 20000);
		$path = "../bodega/tmp/$archivo.xls";
		$fp = fopen($path, "w");
		
		if ($fp) {
			fwrite($fp, $txtRs2Xml);
			fclose($fp);
		}
?>
	
<br>
<center>
	<span class="listado5">
		<a "target="blank" href="<?=$path ?>"> DESCARGAR EXCEL</a>
	</span>

<?php
	}
	if ($tipoEstadistica==1 or $tipoEstadistica==3 or $tipoEstadistica==6 or $tipoEstadistica==8 or $tipoEstadistica==12 or $tipoEstadistica==15 or $tipoEstadistica==13) {
		if ($genTodosDetalle==1 or $genDetalle==1) {
?>
	
	<br>
		<a href="genEstadistica.php?<?=$datosEnvioDetalle?>&genTodosDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>"> </a>
	<br>
	
<?php
		}
		else {
?>
		
	<table border=0 cellspace=2 cellpad=2 WIDTH=100% class='borde_tab' align='center'>
		<tr align="center">
			<td>

<?php
			// Se calcula el numero de | a mostrar
			$rsE=$db->conn->Execute($isqlCount);
			$paginas = (($iRow-1) / 1000);
			if($paginas>=2){
?>
			
				<span class='vinculos'> Paginas </span> 
			
<?php
				if(intval($paginas) <= $paginas){
					$paginas=$paginas;
				}
				else{
					$paginas=$paginas-1;
				}

				// Se imprime el numero de Paginas.
				for($ii=0;$ii<$paginas;$ii++) {
					if($pagina==$ii){
						$letrapg="<font color=green size=3>";
					}
					else{
						$letrapg="";
					}
					echo " <a href='tablaHtml.php?pagina=$ii&$encabezado$orno'>
						<span class=leidos> $letrapg".($ii+1)."</span> </font> </a> \n";
				}
			}
			
			echo "<input type=hidden name=check value=$check>";
?>

			</td>
		</tr>
	</table>
	<form name=jh >
		<input type="hidden" name=jj value=0>
		<input type="hidden" name=dS value=0>
	</form>
	<br/>
<?php
if ($noRegs>=1) {
?>
	<a href="genEstadistica.php?<?=$datosEnvioDetalle?>&genTodosDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">
		VER TODOS LOS DETALLES
	</a>
<?php }
	}
}
if($genDetalle!=1 and $noRegs>=1) {
	include "genBarras1.php";
?>
	
	<br>
	<input type=button class="botones_largo" value="Ver Grafica" onClick='window.open("./image.php?rutaImagen=<?=$rutaImagen."&fechaH=".date("YmdHis")?>" , "Grafica Estadisticas - Orfeo", "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=560,height=720");'>

<?php
	}
?>	
	</center>
</body>
</html>