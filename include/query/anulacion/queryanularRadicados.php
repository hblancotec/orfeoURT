<?php
	/**
	  * CONSULTA TIPO RADICACION
	  */
	switch($db->driver)
	{  
	 case 'mssqlnative':
		$whereTipoRadi = ' and b.radi_tiporad = ' .$tipoRadicado;
	break;		
	case 'oracle':
	case 'oci8':
	case 'oci805':	
		$whereTipoRadi = ' and '.$db->conn->substr.'(b.radi_nume_radi, 14, 1) = ' .$tipoRadicado;
	break;		
	}
?>