<?php
    session_start();
    if (!$ruta_raiz) $ruta_raiz = ".";
    include("$ruta_raiz/config.php");
    if (!isset($_SESSION['dependencia'])) include "$ruta_raiz/rec_session.php";
    if (isset($db)) unset($db);
    include_once("$ruta_raiz/include/db/ConnectionHandler.php");
    $db = new ConnectionHandler("$ruta_raiz");
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    require_once(ORFEOPATH . "class_control/anexo.php");
    require_once(ORFEOPATH . "class_control/CombinaError.php");
    require_once(ORFEOPATH . "class_control/Sancionados.php");
    require_once(ORFEOPATH . "class_control/Dependencia.php");
    require_once(ORFEOPATH . "class_control/Esp.php");
    require_once(ORFEOPATH . "class_control/TipoDocumento.php");
    require_once(ORFEOPATH . "class_control/Radicado.php");
    require_once(ORFEOPATH . "include/tx/Radicacion.php");
    include_once(ORFEOPATH . "include/tx/Historico.php");
    require_once(ORFEOPATH . "class_control/ControlAplIntegrada.php");
    require_once(ORFEOPATH . "include/tx/Expediente.php");
    require_once(ORFEOPATH . "include/tx/Historico.php");

    //$db->conn->debug = true;

    # si no viene combina se combina...
    if (!isset($combina)) {
       $combina = true;
    }
    else {
       $combina = false;
    }

    $dep = new Dependencia($db);
    $espObjeto = new Esp($db);
    $radObjeto = new Radicado($db);
    $radObjeto->radicado_codigo($numrad);
    $tdoc   = new TipoDocumento($db);	//objeto que maneja el tipo de documento del anexos
    $tdoc2  = new TipoDocumento($db);	//objeto que maneja el tipo de documento del radicado
    $tdoc2->TipoDocumento_codigo($radObjeto->getTdocCodi());
    $fecha_dia_hoy = Date("Y-m-d");
    $sqlFechaHoy = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
    $objCtrlAplInt = new ControlAplIntegrada($db);	// Objeto control de aplicaciones integradas
    $objExpediente = new Expediente($db);           // Objeto expediente
    $expRadi = $objExpediente->consulta_exp($numrad);
    // Variable que contiene la descripcion del anexo para pasarla al asunto del radicado
    $radAsunto = "";

    $dep->Dependencia_codigo($dependencia);
    $dep_sigla = $dep->getDepeSigla();
    $nurad  = trim($nurad);
    $numrad = trim($numrad);
    $hora   = date("H")."_".date("i")."_".date("s");
    $ddate  = date('d');	// var que almacena el dia de la fecha
    $mdate  = date('m');	// var que almacena el mes de la fecha
    $adate  = date('Y');	// var que almacena el ano de la fecha
    $fechaArchivo = $adate."_".$mdate."_".$ddate; // var que almacena  la fecha formateada
    //var que almacena el nombre que tendra la pantilla
    $archInsumo = "tmp_" . $usua_doc . "_" . $fechaArchivo . "_" . $hora . ".txt";
    $terr_ciu_nomb = $dep->getTerrCiuNomb();    //Var que almacena el nombre de la ciudad de la territorial
    $terr_sigla = $dep->getTerrSigla();             //Var que almacena el nombre corto de la territorial
    $terr_direccion = $dep->getTerrDireccion();     //Var que almacena la direccion de la territorial
    $terr_nombre = $dep->getTerrNombre();           //Var que almacena el nombre largo de la territorial
    $nom_recurso =  $tdoc2->get_sgd_tpr_descrip();	//Var que almacena el nombre del recurso
?>
<head><title>Gen  -  ORFEO - <?=DATE ?></TITLE>
<link rel="stylesheet" href="estilos_totales.css">
</head>
<body>
<?php

if(!$numrad) {
   $numrad=$verrad;
}

if(strlen(trim($radicar_a))==13 or strlen(trim($radicar_a))==18) {
   $no_digitos = 5;
}
else {
   $no_digitos = 6;
}

//include "plantillas.php";
//echo "linkarchivo" . $linkarchivo;

$linkarchivoOri       = $linkarchivo;
$linkArchSimple       = strtolower($linkarchivo);
$linkArchivoTmpSimple = strtolower($linkarchivotmp);
$linkarchivo    = "$ruta_raiz/".strtolower($linkarchivo);
$linkarchivotmp = "$ruta_raiz/".strtolower($linkarchivotmp);
$fechah         = date("Ymd") . "_" . time("hms");
$trozosPath     = explode("/",$linkarchivo);
$nombreArchivo = $trozosPath[count($trozosPath)-1];
move_uploaded_file("$ruta_raiz/$linkarchivo","$ruta_raiz/bodega/masiva/$nombreArchivo");

//die("El archivo es <a href=$ruta_raiz/bodega/masiva/$nombreArchivo> ($linkarchivo)($nombreArchivo)");
//die("<BR>El archivo es <a href=$ruta_raiz/$linkarchivo> ($linkarchivo)($nombreArchivo)");

$a = new Anexo($db);
$a->anexoRadicado($numrad,$anexo);
$apliCodiaux    = $a->get_sgd_apli_codi();
$anex           = $a;
$secuenciaDocto = $a->get_doc_secuencia_formato($dependencia);
$fechaDocumento = $a->get_sgd_fech_doc();
$tipoDocumento  = $a->get_sgd_tpr_codigo();
$radAsunto      = $a->get_anex_desc();
$tdoc->TipoDocumento_codigo($tipoDocumento);
$tipoDocumentoDesc = $tdoc->get_sgd_tpr_descrip();

if ($radicar_documento) {
	// Generacion de la secuencia para documentos especiales
	// Generar el Numero de Radicacion
	if(($ent!=2) and $nurad and $vpppp=="ddd") {
	   $sec = $nurad;
	   $anoSec = substr($nurad,0,4);
	   // @tipoRad define el tipo de radicado el -X
	   $tipoRad = substr($radicar_documento,-1);
	}
	else {
	   if($vp=="n" and $radicar_a=="si") {
	      if($generar_numero=="no") {
	         $sec = substr($nurad,7,$no_digitos);
	         $anoSec = substr($nurad,0,4);
		 $tipoRad = substr($radicar_documento,-1);
	      }
	      else {
	         $isql = "select * from ANEXOS
		          where ANEX_CODIGO='$anexo' AND
                          ANEX_RADI_NUME=$numrad";
		 $rs = $db->conn->Execute($isql);
		 if (!$rs->EOF){
		    $radicado_salida=$rs->fields['RADI_NUME_SALIDA'];
		    $expAnexoActual = $rs->fields['SGD_EXP_NUMERO'];
		    if ($expAnexoActual != '') {
		       $expRadi = $expAnexoActual;
		    }
		 }
		 else {
		    $db->conn->RollbackTrans();
		    die ("<span class='etextomenu'>No se ha podido obtener la informacion del radicado");
		 }

		 if (!$radicado_salida) {
		    $no_digitos = 6;
		    $tipoRad = "1";
		 }
		 else {
		    $sec = substr($radicado_salida,7,$no_digitos);
		    $tipoRad = substr($radicar_documento,-1);
		    $anoSec = substr($radicado_salida,0,4);
		    $db->conn->RollbackTrans();
		    die ("<span class='etextomenu'><br>Ya estaba radicado<br>");
		    $radicar_a = $radicado_salida;
		 }
	      }
	   }
	   else {
	      if($vp=="s") {
		 $sec = "XXX";
	      }
	      else {
		 // En esta parte es en la cual se entra a asignar el numero de radicado
		 $sec = substr($radicar_a,7,$no_digitos);
		 $anoSec = substr($radicar_a,0,4);
		 $tipoRad = substr($radicar_a,13,1);
	      }
	   }

	   // Generacion de numero de radicado de salida
	   $sec = str_pad($sec,$no_digitos,"0",STR_PAD_LEFT);
	   $plg_comentarios = "";
	   $plt_codi = $plt_codi;

	   if(!$anoSec) {
	      $anoSec = date("Y");
	   }

	   if(!$tipoRad) {
	      $tipoRad = "1";
	   }

	   //Adicion para que no reemplace el numero de radicado de un anexo al ser reasignado a otra dependencia
	   if($generar_numero=="no") {
	      $rad_salida = $numrad;
	   }
	   else {
	      //Es un anexo radicado en otra dependencia y no queremos que le genere un nuevo numero
	      if (  $radicar_a != null && $radicar_a != 'si' ) {
	         $rad_salida = $radicar_a;
	      }
	      else {
	         $rad_salida = $anoSec . $dependencia . $sec .$tipoRad;
	      }
	   }

	   if ($numerar==1){
	      $numResol = $a->get_doc_secuencia_formato();
 	      $rad_salida = date("Y") . $dependencia .
              str_pad($a->sgd_doc_secuencia(),6,"0",STR_PAD_left) . $a->get_sgd_tpr_codigo();
	   }
        }

        // Fin generacion de numero de radicado de salida
	$ext = substr(trim($linkarchivo),-3);
	echo "<font size='3' color='#000000'><span class='etextomenu'>\n";
	$extVal = strtoupper($ext);
	if (($extVal=="XLS" or $extVal=="PPT" or $extVal=="PDF") && ($combina)) {
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

        if( $muni_us7 != null &&  $muni_us7 != null &&  $muni_us1 == null &&  $muni_us1 == null) {
            $muni_us1 = $muni_us7;
            $codep_us1 = $codep_us7;

            //No tenemos el numero de doc, luego buscamos el destino para obtenerlo

            //Con el nombre de destino, buscamos el numero de codigo
             $isql = "select sgd_doc_fun, sgd_esp_codi, sgd_oem_codigo, sgd_ciu_codigo from SGD_DIR_DRECCIONES
                                            where RADI_NUME_RADI=$numrad";
                            $rs = $db->conn->Execute($isql);
                            if (!$rs->EOF){
                    $docFun = $rs->fields['sgd_doc_fun'];
                    $docEsp = $rs->fields['sgd_esp_codi'];
                    $docOem = $rs->fields['sgd_oem_codigo'];
                    $docCiu = $rs->fields['sgd_ciu_codigo'];
                    if($docFun != null){
                    $documento_us7 = $docFun;
                    $tipo_emp_us1 = 6;
                     }else	if($docEsp != null){
                    $documento_us7 = $docEsp;
                    $tipo_emp_us1 = 1;
                     }else	if($docOem != null){
                    $documento_us7 = $docOem;
                    $tipo_emp_us1 = 2;
                     }else	if($docCiu != null){
                    $documento_us7 = $docCiu;
                    $tipo_emp_us1 = 0;
                     }
                            }
        }
		include "$ruta_raiz/radicacion/busca_direcciones.php";
		$a = new LOCALIZACION($codep_us1,$muni_us1,$db);
		$dpto_nombre_us1 = $a->departamento;
		$muni_nombre_us1 = $a->municipio;
		$a = new LOCALIZACION($codep_us2,$muni_us2,$db);
		$dpto_nombre_us2 = $a->departamento;
		$muni_nombre_us2 = $a->municipio;

		//Se agrega, pues no está verificando si se modificó el municipio y/o departamento de la ESP
        $isql = "select MUNI_CODI,
                            DPTO_CODI
                        from ANEXOS
                        where ANEX_CODIGO='$anexo' AND
                                ANEX_RADI_NUME=$numrad";
			$rs = $db->conn->Execute($isql);
			if (!$rs->EOF){
				$codigoCiudadESPMod =  $rs->fields['MUNI_CODI'];
				$codigoDeptoESPMod =  $rs->fields['DPTO_CODI'];
				if ( $espcodi && $codigoCiudadESPMod && $codigoDeptoESPMod ) {
					$codep_us3 = $codigoDeptoESPMod;
					$muni_us3 = $codigoCiudadESPMod;
				}
			}
		$a = new LOCALIZACION($codep_us3,$muni_us3,$db);
		$dpto_nombre_us3 = $a->departamento;
		$muni_nombre_us3 = $a->municipio;

         $a = new LOCALIZACION($codep_us7,$muni_us7,$db);
         $dpto_nombre_us7 = $a->departamento;
         $muni_nombre_us7 = $a->municipio;
		$espObjeto->Esp_nit($cc_documento_us3);
		$nuir_e = $espObjeto->getNuir();
		// Inicializacion de la fecha que va a pasar al reemplazable *F_RAD_S*
		$fecha_hoy_corto = "";
		include "$ruta_raiz/class_control/class_gen.php";

		$b = new CLASS_GEN();
		$date =  date("m/d/Y");
		$fecha_hoy = $b->traducefecha($date);
		$fecha_e = $b->traducefecha($radi_fech_radi);
		$fechaDocumento2 = $b->traducefecha_sinDia($fechaDocumento);
		$fechaDocumento = $b->traducefechaDocto($fechaDocumento);

		if($vp=="n") $archivoFinal = $linkArchSimple;
		else $archivoFinal = $linkArchivoTmpSimple;

		//almacena la extension del archivo a procedar
		$extension = (strrchr ( $archivoFinal, "."));
		$archSinExt = substr($archivoFinal,0, strpos($archivoFinal,$extension));
		//Almacena el path completo hacia el archivo a producirse luego de la combinacion

		if(substr($archSinExt,-1) == "d") {
			$caracterDefinitivo = "";
		} else {
			$caracterDefinitivo = "d";
		}

		if( $ext == 'xml' || $ext == 'XML' || $ext == 'odt' || $ext == 'ODT'){
			$archivoFinal = $archSinExt . "." . $ext;
		}else {
			$archivoFinal = $archSinExt . $caracterDefinitivo . "." . $ext;
		}

		// Almacena el nombre de archivo a producirse,
        // luego de la combinacion y que ha de actualizarce en la tabla de anexos
		$archUpdate = substr($archivoFinal,
					strpos( $archivoFinal,strrchr($archivoFinal, "/")) + 1,
					strlen ($archivoFinal)- strpos( $archivoFinal,
									strrchr($archivoFinal, "/")) + 1);
		// Almacena el path de archivo a producirse,
        // luego de la combinacion y que ha de actualizarce en la tabla de radicados
		 $archUpdateRad  = substr_replace ($archivoFinal,"",0,
							strpos($archivoFinal,"bodega")+strlen("bodega"));
	}
        $db->conn->BeginTrans();
        $tipo_docto = $anex->get_sgd_tpr_codigo();

        //Adiciones para DIR_E SSPD, no quieren que se reemplace DIR_R con DIR_E para Sancionados
		$campos = array();
		$datos  = array();
		$anex->obtenerArgumentos($campos,$datos);
        $vieneDeSancionados = 0;
        //Trae la informacion de Sancionados y genera los campos de combinaciï¿½n
        $camposSanc = array();
        $datosSanc  = array();
        $objSancionados =  new Sancionados($db);
        if ($objSancionados->sancionadosRad($anexo)){
            $objSancionados->obtenerCampos($camposSanc,$datosSanc);
            $vieneDeSancionados = 1;
        } else if($objSancionados->sancionadosRad($numrad)){
            $objSancionados->obtenerCampos($camposSanc,$datosSanc);
            $vieneDeSancionados = 2;
        } else $vieneDeSancionados = 0;

        $isql = "select SGD_DIR_DIRECCION,
                        SGD_DIR_TIPO
                    from ANEXOS
                    where ANEX_CODIGO='$anexo' AND
                        ANEX_RADI_NUME=$numrad";
        $rs = $db->conn->Execute($isql);

        //Verifica y cambia la dirección cuando modifican la dir de la ESP, se movio a
        //esta parte para que funcione también en previsualización
        $direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
        $sgd_dir_tipo   = $rs->fields["SGD_DIR_TIPO"];
        if($sgd_dir_tipo==2 && $vieneDeSancionados == 0) {
            $dir_tipo_us1   = $dir_tipo_us2;
            $tipo_emp_us1   = $tipo_emp_us2;
            $nombre_us1     = $nombre_us2;
            $grbNombresUs1  = $nombre_us2;
            $documento_us1  = $documento_us2;
            $cc_documento_us1 = $cc_documento_us2;
            $prim_apel_us1  = $prim_apel_us2 ;
            $seg_apel_us1   = $seg_apel_us2 ;
            $telefono_us1   = $telefono_us2;
            $direccion_us1  = $direccion_us2;
            $mail_us1   = $mail_us2;
            $muni_us1   = $muni_us2;
            $codep_us1  = $codep_us2;
            $tipo_us1   = $tipo_us2;
            $otro_us1   = $otro_us2;
        }
		if($sgd_dir_tipo==3 && $vieneDeSancionados == 0) {
			$dir_tipo_us1   = $dir_tipo_us3;
			$tipo_emp_us1   = $tipo_emp_us3;
			$nombre_us1     = $nombre_us3;
			$grbNombresUs1  = $nombre_us3;
			$documento_us1  = $documento_us3;
			$cc_documento_us1 = $cc_documento_us3;
			$prim_apel_us1  = $prim_apel_us3 ;
			$seg_apel_us1   = $seg_apel_us3 ;
			$telefono_us1   = $telefono_us3;
			$direccion_us1  = $direccion_us3;
			$mail_us1       = $mail_us3;
			$muni_us1       = $muni_us3;
			$codep_us1      = $codep_us3;
			$tipo_us1       = $tipo_us3;
			$otro_us1       = $otro_us3;
		}
		if($direccionAlterna and $sgd_dir_tipo==3) {
			$direccion_us3 = $direccionAlterna;
			$muni_us3 = $muniCodiAlterno;
			$codep_us3 = $dptoCodiAlterno;
		}
        $tipoSubStr = substr($sgd_dir_tipo,0,1);
        if( $tipoSubStr == '7' ) {
            $direccion_us_otro = $direccion_us7;
            $nombre_us_otro = ($nombre_us7) ? $nombre_us7 : $nombre;
            $dpto_nombre_us_otro = $dpto_nombre_us7;
            $muni_nombre_us_otro = $muni_nombre_us7;

            //Nov2 ini
            unset($direccion_us3);
            unset($muni_nombre_us3);
            unset($dpto_nombre_us3);
            unset($nombret_us3_u);
            unset($telefono_us3);
            unset($mail_us3);
            unset($nombret_us3_u);
            unset($nuir_e);
            unset($cc_documento_us3);
            //Nov2 fin

            //Toca cambiarle el tipo de destinatario a 1, para que lo muestre en consultas y para que
            //muestre datos en Informacion General
      /*    $sqlUPDIR = "update SGD_DIR_DRECCIONES set  SGD_DIR_TIPO = 1 where SGD_ANEX_CODIGO='$anexo' ";
           $rsUPDIR=$db->conn->Execute($sqlUPDIR);
*/
          }
	if (!$tipo_docto) $tipo_docto = 0;
	if($sec and $vp=="n") {
		if($generar_numero!="no" and $radicar_a=="si") {
			if (!$tpradic) {
				$tpradic='null';
			}

			$rad = new Radicacion($db);
			$hist = new Historico($db);
			$rad->radiTipoDeri = 0;
			$rad->radiCuentai = "''";
			$rad->eespCodi = $espcodi;
			$rad->mrecCodi = "null"; // "dd/mm/aaaa"
  		 	// $fecha_gen_doc_YMD = substr($fecha_gen_doc,6 ,4)."-".substr($fecha_gen_doc,3 ,2)."-".substr($fecha_gen_doc,0 ,2);
			 $rad->radiFechOfic =  $sqlFechaHoy;
			// if(!$radicadopadre)  $radicadopadre = null;
			$rad->radiNumeDeri = trim($verrad);
			$rad->descAnex =  $desc_anexos;
			$rad->radiPais =  "$pais";
			$rad->raAsun = $asunto ;

			if ($tpradic==1) {
				if ($entidad_depsal !=0) {
					$rad->radiDepeActu = $entidad_depsal;
					$rad->radiUsuaActu =1;
				} else {
					$rad->radiDepeActu = $dependencia;
					$rad->radiUsuaActu =$codusuario;
				}
			} else {
				$rad->radiDepeActu = $dependencia;
				$rad->radiUsuaActu =$codusuario;
			}

			$rad->radiDepeRadi = $dependencia ;
			$rad->trteCodi =  "null";
			$rad->tdocCodi = $tipo_docto;
			$rad->raAsun = $radAsunto;
			$rad->tdidCodi = "null";
			$rad->carpCodi = $tpradic; //por revisar coomo recoger el valor
			$rad->carPer = 0;
			$rad->trteCodi = "null";
			$rad->ra_asun = "'$asunto'";

                        if (!$combina) {
                           $fori = ORFEOPATH . substr($linkarchivoOri,2, 200);
                           $fdes = BODEGAPATH . substr($archUpdateRad,0, 200);
                           copy($fori, $fdes);
                        }

                        $rad->radiPath = "'$archUpdateRad'";

			if (strlen(trim($apliCodiaux)) > 0 && $apliCodiaux > 0)
			 		$aplinteg = $apliCodiaux;
			else $aplinteg = "0";
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
			$fecha_hoy_corto = $radGenerado->getRadi_fech_radi("d-m-Y");
			//BUSCA QUERYS ADICIONALES RESPECTO DE APLICATIVOS INTEGRADOS
			$campos["P_RAD_E"] = $noRad;
			$campos["P_USUA_CODI"] = $codusuario;
			$campos["P_DEPENDENCIA"] = $dependencia;
			$campos["P_USUA_DOC"] = $usua_doc;
			$campos["P_COD_REF"] = $anexo;

			//El nuevo radicado hereda la informacion del expediente del radicado padre
			if (isset($expRadi) && $expRadi!=0) {
				$resultadoExp = $objExpediente->insertar_expediente($expRadi,
                                                                    $noRad,
                                                                    $dependencia,
                                                                    $codusuario,
                                                                    $usua_doc);
				$radicados = "";
				if($resultadoExp==1) {
					$observa = "Se ingresa al expediente del radicado padre ($numrad)";
					include_once "$ruta_raiz/include/tx/Historico.php";
					$radicados[] = $noRad;
					$tipoTx = 53;
					$Historico = new Historico($db);
					$Historico->insertarHistoricoExp($expRadi,
									$radicados,
									$dependencia,
									$codusuario,
									$observa,
									$tipoTx,0,0);
				}else{
	 				die ('<hr><font color=red>No se anexo este radicado al expediente. Verifique que el numero del expediente exista e intente de nuevo.</font><hr>');
				}
			}

			$estQueryAdd = $objCtrlAplInt->queryAdds($noRad,$campos,$MODULO_RADICACION_DOCS_ANEXOS);
			if ($estQueryAdd=="0"){
				$db->conn->RollbackTrans();
				die;
			}


			$radicadosSel[0] = $noRad;
			$hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, " ", $codTx);
			if ($noRad=="-1") {
				$db->conn->RollbackTrans();
				die("<hr><b><font color=red><center>Error no genero un Numero de Secuencia o inserto el radicado </center></font></b><hr>");
    			}
    			$rad_salida = $noRad;
  		} else {
		  	$linkarchivo_grabar = str_replace("bodega","",$linkarchivo);
			$linkarchivo_grabar = str_replace("./","",$linkarchivo_grabar);

			$posExt = strpos($linkarchivo_grabar,'d.doc');
			if ( $posExt === false ) {

				$temp = $linkarchivo_grabar;
				$ruta = str_replace('.doc', 'd.doc',$temp);
				$linkarchivo_grabar = $ruta;
			}

		  	$isql = "update RADICADO set RADI_PATH='$linkarchivo_grabar'
					where RADI_NUME_RADI = $rad_salida";

			$radGenerado = new Radicado($db);
			$radGenerado->radicado_codigo($rad_salida);
			// Asgina la fecha de radicacion
			$fecha_hoy_corto = $radGenerado->getRadi_fech_radi("d-m-Y");
			$rs = $db->conn->Execute($isql);
			if (!$rs){
				$db->conn->RollbackTrans();
				die ("<span class='etextomenu'>No se ha podido Actualizar el Radicado");
			}
		}

		if($ent==1 ) {
                    $rad_salida = $nurad;
                }

		$isql = "update ANEXOS set RADI_NUME_SALIDA=$rad_salida,
						ANEX_SOLO_LECT = 'S',
						ANEX_RADI_FECH = $sqlFechaHoy,
						ANEX_ESTADO = 2,
						ANEX_NOMB_ARCHIVO = '$archUpdate',
						ANEX_TIPO='$numextdoc',
						SGD_DEVE_CODIGO = null
		           where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";

		$rs=$db->conn->Execute($isql);
		if (!$rs){
			$db->conn->RollbackTrans();
			die ("<span class='etextomenu'>No se ha podido actualizar la informacion de anexos");
		}

		$isql = "select * from ANEXOS where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";
		$rs=$db->conn->Execute($isql);
		if ($rs==false){
    			$db->conn->RollbackTrans();
			die ("<span class='etextomenu'>No se ha podido obtener la informacion de anexo");
		}

		$sgd_dir_tipo = $rs->fields["SGD_DIR_TIPO"];
		$anex_desc = $rs->fields["ANEX_DESC"];
		$anex_numero = $rs->fields["ANEX_NUMERO"];
		$direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
		$pasar_direcciones = true;
		$dep_radicado = substr($rad_salida,4,3);
		//	 ("al radicar($dep_radicado)($rad_salida)");
		$carp_codi = 1;

		if (!$tipo_docto) $tipo_docto=0;

		$linkarchivo_grabar = str_replace("bodega","",$linkarchivo);
		$linkarchivo_grabar = str_replace("./","",$linkarchivo_grabar);

		if($sgd_dir_tipo==1) {
			$grbNombresUs1=$nombret_us1_u;
		}


		$nurad = $rad_salida;
		$documento_us2 = "";
		$documento_us3 = "";
		$conexion = $db;

		if ($numerar!=1)
		 	 include "$ruta_raiz/radicacion/grb_direcciones.php";

		$actualizados = 4;
		$sgd_dir_tipo = 1;

		// Borro todo lo generando anteriormete .....  para el caso de regenerar
		$isql = "delete
	         from ANEXOS
	         where
			   RADI_NUME_SALIDA=$nurad
			   and sgd_dir_tipo like '7%' and sgd_dir_tipo !=7
			 ";
		$rs=$db->conn->Execute($isql);
		if (!$rs){
			$db->conn->RollbackTrans();
			die ("<span class='etextomenu'>No se ha borrar los datos previos del radicado");
		}
			// fIN BORRADO Para reproceso....
    $isql = "select ANEX_NUMERO
	         from ANEXOS
	         where
			   ANEX_RADI_NUME=$nurad
			 Order by ANEX_NUMERO desc
			 ";
			$rs=$db->conn->Execute($isql);
		if (!$rs->EOF)
		$i=$rs->fields['ANEX_NUMERO'];
		include_once "./include/query/queryGenarchivo.php";
        $isql = $query1;
		$rs=$db->conn->Execute($isql);
		$k = 0;

		while(!$rs->EOF) {
			  $anexo_new = $rad_salida.substr("00000". ($i+1),-5);
			  $sgd_dir_codigo = $rs->fields['SGD_DIR_CODIGO'];
			  $radi_nume_radi = $rs->fields['RADI_NUME_RADI'];
			  $sgd_dir_tipo = $rs->fields['SGD_DIR_TIPO'];
			  $anex_tipo = "20";
			  $anex_creador = $krd;
			  $anex_borrado = "N";
			  $anex_nomb_archivo = " ";
			  $anexo_num = $i + 1;
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
				$rs2=$db->conn->Execute($isql);

				if (!$rs2){
					$db->conn->RollbackTrans();
					die ("<span class='etextomenu'>No se pudo insertar en la tabla de anexos");
				}
				$isql = "UPDATE sgd_dir_drecciones
				         set RADI_NUME_RADI=$rad_salida
  					     where sgd_dir_codigo=$sgd_dir_codigo";

				$rs2=$db->conn->Execute($isql);

				if (!$rs2){
					$db->conn->RollbackTrans();
					die ("<span class='etextomenu'>No se pudo actualizar las direcciones");
				}
			  $sgd_dir_tipo++;
			  $i++;
			  $k++;
				$rs->MoveNext();
			 }
			 echo "<br>Se han generado $k copias<br>";
		  ?>
<p>
  <center>
<?php
	if($actualizados>0) {
	if($ent != 1) {
        $mensaje="<input type='button' value='cerrar' onclick='opener.history.go(0); window.close()'>";
		$mensaje = "";
	if ($numerar!=1) { $numerar=$numerar;
?>
    <span class='etextomenu'>Ha sido Radicado el Documento con el N&uacute;mero <br><b>
     <?=$rad_salida ?><p><?=$mensaje ?>
	<?php
    }
} else $mensaje = "";
	}else{
	?>
	<span class='etextomenu'>No se ha podido radicar el Documento con el N&uacute;mero <b>
    </b>
	<?php
	}
	?>
  </center>
  <?php
		}
}


if ($combina) { $db->conn->CommitTrans();
		   //$a->combinar($campos,$datos);
			 $ra_asun =  preg_replace ( "\n", "-", $ra_asun);
			 $ra_asun =  preg_replace ( "\r", " ", $ra_asun);
			 $archInsumo="tmp_".$usua_doc."_".$fechaArchivo."_".$hora.".txt";

			 $fp=fopen("$ruta_raiz/bodega/masiva/$archInsumo",'w+');

			 if (!$fp){
			 	echo "<br><font size='3' ><span class='etextomenu'>ERROR..No se pudo abrir el archivo $ruta_raiz/bodega/masiva/$archInsumo</br>";
				$db->conn->RollbackTrans();
				die;
			 }
			 fputs ($fp,"archivoInicial=$linkArchSimple"."\n");
			 fputs ($fp,"archivoFinal=$archivoFinal"."\n");
			 fputs ($fp,"*RAD_S*=$rad_salida\n");
			 fputs ($fp,"<Radicado>=$rad_salida\n");
			 fputs ($fp,"*RAD_E_PADRE*=$radicado_p\n");
			 fputs ($fp,"*CTA_INT*=$cuentai\n");
			 fputs ($fp,"*ASUNTO*=$ra_asun\n");
			 fputs ($fp,"*F_RAD_E*=$fecha_e\n");
			 fputs ($fp,"*SAN_FECHA_RADICADO*=$fecha_e\n");
			 fputs ($fp,"*NOM_R*=$nombret_us1_u\n");
			 fputs ($fp,"<USUARIO>=$nombret_us1_u\n");
			 fputs ($fp,"*DIR_R*=$direccion_us1\n");
			 fputs ($fp,"*DIR_E*=$direccion_us3\n");
			 fputs ($fp,"<SGD_CIU_DIRECCION>=$direccion_us1\n");
			 fputs ($fp,"*DEPTO_R*=$dpto_nombre_us1\n");
			 fputs ($fp,"*MPIO_R*=$muni_nombre_us1\n");
			 fputs ($fp,"<DPTO_NOMB>=$dpto_nombre_us1\n");
			 fputs ($fp,"<MUNI_NOMB>=$muni_nombre_us1\n");
			 fputs ($fp,"*TEL_R*=$telefono_us1\n");
			 fputs ($fp,"*MAIL_R*=$mail_us1\n");
			 fputs ($fp,"*DOC_R*=$cc_documentous1\n");
			 fputs ($fp,"*NOM_P*=$nombret_us2_u\n");
			 fputs ($fp,"*DIR_P*=$direccion_us2\n");
			 fputs ($fp,"*DEPTO_P*=$dpto_nombre_us2\n");
			 fputs ($fp,"*MPIO_P*=$muni_nombre_us2\n");
			 fputs ($fp,"*TEL_P*=$telefono_us1\n");
			 fputs ($fp,"*MAIL_P*=$mail_us2\n");
			 fputs ($fp,"*DOC_P*=$cc_documento_us2\n");
			 fputs ($fp,"*NOM_E*=$nombret_us3_u\n");
			 fputs ($fp,"<NOMBRE_DE_LA_EMPRESA>=$nombret_us3_u\n");
			 fputs ($fp,"*DIR_E*=$direccion_us3\n");
			 fputs ($fp,"*MPIO_E*=$muni_nombre_us3\n");
			 fputs ($fp,"*DEPTO_E*=$dpto_nombre_us3\n");
			 fputs ($fp,"*TEL_E*=$telefono_us3\n");
			 fputs ($fp,"*MAIL_E*=$mail_us3\n");
			 fputs ($fp,"*NIT_E*=$cc_documento_us3\n");
			 fputs ($fp,"*NUIR_E*=$nuir_e\n");
			 fputs ($fp,"*F_RAD_S*=$fecha_hoy_corto\n");
			 fputs ($fp,"*RAD_E*=$radicado_p\n");
			 fputs ($fp,"*SAN_RADICACION*=$radicado_p\n");
			 fputs ($fp,"*SECTOR*=$sector_nombre\n");
			 fputs ($fp,"*NRO_PAGS*=$radi_nume_hoja\n");
			 fputs ($fp,"*DESC_ANEXOS*=$radi_desc_anex\n");
			 fputs ($fp,"*F_HOY_CORTO*=$fecha_hoy_corto\n");
			 fputs ($fp,"*F_HOY*=$fecha_hoy\n");
			 fputs ($fp,"*NUM_DOCTO*=$secuenciaDocto\n");
			 fputs ($fp,"*F_DOCTO*=$fechaDocumento\n");
			 //fputs ($fp,"*NOM_REC*=$tipoDocumentoDesc\n");
			 fputs ($fp,"*F_DOCTO1*=$fechaDocumento2\n");
			 fputs ($fp,"*FUNCIONARIO*=$usua_nomb\n");
			 fputs ($fp,"*LOGIN*=$krd\n");
			 fputs ($fp,"*DEP_NOMB*=$dependencianomb\n");
			 fputs ($fp,"*CIU_TER*=$terr_ciu_nomb\n");
			 fputs ($fp,"*DEP_SIGLA*=$dep_sigla\n");
			 fputs ($fp,"*TER*=$terr_sigla\n");
			 fputs ($fp,"*DIR_TER*=$terr_direccion\n");
			 fputs ($fp,"*TER_L*=$terr_nombre\n");
			 fputs ($fp,"*NOM_REC*=$nom_recurso\n");
			 fputs ($fp,"*EXPEDIENTE*=$expRadi\n");
			 fputs ($fp,"*NUM_EXPEDIENTE*=$expRadi\n");
			 fputs ($fp,"*DIGNATARIO*=$otro_us1\n");
             //Reemplazables para la seleccion de otro destinatario
             fputs ($fp,"*DIR_O*=$direccion_us_otro\n");
             fputs ($fp,"*NOM_O*=$nombre_us_otro\n");
             fputs ($fp,"*DEPTO_O*=$dpto_nombre_us_otro\n");
             fputs ($fp,"*MPIO_O*=$muni_nombre_us_otro\n");



			 for ($i_count=0;$i_count<count ($camposSanc);$i_count++){
			 	fputs ($fp,trim($camposSanc[$i_count])."=".trim($datosSanc[$i_count])."\n");
			 }

			 for ($i_count=0;$i_count<count ($campos);$i_count++){
			 	fputs ($fp,trim($campos[$i_count])."=".trim($datos[$i_count])."\n");
			}

			 fclose($fp);
             //die("$ruta_raiz/bodega/masiva/$archInsumo");
			 /*
			 $dep->conexion->close();
			 $espObjeto->conexion->close();
			 $radObjeto->conexion->close();
			 $tdoc2->conexion->close();
			 $anex->conexion->close();
			 */

			 // El include del servlet hace que se altere el valor de la variable
             // $estadoTransaccion como 0 si se pudo procesar el documento, -1 de lo
			 // contrario
			 $estadoTransaccion=-1;

		if($ext=="ODT" || $ext=="odt") {
				//Se incluye la clase que maneja la combinación masiva
				include ( "$ruta_raiz/radsalida/masiva/OpenDocText.class.php" );

				define ( 'WORKDIR', './bodega/tmp/workDir/' );
				define ( 'CACHE', WORKDIR . 'cacheODT/' );

				//Se abre archivo de insumo para lectura de los datos
				$fp = fopen("$ruta_raiz/bodega/masiva/$archInsumo",'r');
			 	if ($fp) {
			 			$contenidoCSV = file( "$ruta_raiz/bodega/masiva/$archInsumo" );
						fclose($fp);
			 	} else {
			 		echo "<br><b>No hay acceso para crear el archivo $archInsumo <b>";
			 		exit();
			 	}
			 	$accion = false;
				$odt = new OpenDocText();
				$odt->setDebugMode(true);
				//Se carga el archivo odt Original
				$archivoACargar = str_replace('../','',$linkarchivo);
				$odt->cargarOdt( "$archivoACargar", $nombreArchivo );
				$odt->setWorkDir( WORKDIR );
				$accion = $odt->abrirOdt();
				if(!$accion){
					die( "<CENTER><table class=borde_tab><tr><td class=titulosError>Problemas en el servidor abriendo archivo ODT para combinaci&oacute;n.</td></tr></table>" );

				}
				$odt->cargarContenido();

			 	//Se recorre el archivo de insumo
				foreach ( $contenidoCSV as $line_num => $line ) {
                    //Desde la línea 2 hasta el final del archivo de insumo están los datos de reemplazo
				    if ( $line_num > 1 ) {
				   		$cadaLinea =  explode( "=",$line ) ;
							$cadaLinea[1] = str_replace("<", "'", $cadaLinea[1]);
							$cadaLinea[1] = str_replace(">", "'", $cadaLinea[1]);
				   			$cadaVariable[$line_num-2] = $cadaLinea[0];
				   			$cadaValor[$line_num-2] = $cadaLinea[1];
				   }

				}
				$tipoUnitario = '1';
				if($vp=="s"){
					//echo "<br>archivo grabar: " . $linkarchivo_grabar;
					//echo "<br>archivo tmp: " . $linkarchivotmp;
					$linkarchivo_grabar = str_replace("bodega/","",$linkarchivotmp);
			 	 	$linkarchivo_grabar = str_replace("./","",$linkarchivo_grabar);
			 	 	$odt->setVariable( $cadaVariable, $cadaValor );
			 		//echo "<br>VISTA PREVIA 1";

			 		$archivoDefinitivo = $odt->salvarCambios( null, $linkarchivo_grabar, '1' );
			 		//echo "<br>VISTA PREVIA 2";
				}else {
					//echo "<br>archivo grabar: " . $linkarchivo_grabar;
					$odt->setVariable( $cadaVariable, $cadaValor );
			 		$odt->salvarCambios( null, $linkarchivo_grabar, '1' );
			 		//echo "<br>COMBINACION DEFINITIVA";
				}
//				$odt->borrar();
				//$estadoTrans=$masiva->confirmarMasiva();
				$db->conn->CommitTrans();

				echo "<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; }</script>
<br><B><CENTER><span class='info'>Combinacion de Correspondencia Realizada <br>";
				echo "<B><CENTER><a class='vinculos' href=javascript:abrirArchivo('./bodega/". $linkarchivo_grabar."?time=".time() ."')> Ver Archivo </a><br>";

			}elseif ( $ext=="XML" || $ext=="xml" ){
                        //Se incluye la clase que maneja la combinación masiva
                        include ( "$ruta_raiz/include/AdminArchivosXML.class.php" );
                        define ( 'WORKDIR', './bodega/tmp/workDir/' );
                        define ( 'CACHE', WORKDIR . 'cacheODT/' );

                        //Se abre archivo de insumo para lectura de los datos
                        $fp=fopen("$ruta_raiz/bodega/masiva/$archInsumo",'r');
                        if ($fp) {
                                        $contenidoCSV = file( "$ruta_raiz/bodega/masiva/$archInsumo" );
                                        fclose($fp);
                        } else {
                                echo "<br><b>No hay acceso para crear el archivo $archInsumo <b>";
                                exit();
                        }
                        $accion = false;
                        $xml = new AdminArchivosXML();
                        //Se carga el archivo odt Original
                        $archivoACargar = str_replace('../','',$linkarchivo);
                        $xml->cargarXML( "$archivoACargar", $nombreArchivo );
                        $xml->setWorkDir( WORKDIR );
                        $accion = $xml->abrirXML();
                        $xml->cargarContenido();

                        //Se recorre el archivo de insumo
                        foreach ( $contenidoCSV as $line_num => $line ) {
                           if ( $line_num > 1 ) { //Desde la línea 2 hasta el final del archivo de insumo están los datos de reemplazo
                                        $cadaLinea =  explode( "=",$line ) ;
                                                $cadaLinea[1] = str_replace("<", "'", $cadaLinea[1]);
                                                $cadaLinea[1] = str_replace(">", "'", $cadaLinea[1]);
                                                $cadaVariable[$line_num-2] = $cadaLinea[0];
                                                $cadaValor[$line_num-2] = $cadaLinea[1];
                           }

                        }
                        if($vp=="s"){
                                $linkarchivo_grabar = str_replace("bodega","",$linkarchivotmp);
                                $linkarchivo_grabar = str_replace("./","",$linkarchivo_grabar);
                        }

                        $xml->setVariable( $cadaVariable, $cadaValor );
                        $xml->salvarCambios( null, $linkarchivo_grabar );

                        //$estadoTrans=$masiva->confirmarMasiva();
                        $db->conn->CommitTrans();

                        echo "<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; }</script>
<br><B><CENTER><span class='info'>Combinacion de Correspondencia Realizada <br>";

                        echo "<B><CENTER><a class='vinculos' href=javascript:abrirArchivo('./bodega". $linkarchivo_grabar ."')> Ver Archivo </a><br>";

                }
			else {
				include ("http://$servProcDocs/docgen/servlet/WorkDistributor?accion=1&ambiente=$ambiente&archinsumo=$archInsumo&vp=$vp");
				 if ($estadoTransaccion!=0){
			 	$db->conn->RollbackTrans();
				$objError = new CombinaError (NO_DEFINIDO);
				echo ($objError->getMessage());
				die;
			 }

			 print ("<BR> El estado de la transaccion....$estadoTransaccion");
			 $linkarchivo_grabar = $linkarchivo;

		   if (!strrpos ($rad_salida,"XXX")){
		   		copy(ORFEOPATH . "$linkarchivo",ORFEOPATH . "bodega/masiva/$nombreArchivo.cb");
                // Revisa si el archivo no se encuentra para asi copiarlo
                if (file_exists(ORFEOPATH . "bodega/masiva/$nombreArchivo")) {
		   	    	copy(ORFEOPATH . "bodega/masiva/$nombreArchivo",ORFEOPATH . "$linkarchivo");
                }
		   }

		   $db->conn->CommitTrans();

			if  (!strrpos ( $rad_salida,"XXX") && $radObjeto->radicado_codigo($rad_salida) )
                copy(ORFEOPATH . "bodega/masiva/$nombreArchivo.cb",ORFEOPATH . "$linkarchivo");
			}
			// session_start();
}
else {
   if ($combina=1) {
      $radicadosSel[] = $rad_salida;
      $codTx          = 42; //Código de la transacción
      $observa        = "RADICACION AUTOMATICA - RESPUESTA RAPIDA";
      $hist = new Historico($db);
      $hist->insertarHistorico($radicadosSel,  $dependencia, $codusuario, $dependencia, $codusuario, $observa, $codTx);
   }
   $db->conn->CommitTrans();
}
//echo "<B><CENTER><a href=$ruta_raiz/bodega/masiva/$archInsumo> Insumo($archivoFinal)($archUpdateRad)(http://172.16.1.200:8080/docgen/servlet/WorkDistributor?accion=1&ambiente=$ambiente&archinsumo=$archInsumo&vp=$vp)</a><br>";
?>
</body>
