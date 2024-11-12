<?php
	$isql ="SELECT	U.*,
					D.DEPE_NOMB
			FROM	USUARIO U
					JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI
			WHERE	USUA_LOGIN = '$usuLoginSel'";
	$rsUsu = $db->conn->Execute($isql);
	
	if ($rsUsu){
		
		###	PERMISOS PARA LA PESTAÑA DE INFORMACIÓN GENERAL
		##	SE VERIFICA SI EL USUARIO ES JEFE(2), NORMAL(1) O AUDITOR(3)
		if ($rsUsu->fields['SGD_ROL_CODIGO'] == 1) 
			$perfil = 'Jefe';
		elseif ($rsUsu->fields['SGD_ROL_CODIGO'] == 3)
			$perfil = 'Auditor';
		else
			$perfil = 'Normal';
		
		
		$depeNomb	= $rsUsu->fields['DEPE_NOMB'];
		$dep_sel	= $rsUsu->fields['DEPE_CODI'];
		$usuaCodi	= $rsUsu->fields['USUA_CODI'];
		$cedula		= $rsUsu->fields['USUA_DOC'];
		$nombre		= $rsUsu->fields['USUA_NOMB'];
		$usuLogin	= $usuLoginSel;
		$usua_activo= $rsUsu->fields['USUA_ESTA'];
		$nivel		= $rsUsu->fields['CODI_NIVEL'];
		$email		= trim($rsUsu->fields['USUA_EMAIL']);
		$email1		= trim($rsUsu->fields['USUA_EMAIL_1']);
		$email2		= trim($rsUsu->fields['USUA_EMAIL_2']);
		$piso		= $rsUsu->fields['USUA_PISO'];
		$extension	= $rsUsu->fields['USUA_EXT'];
		$usua_publico = $rsUsu->fields['USUARIO_PUBLICO'];
		$notifReasignacion = $rsUsu->fields['USUA_PERMNOTREENVIO'];
		
		if ($rsUsu->fields['USUA_NUEVO'] == 0)
			$usua_nuevoM = 'Nuevo';
		
		
		###	PERMISOS PARA LA PESTAÑA DE TIPOS DE RADICADOS
		$cad = "perm_tp";
		$sql = "SELECT SGD_TRAD_CODIGO,SGD_TRAD_DESCR FROM SGD_TRAD_TIPORAD ORDER BY SGD_TRAD_CODIGO";
		$rs_trad = $db->conn->Execute($sql);
		while ($arr = $rs_trad->FetchRow()) {
			if ($rsUsu->fields["USUA_PRAD_TP".$arr['SGD_TRAD_CODIGO']] >= 0) {
				${$cad.$arr['SGD_TRAD_CODIGO']} = $rsUsu->fields["USUA_PRAD_TP".$arr['SGD_TRAD_CODIGO']];
			}
			else {
				${$cad.$arr['SGD_TRAD_CODIGO']} = 0;
			}
		}
		
		
		###	PERMISOS PARA LA PESTAÑA DE ADMINISTRACIÓN
		$usua_permexp	= $rsUsu->fields["USUA_PERM_EXPEDIENTE"];
		$tablas			= $rsUsu->fields["USUA_PERM_TRD"];
		$prestamo		= $rsUsu->fields["USUA_PERM_PRESTAMO"];
		$adm_sistema 	= $rsUsu->fields["USUA_ADMIN_SISTEMA"];
		$env_correo 	= $rsUsu->fields["USUA_PERM_ENVIOS"];
		$perm_servweb   = $rsUsu->fields["USUA_ADM_SERVWEB"];
		if ($rsUsu->fields["SGD_PANU_CODI"] == 1) $s_anulaciones = 1;
		if ($rsUsu->fields["SGD_PANU_CODI"] == 2) $anulaciones = 1;
		if ($rsUsu->fields["SGD_PANU_CODI"] == 3) {$s_anulaciones = 1; $anulaciones = 1;}
		$perm_adminflujos = $rsUsu->fields["USUA_PERM_ADMINFLUJOS"];
		$notifAdm		= $rsUsu->fields["USUA_NOTIF_ADMIN"];
		$adm_archivo 	= $rsUsu->fields["USUA_ADMIN_ARCHIVO"];
		
		
		###	PERMISOS PARA LA PESTAÑA VARIOS
		$accMasiva_trd 		= $rsUsu->fields["USUA_MASIVA_TRD"];
		$accMasiva_incluir 	= $rsUsu->fields["USUA_MASIVA_INCLUIR"];
		$accMasiva_prestamo	= $rsUsu->fields["USUA_MASIVA_PRESTAMO"];
		$accMasiva_temas	= $rsUsu->fields["USUA_MASIVA_TEMAS"];
		$permTipificaAnexos = $rsUsu->fields["PERM_TIPIF_ANEXO"];
		$permBorraAnexos = $rsUsu->fields["PERM_BORRAR_ANEXO"];
		$digitaliza		= $rsUsu->fields["PERM_RADI"];
		$modificaciones	= $rsUsu->fields["USUA_PERM_MODIFICA"];
		$masiva 		= $rsUsu->fields["USUA_MASIVA"];
		$impresion 		= $rsUsu->fields["USUA_PERM_IMPRESION"];
		$exp_temas	    = $rsUsu->fields["USUA_PERM_TEM_EXP"];
		$reasigna       = $rsUsu->fields["USUARIO_REASIGNAR"];
		$permArchivar 	= $rsUsu->fields["PERM_ARCHI"];
		$firma          = $rsUsu->fields["USUA_PERM_FIRMA"];
		$autenticaLDAP	= $rsUsu->fields["USUA_AUTH_LDAP"];
		$estadisticas 	= $rsUsu->fields["SGD_PERM_ESTADISTICA"];
		$notifica       = $rsUsu->fields["USUA_PERM_NOTIFICA"];
		$temas          = $rsUsu->fields["USUA_PERM_TEMAS"];
		$respuesta      = $rsUsu->fields["USUA_PERM_RESPUESTA"];
		$medios			= $rsUsu->fields["USUA_PERM_MEDIO"];
		$ccalarmas		= $rsUsu->fields["USUA_PERM_CC_ALAR"];
		$alertaDP		= $rsUsu->fields["USUA_ALERTA_DP"];
		$permRadMail 	= $rsUsu->fields["USUA_PERM_RADEMAIL"];
		$no_trd			= $rsUsu->fields["USUA_NO_TIPIFICA"];
		$ordena			= $rsUsu->fields["USUA_PERM_ORDENAR"];
		$permDespla		= $rsUsu->fields["USUA_PERM_DESPLA"];
		$dev_correo 	= $rsUsu->fields["USUA_PERM_DEV"];
		$coinfo			= $rsUsu->fields["USUA_PERM_COINFO"];
		$repMailCert	= $rsUsu->fields["USUA_REP_MAILCERT"];
		$repMailNoHabil	= $rsUsu->fields["USUA_PERM_REENVIO_EMAILNOHABIL"];
		$pqrVerbal		= $rsUsu->fields["USUA_PRAD_PQRVERBAL"];
		$permRecRadEnt	= $rsUsu->fields["USUA_PERM_REC_RADENTRADA"];
		$devCorreo		= $rsUsu->fields["USUA_PERM_DEV"];
		$repMailRadExp	= $rsUsu->fields["USUA_NOTIF_RADEXP"];
		$repNotifCorreo	= $rsUsu->fields["USUA_NOTIF_CORREO"];
		$repVencPrestamo = $rsUsu->fields["USUA_VENC_PRESTAMO"];
		$modificarTRD = $rsUsu->fields["USUA_MODIFICA_TRD"];
		$retipificarTRD = $rsUsu->fields["USUA_RETIPIFICA_TRD"];
		$anexarCorreo = $rsUsu->fields["USUA_ANEXA_CORREO"];
		$modificarTipoDoc = $rsUsu->fields["USUA_MODIFICA_TIPODOC"];
		$cedulafirma = $rsUsu->fields["IDENTIFICACION"];
		
	}
?>