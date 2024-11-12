<?php 
	session_start();
	$ruta_raiz = "../";
	$ruta_libs = "../respuestaRapida/";
	define('SMARTY_DIR', $ruta_libs.'libs/');
	require (SMARTY_DIR.'Smarty.class.php');

    // Include the CKEditor class.
    include_once "$ruta_raiz/ckeditor/ckeditor_php5.php";
	
		
    // Create a class instance.
    $CKEditor = new CKEditor();

    // Path to the CKEditor directory.
    $CKEditor->basePath = $ruta_raiz."ckeditor/";
	
	$smarty = new Smarty;
	$smarty->template_dir = './templates';
	$smarty->compile_dir = './templates_c';
	$smarty->config_dir = './configs/';
	$smarty->cache_dir = './cache/';
	
	$smarty->left_delimiter = '<!--{';
	$smarty->right_delimiter = '}-->';

	if ($_SESSION["krd"])
	    $krd = $_SESSION["krd"];
	if ($_GET["asunto"])
	    $asunto = $_GET["asunto"];
	if ($_GET["radicadopadre"])
	    $radicado = $_GET["radicadopadre"];
	
	include_once ($ruta_raiz."include/db/ConnectionHandler.php");

	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug=true;

	$usuario      = $_SESSION["usua_nomb"];
	$dependencia  = $_SESSION["depe_nomb"];
	$dep_code     = $_SESSION["dependencia"];
	$encabezado   = session_name()."=".session_id();
	$encabezado .= "&krd= $krd";
	
	$isql ="SELECT 	USUA_EMAIL, 
					USUA_EMAIL_1, 
					USUA_EMAIL_2, 
					DEPE_CODI, 
					USUA_CODI, 
					USUA_NOMB, 
					USUA_LOGIN, 
					USUA_DOC 
			FROM 	USUARIO 
			WHERE 	USUA_LOGIN ='$krd' ";
	$rs 	= $db->conn->Execute($isql);	
	
	while (!$rs->EOF) {
		
	    $emails[] = trim(strtolower($rs->fields["USUA_EMAIL"]));		
		$temEmail =	trim(strtolower($rs->fields["USUA_EMAIL_1"]));
		$temEmai  =	trim(strtolower($rs->fields["USUA_EMAIL_2"]));
		
		//buscamos el correo que inicie con web para colocarlo como primero
		if(substr($temEmail, 0, 3)== 'web'){
			array_unshift($emails, $temEmail);	
		}else{
			$emails[] = $temEmail; 	
		}		
		
		if(substr($temEmai, 0, 3)== 'web'){
			array_unshift($emails, $temEmai);	
		}else{
			$emails[] = $temEmai;
		}
		
		$usuacodi  = $rs->fields["USUA_CODI"];
		$depecodi  = $rs->fields["DEPE_CODI"];
		$usuanomb  = $rs->fields["USUA_NOMB"];
		$usualog   = $rs->fields["USUA_LOGIN"];
		$codigoCiu = $rs->fields["USUA_DOC"];
	    $rs->MoveNext();
	}		
	//Eliminamos los campos vacios en el array	
	$emails 	=  array_filter($emails);
	
	# informacion remitente
	$name  = "";
	$email = "";

	$isql  = "SELECT D.* FROM SGD_DIR_DRECCIONES D
	             WHERE D.RADI_NUME_RADI = $radicado";
	$rs = $db->conn->Execute($isql);

    $name       = $rs->fields["SGD_DIR_NOMREMDES"];
    $email      = $rs->fields["SGD_DIR_MAIL"];
    $municicodi = $rs->fields["MUNI_CODI"];
    $depecodi2  = $rs->fields["DPTO_CODI"];
    
    $name       = ucfirst(strtolower($name));
    $depcNomb   = ucfirst(strtolower($depcNomb));
    
    $asunto = " Se&ntilde;or(a)<br />
                <strong>$name<br /></strong>"
                .$email;
	
	$sqlD = " SELECT
		           a.MUNI_NOMB,
		           b.DPTO_NOMB
              FROM 
                   MUNICIPIO a, DEPARTAMENTO b
			  WHERE (a.ID_PAIS = 170)
					AND	(a.ID_CONT = 1)
					AND (a.DPTO_CODI = $depecodi2)
					AND (a.MUNI_CODI = $municicodi)
					AND (a.DPTO_CODI=b.DPTO_CODI)
					AND (a.ID_PAIS=b.ID_PAIS)
					AND (a.ID_CONT=b.ID_CONT)";

	$descripMuniDep = $db->conn->Execute($sqlD);
	$depcNomb       = $descripMuniDep->fields["MUNI_NOMB"];
	$muniNomb       = $descripMuniDep->fields["DPTO_NOMB"];
	
	$destinatario = $email;

    $sql1 		= "	select
                        anex_tipo_ext as ext
                    from
                        anexos_tipo";

    $exte = $db->conn->Execute($sql1);

    while(!$exte->EOF) {
        $val  = $exte->fields["ext"];
        $extn .= empty($extn)? $val : "|".$val;
        //arreglo para validar la extension
        $exte->MoveNext();
    };
	
	$smarty->assign("sid"			, SID); //Envio de session por get
	$smarty->assign("usuacodi" 		, $usuacodi);
	$smarty->assign("extn" 		    , $extn);
	$smarty->assign("depecodi"		, $depecodi);
	$smarty->assign("codigoCiu"		, $codigoCiu);
	$smarty->assign("radPadre"		, $radicado);
	$smarty->assign("usuanomb"		, $usuanomb);
	$smarty->assign("usualog"		, $usualog);
	$smarty->assign("destinatario"	, $destinatario);
	$smarty->assign("concopia"		, "");
	$smarty->assign("concopiaOculta", "");
	$smarty->assign("asunto"		, $asunto);
	$smarty->assign("emails"		, $emails);
	$smarty->display('index.tpl');

    // Replace a textarea element with an id (or name) of "textarea_id".
	$CKEditor->config['height'] = 575;
    $CKEditor->replace("texrich");
?>
