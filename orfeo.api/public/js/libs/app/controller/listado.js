Ext.define('ExtMVC.controller.listado', {
    extend: 'Ext.app.Controller',
 
    stores: ['listado'],
 
    models: ['listado'],
 
    views: ['listado.listadoGrid'],
 
    init: function() {
        this.control({
            'listadogrid button[action=showPreview]': {
                toggle: this.showPreview
            }
        });
    },
 
    showPreview: function(btn, pressed){
 
        var preview = Ext.ComponentQuery.query('listadogrid dataview')[0].plugins[0];
 
        preview.toggleExpanded(pressed);
    }
});