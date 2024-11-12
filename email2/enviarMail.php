<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usuaPermRadEmail'] != 1){
	die(include "../sinpermiso.php");
	exit;
}

include "../class_control/correoElectronico.php";
$pattern="/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i";
preg_match_all($pattern,$mailFrom, $salida);

$destinatario=$salida[0];
$destinatario=$destinatario[0];

//para el envío en formato HTML
$archivoRadicadoMail = str_replace("../bodega","https://orfeo.dnp.gov.co/bodega",$archivoRadicado);
$cuerpo = "<br>$texto
                <br> Se ha recibido su correo y se ha radicado con el $numeroRadicado, el cual tambien puede ser consultado en el portal Web del DNP.</p>
                 <br><br><b><center>Puede Consultarlos el estado en:
                 <a href='https://pqrsd.dnp.gov.co/consulta.php?rad=$numeroRadicado'>https://pqrsd.dnp.gov.co/consulta.php</a><br><br><br>".$respuesta."</b></center><BR>
                 <hr>Documento Recibido<hr>
                 <table>
                 <tr><td>
                 $archivoRadicadoMail
                 </td></tr>
                 </table>";
$asunto = "Se ha recibido su Correo (No. $numeroRadicado)";
$objMail = new correoElectronico("..");
$objMail->FromName = "Notificaciones";
$result = $objMail->enviarCorreo(array($destinatario), null, null, $asunto, $cuerpo);
if($result) {
    echo "Se envi&oacute; correo a ".$destinatario;
} else {
    echo "fallo el env&iacute;o de correo respuesta a ".$destinatario;
}
?>