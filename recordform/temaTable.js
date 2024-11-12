
/**** OTRAS DEFINICIONES ****/
var columnsData = [{ header   : "Nombre del Tema"
		    ,id       : 'SGD_TEMA_NOMBRE'
		    ,dataIndex: 'SGD_TEMA_NOMBRE'
		    ,width    : 300
		    ,sortable : true
		    ,editor   : new Ext.form.TextField({
			            allowBlank:false
			 	})
		    }
		   ,{ header   : "Tema Activo (SI/NO)"
		    ,id       : 'SGD_TEMA_ACTIVO'
		    ,dataIndex: 'SGD_TEMA_ACTIVO'
		    ,width    : 120
		    ,sortable : true
		    ,editor   : new Ext.form.TextField({
			            allowBlank:false
			 	})
		    }
                    ];

var dataFields = [ {name:'ID'             ,   type:'int'}
		  ,{name:'SGD_TEMA_NOMBRE', type:'string'}
		  ,{name:'SGD_TEMA_ACTIVO', type:'string'}
		 ]

var p = {
          nameGrid  : 'orfeo_tematable'
         ,title     : 'Manejo de Temas'
         ,url       : 'tema-request.php'
         ,objName   : 'SGD_TEM_NOMBRES'
         ,idName    : 'ID'
         ,sortName  : 'SGD_TEMA_NOMBRE'
         ,columns   : columnsData
         ,fields    : dataFields
         ,columnForm: 1
         ,makedelete: false
        }
grid = baseGridPanel(p);

// app entry point
Ext.onReady(function() {
   Ext.QuickTips.init();

   var p   = { id     : 'winTemaTable'
              ,title  : 'Manejo de Temas'
              ,xtype  : 'orfeo_tematable'}
   var win = baseWindow(p)
   win.show();
})