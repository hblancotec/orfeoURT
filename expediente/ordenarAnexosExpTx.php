<?php 
session_start();
if (!$ruta_raiz) 
	$ruta_raiz= "..";
include_once ("$ruta_raiz/config.php");
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("..");
//$db->conn->debug=true;
// array con el nuevo orden de nuestros registros
$anexos_ordenados = $_POST['order'];
$pos = 1;
$mensaje = 0;


if($anexos_ordenados){
	foreach ($anexos_ordenados as $valor) {
		list($key, $cedula) = explode('B', $valor);

		$hoy = getdate();
		// Se consulta la posición anterior del anexo
		$queryC = "	SELECT	ANEXOS_EXP_ORDEN,
							SGD_EXP_NUMERO
					FROM	SGD_ANEXOS_EXP
					WHERE	ANEXOS_EXP_ID = '$key'";
		$rsQ = $db->conn->Execute($queryC);
		
		$posAnt = $rsQ->fields[0];
		$nuExp  = $rsQ->fields[1];
		
		// Se valida si cambio la posición del anexo
		if ($posAnt != $pos) {
			// Se actualiza el campo ANEX_ORDEN
			$query = "	UPDATE	SGD_ANEXOS_EXP
						SET 	ANEXOS_EXP_ORDEN = $pos
						WHERE 	ANEXOS_EXP_ID = '$key'";
			$rsUp = $db->conn->Execute($query);
			if (!$rsUp)
				$mensaje = 0;
			else {
				
				$queryH ="	INSERT INTO SGD_HFLD_HISTFLUJODOC
							(	SGD_FEXP_CODIGO,
								SGD_HFLD_FECH,
								SGD_EXP_NUMERO,
								USUA_DOC,
								USUA_CODI,
								DEPE_CODI,
								SGD_TTR_CODIGO,
								SGD_HFLD_OBSERVA)
							VALUES
							(	0,
								GETDATE(),
								'".$nuExp."',
								'".$_SESSION['usua_doc']."',
								".$_SESSION['codusuario'].",
								".$_SESSION['dependencia'].",
								85,
								'Se actualiza el orden de los anexos del expediente (".$key.")')";
				$rsHist = $db->conn->Execute($queryH);
				$mensaje ++;
			}
		}
		$pos++;
	}
	if ($mensaje == 0)
		echo "No se pudo actualizar el orden, por favor intente de nuevo";
	if ($mensaje >= 1)
		echo "Los anexos se ordenaron correctamente";
}
?>