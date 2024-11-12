var regSeleccionados = new Object();
Ext.define('ExtMVC.view.listaCarpeta.listaCarpetaGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.listacarpetagrid',
    requires: (['Ext.ux.PreviewPlugin', 'Ext.selection.CheckboxModel', 'Ext.grid.*', 'Ext.data.*']),//Requires
    title: '&nbsp;',
    selType: 'checkboxmodel',//Modo de seleccion con ChekBox
    height: 656,
    width: '100%',
    //width: 1000,
    //forceFit: true,
    renderTo: 'div-listado',
    enableColumnResize: true,
    enableColumnMove: false,
    layout: 'fit',
    //columnLines: true,
    viewConfig: {
        enableTextSelection: true,
        forceFit: true//,
        //,autoFill:true
    },
    fixed: true,
    selModel: Ext.create('Ext.selection.CheckboxModel', { //Se inicializa para 
        name: 'seleccionados',
        listeners: {
            selectionchange: function (sm, selections) {
                regSeleccionados = selections;
            }
        }
    }),
    initComponent: function () {

        this.store = 'listaCarpeta';
        this.columns = [
            Ext.create('Ext.grid.RowNumberer'),
            {
                id: 'icons',
                text: "",
                dataIndex: 'icons',
                //flex: 1,
                width: 85,
                //renderer: this.renderTopic,
                sortable: false,
                resizable: false
            },

        {
            id: 'NoRadicado',
            text: "No Radicado",
            dataIndex: 'RADI_NUME_RADI',
            //flex: 1,
            width: 130,
            //renderer: this.renderTopic,
            sortable: true,
            resizable: false
        }, {
            id: "RADI_FECH_RADI",
            text: "Fecha Radicaci&oacute;n",
            dataIndex: 'RADI_FECH_RADI',
            hidden: false,
            sortable: true,
            width: 150,
            resizable: false
            //renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s a')
        }, {
            id: 'RA_ASUN',
            text: "Asunto",
            dataIndex: 'RA_ASUN',
            width: 510,
            align: 'left',
            sortable: true,
            resizable: false
        }, {
            id: 'SGD_TPR_DESCRIP',
            text: "Tipo Documento",
            dataIndex: 'SGD_TPR_DESCRIP',
            width: 190,
            //renderer: this.renderLast,
            sortable: true
            //,resizable: false
        }, {
            id: 'RADI_USUA_ANTE',
            text: "Enviado Por",
            dataIndex: 'RADI_USU_ANTE',
            width: 130,
            //renderer: this.renderLast,
            sortable: true
            //,resizable: false
        }, {
            id: 'RADI_FECHA_VENCE',
            text: "Vence",
            dataIndex: 'RADI_FECHA_VENCE',
            width: 85,
            //renderer: this.renderLast,
            sortable: true,
            resizable: false
        }, {
            id: 'RADI_DIAS_VENCE',
            text: "D&iacute;as",
            dataIndex: 'RADI_DIAS_VENCE',
            width: 50,
            //renderer: this.renderLast,
            sortable: true,
            resizable: false
        }, {
            id: 'HID',
            text: "HID",
            dataIndex: 'HID',
            width: 0,
            //renderer: this.renderLast,
            sortable: true,
            resizable: false
        }];

        //
        var combo = new Ext.form.ComboBox({
            name: 'perpage',
            width: 40,
            store: new Ext.data.ArrayStore({
                fields: ['id'],
                data: [
                  ['20'],
                  ['50'],
                  ['100'],
                  ['200'],
                  ['300']
                ]
            }),
            mode: 'local',
            value: '20',
            listWidth: 40,
            triggerAction: 'all',
            displayField: 'id',
            valueField: 'id',
            editable: false,
            forceSelection: true
        });

        // paging bar on the bottom
        this.bbar = Ext.create('Ext.PagingToolbar', {
            store: this.store,
            displayInfo: true,
            displayMsg: 'Registros {0} - {1} de {2}',
            emptyMsg: "No hay registros para mostrar",
            items: [
                    '-',
                    'Por P&aacute;gina: ',
                    combo
            ]
        });
        combo.on('select', function (combo, record) {
            this.store.pageSize = parseInt(record[0].get('id'), 10);
            this.store.loadPage(1);
        }, this);
        this.callParent(arguments);
        //this.render('div-listado');
        Ext.EventManager.onWindowResize(function () {
            grid.setSize(undefined, undefined);
        });
    }
});