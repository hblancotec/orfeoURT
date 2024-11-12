<?php
ini_set('set_time_limit', 0);
ini_set('display_errors', 1);
echo "iniciamos .. " . date('Ymd H:i:s') . "<br/>";

if (isset($argc)) {
    for ($i = 0; $i < $argc; $i ++) {
        if ($i == 1) {
            $fecha_ini = $argv[$i];
        }
        if ($i == 2) {
            $fecha_fin = $argv[$i];
        }
        if ($i == 3) {
            $dependencia_busq = $argv[$i];
        }
        if ($i == 4) {
            $SelPqr = $argv[$i];
        }
        if ($i == 5) {
            $temaSelect = $argv[$i];
        }
        if ($i == 6) {
            $usua_email = $argv[$i];
        }
        if ($i == 7) {
            $campos = $argv[$i];
        }
        echo "Argument #" . $i . " - " . $argv[$i] . "\n";
    }
} else {
    echo "argc and argv disabled\n";
}

include_once dirname(__FILE__) . "\\config.php";
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
// ############################################################################
$conn = NewADOConnection($dsn);
$conn->SetFetchMode(ADODB_FETCH_ASSOC);

$generarOrfeo = true;

/*function carga_radicados(&$mydata, &$result, $conn)
{
    
    return $myData;
}*/

$fechafiltro = " and r.RADI_FECH_RADI BETWEEN " . $conn->DBTimeStamp($fecha_ini . "T00:00:00") . " and " . $conn->DBTimeStamp($fecha_fin . "T23:59:59");
if ($dependencia_busq == 99999) {
    $dependencia_busq2 = "";
    $dep_historico = ", '' AS DEP_HISTORICO";
} else {

    $dep_historico = ", (SELECT TOP 1 H.DEPE_CODI_DEST
                                FROM RADICADO R1 INNER JOIN HIST_EVENTOS H ON R1.RADI_NUME_RADI = H.RADI_NUME_RADI
                                WHERE (H.SGD_TTR_CODIGO = 2 OR H.SGD_TTR_CODIGO = 9) 
                                    AND R1.RADI_NUME_RADI = R.RADI_NUME_RADI AND H.DEPE_CODI_DEST = $dependencia_busq) AS DEP_HISTORICO";

    $dependencia_busq2 = " and $dependencia_busq in (SELECT H.DEPE_CODI_DEST
                FROM RADICADO R1 INNER JOIN HIST_EVENTOS H ON R1.RADI_NUME_RADI = H.RADI_NUME_RADI
                WHERE (H.SGD_TTR_CODIGO = 2 OR H.SGD_TTR_CODIGO = 9) AND R1.RADI_NUME_RADI = R.RADI_NUME_RADI )";
    // $dependencia_busq2 = " and r.RADI_DEPE_ACTU IN ($dependencia_busq)";
}

if ($SelPqr == 1) {
    $wherePqr = "AND s.SGD_TPR_REPORT1 = 1 ";
} else {
    $wherePqr = " ";
}

$resulta = (empty($med_rec2)) ? "" : implode(',', $med_rec2);
$medio_busq = (empty($resulta)) ? "" : " and r.MREC_CODI IN ($resulta)";
$temasFrom = "LEFT OUTER JOIN dbo.SGD_CAUX_CAUSALES caux
    						ON (r.RADI_NUME_RADI=caux.RADI_NUME_RADI)";
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

$radiFechradi1 = $conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI');
$radiFechradi2 = $conn->Concat($conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI'), "' 00:00:00'");
// echo "temas... ".$temasWhere;
$histFech = $conn->SQLDate('Y-m-d', 'h.HIST_FECH') . $conn->concat_operator . "' 00:00:00'";
$fechResp1 = "(	SELECT	TOP 1 " . $conn->SQLDate('Y-m-d', 'h.HIST_FECH') . " AS FECHA_RESP
						FROM	RADICADO RA WITH (NOLOCK), ANEXOS A WITH (NOLOCK), HIST_EVENTOS H WITH (NOLOCK)
						WHERE	A.ANEX_RADI_NUME = RA.RADI_NUME_RADI
								AND (convert(varchar(15), A.RADI_NUME_SALIDA) LIKE '%[136]')
								AND A.RADI_NUME_SALIDA = H.RADI_NUME_RADI
								AND H.SGD_TTR_CODIGO IN (42,22,23)
								AND RA.RADI_NUME_RADI = R.RADI_NUME_RADI
						ORDER BY FECHA_RESP)";

$fechResp2 = "	SELECT	TOP 1 " . $conn->SQLDate('Y-m-d', 'h.HIST_FECH') . "+' 00:00:00 ' AS FECHA_RESP
						FROM	RADICADO RA WITH (NOLOCK), ANEXOS A WITH (NOLOCK), HIST_EVENTOS H WITH (NOLOCK)
						WHERE	A.ANEX_RADI_NUME = RA.RADI_NUME_RADI
								AND (CONVERT(varchar(15), A.RADI_NUME_SALIDA) LIKE '%[136]')
								AND A.RADI_NUME_SALIDA = H.RADI_NUME_RADI
								AND H.SGD_TTR_CODIGO IN (42,22)
								AND RA.RADI_NUME_RADI = R.RADI_NUME_RADI
						ORDER BY FECHA_RESP";

/*$query = " SELECT DISTINCT R.RADI_NUME_RADI AS RADICADO, " . $radiFechradi1 . " AS FECHA_RAD, FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1,
                        CASE R.RADI_FECHA_VENCE WHEN '' THEN ''
    		              WHEN NULL THEN ''
    		              ELSE " . $conn->SQLDate('Y-m-d', 'R.RADI_FECHA_VENCE') . " END AS FECH_VENCE
    						, R.RADI_DIAS_VENCE
                            , R.RADI_ANONIMO AS ANONIMO
    						, S.SGD_TPR_DESCRIP AS TIPO_DOC
                            , S.SGD_TPR_TERMINO AS TERMINO
    						, M.MREC_DESC AS M_RECEP
                            , E.DEPE_NOMB AS DEPE_ACTU
    						, UA.USUA_NOMB AS USUA_ACTUAL
                            , R.RA_ASUN
                            , R.RADI_CUENTAI
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
                            $dep_historico
    						, D.SGD_DIR_NOMREMDES as REMITENTE
                            , D.SGD_DIR_DOC as DOCUMENTO
                            , D.SGD_DIR_TELEFONO as TELEFONO
                            , D.SGD_DIR_MAIL as EMAIL
    						, DP.DPTO_NOMB AS DEPARTAMENTO
    						, MP.MUNI_NOMB AS MUNICIPIO
    						, IP.SGD_INFPOB_DESC AS INF_POBLACIONAL
    						, (CASE WHEN ME.PQRVERBAL = 1 THEN 'SI' ELSE 'NO' END) AS PQR_VERBAL
                            , D.SGD_OEM_CODIGO, D.SGD_CIU_CODIGO, D.SGD_ESP_CODI, D.SGD_DOC_FUN
                            , S.SGD_TPR_TERMINO
    				FROM	RADICADO R WITH (NOLOCK) LEFT JOIN USUARIO U WITH (NOLOCK) ON R.RADI_USU_ANTE = U.USUA_LOGIN
    						LEFT JOIN SGD_DIR_DRECCIONES D WITH (NOLOCK) ON R.radi_nume_radi = D.radi_nume_radi AND D.SGD_DIR_TIPO = 1
    						LEFT JOIN RADICADO AS R2 WITH (NOLOCK) ON R2.RADI_NUME_RADI = R.RADI_NUME_DERI
    						LEFT JOIN USUARIO U2 WITH (NOLOCK) ON U2.DEPE_CODI = R2.RADI_DEPE_RADI AND U2.USUA_CODI = R2.RADI_USUA_RADI
    						LEFT JOIN USUARIO UA WITH (NOLOCK) ON UA.DEPE_CODI = R.RADI_DEPE_ACTU AND UA.USUA_CODI = R.RADI_USUA_ACTU
    						LEFT JOIN DEPARTAMENTO DP ON DP.ID_PAIS = 170 AND DP.DPTO_CODI = D.DPTO_CODI
    						LEFT JOIN MUNICIPIO MP ON MP.MUNI_CODI = D.MUNI_CODI AND MP.DPTO_CODI = D.DPTO_CODI AND MP.ID_PAIS = 170
    						LEFT JOIN SGD_PQR_METADATA AS ME ON ME.RADI_NUME_RADI = R.RADI_NUME_RADI
    						LEFT JOIN SGD_INF_INFPOB AS IP ON IP.ID_INFPOB = ME.ID_INFPOB
    						INNER JOIN SGD_TPR_TPDCUMENTO AS S ON R.TDOC_CODI = S.SGD_TPR_CODIGO
    						INNER JOIN MEDIO_RECEPCION AS M ON R.MREC_CODI = M.MREC_CODI
    						INNER JOIN dbo.DEPENDENCIA AS E ON R.RADI_DEPE_ACTU = E.DEPE_CODI
				WHERE	r.RADI_TIPORAD = 2 and E.DEPE_CODI <> 9000  
						$temasWhere
    					$wherePqr
    					$medio_busq
    					$fechafiltro
    					$dependencia_busq2
				ORDER BY 2 ";*/

//echo $query;

$st = " DECLARE @return_value int
            
        EXEC @return_value = [dbo].[ReporteInternetConsultar]
                            @DEPENDENCIA = $dependencia_busq,
                    		@PQR = $SelPqr,
                    		@MEDIO_RECEP = 0,
                    		@FECHA_INI = N'$fecha_ini',
                    		@FECHA_FIN = N'$fecha_fin'";
    					
echo "$st";
$result = $conn->Execute($st);
  
//$conn->Execute("SET DATEFIRST 1");
//$result = $conn->Execute($query);
$myData = array();
//$myData = carga_radicados($mydata, $result, $conn);

$contenido = "";
$contenido1 = "";
$csv_end = "
    ";

$fecha = date('YmdHis');
$archivo1 = dirname(__FILE__) . "\\bodega\\tmp\\Nomb" . "_$fecha" . ".csv";
$fp = fopen($archivo1, "wb");

$contenido1 .= "Nro_Radicado|";
if (isset($campos)) {
    $campos = explode("-", $campos);
    if (is_array($campos)) {
        $current = 0;
        foreach ($campos as $key => $value) {
            if ($current != $num_campos - 1) {
                $contenido1 .= $value . '|';
            } else {
                $contenido1 .= $value . $csv_end;
                $current ++;
            }
        }
    }
}
$contenido .= $contenido1 . $csv_end;
fputs($fp, $contenido);

while ($result && ! $result->EOF) {
    $nroradicado = $result->fields["RADICADO"]; 
    
    // Mostrar dependencia anterior
    $isqlq = "SELECT D.DEPE_NOMB AS DEPE_ANTE
    		    FROM RADICADO R WITH (NOLOCK), DEPENDENCIA D WITH (NOLOCK), USUARIO U WITH (NOLOCK)
    			WHERE D.DEPE_CODI = U.DEPE_CODI AND
    				R.RADI_USU_ANTE  = U.USUA_LOGIN AND
    				R.RADI_NUME_RADI = $nroradicado";
    
    $resulDepe = $conn->Execute($isqlq);
    
    $temaRad = "";
    $producto = "";
    $sqlSelect = "SELECT CAUX.SGD_CAUX_CODIGO, CAU.SGD_CAU_DESCRIP as SECTOR,
    					DCAU.SGD_DCAU_DESCRIP as CAUSAL, DCAU.SGD_DCAU_PRODUCTO as PRODUCTO,
    					CAUX.SGD_CAUX_CODIGO, CAUX.RADI_NUME_RADI COUNT_RADI,
    					CAUX.SGD_CAUX_FECHA
    				FROM SGD_CAUX_CAUSALES CAUX WITH (NOLOCK), SGD_CAU_CAUSAL CAU WITH (NOLOCK), SGD_DCAU_CAUSAL DCAU WITH (NOLOCK)
    				WHERE RADI_NUME_RADI = $nroradicado
    					AND CAUX.SGD_DCAU_CODIGO=dcau.sgd_dcau_codigo
    					AND DCAU.SGD_CAU_CODIGO=cau.SGD_CAU_CODIGO";
    
    $rst = $conn->Execute($sqlSelect);
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
    
    $radiFechradi1 = $conn->Concat($conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI'), "' 00:00:00'");
    $fechResp1 = "(	SELECT TOP 1 FORMAT(SGD_RENV_FECH, 'yyyy-MM-dd', 'co-CO' ) FROM SGD_RENV_REGENVIO WITH (NOLOCK)
    							WHERE RADI_NUME_SAL = A.RADI_NUME_SALIDA AND SGD_DIR_TIPO = 1
                                ORDER BY SGD_RENV_FECH ) ";
    
    $fechResp2 = "(	SELECT TOP 1 FORMAT(SGD_RENV_FECH, 'yyyy-MM-dd', 'co-CO' ) FROM SGD_RENV_REGENVIO WITH (NOLOCK)
    							WHERE RADI_NUME_SAL = R.RADI_NUME_RADI AND SGD_DIR_TIPO = 1
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
    $diasTermino = "";
    $telefono = "";
    $email = "";
    $radcuentai = "";
    $depResponde1 = "";
    $respuesta3 = "";
    $diasVence = "";
    
    $sqlExpdien = " SELECT R.RADI_NUME_RADI AS RADICADO, EX.SGD_EXP_NUMERO AS EXPEDIENTE, SEXP.SGD_SEXP_PAREXP1 AS NOMEXP
                                FROM RADICADO R WITH (NOLOCK) LEFT JOIN SGD_EXP_EXPEDIENTE AS EX WITH (NOLOCK) ON R.RADI_NUME_RADI = EX.RADI_NUME_RADI
    		                      LEFT JOIN SGD_SEXP_SECEXPEDIENTES AS SEXP WITH (NOLOCK) ON SEXP.SGD_EXP_NUMERO = EX.SGD_EXP_NUMERO
                                WHERE R.RADI_NUME_RADI = $nroradicado ";
    $rsExp = $conn->Execute($sqlExpdien);
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
    
    $sqlAnexs = " SELECT R.RADI_NUME_RADI AS RADICADO
    						, A.RADI_NUME_SALIDA AS RESPUESTA
    						, TIP.SGD_TPR_DESCRIP AS TIPO_RESP
    						, U1.USUA_NOMB AS RESPONSABLE
    						, (	SELECT RE.RADI_PATH FROM RADICADO RE WHERE RE.RADI_NUME_RADI = A.RADI_NUME_SALIDA) AS IMAGEN_RESP
    						, A.ANEX_NOMB_ARCHIVO AS IMAGEN_ANEXO
    						, ( CASE  WHEN a.ANEX_ESTADO = 4 THEN ('CORRESPONDENCIA') ELSE NULL END) AS ENVI_CORRES
    						, $fechResp1 as FECHA_RESP
    						, dbo.diashabilestramite($radiFechradi1, $fechResp1) as DIAS_RESP
    						, DEP.DEPE_NOMB AS DEP_RESPONDE
                            , G.SGD_RENV_FECH AS ENVIO
    				FROM	RADICADO R WITH (NOLOCK) LEFT JOIN ANEXOS A WITH (NOLOCK) ON A.ANEX_RADI_NUME = R.RADI_NUME_RADI AND A.ANEX_SALIDA = 1 AND A.anex_radi_nume <> A.radi_nume_salida AND A.anex_borrado = 'N'
    						LEFT JOIN USUARIO U1 WITH (NOLOCK) ON U1.USUA_LOGIN = A.ANEX_CREADOR
    						LEFT JOIN RADICADO AS R2 WITH (NOLOCK) ON R2.RADI_NUME_RADI = R.RADI_NUME_DERI
    						LEFT JOIN RADICADO RR WITH (NOLOCK) ON RR.RADI_NUME_RADI = A.RADI_NUME_SALIDA
    						LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = RR.TDOC_CODI
    						LEFT JOIN SGD_ENCUESTA ENC ON ENC.RADI_NUME_RADI = A.RADI_NUME_SALIDA
    						LEFT JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = RR.RADI_DEPE_RADI
                            LEFT JOIN SGD_RENV_REGENVIO G WITH (NOLOCK) ON G.RADI_NUME_SAL = A.RADI_NUME_SALIDA
    				WHERE r.RADI_TIPORAD = 2 and R.RADI_NUME_RADI = $nroradicado and G.SGD_DIR_TIPO = 1
    				ORDER BY 2";
    // echo $sqlAnexs;
    $rsA = $conn->Execute($sqlAnexs);
    if ($rsA && ! $rsA->EOF) {
        while (! $rsA->EOF) {
            if ($rsA->fields["RESPUESTA"] != '') {
                
                if (substr($rsA->fields["RESPUESTA"], - 1) != '3') {
                    
                    if (strlen($respuesta1) > 0) {
                        $respuesta1 .= " - ";
                    }
                    $respuesta1 .= $rsA->fields["RESPUESTA"];
                    if (strlen($tipoResp1) > 0) {
                        $tipoResp1 .= " - ";
                    }
                    $tipoResp1 .= $rsA->fields["TIPO_RESP"];
                    if (strlen($responsable1) > 0) {
                        $responsable1 .= " - ";
                    }
                    $responsable1 .= $rsA->fields["RESPONSABLE"];
                    if (strlen($fechaResp1) > 0) {
                        $fechaResp1 .= " - ";
                    }
                    $fechaResp1 .= $rsA->fields["FECHA_RESP"];
                    if (strlen($imageResp1) > 0) {
                        $imageResp1 .= " - ";
                    }
                    $imageResp1 .= $rsA->fields["IMAGEN_RESP"];
                    if (strlen($imageAnex1) > 0) {
                        $imageAnex1 .= " - ";
                    }
                    $imageAnex1 .= $rsA->fields["IMAGEN_ANEXO"];
                    if (strlen($enviCorres) > 0) {
                        $enviCorres .= " - ";
                    }
                    $enviCorres .= $rsA->fields["ENVI_CORRES"];
                    if (strlen($diasResp1) > 0) {
                        $diasResp1 .= " - ";
                    }
                    if (strlen($rsA->fields["DIAS_RESP"]) > 0) {
                        $diasResp1 .= $rsA->fields["DIAS_RESP"];
                    } 
                    if (strlen($depResponde1) > 0) {
                        $depResponde1 .= " - ";
                    }
                    $depResponde1 .= $rsA->fields["DEP_RESPONDE"];
                }
            }
            
            $rsA->MoveNext();
        }
    }
    
    $radiFechradi2 = $result->fields["FECHA_RAD"] . " 00:00:00";
    $anexoasociado = "";
    $radirespuesta = "";
    $panexoasociado = "";
    $pradirespuesta = "";
    $sqlRespuestas = "SELECT R.RADI_NUME_RADI,
                            	R.RADI_TIPO_DERI as TIPOANEXASOCIADO, R.RADI_NUME_DERI as RADIANEXASOCIADO, C.RADI_PATH as PATHANEXASOCIADO,
                                R.RADI_RESPUESTA as RADIRADIRESPUESTA, D.RADI_PATH as PATHRADIRESPUESTA
                            FROM RADICADO R WITH (NOLOCK) INNER JOIN SGD_TPR_TPDCUMENTO T WITH (NOLOCK) ON T.SGD_TPR_CODIGO = R.TDOC_CODI
                                LEFT JOIN RADICADO C WITH (NOLOCK) ON C.RADI_NUME_RADI=R.RADI_NUME_DERI
                                LEFT JOIN RADICADO D WITH (NOLOCK) ON D.RADI_NUME_RADI=R.RADI_RESPUESTA
                            WHERE R.RADI_NUME_RADI = $nroradicado ";
    $rsResp = $conn->Execute($sqlRespuestas);
    if ($rsResp && ! $rsResp->EOF) {
        if ($rsResp->fields["RADIANEXASOCIADO"] != '') {
            if ((substr($rsResp->fields["RADIANEXASOCIADO"], - 1) == '1' || (substr($rsResp->fields["RADIANEXASOCIADO"], - 1) == '7')) && (substr($rsResp->fields["RADIANEXASOCIADO"], - 1) != '3') && (substr($rsResp->fields["RADIANEXASOCIADO"], - 1) != '2')) {
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
                                FROM RADICADO R WITH (NOLOCK) INNER JOIN USUARIO U WITH (NOLOCK) ON U.USUA_CODI = R.RADI_USUA_RADI
                                LEFT JOIN SGD_TPR_TPDCUMENTO TIP WITH (NOLOCK) ON TIP.SGD_TPR_CODIGO = R.TDOC_CODI
                                INNER JOIN DEPENDENCIA AS DEP WITH (NOLOCK) ON DEP.DEPE_CODI = R.RADI_DEPE_RADI AND DEP.DEPE_CODI = U.DEPE_CODI
                                LEFT JOIN SGD_RENV_REGENVIO G WITH (NOLOCK) ON G.RADI_NUME_SAL = R.RADI_NUME_RADI
                                WHERE (r.RADI_TIPORAD = 1 or r.RADI_TIPORAD = 7) and R.RADI_NUME_RADI = $anexoasociado ";
                    $rsAsoc = $conn->Execute($sqlAsoc);
                    if ($rsAsoc && ! $rsAsoc->EOF) {
                        if (strlen($fechaResp1) > 0)
                            $fechaResp1 .= " - ";
                            $fechaResp1 .= $rsAsoc->fields["FECHA_RESP"];
                            
                            if (strlen($diasResp1) > 0) {
                                $diasResp1 .= " - ";
                            }
                            if (strlen($rsAsoc->fields["DIAS_RESP"]) > 0) {
                                $diasResp1 .= $rsAsoc->fields["DIAS_RESP"];
                            }
                            
                            if (strlen($depResponde1) > 0) {
                                $depResponde1 .= " - ";
                            }
                            $depResponde1 .= $rsAsoc->fields["DEP_RESPONDE"];
                            
                            if (strlen($tipoResp1) > 0) {
                                $tipoResp1 .= " - ";
                            }
                            $tipoResp1 .= $rsAsoc->fields["TIPO_RESP"];
                            
                            if (strlen($responsable1) > 0) {
                                $responsable1 .= " - ";
                            }
                            $responsable1 .= $rsAsoc->fields["RESPONSABLE"];
                    }
            }
        }
        
        if ($rsResp->fields["RADIRADIRESPUESTA"] != '') {
            $var = substr($rsResp->fields["RADIRADIRESPUESTA"], - 1);
            if (($var == '1' || $var == '7') && (substr($rsResp->fields["RADIRADIRESPUESTA"], - 1) != '3')) {
                $radirespuesta = $rsResp->fields["RADIRADIRESPUESTA"];
                $pradirespuesta = $rsResp->fields["PATHRADIRESPUESTA"];
                if (strlen($imageResp1) > 1) {
                    $imageResp1 .= " - ";
                }
                $imageResp1 .= $pradirespuesta;
                
                $sqlResp = "SELECT R.RADI_NUME_RADI AS RADICADO, TIP.SGD_TPR_DESCRIP AS TIPO_RESP
                                        , U.USUA_NOMB AS RESPONSABLE, ( $fechResp2 ) as FECHA_RESP
                                        , dbo.diashabilestramite(('$radiFechradi2'), ($fechResp2)) as DIAS_RESP
                                        , DEP.DEPE_NOMB AS DEP_RESPONDE
                                        , G.SGD_RENV_FECH AS ENVIO
                                    FROM RADICADO R WITH (NOLOCK) INNER JOIN USUARIO U WITH (NOLOCK) ON U.USUA_CODI = R.RADI_USUA_RADI
                                    LEFT JOIN SGD_TPR_TPDCUMENTO TIP WITH (NOLOCK) ON TIP.SGD_TPR_CODIGO = R.TDOC_CODI
                                    INNER JOIN DEPENDENCIA AS DEP WITH (NOLOCK) ON DEP.DEPE_CODI = R.RADI_DEPE_RADI AND DEP.DEPE_CODI = U.DEPE_CODI
                                    LEFT JOIN SGD_RENV_REGENVIO G WITH (NOLOCK) ON G.RADI_NUME_SAL = R.RADI_NUME_RADI
                                    WHERE G.SGD_DIR_TIPO = 1 AND (R.RADI_TIPORAD = 1 or R.RADI_TIPORAD = 7) and R.RADI_NUME_RADI = $radirespuesta ";
                $rsResp = $conn->Execute($sqlResp);
                if ($rsResp && ! $rsResp->EOF) {
                    if (strlen($fechaResp1) > 0) {
                        $fechaResp1 .= " - ";
                    }
                    $fechaResp1 .= $rsResp->fields["FECHA_RESP"];
                    
                    if (strlen($diasResp1) > 0) {
                        $diasResp1 .= " - ";
                    }
                    if (strlen($rsResp->fields["DIAS_RESP"]) > 0) {
                        $diasResp1 .= $rsResp->fields["DIAS_RESP"];
                    }
                    
                    if (strlen($depResponde1) > 0) {
                        $depResponde1 .= " - ";
                    }
                    $depResponde1 .= $rsResp->fields["DEP_RESPONDE"];
                    
                    if (strlen($tipoResp1) > 0) {
                        $tipoResp1 .= " - ";
                    }
                    $tipoResp1 .= $rsResp->fields["TIPO_RESP"];
                    
                    if (strlen($responsable1) > 0) {
                        $responsable1 .= " - ";
                    }
                    $responsable1 .= $rsResp->fields["RESPONSABLE"];
                }
            }
        }
    }
    
    $respuesta3 = $radirespuesta;
    $fechaRadicado = $result->fields["FECHA_RAD"]; // *** Fecha radicado
    $tiporad = iconv("iso-8859-1", "utf-8", trim($result->fields["TIPO_DOC"])); // *** Tipo radicado
    // $asunto = mb_convert_encoding($result->fields["RA_ASUN"], "UTF-8", "UTF-8,ISO-8859-1,ASCII");
    $asunto = preg_replace('/\s+/', ' ', $result->fields["RA_ASUN"]);
    $asunto = iconv("iso-8859-1", "utf-8", str_replace('|', '', $asunto));
    $medRecepcio = iconv("iso-8859-1", "utf-8", $result->fields["M_RECEP"]); // *** Medio de recepcion
    $medRespuesol = iconv("iso-8859-1", "utf-8", $result->fields["M_RESPUESTA_SOL"]); // *** Medio de respuesta solicitado
    $pqrVerbal = $result->fields["PQR_VERBAL"]; // *** Pqr Verbal
    $depeactu = iconv("iso-8859-1", "utf-8", $result->fields["DEPE_ACTU"]); // *** Dependencia actual
    $usuaactu = iconv("iso-8859-1", "utf-8", $result->fields["USUA_ACTUAL"]); // *** Usuario actual
    $depeante = iconv("iso-8859-1", "utf-8", $resulDepe->fields["DEPE_ANTE"]); // *** Dependencia anterior
    // $padre = $result->fields["PADRE"]; // *** Radicado padre - posible respuesta2
    // $resPadre = $result->fields["RESP_PADRE"]; // *** Usuario que responde2
    $diasVence = $result->fields["RADI_DIAS_VENCE"];
    $diasTermino = $result->fields["SGD_TPR_TERMINO"];
    $telefono = $result->fields["TELEFONO"];
    $email = $result->fields["EMAIL"];
    $radcuentai = $result->fields["RADI_CUENTAI"];
    
    $termino = $result->fields["TERMINO"];
    $hist1 = iconv("iso-8859-1", "utf-8", trim(preg_replace('/\s+/', ' ', $result->fields["HIST1"]))); // *** Historico1
    $hist2 = iconv("iso-8859-1", "utf-8", trim(preg_replace('/\s+/', ' ', $result->fields["HIST2"]))); // *** Historico2
    $dep_hist = iconv("iso-8859-1", "utf-8", trim($result->fields["DEP_HISTORICO"])); // *** Historico2
    if (strlen($result->fields["FECH_VENCE"]) > 4) {
        $fechVence = $result->fields["FECH_VENCE"]; // *** Fecha creada desde alarmas
    } else {
        if ($termino > 0) {
            $fecha1 = $result->fields['FECHA1'];
            $termino1 = $termino - 1;
            $sqlFec = "SELECT dbo.sumadiasfecha($termino1, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
            $fecVence = $conn->getone($sqlFec);
            $fecVence = substr($fecVence, 0, 10);
        }
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
    $departamento = iconv("utf-8", "iso-8859-1", $result->fields["DEPARTAMENTO"]);
    $municipio = iconv("utf-8", "iso-8859-1", $result->fields["MUNICIPIO"]);
    $infPoblacional = iconv("utf-8", "iso-8859-1", $result->fields["INF_POBLACIONAL"]);
    $depResponde1 = $depResponde1;
    $remitente = iconv("utf-8", "iso-8859-1", htmlspecialchars($result->fields["REMITENTE"]));
    $docremite = $result->fields["DOCUMENTO"];
    $rem_oem = $result->fields["SGD_OEM_CODIGO"];
    $rem_ciu = $result->fields["SGD_CIU_CODIGO"];
    $rem_esp = $result->fields["SGD_ESP_CODI"];
    $rem_fun = $result->fields["SGD_DOC_FUN"];
    $rem_final = ($rem_oem) ? "Empresa" : ($rem_ciu ? "Ciudadano" : ($rem_esp ? "Entidad" : "Funcionario"));
    $esAnonimo = $result->fields["ANONIMO"] == 0 ? "No" : "Si";  
    
    $myData[0] = array(
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
        $diasTermino,
        $telefono,
        $email,
        $radcuentai,
        $departamento,
        $municipio,
        $infPoblacional,
        $depResponde1,
        $pqrVerbal,
        $medRespuesol,
        $rem_final,
        $esAnonimo,
        $producto,
        $dep_hist
    );
    unset($diasdeResp);
    $nroradicadoAnt = $nroradicado;
    
    $contenido = "";
    foreach ($myData as $item) {
        
        // 0 $nroradicado,
        $contenido .= trim($item[0]) . "|";
        // 38 $esAnonimo,
        if (strpos($contenido1, "Es_Anonimo")) {
            $contenido .= trim($item[38]) . "|";
        }
        // 37 $rem_final,
        if (strpos($contenido1, "Tipo_Tercero")) {
            $contenido .= trim($item[37]) . "|";
        }
        // 2 $tiporad,
        if (strpos($contenido1, "Tipo_de_Documento")) {
            $contenido .= trim($item[2]) . "|";
        }
        // 33 $infPoblacional,
        if (strpos($contenido1, "Inf_Poblacional")) {
            $contenido .= trim($item[33]) . "|";
        }
        // 15 $temaRad,
        if (strpos($contenido1, "Tema")) {
            $contenido .= trim($item[15]) . "|";
        }
        // 1 $fechaRadicado,
        if (strpos($contenido1, "Fecha_Radicado")) {
            $contenido .= trim($item[1]) . "|";
        }
        // 17 $fechVence,
        if (strpos($contenido1, "Fecha_Vence")) {
            $contenido .= trim($item[17]) . "|";
        }
        // 10 $fechaResp1,
        if (strpos($contenido1, "Fecha_Respuesta")) {
            $contenido .= trim($item[10]) . "|";
        }
        // 19 $diasVence,
        if (strpos($contenido1, "Dias_Vencimiento")) {
            $contenido .= trim($item[19]) . "|";
        }
        // 16 $diasResp1,
        if (strpos($contenido1, "Dias_respuesta")) {
            $contenido .= trim($item[16]) . "|";
        }
        // 9 $Respuesta1,
        if (strpos($contenido1, "Respuesta_1")) {
            $contenido .= htmlspecialchars(trim($item[9])) . "|";
        }
        // 34 $depResponde1,
        if (strpos($contenido1, "Dep_Respuesta")) {
            $contenido .= trim($item[34]) . "|";
        }
        // 25 $TipoResp1,
        if (strpos($contenido1, "Tipo_Respuesta")) {
            $contenido .= trim($item[25]) . "|";
        }
        // 22 $responsable1,
        if (strpos($contenido1, "Responsable")) {
            $contenido .= trim($item[22]) . "|";
        }
        // 6 $anexoasociado,
        if (strpos($contenido1, "Respuesta_2")) {
            $contenido .= trim($item[6]) . "|";
        }
        // 12 $respuesta3,
        if (strpos($contenido1, "Respuesta_3")) {
            $contenido .= trim($item[12]) . "|";
        }
        // 11 $imaResp1,
        if (strpos($contenido1, "Imagen_Respuesta")) {
            $contenido .= trim($item[11]) . "|";
        }
        // 18 $asunto,
        if (strpos($contenido1, "Asunto")) {
            $contenido .= htmlspecialchars(trim($item[18])) . "|";
        }
        // 3 $medRecepcio,
        if (strpos($contenido1, "Medio_de_Recepcion")) {
            $contenido .= trim($item[3]) . "|";
        }
        // 36 $medRespuesol,
        if (strpos($contenido1, "Medio_Respuesta_solicitado")) {
            $contenido .= trim($item[36]) . "|";
        }
        // 35 $pqrVerbal,
        if (strpos($contenido1, "Pqr_Verbal")) {
            $contenido .= trim($item[35]) . "|";
        }
        // 31 $departamento,
        if (strpos($contenido1, "Departamento")) {
            $contenido .= trim($item[31]) . "|";
        }
        // 32 $municipio,
        if (strpos($contenido1, "Municipio")) {
            $contenido .= trim($item[32]) . "|";
        }
        // 4 $depeactu,
        if (strpos($contenido1, "Dependencia_Actual")) {
            $contenido .= trim($item[4]) . "|";
        }
        // 24 $usuaactu,
        if (strpos($contenido1, "Usuario_Actual")) {
            $contenido .= trim($item[24]) . "|";
        }
        // 5 $depeante,
        if (strpos($contenido1, "Dependencia_anterior")) {
            $contenido .= trim($item[5]) . "|";
        }
        // 7 $hist1,
        if (strpos($contenido1, "Historico_1")) {
            $contenido .= htmlspecialchars(trim($item[7])) . "|";
        }
        // 8 $hist2,
        if (strpos($contenido1, "Historico_2")) {
            $contenido .= htmlspecialchars(trim($item[8])) . "|";
        }
        // 14 $expediente,
        if (strpos($contenido1, "Expediente")) {
            $contenido .= trim($item[14]) . "|";
        }
        // 26 $nomExp,
        if (strpos($contenido1, "Nombre_Expediente")) {
            $contenido .= htmlspecialchars(trim($item[26])) . "|";
        }
        // 20 $remitente,
        if (strpos($contenido1, "Remitente")) {
            $contenido .= htmlspecialchars(trim($item[20])) . "|";
        }
        // 21 $docremite,
        if (strpos($contenido1, "Documento")) {
            $contenido .= trim($item[21]) . "|";
        }
        // 27 $diasTermino,
        if (strpos($contenido1, "DiasTermino")) {
            $contenido .= trim($item[27]) . "|";
        }
        // 28 $telefono,
        if (strpos($contenido1, "Telefono")) {
            $contenido .= trim($item[28]) . "|";
        }
        // 29 $email,
        if (strpos($contenido1, "Email")) {
            $contenido .= trim($item[29]) . "|";
        }
        // 30 $radcuentai,
        if (strpos($contenido1, "Referencia")) {
            $contenido .= trim($item[30]) . "|";
        }
        // 39 $producto
        if (strpos($contenido1, "Producto")) {
            $contenido .= trim($item[39]) . "|";
        }
        
        // 13 $envioCorr,
        
        // 23 "",
        
        // 40 $dep_hist
         
         $contenido .= $csv_end;
    }
    
    fputs($fp, $contenido);
    //rewind($fp); 
    
    
    $result->MoveNext();
}

fclose($fp);

try {
    $archivo = dirname(__FILE__) . "\\bodega\\tmp\\Nomb" . "_$fecha" . ".xlsx";
    include_once dirname(__FILE__) . '\\lib\\PHPExcel\\IOFactory.php';
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
    
    // unlink($archivo1);
} catch (Exception $e) {
    echo 'Excepci�n capturada: ', $e->getMessage(), "\n";
    exit();
}

$emailU = Array();
// $emailU[] = "infogasc@urt.gov.co";
$emailU[] = $usua_email;

$emailCop = Array();
// $emailCop[] = "hugo.galvez@urt.gov.co";
// $emailCop[] = "sandra.gamboa@urt.gov.co";
// $emailCop[] = "luis.lopez@urt.gov.co";
// $emailCop[] = "jhon.zabala@urt.gov.co";

$emailCCO = Array();
// $emailCCO[] = "jhon.zabala@urt.gov.co";

include_once dirname(__FILE__) . "/envioEmail.php";
$objMail = new correo();

$objMail->agregarAdjunto($archivo, "Nomb" . "_$fecha" . ".xlsx");

$cuerpo = iconv("utf-8", "iso-8859-1", "Se envía el reporte de Internet, desde " . $fecha_ini . " hasta " . $fecha_fin . " de todas las PQRSDF");
$enviarCorreo = $objMail->enviarCorreo($emailU, $emailCop, $emailCCO, $cuerpo, "Reporte Internet, desde " . $fecha_ini . " hasta " . $fecha_fin);
echo " -- " . $enviarCorreo[1] . " | " . $emailU . " -- ";
$objMail->SmtpClose();

/*if ($generarOrfeo) {
    $contenido = "";
    $contenido1 = "";
    $csv_end = "
    ";

    if ($myData != null) {

        // $contenido = iconv("iso-8859-1", "utf-8", "Gener�: ");
        // $contenido .= $_SESSION['usua_nomb'] . " - Fecha: " . date('d-m-Y') . $csv_end;
        $contenido1 .= "Nro_Radicado|";
        if (isset($campos)) {
            $campos = explode("-", $campos);
            if (is_array($campos)) {
                $current = 0;
                foreach ($campos as $key => $value) {
                    if ($current != $num_campos - 1) {
                        $contenido1 .= $value . '|';
                    } else {
                        $contenido1 .= $value . $csv_end;
                        $current ++;
                    }
                }
            }
        }
        echo $contenido1;

        $contenido .= $contenido1 . $csv_end;

        foreach ($myData as $item) {

            // 0 $nroradicado,
            $contenido .= trim($item[0]) . "|";
            // 38 $esAnonimo,
            if (strpos($contenido1, "Es_Anonimo")) {
                $contenido .= trim($item[38]) . "|";
            }
            // 37 $rem_final,
            if (strpos($contenido1, "Tipo_Tercero")) {
                $contenido .= trim($item[37]) . "|";
            }
            // 2 $tiporad,
            if (strpos($contenido1, "Tipo_de_Documento")) {
                $contenido .= trim($item[2]) . "|";
            }
            // 33 $infPoblacional,
            if (strpos($contenido1, "Inf_Poblacional")) {
                $contenido .= trim($item[33]) . "|";
            }
            // 15 $temaRad,
            if (strpos($contenido1, "Tema")) {
                $contenido .= trim($item[15]) . "|";
            }
            // 1 $fechaRadicado,
            if (strpos($contenido1, "Fecha_Radicado")) {
                $contenido .= trim($item[1]) . "|";
            }
            // 17 $fechVence,
            if (strpos($contenido1, "Fecha_Vence")) {
                $contenido .= trim($item[17]) . "|";
            }
            // 10 $fechaResp1,
            if (strpos($contenido1, "Fecha_Respuesta")) {
                $contenido .= trim($item[10]) . "|";
            }
            // 19 $diasVence,
            if (strpos($contenido1, "Dias_Vencimiento")) {
                $contenido .= trim($item[19]) . "|";
            }
            // 16 $diasResp1,
            if (strpos($contenido1, "Dias_respuesta")) {
                $contenido .= trim($item[16]) . "|";
            }
            // 9 $Respuesta1,
            if (strpos($contenido1, "Respuesta_1")) {
                $contenido .= htmlspecialchars(trim($item[9])) . "|";
            }
            // 34 $depResponde1,
            if (strpos($contenido1, "Dep_Respuesta")) {
                $contenido .= trim($item[34]) . "|";
            }
            // 25 $TipoResp1,
            if (strpos($contenido1, "Tipo_Respuesta")) {
                $contenido .= trim($item[25]) . "|";
            }
            // 22 $responsable1,
            if (strpos($contenido1, "Responsable")) {
                $contenido .= trim($item[22]) . "|";
            }
            // 6 $anexoasociado,
            if (strpos($contenido1, "Respuesta_2")) {
                $contenido .= trim($item[6]) . "|";
            }
            // 12 $respuesta3,
            if (strpos($contenido1, "Respuesta_3")) {
                $contenido .= trim($item[12]) . "|";
            }
            // 11 $imaResp1,
            if (strpos($contenido1, "Imagen_Respuesta")) {
                $contenido .= trim($item[11]) . "|";
            }
            // 18 $asunto,
            if (strpos($contenido1, "Asunto")) {
                $contenido .= htmlspecialchars(trim($item[18])) . "|";
            }
            // 3 $medRecepcio,
            if (strpos($contenido1, "Medio_de_Recepcion")) {
                $contenido .= trim($item[3]) . "|";
            }
            // 36 $medRespuesol,
            if (strpos($contenido1, "Medio_Respuesta_solicitado")) {
                $contenido .= trim($item[36]) . "|";
            }
            // 35 $pqrVerbal,
            if (strpos($contenido1, "Pqr_Verbal")) {
                $contenido .= trim($item[35]) . "|";
            }
            // 31 $departamento,
            if (strpos($contenido1, "Departamento")) {
                $contenido .= trim($item[31]) . "|";
            }
            // 32 $municipio,
            if (strpos($contenido1, "Municipio")) {
                $contenido .= trim($item[32]) . "|";
            }
            // 4 $depeactu,
            if (strpos($contenido1, "Dependencia_Actual")) {
                $contenido .= trim($item[4]) . "|";
            }
            // 24 $usuaactu,
            if (strpos($contenido1, "Usuario_Actual")) {
                $contenido .= trim($item[24]) . "|";
            }
            // 5 $depeante,
            if (strpos($contenido1, "Dependencia_anterior")) {
                $contenido .= trim($item[5]) . "|";
            }
            // 7 $hist1,
            if (strpos($contenido1, "Historico_1")) {
                $contenido .= htmlspecialchars(trim($item[7])) . "|";
            }
            // 8 $hist2,
            if (strpos($contenido1, "Historico_2")) {
                $contenido .= htmlspecialchars(trim($item[8])) . "|";
            }
            // 14 $expediente,
            if (strpos($contenido1, "Expediente")) {
                $contenido .= trim($item[14]) . "|";
            }
            // 26 $nomExp,
            if (strpos($contenido1, "Nombre_Expediente")) {
                $contenido .= htmlspecialchars(trim($item[26])) . "|";
            }
            // 20 $remitente,
            if (strpos($contenido1, "Remitente")) {
                $contenido .= htmlspecialchars(trim($item[20])) . "|";
            }
            // 21 $docremite,
            if (strpos($contenido1, "Documento")) {
                $contenido .= trim($item[21]) . "|";
            }
            // 27 $diasTermino,
            if (strpos($contenido1, "DiasTermino")) {
                $contenido .= trim($item[27]) . "|";
            }
            // 28 $telefono,
            if (strpos($contenido1, "Telefono")) {
                $contenido .= trim($item[28]) . "|";
            }
            // 29 $email,
            if (strpos($contenido1, "Email")) {
                $contenido .= trim($item[29]) . "|";
            }
            // 30 $radcuentai,
            if (strpos($contenido1, "Referencia")) {
                $contenido .= trim($item[30]) . "|";
            }
            // 39 $producto
            if (strpos($contenido1, "Producto")) {
                $contenido .= trim($item[39]) . "|";
            }

            // 13 $envioCorr,

            // 23 "",

            // 40 $dep_hist

            /*
             * $contenido .= trim($item[0]) . "|";
             * $contenido .= trim($item[37]) . "|";
             *
             * $contenido .= trim($item[33]) . "|";
             * $contenido .= trim($item[15]) . "|";
             *
             * $contenido .= trim($item[17]) . "|";
             * $contenido .= trim($item[10]) . "|";
             * $contenido .= trim($item[19]) . "|";
             * $contenido .= trim($item[16]) . "|";
             * $contenido .= htmlspecialchars(trim($item[9])) . "|";
             * $contenido .= trim($item[34]) . "|";
             * $contenido .= trim($item[25]) . "|";
             * $contenido .= trim($item[22]) . "|";
             * $contenido .= trim($item[23]) . "|";
             * $contenido .= trim($item[12]) . "|";
             * $contenido .= trim($item[11]) . "|";
             * $contenido .= htmlspecialchars(trim($item[18])) . "|";
             *
             * $contenido .= trim($item[36]) . "|";
             * $contenido .= trim($item[35]) . "|";
             * $contenido .= trim($item[31]) . "|";
             * $contenido .= trim($item[32]) . "|";
             * $contenido .= trim($item[4]) . "|";
             * $contenido .= trim($item[24]) . "|";
             * $contenido .= trim($item[5]) . "|";
             * $contenido .= htmlspecialchars(trim($item[7])) . "|";
             * $contenido .= htmlspecialchars(trim($item[8])) . "|";
             * $contenido .= trim($item[14]) . "|";
             * $contenido .= htmlspecialchars(trim($item[26])) . "|";
             * $contenido .= htmlspecialchars(trim($item[20])) . "|";
             * $contenido .= trim($item[21]) . "|";
             * $contenido .= trim($item[27]) . "|";
             * $contenido .= trim($item[28]) . "|";
             * $contenido .= trim($item[29]) . "|";
             * $contenido .= "|";
             * $contenido .= trim($item[38]) . "|";
             * $contenido .= trim($item[39]) . "|";
             *

            $contenido .= $csv_end;
        }
    }
    // var que almacena la fecha formateada
    $fecha = date('YmdHis');
    // guarda el path del archivo generado
    $archivo1 = dirname(__FILE__) . "\\bodega\\tmp\\Nomb" . "_$fecha" . ".csv";
    $fp = fopen($archivo1, "wb");
    fputs($fp, $contenido);
    fclose($fp);

    try {
        $archivo = dirname(__FILE__) . "\\bodega\\tmp\\Nomb" . "_$fecha" . "$hora" . ".xlsx";
        include_once dirname(__FILE__) . '\\lib\\PHPExcel\\IOFactory.php';
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

        // unlink($archivo1);
    } catch (Exception $e) {
        echo 'Excepci�n capturada: ', $e->getMessage(), "\n";
        exit();
    }
}*/

/*$emailU = Array();
// $emailU[] = "infogasc@urt.gov.co";
$emailU[] = $usua_email;

$emailCop = Array();
// $emailCop[] = "hugo.galvez@urt.gov.co";
// $emailCop[] = "sandra.gamboa@urt.gov.co";
// $emailCop[] = "luis.lopez@urt.gov.co";
// $emailCop[] = "jhon.zabala@urt.gov.co";

$emailCCO = Array();
// $emailCCO[] = "jhon.zabala@urt.gov.co";

include_once dirname(__FILE__) . "/envioEmail.php";
$objMail = new correo();

$objMail->agregarAdjunto($archivo, "Nomb" . "_$fecha" . "$hora" . ".xlsx");

$cuerpo = iconv("utf-8", "iso-8859-1", "Se envía el reporte de Internet, desde " . $fecha_ini . " hasta " . $fecha_fin . " de todas las PQRSDF");
$enviarCorreo = $objMail->enviarCorreo($emailU, $emailCop, $emailCCO, $cuerpo, "Reporte Internet, desde " . $fecha_ini . " hasta " . $fecha_fin);
echo " -- " . $enviarCorreo[1] . " | " . $emailU . " -- ";
$objMail->SmtpClose();*/

echo "Finalizamos .. " . date('Ymd H:i:s') . "<br/>";
?>