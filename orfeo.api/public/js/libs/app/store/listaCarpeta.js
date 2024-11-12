var storeListaCarpeta = Ext.define('ExtMVC.store.listaCarpeta', {
    extend: 'Ext.data.Store',
    model: 'ExtMVC.model.listaCarpeta',
    autoLoad: true,
    remoteSort: true,
    //buffered:true,
    proxy: {
        // load using script tags for cross domain, if the data in on the same domain as
        // this page, an HttpProxy would be better
        type: 'jsonp',
        noCache: true,
        url: (config.pathMVC? config.pathMVC:'') +'carpeta/listadoRadicadosEnCarpetaJSON',
        extraParams: {
            codigoCarpeta : config.codigoCarpeta,
            carpetaPersonal: config.carpetaPersonal,
            NoRadicado: config.NoRadicado
        },
        reader: {
            root: 'datosGenerales',
            totalProperty: 'NoRegistrosPagina'
        },
        timeout: 120000,
        // sends single sort as multi parameter
        simpleSortMode: true
    },
    sorters: [{
        property: 'RADI_FECH_RADI',
        direction: 'DESC'
    }]
    ,pageSize:20
    ,limit:20
    
});