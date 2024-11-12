<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
if ($_SESSION['usua_perm_trd'] != 1) {
    die(include $ruta_raiz . "/sinpermiso.php");
    exit();
}

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
if (!defined('ADODB_FETCH_ASSOC')){
	define('ADODB_FETCH_ASSOC', 2);
}

### SI SE VA A EDITAR EL TIPO DOCUMENTAL
if($_GET['edit'] > 0){
	$where = " WHERE ID = " .$_GET['edit'];
}

if ($_POST['val'] == 'modificar'){
	$where = " WHERE SGD_TPR_CODIGO = " .$_POST['codigo'];
}

if ($version) {
    $version = strtoupper(trim($version));
} else {
    $version = 0;
}

include "admin_tipdoc_tx.php";

### SE CAPTURAN EL RESULTADO DEL QUERY PARA MOSTRARLO EN EL FORMULARIO
if($_GET['edit'] > 0){
	$cod = $rs->fields['CODIGO'];
	$tipDoc = $rs->fields['DESCRIP'];
	$termino = $rs->fields['TERMINO'];
	$alerta = $rs->fields['ALERTA'];
	if ($rs->fields['NOTIFICA'] == 1) { $notifica = 1; }
	if ($rs->fields['REPORT1'] == 1)  { $reporte = 1; }
	if ($rs->fields['TP1'] == 1)	{ $salida = 1; 	}
	if ($rs->fields['TP2'] == 1)	{ $entrada = 1; }
	if ($rs->fields['TP3'] == 1)	{ $memorando = 1; }
	if ($rs->fields['TP4'] == 1)	{ $cirExt = 1; }
	if ($rs->fields['TP5'] == 1)	{ $resolucion = 1; }
	if ($rs->fields['TP6'] == 1)	{ $concepto = 1; }
	if ($rs->fields['TP7'] == 1)	{ $edicto = 1; }
	if ($rs->fields['TP8'] == 1)	{ $circular = 1; }
	if ($rs->fields['TP9'] == 1)	{ $auto = 1; }
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title> Administrador de Tipos Documentales </title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
	</head>
	<body>
		<script language="javascript">
    		function selectVersion() {
    			document.getElementById("admin").submit();
    		}
		
			function limpiar() {
				document.admin.elements.codigo.value = "";
				document.admin.elements.tipDoc.value = "";
				document.admin.elements.termino.value = "";
				document.admin.elements.alerta.value = "";
				document.admin.elements.notifica.checked=0;
				document.admin.elements.reporte.checked=0;
				document.admin.elements.salida.checked=0;
				document.admin.elements.entrada.checked=0;
				document.admin.elements.memorando.checked=0;
				document.admin.elements.cirExt.checked=0;
				document.admin.elements.resolucion.checked=0;
				document.admin.elements.concepto.checked=0;
				document.admin.elements.edicto.checked=0;
				document.admin.elements.circular.checked=0;
				document.admin.elements.auto.checked=0;
			}

			function valida() {
				var alerta = parseInt(document.admin.elements.alerta.value);
				var termino = parseInt(document.admin.elements.termino.value);
				
				if (document.admin.elements.tipDoc.value == ""){
					alert("Por favor digitar el Tipo Documental");
					document.admin.tipDoc.focus()
				}
				else if ( parseInt(alerta) > parseInt(termino) ){
					alert("El tiempo para inicio de la Alerta " + alerta + " debe ser inferior al Termino " + termino);
					document.admin.alerta.focus()
				}
				else{
					document.admin.val.value = "nuevo";
					document.admin.submit();
				}
			}
			
			function modifica() {
				var alerta = document.admin.elements.alerta.value;
				var termino = document.admin.elements.termino.value;
				
				if (document.admin.elements.codigo.value == ""){
					alert("Por favor debe seleccionar un Tipo Documental");
					document.admin.codigo.focus()
				}
				else if (document.admin.elements.tipDoc.value == ""){
					alert("Por favor digitar el nombre del Tipo Documental");
					document.admin.tipDoc.focus()
				}
				else if ( parseInt(alerta) > parseInt(termino) ){
					alert("El tiempo para inicio de la Alerta " + alerta + " debe ser inferior al Termino " + termino);
					document.admin.alerta.focus()
				}
				else{
					document.admin.val.value = "modificar";
					document.admin.submit();
				}
			}
		</script>
		
		<form method="post" action="admin_tipodoc.php" name="admin" id="admin">
			<table width="50%" align="center" border="0" cellpadding="0" cellspacing="9" class="borde_tab">
				
				<tr>
					<td class="titulos4" colspan="8" align="center"> Administrador de Tipos Documentales </td>
				</tr>
				
				<tr>
    				<td width="95" height="21" class='titulos2'>
    					Versi&oacute;n
    				</td>
    				<td>
    					<select name="version" id="version" class="select" onChange="selectVersion();">
    							<option value="">--Seleccione--</option>
    						<?php 
    						$sqlVer = "SELECT [ID], [VERSION], [NOMBRE], [OBSERVACION], [FECHA_INICIO], [FECHA_FIN]
                                            FROM [VERSIONES]";
                            $rsVer = $db->conn->Execute($sqlVer);
                            if ($rsVer) {
                                while (!$rsVer->EOF) {
                                    $idver = $rsVer->fields["ID"];
                                    $versi = $rsVer->fields["VERSION"];
                                    $nombre = $rsVer->fields["NOMBRE"];
                                    
                                    $selected = "";
                                    if ($idver == $version) {
                                        $selected = " selected ";
                                    }
                                    ?>
                                	<option value="<?=$idver?>" <?=$selected?>><?=$versi . " - " . $nombre?></option>
                                <?php 
                                $rsVer->MoveNext();
                                }
                            }                           
                            ?>
                    	</select>
    				</td>
    			</tr>
    			
				<tr>
					<td class="titulos2"> C&oacute;digo: </td>
					<td> 
						<input type="text" readonly="readonly" name="codigo" maxlength="5" value="<?php echo $cod; ?>" size="5"> 
					</td>
					
					<td valign="middle" align="right" colspan="6"> 
						<input class="botones" type="Submit" name="Buscar" value="Buscar">
					</td>
				</tr>
				
				<tr>
					<input type="hidden" name="val" value='vacio'>
					
					<td width="20%" class="titulos2" colspan="2"> Tipo Documental: </td>
					<td width="80%" colspan="6"> 
						<input class="tex_area" type="text" name="tipDoc" maxlength="100" value="<?php echo $tipDoc; ?>" size="75"> 
					</td>
				</tr>				
				
				<tr>
					<td width="5%" class="titulos2"> Notifica: </td>
					<td width="5%" align="center">
						<input class="checkbox" type="checkbox" name="notifica" value="notifica" <?php if ($notifica) echo "checked"; else echo ""; ?>>
					</td>

					<td width="5%" class="titulos2"> Termino: </td>
					<td width="5%" align="center"> 
						<input class="tex_area" type="text" name="termino" maxlength="2" value="<?php echo $termino; ?>" size="2">
					</td>

					<td width="10%" class="titulos2"> Inicio de alerta: </td>
					<td width="5%" align="center">
						<input class="tex_area" type="text" name="alerta" maxlength="2" value="<?php echo $alerta; ?>" size="2">
					</td>

					<td width="5%" class="titulos2"> Reporte: </td>
					<td width="5%" align="center">
						<input class="checkbox" type="checkbox" name="reporte" value="reporte" <?php if ($reporte) echo "checked"; else echo ""; ?>>
					</td>
				</tr>
				<tr> <td colspan="20"> <hr> </td> </tr>
				
				<tr>
					<td width="10%" class="titulos2" colspan="8"> Este Tipo Documental se puede asignar en radicados de: </td>
				</tr>
				
				<tr>
					<td width="30%" colspan="3" align="left">
						<input class="checkbox" type="checkbox" name="salida" value="salida" <?php if ($salida) echo "checked"; else echo ""; ?>>  &nbsp;
						1. Salida <br>	
						<input class="checkbox" type="checkbox" name="entrada" value="entrada" <?php if ($entrada) echo "checked"; else echo ""; ?>>  &nbsp;
						2. Entrada <br>
						<input class="checkbox" type="checkbox" name="memorando" value="memorando" <?php if ($memorando) echo "checked"; else echo ""; ?>>  &nbsp;
						3. Memorando 
					</td>
					
					<td width="30%" colspan="3" align="left">
						<input class="checkbox" type="checkbox" name="cirExt" value="cirExt" <?php if ($cirExt) echo "checked"; else echo ""; ?>>  &nbsp;
						4. Circular Ext. <br>
						<input class="checkbox" type="checkbox" name="resolucion" value="resolucion" <?php if ($resolucion) echo "checked"; else echo ""; ?>>  &nbsp;
						5. Resoluci&oacute;n <br>
						<input class="checkbox" type="checkbox" name="concepto" value="concepto" <?php if ($concepto) echo "checked"; else echo ""; ?>>  &nbsp;
						6. Concepto
					</td>
					
					<td width="30%" colspan="2" align="left">
						<input class="checkbox" type="checkbox" name="edicto" value="edicto" <?php if ($edicto) echo "checked"; else echo ""; ?>> &nbsp;
						7. Edicto <br>
						<input class="checkbox" type="checkbox" name="circular" value="circular" <?php if ($circular) echo "checked"; else echo ""; ?>>	&nbsp;
						8. Circular<br>
						<input class="checkbox" type="checkbox" name="auto" value="auto" <?php if ($auto) echo "checked"; else echo ""; ?>> &nbsp;
						9. Auto / Acto
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" colspan="8" align="center">
						<input class="botones" type="button" name="crear" value="Crear" onclick="valida();">
						&nbsp; &nbsp; &nbsp; &nbsp;
						<input class="botones" type="button" name="modificar" value="Modificar" onclick="modifica();">
						&nbsp; &nbsp; &nbsp; &nbsp;
						<input class="botones" type="button" name="accion" value="Limpiar" onclick="limpiar();">
					</td>
				</tr>
				
				<?php
				if ($msg){
					
				?>
				
				<tr>
					<td colspan="8" align="center">
						<font size='3'  color='#FF0000'> <?php echo $msg; ?> </font>
					</td>
				</tr>	
				
				<?php
				}
				?>
				
			</table>
			<br><br>
		</form>
		
		<?php		
		### SE INCLUYE ARCHIVO EN DONDE SE ARMAN LOS REPORTES EN EXCEL
		/*require ("../include/rs2xml.php");
		$rsExcel = $db->conn->Execute($sql);
		$obj = new rs2xml();
		
		$path = "../bodega/tmp/tipdoc".date("dmYh").time("his").".csv";

		$Rs2Xml = $obj->getXML($rsExcel);
		$arch = "tipDoc_".rand(10000, 20000);
		$ruta = "../bodega/tmp/$arch.xls";
		$fpDev = fopen($ruta, "w");
		if ($fpDev) {
			fwrite($fpDev, $Rs2Xml);
			fclose($fpDev);
		}
		*/
		?>
		
		
		<form method="post" action="admin_tipodoc.php" name="listado">
			<table width="70%" align="center" border="0" cellpadding="0" cellspacing="2" class="borde_tab">
				
				<tr>
					<td colspan="7" align="center"> Listado de Tipos Documentales  &nbsp;&nbsp;
						<?php //echo "&nbsp; <a href='$ruta' target='_blank'> Reporte </a>"; ?>
					</td>	
				</tr>
								
				<tr>
					<td class="titulos4" align="center" width="05%"> C&Oacute;DIGO </td>
					<td class="titulos4" align="center" width="45%"> TIPO DOCUMENTAL </td>
					<td class="titulos4" align="center" width="10%"> TERMINO </td>
					<td class="titulos4" align="center" width="10%"> GENERA ALERTA </td>
					<td class="titulos4" align="center" width="10%"> INICIO DE ALERTA </td>
					<td class="titulos4" align="center" width="10%"> REPORTE INTERNET </td>
				</tr>
		
				<?php
				while (!$rs->EOF) {	
					
				?>	
				
				<input type='hidden' name='id' value='<?php echo $rs->fields['ID']; ?>'>
				
				<tr>
					<td class="listado2_center" width="5%"> <?php echo $rs->fields['CODIGO']; ?> </td>
					<td class="listado2" width="45%">
						<a href="./admin_tipodoc.php?edit=<?=$rs->fields['ID']?>&login=<?=$login?>">
							<?php echo $rs->fields['DESCRIP']; ?>
						</a>
					</td>
					
					<td class="listado2_center" width="10%"> <?php echo $rs->fields['TERMINO']; ?> </td>
					<td class="listado2_center" width="10%"> <?php echo $rs->fields['ALERTA']; ?> </td>
					<td class="listado2_center" width="10%"> <?php echo $rs->fields['NOTIFICA']; ?> </td>	
					<td class="listado2_center" width="10%"> <?php echo $rs->fields['REPORT1']; ?> </td>
				</tr>
				
				<?php
					$rs->MoveNext();
				}
				?>
				
			</table>
			<br><br>
		</form>
	</body>
</html>
