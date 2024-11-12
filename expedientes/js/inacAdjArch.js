/*
 * Activar inactivar adjuntos de Expedientes
 * Envia a un archivo php los adjuntos seleccionados
 * para que estos puedan cambiar su estado
*/

//Inicia cuando todos los elementos estan activos
	YAHOO.util.Event.onDOMReady(function (){
		
		var	actualizar				= new YAHOO.widget.Button("actualizar"),
			actualizar2				= new YAHOO.widget.Button("actualizar2"),
			inactivar				= new YAHOO.widget.Button("inactivar"),
			activar					= new YAHOO.widget.Button("activar");
	
		// FUNCION  
		// Cambiar estado de los expedientes		
		
		function cambiarEstado(p_oEvent){	 
	    	 
			var mensaje1			= 'Parametros incorrectos, faltan datos',
				Dom 				= YAHOO.util.Dom,		
				accion				= this.get("id"),		
				sUrl				= 'libs/inacAdjArch.php',			
				postData = 	(accion == 'inactivar')? 'accion=1' : 'accion=0'; 				
			
				
			var callback = {					
						
				success: function(o) {		
					var r		= eval('(' + o.responseText + ')');
					
					if (r.respuesta == true) {
						alert(r.mensaje);	
						location.reload(true);											
		            }else{
						alert(r.mensaje);
					}						
				},
										
				failure: function(o) {
					alert("Error retornando datos" 
							+ o.status + " : " + o.statusText);
				}
			};	
			
			
			
			YAHOO.util.Connect.setForm('enviCamb');	
			var transaction = YAHOO.util.Connect.asyncRequest(
						"POST"
						, sUrl
						, callback
						, postData);	
		
		} 
																		
		actualizar.on(	"click",		
						function(){
							location.reload(true);
						});
		
		actualizar2.on(	"click",		
						function(){
							location.reload(true);
						});
		
		inactivar.on(	"click",		
						cambiarEstado);
		
		activar.on(		"click",		
						cambiarEstado);		
	});