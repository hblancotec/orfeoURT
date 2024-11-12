<?php
// Quitamos límite de tiempo en la ejecución del script.
set_time_limit(0);
// Iniciamos script
echo "Inicio " . date('Ymd H:i:s') . "<br/>";
// Validamos que no se ejecute en sabados o domingos
$weekDay = date('w');
if ($weekDay == 0 || $weekDay == 6) {
    die("No se ejecuta porque es fin de semana.");
}

// ############################################################################
// # ARCHIVOS REQUERIDOS PARA EJECUTAR ESTE SCRIPT
$ruta_raiz = ".";
require dirname(__FILE__) . "/config.php";
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
// ############################################################################
try {
    $conn = NewADOConnection($dsn);
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);

    // Validamos si es festivo
    $sql = "SELECT count(SGD_FESTIVO) from SGD_DIAS_FESTIVOS where SGD_FESTIVO='" . date('Y/m/d') . "'";
    $esFestivo = $conn->GetOne($sql);
    if ($esFestivo) {
        die("No se ejecuta porque es festivo.");
    } else {
        // Traemos los usuarios que reciben "Copia de alertas PQR y/o Radicado no reporta respuesta"
        // $sql = "SELECT USUA_EMAIL FROM USUARIO WHERE USUA_PERM_CC_ALAR = 1";
        // $rs = $conn->Execute($sql);
        // foreach($rs as $k => $row) {
        // $destCorreosCC[]= $row['USUA_EMAIL'];
        // }
       
        // Traemos Todos los radicados que fueron radicados hace 2 semanas con sus respuestas
        $sql = "SELECT	R.RADI_NUME_RADI, 
            		--R.RADI_FECH_RADI as FECHARADICACION, 
            		--CONVERT(DATETIME, CONVERT(DATE, R.RADI_FECH_RADI)) as FECHARADICACIONTRUNCADA, 
            		U.USUA_EMAIL as EMAIL, U.USUA_NOMB as NOMBRE,
            		--dbo.diashabiles( GETDATE(), R.RADI_FECH_RADI) as DIASHABILES,
            		--GETDATE() as HOY, CONVERT(DATETIME, CONVERT(varchar(11), GETDATE(), 111 ) + ' 23:59:59', 111) as HOYTRUNCADO, 
            		--dbo.diashabiles( CONVERT(DATETIME, CONVERT(varchar(11), GETDATE(), 111 ) + ' 23:59:59', 111), CONVERT(DATETIME, CONVERT(DATE, R.RADI_FECH_RADI)) ) as DIASHABILESTRUNCADOS,
            		A.RADI_NUME_SALIDA as ANEXRADISALIDA,
            		B.RADI_PATH as ANEXRUTASALIDA,
            		R.RADI_TIPO_DERI as TIPOANEXASOCIADO,
            		R.RADI_NUME_DERI as RADIANEXASOCIADO,
            		C.RADI_PATH as PATHANEXASOCIADO,
            		R.RADI_RESPUESTA as RADIRADIRESPUESTA,
            		D.RADI_PATH as PATHRADIRESPUESTA		
            FROM RADICADO R 
            	INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = R.TDOC_CODI AND T.SGD_TPR_NOTIFICA = 1
            	LEFT JOIN USUARIO U ON R.RADI_USUA_ACTU=U.USUA_CODI AND R.RADI_DEPE_ACTU = U.DEPE_CODI
            	LEFT JOIN ANEXOS A ON A.ANEX_RADI_NUME=R.RADI_NUME_RADI AND A.ANEX_SALIDA=1 
            	LEFT JOIN RADICADO B ON B.RADI_NUME_RADI=A.RADI_NUME_SALIDA 
            	LEFT JOIN RADICADO C ON C.RADI_NUME_RADI=R.RADI_NUME_DERI
            	LEFT JOIN RADICADO D ON D.RADI_NUME_RADI=R.RADI_RESPUESTA
            WHERE R.RADI_FECH_RADI >= DATEADD(week,-2,GETDATE()) AND R.RADI_TIPORAD=2 AND R.RADI_DEPE_ACTU!=999 
                AND (dbo.diashabiles( CONVERT(DATETIME, CONVERT(varchar(11), GETDATE(), 111 ) + ' 23:59:59', 111), CONVERT(DATETIME, CONVERT(DATE, R.RADI_FECH_RADI)) ) >=1 )
                AND (dbo.diashabiles( CONVERT(DATETIME, CONVERT(varchar(11), GETDATE(), 111 ) + ' 23:59:59', 111), CONVERT(DATETIME, CONVERT(DATE, R.RADI_FECH_RADI)) ) <=5 )
            ORDER BY R.RADI_NUME_RADI";
        $rs = $conn->Execute($sql);
        while ($rs && !$rs->EOF) {

            $destCorreosPARA = array();
            $radiValidado = $rs->fields['RADI_NUME_RADI'];
            $nombreUsr = $rs->fields['NOMBRE'];
            $correoUsr = $rs->fields['EMAIL'];
            $mereceEnviarCorreo = mereceEnviarCorreo($rs->fields); //|| $mereceEnviarCorreo

            echo $radiValidado . " - " . $nombreUsr . " - " . $correoUsr . " - " . $mereceEnviarCorreo . "\n";
            if ($mereceEnviarCorreo) {

                echo $correoUsr . "\n";
                $destCorreosPARA[] = $correoUsr;

                // El radicado cumple los requisitos para enviar el correo. Buscamos los destinatarios derivados del radicado
                /*$sql = "select USUA_EMAIL FROM USUARIO U inner join SGD_RG_MULTIPLE M on M.usuario=U.USUA_CODI AND M.area=U.DEPE_CODI AND estatus='ACTIVO' AND RADI_NUME_RADI=" . $radiValidado;
                $rsPara = $conn->Execute($sql);
                while ($rsPara && !$rsPara->EOF) {
                    if ($rsPara->fields['USUA_EMAIL'] != "") {
                        $destCorreosPARA[] = $rsPara->fields['USUA_EMAIL'];
                        $rsPara->MoveNext();
                    }
                }*/

                $asunto = "SGD Orfeo. Gestión en radicado " . $radiValidado;
                $cuerpo = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
                        <tr><td colspan='2' style='font-family: verdana; font-size: 75%'><br/><br/>
                        Estimado(a):<br/>" . $nombreUsr . "<br/><br/>Recuerde que si la PQRSD " . $radiValidado . " no es competencia del <b>DNP</b>, usted deber&aacute; realizar 'TRASLADO' dentro de los 5 d&iacute;as h&aacute;biles siguientes a la recepci&oacute;n del documento, o realizar el tr&aacute;mite pertinente.
                        </td><tr>
                        <tr><td colspan='2'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
                        </table>";
                echo $asunto . " - " . $cuerpo . "\n";

                /*
                 * require_once dirname(__FILE__) . "/class_control/correoElectronico.php";
                 * $objMail = new correoElectronico(".");
                 * echo "Radicado " . $radiValidado . " envi&oacute; correo a " . implode(' ', $destCorreosPARA) . "<br/>";
                 * $cco = 'ajmartinez@dnp.gov.co';
                 * $radiEnvioCorreo = $objMail->enviarCorreo($destCorreosPARA, $destCorreosCC, array(
                 * $cco
                 * ), $cuerpo, $asunto);
                 */
                // $radiEnvioCorreo = enviarCorreo($rs->fields['RADI_NUME_RADI'], $destCorreosPARA, $destCorreosCC, $cuerpo, $asunto);
                $usuaCC = array();
                // $usuaCC[] = "notificaservicioalciudadano@dnp.gov.co";
                $cco = array();
                $cco[] = 'ajmartinez@dnp.gov.co';
                
                try {
                    if ($destCorreosPARA[0] != "") {
                        include_once dirname(__FILE__) . "/envioEmail.php";
                        $objMail = new correo();
                        echo $destCorreosPARA[0] ." - ". $usuaCC[0] ." - ". $cco[0] . "<br/>";
                        $result = $objMail->enviarCorreo($destCorreosPARA, $usuaCC, $cco, $cuerpo, $asunto);
                        echo $result[0] . " - " . $result[1] . "\n";
                        $objMail = null;
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                
                $mereceEnviarCorreo = false; // reiniciamos variable para el proximo (grupo del mismo) radicado.
                
                unset($destCorreosPARA);
            }

            $rs->MoveNext();
        }
    }
    $conn->Close();
} catch (Exception $e) {
    echo $e->getMessage();
}
echo "Fin " . date('Ymd H:i:s') . "<br/>";

function mereceEnviarCorreo($row)
{
    // Vector de extensiones válidas para un radicado.
    $arrayExtValidas = array(
        "pdf",
        "msg",
        "eml"
    );

    // Validación de respuesta "si no posee una respuesta asociada (vinculado por la pestaña de documentos con un radicado de salida" y con extension pdf/msg/eml
    if ((substr($row['ANEXRADISALIDA'], - 1) == 1) and (in_array(strtolower(substr($row['ANEXRUTASALIDA'], - 3)), $arrayExtValidas))) {
        $validoAnexoRepuesta = true;
    } else {
        $validoAnexoRepuesta = false;
    }

    // Validación de Anexo/Asociado por Pestaña Información General
    if ((substr($row['RADIANEXASOCIADO'], - 1) == 1) and (in_array(strtolower(substr($row['PATHANEXASOCIADO'], - 3)), $arrayExtValidas))) {
        $validoAnexSociadoRepuesta = true;
    } else {
        $validoAnexSociadoRepuesta = false;
    }

    // Validación de RESPUESTA por Pestaña Información General
    if ((substr($row['RADIRADIRESPUESTA'], - 1) == 1) and (in_array(strtolower(substr($row['PATHRADIRESPUESTA'], - 3)), $arrayExtValidas))) {
        $validoRespuestaRepuesta = true;
    } else {
        $validoRespuestaRepuesta = false;
    }

    if (! $validoAnexoRepuesta and ! $validoAnexSociadoRepuesta and ! $validoRespuestaRepuesta) {
        $radiEnvioCorreo = true;
    } else {
        $radiEnvioCorreo = false;
    }
    return $radiEnvioCorreo;
}

?>