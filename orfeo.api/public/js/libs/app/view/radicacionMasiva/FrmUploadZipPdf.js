Ext.define('ExtMVC.view.radicacionMasiva.FrmUploadZipPdf', {
    extend: 'Ext.form.Panel',
    alias: 'widget.uploadzippdfmasivaform',
    requires: ['Ext.form.field.File',
        'Ext.button.Button'],
    itemId: 'FrmUploadZipPdf',
    title: 'Carga de Archivo Zip de plantillas.',
    width: 350,
    heigth: 250,
    floating: true,
    closable: true,
    modal: true,
    renderTo: Ext.getBody(),
    layout: 'fit',
    items: [{// Let's put an empty grid in just to illustrate fit layout
            xtype: 'filefield',
            fieldLabel: 'Archivo',
            name: 'FfdZipPlantillaFrmRadicacionmasiva',
            itemId: 'FfdZipPlantillaFrmRadicacionmasiva',
            allowBlank: false,
            allowOnlyWhitespace: false,
            emptyText: 'Seleccione archivo datos (zip)',
            regex: /^.*\.(zip|ZIP)$/,
            regexText: 'Solo se permite archivo con extensi&oacute;n zip',
            msgTarget: 'under',
            listeners: {
                change: function (fld, value) {
                	var newValue = value.replace(/(^.*(\\|\/))?/, "");
                    fld.setRawValue(newValue);
                }
            }
        }],
    buttons: [{
            text: 'Cargar',
            formBind: true,
            itemId: 'btnUpFrmUploadZipPdf'
        }]
});