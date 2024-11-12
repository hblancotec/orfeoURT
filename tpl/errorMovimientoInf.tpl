<html>
	<head>
		<title>Transaccion Realizada - Orfeo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../estilos/orfeo.css">
	</head>
	<body>
		<form action="{FORM_ACTION}" method="post" name="formulario">
			<input type="hidden" name="PHPSESSID" value="{PHPSESS_ID}" />
			<table border="0" cellspace="2" cellpad="2" width="50%"  class="t_bordeGris" id="tb_general" align="left">
				<tr>
					<td colspan="2" class="titulos4">Error:: {ERROR_MSG}</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">TIPO ERROR :&nbsp;</td>
					<td  width="65%" height="25" class="listado2_no_identa">{ERROR_MSG_DESC}</td>
				</tr>
			</table>
		</form>
	</body>
</html>
