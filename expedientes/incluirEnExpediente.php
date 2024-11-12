<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

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

	include_once "confSmarty.php";
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	require_once 'libs/JSON.php';

	//Inicia adodb
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	
		
	$nurad				= $_GET['numRad'];
	$depecodi 			= $_SESSION['depecodi'];	
	$documento_Usua		= $_SESSION['usua_doc'];		
	$codusua 			= $_SESSION['codusuario'];
	$veractivos			= isset($_POST['veractivos']) ? $_POST['veractivos'] : 1;
	$ano_busq			= date("Y");
		
	/**
	 * Select para mostrar todas las dependencias
	 */	

	$sql_depe = "SELECT DE.DEPE_CODI AS CODIGO, RIGHT('0000' + convert(varchar,DE.DEPE_CODI),4)+ ' - ' + DE.DEPE_NOMB AS NOMBRE
				FROM DEPENDENCIA DE WHERE DEPENDENCIA_ESTADO=2";	
				
	$result_dep = $db->conn->Execute($sql_depe);
			
	while(!$result_dep->EOF){
		$depeArray[$result_dep->fields["CODIGO"]] = htmlentities(trim($result_dep->fields["NOMBRE"]));							
		$result_dep->MoveNext();			
	}		
	
	/**
	 * Consulta de la serie
	 * Con la selecion de una serie se buscaran las respectivas 
	 * subserie y tipo documental para crear el expediente.
	 * Esta busqueda inicia con el filtro realizado por la dependencia
	 * 
	 */
	
	$fecha_hoy 		= 	Date("d-m-Y");
	$sqlFechaHoy	=	$db->conn->DBDate($fecha_hoy);
	$wva = ($veractivos==1) ? " and M.SGD_MRD_ESTA = 1 " : "";
	$sql1 = "SELECT	DISTINCT (RIGHT('000' + convert(varchar,S.SGD_SRD_CODIGO),3)+' - '+S.SGD_SRD_DESCRIP + ' - ' + CASE WHEN M.SGD_MRD_ESTA = 0 THEN '(INACTIVA)' WHEN M.SGD_MRD_ESTA = 1 THEN '(ACTIVA)' END)AS DETALLE,
					RIGHT('000' + convert(varchar,S.SGD_SRD_CODIGO),3) AS CODIGO_SERIE,
					M.SGD_MRD_ESTA
			 FROM	SGD_MRD_MATRIRD M
					JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO AND S.SGD_SRD_CODIGO > 0
			 WHERE	M.DEPE_CODI = '$depecodi' AND
					M.SGD_SRD_CODIGO > 0 AND
					M.SGD_MRD_CODIGO = (SELECT	TOP(1) M2.SGD_MRD_CODIGO 
										FROM	SGD_MRD_MATRIRD M2 
										WHERE	M2.DEPE_CODI = '$depecodi' AND 
												M2.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
										ORDER BY M2.SGD_MRD_ESTA DESC)
				$wva	
			ORDER BY M.SGD_MRD_ESTA DESC, DETALLE";
	$rs				=	$db->conn->Execute($sql1);
	while(!$rs->EOF){
		$serieArray[$rs->fields["CODIGO_SERIE"]] = $rs->fields["DETALLE"];
		$rs->MoveNext();				
	}
	
	/**
	 * @var $ano_busq select de años para filtrar busqueda
	 * se realiza desde el año actual hasta 10 años antes  
	 */
	
	for($i=0; $i< 10; $i++){
		$anoArray[$ano_busq - $i]= $ano_busq - $i;		
	}

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
	
	$usuaModifica1 = "";
	$usuaModifica2 = "";
	$sqlus1 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TRD = 1 ";
	$rsus1 = $db->conn->Execute($sqlus1);
	if ($rsus1 && ! $rsus1->EOF) {
	    $usuaModifica1 = $rsus1->fields['USUA_NOMB'];
	}
	$sqlus2 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TIPODOC = 1 ";
	$rsus2 = $db->conn->Execute($sqlus2);
	if ($rsus2 && ! $rsus2->EOF) {
	    $usuaModifica2 = $rsus2->fields['USUA_NOMB'];
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
	
	$pqr = '0';
	$sqlPqr = "SELECT D.SGD_TPR_NOTIFICA AS PQR FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO D ON R.TDOC_CODI = D.SGD_TPR_CODIGO
            WHERE R.RADI_NUME_RADI = $nurad ";
	$rsPqr = $db->conn->Execute($sqlPqr);
	if ($rsPqr && ! $rsPqr->EOF) {
	    $pqr = ($rsPqr->fields["PQR"] == null ? '0' : $rsPqr->fields["PQR"]);
	}
	
	//IBISCOM 2018-12-12 INICIO
	$sqlValidaMet = "SELECT COUNT(id_anexo) as NumMetad
                	FROM  METADATOS_DOCUMENTO
                	WHERE id_anexo LIKE  '%$nurad%'";
	$resultMetadatos = $db->conn->Execute($sqlValidaMet)->fields["NumMetad"];
	$hayMetadatos = "";
	$mensajeMetadatos = "";
	if($resultMetadatos >= 1)
	{
	    $hayMetadatos = ""; //disabled
	    $mensajeMetadatos="El radicado ya tiene documentos con metadatos";
    }
	//IBISCOM 2018-12-12 FIN
	/**
	 * El resultados de la logica se coloca en las variables
	 * de la plantilla crearExpedientesTipificar.php
	 */

	if ($ocultaDocElectronico == 1) {
	    $oculta = "";
	} else {
	    $oculta = "none";
	}
	//parametros para javascript y generar resultados a partir
	//de la deperdencia a la cual pertenece el usuario

	$smarty->assign("sid"			   , SID			);//Envio de session por get
	$smarty->assign("anoArray"		   , $anoArray		);//Año actual
	$smarty->assign("serieArray"       , $serieArray	);
	$smarty->assign("depeArray"		   , $depeArray		);//dependencia de la cual se buscan los expedientes
	$smarty->assign("mosArbol"		   , $mosArbol		);
	$smarty->assign("arrayArbol"	   , $arrayArbol	);
	$smarty->assign("numRad"	      	, $nurad);
	$smarty->assign("depecodi"		    , $depecodi		  );//dependencia del usuario
	$smarty->assign("usua_doc"		    , $documento_Usua );//Documento del usuario
	$smarty->assign("codusua"		    , $codusua );		//Codigo del usuario	
	$smarty->assign("veractivos"	    , 1);				//Ver TODAS las dependencias y series.
	$smarty->assign("hayMetadatos"	    , $hayMetadatos);   //IBISCOM 2018-12-12
	$smarty->assign("mensajeMetadatos"	, $mensajeMetadatos);   //IBISCOM 2018-12-12
	$smarty->assign("oculta"            , $oculta );
	$smarty->assign("tdoc"              , $tipoDoc );
	$smarty->assign("serie"             , $serie );
	$smarty->assign("subSerie"          , $subSerie );
	$smarty->assign("pqr"               , $pqr );
	$smarty->assign("cambio"            , $cambio );
	$smarty->assign("retipifica"	    , $_SESSION["retipificatrd"] );
	$smarty->assign("usuaModifica1"	    , $usuaModifica1 );
	$smarty->assign("usuaModifica2"	    , $usuaModifica2 );
	
	$smarty->display('incluirEnExpediente.tpl');
?>