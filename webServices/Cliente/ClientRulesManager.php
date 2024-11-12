<?php

/**
 * Debe especificar los clientes a ejecutar
 *
 */
class ClientRulesManager {
	
	private $impresor;
	
	public function __construct() {
		
		$this->impresor = new Impresor ( );
	
	}
	
	/**
	 * Metodo que depura los cleinets a ejecutar deacuerdo a las reglas de negocio.
	 *
	 * @param unknown_type $clientes
	 * @param unknown_type $informacion
	 * @return unknown: array de clientes a ejecutar
	 */
	public function decideExecutionClients($clientes, $parametrosDecision) {
		
		$clientesEjecutar = array ();
		
		//si hay parametros de desicion
		if ($parametrosDecision != NULL && sizeof ( $parametrosDecision ) > 0) {
			
			$this->impresor->imprimir ( "********* Manejador de Reglas ************" );
			$this->impresor->imprimir ( "<br/>" );
			
			//recorro los clientes
			for($i = 0; $i < sizeof ( $clientes ); $i ++) {
				
				//obtengo el cliente				
				$unCliente = $clientes [$i];
				
				if ($unCliente != NULL) {
					
					$this->impresor->imprimir ( "-- Decision Cliente " . $unCliente->getId () . " --" );
					
					//Si el cliente es el 5
					if ($unCliente->getId () == '5') {
						
						//obtengo los parametros de desicion
						$medioRecepcion = $parametrosDecision ["medioRecepcion"];
						$dependenciaOrigen = $parametrosDecision ["dependenciaOrigen"];
						
						if ($medioRecepcion == '3' && $dependenciaOrigen == '529') {
							
							array_push ( $clientesEjecutar, $unCliente );
							$this->impresor->imprimir ( "<br/>" );
							$this->impresor->imprimir ( "--> Se ejecutara;" );
						
						} else {
							
							$this->impresor->imprimir ( "<br/>" );
							$this->impresor->imprimir ( "--> NO se ejecutara" );
						}
					
					} // si el cliente no tiene restricciones
					else {
						
						//lo coloco en el array de clientes a ejecutar
						array_push ( $clientesEjecutar, $unCliente );
						$this->impresor->imprimir ( "<br/>" );
						$this->impresor->imprimir ( "--> Se ejecutará" );
					}
				
				} // si cliente no el nulo			
			

			} //end for - recorro clientes
		

		} //si no hay parametros de desicion se deben ejecutar todos los clientes
		else {
			
			$clientesEjecutar = array_merge ( $clientes );
		
		}
		
		return $clientesEjecutar;
	
	}
	
	/**
	 * Metodo que filtra los clientes a ejecutar de acuerdo a la regla de ejecucion
	 * que tenga asociada y a unos parametros de desicion
	 *
	 * @param unknown_type $clientes: array de posibles clientes a ejecutar
	 * @param unknown_type $parametrosDecision: array con parametros de desicion
	 * @return unknown array de clientes a ejecutar
	 */
	public function filterExecutionClients($clientes, $parametrosDecision) {
		
		$clientesEjecutar = array ();
		
		if ($parametrosDecision != NULL && sizeof ( $parametrosDecision ) > 0) {
			
			
			$this->impresor->imprimir ( "********* Manejador de Reglas ************" );
			$this->impresor->imprimir ( "<br/>" );
			
			//recorro los clientes
			for($i = 0; $i < sizeof ( $clientes ); $i ++) {
				
				$unCliente = $clientes [$i];
				
				if ($unCliente != NULL) {
					
					$this->impresor->imprimir ( "---- Decision Cliente " . $unCliente->getId () . " ----" );
					$this->impresor->imprimir ( "<br/>" );
					
					//obtengo la regla de ejecucion
					$reglaEjecucion = $unCliente->getReglaEjecucion ();
					
					if ($reglaEjecucion != NULL) {
						
						$this->imprimirRegla ( $reglaEjecucion );
						
						$condicion = $reglaEjecucion->getCondiciones ();
						$condicionAuxiliar = $condicion;
						
						//expresion completa a evaluar
						$expresion = array ();
						
						// ciclo para evaluar las condiciones 
						while ( $condicionAuxiliar != NULL ) {
							
							try {
								//valido la condicion
								$resultValidacion = $this->validateCondition ( $condicionAuxiliar, $parametrosDecision);
								
								//$this->impresor->imprimirTodo($resultValidacion);
								//$this->impresor->imprimir("<br/>" );
								

								if ($resultValidacion != NULL) {
									
									$condicionAuxiliar->setValorBooleano ( $resultValidacion );
								
								}
							
							} catch ( Exception $e ) {
								
								//$this->impresor->imprimirTodo ( $e->getMessage () );
								throw $e;
							}
							
							array_push ( $expresion, $condicionAuxiliar->getValorBooleano () );
							
							if ($condicionAuxiliar->getOperadorLogico () != NULL) {
								
								array_push ( $expresion, $condicionAuxiliar->getOperadorLogico () );
							
							}
							
							//avanzo a la siguiente condicion							
							$condicionAuxiliar = $condicionAuxiliar->getCondicionRelacionada ();
						
						} //end while condiciones
						

						//validacion de las condiciones anidadas
						if ($expresion != NULL && sizeof ( $expresion ) > 0) {
							
							$ejecutar = $this->validateExpresion ( $expresion );
							
							// si ejecutar es true -> se coloca el cliente en los clientes a ejecutar
							if ($ejecutar == "TRUE") {
								
								$this->impresor->imprimir("--> Se ejecutara!");
								$this->impresor->imprimir("<br/>");
								array_push ( $clientesEjecutar, $unCliente );
							}else {
								
								$this->impresor->imprimir("--> No se ejecutara!");
								$this->impresor->imprimir("<br/>");
							}
							
						}
					
					} else {
						
						$this->impresor->imprimir("--> No tiene regla de ejecucion, se ejecutara!");
						$this->impresor->imprimir("<br/>");
						array_push ( $clientesEjecutar, $unCliente );
					
					}
				}
			
			} //end for recorrer clientes
		

		} else {
			
			$clientesEjecutar = $clientes;
		
		}
		
		
		$this->imprimirResultado($clientesEjecutar);
		return $clientesEjecutar;
	}
	
	/**
	 * Valida una condicion
	 *
	 * @param Condicion $condicion: condicion a validar 
	 * @return unknown boolean que indica si cumplio o no la condicion
	 */
	private function validateCondition(Condicion $condicion, $parametrosDecision) {
		
		
		$resultado = NULL;
		//1. obtengo la informacion de la condicion
		$nombreVariable = $condicion->getVariable ();
		$operador = $condicion->getOperador ();
		$posiblesValores = $condicion->getValores ();
		
		//validacion datos suficientes
		if ($nombreVariable != NULL && $operador != NULL && $posiblesValores != NULL && sizeof ( $posiblesValores ) > 0) {
			
			$valorVariable = $parametrosDecision [$nombreVariable];
			
			if ($valorVariable != NULL) {
				
				$this->impresor->imprimir ( "...Validando->" . $nombreVariable . " " . $operador . " " . $valorVariable );
				//2.  Validadcion del valor de la variable
				

				switch ($operador) {
					//operador =
					case "=" :
						{
							
							if (in_array ( $valorVariable, $posiblesValores )) {
								
								$resultado = "TRUE";
							
							} else {
								
								$resultado = "FALSE";
							
							}
							break;
						
						} //end case =
					//operador diferente
					case "!=" :
						{
							
							if (! in_array ( $valorVariable, $posiblesValores )) {
								
								$resultado = "TRUE";
							
							} else {
								
								$resultado = "FALSE";
							
							}
							break;
						
						} //end case !=
					//operador >
					case ">" :
						{
							
							$fallo = FALSE;
							
							for($i = 0; $i < sizeof ( $posiblesValores ); $i ++) {
								
								if ($valorVariable <= $posiblesValores [$i]) {
									$fallo = TRUE;
									break;
								}
							} //end for - recorrer valores 
							

							if (! $fallo) {
								
								$resultado = "TRUE";
							
							} else {
								
								$resultado = "FALSE";
							
							}
							
							break;
						} //end case >
					//operador <
					case "<" :
						{
							
							$fallo = FALSE;
							
							for($i = 0; $i < sizeof ( $posiblesValores ); $i ++) {
								
								if ($valorVariable >= $posiblesValores [$i]) {
									$fallo = TRUE;
									break;
								}
							} //end for - recorrer valores 
							

							if (! $fallo) {
								
								$resultado = "TRUE";
							
							} else {
								
								$resultado = "FALSE";
							
							}
							
							break;
						} //end case <
					//operador >=
					case ">=" :
						{
							
							$fallo = FALSE;
							
							for($i = 0; $i < sizeof ( $posiblesValores ); $i ++) {
								
								if ($valorVariable < $posiblesValores [$i]) {
									$fallo = TRUE;
									break;
								}
							
							} //end for - recorrer valores 
							

							if (! $fallo) {
								
								$resultado = "TRUE";
							
							} else {
								
								$resultado = "FALSE";
							
							}
							break;
						} //end case >=
					

					case "<=" :
						{
							
							$fallo = FALSE;
							
							for($i = 0; $i < sizeof ( $posiblesValores ); $i ++) {
								
								if ($valorVariable > $posiblesValores [$i]) {
									$fallo = TRUE;
									break;
								}
							
							} //end for - recorrer valores 
							

							if (! $fallo) {
								
								$resultado = "TRUE";
							
							} else {
								
								$resultado = "FALSE";
							
							}
							
							break;
						
						} //end case <=
					

					default :
						{
							
							if (in_array ( $valorVariable, $posiblesValores )) {
								
								$resultado = "TRUE";
							
							} else {
								
								$resultado = "FALSE";
							
							}
							break;
						}
				
				} //end switch
				

				$this->impresor->imprimir ( " --> " . $resultado );
				$this->impresor->imprimir ( "<br/>" );
			
			} else {
				
				throw new Exception ( "No se obtuvo un valor para la variable dentro de los parametros de desicion." );
			
			}
		
		} else {
			
			throw new Exception ( "No se pudo realizar la validacion de la condicion debido a datos insuficientes." );
		
		}
		
		return $resultado;
	}
	
	/**
	 * Valida la expresion resultado de las condiciones. la idea es validar algo de la forma
	 * (true, and, false, or, true) y obtener un resultado.
	 *
	 * @param unknown_type $expresion: Array con la expresion a validar
	 * @return unknown: boolean que indica resultado de la validacion de la expresion
	 */
	private function validateExpresion($expresion) {
		
		$resultado = NULL;
		
		if ($expresion != NULL && sizeof ( $expresion ) > 0) {
			
			$this->impresor->imprimir ( "...Validando la expresion: '" );
			
			for($j = 0; $j < sizeof ( $expresion ); $j ++) {
				
				$valor = $expresion [$j];
				$this->impresor->imprimir ( " " . $valor );
			}
			$this->impresor->imprimir ( "'" );
			
			//vector auxiliar
			$vectorAuxiliar = array_merge ( $expresion );
			$resultadoParcial;
			
			//$this->impresor->imprimirTodo($vectorAuxiliar);
			//$this->impresor->imprimir("<br/>");
			

			while ( sizeof ( $vectorAuxiliar ) >= 3 ) {
				
				try {
					
					$resultadoParcial = NULL;
					//quito el primero
					$var1 = array_shift ( $vectorAuxiliar );
					//quito el segundo
					$operador = array_shift ( $vectorAuxiliar );
					//quito el tercero
					$var2 = array_shift ( $vectorAuxiliar );
					
					//verificamos al completitud de las variables
					if ($var1 != NULL && $var2 != NULL && $operador != NULL) {
						
						// conversion de variables a booleanos
						$var1 = $this->getBooleanValueOfString($var1);
						$var2 = $this->getBooleanValueOfString($var2);
						
						// hacemos la evaluacion dpendiendo del operador
						switch ($operador) {
							case "AND" :
								{
									if ($var1 && $var1) {
										
										$resultadoParcial = "TRUE";
									}else {
										
										$resultadoParcial = "FALSE";
									}
									break;
								}
							
							case "OR" :
								{
									
									if ($var1 || $var2) {
										
										$resultadoParcial = "TRUE";
									}else {
										
										$resultadoParcial = "FALSE";
									}
									break;
									
								}
							
							default :
								{
									
									if ($var1 && $var1) {
										
										$resultadoParcial = "TRUE";
									}else {
										
										$resultadoParcial = "FALSE";
									}
									break;
								}
						
						} //end switch
						

						//colocamos en la primera posicion del array el resultado parcial
						if ($resultadoParcial != NULL) {
							
							array_unshift ( $vectorAuxiliar, $resultadoParcial);
						
						}
					
					} else {
						
						throw new Exception ( "La Expresion esta mal formada." );
					
					}
				
				} catch ( Exception $e ) {
					
					throw $e;
					
				}
			
			} //end while - recorre la expresion
			

			/*
			 * al salir del while debe quedar una expresion con el valor 
			 * de la evaluacion en la primera posicion 
			 */
			
			$resultado = array_shift ( $vectorAuxiliar );
			$this->impresor->imprimir ( " --> Resultado: " .$resultado);
			$this->impresor->imprimir ( "<br/>" );
		
		} else {
			
			throw new Exception ( "No se puede validar la expresion debido a datos insuficientes" );
		
		}
		
		return $resultado;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param ReglaEjecucion $regla
	 */
	private function imprimirRegla(ReglaEjecucion $regla) {
		
		if ($regla != null) {

			$this->impresor->imprimir ( "Regla de Ejecucion: " . $regla->getNombre () );
			$this->impresor->imprimir ( "<br/>" );
			
			$condicion = $regla->getCondiciones ();
			$this->impresor->imprimir ( "Condicion:" );
			while ( $condicion != NULL ) {
				
				$this->impresor->imprimir ( " " . $condicion->getVariable () . " " . $condicion->getOperador () );
				$valores = $condicion->getValores ();
				
				for($j = 0; $j < sizeof ( $valores ); $j ++) {
					
					if ($j == 0) {
						
						$this->impresor->imprimir ( " " . $valores [$j] );
					
					} else {
						
						$this->impresor->imprimir ( ", " . $valores [$j] );
					
					}
				
				} //end for - recorrer valores 
				

				if ($condicion->getOperadorLogico () != NULL) {
					
					$this->impresor->imprimir ( " " . $condicion->getOperadorLogico () . " " );
				}
				
				$condicion = $condicion->getCondicionRelacionada ();
			
			} // end while...
			

			$this->impresor->imprimir ( "<br/>" );
		} //end if - si regla es diferente de null
	

	}
	
	/**
	 * Imprime el resultado de los clientes a ejecutar
	 *
	 * @param unknown_type $clientes
	 */
	private function imprimirResultado($clientes) {
		
		$this->impresor->imprimir ( "---- Resultado ---- " );
		$this->impresor->imprimir ( "<br/>" );
		
		if ($clientes != NULL && sizeof ( $clientes ) > 0) {
			
			
			$this->impresor->imprimir ( "Clientes a Ejecutar:" );
			
			for($i = 0; $i < sizeof ( $clientes ); $i ++) {
				
				$uncliente = $clientes [$i];
				$this->impresor->imprimir ( " " . $uncliente->getId () );
				;
			
			}
			$this->impresor->imprimir ( "<br/>" );
		
		} else {
		
			$this->impresor->imprimir ( "No se ejecutara ningun cliente" );
		
		}
	
	}
	
	
	/**
	 * Devuelve el valor booleano de un String
	 *
	 * @param unknown_type $cadena
	 * @return unknown
	 */
	private function getBooleanValueOfString($cadena){
		
		$boolValue;
		
		if ($cadena == "TRUE") {
			
			$boolValue = TRUE;
			
		}elseif ($cadena == "FALSE") {
			
			$boolValue = FALSE;
			
		}else {
			
			$boolValue = FALSE;
		}
		
		return $boolValue;
		
	}
	
	/**
	 * Devuelve el String de un booleano
	 *
	 * @param unknown_type $boolean
	 * @return unknown
	 */
	private function getStringValueOfBoolean($boolean){
		
		$valorCadena = "FALSE";
		
		if ($boolean) {
			
			$valorCadena = "TRUE";
		}else {
			
			$valorCadena = "FALSE";
		}

		return $valorCadena;
		
	}
	
}

?>