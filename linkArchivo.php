<?php
session_start();
/*
 * Invocado por una funcion javascript (funlinkArchivo(numrad,rutaRaiz))
 * Consulta el path del radicado
 * @author Liliana Gomez Velasquez
 * @since 20 de AGOSTO de 2009
 * @category imagenes
 */

if ($_GET['krd'] == NULL) {
    $krd = $_SESSION["krd"];
} else
    $krd = $_GET['krd'];

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);
    
// echo "valor krd ".$krd;
if (! $ruta_raiz)
    $ruta_raiz = ".";
// Valida que exista una session de Orfeo abierta
// if(!isset($_SESSION['dependencia']) or !isset($_SESSION['nivelus'])) include "$ruta_raiz/rec_session.php";

if (isset($db))
    unset($db);
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
include_once "$ruta_raiz/tx/verLinkArchivo.php";

$verLinkArchivo = new verLinkArchivo($db);

if (strlen($numrad) <= "14") {
    // Se trata de un Radicado
    
    $resulVali = $verLinkArchivo->valPermisoRadi($numrad, $radpad);
    $verImg = $resulVali['verImg'];
    $pathImagen = $resulVali['pathImagen'];
    
    $file = $ruta_raiz . "/bodega/" . $pathImagen;
} else {
    // Se trata de un anexo
    $resulValiA = $verLinkArchivo->valPermisoAnex($numrad);
    // print_r($resulValiA);
    $verImg = $resulValiA['verImg'];
    $pathImagen = $resulValiA['pathImagen'];
    $file = "$ruta_raiz/bodega/" . substr(trim($numrad), 0, 4) . "/" . substr(trim($numrad), 4, 3) . "/docs/" . trim($pathImagen);
}

$fileArchi = $file;
$tmpExt = explode('.', $pathImagen);
$filedatatype = $pathImagen;
// Si se tiene una extension
if (count($tmpExt) > 1) {
    $filedatatype = $tmpExt[count($tmpExt) - 1];
}
$verImg = "SI";
if ($verImg == "SI") {
    if (file_exists($fileArchi)) {
        header('Content-Description: File Transfer');
        switch ($filedatatype) {
            case 'odt':
                header('Content-Type: application/vnd.oasis.opendocument.text');
                break;
            case 'doc':
                header('Content-Type: application/msword');
                break;
            case 'tif':
                header('Content-Type: image/TIFF');
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            case 'xls':
                header('Content-Type: application/vnd.ms-excel');
                break;
            case 'csv':
                header('Content-Type: application/vnd.ms-excel');
                break;
            case 'ods':
                header('Content-Type: application/vnd.ms-excel');
                break;
            default:
                header('Content-Type: application/octet-stream');
                break;
        }
        
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit();
    } else {
        die("<B><CENTER>  NO se encontro el Archivo </a><br>");
    }
} elseif ($verImg = "NO") {
    die("<B><CENTER>  NO tiene permiso para acceder el Archivo </a><br>");
} else {
    die("<B><CENTER>  NO se ha podido encontrar informacion del Documento</a><br>");
}

?>