/**
 * Lukas Sliwinski
 * sliwinski.lukas@gmail.com
 *
 * Dynamic grid, allow to display data setting only URL.
 * Columns and model will be created dynamically.
 */

Ext.define('Ext.ux.grid.DynamicGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.dynamicGrid',
    alternateClassName: 'Ext.grid.DynamicGrid',
    requires: [
        'Ext.ux.data.reader.DynamicReader'
    ],
    // URL used for request to the server. Required
    url: '',
    //selModel: {
    //    selType: 'checkboxmodel',
    //    mode   : 'SIMPLE'
    //},
    initComponent: function() {
        console.log('DynamicGrid initComponent!');
        var me = this;

        if (me.url == '') {
            Ext.Error.raise('url parameter is empty! You have to set proper url to get data form server.');
        }
        else {
            Ext.applyIf(me, {
                columns: [],
                //forceFit: true,
                //autoScroll: true,
                //scrollable: true,
                //autoHeight: false,
                store: Ext.create('Ext.data.Store', {
                    // Fields have to be set as empty array. Without this Ext will not create dynamic model.
                    fields: [],
                    // After loading data grid have to reconfigure columns with dynamic created columns
                    // in Ext.ux.data.reader.DynamicReader
                    listeners: {
                        'metachange': function(store, meta) {
                            me.reconfigure(store, meta.columns);
                        }
                    },
                    autoLoad: true,
                    remoteSort: false,
                    remoteFilter: false,
                    remoteGroup: false,
                    proxy: {
                        reader: 'dynamicReader',
                        type: 'rest',
                        url: me.url
                    }
                })
            });
        }

        me.callParent(arguments);
    }
});