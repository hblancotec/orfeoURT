<?

//Servicio para validacion Radicado
$server->register('validaRadicado', 
    	array(
		'expnum' => 'xsd:string', 
     		'radinum' => 'xsd:string',
     	),
    	array(
		'return' => 'xsd:string'
	),
   	 $ns 
);

?>
