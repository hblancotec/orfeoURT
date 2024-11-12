<?php
    // Script para ejecutar para ejecutar el
    define ('ORFEOSRC', '/var/www/orfeo36/');
    require (ORFEOSRC . "config.php");
    require (ORFEOPATH . "include/db/ConnectionHandler.php");
    $db = new ConnectionHandler(ORFEOPATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
    // Expresion regular para encontrar las tablas de secuencias
    $patronReg      = '^SECR_TP[[:digit:]]_[[:digit:]]';
    $existeBodega   = '';
    $anoActual      = date("Y");
    $existeDirAno   = '';
    $anoSiguiente   = $anoActual + 1;
    $creoDirSigAno  = false;
    $sqlDep         = "SELECT DEPE_CODI FROM DEPENDENCIA";
    $dirDependencia = 0;
    $dirVerificacion= BODEGAPATH . $anoActual;
    // Capturando nombres de las tablas
    $tablas         = $db->conn->MetaTables('TABLES');
    $secuencias     = array();
    $encontroSec    = false;
    $dirBodega      = BODEGAPATH;
    $existeDirBodega= !empty($dirBodega);
    $inicializarSec = false;
    // Variable para tener de referencia cuales secuencias ha actualizado
    $actSec = array();
    $numSec = 0;
    
    // Capturando tablas de secuencias para inicializarlas
    switch ($db->driver) {
        case 'oci8' : 
            break;
        case 'mssql' :
            foreach ($tablas as $tabla) {
                $tabla = trim($tabla);
                $encontroSec = eregi($patronReg, $tabla);
                if ($encontroSec) {
                    $secuencias[] = $tabla;
                }
            }
    }
    
    if ($existeDirBodega) {
        $existeBodega = is_dir($dirBodega);
        if ($existeBodega) {
            // Si se hace antes del ano a crear
            $existeDirAno = is_dir($dirVerificacion);
            if ($existeDirAno) {
                // Se crea el directorio del siguiente ano
                $dirTrabajo = BODEGAPATH . $anoSiguiente;
                $creoDirSigAno = mkdir($dirTrabajo, 0770);
                // Capturando Codigo Dependencias
                if ($creoDirSigAno) {
                    $rsDep = $db->conn->Execute($sqlDep);
                    if ($rsDep === false) {
                        $errorMsg = $db->conn->ErrorMsg();
                        exit();
                    } else {
                        while (!$rsDep->EOF) {
                            $dirDependencia = $rsDep->fields['DEPE_CODI'];
                            $existeDirDepe = is_dir ("$dirTrabajo/$dirDependencia");

                            if (!$existeDirDepe) {
                                $creoDirDepe = mkdir("$dirTrabajo/$dirDependencia", 0770);
                            } else {
                                $creoDirDepe = true;
                            }

                            if ($creoDirDepe) {
                                $existeDirDocs = is_dir("$dirTrabajo/$dirDependencia/docs");

                                if (!$existeDirDocs) {
                                    $creoDirDocs = mkdir("$dirTrabajo/$dirDependencia/docs", 0770);
                                } else {
                                    $creoDirDocs = true;
                                }

                                if (!$creoDirDocs) {
                                    var_dump("Error creando el directorio $dirTrabajo/$dirDependencia/docs");
                                    exit();
                                }
                            }
                            $rsDep->MoveNext();
                        }
                    }
                }
            } else {
                // Si no existe ano actual entonces lo crea
                $dirTrabajo = BODEGAPATH . $anoActual;
                $creoDirSigAno = mkdir($dirTrabajo, 0770);
                // Capturando Codigo Dependencias
                if ($creoDirSigAno) {
                    $rsDep = $db->conn->Execute($sqlDep);
                    if ($rsDep === false) {
                        $errorMsg = $db->conn->ErrorMsg();
                        exit();
                    } else {
                        while (!$rsDep->EOF) {
                            $dirDependencia = $rsDep->fields['DEPE_CODI'];
                            $existeDirDepe = is_dir ("$dirTrabajo/$dirDependencia");

                            if (!$existeDirDepe) {
                                $creoDirDepe = mkdir("$dirTrabajo/$dirDependencia", 0770);
                            } else {
                                $creoDirDepe = true;
                            }

                            if ($creoDirDepe) {
                                $existeDirDocs = is_dir("$dirTrabajo/$dirDependencia/docs");

                                if (!$existeDirDocs) {
                                    $creoDirDocs = mkdir("$dirTrabajo/$dirDependencia/docs", 0770);
                                } else {
                                    $creoDirDocs = true;
                                }

                                if (!$creoDirDocs) {
                                    var_dump("Error creando el directorio $dirTrabajo/$dirDependencia/docs");
                                    exit();
                                }
                            }
                            $rsDep->MoveNext();
                        }
                    }
                }
            }
        }

        // si realizo la operacion entonces inicializa secuencias si esta haciendo en el nuevo ano
        if ($inicializarSec && !$existeDirAno) {
            switch ($db->driver) {
                case 'mssql' :
                    $numSec = count($secuencias);
                    if ($numSec) {
                        foreach ($secuencias as $secuencia) {
                            $secUpd = "UPDATE $secuencia SET ID = 0";
                            $rsSecUpd = $db->conn->Execute($secUpd);
                            if ($rsSecUpd === false) {
                                $actSec[$secuencia] = 0;
                                $actSec[$secuencia]['errorMsq'] = $rsSecUpd->ErrorMsg();
                            } else {
                                $actSec[$secuencia] = 1;
                            }
                        }
                    } else {
                        var_dump("No se encontraron las tablas de secuencias");
                    }
            }
        }
    } else {
        $errorMsg = "No exite directorio de la bodega en Orfeo";
    }
?>
