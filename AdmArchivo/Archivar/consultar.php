<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

if ($_SESSION['usua_admin_archivo'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}
if (!(isset($login))){
	$login = $_REQUEST['login'];
}
	
include_once ("../../include/db/ConnectionHandler.php");
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;
### SE INCLUYE ARCHIVO EN DONDE SE ARMAN LOS REPORTES EN EXCEL
require ("../../include/rs2xml.php");
$obj = new rs2xml();
	
if ($_POST['Buscar']) {
	$busRad = $_POST['radicado'];
	$busExp = $_POST['expediente'];
	$busDep = $_POST['depend'];
	$busSer = $_POST['serie_busq'];
	$busSub = $subSerie_busq;
	$busAno = $_POST['Ano_busq'];
	$estado = $_POST['estado'];
}
elseif ($_GET){
	$busExp = $_GET['exp'];
	$busDep = $_GET['dep'];
	$busSer = $_GET['ser'];
	$busSub = $_GET['sub'];
	$busAno = $_GET['ano'];
	$estado = $_GET['estado'];
}
	
switch ($estado){
	case 0:
		$est0 = "Selected";
		break;
	case 1:
		$est1 = "Selected";
		break;
	case 2:
		$est2 = "Selected";
		break;
	case 3:
		$est3 = "Selected";
		break;
}
		
include "consultarTx.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Administrador de estructura organizativa de archivo f&iacute;sico </title>
		<meta name="GENERATOR" content="YesSoftware CodeCharge v.2.0.5 build 11/30/2001">
        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="cache-control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="Site.css" type="text/css">
		<link rel="stylesheet" href="../../estilos/orfeo.css" type="text/css">
		
		<script type="text/javascript" src="../../js/jquery-3.5.1.js"></script>
    	<script type="text/javascript" src="../../s/jquery-1.9.1.min.js"></script>
    	<script type="text/javascript" src="../../js/jquery.blockUI.js"></script> 
    	<link rel="stylesheet" type="text/css" href="../../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.css">
    	<link rel="stylesheet" type="text/css" href="../../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.min.css">
    	<link rel="stylesheet" type="text/css" href="../../lib/DataTables/DataTables-1.10.21/css/fixedColumns.dataTables.min.css">
    	<script type="text/javascript" charset="utf8" src="../../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.js"></script>
    	<script type="text/javascript" charset="utf8" src="../../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
    	<script type="text/javascript" charset="utf8" src="../../lib/DataTables/DataTables-1.10.21/js/dataTables.fixedColumns.min.js"></script>
		<style type="text/css" class="init">
	
	       /*.tabla {
              margin: 0 auto;
              width: 100%;
              clear: both;
              border-collapse: collapse;
              table-layout: fixed;
              word-wrap:break-word;
              text-align: center;
            }*/
            
        	div.dataTables_wrapper {
                width: 1000px;
                margin: 0 auto;
            }
            
        	/*th, td { white-space: nowrap; }*/
        	th.dt-center, td.dt-center { text-align: center; }
        	div.dataTables_wrapper {
        		margin: 0 auto;
        	}
        
        	div.container {
        		width: 80%;
        	}
    
    	</style>
    	</head>
	<body>
		<form method="post" action="consultar.php" name="consultar">
			<table width="70%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
				
				<input type='hidden' name='login' value='<?php echo $login; ?>'>
				<tr>
					<td class="titulos4" colspan="4" align="center"> Consulta de documentos para Archivar </td>
				</tr>
				
				<tr>
					<!-- CAMPO PARA EL RADICADO -->
					<td width="15%" class="titulos2"> Radicado: </td>
					<td width="30%"> <input class="tex_area" type="text" name="radicado" maxlength="15" value="<?php echo $busRad; ?>" size="20"> </td>
					<!-- FIN CAMPO PARA EL RADICADO -->
					
					<!-- CAMPO PARA EL EXPEDIENTE -->
					<td width="15%" class="titulos2"> Expediente: </td>
					<td width="30%"> <input class="tex_area" type="text" name="expediente" maxlength="20" value="<?php echo $busExp; ?>" size="20"> </td>
					<!-- FIN CAMPO PARA EL EXPEDIENTE -->
				</tr>
					
				<tr>
					<td class="titulos4" colspan="4" align="center"> Consulta de documentos por Dependencia </td>
				</tr>
				
				<tr>
					<!-- CAMPO PARA LA DEPENDENCIA -->
					<td width="15%" class="titulos2"> Dependencia: </td>
					<td width="30%">
						<select name='depend' id='depend' class='select' onChange='consultar.submit();'>
						<?php 
							$datos= " selected ";
							echo "<option value='9999' $datos>-- Todas las Dependencias --</option>\n";

							$isqlus = "	SELECT	DEPE_CODI,
												DEPE_NOMB
										FROM	DEPENDENCIA
										ORDER BY DEPENDENCIA_ESTADO DESC, DEPE_CODI";
							$rs1 = $db->conn->Execute($isqlus);
							do{
								$codigo = $rs1->fields["DEPE_CODI"];
								$depnombre = $codigo." - ".substr($rs1->fields["DEPE_NOMB"], 0, 35);
								$datos="";
								if($depend==$codigo)
									$datos= " selected ";
								echo "<option value='$codigo' $datos>$depnombre</option>\n";
								$rs1->MoveNext();
							}
							while(!$rs1->EOF);
						  ?>
						</select>
					</td>
					<!-- FIN CAMPO PARA LA DEPENDENCIA -->

					<!-- CAMPO PARA EL AÑO -->
					<td width="15%" class="titulos2">A&ntilde;o</td>
					<td width="30%">
						<select name=Ano_busq  class="select"  onChange="consultar.submit();">

							<?php
							$fechAno_busq = $_POST['Ano_busq'];
							
							### GENERA EL RANGO DE AÑOS PARA SELECCIONAR
							for($i = Date("Y"); $i > 2007; $i-- ) {
								$datossFec = ($fechAno_busq == $i)? $datossFec = " selected ":"";
								echo "<option value='$i' $datossFec>$i</option>";
							}
							
							if($fechAno_busq == 9999) {
								$datossFec = " selected ";
							}
							echo "<option value='9999' $datossFec>-- Todos los Años --</option>\n";
							?>

						</select>
					</td>
					<!-- FIN CAMPO PARA EL AÑO -->
				</tr>
				
				<tr>
					<!-- CAMPO PARA LA SERIE DOCUMENTAL -->
					<td width="15%" class="titulos2"> 
						Serie <br/>

						<?php
						if(!$depend)
							$depend = 99999;
						$datoss = "";
						if($srdOn) {
							$datoss = " checked ";
						}
						?>

						&nbsp; Solo Inactivas
						<input name="srdOn" type="checkbox" class="select" <?=$datoss?> onChange="consultar.submit();">
					</td>
					<td width="30%">
						<select name=serie_busq  class="select"  onChange="consultar.submit();">
							<?php
							$whereSrdOn = (!isset($_POST['srdOn']) )? "M.SGD_MRD_ESTA = 1 " : " M.SGD_MRD_ESTA = 0";

							###	CONSULTA DE LA SERIE CON LA DEPENDENCIA SELECCIONADA
							$fechaHoy	= date("d-m-Y");
							$sqlFechaHoy	= $db->conn->DBDate(date("d-m-Y"));
							$depeConsulta	= '';
							$datoss			= '';

							if ($depend != 99999)  {
								$depeConsulta =	'M.DEPE_CODI = '.$depend.' AND ';
							}

							if ($serie_busq == 22222)  {
								$datoss	= " selected ";
							}

							$whereSrdOff = (!isset($_POST['srdOn']) )? "" : " AND 
									M.SGD_SRD_CODIGO NOT IN	(SELECT	DISTINCT M.SGD_SRD_CODIGO 
														FROM	SGD_MRD_MATRIRD M 
														WHERE	$depeConsulta M.SGD_MRD_ESTA = 1)";
								echo "<option value='22222' $datoss>-- Todas las Series --</option>\n";
								$getSerie ="SELECT	DISTINCT (CONVERT(CHAR(4),S.SGD_SRD_CODIGO,0)+' - '+S.SGD_SRD_DESCRIP) AS DETALLE,
													S.SGD_SRD_CODIGO AS CODIGO
											FROM	SGD_MRD_MATRIRD M
													JOIN SGD_SRD_SERIESRD S ON
													S.SGD_SRD_CODIGO = m.SGD_SRD_CODIGO
											WHERE	$depeConsulta
													$whereSrdOn AND
													'".$fechaHoy."' BETWEEN S.SGD_SRD_FECHINI AND S.SGD_SRD_FECHFIN
													$whereSrdOff
											ORDER BY detalle";
													echo $getSerie;
								$rsSerie = $db->conn->Execute($getSerie);
								while(!$rsSerie->EOF)  {
									$codigoSer 	= $rsSerie->fields["CODIGO"];
									$detalle 	= substr($rsSerie->fields["DETALLE"],0,42);
									$datoss 	= ($serie_busq == $codigoSer)? $datoss= " selected ":"";
									echo "<option value='$codigoSer' $datoss>$detalle</option>";
									$rsSerie->MoveNext();
								};
						?>
						</select>
					</td>
					<!-- FIN CAMPO PARA LA SERIE DOCUMENTAL -->


					<!-- CAMPO PARA LA SUB-SERIE -->
					<td width="15%" class="titulos2">
						SubSerie <br/>
						<?php
							$datossb = "";
							if($sbrdOn) { 
								$datossb = " checked "; 
							}
						?>

						&nbsp; Solo Inactivas
						<input name="sbrdOn" type="checkbox" class="select" <?=$datossb?> onChange="consultar.submit();">
					</td>

					<td width="30%">
						<select name=subSerie_busq  class="select"  onChange="consultar.submit();">

							<?php
							### CONSULTA DE LA SUB-SERIE CON LA SERIE Y DEPENDENCIA SELECCIONADA
							$sqlFechaHoy	= $db->conn->DBDate(Date("d-m-d"));
							$depeConsulta	= '';
							$datoss			= '';
							$datossb		= '';
							if($depend != 99999) {
								$depeConsulta = 'M.DEPE_CODI ='.$depend. 'AND';
							}

							if($subSerie_busq == 33333)  {
								$datossb = " selected ";
							}
							
							### ENTRA SI NO SE MARCA LA OPCION DE SERIES INACTIVAS
							if (!isset($_POST['srdOn'])) {

								### ENTRA SI NO SE MARCA SUB-SERIES INACIVAS
								if (!isset($_POST['sbrdOn']) ) {
									$whereSbrdOn = "AND m.SGD_MRD_ESTA=1";
									$whereSbrdOff = "";
								}
								else {
									$whereSbrdOn = "AND m.SGD_MRD_ESTA=0";
									$whereSbrdOff = "AND m.SGD_SBRD_CODIGO NOT IN 
										(SELECT DISTINCT m.SGD_SBRD_CODIGO 
										FROM	SGD_MRD_MATRIRD m 
										WHERE	$depeConsulta
												m.SGD_MRD_ESTA = 1)";
								}
							}

							### ENTRA SI SE MARCA LA OPCION DE SERIES INACTIVAS
							else {
								$whereSbrdOn = 	"AND M.SGD_MRD_ESTA=0";
								$whereSbrdOff = "AND M.SGD_SBRD_CODIGO NOT IN 
										(SELECT	DISTINCT M.SGD_SBRD_CODIGO 
										FROM 	SGD_MRD_MATRIRD M 
										WHERE	$depeConsulta
												M.SGD_MRD_ESTA = 1)";
							}
							echo "<option value='33333' $datoss>-- Todas las SubSeries --</option>\n";

							$querySub =	"SELECT	DISTINCT 
												(CONVERT(CHAR(4),SU.SGD_SBRD_CODIGO,0)+' - '+SU.SGD_SBRD_DESCRIP) AS DETALLE,
												SU.SGD_SBRD_CODIGO AS CODIGO
										 FROM	SGD_MRD_MATRIRD M 
												JOIN SGD_SBRD_SUBSERIERD SU ON
													M.SGD_SBRD_CODIGO = SU.SGD_SBRD_CODIGO
										 WHERE	$depeConsulta
												M.SGD_SRD_CODIGO = '$serie_busq' AND
												SU.SGD_SRD_CODIGO = '$serie_busq'
												$whereSbrdOn AND
												'".$fechaHoy."' BETWEEN SU.SGD_SBRD_FECHINI AND SU.SGD_SBRD_FECHFIN
													$whereSbrdOff
										 ORDER BY DETALLE";
							$rsSub=$db->conn->Execute($querySub);

							while(!$rsSub->EOF)  {
								$detalleSub	= substr($rsSub->fields["DETALLE"],0,40);
								$codigoSub 	= $rsSub->fields["CODIGO"];
								$datossSub 	= ($subSerie_busq == $codigoSub)? $datossSub = " selected ":"";
								echo "<option value='$codigoSub' $datossSub>$detalleSub</option>";
								$rsSub->MoveNext();
							}
							?>

						</select>
					</td>
					<!-- FIN CAMPO PARA LA SUB-SERIE -->
				</tr>
				
				
				<tr>
					<td class="titulos4" colspan="4" align="center"> Consulta por estado de los Radicados </td>
				</tr>
				
				
				<tr>
					<td width="15%" class="titulos2">
						Estado:
					</td>
					<td width="30%">
						<select name=estado  class="select">
							<option value="0" <?php echo $est0; ?> > Todos los estados </option>
							<option value="1" <?php echo $est1; ?> > Radicados Archivados </option>
							<option value="2" <?php echo $est2; ?> > Radicados sin Archivar </option>
							<option value="3" <?php echo $est3; ?> > Radicados Archivados - Excluidos </option>
						</select>
					</td>
					
					<td width="15%"></td>
					<td width="30%"> </td>
				</tr>
				
		
				<!-- CAMPO PARA LOS BOTONES -->
				<tr>
					<td class="titulos2" colspan="4" align="center">
						<input class='botones' type='button' name='Atras' value='Atras' onclick='atras();'>
							&nbsp; &nbsp; &nbsp;
						<input class="botones" type="button" value="Limpiar" onclick="limpiar();">
						&nbsp; &nbsp; &nbsp;
						<input class="botones" type="submit" name="Buscar" value="Buscar">
					</td>
				</tr>
			</table>		
		</form>
		
				
		<?php
		if($rsExcelDetalle){
			$path = "../bodega/tmp/archivar".date("dmYh").time("his").".csv";
			$Rs2Xml = $obj->getXML($rsExcelDetalle);
			$arch = $krd."_".rand(10000, 20000);
			$rutaGral = "../../bodega/tmp/$arch.xls";
			$fpDev = fopen($rutaGral, "w");
			if ($fpDev) {
				fwrite($fpDev, $Rs2Xml);
				fclose($fpDev);
			}
		}
			
		#######################################################################
		### SE CREA LA GRILLA DE RESULTADOS DE BUSQUEDA POR DEPENDENCIA
		if ($rsDep && !$flag){
		?>	
		
			<br/>	
			<table style="width:65%" align="center" border="0" cellpadding="0" cellspacing="3">
				
				<?php	
				### SI VIENEN RESULTADOS SE MUESTRAN EN LA TABLA
				if (!$rsDep->EOF) {
				?>
					<tr><td align="left"><?php echo "&nbsp; <a href='$rutaGral' target='_blank'> Reporte General </a>"; ?></td></tr>
					<tr>
						<td class="titulos4" align="center"> DEPENDENCIA </td>
						<td class="titulos4" align="center"> AÑO </td>
						<td class="titulos4" align="center"> SERIE </td>
						<td class="titulos4" align="center"> SUB-SERIE </td>
						<td class="titulos4" align="center"> CANTIDAD EXP. </td>
					</tr>
					
					<?php
					while (!$rsDep->EOF) {
					?>
					
						<tr>
							<td class="listado2_center" title="<?php echo $rsDep->fields['DEPE_NOMB'];?>">
								<?php echo $rsDep->fields['DEPE_CODI'];?>
							</td>
							<td class="listado2_center" title="<?php echo $rsDep->fields['SGD_SEXP_ANO'];?>">
								<?php echo $rsDep->fields['SGD_SEXP_ANO'];?>
							</td>
							<td class="listado2_center" title="<?php echo $rsDep->fields['SGD_SRD_DESCRIP'];?>">
								<?php echo $rsDep->fields['SGD_SRD_CODIGO'];?>
							</td>
							<td class="listado2_center" title=" <?php echo $rsDep->fields['SGD_SBRD_DESCRIP'];?>">
								<?php echo $rsDep->fields['SGD_SBRD_CODIGO'];?>
							</td>
							<td class="listado2_center">
								<a href="./consultar.php?dep=<?=$rsDep->fields['DEPE_CODI']?>&ser=<?=$rsDep->fields['SGD_SRD_CODIGO']?>&sub=<?=$rsDep->fields['SGD_SBRD_CODIGO']?>&estado=<?=$estado?>&ano=<?=$rsDep->fields['SGD_SEXP_ANO']?>&login=<?=$login?>">
									<?php echo $rsDep->fields['CANT'];?> 
								</a>
							</td>
						</tr>

					<?php
						$rsDep->MoveNext();
					}
				}
				else{
					echo "<tr> <td align='center'> <font size='4' color='#FF0000' align='center'> 
						No se encontraron resultados para la consulta realizada 
						</font> </td> </tr>";
				}
				?>
			</table>
		<?php	
		}
		#######################################################################
		
		
		
		#######################################################################
		### SE CREA LA GRILLA DE RESULTADOS DE BUSQUEDA POR DEPENDENCIA 2
		if ($rsDep and $flag == 1){
		?>
			<br/>
			<table border="0" width="60%" class="borde_tab" align="center" class="titulos2">
				<?php	
				### SI VIENEN RESULTADOS SE MUESTRAN EN LA TABLA
				if (!$rsDep->EOF) {
				?>
								
					<tr bordercolor="#FFFFFF">
						<td colspan="4" class="titulos4" align="center"> INFORMACI&Oacute;N DE LA CONSULTA </td>
					</tr>
					<tr>
						<td class='titulos2'> <font size='1'> Dependencia: </font> </td>
						<td class="titulos5"> <font size="1" color='#FF0000'><?=$rsDep->fields['DEPE_NOMB']?></font> </td>
						<td class='titulos2'> Serie: </td>
						<td class='titulos5'> <font size='1'  color='#FF0000'><?=$rsDep->fields['SGD_SRD_DESCRIP']?></font> </td>
					</tr>
					<tr>
						<td class='titulos2'> Sub-serie: </td>
						<td class='titulos5'> <font size='1' color="#FF0000"><?=$rsDep->fields['SGD_SBRD_DESCRIP']?></font> </td>
						<td class='titulos2'> A&ntilde;o: </td>
						<td class='titulos5'> <font size='1' color="#FF0000"> <?=$busAno?></font> </td>
					</tr> 
				</table> <br/>
				
				<table border="0" width="65%" class="borde_tab" align="center" class="titulos2">
					<tr><td align="left"><?php echo "&nbsp; <a href='$rutaGral' target='_blank'> Reporte General</a>"; ?></td></tr>
					<tr>
						<td class='titulos4' align='center' width='20%'> No. EXPEDIENTE </td>
						<td class='titulos4' align='center' width='60%'> NOMBRE EXPEDIENTE </td>
						<td class='titulos4' align='center' width='20%'> CANTIDAD RADICADOS </td>
					</tr>

					<?php
					while (!$rsDep->EOF) {
					?>

						<tr>
							<td class="listado2_center" width="20%"> <?php echo $rsDep->fields['SGD_EXP_NUMERO'];?> </td>
							<td class="listado2" width="60%"> <?php echo $rsDep->fields['SGD_SEXP_PAREXP1'];?> </td>
							<td class="listado2_center">
								<a href="./consultar.php?exp=<?=$rsDep->fields['SGD_EXP_NUMERO']?>&estado=<?=$estado?>&ano=<?=$rsDep->fields['SGD_SEXP_ANO']?>&login=<?=$login?>" >
									<?php echo $rsDep->fields['CANT'];?>
								</a>
							</td>
						</tr>
					
					<?php
						$rsDep->MoveNext();
					}
				}
				else{
					echo "<tr> <td align='center'> <font size='4' color='#FF0000' align='center'> 
						No se encontraron resultados para la consulta realizada
						</font> </td> </tr>";
				}
				?>
				</table>
		<?php	
		}

		#######################################################################
		$rsExcel = $db->conn->Execute($sqlArch);
		######################################################################
		### SE CREA LA GRILLA DE RESULTADOS DE BUSQUEDA POR EXPEDIENTE O RADICADO
		if ($rsExp){
		?>	
			<br/>
			<table width="70%" align="center" border="0" cellpadding="0" cellspacing="3" class="borde_tab">
				
				<?php	
				### SI VIENEN RESULTADOS SE MUESTRAN EN LA TABLA
				if (!$rsExp->EOF) {
				?>
					
					<tr bordercolor="#FFFFFF">
						<td colspan="4" class="titulos4" align="center"> INFORMACI&Oacute;N DE LA CONSULTA </td>
					</tr>
					<tr>
						<td class='titulos2'> <font size='1'> Dependencia: </font> </td>
						<td class="titulos5"> <font size="1" color='#FF0000'><?=$rsExp->fields['DEPENDENCIA']?></font> </td>
						<td class='titulos2'> Serie: </td>
						<td class='titulos5'> <font size='1'  color='#FF0000'><?=$rsExp->fields['SERIE']?></font> </td>
					</tr>
					<tr>
						<td class='titulos2'> Sub-serie: </td>
						<td class='titulos5'> <font size='1' color="#FF0000"><?=$rsExp->fields['SUBSERIE']?></font> </td>
						<td class='titulos2'> A&ntilde;o: </td>
						<td class='titulos5'> <font size='1' color="#FF0000"> <?=$rsExp->fields['ANO']?></font> </td>
					</tr>
				</table> <br/>
				
				
				<?php
					$path = "../bodega/tmp/archivar".date("dmYh").time("his").".csv";

					$Rs2Xml = $obj->getXML($rsExcel);
					$arch = $krd."_".rand(10000, 20000);
					$ruta = "../../bodega/tmp/$arch.xls";
					$fpDev = fopen($ruta, "w");
					if ($fpDev) {
						fwrite($fpDev, $Rs2Xml);
						fclose($fpDev);
					}
				?>
				
				
				<form method="post" action="consultar.php" name="archivar">
					
					<table border="0" width="70%" align="center">
						<tr>
							<td align="center">
								<input class="botones" type="submit" name="Archivar" value="Archivar">
							</td>
							<td align="center">
								<input class="botones" type="submit" name="Excluir" value="Excluir">
							</td>
						</tr>
					</table>

					<?php echo "&nbsp; <a href='$ruta' target='_blank'> REPORTE (csv) </a>"; ?>
					
					<table id="grid" class="tabla hover stripe order-column cell-border compact">
						<thead>
    						<tr>
    							<td class="titulos3"> EXPEDIENTE </td>
    							<td class="titulos3"> RADICADO </td>
    							<td class="titulos3"> FECHA </td>
    							<td class="titulos3"> TIPO DOCUMENTAL </td>
    							<td class="titulos3"> ESTADO FISICO</td>
    							<td class="titulos3"> No. FOLIOS</td>
    							<td class="titulos3"> CARPETA ACTUAL</td>
    							<td class="titulos3"> SELECCIONAR </td>
    						</tr>
						</thead>
						<tbody>
						<?php						
						while (!$rsExp->EOF) {
							if ($rsExp->fields['ESTADO'] == 'SIN ARCHIVAR') {
								$color = "#FF0000";
							}
							elseif ($rsExp->fields['ESTADO'] == 'PARA EXCLUIR') {
								$color = "#FF8000";
							}
							elseif ($rsExp->fields['ESTADO'] == 'ARCHIVADO') { 
								$color = "#00AA00";
							}
						?>
							<tr>
								<td class="listado2"> <?php echo $rsExp->fields['EXPEDIENTE'];?> </td>
								<td class="listado2"> <?php echo $rsExp->fields['RADICADO'];?> </td>
								<td class="listado2"> <?php echo $rsExp->fields['FECHA_RAD'];?> </td>
								<td class="listado2"> <?php echo $rsExp->fields['TIPO_DOCUMENTAL'];?> </td>
								<td class="listado2"> <font size='1' color="<?php echo $color; ?>"> <?php echo $rsExp->fields['ESTADO'];?> </font> </td>
								<td class="listado2"> <input class="tex_area" type="text" name="folios[<?php echo $rsExp->fields['RADICADO'];?>-<?php echo $rsExp->fields['EXPEDIENTE']; ?>]" maxlength="5" value="<?php echo $rsExp->fields['FOLIOS']; ?>" size="5"> </td>
								<td class="listado2"> <input class="tex_area" type="text" name="carpeta[<?php echo $rsExp->fields['RADICADO'];?>-<?php echo $rsExp->fields['EXPEDIENTE']; ?>]" maxlength="3" value="<?php echo $rsExp->fields['CARPETA']; ?>" size="3"> </td>
								<td class="listado2"> <input class="checkbox" type="checkbox" name="marcado[<?php echo $rsExp->fields['RADICADO'];?>-<?php echo $rsExp->fields['EXPEDIENTE']; ?>]" > </td>	
							</tr>

						<?php
							$rsExp->MoveNext();
						}
						?>
						</tbody>
					</table>
					<?php 
					}
					else{
						echo "<tr> <td align='center'> <font size='4' color='#FF0000' align='center'> 
								No se encontraron resultados para la consulta realizada 
							</font> </td> </tr>";
					}
					?>
					
				</form>
		<?php		
		#######################################################################
		}
		
		if ($msg){
		?>	
			<table width="70%" align="center" border="0" cellpadding="0" cellspacing="3" class="borde_tab"> 
				<tr>
					<td class='titulos5' align="center">
						<font size="2" color='#FF0000'> <?php echo $msg; ?> </font>
					</td>
				</tr>
			</table>
		<?php
		}
		
		?>
		
		
		
				
	
	</body>
	<script language="javascript">
		function limpiar() {
			document.consultar.elements['radicado'].value = "";
			document.consultar.elements['expediente'].value = "";
			document.consultar.elements['depend'].value = 9999;
			document.consultar.elements['ano_busq'].value = '2013';
			document.consultar.elements['serie_busq'].value = 22222;
			document.consultar.elements['subSerie_busq'].value = 33333;
		}
		
		function atras() 
		{
			window.location.href = "../index.php?login=<?=$login?>";
		}
		
		function genDatatable(table) 
    	{
    		table = $('#grid').removeAttr('width').DataTable( {
              paging:   true,
              ordering: true,
              info:     true,
              scrollY:  "600px",
              scrollX: true,
              scrollCollapse: true,
              pagingType: "full_numbers",
              fixedColumns: false,
              columnDefs: [
                  { width: 50, targets: 0 }
              ],
              language: {
                  "lengthMenu": "Mostrando _MENU_ registros por p&aacute;gina",
                  "zeroRecords": "No hay registros",
                  "info": "Mostrando p&aacute;gina _PAGE_ de _PAGES_",
                  "infoEmpty": "No hay registros disponibles",
                  "infoFiltered": "(Filtrado de _MAX_ registros totales)",
                  "search":         "Filtrar:",
                  "paginate": {
                      "first":      "Primero",
                      "last":       "&Uacute;ltimo",
                      "next":       "Siguiente",
                      "previous":   "Anterior"
                  }
              }
          } );
    	}
		
		$(document).ready(function() {
			debugger;
      		var table = "";
      		
      		$.blockUI({
			      message: 'Espere Un Momento ...',
			      css: {
			        border: 'none',
			        padding: '15px',
			        backgroundColor: '#000',
			        '-webkit-border-radius': '10px',
			        '-moz-border-radius': '10px',
			        opacity: '.5',
			        color: '#fff',
			        fontSize: '18px',
			        fontFamily: 'Verdana,Arial',
			        fontWeight: 200 } });
			        
			if ( $.fn.dataTable.isDataTable( '#grid' ) ) {
				table = $('#grid').DataTable();
				table.destroy();
				genDatatable(table);
			} else {
				genDatatable(table);
			}
			
			$.unblockUI();
        } );
	</script>
</html>