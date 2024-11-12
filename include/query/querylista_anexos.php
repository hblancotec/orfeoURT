<?php

switch ($db->driver) 
	{ 
	case "oracle" :
	case 'oci8':
		$nombre = "RADI_NUME_SALIDA";
	break;	
	case "mssqlnative":
		$nombre = "convert(varchar(15), RADI_NUME_SALIDA) as RADI_NUME_SALIDA";
	break;				   			   
	}
?>
