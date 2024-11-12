/*
 * Activar/Inactivar Expedientes
 * Se utiliza cuando el usuario crea un expediente
 * y el numero con el que lo hace no es el correcto
*/

//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function (){	
	
	//VARIABLES GENERALES GLOBALES
		var	activaExp				= new YAHOO.widget.Button("crearMassExp"),			
			GLOBAL_enviado_conf		= false,
			GLOBAL_depecodi			= depecodi(),			
			GLOBAL_codusua			= codusua(),			
			GLOBAL_sid				= sid(),		
			fecha					= new Date(),
			GLOBAL_dia 				= fecha.getDate(),
			GLOBAL_mes 				= (fecha.getMonth() + 1),
			GLOBAL_ano 				= fecha.getFullYear();
			
		
		document.getElementById("date1").value = GLOBAL_fecha  = GLOBAL_dia
															+ "/"
															+ GLOBAL_mes
															+ "/"
															+  GLOBAL_ano;		
															
		
		// FUNCTION  NUMERACION TEXTAREAS____________________________________
		/**
		* Realiza la numeracion de un campo de texto
		* Este script requiere contenido css que esta al
		* final del archivo orfeo.css
		*/
		//Numeracion para un textarea
		var lineObjOffsetTop = 4;
            
        function createTextAreaWithLines(id){
            var el = document.createElement('DIV');
            var ta = document.getElementById(id);
            ta.parentNode.insertBefore(el, ta);
            el.appendChild(ta);
            
            el.className = 'textAreaWithLines';
            el.style.width = '100%';
            ta.style.position = 'absolute';
            ta.style.left = '25px';
            el.style.height = (ta.offsetHeight + 1) + 'px';
            el.style.overflow = 'hidden';
            el.style.position = 'relative';            
            var lineObj = document.createElement('DIV');
            lineObj.style.position = 'absolute';
            lineObj.style.top = lineObjOffsetTop + 'px';
            lineObj.style.left = '0px';
            lineObj.style.width = '20px';
            el.insertBefore(lineObj, ta);
            lineObj.style.textAlign = 'right';
            lineObj.className = 'lineObj';
            var string = '';
            for (var no = 1; no < 7000; no++) {
                if (string.length > 0) 
                    string = string + '<br>';
                string = string + no;
            }
            
            ta.onkeydown = function(){
                positionLineObj(lineObj, ta);
            };
            ta.onmousedown = function(){
                positionLineObj(lineObj, ta);
            };
            ta.onscroll = function(){
                positionLineObj(lineObj, ta);
            };
            ta.onblur = function(){
                positionLineObj(lineObj, ta);
            };
            ta.onfocus = function(){
                positionLineObj(lineObj, ta);
            };
            ta.onmouseover = function(){
                positionLineObj(lineObj, ta);
            };
            lineObj.innerHTML = string;
            
        }
            
            function positionLineObj(obj, ta){
                obj.style.top = (ta.scrollTop * -1 + lineObjOffsetTop) + 'px';          
                
            }
		
		
	
		

		//FUNCION  
		//ENVIAR DEPENDENCIA PARA OPTENER SERIE	Y RESPONSABLE
		/*
		*Funcion que cambia el valor de la dependencia para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendDepBusq(e){
			var depeInput 	= YAHOO.util.Dom.get("nom_depe_4").value,
				selSer		= YAHOO.util.Dom.get("selectSerie_4"),
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_4"),
				selectRes_4	= YAHOO.util.Dom.get("selectResponsable_4"),				
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 6									 									
							  + '&depeInput='   + depeInput
							  + '&veractivos='  + veractivos();				
			
			selSer.options.length=0;			
			selSer.options[0] 		= new Option('Seleccione una Serie',0,true,true);
							
			selSubSer.options.length=0;			
			selSubSer.options[0] 	= new Option('Seleccione una subSerie',0,true,true);			
			
			selectRes_4.options.length=0;			
			selectRes_4.options[0] 	= new Option('Seleccione un Responsable',0,true,true);			
			
			if(depeInput != 0){				 
						
				var successHandler = function(o) {			
					var r		= eval('(' + o.responseText + ')');
					if (r.respuesta == true) {					
						var lenSuv = r.mensaje.length;
						var usuari = r.usuario.length;
											
						for (i = 0; i < lenSuv; i++) {
							var j = selSer.options.length;																	
							selSer.options[j] = 
								new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
						}											
						for (i = 0; i < usuari; i++) {
							var j = selectRes_4.options.length;																	
							selectRes_4.options[j] = 
								new Option(r.usuario[i].nombre,r.usuario[i].codigo,false,false);
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
		// ENVIAR SERIE PARA OPTENER SUBSERIE	
		/*
		*Funcion que cambia el valor de la serie para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendSerie(e){
			var serieInput 	= YAHOO.util.Dom.get("selectSerie_4").value,
				depeInput	= YAHOO.util.Dom.get("nom_depe_4").value,
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie_4"),
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
				
		// FUNCTION  SECUENCIA EXPEDIENTE____________________________
		//Funcion para obtener el numero de secuencia del expedientes
		//Esta se invoca despues de que seleccionamos la subserie.
		
		function getSecExpediente(){
			debugger;
			var mensaje1			= "Formato de la fecha incorrecto",
				mensaje2			= "El numero del rango No es numérico",
				sUrl				= 'libs/crearExpedientesj.php',
				ano_busq_4			= YAHOO.util.Dom.get('ano_busq_4').value,
				nom_depe_4 			= YAHOO.util.Dom.get('nom_depe_4').value,
				selectSerie_4 		= YAHOO.util.Dom.get('selectSerie_4').value,
				selectSubSerie_4 	= YAHOO.util.Dom.get('selectSubSerie_4').value,				
				rang_ExpeCrear 		= parseInt(YAHOO.util.Dom.get('rang_ExpeCrear').value),
				rang_CrearExpe1	 	= YAHOO.util.Dom.get('rang_CrearExpe1'),
				rang_CrearExpe2	 	= YAHOO.util.Dom.get('rang_CrearExpe2'),
				expFin				= '00000',
				secuencia			= '00000',
				postData 			=	GLOBAL_sid +
										"&evento=" 			+ 2 					+
										"&depeCodiUsua="	+ nom_depe_4		 	+
										"&selectSerie=" 	+ selectSerie_4			+		
										"&selectSubSerie=" 	+ selectSubSerie_4		+
										"&numExp_Ano=" 		+ ano_busq_4;
					
			var successHandler = function(o) {		
				debugger;		
				var r		= eval('(' + o.responseText + ')');	
			    if(r.respuesta == true){			    	
			    	secuencia = r.mensaje;					
					
					if((selectSubSerie_4 != 0) && (typeof(rang_ExpeCrear) == 'number') && (rang_ExpeCrear > 0)){
						if(rang_ExpeCrear == 1){
							expFin	= secuencia;
						}else{
							expFin	= parseInt(secuencia, 10) + parseInt(rang_ExpeCrear, 10) - 1;							
							while (expFin.toString().length < 5) {
								expFin = '0' + expFin.toString();								
							}																								
						}				
					}else{
						expFin	= secuencia;				
					}
					
					selectSubSerie_4 = zfill(selectSubSerie_4, 3);
					nom_depe_4 = zfill(nom_depe_4, 4);
					rang_CrearExpe2.value = ano_busq_4 + 
											nom_depe_4 + 
											selectSerie_4 + 
											selectSubSerie_4 + 
											expFin + 
											'E';	
					
					rang_CrearExpe1.value = ano_busq_4 + 
											nom_depe_4 + 
											selectSerie_4 + 
											selectSubSerie_4 + 
											secuencia + 
											'E';									
				}else{
					alert(r.mensaje);
				}
			};
					
			var failureHandler = function(o) {
				alert("Error retornando datos de secuencia del expediente." 
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
		// VALIDAR NOMBRES DE LOS EXPEDIENTES
		/*
		* Verifica que los nombres que el usuario digita
		* sean correctos y que corresponadan a la cantidad
		* de expedientes a crear
		*/
		
		function valdNombres(e){
			var mensaje1		= 'No digito un rando correcto',
				mensaje2		= 'El campo de nombres no es correcto',
				mensaje3		= 'Estos nombres no son correctos \n LINEAS: ',
				mensaje4		= 'la cantidad de nombres y de expedientes no son iguales',				
				nombs_Exp_4 	= YAHOO.util.Dom.get('nombs_Exp_4'),	
				rang_ExpeCrear 	= YAHOO.util.Dom.get('rang_ExpeCrear').value,
				texto			= '',				
				frases			= new Array(),
				nombOk			= new Array(),
				salidaRes		= '';
				
			texto 				= nombs_Exp_4.value.replace(/\n/g, '');
			nombs_Exp_4.value	= texto.replace(/;/g, ';\n');
			
			if(nombs_Exp_4.value.length < 4){
				alert(mensaje2);
				return 'error';				
			}	
			
			if ((rang_ExpeCrear == '') && (typeof(rang_ExpeCrear) != 'number') && 
				(rang_ExpeCrear < 1)) {				
					alert(mensaje1);
					return 'error';
			}else{
				
				frases = nombs_Exp_4.value.split(";");			
				
				for (x in frases){
					var frase	= frases[x].toLowerCase();					  
					if (frase.lenght < 4){
						var res = parseInt(x,10) + 1;						
					  	salidaRes += (salidaRes.length == 0)? res: ',' + res ;
					}										
					nombOk[nombOk.length] = frase; 	
				}
								
				if(salidaRes.length > 0){
					alert(mensaje3 + '\n ' + salidaRes);
					return 'error';
				}
								
				if (rang_ExpeCrear != nombOk.length) {
					alert(mensaje4);
					return 'error';
				}		
				
			}
		}
	
	
		// FUNCION  
		// CREAR EXPEDIENTES MASIVAMENTE
		/*
		* Verifica que los datos esten completos antes de ser
		* enviados al archivo que los procesa
		*/	
		
		function crearMassExp(){
			
			var mensaje1			= 'Parametros incorrectos, faltan datos',
				Dom 				= YAHOO.util.Dom,
				element2			= Dom.get('sonico'),
				selectSubSerie_4 	= Dom.get('selectSubSerie_4').value,
				selectResponsable_4	= Dom.get('selectResponsable_4').value,
				rang_ExpeCrear		= Dom.get('rang_ExpeCrear').value,
				nombs_Exp_4			= Dom.get('nombs_Exp_4').value;
				sUrl				= 'libs/crearExpeMass.php';
			
			if(valdNombres() == 'error'){
				return 'error';
			} 
			
			if((selectSubSerie_4 == 0) || (selectResponsable_4 == 0)){
				alert(mensaje1);
				return 'error';	
				
			}else{	
				
				var globalEvents = {
					start:function(o){
						element2.style.visibility = 'visible';//Esto es para que oculte la mugre de ie
						Dom.removeClass(element2, 'yui-hidden2');				
					},
		
					complete:function(o){
						element2.style.visibility = 'hidden'; //Esto es para que oculte la mugre de ie
						Dom.addClass(element2, 'yui-hidden2');
					}
				};				
				
				Dom.get("crearMassExp").value = "Enviando...";
    			Dom.get("crearMassExp").disabled = true;		
				
				var callback = {					
							
					success: function(o) {		
						var r		= eval('(' + o.responseText + ')');
						
						if (r.respuesta == true) {
							alert(r.mensaje);												
			            }else{
							alert(r.mensaje);
						}		
						Dom.get("crearMassExp").value = "Crear";
    					Dom.get("crearMassExp").disabled = false;				
					},
											
					failure: function(o) {
						alert("Error retornando datos" 
								+ o.status + " : " + o.statusText);
					}
				};	
				
				//event
				YAHOO.util.Connect.startEvent.subscribe(globalEvents.start);
				YAHOO.util.Connect.completeEvent.subscribe(globalEvents.complete);
				
				YAHOO.util.Connect.setForm('masiva');
					
				var transaction = YAHOO.util.Connect.asyncRequest(
							"POST"
							, sUrl
							, callback);
			}
		}	
			
	
		//EVENTO		
		/*
		 * Manejo de eventos originados por acciones o botones
		 */	
		
		YAHOO.util.Event.on(	"rang_NombExpe1",
								"change",
								function(){									
									Dom.get('nomb_Expe_search_2').value = '';
								});
			
				
		/*
		* Manejo de eventos originados por acciones de los select
		* para cambiar el numero del expediente
		*/	
		
		createTextAreaWithLines('nombs_Exp_4');
		
		YAHOO.util.Event.on(	"selectSubSerie_4",
								"change",
								getSecExpediente);
		
		YAHOO.util.Event.on(	"rang_ExpeCrear",
								"change",
								getSecExpediente);
		
		YAHOO.util.Event.on(	"nombs_Exp_4",
								"change",
								valdNombres);		
		
		YAHOO.util.Event.on(	"nom_depe_4",
								"change",
								sendDepBusq); 
		
		YAHOO.util.Event.on(	"selectSerie_4",
								"change",
								sendSerie);
		
		YAHOO.util.Event.on(	"ano_busq_4",
								"change",
								sendDepBusq);
																
		activaExp.on(	"click",		
						crearMassExp);	
	});