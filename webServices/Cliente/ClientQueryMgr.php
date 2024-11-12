<?php
include_once 'ModelDataClient.php';
include_once 'conf.php';
/**
 * Clase que se encarga de completar la información de ejecucion de una Operacion de WS
 *
 */
class ClientQueryMgr {
	
	private static $db;
	private $impresor;
	
	function __construct() {
		
		global $ruta_raiz;
		//		$db = new ConnectionHandler( $ruta_raiz);
		$this->db = new ConnectionHandler ( $ruta_raiz,'WS' );
		$this->db->conn->SetFetchMode ( ADODB_FETCH_ASSOC );
		$this->impresor = new Impresor();
		
	}
	
	/**
	 * Procedimiento que obtiene los valores asociados a un parametro utilizando las variables de Orfeo
	 *
	 * @param unknown_type $parametros: 
	 * @param unknown_type $parametrosBusqueda
	 */
	public function getValueParameters($parametros, $parametrosBusqueda) {
		
		$parametrosCompletos = array ();
		
		//si ha y datos
		if ($parametros != NULL && sizeof ( $parametros ) > 0 && $parametrosBusqueda != NULL && sizeof ( $parametrosBusqueda ) > 0) {
			
			/*
			 * vector donde se guardaran las consultas ejecutadas.
			 * Se utilizará, para evitar ejecutar una consulta mas de una vez 
			 */
			$consultasEjecutadas = array ();
			
			//obtengo un vector con las keys del vector $parametrosBusqueda
			$nombresVariablesParbusqueda = array_keys ( $parametrosBusqueda );
			
			//recorremos el array de parametros a completar
			for($i = 0; $i < sizeof ( $parametros ); $i ++) {
				
				$unPar = $parametros [$i];
				$variable = $unPar->getVariableOrfeo ();
				
				$this->impresor->imprimir ( "-- Obteniendo parametro -> " . $unPar->getNombre () );
				$this->impresor->imprimir ( "<br>" );
				
				if ($variable != NULL) {
					
					//Obtenemos un objeto de tipo ConsultaOrfeo
					$consulta = $variable->getConsultaOrfeo ();
					
					// obtenemos la query a ejecutar
					$sql = $consulta->getQuery ();
					
					//si la consulta no ha sido ejecutada
					if (! $this->isQueryExecuted ( $consulta->getId (), $consultasEjecutadas )) {
						
						//recorro los parametros de busqueda
						for($j = 0; $j < sizeof ( $nombresVariablesParbusqueda ); $j ++) {
							
							//nombre de la variable del parametro de busqueda
							$nombreVar = $nombresVariablesParbusqueda [$j];
							$nombreVarBusq = "*" . $nombresVariablesParbusqueda [$j] . "*";
							//Valor de la variable de busqueda
							$valorVarBusq = $parametrosBusqueda [$nombreVar];
							//echo ("Valor: " + $valorVarBusq);
							$sql = str_replace ( $nombreVarBusq, $valorVarBusq, $sql );
						
						}
						
						//ejecutamos la consulta
						try {
							
							//si la consulta no tiene asteriscos
							if (! strpos ( $sql, "*" )) {
								
								$rs = $this->db->query ( $sql );
								
								//si se obtienen datos
								if (! $rs->EOF) {
									
									//guardo el resultado en el objeto consulta
									$consulta->setResultado ( $rs );
									array_push ( $consultasEjecutadas, $consulta );
									
									//obtengo el resultado de la consulta asociado al parametro
									$valorParametro = $rs->fields [$variable->getNombre ()];
									
									//asignamos el valor al parametro
									if ($valorParametro != NULL) {
										
										$nuevoParametro = new Parametro($unPar->getId(), $unPar->getNombre(), $unPar->getTipo(), NULL);
										$nuevoParametro->setValor($valorParametro);
										
										//$unPar->setValor ( $valorParametro );
										$this->impresor->imprimir ( " -> Encontrado - Valor: " . $valorParametro );
										$this->impresor->imprimir ( "<br/>" );
										array_push ( $parametrosCompletos, $nuevoParametro);
									
									} else {
										
										$this->impresor->imprimir ( " -> No Encontrado." );
										$this->impresor->imprimir ( "<br/>" );
									}
								
								} else {
									
									$this->impresor->imprimir ( " -> No Encontrado." );
									$this->impresor->imprimir ( "<br/>" );
									throw new Exception ( "ERROR: No se obtuvieron resultados al ejecutar la consulta: " . $sql );
								}
							
							} else {
								
								throw new Exception ( "ERROR: Consulta no valida!" );
							
							}
						
						} catch ( Exception $e ) {
							
							throw $e;
						}
					
					} else {
						
						// obtengo la consulta del array
						$consultaEjecutada = $this->getQueryExecuted ( $consulta->getId (), $consultasEjecutadas );
						
						if ($consultaEjecutada != NULL) {
							
							//Obtengo el resultado
							$rs = $consultaEjecutada->getResultado ();
							
							if ($rs != NULL) {
								
								//obtengo el resultado de la consulta asociado al parametro
								$valorParametro = $rs->fields [$variable->getNombre ()];
								
								//asignamos el valor al parametro
								if ($valorParametro != NULL) {
									
									$nuevoParametro = new Parametro($unPar->getId(), $unPar->getNombre(), $unPar->getTipo, NULL);
									$nuevoParametro->setValor($valorParametro);
									//$unPar->setValor ( $valorParametro );
									$this->impresor->imprimir ( " -> Encontrado - Valor: " . $valorParametro );
									$this->impresor->imprimir ( "<br/>" );
									array_push ( $parametrosCompletos, $nuevoParametro );
								
								} else {
									
									$this->impresor->imprimir ( " -> No Encontrado." );
									$this->impresor->imprimir ( "<br/>" );
								}
							
							} else {
								
								$this->impresor->imprimir ( " -> No Encontrado." );
								$this->impresor->imprimir ( "<br/>" );
							}
						
						} else {
							
							throw new Exception ( "No se encontró la consulta previamente ejecutada" );
						
						}
					} //end if - Consulta ejecutada

				} else {
					
					throw new Exception ( "No se puede obtener el valor del parametro debido que no tiene una variable asociada." );
				
				} // end else- si existe variableOrfeo
			

			} //end for - recorree parametros a buscar por base de datos 
		

		}else {
			
			$this->impresor->imprimir ( " -> Falta Informacion para realizar el proceso." );
			
		}
		
		
		return $parametrosCompletos;
	
	}
	
	/**
	 * Devuelve si la consulta identificada con $idConsulta ha sido ejecutada
	 *
	 * @param unknown_type $idConsulta
	 * @param unknown_type $consultasEjecutadas
	 */
	private function isQueryExecuted($idConsulta, $consultasEjecutadas) {
		
		$ejecutada = false;
		
		// itero el array
		for($i = 0; $i < sizeof ( $consultasEjecutadas ); $i ++) {
			
			$consulta = $consultasEjecutadas [$i];
			
			if ($consulta->getId () == $idConsulta) {
				
				$ejecutada = true;
				break;
			}
		
		}
		
		return $ejecutada;
	
	}
	
	/**
	 * devuelve la consulta identificada con $idConsulta del array
	 *
	 * @param unknown_type $idConsulta
	 * @param unknown_type $consultasEjecutadas
	 */
	private function getQueryExecuted($idConsulta, $consultasEjecutadas) {
		
		$consultaEjecutada = NULL;
		
		// itero el array
		for($i = 0; $i < sizeof ( $consultasEjecutadas ); $i ++) {
			
			$consulta = $consultasEjecutadas [$i];
			
			if ($consulta->getId () == $idConsulta) {
				
				$consultaEjecutada = $consulta;
				break;
			}
		
		}
		
		return $consultaEjecutada;
	
	}

}

?>