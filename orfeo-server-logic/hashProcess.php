<?php
	
	$userId = substr($_REQUEST["usua_identif"], 0, strlen($_REQUEST["usua_identif"]) - 1);
    $signerId = '"' . $userId . '"';

    $currentFile =  $_REQUEST['file'] ;
    $currentTimeStamp = $_REQUEST['timeStamp'];    

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
	$qrBackgroundImageOffset = '"' . $_REQUEST["qrBackgroundImageOffset"] . '"';
	$backgroundSignImage = '"' . $_REQUEST["backgroundSignImage"] . '"';
	$leftSignImage = '"' . $_REQUEST["leftSignImage"] . '"';
	
    $absoluteBasePath = str_replace("orfeo-server-logic", "", dirname(__FILE__));

    $basePath = $absoluteBasePath . 'orfeo-server/';
    $currentSignCommand = '"' .$basePath . 'orfeo-server.jar"';
    $signCommand = 'all';
    $signReason = '"Firma y aprobacion de documento Departamento Nacional de Planeacion DNP - SGD Orfeo"';
    $signLocation = '"Departamento Nacional de Planeacion DNP - SGD Orfeo - Colombia"';
	
	$signQRImagePath = '"' . $basePath . 'LogoGrande18k-2.png"';
    $signCasPath = '"' . $absoluteBasePath . 'orfeo-server-cas/"';
	$documentUrl = '"' . getAbsolutePathOfficialUrlAres($currentFile) . '"';


	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
	{
		$command = 'start /B cmd /C java -jar ' . $currentSignCommand . ' ' . $signCommand . ' "' . $currentFile . '" ' . $signerCommonName . ' ' . $signerId . ' ' . $currentTimeStamp . ' ' . $signReason . ' ' . $signLocation . ' ' . $backgroundSignImage . ' ' . $signCasPath . ' ' . $documentUrl . ' ' . $visible . ' ' . $lowerLeftX . ' ' . $lowerLeftY . ' ' . $upperRightX . ' ' . $upperRightY . ' ' . $qrCodeSize . ' ' . $qrCodeVisibility . ' ' . $page . ' ' . $placeholder . ' ' . $qrPage . ' ' . $absoluteX . ' ' . $absoluteY . ' ' . $absoluteSize . ' ' . $renderingMode . ' ' . $description . ' ' . $signQRImagePath . ' ' . $qrBackgroundImageFlag . ' ' . $qrBackgroundImageOffset . ' ' . $leftSignImage .' >c:/temp/log1.txt 2>c:/temp/log2.txt';
		
		pclose(popen($command, 'r'));
	}
	else
	{
		shell_exec('java -jar ' . $currentSignCommand . ' ' . $signCommand . ' "' . $currentFile . '" '. $signerCommonName . ' ' . $signerId . ' ' . $currentTimeStamp . ' ' . $signReason . ' ' . $signLocation . ' ' . $backgroundSignImage . ' ' . $signCasPath . ' ' . $documentUrl. ' ' . $visible . ' ' . $lowerLeftX . ' ' . $lowerLeftY . ' ' . $upperRightX . ' ' . $upperRightY . ' ' . $qrCodeSize . ' ' . $qrCodeVisibility . ' ' . $page . ' ' . $placeholder . ' ' . $qrPage . ' ' . $absoluteX . ' ' . $absoluteY . ' ' . $absoluteSize . ' ' . $renderingMode . ' ' . $description . ' ' . $signQRImagePath . ' ' . $qrBackgroundImageFlag . ' ' . $qrBackgroundImageOffset . ' ' . $leftSignImage .' > /dev/null &');
	}

    $currentHash = "";

    foreach (explode('|', $currentFile) as $currentToken)
    {
        if (strlen(trim ($currentHash)) > 0)
        {
            $currentHash = $currentHash . "|";
        }

        $currentHashFile = $currentToken.'.'.$currentTimeStamp . '.hash';
		
        while(true)
        {
            if (file_exists ( $currentHashFile ) && filesize ($currentHashFile) > 0)
            {
                break;
            }
            sleep(1);
        }

        $currentHash = $currentHash . file_get_contents($currentHashFile);
    }

    echo $currentHash;

	function getAbsolutePathOfficialUrlAres($currentFile)
	{
		$officialBaseUrl = "https://ares.dnp.gov.co/ARES_Server/layout/download.seam?id=";
		$currentUrl = "";

		foreach (explode('|', $currentFile) as $currentToken)
		{
			if (strlen(trim ($currentUrl)) > 0)
			{
				$currentUrl = $currentUrl . "|";
			}

			$currentFilename = basename($currentToken, ".pdf");
			$currentUrl = $currentUrl . $officialBaseUrl . $currentFilename;
		}

		return $currentUrl;
	}
