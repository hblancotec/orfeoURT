<?php
    session_start();
    if (count($_SESSION) == 0) {
        die(include "../sinacceso.php");
        exit();
    } else {
        extract($_GET);
        extract($_POST);
        extract($_SESSION);
    }
	$ruta_raiz = "..";

	if (!$nurad) $nurad= $rad;
	if($nurad) {
		$ent = substr($nurad,-1);
	}
    include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
    //$db->conn->debug = true;
	include_once "$ruta_raiz/include/tx/Historico.php";
	include_once ("$ruta_raiz/class_control/TipoDocumental.php");
	include_once "$ruta_raiz/include/tx/Expediente.php";
	$trd = new TipoDocumental($db);
	$encabezadol  = $_SERVER['PHP_SELF']."?".session_name()."=".session_id();
    $encabezadol .= "&opcionExp=$opcionExp&numeroExpediente=$numeroExpediente";
    $encabezadol .= "&dependencia=$dependencia&krd=$krd&nurad=$nurad";
    $encabezadol .= "&coddepe=$coddepe&codusua=$codusua&depende=$depende";
    $encabezadol .= "&ent=$ent&tdoc=$tdoc&codiTRDModi=$codiTRDModi";
    $encabezadol .= "&codiTRDEli=$codiTRDEli&codserie=$codserie&tsub=$tsub&ind_ProcAnex=$ind_ProcAnex";
?>

<html>
	<head>
		<title>Seguridad del Radicado</title>
		<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
		<script>
			function regresar(){
				document.TipoDocu.submit();
			}
		</script>
		<style type="text/css">
			<!--
			.style1 {font-size: 14px}
			-->
		</style>
	</head>
	<body bgcolor="#FFFFFF">
		<form method="post" action="radicado.php?krd=<?=$krd?>&numRad=<?=$numRad?>" name="TipoDocu">
			<table border="0" width="80%" align="center" class="borde_tab" cellspacing="0">
				<tr align="center" class="titulos2">
					<td height="15" class="titulos2">
						NIVEL DE SEGURIDAD DEL RADICADO No. <?=$numRad?>
					</td>
				</tr>
			</table>
			<br>
			<table width="80%" border="0" cellspacing="1" cellpadding="5" align="center" class="borde_tab">
				<tr>
					<td width="40%" class="titulos2" >Nivel</td>
					<td width="60%" class="listado2_center">
						<select name=nivelRad class=select>
							<?php
								if ($nivelRad == 0) {
									$datoss = " selected "; 
								}	
								else {
									$datoss = "";
								}
							?>
							
							<option value=0 <?=$datoss?>>P&uacute;blico</option>
							
							<?php
								if ($nivelRad == 1){
									$datoss = " selected "; 
								}
								else {
									$datoss = "";
								}
							?>
								
								<option value=1 <?=$datoss?>>Privado (Dependencia)</option>
							
							<?php
								if ($nivelRad == 2) {
									$datoss = " selected "; 
								}
								else {
									$datoss = "";
								}
							?>
								
								<option value=2 <?=$datoss?>>Privado (Usuario)</option>	
						</select>
					</td>
				</tr>

				<tr>
					<td class="listado2" colspan="2" >
					Nota: Por favor tenga en cuenta las siguientes indicaciones:
					<br/><br/>
					1. Si selecciona Privado (Dependencia), el Radicado solo podr&aacute; ser consultado por los usuarios que lo tengan
					en su poder y los usuarios que pertencen a la Dependencia que privatizo el radicado.
					<br/><br/>
					2. Si selecciona Privado (Usuario), el Radicado solo podr&aacute; ser consultado por los usuarios que lo tengan
					en su poder y el usuario que privatizo el radicado.
					</td>
				</tr>

				<tr>
					<td class=listado5  align="center" COLSPAN=2>
						<center>
							<input type="submit" class="botones" name=grbNivel value="Actualizar">
							&nbsp;&nbsp;
							<input type="button" class="botones" name="Cerrar" value="Cerrar" id="envia22" onClick="opener.regresar();window.close();">
						</center>
					</td>
				</tr>
			</table>
		</form>
		
		<?php
		if($grbNivel and $numRad) {
			if($nivelRad == 1){
				$query = "	UPDATE	RADICADO
							SET		SGD_SPUB_CODIGO = 1,
									RADI_USUA_PRIVADO = '".$_SESSION['usua_doc']."'
							WHERE	RADI_NUME_RADI = ".$numRad;
				$observa = "Se actualizo el estado del Radicado a Confidencial (Dependencia)";
			}
			else if ($nivelRad == 2){
				$query = "	UPDATE	RADICADO
							SET		SGD_SPUB_CODIGO = 2,
									RADI_USUA_PRIVADO = '".$_SESSION['usua_doc']."'
							WHERE	RADI_NUME_RADI = ".$numRad;
				$observa = "Se actualizo el estado del Radicado a Confidencial (Usuario)";
			}
			else {
				$query = "	UPDATE	RADICADO 
							SET		SGD_SPUB_CODIGO = 0 
							WHERE	RADI_NUME_RADI = $numRad";
				$observa = "Se actualizo el estado del Radicado a Publico.";
			}
			
			if($db->conn->Execute($query)) {
				echo "<span class=leidos>El nivel de seguridad se actualiz&oacute; correctamente.";
				include_once "$ruta_raiz/include/tx/Historico.php";
				$codiRegH = "";
				$Historico = new Historico($db);
				$codiRegE[0] = $numRad;
				$radiModi = $Historico->insertarHistorico($codiRegE, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 54);
			}
			else {
				echo "<span class=titulosError> !No se pudo actualizar el nivel de seguridad!";
			}
		}
		?>
		<?=$mensaje_err?>
		</span>
	</body>
</html>
