<?

$server->register( 'notificar',   //nombre del servicio 
    array('resolucion' => 'xsd:string','tipoNotificacion' => 'xsd:string', 'fechaNotificacion' => 'xsd:string', 'fechaFijacion' => 'xsd:string','fechaDesfijacion' => 'xsd:string','numeroEdicto' => 'xsd:string','notificador' => 'xsd:string','notificado' => 'xsd:string', 'accion' => 'xsd:string',),//entradas
    array('return' => 'xsd:string'), // salidas
    $ns,
$ns.'#notificar',
	'rpc',
	'encoded',
	'notificar'
);

?>
