<?php
$sqlConcat = $db->conn->Concat("U.USUA_DOC","'-'","U.USUA_LOGIN");
if($_POST['busqRadicados']){
    $busqRadicados = strtoupper($_POST['busqRadicados']);
	$whereUsuario = "( U.USUA_LOGIN LIKE '%$busqRadicados%' OR U.USUA_NOMB LIKE '%$busqRadicados%') ";
}

if(isset($_POST['depActual']) && ($_POST['depActual']==true) ){
	$whereDependencia = "";
}
else{
	if ($whereUsuario){
		$whereDependencia = "AND U.DEPE_CODI = " . $dep_sel;
	}
	else{
		$whereDependencia = "U.DEPE_CODI = " . $dep_sel;
	}
	
}

$isql ="SELECT	U.USUA_NOMB AS Nombre, 
				U.USUA_LOGIN AS Usuario,
               	CASE U.SGD_ROL_CODIGO	WHEN 1 THEN 'Jefe'
										WHEN 2 THEN 'Jefe -Encargado'
										WHEN 3 THEN 'Auditor'
										ELSE 'Normal' END AS Rol,
				CASE U.USUA_ESTA	WHEN 1 THEN 'Activo'
									WHEN 2 THEN 'Vacaciones'
									ELSE 'Inactivo' END AS Estado,
				D.DEPE_NOMB AS Dependencia,
				" . $sqlConcat  . " AS CHR_USUA_DOC
		FROM	USUARIO U
				JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI
		WHERE   $whereUsuario
				$whereDependencia
		ORDER BY " . $order . " " . $orderTipo;
?>
