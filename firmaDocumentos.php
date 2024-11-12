<?php
set_time_limit(0);
echo "\n" . "Inicia Alarmas: " . date('Y/m/d_h:i:s') . "\n";

require dirname(__FILE__) . "/config.php";
require dirname(__FILE__) . '\\lib\\adodb\\adodb.inc.php';
$dsnn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsnn);


function finlizaProceso($conn, $idcf) {
    $pathSuffix = BODEGAPATH;
    $pathSuffix = str_replace ("/" , "\\", $pathSuffix);
    // 0=Solicitado 1=Firmado 2=Modificacion 3=Rechazado 4=FinalizadoPorRechazo
    $updateStateSql = "UPDATE d set d.estado = 1 FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . "";
    $ok1 = $conn->Execute($updateStateSql);
    // Get to total number of signers
    $totalSignersSQL = "SELECT COUNT(1) FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . "";
    $totalSigners = $conn->GetOne($totalSignersSQL);
    // Get the current number of signers successfully applied
    $currentSignersSQL =  "select COUNT(1) from SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . " and d.estado = 1";
    $currentSigners = $conn->GetOne($currentSignersSQL);
    // Insertamos en el hist�rico
    $sqlRadHist = "select radi_nume_radi, rutapdf from SGD_CICLOFIRMADOMASTER where idcf = " . $idcf . "";
    $rsdt = $conn->query($sqlRadHist);
    if ($rsdt && !$rsdt->EOF) {
        $radicadoActual = $rsdt->fields['radi_nume_radi'];
        $file = $rsdt->fields['rutapdf'];
    }
    
    $file = str_replace($pathSuffix, "", $file);
    if ($totalSigners === $currentSigners)
    {
        // Actualizar el pdf al radicado.
        //$sql = "select r.radi_depe_radi, r.radi_nume_radi from radicado r where r.radi_nume_radi = (select m.radi_nume_radi from SGD_CICLOFIRMADOMASTER m where m.idcf = " . $idcf . " and m.estado = 1)";
        $sql = "select r.radi_depe_radi, m.radi_nume_radi
                from SGD_CICLOFIRMADOMASTER m INNER JOIN radicado r ON m.radi_nume_radi = r.radi_nume_radi
                where m.idcf = " . $idcf . " and m.estado = 1 ";
        $rs = $conn->Execute($sql);
        if ($rs && !$rs->EOF)
        {
            $tmpRadi = $rs->fields['radi_nume_radi'];
            $tmpdepe = $rs->fields['radi_depe_radi'];
            $nombrear = $tmpRadi."_".rand(1, 99999).".pdf";
            $TmpFile = substr($tmpRadi,0,4).'\\'.$tmpdepe.'\\'.$nombrear;
            $TmpFileAnex = substr($tmpRadi,0,4).'/'.$tmpdepe.'/docs/'.$nombrear;
            // 1=Iniciado 2=Solicitud Cambio 3=Finalizado Bien 4=Finalizado Cancelado
            $updateStateMasterSql = "UPDATE SGD_CICLOFIRMADOMASTER set estado = 3 where idcf = " . $idcf . "";
            $rsC = $conn->Execute($updateStateMasterSql);
            // Copie el pdf firmado al radicado respectivo.
            if (rename(BODEGAPATH.$file, BODEGAPATH.$TmpFile))
            {
                //if ($anexNum > 1) {
                $sql1 = "update radicado set radi_path = '$TmpFile' where radi_nume_radi = $tmpRadi";
                $rsRad = $conn->Execute($sql1);
                // Insertamos en el hist�rico evento de cambio de imagen
                insertHistoric($conn, $tmpRadi, "Circuito de firma finalizado satisfactoriamente.", 23);
                $resultado = "1";
                //copy(BODEGAPATH.$TmpFile, BODEGAPATH.$TmpFileAnex);
            }
        }
    } else {
        $resultado = "1";
    }
}

function insertHistoric($conn, $radicado, $comments, $tx)
{
    $insertStateSql = "INSERT INTO HIST_EVENTOS (DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI, HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO, HIST_DOC_DEST, DEPE_CODI_DEST)
            VALUES (3006, " . $conn->sysTimeStamp . " , 45, " .$radicado.  ", '" .$comments. "' , 45, '321' , " . $tx . " , '321' , 3006)";
    $conn->Execute($insertStateSql );
}

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
    $sqlciclo = "SELECT l.id, l.radi_nume_radi, l.datos_send, l.estado as estado_log, m.idcf, m.estado as estado_master, m.rutapdf
                FROM log_firma l inner join SGD_CICLOFIRMADOMASTER m on l.radi_nume_radi = m.radi_nume_radi
                	inner join SGD_CICLOFIRMADODETALLE d on d.idcf=m.idcf
                WHERE m.estado = 1 and l.estado = 3 and CHARINDEX(m.rutapdf, l.datos_send, 0) > 0 
                ORDER BY l.id ";
    echo "\n" . "Query Finaliza: " . $sqlciclo . " " . date('Y/m/d_h:i:s') . "\n";
    $rsciclo = $conn->Execute($sqlciclo);
    if ($rsciclo) {
        while (!$rsciclo->EOF) {
            
            $info = pathinfo($rsciclo->fields['rutapdf']);
            $filePdf = $info['filename'] . ".pdf";
            
			echo "\n" . $serverCompartida . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf . " " . date('Y/m/d_h:i:s') . "\n";
            if (file_exists($serverCompartida . $info['dirname'] . DIRECTORY_SEPARATOR . $filePdf)) {
                
                $sql = "update sgd_ciclofirmadomaster set rutapdf = 'dav/" . date('Y') . "/" . $filePdf . "' where idcf = ".$rsciclo->fields['idcf']."";
				echo "\n" . "Query Update: " . $sql . " " . date('Y/m/d_h:i:s') . "\n";
                $rs1 = $conn->Execute($sql);
                if ($rs1 !== false) {
                    $rs1->Close();
                }
                finlizaProceso($conn, $rsciclo->fields['idcf']);
            }
            
            $rsciclo->MoveNext();
        }
    }
    
    /*$sql_valida = "SELECT count(*) as CONTADOR
                 FROM log_firma
                 WHERE estado = 2; ";
    $rsval = $conn->Execute($sql_valida);
    if ($rsval && ! $rsval->EOF) {
        if (intval($rsval->fields['CONTADOR']) == 0)
        {
            $SQL_QUERY = " SELECT TOP 2 ID, radi_nume_radi, datos_send, date_send, response, date_recibe, estado
                         FROM log_firma
                         WHERE estado = 1
                         ORDER BY id; ";
            echo "\n" . "Query Firma: " . $SQL_QUERY . " " . date('Y/m/d_h:i:s') . "\n";
            $rsfirma = $conn->Execute($SQL_QUERY);
            if ($rsfirma && ! $rsfirma->EOF) {
                while ($rsfirma && !$rsfirma->EOF) {
                    
                    $id = $rsfirma->fields['ID'];
                    
                    $cmd = 'E:\\OI_OrfeoPHP7_64\\orfeo\\firma\\combina.exe '.$id.' >> E:\logs\Firma_Radicado_'.$id.'.log';
                    $handle = popen("start /B ". $cmd, "r");
                    $read = fread($handle, 4096);
                    pclose($handle);
                    
                    $rsfirma->MoveNext();
                }
            }
        }
    }*/
} else {
    echo "\n" . "No hay conexión a la base de datos: " . date('Y/m/d_h:i:s') . "\n";
}