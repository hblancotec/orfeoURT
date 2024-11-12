Ext.define('ExtMVC.store.listado', {
    extend: 'Ext.data.Store',
    model: 'ExtMVC.model.listado',
    autoLoad: true,
    remoteSort: true,
    proxy: {
        // load using script tags for cross domain, if the data in on the same domain as
        // this page, an HttpProxy would be better
        type: 'jsonp',
        url: 'http://localhost:81/orfeo.api/radicado/listadoRadicadosCarpetaJSON',
        extraParams: {
            codigoCarpeta : 1,
            carpetaPersonal: 0
        },
        reader: {
            root: 'datosGenerales',
            totalProperty: 'NoRegistrosPagina'
        },
        // sends single sort as multi parameter
        simpleSortMode: true
    },
    sorters: [{
        property: 'fechaRadicacion',
        direction: 'DESC'
    }]
    ,pageSize:20
    ,limit:20
    
});