<?php
$fechafiltro = " and r.RADI_FECH_RADI BETWEEN " . $db->conn->DBTimeStamp($fecha_ini . " 00:00:01") . " and " . $db->conn->DBTimeStamp($fecha_fin . " 23:59:59");
if ($dependencia_busq == 99999) {
    $dependencia_busq2 = "";
} else {

    $dependencia_busq2 = " and $dependencia_busq in (SELECT H.DEPE_CODI_DEST
                FROM RADICADO R1 INNER JOIN HIST_EVENTOS H ON R1.RADI_NUME_RADI = H.RADI_NUME_RADI
                WHERE (H.SGD_TTR_CODIGO = 2 OR H.SGD_TTR_CODIGO = 9) AND R1.RADI_NUME_RADI = R.RADI_NUME_RADI )";
    // $dependencia_busq2 = " and r.RADI_DEPE_ACTU IN ($dependencia_busq)";
}

if ($SelPqr)
    $wherePqr = "AND s.SGD_TPR_REPORT1 = 1 ";
else
    $wherePqr = " ";

$resulta = (empty($med_rec2)) ? "" : implode(',', $med_rec2);
$medio_busq = (empty($resulta)) ? "" : " and r.MREC_CODI IN ($resulta)";
$temasFrom = "LEFT OUTER JOIN dbo.SGD_CAUX_CAUSALES caux
    						ON (r.RADI_NUME_RADI=caux.RADI_NUME_RADI)";
// $temasSelect 0 Todos los radicados, 99999 Solo los que poseen temas
if ($temaSelect == "99999") {
    $temasWhere .= " AND caux.SGD_DCAU_CODIGO>=1 ";
} elseif ($temaSelect == 0) {
    $temasWhere = "";
} else {
    $temasWhere .= " AND caux.SGD_DCAU_CODIGO=$temaSelect ";
}
if ($temasWhere) {
    $temasWhere = "AND r.RADI_NUME_RADI IN (SELECT caux.radi_nume_radi from SGD_CAUX_CAUSALES caux  WHERE (r.RADI_NUME_RADI=caux.RADI_NUME_RADI $temasWhere  ))";
}
$radiFechradi1 = $db->conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI');
$radiFechradi2 = $db->conn->Concat($db->conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI'), "' 00:00:00'");
// echo "temas... ".$temasWhere;
$histFech = $db->conn->SQLDate('Y-m-d', 'h.HIST_FECH') . $db->conn->concat_operator . "' 00:00:00'";
$fechResp1 = "(	SELECT	TOP 1 " . $db->conn->SQLDate('Y-m-d', 'h.HIST_FECH') . " AS FECHA_RESP
    						FROM	RADICADO RA
    								,ANEXOS A
    								,HIST_EVENTOS H
    						WHERE	A.ANEX_RADI_NUME = RA.RADI_NUME_RADI
    								AND (convert(varchar(15), A.RADI_NUME_SALIDA) LIKE '%[136]')
    								AND A.RADI_NUME_SALIDA = H.RADI_NUME_RADI
    								AND H.SGD_TTR_CODIGO IN (42,22,23)
    								AND RA.RADI_NUME_RADI = R.RADI_NUME_RADI
    						ORDER BY FECHA_RESP)";

$fechResp2 = "	SELECT	TOP 1 " . $db->conn->SQLDate('Y-m-d', 'h.HIST_FECH') . "+' 00:00:00 ' AS FECHA_RESP
    						FROM	RADICADO RA
    								,ANEXOS A
    								,HIST_EVENTOS H
    						WHERE	A.ANEX_RADI_NUME = RA.RADI_NUME_RADI
    								AND (CONVERT(varchar(15), A.RADI_NUME_SALIDA) LIKE '%[136]')
    								AND A.RADI_NUME_SALIDA = H.RADI_NUME_RADI
    								AND H.SGD_TTR_CODIGO IN (42,22)
    								AND RA.RADI_NUME_RADI = R.RADI_NUME_RADI
    						ORDER BY FECHA_RESP";

$query = " SELECT	R.RADI_NUME_RADI AS RADICADO, " . $radiFechradi1 . " AS FECHA_RAD,
                        FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1,
                        CASE r.RADI_FECHA_VENCE WHEN '' THEN ''
    		              WHEN NULL THEN ''
    		              ELSE " . $db->conn->SQLDate('Y-m-d', 'r.RADI_FECHA_VENCE') . " END AS FECH_VENCE
    						, R.RADI_DIAS_VENCE
                            , R.RADI_ANONIMO AS ANONIMO
    						, S.SGD_TPR_DESCRIP AS TIPO_DOC
                            , S.SGD_TPR_TERMINO AS TERMINO
    						, M.MREC_DESC AS M_RECEP
                            , E.DEPE_NOMB AS DEPE_ACTU
    						, UA.USUA_NOMB AS USUA_ACTUAL
                            , R.RA_ASUN
    						, ME.SGD_FENV_MODALIDAD AS M_RESPUESTA_SOL
    						, (	SELECT	TOP 1 H1.HIST_OBSE
    							FROM	HIST_EVENTOS H1
    							WHERE	R.RADI_NUME_RADI = H1.RADI_NUME_RADI
    							ORDER BY H1.HIST_FECH DESC) AS HIST2
    						, (	SELECT	TOP 1 H2.HIST_OBSE
    							FROM	HIST_EVENTOS H2
    							WHERE	R.RADI_NUME_RADI = H2.RADI_NUME_RADI
    									and H2.HIST_OBSE <> (SELECT	TOP 1 H1.HIST_OBSE
    														FROM	HIST_EVENTOS H1
    														WHERE	R.RADI_NUME_RADI = H1.RADI_NUME_RADI
    														ORDER BY H1.HIST_FECH DESC)
    							ORDER BY H2.HIST_FECH DESC) AS HIST1
    						, D.SGD_DIR_NOMREMDES as REMITENTE
                            , D.SGD_DIR_DOC as DOCUMENTO
    						, DP.DPTO_NOMB AS DEPARTAMENTO
    						, MP.MUNI_NOMB AS MUNICIPIO
    						, IP.SGD_INFPOB_DESC AS INF_POBLACIONAL
    						, (CASE WHEN ME.PQRVERBAL = 1 THEN 'SI' ELSE 'NO' END) AS PQR_VERBAL
                            , D.SGD_OEM_CODIGO, D.SGD_CIU_CODIGO, D.SGD_ESP_CODI, D.SGD_DOC_FUN
    				FROM	RADICADO R LEFT JOIN USUARIO U ON R.RADI_USU_ANTE = U.USUA_LOGIN
    						LEFT JOIN SGD_DIR_DRECCIONES D ON R.radi_nume_radi = D.radi_nume_radi AND D.SGD_DIR_TIPO = 1
    						LEFT JOIN RADICADO AS R2 ON R2.RADI_NUME_RADI = R.RADI_NUME_DERI
    						LEFT JOIN USUARIO U2 ON U2.DEPE_CODI = R2.RADI_DEPE_RADI AND U2.USUA_CODI = R2.RADI_USUA_RADI
    						LEFT JOIN USUARIO UA ON UA.DEPE_CODI = R.RADI_DEPE_ACTU AND UA.USUA_CODI = R.RADI_USUA_ACTU
    						LEFT JOIN DEPARTAMENTO DP ON DP.ID_PAIS = 170 AND DP.DPTO_CODI = D.DPTO_CODI
    						LEFT JOIN MUNICIPIO MP ON MP.MUNI_CODI = D.MUNI_CODI AND MP.DPTO_CODI = D.DPTO_CODI AND MP.ID_PAIS = 170
    						LEFT JOIN SGD_PQR_METADATA AS ME ON ME.RADI_NUME_RADI = R.RADI_NUME_RADI
    						LEFT JOIN SGD_INF_INFPOB AS IP ON IP.ID_INFPOB = ME.ID_INFPOB
    						INNER JOIN SGD_TPR_TPDCUMENTO AS S ON R.TDOC_CODI = S.SGD_TPR_CODIGO
    						INNER JOIN MEDIO_RECEPCION AS M ON R.MREC_CODI = M.MREC_CODI
    						INNER JOIN dbo.DEPENDENCIA AS E ON R.RADI_DEPE_ACTU = E.DEPE_CODI
    				WHERE	r.RADI_TIPORAD = 2 and E.DEPE_CODI <> 900 
                            $temasWhere
    						$wherePqr
    						$medio_busq
    						$fechafiltro
    						$dependencia_busq2
    				ORDER BY 2 ";

function carga_radicados(&$mydata, &$result, $db)
{
    while ($result && ! $result->EOF) {
        $nroradicado = $result->fields["RADICADO"]; // ******** radicado *****//

        // Mostrar dependencia anterior
        $isqlq = "	SELECT	D.DEPE_NOMB AS DEPE_ANTE
    							FROM	RADICADO R,
    									DEPENDENCIA D,
    									USUARIO U
    							WHERE	D.DEPE_CODI		 = U.DEPE_CODI AND
    									R.RADI_USU_ANTE  = U.USUA_LOGIN AND
    									R.RADI_NUME_RADI = $nroradicado";

        $resulDepe = $db->conn->Execute($isqlq);

        // unset($temas);
        $temaRad = "";
        $producto = "";
        $sqlSelect = "SELECT	CAUX.SGD_CAUX_CODIGO,
    									CAU.SGD_CAU_DESCRIP as SECTOR,
    									DCAU.SGD_DCAU_DESCRIP as CAUSAL,
                                        DCAU.SGD_DCAU_PRODUCTO as PRODUCTO,
    									CAUX.SGD_CAUX_CODIGO,
    									CAUX.RADI_NUME_RADI COUNT_RADI,
    									CAUX.SGD_CAUX_FECHA
    							FROM	SGD_CAUX_CAUSALES CAUX,
    									SGD_CAU_CAUSAL CAU,
    									SGD_DCAU_CAUSAL DCAU
    							WHERE	RADI_NUME_RADI = $nroradicado
    									AND CAUX.SGD_DCAU_CODIGO=dcau.sgd_dcau_codigo
    									AND DCAU.SGD_CAU_CODIGO=cau.SGD_CAU_CODIGO";

        $rst = $db->conn->Execute($sqlSelect);
        while (! $rst->EOF) {
            if (strlen($temaRad) > 5) {
                $temaRad .= " -- ";
            }
            $temaRad .= $rst->fields["SECTOR"] . " / " . $rst->fields["CAUSAL"]; // ***Tema
            if (strlen($producto) > 5) {
                $producto .= " -- ";
            }
            $producto .= $rst->fields["PRODUCTO"];
            // $temas[] = $temaRad;
            $rst->MoveNext();
        }

        $radiFechradi1 = $db->conn->Concat($db->conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI'), "' 00:00:00'");
        $fechResp1 = "(	SELECT TOP 1 FORMAT(SGD_RENV_FECH, 'yyyy-MM-dd', 'co-CO' ) FROM SGD_RENV_REGENVIO
    							WHERE RADI_NUME_SAL = A.RADI_NUME_SALIDA AND NOT SGD_RENV_DIR LIKE 'COPIA%' AND SGD_DIR_TIPO = 1
                                ORDER BY SGD_RENV_FECH ) ";

        $fechResp2 = "(	SELECT TOP 1 FORMAT(SGD_RENV_FECH, 'yyyy-MM-dd', 'co-CO' ) FROM SGD_RENV_REGENVIO
    							WHERE RADI_NUME_SAL = R.RADI_NUME_RADI AND NOT SGD_RENV_DIR LIKE 'COPIA%' AND SGD_DIR_TIPO = 1
                                ORDER BY SGD_RENV_FECH ) ";

        $medRespuesol = "";
        $respuesta1 = "";
        $tipoResp1 = "";
        $responsable1 = "";
        $fechaResp1 = "";
        $imageResp1 = "";
        $imageAnex1 = "";
        $enviCorres = "";
        $expedient = "";
        $nomExpedi = "";
        $diasResp1 = "";
        $pregunta1 = "";
        $pregunta2 = "";
        $pregunta3 = "";
        $pregunta4 = "";
        $depResponde1 = "";
        $respuesta3 = "";
        $diasVence = "";

        $sqlExpdien = " SELECT R.RADI_NUME_RADI AS RADICADO, EX.SGD_EXP_NUMERO AS EXPEDIENTE, SEXP.SGD_SEXP_PAREXP1 AS NOMEXP
                                FROM RADICADO R LEFT JOIN SGD_EXP_EXPEDIENTE AS EX ON R.RADI_NUME_RADI = EX.RADI_NUME_RADI
    		                      LEFT JOIN SGD_SEXP_SECEXPEDIENTES AS SEXP ON SEXP.SGD_EXP_NUMERO = EX.SGD_EXP_NUMERO
                                WHERE R.RADI_NUME_RADI = $nroradicado ";
        $rsExp = $db->conn->Execute($sqlExpdien);
        if ($rsExp && ! $rsExp->EOF) {
            while (! $rsExp->EOF) {
                if (strlen($expedient) > 1)
                    $expedient .= " - ";
                $expedient .= $rsExp->fields["EXPEDIENTE"];
                if (strlen($nomExpedi) > 1)
                    $nomExpedi .= " - ";
                $nomExpedi .= trim(preg_replace('/\s+/', ' ', $rsExp->fields["NOMEXP"]));

                $rsExp->MoveNext();
            }
        }

        $valResp = false;
        $sqlAnexs = " SELECT R.RADI_NUME_RADI AS RADICADO
    						, A.RADI_NUME_SALIDA AS RESPUESTA
    						, TIP.SGD_TPR_DESCRIP AS TIPO_RESP
    						, U1.USUA_NOMB AS RESPONSABLE
    						, (	SELECT RE.RADI_PATH FROM RADICADO RE WHERE RE.RADI_NUME_RADI = A.RADI_NUME_SALIDA) AS IMAGEN_RESP
    						, A.ANEX_NOMB_ARCHIVO AS IMAGEN_ANEXO
    						, ( CASE  WHEN a.ANEX_ESTADO = 4 THEN ('CORRESPONDENCIA') ELSE NULL END) AS ENVI_CORRES
    						, $fechResp1 as FECHA_RESP
    						, dbo.diashabilestramite($radiFechradi1, $fechResp1) as DIAS_RESP
    						, ENC.RADI_PREGUNTA1 AS PREGUNTA1
    						, ENC.RADI_PREGUNTA2 AS PREGUNTA2
    						, ENC.RADI_PREGUNTA3 AS PREGUNTA3
    						, ENC.RADI_PREGUNTA4 AS PREGUNTA4
    						, DEP.DEPE_NOMB AS DEP_RESPONDE
                            , G.SGD_RENV_FECH AS ENVIO
    				FROM	RADICADO R LEFT JOIN ANEXOS A ON A.ANEX_RADI_NUME = R.RADI_NUME_RADI AND A.ANEX_SALIDA = 1 AND A.anex_radi_nume <> A.radi_nume_salida AND A.anex_borrado = 'N'
    						LEFT JOIN USUARIO U1 ON U1.USUA_LOGIN = A.ANEX_CREADOR
    						LEFT JOIN RADICADO AS R2 ON R2.RADI_NUME_RADI = R.RADI_NUME_DERI
    						LEFT JOIN RADICADO RR ON RR.RADI_NUME_RADI = A.RADI_NUME_SALIDA
    						LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = RR.TDOC_CODI
    						LEFT JOIN SGD_ENCUESTA ENC ON ENC.RADI_NUME_RADI = A.RADI_NUME_SALIDA
    						LEFT JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = RR.RADI_DEPE_RADI
                            LEFT JOIN SGD_RENV_REGENVIO G ON G.RADI_NUME_SAL = A.RADI_NUME_SALIDA
    				WHERE r.RADI_TIPORAD = 2 and R.RADI_NUME_RADI = $nroradicado and G.SGD_DIR_TIPO = 1
    				ORDER BY 2";
        // echo $sqlAnexs;
        $rsA = $db->conn->Execute($sqlAnexs);
        if ($rsA && ! $rsA->EOF) {
            while (! $rsA->EOF) {
                if ($rsA->fields["RESPUESTA"] != '') {

                    if (substr($rsA->fields["RESPUESTA"], - 1) != '3') {

                        $valResp = true;
                        
                        if (strlen($respuesta1) > 0)
                            $respuesta1 .= " - ";
                        $respuesta1 .= $rsA->fields["RESPUESTA"];
                        if (strlen($tipoResp1) > 0)
                            $tipoResp1 .= " - ";
                        $tipoResp1 .= $rsA->fields["TIPO_RESP"];
                        if (strlen($responsable1) > 0)
                            $responsable1 .= " - ";
                        $responsable1 .= $rsA->fields["RESPONSABLE"];
                        if (strlen($fechaResp1) > 0)
                            $fechaResp1 .= " - ";
                        $fechaResp1 .= $rsA->fields["FECHA_RESP"];
                        if (strlen($imageResp1) > 0)
                            $imageResp1 .= " - ";
                        $imageResp1 .= $rsA->fields["IMAGEN_RESP"];
                        if (strlen($imageAnex1) > 0)
                            $imageAnex1 .= " - ";
                        $imageAnex1 .= $rsA->fields["IMAGEN_ANEXO"];
                        if (strlen($enviCorres) > 0)
                            $enviCorres .= " - ";
                        $enviCorres .= $rsA->fields["ENVI_CORRES"];
                        if (strlen($diasResp1) > 0)
                            $diasResp1 .= " - ";
                        if (strlen($rsA->fields["DIAS_RESP"]) > 0) {
                            // if ($rsA->fields["DIAS_RESP"] < 0) {
                            // $diasResp1 .= 0;
                            // } else {
                            $diasResp1 .= $rsA->fields["DIAS_RESP"];
                            // }
                        } // ($rsA->fields["DIAS_RESP"] == 0 ? "" : $rsA->fields["DIAS_RESP"]);
                        if (strlen($pregunta1) > 0)
                            $pregunta1 .= " - ";
                        $pregunta1 .= $rsA->fields["PREGUNTA1"];
                        if (strlen($pregunta2) > 0)
                            $pregunta2 .= " - ";
                        $pregunta2 .= $rsA->fields["PREGUNTA2"];
                        if (strlen($pregunta3) > 0)
                            $pregunta3 .= " - ";
                        $pregunta3 .= $rsA->fields["PREGUNTA3"];
                        if (strlen($pregunta4) > 0)
                            $pregunta4 .= " - ";
                        $pregunta4 .= $rsA->fields["PREGUNTA4"];
                        if (strlen($depResponde1) > 0)
                            $depResponde1 .= " - ";
                        $depResponde1 .= $rsA->fields["DEP_RESPONDE"];
                        if (strlen($diasVence) > 0) {
                            $diasVence .= " - ";
                        }
                        if (strlen($rsA->fields["ENVIO"]) > 4) {
                            $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '" . $result->fields["FECHA_RAD"] . "'), CONVERT (date, '" . $rsA->fields["ENVIO"] . "') )";
                            $diasTram = $db->conn->getone($sqlDiasV);
                            $diasVence .= $diasTram;
                        }

                        /*
                         * if (strlen($padre) > 1)
                         * $padre .= " - ";
                         * if (strlen($resPadre) > 1) {
                         * $resPadre .= " - ";
                         * }
                         * if (substr($rsA->fields["PADRE"], -1) == "1") {
                         * $padre = $rsA->fields["PADRE"];
                         * $resPadre = $rsA->fields["RESP_PADRE"];
                         * }
                         */
                    }
                }

                $rsA->MoveNext();
            }
        }
        
        if ($valResp) {
            $valResp = false;
            $result->MoveNext();
            continue;
        }

        $radiFechradi2 = $result->fields["FECHA_RAD"] . " 00:00:00";
        $anexoasociado = "";
        $radirespuesta = "";
        $panexoasociado = "";
        $pradirespuesta = "";
        $sqlRespuestas = "SELECT	R.RADI_NUME_RADI,
                            	R.RADI_TIPO_DERI as TIPOANEXASOCIADO, R.RADI_NUME_DERI as RADIANEXASOCIADO, C.RADI_PATH as PATHANEXASOCIADO,
                                R.RADI_RESPUESTA as RADIRADIRESPUESTA, D.RADI_PATH as PATHRADIRESPUESTA
                            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = R.TDOC_CODI
                                LEFT JOIN RADICADO C ON C.RADI_NUME_RADI=R.RADI_NUME_DERI
                                LEFT JOIN RADICADO D ON D.RADI_NUME_RADI=R.RADI_RESPUESTA
                            WHERE R.RADI_NUME_RADI = $nroradicado ";
        $rsResp = $db->conn->Execute($sqlRespuestas);
        if ($rsResp && ! $rsResp->EOF) {
            
            if (($rsResp->fields["RADIANEXASOCIADO"] != '' || $rsResp->fields["RADIANEXASOCIADO"] != '0') && ($rsResp->fields["RADIRADIRESPUESTA"] != '' || $rsResp->fields["RADIRADIRESPUESTA"] != '0')) {
                $valResp = false;
                $result->MoveNext();
                continue;
            }
            
            if ($rsResp->fields["RADIANEXASOCIADO"] != '') {
                if ((substr($rsResp->fields["RADIANEXASOCIADO"], - 1) == '1') && (substr($rsResp->fields["RADIANEXASOCIADO"], - 1) != '3') && (substr($rsResp->fields["RADIANEXASOCIADO"], - 1) != '2')) {
                    $anexoasociado = $rsResp->fields["RADIANEXASOCIADO"];
                    $panexoasociado = $rsResp->fields["PATHANEXASOCIADO"];
                    if (strlen($imageResp1) > 1)
                        $imageResp1 .= " - ";
                    $imageResp1 .= $panexoasociado;

                    $sqlAsoc = "SELECT R.RADI_NUME_RADI AS RADICADO, TIP.SGD_TPR_DESCRIP AS TIPO_RESP
                                        , U.USUA_NOMB AS RESPONSABLE, ( $fechResp2 ) as FECHA_RESP
                                        , dbo.diashabilestramite(('$radiFechradi2'), ($fechResp2)) as DIAS_RESP
                                        , DEP.DEPE_NOMB AS DEP_RESPONDE
                                        , G.SGD_RENV_FECH AS ENVIO
                                FROM RADICADO R INNER JOIN USUARIO U ON U.USUA_CODI = R.RADI_USUA_RADI
                                LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = R.TDOC_CODI
                                INNER JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = R.RADI_DEPE_RADI AND DEP.DEPE_CODI = U.DEPE_CODI
                                LEFT JOIN SGD_RENV_REGENVIO G ON G.RADI_NUME_SAL = R.RADI_NUME_RADI
                                WHERE r.RADI_TIPORAD = 1 and R.RADI_NUME_RADI = $anexoasociado ";
                    $rsAsoc = $db->conn->Execute($sqlAsoc);
                    if ($rsAsoc && ! $rsAsoc->EOF) {
                        if (strlen($fechaResp1) > 0)
                            $fechaResp1 .= " - ";
                        $fechaResp1 .= $rsAsoc->fields["FECHA_RESP"];

                        if (strlen($diasResp1) > 0) {
                            $diasResp1 .= " - ";
                        }
                        if (strlen($rsAsoc->fields["DIAS_RESP"]) > 0) {
                            // if ($rsAsoc->fields["DIAS_RESP"] < 0) {
                            // $diasResp1 .= 0;
                            // } else {
                            $diasResp1 .= $rsAsoc->fields["DIAS_RESP"];
                            // }
                        }

                        if (strlen($depResponde1) > 0)
                            $depResponde1 .= " - ";
                        $depResponde1 .= $rsAsoc->fields["DEP_RESPONDE"];

                        if (strlen($tipoResp1) > 0)
                            $tipoResp1 .= " - ";
                        $tipoResp1 .= $rsAsoc->fields["TIPO_RESP"];

                        if (strlen($responsable1) > 0)
                            $responsable1 .= " - ";
                        $responsable1 .= $rsAsoc->fields["RESPONSABLE"];

                        if (strlen($diasVence) > 0) {
                            $diasVence .= " - ";
                        }
                        if (strlen($rsAsoc->fields["ENVIO"]) > 4) {
                            $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '" . $result->fields["FECHA_RAD"] . "'), CONVERT (date, '" . $rsAsoc->fields["ENVIO"] . "') )";
                            $diasTram = $db->conn->getone($sqlDiasV);
                            $diasVence .= $diasTram;
                        }
                    }
                } 
            }

            if ($rsResp->fields["RADIRADIRESPUESTA"] != '') {
                $var = substr($rsResp->fields["RADIRADIRESPUESTA"], - 1);
                if ($var == '1' && (substr($rsResp->fields["RADIRADIRESPUESTA"], - 1) != '3')) {
                    $radirespuesta = $rsResp->fields["RADIRADIRESPUESTA"];
                    $pradirespuesta = $rsResp->fields["PATHRADIRESPUESTA"];
                    if (strlen($imageResp1) > 1)
                        $imageResp1 .= " - ";
                    $imageResp1 .= $pradirespuesta;

                    $sqlResp = "SELECT R.RADI_NUME_RADI AS RADICADO, TIP.SGD_TPR_DESCRIP AS TIPO_RESP
                                        , U.USUA_NOMB AS RESPONSABLE, ( $fechResp2 ) as FECHA_RESP
                                        , dbo.diashabilestramite(('$radiFechradi2'), ($fechResp2)) as DIAS_RESP
                                        , DEP.DEPE_NOMB AS DEP_RESPONDE
                                        , G.SGD_RENV_FECH AS ENVIO
                                    FROM RADICADO R INNER JOIN USUARIO U ON U.USUA_CODI = R.RADI_USUA_RADI
                                    LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = R.TDOC_CODI
                                    INNER JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = R.RADI_DEPE_RADI AND DEP.DEPE_CODI = U.DEPE_CODI
                                    LEFT JOIN SGD_RENV_REGENVIO G ON G.RADI_NUME_SAL = R.RADI_NUME_RADI
                                    WHERE G.SGD_DIR_TIPO = 1 AND R.RADI_TIPORAD = 1 and R.RADI_NUME_RADI = $radirespuesta ";
                    $rsResp = $db->conn->Execute($sqlResp);
                    if ($rsResp && ! $rsResp->EOF) {
                        if (strlen($fechaResp1) > 0)
                            $fechaResp1 .= " - ";
                        $fechaResp1 .= $rsResp->fields["FECHA_RESP"];

                        if (strlen($diasResp1) > 0) {
                            $diasResp1 .= " - ";
                        }
                        if (strlen($rsResp->fields["DIAS_RESP"]) > 0) {
                            // if ($rsResp->fields["DIAS_RESP"] < 0) {
                            // $diasResp1 .= 0;
                            // } else {
                            $diasResp1 .= $rsResp->fields["DIAS_RESP"];
                            // }
                        }

                        if (strlen($depResponde1) > 0)
                            $depResponde1 .= " - ";
                        $depResponde1 .= $rsResp->fields["DEP_RESPONDE"];

                        if (strlen($tipoResp1) > 0)
                            $tipoResp1 .= " - ";
                        $tipoResp1 .= $rsResp->fields["TIPO_RESP"];

                        if (strlen($responsable1) > 0)
                            $responsable1 .= " - ";
                        $responsable1 .= $rsResp->fields["RESPONSABLE"];

                        if (strlen($diasVence) > 0) {
                            $diasVence .= " - ";
                        }
                        if (strlen($rsResp->fields["ENVIO"]) > 4) {
                            $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '" . $result->fields["FECHA_RAD"] . "'), CONVERT (date, '" . $rsResp->fields["ENVIO"] . "') )";
                            $diasTram = $db->conn->getone($sqlDiasV);
                            $diasVence .= $diasTram;
                        }
                    }
                } 
            }
        }

        $respuesta3 = $radirespuesta;
        $fechaRadicado = $result->fields["FECHA_RAD"]; // *** Fecha radicado
        $tiporad = trim(str_replace('|', '', $result->fields["TIPO_DOC"])); // *** Tipo radicado
                                                                            // $asunto = mb_convert_encoding($result->fields["RA_ASUN"], "UTF-8", "UTF-8,ISO-8859-1,ASCII");
        $asunto = preg_replace('/\s+/', ' ', $result->fields["RA_ASUN"]);
        $asunto = str_replace('|', '', $asunto);
        $medRecepcio = $result->fields["M_RECEP"]; // *** Medio de recepcion
        $medRespuesol = $result->fields["M_RESPUESTA_SOL"]; // *** Medio de respuesta solicitado
        $pqrVerbal = $result->fields["PQR_VERBAL"]; // *** Pqr Verbal
        $depeactu = $result->fields["DEPE_ACTU"]; // *** Dependencia actual
        $usuaactu = $result->fields["USUA_ACTUAL"]; // *** Usuario actual
        $depeante = $resulDepe->fields["DEPE_ANTE"]; // *** Dependencia anterior
                                                     // $padre = $result->fields["PADRE"]; // *** Radicado padre - posible respuesta2
                                                     // $resPadre = $result->fields["RESP_PADRE"]; // *** Usuario que responde2
        $termino = $result->fields["TERMINO"];
        $hist1 = trim(preg_replace('/\s+/', ' ', $result->fields["HIST1"])); // *** Historico1
        $hist2 = trim(preg_replace('/\s+/', ' ', $result->fields["HIST2"])); // *** Historico2
        if (strlen($result->fields["FECH_VENCE"]) > 4) {
            $fechVence = $result->fields["FECH_VENCE"]; // *** Fecha creada desde alarmas
        } else {
            if ($termino > 0) {
                $fecha1 = $result->fields['FECHA1'];
                $termino1 = $termino - 1;
                $sqlFec = "SELECT dbo.sumadiasfecha($termino1, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                $fecVence = $db->conn->getone($sqlFec);
                $fecVence = substr($fecVence, 0, 10);
            }
        }

        if (strlen($diasVence) == 0) {
            $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '" . $result->fields["FECHA_RAD"] . "'), CONVERT (date, $fecVence) )";
            $diasTram = $db->conn->getone($sqlDiasV);
            $diasVence .= $diasTram;
        }

        // $diasVence = $result->fields["RADI_DIAS_VENCE"]; // *** Dias pendiente para dar respuesta
        $Respuesta1 = trim(preg_replace('/\s+/', ' ', $respuesta1)); // *** Respuesta
        $TipoResp1 = $tipoResp1; // *** Tipo Documental de la Respuesta
        $responsable1 = $responsable1; // *** Usuario que Responde
        $fechaResp1 = $fechaResp1; // *** Fecha de respuesta
        $imaResp1 = $imageResp1; // *** Img respuesta
        $envioCorr = $enviCorres; // *** Correspondencia
        $expedient = $expedient; // *** Expediente
        $nomExp = $nomExpedi; // *** Nombre de Expediente
        $esAnonimo = $result->fields["ANONIMO"] == 0 ? "No" : "Si";
        $pregunta1 = trim(preg_replace('/\s+/', ' ', $pregunta1));
        $pregunta2 = trim(preg_replace('/\s+/', ' ', $pregunta2));
        $pregunta3 = trim(preg_replace('/\s+/', ' ', $pregunta3));
        $pregunta4 = trim(preg_replace('/\s+/', ' ', $pregunta4));
        $departamento = $result->fields["DEPARTAMENTO"];
        $municipio = $result->fields["MUNICIPIO"];
        $infPoblacional = $result->fields["INF_POBLACIONAL"];
        $depResponde1 = $depResponde1;
        $remitente = htmlspecialchars($result->fields["REMITENTE"]);
        $docremite = $result->fields["DOCUMENTO"];
        $rem_oem = $result->fields["SGD_OEM_CODIGO"];
        $rem_ciu = $result->fields["SGD_CIU_CODIGO"];
        $rem_esp = $result->fields["SGD_ESP_CODI"];
        $rem_fun = $result->fields["SGD_DOC_FUN"];
        $rem_final = ($rem_oem) ? "Empresa" : ($rem_ciu ? "Ciudadano" : ($rem_esp ? "Entidad" : "Funcionario"));
        $esAnonimo = $result->fields["ANONIMO"] == 0 ? "No" : "Si";
        $myData[] = array(
            $nroradicado,
            $fechaRadicado,
            $tiporad,
            $medRecepcio,
            $depeactu,
            $depeante,
            $anexoasociado,
            $hist1,
            $hist2,
            $Respuesta1,
            $fechaResp1,
            $imaResp1,
            $respuesta3,
            $envioCorr,
            $expedient,
            $temaRad,
            $diasResp1,
            $fechVence,
            $asunto,
            $diasVence,
            $remitente,
            $docremite,
            $responsable1,
            "",
            $usuaactu,
            $TipoResp1,
            $nomExp,
            $pregunta1,
            $pregunta2,
            $pregunta3,
            $pregunta4,
            $departamento,
            $municipio,
            $infPoblacional,
            $depResponde1,
            $pqrVerbal,
            $medRespuesol,
            $rem_final,
            $esAnonimo,
            $producto
        );
        unset($diasdeResp);
        $nroradicadoAnt = $nroradicado;
        $result->MoveNext();
    }
    return $myData;
}

$db->conn->Execute("SET DATEFIRST 1");
// $result =$db->conn->Execute($isql);
//echo $query;
$result = $db->conn->Execute($query);
$myData = array();
$myData = carga_radicados($mydata, $result, $db);

if ($generarOrfeo) {
    $contenido = "";
    $contenido1 = "";
    $csv_end = "
    ";
    if ($myData != null) {

        $contenido = "Genero: " . $_SESSION["login"] . $csv_end;

        if (isset($_POST['campos'])) {
            if (is_array($_POST['campos'])) {
                $num_campos = count($_POST['campos']);
                $current = 0;
                $contenido1 .= "Nro_Radicado|";
                foreach ($_POST['campos'] as $key => $value) {
                    if ($current != $num_campos - 1)
                        $contenido1 .= $value . '|';
                    else
                        $contenido1 .= $value . $csv_end;
                    $current ++;
                }

                $contenido .= $contenido1;
                foreach ($myData as $item) {

                    $contenido .= trim($item[0]) . "|";

                    if (strpos($contenido1, "Es_Anonimo")) {
                        $contenido .= trim($item[38]) . "|";
                    }
                    if (strpos($contenido1, "Tipo_Tercero")) {
                        $contenido .= trim($item[37]) . "|";
                    }
                    if (strpos($contenido1, "Tipo_de_Documento")) {
                        $contenido .= trim($item[2]) . "|";
                    }
                    if (strpos($contenido1, "Inf_Poblacional")) {
                        $contenido .= trim($item[33]) . "|";
                    }
                    if (strpos($contenido1, "Tema")) {
                        $contenido .= trim($item[15]) . "|";
                    }
                    if (strpos($contenido1, "Fecha_Radicado")) {
                        $contenido .= trim($item[1]) . "|";
                    }

                    // $i=0;
                    // if($item[15]){
                    // foreach($item[15] as $itemTema){
                    // $i++;
                    // $contenido .= trim($itemTema) . ",";
                    // }
                    // }
                    if (strpos($contenido1, "Fecha_Vence")) {
                        $contenido .= trim($item[17]) . "|";
                    }
                    if (strpos($contenido1, "Fecha_Respuesta")) {
                        $contenido .= trim($item[10]) . "|";
                    }
                    if (strpos($contenido1, "Dias_Vencimiento")) {
                        $contenido .= trim($item[19]) . "|";
                    }
                    if (strpos($contenido1, "Dias_respuesta")) {
                        $contenido .= trim($item[16]) . "|";
                    }
                    if (strpos($contenido1, "Respuesta_1")) {
                        $contenido .= htmlspecialchars(trim($item[9])) . "|";
                    }
                    if (strpos($contenido1, "Dep_Respuesta")) {
                        $contenido .= trim($item[34]) . "|";
                    }
                    if (strpos($contenido1, "Tipo_Respuesta")) {
                        $contenido .= trim($item[25]) . "|";
                    }
                    if (strpos($contenido1, "Responsable_1")) {
                        $contenido .= trim($item[22]) . "|";
                    }
                    if (strpos($contenido1, "Respuesta_2")) {
                        $contenido .= trim($item[6]) . "|";
                    }
                    // if (strpos($contenido1, "Responsable_2")) {
                    // $contenido .= trim($item[23]) . "|";
                    // }
                    if (strpos($contenido1, "Respuesta_3")) {
                        $contenido .= trim($item[12]) . "|";
                    }
                    if (strpos($contenido1, "Imagen_Respuesta")) {
                        $contenido .= trim($item[11]) . "|";
                    }
                    if (strpos($contenido1, "Asunto")) {
                        $contenido .= htmlspecialchars(trim($item[18])) . "|";
                    }
                    if (strpos($contenido1, "Medio_de_Recepcion")) {
                        $contenido .= trim($item[3]) . "|";
                    }
                    if (strpos($contenido1, "Medio_Respuesta_solicitado")) {
                        $contenido .= trim($item[36]) . "|";
                    }
                    if (strpos($contenido1, "Pqr_Verbal")) {
                        $contenido .= trim($item[35]) . "|";
                    }
                    if (strpos($contenido1, "Departamento")) {
                        $contenido .= trim($item[31]) . "|";
                    }
                    if (strpos($contenido1, "Municipio")) {
                        $contenido .= trim($item[32]) . "|";
                    }
                    if (strpos($contenido1, "Dependencia_Actual")) {
                        $contenido .= trim($item[4]) . "|";
                    }
                    if (strpos($contenido1, "Usuario_Actual")) {
                        $contenido .= trim($item[24]) . "|";
                    }
                    if (strpos($contenido1, "Dependencia_anterior")) {
                        $contenido .= trim($item[5]) . "|";
                    }
                    if (strpos($contenido1, "Historico_1")) {
                        $contenido .= htmlspecialchars(trim($item[7])) . "|";
                    }
                    if (strpos($contenido1, "Historico_2")) {
                        $contenido .= htmlspecialchars(trim($item[8])) . "|";
                    }
                    if (strpos($contenido1, "Expediente")) {
                        $contenido .= trim($item[14]) . "|";
                    }
                    if (strpos($contenido1, "Nombre_Expediente")) {
                        $contenido .= htmlspecialchars(trim($item[26])) . "|";
                    }
                    if (strpos($contenido1, "Remitente")) {
                        $contenido .= htmlspecialchars(trim($item[20])) . "|";
                    }
                    if (strpos($contenido1, "Documento")) {
                        $contenido .= trim($item[21]) . "|";
                    }
                    if (strpos($contenido1, "Pregunta_1")) {
                        $contenido .= trim($item[27]) . "|";
                    }
                    if (strpos($contenido1, "Pregunta_2")) {
                        $contenido .= trim($item[28]) . "|";
                    }
                    if (strpos($contenido1, "Pregunta_3")) {
                        $contenido .= trim($item[29]) . "|";
                    }
                    if (strpos($contenido1, "Pregunta_4")) {
                        $contenido .= trim($item[30]) . "|";
                    }
                    /*
                     * if (strpos($contenido1, "Producto")) {
                     * $contenido .= trim($item[39]);
                     * }
                     */

                    $contenido .= $csv_end;
                }
            }
        }
    }
    // unset($item);

    $hora = date("H") . date("i") . date("s");
    // var que almacena el dia de la fecha
    $ddate = date('d');
    // var que almacena el mes de la fecha
    $mdate = date('m');
    // var que almacena el aÃ±o de la fecha
    $adate = date('Y');
    // var que almacena la fecha formateada
    $fecha = $adate . $mdate . $ddate;
    // guarda el path del archivo generado
    $archivo1 = "../bodega/tmp/Nomb" . "_$fecha" . "$hora" . ".csv";
    $fp = fopen($archivo1, "wb");
    fputs($fp, $contenido);
    fclose($fp);

    try {
        $archivo = "../bodega/tmp/Nomb" . "_$fecha" . "$hora" . ".xlsx";
        include '../lib/PHPExcel/IOFactory.php';
        $objReader = PHPExcel_IOFactory::createReader('CSV');
        // If the files uses a delimiter other than a comma (e.g. a tab), then tell the reader
        $objReader->setDelimiter("|");
        // If the files uses an encoding other than UTF-8 or ASCII, then tell the reader
        $objReader->setInputEncoding('UTF-8');
        $objPHPExcel = $objReader->load($archivo1);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($archivo);

        unlink($archivo1);
    } catch (Exception $e) {
        echo 'Excepción capturada: ', $e->getMessage(), "\n";
        exit();
    }

    /*
     * require '../../vendor/autoload.php';
     *
     * include 'PhpSpreadsheet\src\PhpSpreadsheet\Spreadsheet';
     * include 'PhpSpreadsheet\src\PhpSpreadsheet\Writer\Xlsx';
     *
     * $spreadsheet = new Spreadsheet();
     * $reader = new PhpSpreadsheet\src\PhpSpreadsheet\Reader\Csv();
     *
     * $reader->setDelimiter('|');
     * $reader->setEnclosure('"');
     * $reader->setSheetIndex(0);
     *
     * $spreadsheet = $reader->load('$archivo1');
     * $writer = new Xlsx($spreadsheet);
     * $writer->save($archivo);
     *
     * $spreadsheet->disconnectWorksheets();
     * unset($spreadsheet);
     */
}
?>