<?php

class MSSQL {
	
	var $conexion;
	
	function MSSQL($ruta_raiz) {
		require_once $ruta_raiz."/config.php";
		if(!isset($this->conexion)) {
			$this->conexion = (mssql_connect($servidor,$usuario,$contrasena)) or die("Error al conectar BD");
			mssql_select_db($servicio, $this->conexion) or die("Error al seleccionar BD");
		}
	}

 	function consulta($consulta) {
 		$resultado = mssql_query($consulta,$this->conexion);
 		if(!$resultado) {
 			echo 'Error al ejecutar consulta';
			exit;
		}
  		return $resultado;
  	}
  
 	function fetch_array($consulta) {
 		return mssql_fetch_array($consulta);
 	}
 
	function num_rows($consulta) {
		return mssql_num_rows($consulta);
	}
 
	function fetch_row($consulta) {
		return mssql_fetch_row($consulta);
	}
	
	function fetch_assoc($consulta) {
		return mssql_fetch_assoc($consulta);
	} 
}
?>