<?php
	switch($db->driver)
	{
	case 'mssqlnative':
	$qeryRadicado_codigo= "select convert(char(15), radi_nume_radi) as RADNUM, r.*, $sqlFecha as FECDOC  from radicado r 
       where radi_nume_radi=$codigo";
	break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
 	$qeryRadicado_codigo= "select radi_nume_radi as RADNUM, r.*, $sqlFecha as fecdoc  from radicado r 
       where radi_nume_radi=$codigo";
	break;
	}
?>