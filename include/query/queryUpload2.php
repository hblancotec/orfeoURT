<?php
	switch($db->driver) {
	case 'mssqlnative':
	    break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
	 	$tamano = " to_number($tamano) ";
	    break;
	}
?>
