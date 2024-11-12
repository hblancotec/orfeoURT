<?php
	switch($db->driver)
	{
	case 'mssqlnative':
		$isql = "SELECT
			convert(varchar(15),b.RADI_NUME_RADI) as RADI_NUME_RADI,a.ANEX_NOMB_ARCHIVO,a.ANEX_DESC,a.SGD_REM_DESTINO,a.SGD_DIR_TIPO,
			convert(varchar(15),a.ANEX_RADI_NUME) as ANEX_RADI_NUME, convert(varchar(15),a.RADI_NUME_SALIDA) as RADI_NUME_SALIDA,
			dir.SGD_DIR_NOMREMDES, dir.SGD_DIR_CODPOSTAL, dir.SGD_FENV_CODIGO, f.SGD_FENV_DESCRIP, a.ANEX_NUMERO, b.RADI_ANONIMO,
			convert(varchar(15),a.RADI_NUME_SALIDA) as RADI_NUME_SALIDA, dir.SGD_DIR_NOMREMDES, dir.SGD_DIR_CODPOSTAL, dir.SGD_DIR_MAIL    
		 FROM ANEXOS a inner join RADICADO b on a.radi_nume_salida=b.radi_nume_radi 
				inner join sgd_dir_drecciones dir on a.SGD_DIR_TIPO=dir.SGD_DIR_TIPO 
				left join SGD_FENV_FRMENVIO f on dir.SGD_FENV_CODIGO = f.SGD_FENV_CODIGO
		 WHERE a.RADI_NUME_SALIDA in(".$setFiltroSelect.")
                        and a.RADI_NUME_SALIDA=dir.RADI_NUME_RADI
                        and a.SGD_DIR_TIPO=dir.SGD_DIR_TIPO
			and anex_estado=2";
		break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	$isql = "SELECT
	        b.RADI_NUME_RADI as RADI_NUME_RADI,a.ANEX_NOMB_ARCHIVO,a.ANEX_DESC,a.SGD_REM_DESTINO,a.SGD_DIR_TIPO
  		   ,a.ANEX_RADI_NUME as ANEX_RADI_NUME, a.RADI_NUME_SALIDA as RADI_NUME_SALIDA
		 FROM ANEXOS a,RADICADO b
		 WHERE a.radi_nume_salida=b.radi_nume_radi
			and a.RADI_NUME_SALIDA in(".$setFiltroSelect.")
			and anex_estado=2";		break;
	}
?>