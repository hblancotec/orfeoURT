<?php

$input="W3sib3JpZ2luYWwiOiIyMDE4LzYwMC9kb2NzL0xWQUxCVUVOQTE1NDIyMjY2MzZfUmVzcHVlc3RhLnBkZiIsIm5hbWVBdHRhY2htZW50IjoiUmVzcHVlc3RhLnBkZiJ9LHsib3JpZ2luYWwiOiIvMjAxOC82MDAvMjAxODYwMDA1Nzg3MzIucGRmIiwibmFtZUF0dGFjaG1lbnQiOiJTb2xpY2l0dWQucGRmIn1d";

$json_encode = base64_decode($input);
echo $json_encode;
$json_decode = json_decode($json_encode);
echo "<br />";
$bodega="\\\\vorfeobod\\bodega\\";
foreach($json_decode as $k=>$v){
    echo $k . " => " . $v->original . " => " . $v->nameAttachment .  "<br />";
    $carpetaInicial = anexosRR;
    if (!is_dir($filename)) {
        if (mkdir( dirname(__FILE__) . DIRECTORY_SEPARATOR . $carpetaInicial)){
            echo "Carpeta " .dirname(__FILE__) . DIRECTORY_SEPARATOR . $carpetaInicial . " creada.";
        } else {
            echo "ERROR no se cre&oacute; la carpeta " .dirname(__FILE__) . $carpetaInicial . ".";
        }
    }
    echo "<br />";
    $pathOri = stripos($v->original, "bodega") ? substr($v->original, stripos($v->original, "bodega")+6): $v->original;
    if (copy($bodega.$pathOri, dirname(__FILE__) . DIRECTORY_SEPARATOR . $carpetaInicial."/".$v->nameAttachment)) {
        echo "Archivo ".$v->nameAttachment." copiado.";
    } else {
        echo "Error al copiar ". $v->nameAttachment. ".";
    }
    echo "<br />";
}
?>