<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;
$isql ="SELECT	U.USUA_NOMB, 
				U.USUA_LOGIN, 
				U.SGD_ROL_CODIGO, 
				U.USUA_CODI
		FROM	USUARIO U
		WHERE	U.DEPE_CODI = $id AND
				U.USUA_ESTA = 1 AND
				(U.SGD_ROL_CODIGO IN (1,2) OR U.USUA_PERM_REC_RADENTRADA = 1)
				
		ORDER BY U.SGD_ROL_CODIGO DESC";
$rs = $db->conn->Execute($isql);
if ($rs && !$rs->EOF) {
	$i=0;
	do {
		$usuaNomb =  $rs->fields[0];
		$usuaLogin =  $rs->fields[1];
		$rolCodigo =  $rs->fields[2];
		$usCodigo =  $rs->fields[3];
		switch ($rolCodigo) {
			case 1: $rol = " - Jefe";
				break;
			case 2: $rol = " - Jefe Encargado";
				break;
			default: $rol = " - Delegado";
		}
		$usuaNomb = $usuaNomb . $rol;
		
		//$usuarios[$i] = $usuaNomb.'-'.$usuaLogin.'-'.$rolCodigo.'-'.$usCodigo;
		echo '<option value="'.$usCodigo.'">'.$usuaNomb.'</option>';
		$i++;
		$rs->MoveNext();
	}while(!$rs->EOF);
}
echo json_encode($usuarios);
?>