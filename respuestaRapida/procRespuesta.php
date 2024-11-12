<?php
    session_start();
	//error_reporting(7);
    //set_time_limit(0);
    if($_SESSION["krd"]){
        $krd = $_SESSION["krd"];
    } 
    if (!isset($_SESSION['dependencia'])){
        include "../rec_session.php";
    }
/***********************************************
// INICIO DE VARIABLES Y CONSTANTES
***********************************************/
    // envio de respuesta via email
    // Obtiene los datos de la respuesta rapida.
    $ruta_raiz = "../";
    $ruta_libs = $ruta_raiz."respuestaRapida/";
    define('ADODB_ASSOC_CASE', 0);
    define('SMARTY_DIR', $ruta_libs . 'libs/');

	//Funcion que convierte los acenntos y caracteres extraños para evitar que se rompa el código a causa de estos caracteres
	function StrValido($cadena)
	{
		$login = strtolower($cadena);
		$b     = array("á","é","í","ó","ú","ä","ë","ï","ö","ü","à","è","ì","ò","ù","ñ",",",";",":","¡","!","\¿","/?",'"',"/","&","\n","#","\$","*","%","+","[","]","=","¬","|","^","`","~","\\","'");
		$c     = array("a","e","i","o","u","a","e","i","o","u","a","e","i","o","u","n","","","","","","","","",'',"","","","","","","","","","","","","","","","","",);
		$login = str_replace($b,$c,$login);
		$login = preg_replace('@^([^\?]+)(\?.*)$@','\1',$login);
		return $login;
	}
	
	
	//formato para fecha en documentos
    function fechaFormateada($FechaStamp){
        $ano = date('Y', $FechaStamp); //<-- Ano
        $mes = date('m', $FechaStamp); //<-- número de mes (01-31)
        $dia = date('d', $FechaStamp); //<-- Día del mes (1-31)
        $dialetra = date('w', $FechaStamp); //Día de la semana(0-7)

        switch ($dialetra) {
            case 0 :
                $dialetra = "domingo";
                break;
            case 1 :
                $dialetra = "lunes";
                break;
            case 2 :
                $dialetra = "martes";
                break;
            case 3 :
                $dialetra = "miércoles";
                break;
            case 4 :
                $dialetra = "jueves";
                break;
            case 5 :
                $dialetra = "viernes";
                break;
            case 6 :
                $dialetra = "sábado";
                break;
        }

        switch ($mes) {
            case '01' :
                $mesletra = "enero";
                break;
            case '02' :
                $mesletra = "febrero";
                break;
            case '03' :
                $mesletra = "marzo";
                break;
            case '04' :
                $mesletra = "abril";
                break;
            case '05' :
                $mesletra = "mayo";
                break;
            case '06' :
                $mesletra = "junio";
                break;
            case '07' :
                $mesletra = "julio";
                break;
            case '08' :
                $mesletra = "agosto";
                break;
            case '09' :
                $mesletra = "septiembre";
                break;
           case '10' :
                $mesletra = "octubre";
                break;
            case '11' :
                $mesletra = "noviembre";
                break;
            case '12' :
                $mesletra = "diciembre";
                break;
        }

        return htmlentities("$dialetra, $dia de $mesletra de $ano");
    }

    $encabe = session_name()."=".session_id()."&krd=$krd";

    $pos = strpos('salidaRespuesta',$_SERVER['HTTP_REFERER']);

    if ($pos !== false){
	    header("Location: index.php?$encabe");
        die;
    } 

    require_once($ruta_raiz."_conf/constantes.php");
    require_once($ruta_raiz."include/db/ConnectionHandler.php");
    include_once($ruta_raiz."class_control/AplIntegrada.php");
    include_once($ruta_raiz."class_control/anexo.php");
    include_once($ruta_raiz."class_control/anex_tipo.php");
    include_once($ruta_raiz."include/tx/Tx.php");
    include_once($ruta_raiz."include/tx/Radicacion.php");
    include_once($ruta_raiz."class_control/Municipio.php");
    include_once($ruta_raiz."envios/class.phpmailer.php");
    require_once($ruta_raiz."tcpdf/config/lang/eng.php");
    require_once($ruta_raiz."tcpdf/tcpdf.php");

    $db      = new ConnectionHandler("$ruta_raiz");
    $hist    = new Historico($db);
    $Tx      = new Tx($db);
    $anex    = new Anexo($db);
    $anexTip = new Anex_tipo($db);
    $mail    = new PHPMailer();

    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;	
    $anexTip->anex_tipo_codigo(7);
    $sqlFechaHoy      = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
    $numRadicadoPadre = $_POST["radPadre"];

	$tamanoMax		= 7 * 1024 * 1024; // 7 megabytes
    $fechaGrab      = trim($date1);
    $numramdon      = rand (0,100000);
    $contador       = 0;
    $regFile        = array();
    $conCopiaA      = "";
    $enviadoA       = "";
    $cCopOcu        = "";

    $ddate          = date("d");
    $mdate          = date("m");
    $adate          = date("Y");
    $fechproc4      = substr($adate,2,4);
    $fecha1         = time();
    $fecha          = fechaFormateada($fecha1);

    //DATOS A VALIDAR EN RADICADO //
    $servidorSmtp   = "172.16.1.92:25";
    $tdoc           = 0;  // tipo documental no definido
    $ent            = '1';
    $pais           = 170; //OK, codigo pais
    $cont           = 1; //id del continente
    $radicado_rem   = 7;
    $auxnumero      = str_pad($auxnumero, 5, "0", STR_PAD_LEFT);
    $tipo           = 7;  #pdf
    $tamano         = 1000;
    $auxsololect    = 'N';
    $radicado_rem   = 1;
    $descr          = 'Respuesta';
    $fechrd         = $ddate.$mdate.$fechproc4;
    $coddepe        = $_POST["depecodi"];
    $usua_actu      = $_POST["usuacodi"];
    $usua           = $_POST["usualog"];
    $codigoCiu      = $_POST["codigoCiu"];

    $usMailSelect   = $_POST['usMailSelect']; //correo del emisor de la respuesta
    $destinat       = $_POST["destinatario"]; //correos de los destinatarios
    $correocopia    = $_POST["concopia"]; //destinatarios con copia
    $conCopOcul     = $_POST["concopiaOculta"]; //con copia oculta
        
    $asu            = $_POST["respuesta"];

    $tpDepeRad      = $coddepe;
    $radUsuaDoc     = $codigoCiu;
    $usua_doc       = $_SESSION["usua_doc"];
    $usuario        = $_SESSION["usua_nomb"]; 
    $setAutor       = 'Sistema de Gestion Documental Orfeo';
    $SetTitle       = 'Respuesta a solicitud';
    $SetSubject     = 'Departamento Naciona de Planeación';
    $SetKeywords    = 'dnp, respuesta, salida, generar';

    //DATOS EMPRESA
    $sigla          = 'null';
    $iden           = $db->conn->nextId("sec_ciu_ciudadano");//uniqe key

    //ENLACE DEL ANEXO
    $radano = substr($numRadicadoPadre,0,4);
	$deppadre = substr($numRadicadoPadre, 4, 3);
    //$ruta   = $adate.$coddepe.$usua_actu."_".rand(10000, 99999)."_".time().".pdf";
	$ruta   = $adate.$coddepe.$usua_actu."_".time()."_Respuesta.pdf";
    $ruta2  = "bodega/$radano/$deppadre/docs/".$ruta;
	$ruta3  = "$radano/$deppadre/docs/".$ruta;

/***********************************************
// DATOS DEL RADICADO PADRE 
***********************************************/
    $desti = "
            SELECT 
                s.SGD_DIR_NOMREMDES,
                s.SGD_DIR_DIRECCION,
                s.SGD_DIR_TIPO,
                s.SGD_DIR_MAIL,
                s.SGD_DIR_TELEFONO,
                s.SGD_SEC_CODIGO,
                r.RADI_PATH
            FROM 
                SGD_DIR_DRECCIONES s,
                RADICADO r
            WHERE 
                r.RADI_NUME_RADI     = $numRadicadoPadre
                AND s.RADI_NUME_RADI = r.RADI_NUME_RADI";

    $rssPatth       = $db->conn->Execute($desti);
    $dir_nombre     = $rssPatth->fields["SGD_DIR_NOMREMDES"];
    $dir_tipo       = $rssPatth->fields["SGD_DIR_TIPO"];
    $dir_mail       = $rssPatth->fields["SGD_DIR_MAIL"];
    $dir_telefono   = $rssPatth->fields["SGD_DIR_TELEFONO"];
    $dir_direccion  = $rssPatth->fields["SGD_DIR_DIRECCION"];
    $pathPadre      = $rssPatth->fields["RADI_PATH"];

/***********************************************
// CREACION DEL RADICADO RESPUESTA
***********************************************/
    //Para crear el numero de radicado se realiza el siguiente procedimiento
    $isql_consec = "SELECT 
                        DEPE_RAD_TP$ent as secuencia 
                    FROM 
                        DEPENDENCIA 
                    WHERE 
                        DEPE_CODI = $tpDepeRad";

    $creaNoRad   = $db->conn->Execute($isql_consec);
    $tpDepeRad   = $creaNoRad->fields["secuencia"];

    $rad = new Radicacion($db);
    $rad->radiTipoDeri  = 0; // ok ????
    $rad->radiCuentai   = 'null';  // ok, Cuenta Interna, Oficio, Referencia
    $rad->eespCodi      = $iden; //codigo emepresa de servicios publicos bodega
    $rad->mrecCodi      = 3; // medio de correspondencia, 3 internet
    $rad->radiFechOfic  = "$ddate/$mdate/$adate"; // igual fecha radicado;
    $rad->radiNumeDeri  = $numRadicadoPadre; //ok, radicado padre
    $rad->radiPais      = $pais; //OK, codigo pais
    $rad->descAnex      = '.'; //OK anexos
    $rad->raAsun        = "Respuesta al radicado " . $numRadicadoPadre; // ok asunto
    $rad->radiDepeActu  = $coddepe; // ok dependencia actual responsable
    $rad->radiUsuaActu  = $usua_actu; // ok usuario actual responsable
    $rad->radiDepeRadi  = $coddepe; //ok dependencia que radica
    $rad->usuaCodi      = $usua_actu; // ok usuario actual responsable
    $rad->dependencia   = $coddepe; //ok dependencia que radica
    $rad->trteCodi      = 0; //ok, tipo de codigo de remitente
    $rad->tdocCodi      = $tdoc; //ok, tipo documental
    $rad->tdidCodi      = 0; //ok, ????
    $rad->carpCodi      = 1; //ok, carpeta entradas
    $rad->carPer        = 0; //ok, carpeta personal
    $rad->ra_asun       = "Respuesta al radicado " . $numRadicadoPadre;
    $rad->radiPath      = 'null';
    $rad->sgd_apli_codi = '0';
    $rad->usuaDoc       = $radUsuaDoc;
    $codTx              = 62;

    $nurad = $rad->newRadicado($ent, $tpDepeRad);
	
    if ($nurad=="-1"){
	    header("Location: salidaRespuesta.php?$encabe&error=1");
        die;
    }
    //datos para guardar los anexos en la carpeta del nuevo radicado
    $primerno         = substr($nurad, 0, 4);
    $segundono        = substr($nurad, 4, 3);
    $ruta1             = $primerno . "/" . $segundono . "/docs/";
	$adjunto 		  = trim(str_replace(" ","", BODEGAPATH));
	$adjuntos 	      = str_replace("/","\\",$adjunto.$ruta1);

    $nextval            = $db->nextId("SEC_DIR_DIRECCIONES");
    //se buscan los datos del radicado padre y se 
    //insertaran en los del radicado hijo

    $isql = "insert into SGD_DIR_DRECCIONES(
                                SGD_TRD_CODIGO,
                                SGD_DIR_NOMREMDES,
                                SGD_DIR_DOC,
                                DPTO_CODI,
                                MUNI_CODI,
                                id_pais,
                                id_cont,
                                SGD_DOC_FUN,
                                SGD_OEM_CODIGO,
                                SGD_CIU_CODIGO,
                                SGD_ESP_CODI,
                                RADI_NUME_RADI,
                                SGD_SEC_CODIGO,
                                SGD_DIR_DIRECCION,
                                SGD_DIR_TELEFONO,
                                SGD_DIR_MAIL,
                                SGD_DIR_TIPO,
                                SGD_DIR_CODIGO,
                                SGD_DIR_NOMBRE)
                        values( 1,
                                '$dir_nombre',
                                NULL,
                                11,
                                1,
                                170,
                                1,
                                '$usua_doc',
                                NULL,
                                NULL,
                                NULL,
                                $nurad,
                                0,
                                '$dir_direccion',
                                '$dir_telefono',
                                '$dir_mail',
                                1,
                                $nextval,
                                '$dir_nombre')";

    $rsg               = $db->conn->Execute($isql);

    $mensajeHistorico  = "Se envia respuesta rapida";

    if(!empty($regFile)){
        $mensajeHistorico .= ", con archivos adjuntos";
    }

    //inserta el evento del radicado padre.
    $radicadosSel[0] = $numRadicadoPadre;

    $hist->insertarHistorico($radicadosSel,
                          $coddepe,
                          $usua_actu,
                          $coddepe,
                          $usua_actu,
                          $mensajeHistorico,
                          $codTx);

    //Inserta el evento del radicado de respuesta nuevo.
    $radicadosSel[0] = $nurad;
    $hist->insertarHistorico($radicadosSel,
                          $coddepe,
                          $usua_actu,
                          $coddepe,
                          $usua_actu,
                          "",
                          2);

    //Agregar un nuevo evento en el historico para que
    //muestre como contestado y no genere alarmas.
    //A la respuesta se le agrega el siguiente evento
    $hist->insertarHistorico($radicadosSel,
                          $coddepe,
                          $usua_actu,
                          $coddepe,
                          $usua_actu,
                          "Imagen asociada desde respuesta rapida",
                          42);

/***********************************************
// VALIDAR DATOS ADJUNTOS
***********************************************/
    if(!empty($_FILES["archs"]["name"][0])){
        // Arreglo para Validar la extension
        $sql1 		= "	select
                            anex_tipo_codi as codigo
                            , anex_tipo_ext as ext
                            , anex_tipo_mime as mime
                        from
                            anexos_tipo";

        $exte = $db->conn->Execute($sql1);

        while(!$exte->EOF) {
            $codigo 		= $exte->fields["codigo"];
            $ext			= $exte->fields["ext"];
            $mime1			= $exte->fields["mime"];
            $mime2			= explode(",",$mime1);

            //arreglo para validar la extension
            $exts[".".$ext]	= array ('codigo' 	=> $codigo,
                                     'mime'		=> $mime2);
            $exte->MoveNext();
        };

        //Si no existe la carpeta se crea.
        if(!is_dir($adjuntos)){
            $rs	= mkdir($adjuntos, 0700);
            if(empty($rs)){
	            $errores .= empty($errores)? "&error=2" : '-2';
            }
		}
                
        $i = 0;
        $anexo = new Anexo($db);   

        //Validaciones y envio para grabar archivos
        foreach($_FILES["archs"]["name"] as $key => $name){
            $nombre 	= strtolower(trim($_FILES["archs"]["name"][$key]));
            $type		= trim($_FILES["archs"]["type"][$key]);
            $tamano		= trim($_FILES["archs"]["size"][$key]);
            $tmporal	= trim($_FILES["archs"]["tmp_name"][$key]);
            $error		= trim($_FILES["archs"]["error"][$key]);            
            $ext 		= strrchr($nombre,'.');
            if (is_array($exts[$ext])){				
                foreach ($exts[$ext]['mime'] as $value){
                    if(eregi($type,$value)){
                        $bandera = true;
                        if($tamano < $tamanoMax){
                            //grabar el registro en la base de datos                            
                            if(strlen($str) > 90){
								$nombre	= substr($nombre, '-90:');                            
							}                          							
                            $anexo->anex_radi_nume    = $nurad;
                            $anexo->usuaCodi          = $usua_actu;
                            $anexo->depe_codi         = $coddepe;
                            $anexo->anex_solo_lect    = "'S'";
                            $anexo->anex_tamano       = $tamano;
                            $anexo->anex_creador      = "'".$usua."'";
                            $anexo->anex_desc         = "Adjunto: ". $nombre;
                            $anexo->anex_nomb_archivo = $nombre;
                            $auxnumero                = $anexo->obtenerMaximoNumeroAnexo($nurad);
                            $anexoCodigo              = $anexo->anexarFilaRadicado($auxnumero);
                            $nomFinal                 = $anexo->get_anex_nomb_archivo();

                            //Guardar el archivo en la carpteta ya creada
                            $Grabar_path	= $adjuntos.$nomFinal;
                            if (move_uploaded_file($tmporal, $Grabar_path)) {
                                //si existen adjuntos los agregamos para enviarlos por correo
                                $mail->AddAttachment($Grabar_path, $nombre);
                            }else {
	                            $errores .= empty($errores)? "&error=6" : '-6';
                            }
                        }else{
	                        $errores .= empty($errores)? "&error=5" : '-5';
                        }
                    }
                }

                if(empty($bandera)){
	                $errores .= empty($errores)? "&error=4" : '-4';
                };

            }else{
	            $errores .= empty($errores)? "&error=3" : '-3';
            }

            $contador ++;
        };
    };

/***********************************************
// AGREGAR LOS ADJUNTOS AL RADICADO
***********************************************/
    $auxnumero    = $anex->obtenerMaximoNumeroAnexo($nurad);

    do{
        $auxnumero += 1;
        $codigo     = trim($numRadicadoPadre) . trim(str_pad($auxnumero, 5, "0", STR_PAD_LEFT));
    }while ($anex->existeAnexo($codigo));

    $isql = "INSERT INTO ANEXOS (SGD_REM_DESTINO,
                                ANEX_RADI_NUME,
                                ANEX_CODIGO,
                                ANEX_ESTADO,
                                ANEX_TIPO,
                                ANEX_TAMANO,
                                ANEX_SOLO_LECT,
                                ANEX_CREADOR,
                                ANEX_DESC,
                                ANEX_NUMERO,
                                ANEX_NOMB_ARCHIVO,
                                ANEX_BORRADO,
                                ANEX_SALIDA,
                                SGD_DIR_TIPO,
                                ANEX_DEPE_CREADOR,
                                SGD_TPR_CODIGO,
                                ANEX_FECH_ANEX,
                                SGD_APLI_CODI,
                                SGD_TRAD_CODIGO,
                                RADI_NUME_SALIDA,
                                SGD_EXP_NUMERO,
                               ANEX_ESTADO_EMAIL)
                        values ($radicado_rem,
                                $numRadicadoPadre,
                                '$codigo',
                                2,
                                '$tipo',
                                $tamano,
                                '$auxsololect',
                                '$usua',
                                '$descr',
                                $auxnumero,
                                '$ruta',
                                'N',
                                1,
                                $radicado_rem,
                                $coddepe,
                                NULL,
                                $sqlFechaHoy,
                                NULL,
                                1,
                                $nurad,
                                NULL,1)";

    $bien = $db->conn->Execute($isql);
    // Si actualizo BD correctamente
    if (!$bien) {
	    $errores .= empty($errores)? "&error=7" : '-7';
    }

/***********************************************
// CREACION DE PDF RESPUESTA AL RADICADO 
***********************************************/
    $cond = "SELECT 
                DEP_SIGLA, 
                DEPE_NOMB 
             FROM 
                DEPENDENCIA 
             WHERE 
                DEPE_CODI = $coddepe";

    $exte       = $db->conn->Execute($cond);
    $dep_sig 	= $exte->fields["DEP_SIGLA"];
    $dep_nom	= $exte->fields["DEPE_NOMB"];

	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			// Logo
			$this->Image('../img/banerPDF.JPG', 30, 10, 167, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-20);
			// Page number
            $txt = "<div align='center'>Calle 26 # 13 - 19 Bogot&aacute;, D.C., Colombia PBX 381 5000 www.dnp.gov.co";
            $this->writeHTMLCell($w=0, $h=3, $x='52', $y='', $txt, $border=0, $ln=1, $fill=0, $reseth=true);
		}
	}

	// create new PDF document
	$pdf = new MYPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($setAutor);
    $pdf->SetTitle($SetTitle);
    $pdf->SetSubject($SetSubject);
    $pdf->SetKeywords($SetKeywords);

    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    // ---------------------------------------------------------
	
    // set default font subsetting mode
    $pdf->setFontSubsetting(true);
	//$login = StrValido($usuario);
    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();
    // Print text using writeHTMLCell()
	//echo "Esta es la variable login ".$login;
	//echo "<br>Esta es la variable usuario ".$usuario;
	//echo "<br>Esta es la variable dependencia ".$dep_nomb;
    $asu = "
        <p><b>$dep_sig - $nurad <br/ >Bogot&aacute; D.C., $fecha</b><p/>
        <br />$asu<br /><br />
        Atentamente<br />
        $dep_nom <br />
		$usMailSelect <br />
        CC -> $correocopia";

    // output the HTML content
    $pdf->writeHTML($asu, true, false, true, false, '');
    // ---------------------------------------------------------
	
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output($ruta_raiz.$ruta2, 'F');
	
	$adjunto = trim(str_replace(" ","", BODEGAPATH));
    $post    = strpos(strtolower($pathPadre),'bodega');
    if($post !== false){
		$pathPadre = substr($pathPadre,$post + 6);
	}
    
    $filepad = str_replace("/", "\\", str_replace("//", "\\", $adjunto.$pathPadre)); 
    $filepad = str_replace("/", "\\", str_replace("//", "\\", $filepad)); 
	
	$mail->AddAttachment($ruta_raiz.$ruta2);
	if(!empty($mail->ErrorInfo)){
		$errores .= empty($errores)? "&error=10" : '-10';
	}
	
    $mail->AddAttachment($filepad);
	
    if(!empty($mail->ErrorInfo)){		
	    $errores .= empty($errores)? "&error=9" : '-9';
	}

/***********************************************
// ENVIO DE CORREO ELECTRONICO
***********************************************/
    //Destinatario de correo
    if(trim($destinat)){
        $emailSend = explode(";",$destinat);
        foreach($emailSend as $mailDir){
            if(trim($mailDir)) $mail->AddAddress($mailDir);
        }
    }

    if(trim($correocopia)){
        $emailSend = explode(";",$correocopia);
        foreach($emailSend as $mailDir){
            if($mailDir) $mail->AddCC(trim($mailDir));
        }
    }

    if(trim($conCopOcul)){
        $emailSend = explode(";",$conCopOcul);
        foreach($emailSend as $mailDir){
            if($mailDir) $mail->AddBCC(trim($mailDir));
        }
    }

    $mail->AddReplyTo($usMailSelect, $usMailSelect);

    $cuerpo = " <br> El Departamento Nacional de Planeación le informa que se ha
                dado respuesta a su solicitud No. $numRadicadoPadre mediante 
				el oficio de salida No. $nurad, el cual también puede ser consultado  
                en el portal Web del DNP.</p>
                <br><br><b><center>Si no puede visualizar el correo, o los archivos 
				adjuntos, puede consultarlos también en la siguiente dirección: <br>
                <a href='http://orfeo.dnp.gov.co/pqr/consulta.php?rad=$numRadicadoPadre'>
                http://orfeo.dnp.gov.co/pqr/consulta.php
                </a><br><br><br>DNP</b></center><BR>";

    $mail->Mailer   = "smtp";
    $mail->From     = $usMailSelect;
    $mail->FromName = $usMailSelect;
    $mail->Host     = $servidorSmtp;
    $mail->Mailer   = "smtp";
    //$mail->SMTPAuth = "false";
    $mail->Subject  = "Respuesta al radicado " . $numRadicadoPadre . " del Departamento Nacional de Planeacion";
    $mail->AltBody  = "Para ver el mensaje, por favor use un visor de E-mail compatible!";
    $mail->Body     = $cuerpo;
    $mail->IsHTML(true);
    $mail->SMTPDebug  = 1;	// 1 = errors and messages // 2 = messages only 
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    if (!$mail->Send()) {
	    $errores .= empty($errores)? "&error=8" : '-8';
    }else{
        $sql_sgd_renv_codigo = "SELECT 
                                    SGD_RENV_CODIGO 
                                FROM 
                                    SGD_RENV_REGENVIO 
                                ORDER BY SGD_RENV_CODIGO DESC ";

        $rsRegenvio    = $db->conn->SelectLimit($sql_sgd_renv_codigo,2);
        $nextval       = $rsRegenvio->fields["SGD_RENV_CODIGO"];
        $nextval++;
        $fechaActual   = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
        $destinatarios = "Destino:".$destinat." Copia:".$correocopia;
        $dependencia   = $_POST["depecodi"];

        $iSqlEnvio = "  INSERT INTO SGD_RENV_REGENVIO(
                            SGD_RENV_CODIGO
                            ,SGD_FENV_CODIGO
                            ,SGD_RENV_FECH
                            ,RADI_NUME_SAL
                            ,SGD_RENV_DESTINO
                            ,SGD_RENV_MAIL
                            ,SGD_RENV_PESO
                            ,SGD_RENV_VALOR
                            ,SGD_RENV_ESTADO
                            ,USUA_DOC
                            ,SGD_RENV_NOMBRE
                            ,SGD_RENV_PLANILLA
                            ,SGD_RENV_FECH_SAL
                            ,DEPE_CODI
                            ,SGD_DIR_TIPO
                            ,RADI_NUME_GRUPO
                            ,SGD_RENV_DIR
                            ,SGD_RENV_CANTIDAD
                            ,SGD_RENV_TIPO
                            ,SGD_RENV_OBSERVA
                            ,SGD_RENV_GRUPO
                            ,SGD_RENV_VALORTOTAL
                            ,SGD_RENV_VALISTAMIENTO
                            ,SGD_RENV_VDESCUENTO
                            ,SGD_RENV_VADICIONAL
                            ,SGD_DEPE_GENERA
                            ,SGD_RENV_PAIS
                            ,SGD_RENV_NUMGUIA)
                       VALUES (
                            $nextval
                            ,106
                            ,$fechaActual
                            ,$nurad
                            ,'$destinatarios'
                            ,'$destinatarios'
                            ,'0'
                            ,'0'
                            ,1
                            ,".$_SESSION["usua_doc"]."
                            ,'".$destinat."'
                            , '0' 
                            ,$fechaActual
                            ,".$dependencia."
                            , 1
                            ,$nurad 
                            ,'$destinatarios'
                            ,1 
                            ,1 
                            ,'Envio Respuesta Rapida a Correo Electronico'
                            ,$nurad 
                            ,'0'
                            ,'0'
                            ,'0'
                            ,'0'
                            ,$dependencia
                            ,'Colombia'
                            ,'0')"; 
               
        $rsRegenvio = $db->conn->Execute($iSqlEnvio);
    }

    $sqlE ="UPDATE	RADICADO
			SET		RADI_PATH = '$ruta3'
			WHERE	RADI_NUME_RADI = $nurad";
    
	$db->conn->Execute($sqlE);
	
	//Alimentamos log de imagenes.
	$sqlR = "INSERT INTO SGD_HIST_IMG_RAD
           (RADI_NUME_RADI
           ,RUTA
		   ,FECHA
           ,USUA_DOC
           ,USUA_LOGIN
           ,ID_TTR_HIAN)
     VALUES
           ($nurad, '$ruta3', ".$db->conn->sysTimeStamp." , '".$_SESSION['usua_doc']."', '$usua', 42)";
	$okR = $db->conn->Execute($sqlR);


    $isqlDepR ="SELECT	RADI_DEPE_ACTU,
                        RADI_USUA_ACTU
                FROM	RADICADO
                WHERE	RADI_NUME_RADI = $nurad";

    $rsDepR = $db->conn->Execute($isqlDepR);

    $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
    $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
	
	header("Location: salidaRespuesta.php?$encabe&nurad=$nurad".$errores);
?>
