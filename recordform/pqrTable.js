/*************** TIPO DOCUMENTAL ********/
var dstipo = new Ext.data.Store({
   autoLoad: true,
   proxy: new Ext.data.HttpProxy ({
      url: 'process-request.php?cmd=data_tipo'
   }),
   reader: new Ext.data.JsonReader({
                 root         : 'rows'
                ,totalProperty: 'totalCount'
                ,id           : 'SGD_TPR_CODIGO'}
                ,[ {name:'SGD_TPR_CODIGO',  id:'SGD_TPR_CODIGO'}
		  ,{name:'SGD_TPR_DESCRIP', id:'SGD_TPR_DESCRIP'}]
   )
});

var baseDef = baseComboDef();
baseDef.store        = dstipo;
baseDef.displayField = 'SGD_TPR_DESCRIP';
baseDef.id           = 'tipo';
baseDef.valueField   = 'SGD_TPR_CODIGO';
var tipo = new Ext.form.ComboBox(baseDef);

/*************** DEPENDENCIA ********/
var dsdependencia = new Ext.data.Store({
   autoLoad: true,
   proxy: new Ext.data.HttpProxy ({
      url: 'process-request.php?cmd=data_dependencia'
   }),
   reader: new Ext.data.JsonReader({
                 root: 'rows'
                ,totalProperty: 'totalCount'
                ,id: 'DEPE_CODI'}
                ,[ {name:'DEPE_CODI', id:'DEPE_CODI'}
		  ,{name:'DEPE_NOMB', id:'DEPE_NOMB'}]
   )
});

var baseDef = baseComboDef();
baseDef.store        = dsdependencia;
baseDef.displayField = 'DEPE_NOMB';
baseDef.id           = 'dependencia';
baseDef.valueField   = 'DEPE_CODI';
baseDef.listeners    = {
                         select: {
                                    fn:function() {
                                       var usucombo = Ext.getCmp('usuario');
                                       usucombo.setValue(null); //fire changevalue
                                    }
                                 }
                       };
var dependencia = new Ext.form.ComboBox(baseDef);

/*************** FUNCIONARIO ********/
var dsusuario = new Ext.data.Store({
   autoLoad: true,
   proxy: new Ext.data.HttpProxy ({
      url: 'process-request.php?cmd=data_usuario'
   }),
   reader: new Ext.data.JsonReader({
                 root: 'rows'
                ,totalProperty: 'totalCount'
                ,id: 'USUA_CODI'}
                ,[ {name:'USUA_CODI', id:'USUA_CODI', type:'int'}
		  ,{name:'USUA_NOMB', id:'USUA_NOMB'}]
   )
});

var baseDef = baseComboDef();
baseDef.store        = dsusuario;
baseDef.displayField = 'USUA_NOMB';
baseDef.id           = 'usuario';
baseDef.valueField   = 'USUA_CODI';
baseDef.listeners    = {
                         beforechangevalue: {
                                   fn:function(field, newValue, oldValue) {
                                      var dep      = Ext.getCmp('dependencia').getValue();
                                      var usucombo = Ext.getCmp('usuario');
                                      function callbackchange() {
                                         usucombo.dep = dep;
                                         var r = usucombo.findRecord(usucombo.valueField, newValue);
                                         if (r) {
                                            usucombo.setRawValue(r.data[usucombo.displayField]);
                                         }
                                      }
                                      if (usucombo.dep != dep) {
                                         usucombo.store.removeAll();
                                         usucombo.clearValue();
                                         usucombo.store.proxy = new Ext.data.HttpProxy({url: 'process-request.php?cmd=data_usuario&dep='+dep});
                                         usucombo.store.load({callback: callbackchange, scope:this});
                                      }
                                   }
                                 }
                       };
var usuario = new Ext.form.ComboBox(baseDef);

/**** OTRAS DEFINICIONES ****/
var columnsData = [{ header   : "Titulo WEB"
		    ,id       : 'SGD_PQR_LABEL'
		    ,dataIndex: 'SGD_PQR_LABEL'
		    ,width    : 300
		    ,sortable : true
		    ,editor   : new Ext.form.TextField({
			            allowBlank:false
			 	})
		    }

                   ,{ header   : "Descripción"
		    ,id       : 'SGD_PQR_DESCRIP'
		    ,dataIndex: 'SGD_PQR_DESCRIP'
		    ,width    : 300
		    ,sortable : false
		    ,editor   : new Ext.form.TextArea({
			            allowBlank:false
			 	})
		    }

                   ,{ header   : 'Dependencia'
		     ,dataIndex: 'SGD_PQR_DEPE'
		     ,width    : 200
		     ,sortable : false
		     ,editor   : dependencia
                     ,hidden   : true
                    }

                   ,{ header   : 'Usuario'
		     ,dataIndex: 'SGD_PQR_USUA'
		     ,width    : 200
		     ,sortable : false
		     ,editor   : usuario
                     ,hidden   : true
                    }

                   ,{ header   : 'Tipo Documental'
		     ,dataIndex: 'SGD_PQR_TPD'
		     ,width    : 200
		     ,sortable : false
		     ,editor   : tipo
                     ,hidden   : true
                    }
                    ];

var dataFields = [ {name:'ID'           ,   type:'int'}
		  ,{name:'SGD_PQR_LABEL',   type:'string'}
                  ,{name:'SGD_PQR_DESCRIP', type:'string'}
                  ,{name:'SGD_PQR_DEPE',    type:'int'}
                  ,{name:'SGD_PQR_USUA',    type:'int'}
                  ,{name:'SGD_PQR_TPD',     type:'int'}
		 ]

var p = {
          nameGrid  : 'orfeo_pqrtable'
         ,title     : "Titulos PQR's"
         ,url       : 'process-request.php'
         ,objName   : 'SGD_PQR_MASTER'
         ,idName    : 'ID'
         ,sortName  : 'SGD_PQR_LABEL'
         ,columns   : columnsData
         ,fields    : dataFields
         ,columnForm: 1
         ,makedelete: true
        }
grid = baseGridPanel(p);

// app entry point
Ext.onReady(function() {
   Ext.QuickTips.init();

   var p   = { id     : 'winPqrTable'
              ,title  : 'Etiquetas de PQR'
              ,xtype  : 'orfeo_pqrtable'}
   var win = baseWindow(p)
   win.show();
})