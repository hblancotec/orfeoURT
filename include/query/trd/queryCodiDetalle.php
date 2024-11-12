<?php
	/**
	  * Crea una cadena con el codigo del campo y la descripcion
	  * $num_car numero de caracteres
	  * $nomb_varc variable codigo
	  * $nomb_varde variable descripcion
	  */
	switch($db->driver)
	{  
	 case 'mssqlnative':
			$sqlConcat = $db->conn->Concat("convert(char($num_car),$nomb_varc,0)","'-'","$nomb_varde");
	break;		
	case 'oracle':
	case 'oci8':
			$sqlConcat = $db->conn->Concat("$nomb_varc","'-'","$nomb_varde");
	break;		
	}
?>