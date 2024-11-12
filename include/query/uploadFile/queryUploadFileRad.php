<?php
	/**
	  * CONSULTA DE UPLOAD FILE
	  * @author JAIRO LOSADA DNP - SSPD 2006/03/01
	  * @version 3.5.1
	  *
	  * @param $query String Almacena Consulta que se enviara
	  * @param $sqlFecha String  Almacena fecha en  formato Y-m-d que devuelve ADODB para la base de datos escogida
	  */
	$sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","RADI_FECH_RADI");
	$tmp = $db->conn->substr."(RADI_PATH,". $db->conn->length."(RADI_PATH)-2,3) IN ('doc','odt','rtf') ";
	switch($db->driver) {
	case 'mssqlnative':
		$tmp = " RIGHT(RADI_PATH, CHARINDEX('.', REVERSE('.' + RADI_PATH)) - 1) IN ('docx', 'doc', 'tif', 'tiff', 'rtf')";
		$query = "	SELECT	convert(char(15), RADI_NUME_RADI) as IDT_Numero_Radicado,
							RADI_PATH as HID_RADI_PATH,
							$sqlFecha as DAT_Fecha_Radicado,
							RADI_NUME_DERI AS RADICADO_PADRE,
							RADI_NUME_RADI as HID_RADI_NUME_RADI,
							RA_ASUN ASUNTO,
							convert(varchar(15), radi_nume_radi) CHR_DATO
					FROM	RADICADO
					WHERE	$busq_radicados_tmp
							AND ($tmp OR RADI_PATH IS NULL)
					ORDER BY RADI_FECH_RADI DESC";
		$query2 = "	SELECT	RADI_NUME_RADI as IDT_Numero_Radicado,
							RADI_PATH as HID_RADI_PATH,
							$sqlFecha as DAT_Fecha_Radicado,
							RADI_NUME_DERI RADICADO_PADRE,
							RADI_NUME_RADI as HID_RADI_NUME_RADI,
							RA_ASUN ASUNTO
					FROM	RADICADO
					WHERE	$busq_radicados_tmp";
		$query3	= "	SELECT	RADI_NUME_SALIDA
					FROM	RADICADO 
							JOIN ANEXOS ON RADI_NUME_SALIDA = RADI_NUME_RADI
					WHERE	$busq_radicados_tmp";
	break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	$query = "SELECT
			RADI_NUME_RADI as IDT_Numero_Radicado,
			RADI_PATH as HID_RADI_PATH,
			$sqlFecha as DAT_Fecha_Radicado,
			RADI_NUME_DERI RADICADO_PADRE,
			RADI_NUME_RADI as HID_RADI_NUME_RADI,
			RA_ASUN ASUNTO,
			RADI_NUME_RADI CHR_DATO
			FROM RADICADO
			 WHERE
			 $busq_radicados_tmp
			 AND RADI_PATH IS NULL
			 ORDER BY RADI_FECH_RADI DESC";
	$query2 = "SELECT
			RADI_NUME_RADI as IDT_Numero_Radicado,
			RADI_PATH as HID_RADI_PATH,
			$sqlFecha as DAT_Fecha_Radicado,
			RADI_NUME_DERI RADICADO_PADRE,
			RADI_NUME_RADI as HID_RADI_NUME_RADI,
			RA_ASUN ASUNTO
			FROM RADICADO
			 WHERE
			 $busq_radicados_tmp
			 ORDER BY RADI_FECH_RADI DESC";
	break;
	}
?>
