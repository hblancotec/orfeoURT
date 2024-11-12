<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

$ruta_raiz = "..";
!$ruta_raizImg ? $ruta_raizImg = "..":0;
//Ibis: Variables
$arrayRadi=array();
$arrayRadiAnex=array();
include_once ($ruta_raiz.'/config.php');
define('ADODB_ASSOC_CASE', 1);
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db= new ConnectionHandler("$ruta_raiz");

function sort_by_orden ($a, $b) {
    return $a['fecha'] - $b['fecha'];
}

if($db) {
    ?>
    <script>
    // despliega una pantalla para ver los metadatos de un anexo a un radicado
    function verMetadatosAnexRadi(radicado, anexo){
        url="/MetadatosAnexo.php?numRadicado="+radicado+"&idAnexo="+anexo;
        window.open(url,'Metadatos anexos al radicado','height=480,width=640');
    }
    // despliega una pantalla para ver los metadatos de un anexo a un expediete
    function verMetadatosAnexExp(anexo,IdExpediente){
        url="/MetadatosAnexoExpediente.php?anexo="+anexo+"&expediente="+IdExpediente;
        window.open(url,'Metadatos anexos al expediente','height=480,width=640');
    }
    //Valida hash de un archivo
    function metodoVhash(anexoidexp, tipo) {
		jQuery.ajax({
			url:"expediente/consulta.php?<?=session_name()?>=<?=trim(session_id())?>",
			type:'POST',
			cache:false,
			dataType:'html',
			data:({
				anexoidexp: anexoidexp,
				tipo : tipo,
				frmActual:"<?php echo $_SERVER['PHP_SELF']; ?>"
				}),
			error:function (objeto,que_paso,objeto){
				alert ('Error de conexión');
			},
			success:function(data){
				alert(data);
			}
		});
    }
    //llama al php encargado de crear el indice electrónico en pdf
    function metodopdf(aRadi,aRadiAnex,expediente){
        url="/IndiceElectronico.php?numRadicado="+aRadi+"&idAnexo="+aRadiAnex+"&expediente="+expediente;
        window.open(url,'Indice Electrónico PDF',1);
        window.location.reload(false);
    }
    //llama al php encargado de validar el indice electrónico
    function metodoValida(expediente){
    	debugger;
    	var rads = document.getElementById('txtrads').value;
    	var radsanex = document.getElementById('txtradsanex').value;
    
        url="/validaIndice.php?numRadicado="+rads+"&idAnexo="+radsanex+"&expediente="+expediente;
        window.open(url,'Validación Indice Electrónico',"width=500,height=120");
    }
    
    //ibis: indice firma
    function metodoFirmaIndice(expediente){
        url="/SeleccFirmanteindice.php?expediente="+expediente;
        window.open(url,'Validación Indice Electrónico',"width=760,height=300");
        //window.location.reload(false);
    }
    
    function datosDelExp(numeroExpediente)
	{
		window.open("<?=$ruta_raiz?>/expediente/ver_datos_exp.php?sessid=<?=session_id()?>&numeroExpediente="+numeroExpediente+"&krd=<?=$krd?>","Modifcar_Expediente","height=300,width=800,left=400,top=100,scrollbars=yes");
	}
    </script>
    <?php 
    
    //IBISCOM  2018-11-01
    $palabraClave = $_POST['palabraClaveDocumento'];
    
	//$db->conn->debug=true;
	if ($_REQUEST['val']){
		$numExpediente = substr($_REQUEST['val'], 0,20);
		
		if(substr($numExpediente,-2)== " -") {
			$numExpediente = substr($numExpediente,0,strlen($numExpediente)-2);
		}
	}
	
	if ($_REQUEST['tdoc']) {
		$tipDoc		= $_REQUEST['tdoc'];
		$whereAdjun = "AND A.SGD_TPR_CODIGO = ".$tipDoc;
	}
	else {
		$whereAdjun = "";
	}
	
	if($_REQUEST['tdoc'] == 99999)
		$whereAdjun = "";
	
	if(!$frmActual) {
		$frmActual = $_SERVER['PHP_SELF'];
	}
	
	if($numExpediente) {
		include_once ("$ruta_raiz/include/tx/Expediente.php");
		include_once("$ruta_raiz/class_control/Dependencia.php");
		include_once("$ruta_raiz/include/tx/Historico.php");
		$expediente = new Expediente($db);
		$expediente->getExpediente($numExpediente);
		$depExp	 = $expediente->depeNomb;
		$codDepExp = $expediente->depCodi;
		$nombExp = $expediente->nombreExp;
		$respExp = $expediente->responsableNom;
		$fechExp = $expediente->fecha;
		$trdExp  = $expediente->serie . " / " . $expediente->subserie;
		$migraEstado =$expediente->migraEstado;
		$migraDescri =$expediente->migraDescri;
		$estExp = $expediente->estado;
		if ($expediente->privado){
			$privExp = 'Privado';
		}
		else {
			$privExp = 'P&uacute;blico';
		}
		#IBISCOM 2018-10-30
		$sqlSERIE= "SELECT	SR.SGD_SRD_CODIGO AS CODIGO_SERIE
				    FROM	SGD_SRD_SERIESRD SR
				    WHERE	SR.SGD_SRD_DESCRIP = '$expediente->serie'";
		$codigoSerie = $db->conn->Execute($sqlSERIE)->fields['CODIGO_SERIE']; // SE OBTIENE EL CODIGO DE LA SERIE
		
		
		$sqlSubSerie= "	SELECT	SB.SGD_SBRD_CODIGO AS CODIGO_SUBSERIE
				        FROM	SGD_SBRD_SUBSERIERD SB
				        WHERE	SB.SGD_SBRD_DESCRIP = '$expediente->subserie'
                        AND     SB.SGD_SRD_CODIGO='$codigoSerie'";
		
		$codigoSubSerie = $db->conn->Execute($sqlSubSerie)->fields['CODIGO_SUBSERIE']; // SE OBTIENE EL CODIGO DE LA SUBSERIE
		
		$trdExp  = $codigoSerie ." - ".$expediente->serie . " / " . $codigoSubSerie ." - ".$expediente->subserie; // SE CONCADENA CON SU RESPECTVO codigo - NOMBRE
		
		$queryFechIni = "SELECT TOP (1) SGD_HFLD_FECH  AS FECHA_INICIAL
                		FROM SGD_HFLD_HISTFLUJODOC
                		WHERE SGD_EXP_NUMERO = '$numExpediente'
                		AND SGD_HFLD_OBSERVA ='Incluir radicado en Expediente'
            		        order by sgd_hfld_fech asc";
		$fecha_extrema_inicial =  $db->conn->Execute($queryFechIni)->fields['FECHA_INICIAL']; //Fecha del primer documento del expediente
		
		
		$queryFechFinal = "SELECT TOP (1) SGD_HFLD_FECH AS FECHA_FINAL
                		FROM SGD_HFLD_HISTFLUJODOC
                		WHERE SGD_EXP_NUMERO = '$numExpediente'
                		AND SGD_HFLD_OBSERVA ='Incluir radicado en Expediente'
            		        order by sgd_hfld_fech desc";
		$fecha_extrema_final =  $db->conn->Execute($queryFechFinal)->fields['FECHA_FINAL'];//Fecha del último documento del expediente
		
		
		#IBISCOM 2018-10-30
		
		### SE VALIDA SI EL USUARIO QUE CONSULTA PUEDE ACCEDER AL EXPEDIENTE. $verExp = 0 SI PUEDE ACCEDER
		$sqlverExp = "SELECT dbo.VALIDAR_ACCESO_RADEXP (0, '$numExpediente', '".$_SESSION['login']."')";
		$verExp = $db->conn->Getone($sqlverExp);


		
	}
}
$year = substr($numExpediente,0,4);
?>

<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
<script language="JavaScript" src="<?=$ruta_raiz?>/js/funciones.js"></script>
<script>

function noPermiso(tip)
{
    if(tip==0)
        alert ("No puede acceder por su nivel de seguridad");
    if(tip==1)
        alert ("No puede acceder por el nivel de privacidad");
}



function verHistExpediente(numeroExpediente,codserie,tsub,tdoc,opcionExp) 
{
        <?php
        $isqlDepR ="SELECT	RADI_DEPE_ACTU,
							RADI_USUA_ACTU
					FROM	RADICADO
					WHERE	RADI_NUME_RADI = '$numrad'";
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
</script>


<?php	
	### ENTRA SI CUMPLE CON LAS VALIDACIONES DE PRIVACIDAD DE EXPEDIENTES
	if($verExp <> 0 ){
?>		
		<table border="0" width="50%" class="borde_tab" align="center" class="titulos2">
			<tr class="titulos2">
				<td class="titulos2" width="30%">
					<span class='leidos2' align="left">
						<h4>
							Documento incluido en el(los) expediente(s) con car&aacute;cter privado y solo 
							ser&aacute;n visibles por usuarios de la misma dependencia a la que pertenece(n)
						</h4>
					</span>
				</td>
				<td align="center" width="15%"> 
					<div style="background:#E3E8EC; padding:6px; width:150px; height:40px; overflow:auto; text-align:left; "> 
						<?php echo $numExpediente; ?>
					</div>
				</td>	
			</tr>
		</table>
		
<?php
	}
	else{
?>

		<form id="frmExp" name="frmExp" method="post" action="<?=$frmActual."?val=$numExpediente&tdoc=$tipDoc&anExp=$year"?>">
			<table border="0" width="100%" align="center" class='borde_tab'>
				<tr>
					<td colspan="8">
						<table border="0" width="100%" class='borde_tab' align="center" cellspacing="1" >
							<tr bordercolor="#FFFFFF">
								<td colspan="4" class="titulos4">
									<div align="center">
										<strong><br>INFORMACI&Oacute;N DEL EXPEDIENTE</strong>
									</div>
								</td>
							</tr>
							<tr>
								<td class="titulos2" width="15%"> <font size="1">N&uacute;mero de Expediente:</font> </td>
								<td class="listado2" width="30%"> <font size="1"  color="#FF0000"><?=$numExpediente?></font>  </td>
								<td class='titulos2' width="15%" nowrap> Nombre del Expediente: </td>
								<td class="listado2" width="40%"> <font size="1"  color="#FF0000"><?=$nombExp?></font> </td>
							</tr>
							<tr>
								<td class="titulos2"  nowrap> Dependencia: </td>
								<td class="listado2" > <?=$depExp; ?> </td>
								<td class='titulos2'  nowrap> Responsable: </td>
								<td class="listado2" > <?=$respExp?> </td>
							</tr>
							<tr>
								<td class="titulos2" > Serie/SubSerie </td><!-- IBISCOM 2018-10-30 CAMBIO de nombre de TRD a Serie/SubSerie  -->
								<td class="listado2"> <?php echo $trdExp;?> </td>
								<td class="titulos2"> Fecha inicio: </td>
								<td class="listado2" > <?php echo $fecha_extrema_inicial ?> </td> <!--  IBISCOM 2018-10-30 Antes  $expediente->fecExp Despues $fecha_extrema_inicial -->
							</tr>
							<tr>
								<td class="titulos2" > Fase de Archivo: </td>
								<td class="listado2" > <?php echo $expediente->faseExpDisplay ?> </td><!-- IBISCOM 2018-10-30 CAMBIO de variable de faseExp a faseExpDisplay  -->
								<td class="titulos2" > Fecha Fin: </td>
								<td class="listado2" > <?php echo $fecha_extrema_final ?> </td><!--  IBISCOM 2018-10-30 Antes  $expediente->fecExpFin Despues $fecha_extrema_inicial -->
							</tr>
							<tr>
								<td class="titulos2" > Estado: </td>
								<td class="listado2" > <? echo $expediente->estadoExp?> </td>
								<td class="titulos2"> Nivel de Seguridad: </td>
								<td class="listado2" > <? echo $privExp; ?> </td>
							</tr>
							<tr>
								<td class="titulos2" > BPIN: </td>
								<td class="listado2" > <?php echo $bpinExp; ?> </td>
								<td class="titulos2" > Historia del Expediente </td>
								<td class="listado2" > <input type="button" value="..." onClick="verHistExpediente('<?=$numExpediente?>');"> </td>
							</tr>

							<tr>
								<td class="titulos2" > Sector: </td>
								<td class="listado2" > <?php echo $sectExp; ?> </td>
								<td class="titulos2" > Adjuntar archivos al Exp. </td>
								<td class="listado2" > 
									<?php
									if ($estExp==0) {
									    echo "<input type='button' value='...' onClick=\"adjuntarArchivos('".$numExpediente."','".$numrad."');\">";
									}
									?>
								</td>
							</tr>

							<tr>
								<td class="titulos2" > Entidad: </td>
								<td class="listado2" > <?php echo $empExp; ?> </td>
								<td class="titulos2" > Datos del Expediente </td>
								<td class="listado2"> 

									<?php 
									if ($_SESSION["usuaPermExpediente"]>1 || $expediente->responsable == $_SESSION['usua_doc']) {
										if ($estExp==0) {
									?>

										<input type="button" value="..." onClick="datosDelExp('<?=$numExpediente?>');"> 

									<?php 
										}
									}
									?>

								</td>
							</tr>

							<tr>
								<td class="titulos2" > Proyecto: </td>
								<td class="listado2" > <?php echo $nombreP; ?> </td> <!--ibis Modifico: colspan="3"> -->
								<!-- Ibiscom Boton y label hash -->
								<td class="titulos2" > Generar Indice Electr&oacute;nico </td>
								<td class="listado2" > <input type="button" value="..." onClick="metodopdf('<?=$Send1?>','<?=$Send2?>','<?=$numExpediente?>');"> </td>
								
								<!--  Ibiscom Boton y Label Validar hash -->
							</tr>
							<tr>
								<td class="titulos2" > Validar Indice Electr&oacute;nico </td>
								<td class="listado2" > <input type="button" value="..." onClick="metodoValida('<?=$numExpediente?>');"> </td>
								<td class="titulos2" > Solicitar Firma de Indice Electr&oacute;nico </td>
								<td class="listado2" colspan="3"> <input type="button" value="..." onClick="metodoFirmaIndice('<?=$numExpediente?>');"> </td>
							</tr>
							<?php 
							if ($migraEstado==1){
                                echo "<tr><td colspan='4'><font color='red'>".$migraDescri."</font></td><tr>";
                            }
                            ?>
						</table>
					</td>
				</tr>
				<tr class='timparr'>
					<td colspan="6" class="titulos5">
						<table  width=100% class='borde_tab' align='center' cellpadding='0' cellspacing='0'>
							<tr class='timparr'>
								<td colspan='8' class='titulos4' align='center'>
									<font><b>Documentos Pertenecientes al expediente &nbsp;</b></font>
								</td>
							</tr>
							<tr class='titulos3'>
								<td align='center' colspan='3' align='center'>Radicado </td>
								<td align='center' align='center' width='13%'>Fecha Radicaci&oacute;n </td>
								<td align='center' align='center'>
									<select name='tdoc' id='tdoc' class='select' style='width:300px' onChange='frmExp.submit();'>

										<?php
										// Consulta de los Tipos Documentales de la Serie y Sub-Serie del Expedeinte
										$sqlTdoc = "SELECT 	DISTINCT T.SGD_TPR_DESCRIP AS DETALLE,
															T.SGD_TPR_CODIGO AS CODIGO
													FROM    SGD_MRD_MATRIRD M
															JOIN SGD_TPR_TPDCUMENTO T ON
																T.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
															JOIN SGD_SEXP_SECEXPEDIENTES S ON
																M.DEPE_CODI = S.DEPE_CODI AND
																M.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO AND
																M.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO
													WHERE   S.SGD_EXP_NUMERO = '$numExpediente'
													ORDER BY DETALLE";
										$rsTdoc=$db->conn->Execute($sqlTdoc);
										$datoss = "";

										if($tipDoc=='99999')
											$datoss= " selected ";
										echo "<option value='99999' $datoss >Todos los Tipos Documentales</option>\n";
										do{
											$codigo	  = $rsTdoc->fields["CODIGO"];
											$descTdoc = $rsTdoc->fields["DETALLE"];
											$datoss="";
											if($tipDoc == $codigo){
												$datoss= " selected ";
											}
											echo "<option value=$codigo $datoss> $descTdoc </option>";
											$rsTdoc->MoveNext();
										} while(!$rsTdoc->EOF);
										?>

									</select>
								</td>
								<td align='center' align='center' width='35%'>Asunto</td>
								<td align='center' align='center' width='35%'>Consultar metadatos</td> <!--2018-10-19 IBISCOM CAMPO PARA CONSULTAR METADOS DE UN DOCUMENTO INICIO-->
								<td align='center' align='center' width='5%'>Validar  Huella</td> 
								<!--2018-10-19 IBISCOM CAMPO PARA CONSULTAR METADOS DE UN DOCUMENTO FIN -->
							</tr>
							<?php
							
							//$tablaRads
							
							### ENTRA SI CUMPLE CON LAS VALIDACIONES DE PRIVACIDAD DE EXPEDIENTES
							if($verExp == 0) {
							    
							    ### SE REALIZA EL REGISTRO EN EL LOG, DE LA CONSULTA DEL EXPEDIENTE
							    $usDoc	 = $_SESSION['usua_doc'];
							    $usLogin = $_SESSION['login'];
							    $dir = substr($_SERVER['HTTP_REFERER'], 0, 140);
							    
							    $sql = "INSERT INTO
					SGD_HIST_CONSULTAS (	USUA_DOC,
											USUA_LOGIN,
											SGD_TTR_CODIGO,
											SGD_EXP_NUMERO,
											HIST_CON_MODULO)
					VALUES	(	'".$usDoc."',
								'".$usLogin."',
								98,
								'".$numExpediente."',
								'".$dir."' )";
							    $rs = $db->conn->Execute($sql);
							    
							    include_once($ruta_raiz.'/include/query/queryver_datosrad.php');
							    
							    //IBISCOM 2018-11-07  APLICA PARA CUANDO SE INGRESA UNA PALABRA CLAVE PARA ANEXOS A EXPEDIENTES
							    if($palabraClave != ''){
							        
							        $consulta ="SELECT	A.ANEXOS_EXP_NOMBRE AS NOMBRE,
    								convert(varchar(10), A.ANEXOS_EXP_FECH_CREA,103) AS TIEMPO,
    								B.SGD_TPR_DESCRIP 	AS TD,
    								A.ANEXOS_EXP_DESC 	AS ASU,
                                    A.ANEXOS_EXP_PATH 	AS RUTA,
                                    A.ANEXOS_EXP_ID     AS ANEXO_ID_EXP
    						FROM	SGD_ANEXOS_EXP A
    								JOIN SGD_TPR_TPDCUMENTO B ON
    									A.SGD_TPR_CODIGO = B.SGD_TPR_CODIGO
    						WHERE	SGD_EXP_NUMERO = '$numExpediente'
    								$whereAdjun
    								AND A.ANEXOS_EXP_ESTADO != 1
                                    AND  A.ANEXOS_EXP_ID IN (
                                                              SELECT id_anexo
                                                                FROM METADATOS_DOCUMENTO
                                                                WHERE palabras_clave LIKE '%$palabraClave%'
                                                                AND id_tipo_anexo = 1
                                                            )
    						ORDER BY A.ANEXOS_EXP_FECH_CREA, A.ANEXOS_EXP_ORDEN";
    								##IBISCOM 2018-10-20  se agrego en la query  A.ANEXOS_EXP_ID     AS ANEXO_ID_EXP
    								
    								//2019-06-19 INICIO POR CUESTION DE ORDENANIENTO DE DOCUMENTOS EN EXPEDIENTES
    								$consultaOrdenByFechaProP ="SELECT	A.ANEXOS_EXP_NOMBRE AS NOMBRE,
    								IIF(M.fecha_produccion IS NULL, convert(varchar(10), A.ANEXOS_EXP_FECH_CREA,103), convert(varchar(10), M.fecha_produccion,103)) AS TIEMPO,
    								B.SGD_TPR_DESCRIP 	AS TD,
    								A.ANEXOS_EXP_DESC 	AS ASU,
                                    A.ANEXOS_EXP_PATH 	AS RUTA,
                                    A.ANEXOS_EXP_ID     AS ANEXO_ID_EXP,
                                    M.hash AS HS
    						FROM	SGD_ANEXOS_EXP A
    								JOIN SGD_TPR_TPDCUMENTO B ON
    									A.SGD_TPR_CODIGO = B.SGD_TPR_CODIGO
                                    LEFT JOIN METADATOS_DOCUMENTO M ON M.id_anexo = convert(varchar(20),a.ANEXOS_EXP_ID)
    						WHERE	SGD_EXP_NUMERO = '$numExpediente'
                                    --AND M.id_tipo_anexo = '1'
    								$whereAdjun
    								AND A.ANEXOS_EXP_ESTADO != 1
                                    AND  A.ANEXOS_EXP_ID IN (
                                                              SELECT id_anexo
                                                                FROM METADATOS_DOCUMENTO
                                                                WHERE palabras_clave LIKE '%$palabraClave%'
                                                                AND id_tipo_anexo = 1
                                                            )
    						ORDER BY M.fecha_produccion, A.ANEXOS_EXP_ORDEN";
    								$adjun = $db->conn->Execute($consultaOrdenByFechaProP);
    								if ($adjun->EOF){ // si es vacio
    								    $adjun = $db->conn->Execute($consulta);
    								}
    								//2019-06-19 Fin
							    }	//IBISCOM 2018-11-07
							    if($palabraClave == ''){			//IBISCOM 2018-11-07
							        
							        ### ***CONSULTA DE DOCUMENTOS ADJUNTOS AL EXPEDIENTE SELECCIONADO***
							        $consulta ="SELECT	A.ANEXOS_EXP_NOMBRE AS NOMBRE,
								convert(varchar(10), A.ANEXOS_EXP_FECH_CREA,103) AS TIEMPO,
								B.SGD_TPR_DESCRIP 	AS TD,
								A.ANEXOS_EXP_DESC 	AS ASU,
                                A.ANEXOS_EXP_PATH 	AS RUTA,
                                A.ANEXOS_EXP_ID     AS ANEXO_ID_EXP
						FROM	SGD_ANEXOS_EXP A
								JOIN SGD_TPR_TPDCUMENTO B ON
									A.SGD_TPR_CODIGO = B.SGD_TPR_CODIGO
						WHERE	SGD_EXP_NUMERO = '$numExpediente'
								$whereAdjun
								AND A.ANEXOS_EXP_ESTADO != 1
						ORDER BY A.ANEXOS_EXP_FECH_CREA, A.ANEXOS_EXP_ORDEN ";
								##IBISCOM 2018-10-20  se agrego en la query  A.ANEXOS_EXP_ID     AS ANEXO_ID_EXP
								
								//2019-06-19 INICIO INICIO POR CUESTION DE ORDENANIENTO DE DOCUMENTOS EN EXPEDIENTES
								$consultaOrdenByFechaPro ="SELECT	A.ANEXOS_EXP_NOMBRE AS NOMBRE,
    								IIF(M.fecha_produccion IS NULL, convert(varchar(10), A.ANEXOS_EXP_FECH_CREA,103), convert(varchar(10), M.fecha_produccion,103)) AS TIEMPO,
    								B.SGD_TPR_DESCRIP 	AS TD,
    								A.ANEXOS_EXP_DESC 	AS ASU,
                                    A.ANEXOS_EXP_PATH 	AS RUTA,
                                    A.ANEXOS_EXP_ID     AS ANEXO_ID_EXP,
                                    M.hash AS HS
    						FROM	SGD_ANEXOS_EXP A
    								JOIN SGD_TPR_TPDCUMENTO B ON
    									A.SGD_TPR_CODIGO = B.SGD_TPR_CODIGO
                                    LEFT JOIN METADATOS_DOCUMENTO M ON M.id_anexo = convert(varchar(20),a.ANEXOS_EXP_ID)
    						WHERE	SGD_EXP_NUMERO = '$numExpediente'
    								$whereAdjun
    								AND A.ANEXOS_EXP_ESTADO != 1
    						ORDER BY M.fecha_produccion, A.ANEXOS_EXP_ORDEN ";
    								##IBISCOM 2018-10-20  se agrego en la query  A.ANEXOS_EXP_ID     AS ANEXO_ID_EXP
    								
    								$adjun = $db->conn->Execute($consultaOrdenByFechaPro);
    								if ($adjun->EOF){  // si es vacio
    								    $adjun = $db->conn->Execute($consulta);
    								}
    								//2019-06-19 Fin
    								
							    }//IBISCOM 2018-11-07
							    //$adjun = $db->conn->Execute($consulta);
							    
							    $adjArr = array();
							    $contador = 0;
							    
							    while(!$adjun->EOF and !empty($adjun)) {
							        //Ibis: se agrego captura de id
							        $anexoIdExp		= $adjun->fields['ANEXO_ID_EXP'];
							        $nombre		= $adjun->fields['NOMBRE'];
							        //Ibis: Se incluye if para colocar como orden 1 en indice electronico
							        $varTiempoIndice = strpos($nombre,"indice_");
							        $tiempo		= $adjun->fields['TIEMPO'];
							        if ($varTiempoIndice !== false){
							            $tiempo2 = $tiempo;
							            $tiempo ="01/12/1900";
							        }
							        //Ibis: Se incluye if para colocar como orden 1 en indice electronico
							        $td			= $adjun->fields['TD'];
							        $descript	= $adjun->fields['ASU'];
							        $ruta	    = $adjun->fields['RUTA'];
							        $ruta		= '<a href=../../bodega/'.$ruta.'>'.$nombre.'</a>';
							        if ($tiempo != "") {
							            list($diaa,$mess,$anno)	= explode('/',$tiempo);
							            $fecha_comp = mktime(0,0,0,$mess,$diaa,$anno);
							        } else {
							            $fecha_comp = "";
							        }
							        
							        //Ibis: Link para llamar el metodo encargado de validar la huella del docmuento
							        $linkIbisHuellaValExp = "<a href=javascript:metodoVhash('$anexoIdExp','1')><img src='".$ruta_raizImg."/iconos/huella.png'></a>";
							        
							        ##IBISCOM 2018-10-20 Inicio
							        
							        $linkMetadatosDocEXP = "<a href=javascript:verMetadatosAnexExp('".$anexoIdExp."','".$numExpediente."') ><img src='".$ruta_raizImg."/iconos/carpeta_azul.gif'>" ."</a>";
							        ##IBISCOM 2018-10-20 Fin
							        if ($varTiempoIndice !== false){
							            $htmlRe = "<tr class='timpar'>
							<td valign='baseline' class='listado5'>&nbsp;</td>
							<td valign='baseline' class='listado5' align='center'>
								<img name='imgAdjuntos' src='../img/silk/icons/attach.png' border='0'>
							</td>
							<td valign='baseline' class='listado5' align='left'>$ruta</td>
							<td valign='baseline' class='listado5' align='center' width='13%'>$tiempo2</td>
							<td valign='baseline' class='listado5' align='left' width='30%'>$td</td>
							<td valign='baseline' class='listado5' align='left' width='35%'>$descript</td>
                            <td valign='baseline' class='listado5' align='left' width='35%'>&nbsp;".$linkMetadatosDocEXP."&nbsp;</td>
				            <td valign='baseline' class='listado5' align='center' width='5%'>&nbsp;".$linkIbisHuellaValExp."&nbsp;</td> </tr>";
							        }else{
							            /*
							             //2019-06-19 INICIO Para que muestre la fecha del anexo al expediente digitada
							             $queryFechaPro = "SELECT fecha_produccion AS fechaPro FROM IBIS.METADATOS_DOCUMENTO WHERE ID_ANEXO = '".$anexoIdExp."' AND id_tipo_anexo = '1'";
							             $fechaPro = "";
							             $fechaPro = $db->conn->Execute($queryFechaPro)->fields['fechaPro'];
							             if(is_null($fechaPro) ||  empty($fechaPro) ){
							             $fechaPro = $tiempo ;
							             }
							             //2019-06-19 FIN se cambia $tiempo  por  $fechaPro
							             */
							            $fechaPro = $tiempo ;
							            
							            $htmlRe = "<tr class='timpar'>
							<td valign='baseline' class='listado5'>&nbsp;</td>
							<td valign='baseline' class='listado5' align='center'>
								<img name='imgAdjuntos' src='../img/silk/icons/attach.png' border='0'>
							</td>
							<td valign='baseline' class='listado5' align='left'>$ruta</td>
							<td valign='baseline' class='listado5' align='center' width='13%'>$fechaPro</td>
							<td valign='baseline' class='listado5' align='left' width='30%'>$td</td>
							<td valign='baseline' class='listado5' align='left' width='35%'>$descript</td>
                            <td valign='baseline' class='listado5' align='left' width='35%'>&nbsp;".$linkMetadatosDocEXP."&nbsp;</td>
				            <td valign='baseline' class='listado5' align='center' width='5%'>&nbsp;".$linkIbisHuellaValExp."&nbsp;</td>
                            </tr>";
							        }
							        //<!--2018-10-20 IBISCOM CAMPO PARA CONSULTAR METADATOS link -->
							        
							        $adjArr[] =	array(	'fecha' => $fecha_comp,
							        'htmlRe'=> $htmlRe);
							        $contador ++;
							        $adjun->MoveNext();
							    }
							    $tablaRads = '';
							    
							    
							    ### ***CONSULTA DE LOS RADICADOS QUE PERTENECEN AL EXPEDIENTE SELECCIONADO***
							    $isql ="SELECT	C.SGD_TPR_DESCRIP AS DESCRIP,
							A.TDOC_CODI AS TIPODOC,
							convert (varchar(10),A.RADI_FECH_RADI,103) AS FECHA_RAD,
							A.RA_ASUN AS ASUNTO,
							A.RADI_PATH,
							$radi_nume_radi as RADI_NUME_RADI,
							A.RADI_USUA_ACTU,
							A.RADI_DEPE_ACTU,
							SEXP.SGD_SEXP_ESTADO,
							dbo.VALIDAR_ACCESO_RADEXP (A.RADI_NUME_RADI, '', '" .$_SESSION["login"]. "') AS PERMISO
					FROM	SGD_EXP_EXPEDIENTE R
							JOIN RADICADO A ON	R.RADI_NUME_RADI = A.RADI_NUME_RADI
							JOIN SGD_TPR_TPDCUMENTO C ON	A.TDOC_CODI = C.SGD_TPR_CODIGO
							JOIN SGD_SEXP_SECEXPEDIENTES SEXP ON	SEXP.SGD_EXP_NUMERO = R.SGD_EXP_NUMERO
					WHERE	R.SGD_EXP_NUMERO = '$numExpediente' AND R.SGD_EXP_ESTADO <> 2
					ORDER BY  A.RADI_FECH_RADI";
							$rs = $db->conn->query($isql);
							
							if($rs && !$rs->EOF ){
							    while(!$rs->EOF) {
							        $contRad = 0;
							        $rad_nume 	= "";
							        $rad_path	= "";
							        $rad_fech	= "";
							        $rad_asun	= "";
							        $rad_tdoc	= "";
							        $rad_tdoc_desc = "";
							        
							        $rad_nume 	= $rs->fields["RADI_NUME_RADI"];
							        $rad_path 	= $rs->fields["RADI_PATH"];
							        $rad_fech 	= $rs->fields["FECHA_RAD"];
							        $rad_asun 	= $rs->fields["ASUNTO"];
							        $rad_tdoc	= $rs->fields["TIPODOC"];
							        $permiso	= $rs->fields["PERMISO"];
							        $rad_tdoc_desc = $rs->fields["DESCRIP"];
							        $usuaCodiActu  = $rs->fields["RADI_USUA_ACTU"];
							        $depeCodiActu  = $rs->fields["RADI_DEPE_ACTU"];
							        list($diaa,$mess,$anno)	= explode('/',$rad_fech);
							        $fecha_operar =	mktime(0,0,0,$mess,$diaa,$anno);
							        
							        if($rad_tdoc == $tipDoc || $tipDoc == 99999 || !$tipDoc) {
							            $contRad = 1;
							        }
							        
							        /*if(!empty($adjArr)){
							            foreach ( $adjArr as $k => $v ){
							                if($v['fecha'] < $fecha_operar){
							                    echo $adjArr[$k]['htmlRe'];
							                    unset($adjArr[$k]);
							                }
							            }
							        }*/
							        
							        ### SE VERIFICA SI EL USUARIO TIENE ACCESO AL RADICADO Y DE ACUERDO A ELLO SE ARMAN LOS ENLACES.
							        switch ($permiso){
							            case 0:
							                $linkInfGeneral = "<a href='$ruta_raizImg/verradicado.php?verrad=$rad_nume&PHPSESSID=".session_id()."&krd=$krd&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0&menu_ver_tmp=3' target='accedeRadicado'><span class=leidos>$rad_fech</span></a>";
							                if(strlen($rad_path)) {
							                    $linkDocto = "<a href='$ruta_raizImg/bodega/$rad_path' ><span class=leidos>$rad_nume</span></a>";
							                }
							                //Ibis: Se corregi bug, cuando el radicadpo no tiene doc principal
							                else{
							                    $linkDocto = $rad_nume;
							                }
							                //Ibis: Fin Se corregi bug, cuando el radicadpo no tiene doc principal
							                break;
							            case 1:
							                $linkDocto = "<a class='vinculos' href='javascript:noPermiso(1)'> $rad_nume </a>";
							                $linkInfGeneral = "<a class='vinculos' href='javascript:noPermiso(1)' > $rad_fech </a>";
							                break;
							            case 2:
							                $linkDocto = "<a class='vinculos' href='javascript:noPermiso(0)' > $rad_nume </a>";
							                $linkInfGeneral = "<a class='vinculos' href='javascript:noPermiso(0)' > $rad_fech </a>";
							                break;
							        }
							        
							        
							        include_once "$ruta_raiz/class_control/anexo.php";
							        include_once "$ruta_raiz/class_control/TipoDocumento.php";
							        $a = new Anexo($db->conn);
							        $tp_doc = new TipoDocumento($db);
							        $contAnex = 0;
							        if($palabraClave == ''){//IBISCOM 2018-11-07
							            $num_anexos = $a->anexosRadicado($rad_nume, true);
							        }else{//IBISCOM 2018-11-07
							            $num_anexos = $a->anexosRadicadoFiltrado($rad_nume, true, $palabraClave);
							        }
							        $anexos_radicado = $a->anexos;
							        $datosAnex = array();
							        for($i=0;$i<=$num_anexos;++$i) {
							            $anex = $a;
							            $codigo_anexo = $a->codi_anexos[$i];
							            
							            if($codigo_anexo and substr($anexDirTipo,0,1)!='7') {
							                $anex_tdoc_desc = "";
							                $anex_tp_desc = ""; //IBISCOM 2019-05-09
							                $anex_fech = "";
							                $anex_desc = "";
							                $anex->anexoRadicado($rad_nume,$codigo_anexo);
							                $secuenciaDocto = $anex->get_doc_secuencia_formato($dependencia);
							                $anex_fech = $anex->get_sgd_fech_doc();
							                $anex_nomb_archivo= $anex->get_anex_nomb_archivo();
							                $anex_desc = $anex->get_anex_desc();
							                $dep_creadora = substr($codigo_anexo,4,3);
							                $ano_creado = substr($codigo_anexo,0,4);
							                $anex_tpr = $anex->get_sgd_tpr_codigo();
							                $anexBorrado = $anex->anex_borrado;
							                $anexSalida = $anex->get_radi_anex_salida();
							                $ext = substr($anex_nomb_archivo,-3);
							                
							                //Trae la descripcion del tipo de Documento del anexo
							                if($anex_tpr) {
							                    $tp_doc->TipoDocumento_codigo($anex_tpr);
							                    $anex_tp_desc = $tp_doc->get_sgd_tpr_descrip();
							                }
							                
							                if( $anexBorrado == "S" ){
							                    $imgTree="docs_tree_del.gif";
							                    $idBorrados="id='borrados' style='display:none'";
							                }
							                else if( $anexBorrado == "N" ) {
							                    $imgTree="docs_tree.gif";
							                    $idBorrados="id='anex'";
							                }
							                if(trim($anex_nomb_archivo) and $anexSalida != 1 and $anexBorrado != 'S' ) {
							                    if ($anex_tpr==$tipDoc || $tipDoc=='99999' || !$tipDoc){
							                        $datosAnex[$contAnex]['icono']  = "<img src='".$ruta_raizImg."/iconos/".$imgTree."'>";
							                        $datosAnex[$contAnex]['enlace'] = "<a href='bodega/".$ano_creado."/".$dep_creadora."/docs/".$anex_nomb_archivo."'>".substr($codigo_anexo,-4)."</a>";
							                        $datosAnex[$contAnex]['fecdoc'] = $anex_fech;
							                        $datosAnex[$contAnex]['tipdoc'] = $anex_tp_desc;
							                        $datosAnex[$contAnex]['asunto'] = $anex_desc;
							                        //IBISCOM 2018-10-20 inicio
							                        $datosAnex[$contAnex]['codigo'] = $codigo_anexo;
							                        $datosAnex[$contAnex]['codigo_anexo'] = "<a  href=javascript:verMetadatosAnexRadi('$rad_nume','$codigo_anexo') >"."<img src='".$ruta_raizImg."/iconos/vista_preliminar.gif'>" ."</a>"; //IBISCOM 2018-10-20
							                        //IBISCOM 2018-10-20 fin
							                        $contAnex++;
							                    }
							                } // Fin del if que busca si hay link de archivo para mostrar o no el doc anexo
							            }
							        }  // Fin del For que recorre la matriz de los anexos de cada radicado perteneciente al expediente
							        
							        //Si viene almenos un anexo o el radicado, pinta los datos del radicado
							        if ($contAnex > 0 || $contRad == 1){
							            $htmlRe = "";
							            
							            //ibis hash Radicado
							            array_push($arrayRadi, $rad_nume);
							            if (strlen($rad_path)>0){
							                /*$querySelectRad = "SELECT ANEX_CODIGO  FROM ANEXOS
							                 WHERE ANEX_RADI_NUME = '$rad_nume'
							                 AND RADI_NUME_SALIDA IS NOT NULL";
							                 
							                 $anexTofind = $db->conn->query($querySelectRad)->fields["ANEX_CODIGO"];
							                 $rutahash = BODEGAPATH.$rad_path;*/
							                
							                $linkIbisHuellaValRad = "<a href=javascript:metodoVhash('$rad_nume','2')><img src='".$ruta_raizImg."/iconos/huella.png'></a>";
							                
							                
							                if($palabraClave == ''){//IBISCOM 2018-11-07
							                    #IBISCOM 2018-10-20 INICIO
							                    $queryIdAnexoPadre = "SELECT ANEX_CODIGO  FROM ANEXOS
                        						WHERE ANEX_RADI_NUME = '$rad_nume'
                        						AND RADI_NUME_SALIDA IS NOT NULL";
							                    $idAnexoPadre = $db->conn->query($queryIdAnexoPadre)->fields["ANEX_CODIGO"];
							                    
							                    $idAnexoRadicadoPadre = $idAnexoPadre; // cuando es el anexo "PADRE" deja como identificador del documento en la tabla ANEXOS el Numero de la radicacion
							                    
							                    $linkMetadatosDoc = "<a  href=javascript:verMetadatosAnexRadi('$rad_nume','$idAnexoRadicadoPadre') >"."<img src='".$ruta_raizImg."/iconos/vista_preliminar.gif'>" ."</a>";
							                    #IBISCOM 2018-10-20 FIN
							                    
							                    $htmlRe .= "<tr class='tpar'><td valign='baseline' class='listado1'>&nbsp;</td>
									<td valign='baseline' class='listado1'>";
							                    $htmlRe .= "<img name='imgVerAnexos_".$rad_nume."' src='".$ruta_raizImg."/imagenes/menu.gif' border='0'></td>";
							                    $htmlRe .= "<td valign='baseline' class='listado1' align='left'><span class='leidos'>".$linkDocto."</span></td>
							<td valign='baseline' class='listado1' align='center'><span class='leidos2' width='13%'>&nbsp;".$linkInfGeneral."&nbsp;</span></td>
							<td valign='baseline' class='listado1' align='left' width='30%'><span class='leidos2'>&nbsp;".$rad_tdoc_desc."&nbsp;</span></td>
							<td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$rad_asun."&nbsp;</span></td>
                            <td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$linkMetadatosDoc."&nbsp;</span></td>
                            <td valign='baseline' class='listado1' align='left' width='5%'>&nbsp;".$linkIbisHuellaValRad."&nbsp;</td>
                            </tr>";
							                    //<!--2018-10-19 IBISCOM CAMPO PARA CONSULTAR METADATOS link -->
							                }else{
							                    #IBISCOM 2018-10-20 INICIO
							                    $queryIdAnexoPadre = "SELECT ANEX_CODIGO
                                                FROM ANEXOS
                        						WHERE ANEX_RADI_NUME = '$rad_nume'
                        						AND RADI_NUME_SALIDA IS NOT NULL
                                                AND ANEX_CODIGO  IN  (
                                                                    SELECT id_anexo FROM METADATOS_DOCUMENTO  WHERE palabras_clave LIKE '%$palabraClave%' AND id_tipo_anexo = 0
                                                                     )";
							                    $idAnexoPadre = $db->conn->query($queryIdAnexoPadre)->fields["ANEX_CODIGO"];
							                    if($idAnexoPadre != ''){
							                        
							                        $idAnexoRadicadoPadre = $idAnexoPadre; // cuando es el anexo "PADRE" deja como identificador del documento en la tabla ANEXOS el Numero de la radicacion
							                        
							                        $linkMetadatosDoc = "<a  href=javascript:verMetadatosAnexRadi('$rad_nume','$idAnexoRadicadoPadre') >"."<img src='".$ruta_raizImg."/iconos/vista_preliminar.gif'>" ."</a>";
							                        #IBISCOM 2018-10-20 FIN
							                        $htmlRe .= "<tr class='tpar'><td valign='baseline' class='listado1'>&nbsp;</td>
    									<td valign='baseline' class='listado1'>";
							                        $htmlRe .= "<img name='imgVerAnexos_".$rad_nume."' src='".$ruta_raizImg."/imagenes/menu.gif' border='0'></td>";
							                        $htmlRe .= "<td valign='baseline' class='listado1' align='left'><span class='leidos'>".$linkDocto."</span></td>
    							<td valign='baseline' class='listado1' align='center'><span class='leidos2' width='13%'>&nbsp;".$linkInfGeneral."&nbsp;</span></td>
    							<td valign='baseline' class='listado1' align='left' width='30%'><span class='leidos2'>&nbsp;".$rad_tdoc_desc."&nbsp;</span></td>
    							<td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$rad_asun."&nbsp;</span></td>
						        <!-- Ibis: Campo boton para llamr metodo validar huella -->
    						    <td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$linkMetadatosDoc."&nbsp;</span></td>
                                <td valign='baseline' class='listado1' align='left' width='5%'>&nbsp;".$linkIbisHuellaValRad."&nbsp;</td>
                                </tr>";
							                        //<!--2018-10-19 IBISCOM CAMPO PARA CONSULTAR METADATOS link -->
							                    }
							                }
							                
							            }else{
							                if($palabraClave == ''){//IBISCOM 2018-11-07
							                    #IBISCOM 2018-10-20 INICIO
							                    $queryIdAnexoPadre = "SELECT ANEX_CODIGO  FROM ANEXOS
                        						WHERE ANEX_RADI_NUME = '$rad_nume'
                        						AND RADI_NUME_SALIDA IS NOT NULL";
							                    $idAnexoPadre = $db->conn->query($queryIdAnexoPadre)->fields["ANEX_CODIGO"];
							                    
							                    $idAnexoRadicadoPadre = $idAnexoPadre; // cuando es el anexo "PADRE" deja como identificador del documento en la tabla ANEXOS el Numero de la radicacion
							                    
							                    $linkMetadatosDoc = "<a  href=javascript:verMetadatosAnexRadi('$rad_nume','$idAnexoRadicadoPadre') >"."<img src='".$ruta_raizImg."/iconos/vista_preliminar.gif'>" ."</a>";
							                    #IBISCOM 2018-10-20 FIN
							                    
							                    $htmlRe .= "<tr class='tpar'><td valign='baseline' class='listado1'>&nbsp;</td>
									<td valign='baseline' class='listado1'>";
							                    $htmlRe .= "<img name='imgVerAnexos_".$rad_nume."' src='".$ruta_raizImg."/imagenes/menu.gif' border='0'></td>";
							                    $htmlRe .= "<td valign='baseline' class='listado1' align='left'><span class='leidos'>".$linkDocto."</span></td>
							<td valign='baseline' class='listado1' align='center'><span class='leidos2' width='13%'>&nbsp;".$linkInfGeneral."&nbsp;</span></td>
							<td valign='baseline' class='listado1' align='left' width='30%'><span class='leidos2'>&nbsp;".$rad_tdoc_desc."&nbsp;</span></td>
							<td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$rad_asun."&nbsp;</span></td>
						    <td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$linkMetadatosDoc."&nbsp;</span></td> </tr>";
							                    //<!--2018-10-19 IBISCOM CAMPO PARA CONSULTAR METADATOS link -->
							                } else {
							                    #IBISCOM 2018-10-20 INICIO
							                    $queryIdAnexoPadre = "SELECT ANEX_CODIGO
                                                FROM ANEXOS
                        						WHERE ANEX_RADI_NUME = '$rad_nume'
                        						AND RADI_NUME_SALIDA IS NOT NULL
                                                AND ANEX_RADI_NUME  IN  (
                                                                    SELECT id_anexo FROM METADATOS_DOCUMENTO  WHERE palabras_clave LIKE '%$palabraClave%' AND id_tipo_anexo = 0
                                                                     )";
							                    $idAnexoPadre = $db->conn->query($queryIdAnexoPadre)->fields["ANEX_CODIGO"];
							                    if($idAnexoPadre != ''){
							                        
							                        $idAnexoRadicadoPadre = $idAnexoPadre; // cuando es el anexo "PADRE" deja como identificador del documento en la tabla ANEXOS el Numero de la radicacion
							                        
							                        $linkMetadatosDoc = "<a  href=javascript:verMetadatosAnexRadi('$rad_nume','$idAnexoRadicadoPadre') >"."<img src='".$ruta_raizImg."/iconos/vista_preliminar.gif'>" ."</a>";
							                        #IBISCOM 2018-10-20 FIN
							                        $htmlRe .= "<tr class='tpar'><td valign='baseline' class='listado1'>&nbsp;</td>
    									<td valign='baseline' class='listado1'>";
							                        $htmlRe .= "<img name='imgVerAnexos_".$rad_nume."' src='".$ruta_raizImg."/imagenes/menu.gif' border='0'></td>";
							                        $htmlRe .= "<td valign='baseline' class='listado1' align='left'><span class='leidos'>".$linkDocto."</span></td>
    							<td valign='baseline' class='listado1' align='center'><span class='leidos2' width='13%'>&nbsp;".$linkInfGeneral."&nbsp;</span></td>
    							<td valign='baseline' class='listado1' align='left' width='30%'><span class='leidos2'>&nbsp;".$rad_tdoc_desc."&nbsp;</span></td>
    							<td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$rad_asun."&nbsp;</span></td>
    						    <td valign='baseline' class='listado1' align='left' width='35%'><span class='leidos2'>&nbsp;".$linkMetadatosDoc."&nbsp;</span></td> </tr>";
							                        //<!--2018-10-19 IBISCOM CAMPO PARA CONSULTAR METADATOS link -->
							                    }
							                }
							            }
							            
							            
							            
							            //Si viene algun anexo lo pinta
							            $z = count($datosAnex);
							            for ($j=0;$j<$z;++$j){
							                //Ibis: hash anexos exp
							                array_push($arrayRadiAnex, $datosAnex[$j]['codigo']);
							                /*$querySelectId3 = "SELECT HASH AS HS FROM METADATOS_DOCUMENTO WHERE ID_ANEXO = '".$datosAnex[$j]['codigo']."'";
							                 $hashSave2 = $db->conn->Execute($querySelectId3)->fields['HS'];
							                 $rutahash2 = str_replace("<a href='bodega/", "", $datosAnex[$j]['enlace']);
							                 $intUse = strpos($rutahash2,"'>");
							                 $rutahash2 = substr($rutahash2, 0,$intUse);
							                 $rutahash2 = BODEGAPATH.$rutahash2;*/
							                //BODEGAPATH.$ano_creado."/".$dep_creadora."/docs/".$anex_nomb_archivo;
							                
							                $linkIbisHuellaValRadAnex = "<a href=javascript:metodoVhash('".$datosAnex[$j]['codigo']."','3')><img src='".$ruta_raizImg."/iconos/huella.png'></a>";
							                
							                $htmlRe .= "<tr  class='timpar' $idBorrados>
								<td valign='baseline' class='listado5'>&nbsp;</td>
								<td valign='baseline' class='listado5'>".$datosAnex[$j]['icono']."&nbsp;</td>
								<td valign='baseline' class='listado5'>".$datosAnex[$j]['enlace']."</td>
								<td valign='baseline' class='listado5' align='center' width='13%'>".$datosAnex[$j]['fecdoc']."</td>
								<td valign='baseline' class='listado5' align='left' width='30%'>".$datosAnex[$j]['tipdoc']."</td>
								<td valign='baseline' class='listado5' align='center' width='35%'>".$datosAnex[$j]['asunto']."</td>
							    <td valign='baseline' class='listado5' align='left' width='35%'>&nbsp;".$datosAnex[$j]['codigo_anexo']."&nbsp;</td>
                                <td valign='baseline' class='listado5' align='center' width='5%'>&nbsp;".$linkIbisHuellaValRadAnex."&nbsp;</td>
						        </tr>";
							                //<!--2018-10-19 IBISCOM CAMPO PARA CONSULTAR METADATOS link -->
							                
							            }
							        }
							        
							        $adjArr[] =	array(	'fecha' => $fecha_operar,
							            'htmlRe'=> $htmlRe);
							        
							        /*if(!empty($adjArr)){
							            foreach ( $adjArr as $k => $v ){
							                if($v['fecha'] < $fecha_operar){
							                    echo $adjArr[$k]['htmlRe'];
							                    unset($adjArr[$k]);
							                }
							            }
							        }*/
							        
							        $rs->MoveNext();
							    }
							}
							
							/*if(!empty($adjArr)){
							    //uasort($adjArr, 'sort_by_orden');
							    //sort($adjArr, SORT_STRING);
							    /*usort($adjArr, function ($a, $b) {
							     return strcmp($a["fecha"], $b["fecha"]);
							     });
							    sort($adjArr);
							    
							    foreach ( $adjArr as $k => $v ){
							        $tablaRads .= $adjArr[$k]['htmlRe'];
							        unset($adjArr[$k]);
							    }
							}*/
							
							elseif (!$adjArr) {
							    $tablaRads.="<table  width=100% class='borde_tab' align='center' cellpadding='0' cellspacing='0'><tr class='timparr'>
					<td colspan='6' class='titulos2'><center><font><b>Actualmente no tiene Radicados Archivados</b></font></center></td>
					</tr></table>";
							}
							
							$tablaRads.="</table>";
							}
							
							sort($adjArr);
							if(!empty($adjArr)){
							    foreach ( $adjArr as $k => $v ){
							        echo $v['htmlRe'];
							    }
							}
							
							//$tablaRads
							
							?>
					</td>
				</tr>
			</table>
			<?php
			     $Send1=implode(",", $arrayRadi);
			     $Send2=implode(",", $arrayRadiAnex);
			?>
			<input type="hidden" id="txtrads" name="txtrads" value="<?=$Send1 ?>" />
			<input type="hidden" id="txtradsanex" name="txtradsanex" value="<?=$Send2 ?>" />
		</form>
<?php
	}
?>	