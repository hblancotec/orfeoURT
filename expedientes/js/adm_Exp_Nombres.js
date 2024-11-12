/*
 * Archivo para la administración de expedientes
 * Se manejan los eventos para incluir modificar 
 * Permisos etc. relacionados con expedientes.
 * Este archivo esta relacionado con:
 * adm_Expedientes.tpl =>Interfaz de usuario
 * adm_Expedientes.php =>Lógica de inicio de la aplicación
 * adm_nombreTemasExpj.php	=>Realiza las consultas y procesos solicitados por este archivo
*/


//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function () {	
	//VARIABLES GENERALES GLOBALES
		var	GLOBAL_select_depen 	= select_depen(),
			GLOBAL_codusua			= codusua(),
			GLOBAL_depecodi 		= depecodi(),			
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
		var autoNomb = new YAHOO.widget.AutoComplete("nomb_Expe_search", "contnExpSearch", search);
		
		// Tamaño maximo a mostrar de resultados
		autoNomb.maxResultsDisplayed = 30;
		
		//funcion para cuando se seleccione un item
	    var myHandler = function(sType, aArgs) {
				        
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
								
			ano_busq 		= YAHOO.util.Dom.get('ano_busq').value;
			selectSerie 	= YAHOO.util.Dom.get('selectSerie').value;	
			selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie').value;				
			
			depe = (GLOBAL_select_depen == 'true')? YAHOO.util.Dom.get('nom_depe').value : GLOBAL_depecodi;
			
			return 			GLOBAL_sid			+
							"&evento=1"			+
							"&query=" 			+ sQuery +
							"&depe=" 			+ depe +
							"&depeInput=" 		+ depe +
							"&ano_busq=" 		+ ano_busq +
							"&selectSerie=" 	+ selectSerie +							
							"&selectSubSerie=" 	+ selectSubSerie;
		};
		
		return {
			search: search,
			autoNomb: autoNomb
		};
		
		//Evento cuando selecciona un item
		autoNomb.itemSelectEvent.subscribe(myHandler); 
		
	})();


	// FUNCION  
	//ENVIAR DEPENDENCIA PARA OPTENER SERIE	
	/*
	*Funcion que cambia el valor de la dependencia para ser enviada en
	*el siguiete input y generar combobox en cascada
	*/
	
	function sendDepBusq(e){
		debugger;
		var selSer		= YAHOO.util.Dom.get("selectSerie"),
			selSubSer	= YAHOO.util.Dom.get("selectSubSerie"),				
			sUrl		= 'libs/crearExpedientesj.php',	
			postData 	= GLOBAL_sid
						  + '&evento=' + 6
						  + '&depe='   + GLOBAL_depecodi
						  + '&veractivos='   + veractivos();
						  				  	
						  
		depe = (GLOBAL_select_depen == 'true')? YAHOO.util.Event.getTarget(e).value : GLOBAL_depecodi;		
		postData 		+= '&depeInput='   + depe;
		
		selSer.options.length=0			
		selSer.options[0] = new Option('Seleccione una Serie',0,true,true)
						
		selSubSer.options.length=0			
		selSubSer.options[0] = new Option('Seleccione una subSerie',0,true,true)					 
					
		var successHandler = function(o) {	
			debugger;		
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
	}		



	// FUNCION  
	//ENVIAR SERIE PARA OPTENER SUBSERIE	
	/*
	*Funcion que cambia el valor de la serie para ser enviada en
	*el siguiete input y generar combobox en cascada
	*/
	
	function sendSerie(e){
		var serieInput 	= YAHOO.util.Event.getTarget(e).value,			
			selSubSer	= YAHOO.util.Dom.get("selectSubSerie"),
			sUrl		= 'libs/crearExpedientesj.php',	
			postData 	= GLOBAL_sid
						  + '&evento=' 		+ 1									 									
						  + '&selectSerie=' + serieInput;						  
						  	
		depe = (GLOBAL_select_depen == 'true')? YAHOO.util.Dom.get('nom_depe').value : GLOBAL_depecodi;		
		postData 		+= '&depeInput='   + depe;			
						
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
	//CAMBIAR NOMBRE DEL EXPEDIENTE
	/*
	 * Se envia las mismas variable que se utilizaron para 
	 * realizar la busqueda y se verifica si existe o no 
	 * se puede realizar el cambio
	 */
	
	function mod_nomExpediente(){
		debugger;
		var 
			exp				= /^[0-9]{16,19}[E]{1}/,
			ano_busq 		= YAHOO.util.Dom.get('ano_busq').value,
			selectSerie 	= YAHOO.util.Dom.get('selectSerie').value,
			selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie').value,
			text_NomExp		= YAHOO.util.Dom.get("nomb_Expe_search").value,
			nomb_Expe_300	= YAHOO.util.Dom.get("nomb_Expe_300").value,			
			mensaje2		= "El campo para el nuevo nombre esta sin datos o \n" +
							  "el numero del expediente no es correcto",
			no_exp			= exp.exec(text_NomExp),
			
			sUrl			= 'libs/adm_nombreTemasExpJ.php',
			depe 			= (GLOBAL_select_depen == 'true')? 
									YAHOO.util.Dom.get('nom_depe').value : GLOBAL_depecodi,
			
			postData 		= 	GLOBAL_sid			+
								"&evento=" 			+ 2 + 
								"&depe=" 			+ depe +
								"&ano_busq=" 		+ ano_busq +
								"&selectSerie=" 	+ selectSerie +
								"&selectSubSerie=" 	+ selectSubSerie +							
								"&query="			+ text_NomExp +	
								"&depeCodiUsua="	+ GLOBAL_depecodi +								
								"&codUsua="			+ GLOBAL_codusua +
									
								"&nomb_Expe_300="	+ nomb_Expe_300;			
		
		
		if((nomb_Expe_300 != '') && exp.test(no_exp)){
			var callback = {
						success: function(o) {
									debugger;
									var r		= eval('(' + o.responseText + ')');
									if (r.respuesta == true){	
										var renTex = YAHOO.util.Dom.get("nombActuExp");
										while (renTex.hasChildNodes()) 
											renTex.removeChild(renTex.firstChild);
										text_NomExp	= YAHOO.util.Dom.get("nomb_Expe_300").value;
										YAHOO.util.Dom.get("nomb_Expe_300").value = '';								
										
										alert(r.mensaje + ' ' + text_NomExp );				                						
						            }else{
										alert(r.mensaje);
									}							
								},
		  				
						failure: function(o) {
									alert("Error " + o.status + " : " + o.statusText)
								}
				};
							
			var transaction = YAHOO.util.Connect.asyncRequest(
							"POST"
							, sUrl
							, callback
							, postData);			
		}else{
			alert(mensaje2);
		}	
	};





	//FUNCION
	//BUSCAR NOMBRE DEL EXPEDIENTE CON EL NUMERO
	/*
	 * Si el usuario digita o pega el nuemro del expediente
	 * se debe buscar el nombre que le corresponde
	 * respetando los filtros seleccionados
	 */
	
	function buscNombExpNumero(){
		debugger;
		var exp				= /^[0-9]{16,19}[E]{1}/,
			ano_busq 		= YAHOO.util.Dom.get('ano_busq').value,			
			text_NomExp		= YAHOO.util.Dom.get("nomb_Expe_search").value,						
			mensaje2		= "El numero del expediente no es correcto",
			no_exp			= exp.exec(text_NomExp),
			
			sUrl			= 'libs/adm_nombreTemasExpJ.php',
			depe 			= (GLOBAL_select_depen == 'true')? 
									YAHOO.util.Dom.get('nom_depe').value : GLOBAL_depecodi,
			
			postData 		= 	GLOBAL_sid			+
								"&evento=" 			+ 6 + 
								"&depe=" 			+ depe +
								"&ano_busq=" 		+ ano_busq +						
								"&query="			+ text_NomExp;											
		
		
		if(exp.test(no_exp)){
			var callback = {
						success: function(o) {
									debugger;
									var r		= eval('(' + o.responseText + ')');
									if (r.respuesta == true){
									
										var renTex = YAHOO.util.Dom.get("nombActuExp");
										
										while (renTex.hasChildNodes()) 
											renTex.removeChild(renTex.firstChild);			
																							
										texto1 		= document.createTextNode(r.mensaje);			
										renTex.appendChild(texto1);
														                						
						            }else{
										alert(r.mensaje);
									}							
								},
		  				
						failure: function(o) {
									alert("Error " + o.status + " : " + o.statusText)
								}
				};
							
			var transaction = YAHOO.util.Connect.asyncRequest(
							"POST"
							, sUrl
							, callback
							, postData);			
		}else{
			alert(mensaje2);
		}	
	};




	//FUNCION
	//CAMBIAR NOMBRES DE PROYECTOS__________________________________________
	/*
	 * Desde la funcion mod_proyecto podemos cambiar, modificar,borrar y crear
	 * nombres de proyectos para los expedientes.
	 * Se crean dos nombres, uno largo y uno corto para que pudan ser reconocidos
	 * por las diferentes dependencias.
	 * 
	 */
	
	if(mosProy()=='true'){	
	
	    var mod_proyecto = function(e){
					
			var salida 			= YAHOO.util.Event.getTarget(e).id,					   			
				sUrl			= 'libs/adm_nombreTemasExpJ.php',			
				postData 		= GLOBAL_sid,			
				nomb_proyecto1 	= YAHOO.util.Dom.get('nomb_proyecto1'),
				nomb_proyecto2 	= YAHOO.util.Dom.get('nomb_proyecto2'),
				nomb_anterior 	= YAHOO.util.Dom.get("selectProyecto"),
				mensaje1		= 'Los campos estan mal diligenciados',					
				codigo_nomAnt	= nomb_anterior.value;				
				
			switch (salida) {
				
	            case 'crear_proy-button':
				
					if(	nomb_proyecto1.value == '' || nomb_proyecto2.value == ''){
						nomb_proyecto1.focus();
			       		alert(mensaje1);
			       		return false    //no submit
			    	}					 
					  
					nombre1		= nomb_proyecto1.value.toUpperCase();
					nombre2		= nomb_proyecto2.value.toUpperCase();						
	                postData 	+= '&evento=' + 3 + 
							   	   '&nomb_proyecto1=' + nombre1 +
								   '&nomb_proyecto2=' + nombre2 ; 
	                break;
					
	            case 'modificar_proy-button':
		            if (codigo_nomAnt == 0 || nomb_proyecto1.value == '' || nomb_proyecto2.value == '') {
		                alert(mensaje1);
		                return; //no submit
		            }	
					
					nombre1		= nomb_proyecto1.value.toUpperCase();
					nombre2		= nomb_proyecto2.value.toUpperCase();											
	                postData 	+= '&evento=' + 4 +
								   '&nomb_proyecto1=' + nombre1 +
								   '&nomb_proyecto2=' + nombre2 +
								   '&nomb_anterior=' + codigo_nomAnt;				
	                break;
					
	            case 'borrar_proy-button':
					if (codigo_nomAnt == 0) {
		                alert(mensaje1);
		                return; //no submit
		            }								
	                postData 	+= '&evento=' + 5 +
								   '&nomb_anterior=' + codigo_nomAnt;
	                break;				
	        }	
					
			var successHandler = function(o) {			
				var r		= eval('(' + o.responseText + ')');
				if (r.respuesta == true){
					alert(r.mensaje);				                						
	            }else{
					alert(r.mensaje);
				}
			};
					
			var failureHandler = function(o) {
				alert("Error " + o.status + " : " + o.statusText);
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
		
		//crear botones para proyecto
		var crearButton 	= new YAHOO.widget.Button("crear_proy");
		var modificarButton = new YAHOO.widget.Button("modificar_proy");
		var borrarButton 	= new YAHOO.widget.Button("borrar_proy");		
		
		crearButton.on(		"click"		,mod_proyecto);		
		modificarButton.on(	"click"		,mod_proyecto);
		borrarButton.on(	"click"		,mod_proyecto);		
	}
	
	
	//EVENTO
	/*
	 * Manejo de eventos originados por acciones o botones
	 */	
	 
	if(GLOBAL_select_depen == 'true'){
		YAHOO.util.Event.on( "nom_depe",
							 "change",
							 sendDepBusq);		
	}
	
	
	YAHOO.util.Event.on(	"selectSerie",
							"change",
							sendSerie);
							
	var		myTabs 					= new YAHOO.widget.TabView("tapsExp"),
			cambNomExp				= new YAHOO.widget.Button("cambNomExp"),
			buscNomExp				= new YAHOO.widget.Button("buscNomExp");		
	
	cambNomExp.on(	"click",		
					mod_nomExpediente);
	
	buscNomExp.on(	"click",		
					buscNombExpNumero);
					
						
	});