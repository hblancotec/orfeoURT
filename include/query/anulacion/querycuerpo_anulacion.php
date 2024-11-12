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
					 	b.radi_nume_radi is not null
						and b.radi_depe_radi = '.$dep_sel.'
						and b.radi_tiporad in (1,3,4,5,6,7,8,9)
						--Adicionado el 9 (25/01/2008) y el 6 (12/03/2008) por Eduardo Pires

						and b.tdoc_codi=c.sgd_tpr_codigo
						and sgd_eanu_codigo is null
						'.$whereTpAnulacion.'
						'.$whereFiltro.'
					  order by '.$order .' ' .$orderTipo;
	break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
		$isql = 'select
								to_char(b.RADI_NUME_RADI) "IMG_Numero Radicado"
								,b.RADI_PATH "HID_RADI_PATH"
								,to_char(b.RADI_NUME_DERI) "Radicado Padre"
								,b.RADI_FECH_RADI "HOR_RAD_FECH_RADI"
								,b.RADI_FECH_RADI "Fecha Radicado"
								,b.RA_ASUN "Descripcion"
								,c.SGD_TPR_DESCRIP "Tipo Documento"
								,b.RADI_NUME_RADI "CHK_CHKANULAR"
						from
						 radicado b,
						 SGD_TPR_TPDCUMENTO c
						where
					 	b.radi_nume_radi is not null
						and '.$db->conn->substr.'(b.radi_nume_radi, 5, 3)='.$dep_sel.'
						and '.$db->conn->substr.'(b.radi_nume_radi, 14, 1) in (1,3,5,6,9)
						and b.tdoc_codi=c.sgd_tpr_codigo
						and sgd_eanu_codigo is null
						and rownum < 200
						'.$whereTpAnulacion.'
						'.$whereFiltro.'
					  order by '.$order .' ' .$orderTipo;
			break;
	}
?>