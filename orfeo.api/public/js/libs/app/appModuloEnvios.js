Ext.Loader.setConfig({

});

Ext.application({
    views: [
        'Administracion.Dependencias.cmbDependencia'
    ],
    controllers: [
        'Envios.index',
        'Envios.normal'
    ],
    name: 'ExtMVC',
	appFolder: '/orfeo.api/public/js/libs/app',
	namespaces: [
		'Envios',
		'Dependencia'
    ],
    launch: function() {
        Ext.create('ExtMVC.view.Envios.PanelIndex');
    }

});