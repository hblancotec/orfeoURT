<?php

$currentFile =  $_REQUEST['file'];
$currentTimeStamp = $_REQUEST['timeStamp'];
$signerId = '"' . $_REQUEST["usua_identif"] . '"';
$signerCommonName = '"' . $_REQUEST["usua_nomb_compl"] . '"';
$timestampFlag =  '"' . $_REQUEST["timestampFlag"] . '"';
$visible =  '"' . $_REQUEST["visible"] . '"';
$lowerLeftX =  '"' . $_REQUEST["lowerLeftX"] . '"';
$lowerLeftY =  '"' . $_REQUEST["lowerLeftY"] . '"';
$upperRightX =  '"' . $_REQUEST["upperRightX"] . '"';
$upperRightY =  '"' . $_REQUEST["upperRightY"] . '"';
$qrCodeSize =  '"' . $_REQUEST["qrCodeSize"] . '"';
$qrCodeVisibility =  '"' . $_REQUEST["qrCodeVisibility"] . '"';
$page =  '"' . $_REQUEST["page"] . '"';
$placeholder =  '"' . $_REQUEST["placeholder"] . '"';
$qrPage =  '"' . $_REQUEST["qrPage"] . '"';
$absoluteX =  '"' . $_REQUEST["absoluteX"] . '"';
$absoluteY =  '"' . $_REQUEST["absoluteY"] . '"';
$absoluteSize =  '"' . $_REQUEST["absoluteSize"] . '"';
$renderingMode =  '"' . $_REQUEST["renderingMode"] . '"';
$description =  '"' . $_REQUEST["description"] . '"';
$qrBackgroundImageFlag =  '"' . $_REQUEST["qrBackgroundImageFlag"] . '"';
$qrBackgroundImageOffset =  '"' . $_REQUEST["qrBackgroundImageOffset"] . '"';

$absoluteBasePath = str_replace("orfeo-server-logic-test", "", dirname(__FILE__));

$basePath = $absoluteBasePath . 'orfeo-server/';
$currentSignCommand = '"' . $basePath . 'orfeo-server.jar"';
$signCommand = 'all';
$signReason = '"Firma y aprobacion de documento Departamento Nacional de Planeacion DNP - Sistema Orfeo"';
$signLocation = '"Departamento Nacional de Planeacion DNP - Orfeo - Colombia"';
$bgSignImagePath = '"' . $basePath . 'SoftwareColombia-DNP-IntegracionOrfeo-Diseno-Firma-V-1-20180612.png"';
$bgQRImagePath = '"' . $basePath . 'LogoFirmadoDigitalmenteDNP.png"';
$graphicSignImagePath = '"' . $basePath . 'LogoFirmadoDigitalmenteDNP.png"';
$signCasPath = '"' . $absoluteBasePath . 'orfeo-server-cas/"';

//$documentUrl = '"' . getAbsolutePathOfficialUrl($currentFile) . '"';
$documentUrl = '"' . getAbsolutePathOfficialUrlAres($currentFile) . '"';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $command = 'start /B cmd /C java -jar ' . $currentSignCommand . ' ' . $signCommand . ' "' . $currentFile . '" ' . $signerCommonName . ' ' . $signerId . ' ' . $currentTimeStamp . ' ' . $signReason . ' ' . $signLocation . ' ' . $bgSignImagePath . ' ' . $signCasPath . ' ' . $documentUrl . ' ' . $visible . ' ' . $lowerLeftX . ' ' . $lowerLeftY . ' ' . $upperRightX . ' ' . $upperRightY . ' ' . $qrCodeSize . ' ' . $qrCodeVisibility . ' ' . $page . '  ' . $placeholder . ' ' . $qrPage . ' ' . $absoluteX . ' ' . $absoluteY . ' ' . $absoluteSize . ' ' . $renderingMode . ' ' . $description . ' ' . $bgQRImagePath . ' ' . $qrBackgroundImageFlag . ' ' . $qrBackgroundImageOffset . ' ' . $graphicSignImagePath . '>D:/Temp/log1.txt 2>D:/Temp/log2.txt';

    pclose(popen($command, 'r'));
} else {
    shell_exec('java -jar ' . $currentSignCommand . ' ' . $signCommand . ' "' . $currentFile . '" ' . $signerCommonName . ' ' . $signerId . ' ' . $currentTimeStamp . ' ' . $signReason . ' ' . $signLocation . ' ' . $bgSignImagePath . ' ' . $signCasPath . ' ' . $documentUrl . ' ' . $visible . ' ' . $lowerLeftX . ' ' . $lowerLeftY . ' ' . $upperRightX . ' ' . $upperRightY . ' ' . $qrCodeSize . ' ' . $qrCodeVisibility . ' ' . $page . ' ' . $placeholder . ' ' . $qrPage . ' ' . $absoluteX . ' ' . $absoluteY . ' ' . $absoluteSize . ' ' . $renderingMode . ' ' . $description . ' ' . $bgQRImagePath . ' ' . $qrBackgroundImageFlag . ' ' . $qrBackgroundImageOffset . ' ' . $graphicSignImagePath . '> /dev/null &');
}

$currentHash = "";

foreach (explode('|', $currentFile) as $currentToken) {
    if (strlen(trim($currentHash)) > 0) {
        $currentHash = $currentHash . "|";
    }

    $currentHashFile = $currentToken . '.' . $currentTimeStamp . '.hash';

    while (true) {
        if (file_exists($currentHashFile) && filesize($currentHashFile) > 0) {
            break;
        }
        sleep(1);
    }

    $currentHash = $currentHash . file_get_contents($currentHashFile);
}

echo $currentHash;

function getAbsolutePathUrl($currentFile)
{
    $resourceUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $currentUrl = "";

    foreach (explode('|', $currentFile) as $currentToken) {
        if (strlen(trim($currentUrl)) > 0) {
            $currentUrl = $currentUrl . "|";
        }

        $indexBasePath = strrpos($resourceUrl, "orfeo-server-logic");
        $urlBasePath = substr($resourceUrl, 0, $indexBasePath);

        $indexPdfPath = strrpos($currentToken, "bodega/");
        $urlPdfPath = substr($currentToken, $indexPdfPath);

        $currentUrl = $currentUrl . $urlBasePath . $urlPdfPath . "?time=" . time();
    }

    return $currentUrl;
}

function getAbsolutePathOfficialUrl($currentFile)
{
    $officialBaseUrl = "http://orfeo.ani.gov.co/Orfeo/";
    $currentUrl = "";

    foreach (explode('|', $currentFile) as $currentToken) {
        if (strlen(trim($currentUrl)) > 0) {
            $currentUrl = $currentUrl . "|";
        }

        $indexPdfPath = strrpos($currentToken, "bodega/");
        $urlPdfPath = substr($currentToken, $indexPdfPath);

        $currentUrl = $currentUrl . $officialBaseUrl . $urlPdfPath . "?time=" . time();
    }

    return $currentUrl;
}


function getAbsolutePathOfficialUrlAres($currentFile)
{
    $officialBaseUrl = "https://ares.dnp.gov.co/ARES_Server/layout/download.seam?id=";
    $currentUrl = "";

    foreach (explode('|', $currentFile) as $currentToken) {
        if (strlen(trim($currentUrl)) > 0) {
            $currentUrl = $currentUrl . "|";
        }

        $currentFilename = basename($currentToken, ".pdf");
        $currentUrl = $currentUrl . $officialBaseUrl . $currentFilename;
    }

    return $currentUrl;
}
