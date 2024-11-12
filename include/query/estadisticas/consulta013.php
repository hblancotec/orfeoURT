<?php
if ($dependencia_busq != 99999) {
    $wDep = str_pad($dependencia_busq, 4, "0", STR_PAD_LEFT);
    $wDep2 = "AND S.depe_codi = " . $dependencia_busq;
    $wDep3 = str_pad($dependencia_busq, 3, "0", STR_PAD_LEFT);
} else {
    $wDep = "";
    $wDep2 = "";
}

if ($fechAno_busq != 55555) {
    $wAno = "AND SUBSTRING(S.SGD_EXP_NUMERO,1,4) = '" . $fechAno_busq . "'";
} else {
    $wAno = "";
}

if ($serie_busq != 22222) {
    $wSer = "AND S.SGD_SRD_CODIGO = " . $serie_busq;
    if ($_POST['subSerie_busq'] != 33333) {
        $wSer = $wSer . " AND S.SGD_SBRD_CODIGO = " . $_POST['subSerie_busq'];
    }
} else {
    $serieList = explode(";", $_POST['itCodigoSerie']);
    if (count($serieList) > 1) {
        for ($xy = 0; $xy < count($serieList) - 1; $xy ++) {
            if ($xy == 0) {
                $wSer = "AND ( S.SGD_SRD_CODIGO = " . $serieList[$xy];
            } else {
                $wSer .= " OR S.SGD_SRD_CODIGO = " . $serieList[$xy];
            }
        }
        $wSer .= " ) ";
    } else {
        $wSer = "";
    }
}

if ($_POST['codus']) {
    $wUsu = "JOIN USUARIO U ON
				U.DEPE_CODI = " . $dependencia_busq . " AND
				U.USUA_CODI = " . $_POST['codus'] . " AND
				S.USUA_DOC_RESPONSABLE = U.USUA_DOC";
}

// ## CONSULTA GENERAL
//se anula la condición
$queryE = "	SELECT	S.SGD_EXP_NUMERO AS No_EXPEDIENTE, 
					S.SGD_SEXP_PAREXP1 + ' ' + S.SGD_SEXP_PAREXP2 AS NOMBRE_EXPEDIENTE,
					CASE S.SGD_SEXP_ESTADO WHEN 1 THEN 'INACTIVO' ELSE 'ACTIVO' END AS ESTADO,
					COUNT (X.ID) AS CANT_DOCUMENTOS,

                    (select CONVERT(NVARCHAR, CAST(MIN(FECHA) AS DATETIME), 111) AS FECHA from
                		(SELECT CONVERT(varchar, MIN(R.RADI_FECH_RADI), 5) AS FECHA
                			FROM RADICADO R LEFT JOIN SGD_EXP_EXPEDIENTE E ON R.RADI_NUME_RADI = E.RADI_NUME_RADI
                		WHERE E.SGD_EXP_NUMERO = S.sgd_exp_numero AND E.SGD_EXP_ESTADO != 2
                		union all
                		SELECT CONVERT(varchar, MIN(A.ANEXOS_EXP_FECH_CREA), 5) AS FECHA
                			FROM SGD_SEXP_SECEXPEDIENTES E INNER JOIN SGD_ANEXOS_EXP A ON A.SGD_EXP_NUMERO = E.SGD_EXP_NUMERO
                		WHERE A.SGD_EXP_NUMERO = S.sgd_exp_numero ) AS TABLA
                		where FECHA IS NOT NULL) AS FECHA_INICIAL,

					(select CONVERT(NVARCHAR, CAST(MAX(FECHA) AS DATETIME), 111) AS FECHA from
                		(SELECT CONVERT(varchar, MAX(R.RADI_FECH_RADI), 5) AS FECHA
                			FROM RADICADO R LEFT JOIN SGD_EXP_EXPEDIENTE E ON R.RADI_NUME_RADI = E.RADI_NUME_RADI
                		WHERE E.SGD_EXP_NUMERO = S.sgd_exp_numero AND E.SGD_EXP_ESTADO != 2
                		union all
                		SELECT CONVERT(varchar, MAX(A.ANEXOS_EXP_FECH_CREA), 5) AS FECHA
                			FROM SGD_SEXP_SECEXPEDIENTES E INNER JOIN SGD_ANEXOS_EXP A ON A.SGD_EXP_NUMERO = E.SGD_EXP_NUMERO
                		WHERE A.SGD_EXP_NUMERO = S.sgd_exp_numero ) AS TABLA
                		where FECHA IS NOT NULL) AS FECHA_FINAL

			FROM	SGD_SEXP_SECEXPEDIENTES S
					LEFT JOIN ( SELECT E.SGD_EXP_NUMERO, E.RADI_NUME_RADI AS ID FROM SGD_EXP_EXPEDIENTE E 
                            	WHERE E.SGD_EXP_ESTADO != 2 AND  (
                                    (LEN(E.SGD_EXP_NUMERO) = 19 
                                         AND SUBSTRING(E.SGD_EXP_NUMERO,5,3) = '$wDep3'
                                    ) 
                                    OR 
                                    (LEN(E.SGD_EXP_NUMERO) = 20 
                                         AND SUBSTRING(E.SGD_EXP_NUMERO,5,4) = '$wDep')
                                  )
                            	UNION 
                            	SELECT A.SGD_EXP_NUMERO, A.ANEXOS_EXP_ID AS ID 
                            	FROM SGD_ANEXOS_EXP A 
                            	WHERE A.ANEXOS_EXP_ESTADO = 0 AND (
                                    (LEN(A.SGD_EXP_NUMERO) = 19 
                                         AND SUBSTRING(A.SGD_EXP_NUMERO,5,3) = '$wDep3'
                                    ) 
                                    OR 
                                    (LEN(A.SGD_EXP_NUMERO) = 20 
                                         AND SUBSTRING(A.SGD_EXP_NUMERO,5,4) = '$wDep')
                                  ) )  AS X ON X.SGD_EXP_NUMERO = S.SGD_EXP_NUMERO  
					$wUsu			
			WHERE	1 = 1
					$wDep2
					$wAno
					$wSer
					
			GROUP BY S.SGD_EXP_NUMERO, S.SGD_SEXP_PAREXP1, S.SGD_SEXP_PAREXP2, S.SGD_SEXP_ESTADO
			ORDER BY S.SGD_EXP_NUMERO";

// ## CONSULTA PARA VER DETALLES
// Ibis: Se modifico la consulta pra organizar el indice electronico con indice 1
$queryEDetalle = "SELECT * FROM(SELECT	A.SGD_EXP_NUMERO AS No_EXPEDIENTE,
							A.ANEXOS_EXP_NOMBRE AS RADICADO,
							A.ANEXOS_EXP_FECH_CREA AS FECHA_RADICADO,
							U.USUA_NOMB AS USUARIO_CREADOR,
							D.DEPE_NOMB AS DEPENDENCIA,
							A.ANEXOS_EXP_DESC AS ASUNTO,
							A.ANEXOS_EXP_PATH AS HID_RADI_PATH,
							dbo.VALIDAR_ACCESO_RADEXP (0, A.SGD_EXP_NUMERO, '" . $_SESSION['login'] . "') AS HID_PERMISO,
							'1900-01-01' AS HID_FECHA_P, A.ANEXOS_EXP_ORDEN AS RADID
					FROM	SGD_ANEXOS_EXP A
							JOIN USUARIO U ON U.USUA_LOGIN = A.USUA_LOGIN_CREA
							JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI
					WHERE	A.ANEXOS_EXP_ESTADO = 0
							AND A.SGD_EXP_NUMERO = '" . $expediente . "'
							AND A.ANEXOS_EXP_NOMBRE like '%indice_%') AS A1
							    
UNION ALL
							    
SELECT * FROM(SELECT E.SGD_EXP_NUMERO AS No_EXPEDIENTE,
							convert(varchar(15), E.RADI_NUME_RADI) AS RADICADO,
							R.RADI_FECH_RADI AS FECHA_RADICADO,
							U.USUA_NOMB AS USUARIO_CREADOR,
							DEP.DEPE_NOMB AS DEPENDENCIA,
							R.RA_ASUN AS ASUNTO,
							R.RADI_PATH AS HID_RADI_PATH,
							dbo.VALIDAR_ACCESO_RADEXP (R.RADI_NUME_RADI, '', '" . $_SESSION['login'] . "') AS HID_PERMISO,
							R.RADI_FECH_RADI AS HID_FECHA_P, R.RADI_FECH_RADI AS RADID
					FROM	SGD_EXP_EXPEDIENTE E
							INNER JOIN RADICADO R ON R.RADI_NUME_RADI = E.RADI_NUME_RADI
							LEFT JOIN USUARIO U ON U.USUA_DOC = E.USUA_DOC
							INNER JOIN DEPENDENCIA DEP ON DEP.DEPE_CODI = E.DEPE_CODI
					WHERE	E.SGD_EXP_ESTADO != 2 AND
							E.SGD_EXP_NUMERO = '" . $expediente . "'
							    
					UNION
							    
					SELECT	A.SGD_EXP_NUMERO AS No_EXPEDIENTE,
							A.ANEXOS_EXP_NOMBRE AS RADICADO,
							A.ANEXOS_EXP_FECH_CREA AS FECHA_RADICADO,
							U.USUA_NOMB AS USUARIO_CREADOR,
							D.DEPE_NOMB AS DEPENDENCIA,
							A.ANEXOS_EXP_DESC AS ASUNTO,
							A.ANEXOS_EXP_PATH AS HID_RADI_PATH,
							dbo.VALIDAR_ACCESO_RADEXP (0, A.SGD_EXP_NUMERO, '" . $_SESSION['login'] . "') AS HID_PERMISO,
							ANEXOS_EXP_FECH_CREA AS HID_FECHA_P, A.ANEXOS_EXP_ORDEN AS RADID
					FROM	SGD_ANEXOS_EXP A
							JOIN USUARIO U ON U.USUA_LOGIN = A.USUA_LOGIN_CREA
							JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI
					WHERE	A.ANEXOS_EXP_ESTADO = 0
							AND A.SGD_EXP_NUMERO = '" . $expediente . "'
							AND A.ANEXOS_EXP_NOMBRE not like '%indice_%') AS A2
ORDER BY FECHA_RADICADO, RADID"; // IBIS condicion adicional para indice

$queryETodosDetalle = $queryEDetalle;
$queryE = str_replace('substr', 'substring', $queryE);
$queryETodosDetalle = str_replace('substr', 'substring', $queryETodosDetalle);
$queryEDetalle = str_replace('substr', 'substring', $queryEDetalle);
?>