<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~ E_NOTICE & ~ E_WARNING);

require_once $ruta_raiz . '/_conf/constantes.php';
require_once 'HTML/Template/IT.php';
require_once ORFEOPATH . 'class_control/Dependencia.php';

// si llegan datos del grid de cuerpo, lista de numeros de radicados separados por coma.
include_once ORFEOPATH . "php-ext/php-ext/php-ext.php";
include_once NS_PHP_EXTJS_CORE;
include_once NS_PHP_EXTJS_DATA;
include_once NS_PHP_EXTJS_GRID;

$cont = 0;
$krdOld = $krd;
$usuaDoc = 0;
$carpetaOld = $carpeta;
$moverSelect = 0;
$tipoCarpOld = $tipo_carp;
$msg = 0; // Variable que se utiliza para saber si sale el alert de Js que pregunta si se quita marca de radicado privado
          
// reemplaza el post por los seleccinados del grid
if (isset($_POST['seleccionados'])) {
    $keys = explode(",", $_POST['seleccionados']);
    $seleccionados = array();
    foreach ($keys as $k => $v) {
        $seleccionados[$v] = $k;
    }
    // while (list($temp, $recordid) = each($keys)) {
    // $seleccionados[$recordid] = $temp;
    // }
    $_POST['checkValue'] = $seleccionados;
}
;

// arma array de derivados
$derivados = array();
if (isset($_POST['noraiz'])) {
    $keys = explode(",", $_POST['noraiz']);
    foreach ($keys as $k => $v) {
        if ($v != "") {
            $derivados[$v] = $k;
        }
    }
    // While (list($temp, $recordid) = each($keys)) {
    // if ($recordid != "") {
    // $derivados[$recordid] = $temp;
    // }
    // }
} else {
    if ((count($_POST['checkValue']) == 1) && (count($_POST['seleccionados']) == 0)) {
        //print_r($_POST['checkValue']);
    }
}

// fitra para algunos casos solo los radicados del raiz
switch ($codTx) {
    case 10:
    case 12:
        $_POST['checkValue'] = array_diff(array_flip($_POST['checkValue']), array_flip($derivados));
        $_POST['checkValue'] = array_flip($_POST['checkValue']);
        if (count($_POST['checkValue']) == 0) {
            $_POST['checkValue'] = array(
                - 999999 => 0
            );
        }
        break;
    default:
        break;
}

$moverInformados = isset($_POST['moverInfEstado']) ? $_POST['moverInfEstado'] : null;
$informadosUpd = '';
$informadoTmp = '';
$informadosMover = array();
$informadosMover = (is_array($_POST['checkValue'])) ? array_keys($_POST['checkValue']) : null;
$carpetaNuevaInf = $_POST['moverSelect'];
$moverSelect = $_POST['moverSelect'];

$mensaje_error = false;
$subCarpDest = '';

if (! isset($_SESSION['dependencia']))
    include (ORFEOPATH . "rec_session.php");

$depeCodi = $_SESSION['dependencia'];

// Inclusion de archivos para utilizar la libreria ADODB
require_once ORFEOPATH . "include/db/ConnectionHandler.php";
if (! isset($db))
    $db = new ConnectionHandler($ruta_raiz);

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
// $db->conn->debug = true;
$tpl = new HTML_Template_IT(TPLPATH);
$objDep = new Dependencia($db);

/*
 * Genreamos el encabezado que envia las variable a la paginas siguientes.
 * Por problemas en las sesiones enviamos el usuario.
 * @$encabezado Incluye las variables que deben enviarse a la singuiente pagina.
 * @$linkPagina Link en caso de recarga de esta pagina.
 */
$encabezado = session_name() . "=" . session_id();
$encabezado .= "&krd=$krd&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
$linkPagina = $_SERVER['PHP_SELF'] . "?$encabezado&orderTipo=$orderTipo&orderNo=";

if (! empty($moverInformados) && $moverInformados == "true") {
    if ($moverSelect != 'N') {
        $moverSelect = $_POST['moverSelect'];
        $usuaDoc = $_SESSION['usua_doc'];
        $usuaCodi = $_SESSION['codusuario'];
        if (is_array($informadosMover) || ! empty($informadosMover)) {
            $cont = count($informadosMover);
            foreach ($informadosMover as $informado) {
                $cont --;
                $informadoTmp = explode('-', $informado);
                $informadosUpd .= $informadoTmp[1];
                if ($cont) {
                    $informadosUpd .= ',';
                }
            }
        } else {
            // No coloco ningun informado para mover
            $tpl->loadTemplatefile('errorMovimientoInf.tpl');
            $tpl->setVariable('ERROR_MSG', "No ha seleccionado ningun informado!!!");
            $tpl->setVariable('ERROR_MSG_DESC', "Por favor seleccione informado(s) que desee mover!!!");
            $tpl->show();
            exit();
        }
        $sqlInf = "	UPDATE	INFORMADOS
					SET		SGD_INFDIR_CODIGO = $carpetaNuevaInf
					WHERE	RADI_NUME_RADI IN($informadosUpd) AND
							USUA_DOC='$usuaDoc' AND
							USUA_CODI ='$usuaCodi'";
        $rsInf = $db->conn->Execute($sqlInf);
        
        if ($rsInf === false) {
            $tpl->loadTemplatefile('errorMovimientoInf.tpl');
            $tpl->setVariable('ERROR_MSG', "Error en la actualizacion del informado!!!");
            $tpl->setVariable('ERROR_MSG_DESC', $db->conn->ErrorMsg());
            $tpl->show();
            exit();
        }
        
        $sqlInfCarp = "	SELECT	SGD_INFDIR_NOMBRE
						FROM	SGD_INFDIR_INFORMADOSDIR
						WHERE	SGD_INFDIR_CODIGO = $carpetaNuevaInf";
        $rsInfCarp = $db->conn->Execute($sqlInfCarp);
        
        if (! $rsInfCarp->EOF) {
            $subCarpDest = $rsInfCarp->fields['SGD_INFDIR_NOMBRE'];
        }
        
        $tpl->loadTemplatefile('mostrarMovimientoInformados.tpl');
        $tpl->setVariable('USUA_LOGIN', $usuario);
        $tpl->setVariable('INFORMADOS', $informadosUpd);
        $tpl->setVariable('DEPE_NOMB', $depe_nomb);
        $tpl->setVariable('SUB_CARPETA', $subCarpDest);
        $tpl->setVariable('USUA_LOGIN_DEST', $krd);
        $tpl->show();
        exit();
    } else {
        // No ha seleccionado una carpeta destinoecho
        $errorCarpNo = "No ha seleccionado la carpeta destino donde van a mover los informados";
        $tpl->loadTemplatefile('errorMovimientoInf.tpl');
        $tpl->setVariable('ERROR_MSG', "Movimiento de informado!!!");
        $tpl->setVariable('ERROR_MSG_DESC', $errorCarpNo);
        $tpl->show();
        exit();
    }
}

// filtro de datos
if (isset($_POST['checkValue'])) {
    $num = count($_POST['checkValue']);
    reset($_POST['checkValue']);
    $i = 0;
    foreach ($_POST['checkValue'] as $recordid => $tmp) {
        $record_id = $recordid;
        
        // ## CONSULTA SI EL RADICADO ES UN DERIVADO
        $rsq1 = "SELECT	RADI_NUME_RADI AS DERIVADO
				FROM	SGD_RG_MULTIPLE
				WHERE	RADI_NUME_RADI = $record_id
						AND	USUARIO = $codusuario
						AND	AREA = $dependencia
						AND ESTATUS = 'ACTIVO'";
        $res = $db->conn->Execute($rsq1);
        $existeDev = $res->fields['DERIVADO'];
        
        // ## CONSULTA DATOS DEL RADICADO
        $isql = "SELECT	R.RADI_USUA_ACTU AS USU,
						R.RADI_DEPE_ACTU AS DEP,
						R.RADI_PATH AS RUTA,
						R.SGD_EANU_CODIGO AS EANU,
						T.RADI_NUME_RADI AS TRD,
						R.TDOC_CODI AS TDOC,
						R.RADI_NUME_DERI AS ASOCIADO
				FROM	RADICADO R
						LEFT JOIN SGD_RDF_RETDOCF T ON R.RADI_NUME_RADI = T.RADI_NUME_RADI
				WHERE	R.RADI_NUME_RADI = $record_id";
        $rsr = $db->conn->Execute($isql);
        
        // ## USUARIO ACTUAL DEL RADICADO
        $radi_usua_actu = $rsr->fields["USU"];
        $radi_depe_actu = $rsr->fields["DEP"];
        
        // ## EXTENSION DE LA IMAGEN DEL RADICADO
        $pathImg = $rsr->fields["RUTA"];
        if ($pathImg != Null)
            $extImg = substr($pathImg, - 3);
        else
            $extImg = 0;
        
        // ## ESTADO DE ANULACION DEL RADICADO
        $eanu = $rsr->fields["EANU"];
        
        // ## TIENE TRD EL RADICADO
        $radTRD = $rsr->fields["TRD"];
        
        // ## TIPO DOCUMENTAL DEL RADICADO
        $tdoc = $rsr->fields["TDOC"];
        
        // ## RADICADO ASOCIADO AL RADICADO PRINCIPAL
        $asociado = $rsr->fields["ASOCIADO"];
        
        // # CONSULTA EL PERMISO DE EXONERADO DE APLICAR TRD AL REASIGNAR
        $ksql = "SELECT	USUA_NO_TIPIFICA AS SIN_TRD
				FROM	USUARIO
				WHERE 	USUA_CODI = $codusuario and
						DEPE_CODI = $dependencia and
						USUA_ESTA <> 0";
        $kres = $db->conn->Execute($ksql);
        $noTRD = $kres->fields["SIN_TRD"];
        
        // ## CONDICION PARA CONSULTAR ANEXOS RADICADOS
        if (empty($existeDev)) {
            $isqlTRDA = "SELECT	RADI_NUME_SALIDA as RADI_NUME_SALIDA
						FROM	ANEXOS
						WHERE	ANEX_RADI_NUME = '$record_id' and
								RADI_NUME_SALIDA != 0 AND
								ANEX_BORRADO = 'N' AND
								RADI_NUME_SALIDA not in(
									SELECT	RADI_NUME_RADI
									FROM	SGD_RDF_RETDOCF)";
        } else {
            $isqlTRDA = "SELECT	RADI_NUME_SALIDA as RADI_NUME_SALIDA
						FROM	ANEXOS A
								INNER JOIN USUARIO U ON
									ANEX_CREADOR = U.USUA_LOGIN AND
									U.USUA_CODI = $codusuario AND
									U.DEPE_CODI	= $dependencia
						WHERE	ANEX_RADI_NUME = '$record_id' AND
								RADI_NUME_SALIDA != 0 AND
								ANEX_BORRADO = 'N' AND
								RADI_NUME_SALIDA NOT IN (
									SELECT	RADI_NUME_RADI
									FROM	SGD_RDF_RETDOCF)";
        }
        $rsTRDA = $db->conn->Execute($isqlTRDA);
        
        switch ($codTx) {
            case 7:
            case 8:
                if (strpos($record_id, '-')) {
                    // Si trae el informador concatena el informador con el radicado sino solo concatena los radicados.
                    $tmp = explode('-', $record_id);
                    if ($tmp[0]) {
                        $whereFiltro .= ' (b.radi_nume_radi = ' . $tmp[1] . ' and i.info_codi=' . $tmp[0] . ') or';
                        $tmp_arr_id = 2;
                    } else {
                        $whereFiltro .= ' b.radi_nume_radi = ' . $tmp[1] . ' or';
                        $tmp_arr_id = 1;
                    }
                } else {
                    $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                    $tmp_arr_id = 0;
                }
                $record_id = $tmp[1];
                break;
            
            // ##################################################################
            // # ACCION DE REASIGNAR
            case 9:
                $pasaFiltro = "Si";
                
                // ## USUARIO EXONERADO DE APLICAR TRD
                if ($noTRD != 1) {
                    
                    // ## VERIFICA SI EL RADICADO ES DERIVADO O QUIEN REASIGNA ES JEFE
                    if (($_POST['rolUsuario'] == 1) or ($existeDev)) { // probar reasignando derivado desde un usuario jefe
                    } else {
                        // ## EL RADICADO NO TIENE TRD
                        if (strlen(trim($radTRD) == 0)) {
                            $pasaFiltro = "No";
                            $flagTRD .= $record_id . ", ";
                            
                            // ## N
                            if ($i <= ($num)) {
                                $flagTRD .= ",";
                            }
                        }
                        
                        // ## VALIDA CONDICIONES DE LOS ANEXOS DEL RADICADO
                        while ($rsTRDA && ! $rsTRDA->EOF && $pasaFiltro != "No") {
                            $radiNumero = $rsTRDA->fields["RADI_NUME_SALIDA"];
                            $anoRadsal = substr($radiNumero, 0, 4);
                            $depRadsal = substr($radiNumero, 4, 3);
                            if ($depRadsal == $_SESSION['dependencia']) { // Se valida si el anexo pertenece a la misma dependencia del usuario
                                if ($radiNumero != '' && ! ($anoRadsal == "2006" or $anoRadsal == "2005" or $anoRadsal == "2004" or $anoRadsal == "2003")) {
                                    $pasaFiltro = "No";
                                    $flagAnexTrd .= $radiNumero . ", ";
                                    if ($i <= ($num)) {
                                        $flagAnexTrd .= " ";
                                    }
                                    break;
                                }
                            }
                            $rsTRDA->MoveNext();
                        } // FIN -VALIDA CONDICIONES DE LOS ANEXOS DEL RADICADO
                        $i ++;
                    } // FIN -VERIFICA SI EL RADICADO ES DERIVADO O QUIEN REASIGNA ES JEFE
                } // FIN -USUARIO EXONERADO DE APLICAR TRD
                  
                // ## VALIDA SI EL RADICADO NO TIENE IMAGEN ASOCIADA
                $arrayExt = array(
                    "doc",
                    "ocx",
                    "odt",
                    "odf",
                    "rtf",
                    "DOC",
                    "docx"
                );
                if (in_array($extImg, $arrayExt) and (! ($eanu == 1 or $eanu == 2))) {
                    $flag = 0;
                    $var = substr($record_id, 4, 4);
                    
                    // ## SE VALIDA SI UNA DE LAS DEPENDENCIAS A REASIGNAR ES LA MISMA DEP. DEL RADICADO
                    foreach ($depsel as $depAux) {
                        if ($depAux == $var) {
                            $flag = 1;
                        }
                    }
                    
                    // ## SI LAS DEPENDENCIAS SON DIFERENTES A LA DEL RADICADO NO SE PUEDE REASIGNAR
                    if ($flag == 0) {
                        $pasaFiltro = "No";
                        $flagImg .= $record_id . ", ";
                    }                    // ## SI LA DEPENDENCIA ES LA MISMA DEL RADICADO, SE PERMITE REASIGNAR SIN IMAGEN
                    else {
                        unset($depsel);
                        $depsel[0] = $var;
                    }
                }
                
                /*if ($pasaFiltro == "Si") {
                    // ## CONSULTA DE EXPEDIENTES PARA EL RADICADO
                    $isqlCambio = "SELECT SGD_CAMBIO_TRD FROM RADICADO WHERE RADI_NUME_RADI = $record_id";
                    $rsCambio = $db->conn->Execute($isqlCambio);
                    $cambio = $rsCambio->fields['SGD_CAMBIO_TRD'];
                    if ($cambio > 0) {
                        $flagCambio .= $record_id . ", ";
                        $pasaFiltro = "No";
                    }
                }*/
                
                $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                break;
            // ## FIN -ACCION DE REASIGNAR RADICADO
            // ##################################################################
            
            // ##################################################################
            // ## ACCION DEVOLVER RADICADO
            case 12:
                $pasaFiltro = "Si";
                if ($num == 1) {
                    if ($codusuario != $radi_usua_actu) {
                        $flagDeri = $record_id . ", ";
                        $pasaFiltro = "No";
                        $_POST['noraiz'] = $record_id;
                        break;
                    }
                }
                
                // ## CONSULTA EL ESTADO DEL USUARIO A QUIEN SE DEVUELVE EL RADICADO
                $isql = "SELECT	U.USUA_ESTA AS ESTADO,
								U.USUA_LOGIN AS LOGIN
						FROM	RADICADO R
								INNER JOIN USUARIO U ON
								U.USUA_LOGIN = R.RADI_USU_ANTE
						WHERE	R.RADI_NUME_RADI = $record_id ";
                $rsr = $db->conn->Execute($isql);
                $estado = $rsr->fields["ESTADO"];
                $usuLogin = $rsr->fields["LOGIN"];
                
                // ## EL USUARIO DESTINO ES EL MISMO USUARIO ACTUAL
                if ($usuLogin != $krd) {
                    
                    // ## EL USUARIO DESTINO ESTA ACTIVO
                    if ($estado == 1) {
                        
                        // ## USUARIO EXONERADO DE APLICAR TRD
                        if ($noTRD != 1) {
                            
                            // ## VERIFICA SI QUIEN DEVUELVE ES JEFE
                            if ($codusuario != 1) {
                                
                                // ## VERIFICA TRD DEL RADICADO
                                if (strlen(trim($radTRD) == 0)) {
                                    $pasaFiltro = "No";
                                    $flagTRD .= $record_id . ", ";
                                    if ($i <= ($num)) {
                                        $flagTRD .= " ";
                                    }
                                } // FIN -VERIFICA TRD DEL RADICADO
                            } // FIN -VERIFICA SI QUIEN DEVUELVE ES JEFE
                        } // FIN -USUARIO EXONERADO DE APLICAR TRD
                          
                        // ## VALIDA SI EL RADICADO TIENE IMAGEN ASOCIADA
                        $arrayExt = array(
                            "doc",
                            "ocx",
                            "odt",
                            "odf",
                            "rtf",
                            "DOC",
                            "docx"
                        );
                        if (in_array($extImg, $arrayExt) and (! ($eanu == 1 or $eanu == 2))) {
                            $pasaFiltro = "No";
                            $flagImg .= $record_id . ", ";
                        } // FIN -VALIDA SI EL RADICADO NO TIENE IMAGEN ASOCIADA
                          
                        // ## SI EL RADICADO CUMPLE TODO SE VALIDAN LOS ANEXOS
                        if ($pasaFiltro == "Si") {
                            
                            // ## VALIDA CONDICIONES DE LOS ANEXOS DEL RADICADO
                            while ($rsTRDA && ! $rsTRDA->EOF && $pasaFiltro != "No") {
                                $radiNumero = $rsTRDA->fields["RADI_NUME_SALIDA"];
                                $anoRadsal = substr($radiNumero, 0, 4);
                                $depRadsal = substr($radiNumero, 4, 3);
                                if ($depRadsal == $_SESSION['dependencia']) { // Se valida si el anexo pertenece a la misma dependencia del usuario
                                    if ($radiNumero != '' && ! ($anoRadsal == "2006" or $anoRadsal == "2005" or $anoRadsal == "2004" or $anoRadsal == "2003")) {
                                        $pasaFiltro = "No";
                                        $flagAnexTrd .= $radiNumero . ", ";
                                        if ($i <= ($num)) {
                                            $flagAnexTrd .= " ";
                                        }
                                    }
                                }
                                $rsTRDA->MoveNext();
                            } // FIN -VALIDA CONDICIONES DE LOS ANEXOS DEL RADICADO
                            $i ++;
                        } // FIN -SI EL RADICADO CUMPLE TODO SE VALIDAN LOS ANEXOS
                    } // FIN -EL USUARIO DESTINO ESTA ACTIVO
                    else {
                        $pasaFiltro = "No";
                        $flagUsInac .= $record_id . ", ";
                        break;
                    }
                } // FIN -EL USUARIO DESTINO ES EL MISMO USUARIO ACTUAL
                else {
                    $pasaFiltro = "No";
                    $flagUsIgual .= $record_id . ", ";
                    break;
                }
                $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                break;
            // ## FIN -ACCION DEVOLVER RADICADO
            // ##################################################################
            
            // ##################################################################
            // # ACCION DE ARCHIVAR
            case 13:
                
                if ($pasaFiltro == "No") {
                    break;
                    break;
                }
                
                $pasaFiltro = "Si";
                $anoRad = substr($record_id, 0, 4);
                $tipoRad = substr($record_id, -1);
                
                if (($num == 1) && (count($derivados) == 0)) {
                    if ($codusuario != $radi_usua_actu) {
                        $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                        $_POST['noraiz'] = array(
                            $record_id
                        );
                        $tmp_arr_id = 0;
                        break;
                    }
                }
                
                // ## EL ANO DEL RADICADO ES SUPERIOR A 2006
                if (! ($anoRad == "2006" or $anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")) {
                    
                    // ## EL RADICADO ES EL ORIGINAL, NO ES UN DERIVADO
                    if (empty($existeDev)) {
                        
                        // ## EL RADICADO NO TIENE TRD
                        if (strlen(trim($radTRD) == 0)) {
                            $pasaFiltro = "No";
                            $flagTRD .= $record_id . ", ";
                            if ($i <= ($num)) {
                                $flagTRD .= ",";
                            }
                        }
                        
                        // ## VALIDA SI EL RADICADO NO TIENE IMAGEN ASOCIADA
                        $arrayExt = array(
                            "doc",
                            "ocx",
                            "odt",
                            "odf",
                            "rtf",
                            "DOC",
                            "docx"
                        );
                        if (in_array($extImg, $arrayExt) and (! ($eanu == 1 or $eanu == 2))) {
                            $pasaFiltro = "No";
                            $flagImg .= $record_id . ", ";
                        }
                        
                        // ## VALIDA CONDICIONES DE LOS ANEXOS DEL RADICADO
                        while ($rsTRDA && ! $rsTRDA->EOF) {
                            $radiNumero = $rsTRDA->fields["RADI_NUME_SALIDA"];
                            $anoRadsal = substr($radiNumero, 0, 4);
                            $depRadsal = substr($radiNumero, 4, 3);
                            if ($depRadsal == $_SESSION['dependencia']) { // Se valida si el anexo pertenece a la misma dependencia del usuario
                                if ($radiNumero != '' && ! ($anoRadsal == "2006" or $anoRadsal == "2005" or $anoRadsal == "2004" or $anoRadsal == "2003")) {
                                    $pasaFiltro = "No";
                                    $flagAnexTrd .= $radiNumero . ", ";
                                    if ($i <= ($num)) {
                                        $flagAnexTrd .= " ";
                                    }
                                }
                            }
                            $rsTRDA->MoveNext();
                        }
                        $i ++;
                        
                        $deta_pqr = '0';
                        $queryTrd = "SELECT R.SGD_MRD_CODIGO FROM SGD_RDF_RETDOCF R WHERE R.RADI_NUME_RADI = $record_id ORDER BY R.SGD_RDF_FECH DESC ";
                        $rsTrd = $db->conn->Execute($queryTrd);
                        if ($rsTrd && !$rsTrd->EOF) {
                        
                            $TRD = $rsTrd->fields['SGD_MRD_CODIGO'];
                            include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
                            
                            $deta_pqr = ($deta_pqr == null ? '0' : $deta_pqr);
                            if ($pasaFiltro == "Si" && $tipoRad == '2' && $deta_pqr == '1' && $tdoc != 4683) {
                                $anexradisalida = "0";
                                $radianexaasociado = "0";
                                $radiradirespuesta = "0";
                                $flagRta = true;
                                $isqlRta = "SELECT	R.RADI_NUME_RADI, A.RADI_NUME_SALIDA as ANEXRADISALIDA, B.RADI_PATH as ANEXRUTASALIDA,
        		                                          R.RADI_TIPO_DERI as TIPOANEXASOCIADO, R.RADI_NUME_DERI as RADIANEXASOCIADO, C.RADI_PATH as PATHANEXASOCIADO,
        		                                          R.RADI_RESPUESTA as RADIRADIRESPUESTA, D.RADI_PATH as PATHRADIRESPUESTA
                                                    FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = R.TDOC_CODI
        	                                               LEFT JOIN ANEXOS A ON A.ANEX_RADI_NUME=R.RADI_NUME_RADI AND A.ANEX_SALIDA=1
        	                                               LEFT JOIN RADICADO B ON B.RADI_NUME_RADI=A.RADI_NUME_SALIDA
        	                                               LEFT JOIN RADICADO C ON C.RADI_NUME_RADI=R.RADI_NUME_DERI
        	                                               LEFT JOIN RADICADO D ON D.RADI_NUME_RADI=R.RADI_RESPUESTA
                                                    WHERE R.RADI_NUME_RADI = '$record_id'";
                                $rsRta = $db->conn->Execute($isqlRta);
                                if ($rsRta && !$rsRta->EOF) {
                                    
                                    while ($rsRta && ! $rsRta->EOF) {
                                        $anexradisalida = ($rsRta->fields['ANEXRADISALIDA'] == null ? "0" : $rsRta->fields['ANEXRADISALIDA']);
                                        $radianexaasociado = ($rsRta->fields['RADIANEXASOCIADO'] == null ? "0" : $rsRta->fields['RADIANEXASOCIADO']);
                                        $radiradirespuesta = ($rsRta->fields['RADIRADIRESPUESTA'] == null ? "0" : $rsRta->fields['RADIRADIRESPUESTA']);
                                        
                                        if ( (substr($anexradisalida, -1) == '1') || (substr($radianexaasociado, -1) == '1') || (substr($radiradirespuesta, -1) == '1')) {
                                            $pasaFiltro = "Si";
                                            $flagRta = false;
                                            break;
                                        } 
                                        if ( (substr($anexradisalida, -1) == '7') || (substr($radianexaasociado, -1) == '7') || (substr($radiradirespuesta, -1) == '7')) {
                                            $pasaFiltro = "Si";
                                            $flagRta = false;
                                            break;
                                        }                                         
                                        
                                        $rsRta->MoveNext();
                                    }
                                    
                                    if ( $flagRta == true) {
                                        $pasaFiltro = "No";
                                        $flagRta .= $record_id . ", ";
                                    }
                                }
                            }       
                            
                        }
                        
                        if ($pasaFiltro == "Si") {
                            // ## CONSULTA DE EXPEDIENTES PARA EL RADICADO
                            $isqlCambio = "SELECT SGD_CAMBIO_TRD FROM RADICADO WHERE RADI_NUME_RADI = $record_id";
                            $rsCambio = $db->conn->Execute($isqlCambio);
                            $cambio = $rsCambio->fields['SGD_CAMBIO_TRD'];
                            if ($cambio > 0) {
                                $flagCambio .= $record_id . ", ";
                                $pasaFiltro = "No";
                                break;
                            }
                        }
                                  
                        if ($pasaFiltro == "Si") {
                            
                            if (substr($record_id, -1) == "1") {
                                $isqlEnvio = "SELECT SGD_RENV_CODIGO, SGD_RENV_FECH, RADI_NUME_SAL
                                          FROM SGD_RENV_REGENVIO
                                          WHERE RADI_NUME_SAL = $record_id";
                                $rsEnvio = $db->conn->Execute($isqlEnvio);
                                if ($rsEnvio && !$rsEnvio->EOF) {
                                    $codEnvio = $rsEnvio->fields['SGD_RENV_CODIGO'];
                                } else {
                                    $codEnvio = "";
                                }
                                
                                $isqlAnula = "SELECT SGD_ANU_ID, SGD_ANU_DESC, RADI_NUME_RADI
                                              FROM SGD_ANU_ANULADOS
                                              WHERE RADI_NUME_RADI = $record_id";
                                $rsAnula = $db->conn->Execute($isqlAnula);
                                if ($rsAnula && !$rsAnula->EOF) {
                                    $codAnula = $rsAnula->fields['RADI_NUME_RADI'];
                                } else {
                                    $codAnula = "";
                                }
                                
                                if ($codEnvio == "" && $codAnula == "") {
                                    $pasaFiltro = "No";
                                    $flagEnvio .= $record_id . ", ";
                                    break;
                                } else {
                                    $pasaFiltro = "Si";
                                }
                            }
                            
                            if (substr($record_id, -1) == "3") {
                                $isqlReasig = "SELECT HIST_FECH, RADI_NUME_RADI
                                          FROM HIST_EVENTOS
                                          WHERE SGD_TTR_CODIGO = 9 AND RADI_NUME_RADI = $record_id";
                                $rsReasig = $db->conn->Execute($isqlReasig);
                                if ($rsReasig && !$rsReasig->EOF) {
                                    $fechaHist = $rsReasig->fields['HIST_FECH'];
                                } else {
                                    $fechaHist = "";
                                }
                                
                                $isqlAnula = "SELECT SGD_ANU_ID, SGD_ANU_DESC, RADI_NUME_RADI
                                              FROM SGD_ANU_ANULADOS
                                              WHERE RADI_NUME_RADI = $record_id";
                                $rsAnula = $db->conn->Execute($isqlAnula);
                                if ($rsAnula && !$rsAnula->EOF) {
                                    $codAnula = $rsAnula->fields['RADI_NUME_RADI'];
                                } else {
                                    $codAnula = "";
                                }
                                
                                if ($fechaHist == "" && $codAnula == "") {
                                    $pasaFiltro = "No";
                                    $flagReasig .= $record_id . ", ";
                                    break;
                                } else {
                                    $pasaFiltro = "Si";
                                }
                            }
                        }
                        
                        /*$sqlPqr = "	SELECT	SGD_TPR_CODIGO
									FROM	SGD_TEMAS_TIPOSDOC
									WHERE	SGD_TPR_CODIGO = $tdoc";
                        $rsPqr = $db->conn->Execute($sqlPqr);
                        $pqr = $rsPqr->fields['SGD_TPR_CODIGO'];
                        
                        // ## VALIDA SI EL RADICADO ES -2 PQR Y NO TIENE RESPUESTA
                        if ((substr($record_id, - 1) == 2)) {     //and ($pqr != NULL)
                            // ## SE VALIDAN LAS SIGUIENTES CONDICIONES
                            // ## a. Solicitud tenga radicado un documento -1 por la pestana documentos y a ese radicado respuesta -1 tenga asociada la imagen.
                            // ## b. En la pestana de informacion general de la solicitud tenga asociado un radicado de salida.
                            // ## c. En la pestana documentos de la solicitud tenga anexo un archivo .pdf o .msg. y que estos no hallan sido anexados por el usuario 'RADICACIONWEB'
                            // ## d. En la pestana documentos tenga asociada una respuesta rapida.
                            
                            $isqlRta = "SELECT  A.RADI_NUME_SALIDA
										FROM 	ANEXOS A
												INNER JOIN RADICADO R ON
													R.RADI_NUME_RADI = A.RADI_NUME_SALIDA AND
													R.RADI_PATH LIKE '%pdf'
										WHERE	A.ANEX_RADI_NUME = $record_id";
                            $rsRta = $db->conn->Execute($isqlRta);
                            
                            // ## EL RADICADO PQR TIENE ANEXO RADICADO CON IMAGEN
                            if ($rsRta->fields["RADI_NUME_SALIDA"]) {
                                $pasaFiltro = "Si";
                            } else {
                                // ## EL RADICADO PQR NO TIENE ASOCIADO UN RADICADO -1
                                if (substr($asociado, - 1) != 1) {
                                    
                                    
                                    $isqlRta = "SELECT	A.ANEX_NOMB_ARCHIVO
												FROM	ANEXOS A
												WHERE	A.ANEX_CREADOR != 'RADICACIONWEB' AND
														A.ANEX_RADI_NUME = $record_id AND
														(A.ANEX_NOMB_ARCHIVO like '%pdf')";
                                    $rsRta = $db->conn->Execute($isqlRta);
                                    
                                    // ## EL RADICADO PQR TIENE UN ANEXO CON IMAGEN PDF o MSG
                                    if (! $rsRta->fields["ANEX_NOMB_ARCHIVO"]) {
                                        $pasaFiltro = "No";
                                        $flagRta .= $record_id . ", ";
                                    } // FIN -EL RADICADO PQR TIENE UN ANEXO CON IMAGEN PDF, TIF O MSG
                                } // FIN -EL RADICADO PQR TIENE ASOCIADO UN RADICADO -1
                            } // FIN -EL RADICADO PQR TIENE ANEXO RADICADO CON IMAGEN
                        } // FIN -VALIDA SI EL RADICADO ES -2 PQR Y NO TIENE RESPUESTA*/
                          
                        if ($pasaFiltro == "Si") {
                            // ## CONSULTA DE EXPEDIENTES PARA EL RADICADO
                            $isqlExp = "SELECT 	A.SGD_EXP_NUMERO as NumExpediente
    									FROM	SGD_EXP_EXPEDIENTE A JOIN SGD_SEXP_SECEXPEDIENTES S ON S.SGD_EXP_NUMERO = A.SGD_EXP_NUMERO  
    									WHERE	A.SGD_EXP_ESTADO <> 2 AND
    											A.RADI_NUME_RADI = $record_id 
                                                and S.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas')";
                            $rsExp = $db->conn->Execute($isqlExp);
                            $expNumero = $rsExp->fields['NumExpediente'];
                            
                            // ## VALIDA SI EL RADICADO ESTA INCLUIDO EN EXPEDIENTE
                            if (empty($expNumero)) {
                                // Variable que imprime los radicados sin expediente
                                $setFiltroSinEXP .= empty($setFiltroSinEXP) ? "$record_id.<br/>" : ",$record_id<br/>";
                                $flagExp .= $record_id . ", ";
                                $pasaFiltro = "No";
                                break;
                            }
                        }
                        
                        // ## CONSULTA LOS RAD. ANEXOS, QUE SE PUEDAN ARCHIVAR JUNTO AL PADRE.
                        // ## LOS ANEXOS DEBEN TENER IMAGEN ASOCIADA Y TRD ASIGNADA.
                        // ## SI NO TIENEN EXP SE INCLUIRAN EN EL MISMO DEL RADICADO PADRE.
                        if ($pasaFiltro == "Si") {
                            $queryAnex = "SELECT	A.RADI_NUME_SALIDA AS RADI
										FROM	ANEXOS A
												INNER JOIN RADICADO R ON
													A.RADI_NUME_SALIDA = R.RADI_NUME_RADI AND
													R.RADI_DEPE_ACTU = $depeCodi AND
													R.RADI_USUA_ACTU = $codusuario
												INNER JOIN HIST_EVENTOS H ON
													H.RADI_NUME_RADI = A.RADI_NUME_SALIDA AND
													H.SGD_TTR_CODIGO IN (22,23,42)
												INNER JOIN SGD_RDF_RETDOCF T ON
													A.RADI_NUME_SALIDA = T.RADI_NUME_RADI
										WHERE	A.ANEX_RADI_NUME = $record_id AND
												A.RADI_NUME_SALIDA != 0 AND
												A.RADI_NUME_SALIDA != ANEX_RADI_NUME";
                            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                            $rsQueryAnex = $db->conn->Execute($queryAnex);
                            
                            while ($rsQueryAnex && ! $rsQueryAnex->EOF) {
                                $anexArch = $rsQueryAnex->fields['RADI'];
                                $rsQueryAnex->MoveNext();
                                $whereAnexos .= ' b.radi_nume_radi = ' . $anexArch . ' or';
                            }
                        } // FIN -CONSULTA LOS RAD. ANEXOS, QUE SE PUEDAN ARCHIVAR JUNTO AL PADRE.
                    } // FIN -EL RADICADO ES EL ORIGINAL, NO ES UN DERIVADO
                    $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                } // FIN -EL ANO DEL RADICADO ES SUPERIOR A 2006
                $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                
                break;
            // # FIN -ACCION DE ARCHIVAR
            // ##################################################################
            
            // ##################################################################
            // ## ACCION DE NRR
            case 16:
                $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                /**
                 * Modificaciones Febrero de 2007, por SSPD para el DNP
                 * Archivar:
                 * Se verifica si el radicado se encuentra o no en un expediente,
                 * si es negativa la verificacion, ese radicado no se puede archivar
                 */
                include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
                $db = new ConnectionHandler(ORFEOPATH);
                break;
            // ## ACCION DE NRR
            // ##################################################################
            
            default:
                $whereFiltro .= ' b.radi_nume_radi = ' . $record_id . ' or';
                break;
        }
    }
    
    // Logica para validar VoBo envio FAX
    include $ruta_raiz . '/include/class/DatoContacto.php';
    $mensaje_error_VoBoFax = "";
    if (is_array($_POST['checkValue']) && $codTx == 13) {
        $flagVoBoFax = true;
        $radsMalos = Array();
        $objDir = new DatoContacto($db->conn);
        foreach ($_POST['checkValue'] as $i => $val) {
            $valida = $objDir->verificaVoBoEnvioFax($i);
            if (! $valida) {
                $radsMalos[] = $i;
                $flagVoBoFax = false;
            }
        }
        
        if (! $flagVoBoFax) {
            $mensaje_error_VoBoFax = "< " . implode(",", $radsMalos) . "> <BR> FALTA ENV&Iacute;O DEL F&Iacute;SICO AL GRUPO DE CORRESPONDENCIA";
        }
    }
    
    // ##########################################################################
    // ## MENSAJES DE FILTROS QUE NO PEMITEN REALIZAR LAS ACCIONES
    
    
    
    if ($pasaFiltro == "No")
        $msgError = "NO SE PERMITE ESTA OPERACI&Oacute;N PARA LOS SIGUIENTES RADICADOS:";
    
    if ($flagUsInac)
        $msgError .= "<BR><BR> $flagUsInac PORQUE EL USUARIO DESTINO NO ESTA ACTIVO";
    
    if ($flagUsIgual)
        $msgError .= "<BR><BR> $flagUsIgual PORQUE EL USUARIO DESTINO ES EL MISMO USUARIO ACTUAL";
    
    if ($flagDeri)
        $msgError = "NO SE PERMITE ESTA OPERACI&Oacute;N PORQUE ES UN DERIVADO, REASIGNE O ARCHIVE";
    
    if ($flagTRD)
        $msgError .= "<BR><BR> $flagTRD PORQUE NO TIENEN CLASIFICACI&Oacute;N TRD";
    
    if ($flagImg)
        $msgError .= "<BR><BR> $flagImg PORQUE NO TIENEN IMAGEN ASOCIADA";
    
    if ($flagAnexTrd)
        $msgError .= "<BR><BR> $flagAnexTrd PORQUE ESTOS ANEXOS NO TIENEN TRD O IMAGEN";
    
    if ($flagRta == true) 
        $msgError .= "<BR><BR> $flagRta PORQUE NO TIENEN RESPUESTA ASOCIADA";
    
    if ($flagExp)
        $msgError .= "<BR><BR> $flagExp PORQUE NO EST&Aacute;N INCLUIDOS EN UN EXPEDIENTE";
    
    if ($flagCambio)
        $msgError .= "<BR><BR> $flagCambio PORQUE ESTA PENDIENTE DE CAMBIO DE TRD";
    
    if ($flagEnvio) {
        $msgError .= "<BR><BR>  $flagEnvio PORQUE NO TIENE DATOS DE ENVIO O ANULACI0N ";
    }
    
    if ($flagReasig) {
        $msgError .= "<BR><BR>  $flagReasig PORQUE NO SE ENCUENTRA REASIGNADO O ANULADO ";
    }
    
    if (substr($whereFiltro, - 2) == "or")
        $whereFiltro = substr($whereFiltro, 0, strlen($whereFiltro) - 2);
    
    if (trim($whereFiltro))
        $whereFiltro = "and ( $whereFiltro ) ";
    
    if (substr($whereAnexos, - 2) == "or")
        $whereAnexos = substr($whereAnexos, 0, strlen($whereAnexos) - 2);
    
    if (trim($whereAnexos))
        $whereAnexos = "and ( $whereAnexos ) ";
    
    if ($codTx == 13) {
        
        $sqlRadPriv = "	SELECT	DISTINCT b.RADI_NUME_RADI
						FROM	RADICADO b
						WHERE	b.SGD_SPUB_CODIGO > 0
								$whereFiltro ";
        $rsRadPriv = $db->conn->Execute($sqlRadPriv);
        
        while ($rsRadPriv && ! $rsRadPriv->EOF) {
            
            $Priv = $rsRadPriv->fields['RADI_NUME_RADI'];
            
            // ## CONSULTA PARA SABER QUIEN PRIVATIZO EL RADICADO Y QUIEN LO PUEDE DESPRIVATIZAR
            $sqlPriv = "SELECT	R.SGD_SPUB_CODIGO,
								R.RADI_USUA_PRIVADO,
								U.DEPE_CODI
						FROM	RADICADO R
								JOIN USUARIO U ON U.USUA_DOC = R.RADI_USUA_PRIVADO
						WHERE	RADI_NUME_RADI = $Priv";
            $rsPriv = $db->conn->Execute($sqlPriv);
            
            $estado = $rsPriv->fields['SGD_SPUB_CODIGO']; // 0-Publico 1-Privado para la Dependencia 2-Privado para el usuario
            $privatizo = $rsPriv->fields['RADI_USUA_PRIVADO'];
            $depPriv = $rsPriv->fields['DEPE_CODI'];
            
            if ($estado == 2 && $_SESSION['usua_doc'] == $privatizo) { // El radicado es privado para el usuario y solo el que privatizo puede desprivatizar
                $radPriv = $rsRadPriv->fields['RADI_NUME_RADI'] . ",";
            } elseif ($estado == 1 && $_SESSION['dependencia'] == $depPriv) { // El radicado es privado para la dependencia y solo usuarios de la misma dependencia que privatizo, pueden desprivatizar.
                $radPriv = $rsRadPriv->fields['RADI_NUME_RADI'] . ",";
            }
            
            $rsRadPriv->MoveNext();
        }
        
        if (substr($radPriv, - 1) == ",") {
            $radPriv = substr($radPriv, 0, strlen($radPriv) - 1);
        }
    }
} else {
    $mensaje_error = "NO HAY REGISTROS SELECCIONADOS";
}
?>

<html>
<head>
<title>Enviar Datos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css"
	href="<?=$ruta_raiz;?>/extjs/resources/css/ext-all.css">
<script src="<?=$ruta_raiz;?>/extjs/adapter/ext/ext-base.js"></script>
<script src="<?=$ruta_raiz;?>/extjs/ext-all.js"></script>
<link rel="stylesheet" href="<?=$ruta_raiz;?>/estilos/orfeo.css">
<script language="javascript">

			function notSupported() 
			{
				alert('Su browser no soporta las funciones Javascript de esta pagina.');
			}

			function setSel(start,end) 
			{
				document.realizarTx.observa.focus();
				var t=document.realizarTx.observa;
				if(t.setSelectionRange){
					t.setSelectionRange(start,end);
					t.focus();
					//f.t.value = t.value.substr(t.selectionStart,t.selectionEnd-t.selectionStart);
				}
				else 
					notSupported();
			}

			function valMaxChars(maxchars) 
			{
				document.realizarTx.observa.focus();
				if(document.realizarTx.observa.value.length > maxchars) {
					/*  alert('Demasiados caracteres en el texto ! Por favor borre '+
					(document.realizarTx.observa.value.length - maxchars)+ ' caracteres pues solo se permiten '+ maxchars);*/
					alert('Demasiados caracteres en el texto, solo se permiten '+ maxchars);
					setSel(maxchars,document.realizarTx.observa.value.length);
					return false;
				}
				else
					return true;
			}
	
			 /* OPERACIONES EN JAVASCRIPT
				* @marcados Esta variable almacena el numeo de chaeck seleccionados.
				* @document.realizarTx  Este subNombre de variable me indica el formulario principal del listado generado.
				* @tipoAnulacion Define si es una solicitud de anulacion  o la Anulacion Final del Radicado.
				* Funciones o Metodos EN JAVA SCRIPT
				* Anular()  Anula o solicita esta dependiendo del tipo de anulacin.
				* Previamente verifica que este seleccionado algun  radicado.
				* markAll() Marca o desmarca los check de la pagina. */
	
			function Anular(tipoAnulacion) 
			{
				marcados = 0;
				for(i=0;i<document.realizarTx.elements.length;i++) {
					if(document.realizarTx.elements[i].checked==1 ){
						marcados++;
					}
				}
				<?php
    if ($codusuario == 1 || $usuario_reasignacion == 1) {
        echo "if(document.realizarTx.chkNivel.checked==1)	 marcados = marcados -1 \n";
    }
    ?>
				if(marcados>=1) {
					return 1;
				} 
				else {
					alert("Debe marcar un elemento");
					return 0;
				}
			}

			function markAll(noRad) 
			{
				if(document.realizarTx.elements.check_titulo.checked || noRad >=1) {
					for(i=3;i<document.realizarTx.elements.length;i++) {
						document.realizarTx.elements[i].checked=1;
					}
				}
				else {
					for(i=3;i<document.realizarTx.elements.length;i++) {
						document.realizarTx.elements[i].checked=0;
					}
				}
			}

			function okTx() 
			{
				valCheck = Anular(0);
				if(valCheck==0) 
					return 0;
				if ((document.realizarTx.usCodSelect != undefined) && (document.realizarTx.usCodSelect.selectedIndex == -1)){
					alert("Atencion: Seleccione al menos un destinatario");
					return 0;
				};
				numCaracteres = document.realizarTx.observa.value.length;
				if(numCaracteres>=6) {
					//alert(document.realizarTx.usCodSelect.select);
					if (valMaxChars(550)) {
						<?php
    if ($radPriv && $codTx == 13) {
        ?>

								confirmar = confirm("Los siguientes Radicado (s) que desea archivar estan marcados como privados: , si desea quitar la marca de privacidad por favor de clic en ACEPTAR, de lo contrario de clic en CANCELAR");
								if (confirmar){
									document.realizarTx.desprivatiza.value = 1;
									document.realizarTx.submit();
								}
								else{
									document.realizarTx.submit();
								}
						
						<?php
    } else {
        ?>
								document.realizarTx.submit();
						<?php
    }
    ?>
					}
				}
				else {
					alert("Atencion: El numero de Caracteres minimo el la Observacion es de 6. (Digito :"+numCaracteres+")");
				}
			}

		</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="markAll(1);">
		<?php
// ## SE IMPRIMEN LOS MENSAJES DE ERROR
if ($msgError || $mensaje_error_VoBoFax) {
    DIE("<center>
						<table class='borde_tab' width=100% CELSPACING=5>
							<tr class=titulosError>
								<td align='center'>
									$msgError <br> 
									$mensaje_error_VoBoFax
								</td>
							</tr>
						</table>
					</CENTER>");
} else {
    ?>
		<table border=0 width=100% cellpadding="0" cellspacing="0">
		<tr>
			<td width=100%><br>
				<form action='realizarTx.php?<?=$encabezado?>' method=post
					name="realizarTx" id="realizarTx">
					<input type="hidden" name="desprivatiza" id="desprivatiza" value=0>
					<input type='hidden' name="depsel8"
						value='<?=implode($depsel8,',')?>'> <input type='hidden'
						name="codTx" value='<?=$codTx?>'> <input type='hidden'
						name="EnviaraV" value='<?=$EnviaraV?>'> <input type='hidden'
						name="fechaAgenda" value='<?=$fechaAgenda?>'> <input type='hidden'
						name="$record_id" value='<?=$record_id?>'>
					<table width="98%" border="0" cellpadding="0" cellspacing="5"
						class="borde_tab">
						<tr>
							<TD width="30%" class="titulos4">USUARIO:<br>
							<br><?=$_SESSION['usua_nomb']?> </TD>
							<TD width='30%' class="titulos4">DEPENDENCIA:<br>
							<br><?=$_SESSION['depe_nomb']?><br></TD>
							<td class="titulos4">
									<?php
    switch ($codTx) {
        case 7:
            print "Borrar Informados ";
            echo "<input type='hidden' name='info_doc' value='" . $tmp_arr_id . "'>";
            break;
        case 8:
            $usDefault = 1;
            $cad = $db->conn->Concat("RTRIM(USD.depe_codi)", "'-'", "RTRIM(u.usua_codi)");
            $cad2 = $db->conn->Concat($db->conn->IfNull("d.DEP_SIGLA", "'N.N.'"), "'-'", "RTRIM(u.usua_nomb)");
            $sql = "Select	$cad2 as usua_nomb,
																				$cad as usua_codi
																From		usuario u,	
																				dependencia d, 
																				SGD_USD_USUADEPE USD
																Where		USD.depe_codi in(" . implode($depsel8, ',') . ")
																				$whereReasignar and
																				u.USUA_ESTA = 1 and
																				USD.depe_codi = d.depe_codi AND
																				u.USUA_LOGIN = USD.USUA_LOGIN AND
																				u.USUA_DOC = USD.USUA_DOC
																Order By usua_nomb";
            $rs = $db->conn->Execute($sql);
            $usuario = $codUsuario;
            print "Informados";
            print $rs->GetMenu2('usCodSelect[]', $usDefault, false, true, 10, " id='usCodSelect' class='select' ");
            break;
        case 9:
            if (in_array($dependencia, $depsel)) {
                $usDefault = $codusuario;
            }
            $sql = "SELECT	DEPENDENCIA_OBSERVA, 
																DEPENDENCIA_VISIBLE 
														FROM	DEPENDENCIA_VISIBILIDAD
														WHERE	DEPENDENCIA_OBSERVA=$dependencia";
            $rs1 = $db->conn->Execute($sql);
            $usuario_publico = "";
            if (! $rs1->EOF) {
                $usuario_publico_dep = "";
                while (! $rs1->EOF) {
                    if (in_array($rs1->fields["DEPENDENCIA_VISIBLE"], $depsel)) {
                        $usuario_publico_dep = $usuario_publico_dep . $rs1->fields["DEPENDENCIA_VISIBLE"] . ",";
                    }
                    $rs1->MoveNext();
                }
                if ($usuario_publico_dep != "") {
                    $usuario_publico = "or   
															 (u.USUA_LOGIN = USD.USUA_LOGIN AND 
																u.USUA_DOC = USD.USUA_DOC AND
																USD.DEPE_CODI in (" . $usuario_publico_dep;
                    $usuario_publico = substr($usuario_publico, 0, strlen($usuario_publico) - 1) . ")
															AND u.USUARIO_PUBLICO = 1 AND U.USUA_ESTA = 1)";
                }
                ;
            }
            if ($EnviaraV != "VoBo") {
                $depselNoMisma = array_diff($depsel, array(
                    $dependencia
                ));
                if (count($depselNoMisma) > 0) {
                    $whereReasignar = "	and ((u.sgd_rol_codigo = 1 or u.sgd_rol_codigo = 2) 
																			and USD.depe_codi in(" . join(",", $depselNoMisma) . "))";
                }
            }
            if ((in_array($dependencia, $depsel) && ($codusuario != 1 || $usuario_reasignacion != 1) && $EnviaraV == "VoBo")) {
                $whereReasignar = "	and ((u.sgd_rol_codigo = 1 or u.sgd_rol_codigo = 2)
																		or u.usuario_publico = 1  or USD.depe_codi = $dependencia)";
            }
            if (($_POST['rolUsuario'] == 1 || $usuario_reasignacion == 1) && in_array($dependencia, $depsel) && $EnviaraV == "VoBo") {
                if ($objDep->Dependencia_codigo($dependencia)) {
                    $depPadre = $objDep->getDepe_codi_padre();
                }
                print("La dependencia  padre ...($depPadre)");
                $whereDep = " AND USD.depe_codi=$depPadre 
																				AND (u.sgd_rol_codigo = 1 or u.sgd_rol_codigo = 2) ";
                $depsel[] = $depPadre;
            }
            if ($EnviaraV == "VoBo") {
                $proccarp = "Visto Bueno";
            }
            $whereDep = "";
            if (in_array($dependencia, $depsel)) {
                $where = "(USD.depe_codi = $dependencia and 
																u.USUA_ESTA = 1 AND
																u.USUA_DOC = USD.USUA_DOC AND
																u.USUA_LOGIN = USD.USUA_LOGIN)";
                if (($whereReasignar == "") && ($usuario_publico == "")) {
                    $whereDep = " and " . $where;
                } else {
                    $whereDep = " or " . $where;
                }
            }
            $cad = $db->conn->Concat("RTRIM(USD.depe_codi)", "'-'", "RTRIM(u.usua_codi)");
            $sql = "Select 	U.USUA_NOMB, 
																$cad AS USUA_COD, 
																USD.DEPE_CODI,
																CASE WHEN U.SGD_ROL_CODIGO = 1 THEN 'JEFE'
																	 WHEN U.SGD_ROL_CODIGO = 2 THEN 'JEFE-ENCARGADO'
																	 WHEN U.SGD_ROL_CODIGO = 3 THEN 'AUDITOR' 
																	 ELSE 'NORMAL' END AS ROL 
														From	USUARIO U, 
																SGD_USD_USUADEPE USD
														Where 	(U.USUA_ESTA = 1 AND 
																U.USUA_DOC = USD.USUA_DOC AND
																U.USUA_LOGIN = USD.USUA_LOGIN)
																$whereReasignar
																$whereDep
																$usuario_publico
														ORDER BY USD.depe_codi, USUA_NOMB";
            $rs = $db->conn->Execute($sql);
            $usuario = $codUsuario;
            ?>
												<select name="usCodSelect[]" class="select" id="usCodSelect"
								multiple="multiple">
												<?php
            while (! $rs->EOF) {
                $depCodiP = $rs->fields["DEPE_CODI"];
                $usuNombP = $rs->fields["USUA_NOMB"];
                $usuCodiP = $rs->fields["USUA_COD"];
                $rol = $rs->fields["ROL"];
                $valOptionP = "";
                $valOptionP = $usuNombP;
                $class = "";
                if (True) {
                    $sql = "SELECT	DEPE_NOMB
																	FROM	dependencia
																	WHERE	depe_codi=$depCodiP";
                    $rs2 = $db->conn->Execute($sql);
                    $depNombP = $rs2->fields["DEPE_NOMB"];
                    // $valOptionP .= " [ ".$depNombP."] [".$usuCodiP."]";
                    $valOptionP .= " [ " . $depNombP . "] [" . $rol . "]";
                    $class = " class='leidos'";
                }
                if ($RecordCount == 1) {
                    ?>
															<option selected <?=$class?> value="<?=$usuCodiP?>"><?=$valOptionP?></option>
															<?php
                } else {
                    ?>
															<option <?=$class?> value="<?=$usuCodiP?>"><?=$valOptionP?></option>
															<?php
                }
                $rs->MoveNext();
            }
            ?>
												</select>
												<?php
            print "Reasignar $proccarp";
        case 10:
            $carpetaTipo = substr($carpSel, 1, 1);
            $carpetaCodigo = intval(substr($carpSel, - 3));
            if ($carpetaTipo == 1) {
                $sql = "Select	NOMB_CARP as carp_desc
															From	CARPETA_PER
															Where	codi_carp=$carpetaCodigo and
																	usua_codi=$codusuario and
																	depe_codi=$dependencia";
            } else {
                $sql = "Select	carp_desc 
															From	carpeta 
															where	carp_codi=$carpetaCodigo";
            }
            $rs = $db->conn->Execute($sql); // Ejecuta la busqueda y obtiene el recordset vacio
            $carpetaNombre = $rs->fields['carp_desc'];
            print "Movimiento a Carpeta <b>$carpetaNombre</b>
													<input type=hidden name='carpetaCodigo' value='$carpetaCodigo'>
													<input type=hidden name='carpetaTipo' value='$carpetaTipo'>
													<input type=hidden name='carpetaNombre' value='$carpetaNombre'>";
            break;
        case 12:
            print "Devolver documentos a Usuario Anterior ";
            break;
        case 13:
            print "Archivo de Documentos";
            break;
        case 16:
            print "Archivo de NRR";
            break;
    }
    ?>
									<br>
							</td>
							<td width='5' class="grisCCCCCC"><input type="button"
								value="REALIZAR" onClick="okTx();" name="enviardoc"
								align="bottom" class="botones" id="REALIZAR"></td>
						</tr>
						<tr align="center">
							<td colspan="4" class="celdaGris" align=center><br>
									<?php
    if (($codusuario == 1) || ($usuario_reasignacion == 1)) {
        ?>
										<input type="checkbox" name="chkNivel" checked class="ebutton">
								<span class="info">El documento tomara el nivel del usuario
									destino.</span><br>
										<?php
    }
    ?>
									<center>
									<table width="500" border=0 align="center" bgcolor="White">
										<tr bgcolor="White">
											<td width="100">
												<center>
													<img src="<?=$ruta_raiz?>/iconos/tuxTx.gif"
														alt="Tux Transaccion" title="Tux Transaccion">
												</center>
											</td>
										</tr>
										<tr align="left">
											<td><span class="etextomenu"> </span> 
												<textarea id="observa" name="observa" maxlength="500" cols="70" rows="3" class="ecajasfecha"></textarea>
											</td>
										</tr>
										<tr>
											<td><input type="hidden" name="enviar" value="enviarsi"> <input
												type="hidden" name="enviara" value='9'> <input type="hidden"
												name="carpeta" value="12"> <input type="hidden"
												name="carpper" value="10001"></td>
										</tr>
									</table>
									<br>
								</center>
									<?php
    /*
     * GENERACION LISTADO DE RADICADOS
     * Aqui utilizamos la clase adodb para generar el listado de los radicados
     * Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
     * el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
     */
    if (! $orderNo)
        $orderNo = 0;
    $order = $orderNo + 1;
    $sqlFecha = $db->conn->SQLDate("d-m-Y H:i A", "b.RADI_FECH_RADI");
    include_once ORFEOPATH . "include/query/tx/queryFormEnvio.php";
    switch ($codTx) {
        case 12:
            $isql = str_replace("Enviado Por", "Devolver a", $isql);
            break;
        default:
            break;
    }
    // ## PARA PINTAR LOS RADICADOS SELECCIONADOS
    //echo $isql;
    $pager = new ADODB_Paginacion($db, $isql, 'adodb', true, $orderNo, $orderTipo);
    $pager->toRefLinks = $linkPagina;
    $pager->toRefVars = $encabezado;
    $pager->checkAll = true;
    $pager->checkTitulo = true;
    $pager->Render(500, $linkPagina);
    
    // ## PARA PINTAR LOS ANEXOS DE LOS RADICADOS
    if ($codTx == 13 && $whereAnexos) {
        ?>
									
											<br> <b> <span class="info">A continuaci&oacute;n se listan
										los radicados anexos</span>
							</b> <br>
									
									<?php
									
        $pager = new ADODB_Paginacion($db, $asql, 'adodb', true, $orderNo, $orderTipo);
        $pager->toRefLinks = $linkPagina;
        $pager->toRefVars = $encabezado;
        $pager->checkAll = true;
        $pager->checkTitulo = true;
        $pager->Render(500, $linkPagina);
    }
    ?>
									<input type='hidden' name=depsel value='<?=$depsel?>'></td>
						</tr>
					</table>
				</form></td>
		</tr>
	</table>
	<script language="javascript">
		<?php
    //echo "var myNoRaiz = " . Javascript::valueToJavascript($_POST['noraiz']);
    ?>
    		var myNoRaiz = <?php echo $_POST['noraiz'] ?>;
			noraiz = Ext.DomHelper.append(document.realizarTx, {tag:'input',style:'visibility:hidden',id:'noraiz',name:'noraiz',value:myNoRaiz});
		</script>
		<?php
}
?>
	</body>
</html>