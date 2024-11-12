<?php
    require ("../../config.php");
    require (ORFEOPATH . "include/db/ConnectionHandler.php");
    require ("HTML/Template/IT.php");
    $db = new ConnectionHandler(ORFEOPATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    //$db->conn->debug = true;
    // Variable que se tendra como referencia para saber si se hace por el modulo de administracion o crontab
    
    $patronReg      = '/^SECR_TP[[:digit:]]_[[:digit:]]/';
    $existeBodega   = '';
    $anoActual      = 0;
    $existeDirAno   = '';
    $creoDirSigAno  = false;
    $sqlDep         = "SELECT DEPE_CODI FROM DEPENDENCIA";
    $dirDependencia = 0;
    $dirVerificacion= BODEGAPATH . $anoActual;
    $tablas         = $db->conn->MetaTables('TABLES');
    $secuencias     = array();
    $encontroSec    = false;
    $dirBodega      = BODEGAPATH;
    $existeDirBodega= !empty($dirBodega);
    $iniciarSec     = (!empty($_POST['iniciarSec'])) ? $_POST['iniciarSec'] : 0;
    $anoActual      = (!empty($_POST['directorio'])) ? $_POST['directorio'] : null;
    $tpl            = new HTML_Template_IT(TPLPATH);
    // Capturando tablas de secuencias para inicializarlas
    switch ($db->driver) {
        case 'oci8' : 
            break;
        case 'mssqlnative' :
            foreach ($tablas as $tabla) {
                $tabla = trim($tabla);
                $encontroSec = preg_match($patronReg, $tabla);
                if ($encontroSec) {
                    $secuencias[] = $tabla;
                }
            }
    }
    //exit();
    
    // Existe veridicando variable por post
    echo "<hr> Año $anoActual";
    echo "<hr>$dirVerificacion<hr>";
    if (!empty($anoActual)) {
        // Existe variable bodega
        if ($existeDirBodega) {
            $existeBodega = is_dir($dirBodega);
            // Existe directorio bodega
            if ($existeBodega) {
                $existeDirAno = is_dir($dirVerificacion);
                if ($existeDirAno) {
                    $errorMsg = 'Ya exite el directorio no se puede realizar la operacion de creacion';
                } else {
                    // Se crea el directorio del siguiente ano
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
                                        //exit();
                                    }else{
					echo "Se crea Directorio $dirTrabajo/$dirDependencia Ok !<br>";
				    }
                                }
                                $rsDep->MoveNext();
                            }
                        }
                    }
                    $actSec = array();
                    if ($iniciarSec) {
			echo "<hr> se entra a crear secuencias. <hr>";
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
				    echo "Se actualizo Secuencia $secuencia Ok <br>";
                                }
                            }
                        } else {
                            // no ha encontrado secuencias
                        }
                    }
                }
            } else {
                
            }
        } else {
            $errorMsg = 'no hay definida la ruta de la bodega por favor verificar la configuracion';
        }
    } else {
        $errorMsg = 'No ha definido un directorio para crear';
    }
?>
