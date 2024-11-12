<?php

define('ADODB_ASSOC_CASE', 1);
ignore_user_abort(true);
set_time_limit(0);

$ruta_raiz = ".";
require $ruta_raiz . "/config.php";
require $ADODB_PATH . "/adodb.inc.php";
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);
if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $rad = $_GET['r'];
    if (substr($rad, -1) == 2) {
        $sql = "select radi_path, tdoc_codi from radicado where radi_nume_radi=$rad";
        $rs = $conn->Execute($sql);
        if ($rs->RecordCount() > 0) {
            if ($rs->fields['tdoc_codi'] == 16) {
                $ruta = $rs->fields['radi_path'];
                if ($fd = fopen(BODEGAPATH . $ruta, "r")) {
                    $fsize = filesize(BODEGAPATH . $ruta);
                    $path_parts = pathinfo(BODEGAPATH . $ruta);
                    $ext = strtolower($path_parts["extension"]);
                    switch ($ext) {
                        case "pdf": {
                                header("Content-type: application/pdf");
                                header("Content-Disposition: attachment; filename=\"" . $rad . "." . $ext . "\"");
                            }break;
                        default: {
                                header("Content-type: application/octet-stream");
                                header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
                            }break;
                    }
                    header("Content-length: $fsize");
                    header("Cache-control: private");
                    while (!feof($fd)) {
                        $buffer = fread($fd, 2048);
                        echo $buffer;
                    }
                    fclose($fd);
                }
            }
        }
    }
}
exit;