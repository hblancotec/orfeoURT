/*
 * Archivo para la administración de expedientes
 * Se manejan los eventos para incluir modificar 
 * Permisos etc. relacionados con expedientes.
 * Este archivo esta relacionado con:
 * incluirEnExpediente.tpl =>Interfaz de usuario
 * incluirEnExpediente.php =>Lógica de inicio de la aplicación
 * incluirEnExpedientej.php	=>Realiza las consultas y procesos solicitados por este archivo
 * /adm_nombreTemasExpJ.php => Maneja consultas ajax
*/


//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function () {	
	
		//VARIABLES GENERALES GLOBALES
		var	incluirRadExpe			= new YAHOO.widget.Button("incluirRadExp"),	
			buscNomExp				= new YAHOO.widget.Button("buscNomExp"),
			cerrarVentana			= new YAHOO.widget.Button("cerrarVentana"),												  
			GLOBAL_rad_Arbol 		= arrayArbol(),	
			GLOBAL_depecodi			= depecodi(),			
			GLOBAL_codusua			= codusua(),	
			GLOBAL_numRad			= numRad(),		
			GLOBAL_usuadoc			= usuadoc(),
			GLOBAL_sal_Radic		= [],			
			GLOBAL_sid				= sid();		
		
		// FUNCTION MOSTRAR ARBOL DE RADICADOS ANEXOS PARA ASOCIAR_____________________________
		/**
		*Se genera la funcion para crear los arboles de radicados
		*para ser asociados, Se leen los datos enviados desde el archivo
		*php a consultar que en este caso ysearch_Arbol_Exp.php.
		*Al resultado tambien se le aplica formato para generar los
		*checkbox
		*/
		
		if(mosArbol()=='true'){
			var tree;
			var nodes = [];
			var nodeIndex;
			GLOBAL_rad_Arbol = arrayArbol();
							
			function treeInit() {
				buildRandomTaskNodeTree();
			}
	
			//handler for expanding all nodes
			YAHOO.util.Event.on("expand", "click", function(e) {
				tree.expandAll();
				YAHOO.util.Event.preventDefault(e);
			});
	
			//handler for collapsing all nodes
			YAHOO.util.Event.on("collapse", "click", function(e) {
				tree.collapseAll();
				YAHOO.util.Event.preventDefault(e);
			});
	
			//handler for checking all nodes
			YAHOO.util.Event.on("check", "click", function(e) {
				checkAll();
				GLOBAL_sal_Radic = getCheckedNodes();
				YAHOO.util.Event.preventDefault(e);
			});
	
			//handler for unchecking all nodes
			YAHOO.util.Event.on("uncheck", "click", function(e) {
				uncheckAll();
				GLOBAL_sal_Radic = getCheckedNodes();
				YAHOO.util.Event.preventDefault(e);
	
			});
	
			//Function  creates the tree
		    function buildRandomTaskNodeTree() {
	
				//instantiate the tree:
		        tree = new YAHOO.widget.TreeView("treeDiv1");
		        var root = tree.getRoot();
	
				//se utiliza la variable global para mostrar los
				//radicados que pueden ser incluidos en el expediente
		        for (var i = 0; i < GLOBAL_rad_Arbol.length; i++) {
	
		        	var myobj = { label: "Radicado No: "
		            				+ GLOBAL_rad_Arbol[i].Radicado
		            				+ " Asunto: "
		            				+ GLOBAL_rad_Arbol[i].Asunto
		            			, myNodeId: GLOBAL_rad_Arbol[i].Radicado} ;
		            var tmpNode = new YAHOO.widget.TaskNode(
		            				myobj , root, false);
		            // tmpNode.collapse();
		            // tmpNode.expand();
		            buildRandomTaskBranch(tmpNode, GLOBAL_rad_Arbol[i]);
		        }
	
		       // Trees with TaskNodes will fire an event for when a check box is clicked
		       tree.subscribe("checkClick", function(node) {
		       		GLOBAL_sal_Radic = getCheckedNodes();
		        });
	
				//The tree is not created in the DOM until this method is called:
		        tree.draw();
		    }
	
			var callback = null;
	
			function buildRandomTaskBranch(node, rama) {
				if (rama.hijos != null) {
					for ( var f = 0; f < rama.hijos.length; f++ ) {
						var myobj = {label: "Radicado No: "
									+ rama.hijos[f].Radicado
									+ " Asunto: "
									+ rama.hijos[f].Asunto
								,myNodeId: rama.hijos[f].Radicado};
	
						var tmpNode = new YAHOO.widget.TaskNode(
									myobj, node, false);
			                //tmpNode.onCheckClick = onCheckClick;
					}
				}
			}
	
		    function checkAll() {
		        var topNodes = tree.getRoot().children;
		        for(var i=0; i<topNodes.length; ++i) {
		            topNodes[i].check();
		        }
		    }
	
		    function uncheckAll() {
		        var topNodes = tree.getRoot().children;
		        for(var i=0; i<topNodes.length; ++i) {
		            topNodes[i].uncheck();
		        }
		    }
	
		   // Gets the labels of all of the fully checked nodes
		   // Could be updated to only return checked leaf nodes by evaluating
		   // the children collection first.
	
		    function getCheckedNodes(nodes) {
		        nodes = nodes || tree.getRoot().children;
		        checkedNodes = [];
		        for(var i=0, l=nodes.length; i<l; i=i+1) {
		            var n = nodes[i];
		            if (n.checkState > 0) { // if we were interested in the nodes that have some but not all children checked
		            //if (n.checkState === 2) {
		                checkedNodes.push(n.data.myNodeId); // just using label for simplicity
		            }
	
		            if (n.hasChildren()) {
						checkedNodes = checkedNodes.concat(getCheckedNodes(n.children));
		            }
		        }
	
		        return checkedNodes;
		    }
			treeInit();
		};


		// FUNCION  
		//ENVIAR DEPENDENCIA PARA OPTENER SERIE	
		/*
		*Funcion que cambia el valor de la dependencia para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendDepBusq(e){
			var depeInput 	= YAHOO.util.Event.getTarget(e).value,
				selSer		= YAHOO.util.Dom.get("selectSerie"),
				selSubSer	= YAHOO.util.Dom.get("selectSubSerie"),
				sUrl		= 'libs/crearExpedientesj.php',	
				postData 	= GLOBAL_sid
							  + '&evento=' 		+ 6									 									
							  + '&depeInput='   + depeInput
							  + '&veractivos='  + veractivos();	  
			
			selSer.options.length=0			
			selSer.options[0] = new Option('Seleccione una Serie',0,true,true)
							
			selSubSer.options.length=0			
			selSubSer.options[0] = new Option('Seleccione una subSerie',-1,true,true)
			
			YAHOO.util.Dom.get("nomb_Expe_search").value = '';
			
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
				
				vaciarTipDocs();
						
				var failureHandler = function(o) {
					alert("Error retornando datos de Serie " + o.status + " : " + o.statusText);
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

		// FUNCION  
		//ENVIAR SERIE PARA OBTENER SUBSERIE	
		/*
		*Funcion que cambia el valor de la serie para ser enviada en
		*el siguiete input y generar combobox en cascada
		*/
		
		function sendSerie(e){
			debugger;
			var serieInput 	= document.getElementById("selectSerie").value; //YAHOO.util.Event.getTarget(e).value,
			var	depeInput	= YAHOO.util.Dom.get("nom_depe").value;
			var	selSubSer	= YAHOO.util.Dom.get("selectSubSerie");
			//var selSubSer	= document.getElementById("selectSubSerie");			
			var subSerActu	= document.getElementById("subSerActu").value;
			var sUrl		= 'libs/crearExpedientesj.php';
			var	postData 	= GLOBAL_sid
							  + '&evento=' 		+ 1									 									
							  + '&selectSerie=' + serieInput 
							  + '&depeInput='   + depeInput
							  + '&veractivos='  + veractivos();							  
							
			selSubSer.options.length=0
			selSubSer.options[0] = new Option('Seleccione una subSerie',-1,true,true)
			
			YAHOO.util.Dom.get("nomb_Expe_search").value = '';
			//Borramos el contenido del listado de tipos documentales.
			vaciarTipDocs();
			if(serieInput != 0){				 
						
				var successHandler = function(o) {			
					var r		= eval('(' + o.responseText + ')');
					if (r.respuesta == true) {					
						var lenSuv = r.mensaje.length;					
						for (i = 0; i < lenSuv; i++) {
							var j = selSubSer.options.length;
							if (subSerActu == r.mensaje[i].codigo) { 
								selSubSer.options[j] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,true);
							} else {
								selSubSer.options[j] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
							}
						}
						
						if (subSerActu != -1) {
					   		sendSubSerie(document.getElementById("selectSubSerie"));
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
			debugger;
			var subserieInput 	= document.getElementById("selectSubSerie").value; //YAHOO.util.Event.getTarget(e).value,
			var	anhoBusq	= YAHOO.util.Dom.get("ano_busq").value;
			var	depeInput	= YAHOO.util.Dom.get("nom_depe").value;
			var	selSerie	= YAHOO.util.Dom.get("selectSerie").value;
			var	selTipDoc	= YAHOO.util.Dom.get("selectTipoDocumental");
			var docuActu	= document.getElementById("docuActu").value;
			var	sUrl		= 'libs/crearExpedientesj.php';
			var	postData 	= GLOBAL_sid
							  + '&evento=' 		+ 8	
							  + '&selectSerie=' + selSerie 							  
							  + '&selectSubSerie=' + subserieInput 
							  + '&depeInput='   + depeInput
							  + '&veractivos='  + veractivos();			
			
			var selTipDoc2 = null;	
			if (YAHOO.util.Dom.get("selectTipoDocumental2"))
			{
				selTipDoc2 = YAHOO.util.Dom.get("selectTipoDocumental2");
				selTipDoc2.options.length=0;
				selTipDoc2.options[0] = new Option('Seleccione un Tipo Documental',0,true,true);	
			}			
			
			vaciarTipDocs();
			YAHOO.util.Dom.get("nomb_Expe_search").value = '';
			selTipDoc.options.length=0;
			selTipDoc.options[0] = new Option('Seleccione un Tipo Documental',0,true,true);					   							 
			
			if(subserieInput != -1){
						
				var successHandler = function(o) {			
					var r		= eval('(' + o.responseText + ')');
					if (r.respuesta == true) {					
						var lenSuv = r.mensaje.length;					
						for (i = 0; i < lenSuv; i++) {
							var j = selTipDoc.options.length;
							if (docuActu == r.mensaje[i].codigo) {
								selTipDoc.options[j] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,true);
							} else {
								selTipDoc.options[j] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
							}
							if (selTipDoc2 != null) {
								var y = selTipDoc2.options.length;	
								//if (docuActu == r.mensaje[i].codigo) {
								//	selTipDoc2.options[y] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,true);
								//} else {
									selTipDoc2.options[y] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
								//}
							}
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
		
		// FUNCION  
		//SELECCIONAR TIPO DOCUMENTAL PARA CLASIFICACION DE RADICADOS DE SALIDA	
		/*
		*Funcion que cambia el valor del Tipo Documental para clasificar el 
		*anexo de salida.
		*/
		
		function sendTipoDoc2(e){
			
			var tipoDocInput 	= YAHOO.util.Event.getTarget(e).value,
				anhoBusq		= YAHOO.util.Dom.get("ano_busq").value,
				depeInput		= YAHOO.util.Dom.get("nom_depe").value,
				selSerie		= YAHOO.util.Dom.get("selectSerie").value,
				selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie').value,
				selTipDoc		= YAHOO.util.Dom.get("selectTipoDocumental"),
				selTipDoc2		= YAHOO.util.Dom.get("selectTipoDocumental2"),
				sUrl			= 'libs/crearExpedientesj.php',	
				postData 		= GLOBAL_sid
							  + '&evento=' 		+ 9	
							  + '&depeCodiUsua='	+ GLOBAL_depecodi
							  + '&codUsua='			+ GLOBAL_codusua 
							  + '&docUsua='			+ GLOBAL_usuadoc	
							  + '&selectSerie=' + selSerie 							  
							  + '&selectSubSerie=' + selectSubSerie 
							  + '&rad_anex=' + GLOBAL_sal_Radic
							  + '&depeInput='   + depeInput
							  + '&tipoDoc=' + selTipDoc2.value;
			
			//alert(GLOBAL_sal_Radic);
			if (GLOBAL_sal_Radic != "") {
				if (confirm("Desea asignar el tipo documental " + selTipDoc2.value + " a los radicados: " + GLOBAL_sal_Radic + " ?") == true) {
					
					var callback = {
							success: function(o) {
										debugger;
										var resul		= eval('(' + o.responseText + ')');
										if(resul.respuesta == true){
											alert(resul.mensaje);
											//opener.regresar();
											//self.close();
										}else{											
											alert(resul.mensaje);
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
				
				}
			} else {
				alert('Debe seleccionar los radicados de salida !!');
				selTipDoc2.value = 0;
			}
		}

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
			autoNomb.maxResultsDisplayed = 25;
			
			// Set Request change de values to send
			autoNomb.generateRequest = function(sQuery){				
				var depe,
					ano_busq,
					selectSubSerie,					
					selectSerie;
									
				ano_busq 		= YAHOO.util.Dom.get('ano_busq').value;
				selectSerie 	= YAHOO.util.Dom.get('selectSerie').value;	
				selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie').value;				
				
				depe = YAHOO.util.Dom.get('nom_depe').value;
				
				return 			GLOBAL_sid			+
								"&evento=1"			+
								"&query=" 			+ sQuery +
								"&depe=" 			+ depe +
								"&ano_busq=" 		+ ano_busq +
								"&selectSerie=" 	+ selectSerie +							
								"&selectSubSerie=" 	+ selectSubSerie;
			};
			
			return {
				search: search,
				autoNomb: autoNomb
			};
		})();
		



		//FUNCION
		//INSERTAR RADICADOS EN EXPEDIENTE
		/*
		 * Se envian los radicados seleccionados
		 * al numero de expediente que se escribio o
		 .
		 * selecciono mediante el autocompletar
		 */

		function incluirRadExp()
		{
			debugger;
			var serie =	document.getElementById("selectSerie").value;
			var subserie = document.getElementById("selectSubSerie").value;
			var tdocu =	document.getElementById("selectTipoDocumental").value;
			var numrad = document.getElementById("numrad").value;
			var tiporad = numrad.toString().substr(-1);
			
			var seractu = document.getElementById("serieActu").value;
			var subseractu = document.getElementById("subSerActu").value;
	    	var cambio = document.getElementById("cambio").value;
	    	var tdocactu = document.getElementById("docuActu").value;
	    	var retipifica = document.getElementById("retipifica").value;
	    	var usuModifica1 = document.getElementById("usuaModifica1").value;
			var usuModifica2 = document.getElementById("usuaModifica2").value;
			var usactu = document.getElementById("codusua").value;
			var depactu = document.getElementById("depecodi").value;
			var pqr = document.getElementById("radPqr").value;
			
			var exp				= /^[0-9]{17,19}[E]{1}/,				
				text_NomExp		= YAHOO.util.Dom.get("nomb_Expe_search").value,
				no_exp			= exp.exec(text_NomExp),
				selectSerie 	= YAHOO.util.Dom.get('selectSerie').value,
				selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie').value,
				ano             = YAHOO.util.Dom.get('ano_busq').value,
				tipodoc         = YAHOO.util.Dom.get('tdoc').value,
				radSerie        = YAHOO.util.Dom.get('radSerie').value,
				radPqr          = YAHOO.util.Dom.get('radPqr').value,
				mensaje2		= "El numero del expediente no es correcto",
				mensaje3		= "Se incluyeron los radicados :)\n\t",
				mensaje4		= "No se incluyeron los radicados :(\n\t ya existen en el expediente",
				
				sUrl			= 'libs/crearExpedientesj.php',
				depe 			= YAHOO.util.Dom.get('nom_depe').value,
				
				postData 		=	GLOBAL_sid			+
									"&evento=" 			+ 5 +
									"&depen=" 			+ depe +
									"&depeCodiUsua="	+ GLOBAL_depecodi +
									"&codUsua="			+ GLOBAL_codusua +
									"&docUsua="			+ GLOBAL_usuadoc +									
									"&nurad="			+ GLOBAL_numRad +	
									"&numExpe="			+ no_exp +
									"&codTdoc="			+ YAHOO.util.Dom.get('selectTipoDocumental').value +
									"&rad_anex=" 		+ GLOBAL_sal_Radic+
									"&selectSerie=" 	+ selectSerie +							
									"&selectSubSerie=" 	+ selectSubSerie +
									"&folios="			+ YAHOO.util.Dom.get('folios').value +
									"&palabrasClave="	+ YAHOO.util.Dom.get('palabrasClave').value +
									"&nombreProyector="	+ YAHOO.util.Dom.get('nombreProyector').value +
									"&nombreRevisor="	+ YAHOO.util.Dom.get('nombreRevisor').value +
									"&cambio="			+ cambio;
			
			
			
			/*if(radSerie == 176 || tipodoc == 2 || radPqr != '') {
				alert("Por ser documento tipificado como PQRSD para su cambio comun\u00edquese con XXXXXX del Grupo de Gesti\u00f3n Documental y Biblioteca y env\u00ede notificaci\u00f3n al \u00e1rea");
				return false;
			}*/
			if (selectSerie == '0') {
				//0 es el value en el tpl  de Seleccione una serie IBISCOM 2018-10-29 validacion de campos obligatorios
				alert("Debe seleccionar una serie");					
		       	return false;
			} 
			if(selectSubSerie == '-1') {
			 	alert("Debe seleccionar una subSerie");
				return false;
			} 
			if(YAHOO.util.Dom.get('selectTipoDocumental').value == '0'){
				alert("Debe seleccionar un tipo documental");
				return false;
			} 
			if(YAHOO.util.Dom.get('ocultaDocElectronico').value == 1) {
				if (YAHOO.util.Dom.get('folios').value.trim() == '') {
					alert("Debe digitar folios");
					return false;    //no submit
				}
			} else {
				YAHOO.util.Dom.get('folios').value = 0;
			}
			
			var valida = 0;
			if ((cambio == '' || cambio == '0') && (seractu == '' || seractu == '0') && (serie == '176' || serie == '999') && (tdocactu != tdocu) && (tiporad == 2) && (retipifica == 0 && pqr == '1')) {
				var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica2 + " del Grupo de Relacionamiento al ciudadano, desea enviar notificaci\u00f3n al \u00e1rea ? ");
                if (opcion == true) {
                	               	
                	var postData = 	GLOBAL_sid
						+ '&tipo=1' 
						+ '&rad='				+ numrad						
						+ '&usua=' 				+ usactu
						+ '&depe=' 				+ depactu
						+ '&depesel=' 			+ depe
						+ '&notifica=2'
						+ '&seractu=' 			+ seractu
						+ '&subseractu=' 		+ subseractu
						+ '&tdocactu=' 			+ tdocactu
						+ '&serie=' 			+ serie
						+ '&tsub=' 				+ subserie
						+ '&tdoc=' 				+ tdocu;
					
					var sUrl			= '../class_control/ModificaTRD.php';
					var successHandler 	= function(o) {
						debugger;
						var resul 		= eval('(' + o.responseText + ')');
						if(resul != '1') {
        					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
            			} else {
            				cerrar();
            			} 
						return false;
					}
					//define failure handler
					var failureHandler = function(o) {
						alert("Error " + o.status + " : " + o.statusText);
					}
					//define callback object
					var callback = {
						success:successHandler,
						failure:failureHandler
					}
					//define transaction to send stuff to server
					var transaction = YAHOO.util.Connect.asyncRequest(
									"POST"
									, sUrl
									, callback
									, postData);
						
                } else {
                	return false;
                }
			}
			else if ((cambio == '' || cambio == '0') && (seractu == '' || seractu == '0') && (serie != '176' && serie != '999') && (tiporad == '2') && (retipifica == '0') && (pqr == '1')) {
				
				var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica1 + " del Grupo de Gesti\u00f3n Documental y Biblioteca, desea enviar notificaci\u00f3n al \u00e1rea ? ");
                if (opcion == true) {
                	               	
                	var postData = 	GLOBAL_sid
						+ '&tipo=1' 
						+ '&rad='				+ numrad						
						+ '&usua=' 				+ usactu
						+ '&depe=' 				+ depactu
						+ '&depesel=' 			+ depe
						+ '&notifica=1'
						+ '&seractu=' 			+ seractu
						+ '&subseractu=' 		+ subseractu
						+ '&tdocactu=' 			+ tdocactu
						+ '&serie=' 			+ serie
						+ '&tsub=' 				+ subserie
						+ '&tdoc=' 				+ tdocu;
					
					var sUrl			= '../class_control/ModificaTRD.php';
					var successHandler 	= function(o) {
						debugger;
						var resul 		= eval('(' + o.responseText + ')');
						if(resul != '1') {
        					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
            			} else {
            				cerrar();
            			} 
						return false;
					}
					//define failure handler
					var failureHandler = function(o) {
						alert("Error " + o.status + " : " + o.statusText);
					}
					//define callback object
					var callback = {
						success:successHandler,
						failure:failureHandler
					}
					//define transaction to send stuff to server
					var transaction = YAHOO.util.Connect.asyncRequest(
									"POST"
									, sUrl
									, callback
									, postData);
						
                } else {
                	return false;
                }
                
			}    
			else if ((cambio == '' || cambio == '0') && (seractu == '176' || seractu == '999') && (serie == '176' || serie == '999') && (tiporad == '2') && (retipifica == '0') && (pqr == '1')) {
				if (tdocactu != tdocu) {

					var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica2 + " del Grupo de Relacionamiento al Ciudadano, desea enviar notificaci\u00f3n al \u00e1rea ? ");
	                if (opcion == true) {
	                	               	
	                	var postData = 	GLOBAL_sid
							+ '&tipo=1' 
							+ '&rad='				+ numrad						
							+ '&usua=' 				+ usactu
							+ '&depe=' 				+ depactu
							+ '&depesel=' 			+ depe
							+ '&notifica=2'
		                	+ '&seractu=' 			+ seractu
							+ '&subseractu=' 		+ subseractu
							+ '&tdocactu=' 			+ tdocactu
							+ '&serie=' 			+ serie
							+ '&tsub=' 				+ subserie
							+ '&tdoc=' 				+ tdocu;
						
						var sUrl			= '../class_control/ModificaTRD.php';
						var successHandler 	= function(o) {
							debugger;
							var resul 		= eval('(' + o.responseText + ')');
							if(resul != '1') {
	        					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
	            			} else {
	            				cerrar();
	            			} 
							return false;
						}
						//define failure handler
						var failureHandler = function(o) {
							alert("Error " + o.status + " : " + o.statusText);
						}
						//define callback object
						var callback = {
							success:successHandler,
							failure:failureHandler
						}
						//define transaction to send stuff to server
						var transaction = YAHOO.util.Connect.asyncRequest(
										"POST"
										, sUrl
										, callback
										, postData);
							
	                } else {
	                	return false;
	                }
				} else {
					valida = 1;
				}
			}
			else if ((cambio == '' || cambio == '0') && (seractu == '176' || seractu == '999') && (serie != '176' && serie != '999') && (tiporad == '2') && (retipifica == '0') && (pqr == '1')) {
				
				var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica1 + " del Grupo de Gesti\u00f3n Documental y Biblioteca, desea enviar notificaci\u00f3n al \u00e1rea ? ");
	            if (opcion == true) {
	                   			
						var postData = 	GLOBAL_sid
							+ '&tipo=1' 
							+ '&rad='				+ numrad						
							+ '&usua=' 				+ usactu
							+ '&depe=' 				+ depactu
							+ '&depesel=' 			+ depe
							+ '&notifica=1'
							+ '&seractu=' 			+ seractu
							+ '&subseractu=' 		+ subseractu
							+ '&tdocactu=' 			+ tdocactu
							+ '&serie=' 			+ serie
							+ '&tsub=' 				+ subserie
							+ '&tdoc=' 				+ tdocu;
					
						var sUrl			= '../class_control/ModificaTRD.php';
						var successHandler 	= function(o) {
							debugger;
							var resul 		= eval('(' + o.responseText + ')');	
							if(resul != '1') {
	            				alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
	               			} else {
	               				cerrar();
	               			} 
						}
						//define failure handler
						var failureHandler = function(o) {
							alert("Error " + o.status + " : " + o.statusText);
						}
						//define callback object
						var callback = {
							success:successHandler,
							failure:failureHandler
						}
						//define transaction to send stuff to server
						var transaction = YAHOO.util.Connect.asyncRequest(
										"POST"
										, sUrl
										, callback
										, postData);
	            } else {
	               	return false;
	            }
			} else if (cambio == '1') {			
				alert('No se permite incluir en expediente, est\u00e1 pendiente de aprobaci\u00f3n para cambio de TRD !!');
				return false;
			} else if (cambio == '3') {
				alert('No se permite incluir en expediente, est\u00e1 pendiente de aprobaci\u00f3n para cambio de Tipo Documental !!');
				return false;
			} else if (cambio == '2' && seractu == 176 && serie == 176 && tiporad == '2') {
				alert('Se aprob\u00f3 el cambio de la TRD, por lo tanto debe cambiarlo');
				return false;
			} else if (cambio == '4' && seractu == 176 && serie != 176 && tiporad == '2') {
				alert('Se aprob\u00f3 solo el cambio del Tipo Documental, por lo tanto no debe camabiar la TRD completa');
				return false;
			} else {
				valida = 1;
			}
			
			if (valida == 1) {
				debugger;
				if(exp.test(no_exp)){
				
					let arr = text_NomExp.split(' ');
					var numexp = arr[0];
					//alert(numexp.substring(0, 4) + ' - ' + numexp.substring(4, 7) + ' - ' + numexp.substring(7, 10) + ' - ' + numexp.substring(10, 13) + ' - ' + numexp.substring(13, 18));
					if (numexp.length == 19) {
						if (Number(numexp.substring(4, 7)) != depe) {
							alert("La dependencia seleccionada y la dependencia del expediente son diferentes!!");
							return false
						}
						if (numexp.substring(7, 10) != selectSerie) {
							alert("La Serie seleccionada y la Serie del expediente son diferentes!!");
							return false
						}
						if (numexp.substring(10, 13) != zfill(selectSubSerie, 3)) {
							alert("La Subserie seleccionada y la Subserie del expediente son diferentes!!");
							return false
						}
					} else if (numexp.length == 20) {
						if (Number(text_NomExp.substring(4, 8)) != depe) {
							alert("La dependencia seleccionada y la dependencia del expediente son diferentes!!");
							return false
						}
						if (numexp.substring(8, 11) != selectSerie) {
							alert("La Serie seleccionada y la Serie del expediente son diferentes!!");
							return false
						}
						if (numexp.substring(11, 14) != zfill(selectSubSerie, 3)) {
							alert("La Subserie seleccionada y la Subserie del expediente son diferentes!!");
							return false
						}
					}
					if (ano != 0 && numexp.substring(0, 4) != ano) {
							alert("El a\u00F1o seleccionado y el a\u00F1o del expediente son diferentes!!");
							return false
					}
						
						var callback = {
								success: function(o) {
									debugger;
											var resul		= eval('(' + o.responseText + ')');
											if(resul.respuesta == true) {
												if(resul.grabados.length > 0) {
													alert(mensaje3 
													+ resul.grabados);
												}
												if(resul.existen != null) {
													alert(mensaje4
													+ resul.existen);
												}
												if (YAHOO.util.Dom.get("selectTipoDocumental2") == null)
												{
													opener.regresar();
													self.close();
												}
											}else{											
												if(resul.existen != null) {
													alert(mensaje4
													+ resul.existen);
												} else {
													alert(resul.mensaje);
													return false
												}
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
			
			var exp				= /^[0-9]{17,19}[E]{1}/,
				ano_busq 		= YAHOO.util.Dom.get('ano_busq').value,
				depe 			= YAHOO.util.Dom.get('nom_depe').value,			
				text_NomExp		= YAHOO.util.Dom.get("nomb_Expe_search").value,						
				mensaje2		= "El numero del expediente no es correcto",
				no_exp			= exp.exec(text_NomExp),
				
				sUrl			= 'libs/adm_nombreTemasExpJ.php',
				
				
				postData 		= 	GLOBAL_sid			+
									"&evento=" 			+ 6 + 
									"&depe=" 			+ depe +
									"&ano_busq=" 		+ ano_busq +						
									"&query="			+ text_NomExp;											
			
			
			if(exp.test(no_exp)){
				var callback = {
							success: function(o) {
										var r		= eval('(' + o.responseText + ')');
										if (r.respuesta == true){
										
											var renTex 	= YAHOO.util.Dom.get("nombActuExp");
											
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


		function cerrar() {
			opener.regresar();
			self.close();
		};	  

	
		//EVENTO
		/*
		 * Manejo de eventos originados por acciones o botones
		 */	
		
		YAHOO.util.Event.on(	"nom_depe",
								"change",
								sendDepBusq);		
		
		YAHOO.util.Event.on(	"selectSerie",
								"change",
								sendSerie);
		
		YAHOO.util.Event.on(	"selectSubSerie",
								"change",
								sendSubSerie);
		
		YAHOO.util.Event.on(	"selectTipoDocumental2",
				"change",
				sendTipoDoc2);
		
		incluirRadExpe.on("click",		
						incluirRadExp);
		
		buscNomExp.on(	"click",		
					buscNombExpNumero);
		
		cerrarVentana.on("click",		
				cerrar);	 
		
		if (document.getElementById("selectSerie").value != 0) {
	   		sendSerie(document.getElementById("selectSerie"));
	   	}
	   	
		if (document.getElementById("selectSubSerie").value != -1) {
	   		sendSubSerie(document.getElementById("selectSubSerie"));
	   	}
		
	});