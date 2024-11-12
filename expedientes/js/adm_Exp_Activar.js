/*
 * Activar/Inactivar Expedientes
 * Se utiliza cuando el usuario crea un expediente
 * y el numero con el que lo hace no es el correcto
*/


//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function () {	
	//VARIABLES GENERALES GLOBALES
		var	activaExp				= new YAHOO.widget.Button("activaExp"),
			inactiExp				= new YAHOO.widget.Button("inactiExp"),
			buscRad_2				= new YAHOO.widget.Button("buscRad_2"),
					
			Global_EnvioForm		= false,			
			GLOBAL_sid				= sid(),	
			
			GLOBAL_depecodi			= depecodi(),			
			GLOBAL_codusua			= codusua();			

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
			var autoNomb = new YAHOO.widget.AutoComplete("nomb_Expe_search_2", "contnExpSearch_2", search);
			
			// Tama�o maximo a mostrar de resultados
			autoNomb.maxResultsDisplayed = 30;
			
			
			//funcion para cuando se seleccione un item
		    var myHandlerNomb = function(sType, aArgs) {
					        
		        var oData 	= aArgs[2].toString().substring(19); // object literal of selected item's result data
		        var renTex 	= YAHOO.util.Dom.get("nombActuExp");			
				
				while (renTex.hasChildNodes()) 
					renTex.removeChild(renTex.firstChild);
																	
				texto1 		= document.createTextNode(oData);			
				renTex.appendChild(texto1);				        
		    };			
			
			// Set Request change de values to send
			autoNomb.generateRequest = function(sQuery){				
				var depe,
					ano_busq,
					selectSubSerie,					
					selectSerie;
									
				ano_busq 		= YAHOO.util.Dom.get('ano_busq_2').value;
				selectSerie 	= YAHOO.util.Dom.get('selectSerie_2').value;	
				selectSubSerie 	= zfill(YAHOO.util.Dom.get('selectSubSerie_2').value, 3);
				depe 			= YAHOO.util.Dom.get('nom_depe_2').value;
				
				return 			GLOBAL_sid			+
								"&evento=1"			+
								"&query=" 			+ sQuery +
								"&depe=" 			+ depe +
								"&ano_busq=" 		+ ano_busq +
								"&selectSerie=" 	+ selectSerie +							
								"&selectSubSerie=" 	+ selectSubSerie +
								"&todos=" 			+ 1;;
			};
			
			//Evento cuando selecciona un item
			autoNomb.itemSelectEvent.subscribe(myHandlerNomb); 
			
			return {
				search: search,
				autoNomb: autoNomb
			};		
			
		})();	
		
		//FUNCTION ZFILL
		function zfill(number, width) {
		    var numberOutput = Math.abs(number); /* Valor absoluto del número */
		    var length = number.toString().length; /* Largo del número */ 
		    var zero = "0"; /* String de cero */  
		    
		    if (width <= length) {
		        if (number < 0) {
		             return ("-" + numberOutput.toString()); 
		        } else {
		             return numberOutput.toString(); 
		        }
		    } else {
		        if (number < 0) {
		            return ("-" + (zero.repeat(width - length)) + numberOutput.toString()); 
		        } else {
		            return ((zero.repeat(width - length)) + numberOutput.toString()); 
		        }
		    }
		}

		// FUNCION  
		//ENVIAR DEPENDENCIA PARA OPTENER SERIE	
		/*
		*Funcion que cambia el valor de la dependencia para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendDepBusq(e){
			var depeInput 	= YAHOO.util.Event.getTarget(e).value,
				selSer		= YAHOO.util.Dom.get("selectSerie_2"),
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_2"),				
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 6									 									
							  + '&depeInput='   + depeInput			
							  + '&veractivos='  + veractivos();
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
				depeInput	= YAHOO.util.Dom.get("nom_depe_2").value,
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_2"),
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 1									 									
							  + '&selectSerie=' + serieInput
							  + '&depeInput='   + depeInput
							  + '&veractivos='  + veractivos();					
							
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
						rang_Expedientes();
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
		//ACTIVAR NUMERO DE EXPEDIENTE	
		/*
		*Funcion que envia el numero del expediente y lo activa
		*/		
		function acti_Expediente(e){
			
			var exp					= /^[0-9]{16,19}[E]{1}/,
				exp2				= /^[0-9]{5}/,
				exp3				= /^[0-9]{13}/,
				sUrl				= 'libs/adm_nombreTemasExpJ.php',
				nomb_Expe_search_2 	= YAHOO.util.Dom.get('nomb_Expe_search_2').value,
				no_exp				= exp.exec(nomb_Expe_search_2),
				nomb_coment			= YAHOO.util.Dom.get("nomb_coment"),
				depe_2 				= YAHOO.util.Dom.get('nom_depe_2').value,
				ano_busq_2 			= YAHOO.util.Dom.get('ano_busq_2').value,
				selectSerie_2 		= YAHOO.util.Dom.get('selectSerie_2').value,
				selectSubSerie_2 	= zfill(YAHOO.util.Dom.get('selectSubSerie_2').value, 3),				
				rang_NombExpe2		= YAHOO.util.Dom.get('rang_NombExpe2').value,
				rang_NombExpe4		= YAHOO.util.Dom.get('rang_NombExpe4').value,
				rang_NombExpe1		= YAHOO.util.Dom.get('rang_NombExpe1').value,	
				tablaRadi_2			= YAHOO.util.Dom.get('tablaRadi_2'),			
				boton               = YAHOO.util.Event.getTarget(e).id,
				accion				= (boton == 'activaExp-button')? 0 : 1,
				data				= (boton == 'activaExp-button')? 'habilitaron' : 'Inhabilitaron',
				mensaje1			= "El comentario esta sin datos o \n" +
							  	   	  "tiene menos de 20 caracteres",			
				mensaje2			= "Rango no es correcto",
				mensaje3			= "El N�mero del expediente no es correcto",
				mensaje4			= "Se " + data  + " los expedientes ",
				mensaje5			= "No se modificaron los siguiente Expediente \n pueden contener radicados activos",
				 
				postData 			= GLOBAL_sid 
										+ '&evento=' + 8
										+ '&accion=' + accion 
										+ '&depeCodiUsua='	+ GLOBAL_depecodi
									    + '&codUsua='		+ GLOBAL_codusua 
										+ '&comentario=' + nomb_coment.value;
			
			//validar datos a enviar 
					
			if ((nomb_coment.value.trim() == '') || (nomb_coment.value.length < 20)){
				alert(mensaje1);
				return;
			} 
			
			if(!exp.test(no_exp) 
					&& nomb_Expe_search_2 != ''){
				alert(mensaje3);
				return;
			} 				
			
			if( nomb_Expe_search_2 == '' && 
					(!exp2.test(rang_NombExpe2) 
						|| !exp2.test(rang_NombExpe4) 
						|| !exp3.test(rang_NombExpe1)
						|| ((rang_NombExpe4-rang_NombExpe2))< 1)){
				alert(mensaje2);
				return
			}
			
			//enviamos el numero del expediente
			if(exp.test(no_exp)){
				postData += '&numExpedi=' 	 + no_exp +
							'&depe=' 		+ depe_2;
				
				Global_EnvioForm = false;
			}else{
				postData += "&depe=" 			+ depe_2 +
							"&ano_busq=" 		+ ano_busq_2 +
							"&selectSerie=" 	+ selectSerie_2 +							
							"&selectSubSerie=" 	+ selectSubSerie_2 +
							"&rang_ini="		+ rang_NombExpe2 +
							"&rang_fin="		+ rang_NombExpe4;
				
				Global_EnvioForm = true;	
			}
					
			var successHandler = function(o) {			
				var r		= eval('(' + o.responseText + ')');
				if (r.respuesta == true) {
										
					alert(mensaje4 + r.mensaje);
					
					if(r.norealizados != null){
						alert(mensaje5 + '\n'
							+ r.norealizados);
					}
	            }else{
					alert(r.mensaje);
				}
				
				nomb_coment.value= "";
				while (tablaRadi_2.hasChildNodes()) 
						tablaRadi_2.removeChild(tablaRadi_2.firstChild);
				
				
			};
			
			if(Global_EnvioForm){
				var formObject = YAHOO.util.Dom.get('checkExpbox');
				YAHOO.util.Connect.setForm(formObject);				
			}		
			
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
				
		}
	
	
		// FUNCION  
		//NUMERO DE EXPEDIENTE PARA CAMBIAR POR RANGO	
		/*
		*Funcion que cambia el numero del expediente mostrado para
		*seleccionar un rango al cual se activara o inactivaran
		*los expediente.
		*/		
		
		function rang_Expedientes(){
			var nom_depe_2 			= YAHOO.util.Dom.get('nom_depe_2').value,
				ano_busq_2 			= YAHOO.util.Dom.get('ano_busq_2').value,
				selectSerie_2 		= YAHOO.util.Dom.get('selectSerie_2').value,
				selectSubSerie_2 	= zfill(YAHOO.util.Dom.get('selectSubSerie_2').value, 3),
				rang_NombExpe1	 	= YAHOO.util.Dom.get('rang_NombExpe1');
			
			rang_NombExpe1.value = ano_busq_2 + nom_depe_2 + selectSerie_2 + selectSubSerie_2;    
				
		}	
		
		
		// FUNCION
		// MUESTRA LOS EXPEDIENTES EXISTENTES EN EL RAGO QUE SE SELECCIONO
		/*
		 * Funcion que busca los distintos expedientes y muestra la informacion
		 * si estan activos o no, junto a un chexbox de multiple seleccion,
		 * para ser seleccionados y cambiar su estado.
		 */
		
		function buscar_Expediente(){
			
			var exp2				= /^[0-9]{5}/,
				exp3				= /^[0-9]{13}/,
				
				sUrl				= 'libs/adm_nombreTemasExpJ.php',
				depe_2 				= YAHOO.util.Dom.get('nom_depe_2').value,
				ano_busq_2 			= YAHOO.util.Dom.get('ano_busq_2').value,						
				selectSerie_2 		= YAHOO.util.Dom.get('selectSerie_2').value,
				selectSubSerie_2 	= zfill(YAHOO.util.Dom.get('selectSubSerie_2').value, 3),
				rang_NombExpe1		= YAHOO.util.Dom.get('rang_NombExpe1').value,						
				rang_NombExpe2		= YAHOO.util.Dom.get('rang_NombExpe2').value,
				rang_NombExpe4		= YAHOO.util.Dom.get('rang_NombExpe4').value,
				buscarExpActInac	= document.getElementsByName('buscarExpActInac'),
				tablaRadi_2			= YAHOO.util.Dom.get('tablaRadi_2'),
				
				nomb_Expe_search_2 	= YAHOO.util.Dom.get('nomb_Expe_search_2'),				
									  
				mensaje2			= "Rango no es correcto",
				
				postData 			=   GLOBAL_sid 			+ 
										"&evento=" 			+ 11 +										
										"&depe=" 			+ depe_2 +
										"&ano_busq=" 		+ ano_busq_2 +
										"&selectSerie=" 	+ selectSerie_2 +							
										"&selectSubSerie=" 	+ selectSubSerie_2 +
										"&rang_ini="		+ rang_NombExpe2 +
										"&rang_fin="		+ rang_NombExpe4;
			
			
			
			for(var i=0; i< buscarExpActInac.length; i++) {
				if(buscarExpActInac[i].checked == true){
					postData += "&filtro_busq="	+ i; 					
				}			  
			}					
			
			nomb_Expe_search_2.value = '';			
						
			if( !exp2.test(rang_NombExpe2) 
				|| !exp2.test(rang_NombExpe4) 
				|| !exp3.test(rang_NombExpe1)
				|| ((rang_NombExpe4-rang_NombExpe2))< 1){
				
				alert(mensaje2);
				return
			}
			
			var successHandler = function(o) {			
				var r		= eval('(' + o.responseText + ')');
				
				if (r.respuesta == true) {					
					var actu = tablaRadi_2.innerHTML.value;
					//creamos la tabla de forma dinamica
					
					result	 =	"<form  name='checkExpbox' id='checkExpbox'><table border='1px' bordercolor='black' width='100%'>";
					
					result	+=	"<tr>" +
									"<td valign='center' align='center' width='130px'><b>Nro Expediente</b></td>"+
									"<td valign='center' align='center' width='100%'><b>Nombre del Expediente</b></td>"+
									"<td valign='center' align='center' width='120px'><b>Estado</b></td>"+
									"<td valign='center' align='center'><input type='checkbox'  id='checAll'/></td>"+
								"</tr>";
					
					for (var i = r.mensaje.length - 1; i >= 0; i--){
												
						var numExp  = r.mensaje[i].numExp,
							nombExp = r.mensaje[i].nombExp,
							numRad 	= r.mensaje[i].estadoExp;
						
						result	+=	"<tr><td>" 	+ numExp +"</td><td>" +
									"<textarea readonly='READONLY'  class='select_crearExp nombActuExp' rows='2' wrap='soft' class='tex_area2'>" 
									+ nombExp +"</textarea>" +
								    "</td><td>" + numRad + "</td><td>"
									+ "<input type=checkbox name=activRad[] value='"+ numExp + 
									"'></td></tr>";														
					};
					
					result 	+= "</table></form>";
					 
					tablaRadi_2.innerHTML = result;
					
					YAHOO.util.Event.on("checAll","change",							
	                                function(){
										var checkS = document.forms["checkExpbox"].elements;
										
                                        for (var i = 0; i < checkS.length; i++) {
											var elemento = checkS[i];
											if (elemento.type == "checkbox") {
												elemento.checked = YAHOO.util.Dom.get('checAll').checked;
											}
										}
											                                
									});
									
					Global_EnvioForm = true;						
				}
	            else{
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
		}		
	
		//EVENTO
		/*
		 * Manejo de eventos originados por acciones o botones
		 */	
		
		YAHOO.util.Event.on(	"nom_depe_2",
								"change",
								rang_Expedientes);
		
		YAHOO.util.Event.on(	"ano_busq_2",
								"change",
								rang_Expedientes);
		
		YAHOO.util.Event.on(	"selectSerie_2",
								"change",
								sendSerie);
		
		YAHOO.util.Event.on(	"selectSubSerie_2",
								"change",
								rang_Expedientes);
		
		YAHOO.util.Event.on(	"nomb_Expe_search_2",
								"change",
								function(){
									var datos = '';
									YAHOO.util.Dom.get('rang_NombExpe2').value = datos;
									YAHOO.util.Dom.get('rang_NombExpe4').value = datos;
								});
		
		YAHOO.util.Event.on(	"rang_NombExpe2",
								"change",
								function(){									
									YAHOO.util.Dom.get('nomb_Expe_search_2').value = '';
								});
		
		YAHOO.util.Event.on(	"rang_NombExpe4",
								"change",
								function(){									
									YAHOO.util.Dom.get('nomb_Expe_search_2').value = '';
								});
		
		YAHOO.util.Event.on(	"nom_depe_2",
								"change",
								sendDepBusq);	
		
		
		activaExp.on(	"click",		
						acti_Expediente);
		
		inactiExp.on(	"click",		
						acti_Expediente);
		
		buscRad_2.on(	"click",		
						buscar_Expediente);			
								
	});