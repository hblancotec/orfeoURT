<?php 
//session_start();
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

		// Se consulta la posición anterior del anexo
		$queryC = "	SELECT	ANEX_ORDEN
					FROM	ANEXOS
					WHERE	ANEX_CODIGO = '$key'";
		$rsQ = $db->conn->Execute($queryC);
		$posAnt = $rsQ->fields[0];
		// Se valida si cambio la posición del anexo
		if ($posAnt != $pos) {
			// Se actualiza el campo ANEX_ORDEN
			$query = "	UPDATE	ANEXOS
						SET 	ANEX_ORDEN = $pos
						WHERE 	ANEX_CODIGO = '$key'";
			$rsUp = $db->conn->Execute($query);
			if (!$rsUp)
				$mensaje = 0;
			else {
				$numrad = substr($key, 0, 14);
				$queryH=	"	INSERT	INTO HIST_EVENTOS_ANEXOS
									(ANEX_RADI_NUME, ANEX_CODIGO, SGD_TTR_CODIGO, USUA_DOC)
							VALUES	($numrad, '$key', 85, '$cedula')";
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