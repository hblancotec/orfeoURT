<?php
	/**
	  * CONSULTA VERIFICACION PREVIA A LA RADICACION
	  */
	switch($db->driver)
	{
	case 'mssqlnative':
	  $isql = "select convert(char(15), r.RADI_NUME_GRUPO) as RADI_NUME_GRUPO,
                        count(*) as DOCUMENTOS,
                      min( convert(char(15), r.RADI_NUME_SAL) ) as RAD_INI,
                      MAX(convert(char(15), r.RADI_NUME_SAL)) as  RAD_FIN,
                      $sqlFecha AS FECHA,
                      r.USUA_DOC,
                      rd.TDOC_CODI 
				from sgd_renv_regenvio  r,
                        radicado rd 
			   WHERE r.sgd_renv_planilla = '00' and r.sgd_renv_tipo = 1 and 
				    rd.RADI_NUME_RADI= r.RADI_NUME_GRUPO and
                    sgd_depe_genera = '$dep_sel'
               group by r.radi_nume_grupo, $sqlFecha,r.usua_doc,rd.TDOC_CODI 
			   order by $order ";	
		break;
	case 'oracle':
	case 'oci8':
	    $isql = "select r.RADI_NUME_GRUPO
	                  , count(*) as DOCUMENTOS
	                  , min(r.RADI_NUME_SAL) as RAD_INI
	                  , MAX(r.RADI_NUME_SAL) as  RAD_FIN 
					  , $sqlFecha AS FECHA  
						, r.USUA_DOC
						,rd.TDOC_CODI 
						from sgd_renv_regenvio  r, radicado rd 
			   WHERE r.sgd_renv_planilla = '00' and r.sgd_renv_tipo = 1 and 
				 rd.RADI_NUME_RADI= r.RADI_NUME_GRUPO 
				 and sgd_depe_genera = '$dep_sel'
               group by r.radi_nume_grupo, $sqlFecha,r.usua_doc,rd.TDOC_CODI 
			   order by $order ";
		break;
	}
?>
