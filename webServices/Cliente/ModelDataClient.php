<?php

class FuncionalidadOrfeo {
	
	private $id;
	private $nombre;
	private $clientes; //arary de Clientes
	

	function __construct($id, $nombre, $clientes) {
		
		$this->setId($id);
		$this->setNombre($nombre);
		$this->setClientes($clientes);
		
	}
	
	/**
	 * @return unknown
	 */
	public function getClientes() {
		return $this->clientes;
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getNombre() {
		return $this->nombre;
	}
	
	/**
	 * @param unknown_type $clientes
	 */
	public function setClientes($clientes) {
		$this->clientes = $clientes;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $nombre
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}


}

class Servicio {
	
	private $id;
	private $url;
	
	public function __construct($id, $url) {
		
		$this->id = $id;
		$this->url = $url;
	
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

}

class Cliente {
	
	private $id;
	//objeto de tipo Operacion
	private $operacion;
	//Objeto de tipo ReglaEjecucion - Puede ser nulo
	private $reglaEjecucion;
	
	public function __construct($id, Operacion $operacion) {
		
		$this->id = $id;
		$this->operacion = $operacion;
		$this->reglaEjecucion = NULL;
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getOperacion() {
		return $this->operacion;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $operacion
	 */
	public function setOperacion(Operacion $operacion) {
		$this->operacion = $operacion;
	}
	
	/**
	 * @return unknown
	 */
	public function getReglaEjecucion() {
		return $this->reglaEjecucion;
	}
	
	/**
	 * @param unknown_type $reglaEjecucion
	 */
	public function setReglaEjecucion(ReglaEjecucion  $reglaEjecucion) {
		$this->reglaEjecucion = $reglaEjecucion;
	}

	
	
}

class Operacion {
	
	private $id;
	private $nombre;
	/*
	 * Entero que indica el tipo de datos que recibe operacion
	 * - 0 si es un array de paramtros. 
	 * - 1 si es por esquema simple.
	 * - 2 si es por esquema masivo
	 */ 
	private $tipo; 
	private $parametros; // array de parametros
	
	/**
	 * Array multimensional que contiene los parametros que se le vana enviar al servicio.
	 * Adaptacion para implementar los mensajes masivos
	 * 
	 *
	 * @var unknown_type
	 */
	private $parametrosEnviar; 
	private $servicio; //objeto de tipo Servicio
	
	function __construct($id, $nombre, $tipo, $parametros, Servicio $serv) {
		
		$this->id = $id;
		$this->nombre = $nombre;
		$this->tipo = $tipo;
		$this->parametros = $parametros;
		$this->servicio = $serv;
		$this->parametrosEnviar = array();
			
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getNombre() {
		return $this->nombre;
	}
	
	/**
	 * @return unknown
	 */
	public function getParametros() {
		return $this->parametros;
	}
	
	/**
	 * @return unknown
	 */
	public function getServicio() {
		return $this->servicio;
	}
	
	/**
	 * @return unknown
	 */
	public function getTipo() {
		return $this->tipo;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $nombre
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}
	
	/**
	 * @param unknown_type $parametros
	 */
	public function setParametros($parametros) {
		$this->parametros = $parametros;
	}
	
	/**
	 * @param unknown_type $servicio
	 */
	public function setServicio(Servicio  $servicio) {
		$this->servicio = $servicio;
	}
	
	/**
	 * @param unknown_type $tipo
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}
	
	/**
	 * @return unknown_type
	 */
	public function getParametrosEnviar() {
		return $this->parametrosEnviar;
	}
	
	/**
	 * @param unknown_type $parametrosEnviar
	 */
	public function setParametrosEnviar($parametrosEnviar) {
		$this->parametrosEnviar = $parametrosEnviar;
	}

	
	/**
	 * Actualiza un parametro de una operacion
	 *
	 * @param Parametro $par
	 */
	public function updateParameter(Parametro $par){
		
		for ($i=0;$i<sizeof($this->parametros);$i++){
			
			$parametro = $this->parametros[$i];

			if ($parametro->getId() == $par->getId()){
				
				$this->parametros[$i] = $par;
				break;
			}
			
		}
		
		
	}

	/**
	 * Agrega un array de parametros al vector de parametros a enviar
	 *
	 * @param unknown_type $parametros
	 */
	public function addParametrosEnviar($parametros){
		
		array_push($this->parametrosEnviar,$parametros);
		
	}
	
}

class Parametro {
	
	private $id;
	private $nombre;
	private $tipo;
	private $valor;
	private $variableOrfeo;
	
	public function __construct($id, $nombre, $tipo, $var) {
		
		$this->id = $id;
		$this->nombre = $nombre;
		$this->tipo = $tipo;
		$this->variableOrfeo = $var;
		$this->valor = NULL;
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getNombre() {
		return $this->nombre;
	}
	
	/**
	 * @return unknown
	 */
	public function getTipo() {
		return $this->tipo;
	}
	
	/**
	 * @return unknown
	 */
	public function getValor() {
		return $this->valor;
	}
	
	/**
	 * @return unknown
	 */
	public function getVariableOrfeo() {
		return $this->variableOrfeo;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $nombre
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}
	
	/**
	 * @param unknown_type $tipo
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}
	
	/**
	 * @param unknown_type $valor
	 */
	public function setValor($valor) {
		$this->valor = $valor;
	}
	
	/**
	 * @param unknown_type $variableOrfeo
	 */
	public function setVariableOrfeo(VariableOrfeo $variableOrfeo) {
		$this->variableOrfeo = $variableOrfeo;
	}

}

class VariableOrfeo {
	
	private $id;
	private $nombre;
	private $consultaOrfeo;
	
	/**
	 * Constructor de la clase
	 *
	 * @param unknown_type $id
	 * @param unknown_type $nombre
	 * @param unknown_type $consulta
	 */
	public function __construct($id, $nombre,ConsultaOrfeo  $consulta) {
		
		$this->id = $id;
		$this->nombre = $nombre;
		$this->consultaOrfeo = $consulta;
	
	}
	
	/**
	 * @return unknown
	 */
	public function getConsultaOrfeo() {
		return $this->consultaOrfeo;
	}
	
	/**
	 * @param unknown_type $consultaOrfeo
	 */
	public function setConsultaOrfeo($consultaOrfeo) {
		$this->consultaOrfeo = $consultaOrfeo;
	}

	
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getNombre() {
		return $this->nombre;
	}
	
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $nombre
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}

}


class ConsultaOrfeo {
	
	private $id;
	private $query;
	private $resultado;
	
	
	function __construct($id, $query) {
		
		$this->id = $id;
		$this->query = $query;
		$this->resultado = NULL;
	}
	
	/**
	 * @return unknown
	 */
	public function getQuery() {
		return $this->query;
	}
	
	/**
	 * @param unknown_type $query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}

	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @return unknown
	 */
	public function getResultado() {
		return $this->resultado;
	}
	
	/**
	 * @param unknown_type $resultado
	 */
	public function setResultado($resultado) {
		$this->resultado = $resultado;
	}

	
	
}


/**
 * Clase que representa la regla de ejecucuion de un cliente
 *
 */
class ReglaEjecucion {
	
	private $id;
	private $nombre;
	
	// objeto de tipo condicion que define las condiciones que debe cumplir la regla
	private $condiciones; 
	
	
	function __construct($id, $nombre, Condicion $condicion) {
		
		
		$this->id = $id;
		$this->nombre = $nombre;
		$this->condiciones = $condicion;
		
	}
	
	/**
	 * @return unknown
	 */
	public function getCondiciones() {
		return $this->condiciones;
	}
	
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getNombre() {
		return $this->nombre;
	}
	
	/**
	 * @param unknown_type $condiciones
	 */
	public function setCondiciones($condiciones) {
		$this->condiciones = $condiciones;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $nombre
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}

	
	
}

/**
 * Clase que define la una condicion y su asociacion con otras condiciones 
 * para cumplir la regla de negocio. 
 *
 */
class Condicion {
	
	private $id;
	private $variable;
	private $operador;
	
	//array de posibles valores asociados a la variable
	private $valores; 
	
	private $orden;
	
	// Operador logico a operar con otra condicion. puede ser null
	private $operadorLogico; 
	
	// Objeto de tipo condicion, puede ser null si no tiene relacion con otras condiciones
	private $condicionRelacionada;  
	
	//boolean que contiene el resultado de evaluar la condicion
	private $valorBooleano;
	
	
	
	function __construct($id, $variable, $operador, $valores, $orden) {
		
		$this->id = $id;
		$this->variable = $variable;
		$this->operador = $operador;
		$this->valores = $valores;
		$this->orden = $orden;
		$this->operadorLogico = NULL;
		$this->condicionRelacionada = NULL;
		$this->valorBooleano = NULL;
		
	}
	
	/**
	 * @return unknown
	 */
	public function getCondicionRelacionada() {
		return $this->condicionRelacionada;
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getOperador() {
		return $this->operador;
	}
	
	/**
	 * @return unknown
	 */
	public function getOperadorLogico() {
		return $this->operadorLogico;
	}
	
	/**
	 * @return unknown
	 */
	public function getValores() {
		return $this->valores;
	}
	
	/**
	 * @return unknown
	 */
	public function getVariable() {
		return $this->variable;
	}
	
	/**
	 * @param unknown_type $condicionRelacionada
	 */
	public function setCondicionRelacionada(Condicion $condicionRelacionada) {
		$this->condicionRelacionada = $condicionRelacionada;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $operador
	 */
	public function setOperador($operador) {
		$this->operador = $operador;
	}
	
	/**
	 * @param unknown_type $operadorLogico
	 */
	public function setOperadorLogico($operadorLogico) {
		$this->operadorLogico = $operadorLogico;
	}
	
	/**
	 * @param unknown_type $valores
	 */
	public function setValores($valores) {
		$this->valores = $valores;
	}
	
	/**
	 * @param unknown_type $variable
	 */
	public function setVariable($variable) {
		$this->variable = $variable;
	}
	
	/**
	 * @return unknown
	 */
	public function getOrden() {
		return $this->orden;
	}
	
	/**
	 * @param unknown_type $orden
	 */
	public function setOrden($orden) {
		$this->orden = $orden;
	}
	
	/**
	 * @return unknown
	 */
	public function getValorBooleano() {
		return $this->valorBooleano;
	}
	
	/**
	 * @param unknown_type $valorBooleano
	 */
	public function setValorBooleano($valorBooleano) {
		$this->valorBooleano = $valorBooleano;
	}

	
}

?>