<?php

$queryE = "	SELECT	M.MREC_DESC AS MEDIO_RECEPCION,
					COUNT(*) AS RADICADOS,
                    MAX(M.MREC_CODI) AS HID_MREC_CODI
            FROM	RADICADO R
					JOIN MEDIO_RECEPCION M ON M.MREC_CODI = R.MREC_CODI
					JOIN USUARIO U ON U.USUA_CODI = R.RADI_USUA_RADI AND U.DEPE_CODI = R.RADI_DEPE_RADI $wUsua
			WHERE	".$db->conn->SQLDate('Y/m/d', 'R.RADI_FECH_RADI')." BETWEEN '$fecha_ini' AND '$fecha_fin'
					$wDepe2
				    $wTipoRad
			GROUP BY M.MREC_DESC
			ORDER BY MEDIO_RECEPCION";
 			
// CONSULTA PARA VER DETALLES 
$queryEDetalle = "	SELECT	$radi_nume_radi AS RADICADO, 
							".$db->conn->SQLDate('Y/m/d h:i:s','R.RADI_FECH_RADI')." AS FECHA_RADICADO
							,M.MREC_DESC AS MEDIO_RECEPCION
							,R.RA_ASUN AS ASUNTO
							,U.USUA_NOMB AS USUARIO
							,R.RADI_PATH AS HID_RADI_PATH,
							dbo.VALIDAR_ACCESO_RADEXP (r.radi_nume_radi, '', '" . $_SESSION['login'] . "') AS HID_PERMISO
					FROM	RADICADO R
							JOIN MEDIO_RECEPCION M ON M.MREC_CODI = R.MREC_CODI 
							JOIN USUARIO U ON U.USUA_CODI = R.RADI_USUA_RADI AND U.DEPE_CODI = R.RADI_DEPE_RADI $wUsua
					WHERE	".$db->conn->SQLDate('Y/m/d', 'R.RADI_FECH_RADI')." BETWEEN '$fecha_ini'  AND '$fecha_fin'
							AND R.MREC_CODI = $mrecCodi
							$wDepe2
							$wTipoRad
					ORDER BY FECHA_RADICADO, RADICADO";			

// CONSULTA PARA VER TODOS LOS DETALLES 
$queryETodosDetalle = $queryEDetalle;
?>
