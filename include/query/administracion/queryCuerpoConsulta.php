<?php
	// CONSULTA VERIFICACION PREVIA A LA RADICACION
	$sqlConcat = $db->conn->Concat("u.usua_doc","'-'","u.usua_login");
	switch($db->driver) {
	case 'mssqlnative': 
        $isql = "SELECT u.usua_nomb	AS NOMBRE,
                        u.usua_login AS USUARIO,
                        DEP.depe_nomb AS DEPENDENCIA,
                        " . $sqlConcat  . " AS CHR_USUA_DOC
                    FROM usuario u,
                            SGD_USD_USUADEPE USD,
                            dependencia DEP
                    WHERE DEP.depe_codi = " . $dep_sel .
                    " AND USD.USUA_DOC = u.USUA_DOC AND
                        u.usua_login  = USD.USUA_LOGIN AND
                    DEP.depe_codi = USD.depe_codi " . $dependencia_busq2 . "
                    ORDER BY " . $order . " " . $orderTipo;
		break;
	case 'oracle':
	case 'oci8':
        $isql = "select 
                u.usua_nomb      		AS NOMBRE
                ,u.usua_login	     	AS USUARIO
                ,d.depe_nomb			AS DEPENDENCIA
                ," . $sqlConcat  . " 	AS CHR_USUA_DOC
            from usuario u, dependencia d 
            where u.depe_codi = " . $dep_sel .
            " AND u.depe_codi = d.depe_codi " . $dependencia_busq2 . "
            order by " . $order . " " . $orderTipo;
        break;
	}
?>
