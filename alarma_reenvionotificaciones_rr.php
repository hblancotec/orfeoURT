<?php
require dirname(__FILE__) . "\\config.php";
//require $ruta_raiz."/include/db/ConnectionHandler.php";
//$db = new ConnectionHandler("$ruta_raiz");
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
#############################################################################
$conn = NewADOConnection($dsnn);
if( $conn ) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
	$sql = "select idene,fecha_registro,fecha_envio,remitente,asunto,cuerpo, ".
			"radi_nume_radi, radi_nume_sal, estado, intentos, para, cc, cco, anexosbase64json ".
			"from sgd_correonoenviado where estado=0";
	
	$rs = $conn->Execute($sql);
	if( $rs !== false ) {

		//$objMail = new correoElectronico($ruta_raiz, false, true);
		
	    while ($row = $rs->FetchRow()) {
			$cuerpo = stripslashes($row['cuerpo']);
			$anxJson = base64_decode($row['anexosbase64json']);
			$cnt = $row['intentos'] + 1;
			
			$defaults = array(
					CURLOPT_POST => 1,
					CURLOPT_HEADER => 0,
					CURLOPT_URL => SERVIDOR.PATHMVC.'notificacion/enviarCorreoRespuestaRapida',
					CURLOPT_FRESH_CONNECT => 0,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_FORBID_REUSE => 0,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_POSTFIELDS => (array(	
							'pathsAttachments'=>$anxJson,
							'NoRadicadoSalida'=>$row['radi_nume_sal'],
							'NoRadicadoPadre'=>$row['radi_nume_radi'],
							'nremitente'=>$row['remitente'],
							'destinatario'=>$row['para'],
							'cc'=>$row['cc'],
							'cco'=>$row['cco']
					)),
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0
			);
			// create a new cURL resource
			$ch = curl_init();
			//set URL and other appropriate options
			curl_setopt_array($ch, ($defaults));
			// grab URL and pass it to the browser
			if( ! $result = curl_exec($ch))
			{
			    $sqlu = "update sgd_correonoenviado set intentos=$cnt where idene=".$row['idene'];
				$errorMsg = curl_error($ch);
				$errorNro = curl_errno($ch);
			} else {
				$sqlu = "update sgd_correonoenviado set fecha_envio=getdate(), estado=1, intentos=$cnt where idene=".$row['idene'];
			}
			$stup = $conn->Execute($sqlu);
			// close cURL resource, and free up system resources
			curl_close($ch);
		}
		// Cerrar la conexión.
		$conn->Close();
	} else {
		die( print_r( sqlsrv_errors(), true));
	}	
} else {
	// No hay conexion a BD
}
?>