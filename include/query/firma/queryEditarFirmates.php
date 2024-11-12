<?php
switch ($db->driver) {
    case 'oci8': 
    case 'oracle':
        
        $query = ' 
				 select  1 as "HID_1",
				  uf.USUA_NOMB as "Fimante",
				  us.USUA_NOMB as "Solicitado Por",
				  fr.SGD_FIRRAD_FECHSOLIC as "Desde", 
				  fr.SGD_FIRRAD_FECHA   as "Firmado", 
				   to_char (fr.SGD_FIRRAD_ID) AS "CHK_SOL_FIRMA"  
		         from usuario uf, usuario us,SGD_FIRRAD_FIRMARADS fr
		         where  
		         fr.USUA_DOC = uf.USUA_DOC and
		         fr.SGD_FIRRAD_DOCSOLIC = us.USUA_DOC ' . $filtroSelect;
        break;
    case 'mssqlnative':
        $query = ' 
				 select  1 as "HID_1",
				  uf.USUA_NOMB as "Fimante",
				  us.USUA_NOMB as "Solicitado Por",
				  fr.SGD_FIRRAD_FECHSOLIC as "Desde", 
				  fr.SGD_FIRRAD_FECHA   as "Firmado", 
				  convert(char(15),fr.SGD_FIRRAD_ID) AS "CHK_SOL_FIRMA"  
		         from usuario uf, usuario us,SGD_FIRRAD_FIRMARADS fr
		         where  
		         fr.USUA_DOC = uf.USUA_DOC and
		         fr.SGD_FIRRAD_DOCSOLIC = us.USUA_DOC ' . $filtroSelect;
        $query= "select d.iddcf as CHK_SOL_FIRMA, m.radi_nume_radi as Radicado,
                    m.rutapdf as HID_ruta, us.usua_nomb as Solicitante, 
                    uf.USUA_NOMB as Firmante,m.sgd_ciclo_fechasol as Fecha_Solicitud 
		         from SGD_CICLOFIRMADOMASTER m
		         inner join SGD_CICLOFIRMADODETALLE d on d.idcf=m.idcf and d.estado=0 
		         inner join USUARIO us on us.USUA_DOC=m.usua_doc
		         left join USUARIO uf on uf.USUA_DOC=d.usua_doc
		         where 1=1 $filtroSelect";
        break;
}

?>
