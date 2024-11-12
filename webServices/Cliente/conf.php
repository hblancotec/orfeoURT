<?php 


class Impresor {
	
	private $imprimirResultadoCliente;  
	private $rutaXml;
	
    function __construct(){
		
//		$this->imprimirResultadoCliente = TRUE;
		$this->imprimirResultadoCliente = FALSE;
		//$this->rutaXml="/var/www/orfeo/orfeo_3.6pruebas" ;
		$this->rutaXml="/var/www/orfeo/orfeo_3.6p" ;
	}
	/**
	 * @return unknown
	 */
	public function getRutaXml() {
		return $this->rutaXml;
	}
	
	/**
	 * @param unknown_type $rutaXml
	 */
	public function setRutaXml($rutaXml) {
		$this->rutaXml = $rutaXml;
	}
	
		
	function imprimir($linea) {
		
		if ($this->imprimirResultadoCliente) {
			
			echo ($linea);
		}
	
	}
	
	function imprimirTodo($variable){
		
		if ($this->imprimirResultadoCliente) {
			
			print_r($variable);
			
		}
		
	}
	
}


?>