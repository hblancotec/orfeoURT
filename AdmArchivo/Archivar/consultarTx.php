<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_archivo'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

if (!$_POST && !$_GET){ 
	header("Location: consultar.php");
	die;
}
	
	include_once ("../../include/db/ConnectionHandler.php");
	$db = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	
	$usrDoc = $_SESSION['usua_doc'];
	$usrDep = $_SESSION['dependencia'];
	$usrCod = $_SESSION['codusuario'];
	
	if($busAno == 9999){
		$whereAno = "";
	}
	else{
		$whereAno = "S.SGD_SEXP_ANO = ".$busAno. "AND";
	}

	###########################################################################
	### REALIZA EL REGISTRO DE LA ASIGNACION DE UBICACION A UN RADICADO
	if ($_POST['Archivar'] || $_POST['Excluir']){
		
		$sqlFecha = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
		
		## SE RECORREN LOS RADICADOS QUE HAYAN SIDO MARCADOS EN EL FORMULARIO
		foreach ($_POST['marcado'] as $key => $value) {
			
			$rad = substr($key, 0, 15);
			$exp = substr($key, 16, 20);
			
			/*if ($folios[$key]){
				$fol = ", SGD_EXP_FOLIOS = ". $folios[$key];
			}
			else {
				$fol = "";
				}*/
			if ($folios[$key]){
				$fol = $folios[$key];
			}
			else {
			    $fol = 0;
			}
			
			/*if ($carpeta[$key]){
				$car = ", SGD_EXP_CARPETA = ".$carpeta[$key];
			}
			else{
				$car = "";
			}*/
			if ($carpeta[$key]){
			    $car = $carpeta[$key];
			}
			else{
			    $car = 0;
			}
			
			if (strlen($datos) > 5) {
			    $datos .= ",";
			}
			$datos .= "$exp-$rad-$fol-$car";
			    
			$verifica = "	SELECT	SGD_EXP_UFISICA, SGD_EXP_ESTADO
							FROM	SGD_EXP_EXPEDIENTE
							WHERE	SGD_EXP_NUMERO = '$exp' AND
									RADI_NUME_RADI = $rad";
			$rsVerifica = $db->conn->Getone($verifica);
			
			
			### SE ARCHIVAN FISICAMENTE LOS RADICADOS
			if($_POST['Archivar'] && $rsVerifica == 1){
				$msg = "El radicado $rad que intenta Archivar en el expediente $exp, ya se encuentra ARCHIVADO, por favor verifique";
			}
						
			elseif ($_POST['Archivar']){
			    
			    $tipo = 1;

				/*$sqlUpd = "	UPDATE	SGD_EXP_EXPEDIENTE
							SET		RADI_USUA_ARCH = '".$usrDoc."',
									SGD_EXP_FECH_ARCH = $sqlFecha,
									SGD_EXP_UFISICA	= 1"
									.$fol
									.$car. "
							WHERE	SGD_EXP_NUMERO = '".$exp."' AND
									RADI_NUME_RADI = ".$rad;
				$rsUpd = $db->conn->Execute($sqlUpd);
				$cntAffect = $db->conn->Affected_Rows();

				### SI SE ACTUALIZO LA TABLA SGD_EXP_EXPEDIENTE, SE REGISTRA EN EL HISOTRICO DEL RADICADO
				if ($cntAffect > 0){
					$archivados = $archivados. " <br> ".$rad." --> " .$exp;
					$msg = "Se archivaron correctamente los siguientes registros: ".$archivados;
					$sqlFecha = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);

					$obs = iconv("iso-8859-1", "utf-8", "Archivado físicamente");

					$sqlIns = "	INSERT INTO HIST_EVENTOS (	DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI,
								    HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO, HIST_DOC_DEST,
                                    DEPE_CODI_DEST)
								VALUES	(	".$usrDep.", ".$sqlFecha.", ".$usrCod.", ".$rad.", '".$obs."',
									".$usrCod.", '".$usrDoc."', 57, '".$usrDoc."', ".$usrDep.")";
					$rsIns = $db->conn->Execute($sqlIns);
					
					//DEVOLVER PRESTAMO
					$sqlEval = "SELECT PRES_ID, PRES_ESTADO FROM PRESTAMO WHERE RADI_NUME_RADI = $rad ";
					$rsEval = $db->conn->query($sqlEval);
					if ($rsEval && !$rsEval->EOF)
					{
					    $sfldPRES_ID = $rsEval->fields["PRES_ID"];
					    $estadoActual = $rsEval->fields["PRES_ESTADO"];
					    if ($estadoActual == 2 || $estadoActual == 6 || $estadoActual == 7) {
					
					        $fldPRES_FECH = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
					        $fldDESC = iconv("iso-8859-1", "utf-8", "Devolución de Documento");
					        $setFecha = "PRES_FECH_DEVO= " . $fldPRES_FECH . ", DEV_DESC= '" . $fldDESC . "', USUA_LOGIN_RX='" . $krd . "' ";
					        
        					$sSQL = "	UPDATE	PRESTAMO
        						SET		" . $setFecha . ", PRES_ESTADO = 4
        						WHERE	PRES_ID = " . $sfldPRES_ID;
        					
        					if ($db->conn->Execute($sSQL)) {
        					    
        					    $obs = iconv("iso-8859-1", "utf-8", "Devolver documento: Devolución de documento");
        					    
        					    $sqlIns = "	INSERT INTO HIST_EVENTOS (	DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI,
								    HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO, HIST_DOC_DEST,
                                    DEPE_CODI_DEST)
								VALUES	(	".$usrDep.", ".$sqlFecha.", ".$usrCod.", ".$rad.", '".$obs."',
									".$usrCod.", '".$usrDoc."', 75, '".$usrDoc."', ".$usrDep.")";
        					    $rsIns = $db->conn->Execute($sqlIns);
        					    
        					}
					    }
					}
				}
				else {
					$msg = "No se pudo archivar el Radicado No. ".$busRad." del Expediente No. ".$exp.", 
						por favor intentelo de nuevo. <br/> En caso de persistir esta situaci&oacute;n comuniquese con el Administrador del Sistema";
				}*/
			}

			
			### SE EXCLUYEN FISICAMENTE LOS RADICADOS
			elseif($_POST['Excluir'] && $rsVerifica == 2){
				$msg = "El radicado $rad que intenta Excluir, ya fue no esta incluido en el Expediente $exp, por favor verifique";
			}
			
			elseif ($_POST['Excluir'] && $rsVerifica == NULL ){
				$msg = "El radicado $rad que intenta Excluir, no tiene registro de Archivo físico en el Expediente $exp, por favor verifique";
			}
			
			elseif($_POST['Excluir']){
			    
			    $tipo = 2;
				/*$sqlUpd = "	UPDATE	SGD_EXP_EXPEDIENTE
							SET		SGD_EXP_UFISICA	= 2,
									SGD_EXP_FOLIOS = 0,
									SGD_EXP_CARPETA = 0
							WHERE	SGD_EXP_NUMERO = '".$exp."' AND
									RADI_NUME_RADI = ".$rad;
				$rsUpd = $db->conn->Execute($sqlUpd);
				$cntAffect = $db->conn->Affected_Rows();

				### SI SE ACTUALIZO LA TABLA SGD_EXP_EXPEDIENTE, SE REGISTRA EN EL HISOTRICO DEL RADICADO
				if ($cntAffect > 0){
					$excluidos = $excluidos. " <br> ".$rad." --> " .$exp;
					$msg = "Se excluyeron correctamente los siguientes registros: ".$excluidos;
					$sqlFecha = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);

					$obs = iconv("iso-8859-1", "utf-8", "Excluido físicamente");

					$sqlIns = "	INSERT INTO HIST_EVENTOS (	DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI,
                        HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO, HIST_DOC_DEST, DEPE_CODI_DEST)
						VALUES	(	".$usrDep.", ".$sqlFecha.", ".$usrCod.", ".$rad.", '".$obs."',
							".$usrCod.", '".$usrDoc."', 99, '".$usrDoc."', ".$usrDep.")";
					$rsIns = $db->conn->Execute($sqlIns);
				}
				else {
					$msg = "No se pudo excluir el Radicado No. ".$busRad." del Expediente No. ".$exp.", 
						por favor intentelo de nuevo. <br/> En caso de persistir esta situaci&oacute;n comuniquese con el Administrador del Sistema";
				}*/
			}	
		}

		$st = " DECLARE @return_value int
		
                EXEC @return_value = [dbo].[ArchivarArchivo]
                                    @List = '$datos',
                                    @USUA_DOC = '$usrDoc',
                            		@DEPE_CODI = '$usrDep',
                            		@USUA_CODI = '$usrCod',
                                    @USUA_LOGIN = '$krd',
                                    @TIPO = $tipo";
		$rs = $db->conn->Execute($st);
		if ($rs) {
		    //echo "Se ejecuto";
		} else {
		    //echo "No se ejecuto ". "-" . $datos ."-" . $usrDoc . "-" . $usrDep ."-". $usrCod."-".$krd. "-".$tipo;
		}
	}
	###########################################################################

	

	###########################################################################
	if ($busRad || $busDep || $busExp) {
		
		### SE VERIFICA EL ESTADO SELECCIONADO EN LA CONSULTA
		switch ($estado) {
			case 0:
				$wEst = "VISTA.ESTADO <> 'EXCLUIDOS' ";

				break;
			case 1:
				$wEst = " VISTA.ESTADO = 'ARCHIVADO' ";
				break;
			case 2:
				$wEst = " VISTA.ESTADO = 'SIN ARCHIVAR'";
				break;
			case 3:
				$wEst = " VISTA.ESTADO = 'PARA EXCLUIR'";
				break;
		}
		
		### SI SE DIGITO NUMERO DE RADICADO
		if ($busRad) {
			$busExp = false;
			$busDep = false;
			$wRadExp =	" WHERE E.RADI_NUME_RADI = ".$busRad;
		}

		### SI SE DIGITO NUMERO DE EXPEDIENTE
		elseif ($busExp){
			$busDep = false;
			$wRadExp =	" WHERE E.SGD_EXP_NUMERO = '".$busExp."'";
		}

		### SI NO SE DIGITO NI RADICADO NI EXPEDIENTE
		elseif ($busDep) {
			if ($busDep == '9999'){
				$whereDep = '';
			}
			else {
				$whereDep = ' S.DEPE_CODI = '.$busDep."AND ";
				if ($busSer == 22222){
					$whereSer = '';
				}
				else{
					$whereSer = ' S.SGD_SRD_CODIGO = '.$busSer."AND ";
					if ($busSub == '33333'){
						$whereSub = '';
					}
					else{
						$whereSub = ' S.SGD_SBRD_CODIGO = '.$busSub."AND ";
						$flag = 1;
					}
				}
			}
		}


		###########################################################################
		### CONSULTA POR RADICADO O EXPEDIENTE

		if ($busRad || $busExp){
			$sqlArch = "SELECT VISTA.*
						FROM (	SELECT	E.RADI_NUME_RADI AS RADICADO,
										R.RADI_FECH_RADI AS FECHA_RAD,
										R.RADI_NUME_HOJA AS NO_HOJA,
										T.SGD_TPR_DESCRIP AS TIPO_DOCUMENTAL,
										E.SGD_EXP_NUMERO AS EXPEDIENTE,
										D.DEPE_NOMB AS DEPENDENCIA,
										SR.SGD_SRD_DESCRIP AS SERIE,
										SB.SGD_SBRD_DESCRIP AS SUBSERIE,
										S.SGD_SEXP_ANO AS ANO,
										E.SGD_EXP_CARPETA AS CARPETA,
										E.SGD_EXP_FOLIOS AS FOLIOS,
										CASE WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 2	THEN 'PARA EXCLUIR' 
											 WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
											 WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
											 WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR' 
											 WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR'
											 WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 0	THEN 'ARCHIVADO'END AS ESTADO,
										CONVERT(VARCHAR(15),E.RADI_NUME_RADI) + E.SGD_EXP_NUMERO AS CHK_RADICADO
								FROM	SGD_EXP_EXPEDIENTE AS E JOIN RADICADO AS R ON R.RADI_NUME_RADI = E.RADI_NUME_RADI
										JOIN SGD_TPR_TPDCUMENTO AS T ON T.SGD_TPR_CODIGO = R.TDOC_CODI
										JOIN SGD_SEXP_SECEXPEDIENTES AS S ON S.SGD_EXP_NUMERO = E.SGD_EXP_NUMERO
										JOIN DEPENDENCIA AS D ON D.DEPE_CODI = S.DEPE_CODI
										JOIN SGD_SRD_SERIESRD AS SR ON SR.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
										JOIN SGD_SBRD_SUBSERIERD AS SB ON SB.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO AND SB.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
                                        LEFT JOIN MEDIO_RECEPCION M ON M.MREC_CODI = R.MREC_CODI
	                                    LEFT JOIN SGD_FENV_FRMENVIO F ON R.SGD_FENV_CODIGO = F.SGD_FENV_CODIGO
								$wRadExp ) AS VISTA
						WHERE	$wEst
						ORDER BY VISTA.FECHA_RAD";
			
			 $st = " DECLARE @return_value int
								
                EXEC @return_value = [dbo].[CONSULTA]
                                    @querry = $sqlArch ";
		     //$rsExp = $db->conn->Execute($st);
								
			$rsExp = $db->conn->Execute($sqlArch);
		}
		###########################################################################



		###########################################################################
		### CONSULTA POR DEPENDENCIA
		if ($busDep){

			### SI VIENE DEP. SERIE, SUB-SERIE Y AÑO
			if ($flag == 1){
				$sqlDep = "	SELECT VISTA.*
							FROM  (	SELECT	D.DEPE_NOMB,
											SR.SGD_SRD_DESCRIP,
											SB.SGD_SBRD_DESCRIP,
											S.SGD_SEXP_ANO,
											S.SGD_EXP_NUMERO,
											S.SGD_SEXP_PAREXP1,
											CASE WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 2	THEN 'PARA EXCLUIR' 
													 WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
													 WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
													 WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR' 
													 WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR'
													 WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 0	THEN 'ARCHIVADO' END AS ESTADO,
											COUNT(E.RADI_NUME_RADI) AS CANT
									FROM	SGD_SEXP_SECEXPEDIENTES AS S
											JOIN DEPENDENCIA AS D ON
												D.DEPE_CODI = S.DEPE_CODI
											JOIN SGD_SRD_SERIESRD AS SR ON
												SR.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
											JOIN SGD_SBRD_SUBSERIERD AS SB ON
												SB.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO AND
												SB.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
											JOIN SGD_EXP_EXPEDIENTE AS E ON
												E.SGD_EXP_NUMERO = S.SGD_EXP_NUMERO 
									WHERE	$whereDep 
											$whereSer 
											$whereSub
											$whereAno
											S.SGD_SEXP_ESTADO = 0
									GROUP BY	D.DEPE_NOMB, SR.SGD_SRD_DESCRIP, SB.SGD_SBRD_DESCRIP, S.SGD_SEXP_ANO, S.SGD_EXP_NUMERO, 
												S.SGD_SEXP_PAREXP1, E.SGD_EXP_UFISICA, E.SGD_EXP_ESTADO 
								  ) AS VISTA
							WHERE	$wEst
							ORDER BY VISTA.SGD_EXP_NUMERO";
			}

			### SI NO VIENE LA SUB-SERIE
			else {
				$sqlDep = "	SELECT	S.DEPE_CODI,
									D.DEPE_NOMB,
									S.SGD_SRD_CODIGO,
									SR.SGD_SRD_DESCRIP,
									S.SGD_SBRD_CODIGO,
									SB.SGD_SBRD_DESCRIP,
									S.SGD_SEXP_ANO,
									COUNT (VISTA.EXPE) AS CANT
							FROM	(	SELECT	E.SGD_EXP_NUMERO AS EXPE, 
												CASE	WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 2	THEN 'PARA EXCLUIR' 
														WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
														WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
														WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR' 
														WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR'
														WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 0	THEN 'ARCHIVADO' END AS ESTADO,
												COUNT(E.SGD_EXP_NUMERO) AS CANT 
										FROM	SGD_EXP_EXPEDIENTE AS E
										WHERE	( ISNULL( E.SGD_EXP_ESTADO, 0) <> 2 OR ISNULL( E.SGD_EXP_UFISICA, 0) <> 2 )		
										GROUP BY E.SGD_EXP_NUMERO, E.SGD_EXP_UFISICA, E.SGD_EXP_ESTADO
									) AS VISTA
									JOIN SGD_SEXP_SECEXPEDIENTES S ON 
										$whereDep
										$whereAno
										$whereSer
										S.SGD_EXP_NUMERO = VISTA.EXPE AND 
										S.SGD_SEXP_ESTADO = 0
									JOIN DEPENDENCIA AS D ON
										D.DEPE_CODI = S.DEPE_CODI
									JOIN SGD_SRD_SERIESRD AS SR ON
										SR.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
									JOIN SGD_SBRD_SUBSERIERD AS SB ON
										SB.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO AND
										SB.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
							WHERE	$wEst			
							GROUP BY	S.DEPE_CODI, D.DEPE_NOMB, S.SGD_SRD_CODIGO, SR.SGD_SRD_DESCRIP, 
										S.SGD_SBRD_CODIGO, SB.SGD_SBRD_DESCRIP, S.SGD_SEXP_ANO
							ORDER BY DEPE_CODI, S.SGD_SRD_CODIGO, S.SGD_SBRD_CODIGO";


			}
			$rsDep = $db->conn->Execute($sqlDep);
			if ($rsDep){
				$vacio = true;
			}
		}
		###########################################################################


		###########################################################################
		### CONSULTA PARA EXPORTAR A EXCEL CON TODOS LOS DETALLES

		$sqlExcel =	"	SELECT VISTA.*
						FROM (	SELECT	D.DEPE_NOMB DEPENDENCIA,
										SR.SGD_SRD_DESCRIP SERIE,
										SB.SGD_SBRD_DESCRIP SUB_SERIE,
										S.SGD_SEXP_ANO AÑO,
										E.SGD_EXP_NUMERO NÚMERO_EXP,
										S.SGD_SEXP_PAREXP1 NOMBRE_EXP,
										E.RADI_NUME_RADI RADICADO,
										R.RADI_FECH_RADI FECHA_RADICADO,
										T.SGD_TPR_DESCRIP TIPO_DOCUMENTAL,
										R.RADI_NUME_HOJA No_HOJAS,
										E.SGD_EXP_CARPETA CARPETA,
										E.SGD_EXP_FOLIOS FOLIOS,
										CASE WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 2	THEN 'PARA EXCLUIR' 
											 WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
											 WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 2	THEN 'EXCLUIDOS'
											 WHEN E.SGD_EXP_UFISICA IS NULL AND	E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR' 
											 WHEN E.SGD_EXP_UFISICA = 2 AND		E.SGD_EXP_ESTADO = 0	THEN 'SIN ARCHIVAR'
											 WHEN E.SGD_EXP_UFISICA = 1 AND		E.SGD_EXP_ESTADO = 0	THEN 'ARCHIVADO'END AS ESTADO
								FROM	SGD_EXP_EXPEDIENTE AS E
										JOIN RADICADO AS R ON
											R.RADI_NUME_RADI = E.RADI_NUME_RADI
										JOIN SGD_TPR_TPDCUMENTO AS T ON
											T.SGD_TPR_CODIGO = R.TDOC_CODI
										JOIN SGD_SEXP_SECEXPEDIENTES AS S ON
											$whereDep
											$whereAno
											$whereSer
											S.SGD_EXP_NUMERO = E.SGD_EXP_NUMERO
										JOIN DEPENDENCIA AS D ON
											D.DEPE_CODI = S.DEPE_CODI
										JOIN SGD_SRD_SERIESRD AS SR ON
											SR.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
										JOIN SGD_SBRD_SUBSERIERD AS SB ON
											SB.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO AND
											SB.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
								$wRadExp ) AS VISTA
						WHERE	$wEst
						ORDER BY VISTA.FECHA_RADICADO";

		$rsExcelDetalle = $db->conn->Execute($sqlExcel);

		###########################################################################



		###########################################################################
		### CONSULTA DE LA CANTIDAD DE ANEXOS QUE TIENE UN EXPEDIENTE
		if ($exp){
			$sqlAnex = "SELECT	COUNT (SGD_EXP_NUMERO) AS CANTIDAD
						FROM	SGD_ANEXOS_EXP
						WHERE	SGD_EXP_NUMERO = '$exp' AND
								ANEXOS_EXP_ESTADO <> 1";

			$rsAnex = $db->conn->GetOne($sqlAnex);
		}
		###########################################################################
	}
	###########################################################################
?>