<html>
	<head>
		<title>Transaccion Realizada - Orfeo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../estilos/orfeo.css">
	</head>
	<body>
		<form action="{FORM_ACTION}" method="post" name="formulario">
			<input type="hidden" name="PHPSESSID" value="{PHPSESS_ID}" />
			<table border="0" cellspace="2" cellpad="2" WIDTH="50%"  class="t_bordeGris" id="tb_general" align="left">
				<tr>
					<td colspan="2" class="titulos4">Accion de Movimiento de Informados fue completados </td>
				</tr>
				<tr>
					<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">ACCION REQUERIDA :</td>
					<td  width="65%" height="25" class="listado2_no_identa">Movimiento a Subcarpeta Informados ({SUB_CARPETA}) </td>
				</tr>
				<tr>
					<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">INFORMADOS INVOLUCRADOS :</td>
					<td  width="65%" height="25" class="listado2_no_identa">{INFORMADOS}</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">USUARIO DESTINO :
				</td>
					<td  width="65%" height="25" class="listado2_no_identa">{USUA_LOGIN_DEST}</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">FECHA Y HORA :</td>
					<td  width="65%" height="25" class="listado2_no_identa">{FECHA_HORA}</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">USUARIO ORIGEN:</td>
					<td  width="65%" height="25" class="listado2_no_identa"> {USUA_LOGIN_DEST}	</td>
				</tr>
				<tr>
					<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">DEPENDENCIA ORIGEN:</td>
					<td  width="65%" height="25" class="listado2_no_identa">{DEPE_NOMB}</td>
				</tr>
			</table>
		</form>
	</body>
</html>
