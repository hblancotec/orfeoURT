<?php

    session_start();

	/*
    if (count($_SESSION) == 0)
    {
        die(include "../../sinacceso.php");
        exit();
    }*/
/*
    if (!$ruta_raiz)
        $ruta_raiz = "../";

    require $ruta_raiz.'config.php';
    require $ruta_raiz.'include/db/ConnectionHandler.php';
*/
    //
    // Data base connection manager
    //$db = new ConnectionHandler($ruta_raiz);

	$file = $_REQUEST['file'];
	$signerId = $_REQUEST["signerId"];
    $signerCommonName =  $_REQUEST["signerCommonName"];
	
	$timestampFlag =  isset($_REQUEST['timestampFlag']) ? $_REQUEST["timestampFlag"] : "false";
	$visible = isset($_REQUEST['visible']) ? $_REQUEST["visible"] : " ";
	$lowerLeftX = isset($_REQUEST['lowerLeftX']) ? $_REQUEST["lowerLeftX"] : " ";
	$lowerLeftY = isset($_REQUEST['lowerLeftY']) ? $_REQUEST["lowerLeftY"] : " ";
	$upperRightX = isset($_REQUEST['upperRightX']) ? $_REQUEST["upperRightX"] : " ";
	$upperRightY = isset($_REQUEST['upperRightY']) ? $_REQUEST["upperRightY"] : " ";
	$qrCodeSize = isset($_REQUEST['qrCodeSize']) ? $_REQUEST["qrCodeSize"] : " ";
	$qrCodeVisibility = isset($_REQUEST['qrCodeVisibility']) ? $_REQUEST["qrCodeVisibility"] : " ";
	$page = isset($_REQUEST['page']) ? $_REQUEST["page"] : " ";
    $placeholder = isset($_REQUEST['placeholder']) ? $_REQUEST["placeholder"] : " ";
    $qrPage = isset($_REQUEST['qrPage']) ? $_REQUEST["qrPage"] : " ";
    $absoluteX = isset($_REQUEST['absoluteX']) ? $_REQUEST["absoluteX"] : " ";
    $absoluteY = isset($_REQUEST['absoluteY']) ? $_REQUEST["absoluteY"] : " ";
    $absoluteSize = isset($_REQUEST['absoluteSize']) ? $_REQUEST["absoluteSize"] : " ";
    $renderingMode = isset($_REQUEST['renderingMode']) ? $_REQUEST["renderingMode"] : " ";
    $description = isset($_REQUEST['description']) ? $_REQUEST["description"] : " ";
    $qrBackgroundImageFlag = isset($_REQUEST['qrBackgroundImageFlag']) ? $_REQUEST["qrBackgroundImageFlag"] : " ";
    $qrBackgroundImageOffset = isset($_REQUEST['qrBackgroundImageOffset']) ? $_REQUEST["qrBackgroundImageOffset"] : " ";
	$timeStampParameters = "";
	//
	// Windows server support
	$file = str_replace ("\\" , "/", $file);

    //
    // Time stamp parameters consulting and ciphering
    if ($timestampFlag === "true")
    {
        $timeStampParameters = getTSAServiceCrypto($db);
        if ($timeStampParameters === "")
        {
            $timestampFlag = "false";
        }
    }

    //---------------------------------------
    // Helper functions
    //---------------------------------------

    function getTSAServiceCrypto($db)
    {
        $timeStampParameters = "";
        try
        {
            $use = getProperty($db, "TSA_USE");                 // i.e. true or false

            if (string2boolean($use))
            {
                $startPattern = "START:";
                $endPattern = ":END";

                $absoluteBasePath = str_replace("orfeo-server-logic", "", dirname(__FILE__));
                $basePath = $absoluteBasePath . 'orfeo-server/';
                $currentSignCommand = '"' .$basePath . 'orfeo-server.jar"';

                $currentCommand = "tsa";
                $url = getProperty($db, "TSA_URL");             // i.e. http://tsa.gse.co
                $port = getProperty($db, "TSA_PORT");           // i.e. 80
                $user = getProperty($db, "TSA_USER");           // i.e. 8301259969
                $password = getProperty($db, "TSA_PASSWORD");   // i.e. UGq6LTUmZ9179uwffLMV
                $path = getProperty($db, "TSA_PATH");           // i.e. "" (Empty)
                $oid = getProperty($db, "TSA_OID");             // i.e. 1.3.6.1.4.1.31136.2.3.3.1

                $commandResult = shell_exec('java -jar ' . $currentSignCommand . ' ' .  $currentCommand . ' "' . $url . '" "' . $port . '" "' . $user . '" "' . $password . '" "' . $path . '" "' . $oid . '"');

                $startPosition = strpos($commandResult, $startPattern);
                $endPosition = strpos($commandResult, $endPattern);

                if ($startPosition !== false && $endPosition !== false)
                {
                    $timeStampParameters = substr ($commandResult, $startPosition + strlen($startPattern), $endPosition - $startPosition - strlen($endPattern) -2 );
                }
            }

            return $timeStampParameters;
        }
        catch (Exception $e)
        {
            return "";
        }
    }

    function string2boolean ($value)
    {
        try
        {
            return trim(strtolower($value)) === 'true';
        }
        catch (Exception $e)
        {
            return false;
        }
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
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Proceso de generaci&oacute;n de firma digital</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link href="css/bootstrap.min.css" rel="stylesheet"/>
		<link href="css/bootstrap-grid.min.css" rel="stylesheet"/>
		<link href="css/bootstrap-reboot.min.css" rel="stylesheet"/>
		<link href="css/font-awesome.min.css" rel="stylesheet"/>
		
		<script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="js/popper.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="js/sc-elogicmonitor-api.js"></script>

        <script type="text/javascript">

            //
            // TSA ciphered access parameters
            var tsa = '<?=$timeStampParameters?>';

            //
            // This variables will be used in the javascript file named signature.js
			var globalTimeStamp = '<?=time()?>'; 
			var globalFile = '<?=$file?>';
			var globalUser = '<?=$signerCommonName?>';
			var globalIdentification = '<?=$signerId?>';
			
			var files = globalFile.split("|");

			
			var globalTimestampFlag = '<?=$timestampFlag?>';
			var globalVisible = '<?=$visible?>';
			var globalLowerLeftX = '<?=$lowerLeftX?>';
			var globalLowerLeftY = '<?=$lowerLeftY?>';
			var globalUpperRightX = '<?=$upperRightX?>';
			var globalUpperRightY = '<?=$upperRightY?>';
			var globalQrCodeSize = '<?=$qrCodeSize?>';
			var globalQrCodeVisibility = '<?=$qrCodeVisibility?>';
			var globalPage = '<?=$page?>';
            var globalPlaceholder = '<?=$placeholder?>';
            var qrPage = '<?=$qrPage?>';
            var absoluteX = '<?=$absoluteX?>';
            var absoluteY = '<?=$absoluteY?>';
            var absoluteSize = '<?=$absoluteSize?>';
            var renderingMode = '<?=$renderingMode?>';
            var description = '<?=$description?>';
            var qrBackgroundImageFlag = '<?=$qrBackgroundImageFlag?>';
            var qrBackgroundImageOffset = '<?=$qrBackgroundImageOffset?>';
	
			console.log(globalTimeStamp);
            console.log(globalFile);
            console.log(globalUser);
            console.log(globalIdentification);

			console.log(globalTimestampFlag);
            console.log(globalVisible);
            console.log(globalLowerLeftX);
            console.log(globalLowerLeftY);
            console.log(globalUpperRightX);
            console.log(globalUpperRightY);
            console.log(globalQrCodeSize);
            console.log(globalQrCodeVisibility);
            console.log(globalPage);
            console.log(globalPlaceholder);
            //console.log(qrPage);
            //console.log(absoluteX);
            //console.log(absoluteY);
            //console.log(absoluteSize);
            console.log(renderingMode);
            console.log(description);
            console.log(qrBackgroundImageFlag);
            console.log(qrBackgroundImageOffset);
        </script>


		<style>
			.tooltip .tooltip-arrow{
				top:50%;
				left:0;
				margin-top:-5px;
				border-top:5px solid transparent;
				border-bottom:5px solid transparent;
				border-right:5px solid #000;
			}
			.tooltip .tooltip-inner{
				padding:1px;
				color:#fff;
				text-align:center;
				background-color:white;
				-webkit-border-radius:4px;
				-moz-border-radius:4px;
				border-radius:4px
			}
			.tooltip-arrow{
				position:absolute;
				width:0;
				height:0
			}
		</style>


</head>
	
	<body>
	
		<div class="container" style="margin-top: 2rem">
			<div class="row justify-content-md-center">
				<div class="card text-center" style="width: 30rem;">
					
					<div class="card-header">
						<?=$signerCommonName?>
					</div>
  
					<div class="card-body">
						
						<img src="img/signer.png" width="128" height="128" alt="Card image cap">
						<h4 class="card-title"><?=$signerId?></h4>
						<a id="retrySign" href="javascript:hashProcess (globalTimeStamp, globalFile, globalUser, globalIdentification, globalTimestampFlag, globalVisible, globalLowerLeftX, globalLowerLeftY, globalUpperRightX, globalUpperRightY, globalQrCodeSize, globalQrCodeVisibility, globalPage, globalPlaceholder, qrPage, absoluteX, absoluteY, absoluteSize, renderingMode, description, qrBackgroundImageFlag, qrBackgroundImageOffset);" class="btn btn-primary" style="display: none"><i class="fa fa-pencil fa-fw"></i>  <i class="fa fa-user fa-fw"></i> Reintentar firma digital</a>
						
					</div>
					
					<div class="card-footer text-muted">

                        <div id ="processingState" class=" row justify-content-md-center">
                            <div class="container">
                                <div class="row align-items-center">

                                    <div class="col-md-3">
                                        <i class="fa fa-cog fa-spin fa-4x fa-fw"></i>
                                        <span class="sr-only">Procesando...</span>
                                    </div>

                                    <div id="hashState" class="col-md-9">
                                        <i class="fa fa-files-o"></i> &nbsp; Preparando documentos
                                    </div>

                                    <div id="signState" class="col-md-9" style="display: none">
                                        <i class="fa fa-pencil-square-o"></i> &nbsp; Esperando firma digital
                                    </div>

                                    <div id="verifyState" class="col-md-9" style="display: none">
                                        <i class="fa fa-check-square-o"></i> &nbsp; Verificando firma digital
                                    </div>

                                </div>
                             </div>
                        </div>

                        <div id ="successState" class="row justify-content-md-center" style="display: none">
                            <div class="container">
                                <div class="row align-items-center">

                                    <div class="col-md-3">
                                        <i class="fa fa-check-square-o fa-4x fa-fw"></i>
                                        <span class="sr-only">Exito...</span>
                                    </div>

                                    <div class="col-md-9">
                                        <i class="fa fa-file-text"></i> &nbsp; Firma digital generada exitosamente
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id ="errorState" class=" row justify-content-md-center" style="display: none">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <i class="fa fa-exclamation-triangle fa-4x fa-fw"></i>
                                        <span class="sr-only">Falla...</span>
                                    </div>

                                    <div class="col-md-9">
                                        <i class="fa fa-file-powerpoint-o"></i> &nbsp; Proceso de firma digital no exitoso
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
			
			<br/>
			<div id="signError" class="row justify-content-md-center" style="display: none">
				<div class="card card-outline-danger text-center" style="width: 30rem;">
					<div class="card-block">
						<blockquote class="card-blockquote">
							
							<div id="signErrorText" class="alert alert-danger" role="alert">
								&nbsp;
							</div>

						</blockquote>
					</div>
				</div>
			</div>
			
			<div id="eLogicSetup" class="row justify-content-md-center" style="display: none">
				<div class="card card-outline-danger text-center" style="width: 30rem;">
					<div class="card-block">
						<blockquote class="card-blockquote">
							<div class="alert alert-warning" role="alert">
								Servicio de firma digital no encontrado.<br/>
								Por favor descargue e instale el agente de firma haciendo <br/> <a href="https://elogicmonitor.software-colombia.com" target="_blank">clic aqu&iacute; <i class="fa fa-download"></i></a><br/>
								<small>Nota: para la instalaci&oacute;n es necesario contar con permisos de administrador sobre el equipo</small>
							</div>
						</blockquote>
					</div>
				</div>
			</div>
			
			<div id="eLogicSearch" class="row justify-content-md-center">		
				<div class="card card-outline-danger text-center" style="width: 30rem;">
					<div class="card-block">
						<blockquote class="card-blockquote">
							<div class=" alert alert-info" role="alert">
								Buscando componente de firma digital <i class="fa fa-search" aria-hidden="true"></i>
							</div>
						</blockquote>
					</div>
				</div>
			</div>
			
			<br/>
			<div class="row justify-content-md-center" id="statusFile" style="display: none">

                <div class="card card-outline-danger text-center" style="width: 30rem;">
                    <div class="card-body">
						<input type="hidden" name="responseText" id="responseText"/>

                        <?php
							$i = 0;
                            foreach (explode('|', $file) as $currentToken)
                            {
                                $indexSlash = strrpos ( $currentToken , "/" );
                                $indexPdf = strrpos ( $currentToken , ".pdf" );
                                $indexBodega = strrpos ( $currentToken , "/bodega/" );
                                $documentId = substr($currentToken, $indexSlash + 1, ($indexPdf - $indexSlash) - 1);

                                if ($indexBodega !== false)
                                {
                                    $pathDownload = substr($currentToken, $indexBodega);
                                }
                                else
                                {
                                    $indexBodega = strrpos ( $currentToken , "/orfeo-server-logic/" );
                                    $pathDownload = substr($currentToken, $indexBodega);
                                }
								$i=$i+1;
				
                        ?>

                                <div class="row align-items-center" id="rowFile<?=$i?>">
                                    <div class="col-md-1">
                                        <i class="fa fa-file-pdf-o fa-2x fa-fw"></i>
                                    </div>
                                    <div class="col-md-5">
                                        <?=$documentId?>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?=  '../' . $pathDownload . '?time=' . time() ?>" target="_blank" class="btn btn-outline-info btn-sm"> <i class="fa fa-download"></i> Descargar</a>
                                    </div>
									<div class="col-md-1" id="ok<?=$i?>" style="display: none">
										<a class="fa fa-check-circle" style="color:green;"></a>
									</div>
									<div class="col-md-3" id="fail<?=$i?>" style="display: none">
										<a style="color:red;" data-toggle="tooltip" data-placement="right" class="btn btn-outline-danger btn-sm" href="javascript:hashProcess (globalTimeStamp, files[<?=$i-1?>], globalUser, globalIdentification, globalTimestampFlag, globalVisible, globalLowerLeftX, globalLowerLeftY, globalUpperRightX, globalUpperRightY, globalQrCodeSize, globalQrCodeVisibility, globalPage, globalPlaceholder,qrPage, absoluteX, absoluteY, absoluteSize, renderingMode, description, qrBackgroundImageFlag, qrBackgroundImageOffset, <?=$i?>);">
										<i class="fa fa-exclamation-triangle" style="color:red;"></i> Reintentar</a>
									</div>
                                </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>

			</div>
		</div>
		
	</body>
	<script type="text/javascript" src="js/signature.js"></script>
</html>