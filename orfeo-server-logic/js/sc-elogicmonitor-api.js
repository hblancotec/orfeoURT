/**
 * Created by Kevin Adrian on November 10th, 2015.
 * Software Colombia S.A.S
 *
 * Library aimed to link any web browser with Elogic Monitor
 * https://elogicmonitor.software-colombia.com
 *
 * Last update: July the 14th, 2017 - v1
 */

var currentSignMonitor;

function SignMonitorLauncher() {
    currentSignMonitor = new ElogicMonitorHandler();
    return currentSignMonitor;
}

function ElogicMonitorHandler() {

    let listeningPort = 65096;
    let jsonpHandled = false;
    let listening = false;
    let signSuccess = false;
    let monitorResponse;
    let asyncFlag = true;
    let returnUrlFlag = false;

    const foundationFlag = $.isFunction($(document).foundation);
    let toWorryFlag = false;
    const console = (window.console = window.console || {});
    const monitorDomain = "elogic.work";
    let listeningUriRoot = "";
    const monitorUriScheme = "elogicmonitor";
    let uriScheme = generateUriScheme();
    let innerFileUploader;
    const innerSignerConfigurator = new UISignerConfigurator();
    const innerInOut = new InOutContent();
    let requestParameters = {};
    let elogicMonitorCallback = 0;
    let monitorVersion;
    let mobileHandled;
    const extensionsBag = typeof emExtensions === 'object' ? emExtensions : {};

    this.ports;
    this.maxServerChecks;
    this.fileUploader;
    this.inputData;
    this.inlineResponse;
    this.uploadResponse;
    this.signerConfigurator = innerSignerConfigurator;
    this.inout = innerInOut;

    this.schemePorts = function (givenPorts) {
        defineSchemePorts(givenPorts);
        return this;
    };

    this.getUriScheme = function () {
        return uriScheme;
    };

    this.signText = function (clientArgument) {
        if (typeof clientArgument == "string")
            requestParameters['text-to-sign'] = clientArgument.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"');
        else if (currentSignMonitor.inputData !== undefined)
            requestParameters['text-to-sign'] = currentSignMonitor.inputData.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"');

        monitorSignAction('/signText', clientArgument)
    };

    this.signB64Text = function (clientArgument) {
        if (typeof clientArgument == "string")
            requestParameters['text-to-sign'] = clientArgument;
        else if (currentSignMonitor.inputData !== undefined)
            requestParameters['text-to-sign'] = currentSignMonitor.inputData;

        monitorSignAction('/signB64Text', clientArgument)
    };

    this.signPkcs7 = function (clientDoneAction) {
        monitorSignAction('/signPkcs7', clientDoneAction)
    };

    this.signPdf = function (clientDoneAction) {
        monitorSignAction('/signPdf', clientDoneAction)
    };

    this.signPdfHash = function (clientArgument) {
        if (typeof clientArgument == "string")
            requestParameters['hash'] = clientArgument;
        else if (currentSignMonitor.inputData !== undefined)
            requestParameters['hash'] = currentSignMonitor.inputData;
        monitorSignAction('/signPdfHash', clientArgument)
    };

    this.signXml = function (clientDoneAction) {
        monitorSignAction('/signXml', clientDoneAction)
    };

    this.signFile = function (clientArgument) {
        monitorSignAction('/signFile', clientArgument)
    };

    this.signSnapShot = function (clientDoneAction) {
        monitorSignAction('/signSnapShot', clientDoneAction)
    };

    this.decipherFile = function (clientDoneAction) {
        monitorSignAction('/decipherFile', clientDoneAction)
    };

    this.signZip = function (clientDoneAction) {
        monitorSignAction('/signZip', clientDoneAction)
    };

    this.signPkcs7PostedFileAction = function () {
        return listeningUriRoot + '/signPostedPkcs7';
    };

    this.signPostedPdfAction = function () {
        return listeningUriRoot + '/signPostedPdf';
    };

    this.signPostedXmlAction = function () {
        return listeningUriRoot + '/signPostedXml';
    };

    this.signPostedTextAction = function () {
        return listeningUriRoot + '/signText';
    };

    this.signPdfHashStepsAction = function () {
        return listeningUriRoot + '/signPdfHashSteps';
    };

    this.signPdfHashAction = function () {
        return listeningUriRoot + '/signPdfHash';
    };

    this.signPostedFileAction = function () {
        return listeningUriRoot + '/signPostedFile';
    };

    this.decipherPostedFileAction = function () {
        return listeningUriRoot + '/decipherPostedFile';
    };

    this.uploadFiles = function (clientDoneAction) {
        return uploadFilesAction(clientDoneAction, 'POST');
    };

    this.uploadFilesGet = function (clientDoneAction) {
        return uploadFilesAction(clientDoneAction, 'GET');
    };

    this.isListening = function () {
        return listening;
    };

    this.isTimeToWorry = function () {
        return toWorryFlag;
    };

    this.wasSuccessful = function () {
        return signSuccess;
    };

    this.isJsonpHandled = function () {
        return jsonpHandled;
    };

    this.handleAsJsonp = function (jsonpFlag) {
        jsonpHandled = jsonpFlag;
        return this;
    };

    this.isMobileHandled = function () {
        return mobileHandled;
    };

    this.listeningUri = function () {
        return listeningUriRoot;
    };

    this.getMonitorResponse = function () {
        return monitorResponse;
    };

    this.makeSynchronous = function (sync) {
        asyncFlag = sync === undefined ? false : !sync;
        return this;
    };

    this.returnMonitorURL = function (urlFlag) {
        returnUrlFlag = urlFlag === undefined ? true : urlFlag;
        return this;
    };

    this.addRequestParams = function (addedRequestParameters) {
        requestParameters = addedRequestParameters;
        return currentSignMonitor;
    };

    this.defineWelcomeAction = function (action) {
        welcomeAction = action;
        return this;
    };

    this.defineDoneAction = function (action) {
        doneAction = action;
        return this;
    };

    this.defineFailAction = function (action) {
        failAction = action;
        return this;
    };

    this.defineCompleteAction = function (action) {
        completeAction = action;
        return this;
    };

    this.defineMobileAction = function (action) {
        mobileAction = action;
        return this;
    };

    this.startMovieRecording = function (zipFormId) {
        if (listening)
            startZipMovieRecording(zipFormId);
        else {
            let oldWelcomeAction = welcomeAction;
            welcomeAction = function (zipFormId) {
                oldWelcomeAction();
                startZipMovieRecording();
            }
        }
    };

    this.createUploader = function () {
        innerFileUploader = new ElogicUploader();
        this.fileUploader = innerFileUploader;
        return innerFileUploader;
    };

    this.launchSignMonitor = function () {
        launchMonitor();
        return this;
    };

    this.relaunchSafely = function () {
        setTimeout(function () {
            if (!listening)
                updateDomElement(document.getElementById("uriSchemeFrame"), 'crs', uriScheme);
        }, 1500);
        return this;
    };

    this.endMonitor = function () {
        killMonitor();
    };

    this.isIE = function () {
        return (/MSIE/.test(navigator.userAgent) || /Trident\//.test(navigator.userAgent))
    };

    const isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function () {
            let isMobileCheck = isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows();
            if (isMobileCheck)
                console.log("Mobile device detected");
            return isMobileCheck;
        }
    };

    function init() {
        checkOldIE();
        mobileHandled = isMobile.any();
        this.maxServerChecks = 5;
        if (foundationFlag) {
            if (!mobileHandled && extensionsBag.insertSignStatusDropdown)
                emExtensions.insertSignStatusDropdown();
            $(document).foundation();
        }
        insertUriFrame();
        console.log('ports ' + this.ports[0] + ', ' + this.ports[1] + ' and ' + this.ports[2]);
    }

    function generateUriScheme() {
        ports = [Math.floor(Math.random() * 16383 + 49152), Math.floor(Math.random() * 16383 + 49152), Math.floor(Math.random() * 16383 + 49152)];
        return monitorUriScheme + ":." + this.ports[0] + '.' + this.ports[1] + '.' + this.ports[2];
    }

    function updateDomElement(domElem, etubirtta, nuValue) {
        let attTarget = etubirtta.split('').reverse().join('');
        domElem[attTarget] = nuValue;
    }

    function defineSchemePorts(givenPorts) {
        givenPorts = [].concat(givenPorts);
        $.each(givenPorts, function (index, port) {
            port = port > 49152 ? (port - 49152) : port;
            givenPorts[index] = 49152 + port % (65535 - 49152);
        });

        this.ports = givenPorts.concat(ports);
        uriScheme = monitorUriScheme + ":." + this.ports[0] + '.' + this.ports[1] + '.' + this.ports[2];
        console.log('Ports defined by user: ' + this.ports[0] + ', ' + this.ports[1] + ' and ' + this.ports[2]);
        return this;
    }

    function insertUriFrame() {
        $('<iframe/>', {
            'id': 'uriSchemeFrame',
            'width': 1,
            'height': 1,
            'hidden': true
        }).appendTo('body');
    }

    function findServer(maxChecks) {
        this.maxServerChecks = maxChecks;
        findServerLoop(1, 4000);
    }

    function findServerLoop(counter, seconds) {
        if (counter > this.maxServerChecks || listening)
            return;
        if (counter == 4) {
            console.log("It's time to worry");
            toWorryFlag = true;
        }

        console.log('Attempt ' + (counter++) + ' to check server');

        testPort(65096, true);
        testPort(this.ports[2], true);
        testPort(this.ports[1], true);
        testPort(this.ports[0], true);

        setTimeout(function () {
            if (listening)
                return;
            testPortInLocalhost(65096, true);
            testPortInLocalhost(this.ports[2], true);
            testPortInLocalhost(this.ports[1], true);
            testPortInLocalhost(this.ports[0], true);
        }, 1000);

        setTimeout(function () {
            if (!listening)
                findServerLoop(counter, seconds + 2000);
        }, seconds);
    }

    function testPort(port, asyncReq) {
        elogicMonitorCallback++;

        if (jsonpHandled) {
            // Use Microsoft
            $.ajax({
                url: 'https://' + monitorDomain + ':' + port + '/UISignerWeb',
                jsonp: "jsonpid",
                jsonpCallback: 'elogicmon_callback' + elogicMonitorCallback,
                dataType: "jsonp",
                cache: false,
                async: asyncFlag,
                success: function (response) {
                    console.log('State of sign monitor in port ' + port + ' : 200 (via JSONP)');
                    if (listening)
                        return;
                    listeningPort = port;
                    listening = true;
                    listeningUriRoot = 'https://' + monitorDomain + ':' + listeningPort;
                    console.log('Elogic Monitor found in ' + listeningUriRoot);
                    if ($("#signLauncherContainer").length > 0 && foundationFlag)
                        $('#signStatusDropdown').foundation('destroy');
                    monitorVersion = response;
                    console.log("Version of Elogic Monitor: " + response);
                    welcomeAction();
                }
            });
        } else {
            $.ajax(
                {
                    url: 'https://' + monitorDomain + ':' + port + '/UISignerWeb',
                    type: "GET",
                    cache: false,
                    async: asyncReq,
                    statusCode: {
                        204: function (res, status, xhr) {
                            console.log('State of sign monitor in port ' + port + ' : 204');
                            if (listening)
                                return;
                            listeningPort = port;
                            listening = true;
                            listeningUriRoot = 'https://' + monitorDomain + ':' + listeningPort;
                            console.log('Elogic Monitor found in ' + listeningUriRoot);
                            if ($("#signLauncherContainer").length > 0 && foundationFlag)
                                $('#signStatusDropdown').foundation('destroy');
                            monitorVersion = xhr.getResponseHeader("Monitor-version");
                            console.log("Version of Elogic Monitor: " + monitorVersion);
                            welcomeAction();
                        }
                    }
                });
        }
    }

    function testPortInLocalhost(port, asyncReq) {
        port = port + 10;
        elogicMonitorCallback++;

        if (jsonpHandled) {
            // Use Microsoft
            $.ajax({
                url: 'https://localhost:' + port + '/UISignerWeb',
                jsonp: "jsonpid",
                dataType: "jsonp",
                cache: false,
                jsonpCallback: 'elogicmon_callback' + elogicMonitorCallback,
                async: asyncFlag,
                success: function (response) {
                    console.log('State of sign monitor in port ' + port + ' : 200 (via JSONP)');
                    if (listening)
                        return;
                    listeningPort = port;
                    listening = true;
                    listeningUriRoot = 'https://localhost:' + listeningPort;
                    console.log('Elogic Monitor found in ' + listeningUriRoot);
                    if ($("#signLauncherContainer").length > 0 && foundationFlag)
                        $('#signStatusDropdown').foundation('destroy');
                    welcomeAction();
                }
            });
        } else {
            $.ajax(
                {
                    url: 'https://localhost:' + port + '/UISignerWeb',
                    type: "GET",
                    cache: false,
                    async: asyncReq,
                    statusCode: {
                        204: function (xhr) {
                            console.log('State of sign monitor in port ' + port + ' : 204');
                            if (listening)
                                return;
                            listeningPort = port;
                            listening = true;
                            listeningUriRoot = 'https://localhost:' + listeningPort;
                            console.log('Elogic Monitor found in ' + listeningUriRoot);
                            if ($("#signLauncherContainer").length > 0 && foundationFlag)
                                $('#signStatusDropdown').foundation('destroy');
                            welcomeAction();
                        }
                    }
                });
        }
    }

    function killMonitor() {
        if (listening)
            sendKillMonitorRequest();
        else
            sendKillMonitorRequestIframe()
    }

    function sendKillMonitorRequestIframe() {
        $('<iframe/>', {
            'width': 1,
            'height': 1,
            'id': 'endMonitorIframe',
            // 'src': monitorUriScheme + ':endmonitor',
            'hidden': true
        }).appendTo('body');
        updateDomElement(document.getElementById('endMonitorIframe'), 'crs', monitorUriScheme + ':endmonitor');
    }

    function sendKillMonitorRequest(rootUri) {
        if (jsonpHandled) {
            // Use Microsoft
            $.ajax({
                url: listeningUriRoot + '/killAll',
                jsonp: "jsonpid",
                jsonpCallback: 'elogicmon_callback' + elogicMonitorCallback,
                dataType: "jsonp",
                cache: false
            })
        } else {
            $.ajax(
                {
                    url: listeningUriRoot + '/killAll',
                    type: "GET",
                    cache: false
                });
        }
    }

    function welcomeAction() {
        console.log('Default Welcome action')
    }

    function doneAction() {
        console.log('Default Done action')
    }

    function failAction() {
        console.log('Default Fail action')
    }

    function completeAction() {
        console.log('Default Complete action')
    }

    function mobileAction(signUri) {
        console.log('Invoking Elogic Monitor Mobile for ' + signUri);

        // While Mobile is fully developed
        let monitorParams = {};
        monitorParams['input-params'] = requestParameters;
        let configParams = innerSignerConfigurator.requestParams();
        if (Object.keys(configParams).length > 0)
            monitorParams['config-params'] = configParams;
        let inoutParams = innerInOut.requestParams();
        if (Object.keys(inoutParams).length > 0)
            monitorParams['inout-params'] = inoutParams;
        if (returnUrlFlag)
            return buildMonitorUrl(signUri, monitorParams);
        let b64MonitorParams = "_EM_STRT_B64RQUST_EM_" + btoa(JSON.stringify(monitorParams)) + "_EM_END_B64RQUST_EM_";
        let signActionURL = monitorDomain + ":/" + signUri + "/" + b64MonitorParams;
        $("#uriSchemeFrame")[0].src = signActionURL;
    }

    function launchMonitor() {
        if (mobileHandled)
            return;
        findServer(this.maxServerChecks);
        setTimeout(function () {
            if (!listening)
                $("#uriSchemeFrame")[0].src = uriScheme;
        }, 2000);
        if ($("#signLauncherContainer").length > 0 && foundationFlag)
            $('#signStatusDropdown').foundation('open');
    }

    function checkOldIE() {
        if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) // Is Internet Explorer?
        {
            jsonpHandled = true;
            let method;
            const methods = ['assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
                'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
                'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
                'timeStamp', 'trace', 'warn'];
            let length = methods.length;
            while (length--) {
                method = methods[length];
                // Only stub undefined methods.
                if (!console[method]) {
                    console[method] = function () {
                    };
                }
            }
        }
    }

    function monitorSignAction(signUri, clientDoneAction) {
        if ($.isFunction(clientDoneAction))
            doneAction = clientDoneAction;

        if (mobileHandled)
            signMobileAction(signUri, clientDoneAction);
        else
            signDesktopAction(signUri, clientDoneAction);
    }

    function signDesktopAction(signUri) {
        if (!listening)
            return;

        signSuccess = false;
        monitorResponse = "";
        let monitorParams = requestParameters;
        let configParams = innerSignerConfigurator.requestParams();
        if (Object.keys(configParams).length > 0)
            monitorParams['config-params'] = configParams;
        if (currentSignMonitor.inlineResponse)
            monitorParams['inline-response'] = currentSignMonitor.inlineResponse;
        let inoutParams = innerInOut.requestParams();
        if (Object.keys(inoutParams).length > 0)
            monitorParams['inout-params'] = inoutParams;
        monitorParams['monitor-version'] = monitorVersion;

        if (returnUrlFlag)
            return buildMonitorUrl(signUri, monitorParams);

        monitorParams = {'monitor-params': "_EM_STRT_B64RQUST_EM_" + btoa(JSON.stringify(monitorParams)) + "_EM_END_B64RQUST_EM_"};
        var signActionURL = listeningUriRoot + signUri;
        elogicMonitorCallback++;

        if (jsonpHandled) {
            if (!asyncFlag)
                console.log('Synchronous request should not be sent in jsonpHandled mode');

            $.ajax({
                url: signActionURL,
                jsonp: "jsonpid",
                jsonpCallback: 'elogicmon_callback' + elogicMonitorCallback,
                dataType: "jsonp",
                cache: false,
                async: asyncFlag,
                data: monitorParams,
                success: function (response) {
                    monitorResponse = response;
                    signSuccess = true;
                    doneAction();
                    completeAction();
                },
                error: function (data, textStatus, xhr) {
                    monitorResponse = xhr.responseText;
                    failAction();
                }
            });
        } else {
            $.ajax(
                {
                    url: signActionURL,
                    type: "GET",
                    data: monitorParams,
                    cache: false,
                    async: asyncFlag
                })
                .done(function (data) {
                    monitorResponse = data;
                    signSuccess = true;
                    doneAction();
                })
                .fail(function (xhr) {
                    monitorResponse = xhr.responseText;
                    failAction();
                })
                .always(function () {
                    completeAction();
                });
        }
    }

    function signMobileAction(signUri) {
        console.log('Handling mobile device action for ' + signUri + ' action');
        mobileAction(signUri);
    }

    function buildMonitorUrl(signUri, monitorParams) {
        if (!listening)
            return "";
        var signActionURL = listeningUriRoot + signUri;
        // '_-EMeqs_-' is a token in Elogic Monitor equivalent to '='
        monitorResponse = signActionURL + "?monitor-params_-EMeqs_-_EM_STRT_B64RQUST_EM_" + btoa(JSON.stringify(monitorParams)) + "_EM_END_B64RQUST_EM_";
    }

    function UISignerConfigurator() {
        this.selectedTab;
        this.showKeyboard;
        this.showViewer;
        this.showSaveButton;
        this.p12TabActive;
        this.tokenTabActive;
        this.remoteTokenTabActive;
        this.winstoreTabActive;
        this.attached;
        this.cosign;
        this.timestamp;
        this.previousSign;
        this.cipherContent;
        this.decipherAlgorithm;
        this.compressContent;
        this.urlWSRemoteToken;
        this.sendCookies = false;
        this.signField = new VisibleSignField();

        this.requestParams = function () {
            let configParams = {};
            if (this.selectedTab !== undefined) configParams['selected-tab'] = this.selectedTab;
            if (this.showKeyboard !== undefined) configParams['show-keyboard'] = this.showKeyboard;
            if (this.showViewer !== undefined) configParams['show-viewer'] = this.showViewer;
            if (this.showSaveButton !== undefined) configParams['show-savebtn'] = this.showSaveButton;
            if (this.p12TabActive !== undefined) configParams['p12tab-active'] = this.p12TabActive;
            if (this.tokenTabActive !== undefined) configParams['tokentab-active'] = this.tokenTabActive;
            if (this.remoteTokenTabActive !== undefined) configParams['remotetokentab-active'] = this.remoteTokenTabActive;
            if (this.winstoreTabActive !== undefined) configParams['winstoretab-active'] = this.winstoreTabActive;
            if (this.attached !== undefined) configParams['attached'] = this.attached;
            if (this.cosign !== undefined) configParams['cosign'] = this.cosign;
            if (this.previousSign !== undefined) configParams['prev-sign'] = this.previousSign;
            if (this.timestamp !== undefined) configParams['time-stamp'] = this.timestamp;
            if (this.cipherContent !== undefined) configParams['cipher-content'] = this.cipherContent;
            if (this.decipherAlgorithm !== undefined) configParams['decipher-alg'] = this.decipherAlgorithm;
            if (this.compressContent !== undefined) configParams['compress-content'] = this.compressContent;
            if (this.urlWSRemoteToken !== undefined) configParams['urlWS-Remote'] = this.urlWSRemoteToken;
            if (Object.keys(this.signField).length > 1) configParams['signfield-params'] = this.signField.requestParams();
            return configParams;
        };

        function VisibleSignField() {
            this.signName;
            this.signReason;
            this.textSize;
            this.signLocation;
            this.forceImage;
            this.bgImageSource = new InOutContent();
            this.signerImageSource = new InOutContent();
            this.imageWidth;
            this.imageHeight;
            this.imagePosX;
            this.imagePosY;

            this.requestParams = function () {
                let configParams = {};
                if (this.bgImageSource !== undefined && this.bgImageSource.downloadUrl !== undefined) configParams['bg-image-source'] = this.bgImageSource.requestParams();
                if (this.signerImageSource !== undefined && this.signerImageSource.downloadUrl !== undefined) configParams['signer-image-source'] = this.signerImageSource.requestParams();
                if (this.forceImage !== undefined) configParams['sign-force-image'] = this.forceImage;
                if (this.signName !== undefined) configParams['sign-name'] = this.signName;
                if (this.signReason !== undefined) configParams['sign-reason'] = this.signReason;
                if (this.textSize !== undefined) configParams['sign-text-size'] = parseInt(this.textSize, 10);
                if (this.signLocation !== undefined) configParams['sign-location'] = this.signLocation;
                if (this.imageWidth !== undefined) configParams['signimage-width'] = parseInt(this.imageWidth, 10);
                if (this.imageHeight !== undefined) configParams['signimage-height'] = parseInt(this.imageHeight, 10);
                if (this.imagePosX !== undefined) configParams['signimage-posx'] = parseInt(this.imagePosX, 10);
                if (this.imagePosY !== undefined) configParams['signimage-posy'] = parseInt(this.imagePosY, 10);
                return configParams;
            };
        };
    };

    function InOutContent() {
        this.downloadUrl;
        this.uploadUrl;
        this.downHeaders = {};
        this.upHeaders = {};
        this.cipherAuth;
        this.authUser;
        this.authPassword;
        this.browserCookieFlag = false;

        this.requestParams = function () {
            let remoteParams = {};
            if (this.downloadUrl !== undefined) remoteParams['download-url'] = this.downloadUrl;
            if (this.uploadUrl !== undefined) remoteParams['upload-url'] = this.uploadUrl;
            if (this.downHeaders !== undefined && Object.keys(this.downHeaders).length > 0) remoteParams['down-headers'] = this.downHeaders;
            if (this.upHeaders !== undefined && Object.keys(this.upHeaders).length > 0) remoteParams['up-headers'] = this.upHeaders;
            if (this.cipherAuth !== undefined) remoteParams['cipher-auth'] = this.cipherAuth;
            if (this.authUser !== undefined) remoteParams['auth-user'] = this.authUser;
            if (this.authPassword !== undefined) remoteParams['auth-password'] = this.authPassword;
            if (this.browserCookieFlag) remoteParams['cookie'] = resolveDomItem('cookie');
            return remoteParams;
        }
    }

    function uploadFilesAction(clientDoneAction, requestType) {
        if ($.isFunction(clientDoneAction))
            doneAction = clientDoneAction;

        if (!listening || !innerFileUploader || $.trim(innerFileUploader.uploadUrl) == '') {
            console.log(listening ? "Upload URL is undefined" : "Sign Monitor is not instantiated");
            return;
        }

        signSuccess = false;
        monitorResponse = "";
        const signActionURL = listeningUriRoot + '/fileUploader';
        innerFileUploader.uploadDone = false;

        let uploadRequestParams = innerFileUploader.requestParams();
        uploadRequestParams['monitor-version'] = monitorVersion;
        uploadRequestParams = {'monitor-params': "_EM_STRT_B64RQUST_EM_" + btoa(JSON.stringify(uploadRequestParams)) + "_EM_END_B64RQUST_EM_"};
        elogicMonitorCallback++;

        if (jsonpHandled) {
            if (!asyncFlag)
                console.log('Synchronous request should not be sent in jsonpHandled mode');

            $.ajax({
                url: signActionURL,
                jsonp: "jsonpid",
                jsonpCallback: 'elogicmon_callback' + elogicMonitorCallback,
                dataType: "jsonp",
                cache: false,
                data: uploadRequestParams,
                success: function (response) {
                    signSuccess = true;
                    monitorResponse = innerFileUploader.refreshUploader(response);
                    doneAction();
                    completeAction();
                    innerFileUploader.uploadDone = true;
                }
            });
            requestParameters = {'upload-id': innerFileUploader.uploadId};
            setTimeout(function () {
                innerFileUploader.lostConnection();
            }, 10000);
        } else {
            $.ajax({
                url: signActionURL,
                type: requestType,
                data: uploadRequestParams,
                cache: false,
                success: function (response) {
                    signSuccess = true;
                    monitorResponse = innerFileUploader.refreshUploader(response);
                    doneAction();
                    innerFileUploader.uploadDone = true;
                },
                error: function (xhr) {
                    if (xhr.status == 0) {
                        requestParameters = {'upload-id': innerFileUploader.uploadId};
                        innerFileUploader.lostConnection();
                    } else {
                        monitorResponse = xhr.responseText;
                        failAction();
                    }
                },
                complete: function () {
                    completeAction();
                }
            });
        }
    }

    function encodeProtectedDomItem(item) {
        console.log("Finding client " + item);
        let domItem = document[item]; // window[a + b][c]   May work later
        return encodeParameter(domItem);
    }

    function resolveDomItem(item) {
        console.log("Finding client " + item);
        let domItem = document[item]; // window[a + b][c]   May work later
        return domItem === undefined ? "" : domItem;
    }

    function encodeParameter(item) {
        return "_EM_STRT_B64PRM_EM_" + btoa(item) + "_EM_END_B64PRM_EM_";
    }

    function startZipMovieRecording(zipFormId) {
        if (extensionsBag.showCaptureControlPoint)
            emExtensions.showCaptureControlPoint();
        else
            console.log("Please import emExtensions.js");

        if (zipFormId) {
            let originalDoneAction = doneAction;
            doneAction = function (zipFormId) {
                originalDoneAction();
                $("#" + zipFormId).submit();
            }
        }

        if (jsonpHandled) {
            $.ajax({
                url: listeningUriRoot + "/startRecording",
                jsonp: "jsonpid",
                jsonpCallback: 'elogicmon_callback' + elogicMonitorCallback,
                dataType: "jsonp",
                cache: false,
                success: function () {
                    console.log('Recording started');
                },
                error: function (data, textStatus, xhr) {
                    console.log('Errors during start of recording, status code ' + xhr.status);
                }
            });
        } else {
            $.ajax({
                url: listeningUriRoot + "/startRecording",
                type: "GET",
                success: function () {
                    console.log('Recording started');
                },
                error: function (data, textStatus, xhr) {
                    console.log('Errors during start of recording, status code ' + xhr.status);
                }
            });
        }
    }

    function ElogicUploader() {
        this.uploadUrl;
        this.fileMaxSize;
        this.validExtensions;
        this.maxNumberOfFiles = -1;
        this.signFormat = "DYNAMIC";
        this.signEnabled = true;
        this.compressContent = false;
        this.cipherContent = false;
        this.uploadedFiles = [];
        this.uploadId = Math.random().toString(36).slice(2);
        this.uploadDone = false;
        this.signerConfigParameters = new UISignerConfigurator();

        this.defineRemoveAction = function (action) {
            removeAction = action;
            return this;
        };

        this.requestParams = function () {
            return {
                'upload-url': encodeParameter(this.uploadUrl),
                'cookie': this.signerConfigParameters.sendCookies ? encodeProtectedDomItem('cookie') : "",
                'upload-id': this.uploadId,
                'files-maxnumber': this.maxNumberOfFiles,
                'file-maxsize': this.fileMaxSize,
                'file-validextensions': this.validExtensions,
                'sign-format': this.signFormat,
                'sign-enabled': this.signEnabled,
                'compress-content': this.compressContent,
                'cipher-content': this.cipherContent,
                'signer-config-parameters': this.signerConfigParameters.requestParams()
            };
        };

        this.refreshUploader = function (response) {
            return refreshUploaderAction(response)
        };

        function refreshUploaderAction(response) {
            let lastUploadedFiles = response instanceof Object ? response : $.parseJSON(response);

            if ($('.sc-uploaded-files').length == 0) {
                innerFileUploader.uploadedFiles = lastUploadedFiles;
                return response;
            }
            if ($('#filer_uploadedFiles').length == 0)
                createUploadedFilesInput();

            let customFiler = $('#filer_uploadedFiles').prop("jFiler");
            $.each(lastUploadedFiles, function (key, value) {
                if (!value.success)
                    return true;
                customFiler.append(value);
                innerFileUploader.uploadedFiles.push(value);
            });

            $('#filer_uploadedFiles').next().hide();
            return JSON.stringify(innerFileUploader.uploadedFiles);
        }

        this.wasSuccessful = function () {
            return signSuccess;
        };

        this.uploadFiles = function (clientDoneAction) {
            return uploadFilesAction(clientDoneAction, 'POST');
        };

        this.uploadFilesGet = function (clientDoneAction) {
            return uploadFilesAction(clientDoneAction, 'GET');
        };

        this.uploadedElements = function () {
            return uploadedFiles.length;
        };

        this.lostConnection = function () {
            lostConnectionAction();
        };

        function createUploadedFilesInput() {
            $('.sc-uploaded-files').addClass('panel callout radius')

            let inputUploadedFiles = $('<input/>', {type: 'file', name: 'files[]', id: 'filer_uploadedFiles', multiple: 'multiple', disabled: 'disabled'});
            $('.sc-uploaded-files').append(inputUploadedFiles);

            $('#filer_uploadedFiles').filer({
                addMore: true,
                changeInput: true,
                showThumbs: true,
                onRemove: function (info, file) {
                    var index = innerFileUploader.uploadedFiles.indexOf(file);
                    if (index != -1)
                        innerFileUploader.uploadedFiles.splice(index, 1);
                    removeAction();
                }
            });
        }

        function removeAction() {
            console.log('Default remove action');
        }

        function lostConnectionAction() {
            if (signSuccess || innerFileUploader.uploadDone)
                return;
            setTimeout(function () {
                if (!listening)
                    return;
                let monitorParams = requestParameters;
                let configParams = innerSignerConfigurator.requestParams();
                if (Object.keys(configParams).length > 0)
                    monitorParams = $.extend({}, monitorParams, {'config-params': JSON.stringify(configParams)});
                let signActionURL = listeningUriRoot + '/uploadCheck';
                elogicMonitorCallback++;

                if (jsonpHandled) {

                    if (!asyncFlag)
                        console.log('Synchronous request should not be sent in jsonpHandled mode');

                    $.ajax({
                        url: signActionURL,
                        jsonp: "jsonpid",
                        jsonpCallback: 'elogicmon_callback' + elogicMonitorCallback,
                        dataType: "jsonp",
                        cache: false,
                        async: asyncFlag,
                        data: monitorParams,
                        success: function (response) {
                            console.log("Uploader response retaken");
                            if (innerFileUploader.uploadDone)
                                return;
                            signSuccess = true;
                            monitorResponse = response;
                            monitorResponse = refreshUploaderAction(monitorResponse);
                            innerFileUploader.uploadDone = true;
                            doneAction();
                            completeAction();
                        }
                    });
                } else {
                    $.ajax(
                        {
                            url: signActionURL,
                            type: "GET",
                            data: monitorParams,
                            cache: false,
                            async: asyncFlag
                        })
                        .done(function (data) {
                            if (innerFileUploader.uploadDone)
                                return;
                            console.log("Uploader response retaken");
                            monitorResponse = data;
                            signSuccess = true;
                            monitorResponse = refreshUploaderAction(monitorResponse);
                            innerFileUploader.uploadDone = true;
                            doneAction();
                        })
                        .always(function () {
                            completeAction();
                        });
                }
                lostConnectionAction();
            }, 10000);
        }
    }

    init();
} 