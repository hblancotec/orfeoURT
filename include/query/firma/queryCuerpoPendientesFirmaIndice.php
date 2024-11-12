<?php
//Ibiscom
//$idToChar =  $db->conn->numToString("fr.SGD_FIRRAD_ID"); 

switch ($db->driver) { 
	case 'oci8':
	case 'oracle':
	
		$query2= ' 
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
        $query2= "select distinct m.idcf as CHK_SOL_FIRMA, m.radi_nume_radi as Radicado,
                    m.rutapdf as HID_ruta, us.usua_nomb as Solicitante, 
                    uf.USUA_NOMB as Firmante,m.sgd_ciclo_fechasol as Fecha_Solicitud, m.estado as estado,
					m.anex_indice as indice  
		         from SGD_CICLOFIRMADOMASTER m
		         inner join SGD_CICLOFIRMADODETALLE d on d.idcf=m.idcf
		         inner join USUARIO us on us.USUA_DOC=m.usua_doc
		         left join USUARIO uf on uf.USUA_DOC=d.usua_doc               
		         where (m.usua_doc='".$_SESSION['usua_doc']."' or d.usua_doc='".$_SESSION['usua_doc']."') and m.radi_nume_radi is null and m.estado in (1,2) $whereFiltro";
		break;
	}
	
?>
