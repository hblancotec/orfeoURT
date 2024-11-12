<?php
	/**
	  * Consulta para paEncabeza.php
	  */
	switch($db->driver)
	{
	case 'mssqlnative':
			$conversion = "CONVERT (CHAR(5), depe_codi)"; 		
	break;
	case 'oracle':
	case 'oci8':
		$conversion = "depe_codi";
	break;
	}
?>