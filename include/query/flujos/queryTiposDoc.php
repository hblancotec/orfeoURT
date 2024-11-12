<?php
switch ( $db->driver ) { 
	case 'oracle':
	case 'mssqlnative':
	case 'oci8':
	
		
				$sql = "select t.sgd_trad_descr, t.sgd_trad_codigo 
		         from sgd_trad_tiporad t
		         order by  t.sgd_trad_codigo
				 ";
	break;
	}

?>