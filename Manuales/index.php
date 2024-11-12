<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
if(!isset($_SESSION['dependencia']))include "../rec_session.php";
include "$ruta_raiz/config.php";
include "$ruta_raiz/include/class/Plantillas.class.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("..");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objPln = new Plantilla($db);
$tbl=$objPln->vistaDir(BODEGAPATH . "Ayuda",0);
?>
<html>
<head>
<title>.: AYUDAS de Orfeo :.</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body>
<?php echo $tbl ?>
</body>
</html>