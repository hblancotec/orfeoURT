<?php
	switch($db->driver)
	{
	case 'mssqlnative':
		$isqlT = 'SELECT convert(varchar(15),r.radi_nume_radi) as "IMG_Radicado" 
				,r.RADI_PATH as "HID_RADI_PATH"
				,'.$sqlFecha.' as "DAT_Fecha_Radicado"
				, convert(varchar(15),r.RADI_NUME_RADI) as "HID_Numero Radicado",
				R.RADI_NOMB as "Nombre",
				R.RADI_PRIM_APEL as "Apellido 1",
				R.RADI_SEGU_APEL as "Apellido 2",
    			R.RADI_NUME_IDEN as "Identificacion", 
    			R.RA_ASUN as "HID_ASUN",
    			R.RADI_REM as "HID_R_RADI_REM",
    			R.TDOC_CODI as "HID_R_TDOC_CODI"
    			from RADICADO R INNER JOIN USUARIO U ON r.radi_usua_actu = u.usua_codi and r.radi_depe_actu = u.depe_codi ';
		$consultadoble = " and u.usua_login='".$usuario."'" ;	
  		$isqlT1 = 'select distinct R.RADI_DEPE_ACTU as "HID_R_RADI_DEPE_ACTU" 
        				, r.radi_nume_radi as "IMG_Radicado" 
    					, r.RADI_PATH as "HID_RADI_PATH"
    					, r.RADI_FECH_RADI as "DAT_Fecha_Radicado"
    					, r.RADI_NUME_RADI as "HID_Numero Radicado"
    					, D.DEPE_NOMB as "R_RADI_DEPE_ACTU",
    					R.RADI_NOMB as "Nombre",
    					R.RADI_PRIM_APEL as "Apellido 1",
    					R.RADI_SEGU_APEL as "Apellido 2",
        				R.RADI_NUME_IDEN as "Identificacion"
    				from RADICADO R INNER JOIN HIST_EVENTOS H ON R.RADI_NUME_RADI=H.RADI_NUME_RADI
	                   INNER JOIN USUARIO U ON H.USUA_CODI = U.USUA_CODI and H.DEPE_CODI = U.DEPE_CODI
                       INNER JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI ';			
	$consultadoble1 = " and U.USUA_LOGIN='$usuario' " ;					
	break;
	case 'oracle':
	case 'oci8':
		$isqlT = 'SELECT to_char(r.radi_nume_radi) as "IMG_Radicado" 
				,r.RADI_PATH as "HID_RADI_PATH"
				,'.$sqlFecha.' as "DAT_Fecha_Radicado"
				, to_char(r.RADI_NUME_RADI) as "HID_Numero Radicado",
				R.RADI_NOMB as "Nombre",
				R.RADI_PRIM_APEL as "Apellido 1",
				R.RADI_SEGU_APEL as "Apellido 2",
    			R.RADI_NUME_IDEN as "Identificacion", 
    			R.RA_ASUN as "HID_ASUN",
    			R.RADI_REM as "HID_R_RADI_REM",
    			to_char(R.TDOC_CODI) as "HID_R_TDOC_CODI"
    			from RADICADO R ';
			$consultadoble = " and ((r.radi_usua_actu,r.radi_depe_actu)=(select u.usua_codi, u.depe_codi from usuario u where u.usua_login='".$usuario."'))" ;
  			$isqlT1 = 'select UNIQUE to_char(R.RADI_DEPE_ACTU) as "HID_R_RADI_DEPE_ACTU" 
    				, to_char(r.radi_nume_radi) as "IMG_Radicado" 
					, r.RADI_PATH as "HID_RADI_PATH"
					,'.$sqlFecha.' as "DAT_Fecha_Radicado"
					, to_char(r.RADI_NUME_RADI) as "HID_Numero Radicado"
					, to_char(H.RADI_NUME_RADI) as "HID_H_RADI_NUME_RADI", 
					R.RADI_NOMB as "Nombre",
					to_char(R.RADI_DEPE_ACTU) as "R_RADI_DEPE_ACTU",
					R.RADI_PRIM_APEL as "Apellido 1",
					R.RADI_SEGU_APEL as "Apellido 2",
    				R.RADI_NUME_IDEN as "Identificacion", 
    				R.RA_ASUN as "HID_ASUN",
    				R.RADI_REM as "HID_R_RADI_REM",
    				to_char(R.TDOC_CODI) as "HID_R_TDOC_CODI"
    				from RADICADO R, HIST_EVENTOS H ';			
			$consultadoble1 = "	((H.USUA_CODI,H.DEPE_CODI)=(SELECT U.USUA_CODI, U.DEPE_CODI FROM USUARIO U WHERE U.USUA_LOGIN='$usuario') AND R.RADI_NUME_RADI=H.RADI_NUME_RADI)" ;
	break;
	}
?>
