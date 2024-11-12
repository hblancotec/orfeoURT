<?php
	switch($db->driver)
	{
	case 'mssqlnative': 
	$isql = "select h.SGD_USH_FECHEVENTO AS FECHA,
                    u.usua_nomb	AS ADMINISTRADOR,
                    d.DEPE_NOMB	AS DEPENDENCIA,
                    m.SGD_USM_MODDESCR AS OBSERVACION
                from dependencia d,
                        SGD_USH_USUHISTORICO h,
                        SGD_USM_USUMODIFICA m,
                        usuario u,
                        SGD_USD_USUADEPE USD
                where   h.SGD_USH_ADMDOC = u.USUA_DOC AND
                        u.USUA_DOC = USD.USUA_DOC AND
                        h.SGD_USH_ADMDEP = USD.depe_codi AND
                        u.USUA_LOGIN = USD.USUA_LOGIN AND
                        d.depe_codi = USD.DEPE_CODI AND
                        h.SGD_USH_MODCOD = m.SGD_USM_MODCOD AND 
                        H.SGD_USH_USULOGIN = " . "'" . $usuLogin . "'" .
                    " order by " . $order . "  " . $orderTipo;
		break;
	case 'oracle':
	case 'oci8':
	$isql = "select 
			h.SGD_USH_FECHEVENTO 		AS FECHA
			,u.usua_nomb				AS ADMINISTRADOR
			,d.DEPE_NOMB				AS DEPENDENCIA
			,m.SGD_USM_MODDESCR			AS OBSERVACION
		from dependencia d, SGD_USH_USUHISTORICO h, SGD_USM_USUMODIFICA m, usuario u
		where h.SGD_USH_ADMCOD = u.usua_codi AND
		h.SGD_USH_ADMDEP = u.depe_codi	AND
		d.depe_codi = h.SGD_USH_ADMDEP AND
		h.SGD_USH_MODCOD = m.SGD_USM_MODCOD AND 
		H.SGD_USH_USULOGIN = " . "'" . $usuLogin . "'" .
		" order by " . $order . "  " . $orderTipo;
	break;
	}
?>
