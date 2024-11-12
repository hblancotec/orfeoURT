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
		
		//funcion para cuando se seleccione un item
	    var myHandler = function(sType, aArgs) {
				        
	        var oData 	= aArgs[2].toString().substring(20); // object literal of selected item's result data
	        var renTex 	= YAHOO.util.Dom.get("nombActuExp");			
			
			while (renTex.hasChildNodes()) 
				renTex.removeChild(renTex.firstChild);
																
			texto1 		= document.createTextNode(oData);			
			renTex.appendChild(texto1);				        
	    };
		
	})();

	function getNombreExpediente(e) {
		var tmpExp	= YAHOO.util.Event.getTarget(e).value,
			exp		= /^[0-9]{17,19}[E]{1}/,
			sUrl	= 'libs/adm_nombreTemasExpJ.php',
			postData=	GLOBAL_sid	+
						"&evento="	+ 14 + 
						"&depe=" 	+ GLOBAL_depecodi +
						"&numExpSess="	+ tmpExp;
		if(exp.test(tmpExp)){
			var callback = {
				success: function(o) {
					var r	= eval('(' + o.responseText + ')');
					if (r.respuesta == true){						
						var renTex = YAHOO.util.Dom.get(YAHOO.util.Event.getTarget(e).id + "Desc");
						renTex.value = r.mensaje;
					} else {
						YAHOO.util.Dom.get(YAHOO.util.Event.getTarget(e).id).value='';
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
		} else {
			YAHOO.util.Dom.get(YAHOO.util.Event.getTarget(e).id).value='';
			alert('El c\xf3digo ' + tmpExp + ' no es de un expediente.');
		}
	};
	
	function migrarExpediente() {
		var mensaje = '',
			exp		= /^[0-9]{17,19}[E]{1}/,
			sUrl	= 'libs/adm_nombreTemasExpJ.php',
			tmpExpOri = YAHOO.util.Dom.get('txtExpOri1').value,
			tmpExpDes = YAHOO.util.Dom.get('txtExpDes1').value,
			tmpExpCom = YAHOO.util.Dom.get('nomb_coment_6').value.trim();
			
		if (tmpExpOri.trim().length==0 || tmpExpDes.trim().length==0 || tmpExpCom.length==0 ) {
			alert('Error. Son obligatorios los c\xf3digos de expedientes y comentario.\n');
		} else if (tmpExpOri != tmpExpDes) {
					
			var tmpDatos=	GLOBAL_sid	+
				"&evento="	+ 15 + 
				"&depe=" 	+ GLOBAL_depecodi +						
				"&codUsua="	+ GLOBAL_codusua +
				'&usuaDoc='	+ GLOBAL_usuadoc + 
				"&comen="	+ tmpExpCom +
				"&expori="	+ tmpExpOri +
				"&expdes="	+ tmpExpDes;
			var postData = tmpDatos; 
			var callback = {
				success: function(o) {
					var r	= eval('(' + o.responseText + ')');
					if (r.respuesta == true){						
						mensaje += r.mensaje + '\n';
						
					} else {
						mensaje += 'Error ' + r.mensaje + '\n';
					}
					alert(mensaje);
				},
				failure: function(o) {
					alert("Error " + o.status + " : " + o.statusText);
				}
			};
			var transaction = YAHOO.util.Connect.asyncRequest(
							"POST"
							, sUrl
							, callback
							, postData);
		} else {
			alert('Error. Los expedientes son iguales.\n');
		}
	};
	
	function limpiarFrmMigrar(){
		YAHOO.util.Dom.get('txtExpOri1').value = '';
		YAHOO.util.Dom.get('txtExpOri1Desc').value = '';
		YAHOO.util.Dom.get('txtExpDes1').value = '';
		YAHOO.util.Dom.get('txtExpDes1Desc').value = '';
		YAHOO.util.Dom.get('nomb_coment_6').value = '';
	}
	
	//EVENTO
	/*
	 * Manejo de eventos originados por acciones o botones
	 */	
	YAHOO.util.Event.on("txtExpOri1",	"change",	getNombreExpediente);
	YAHOO.util.Event.on("txtExpDes1",	"change",	getNombreExpediente);
							
	var btnMigExp = new YAHOO.widget.Button("btnMigrar");		
	var btnLimExp = new YAHOO.widget.Button("btnReset");
	btnMigExp.on("click", migrarExpediente);					
	btnLimExp.on("click", limpiarFrmMigrar);	
});