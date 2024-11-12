<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

if (!isset($_SESSION['dependencia']) or !isset($_SESSION['nivelus']))
    include "$ruta_raiz/rec_session.php";

require_once "$ruta_raiz/class_control/correoElectronico.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/include/tx/Tx.php";

$db = new ConnectionHandler("$ruta_raiz");
$mail = new correoElectronico("..");

$rs = new Tx($db);

function validarCorreo($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

//  REALIZAR TRANSACCIONES Este archivo realiza las transacciones de radicados en Orfeo.
?>
<html>
    <head>
        <title>Realizar Transacci&oacute;n - Orfeo </title>
        <link rel="stylesheet" href="../estilos/orfeo.css">
        <script src='../js/jquery.js' type="text/javascript"></script>
    </head>
    <body>
        <?php
        /* Genreamos el encabezado que envia las variable a la paginas siguientes.
         * Por problemas en las sesiones enviamos el usuario.
         * @$encabezado  Incluye las variables que deben enviarse a la singuiente pagina.
         * @$linkPagina  Link en caso de recarga de esta pagina.
         */

        $encabezado = session_name() . "=" . session_id();
        $encabezado .= "&krd=$krd&depeBuscada=$depeBuscada&";
        $encabezado .= "filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";

        /*  FILTRO DE DATOS
         *  @$setFiltroSelect  Contiene los valores digitados por el usuario separados por coma.
         *  @$filtroSelect Si SetfiltoSelect contiene algunvalor la siguiente rutina realiza el arreglo de la condici� para la consulta a la base de datos y lo almacena en whereFiltro.
         *  @$whereFiltro  Si filtroSelect trae valor la rutina del where para este filtro es almacenado aqui.
         */

        if ($checkValue) {
            $num = count($checkValue);
            $i = 0;
            while ($i < $num) {
                $record_id = key($checkValue);
                $setFiltroSelect .= $record_id;
                $radicadosSel[] = $record_id;
                if ($i <= ($num - 2)) {
                    $setFiltroSelect .= ",";
                }
                next($checkValue);
                $i++;
            }
            if ($radicadosSel) {
                $whereFiltro = " and b.radi_nume_radi in($setFiltroSelect)";
            }
        }

        if ($setFiltroSelect) {
            $filtroSelect = $setFiltroSelect;
        }

        $txSql = "";
        if ($chkNivel and $codusuario == 1) {
            $tomarNivel = "si";
        } else {
            $tomarNivel = "no";
        }

        $tmp_rad = array();
		foreach($radicadosSel as $k => $noRadicado) {
			if (strstr($noRadicado, '-')) {
                $tmp = explode('-', $noRadicado);
                $tmp = $tmp[1];
            }
            else
                $tmp = $noRadicado;
            $tmp_rad[] = $tmp;
		}

		// arreglo de no raiz
        $noraiz = array();
        if (isset($_POST['noraiz'])) {
            $keys = explode(",", $_POST['noraiz']);
			foreach($keys as $temp => $recordid) {
				if ($recordid)
                    $noraizid[$temp] = $recordid;
			}
        }

        $noraizid = array();
        if (isset($_POST['noidraiz'])) {
            $keys = explode(",", $_POST['noidraiz']);
			foreach($keys as $temp => $recordid) {
				if ($recordid)
                    $noraizid[$temp] = $recordid;
			}
        }

//Mensaje que se envia por defecto						
        $mensaje1 = "No esta configurada una Cuenta de correo del remitente. --";
        $mensaje2 = "No esta configurada una Cuenta de correo de destino. --";
        $mensaje3 = "El destinatario tiene un radicado con el mismo numero. --";
        $mensaje4 = "El destinatario no permite notificaci�n por correo electronico. --";
        switch ($codTx) {
            case 7:
                $nombTx = "Borrar Informados";
                $observa = "($krd) $observa";
                $radicadosSel = $rs->borrarInformado($radicadosSel, $krd, $depsel8, $_SESSION['dependencia'], $_SESSION['codusuario'], $codusuario, $observa);
                break;
            case 8:
                if (is_array($_POST['usCodSelect'])) {
					foreach($_POST['usCodSelect'] as $k=>$var) {
                        $depsel8 = explode('-', $var);
                        $usCodSelect = $depsel8[1];
                        $depsel8 = $depsel8[0];
                        $nombTx = "Informar Documentos";
                        $usCodDestino .= $rs->informar($radicadosSel, $krd, $depsel8, $dependencia, $usCodSelect, $codusuario, $observa, $_SESSION['usua_doc']) . ", ";
                    }
                }
                $usCodDestino = substr($usCodDestino, 0, strlen(trim($usCodDestino)) - 1);
                break;
            case 9:
                if ($EnviaraV == "VoBo") {
                    $codTx = 16;
                    $carp_codi = 11;
                } else {
                    $codTx = 9;
                    $carp_codi = 2;
                }
                reset($radicadosSel);
                    $arrCodSelect = $_POST['usCodSelect'];
                foreach ($_POST['usCodSelect'] as $var) {
                    $depsel = explode('-', $var);
                    $usCodi = $depsel[1];
                    $depsel = $depsel[0];
                    $sqlNomb = "SELECT	U.USUA_NOMB
						FROM	USUARIO U
						WHERE	U.DEPE_CODI  = $depsel
								AND U.USUA_CODI  = $usCodi
								AND U.USUA_ESTA=1
						ORDER BY U.SGD_ROL_CODIGO DESC, U.USUA_NOMB";
                    $sgd = $db->query($sqlNomb);
                    if (!$sgd->EOF) {
                        $usCodDestino .= $sgd->fields["USUA_NOMB"] . ' ' . $usCodi . '<br/>';
                    }
                }
                
                $nombTx = "Reasignar Documentos";
                $tmp_RadsParaCorreos = array();
                $tmp_DestParaCorreos = array();
                foreach ($radicadosSel as $key => $radicado) {
                    
                    $sqlRad = "SELECT R.RADI_USUA_ACTU, R.RADI_DEPE_ACTU FROM RADICADO R
                         WHERE	R.RADI_NUME_RADI = $radicado ";
                    $rsRad = $db->query($sqlRad);
                    if (!$rsRad->EOF) {
                        $depActual = $rsRad->fields["RADI_DEPE_ACTU"];
                        $usuActual = $rsRad->fields["RADI_USUA_ACTU"];
                    }
                    
                    $seTraslado = false;
                    $radicadoArr = array();
                    $radicadoArr[] = $radicado;
                    if ($_SESSION['dependencia'] == $depActual && $_SESSION['codusuario'] == $usuActual) {
                                                
                        foreach($arrCodSelect as $k=>$var) {
                            $depsel = explode('-', $var);
                            $usCodSelect = $depsel[1];
                            $depSelect = $depsel[0];
                            
                            if ($_SESSION['dependencia'] == $depSelect && $_SESSION['codusuario'] == $usCodSelect) {
                                $usCodDestinoArr[] = $rs->reasignar($radicadoArr, $krd, $depSelect, $dependencia, $usCodSelect, $codusuario, $tomarNivel, $observa, $codTx, $carp_codi);
                                $seTraslado = true;
                                $enviCorreoAccion = true;
                            } else {
                                $usuaDeriva[] = $depSelect."-".$usCodSelect;
                            }
                        }
                        
                        $i = 1;
                        $num = count($usuaDeriva);
                        $enviarCorreo = true;
                        foreach($usuaDeriva as $k=>$var) {
                            $depsel = explode('-', $var);
                            $usCodSelect = $depsel[1];
                            $depSelect = $depsel[0];

                            # busca si el usuario tiene un derivado del documento
                            $destinoEnDerivado = $rs->busca_asignados_derivado($radicadoArr, $depSelect, $usCodSelect);
                            
                            if ($num == $i && $seTraslado == false) {
                                if ($destinoEnDerivado) {
                                    $usFinaliza = $rs->finaliza_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                    $usCodDestinoArr[] = $rs->reasignar($radicadoArr, $krd, $depSelect, $dependencia, $usCodSelect, $codusuario, $tomarNivel, $observa, $codTx, $carp_codi);
                                    $enviCorreoAccion = true;
                                } else {
                                    $observa .= " - !Asigna original";
                                    $usCodDestinoArr[] = $rs->reasignar($radicadoArr, $krd, $depSelect, $dependencia, $usCodSelect, $codusuario, $tomarNivel, $observa, $codTx, $carp_codi);
                                    $enviCorreoAccion = true;
                                }
                            } else {
                                if ($destinoEnDerivado) {
                                    $usFinaliza = $rs->finaliza_derivado_crea($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                    //$usCodDestinoArr[] = $rs->crea_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                    $enviCorreoAccion = true;
                                } else {
                                    $usCodDestinoArr[] = $rs->crea_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                    $enviCorreoAccion = true;
                                }
                            }
                            $i = $i + 1;
                            
                            $query1 = "	SELECT	U.USUA_EMAIL AS EMAIL, U.USUA_PERMNOTREENVIO AS PERMAIL
								FROM	USUARIO U
								WHERE 	U.DEPE_CODI = $depSelect
										AND U.USUA_CODI = $usCodSelect";
                            $email_rsf = $db->conn->Execute($query1);
                            $correoUsuaDes = trim($email_rsf->fields["EMAIL"]);
                            
                            if ($email_rsf->fields["PERMAIL"]==0){
                                $email_res .= $mensaje4;
                                $enviarCorreo = FALSE;
                            }
                            
                            if (!validarCorreo($correoUsuaDes)) {
                                $email_res .= $mensaje2;
                                $enviarCorreo = FALSE;
                            }
                            
                            if ($enviCorreoAccion && $enviarCorreo) {
                                if (!in_array($radicado, $tmp_RadsParaCorreos))
                                    $tmp_RadsParaCorreos[] = $radicado;
                                    if (!in_array($correoUsuaDes, $tmp_DestParaCorreos))
                                        $tmp_DestParaCorreos[] = $correoUsuaDes;
                            }
                            $email_res .= ' Cod: ' . $usCodSelect . '<br/>';
                        }
 
                    } else {
                        
                        foreach($arrCodSelect as $k=>$var) {
                            $depsel = explode('-', $var);
                            $usCodSelect = $depsel[1];
                            $depSelect = $depsel[0];
                            
                            if ($_SESSION['dependencia'] == $depSelect && $_SESSION['codusuario'] == $usCodSelect) {
                                $usFinaliza = $rs->finaliza_derivado_crea($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                $seTraslado = true;
                                $enviCorreoAccion = true;
                            } else {
                                $usuaDeriva[] = $depSelect."-".$usCodSelect;
                            }
                        }

                        $i = 1;
                        $num = count($usuaDeriva);
                        $enviarCorreo = true;
                        foreach($usuaDeriva as $k=>$var) {
                            $depsel = explode('-', $var);
                            $usCodSelect = $depsel[1];
                            $depSelect = $depsel[0];
                            
                            $destinoEnRaiz = $rs->busca_asignados_raiz($radicadoArr, $depSelect, $usCodSelect);
                            $destinoEnDerivado = $rs->busca_asignados_derivado($radicadoArr, $depSelect, $usCodSelect);
                            if ($num == $i && $seTraslado == false) {
                                if ($destinoEnDerivado) {
                                    $obs = "Finaliza derivado por que el usuario ya tenia uno. " . $observa;
                                    $rs->hist_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $obs, $codTx);
                                    $enviCorreoAccion = true;
                                } else {
                                    if ($destinoEnRaiz) {
                                        $usFinaliza = $rs->finaliza_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                        $enviCorreoAccion = true;
                                    } else {
                                        $usCodDestinoArr[] = $rs->reasigna_derivado_crea($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                        $enviCorreoAccion = true;
                                    }
                                }
                                
                                $isql = "DELETE SGD_RG_MULTIPLE WHERE area = " . $_SESSION['dependencia'] . " AND	usuario = " .
                                    $_SESSION['codusuario'] . " AND RADI_NUME_RADI = $radicado";
                                $db->conn->Execute($isql);
                            } else {
                                if ($destinoEnDerivado) {
                                    $usFinaliza = $rs->finaliza_derivado_crea($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                    //$usCodDestinoArr[] = $rs->crea_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                    $enviCorreoAccion = true;
                                } else {
                                    if ($destinoEnRaiz) {
                                        $usFinaliza = $rs->finaliza_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                        $enviCorreoAccion = true;
                                    } else {
                                        $usCodDestinoArr[] = $rs->crea_derivado($radicadoArr, $depSelect, $dependencia, $usCodSelect, $codusuario, $observa, $codTx);
                                        $enviCorreoAccion = true;
                                    }
                                }
                            }
                            $i = $i + 1;
                                                        
                            $query1 = "	SELECT	U.USUA_EMAIL AS EMAIL, U.USUA_PERMNOTREENVIO AS PERMAIL
								FROM	USUARIO U
								WHERE 	U.DEPE_CODI = $depSelect
										AND U.USUA_CODI = $usCodSelect";
                            $email_rsf = $db->conn->Execute($query1);
                            $correoUsuaDes = trim($email_rsf->fields["EMAIL"]);
                            
                            if ($email_rsf->fields["PERMAIL"]==0){
                                $email_res .= $mensaje4;
                                $enviarCorreo = FALSE;
                            }
                            
                            if (!validarCorreo($correoUsuaDes)) {
                                $email_res .= $mensaje2;
                                $enviarCorreo = FALSE;
                            }
                            
                            if ($enviCorreoAccion && $enviarCorreo) {
                                if (!in_array($radicado, $tmp_RadsParaCorreos))
                                    $tmp_RadsParaCorreos[] = $radicado;
                                    if (!in_array($correoUsuaDes, $tmp_DestParaCorreos))
                                        $tmp_DestParaCorreos[] = $correoUsuaDes;
                            }
                            $email_res .= ' Cod: ' . $usCodSelect . '<br/>';
                        }
                    }
                }	
                $radicadosSel = $tmp_RadsParaCorreos;
                break;
            case 10:
                $nombTx = "Movimiento a Carpeta $carpetaNombre";
                $okTx = $rs->cambioCarpeta($radicadosSel, $krd, $carpetaCodigo, $carpetaTipo, $tomarNivel, $observa);
                $depSel = $dependencia;
                $usCodSelect = $codusuario;
                $usCodDestino = $usua_nomb;
                break;
            case 12:
                //Devolver
				/*
                  Foreach ($radicadosSel as $key => $radicado) {
                  $enviarCorreo = TRUE;
                  $nombTx = "Devolver Radicado";
                  if (!validarCorreo($_SESSION['usua_email'])) {
                  $email_res .= $mensaje1;
                  $enviarCorreo = FALSE;
                  }
                  //Envio de correo si la transacion fue exitosa
                  $query1 = "	SELECT	U.USUA_EMAIL AS EMAIL
                  FROM	USUARIO U,
                  RADICADO R
                  WHERE	U.USUA_LOGIN = R.RADI_USU_ANTE
                  AND R.RADI_NUME_RADI = $radicado";
                  $email_rsf = $db->conn->Execute($query1);
                  $correoUsuaDes = trim($email_rsf->fields["EMAIL"]);
                  if (!validarCorreo($correoUsuaDes)) {
                  $email_res .= $mensaje2;
                  $enviarCorreo = FALSE;
                  }
                  if ($enviarCorreo) {
                  $correoEmisor = $_SESSION['usua_email'];
                  $nombreEmisor = $_SESSION['usua_nomb'];
                  $nombdepend = $_SESSION['depe_nomb'];
                  $listRad = is_array($tmp_rad) ? implode(',', $tmp_rad) : $tmp_rad;
                  //$mail->From = $correoEmisor;
                  $mail->FromName = $nombreEmisor;
                  $mail->AddAddress($correoUsuaDes);
                  $mail->Subject = "ORFEO: Devolución de radicado(s) " . $listRad;
                  $mail->Body = "<center/>El usuario <strong/>" . $nombreEmisor . "</strong/> de la dependencia
                  <strong/>" . $nombdepend . "</strong/> le regreso el(los) documento(s) ("
                  . $listRad . ") al sistema Orfeo, con el siguiente comentario:
                  <br/> <br/><strong/>'" . $observa . "'</strong/></center/>";
                  if ($mail->Send()) {
                  $email_men = "Se envio correo electronico: $correoUsuaDes
                  <br/>Enviado Por: $nombreEmisor
                  <br/>Con el correo:	$correoEmisor";
                  } else {
                  $email_men = "Problemas enviando correo electr�nico a $correoUsuaDes error: " . $mail->ErrorInfo;
                  }
                  $mail->ClearAddresses();
                  $mail->ClearAttachments();
                  $email_res .= $email_men;
                  }
                  }
				*/
                $nombTx = "Devolucion de Documentos";
                $usCodDestino = $rs->devolver($radicadosSel, $krd, $dependencia, $codusuario, $tomarNivel, $observa);
                break;
            case 13:
                $nombTx = "Archivo de Documentos";
                //echo "<br/> norauiz:";
                //print_r($noraiz);
                //echo "<br/>";
                foreach ($radicadosSel as $key => $radicado) {
                    $radicadoArr = array();
                    $radicadoArr[] = $radicado;
                    
                    $destinoEnDerivado = $rs->busca_asignados_derivado($radicadoArr, $dependencia, $codusuario);
                    //if (!in_array($radicado, $noraiz)) { //no es derivado
                    if (!$destinoEnDerivado) { //no es derivado
                        ### SE VERIFICAN LOS RADICADOS QUE NO TIENEN EXPEDIENTE Y SE
                        ### INCLUYEN EN EL EXPEDIENTE DEL RADICADO PADRE
                        $sqlRadExp = '	SELECT	DISTINCT 
												(SELECT	TOP 1 X.SGD_EXP_NUMERO
												FROM	SGD_EXP_EXPEDIENTE X
												WHERE	X.RADI_NUME_RADI = R.RADI_NUME_DERI AND 
														X.SGD_EXP_ESTADO <> 2) AS EXP
										FROM	RADICADO AS R,
												SGD_EXP_EXPEDIENTE AS E
										WHERE	R.RADI_NUME_RADI = ' . $radicado . ' AND
												R.RADI_NUME_RADI NOT IN (	SELECT	RADI_NUME_RADI
																			FROM	SGD_EXP_EXPEDIENTE E
																			WHERE	R.RADI_NUME_RADI = ' . $radicado . ' AND 
																					E.SGD_EXP_ESTADO <> 2)';
                        $rsRadExp = $db->conn->Execute($sqlRadExp);
                        $exp = $rsRadExp->fields['EXP'];

                        if ($exp) {
                            include_once "$ruta_raiz/include/tx/Expediente.php";
                            $ex = new Expediente($db);

                            $incluirExp = $ex->insertar_expediente($exp, $radicado, $dependencia, $codusuario, $_SESSION['usua_doc']);

                            ### SI SE INCLUYO EL RADICADO EN EL EXPEDIENTE
                            if ($incluirExp == 1) {
                                $ob = "Incluir radicado en Expediente";
                                include_once "$ruta_raiz/include/tx/Historico.php";
                                $Historico = new Historico($db);
                                $radicados[0] = $radicado;
                                $tipoTx = 53;
                                $Historico->insertarHistoricoExp($exp, $radicado, $dependencia, $codusuario, $ob, $tipoTx, 0);
                            } else {
                                print '<hr><font color=red>No se incluyo el radicado No. ' . $radicado . ' en el expediente No. ' . $exp . '. Por favor intente de nuevo.</font><hr>';
                                break;
                            }
                        }

                        ### SE ARCHIVA EL RADICADO
                        $txSql = $rs->archivar($radicadoArr, $krd, $dependencia, $codusuario, $observa);
						$usCodDestino = 'Grupo de Biblioteca y Archivo';
                    } 
					else {
                        $txSql = $rs->finaliza_derivado($radicadoArr, $dependencia, $dependencia, $codusuario, $codusuario, $observa, $codTx);
                    }
                }
                break;
            case 14:
                $nombTx = "Agendar Documentos";
                $txSql = $rs->agendar($radicadosSel, $krd, $dependencia, $codusuario, $observa, $fechaAgenda);
                break;
            case 15:
                $nombTx = "Sacar de 'Agendar Documentos'";
                $txSql = $rs->noAgendar($radicadosSel, $krd, $dependencia, $codusuario, $observa);
                break;
            case 16:
                $nombTx = "Radicados NRR";
                $txSql = $rs->nrr($radicadosSel, $krd, $dependencia, $codusuario, $observa);
                break;
        }

        if ($okTx == -1)
            $okTxDesc = " No ";
        //  IMPRESION DE RESULTADOS DE LA TRANSACCION
        ?>

        <table align="center" border="0" cellspace="2" cellpad="2" WIDTH="95%" class="t_bordeGris" id="tb_general" align="left">
            <tr>
                <td colspan="2" class="titulos4">
                    ACCI&Oacute;N REQUERIDA <?= $accionCompletada ?><?= $okTxDesc ?>COMPLETADA <?= $causaAccion ?>
                </td>
            </tr>
            <tr>
                <td width="15%" align="right" bgcolor="#CCCCCC" height="25" class="titulos2">
                    ACCI&Oacute;N REQUERIDA :
                </td>
                <td height="25" class="listado2_no_identa">
                    <?= $nombTx ?>
                </td>
            </tr>
            <tr>
                <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">
                    RADICADOS INVOLUCRADOS :
                </td>
                <td height="25" class="listado2_no_identa"><?= join("<BR> ", $tmp_rad) ?></td>
            </tr>
            <tr>
                <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">
                    USUARIO DESTINO :
                </td>
                <td height="25" class="listado2_no_identa">
                    <?= $usCodDestino ?>
                </td>
            </tr>
            <tr>
                <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">
                    FECHA Y HORA :
                </td>
                <td height="25" class="listado2_no_identa">
                    <?= date("d-m-Y H:i:s") ?>
                </td>
            </tr>
            <tr>
                <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">
                    USUARIO ORIGEN:
                </td>
                <td height="25" class="listado2_no_identa">
                    <?= $usua_nomb ?>
                </td>
            </tr>
            <tr>
                <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">
                    DEPENDENCIA ORIGEN:
                </td>
                <td height="25" class="listado2_no_identa">
                    <?= $depe_nomb ?>
                </td>
            </tr>
            <tr>
                <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">
                    ENV&Iacute;O DE CORREO:
                </td>
                <td height="25" class="listado2_no_identa">
                    <div id="sendMail" name="sendMail"></div>
                </td>
            </tr>
        </table>
        <input type="hidden" value="<?=$codTx?>" name="txtTx" id="txtTx" />
        <input type="hidden" value="<?=implode("_", $radicadosSel)?>" name="txtRads" id="txtRads" />
        <input type="hidden" value="<?=$observa?>" name="txtObserva" id="txtObserva" />
        <input type="hidden" value="<?=$_SESSION['usua_nomb']?>" name="txtUsua" id="txtUsua" />
        <input type="hidden" value="<?=$_SESSION['depe_nomb']?>" name="txtDepe" id="txtDepe" />
        <input type="hidden" value="<?= (is_array($tmp_DestParaCorreos))?implode(";", $tmp_DestParaCorreos):"";?>" name="txtMails" id="txtMails" />
        <script type="text/javascript" language="javascript">
			
            $(document).ready(function(){enviarCorreoAsincrono();});
		
            function enviarCorreoAsincrono(){
                var dataString = 'tx='+ $("input#txtTx").val() + '&obse=' + $("input#txtObserva").val() + '&rads=' + $("input#txtRads").val() + "&usr="+ $("input#txtUsua").val() +"&dep="+$("input#txtDepe").val()+"&mails="+$("input#txtMails").val();
                $.ajax({
                    type: "POST",
                    url: "enviaNotificacion.php",
                    async:true,
                    cache:false,
                    dataType:"html",
                    contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
                    data: dataString,
                    success:  function(respuesta){
                        $("#sendMail").html(respuesta);
                    },
                    beforeSend:function(){$("#sendMail").html("Enviando notificaciones...")},
                    error:function(objXMLHttpRequest){}
                });
            }
        </script>
    </body>
</html>
