<?php
	session_start();
	if (!isset($_SESSION['dependencia']))
		include "../rec_session.php";
?>
<html>
	<head>
		<link rel="stylesheet" href="../estilos/orfeo.css">
	</head>
	<body>
		<table class=borde_tab width='100%' cellspacing="5">
			<tr>
				<td class="titulos2"> <center> CARGUE DE No. DE GUIAS </center> </td>
			</tr>
		</table> <br/>
		<form name="cargar" action="cargar_csv_envio.php?<?=session_name()."=".session_id()?>" method="post" enctype="multipart/form-data">
		
			<table width="500" class="borde_tab" cellspacing="5" align="center">
				<tr>
					<td height="26" class="titulos2">Seleccione un archivo</td>
					<td valign="top" align="left" class="listado2">
						<input type="file" name="file" size="40">
					</td>
				</tr>
				<tr>
					<td height="26" colspan="2" valign="top" class='titulos2'> 
						<center>
							<input type="submit" name="cargar_archivo" Value="Cargar archivo" class="botones_largo">
						</center>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>