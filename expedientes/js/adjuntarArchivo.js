YAHOO.util.Event.onDOMReady(function () {

	// Variables globales
	var	GLOBAL_maxlong			= 200,
		fecha					= new Date(),
		GLOBAL_dia 				= fecha.getDate(),
		GLOBAL_mes 				= (fecha.getMonth() + 1),
		GLOBAL_ano 				= fecha.getFullYear();


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


	// FUNCTION ENVIAR FORMULARIO CON ARCHIVOS ____________________________________
    /**
    *Modificar los datos del expedientes si existe
    *o crea un nuevo.
    */
	function formularioArchivos(){
		//validar formulario
		var form 		= document.getElementById("adjuntarArchivo"),
			element2	= document.getElementById('sonico'),
			element3	= document.getElementById('returnedDataDisplay'),
			fecha		= document.getElementById('date1'),
			texto		= form.descrip,
			tipoDocum	= form.var2Value,
			exp_Fecha	= /^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/,
            //caracteres especiales tildes 
            //a=\xe1 A=\xc1
            //e=\xe9 E=\xc9
            //i=\xed I=\xcd 
            //o=\xf3 O=\xd3
            //u=\xfa U=\xda
            //u=\xf1 U=\xd1
			exp_Tex		= /[^\w:()@&\n\s\xf3n\-=#°,.\xe1\xe9\xed\xf3\xfa\xf1\xc1\xc9\xcd\xd3\xda\xd1]+/,
			Dom 		= YAHOO.util.Dom,
			YCM 		= YAHOO.util.Connect,
			mensaje1	= "No incluya texto con caracteres raros",
			mensaje2	= "Texto con mas de 200 caracteres.\nSe eliminara el exeso.",
			mensaje3	= "Formato de descripción erróneo.",
			mensaje4	= "Seleccione un tipo documental.",
			mensaje5	= "No adjunto un archivo",
			mensaje6	= "El formato de la fecha es invalido",
			mensaje7	= "Tiene intercambiado el mes y el día",
            
			compilado	= '',
			result,
			existente;


		//validaciones realizadas antes de enviar el formulario//
		//******************** Inicio *************************//

		for(var i = 0; i < 4; i++ ){
			var searchDiv	= document.getElementById("div" + i);
			if(searchDiv != null){
				if(searchDiv.value.length > 0){
					compilado += searchDiv.value + '\n';
				};
			};
		};

		if(compilado.length == 0){
			alert(mensaje5);
       		return false;
		}else if(exp_Tex.test(texto.value)) {
			texto.focus();
       		alert(mensaje1);
       		return false;    //no submit   	
    	}else if(tipoDocum.value == 'xx'){
			alert(mensaje4);
       		return false;
		}else if(!exp_Fecha.test(fecha.value)) {
			fecha.focus();
       		alert(mensaje6);
       		return false    //no submit
    	};
    	//IBISCOM 2018-10-30  validacion de campos obligatorios
    	if(YAHOO.util.Dom.get('descrip').value == '' ){
    		alert("Debe digitar una descripcion");
       		return false    //no submit
    	}
    	if(YAHOO.util.Dom.get('ocultaDocElectronico').value == 1){
	    	if(YAHOO.util.Dom.get('folios').value == ''){
	    		alert("Debe digitar un numero de folios");
	       		return false    //no submit
	    	}
    	} else {
    		YAHOO.util.Dom.get('folios').value == 0;
    	}
    	//IBISCOM 2018-10-30
		//******************** FIN ****************************//
		//validaciones realizadas antes de enviar el formulario//
		var sUrl	 = "libs/adjuntarArchivos.php";

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
		
		//define callback object
		var callback = {
			upload:function(o) {
				resul = eval('(' + o.responseText + ')');	
				
				if(resul.respuesta == 'alert'){
					alert(resul.mensaje);
					return false;
				}
				for(var salida in resul){
					//colocar mensajes en campo returnedDataDisplay
					var divSalida		= document.createElement('div');
					var myText 			= document.createTextNode(resul[salida].mensaje);
					divSalida.id		= Dom.generateId();
	
					if(resul[salida].respuesta){
						divSalida.appendChild(myText);
					}else{
						var font 			= document.createElement("font");
						font.style.color 	= "red";
						font.appendChild(myText);
						divSalida.appendChild(font);
					}
	
					//validamos si el elemento tiene hijos
					if(element3.hasChildNodes()){
						existente = element3.firstChild;
						Dom.insertBefore(divSalida, existente);
					}else{
						element3.appendChild(divSalida);
					};
				};
			}
		};
		
		
		//event
		YCM.startEvent.subscribe(globalEvents.start);
		YCM.completeEvent.subscribe(globalEvents.complete);
		//harvest form data ready to send to the server
		YAHOO.util.Connect.setForm(form , true, true);		
		//define transaction to send stuff to server
		//IBISCOM 2018-10-25		
		var postData 	= "folios="	+YAHOO.util.Dom.get('folios').value +"&palabrasClave=" +YAHOO.util.Dom.get('palabrasClave').value+"&nombreProyector=" +YAHOO.util.Dom.get('nombreProyector').value+"&nombreRevisor=" +YAHOO.util.Dom.get('nombreRevisor').value;				
		var transaction = YAHOO.util.Connect.asyncRequest(
						"POST"
						, sUrl
						, callback,  postData);//IBISCOM 2018-10-25 se agrega un postData para que las variables sean usadas en el formulario de sUrl
		
	};


	//BOTONES para enviar la informacion del formulario y crear
	//nuevos campos
	var oPushButton1 = new YAHOO.widget.Button("show1up");

	YAHOO.util.Event.addListener("show1up",
								"click",
								calendarioExpedientes.show,
								calendarioExpedientes,
								true);

	YAHOO.util.Event.addListener("showCal",
								"click",
								calendarioExpedientes.show,
								calendarioExpedientes,
								true);


	// Create Buttons without using existing markup
	var oPushButton1 = new YAHOO.widget.Button({
											label:" Enviar "
											, id:"sumit"
											, container:"enviarForm"
											, onclick: { fn: formularioArchivos} });

	// Create Buttons without using existing markup
	var oPushButton1 = new YAHOO.widget.Button({
											label:" Cerrar"
											, id:"cancel"
											, container:"cancelForm"
											, onclick: { fn: function(){
															opener.regresar();
															window.close();
														}}});


	//Incia el formulario con un campo para ingresar archivos
	//crearCampo();
});

	// Filtrar el texto del textares para limitar su tamaño
	function maximaLongitud(texto) {
		if (texto.value.length > GLOBAL_maxlong) {
			in_value = texto.value;
			out_value = in_value.substring(0,GLOBAL_maxlong);
			texto.value = out_value;
		};
	};
