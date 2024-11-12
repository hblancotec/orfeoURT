<?php

    $timeStamp = $_POST['timeStamp'];
	$file = $_POST['file'];

    if (strpos($file, '|') !== false)
    {
        $fileArray = explode('|', $file);
        $response = "";
        for ($i = 0; $i < count($fileArray) ; $i++)
        {
            $currentFile =$fileArray[$i];

            manageDummyResponse( $timeStamp, $currentFile);
        }
    }
    else
    {
        manageDummyResponse($timeStamp, $file);
    }

    echo "Process cancelled";

    function manageDummyResponse ($timeStamp, $file)
    {
        $signatureFile = $file .  "." . $timeStamp . ".signature";
        $handle = fopen($signatureFile, 'w') or die ('Cannot open file:  '.$signatureFile);
        fwrite($handle, base64_decode("UHJvY2VzcyBjYW5jZWxsZWQ="));

        $currentVerifyFile = $file.'.'.$timeStamp . '.verify';
        while(true)
        {
            if (file_exists ( $currentVerifyFile ) && filesize ($currentVerifyFile) > 0)
            {
                break;
            }
            sleep(1);
        }

        removeTempFilesCancelProcess($file, $timeStamp);
    }

    function removeTempFilesCancelProcess ($file, $timeStamp)
    {
        $finalPdfFile = $file."." . $timeStamp;
        $newSignedFileName = str_replace(".pdf", "." . $timeStamp . ".signed.pdf", $file);

        unlink($finalPdfFile . ".verify");
        unlink($finalPdfFile . ".signature");
        unlink($finalPdfFile . ".hash");
        unlink($newSignedFileName );
    }
