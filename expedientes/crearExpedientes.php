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
 * se encuentran en el archivo crearExpedientes.js
 * y en la plantilla asociada a este script
 * (crearExpedientesTipificar.tpl)
 */

/**@filesource confSmarty.php incluye la ruta raiz definida como
 * $ruta_raiz = "../";
 */

include_once("confSmarty.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
require_once('libs/JSON.php');	


//Inicia adodb
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	

$dependencia 	= $_SESSION['depecodi'];
$documento_Usua	= $_SESSION['usua_doc'];
$codusua		= $_SESSION['codusuario'];

/**
 * Variables enviadas por _GET
 */	
$nurad			= $_GET['nurad'];				//radicado	
	

//$vars = get_defined_vars(); print_r($vars["_GET"]);
	
	
/**
 * Consulta de la serie
 * Con la selecion de una serie se buscaran las respectivas 
 * subserie y tipo documental para crear el expediente.
 * Esta busqueda inicia con el filtro realizado por la dependencia
 * 
 */
	
$fecha_hoy 		= 	Date("Y-m-d");
$sqlFechaHoy	=	$db->conn->DBDate($fecha_hoy);
	
$sql1 = "SELECT	DISTINCT (RIGHT('000' + convert(varchar,S.SGD_SRD_CODIGO),3)+' - '+S.SGD_SRD_DESCRIP + ' - ' + CASE WHEN M.SGD_MRD_ESTA = 0 THEN '(INACTIVA)' WHEN M.SGD_MRD_ESTA = 1 THEN '(ACTIVA)' END)AS DETALLE,
				RIGHT('000' + convert(varchar,S.SGD_SRD_CODIGO),3) AS CODIGO_SERIE,
				M.SGD_MRD_ESTA
		 FROM	SGD_MRD_MATRIRD M
				JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO AND S.SGD_SRD_CODIGO > 0
		 WHERE	M.DEPE_CODI = '$dependencia' AND
				M.SGD_SRD_CODIGO > 0 AND
				M.SGD_MRD_CODIGO = (SELECT	TOP(1) M2.SGD_MRD_CODIGO 
									FROM	SGD_MRD_MATRIRD M2 
									WHERE	M2.DEPE_CODI = '$dependencia' AND 
											M2.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
									ORDER BY M2.SGD_MRD_ESTA DESC)
				and M.SGD_MRD_ESTA = 1
		ORDER BY M.SGD_MRD_ESTA DESC, DETALLE";
$rs				=	$db->conn->Execute($sql1);
while(!$rs->EOF){
	$serieArray[$rs->fields["CODIGO_SERIE"]] = $rs->fields["DETALLE"];
	$rs->MoveNext();				
}
	
/**
 * Consulta los usuario que pertenecen a 
 * la dependencia para que puedan ser seleccionados
 * y asignarselo al expediente
 */	
$wUsua= ($_SESSION["usuaPermExpediente"]<=1? " AND USUA.USUA_LOGIN='".$_SESSION["krd"]."' ":" " ); 
$sql2 = "SELECT 
			USUA.USUA_NOMB AS USUARIO
			,USUA.USUA_DOC AS CODIGO
		FROM 
		 	USUARIO USUA
			,SGD_USD_USUADEPE USD
        WHERE
           	USD.DEPE_CODI		= '$dependencia'
           	AND USUA.USUA_DOC 	= USD.USUA_DOC
           	AND USUA.USUA_LOGIN = USD.USUA_LOGIN
           	AND USUA_ESTA		= 1            	
            $wUsua                
		ORDER BY USUA_NOMB";

$rs2	= $db->conn->Execute($sql2);
while(!$rs2->EOF){
	$usuaArray[$rs2->fields["CODIGO"]] = $rs2->fields["USUARIO"];
	$rs2->MoveNext();				
}


/**
 * Consulta las dependencias 
 * Para seleccionar el usuario Responsable.
 */	
    if($_SESSION["usuaPermExpediente"]==3){ 
        $sql2 = "SELECT DEPE_CODI CODIGO,".$db->conn->Concat("cast(DEPE_CODI as varchar)","' - '","DEPE_NOMB")." DEPENDENCIA
                 FROM DEPENDENCIA 
                 WHERE DEPENDENCIA_ESTADO=2";

        $rs2	= $db->conn->Execute($sql2);
        while(!$rs2->EOF){
            $depeArray[$rs2->fields["CODIGO"]] = $rs2->fields["DEPENDENCIA"];
            $rs2->MoveNext();				
        }
        $smarty->assign("depeArray"	, $depeArray);
    }
/**
 * Mostar el select para proyectos si el
 * usuario tiene permisos
 */
	
$sql3	= "	SELECT
				P.SGD_EPRY_CODIGO as CODIGO, 
				P.SGD_EPRY_NOMBRE as NOMBRES_PROY
			FROM 
				SGD_EPRY_EPROYECTO P
			WHERE 
				P.DEPE_CODI = '$dependencia'
				 ORDER BY 1";
			
$result = $db->conn->Execute($sql3);
				
while(!$result->EOF){
	$proyArray[$result->fields["CODIGO"]]=
			htmlentities(trim($result->fields["NOMBRES_PROY"]));							
	$result->MoveNext();
}
		
$mosProy = empty($proyArray)?  'false' : 'true';
	
	
/**
 * Buscar los radicados para mostrar el arbol de 
 * documentos anexos.
 */
	
$sql4  = 	"SELECT
				R.RADI_NUME_RADI AS RADICADO
				,R.RA_ASUN 		 AS ASUNTO
			FROM
				RADICADO R
				, ANEXOS A
			WHERE
				A.ANEX_RADI_NUME 	 = $nurad
				AND A.ANEX_SALIDA	 = 1
				AND A.ANEX_RADI_NUME <> A.RADI_NUME_SALIDA
				AND R.RADI_NUME_RADI = A.RADI_NUME_SALIDA";

$salida = $db->conn->Execute($sql4);

while(!$salida->EOF) {
	$rad_Padre = $salida->fields["RADICADO"];
	$asu_Padre = htmlentities($salida->fields["ASUNTO"]);

	//Busqueda de radicados anexos a los hijos del $rad_num (nietos)
	$sqlF =	"	SELECT
				R.RADI_NUME_RADI AS RADI_HIJO
				, R.RA_ASUN AS ASU_HIJO
			FROM
				RADICADO R
			WHERE
				R.RADI_NUME_RADI IN
					(SELECT
						A.RADI_NUME_SALIDA
           			FROM
           				ANEXOS A
           			WHERE
           				A.ANEX_SALIDA = 1
           				AND A.ANEX_RADI_NUME = $rad_Padre
           				AND A.ANEX_RADI_NUME <> A.RADI_NUME_SALIDA)";

    $salida_Sqlf = $db->conn->Execute($sqlF);
		
    while(!$salida_Sqlf->EOF){
       	$rad_Hijo = $salida_Sqlf->fields["radi_hijo"];
		$asu_Hijo = htmlentities($salida_Sqlf->fields["asu_hijo"]);
		$radicados_hijos[]=array('Radicado'=>$rad_Hijo, 'Asunto'=>$asu_Hijo);
		$salida_Sqlf->MoveNext();
    }
	
	$arrayArbol[]=	array(  'Radicado'=>	$rad_Padre
							,'Asunto'=>		$asu_Padre
							,'hijos' => 	$radicados_hijos);
						
	unset($radicados_hijos);
	$salida->MoveNext();
};	
	
$mosArbol 		= empty($arrayArbol)? 'false' : 'true';
if($mosArbol=='true'){
	$json 	  		= new Services_JSON();
	$arrayArbol 	= ($json->encode($arrayArbol));
}		
	
$tipoDoc = "";
$subSerie = "";
$serie = "";
$cambio = 0;
$coditrdx = "SELECT S.SGD_TPR_CODIGO, S.SGD_TPR_DESCRIP as TPRDESCRIP, R.RADI_DEPE_ACTU, R.RADI_USUA_ACTU, R.SGD_CAMBIO_TRD
            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO S ON R.TDOC_CODI = S.SGD_TPR_CODIGO
            WHERE R.RADI_NUME_RADI = $nurad";
$res_coditrdx = $db->conn->Execute($coditrdx);
if ($res_coditrdx && !$res_coditrdx->EOF) {
    $tipoDoc = $res_coditrdx->fields['SGD_TPR_CODIGO'];
    $TDCactu = $res_coditrdx->fields['TPRDESCRIP'];
    $usuactu = $res_coditrdx->fields['RADI_USUA_ACTU'];
    $depeactu = $res_coditrdx->fields['RADI_DEPE_ACTU'];
    $cambio = ($res_coditrdx->fields['SGD_CAMBIO_TRD'] == null ? 0 : $res_coditrdx->fields['SGD_CAMBIO_TRD']);
}

$sqlMRD = "SELECT M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO
                FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
                WHERE R.RADI_NUME_RADI = $nurad ORDER BY R.SGD_RDF_FECH desc " ;
$rsMRD = $db->conn->Execute($sqlMRD);
if ($rsMRD && !$rsMRD->EOF) {
    $serie = $rsMRD->fields["SGD_SRD_CODIGO"];
    $subSerie = $rsMRD->fields["SGD_SBRD_CODIGO"];
    $tipoDoc = $rsMRD->fields["SGD_TPR_CODIGO"];
} 

$usuaModifica1 = "";
$usuaModifica2 = "";
$sqlus1 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TRD = 1  ";
$rsus1 = $db->conn->Execute($sqlus1);
if ($rsus1 && ! $rsus1->EOF) {
    $usuaModifica1 = $rsus1->fields['USUA_NOMB'];
}
$sqlus2 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TIPODOC = 1 ";
$rsus2 = $db->conn->Execute($sqlus2);
if ($rsus2 && ! $rsus2->EOF) {
    $usuaModifica2 = $rsus2->fields['USUA_NOMB'];
}

$pqr = '0';
$sqlPqr = "SELECT D.SGD_TPR_NOTIFICA AS PQR FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO D ON R.TDOC_CODI = D.SGD_TPR_CODIGO
            WHERE R.RADI_NUME_RADI = $nurad ";
$rsPqr = $db->conn->Execute($sqlPqr);
if ($rsPqr && ! $rsPqr->EOF) {
    $pqr = ($rsPqr->fields["PQR"] == null ? '0' : $rsPqr->fields["PQR"]);
}


/**
 * El resultados de la logica se coloca en las variables
 * de la plantilla crearExpedientesTipificar.php
 */

//parametros para javascript y generar resultados a partir
//de la deperdencia a la cual pertenece el usuario

$smarty->assign("sid"			, SID			);//Envio de session por get	
$smarty->assign("dependencia"	, $dependencia	);
$smarty->assign("documento_Usua", $documento_Usua);
$smarty->assign("nurad"			, $nurad		);	
	
$smarty->assign("serieArray"	, $serieArray	);
$smarty->assign("usuaArray"		, $usuaArray	);
$smarty->assign("mosProy"		, $mosProy		);
$smarty->assign("proyArray"		, $proyArray	);
$smarty->assign("mosArbol"		, $mosArbol		);
$smarty->assign("arrayArbol"	, $arrayArbol	);
$smarty->assign("veractivos"	,1);				//Ver TODAS las dependencias y series.
$smarty->assign("depecodi"		, $dependencia		  );//dependencia del usuario
$smarty->assign("usua_doc"		, $documento_Usua );//Documento del usuario
$smarty->assign("codusua"		, $codusua );		//Codigo del usuario	
$smarty->assign("permExp"		, $_SESSION["usuaPermExpediente"] );//Codigo del usuario
$smarty->assign("docuActu"		, $tipoDoc ); //Codigo tipo de documento
$smarty->assign("subSerActu"	, $subSerie ); //Codigo serie
$smarty->assign("serieActu"		, $serie ); //Codigo subserie
$smarty->assign("cambio"	    , $cambio ); //Codigo cambio
$smarty->assign("retipifica"	, $_SESSION["retipificatrd"] );
$smarty->assign("usuaModifica1"	, $usuaModifica1 );
$smarty->assign("usuaModifica2"	, $usuaModifica2 );
$smarty->assign("pqr"	        , $pqr );

$smarty->display('crearExpedientes.tpl');
?>