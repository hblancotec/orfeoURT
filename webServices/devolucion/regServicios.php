<?

// devolver radicados

$server->register( 'devolucion',   //nombre del servicio 
    array('comentario' => 'xsd:string','radinume' => 'xsd:string', 'usuario' => 'xsd:string'),//entradas
    array('return' => 'xsd:string'), // salidas
    $ns,
$ns.'#devoluccion',
	'rpc',
	'encoded',
	'Devoluciones'
);

?>
