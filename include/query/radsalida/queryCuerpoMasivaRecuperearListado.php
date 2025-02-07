<?php
	switch($db->driver) {
	case 'mssqlnative':
	 $isql = "select  r.RADI_NUME_GRUPO as RADI_NUME_GRUPO,
                        count(*) as DOCUMENTOS,
                        min(convert(char(15),r.RADI_NUME_SAL)) as RAD_INI,
                        MAX(convert(char(15),r.RADI_NUME_SAL)) as  RAD_FIN,
                        $sqlFecha AS FECHA,
                        r.USUA_DOC,
                        rd.TDOC_CODI 
					FROM sgd_renv_regenvio  r,
                        radicado rd 
		            WHERE  r.sgd_renv_tipo <> 0  and
                        RADI_NUME_GRUPO  is not  null and
			            rd.RADI_NUME_RADI= r.RADI_NUME_GRUPO 
			   $dependencia_busq2
               group by r.radi_nume_grupo, $sqlFecha,r.usua_doc,rd.TDOC_CODI  
			   order by $order ";
	break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
 $isql = "select r.RADI_NUME_GRUPO
	                  , count(*) as documentos
	                  , min(r.RADI_NUME_SAL) as RAD_INI
	                  , MAX(r.RADI_NUME_SAL) as  RAD_FIN 
					  , $sqlFecha AS FECHA  
						, USUA_DOC
						,rd.TDOC_CODI 
						from sgd_renv_regenvio  r , radicado rd 
			   WHERE  r.sgd_renv_tipo <> 0  and RADI_NUME_GRUPO  is not  null and
				  rd.RADI_NUME_RADI= r.RADI_NUME_GRUPO 
			   $dependencia_busq2
               group by r.radi_nume_grupo, $sqlFecha,usua_doc,rd.TDOC_CODI  
			   order by $order ";
	break;
	}
?>
