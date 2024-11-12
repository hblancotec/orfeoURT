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
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	$db = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	
	
	if ($_POST) {
		$edificio = $_POST['selEd'];
		if ($_POST['seccion'] == 1) {
			include ("estructuraTx.php");
		}
	}
	elseif ($_GET) {
		$edificio = $_GET['edi'];
		$codSec = $_GET['codSec'];
		$nomSec = $_GET['nom'];
		$sigSec = $_GET['sig'];
		$estSec = $_GET['est'];
	}
	
	if($codSec){
		$boton = "Modificar";
	}
	else {
		$boton = "Crear";
	}
	
	if ($estSec == 1) {
		$on = 'selected';
		$off = '';
	}
	elseif ($estSec == 2){
		$on = '';
		$off = 'selected';
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Administrador de Secciones / Pisos </title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../../estilos/orfeo.css">
	</head>
	<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
		<form name="frm_admSeccion" method="post" action="adm_seccion.php">
			<table width="50%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
				<tr>
					<td height="30" colspan="4" class="titulos4" align="center">
						Administrador de Secciones / Pisos
					</td>
				</tr>				
				<tr>
					<td class="titulos2" width="20%"> Edificio: </td>
					<td class="listado2" width="80%" colspan="3">
						<select name='selEd' class='select'>
							<?php
								echo "<option value='0' $datos>-- Seleccione --</option>\n";
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
				</tr>
				<tr>
					<td class="titulos2" width="20%"> Nombre: </td>
					<td class="listado2" width="80%" colspan="3"> 
						<input type="text" class="tex_area" name="nomSec" size="70" value="<?php echo $nomSec; ?>" >
					</td>
				</tr>
				<tr>
					<td class="titulos2" width="20%"> Sigla: </td>
					<td class="listado2" width="25%"> 
						<input type="text" class="tex_area" name="sigSec" size="15" maxlength="8" value="<?php echo $sigSec; ?>" >
					</td>				
					<td class="titulos2" width="20%"> Estado: </td>
					<td class="listado2" width="25%"> 
						<select name="estSec" class="select">
							<option value="" selected>-- seleccione --</option>
							<option value="1" <?= $on ?>> Activo </option>
							<option value="2" <?= $off ?>> Inactivo </option>
						</select>
					</td>
				</tr>				
				<tr>
					<td class="titulos2" width="20%"> Cantidad Est: </td>
					<td class="listado2" width="25%" colspan="3">
						<input type="text" class="tex_area" name="canEst" size="15" maxlength="4" value="<?php echo $canEst; ?>" >
						<font size="1" color='#FF0000'> 
							* Si desea crear los estantes en este mismo paso, por favor digitar la cantidad
						</font>
					</td>
				</tr>

				<input type="hidden" name="codSec" value="<?php echo $codSec; ?>" >
				<input type="hidden" name="seccion" value="0" >
				
				<tr align="center">
					<td colspan="4">
						<input class='botones' type='button' name='btn_atras' value='Atras' onclick='atras();'> &nbsp; &nbsp;
						<input class='botones' type='submit' name='btn_limpiar' value='Limpiar' onclick='limpiar();'> &nbsp; &nbsp;
						<input class="botones" type="submit" name="btn_piso" value="<?php echo $boton;?>" onClick="return verifica(this.value);">
					</td>
				</tr>
			</table>
			<br/>
			
			<?php
				$sqlSec = "	SELECT	P.SGD_EDIFICIO_COD AS COD_ED,
									E.SGD_EDIFICIO_NOMB AS NOM_ED,
									P.SGD_PISO_COD AS COD, 
									P.SGD_PISO_DESC AS NOM, 
									P.SGD_PISO_SIGLA AS SIG,
									P.SGD_PISO_ESTADO AS EST
							FROM	SGD_PISO_ARCHIVO AS P
									JOIN SGD_EDIFICIO_ARCHIVO AS E ON
										E.SGD_EDIFICIO_COD = P.SGD_EDIFICIO_COD
							ORDER BY NOM_ED, NOM";
				$rsSec = $db->conn->Execute($sqlSec);
				
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
			?>
			<table align="center" width="50%">
				<tr>
					<td class="titulos4" align="center"> Edificio </td>
					<td class="titulos4" align="center"> Secci&oacute;n	</td>
					<td class="titulos4" align="center"> Sigla	</td>
					<td class="titulos4" align="center"> Estado	</td>
					<td class="titulos4" align="center"> Cant. Estantes	</td>
					<td class="titulos4" align="center"> Editar	</td>
				</tr>
				<?php
					while (!$rsSec->EOF) {
						if ($rsSec->fields['EST'] == 1)
							$estado = "ACTIVO";
						elseif ($rsSec->fields['EST'] == 2)
							$estado = "INACTIVO";
						
						$sqlCant = "SELECT	COUNT(SGD_EST_COD)
									FROM	SGD_ESTANTE_ARCHIVO
									WHERE	SGD_PISO_COD = ".$rsSec->fields['COD'];
						$rsCant = $db->conn->getOne($sqlCant);
				?>
				
						<tr>
							<td class="listado2_center"> <?php echo $rsSec->fields['NOM_ED'];?> </td>
							<td class="listado2_center"> <?php echo $rsSec->fields['NOM'];?> </td>
							<td class="listado2_center"> <?php echo $rsSec->fields['SIG'];?> </td>
							<td class="listado2_center"> <?php echo $estado;?> </td>
							<td class="listado2_center"> <?php echo $rsCant;?> </td>
							<td class="listado2_center">
								<a href="adm_seccion.php?codSec=<?=$rsSec->fields['COD']?>&nom=<?=$rsSec->fields['NOM']?>
									&sig=<?=$rsSec->fields['SIG']?>&est=<?=$rsSec->fields['EST']?>
									&edi=<?=$rsSec->fields['COD_ED']?> "> EDITAR 
								</a>
							</td>
						</tr>
					
				<?php
						$rsSec->MoveNext();
					}
				?>
			</table>
		</form>
		<script language="javascript">
			function limpiar()
			{
				document.frm_admSeccion.elements['selEd'].value = 0;
				document.frm_admSeccion.elements['nomSec'].value = '';
				document.frm_admSeccion.elements['sigSec'].value = '';
				document.frm_admSeccion.elements['estSec'].value = '';
				document.frm_admSeccion.elements['codSec'].value = 0;
				document.frm_admSeccion.elements['canEst'].value = '';
			}


			function atras()
			{
				window.location.href = "./menu_estructura.php";
			}

			function verifica(accion)
			{
				//SE VALIDA SI SELECCIONO BTN_MODIFICAR Y VIENE UN CODIGO DE EDIFICIO
				if (accion === "Modificar" && document.frm_admSeccion.codSec.value === ""){
					alert("Para Modificar debe seleccionar previamente un Piso");
					return 0;
				}
		
				//SE VALIDA QUE EL CAMPO EDIFICIO NO VENGA VACIO
				if (document.frm_admSeccion.selEd.value <= 0){
					alert("Debe seleccionar un Edificio");
					document.frm_admSeccion.selEd.focus();
					return 0;
				}

				//SE VALIDA QUE EL CAMPO NOMBRE NO VENGA VACIO
				if (document.frm_admSeccion.nomSec.value <= 0 ){
					alert("Debe digitar un Nombre");
					document.frm_admSeccion.nomSec.focus();
					return 0;
				}

				//SE VALIDA QUE EL CAMPO SIGLA NO VENGA VACIO
				if (document.frm_admSeccion.sigSec.value <= 0){
					alert("Debe digitar la sigla");
					document.frm_admSeccion.sigSec.focus();
					return 0;
				}
				
				//SE VALIDA QUE EL CAMPO ESTADO NO VENGA VACIO
				if (document.frm_admSeccion.estSec.value <= 0){
					alert("Debe seleccionar el Estado");
					document.frm_admSeccion.estSec.focus();
					return 0;
				}
				
				document.frm_admSeccion.elements['seccion'].value = 1;
				document.frm_admSeccion.submit();
			};
		</script>
	</body>
</html>