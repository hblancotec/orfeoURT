<?php
if ($wTipoRad){
    $where = "WHERE" . str_replace('AND', '', $wTipoRad);
}

$queryE = "	SELECT  USUARIO, 
					SUM(RADICADOS) AS RADICADOS, 
                    HID_COD_USUARIO AS HID_COD_USUARIO
			FROM (  SELECT  U.USUA_NOMB USUARIO,
							COUNT($radi_nume_radi) RADICADOS, 
							MIN(U.USUA_CODI) HID_COD_USUARIO, 
							MIN(T.SGD_TPR_CODIGO) HID_TPR_CODIGO
					FROM	RADICADO R
							JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = R.RADI_DEPE_ACTU
								$wDepe
							JOIN USUARIO U ON 
								U.USUA_CODI = R.RADI_USUA_ACTU AND 
								U.DEPE_CODI = R.RADI_DEPE_ACTU 
								$wUsua 
							LEFT JOIN SGD_TPR_TPDCUMENTO T ON 
								T.SGD_TPR_CODIGO = R.TDOC_CODI 
								$wTipoDoc 
                            LEFT JOIN SGD_DIR_DRECCIONES D ON 
								D.RADI_NUME_RADI = R.RADI_NUME_RADI AND 
								D.SGD_DIR_TIPO = 1 
                    $where
                    GROUP BY U.USUA_NOMB
                    UNION
                    SELECT	U.USUA_NOMB USUARIO,
							COUNT($radi_nume_radi) RADICADOS, 
							MIN(U.USUA_CODI) HID_COD_USUARIO,
							MIN(T.SGD_TPR_CODIGO) HID_TPR_CODIGO
					FROM	SGD_RG_MULTIPLE RG
							JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = RG.AREA
								$wDepe
							JOIN USUARIO U ON 
								U.DEPE_CODI = RG.AREA AND 
								U.USUA_CODI = RG.USUARIO
								$wUsua
							JOIN RADICADO R ON 
								R.RADI_NUME_RADI = RG.RADI_NUME_RADI 
								$wTipoRad
							LEFT JOIN SGD_TPR_TPDCUMENTO T ON 
								T.SGD_TPR_CODIGO = R.TDOC_CODI 
								$wTipoDoc 
                            LEFT JOIN SGD_DIR_DRECCIONES D ON 
								D.RADI_NUME_RADI = RG.RADI_NUME_RADI AND 
								D.SGD_DIR_TIPO = 1 
                    WHERE	RG.ESTATUS = 'ACTIVO'
                    GROUP BY U.USUA_NOMB
                ) CONSULTA
			GROUP BY USUARIO, HID_COD_USUARIO
			ORDER BY USUARIO";


// CONSULTA PARA VER DETALLES 
$queryEDetalle = "	SELECT	DISTINCT $radi_nume_radi RADICADO, 
							R.RADI_FECH_RADI FECHA_RADICADO,
							T.SGD_TPR_DESCRIP TIPO_DOCUMENTO,
							U.USUA_NOMB USUARIO,
							R.RA_ASUN ASUNTO,
							D.SGD_DIR_NOMREMDES DEST_REM,
							R.RADI_PATH HID_RADI_PATH,
							dbo.VALIDAR_ACCESO_RADEXP (r.radi_nume_radi, '', '" . $_SESSION['login'] . "') AS HID_PERMISO
					FROM	RADICADO R
							JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = R.RADI_DEPE_ACTU
								$wDepe
							JOIN USUARIO U ON 
								U.USUA_CODI = R.RADI_USUA_ACTU AND 
								U.DEPE_CODI = R.RADI_DEPE_ACTU
								$wUsua
							LEFT JOIN SGD_TPR_TPDCUMENTO T ON 
								T.SGD_TPR_CODIGO = R.TDOC_CODI
								$wTipoDoc
							LEFT JOIN SGD_DIR_DRECCIONES D ON 
								D.RADI_NUME_RADI = R.RADI_NUME_RADI AND 
								D.SGD_DIR_TIPO = 1
					$where
					UNION
					SELECT	DISTINCT $radi_nume_radi RADICADO, 
							R.radi_fech_radi FECHA_RADICACION,
							T.SGD_TPR_DESCRIP TIPO_DOCUMENTO,
							U.USUA_NOMB USUARIO,
							R.RA_ASUN ASUNTO,
							D.SGD_DIR_NOMREMDES DEST_REM,
							R.RADI_PATH HID_RADI_PATH,
							dbo.VALIDAR_ACCESO_RADEXP (r.radi_nume_radi, '', '" . $_SESSION['login'] . "') AS HID_PERMISO
					FROM	SGD_RG_MULTIPLE RG
							JOIN DEPENDENCIA DEP ON
								DEP.DEPE_CODI = RG.AREA
								$wDepe
							JOIN USUARIO U ON 
								U.DEPE_CODI = RG.AREA AND 
								U.USUA_CODI = RG.USUARIO
								$wUsua
							JOIN RADICADO R ON 
								R.RADI_NUME_RADI = RG.RADI_NUME_RADI 
								$wTipoRad
							LEFT JOIN SGD_TPR_TPDCUMENTO T ON 
								T.SGD_TPR_CODIGO = R.TDOC_CODI 
								$wTipoDoc
							LEFT JOIN SGD_DIR_DRECCIONES D ON 
								D.RADI_NUME_RADI = RG.RADI_NUME_RADI AND 
								D.SGD_DIR_TIPO = 1	
					WHERE	RG.ESTATUS = 'ACTIVO'";

$orderE = "	ORDER BY 1";

// CONSULTA PARA VER TODOS LOS DETALLES
$queryETodosDetalle = $queryEDetalle . $orderE;
$detalles = $queryETodosDetalle;
$queryEDetalle .= $orderE;
?>