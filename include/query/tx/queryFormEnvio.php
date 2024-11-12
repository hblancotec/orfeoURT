<?php
switch ($tmp_arr_id){
	case 1:
		$tmp_where = " and (i.info_codi is null) ";break;
	default:
		$tmp_where = " ";break;
}

//($tmp_arr_id)? $tmp_where = " " : $tmp_where = " and i.info_codi is null ";
switch($db->driver){
	case 'mssqlnative':{
		switch ($tmp_arr_id){
			case 0:	{
				$isql ='SELECT	convert(char(15), b.RADI_NUME_RADI) as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,convert(char(15),b.RADI_NUME_RADI) as "HID_Numero Radicado"
								,b.RA_ASUN  as "Descripcion"
								,b.RADI_USU_ANTE "Enviado Por"
								,convert(char(15), b.RADI_NUME_RADI) "CHK_CHKANULAR"
						FROM	radicado b
		 				WHERE	b.RADI_NUME_RADI is not null
								'.$whereFiltro.'
								'.$whereCarpeta.'
	  					ORDER BY '.$order.' '.$orderTipo;
				
				$asql ='SELECT 	convert(char(15), b.RADI_NUME_RADI) as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,convert(char(15),b.RADI_NUME_RADI) as "HID_Numero Radicado"
								,b.RA_ASUN  as "Descripcion"
								,b.RADI_USU_ANTE "Enviado Por"
								,convert(char(15), b.RADI_NUME_RADI) "CHK_CHKANULAR"
						FROM	radicado b
		 				WHERE	b.RADI_NUME_RADI is not null
								'.$whereAnexos.'
								'.$whereCarpeta.'
	  					ORDER BY '.$order.' '.$orderTipo;
					
				}
			break;
			case 1:	{
				$radi_nume_radi = "convert(varchar(15),i.RADI_NUME_RADI)";
				$tmp_cad1 = "convert(varchar,".$db->conn->concat("'0'","'-'",$radi_nume_radi).")";
				$tmp_cad2 = "convert(varchar,".$db->conn->concat('i.info_codi',"'-'",$radi_nume_radi).")";
				$isql = 'select	convert(char(15), b.RADI_NUME_RADI) as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,convert(char(15),b.RADI_NUME_RADI) as "HID_Numero Radicado"
								,i.info_codi as "HID_InfoCodi"
								,i.info_desc  as "Descripcion"
								,'.chr(39).chr(39).' "Informado Por"
								,'.$tmp_cad1.' "CHK_CHKANULAR"
						 from	informados i, radicado b
						 where	i.radi_nume_radi = b.radi_nume_radi and
						  		i.RADI_NUME_RADI is not null
						  		AND (i.USUA_CODI = '.$_SESSION['codusuario'].') AND (i.DEPE_CODI = '.$_SESSION['dependencia'].')
								'.$whereFiltro.$tmp_where.'
								'.$whereCarpeta.'
					  	order by '.$order.' '.$orderTipo;
				
				$asql = 'select	convert(char(15), b.RADI_NUME_RADI) as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,convert(char(15),b.RADI_NUME_RADI) as "HID_Numero Radicado"
								,i.info_codi as "HID_InfoCodi"
								,i.info_desc  as "Descripcion"
								,'.chr(39).chr(39).' "Informado Por"
								,'.$tmp_cad1.' "CHK_CHKANULAR"
						 from	informados i, radicado b
						 where	i.radi_nume_radi = b.radi_nume_radi and
						  		i.RADI_NUME_RADI is not null
						  		AND (i.USUA_CODI = '.$_SESSION['codusuario'].') AND (i.DEPE_CODI = '.$_SESSION['dependencia'].')
								'.$whereAnexos.$tmp_where.'
								'.$whereCarpeta.'
					  	order by '.$order.' '.$orderTipo;
				}
				break;
			default:{
				$radi_nume_radi = "convert(varchar(15),i.RADI_NUME_RADI)";
				$tmp_cad1 = "convert(varchar,".$db->conn->concat("'0'","'-'",$radi_nume_radi).")";
				$tmp_cad2 = "convert(varchar,".$db->conn->concat('i.info_codi',"'-'",$radi_nume_radi).")";
				$isql = 'select	'.$radi_nume_radi.' as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,'.$radi_nume_radi.' as "HID_Numero Radicado"
								,i.info_codi as "HID_InfoCodi"
								,i.info_desc  as "Descripcion"
								,u.USUA_NOMB "Informado Por"
								,'.$tmp_cad2.' "CHK_CHKANULAR"
		 				from	informados i, radicado b,  usuario u
		 				where	i.radi_nume_radi = b.radi_nume_radi and
						 		i.info_codi = u.usua_doc and
						  		i.RADI_NUME_RADI is not null
						  		AND (i.USUA_CODI = '.$_SESSION['codusuario'].') AND (i.DEPE_CODI = '.$_SESSION['dependencia'].')
								'.$whereFiltro.$tmp_where.'
								'.$whereCarpeta.'
	  					order by '.$order.' '.$orderTipo;
				
				$asql = 'select	'.$radi_nume_radi.' as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,'.$radi_nume_radi.' as "HID_Numero Radicado"
								,i.info_codi as "HID_InfoCodi"
								,i.info_desc  as "Descripcion"
								,u.USUA_NOMB "Informado Por"
								,'.$tmp_cad2.' "CHK_CHKANULAR"
		 				from	informados i, radicado b,  usuario u
		 				where	i.radi_nume_radi = b.radi_nume_radi and
						 		i.info_codi = u.usua_doc and
						  		i.RADI_NUME_RADI is not null
						  		AND (i.USUA_CODI = '.$_SESSION['codusuario'].') AND (i.DEPE_CODI = '.$_SESSION['dependencia'].')
								'.$whereAnexos.$tmp_where.'
								'.$whereCarpeta.'
	  					order by '.$order.' '.$orderTipo;
				}
				break;
			}
		}
		break;
	case 'oracle':
	case 'oci8':{
		switch ($tmp_arr_id){
			case 0:	{
				$isql ='select	to_char(b.RADI_NUME_RADI) as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,b.RADI_NUME_RADI as "HID_Numero Radicado"
								,b.RA_ASUN  as "Descripcion"
								,c.SGD_TPR_DESCRIP as "Tipo Documento"
								,b.RADI_USU_ANTE "Enviado Por"
								,to_char(b.RADI_NUME_RADI) "CHK_CHKANULAR"
						from	radicado b,
								SGD_TPR_TPDCUMENTO c
						where	b.radi_nume_radi is not null '.
								' and b.tdoc_codi=c.sgd_tpr_codigo
								'.$whereFiltro.$whereCarpeta.'
						order by '.$order .' ' .$orderTipo;
				}
				break;
			case 1:{
				$radi_nume_radi = "to_char(i.RADI_NUME_RADI)";
				$tmp_cad1 = "to_char(".$db->conn->concat("'0'","'-'",$radi_nume_radi).")";
				$tmp_cad2 = "to_char(".$db->conn->concat('i.info_codi',"'-'",$radi_nume_radi).")";
				$isql ='select	to_char(b.RADI_NUME_RADI) as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,to_char(b.RADI_NUME_RADI) as "HID_Numero Radicado"
								,i.info_codi as "HID_InfoCodi"
								,i.info_desc  as "Descripcion"
								,'.chr(39).chr(39).' "Informado Por"
								,'.$tmp_cad1.' "CHK_CHKANULAR"
						from	informados i, radicado b
						where	i.radi_nume_radi = b.radi_nume_radi and
						  		i.RADI_NUME_RADI is not null
						  		AND (i.USUA_CODI = '.$_SESSION['codusuario'].') AND (i.DEPE_CODI = '.$_SESSION['dependencia'].')
								'.$whereFiltro.$tmp_where.'
								'.$whereCarpeta.'
					  	order by '.$order.' '.$orderTipo;
				}
				break;
			case 2:{
				$radi_nume_radi = "to_char(i.RADI_NUME_RADI)";
				$tmp_cad1 = "to_char(".$db->conn->concat("'0'","'-'",$radi_nume_radi).")";
				$tmp_cad2 = "to_char(".$db->conn->concat('i.info_codi',"'-'",$radi_nume_radi).")";
				$isql ='select	'.$radi_nume_radi.' as "IMG_Numero Radicado"
								,b.RADI_PATH as "HID_RADI_PATH"
								,b.RADI_FECH_RADI as "HOR_RAD_FECH_RADI"
								,'.$sqlFecha.' as "DAT_Fecha Radicado"
								,'.$radi_nume_radi.' as "HID_Numero Radicado"
								,i.info_codi as "HID_InfoCodi"
								,i.info_desc  as "Descripcion"
								,u.USUA_NOMB "Informado Por"
								,'.$tmp_cad2.' "CHK_CHKANULAR"
						from	informados i, radicado b,  usuario u
						where	i.radi_nume_radi = b.radi_nume_radi and
						 		i.info_codi = u.usua_doc and
						  		i.RADI_NUME_RADI is not null
						  		AND (i.USUA_CODI = '.$_SESSION['codusuario'].') AND (i.DEPE_CODI = '.$_SESSION['dependencia'].')
								'.$whereFiltro.$tmp_where.'
								'.$whereCarpeta.'
					  	order by '.$order.' '.$orderTipo;
				}
				break;
			}
		}
}
?>
