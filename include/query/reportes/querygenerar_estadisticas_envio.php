<?php
/**
  * CONSULTA VERIFICACION PREVIA A LA RADICACION
  */
switch($db->driver)
{
	case 'mssqlnative':
		$radi_nume_sal = "convert(varchar(15), a.RADI_NUME_SAL)";
		if (!empty($lista_depcod)) {
			$whereDependencia = $db->conn->substr."($radi_nume_sal, 5, 4)" . " in ($lista_depcod) and ";
		}
		$query = "select
				c.depe_nomb as DEPENDENCIA,
				$radi_nume_sal as RADICADO,
				a.sgd_renv_fech as FECHA_DE_ENVIO,
				d.sgd_fenv_descrip as FORMA_DE_ENVIO,
				a.sgd_renv_nombre as DESTINATARIO,
				a.sgd_renv_dir as DIRECCION,
                a.SGD_RENV_MAIL as EMAIL,
				a.sgd_renv_mpio as MUNICIPIO_DESTINO,
				$select102
				a.sgd_renv_depto as DEPARTAMENTO_DESTINO , u.USUA_NOMB as USUARIO  
		from	SGD_RENV_REGENVIO a  
				inner join dependencia c on ". $db->conn->substr. "($radi_nume_sal ,5, 4)=c.depe_codi 
				inner join SGD_FENV_FRMENVIO d on a.sgd_fenv_codigo=d.sgd_fenv_codigo
                inner join USUARIO u on u.USUA_DOC = cast(a.USUA_DOC as varchar)
				$inner102
		where	$whereDependencia
				a.sgd_renv_fech BETWEEN '".$fecha_ini." 00:00:00' and '" .$fecha_fin." 23:59:59'
				$where_tipo 
		order by ". $db->conn->substr."($radi_nume_sal, 5, 4), a.SGD_RENV_FECH DESC,a.SGD_RENV_PLANILLA DESC";
		
		$fecha_mes = substr($fecha_ini,0,7);
				
		$sqlSipost = "	SELECT	A.SGD_RENV_NOMBRE AS NOMBRE_DESTINATARIO,
								A.SGD_RENV_DIR AS DIRECCION,
								UPPER(A.SGD_RENV_MPIO + '_' + A.SGD_RENV_DEPTO) AS CIUDAD,
								A.SGD_RENV_PESO AS PESO,
								$radi_nume_sal + '_' + CONVERT(VARCHAR(4),A.SGD_DIR_TIPO) AS REFERENCIA
						FROM	SGD_RENV_REGENVIO A  
								JOIN DEPENDENCIA AS C ON 
									". $db->conn->substr. "($radi_nume_sal ,5, 4) = C.DEPE_CODI 
								JOIN SGD_FENV_FRMENVIO AS D ON
									A.SGD_FENV_CODIGO = D.SGD_FENV_CODIGO
								$inner102
						WHERE	$whereDependencia
								A.SGD_RENV_FECH BETWEEN '".$fecha_ini."' and '" .$fecha_fin."'
								$where_tipo 
						ORDER BY ". $db->conn->substr."($radi_nume_sal, 5, 4), 
								A.SGD_RENV_FECH DESC,
								A.SGD_RENV_PLANILLA DESC";
		break;		
	case 'oracle':
	case 'oci8':
	case 'oci805':
	$query = 'select  
		c.depe_nomb,
		a.radi_nume_sal,
		a.sgd_renv_nombre,
		a.sgd_renv_dir ,
		a.sgd_renv_mpio,
		a.sgd_renv_depto,
		a.sgd_renv_fech,
		a.sgd_renv_planilla,
		a.sgd_renv_cantidad,
		a.sgd_renv_valor,
		a.sgd_deve_fech,
		d.sgd_fenv_descrip
		from SGD_RENV_REGENVIO a, dependencia c, SGD_FENV_FRMENVIO d  ';
		$fecha_mes = substr($fecha_ini,0,7);
		// Si la variable $generar_listado_existente viene entonces este if genera la planilla existente
		$where_isql = ' WHERE a.sgd_renv_fech BETWEEN
		'.$db->conn->DBTimeStamp($fecha_ini).' and '.$db->conn->DBTimeStamp($fecha_fin).'
		and '.$db->conn->substr.'(a.radi_nume_sal, 5, 3)=c.depe_codi
		and a.sgd_fenv_codigo=d.sgd_fenv_codigo';
	$order_isql = '  ORDER BY  FECHA_DE_ENVIO'.$db->conn->substr.'(a.radi_nume_sal, 5, 3), a.SGD_RENV_FECH DESC,a.SGD_RENV_PLANILLA DESC';
	break;		
	}
?>
