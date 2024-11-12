var signMonitorLauncher;
var filePath;
var indexFile; 
var flagRetry;

$(document).ready(function ()
{
    signMonitorLauncher = new SignMonitorLauncher()
        .defineWelcomeAction(monitorWelcomeAction)
        .defineFailAction(monitorFailureAction)
        .schemePorts([25, 65097])
        .launchSignMonitor();

    setTimeout(function ()
    {
        if (signMonitorLauncher === undefined || !signMonitorLauncher.isListening())
        {
            $('#eLogicSetup').show();
            $('#eLogicSearch').hide();
        }
    }, 30000);
});

function monitorWelcomeAction()
{
    $('#eLogicSearch').hide();
    signMonitorLauncher.signerConfigurator.remoteTokenTabActive = false;
    signMonitorLauncher.signerConfigurator.tokenTabActive = true;
    signMonitorLauncher.signerConfigurator.winstoreTabActive = false;
    signMonitorLauncher.signerConfigurator.p12TabActive = false;
	
	if (globalTimestampFlag === "true")
		signMonitorLauncher.signerConfigurator.timestamp = tsa;
	
	flagRetry = true;
    hashProcess(globalTimeStamp, globalFile, globalUser, globalIdentification, globalTimestampFlag, globalVisible, globalLowerLeftX, globalLowerLeftY, globalUpperRightX, globalUpperRightY, globalQrCodeSize, globalQrCodeVisibility, globalPage, globalPlaceholder, qrPage, absoluteX, absoluteY, absoluteSize, renderingMode, description, qrBackgroundImageFlag, qrBackgroundImageOffset, backgroundSignImage, leftSignImage);
}

function monitorDoneAction()
{
    $('#processingState').show();
    $('#hashState').hide();
    $('#signState').hide();
    $('#verifyState').show();

    var response = signMonitorLauncher.getMonitorResponse();
	postSign(response,  globalTimeStamp, filePath, indexFile);
}

function monitorFailureAction()
{
    failAlertProcess("Firma digital cancelada por el usuario", globalTimeStamp, globalFile, "Procesos finalizado", "Cancel action", "Cancel action", false, true);
}

function postSign (signature, currentTimeStamp, currentFile, index, currentRadicado)
{
    var request = $.ajax({
        url: "postSign.php",
        type: "post",
        data: { 'signature': signature, 'timeStamp' : currentTimeStamp, 'file' : currentFile, 'idDocumentSettled' : currentRadicado, 'signerCommonName' :globalUser , 'timestampFlag' :globalTimestampFlag}
    });

    request.done(function (response)
    {
		if (response.indexOf("Firma digital invalida. No se puede verificar el archivo") !== -1 && flagRetry === true) {
			flagRetry = false;
			hashProcess (globalTimeStamp, globalFile, globalUser, globalIdentification, globalTimestampFlag, globalVisible, globalLowerLeftX, globalLowerLeftY, globalUpperRightX, globalUpperRightY, globalQrCodeSize, globalQrCodeVisibility, globalPage, globalPlaceholder, qrPage, absoluteX, absoluteY, absoluteSize, renderingMode, description, qrBackgroundImageFlag, qrBackgroundImageOffset, backgroundSignImage, leftSignImage);
		}
		var arrayResponse = response.split(";");
		var i=1;
		if (index >= 0){
			i = index;
		}
		for ( var j=0; j< (arrayResponse.length-1);j++) {
			var element = arrayResponse[j];
			if(element.indexOf("Firma digital generada exitosamente") === -1 ) {
				var idDiv = "rowFile" + String(i);
				var container = document.getElementById(idDiv);
				var content = container.innerHTML;
				container.innerHTML= content;
				$('#processingState').hide();
				$('#successState').hide();
				$('#errorState').show();
				$('#signError').hide();
				$('#retrySign').show();
				$('#statusFile').show();
				
				$('#signErrorText').text(response);
				$('#responseText').text(response);
				$('div#fail' + i).show();
				$('div#ok' + i).hide();
				$('div#fail' + i + ' a[data-toggle="tooltip"]').tooltip({
					title: "<div id='signErText1' class='alert alert-danger' role='alert'>" + element +"</div>", html: true, 
				});
					
			} else {
				$('#processingState').hide();
				$('#successState').show();
				$('#errorState').hide();
				$('#signError').hide();
				$('#retrySign').hide();
				$('#statusFile').show();
				$('div#fail' + i).hide();
				$('div#ok' + i).show();            
			}
			i=i+1;
		}
    });

    request.fail(function (jqXHR, textStatus, errorThrown)
    {
        failAlertProcess("Se presentaron errores al momento de calcular la firma digital", currentTimeStamp, currentFile, textStatus, jqXHR, errorThrown, false, true);
    });
}

function cancelSignProcess (currentTimeStamp, currentFile)
{
    var requestCancel = $.ajax ({
        url: "cancelProcess.php",
        type: "post",
        data: { 'timeStamp' : currentTimeStamp, 'file' : currentFile }
    });

    requestCancel.fail(function (jqXHR, textStatus, errorThrown)
    {
        failAlertProcess("Se presentaron problemas al momento de restaurar el estado de los archivos PDF", currentTimeStamp, currentFile, textStatus, jqXHR, errorThrown, true, false);
    });
}

function hashProcess(currentTimeStamp, currentFile, currentUser, currentIdentification, currentTimestampFlag, currentVisible, currentLowerLeftX, currentLowerLeftY, currentUpperRightX, currentUpperRightY, currentQrCodeSize, currentQrCodeVisibility, currentPage, currentPlaceholder, qrPage, absoluteX, absoluteY, absoluteSize, renderingMode, description, qrBackgroundImageFlag,  qrBackgroundImageOffset, backgroundSignImage, leftSignImage, index)
{
    $('#processingState').show();
    $('#hashState').show();
    $('#signState').hide();
    $('#verifyState').hide();
	$('#statusFile').hide();

    $('#successState').hide();
    $('#errorState').hide();
    $('#signError').hide();
    $('#retrySign').hide();

    var d = new Date();

    var requestHashProcess = $.ajax({
        url: "hashProcess.php",
        type: "post",
        cache: false,
        data: { 'timeStamp': currentTimeStamp, 'file': currentFile, 'usua_identif': currentIdentification, 'usua_nomb_compl': currentUser, 'cacheControl': d.getTime(), 'timestampFlag': currentTimestampFlag, 'visible': currentVisible, 'lowerLeftX': currentLowerLeftX, 'lowerLeftY': currentLowerLeftY, 'upperRightX': currentUpperRightX, 'upperRightY': currentUpperRightY, 'qrCodeSize': currentQrCodeSize, 'qrCodeVisibility': currentQrCodeVisibility, 'page': currentPage, 'placeholder': currentPlaceholder, 'qrPage': qrPage, 'absoluteX': absoluteX, 'absoluteY': absoluteY, 'absoluteSize': absoluteSize , 'renderingMode': renderingMode, 'description': description, 'qrBackgroundImageFlag': qrBackgroundImageFlag, 'qrBackgroundImageOffset': qrBackgroundImageOffset, 'backgroundSignImage': backgroundSignImage, 'leftSignImage': leftSignImage }

    });

    requestHashProcess.done(function (response)
    {
        $('#processingState').show();
        $('#hashState').hide();
        $('#signState').show();
        $('#verifyState').hide();
		filePath = currentFile;
		indexFile = index;


        signMonitorLauncher.addRequestParams({ 'hash': response }).signPdfHash(monitorDoneAction);
    });

    requestHashProcess.fail(function (jqXHR, textStatus, errorThrown)
    {
        failAlertProcess("Se presentaron problemas al momento de preparar el documento para firma", currentTimeStamp, currentFile, textStatus, jqXHR, errorThrown, false, true);
    });
}

function failAlertProcess(currentAlert, currentTimeStamp, currentFile, textStatus, jqXHR, errorThrown, appendText, cancelProcess)
{
    $('#retrySign').show();
    $('#signError').show();

    var signErrorTextComponent = $('#signErrorText');
    if (appendText)
    {
        signErrorTextComponent.append("<br/><br/>" + currentAlert + " : " + textStatus);
    }
    else
    {
        signErrorTextComponent.text(currentAlert + " : " + textStatus);
    }

    signErrorTextComponent.append('<br/><span class="font-weight-bold">Respuesta:</span> ' + jqXHR);
    signErrorTextComponent.append('<br/><span class="font-weight-bold">Error:</span> ' + errorThrown);

    $('#processingState').hide();
    $('#successState').hide();
    $('#errorState').show();

    if (cancelProcess)
    {
        cancelSignProcess (currentTimeStamp, currentFile);
    }
}