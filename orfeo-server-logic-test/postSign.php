<?php

    //include("../config.php");
    //include_once('../PHPMailer_v5.1/class.phpmailer.php');
    //include_once("../include/db/ConnectionHandler.php");
    //$db = new ConnectionHandler("../");
	$db = null;
	
	$signature = $_POST['signature'];
	$timeStamp = $_POST['timeStamp'];
	$timestampFlag = $_POST['timestampFlag'];
	$file = $_POST['file'];
    $signerCommonName =  $_POST["signerCommonName"];

    $fileArray = explode('|', $file);
    $signatureArray = explode('|', $signature);
    $response = "";
	$text = "";
	
    for ($i = 0; $i < count($fileArray) ; $i++)
    {
        $currentFile =$fileArray[$i];
        $currentSignature = $signatureArray[$i];

        $response =  $response . " - ". manageResponse($currentSignature, $timeStamp, $currentFile, $db, $timestampFlag);
		$text = $text . responseTranslate($response) . ";";		 
    }
    echo $text;								  

    function manageResponse ($signature, $timeStamp, $file, $db, $timestampFlag)
    {
        $signatureFile = $file .  "." . $timeStamp . ".signature";
        $handle = fopen($signatureFile, 'w');

        if (!$handle)
        {
            return "Can not open file: " . $signatureFile;
        }

        fwrite($handle, base64_decode($signature));
		fclose($handle);
		
        $currentVerifyFile = $file.'.'.$timeStamp . '.verify';
        while(true)
        {
            if (file_exists ( $currentVerifyFile ) && filesize ($currentVerifyFile) > 0)
            {
                break;
            }
            sleep(1);
        }


        $digitalSignatureVerification = file_get_contents($currentVerifyFile);

        $newSignedFileName = str_replace(".pdf", "." . $timeStamp . ".signed.pdf", $file);
        if (strpos($digitalSignatureVerification, 'Digital signature OK') !== false)
        {
            rename($newSignedFileName, $file);
            //sendToAres($db, $file);
            //databaseSignatureUpdate($db, $file, $signerCommonName);
        }
        else
        {
            unlink($newSignedFileName);
        }

        if ($timestampFlag === "true")
        {
            databaseTimeStampQuotaUpdate($db);
        }
        removeTempFiles($file, $timeStamp);
        return  $digitalSignatureVerification ;
    }

    function sendToAres($db, $file)
    {

        $aresWsdl = getProperty($db, "ARES_WSDL");

        if ($aresWsdl !== "")
        {
            $absoluteBasePath = str_replace("orfeo-server-logic", "", dirname(__FILE__));
            $basePath = $absoluteBasePath . 'orfeo-server/';
            $currentSignCommand = '"' .$basePath . 'orfeo-server.jar"';

            $currentCommand = "ares";
            $aresUser = getProperty($db, "ARES_USER");
            $aresPassword = getProperty($db, "ARES_PASSWORD");

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                $command = 'start /B cmd /C java -jar ' . $currentSignCommand . ' ' . $currentCommand . ' "' . $file . '" "' . $aresUser . '" "' . $aresPassword . '" "' . $aresWsdl  . '" >NUL 2>NUL';
                pclose(popen($command, 'r'));
            }
            else
            {
                shell_exec('java -jar ' . $currentSignCommand . ' ' . $currentCommand . ' "' . $file . '" "'. $aresUser . '" "' . $aresPassword . '" "' . $aresWsdl . '" > /dev/null &');
            }
        }
    }

    function databaseSignatureUpdate($db, $file, $signerCommonName)
    {
        $indexSlash = strrpos ( $file , "/" );
        $indexPdf = strrpos ( $file , ".pdf" );
        $documentId = substr ( $file , $indexSlash + 1,  ($indexPdf - $indexSlash) - 1);

        $updateStateSql = "UPDATE SGD_FIRRAD_FIRMARADS set SGD_FIRRAD_FIRMA = '01', USUA_NOMB_COMPL = '$signerCommonName', SGD_FIRRAD_FECHA =  SYSDATE WHERE RADI_NUME_RADI = $documentId";
        $db->query($updateStateSql);
    }

    function databaseTimeStampQuotaUpdate($db)
    {
        $currentQuota = getProperty($db, "TSA_CURRENT_QUOTA");

        if($currentQuota !== "")
        {
            $currentQuota++;

            $updateStateSql = "UPDATE PROPIEDADES set VALOR = '" . $currentQuota . "' WHERE NOMBRE LIKE 'TSA_CURRENT_QUOTA'";
            $db->query($updateStateSql);

            $totalQuota = getProperty($db, "TSA_TOTAL_QUOTA");
            $criticQuota = getProperty($db, "TSA_CRITIC_QUOTA");
            $criticQuotaNotificationMail = getProperty($db, "TSA_CRITIC_QUOTA_NOTIFICATION");

            if ($currentQuota >= $criticQuota )
            {
                $modulo = $currentQuota % $criticQuota;
                $notificationFlag = $modulo % 300;

                if ($notificationFlag == 0)
                {
                    notifyTimeStampQuota($db, $totalQuota, $currentQuota, $criticQuotaNotificationMail);
                }

            }

        }
    }

    function notifyTimeStampQuota($db, $totalQuota, $currentQuota, $email)
    {
        if($email !== "")
        {
            $admPHPMailer = getProperty($db, "EMAIL_FROM");
            $userPHPMailer = getProperty($db, "EMAIL_SMTP_SERVER_USER");
            $keyPHPMailer = getProperty($db, "EMAIL_SMTP_SERVER_PASSWORD");
            $hostPHPMailer = getProperty($db, "EMAIL_SMTP_SERVER_HOST");
            $portPHPMailer = getProperty($db, "EMAIL_SMTP_SERVER_PORT");
            $debugPHPMailer = "1";
            $subject = "Cupo de estampas agotado :: Cuota total :" . $totalQuota . " - Cuota usada: " . $currentQuota . "::" ;
            $message = "Cupo de estampas de tiempo (usadas en los procesos de firma digital) del sistema documental Orfeo agotado :: Cuota total :" . $totalQuota . " - Cuota usada: " . $currentQuota . "::" ;

            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->Host       = $hostPHPMailer;
            $mail->SMTPDebug  = $debugPHPMailer;
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = "tls";
            $mail->Host       = $hostPHPMailer;
            $mail->Port       = $portPHPMailer;
            $mail->Username   = $userPHPMailer;
            $mail->Password   = $keyPHPMailer;
            $mail->SetFrom($admPHPMailer, $admPHPMailer);
            $mail->Subject = $subject;
            $mail->AltBody = $subject;
            $mail->MsgHTML($message);
            $mail->AddAddress($email,$email);
            $mail->Send();
        }
    }

    function responseTranslate ($response)
    {
        if (strpos($response, 'Digital signature OK') !== false)
        {
            $currentResponse = "Firma digital generada exitosamente";
        }
        else if (strpos($response, 'User session information') !== false)
        {
            $currentResponse =  "Firma digital generada con un certificado que no le corresponde al usuario actualmente autenticado";
        }
        else if (strpos($response, 'Invalid certificate') !== false)
        {
            $currentResponse =  "Firma digital generada con un certificado revocado";
        }
        else if (strpos($response, 'Not trusted signing certificate') !== false)
        {
            $currentResponse =  "Firma digital generada con un certificado emitido por una autoridad de certificacion de digital que no es de confianza";
        }
        else if (strpos($response, 'vigencia del certificado') !== false)
        {
            $currentResponse =  "No es posible completar la firma digital porque el certificado se encuentra caducado";
        }
        else
        {
            $currentResponse =  "Firma digital invalida. No se puede verificar el archivo: " . $response;
        }

        return $currentResponse;
    }

    function removeTempFiles ($file, $timeStamp)
    {
        $finalPdfFile = $file."." . $timeStamp;

        unlink($finalPdfFile . ".verify");
		unlink($finalPdfFile . ".signature");
		unlink($finalPdfFile . ".hash");
    }

    function getProperty($db, $propertyName)
    {
        try
        {
            $selectSql = "SELECT VALOR FROM  PROPIEDADES WHERE NOMBRE LIKE '" . $propertyName . "'";
            $rs = $db->query($selectSql);

            if(!$rs->EOF)
            {
                if (trim ($rs->fields[0]) !== '')
                {
                    return $rs->fields[0];
                }
                else
                {
                    return $rs->fields['VALOR'];
                }
            }

            return "";
        }
        catch (Exception $e)
        {
            return "";
        }
    }
