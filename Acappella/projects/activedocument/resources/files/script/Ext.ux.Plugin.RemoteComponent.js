Ext.namespace('Ext.ux');
Ext.namespace('Ext.ux.Plugin');

/**
 * @author Timo Michna / matikom
 * @class Ext.ux.Plugin.RemoteComponent
 * @extends Ext.util.Observable
 * @constructor
 * @param {Object} config
 * @version 0.2.1
 * Plugin for Ext.Container/Ext.Toolbar Elements to dynamically 
 * add Components from a remote source to the Element´s body.  
 * Loads configuration as JSON-String from a remote source. 
 * Creates the Components from configuration.
 * Adds the Components to the Container body.
 * Additionally to its own config options the class accepts all the 
 * configuration options required to configure its internal Ext.Ajax.request().
 */
Ext.ux.Plugin.RemoteComponent = function (config, addContainer){   
   this.config = config;
   /**
    * @cfg {String} breakOn set to one of the plugins events, to stop any 
    * further processing of the plugin, when the event fires.
    */
   /**
    * @cfg {String} loadOn set to one of the Containers events, to stop any 
    * further processing of the plugin, when the event fires.
    */
   /**
	* @cfg {String} xtype Default xtype for loaded toplevel component.
	* Overwritten by config.xtype or xtype declaration 
	* Defaults to 'panel'
	* in loaded toplevel component.
	*/
	var defaultType = config.xtype || 'panel';
   /**
	* @cfg {String} purgeListeners Set true to automatically purge
	* any listner for the plugin after the process chain (even when the
	* processing has been stopped by config option breakOn).
	* Defaults to true
	* Overwritten by config.xtype or xtype declaration 
	* in loaded toplevel component.
	*/
    //var purgeListeners = config.purgeListeners || true;
		var purgeListeners = config.purgeListeners || false;
    this.addEvents({
	    /**
	     * @event beforeload
	     * Fires before AJAX request. Return false to stop further processing.
	     * @param {Object} config
	     * @param {Ext.ux.Plugin.RemoteComponent} this
	     */
        'beforeload' : true,
	    /**
	     * @event beforecreate
	     * Fires before creation of new Components from AJAX response. 
		 * Return false to stop further processing.
	     * @param {Object} JSON-Object decoded from AJAX response
	     * @param {Ext.ux.Plugin.RemoteComponent} this
	     */
        'beforecreate' : true,
	    /**
	     * @event beforeadd
	     * Fires before adding the new Components to the Container. 
		 * Return false to stop further processing.
	     * @param {Object} new Components created from AJAX response.
	     * @param {Ext.ux.Plugin.RemoteComponent} this
	     */
        'beforeadd' : true,
	    /**
	     * @event beforecomponshow
	     * Fires before show() is called on the new Components. 
		 * Return false to stop further processing.
	     * @param {Object} new Components created from AJAX response.
	     * @param {Ext.ux.Plugin.RemoteComponent} this
	     */
        'beforecomponshow': true,
	    /**
	     * @event beforecontainshow
	     * Fires before show() is called on the Container. 
		 * Return false to stop further processing.
	     * @param {Object} new Components created from AJAX response.
	     * @param {Ext.ux.Plugin.RemoteComponent} this
	     */
        'beforeshow': true,
	    /**
	     * @event success
	     * Fires after full process chain. 
		 * Return false to stop further processing.
	     * @param {Object} new Components created from AJAX response.
	     * @param {Ext.ux.Plugin.RemoteComponent} this
	     */
        'success': true
    });
	// set breakpoint 
	if(config.breakOn){
	 	this.on(config.breakOn, function(){return false;});
	}
	
	// registro en nombre de espacios global de widgets
  function itemregister(item) {
		    if (item._register == true) {
				   var idReg = null;
				   if (item.getId) {				   			 
					    idReg = item.getId();
					 }		
					 else {
					    idReg = item.id;
					 }	
					 	
           if ((item._id != undefined) && (item._id != null) && 
					      (item._id != '')) {
					    idReg = item._id;
					 }
					  					 		
					 if ((idReg != undefined) && (idReg != null)) {
					    // window espacio base del navegador 
				      window[idReg] = item;
					 };	 			
				}	
	};				
	
  /**
  * private
  * Callback method for successful Ajax request.
  * Creates Components from responseText and  
  * and populates Components in Container.
  * @param {Object} response object from successful AJAX request.
  */
  var callback = function(res){
	   // ojo no evalua errores 	   
	   var JSON = Ext.decode(res.responseText).data;
		 
		 // modificado para eventos JCR 2008/07/01		 
		 function evalDinamic(text, id) {
		    return function event() {
				   var saleObject = null;
				   var object = Ext.getCmp(id); 
					 eval(text);
					 if ((saleObject != undefined) && (saleObject != null)) {
					    return saleObject;
					 }
					 return null; 
			  };
		 };		 
		 
		 function JsonEvents(item) {
		    // eventos
		    var listeners = item['listeners'];
		    if (listeners != null) {
		       for (var event in listeners) {			
			   		  listeners[event] = evalDinamic(listeners[event], item['id']);					 
			   	 }
				   item['listeners'] = listeners;
		    }		 
		    else {
		       delete item['listeners'];
		    }
				
		    // eventos de botones				
		    var buttons = item['buttons'];
				if (buttons != null) {
				   for (var i = 0; i < item['buttons'].length; i++) {		
		          if (buttons[i]['handler']) {			
					       buttons[i]['handler'] = evalDinamic(buttons[i]['handler'], 
								                                     buttons[i]['id']);					 
				      }
					 }		
				   item['buttons'] = buttons;
		    }		 
		    else {
		      delete item['buttons'];
		    }				
										
		    // botones de toolbar
			  var tbar = item['tbar'];
			  if (tbar != null) {
			     for (var i = 0; i < tbar.length; i++) {		
		          if (tbar[i]['handler']) {		
			 		       tbar[i]['handler'] = evalDinamic(tbar[i]['handler'], 
								                                  tbar[i]['id']);					 
				      }
					 }		
			  }
				
				// botones de botonbar
			  var bbar = item['bbar'];
			  if (bbar != null) {
			     for (var i = 0; i < bbar.length; i++) {		
		          if (bbar[i]['handler']) {		
			 		       bbar[i]['handler'] = evalDinamic(bbar[i]['handler'], 
								                                  bbar[i]['id']);					 
				      }
					 }		
			  }								
												
				if (item['items'] != undefined) {
				   for (var i = 0; i < item['items'].length; i++) {		
				      item['items'][i] = JsonEvents(item['items'][i])					 
				   }
				}	 								
		 		return item;
		 }
		 		 
		 JSON = JsonEvents(JSON);
		 
		 // registro en nombre de espacios global de widgets		 
		 function componentRegister(item) {
		    itemregister(item);	 
				
				// registra botones				
		    var buttons = item['buttons'];
				if (buttons != null) {
				   for (var i = 0; i < item['buttons'].length; i++) {		
					    itemregister(buttons[i]);
					 };		
		    }		 
		    
				if ((item.items != undefined) &&
				    (item.xtype != 'radiogroup') && (item.xtype != 'checkboxgroup')) {
				   for (var i = 0; i < item.items.getCount(); i++) {
					    //var itemr = item.items.itemAt(i);
					    componentRegister(item.items.itemAt(i));
							
							// botones top toolbar
							if ((item.items.itemAt(i).getTopToolbar != null) && 
							    (item.items.itemAt(i).getTopToolbar != undefined)) {
				         var tbar = item.items.itemAt(i).getTopToolbar();					 
 				         if ((tbar != null) && (tbar != undefined)) {								    
										for (var x = 0; x < tbar.length; x++) {
					             //itemregister(tbar[x]);
							      };	 					 
				         };
			        };
							
							// botones Bottom toolbar
							if ((item.items.itemAt(i).getBottomToolbar != null) && 
							    (item.items.itemAt(i).getBottomToolbar != undefined)) {
				         var tbar = item.items.itemAt(i).getBottomToolbar();										  
 				         if ((tbar != null) && (tbar != undefined)) {								   								   
				            for (var x = 0; x < tbar.length; x++) {
										   //itemregister(tbar[x]);
							      };	 					 
				         };
			        };												 
				   };
				};	 
		 }
		 
		 function componentPrerequisites(item) {
		    if ((item.xtype == "button") || (item.xtype == "formBtn"))  {
				   item.handler = evalDinamic(item.handler, item.id);		
				};
				
		    // tree necesita un nodo root por defecto
				if (item.xtype == "treepanel") {
				   var component = Ext.ComponentMgr.create(item.root, defaultType);
					 if (item.root.items != undefined) {
					    for (var i = 0; i < item.root.items.length; i++) {	
					       var node = Ext.ComponentMgr.create(item.root.items[i], 
								                                    defaultType);	
				         component.appendChild(node);				 
				      };
					 };		
					 item.root = component;	   
				}
				
				if (item.xtype == "grid") {				
				   var bufferedReader      = Ext.ComponentMgr.create(item.ds.reader, 
					                                                   defaultType);
					 item.ds.reader          = bufferedReader;
					 var bufferedDataStore   = Ext.ComponentMgr.create(item.ds, 
					                                                   defaultType);
					 item.ds                 = bufferedDataStore;
					 if (item.view.getRowClass) {
							item.view.getRowClass = evalDinamic(item.view.getRowClass, 
							                                    item.view.id);
					 }		
					 var bufferedView        = Ext.ComponentMgr.create(item.view, 
					                                                   defaultType);					 
					 item.bbar.view          = bufferedView;
					 item.view               = bufferedView;
					 var bufferedGridToolbar = Ext.ComponentMgr.create(item.bbar, 
					                                                   defaultType);
					 item.bbar               = bufferedGridToolbar;
					 //var bufferedGridTop     = Ext.ComponentMgr.create(item.tbar, 
					 //                                                  defaultType);
					 //item.bbar               = bufferedGridToolbar;
					 item.bbar               = bufferedGridToolbar;
					 item.sm                 = new Ext.ux.grid.BufferedRowSelectionModel();
					 item.cm                 = new Ext.grid.ColumnModel(item.cm.columns);
					 if (item['viewConfig']) {
					    item['viewConfig'].getRowClass = evalDinamic(item['viewConfig'].getRowClass, 
						                                               item['id'])
					 };	 
				}
				
				if ((item.xtype == "comboboxex") &&  (item.store != undefined)) {								
					 var proxy         = Ext.ComponentMgr.create(item.store.proxy, 
					                                             defaultType);
																											 																														 
           var reader        = Ext.ComponentMgr.create(item.store.reader, 
					                                             defaultType);
					 																						 
					 item.store.proxy  = proxy;
					 item.store.reader = reader;
					 var store         = Ext.ComponentMgr.create(item.store, 
					                                             defaultType);
					 item.store        = store;  
				}
				
				if ((item.xtype == "comboboxlov") && (item.store != undefined)) {								
					 var proxy         = Ext.ComponentMgr.create(item.store.proxy, 
					                                             defaultType);
																											 																														 
           var reader        = Ext.ComponentMgr.create(item.store.reader, 
					                                             defaultType);
					 																						 
					 item.store.proxy  = proxy;
					 item.store.reader = reader;
					 var store         = Ext.ComponentMgr.create(item.store, 
					                                             defaultType);
					 item.store        = store;  
				}
				
				if ((item.xtype == "dataview") && (item.store != undefined)) {								
					 var store  = Ext.ComponentMgr.create(item.store, defaultType);
					 item.store = store;
					 var tpl    = new Ext.XTemplate(item.tpl);
					 item.tpl   = tpl;  
				}		
				
				if ((item.items != undefined) &&
				    (item.xtype != 'radiogroup') && (item.xtype != 'checkboxgroup') ) {						
				   for (var i = 0; i < item.items.length; i++) {		
				      componentPrerequisites(item.items[i]);				 
				   };
				};	 				
		 }
		 		 
		 var component;  
		 
		 // termina modificado para eventos JCR 2008/07/01		 		 		 
		 if(this.fireEvent('beforecreate', JSON, this)){		    
		   	componentPrerequisites(JSON);					  
 		    component = Ext.ComponentMgr.create(JSON, defaultType);
				// carga informacion de llamado y apuntador a si mismo
				component._info       = this.config['info'];			
				component._widgetBase = component;	
				componentRegister(component);
				if(this.fireEvent('beforeadd', component, this)){
				   if (addContainer) this.container.add(component);					 
					 // simplifica acceso a formas con window y desde botones
					 if ((component.xtype == "window") && 
					     (component.initialConfig['_xtype'] == "formWindow")) {
							component.form           = component.items.itemAt(0);
							component.formData       = component.form.form;
							component.formData._info = this.config['info'];
							if (component.form.buttons != undefined) {
				         for (var i = 0; i < component.form.buttons.length; i++) {		
				            component.form.buttons[i].formData = component.formData;
										component.form.buttons[i].window   = component;
					       };
				      };						  																			    
				   };
					 
				   if(this.fireEvent('beforeshow', component, this)){
					    component.show();							
							if(this.fireEvent('beforecontainshow', component, this)){
						     if (addContainer) this.container.doLayout();
						     this.fireEvent('success', component, this);
					    } 					
				   } 				
			  } 				
		 }   
		 this.purgeListeners();
		 this.component = component;
		 
		 function deepShowCommands(item) {
		    // enlace a widget visual base
			  // botones de toptoolbar
				if ((item.getTopToolbar != null) && 
				    (item.getTopToolbar != undefined)) {
				   var tbar = item.getTopToolbar();					 
				   if ((tbar != null) && (tbar != undefined)) {
				      for (var i = 0; i < tbar.items.getCount(); i++) {
					       tbar.items.itemAt(i)._widgetBase = component;
								 itemregister(tbar.items.itemAt(i));
							};	 					 
				   };
			  };
				
				// botones de bottomtoolbar
				if ((item.getBottomToolbar != null) && 
				    (item.getBottomToolbar != undefined)) {
				   var tbar = item.getBottomToolbar();					 
				   if ((tbar != null) && (tbar != undefined)) {
				      for (var i = 0; i < tbar.items.getCount(); i++) {
					       tbar.items.itemAt(i)._widgetBase = component;
								 itemregister(tbar.items.itemAt(i));								 
							};	 					 
				   };
			  };
				
				// botones 
				if ((item['buttons'] != null) && (item['buttons'] != undefined)){
				   for (var i = 0; i < item['buttons'].length; i++) {
					 	  item['buttons'][i]._widgetBase = component;   
					 }  
			  }
				item._widgetBase = component;
			  				 
				if (item.addTo != undefined) {
				   var parent = Ext.getCmp(item.addTo);
					 if (parent) {// cuando adiciona sobre widget 
					    parent.add(item);
					    parent.doLayout(item);
					    // forzar que se borre la sombra ??????
					    item.setVisible(false);
					    item.setVisible(true);
					    // ajusta window a tamaño de contenedor
					    if (item.adjust > 0) {
					       var p = parent.getSize();
							   p.width  = p.width  - item.adjust;
							   p.height = p.height - item.adjust;
							   item.setSize(p);
				      }
					 }		
				}									
		 
				if ((item.items != undefined) && (item.items != null) &&
				    (item.xtype != 'radiogroup') && (item.xtype != 'checkboxgroup')) {						
				   for (var i = 0; i < item.items.getCount(); i++) {					    					    
					    deepShowCommands(item.items.itemAt(i));					 
				   };
				};	 
		 }		
		 	 
     deepShowCommands(component); 	 
	};
	
  /**
  * public
  * Processes the AJAX request.
  * Generally only called internal. Can be called external,
  * when processing has been stopped or defered by config
  * options breakOn or loadOn.
  */
	this.load = function(){
		if(this.fireEvent('beforeload', config, this)){
		  //params jcr			
			Ext.apply(config, {success : callback, 
			                   scope   : this})
		  config.params['_usuario'] = _usuario_info.id;										 
			Ext.Ajax.request(config);				
		} 
	};
	
  /**
  * public
  * Initialization method called by the Container.
  */
  this.init = function (container){	   
		 container.on('beforedestroy', function(){this.purgeListeners();}, this);
		 this.container = container;
		 if(config.loadOn){
		 	  var defer = function (){
				   this.load();
				   container.un(config.loadOn, defer, this);	
			  };
		    container.on(config.loadOn, defer, this);
		 }else{
			  this.load();	
		 }           
  };
	
	/**
  * public
  * Initialization method called by the Container.
  */
  this.initOnly = function (){
	  this.load();	
  };
	
	this.getComponent = function() {
	  return this.component;
	};
	
};
Ext.extend(Ext.ux.Plugin.RemoteComponent, Ext.util.Observable);