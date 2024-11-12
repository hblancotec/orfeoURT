<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Incluir Radicado en expediente</title>

<link rel="stylesheet" type="text/css" href="../estilos/orfeo.css" >
<link rel="stylesheet" type="text/css" href="../estilos/fonts-min.css" >
<link rel="stylesheet" type="text/css" href="../estilos/autocomplete.css" >
<link rel="stylesheet" type="text/css" href="../estilos/button.css" >
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
<script type="text/javascript" src="./js/element-min.js"></script>
<script type="text/javascript" src="./js/button-min.js"></script>
<script type="text/javascript" src="./js/TaskNode.js"></script>
<!-- INICIO archivo js para manejar los eventos de crearExpedientes -->
<script type="text/javascript" src="./js/incluirEnExpediente.js"></script>

<!-- FIN archivo js para manejar los eventos de crearExpedientes -->



<!-- Se llama la siguiente funcion ubicada
	en el archivo crearExpedientes.js para pasar
	el valor de la dependencia del usuario, obtenida
	desde la session  -->

<script type="text/javascript">	
	function sid()				{ return "<!--{$sid}-->"; }	
	function numRad()			{ return "<!--{$numRad}-->"; }	
	function veractivos()		{ return "<!--{$veractivos}-->"; }
	function depecodi()			{ return "<!--{$depecodi}-->"; }
	function usuadoc()			{ return "<!--{$usua_doc}-->"; }
	function codusua()			{ return "<!--{$codusua}-->"; }
	function mosArbol()			{ return "<!--{$mosArbol}-->"; }
	function arrayArbol()		{ return <!--{$arrayArbol}-->; }
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
<input type='hidden' id='veractivos' name='veractivos' value='<!--{$veractivos}-->' />
<table  id="crearExpeUno" width="95%" align="center" margin="4">
	
	
	<tr>
		<td  class="titulos4" colspan="3" align="center" valign="middle">
			<b>Incluir radicado en expediente</b>
		</td>
	</tr>
	
	<!-- INICIO dependencia-->
	<tr height="40px">
		<td width="15%">* Dependencia: </td>
		<td width="85%" colspan="2">
			<select name="nom_depe" id="nom_depe" class="select_crearExp">								
                <!--{foreach key=key item=item from = $depeArray}-->
					<!--{if $depecodi eq $key}-->
						<option selected value=<!--{$key}-->><!--{$item}--></option>
					<!--{else}-->
					<option value=<!--{$key}-->><!--{$item}--></option>
					<!--{/if}-->
                <!--{/foreach}-->
            </select>
		</td>
	</tr>
	<!-- FIN dependencia-->
	
	
	<!-- INICIO Fecha para filtra la busqueda ano-->					
	<tr height="40px">
		<td>* A&ntilde;o: </td>
		<td colspan="2">													
				<select name="ano_busq" id="ano_busq" class="select_crearExp">								
	                <!--{foreach key=key item=item from = $anoArray}-->										
							<option value=<!--{$key}-->><!--{$item}--></option>										
	                <!--{/foreach}-->
					<option value="0"> Todos los a&ntilde;os</option>
	            </select>
		</td>
	</tr>					
	<!-- FIN Fecha para filtra la busqueda ano-->
	
	
	<!--INICIO  Seleccion de Serie-->	
	<tr height="40px">
		<td>Serie:</td>
		<td colspan="2">
			<select name="selectSerie" id="selectSerie" class="select_crearExp">
				<option value="0" selected="selected"> Seleccione una serie </option>
                <!--{foreach key=key item=item from=$serieArray}-->
                	<!--{if $serie eq $key}-->
                		<option selected value=<!--{$key}-->><!--{$item}--></option>
                	<!--{else}-->
                		<option value=<!--{$key}-->><!--{$item}--></option>
                	<!--{/if}-->
                <!--{/foreach}-->
            </select>
		</td>
	</tr>
	<!--FIN  Seleccion de Serie-->	
	
	
	<!--INICIO  Seleccion de SubSerie-->
	<tr height="40px">
		<td >SubSerie:</td>
		<td colspan="2">
			<select name="selectSubSerie" id="selectSubSerie" class="select_crearExp">
				<option value="0" selected="selected"> Seleccione una subSerie </option>                            
            </select>
		</td>
	</tr>
	<!--FIN  Seleccion de SubSerie-->

	<!--INICIO  Seleccion de Tipo Documental-->
	<tr height="40px">
		<td >T. Documental:</td>
		<td colspan="2">
			<select name="selectTipoDocumental" id="selectTipoDocumental" class="select_crearExp" required>
				<option value="0" selected="selected"> Seleccione un Tipo Documental</option>                            
            </select>
		</td>
	</tr>
	<!--FIN  Seleccion de Tipo Documental-->

	<!--INICIO  Folios IBISCOM 2018-10-25-->
	
	<tr height="40px" style="display:<!--{$oculta}-->">
		<td >* Folios:</td>
		<td>
			<input  type="number" min="0" max="999" name="folios" class="select_crearExp"	id="folios" <!--{$hayMetadatos}--> />
		</td>
	</tr>
	<!--FIN  Folios IBISCOM 2018-10-25-->
	<!--INICIO  Palabras clave IBISCOM 2018-10-25-->
	<tr height="40px" style="display:<!--{$oculta}-->">
		<td >Palabras clave:</td>
		<td>
			<input  type="text" name="palabrasClave" class="select_crearExp"	id="palabrasClave" <!--{$hayMetadatos}--> />
		</td>
	</tr>
	<!--FIN  Palabras clave IBISCOM 2018-10-25-->

		<!--INICIO  nombre proyector IBISCOM 2018-11-01-->
	<tr height="40px" style="display:<!--{$oculta}-->">
		<td >Nombre proyector:</td>
		<td>
			<input  type="text" name="nombreProyector" class="select_crearExp"	id="nombreProyector" <!--{$hayMetadatos}--> />
		</td>
	</tr>
	<!--FIN  nombre proyector IBISCOM 2018-11-01-->
	
		<!--INICIO  nombre revisor IBISCOM 2018-11-01-->
	<tr height="40px" style="display:<!--{$oculta}-->">
		<td >Nombre revisor:</td>
		<td>
			<input  type="text" name="nombreRevisor" class="select_crearExp"	id="nombreRevisor" <!--{$hayMetadatos}--> />
		</td>
	</tr>
	<!--FIN  nombre revisor IBISCOM 2018-11-01-->
	
	<!-- INICIO Buscar Expediente -->					
	<tr height="40px">
        <td>* Expediente</td>
		<td colspan="2">							
			<div class="searchNomAutoCom">								
				<input  type="text" name="nomb_Expe_search" class="select_crearExp"	id="nomb_Expe_search"/>
			    <div id="contnExpSearch"></div>
			</div>
			<div class="inpuNoExp"><button type="button" name="buscNomExp" id="buscNomExp"> Buscar </button></div>																					                    
		</td>
    </tr>					
	<!-- FIN Buscar Expediente -->
	
	<!-- INICIO Nombre Actual del expediente -->
	<tr height="40px">
        <td valign="top">Nombre del Expediente:</td>
		<td valign="center" width="100%" colspan="2">
			<textarea readonly="READONLY"  class="select_crearExp nombActuExp" name="nombActuExp" id="nombActuExp" rows="4" wrap="soft" class="tex_area2"></textarea>																					                    
		</td>
    </tr>
	<!-- FIN Nombre Actual del expediente -->

	<!-- INICIO Arbol de radicados para anexar al expediente class="yui-hidden2"  -->
	
	<!--{if $mosArbol eq 'true'}-->						
	<tr>
		<td colspan="2" align="left" width="55%">
			<div id="arbol5">
				<a id="expand" href="#">Expandir</a>
				<a id="collapse" href="#">Compactar</a>
				<a id="check" href="#">Seleccionar</a>
				<a id="uncheck" href="#">Desseleccionar</a>
			</div>
			<div id="treeDiv1"></div>
		</td>
	    <td width="45%">
			T. Documental:&nbsp;
			<select name="selectTipoDocumental2" id="selectTipoDocumental2" class="select_crearExp">
				<option value="0" selected="selected"> Seleccione un Tipo Documental</option>                            
            </select> 
            <!--<button class="botones" type="button" id="clasificaSalida"> Guardar </button>-->	         
		</td>
	</tr>
	<!--{/if}-->				
	<!-- FIN Arbol de radicados para anexar al expediente -->


	<!--INICIO Input nombre Nuevo-->
	<tr height="40px">		                
		<td colspan="2" valign="center" align="center">
			<button class="botones" type="button" id="incluirRadExp"> Incluir </button>	
			&nbsp;											                    
			<button class="botones" type="button" id="cerrarVentana"> Cerrar </button>
		</td>
    </tr>	
	<!--FIN Input nombre Nuevo-->
	
</table>

<input type="hidden" id="ocultaDocElectronico" name="ocultaDocElectronico" value="<!--{$oculta}-->" />
<input type="hidden" id="tdoc" name="tdoc" value="<!--{$tdoc}-->" />
<input type="hidden" id="radSerie" name="radSerie" value="<!--{$serie}-->" />
<input type="hidden" id="radPqr" name="radPqr" value="<!--{$pqr}-->" />

<input type="hidden" id="docuActu" name="docuActu" value="<!--{$tdoc}-->" />
<input type="hidden" id="subSerActu" name="subSerActu" value="<!--{$subSerie}-->" />
<input type="hidden" id="serieActu" name="serieActu" value="<!--{$serie}-->" />
<input type="hidden" id="depecodi" name="depecodi" value="<!--{$depecodi}-->" />
<input type="hidden" id="codusua" name="codusua" value="<!--{$codusua}-->" />
<input type="hidden" id="cambio" name="cambio" value="<!--{$cambio}-->" />
<input type="hidden" id="retipifica" name="retipifica" value="<!--{$retipifica}-->" />
<input type="hidden" id="usuaModifica1" name="usuaModifica1" value="<!--{$usuaModifica1}-->" />
<input type="hidden" id="usuaModifica2" name="usuaModifica2" value="<!--{$usuaModifica2}-->" />
<input type="hidden" id="numrad" name="numrad" value="<!--{$numRad}-->" />
</body>
</html>