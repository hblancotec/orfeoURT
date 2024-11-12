<?

$server->register('cambiarImagenRad2',
	array(
		'numRadicado'=>'xsd:string',
		'ext'=>'xsd:string',
		'file'=>'xsd:base64binary',
		'hist'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns."#cambiarImagenRad2",
	'rpc',
	'encoded',
	'Cambiar imagen a un radicado 2'
);
?>