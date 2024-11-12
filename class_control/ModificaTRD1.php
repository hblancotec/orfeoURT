<?php
session_start();
$ruta_raiz = "..";

include_once("$ruta_raiz/config.php");
//include $ruta_raiz.'/tx/diasHabiles.php';
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;
//$funcdias = new FechaHabil($db);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$ADODB_COUNTRECS = true;

include_once "$ruta_raiz/include/tx/Historico.php";
$Historico = new Historico($db);

if ($_POST['tipo'] == 1) {    //SOLITUD DE CAMBIO DE TRD
    if ($_POST['rad'] != null) {
        
        $notifica = $_POST['notifica'];
        $seractu = $_POST['seractu'];
        $subseractu = $_POST['subseractu'];
        $tdocactu = $_POST['tdocactu'];
        $serie = $_POST['serie'];
        $tsub = $_POST['tsub'];
        $tdoc = $_POST['tdoc'];
        $depe = $_POST['depe'];
        $depesel = $_POST['depesel'];
        $usuaCC = array();
        $usuaCCO = array();
        
        $isqlSer = "SELECT SGD_SRD_DESCRIP FROM SGD_SRD_SERIESRD WHERE SGD_SRD_CODIGO = $serie ";
        $isqlSSer = "SELECT SGD_SBRD_DESCRIP FROM SGD_SBRD_SUBSERIERD WHERE SGD_SBRD_CODIGO = $tsub AND SGD_SRD_CODIGO = $serie ";
        $isqlTdoc = "SELECT SGD_TPR_DESCRIP FROM SGD_TPR_TPDCUMENTO WHERE SGD_TPR_CODIGO = $tdoc ";
        $rsSer = $db->conn->Execute($isqlSer);
        if ($rsSer && !$rsSer->EOF) {
            $nomser = $rsSer->fields['SGD_SRD_DESCRIP'];
        }
        $rsSSer = $db->conn->Execute($isqlSSer);
        if ($rsSSer && !$rsSSer->EOF) {
            $nomsubser = $rsSSer->fields['SGD_SBRD_DESCRIP'];
        }
        $rsTdoc = $db->conn->Execute($isqlTdoc);
        if ($rsTdoc && !$rsTdoc->EOF) {
            $nomtipo = $rsTdoc->fields['SGD_TPR_DESCRIP'];
        }
        
        $isqlSera = "SELECT SGD_SRD_DESCRIP FROM SGD_SRD_SERIESRD WHERE SGD_SRD_CODIGO = $seractu ";
        $isqlSSera = "SELECT SGD_SBRD_DESCRIP FROM SGD_SBRD_SUBSERIERD WHERE SGD_SBRD_CODIGO = $subseractu AND SGD_SRD_CODIGO = $seractu ";
        $isqlTdoca = "SELECT SGD_TPR_DESCRIP FROM SGD_TPR_TPDCUMENTO WHERE SGD_TPR_CODIGO = $tdocactu ";
        $rsSera = $db->conn->Execute($isqlSera);
        if ($rsSera && !$rsSera->EOF) {
            $nomsera = $rsSera->fields['SGD_SRD_DESCRIP'];
        }
        $rsSSera = $db->conn->Execute($isqlSSera);
        if ($rsSSera && !$rsSSera->EOF) {
            $nomsubsera = $rsSSera->fields['SGD_SBRD_DESCRIP'];
        }
        $rsTdoca = $db->conn->Execute($isqlTdoca);
        if ($rsTdoca && !$rsTdoca->EOF) {
            $nomtipoa = $rsTdoca->fields['SGD_TPR_DESCRIP'];
        }
                
        if ($notifica == 1) {
            $sqlus = "select USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TRD = 1 ";
            $mensaje = "Notificamos la validaci&oacute;n de cambio de TRD del radicado: ".$_POST['rad']."<br/>";
        } else {
            $sqlus = "select USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TIPODOC = 1 ";
            $mensaje = "Notificamos la validaci&oacute;n de cambio de Tipo Documental del radicado: ".$_POST['rad']."<br/>";
        }
        $rsus = $db->conn->Execute($sqlus);
        if ($rsus && ! $rsus->EOF) {
            while (! $rsus->EOF) {
                
                $usuacod = $rsus->fields["USUA_CODI"];
                $depecod = $rsus->fields["DEPE_CODI"];
                $nombUSU = $rsus->fields["USUA_NOMB"];
                $emailUSU = $rsus->fields["USUA_EMAIL"];
                
                if ($emailUSU && $emailUSU != '') {
                    $asunto = "Env&iacute;o de notificaci&oacute;n radicado ".$_POST['rad'];
                    $cuerpo = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
                                        <tr><td colspan='2' style='font-family: verdana; font-size: 75%'><br/><br/>
                                        Estimado(a):<br/>".$nombUSU."<br/><br/>$mensaje
                                        De: $nomsera/$nomsubsera/$nomtipoa - A: $nomser/$nomsubser/$nomtipo
                                        </td><tr>
                                        <tr><td colspan='2'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
                                        </table>";
                                
                    require_once ("correoElectronico.php");
                    $objMail = new correoElectronico("..");
                    $objMail->FromName = "Notificaciones Orfeo";
                    $result = $objMail->enviarCorreo(array($emailUSU), $usuaCC, $usuaCCO, $asunto, $cuerpo);
                }
                
                $rsus->MoveNext();
            }
            
            if ($notifica == 1) {
                $sqlUpd = "UPDATE RADICADO SET SGD_CAMBIO_TRD = 1 WHERE RADI_NUME_RADI = " . $_POST['rad'];
            } else {
                $sqlUpd = "UPDATE RADICADO SET SGD_CAMBIO_TRD = 3 WHERE RADI_NUME_RADI = " . $_POST['rad'];
            }
            $ok = $db->conn->Execute($sqlUpd);
            
            if ($ok)
            {        
                if ($notifica == 1) {
                    $radHist = $Historico->insertarHistorico( array($_POST['rad']), $_SESSION['dependencia'], $_SESSION["codusuario"], $depecod, $usuacod, "Solicitud cambio de TRD, De: $nomsera/$nomsubsera/$nomtipoa - A: $nomser/$nomsubser/$nomtipo", 103 );
                } else {
                    $radHist = $Historico->insertarHistorico( array($_POST['rad']), $_SESSION['dependencia'], $_SESSION["codusuario"], $depecod, $usuacod, "Solicitud cambio de Tipo Documental, De: $nomsera/$nomsubsera/$nomtipoa - A: $nomser/$nomsubser/$nomtipo", 108 );
                }
            }
        }
    }
    echo $result;
}
elseif ($_POST['tipo'] == 2) {    // APROBACION SI O NO DE CAMBIO DE TRD
    if ($_POST['rad'] != null) {
        $aprueba = $_POST['aprueba'];
        $notifica = $_POST['notifica'];
        $usuaCC = array();
        $usuaCCO = array();
        
        if ($notifica == 1) {
            $cambio = 0;
            $transac = 106;
            $comen = "No habilitaron el cambio de la TRD";
            if ($aprueba == 1) {
                $cambio = 2;
                $transac = 104;
                $comen = "Habilitaron el cambio de la TRD";
            }
        } else {
            $cambio = 0;
            $transac = 110;
            $comen = "No habilitaron el cambio del Tipo Documental";
            if ($aprueba == 1) {
                $cambio = 4;
                $transac = 109;
                $comen = "Habilitaron el cambio del Tipo Documental";
            }
        }

        $sqlUpd = "UPDATE RADICADO SET SGD_CAMBIO_TRD = $cambio WHERE RADI_NUME_RADI = " . $_POST['rad'];
        $ok = $db->conn->Execute($sqlUpd);
        if ($ok) {
            
            $sqlus = "SELECT R.RADI_NUME_RADI, U.USUA_NOMB, U.USUA_EMAIL, U.USUA_CODI, U.DEPE_CODI
                    FROM RADICADO R INNER JOIN USUARIO U ON U.USUA_CODI = R.RADI_USUA_ACTU AND U.DEPE_CODI = R.RADI_DEPE_ACTU
                    WHERE R.RADI_NUME_RADI = ".$_POST['rad']." " ;
            $rsus = $db->conn->Execute($sqlus);
            if ($rsus && ! $rsus->EOF) {
                while (! $rsus->EOF) {
                    
                    $usuacod = $rsus->fields["USUA_CODI"];
                    $depecod = $rsus->fields["DEPE_CODI"];
                    $nombUSU = $rsus->fields["USUA_NOMB"];
                    $emailUSU = $rsus->fields["USUA_EMAIL"];
                    
                    if ($emailUSU && $emailUSU != '') {
                    
                        $asunto = "Env&iacute;o de notificaci&oacute;n radicado ".$_POST['rad'];
                        $cuerpo = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
                                        <tr><td colspan='2' style='font-family: verdana; font-size: 75%'><br/><br/>";
                        if ($cambio == 2) {
                            $cuerpo .= "Estimado(a):<br/>".$nombUSU."<br/><br/>Notificamos la habilitaci&oacute;n del cambio de TRD del radicado: ".$_POST['rad']."";
                        } else if ($cambio == 0) {
                            $cuerpo .= "Estimado(a):<br/>".$nombUSU."<br/><br/>Notificamos que no habilitaron el cambio de TRD del radicado: ".$_POST['rad']."";
                        } else if ($cambio == 4) {
                            $cuerpo .= "Estimado(a):<br/>".$nombUSU."<br/><br/>Notificamos la habilitaci&oacute;n del cambio del Tipo Documental del radicado: ".$_POST['rad']."";
                        }
                        $cuerpo .= "</td><tr>
                                        <tr><td colspan='2'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
                                        </table>";
                        
                        require_once ("correoElectronico.php");
                        $objMail = new correoElectronico("..");
                        $objMail->FromName = "Notificaciones Orfeo";
                        $result = $objMail->enviarCorreo(array($emailUSU), $usuaCC, $usuaCCO, $asunto, $cuerpo);
                    }
                    
                    $rsus->MoveNext();
                }
            }
            
            $radHist = $Historico->insertarHistorico( array($_POST['rad']), $_SESSION['dependencia'], $_SESSION["codusuario"], $depecod, $usuacod, $comen, $transac );
        }
            
        echo $cambio;
    }
}
elseif ($_POST['tipo'] == 3) {    //  SELECCIONAR SUBSERIES POR SERIE
    if ($_POST['serie'] != null) {
        $subseactu = $_POST['subseactu'];
        $fecha_hoy = Date("Y-m-d");
        $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
        $querySub = "SELECT	distinct (".$db->conn->Concat("convert(char(4),su.sgd_sbrd_codigo,0)","'-'","su.sgd_sbrd_descrip").") as detalle, su.sgd_sbrd_codigo
                   FROM	sgd_mrd_matrird m, sgd_sbrd_subserierd su
                   WHERE	m.depe_codi = ".$_SESSION['dependencia']." and
                            m.sgd_srd_codigo = '".$_POST['serie']."' and
                            su.sgd_srd_codigo = '".$_POST['serie']."' and
                            su.sgd_sbrd_codigo = m.sgd_sbrd_codigo and
                            m.sgd_mrd_esta = '1' and
                            $sqlFechaHoy between su.sgd_sbrd_fechini and
                            su.sgd_sbrd_fechfin
                   ORDER BY detalle";
        $rsSub = $db->conn->Execute($querySub);
        echo "<option value='0'>-- Seleccione --</option>";
        $selec = "";
        if ($rsSub && !$rsSub->EOF) {
            while (!$rsSub->EOF)
            {
                if ($subseactu == $rsSub->fields['sgd_sbrd_codigo'])
                    $selec = 'selected';
                echo "<option value='".$rsSub->fields['sgd_sbrd_codigo']."' $selec>".$rsSub->fields['detalle']."</option>";
                $selec = "";
                $rsSub->MoveNext();
            }
        }
    }
}
elseif ($_POST['tipo'] == 4) {   ///SELECCIONAR TIPOS DOCUMENTALES POR SUBSERIE
    if ($_POST['serie'] != null) {
        $docuactu = $_POST['docuactu'];
        $ent = substr($_POST['nurad'], - 1);
        $fecha_hoy = Date("Y-m-d");
        $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
        $queryTip = "SELECT	distinct (".$db->conn->Concat("convert(char(4),t.sgd_tpr_codigo,0)","'-'","t.sgd_tpr_descrip").") as detalle, t.sgd_tpr_codigo
                        			FROM	sgd_mrd_matrird m, sgd_tpr_tpdcumento t
                        			WHERE	m.depe_codi = ".$_SESSION['dependencia']."
                        					and m.sgd_mrd_esta = '1'
                        					and m.sgd_srd_codigo = ".$_POST['serie']."
                        					and m.sgd_sbrd_codigo = ".$_POST['subserie']."
                        					and t.sgd_tpr_codigo = m.sgd_tpr_codigo
                        					and t.sgd_tpr_tp$ent='1'
                        			ORDER BY detalle";
        $rsTip = $db->conn->Execute($queryTip);
        echo "<option value='0'>-- Seleccione --</option>";
        $selec = "";
        if ($rsTip && !$rsTip->EOF) {
            while (!$rsTip->EOF)
            {
                if ($docuactu == $rsTip->fields['sgd_tpr_codigo'])
                    $selec = 'selected';
                echo "<option value='".$rsTip->fields['sgd_tpr_codigo']."' $selec>".$rsTip->fields['detalle']."</option>";
                $selec = "";
                $rsTip->MoveNext();
            }
        }
    }
}
elseif ($_POST['tipo'] == 5) {   //modificar TRD
    if ($_POST['rad'] != null) {
        $response = "";
        
        $just = $_POST['just'];
        $nurad = $_POST['rad'];
        $dependencia = $_SESSION['dependencia'];
        $codusuario = $_SESSION["codusuario"];
        $usua_doc = $_SESSION["usua_doc"];
        $tdoc = $_POST['tdoc'];
        $codserie = $_POST['serie'];
        $tsub = $_POST['subser'];
        $cambio = $_POST['cambio'];
        
        $codiRegH = Array();
        /*$sqlUpd = "UPDATE RADICADO SET SGD_CAMBIO_TRD = 0 WHERE RADI_NUME_RADI = " . $_POST['rad'];
        $ok = $db->conn->Execute($sqlUpd);
        
        $sqlH = "	SELECT	r.radi_nume_radi RADI_NUME_RADI, SGD_MRD_CODIGO
				    FROM SGD_RDF_RETDOCF r
				    WHERE RADI_NUME_RADI = $nurad";
        $rsH = $db->conn->Execute($sqlH);
        $codiActu = $rsH->fields['SGD_MRD_CODIGO'];
        $i = 0;  
        while (!$rsH->EOF) {
            $codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
            $i++;
            $rsH->MoveNext();
        }*/
        
        $serieactu = "";
        $subseactu = "";
        $docuactu = "";
        $sqlDt = "SELECT R.SGD_MRD_CODIGO, R.RADI_NUME_RADI, M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO, 
                    S.SGD_SRD_DESCRIP, B.SGD_SBRD_DESCRIP, T.SGD_TPR_DESCRIP
                FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
                	INNER JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
                	INNER JOIN SGD_SBRD_SUBSERIERD B ON B.SGD_SBRD_CODIGO = M.SGD_SBRD_CODIGO AND B.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
                	INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
                WHERE R.RADI_NUME_RADI = $nurad AND R.DEPE_CODI = $dependencia ORDER BY R.SGD_RDF_FECH DESC ";
        $rsDt = $db->conn->Execute($sqlDt);
        if ($rsDt && ! $rsDt->EOF) {
            $serieactu = $rsDt->fields["SGD_SRD_DESCRIP"];
            $subseactu = $rsDt->fields["SGD_SBRD_DESCRIP"];
            $docuactu = $rsDt->fields["SGD_TPR_DESCRIP"];
        }
        
        $codiRegH[] = $nurad;
        $transac = 34;
        if ($cambio == 4)
            $transac = 107;
        
        $observa 	= "Observacion: " . $just . " - TRD Anterior: ".$serieactu."/".$subseactu."/".$docuactu;
        $Historico 	= new Historico($db);
        $radiModi 	= $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, $transac);
        
        //Actualiza el campo tdoc_codi de la tabla Radicados
        include_once "$ruta_raiz/class_control/TipoDocumental.php";
        $trd = new TipoDocumental($db);
        $radiUp = $trd->actualizarTRD($codiRegH, $tdoc);
        
        ##################################################################################
        ### SI LA TRD ANTERIOR ERA UNA PQR, SE ENVIA CORREO ELECTRONICO A LAS PERSONAS
        ### QUE TIENEN ACTIVO EL PERMISO DE COPIAS DE ALERTAS PQR.
        $isqlTRD 	= "	SELECT SGD_MRD_CODIGO FROM	SGD_MRD_MATRIRD
	      			WHERE DEPE_CODI = '$dependencia' AND SGD_SRD_CODIGO = '$codserie'
					   AND SGD_SBRD_CODIGO = '$tsub' AND SGD_TPR_CODIGO = '$tdoc'";
        $rsTRD 		= $db->conn->Execute($isqlTRD);
        $codiTRDU 	= $rsTRD->fields['SGD_MRD_CODIGO'];
        
        $TRD = $codiTRDU;
        include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
        $tipoRad = substr($radiUp[0], -1, 1);
        if ($radiUp[0]){
            /*if ($deta_pqr == 1  && $tipoRad == 2) {
                
                ### SE CONSULTA LA DESCRIPCION DEL TIPO DOCUMENTAL ANTERIOR
                $sqlTd = "	SELECT	SGD_TPR_DESCRIP
						FROM SGD_MRD_MATRIRD M JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
						WHERE M.SGD_MRD_CODIGO = " .$codiActu;
                $tDocAnt = $db->conn->Getone($sqlTd);
                
                ### SE CONSULTA LA DESCRIPCION DEL TIPO DOCUMENTAL NUEVO
                $sqlTipo = "	SELECT	SGD_TPR_DESCRIP
						FROM	SGD_TPR_TPDCUMENTO
						WHERE	SGD_TPR_CODIGO = " . $tdoc;
                $tDocNew = $db->conn->Getone($sqlTipo);
                
                ### CAPTURA DE VARIABLES PARA ENVIAR EL CORREO
                $asunto = "Orfeo-DNP Modificación de TRD al radicado: ".$radiUp[0];
                $cuerpo = "Al siguiente No. de Radicado: <b/> ".$radiUp[0]." </b/> se le acaba de modificar la clasificación TRD,
						de <b/> ".$tDocAnt." </b/> a <b/>".$tDocNew."</b/>, con la observaci&oacute;n: '".$just."', por favor verificar.";
                
                ###	CONSULTA DEL CORREO DE LOS USUARIOS A LOS CUALES SE LES DEBE ENVIAR LAS ALERTAS
                $correos = array();
                $cc = array();
                $sqlCorreos = "	SELECT	DISTINCT USUA_EMAIL
							FROM    USUARIO
							WHERE   USUA_PERM_CC_ALAR = 1 AND USUA_EMAIL != ''";
                $rsCorreos = $db->conn->execute($sqlCorreos);
                while(!$rsCorreos->EOF) {
                    $correos[] = $rsCorreos->fields['USUA_EMAIL'];
                    $rsCorreos->MoveNext();
                }
                
                ###	CUENTA DE CORREO PARA ENVIAR LAS COPIAS OCULTAS
                $cco = 'jzabala@dnp.gov.co'; //'ajmartinez@dnp.gov.co';
                require_once "$ruta_raiz/class_control/correoElectronico.php";
                $objMail = new correoElectronico("..");
                $objMail->FromName = "Notificaciones Orfeo";
                $result = $objMail->enviarCorreo($correos, $cc, array($cco), $asunto, $cuerpo);
                
                //$response = $result;
                unset($correos);
                unset($usuaCC);
                unset($cco);
                
            }*/
        }
        $codiRegH = null;
        
        //logica de prorroga
        include_once ($ruta_raiz . "/radsalida/masiva/CombinaPlantilla.php");
        $objcomb = new CombinaPlantilla();
        
        $isqlTdoc = "SELECT SGD_TPR_CODIGO, SGD_TPR_DESCRIP, SGD_TPR_TERMINO FROM SGD_TPR_TPDCUMENTO WHERE SGD_TPR_CODIGO = $tdoc ";
        $rsTdoc = $db->conn->Execute($isqlTdoc);
        if ($rsTdoc && !$rsTdoc->EOF) {
            $idTipo = $rsTdoc->fields['SGD_TPR_CODIGO'];
            $nomtipo = iconv($objcomb->codificacion($rsTdoc->fields['SGD_TPR_DESCRIP']),'UTF-8',$rsTdoc->fields['SGD_TPR_DESCRIP']);
            $termino = intval($rsTdoc->fields['SGD_TPR_TERMINO']);
        }
        
        if ($termino > 0) {
            //verificar path y plantilla
            $validaPlantilla = false;
            $sqLPath = "SELECT R.RADI_PATH, R.RADI_NUME_DERI AS PADRE, ".$db->conn->SQLDate('Y-m-d H:i:s', 'R.RADI_FECH_RADI')." AS FECHA,
                            FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1
                        FROM RADICADO R WHERE R.RADI_NUME_RADI = " . $nurad;
            $rsPath = $db->conn->Execute($sqLPath);
            if ($rsPath && !$rsPath->EOF) {
                $path = BODEGAPATH . $rsPath->fields["RADI_PATH"];
                $fechaRad = $rsPath->fields["FECHA"];
                $fecha1 = $rsPath->fields['FECHA1'];
                $padre = $rsPath->fields['PADRE'];
                $ext = substr($path, strrpos(trim($path), '.')+1);
                if ($ext == "DOCX" || $ext == "docx" ) {
                    $validaPlantilla = $objcomb->docx2search($path, array('#1#'));
                }
            }
            
            if ($validaPlantilla && $idTipo == 3181) {
                
                if (isset($padre)) {
                    $numradi = $padre;
                    
                    $sqlPadre = "SELECT ".$db->conn->SQLDate('Y-m-d H:i:s', 'R.RADI_FECH_RADI')." AS FECHA,
                                FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1, R.RADI_FECHA_VENCE AS FECHA_VENCE,
                                T.SGD_TPR_TERMINO, S.FECHA_VENCE AS PRORROGA
                            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO T ON R.TDOC_CODI = T.SGD_TPR_CODIGO
		                          LEFT JOIN SGD_PRORROGAS S ON R.RADI_NUME_RADI = S.RADI_NUME_RADI
                            WHERE R.RADI_NUME_RADI = " . $numradi;
                    $rsPadre = $db->conn->Execute($sqlPadre);
                    if ($rsPadre && !$rsPadre->EOF) {
                        
                        $fechaRad = $rsPadre->fields["FECHA"];
                        $fecha1 = $rsPadre->fields['FECHA1'];
                        $fechaVenci = $rsPadre->fields['FECHA_VENCE'];
                        $termino = intval($rsPadre->fields['SGD_TPR_TERMINO']);
                        $prorroga = $rsPadre->fields['PRORROGA'];
                        $d = new DateTime($fechaVenci);
                        $format_date = $d->format('Y-m-d');
                        
                        if (date("Y-m-d") < $format_date && !isset($prorroga)) {
                            
                            $termino1 = $termino * 2;
                            $termino2 = $termino1 - 1;
                            
                            $sqlFec	= "SELECT dbo.sumadiasfecha($termino2, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                            $fecVence	= $db->conn->getone($sqlFec);
                            
                            //$sqlDiasV	= "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                            $sqlDiasV	= "SELECT dbo.diashabilestramite(CONVERT (date, GETDATE()), CONVERT (date, '$fecVence') )";
                            $diasTram	= intval(( $db->conn->getone($sqlDiasV) ));
                            
                            //$diasRest = $termino1 - $diasTram;
                            $fecVence = substr($fecVence,0,10);
                            
                            $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '$fecVence', RADI_DIAS_VENCE = $diasTram WHERE RADI_NUME_RADI = " . $numradi;
                            $ok = $db->conn->Execute($sqlUpd);
                            
                            $queryGrabar = "INSERT INTO SGD_PRORROGAS (RADI_NUME_RADI, FECHA_INICIO, FECHA_VENCE, TIPO_DOC, USUA_CODI,
    									       DEPE_CODI, USUA_DOC, DIAS_VENCE)
    							         VALUES($numradi, " . $db->conn->OffsetDate(0, $db->conn->sysTimeStamp) . ", CONVERT(DATETIME,'$fecVence'),
        									   $tipodoc, $codusuario, $dependencia, '$usua_doc', $diasTram)";
        				    $execQuery = $db->conn->Execute($queryGrabar);
                        }
                        else {
                            $respuesta = 0;
                        }
                    }
                    
                } else {
                    //$numradi = $nurad;
                    $respuesta = 0;
                }
            }
            else {
                
                $sqlDiasV	= "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                $diasTram	= ( $db->conn->getone($sqlDiasV) );
                
                $termino1 = $termino - 1;
                $sqlFec	= "SELECT dbo.sumadiasfecha($termino1, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                $fecVence	= $db->conn->getone($sqlFec);
                $fecVence = substr($fecVence,0,10);
                $diasRest = $termino - $diasTram;
                
                $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '$fecVence', RADI_DIAS_VENCE = $diasRest WHERE RADI_NUME_RADI = " . $nurad;
                $ok = $db->conn->Execute($sqlUpd);
            }
            
        } else {
            $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '', RADI_DIAS_VENCE = NULL WHERE RADI_NUME_RADI = " . $nurad;
            $ok = $db->conn->Execute($sqlUpd);
        }

        $selDep = "SELECT DEPE_CODI FROM SGD_RDF_RETDOCF WHERE RADI_NUME_RADI = $nurad AND DEPE_CODI =  $dependencia ";
        $rsDep = $db->conn->Execute($selDep);
        if ($rsDep && !$rsDep->EOF) {
            $sqlUA 	= "	UPDATE SGD_RDF_RETDOCF SET SGD_MRD_CODIGO = '$codiTRDU',
							USUA_CODI = '$codusuario', USUA_DOC = '$usua_doc'
	      			  WHERE	RADI_NUME_RADI = $nurad AND DEPE_CODI =  $dependencia ";
            $rsUp = $db->conn->Execute($sqlUA);
        } else {
            $sqlIns = "	INSERT INTO SGD_RDF_RETDOCF (SGD_MRD_CODIGO, RADI_NUME_RADI, DEPE_CODI, USUA_CODI, USUA_DOC, SGD_RDF_FECH, xx) 
                        VALUES ($codiTRDU, $nurad, $dependencia, $codusuario, '$usua_doc', ".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).", NULL ";
            $rsIns = $db->conn->Execute($sqlIns);
        }     
        
        //Se guarda el registro en el historico de TRD      
        $queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(	SGD_HMTD_FECHA, RADI_NUME_RADI,
								USUA_CODI, SGD_HMTD_OBSE, USUA_DOC, DEPE_CODI, SGD_MRD_CODIGO,
						SGD_TTR_CODIGO) VALUES(	".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
						  $nurad, $codusuario, '$just', $usua_doc, $dependencia, '$codiTRDU', $transac)";
	    $ejecutarQuerey	= $db->conn->Execute($queryGrabar);
        if(empty($ejecutarQuerey)) {
            $response = "0";
        } else {
            $response = "1";
        }
		
		echo $response;
    }
}
elseif ($_POST['tipo'] == 6) {   //  SELECCIONAR TRD DEL RADICADO
    if ($_POST['rad'] != null) {
        $nurad = $_POST['rad'];
        $codusua = $_SESSION["codusuario"];
        $coddepe = $_SESSION['dependencia'];
        
        $html = "";
        $sqlFechaDocto =  $db->conn->SQLDate("Y-m-D H:i:s A","mf.sgd_rdf_fech");
        $sqlSubstDescS =  $db->conn->substr."(s.sgd_srd_descrip, 0, 30)";
        $sqlSubstDescSu = $db->conn->substr."(su.sgd_sbrd_descrip, 0, 30)";
        $sqlSubstDescT =  $db->conn->substr."(t.sgd_tpr_descrip, 0, 30)";
        $sqlSubstDescD =  $db->conn->substr."(d.depe_nomb, 0, 30)";
        
        include "$ruta_raiz/include/query/trd/querylista_tiposAsignados.php";
        $isqlC = 'SELECT	'. $sqlConcat .	'AS "CODIGO"
					, '. $sqlSubstDescS .	'AS "SERIE"
					, '. $sqlSubstDescSu.	'AS "SUBSERIE"
					, '. $sqlSubstDescT .	'AS "TIPO_DOCUMENTO"
					, '. $sqlSubstDescD .	'AS "DEPENDENCIA"
					, m.sgd_mrd_codigo       AS "CODIGO_TRD"
					, mf.usua_codi			 AS "USUARIO"
					, mf.depe_codi           AS "DEPE"
			FROM	SGD_RDF_RETDOCF mf,
					SGD_MRD_MATRIRD m,
					DEPENDENCIA d,
					SGD_SRD_SERIESRD s,
					SGD_SBRD_SUBSERIERD su,
					SGD_TPR_TPDCUMENTO t
	   		WHERE	d.depe_codi = mf.depe_codi
					and s.sgd_srd_codigo  = m.sgd_srd_codigo
					and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
					and su.sgd_srd_codigo = m.sgd_srd_codigo
					and t.sgd_tpr_codigo  = m.sgd_tpr_codigo
					and mf.sgd_mrd_codigo = m.sgd_mrd_codigo
					and mf.radi_nume_radi = '. $nurad;
        $rsC = $db->conn->Execute($isqlC);
        while(!$rsC->EOF){
            $coddocu  =$rsC->fields["CODIGO"];
            $dserie   =$rsC->fields["SERIE"];
            $dsubser  =$rsC->fields["SUBSERIE"];
            $dtipodo  =$rsC->fields["TIPO_DOCUMENTO"];
            $ddepend  =$rsC->fields["DEPENDENCIA"];
            $codiTRDEli  =$rsC->fields["CODIGO_TRD"];
            
            $html .= "<tr>
                          <td class='listado5'> <font size=-3>$coddocu</font> </td>
		                  <td class='listado5'> <font size=-3>$dserie</font> </td>
		                  <td class='listado5'> <font size=-3>$dsubser</font> </td>
		                  <td class='listado5'> <font size=-3>$dtipodo</font> </td>
		                  <td class='listado5'> <font size=-3>$ddepend</font> </td>
		                  <td "; 
		    if (!$rsC->fields["CODIGO"]) 
		        $html .= " class='celdaGris' "; 
		    else 
		        $html .= " class='e_tablas' ";   
		    $html .= ">"; 
		    $html .= "<font size=2>";
        	if($coddocu && $rsC->fields["USUARIO"] == $codusua && $rsC->fields["DEPE"] == $coddepe) {
        	    $html .= "<a href=javascript:borrarArchivo('$codiTRDEli','si')><span class='botones_largo'>Borrar</a> ";
        	} 
        	$html .= "</font></td></tr>";
            
            $rsC->MoveNext();
        }
        
        echo $html;
    }
}
/*elseif ($_POST['tipo'] == 6) {
    if ($_POST['rad'] != null) {
        
        $nurad = $_POST['rad'];
        $codiTRDEli = $_POST['coditrd'];
        $dependencia = $_SESSION['dependencia'];
        $codusuario = $_SESSION["codusuario"];
        $usua_doc = $_SESSION["usua_doc"];
        
        include_once "$ruta_raiz/include/query/busqueda/busquedaPiloto1.php";
        $sqlE ="SELECT	$radi_nume_radi RADI_NUME_RADI FROM	SGD_RDF_RETDOCF r
			     WHERE RADI_NUME_RADI = $nurad AND SGD_MRD_CODIGO = $codiTRDEli";
        $rsE=$db->conn->Execute($sqlE);
        $i=0;
        while(!$rsE->EOF){
            $codiRegE[$i] = $rsE->fields['RADI_NUME_RADI'];
            $i++;
            $rsE->MoveNext();
        }
        
        include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
        $observa = "*Eliminada TRD*".$deta_serie."/".$deta_subserie."/".$deta_tipodocu;
                
        $radiModi = $Historico->insertarHistorico($codiRegE, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 33);
        $radicados = $trd->eliminarTRD($nurad, $dependencia, $usua_doc, $codusuario, $codiTRDEli);
        $mensaje = "Archivo eliminado<br> ";
        
        $queryGrabar = "INSERT INTO SGD_HMTD_HISMATDOC(	SGD_HMTD_FECHA, RADI_NUME_RADI, USUA_CODI, SGD_HMTD_OBSE,
							USUA_DOC, DEPE_CODI, SGD_TTR_CODIGO)
					   VALUES(	".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).", $nurad,
							$codusuario, 'Se borro la TRD', $usua_doc, $dependencia, 33)";
        $ejecutarQuerey	= $db->conn->Execute($queryGrabar);
		if(empty($ejecutarQuerey)){
		    echo 0;
		} else {
		    echo 1;
		}
        
    }
}*/
elseif ($_POST['tipo'] == 7) {    //   ELIMINAR TRD
    if ($_POST['rad'] != null) {
        
        $nurad = $_POST['rad'];
        $coditrd = $_POST['coditrd'];
        $dependencia = $_SESSION['dependencia'];
        $codusuario = $_SESSION["codusuario"];
        $usua_doc = $_SESSION["usua_doc"];
        $respuesta = 0;
        
        include_once "$ruta_raiz/include/query/busqueda/busquedaPiloto1.php";
        $sqlE = "SELECT	$radi_nume_radi RADI_NUME_RADI
			FROM	SGD_RDF_RETDOCF r
			WHERE	RADI_NUME_RADI = $nurad AND  SGD_MRD_CODIGO = $coditrd";
        $rsE = $db->conn->Execute($sqlE);
        $i=0;
        if ($rsE && !$rsE->EOF) {
            while(!$rsE->EOF){
                $codiRegE[$i] = $rsE->fields['RADI_NUME_RADI'];
                $i++;
                $rsE->MoveNext();
            }
            $respuesta = 1;
        } else {
            $respuesta = 0;
        }
        
        if ($respuesta == 1) {
            $TRD = $coditrd;
            include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
            $observa = "*Eliminada TRD*".$deta_serie."/".$deta_subserie."/".$deta_tipodocu;
            
            $Historico = new Historico($db);
            
            $radiModi = $Historico->insertarHistorico($codiRegE, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 33);
            if ($radiModi)
                $respuesta = 1;
            else
                $respuesta = 0;
            
            if ($respuesta == 1) {
                include_once "$ruta_raiz/class_control/TipoDocumental.php";
                $trd = new TipoDocumental($db);
                $trd->eliminarTRD($nurad, $dependencia, $usua_doc, $codusuario, $coditrd);
                $respuesta = 1;
            } else {
                $respuesta = 0;
            }
           
            if ($respuesta == 1) {
                $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '', RADI_DIAS_VENCE = NULL WHERE RADI_NUME_RADI = " . $nurad;
                $ok = $db->conn->Execute($sqlUpd);
                
                $queryGrabar = "INSERT INTO SGD_HMTD_HISMATDOC(	SGD_HMTD_FECHA, RADI_NUME_RADI, USUA_CODI, SGD_HMTD_OBSE,
        							USUA_DOC, DEPE_CODI, SGD_TTR_CODIGO)
        					   VALUES(	".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
        							$nurad, $codusuario, 'Se borro la TRD', $usua_doc, $dependencia, 33)";
        	    $ejecutarQuerey	= $db->conn->Execute($queryGrabar);
        		if(empty($ejecutarQuerey)){
        		    $respuesta = 0;
        		} else {
        		    $respuesta = 1;
        		}
            } else {
                $respuesta = 0;
            }
        }
        
	    echo $respuesta;
    }
}
elseif ($_POST['tipo'] == 8) {     //  INSERTAR TRD
    if ($_POST['rad'] != null) {
        
        $respuesta = '0';
        $nurad = $_POST['rad'];
        $codserie = $_POST['serie'];
        $subserie = $_POST['subserie'];
        $tipodoc = $_POST['tipodoc'];
        $dependencia = $_SESSION['dependencia'];
        $codusuario = $_SESSION["codusuario"];
        $usua_doc = $_SESSION["usua_doc"];
        
        $sqlUpd = "UPDATE RADICADO SET SGD_CAMBIO_TRD = 0 WHERE RADI_NUME_RADI = " . $nurad;
        $ok = $db->conn->Execute($sqlUpd);
        
        $coditrdx = "SELECT S.SGD_TPR_DESCRIP as TPRDESCRIP, R.RADI_DEPE_ACTU, R.RADI_USUA_ACTU, R.SGD_CAMBIO_TRD
            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO S ON R.TDOC_CODI = S.SGD_TPR_CODIGO
            WHERE R.RADI_NUME_RADI = $nurad";
        $res_coditrdx = $db->conn->Execute($coditrdx);
        if ($res_coditrdx && !$res_coditrdx->EOF) {
            $TDCactu = $res_coditrdx->fields['TPRDESCRIP'];
            $usuactu = $res_coditrdx->fields['RADI_USUA_ACTU'];
            $depeactu = $res_coditrdx->fields['RADI_DEPE_ACTU'];
            $cambio = $res_coditrdx->fields['SGD_CAMBIO_TRD'];
        }
        
        
        include_once ($ruta_raiz . "/radsalida/masiva/CombinaPlantilla.php");
        $objcomb = new CombinaPlantilla();
        
        $isqlTdoc = "SELECT SGD_TPR_CODIGO, SGD_TPR_DESCRIP, SGD_TPR_TERMINO FROM SGD_TPR_TPDCUMENTO WHERE SGD_TPR_CODIGO = $tipodoc ";
        $rsTdoc = $db->conn->Execute($isqlTdoc);
        if ($rsTdoc && !$rsTdoc->EOF) {
            $idTipo = $rsTdoc->fields['SGD_TPR_CODIGO'];
            $nomtipo = iconv($objcomb->codificacion($rsTdoc->fields['SGD_TPR_DESCRIP']),'UTF-8',$rsTdoc->fields['SGD_TPR_DESCRIP']);
            $termino = intval($rsTdoc->fields['SGD_TPR_TERMINO']);
        }
        
        if ($termino > 0) {
        //verificar path y plantilla
            $validaPlantilla = false;
            $sqLPath = "SELECT R.RADI_PATH, R.RADI_NUME_DERI AS PADRE, ".$db->conn->SQLDate('Y-m-d H:i:s', 'R.RADI_FECH_RADI')." AS FECHA,
                            FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1
                        FROM RADICADO R WHERE R.RADI_NUME_RADI = " . $nurad;
            $rsPath = $db->conn->Execute($sqLPath);
            if ($rsPath && !$rsPath->EOF) {
                $path = BODEGAPATH . $rsPath->fields["RADI_PATH"];
                $fechaRad = $rsPath->fields["FECHA"];
                $fecha1 = $rsPath->fields['FECHA1'];
                $padre = $rsPath->fields['PADRE'];
                $ext = substr($path, strrpos(trim($path), '.')+1);
                if ($ext == "DOCX" || $ext == "docx" ) {
                    $validaPlantilla = $objcomb->docx2search($path, array('#1#'));
                }
            }
            
            if ($validaPlantilla && $idTipo == 3181) {
                                            
                if (isset($padre)) {
                    $numradi = $padre;
                    
                    $sqlPadre = "SELECT ".$db->conn->SQLDate('Y-m-d H:i:s', 'R.RADI_FECH_RADI')." AS FECHA,
                                FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1, R.RADI_FECHA_VENCE AS FECHA_VENCE,
                                T.SGD_TPR_TERMINO, S.FECHA_VENCE AS PRORROGA
                            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO T ON R.TDOC_CODI = T.SGD_TPR_CODIGO
		                          LEFT JOIN SGD_PRORROGAS S ON R.RADI_NUME_RADI = S.RADI_NUME_RADI
                            WHERE R.RADI_NUME_RADI = " . $numradi;
                    $rsPadre = $db->conn->Execute($sqlPadre);
                    if ($rsPadre && !$rsPadre->EOF) {
                        
                        $fechaRad = $rsPadre->fields["FECHA"];
                        $fecha1 = $rsPadre->fields['FECHA1'];
                        $fechaVenci = $rsPadre->fields['FECHA_VENCE'];
                        $termino = intval($rsPadre->fields['SGD_TPR_TERMINO']);
                        $prorroga = $rsPadre->fields['PRORROGA'];
                        $d = new DateTime($fechaVenci);
                        $format_date = $d->format('Y-m-d');
                        
                        if (date("Y-m-d") < $format_date && !isset($prorroga)) {

                            $termino1 = $termino * 2;
                            $termino2 = $termino1 - 1;
                            
                            $sqlFec	= "SELECT dbo.sumadiasfecha($termino2, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                            $fecVence	= $db->conn->getone($sqlFec);
                            
                            //$sqlDiasV	= "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                            $sqlDiasV	= "SELECT dbo.diashabilestramite(CONVERT (date, GETDATE()), CONVERT (date, '$fecVence') )";
                            $diasTram	= intval(( $db->conn->getone($sqlDiasV) ));                           
                            
                            //$diasRest = $termino1 - $diasTram;
                            $fecVence = substr($fecVence,0,10);
                        
                            $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '$fecVence', RADI_DIAS_VENCE = $diasTram WHERE RADI_NUME_RADI = " . $numradi;
                            $ok = $db->conn->Execute($sqlUpd);
                            
                            $queryGrabar = "INSERT INTO SGD_PRORROGAS (RADI_NUME_RADI, FECHA_INICIO, FECHA_VENCE, TIPO_DOC, USUA_CODI,
    									       DEPE_CODI, USUA_DOC, DIAS_VENCE)
    							         VALUES($numradi, " . $db->conn->OffsetDate(0, $db->conn->sysTimeStamp) . ", CONVERT(DATETIME,'$fecVence'),
        									   $tipodoc, $codusuario, $dependencia, '$usua_doc', $diasTram)";
        					$execQuery = $db->conn->Execute($queryGrabar);
        					if (!$execQuery)
        					    $respuesta = $queryGrabar;
        					else 
        					    $respuesta = '1';
                        }
                        else {
                            $respuesta = "La fecha es mayor o ya tiene prorrroga.";
                        }
                    }
                    
                } else {
                    //$numradi = $nurad;
                    $respuesta = "No tiene radicado padre";
                }
            }
            else {
                
                $sqlDiasV	= "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                $diasTram	= ( $db->conn->getone($sqlDiasV) );
                
                $termino1 = $termino - 1;
                $sqlFec	= "SELECT dbo.sumadiasfecha($termino1, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                $fecVence	= $db->conn->getone($sqlFec);
                $fecVence = substr($fecVence,0,10);
                $diasRest = $termino - $diasTram;
                
                $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '$fecVence', RADI_DIAS_VENCE = $diasRest WHERE RADI_NUME_RADI = " . $nurad;
                $ok = $db->conn->Execute($sqlUpd);
                
                $respuesta = '1';
            }
            
        } else {
            $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '', RADI_DIAS_VENCE = NULL WHERE RADI_NUME_RADI = " . $nurad;
            $ok = $db->conn->Execute($sqlUpd);
            
            $respuesta = '1';
        }
        
        if ($respuesta == '1') {
        
            include_once ($ruta_raiz . "/include/query/busqueda/busquedaPiloto1.php");
            $radiNumero = "";
            $sql = "SELECT	$radi_nume_radi AS RADI_NUME_RADI
    			FROM	SGD_RDF_RETDOCF 
    			WHERE	RADI_NUME_RADI = $nurad AND DEPE_CODI = $dependencia ";
            $rs = $db->conn->Execute($sql);
            if ($rs && ! $rs->EOF) {
                $radiNumero = $rs->fields["RADI_NUME_RADI"];
                $respuesta = '0';
            } else {
                $respuesta = '1';
            }
            
            if ($respuesta == '0') {
                $respuesta = '2';
            } else {
                $isqlTRD = "SELECT	SGD_MRD_CODIGO FROM	SGD_MRD_MATRIRD
    					WHERE DEPE_CODI = $dependencia AND SGD_SRD_CODIGO = $codserie
    							AND SGD_SBRD_CODIGO = $subserie AND SGD_TPR_CODIGO 	= $tipodoc";          
                $rsTRD = $db->conn->Execute($isqlTRD);
                $i = 0;
                if ($rsTRD && ! $rsTRD->EOF) {
                    while (! $rsTRD->EOF) {
                        $codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
                        $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
                        $i ++;
                        $rsTRD->MoveNext();
                    }
                    $respuesta = '1';
                } else {
                    $respuesta = '0';
                }
                
                if ($respuesta == 1) {
                    include_once "$ruta_raiz/class_control/TipoDocumental.php";
                    $trd = new TipoDocumental($db);
                    
                    $radicados = $trd->insertarTRD($codiTRDS, $codiTRD, $nurad, $dependencia, $codusuario);
                    
                    //include_once ($ruta_raiz . "/radicacion/detalle_clasificacionTRD.php");
                    $sqlH = "SELECT	$radi_nume_radi RADI_NUME_RADI FROM SGD_RDF_RETDOCF r
        				WHERE r.RADI_NUME_RADI = $nurad AND r.SGD_MRD_CODIGO =  $codiTRD";
                    $rsH = $db->conn->Execute($sqlH);
                    $i = 0;
                    if ($rsH && ! $rsH->EOF) {
                        while (! $rsH->EOF) {
                            $codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
                            $i ++;
                            $rsH->MoveNext();
                        }
                        $respuesta = '1';
                    } else {
                        $respuesta = '0';
                    }
                    
                    if ($respuesta == '1') {
                                          
                        $transac = 0;
                        if ($radiNumero == "") {
                            $transac = 105;
                        } else {
                            $transac = 34;
                        }
                        $observa = "Tipo documental anterior: " . $TDCactu;
                        $radiModi = $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, $transac);
                        
                        // Se guarda el registro en el historico de TRD
                        $queryGrabar = "INSERT INTO SGD_HMTD_HISMATDOC(	SGD_HMTD_FECHA, RADI_NUME_RADI, USUA_CODI,
    									USUA_DOC, DEPE_CODI, SGD_HMTD_OBSE, SGD_MRD_CODIGO, SGD_TTR_CODIGO)
    									VALUES(	" . $db->conn->OffsetDate(0, $db->conn->sysTimeStamp) . ", $nurad,
    									$codusuario, $usua_doc, $dependencia, 'Se inserta TRD', '$codiTRD', 32)";
    				    $ejecutarQuerey = $db->conn->Execute($queryGrabar);
    				    
    				    $radiUp = $trd->actualizarTRD($codiRegH, $tipodoc);
    				    
    				    $respuesta = '1';
                    }
                }
            }		
        
        }
    }
    
    echo $respuesta;
}
?>