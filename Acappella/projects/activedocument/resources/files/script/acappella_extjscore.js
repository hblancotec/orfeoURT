// registra tree node
Ext.reg('treenode',            Ext.tree.TreeNode);
Ext.reg('asynctreenode',       Ext.tree.AsyncTreeNode);
Ext.reg('bufferedjsonreader',  Ext.ux.data.BufferedJsonReader);
Ext.reg('bufferedstore',       Ext.ux.grid.BufferedStore);
Ext.reg('bufferedgridview',    Ext.ux.grid.BufferedGridView);
Ext.reg('bufferedgridtoolbar', Ext.ux.BufferedGridToolbar);
Ext.reg('columnmodel',         Ext.grid.ColumnModel);
Ext.reg('datastore',           Ext.data.Store);
Ext.reg('jsonstore',           Ext.data.JsonStore);
Ext.reg('httpproxy',           Ext.data.HttpProxy);
Ext.reg('jsonreader',          Ext.data.JsonReader);
Ext.reg('comboboxex',          com.succinctllc.form.ComboBox);
Ext.reg('comboboxlov',         Ext.ux.form.LovCombo);
//Ext.reg('comboboxex',          Ext.form.ComboBox);

// informacion del usuario
_usuario_info = {'id':-1}

// Tooltips on form fields
// habilitar tooltip en cada field
// tomado de http://www.rowlands-bcs.com/?q=node/11
Ext.form.Field.prototype.msgTarget = 'side';

Ext.override(Ext.form.Field, {
    onRender : function(ct, position){
        Ext.form.Field.superclass.onRender.call(this, ct, position);
        if(!this.el){
            var cfg = this.getAutoCreate();
            if(!cfg.name){
                cfg.name = this.name || this.id;
            }
            if(this.inputType){
                cfg.type = this.inputType;
            }
            if(this.tooltip){						    
                cfg['ext:qtip'] = this.tooltip.tip;
                cfg['ext:qwidth'] = this.tooltip.width || 100;
            }
            this.el = ct.createChild(cfg, position);
        }
        var type = this.el.dom.type;
        if(type){
            if(type == 'password'){
                type = 'text';
            }
            this.el.addClass('x-form-'+type);
        }
        if(this.readOnly){
            this.el.dom.readOnly = true;
        }
        if(this.tabIndex !== undefined){
            this.el.dom.setAttribute('tabIndex', this.tabIndex);
        }
        
        this.el.addClass([this.fieldClass, this.cls]);
        this.initValue();
    }
});

// carga componente remoto sobre derivado de container
getRemoteComponentPlugin = function(options, add, initOnly, hideCurrent) {   	 
   if ((hideCurrent == null) || (hideCurrent == undefined)) hideCurrent = true;
	 
	 var currentWindow = Ext.WindowMgr.getActive();	 
	 if ((currentWindow != null) && (hideCurrent==true)) {
	    currentWindow.hide();
	 };
	 
	 if ((options['params'] != undefined) && (options['params']['name'] != undefined)) {	    
			var comp = Ext.getCmp(options['params']['name']);
			// si el widget existe, no lo llama..
			if (comp != undefined) {
				 // asume widget visual
				 comp._info = options['info'];
				 // simplificar acceso en formas window de edicion y consulta de datos
				 if ((comp.xtype == "window") && (comp.initialConfig['_xtype'] == "formWindow")) {				    
	          comp.form.form._info = options['info'];			 
				 };
 
         if (options.listeners) {
	          //for (var l in options.listeners) {						
		        //   comp.addEvents(l);				
		        //};
						
						for (var l in options.listeners) {						
		           comp.addListener(l, options.listeners[l]);				
		        };		 		 
	       };
				 
				 //comp.fireEvent('beforecomponshow', comp, comp)				 
				 
   	     comp.show();
				 // informacion del llamado, es particular de cada llamado
				 return comp;
			};	 
 	 };
	 var comp = new Ext.ux.Plugin.RemoteComponent(options, add);	 
	 if (options.listeners) {
	   for (var l in options.listeners) {
		    comp.on(l, options.listeners[l]);				
		 }		 		 
	 }
	 if (initOnly) comp.initOnly(); 
   return comp; 
};

// forma Acappella
Ext.FormAcappella = function(config) {
    // asignacion dinamica de eventos respuesta Ajax
    function eventResponseDynamic(textD) {
		   return function(form, action) {			    
			    var response = Ext.decode(action.response.responseText);
				  eval(textD);
			 };
		};		    
		
    // evento respuesta correcta ajax
		var onSuccess =  function(form, action) {
			   alert("sucess:"+action.response.responseText);
		};
		
		var onSuccessEvent = onSuccess;
		if (config._success != undefined) {
		   onSuccessEvent = eventResponseDynamic(config._success)
		}		
    
		// evento respuesta incorrecta ajax
		var onFailure =  function(form, action) {		
		     if (action.failureType == "client") {
				    showError("Información incompleta o invalida");
				 }
				 else { 
		        showError(action.result.error || action.response.responseText);
				 };		
		};
		
		var onFailureEvent = onFailure;
		if (config._failure != undefined) {
		   onFailureEvent = eventResponseDynamic(config._failure)
		}			
		  
    config.onSubmit = Ext.emptyFn;
    config.submit   =  function() {	
		   this.form.doAction('submit', {
          url     : this.url,
          scope   : this,
          success : onSuccessEvent,
          failure : onFailureEvent,
          waitMsg : 'Enviando...',
					clientValidation:true
       }, {});			 			 
    };
		
		// call parent constructor
    Ext.FormAcappella.superclass.constructor.call(this, config); 		
};

Ext.extend(Ext.FormAcappella, Ext.FormPanel);
Ext.reg('formAcappella', Ext.FormAcappella);

// *********************** //
// ****** extensiones **** //
// *********************** //
Ext.override(Ext.form.CheckboxGroup, {
    /**
     * @cfg {String} name The field's HTML name attribute (defaults to "").
     */

    /**
     * @cfg {string} separator String seperator between multiple values
     */
    separator: ';',
    
    // private
    afterRender : function() {
        this.items.each(function(i) {
            i.ownerGroup = this; // kind of lame hack
        }, this);
        Ext.form.CheckboxGroup.superclass.afterRender.call(this);
    },
    
    /**
     * @method initValue
     * @hide
     */
    initValue : function(){
        if(this.value !== undefined){
            this.setValue(this.value);
        }
    },
    
    /**
     * @method getValue
     * @hide
     */
    getValue : function() {
        if(!this.rendered) {
            return this.value;
        }
        var v = [];
        this.items.each(function(i) {
            if(i.getValue()) v.push(i.inputValue);
        });		
        return v.join(this.separator);
    },
    
    /**
     * @method setValue
     * @hide
     */
    setValue :  function(v) {
				v = "" + v;
				this.value = v;
        if(this.rendered){
            v = v.split(this.separator);
            this.items.each(function(i) {
                i.setValue(v.indexOf(i.inputValue) >= 0);
            }, this);
            this.validate();
        }
    },

    /**
     * Returns the name attribute of the field if available
     * @return {String} name The field name
     */
    getName: function(){
         return this.name;
    }
});


Ext.override(Ext.form.Radio, {   
    // private
    toggleValue : function() {
        if(!this.checked){
            // notify owning group that value changed
            if (this.ownerGroup) {
                this.ownerGroup.setValue(this.inputValue);
            }
            else {
                var els = this.getParent().select('input[name=' + this.el.dom.name + ']');
                els.each(function(el){
                    if (el.dom.id == this.id) {
                        this.setValue(true);
                    }
                    else {
                        Ext.getCmp(el.dom.id).setValue(false);
                    }
                }, this);				
            }
        }
    }
});

/***
 * Formlayout fix (only add items to form if name set)
 */
Ext.override(Ext.FormPanel, {
    initFields : function(){
        var f = this.form;
        var formPanel = this;
        var fn = function(c){
            if(c.isFormField && c.name){ // only use formfields with a name?
                f.add(c);
            }else if(c.doLayout && c != formPanel){
                Ext.applyIf(c, {
                    labelAlign: c.ownerCt.labelAlign,
                    labelWidth: c.ownerCt.labelWidth,
                    itemCls: c.ownerCt.itemCls
                });
                if(c.items){
                    c.items.each(fn);
                }
            }
        }
        this.items.each(fn);
    },
        
    onAdd : function(ct, c) {
        if (c.isFormField && c.name) {
            this.form.add(c);
        }
    }
});

Ext.override(Ext.form.CheckboxGroup, {
  
    afterRender: function() {
        var that = this;
        this.items.each(function(i) {
            that.relayEvents(i, ['check']);
        });
        
        Ext.form.CheckboxGroup.superclass.afterRender.call(this)
    }
});