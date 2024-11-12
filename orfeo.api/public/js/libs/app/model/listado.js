Ext.define('ExtMVC.model.listado', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'NoRadicado', mapping: 'NoRadicado', type: 'float'},
        {name: 'fechaRadicacion', mapping: 'fechaRadicacion', type: 'datetime', dateFormat: 'timestamp'},
        'asunto',
        {name: 'codigoTipoDocumental', mapping: 'codigoTipoDocumental', type: 'float'},
        {name: 'NoRadicadoPadre', mapping: 'NoRadicadoPadre', type: 'float'},
        'nombreContacto',
	'direccionContacto',
	'correoContacto',
	'codigoPostal',
        'codigoContinente',
	'codigoPais',
        'codigoMunicipio',
	'codigoDepartamento',
	'usuarioActual',
	'codigoDependenciaActual',
	'idCiudadano',
	'idEmpresa',
	'idEntidad',
	'loginFuncionario',
	'codigoCarpeta',
	'esCarpetaPersonal'
    ],
    idProperty: 'NoRadicado'
});