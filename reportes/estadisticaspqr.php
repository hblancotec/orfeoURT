<?php
	
	session_start();
	
	$ruta_raiz 		= "..";
	$krd 			= $_SESSION["krd"];
	$usua_nombre 	= $_SESSION['usua_nomb'];
	$dependencia 	= $_SESSION["dependencia"];
	$depeNombre	 	= $_SESSION['depe_nomb'];		
		
	include_once ("$ruta_raiz/_conf/constantes.php");
	require_once (ORFEOPATH . "include/db/ConnectionHandler.php");
	
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->debug = true;
	
	$reporte 	=($_POST['reporte'])? true : false;
	$med_rec2 	=(empty($_POST['med_rec']))? '' : implode(",", $_POST['med_rec']);	
	$dep_sel2 	=(empty($_POST['dep_sel']))? '' : implode(",", $_POST['dep_sel']);
	$fechai 	=(empty($_POST['fechai']))? date("Y-m-d") : $_POST['fechai'];
	$fechafi 	=(empty($_POST['fechafi']))? date("Y-m-d") : $_POST['fechafi'];
	
	if($reporte){
		$mostrar ='';
		$ocultar ='colspan="2"';    
	}else{
		$mostrar ='colspan="3"';
		$ocultar ='class="yui-hidden2"';
	}
	
	//Consultas para filtros de seleccion
	//medio de recepcion
	$medSQL			= " SELECT																
							MREC_DESC,
							MREC_CODI
						FROM
							MEDIO_RECEPCION
							ORDER BY MREC_DESC";						
	$cons_medRecp 	= $db->conn->Execute($medSQL);
	
	//dependencia
	$depSQL 		= " SELECT
							DEPE_NOMB,
							DEPE_CODI
						FROM
							DEPENDENCIA
						order by DEPE_NOMB";
						
	$cons_depende	= $db->conn->Execute($depSQL);				
	
	//variable con elementos de sesion
	$encabezado = $PHP_SELF."?".session_name()."=".session_id();	
	
	$fechafiltro = " and r.RADI_FECH_RADI BETWEEN '$fechai' and '$fechafi'";
	
	if(empty($dep_sel2)){
		$dependencia_busq2 = "";
	}else{
		$dependencia_busq2 = " and r.RADI_DEPE_ACTU IN ($dep_sel2)";
	}
	
	if(empty($med_rec2)){
		$medio_busq = "";
	}else{
		$medio_busq = " and r.MREC_CODI IN ($med_rec2)";
	}
	if($SelPqr) $wherePqr = "AND s.SGD_TPR_REPORT1    = '1' "; else $wherePqr = " ";
	
	$temasFrom  = "LEFT OUTER JOIN dbo.SGD_CAUX_CAUSALES caux ON (r.RADI_NUME_RADI=caux.RADI_NUME_RADI)";
	// $temasSelect  0 Todos los radicados, 99999 Solo los que poseen temas
		if($temaSelect=="99999"){
			$temasWhere .= " AND caux.SGD_DCAU_CODIGO>=1 ";
		}elseif($temaSelect==0) {
			$temasWhere = "";
		}else{
			$temasWhere .= " AND caux.SGD_DCAU_CODIGO=$temaSelect ";
		}
	if($temasWhere){
	$temasWhere = "AND r.RADI_NUME_RADI IN (SELECT caux.radi_nume_radi from SGD_CAUX_CAUSALES caux  WHERE (r.RADI_NUME_RADI=caux.RADI_NUME_RADI $temasWhere  ))";
	}
	$isql =
		"SELECT  r.RADI_NUME_RADI         AS RADICADO
		       , r.RADI_FECH_RADI         AS FECHA_RAD
		       , s.SGD_TPR_DESCRIP        AS TIPO_DOC
		       , m.MREC_DESC              AS M_RECEP
		       , e.DEPE_NOMB              AS DEPE_ACTU
		       , de.DEPE_NOMB             AS DEPE_ANTE
		       , r.RADI_NUME_DERI         AS PADRE
		       , (SELECT TOP 1 h1.hist_obse FROM hist_eventos h1 where r.radi_nume_radi = h1.radi_nume_radi order by h1.hist_fech desc) as hist2
		       , (SELECT TOP 1 h2.hist_obse FROM hist_eventos h2 where r.radi_nume_radi = h2.radi_nume_radi and h2.hist_obse <> (SELECT TOP 1 h1.hist_obse from hist_eventos h1 where r.radi_nume_radi = h1.radi_nume_radi) order by h2.hist_fech desc)as hist1
		       , a.RADI_NUME_SALIDA       AS RESPUESTA
		       , (select re.radi_fech_radi  from radicado re where re.radi_nume_radi=a.radi_nume_salida)         AS FECHA_RESP
		       , (select re.radi_path from radicado re where re.radi_nume_radi=a.radi_nume_salida)               AS IMAGEN_RESP
		       , a.ANEX_NOMB_ARCHIVO      AS IMAGEN_ANEXO
		       , (CASE  WHEN a.ANEX_ESTADO = 4 THEN ('CORRESPONDENCIA') ELSE NULL END) AS ENVI_CORRES
		       , ex.SGD_EXP_NUMERO AS Expediente
		FROM RADICADO r
		     LEFT OUTER JOIN dbo.ANEXOS a ON (a.ANEX_RADI_NUME = r.RADI_NUME_RADI AND a.ANEX_SALIDA = 1 AND a.ANEX_NUMERO = 1)
		     LEFT OUTER JOIN dbo.USUARIO u ON (r.RADI_USU_ANTE = u.USUA_LOGIN)
		     LEFT OUTER JOIN dbo.SGD_EXP_EXPEDIENTE ex ON (r.RADI_NUME_RADI = ex.RADI_NUME_RADI)
				 LEFT OUTER JOIN DEPENDENCIA de ON (de.dEPE_CODI = U.depe_codi),
		     SGD_TPR_TPDCUMENTO s,
		     dbo.MEDIO_RECEPCION m,
		     dbo.DEPENDENCIA e
		WHERE
		     r.RADI_DEPE_ACTU = e.DEPE_CODI
		     and r.MREC_CODI      = m.MREC_CODI
		     and r.RADI_NUME_RADI LIKE '%2'
		     and r.TDOC_CODI       = s.SGD_TPR_CODIGO
			 $temasWhere
			 $wherePqr
			 $medio_busq		
			 $fechafiltro
			 $dependencia_busq2
			 order by r.RADI_FECH_RADI";
	//$db->conn->debug = TRUE;
			 
	function carga_radicados(&$mydata, &$result,$db)
	{
		$nroradicadoAnt = "xxxx";
		while($result && !$result->EOF)		
		{
			$nroradicado 	= $result->fields["RADICADO"];//******** radicado *****//
			if($nroradicadoAnt <> $nroradicado){
			
			$fechaRadicado 	= $result->fields["FECHA_RAD"];//******* fecha radicado *****//
			$tiporad 		= $result->fields["TIPO_DOC"];//******** tipo radicado *****//
			$medRecepcio 	= $result->fields["M_RECEP"];//********* medio de recepcion *****//
			$depeactu		= $result->fields["DEPE_ACTU"];//******* dependencia actual *****//
			$depeante		= $result->fields["DEPE_ANTE"];//******* dependencia ante *****//
			$padre		 	= $result->fields["PADRE"];//*********** padre *****//
			$hist1			= $result->fields["hist1"];//*********** historico1 *****//
			$hist2			= $result->fields["hist2"];//*********** historico2 *****//
			$Resp			= $result->fields["RESPUESTA"];//******* respuesta *****//
			$fechaResp		= $result->fields["FECHA_RESP"];//****** fecha respuesta *****//
			$imaResp		= $result->fields["IMAGEN_RESP"];//***** img respuesta *****//
			$anexo			= $result->fields["IMAGEN_ANEXO"];//**** anexo *****//
			$envioCorr		= $result->fields["ENVI_CORRES"];//***** correspondencia *****//	
			$expedient		= $result->fields["Expediente"];//****** expediente *****//
			$dCauCodigo		= $result->fields["SGD_DCAU_CODIGO"];
			unset($temas);
			
			$sqlSelect = "SELECT caux.SGD_CAUX_CODIGO,cau.SGD_CAU_DESCRIP as SECTOR
									, dcau.SGD_DCAU_DESCRIP as CAUSAL,caux.SGD_CAUX_CODIGO
									,caux.RADI_NUME_RADI COUNT_RADI, caux.SGD_CAUX_FECHA
									FROM SGD_CAUX_CAUSALES caux, SGD_CAU_CAUSAL cau, SGD_DCAU_CAUSAL dcau
									WHERE RADI_NUME_RADI = $nroradicado
									and caux.SGD_DCAU_CODIGO=dcau.sgd_dcau_codigo
									and dcau.SGD_CAU_CODIGO=cau.SGD_CAU_CODIGO
									";
			 
			 $rs = $db->conn->Execute($sqlSelect);
			 while (!$rs->EOF)  {
				$temaRad = $rs->fields["SECTOR"] ." / " .$rs->fields["CAUSAL"];
				$temas[] = $temaRad;
				$rs->MoveNext();
			 }
			
			$myData[]    	= array($nroradicado,
									$fechaRadicado,
									$tiporad,
									$medRecepcio,
									$depeactu,
									$depeante,
									$padre,
									$hist1,
									$hist2,
									$Resp,
									$fechaResp,
									$imaResp,
									$anexo,
									$envioCorr,	
									$expedient,
									$temas);
			}
			$nroradicadoAnt = $nroradicado;
			$result->MoveNext();
		}
		return $myData;		
	}
	//$db->conn->debug = true;
	$result	  =$db->conn->Execute($isql);
	$myData   = array();
	$myData   = carga_radicados($mydata, $result, $db);
	//print_r($myData);
	
	if ($reporte) {
		$contenido = "";
		$contenido .= '<?xml version="1.0" encoding="iso-8859-1"?>';
		$contenido .= "\n<Pendientes>\n ";
		if($myData != null){
			foreach ($myData as $item) {
				$contenido .= "	<Radicado>\n ";				
				$contenido .= "		<Nro_Radicado>" 		. $item[0]  . "</Nro_Radicado>\n ";
				$contenido .= "		<Fecha_Radicado>" 		. $item[1]  . "</Fecha_Radicado>\n ";
				$contenido .= "		<Tipo_de_Documento>"	. $item[2]  . "</Tipo_de_Documento>\n ";
				$contenido .= "		<Medio_de_Recepcion>"   . $item[3]  . "</Medio_de_Recepcion>\n ";
				$contenido .= "		<Dependencia_actual>"	. $item[4]  . "</Dependencia_actual>\n ";
				$contenido .= "		<Dependencia_anterior>" . $item[5]  . "</Dependencia_anterior>\n ";
				$contenido .= "		<Radicado_Padre>"		. $item[6]  . "</Radicado_Padre>\n ";				
				$contenido .= "		<Historico_1>"			. str_replace("&", " ", $item[7])  . "</Historico_1>\n ";
				$contenido .= "		<Historico_2>"			. str_replace("&", " ", $item[8])  . "</Historico_2>\n ";
				$contenido .= "		<Respuesta>"			. $item[9]  . "</Respuesta>\n ";
				$contenido .= "		<Fecha_Respuesta>"		. $item[10] . "</Fecha_Respuesta>\n ";
				$contenido .= "		<Imagen_Respuesta>"		. $item[11] . "</Imagen_Respuesta>\n ";
				$contenido .= "		<Anexo>"				. $item[12] . "</Anexo>\n ";
				$contenido .= "		<EnvioCorreo>"			. $item[13] . "</EnvioCorreo>\n ";
				$contenido .= "		<Expediente>"			. $item[14] . "</Expediente>\n ";
				$i=0;
				if($item[15]){
				foreach($item[15] as $itemTema){
					$i++;
					$contenido .= "		<Tema$i>"			. $itemTema . "</Tema$i>\n ";
				}
				}
				$contenido .= "	</Radicado>\n ";
				
			}
		}
		$contenido .= "</Pendientes>\n ";
		unset($item);

		$hora=date("H").date("i").date("s");
		// var que almacena el dia de la fecha
		$ddate=date('d');
		// var que almacena el mes de la fecha
		$mdate=date('m');
		// var que almacena el año de la fecha
		$adate=date('Y');
		// var que almacena  la fecha formateada
		$fecha=$adate. $mdate . $ddate;
		// guarda el path del archivo generado
		$archivo = "../bodega/tmp/Nomb"."_$fecha"."$hora" .".xls";
		$_SESSION['nomb_archivo']= $archivo;
		$fp=fopen($archivo,"wb");
		fputs($fp,$contenido);
		fclose($fp);
	}		
	?>
	
	<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<link rel="stylesheet" href="../estilos/orfeo.css">		
	</head>
	
	<body>	
		<div id="spiffycalendar" class="text"></div>
		<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
		<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
		
		<form name="form_busq_rad" action="<?=$encabezado?>" method="post">
									
			<table border="0" cellspacing="1" WIDTH="98%" class="borde_tab" valign="top" align="center">
				<tr class="titulos2">
					<td height="20" >
						<span align="left" >USUARIO</span>
					</td >
					<td height="20" colspan="2">
						<span align="left" >DEPENDENCIA DEL USUARIO</span>
					</td>									
				</tr>
				<tr class="info">
					<td height="20px">
						<span align="left" ><?=$usua_nombre?></span>
					</td>
					<td height="20px"  colspan="2">
						<span align="left" ><?=$depeNombre?></span>
					</td>						
				</tr>
				<tr class="titulos2">									
					<td height="20" >
						<span align="left" >MEDIO DE RECEPCI&Oacute;N</span>
					</td>
					<td width="100%" colspan="2">
						<span align="left" >DEPENDENCIA</span>
					</td>												
				</tr>
				<tr class="info2">
					<td>
						<?php print $cons_medRecp->GetMenu2('med_rec'
														,$med_rec
														,'0: [-Todos los medios-]'
														,true
														,5
														, 'id="med_rec" class="select" ');										
						?>
					</td>											
					<td>												
						<?php print $cons_depende->GetMenu2('dep_sel'
														,$dep_sel
														,'0: [-Todas las dependencias -]'
														,true
														,5
														, 'id="dep_sel" class="select" ');										
						?>												
					</td>
					<td width="95%">
						Fecha<br />
						<script language="javascript">
							var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "form_busq_rad", "fechai","btnDate1","<?=$fechai?>",scBTNMODE_CUSTOMBLUE);
								dateAvailable1.date = "2003-08-05";
								dateAvailable1.writeControl();
								dateAvailable1.dateFormat="yyyy-MM-dd";
						</script>(Desde)						
						<br />
						<script language="javascript">
							var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "form_busq_rad", "fechafi","btnDate2","<?=$fechafi?>",scBTNMODE_CUSTOMBLUE);
									dateAvailable2.date = "2003-08-05";
								dateAvailable2.writeControl();
								dateAvailable2.dateFormat="yyyy-MM-dd";
						</script>(Hasta)						
					</td>																			
				</tr>
			</table>
			<table><tr><td></td></tr></table>
				<TABLE border="0" cellspacing="1" WIDTH="98%" class="borde_tab" valign="top" align="center">
				<TR class="titulos2">
					<TH width="5%" >
						Tipo Documento
					</TH >
					<TH height="70%" colspan="3" align=CENTER>
						TEMA
					</TH>
				</TR>
				<tr class="listado2">
					<td width="5%" >
						<?php
						  //if($SelPqr || !$pasoSengundo) $datoss = "checked";  Este es pa dejarlo por defecto seleccionado.
							if($SelPqr ) $datoss = "checked";
						?>
						<input type="hidden" name="pasoSengundo" value="pasoSegundo">
						<input type="checkbox" name="SelPqr" <?=$datoss?>> PQR's
					</td >
					<td height="70%" colspan="3" align=CENTER>
						
						<?php
						echo "$temaSelect <**";
           $sqlPqr = "Select SGD_DCAU_DESCRIP, SGD_DCAU_CODIGO
                from SGD_DCAU_CAUSAL
								WHERE SGD_DCAU_CODIGO>=1
								order by SGD_DCAU_DESCRIP ";
						$rs = $db->conn->Execute($sqlPqr );
						if(!$temaSelect) $temaSelect = 0;
						?>
						<select name="temaSelect">
							<?php
							if($temaSelect=="99999") $datoss = " selected "; else $datoss = "  ";
							?>
							<OPTION value="99999" <?=$datoss?>> -- Solo Radicados Con Temas --</OPTION>
						  <?php
							if($temaSelect==0) $datoss = " selected "; else $datoss = "  ";
							?>
							<OPTION value=0 <?=$datoss?>> -- Todos los Radicados --</OPTION>
							<?php
							while(!$rs->EOF){
								echo "$temaSelect>".$rs->fields["SGD_DCAU_CODIGO"];
								if($temaSelect==$rs->fields["SGD_DCAU_CODIGO"]) $datoss = " selected "; else $datoss = "  ";
								
								$temaDesc = $rs->fields["SGD_DCAU_DESCRIP"];
								?>
								<OPTION value='<?=$rs->fields["SGD_DCAU_CODIGO"]?>' <?=$datoss?>><?=$temaDesc?> </OPTION>
							  <?php
								$rs->MoveNext();
							}
							?>
						</select>
					</td>									
				</tr>
				</table>
				<table><tr><td></td></tr></table>
			<table border="0" cellspacing="1" WIDTH="98%" class="borde_tab" valign="top" align="center">
				<tr class="tablas">
					<td <?=$mostrar?>>						 
						<input type="submit" value="Generar_Reporte" name="reporte" valign='middle' class="botones_largo">
					</td>
					<td align="center" <?=$ocultar?>>
						Para obtener el archivo guarde el destino del
						siguiente v&iacute;nculo al archivo: 
						<a href="<?=$_SESSION['nomb_archivo']?>" target="_blank">GENERADO</a>
					</td>
					
				<tr>							
			</table>
				
		</form>
	</body>
	</html>
