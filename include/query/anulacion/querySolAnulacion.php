<?php
	/**
	  * CONSULTA VERIFICACION PREVIA A LA RADICACION
	  */
	switch($db->driver)
	{  
	 case 'mssqlnative':
		$isql = 'select
								b.RADI_NUME_RADI "IMG_Numero Radicado"
								,b.RADI_PATH "HID_RADI_PATH"
								,b.RADI_NUME_DERI "Radicado Padre"
								,b.RADI_FECH_RADI "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' "Fecha Radicado"
								,b.RA_ASUN "Descripcion"
								,c.SGD_TPR_DESCRIP "Tipo Documento"
								,b.RADI_NUME_RADI "CHK_CHKANULAR"
						 from
						 radicado b,
						 SGD_TPR_TPDCUMENTO c
					 where 
						b.tdoc_codi=c.sgd_tpr_codigo
						'.$whereFiltro.'
						order by '.$order .' ' .$orderTipo;
	break;		
	case 'oracle':
	case 'oci8':
	case 'oci805':	
		$isql = 'select
								b.RADI_NUME_RADI "IMG_Numero Radicado"
								,b.RADI_PATH "HID_RADI_PATH"
								,b.RADI_NUME_DERI "Radicado Padre"
								,b.RADI_FECH_RADI "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' "Fecha Radicado"
								,b.RA_ASUN "Descripcion"
								,c.SGD_TPR_DESCRIP "Tipo Documento"
								,b.RADI_NUME_RADI "CHK_CHKANULAR"
						 from
						 radicado b,
						 SGD_TPR_TPDCUMENTO c
					 where 
						b.tdoc_codi=c.sgd_tpr_codigo
						'.$whereFiltro.'
						order by '.$order .' ' .$orderTipo;
			break;		
	}
?>