Ext.define('ExtMVC.controller.listaCarpeta', {
    extend: 'Ext.app.Controller',
 
    stores: ['listaCarpeta'],
 
    models: ['listaCarpeta'],
 
    views: ['listaCarpeta.listaCarpetaGrid'],
 
    init: function() {
        this.control({
            'listadogrid button[action=showPreview]': {
                toggle: this.showPreview
            }
        });
    },
 
    showPreview: function(btn, pressed){
 
        var preview = Ext.ComponentQuery.query('listacarpetagrid dataview')[0].plugins[0];
 
        preview.toggleExpanded(pressed);
    }
});