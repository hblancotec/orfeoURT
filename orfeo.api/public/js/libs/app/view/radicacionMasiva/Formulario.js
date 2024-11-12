Ext.define('ExtMVC.view.radicacionMasiva.Formulario', {
    extend: 'Ext.form.Panel',
    alias: 'widget.radicacionmasivaform',
    requires: [
        'ExtMVC.view.radicacionMasiva.FrmUploadZipPdf',
        'Ext.form.field.File',
        'Ext.button.Button',
        'Ext.toolbar.Toolbar',
        'Ext.ux.grid.DynamicGrid'],
    title: 'Radicaci&oacute;n Masiva',
    itemId: 'FrmRadicacionmasiva',
    renderTo: Ext.getBody(),
    bodyPadding: 5,
    layout: 'fit',
    dockedItems: [
        {
            xtype: 'panel',
            bodyPadding: 5,
            dock: 'top',
            itemId: 'PanelFilesFrmRadicarmasiva',
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [
                {
                    xtype: 'filefield',
                    flex: 0,
                    name: 'FfdPlantillaFrmRadicacionmasiva',
                    itemId: 'FfdPlantillaFrmRadicacionmasiva',
                    fieldLabel: 'Plantilla:',
                    allowBlank: false,
                    allowOnlyWhitespace: false,
                    emptyText: 'Seleccione archivo plantilla (docx, odt, xml)',
                    regex: /^.*\.(xml|XML|docx|DOCX|odt|ODT)$/,
                    regexText: 'Solo se permite archivo con extensi&oacute;n docx, odt y xml',
                    msgTarget: 'under',
                    listeners: {
                        change: function (fld, value) {
                        	var newValue = value.replace(/(^.*(\\|\/))?/, "");
                            fld.setRawValue(newValue);
                        }
                    }
                },
                {
                    xtype: 'filefield',
                    flex: 0,
                    name: 'FfdDatosFrmRadicacionmasiva',
                    itemId: 'FfdDatosFrmRadicacionmasiva',
                    fieldLabel: 'Datos:',
                    allowBlank: false,
                    allowOnlyWhitespace: false,
                    emptyText: 'Seleccione archivo datos (csv)',
                    regex: /^.*\.(csv|CSV)$/,
                    regexText: 'Solo se permite archivo con extensi&oacute;n csv(separado por ;)',
                    msgTarget: 'under',
                    listeners: {
                        change: function (fld, value) {
                        	var newValue = value.replace(/(^.*(\\|\/))?/, "");
                        	newValue = newValue.replace(/(\s)/g, "");
                            fld.setRawValue(newValue);
                        }
                    }
                }]
        }
    ],
    items: [
        {
            xtype: 'dynamicGrid',
            itemId: 'FrmRadicarmasivaRegistros',
            title: 'Registros',
            url: 'datosVacios',
            layout: 'fit'
        }
    ],
    buttons: [{
            text: 'Cargar Datos',
            itemId: 'btnCargardatosFrmRadmasiva',
            tooltip: 'Carga la informaci&oacute;n contenida en el archivo seleccionado en Datos'
        },
        {
            //disabled: true,
            itemId: 'btnRadicarFrmRadmasiva',
            text: 'Radicar',
            tooltip: 'Genera radicados a partir de la plantilla y registros seleccionados'
        },
//        {
//            text: "Combinar docx Wcf",
//            itemId: 'btnPreviewFrmRadmasivaWcf',
//            tooltip: 'Genera <b>1</b> documento combinando la plantilla y registros seleccionados'
//        },
        {
            text: "Combinar Plantillas",
            itemId: 'btnPreviewFrmRadmasivaZip',
            tooltip: 'Genera <b>n</b> documentos combinando la plantilla y registros seleccionados'
        },
        {
            itemId: 'btnGenerarpdfsFrmRadmasiva',
            text: 'Generar PDFs',
            tooltip: 'Genera <b>n</b> pdfs a partir de la plantilla y zip de plantillas'
        }]

});