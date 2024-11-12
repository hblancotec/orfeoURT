/*
 * Activar/Inactivar Expedientes
 * Se utiliza cuando el usuario crea un expediente
 * y el numero con el que lo hace no es el correcto
*/


//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function (){	
	//VARIABLES GENERALES GLOBALES
		var	buscRad				= new YAHOO.widget.Button("buscRad"),
			GLOBAL_enviado_conf		= false,
			GLOBAL_depecodi			= depecodi(),			
			GLOBAL_codusua			= codusua(),			
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
			var autoNomb = new YAHOO.widget.AutoComplete("nomb_Expe_search_3", "contnExpSearch_3", search);
			
			// Tamaño maximo a mostrar de resultados
			autoNomb.maxResultsDisplayed = 30;
			
			
			//funcion para cuando se seleccione un item
		    var myHandlerExcluir = function(sType, aArgs) {				        
		        var datos = '';
				YAHOO.util.Dom.get('nomb_radicado').value = datos;     
		    };		
			
			// Set Request change de values to send
			autoNomb.generateRequest = function(sQuery){				
				var depe,
					ano_busq,
					selectSubSerie,					
					selectSerie;
									
				ano_busq 		= YAHOO.util.Dom.get('ano_busq_3').value;
				selectSerie 	= YAHOO.util.Dom.get('selectSerie_3').value;	
				selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie_3').value;
				depe 			= YAHOO.util.Dom.get('nom_depe_3').value;
				
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
		//ENVIAR DEPENDENCIA PARA OPTENER SERIE	
		/*
		*Funcion que cambia el valor de la dependencia para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendDepBusq(e){
			var depeInput 	= YAHOO.util.Event.getTarget(e).value,
				selSer		= YAHOO.util.Dom.get("selectSerie_3"),
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_3"),				
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 6									 									
							  + '&depeInput='   + depeInput
							  + '&veractivos='   + veractivos();				
			
			selSer.options.length=0			
			selSer.options[0] = new Option('Seleccione una Serie',0,true,true)
							
			selSubSer.options.length=0			
			selSubSer.options[0] = new Option('Seleccione una subSerie',0,true,true)
			
			if(depeInput != 0){				 
						
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
															
			}else{
				GLOBAL_serie = 0;
				return;
			}
		}

		
		// FUNCION  
		//ENVIAR SERIE PARA OPTENER SUBSERIE	
		/*
		*Funcion que cambia el valor de la serie para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendSerie(e){
			var serieInput 	= YAHOO.util.Event.getTarget(e).value,
				depeInput	= YAHOO.util.Dom.get("nom_depe_3").value,
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_3"),
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 1									 									
							  + '&selectSerie=' + serieInput
							  + '&depeInput='   + depeInput
							  + '&veractivos='   + veractivos();
							  
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
	
		
		//FUNCION
		//VALIDAR RADICADO ANTES DE SER ENVIADO
		/*
		 * Valida si el formato de radicado es valido
		 * tambien deja en blanco el campo de expediente para que este 
		 * no sea enviado
		 */
		
		function val_radicado(){
			
			var datos 			= '',
				mensaje1		= "El numero del radicado no es correcto",
				nomb_radicado 	= YAHOO.util.Dom.get('nomb_radicado').value,
				rad				= /^[0-9]{14}/;
				
			YAHOO.util.Dom.get('nomb_Expe_search_3').value = datos;
			
			if(!rad.test(nomb_radicado) || nomb_radicado.length > 14 ){
				alert(mensaje1);
			}  
		}


		//FUNCION
		//BUSCAR RADICADOS INCLUIDOS EN EXPEDIENTES
		/*
		 * Si el usuario tiene un numero de radicado o expediente valido busca
		 * los radicados incluidos en este y los expediente relacionados.
		 */
		function buscar_radiEnExpedientes(){
			var exp					= /^[0-9]{17,19}[E]{1}/,
				rad					= /^[0-9]{15}/,
				sUrl				= 'libs/adm_nombreTemasExpJ.php',
				result				= '',
				
				nomb_Expe_search_3 	= YAHOO.util.Dom.get('nomb_Expe_search_3').value,
				no_exp				= exp.exec(nomb_Expe_search_3),				
				depe_3 				= YAHOO.util.Dom.get('nom_depe_3').value,
				ano_busq_3 			= YAHOO.util.Dom.get('ano_busq_3').value,
				selectSerie_3 		= YAHOO.util.Dom.get('selectSerie_3').value,
				selectSubSerie_3 	= YAHOO.util.Dom.get('selectSubSerie_3').value,
				nomb_radicado		= YAHOO.util.Dom.get('nomb_radicado').value,
				tablaRadi			= YAHOO.util.Dom.get('tablaRadi'),
				excluRad			= YAHOO.util.Dom.get('excluRad'),
				buscaBoton			= YAHOO.util.Dom.get('buscRad-button'),
				

				mensaje1			= "El numero del expediente o el del radicado no son correctos",
				mensaje2			= "Ya se esta buscando...\n tomala suave",	
				mensaje3			= "Enviando...",	
				mensaje4			= "buscar",	
				mensaje5			= "No se modificaron los siguiente Expediente \n pueden contener radicados activos",
				 
				postData 			= GLOBAL_sid +
									    "&evento=" + 9 +
										"&depe=" 			+ depe_3 +
										"&ano_busq=" 		+ ano_busq_3 +
										"&selectSerie=" 	+ selectSerie_3 +							
										"&selectSubSerie=" 	+ selectSubSerie_3 ;
			
			//borrar elementos existente
			while (tablaRadi.hasChildNodes()) 
					tablaRadi.removeChild(tablaRadi.firstChild);
			while (excluRad.hasChildNodes()) 
					excluRad.removeChild(excluRad.firstChild);			
						
			//enviamos el numero del expediente
			if(exp.test(no_exp)){
				postData += "&numExpedi=" 	+ no_exp;
			}else if(rad.test(nomb_radicado) && (nomb_radicado.length = 15)){
				postData += "&rad_num="		+ nomb_radicado;				
			}else{
				alert(mensaje1);
				return;
			}
			
			var successHandler = function(o) {			
				var r		= eval('(' + o.responseText + ')');
				
				if (r.respuesta == true) {					
					var actu = tablaRadi.innerHTML.value;
					//creamos la tabla de forma dinamica
					
					result	 =	"<form id='checkradbox'><table border='1px' bordercolor='black' width='100%'>";
					
					result	+=	"<tr>" +
									"<td valign='center' align='center' width='130px'><b>N&uacute;mero</b></td>"+
									"<td valign='center' align='center' width='100%'><b>Nombre</b></td>"+
									"<td valign='center' align='center' width='120px'><b>Radicado</b></td>"+
									"<td valign='center' align='center'></td>"+
								"</tr>";
					
					for (var i = r.mensaje.length - 1; i >= 0; i--){
						var numExp  = r.mensaje[i].numExp,
							numRad  = r.mensaje[i].numRad,
							nombExp = r.mensaje[i].nombExp;
						
						result	+=	"<tr><td>" 	+ numExp +"</td><td>" +
									"<textarea readonly='READONLY'  class='select_crearExp nombActuExp' rows='2' wrap='soft' class='tex_area2'>" 
									+ nombExp +"</textarea>" +
								    "</td><td>" + numRad + "</td><td>"
									+ "<input type=checkbox name=excluRad[] value='"+ numExp +'_'+ numRad + 
									"'></td></tr>";														
					};
					
					result 	+= "</table></form>";
					 
					tablaRadi.innerHTML = result;
						
					var excRadBot = new YAHOO.widget.Button({
					    id: "botonExcluRad", 
					    type: "button", 
					    label: "Excluir", 
					    container: "excluRad" 
					});

					excRadBot.on("click",		
					excluir_radExpediente);											
				}
	            else{
					alert(r.mensaje);
				}
				
				GLOBAL_enviado_conf = false;
				buscaBoton.disabled = false;
				buscaBoton.value	= mensaje4;
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
				buscaBoton.value = mensaje3;
	    		buscaBoton.disabled = true;
				
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
		//Excluye los radicados de un expediente.
		/*
		 * Envia el evento y el numero de radicados a excluir 
		 * de un expediente en el arreglo excluRad[] creados desde 
		 * este mismo script.
		 */
		
		function excluir_radExpediente(){
			var sUrl		   	= 'libs/adm_nombreTemasExpJ.php',
				tablaRadi		= YAHOO.util.Dom.get('tablaRadi'),
				excluRad		= YAHOO.util.Dom.get('excluRad'),
				nomb_coment_3 	= YAHOO.util.Dom.get('nomb_coment_3'),
				mensaje1		=  "El comentario esta sin datos o \n" +
							  	   "tiene menos de 20 caracteres",
				postData 	   	= GLOBAL_sid +
								  "&evento=" + 10 +
								  '&depeCodiUsua='	+ GLOBAL_depecodi +
								  '&depe='			+ GLOBAL_depecodi +
								  '&codUsua='		+ GLOBAL_codusua + 
								  "&nomb_coment_3=" + nomb_coment_3.value;
			
			if((nomb_coment_3.value.trim() == '') || (nomb_coment_3.value.length < 20)){
				alert(mensaje1);
				return
			} 
				
			var successHandler = function(o) {
				var r		= eval('(' + o.responseText + ')');
				if (r.respuesta == true) {
					alert(r.mensaje);
				}else{
					alert(r.mensaje);
				}
				
				nomb_coment_3.value = '';	
				
				//borrar elementos existente
				while (tablaRadi.hasChildNodes()) 
						tablaRadi.removeChild(tablaRadi.firstChild);
				while (excluRad.hasChildNodes()) 
						excluRad.removeChild(excluRad.firstChild);				
			};
					
			var failureHandler = function(o) {
				alert("Error retornando datos de subSerie " 
						+ o.status + " : " + o.statusText);
			};
					
			var callback = {
				success:successHandler,
				failure:failureHandler
			};
			
			var formObject = YAHOO.util.Dom.get('checkradbox');
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
		
		YAHOO.util.Event.on(	"nom_depe_3",
								"change",
								sendDepBusq); 
		
		YAHOO.util.Event.on(	"selectSerie_3",
								"change",
								sendSerie);
		
		YAHOO.util.Event.on(	"nomb_radicado",
								"change",
								val_radicado);		
		
		buscRad.on(	"click",		
					buscar_radiEnExpedientes);			
								
	});