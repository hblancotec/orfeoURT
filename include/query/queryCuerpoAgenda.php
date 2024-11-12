<?php
    $coltp3Esp = '"'.$tip3Nombre[3][2].'"';
    $sqlFechaAgenda = $db->conn->SQLDate("Y-m-d H:i A","agen.SGD_AGEN_FECHPLAZO");
    switch($db->driver) {
        case 'mssqlnative':
        // REMPLAZAR EL EQUIVALENTE DE SYSDATE EN SQL SERVER
        // Si es una entidad diferente al DNP ejecuta consulta estandar
        if ($entidad !="DNP") {
            if($agendado==1) {
                $whereAgendado = "AND agen.SGD_AGEN_FECHPLAZO<=GETDATE() ";
            } else {
                $whereAgendado = "AND agen.SGD_AGEN_FECHPLAZO>=GETDATE() ";
            }
            $isql = 'select convert(char(15), b.RADI_NUME_RADI) as "IDT_Numero Radicado",
                            b.RADI_PATH as "HID_RADI_PATH",
                            '.$sqlFecha.' as "DAT_Fecha Radicado",
                            '.$sqlFechaAgenda.' as "DAT_Fecha Plazo Agenda",
                            convert(char(15), b.RADI_NUME_RADI) as "HID_RADI_NUME_RADI",
                            UPPER(b.RA_ASUN) as "Asunto",
                            c.SGD_TPR_DESCRIP as "Tipo Documento",
                            (select usua_login 
                                from usuario us 
                                where us.USUA_DOC = agen.USUA_DOC) "Agendado Por",
                                convert(char(15),b.RADI_NUME_RADI) "CHK_CHKANULAR",
                            b.RADI_LEIDO "HID_RADI_LEIDO",
                            b.RADI_NUME_HOJA "HID_RADI_NUME_HOJA",
                            b.CARP_PER "HID_CARP_PER",
                            b.CARP_CODI "HID_CARP_CODI",
                            b.SGD_EANU_CODIGO "HID_EANU_CODIGO",
                            b.RADI_NUME_DERI "HID_RADI_NUME_DERI",
                            b.RADI_TIPO_DERI "HID_RADI_TIPO_DERI"
                        FROM SGD_AGEN_AGENDADOS AGEN,
                            radicado b left outer join SGD_TPR_TPDCUMENTO c on 
                            b.tdoc_codi = c.sgd_tpr_codigo
                            left outer join BODEGA_EMPRESAS d on 
                            b.eesp_codi = d.identificador_empresa
                            where b.RADI_NUME_RADI = agen.RADI_NUME_RADI AND 
                            agen.SGD_AGEN_ACTIVO = 1 AND 
                            b.radi_nume_radi is not null AND 
                            b.radi_depe_actu = ' . $dependencia .
                        $whereUsuario.$whereFiltro.$whereAgendado.'
                        order by '.$order .' ' .$orderTipo;
            } else {
                $tmp_s = ($_GET['agendado'] == 1) ? " >= " : " < ";
                include("$ruta_raiz/include/query/busqueda/busquedaPiloto1.php");
                $isql = 'SELECT ' . $radi_nume_radi . 'as "IDT_Numero Radicado",
                        r.RADI_PATH as "HID_RADI_PATH",'.
                        $db->conn->SQLDate("Y-m-d H:i A","r.RADI_FECH_RADI").' as "DAT_Fecha Radicado",'.
                        $radi_nume_radi	.'as "HID_RADI_NUME_RADI",'.
                        $db->conn->SQLDate("Y-m-d H:i A","agen.SGD_AGEN_FECHPLAZO").' as "Fecha Plazo Agenda",
                        UPPER(agen.SGD_AGEN_OBSERVACION) Observacion,
                        UPPER(r.RA_ASUN)  as "Asunto",
                        c.SGD_TPR_DESCRIP as "Tipo Documento",'.
                        $redondeo2 .' as "Dias Calendario Restantes",
                        us.usua_login as "Agendado Por",
                        usActu.usua_login as "Usuario_Actual",'.
                        $radi_nume_radi.' as "CHK_CHKANULAR",
                        r.RADI_LEIDO "HID_RADI_LEIDO",
                        r.RADI_NUME_HOJA "HID_RADI_NUME_HOJA",
                        r.CARP_PER "HID_CARP_PER",
                        r.CARP_CODI "HID_CARP_CODI",
                        r.SGD_EANU_CODIGO "HID_EANU_CODIGO",'.
                        $radi_nume_deri.' as "HID_RADI_NUME_DERI",
                        r.RADI_TIPO_DERI "HID_RADI_TIPO_DERI"
                    FROM SGD_AGEN_AGENDADOS agen INNER JOIN radicado r ON 
                        agen.RADI_NUME_RADI = r.RADI_NUME_RADI INNER JOIN
                        USUARIO us ON
                        agen.USUA_DOC = us.USUA_DOC INNER JOIN 
                        USUARIO usActu ON
                        usActu.usua_codi = r.radi_usua_actu INNER JOIN
                        SGD_USD_USUADEPE USD ON 
                        USD.depe_codi = r.radi_depe_actu AND 
                        agen.USUA_DOC = USD.USUA_DOC AND 
                        USD.SGD_USD_DEFAULT = 1 INNER JOIN 
                        SGD_TPR_TPDCUMENTO c ON r.tdoc_codi = c.sgd_tpr_codigo
                    WHERE USD.USUA_DOC = usActu.USUA_DOC AND
                    agen.SGD_AGEN_ACTIVO = 1 AND 
                    r.radi_nume_radi is not null AND
                    r.radi_depe_actu = ' . $_SESSION['dependencia'] . ' AND
                    r.radi_usua_actu = ' . $_SESSION['codusuario'] .
                    $whereUsuario.$whereFiltro.' AND
                    ' . $db->conn->SQLDate($db->conn->fmtDate,'agen.SGD_AGEN_FECHPLAZO') .
                    $tmp_s . $db->conn->SQLDate($db->conn->fmtDate,$db->conn->sysTimeStamp).'
                ORDER BY '.$order .' ' .$orderTipo;
            }
        break;
        case 'oracle':
        case 'oci8':
        case 'oci805':
        case 'ocipo':
        if($agendado==1) {
            $whereAgendado = " AND agen.SGD_AGEN_FECHPLAZO>=SYSDATE";
        } else {
            $whereAgendado = " AND agen.SGD_AGEN_FECHPLAZO<=SYSDATE";
        }
        $isql = 'select to_char(b.RADI_NUME_RADI) as "IDT_Numero_Radicado",
                        b.RADI_PATH as "HID_RADI_PATH",
                        '.$sqlFecha.' as "DAT_Fecha Radicado",
                        to_char(b.RADI_NUME_RADI) as "HID_RADI_NUME_RADI",
                        '.$sqlFechaAgenda.' as "Fecha Plazo Agenda",
                        UPPER(agen.SGD_AGEN_OBSERVACION) Observacion,
                        UPPER(b.RA_ASUN)  as "Asunto",
                        c.SGD_TPR_DESCRIP as "Tipo Documento",
                        round(agen.SGD_AGEN_FECHPLAZO-sysdate) as "Dias Calendario Restantes",
                        us.USUA_LOGIN "Agendado Por",
                        (select usActu.usua_login
                            from usuario usActu
                            where usActu.usua_codi=b.radi_usua_actu AND
                            usActu.depe_codi=b.radi_depe_actu) "Usuario_Actual"
                        ,to_char(b.RADI_NUME_RADI) "CHK_CHKANULAR"
                        ,b.RADI_LEIDO "HID_RADI_LEIDO"
                        ,b.RADI_NUME_HOJA "HID_RADI_NUME_HOJA"
                        ,b.CARP_PER "HID_CARP_PER"
                        ,b.CARP_CODI "HID_CARP_CODI"
                        ,b.SGD_EANU_CODIGO "HID_EANU_CODIGO"
                        ,b.RADI_NUME_DERI "HID_RADI_NUME_DERI"
                        ,b.RADI_TIPO_DERI "HID_RADI_TIPO_DERI"
                 from radicado b,
                         SGD_TPR_TPDCUMENTO c,
                         SGD_AGEN_AGENDADOS AGEN,
                         USUARIO us
                 where agen.USUA_DOC=us.USUA_DOC AND
                    b.RADI_NUME_RADI=agen.RADI_NUME_RADI AND
                    agen.USUA_DOC='.$usua_doc.' AND
                    agen.SGD_AGEN_ACTIVO = 1 AND
                    b.radi_nume_radi is not null
                    '.$whereUsuario.$whereFiltro.$whereAgendado.
                    ' AND b.tdoc_codi=c.sgd_tpr_codigo (+)
                 order by '.$order .' ' .$orderTipo;
        break;
	}
?>
