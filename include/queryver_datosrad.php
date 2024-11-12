<?php

switch ($db->driver) 
	{ 
	case "oracle" :
	case 'oci8':
		$numero = "RADI_NUME_DERI as RADI_NUME_DERI1";
	break;	
	case "mssql":
		$numero = "convert(varchar(15), a.RADI_NUME_DERI) as RADI_NUME_DERI1";
	break;				   			   
	}
?>
