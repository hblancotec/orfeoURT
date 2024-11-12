<?php
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';	
switch($db->driver) {
	case 'mssqlnative':
	$isql = 'select b.id, b.RADI_FECH_RADI, convert(char(15), b.RADI_NUME_RADI) as "IDT_Numero Radicado"
				,b.RADI_PATH as "HID_RADI_PATH"
				,RADI_FECHA_VENCE as VENCE
				,RADI_DIAS_VENCE as DIASVENCE
				,'.$sqlFecha.' as "DAT_Fecha Radicado"
				,convert(char(15), b.RADI_NUME_RADI) as "HID_RADI_NUME_RADI"
				,UPPER(b.RA_ASUN)  as "Asunto"'.
				$colAgendado.
				',d.NOMBRE_DE_LA_EMPRESA as "'.$tip3Nombre[3][2].'"  
				,c.SGD_TPR_DESCRIP as "Tipo" 
				,b.RADI_USU_ANTE "Enviado Por"
				,convert(char(15),b.RADI_NUME_RADI) "CHK_CHKANULAR"
				,b.RADI_LEIDO "HID_RADI_LEIDO"
				,b.RADI_NUME_HOJA "HID_RADI_NUME_HOJA"
				,b.CARP_PER "HID_CARP_PER"
				,b.CARP_CODI "HID_CARP_CODI"
				,b.SGD_EANU_CODIGO "HID_EANU_CODIGO"
				,b.RADI_NUME_DERI "HID_RADI_NUME_DERI"
				,b.RADI_TIPO_DERI "HID_RADI_TIPO_DERI"
                                ,c.SGD_TPR_DESCRIP AS TIPO
                                ,c.sgd_tpr_termino AS TERMINO
		 from
		  radicado b
	         left outer join SGD_TPR_TPDCUMENTO c
	         on b.tdoc_codi = c.sgd_tpr_codigo
	         left outer join BODEGA_EMPRESAS d
	         on b.eesp_codi=d.identificador_empresa
                where b.radi_nume_radi is not null and c.SGD_TPR_CODIGO = b.tdoc_codi
		      and b.radi_depe_actu='.$dependencia.
		      $whereUsuario.$whereFiltro.'
		      '.$whereCarpeta.'
		      '.$sqlAgendado.'
	        order by '.$order .' ' .$orderTipo;                
	break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
	$isql = 'select
				to_char(b.RADI_NUME_RADI) as "IDT_Numero Radicado"
				,b.RADI_PATH as "HID_RADI_PATH"
				,'.$sqlFecha.' as "DAT_Fecha Radicado"
				,to_char(b.RADI_NUME_RADI) as "HID_RADI_NUME_RADI"
				,UPPER(b.RA_ASUN)  as "Asunto"'.
				$colAgendado.
				',d.NOMBRE_DE_LA_EMPRESA '.$tip3Nombre[3][2].'
				,c.SGD_TPR_DESCRIP as "Tipo Documento"
				,round(((radi_fech_radi+(c.sgd_tpr_termino * 7/5))-sysdate)) as "Dias Restantes"
				,b.RADI_USU_ANTE "Enviado Por"
				,to_char(b.RADI_NUME_RADI) "CHK_CHKANULAR"
				,b.RADI_LEIDO "HID_RADI_LEIDO"
				,b.RADI_NUME_HOJA "HID_RADI_NUME_HOJA"
				,b.CARP_PER "HID_CARP_PER"
				,b.CARP_CODI "HID_CARP_CODI"
				,b.SGD_EANU_CODIGO "HID_EANU_CODIGO"
				,b.RADI_NUME_DERI "HID_RADI_NUME_DERI"
				,b.RADI_TIPO_DERI "HID_RADI_TIPO_DERI"
		 from
		 radicado b,
		 SGD_TPR_TPDCUMENTO c,
		 BODEGA_EMPRESAS d
	 where
		b.radi_nume_radi is not null
		and b.radi_depe_actu='.$dependencia.
		$whereUsuario.$whereFiltro.
		'and b.tdoc_codi=c.sgd_tpr_codigo (+)
		and b.eesp_codi=d.identificador_empresa (+)
		'.$whereCarpeta.'
		'.$sqlAgendado.'
	  order by '.$order .' ' .$orderTipo;
	break;
	case 'postgres':
$isql = 'select b.RADI_NUME_RADI as "IDT_Numero Radicado"
				,b.RADI_PATH as "HID_RADI_PATH"
				,'.$sqlFecha.' as "DAT_Fecha Radicado"
				, b.RADI_NUME_RADI as "HID_RADI_NUME_RADI"
				,UPPER(b.RA_ASUN)  as "Asunto"'.
				$colAgendado.
				',d.NOMBRE_DE_LA_EMPRESA  as '.$tip3Nombre[3][2].'
				,c.SGD_TPR_DESCRIP as "Tipo Documento"
				,b.RADI_USU_ANTE as "Enviado Por"
				,b.RADI_NUME_RADI as "CHK_CHKANULAR"
				,b.RADI_LEIDO as "HID_RADI_LEIDO"
				,b.RADI_NUME_HOJA as "HID_RADI_NUME_HOJA"
				,b.CARP_PER as "HID_CARP_PER"
				,b.CARP_CODI as "HID_CARP_CODI"
				,b.SGD_EANU_CODIGO as "HID_EANU_CODIGO"
				,b.RADI_NUME_DERI as "HID_RADI_NUME_DERI"
				,b.RADI_TIPO_DERI as "HID_RADI_TIPO_DERI"
		 from
		 radicado b
	left outer join SGD_TPR_TPDCUMENTO c
	on b.tdoc_codi=c.sgd_tpr_codigo
	left outer join BODEGA_EMPRESAS d
	on b.eesp_codi=d.identificador_empresa
    where 
		b.radi_nume_radi is not null
		and b.radi_depe_actu='.$dependencia.
		$whereUsuario.$whereFiltro.'
		'.$whereCarpeta.'
		'.$sqlAgendado.'
	  order by '.$order .' ' .$orderTipo;
	break;
	}
?>
