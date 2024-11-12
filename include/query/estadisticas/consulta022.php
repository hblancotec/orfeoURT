<?php 

if ($tipoCambio == 1) {
    $transac = " H.SGD_TTR_CODIGO = 107 ";
} else if ($tipoCambio == 2) {
    $transac = " H.SGD_TTR_CODIGO = 34 ";
} else {
    $transac = " (H.SGD_TTR_CODIGO = 34 or H.SGD_TTR_CODIGO = 107) ";
}

$queryE = "SELECT U.USUA_NOMB, D.DEPE_NOMB, count(R.RADI_NUME_RADI) AS CAMBIOS, U.USUA_CODI AS HID_COD_USUARIO,
					U.USUA_DOC AS HID_USUA_DOC 
            FROM RADICADO R INNER JOIN HIST_EVENTOS H ON R.RADI_NUME_RADI = H.RADI_NUME_RADI 
            	INNER JOIN USUARIO U ON U.USUA_DOC = H.USUA_DOC 
            	INNER JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI 
            WHERE $transac AND ".$db->conn->SQLDate('Y/m/d', 'H.HIST_FECH')." BETWEEN '$fecha_ini' AND '$fecha_fin'
                $wDepe2
            GROUP BY U.USUA_NOMB, D.DEPE_NOMB, U.USUA_CODI, U.USUA_DOC";

$queryEDetalle = "SELECT R.RADI_NUME_RADI AS RADICADO, R.RADI_PATH AS HID_RADI_PATH, U.USUA_CODI AS HID_COD_USUARIO,
					U.USUA_DOC AS HID_USUA_DOC, R.RADI_FECH_RADI AS FECHA_RADICADO, H.HIST_FECH AS [FECHA ACCION],  
                    H.HIST_OBSE AS OBSERVACIONES,
                    dbo.VALIDAR_ACCESO_RADEXP (R.RADI_NUME_RADI, '', '" . $_SESSION['login'] . "') AS HID_PERMISO
                FROM RADICADO R INNER JOIN HIST_EVENTOS H ON R.RADI_NUME_RADI = H.RADI_NUME_RADI 
                	INNER JOIN USUARIO U ON U.USUA_DOC = H.USUA_DOC 
                	INNER JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI 
                WHERE $transac AND ".$db->conn->SQLDate('Y/m/d', 'H.HIST_FECH')." BETWEEN '$fecha_ini' AND '$fecha_fin'
                	$wDepe2 $wUsua ";

$queryETodosDetalle = $queryEDetalle
?>