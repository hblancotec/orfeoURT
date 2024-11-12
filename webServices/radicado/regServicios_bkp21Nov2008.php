<?

// Servicio que realiza una radicacion en Orfeo
$server->register('modificarRadicado',
	array(
     	'radiNume' => 'xsd:string',
     	'correo' => 'xsd:string',	
		'destinatario'=>'tns:Destinatario',
		'predio'=>'tns:Destinatario',
		'esp'=>'tns:Destinatario',
		'asu'=>'xsd:string',
		'med'=>'xsd:string',
		'ane'=>'xsd:string',
		'coddepe'=>'xsd:string',
		'tpRadicado'=>'xsd:string',
		'cuentai'=>'xsd:string',
		'radi_usua_actu'=>'xsd:string',
		'tip_rem'=>'xsd:string',
		'tdoc'=>'xsd:string',
		'tip_doc'=>'xsd:string',
		'carp_codi'=>'xsd:string',
		'carp_per'=>'xsd:string'
	),
	array(
		'return' => 'xsd:string'
	),
	$ns,
	$ns."#modificarRadicado",
	'rpc',
	'encoded',
	'Modificacion de Radicado'
);
?>
