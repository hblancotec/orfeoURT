/*
 * Incluir radicados en Expedientes
 * Se utiliza cuando el usuario desea incluir n radicados en un expediente
*/


//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function (){	
	//VARIABLES GENERALES GLOBALES
		var	buscRad5				= new YAHOO.widget.Button("buscRad5"),
			GLOBAL_enviado_conf		= false,
			GLOBAL_depecodi			= depecodi(),			
			GLOBAL_codusua			= codusua(),
			GLOBAL_usuadoc			= usuadoc(),
			GLOBAL_sid				= sid();
			

		//FUNCION
		//BUSCAR EXPEDIENTES
		/*
		 * Busca las dependencias dependiendo de los permisos otorgados
		 * y de la dependencia seleccionada.
		 */
	
		(function(){
			var search = new YAHOO.util.XHRDataSource('libs/adm_nombreTemasExpJ.php');
			
			// Set the responseType
			search.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
			
			// Metodo envio
			search.connMethodPost = true;
			
			// Define the schema of the delimited results
			search.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
			};
			
			// Instantiate the AutoComplete
			var autoNomb = new YAHOO.widget.AutoComplete("nomb_Expe_search_5", "contnExpSearch_5", search);
			
			// Tamaño maximo a mostrar de resultados
			autoNomb.maxResultsDisplayed = 30;
			
			
			//funcion para cuando se seleccione un item
		    var myHandlerExcluir = function(sType, aArgs) {				        
		        var datos = '';
				YAHOO.util.Dom.get('nomb_radicado5').value = datos;     
		    };		
			
			// Set Request change de values to send
			autoNomb.generateRequest = function(sQuery){				
				var depe,
					ano_busq,
					selectSubSerie,					
					selectSerie;
									
				ano_busq 		= YAHOO.util.Dom.get('ano_busq_5').value;
				selectSerie 	= YAHOO.util.Dom.get('selectSerie_5').value;	
				selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie_5').value;
				depe 			= YAHOO.util.Dom.get('nom_depe_5').value;
				
				return 			GLOBAL_sid			+
								"&evento=1"			+
								"&query=" 			+ sQuery +
								"&depe=" 			+ depe +
								"&ano_busq=" 		+ ano_busq +
								"&selectSerie=" 	+ selectSerie +							
								"&selectSubSerie=" 	+ selectSubSerie;
			};
			
			//Evento cuando selecciona un item
			autoNomb.itemSelectEvent.subscribe(myHandlerExcluir); 
			return {
				search: search,
				autoNomb: autoNomb
			};
		})();	
		


		// FUNCION  
		//ENVIAR DEPENDENCIA PARA OBTENER SERIE	
		/*
		*Funcion que cambia el valor de la dependencia para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendDepBusq(e){
			var depeInput 	= YAHOO.util.Event.getTarget(e).value,
				selSer		= YAHOO.util.Dom.get("selectSerie_5"),
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_5"),				
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 6									 									
							  + '&depeInput='   + depeInput;				
			
			selSer.options.length=0			
			selSer.options[0] = new Option('Seleccione una Serie',0,true,true)
							
			selSubSer.options.length=0			
			selSubSer.options[0] = new Option('Seleccione una Subserie',0,true,true)
			
			if(depeInput != 0) {
				var successHandler = function(o) {			
					var r		= eval('(' + o.responseText + ')');
					if (r.respuesta == true) {					
						var lenSuv = r.mensaje.length;					
						for (i = 0; i < lenSuv; i++) {
							var j = selSer.options.length;																	
							selSer.options[j] = 
								new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
						}
		            }else{
						alert(r.mensaje);
					}
				};
						
				var failureHandler = function(o) {
					alert("Error retornando datos de Serie " 
							+ o.status + " : " + o.statusText);
				};
						
				var callback = {
					success:successHandler,
					failure:failureHandler
				};
						
				var transaction = YAHOO.util.Connect.asyncRequest(
								"POST"
								, sUrl
								, callback
								, postData);			
															
			} else {
				GLOBAL_serie = 0;
				return;
			}
		}

		
		// FUNCION  
		//ENVIAR SERIE PARA OBTENER SUBSERIE	
		/*
		*Funcion que cambia el valor de la serie para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendSerie(e){
			var serieInput 	= YAHOO.util.Event.getTarget(e).value,
				depeInput	= YAHOO.util.Dom.get("nom_depe_5").value,
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_5"),
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 1									 									
							  + '&selectSerie=' + serieInput
							  + '&depeInput='   + depeInput;
							  
			selSubSer.options.length=0
			selSubSer.options[0] = new Option('Seleccione una subSerie',0,true,true)
			
			if(serieInput != 0){				 
						
				var successHandler = function(o) {			
					var r		= eval('(' + o.responseText + ')');
					if (r.respuesta == true) {					
						var lenSuv = r.mensaje.length;					
						for (i = 0; i < lenSuv; i++) {
							var j = selSubSer.options.length;																	
							selSubSer.options[j] = 
								new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
						}
		            }else{
						alert(r.mensaje);
					}
				};
						
				var failureHandler = function(o) {
					alert("Error retornando datos de subSerie " 
							+ o.status + " : " + o.statusText);
				};
						
				var callback = {
					success:successHandler,
					failure:failureHandler
				};
						
				var transaction = YAHOO.util.Connect.asyncRequest(
								"POST"
								, sUrl
								, callback
								, postData);			
															
			}else{
				GLOBAL_serie = 0;
				return;
			}
		}
		
		// FUNCION  
		//ENVIAR SERIE PARA OBTENER TIPOS DOCUMENTALES DE LA TRD	
		/*
		*Funcion que cambia el valor de la subserie para ser enviada en
		*el siguiete input y generar combobox de los tipos documentales en cascada.
		*/
		
		function sendSubSerie(e){
			var subserieInput 	= YAHOO.util.Event.getTarget(e).value,
				anhoBusq	= YAHOO.util.Dom.get("ano_busq").value,
				depeInput	= YAHOO.util.Dom.get("nom_depe_5").value,
				selSerie	= YAHOO.util.Dom.get("selectSerie_5").value,
				selTipDoc	= YAHOO.util.Dom.get("selectTipoDocumental"),
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 8	
							  + '&selectSerie=' + selSerie 							  
							  + '&selectSubSerie=' + subserieInput 
							  + '&depeInput='   + depeInput;					
			
			vaciarTipDocs();
			YAHOO.util.Dom.get("nomb_Expe_search").value = '';
			
			if(subserieInput != 0){
						
				var successHandler = function(o) {			
					var r		= eval('(' + o.responseText + ')');
					if (r.respuesta == true) {					
						var lenSuv = r.mensaje.length;					
						for (i = 0; i < lenSuv; i++) {
							var j = selTipDoc.options.length;																	
							selTipDoc.options[j] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
						}
		            }else{
						alert(r.mensaje);
					}
				};
						
				var failureHandler = function(o) {
					alert("Error retornando datos de tipos documentales " 
							+ o.status + " : " + o.statusText);
				};
						
				var callback = {
					success:successHandler,
					failure:failureHandler
				};
						
				var transaction = YAHOO.util.Connect.asyncRequest(
								"POST"
								, sUrl
								, callback
								, postData);
															
			}else{
				GLOBAL_Subserie = 0;
				return;
			}
		}
	
		
		//FUNCION
		//VALIDAR RADICADO ANTES DE SER ENVIADO
		/*
		 * Valida si el formato de radicado es valido
		 * tambien deja en blanco el campo de expediente para que este 
		 * no sea enviado
		 */
		
		function val_radicado5(){
			
			var datos 			= '',
				mensaje1		= "Digite radicados completos (14 digitos) separados por coma(,)",
				nomb_radicado5 	= YAHOO.util.Dom.get('nomb_radicado5').value,
				rad				= /^(([ ]*[0-9]{15}[ ]*)[,]?)*/;
				
			//YAHOO.util.Dom.get('nomb_Expe_search_5').value = datos;
			
			if(!rad.test(nomb_radicado5) || nomb_radicado5.length < 15 ){
				alert(mensaje1);
			}  
		}


		//FUNCION
		//BUSCAR RADICADOS INCLUIDOS EN EXPEDIENTES
		/*
		 * Si el usuario tiene un numero de radicado o expediente valido busca
		 * los radicados incluidos en este y los expediente relacionados.
		 */
		function buscar_radiEnExpedientes5(){
			var exp					= /^[0-9]{17,19}[E]{1}/,
				rad					= /^(([ ]*[0-9]{15}[ ]*)[,]?)*/,
				sUrl				= 'libs/adm_nombreTemasExpJ.php',
				result				= '',
				pass_exp			= false,
				pass_rad			= false,
				nomb_Expe_search_5 	= YAHOO.util.Dom.get('nomb_Expe_search_5').value,
				nomb_radicado5		= YAHOO.util.Dom.get('nomb_radicado5').value,
				no_exp5				= exp.exec(nomb_Expe_search_5),
				no_rads5			= rad.exec(nomb_radicado5),
				depe_5 				= YAHOO.util.Dom.get('nom_depe_5').value,
				ano_busq_5 			= YAHOO.util.Dom.get('ano_busq_5').value,
				selectSerie_5 		= YAHOO.util.Dom.get('selectSerie_5').value,
				selectSubSerie_5 	= YAHOO.util.Dom.get('selectSubSerie_5').value,
				tablaRadi5			= YAHOO.util.Dom.get('tablaRadi5'),
				incluRad5			= YAHOO.util.Dom.get('incluRad5'),
				buscaBoton5			= YAHOO.util.Dom.get('buscRad5-button'),
				

				mensaje1			= "El numero del expediente o los radicados no son correctos",
				mensaje2			= "Ya se esta buscando...\n tomala suave",	
				mensaje3			= "Enviando...",	
				mensaje4			= "buscar",	
				mensaje5			= "No se modificaron los siguiente Expediente \n pueden contener radicados activos",
				 
				postData 			= GLOBAL_sid +
									    "&evento=" + 12 +
										"&depe=" 			+ depe_5 +
										"&ano_busq=" 		+ ano_busq_5 +
										"&selectSerie=" 	+ selectSerie_5 +							
										"&selectSubSerie=" 	+ selectSubSerie_5;
			
			//borrar elementos existente
			while (tablaRadi5.hasChildNodes()) 
					tablaRadi5.removeChild(tablaRadi5.firstChild);
			while (incluRad5.hasChildNodes()) 
					incluRad5.removeChild(incluRad5.firstChild);			
						
			//enviamos el numero del expediente
			if(exp.test(no_exp5)){
				postData += "&numExpedi=" 	+ no_exp5;
				pass_exp = true;
			}
			//enviamos los numeros de radicados
			if(rad.test(nomb_radicado5)){
				postData += "&rad_num="		+ nomb_radicado5;
				pass_rad = true;
			}
			if (!pass_exp && !pass_rad) {
				alert(mensaje1);
				return;
			}
			
			var successHandler = function(o) {			
				var r		= eval('(' + o.responseText + ')');
				
				if (r.respuesta == true) {					
					var actu = tablaRadi5.innerHTML.value;
					//creamos la tabla de forma dinamica
					
					result	 =	"<form id='checkradbox5'><table border='1px' bordercolor='black' width='100%'>";
					
					result	+=	"<tr>" +
									"<td valign='center' align='center' width='20%'><b>Radicado</b></td>"+
									"<td valign='center' align='center' width='79%'><b>Asunto</b></td>"+
									"<td valign='center' align='center' width='1%'></td>"+
								"</tr>";
					
					for (var i = r.mensaje.length - 1; i >= 0; i--){
						var numRad  = r.mensaje[i].numRad,
							nomRad = r.mensaje[i].nomRad;
						
						result	+=	"<tr><td>"+ numRad + "</td><td>" + nomRad + "</td><td>"
									+ "<input type=checkbox name=incluRad5[] value='"+ numRad + "'></td></tr>";														
					};
					
					result 	+= "</table></form>";
					 
					tablaRadi5.innerHTML = result;
						
					var excRadBot = new YAHOO.widget.Button({
					    id: "botonIncluRad",
					    type: "button", 
					    label: "Incluir", 
					    container: "incluRad5" 
					});

					excRadBot.on("click", incluir_radExpediente);											
				}
	            else{
					alert(r.mensaje);
				}
				
				GLOBAL_enviado_conf = false;
				buscaBoton5.disabled = false;
				buscaBoton5.value	= mensaje4;
			};
					
			var failureHandler = function(o) {
				alert("Error retornando datos de subSerie " 
						+ o.status + " : " + o.statusText);
			};
					
			var callback = {
				success:successHandler,
				failure:failureHandler
			};
				
				
			if (!GLOBAL_enviado_conf) {
		        GLOBAL_enviado_conf = true;
				buscaBoton5.value = mensaje3;
	    		buscaBoton5.disabled = true;
				
				var transaction = YAHOO.util.Connect.asyncRequest(
																"POST"
																, sUrl
																, callback
																, postData);
								
		    } else {
		        alert(mensaje2);
		        return ;
		    }	
		}
	
		
		
		//FUNCION
		//Incluye los radicados de un expediente.
		/*
		 * Envia el evento y los numeros de radicados a incluir 
		 * de un expediente en el arreglo incluRad5[] creados desde 
		 * este mismo script.
		 */
		
		function incluir_radExpediente(){

			var exp				= /^[0-9]{17,19}[E]{1}/,
				nomb_Expe_search_5 	= YAHOO.util.Dom.get('nomb_Expe_search_5').value,
				nrosrie			= YAHOO.util.Dom.get('selectSerie_5').value,
				nrosubserie		= YAHOO.util.Dom.get('selectSubSerie_5').value,
				tipodoc 		= YAHOO.util.Dom.get('selectTipoDocumental').value,
				no_exp5			= exp.exec(nomb_Expe_search_5),
				sUrl		   	= 'libs/adm_nombreTemasExpJ.php',
				tablaRadi5		= YAHOO.util.Dom.get('tablaRadi5'),
				incluRad5		= YAHOO.util.Dom.get('incluRad5'),
				nomb_coment_5 	= YAHOO.util.Dom.get('nomb_coment_5'),
				mensaje1		=  "El comentario esta sin datos o \n" + "tiene menos de 20 caracteres.",
				mensaje2		=  "El c\xf3digo de expediente est\xe1 mal.",
				postData 	   	= GLOBAL_sid +
								  "&evento=" + 13 +
								  '&depeCodiUsua='	+ GLOBAL_depecodi +
								  '&depe='			+ YAHOO.util.Dom.get('nom_depe_5').value +
								  '&codUsua='		+ GLOBAL_codusua + 
								  '&usuaDoc='		+ GLOBAL_usuadoc + 
								  '&selectSerie=' 	+ nrosrie +
								  '&selectSubSerie='+ nrosubserie +
								  '&codTdoc=' 		+ tipodoc +
								  '&nomb_coment_5=' + nomb_coment_5.value ;
			
			if((nomb_coment_5.value.trim() == '') || (nomb_coment_5.value.length < 20)){
				alert(mensaje1);
				return
			}
			
			if(tipodoc == 0){
				alert('Tipo documental exigido.');
				return
			}
			
			if(exp.test(no_exp5)){
				postData += "&numExpedi=" 	+ no_exp5;
			} else {
				alert(mensaje2);
				return;
			}
				
			var successHandler = function(o) {
				var r	= eval('(' + o.responseText + ')');
				if (r.respuesta == true) {
					alert(r.mensaje);
				}else{
					alert(r.mensaje);
				}
				
				nomb_coment_5.value = '';	
				
				//borrar elementos existente
				while (tablaRadi5.hasChildNodes()) 
						tablaRadi5.removeChild(tablaRadi5.firstChild);
				while (incluRad5.hasChildNodes()) 
						incluRad5.removeChild(incluRad5.firstChild);				
			};
					
			var failureHandler = function(o) {
				alert("Error retornando datos de subSerie " + o.status + " : " + o.statusText);
			};
					
			var callback = {
				success:successHandler,
				failure:failureHandler
			};
			
			var formObject = YAHOO.util.Dom.get('checkradbox5');
			YAHOO.util.Connect.setForm(formObject);
			
			var transaction = YAHOO.util.Connect.asyncRequest(
							"POST"
							, sUrl
							, callback
							, postData);
		}
		
	
		//EVENTO		
		/*
		 * Manejo de eventos originados por acciones o botones
		 */	
		
		YAHOO.util.Event.on(	"nom_depe_5", "change", sendDepBusq); 
		
		YAHOO.util.Event.on(	"selectSerie_5", "change", sendSerie);
		
		YAHOO.util.Event.on(	"selectSubSerie_5", "change", sendSubSerie);
		
		YAHOO.util.Event.on(	"nomb_radicado5", "change", val_radicado5);		
		
		buscRad5.on(	"click", buscar_radiEnExpedientes5);			
								
	});