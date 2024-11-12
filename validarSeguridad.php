<?php
session_start();
$ruta_raiz = ".";
$html = "";

if ($_POST['radicado'] != null) {
	
	$ruta = $_POST['ruta'];
	$login = $_SESSION['login'];
	$radicado = $_POST['radicado'];
	$expediente = $_POST['expediente'];
	
	include_once("$ruta_raiz/config.php");
	if ($ruta)
	   $ruta = $carpetaBodega . base64_decode($_POST['ruta']);

	include './include/class/mime.class.php';
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$ADODB_COUNTRECS = true;
	
	if ($radicado) {
	    $sql = "SELECT dbo.VALIDAR_ACCESO_RADEXP (".$radicado.", '', '".$login."') AS PERMISO";
	} elseif ($expediente) {
	    $sql = "SELECT dbo.VALIDAR_ACCESO_RADEXP ('', '".$expediente."', '".$login."') AS PERMISO";
	}
	   
	$rs = $db->conn->Execute($sql);
	$permiso = $rs->fields['PERMISO'];
	if ($permiso == 0) {
	    if ($ruta) {
    	    if (file_exists($ruta)) {
    	        $html = "../".$ruta;
    	    } else {
    	        $html = "-";
    	    }
	    } else {
	        $html = "../verradicado.php?verrad=$radicado";
	    }
	}
}
else {
	$html = "";
}

echo $html;

?>
