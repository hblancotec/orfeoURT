<?php
	/**
	  * CONSULTA VERIFICACION PREVIA A LA RADICACION
	  */
	switch($db->driver)
	{  
	 case 'mssqlnative':
			$radi_nume_sal = "convert(varchar(15), RADI_NUME_SAL)";
			$where_depe = " and ".$db->conn->substr."(".$radi_nume_sal.", 5, 4) in ($lista_depcod)";
	break;		
	case 'oracle':
	case 'oci8':
	case 'oci805':		
			$where_depe = "and ".$db->conn->substr."(a.radi_nume_sal, 5, 4) in ($lista_depcod)";
	break;		
	}
?>
