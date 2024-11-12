<?php
session_start();
$ruta_raiz="..";
if (count($_SESSION) == 0) {
    die(include "$ruta_raiz/sinacceso.php");
    exit;
}
else if (isset($_SESSION['krd'])) {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
require_once("$ruta_raiz/_conf/constantes.php");
if(empty($_SESSION['dependencia'])) {
    include (ORFEOPATH . "rec_session.php");
}

include_once "$ruta_raiz/config.php";
require_once "$ruta_raiz/class_control/correoElectronico.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
include_once "$ruta_raiz/include/query/busqueda/busquedaPiloto1.php";
include_once "$ruta_raiz/include/tx/Historico.php";
include_once "$ruta_raiz/class_control/TipoDocumental.php";
$trd = new TipoDocumental($db);
	
if ($borrar){		
	$sqlE ="SELECT	$radi_nume_radi RADI_NUME_RADI
			FROM	SGD_RDF_RETDOCF r 
			WHERE	RADI_NUME_RADI = $nurad
					AND  SGD_MRD_CODIGO = $codiTRDEli";
	$rsE=$db->conn->Execute($sqlE);

	$i=0;
	while(!$rsE->EOF){
		$codiRegE[$i] = $rsE->fields['RADI_NUME_RADI'];
    	$i++;
		$rsE->MoveNext();
	}

	$TRD = $codiTRDEli;
	include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
	$observa = "*Eliminada TRD*".$deta_serie."/".$deta_subserie."/".$deta_tipodocu;
		
	$Historico = new Historico($db);		  
  		 
	$radiModi = $Historico->insertarHistorico($codiRegE, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 33); 
	$radicados = $trd->eliminarTRD($nurad,$coddepe,$usua_doc,$codusua,$codiTRDEli);
	$mensaje="Archivo eliminado<br> ";
		
	//guardar el registro en el historico de tipo documental.
	$queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(	SGD_HMTD_FECHA,
														RADI_NUME_RADI,
														USUA_CODI,
														SGD_HMTD_OBSE,
														USUA_DOC,
														DEPE_CODI,
														SGD_TTR_CODIGO)
									VALUES(	".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
											$nurad,
											$codusua,
											'Se borro la TRD',
											$usua_doc,
											$dependencia,
											33)";	
						
	$ejecutarQuerey	= $db->conn->Execute($queryGrabar);
		
    if(empty($ejecutarQuerey)){
    	echo 'No se guardo el registro en historico documental';
    }
}

// Proceso de modificacion de una clasificacion TRD
if ($modificar && $tdoc !=0 && $tsub !=0 && $codserie !=0) {
	$sqlH = "	SELECT	$radi_nume_radi RADI_NUME_RADI,
						SGD_MRD_CODIGO 
				FROM	SGD_RDF_RETDOCF r
				WHERE	RADI_NUME_RADI = $nurad";
	$rsH = $db->conn->Execute($sqlH);	
		
	$codiActu = $rsH->fields['SGD_MRD_CODIGO'];
	$i = 0;
		
	while (!$rsH->EOF) {
	    $codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
	    $i++;
	    $rsH->MoveNext();
	}
		
	$TRD = $codiActu;
	include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
	      
	$observa 	= "Observación: ".$just;
	$Historico 	= new Historico($db);
	$radiModi 	= $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 34);
		
	//Actualiza el campo tdoc_codi de la tabla Radicados		
	$radiUp 	= $trd->actualizarTRD($codiRegH, $tdoc);
	
	##################################################################################
	### SI LA TRD ANTERIOR ERA UNA PQR, SE ENVIA CORREO ELECTRONICO A LAS PERSONAS
	### QUE TIENEN ACTIVO EL PERMISO DE COPIAS DE ALERTAS PQR.
	
	$tipoRad = substr($radiUp[0], -1, 1);
	
	if ($radiUp[0]){
		if ($deta_pqr == 1  && $tipoRad == 2) {
			
			### SE CONSULTA LA DESCRIPCION DEL TIPO DOCUMENTAL ANTERIOR
			$sqlTd = "	SELECT	SGD_TPR_DESCRIP
						FROM	SGD_MRD_MATRIRD M
								JOIN SGD_TPR_TPDCUMENTO T ON
									T.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
						WHERE	M.SGD_MRD_CODIGO = " .$codiActu;
			$tDocAnt = $db->conn->Getone($sqlTd);
			
			### SE CONSULTA LA DESCRIPCION DEL TIPO DOCUMENTAL NUEVO
			$sqlTipo = "	SELECT	SGD_TPR_DESCRIP
						FROM	SGD_TPR_TPDCUMENTO
						WHERE	SGD_TPR_CODIGO = " . $tdoc;
			$tDocNew = $db->conn->Getone($sqlTipo);
			
			### CAPTURA DE VARIABLES PARA ENVIAR EL CORREO
			$asunto = "Orfeo-DNP Modificación de TRD al radicado: ".$radiUp[0];
			$cuerpo = "Al siguiente No. de Radicado: <b/> ".$radiUp[0]." </b/> se le acaba de modificar la clasificación TRD, 
						de <b/> ".$tDocAnt." </b/> a <b/>".$tDocNew."</b/>, con la observaci&oacute;n: '".$just."', por favor verificar.";
			
			###	CONSULTA DEL CORREO DE LOS USUARIOS A LOS CUALES SE LES DEBE ENVIAR LAS ALERTAS
			$correos = array();
			$cc = array();
			$sqlCorreos = "	SELECT	DISTINCT USUA_EMAIL
							FROM    USUARIO
							WHERE   USUA_PERM_CC_ALAR = 1 AND
									USUA_EMAIL != ''";
			$rsCorreos = $db->conn->execute($sqlCorreos);

			while(!$rsCorreos->EOF) {
				$correos[] = $rsCorreos->fields['USUA_EMAIL'];
				$rsCorreos->MoveNext();
			}
			
			###	CUENTA DE CORREO PARA ENVIAR LAS COPIAS OCULTAS
			$cco = 'ajmartinez@dnp.gov.co';
			
			$objMail = new correoElectronico("..");

			$objMail->FromName = "Notificaciones Orfeo";
			$result = $objMail->enviarCorreo($correos, $cc, array($cco), $asunto, $cuerpo);
			
			echo $result;
			unset($correos);
			unset($usuaCC);
			unset($cco);
			
		}
	}
	### FIN - ENVIÓ DE ALERTA POR CAMBIO DE TRD EN UNA PQR
	##############################################################################
	
	
	$mensaje 	= "Registro Modificado";
	$isqlTRD 	= "	SELECT	SGD_MRD_CODIGO 
	      			FROM	SGD_MRD_MATRIRD 
	      			WHERE	DEPE_CODI = '$coddepe'
							AND SGD_SRD_CODIGO = '$codserie'
							AND SGD_SBRD_CODIGO = '$tsub'
							AND SGD_TPR_CODIGO = '$tdoc'";
		      
	$rsTRD 		= $db->conn->Execute($isqlTRD);
	$codiTRDU 	= $rsTRD->fields['SGD_MRD_CODIGO'];
		
	$sqlUA 		= "	UPDATE	SGD_RDF_RETDOCF 
					SET		SGD_MRD_CODIGO = '$codiTRDU',
							USUA_CODI = '$codusua',
							DEPE_CODI =  $coddepe,
							USUA_DOC = '$usua_doc'	
	      			WHERE	RADI_NUME_RADI = $nurad ";
							
	$rsUp = $db->conn->Execute($sqlUA);
		
	//Se guarda el registro en el historico de TRD
	$isqlTRD = "SELECT	SGD_MRD_CODIGO
				FROM	SGD_MRD_MATRIRD
				WHERE	DEPE_CODI = '$dependencia'
						AND SGD_SRD_CODIGO = '$codserie'
						AND SGD_SBRD_CODIGO = '$tsub'
						AND SGD_TPR_CODIGO = '$tdoc'";
					
	$rsTRD = $db->conn->Execute($isqlTRD);		    		
	$codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
    	
	$queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(	SGD_HMTD_FECHA,
														RADI_NUME_RADI,
														USUA_CODI,
														SGD_HMTD_OBSE,
														USUA_DOC,
														DEPE_CODI,
														SGD_MRD_CODIGO,
														SGD_TTR_CODIGO)
									VALUES(	".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
											$nurad,
											$codusua,
											'$just',
											$usua_doc,
											$dependencia,
											'$codiTRD',
											34)";	

	$ejecutarQuerey	= $db->conn->Execute($queryGrabar);
		
    if(empty($ejecutarQuerey)){
    	echo 'No se guardo el registro en historico documental';
		echo $queryGrabar;
    }
		
	$mensaje = "Registro Modificado   <br> ";		
}

$tdoc = '';
$tsub = '';
$codserie = '';
?>
</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
<br>
<div align="center">
<p>
<?=$mensaje?>
</p>
<input type='button' value='   Cerrar   ' class='botones_largo' onclick='opener.regresar();window.close();'>
</body>
</html>