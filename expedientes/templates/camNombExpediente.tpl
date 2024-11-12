<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Incluir Radicado en expediente</title>

<link rel="stylesheet" type="text/css" href="../estilos/orfeo.css" >
<link rel="stylesheet" type="text/css" href="../estilos/fonts-min.css" >
<link rel="stylesheet" type="text/css" href="../estilos/autocomplete.css" >
<link rel="stylesheet" type="text/css" href="../estilos/button.css" >

<!-- crearExpedientes.js = Maneja los eventos del autocomplete
	y demas eventos javascrip junto con las librerias de yui -->

<script type="text/javascript" src="./js/yahoo-dom-event.js"></script>
<script type="text/javascript" src="./js/dom-min.js"></script>
<script type="text/javascript" src="./js/event-min.js"></script>
<script type="text/javascript" src="./js/connection-min.js"></script>
<script type="text/javascript" src="./js/animation-min.js"></script>
<script type="text/javascript" src="./js/datasource-min.js"></script>
<script type="text/javascript" src="./js/autocomplete-min.js"></script>
<script type="text/javascript" src="./js/element-min.js"></script>
<script type="text/javascript" src="./js/button-min.js"></script>
<!-- INICIO archivo js para manejar los eventos de crearExpedientes -->
<script type="text/javascript" src="./js/camNombExpediente.js"></script>

<!-- FIN archivo js para manejar los eventos de crearExpedientes -->



<!-- Se llama la siguiente funcion ubicada
	en el archivo camNombExpediente.js para pasar
	el valor de la session  -->

<script type="text/javascript">	
	function sid()				{ return "<!--{$sid}-->"; }
	function depecodi()			{ return "<!--{$depecodi}-->"; }
	function usua_doc()			{ return "<!--{$usua_doc}-->"; }
	function codusua()			{ return "<!--{$codusua}-->"; }
	function numExp()			{ return "<!--{$numExp}-->"; }	
</script>

</head>

<body  class=" yui-skin-sam">

	<table  id="crearExpeUno" width="95%" align="center" margin="4">		
		
		<tr>
			<td  class="titulos4" colspan="2" align="center" valign="middle">
				<b>Datos Actuales</b>
			</td>
		</tr>	
		<tr height="20px"><td colspan="2"></td></tr>
		<tr height="40px">
			<td  valign="top" width="25%" align="left">
				Numero del Expediente:
			</td>
			<td valign="top">				  
				<!--{$numExp}-->
			</td>
		</tr>
		<tr valign="top" height="40px">
			<td valign="top" align="left">
				Serie:
			</td>
			<td>
				<!--{$serie}-->
			</td>
		</tr>
		<tr height="40px">
			<td valign="top" align="left">
				SubSerie:
			</td>
			<td valign="top">
				<!--{$subSerie}-->
			</td>
		</tr>
		
		<tr height="40px">
			<td valign="top" align="left">
				Nombre del expediente:
			</td>
			<td valign="top">
				<!--{$nombreExp}-->
			</td>
		</tr>
		
		<tr>
			<td  class="titulos4" colspan="2" align="center" valign="middle">
				<b>Modificar Nombre de Expediente</b>
			</td>
		</tr>		
	
		<!--INICIO Input nombre Nuevo-->
		<tr height="40px">
			<td  valign="top" align="left" >Nombre del Expediente:</td>
			<td valign="top">
				<div id="searchNomAutoCom">
				    <textarea class="select_crearExp" name="nomb_Expe_300" id="myInput" rows="2" wrap="soft" class="tex_area2"></textarea>
				    <div id="myContainer"></div>
				</div>			
			</td>
		</tr>
		<!--FIN Input nombre Nuevo-->
		
		<!--INICIO Input nombre Nuevo-->
		<tr height="50px">		                
			<td colspan="2" valign="center" align="center">
				<button class="botones" type="button" id="cambNomExp"> Cambiar </button>												                    
			</td>
	    </tr>	
		<!--FIN Input nombre Nuevo-->	
		
	</table>

</body>
</html>