Ext.define('ExtMVC.controller.radicacionmasiva.Global', {
    singleton: true,
    filePlantilla: '',
    primerRegistro: ''
});

Ext.define('ExtMVC.controller.RadicacionMasiva.Radmasiva', {
    extend: 'Ext.app.Controller',
    refs: {
        frmRadicacionmasiva: '#FrmRadicacionmasiva',
        frmUploadzippdf: '#FrmUploadZipPdf'
    },
    control: {
        "#btnCargardatosFrmRadmasiva": {
            click: 'onBtnCargardatosFrmRadmasivaClick'
        },
        "#btnRadicarFrmRadmasiva": {
            click: 'onBtnRadicarFrmRadmasivaClick'
        },
        "#btnPreviewFrmRadmasivaWcf": {
            click: 'onBtnPreviewFrmRadmasivaWcfClick'
        },
        "#btnPreviewFrmRadmasivaZip": {
            click: 'onBtnPreviewFrmRadmasivaZipClick'
        },
        "#btnGenerarpdfsFrmRadmasiva": {
            click: 'onBtnGenerarpdfsFrmRadmasivaClick'
        },
        "#btnUpFrmUploadZipPdf": {
            click: 'onBtnUpFrmUploadZipPdfClick'
        }

    },
    onBtnCargardatosFrmRadmasivaClick: function (button, e, eOpts) {
        var form = this.getFrmRadicacionmasiva();
        if (form.isValid()) {
            this.fileData = this.getFrmRadicacionmasiva().queryById('FfdDatosFrmRadicacionmasiva').getValue().replace("C:\\fakepath\\", "");;
            ExtMVC.controller.radicacionmasiva.Global.filePlantilla = this.getFrmRadicacionmasiva().queryById('FfdPlantillaFrmRadicacionmasiva').getValue();
            form.submit({
                url: '/orfeo.api/radicacionmasiva/cargarDataFile',
                method: 'POST',
                async: false,
                timeout: 180000,
                waitMsg: 'Cargando archivo Datos ...',
                scope: this,
                success: function (form, actions) {
                    obj2Json = Ext.JSON.decode(actions.response.responseText, false);
                    if (obj2Json.success == 'true' || obj2Json.success) {
                        var dynamicGrid = Ext.ComponentQuery.query('dynamicGrid')[0];
                        var columnsCount = dynamicGrid.headerCt.items.length;
                        for (var i = 0; i < columnsCount; i++) {
                            var column = dynamicGrid.headerCt.getComponent(0);
                            dynamicGrid.headerCt.remove(column);
                        }
                        dynamicGrid.getView().refresh();
                        dynamicGrid.getStore().removeAll();
                        dynamicGrid.getStore().getProxy().url = '/orfeo.api/radicacionmasiva/datosLlenos/' + this.fileData;
                        dynamicGrid.getStore().load();
                        dynamicGrid.getView().refresh();
                    } else {
                        Ext.Msg.show({
                            title: 'Error',
                            msg: obj2Json.message,
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                },
                failure: function (form, actions) {
                    obj2Json = Ext.JSON.decode(actions.response.responseText, false);
                    Ext.Msg.show({
                        title: 'Error',
                        msg: obj2Json.message,
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            });
        }
    },
    onBtnRadicarFrmRadmasivaClick: function (button, e, eOpts) {
        Ext.Msg.show({
            title: 'Error',
            msg: 'Funcionalidad no implementada a&uacute;n.',
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.ERROR
        });
    },
    onBtnPreviewFrmRadmasivaWcfClick: function (button, e, eOpts) {
        var dynamicGrid = Ext.ComponentQuery.query('dynamicGrid')[0];
        var regSeleccionados = [];
        var uid = Ext.Date.format(new Date(), 'YmdHis');
        var form = this.getFrmRadicacionmasiva();
        if ((dynamicGrid.getSelectionModel().getCount() > 0) && (ExtMVC.controller.radicacionmasiva.Global.filePlantilla !== '')) {
            form.mask('Cargando registros seleccionados ...');
            Ext.each(dynamicGrid.getSelection(), function (row, index, value) {
                regSeleccionados.push(row.raw); // push this to the array
            });
            form.mask('Enviando registros ...');
            Ext.Ajax.request({
                url: '/orfeo.api/radicacionmasiva/combinarPlantillaWcf/',
                method: 'POST',
                async: false,
                params: {
                    gridRows: Ext.JSON.encode(regSeleccionados),
                    fileDoc: ExtMVC.controller.radicacionmasiva.Global.filePlantilla,
                    dirTmp: uid
                },
                success: function (response, opts) {
                    obj2Json = Ext.JSON.decode(response.responseText, false);
                    form.unmask();
                    if (obj2Json.success || obj2Json.success == 'true') {
                        Ext.Msg.show({
                            title: 'Descarga',
                            msg: 'Descargue su archivo &gt;&gt; <a href=\'' + obj2Json.data + '\'>aqu&iacute;</a> &lt;&lt;<br/>' +
                                    'Inicia en ' + uid + ', finaliza en ' + Ext.Date.format(new Date(), 'YmdHis') + ' y son ' + dynamicGrid.getSelectionModel().getCount() + ' registros.',
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.INFO
                        });
                    } else {
                        form.unmask();
                        Ext.Msg.show({
                            title: 'Descarga',
                            msg: ' error 1.<br/>' +
                                    'Inicia en ' + uid + ', finaliza en ' + Ext.Date.format(new Date(), 'YmdHis') + ' y son ' + dynamicGrid.getSelectionModel().getCount() + ' registros.',
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        //strError = strError + obj2Json.message + '<br>';
                    }
                },
                failure: function (response, opts) {
                    form.unmask();
                    Ext.Msg.show({
                        title: 'ERROR',
                        msg: 'Fallo de comunicaci&oacute;n con el servicio de combinaci&oacute;n.<br/>' +
                                'Inicia en ' + uid + ', finaliza en ' + Ext.Date.format(new Date(), 'YmdHis') + ' y son ' + dynamicGrid.getSelectionModel().getCount() + ' registros.',
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.INFO
                    });
                }
            });
        } else {
            Ext.Msg.show({
                title: 'Error',
                msg: 'No hay registros seleccionados o Plantilla no ha sido cargada.',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    },
    onBtnPreviewFrmRadmasivaZipClick: function (button, e, eOpts) {
        var form = this.getFrmRadicacionmasiva();
        var time1 = Ext.Date.format(new Date(), 'YmdHis');
        var dynamicGrid = Ext.ComponentQuery.query('dynamicGrid')[0];
        var regSeleccionados = [];
        var uid = Ext.Date.format(new Date(), 'YmdHis');
        var contador = 1;
        //if ((dynamicGrid.getSelectionModel().getCount() > 0) && (ExtMVC.controller.radicacionmasiva.Global.filePlantilla !== '')) {
        if ((dynamicGrid.getStore().data.length > 0) && (ExtMVC.controller.radicacionmasiva.Global.filePlantilla !== '')) {
            //form.mask('Paso 1 de 2.<br/>Combinando plantillas ' + contador + ' de ' + dynamicGrid.getSelectionModel().getCount());
            form.mask('Paso 1 de 2.<br/>Combinando plantillas ' + contador + ' de ' + dynamicGrid.getStore().data.length);
            //Ext.each(dynamicGrid.getSelection(), function(row, index, value) {
            Ext.each(dynamicGrid.getStore().getRange(), function (row, index, value) {
                if (row.data['*RAD_S*'] !== '') {
                    Ext.Ajax.request({
                        url: '/orfeo.api/radicacionmasiva/combinarPlantillaZip/',
                        method: 'POST',
                        async: false,
                        timeout: 180000,
                        params: {
                            gridRows: Ext.JSON.encode(row.raw),
                            fileDoc: ExtMVC.controller.radicacionmasiva.Global.filePlantilla,
                            dirTmp: uid
                        },
                        success: function (response, opts) {
                            contador += 1;
                        },
                        failure: function (response, opts) {
                            //
                        }
                    });
                };
                //form.mask('Paso 1 de 2.<br/>Combinando plantillas ' + contador + ' de ' + dynamicGrid.getSelectionModel().getCount());
                form.mask('Paso 1 de 2.<br/>Combinando plantillas ' + contador + ' de ' + dynamicGrid.getStore().data.length);
            })

            form.mask('Paso 2 de 2.<br/>Empaquetando plantillas combinadas.');
            Ext.Ajax.request({
                url: '/orfeo.api/radicacionmasiva/descargarZip/',
                method: 'POST',
                async: false,
                params: {
                    dirTmp: uid
                },
                success: function (response, opts) {
                    form.unmask();
                    obj2Json = Ext.JSON.decode(response.responseText, false);
                    if (obj2Json.success || obj2Json.success == 'true') {
                        Ext.Msg.show({
                            title: 'Descarga',
                            msg: 'Descargue su archivo &gt;&gt; <a href=\'' + obj2Json.data + '\'>aqu&iacute;</a> &lt;&lt;<br/>' +
                                    'Inicia en ' + uid + ', finaliza en ' + Ext.Date.format(new Date(), 'YmdHis') + ' y son ' + dynamicGrid.getStore().data.length + ' registros.',
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.INFO
                        });
                    } else {
                        //strError = strError + obj2Json.message + '<br>';
                    }
                },
                failure: function (response, opts) {
                    form.unmask();
                    Ext.Msg.show({
                        title: 'Error',
                        msg: response.responseText,
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            });
        } else {
            Ext.Msg.show({
                title: 'Error',
                msg: 'No hay registros seleccionados o Plantilla no ha sido cargada.',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    },
    onBtnGenerarpdfsFrmRadmasivaClick: function (button, e, eOpts) {
        Ext.create('ExtMVC.view.radicacionMasiva.FrmUploadZipPdf');
    },
    onBtnUpFrmUploadZipPdfClick: function (button, e, eOpts) {
        var uid = Ext.Date.format(new Date(), 'YmdHis');
        var form = button.up('form');
        if (form.isValid()) {
            form.submit({
                url: '/orfeo.api/radicacionmasiva/cargarZipPlantillas',
                method: 'POST',
                async: false,
                timeout: 180000,
                waitMsg: 'Cargando archivo Datos ...',
                success: function (form, action) {
                    Ext.Msg.alert('Success', 'Inicio en ' + uid + ' y finaliza en ' + Ext.Date.format(new Date(), 'YmdHis') + '<br/>' +
                            'Descargue el archivo &gt;&gt; <a href=\'' + action.result.message + '\'>aqu&iacute;</a> &lt;&lt;');
                },
                failure: function (form, action) {
                    Ext.Msg.alert('Error', action.result.message);
                }
            });
        }
    }
});