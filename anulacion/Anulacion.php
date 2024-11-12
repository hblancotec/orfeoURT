<?php
class Anulacion {
	/**
	 * Clase que maneja los documentos anulados
	 *
	 * @param
	 *        	int Dependencia Dependencia de Territorial que Anula
	 *        	@db Objeto conexion
	 * @access public
	 */
	var $db;
	function __construct($db) {
		/**
		 * Constructor de la clase anulaci�n
		 * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
		 */
		$this->db = $db;
	}
	function consultaAnulados($dependencia) {
	} // end of member function consultaAnulados
	
	/**
	 * @db Cursor de la base de datos que etamos trabajando.
	 *
	 * @param
	 *        	int dependencia Dependencia que pide la anulacion del documento.
	 * @param
	 *        	int usuadoc Documento de identificación del usuario que pide la anulación
	 * @return void
	 * @access public
	 */
	function solAnulacion($radicados, $dependencia, $usuadoc, $comentario, $codUsuario, $systemDate) {
		// Arreglo que almacena los nombres de columna
		foreach( $radicados as $noRadicado ) {
			$sql = "SELECT SGD_EANU_CODIGO
					FROM radicado 
					WHERE radi_nume_radi = " . $noRadicado;
			// Selecciona el registro a actualizar
			$rs = $this->db->conn->Execute( $sql ); // Executa la busqueda y obtiene el registro a actualizar.
			$record = array(); // Inicializa el arreglo que contiene los datos a modificar
			                    // Asignar el valor de los campos en el registro
			                    // Observa que el nombre de los campos pueden ser mayusculas o minusculas
			$record['SGD_EANU_CODIGO'] = "1";
			
			// Mandar como parametro el recordset y el arreglo conteniendo los datos a actualizar
			// a la funcion GetUpdateSQL. Esta procesara los datos y regresara el enunciado sql del
			// update necesario con clausula WHERE correcta.
			// Si no se modificaron los datos no regresa nada.
			$updateSQL = $this->db->conn->GetUpdateSQL( $rs, $record, true );
			$this->db->conn->Execute( $updateSQL ); // Actualiza el registro en la base de datos
			                                         // Si no se modificaron los datos no regresa nada.
			$updateSQL = $this->db->conn->GetUpdateSQL( $rs, $record, true );
			$this->db->conn->Execute( $updateSQL ); // Actualiza el registro en la base de datos
			                                         // Insertamos en la tabla sgd_anu_anulados los registros que entran en solicitud.
			$sql = "insert into sgd_anu_anulados(RADI_NUME_RADI,
                                                        SGD_EANU_CODI,
                                                        SGD_ANU_SOL_FECH,
                                                        DEPE_CODI,
                                                        USUA_DOC,
                                                        SGD_ANU_DESC,
                                                        USUA_CODI) 
				                    VALUES($noRadicado,
                                            1,
                                            $systemDate,
                                            $dependencia,
                                            $usuadoc,
                                            '$comentario',
                                            $codUsuario)";
			if($this->db->conn->Execute( $sql ) == false) {
				print 'error al insertar: ' . $this->db->conn->ErrorMsg() . '<BR>';
			}
			// Fin insercion en tabla de Anulados
		}
		return($radicados);
	} // end of member function solicitudAnulacion
	
	/**
	 *
	 * @param integer $radicados
	 * @param integer $dependencia
	 * @param string $usuadoc
	 * @param string $comentario
	 * @param integer $codUsuario        	
	 */
	function apruebaAnulacion($radicados, $dependencia, $usuadoc, $comentario, $codUsuario, $Historico) {
		// SE OBTIENE LA CANTIDAD DE RADICADOS A ANULAR
		$cant = count( $radicados );
		$i = 0;
		$rad = array();
		$flag1 = 0;
		$flag2 = 0;
		$systemDate = $this->db->conn->sysTimeStamp;
		$expedientes = array();
		// Hacemos un ciclo para gestionar cada anulacion de radicado
		while( $i < $cant ) {
			$rad[0] = $radicados[$i];
			// Buscamos los datos del usuario y dependencia del radicador por cada radicado a anular.
			$sql = "select radi_depe_radi, radi_usua_radi from radicado where radi_nume_radi = " . $rad[0];
			$rs = $this->db->conn->Execute( $sql );
			$urad = $rs->fields['radi_usua_radi'];
			$drad = $rs->fields['radi_depe_radi'];
			$sql = "select usua_doc from usuario where depe_codi = $drad and usua_codi = $urad";
			$irad = $this->db->conn->GetOne( $sql );
			
			// ## SE ASIGNA TRD DE ANULADO A LOS RADICADOS ###
			
			// CONSULTA SI EXISTE LA TRD PARA LA DEPENDENCIA
			$mrd = "	SELECT	SGD_MRD_CODIGO
					FROM	SGD_MRD_MATRIRD
					WHERE	DEPE_CODI = " . $drad . " AND
							SGD_SRD_CODIGO = 999 AND
							SGD_SBRD_CODIGO = 998 AND
							SGD_TPR_CODIGO = 1083";
			$rsMrd = $this->db->conn->GetOne( $mrd );

			if($rsMrd) {
				// CONSULTA SI EL RADICADO YA TIENE TRD
				$trdDep = "	SELECT	RADI_NUME_RADI FROM SGD_RDF_RETDOCF WHERE RADI_NUME_RADI = " . $rad[0];
				$rsTrdDep = $this->db->conn->GetOne( $trdDep );
				if($rsTrdDep) {
					// ## SE ACTUALIZA TRD
					$Trd = "UPDATE	SGD_RDF_RETDOCF
							SET		SGD_MRD_CODIGO = " . $rsMrd . ",
									DEPE_CODI = " . $drad . ",
									USUA_CODI = " . $urad . ",
									USUA_DOC = '" . $irad . "',
									SGD_RDF_FECH = " . $systemDate . "
							WHERE	RADI_NUME_RADI = " . $rad[0];
					
					$codTx = 34;
				} else {
					// ## SE INSERTA TRD
					$codTx = 32;
					$Trd = "INSERT INTO	SGD_RDF_RETDOCF(	SGD_MRD_CODIGO,
															DEPE_CODI,
															USUA_CODI,
															USUA_DOC,
															SGD_RDF_FECH,
															RADI_NUME_RADI )
											VALUES	(	" . $rsMrd . ",
														" . $drad . ",
														" . $urad . ",
														'" . $irad . "',
														" . $systemDate . ",
														" . $rad[0] . " )";
				}
				$rsTrd = $this->db->conn->Execute( $Trd );
				if($rsTrd == true) {
					$flag1 = 1;
					$msg1 = "Se actualizo la TRD, correctamente";
					
					// ## SE REGISTRA EN EL HISOTRICO DEL RADICADO
					$radHist = $Historico->insertarHistorico( $rad, $dependencia, $codUsuario, $dependencia, $codUsuario, "Se actualiza la TRD automaticamente, por anulaci󮠤el radicado", $codTx );
					
					// ## SE REGISTRA EN EL HISTORICO DE TRD
					$trdHist = "INSERT INTO SGD_HMTD_HISMATDOC(	SGD_HMTD_FECHA,
																	RADI_NUME_RADI,
																	USUA_CODI,
																	SGD_HMTD_OBSE,
																	USUA_DOC,
																	DEPE_CODI,
																	SGD_TTR_CODIGO,
																	SGD_MRD_CODIGO )
													VALUES	(	" . $systemDate . ",
																" . $rad[0] . ",
																" . $codUsuario . ",
																'Por anulaci󮠤el radicado, se actualiza la TRD automaticamente',
																" . $usuadoc . ",
																" . $dependencia . ",
																" . $codTx . ",
																" . $rsMrd . " )";
					$rsTrdHist = $this->db->conn->Execute( $trdHist );
				} else {
					$msg1 = "No se pudo actualizar la TRD";
				}
				// #####################################################################################
				// ## SE INCLUYE EN EXPEDIENTE ANULADOS
				
				$tipoRad = substr( $rad[0], - 1 );
				$ano = substr( $rad[0], 0, 4 );
				
				// ## SI EL RADICADO ES DE SALIDA(1) SE DEBE INCLUIR EN EL EXPEDIENTE DE LA DEPENDENCIA 663
				$depexp =($tipoRad == 1) ? 3003 : $drad;
				
				$exp = "SELECT	SGD_EXP_NUMERO,
				SGD_SEXP_SECUENCIA
				FROM	SGD_SEXP_SECEXPEDIENTES
				WHERE	DEPE_CODI = $depexp AND
				SGD_SRD_CODIGO = 999 AND
				SGD_SBRD_CODIGO = 998 AND
				SGD_SEXP_ANO = $ano AND
				SGD_SEXP_NOMBRE = 'ANULADOS' AND
				SGD_SEXP_ESTADO = 'False'";
				$rsExp = $this->db->conn->Execute( $exp );
				
				$existeExp = $rsExp->fields['SGD_EXP_NUMERO'];
				$secExp = $rsExp->fields['SGD_SEXP_SECUENCIA'];
				
				// ## SI EL EXPEDIENTE DE ANULADOS NO ESTA CREADO, SE PROCEDE A CREARLO
				if(! $existeExp) {
					$expAct = "SELECT	sgd_sexp_ano, depe_codi, sgd_srd_codigo, sgd_sbrd_codigo, max(SGD_SEXP_SECUENCIA) as SGD_SEXP_SECUENCIA
					FROM	SGD_SEXP_SECEXPEDIENTES
					WHERE	DEPE_CODI = $depexp AND
					SGD_SRD_CODIGO = 999 AND
					SGD_SBRD_CODIGO = 998 AND
					SGD_SEXP_ANO = $ano
					GROUP BY sgd_sexp_ano, depe_codi, sgd_srd_codigo, sgd_sbrd_codigo";
					$rsExpAct = $this->db->conn->Execute( $expAct );
					
					$numExpAct = $rsExpAct->fields['SGD_EXP_NUMERO'];
					$secExp = $rsExpAct->fields['SGD_SEXP_SECUENCIA'];
					
					$secExp ++;
					$sec = str_pad( $secExp, 5, "0", STR_PAD_LEFT );
					$numExp = $ano . $depexp . "999998" . $sec . "E";
					
					$newExp = "	INSERT	INTO SGD_SEXP_SECEXPEDIENTES(	SGD_EXP_NUMERO,
					SGD_SRD_CODIGO,
					SGD_SBRD_CODIGO,
					SGD_SEXP_SECUENCIA,
					DEPE_CODI,
					USUA_DOC,
					SGD_SEXP_FECH,
					SGD_SEXP_ANO,
					USUA_DOC_RESPONSABLE,
					SGD_PEXP_CODIGO,
					SGD_SEXP_PAREXP1,
					SGD_SEXP_NOMBRE,
					SGD_SEXP_ESTADO)
					VALUES	(	'$numExp',
					999,
					998,
					$secExp,
					$depexp,
					'19199000',
					" . $systemDate . ",
					$ano,
					'19199000',
					0,
					'ANULADOS',
					'ANULADOS',
					0)";
					$rsNewExp = $this->db->conn->Execute( $newExp );
					
					// ## SI EL EXPEDIENTE SE CREO CORRECTAMENTE, SE ASIGNA EL NUMERO DEL EXPEDIENTE CREADO A LA VARIABLE $rsExp
					if($rsNewExp) {
						$existeExp = $numExp;
					} else {
						$retorno['error'][] = 'No se pudo crear el expediente para incluir el radicado';
					}
				}
				if($existeExp) {
					$incExp = "INSERT INTO SGD_EXP_EXPEDIENTE(	SGD_EXP_NUMERO,
																RADI_NUME_RADI,
																SGD_EXP_FECH,
																DEPE_CODI,
																USUA_CODI,
																USUA_DOC,
																SGD_EXP_ESTADO
																)
												VALUES(	'" . $existeExp . "',
															" . $rad[0] . ",
															" . $systemDate . ",
															" . $dependencia . ",
															" . $codUsuario . ",
															" . $usuadoc . ",
															0)";
					$rsIncExp = $this->db->conn->Execute( $incExp );
					
					if($rsIncExp) {
						$flag2 = 1;
						$msg2 = "Se incluyo en el expediente";
						
						// ## SE ALMACENAN LOS DIFERENTES # DE EXPEDIENTES PARA LUEGO PINTARLOS EN EL FORMULARIO FINAL
						if(in_array( $existeExp, $expedientes )) {
							// SI EL RADICADO YA ESTA EN EL ARREGLO, NO SE HACE NADA
						} else {
							$expedientes[$rad[0]] = $existeExp;
							$k ++;
						}
						
						// ## SE REGISTRA EN EL HISTORICO DEL EXPEDIENTE LA INCLUCIӎ DEL RADICADO
						$incHisExp = "INSERT INTO SGD_HFLD_HISTFLUJODOC(	SGD_FEXP_CODIGO,
																			SGD_HFLD_FECH,
																			SGD_EXP_NUMERO,
																			RADI_NUME_RADI,
																			USUA_DOC,
																			USUA_CODI,
																			DEPE_CODI,
																			SGD_TTR_CODIGO,
																			SGD_HFLD_OBSERVA
																)
												VALUES(	0,
															" . $systemDate . ",
															'" . $existeExp . "',
															" . $rad[0] . ",
															" . $usuadoc . ",
															" . $codUsuario . ",
															" . $dependencia . ",
															53,
															'Se incluye automaticamente el radicado en el Expediente de Anulados')";
						$rsIncHistExp = $this->db->conn->Execute( $incHisExp );
					}
				} else {
					$retorno['error'][] = "No se pudo crear el expediente de ANULADOS para dependencia $dependencia";
				}
				
				// ## SE ARCHIVAN LOS RADICADOS
				// $flag1=1 indica que al radicado se le asigno TRD correctamente
				// $flag2=1 indica que al radicado se incluyo en expediente
				
				if($flag1 == 1 and $flag2 == 1) {
					
					// ## SE ACTUALIZA EL USUARIO Y DEPENDENCIA ACTUAL DEL RADICADO
					$arch = "UPDATE	RADICADO
							SET		RADI_DEPE_ACTU = 999,
									RADI_USUA_ACTU = 1,
									TDOC_CODI = 1083
							WHERE	RADI_NUME_RADI = " . $rad[0];
					$rsArch = $this->db->conn->Execute( $arch );
					
					// ## SE REGISTRA EN EL HISOTRICO DEL RADICADO EL ARCHIVO
					$radHist = $Historico->insertarHistorico( $rad, $dependencia, $codUsuario, 999, 1, "Se archiva automaticamente, por anulaci󮠤el radicado", 13 );
					
					if($radHist) {
						$msg4 = "Se archivo el radicado";
					}
				}
			} else {
				$retorno['error'][] = "La dependencia $drad no tiene asignada la TRD para anulacion correspondiente.";
			}
			$i++;
		}
		return $expedientes;
	}
	
	/**
	 * @db Cursor de la base de datos que etamos trabajando.
	 *
	 * @param	int dependencia Dependencia que pide la anulacion del documento.
	 * @param	int usuadoc Documento de identificación del usuario que pide la anulación
	 * @return void
	 * @access public
	 */
	function genAnulacion($radicados, $dependencia, $usuadoc, $comentario, $codUsuario, $actaNo, $pathActa, $where_depe, $where_TipoRadicado, $tipoRadicado) {
		// Arreglo que almacena los nombres de columna
		// Codigo de prueba para UPDATE
		$sql = "SELECT USUA_ANU_ACTA
                    FROM sgd_anu_anulados 
                    WHERE USUA_ANU_ACTA = " . $actaNo . $where_depe . $where_TipoRadicado;
		
		// Selecciona el registro a actualizar
		// Executa la busqueda y obtiene el registro a actualizar.
		$rs = $this->db->conn->Execute( $sql );
		
		if($rs->RowCount() >= 1)
			die( "<hr>
                    <center>
                        <b>
                            <font color='red'>EL ACTA No < $actaNo > YA EXISTE. <br>
                                VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO
                            </font>
                        </b>
                    </center>
                </hr>" );
		if($radicados) {
			foreach( $radicados as $noRadicado ) {
				$sql = "SELECT SGD_EANU_CODIGO
                                FROM radicado 
                                WHERE radi_nume_radi = " . $noRadicado;
				
				// Selecciona el registro a actualizar
				// Executa la busqueda y obtiene el registro a actualizar.
				$rs = $this->db->conn->Execute( $sql );
				
				// Inicializa el arreglo que contiene los datos a modificar
				$record = array();
				
				// Asignar el valor de los campos en el registro
				// Observa que el nombre de los campos pueden ser mayusculas o minusculas
				$record['SGD_EANU_CODIGO'] = "2";
				
				// Mandar como parametro el recordset y el arreglo conteniendo los datos a actualizar
				// a la funcion GetUpdateSQL. Esta procesara los datos y regresara el enunciado sql del
				// update necesario con clausula WHERE correcta.
				// Si no se modificaron los datos no regresa nada.
				$updateSQL = $this->db->conn->GetUpdateSQL( $rs, $record, true );
				
				// Actualiza el registro en la base de datos
				$this->db->conn->Execute( $updateSQL );
				
				// Si no se modificaron los datos no regresa nada.
				$updateSQL = $this->db->conn->GetUpdateSQL( $rs, $record, true );
				
				// Actualiza el registro en la base de datos
				$this->db->conn->Execute( $updateSQL );
				
				// Insertamos en la tabla sgd_anu_anulados los registros que entran en solicitud.
				$sql = "update sgd_anu_anulados
                                    set SGD_EANU_CODI=2,
                                        DEPE_CODI_ANU= $dependencia,
                                        USUA_DOC_ANU=$usuadoc,
                                        USUA_CODI_ANU=$codUsuario,
                                        USUA_ANU_ACTA=$actaNo,
                                        SGD_TRAD_CODIGO=$tipoRadicado,
                                        SGD_ANU_PATH_ACTA='$pathActa'
                                    where radi_nume_radi = $noRadicado";
				if($this->db->conn->Execute( $sql ) == false) {
					print 'error al insertar: ' . $this->db->conn->ErrorMsg() . '<BR>';
				}
				// Fin insercion en tabla de Anulados
			}
			return($radicados);
		}
	} // end of member function solicitudAnulacion
} // end of ANULACION
?>
