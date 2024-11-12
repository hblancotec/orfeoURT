<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Tipificar Expediente</title>

<link rel="stylesheet" type="text/css" href="../estilos/orfeo.css" >
<link rel="stylesheet" type="text/css" href="../estilos/fonts-min.css" >
<link rel="stylesheet" type="text/css" href="../estilos/autocomplete.css" >
<link rel="stylesheet" type="text/css" href="../estilos/button.css" >
<link rel="stylesheet" type="text/css" href="../estilos/calendar.css" />
<link rel="stylesheet" type="text/css" href="../estilos/treeview.css" />

<!-- crearExpedientes.js = Maneja los eventos del autocomplete
	y demas eventos javascrip junto con las librerias de yui -->

<script type="text/javascript" src="./js/yahoo-dom-event.js"></script>
<script type="text/javascript" src="./js/dom-min.js"></script>
<script type="text/javascript" src="./js/event-min.js"></script>
<script type="text/javascript" src="./js/treeview-min.js"></script>
<script type="text/javascript" src="./js/connection-min.js"></script>
<script type="text/javascript" src="./js/animation-min.js"></script>
<script type="text/javascript" src="./js/datasource-min.js"></script>
<script type="text/javascript" src="./js/autocomplete-min.js"></script>
<script type="text/javascript" src="./js/calendar-min.js"></script>
<script type="text/javascript" src="./js/element-min.js"></script>
<script type="text/javascript" src="./js/button-min.js"></script>
<!-- INICIO archivo js para manejar los eventos de crearExpedientes -->
<script type="text/javascript" src="./js/crearExpedientes.js"></script>
<script type="text/javascript" src="./js/TaskNode.js"></script>
<!-- FIN archivo js para manejar los eventos de crearExpedientes -->



<!-- Se llama la siguiente funcion ubicada
	en el archivo crearExpedientes.js para pasar
	el valor de la dependencia del usuario, obtenida
	desde la session  -->

<script type="text/javascript">	
	function sid()				{ return "<!--{$sid}-->"; }
	function depe_script() 		{ return "<!--{$dependencia}-->"; }
	function usua_doc()			{ return "<!--{$usua_doc}-->"; }
	function codusua()			{ return "<!--{$codusua}-->"; }
	function nurad() 			{ return "<!--{$nurad}-->"; }
	function mosProy()			{ return "<!--{$mosProy}-->"; }
	function mosArbol()			{ return "<!--{$mosArbol}-->"; }
	function arrayArbol()		{ return <!--{$arrayArbol}-->; }
	function veractivos()		{ return "<!--{$veractivos}-->"; }
	function vaciarTipDocs() {
		var sel = document.getElementById("selectTipoDocumental");
		sel.options.length = 0;
		var option = document.createElement("option");
		option.text = "Seleccione un Tipo Documental";
		sel.add(option, sel[0]);
	}
</script>

</head>

<body  class=" yui-skin-sam">

<table  id="crearExpeUno" width="100%" align="center" margin="4">
	<tr>
		<td  class="titulos4" colspan="2" align="center" valign="middle">
			<b>Creaci&oacute;n de Expedientes</b>
		</td>
	</tr>
	<!--INICIO  Seleccion de la trd -->
	<form id="modiCreaExpe" method="POST" action="">
	<tr height="40px">
		<td align="left" width="30%">Serie:</td>
		<td>
			<select name="selectSerie" id="selectSerie" class="select_crearExp">
				<option value="0"> Seleccione una serie </option>
                <!--{foreach key=key item=item from=$serieArray}-->
                	<!--{if $serieActu eq $key}-->
                		<option selected value=<!--{$key}-->><!--{$item}--></option>
                	<!--{else}-->
                		<option value=<!--{$key}-->><!--{$item}--></option>
                	<!--{/if}-->
                <!--{/foreach}-->
            </select>
		</td>
	</tr>
	<tr height="40px">
		<td align="left" >SubSerie:</td>
		<td>
			<select name="selectSubSerie" id="selectSubSerie" class="select_crearExp">
				<option value="0"> Seleccione una subSerie </option>                            
            </select>
		</td>
	</tr>
	<!--INICIO  Seleccion de Tipo Documental-->
	<tr height="40px">
		<td >T. Documental:</td>
		<td>
			<select name="selectTipoDocumental" id="selectTipoDocumental" class="select_crearExp">
				<option value="0"> Seleccione un Tipo Documental</option>                            
            </select>
		</td>
	</tr>
	<!--FIN  Seleccion de Tipo Documental-->	
	<tr height="40px">
		<td align="left" >Responsable:</td>
		<td>
			<select name="selectUsuario" id="selectUsuario" class="select_crearExp">				
                <!--{foreach key=key item=item from=$usuaArray}-->
					<!--{if $usua_doc eq $key}-->
						<option selected value=<!--{$key}-->><!--{$item}--></option>
					<!--{else}-->
					<option value=<!--{$key}-->><!--{$item}--></option>
					<!--{/if}-->
                <!--{/foreach}-->
            </select>
		</td>
	</tr>
	<tr height="40px">
		<td align="left" >Fecha de Inicio:</td>
		<td>
			<input type="text" name="date1" id="date1" readonly="READONLY"/>
			<button id="show1up" type="button">Calendario</button>
			<div id="cal1Container"></div>			
		</td>
	</tr>
	<tr height="40px">
		<td   align="left">Nro. de Expediente:</td>
		<td valign="bottom">
			<input readonly="READONLY" maxlength="4" type="text"  class="numExpdR1" id="numExpd1" />
			<input readonly="READONLY" maxlength="3" type="text"  class="numExpdR2" id="numExpd2" />
			<input readonly="READONLY" maxlength="6" type="text"  class="numExpdR3" id="numExpd3" />
			<input readonly="READONLY" maxlength="5" type="text"  class="numExpdR4" id="numExpd4" />
			<input readonly="READONLY" maxlength="1" type="text"  class="numExpdR5" id="numExpd5" />
			<input valign="center" type="checkbox" id="modificarNum"/>						
			<br />
		</td>
	</tr>
	<tr height="40px">
		<td valign="center" align="left" >Privacidad:</td>
		<td>		
			<input id="publico_X" type="radio" name="radioPrivado1" value="Publico" checked="checked"/> P&uacute;blico 
		    <input id="privado_X"type="radio" name="radioPrivado1" value="Privado" /> Privado						
		</td>
	</tr>
	
	<!--{if $mosProy eq 'true'}-->
	<tr height="40px">
		<td  valign="top" align="left" >Nombre proyecto</td>
		<td>						 
            <select name="selectProyecto" id="selectProyecto" class="select_crearExp">
            	<option value="0" selected="selected"> Seleccione un proyecto</option>
                <!--{foreach key=key item=item from=$proyArray}--><option value=<!--{$key}-->><!--{$item}--></option>
                <!--{/foreach}-->
            </select>				
		</td>
	</tr>
	<!--{/if}-->
	
	<tr height="40px">
		<td   align="left" >Nombre del Expediente:</td>
		<td>
			<div id="searchNomAutoCom">
			    <textarea class="select_crearExp" name="nomb_Expe_300" id="myInput" rows="2" wrap="soft" class="tex_area2"></textarea>
			    <div id="myContainer"></div>
			</div>			
		</td>
	</tr>
	
	<!-- INICIO Arbol de radicados para anexar al expediente class="yui-hidden2"  -->
	
	<!--{if $mosArbol eq 'true'}-->						
	<tr height="40px">
		<td colspan="2" align="left">
			<div id="arbol2">
				<a id="expand" href="#">Expandir</a>
				<a id="collapse" href="#">Compactar</a>
				<a id="check" href="#">Seleccionar</a>
				<a id="uncheck" href="#">Desseleccionar</a>
			</div>
			<div id="treeDiv1"></div>
		</td>
	</tr>
	<!--{/if}-->				
	<!-- FIN Arbol de radicados para anexar al expediente -->
   </form>
   <!--INICIO CONFIRMAR -->
   <tr height="40px">
   		<td colspan="2" align="center">								
			<button type="button" id="javaScripConfirmar1"> CrearExpediente </button>							    
		</td>
   </tr>			
   <!--FIN CONFIRMAR -->
</table>

<!--FIN Seleccion de la trd -->

<!--INICIO Respuesta -->
<table id="respuesta"  class="yui-hidden2"  width="100%" align="center" margin="4">
	<tr>
		<td  class="titulos4" colspan="2" align="center" valign="middle">
			<center><b>Se creo el Expedientes con los siguientes datos<b></center>
		</td>
	</tr>
	<tr bordercolor="white" height="40px">
		<td  valign="center" width="40%" align="left">
			<b>Numero del Expediente:</b><br/>
		</td>
		<td>
			<div id="expedi_NumId"></div>
		</td>
	</tr>
	<tr height="40px">
		<td valign="center" align="left">
			<b>Serie:</b><br/>
		</td>
		<td>
			<div id="serie_NumId"></div>
		</td>
	</tr>
	<tr height="40px">
		<td valign="center" align="left">
			<b>SubSerie:</b><br/>
		</td>
		<td>
			<div id="subSerie_NumId"></div>
		</td>
	</tr>
	<!--{if $mosProy eq 'true'}-->
	<tr height="40px">
		<td  valign="center" align="left">
			<b>Proyecto:</b><br/>
		</td>
		<td>
			<div id="nombre_Proyecto"></div>
		</td>
	</tr>	
	<!--{/if}-->
	<tr height="40px">
		<td valign="center" align="left">
			<b>Nombre del expediente:</b><br/>
		</td>
		<td>
			<div id="nombre_Expediente"></div>
		</td>
	</tr>
	<tr height="40px">
		<td valign="center" align="left">
			<b>Permisos:</b><br/>
		</td>
		<td>
			<div id="nombre_Permisos"></div>
		</td>
	</tr>
	<tr height="40px">
		<td colspan="2" valign="bottom" align="center">
			<button type="button" id="respCerrar"> Cerrar </button>
        </td>
	</tr>
</table>
<input type="hidden" id="docuActu" name="docuActu" value="<!--{$docuActu}-->" />
<input type="hidden" id="subSerActu" name="subSerActu" value="<!--{$subSerActu}-->" />
<input type="hidden" id="serieActu" name="serieActu" value="<!--{$serieActu}-->" />
<input type="hidden" id="depecodi" name="depecodi" value="<!--{$depecodi}-->" />
<input type="hidden" id="codusua" name="codusua" value="<!--{$codusua}-->" />
<input type="hidden" id="cambio" name="cambio" value="<!--{$cambio}-->" />
<input type="hidden" id="retipifica" name="retipifica" value="<!--{$retipifica}-->" />
<input type="hidden" id="usuaModifica1" name="usuaModifica1" value="<!--{$usuaModifica1}-->" />
<input type="hidden" id="usuaModifica2" name="usuaModifica2" value="<!--{$usuaModifica2}-->" />
<input type="hidden" id="numrad" name="numrad" value="<!--{$nurad}-->" />
<input type="hidden" id="pqr" name="pqr" value="<!--{$pqr}-->" />

<!--FIN Respuesta -->

</body>
</html>