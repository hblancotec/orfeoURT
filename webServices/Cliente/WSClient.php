<?php
/*
$ruta = "$ruta_raiz/webServices/Cliente";
include_once "$ruta/ModelDataClient.php";
include_once "$ruta/ClientFinder.php";
include_once "$ruta/ClientRulesManager.php";
include_once "$ruta/ClientExecutor.php";
include_once "$ruta/ClientQueryMgr.php";
include_once "$ruta/ClientAnalyzer.php";
include_once "$ruta/conf.php";
*/
include_once "ModelDataClient.php";
include_once "ClientFinder.php";
include_once "ClientRulesManager.php";
include_once "ClientExecutor.php";
include_once "ClientQueryMgr.php";
include_once "ClientAnalyzer.php";
include_once "conf.php";



/**
 * Clase que se encarga de ejecutar los clientes asociados a una funcionalidad de ORFEO
 *
 */
class WSClientManager {
	
	/*
	 * Objeto de tipo ClientFinder para buscar los clientes
	 */
	private $buscadorClientes;
	/*
	 * Objeto que define los clientes a ejecutar deacuerdo a las reglas de negocio
	 */
	private $manejadorReglas;
	
	/*
	 * Objeto de tipo ClientExecutor que ejecuta los clientes
	 */
	private $ejecutorClientes;
	
	/**
	 * Analizador de clientes
	 *
	 * @var unknown_type
	 */
	private $analizadorClientes;
	
	
	private $impresor;
	
	public function __construct() {
		
		$this->buscadorClientes = new ClientFinder ( );
		$this->manejadorReglas = new ClientRulesManager ( );
		$this->ejecutorClientes = new ClientExecutor ( );
		$this->analizadorClientes = new ClientAnalyzer ( );
		$this->impresor = new Impresor();
	}
	
	/**
	 * @return unknown
	 */
	public function getAnalizadorClientes() {
		return $this->analizadorClientes;
	}
	
	/**
	 * @return unknown
	 */
	public function getBuscadorClientes() {
		return $this->buscadorClientes;
	}
	
	/**
	 * @return unknown
	 */
	public function getEjecutorClientes() {
		return $this->ejecutorClientes;
	}
	
	/**
	 * @return unknown
	 */
	public function getManejadorReglas() {
		return $this->manejadorReglas;
	}
	
	/**
	 * @param unknown_type $analizadorClientes
	 */
	public function setAnalizadorClientes(ClientAnalyzer $analizadorClientes) {
		$this->analizadorClientes = $analizadorClientes;
	}
	
	/**
	 * @param unknown_type $buscadorClientes
	 */
	public function setBuscadorClientes(ClientFinder $buscadorClientes) {
		$this->buscadorClientes = $buscadorClientes;
	}
	
	/**
	 * @param unknown_type $ejecutorClientes
	 */
	public function setEjecutorClientes(ClientExecutor $ejecutorClientes) {
		$this->ejecutorClientes = $ejecutorClientes;
	}
	
	/**
	 * @param unknown_type $manejadorReglas
	 */
	public function setManejadorReglas(ClientRulesManager $manejadorReglas) {
		$this->manejadorReglas = $manejadorReglas;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $funcionaliad
	 * @param unknown_type $parametros
	 * @param unknown_type $paraBusqueda
	 * @param unknown_type $parametrosDecision
	 */
	function callWSClients($funcionaliad, $parametros, $paraBusqueda, $parametrosDecision) {
		
		try {
			//si la funcionalidad no es null
			if ($funcionaliad != NULL) {
				
				$this->impresor->imprimir ( "<hr/>" );
				$this->impresor->imprimir ( "********** MANEJADOR DE CLIENTES ***********" );
				$this->impresor->imprimir ( "<hr/>" );
				$this->impresor->imprimir ( "Funcionalidad: " . $funcionaliad );
				$this->impresor->imprimir ( "<br/>" );
				
				//obtengo la funcionalidad
				$func = $this->buscadorClientes->searchClientsByFunctionality ( $funcionaliad );
  
				if ($func != NULL) {
					
					//obtenemos los clientes
					$clientesFunc = $func->getClientes ();
					
					if ($clientesFunc != NULL && sizeof ( $clientesFunc ) > 0) {
						
						$this->impresor->imprimir ( "Numero de Clientes: " . sizeof ( $clientesFunc ) );
						$this->impresor->imprimir ( "<br/>" );
						
						//depuramos los clientes a ejecutar con el rulesmanager
						
 
						//$clientesEjecutar = $this->manejadorReglas->decideExecutionClients ( $clientesFunc, $parametrosDecision );
						$clientesEjecutar = $this->manejadorReglas->filterExecutionClients ( $clientesFunc, $parametrosDecision );
						
						//si hay clientes que ejecutar
						if ($clientesEjecutar != NULL && sizeof ( $clientesEjecutar ) > 0) {
							
							//recorro los clientes
							for($i = 0; $i < sizeof ( $clientesEjecutar ); $i ++) {
								
								$unCliente = $clientesEjecutar [$i];
								//los analizo
								//$unCliente = $this->analizadorClientes->analyzeClient ( $unCliente, $parametros, $paraBusqueda );
						
								$this->analizadorClientes->analyzeClient ( $unCliente, $parametros, $paraBusqueda );
								$this->imprimirCliente ( $unCliente );
								$this->impresor->imprimir ( '<br/>' );
								
								
							
								$this->impresor->imprimir ( "******** Ejecucion Cliente ************" );
								$this->impresor->imprimir ( '<br/>' );
								//si el cliente esta completo se ejecuta
								if ($this->analizadorClientes->checkCompleteClient ( $unCliente )) {
									
									//se ejecuta
									$resultado = $this->ejecutorClientes->executeClient ( $unCliente );
									
									if ($resultado != NULL) {
										
										$this->impresor->imprimir ( "<br/>" );
										$this->impresor->imprimir ( "-- Resultado -> $resultado" );
									} else {
										
										$this->impresor->imprimir ( "<br/>" );
										$this->impresor->imprimir ( "-- Resultado -> Ocurrio un problema con la ejecucion" );
									}
								
								} else {
									
									$this->impresor->imprimir ( "<br/>" );
									$this->impresor->imprimir ( "-- Resultado -> No se pudo ejecutar el cliente debido a que no esta completo" );
								
								}
							
							} //end for - recorrer clientes
						

						} //end if - existen clientes a ejecutar
						else {
							
							$this->impresor->imprimir ( '<br/>' );
							$this->impresor->imprimir ( "No se ejecutara ningun cliente para la funcionalidad " . $funcionaliad );
						
						}
					
					} else {
						
						$this->impresor->imprimir ( '<br/>' );
						$this->impresor->imprimir ( "No se encontraron clientes para la funcionalidad " . $funcionaliad );
					
					}
				
				} // end if - funcionalidad no nula
				else {
					$this->impresor->imprimir ( '<br/>' );
					$this->impresor->imprimir ( "No encontró la funcionalidad de nombre " . $funcionaliad );
				
				}
				
				$this->impresor->imprimir ( "<hr/>" );
			} // si llegaron las variables
		

		} catch ( Exception $e ) {
			
			//$this->impresor->imprimir ($e->getMessage());
			throw $e;
		
		}

	}
	
	/**
	 * Imprime el cliente
	 *
	 * @param Cliente $cliente
	 */
	private function imprimirCliente(Cliente $cliente) {
		
		//imprimimos el cliente completo
		
		$this->impresor->imprimir('<br/>');
		$this->impresor->imprimir('<hr/>');
		$this->impresor->imprimir("********  Cliente ************");
		$this->impresor->imprimir('<br/>');
		$this->impresor->imprimir("Cliente: " . $cliente->getId ());
		$this->impresor->imprimir('<br/>');
		$this->impresor->imprimir("Operacion: " . $cliente->getOperacion ()->getNombre ());	
		$this->impresor->imprimir('<br/>');
		
		$tipoOperacion = $cliente->getOperacion ()->getTipo();
		
		$this->impresor->imprimir("Tipo de Operacion: " . $tipoOperacion);	
		$this->impresor->imprimir('<br/>');
		$this->impresor->imprimir("-- Parametros a Enviar --");
		$this->impresor->imprimir('<br/>');		
		
		$parametrosEnviar = $cliente->getOperacion()->getParametrosEnviar();
		
		for($j = 0; $j < sizeof ( $parametrosEnviar ); $j ++) {
			
			$this->impresor->imprimir("--> Grupo ".($j+1) ." <--" );
			$this->impresor->imprimir('<br/>');	
			
			$parametros = $parametrosEnviar[$j];
			
			for ($i = 0; $i < sizeof($parametros); $i++){
				
				$otroPar = $parametros [$i];
			
			
				$this->impresor->imprimir($otroPar->getNombre () . "=" . $otroPar->getValor () . " (".$otroPar->getTipo() .")");
				$this->impresor->imprimir('<br/>');	
				
			}
		
		}
	
	}

}

?>