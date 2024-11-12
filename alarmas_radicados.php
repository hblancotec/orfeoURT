<?php
set_time_limit(0);
echo "\n" . "Inicia Alarmas: " . date('Y/m/d_h:i:s') . "\n";

$ruta_raiz = ".";
require dirname(__FILE__) . "/config.php";
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsnn);

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);

    // Validamos si es festivo
    $sql = "SELECT count(SGD_FESTIVO) from SGD_DIAS_FESTIVOS where SGD_FESTIVO='" . date('Y/m/d') . "'";
    $esFestivo = $conn->GetOne($sql);
    if ($esFestivo) {
        die("No se ejecuta porque es festivo.");
    }

    // ##########################################################################
    // ## SE CALCULA LA FECHA DE 2 MESES ATRAS, A PARTIR DE LA FECHA ACTUAL
    /*
     * Se comentarea esta opción por solicitud de la mesa de ayuda de Orfeo
     * $ano_ini = date("Y");
     * $mes_ini = substr("00".(date("m")-3),-2);
     * $dia_ini = date("d");
     *
     * switch ($mes_ini){
     * case '0':
     * $mes_ini="12";
     * $ano_ini = date("Y") - 1;
     * break;
     * case '-1':
     * $mes_ini="11";
     * $ano_ini = date("Y") - 1;
     * break;
     * case '-2':
     * $mes_ini="10";
     * $ano_ini = date("Y") - 1;
     * break;
     * case '2':
     * if($dia_ini == 29 or $dia_ini == 30){
     * $dia_ini = 27;
     * }
     * }
     *
     * $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
     */
    // ##########################################################################

    $queryUsua = "SELECT u.USUA_CODI, u.DEPE_CODI
                  FROM USUARIO u
                  where u.USUA_ESTA = 1 and u.USUA_EMAIL != '' and u.USUA_EMAIL is not null 
                    and u.DEPE_CODI <> 900 and u.DEPE_CODI <> 999 ";
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
        			WHERE	R.RADI_FECH_RADI >= '2021-01-01' AND
        					R.RADI_NUME_RADI like '%2'
                            and U.USUA_CODI = '" . $rsusua->fields['USUA_CODI'] . "'
    					    and U.DEPE_CODI = '" . $rsusua->fields['DEPE_CODI'] . "' 
        			ORDER BY NOMBRE ";
            $result = $conn->Execute($isql);
            while ($result && ! $result->EOF) {
                $radicado = $result->fields['RADICADO'];
                $fechaRad = $result->fields['FECHA'];
                $fecha1 = $result->fields['FECHA1'];
                $fecha2 = $result->fields['FECHA2'];
                $termino = $result->fields['TERMINO'];
                $alerta = $result->fields['ALERTA'];
                $nombre = $result->fields['NOMBRE'];
                $email = $result->fields['EMAIL'];
                $tipo = $result->fields["TIPO"];
                $codtipo = $result->fields['CODTIPO'];
                $enviaMail = 1;
                $flag = 0;

                /*$fecha_radica = strtotime($fecha2);
                $fecha_entrada = strtotime("10-03-2020 00:00:00");
                $fecha_finaliza = strtotime("18-05-2022 00:00:00");
                
                if ($fecha_radica >= $fecha_entrada && $fecha_radica <= $fecha_finaliza) {
                    switch ($codtipo) {
                        case 2:
                            $termino = 30;
                            $alerta = 25;
                        case 28:
                            $termino = 30;
                            $alerta = 25;
                            break;
                        case 748:
                            $termino = 30;
                            $alerta = 25;
                            break;
                        case 991:
                            $termino = 35;
                            $alerta = 30;
                            break;
                        case 1057:
                            $termino = 30;
                            $alerta = 25;
                            break;
                        case 2120:
                            $termino = 30;
                            $alerta = 25;
                            break;
                        case 2131:
                            $termino = 20;
                            $alerta = 15;
                            break;
                        case 4118:
                            $termino = 30;
                            $alerta = 25;
                            break;
                        case 4117:
                            $termino = 30;
                            $alerta = 25;
                            break;
                        case 4289:
                            $termino = 20;
                            $alerta = 15;
                            break;
                            // default:
                            // echo "Your favorite color is neither red, blue, nor green!";
                    }
                } else {
                    switch ($codtipo) {
                        case 2:
                            $termino = 15;                         
                            $alerta = 10;
                        case 28:
                            $termino = 15;
                            $alerta = 10;
                            break;
                        case 748:
                            $termino = 15;
                            $alerta = 10;
                            break;
                        case 991:
                            $termino = 30;
                            $alerta = 25;
                            break;
                        case 1057:
                            $termino = 15;
                            $alerta = 10;
                            break;
                        case 2120:
                            $termino = 15;
                            $alerta = 10;
                            break;
                        case 2131:
                            $termino = 10;
                            $alerta = 5;
                            break;
                        case 4118:
                            $termino = 15;
                            $alerta = 10;
                            break;
                        case 4117:
                            $termino = 15;
                            $alerta = 10;
                            break;
                        case 4289:
                            $termino = 10;
                            $alerta = 5;
                            break;
                    }
                }*/
                
                // echo '<br/><br/>Radicado No.: ' . $radicado . ' con Fecha: ' . $fechaRad;
                $sqlResp = "SELECT	A.RADI_NUME_SALIDA,
    								R.TDOC_CODI,
    								R.RADI_PATH
    						FROM	ANEXOS A
    								LEFT JOIN RADICADO R ON R.RADI_NUME_RADI = A.RADI_NUME_SALIDA
    						WHERE	A.ANEX_RADI_NUME = $radicado AND
    								A.RADI_NUME_SALIDA LIKE '%1' AND
    								A.ANEX_SALIDA = 1
    						ORDER BY R.RADI_FECH_RADI DESC";
                $rsResp = $conn->Execute($sqlResp);
                if ($rsResp && ! $rsResp->EOF) {
                    while (! $rsResp->EOF) {
                        $tdocResp = $rsResp->fields['TDOC_CODI'];
                        $pathResp = $rsResp->fields['RADI_PATH'];
                        $respuesta = $rsResp->fields['RADI_NUME_SALIDA'];

                        // # SE VERIFICA SI TIENE UNA RESPUESTA
                        if (substr($respuesta, - 1, 1) == 1) {

                            // # SE VERIFICA SI LA RESPUESTA NO ESTA TIPIFICADA COMO SUSPENSION (CODIGO 3181) Y SI TIENE IMAGEN ASOCIADA
                            if ($tdocResp != 3181 && substr($pathResp, - 3, 3) == 'pdf') {
                                $enviaMail = 0;
                                $flag = 1;
                                break;
                            }
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
                if ($result->fields['USUA'] == 1 && $result->fields['DEPE'] == 999) {
                    $enviaMail = 0;
                }

                // # POR SOLICITUD DE SECRETARIA GENERAL-ATENCION AL CIUDADANO Y CON VobO DE MESA DE AYUDA DE ORFEO, LAS PQR DEBEN
                // # INICIAR EL CONTEO A PARTIR DE LA FECHA DE RADICACION, ES DECIR CONTAR COMO EL DÍA 1 EL MISMO DÍA EN QUE SE
                // # RADICA LA SOLICITUD, DE ACUERDO A ESTO EN ALGUNAS VARIABLES SE RESTA O SUMA 1 DÍA, PARA QUE LOS TIEMPOS DE
                // # TERMINOS Y ALERTAS CONCUERDEN CON LO SOLICITADO Y ASI NO TENER QUE AFECTAR LAS FUNCIONES DE BD QUE HACEN ESTOS
                // # CALCULOS YA QUE TAMBIEN SON UTILIZADAS POR OTRAS FUNCIONALIDADES DE ORFEO. 16/06/2016 "TAG: //DIA MENOS"

                // # SE INVOCA LA FUNCION SUMADIASHABILES, PARA DETERMINAR LA FECHA DE VENCIMIENTO
                $termino1 = $termino - 2;
                $sqlFec = "SELECT dbo.sumadiasfecha($termino1, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                $fecVence = $conn->getone($sqlFec);

                // # SE INVOCA LA FUNCION diashabilestramite, PARA DETERMINAR LA CANTIDAD DE DIAS QUE HAN
                // # TRANSCURRIDO DESDE QUE SE GENERO EL RADICADO HASTA EL DIA ACTUAL
                $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                $diasTram = ($conn->getone($sqlDiasV)) + 1; // DIA MENOS

                $diasRest = $termino - $diasTram; // dias restantes para dar respuesta

                if ($flag == 1) {
                    $diasRest = 0;
                }

                // # SE OBTIENE EL MAXIMO NUMERO DE DIAS HASTA EL CUAL SE DEBE GENERAR LA ALERTA PRINCIPAL,
                // # QUE SON DOS DIAS MAS DEL TERMINO OFICIAL.
                $diasMax = $termino + 1; // DIA MENOS

                $fecVence = substr($fecVence, 0, 10);

                // ## ACTUALIZACION DE FECHAS
                // echo " Se actualiza la fecha de vencimiento: " . $fecVence . " el termino legal
                // es: " . $termino . " y han transcurrido " . $diasTram . " dias desde la radicacion";

                /*
                 * $update = " UPDATE RADICADO
                 * SET RADI_FECHA_VENCE = '$fecVence',
                 * RADI_DIAS_VENCE = $diasRest
                 * WHERE RADI_NUME_RADI = $radicado";
                 * $rsUp = $db->conn->Execute($update);
                 */

                // ##########################################################################################################################
                // # SI $diasTram ES SUPERIOR A LOS DIAS DE $alerta Y LA VARIABLE $enviaMail = 1, ENTRA PARA GENERAR NOTIFICACIONES AL CORREO
                if ($enviaMail == 1 && $diasTram >= $alerta) {

                    // ## ENTRA CUANDO CUANDO EL RADICADO TIENE MAS DE 2 DIAS DE VENCIDO
                    if ($diasTram > $diasMax) {

                        if (strlen($cuerpo) == 0) {
                            $cuerpo = " <table border='1'> <thead> <tr>
                                    <th> USUARIO </th> <th> RADICADO </th> <th> TIPO </th> <th> FECHA GENERACION </th> <th> FECHA VENCIMIENTO </th> <th> DESCRIPCION </th>
                                    </tr> </thead>
                                    <tbody> <tr>
                                    <td> " . $nombre . " </td> <td> " . $radicado . " </td> <td> " . $tipo . " </td> <td> " . $fechaRad . " </td>
                                    <td> " . $fecVence . " </td> <td> No reporta una respuesta anexa </td>
                                    </tr> ";
                        } else {
                            $cuerpo .= " <tr>
                                    <td> " . $nombre . " </td> <td> " . $radicado . " </td> <td> " . $tipo . " </td> <td> " . $fechaRad . " </td>
                                    <td> " . $fecVence . " </td> <td> No reporta una respuesta anexa </td>
                                    </tr> ";
                        }
                    }

                    // ##################################################################################################
                    // ## CONSULTA DEL CORREO DE LOS USUARIOS A LOS CUALES LES DEBE LLEGAR COPIA DE LAS ALERTAS DE PQR'S,
                    // ## ES DECIR QUIER REALIZA SEGUIMIENTO. EN EL CAMPO "USUA_EMAIL" DEBE ESTAR EL CORREO INSTITUCIONAL DEL USUARIO.
                    // $usuaCC = array();
                    /*
                     * $sqlCC = " SELECT DISTINCT USUA_EMAIL
                     * FROM USUARIO
                     * WHERE USUA_PERM_CC_ALAR = 1 AND
                     * USUA_EMAIL != ''";
                     * $rsCc = $conn->execute($sqlCC);
                     *
                     * while(!$rsCc->EOF) {
                     * $usuaCC[] = $rsCc->fields['USUA_EMAIL'];
                     * $rsCc->MoveNext();
                     * }
                     */
                    // ##########################################################################

                    // include_once ("./envioEmail.php");
                    // enviarCorreo($radicado, $destinoMail, $usuaCC, $cuerpo, $asunto);
                }

                $result->MoveNext();
            }

            if (strlen($cuerpo) > 10) {
                $cuerpo .= "</tbody> </table>";
                echo $cuerpo . " <br> " . utf8_encode($nombre) . " -- $email <br/>";

                $usuaCC	= array();
                $usuaCC[] = "notificaservicioalciudadano@dnp.gov.co";
                
                $cco	= array();
                $cco[] = 'ajmartinez@dnp.gov.co';
                
                $asunto = "OrfeoDNP los Radicados no reportan respuesta anexa";
                include_once dirname(__FILE__) . "/envioEmail.php";
                $objMail = new correo();
                $result = $objMail->enviarCorreo(array($email), $usuaCC, $cco, $cuerpo, $asunto);
                echo $result;

                $cuerpo = "";
            }

            $rsusua->MoveNext();
        }
    }
}
// ############################################################################
echo "\n" . "Finaliza Alarmas: " . date('Y/m/d_h:i:s') . "\n";
?>