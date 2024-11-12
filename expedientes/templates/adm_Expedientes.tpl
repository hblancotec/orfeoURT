<html>
<head>
<title>Administraci&oacute;n de expedientes</title>
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Last-Modified" content="0">
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<meta http-equiv="Pragma" content="no-cache">

    <link rel="stylesheet" type="text/css" href="../estilos/orfeo.css">
    <link rel="stylesheet" type="text/css" href="../estilos/fonts-min.css">
    <link rel="stylesheet" type="text/css" href="../estilos/autocomplete.css">
    <link rel="stylesheet" type="text/css" href="../estilos/datatable.css">
    <link rel="stylesheet" type="text/css" href="../estilos/tabview.css">
    <link rel="stylesheet" type="text/css" href="../estilos/button.css">
	<link rel="stylesheet" type="text/css" href="../estilos/calendar.css" />
		
	<script type="text/javascript" src="./js/yahoo-dom-event.js"></script>
	<script type="text/javascript" src="./js/element-min.js"></script>
	
	<script type="text/javascript">
		function sid() 				{ return "<!--{$sid}-->"; }				
		function mosProy()			{ return "<!--{$mosProy}-->"; }
		function select_depen()		{ return "<!--{$select_depen}-->"; }
		function depecodi()			{ return "<!--{$depecodi}-->"; }
		function codusua()			{ return "<!--{$codusua}-->"; }							
		function usuadoc()			{ return "<!--{$usuadoc}-->"; }
		function veractivos()		{ return "<!--{$veractivos}-->"; }
		function vaciarTipDocs() {
			var sel = document.getElementById("selectTipoDocumental");
			sel.options.length=0;
			var option = document.createElement("option");
			option.text = "Seleccione un Tipo Documental";
			sel.add(option, sel[0]);
		}
	</script>	
	    
    <script type="text/javascript" src="./js/connection-min.js"></script>
    <script type="text/javascript" src="./js/animation-min.js"></script>
    <script type="text/javascript" src="./js/datasource-min.js"></script>
    <script type="text/javascript" src="./js/autocomplete-min.js"></script>	
    <script type="text/javascript" src="./js/datasource-min.js"></script>    
    <script type="text/javascript" src="./js/tabview-min.js"></script>
    <script type="text/javascript" src="./js/button-min.js"></script>
	<script type="text/javascript" src="./js/calendar-min.js"></script>
	<script type="text/javascript" src="./js/yahoo-min.js"></script>
	<script type="text/javascript" src="./js/event-min.js"></script>
	
    <!-- INICIO archivo js para manejar los eventos de adm_nombreTemasEXP -->
    <script type="text/javascript" src="./js/adm_Exp_Nombres.js"></script>	
    <!-- FIN archivo js para manejar los eventos de adm_nombreTemasEXP -->	
	
</head>
<body class=" yui-skin-sam" >
	<input type='hidden' id='veractivos' name='veractivos' value='<!--{$veractivos}-->' />
	<div id="tapsExp" class="yui-navset">
	    <ul class="yui-nav">
			
	        <li class="selected"><a href="#tab1"><em>Cambiar Nombres de Exp.</em></a></li>
			<!--{if $perm_temas_exp gt '0'}-->
		    <li><a href="#tab2"><em>Activar / Inactivar Exp.</em></a></li>
		    <li><a href="#tab3"><em>Excluir Radicados de Exp.</em></a></li>
			<li><a href="#tab4"><em>Crear Exp masiva.</em></a></li>
			<li><a href="#tab5"><em>Incluir Radicados en Expediente.</em></a></li>
			<!--{/if}-->
			<!--{if $perm_temas_exp gt '2'}-->
			<li><a href="#tab6"><em>Migrar Expedientes.</em></a></li>
			<!--{/if}-->
	    </ul>            
	    <div class="yui-content">
	    	
	    	<!--INICIO PRIMER TAB NOMBRES EXPEDIENTES-->
	        <div>
	        	<table width="100%">				
	        		<!-- INICIO dependencia-->
					<tr>
						<td width="18%" style="padding-left: 10px; padding-top: 10px;">Dependencia:</td>
						<td width="82%" style="padding-top: 10px;">
							<!--{if $perm_temas_exp gt '1'}-->							
								<select name="nom_depe" id="nom_depe" class="select_crearExp">				
					                <!--{foreach key=key item=item from = $depeArray}-->
										<!--{if $depe_cod eq $key}-->
											<option selected value=<!--{$key}-->><!--{$item}--></option>
										<!--{else}-->
										<option value=<!--{$key}-->><!--{$item}--></option>
										<!--{/if}-->
					                <!--{/foreach}-->
					            </select>
							<!--{else}-->									
								<b> <!--{$depeArray}--></b>							
							<!--{/if}-->
						</td>
					</tr>
					<!-- FIN dependencia-->					
					<!-- INICIO Fecha para filtra la busqueda ano-->					
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">A&ntilde;o:</td>
						<td style="padding-top: 10px;">													
								<select name="ano_busq" id="ano_busq" class="select_crearExp">		
					                <!--{foreach key=key item=item from = $anoArray}-->										
											<option value=<!--{$key}-->><!--{$item}--></option>										
					                <!--{/foreach}-->
									<option value="0"> Todos los a&ntilde;os</option>
					            </select>
						</td>
					</tr>					
					<!-- FIN Fecha para filtra la busqueda ano-->					
					<!-- INICIO Seleccionar serie-->
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">Serie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSerie" id="selectSerie" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una serie </option>
				                <!--{foreach key=key item=item from=$serieArray}--><option value=<!--{$key}-->><!--{$item}--></option>
				                <!--{/foreach}-->
				            </select>
						</td>
					</tr>							
					<!-- Fin Seleccionar serie-->					
					<!-- INICIO Subserie-->
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">SubSerie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSubSerie" id="selectSubSerie" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una subSerie </option>                            
				            </select>
						</td>
					</tr>
					<!-- FIN Subserie-->					
					<!-- INICIO Buscar Expediente -->					
					<tr>
		                <td height="40px" valign="center" style="padding-left: 10px; padding-top: 10px;">Expediente:</td>
						<td>							
							<div class="searchNomAutoCom">								
								<input  type="text" name="nomb_Expe_search" class="select_crearExp"	id="nomb_Expe_search"/>
							    <div id="contnExpSearch"></div>
							</div>
							<div class="inpuNoExp"><button type="button" name="buscNomExp" id="buscNomExp"> Buscar </button></div>																					                    
						</td>
		            </tr>					
					<!-- FIN Buscar Expediente -->					
					<!-- INICIO Nombre Actual del expediente -->
					<tr height="50px">
		                <td valign="top" style="padding-left: 10px;">Nombre Actual del Expediente:</td>
						<td valign="top" width="100%">
							<textarea readonly="READONLY"  class="select_crearExp nombActuExp" name="nombActuExp" id="nombActuExp" rows="4" wrap="soft" class="tex_area2"></textarea>																					                    
						</td>
		            </tr>
					<!-- FIN Nombre Actual del expediente -->					
					<!--INICIO Input nombre Nuevo-->
					<tr>
		                <td valign="top" style="padding-left: 10px;">Nuevo Nombre del expediente:</td>
						<td>
							<textarea class="select_crearExp" name="nomb_Expe_300" id="nomb_Expe_300" rows="2" wrap="soft" class="tex_area2"></textarea>												                    
						</td>
		            </tr>	
					<!--FIN Input nombre Nuevo-->					
					<!--INICIO Input nombre Nuevo-->
					<tr height="50px">		                
						<td colspan="2" valign="center" align="center">
							<button class="botones" type="button" id="cambNomExp"> Modificar </button>												                    
						</td>
		            </tr>	
					<!--FIN Input nombre Nuevo-->
	        	<!-- Proyectos -->	
				<!--{if $mosProy eq 'true'}-->
					<tr><td colspan="2"><br/><HR width=100% align="center"><br/><br/></td></tr>																				
		            <tr>
		                <td valign="top" style="padding-left: 10px;">                	                
		                    Nombre de proyecto  
		                </td>
						<td>
							<input id="nomb_proyecto1" class="campo_proye" name="nomb_proyecto1" type="text">
							<input id="nomb_proyecto2" class="campo_proye" name="nomb_proyecto2" type="text">
							<button class="botones" type="button" id="crear_proy"> Crear </button>                    
						</td>
		            </tr>
					<tr>
						<td valign="top" style="padding-left: 10px;">Nombre anterior</td>
						<td>
							<div id="AutoCompleProy">                        
								<select name="selectProyecto" id="selectProyecto" class="select_crearExp">
					            	<option value="0" selected="selected"> Seleccione un proyecto</option>
					                <!--{foreach key=key item=item from=$proyArray}--><option value=<!--{$key}-->><!--{$item}--></option>
									<!--{/foreach}-->
					            </select>						
								<button class="botones" type="button" id="borrar_proy"> Borrar </button>
								<button class="botones" type="button" id="modificar_proy"> Modificar </button>
		                        <div id="ContainProy">
		                        </div>
		                    </div>		
						</td>				
					</tr>
				<!--{/if}-->
				<!-- Fin Proyectos -->				
				</table>			
			</div>
			<!--FIN PRIMER TAB NOMBRES EXPEDIENTES-->
				
									
			<!--{if $perm_temas_exp gt '2'}-->			
			<!--INICIO SEGUNDO TAB ACTIVAR INACTIVAR EXPEDIENTES-->				
	        <div>
	        	<table width="100%">	
	        		<!-- INICIO dependencia-->
					<tr >
						<td width="18%" style="padding-left: 10px; padding-top: 10px;">Dependencia:</td>
						<td width="82%" style="padding-top: 10px;">												
							<select name="nom_depe_2" id="nom_depe_2" class="select_crearExp">				
					            <!--{foreach key=key item=item from = $depeArray}-->
									<!--{if $depe_cod eq $key}-->
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
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">A&ntilde;o:</td>
						<td style="padding-top: 10px;">													
							<select name="ano_busq_2" id="ano_busq_2" class="select_crearExp">			
					            <!--{foreach key=key item=item from = $anoArray}-->										
									<option value=<!--{$key}-->><!--{$item}--></option>										
					            <!--{/foreach}-->
								<option value="0"> Todos los a&ntilde;os</option>
					        </select>
						</td>
					</tr>					
					<!-- FIN Fecha para filtra la busqueda ano-->
					<!-- INICIO Seleccionar serie-->
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">Serie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSerie_2" id="selectSerie_2" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una serie </option>
				                <!--{foreach key=key item=item from=$serieArray}--><option value=<!--{$key}-->><!--{$item}--></option>
				                <!--{/foreach}-->
				            </select>
						</td>
					</tr>							
					<!-- Fin Seleccionar serie-->					
					<!-- INICIO Subserie-->
					<tr>
						<td style="padding-left: 10px; padding-top: 10px;">SubSerie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSubSerie_2" id="selectSubSerie_2" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una subSerie </option>                            
				            </select>
						</td>
					</tr>					
					<!-- FIN Subserie-->					
					<!-- INICIO Rango Expediente -->					
					<tr>
		                <td height="40px" valign="center" style="padding-left: 10px;">                	                
		                   Rango Expedientes  
		                </td>
						<td>
							<input readonly="READONLY" type="text" name="rang_NombExpe1" id="rang_NombExpe1"/>Inicio - 	
							<input class="numExpdR3" maxlength="5"  type="text" name="rang_NombExpe2" id="rang_NombExpe2"/>Fin - 
							<input class="numExpdR3" maxlength="5"  type="text" name="rang_NombExpe4" id="rang_NombExpe4"/>&nbsp;&nbsp;Todos
							<input type="radio" name="buscarExpActInac" value="0" checked="checked"/>&nbsp;Activos
							<input type="radio" name="buscarExpActInac" value="1"/>&nbsp;Inactivos
							<input type="radio" name="buscarExpActInac" value="2"/>&nbsp;
							<button class="botones" type="button" id="buscRad_2"> Buscar </button>																				                    
						</td>
		            </tr>
					<!-- FIN Rango Expediente -->
					<!-- INICIO Buscar Expediente -->					
					<tr>
		                <td valign="top" style="padding-left: 10px;">                	                
		                    Expediente  
		                </td>
						<td>								
							<input  type="text" name="nomb_Expe_search_2" class="select_crearExp" id="nomb_Expe_search_2"/>
							<div id="contnExpSearch_2"></div>																																			                    
						</td>
		            </tr>					
					<!-- FIN Buscar Expediente -->
					<!--INICIO TABLA RADICADOS-->
					<tr>		                
						<td colspan="2" valign="center" align="center">							
							<br/><div id="tablaRadi_2"></div>																							                    
						</td>
		            </tr>	
					<!--FIN TABLA RADICADOS-->
					<!-- INICIO COMENTARIO -->					
					<tr>
						<td valign="top" style="padding-left: 10px;">
						 	Comentario:
						</td>
						<td>
							<textarea class="select_crearExp" name="nomb_coment" id="nomb_coment" rows="2" wrap="soft" class="tex_area2"></textarea>						
						</td>
					</tr>					
					<!-- FIN COMENTARIO-->									
					<!--INICIO BOTONES-->
					<tr height="50px">		                
						<td colspan="2" valign="center" align="center">
							<button class="botones" type="button" id="activaExp"> Activar </button>
							<button class="botones" type="button" id="inactiExp"> Inactivar </button>								                    
						</td>
		            </tr>	
					<!--FIN BOTONES-->	
				</table>	
	        </div>
			<!--FIN SEGUNDO TAB ACTIVAR INACTIVAR EXPEDIENTES-->

			<!--INICIO TERCER TAB INCLUIR EXPEDIENTES-->
	        <div>
	        	<table width="100%">
					<!-- INICIO dependencia-->
					<tr >
						<td width="18%" style="padding-left: 10px; padding-top: 10px;">Dependencia:</td>
						<td width="82%" style="padding-top: 10px;">												
							<select name="nom_depe_3" id="nom_depe_3" class="select_crearExp">				
				                <!--{foreach key=key item=item from = $depeArray}-->
									<!--{if $depe_cod eq $key}-->
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
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">A&ntilde;o:</td>
						<td style="padding-top: 10px;">													
								<select name="ano_busq_3" id="ano_busq_3" class="select_crearExp">												
					                <!--{foreach key=key item=item from = $anoArray}-->										
											<option value=<!--{$key}-->><!--{$item}--></option>										
					                <!--{/foreach}-->
									<option value="0"> Todos los a&ntilde;os </option>
					            </select>
						</td>
					</tr>					
					<!-- FIN Fecha para filtra la busqueda ano-->					
					<!-- INICIO Seleccionar serie-->
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">Serie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSerie_3" id="selectSerie_3" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una serie </option>
				                <!--{foreach key=key item=item from=$serieArray}--><option value=<!--{$key}-->><!--{$item}--></option>
				                <!--{/foreach}-->
				            </select>
						</td>
					</tr>							
					<!-- Fin Seleccionar serie-->					
					<!-- INICIO Subserie-->
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">SubSerie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSubSerie_3" id="selectSubSerie_3" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una subSerie </option>                            
				            </select>
						</td>
					</tr>					
					<!-- FIN Subserie-->
					<!-- INICIO Buscar Expediente -->					
					<tr>
		                <td style="padding-left: 10px; height=40px; valign=center">                	                
		                    Expediente  
		                </td>
						<td>								
							<input  type="text" name="nomb_Expe_search_3" class="select_crearExp" id="nomb_Expe_search_3"/>
						    <div id="contnExpSearch_3"></div>																																			                    
						</td>
		            </tr>					
					<!-- FIN Buscar Expediente -->
					<!-- INICIO Buscar Expediente -->					
					<tr>
		                <td style="padding-left: 10px; valign=center">                	                
		                    Radicado  
		                </td>
						<td>								
							<input  type="text" name="nomb_radicado" class="select_crearExp" id="nomb_radicado"/>						    																											                    
						</td>
		            </tr>					
					<!-- FIN Buscar Expediente -->					
					<!-- INICIO COMENTARIO -->					
					<tr>
						<td style="padding-left: 10px; valign=center">
						 	Comentario:
						</td>
						<td>
							<textarea class="select_crearExp" name="nomb_coment_3" id="nomb_coment_3" rows="2" wrap="soft" class="tex_area2"></textarea>						
						</td>
					</tr>
					<!-- FIN COMENTARIO-->					
					<!--INICIO BOTONES Buscar-->
					<tr height="50px">		                
						<td colspan="2" valign="center" align="center">
							<button class="botones" type="button" id="buscRad"> Buscar </button>																			                    
						</td>
		            </tr>	
					<!--FIN BOTONES Buscar-->					
					<!--INICIO TABLA RADICADOS-->
					<tr>		                
						<td colspan="2" valign="center" align="center">							
							<div id="tablaRadi"></div>																							                    
						</td>
		            </tr>	
					<!--FIN TABLA RADICADOS-->					
					<!--INICIO BOTONES Buscar-->
					<tr height="50px">		                
						<td colspan="2" valign="center" align="center">
							<div id="excluRad"></div>																			                    
						</td>
		            </tr>
					<!--FIN BOTONES Buscar-->									
	        	</table>
			</div>
			<!--FIN TERCER TAB INCLUIR EXPEDIENTES-->			

			<!--INICIO CUARTO TAB INCLUIR EXPEDIENTES-->
	        <div>
	           <form id="masiva" name="masiva"  method="POST">
	           	<input type="hidden" value="<!--{$krd}-->" 	name="krd">		
	        	<table width="100%">	        			        		
					<!-- INICIO dependencia-->
					<tr>
						<td width="18%" style="padding-left: 10px; padding-top: 10px;">Dependencia:</td>
						<td width="82%" style="padding-top: 10px;">												
							<select name="nom_depe_4" id="nom_depe_4" class="select_crearExp">				
					            <!--{foreach key=key item=item from = $depeArray}-->
									<!--{if $depe_cod eq $key}-->
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
					<tr>
						<td style="padding-left: 10px; padding-top: 10px;">A&ntilde;o:</td>
						<td style="padding-top: 10px;">													
							<select name="ano_busq_4" id="ano_busq_4" class="select_crearExp">												
					            <!--{foreach key=key item=item from = $anoArray}-->										
									<option value=<!--{$key}-->><!--{$item}--></option>										
					            <!--{/foreach}-->
								<option value="0"> Todos los a&ntilde;os </option>
					        </select>
						</td>
					</tr>					
					<!-- FIN Fecha para filtra la busqueda ano-->					
					<!-- INICIO Seleccionar serie-->
					<tr>
						<td style="padding-left: 10px; padding-top: 10px;">Serie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSerie_4" id="selectSerie_4" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una serie </option>
				                <!--{foreach key=key item=item from=$serieArray}--><option value=<!--{$key}-->><!--{$item}--></option>
				                <!--{/foreach}-->
				            </select>
						</td>
					</tr>							
					<!-- Fin Seleccionar serie-->					
					<!-- INICIO Subserie-->
					<tr>
						<td style="padding-left: 10px; padding-top: 10px;">SubSerie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSubSerie_4" id="selectSubSerie_4" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una subSerie </option>                            
				            </select>
						</td>
					</tr>					
					<!-- FIN Subserie-->					
					<!-- INICIO Fecha de inicio -->
					<tr>
						<td style="padding-left: 10px; padding-top: 10px;">Fecha de Inicio:</td>
						<td style="padding-top: 10px;">
							<input type="text" name="date1" id="date1" readonly="READONLY"/>
							<button id="show1up" type="button">Calendario</button>
							<div id="cal1Container"></div>			
						</td>
					</tr>					
					<!-- FIN Fecha de inicio-->
					<!-- INICIO Responsable-->
					<tr>
						<td style="padding-left: 10px; padding-top: 10px;">Responsable:</td>
						<td style="padding-top: 10px;">													
							<select name="selectResponsable_4" id="selectResponsable_4" class="select_crearExp">
								<!--{foreach key=key item=item from = $usuarios}-->
									<!--{if $codusua eq $key}-->
										<option selected value=<!--{$key}-->><!--{$item}--></option>
									<!--{else}-->
										<option value=<!--{$key}-->><!--{$item}--></option>
									<!--{/if}-->
				                <!--{/foreach}-->								                            
				            </select>
						</td>
					</tr>					
					<!-- FIN Responsable-->					
					<!-- INICIO Rango Expediente -->					
					<tr>
		                <td height="40px" valign="center" style="padding-left: 10px;">                	                
		                   Rango Expedientes  
		                </td>
						<td>
										<input maxlength="5"   type="text" id="rang_ExpeCrear" name="rang_ExpeCrear"   class="numExpdR3"/>
							Inicio - 	<input maxlength="20"  type="text" id="rang_CrearExpe1" name="rang_iniExpe" readonly="READONLY"/>
							Fin - 		<input maxlength="20"  type="text" id="rang_CrearExpe2" readonly="READONLY"/>																											                    
						</td>
		            </tr>					
					<!-- FIN Rango Expediente -->	
					
					<!--INICIO nombres para los expedientes-->
					<tr>
		                <td height="40px" valign="center" style="padding-left: 10px;">                	                
		                   Nombres para Expedientes:  
		                </td>
						<td>
							<textarea name="nombs_Exp_4" id="nombs_Exp_4"></textarea>
							Si es m&aacute;s de un Expediente, los nombres deben ir separados por punto y coma(;)																																	                    
						</td>
		            </tr>					
					<!--FIN nombres para los expedientes-->	
					
					<!--INICIO Seleccionar publico o privado-->
					<tr>
		                <td height="40px" valign="center" style="padding-left: 10px;">                	                
		                   Privacidad:  
		                </td>
						<td>		
							<input type="radio" name="radPrivado4" value="0" checked="checked"/> P&uacute;blico 
						    <input type="radio" name="radPrivado4" value="1" /> Privado						
						</td>	
					</tr>	
					<!--FIN Seleccionar publico o privado-->
					
					<!--INICIO BOTONES-->
					<tr height="50px">		                
						<td colspan="2" height="40px" valign="center" style="padding-left: 10px;" align="center">							
							<button class="botones" type="button" id="crearMassExp">									
								Crear 
							</button>												
							<div id="enviarForm">
									<img id="sonico" class="yui-hidden2" src="../../img/loading.gif"/>			
							</div>							                    
						</td>
		            </tr>	
					<!--FIN BOTONES-->	
					
	        	</table>
			  </form>
			</div>
			<!--FIN CUARTO TAB INCLUIR EXPEDIENTES-->
			
			<!--INICIO QUINTO TAB INCLUIR EXPEDIENTES-->
	        <div>
				<table width="100%">
					<!-- INICIO dependencia-->
					<tr >
						<td width="18%" style="padding-left: 10px; padding-top: 10px;">Dependencia:</td>
						<td width="82%" style="padding-top: 10px;">												
							<select name="nom_depe_5" id="nom_depe_5" class="select_crearExp">				
				                <!--{foreach key=key item=item from = $depeArray}-->
									<!--{if $depe_cod eq $key}-->
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
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">A&ntilde;o:</td>
						<td style="padding-top: 10px;">													
								<select name="ano_busq_5" id="ano_busq_5" class="select_crearExp">												
					                <!--{foreach key=key item=item from = $anoArray}-->										
											<option value=<!--{$key}-->><!--{$item}--></option>										
					                <!--{/foreach}-->
									<option value="0"> Todos los a&ntilde;os </option>
					            </select>
						</td>
					</tr>					
					<!-- FIN Fecha para filtra la busqueda ano-->					
					<!-- INICIO Seleccionar serie-->
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">Serie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSerie_5" id="selectSerie_5" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una serie </option>
				                <!--{foreach key=key item=item from=$serieArray}--><option value=<!--{$key}-->><!--{$item}--></option>
				                <!--{/foreach}-->
				            </select>
						</td>
					</tr>							
					<!-- Fin Seleccionar serie-->					
					<!-- INICIO Subserie-->
					<tr >
						<td style="padding-left: 10px; padding-top: 10px;">SubSerie:</td>
						<td style="padding-top: 10px;">													
							<select name="selectSubSerie_5" id="selectSubSerie_5" class="select_crearExp">
								<option value="0" selected="selected"> Seleccione una subSerie </option>                            
				            </select>
						</td>
					</tr>					
					<!-- FIN Subserie-->
					<tr>
						<td style="padding-left: 10px; padding-top: 10px;">T. Documental:</td>
						<td style="padding-top: 10px;">	
							<select name="selectTipoDocumental" id="selectTipoDocumental" class="select_crearExp" required>
								<option value="0" selected="selected"> Seleccione un Tipo Documental</option>                            
							</select>
						</td>
					</tr>
					<!-- INICIO Buscar Expediente -->					
					<tr>
		                <td style="padding-left: 10px; padding-top: 10px;">                	                
		                    Expediente  
		                </td>
						<td style="padding-top: 10px;">								
							<input type="text" name="nomb_Expe_search_5" class="select_crearExp" id="nomb_Expe_search_5"/>
						    <div id="contnExpSearch_5"></div>																																			                    
						</td>
		            </tr>					
					<!-- FIN Buscar Expediente -->
					<!-- INICIO Buscar Expediente -->					
					<tr>
		                <td style="padding-left: 10px; valign=center">                	                
		                    Radicado  
		                </td>
						<td>								
							<input  type="text" name="nomb_radicado5" class="select_crearExp" id="nomb_radicado5"/>						    																											                    
						</td>
		            </tr>					
					<!-- FIN Buscar Expediente -->					
					<!-- INICIO COMENTARIO -->					
					<tr>
						<td style="padding-left: 10px; valign=center">
						 	Comentario:
						</td>
						<td>
							<textarea class="select_crearExp" name="nomb_coment_5" id="nomb_coment_5" rows="2" wrap="soft" class="tex_area2" maxlength="200"></textarea>						
						</td>
					</tr>
					<!-- FIN COMENTARIO-->					
					<!--INICIO BOTONES Buscar-->
					<tr height="50px">		                
						<td colspan="2" valign="center" align="center">
							<button class="botones" type="button" id="buscRad5"> Buscar </button>																			                    
						</td>
		            </tr>	
					<!--FIN BOTONES Buscar-->					
					<!--INICIO TABLA RADICADOS-->
					<tr>		                
						<td colspan="2" valign="center" align="center">							
							<div id="tablaRadi5"></div>																							                    
						</td>
		            </tr>	
					<!--FIN TABLA RADICADOS-->					
					<!--INICIO BOTONES Buscar-->
					<tr height="50px">		                
						<td colspan="2" valign="center" align="center">
							<div id="incluRad5"></div>																			                    
						</td>
		            </tr>
					<!--FIN BOTONES Buscar-->									
	        	</table>
			</div>
			<!--{/if}-->
			<!--FIN QUINTO TAB INCLUIR EXPEDIENTES-->
			
			<!--{if $perm_temas_exp ge '2' }-->
			<!--INICIO SEXTO TAB MIGRAR EXPEDIENTES-->
			<div>
				<form id="frmasiva" name="frmasiva"  method="POST">
					<table width="100%">
					<tr >
						<td width="18%" style="padding-left: 10px; padding-top: 10px;">Expediente Origen:</td>
						<td width="12%" style="padding-left: 10px; padding-top: 10px;">
							<input type='text' maxlength='19' id='txtExpOri1' placeholder='numero exp.' required />
						</td>
						<td width="70%" style="padding-top: 10px;">												
							<input type='text' size='40' id='txtExpOri1Desc' class='select_crearExp' readonly />
						</td>
					</tr>
					<tr >
						<td width="18%" style="padding-left: 10px; padding-top: 10px;">Expediente Destino:</td>
						<td width="12%" style="padding-left: 10px; padding-top: 10px;">
							<input type='text' maxlength='19' id='txtExpDes1' placeholder='numero exp.' required />
						</td>
						<td width="70%" style="padding-top: 10px;">												
							<input type='text' size='40' id='txtExpDes1Desc' class='select_crearExp' readonly />
						</td>
					</tr>
					<tr>
						<td style="padding-left: 10px; valign=center">
						 	Comentario:
						</td>
						<td colspan='2'>
							<textarea class="select_crearExp" name="nomb_coment_6" id="nomb_coment_6" rows="2" wrap="soft" class="tex_area2" required ></textarea>			
						</td>
					</tr>					
					<tr>
						<td colspan='3'>&nbsp;</td>
					</tr>
					<tr height="50px">
						<td align='center' colspan='3'>
							<button class="botones" type="reset" id="btnReset"> Limpiar </button>
							<button class="botones" type="button" id="btnMigrar"> Migrar </button>							
						</td>
					</tr>
					</table>
				</form>
			</div>
			<!--{/if}-->
			<!--FIN SEXTO TAB INCLUIR EXPEDIENTES-->
	    </div>
	</div>	
</body>
</html>

<!--{if $perm_temas_exp gt '2'}--> 
	<!-- Si los permisos son correctos muestra las demas pestañas-->
	<script type="text/javascript" src="./js/adm_Exp_Activar.js"></script>
	<script type="text/javascript" src="./js/adm_Exp_Excluir.js"></script>
	<script type="text/javascript" src="./js/adm_Exp_Crear.js"></script>
	<script type="text/javascript" src="./js/adm_Exp_Incluir.js"></script>
<!--{/if}-->
<!--{if $perm_temas_exp ge '3'}--> 
	<script type="text/javascript" src="./js/adm_Exp_Migrar.js"></script>
<!--{/if}-->