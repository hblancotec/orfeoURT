<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../../sinacceso.php");
    exit;
}
else {
    extract($_POST);
    extract($_SESSION);
}

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz = "../..";

if(!$_SESSION['dependencia'])
	include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";

$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$sqlFechaHoy=$db->conn->DBTimeStamp(time());
//$db->conn->debug = true;
$estadoAnt = 0;
?>

<html>
	<head>
		<title>Untitled Document</title>
		<link rel="stylesheet" href="../../estilos/orfeo.css">
	</head>
	<body>

<?php
$usuLogin = strtoupper(trim($usuLogin));
if ($usModo == 2) {
	$isql ="SELECT	USUA_DOC,
					USUA_NOMB,
					DEPE_CODI,
					USUA_LOGIN,
					USUA_NACIM,
					USUA_AT,
					USUA_PISO,
					USUA_EXT,
					USUA_EMAIL,
					USUA_EMAIL_1,
					USUA_EMAIL_2,
					USUA_CODI,
					SGD_ROL_CODIGO,
					USUA_PERMNOTREENVIO
			FROM	USUARIO 
			WHERE	USUARIO.USUA_LOGIN = '" .$usuLogin ."'";
	$rs = $db->conn->Execute($isql);
	
	$isqlRadic = "	SELECT	RADI_NUME_RADI AS RADICADO
					FROM	RADICADO
					WHERE	RADI_DEPE_ACTU = " . $rs->fields["DEPE_CODI"]. " AND
							RADI_USUA_ACTU = " . $rs->fields["USUA_CODI"]. "
					UNION
					SELECT	RADI_NUME_RADI AS RADICADO
					FROM	SGD_RG_MULTIPLE
					WHERE	AREA = ".$rs->fields["DEPE_CODI"]." AND
							USUARIO = ". $rs->fields["USUA_CODI"]." AND
							ESTATUS = 'ACTIVO'";
	$rsRadic = $db->conn->Execute($isqlRadic);	
	$radicado = $rsRadic->fields["RADICADO"];
	
	$isqlTemas = "	SELECT	C.SGD_DCAU_DESCRIP AS TEMA
					FROM	SGD_PQR_TEMAUSU T INNER JOIN SGD_DCAU_CAUSAL C ON T.SGD_DCAU_CODIGO = C.SGD_DCAU_CODIGO 
					WHERE	DEPE_CODI = " . $rs->fields["DEPE_CODI"]. " AND
							USUA_CODI = " . $rs->fields["USUA_CODI"]. "";
	$rsTemas = $db->conn->Execute($isqlTemas);
	$temaUsu = $rsTemas->fields["TEMA"];
	
	if($perfilOrig != $perfil) {
		
		if($perfilOrig == "Jefe" && $perfil != "Jefe") {
			$codTransaccion = 77;
			$nusua_codi = $rs->fields["USUA_CODI"];
			$sgdRolCodigo = 0;
		}
		
		if($perfilOrig != "Jefe" && $perfil == "Jefe"){
			$codTransaccion = 50;
			$nusua_codi = $rs->fields["USUA_CODI"];
			$sgdRolCodigo = 1;
		}
		
		if($perfilOrig == "Auditor" && $perfil != "Auditor"){
			$codTransaccion = 3;
			$nusua_codi = $rs->fields["USUA_CODI"];
			$sgdRolCodigo = 0;
		}
		
		if($perfilOrig != "Auditor" && $perfil == "Auditor"){
			$codTransaccion = 2;
			$nusua_codi = $rs->fields["USUA_CODI"];
			$sgdRolCodigo = 3;
		}
		
		
		$isql1 = $isql1." DEPE_CODI = ".$dep_sel.", ";
		
		$isql1 = $isql1." USUA_CODI = ".$nusua_codi.", SGD_ROL_CODIGO=".$sgdRolCodigo.",";
		
		$isql ="INSERT	INTO SGD_USH_USUHISTORICO 
						(SGD_USH_ADMCOD,
						SGD_USH_ADMDEP,
						SGD_USH_ADMDOC,
						SGD_USH_USUCOD,
						SGD_USH_USUDEP,
						SGD_USH_USUDOC,
						SGD_USH_MODCOD,
						SGD_USH_FECHEVENTO,
						SGD_USH_USULOGIN) 
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].",
						$dep_sel,
						'".$cedula."',
						$codTransaccion,
						".$sqlFechaHoy.",
						'".$usuLogin."')";
		$db->conn->Execute($isql);
		
	}

	
	### SE VERIFICA SI ES PERFIL AUDITOR PARA ASIGNAR PERMISOS SOBRE ANOS Y DEPENDENCIAS
	if ($perfil == 'Auditor'){
		
		$db->conn->Execute("DELETE FROM SGD_USUAROL_ANO WHERE SGD_URA_LOGIN = '" . $usuLogin . "'");
		if (is_array($_REQUEST['Slc_ano'])) {
    		foreach ($_REQUEST['Slc_ano'] as $valor)
    		{
    			$sqlIns1 = "INSERT INTO SGD_USUAROL_ANO (SGD_URA_LOGIN, SGD_URA_DOC, SGD_URA_ANO)
    						VALUES ('$usuLogin', '$cedula', $valor)";
    			$rs1 = $db->conn->Execute($sqlIns1);
    		}
		}

		$db->conn->Execute("DELETE FROM SGD_USUAROL_DEPENDENCIA WHERE SGD_URD_LOGIN = '" . $usuLogin . "'");
		if (is_array($_REQUEST['Slc_deps'])) {
    		foreach ($_REQUEST['Slc_deps'] as $val)
    		{
    			$sqlIns2 = "INSERT INTO SGD_USUAROL_DEPENDENCIA (SGD_URD_LOGIN, SGD_URD_DOC, SGD_URD_DEPE_CODI)
    						VALUES ('$usuLogin', '$cedula', $val)";
    			$rs2 = $db->conn->Execute($sqlIns2);
    		}
		}
	}
	
	
	
	if($rs->fields["USUA_NOMB"] <> $nombre)
	{	$isql1 = $isql1." USUA_NOMB = '".$nombre."', ";
		$isql = "INSERT	INTO SGD_USH_USUHISTORICO 
						(SGD_USH_ADMCOD,
						SGD_USH_ADMDEP,
						SGD_USH_ADMDOC,
						SGD_USH_USUCOD,
						SGD_USH_USUDEP,
						SGD_USH_USUDOC,
						SGD_USH_MODCOD,
						SGD_USH_FECHEVENTO, 
						SGD_USH_USULOGIN) 
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].", 
						$dep_sel,
						'".$cedula."', 
						5,
						".$sqlFechaHoy.", 
						'".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	
	
	if($rs->fields["DEPE_CODI"] <> $dep_sel) {
	    if (!$radicado && !$temaUsu)	{
			$isqlCod ="	SELECT	MAX(USUA_CODI) AS NUMERO 
						FROM	USUARIO 
						WHERE 	DEPE_CODI = ".$dep_sel;
			$rs7= $db->conn->Execute($isqlCod);
			$nusua_codi = $rs7->fields["NUMERO"] + 1;
			$isql1 = $isql1." DEPE_CODI = ".$dep_sel.", ";
			$isql1 = $isql1." USUA_CODI = ".$nusua_codi.", ";
			$isql ="INSERT	INTO SGD_USH_USUHISTORICO 
							(SGD_USH_ADMCOD, 
							SGD_USH_ADMDEP, 
							SGD_USH_ADMDOC, 
							SGD_USH_USUCOD, 
							SGD_USH_USUDEP, 
							SGD_USH_USUDOC, 
							SGD_USH_MODCOD, 
							SGD_USH_FECHEVENTO, 
							SGD_USH_USULOGIN) 
					VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
							".$rs->fields["USUA_CODI"].",
							$dep_sel,
							'".$cedula."', 
							8,
							".$sqlFechaHoy.",
							'".$usuLogin."')";
			$db->conn->Execute($isql);
            $sqlUsuaDepe = "UPDATE	SGD_USD_USUADEPE
							SET		DEPE_CODI = $dep_sel
							WHERE	USUA_LOGIN = '".$usuLogin."'";
			$db->conn->Execute($sqlUsuaDepe);
			
			$sqlUsuaRad = "UPDATE	RADICADO
							SET		RADI_DEPE_RADI = $dep_sel, RADI_USUA_RADI = $nusua_codi 
							WHERE	RADI_DEPE_RADI = ".$rs->fields["DEPE_CODI"]." AND RADI_USUA_RADI = ".$rs->fields["USUA_CODI"]."";
			$db->conn->Execute($sqlUsuaRad);
		}
		else {
		    if ($radicado) {
?>
        		<table align="center" border="2" bordercolor="#000000">
        			<form name="frmAbortar" action="../formAdministracion.php" method="post">
        				<tr bordercolor="#FFFFFF">
        					<td width="211" height="30" colspan="2" class="listado2">
        						<p> <span class=etexto>
        							<center>
        								<B> El usuario <?=$usuLogin?> tiene radicados a su cargo, NO PUEDE CAMBIAR DE DEPENDENCIA </B>
        							</center>
        						</span> </p>
        					</td>
        				</tr>
        				<tr bordercolor="#FFFFFF">
        					<td height="30" colspan="2" class="listado2">
        						<center> <input class="botones" type="submit" name="Submit" value="Aceptar"> </center>
        						<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
        						<input name="krd" type="hidden" value='<?=$krd?>'>
        				   </td>
        				</tr>
        			</form>
        		</table>
<?php
		    }
		    
		    if ($temaUsu) {
		        
		        ?>
        		<table align="center" border="2" bordercolor="#000000">
        			<form name="frmAbortar" action="../formAdministracion.php" method="post">
        				<tr bordercolor="#FFFFFF">
        					<td width="211" height="30" colspan="2" class="listado2">
        						<p> <span class=etexto>
        							<center>
        								<B> El usuario <?=$usuLogin?> tiene a su cargo el tema: <?php echo strtoupper($temaUsu) ?>, NO PUEDE CAMBIAR DE DEPENDENCIA </B>
        							</center>
        						</span> </p>
        					</td>
        				</tr>
        				<tr bordercolor="#FFFFFF">
        					<td height="30" colspan="2" class="listado2">
        						<center> <input class="botones" type="submit" name="Submit" value="Aceptar"> </center>
        						<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
        						<input name="krd" type="hidden" value='<?=$krd?>'>
        				   </td>
        				</tr>
        			</form>
        		</table>
<?php

		    }
			return;
		}
	}
			
		
	if($rs->fields["USUA_EXT"] <> $extension) {
		$isql1 = $isql1." USUA_EXT = ".$extension.", ";
		$isql = "INSERT	INTO SGD_USH_USUHISTORICO
						(SGD_USH_ADMCOD,
						SGD_USH_ADMDEP, 
						SGD_USH_ADMDOC, 
						SGD_USH_USUCOD, 
						SGD_USH_USUDEP, 
						SGD_USH_USUDOC, 
						SGD_USH_MODCOD, 
						SGD_USH_FECHEVENTO, 
						SGD_USH_USULOGIN) 
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].", 
						$dep_sel, 
						'".$cedula."', 
						39, 
						".$sqlFechaHoy.", 
						'".$usuLogin."')";
		$db->conn->Execute($isql);
	} 
	
	if($rs->fields["USUA_PISO"] <> $piso) {
		$isql1 = $isql1." USUA_PISO = ".$piso.", ";
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
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].",
						$dep_sel, 
						'".$cedula."',
						8, 
						".$sqlFechaHoy.", 
						'".$usuLogin."')";
						$db->conn->Execute($isql);
	}
	
	if($rs->fields["USUA_EMAIL"] <> $email) {
		$isql1 = $isql1." USUA_EMAIL = '".$email."', ";
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
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].", 
						$dep_sel, 
						'".$cedula."', 
						40, 
						".$sqlFechaHoy.", 
						'".$usuLogin."')";
		$db->conn->Execute($isql);
	}
			
	if($rs->fields["USUA_EMAIL_1"] <> $email1) {
		$isql1 = $isql1." USUA_EMAIL_1 = '".$email1."', ";
	}
	
	if($rs->fields["USUA_EMAIL_2"] <> $email2) {
		$isql1 = $isql1." USUA_EMAIL_2 = '".$email2."', ";
	}
	
		
	if (empty($permRadMail)){
		$isql1 = $isql1." USUA_PERM_RADEMAIL = 0 ,";
	}
	else {
		$isql1 = $isql1." USUA_PERM_RADEMAIL = 1 ,";	
	}

	
	if(empty($permDespla)) {
		$isql1 = $isql1." USUA_PERM_DESPLA = 0 ,";
	}
	else{
		$isql1 = $isql1." USUA_PERM_DESPLA = 1 ,";	
	}
	
	
	if(empty($repMailCert)) {
		$isql1 = $isql1." USUA_REP_MAILCERT = 0 ,";
	}
	else{
		$isql1 = $isql1." USUA_REP_MAILCERT = 1 ,";	
	}
	
	
	if(empty($pqrVerbal)) {
		$isql1 = $isql1." USUA_PRAD_PQRVERBAL = 0 ,";
	}
	else{
		$isql1 = $isql1." USUA_PRAD_PQRVERBAL = 1 ,";	
	}
	
	
	if(empty($devCorreo)) {
		$isql1 = $isql1." USUA_PERM_DEV = 0 ,";
	}
	else{
		$isql1 = $isql1." USUA_PERM_DEV = 1 ,";	
	}
	
	if (empty($_POST['repMailRadExp'])) {
	    $isql1 = $isql1." USUA_NOTIF_RADEXP = 0, ";
	}
	else {
	    $isql1 = $isql1." USUA_NOTIF_RADEXP = 1, ";
	}
	
	if(empty($permRecRadEnt)) {
		$isql1 = $isql1." USUA_PERM_REC_RADENTRADA = 0 ,";
	}
	else{
		$isql1 = $isql1." USUA_PERM_REC_RADENTRADA = 1 ,";	
	}
	
	
	if(empty($no_trd)) {
		$isql1 = $isql1." USUA_NO_TIPIFICA = 0, ";
	}
	else {
		$isql1 = $isql1." USUA_NO_TIPIFICA = 1, ";	
	}
	
	
    if(empty($autenticaLDAP)){
		$isql1 = $isql1." USUA_AUTH_LDAP = 0, ";
	}
	else {
		$isql1 = $isql1." USUA_AUTH_LDAP = 1, ";	
	}
    
	
	if(empty($ordena)) {
		$isql1 = $isql1." USUA_PERM_ORDENAR = 0, ";
	}
	else {
		$isql1 = $isql1." USUA_PERM_ORDENAR = 1, ";	
	}

	
    if ($_POST['perm_servweb']) {
		$isql1 = $isql1." USUA_ADM_SERVWEB = 1, ";
	}
	else {
		$isql1 = $isql1." USUA_ADM_SERVWEB = 0, ";
	}

	if($notifAdm) {
		$isql1 = $isql1." USUA_NOTIF_ADMIN = 0, ";
	}
	else {
		$isql1 = $isql1." USUA_NOTIF_ADMIN = 1, ";	
	}
	
	if ($cedulafirma) {
	    $isql1 = $isql1." IDENTIFICACION = '".$cedulafirma."' ";
	}   else {
	    $isql1 = $isql1." IDENTIFICACION = '' ";
	}
	
	
	if ($isql1 != "" ) {
		$isql1 = "	UPDATE	USUARIO
					SET		" .$isql1. " 
					WHERE	USUA_LOGIN = '".$usuLogin."'";

		$queryAnt ="SELECT	*
					FROM	USUARIO 
					WHERE	USUA_LOGIN = '".$usuLogin."'";
		$estadoAnt = $db->conn->Execute($queryAnt);
		
		$rsupdate = $db->conn->Execute($isql1);
	}

	//var_dump($isql1);
	include "./acepPermisosModif.php";

	
	$isql = "SELECT	USUA_ESTA,
					USUA_PRAD_TP2,
					USUA_PERM_ENVIOS,
					USUA_ADMIN,
					USUA_ADMIN_ARCHIVO, 
					USUA_NUEVO, 
					CODI_NIVEL, 
					USUA_PRAD_TP1, 
					USUA_MASIVA, 
					USUA_PERM_DEV, 
					SGD_PANU_CODI, 
					USUA_CODI 
			FROM	USUARIO
			WHERE 	USUA_LOGIN = '".$usuLogin."'";
	$rs=$db->conn->Execute($isql);
	if (!$swConRadicado) {
?>

  <table align="center" border="2" bordercolor="#000000">
   <form name="frmConfirmaCreacion" action="../formAdministracion.php" method="post">
	<tr bordercolor="#FFFFFF"> 
	 <td width="211" height="30" colspan="2" class="listado2">
	  <p> <span class=etexto>
		<center> <B> El usuario <?=$usuLogin?> ha sido Modificado con &Eacute;xito </B> </center>
	   </span> </p>
	 </td>
	</tr>
	<tr bordercolor="#FFFFFF">	
	 <td height="30" colspan="2" class="listado2">
	  <center> <input class="botones" type="submit" name="Submit" value="Aceptar"> </center>
	  <input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
	  <input name="krd" type="hidden" value='<?=$krd?>'>
	 </td>
	</tr>
   </form>
  </table>

<?php
	}
	else
		return;
}
else {
	$rol = 0;
	
	$isql = "	SELECT	MAX(USUA_CODI) AS NUMERO
				FROM	USUARIO
				WHERE 	DEPE_CODI = ".$dep_sel;
	$rs7= $db->conn->Execute($isql);
	$nusua_codi = $rs7->fields["NUMERO"] + 1;
	
	if($perfil=="Auditor"){
		$rol = 3;
	}
	elseif ($perfil=="Jefe") {
		$rol = 1;
	}
	
	$isql_inicial = "INSERT	INTO USUARIO 
							(USUA_CODI,
							SGD_ROL_CODIGO, 
							DEPE_CODI,
							USUA_LOGIN,
							USUA_FECH_CREA,
							USUA_NOMB, 
							USUA_DOC, 
							USUA_NACIM,
							";
	$isql_final = " VALUES ($nusua_codi,
							$rol, 
							$dep_sel, 
							'".strtoupper($usuLogin)."',
							$sqlFechaHoy, 
							'".$nombre."', '".
							$cedula."',
							";
	if (($dia == "") && ($mes == "") && ($ano == ""))
		$isql_final = $isql_final . "''" .", ";
	else {	
		$fenac = $db->conn->DBTimeStamp("$ano-$mes-$dia");
		$isql_final = $isql_final.$fenac.", ";
	}
	
	if ($piso <> "") {
		$isql_inicial = $isql_inicial . " USUA_PISO, ";
		$isql_final = $isql_final.$piso.", ";
	}
	
	if ($ubicacion) {
		$isql_inicial = $isql_inicial . " USUA_AT, ";
		$isql_final = $isql_final."'".$ubicacion."', ";
	}
    
	if ($email) {
		$isql_inicial = $isql_inicial . " USUA_EMAIL, ";
		$isql_final = $isql_final."'".$email."', ";
	}
    	
	if ($permArchivar) {
		$isql_inicial = $isql_inicial . " PERM_ARCHI, ";
		$isql_final = $isql_final."'1', ";
	}	        
	
	if ($email1) {
		$isql_inicial = $isql_inicial . " USUA_EMAIL_1, ";
		$isql_final = $isql_final."'".$email1."', ";
	}
	
	if ($email2) {
		$isql_inicial = $isql_inicial . " USUA_EMAIL_2, ";
		$isql_final = $isql_final."'".$email2."', ";
	}
	
	if ($extension) {
		$isql_inicial = $isql_inicial . " USUA_EXT, ";
		$isql_final = $isql_final.$extension.", ";
	}
	
	if ($_POST['perm_servweb']){
		$perm_servweb = 1;
	}
	
		
	$isql_inicial = $isql_inicial . " USUA_PASW, PERM_RADI_SAL, ";
	$isql_final = $isql_final."123, 2,";
	
	//echo "--->$isql";
	include "acepPermisosNuevo.php";
	//echo "<---Fin ";
	$isql = $isql_inicial.$isql_final;
	//INICIALIZAMOS LA INSERCION EN LAS DIFERENTES TABLAS.....
	$okU = $db->conn->Execute($isql);	//Tabla USUARIOS
	
	if ($okU){
		$insertUsuaDepe = "	INSERT	INTO SGD_USD_USUADEPE(
									USUA_DOC,
									USUA_LOGIN,
									DEPE_CODI,
									SGD_USD_DEFAULT,
									SGD_USD_SESSACT)
							VALUES	('".$cedula."',
									'".strtoupper($usuLogin)."',
									$dep_sel,
									'1',
									'1')";
		$okU2 = $db->conn->Execute($insertUsuaDepe);	//Tabla SGD_USD_USUADEPE
		
		if (is_array($_REQUEST['Slc_ano'])) {
    		foreach ($_REQUEST['Slc_ano'] as $valor)
    		{
    			$sqlIns1 = "INSERT INTO SGD_USUAROL_ANO (SGD_URA_LOGIN, SGD_URA_DOC, SGD_URA_ANO)
    						VALUES ('$usuLogin', '$cedula', $valor)";
    			$rs1 = $db->conn->Execute($sqlIns1);
    		}
		}

		if (is_array($_REQUEST['Slc_deps'])) {
    		foreach ($_REQUEST['Slc_deps'] as $val)
    		{
    			$sqlIns2 = "INSERT INTO SGD_USUAROL_DEPENDENCIA (SGD_URD_LOGIN, SGD_URD_DOC, SGD_URD_DEPE_CODI)
    						VALUES ('$usuLogin', '$cedula', $val)";
    			$rs2 = $db->conn->Execute($sqlIns2);
    		}
		}
	
		$isql ="SELECT	USUA_CODI 
				FROM	USUARIO
				WHERE 	USUA_LOGIN = '".$usuLogin."'";
		$rs = $db->conn->Execute($isql);
	
		if ($masiva) {
			$isql ="INSERT	INTO CARPETA_PER
							(USUA_CODI,
							DEPE_CODI,
							NOMB_CARP,
							DESC_CARP,
							CODI_CARP)
					VALUES (" . $rs->fields["USUA_CODI"] . ", 
							" . $dep_sel . ", 
							'Masiva', 
							'Radicacion Masiva',
							5 )";
			$db->conn->Execute($isql);
		}
	
		$isql ="INSERT	INTO SGD_USH_USUHISTORICO
						(SGD_USH_ADMCOD,
						SGD_USH_ADMDEP,
						SGD_USH_ADMDOC,
						SGD_USH_USUCOD,
						SGD_USH_USUDEP,
						SGD_USH_USUDOC,
						SGD_USH_MODCOD,
						SGD_USH_FECHEVENTO,
						SGD_USH_USULOGIN)
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].",
						".$dep_sel.",
						'".$cedula."',
						1 , 
						".$sqlFechaHoy.",
						'".$usuLogin."')";
		$db->conn->Execute($isql);
		$isql ="SELECT	USUA_LOGIN,
						USUA_ESTA,
						USUA_PRAD_TP2,
						USUA_PERM_ENVIOS,
						USUA_ADMIN,
						USUA_ADMIN_ARCHIVO,
						USUA_NUEVO,
						CODI_NIVEL,
						USUA_PRAD_TP1,
						USUA_MASIVA,
						USUA_PERM_DEV,
						SGD_PANU_CODI
				FROM	USUARIO 
				WHERE	USUA_LOGIN = '".$usuLogin."'";
		$rs = $db->conn->Execute($isql);

		//Confirmamos las inserciones de datos
		//$ok = $db->conn->CompleteTrans();
		if (strtoupper($rs->fields["USUA_LOGIN"])==strtoupper($usuLogin)) {
?>

  <form name="frmConfirmaCreacion" action="../formAdministracion.php" method="post">
   <table align="center" border="2" bordercolor="#000000">
	<tr bordercolor="#FFFFFF">
	 <td width="211" height="30" colspan="2" class="listado2">
	  <p> <span class=etexto>
	   <center> <B> El usuario <?=$usuLogin?> ha sido creado con &Eacute;xito </B> </center>
	  </span> </p>
	 </td>
	</tr>
	<tr bordercolor="#FFFFFF">
	 <td height="30" colspan="2" class="listado2">
	  <center><input class="botones" type="submit" name="Submit" value="Aceptar"></center>
	  <input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
	  <input name="krd" type="hidden" value='<?=$krd?>'>
	 </td>
	</tr>
   </table>
  </form>

<?php
		}
		else {
			echo ".:Existe un error en los datos diligenciados:.";
		}
	}
	else {
		echo ".Existe un error en los datos diligenciados."; 
	}
}


if($usua_activo==2) {
	if ($perfil == "Jefe") {
		$usrActualEncargado = $db->conn->GetRow("SELECT	USUA_LOGIN
												 FROM	USUARIO 
												 WHERE	DEPE_CODI = $dep_sel AND SGD_ROL_CODIGO=2");
		$query ="UPDATE	USUARIO
				 SET	SGD_ROL_CODIGO = 0 
				 WHERE 	DEPE_CODI = $dep_sel AND SGD_ROL_CODIGO=2";
		$db->conn->Execute($query);
		
		$query ="INSERT	INTO SGD_USH_USUHISTORICO
						(SGD_USH_ADMCOD,
						SGD_USH_ADMDEP,
						SGD_USH_ADMDOC,
						SGD_USH_USUDEP,
						SGD_USH_USUDOC,
						SGD_USH_MODCOD,
						SGD_USH_FECHEVENTO,
						SGD_USH_USULOGIN)
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].",
						".$dep_sel.",
						'".$cedula."',
						79 , 
						".$sqlFechaHoy.", 
						'".$usrActualEncargado['USUA_LOGIN']."')";
		$db->conn->Execute($query);
		
		$query ="UPDATE	USUARIO
				 SET	SGD_ROL_CODIGO = 2
				 WHERE	USUA_LOGIN = '$Slc_enc'";
		$db->conn->Execute($query);
		
		$arrUsrDestino = $db->conn->GetRow( "SELECT	USUA_CODI,
													DEPE_CODI,
													USUA_DOC 
											 FROM	USUARIO
											 WHERE 	USUA_LOGIN = '$Slc_enc'");
		
		$query ="INSERT INTO SGD_USH_USUHISTORICO 
						(SGD_USH_ADMCOD, 
						SGD_USH_ADMDEP, 
						SGD_USH_ADMDOC, 
						SGD_USH_USUCOD, 
						SGD_USH_USUDEP, 
						SGD_USH_USUDOC, 
						SGD_USH_MODCOD, 
						SGD_USH_FECHEVENTO,
						SGD_USH_USULOGIN) 
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$arrUsrDestino["USUA_CODI"].",
						".$arrUsrDestino["DEPE_CODI"].", 
						'".$arrUsrDestino["USUA_DOC"]."', 
						78 , 
						".$sqlFechaHoy.", 
						'".$Slc_enc."')";
		$db->conn->Execute($query);
	}
	else { 
		
	}
}
else if ($usua_activo==1) {
	if ($perfil == "Jefe") {
		$usrActualEncargado = $db->conn->GetRow("SELECT	USUA_LOGIN
												 FROM	USUARIO
												 WHERE	DEPE_CODI = $dep_sel AND SGD_ROL_CODIGO=2");
														
		$query ="UPDATE	USUARIO
				 SET	SGD_ROL_CODIGO = 0 
				 WHERE	DEPE_CODI = $dep_sel AND SGD_ROL_CODIGO=2";
		$db->conn->Execute($query);
		
		$query ="INSERT	INTO SGD_USH_USUHISTORICO 
						(SGD_USH_ADMCOD, 
						SGD_USH_ADMDEP, 
						SGD_USH_ADMDOC, 
						SGD_USH_USUCOD,
						SGD_USH_USUDOC, 
						SGD_USH_MODCOD, 
						SGD_USH_FECHEVENTO,
						SGD_USH_USULOGIN) 
				VALUES	(".$_SESSION['codusuario'].", ".
				        $_SESSION['dependencia'].", '".
				        $_SESSION['usua_doc']."',
						".$rs->fields["USUA_CODI"].",
						".$dep_sel.",
						'".$cedula."', 
						79,
						".$sqlFechaHoy.", 
						'".$usrActualEncargado['USUA_LOGIN']."')";
		$db->conn->Execute($query);
	}
}
?>

 </body>
</html>
