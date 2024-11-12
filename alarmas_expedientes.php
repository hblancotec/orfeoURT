<?php
// ##########################################################################
// # Archivos requeridos para ejecutar este script
set_time_limit(0);
// ##########################################################################

require dirname(__FILE__) . "/config.php";
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsnn);

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
    $sql = "SELECT count(SGD_FESTIVO) from SGD_DIAS_FESTIVOS where SGD_FESTIVO='" . date('Y/m/d') . "'";
    $esFestivo = $conn->GetOne($sql);
    if ($esFestivo) {
        die("No se ejecuta porque es festivo.");
    }
    
    // ##########################################################################
    // ## SE CALCULA LA FECHA DE 2 MESES ATRAS, A PARTIR DE LA FECHA ACTUAL
    $ano_ini = date("Y");
    $mes_ini = substr("00" . (date("m") - 1), - 2);
    $dia_ini = date("d");

    switch ($mes_ini) {
        case '0':
            $mes_ini = "12";
            $ano_ini = date("Y") - 1;
            break;
        case '-1':
            $mes_ini = "11";
            $ano_ini = date("Y") - 1;
            break;
        case '-2':
            $mes_ini = "10";
            $ano_ini = date("Y") - 1;
            break;
    }

    $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";

    // ##########################################################################
    // # CONSULTA DE LOS RADICADOS A PARTIR DEL ANO 2013, QUE NO ESTEN
    // # INCLUIDOS EN EXPEDIENTE Y QUE NO ESTEN ARCHIVADOS
    $isql = "SELECT  R.RADI_NUME_RADI AS RADICADO,
    					" . $conn->SQLDate('Y-m-d H:i:s', 'R.RADI_FECH_RADI') . " AS FECHA,
    					R.RADI_USUA_ACTU AS USUA,
    					R.RADI_DEPE_ACTU AS DEPE,
    					U.USUA_NOMB AS NOMBRE,
    					U.USUA_EMAIL AS EMAIL,
    						(SELECT	TOP 1 H.HIST_FECH 
    						 FROM	HIST_EVENTOS H 
    						 WHERE	H.RADI_NUME_RADI = R.RADI_NUME_RADI AND
    								H.DEPE_CODI_DEST = R.RADI_DEPE_ACTU AND
    								H.USUA_CODI_DEST = R.RADI_USUA_ACTU AND
    								H.SGD_TTR_CODIGO IN (2,9,12)
    						 ORDER BY H.HIST_FECH DESC) as FECHA_TX,
    						(SELECT	TOP 1 H.HIST_FECH 
    						 FROM	HIST_EVENTOS H
    						 WHERE	H.RADI_NUME_RADI = R.RADI_NUME_RADI AND 
    								H.DEPE_CODI_DEST = R.RADI_DEPE_ACTU AND
    								H.USUA_CODI_DEST = R.RADI_USUA_ACTU AND 
    								H.SGD_TTR_CODIGO IN (22,23,42)
    						 ORDER BY H.HIST_FECH DESC) AS FECHA_IM
    			FROM	RADICADO R
    					INNER JOIN USUARIO U ON
    						U.USUA_CODI = R.RADI_USUA_ACTU AND
    						U.DEPE_CODI = R.RADI_DEPE_ACTU
    			WHERE	R.RADI_FECH_RADI >= '$fecha_ini'
    					AND R.RADI_DEPE_ACTU <> 900
    					AND R.RADI_DEPE_ACTU <> 999
    					AND R.RADI_DEPE_ACTU <> 640
    					AND R.RADI_NUME_RADI NOT IN(SELECT	RADI_NUME_RADI
    												FROM	SGD_EXP_EXPEDIENTE
    												WHERE	SGD_EXP_ESTADO = 0)
    			ORDER BY DEPE, USUA";
    $result = $conn->Execute($isql);
    // ##########################################################################
    // ##########################################################################
    // ## PROCESA LOS DATOS DE LOS RADICADOS QUE PUEDEN GENERAR ALERTAS

    // ## Almacena datos procesados para el envio de alarmas
    $depeCodi = 0;
    $usuaCodi = 0;

    while (! $result->EOF) {
        $fechaTx = substr($result->fields['FECHA_TX'], 0, 10);
        $rad = $result->fields['RADICADO'];

        if (substr($rad, - 1) == 1) {
            if (isset($result->fields['FECHA_IM'])) {
                $fechaTx = substr($result->fields['FECHA_IM'], 0, 10);
            }
        }

        // # SE INVOCA LA FUNCION diashabilestramite, PARA DETERMINAR LA CANTIDAD DE DIAS QUE HAN
        // # TRANSCURRIDO DESDE QUE SE GENERO EL RADICADO HASTA EL DIA ACTUAL
        $sqlDiasV = "SELECT dbo.diashabilestramite('$fechaTx', GetDate())";
        $nDias = $conn->getone($sqlDiasV);

        // ## SE CONSULTA EL CORREO DEL JEFE PARA ENVIAR COPIA DE LA ALERTA
        $sql1 = "SELECT	USUA_EMAIL
    				FROM	USUARIO
    				WHERE	DEPE_CODI = " . $result->fields['DEPE'] . "
    						AND USUA_CODI = 1";
        $rs1 = $conn->execute($sql1);

        // ## AL DIA 11 Y 12 DE LA ASIGNACION DEBE ENVIAR ALERTA AL CORREO.
        if ($nDias == 11 || $nDias == 12) {

            // ## CONSULTA SI SE TRATA DE LA MISMA DEPENDENCIA
            if ($depeCodi == $result->fields['DEPE']) {

                // ## CONSULTA SI SE TRATA DEL MISMO USUARIO
                if ($usuaCodi == $result->fields['USUA']) {
                    $radicado .= ", " . $result->fields['RADICADO'];
                } // ## EL USUARIO ES DIFERENTE
                else {

                    // ## LA VARIABLE $radicado VIENE CON REGISTROS
                    if (isset($radicado)) {
                        enviarCorreo($radicado, $usuaMail, $nombre, $jefe);
                    }

                    $radicado = $result->fields['RADICADO'];
                    $depeCodi = $result->fields['DEPE'];
                    $usuaCodi = $result->fields['USUA'];
                    $nombre = $result->fields['NOMBRE'];
                    $jefe = $rs1->fields['USUA_EMAIL'];
                    $usuaMail = $result->fields['EMAIL'];
                }
            } // ## LA DEPENDENCIA ES DIFERENTE
            else {

                // ## LA VARIABLE $radicado VIENE CON REGISTROS
                if (isset($radicado)) {
                    enviarCorreo($radicado, $usuaMail, $nombre, $jefe);
                }

                $radicado = $result->fields['RADICADO'];
                $depeCodi = $result->fields['DEPE'];
                $usuaCodi = $result->fields['USUA'];
                $nombre = $result->fields['NOMBRE'];
                $jefe = $rs1->fields['USUA_EMAIL'];
                $usuaMail = $result->fields['EMAIL'];
            }
        } // FIN -SI HOY ES EL DIA�A 11 O 12 SE GENERA LA ALERTA
        $result->MoveNext();
    }
    if (isset($radicado)) {
        enviarCorreo($radicado, $usuaMail, $nombre, $jefe);

        $radicado = 0;
        $depeCodi = 0;
        $usuaCodi = '';
        $usuaMail = '';
    }
}

// ##########################################################################

// ##########################################################################
// ## FUNCION QUE REALIZA EL ENVIO DE LAS ALERTAS A LOS CORREOS ELECTRONICOS
function enviarCorreo($noRad, $mail, $nombre, $mailJ)
{
    /*
     * Description: funcion que genera el envï¿½o de alertas al correo electrï¿½nico
     * @param , $noRad: variable donde vienen los numeros de radicados
     * @param , $mail: variable donde vienen el correo del usuario
     * @param , $mailJ: variable donde viene el correo del jefe del usuario
     * @return, n/a
     * @Creado Feb de 2013
     * @autor Carlos Eduardo Campos
     */
    require dirname(__FILE__) . "/config.php";

    $result = "-1";
    $asunto = "OrfeoDNP Alerta de radicados sin expediente";
    $cuerpo = "Sr. (a) Usuario (a): " . $nombre . "<br><br> Los siguientes radicados
				se encuentran en su poder en el SGD Orfeo y a&uacute;n no est&aacute;n vinculados
				a ning&uacute;n expediente, por favor ingrese a Orfeo e incluya cada
				uno de estos radicados al respectivo expediente.<br><br><b>" . $noRad . "
				</b> <br><br> Cualquier inquietud por favor comunicarse con la mesa de 
				ayuda de Orfeo Ext: 4043-4054-4070-4071-4074-4077.";

    $cco = array();
    $usuaCC	= array();
    // ## Cuenta de correo a enviar como copia oculta
    $cco[] = "ajmartinez@dnp.gov.co";

    // ## La mesa de ayuda solicita que no se envien copia a los jefes aun.
    $mailJ = '';

    // ## No se envia copia a ningún usuario
    $usuaCC[] = '';

    echo "<br><br> Sr. usuario " . $nombre . " los siguientes radicados estan sin expediente: <br>" . $noRad . "";

    include_once dirname(__FILE__) . "/envioEmail.php";
    $objMail = new correo();
    $result = $objMail->enviarCorreo(array($mail), $usuaCC, $cco, $cuerpo, $asunto);

    // $result = $objMail->enviarCorreo(array($mail), $usuaCC, $cco, $asunto, $cuerpo);

    echo "<br>" . $result;

    $mail = null;
    $objMail = null;
    $mail = null;
    $cco = null;
}
// ##########################################################################
?>