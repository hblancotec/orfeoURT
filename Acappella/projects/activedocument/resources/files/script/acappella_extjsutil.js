// mensaje ERROR
showError = function(msg, title) {
   title = title || 'Error';
   Ext.Msg.show({title:title, msg:msg, modal:true,
                 icon:Ext.Msg.ERROR, buttons:Ext.Msg.OK
   });		
};

// mensaje OK
showOk = function(msg, title) {
   title = title || 'Aviso';
   Ext.Msg.show({title:title, msg:msg, modal:true,
                 icon:Ext.Msg.INFO, buttons:Ext.Msg.OK});		
};

// function OK generica
function returnOk(okFn, errorFn,  okMessage, errorMessage) {
   okMessage    = okMessage    || false;
	 errorMessage = errorMessage || true;
   return function okRequest(res) {
      var response = Ext.decode(res.responseText);
			if (!response.success) {
			   if (errorMessage == true) {
			      showError(response.error);
				 };
				 if ((errorFn != undefined) && (errorFn != null)) {
					  errorFn(response);
				 }
      }         
      else {
			   if (okMessage == true) {
				    showOk(response.data)													
				 };						 							           
				 if ((okFn != undefined) && (okFn != null)) {
				 	  okFn(response);
				 }
      };   
   };     
};

// solo muestra mensaje de error
function onlyOnError() {
   return function okRequest(res) {
      var response = Ext.decode(res.responseText);
			if (!response.success) {
			   Ext.Msg.show({title:'Error', msg:response.error, modal:true,
                       icon:Ext.Msg.ERROR, buttons:Ext.Msg.OK});
			};		
	 };												      
};

// error global
errorGlobal = returnErrorRequest(null, null);

// ok global
okGlobal    = returnOk(null, null, null, null);

// ok global
silentGlobal = function(response) {};

// request global
ac_request = function(url, params, sucess, failure, waitMsg) {
  waitMsg = waitMsg || 'Enviando...';
	failure = failure || errorGlobal;
  Ext.Ajax.request({ 'url'    : url
                    ,'success': sucess
									  ,'failure': failure
                    ,'waitMsg': waitMsg
                    ,'params' : params});
}

// make url params
ac_params = function(params) {
	 var ps = '';
	 var i  = 0;
	 for (p in params) {
	    ps = ps + p + '=' + params[p] + '&';
	    i  = i + 1;
	 };
	 if (i > 0) ps = '?' + ps;	 
	 return ps
}

// reset combo
ac_reset_combo = function(combo, url, params) {
	 url = url + ac_params(params);	 
	 combo.reset();	 
   combo.store.proxy = new Ext.data.HttpProxy({url: url});    
   combo.load();
}

// reset dataview
ac_reset_dataview = function(dataview, url, params) {
	 url = url + ac_params(params);	 
	 dataview.store.removeAll();	 
   dataview.store.proxy = new Ext.data.HttpProxy({url:url});    
   dataview.store.load();
}

// limpia y valida una forma
resetForm = function(form) {
   form.reset();
   form.isValid();		
};

// genera array de lista 
ac_toArray = function(data, sep) {
   sep = sep || ',';
	 var value = (data + '').split(sep);
   if ((value.length == 1) && (Ext.isEmpty(value[0]))) 
	    value = [];
	 return value;		
}							
														
//rutina de impresion
function ac_print(html) {
	 var printWin = window.open("","printSpecial");
	 printWin.document.open();
	 printWin.document.write(html);
	 printWin.document.close();
	 printWin.print();
	 printWin.close();
};			        

// funcion paso de reporte
url_wind_direct = function(options){	 
   desde     = options['url'];          
   parametro = options['params'];         
   path = desde+ "?";         
   for (p in parametro) {
      path = path + p + "=" + parametro[p] + "&";
   }         
   window.open(path, 'mywindow', 'width=300,height=400');          
};

// leer informacion de un registro remoto
function remoteRecord(url, id, source, otherparams, success, failure) {   
	 var params = {'_id': id, '_source':source, '_usuario': _usuario_info.id};	 	 	 
	 Ext.apply(params, otherparams);
   Ext.Ajax.request({'url'    : url, 
	                   'success': success, 
										 'failure': failure,
	                   'waitMsg':'Enviando...', 
										 'params' : params});
};

// ************************************************ //
// ****** funciones de ok y error de request ****** //
// ************************************************ //
// retorna error de request por conexion
function returnErrorRequest(msg, title) {
   return function ErrorRequest() {
      var title = "Error";
      var msg   = "Error en la conexión";
      Ext.Msg.show({title:title, msg:msg, modal:true,
                    icon:Ext.Msg.ERROR, buttons:Ext.Msg.OK});
	 };									
};

// retorna mensajes de request
function returnOkRequest(object, okMessage, okFn, errorMessage, errorFn) {
   return function okRequest(res) {
      var response = Ext.decode(res.responseText);
			if (!response.success) {
			   if (errorMessage == true) {				    
            Ext.Msg.show({title:'Error', msg:response.error, modal:true,
                          icon:Ext.Msg.ERROR, buttons:Ext.Msg.OK});			       										
		     }				 											           
				 if ((errorFn != undefined) && (errorFn != null)) {
					  errorFn(response);
				 }
      }         
      else {
			   if (okMessage == true) {
				    object._widgetBase.hide();
            Ext.getCmp(object._widgetBase._info.call).show();
            Ext.Msg.show({title:'Ok', msg:response.data, modal:true,
                          icon:Ext.Msg.INFO, buttons:Ext.Msg.OK});													
				 };						 							           
				 if ((okFn != undefined) && (okFn != null)) {
				 	  okFn(response);
				 }
      };   
   };     
};

// logica para actualizacion de campos
function updateSource(sourceData, object, ok, error) {
   var fD = object.formData;
   if (fD.isValid()) {
      var fields = fD.getValues();        
      if (fD._info.mode == "edit") {        
         remoteRecord(sourceData+'UpdateRecord', fD._info.record,
                      fD._source, {"fields":Ext.encode(fields)},
                      ok, error);
      };
        
      if (fD._info.mode == "new") {
         remoteRecord(sourceData+'InsertRecord', fD._info.record,
                      fD._source, {"fields":Ext.encode(fields)},
                      ok, error);
      };
   }
   else {
      showError("Información incompleta o invalida");
   };
};

// logica para eliminacion de campos
function deleteSource(sourceData, object, ok, error) {
   var fD = object.formData;   
	 remoteRecord(sourceData + 'DeleteRecord', fD._info.record, 
	              fD._source, {}, ok, error);  
};

// regresa de forma windows a grid 
function regresaSource(object) {
   object._widgetBase.hide();
	 var call = Ext.getCmp(object._widgetBase._info.call);
   call.show();  
	 return call;
};

// regresa ok de mostrar registro 
function returnShowOk(object) {
   var fD = object.formData;
	 return function showOk(res) {
      var response = Ext.decode(res.responseText);
      if (!response.success) {
         Ext.Msg.show({title:'Error', msg:response.error, modal:true,
                       icon:Ext.Msg.ERROR, buttons:Ext.Msg.OK});           
      }         
      else {
			   fD.reset();				 
         fD.loadRecord(response);
      };
	 };		   
};

// muestra la informacion de un registro de fuente de datos
function readRecordSource(sourceData, object, ok, error) {
   var fD = object.formData;
   if (object._info.mode == 'edit') {
      Ext.getCmp(object.getId()+"_button_delete").show();
      var fields = fD.getFields();        
      //for (var k in fD.getValues()) {
      //   fields[fields.length] = k;
      //}
      remoteRecord(sourceData + 'ReadRecord', object._info.record, 
			             object._source, {"fields":fields}, ok, error);
   };
	 
   if (object._info.mode == 'new') {	    
      Ext.getCmp(object.getId()+"_button_delete").hide();        
      fD.reset();
      fD.isValid();
   };   
	 
	 if (object._info.mode == 'read') {
      Ext.getCmp(object.getId()+"_button_delete").hide();
      Ext.getCmp(object.getId()+"_button_save").hide();
      var fields = fD.getFields();        

      remoteRecord(sourceData + 'ReadRecord', object._info.record, 
			             object._source, {"fields":fields}, ok, error);
   };  

   Ext.getCmp(object._widgetBase._info.call).hide();     
};

// devuelve json store
function jsonStore(url, fields) {
   var store = new Ext.data.JsonStore({
      url            : url,
			root           : 'data.items',
      versionProperty: 'data.version',
      totalProperty  : 'data.total_count',
			id             : 'id',              
      fields         : fields
   });
	 
	 return store;  
};


// ********************************* //
// ****** funciones globales ****** //
// ******************************** //

// muestra el primer Tab del TabPanel
// y corrije problema con IE, que no
// muestra la data de los Tabs la 1ra vez 
function panelTabShow(panel) {
   // la primera vez para IE que jode mucho!!!
   if (panel._render == undefined) {
      for (var i = 0; i < panel.items.getCount(); i++) {
         panel.activate(panel.items.itemAt(i).getId());
      }  
   }
   panel._render = true;
   panel.setActiveTab(panel.items.itemAt(0).getId());
	 return panel;      
}