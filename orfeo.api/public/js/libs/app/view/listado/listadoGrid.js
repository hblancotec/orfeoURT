Ext.define('ExtMVC.view.listado.listadoGrid' ,{
    extend: 'Ext.grid.Panel',
    alias : 'widget.listadogrid',
    requires: (['Ext.ux.PreviewPlugin', 'Ext.selection.CheckboxModel', 'Ext.grid.*','Ext.data.*']),//Requires
    title : 'Listado de Radicados', 
    selType: 'checkboxmodel',//Modo de seleccion con ChekBox
    selModel: Ext.create('Ext.selection.CheckboxModel', { //Se inicializa para 
        name: 'seleccionados',
        listeners: {
            selectionchange: function(sm, selections) {
                //grid4.down('#removeButton').setDisabled(selections.length === 0);
                console.log('e'+selections.length);
                alert("Hola!"+selections.length);
            }
        }
    }),
    //columnLines: true,
    dockedItems: [
        {
            xtype: 'toolbar',
            items: [
                {
                    itemId: 'respuestaRapidaAction',
                    text:'Respuesta Rapida',
                    tooltip:'Respuesta Rapida de Documentos',
                    iconCls:'remove',
                    disabled: false
                }]
        }],
 /*
    viewConfig: {
        id: 'gv',
        trackOver: false,
        stripeRows: false,
        plugins: [{
            ptype: 'preview',
            bodyField: 'excerpt',
            expanded: true,
            pluginId: 'preview'
        }]
    },
 */
    // pluggable renders
   /* renderTopic: function(value, p, record) {
        return Ext.String.format(
            '<strong><a href="http://sencha.com/forum/showthread.php?t={2}" target="_blank">{0}</a></strong><a href="http://sencha.com/forum/forumdisplay.php?f={3}" target="_blank">{1} Forum</a>',
            value,
            record.data.forumtitle,
            record.getId(),
            record.data.forumid
        );
    },
 
    renderLast: function(value, p, r) {
        return Ext.String.format('{0} by {1}', Ext.Date.dateFormat(value, 'M j, Y, g:i a'), r.get('fechaRadicacion'));
    },*/
    
    initComponent: function() {
 
        this.store = 'listado';
        this.columns = [
            Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'NoRadicado',
            text: "NoRadicado",
            dataIndex: 'NoRadicado',
            //flex: 1,
            //renderer: this.renderTopic,
            sortable: true
        },{
            text: "fechaRadicacion",
            dataIndex: 'fechaRadicacion',
            hidden: false,
            sortable: true,
            renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s a')
        },{
            text: "asunto",
            dataIndex: 'asunto',
            //width: 70,
            align: 'center',
            sortable: true
        },{
            id: 'direccionContacto',
            text: "direccionContacto",
            dataIndex: 'direccionContacto',
            //width: 150,
            //renderer: this.renderLast,
            sortable: true
        }];
    
        //
        var combo = new Ext.form.ComboBox({
            name : 'perpage',
            width: 40,
            store: new Ext.data.ArrayStore({
                    fields: ['id'],
                    data  : [
                      ['10'], 
                      ['20'],
                      ['30'],
                      ['40'],
                      ['50'],
                    ]
                  }),
            mode : 'local',
            value: '10',
            listWidth     : 40,
            triggerAction : 'all',
            displayField  : 'id',
            valueField    : 'id',
            editable      : false,
            forceSelection: true,
          });
 
         // paging bar on the bottom
        this.bbar = Ext.create('Ext.PagingToolbar', {
            store: this.store,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} de {2}',
            emptyMsg: "No topics to display",
            items: [
                    '-',
                    'Per Page: ',
                    combo
                    ]
        });
        combo.on('select', function(combo, record) {
            this.store.pageSize = parseInt(record[0].get('id'), 10);
            this.store.loadPage(1);
          }, this);
        this.callParent(arguments);
    }
 });