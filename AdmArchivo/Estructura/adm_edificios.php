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

	$ruta_raiz = "../..";
	include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($_POST) {
		$continente = $_POST['selCont'];
		$pais = $_POST['selPais'];
		$dpto = $_POST['selDpto'];
		$mpio = $_POST['selMpio'];
		if ($_POST['edificio'] == 1) {
			include ("estructuraTx.php");
		}
	}
	elseif (isset($_GET)) {
		$codEd = $_GET['cod'];
		$nomEd = $_GET['nom'];
		$dirEd = $_GET['dir'];
		$sigEd = $_GET['sig'];
		$estEd = $_GET['est'];
		$continente = $_GET['cont'];
		$pais = $_GET['pa'];
		$dpto = $_GET['dpto'];
		$mpio = $_GET['mpio'];
	}
	
	
	if($codEd){
		$boton = "Modificar";
	}
	else {
		$boton = "Crear";
	}
	
	if ($estEd == 1) {
		$on = 'selected';
		$off = '';
	}
	elseif ($estEd == 2){
		$on = '';
		$off = 'selected';
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Administrador de Edificios </title>
		<meta http-equiv="Content-Type" content="text/html;">
		<link rel="stylesheet" href="../../estilos/orfeo.css">
	</head>
	<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
		<form name="frm_admEdificio" method="post" action="adm_edificios.php">
			<table width="50%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
				<tr>
					<td height="40%" colspan="4" class="titulos4" align="center">
						Administrador de Edificios
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="20%">Ubicaci&oacute;n:</td>
					<td class="listado2" width="75%" colspan="3">
						<table>
							<tr>
								<!-- CAMPO PARA EL CONTINENTE -->
								<td width="15%"> Continente: </td>
								<td width="30%">
									<select name='selCont' class='select' onChange='frm_admEdificio.submit();'>
									<?php
										$datos= "selected";
										echo "<option value='0' $datos>-- Seleccione --</option>\n";

										$sqlCon = "	SELECT	ID_CONT,
															NOMBRE_CONT
													FROM	SGD_DEF_CONTINENTES
													ORDER BY NOMBRE_CONT";
										$rsCon = $db->conn->Execute($sqlCon);
										do{
											$contCod = $rsCon->fields["ID_CONT"];
											$contNom = $rsCon->fields["NOMBRE_CONT"];
											$datos="";
											if($continente==$contCod)
												$datos = "selected";
											echo "<option value='$contCod' $datos> $contNom </option>\n";
											$rsCon->MoveNext();
										}
										while(!$rsCon->EOF);
									  ?>
									</select>
								</td>
								<!-- FIN CAMPO PARA EL CONTINENTE -->


								<!-- CAMPO PARA EL PAIS -->
								<td width="15%" > Pa&iacute;s </td>
								<td width="30%">
									<?php
										if(!$continente)	$pais = 0;
										$datosP = "";
									?>
									<select name=selPais  class="select"  onChange="frm_admEdificio.submit();">

										<?php
											echo "<option value='0' $datosP>-- Seleccione --</option>\n";
											
											###	CONSULTA DE LOS PAISES QUE PERTENECEN AL CONTINENTE SELECCIONADO
											if($continente > 0){
												$sqlPais = "SELECT	ID_PAIS,
																	NOMBRE_PAIS
															FROM	SGD_DEF_PAISES
															WHERE	ID_CONT = $continente
															ORDER BY NOMBRE_PAIS";
												$rsPais = $db->conn->Execute($sqlPais);

												while(!$rsPais->EOF) {
													$paisCod = $rsPais->fields["ID_PAIS"];
													$paisNom = $rsPais->fields["NOMBRE_PAIS"];
													$datosP  = ($pais == $paisCod)? $datosP = " selected ":"";
													echo "<option value='$paisCod' $datosP> $paisNom </option>";
													$rsPais->MoveNext();
												}
											}
										?>

									</select>
								</td>
								<!-- FIN CAMPO PARA EL PAIS -->
							</tr>

							<tr>
								<!-- CAMPO PARA EL DEPARTAMENTO -->
								<td width="15%">  Departamento <br/> </td>
								<td width="30%">
									<?php
										if(!$pais)	$dpto = 0;
										$datosD = "";
									?>
									<select name=selDpto class="select"  onChange="frm_admEdificio.submit();">
										<?php
											$datosD = "selected";
											echo "<option value='0' $datosD>-- Seleccione --</option>\n";
										
											###	CONSULTA DE LOS DEPTOS QUE PERTENECEN AL CONTINENTE Y PAIS SELECCIONADO
											if($pais > 0){
												$sqlDpto = "SELECT	DPTO_CODI,
																	DPTO_NOMB
															FROM	DEPARTAMENTO
															WHERE	ID_PAIS = $pais AND
																	ID_CONT = $continente
															ORDER BY DPTO_NOMB";
												$rsDpto = $db->conn->Execute($sqlDpto);

												while(!$rsDpto->EOF) {
													$dptoCod = $rsDpto->fields['DPTO_CODI'];
													$dptoNom = $rsDpto->fields['DPTO_NOMB'];
													$datosD  = ($dpto == $dptoCod)? $datosD= " selected ":"";
													echo "<option value='$dptoCod' $datosD> $dptoNom </option>";
													$rsDpto->MoveNext();
												}
											}												
										?>
									</select>
								</td>
								<!-- FIN CAMPO PARA EL DEPARTAMENTO -->


								<!-- CAMPO PARA EL MUNICIPIO -->
								<td width="15%"> Municipio <br/>  </td>
									
								<td width="30%">
									<select name=selMpio class="select" onChange="frm_admEdificio.submit();">
										<?php
											$datosM = "";
											echo "<option value='0' $datosM>-- Seleccione --</option>\n";
											
											###	CONSULTA DE LOS MPIOS QUE PERTENECEN AL PAIS Y DEPTOS ELECCIONADO
											if($dpto > 0){
												$sqlMpio = "SELECT	MUNI_CODI,
																	MUNI_NOMB
															FROM	MUNICIPIO
															WHERE	ID_PAIS = $pais AND
																	ID_CONT = $continente AND
																	DPTO_CODI = $dpto
															ORDER BY MUNI_NOMB";
												$rsMpio = $db->conn->Execute($sqlMpio);

												while(!$rsMpio->EOF) {
													$mpioCod = $rsMpio->fields['MUNI_CODI'];
													$mpioNom = $rsMpio->fields['MUNI_NOMB'];
													$datosM  = ($mpio == $mpioCod)? $datosM = " selected ":"";
													echo "<option value='$mpioCod' $datosM> $mpioNom </option>";
													$rsMpio->MoveNext();
												}
											}
										?>
									</select>
								</td>
								<!-- FIN CAMPO PARA  EL MUNICIPIO -->
							</tr>
						</table>
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="20%"> Nombre:</td>
					<td class="listado2" width="75%" colspan="3"> 
						<input type="text" class="tex_area" name="nomEd" size="70" value="<?php echo $nomEd; ?>" >
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="20%"> Direcci&oacute;n:</td>
					<td class="listado2" width="75%" colspan="3"> 
						<input type="text" class="tex_area" name="dirEd" size="70" value="<?php echo $dirEd; ?>" >
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="15%"> Sigla: </td>
					<td class="listado2" width="25%"> 
						<input type="text" class="tex_area" name="sigEd" size="20" maxlength="8" value="<?php echo $sigEd; ?>" >
					</td>
				
					<td class="titulos2" width="15%"> Estado: </td>
					<td class="listado2" width="25%"> 
						<select name="estEd" id="estEd" class="select">
							<option value="" selected>-- seleccione --</option>
							<option value="1" <?= $on ?>> Activo </option>
							<option value="2" <?= $off ?>> Inactivo </option>
						</select>
					</td>
				</tr>
				<input type="hidden" name="codEd" value="<?php echo $codEd; ?>" >
				<input type="hidden" name="edificio" value="0">
				
				<tr align="center">
					<td colspan="4">
						<input class="botones" type="button" name="btn_atras" value="Atras" onclick="atras();"> &nbsp; &nbsp;
						<input class="botones" type="submit" name="btn_limpiar" value="Limpiar" onclick='limpiar();'> &nbsp; &nbsp;
						<input class="botones" type="submit" name="btn_edif" value="<?php echo $boton; ?>" onClick="return verifica(this.value);">
					</td>
				</tr>
			</table>
			<br/>
			
			<?php
				$sqlEdi = "	SELECT	SGD_EDIFICIO_COD AS COD,
									SGD_EDIFICIO_NOMB AS NOMB,
									SGD_EDIFICIO_SIGLA AS SIG,
									SGD_EDIFICIO_DIR AS DIR,
									SGD_EDIFICIO_ESTADO AS EST,
									SGD_EDIFICIO_CONT AS CONT,
									SGD_EDIFICIO_PAIS AS PAIS,
									SGD_EDIFICIO_DPTO AS DPTO,
									SGD_EDIFICIO_MPIO AS MPIO
							FROM	SGD_EDIFICIO_ARCHIVO";
				$rsEdi = $db->conn->Execute($sqlEdi);
				
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
			
			<table align="center" width="60%">
				<tr>
					<td class="titulos4" align="center"> Edificio	</td>
					<td class="titulos4" align="center"> Sigla		</td>
					<td class="titulos4" align="center"> Direcci&oacute;n	</td>
					<td class="titulos4" align="center"> Estado		</td>
					<td class="titulos4" align="center"> Editar		</td>
				</tr>
				
				<?php
					while (!$rsEdi->EOF) {
						if ($rsEdi->fields['EST'] == 1)
							$estado = "ACTIVO";
						else
							$estado = "INACTIVO";
				?>
				
						<tr>
							<td class="listado2_center"> <?php echo $rsEdi->fields['NOMB'];?> </td>
							<td class="listado2_center"> <?php echo $rsEdi->fields['SIG'];?> </td>
							<td class="listado2_center"> <?php echo $rsEdi->fields['DIR'];?> </td>
							<td class="listado2_center"> <?php echo $estado;?> </td>
							<td class="listado2_center">
								<a href="adm_edificios.php?cod=<?=$rsEdi->fields['COD']?>&nom=<?=$rsEdi->fields['NOMB']?>
									&sig=<?=$rsEdi->fields['SIG']?>&dir=<?=$rsEdi->fields['DIR']?>&est=<?=$rsEdi->fields['EST']?>
									&cont=<?=$rsEdi->fields['CONT']?>&pa=<?=$rsEdi->fields['PAIS']?>&dpto=<?=$rsEdi->fields['DPTO']?>
									&mpio=<?=$rsEdi->fields['MPIO']?>" > EDITAR 
								</a>
							</td>
						</tr>
					
				<?php
						$rsEdi->MoveNext();
					}
				?>
			</table>
		</form>
		
		<script language="javascript">
			function limpiar()
			{
				document.frm_admEdificio.elements['selCont'].value = 0;
				document.frm_admEdificio.elements['selPais'].value = 0;
				document.frm_admEdificio.elements['selDpto'].value = 0;
				document.frm_admEdificio.elements['selMpio'].value = 0;
				document.frm_admEdificio.elements['dirEd'].value = '';
				document.frm_admEdificio.elements['nomEd'].value = '';
				document.frm_admEdificio.elements['estEd'].value = '';
				document.frm_admEdificio.elements['sigEd'].value = '';
				document.frm_admEdificio.elements['codEd'].value = '';
			}


			function atras() 
			{
				window.location.href = "./menu_estructura.php";
			}

			function verifica(accion) 
			{
				//SE VALIDA SI SELECCIONO BTN_MODIFICAR Y VIENE UN CODIGO DE EDIFICIO
				if (accion === "Modificar" && document.frm_admEdificio.codEd.value === ""){
					alert("Para Modificar debe seleccionar previamente un Edificio");
					return 0;
				}
				
				//SE VALIDA QUE EL CAMPO CONTINENTE NO VENGA VACIO
				if (document.frm_admEdificio.selCont.value <= 0){
					alert("Debe seleccionar un Continente");
					document.frm_admEdificio.selCont.focus();
					return 0;
				} 

				//SE VALIDA QUE EL CAMPO PAIS NO VENGA VACIO
				if (document.frm_admEdificio.selPais.value <= 0 ){
					alert("Debe seleccionar un Pa\xeds");
					document.frm_admEdificio.selPais.focus();
					return 0;
				}

				//SE VALIDA QUE EL CAMPO DEPARTAMENTO NO VENGA VACIO
				if (document.frm_admEdificio.selDpto.value <= 0){
					alert("Debe seleccionar un Departamento");
					document.frm_admEdificio.selDpto.focus();
					return 0;
				}
				
				//SE VALIDA QUE EL CAMPO MUNICIPIO NO VENGA VACIO
				if (document.frm_admEdificio.selMpio.value <= 0){
					alert("Debe seleccionar un Municipio");
					document.frm_admEdificio.selMpio.focus();
					return 0;
				}

				//SE VALIDA QUE EL CAMPO DIRECCION NO VENGA VACIO
				if (document.frm_admEdificio.dirEd.value <= 0){
					alert("Debe digitar la direcci\xf3n del Edificio");
					document.archivar.dirEd.focus();
					return 0;
				}

				//SE VALIDA QUE EL CAMPO NOMBRE NO VENGA VACIO
				if (document.frm_admEdificio.nomEd.value <= 0){
					alert("Debe asignar un nombre");
					document.frm_admEdificio.nomEd.focus();
					return 0;
				}
				
				//SE VALIDA QUE EL CAMPO ESTADO NO VENGA VACIO
				if (document.frm_admEdificio.estEd.value <= 0){
					alert("Debe seleccionar el estado del Edificio");
					document.archivar.estEd.focus();
					return 0;
				}

				//SE VALIDA QUE EL CAMPO SIGLA NO VENGA VACIO
				if (document.frm_admEdificio.sigEd.value <= 0){
					alert("Debe asignar una sigla al Edificio");
					document.frm_admEdificio.sigEd.focus();
					return 0;
				}
				
				document.frm_admEdificio.elements['edificio'].value = 1;
				document.frm_admEdificio.submit();
			}
		</script>
		
	</body>
</html>