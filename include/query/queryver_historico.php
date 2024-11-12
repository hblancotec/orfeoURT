<?php

switch ($db->driver) 
	{ 
	case "oracle" :
	case 'oci8':
		$numero_salida = "RADI_NUME_SALIDA";
		$numero_sal = "a.RADI_NUME_SAL"; 
	break;	
	case "mssqlnative":
		$numero_salida = "convert(varchar(15), a.RADI_NUME_SALIDA) as RADI_NUME_SALIDA"; 	
		$numero_sal= "convert(varchar(15), a.RADI_NUME_SAL) as RADI_NUME_SAL";
	break;				   			   
	}
?>
