<?php
	###########################################################################
	## ORFEO GPL: Sistema de Gestion Documental		http://www.orfeogpl.org	 ##
	## Idea Original de la													 ##
	## SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS					 ##
	##                   ===========================						 ##
	##																		 ##
	## Este programa es software libre. usted puede redistribuirlo y/o		 ##
	## modificarlo bajo los terminos de la licencia GNU General Public		 ##
	## publicada por la "Free Software Foundation"; Licencia version 2.		 ##
	## Copyright (c) 2005 por : SSPS										 ##
	##                   ===========================						 ##
	## Elaborado por:														 ##
	##	Ing. Cesar A. Gonzalez		aurigadl@gmail.com		2010-08	DNP		 ##
	##	Ing. Carlos E. Campos		careduc@gmail.com		2013-02 DNP		 ##
	###########################################################################

	###########################################################################
	##       EN ESTE ARCHIVO SE REALIZAN LAS SIGUIENTES ACCIONES:			 ##
	##	1. Se genera el numero para el radicado de la Respuesta (-1)		 ##
	##	2. Se registra el nuevo radicado en la tabla de Direcciones.		 ##
	##	3. Se inserta en historicos (Solicitud y respuesta) creacion del rad.##
	##	4. Se asocia la Respuesta como anexo de la solicitud.				 ##
	##	5. Se crea el PDF con la respuesta.									 ##
	##	6. Se registra en el historico de la respuesta, asociacion de imagen.##
	##	7. Se asocian los anexos al radicado de la respuesta.				 ##
	##	8. Se envia la respuesta por correo electronico.					 ##
	###########################################################################
	
	session_start();
	set_time_limit(0);
	if ($_SESSION["krd"])	
		$krd = $_SESSION["krd"];
	if (!isset($_SESSION['dependencia']))	
		include "../rec_session.php";

	$ruta_raiz = "..";
	$ruta_libs = $ruta_raiz."/respuestaRapida/";
	define('ADODB_ASSOC_CASE', 0);
	define('SMARTY_DIR', $ruta_libs . 'libs/');
	
	## Archivos requeridos
	require_once($ruta_raiz."/_conf/constantes.php");
	require_once($ruta_raiz."/include/db/ConnectionHandler.php");
	require_once($ruta_raiz."/tcpdf/config/lang/eng.php");
	include_once($ruta_raiz."/class_control/AplIntegrada.php");
	include_once($ruta_raiz."/include/tx/Tx.php");
	require_once($ruta_raiz.'/config.php');
	require_once($ruta_raiz.'/class_control/correoElectronico.php');
	
	$encabe = session_name()."=".session_id()."&krd=$krd";
	$pos = strpos('salidaRespuesta',$_SERVER['HTTP_REFERER']);
	if ($pos !== false) {
		header("Location: index.php?$encabe");
		die;
	}
	
	if (!$_POST){ 
	?>
		<script>
			alert('Uno de los archivos supera el limite de 15 Mb, por favor verifique e intente de nuevo');
			window.close()
		</script>
	<?php
		die;
	}

	$db	= new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	$numRadicadoPadre = $_POST["radPadre"];

	$objMail = new correoElectronico(".."); 
	
	$regFile		= array();
	$tamanoMax	= 15 * 1024 * 1024; // 15 mb
	$ddate		= date("d");
	$mdate		= date("m");
	$adate		= date("Y");
	//$fechproc4= substr($adate,2,4);
	
	$tamano	= 1000;
	$ent = '1';
	$usua_doc	= $_SESSION["usua_doc"];	// Cedula del usuario logeado.
	$coddepe	= $_POST["depecodi"];		// Cod. Dep. del usuario logeado.
	$usua_actu	= $_POST["usuacodi"];		// Cod. del usuario logeado.
	$usua		= $_POST["usualog"];		// Login del usuario logeado.
	$codigoCiu	= $_POST["codigoCiu"];		//
	$asu		= $_POST["respuesta"];		// Asunto.
	$usMail		= $_POST['usMailSelect'];	// Cuenta de correo del usuario logeado.
	$destMail	= $_POST["destinatario"];	// Cuenta de correo de los destinatarios.
	$copiaMail	= $_POST["concopia"];		// Cuenta de correo para las copias.
	$ccoMail	= $_POST["concopiaOculta"]; // Cuenta de correo para las copias ocultas.

	$radano = substr($numRadicadoPadre,0,4);
	$deppadre = substr($numRadicadoPadre, 4, 3);
	$ruta   = $adate.$coddepe.$usua_actu."_".time()."_Respuesta.pdf";
	
	## Enlace de la imagen de la Respuesta
	$ruta2 = "/bodega/$radano/$deppadre/docs/".$ruta;
	$ruta3 = "/$radano/$deppadre/docs/".$ruta;
	
	
	
	#############################################################################
	###	SE OBTIENEN DATOS DEL RADICADO PADRE
		$datRadPad = "SELECT	S.SGD_DIR_NOMREMDES,
								S.SGD_DIR_DIRECCION,
								S.SGD_DIR_MAIL,
								S.SGD_DIR_TELEFONO,
								S.SGD_SEC_CODIGO,
								S.SGD_CIU_CODIGO,
								R.RADI_PATH
					FROM		SGD_DIR_DRECCIONES S,
								RADICADO R
					WHERE		R.RADI_NUME_RADI = $numRadicadoPadre
													AND S.RADI_NUME_RADI = R.RADI_NUME_RADI";
		$rsDatRadPad = $db->conn->Execute($datRadPad);

		$codCiu		= $rsDatRadPad->fields["SGD_CIU_CODIGO"];
		$dirTel		= $rsDatRadPad->fields["SGD_DIR_TELEFONO"];
		$dirMail	= $rsDatRadPad->fields["SGD_DIR_MAIL"];
		$dirDir		= $rsDatRadPad->fields["SGD_DIR_DIRECCION"];
		$nomRemDes	= $rsDatRadPad->fields["SGD_DIR_NOMREMDES"];
		$pathPadre	= $rsDatRadPad->fields["RADI_PATH"];
		
	### FIN- SE OBTIENEN DATOS DEL RADICADO PADRE
	#############################################################################
	
	
	
	#############################################################################
	###	GENERA EL NUMERO DEL RADICADO DE SALIDA
	
		include_once($ruta_raiz."/include/tx/Radicacion.php");
		$rad = new Radicacion($db);
		
		$isql_sec ="SELECT	DEPE_RAD_TP$ent AS SECUENCIA
					FROM	DEPENDENCIA
					WHERE	DEPE_CODI = $coddepe";
		$creaNoRad = $db->conn->Execute($isql_sec);
		$tpDepeRad = $creaNoRad->fields["SECUENCIA"];
		
		$rad->radiTipoDeri  = 0;							// ??
		$rad->radiCuentai   = 'Null';						// Numero de referencia
		$rad->eespCodi      = $codCiu;						// codigo emepresa de servicios publicos bodega
		$rad->mrecCodi      = 4;							// Medio de correspondencia - 4 Mail
		$rad->radiFechOfic  = "$ddate/$mdate/$adate"; 		// Fecha del radicado;
		$rad->radiNumeDeri  = $numRadicadoPadre;			// Radicado de la solicitud (Rad. Padre)
		$rad->radiPais      = 170;							// Codigo pais - 170 Colombia
		$rad->descAnex      = '.';							// Descripcion de anexos del radicado
		$rad->raAsun        = "Respuesta al radicado " .$numRadicadoPadre; // Asunto del radicado
		$rad->radiDepeActu  = $coddepe;						// Dependencia del usuario radicador
		$rad->radiUsuaActu  = $usua_actu;					// Codigo del usuario radicador
		$rad->radiDepeRadi  = $coddepe;						// Dependencia del usuario que radica
		$rad->usuaCodi      = $usua_actu;					// Codigo del usuario radicador
		$rad->dependencia   = $coddepe;						// Dependencia que radica
		$rad->trteCodi      = 0;							// Tipo de codigo de remitente
		$rad->tdocCodi      = 0;							// Codigo Tipo Documental
		$rad->tdidCodi      = 0;							// ??
		$rad->carpCodi      = 1;							// Bandeja de salida
		$rad->carPer        = 0;							// Carpeta estandar
		$rad->radiPath      = 'null';						// Ruta de la imagen del radicado
		$rad->sgd_apli_codi = '0';							// ??
		$rad->usuaDoc       = $codigoCiu;					// ??

		$nurad = $rad->newRadicado($ent, $tpDepeRad);
		echo "RADICADOK ".$nurad;
		if (!$nurad){
			header("Location: salidaRespuesta.php?$encabe&error=1");
			die;
		}
	
	###	FIN - GENERA EL NUMERO DEL RADICADO DE SALIDA
	#############################################################################
	
	
	$prim	= substr($nurad, 0, 4);
	$seg	= substr($nurad, 4, 3);
	$aux	= $prim . "/" . $seg . "/docs/";
	$adjunto  = trim(str_replace(" ","", BODEGAPATH));
	$adjuntos = str_replace("/","\\",$adjunto.$aux);
	
	
	#############################################################################
	### SE REGISTRAN LOS DATOS DEL RADICADO EN LA TABLA DIRECCIONES
	
		$nextval = $db->nextId("SEC_DIR_DIRECCIONES");
		$isql ="INSERT INTO	
					SGD_DIR_DRECCIONES(	SGD_TRD_CODIGO,
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
						VALUES (1,
								'$nomRemDes',
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
								'$dirDir',
								'$dirTel',
								'$dirMail',
								1,
								$nextval,
								'$nomRemDes')";
		$rsg = $db->conn->Execute($isql);
		if (!$rsg){
			header("Location: salidaRespuesta.php?$encabe&error=11");
			die;
		}
	
	## FIN- SE REGISTRAN LOS DATOS DEL RADICADO EN LA TABLA DIRECCIONES
	#############################################################################

	
	
	#############################################################################
	### SE REGISTRA EN HISTORICO (ENTRADA Y SALIDA) LA GENERACION DE LA RESPUESTA
	
		include_once($ruta_raiz."/include/tx/Historico.php");
		$hist = new Historico($db);
		
		$comentario = "Se genera respuesta rapida No. ".$nurad;
		if(!empty($regFile))	$comentario .= ", con archivos adjuntos";

		$radicadosSel[0] = $numRadicadoPadre;
		$hist->insertarHistorico(	$radicadosSel,
									$coddepe,
									$usua_actu,
									$coddepe,
									$usua_actu,
									$comentario,
									62);

		$comentario ="Se genera respuesta rapida, para dar respuesta al radicado No. ".$numRadicadoPadre;

		$radicadosSel[0] = $nurad;
		$hist->insertarHistorico(	$radicadosSel,
									$coddepe,
									$usua_actu,
									$coddepe,
									$usua_actu,
									$comentario,
									2);
	
	### FIN- SE REGISTRA EN HISTORICO (ENTRADA Y SALIDA) LA GENERACION DE LA RESPUESTA
	#############################################################################
	
	
	
	#############################################################################	
	### SE ANEXA EL NUEVO RADICADO (-1) COMO RESPUESTA DE LA SOLICITUD (-2)
	
		include_once($ruta_raiz."/class_control/anexo.php");
		$anex = new Anexo($db);
		
		$sqlFechaHoy = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
		$auxnumero = $anex->obtenerMaximoNumeroAnexo($nurad);
		
		do{
			$auxnumero += 1;
			$codigo     = trim($numRadicadoPadre) . trim(str_pad($auxnumero, 5, "0", STR_PAD_LEFT));
		} while ($anex->existeAnexo($codigo));

		$isql = "INSERT INTO ANEXOS (	SGD_REM_DESTINO,
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
						values(	1,
								$numRadicadoPadre,
								'$codigo',
								2,
								7,
								$tamano,
								'N',
								'$usua',
								'Respuesta de PQR',
								$auxnumero,
								'$ruta',
								'N',
								1,
								1,
								$coddepe,
								NULL,
								$sqlFechaHoy,
								NULL,
								1,
								$nurad,
								NULL,
								1)";

		$bien = $db->conn->Execute($isql);
		if (!$bien) {
			$errores .= empty($errores)? "&error=7" : '-7';
		}
	
	### FIN- SE ANEXA EL NUEVO RADICADO (-1) COMO RESPUESTA DE LA SOLICITUD (-2)
	#############################################################################


	#############################################################################
	###	CREACION DE LA IMAGEN DEL RADICADO RESPUESTA - PDF
	
		require_once($ruta_raiz."/tcpdf/tcpdf.php");
		
		if (empty($errores)){
			$fecha1		= time();
			$fecha		= fechaFormateada($fecha1);
			
			$cond ="SELECT	DEP_SIGLA, 
							DEPE_NOMB 
					FROM	DEPENDENCIA 
					WHERE	DEPE_CODI = $coddepe";
			$exte = $db->conn->Execute($cond);

			$dep_sig = $exte->fields["DEP_SIGLA"];
			$dep_nom = $exte->fields["DEPE_NOMB"];

			## Extiende de la clase TCPDF, para crear encabezado y pie de pagina personalizados
			class MYPDF extends TCPDF 
			{
				## Encabezado de la pagina
				public function Header() {
					## Logo
					$this->Image('../img/banerPDF.JPG',30,10,167,'','JPG','','T',false,300,'',false,false,0,false,false,false);
				}

				## Pie de pagina
				public function Footer() {
					## Posicion a 15 mm de la parte inferior
					$this->SetY(-20);
					## Numero de pagina
					$txt = "Calle 26 # 13-19 C&oacute;digo Postal 110311 Bogot&aacute;, D.C., Colombia PBX 381 5000 www.dnp.gov.co";
					$this->writeHTMLCell($w=0,$h=3,$x='20',$y='',$txt,$border=0,$ln=1,$fill=0,$reseth=true,'C');
				}
			}

			## Crea el documento en PDF
			$pdf = new MYPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
			## Informaciion general del PDF
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('Sistema de Gestion Documental Orfeo');
			$pdf->SetTitle('Respuesta de Solicitud PQR');
			$pdf->SetSubject('Departamento Nacional de Planeación');
			$pdf->SetKeywords('dnp, respuesta, salida, generar');

			## Se establecen datos para la cabecera
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

			## Se establecen Fuentes para el encabezado y pie de pagina
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

			## Se define la fuente predeterminada
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			## Se establecen margenes
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			## Se establecen saltos de pagina automaticos
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			## Set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			## Set some language-dependent strings
			$pdf->setLanguageArray($l);

			## Set default font subsetting mode
			$pdf->setFontSubsetting(true);
			
			## Adiciona una nueva pagina
			## This method has several options, check the source code documentation for more information.
			$pdf->AddPage();
			
			## Se obtiene el código de barras del número del radicado
			$codBar = $pdf->write1DBarcode($nurad, 'c39', 145, '', 50, 10);
						
			$asu ='	<p> <b> <table>
					<tr>
						<td width="60%" align="left"> Bogot&aacute; D.C., '.$fecha.' </td>
						<td width="40%" align="right"> '.$codBar.' </td>
					</tr>
					<tr>
						<td width="60%" align="left"> '.$dep_sig.' </td>
						<td width="40%" align="right"> </td>
					</tr>
					<tr>
						<td width="60%" align="left"> </td>
						<td width="40%" align="right"> al responder cite este n&uacute;mero </td>
					</tr>
					<tr>
						<td width="60%" align="left"> </td>
						<td width="40%" align="right"> '.$nurad.' </td>
					</tr>
					</table> </b> <p/> <br/>
					'.$asu.' <br/> <br/>
					Atentamente <br/>
					'.$dep_nom.' <br/>
					'.$usMail.' <br/>
					CC -> '.$copiaMail;

			## Salida del contenido HTML
			$pdf->writeHTML($asu, true, false, true, false, '');
		
			// Cierra el documento PDF
			// This method has several options, check the source code documentation for more information.
			$pdf->Output($ruta_raiz.$ruta2, 'F');

			$adjunto = trim(str_replace(" ","", BODEGAPATH));
			$post = strpos(strtolower($pathPadre),'bodega');
			
			if($post !== false){
				$pathPadre = substr($pathPadre,$post + 6);	
			}
		
			$filepad = str_replace("/", "\\", str_replace("//", "\\", $adjunto.$ruta3)); 
			$filepad = str_replace("/", "\\", str_replace("//", "\\", $filepad));	
			
			if (file_exists($filepad)){
				$sqlE ="UPDATE	RADICADO
						SET		RADI_PATH = '$ruta3'
						WHERE	RADI_NUME_RADI = $nurad";
				$db->conn->Execute($sqlE);
				$objMail->agregarAdjunto($filepad, 'Respuesta.pdf');
			}
			else {
				$errores .= empty($errores)? "&error=12" : '-12';
				header("Location: salidaRespuesta.php?$encabe&error=12");
			}
		}
	
	###	FIN- CREACION DE LA IMAGEN DEL RADICADO RESPUESTA - PDF		
	#############################################################################
	
	
	
	#############################################################################	
	###	REGISTRO EN EL HISTORICO DE IMAGEN ASOCIADA AL RADICADO RESPUESTA
	
		$hist->insertarHistorico(	$radicadosSel,
									$coddepe,
									$usua_actu,
									$coddepe,
									$usua_actu,
									"Imagen asociada desde respuesta rapida",
									42);
		
	###	FIN- REGISTRO EN EL HISTORICO DE IMAGEN ASOCIADA AL RADICADO RESPUESTA
	#############################################################################
	
	
	
	#############################################################################	
	###	ASOCIAR ARCHIVOS ADJUNTOS AL RADICADO RESPUESTA
	
		## VALIDA ARCHIVOS ADJUNTOS
		if(!empty($_FILES["archs"]["name"][0])) {
			$sql1 ="SELECT	ANEX_TIPO_CODI AS CODIGO,
							ANEX_TIPO_EXT AS EXT,
							ANEX_TIPO_MIME AS MIME
					FROM	ANEXOS_TIPO";
			$exte = $db->conn->Execute($sql1);

			while(!$exte->EOF) {
				$codigo = $exte->fields["CODIGO"];
				$ext		= $exte->fields["EXT"];
				$mime1	= $exte->fields["MIME"];
				$mime2	= explode(",",$mime1);
				## Arreglo para validar la extensión
				$exts[".".$ext]	= array ('codigo' => $codigo, 'mime' => $mime2);
				$exte->MoveNext();
			}

			##Si no existe la carpeta se crea.
			if(!is_dir($adjuntos)){
				$rs	= mkdir($adjuntos, 0700);
				if(empty($rs)){
					$errores .= empty($errores)? "&error=2" : '-2';
				}
			}

			$anexo = new Anexo($db);

			## Validaciones y envio para grabar archivos
			foreach($_FILES["archs"]["name"] as $key => $name) {
				$nombre 	= strtolower(trim($_FILES["archs"]["name"][$key]));
				$type		= trim($_FILES["archs"]["type"][$key]);
				$tamano		= trim($_FILES["archs"]["size"][$key]);
				$tmporal	= trim($_FILES["archs"]["tmp_name"][$key]);
				$error	= trim($_FILES["archs"]["error"][$key]);
				$ext		= strrchr($nombre,'.');
				
				if (is_array($exts[$ext])){
					foreach ($exts[$ext]['mime'] as $value){
						if(eregi($type,$value)){
							$bandera = true;
							if($tamano < $tamanoMax){
								//grabar el registro en la base de datos
								if(strlen($nombre) > 60){
									$nombre	= substr($nombre, '-60:');
								}
								$anexo->anex_radi_nume		= $nurad;
								$anexo->usuaCodi			= $usua_actu;
								$anexo->depe_codi			= $coddepe;
								$anexo->anex_solo_lect		= "'S'";
								$anexo->anex_tamano			= $tamano;
								$anexo->anex_creador		= "'".$usua."'";
								$anexo->anex_desc			= "Adjunto: ". $nombre;
								$anexo->anex_nomb_archivo	= $nombre;
							
								$auxnumero	 = $anexo->obtenerMaximoNumeroAnexo($nurad);
								$anexoCodigo = $anexo->anexarFilaRadicado($auxnumero);
								$nomFinal	 = $anexo->get_anex_nomb_archivo();
							
								//Guardar el archivo en la carpteta ya creada
								$Grabar_path = $adjuntos.$nomFinal;
								$upOk = move_uploaded_file($tmporal, $Grabar_path);
							
								if ($upOk) {
									## Si existen adjuntos los agregamos para enviarlos por correo
									$objMail->agregarAdjunto($Grabar_path, $nombre);
								}
								else {
									$errores .= empty($errores)? "&error=6&UpOk=$upOk" : '-6';
								}
							}
							else{
								$errores .= empty($errores)? "&error=5" : '-5';
							}
						}
					}

					if(empty($bandera)){
						$errores .= empty($errores)? "&error=4" : '-4';
					}
				}
				else{
					$errores .= empty($errores)? "&error=3" : '-3';
				}
			}
		}
		
	###	FIN- ASOCIAR ARCHIVOS ADJUNTOS AL RADICADO RESPUESTA
	#############################################################################

	
	
	#############################################################################	
	###	ENVIA LA RESPUESTA POR CORREO ELECTRONICO
		
		## SE ADJUNTA LA IMAGEN DEL RADICADO PADRE, PARA ENVÍO POR CORREO
		$bodeg =  trim(str_replace(" ","", BODEGAPATH));
		$ruta1 = str_replace("/", "\\", str_replace("//", "\\", $bodeg . $pathPadre));
		$objMail->agregarAdjunto($ruta1, 'Solicitud.pdf');
		
		if (empty($errores)){
			$destinatarios 	= "Destino:".$destMail." Copia:".$copiaMail;
			
			if(trim($destMail))
				$dest = explode(";",$destMail);

			if(trim($copiaMail))
				$copia = explode(";",$copiaMail);

			if(trim($ccoMail))
				$ccoMail = explode(";",$ccoMail);

			$asunto = "Respuesta del Departamento Nacional de Planeacional DNP a su solicitud No. " .$numRadicadoPadre;
			$cuerpo = "<br> El Departamento Nacional de Planeaci&oacute;n le informa que se ha dado respuesta a su solicitud 
						No. $numRadicadoPadre mediante el oficio de salida No. $nurad, el cual tambi&eacute;n puede ser 
						consultado en el portal Web del DNP.</p> <br><br><b> <center> Si no puede visualizar el correo,
						o los archivos adjuntos, puede consultarlos tambi&eacute;n en la siguiente direcci&oacute;n: <br>
						<a href='http://orfeo.dnp.gov.co/pqr/consulta.php?rad=$numRadicadoPadre'>
						http://orfeo.dnp.gov.co/pqr/consulta.php </a><br><br><br> DNP </b></center><BR>";
			$objMail->FromName = "Web OI";
			$result = $objMail->enviarCorreo($dest, $copia, $ccoMail, $asunto, $cuerpo);
		
		
			###	SI EL MAIL SE ENVIO, SE REGISTRA EL ENVIO EN EL HISTORICO DE LA RESPUESTA
			if ($result == True) {
				$sql_sgd_renv_codigo = "SELECT	SGD_RENV_CODIGO 
										FROM 	SGD_RENV_REGENVIO 
										ORDER BY SGD_RENV_CODIGO DESC ";

				$rsRegenvio		= $db->conn->SelectLimit($sql_sgd_renv_codigo,2);
				$nextval       	= $rsRegenvio->fields["SGD_RENV_CODIGO"];
				$nextval++;
				$fechaActual   	= $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
				$destinatarios 	= "Destino:".$destMail." Copia:".$copiaMail;
				$dependencia   	= $_POST["depecodi"];

				$iSqlEnvio = " INSERT INTO 
								SGD_RENV_REGENVIO(	 SGD_RENV_CODIGO
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
								VALUES (	$nextval
											,106
											,$fechaActual
											,$nurad
											,'$destinatarios'
											,'$destinatarios'
											,'0'
											,'0'
											,1
											,".$_SESSION["usua_doc"]."
											,'".$destMail."'
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
				
				//Alimentamos log de imagenes.
				$sqlR = "INSERT INTO SGD_HIST_IMG_RAD
					   (RADI_NUME_RADI, RUTA, FECHA, USUA_DOC, USUA_LOGIN, ID_TTR_HIAN)
				 VALUES
					   ($nurad, '$ruta3', ".$db->conn->sysTimeStamp." , '".$_SESSION['usua_doc']."', '$usua', 42)";
				$okR = $db->conn->Execute($sqlR);
			}
			else {
				$errores .= empty($errores)? "&error=8" : '-8';
			}
		}
		
	###	ENVIA LA RESPUESTA POR CORREO ELECTRONICO
	#############################################################################

	
	header("Location: salidaRespuesta.php?$encabe&resul=$result&nurad=$nurad".$errores);
	
	
	#############################################################################
	### FUNCION QUE CONVIERTE CARACTERES CON ACENTO A CARACTERES SIN ACENTO
	
		function StrValido($cadena)
		{
			/* Description: Funcion que convierte los acentos y caracteres extranos para
			 *				evitar que se rompa el codigo a causa de estos caracteres.
			 * @param,	$cadena: Variable que trae el login original del usuario incluir
			 * @var,	$login; variable en donde se almacen el login y es procesado.
			 * @return,	$login: Variable con el login del usuario, pero sin acentos.
			 * @Creado Nov 22 de 2012
			 * @autor: DNP
			 */
			$cadena = strtolower($cadena);
			$original = array("á","é","í","ó","ú","ä","ë","ï","ö","ü","à","è","ì","ò","ù","ñ",",",";",":","¡","!","\¿","/?",'"',"/","&","\n","#","\$","*","%","+","[","]","=","¬","|","^","`","~","\\","'");
			$nuevas	  = array("a","e","i","o","u","a","e","i","o","u","a","e","i","o","u","n","","","","","","","","",'',"","","","","","","","","","","","","","","","","",);
			$cadena = $cadena;
			//$login = str_replace($b,$c,$login);
			$cadena = strtr($cadena, $original, $nuevas);
			$cadena = preg_replace('@^([^\?]+)(\?.*)$@','\1',$cadena);
			return $cadena;
		}
	
	### FIN - FUNCION QUE CONVIERTE CARACTERES CON ACENTO A CARACTERES SIN ACENTO
	#############################################################################
	
	
		
	#############################################################################
	### FUNCI�N QUE CONVIERTE LA FECHA EN FORMATO DE LETRAS
	
		function fechaFormateada($FechaStamp)
		{
			$ano = date('Y', $FechaStamp);			// Año
			$mes = date('m', $FechaStamp);			// Numero de mes (01-31)
			$dia = date('d', $FechaStamp);			// Dia del mes (1-31)
			$dialetra = date('w', $FechaStamp); // Dia de la semana(0-7)

			switch ($dialetra) {
				case 0:	$dialetra = "domingo";
					break;
				case 1:	$dialetra = "lunes";
					break;	
				case 2:	$dialetra = "martes";
					break;
				case 3:	$dialetra = "miercoles";
					break;
				case 4:	$dialetra = "jueves";
					break;
				case 5:	$dialetra = "viernes";
					break;
				case 6:	$dialetra = "sabado";
					break;
			}

			switch ($mes) {
				case '01':	$mesletra = "enero";
					break;
				case '02':	$mesletra = "febrero";
					break;
				case '03':	$mesletra = "marzo";
					break;
				case '04':	$mesletra = "abril";
					break;
				case '05':	$mesletra = "mayo";
					break;
				case '06':	$mesletra = "junio";
					break;
				case '07':	$mesletra = "julio";
					break;
				case '08':	$mesletra = "agosto";
					break;
				case '09':	$mesletra = "septiembre";
					break;
				case '10':	$mesletra = "octubre";
					break;
				case '11':	$mesletra = "noviembre";
					break;
				case '12':	$mesletra = "diciembre";
					break;
			}
			return htmlentities("$dialetra, $dia de $mesletra de $ano");
		}
	
	### FIN - FUNCI�N QUE CONVIERTE LA FECHA EN FORMATO DE LETRAS
	#############################################################################
?>