/*
 * Archivo para la administración de expedientes
 * Se manejan los eventos para incluir modificar 
 * Permisos etc. relacionados con expedientes.
 * Este archivo esta relacionado con:
 * camNombExpediente.tpl =>Interfaz de usuario
 * camNombExpediente.php =>Lógica de inicio de la aplicación
 * camNombExpediente.js =>Realiza las consultas y procesos solicitados por este archivo  
*/


//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function () {	
	
		//VARIABLES GENERALES GLOBALES
		var	cambNomExp				= new YAHOO.widget.Button("cambNomExp"),
			GLOBAL_depecodi			= depecodi(),			
			GLOBAL_codusua			= codusua(),
			GLOBAL_numExp			= numExp(),		
			GLOBAL_sid				= sid();		
		
		
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
		
		// Tamaño maximo a mostrar de resultados
		autoNomb.maxResultsDisplayed = 25;
		
		// Set Request change de values to send
		autoNomb.generateRequest = function(sQuery){
			return 	GLOBAL_sid + 
					"&depeCodiUsua="	+ GLOBAL_depecodi +
					"&evento=4&query=" + sQuery;
		};
		
		return {
			search: search,
			autoNomb: autoNomb
		};
	}();
	
	
	//FUNCION
	//CAMBIAR NOMBRE DEL EXPEDIENTE
	/*
	 * Se envia las mismas variable que se utilizaron para 
	 * realizar la busqueda y se verifica si existe o no 
	 * se puede realizar el cambio
	 */
	
	function mod_nomExpediente(){
		
		var nomb_Expe_300	= YAHOO.util.Dom.get("myInput").value,
			mensaje1		= "Se realizo el cambio de nombre la expediente: \n"			
			mensaje2		= "El campo para el nuevo nombre esta sin datos o \n",
			mensaje3		= "No se realizo el cambio de nombre",			
			
			sUrl			= 'libs/adm_nombreTemasExpJ.php',
			
			postData 		= 	GLOBAL_sid			+
								"&evento=" 			+ 7 +	
								"&depe="	+ GLOBAL_depecodi +
								"&codUsua="			+ GLOBAL_codusua +
								"&numExpSess="		+ GLOBAL_numExp +																		
								"&nomb_Expe_300="	+ nomb_Expe_300;
		
		if(nomb_Expe_300 != ''){
			var callback = {
						success: function(o) {
									var r		= eval('(' + o.responseText + ')');
									if (r.respuesta == true){
											alert(mensaje1+ ' ' + nomb_Expe_300 );
											opener.regresar();
											window.close();				                						
						            }else{
										alert(mensaje3);
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

	
		//EVENTO
		/*
		 * Manejo de eventos originados por acciones o botones
		*/ 	
		
		cambNomExp.on("click",		
						mod_nomExpediente);
		
	});