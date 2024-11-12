<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}
if (!$ruta_raiz)
    $ruta_raiz = "..";
include_once "$ruta_raiz/class_control/firmaRadicado.php";
require_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/class_control/usuario.php";
include_once "$ruta_raiz/class_control/Radicado.php";

?>
<html>
<head>
<title>Registro de Solicitud de Firma</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../estilos_totales.css">
</head>
<body>
<?php
include "../config.php";
if (!$dependencia || !$usua_doc )   
	include "../rec_session.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objRadicado = new Radicado($db);

//Almacena la cantidad de radicados para firma
$num = count($checkValue);
//Iterador 
$i=0;
//Almacena la cadena de radicados que ha de ser enviada al applet
$radicados = "";
//Almacena la cadena de paths de los radicados que se han de firmar
$paths = "";
while ($i < $num) { 
	//Almacena temporalmente la solicitud de firma
	$record_id = key($checkValue); 
	if (strlen(trim ($radicados)) > 0){
		$radicados = $radicados . ",";
		$paths = $paths . ",";
	}
	$radicados = $radicados .  $record_id;
	
	$objRadicado->radicado_codigo($record_id);
	$paths = $paths . $objRadicado->getRadi_path();
	next($checkValue); 
	$i++;
}
$paths = "\\\\DNPDP42715\\bodega\\tmp\\pdf1.pdf|\\\\DNPDP42715\\bodega\\tmp\\pdf2.pdf|\\\\DNPDP42715\\bodega\\tmp\\pdf3.pdf";
//$paths = "\\\\DNPDP42715\\bodega\\tmp\\pdf2.pdf";
$id = "8999990902";
$name = "Hollman Ladino";
?>

<!-- 
<iframe frameborder="0" scrolling="auto" src="../orfeo-server-logic/signature.php?signerId=<?=$id?>&signerCommonName=<?=$name?>&file=<?= $paths ?>" width="100%" height="500"></iframe>
 -->	
	
<iframe frameborder="0" scrolling="auto" src="../orfeo-server-logic/signature.php?signerId=<?=$id?>&signerCommonName=<?=$name?>&file=<?= $paths ?>&timestampFlag=<?= "false" ?>&visible=<?= "true" ?>&lowerLeftX=<?= "5" ?>&lowerLeftY=<?= "169" ?>&upperRightX=<?= "75" ?>&upperRightY=<?= "667" ?>&qrCodeSize=<?= "200" ?>&qrCodeVisibility=<?= "true" ?>&page=<?= "1" ?>&placeholder=<?= " " ?>" width="100%" height="500"></iframe>
	
</body>
</html>