var grid;
Ext.application({
    name: 'ExtMVC',
 
    paths: { 'Ext.ux':  (config.pathMVC? config.pathMVC:'') +'public/js/libs/ext-4.2.1/ux/' },
    appFolder:  (config.pathMVC? config.pathMVC:'') +'public/js/libs/app',
    controllers: [
        config.controller
    ]//,
    //,autoCreateViewport: true
    ,launch: function() {
       //Este es el Inicio para pintar la Grilla en el Div Correspondiente.
       grid=new ExtMVC.view.listaCarpeta.listaCarpetaGrid({
                renderTo: 'div-listado'
                
            });; 
    }

});
