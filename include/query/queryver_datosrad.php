<?php

switch ($db->driver) 
	{ 
	case "oracle" :
	case 'oci8':
		$numero = "a.RADI_NUME_RADI as RADI_NUME_DERI1";
		$radi_nume_radi = " a.radi_nume_radi ";
		$radi_nume_deri = " a.radi_nume_deri ";
	break;	
	case "mssqlnative":
		$numero = "convert(varchar(15), a.RADI_NUME_RADI) as RADI_NUME_DERI1";
		$radi_nume_radi = " convert(varchar(15), a.radi_nume_radi) ";
		$radi_nume_deri = " convert(varchar(15), a.radi_nume_deri) ";
	break;				   			   
	}
?>
