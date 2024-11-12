<?php
require_once 'util/referenciaServicios/OrfeoServiceWCF/OrfeoServiceWCFClient.class.php';
class Radicado_Model extends Model {
	public function __construct() {
		parent::__construct ();
	}
	/**
	 *
	 * @param
	 *        	type JSON $usuarioRadica (UsuarioTXDS) par ver el detalle de los valores posibles ver el WSDL en el siguiente enlace: https://orfeoservice.dnp.gov.co/OrfeoServiceWCF/OrfeoServiceWCF.svc?xsd=xsd2
	 * @param
	 *        	type JSON $loginUsuarioDestino (UsuarioTXDS) par ver el detalle de los valores posibles ver el WSDL en el siguiente enlace: https://orfeoservice.dnp.gov.co/OrfeoServiceWCF/OrfeoServiceWCF.svc?xsd=xsd2
	 * @param
	 *        	type JSON $datosContacto (DatosContactoDS)par ver el detalle de los valores posibles ver el WSDL en el siguiente enlace: https://orfeoservicetest.dnp.gov.co/OrfeoServiceWCF/OrfeoServiceWCF.svc?xsd=xsd4
	 * @param
	 *        	type JSON $datosRadicado (DatosRadicacionDS) par ver el detalle de los valores posibles ver el WSDL en el siguiente enlace: https://orfeoservicetest.dnp.gov.co/OrfeoServiceWCF/OrfeoServiceWCF.svc?xsd=xsd5
	 */
	public function radicacionSOA($usuarioRadica, $usuarioDestino, $datosContacto, $datosRadicado) {
		try {
			$this->ClienteOrfeoServiceWCF = new OrfeoServiceWCFClient ();
			$response = $this->ClienteOrfeoServiceWCF->radicarDocumentoJSON ( $usuarioRadica, $usuarioDestino, $datosContacto, $datosRadicado );
			$retorno = ( array ) json_decode ( $response, true );
			return $retorno ["RespuestaRadicadoDT"] [0];
		} catch ( Exception $ex ) {
			$retorno ['estado'] = false;
			$retorno ['mensaje'] = "<h2>Exception Error!</h2></b>" . $ex->getMessage ();
			return $retorno;
		}
	}
	function buscarRadicadosSOA() {
		
		// Pendiente Implementación para cosumir los Servicios WCF
		$parametros = Array (
				"NoRadicado" => "20139000052321",
				"usuarioActual" => $_SESSION ['krd'],
				// "codigoCarpeta"=>"0",
				"fechaInicio" => "1900/01/01" 
			// "esCarpetaPersonal"=>false
		);
		if (isset ( $_GET )) {
			if (isset ( $_GET ['sort'] )) {
				$parametros ['campoOrden'] = $_GET ['sort'];
			}
			if (isset ( $_GET ['dir'] )) {
				$parametros ['tipoOrden'] = $_GET ['dir'];
			}
			if (isset ( $_GET ['limit'] )) {
				$parametros ['NoRegistrosPagina'] = $_GET ['limit'];
			}
			if (isset ( $_GET ['page'] )) {
				$parametros ['NoPaginaResultado'] = $_GET ['page'];
			}
			if (isset ( $_GET ['callback'] )) {
				$callBack = $_GET ['callback'];
			}
			if (isset ( $_GET ['carp_codi'] )) {
				$parametros ['codigoCarpeta'] = $_GET ['carp_codi'];
			} else {
				$parametros ['codigoCarpeta'] = - 1;
			}
			if (isset ( $_GET ['carp_per'] )) {
				$parametros ['esCarpetaPersonal'] = ($_GET ['carp_per'] > 0) ? true : false;
			} else {
				$parametros ['esCarpetaPersonal'] = false;
			}
		}
		// var_dump($datos);
		$this->ClienteOrfeoServiceWCF = new OrfeoServiceWCFClient ();
		$json1XY = "{UsuarioTXDT:" . json_encode ( Array (
				Array (
						"documento" => "800975021",
						"login" => "carlos" 
				) 
		) ) . "}";
		$json2 = "{datosConsultarRadicado:" . json_encode ( Array (
				$parametros 
		) ) . "}";
		$response = $this->ClienteOrfeoServiceWCF->consultarRadicadoJSON ( $json1XY, $json2 );
		$repArray = json_decode ( $response, true );
		$estado = $repArray ["respuestaEstado"];
		$lst = $repArray ["datosGenerales"];
		
		if ($estado [0] ['estado'] === true) {
			$datos ['NoRegistrosPagina'] = $estado [0] ['NoRegistrosPagina'];
			$datos ["datosGenerales"] = $lst;
			if (isset ( $callBack )) {
				header ( 'Content-Type: application/javascript' );
				echo "$callBack(" . json_encode ( $datos ) . ");";
			} else {
				echo $response;
			}
		} else {
			echo "este es el mensaje!" . $estado [0] ['mensaje'];
		}
	}
	public function radicacion() {
		echo "Pendiente implementacion con persistencia a BD directa!";
	}
	
	/**
	 *
	 * @param string $radi_nume_radi        	
	 * @return Array with data from Radicado.
	 */
	public function datosRadicado($radi_nume_radi) {
		$sql = "select * from radicado where radi_nume_radi=?";
		$rs = $this->db->select ( $sql, array (
				$radi_nume_radi 
		), false );
		if ($rs && ! $rs->EOF) {
			return $rs->fields;
		} else {
			return false;
		}
	}
	
	/**
	 *
	 * @param integer $radi_nume_radi        	
	 * @return boolean
	 */
	public function datosExpedienteRadicado($radi_nume_radi) {
		$sql = "select e.RADI_NUME_RADI
                from sgd_exp_expediente e inner join SGD_SEXP_SECEXPEDIENTES s on e.SGD_EXP_NUMERO = s.sgd_exp_numero 
                where radi_nume_radi= ? AND E.SGD_EXP_ESTADO <> 2 and s.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas')";
		$rs = $this->db->select ( $sql, array (
				$radi_nume_radi 
		), false );
		if ($rs && ! $rs->EOF) {
			$datos = null;
			while ( ! $rs->EOF ) {
				$datos [] = $rs->fields;
				$rs->MoveNext ();
			}
			
			return $datos;
		} else {
			return false;
		}
	}
	
	public function respuestaRapida() {
		$msg = null;
		$band = true;
		$msgFile = null;
		require_once '_anexo_model.php';
		$objAnexo = new _Anexo_Model ();
		$pathsAttachment = null;
		$objCodificacionEspecial = new CodificacionEspecial ();
		
		// Se validan los datos de $_FILE		
		try {
			if (isset ( $_FILES )) {
				if (is_array ( $_FILES ) && count ( $_FILES ) > 0) {
					
					foreach ( $_FILES as $key => $value ) {
						
						if ($value ['size'] > $objAnexo->return_bytes ( ini_get ( 'upload_max_filesize' ) ) || (strlen ( $value ['name'] ) > 0 && ! $value ['size'])) {
							$band = false;
							$msg [] = "Verifique el tama&ntilde;o del Archivo: " . $value ['name'] . " Excede el tama&ntilde;o permitido" . ini_get ( 'upload_max_filesize' ) . "!\n";
						}
					}
				}
			}
			
			// Paso preliminar: Se preparan las variables necesarias.
			if (isset ( $_POST ) && $band == true) {
				
				$datosContacto = null;
				$datosRadicado = null;
				$usuarioRadica ['UsuarioTXDT'] [] = Array (
						"login" => $_SESSION ['krd'] 
				);
				$usuarioDestino ['UsuarioTXDT'] [] = Array (
						"login" => $_SESSION ['krd'] 
				);
				if (isset ( $_POST ['data'] )) { // se capturan los datos del registro enviado desde la vista de carpetas
					
					$data = json_decode ( $_POST ['data'] );
					if (is_array ( $data ) && count ( $data ) > 0) {
						$datosRegistroSeleccionado = ( array ) $data [0];
					}
					if (is_array ( $datosRegistroSeleccionado ) && count ( $datosRegistroSeleccionado ) > 0) {
						// Se Obtienen los datos del contacto asociado.
						if ($datosRegistroSeleccionado ['idCiudadano']) {
							$datosContacto ['CiudadanoDT'] [] = Array (
									"idCiudadano" => $datosRegistroSeleccionado ['idCiudadano'] 
							);
						}
						if ($datosRegistroSeleccionado ['idEmpresa']) {
							$datosContacto ['EmpresaDT'] [] = Array (
									"idEmpresa" => $datosRegistroSeleccionado ['idEmpresa'] 
							);
						}
						if ($datosRegistroSeleccionado ['idEntidad']) {
							$datosContacto ['EntidadDT'] [] = Array (
									"idEntidad" => $datosRegistroSeleccionado ['idEntidad'] 
							);
						}
						if ($datosRegistroSeleccionado ['loginFuncionario']) {
							$datosContacto ['FuncionarioDT'] [] = Array (
									"login" => $datosRegistroSeleccionado ['loginFuncionario'] 
							);
						}
						// Se configuran los datos propios de la radicacion.
						$datosRadicado ['DatosRadicacionDT'] [] = array_map ( "htmlentities", Array (
								"asunto" => "Respuesta al radicado No." . $datosRegistroSeleccionado ['nroradicado'],
								"tipoRadicado" => 1,
						        "medioRecepcionEnvio" => 115,
								"fechaOficio" => date ( 'Y/m/d' ),
								"noRadicadoPadre" => $datosRegistroSeleccionado ['nroradicado'] 
						) );
					} else {
						$msg [] = "No se envi&oacute; la informaci&oacute;n con la variable data en formato JSON correctamente por el metodo POST, verifiquela";
						$band = false;
					}
					// Validamos directorio donde se alojará el PDF del radicado
					$anoPath = date ( 'Y' );
					$depPath = substr ( $datosRegistroSeleccionado ['nroradicado'], 4, 3 );
					if (! is_dir ( BODEGAPATH."$anoPath/$depPath/docs/" )) {
						$msg [] = "No es posible realizar el proceso de Respuesta Rapida debido a que no existe la carpeta donde se alojara el PDF generado. verifique con el Administrador.";
						$band = false;
					}
				} else {
					$msg [] = "Uno de sus archivos adjuntos supera el tama&ntilde;o permitido(No data POST), verifiquela";
					$band = false;
				}
				// PASO 1. Generar Radicado a traves del Servicio.
				if ($band == true) // Importante validar que no hayan ocurrido errores al configurar las variables para el paso 1.
				{
					// Llamamos el Servicio de Radicacion
					$respuestaRadicacion = $this->radicacionSOA ( json_encode ( $usuarioRadica ), json_encode ( $usuarioDestino ), json_encode ( $datosContacto ), json_encode ( $datosRadicado ) );
					if (is_array ( $respuestaRadicacion ) && count ( $respuestaRadicacion ) > 0) {
						
						if ($respuestaRadicacion ["estado"] = true && $respuestaRadicacion ["NoRadicado"]) {
							// Paso 1.1 Actualizar Historicos Radicado y RadicadoPadre.
							
							$msg [] = "Se ha generado el Radicado No. " . $respuestaRadicacion ["NoRadicado"];
							require_once 'historico_model.php';
							$objHist = new Historico_Model ();
							$comentario = "Se genera respuesta rapida, para dar respuesta al radicado No." . $datosRegistroSeleccionado ['nroradicado'];
							$hist1 = $objHist->insertarHistoricoRadicado ( $respuestaRadicacion ["NoRadicado"], $_SESSION ["dependencia"], $_SESSION ["codusuario"], $_SESSION ["usua_doc"], $_SESSION ["dependencia"], $_SESSION ["codusuario"], $_SESSION ["usua_doc"], $comentario, 2 );
							if ($hist1 == false) {
								$msg [] = "Ocurri&oacute; una falla al insertar en el Historico del Radicado No." . $respuestaRadicacion ["NoRadicado"];
								$band = false;
							}
							
							$comentario = "Se genera respuesta rapida No." . $respuestaRadicacion ["NoRadicado"];
							$hist2 = $objHist->insertarHistoricoRadicado ( $datosRegistroSeleccionado ['nroradicado'], $_SESSION ["dependencia"], $_SESSION ["codusuario"], $_SESSION ["usua_doc"], $_SESSION ["dependencia"], $_SESSION ["codusuario"], $_SESSION ["usua_doc"], $comentario, 62 );
							if ($hist2 = false) {
								$msg [] = "Ocurri&oacute; una falla al insertar en el Historico del Radicado No." . $datosRegistroSeleccionado ['nroradicado'];
								$band = false;
							}
						} else {
							$msg [] = "El servicio de Radicaci&oacute;n responde con el siguiente mensaje:" . $respuestaRadicacion ["mensaje"];
							$band = false;
						}
					} else {
						$msg [] = "La respuesta del servicio de radicacion no fu6eacute; la adecuada." . $respuestaRadicacion;
						$band = false;
					}
				}
			} else {
				$msg [] = "No se envi&oacute; la informaci&oacute;n correctamente por el metodo POST, verifiquela";
				$band = false;
			}
			
			if ($band) // Si llegamos a este Punto el Paso 1 ya ha sido superado!.
			{
				// Paso 2. Crea PDF
				try {
					$objPDF = new GeneradorPDF ();
					$nombreArchivo = $_SESSION ['krd'] . time () . "_Respuesta.pdf";
					$pahtPDF = "$anoPath/$depPath/docs/" . $nombreArchivo;
					
					$genPDF = $objPDF->generaPDFRespuestaRapida ( $respuestaRadicacion ["NoRadicado"], $pahtPDF, $_POST ['mensaje'], $_POST ['nremitente'], $_POST ['destinatario'], $_POST ['cc'] );
					
					$pathsAttachment [0] ['original'] = $pahtPDF;
					$pathsAttachment [0] ['nameAttachment'] = "Respuesta.pdf";
					
					$datosRadPadre = $this->datosRadicado ( $datosRegistroSeleccionado ['nroradicado'] );
					if ($datosRadPadre && is_array ( $datosRadPadre ) && isset ( $datosRadPadre ['RADI_PATH'] ) && strlen ( $datosRadPadre ['RADI_PATH'] ) > 0) {
						$pathFileRadPadre = BODEGAPATH . $datosRadPadre ['RADI_PATH'];
						if (is_file ( $pathFileRadPadre ) && strpos ( $pathFileRadPadre, ".pdf" )) {
							$pathsAttachment [1] ['original'] = $datosRadPadre ['RADI_PATH'];
							$pathsAttachment [1] ['nameAttachment'] = "Solicitud.pdf";
						}
					}
				} catch ( Exception $exc ) {
					$msg [] = "Error al crear el PDF, favor comunicar el siguiente error:" . $exc->getMessage ();
					echo $objCodificacionEspecial->jsonRemoveUnicodeSequences ( json_encode ( Array (
							"success" => false,
							"msg" => $msg 
					) ) );
					die ();
				}
				
				// Paso 2.1 Asocia PDF generado al Radicado
				if (file_exists ( BODEGAPATH . $pahtPDF )) {
					$datos = Array (
							"RADI_PATH" => $pahtPDF 
					); // Se actualiza el Path del Radicado.
					if (! $this->actualizaDatos ( $respuestaRadicacion ["NoRadicado"], $datos )) {
						$msg [] = "Hubo un error al actualizar el PATH en la BD.";
						$band = false;
					} else {
						unset ( $datos );
						$datos = Array (
								"ANEX_NOMB_ARCHIVO" => $nombreArchivo,
								"ANEX_TIPO" => 7,
								"ANEX_ESTADO" => 3,
								"ANEX_ESTADO_EMAIL" => 1 
						); // Se actualiza el Path del Anexo.
						if (! $objAnexo->actualizaDatosAnexo ( $datos, "RADI_NUME_SALIDA=" . $respuestaRadicacion ["NoRadicado"] )) {
							$msg [] = "Hubo un error al actualizar el PATH del anexo en la BD.";
							$band = false;
						} else {
							$datosAnexo = $objAnexo->obtenerAnexoSalida ( $respuestaRadicacion ["NoRadicado"] );
							if (is_array ( $datosAnexo ) && count ( $datosAnexo ) > 0) {
								$comentario = "Imagen asociada desde respuesta rapida";
								$hist1 = $objHist->insertarHistoricoRadicado ( $respuestaRadicacion ["NoRadicado"], $_SESSION ["dependencia"], $_SESSION ["codusuario"], $_SESSION ["usua_doc"], $_SESSION ["dependencia"], $_SESSION ["codusuario"], $_SESSION ["usua_doc"], $comentario, 42 );
								if ($hist1 == false) {
									$msg [] = "Ocurri&oacute; una falla al insertar en el Historico del Radicado No." . $respuestaRadicacion ["NoRadicado"];
									$band = false;
								}
								if (! $objHist->insertarHistoricoImagenAnexo ( $datosRegistroSeleccionado ['nroradicado'], $datosAnexo ['ANEX_CODIGO'], $nombreArchivo, $_SESSION ['usua_doc'], $_SESSION ['krd'], 20 )) { // Se adiciona historico de imagen de anexo
									$msg [] = "Hubo un error al insertar el historico de la imagen del Anexo en la BD.";
									$band = false;
								}
							} else {
								$msg [] = "No se pudo obtener los datos del anexo!";
								$band = false;
							}
						}
						if (! $objHist->insertarHistoricoImagenRadicado ( $respuestaRadicacion ["NoRadicado"], $pahtPDF, $_SESSION ['usua_doc'], $_SESSION ['krd'], 20 )) { // Se adiciona historico de imagen.
							$msg [] = "Hubo un error al insertar el historico de la imagen principal del radicado en la BD.";
							$band = false;
						}
					}
				} else {
					$msg [] = "No se guard&oacute; el archivo PDF.";
					$band = false;
				}
				
				// PASO 3. Adjuntar Archivos
				// adjuntar:
				$anoPath = date ( 'Y' );
				$depPath = substr ( $respuestaRadicacion ["NoRadicado"], 4, 3 );
				$nombreArchivo = $_SESSION ['krd'] . time () . "_Respuesta.pdf";
				$pahtPDF = "$anoPath/$depPath/docs/" . $nombreArchivo;
				
				if (isset ( $_FILES )) {
					
					if (is_array ( $_FILES ) && count ( $_FILES ) > 0) {
						$i = 2;
						foreach ( $_FILES as $key => $value ) {
							
							if ($value ["error"] == UPLOAD_ERR_OK && strlen ( $value ["tmp_name"] ) > 0) {
								$tmp_name = $value ["tmp_name"];
								$name = $value ["name"];
								$ext = strrchr ( $name, '.' );
								$codigo_max_anexo = $objAnexo->obtenerMaximoNumeroAnexo ( $respuestaRadicacion ["NoRadicado"] );
								$codigo_anexo = trim ( $respuestaRadicacion ["NoRadicado"] ) . trim ( str_pad ( $codigo_max_anexo, 5, "0", STR_PAD_LEFT ) );
								$anexoNombreArchivo = trim ( $respuestaRadicacion ["NoRadicado"] ) . '_' . trim ( str_pad ( $codigo_max_anexo, 5, "0", STR_PAD_LEFT ) ) . $ext;
								if (! @move_uploaded_file ( $tmp_name, BODEGAPATH . "$anoPath/$depPath/docs/$anexoNombreArchivo" )) {
									$band = false;
									$msg [] = "El Archivo " . $value ['name'] . " no pudo ser alamacenado!\n";
								} else {
									// Paso 3.1 Crear anexos de archivos anjuntados.
									$datosTipoAnexo = $objAnexo->obtenerTipoAnexo ( str_replace ( ".", "", $ext ) );
									$datos ["ANEX_TIPO"] = $datosTipoAnexo ["ANEX_TIPO_CODI"];
									$datos ["ANEX_NOMB_ARCHIVO"] = $anexoNombreArchivo;
									$datos ["ANEX_RADI_NUME"] = $respuestaRadicacion ["NoRadicado"];
									$datos ["ANEX_CODIGO"] = $codigo_anexo;
									$datos ["ANEX_ESTADO"] = 1;
									$datos ["ANEX_TAMANO"] = $value ["size"];
									$datos ["ANEX_SOLO_LECT"] = "N";
									$datos ["ANEX_CREADOR"] = $_SESSION ['krd'];
									$datos ["ANEX_DESC"] = "Adjunto:" . $value ['name'];
									$datos ["ANEX_NUMERO"] = $codigo_max_anexo;
									$datos ["ANEX_BORRADO"] = "N";
									$datos ["ANEX_DEPE_CREADOR"] = $_SESSION ['dependencia'];
									$datos ["ANEX_FECH_ANEX"] = $this->db->conn->OffsetDate ( 0, $this->db->conn->sysTimeStamp );
									$datos ["USUA_DOC"] = $_SESSION ["usua_doc"];
									$estado_anexado = $objAnexo->insertarAnexo ( $datos );
									if (! $estado_anexado) {
										$band = false;
										$msg [] = "El Archivo " . $value ['name'] . " no pudo ser anexado al Radicado Generado No." . $respuestaRadicacion ["NoRadicado"] . " !\n";
									} else {
										if (! $objHist->insertarHistoricoImagenAnexo ( $respuestaRadicacion ["NoRadicado"], $codigo_anexo, $anexoNombreArchivo, $_SESSION ['usua_doc'], $_SESSION ['krd'], 20 )) { // Se adiciona historico de imagen de anexo
											$msg [] = "Hubo un error al insertar el historico de la imagen del Anexo en la BD.";
											$band = false;
										}
										$pathsAttachment [$i] ['original'] = RUTARAIZ . "bodega/$anoPath/$depPath/docs/$anexoNombreArchivo";
										$pathsAttachment [$i] ['nameAttachment'] = iconv ( $objCodificacionEspecial->codificacion ( $value ['name'] ), "UTF-8", htmlentities ( $value ['name'] ) );
										$i ++;
										$msg [] = "El Archivo " . iconv ( $objCodificacionEspecial->codificacion ( $value ['name'] ), "UTF-8", htmlentities ( $value ['name'] ) ) . " ha sido anexado!\n";
									}
								}
							}
						}
					}
				}
				
				// Paso 4. Envia Email con adjuntos. se utilizará la variable de retorno con los $pathsAttachment para adjuntar los archivos. Este paso se realizará de forma asincrona desde JavaScript
			}
			echo $objCodificacionEspecial->jsonRemoveUnicodeSequences ( json_encode ( Array (
					"success" => $band,
					"msg" => $msg,
					"NoRadicado" => $respuestaRadicacion ["NoRadicado"] ? $respuestaRadicacion ["NoRadicado"] : "",
					"pathsAttachment" => $pathsAttachment 
			) ) );
		} catch ( Exception $ex ) {
			echo $objCodificacionEspecial->jsonRemoveUnicodeSequences ( json_encode ( Array (
					"success" => false,
					"msg" => $ex->getMessage () 
			) ) );
		}
	}
	private function actualizaDatos($radi_nume_radi, $datos) {
		if (is_array ( $datos ) && count ( $datos )) {
			// $this->db->conn->debug=true;
			$rs = $this->db->update ( "RADICADO", $datos, "RADI_NUME_RADI=" . $radi_nume_radi );
			if ($rs) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}