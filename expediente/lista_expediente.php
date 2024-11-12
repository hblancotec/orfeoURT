<html>
	<head>
		<title>.: Expedientes orfeo :.</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script language="JavaScript" src="../js/jquery-1.8.2.js"></script>
		<script language="JavaScript" src="../js/jquery-ui.js"></script>
		<link rel="stylesheet" href="../estilos/orfeo.css">
		
		<script language="javascript">
			
			function regresar()
			{
				window.location.reload();
				window.close();
			}
			
			
			function insertarExpediente(radicado, depe, usua, retipifica, serieactu, subseactu)
			{
	            if (radicado != '')
	            {
		            /*var tiporad = radicado.toString().substr(13, 1); 
			            
	            	var parametros = {
	                		"filtrar" : 1,
	                    	"radicado" : radicado
	                	};

	            	$.ajax({
	            		url: 'expediente/consulta.php',
	            		type: 'POST',
	            		cache: false,
	            		async: false,
	            		data:  parametros,
	            		success: function(text) {
	            			if (text == '0' || text == '2') {
		            			if (tiporad == 2 && retipifica == 0) {
		            				var opcion = confirm("Por ser documento tipificado como PQRSD para su cambio comun\u00edquese con XXXXXX del Grupo de Biblioteca y Archivo, desea enviar notificaci\u00f3n al \u00e1rea ? ");
	                                if (opcion == true) {

	                                	var parametros = {
	                                    		"rad"  : radicado,
	                                    		"tipo" : 1,
	                                    		"usua" : usua,
	                                    		"depe" : depe
	                                    	};
	                                    			
	                                    	$.ajax({
	                                    		url: '../class_control/ModificaTRD.php',
	                                    		type: 'POST',
	                                    		cache: false,
	                                    		async: false,
	                                    		data:  parametros,
	                                    		success: function(text) {
	                                    			if(text != '1') {
	                                					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
	                                    			} else {
	                                    				//alert("Error en el proceso, consulte el administrador del sistema." + text);
	                                    			} 
	                                    			document.form2.submit();
	                                    		},
	                                    		error: function(text) { alert('Se ha producido un error ' + text); }
	                                    	});
	                                }
		            			} else {
		            				window.open( "<?=$ruta_raiz?>/expedientes/incluirEnExpediente.php?<?=SID?>&numRad=<?=$verrad?>&krd=<?=$krd?>&tdoc=<?=$tpdoc_rad?>","Incluir_Expediente","height=500px,width=800px,scrollbars=yes");
		            			}
	            			} 
	            			else if (text == '1') {
		            			alert('¡ El radicado no tiene documento asociado !');
	            			}
	            			else if (text == '3') {
	            				window.open( "<?=$ruta_raiz?>/expedientes/incluirEnExpediente.php?<?=SID?>&numRad=<?=$verrad?>&krd=<?=$krd?>&tdoc=<?=$tpdoc_rad?>","Incluir_Expediente","height=500px,width=800px,scrollbars=yes");
	            			}
	            			else if (text == '4') {
		            			alert('¡ Debe aprobarse el cambio de la TRD !');
	            			}
	            		},
	            		error: function(text) { 
		            		alert('Se ha producido un error ' + text); 
		            	}
	            	}); */

	            	window.open( "<?=$ruta_raiz?>/expedientes/incluirEnExpediente.php?<?=SID?>&numRad=<?=$verrad?>&krd=<?=$krd?>&tdoc=<?=$tpdoc_rad?>&serieactu="+serieactu+"&subseactu="+subseactu+"","Incluir_Expediente","height=500px,width=800px,scrollbars=yes");
	            }
			}
			
			
			function crearExpedientes(rad, depe, usua, retipifica, serieactu, subseactu) 
			{
				debugger;
				/*var tiporad = rad.toString().substr(13, 1); 
				
				var parametros = {
                		"filtrar" : 1,
                    	"radicado" : rad
                	};

            	$.ajax({
            		url: 'expediente/consulta.php',
            		type: 'POST',
            		cache: false,
            		async: false,
            		data:  parametros,
            		success: function(text) {
            			if (text == '0' || text == '2') {
            				if (tiporad == 2 && retipifica == 0) {
	            				var opcion = confirm("Por ser documento tipificado como PQRSD para su cambio comun\u00edquese con XXXXXX del Grupo de Biblioteca y Archivo, desea enviar notificaci\u00f3n al \u00e1rea ? ");
                                if (opcion == true) {

                                	var parametros = {
                                    	"rad"  : rad,
                                    	"tipo" : 1,
                                    	"usua" : usua,
                                    	"depe" : depe
                                    };
                                    			
                                    $.ajax({
                                    	url: '../class_control/ModificaTRD.php',
                                    	type: 'POST',
                                    	cache: false,
                                    	async: false,
                                    	data:  parametros,
                                    	success: function(text) {
                                    		if(text != '1') {
                                				alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
                                    		} else {
                                    			//alert("Error en el proceso, consulte el administrador del sistema." + text);
                                    		} 
                                    		document.form2.submit();
                                    	},
                                    	error: function(text) { alert('Se ha producido un error ' + text); }
                                    });
                                }
            				} else {
            					window.open("<?=$ruta_raiz?>/expedientes/crearExpedientes.php?<?=SID?>&nurad=<?=$verrad?>&krd=<?=$krd?>&tdoc=<?=$tpdoc_rad?>","Crear_Expediente","height=500,width=555,scrollbars=yes");
            				}
            			} 
            			else if (text == '1') {
	            			alert('¡ El radicado no tiene documento asociado !');
            			}
            			else if (text == '3') {
            				window.open("<?=$ruta_raiz?>/expedientes/crearExpedientes.php?<?=SID?>&nurad=<?=$verrad?>&krd=<?=$krd?>&tdoc=<?=$tpdoc_rad?>","Crear_Expediente","height=500,width=555,scrollbars=yes");	
            			}
            			else if (text == '4') {
	            			alert('¡ Debe aprobarse el cambio de la TRD !');
            			}
            		},
            		error: function(text) { 
	            		alert('Se ha producido un error ' + text); 
	            	}
            	});*/

				window.open("<?=$ruta_raiz?>/expedientes/crearExpedientes.php?<?=SID?>&nurad=<?=$verrad?>&krd=<?=$krd?>&tdoc=<?=$tpdoc_rad?>&serieactu="+serieactu+"&subseactu="+subseactu+"","Crear_Expediente","height=600,width=655,scrollbars=yes");
			}
			
			
			function crearExpediente()
			{
				numExpediente = document.getElementById('num_expediente').value;
				numExpedienteDep = document.getElementById('num_expediente').value.substr(4,3);
				if(numExpedienteDep==<?=$dependencia?>) {
					if(numExpediente.length==13) {
						insertarExpedienteVal = true;
					} 
					else {
						alert("Error. El numero de digitos debe ser de 13.");
						insertarExpedienteVal = false;
					}
				}
				else {
					alert("Error. Para crear un expediente solo lo podra realizar con el codigo de su dependencia. ");
					insertarExpedienteVal = false;
				}
				if(insertarExpedienteVal == true) {
					respuesta = confirm("Esta apunto de crear el EXPEDIENTE No. " + numExpediente + " Esta Seguro ? ");
					insertarExpedienteVal = respuesta;
					if(insertarExpedienteVal == true) {
						dv = digitoControl(numExpediente);
						document.getElementById('num_expediente').value = document.getElementById('num_expediente').value + "E" + dv;
						document.getElementById('funExpediente').value = "CREAR_EXP"
						document.form2.submit();
					}
				}
			}
			
			
			function excluirExpediente() 
			{
				window.open( "<?=$ruta_raiz?>/expediente/excluirExpediente.php?sessid=<?=session_id()?>&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>","HistExp<?=$fechaH?>","height=300,width=600,scrollbars=yes" );
			}
			
			function verHistExpediente(numeroExpediente,codserie,tsub,tdoc,opcionExp) 
			{
				<?php
				$isqlDepR ="SELECT	RADI_DEPE_ACTU,
									RADI_USUA_ACTU
							FROM	RADICADO
							WHERE	RADI_NUME_RADI = $numrad";
				$rsDepR = $db->conn->Execute($isqlDepR);
				$coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
				$codusua = $rsDepR->fields['RADI_USUA_ACTU'];
				$ind_ProcAnex = "N";
				?>
				window.open("<?=$ruta_raiz?>/expediente/verHistoricoExp.php?sessid=<?=session_id()?>&opcionExp="+opcionExp+"&numeroExpediente="+numeroExpediente+"&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>","HistExp<?=$fechaH?>","height=800,width=1060,scrollbars=yes");
			}
			
			
			function adjuntarArchivos(numeroExpediente,numrad)
			{
				window.open( "<?=$ruta_raiz?>/expedientes/adjuntarArchivos.php?<?=session_name()?>=<?=session_id()?>&krd=<?=$krd?>&numrad="+ numrad + "&num_expediente="+ numeroExpediente,"Adjuntar","height=720,width=520,left=400,top=100,scrollbars=yes" );
			}
			
			
			function verHistoricoImagen(radicado, anexo)
			{
				nombreventana= "ventHistAnexDoc";
				url="ver_hist_imagen.php?type=a&id="+anexo+"&rad="+radicado;
				window.open(url,nombreventana,'height=400,width=630');
			}

			
			function datosDelExp(numeroExpediente)
			{
				window.open("<?=$ruta_raiz?>/expediente/ver_datos_exp.php?sessid=<?=session_id()?>&numeroExpediente="+numeroExpediente+"&krd=<?=$krd?>","Modifcar_Expediente","height=300,width=800,left=400,top=100,scrollbars=yes");
			}
			
			
			function ordenarAnex(numeroExpediente)
			{
				window.open("<?=$ruta_raiz?>/expediente/ordenarAnexosExp.php?sessid=<?=session_id()?>&numeroExpediente="+numeroExpediente+"&krd=<?=$krd?>","Ordenar Anexos de Expediente","height=500,width=900,left=400,top=100,scrollbars=yes");
			}
			
			
			function verHistoricoImagenE(anexo)
			{
				nombreventana= "ventHistAnexExp";
				url="ver_hist_imagen.php?type=e&id="+anexo;
				window.open(url,nombreventana,'height=400,width=630');
			}


			function verHistoricoImagenR(radicado)
			{
				nombreventana= "ventHistRadDoc";
				url="ver_hist_imagen.php?type=r&rad="+radicado;
				window.open(url,nombreventana,'height=400,width=630');
			}
		</script>
		
	</head>
	<body bgcolor="#FFFFFF" topmargin="0">
		
<?php
include_once ("$ruta_raiz/include/tx/Expediente.php");

if ( $num_expediente != "" && !isset( $_POST['expIncluido'][0] ) ) {
	$numExpediente = $num_expediente;
}
else if ( isset( $_POST['expIncluido'][0] ) && $_POST['expIncluido'][0] != "" ) {
	$numExpediente = $_POST['expIncluido'][0];
}

function sort_by_orden ($a, $b) {
    return $a['fecha'] - $b['fecha'];
}

$cambio = 0;
$coditrdx = "SELECT S.SGD_TPR_DESCRIP as TPRDESCRIP, R.RADI_DEPE_ACTU, R.RADI_USUA_ACTU, R.SGD_CAMBIO_TRD
            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO S ON R.TDOC_CODI = S.SGD_TPR_CODIGO
            WHERE R.RADI_NUME_RADI = $numrad";
$res_coditrdx = $db->conn->Execute($coditrdx);
if ($res_coditrdx && !$res_coditrdx->EOF) {
    $TDCactu = $res_coditrdx->fields['TPRDESCRIP'];
    $usuactu = $res_coditrdx->fields['RADI_USUA_ACTU'];
    $depeactu = $res_coditrdx->fields['RADI_DEPE_ACTU'];
    $cambio = ($res_coditrdx->fields['SGD_CAMBIO_TRD'] == null ? 0 : $res_coditrdx->fields['SGD_CAMBIO_TRD']);
}

$serieactu = 0;
$subseactu = 0;
$docuactu = 0;
$sqlDt = "SELECT R.SGD_MRD_CODIGO, R.RADI_NUME_RADI, M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO
            FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
            WHERE RADI_NUME_RADI = $numrad ORDER BY R.SGD_RDF_FECH desc ";
$rsDt = $db->conn->Execute($sqlDt);
if ($rsDt && ! $rsDt->EOF) {
    $serieactu = $rsDt->fields["SGD_SRD_CODIGO"];
    $subseactu = $rsDt->fields["SGD_SBRD_CODIGO"];
    $docuactu = $rsDt->fields["SGD_TPR_CODIGO"];
}
$tiporad = substr($numrad, - 1);

$retipifica = $_SESSION['retipificatrd'];
$num_expediente = $numExpediente;
$expediente = new Expediente($db);
$expediente->getExpediente($num_expediente);

if($radi_nume_deri){
	$isql = "SELECT A.RADI_FECH_RADI,
					A.RADI_PATH,
					A.RA_ASUN,
					A.RADI_CUENTAI,
					A.RADI_NUME_RADI
			FROM	RADICADO A
			WHERE	A.radi_nume_radi = $radi_nume_deri";
	$rs = &$db->conn->Execute($isql);
	
	if($rs) {
		$fechaRadicadoPadre = $rs->fields["RADI_FECH_RADI"];
		$radicado_d		= $rs->fields["RADI_NUME_RADI"];
		$radicado_path	= $rs->fields["RADI_PATH"];
		$raAsunAnexo	= $rs->fields["RA_ASUN"];
		$cuentaIAnexo	= $rs->fields["RADI_CUENTAI"];
		
		if($radicado_path) {
			$ref_radicado = "<a href='bodega/$radicado_path'>$radicado_d </a>";
		}
		else {
			$ref_radicado = "$radicado_d";
		}
?>
		
		<table  cellspacing="5" width=100% align="center" class="borde_tab">
			<tr>
				<td class="titulos5" colspan="4" width=25%>
					<span class="leidos"> DOCUMENTO <?=$nombre_deri ?> <?= $ref_radicado ?> </span>
				</td>
			
				<td class="listado5" width=20%>
					<span class="leidos2"> Fecha Rad:
						<a href="<?=$ruta_raiz?>/verradicado.php?verrad=<?=$radicado_d ?>&<?=session_name()?>=<?=session_id()?>&krd=<?=$krd?>" target="VERRAD<?=$radicado_d?>">
							<?=$fechaRadicadoPadre?>
						</a>
					</span>
				</td>
				<td class="listado5"> <span class="leidos2">Asunto: <?=$raAsunAnexo ?> </span> </td>
				<td class="listado5"> <span class="leidos2">Ref: <?=$cuentaIAnexo ?> </span> </td>
			</tr>
		</table>
		
<?php
	}
}


$sqlExp	= "	SELECT	E.SGD_EXP_NUMERO,
					E.SGD_EXP_FECH
			FROM	SGD_EXP_EXPEDIENTE E inner join SGD_SEXP_SECEXPEDIENTES s on E.SGD_EXP_NUMERO = s.sgd_exp_numero 
			WHERE	E.RADI_NUME_RADI = $numrad AND 
					E.SGD_EXP_ESTADO <> 2 and s.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas') ";
$rsExp = $db->conn->Execute($sqlExp);

if( $rsExp->RecordCount() == 0 ) {
	
?>
		<table border="0" width="100%" class="borde_tab" align="center" class="titulos2">
			<tr class="titulos2">
				<td align="center" class="titulos2">
					<span class="leidos2" class="titulos2" align="center">
						<H2>ESTE DOCUMENTO NO HA SIDO INCLUIDO EN NING&Uacute;N EXPEDIENTE P&Uacute;BLICO</H2>
					</span>
				</td>
				
				<td align="left" class="titulos2" width="10%">
					
					<?php
					if($_SESSION["usuaPermExpediente"] > 0){
					?>
					
					<a href="#" onClick="crearExpedientes(<?=$numrad?>, <?=$coddepe?>, <?=$codusua?>, <?=$retipifica?>, <?=$serieactu?>, <?=$subseactu?>)"> <span class="leidos"> <b> CREAR </b> </span> </a>
					<br> <br> &nbsp;
					
					<?php
					}
					?>
					<a href="#" onClick="insertarExpediente(<?php echo $numrad ?>, <?=$coddepe?>, <?=$codusua?>, <?=$retipifica?>, <?=$serieactu?>, <?=$subseactu?>);" > <span class="leidos2"> <b>INCLUIR EN</b> </span> </a>
				</td>
			</tr>
		</table>
  
<?php
}
else{
	
?>
		
		<table border="0" width="100%" class="borde_tab" align="center" class="titulos2">
			<tr class="titulos2">
				
				
<?php

	$sqlExpPriv = "	SELECT	E.SGD_EXP_NUMERO,
							dbo.VALIDAR_ACCESO_RADEXP (0, 'E.SGD_EXP_NUMERO', '$krd') AS PERMISO
					FROM	SGD_EXP_EXPEDIENTE E
							JOIN SGD_SEXP_SECEXPEDIENTES S ON
								S.SGD_EXP_NUMERO = E.SGD_EXP_NUMERO AND
								S.SGD_SEXP_PRIVADO = 1
					WHERE	E.SGD_EXP_ESTADO <> 2 AND
							E.RADI_NUME_RADI = $numrad 
                            and S.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas') ";
	$privExp = $db->conn->Execute($sqlExpPriv);

	### ENTRA SI VIENEN EXPEDIENTES PRIVADOS
	if($privExp->RecordCount() != 0){
?>
				
				
				<td class="titulos2" width="30%">
				
<?php

		while(!$privExp->EOF) {
			$priExpedientes = $privExp->fields['SGD_EXP_NUMERO'];
			$salidaPriv .= $priExpedientes."<br/>";
			$Exped_Priv[] = $privExp->fields['SGD_EXP_NUMERO'];	
			
			if ($num_expediente == $privExp->fields['SGD_EXP_NUMERO']){
				$num_expediente = '';
			}
			
			$privExp->MoveNext();

		}

		if(!empty($salidaPriv)){
			
?>					
					<span class='leidos2' align="left">
						<h4>
							Documento incluido en el(los) expediente(s) con car&aacute;cter privado y solo 
							ser&aacute;n visibles por usuarios de la misma dependencia a la que pertenece(n)
						</h4>
					</span>
				</td>
				<td align="center" width="15%"> 
					<div style="background:#E3E8EC; padding:6px; width:150px; height:40px; overflow:auto; text-align:left; "> 
						<?php echo $salidaPriv; ?>
					</div>
<?php
		}
		else {
			echo "<br>Documento incluido en expedientes que solo pertenecen a esta dependencia.";
		}

?>
				
				</td>

				
<?php
	}
	### FIN - ENTRA SI VIENEN EXPEDIENTES PRIVADOS

	
	
	### SI QUIEN CONSULTA TIENE ROL DE AUDITOR, SOLO SE MOSTRARAN LOS EXPEDIENTES DE
	### LAS DEPENDENCIAS Y ANHOS EN LOS CUALES TIENE PERMISO
	$sqlRol = "	SELECT	SGD_ROL_CODIGO
				FROM	USUARIO
				WHERE	USUA_LOGIN = '$krd'";
	$rol = $db->conn->Getone($sqlRol);
	
	
	if ($rol == 3){
		$sqlExped	= "	SELECT	A.SGD_EXP_NUMERO
						FROM	SGD_EXP_EXPEDIENTE A 
								JOIN SGD_SEXP_SECEXPEDIENTES S ON
									S.SGD_EXP_NUMERO = A.SGD_EXP_NUMERO AND 
									(S.SGD_SEXP_PRIVADO <> 1 OR SGD_SEXP_PRIVADO IS NULL)
						WHERE	A.RADI_NUME_RADI = $numrad
								AND A.SGD_EXP_ESTADO <> 2
								AND dbo.VALIDAR_ACCESO_RADEXP (0, A.SGD_EXP_NUMERO, '$krd') = 0
                                and S.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas') ";
	}
	else{
		$sqlExped ="SELECT	a.SGD_EXP_NUMERO
					FROM	SGD_EXP_EXPEDIENTE a INNER JOIN SGD_SEXP_SECEXPEDIENTES b ON a.sgd_exp_numero = b.sgd_exp_numero
					WHERE	a.RADI_NUME_RADI = $numrad
							and a.SGD_EXP_ESTADO <> 2
                            and b.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas')
							and a.sgd_exp_numero not in (	SELECT	a.sgd_exp_numero
															FROM	SGD_SEXP_SECEXPEDIENTES a,
																	SGD_EXP_EXPEDIENTE b
															WHERE	b.radi_nume_radi = $numrad
																	and a.sgd_exp_numero = b.sgd_exp_numero
																	and a.depe_codi <> $depeCodi
																	and SGD_SEXP_PRIVADO = 1  )";
	}
	$rsExped = $db->conn->Execute($sqlExped);

	if($rsExped->RecordCount() == 0 ) {
		
?>
				
				<td align="center" >
					<span class='leidos2'>
						<h3>ESTE DOCUMENTO NO HA SIDO INCLUIDO EN UN EXPEDIENTE P&Uacute;BLICO</h3>
					</span>
				</td>
				<td align="left" width="10%">
					<a href="#" onClick="insertarExpediente(<?php echo $numrad ?>, <?=$coddepe?>, <?=$codusua?>, <?=$retipifica?>, <?=$serieactu?>, <?=$subseactu?>);" > <span class="leidos2"> <b>INCLUIR EN</b> </span> </a>
				</td
<?php
	}
	else {
		
		if ($num_expediente == ''){
			$numExpediente = $rsExped->fields['SGD_EXP_NUMERO'];
			$num_expediente = $numExpediente;
		}
?>
				<td align="center" >
					<span class='titulos5'>
						<h2>Documento incluido en (los) siguiente(s) expediente(s).</h2></p>
					</span>
				</td>
				
				<td align="center" valign="middle">
					
<?php
		print $rsExped->GetMenu( 'expIncluido', $expIncluido, false, true, 4, "class='select' onChange='document.form2.submit();'", false );
?>			
		
				</td>
				
				<td align="left" width="10%"> 
					
					<?php
					if($_SESSION["usuaPermExpediente"] > 0){
					?>
					
					<a href="#" onClick="crearExpedientes(<?=$numrad?>, <?=$coddepe?>, <?=$codusua?>, <?=$retipifica?>, <?=$serieactu?>, <?=$subseactu?>)"> <span class="leidos"> <b> CREAR </b> </span> </a>
					<br><br>&nbsp;
					
					<?php
					}
					?>
					
					<a href="#" onClick="insertarExpediente(<?php echo $numrad ?>, <?=$coddepe?>, <?=$codusua?>, <?=$retipifica?>, <?=$serieactu?>, <?=$subseactu?>);" > <span class="leidos2"> <b>INCLUIR EN</b> </span> </a>
					<br><br>&nbsp;
					
					<?php
					if($_SESSION['depecodi'] == substr($numExpediente,4,4) ){
					?>
					
					<a href="#" onClick="excluirExpediente()"> <span class="leidos"> <b> EXCLUIR DE </b> </span> </a>
					
					<?php
					}
					?>
					
				</td>
			</tr>
		</table>
			
<?php
		
		$sqlInfExp = "	SELECT	S.SGD_EXP_NUMERO,
								S.SGD_SEXP_PAREXP1,
								S.SEXP_BPIN,
								D.DEPE_NOMB,
								D.DEPE_CODI,
								U.USUA_NOMB,
								S.SGD_SEXP_FECH,
								SR.SGD_SRD_DESCRIP,
								SB.SGD_SBRD_DESCRIP,
								S.SGD_SEXP_PRIVADO,
								B.NOMBRE_DE_LA_EMPRESA,
								SEC.EXP_SECT_NOMBRE,
								P.SGD_EPRY_NOMBRE_CORTO
						FROM	SGD_SEXP_SECEXPEDIENTES S
								LEFT JOIN DEPENDENCIA D ON 
									D.DEPE_CODI = S.DEPE_CODI
								LEFT JOIN USUARIO U ON
									U.USUA_DOC = S.USUA_DOC_RESPONSABLE
								LEFT JOIN SGD_SRD_SERIESRD SR ON
									SR.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
								LEFT JOIN SGD_SBRD_SUBSERIERD SB ON
									SB.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO AND
									SB.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
								LEFT JOIN BODEGA_EMPRESAS B ON
									B.IDENTIFICADOR_EMPRESA = SGD_EMP_ID
								LEFT JOIN SGD_EXP_SECTORES SEC ON
									SEC.EXP_SECT_ID = S.EXP_SECT_ID
								LEFT JOIN SGD_EPRY_EPROYECTO P ON
									P.SGD_EPRY_CODIGO = S.SGD_EPRY_CODIGO
						WHERE	S.SGD_EXP_NUMERO = '$numExpediente'";
		$rsInfExp = $db->conn->Execute($sqlInfExp);
		
		$numExp = $rsInfExp->fields['SGD_EXP_NUMERO'];
		$nomExp = $rsInfExp->fields['SGD_SEXP_PAREXP1'];
		$depExp = $rsInfExp->fields['DEPE_NOMB'];
		$depCod = $rsInfExp->fields['DEPE_CODI'];
		$resExp = $rsInfExp->fields['USUA_NOMB'];
		$trdExp = $rsInfExp->fields['SGD_SRD_DESCRIP']." / ".$rsInfExp->fields['SGD_SBRD_DESCRIP'];
		$fecExp = $rsInfExp->fields['SGD_SEXP_FECHAINICIO'];
                //$fecExpFin = $rsInfExp->fields['SGD_SEXP_FECHAFIN'];
                //$faseExp = $rsInfExp->fields['SGD_SEXP_FASE'];
                //$estadoExp = $rsInfExp->fields['SGD_SEXP_CERRADO'];
		$bpinExp = $rsInfExp->fields['SEXP_BPIN'];
		$sectExp = $rsInfExp->fields['EXP_SECT_NOMBRE'];
		$empExp  = $rsInfExp->fields['NOMBRE_DE_LA_EMPRESA'];
		$nombreP = $rsInfExp->fields['SGD_EPRY_NOMBRE_CORTO'];
		
		if ($rsInfExp->fields['SGD_SEXP_PRIVADO']){
			$privExp = 'Privado';
		}
		else {
		    $privExp = utf8_encode('Público');
		}
		
		### SE REALIZA EL REGISTRO EN EL LOG, DE LA CONSULTA DEL EXPEDIENTE
		$usDoc	 = $_SESSION['usua_doc'];
		$usLogin = $_SESSION['login'];
		$dir = substr($_SERVER['HTTP_REFERER'], 0, 140);

		$sql = "INSERT INTO 
				SGD_HIST_CONSULTAS (	USUA_DOC,
										USUA_LOGIN,
										SGD_TTR_CODIGO,
										RADI_NUME_RADI,
										SGD_EXP_NUMERO,
										HIST_CON_MODULO)
				VALUES	(	'".$usDoc."',
							'".$usLogin."',
							98,
							".$numRad.",
							'".$numExpediente."',	
							'".$dir."' )";
		$rs = $db->conn->Execute($sql);
		
?>		
		
		<table border="0" width="100%" class='borde_tab' align="center" cellspacing="1" >
			<tr>
				<td class="titulos2" width="15%"> N&uacute;mero de Expediente </td>
				<td class="listado2" width="30%" style="color: #ff0000;"> <?php echo $numExp; ?> </td>
				
				<td class="titulos2" width="15%"> Nombre del Expediente </td>
				<td class="listado2" width="40%" style="color: #ff0000;"> <?php echo $nomExp; ?> </td>
			</tr>
			
			<tr>
				<td class="titulos2" > Dependencia </td>
				<td class="listado2" > <?php echo $depExp;?> </td>
				
				<td class="titulos2" > Responsable </td>
				<td class="listado2" > <?php echo $resExp; ?> </td>
			</tr>
			
			<tr>
				<td class="titulos2" > Serie/SubSerie </td><!-- IBISCOM 2018-10-30 CAMBIO de nombre de TRD a subSerie  -->
				<td class="listado2"> <?php echo $trdExp;?> </td>
                
                <td class="titulos2"> Fecha inicio </td>
				<td class="listado2" > <?php echo $expediente->fecExp ?> </td>
			</tr>
			<tr>
				<td class="titulos2" > Fase de Archivo </td>
				<td class="listado2" > <?php echo $expediente->faseExpDisplay ?> </td>
				
				<td class="titulos2" > Fecha Fin </td>
                <td class="listado2" > <?php echo $expediente->fecExpFin ?> </td>
			</tr>
                        <tr>
				<td class="titulos2" > Estado </td>
				<td class="listado2" > <?php echo $expediente->estadoExpDisplay?> </td>
                
				<td class="titulos2"> Nivel de Seguridad </td>
				<td class="listado2" > <?php echo $privExp; ?> </td>
			</tr>
			<tr>
				<td class="titulos2" > BPIN </td>
				<td class="listado2" > <?php echo $bpinExp; ?> </td>
				
				<td class="titulos2" > Historia del Expediente </td>
				<td class="listado2" > <input type="button" value="..." onClick="verHistExpediente('<?=$numExp?>');"> </td>
			</tr>
						
			<tr>
				<td class="titulos2" > Sector </td>
				<td class="listado2" > <?php echo $sectExp; ?> </td>
				
				<td class="titulos2" > Adjuntar archivos al Exp. </td>
				<td class="listado2" > <input type="button" value="..." onClick="adjuntarArchivos('<?=$numExp?>','<?=$numrad?>');"> </td>
			</tr>
			
			<tr>
				<td class="titulos2" > Entidad </td>
				<td class="listado2" > <?php echo $empExp; ?> </td>
				
				<td class="titulos2" > Datos del Expediente </td>
				<td class="listado2"> 
					
					<?php 
					if ($_SESSION["usuaPermExpediente"]>1 || $expediente->responsable == $_SESSION['usua_doc']) {
					?>
					
						<input type="button" value="..." onClick="datosDelExp('<?=$numExp?>');"> 
					
					<?php 
					}
					?>
					
				</td>
			</tr>
			
			<tr>
				<td class="titulos2" > Proyecto </td>
                <td class="listado2" > <?php echo $nombreP; ?> </td>
				
				<td class="titulos2" > Ordenar anexos </td>
				<td class="listado2"> 
					
					<?php 
					$sqlOrdenar = "	SELECT	USUA_PERM_ORDENAR
									FROM	USUARIO
									WHERE	USUA_DOC = '".$usDoc."'";
					$ordenar = $db->conn->Getone($sqlOrdenar);
					
					if ($ordenar == 1) {
					?>
					
						<input type="button" value="..." onClick="ordenarAnex('<?=$numExp?>');"> 
					
					<?php 
					}
					?>
					
				</td>
			</tr>
		</table>
		
		<br>
		
		<table border="0" width="100%" class='borde_tab' align="center" cellspacing="1">
			<input name="num_expediente" type="hidden" id='num_expediente' value="">
			<input type="hidden" name='funExpediente' id='funExpediente' value="" >
			<input type="hidden" name='menu_ver_tmp' id='funExpediente' value="4" >				
			<tr class='timparr'>
				<td colspan="6" class="titulos4" align="center">
					<span class="titulos4" >Documentos Pertenecientes al expediente</span>
				</td>
			</tr>
			<tr class='timparr'>
				<td>
			
					<table border="0" width="100%" class="borde_tab" align="center" cellpadding="0" cellspacing="0">
						<tr class="listado5" style="height: 25px;" >
							<td>&nbsp;</td>
							<td class="titulos5" align="center"> Radicado </td>
							<td class="titulos5" align="center"> Log Imagen </td>
							<td class="titulos5" align="center"  width="15%"> Fecha Radicaci&oacute;n </td>
							<td class="titulos5" align="center"> Tipo Documental </td>
							<td class="titulos5" align="center"> Asunto </td>
							<td class="titulos5"> Carpeta </td>
						</tr>

<?php
		### <!-- INICIO MOSTRAR DOCUMENOS ELECTRONICOS -->
		### consulta: documentos electronicos adjuntos al expediente
$consulta ="SELECT	a.ANEXOS_EXP_NOMBRE AS NOMBRE,
							convert(varchar(10), a.ANEXOS_EXP_FECH_CREA,103) AS TIEMPO,
							b.SGD_TPR_DESCRIP AS TD,
							a.ANEXOS_EXP_DESC AS DESCRIPT,
							a.ANEXOS_EXP_PATH AS RUTA,
							a.ANEXOS_EXP_ID	AS IDEA,
							a.ANEXOS_EXP_ORDEN,
                            CASE
								WHEN M.fecha_produccion IS NULL THEN convert(varchar(10), a.ANEXOS_EXP_FECH_CREA,103)
								ELSE convert(varchar(10), M.fecha_produccion,103)
							 END as PROD
					FROM	SGD_ANEXOS_EXP a INNER JOIN SGD_TPR_TPDCUMENTO b on a.SGD_TPR_CODIGO = b.SGD_TPR_CODIGO
							LEFT JOIN METADATOS_DOCUMENTO M ON M.id_anexo = convert(varchar(20),a.ANEXOS_EXP_ID)
					WHERE	SGD_EXP_NUMERO = '$numExpediente'
							AND a.ANEXOS_EXP_ESTADO <> 1
					ORDER BY a.ANEXOS_EXP_FECH_CREA, a.ANEXOS_EXP_ORDEN ";

		$adjun 	=	$db->conn->Execute($consulta);
		$htmlPrintIni1	= '<tr class="listado1"  class="tpar">';
		$htmlPrintIni2	= '<td style="margin-left: 10px; margin-right: 10px; ' .
						'padding-bottom: 5px;'.
						'padding-left: 10px; padding-right: 10px;">';
		$htmlPrintFin2	= '</td>';
		$htmlPrintFin1	= '</tr>';
		$contador		= 1;

		while(!$adjun->EOF and !empty($adjun)) {
			$nombre		= $adjun->fields['NOMBRE'];
			//Ibis: Se incluye if para colocar como orden 1 en indice electronico
			$varTiempoIndice = strpos($nombre,"indice_");
			$tiempo		= $adjun->fields['PROD'];
			if ($varTiempoIndice !== false){
			    $tiempo2 = $tiempo;
			    $tiempo ="1900/01/01";
			}
			//Ibis: Se incluye if para colocar como orden 1 en indice electronico
			$td			= $adjun->fields['TD'];
			$descript	= $adjun->fields['DESCRIPT'];
			$ruta		= $adjun->fields['RUTA'];
			$ruta		= str_replace("\\","/",$ruta);
			$icono		= '<img name="imgAdjuntos" src="img/silk/icons/attach.png" border="0">';
			$ruta		= '<a href=../bodega/'.$ruta.'>'.$nombre.'</a>';
			$rutaHistImg = '<center><a href=javascript:verHistoricoImagenE('.$adjun->fields['IDEA'].') ><img  border="0" src="imagenes/log.png" alt="Log del documento" title="Log del documento" height="12" width="12" /></a></center>';
			list($diaa,$mess,$anno)	=	explode('/',$tiempo);
			$fecha_operar =	mktime(0,0,0,$mess,$diaa,$anno);
			
			//Ibis: Se incluye if para colocar como orden 1 en indice electronico
			if ($varTiempoIndice !== false){
			    $htmlRe		=	$htmlPrintIni1.
			    $htmlPrintIni2 .$icono.			$htmlPrintFin2.
			    $htmlPrintIni2 .$ruta.			$htmlPrintFin2.
			    $htmlPrintIni2 .$rutaHistImg.	$htmlPrintFin2.
			    $htmlPrintIni2 .'<center>'.$tiempo2.'</center>'.$htmlPrintFin2.
			    $htmlPrintIni2 .$td.			$htmlPrintFin2.
			    $htmlPrintIni2 .$descript.		$htmlPrintFin2.
			    $htmlPrintIni2 .'       '.		$htmlPrintFin2.
			    $htmlPrintFin1;
			}else{
			    $htmlRe		=	$htmlPrintIni1.
			    $htmlPrintIni2 .$icono.			$htmlPrintFin2.
			    $htmlPrintIni2 .$ruta.			$htmlPrintFin2.
			    $htmlPrintIni2 .$rutaHistImg.	$htmlPrintFin2.
			    $htmlPrintIni2 .'<center>'.$tiempo.'</center>'.$htmlPrintFin2.
			    $htmlPrintIni2 .$td.			$htmlPrintFin2.
			    $htmlPrintIni2 .$descript.		$htmlPrintFin2.
			    $htmlPrintIni2 .'       '.		$htmlPrintFin2.
			    $htmlPrintFin1;
			}

			$adjArr[]	=	 array( 'fecha' => $fecha_operar,
							 'htmlRe' => $htmlRe);
			$contador ++;
			$adjun->MoveNext();
		}
		
		if (is_array($adjArr) && count($adjArr) > 0) {
		  //uasort($adjArr, 'sort_by_orden');
		  //asort($adjArr, "fecha");
		}
				    
		if ( $num_expediente != "" && !isset( $_POST['expIncluido'][0] ) ) {
			$expedienteSeleccionado = $num_expediente;
		}
		else if ( isset( $_POST['expIncluido'][0] ) && $_POST['expIncluido'][0] != "" ) {
			$expedienteSeleccionado = $_POST['expIncluido'][0];
		}

		if( $expedienteSeleccionado ) {
			include_once($ruta_raiz.'/include/query/queryver_datosrad.php');
			$fecha = $db->conn->SQLDate("d-m-Y H:i A","a.RADI_FECH_RADI");

			
			$isql = "SELECT T.SGD_TPR_DESCRIP,
							CONVERT(VARCHAR(10),A.RADI_FECH_RADI,103) AS FECHA_RAD,
							A.RADI_CUENTAI,
							A.RA_ASUN,
							A.RADI_PATH,
							$radi_nume_radi AS RADI_NUME_RADI,
							A.RADI_USUA_ACTU,
							A.RADI_DEPE_ACTU,
							E.SGD_EXP_CARPETA,
							dbo.VALIDAR_ACCESO_RADEXP (A.RADI_NUME_RADI, '', '$krd') AS PERMISO
					FROM	SGD_EXP_EXPEDIENTE E
							JOIN RADICADO A ON
								A.RADI_NUME_RADI = E.RADI_NUME_RADI
							JOIN SGD_TPR_TPDCUMENTO T ON
								T.SGD_TPR_CODIGO = A.TDOC_CODI
					WHERE	E.SGD_EXP_NUMERO = '$expedienteSeleccionado' AND
							E.SGD_EXP_ESTADO != 2
					ORDER BY A.RADI_FECH_RADI";
			
			$rs = $db->conn->Execute($isql);
			$i = 0;

			// ********************* INICIO DEL WHILE MOSTRAR ANEXOS, ADJUNTOS***************
			while(!$rs->EOF) {
				$radicado_d		= "";
				$radicado_path	= "";
				$radicado_fech	= "";
				$radi_cuentai	= "";
				$rad_asun		= "";
				$tipo_documento_desc = "";
				
				$radicado_d		= $rs->fields["RADI_NUME_RADI"];
				$radicado_path	= $rs->fields["RADI_PATH"];
				$radicado_fech	= $rs->fields["FECHA_RAD"];
				$radi_cuentai	= $rs->fields["RADI_CUENTAI"];
				$rad_asun		= $rs->fields["RA_ASUN"];
				$subexpediente	= $rs->fields["SGD_EXP_CARPETA"];
				$nombreSubExp	= $rs->fields["SGD_EXP_NOMBRESUBEXP"];
				$usu_cod		= $rs->fields["RADI_USUA_ACTU"];
				$radi_depe		= $rs->fields["RADI_DEPE_ACTU"];			
				$permiso		= $rs->fields["PERMISO"];
				$tipo_documento_desc	= $rs->fields["SGD_TPR_DESCRIP"];
				
				list($diaa,$mess,$anno)	= explode('/',$radicado_fech);
				
				$fecha_operar	=	mktime(0,0,0,$mess,$diaa,$anno);

				if(!empty($adjArr)){				
					foreach ( $adjArr as $k => $v ){
						if($v['fecha'] < $fecha_operar){											
							echo $adjArr[$k]['htmlRe'];						
							unset($adjArr[$k]);												
						}						
					}
				}
				
				
				### SE VERIFICA SI EL USUARIO TIENE ACCESO AL RADICADO Y DE ACUERDO A ELLO SE ARMAN LOS ENLACES.
				switch ($permiso){
					case 0:
						$radicado_fech = "<a href='$ruta_raiz/verradicado.php?verrad=$radicado_d&PHPSESSID=".session_id().
									 "&krd=$krd&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0&menu_ver_tmp=3' target=".
									 $radicado_fech."> <span class=leidos> $radicado_fech </span> </a>";
						if( strlen ( trim ($radicado_path) ) > 0 ) {
							$ref_radicado = "<a href='bodega/$radicado_path' ><span class=leidos>$radicado_d </span></a>";
						}
						else {
							$ref_radicado = "$radicado_d";
						}
						break;
					case 1:
						$radicado_fech = "<a href='#' onclick=\"alert('No puede acceder por el nivel de privacidad'); 
											return false; \"> <span class=leidos> $radicado_fech </span> </a>";
						$ref_radicado="<a href='#' onclick=\"alert('No puede acceder por el nivel de privacidad'); 
											return false; \"> <span class=leidos> $radicado_d </span> </a>";
						break;
					case 2:
						$radicado_fech = "<a href='#' onclick=\"alert('No puede acceder por su nivel de seguridad'); 
											return false;\"><span class=leidos>$radicado_fech</span></a>";
						//if( strlen (trim ($radicado_path) ) > 0) {
						$ref_radicado="<a href='#' onclick=\"alert('No puede acceder por su nivel de seguridad'); 
											return false;\"><span class=leidos>$radicado_d</span></a>";
						//}
						break;
				}					
				
?>

						<tr class='tpar'>
							<td valign="baseline" class='listado1'>

<?php
				if (!isset ($_POST['verBorrados'])) {
					if (($_POST['anexosRadicado'] != $radicado_d)) {
?>

								<img name="imgVerAnexos_<?php print $radicado_d; ?>" src="imagenes/menu.gif" border="0">

<?php
					}
					else
						if (($_POST['anexosRadicado'] == $radicado_d)) {
?>

								<img name="imgVerAnexos_<?php print $radicado_d; ?>" src="imagenes/menuraya.gif" border="0">

<?php
						}
				}
				if (isset ($_POST['verBorrados'])) {
					if (($_POST['verBorrados'] == $radicado_d)) {
?>

								<img name="imgVerAnexos_<?php print $radicado_d; ?>" src="imagenes/menuraya.gif" border="0">

<?php
					}
					else
						if (($_POST['verBorrados'] != $radicado_d)) {
?>

								<img name="imgVerAnexos_<?php print $radicado_d; ?>" src="imagenes/menu.gif" border="0">
<?php
						}
				}
?>
								
							</td>
							<td valign="baseline" class='listado1'>
								<span class="leidos"><?=$ref_radicado ?></span>
							</td>
							<td valign="baseline" class='listado1'>&nbsp;</td>
							<td valign="baseline" class='listado1'><p style="text-align:center;"><?=$radicado_fech ?></p></td>
							<td valign="baseline" class='listado1'><span class="leidos2"><?=$tipo_documento_desc ?></span></td>
							<td valign="baseline" class='listado1'><span class="leidos2"><?=$rad_asun ?></span></td>
							<td valign="baseline" class='listado1'><span class="leidos2"><?=$subexpediente ?></span></td>
						</tr>
						
<?php
				/* Carga los anexos del radicado indicado en la variable $radicado_d
				 * incluye la clase anexo.php
				 */

				include_once "$ruta_raiz/class_control/anexo.php";
				include_once "$ruta_raiz/class_control/TipoDocumento.php";
				$a = new Anexo($db->conn);
				$tp_doc = new TipoDocumento($db->conn);

				/* Modificacion: 15-Julio-2006 Mostrar los anexos del radicado seleccionado.
				 * Modificado: 23-Agosto-2006 Supersolidaria
				 * Muestra todos los anexos de un radicado al ingresar a la pestana de EXPEDIENTES.
				 */
				$num_anexos = $a->anexosRadicado($radicado_d);
				$anexos_radicado = $a->anexos;
				/* Modificado: 23-Agosto-2006 Supersolidaria
				 * Muestra los anexos borrados de un radicado al ingresar a la pestana de EXPEDIENTES.
				 */

				if (isset ($_POST['verBorrados'])) {
					$num_anexos = $a->anexosRadicado($radicado_d, true);
				}

				if ($num_anexos >= 1) {
					for ($i = 0; $i <= $num_anexos; $i++) {
						$anex = $a;
						$codigo_anexo = $a->codi_anexos[$i];
						if ($codigo_anexo and substr($anexDirTipo, 0, 1) != '7') {
							$tipo_documento_desc = "";
							$fechaDocumento = "";
							$anex_desc = "";
							//$anex = new Anexo;
							$anex->anexoRadicado($radicado_d, $codigo_anexo);
							//$anex=$a;
							$secuenciaDocto = $anex->get_doc_secuencia_formato($dependencia);
							$fechaDocumento = $anex->get_sgd_fech_doc();
							$anex_nomb_archivo = $anex->get_anex_nomb_archivo();
							$anex_desc = $anex->get_anex_desc();
							
							if (strlen($radicado_d) == 14) {
							    $dependencia_creadora = intval(substr($codigo_anexo, 4, 3));
							} else if (strlen($radicado_d) == 15) {
							    $dependencia_creadora = intval(substr($codigo_anexo, 4, 4));
							}
							
							//$dependencia_creadora = substr($codigo_anexo, 4, 3);
							$ano_creado = substr($codigo_anexo, 0, 4);
							$sgd_tpr_codigo = $anex->get_sgd_tpr_codigo();
							// Trae la descripcion del tipo de Documento del anexo
							if ($sgd_tpr_codigo) {
								//$tp_doc = new TipoDocumento($db->conn);
								$tp_doc->TipoDocumento_codigo($sgd_tpr_codigo);
								$tipo_documento_desc = $tp_doc->get_sgd_tpr_descrip();
							}
							$anexBorrado = $anex->anex_borrado;
							$anexSalida = $anex->get_radi_anex_salida();
							$ext = substr($anex_nomb_archivo, -3);
							if (trim($anex_nomb_archivo) and $anexSalida != 1) {
								//if($ext!="doc") {
?>						
							
						<tr class='timpar'>
							<td class='listado5'></td>
							<td valign="baseline"  class='listado5'>
								
<?php
								if ($anexBorrado == "S" ) {
?>
								
								<img src="iconos/docs_tree_del.gif">
								
<?php
								}
								else
									if ($anexBorrado == "N") {
?>								
								
									<img src="iconos/docs_tree.gif">
									
<?php
									}
								if($permiso == 0){
?>									
									
									<a href='bodega/<?=$ano_creado."/$dependencia_creadora/docs/$anex_nomb_archivo"?>'> <?=substr($codigo_anexo,-4)?> </a>
									
<?php 
								}
								else {
?>									
									
									<?=substr($codigo_anexo,-4) ?>
									
<?php
								}
?>
									
							</td>
							<td valign="baseline" class='listado5'>&nbsp;</td>
							<td valign="baseline" class='listado5'><?=$fechaDocumento ?></td>
							<td valign="baseline" class='listado5'><?=$tipo_documento_desc ?></td>
							<td valign="baseline" class='listado5'><?=$anex_desc ?></td>
							<td valign="baseline" class='listado5'></td>
							<td valign="baseline" class='listado5'></td
						</tr>
						
<?php
							
							} // Fin del if que busca si hay link de archivo para mostrar o no el doc anexo
						}
					} // Fin del For que recorre la matriz de los anexos de cada radicado perteneciente al expediente
				}
				$rs->MoveNext();
			}
			if(!empty($adjArr)){
				foreach ( $adjArr as $k => $v ){
					echo $v['htmlRe'];
				}
			}
			// ********************* FIN DEL WHILE MOSTRAR ANEXOS, ADJUNTOS***************
		}
		// Fin del While que Recorre los documentos de un expediente.
?>						
						
					</table>
				</td>
			</tr>
		</table
		
<?php
	}
}
?>		
		
		<table border="0" width="100%"  align="center" cellspacing="0" >
			<tr>
				<td class="titulosError" colspan="6" align="center"><!--Nota.  En el momento de Grabar el expediente este aparecera en la 
					pantalla de archivo para su re-ubicacion fisica. (Si no esta seguro de esto por favor no lo realice)-->
				</td>
			</tr>
		</table>
		<p>
	</body>
</html>