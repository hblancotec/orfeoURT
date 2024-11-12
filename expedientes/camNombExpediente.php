<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

$krd = $_GET["krd"];
if (!$ruta_raiz) $ruta_raiz = "..";
include("$ruta_raiz/config.php");
if (!isset($_SESSION['dependencia'])) include "$ruta_raiz/rec_session.php";
	/**
	 * Los combobox y demas elementos de presentacion
	 * se encuentran en el archivo incluirEnExpediente.js
	 * y en la plantilla asociada a este script
	 * (incluirEnExpediente.tpl)
	 */

	/**@filesource confSmarty.php incluye la ruta raiz definida como
	 * $ruta_raiz = "../";
	 */

	include_once("confSmarty.php");
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	
	
	//Inicia adodb
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	

	$depecodi 			= $_SESSION['depecodi'];	
	$documento_Usua		= $_SESSION['usua_doc'];		
	$codusua 			= $_SESSION['codusuario'];
	$numExp				= $expediente;	
	
	$vars = get_defined_vars(); print_r($vars["$_GET"]);
			
	/**
	 * Select para mostrar todas las dependencias
	 */	
		
	$sql4  = 	"SELECT   SE.SGD_SEXP_PAREXP1
						  + ' ' + SE.SGD_SEXP_PAREXP2
						  + ' ' + SE.SGD_SEXP_PAREXP3
						  + ' ' + SE.SGD_SEXP_PAREXP4
						  + ' ' + SE.SGD_SEXP_PAREXP5
						  AS NOMEXPED					
				        , SB.SGD_SBRD_DESCRIP   AS SUBSERIE
				        , SR.SGD_SRD_DESCRIP    AS SERIE
				
				FROM     SGD_SEXP_SECEXPEDIENTES 	SE
				        ,SGD_SRD_SERIESRD 			SR
				        ,SGD_SBRD_SUBSERIERD 		SB
				      
				WHERE     SE.sgd_sbrd_codigo   = SB.SGD_SBRD_CODIGO
				      AND SE.sgd_srd_codigo    = SR.SGD_SRD_CODIGO
				      AND SB.SGD_SRD_CODIGO    =  SR.SGD_SRD_CODIGO
				      AND SE.SGD_EXP_NUMERO     like  '$numExp'";

	
	$salida = $db->conn->Execute($sql4);

    while(!$salida->EOF){
    	$nombreExp	= $salida->fields["NOMEXPED"];
		$serie		= $salida->fields["SERIE"];
		$subSerie	= $salida->fields["SUBSERIE"];
		$salida->MoveNext();  
	};	
	
		
	/**
	 * El resultados de la logica se coloca en las variables
	 * de la plantilla camNombExpediente.php
	 */

	//parametros para javascript y generar resultados a partir
	//de la deperdencia a la cual pertenece el usuario

	$smarty->assign("sid"			, SID			);//Envio de session por get	
	$smarty->assign("numExp"		, $numExp		);//Numero del expediente
	$smarty->assign("nombreExp"		, $nombreExp	);//Nombre actual del expediente
	$smarty->assign("serie"			, $serie		);//Serie
	$smarty->assign("subSerie"		, $subSerie		);//Subserie
	
	$smarty->assign("depecodi"		, $depecodi		  );//dependencia del usuario
	$smarty->assign("usua_doc"		, $documento_Usua );//Documento del usuario
	$smarty->assign("codusua"		, $codusua );		//Codigo del usuario	

	$smarty->display('camNombExpediente.tpl');
?>