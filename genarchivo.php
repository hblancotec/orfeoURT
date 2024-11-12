<?php
session_start();
if (!$ruta_raiz)
	$ruta_raiz = ".";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
set_time_limit(500);

include ("$ruta_raiz/config.php");
if (!isset ($_SESSION['dependencia']))
	include "$ruta_raiz/rec_session.php";
if (isset ($db))
	unset ($db);
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug = true;
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
require_once ORFEOPATH . "class_control/anexo.php";
require_once ORFEOPATH . "class_control/CombinaError.php";
require_once ORFEOPATH . "class_control/Dependencia.php";
require_once ORFEOPATH . "class_control/Esp.php";
require_once ORFEOPATH . "class_control/TipoDocumento.php";
require_once ORFEOPATH . "class_control/Radicado.php";
require_once ORFEOPATH . "include/tx/Radicacion.php";
include_once ORFEOPATH . "include/tx/Historico.php";
require_once ORFEOPATH . "class_control/ControlAplIntegrada.php";
require_once ORFEOPATH . "include/tx/Expediente.php";
require_once ORFEOPATH . "include/tx/Historico.php";

# si no viene combina se combina...
if (!isset ($combina)) {
	$combina = true;
} else {
	$combina = false;
}

$dep = new Dependencia($db);
$espObjeto = new Esp($db);
$radObjeto = new Radicado($db);
$radObjeto->radicado_codigo($numrad);
$tdoc = new TipoDocumento($db); //objeto que maneja el tipo de documento del anexos
$tdoc2 = new TipoDocumento($db); //objeto que maneja el tipo de documento del radicado
$tdoc2->TipoDocumento_codigo($radObjeto->getTdocCodi());
$fecha_dia_hoy = Date("Y-m-d");
$sqlFechaHoy = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
$objCtrlAplInt = new ControlAplIntegrada($db); // Objeto control de aplicaciones integradas
$objExpediente = new Expediente($db); // Objeto expediente
$expRadi = $objExpediente->consulta_exp($numrad);
// Variable que contiene la descripcion del anexo para pasarla al asunto del radicado
$radAsunto = "";

$dep->Dependencia_codigo($dependencia);
$dep_sigla = $dep->getDepeSigla();
$nurad = isset($nurad) ? trim($nurad) : NULL;
$numrad = trim($numrad);
$hora = date("H") . "_" . date("i") . "_" . date("s");
$ddate = date('d'); // var que almacena el dia de la fecha
$mdate = date('m'); // var que almacena el mes de la fecha
$adate = date('Y'); // var que almacena el ano de la fecha
$fechaArchivo = $adate . "_" . $mdate . "_" . $ddate; // var que almacena  la fecha formateada
//var que almacena el nombre que tendra la pantilla
$archInsumo = "tmp_" . $usua_doc . "_" . $fechaArchivo . "_" . $hora . ".txt";
$terr_ciu_nomb = $dep->getTerrCiuNomb(); //Var que almacena el nombre de la ciudad de la territorial
$terr_sigla = $dep->getTerrSigla(); //Var que almacena el nombre corto de la territorial
$terr_direccion = $dep->getTerrDireccion(); //Var que almacena la direccion de la territorial
$terr_nombre = $dep->getTerrNombre(); //Var que almacena el nombre largo de la territorial
$nom_recurso = $tdoc2->get_sgd_tpr_descrip(); //Var que almacena el nombre del recurso
?>
<head><title>Gen  -  ORFEO</title>
<link rel="stylesheet" href="estilos_totales.css">
</head>
<body style="text-align: center">
<?php

if (!$numrad) {
	$numrad = $verrad;
}
if (isset($radicar_a) && (strlen(trim($radicar_a)) == 13 or strlen(trim($radicar_a)) == 18)) {
	$no_digitos = 5;
} else {
	$no_digitos = 6;
}

$orlinkarchivo = $linkarchivo;
$updateRadiPaht = "/" . $linkarchivo;
$linkarchivo = str_replace("/", "\\", $linkarchivo);
$linkArchSimple = strtolower($linkarchivo);

$linkArchivoTmpSimple = strtolower($linkarchivotmp);

$linkarchivo = "/" . strtolower($linkarchivo);
$linkarchivotmp=  "$ruta_raiz/" . strtolower($linkarchivotmp);
$fechah = date("Ymd") . "_" . time("hms");
$trozosPath = explode("\\", $linkarchivo);
$nombreArchivo = $trozosPath[count($trozosPath) - 1];
//$rsMove = copy(BODEGAPATH.str_replace("/", "\\", substr($updateRadiPaht,1)), BODEGAPATH."masiva\\$nombreArchivo");


$a = new Anexo($db);
$a->anexoRadicado($numrad, $anexo);
$apliCodiaux = $a->get_sgd_apli_codi();
$anex = $a;
$secuenciaDocto = $a->get_doc_secuencia_formato($dependencia);
$fechaDocumento = $a->get_sgd_fech_doc();
$tipoDocumento = $a->get_sgd_tpr_codigo();
$radAsunto = $a->get_anex_desc();
$tdoc->TipoDocumento_codigo($tipoDocumento);
$tipoDocumentoDesc = $tdoc->get_sgd_tpr_descrip();
$radinumesalida = $a->get_radi_nume_salida();
$anexradifech = $a->get_anex_radi_fech();

//Iniciamos StartTrans debido a que la logica de obtener datos y actualzar rutas no toma en cuenta
//si se combina bien el documento. Entonces despues de combinado hacemos el commitTrans().
//$db->conn->StartTrans();

if ($radicar_documento) {
	// Generacion de la secuencia para documentos especiales
	// Generar el Numero de Radicacion

	if (isset($ent) && $ent != 2 && $nurad and $vpppp == "ddd") {
		$sec = $nurad;
		$anoSec = substr($nurad, 0, 4);
		// @tipoRad define el tipo de radicado el -X
		$tipoRad = substr($radicar_documento, -1);
	} else {
		if ($vp == "n" and $radicar_a == "si") {
			if ($generar_numero == "no") {
				$sec = substr($nurad, 7, $no_digitos);
				$anoSec = substr($nurad, 0, 4);
				$tipoRad = substr($radicar_documento, -1);
			} else {
				$isql = "select * from ANEXOS
						where ANEX_CODIGO='$anexo' AND
						ANEX_RADI_NUME=$numrad";
				$rs = $db->conn->Execute($isql);
				if (!$rs->EOF) {
					$radicado_salida = $rs->fields['RADI_NUME_SALIDA'];
					$expAnexoActual = $rs->fields['SGD_EXP_NUMERO'];
					if ($expAnexoActual != '') {
						$expRadi = $expAnexoActual;
					}
				} else {
					//$db->conn->RollbackTrans();
					die("<span class='etextomenu'>No se ha podido obtener la informacion del radicado");
				}

				if (!$radicado_salida) {
					$no_digitos = 6;
					$tipoRad = "1";
				} else {
					$sec = substr($radicado_salida, 7, $no_digitos);
					$tipoRad = substr($radicar_documento, -1);
					$anoSec = substr($radicado_salida, 0, 4);
					//$db->conn->RollbackTrans();
					die("<span class='etextomenu'><br>Ya estaba radicado<br>");
					$radicar_a = $radicado_salida;
				}
			}
		} else {
			if ($vp == "s") {
				$sec = "XXX";
			} else {
				// En esta parte es en la cual se entra a asignar el numero de radicado
				$sec = substr($radicar_a, 7, $no_digitos);
				$anoSec = substr($radicar_a, 0, 4);
				$tipoRad = substr($radicar_a, 13, 1);
			}
		}

		// Generacion de numero de radicado de salida
		$sec = str_pad($sec, $no_digitos, "0", STR_PAD_LEFT);
		$plg_comentarios = "";
		$plt_codi = $plt_codi;

		if (!$anoSec) {
			$anoSec = date("Y");
		}

		if (!$tipoRad) {
			$tipoRad = "1";
		}

		//Adicion para que no reemplace el numero de radicado de un anexo al ser reasignado a otra dependencia
		if ($generar_numero == "no") {
			$rad_salida = $numrad;
		} else {
			//Es un anexo radicado en otra dependencia y no queremos que le genere un nuevo numero
			if ($radicar_a != null && $radicar_a != 'si') {
				$rad_salida = $radicar_a;
			} else {
				$rad_salida = $anoSec . $dependencia . $sec . $tipoRad;
			}
		}

		if ($numerar == 1) {
			$numResol = $a->get_doc_secuencia_formato();
			$rad_salida = date("Y") . $dependencia .
			str_pad($a->sgd_doc_secuencia(), 6, "0", STR_PAD_left) . $a->get_sgd_tpr_codigo();
		}
	}

	// Fin generacion de numero de radicado de salida
	$ext = substr($linkarchivo, strrpos(trim($linkarchivo), '.')+1);
	echo "<font size='3' color='#000000'><span class='etextomenu'>\n";
	$extVal = strtoupper($ext);
	if (($extVal == "XLS" or $extVal == "PPT" or $extVal == "PDF") && ($combina)) {
		echo "<br>
		                <font size='3'>
		                    <span class='etextomenu'>
		                        Sobre formato ($ext) no se puede realizar combinaci&oacute;n de correspondencia
		                    </span>
		            </br>";
		die;
	} else {
		require "$ruta_raiz/jh_class/funciones_sgd.php";
		$verrad = $numrad;
		$radicado_p = $verrad;
		$no_tipo = "true";
		require "$ruta_raiz/ver_datosrad.php";

		if ($muni_us7 != null && $muni_us7 != null && $muni_us1 == null && $muni_us1 == null) {
			$muni_us1 = $muni_us7;
			$codep_us1 = $codep_us7;

			//No tenemos el numero de doc, luego buscamos el destino para obtenerlo

			//Con el nombre de destino, buscamos el numero de codigo
			$isql = "select sgd_doc_fun, sgd_esp_codi, sgd_oem_codigo, sgd_ciu_codigo from SGD_DIR_DRECCIONES
			                                            where RADI_NUME_RADI=$numrad";
			$rs = $db->conn->Execute($isql);
			if (!$rs->EOF) {
				$docFun = $rs->fields['sgd_doc_fun'];
				$docEsp = $rs->fields['sgd_esp_codi'];
				$docOem = $rs->fields['sgd_oem_codigo'];
				$docCiu = $rs->fields['sgd_ciu_codigo'];
				if ($docFun != null) {
					$documento_us7 = $docFun;
					$tipo_emp_us1 = 6;
				} else
					if ($docEsp != null) {
						$documento_us7 = $docEsp;
						$tipo_emp_us1 = 1;
					} else
						if ($docOem != null) {
							$documento_us7 = $docOem;
							$tipo_emp_us1 = 2;
						} else
							if ($docCiu != null) {
								$documento_us7 = $docCiu;
								$tipo_emp_us1 = 0;
							}
			}
		}
		include "$ruta_raiz/radicacion/busca_direcciones.php";
		$a = new LOCALIZACION($codep_us1, $muni_us1, $db);
		$dpto_nombre_us1 = $a->departamento;
		$muni_nombre_us1 = $a->municipio;
		$a = new LOCALIZACION($codep_us2, $muni_us2, $db);
		$dpto_nombre_us2 = $a->departamento;
		$muni_nombre_us2 = $a->municipio;

		//Se agrega, pues no esta verificando si se modifico el municipio y/o departamento de la ESP
		$isql = "select MUNI_CODI,
		                            DPTO_CODI
		                        from ANEXOS
		                        where ANEX_CODIGO='$anexo' AND
		                                ANEX_RADI_NUME=$numrad";
		$rs = $db->conn->Execute($isql);
		if (!$rs->EOF) {
			$codigoCiudadESPMod = $rs->fields['MUNI_CODI'];
			$codigoDeptoESPMod = $rs->fields['DPTO_CODI'];
			if ($espcodi && $codigoCiudadESPMod && $codigoDeptoESPMod) {
				$codep_us3 = $codigoDeptoESPMod;
				$muni_us3 = $codigoCiudadESPMod;
			}
		}
		$a = new LOCALIZACION($codep_us3, $muni_us3, $db);
		$dpto_nombre_us3 = $a->departamento;
		$muni_nombre_us3 = $a->municipio;

		$a = new LOCALIZACION($codep_us7, $muni_us7, $db);
		$dpto_nombre_us7 = $a->departamento;
		$muni_nombre_us7 = $a->municipio;
		$espObjeto->Esp_nit($cc_documento_us3);
		$nuir_e = $espObjeto->getNuir();
		// Inicializacion de la fecha que va a pasar al reemplazable *F_RAD_S*
		$fecha_hoy_corto = "";
		include "$ruta_raiz/class_control/class_gen.php";

		$b = new CLASS_GEN();
		$date = date("m/d/Y");
		$fecha_hoy = $b->traducefecha($date);
		$fecha_e = $b->traducefecha($radi_fech_radi);
		$fechaDocumento2 = $b->traducefecha_sinDia($fechaDocumento);
		$fechaDocumento = $b->traducefechaDocto($fechaDocumento);
		
		if (!empty($radinumesalida)) {
    		if ($radinumesalida != $numrad) {
    		    $fechaResolucion = $b->traduceResolucion( empty($anexradifech) ? date("m/d/Y") : $anexradifech);
    		} else {
    		    $fechaResolucion = $b->traduceResolucion( empty($radi_fech_radi) ? date("m/d/Y") : $radi_fech_radi);
    		}
	    }
		//$fechaResolucion = $b->traduceResolucion(date("m/d/Y")) ;
		
		if ($vp == "n")
			$archivoFinal = $linkArchSimple;
		else
			$archivoFinal = $linkArchivoTmpSimple;

		#echo ">>>> ARCHIVO FINAL:" . $archivoFinal;

		//almacena la extension del archivo a procedar
		//$extension = (strrchr($archivoFinal, "."));
		//$archSinExt = substr($archivoFinal, 0, strpos($archivoFinal, $extension));
		$archSinExt = substr($archivoFinal, 0, strrpos($archivoFinal, "."));
		//Almacena el path completo hacia el archivo a producirse luego de la combinacion

		if (substr($archSinExt, -1) == "d") {
			$caracterDefinitivo = "";
		} else {
			$caracterDefinitivo = "d";
		}

		$date = date_create(date('Y-m-d h:m:s'));
		$date = date_format($date,"YmdHis");
		
		//cambio de nombre de archivo final... para efectos del historico de imagen.
		$tmpNombLinkArchivo = explode("_", $archSinExt);
		if ( count($tmpNombLinkArchivo) == 2 ) {
		    $archSinExt = $tmpNombLinkArchivo[0] . "_" . $date . "_" . mt_rand(1, 99999) . "_" .$tmpNombLinkArchivo[1];
		} else {
		    $archSinExt = $tmpNombLinkArchivo[0] . "_" . $date . "_" . mt_rand(1, 99999) . "_" .$tmpNombLinkArchivo[2];
		}
		
		if ($ext == 'xml' || $ext == 'XML') {
			$archivoFinal = $archSinExt . "." . $ext;
		} else {
			$archivoFinal = $archSinExt . $caracterDefinitivo . "." . $ext;
		}
		
		// Almacena el nombre de archivo a producirse,
		// luego de la combinacion y que ha de actualizarce en la tabla de anexos
		//$archUpdate = substr($archivoFinal, strpos($archivoFinal, strrchr($archivoFinal, "\\")) + 1, strlen($archivoFinal) - strpos($archivoFinal, strrchr($archivoFinal, "/")) + 1);
        $archUpdate = basename($archivoFinal);
		// Almacena el path de archivo a producirse,
		// luego de la combinacion y que ha de actualizarce en la tabla de radicados
		$archUpdateRad = $archivoFinal;
	}
	//$db->conn->BeginTrans();
	$tipo_docto = $anex->get_sgd_tpr_codigo();

	$isql = "select SGD_DIR_DIRECCION,
	                        SGD_DIR_TIPO
	                    from ANEXOS
	                    where ANEX_CODIGO='$anexo' AND
	                        ANEX_RADI_NUME=$numrad";
	$rs = $db->conn->Execute($isql);

	//Verifica y cambia la direccion cuando modifican la dir de la ESP, se movio a
	//esta parte para que funcione tambien en previsualizacion
	$direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
	$sgd_dir_tipo = $rs->fields["SGD_DIR_TIPO"];
	if ($sgd_dir_tipo == 2 && $vieneDeSancionados == 0) {
		$dir_tipo_us1 = $dir_tipo_us2;
		$tipo_emp_us1 = $tipo_emp_us2;
		$nombre_us1 = $nombre_us2;
		$grbNombresUs1 = $nombre_us2;
		$documento_us1 = $documento_us2;
		$cc_documento_us1 = $cc_documento_us2;
		$prim_apel_us1 = $prim_apel_us2;
		$seg_apel_us1 = $seg_apel_us2;
		$telefono_us1 = $telefono_us2;
		$direccion_us1 = $direccion_us2;
		$mail_us1 = $mail_us2;
		$muni_us1 = $muni_us2;
		$codep_us1 = $codep_us2;
		$tipo_us1 = $tipo_us2;
		$otro_us1 = $otro_us2;
	}
	if ($sgd_dir_tipo == 3 && $vieneDeSancionados == 0) {
		$dir_tipo_us1 = $dir_tipo_us3;
		$tipo_emp_us1 = $tipo_emp_us3;
		$nombre_us1 = $nombre_us3;
		$grbNombresUs1 = $nombre_us3;
		$documento_us1 = $documento_us3;
		$cc_documento_us1 = $cc_documento_us3;
		$prim_apel_us1 = $prim_apel_us3;
		$seg_apel_us1 = $seg_apel_us3;
		$telefono_us1 = $telefono_us3;
		$direccion_us1 = $direccion_us3;
		$mail_us1 = $mail_us3;
		$muni_us1 = $muni_us3;
		$codep_us1 = $codep_us3;
		$tipo_us1 = $tipo_us3;
		$otro_us1 = $otro_us3;
	}
	if ($direccionAlterna and $sgd_dir_tipo == 3) {
		$direccion_us3 = $direccionAlterna;
		$muni_us3 = $muniCodiAlterno;
		$codep_us3 = $dptoCodiAlterno;
	}
	$tipoSubStr = substr($sgd_dir_tipo, 0, 1);
	if ($tipoSubStr == '7') {
		$direccion_us_otro = $direccion_us7;
		$nombre_us_otro = ($nombre_us7) ? $nombre_us7 : $nombre;
		$dpto_nombre_us_otro = $dpto_nombre_us7;
		$muni_nombre_us_otro = $muni_nombre_us7;

		//Nov2 ini
		unset ($direccion_us3);
		unset ($muni_nombre_us3);
		unset ($dpto_nombre_us3);
		unset ($nombret_us3_u);
		unset ($telefono_us3);
		unset ($mail_us3);
		unset ($nombret_us3_u);
		unset ($nuir_e);
		unset ($cc_documento_us3);
		//Nov2 fin

		//Toca cambiarle el tipo de destinatario a 1, para que lo muestre en consultas y para que
		//muestre datos en Informacion General
		/*    $sqlUPDIR = "update SGD_DIR_DRECCIONES set  SGD_DIR_TIPO = 1 where SGD_ANEX_CODIGO='$anexo' ";
		     $rsUPDIR=$db->conn->Execute($sqlUPDIR);
		*/
	}
	if (!$tipo_docto)
		$tipo_docto = 0;
	if ($sec and $vp == "n") {
		if ($generar_numero != "no" and $radicar_a == "si") {
			if (!$tpradic) {
				$tpradic = 'null';
			}

			$rad = new Radicacion($db);
			$hist = new Historico($db);
			$rad->radiTipoDeri = 0;
			$rad->radiCuentai = "''";
			$rad->eespCodi = $espcodi;
			
			// $fecha_gen_doc_YMD = substr($fecha_gen_doc,6 ,4)."-".substr($fecha_gen_doc,3 ,2)."-".substr($fecha_gen_doc,0 ,2);
			$rad->radiFechOfic = $sqlFechaHoy;
			// if(!$radicadopadre)  $radicadopadre = null;
			$rad->radiNumeDeri = trim($verrad);
			$rad->descAnex = $desc_anexos;
			$rad->radiPais = "$pais";
			$rad->raAsun = $asunto;
			
			if ($tpradic == 2) {
			    $rad->mrecCodi = 8;
			} else {
			    $rad->fenvCodi = 122;
			}
			
			if ($tpradic == 1) {
				if ($entidad_depsal != 0) {
					$rad->radiDepeActu = $entidad_depsal;
					$rad->radiUsuaActu = 1;
				} else {
					$rad->radiDepeActu = $dependencia;
					$rad->radiUsuaActu = $codusuario;
				}
			} else {
				$rad->radiDepeActu = $dependencia;
				$rad->radiUsuaActu = $codusuario;
			}

			$rad->radiDepeRadi = $dependencia;
			$rad->trteCodi = "null";
			$rad->tdocCodi = $tipo_docto;
			$rad->raAsun = $radAsunto;
			$rad->tdidCodi = "null";
			$rad->carpCodi = $tpradic; //por revisar coomo recoger el valor
			$rad->carPer = 0;
			$rad->trteCodi = "null";
			$rad->ra_asun = "'$asunto'";

			if (!$combina) {
				$fori = ORFEOPATH . substr($linkarchivoOri, 2, 200);
				$fdes = BODEGAPATH . substr($archUpdateRad, 0, 200);
				copy($fori, $fdes);
			}

			//echo "[" . str_replace("\\", "/", $archUpdateRad) . "] <br/>";
			$pathupdate = str_replace("\\", "/", $archUpdateRad);

			$rad->radiPath = "$pathupdate";

			if (strlen(trim($apliCodiaux)) > 0 && $apliCodiaux > 0)
				$aplinteg = $apliCodiaux;
			else
				$aplinteg = "0";
			//echo ("Se propine ($apliCodiaux).... queda ($aplinteg) ");

			$rad->sgd_apli_codi = $aplinteg;
			$codTx = 2;
			$flag = 1;

			// Se genera el numero de radicado del anexo
			$noRad = $rad->newRadicado($tpradic, $tpDepeRad[$tpradic]);
			// Se instancia un objeto para el radicado generado y obtener la fecha real de radicacion
			$radGenerado = new Radicado($db);
			$radGenerado->radicado_codigo($noRad);
			// Asgina la fecha de radicacion
			$fecha_hoy_corto = $radGenerado->getRadi_fech_radi();
			//$fecha_hoy_corto = date("d/m/Y H:i:s"); 
			//BUSCA QUERYS ADICIONALES RESPECTO DE APLICATIVOS INTEGRADOS
			$campos["P_RAD_E"] = $noRad;
			$campos["P_USUA_CODI"] = $codusuario;
			$campos["P_DEPENDENCIA"] = $dependencia;
			$campos["P_USUA_DOC"] = $usua_doc;
			$campos["P_COD_REF"] = $anexo;

			//El nuevo radicado hereda la informacion del expediente del radicado padre
			if (isset ($expRadi) && $expRadi != 0) {
			    
			    $expComunica1 = $adate."66331900100001E";
			    $expComunica2 = $adate."66331900200001E";
			    if ($expRadi == $expComunica1 || $expRadi == $expComunica2) {
			        
			    } else {
			    
    				$resultadoExp = $objExpediente->insertar_expediente($expRadi, $noRad, $dependencia, $codusuario, $usua_doc);
    				$radicados = array();
    				if ($resultadoExp == 1) {
    					$observa = "Se ingresa al expediente del radicado padre ($numrad)";
    					include_once "$ruta_raiz/include/tx/Historico.php";
    					$radicados[] = $noRad;
    					$tipoTx = 53;
    					$Historico = new Historico($db);
    					$Historico->insertarHistoricoExp($expRadi, $radicados, $dependencia, $codusuario, $observa, $tipoTx, 0, 0);
    				} else {
    					die('<hr><font color=red>No se anexo este radicado al expediente. Verifique que el numero del expediente exista e intente de nuevo.</font><hr>');
    				}
			    }
			}

			$estQueryAdd = $objCtrlAplInt->queryAdds($noRad, $campos, $MODULO_RADICACION_DOCS_ANEXOS);
			if ($estQueryAdd == "0") {
				//$db->conn->RollbackTrans();
				die;
			}

			$radicadosSel[0] = $noRad;
			$hist->insertarHistorico($radicadosSel, $dependencia, $codusuario, $dependencia, $codusuario, " ", $codTx);
			if ($noRad == "-1") {
				//$db->conn->RollbackTrans();
			    echo $rad->errorNewRadicado;
				die("<hr><b><font color=red><center>Error no genero un Numero de Secuencia o inserto el xxx radicado </center></font></b><hr>");
			}
			$rad_salida = $noRad;
			
			$sql_hima = "INSERT INTO SGD_HIST_IMG_RAD ( RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, FECHA, ID_TTR_HIAN) VALUES
					($noRad, '$pathupdate', '".$_SESSION['usua_doc']."', '$krd', $sqlFechaHoy, 23)";
			
		} else {
			
			$updateRadiPaht = (strpos($archivoFinal, "bodega")!== FALSE) ? substr($archivoFinal , strpos($archivoFinal, "bodega")+strlen("bodega")) : $archivoFinal;
			$linkarchivo_grabar = str_replace("\\", "/", $updateRadiPaht);

			$posExt = strpos($linkarchivo_grabar, "d." . $ext);
			if ($posExt === false) {

				$temp = $linkarchivo_grabar;
				switch ($ext) {
					case "doc":
						$ruta = str_replace('.doc', 'd.doc', $temp);
					break;
					case "docx":
						$ruta = str_replace('.docx', 'd.docx', $temp);
					break;
					case "rtf":
						$ruta = str_replace('.rtf', 'd.rtf', $temp);
						break;
					case "odt":
						$ruta = str_replace('.odt', 'd.odt', $temp);
						break;
					default:
						$ruta = str_replace(".".$ext, 'd.'.$ext, $temp);;
					break;
				}
				$linkarchivo_grabar = $ruta;
			}
                        
            $posBodega = stripos($updateRadiPaht, "bodega");
            if ($posBodega === FALSE) {
				$emparapete = str_replace("\\", "/", $updateRadiPaht);			
			} else {
				$emparapete = substr($updateRadiPaht, $posBodega+strlen("bodega"));
				$emparapete = str_replace("\\", "/", $emparapete);
			}
			$updateRadiPaht = str_replace("\\", "/", $updateRadiPaht);
                        
			$isql = "update RADICADO set RADI_PATH='$emparapete' where RADI_NUME_RADI = $rad_salida";
			
			$radGenerado = new Radicado($db);
			$radGenerado->radicado_codigo($rad_salida);
			// Asgina la fecha de radicacion
			$fecha_hoy_corto = $radGenerado->getRadi_fech_radi();
			//$fecha_hoy_corto = date("d/m/Y H:i:s"); 
			$rs = $db->conn->Execute($isql);
			if (!$rs) {
				//$db->conn->RollbackTrans();
				die("<span class='etextomenu'>No se ha podido Actualizar el Radicado");
			}
			
			
			$sql_hima = "INSERT INTO SGD_HIST_IMG_RAD ( RADI_NUME_RADI, RUTA, USUA_DOC, USUA_LOGIN, FECHA, ID_TTR_HIAN) VALUES
			($rad_salida, '$emparapete', '".$_SESSION['usua_doc']."', '$krd', $sqlFechaHoy, 23)";
		}

		if ($ent == 1) {
			$rad_salida = $nurad;
		}

		//echo "<br/><br/>>> ARCHIVO update:" . $archUpdate . "<br/><br/>" . $archivoFinal . "<br/><br/>";
		$trozosPath1 = explode("\\", $archUpdate);
		$nombreArchivo1 = $trozosPath1[count($trozosPath1) - 1];
		//echo "<br/> solo archivo :" . $nombreArchivo1 . "<br/><br/>";
		//echo "</br>" . $numrad  . "</br>";
		
		$isql = "update ANEXOS set RADI_NUME_SALIDA=$rad_salida,
				ANEX_SOLO_LECT = 'S',
				ANEX_RADI_FECH = $sqlFechaHoy,
				ANEX_ESTADO = 2,
				ANEX_NOMB_ARCHIVO = '$nombreArchivo1',
				ANEX_TIPO='$numextdoc',
				SGD_DEVE_CODIGO = null
			where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";
		//echo $isql;
		$rs = $db->conn->Execute($isql);
		if (!$rs) {
			//$db->conn->RollbackTrans();
			die("<span class='etextomenu'>No se ha podido actualizar la informacion de anexos");
		}
		### SE AGREGA ESTA SECCIÓN PARA ACTUALIZAR EL CAMPO RESPUESTA DE RADICADOS DE ENTRADA CON LOS RADICADOS DE SALIDA NUEVOS QUE SE GENEREN
		else {
			$resp ="SELECT	RADI_RESPUESTA
					FROM	RADICADO
					WHERE	RADI_NUME_RADI = $numrad";
			$rsResp = $db->conn->Execute($resp);
			$respuesta = $rsResp->fields["RADI_RESPUESTA"];
			
			if (!$respuesta) {
				if ( (substr($rad_salida,-1,1) == 1 ) && (SUBSTR($numrad,-1,1) == 2) ){
					$sqlResp = "UPDATE	RADICADO 
								SET		RADI_RESPUESTA = $rad_salida
								WHERE	RADI_NUME_RADI = $verrad";
					$rsResp = $db->conn->Execute($sqlResp);
				}
			}
		}
        
		if (empty($radinumesalida)) {
		      $fechaResolucion = $b->traduceResolucion( empty($radi_fech_radi) ? date("m/d/Y") : $radi_fech_radi);
		}
		
		$isql = "select * from ANEXOS where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";
		$rs = $db->conn->Execute($isql);
		if ($rs == false) {
			//$db->conn->RollbackTrans();
			die("<span class='etextomenu'>No se ha podido obtener la informacion de anexo");
		}

		$sgd_dir_tipo = $rs->fields["SGD_DIR_TIPO"];
		$anex_desc = $rs->fields["ANEX_DESC"];
		$anex_numero = $rs->fields["ANEX_NUMERO"];
		$direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
		$pasar_direcciones = true;
		$dep_radicado = substr($rad_salida, 4, 3);
		$prioridad = $rs->fields["SGD_PRIORIDAD"];
		//	 ("al radicar($dep_radicado)($rad_salida)");
		$carp_codi = 1;

		if (!$tipo_docto)
			$tipo_docto = 0;

		$linkarchivo_grabar = str_replace("bodega", "", $linkarchivo);
		$linkarchivo_grabar = str_replace("./", "", $linkarchivo_grabar);

		if ($sgd_dir_tipo == 1) {
			$grbNombresUs1 = $nombret_us1_u;
		}

		$nurad = $rad_salida;
		$documento_us2 = "";
		$documento_us3 = "";
		$conexion = $db;

		if ($numerar != 1)
			include "$ruta_raiz/radicacion/grb_direcciones.php";

		$actualizados = 4;
		$sgd_dir_tipo = 1;

		// Borro todo lo generando anteriormete .....  para el caso de regenerar
		$isql = "delete
			         from ANEXOS
			         where
					RADI_NUME_SALIDA=$nurad
					and sgd_dir_tipo like '7%' and sgd_dir_tipo !=7 ";
		$rs = $db->conn->Execute($isql);
		if (!$rs) {
			//$db->conn->RollbackTrans();
			die("<span class='etextomenu'>No se ha borrar los datos previos del radicado");
		}
		// fIN BORRADO Para reproceso....
		$isql = "select ANEX_NUMERO
			         from ANEXOS
			         where
				ANEX_RADI_NUME=$nurad
			      Order by ANEX_NUMERO desc
					 ";
		$rs = $db->conn->Execute($isql);
		if (!$rs->EOF)
			$i = $rs->fields['ANEX_NUMERO'];
		include_once "./include/query/queryGenarchivo.php";
		$isql = $query1;

		$rs = $db->conn->Execute($isql);
		$k = 0;

		while (!$rs->EOF) {

			$anexo_new = $rad_salida . substr("00000" . ($i +1), -5);
			$sgd_dir_codigo = $rs->fields['SGD_DIR_CODIGO'];
			$radi_nume_radi = $rs->fields['RADI_NUME_RADI'];
			$sgd_dir_tipo = $rs->fields['SGD_DIR_TIPO'];
			$anex_tipo = "20";
			$anex_creador = $krd;
			$anex_borrado = "N";
			$anex_nomb_archivo = " ";
			$anexo_num = $i +1;
			//$sgd_dir_tipo  = "7$anexo_num";
			$isql = "insert into ANEXOS (ANEX_RADI_NUME,
					RADI_NUME_SALIDA,
					ANEX_SOLO_LECT,
					ANEX_RADI_FECH,
					ANEX_ESTADO,
					ANEX_CODIGO,
					anex_tipo,
					ANEX_CREADOR,
					ANEX_NUMERO,
					ANEX_NOMB_ARCHIVO,
					ANEX_BORRADO,
					sgd_dir_tipo)
						VALUES ($verrad,
					$rad_salida,
					'S',
					$sqlFechaHoy,
					2,
					'$anexo_new',
					'$anex_tipo',
					'$anex_creador',
					'$anexo_num',
					'$anex_nomb_archivo',
					'$anex_borrado',
					'$sgd_dir_tipo')";
			$rs2 = $db->conn->Execute($isql);

			if (!$rs2) {
				//$db->conn->RollbackTrans();
				die("<span class='etextomenu'>No se pudo insertar en la tabla de anexos");
			}
			$isql = "UPDATE sgd_dir_drecciones
							         set RADI_NUME_RADI=$rad_salida
			  					     where sgd_dir_codigo=$sgd_dir_codigo";

			$rs2 = $db->conn->Execute($isql);

			if (!$rs2) {
				//$db->conn->RollbackTrans();
				die("<span class='etextomenu'>No se pudo actualizar las direcciones");
			}
			$sgd_dir_tipo++;
			$i++;
			$k++;
			$rs->MoveNext();
		}
		echo "<br>Se han generado $k copias<br>";
?>
<p align="center">
<?php

		if ($actualizados > 0) {
			if ($ent != 1) {
				$mensaje = "<input type='button' value='cerrar' onclick='opener.history.go(0); window.close()'>";
				$mensaje = "";
				if ($numerar != 1) {
					$numerar = $numerar;
?>
    <span class='etextomenu'><br>Ha sido Radicado el Documento con el N&uacute;mero<br>
     <?=$rad_salida ?><p><?=$mensaje ?>
	<?php

				}
			} else
				$mensaje = "";
		} else {
?>
	<span class='etextomenu'><br>No se ha podido radicar el Documento con el N&uacute;mero<br>
    </b>
	<?php

		}
	}
}

if ($combina) {
	
	$ra_asun = preg_replace("/\\\\n/", "-", $ra_asun);
	$ra_asun = preg_replace("/\\\\r/", " ", $ra_asun);
	
	### Se consulta la fecha del radicado, para combinar esta fecha en el documento que se va Radicar o Re-generar
	$sqlFec = "	SELECT	RADI_FECH_RADI
				FROM	RADICADO
				WHERE	RADI_NUME_RADI = ".$rad_salida;
	$fechComb = $db->conn->Getone($sqlFec);
	if ($fechComb)
	    $fechComb = $b->traducefecha($fechComb);
	else 
	    $fechComb= "";
		    
	    $datos = array('*RAD_S*' => utf8_encode($rad_salida), '<Radicado>' => utf8_encode($rad_salida),
	        '*RAD_R*' => utf8_encode(substr($rad_salida, 9, 4)),
	        '*RAD_C*' => utf8_encode(substr($rad_salida, 9, 4) . '-'. substr($rad_salida, -1)),
	        '*F_RAD_R*' => utf8_encode($fechaResolucion),
	        '*RAD_E_PADRE*' => utf8_encode($radicado_p), '*CTA_INT*' => utf8_encode($cuentai),
	        '*ASUNTO*' => utf8_encode($ra_asun), '*F_RAD_E*' => utf8_encode($fecha_e),
	        '*RAD_E_PADRE*' => utf8_encode($radicado_p), '*SAN_FECHA_RADICADO*' => utf8_encode($fecha_e),
	        '*NOM_R*' => utf8_encode($nombret_us1_u), '<USUARIO>' => utf8_encode($nombret_us1_u),
	        '*DIR_R*' => utf8_encode($direccion_us1), '*DIR_E*' => utf8_encode($direccion_us3),
	        '<SGD_CIU_DIRECCION>' => utf8_encode($direccion_us1), '*DEPTO_R*' => utf8_encode($dpto_nombre_us1),
	        '*MPIO_R*' => utf8_encode($muni_nombre_us1), '<DPTO_NOMB>' => utf8_encode($dpto_nombre_us1),
	        '<MUNI_NOMB>' => utf8_encode($muni_nombre_us1), '*TEL_R*' => utf8_encode($telefono_us1),
	        '*MAIL_R*' => utf8_encode($mail_us1), '*DOC_R*' => utf8_encode($cc_documentous1),
	        '*NOM_P*' => utf8_encode($nombret_us2_u), '*DIR_P*' => utf8_encode($direccion_us2),
	        '*DEPTO_P*' => utf8_encode($dpto_nombre_us2), '*MPIO_P*' => utf8_encode($muni_nombre_us2),
	        '*TEL_P*' => utf8_encode($telefono_us1), '*MAIL_P*' => utf8_encode($mail_us2),
	        '*DOC_P*' => utf8_encode($cc_documento_us2), '*NOM_E*' => utf8_encode($nombret_us3_u),
	        '<NOMBRE_DE_LA_EMPRESA>' => utf8_encode($nombret_us3_u), '*DIR_E*' => utf8_encode($direccion_us3),
	        '*MPIO_E*' => utf8_encode($muni_nombre_us3), '*DEPTO_E*' => utf8_encode($dpto_nombre_us3),
	        '*TEL_E*' => utf8_encode($telefono_us3), '*MAIL_E*' => utf8_encode($mail_us3),
	        '*NIT_E*' => utf8_encode($cc_documento_us3), '*NUIR_E*' => utf8_encode($nuir_e),
	        '*F_RAD_S*' => utf8_encode($fecha_hoy_corto), '*RAD_E*' => utf8_encode($radicado_p),
	        '*SAN_RADICACION*' => utf8_encode($radicado_p), '*SECTOR*' => utf8_encode($sector_nombre),
	        '*NRO_PAGS*' => utf8_encode($radi_nume_hoja), '*DESC_ANEXOS*' => utf8_encode($radi_desc_anex),
	        '*F_HOY_CORTO*' => utf8_encode($fecha_hoy_corto), '*F_HOY*' => utf8_encode($fechComb),
	        '*NUM_DOCTO*' => utf8_encode($secuenciaDocto), '*F_DOCTO*' => utf8_encode($fechaDocumento),
	        '*F_DOCTO1*' => utf8_encode($fechaDocumento2), '*FUNCIONARIO*' => utf8_encode($usua_nomb),
	        '*LOGIN*' => utf8_encode($krd), '*DEP_NOMB*' => utf8_encode($dependencianomb),
	        '*CIU_TER*' => utf8_encode($terr_ciu_nomb), '*DEP_SIGLA*' => utf8_encode($dep_sigla),
	        '*TER*' => utf8_encode($terr_sigla), '*DIR_TER*' => utf8_encode($terr_direccion),
	        '*TER_L*' => utf8_encode($terr_nombre), '*NOM_REC*' => utf8_encode($nom_recurso),
	        '*EXPEDIENTE*' => utf8_encode($expRadi), '*NUM_EXPEDIENTE*' => utf8_encode($expRadi),
	        '*DIGNATARIO*' => utf8_encode($otro_us1), '*DIR_O*' => utf8_encode($direccion_us_otro),
	        '*NOM_O*' => utf8_encode($nombre_us_otro), '*DEPTO_O*' => utf8_encode($dpto_nombre_us_otro),
	        '*MPIO_O*' => utf8_encode($muni_nombre_us_otro), '*CODPOSTAL_R*' => utf8_encode($codpostal_us1),
	        '*CODPOSTAL_P*' => utf8_encode($codpostal_us2), '*CODPOSTAL_E*' => utf8_encode($codpostal_us3)
	    );
	
	$dicOpciones["archivoInicial"] = BODEGAPATH . $linkArchSimple;
	$dicOpciones["archivoFinal"] = BODEGAPATH . $archivoFinal;
	if (is_array($camposSanc))
	for ($i_count = 0; $i_count < count($camposSanc); $i_count++) {
		$dicOpciones[trim($camposSanc[$i_count])] = trim($datosSanc[$i_count]);
	}
	if (is_array($campos))
	for ($i_count = 0; $i_count < count($campos); $i_count++) {
	    if (!isset($campos[$i_count]) && !isset($datos[$i_count])) {
		  $dicOpciones[trim($campos[$i_count])] = trim($datos[$i_count]);
	    }
	}

	if (	$ext == "ODT" || $ext == "odt" || 
			$ext == "DOCX" || $ext == "docx"  
		) {
		
		require $ruta_raiz.'/radsalida/masiva/CombinaPlantilla.php';
		$objcomb = new CombinaPlantilla();
		if ($vp == 's') {
		    //$new_filename = (strpos($archivoFinal, BODEGAPATH) === FALSE) ? BODEGAPATH.$archivoFinal : $archivoFinal ;
		    $new_filename = basename($archivoFinal);
		    copy(BODEGAPATH.$linkArchSimple, BODEGAPATH ."tmp/". $new_filename);
		    $rutacombina = BODEGAPATH ."tmp/". $new_filename;
		} else {
		    $trozosPath = explode("/", $orlinkarchivo);
		    $trozosPath[count($trozosPath) - 1] = $nombreArchivoFinal;
		    $orlinkarchivo = implode("/", $trozosPath);
		    
		    rename(BODEGAPATH.$linkArchSimple, BODEGAPATH.$orlinkarchivo.$nombreArchivo1);
		    $rutacombina = BODEGAPATH.$orlinkarchivo.$nombreArchivo1;
		}
		
		if ($ext == "ODT" || $ext == "odt" )
		    $objcomb->odt2merge($rutacombina, $datos);
		else 
		    $objcomb->docx2merge($rutacombina, $datos);
		    
		if ($vp == 's') {
		    echo "<B><CENTER><a class='vinculos' href='".BODEGAURL."tmp/". $new_filename."' target='_blank'> Ver Archivo </a><CENTER/></span>";
		} else {
		    echo "<B><CENTER><a class='vinculos' href='".BODEGAURL.$orlinkarchivo.$nombreArchivo1."' target='_blank'> Ver Archivo </a><CENTER/></span>";
		}
		
		print ("<BR> Transacci&oacute;n Exitosa");
		    
		$linkarchivo_grabar = $linkarchivo;
		if ($vp == "n") $db->conn->Execute($sql_hima);
		
		//logica de prorroga
		include_once ($ruta_raiz . "/radsalida/masiva/CombinaPlantilla.php");
		$objcomb = new CombinaPlantilla();
		$validaPlantilla = false;
		$sqLPath = "SELECT RADI_PATH, ".$db->conn->SQLDate('Y-m-d H:i:s', 'RADI_FECH_RADI')." AS FECHA,
                    FORMAT(RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1, RADI_FECHA_VENCE
                    FROM RADICADO WHERE RADI_NUME_RADI = " . $rad_salida;
		$rsPath = $db->conn->Execute($sqLPath);
		if ($rsPath && !$rsPath->EOF) {
		    $path = BODEGAPATH . $rsPath->fields["RADI_PATH"];
		    $fechaRad = $rsPath->fields["FECHA"];
		    $fecha1 = $rsPath->fields['FECHA1'];
		    $fechaVenci = $rsPath->fields['RADI_FECHA_VENCE'];
		    $d = new DateTime($fechaVenci);
		    $format_date = $d->format('Y-m-d');
		    
		    $ext = substr($path, strrpos(trim($path), '.')+1);
		    if ($ext == "DOCX" || $ext == "docx" ) {
		        $validaPlantilla = $objcomb->docx2search($path, array('#1#'));
		    }
		    
		    if ($validaPlantilla) {
		        
		        $isqlDepR = "	SELECT	RADI_DEPE_ACTU
							,RADI_USUA_ACTU
					FROM	RADICADO
					WHERE	RADI_NUME_RADI = $rad_salida ";
		        $rsDepR = $db->conn->Execute($isqlDepR);
		        $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
		        $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
		        $ind_ProcAnex="S";
		        
		       
                echo "<script type='text/javascript'>
		        		window.open(\"./radicacion/tipificar_documento.php?krd=$krd&nurad=$rad_salida&ind_ProcAnex=$ind_ProcAnex&cerrar=1&codusua=$codusua&coddepe=$coddepe&tsub=&codserie=&texp=$texp\",\"Tipificacion_Documento_Anexos\",\"height=500,width=750,scrollbars=yes\");
		        	</script>";
		        
		        
		    }
		}
	}
	elseif ($ext == "XML" || $ext == "xml") {
		//Se incluye la clase que maneja la combinacion masiva
		include ("$ruta_raiz/include/AdminArchivosXML.class.php");
		define('WORKDIR', './bodega/tmp/workDir/');
		define('CACHE', WORKDIR . 'cacheODT/');

		//Se abre archivo de insumo para lectura de los datos
		$fp = fopen("$ruta_raiz/bodega/masiva/$archInsumo", 'r');
		if ($fp) {
			$contenidoCSV = file("$ruta_raiz/bodega/masiva/$archInsumo");
			fclose($fp);
		} else {
			echo "<br><b>No hay acceso para crear el archivo $archInsumo <b>";
			exit ();
		}
		$accion = false;
		$xml = new AdminArchivosXML();
		//Se carga el archivo odt Original
		$archivoACargar = str_replace('../', '', $linkarchivo);
		$xml->cargarXML("$archivoACargar", $nombreArchivo);
		$xml->setWorkDir(WORKDIR);
		$accion = $xml->abrirXML();
		$xml->cargarContenido();

		//Se recorre el archivo de insumo
		foreach ($contenidoCSV as $line_num => $line) {
			if ($line_num > 1) { //Desde la linea 2 hasta el final del archivo de insumo están los datos de reemplazo
				$cadaLinea = explode("=", $line);
				$cadaLinea[1] = str_replace("<", "'", $cadaLinea[1]);
				$cadaLinea[1] = str_replace(">", "'", $cadaLinea[1]);
				$cadaVariable[$line_num -2] = $cadaLinea[0];
				$cadaValor[$line_num -2] = $cadaLinea[1];
			}

		}
		if ($vp == "s") {
			$linkarchivo_grabar = str_replace("bodega", "", $linkarchivotmp);
			$linkarchivo_grabar = str_replace("./", "", $linkarchivo_grabar);
		}

		$xml->setVariable($cadaVariable, $cadaValor);
		$xml->salvarCambios(null, $linkarchivo_grabar);

		echo "<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; }</script>
		<br><B><CENTER><span class='info'>Combinacion de Correspondencia Realizada <br>";

		echo "<B><CENTER><a class='vinculos' href=javascript:abrirArchivo('./bodega" . $linkarchivo_grabar . "')> Ver Archivo 444</a><br>";

	}
} else {
	if ($combina = 1) {
		$radicadosSel[] = $rad_salida;
		$codTx = 42; //Codigo de la transaccion
		$observa = "RADICACION AUTOMATICA - RESPUESTA RAPIDA";
		$hist = new Historico($db);
		$hist->insertarHistorico($radicadosSel, $dependencia, $codusuario, $dependencia, $codusuario, $observa, $codTx);
	}
}
//$db->conn->CompleteTrans();
?>
</body>