<?php
    // SEGUNDA PARTE DE LA CONSULTA. WHERE PARA LOS CRITERIOS DE BUSQUEDA DEFINIDOS POR EL USUARIO prestamo.php
    // Adecuacion de las fechas
    //$sqlWHERE_PRES_FECH_PEDI=$db->conn->SQLDate("dmY","P.PRES_FECH_PEDI"); 
    $sqlWHERE_PRES_FECH_PEDI = date("d-m-Y","P.PRES_FECH_PEDI");
    $sqlWHERE_PRES_FECH_CANC=$db->conn->SQLDate("dmY","P.PRES_FECH_CANC"); 
   	//$sqlWHERE_PRES_FECH_CANC = date("d-m-Y","P.PRES_FECH_CANC");
    $sqlWHERE_PRES_FECH_DEVO=$db->conn->SQLDate("dmY","P.PRES_FECH_DEVO"); 
    //$sqlWHERE_PRES_FECH_DEVO = date("d-m-Y","P.PRES_FECH_DEVO");
    $sqlWHERE_PRES_FECH_PRES=$db->conn->SQLDate("dmY","P.PRES_FECH_PRES"); 
    //$sqlWHERE_PRES_FECH_PRES = date("d-m-Y","P.PRES_FECH_PRES");
    $sqlWHERE_PRES_FECH_VENC=$db->conn->SQLDate("dmY","P.PRES_FECH_VENC"); 
    //$sqlWHERE_PRES_FECH_VENC = date("d-m-Y","P.PRES_FECH_VENC");
    $sqlWHERE_PRES_FECH_PEDI_HOR=$db->conn->SQLDate("dmYhiA","P.PRES_FECH_PEDI");
    //$sqlWHERE_PRES_FECH_PEDI_HOR = date("d-m-Y h:i:A","P.PRES_FECH_PEDI");

    if ($ps_RADI_NUME_RADI != "") {
        $sWhere .= " and P.RADI_NUME_RADI LIKE '%" . $ps_RADI_NUME_RADI . "'";
    }
    if ($ps_USUA_LOGIN != "") {
        $sWhere .= " and P.USUA_LOGIN_ACTU LIKE '" . strtoupper($ps_USUA_LOGIN) . "'";
    }
    if ($ps_DEPE_NOMB != "") {
        $sWhere .= " and P.DEPE_CODI=" . $ps_DEPE_NOMB;
    }
    if ($ps_PRES_REQUERIMIENTO != "") {
        $sWhere .= " and P.PRES_REQUERIMIENTO=" . $ps_PRES_REQUERIMIENTO;
    }
    if ($ps_USUA_NOMB != "") {
        if (strcasecmp(strtoupper($ps_USUA_NOMB), strtoupper($ps_USUA_LOGIN)) != 0) {
            $sWhere .= " and P.USUA_LOGIN_ACTU LIKE '" . strtoupper($ps_USUA_NOMB) . "'";
        }
    }

            // condicion para limitar por fechas y estado la b�squeda seg�n la opci�n del men� seleccionada
//reporte y estado de reservas

    if ($opcionMenu == 0 || $opcionMenu == 4) {
        if ($ps_PRES_ESTADO == "") {
            if ($fechaInicial != "" || $fechaFinal != "") {
                $sWhere .= " and " . $sqlWHERE_PRES_FECH_PEDI;
            } // Fecha de creaci�n del registro
        } else {
            if ($ps_PRES_ESTADO != - 1) { // diferentes a vencido
                $sWhere .= " and P.PRES_ESTADO=" . $ps_PRES_ESTADO . " ";
                if ($fechaInicial != "" || $fechaFinal != "") {
                    if ($ps_PRES_ESTADO == 2 || $ps_PRES_ESTADO == 5) {
                        $sWhere .= " and " . $sqlWHERE_PRES_FECH_PRES;
                    } elseif ($ps_PRES_ESTADO == 1) {
                        $sWhere .= " and " . $sqlWHERE_PRES_FECH_PEDI;
                    } elseif ($ps_PRES_ESTADO == 3) {
                        $sWhere .= " and " . $sqlWHERE_PRES_FECH_DEVO;
                    } elseif ($ps_PRES_ESTADO == 4) {
                        $sWhere .= " and " . $sqlWHERE_PRES_FECH_CANC;
                    } elseif ($ps_PRES_ESTADO == 7) {
                        $sWhere .= " and " . $sqlWHERE_PRES_FECH_PRES;
                    }
                }
            } else { // estado vencido
                $sWhere .= " and P.PRES_ESTADO=2 and " . $sqlWHERE_PRES_FECH_VENC;
                if ($fechaInicial == "" && $fechaFinal == "") {
                    $sWhere .= "<'" . date('d-m-Y') . "' ";
                }
            }
        }
    } elseif ($opcionMenu == 1 || $opcionMenu == 3) { // prestamo y cancelaci�n
        $sWhere .= " and P.PRES_ESTADO in (1,5) ";
        if ($opcionMenu == 3) {
            $sWhere .= " and " . $sqlWHERE_PRES_FECH_PEDI_HOR;
        } // fecha hasta la cual se cancelan
    } elseif ($opcionMenu == 2) { // devolucion
        $sWhere .= " and P.PRES_ESTADO in (2, 6) ";
    } elseif ($opcionMenu == 5) { // devolucion
        $sWhere .= " and P.PRES_ESTADO in (2,6,7) ";
    }

            // termina la sentencia con el respectivo valor de la fecha	 
              
    if (($fechaInicial != "" || $fechaFinal != "") && $opcionMenu != 1 && $opcionMenu != 2 && $opcionMenu != 5) {
        //$sqlFechaInicial = str_replace('-', '', $fechaInicial);
        $sqlFechaInicial = $fechaInicial . " 00:00:00";
        //$sqlFechaFinal = str_replace('-', '', $fechaFinal);
        $sqlFechaFinal = $fechaFinal . " 11:59:59";
        if ($opcionMenu == 3) {
            //$sqlFechaFinal .= $ps_hora_limite . $ps_minuto_limite . $ps_meridiano;
        }
        if ($fechaInicial != "" && $fechaFinal == "") {
            $sWhere .= ">='" . $sqlFechaInicial . "' ";
        } elseif ($fechaInicial == "" && $fechaFinal != "") {
            $sWhere .= "<='" . $sqlFechaFinal . "' ";
        } elseif ($fechaInicial != "" && $fechaFinal != "") {
            // Verifica que la fecha inicial sea menor que la final
            if ($sqlFechaFinal < $sqlFechaInicial) {
                //$a = $sqlFechaInicial;
                //$sqlFechaInicial = $sqlFechaFinal;
                //$sqlFechaFinal = $a;
            }
            $sWhere .= " between '" . $sqlFechaInicial . "' and '" . $sqlFechaFinal . "' ";
        }
    }
    
    // limita los resultados a la territorial que custodia los documentos
    if ($opcionMenu == 2 || $opcionMenu == 3) { // pr�stamo, devoluci�n o cancelaci�n   //$opcionMenu == 1 ||
        /*$sWhere .= " and DA.DEPE_CODI_TERRITORIAL=
         ( select UD.DEPE_CODI_TERRITORIAL
         from   USUARIO UP,
         SGD_USD_USUADEPE USD,
         DEPENDENCIA UD
         where  UP.USUA_LOGIN LIKE '" . strtoupper($krd) . "' AND
         UP.USUA_LOGIN = USD.USUA_LOGIN AND
         UP.USUA_DOC = USD.USUA_DOC AND
         USD.SGD_USD_SESSACT = 1 AND
         USD.DEPE_CODI=UD.DEPE_CODI ) ";*/
    }
?>