<?php
	switch($db->driver)
	{
	case 'mssqlnative':
	case 'oracle':
	case 'oci8':
			$isqlus = "SELECT u.USUA_NOMB,u.USUA_CODI,u.USUA_ESTA FROM USUARIO u
					   WHERE $whereUsua $whereUsSelect $whereDependencia
					   ORDER BY u.USUA_NOMB";

	break;
	}
?>