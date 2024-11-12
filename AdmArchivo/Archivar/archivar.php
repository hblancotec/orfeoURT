<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
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
	
	if ($_GET) {
		$busRad = $_GET['rad'];
		$exp = $_GET['exp'];
	}
	else if ($_POST){
		$busRad	= $_POST['radicado'];
		$exp	= $_POST['expediente'];
		$arch	= $_POST['archivarOk'];
	}
	
	include "consultarTx.php";
	$dep = $rsRad->fields['DEPE_NOMB'];
	$ser = $rsRad->fields['SGD_SRD_DESCRIP'];
	$sub = $rsRad->fields['SGD_SBRD_DESCRIP'];
	$ano = $rsRad->fields['SGD_SEXP_ANO'];
	$anex  = $rsAnex;
	$folio  = $rsRad->fields['SGD_EXP_FOLIOS'];
	
	
	### SE VERIFICA SI DESDE EL FORMULARIO SE ACTUALIZAN DATOS DE LO CONTRARIO
	### SE CARGAN LOS DATOS QUE VIENEN DE LA CONSULTA A LA BASE DE DATOS
	if (isset($_POST['edificio']))
		$selEdi = $_POST['edificio'];
	else
		$selEdi = $rsRad->fields['SGD_EXP_EDIFICIO'];
		
	if (isset($_POST['piso']))
		$selPis = $_POST['piso'];
	else
		$selPis = $rsRad->fields['SGD_EXP_ARCHIVO'];
	
	if (isset($_POST['estante']))
		$selEst = $_POST['estante'];
	else
		$selEst = $rsRad->fields['SGD_EXP_ESTANTE'];
		
	if (isset($_POST['entrepano']))
		$selEnt = $_POST['entrepano'];
	else
		$selEnt = $rsRad->fields['SGD_EXP_ENTREPA'];
	
	if (isset($_POST['cajaIni']))
		$cajIni = $_POST['cajaIni'];
	else
		$cajIni = $rsRad->fields['SGD_EXP_CAJA'];
	
	if (isset($_POST['cajaCan']))
		$cajCan = $_POST['cajaCan'];
	else	
		$cajCan = $rsRad->fields['SGD_EXP_CANTCAJ'];
		
	if (isset($_POST['carpIni']))
		$carIni = $_POST['carpIni'];
	else	
		$carIni = $rsRad->fields['SGD_EXP_CARPETA'];
	
	if (isset($_POST['carpCan']))
		$carCan = $_POST['carpCan'];
	else
		$carCan = $rsRad->fields['SGD_EXP_CANTCAR'];
	
	if (isset($_POST['folios']))
		$folio = $_POST['folios'];
	elseif (!$folio)
		$folio = $rsRad->fields['RADI_NUME_HOJA'];
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Archivar documento f&iacute;sico </title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<link rel="stylesheet" href="../../estilos/orfeo.css" type="text/css">
	</head>
	<body>
		<form method="post" action="archivar.php" name="archivar">
			<input type='hidden' name='login' value='<?php echo $login; ?>'>
			<input name="radicado" value="<?php echo $busRad; ?>"  type="hidden">
			<input name="expediente" value="<?php echo $exp; ?>"  type="hidden">
			<br/>
			<table width="70%" align="center" border="0" cellpadding="0" cellspacing="3" class="borde_tab">
				<tr bordercolor="#FFFFFF">
					<td colspan="4" class="titulos4" align="center"> INFORMACI&Oacute;N DE LA CONSULTA </td>
				</tr>
				<tr>
					<td class='titulos2'> <font size='1'> Dependencia: </font> </td>
					<td class="titulos5"> <font size="1" color='#FF0000'><?=$dep?></font> </td>
					<td class='titulos2'> Serie: </td>
					<td class='titulos5'> <font size='1'  color='#FF0000'><?=$ser?></font> </td>
				</tr>
				<tr>
					<td class='titulos2'> Sub-serie: </td>
					<td class='titulos5'> <font size='1' color="#FF0000"><?=$sub?></font> </td>
					<td class='titulos2'> A&ntilde;o: </td>
					<td class='titulos5'> <font size='1' color="#FF0000"> <?=$ano?></font> </td>
				</tr>
				<tr>
					<td class='titulos2'> Expediente: </td>
					<td class='titulos5'> <font size='1' color="#FF0000"><?=$exp?></font> </td>
					<td class='titulos2'> Anexos al expediente: </td>
					<td class='titulos5'> <font size='1' color="#FF0000"> <?=$anex?> </font> </td>
				</tr>
				<tr>
					<td class='titulos2'> Radicado: </td>
					<td class='titulos5'> <font size='1' color="#FF0000"> <?=$busRad?></font> </td>
					<td class='titulos2'> No. Folios: </td>
					<td class='titulos5'> <input class="tex_area" type="text" name="folios" maxlength="5" value="<?php echo $folio; ?>" size="5"> </td>
					
				</tr>
			</table>
			<br/>
			
			<?php
			if ($msg){
			?>	
				<table width="70%" align="center" border="0" cellpadding="0" cellspacing="3" class="borde_tab"> 
					<tr>
						<td class='titulos5' align="center">
							<font size="1" color='#FF0000'> <?php echo $msg; ?> </font>
						</td>
					</tr>
				</table>
			<?php
			}
			?>
			
			<br/>
			<table width="90%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
				<tr>
					<td class="titulos4" colspan="8" align="center">
						Ubicación física del radicado
					</td>
				</tr>
			
				<tr>
					<td width="10%" class="titulos2" align="center"> Edificio </td>
					<td width="10%" class="titulos2" align="center"> Piso </td>
					<td width="10%" class="titulos2" align="center"> Estante </td>
					<td width="10%" class="titulos2" align="center"> Entrepa&ntilde;o </td>
					<td width="10%" class="titulos2" align="center"> Caja Inicial </td>
					<td width="10%" class="titulos2" align="center"> Cantidad Cajas </td>
					<td width="10%" class="titulos2" align="center"> Cantidad Carpetas </td>
					<td width="10%" class="titulos2" align="center"> Carpeta Actual </td>
				</tr>
				<tr> <td></td> </tr>
				<tr>
					<td width="10%"  align="center">
						<select name='edificio' id='edificio' class='select' onChange='archivar.submit();'>
							<?php
								$datos= "selected";
								echo "<option value='0' $datos>Seleccione Uno...</option>\n";
								$sqlEd = "	SELECT	SGD_EDIFICIO_COD, 
													SGD_EDIFICIO_NOMB,
													SGD_EDIFICIO_SIGLA
											FROM	SGD_EDIFICIO_ARCHIVO
											WHERE	SGD_EDIFICIO_ESTADO = 1
											ORDER BY SGD_EDIFICIO_NOMB";
								$rsEd = $db->conn->Execute($sqlEd);

								do{
									$codEd = $rsEd->fields["SGD_EDIFICIO_COD"];
									$edificio = $codEd;
									$edificio = $edificio." - ".$rsEd->fields["SGD_EDIFICIO_NOMB"];
									$datos="";
									if($selEdi==$codEd)
										$datos= "selected";
									echo "<option value='$codEd' $datos>".$rsEd->fields["SGD_EDIFICIO_NOMB"]."</option>\n";
									$rsEd->MoveNext();
								}
								while(!$rsEd->EOF);
							?>
						</select>
					</td>
					
					<td width="10%" align="center">
						<select name='piso' id='piso' class='select' onChange='archivar.submit();'>
							<?php
								$datos= "selected";
								if (!$selEdi)	$selEdi = 0;
								if (!$selPis)	$selPis = 0;
								echo "<option value='0' $datos> Seleccione Uno... </option>\n";
								$sqlPi = "	SELECT	SGD_PISO_COD,
													SGD_PISO_DESC
											FROM	SGD_PISO_ARCHIVO
											WHERE	SGD_EDIFICIO_COD = ".$selEdi."
											ORDER BY SGD_PISO_DESC";
								$rsPi = $db->conn->Execute($sqlPi);
							
								do{
									$codPi = $rsPi->fields["SGD_PISO_COD"];
									$piso = $codPi;
									$piso = $piso." - ".$rsPi->fields["SGD_PISO_DESC"];
									$datos="";
									if($selPis==$codPi)
										$datos= "selected";
									echo "<option value='$codPi' $datos>".$rsPi->fields["SGD_PISO_DESC"]."</option>\n";
									$rsPi->MoveNext();
								}
								while(!$rsPi->EOF);
							?>
						</select>
					</td>
					
					<td width="1%" align="center">
						<select name='estante' id='estante' class='select' onChange='archivar.submit();'>
							<?php
								if (!$selPis)	$selPis = 0;
								if (!$selEst)	$selEst = 0;
								echo "<option value='0'> Seleccione Uno... </option>";
								$sqlEs = "	SELECT	SGD_EST_COD,
													SGD_EST_DESC
											FROM	SGD_ESTANTE_ARCHIVO
											WHERE	SGD_PISO_COD = ".$selPis."
											ORDER BY SGD_EST_DESC";
								$rsEs = $db->conn->Execute($sqlEs);
								
								do{
									$codEs = $rsEs->fields["SGD_EST_COD"];
									$estante = $codEs;
									$estante = $estante." - ".$rsEs->fields["SGD_EST_DESC"];
									$datos="";
									if($selEst==$codEs)
										$datos= "selected";
									echo "<option value='$codEs' $datos>".$rsEs->fields["SGD_EST_DESC"]."</option>\n";
									$rsEs->MoveNext();
								}
								while(!$rsEs->EOF);
							?>
						</select>
					</td>
					
					<td width="10%" align="center">
						<select name='entrepano' id='entrepano' class='select' onChange='archivar.submit();'>
							<?php
								if (!$selEnt)	$selEnt = 0;
								echo "<option value='0'> Seleccione Uno... </option>";
								$sqlEn = "	SELECT	SGD_ENT_DESC,
													SGD_ENT_COD
											FROM	SGD_ENTREPANO_ARCHIVO
											ORDER BY SGD_ENT_DESC";
								$rsEn = $db->conn->Execute($sqlEn);
								
								do{
									$codEn = $rsEn->fields["SGD_ENT_COD"];
									$entrep = $codEn;
									$entrep = $entrep." - ".$rsEn->fields["SGD_ENT_DESC"];
									$datos="";
									if($selEnt==$codEn)
										$datos= "selected";
									echo "<option value='$codEn' $datos>".$rsEn->fields["SGD_ENT_DESC"]."</option>\n";
									$rsEn->MoveNext();
								}
								while(!$rsEn->EOF);
							?>
						</select>
					</td>
					
					
					<td width="10%" align="center">
						<input class="tex_area" type="text" name="cajaIni" maxlength="3" value="<?php echo $cajIni; ?>" size="3">
					</td>
					
					<td width="10%" align="center">
						<input class="tex_area" type="text" name="cajaCan" maxlength="3" value="<?php echo $cajCan; ?>" size="3">
					</td>
										
					<td width="10%" align="center">
						<input class="tex_area" type="text" name="carpCan" maxlength="3" value="<?php echo $carCan; ?>" size="3">
					</td>
					
					<td width="10%" align="center">
						<input class="tex_area" type="text" name="carpIni" maxlength="3" value="<?php echo $carIni; ?>" size="3">
					</td>
				</tr>

				<!-- CAMPO PARA LOS BOTONES -->
				<tr>
					<td class="titulos2" colspan="8" align="center">
						<input class='botones' type='button' name='Atras' value='Atras' onclick='atras();'>
							&nbsp; &nbsp; &nbsp;
						<input class='botones' type='button' value='Limpiar' onclick='limpiar();'>
							&nbsp; &nbsp; &nbsp;
						<input class='botones' type='button' name='valida' value='Archivar' onclick='verifica();'>
						<input type='hidden' name='archivarOk' value='0'>
					</td>
				</tr>
			</table>		
			
		</form>
		
	</body>
	<script language="javascript">
		function limpiar()
		{
			document.archivar.elements['edificio'].value = 0;
			document.archivar.elements['piso'].value = 0;
			document.archivar.elements['estante'].value = 0;
			document.archivar.elements['entrepano'].value = 0;
			document.archivar.elements['cajaIni'].value	= '0';
			document.archivar.elements['cajaCan'].value = '0';
			document.archivar.elements['carpIni'].value = '0';
			document.archivar.elements['carpCan'].value = '0';
		}
		
		
		function atras() 
		{
			window.location.href = "./consultar.php?exp=<?=$exp?>&login=<?=$login?>";
		}
		
		function verifica() 
		{
			//SE VALIDA QUE EL CAMPO No. FOLIOS NO VENGA VACIO
			if (document.archivar.folios.value.length == 0){
				alert("Debe digitar la cantidad de Folios del radicado")
				document.archivar.folios.focus()
				return 0;
			} 
				
			//SE VALIDA QUE EL CAMPO EDIFICIO NO VENGA VACIO
			if (document.archivar.edificio.value <= 0 ){
				alert("Debe seleccionar un Edificio")
				document.archivar.edificio.focus()
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO PISO NO VENGA VACIO
			if (document.archivar.piso.value <= 0){
				alert("Debe seleccionar un Piso")
				document.archivar.piso.focus()
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO ESTANTE NO VENGA VACIO
			if (document.archivar.estante.value <= 0){
				alert("Debe seleccionar un Estante")
				document.archivar.estante.focus()
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO ENTREPANO NO VENGA VACIO
			if (document.archivar.entrepano.value <= 0){
				alert("Debe seleccionar un Entrepa\xf1o")
				document.archivar.entrepano.focus()
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO CAJA INICIAL NO VENGA VACIO
			if (document.archivar.cajaIni.value <= 0){
				alert("Debe digitar el n\xfamero de la Caja inicial")
				document.archivar.cajaIni.focus()
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO CANT. CAJAS NO VENGA VACIO
			if (document.archivar.cajaCan.value <= 0){
				alert("Debe digitar la cantidad de Cajas")
				document.archivar.cajaCan.focus()
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO CARPETA INICIAL NO VENGA VACIO
			if (document.archivar.carpIni.value <= 0){
				alert("Debe digitar el n\xfamero de la Carpeta inicial")
				document.archivar.carpIni.focus()
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO CANT. CARPETAS NO VENGA VACIO
			if (document.archivar.carpCan.value <= 0){
				alert("Debe digitar la cantidad de Carpetas")
				document.archivar.carpCan.focus()
				return 0;
			}
			
			document.archivar.elements['archivarOk'].value = 1;
			document.archivar.submit();
		}
	</script>
</html>