<?php
/**
 * Query firma digital
 *
 * @version 1.0
 * @author n-n
 *
 * @version 1.1
 * @author cesgomez
 */
switch ($db->driver) { 
	case 'oci8':
	case 'oracle':
	
		$query= ' 
				 select  1 as "HID_1",
				 to_char(r.RADI_NUME_RADI) as "IDT_Numero Radicado",
				 r.RADI_PATH as "HID_RADI_PATH",
				 uf.USUA_NOMB as "Fimante",
				  us.USUA_NOMB as "Solicitado Por",
				  fr.SGD_FIRRAD_FECHSOLIC as "Desde", 
				   to_char (r.RADI_NUME_RADI) AS "CHK_SOL_FIRMA"  
		         from usuario uf, usuario us,SGD_FIRRAD_FIRMARADS fr, radicado r
		         where  
		         fr.USUA_DOC = uf.USUA_DOC and
		         fr.SGD_FIRRAD_DOCSOLIC = us.USUA_DOC and
		         r.RADI_NUME_RADI = fr.RADI_NUME_RADI  
		         and fr.USUA_DOC = '."'$usua_doc'  
				 and SGD_FIRRAD_FIRMA is null 
		         ".
		         $whereFiltro;
	break;
	case 'mssqlnative': 		         
	    $query= "select distinct m.idcf as CHK_SOL_FIRMA, m.radi_nume_radi as Radicado, 
                    r.ra_asun as Asunto, m.rutapdf as HID_ruta, us.usua_nomb as Solicitante,
                    uf.USUA_NOMB as Firmante, uf.USUA_LOGIN as login, m.sgd_ciclo_fechasol as Fecha_Solicitud, m.estado as estado,
                    d.estado as estado_detalle
		         from SGD_CICLOFIRMADOMASTER m
		         inner join SGD_CICLOFIRMADODETALLE d on d.idcf=m.idcf
		         inner join USUARIO us on us.USUA_DOC=m.usua_doc
		         left join USUARIO uf on uf.USUA_DOC=d.usua_doc
                 inner join radicado r on r.radi_nume_radi=m.radi_nume_radi
		         where ( ( m.usua_doc='".$_SESSION['usua_doc']."' and m.estado in (1,2)) or
						 ( d.usua_doc='".$_SESSION['usua_doc']."' and m.estado = 1 ) )
                order by m.sgd_ciclo_fechasol ";
	    break;
	}
	
?>
