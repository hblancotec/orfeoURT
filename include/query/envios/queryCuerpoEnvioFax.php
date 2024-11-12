<?php
	/**
	 * CONSULTA DE LOS RADICADOS ENVÍADOS VIA FAX Y VALIDA LOS QUE NO
	 * SE HAYAN DESCARGADO POR EL MODULO DE ENVIOS
	 */
	switch($db->driver) {
		case 'mssqlnative':
			$isql	=	"	SELECT	CONVERT (VARCHAR(15), R.RADI_NUME_RADI) AS RADICADO,
												".$db->conn->substr."(CONVERT(CHAR(3),A.SGD_DIR_TIPO),2,3) AS COPIA,
												R.RADI_FECH_RADI AS FECHA,
												A.ANEX_DESC AS DESCRIPCION,
												U.USUA_NOMB AS USUARIO,
												D.NUMERO_FAX AS FAX,
												CASE D.ESTADO_ENVIO_FAX
													WHEN 1 THEN 'Enviado Correctamente' 
													WHEN -1 THEN 'Error - Verificar luego' 
													WHEN -2 THEN 'Error - Max. intentos de envío' 
													ELSE 'Sin Verificar' END AS ESTADO,
												D.ID_FAX AS ID_FAX,
												D.SGD_DIR_CODIGO AS DIR_CODIGO,
                                                                                                A.SGD_DIR_TIPO
								FROM		RADICADO R
												JOIN ANEXOS A ON
													A.RADI_NUME_SALIDA = R.RADI_NUME_RADI
													AND A.ANEX_BORRADO = 'N'
												JOIN USUARIO U ON
													U.USUA_LOGIN = A.ANEX_CREADOR
												JOIN SGD_DIR_DRECCIONES D ON
													D.SGD_DIR_TIPO = A.SGD_DIR_TIPO AND
													D.RADI_NUME_RADI = A.RADI_NUME_SALIDA AND
													D.MREC_CODI = 2 AND 
                                                                                                        D.VOBO_FAX=0
								ORDER BY	FECHA, COPIA";
			break;
		case 'oracle':
		case 'oci8':
	    $isql = "	SELECT
								FROM
								WHERE
								ORDER BY";
		break;
	}
?>