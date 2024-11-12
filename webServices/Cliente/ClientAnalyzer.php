<?php

include_once 'ClientQueryMgr.php';
include_once 'conf.php';
/**
 * Clase que se encarga de completar la información de 
 * los parametros asociados a un cliente
 *
 */
class ClientAnalyzer {
	
	private $queryMgr; //objeto de tipo ClientQueryManager
	
	private $impresor;

	/**
	 * Constructor de la clase
	 *
	 */
	public function __construct() {
		
		$this->queryMgr = new ClientQueryMgr ( );
		$this->impresor = new Impresor();
	}
	
	/**
	 * @return unknown
	 */
	public function getQueryMgr() {
		return $this->queryMgr;
	}
	
	/**
	 * @param unknown_type $queryMgr
	 */
	public function setQueryMgr($queryMgr) {
		$this->queryMgr = $queryMgr;
	}
	
	/**
	 * procedimiento que analiza un cliente y lo completa con 
	 * la informacion de los parámetros
	 *
	 * @return unknown
	 */
	function analyzeClient(Cliente $cliente, $parametrosEnviados, $parametrosBusqueda) {
		
		//$completeClient = NULL;
		
		try {
			
		$this->impresor->imprimir("************ Analisis de Cliente **************");
		$this->impresor->imprimir('<br/>');
		
			
		if ($cliente != NULL ) {
			
			$this->impresor->imprimir("Cliente: " . $cliente->getId ());
			$this->impresor->imprimir('<br>');
			
			//obtengo la operacion
			$operClient = $cliente->getOperacion ();
			
			//si lao operacion no es null
			if ($operClient!= NULL) {
				
				$this->impresor->imprimir("Operacion: " . $operClient->getNombre());
				$this->impresor->imprimir('<br>');
				$this->impresor->imprimir("Tipo Operacion: " . $operClient->getTipo());
				$this->impresor->imprimir('<br>');
				
				if ($operClient->getTipo() == 2) {
					
					$this->completeMasiveClient($operClient,$parametrosEnviados,$parametrosBusqueda);
					
				}else {
						
					$this->completeSimpleClient($operClient,$parametrosEnviados,$parametrosBusqueda);
					
				}
				
			}else {

				throw new Exception("No se obtuvo informacion asociada a la operacion que ejecuta el cliente.");
				
			}
			
		
		} else {
			
			throw new Exception ( "Faltan datos para ejecutar la operación de analisis!" );
		
		}
			
			
		}catch (Exception $e){
			
			throw $e;
			
		}
		
		
		
		
		//return $completeClient;
	}
	
	/**
	 * Metodo que completa el valor de los parametros asociados a un cliente simple
	 *
	 * @param Cliente $cliente: cliente a completar
	 * @param unknown_type $parametrosEnviados
	 * @param unknown_type $parametrosBusqueda
	 * @return Cliente con parametros completos
	 */
	private function completeSimpleClient(Operacion $operacion, $parametrosEnviados, $parametrosBusqueda) {
		
		if ($operacion != NULL) {
			
			//obtengo los parametros de la operacion
			$parOper = $operacion->getParametros ();
			
			
			
			if ($parOper != NULL && sizeof ( $parOper ) > 0) {
			
				$paramCompleto = $this->completeParameters ( $parOper, $parametrosEnviados, $parametrosBusqueda);
					
				//agrego el grupo de parametros a los que se van a enviar en la ejecucion
				if ($paramCompleto != NULL && sizeof ( $paramCompleto ) > 0) {
					
					$operacion->addParametrosEnviar ( $paramCompleto );
				}
			
			} else {
				
				throw new Exception ( "ERROR: No se obtuvieron los parametros asociados a la operacion." );
			
			}
		
		} // si la opracion no es null
	

	}
	
	/**
	 * Metodo que completa el valor de los parametros que  
	 * se le enviaran a la aplicacion de manera masiva
	 *
	 * @param Cliente $cliente: cliente a completar
	 * @param unknown_type $parametrosEnviados
	 * @param unknown_type $parametrosBusqueda
	 *  
	 */
	private function completeMasiveClient(Operacion $operacion, $parametrosEnviados, $parametrosBusqueda) {
		
		if ($operacion != NULL) {
			
			$parametros = $operacion->getParametros ();
			
			
			if ($parametros != NULL && sizeof ( $parametros ) > 0) {
				
				$totalParamMasivos = 0;
				
				/**
				 * 1. Obtener el numero de veces que hay que calcular los parametros
				 * 	- se obtienen de los enviados o de los de busqueda dependiendo del caso 
				 */
				
				if ($parametrosEnviados != NULL && sizeof ( $parametrosEnviados ) > 0) {
					
					
					$totalParamMasivos = sizeof ( $parametrosEnviados );
				
				} else if ($parametrosBusqueda != NULL && sizeof ( $parametrosBusqueda ) > 0) {
					
					$totalParamMasivos = sizeof ( $parametrosBusqueda );
				
				}//end if hay parametros
				
				$this->impresor->imprimir("-- Completando Parametros Masivos -- ");
				$this->impresor->imprimir('<br>');
				$this->impresor->imprimir("Total: ". $totalParamMasivos);
				$this->impresor->imprimir('<br>');
				
				//2. hayar los valores de cada uno de los conjuntos de parametros
				
				for ($i = 0; $i < $totalParamMasivos; $i++){

					$arrayParamEnviados = $parametrosEnviados[$i];
					$arrayParamBusqueda = $parametrosBusqueda[$i];
					
					
					try {
						
						$grupoParam = $this->completeParameters ( $parametros, $arrayParamEnviados, $arrayParamBusqueda );
						
						if ($grupoParam != NULL && sizeof ( $grupoParam ) > 0) {
							
							$operacion->addParametrosEnviar ( $grupoParam );
						}
					
					} catch ( Exception $e ) {
						
						throw $e;
					
					}
					
				}// end for - reco
				
			

			} else {
				
				throw new Exception ( "No se obtuvieron los parametros asociados a la operacion." );
			
			}
		
		} // end if - la operacion no es nula
	}
	
	
	
	/**
	 * Operacion que recibe un array de parametros y los completa utilizando 
	 * los parametros enviados y los de busqueda
	 *
	 * @param Parametro $parametro
	 * @param unknown_type $parametrosEnviados
	 * @param unknown_type $parametrosBusqueda
	 */
	private function completeParameters($parametros, $parametrosEnviados, $parametrosBusqueda) {

		
		$parametrosCompletos = array ();

		//si se recibieron parametros
		if ($parametros != NULL && sizeof ( $parametros ) > 0) {
			
			//arrary de parametros a completar
			$paramToSearch = array ();
			
			
			//recorro el array de parametros
			for($i = 0; $i < sizeof ( $parametros ); $i ++) {
				
				$unParametro = $parametros [$i];
				
				// si el parametro no es nulo
				if ($unParametro != NULL) {
					
					$this->impresor->imprimir ( "-- Buscando Parametro: " . $unParametro->getNombre () . " --" );
					$this->impresor->imprimir ( "<br/>" );
					
					$valorParametro = NULL;
					
					//1. Busqueda en los parametros enviados
					if ($parametrosEnviados != NULL && sizeof ( $parametrosEnviados ) > 0) {
						
						//1.1 obtengo los nombres de los parametros enviados
						$nombresParametrosEnviados = array_keys ( $parametrosEnviados );
						
						//print_r($nombresParametrosEnviados);
						// si el nombre del parametro esta en los enviados
						if (in_array ( $unParametro->getNombre (), $nombresParametrosEnviados )) {
							
							$valorParametro = $parametrosEnviados [$unParametro->getNombre ()];
							
							if ($valorParametro != NULL) {
								
								$nuevoParametro =  new Parametro($unParametro->getId(), $unParametro->getNombre(), $unParametro->getTipo(), NULL);
								$nuevoParametro->setValor($valorParametro);
								$this->impresor->imprimir ( " -> Encontrado en enviados - Valor: " . $nuevoParametro->getValor () );
								$this->impresor->imprimir ( "<br/>" );
								
								array_push($parametrosCompletos, $nuevoParametro);
							}
						
						} 
						// 2. Si no esta en los enviados los buscamos por consulta en base de datos
						else {
						
							$this->impresor->imprimir ( " -> NO encontrado en enviados.");
							$this->impresor->imprimir ( "<br/>" );
							array_push($paramToSearch,$unParametro);
							
							
						}
					
					}// end if - si hay parametros enviados 
					else {
						
						/**
						 * si no hay parametros enviados lo colocamos en los que hay que buscar
						 * por base de datos
						 */
						$this->impresor->imprimir ( " -> NO encontrado en enviados.");
						$this->impresor->imprimir ( "<br/>" );
						array_push($paramToSearch,$unParametro);
						
					}
				
				} //end if - parametro no nulo

			} //end for - recorrer parametros
			

			//2. busco los restantes por base de datos.
			if (sizeof ( $paramToSearch ) > 0) {
				
				$this->impresor->imprimir ( "-- Busqueda de Parametros Por Base de Datos --" );
				$this->impresor->imprimir ( "<br>" );
				
				//ejecuto el ClientQueryMagr para que obtenga el valor de los parametros restantes
				try {

					$parametrsoBD = $this->queryMgr->getValueParameters ( $paramToSearch, $parametrosBusqueda );
				
					if ($parametrsoBD != NULL && sizeof($parametrsoBD) > 0) {
						
						$parametrosCompletos = array_merge ( $parametrosCompletos, $parametrsoBD );
					}
					
				}catch (Exception $e){
					
					throw ($e);
					
				}
			
			}
			
			//print_r(sizeof($parametrosCompletos));
			//print_r(sizeof($parametros));
			
			if (sizeof($parametrosCompletos) < sizeof($parametros)) {
				
				throw new Exception("ERROR: No se completaron todos los parametros");
			}
			
			
		} else {
			
			throw new Exception ( "ERROR: No se recibieron parametros a buscar." );
		
		}
		
		return $parametrosCompletos;
	
	}
	
	
	/**
	 * verifica la completitud del cliente, 
	 * Retorna true o false si el cliente esta completo para ejecutarse
	 *
	 * @param Cliente $cliente
	 * @return unknown true | false dependiendo si todos los parametros del cliente estan completos
	 */
	public function checkCompleteClient(Cliente $cliente) {
		
		$completo = FALSE;
		
		if ($cliente != NULL) {
			
			$operacion = $cliente->getOperacion ();
			
			if ($operacion != NULL) {
				
				$parametrosEnviar = $operacion->getParametrosEnviar ();
				
				if ($parametrosEnviar != NULL && sizeof ( $parametrosEnviar ) > 0) {
					
					$totalGrupoParametros = sizeof ( $parametrosEnviar );
					$gruposCompletos = TRUE;
					
					for($j = 0; $j > sizeof ( $totalGrupoParametros ); $j ++) {
						
						$parametros = $totalGrupoParametros [$j];
						
						if ($parametros != NULL && sizeof ( $parametros ) > 0) {
							
							$totalParametros = sizeof ( $parametros );
							$parametrosCompletos = TRUE;
							
							//recorro los parametros
							for($i = 0; $i < sizeof ( $parametros ); $i ++) {
								
								$unParam = $parametros [$i];
								
								//si el parametro tiene valor
								if ($unParam->getValor () == NULL) {
									
									$parametrosCompletos = FALSE;
									break;							
								}
							
							} //end for - parametros
							

							if (!$parametrosCompletos) {
								
								$gruposCompletos = FALSE;
								break;
							}
						
						}//
					
					} // end for - recorrer array de array de parametros

					
					$completo = $gruposCompletos;

				}
			
			}
		
		}
		
		return $completo;
	}

}

?>