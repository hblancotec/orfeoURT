YAHOO.util.Event.onDOMReady(function () {

	// la variable (GLOBAL_dependencia)viene del archivo crearExpedientes.tpl
	// *** GLOBAL_dependencia ****
	// se crea de la session del usuario y llama esta funcion para
	// integrarla a la aplicacion javascript

	var GLOBAL_dependencia 		= depe_script(),
		GLOBAL_nurad 			= nurad(),		
		GLOBAL_sid				= sid(),	
		GLOBAL_automNum 		= true,
		GLOBAL_privPubl 		= "Publico",
		fecha					= new Date(),
		GLOBAL_dia 				= fecha.getDate(),
		GLOBAL_mes 				= (fecha.getMonth() + 1),
		GLOBAL_ano 				= fecha.getFullYear(),
		GLOBAL_enviado_conf		= false,
		GLOBAL_maxlong			= 300,
		GLOBAL_minlong			= 5,
		GLOBAL_fecha			= 0,	
		GLOBAL_sal_Radic		= [],
		GLOBAL_proyecto 		= 0,
		GLOBAL_sal_Serie		= 0,
		GLOBAL_sal_SubSerie		= 0,		
        GLOBAL_sal_Dep		        = 0,
		GLOBAL_secue			= 0,
		
		GLOBAL_usua_doc			= usua_doc(),					
		GLOBAL_codusua			= codusua(),
		
		GLOBAL_sal_Expe			= 0;
	/**
	*Asignacion de valores por defecto al iniciar la aplicacion
	*/
	

	document.getElementById("numExpd1").value = fecha.getFullYear();
	var depIn = 0;
	depIn = GLOBAL_dependencia;
	
	document.getElementById("numExpd2").value = depIn;
	document.getElementById("numExpd3").value = "000000";
	document.getElementById("numExpd4").value = "00001";
	document.getElementById("numExpd5").value = "E";

	// FUNCTION  CALENDARIO____________________________________
	/**
	*Crea el calendario y añade el evento
	*para mostrar en el input la fecha seleccionada
	*/
		document.getElementById("date1").value 	  = GLOBAL_dia
													+ "/"
													+ GLOBAL_mes
													+ "/"
													+  GLOBAL_ano;


		function handleSelect(type,args,obj) {
	    	var dates = args[0];
			var date = dates[0];
	       	var year = date[0], month = date[1], day = date[2];

			var txtDate1 = document.getElementById("date1");
	       	txtDate1.value =  day + "/" + month + "/" + year;
	   }
		var navConfig = {
            strings : {
                month: "Selecciona el mes",
                year: "Ingresa el a&ntilde;o",
                submit: "Ok",
                cancel: "Cancelar",
                invalidYear: "Ingrese un a&ntilde;o valido",
                title: "Selecciona el d&iacute;a:",
                close: true
            },
            monthFormat: YAHOO.widget.Calendar.SHORT,
            initialFocus: "year"
        };

		calendarioExpedientes = new
   			YAHOO.widget.Calendar("cal1Container", { navigator: navConfig, title:"Selecciona el d&iacute;a:", close:true });
	   	calendarioExpedientes.selectEvent.subscribe(handleSelect,
   								calendarioExpedientes, true);

   		calendarioExpedientes.cfg.setProperty("MONTHS_SHORT",   ["Enr", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]);
		calendarioExpedientes.cfg.setProperty("MONTHS_LONG",    ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]);
        calendarioExpedientes.cfg.setProperty("WEEKDAYS_1CHAR", ["D", "L", "M", "M", "J", "V", "S"]);
        calendarioExpedientes.cfg.setProperty("WEEKDAYS_SHORT", ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"]);
        calendarioExpedientes.cfg.setProperty("WEEKDAYS_MEDIUM",["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"]);
        calendarioExpedientes.cfg.setProperty("WEEKDAYS_LONG",  ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"]);

	   	calendarioExpedientes.render();

	// FUNCTION  AUTOCOMPLETE____________________________________
    /**
    * Muestra los distintos eventos asociados a los
    * input y ejecuta las consultas para crear los
    * autocomplete
    */	
	autoCompleteNom = function(){
		var search = new YAHOO.util.XHRDataSource('libs/crearExpedientesj.php');
		
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
		var autoNomb = new YAHOO.widget.AutoComplete("myInput", "myContainer", search);
		
		// Tama�o maximo a mostrar de resultados
		autoNomb.maxResultsDisplayed = 25;
		
		// Set Request change de values to send
		autoNomb.generateRequest = function(sQuery){
			return 	GLOBAL_sid +
					'&depeCodiUsua='	+ GLOBAL_dependencia +  
					"&evento=4&query=" + sQuery;
		};
		
		return {
			search: search,
			autoNomb: autoNomb
		};
	}();

		
	
	
	
	
	
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

	// FUNCTION  ENVIAR SERIE____________________________________

	/**
	*Funcion que cambia el valor de la serie para ser enviada en
	*el siguiete input y generar combobox en cascada
	*/

	function sendSerie(e){
		debugger;
		var serieInput 				= document.getElementById("selectSerie").value; //  YAHOO.util.Event.getTarget(e).value;
		var selSubSer				= document.getElementById("selectSubSerie");			
		var subSerActu				= document.getElementById("subSerActu").value;
		
		if (subSerActu != 0) {
			GLOBAL_sal_SubSerie 	= zfill(subSerActu, 3);
		} else {
			GLOBAL_sal_SubSerie 	= 0;
		}
		
		selSubSer.options.length= 0
		selSubSer.options[0] 	= new Option('Seleccione una subSerie',-1,true,true);
			
		if(serieInput != 0){
			GLOBAL_sal_Serie = serieInput;			
			document.getElementById("numExpd3").value = GLOBAL_sal_Serie.toString() + '000';			
			
			var sUrl		= 	'libs/crearExpedientesj.php';	
			var postData 	= 	GLOBAL_sid 
								+ '&evento=' 	+ 1
								+ '&selectSerie=' 	+ GLOBAL_sal_Serie		
								+ '&depeCodiUsua='	+ GLOBAL_dependencia
								+ '&veractivos='  + veractivos(); 
					
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
			
			vaciarTipDocs();
					
			var failureHandler = function(o) {
				alert("Error retornando datos de subSerie " + o.status + " : " + o.statusText);
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


	// FUNCTION  ENVIAR SUBSERIE____________________________________
	/**
	*Funcion que cambia el valor de la subserie para ser enviada en
	*el siguiete input y generar combobox en cascada
	*/

	function sendSubSerie(e){
		debugger;	
		var subSerie	= document.getElementById("selectSubSerie").value; // YAHOO.util.Event.getTarget(e).value;
		var docuActu	= document.getElementById("docuActu").value;
		var	selSerie	= document.getElementById("selectSerie");
		var	selTipDoc	= YAHOO.util.Dom.get("selectTipoDocumental");
		var	sUrl		= 'libs/crearExpedientesj.php';
		var	postData 	= GLOBAL_sid
						  + '&evento=' 		+ 8	
						  + '&selectSerie=' + GLOBAL_sal_Serie							  
						  + '&selectSubSerie=' + subSerie 
						  + '&depeCodiUsua=' + GLOBAL_dependencia;

		GLOBAL_sal_SubSerie = zfill(subSerie, 3);	
		
		var selTipDoc2 = null;	
		if (YAHOO.util.Dom.get("selectTipoDocumental2"))
		{
			selTipDoc2 = YAHOO.util.Dom.get("selectTipoDocumental2");
			selTipDoc2.options.length=0;
			selTipDoc2.options[0] = new Option('Seleccione un Tipo Documental',0,true,true);	
		}		
		
		vaciarTipDocs();
		
		if (subSerie != -1) {
			
			var successHandler = function(o) {		
				debugger;	
				var r	= eval('(' + o.responseText + ')');
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
							selTipDoc2.options[y] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
						}
					}
	            }else{
					alert(r.mensaje);
				}
			};
			
			var failureHandler = function(o) {
				alert("Error retornando datos de tipos documentales " + o.status + " : " + o.statusText);
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
				
			document.getElementById("numExpd3").value = GLOBAL_sal_Serie + GLOBAL_sal_SubSerie;
			getSecExpediente();
		}
		else {
			return
		}	
	}


	// FUNCION  
	//SELECCIONAR TIPO DOCUMENTAL PARA CLASIFICACION DE RADICADOS DE SALIDA	
	/*
	*Funcion que cambia el valor del Tipo Documental para clasificar el 
	*anexo de salida.
	*/
	
	function sendTipoDoc2(e){
		debugger;
		var tipoDocInput 	= document.getElementById("selectTipoDocumental2").value; //YAHOO.util.Event.getTarget(e).value,
			depecodi		= document.getElementById("depecodi").value,
			selSerie		= YAHOO.util.Dom.get("selectSerie").value,
			selectSubSerie 	= YAHOO.util.Dom.get('selectSubSerie').value,
			selTipDoc		= YAHOO.util.Dom.get("selectTipoDocumental"),
			selTipDoc2		= YAHOO.util.Dom.get("selectTipoDocumental2"),
			sUrl			= 'libs/crearExpedientesj.php',	
			postData 		= GLOBAL_sid
						  + '&evento=' 		+ 9	
						  + '&depeInput='	+ depecodi
						  + '&depeCodiUsua='	+ depecodi
						  + '&codUsua='			+ GLOBAL_codusua 	
						  + '&docUsua='			+ GLOBAL_usua_doc
						  + '&selectSerie=' + selSerie 							  
						  + '&selectSubSerie=' + selectSubSerie 
						  + '&rad_anex=' + GLOBAL_sal_Radic
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
	
	// FUNCTION  SECUENCIA EXPEDIENTE____________________________________
	//Funcion para obtener el numero de secuencia del expedientes
	//Esta se invoca despues de que seleccionamos la subserie.
	
	function getSecExpediente(){
		var numExp_Ano	= document.forms[0].numExpd1,
			mensaje1	= "Formato de la fecha incorrecto",
			sUrl		= 'libs/crearExpedientesj.php',		
			exp_Ano		= /^\d{4}$/,
			postData 	=	GLOBAL_sid +
							"&evento=" 			+ 2 					+
							"&depeCodiUsua="	+ GLOBAL_dependencia 	+
							"&selectSerie=" 	+ GLOBAL_sal_Serie		+		
							"&selectSubSerie=" 	+ GLOBAL_sal_SubSerie	+
							"&numExp_Ano=" 		+ numExp_Ano.value;			
		
		if(!exp_Ano.test(numExp_Ano.value)) {
			numExp_Ano.value = "";
			numExp_Ano.focus();
       		alert(mensaje1);
       		return false    //no submit
    	}		
				
		var successHandler = function(o) {				
			var r		= eval('(' + o.responseText + ')');			
		    if(r.respuesta == true){
		    	document.getElementById("numExpd4").value = r.mensaje;
		    	GLOBAL_secue = r.mensaje;

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

	
	// FUNCTION  AUTOMATICO_MANUAL____________________________________
	/**
	*Funcion para activar o descativar el cambio de numero
	*del expediente.
	*/
	function desbloquearInputNum() {
		//Numero automatico es true = GLOBAL_automNum
		var id1 		= document.getElementById("numExpd1"),
			id2 		= document.getElementById("numExpd4");
			 
		GLOBAL_automNum = !(GLOBAL_automNum);
		
		if(GLOBAL_automNum) {
			id1.readOnly = true;
			id2.readOnly = true;
			document.getElementById("numExpd1").value = fecha.getFullYear();
			getSecExpediente();
		}
		else {
			id1.readOnly = false;
			id2.readOnly = false;
		}
	}


	// FUNCION VALIDAR DATOS EXPEDIENTE____________________________________

    /**
    *Funcion para cambiar los datos del expediente o
    *modificarlos segun el caso. Esta funcion reune el
    *resultado de los demas componentes.
    *al final de la ejecucion llama al archivo para confirmar
    *la creacion del expediente
    */

	function validarDatosExp(){

		var mensaje1		= 	" Seleccione un elemento existente ",
			mensaje2		=	" El nombre del expediente \n debe contener maximo 300 caracteres \n se recoratara hasta el caracter limite.",
			mensaje3		=	" Debe asignar un nombre al expediente \n minimo 5 caracteres",
			
			serie 			= 	GLOBAL_sal_Serie,
			subSerie		=	GLOBAL_sal_SubSerie,		
			fecha			=	GLOBAL_fecha,			

			numExp_Ano		=	document.forms[0].numExpd1,
			numExp_Dep		=	document.forms[0].numExpd2,
			numExp_SerSub	=	document.forms[0].numExpd3,
			numExp_Sec		=	document.forms[0].numExpd4,
			numExp_Letra	=	document.forms[0].numExpd5,
			nomb_Expe		=	document.forms[0].nomb_Expe_300,

			exp_Fecha		=	/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/,
			exp_Ano			=	/^\d{4}$/,
			exp_Dep			=	/^\d{4}$/,
			exp_SerSub		=	/^\d{6}$/,
			exp_Sec			=	/^\d{5}$/,
			exp_Letra		=	/^[A-Z]{1}$/;
		
		var tdocu =	document.getElementById("selectTipoDocumental").value;
		var numrad = document.getElementById("numrad").value;
		var tiporad = numrad.toString().substr(-1);
		//validacion de los formatos
		
		//Modificado por CECG 30/01/2012
		if (nomb_Expe.value.length < GLOBAL_minlong){
			alert(mensaje3);
			formComp = false;
			return false; //no submit
		} 
		if (nomb_Expe.value.length > GLOBAL_maxlong) {
			nomb_Expe.focus();
			alert(mensaje2);
			out_value = nomb_Expe.value.substring(0, GLOBAL_maxlong);
			nomb_Expe.value = out_value;
			formComp 		= 	false;
			return false; //no submit
		}
		if(serie == 0) {			
       		alert('Serie =>' + mensaje1);
			formComp 		= 	false;      		
       		return false    //no submit
    	}
		
		if(subSerie == -1 || !exp_SerSub.test(numExp_SerSub.value)) {
       		alert('Subserie =>' + mensaje1);
			formComp 		= 	false;
       		return false    //no submit
    	}	

    	if(!exp_Ano.test(numExp_Ano.value)) {
			numExp_Ano.value = "";
			numExp_Ano.focus();
       		alert('Año =>' + mensaje1);
			formComp 		= 	false;
       		return false    //no submit
    	}

    	if(!exp_Dep.test(numExp_Dep.value)) {
			numExp_Dep.value = "";
			numExp_Dep.focus();
       		alert('Dependencia =>' + mensaje1);
			formComp 		= 	false;
       		return false    //no submit
    	}

    	if(!exp_Sec.test(numExp_Sec.value)) {
			numExp_Sec.value = "";
			numExp_Sec.focus();
       		alert('Secuencia =>' + mensaje1);
			formComp 		= 	false;
       		return false    //no submit
    	}

    	if(!exp_Letra.test(numExp_Letra.value)) {
			numExp_Letra.value = "";
			numExp_Letra.focus();
       		alert('Letra =>' + mensaje1);
			formComp 		= 	false;
       		return false    //no submit
    	}
    	
    	var seractu = document.getElementById("serieActu").value;
    	var subseractu = document.getElementById("subSerActu").value;
    	var cambio = document.getElementById("cambio").value;
    	var tdocactu = document.getElementById("docuActu").value;
    	var retipifica = document.getElementById("retipifica").value;
    	var usuModifica1 = document.getElementById("usuaModifica1").value;
		var usuModifica2 = document.getElementById("usuaModifica2").value;
		var usactu = document.getElementById("codusua").value;
		var depactu = document.getElementById("depecodi").value;
		var pqr = document.getElementById("pqr").value;
		
		var valida = 0;
		if ((cambio == '' || cambio == '0') && (seractu == '' || seractu == '0') && (serie != '176' && serie != '999') && (tiporad == '2') && (retipifica == 0) && (pqr == '1')) {
			var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica1 + " del Grupo de Gesti\u00f3n Documental y Biblioteca, desea enviar notificaci\u00f3n al \u00e1rea ? ");
            if (opcion == true) {
            	               	
            	var postData = 	GLOBAL_fecha
					+ '&tipo=1' 
					+ '&rad='				+ numrad						
					+ '&usua=' 				+ usactu
					+ '&depe=' 				+ depactu
					+ '&depesel=' 			+ depactu
					+ '&notifica=1'
					+ '&seractu=' 			+ seractu
					+ '&subseractu=' 		+ subseractu
					+ '&tdocactu=' 			+ tdocactu
					+ '&serie=' 			+ serie
					+ '&tsub=' 				+ subSerie
					+ '&tdoc=' 				+ tdocu;
				
				var sUrl			= '../class_control/ModificaTRD.php';
				var successHandler 	= function(o) {
					var resul 		= eval('(' + o.responseText + ')');
					if(resul != '1') {
    					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
        			} else {
        				window.close();
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
				//harvest form data ready to send to the server
				var form = document.getElementById("modiCreaExpe");
				YAHOO.util.Connect.setForm(form);
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
		else if ((cambio == '' || cambio == '0') && (seractu == '' || seractu == '0') && (serie == '176' || serie == '999') && (tdocactu != tdocu) && (tiporad == '2') && (retipifica == '0') && (pqr == '1')) {
			var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica2 + " del Grupo de Relacionamiento al Ciudadano, desea enviar notificaci\u00f3n al \u00e1rea ? ");
            if (opcion == true) {
            	               	
            	var postData = 	GLOBAL_fecha
					+ '&tipo=1' 
					+ '&rad='				+ numrad						
					+ '&usua=' 				+ usactu
					+ '&depe=' 				+ depactu
					+ '&depesel=' 			+ depactu
					+ '&notifica=2'
					+ '&seractu=' 			+ seractu
					+ '&subseractu=' 		+ subseractu
					+ '&tdocactu=' 			+ tdocactu
					+ '&serie=' 			+ serie
					+ '&tsub=' 				+ subSerie
					+ '&tdoc=' 				+ tdocu;
				
				var sUrl			= '../class_control/ModificaTRD.php';
				var successHandler 	= function(o) {
					var resul 		= eval('(' + o.responseText + ')');
					if(resul != '1') {
    					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
        			} else {
        				window.close();
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
				//harvest form data ready to send to the server
				var form = document.getElementById("modiCreaExpe");
				YAHOO.util.Connect.setForm(form);
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

				var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica2 + " del Grupo de Gesti\u00f3n Documental y Biblioteca, desea enviar notificaci\u00f3n al \u00e1rea ? ");
                if (opcion == true) {
                	               	
                	var postData = 	GLOBAL_fecha
						+ '&tipo=1' 
						+ '&rad='				+ numrad						
						+ '&usua=' 				+ usactu
						+ '&depe=' 				+ depactu
						+ '&depesel=' 			+ depactu
						+ '&notifica=2'
	                	+ '&seractu=' 			+ seractu
						+ '&subseractu=' 		+ subseractu
						+ '&tdocactu=' 			+ tdocactu
						+ '&serie=' 			+ serie
						+ '&tsub=' 				+ subSerie
						+ '&tdoc=' 				+ tdocu;
					
					var sUrl			= '../class_control/ModificaTRD.php';
					var successHandler 	= function(o) {
						var resul 		= eval('(' + o.responseText + ')');
						if(resul != '1') {
        					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
            			} else {
            				window.close();
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
					//harvest form data ready to send to the server
					var form = document.getElementById("modiCreaExpe");
					YAHOO.util.Connect.setForm(form);
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
                   			
					var postData = 	GLOBAL_fecha
							+ '&tipo=1' 
							+ '&rad='				+ numrad						
							+ '&usua=' 				+ usactu
							+ '&depe=' 				+ depactu
							+ '&depesel=' 			+ depactu
							+ '&notifica=1'
							+ '&seractu=' 			+ seractu
							+ '&subseractu=' 		+ subseractu
							+ '&tdocactu=' 			+ tdocactu
							+ '&serie=' 			+ serie
							+ '&tsub=' 				+ subSerie
							+ '&tdoc=' 				+ tdocu;
					
						var sUrl			= '../class_control/ModificaTRD.php';
						var successHandler 	= function(o) {
							var resul 		= eval('(' + o.responseText + ')');	
							if(resul != '1') {
            					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
                			} else {
                				window.close();
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
						//harvest form data ready to send to the server
						var form = document.getElementById("modiCreaExpe");
						YAHOO.util.Connect.setForm(form);
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
			alert('No se permite crear el expediente, est\u00e1 pendiente de aprobaci\u00f3n para cambio de TRD !!');
			return false;
		} else if (cambio == '3') {
			alert('No se permite crear el expediente, est\u00e1 pendiente de aprobaci\u00f3n para cambio de Tipo Documental !!');
			return false;
		} else if (cambio == '2' && seractu == 176 && serie == 176 && tiporad == '2') {
			alert('Se aprob\u00f3 el cambio de la TRD, por lo tanto debe cambiarlo');
			return false;
		} else if (cambio == '4' && seractu == 176 && serie != 176 && tiporad == '2') {
			alert('Se aprob\u00f3 solo el cambio del Tipo Documental, por lo tanto no debe cambiar la TRD completa');
			return false;
		} else {
			valida = 1;
		}
		
		if (valida == 1) {
			
			//Se envian los datos del formulario con formato valido
			//al archivo php para validarlos y procesalos
			//alert("L-526 Radicado: " + GLOBAL_sal_Radic);					
			//define success handler
		    GLOBAL_enviado_conf = false;
			if (!GLOBAL_enviado_conf) {
				GLOBAL_enviado_conf = true;
				document.getElementById("javaScripConfirmar1").value = "Enviando...";
		    	document.getElementById("javaScripConfirmar1").disabled = true;		
			    return true;
			} else {
				alert("Ya se esta enviando...");
			    return false;
			}	
		}
	};



	// FUNCTION NUMERO_DE_EXPEDIETE____________________________________
    /**
    *Se selecciona el numero del expediente a enviar. Si la variable global
    *GLOBAL_automNum esta en automatica "true" se selecciona el numero que
    *que se origina por los valores por defecto. Si esta en manual "false"
    *se traen lo datos de los input y se compone el numero del expediente.
    */

    function numeroExpediente(){
    	var numExpe="",
    		numExp_Ano		=	document.forms[0].numExpd1.value,
			numExp_Dep		=	document.forms[0].numExpd2.value,
			numExp_SerSub	=	document.forms[0].numExpd3.value,
			numExp_Sec		=	document.forms[0].numExpd4.value,
			numExp_Letra	=	document.forms[0].numExpd5.value;

    	if(GLOBAL_automNum == true){
			numExpe=  GLOBAL_ano
					+ GLOBAL_dependencia
					+ GLOBAL_sal_Serie
					+ GLOBAL_sal_SubSerie	
					+ GLOBAL_secue
					+ "E";
    	}else{
    		numExpe= numExp_Ano
    				+ numExp_Dep
    				+ numExp_SerSub
    				+ numExp_Sec
    				+ numExp_Letra
    	}
    	return numExpe;
    }

	// FUNCTION MODIFICAR CREAR EXPEDIENTE____________________________________
    
	/**
    *Modificar los datos del expedientes si existe
    *o crea un nuevo.
    */
	
	function generarModificarExpediente() {
		debugger;
		//validar formulario
		if(!validarDatosExp()){return}		
		//parametros que se muetran en la respuesta
		var remobTabla	= document.getElementById("crearExpeUno"),
			element1 	= document.getElementById("respuesta"),			
			element3 	= document.getElementById("nombre_Proyecto"),
			element4 	= document.getElementById("nombre_Expediente"),
			element5 	= document.getElementById("expedi_NumId"),
			element6 	= document.getElementById("serie_NumId"),
			element7 	= document.getElementById("subSerie_NumId"),
			element8 	= document.getElementById("nombre_Permisos"),
						
			Dom 		= YAHOO.util.Dom,			
			
			text_NomE	= document.forms[0].nomb_Expe_300.value,
			text_NumEx	= numeroExpediente().toString(),
			text_Serie	= (document.getElementById("selectSerie")).options[document.getElementById('selectSerie').selectedIndex].text,
			text_SubSer = (document.getElementById("selectSubSerie")).options[document.getElementById('selectSubSerie').selectedIndex].text;			
			
		var	texto4 = document.createTextNode(text_NomE),
			texto5 = document.createTextNode(text_NumEx),
			texto6 = document.createTextNode(text_Serie),
			texto7 = document.createTextNode(text_SubSer),
			texto8 = document.createTextNode(GLOBAL_privPubl);
		
		if(mosProy()=='true'){			
			var element9 = document.getElementById("selectProyecto");
			var text_Proy	= (document.getElementById("selectProyecto")).options[document.getElementById('selectProyecto').selectedIndex].text;						
			var texto3 = (element9.value == 0)? document.createTextNode(" ") : document.createTextNode(text_Proy);			
			element3.appendChild(texto3);			
		}	
		
		element4.appendChild(texto4);
		element5.appendChild(texto5);
		element6.appendChild(texto6);
		element7.appendChild(texto7);
		element8.appendChild(texto8);		
		
		//Se envia por post el numExpe Generado ya sea automatico o manual
		//Esta funcio envia los campos encontrados en el formulario sin necesidad
		//de instanciarlos.
		var cambio = document.getElementById("cambio").value;
		
		var postData = 	GLOBAL_sid
						+ '&evento=' 			+ 3 
						+ '&numExpe='			+ text_NumEx						
						+ '&auto=' 				+ GLOBAL_automNum
						+ '&nurad=' 			+ GLOBAL_nurad
						+ '&publ_priv='		 	+ GLOBAL_privPubl
						+ '&depeCodiUsua='		+ GLOBAL_dependencia
						+ '&docUsua='			+ GLOBAL_usua_doc
						+ '&codUsua='			+ GLOBAL_codusua
						+ '&cambio='			+ cambio												
						+ '&rad_anex='	 		+ GLOBAL_sal_Radic;
						
		var sUrl			= 'libs/crearExpedientesj.php';
		var successHandler 	= function(o) {
			debugger;
			var resul 		= eval('(' + o.responseText + ')');	
			
			if(resul.respuesta == false){
				GLOBAL_enviado_conf = false;
				document.getElementById("javaScripConfirmar1").value = "Crear";
    			document.getElementById("javaScripConfirmar1").disabled = false;	
				alert(resul.mensaje);
			}else{
				//borramos la tabla de crear expedientes
				while (remobTabla.hasChildNodes()) remobTabla.removeChild(remobTabla.firstChild);				

				//mostramos la tabla de respuesta y de cambiar nombres
				//de expediente

				element1.style.visibility = 'visible';//Esto es para que oculte la mugre de ie				
				Dom.removeClass(element1, 'yui-hidden2');				
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
		//harvest form data ready to send to the server
		var form = document.getElementById("modiCreaExpe");
		YAHOO.util.Connect.setForm(form);
		//define transaction to send stuff to server
		var transaction = YAHOO.util.Connect.asyncRequest(
						"POST"
						, sUrl
						, callback
						, postData);
	}


	// CREACION DE LOS BOTONES EVENTOS______________________
	/**
	*Se crea una instancia de un boton para activar el calendario
	*Se crea una instancia de un boton para Modificar el numero
	*del expediente
	*/
	
	YAHOO.util.Event.addListener("show1up",
								"click",
								calendarioExpedientes.show,
								calendarioExpedientes,
								true);

	var oPushButton2 = new YAHOO.widget.Button("modificarNum", {
                            checked: false, // Attribute override
                            label: "Manual"
                        });

   	var oPushButton4 = new YAHOO.widget.Button(		"javaScripConfirmar1");

   	var oPushButton6 = new YAHOO.widget.Button(		"respCerrar");

	/**
	* Eventos asociados a los input para enviar y generar
	* nuevas variables.
	*/

	YAHOO.util.Event.addListener("selectSerie",
								"change",
								sendSerie);	
	
	YAHOO.util.Event.addListener("selectSubSerie",
								"change",
								sendSubSerie);
	
	YAHOO.util.Event.addListener("numExpd1",
								"change",
								getSecExpediente);
	
	YAHOO.util.Event.on(	"selectTipoDocumental2",
			"change",
			sendTipoDoc2);

	if (document.getElementById("selectSerie").value != 0) {
   		sendSerie(document.getElementById("selectSerie"));
   	}
   	
	if (document.getElementById("selectSubSerie").value != -1) {
   		sendSubSerie(document.getElementById("selectSubSerie"));
   	}
	
	/**
	*Eventos asociados a los botones
	**/
	
	YAHOO.util.Event.addListener("publico_X",
								"click",
								function(){
									GLOBAL_privPubl = "Publico"									
								});
	
	YAHOO.util.Event.addListener("privado_X",
								"click",
								function(){
									GLOBAL_privPubl = "Privado"									
								});		


	YAHOO.util.Event.addListener("modificarNum",
								"click",
								desbloquearInputNum);

        YAHOO.util.Event.addListener("selectDependencia",
								"change",
								sendDepe);


	oPushButton4.on("click", generarModificarExpediente);

	oPushButton6.on("click", function(){
								opener.regresar();
								window.close();
								});							
                                                                
       /**
	*Funcion que cambia el valor de la serie para ser enviada en
	*el siguiete input y generar combobox en cascada
	*/
	
	function sendDepe(e){
		var depeInput 				= YAHOO.util.Event.getTarget(e).value,
			selUsuario				= document.getElementById("selectUsuario");				
			selUsuario.options.length= 0
			selUsuario.options[0] 	= new Option('Seleccione un usuario',0,true,true);
		
		if(depeInput != 0){
			GLOBAL_sal_Depe = depeInput;			
			//document.getElementById("numExpd3").value = GLOBAL_sal_Serie.toString() + '000';			
			
			var sUrl		= 	'libs/crearExpedientesj.php';	
			var postData 	= 	GLOBAL_sid 
								+ '&evento=' 	+ 7
								+ '&selectDependencia=' +GLOBAL_sal_Depe		
								+ '&depeCodiUsua='	+ GLOBAL_dependencia; 
					
			var successHandler = function(o) {			
				var r		= eval('(' + o.responseText + ')');
				if (r.respuesta == true) {					
					var lenSuv = r.mensaje.length;					
					for (i = 0; i < lenSuv; i++) {
						var j = selUsuario.options.length;																	
						selUsuario.options[j] = new Option(r.mensaje[i].nombre,r.mensaje[i].codigo,false,false);
					}
	            }else{
					alert(r.mensaje);
				}
			};
					
			var failureHandler = function(o) {
				alert("Error retornando datos de Usuario " 
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
			return;
		}
	}
});