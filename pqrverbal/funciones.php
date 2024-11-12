<?php

/**
 * 
 * Retorna un vector con todos los archivos anexos que cumplen un $patron dado.
 * @param string $dir  Ruta a la carpeta donde se almacenan los temporales
 * @param string $patron Patron en nombres de archivos
 * @return array $result
 */
function find_all_files($dir, $patron) {
    $root = scandir($dir);
    foreach ($root as $value) {
        if ($value === '.' || $value === '..') {
            continue;
        }
        if ((substr($value, 0, 14) == $patron) && is_file("$dir/$value")) {
            $result[] = "$dir/$value";
            continue;
        }
    }
    return $result;
}

/**
 * Funcion para comprobar una direccion de correo.
 *
 * @param   string $email Correo electronico.
 * @return  boolean
 * @access private
 */
function comprobar_email($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 
 * Enter description here ...
 * @param string $usrRadicador
 * @param int $TipoTercero
 * @param string $NombreTercero
 * @param string $ApellidoTercero
 * @param string $DireccionTercero
 * @param string $TelefonoTercero
 * @param string $CorreoElectronicoTercero
 * @param string $Internacionalizacion
 * @param int $TipoSolicitud
 * @param unknown_type $Tema
 * @param string $AsuntoRadicado
 * @param unknown_type $FechaOficioRadicado
 * @param unknown_type $MedioRecepcion
 * @param string $CodigoPostal
 * @return string|Ambigous <string, multitype:string >|boolean
 */
function CrearRadicado($usrRadicador, $TipoTercero, $NombreTercero, $ApellidoTercero, $TipoDocumento, $DocumentoTercero, $DireccionTercero, $TelefonoTercero, $CorreoElectronicoTercero, $Internacionalizacion, $TipoSolicitud = 0, $Tema, $AsuntoRadicado, $FechaOficioRadicado, $MedioRecepcion, $CodigoPostal, $depe_dest, $usua_dest) {

    $validaOk = true;
    //1a validacion. Que vengan datos basicos.
    if (empty($usrRadicador) || empty($TipoTercero) || empty($NombreTercero) || empty($ApellidoTercero) ||
            empty($DocumentoTercero) || empty($TipoSolicitud) || empty($AsuntoRadicado)
    ) {
        $cadError[] = "Faltan datos básicos.";
        return $cadError;
    } else {   //2a validacion. Que los datos brindados sean validos en tipologia (y longitud).
        $cadError = array();

        //Validamos codigo de aplicacion interfaz
        //Validacion respuesta virtual y correo electronico
        if ($_POST['tipoResp'] == 'V') {
            if (comprobar_email($CorreoElectronicoTercero)) {
                $email = $CorreoElectronicoTercero;
            } else {
                $cadError[] = 'Formato correo electronico erroneo.';
                $validaOk = false;
            }
        }

        //Validacion respuesta fisica y direccion
        if ($_POST['tipoResp'] == 'F') {
            if (empty($DireccionTercero)) {
                $cadError[] = 'Falta Dirección.';
                $validaOk = false;
            }

            if (is_null($Internacionalizacion)) {
                $cadError[] = 'Falta $Internacionalizacion.';
                $validaOk = false;
            }
        }

        if ($Internacionalizacion) {
            $InterNal = explode('-', $Internacionalizacion);
            if (count($InterNal) != 4) {
                $cadError[] = 'Internacionalizacion debe ser del tipo IdContinente-IdPAis-IdDpto-IdMcpio.';
                $validaOk = false;
            } else {
                $idCont = $InterNal[0];
                $idPais = $InterNal[1];
                $idDpto = $InterNal[2];
                $idMpio = $InterNal[3];
            }
        }

        //Validamos tipo tercero.
        if (!is_numeric($TipoTercero)) {
            $cadError[] = 'Tipo tercero debe ser numerico.';
            $validaOk = false;
        } else {
            $sgd_ciu_codigo = 'null';
            $sgd_oem_codigo = 'null';
            $sgd_esp_codigo = 'null';
            $sgd_fun_codigo = 'null';
            switch ($TipoTercero) {
                //USUARIO
                case 1: {
                        $Tercero = 0;
                        $sgdTrd = 1;
                        $datos_t = array();
                        $datos_t['TDID_CODI'] = $TipoDocumento;
                        $datos_t['SGD_CIU_NOMBRE'] = isset($NombreTercero) ? ((strlen(trim($NombreTercero)) > 80) ? substr(trim($NombreTercero), 0, 79) : trim($NombreTercero) ) : "";
                        $datos_t['SGD_CIU_DIRECCION'] = isset($DireccionTercero) ? ((strlen(trim($DireccionTercero)) > 100) ? substr(trim($DireccionTercero), 0, 99) : trim($DireccionTercero)) : "";
                        $datos_t['ID_CONT'] = isset($idCont) ? $idCont : null;
                        $datos_t['ID_PAIS'] = isset($idPais) ? $idPais : null;
                        $datos_t['MUNI_CODI'] = isset($idMpio) ? $idMpio : null;
                        $datos_t['DPTO_CODI'] = isset($idDpto) ? $idDpto : null;
                        $datos_t['SGD_CIU_APELL1'] = isset($ApellidoTercero) ? ( (strlen(trim($ApellidoTercero)) > 50) ? substr(trim($ApellidoTercero), 0, 49) : trim($ApellidoTercero) ) : "";
                        $datos_t['SGD_CIU_EMAIL'] = isset($CorreoElectronicoTercero) ? ((strlen(trim($CorreoElectronicoTercero)) > 99) ? substr(trim($CorreoElectronicoTercero), 0, 99) : trim($CorreoElectronicoTercero)) : "";
                        $datos_t['SGD_CIU_CEDULA'] = isset($DocumentoTercero) ? ((strlen(trim($DocumentoTercero)) > 13) ? substr(trim($DocumentoTercero), 0, 12) : trim($DocumentoTercero)) : "";
                        $datos_t['SGD_CIU_CODPOSTAL'] = (strlen(trim($CodigoPostal)) > 8) ? substr(trim($CodigoPostal), 0, 7) : trim($CodigoPostal);
                    }break;
                //ENTIDADES
                case 2: {
                        $Tercero = 1;
                        $sgdTrd = 3;
                        $cadError[] = 'Logica no implementada para tipo tercero entidad.';
                        $validaOk = false;
                    }break;
                //EMPRESAS
                case 3: {
                        $Tercero = 2;
                        $sgdTrd = 2;
                        $datos_t = array();
                        $datos_t['TDID_CODI'] = $TipoDocumento;
                        $datos_t['SGD_OEM_OEMPRESA'] = isset($NombreTercero) ? ((strlen(trim($NombreTercero)) > 80) ? substr(trim($NombreTercero), 0, 79) : trim($NombreTercero) ) : "";
                        $datos_t['SGD_OEM_DIRECCION'] = isset($DireccionTercero) ? ((strlen(trim($DireccionTercero)) > 100) ? substr(trim($DireccionTercero), 0, 99) : trim($DireccionTercero)) : "";
                        $datos_t['ID_CONT'] = isset($idCont) ? $idCont : null;
                        $datos_t['ID_PAIS'] = isset($idPais) ? $idPais : null;
                        $datos_t['MUNI_CODI'] = isset($idMpio) ? $idMpio : null;
                        $datos_t['DPTO_CODI'] = isset($idDpto) ? $idDpto : null;
                        $datos_t['SGD_OEM_REP_LEGAL'] = isset($ApellidoTercero) ? ( (strlen(trim($ApellidoTercero)) > 50) ? substr(trim($ApellidoTercero), 0, 49) : trim($ApellidoTercero) ) : "";
                        $datos_t['EMAIL'] = isset($CorreoElectronicoTercero) ? ((strlen(trim($CorreoElectronicoTercero)) > 99) ? substr(trim($CorreoElectronicoTercero), 0, 99) : trim($CorreoElectronicoTercero)) : "";
                        $datos_t['SGD_OEM_NIT'] = isset($DocumentoTercero) ? ((strlen(trim($DocumentoTercero)) > 13) ? substr(trim($DocumentoTercero), 0, 12) : trim($DocumentoTercero)) : "";
                        $datos_t['SGD_OEM_CODPOSTAL'] = (strlen(trim($CodigoPostal)) > 8) ? substr(trim($CodigoPostal), 0, 7) : trim($CodigoPostal);
                    }break;
                //FUNCIONARIOS
                case 4: {
                        $Tercero = 6;
                        $sgdTrd = 4;
                        $cadError[] = 'Logica no implementada para tipo tercero funcionarios.';
                        $validaOk = false;
                    }break;
                default: {
                        $cadError[] = 'Tipo tercero no valido.';
                        $validaOk = false;
                    }break;
            }
        }
        //Validamos datos en BD previos a radicar.
        require "../config.php";
        define('ADODB_ASSOC_CASE', 1);
        require "adodb/adodb.inc.php";
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
        $conn = NewADOConnection($dsn);
        ///// Hasta aqui validamos tipologia de los OBLIGATORIOS /////
        //Validamos fecha de oficio
        if (empty($FechaOficioRadicado)) {
            $fechaRadicado = date('d-m-Y H:i:s');
            $fechaRadicado = $conn->DBTimeStamp($fechaRadicado);
        }
        else
            $fechaRadicado = $FechaOficioRadicado;

        if ($conn === false) {
            $cadError[] = 'Error de Conexion a BD.';
            $validaOk = false;
        }

        //b. validamos el usuario radicador.
        $sql = "select u.usua_codi, u.depe_codi, u.usua_esta, u.usua_prad_tp2, u.codi_nivel, u.usua_doc, d.depe_rad_tp2
		                from usuario u
		                    inner join dependencia d on u.depe_codi=d.depe_codi
		                where usua_login='$usrRadicador'";
        $rs = $conn->Execute($sql);
        $ADODB_COUNTRECS = false;
        if ($rs === false) {
            $cadError[] = 'Error en la consulta de usuarios.';
            $validaOk = false;
        }
        if ($rs->RecordCount() == 0) {
            $cadError[] = 'Usuario radicador inexistente.';
            $validaOk = false;
        } else {
            if ($rs->fields['USUA_ESTA'] == 0) {
                $cadError[] = 'Usuario radicador inactivo.';
                $validaOk = false;
            }
            if ($rs->fields['USUA_PRAD_TP2'] == 0) {
                $cadError[] = 'Usuario radicador no tiene permisos para radicacion de entrada.';
                $validaOk = false;
            }

            if (is_null($rs->fields['DEPE_RAD_TP2'])) {
                $cadError[] = 'Usuario radicador no tiene configurada la dependencia para radicacion de entrada.';
                $validaOk = false;
            }

            $depe_radi = $rs->fields['DEPE_CODI'];
            $usua_radi = $rs->fields['USUA_CODI'];
            $usua_docu = $rs->fields['USUA_DOC'];
            $usua_nivel = $rs->fields['CODI_NIVEL'];
            $depe_radi_entrada = $rs->fields['DEPE_RAD_TP2'];
        }

        //b. validamos el usuario donde quedara el radicado.
        $depe_actu = $depe_dest;
        $usua_actu = $usua_dest;

        $docu_actu = $conn->GetOne("SELECT USUA_DOC FROM USUARIO WHERE USUA_CODI=$usua_actu AND DEPE_CODI=$depe_actu");

        if ($validaOk) {
            $conn->StartTrans();
            //1. Validar subida de archivo.
            //  POR HACER ************************
            //2. Generamos radicado.
            //a. Crear registro en tabla radicado.
            /// Iniciamos Radicacion ///
            $secNew = $conn->GenID("SECR_TP2_$depe_radi_entrada");
            if ($secNew == FALSE) {
                $conn->CompleteTrans();
                $cadError[] = "Error al consultar secuencia. <!-- SECR_TP2_$depe_radi_entrada-->";
                return $cadError;
            }
            $newRadicado = date("Y") . str_pad($depe_radi, 3, "0", STR_PAD_LEFT) . str_pad($secNew, 6, "0", STR_PAD_LEFT) . "2";
            $codigo_seguridad = 1;

            $tabla = 'RADICADO';
            //$ok_r = $conn->AutoExecute($tabla, $datos_r, 'INSERT', false, false, false);
            //$idRad = $conn->GetOne("SELECT MAX(ID) FROM RADICADO");

            $sql_r = "INSERT INTO RADICADO " .
                    "(CARP_PER,CARP_CODI,TDOC_CODI,RA_ASUN,TRTE_CODI,MREC_CODI,RADI_FECH_OFIC,RADI_USUA_ACTU,RADI_DEPE_ACTU,RADI_FECH_RADI,RADI_USUA_RADI,RADI_DEPE_RADI,CODI_NIVEL,FLAG_NIVEL,RADI_LEIDO,RADI_NUME_RADI) " . //si desea agregar los rad de entrada como privado agregue la variable $codigo_seguridad y tambien el campo sgd_spub_codigo
                    "VALUES " .
                    "(0, 0, " . $TipoSolicitud . ", ". $conn->qstr(substr(trim($AsuntoRadicado), 0, 350), get_magic_quotes_gpc()) . ", $Tercero, $MedioRecepcion, " . $conn->DBDate($fechaRadicado) . ", $usua_actu, $depe_actu, " . $conn->sysTimeStamp . ", $usua_radi, $depe_radi, $usua_nivel, 1, 0, $newRadicado)";
            $ok_r = $conn->Execute($sql_r);

            if ($ok_r === FALSE) {
                $conn->CompleteTrans();
                $cadError[] = "Error en la insercion de radicado. <!-- " . $sql_r . " -->";
                return $cadError;
            } else {
                //b. Crear registro en tabla historico.
                $datos_h["RADI_NUME_RADI"] = $newRadicado;
                $datos_h["DEPE_CODI"] = $depe_radi;
                $datos_h["USUA_CODI"] = $usua_radi;
                $datos_h["USUA_CODI_DEST"] = $usua_actu;
                $datos_h["DEPE_CODI_DEST"] = $depe_actu;
                $datos_h["USUA_DOC"] = $usua_docu;
                $datos_h["HIST_DOC_DEST"] = $docu_actu;
                $datos_h["SGD_TTR_CODIGO"] = 2;
                $datos_h["HIST_OBSE"] = 'Radicacion PQR Verbal';
                $datos_h["HIST_FECH"] = $conn->sysTimeStamp;
                $tabla = 'HIST_EVENTOS';
                //$sql_h = $conn->GetInsertSQL($tabla, $datos_h, false, false);
                //Consulta del codigo de la matriz TRD.
                /* Se comenta por solicitud de Atencion al Ciudadano
                  $sqlMat = " SELECT	SGD_MRD_CODIGO
                  FROM	SGD_MRD_MATRIRD
                  WHERE	DEPE_CODI = 600 AND
                  SGD_SRD_CODIGO = 176 AND
                  SGD_SBRD_CODIGO = 998 AND
                  SGD_TPR_CODIGO = ".$TipoSolicitud;

                  $rsMat = $conn->GetOne($sqlMat);

                  if ($rsMat){
                  $sqlTrd = "	INSERT	INTO SGD_RDF_RETDOCF
                  (SGD_MRD_CODIGO, RADI_NUME_RADI, DEPE_CODI, USUA_CODI, USUA_DOC, SGD_RDF_FECH)
                  VALUES	($rsMat,$newRadicado,600,$usua_radi,'$usua_docu',$conn->sysTimeStamp)";
                  $ok_trd = $conn->Execute($sqlTrd);
                  if ($ok_trd === FALSE) {
                  $conn->CompleteTrans();
                  $cadError[] = "Error en la insercion de TRD. <!-- $sqlTrd -->";
                  return $cadError;
                  }
                  }
                 */
                $sql_h = "INSERT INTO HIST_EVENTOS " .
                        "(RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_CODI_DEST,DEPE_CODI_DEST,USUA_DOC,HIST_DOC_DEST,SGD_TTR_CODIGO,HIST_OBSE,HIST_FECH) " .
                        "VALUES " .
                        "($newRadicado, $depe_radi, $usua_radi, 1,$depe_actu, '$usua_docu', '$docu_actu', 2, ' ', " . $conn->sysTimeStamp . ")";

                $ok_h = $conn->Execute($sql_h);
                if ($ok_h === FALSE) {
                    $conn->CompleteTrans();
                    $cadError[] = "Error en la insercion de historico. <!-- $sql_h -->";
                    return $cadError;
                } else {
                    //c. Crear registro en tabla de tercero.
                    switch ($TipoTercero) {
                        case 1: {
                                $secuencia = $conn->GenID("SEC_CIU_CIUDADANO");
                                if ($secuencia === FALSE) {
                                    $conn->CompleteTrans();
                                    $cadError[] = "Error al generar secuencia de ciudadanos.";
                                    return $cadError;
                                }
                                $datos_t['SGD_CIU_CODIGO'] = $secuencia;
                                $sgd_ciu_codigo = $secuencia;
                                $tabla = 'SGD_CIU_CIUDADANO';
                            }break;
                        case 3: {
                                $secuencia = $conn->GenID("SEC_OEM_OEMPRESAS");
                                if ($secuencia === FALSE) {
                                    $conn->CompleteTrans();
                                    $cadError[] = "Error al generar secuencia de empresas.";
                                    return $cadError;
                                }
                                $datos_t['SGD_OEM_CODIGO'] = $secuencia;
                                $sgd_oem_codigo = $secuencia;
                                $tabla = 'SGD_OEM_OEMPRESAS';
                            }break;
                        default:
                            break;
                    }
                    $sql_t = $conn->GetInsertSQL($tabla, $datos_t, $magicq = false, $force_type = false);
                    $ok_t = $conn->Execute($sql_t);

                    if ($ok_t === FALSE) {
                        $conn->CompleteTrans();
                        $cadError[] = "Error en la insercion de tercero. <!-- $sql_t --> ";
                        return $cadError;
                    } else {   //d. Crear registro en tabla sgd_dir_drecciones.
                        $nextval = $conn->GenID("SEC_DIR_DIRECCIONES");
                        if ($nextval === FALSE) {
                            $conn->CompleteTrans();
                            $cadError[] = "Error al generar secuencia de direcciones.";
                            return $cadError;
                        }
                        $ADODB_FORCE_TYPE = ADODB_FORCE_NULL;
                        $grbNombresUs1 = substr(trim($NombreTercero) . " " . trim($ApellidoTercero), 0, 900);
                        $datos_d = array();
                        $datos_d['SGD_TRD_CODIGO'] = $sgdTrd;
                        $datos_d['SGD_DIR_NOMREMDES'] = $grbNombresUs1;
                        $datos_d['SGD_DIR_TDOC'] = $TipoDocumento;
                        $datos_d['SGD_DIR_DOC'] = $DocumentoTercero;
                        $datos_d['SGD_DOC_FUN'] = $sgd_fun_codigo;
                        $datos_d['SGD_CIU_CODIGO'] = $sgd_ciu_codigo;
                        $datos_d['SGD_OEM_CODIGO'] = $sgd_oem_codigo;
                        $datos_d['SGD_ESP_CODI'] = $sgd_esp_codigo;
                        $datos_d['RADI_NUME_RADI'] = $newRadicado;
                        $datos_d['SGD_SEC_CODIGO'] = 0;
                        $datos_d['SGD_DIR_DIRECCION'] = $DireccionTercero;
                        $datos_d['SGD_DIR_CODPOSTAL'] = $CodigoPostal;
                        $datos_d['ID_CONT'] = $idCont;
                        $datos_d['ID_PAIS'] = $idPais;
                        $datos_d['MUNI_CODI'] = $idMpio;
                        $datos_d['DPTO_CODI'] = $idDpto;
                        $datos_d['SGD_DIR_TELEFONO'] = 'null';
                        $datos_d['SGD_DIR_MAIL'] = $CorreoElectronicoTercero;
                        $datos_d['SGD_DIR_TIPO'] = 1;
                        $datos_d['SGD_DIR_CODIGO'] = $nextval;
                        $record['SGD_DIR_NOMBRE'] = $otro_us1;
                        $tabla = "SGD_DIR_DRECCIONES";
                        $sql_d = $conn->GetInsertSQL($tabla, $datos_d, false, false);
                        $ok_d = $conn->Execute($sql_d);
                        if ($ok_d === FALSE) {
                            $conn->CompleteTrans();
                            return $cadError;
                        } else {
                            //e. Creamos registro en SGD_CAUX_CAUSALES
                            $tabla = "SGD_CAUX_CAUSALES";
                            $sql = "SELECT MAX(SGD_CAUX_CODIGO)+1 AS CONTEO FROM $tabla";
                            $cntCaux = $conn->GetOne($sql);
                            $datos_c = array();
                            $datos_c['SGD_CAUX_CODIGO'] = $cntCaux;
                            $datos_c['RADI_NUME_RADI'] = $newRadicado;
                            $datos_c['SGD_DCAU_CODIGO'] = $Tema;
                            $datos_c['SGD_CAUX_FECHA'] = $conn->OffsetDate(0, $conn->sysTimeStamp);
                            $ok_c = $conn->Replace($tabla, $datos_c, false, false);
                            if ($ok_c === FALSE) {
                                $conn->CompleteTrans();
                                return $cadError;
                            }
                        }
                    }
                }
            }
            $conn->CompleteTrans();
            if ($ok_r && $ok_h && $ok_t && $ok_d && $ok_c) {
                return $newRadicado;
            }
            else
                return false;
        } else {
            return $cadError;
        }
    }
}

/**
 * 
 * Enter description here ...
 * @param String	$archivoASubir	Ruta y nombre del archivo a subir.
 * @param integer	$radicado		Codigo del radicado al cual se anexaran los archivos.
 * @param String	$usrRadicador	Login del usuario que hara la labor de asociar los archivos.
 * @param boolean	$principal		True, cargara la imagen principal y False las procesara como anexas.
 */
function anexarArchivos($archivoASubir, $radicado, $usrRadicador, $principal = false) {
    if (empty($archivoASubir) || empty($radicado) || empty($usrRadicador)) {
        $cadError[] = "Faltan datos basicos.";
        return $cadError;
    } else {
        $validaOk = true;
        $cadError = array();

        if (is_numeric($radicado)) {
            if (is_readable($archivoASubir)) {
                require("../config.php");
                if (!defined('ADODB_ASSOC_CASE'))
                    define('ADODB_ASSOC_CASE', 1);
                require "adodb/adodb.inc.php";
                $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
                $dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
                $conn = NewADOConnection($dsn);
                ////// VALIDACION DE CONEXION //////
                if ($conn === false) {
                    $cadError[] = "Error de Conexion a BD.";
                    $validaOk = false;
                }
                ////// VALIDACION DE USUARIO //////
                $sql = "select u.usua_esta from usuario u where usua_login='$usrRadicador'";
                $ADODB_COUNTRECS = true;
                $rs = $conn->Execute($sql);
                $ADODB_COUNTRECS = false;
                if ($rs === false) {
                    $cadError[] = "Error en la verificacion de usuario $usrRadicador.";
                    $validaOk = false;
                }
                if ($rs->RecordCount() == 0) {
                    $cadError[] = 'Usuario radicador inexistente.';
                    $validaOk = false;
                } else {
                    if ((int) $rs->fields['USUA_ESTA'] === 0) {
                        $cadError[] = 'Usuario radicador inactivo.';
                        $validaOk = false;
                    }
                }
                ////// VALIDACION DE RADICADO //////
                $query = "SELECT RADI_PATH FROM RADICADO WHERE RADI_NUME_RADI=$radicado";
                $ADODB_COUNTRECS = true;
                $rs = $conn->Execute($query);

                if ($rs->RecordCount() == 0) {
                    $cadError[] = 'Radicado no existe.';
                    $validaOk = false;
                } else {
                    // creamos ruta a bodega
                    $ruta = "../bodega/" . substr($radicado, 0, 4) . "/" . substr($radicado, 4, 3) . "/";
                    $ruta .= ($principal) ? '' : "docs/";

                    $mixInfo = pathinfo($archivoASubir);
                    $extension = $mixInfo['extension'];

                    //Si es imagen principal de un radicado....
                    if ($principal) {
                        if ($extension == 'pdf' || $extension == 'tif') {
                            //Si no tiene imagen asignada...
                            if (empty($rs->fields['RADI_PATH'])) {
                                $ruta_query = "/" . substr($radicado, 0, 4) . "/" . substr($radicado, 4, 3) . "/$radicado.$extension";
                                $query = "UPDATE RADICADO SET RADI_PATH='$ruta_query' WHERE RADI_NUME_RADI=$radicado";
                                $nombreArchivo = "$radicado.$extension";
                            } else {
                                $cadError[] = 'Radicado ya posee imagen.';
                                $validaOk = false;
                            }
                        } else {
                            $cadError[] = 'El documento de imagen para radicados debe ser pdf o tif.';
                            $validaOk = false;
                        }
                    } else {
                        $bytes = filesize($archivoASubir);

                        $descripcion = 'Archivo cargado via Web.';
                        ////// VALIDACION DE SECUENCIA EN ANEXOS //////
                        $query = "SELECT COUNT(1) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME=$radicado";
                        $cnt_actual_anexos = $conn->GetOne($query) + 1;
                        $query = "SELECT max(ANEX_NUMERO) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME=$radicado";
                        $max_secuencia_anexos = $conn->GetOne($query) + 1;
                        $anex_numero = ($cnt_actual_anexos > $max_secuencia_anexos) ? $cnt_actual_anexos : $max_secuencia_anexos;
                        $anex_codigo = $radicado . substr("00000" . $anex_numero, -5);
                        $query = "SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT='$extension'";
                        $tmp_anex_tipo = $conn->GetOne($query);
                        $anex_tipo = empty($tmp_anex_tipo) ? "0" : $tmp_anex_tipo;
                        $fechaAnexado = $conn->OffsetDate(0, $conn->sysTimeStamp);
                        $query = "INSERT INTO ANEXOS
						                                (ANEX_CODIGO, ANEX_RADI_NUME, ANEX_TIPO, ANEX_TAMANO, ANEX_SOLO_LECT,
						                                ANEX_CREADOR, ANEX_DESC, ANEX_NUMERO, ANEX_NOMB_ARCHIVO, ANEX_ESTADO,
						                                SGD_REM_DESTINO, ANEX_FECH_ANEX, ANEX_BORRADO)
						                            VALUES
						                                ('$anex_codigo', $radicado, $anex_tipo, " . round(($bytes / 1024), 2) . ", 'N',
						                                '$usrRadicador','$descripcion', $anex_numero, '$anex_codigo.$extension', 0,
						                                1, $fechaAnexado, 'N')";

                        $nombreArchivo = "$anex_codigo.$extension";
                    }
                }
                if ($validaOk) {
                    $rs = $conn->Execute($query);
                    $ok_r = rename("$archivoASubir", "$ruta" . $nombreArchivo);
                    if ($rs === false || $ok_r === false) {
                        $cadError[] = "Error en la actualizacion en BD o traslado del archivo. <!-- $ok_r -->";
                        return $cadError;
                    } else {

                        return $validaOk;
                    }
                } else {
                    $cadError[] = 'Los siguientes errores fueron hallados:<br>' . implode('<br>', $cadError);
                    return $cadError;
                }
            } else {
                $cadError[] = 'Los siguientes errores fueron hallados:<br>' . implode('<br>', $cadError);
                return $cadError;
            }
        } else {
            $cadError[] = 'Los siguientes errores fueron hallados:<br>' . implode('<br>', $cadError);
            return $cadError;
        }
    }
}

/**
 * 
 */
function AgregarMetadataPQR($radicado, $formaEnvio, $infPoblacional, $esRadPqrVerbal = 0) {
    $cadError === FALSE;
    if (empty($radicado) or empty($formaEnvio)) {
        $cadError[] = "Faltan datos basicos en FormaEnvioPreferida.";
    } else {
        if (!is_numeric($radicado) or !in_array($formaEnvio, array('V', 'F'))) {
            $cadError[] = "Datos erroneos en FormaEnvioPreferida.";
            return $cadError;
        } else {
            require "../config.php";
            if (!defined('ADODB_ASSOC_CASE'))
                define('ADODB_ASSOC_CASE', 1);
            require "adodb/adodb.inc.php";
            $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
            $dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
            $conn = NewADOConnection($dsn);
            ////// VALIDACION DE CONEXION //////
            if ($conn === false) {
                $cadError[] = "Error de Conexion a BD.";
                $validaOk = false;
            } else {
                $sql = "INSERT INTO SGD_PQR_METADATA (RADI_NUME_RADI, SGD_FENV_MODALIDAD, ID_INFPOB, PQRVERBAL) values ($radicado, '$formaEnvio', $infPoblacional, $esRadPqrVerbal)";
                $ok = $conn->Execute($sql);
                if ($ok) {
                    $validaOk = true;
                } else {
                    $cadError[] = "Error al agregar Inf. Reservada.";
                    $validaOk = false;
                }
            }
        }
    }
    return ($cadError ? $cadError : $validaOk);
}

function enviarCorreo($radiNumeRadi) {
    $result = "-1";
    require "../config.php";
    require "../class_control/correoElectronico.php";
    //Correo electronico de la persona que se le asigno el radicado de entrada (-2).
    $tipRad = substr($radiNumeRadi, -1);  // Devuelve el tipo de radicado
    if ($tipRad == 2) {

        require "adodb/adodb.inc.php";
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
        $conn = NewADOConnection($dsn);

        $sql = "SELECT USUA_EMAIL, RA_ASUN FROM RADICADO R INNER JOIN USUARIO U ON R.RADI_USUA_ACTU=U.USUA_CODI AND R.RADI_DEPE_ACTU=U.DEPE_CODI WHERE RADI_NUME_RADI=$radiNumeRadi";
        $rsf = $conn->Execute($sql);
        $correoDes = trim($rsf->fields["USUA_EMAIL"]);
        $asuntoRad = trim($rsf->fields["RA_ASUN"]);

        $asunto = "Nuevo radicado de Entrada en Orfeo DNP: " . $radiNumeRadi;
        $cuerpo = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
		<tr><td colspan='2' style='font-family: verdana; font-size: 75%; text-align: justify'>
		El Sistema de Gesti&oacute;n Documental <b>Orfeo</b> le informa que
		se ha generado un nuevo radicado de Entrada. El N&uacute;mero es <b>$radiNumeRadi</b> con
		asunto <b>$asuntoRad</b>.<br/><br/> Por favor consulte su bandeja de entrada.
		</td><tr>
		<tr><td colspan='2' style='text-align: center'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
		</table>";
        $objMail = new correoElectronico('../');
        $objMail->FromName = "Notificaciones";
        $result = $objMail->enviarCorreo(array($correoDes), null, null, $asunto, $cuerpo);
    }
    return $result;
}

?>