<?php

$queryE = "	SELECT	U.USUA_NOMB AS USUARIO,
					COUNT(*) RADICADOS,
					MIN(U.USUA_CODI) HID_COD_USUARIO,
					MIN(U.DEPE_CODI) HID_DEPE_USUA
			FROM	RADICADO R JOIN USUARIO U ON 
						U.USUA_CODI = R.RADI_USUA_RADI AND 
						U.DEPE_CODI = R.RADI_DEPE_RADI 
						$wDepe4
						$wUsua
					JOIN DEPENDENCIA DEP ON DEP.DEPE_CODI = R.RADI_DEPE_RADI
						$wDepe
                    LEFT JOIN SGD_TPR_TPDCUMENTO T ON R.TDOC_CODI = T.SGD_TPR_CODIGO
								$wTipoDoc
					--LEFT JOIN SGD_EXP_EXPEDIENTE EX ON EX.RADI_NUME_RADI = R.RADI_NUME_RADI AND EX.SGD_EXP_ESTADO = 0 
			WHERE	".$db->conn->SQLDate('Y/m/d', 'R.RADI_FECH_RADI')." BETWEEN '$fecha_ini' AND '$fecha_fin'
					$whereActivos
					$wTipoRad 
			GROUP BY U.USUA_NOMB
			ORDER BY USUARIO";


$queryEDetalle = "	SELECT DISTINCT	R.radi_nume_radi AS RADICADO,
							R.RADI_PATH AS HID_RADI_PATH,
							R.RADI_FECH_RADI AS FECHA_RADICADO,
							U.USUA_NOMB AS USUARIO,
							T.SGD_TPR_DESCRIP AS TIPO_DOCUMENTAL,
							R.RA_ASUN AS ASUNTO,
							R.RADI_DESC_ANEX AS DESC_ANEXO,
							R.RADI_NUME_HOJA AS NUM_HOJAS,
							DEP2.DEPE_NOMB AS DEPENDENCIA_ACTUAL,
							U2.USUA_NOMB AS USUARIO_ACTUAL,
							dbo.VALIDAR_ACCESO_RADEXP (R.RADI_NUME_RADI, '', '" . $_SESSION['login'] . "') AS HID_PERMISO
					FROM	RADICADO R
							JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = R.RADI_DEPE_RADI
								$wDepe
							JOIN USUARIO U ON 
								U.USUA_CODI = R.RADI_USUA_RADI AND 
								U.DEPE_CODI = R.RADI_DEPE_RADI
								$wUsua
							LEFT JOIN SGD_TPR_TPDCUMENTO T ON  
								R.TDOC_CODI = T.SGD_TPR_CODIGO
								$wTipoDoc
							LEFT JOIN SGD_EXP_EXPEDIENTE EX ON 
								EX.RADI_NUME_RADI = R.RADI_NUME_RADI AND 
								EX.SGD_EXP_ESTADO = 0 
							LEFT JOIN SGD_SEXP_SECEXPEDIENTES SEXP ON 
								SEXP.SGD_EXP_NUMERO = EX.SGD_EXP_NUMERO
							JOIN DEPENDENCIA DEP2 ON
								DEP2.DEPE_CODI = R.RADI_DEPE_ACTU
							JOIN USUARIO U2 ON
								U2.DEPE_CODI = R.RADI_DEPE_ACTU AND
								U2.USUA_CODI = R.RADI_USUA_ACTU
					WHERE	".$db->conn->SQLDate('Y/m/d', 'R.radi_fech_radi'). " BETWEEN '$fecha_ini' AND '$fecha_fin'
							$wTipoRad $wUsua 
					ORDER BY FECHA_RADICADO";

$queryETodosDetalle = $queryEDetalle
?>