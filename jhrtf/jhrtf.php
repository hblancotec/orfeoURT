<?php

/*  CLASS jhrtf
 *  @autor JAIRO LOSADA - SIXTO
 *  @fecha 2003/10/16
 *  @version 0.1
 *  Permite hacer combinacion de correspondencia desde php con filas rtf-
 *  @VERSION 0.2
 *  @fecha 2004/01/22
 *  Se anade combinacion masiva
 *  @fecha 2004/08/30
 *  Se anaden las funciones:
 *  setTipoDocto(),verListado(),setDefinitivo(),mostrarError(),getNumColEnc(),validarEsp(),validarLugar(),validarTipo()
 *  validarRegistrosObligsCsv(),hayError(),cargarOblPlant(),cargarObligCsv(),cargarCampos(),validarArchs()
 *
 */
require_once("$ruta_raiz/include/pdf/class.ezpdf.inc");
require_once("$ruta_raiz/class_control/Esp.php");
require_once("$ruta_raiz/class_control/Dependencia.php");
require_once("$ruta_raiz/include/tx/Historico.php");
require_once("$ruta_raiz/class_control/Radicado.php");
require_once("$ruta_raiz/include/tx/Expediente.php");
require_once("$ruta_raiz/include/tx/Flujo.php");

class jhrtf {

    var $archivo_insumo;  // Ubicacion fisica del archivo que indica como habra de realizarce la combinacion
    var $alltext;          // ubicacion fisica del archivo a convertir
    var $texto_masivo;     // utilizado para combinar el vualquier texto
    var $encabezado;
    var $datos;
    var $ruta_raiz;
    var $definitivo;
    var $codigo_envio;
    var $radi_nume_grupo;
    //Contiene los campos obligatorios del archivo CSV
    var $camObligCsv;
    //Contiene los campos obligatorios de la plantilla
    var $camObligPlantilla;
    //Contiene los posibles errores hallados en el encabezado
    var $errorEncab = array();
    //Contiene los posibles errores hallados en la plantilla
    var $errorPlant = array();
    //Contiene los posibles errores hallados en los lugares referenciados en el CSV
    var $errorLugar = array();
    //Contiene los posibles errores hallados en las ESP referenciados en el CSV
    var $errorESP = array();
    //Contiene los posibles errores de completitud del CSV
    var $errorComplCsv = array();
    //Contiene los posibles errores del campo tipo de registro del CSV
    var $errorTipo = array();
    //Contiene los posibles errores del campo direccion del CSV
    var $errorDir = array();
    //Contiene los posibles errores del campo es anexo de un radicado
    var $errorRadAnexo = array();
    //Contiene los posibles errores del campo nombre del CSV
    var $errorNomb = array();
    var $arcPDF;
    //Contiene el path del archivo plantilla
    var $arcPlantilla;
    //Contiene el path del archivo CSV
    var $arcCSV;
    //Contiene el path del archivo Final
    var $arcFinal;
    //Contiene el path del archivo Temporal
    var $linkfile;
    var $arcTmp;
    var $conexion;
    var $pdf;
    var $arregloEsp;
    var $arrCodDepto;
    var $arrCodMuni;
    var $arrCodPais;
    var $arrCodCont;
    var $tipoDocto;
    // Guarda la el objeto CLASS_CONTROL
    var $btt;
    //almacena la conexion que permite efectuar algunas labores de masiva
    var $handle;
    //almacena el resultado obtenido en la combinacion masiva
    var $resulComb;
    var $objExp;
    var $codProceso;
    var $codFlujo;
    var $codArista;
    var $resulNroRad = array();
    var $separador = ',';
    var $wsdlOffice;

    /**
     * Constructor que carga en la clase los parametros relevantes del proceso de combinacion de documentos
     * @param	$archivo_insumo	string	es el path hacia el archivo que contiene los ratos de la combinacion
     * @param	$ruta_raiz	string	es el path hacia la raiz del directorio de ORFEO
     * @param	$arcPDF	string	es el path hacia el archivo PDF que habr� de mostrar el resultado de la combinacion
     * @param	$db	ConnectionHandler	Manejador de la conexion con la base de datos
     */
    function jhrtf($archivo_insumo, $ruta_raiz, $arcPDF, &$db) {
        $this->archivo_insumo = $archivo_insumo;
        $this->ruta_raiz = $ruta_raiz;
        $this->arcPDF = $arcPDF;
        $this->cargarInsumo();
        $this->conexion = & $db;
    }

    /**
     * Genera archivo de combinacion bajo office
     *  No se invoca funcion de office en genarchivo simple se mueve dentro de esta
     *  para facilitar debug y control
     */
    function callWord($plantilla, $archivoFinal, $dicOpciones) {
        $namefile = str_replace("/", "\\", $plantilla);
        $new_filename = $namefile;
        $sigue = True;
        try {
            $word = new COM("word.application") or die("Unable to instantiate Word");
        } catch (Exception $e) {
            //echo "<br/> $$$$$ 00 <br/>" . $e;
            $word = NULL;
            unset($word);
            $sigue = False;
        }

        if ($sigue == True) {
            try {
                $word->visible = False;
                $word->Documents->Open($namefile);
            } catch (Exception $e) {
                //echo "<br/> $$$$$ 01 <br/>";
                $word->Quit();
                $word = NULL;
                unset($word);
                $sigue = False;
            }
        }

        if ($sigue == True) {
            try {
                $word->Selection->HomeKey($Unit = 6);
            } catch (Exception $e) {
                //echo "<br/> $$$$$ 02 <br/>";
                $word->Quit();
                $word = NULL;
                unset($word);
                $sigue = False;
            }
        }

        if ($sigue == True) {
            try {
                $find = $word->Selection->Find;
                foreach ($dicOpciones as $k => $v) {
                    if (($v != NULL) && ($v != "") && ($v != " ")) {
                        $word->Selection->HomeKey($unit = 6); # start at beginning
                        $find->Text = $k;
                        while ($word->Selection->Find->Execute()) {
                            $word->Selection->TypeText($text = $v);
                        }
                    }
                }
            } catch (Exception $e) {
                //echo "<br/> $$$$$ 03 <br/>";
                $sigue = False;
            };
        };

        if ($sigue == True) {
            $new_filename = str_replace("/", "\\", $archivoFinal);
            if ($namefile == $new_filename) {
                $word->Documents[1]->Save();
            } else {
                $word->Documents[1]->SaveAs($new_filename);
            }
            $word->Quit();
            $word = null;
            unset($word);
        }
        return $new_filename;
    }

    function callWord2($plantilla, $archivoFinal, $dicOpciones) {
        try {
            $client = new SoapClient($this->wsdlOffice, array('trace' => 1, 'exceptions' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_1));
            $arregloDatos = array(
                'rutaOrigen' => str_replace("\\\\", "\\", $plantilla),
                'rutaDestino' => str_replace("\\\\", "\\", $archivoFinal),
                'variables' => $dicOpciones
            );

            $result = $client->combinaPlantilla($arregloDatos);
            switch ($result->combinaPlantillaResult) {
                case 1: {
                        //todo ok
                    }break;
                case 2: {   //Error. Archivo origen y Destino deben tener la misma extensi&oacute;n;
                        break;
                    }break;
                case 3: {   //Error. Formato no permitido. Los formatos permitidos son doc, docx, odt, rtf o xml";
                        break;
                    }break;
                case 4: {   //Error. lanzó un error dentro del combinador...;
                        break;
                    }break;
                case 9: {   // Error. Archivo origen no hallado";
                    }break;
                default:
                    ;
                    break;
            }
        } catch (SoapFault $soapFault) {
            //echo "<h3>Error al accesar servicio de combinaci&oacute;n.</h3><br /> Por favor intente nuevamente en contados segundos.";
            //echo "Request :<br>", htmlentities($soapFault->getMessage() + "<br>" + $soapClient->__getLastRequest()), "<br>";
            //echo "Response :<br>", htmlentities($soapClient->__getLastResponse()), "<br>";
        }
        return $result->combinaPlantillaResult;
    }

    /**
     * Genera archivo de combinacion bajo office
     *  No se invoca funcion de office en genarchivo simple se mueve dentro de esta
     *  para facilitar debug y control
     */
    function unifyWord($plantilla, $filesToZip, $archivoFinal) {
        $namefile = str_replace("/", "\\", $plantilla);

        $sigue = True;

        try {
            $word = new COM("word.application") or die("Unable to instantiate Word");
        } catch (Exception $e) {
            //echo "<br/> $$$$$ 00 <br/>" . $e; ;
            $word = NULL;
            unset($word);
            $sigue = False;
        }

        if ($sigue == True) {
            try {
                $word->visible = False;
                $word->Documents->Open($namefile);
            } catch (Exception $e) {
                //echo "<br/> $$$$$ 01 <br/>" . $e;
                $word->Quit();
                $word = NULL;
                unset($word);
                $sigue = False;
            }
        }

        foreach ($filesToZip as $key => $value) {
            $word->Selection->InsertFile($value);
            $word->Selection->InsertBreak(1);
            $sigue = True;
        }

        if ($sigue == True) {
            $new_filename = str_replace("/", "\\", $archivoFinal);

            try {
                $word->Documents[1]->SaveAs($new_filename, 6);
            } catch (Exception $e) {
                //echo $e;
                $word->Quit();
                $word = null;
                unset($word);
            }
            $word->Quit();
            $word = null;
            unset($word);
        }
    }

    function generaOfficeFiles($nombrebase, $plantilla, $dependencia, $archivoFinalF, $temporal) {
        $ext = substr($plantilla, strrpos($plantilla, "."));
        $dep = new Dependencia($this->conexion);
        $dep->Dependencia_codigo($dependencia);
        $dep_sigla = $dep->getDepeSigla();
        $filesToZip = array();
        for ($ii = 0; $ii < count($this->datos); $ii++) {
            $hora = date("H") . "_" . date("i") . "_" . date("s");
            $ddate = date('d');
            $mdate = date('m');
            $adate = date('Y');
            $fecha = $adate . "_" . $mdate . "_" . $ddate;

            $archivoFinal = $nombrebase . $fecha . "._" . $hora . "_" . $ii . $ext;
            $dicOpciones = array();
            foreach ($this->encabezado[0] as $campos_d) {
                $campo = str_replace("*", "", $campos_d);
                $col = $this->getNumColEnc($campo);
                $dicOpciones[] = $campos_d;
                $dicOpciones[] = $this->datos[$ii][$col];
            }

            $result = $this->callWord2($plantilla, $archivoFinal, array($dicOpciones));

            if ($result == 1) {
                $filesToZip[] = $archivoFinal;
            }
        }
        return $filesToZip;
    }

    /**
     * Funcion que carga en la clase el manejador de conexion con la base de datos, en caso de ser necesario
     * @param	$db	ConnectionHandler	Manejador de la conexion con la base de datos
     */
    function setConexion($db) {
        $this->conexion = & $db;
    }

    /*
     * Funcion encargada de gestionar en la base de datos la transaccion que implica la combinacion del documento, desde el archivo CSV
     * @param	$dependencia	string	es la dependencia del usuario que realiza la combinacion
     * @param	$codusuario	string	es el codigo del usuario que realiza la combinacion
     * @param	$usua_doc	string	es numero del documento del usuario que realiza la combinacion
     * @param	$usua_nomb	string	es el nombre del usuario que realiza la combinacion
     * @param	$depe_codi_territorial	string	es el nombre de la territorial a la que pertenece el usuario usuario que realiza la combinacion
     */

    function combinar_csv($dependencia, $codusuario, $usua_doc, $usua_nomb, $depe_codi_territorial, $codiTRD, $TipoRad) {

        //Var que contiene el arreglo de radicados genrados a partir de la masiva
        $arrRadicados = array();
        //echo "<hr> TipoRas es $TipoRad <hr>";
        //Instancia de la dependencia
        $objDependecia = new Dependencia($this->conexion);
        $objDependecia->Dependencia_codigo($dependencia);
        //Almacena la secuencia de radicacion para esta dependencia
        $secRadicacion = "secr_tp" . $TipoRad . "_" . $objDependecia->getSecRadicTipDepe($dependencia, $TipoRad);
//	$this->conexion->conn->debug = true;
        //$archivo = $this->arcFinal;
        $archivo = $this->linkfile;

        //$archivo =  trim(substr($archivo,strpos($archivo,"bodega")+6,strlen($archivo)-strpos($archivo,"bodega")+6));
        // INICIALIZA EL PDF
        $this->pdf = new Cezpdf("LETTER", "landscape");
        $objHist = new Historico($this->conexion);
        $year = date("Y");
        $day = date("d");
        $month = date("m");
        // orientacion izquierda
        $orientCentro = array("left" => 0);
        // justificacion centrada
        $justCentro = array("justification" => "center");
        $estilo1 = array("justification" => "left", "leading" => 8);
        $estilo2 = array("left" => 0, "leading" => 12);
        $estilo3 = array("left" => 0, "leading" => 15);
        $this->pdf->ezSetCmMargins(1, 1, 3, 2); //top,botton,left,right
        /* Se establece la fuente que se utilizara para el texto. */
        $this->pdf->selectFont($this->ruta_raiz . "/include/pdf/fonts/Times-Roman.afm");
        $this->pdf->ezText("LISTADO DE RADICACION MASIVA\n", 15, $justCentro);
        $this->pdf->ezText("Dependencia: $dependencia \n", 12, $estilo2);
        $this->pdf->ezText("Usuario Responsable: $usua_nomb \n", 12, $estilo2);
        $this->pdf->ezText("Fecha: $day-$month-$day \n", 12, $estilo2);
        $this->pdf->ezText($txtformat, 12, $estilo2);
        $data = array();
        $columna = array();
        $contador = 0;
        require_once $this->ruta_raiz . "/class_control/class_control.php";
        $this->btt = new CONTROL_ORFEO($this->conexion);
        echo "<table border=0 width 80% cellpadding='0' cellspacing='5' class='borde_tab' >";
        echo "<tr><td class='titulos4'>Registro</td><td class='titulos4'>Radicado</td><td class='titulos4' >Nombre</td><td class='titulos4'>Direccion</td><td class='titulos4'>Depto</td><td class='titulos4'>Municipio</td><td class='titulos4'>Expediente</td></tr>";
        //Referencia el archivo a abrir
        $ruta = $this->ruta_raiz . "/bodega/masiva/" . $this->archivo_insumo;
        clearstatcache();
        unlink($ruta);
        $fp = fopen($ruta, 'wb');
        if ($fp) {
            fputs($fp, "plantilla=$this->arcPlantilla" . "\n");
            fputs($fp, "csv=$this->arcCSV" . "\n");
            fputs($fp, "archFinal=$this->arcFinal" . "\n");
            fputs($fp, "archTmp=$this->arcTmp" . "\n");
            // Comentariada por HLP. Cambiar , por ;
            fputs($fp, implode($this->separador, $this->encabezado[0]) . ",*RAD_S*,*F_RAD_S*" . "\n");
            //fputs ($fp,implode( ";", $this->encabezado[0]).";*RAD_S*;*F_RAD_S*"."\n");
            //Recorre el arrego de los datos
            for ($ii = 0; $ii < count($this->datos); $ii++) {
                $i = 0;
                $numeroExpediente = "";
                // Aqui se accede a la clase class_control para actualizar expedientes.
                $ruta_raiz = $this->ruta_raiz;

                // Por cada etiqueta de los campos del encabezado del CSV efect�a un reemplazo
                foreach ($this->encabezado[0] as $campos_d) {
                    if (strlen(trim($this->datos[$ii][$i])) < 1)
                        $this->datos[$ii][$i] = "<ESPACIO>";
                    $dato_r = trim($this->datos[$ii][$i]);
                    $texto_tmp = str_replace($campos_d, $dato_r, $texto_tmp);
                    if ($campos_d == "*TIPO*")
                        $tip_doc = $dato_r;
                    if ($campos_d == "*NOMBRE*")
                        $nombre = $dato_r;
                    if ($campos_d == "*DOCUMENTO*")
                        $doc_us1 = $dato_r;
                    if ($campos_d == "*NOMBRE*")
                        $nombre_us1 = $dato_r;
                    if ($campos_d == "*PRIM_APEL*")
                        $prim_apell_us1 = $dato_r;
                    if ($campos_d == "*SEG_APEL*")
                        $seg_apell_us1 = $dato_r;
                    if ($campos_d == "*DIGNATARIO*")
                        $otro_us1 = $dato_r;
                    if ($campos_d == "*CARGO*")
                        $cargo_us1 = $dato_r;
                    if ($campos_d == "*DIR*")
                        $direccion_us1 = $dato_r;
                    if ($campos_d == "*TELEFONO*")
                        $telefono_us1 = $dato_r;
                    if ($campos_d == "*MUNI*")
                        $muni_codi = $dato_r;
                    if ($campos_d == "*DEPTO*")
                        $dpto_codi = $dato_r;
                    if ($campos_d == "*ASUNTO*")
                        $asu = $dato_r;
                    if ($campos_d == "*ID*")
                        $sgd_esp_codigo = $dato_r;
                    if ($campos_d == "*DESC_ANEXOS*")
                        $desc_anexos = $dato_r;
                    if ($campos_d == "*MUNI_NOMBRE*")
                        $muni_nombre = $dato_r;
                    if ($campos_d == "*DEPTO_NOMBRE*")
                        $dpto_nombre = $dato_r;
                    if ($campos_d == "*PAIS_NOMBRE*")
                        $pais = $dato_r;
                    if ($campos_d == "*TIPO_DOC*")
                        $tdoc = trim($dato_r);
                    if ($campos_d == "*NUM_EXPEDIENTE*")
                        $numeroExpediente = trim($dato_r);
                    if ($campos_d == "*ESP_CODIGO*") {
                        $codigoESP = $dato_r;
                        if ($codigoESP == "<ESPACIO>") {
                            $codigoESP = null;
                        }
                    }
                    if ($campos_d == "*RAD_ANEXO*") {
                        $radicadopadre = $dato_r;
                        $tipoanexo = 0;
                        if ($radicadopadre == "<ESPACIO>") {
                            $radicadopadre = "";
                            $tipoanexo = "";
                        }
                    } else {
                        $radicadopadre = "";
                    }

                    $tipo_anexo = "0";
                    $cuentai = "";
                    $documento_us3 = "";
                    $med = "";
                    $fec = "";

                    $ane = "";
                    //$pais="COLOMBIA";
                    $carp_codi = "12";
                    $i++;
                }
                $tip_rem = "1";
                // Si no se especifico el tipo de documento
                IF (!$tdoc)
                    $tdoc = 0;
//			$this->validarLugar();
                $pais_codi = $this->arrCodPais[$pais . $dpto_nombre . $muni_nombre];
                if ($pais_codi == '') {
                    $pais_codi = '170';
                }
                $dpto_codi = $pais_codi . "-" . $this->arrCodDepto[$dpto_nombre];
                $muni_codi = $dpto_codi . "-" . $this->arrCodMuni[$dpto_nombre . $muni_nombre];

                $tmp_objMuni = new Municipio($this->conexion);   //Creamos esto para traer el codigo del continente y transmitirlo
                $tmp_objMuni->municipio_codigo($dpto_codi, $muni_codi); //por las diferentes tablas.
                $cont_codi = $tmp_objMuni->get_cont_codi();
                $muni_codi = $cont_codi . "-" . $muni_codi;

                //Se agregan las dos variables siguientes, para corregir el error que se estaba presentando en la radicación masiva
                $codigo_depto = $this->arrCodDepto[$dpto_nombre];
                $codigo_muni = $this->arrCodMuni[$dpto_nombre . $muni_nombre];
                //Fin Variables agregadas

                $muni_us1 = $muni_codi;
                $codep_us1 = $dpto_codi;
                $nombre_us = "$nombre_us1 $prim_apell_us1 $seg_apell_us1";

                unset($tmp_objMuni);
                $documento_us3 = $codigoESP;
                if (!$documento_us3)
                    $documento_us3 = null;

                //Si se trata de una combinacion de correspondencia definitiva
                if ($this->definitivo == "si") {     // Segun el tipo de remitente se graba en la tabla respectiva.
                    // 0 - ESP 1 - OTRA EMPRESA  2 - PERSONA NATURAL
                    $nurad = $this->btt->radicar_salida_masiva($tipoanexo, $cuentai, $documento_us3, $med, $fec, $radicadopadre, $codusuario, $tip_doc, $ane, $pais, $asu, $dependencia, $tip_rem, $usua_doc, $this->tipoDocto, $muni_codi, $archivo, $usua_doc, $depe_codi_territorial, $secRadicacion, $numeroExpediente);
                    //echo ">>>>>" . $nurad . "::" . $archivo;
                    //include_once("$this->ruta_raiz/include/tx/Expediente.php");
                    //$this->objExp = new Expediente($this->conexion);
                    if (strlen($numeroExpediente) >= 10) {
                        $this->objExp = new Expediente($this->conexion);
                        $resultadoExp = $this->objExp->insertar_expediente($numeroExpediente, $nurad, $dependencia, $codusuario, $usua_doc);
                        $observa = "Por Rad. Masiva.";
                        if ($this->codProceso) {
                            $radicados[] = $nurad;
                            $tipoTx = 50;
                            $objFlujo = new Flujo($this->conexion, $this->codProceso, $usua_doc);
                            $expEstadoActual = $objFlujo->actualNodoExpediente($numeroExpediente);
                            $objFlujo->cambioNodoExpediente($numeroExpediente, $nurad, $this->codFlujo, $this->codArista, 1, $observa, $this->codProceso);
                        }
                    }
                    $nombre_us1 = trim($nombre_us1);
                    $direccion_us1 = trim($direccion_us1);
                    if ($tip_doc == 2) {
                        $codigo_us = $this->btt->grabar_usuario($doc_us1, $nombre_us1, $direccion_us1, $prim_apell_us1, $seg_apell_us1, $telefono_us1, $mail_us1, $codigo_depto, $codigo_muni);
                        $tipo_emp_us1 = 0;
                        $documento_us1 = $codigo_us;
                    }

                    if ($tip_doc == 1) {
                        $codigo_oem = $this->btt->grabar_oem($doc_us1, $nombre_us1, $direccion_us1, $prim_apell_us1, $seg_apell_us1, $telefono_us1, $mail_us1, $codigo_depto, $codigo_muni);
                        $tipo_emp_us1 = 2;
                        $documento_us1 = $codigo_oem;
                    }
                    if ($tip_doc == 0) {
                        $sgd_esp_codigo = $this->arregloEsp[$nombre_us1];
                        $tipo_emp_us1 = 1;
                        $documento_us1 = $sgd_esp_codigo;
                    }
                    $documento_us2 = "";
                    $documento_us3 = "";
                    $mail_us1;
                    $cc_documento_us1 = "documento";
                    $grbNombresUs1 = trim($nombre_us1) . " " . trim($prim_apel_us1) . " " . trim($seg_apel_us1);

                    $conexion = & $this->conexion;
                    include "$ruta_raiz/radicacion/grb_direcciones.php";

                    // En esta parte registra el envio en la tabla SGD_RENV_REGENVIO
                    if (!$this->codigo_envio) {
                        $isql = "select max(SGD_RENV_CODIGO) as MAX FROM SGD_RENV_REGENVIO";
                        $rs = $this->conexion->query($isql);

                        if (!$rs->EOF)
                            $nextval = $rs->fields['MAX'];
                        $nextval++;
                        $this->codigo_envio = $nextval;
                        $this->radi_nume_grupo = $nurad;
                        $radi_nume_grupo = $this->radi_nume_grupo;
                    }
                    else {
                        $nextval = $this->codigo_envio;
                    }
                    $dep_radicado = substr($verrad_sal, 4, 3);
                    $carp_codi = substr($dep_radicado, 0, 2);
                    $dir_tipo = 1;
                    $nombre_us = substr(trim($nombre_us), 0, 29);
                    $direccion_us1 = substr(trim($direccion_us1), 0, 29);
                    //print ("El grupo es ".$this->radi_nume_grupo."----");
                    $isql = "INSERT INTO SGD_RENV_REGENVIO (USUA_DOC, SGD_RENV_CODIGO, SGD_FENV_CODIGO, SGD_RENV_FECH,
						RADI_NUME_SAL, SGD_RENV_DESTINO, SGD_RENV_TELEFONO, SGD_RENV_MAIL, SGD_RENV_PESO, SGD_RENV_VALOR,
						SGD_RENV_CERTIFICADO, SGD_RENV_ESTADO, SGD_RENV_NOMBRE, SGD_DIR_CODIGO, DEPE_CODI, SGD_DIR_TIPO,
						RADI_NUME_GRUPO, SGD_RENV_PLANILLA, SGD_RENV_DIR, SGD_RENV_PAIS, SGD_RENV_DEPTO, SGD_RENV_MPIO,
						SGD_RENV_TIPO, SGD_RENV_OBSERVA,SGD_DEPE_GENERA)
						VALUES
						($usua_doc, $nextval, 101, " . $this->btt->sqlFechaHoy . ", $nurad, '$muni_nomb', '$telefono_us1', '$mail','',
						'$valor_unit', 0, 1, '$nombre_us', NULL, $dependencia, '$dir_tipo', " . $this->radi_nume_grupo . ", '00',
						'$direccion_us1', '$pais','$dpto_nombre', '$muni_nombre', 1, 'Masiva grupo " . $this->radi_nume_grupo . "',
						$dependencia) ";
                    $rs = $this->conexion->query($isql);

                    if (!$rs) {
                        $this->conexion->conn->RollbackTrans();
                        die("<span class='etextomenu'>No se ha podido isertar la informacion en SGD_RENV_REGENVIO");
                    }
                    /*
                     * Registro de la clasificacion TRD
                     */
                    $isql = "INSERT INTO SGD_RDF_RETDOCF(USUA_DOC, SGD_MRD_CODIGO, SGD_RDF_FECH, RADI_NUME_RADI, DEPE_CODI, USUA_CODI)
						VALUES($usua_doc, $codiTRD," . $this->btt->sqlFechaHoy . ", $nurad, '$dependencia', $codusuario )";
                    $rs = $this->conexion->query($isql);

                    if (!$rs) {
                        $this->conexion->conn->RollbackTrans();
                        die("<span class='etextomenu'>No se ha podido isertar la informaci&ocute;n en SGD_RENV_REGENVIO");
                    }
                } else {
                    $sec = $ii;
                    $sec = str_pad($sec, 5, "X", STR_PAD_LEFT);
                    $nurad = date("Y") . $dependencia . $sec . "1X";
                }
                // Comentariada por HLP. Cambiar , por ;
                fputs($fp, implode($this->separador, $this->datos[$ii]) . ",$nurad," . date("d/m/Y") . "\n");
                //fputs ($fp,implode( ";", $this->datos[$ii]).";$nurad;".date("d/m/Y")."\n");
                $contador = $ii + 1;

                echo "<tr><td class='listado2'>$contador</td><td class='listado2' >$nurad</td>
		    	 <td class='listado2'>" . unhtmlspecialchars($nombre_us) . "</td><td class='listado2'>" . unhtmlspecialchars($direccion_us1) . "</td>
		     	<td class='listado2' >$dpto_nombre</td><td class='listado2'>$muni_nombre</td>
		     	<td class='listado2'>$numeroExpediente</td></tr>";
                if (connection_status() != 0) {
                    echo "<h1>Error de conexion</h1>";
                    $objError = new CombinaError(NO_DEFINIDO);
                    echo ($objError->getMessage());
                    die;
                }
                $data = array_merge($data, array(array('#' => $contador, 'Radicado' => $nurad, 'Nombre' => $nombre_us, 'Dirección' => $direccion_us1, 'Departamento' => $dpto_nombre, 'Municipio' => $muni_nombre)));
                $arrRadicados[] = $nurad;
            }
            fclose($fp);
            echo "</table>";
            echo "<span class='info'>Numero de registros $contador</span>";
            $this->pdf->ezTable($data);
            $this->pdf->ezText("\n", 15, $justCentro);
            $this->pdf->ezText("Total Registros $contador \n", 15, $justCentro);
            $pdfcode = $this->pdf->ezOutput();
            $fp = fopen($this->arcPDF, 'wb');
            fwrite($fp, $pdfcode);
            fclose($fp);

            if ($this->definitivo == "si")
                $objHist->insertarHistorico($arrRadicados, $dependencia, $codusuario, $dependencia, $codusuario, "Radicado insertado del grupo de masiva $radi_nume_grupo", 30);
            $this->resulComb = $data;
            $this->resulNroRad = $arrRadicados;
        }
        else
            exit("No se pudo crear el archivo $this->archivo_insumo");
    }

    function cargar_csv() {
        $h = fopen($this->ruta_raiz . "/bodega/masiva/" . $this->arcCSV, "r") or $flag = 2;
        if (!$h) {
            echo "<BR> No hay un archivo csv llamado *" . $this->ruta_raiz . "/bodega/masiva/" . $this->arcCSV . "*";
        } else {
            $contenidoCSV = file($this->ruta_raiz . "/bodega/masiva/" . $this->arcCSV);
            //fclose($h);
            foreach ($contenidoCSV as $line_num => $line) {
                if ($line_num == 0) { //Esta línea contiene las variables a reemplazar
                    $comaPos = stripos($line, ",");
                    $puntocomaPos = stripos($line, ";");
//			echo "<br>Separado por coma: " . $comaPos;
//			echo "<br>Separado por punto y coma: " . $puntocomaPos;
                    if ($comaPos) {
                        $this->separador = ",";
                    } elseif ($puntocomaPos) {
                        $this->separador = ";";
                    } else {
                        die("Separador en archivo CSV inv&aacute;lido.");
                    }
                    break;
                }
            }
            $this->alltext_csv = "";
            $this->encabezado = array();
            $this->datos = array();
            $j = -1;
            while ($line = fgetcsv($h, 10000, $this->separador)) {
                //	Comentariada por HLP. Para puebas de arhivo csv delimitado por ;
                //while ($line=fgetcsv ($h, 10000, ";"))
                $j++;
                if ($j == 0)
                    $this->encabezado = array_merge($this->encabezado, array($line));
                else
                    $this->datos = array_merge($this->datos, array($line));
            } // while get
            //	echo ("El encabezado es " . $this->encabezado[0][0] .", ". $this->encabezado[0][1] .", ". $this->encabezado[0][2] .", ");
            //  echo ("Las lineas de datos son " . count ($this->datos));
            $c = 0;
            while ($c < count($this->datos)) {
                $c++;
            }
        }
    }

    /**
     * Gestiona la validacion de las archivos que intervienen en el proceso antes de invocar esta funcion debe haberse invocado 	cargar_csv() y abrir();
     */
    function validarArchs() {
        $this->cargarObligCsv();
        //$this->cargarOblPlant();
        //Recorre los campos abligatorios buscando que cada uno de ellos se encuentre en el emcabezado del archivo CSV
        for ($i = 0; $i < count($this->camObligCsv); $i++) {
            $sw = 0;
            foreach ($this->encabezado[0] as $campoEnc) {
                if ("*" . $this->camObligCsv[$i] . "*" == $campoEnc) {
                    $sw = 1;
                }
            }
            if ($sw == 0) {
                $this->errorEncab[] = $this->camObligCsv[$i];
            }
        }
        $this->validarTipo();
        $this->validarRegistrosObligsCsv();
        $this->validarLugar();
        $this->validarEsp();
        $this->validarDireccion();
        $this->validarNombre();
        $this->validarSiAnexo();
    }

    /**
     * Carga los campos obligatorios del tipo de archivo enviado como par�metro y lo hace en el arreglo referenciado en el arreglo definido como par�metro
     * @param $tipo     	es el tipo de archivo de masiva
     * @param $arreglo   es el arreglo donde han de quedar los capos abligatorios
     */
    function cargarCampos($tipo, &$arreglo) {
        $q = "select * from sgd_cob_campobliga where sgd_tidm_codi=$tipo";
        $rs = $this->conexion->query($q);
        while (!$rs->EOF) {
            $arreglo[] = $rs->fields['SGD_COB_LABEL'];
            $rs->MoveNext();
        }
    }

    /**
     * Carga los campos obligatorios del tipo de archivo 2 o CSV
     */
    function cargarObligCsv() {
        $this->cargarCampos(2, $this->camObligCsv);
    }

    /**
     * Carga los campos obligatorios del tipo de archivo 1 o Plantilla
     */
    function cargarOblPlant() {
        $this->cargarCampos(1, $this->camObligPlant);
    }

    /**
     * Pregunta si existe alguntipo de error, que puede ser de emcabezado, pantilla, lugar, ESP,de completitud del CSV, o del tipo de registro, antes de llamar esta funcion se debi� validar mediante  validarArchs(). En caso de error retorna true, de lo contrario falso.
     * @return	boolean
     */
    function hayError() {
        if (count($this->errorEncab) > 0 || count($this->errorPlant) > 0 || count($this->errorLugar) > 0 || count($this->errorESP) > 0 ||
                count($this->errorComplCsv) > 0 || count($this->errorTipo) > 0 || count($this->errorDir) > 0 || count($this->errorNomb) > 0 ||
                count($this->errorRadAnexo) > 0)
            return true;
        else
            return false;
    }

    /**
     * Busca si los campos obligatorios est�n completos en todos los registros del archivo CSV
     * Si existe algun error lo registra en el arreglo errorComplCsv
     */
    function validarRegistrosObligsCsv() { //Recorre todos los registros del CSV
        for ($i = 0; $i < count($this->datos); $i++) { //Recorre todos campos obligatorios del CSV y los busca en cada registro
            for ($j = 0; $j < count($this->camObligCsv); $j++) {
                $col = $this->getNumColEnc($this->camObligCsv[$j]);
                $dato = $this->datos[$i][$col];
                //Si no halla algun campo obligatorio lo pone en el arreglo de errores
                if (strlen($dato) == 0) {
                    $this->errorComplCsv[] = "REG " . ($i + 1) . ": " . $this->camObligCsv[$j];
                }
            }
        }
    }

    /**
     * Busca si se ha de generar radicados como anexos, validando que el radicado relacionado exista. Para esto debe existir una columna en el archivo csv llamada *RAD_ANEXO*
     * Si existe algun error lo registra en el arreglo errorRadAnexo
     */
    function validarSiAnexo() {
        $colRadAnexo = $this->getNumColEnc("RAD_ANEXO");
        if ($colRadAnexo != -1) {
            $objRadicado = new Radicado($this->conexion);
            //Recorre todos los registros del CSV
            for ($i = 0; $i < count($this->datos); $i++) {
                $dato = $this->datos[$i][$colRadAnexo];
                //Si la columna que contiene el campo RAD_ANEXO se refiere a un radicado que no existe
                if (strlen(trim($dato)) > 0 && !$objRadicado->radicado_codigo($dato)) {
                    $this->errorRadAnexo[] = "REG " . ($i + 1) . ": $dato";
                }
            }
        }
    }

    /**
     * Busca si el campo obligatorio TIPO existe correctamente  en todos los registros del archivo CSV
     * Si existe algun error lo registra en el arreglo errorTipo
     */
    function validarTipo() {
        $colTipo = $this->getNumColEnc("TIPO");
        //Recorre todos los registros del CSV
        for ($i = 0; $i < count($this->datos); $i++) {
            $dato = $this->datos[$i][$colTipo];
            //Si la columna que contiene el campo TIPO no es correcta la referencia en en arreglo errorTipo
            if ($dato != "0" && $dato != "1" && $dato != "2") {
                $this->errorTipo[] = "REG " . ($i + 1) . ": ";
            }
        }
    }

    /**
     * Busca si los lugares referenciados en el archivo CSV son validos. Si existe algun error lo registra en el arreglo errorLugar
     */
    function validarLugar() {
        $colDepto = $this->getNumColEnc("DEPTO_NOMBRE");
        $colMuni = $this->getNumColEnc("MUNI_NOMBRE");
        $colPais = $this->getNumColEnc("PAIS_NOMBRE");
        //Recorre todos los registros del CSV
        for ($i = 0; $i < count($this->datos); $i++) {
            $dato_pais = $this->datos[$i][$colPais];
            $dato = $this->datos[$i][$colDepto];
            $dato_muni = $this->datos[$i][$colMuni];
            if ($dato_pais == '') {
                $dato_pais = 'COLOMBIA';
            }
            $q3 = "select * from SGD_DEF_PAISES where NOMBRE_PAIS='$dato_pais'";
            $rs = $this->conexion->query($q3);
            if ($rs) {
                $codigoPais = $rs->fields['ID_PAIS'];
                $codigoCont = $rs->fields['ID_CONT'];
                $q = "select * from departamento where dpto_nomb='$dato' and ID_PAIS=$codigoPais";
                $rs = $this->conexion->query($q);
                //Valida si el departamento es valido
                if (!$rs->EOF) {
                    $codigoDepto = $rs->fields['DPTO_CODI'];

                    $q2 = "select * from municipio where muni_nomb='$dato_muni' and dpto_codi=$codigoDepto and ID_PAIS=$codigoPais";
                    $rs = $this->conexion->query($q2);

                    //Valida si el municipio es valido
                    if ($rs->EOF)
                        $this->errorLugar[] = "REG " . ($i + 1) . ": $dato_muni ";
                    else {
                        $codigoMuni = $rs->fields['MUNI_CODI'];
                        $this->arrCodDepto[$dato] = $codigoDepto;
                        $this->arrCodMuni[$dato . $dato_muni] = $codigoMuni;
                        $this->arrCodPais[$dato_pais . $dato . $dato_muni] = $codigoPais;
                        $this->arrCodCont[$codigoCont . $dato_pais . $dato . $dato_muni] = $codigoCont;
                    }
                } else {
                    $this->errorLugar[] = "REG " . ($i + 1) . ": $dato ";
                }
            } else {
                $this->errorLugar[] = "REG " . ($i + 1) . ": $dato_pais ";
            }
        }
    }

    /**
     * Busca si las ESP referenciadas en el archivo CSV son validas. Si existe algun error lo registra en el arreglo errorESP
     */
    function validarEsp() {
        $colESP = $this->getNumColEnc("NOMBRE");
        $colTipo = $this->getNumColEnc("TIPO");
        $esp = new Esp($this->conexion);

        //Recorre todos los registros del CSV
        for ($i = 0; $i < count($this->datos); $i++) {
            if ($this->datos[$i][$colTipo] == 0) {
                $dato = $this->datos[$i][$colESP];
                //$dato_muni = $this->datos[$i][$colMuni];
                //Valida si la ESP valida
                if ($esp->Esp_nombre($dato)) {
                    $this->arregloEsp[$dato] = $esp->getId();
                } else {
                    $this->errorESP[] = "REG " . ($i + 1) . ": $dato ";
                }
            }
        }
    }

    /**
     * Valida que el campo de Nombre, 1er y 2o Apellido existan. Si existe algun error lo registra en el arreglo errorDireccion
     */
    function validarNombre() {
        $colNomb = $this->getNumColEnc("NOMBRE");
        $colPrimApe = $this->getNumColEnc("PRIM_APEL");
        $colsegApe = $this->getNumColEnc("SEG_APEL");

        //Recorre todos los registros del CSV
        for ($i = 0; $i < count($this->datos); $i++) {
            $dato = $this->datos[$i][$colNomb];

            if ($colPrimApe != -1)
                $dato = $dato . " " . $this->datos[$i][$colPrimApe];

            if ($colsegApe != -1)
                $dato = $dato . " " . $this->datos[$i][$colsegApe];

            if (strlen($dato) > 95)
                $this->errorNomb[] = "REG " . ($i + 1) . ": $dato ";
        }
    }

    /**
     * Valida que el campo de direccion no exceda el m�ximo permitido. Si existe algun error lo registra en el arreglo errorDireccion
     */
    function validarDireccion() {
        $colDir = $this->getNumColEnc("DIR");

        //Recorre todos los registros del CSV
        for ($i = 0; $i < count($this->datos); $i++) {
            $dato = $this->datos[$i][$colDir];
            if (strlen($dato) > 95)
                $this->errorDir[] = "REG " . ($i + 1) . ": $dato ";
        }
    }

    /**
     * Retorna el numero de columna en que se encuentra el encabezado que le llegue como par�metro. Si no existe retorna -1
     * @param $nombCol		es el nombre de la columna o encabezado
     * @return   integer
     */
    function getNumColEnc($nombCol) {
        $i = -1;
        $sw = 0;
        //Recorre todo el encabezado
        foreach ($this->encabezado[0] as $campoEnc) {
            $i++;

            if ("*" . $nombCol . "*" == $campoEnc) {
                $sw = 1;
                break;
            }
        }
        if ($sw == 1)
            return($i);
        else
            return -1;
    }

    /**
     * Muestra los errores presentados en la validacion de los archivos
     */
    function mostrarError() {
        $auxErrrEnca = $this->errorEncab;
        $auxErrPlant = $this->errorPlant;
        $auxErrLugar = $this->errorLugar;
        $auxErrESP = $this->errorESP;
        $auxErrCmpCsv = $this->errorComplCsv;
        $auxErrorTipo = $this->errorTipo;
        $auxErrorDir = $this->errorDir;
        $auxErrorNom = $this->errorNomb;
        $auxErrorAnexo = $this->errorRadAnexo;
        $auxErrorEmail = $this->errorEmail;
        $ruta_raiz = "../..";
        include "$ruta_raiz/radsalida/masiva/error_archivo.php";
    }

    /**
     * Cambia el valor  del atributo que indica si se trata de ina combinacion definitiva
     * @param $arg		nuevo valor de la variable, puede ser "si" o "no"
     */
    function setDefinitivo($arg) {
        $this->definitivo = $arg;
    }

    /**
     * Cambia el valor del atributo que indica la caracter�stica de los documentos a combinar
     * @param $tipo		nuevo valor de la variable
     */
    function setTipoDocto($tipo) {
        $this->tipoDocto = $tipo;
    }

    /**
     * Carga los datos del archivo insumo para la generacion de masiva
     */
    function cargarInsumo() {
//Referencia el archivo a abrir
        $fp = fopen("$this->ruta_raiz/bodega/masiva/$this->archivo_insumo", 'r');
        $i = 0;
        while (!feof($fp)) {
            $i++;
            $buffer = fgets($fp, 4096);
            if ($i == 1) {
                $this->arcPlantilla = trim(substr($buffer, strpos($buffer, "=") + 1, strlen($buffer) - strpos($buffer, "=")));
            }
            if ($i == 2) {
                $this->arcCSV = trim(substr($buffer, strpos($buffer, "=") + 1, strlen($buffer) - strpos($buffer, "=")));
            }
            if ($i == 3) {
                $this->arcFinal = trim(substr($buffer, strpos($buffer, "=") + 1, strlen($buffer) - strpos($buffer, "=")));
            }
            if ($i == 4) {
                $this->arcTmp = trim(substr($buffer, strpos($buffer, "=") + 1, strlen($buffer) - strpos($buffer, "=")));
            }
        }
        fclose($fp);
    }

    /**
     * Retorna el path del archivo insumo para masiva
     */
    function getInsumo() {
        return($this->archivo_insumo);
    }

    /**
     *
     * Deshace  una la transaccion de correspondencia masiva en caso de no terminar satisfactoriamente
     */
    function deshacerMasiva() {
        $this->conexion->conn->RollbackTrans();
    }

    /**
     *
     * Confirma  una la transaccion de correspondencia masiva en caso de terminar satisfactoriamente
     */
    function confirmarMasiva() { //$this->conexion->conn->debug = true;
        return ($this->conexion->conn->CompleteTrans());
    }

}

/**
 * Función que reemplaza caracteres con tíldes por sus contrapartes sin tílde, solo para mostrar por pantalla
 *
 * @param String $string
 * @return String
 */
function unhtmlspecialchars($string) {
    $string = str_replace('á', '&aacute;', $string);
    $string = str_replace('é', '&eacute;', $string);
    $string = str_replace('í', '&iacute;', $string);
    $string = str_replace('ó', '&oacute;', $string);
    $string = str_replace('ú', '&uacute;', $string);
    $string = str_replace('Á', '&Aacute;', $string);
    $string = str_replace('É', '&Eacute;', $string);
    $string = str_replace('Í', '&Iacute;', $string);
    $string = str_replace('Ó', '&Oacute;', $string);
    $string = str_replace('Ú', '&Uacute;', $string);
    $string = str_replace('ñ', '&ntilde;', $string);
    $string = str_replace('Ñ', '&Ntilde;', $string);

    return $string;
}

?>
