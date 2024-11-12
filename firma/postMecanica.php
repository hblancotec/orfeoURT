<?php

function insertHistoric($db, $radicado, $comments, $tx)
{
    $insertStateSql = "INSERT INTO HIST_EVENTOS (DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI, HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO, HIST_DOC_DEST, DEPE_CODI_DEST) 
            VALUES (" . $_SESSION['dependencia'] . ", " . $db->conn->sysTimeStamp . " , " . $_SESSION['codusuario'] . ", " .$radicado.  ", '" .$comments. "' , " . $_SESSION['codusuario'] . ", '" . $_SESSION['usua_doc'] . "' , " . $tx . " , '" .$_SESSION['usua_doc']. "' , " . $_SESSION['dependencia'] . ")";
    $db->conn->Execute($insertStateSql );
}

if ($idc > 0) {
    
    $resultado = "";
    
    $idcf = $idc;
    
    $pathSuffix = BODEGAPATH;
    $pathSuffix = str_replace ("/" , "\\", $pathSuffix);

    // 0=Solicitado 1=Firmado 2=Modificacion 3=Rechazado 4=FinalizadoPorRechazo
    $updateStateSql = "UPDATE d set d.estado = 1 FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . " and d.usua_doc = '"  . $_SESSION['usua_doc'] . "' and d.usua_login = '" . $_SESSION['login'] . "'";
    $ok1 = $db->conn->Execute($updateStateSql);
    
    // Get to total number of signers
    $totalSignersSQL = "SELECT COUNT(1) FROM SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . "";
    $totalSigners = $db->conn->GetOne($totalSignersSQL);
    
    // Get the current number of signers successfully applied
    $currentSignersSQL =  "select COUNT(1) from SGD_CICLOFIRMADOMASTER m INNER JOIN SGD_CICLOFIRMADODETALLE d ON m.idcf = d.idcf where m.idcf = " . $idcf . " and d.estado = 1";
    $currentSigners = $db->conn->GetOne($currentSignersSQL);
    
    // Insertamos en el hist�rico
    $sqlRadHist = "select radi_nume_radi, rutapdf from SGD_CICLOFIRMADOMASTER where idcf = " . $idcf . "";
    $rsdt = $db->conn->query($sqlRadHist);
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
        $rs = $db->conn->Execute($sql);
        if ($rs && !$rs->EOF)
        {
            $tmpRadi = $rs->fields['radi_nume_radi'];
            $tmpdepe = $rs->fields['radi_depe_radi'];
            $nombrear = $tmpRadi."_".rand(1, 99999).".pdf";
            $TmpFile = substr($tmpRadi,0,4).'\\'.$tmpdepe.'\\'.$nombrear;
            $TmpFileAnex = substr($tmpRadi,0,4).'/'.$tmpdepe.'/docs/'.$nombrear;

            // 1=Iniciado 2=Solicitud Cambio 3=Finalizado Bien 4=Finalizado Cancelado
            $updateStateMasterSql = "UPDATE SGD_CICLOFIRMADOMASTER set estado = 3 where idcf = " . $idcf . "";
            $rsC = $db->conn->Execute($updateStateMasterSql);
                       
            // Copie el pdf firmado al radicado respectivo.
            if (rename(BODEGAPATH.$file, BODEGAPATH.$TmpFile))
            {
                //if ($anexNum > 1) {
                $sql1 = "update radicado set radi_path = '$TmpFile' where radi_nume_radi = $tmpRadi";
                $rsRad = $db->conn->Execute($sql1);            
                
                // Insertamos en el hist�rico evento de cambio de imagen
                insertHistoric($db, $tmpRadi, "Circuito de firma finalizado satisfactoriamente.", 23);
                
                $resultado = "1";
                
                //copy(BODEGAPATH.$TmpFile, BODEGAPATH.$TmpFileAnex);
                
            }
           
        }
    } else {
        $resultado = "1";
    }
}
?>
