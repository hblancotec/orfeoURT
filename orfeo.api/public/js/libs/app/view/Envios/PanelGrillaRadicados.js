/*
 * File: app/view/Envios/PanelGrillaRadicados.js
 *
 * This file was generated by Sencha Architect
 * http://www.sencha.com/products/architect/
 *
 * This file requires use of the Ext JS 5.0.x library, under independent license.
 * License of Sencha Architect does not include license for Ext JS 5.0.x. For more
 * details see http://www.sencha.com/license or contact license@sencha.com.
 *
 * This file will be auto-generated each and everytime you save your project.
 *
 * Do NOT hand edit this file.
 */

Ext.define('ExtMVC.view.Envios.PanelGrillaRadicados', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.enviospanelgrillaradicados',

    requires: [
        'ExtMVC.view.Envios.PanelGrillaRadicadosViewModel',
        'ExtMVC.view.cmbNroRegGrilla',
        'Ext.grid.column.Template',
        'Ext.XTemplate',
        'Ext.grid.filters.filter.Number',
        'Ext.grid.column.Number',
        'Ext.form.field.Number',
        'Ext.toolbar.Paging',
        'Ext.toolbar.Separator',
        'Ext.form.field.ComboBox',
        'Ext.button.Button',
        'Ext.selection.CheckboxModel',
        'Ext.grid.plugin.RowEditing',
        'Ext.grid.filters.Filters',
        'Ext.grid.feature.Grouping'
    ],

    viewModel: {
        type: 'enviospanelgrillaradicados'
    },
    constrain: true,
    autoScroll: true,
    height: 335,
    id: 'GrdRadicadosEnviar',
    padding: 3,
    collapsed: false,
    icon: '/orfeo.api/public/images/document.gif',
    title: 'Listado de Radicados Generados Unitariamente',
    allowDeselect: true,
    store: 'Envios.GrillaRadicadosEnviar',
    viewConfig: {
        enableTextSelection: true,
        forceFit: true
    },
    columns: [
        {
            xtype: 'templatecolumn',
            tpl: [
                '<a href=\'{ruta}\' target=\'_blank\'>{radicado}</a>'
            ],
            dataIndex: 'radicado',
            lockable: true,
            text: 'Radicado',
            filter: {
                type: 'number'
            }
        },
        {
            xtype: 'numbercolumn',
            width: 40,
            sortable: false,
            dataIndex: 'copia',
            text: 'Copia',
            format: '00'
        },
        {
            xtype: 'gridcolumn',
            width: 250,
            dataIndex: 'destinatario',
            text: 'Destinatario',
            editor: {
                xtype: 'textfield',
                id: 'txtEditDestinatario'
            }
        },
        {
            xtype: 'gridcolumn',
            width: 200,
            dataIndex: 'direccion',
            text: 'Direcci&oacute;n',
            editor: {
                xtype: 'textfield',
                id: 'txtEditDireccion',
                maxLength: 100
            }
        },
        {
            xtype: 'gridcolumn',
            width: 75,
            dataIndex: 'telefono',
            text: 'Tel&eacute;fono',
            editor: {
                xtype: 'textfield',
                id: 'txtEditTelefono',
                maxLength: 15
            }
        },
        {
            xtype: 'gridcolumn',
            width: 65,
            dataIndex: 'codpostal',
            text: 'C&oacute;d. Postal',
            editor: {
                xtype: 'textfield',
                id: 'txtEditCodPostal',
                maxLength: 8
            }
        },
        {
            xtype: 'gridcolumn',
            modelValidation: false,
            width: 150,
            dataIndex: 'correoelectronico',
            text: 'Correo Electr&oacute;nico',
            editor: {
                xtype: 'textfield',
                id: 'txtEditCorreoElectronico',
                vtype: 'email'
            }
        },
        {
            xtype: 'gridcolumn',
            width: 75,
            dataIndex: 'municipio',
            text: 'Municipio'
        },
        {
            xtype: 'gridcolumn',
            width: 75,
            dataIndex: 'departamento',
            text: 'Departamento'
        },
        {
            xtype: 'gridcolumn',
            width: 75,
            dataIndex: 'pais',
            text: 'Pa&iacute;s'
        },
        {
            xtype: 'gridcolumn',
            dataIndex: 'guia',
            groupable: true,
            text: 'Nro Gu&iacute;a',
            editor: {
                xtype: 'numberfield',
                id: 'txtEditGuia'
            }
        },
        {
            xtype: 'gridcolumn',
            width: 149,
            dataIndex: 'observacion',
            groupable: true,
            text: 'Observaciones',
            editor: {
                xtype: 'textfield',
                id: 'txtEditObservacion'
            }
        },
        {
            xtype: 'gridcolumn',
			width: 230,
            hidden: true,
            id: 'Masiva',
            dataIndex: 'masiva',
            groupable: true,
            text: 'Masiva',
			filter: {
                type: 'string'
            }
        }
    ],
    dockedItems: [
        {
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            id: 'ptbGrdRadicadosEnviar',
            displayInfo: true,
            store: 'Envios.GrillaRadicadosEnviar',
            items: [
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'cmbnroreggrilla'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    id: 'btnChangeView',
                    enableToggle: true,
                    text: 'Ver Vista Masiva'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    id: 'btnAsignarMasivaGuiaObservacion',
                    text: 'Asignar masivamente gu&iacute;a/observaci&oacute;n'
                }
            ]
        }
    ],

    initConfig: function(instanceConfig) {
        var me = this,
            config = {
                selModel: Ext.create('Ext.selection.CheckboxModel', {
                    selType: 'checkboxmodel',
                    mode: 'SIMPLE'
                }),
                plugins: [
                    Ext.create('Ext.grid.plugin.RowEditing', {

                    }),
                    {
                        ptype: 'gridfilters'
                    }
                ]
            };
        me.processEnviosPanelGrillaRadicados(config);
        if (instanceConfig) {
            me.getConfigurator().merge(me, config, instanceConfig);
        }
        return me.callParent([config]);
    },

    processEnviosPanelGrillaRadicados: function(config) {
        this.features= [
            {
                ftype: 'grouping',
                enableGroupingMenu: false,
                groupHeaderTpl: [
                    '{columnName}: {name} ({[values.children.length]})'
                ],
                startCollapsed: true,
                id:'idGroupMasiva'
            }
        ];
        return config;
    }

});