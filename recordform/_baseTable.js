/**
 * Ext.ux.grid.RecordForm Plugin Example Application
 *
 * @author    Ing. Jozef Sakáloš
 * @copyright (c) 2008, by Ing. Jozef Sakáloš
 * @date      31. March 2008
 * @version   $Id: _baseTable.js,v 1.3 2009/03/19 23:35:58 cgonzalez Exp $

 * @license recordform.js is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: http://www.gnu.org/licenses/lgpl.html
 */

Ext.BLANK_IMAGE_URL = '../ext/resources/images/default/s.gif';
Ext.state.Manager.setProvider(new Ext.state.CookieProvider);
Ext.override(Ext.form.Field, {msgTarget:'side'});

Ext.override(Ext.form.ComboBox, {
    // private
    initValue: Ext.form.ComboBox.prototype.initValue.createSequence(function() {
        /**
         * @cfg displayValue
         * A display value to initialise this {@link Ext.form.ComboBox}
         * (only useful for ComboBoxes with remote Stores, and having valueField != displayField).
         */
        if (this.mode == 'remote' && !!this.valueField && this.valueField != this.displayField && this.displayValue) {
            if (this.forceSelection) {
                this.lastSelectionText = this.displayValue;
            }
            this.setRawValue(this.displayValue);
        }
    })
});

Ext.override(Ext.form.ComboBox, {
   setValue : function(v){
      var text = v;
      this.fireEvent('beforechangevalue', this, v, this.startValue);
      if(this.valueField){
         var r = this.findRecord(this.valueField, v);
         if(r){
            text = r.data[this.displayField];
         }
         else if(this.valueNotFoundText !== undefined) {
                 text = this.valueNotFoundText;
         }
      }
      this.lastSelectionText = text;
      if(this.hiddenField){
         this.hiddenField.value = v;
      }
      Ext.form.ComboBox.superclass.setValue.call(this, text);
      this.value = v;
      this.fireEvent('changevalue', this, v, this.startValue);
   }
})

function baseComboDef() {
   var def = {
              editable      : false
             ,autoLoad      : true
             ,typeAhead     : false
             ,loadingText   : 'Cargando...'
             ,emptyText     : 'Seleccione..'
             ,width         : 100
             ,hideTrigger   : false
             ,mode          : 'remote'
             ,triggerAction : 'all'
             ,forceSelection: true
             ,lazyRender    : false
             ,lazyInit      : false
   }
   return def;
}

// forma de captura y edicion de datos
function baseRecordForm(fscope, title, id, columns) {
   title   = title   ? title   : '';
   id      = id      ? id      : 'ID';
   columns = columns ? columns : 2;

   var ifields = {};
   ifields[id] = true;

   return new Ext.ux.grid.RecordForm({
			 title         : title
			,iconCls       : 'icon-edit-record'
			,columnCount   : columns
			,ignoreFields  : ifields
			,readonlyFields: {action1:true}
			,disabledFields: {qtip1:true}
			,formConfig    : {
				           labelWidth:140
				          ,buttonAlign:'right'
				          ,bodyStyle:'padding-top:10px'
			                 }
		});
};

// acciones de fila
function baseRowActions(fscope) {
   if (fscope.makedelete) {
      var actionsarray = [{
	   			            iconCls:'icon-minus'
				            ,qtip:'Borrar Registro'
			              },{
				            iconCls:'icon-edit-record'
				           ,qtip:'Editar Registro'
			              }]
   }
   else {
      var actionsarray = [{
				            iconCls:'icon-edit-record'
				           ,qtip:'Editar Registro'
			              }]
   }

   return new Ext.ux.grid.RowActions({
			 actions:actionsarray
			,widthIntercept:Ext.isSafari ? 4 : 2
			,id:'actions'
		});
}

// data store
function baseStore(fscope, dfields, durl, id, sort) {
   id   = id   ? id   : 'ID';
   sort = sort ? sort : id;
   return new Ext.data.Store({
				reader:new Ext.data.JsonReader({
					 id           : id
					,totalProperty:'totalCount'
					,root         :'rows'
					,fields       : dfields
				})
				,proxy:new Ext.data.HttpProxy({url:durl})
				,baseParams:{cmd:'getData', objName:fscope.objName}
				,sortInfo:{field:sort, direction:'ASC'}
				,remoteSort:true
                                ,listeners:{
                                    remove:{scope:fscope, fn:fscope.deleteRecordAjx, buffer:200}
                                 }
			      })
};

// botones del grid
function baseButtons(that) {
   return [{
	     text   : 'Salvar'
	    ,iconCls: 'icon-disk'
	    ,scope  : that
	    ,handler: that.commitChanges
           },{
	      text   : 'Inicial'
	     ,iconCls: 'icon-undo'
	     ,scope  : that
	     ,handler: function() {
		          that.store.rejectChanges();
		       }
	   }]
};

// campos de busqueda
function baseSearch(fscope, readOnly, disable) {
   return new Ext.ux.grid.Search({
				 iconCls        : 'icon-zoom'
				,readonlyIndexes: readOnly
				,disableIndexes : disable
	      })
};

// barra top del grid
function baseTBar(fscope) {
   return [
           {
	     text:'Nuevo Registro'
	    ,tooltip:'Adiciona Registro Con Forma'
      	    ,iconCls:'icon-form-add'
	    ,listeners:{click:{scope : fscope,
                               buffer: 200,
                               fn    : function(btn) {
					 fscope.recordForm.show(
                                                                 fscope.addRecord(),
                                                                 btn.getEl()
                                                               );
				       }
                              }
			}
	   }]
};

// barra bottom del grid
function baseBBar(fscope) {
   return new Ext.PagingToolbar({
			 store      : fscope.store
			,displayInfo: true
			,pageSize   : 10
	      });
};

// crear nuevo registro
function baseAddRecord() {
   return function() {
      var store = this.store;
      if (store.recordType) {
         var rec = new store.recordType({newRecord:true});
         rec.fields.each(function(f) {
    			   rec.data[f.name] = f.defaultValue || null;
		        });
         rec.commit();
         store.add(rec);
         return rec;
      }
      return false;
   }
}

// rutina para borrado de registros
function baseDeleteRecord() {
   return function(record) {
      Ext.Msg.show({
         title  : 'Borrar Registro?'
	,msg    : 'Realmente desea Borrar Registro?, esta información no se podra recuperar.'
	,icon   : Ext.Msg.QUESTION
	,buttons: Ext.Msg.YESNO
	,scope  : this
	,fn     : function(response) {
	   if('yes' !== response) {
	      return;
	   }
           // &&& borrar registro
           this.store.remove(record);
	}
      });
   }
}

// rutina borrado ajax de registro
function baseDeleteRecordAjx() {
   return function(el, record, idx) {
      var o = {
           url      : this.url
          ,method   : 'post'
          ,callback : this.requestCallback
          ,scope    : this
          ,params:{
               cmd     : 'deleteData'
              ,objName : this.objName
              ,data    : Ext.encode(record.get(this.idName))
          }
      };
      Ext.Ajax.request(o);
   }
}

// acciones de fila borrar, editar
function baseOnRowAction() {
   return function(grid, record, action, row, col) {
      switch(action) {
         case 'icon-minus':
	    this.deleteRecord(record);
            break;

         case 'icon-edit-record':
	    this.recordForm.show(record, grid.getView().getCell(row, col));
	    break;
      }
   }
}

// salvar cambios y nuevos regsitros
function baseCommitChanges() {
   return function() {
      var records = this.store.getModifiedRecords();
      if(!records.length) {
         return;
      }
      var data = [];
      Ext.each(records, function(r, i) {
			var o = r.getChanges();
			if(r.data.newRecord) {
			   o.newRecord = true;
			}
			o[this.idName] = r.get(this.idName);
		        data.push(o);
                     }, this);

       var o = {
                url      : this.url
	       ,method   : 'post'
	       ,callback : this.requestCallback
	       ,scope    : this
	       ,params:{
	 		 cmd    : 'saveData'
 			,objName: this.objName
			,data   : Ext.encode(data)
		       }
	      };
       Ext.Ajax.request(o);
   }
}

// retorna de (CommitChanges) salvar cambios y nuevos regsitros
function baseRequestCallback(fscope) {
   return function(options, success, response) {
		if(true !== success) {
		    this.showError(response.responseText);
		    return;
		}
		try {
		    var o = Ext.decode(response.responseText);
		}
		catch(e) {
		    this.showError(response.responseText, 'Cannot decode JSON object');
		    return;
		}
		if(true !== o.success) {
		    this.showError(o.error || 'Error Desconocido');
		    return;
		}

		switch(options.params.cmd) {
		    case 'saveData':
			var records = this.store.getModifiedRecords();
			Ext.each(records, function(r, i) {
					     if (o.insertIds && o.insertIds[i]) {
					        r.set(this.idName, o.insertIds[i]);
						delete(r.data.newRecord);
					     }
				          });
			this.store.commitChanges();
			break;

		    case 'deleteData':
			break;
		}
	  }
}

// muestra mensajes de error
function baseShowError() {
   return function(msg, title) {
       Ext.Msg.show({
	     	     title   : title || 'Error'
		    ,msg     : Ext.util.Format.ellipsis(msg, 2000)
		    ,icon    : Ext.Msg.ERROR
		    ,buttons : Ext.Msg.OK
		    ,minWidth: 1200 > String(msg).length ? 360 : 600
		   });
   }
}

/**************************************/
/******** BASE WINDOWS ****************/
/**************************************/
// ventana base para el grid
function baseWindow(p) {
   p.id      = p.id      ? p.id      : 'idwin';
   p.title   = p.title   ? p.title   : '';
   p.xtype   = p.xtype   ? p.xtype   : p.xtype;
   p.idxtype = p.idxtype ? p.idxtype : p.xtype;
   p.x       = p.x       ? p.x       : 300;
   p.y       = p.y       ? p.y       : 80;
   p.width   = p.width   ? p.width   : 700;
   p.height  = p.height  ? p.height  : 450;

   return new Ext.Window({
		 id         : p.id
                ,title      : p.title
		,iconCls    : 'icon-grid'
		,width      : p.width
		,height     : p.height
//		,stateful: false
		,x          : p.x
		,y          : p.y
		,plain      : true
		,layout     : 'fit'
		,closable   : true
		,border     : false
		,maximizable: true
		,items      : {xtype:p.xtype, id:p.idxtype}
		,plugins    : [new Ext.ux.menu.IconMenu()]
              });
}

// creacion de window del grid
function baseGridPanel(p) {
   //var grid = Ext.extend(Ext.grid.EditorGridPanel, {
   var grid = Ext.extend(Ext.grid.GridPanel, {
	 layout    : 'fit'
	,border    : false
	,stateful  : false
	,url       : p.url
	,objName   : p.objName
	,idName    : p.idName
	,makedelete: p.makedelete

	,initComponent:function() {
                this.recordForm = baseRecordForm(this, p.title, p.idName, p.columnForm);
		this.rowActions = baseRowActions(this);
                this.deleteRecordAjx  = baseDeleteRecordAjx();
		Ext.apply(this, {
			store: baseStore(this, p.fields, this.url, p.idName, p.sortName)
		});
		this.bbar            = baseBBar(this)
                this.addRecord       = baseAddRecord();
	        this.commitChanges   = baseCommitChanges();
                this.requestCallback  = baseRequestCallback();
                Ext.apply(this, {
			columns   : p.columns.concat([this.rowActions])
		       //,plugins   : [baseSearch(this, [], []), this.rowActions, this.recordForm]
                       ,plugins   : [this.rowActions, this.recordForm]
		       ,viewConfig: {forceFit:true}
		       ,buttons   : baseButtons(this)
		       ,tbar      : baseTBar(this)
		});
		grid.superclass.initComponent.apply(this, arguments);
                this.showError        = baseShowError();
                this.onRowAction      = baseOnRowAction();
                this.rowActions.on('action', this.onRowAction, this);
	        this.deleteRecord     = baseDeleteRecord();
	}

        ,onRender:function() {
	   grid.superclass.onRender.apply(this, arguments);
           this.store.load({params:{start:0,limit:10}});
	}
   });
   Ext.reg(p.nameGrid, grid);
   return grid;
};