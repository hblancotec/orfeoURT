<?php
session_start();
$ruta_raiz = "..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
//require "$ruta_raiz/linkArchivo.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug=true;
$docCons = $_POST['busca'];
$hoy = date("Y/m/d");

if ($docCons) {
	$sql = "SELECT	D.SGD_DIR_NOMREMDES AS DESTINATARIO,
					D.SGD_DIR_DOC AS IDENTIFICACION,
					R.RADI_NOTIF_FIJACION AS FECHA_FIJACION,
					R.RADI_NOTIF_DESFIJACION AS FECHA_DESFIJACION,
					R.RADI_NUME_RADI AS RADICADO,
					A.ANEX_CODIGO
			FROM	RADICADO R
					INNER JOIN SGD_DIR_DRECCIONES D ON
						D.RADI_NUME_RADI = R.RADI_NUME_RADI AND
						D.SGD_DIR_DOC = '".$docCons."' AND
						D.SGD_DIR_TIPO = 1
					LEFT JOIN ANEXOS A ON
						A.ANEX_RADI_NUME = R.RADI_NUME_RADI AND
						A.ANEX_BORRADO = 'N' AND
						A.SGD_TPR_CODIGO = 324 AND
						A.ANEX_SALIDA = 0
			WHERE	R.RADI_NOTIFICADO = 1 ";
}
else{
	$sql = "SELECT	D.SGD_DIR_NOMREMDES AS DESTINATARIO,
					D.SGD_DIR_DOC AS IDENTIFICACION,
					R.RADI_NOTIF_FIJACION AS FECHA_FIJACION,
					R.RADI_NOTIF_DESFIJACION AS FECHA_DESFIJACION,
					R.RADI_NUME_RADI AS RADICADO,
					A.ANEX_CODIGO
			FROM	RADICADO R
					INNER JOIN SGD_DIR_DRECCIONES D ON
						D.RADI_NUME_RADI = R.RADI_NUME_RADI AND
						D.SGD_DIR_TIPO = 1
					LEFT JOIN ANEXOS A ON
						A.ANEX_RADI_NUME = R.RADI_NUME_RADI AND
						A.ANEX_BORRADO = 'N' AND
						A.SGD_TPR_CODIGO = 324 AND
						A.ANEX_SALIDA = 0
			WHERE	R.RADI_NOTIFICADO = 1 AND
					R.RADI_NOTIF_FIJACION <= '".$hoy."' AND
					R.RADI_NOTIF_DESFIJACION >= '".$hoy."'
			ORDER BY FECHA_FIJACION, FECHA_DESFIJACION, RADICADO";
}
$rs = $db->conn->Execute($sql);
?>

<html>
	<head>
		<title> Reporte de Notificaciones Administrativas </title>
		<meta http-equiv="Content-Style-Type" content="text/css">
		<style type="text/css"> 
			<!-- 
			.titulo {
				color: white;
				text-align: center;
				background-color: #AF272F;
				font-size: 14;
				font-family: tahoma, arial, verdana
			}
			.registro {
				color: black;
				text-align: center;
				font-size: 12;
				font-family: tahoma, arial, verdana
			}
			-->
		</style>
		<script language="javascript">
			function funlinkArchivo(numrad){
				nombreventana="linkVistArch";
				url = "../linkArchivo.php?"+"&numrad="+numrad;
				ventana = window.open(url,nombreventana,'height=50,width=250');
				return;
			}
		</script>
	</head>
	<body>
		<br><br>
		<form action="reporte.php" name=formulario method="post">
			<table width="40%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="55%">
						<FIELDSET>
							<LEGEND style="color: #AF272F;"> Consulte su c&eacute;dula </LEGEND>
							<input type="text" name="busca" size="20">
							<input class="botones" type="submit" name="consultar" value="Consultar" >
						</FIELDSET>
					</td>
					<td width="45%" valign="bottom">
						Consulte aqu&iacute; su Notificaci&oacute;n a trav&eacute;s del n&uacute;mero de su documento de identidad
					</td>
				</tr>
			</table>
		</form>
		<br><br><br><br>
		<table width="100%" align="center" border="1" cellspacing="1" cellpadding="1" style="border-collapse:collapse;">
			<tr class="titulo">
				<td class="titulo">
					Nombre
				</td>
				<td class="titulo">
					No. Documento
				</td>
				<td class="titulo">
					Fecha Fijaci&oacute;n
				</td>
				<td class="titulo">
					Fecha Desfijaci&oacute;n
				</td>
				<td class="titulo">
					No. Radicado
				</td>
				<td class="titulo">
					Notificaci&oacute;n
				</td>
			</tr>
			<?PHP
			while ($rs && !$rs->EOF) {
				$nombre = $rs->fields['DESTINATARIO'];
				$cedula = $rs->fields['IDENTIFICACION'];
				$fijacion = substr($rs->fields['FECHA_FIJACION'], 0, 10);
				$desfijacion = substr($rs->fields['FECHA_DESFIJACION'], 0, 10);
				$radicado = $rs->fields['RADICADO'];
				$codAnex = $rs->fields['ANEX_CODIGO'];
			?>
				<tr>
					<td class="registro" style="text-align: left;">
						<?php echo $nombre; ?>
					</td>
					<td class="registro">
						<?php echo $cedula; ?>
					</td>
					<td class="registro">
						<?php echo $fijacion; ?>
					</td>
					<td class="registro">
						<?php echo $desfijacion; ?>
					</td>
					<td class="registro">
						<span class="select">
							<a href=# onclick="funlinkArchivo('<?php echo $radicado ?>');">
								<?php echo $radicado; ?>
							</a>
						</span>
					</td>
					<td class="registro">
						<?php
						if ($codAnex) {
						?>
						
							<a href=# onclick="funlinkArchivo('<?php echo $codAnex ?>');">
								<img src="../imagenes/pdf.png" alt="" />
							</a>
						
						<?php
						}
						?>
					</td>
				</tr>
				<?php
				$rs->MoveNext();
			}
			?>
		</table>
	</body>
</html>