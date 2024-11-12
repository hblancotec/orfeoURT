Ext.Loader.setConfig({
    enabled: true
});

Ext.application({
    controllers: ['RadicacionMasiva.Radmasiva'],
    view: ['radicacionMasiva.Formulario','radicacionMasiva.FrmUploadZipPdf'],
    name: 'ExtMVC',
    paths: {
        'Ext.ux': '../public/js/libs/ext/ux/'
    },
    appFolder: '../public/js/libs/app',
    launch      : function() {
        Ext.create('ExtMVC.view.radicacionMasiva.Formulario');
    }
});