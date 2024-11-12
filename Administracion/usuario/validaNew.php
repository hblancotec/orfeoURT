<?php
$ruta_raiz = "../.."; 
include_once($ruta_raiz . "/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$nick = $_REQUEST['username'];
$doc  = $_REQUEST['usuaDoc'];
$perf = $_REQUEST['perfil'];
$est  = $_REQUEST['estado'];
$login= $_REQUEST['login'];
$rol  = $_REQUEST['rol'];
$depe = $_REQUEST['depeCodi']; 


### Entra cuando se digita el Login en el formulario
$respuesta = "";
if($nick){
	$sqlLogin ="SELECT	USUA_LOGIN
				FROM	USUARIO
				WHERE	USUA_LOGIN = '$nick'";
	$rsLogin = $db->conn->Execute($sqlLogin);
	$usuario = $rsLogin->fields['USUA_LOGIN'];

	if($usuario){   
	  // El usuario existe en la Base de Datos  
	    $respuesta =  "Login ya existe";  
	}  
	else  
	{  
	  // Ese nick esta libre  
	    //$respuesta =  "Login valido";  
	}
}


### Entra cuando se digita el Documento de identificación en el formulario
if($doc){
	$sqlDoc = "	SELECT	USUA_DOC
				FROM	USUARIO
				WHERE	USUA_DOC = '$doc'";
	$rsDoc = $db->conn->Execute($sqlDoc);
	$documento = $rsDoc->fields['USUA_DOC'];

	if($documento){   
	  // El usuario existe en la Base de Datos  
	    $respuesta = "Documento ya existe";  
	}  
	else  
	{  
	  // Ese nick esta libre  
	    //$respuesta =  "Documento valido";  
	}
}


### Entra cundo se selecciona perfil Auditor en el formualario
if($perf == 'Auditor'){
	
	### SECCION QUE PINTA EL SELECT DE LOS ANHOS PERMITIDOS
	$year = date('Y');
	$anoIni = 2007;
		
	$sqlA ="WITH CTEYear as(
				SELECT	$anoIni AS NumYear
				UNION ALL
				SELECT	NumYear + 1
				FROM	CTEYear
				WHERE	NumYear < $year
				)
				SELECT	NumYear
				FROM	CTEYear";	
	
	$rsA = $db->conn->Execute($sqlA);
	
	$rs_ano = $db->conn->Execute("SELECT SGD_URA_ANO FROM SGD_USUAROL_ANO WHERE SGD_URA_LOGIN ='$login'");
	$Slc_ano = array();
	$j = 0;
	while ($tmpr = $rs_ano->FetchRow()) {
		$Slc_ano[$j] = $tmpr['SGD_URA_ANO'];
		$j += 1;
	}
	
	$slc_ano1 = $rsA->GetMenu2('Slc_ano[]', $Slc_ano, false, true, 8, 'Class="select" id="Slc_ano"');
	
	###############################################################################
	
	
	
	### SECCION QUE PINTA EL SELECT DE LAS DEPENDENCIAS PERMITIDAS
	$sql = "SELECT	CAST(DEPE_CODI AS char(3))" . $db->conn->concat_operator . "' - '" . $db->conn->concat_operator . "DEPE_NOMB AS DEPE_NOMB,
					DEPE_CODI AS DEPE_CODI
			FROM	DEPENDENCIA
			ORDER BY DEPE_NOMB";
	$rs = $db->conn->Execute($sql);
	
	$rs_dep = $db->conn->Execute( "	SELECT SGD_URD_DEPE_CODI FROM SGD_USUAROL_DEPENDENCIA WHERE SGD_URD_LOGIN ='$login'");

	$Slc_deps = array();
	$i = 0;
	while ($tmp = $rs_dep->FetchRow()) {
		$Slc_deps[$i] = $tmp['SGD_URD_DEPE_CODI'];
		$i += 1;
	}
	
	$slc_dep1 = $rs->GetMenu2('Slc_deps[]', $Slc_deps, false, true, 8, 'Class="select" id="Slc_deps"');
	
	###############################################################################
	
	
	
	echo "  <table width='100%' border='1' align='center' class='t_bordeGris' cellspacing='5'>
				<tr class='timparr'>
					<td class='titulos2' width='11%'> A&ntilde;os Permitidos: </td>
					<td class='listado2' width='22%'> ";
	
	$respuesta = $slc_ano1;
	
	$respuesta .=  "			</td>
					<td class='titulos2' width='11%'> Dependencias permitidas: </td>
					<td class='listado2' colspan='3'> ";

	$respuesta .=  $slc_dep1;
	
	$respuesta .= "			</td>
				</tr>
			</table>";
}


### Entra cundo el estado es Inactivo x Vacaciones (2) y el usuario editado es el Jefe
if ($est == 2 && $rol == 'Jefe') {
	
	$sqlE = "SELECT	USUA_NOMB,
					USUA_LOGIN,
					USUA_CODI,
					SGD_ROL_CODIGO
			FROM	USUARIO
			WHERE	DEPE_CODI = $depe and
					USUA_ESTA = 1 --AND USUA_CODI != 1 
                    AND	SGD_ROL_CODIGO < 3
			ORDER BY SGD_ROL_CODIGO DESC, USUA_NOMB";
	$rsE = $db->conn->Execute($sqlE);
	
	$rs_enc = $db->conn->Execute( "	SELECT USUA_NOMB FROM USUARIO WHERE SGD_ROL_CODIGO = 2 AND DEPE_CODI=".$depe);
	
	$Slc_enc = $rs_enc->fields['USUA_NOMB'];
	
	$slc_enc1 = $rsE->GetMenu2('Slc_enc', $rs_enc, false, false, 0, 'Class="select" id="Slc_enc"');
	
	
	$respuesta = $slc_enc1;
}

echo $respuesta;
?> 