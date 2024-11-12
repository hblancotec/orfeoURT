<?php
if (! $ruta_raiz)
	$ruta_raiz = "../../";
include_once "$ruta_raiz/webServices/Cliente/ModelDataClient.php";
include_once $ruta_raiz . "/include/db/ConnectionHandler.php";
/**
 * Clase que se encarga de obtener la informacion de los clientes asociados
 *  a una funcionalida de Orfo desde la base de datos 
 *
 */
class ClientFinder {
	
	private static $db;
	
	function __construct() {
		global $ruta_raiz;
		
		$this->db = new ConnectionHandler ( $ruta_raiz,'WS' );
		$this->db->conn->SetFetchMode ( ADODB_FETCH_ASSOC );
	
	}
	
	/**
	 * Metodo que debe retornar un objeto de tipo FuncionalidadOrfeo. Para lo cual
	 * Debe hacer una consulta en la BD partiendo en tabla WS_FuO_FuncionalidadOrfeo 
	 * y obtener la información estipulada. 
	 * 
	 *
	 * @param unknown_type $funcionaliad
	 * @return un objeto de tipo FuncionalidadOrfeo
	 */
	public function searchClientsByFunctionality($funcionalidad) {
		
		$funcionalidadEncontrada = NULL;
		
		try {
			//echo "".$funcionalidadEncontrada;
			$funcionalidadEncontrada = $this->getFunctionByName ( $funcionalidad );
		
		} catch ( Exception $e ) {
			
			throw $e;
		
		}
		
		return $funcionalidadEncontrada;
	}
	
	/**
	 * Funcion que consulta una funcionalidad por el nombre
	 *
	 * @param unknown_type $funcName
	 * @return unknown
	 */
	public function getFunctionByName($funcName) {
		
		$funcionalidad = NULL;
		
		$sqlfun = "select ws_fuo_id from ws_fuo_funcionalidadorfeo WHERE ws_fuo_nombre='$funcName'";
		
		try {
			
			$rs = $this->db->query ( $sqlfun );
			
			if (! $rs->EOF) {
				$idfun = $rs->fields ['WS_FUO_ID'];
				
				if ($idfun != NULL) {
					
					$clientes = $this->getClientsByFunctionality ( $idfun );
					
					if ($clientes != NULL) {
						
						$funcionalidad = new FuncionalidadOrfeo ( $idfun, $funcName, $clientes );
					
					} else {
						
						throw new Exception ( "No se pudo obtener el cliente de la funcinalidad " . $funcName );
					}
				
				} else {
					
					throw new Exception ( "No se encontró información válida de funcionalidad" . $funcName );
				
				}
			
			} else {
				
				throw new Exception ( "No se encontró información asociada a la funcionalidad" . $funcName );
			
			}
		
		} catch ( Exception $e ) {
			
			throw new Exception ( "ERROR: " . $e );
		
		}
		
		return $funcionalidad;
	}
	
	/**
	 * Consulta los clientes asociados a una funcionalidad.
	 * Devuelve un array de clientes.
	 *
	 * @param unknown_type $idFunc
	 */
	public function getClientsByFunctionality($idFunc) {
		
		$clientes = NULL;
		
		$sqlfuncc = "select ws_cws_id from ws_func_funccliente where ws_fuo_id=$idFunc";
		  
		try {
			
			//ejecutamos la consulta
			$rs = $this->db->query ( $sqlfuncc );
			
			$i = 0;
			while ( ! $rs->EOF ) {
				
				$idClient = $rs->fields ['WS_CWS_ID'];
				
				if ($idClient != NULL) {
					
					$Operacion = $this->getOperationClient ( $idClient );
					
					// Obtengo la regla de ejecucion
					$reglaEjecucion = $this->getReglaEjecucionCliente($idClient);
					
					
					if ($Operacion != NULL) {
						
						$cliente = new Cliente ( $idClient, $Operacion );
						
						
						//agrago la regla de ejecucion al cliente
						if ($reglaEjecucion != NULL) {

							$cliente->setReglaEjecucion($reglaEjecucion);
						}
						
						$clientes [$i] = $cliente;
						$i ++;
					
					} else {
						
						throw new Exception ( "No se pudo obtener la operación." );
					}
				
				} else {
					
					throw new Exception ( "No se encontró información válida asociada al cliente." );
				
				}
				
				$rs->MoveNext ();
			} //end while
		

		} catch ( Exception $e ) {
			
			throw $e;
		
		}
		
		return $clientes;
	
	}
	
	/**
	 * Devuelve un objeto de tipo Operación asociado al cliente
	 *
	 * @param unknown_type $idClient
	 */
	public function getOperationClient($idClient) {
		
		$Operacion = NULL;
		
		$sqlClient = "select ws_ope_id from ws_cws_clientews where ws_cws_id = $idClient";
		try {
			
			//ejecutamos la consulta
			$rs = $this->db->query ( $sqlClient );
			
			if (! $rs->EOF) {
				
				$idOper = $rs->fields ['WS_OPE_ID'];
				
				if ($idOper != NULL) {
					
					$sqlOperacion = "select ws_ope_nombre, ws_ope_tipo, ws_ser_id from ws_ope_operacion where ws_ope_id = $idOper";
					
					$rs2 = $this->db->query ( $sqlOperacion );
					
					if (! $rs2->EOF) {
						
						$opeNombre = $rs2->fields ['WS_OPE_NOMBRE'];
						$opeTipo = $rs2->fields ['WS_OPE_TIPO'];
						$idServ = $rs2->fields ['WS_SER_ID'];
						
						//OBTENGO EL SERVICIO 
						$servcio = $this->getService ( $idServ );
						
						//OBTENFO LOS PARAMETROS
						$parametros = $this->getOperationParameters ( $idClient );
						
						if ($opeNombre != NULL && $opeTipo != NULL && $servcio != NULL && $parametros != NULL) {
							
							$Operacion = new Operacion ( $idOper, $opeNombre, $opeTipo, $parametros, $servcio );
						
						} else {
							
							throw new Exception ( "No se pudo obtener la operación." );
						
						}
					
					} else {
						
						throw new Exception ( "No se encontró información asociada a la operación " . $idOper );
					
					}
				
				} else {
					
					throw new Exception ( "No se encontró información válida de la operación asociada al cliente " . $idClient );
				
				}
			
			} else {
				
				throw new Exception ( "No se encontró información de la operación asociada al cliente " . $idClient );
			}
		
		} catch ( Exception $e ) {
			
			throw $e;
		}
		
		return $Operacion;
	}
	
	/**
	 * Consulta la base de datos y devuelve un objeto Servicio. 
	 *
	 * @param $idServ: Identificador del servicio.
	 */
	public function getService($idServ) {
		
		$servicio = NULL;
		
		$sqlServ = "select ws_ser_url from ws_ser_servicio where ws_ser_id=$idServ";
		
		try {
			
			//ejecutamos la consulta
			$rs = $this->db->query ( $sqlServ );
			
			if (! $rs->EOF) {
				
				$servUrl = $rs->fields ['WS_SER_URL'];
				
				if ($servUrl != NULL) {
					
					$servicio = new Servicio ( $idServ, $servUrl );
				
				} else {
					
					throw new Exception ( "No se encontró información válida asociada al servicio " . $idServ );
				
				}
			
			} else {
				
				throw new Exception ( "No se encontró información asociada al servicio " . $idServ );
			
			}
		
		} catch ( Exception $e ) {
			
			throw $e;
		}
		
		return $servicio;
	
	}
	
	/**
	 * Consulta la base de datos y devuelve un array de objetos con los parametros asociados al cliente
	 *
	 * @param unknown_type $idwsClient
	 */
	public function getOperationParameters($idOper) {
		
		$parametros = NULL;
		
		$sqlOperParam = "select ws_par_id from ws_cpa_clienteparametro where ws_cli_id = $idOper";
		
		try {
			
			//ejecutamos la consulta
			$rs = $this->db->query ( $sqlOperParam );
			$i = 0; //variables de arr
			while ( ! $rs->EOF ) {
				
				$idPar = $rs->fields ['WS_PAR_ID'];
				
				if ($idPar != NULL) {
					
					$sqlParam = "select ws_var_id, ws_par_nombre, ws_par_tipo from ws_par_parametro where ws_par_id= $idPar";
					
					$rs2 = $this->db->query ( $sqlParam );
					
					if (! $rs2->EOF) {
						
						$idvar = $rs2->fields ['WS_VAR_ID'];
						$nombrePar = $rs2->fields ['WS_PAR_NOMBRE'];
						$tipoPar = $rs2->fields ['WS_PAR_TIPO'];
						$variable = NULL;
						
						if ($idvar != NULL) {
							//OBTENGO LA VARIABLE
							$variable = $this->getVariableById ( $idvar );
						}
						
						if ($nombrePar != NULL && $tipoPar != NULL) {
							
							$parametros [$i] = new Parametro ( $idPar, $nombrePar, $tipoPar, $variable );
							$i ++;
						
						} else {
							
							throw new Exception ( "No se tiene toda la información para crear el parámetro." );
						}
					
					} else {
						
						throw new Exception ( "No se encontró información válida para el parámetro asociado a la operación " . $idOper );
					
					}
				
				} else {
					
					throw new Exception ( "No se encontró información asociada al parámetro asociado a la operación " . $idOper );
				}
				
				$rs->MoveNext ();
			
			} //end while
		

		} catch ( Exception $e ) {
			
			throw $e;
		}
		
		return $parametros;
	
	}
	
	/**
	 * Consulta y devuelve un Objeto de tipo Variable
	 *
	 * @param unknown_type $idVar
	 */
	public function getVariableById($idVar) {
		
		$variable = NULL;
		
		$sqlVariable = "select ws_var_name, ws_coo_id from ws_var_variableorfeo where ws_var_id = $idVar";
		
		$rs = $this->db->query ( $sqlVariable );
		try {
			
			if (! $rs->EOF) {
				
				$varName = $rs->fields ['WS_VAR_NAME'];
				$idConsulta = $rs->fields ['WS_COO_ID'];
				$consulta = $this->getConsultaById ( $idConsulta );
				
				if ($varName != null && $consulta != null) {
					
					$variable = new VariableOrfeo ( $idVar, $varName, $consulta );
				
				} else {
					
					throw new Exception ( "No se pudo crear el objeto VariableOrfeo" );
				}
			
			}
		} catch ( Exception $e ) {
			
			throw $e;
		}
		
		return $variable;
	}
	
	public function getConsultaById($idConsulta) {
		
		$consultaOrfeo = NULL;
		
		try {
			
			if ($idConsulta != NULL) {
				
				$sqlConsulta = "select ws_coo_consulta from ws_coo_consultaorfeo where ws_coo_id = $idConsulta";
				
				$rs = $this->db->query ( $sqlConsulta );
				if (! $rs->EOF) {
					
					$query = $rs->fields ['WS_COO_CONSULTA'];
					
					if ($query != null) {
						
						$consultaOrfeo = new ConsultaOrfeo ( $idConsulta, $query );
					
					} else {
						
						throw new Exception ( "No se encontró información válida para la consulta." );
					}
				
				} else {
					
					throw new Exception ( "No se encontró información asociada a la consulta." );
				
				}
			
			} else {
				
				throw new Exception ( "No se puede realizar la búsqueda si el id de consulta es nulo." );
			}
		
		} catch ( Exception $e ) {
			
			throw $e;
		
		}
		return $consultaOrfeo;
	
	}
	
	/**
	 * Funcion que devuelve un objeto de tipo Regla de Ejecucion asociada al cliente
	 *
	 * @param unknown_type $idCliente
	 */
	public function getReglaEjecucionCliente($idCliente) {
		
		$reglaEjecucion = NULL;
		
		$sqlClient = "select ws_rej_id from ws_cws_clientews where ws_cws_id = $idCliente";
		
		try {
			
			//ejecutamos la consulta
			$rs = $this->db->query ( $sqlClient );
			
			if (! $rs->EOF) {
				
				$idRegla = $rs->fields ['WS_REJ_ID'];
				
				if ($idRegla != NULL) {
					
					$sqlRegla = "select ws_rej_nombre from ws_rej_reglaejecucion where ws_rej_id = $idRegla";
					
					$rs2 = $this->db->query ( $sqlRegla );
					
					if (! $rs2->EOF) {
						
						$rjNombre = $rs2->fields ['WS_REJ_NOMBRE'];
						
						//OBTENGO LAS CONDICIONES 
						$condiciones = $this->getCondicionesReglaEjecucion($idRegla);
						
						if ($rjNombre != NULL && $condiciones != NULL) {
							
							$reglaEjecucion = new ReglaEjecucion($idRegla,$rjNombre,$condiciones);
							
						
						} else {
							
							throw new Exception ( "No se pudo crear la regla de ejecucion debido a información faltante" );
						
						}
					
					} else {
						
						throw new Exception ( "No se encontró informacion asociada a la regla " . $idRegla );
					
					}
				
				}//end if - regla no nul
			
			} else {
				
				throw new Exception ( "No se obtuvo informacion asociada al cliente " . $idClient );
			}
		
		} catch ( Exception $e ) {
			
			throw $e;
		}
		
		return $reglaEjecucion;
	
	} //end getReglaEjecucionCliente
	

	/**
	 * Funcion que deveulve las codiciones asociadas a una regla de ejecucion anidadas
	 *
	 * @param unknown_type $idRegla, identificador de la regla de ejecucion
	 * @return unknown Condiciones asociadas a la regla de ejecucion
	 */
	public function getCondicionesReglaEjecucion($idRegla) {
		
		$condicion = NULL;
		
		if ($idRegla != NULL) {
			
			try {
				
				$sqlReglaCond = "select ws_con_id, ws_rco_orden, ws_rco_oplogico from ws_rco_reglacondicion where ws_rej_id = $idRegla";
				//ejecutamos la consulta
				$rs = $this->db->query ( $sqlReglaCond );
				
				
				$listadoCondiciones = array();
				
				while (!$rs->EOF) {
					
					$idCond = $rs->fields ['WS_CON_ID'];
					$ordenCond = $rs->fields ['WS_RCO_ORDEN'];
					$operadorLogico = $rs->fields ['WS_RCO_OPLOGICO'];
					
					//obtengo los datos de la condicion
					if ($idCond != NULL) {

						$sqlCond= "select ws_con_variable, ws_con_operador, ws_con_valores from ws_con_condicion where ws_con_id = $idCond";
						
						$rs2 = $this->db->query ($sqlCond);
						
						if (!$rs2->EOF) {
							
							$nombreVariable = $rs2->fields['WS_CON_VARIABLE'];
							$operador = $rs2->fields['WS_CON_OPERADOR'];
							$valores = $rs2->fields['WS_CON_VALORES'];
							
							
							if ($ordenCond!= NULL && $nombreVariable!= NULL && $operador != NULL && $valores != NULL) {
								
								//creo una nueva condicion
								$unaCondicion = new Condicion($idCond,$nombreVariable,$operador,explode(",",$valores),$ordenCond);

								if ($unaCondicion!=NULL) {
									
									//le agrego el operador logico si lo tiene
									if ($operadorLogico!= NULL) {
										
										$unaCondicion->setOperadorLogico($operadorLogico);
									}
									
									//agrego la condicion al listado de condiciones
									$listadoCondiciones[$ordenCond - 1] = $unaCondicion;
									
									
								}
								
								
							}else {
								throw new Exception("No se pudo crear la condicion debido a informacion faltante");
							}
							
						}else {
							
							throw new Exception("No se encontro informacion con respecto a la condicion identificada con id: ".$idCond);
							
						}
					}// si el id de la condicion no es null
				
					$rs->MoveNext ();
				}//end while 1
				
				//resultado array de condiciones

				if (sizeof($listadoCondiciones) > 0) {
				
					$condicion = $listadoCondiciones[0];
					
					if ($condicion != NULL) {
						
						$condicionAuxiliar = $condicion;
						
						//recorro el listado de condiciones
						for($i = 1; $i < sizeof ( $listadoCondiciones ); $i ++) {
							
							
							$otraCond = $listadoCondiciones[$i];
							$condicionAuxiliar->setCondicionRelacionada($otraCond);
							$condicionAuxiliar = $otraCond;
							
							
						} //end for
					

					}// si a condicion inicial no es null
					else {
						
						throw new Exception("La condicion inicial es nula.");
						
					}
					
				}else {

					
					throw new Exception("No se encontraron condiciones...");
					
				}
			
			} catch ( Exception $e ) {
				
				throw $e;
			
			}
		
		}
		
		return $condicion;
	
	} // end getCondicionesReglaEjecucion


}

?>