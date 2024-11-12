<?php
$dependencia = (!empty($_POST["dependencia_busq"])) ? $_POST["dependencia_busq"] : $_GET["dependencia_busq"] ;
$tipoRadicado = (!empty($_POST["tipoRadicado"])) ? $_POST["tipoRadicado"] : $_GET["tipoRadicado"];
$usuaDoc = (!empty($_POST["codus"])) ? $_POST["codus"] : $_GET["codus"];
$radi_nume_radi = "CONVERT(varchar(15),RADI.RADI_NUME_RADI)";
$radiNumeSalida = "CONVERT(varchar(15),ANE.RADI_NUME_SALIDA)";

$coltp3Esp = '"'.$tip3Nombre[3][2].'"';	
if(!$orno) $orno=1;
$orderE = "	ORDER BY $orno $ascdesc ";

$desde = $fecha_ini. " ". "00:00:00";
$hasta = $fecha_fin. " ". "23:59:59";

// Si envio tipo de radicado aplicar filtro
$filtroRadicado = (!empty($tipoRadicado)) ? "AND RADI.RADI_TIPORAD = $tipoRadicado " : "";

$condicionE = (!empty($filtroRadicado) && $_POST["dependencia_busq"] != 99999) ? " AND " : "";
$condicionE .= ($_POST["dependencia_busq"] != 99999) ? "RADI.RADI_DEPE_RADI=$dependencia_busq " : "";

$sWhereFec = (!empty($condicionE) || !empty($filtroRadicado)) ? " AND " : "";

$sWhereFec = "AND " . $db->conn->SQLDate('Y/m/d H:i:s', 'RADI.RADI_FECH_RADI')." >= '$desde'
				and ".$db->conn->SQLDate('Y/m/d H:i:s', 'RADI.RADI_FECH_RADI')." <= '$hasta'";

$sWhereFecDet = "AND " . $db->conn->SQLDate('Y/m/d H:i:s', 'RADI.RADI_FECH_RADI')." >= '".$_GET["fechMin"]."'
				and ".$db->conn->SQLDate('Y/m/d H:i:s', 'RADI.RADI_FECH_RADI')." <= '" . $_GET["fechMax"]. "'";
$whereDependencia = (!empty($dependencia) && $dependencia != 99999) ? "RADI.RADI_DEPE_RADI = " . $dependencia ." AND ": "";
$whereDependencia = ($dependencia != 99999 && !empty($dependencia)) ? " AND HIST.DEPE_CODI_DEST = " . $dependencia : "";
$filtrarUsuario =  ($usuaDoc == 0) ? "(SELECT USUA_DOC FROM USUARIO WHERE USUA_LOGIN = '$krd')" : 
					"(SELECT USUA_DOC 
						FROM USUARIO 
						WHERE USUA_CODI = $usuaDoc AND 
							DEPE_CODI = (SELECT DEPE_CODI
									FROM USUARIO
									WHERE USUA_LOGIN = '$krd'))";
$filtroAsig = ($usuaDoc == 0) ?  " OR (USUA_DOC = (SELECT USUA_DOC
							FROM USUARIO
							WHERE USUA_LOGIN = '$krd'))": "";
switch($db->driver)
{	case 'mssqlnative':
		$queryE = "SELECT " . $db->conn->SQLDate('M-Y', 'RADI.RADI_FECH_RADI')." FECHA_RADICADOS, 
					COUNT(RADI.RADI_NUME_RADI) AS RADICADOS,
					MAX(".$db->conn->SQLDate('Y/m/d H:i:s', 'RADI.RADI_FECH_RADI').") HID_FECH_MAX,
					MIN(".$db->conn->SQLDate('Y/m/d H:i:s', 'RADI.RADI_FECH_RADI').") HID_FECH_MIN
				FROM RADICADO RADI
				WHERE 	RADI.RADI_NUME_RADI IN (SELECT DISTINCT RADI_NUME_RADI
                            					FROM HIST_EVENTOS HIST
                            					WHERE (DEPE_CODI_DEST = (SELECT DEPE_CODI 
												FROM SGD_USD_USUADEPE
												WHERE USUA_LOGIN = '$krd' AND 
                                                    DEPE_CODI = $dependencia_busq)) AND
									(HIST_DOC_DEST = $filtrarUsuario AND (SGD_TTR_CODIGO = 9)) 
									$filtroAsig AND
									HIST.SGD_TTR_CODIGO = 9)
					$filtroRadicado $sWhereFec
				GROUP BY ".$db->conn->SQLDate('M-Y', 'RADI.RADI_FECH_RADI')."
				ORDER BY $orno $ascdesc";
		$sqlAneSinS = "SELECT $radi_nume_radi AS RADICADOS,
					" . $db->conn->SQLDate('Y/m/d H:i:sA', 'RADI.RADI_FECH_RADI') . " AS FECHA_RADICADO,
					$radiNumeSalida AS ANEXO,
					TPR.SGD_TPR_DESCRIP AS TIPO_DOCUMENTAL,
					RADI.RA_ASUN AS ASUNTO_ANEXO,
					USUA.USUA_NOMB USUARIO_ACTUAL,
					DEPE.DEPE_NOMB DEPENDENCIA_ACTUAL,
					ANE.ANEX_BORRADO HID_ANEXO_BORRADO,
					ANE.ANEX_NOMB_ARCHIVO HID_NOMBRE_ARCHIVO
				FROM RADICADO RADI LEFT OUTER JOIN ANEXOS ANE 
					ON RADI.RADI_NUME_RADI = ANE.ANEX_RADI_NUME, 
					DEPENDENCIA DEPE,
					USUARIO USUA,
					SGD_TPR_TPDCUMENTO TPR,
                    SGD_USD_USUADEPE USD
				WHERE USD.DEPE_CODI = RADI.RADI_DEPE_ACTU AND
                    USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                    USUA.USUA_DOC = USD.USUA_DOC AND
					USUA.USUA_CODI = RADI.RADI_USUA_ACTU AND
					RADI.TDOC_CODI = TPR.SGD_TPR_CODIGO AND
					RADI.RADI_DEPE_ACTU = USUA.DEPE_CODI AND
					ANE.ANEX_BORRADO <> 'S' AND
					RADI.RADI_NUME_RADI IN (SELECT DISTINCT RADI_NUME_RADI
                            					FROM HIST_EVENTOS HIST
                            					WHERE (DEPE_CODI_DEST = (SELECT DEPE_CODI 
												FROM USUARIO
												WHERE USUA_LOGIN = '$krd')) AND
									(HIST_DOC_DEST = $filtrarUsuario AND (SGD_TTR_CODIGO = 9))
									$filtroAsig AND
									HIST.SGD_TTR_CODIGO = 9)
					$filtroRadicado $sWhereFecDet";
		
		$sqlAneNull = "SELECT $radi_nume_radi AS RADICADOS,
                        " . $db->conn->SQLDate('Y/m/d H:i:sA', 'RADI.RADI_FECH_RADI') . " AS FECHA_RADICADO,
                        $radiNumeSalida AS ANEXO,
                        TPR.SGD_TPR_DESCRIP AS TIPO_DOCUMENTAL,
                        RADI.RA_ASUN AS ASUNTO_ANEXO,
                        USUA.USUA_NOMB USUARIO_ACTUAL,
                        DEPE.DEPE_NOMB DEPENDENCIA_ACTUAL,
                        ANE.ANEX_BORRADO HID_ANEXO_BORRADO,
                        ANE.ANEX_NOMB_ARCHIVO HID_NOMBRE_ARCHIVO
				FROM RADICADO RADI LEFT OUTER JOIN ANEXOS ANE 
                        ON RADI.RADI_NUME_RADI = ANE.ANEX_RADI_NUME, 
                        DEPENDENCIA DEPE,
                        USUARIO USUA,
                        SGD_TPR_TPDCUMENTO TPR
				WHERE	DEPE.DEPE_CODI = RADI.RADI_DEPE_ACTU AND
                        USUA.USUA_CODI = RADI.RADI_USUA_ACTU AND
                        RADI.TDOC_CODI = TPR.SGD_TPR_CODIGO AND
                        RADI.RADI_DEPE_ACTU = USUA.DEPE_CODI AND
                        ANE.ANEX_BORRADO IS NULL AND
                        RADI.RADI_NUME_RADI IN (SELECT DISTINCT RADI_NUME_RADI
                            					FROM HIST_EVENTOS HIST
                            					WHERE (DEPE_CODI_DEST = (SELECT DEPE_CODI 
												FROM USUARIO
												WHERE USUA_LOGIN = '$krd')) AND
									(HIST_DOC_DEST = $filtrarUsuario AND (SGD_TTR_CODIGO = 9))
									$filtroAsig AND
									HIST.SGD_TTR_CODIGO = 9)
					$filtroRadicado $sWhereFecDet";
		$queryEDetalle = "($sqlAneSinS) UNION ALL ($sqlAneNull) ORDER BY $orno $ascdesc";
		break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
}
?>
