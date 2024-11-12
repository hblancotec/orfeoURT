<?php

//  reasignar radicado

$server->register( 'reasignarRadicado',   //nombre del servicio 
    array('numeroRadicado' => 'xsd:string','usuarioOrigen' => 'xsd:string', 'usuarioDestino' => 'xsd:string', 'comentario' => 'xsd:string'),//entradas
    array('return' => 'xsd:string'), // salidas
    $ns,
$ns.'#reasignarRadicado',
	'rpc',
	'encoded',
	'reasignar radicado'
);

$server->register( 'reasignarRadicadoXDoc',   //nombre del servicio 
    array('numeroRadicado' => 'xsd:string','docUsuarioOrigen' => 'xsd:string', 'docUsuarioDestino' => 'xsd:string', 'comentario' => 'xsd:string'),//entradas
    array('return' => 'xsd:string'), // salidas
    $ns,
$ns.'#reasignarRadicadoXDoc',
	'rpc',
	'encoded',
	'reasignar radicado'
);
$server->register( 'reasignarMRadicadoXDoc',   //nombre del servicio 
    array('numeroRadicado' => 'xsd:string','docUsuarioOrigen' => 'xsd:string', 'arregloDocDestinatarios' => 'xsd:Matriz', 'comentario' => 'xsd:string'),//entradas
    array('return' => 'xsd:matriz'), // salidas
    $ns,
    $ns.'#reasignarMRadicadoXDoc',
    'rpc',
    'encoded',
    'Reasignacion Multiple de Radicados<br> Se envia en un arreglo de Destinatarios <br>  '
);

$server->register( 'reasignarRadicadoCarp',   //nombre del servicio 
    array('numeroRadicado' => 'xsd:string','usuarioOrigen' => 'xsd:string', 'usuarioDestino' => 'xsd:string', 'comentario' => 'xsd:string', 'carp' => 'xsd:string'),//entradas
    array('return' => 'xsd:string'), // salidas
    $ns,
    $ns.'#reasignarRadicadoCarp',
	'rpc',
	'encoded',
	'reasignar radicado carpeta'
);
?>
