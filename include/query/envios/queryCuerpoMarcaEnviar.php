<?php

/**
 * CONSULTA VERIFICACION PREVIA A LA RADICACION
 */
switch ($db->driver) {
    case 'mssqlnative':
        $isql = 'select a.anex_estado CHU_ESTADO
            ,a.anex_estado_email CHU_ESTADO_EMAIL
            ,a.sgd_deve_codigo HID_DEVE_CODIGO
            ,a.sgd_deve_fech AS HID_SGD_DEVE_FECH
            ,convert(char(15),a.radi_nume_salida) AS IMG_RADICADO_SALIDA
            ,c.RADI_PATH HID_RADI_PATH
                        ,' . $db->conn->substr . '(convert(char(3),a.sgd_dir_tipo),2,3) AS "COPIA"
			,convert(char(15),a.anex_radi_nume) AS RADICADO_PADRE
			,c.radi_fech_radi AS FECHA_RADICADO
			,a.anex_desc AS DESCRIPCION
			,a.sgd_fech_impres AS FECHA_IMPRESION
			,a.anex_creador AS GENERADO_POR
                        ,convert(char(15), a.radi_nume_salida) AS "CHK_RADI_NUME_SALIDA"
			,a.sgd_deve_codigo HID_DEVE_CODIGO1
			,a.anex_estado HID_ANEX_ESTADO1
                        ,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO"
                        ,a.anex_tamano AS "HID_ANEX_TAMANO"
			,a.anex_radi_fech AS "HID_ANEX_RADI_FECH"
			,' . "'WWW'" . ' AS "HID_WWW"
			,' . "'9999'" . ' AS "HID_9999"
			,a.anex_tipo AS "HID_ANEX_TIPO"
			,a.anex_radi_nume AS "HID_ANEX_RADI_NUME"
			,a.sgd_dir_tipo AS "HID_SGD_DIR_TIPO"
			,a.sgd_deve_codigo AS "HID_SGD_DEVE_CODIGO"
            ,a.SGD_FENV_CODIGO AS "HID_SGD_FENV_CODIGO"
                        ,CASE a.sgd_dir_tipo WHEN 1 THEN \'1\'
                        ELSE ' . $db->conn->substr . '((CAST(a.sgd_dir_tipo as varchar)), 2,3)
                        END AS COPIA0
                        ,d.sgd_dir_nomremdes as "DESTINATARIO"
                        ,m.SGD_FENV_DESCRIP as MEDIO_ENVIO
                        ,m.SGD_FENV_CODIGO as IDMEDIO
                        ,d.estado_envio_fax
                        ,d.sgd_dir_codigo as SGDDIRCODIGO
            ,c.RADI_TIPORAD
		from anexos a inner join usuario b on a.anex_creador = b.usua_login
                inner join radicado c on a.radi_nume_salida=c.radi_nume_radi
                inner join sgd_dir_drecciones d on d.radi_nume_radi=a.radi_nume_salida and a.sgd_dir_tipo=d.sgd_dir_tipo and a.sgd_dir_tipo != 7
                left join SGD_FENV_FRMENVIO m on m.sgd_fenv_codigo = d.sgd_fenv_codigo
	    where ANEX_ESTADO>=' . $estado_sal . ' ' .
	    $dependencia_busq2 . '
				and a.ANEX_ESTADO <= ' . $estado_sal_max . '
				and a.radi_nume_salida=c.radi_nume_radi
				and a.anex_creador=b.usua_login
				and a.anex_borrado= ' . "'N'" . '
				AND
				((c.SGD_EANU_CODIGO <> 2
				AND c.SGD_EANU_CODIGO <> 1)
				or c.SGD_EANU_CODIGO IS NULL)
                order by ' . $order . ' ';
        break;
    case 'oracle':
    case 'oci8':
    case 'oci805':
        $isql = 'select
			a.anex_estado CHU_ESTADO
		 	,a.sgd_deve_codigo HID_DEVE_CODIGO
			,a.sgd_deve_fech AS HID_SGD_DEVE_FECH
		    ,TO_CHAR(a.radi_nume_salida) AS IMG_RADICADO_SALIDA
			,c.RADI_PATH AS HID_RADI_PATH
            ,substr(trim(a.sgd_dir_tipo),2,3) AS COPIA
			,TO_CHAR(a.anex_radi_nume) AS RADICADO_PADRE
			,c.radi_fech_radi AS FECHA_RADICADO
			,a.anex_desc AS DESCRIPCION
			,a.sgd_fech_impres AS FECHA_IMPRESION
			,a.anex_creador AS GENERADO_POR
	        ,a.radi_nume_salida AS "CHK_RADI_NUME_SALIDA"
			,a.sgd_deve_codigo HID_DEVE_CODIGO1
			,a.anex_estado HID_ANEX_ESTADO1
		    ,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO"
		    ,a.anex_tamano AS "HID_ANEX_TAMANO"
			,a.ANEX_RADI_FECH AS "HID_ANEX_RADI_FECH"
			,' . "'WWW'" . ' AS "HID_WWW"
			,' . "'9999'" . ' AS "HID_9999"
			,a.anex_tipo AS "HID_ANEX_TIPO"
			,a.anex_radi_nume AS "HID_ANEX_RADI_NUME"
			,a.sgd_dir_tipo AS "HID_SGD_DIR_TIPO"
			,a.sgd_deve_codigo AS "HID_SGD_DEVE_CODIGO"
		from anexos a,usuario b, radicado c
	    where a.ANEX_ESTADO>=' . $estado_sal . ' ' .
                $dependencia_busq2 . '
				and a.ANEX_ESTADO <= ' . $estado_sal_max . '
				and a.radi_nume_salida=c.radi_nume_radi
				and a.anex_creador=b.usua_login
				and a.anex_borrado= ' . "'N'" . '
				and a.sgd_dir_tipo != 7
				and (a.sgd_deve_codigo >= 90 or a.sgd_deve_codigo =0 or a.sgd_deve_codigo is null)
				AND
				((c.SGD_EANU_CODIGO != 2
				AND c.SGD_EANU_CODIGO != 1)
				or c.SGD_EANU_CODIGO IS NULL)
		order by ' . $order . ' ';
        break;
}
?>