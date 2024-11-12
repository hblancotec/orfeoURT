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

	$ruta_raiz = "../..";
	include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	
	
	if ($_POST) {
		$edificio = $_POST['selEd'];
		$piso = $_POST['selPiso'];
		if ($_POST['estantes'] == 1) {
			include ("estructuraTx.php");
		}
	}
	elseif ($_GET) {
		$edificio = trim($_GET['edi']);
		$piso	= trim($_GET['piso']);
		$nomEst = trim($_GET['nomEst']);
		$sigEst = trim($_GET['sigEst']);
		$refEst = trim($_GET['refEst']);
		$estEst = trim($_GET['estEst']);
		$codEst = trim($_GET['codEst']);
	}
	
	if($codEst){
		$boton = "Modificar";
	}
	else {
		$boton = "Crear";
	}
	
	if ($estEst == 1) {
		$on = 'selected';
		$off = '';
	}
	elseif ($estEst == 2){
		$on = '';
		$off = 'selected';
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Administrador de Estantes </title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../../estilos/orfeo.css" type="text/css">
	</head>
	<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
		<form name="frm_admEstantes" method="post" action="adm_estantes.php">
			<table width="50%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
				<tr>
					<td height="30" colspan="4" class="titulos4" align="center">
						Administrador de Estantes
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="15%"> Edificio: </td>
					<td class="listado2" width="30%">
						<select name='selEd' class='select' onChange='frm_admEstantes.submit();'>
							<?php
								$datos= " selected ";
								echo "<option value='0' $datos> ---Seleccione... </option>\n";
								$sqlEdi = "	SELECT	SGD_EDIFICIO_COD,
													SGD_EDIFICIO_NOMB
											FROM	SGD_EDIFICIO_ARCHIVO
											WHERE	SGD_EDIFICIO_ESTADO = 1
											ORDER BY SGD_EDIFICIO_NOMB";
								$rsEdi = $db->conn->Execute($sqlEdi);
								do{
									$ediCod = $rsEdi->fields["SGD_EDIFICIO_COD"];
									$ediNom = $rsEdi->fields["SGD_EDIFICIO_NOMB"];
									$datos="";
									if($edificio == $ediCod){
										$datos= " selected ";
									}
									echo "<option value='$ediCod' $datos> $ediNom </option>\n";
									$rsEdi->MoveNext();
								}
								while(!$rsEdi->EOF);
							?>
						</select>
					</td>
				
					<td class="titulos2" width="15%"> Secci&oacute;n: </td>
					<td class="listado2" width="30%">
						<select name='selPiso' class='select' onChange='frm_admEstantes.submit();'>
							<?php
								$datosP= "selected";
								echo "<option value='0' $datos> ---Seleccione... </option>\n";
								
								if ($edificio){
									$sqlPi = "	SELECT	SGD_PISO_COD,
														SGD_PISO_DESC
												FROM	SGD_PISO_ARCHIVO
												WHERE	SGD_EDIFICIO_COD = ".$edificio."
												ORDER BY SGD_PISO_DESC";
									$rsPi = $db->conn->Execute($sqlPi);

									do{
										$codPi = $rsPi->fields["SGD_PISO_COD"];
										$nomPi = $rsPi->fields["SGD_PISO_DESC"];
										$datosP="";
										if($piso==$codPi)
											$datosP= "selected";
										echo "<option value='$codPi' $datosP> $nomPi </option>\n";
										$rsPi->MoveNext();
									}
									while(!$rsPi->EOF);
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="15%"> Nombre: </td>
					<td class="listado2" width="30%"> 
						<input type="text" class="tex_area" name="nomEst" size="20" maxlength="40" value="<?php echo $nomEst; ?>" >
					</td>
				
					<td class="titulos2" width="15%"> Sigla: </td>
					<td class="listado2" width="30%"> 
						<input type="text" class="tex_area" name="sigEst" size="13" maxlength="8" value="<?php echo $sigEst; ?>" >
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="15%"> Referencia: </td>
					<td class="listado2" width="30%">	
						<input type="text" class="tex_area" name="refEst" size="20" maxlength="20" value="<?php echo $refEst; ?>" >
					</td>
				
					<td class="titulos2" width="15%"> Estado: </td>
					<td class="listado2" width="30%"> 
						<select name="estEst" class="select">
							<option value="" selected>-- seleccione --</option>
							<option value="1" <?= $on ?>> Activo </option>
							<option value="2" <?= $off ?>> Inactivo </option>
						</select>
					</td>
				</tr>
				<input type="hidden" name="codEst" value="<?php echo $codEst; ?>" >
				<input type="hidden" name="estantes" value="0" >
				
				<tr align="center">
					<td colspan="4">
						<input class="botones" type="button" name="btn_atras" value='Atras' onclick='atras();'> &nbsp; &nbsp;
						<input class="botones" type="submit" name="btn_limpiar" value='Limpiar' onclick='limpiar();'> &nbsp; &nbsp;
						<input class="botones" type="submit" name="btn_estante" value="<?php echo $boton; ?>" onClick="return verifica(this.value);">
					</td>
				</tr>
			</table>
			<br/>
			
			<?php
			if ($mensg){
			?>
			
				<table align="center" width="60%">
					<tr>
						<td class="listado2_center">
							<font size="2" color='#FF0000'> <?php echo $mensg; ?> </font>
						</td>	
					</tr>
				</table>
				<br/>
			<?php
			}
			
 			if ($edificio && $piso){
				$sqlEst = "	SELECT	ED.SGD_EDIFICIO_COD AS COD_ED,
									ED.SGD_EDIFICIO_NOMB AS NOM_ED,
									PI.SGD_PISO_COD AS COD_PI,
									PI.SGD_PISO_DESC AS NOM_PI,
									ES.SGD_EST_COD AS COD_ES,
									ES.SGD_EST_DESC AS NOM_ES,
									ES.SGD_EST_SIGLA AS SIG_ES,
									ES.SGD_EST_ID AS REF_ES,
									ES.SGD_EST_ESTADO AS EST_ES
							FROM	SGD_ESTANTE_ARCHIVO AS ES
									JOIN SGD_PISO_ARCHIVO AS PI ON
										PI.SGD_PISO_COD = ES.SGD_PISO_COD
									JOIN SGD_EDIFICIO_ARCHIVO AS ED ON
										ED.SGD_EDIFICIO_COD = PI.SGD_EDIFICIO_COD AND
										ED.SGD_EDIFICIO_COD = $edificio
							WHERE	ES.SGD_PISO_COD = $piso
							ORDER BY NOM_PI";
				$rsEst = $db->conn->Execute($sqlEst);
			?>
			
				<table align="center" width="50%">
					<tr>
						<td class="titulos4" align="center"> Edificio </td>
						<td class="titulos4" align="center"> SECCI&Oacute;N	</td>
						<td class="titulos4" align="center"> ESTANTE </td>
						<td class="titulos4" align="center"> SIGLA </td>
						<td class="titulos4" align="center"> REFERENCIA </td>
						<td class="titulos4" align="center"> ESTADO </td>
						<td class="titulos4" align="center"> EDITAR	</td>
					</tr>

					<?php
					while (!$rsEst->EOF) {
						if ($rsEst->fields['EST_ES'] == 1)
							$estado = "ACTIVO";
						elseif ($rsEst->fields['EST_ES'] == 2)
							$estado = "INACTIVO";
					?>

						<tr>
							<td class="listado2_center"> <?php echo $rsEst->fields['NOM_ED'];?> </td>
							<td class="listado2_center"> <?php echo $rsEst->fields['NOM_PI'];?> </td>
							<td class="listado2_center"> <?php echo $rsEst->fields['NOM_ES'];?> </td>
							<td class="listado2_center"> <?php echo $rsEst->fields['SIG_ES'];?> </td>
							<td class="listado2_center"> <?php echo $rsEst->fields['REF_ES'];?> </td>
							<td class="listado2_center"> <?php echo $estado;?> </td>
							<td class="listado2_center">
								<a href="adm_estantes.php?codEst=<?=$rsEst->fields['COD_ES']?>
														 &nomEst=<?=$rsEst->fields['NOM_ES']?>
														 &sigEst=<?=$rsEst->fields['SIG_ES']?>
														 &estEst=<?=$rsEst->fields['EST_ES']?>
														 &refEst=<?=$rsEst->fields['REF_ES']?>
														 &edi=<?=$rsEst->fields['COD_ED']?>
														 &piso=<?=$rsEst->fields['COD_PI']?> "> 
									EDITAR
								</a>
							</td>
						</tr>

					<?php
						$rsEst->MoveNext();
					}
					?>
				</table>
			<?php
			}
			?>	
			
		</form>
	</body>
	<script language="javascript">
		function atras()
		{
			window.location.href = "./menu_estructura.php";
		}
		
		
		function limpiar()
		{
			document.frm_admEstantes.elements['nomEst'].value = '';
			document.frm_admEstantes.elements['sigEst'].value = '';
			document.frm_admEstantes.elements['refEst'].value = '';
			document.frm_admEstantes.elements['codEst'].value = '';
			document.frm_admEstantes.elements['selEd'].value = 0;
			document.frm_admEstantes.elements['selPi'].value = 0;
			document.frm_admEstantes.elements['estEst'].value = 0;
		}


		function verifica(accion)
		{
			//SE VALIDA SI SELECCIONO BTN_MODIFICAR Y VIENE UN CODIGO DE EDIFICIO
			if (accion === "Modificar" && document.frm_admEstantes.codEst.value === ""){
				alert("Para Modificar debe seleccionar previamente un Estante");
				return 0;
			}
				
			//SE VALIDA QUE EL CAMPO EDIFICIO NO VENGA VACIO
			if (document.frm_admEstantes.selEd.value <= 0){
				alert("Debe seleccionar un Edificio");
				document.frm_admEstantes.selEd.focus();
				return 0;
			}
			
			//SE VALIDA QUE EL CAMPO SECCION NO VENGA VACIO
			if (document.frm_admEstantes.selPiso.value <= 0){
				alert("Debe seleccionar un Piso");
				document.frm_admEstantes.selPiso.focus();
				return 0;
			}

			//SE VALIDA QUE EL CAMPO NOMBRE NO VENGA VACIO
			if (document.frm_admEstantes.nomEst.value.length === 0 ){
				alert("Debe digitar un Nombre");
				document.frm_admEstantes.nomEst.focus();
				return 0;
			}

			//SE VALIDA QUE EL CAMPO SIGLA NO VENGA VACIO
			if (document.frm_admEstantes.sigEst.value.length === 0){
				alert("Debe digitar la sigla");
				document.frm_admEstantes.sigEst.focus();
				return 0;
			}
				
			//SE VALIDA QUE EL CAMPO ESTADO NO VENGA VACIO
			if (document.frm_admEstantes.estEst.value.length <= 0){
				alert("Debe seleccionar el Estado");
				document.frm_admEstantes.estEst.focus();
				return 0;
			}
			
			document.frm_admEstantes.elements['estantes'].value = 1;
			document.frm_admEstantes.submit();
		}
	</script>
</html>	

