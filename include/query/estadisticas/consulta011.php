<?php
$queryE = "SELECT	U.USUA_NOMB USUARIO,
					COUNT(*) RADICADOS,
					SUM(R.RADI_NUME_HOJA) HOJAS_DIGITALIZADAS,
					MIN(H.USUA_DOC) AS HID_USUA_DOC
			FROM	HIST_EVENTOS H
					JOIN RADICADO R ON
						R.RADI_NUME_RADI = H.RADI_NUME_RADI
						$wTipoRad
					JOIN USUARIO U ON
						U.DEPE_CODI = H.DEPE_CODI AND
						U.USUA_CODI = H.USUA_CODI
						$wUsua
					JOIN DEPENDENCIA DEP ON
						DEP.DEPE_CODI = H.DEPE_CODI
						$wDepe
			WHERE	H.SGD_TTR_CODIGO IN (22,23,42) AND "
					.$db->conn->SQLDate('Y/m/d', 'H.HIST_FECH')." BETWEEN '$fecha_ini' AND '$fecha_fin'
			GROUP BY u.USUA_NOMB
			ORDER BY USUARIO";

// Consulta para ver detalles
$queryEDetalle = "SELECT	CAST(DEP.DEPE_CODI AS CHAR(3))+ ' - ' + U.USUA_NOMB AS USUARIO,
							$radi_nume_radi AS RADICADO,
							R.RADI_FECH_RADI AS FECHA_RADICACION,
							T.SGD_TPR_DESCRIP AS TIPO_DOCUMENTO,
							H.HIST_FECH AS FECHA_DIGITALIZACION,
							R.RADI_NUME_HOJA AS HOJAS,
							H.HIST_OBSE AS OBSERVACION,
							dbo.VALIDAR_ACCESO_RADEXP (r.radi_nume_radi, '', '" . $_SESSION['login'] . "') AS HID_PERMISO
					FROM	HIST_EVENTOS H
							JOIN RADICADO R ON
								R.RADI_NUME_RADI = H.RADI_NUME_RADI
								$wTipoRad
							JOIN USUARIO U ON
								U.DEPE_CODI = H.DEPE_CODI AND
								U.USUA_CODI = H.USUA_CODI
								$wUsua
							JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = H.DEPE_CODI
								$wDepe
							JOIN SGD_TPR_TPDCUMENTO T ON  
								T.SGD_TPR_CODIGO = R.TDOC_CODI
					WHERE	H.USUA_DOC = '".$_GET['usuDoc']."' AND
							H.SGD_TTR_CODIGO IN (22,23,42) AND "
							.$db->conn->SQLDate('Y/m/d', 'H.HIST_FECH')." BETWEEN '$fecha_ini' AND '$fecha_fin'
					ORDER BY USUARIO ";		
?>
