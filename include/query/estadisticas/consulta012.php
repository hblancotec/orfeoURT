<?php

switch($db->driver) {
	case 'mssqlnative':
        $reporte = "SELECT	$radi_nume_radi AS RADICADO,
							R.RADI_FECH_RADI AS FECHA_RADICACION,
                            T.SGD_TPR_DESCRIP AS TIPO_DOCUMENTO,
                            H.HIST_FECH AS FECHA_HISTORICO,
                            H.HIST_OBSE AS OBSERVACION,
                            U.USUA_NOMB AS USUARIO,
                            DEP.DEPE_NOMB AS DEPENDENCIA
					FROM	HIST_EVENTOS H
                            JOIN RADICADO R ON
								R.RADI_NUME_RADI = H.RADI_NUME_RADI
								$wTipoRad
							JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = H.DEPE_CODI
								$wDepe
							LEFT JOIN USUARIO AS U ON 
								U.USUA_DOC = H.USUA_DOC
								$wUsua
							LEFT JOIN SGD_TPR_TPDCUMENTO T ON
								T.SGD_TPR_CODIGO = R.TDOC_CODI
								$wTipoDoc
					WHERE	H.SGD_TTR_CODIGO = 32 AND
							H.HIST_OBSE LIKE '*Modificado TRD*%' AND
                            ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin'
					ORDER BY FECHA_RADICACION";
		break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
		$reporte = "SELECT	$radi_nume_radi AS RADICADO,
							R.RADI_FECH_RADI AS FECHA_RADICACION,
                            T.SGD_TPR_DESCRIP AS TIPO_DOCUMENTO,
                            H.HIST_FECH AS FECHA_HISTORICO,
                            H.HIST_OBSE AS OBSERVACION,
                            U.USUA_NOMB AS USUARIO,
                            DEP.DEPE_NOMB AS DEPENDENCIA
					FROM	HIST_EVENTOS H
                            JOIN RADICADO R ON
								R.RADI_NUME_RADI = H.RADI_NUME_RADI
								$wTipoRad
							LEFT JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = H.DEPE_CODI_DEST
								$wDepe
							LEFT JOIN USUARIO AS U ON 
								U.USUA_DOC = H.USUA_DOC
								$wUsua
							LEFT JOIN SGD_TPR_TPDCUMENTO T ON
								T.SGD_TPR_CODIGO = R.TDOC_CODI
								$wTipoDoc
					WHERE	H.SGD_TTR_CODIGO = 32 AND
							H.HIST_OBSE LIKE '*Modificado TRD*%' AND
                            ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin'
					ORDER BY FECHA_RADICACION";
	break;
	}
?>
