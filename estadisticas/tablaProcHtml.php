<html>
<title>ORFEO - IMAGEN ESTADISTICAS </title>
		<link rel="stylesheet" href="../estilos/orfeo.css" />
<body>
<CENTER>
   <table border="0" cellpadding="0" cellspacing="2" class="borde_tab">
	<tr >
		<td class="titulos3" width="1">	
		# <?=$estadoProc?>
		</td>
		<?php
		$fieldCount = $rsE->FieldCount();
		if($ascdesc=="")
		{
			$ascdesc = " desc ";
		}else
		{
			$ascdesc = "";
		}
		for($iE=0; $iE<=$fieldCount-1; $iE++)
		{
		$fld = $rsE->FetchField($iE);
		/** El siguietne "if" Omite las columnas que venga con encabezado HID
				*/	
				
		if(substr($fld->name,0,3)!="HID") 
		{
		?>
		
		<td class="titulos3">	
		<?php 
		$linkPaginaActual = $PHP_SELF;
		?>
		<a href='<?=$linkPaginaActual?>?<?=$datosaenviar?>&ascdesc=<?=$ascdesc?>&orno=<?=($iE+1)?>&generarOrfeo=Busquedasss&genDetalle=<?=$genDetalle?>&genTodosDetalle=<?=$genTodosDetalle?>&fenvCodi=<?=$fenvCodi?>&tipoDocumento=<?=$tipoDocumento?>' >
			<?php
				echo $fld->name;
			?>
		</a>
		</td>
		<?php
		}
		}
		if(!$genDetalle)
		{
		?>
	<?php
	$codigoTipoExp=1;
	$isqlEstados = "select 
						fe.SGD_FEXP_DESCRIP
						,fe.SGD_FEXP_TERMINOS
						,fe.SGD_FEXP_CODIGO
						,fe.SGD_FEXP_ORDEN
				from SGD_FEXP_FLUJOEXPEDIENTES fe
			where 
				fe.SGD_PEXP_CODIGO ='$codProceso'
				order by fe.SGD_FEXP_ORDEN  ";  
		//$db->conn->debug = true;
		echo "--->$isqlEstados";
		$rs2 = $db->conn->Execute($isqlEstados);
		$terminosTotales = 0;
	$colsProc = 0;
	if($rs2)
	{
		while(!$rs2->EOF)
		{
			$etapaFlujo = $rs2->fields["SGD_FEXP_DESCRIP"];
			$etapaFlujoTerminos = $rs2->fields["SGD_FEXP_TERMINOS"];
			$etapaFlujoNombres[$colsProc]=$etapaFlujo;
				?>
						<TD class="titulos3" align="center"><?=$etapaFlujo?></TD>
				<?php
			$colsProc++;
			$rs2->MoveNext();
		}
	}
	?><td class="titulos3">	</td>	<?php 
	}
	?>
</tr> 
	<?php
	$iRow = 1;
	while(!$rsE->EOF)
	{
	/**  INICIO CICLO RECORRIDO DE LOS REGISTROS
	  *	 En esta seccion se recorre todo el query solicitado
	  *  @numListado Int Variable que almacena 1 O 2 dependiendo de la clase requerida.(Resultado de modulo con doos )
	  */
		$usuaDocProc = $rsE->fields["HID_USUA_DOC"];
	  $numListado = fmod($iRow,2);
	  if($numListado==0)
	  {
	  	$numListado = 2;
	  }
	?>
	<tr class='listado<?=$numListado?>' >
		<td width="1">	
		<?=$iRow?>
		</td>
		<?php
		$fieldCount = $rsE->FieldCount();
		for($iE=0; $iE<=$fieldCount-1; $iE++)
		{
		$fld = $rsE->FetchField($iE);
		if(substr($fld->name,0,3)!="HID") 
		{
		?>
	<td>
	<?php
	$pathImg = "";
	if($fld->name=="RADICADO") 
	{
			$pathImg = $rsE->fields["HID_RADI_PATH"];
			if(trim($pathImg)) 
			{
				echo "<a href=$ruta_raiz/bodega/$pathImg>";
			}
	}
	if($fld->name=="TOTAL_PROCESOS") 
		{
				$totalProcesos = $totalProcesos + $rsE->fields["TOTAL_PROCESOS"];
			$iTotalProcesos = $iE;
			$datosEnvioDetalles = "$datosEnvioDetalle&genDetalle=1&usuaDocProc=$usuaDocProc&$datosaenviar";
			echo "<a href='genEstadisticaProc.php?$datosEnvioDetalles' target=detallesSec>";
		}
			echo $rsE->fields["$fld->name"];
		if(trim($pathImg) or $fld->name=="TOTAL_PROCESOS") 
				{
					echo "</a>";
				}
		?>
		</td>
		<?php
		} // fIN DEL IF QUE OMITE LAS COLUMNAS COM HID_
		if($fld->name=="HID_COD_USUARIO") 
		{
			$datosEnvioDetalle="codUs=".$rsE->fields["$fld->name"];
		} 
		if($fld->name=="USUARIO") 
		{
			$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
			$nombXAxis = "USUARIO";
		}
		if($fld->name=="MEDIO_RECEPCION") 
		{
			$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
			$nombXAxis = "MED RECEPCION";
		}					
		if($fld->name=="MEDIO_ENVIO") 
		{
			$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
			$nombXAxis = "MED ENVIO";
		}										

		if($fld->name=="RADICADOS") 
		{
			$data1y[($iRow-1)]=$rsE->fields["$fld->name"];
			$nombYAxis = "RADICADOS";
		}
		if($fld->name=="TOTAL_ENVIADOS") 
		{
			$data1y[($iRow-1)]=$rsE->fields["$fld->name"];
			$nombYAxis = "RADICADOS";
		}					
		if($fld->name=="HOJAS_DIGITALIZADAS") 
		{
			$data2y[($iRow-1)]=$rsE->fields["$fld->name"];
			$nombYAxis .= " / HOJAS DIGITALIZADAS";
		}										
		if($fld->name=="HID_MREC_CODI") $datosEnvioDetalle.="&mrecCodi=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_CODIGO_ENVIO") $datosEnvioDetalle.="&fenvCodi=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_TPR_CODIGO") $datosEnvioDetalle.="&tipoDOCumento=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_DEPE_USUA") $datosEnvioDetalle.="&depeUs=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_FECH_SELEC") $datosEnvioDetalle.="&fecSel=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_USUA_DOC") {
				$usuaDocProc = $rsE->fields["$fld->name"];
				$datosEnvioDetalle.="&usuaDoc=".$rsE->fields["$fld->name"];
		}
}
	if(!$genDetalle)
	{
		if($genTodosDetalle==1)  {
			?>
			<td align="center">
			<a href="genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>">
			</a>
			</td>
			<?php
		} else {
			if(!$usuaDocProc)
			{
				$queryUs = "select * from usuario where depe_codi=$dependencia_busq and usua_codi=$codus";
				$rsUs = $db->conn->Execute($queryUs);
				$usuaDocProc = $rsUs->fields["USUA_DOC"];
			}
			$queryEstados = "
				select fExp.sgd_fexp_orden ORDEN, count(*) CONTEO, MIN(sExp.SGD_FEXP_CODIGO) SGD_FEXP_CODIGO
				from sgd_sexp_secexpedientes sExp , sgd_pexp_procexpedientes pExp, sgd_fexp_flujoExpedientes fexp
				where SExp.sgd_srd_codigo=pExp.sgd_srd_codigo 
					and SExp.sgd_sbrd_codigo=pExp.sgd_sbrd_codigo 
					and pExp.sgd_pexp_codigo=fExp.sgd_pExp_codigo
					and fExp.sgd_fexp_codigo=sExp.sgd_fexp_codigo
					and sExp.usua_doc_responsable=$usuaDocProc 
				group by fExp.sgd_fexp_orden";
			$rsEstados = $db->conn->Execute($queryEstados);
			$estados = "";
			while(!$rsEstados->EOF)
			{
				$estadoCodigo = $rsEstados->fields["ORDEN"];
				$estadoProc[$estadoCodigo] = $rsEstados->fields["SGD_FEXP_CODIGO"];
				$estadoRegistros = $rsEstados->fields["CONTEO"];
				$estados[$estadoCodigo]= $estadoRegistros;
				$subEstadosTotales[$estadoCodigo]= ($subEstadosTotales[$estadoCodigo]+$estadoRegistros);
				$rsEstados->MoveNext();
			}
			for($k=1;$k<=$colsProc;$k++)
			{
			$descTitulo = $estados[$k] ." Expedientes en Estado ".$etapaFlujoNombres[($k-1)];
			?>
			<td align="center"><center>
			<img src="<?=$ruta_raiz?>/imagenes/investigaciones.jpeg" width=10 height=10  alt="<?=$descTitulo?>" title="<?=$descTitulo?>">
			<a href='genEstadisticaProc.php?<?=$datosEnvioDetalles?>&estadoProc=<?=$estadoProc[$k]?>' target=detallesSec alt="<?=$descTitulo?>" title="<?=$descTitulo?>">
			<?=$estados[$k]?>
			</a>
			</td>
			<?php
			}
		}
	}
	?>
</tr> 
<?php
$rsE->MoveNext();
/**  FIN CICLO RECORRIDO DE LOS REGISTROS
  */
 $iRow++;
}
?>
<tr class=titulos3><td></td><td></td><td></td>
<?php
if(!$genDetalle)
{
$rs2 = $db->conn->Execute($isqlEstados);
		while(!$rs2->EOF)
		{
			$etapaFlujo = $rs2->fields["SGD_FEXP_DESCRIP"];
			$etapaFlujoTerminos = $rs2->fields["SGD_FEXP_TERMINOS"];
			$etapaFlujoNombres[$colsProc]=$etapaFlujo;
				?>
						<TD class="titulos3" align="center"><?=$etapaFlujo?></TD>
				<?php
			$colsProc++;
			$rs2->MoveNext();
		}
}
?>
</tr>
<tr class=titulos3>
<?php
for($iE=0; $iE<=($fieldCount+$colsProc); $iE++)
{
	if($iTotalProcesos==($iE-1))
	{
		echo "<td>$totalProcesos</td>";
	}else
	{
		
		if($iTotalProcesos<=($iE-2))
		{
			echo "<td>";
			echo $subEstadosTotales[$iE-2];
			echo "</td>";
		}else
		{
			echo "<td></td>";
		}
	}
}
$_SESSION["data1y"] = $data1y;
$_SESSION["nombUs"] = $nombUs;
$noRegs = count($data1y);
?>
</tr>
</table>

<?php
error_reporting(7);
//$nombUs[1] = "JHLC";
//$nombUs = array("ddd","kuiyiuiu","kjop99");
//$data1y = array(11,23,45);
//$nombUs = array("ddd","kuiyiuiu","kjop99");
//$data1y = array(11,23,45);
$nombreGraficaTmp = "$ruta_raiz/bodega/tmp/E_$krd.png";
$rutaImagen = $nombreGraficaTmp;
$notaSubtitulo = $subtituloE[$tipoEstadistica]."\n";
$tituloGraph = $tituloE[$tipoEstadistica];
?>
<br><center><span class="listado5">
Items <?=($iRow-1)?>
</span>
<?php if ($tipoEstadistica==1 or $tipoEstadistica==3 or $tipoEstadistica==8)  {
		
	}
?>
<?php
if($genDetalle!=1 and $noRegs>=1)
{
include "genBarras1.php";
?>
	 <br><input type=button class="botones_largo" value="Ver Grafica" onClick='window.open("./image.php?rutaImagen=<?=$rutaImagen."&fechaH=".date("YmdHis")?>" , "Grafica Estadisticas - Orfeo", "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=560,height=720");'>
      <?php
}
?>

</center>
</CENTER>
</body>
</html>
