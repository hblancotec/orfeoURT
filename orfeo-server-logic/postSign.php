<?php
    session_start();
    if (count($_SESSION) == 0) 
    {
        die(include "../sinacceso.php");
        exit();
    }

    if (!$ruta_raiz)
        $ruta_raiz = "../";
    
    require $ruta_raiz.'config.php';
    require $ruta_raiz.'include/db/ConnectionHandler.php';
    
    //
    // Data base connection manager
    $db = new ConnectionHandler($ruta_raiz);
	
    $signature = $_POST['signature'];
	$timeStamp = $_POST['timeStamp'];
	$timestampFlag = $_POST['timestampFlag'];
	$file = $_POST['file'];
    $signerCommonName =  $_POST["signerCommonName"];

    $fileArray = explode('|', $file);
    $signatureArray = explode('|', $signature);
    $response = "Digital signature OK";
	
	$text = "";
	
    for ($i = 0; $i < count($fileArray) ; $i++)
    {
        $currentFile =$fileArray[$i];
        $currentSignature = $signatureArray[$i];
        $currentResponse =  manageResponse($currentSignature, $timeStamp, $currentFile, $db, $timestampFlag);
        
        if (strpos($currentResponse, 'Digital signature OK') === false)
        {
            $response =  $currentResponse;
        }
		
		$text = $text . responseTranslate($response) . ";";
    }
	echo $text;
	
    echo responseTranslate($response); 
	
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
        $contador_i = 0;
        while(true)
        {
            $contador_i++;
            if (file_exists ( $currentVerifyFile ) && filesize ($currentVerifyFile) > 0)
            {
                break;
            }
            if( $contador_i == 30 ){
                return "Can not open file: " . $signatureFile;
            }
            sleep(1);
        }

        $digitalSignatureVerification = file_get_contents($currentVerifyFile);

        $newSignedFileName = str_replace(".pdf", "." . $timeStamp . ".signed.pdf", $file);
        
        if (strpos($digitalSignatureVerification, 'Digital signature OK') !== false)
        {
            rename($newSignedFileName, $file);
			sendToAres($db, $file);
			databaseSignatureUpdate($db, $file, $signerCommonName);
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
        return  $digitalSignatureVerification;
    }

    function databaseSignatureUpdate($db, $file, $signerCommonName)
    {
        $pathSuffix = BODEGAPATH;
        $pathSuffix = str_replace ("\\" , "/", $pathSuffix);
        
        $file = str_replace($pathSuffix, "", $file);
        
        //
        // Update current signature detail table
        // 0=Solicitado 1=Firmado 2=Modificacion 3=Rechazado 4=FinalizadoPorRechazo
        $updateStateSql = "UPDATE d set d.estado = 1 FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.rutapdf = '" . $file . "' and d.usua_doc = '"  . $_SESSION['usua_doc'] . "' and d.usua_login = '" . $_SESSION['login'] . "'";
        $ok1 = $db->conn->Execute($updateStateSql);
        
        //
        // Get to total number of signers
        $totalSignersSQL = "SELECT COUNT(1) FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.rutapdf = '" . $file . "'";
        $totalSigners = $db->conn->GetOne($totalSignersSQL);
        
        //
        // Get the current number of signers successfully applied
        $currentSignersSQL =  "select COUNT(1) from SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.rutapdf = '" . $file . "' and d.estado = 1";
        $currentSigners = $db->conn->GetOne($currentSignersSQL);
        
        //
        // Insertamos en el histórico
        $sqlRadHist = "select radi_nume_radi from SGD_CICLOFIRMADOMASTER where rutapdf = '" . $file . "'";
        $radicadoActual = $db->conn->GetOne($sqlRadHist);
        //Ibiscom
        $queryIndice = "select anex_indice from SGD_CICLOFIRMADOMASTER where rutapdf = '" . $file . "'";
        $esindice = $db->conn->GetOne($queryIndice);
        if(!$esindice){
            insertHistoric($db, $radicadoActual, "Usuario ha firmado digitalmente.", 40);
        }
        //fin Ibiscom
        
        if ($totalSigners === $currentSigners)
        {
            //Ibiscom
            if(!$esindice){
            // Actualizar el pdf al radicado.
                $sql = "select r.radi_depe_radi, r.radi_nume_radi from radicado r where r.radi_nume_radi = (select m.radi_nume_radi from SGD_CICLOFIRMADOMASTER m where m.rutapdf = '" . $file . "' and m.estado = 1)";
                $rs = $db->conn->Execute($sql);
                if (!$rs->EOF) 
                {
                    $tmpRadi = $rs->fields[1];
                    $tmpdepe = $rs->fields[0];
                    $TmpFile = date('Y').'/'.$tmpdepe.'/'.$tmpRadi."_".rand(1, 99999).".pdf";
                    
                    // Copie el pdf firmado al radicado respectivo.
                    if (copy(BODEGAPATH.$file, BODEGAPATH.$TmpFile)) 
                    {   
                        $sql = "update radicado set radi_path='$TmpFile' where radi_nume_radi=$tmpRadi";
                        $db->conn->Execute($sql);
                        
                        // 1=Iniciado 2=Solicitud Cambio 3=Finalizado Bien 4=Finalizado Cancelado
                        $updateStateMasterSql = "UPDATE SGD_CICLOFIRMADOMASTER set estado = 3 where rutapdf = '" . $file . "'";
                        $db->conn->Execute($updateStateMasterSql);
                        
                        //
                        // Insertamos en el histórico evento de cambio de imagen
                        insertHistoric($db, $tmpRadi, "Circuito de firma finalizado satisfactoriamente.", 23);
                        
                    } 
                }
            }else{
                $sql = "select r.DEPE_CODI, r.ANEXOS_EXP_ID, r.SGD_EXP_NUMERO from SGD_ANEXOS_EXP r
                    where r.ANEXOS_EXP_ID = (select m.anex_indice from SGD_CICLOFIRMADOMASTER m where m.rutapdf = '" . $file . "' and m.estado = 1)";
                $rs = $db->conn->Execute($sql);
                
                if (!$rs->EOF)
                {
                    $tmpAnex = $rs->fields[1];
                    $tmpdepe = $rs->fields[0];
                    $tmpExp  = $rs->fields[2];
                    $TmpFile = date('Y').'/'.$tmpdepe.'/'.$tmpExp.'/'."Indice_".$tmpAnex."_".rand(1, 99999).".pdf";
                    
                    // Copie el pdf firmado al radicado respectivo.
                    if (copy(BODEGAPATH.$file, BODEGAPATH.$TmpFile))
                    {
                        $sql = "update SGD_ANEXOS_EXP set ANEXOS_EXP_PATH='$TmpFile' where ANEXOS_EXP_ID=$tmpAnex";
                        $db->conn->Execute($sql);
                        
                        // 1=Iniciado 2=Solicitud Cambio 3=Finalizado Bien 4=Finalizado Cancelado
                        $updateStateMasterSql = "UPDATE SGD_CICLOFIRMADOMASTER set estado = 3 where rutapdf = '" . $file . "'";
                        $db->conn->Execute($updateStateMasterSql);
                        
                    }
                }
                
            }
        }
    }
    
    function insertHistoric($db, $radicado, $comments, $tx)
    {
        $insertStateSql = "INSERT INTO HIST_EVENTOS (DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI, HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO, HIST_DOC_DEST, DEPE_CODI_DEST) VALUES (" . $_SESSION['dependencia'] . ", " . $db->conn->sysTimeStamp . " , " . $_SESSION['codusuario'] . ", " .$radicado.  ", '" .$comments. "' , " . $_SESSION['codusuario'] . ", '" . $_SESSION['usua_doc'] . "' , " . $tx . " , '" .$_SESSION['usua_doc']. "' , " . $_SESSION['dependencia'] . ")";
        $db->conn->Execute($insertStateSql );
    }
    

    function databaseTimeStampQuotaUpdate($db)
    {
        $currentQuota = getProperty($db, "TSA_CURRENT_QUOTA");
        
        if($currentQuota !== "")
        {
            $currentQuota++;
            
            $updateStateSql = "UPDATE PROPIEDADES set VALOR = '" . $currentQuota . "' WHERE NOMBRE LIKE 'TSA_CURRENT_QUOTA'";
            $db->conn->Execute($updateStateSql);
            
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
        else if (strpos($response, 'orfeo update error') !== false)
        {
            $currentResponse =  "No es posible actualizar el documento en los repostiorios de información de orfeo";
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
			$rs = $db->conn->Execute($selectSql);
            
            if(!$rs->EOF)
            {
				if (trim ($rs->fields[0]) !== '')
					return $rs->fields[0];
				else
					return $rs->fields['VALOR'];
            }
            return "";
        }
        catch (Exception $e)
        {
			return "";
        }
    }
    
	function sendToAres($db, $file)
	{

		$aresWsdl = getProperty($db, "ARES_WSDL") . "/ARES_Server-ARES_Server/WebServiceIntegrator?wsdl" ;
		
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
