<?php

class selects extends SQLSRV {
	var $code = "";
	
	function selects() {
	    parent::SQLSRV("../..");
	}
	
	function cargarPaises()	{
		$consulta = parent::consulta("SELECT Name,Code FROM country ORDER BY Name ASC");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0) {
			$paises = array();
			while($pais = parent::fetch_assoc($consulta)) {
				$code = $pais["Code"];
				$name = $pais["Name"];				
				$paises[$code]=$name;
			}
			return $paises;
		} else {
			return false;
		}
	}

		
	function cargarCiudades()
	{
		$consulta = parent::consulta("SELECT MUNI_NOMB, MUNI_CODI FROM MUNICIPIO WHERE DPTO_CODI = ".$this->code." AND ID_PAIS=170 AND ID_CONT=1 ORDER BY MUNI_NOMB");
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0) {
			$ciudades = array();
			while($ciudad = parent::fetch_assoc($consulta)) {
				$code = $ciudad["MUNI_CODI"];
				$name = utf8_encode($ciudad["MUNI_NOMB"]);				
				$ciudades[$code]=$name;
			}
			return $ciudades;
		} else {
			return false;
		}
	}
	
	
	function cargarSolicitudes()	{
		$this->code = empty($this->code) ? 0 : $this->code;
		$sql ="SELECT TD.SGD_PQR_LABEL, TD.SGD_TPR_CODIGO 
				FROM SGD_TPR_TPDCUMENTO TD INNER JOIN SGD_TEMAS_TIPOSDOC TE ON TD.SGD_TPR_CODIGO=TE.SGD_TPR_CODIGO AND TE.SGD_DCAU_CODIGO=".$this->code." 
				WHERE TE.SGD_TEMTDOC_ESTADO = 1 ORDER BY SGD_PQR_LABEL";
		$consulta = parent::consulta($sql);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0) {
			$solicitudes = array();
			while($solicitud = parent::fetch_assoc($consulta)) {
				$code = $solicitud["SGD_TPR_CODIGO"];
				$name = utf8_encode($solicitud["SGD_PQR_LABEL"]);
				$solicitudes[$code]=$name;
			}
			return $solicitudes;
		} else {
			return false;
		}
	}
	
	function cargarDescripSolicitudes()	{
		$this->code = empty($this->code) ? 0 : $this->code;
		$sql ="SELECT CAST(TD.SGD_PQR_DESCRIP as TEXT) AS SGD_PQR_DESCRIP, TD.SGD_PQR_LABEL
					FROM SGD_TPR_TPDCUMENTO TD INNER JOIN SGD_TEMAS_TIPOSDOC TE ON TD.SGD_TPR_CODIGO=TE.SGD_TPR_CODIGO AND SGD_DCAU_CODIGO=".$this->code." 
					WHERE TE.SGD_TEMTDOC_ESTADO = 1 ORDER BY SGD_PQR_LABEL";
		$consulta = parent::consulta($sql);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0) {
			$descripSolicitudes = array();
			while($dato = parent::fetch_assoc($consulta)) {
				$code = $dato["SGD_PQR_LABEL"];
				$name = utf8_encode($dato["SGD_PQR_DESCRIP"]);
				$descripSolicitudes[$code]=$name;
			}
			return $descripSolicitudes;
		} else {
			return false;
		}
	}
	
	function cargarUsuarios()	{
		$this->code = empty($this->code) ? 0 : $this->code;
		$sql ="SELECT USUA_NOMB, USUA_CODI
					FROM USUARIO
					WHERE DEPE_CODI=".$this->code." AND USUA_ESTA=1 AND SGD_ROL_CODIGO>0 
					ORDER BY USUA_NOMB";
		$consulta = parent::consulta($sql);
		$num_total_registros = parent::num_rows($consulta);
		if($num_total_registros>0) {
			$usuarios = array();
			while($solicitud = parent::fetch_assoc($consulta)) {
				$code = $solicitud["USUA_CODI"];
				$name = utf8_encode($solicitud["USUA_NOMB"]);
				$usuarios[$code]=$name;
			}
			return $usuarios;
		} else {
			return false;
		}
	}
	
	function cargarDatosPqrTdoc() {
		$sql ="SELECT SGD_PQR_LABEL, CAST(SGD_PQR_DESCRIP as TEXT) AS SGD_PQR_DESCRIP
					FROM SGD_TPR_TPDCUMENTO
					WHERE SGD_TPR_CODIGO=".$this->code." ";
		$consulta = parent::consulta($sql);
		$datos = parent::fetch_assoc($consulta);
		return $datos["SGD_PQR_LABEL"] . "cP@r@d0R" . $datos["SGD_PQR_DESCRIP"];
	}
}
?>