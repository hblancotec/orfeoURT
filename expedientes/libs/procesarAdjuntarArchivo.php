<?php

	$ruta_raiz = "../..";

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

	// Constantes y variables
	$descrip		= preg_replace("[\r\n]"," ",trim($_POST['descrip']));
	$tipDocum		= trim($_POST['var2Value']);//tipo documental asociado al expediente
	$numExpe		= trim($_POST['numExpe']);
	$depeCodi		= trim($_POST['depe']);
	$numrad			= trim($_POST['numrad']);
	$usualogin		= trim($_POST['usualogin']);
	$fechaGrab		= trim($_POST['date1']);

	$numramdon		= rand (0,100000);
	$contador		= 0;
	$tamanoMax		= 7500000; //7.5MB max tamaño de un archivo
	$fechaNomb 		= Date("ymd_hi_");
	$anofecha		= date('Y');
	//$val_expr		= '[^a-zA-Z0-9 :()&\n\sáéíóúÁÉÍÓÚ=#°,.ñÑ]';
	$mensaje1		= " No incluya texto con caracteres extraños";
	$mensaje2		= " Error al crear la carpeta.";
	$mensaje3		= " El formato no existe: ";
	$mensaje4		= " Este archivo no se envio (Extension invalida)";
	$mensaje5		= " El tamaño del archivo no es valido: ";
	$mensaje8		= " Se grabo con exito ";
	$mensaje9		= " No se grabo el archivo";
	$mensaje11		= " No se grabo el registro en el sistema";
	$mensaje12		= " No se adjunto un archivo";
	$mensaje13		= " No selecciono el tipo documental";
	$mensaje14		= " Archivo adjunto: ";
	$mensaje15		= " El formato del asunto tiene caracteres extra&ntilde;os";

	$error0			= 'No hay ningún error, el archivo cargado con éxito';
	$error1			= 'El archivo excede el los 8MB';
	$error2			= 'El archivo excede el los 8MB';
	$error3			= 'El archivo no se subio completo';
	$error4			= 'En blanco';
	$error6			= 'Falta una carpeta temporal';
	$error7			= 'No se pudo escribir el archivo en el disco';
	$error8			= 'El Archivo a carga se detuvo, por extensión';

	list($diaa,$mess,$anno)	=	explode('/',$fechaGrab);
	$fechaGrab				=	$mess."/".$diaa."/".$anno;
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

	//Funcion grabar en la base de datos
	function grabarRegistro(
						$db
						,$numExpe
						,$tipoExt
						,$usualogin
						,$fechaGrab
						,$descrip
						,$Grabar_path
						,$tipDocum
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
    						$tipDocum,
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
	
	if(empty($tipDocum)){
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
				if(eregi($tipo,$value) ){
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
														,$tipDocum
														,$depeCodi
														,$nombre);
							if($resultado){

								$observa 	= $mensaje14.$nombre;
								$tipoTx 	= 65;
								$radicados[]= '0';
								$salida 	= $Historico->insertarHistoricoExp(
																	  $numExpe
																	, $radicados
																	, $depeCodi
																	, $usua_codi
																	, $observa
																	, $tipoTx
																	, 0);


								$mensajeGraba[] = array('respuesta' => true
												,'mensaje' => $nombMens.$mensaje8);
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
		            $mensajeGraba[] = array('respuesta' => false
							,'mensaje' => $nombMens.$error4);
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