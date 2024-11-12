<?php
if (! $ruta_raiz)
	$ruta_raiz = "../../";
include_once "$ruta_raiz/webServices/Cliente/ModelDataClient.php";

/**
 * Clase que se encarga de la ejecucion de un cliente
 *
 */
class ClientExecutor {
	
	private $impresor;
	
	public function __construct() {
		
		$this->impresor = new Impresor ( );
	}
	
	/**
	 * Utiliza el ClientQueryMgr para completar los parametros faltantes y u
	 *
	 * @param Cliente $cliente
	 * @param unknown_type $parametros
	 * @param unknown_type $parametrosbusqueda
	 */
	public function executeClient(Cliente $cliente) {
		
		$resultado=NULL;
		
		if ($cliente != NULL) {
			
			$operacion = $cliente->getOperacion ()->getNombre ();
			$wsdl = $cliente->getOperacion ()->getServicio ()->getUrl ();
			$params = $cliente->getOperacion()->getParametrosEnviar();
			
			if ($operacion != NULL && $wsdl != NULL && $params != NULL) {

				$this->impresor->imprimir ( "-- Datos ejecucion --" );
				$this->impresor->imprimir ( "<br/>" );
				$this->impresor->imprimir ( "Operacion: " . $operacion );
				$this->impresor->imprimir ( "<br/>" );
				$this->impresor->imprimir ( "Wsdl: " . $wsdl );
				$this->impresor->imprimir ( "<br/>" );
				
				$tipoOperacion = $cliente->getOperacion ()->getTipo ();
				$this->impresor->imprimir ( "Tipo operacion: " . $tipoOperacion );
				$this->impresor->imprimir ( "<br/>" );
				// array de parametros
				if ($tipoOperacion == 0) {
				
					//Utilizo el ejecutor de clientes nuSOAP
					
				}else {
					//ejecucion con mensaje xml
					
					$rutaArchivo = NULL;
					
					if ($tipoOperacion == 1) {
						
						$parametrosEnviar = $params[0];
						//genero mensaje simple
						$rutaArchivo = $this->generarSimpleXML ($parametrosEnviar);
						
					}elseif ($tipoOperacion == 2 ) {
						
						$parametrosEnviar = $params;
						//genero mensaje masivo
						$rutaArchivo = $this->generarMasiveXML ($parametrosEnviar);
					}
					
					if ($rutaArchivo != NULL) {
						
						$this->impresor->imprimir ( "Xml: " . $rutaArchivo );
						$this->impresor->imprimir ( "<br/>" );
												
						//ejecutamos el cliente java
						try {
							
							$resultado=$this->callJavaClient ( $rutaArchivo, $wsdl, $operacion );
							
							
						} catch ( Exception $e ) {
							throw "ERROR: " . $e->getMessage ();
						}
					
					} else {
						throw new Exception ( "ERROR: No se pudo generar el mensaje XML de ejecucion." );
					
					}//end else - si hay archivo xml
					
				}//end else - operacion con mensaje xml
			
			} else {
				
				throw new Exception ( "ERROR: No se recibio informacion valida asociada al cliente a ejecutar." );
			
			}
		
		} else {
			
			throw new Exception ( "ERROR: No se recibio informacion valida asociada al cliente a ejecutar." );
		
		}
		
		return $resultado;
	}
	
	/**
	 * Funcion que construye el mensaje xml a generar 
	 *
	 * @param unknown_type $parametros
	 */
	private function generarSimpleXML($parametros) {
		
		$doc = NULL;
		$rutaArchivo = NULL;
		
		try {
			
			
			if ($parametros != NULL && sizeof ( $parametros ) > 0) {
				
				//$this->impresor->imprimir ( "Numero de parametros: " . sizeof ( $parametros ) );
				//$this->impresor->imprimirTodo($parametros); 
				
				$doc = new DOMDocument ( );
				// Creamos un objeto del árbol
				$Mensage = $doc->createElement ( 'message' );
				
				$doc->appendChild ( $Mensage );
				// 	Creamos un nuevo elemento del árbol
				//$dataset = $doc->createElement ( "dataset" );
				
				//$Mensage->appendChild ( $dataset );
				$properties = $doc->createElement ( "properties" );
				// 	Lo guardamos y añadimos dentro del nivel de $root
				$Mensage->appendChild ( $properties );
				
				for($j = 0; $j < sizeof ( $parametros ); $j ++) {
					
					$unPar = $parametros [$j];
					$property = $doc->createElement ( "property" );
					$properties->appendChild ( $property );
					//   Creamos un nuevo elemento llamado Nombre
					$Nombre = $doc->createElement ( "name" );
					$value = $doc->createElement ( "value" );
					$type = $doc->createElement ("type");
					
					//   Lo añadimos dentro del nodo $DatosPersonales
					$property->appendChild ( $Nombre );
					$property->appendChild ( $value );
					$property->appendChild ( $type ); 
					
					// Codificamos el texto a añadir dentro de Nombre
					$valorName = $doc->createTextNode ( utf8_encode ( $unPar->getNombre () ) );
					$valorValue = $doc->createTextNode ( utf8_encode ( $unPar->getValor () ) );
					$valorType = $doc->createTextNode ( utf8_encode ( $unPar->getTipo() ) );
					
					// 	Añadimos el texto dentro de Nombre
					$Nombre->appendChild ( $valorName );
					$value->appendChild ( $valorValue );
					$type->appendChild ( $valorType );
				
				}// end for
				
				//include 'conf.php';
				//$this->impresor->imprimir ($this->impresor->getRutaXml());
				$rutaXml=$this->impresor->getRutaXml();
				$rutaArchivo = "$rutaXml/webServices/Cliente/Xml/mensaje" . date ( 'd-M-Y-hms' ) . ".xml";
				$doc->save ( $rutaArchivo );
				 
				//$this->impresor->imprimir ( "Devolvera: " . $rutaArchivo );
			
			
			}
			//
			//$rutaArchivo = exec ( 'pwd' ) . "/Xml/mensaje" . date ( 'd-M-Y-hms' ) . ".xml";
			//$rutaArchivo = "/home/orfeodev/hnino/public_html/orfeo_3.6p/webServices/Cliente/Xml/mensaje" . date ( 'd-M-Y-hms' ) . ".xml";
			
		
		} catch ( Exception $e ) {
		
			throw $e;
		}
		
		return $rutaArchivo;
	
	}
	
	/**
	 * Metodo que genera un mensaje xml masivo
	 *
	 * @param unknown_type $parametrosEnviar: Array de Array de parametros con sus valores
	 * @return unknown: ruta del archivo xml a generar
	 */
	private function generarMasiveXML($parametrosEnviar) {
		
		$doc = NULL;
		$rutaArchivo = NULL;
		
		try {
			
			if ($parametrosEnviar != NULL && sizeof ( $parametrosEnviar ) > 0) {

				$doc = new DOMDocument ( );
				// Creamos un objeto del árbol
				$Mensage = $doc->createElement ( 'message' );
				$doc->appendChild ( $Mensage );
				
				//creamos el dataSet
				$dataset = $doc->createElement ( "dataSet" );
				$Mensage->appendChild ( $dataset );
				
				for ($i = 0; $i < sizeof($parametrosEnviar); $i++){
					
					$record =$doc->createElement ( "record" );
								
					$parametros = $parametrosEnviar[$i];
					
					if ($parametros != NULL && sizeof($parametros) > 0) {
						
						$valueRecord = $doc->createElement ("value");
						$record->appendChild ( $valueRecord );
						
						for ($j = 0; $j < sizeof($parametros); $j++){
														
							$unparamtro = $parametros[$j];
							
							if ($unparamtro != NULL) {
								
								$property = $doc->createElement ( "property" );
								$valueRecord->appendChild ( $property );
								
								//   Creamos un nuevo elemento llamado Nombre
								$Nombre = $doc->createElement ( "name" );
								$value = $doc->createElement ( "value" );
								$type = $doc->createElement ("type");
								
								//   Lo añadimos dentro del nodo $DatosPersonales
								$property->appendChild ( $Nombre );
								$property->appendChild ( $value );
								$property->appendChild ( $type ); 
								
								// Codificamos el texto a añadir dentro de Nombre
								$valorName = $doc->createTextNode ( utf8_encode ( $unparamtro->getNombre () ) );
								$valorValue = $doc->createTextNode ( utf8_encode ( $unparamtro->getValor () ) );
								$valorType = $doc->createTextNode ( utf8_encode ( $unparamtro->getTipo() ) );
								// 	Añadimos el texto dentro de Nombre
								$Nombre->appendChild ( $valorName );
								$value->appendChild ( $valorValue );
								$type->appendChild ( $valorType );
							
							}//end if - parametro no es null
							
						}//end for - recorrer array de parametros
						
						
					}//end if - si hay parametros

					$dataset->appendChild ( $record );
					
				}// recorro el listado de grupo de parametros a enviar

				
				$rutaXml = $this->impresor->getRutaXml ();
				$rutaArchivo = "$rutaXml/webServices/Cliente/Xml/mensaje" . date ( 'd-M-Y-hms' ) . ".xml";
				$doc->save ( $rutaArchivo );
			}
				

		} catch ( Exception $e ) {
			
			throw $e;
		}
		
		return $rutaArchivo;
	
	}
	
	/**
	 * Metodo que ejecuta el cliente java
	 *
	 * @param unknown_type $rutaArchivo
	 */
	private function callJavaClient($rutaArchivo, $wsdl, $operacion) {
		$resultado=NULL;
		
		if ($rutaArchivo != NULL && $wsdl != NULL && $operacion != NULL) {
			
			try {
				
				$rutaXml=$this->impresor->getRutaXml();
				
				exec ( "sh $rutaXml/webServices/Cliente/execute.sh $rutaArchivo $wsdl $operacion", $f, $g );
			//	print_r("sh $rutaXml/webServices/Cliente/execute.sh $rutaArchivo $wsdl $operacion"."--->".$f."---->".$g);
				
				
				$resultado=$f[0];
			
			} catch ( Exception $e ) {
				
				throw $e;
			}
	 	
		} else {
			
			throw new Exception ( "No se pudo ejecutar el cliente JAVA debido a falta de informacion." );
		}
	    return $resultado;
	}


}

?>