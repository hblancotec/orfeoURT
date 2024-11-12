<?php

class SQLSRV {
	
	var $conexion;
	private $dsn;
	
	/*function __construct($ruta_raiz) {
	    require $ruta_raiz."/config.php";
	    $this->dsn = array("Database"=>$servicio, "UID"=>$usuario, "PWD"=>$contrasena);
	    if(!isset($this->conexion)) {
	        $this->conexion = (sqlsrv_connect($servidor, $this->dsn)) or die( print_r( sqlsrv_errors(), true));
	    }
	}*/
	
    function SQLSRV($ruta_raiz) {
	    require $ruta_raiz."/config.php";
		$this->dsn = array("Database"=>$servicio, "UID"=>$usuario, "PWD"=>$contrasena);
		if(!isset($this->conexion)) {
		    $this->conexion = (sqlsrv_connect($servidor, $this->dsn)) or die( print_r( sqlsrv_errors(), true));
		}
	}

 	function consulta($consulta) {
 	    $resultado = sqlsrv_query($this->conexion, $consulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ) );
 		if(!$resultado) {
 			echo 'Error al ejecutar consulta';
			exit;
		}
  		return $resultado;
  	}
  
 	function fetch_array($consulta) {
 		return sqlsrv_fetch_array($consulta);
 	}
 
	function num_rows($rs) {
		return sqlsrv_num_rows($rs);
	}
 
	function fetch_row($consulta) {
		return sqlsrv_fetch_array($consulta, SQLSRV_FETCH_NUMERIC);
	}
	
	function fetch_assoc($rs) {
	    return sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	}
}
?>