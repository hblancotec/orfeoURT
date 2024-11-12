<?php
set_time_limit(0);
echo "\n" . "Inicia Alarmas: " . date('Y/m/d_h:i:s') . "\n";

// ############################################################################
// # ARCHIVOS REQUERIDOS PARA EJECUTAR ESTE SCRIPT
require dirname(__FILE__) . "/config.php";
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsnn);

function daysWeek($inicio, $fin, $holidays){
    
    $start = new DateTime($inicio);
    $end = new DateTime($fin);
    
    //de lo contrario, se excluye la fecha de finalización (¿error?)
    $end->modify('+1 day');
    
    $interval = $end->diff($start);
    
    // total dias
    $days = $interval->days;
    
    // crea un período de fecha iterable (P1D equivale a 1 día)
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
    
    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
        
    foreach($period as $dt) {
        $curr = $dt->format('D');
        
        // obtiene si es Sábado o Domingo
        if($curr == 'Sat' || $curr == 'Sun') {
            $days--;
        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
            $days--;
        }
    }
    
    return $days;
}

function sumasdiasemana($fecha,$dias,$holidays)
{
    /*$datestart= strtotime($fecha);
    $datesuma = 15 * 86400;
    $diasemana = date('N',$datestart);
    $totaldias = $diasemana+$dias;
    $findesemana = intval( $totaldias/5) *2 ;
    $diasabado = $totaldias % 5 ;
    if ($diasabado==6) $findesemana++;
    if ($diasabado==0) $findesemana=$findesemana-2;
    
    $total = (($dias+$findesemana) * 86400)+$datestart ;
    return $twstart=date('Y-m-d', $total);*/
    
    $fechaInicial = date($fecha);
    $fechaEnSegundos = strtotime($fechaInicial);
    $diasAumentar = $dias;
    $dia = 86400;
    
    $contador = 1;
    $fechaEnSegundos += $dia;
    while ($contador <= $diasAumentar) {
        $var = date('N',$fechaEnSegundos); 
        if (date('N',$fechaEnSegundos) == 6 or date('N',$fechaEnSegundos) == 7) {
            $fechaEnSegundos += $dia;
        } elseif (in_array(date('d/m/Y',$fechaEnSegundos), $holidays)) {
            $fechaEnSegundos += $dia;
        } else {
            if ($contador != $dias) {
                $fechaEnSegundos += $dia;
            }
            $contador +=1;
        }
    }
    
    return date('Y-m-d h:i:s' , $fechaEnSegundos);
}

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $sql = "SELECT count(SGD_FESTIVO) from SGD_DIAS_FESTIVOS where SGD_FESTIVO='" . date('d/m/Y') . "'";
    $esFestivo = $conn->GetOne($sql);
    if ($esFestivo) {
        die("No se ejecuta porque es festivo.");
    }
    
    $holidays = Array();
    $sql = "SELECT SGD_FESTIVO from SGD_DIAS_FESTIVOS";
    $esFestivo = $conn->Execute($sql);
    if ($esFestivo && !$esFestivo->EOF) {
        while (! $esFestivo->EOF) {
            
            $holidays[] = $esFestivo->fields['SGD_FESTIVO'];
            
            $esFestivo->MoveNext();
        }
    }
    
    $queryUsua = "SELECT u.USUA_CODI, u.DEPE_CODI, u.USUA_NOMB, u.USUA_EMAIL 
                  FROM USUARIO u
                  where (u.USUA_ESTA = 1 or u.USUA_ESTA = 2) and u.DEPE_CODI not in (900, 999) "; // and u.USUA_EMAIL != '' and u.USUA_EMAIL is not null ";
    $rsusua = $conn->Execute($queryUsua);
    if ($rsusua && ! $rsusua->EOF) {
        while (! $rsusua->EOF) {

            // ##########################################################################
            // ## CONSULTA DE TODOS LOS RADICADOS CON CONDICION DE PQR'S, ES DECIR
            // ## "SGD_TPR_NOTIFICA = 1"
            $cuerpo = "";

            $isql = "SELECT	R.RADI_NUME_RADI	AS RADICADO," . $conn->SQLDate('Y-m-d H:i:s', 'R.RADI_FECH_RADI') . " AS FECHA,
							FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1, " . $conn->SQLDate('d-m-Y H:i:s', 'R.RADI_FECH_RADI') . " AS FECHA2,
        					R.RADI_USUA_ACTU	AS USUA,
        					R.RADI_DEPE_ACTU	AS DEPE,
        					R.RADI_FECHA_VENCE	AS VENCE,
        					R.RADI_DIAS_VENCE	AS DIASVENCE,
        					T.SGD_TPR_DESCRIP	AS TIPO,
        					T.SGD_TPR_TERMINO	AS TERMINO,
        					T.SGD_TPR_ALERTA	AS ALERTA,
        					T.SGD_TPR_NOTIFICA	AS NOTIFICA,
        					U.USUA_NOMB			AS NOMBRE,
        					U.USUA_EMAIL		AS EMAIL,
        					D.DEPE_NOMB			AS DEPENDENCIA,
                            T.SGD_TPR_CODIGO	AS CODTIPO
        			FROM	RADICADO R
        					JOIN USUARIO U ON 
        						U.USUA_CODI = R.RADI_USUA_ACTU AND
        						U.DEPE_CODI = R.RADI_DEPE_ACTU
        					JOIN DEPENDENCIA D ON
        						D.DEPE_CODI = R.RADI_DEPE_ACTU
        					JOIN SGD_TPR_TPDCUMENTO T ON
        						T.SGD_TPR_CODIGO = R.TDOC_CODI AND
        						T.SGD_TPR_NOTIFICA = 1
        			WHERE	R.RADI_FECH_RADI >= '2023-01-01' AND 
        					R.RADI_TIPORAD = 2 
                            and U.USUA_CODI = '" . $rsusua->fields['USUA_CODI'] . "'
                            and U.DEPE_CODI = '" . $rsusua->fields['DEPE_CODI'] . "'
        			ORDER BY FECHA ";
            $result = $conn->Execute($isql);
            if ($result && ! $result->EOF) {
                while (! $result->EOF) {

                    $radicado = $result->fields['RADICADO'];
                    $fechaRad = $result->fields['FECHA'];
                    $fecha1 = $result->fields['FECHA1'];
                    $fecha2 = $result->fields['FECHA2'];
                    $termino = $result->fields['TERMINO'];
                    $codtipo = $result->fields['CODTIPO'];
                    $alerta = $result->fields['ALERTA'];
                    $nomusua = $result->fields['NOMBRE'];
                    $mailusua = $result->fields['EMAIL'];
                    $tipo = $result->fields["TIPO"];
                    $enviaMail = 1; // Bandera que determina si se envia o no correo electronico (1 indica que SI; 0 indica que NO)
                    $flag = 0;

                    $tdocResp = "";
                    $pathResp = "";
                    $respuesta = "";

                    $sqlResp = "SELECT R.RADI_NUME_RADI AS RADICADO, A.RADI_NUME_SALIDA AS RESPUESTA, TIP.SGD_TPR_DESCRIP AS TIPO_RESP, TIP.SGD_TPR_CODIGO AS COD_TIPO_DOC, U1.USUA_NOMB AS RESPONSABLE
                                    , (	SELECT RE.RADI_PATH FROM RADICADO RE WHERE RE.RADI_NUME_RADI = A.RADI_NUME_SALIDA) AS IMAGEN_RESP, A.ANEX_NOMB_ARCHIVO AS IMAGEN_ANEXO
                                    , ( CASE  WHEN a.ANEX_ESTADO = 4 THEN ('CORRESPONDENCIA') ELSE NULL END) AS ENVI_CORRES, DEP.DEPE_NOMB AS DEP_RESPONDE, DEP.DEPE_CODI AS COD_DEP_RESPONDE, G.SGD_RENV_FECH AS ENVIO
                                FROM RADICADO R LEFT JOIN ANEXOS A ON A.ANEX_RADI_NUME = R.RADI_NUME_RADI AND A.ANEX_SALIDA = 1 AND A.anex_radi_nume <> A.radi_nume_salida AND A.anex_borrado = 'N'
                                    LEFT JOIN USUARIO U1 ON U1.USUA_LOGIN = A.ANEX_CREADOR
                                    LEFT JOIN RADICADO AS R2 ON R2.RADI_NUME_RADI = R.RADI_NUME_DERI
                                    LEFT JOIN RADICADO RR ON RR.RADI_NUME_RADI = A.RADI_NUME_SALIDA
                                    LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = RR.TDOC_CODI
                                    LEFT JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = RR.RADI_DEPE_RADI
                                    LEFT JOIN SGD_RENV_REGENVIO G ON G.RADI_NUME_SAL = A.RADI_NUME_SALIDA
                                WHERE r.RADI_TIPORAD = 2 and R.RADI_NUME_RADI = $radicado and G.SGD_DIR_TIPO = 1
                                UNION ALL
                                SELECT R.RADI_NUME_RADI AS RADICADO, C.RADI_NUME_RADI AS RESPUESTA, TIP.SGD_TPR_DESCRIP AS TIPO_RESP, TIP.SGD_TPR_CODIGO AS COD_TIPO_DOC,
                                	U.USUA_NOMB AS RESPONSABLE, C.RADI_PATH AS IMAGEN_RESP, '' AS IMAGEN_ANEXO, CAST(G.SGD_RENV_ESTADO AS VARCHAR(1))  AS ENVI_CORRES,
                                    DEP.DEPE_NOMB AS DEP_RESPONDE, DEP.DEPE_CODI AS COD_DEP_RESPONDE, G.SGD_RENV_FECH AS ENVIO
                                FROM RADICADO R INNER JOIN RADICADO C ON C.RADI_NUME_RADI = R.RADI_NUME_DERI
                                	INNER JOIN USUARIO U ON U.USUA_CODI = C.RADI_USUA_RADI
                                    LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = C.TDOC_CODI
                                    INNER JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = C.RADI_DEPE_RADI AND DEP.DEPE_CODI = U.DEPE_CODI
                                    LEFT JOIN SGD_RENV_REGENVIO G ON G.RADI_NUME_SAL = C.RADI_NUME_RADI AND G.SGD_DIR_TIPO = 1
                                WHERE R.RADI_NUME_RADI = $radicado AND (C.RADI_TIPORAD = 1 or C.RADI_TIPORAD = 7)
                                UNION ALL
                                SELECT R.RADI_NUME_RADI AS RADICADO, D.RADI_NUME_RADI AS RESPUESTA, TIP.SGD_TPR_DESCRIP AS TIPO_RESP, TIP.SGD_TPR_CODIGO AS COD_TIPO_DOC, 
                                	U.USUA_NOMB AS RESPONSABLE, D.RADI_PATH AS IMAGEN_RESP,  '' AS IMAGEN_ANEXO, CAST(G.SGD_RENV_ESTADO AS VARCHAR(1)) AS ENVI_CORRES,
                                    DEP.DEPE_NOMB AS DEP_RESPONDE, DEP.DEPE_CODI AS COD_DEP_RESPONDE, G.SGD_RENV_FECH AS ENVIO
                                FROM RADICADO R INNER JOIN RADICADO D ON D.RADI_NUME_RADI = R.RADI_RESPUESTA
                                	INNER JOIN USUARIO U ON U.USUA_CODI = D.RADI_USUA_RADI
                                    LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = D.TDOC_CODI
                                    INNER JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = D.RADI_DEPE_RADI AND DEP.DEPE_CODI = U.DEPE_CODI
                                    LEFT JOIN SGD_RENV_REGENVIO G ON G.RADI_NUME_SAL = D.RADI_NUME_RADI AND G.SGD_DIR_TIPO = 1 
                                WHERE R.RADI_NUME_RADI = $radicado AND (D.RADI_TIPORAD = 1 or D.RADI_TIPORAD = 7) 
                                ORDER BY 2";
                    $rsResp = $conn->Execute($sqlResp);
                    if ($rsResp && !$rsResp->EOF) {

                        while (!$rsResp->EOF) {

                            $tdocResp = $rsResp->fields['COD_TIPO_DOC'];
                            $pathResp = $rsResp->fields['IMAGEN_RESP'];
                            $respuesta = $rsResp->fields['RESPUESTA'];

                            // # SE VERIFICA SI VIENE UNA RESPUESTA
                            if (substr($respuesta, - 1, 1) == 1) {

								$enviaMail = 0;
                                break;
                                
								/*$sqlEnvio = " SELECT R.SGD_RENV_FECH FROM SGD_RENV_REGENVIO R WHERE R.RADI_NUME_SAL = $respuesta ";
                                $rsEnvio = $conn->Execute($sqlEnvio);
                                if ($rsEnvio && ! $rsEnvio->EOF) {

                                    // # SE VERIFICA SI LA RESPUESTA NO ESTA TIPIFICADA COMO SUSPENSION (CODIGO 3181)
                                    if ($tdocResp != 3181) {
                                        $enviaMail = 0;
                                        break;
                                    }
                                }*/
                            }

                            $rsResp->MoveNext();
                        }
                    }

                    // # LA RESPUESTA ES UNA SUSPENSION DE TERMINOS, POR LO TANTO SE DUPLICAN LOS TIEMPOS INICIALES DE LA PQR
                    if ($tdocResp == 3181) {
                        $alerta = $alerta + $termino; // Se duplica el tiempo en que se deben iniciar las alertas
                        $termino = $termino * 2; // Se duplica el termino de la PQR, porque tiene una salida tipificada como Suspension de terminos
                    }

                    // # SE VERIFICA SI EL RADICADO SE ENCUENTRA ARCHIVADO
                    if ($result->fields['USUA'] == 1 && $result->fields['DEPE'] == 9999) {
                        $enviaMail = 0;
                    } elseif ($result->fields['DEPE'] == 9000) {
                        $enviaMail = 0;
                    }
					if ($tdocResp == 1400 || $tdocResp == 1471) {
                        $enviaMail = 0;
                    }
					if ($codtipo == 1400 || $codtipo == 1471) {
                        $enviaMail = 0;
                    }
                    // # SE INVOCA LA FUNCION SUMADIASHABILES, PARA DETERMINAR LA FECHA DE VENCIMIENTO

                    // # SE INVOCA LA FUNCION diashabilestramite, PARA DETERMINAR LA CANTIDAD DE DIAS QUE HAN
                    // # TRANSCURRIDO DESDE QUE SE GENERO EL RADICADO HASTA EL DIA ACTUAL
                   
                    //$sqlDias1 = daysWeek($fecha1, date("Ymd"), $holidays);
                    
                    $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                    $diasTram = ($conn->getone($sqlDiasV));

                    //$sqlFec = "SELECT dbo.sumadiasfecha($termino, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                    //$fecVence = $conn->getone($sqlFec);
                    $fecVence = sumasdiasemana($fechaRad, $termino, $holidays);

                    $diasRest = $termino - $diasTram;

                    if ($flag == 1) {
                        $diasRest = 0;
                    }

                    // # SE OBTIENE EL MAXIMO NUMERO DE DIAS HASTA EL CUAL SE DEBE GENERAR LA ALERTA PRINCIPAL,
                    // # QUE SON DOS DIAS MAS DEL TERMINO OFICIAL.
                    $diasMax = $termino + 1; // DIA MENOS

                    $fecVence = substr($fecVence, 0, 10);

                    // ## ACTUALIZACION DE FECHAS
                    //echo "	Se actualiza la fecha de vencimiento: " . $fecVence . " el termino legal
            		//		es: " . $termino . " y han transcurrido " . $diasTram . " dias desde la radicacion\\n";

                    $update = "UPDATE RADICADO SET RADI_FECHA_VENCE = '$fecVence',
            		          RADI_DIAS_VENCE = $diasRest
            		          WHERE	RADI_NUME_RADI = $radicado";
                    $rsUp = $conn->Execute($update);

                    // ##########################################################################################################################
                    // # SI $diasTram ES SUPERIOR A LOS DIAS DE $alerta Y LA VARIABLE $enviaMail = 1, ENTRA PARA GENERAR NOTIFICACIONES AL CORREO
                    if ($enviaMail == 1 && $diasTram > $alerta) {

                        // ## ENTRA CUANDO CUANDO EL RADICADO ESTA EN TERMINO Y HASTA MAXIMO 2 DIAS DE VENCIDO
                        if ($diasTram <= $diasMax) {

                            if ($diasRest == 0) {
                                $diasN = $diasRest;
                                $diasMsg = "Hoy vence el Radicado ";
                            } elseif ($diasRest < 0) {
                                $diasN = $diasRest * (- 1);
                                $diasMsg = "Hace " . $diasN . " dia(s) vencio el Radicado ";
                            } elseif ($diasRest > 0) {
                                $diasMsg = "En " . $diasRest . " dia(s) h&aacute;bil(es), vence el radicado ";
                            }

                            if (strlen($cuerpo) == 0) {
                                $cuerpo = " <table border='1'> <thead> <tr>
                                            <th> USUARIO </th> <th> RADICADO </th> <th> TIPO </th> <th> FECHA GENERACION </th> <th> FECHA VENCIMIENTO </th> <th> DESCRIPCION </th>
                                            </tr> </thead>
                                            <tbody> <tr>
                                            <td> " . $nomusua . " </td> <td> " . $radicado . " </td> <td> " . $tipo . " </td> <td> " . $fechaRad . " </td>
                                            <td> " . $fecVence . " </td> <td> " . $diasMsg . " </td>
                                            </tr> ";
                            } else {
                                $cuerpo .= " <tr>
                                            <td> " . $nomusua . " </td> <td> " . $radicado . " </td> <td> " . $tipo . " </td> <td> " . $fechaRad . " </td>
                                            <td> " . $fecVence . " </td> <td> " . $diasMsg . " </td>
                                            </tr> ";
                            }

                            // #############################################################
                            // ## CONSULTA SI EL RADICADO QUE GENERA ALERTA, TIENE DERIVADOS
                            /*
                             * $query1 =" SELECT US.USUA_EMAIL
                             * FROM SGD_RG_MULTIPLE RG
                             * JOIN USUARIO US ON
                             * US.DEPE_CODI = RG.AREA AND
                             * US.USUA_CODI = RG.USUARIO
                             * WHERE RG.RADI_NUME_RADI = $radicado AND
                             * RG.ESTATUS = 'ACTIVO'";
                             * $rs_derivaCC = $db->conn->Execute($query1);
                             *
                             * while(!$rs_derivaCC->EOF) {
                             * if ($rs_derivaCC->fields['USUA_EMAIL']){
                             * echo "y Derivado a: ".$rs_derivaCC->fields['USUA_EMAIL']."<br/>";
                             * $destinoMail[] = $rs_derivaCC->fields['USUA_EMAIL'];
                             * }
                             * $rs_derivaCC->MoveNext();
                             * }
                             */
                            // ##################################################################
                        }
                    }

                    $result->MoveNext();
                }
            }

            if (strlen($cuerpo) > 10) {
                $cuerpo .= "</tbody> </table>";
                echo $cuerpo . " <br> " . utf8_encode($nomusua) . " -- $mailusua <br/>";

                $usuaCC	= array();
                $usuaCC[] = "infogasc@urt.gov.co";
                
                $cco	= array();
                $cco[] = 'andrea.martineza@urt.gov.co';
                
                $asunto = "Orfeo URT - Vencimiento de Radicados ";
                include_once dirname(__FILE__) . "/envioEmail.php";
                $objMail = new correo();
                $result = $objMail->enviarCorreo(array($mailusua), $usuaCC, $cco, $cuerpo, $asunto);
                echo $result;
                // include_once ("./envioEmail.php");
                // enviarCorreo ("", array($mailusua), array("notificaservicioalciudadano@dnp.gov.co"), $cuerpo, $asunto);
            }

            $rsusua->MoveNext();
        }
    }
}

/*
 * $nuevostiempos = " UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 30, SGD_TPR_ALERTA = 25 WHERE SGD_TPR_CODIGO = 28;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 30, SGD_TPR_ALERTA = 25 WHERE SGD_TPR_CODIGO = 748;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 35, SGD_TPR_ALERTA = 30 WHERE SGD_TPR_CODIGO = 991;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 30, SGD_TPR_ALERTA = 25 WHERE SGD_TPR_CODIGO = 1057;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 30, SGD_TPR_ALERTA = 25 WHERE SGD_TPR_CODIGO = 2120;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 20, SGD_TPR_ALERTA = 15 WHERE SGD_TPR_CODIGO = 2131;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 30, SGD_TPR_ALERTA = 25 WHERE SGD_TPR_CODIGO = 4118;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 30, SGD_TPR_ALERTA = 25 WHERE SGD_TPR_CODIGO = 4117;
 * UPDATE SGD_TPR_TPDCUMENTO SET SGD_TPR_TERMINO = 20, SGD_TPR_ALERTA = 15 WHERE SGD_TPR_CODIGO = 4289; ";
 * $rsNT = $db->conn->Execute($nuevostiempos);
 */
// ############################################################################
echo "\n" . "Finaliza Alarmas: " . date('Y/m/d_h:i:s') . "\n";
?>