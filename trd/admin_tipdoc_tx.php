<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
if ($_SESSION['usua_perm_trd'] != 1) {
    die(include $ruta_raiz . "/sinpermiso.php");
    exit();
}

if (!$_POST && !$_GET){ 
	header("Location: consultar.php");
	die;
}
	
include_once "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$msg = "";
$hoy = date('Y/m/d_h:i:s');

###############################################################################
### SI SE VA A CONSULTAR UN TIPO DOCUMENTAL
if($_POST['Buscar'] == 'Buscar'){
	if($_POST['codigo']){
		$where = " WHERE SGD_TPR_CODIGO = ".$_POST['codigo'] . " AND ID_VERSION = $version ";
	}
	elseif ($_POST['tipDoc']){
		$where = " WHERE SGD_TPR_DESCRIP LIKE '%" .$_POST['tipDoc']. "%' AND ID_VERSION = $version ";
	}
}
else {
    $where = " WHERE ID_VERSION = $version ";
}

###############################################################################
### QUERY GENERAL PARA REALIZAR LAS CONSULTAS
$sql = "SELECT	ID AS ID,
				SGD_TPR_CODIGO AS CODIGO,
				SGD_TPR_DESCRIP AS DESCRIP, 
				SGD_TPR_TERMINO AS TERMINO, 
				IsNull (SGD_TPR_ALERTA,0) AS ALERTA,
				IsNull (SGD_TPR_NOTIFICA,0) AS NOTIFICA,
				IsNull (SGD_TPR_REPORT1,0) AS REPORT1,
				SGD_TPR_TP1 AS TP1, 
				SGD_TPR_TP2 AS TP2, 
				SGD_TPR_TP3 AS TP3, 
                SGD_TPR_TP4 AS TP4, 
				SGD_TPR_TP5 AS TP5, 
				SGD_TPR_TP6 AS TP6, 
                SGD_TPR_TP7 AS TP7, 
				SGD_TPR_TP8 AS TP8,
				SGD_TPR_TP9 AS TP9
		FROM	SGD_TPR_TPDCUMENTO".
		$where ." 
		ORDER BY SGD_TPR_DESCRIP, SGD_TPR_CODIGO";
$rs = $db->conn->Execute($sql);
###############################################################################




###############################################################################
### SI SE VA A CREAR EL TIPO DOCUMENTAL
if($_POST['val'] == 'nuevo'){
	
	$cons ="SELECT	SGD_TPR_CODIGO,
					SGD_TPR_DESCRIP
			FROM	SGD_TPR_TPDCUMENTO
			WHERE	SGD_TPR_DESCRIP = '" .$_POST['tipDoc']. "'";
	$rsCons = $db->conn->Execute($cons);
	
	$cod = $rsCons->fields['SGD_TPR_CODIGO'];
	$des = $rsCons->fields['SGD_TPR_DESCRIP'];
			
	### SE VALIDA SI YA EXISTE UN TIPO DOCUMENTAL CON EL MISMO NOMBRE
	if ($cod) {
		$msg = "No se pudo crear el Tipo Documental, porque ya existe uno con el mismo nombre: '" .$cod. " - " .$des. "'";
	}			
	else{
		
		if( $_POST['notifica'])		{ $not = 1; }  else{ $not = 0; }
		if( $_POST['reporte'])		{ $rep = 1; }  else{ $rep = 0; }
		if( $_POST['salida'])		{ $sal = 1; }  else{ $sal = 0; }
		if( $_POST['entrada'])		{ $ent = 1; }  else{ $ent = 0; }
		if( $_POST['memorado'])		{ $mem = 1; }  else{ $mem = 0; }
		if( $_POST['cirExt'])		{ $ext = 1; }  else{ $ext = 0; }
		if( $_POST['resolucion'])	{ $res = 1; }  else{ $res = 0; }
		if( $_POST['concepto'])		{ $con = 1; }  else{ $con = 0; }
		if( $_POST['edicto'])		{ $edi = 1; }  else{ $edi = 0; }
		if( $_POST['circular'])		{ $cir = 1; }  else{ $cir = 0; }
		if( $_POST['auto'])			{ $aut = 1; }  else{ $aut = 0; }
		if( $_POST['termino'])		{ $te = $_POST['termino']; } else { $te = 0; }
		if( $_POST['alerta'])		{ $al = $_POST['alerta']; } else { $al = 0; }
				
		
		$ult = "SELECT	TOP 1 SGD_TPR_CODIGO
				FROM	SGD_TPR_TPDCUMENTO
				ORDER BY SGD_TPR_CODIGO DESC";
		$rsUlt = $db->conn->Getone($ult);
		$cod = $rsUlt + 1;

		$ins = "INSERT INTO SGD_TPR_TPDCUMENTO (SGD_TPR_CODIGO, 
												SGD_TPR_DESCRIP, 
												SGD_TPR_TERMINO,
												SGD_TPR_ALERTA,
												SGD_TPR_NOTIFICA, 
												SGD_TPR_REPORT1,
												SGD_TPR_TP1, 
												SGD_TPR_TP2, 
												SGD_TPR_TP3, 
												SGD_TPR_TP4, 
												SGD_TPR_TP5, 
												SGD_TPR_TP6, 
												SGD_TPR_TP7, 
												SGD_TPR_TP8,
												SGD_TPR_TP9)
				VALUES (".$cod.",
						'".$_POST['tipDoc']."',
						".$te.",
						".$al.",
						".$not.",
						".$rep.",
						".$sal.",
						".$ent.",
						".$mem.",
						".$ext.",
						".$res.",
						".$con.",
						".$edi.",
						".$cir.",	
						".$aut.")";

		$rsIns = $db->conn->Execute($ins);

		### SI LA CREACION DEL TIPO DOCUMENTAL ES EXITO, SE REGISTRA EN EL LOG DE TIPOS DOCUMENTALES
		if ($rsIns){
			$log = "INSERT INTO SGD_HIST_TIPDOC (	SGD_TPR_CODIGO,
													USUA_LOGIN,
													USUA_DOC,
													SGD_HIST_TIPDOC_OBS )
							VALUES (".$cod.",
									'".$_SESSION['login']."',
									'".$_SESSION['usua_doc']."',
									'Creacion del Tipo Documental: ".$_POST['tipDoc']."')";
			$rsLog = $db->conn->Execute($log);
			$msg = "Se creo el tipo documental: '" .$cod. " - ".$_POST['tipDoc']."' correctamente";
		}
		else{
			$msg = "No se pudo crear el Tipo Documental, por favor verifique con el Administrador del Sistema";
		}
	}
}
###############################################################################




###############################################################################
### SI SE VA A MODIFICAR UN TIPO DOCUMENTAL
if($_POST['val'] == 'modificar'){
	
	$descAnt = $rs->fields['DESCRIP'];
	$termAnt = $rs->fields['TERMINO'];
	$alerAnt = $rs->fields['ALERTA'];
	$notiAnt = $rs->fields['NOTIFICA'];
	$repoAnt = $rs->fields['REPORT1'];
	$salAnt	 = $rs->fields['TP1'];
	$entAnt	 = $rs->fields['TP2'];
	$memAnt	 = $rs->fields['TP3'];
	$extAnt	 = $rs->fields['TP4'];
	$resAnt	 = $rs->fields['TP5'];
	$conAnt	 = $rs->fields['TP6'];
	$ediAnt	 = $rs->fields['TP7'];
	$cirAnt	 = $rs->fields['TP8'];
	$autAnt	 = $rs->fields['TP9'];
	
	### VERIFICA SI CAMBIA EL CAMPO DE DESCRIPCION
	if( $_POST['tipDoc'] != $descAnt){
		$set = "SGD_TPR_DESCRIP = '" .$_POST['tipDoc']. "',";
		$obs = "Cambio la Descripción del TD: " .$descAnt. " por: " . $_POST['tipDoc'];
	}
	else{
		$set = "";
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO DE TERMINO
	if( $_POST['termino'] == ""){
		$_POST['termino'] = 0;
	}
	if( $_POST['termino'] != $termAnt){
		$set = $set. "SGD_TPR_TERMINO = ".$_POST['termino']. ",";
		$obs = $obs. " - Cambio el Termino del TD: " .$termAnt. " por: " .$_POST['termino'];
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO DE ALERTA
	if( $_POST['alerta'] == ""){
		$_POST['alerta'] = 0;
	}
	if( $_POST['alerta'] != $alerAnt){
		$set = $set. " SGD_TPR_ALERTA = ".$_POST['alerta']. ",";
		$obs = $obs. " - Cambio el campo de Alerta del TD: " .$alerAnt. " por: " .$_POST['alerta'];
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO DE NOTIFICACION
	if( $_POST['notifica']){
		$notNew = 1;
	}
	else{
		$notNew = 0;
	}
	if( $notNew != $notiAnt){
		$set = $set. "SGD_TPR_NOTIFICA = ".$notNew. ",";
		$obs = $obs. " - Cambio el campo Notifica del TD: " .$notiAnt. " por: " .$notNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO DE REPORTE
	if( $_POST['reporte']){
		$repNew = 1;
	}
	else{
		$repNew = 0;
	}
	if( $repNew != $repoAnt){
		$set = $set. " SGD_TPR_REPORT1 = " .$repNew. ",";
		$obs = $obs. " - Cambio el campo de Reporte del TD: " .$repoAnt. " por: " .$repNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP1-SALIDA
	if( $_POST['salida']) { $salNew = 1; } else { $salNew = 0; }
	if( $salNew != $salAnt){
		$set = $set. " SGD_TPR_TP1 = " .$salNew. ",";
		$obs = $obs. " - Cambio el TP1 del TD: " .$salAnt. " por: " .$salNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP2-ENTRADA
	if( $_POST['entrada']) { $entNew = 1; } else { $entNew = 0; }
	if( $entNew != $entAnt){
		$set = $set. " SGD_TPR_TP2 = " .$entNew. ",";
		$obs = $obs. " - Cambio el TP2 del TD: " .$entAnt. " por: " .$entNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP3-MEMORANDO
	if( $_POST['memorando']) { $memNew = 1; } else { $memNew = 0; }
	if( $memNew != $memAnt){
		$set = $set. " SGD_TPR_TP3 = " .$memNew. ",";
		$obs = $obs. " - Cambio el TP3 del TD: " .$memAnt. " por: " .$memNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP4-CIRCULARES EXTERNAS
	if( $_POST['cirExt']) { $extNew = 1; } else { $extNew = 0; }
	if( $extNew != $extAnt){
		$set = $set. " SGD_TPR_TP4 = " .$extNew. ",";
		$obs = $obs. " - Cambio el TP4 del TD: " .$extAnt. " por: " .$extNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP5-RESOLUCION
	if( $_POST['resolucion']) { $resNew = 1; } else { $resNew = 0; }
	if( $resNew != $resAnt){
		$set = $set. " SGD_TPR_TP5 = " .$resNew. ",";
		$obs = $obs. " - Cambio el TP5 del TD: " .$resAnt. " por: " .$resNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP6-CONCEPTOS
	if( $_POST['concepto']) { $conNew = 1; } else { $conNew = 0; }
	if( $conNew != $conAnt){
		$set = $set. " SGD_TPR_TP6 = " .$conNew. ",";
		$obs = $obs. " - Cambio el TP6 del TD: " .$conAnt. " por: " .$conNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP7-EDICTOS
	if( $_POST['edicto']) { $ediNew = 1; } else { $ediNew = 0; }
	if( $ediNew != $ediAnt){
		$set = $set. " SGD_TPR_TP7 = " .$ediNew. ",";
		$obs = $obs. " - Cambio el TP7 del TD: " .$ediAnt. " por: " .$ediNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP8-CIRCULARES
	if( $_POST['circular']) { $cirNew = 1; } else { $cirNew = 0; }
	if( $cirNew != $cirAnt){
		$set = $set. " SGD_TPR_TP8 = " .$cirNew. ",";
		$obs = $obs. " - Cambio el TP8 del TD: " .$cirAnt. " por: " .$cirNew;
	}
	
	
	### VERIFICA SI CAMBIA EL CAMPO TP9-AUTOS
	if( $_POST['auto']) { $autNew = 1; } else { $autNew = 0; }
	if( $autNew != $autAnt){
		$set = $set. " SGD_TPR_TP9 = " .$autNew. ",";
		$obs = $obs. " - Cambio el TP9 del TD: " .$autAnt. " por: " .$autNew;
	}
	
		
	### SE ELIMINAN LA COMA QUE VENGA AL FINAL DE LA CADENA DE CARACTERES
	if(substr($set,-1) == ",") 
		$set = substr($set,0,strlen($set)-1);
	
	
	### SE ACTUALIZA EL TIPO DOCUMENTAL
	$upd = "UPDATE	SGD_TPR_TPDCUMENTO
			SET		$set
			WHERE	SGD_TPR_CODIGO = ".$_POST['codigo'];
	$rsUpd = $db->conn->Execute($upd);
	
	
	### SI LA ACTUALIZACION DEL TIPO DOCUMENTAL ES EXITO, SE REGISTRA EN EL LOG DE TIPOS DOCUMENTALES
	if ($rsUpd){
		$log = "INSERT INTO SGD_HIST_TIPDOC (	SGD_TPR_CODIGO,
												USUA_LOGIN,
												USUA_DOC,
												SGD_HIST_TIPDOC_OBS )
						VALUES (" .$_POST['codigo']. ",
								'" .$_SESSION['login']."',
								'" .$_SESSION['usua_doc']."',
								'Modificación del Tipo Documental: ".$_POST['codigo'] . $obs . "')";
		$rsLog = $db->conn->Execute($log);
		$msg = "Se actualizo correctamente el tipo documental: ".$_POST['codigo']. " - " . $_POST['tipDoc'];
	}
	else{
		$msg = "No se pudo actualizar el Tipo Documental, por favor verifique con el Administrador del Sistema";
	}
}
###############################################################################
?>