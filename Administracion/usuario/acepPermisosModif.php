<?php
$ruta_raiz = "../..";
session_start();
if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad']) include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;
if ($usuLogin) {
       
	$sqlFechaHoy = $db->conn->DBTimeStamp(time());
	$isql = "UPDATE USUARIO SET ";

	if ($prestamo)
		$isql .= " USUA_PERM_PRESTAMO = 1, ";
	else
		$isql .= " USUA_PERM_PRESTAMO = 0, ";

	if ($digitaliza)
		$isql .= " PERM_RADI = 1, ";
	else
		$isql .= " PERM_RADI = 0, ";

	if ($masiva)
		$isql .= " USUA_MASIVA = 1, ";
	else
		$isql .= " USUA_MASIVA = 0, ";

	if ($impresion)
		$isql .= " USUA_PERM_IMPRESION = $impresion, ";
	else
		$isql .= " USUA_PERM_IMPRESION = 0, ";

	if ($exp_temas)
		$isql .= " USUA_PERM_TEM_EXP = $exp_temas, ";
	else
		$isql .= " USUA_PERM_TEM_EXP = 0, ";
	
	if ($ccalarmas)
		$isql .= " USUA_PERM_CC_ALAR = 1, ";
	else
		$isql .= " USUA_PERM_CC_ALAR = 0, ";
	
	if ($permDespla)
		$isql .= " USUA_PERM_DESPLA = 1, ";
	else
		$isql .= " USUA_PERM_DESPLA = 0, ";
	
	if ($repMailCert)
		$isql .= " USUA_REP_MAILCERT = 1, ";
	else
		$isql .= " USUA_REP_MAILCERT = 0, ";
	
	if ($pqrVerbal)
		$isql .= " USUA_PRAD_PQRVERBAL = 1, ";
	else
		$isql .= " USUA_PRAD_PQRVERBAL = 0, ";

	if ($devCorreo)
		$isql .= " USUA_PERM_DEV = 1, ";
	else
		$isql .= " USUA_PERM_DEV = 0, ";
	
	if ($permRecRadEnt)
		$isql .= " USUA_PERM_REC_RADENTRADA = 1, ";
	else
		$isql .= " USUA_PERM_REC_RADENTRADA = 0, ";
	
	if ($no_trd)
		$isql .= " USUA_NO_TIPIFICA = 1, ";
	else
		$isql .= " USUA_NO_TIPIFICA = 0, ";	

	if ($ordena)
		$isql .= " USUA_PERM_ORDENAR = 1, ";
	else
		$isql .= " USUA_PERM_ORDENAR = 0, ";			
	
	if ($_POST['perm_servweb'])
		$isql .= " USUA_ADM_SERVWEB = 1, ";
	else
		$isql .= " USUA_ADM_SERVWEB = 0, ";
	
	if ($notifAdm)
		$isql .= " USUA_NOTIF_ADMIN = 1, ";
	else
		$isql .= " USUA_NOTIF_ADMIN = 0, ";
	
	if (!($s_anulaciones) && !($anulaciones))
		$isql .= " SGD_PANU_CODI = 0, ";
	
	if (($s_anulaciones) && !($anulaciones))
		$isql .= " SGD_PANU_CODI = 1, ";
	
	if (($anulaciones) && !($s_anulaciones))
		$isql .= " SGD_PANU_CODI = 2, ";
	
	if (($s_anulaciones) && ($anulaciones))
		$isql .= " SGD_PANU_CODI = 3, ";

    if ($adm_archivo)
		$isql .= " USUA_ADMIN_ARCHIVO = '1', ";
	else
		$isql .= " USUA_ADMIN_ARCHIVO = '0', ";

	if ($adm_sistema)
		$isql .= " USUA_ADMIN_SISTEMA = '1', ";
	else
		$isql .= " USUA_ADMIN_SISTEMA = '0', ";

	if ($usua_nuevoM)
		$isql .= " USUA_NUEVO = '0', ";
	else
		$isql .= " USUA_NUEVO = '1', ";

	if ($envios && $enviosExt){
		$isql .= " USUA_PERM_ENVIOS = 3, ";
	}
	elseif(!$envios && $enviosExt){
		$isql .= " USUA_PERM_ENVIOS = 2, ";
	}
	elseif($envios && !$enviosExt){
		$isql .= " USUA_PERM_ENVIOS = 1, ";
	}
	else {
		$isql .= " USUA_PERM_ENVIOS = 0, ";
	}	

	if ($estadisticas)
		$isql .= " SGD_PERM_ESTADISTICA = $estadisticas, ";
	else
		$isql .= " SGD_PERM_ESTADISTICA = 0, ";

	if ($firma)
		$isql .= " USUA_PERM_FIRMA = $firma, ";
	else
		$isql .= " USUA_PERM_FIRMA = 0, ";

	if ($reasigna)  {
		$isql .= " USUARIO_REASIGNAR = 1, ";
		}
	else
		$isql .= " USUARIO_REASIGNAR = 0, ";

	if ($usua_publico) {
        $isql .= " USUARIO_PUBLICO = 1, ";
	}
	else
		$isql .= " USUARIO_PUBLICO = 0, ";

	if ($notifReasignacion) {
        $isql .= " USUA_PERMNOTREENVIO = 1, ";
	}
	else
		$isql .= " USUA_PERMNOTREENVIO = 0, ";
	
	
	if ($permBorraAnexos) {
		$isql .= " PERM_BORRAR_ANEXO = 1, ";
	}else
		$isql .= " PERM_BORRAR_ANEXO = 0, ";

	if ($permTipificaAnexos) {
		$isql .= " PERM_TIPIF_ANEXO = 1, ";
	}else
		$isql .= " PERM_TIPIF_ANEXO = 0, ";
    
    if ($repMailNoHabil) {
		$isql .= " USUA_PERM_REENVIO_EMAILNOHABIL = 1, ";
	}else
		$isql .= " USUA_PERM_REENVIO_EMAILNOHABIL = 0, ";

// Inicio permisos masiva
	if ($accMasiva_trd) {	
		$isql .= " USUA_MASIVA_TRD = 1, ";
	}else
		$isql .= " USUA_MASIVA_TRD = 0, ";
				
	if ($accMasiva_incluir) {	
		$isql .= " USUA_MASIVA_INCLUIR = 1, ";
	}else
		$isql .= " USUA_MASIVA_INCLUIR = 0, ";
		
	if ($accMasiva_prestamo) {	
		$isql .= " USUA_MASIVA_PRESTAMO = 1, ";
	}else
		$isql .= " USUA_MASIVA_PRESTAMO = 0, ";
	
	if ($accMasiva_temas) {	
		$isql .= " USUA_MASIVA_TEMAS = 1, ";
	}else
		$isql .= " USUA_MASIVA_TEMAS = 0, ";
// Fin permisos masiva

	if ($autenticaLDAP) {
		$isql .= " USUA_AUTH_LDAP = 1, ";
	}else
		$isql .= " USUA_AUTH_LDAP = 0, ";

	if ($perm_adminflujos) {
		$isql .= " USUA_PERM_ADMINFLUJOS = 1, ";
	}else
		$isql .= " USUA_PERM_ADMINFLUJOS = 0, ";

	if ($alertaDP) {
		$isql .= " USUA_ALERTA_DP = 1, ";
	}else
		$isql .= " USUA_ALERTA_DP = 0, ";

	if ($coinfo) {
		$isql .= " USUA_PERM_COINFO = 1, ";
	}else
		$isql .= " USUA_PERM_COINFO = 0, ";

	if ($medios) {
		$isql .= " USUA_PERM_MEDIO = 1, ";
	}else
		$isql .= " USUA_PERM_MEDIO = 0, ";

	if ($permArchivar) {
		$isql .= " PERM_ARCHI = 1, ";
	}else {
		$isql .= " PERM_ARCHI = 0, ";
    }
		if (empty($_POST['repNotifCorreo'])) {
		    $isql = $isql." USUA_NOTIF_CORREO = 0, ";
		}
		else {
		    $isql = $isql." USUA_NOTIF_CORREO = 1, ";
		}
		
		if (empty($_POST['repVencPrestamo'])) {
		    $isql = $isql." USUA_VENC_PRESTAMO = 0, ";
		}
		else {
		    $isql = $isql." USUA_VENC_PRESTAMO = 1, ";
		}
		if (empty($_POST['modificarTRD'])) {
		    $isql = $isql." USUA_MODIFICA_TRD = 0, ";
		}
		else {
		    $isql = $isql." USUA_MODIFICA_TRD = 1, ";
		}
		if (empty($_POST['retipificarTRD'])) {
		    $isql = $isql." USUA_RETIPIFICA_TRD = 0, ";
		}
		else {
		    $isql = $isql." USUA_RETIPIFICA_TRD = 1, ";
		}
		if (empty($_POST['anexarCorreo'])) {
		    $isql = $isql." USUA_ANEXA_CORREO = 0, ";
		}
		else {
		    $isql = $isql." USUA_ANEXA_CORREO = 1, ";
		}
		if (empty($_POST['modificarTipoDoc'])) {
		    $isql = $isql." USUA_MODIFICA_TIPODOC = 0, ";
		}
		else {
		    $isql = $isql." USUA_MODIFICA_TIPODOC = 1, ";
		}
		
	//  PERMISOS TIPOS DE RADICADOS
	$cad = "perm_tp";
	$sql = "SELECT	SGD_TRAD_CODIGO,
                    SGD_TRAD_DESCR,
                    SGD_TRAD_GENRADSAL
			FROM	SGD_TRAD_TIPORAD
			ORDER BY SGD_TRAD_CODIGO";
	$rs_trad = $db->conn->execute($sql);
	while ($arr = $rs_trad->FetchRow()) {
        $isql .= " USUA_PRAD_TP".$arr['SGD_TRAD_CODIGO']." = ".${$cad.$arr['SGD_TRAD_CODIGO']}.", ";
	}

	if ($cedulafirma) {
	    $isql .= " IDENTIFICACION = '".$cedulafirma."', ";
	}   else {
	    $isql .= " IDENTIFICACION = '', ";
	}
	    
	if ($modificaciones)  {
		$isql .= " USUA_PERM_MODIFICA = 1, ";
	} else
		$isql .= " USUA_PERM_MODIFICA = 0, ";

	if ($notifica)  {
		$isql .= " USUA_PERM_NOTIFICA = 1, ";
	} else
		$isql .= " USUA_PERM_NOTIFICA = 0, ";

	if ($temas)  {
		$isql .= " USUA_PERM_TEMAS = 1, ";
	} 
	else
		$isql .= " USUA_PERM_TEMAS = 0, ";

	if ($respuesta)  {
		$isql .= " USUA_PERM_RESPUESTA = 1, ";
	} 
	else
		$isql .= " USUA_PERM_RESPUESTA = 0, ";

	if ($usua_permexp)
		$isql .= " USUA_PERM_EXPEDIENTE = $usua_permexp, ";
	else
		$isql .= " USUA_PERM_EXPEDIENTE = 0, ";
	
	if ($usua_activo>=1) 
		$isql .= " USUA_ESTA = '".$usua_activo."', ";
	else {
		$isql .= " USUA_ESTA = '0', ";
		
	if ($radicado) {
?>

<html>
	<head> <title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<table align="center" border="2" bordercolor="#000000">
			<form name="frmAbortar" action="../formAdministracion.php" method="post">
				<tr bordercolor="#FFFFFF"> 
					<td width="211" height="30" colspan="2" class="listado2">
						<p> <span class="etexto">
							<center> <B>El usuario <?=$usuLogin?> tiene radicados a su cargo, NO PUEDE INACTIVARSE</B></center>
						</span></p> 
					</td>
				</tr>
				<tr bordercolor="#FFFFFF">	
					<td height="30" colspan="2" class="listado2">
						<center><input class="botones" type="submit" name="Submit" value="Aceptar"></center>
						<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
						<input name="krd" type="hidden" value='<?=$krd?>'>
					</td>
				</tr>
			</form>
		</table>
	</head>
	
<?php
			$swConRadicado = "SI";
			return;
		}
		
		if ($temaUsu) {
		    ?>

        <html>
        	<head> <title></title>
        		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        		<table align="center" border="2" bordercolor="#000000">
        			<form name="frmAbortar" action="../formAdministracion.php" method="post">
        				<tr bordercolor="#FFFFFF"> 
        					<td width="211" height="30" colspan="2" class="listado2">
        						<p> <span class="etexto" style="align-content: center">
        							<B>El usuario <?=$usuLogin?> No permite inactivarse porque esta a cargo del tema:  <?php echo strtoupper($temaUsu) ?> </B>
        						</span></p> 
        					</td>
        				</tr>
        				<tr style="border-color: #FFFFFF; ">	
        					<td height="30" colspan="2" class="listado2">
        						<center><input class="botones" type="submit" name="Submit" value="Aceptar"></center>
        						<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
        						<input name="krd" type="hidden" value='<?=$krd?>'>
        					</td>
        				</tr>
        			</form>
        		</table>
        	</head>
        </html>	
        
        <?php
            $swConRadicado = "SI";
			return;
		}
	}

	if ($tablas)
		$isql .= " USUA_PERM_TRD = '1', ";
	else
		$isql .= " USUA_PERM_TRD = '0', ";

	//Nivel de Seguridad
	if (!$nivel) $nivel = 1;
	$isql .= " CODI_NIVEL = $nivel ";
	$isql .= " WHERE USUA_LOGIN = '$usuLogin'";
	//$isql1 = "select * from USUARIO WHERE USUA_LOGIN = '".$usuLogin."'";
	//$rs1   = $db->conn->Execute($isql1);
	$rs1 = $estadoAnt;
	$rs = $db->conn->Execute($isql);
	$isqldesp = "select * from USUARIO WHERE USUA_LOGIN = '".$usuLogin."'";
	$rs    = $db->conn->Execute($isqldesp);
}
?>

<body style="background-color:#FFFFFF">
	
<?php
if ($db->conn->Execute($isql) == false) {
	echo "Existe un error en los datos diligenciados...";
} 
else {
	if ($rs->fields["USUA_ESTA"]<>$rs1->fields["USUA_ESTA"]) {
		$isql = "INSERT INTO SGD_USH_USUHISTORICO 
						(SGD_USH_ADMCOD,
						SGD_USH_ADMDEP,
						SGD_USH_ADMDOC,
						SGD_USH_USUCOD,
						SGD_USH_USUDEP,
						SGD_USH_USUDOC,
						SGD_USH_MODCOD,
						SGD_USH_FECHEVENTO,
						SGD_USH_USULOGIN)
				VALUES (".$_SESSION['codusuario'].", ".
                        $_SESSION['dependencia'].", '".
                        $_SESSION['usua_doc']."',
						".$rs1->fields["USUA_CODI"].",
						".$rs1->fields["DEPE_CODI"].",
						'$cedula',
						9,
						$sqlFechaHoy,
						'$usuLogin')";
		$db->conn->Execute($isql);
	}

	if ($rs->fields["USUA_PRAD_TP1"]<>$rs1->fields["USUA_PRAD_TP1"]) {
		if ($rs->fields["USUA_PRAD_TP1"]== 1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO 
							(SGD_USH_ADMCOD,
							SGD_USH_ADMDEP,
							SGD_USH_ADMDOC,
							SGD_USH_USUCOD,
							SGD_USH_USUDEP,
							SGD_USH_USUDOC,
							SGD_USH_MODCOD,
                            SGD_USH_FECHEVENTO,
                            SGD_USH_USULOGIN)
					VALUES (".$_SESSION['codusuario'].", ".
                            $_SESSION['dependencia'].", '".
                            $_SESSION['usua_doc']."',
							".$rs1->fields["USUA_CODI"].",
							".$rs1->fields["DEPE_CODI"].",
							'$cedula',
							19,
							".$sqlFechaHoy.",
							'".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PRAD_TP1"]== 2) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '$cedula',
                                                        20,
                                                        $sqlFechaHoy,
                                                        '$usuLogin')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PRAD_TP1"]== 3) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                       SGD_USH_ADMDEP,
                                                       SGD_USH_ADMDOC,
                                                       SGD_USH_USUCOD,
                                                       SGD_USH_USUDEP,
                                                       SGD_USH_USUDOC,
                                                       SGD_USH_MODCOD,
                                                       SGD_USH_FECHEVENTO,
                                                       SGD_USH_USULOGIN)
                                               VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                       ".$rs1->fields["USUA_CODI"].",
                                                       ".$rs1->fields["DEPE_CODI"].",
                                                       '$cedula',
                                                       35,
                                                       $sqlFechaHoy,
                                                       '$usuLogin')";
			$db->conn->Execute($isql);
		}
	}

	if ($rs->fields["USUA_PRAD_TP2"]<>$rs1->fields["USUA_PRAD_TP2"]) {
		if ($rs->fields["USUA_PRAD_TP2"]==0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '$cedula',
                                                        41,
                                                        $sqlFechaHoy,
                                                        '$usuLogin')";
			$db->conn->Execute($isql);
		}
		if ($rs->fields["USUA_PRAD_TP2"]==1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                        VALUES (".$_SESSION['codusuario'].", ".
                                                                $_SESSION['dependencia'].", '".
                                                                $_SESSION['usua_doc']."',
                                                                ".$rs1->fields["USUA_CODI"].",
                                                                ".$rs1->fields["DEPE_CODI"].",
                                                                '$cedula',
                                                                10,
                                                                $sqlFechaHoy,
                                                                '$usuLogin')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PRAD_TP2"]==2) {
				$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                            SGD_USH_ADMDEP,
                                                            SGD_USH_ADMDOC,
                                                            SGD_USH_USUCOD,
                                                            SGD_USH_USUDEP,
                                                            SGD_USH_USUDOC,
                                                            SGD_USH_MODCOD,
                                                            SGD_USH_FECHEVENTO,
                                                            SGD_USH_USULOGIN)
                                                    VALUES (".$_SESSION['codusuario'].", ".
                                                            $_SESSION['dependencia'].", '".
                                                            $_SESSION['usua_doc']."',
                                                            ".$rs1->fields["USUA_CODI"].",
                                                            ".$rs1->fields["DEPE_CODI"].",
                                                            '$cedula',
                                                            11,
                                                            $sqlFechaHoy,
                                                            '$usuLogin')";
				$db->conn->Execute($isql);
	    }
    }

	if ($rs->fields["USUA_PRAD_TP3"]<>$rs1->fields["USUA_PRAD_TP3"]) {
		if ($rs->fields["USUA_PRAD_TP3"]== 0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        28,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PRAD_TP3"]== 1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        29,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUA_PRAD_TP5"]<>$rs1->fields["USUA_PRAD_TP5"]) {
		if ($rs->fields["USUA_PRAD_TP5"]== 0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        30,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PRAD_TP5"]== 1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        31,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUA_PERM_MODIFICA"]<>$rs1->fields["USUA_PERM_MODIFICA"]) {
		if ($rs->fields["USUA_PERM_MODIFICA"]==0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        49,
                                                        $sqlFechaHoy,
                                                        '$usuLogin')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_MODIFICA"]==1) {
				$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                            SGD_USH_ADMDEP,
                                                            SGD_USH_ADMDOC,
                                                            SGD_USH_USUCOD,
                                                            SGD_USH_USUDEP,
                                                            SGD_USH_USUDOC,
                                                            SGD_USH_MODCOD,
                                                            SGD_USH_FECHEVENTO,
                                                            SGD_USH_USULOGIN)
                                                    VALUES (".$_SESSION['codusuario'].", ".
                                                            $_SESSION['dependencia'].", '".
                                                            $_SESSION['usua_doc']."',
                                                            ".$rs1->fields["USUA_CODI"].",
                                                            ".$rs1->fields["DEPE_CODI"].",
                                                            '".$cedula."',
                                                            48,
                                                            ".$sqlFechaHoy.",
                                                            '".$usuLogin."')";
				$db->conn->Execute($isql);
				}
	}

	if ($rs->fields["PERM_RADI"]<>$rs1->fields["PERM_RADI"]) {
		if ($rs->fields["PERM_RADI"]==0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        46,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["PERM_RADI"]==1)
			{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        45,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
			}
	}

	if ($rs->fields["USUA_ADMIN_SISTEMA"]<>$rs1->fields["USUA_ADMIN_SISTEMA"]) {
		if ($rs->fields["USUA_ADMIN_SISTEMA"]==0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        12,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_ADMIN_SISTEMA"]==1)
			{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        13,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
			}
	}

	if ($rs->fields["USUA_PERM_PRESTAMO"]<>$rs1->fields["USUA_PERM_PRESTAMO"]) {
		if ($rs->fields["USUA_PERM_PRESTAMO"]==0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        44,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_PRESTAMO"]==1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        43,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}

	if ($rs->fields["USUA_ADMIN_ARCHIVO"]<>$rs1->fields["USUA_ADMIN_ARCHIVO"])
	{
		if ($rs->fields["USUA_ADMIN_ARCHIVO"]==0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        14,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_ADMIN_ARCHIVO"]==1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        15,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_ADMIN_ARCHIVO"]==2) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        76,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}

	if ($rs->fields["USUA_NUEVO"]<>$rs1->fields["USUA_NUEVO"]) {
		if ($rs->fields["USUA_NUEVO"]==0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        16,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_NUEVO"]==1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        17,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}

	if ($rs->fields["CODI_NIVEL"]<>$rs1->fields["CODI_NIVEL"]) {
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                    SGD_USH_ADMDEP,
                                                    SGD_USH_ADMDOC,
                                                    SGD_USH_USUCOD,
                                                    SGD_USH_USUDEP,
                                                    SGD_USH_USUDOC,
                                                    SGD_USH_MODCOD,
                                                    SGD_USH_FECHEVENTO,
                                                    SGD_USH_USULOGIN)
                                            VALUES (".$_SESSION['codusuario'].", ".
                                                    $_SESSION['dependencia'].", '".
                                                    $_SESSION['usua_doc']."',
                                                    ".$rs1->fields["USUA_CODI"].",
                                                    ".$rs1->fields["DEPE_CODI"].",
                                                    '".$cedula."',
                                                    18,
                                                    ".$sqlFechaHoy.",
                                                    '".$usuLogin."')";
		$db->conn->Execute($isql);
	}

	if ($rs->fields["USUA_MASIVA"]<>$rs1->fields["USUA_MASIVA"]) {
		if ($rs->fields["USUA_MASIVA"]== 0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        22,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_MASIVA"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,
                                                        SGD_USH_ADMDEP,
                                                        SGD_USH_ADMDOC,
                                                        SGD_USH_USUCOD,
                                                        SGD_USH_USUDEP,
                                                        SGD_USH_USUDOC,
                                                        SGD_USH_MODCOD,
                                                        SGD_USH_FECHEVENTO,
                                                        SGD_USH_USULOGIN)
                                                VALUES (".$_SESSION['codusuario'].", ".
                                                        $_SESSION['dependencia'].", '".
                                                        $_SESSION['usua_doc']."',
                                                        ".$rs1->fields["USUA_CODI"].",
                                                        ".$rs1->fields["DEPE_CODI"].",
                                                        '".$cedula."',
                                                        21,
                                                        ".$sqlFechaHoy.",
                                                        '".$usuLogin."')";
			$db->conn->Execute($isql);
			$ret = $db->conn->Replace('CARPETA_PER', 
								array(	'CODI_CARP'=>5,'USUA_CODI'=>$rs1->fields["USUA_CODI"],'DEPE_CODI'=>$rs1->fields["DEPE_CODI"],
										'NOMB_CARP'=>'Masiva', 'DESC_CARP'=>'Radicacion Masiva'), 
								array('CODI_CARP','USUA_CODI','DEPE_CODI'), $autoquote = true);  
	    }
    }

	if ($rs->fields["USUA_PERM_DEV"]<>$rs1->fields["USUA_PERM_DEV"]) {
		if ($rs->fields["USUA_PERM_DEV"]== 0)
		{
		    $isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 23, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_DEV"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 24, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}

	if ($rs->fields["SGD_PANU_CODI"]<>$rs1->fields["SGD_PANU_CODI"])
	{
		if ($rs->fields["SGD_PANU_CODI"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 25, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["SGD_PANU_CODI"]== 2)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 26, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["SGD_PANU_CODI"]== 3)
			{
				$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 27, ".$sqlFechaHoy.", '".$usuLogin."')";
				$db->conn->Execute($isql);
	}		}

	if ($rs->fields["USUA_PERM_IMPRESION"]<>$rs1->fields["USUA_PERM_IMPRESION"])
	{
		if ($rs->fields["USUA_PERM_IMPRESION"]== 0)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 47, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_IMPRESION"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 20, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_IMPRESION"]== 2)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 64, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUA_PERM_ENVIOS"]<>$rs1->fields["USUA_PERM_ENVIOS"])
	{
		if ($rs->fields["USUA_PERM_ENVIOS"]== 0)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 33, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_ENVIOS"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 34, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["SGD_PERM_ESTADISTICA"]<>$rs1->fields["SGD_PERM_ESTADISTICA"])
	{
		if ($rs->fields["SGD_PERM_ESTADISTICA"]== 0)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 53, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["SGD_PERM_ESTADISTICA"]== 1) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 54, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["SGD_PERM_ESTADISTICA"]== 2) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 63, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUA_PERM_EXPEDIENTE"]<>$rs1->fields["USUA_PERM_EXPEDIENTE"]) {
		if ($rs->fields["USUA_PERM_EXPEDIENTE"]== 0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 71, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_EXPEDIENTE"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 70, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_EXPEDIENTE"]== 2) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 75, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUA_PERM_FIRMA"]<>$rs1->fields["USUA_PERM_FIRMA"]) {
		if ($rs->fields["USUA_PERM_FIRMA"]== 0) {
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 59, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_FIRMA"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 60, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_FIRMA"]== 2)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 61, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_FIRMA"]== 3)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 62, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUA_PERM_TRD"]<>$rs1->fields["USUA_PERM_TRD"])
	{
		if ($rs->fields["USUA_PERM_TRD"]== 0)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 52, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_TRD"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 51, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUARIO_PUBLICO"]<>$rs1->fields["USUARIO_PUBLICO"])
	{
		if ($rs->fields["USUARIO_PUBLICO"]== 0)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 56, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUARIO_PUBLICO"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 55, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
	}	}

	if ($rs->fields["USUARIO_REASIGNAR"]<>$rs1->fields["USUARIO_REASIGNAR"])
	{
		if ($rs->fields["USUARIO_REASIGNAR"]== 0)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 58, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUARIO_REASIGNAR"]== 1)
		{
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN)  VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].", '".$cedula."', 57, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}
	
	### PERMISO PARA ORDENAR ANEXOS
	if ($rs->fields["USUA_PERM_ORDENAR"]<> $rs1->fields["USUA_PERM_ORDENAR"])
	{
		if ($rs->fields["USUA_PERM_ORDENAR"]== 0)
		{
			$isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)  
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].", ".$rs1->fields["DEPE_CODI"].", '".$cedula."', 81, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_PERM_ORDENAR"]== 1) {
			$isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)  
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].", ".$rs1->fields["DEPE_CODI"].", '".$cedula."', 82, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}
    
	### PERMISO PARA ADMINISTRADOR DE APLICACIONES
	if ($rs->fields["USUA_ADM_SERVWEB"]<>$rs1->fields["USUA_ADM_SERVWEB"]) {
		if ($rs->fields["USUA_ADM_SERVWEB"]== 0) {
			$isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)  
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."',".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].",'".$cedula."',84,".$db->conn->sysTimeStamp.",'".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_ADM_SERVWEB"]== 1)
		{
			$isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)  
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."',".$rs1->fields["USUA_CODI"].",".$rs1->fields["DEPE_CODI"].",'".$cedula."',83,".$db->conn->sysTimeStamp.",'".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}
	
	### PERMISO PARA NOFICACIONES ADMINISTRATIVAS
	if ($rs->fields["USUA_NOTIF_ADMIN"]<> $rs1->fields["USUA_NOTIF_ADMIN"])
	{
		if ($rs->fields["USUA_NOTIF_ADMIN"]== 0)
		{
			$isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)  
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].", ".$rs1->fields["DEPE_CODI"].", '".$cedula."', 86, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		elseif ($rs->fields["USUA_NOTIF_ADMIN"]== 1) {
			$isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)  
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].", ".$rs1->fields["DEPE_CODI"].", '".$cedula."', 85, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
	}
	### PERMISO PARA RECIBIR NOTIFICACION AL INCLUIR RADICADO EN EXPEDIENTE (CUANDO YA ESTA EN OTRO EXPEDIEENTE)
	if ($rs->fields["USUA_NOTIF_RADEXP"]<> $rs1->fields["USUA_NOTIF_RADEXP"])
	{
	    if ($rs->fields["USUA_NOTIF_RADEXP"]== 0)
	    {
	        $isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].", ".$rs1->fields["DEPE_CODI"].", '".$cedula."', 88, ".$sqlFechaHoy.", '".$usuLogin."')";
	        $db->conn->Execute($isql);
	    }
	    elseif ($rs->fields["USUA_NOTIF_RADEXP"]== 1) {
	        $isql ="INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD,SGD_USH_ADMDEP,SGD_USH_ADMDOC,SGD_USH_USUCOD,SGD_USH_USUDEP,SGD_USH_USUDOC,SGD_USH_MODCOD,SGD_USH_FECHEVENTO,SGD_USH_USULOGIN)
					VALUES (".$_SESSION['codusuario'].", ".$_SESSION['dependencia'].", '".$_SESSION['usua_doc']."', ".$rs1->fields["USUA_CODI"].", ".$rs1->fields["DEPE_CODI"].", '".$cedula."', 87, ".$sqlFechaHoy.", '".$usuLogin."')";
	        $db->conn->Execute($isql);
	    }
	}
}
?>
</body>
</html>
