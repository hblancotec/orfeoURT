<?php
session_start();
	
	$ruta_raiz = "../..";

	//Paremetros get y pos enviados desde la aplicacion origen
	extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);
	
	$folios		= $_POST['folios'];	//IBISCOM 2018-10-25
	$palabrasClave	= $_POST['palabrasClave'];	//IBISCOM 2018-10-25
	/**
	 * Este archivo procesa el formulario eviado desde
	 * adjuntarArchivos.php
	 * Guarda los documentos en la bodega y registra
	 * el archivo en la base de datos
	 *
	 * $vars = get_defined_vars(); print_r($vars["_FILES"]);
	 */

	//header("Content-Type: application/json");
	include_once	("$ruta_raiz/include/db/ConnectionHandler.php");
	include_once	("$ruta_raiz/include/tx/Historico.php");
	require_once	('../../FirePHPCore/fb.php');

	$db 		= new ConnectionHandler("$ruta_raiz");
	$Historico 	= new Historico($db);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    //Traer el valor maximo para cargar archivos de php
    $val  = ini_get('upload_max_filesize');
    $val  = trim($val);
    $last = strtolower($val[strlen($val)-1]);

    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

	// Constantes y variables
	//$descrip		= utf8_decode(preg_replace("[\r\n]"," ",trim($descrip)));	
	$fechaGrab		= trim($date1);
	$numramdon		= rand (0,100000);
	$contador		= 0;
	$tamanoMax		= $val; //9.9MB max tamao de un archivo
	$fechaNomb 		= Date("ymd_hi_");
	$anofecha		= date('Y');
	$mensaje1		= " No incluya texto con caracteres extraños";
	$mensaje2		= " Error al crear la carpeta.";
	$mensaje3		= " El formato no existe: ";
	$mensaje4		= " Este archivo no se envio (Extension invalida)";
	$mensaje5		= " El tamano del archivo no es valido: ";
	$mensaje8		= " Se grabo con exito ";
	$mensaje9		= " No se grabo el archivo";
	$mensaje11		= " No se grabo el registro en el sistema";
	$mensaje12		= " No se adjunto un archivo";
	$mensaje13		= " El tama&ntilde;o del archivo supera las 15Mb.";
	$mensaje14		= " Archivo adjunto: ";
	$mensaje15		= " El formato del asunto tiene caracteres extra&ntilde;os";

	$error0			= 'No hay ningun error, el archivo cargado con éxito';
	$error1			= 'El archivo excede el las 15Mb';
	$error2			= 'El archivo excede el los 15Mb';
	$error3			= 'El archivo no se subio completo';
	$error4			= 'En blanco';
	$error6			= 'Falta una carpeta temporal';
	$error7			= 'No se pudo escribir el archivo en el disco';
	$error8			= 'El Archivo a carga se detuvo, por extension';

	list($diaa,$mess,$anno)	=	explode('/',$fechaGrab);
	//$fechaGrab				=	$mess."/".$diaa."/".$anno;
	$fechaGrab				=	$diaa."/".$mess."/".$anno;
	$mensajeGraba	= array(); //grabar mensajes para se impresos

	//Funciones imprimir mensaje
	function imprimirRespuesta ($mensaje){
		if(is_array($mensaje)){
			print_r(json_encode($mensaje));
		}else{
			$accion	= array( 'respuesta' => "alert",
						     'mensaje' => "$mensaje");
			print_r(json_encode($accion));
		};
	};
    
	#IBISCOM 2018-10-24 INICIO
	//Función que inserta un registro en la tabla de metadatos para un anexo a un expediente
	function insertarMetadatosAnexoExp(	    $db	    ,$codigo	    ,$hash	    ,$folios	    ,$nombre_proyector	    ,$nombre_revisor
	    ,$nombre_firma	    ,$palabras_clave,$fechaProduccion)	{
	        $funcionHash = 'sha1';
	        $id_tipo=1;//aplica si es para un anexo al radicado(0) o un anexo al expediente(1)
	        $insertSQL = "INSERT INTO METADATOS_DOCUMENTO ".//(id_anexo,id_tipo_anexo,hash,funcion_hash,folios,nombre_proyector,nombre_revisor,nombre_firma,palabras_clave)
	   	        "VALUES ('$codigo',$id_tipo,'$hash','$funcionHash','$folios','$nombre_proyector','$nombre_revisor','$nombre_firma','$palabras_clave',NULL,'$fechaProduccion',0)";
	        
	        $stateTX = $db->conn->Execute( $insertSQL);
	        if(empty($stateTX)){
	            return false;
	        }else{
	            return true;
	        };
	};
	
	//Funcion que retorna el identificador de un anexo a un expediente
	function getIdAnexoExp(	  $db	    ,$numExpe	    ,$tipoExt	    ,$usualogin
	    ,$fechaGrab	    ,$descrip	    ,$Grabar_path	    ,$var2Value	    ,$depeCodi	    ,$nombre){
	        
	        $querySelect = "SELECT  ANEXOS_EXP_ID
                    FROM SGD_ANEXOS_EXP AE
                    WHERE AE.SGD_EXP_NUMERO ='$numExpe'
                        AND AE.ANEX_TIPO_CODI = '$tipoExt'         AND AE.USUA_LOGIN_CREA = '$usualogin'
                        AND AE.ANEXOS_EXP_FECH_CREA = '$fechaGrab' AND AE.ANEXOS_EXP_DESC = '$descrip'
                        AND AE.ANEXOS_EXP_PATH = '$Grabar_path'    AND AE.SGD_TPR_CODIGO = '$var2Value'
                        AND AE.DEPE_CODI = '$depeCodi'             AND AE.ANEXOS_EXP_NOMBRE = '$nombre'";
	        
	        $ANEX_EXP_ID= $db->conn->Execute($querySelect)->fields['ANEXOS_EXP_ID'];
	        if(empty($ANEX_EXP_ID)){
	            return false;
	        }else{
	            return $ANEX_EXP_ID;
	        };
	};
	#IBISCOM 2018-10-27 FIN
	
	//Funcion grabar en la base de datos
	function grabarRegistro(
						$db
						,$numExpe
						,$tipoExt
						,$usualogin
						,$fechaGrab
						,$descrip
						,$Grabar_path
						,$var2Value
						,$depeCodi
						,$nombre)
	{
		$queryGrabar	= "INSERT INTO SGD_ANEXOS_EXP(
											SGD_EXP_NUMERO,
                                            ANEX_TIPO_CODI,
                                            USUA_LOGIN_CREA,
                                            ANEXOS_EXP_FECH_CREA,
                                            ANEXOS_EXP_DESC,
                                            ANEXOS_EXP_PATH,
                                            SGD_TPR_CODIGO,
                                            DEPE_CODI,
                                            ANEXOS_EXP_NOMBRE
                                            )";

    	$queryGrabar 	.= " VALUES(
    						'$numExpe',
    						$tipoExt,
    						'$usualogin',
    						'$fechaGrab',
    						'$descrip',
    						'$Grabar_path',
    						$var2Value,
    						$depeCodi,
    						'$nombre')";

    	$ejecutarQuerey	= $db->conn->Execute($queryGrabar);
    	if(empty($ejecutarQuerey)){
    		return false;
    	}else{
    		return true;
    	};
	};

	 $sql4 		= "select
						usua_codi
					from
						usuario
					where
						usua_login = '$usualogin'
						and  depe_codi = '$depeCodi'";

	// codigo del usuario necesario para crear historico
	$exte4 		= $db->conn->Execute($sql4);

	$usua_codi	= $exte4->fields["usua_codi"];

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

	//Ruta en la que se guardara el archivo

	$ruta		= trim($anofecha) . "\\" . trim($depeCodi) . "\\" . trim($numExpe);
	$adjuntos 	= trim(str_replace(" ","", BODEGAPATH . $ruta));
	$adjuntos 	= str_replace("/","\\",$adjuntos);

	//Si no existe la carpeta se crea.
	if(!is_dir($adjuntos)){
		$rs	= mkdir($adjuntos, 0700);
		if(empty($rs)){
		    imprimirRespuesta($mensaje2);
			return;
		}
	}
	
	if(empty($var2Value)){
	    imprimirRespuesta($mensaje13);
		return;
	}

	foreach ($_FILES["archs"]["name"] as $value) {
		if($value){
			$bandera1 = true;
			break;
		};
	};

	if(!$bandera1){
		imprimirRespuesta($mensaje12);
		return;
	};
			
	//Validaciones y envio para grabar archivos
	foreach($_FILES["archs"]["name"] as $key => $name){

		$nombre 	= strtolower(trim($_FILES["archs"]["name"][$key]));
		$tipo		= trim($_FILES["archs"]["type"][$key]);
		$tamano		= trim($_FILES["archs"]["size"][$key]);
		$tmporal	= trim($_FILES["archs"]["tmp_name"][$key]);
		$error		= trim($_FILES["archs"]["error"][$key]);
		$ext 		= strrchr($nombre,'.');
		$nomFinal	= $fechaNomb.$numramdon.$contador.$ext;
		$destino 	= $adjuntos."\\".$nomFinal;
		$strinTama	= 30;

		if(strlen($nombre) > $strinTama){
			$nombMens	= substr($nombre, - ($strinTama - 6)).".......";
		}elseif (strlen($nombre) <= $strinTama) {
			$nombMens 	= str_pad($nombre,$strinTama , ".." );
		};
		if (is_array($exts[$ext])){
			foreach ($exts[$ext]['mime'] as $value){
			    //if(preg_match( '/pdf/i', $value )){
			    if($tipo == $value ){
					$bandera = true;
					if($tamano < $tamanoMax){
						//Guardar el archivo en la carpteta ya creada
						if (move_uploaded_file($tmporal, $destino)) {
							//grabar el registro en la base de datos

							$tipoExt		= $exts[$ext]['codigo'];
							$Grabar_path	= $ruta."\\".$nomFinal;
							if(strlen($nombre) > 99)
								$nombre		= substr($nombre, - 99);

							$resultado		= grabarRegistro(
														$db
														,$numExpe
														,$tipoExt
														,$usualogin
														,$fechaGrab
														,$descrip
														,$Grabar_path
														,$var2Value
														,$depeCodi
														,$nombre);
							if($resultado){
							    #IBISCOM 2018-10-24
							    ## Ibiscom 2019-05-23 $fechaEntrada se cambia como parametro a $fechaGrab por motivo de ordenaniento  de los documentos por fecha en la parte de expedientes
							    // Ibiscom 2019-06-19 se agrega a como estaba en la fecha 2018-11-14 por que el metadato que va aca debe ser automatico y la ordenacion por la fecha se debe validar en DatosExpedientes.php
							    $codigo = getIdAnexoExp($db, $numExpe, $tipoExt, $usualogin, $fechaGrab , $descrip, $Grabar_path, $var2Value, $depeCodi, $nombre);// ******* Hacer un SELECT CON LOS CAMPOS DEL INSERT COMO CONDICION
							    $funcionHash = 'sha1';
							    $hash = hash_file($funcionHash, $destino);
							    $nombre_proyector=$nombreProyector;
							    $nombre_revisor=$nombreRevisor;
							    $nombre_firma="";
							    $resultMetadat	= insertarMetadatosAnexoExp( $db,$codigo,$hash	,$folios ,$nombre_proyector	,$nombre_revisor  ,$nombre_firma ,$palabrasClave,$fechaGrab);
							    
							    #IBISCOM 2018-10-27
							    
								$observa 	= $mensaje14.$nombre;
								$tipoTx 	= 65;
								$radicados[]= '0';
								
								//Insertamos LOG de imagen del anexo al expediente
								$sqlAE = "INSERT INTO SGD_HIST_IMG_ANEX_EXP 
										   (ANEXOS_EXP_ID, RUTA, USUA_DOC, USUA_LOGIN, ID_TTR_HIAN)
										VALUES ( ".$db->conn->Insert_ID().",'".str_replace('\\','/',$Grabar_path)."','$usua_doc','$usualogin', $tipoTx)";
								$okAE = $db->conn->Execute($sqlAE);
								
								$salida 	= $Historico->insertarHistoricoExp(
																	  $numExpe
																	, $radicados
																	, $depeCodi
																	, $usua_codi
																	, $observa
																	, $tipoTx
																	, 0);
                                                                  $st="exec SGD_SEXP_SECEXPEDIENTES_ACTUALIZA_FechasExtremas @NoExpediente='$numExpe'";
                                                                    //$db->conn->debug=true;
                                                                  $rs=$db->conn->Execute($st);
								
								$mensajeGraba[] = array('respuesta' => true,
													'mensaje' => $nombMens.$mensaje8);
									} else{
								$mensajeGraba[] = array('respuesta' => false
												,'mensaje' => $nombMens.$mensaje11);
							};

						} else {
							$mensajeGraba[] = array('respuesta' => false
											,'mensaje' => $nombMens.$mensaje9);
						}
					}else{
						$mensajeGraba[] = array('respuesta' => false
										,'mensaje' => $nombMens.$mensaje5.$tamano);
					}
				}
			};
			if(empty($bandera) && empty($error)){
				$mensajeGraba[] = array('respuesta' => false
								,'mensaje' => $nombMens.$mensaje3.$tipo);
			};

		}else{
			if(!empty($nombre) && empty($error))
			$mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$mensaje4);
		}

		if ($error) {
			switch ($error) {
		        case 0:
		        	$mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error0);
					break;
		        case 1:
		            $mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error1);
					break;
		        case 2:
		            $mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error2);
					break;
		        case 3:
		            $mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error3);
					break;
		        case 4:
		            //$mensajeGraba[] = array('respuesta' => false
					//		,'mensaje' => $nombMens.$error4);
					break;
		        case 6:
		            $mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error6);
					break;
		        case 7:
		            $mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error7);
					break;
		        case 8:
		            $mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error8);
					break;
		        default:
		            return 'Unknown upload error';
		    }
		}

		$contador ++;
	};

	//el resultado del proceso se envia para ser mostrado
	imprimirRespuesta ($mensajeGraba);

?>
