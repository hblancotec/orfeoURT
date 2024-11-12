<html>
<head>
<title>.:Orfeo - Administrador de Dias Festivos.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../../estilos/orfeo.css">
	 
    <link rel="stylesheet" type="text/css" href="../../extjs/resources/css/ext-all.css">	        
    <script type="text/javascript" src="../../extjs/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="../../extjs/ext-all-debug.js"></script>
    
</head>

<body>        
<script language="JavaScript">
  noCache = function() {
     return new Date().getTime();
  };
  
  getDiafestivo = function() {
     var diafestivo = new Ext.form.TextField({
        fieldLabel: 'Dia Festivo (DD-MM-AAAA)',
        allowBlank: false,
        name      : 'SGD_FESTIVO',
        anchor    : '90%' 
     });
     return diafestivo;
  };
  
  getFormPanel = function(urlFP) {    
     var formPanel = new Ext.form.FormPanel({
        baseCls    : 'x-plain',
        labelWidth : 180,
        url        : urlFP,
        defaultType: 'textfield',
        items      : [getDiafestivo()]
      });
      return formPanel
  }
  
  getWindow = function(titleW, formPanelW, buttonsW) {    
     var window = new Ext.Window({
        title      : titleW,
        width      : 350,
        height     : 120,
        minWidth   : 350,
        minHeight  : 120,
        layout     : 'fit',
        plain      : true,
        bodyStyle  : 'padding:5px;',
        buttonAlign: 'center',
        items      : formPanelW,
        buttons    : buttonsW
     })                
     return window;
  }   
 
  getReader = function() {
     var reader = new Ext.data.JsonReader({
           root            : 'rows',
           totalProperty   : 'totalCount',
           successProperty : 'success',
           id              : 'SGD_ID'
        },
        [{name: 'SGD_ID'},
         {name: 'SGD_FESTIVO'}]
     );     
     return reader;
  } 
   
  CreateFestivo = function(dataStore) {
     var formPanel = getFormPanel('dias_fest.php?cmd=saveData');
     var buttons   =
         [{
             text: 'Salvar', 
             handler: function() {
                if (formPanel.form.isValid()) {
 	   	   formPanel.form.submit({			       
		      waitMsg: 'In processing',
                      params : {dc: noCache()},	
		      failure: function(form, action) {
			 Ext.MessageBox.alert('Error Mensaje', action.result.errorInfo);
		      },
		      success: function(form,action) {
		         Ext.MessageBox.alert('Confirmar', action.result.info);
		         window.hide();
			 dataStore.load();
                      }	
		   });                   
                }
                else{
		   Ext.MessageBox.alert('Erroress', 'Por favor corrija.');
		}             
	     }
         },
         {
            text   : 'Cancel',
            handler: function(){window.hide();}
         }]
    
    var window    = getWindow('Nuevo Dia Festivo', formPanel, buttons);        
    window.show();
  };
  
  DeleteFestivo = function(dataStore, gridPanel) {
    var m = gridPanel.getSelections();
    if(m.length > 0)
    {
        Ext.MessageBox.confirm('Mensaje', 'esta seguro?', 
    	   function(btn) {
	      if(btn == 'yes')
	      {
                  jsonData = '[';
		  for(var i = 0, len = m.length; i < len; i++) {        		
		     var ss = m[i].get("SGD_ID") + "";
		     if(i==0)
			jsonData = jsonData + ss ;
		     else
			jsonData = jsonData + "," + ss;			     						
		  }	
		  jsonData = jsonData + "]";
                  
                  var conn = new Ext.data.Connection();
                  conn.request({
                     url     : 'dias_fest.php?cmd=deleteData',
                     method  : 'POST',
                     params  : {deleteIds: jsonData, dc: noCache()},	      
		     waitMsg : 'Procesando...',
                     failure: function(form, action) {
		        Ext.MessageBox.alert('Error', "Error");
		     },
		     success: function(res) {
                        Ext.MessageBox.alert('Confirmar', "Ok");
		        dataStore.load();
		     }
                  });
	      }
	   } 
	);	
    }
    else
    {
       Ext.MessageBox.alert('Error', 'Seleccione dia a a borrar');
    }       
  };  
  
  EditFestivo = function(dataStore, selectedId) {        
    var formPanel = new Ext.form.FormPanel({        
       baseCls   : 'x-plain',
       labelWidth: 180,
       url       : 'dias_fest.php?cmd=saveData',
       items     : [getDiafestivo()],        
       reader    : getReader()
    });

    // load form and assign value to fields
    formPanel.form.load({url    : 'dias_fest.php?cmd=editData&id='+selectedId, 
                         waitMsg: 'Loading'});    
    
    var buttons =
       [{
          text   : 'Salvar', 
          handler: function() {                
             if (formPanel.form.isValid()) {                
	        formPanel.form.submit({		
	 	   params : {id: selectedId, dc: noCache()},	      
		   waitMsg: 'Procesando...',
		   failure: function(form, action) {
		      Ext.MessageBox.alert('Error', action.result.errorInfo);
		   },
		   success: function(form, action) {
		      Ext.MessageBox.alert('Confirmar', action.result.info);
		      window.hide();
		      dataStore.load();
		   }
                });                   
             }
             else {
	        Ext.MessageBox.alert('Errores', 'Corrija Por Favor');
             }             
	  }
        },
        {
           text   : 'Cancelar',
           handler: function(){window.hide();}
        }]    
            
    var window    = getWindow('Editar Dia Festivo', formPanel, buttons);    
    window.show();
  };
      
Ext.onReady(function() {
    Ext.QuickTips.init();   
    Ext.BoxComponent.prototype.setSize('100%','25px');

    var userCM = new Ext.grid.ColumnModel([
        new Ext.grid.RowNumberer(),
        {
           id: 'SGD_ID',
           header: "ID",
           dataIndex: 'SGD_ID',
           width: 150,
           hidden: true
        },{
            header: "Dia Festivo", 
            width: 100, 
            dataIndex: 'SGD_FESTIVO'
        }
    ]);   
	    
    var dataStore = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
           url: 'dias_fest.php?cmd=getData',
           disableCaching : true
        }),
        
        reader: getReader()
    });    
    dataStore.load();	

    var myPagingToolbar = new Ext.PagingToolbar({
        pageSize   : 30,
        displayInfo: true,
        displayMsg : 'total {2} items. Se Muestra {0} - {1}',
        emptyMsg   : 'sin datos para mostrar',
        store      : dataStore
    });
       	    
    var menubar = [{
        text   :'Nuevo Dia Festivo',
        tooltip:'Nuevo Dia Festivo',
        iconCls:'addItem',
        handler: function(){            
           CreateFestivo(dataStore);
        }
    },'-',{
        text   :'Borrar Dia Festivo',
        tooltip:'Borrar Dia Festivo',
        iconCls:'remove',
        handler: function(){
           DeleteFestivo(dataStore, gridPanel);
        }
    }];
    
    var gridPanel = new Ext.grid.GridPanel({
        border: false,	                        
        ds    : dataStore,
        cm    : userCM,	
        viewConfig: {forceFit:true},	                        
        tbar  : menubar,
        bbar  : myPagingToolbar
    });
    
    win = new Ext.Window({
        id     : 'diafestivo',
        title  : 'Manejo de Dias Festivos',
        width  : 700,
        height : 400,
        x      : 100,
        y      : 30,
        iconCls: 'demo',
        shim   : false,
        animCollapse:false,
        constrainHeader:true,

        layout : 'fit',
        items  : gridPanel
    });
    
    win.show();     
    
    gridPanel.on('rowdblclick', function(gridPanel, rowIndex, e) {
	    var selectedId = dataStore.data.items[rowIndex].id;  
	    new EditFestivo(dataStore, selectedId);  
    });    
});  
</script>
        
</body>
</html>