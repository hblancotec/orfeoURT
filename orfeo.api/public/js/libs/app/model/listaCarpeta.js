Ext.define('ExtMVC.model.listaCarpeta', {
    extend: 'Ext.data.Model',
    fields: [
        'icons',
        {name: 'RADI_NUME_RADI', mapping: 'RADI_NUME_RADI'},
        'RADI_FECH_RADI',
        {name:'RA_ASUN', type: 'string'},
        'SGD_TPR_DESCRIP',
        'RADI_USU_ANTE',
        'RADI_FECHA_VENCE',
	'RADI_DIAS_VENCE',
        {name: 'nroradicado', mapping: 'nroradicado', type: 'float'},
        {name: 'asunto', mapping: 'asunto'},
        'dirmail',
        'raiz',
        'nombrecontacto',
        'idCiudadano',
        'idEmpresa',
        'idEntidad',
        'loginFuncionario',
        'HID',
		'modenvio'
    ],
    idProperty: 'NoRadicado'
});