<?php
	define('ADODB_ASSOC_CASE', 0);
	$ruta_raiz = "../..";
	$pearLib = $ruta_raiz . "/pear/";
	$paraServicio = false;	// Variable para artivar la depuracion
	$radicarPrueba = false;	// Variable para controlar el usuario destino de la radicacion
	$usuariosDebug[] = 79981067;	// Mi cedula para hacer pruebas Cmauricio
	//$departamento = 11;
	//$municipio = 1;
	
	// Busqueda de algun usuario de prueba
	foreach ($usuariosDebug as $validar) {
		if ($_POST["nit"] == $validar) {
			$radicarPrueba = true;
			break;
		}
	}
	
	// Pruebas con solo mi usuario
	if (!$radicarPrueba){
		$depDestino 	= 529;		//Dependencia destino a donde va dirigido
		$usuaDestino 	= 1;		//usua_doc del usuario que va el radicado
		$usuaCodi 	= 831;		//Codigo del usuario destino
		if ($paraServicio){
			echo "<b>Lo sentimos!! este servicio no se encuentra disponible en este momento 
					Por favor intente en 2 horas agradecemos su comprensi&oacute;n GRACIAS!!!</b>";
			exit();
		}
	} else {
		// Si es un usuario de pruebas entonces enviar a ADMON1
		// Radicacion Usuario Admon1 pero se puede solicitar a otro usuario
		$depDestino   = 900;		// Dependencia destino a donde va dirigido
		$usuaDestino  = 900102030;	// Cedula del usuario que va el radicado
		$usuaCodi     = 1;		// Codigo del usuario destino
	}
	
	/*$depDestino 	= 905;		//dependencia destino a donde va dirigido
	$usuaDestino 	= 905001;	//Codigo del usuario que va el radicado
	$usuaCodi 	= 1;*/
	/*$depDestino 	= 529;		//Dependencia destino a donde va dirigido
	$usuaDestino 	= 1;		//usua_doc del usuario que va el radicado
	$usuaCodi 	= 831;*/	//Codigo del usuario destino
	$tipoAnexo 	= 3;
	$fechaRadicacion = date("d-m-Y");
	$numMaxRadDia 	= 2;		// Numero maximo de radicados que un usuario puede radicar diariamente
	$numTotalDia 	= 0;		// Tiene el numero de radicado que ha hecho en el dia
	$textoViaWeb 	= "por Internet";
	$arregloTipo["Q"] = "Queja $textoViaWeb" ;
	$arregloTipo["R"] = "Reclamo $textoViaWeb";
	$arregloTipo["S"] = "Sugerencia $textoViaWeb";
	$nombreArchivoPdf = "";
	
	// archivo que contiene la funciones de filtrado y conversion de fecha.
	include ("./funciones.php");
	$documento_us1 = "";		//Numero por verificar
	// Cedula del usuario que remite la queja o sugerencia
	$cc_documento_us1 = (!empty($_POST["nit"])) ? $_POST["nit"] : null;
	$tipo_emp_us1 	= 0;
	$depende22 	= "";
	
	// Capturando las variables que llegan por post
	$nombre_us1 	= (!empty($_POST["nombre"])) ? strtoupper($_POST["nombre"]) : null;
	$prim_apel_us1 	= (!empty($_POST["apellido"])) ? strtoupper($_POST["apellido"]) : null;
	$seg_apel_us1 	= (!empty($_POST["apellido2"])) ? strtoupper($_POST["apellido2"]) : ' ';
	$telefono_us1 	= (!empty($_POST["telefono"])) ? $_POST["telefono"] : null;
	$direccion_us1 	= (!empty($_POST["direccion"])) ? $_POST["direccion"] : null;
	$mail_us1 	= (!empty($_POST["email"])) ? $_POST["email"] : null;	// Correo electronico del remitente
	$descripcion 	= (!empty($_POST["asunto"])) ? $_POST["asunto"] : null;
	$dptoCodi	= (!empty($_POST["departamento"]["depto_codi"])) ? $_POST["departamento"]["depto_codi"] : null;
	$muniCodi 	= (!empty($_POST["municipio"]["codigo"])) ? $_POST["municipio"]["codigo"] : null;
	
	// manda todo a minusculas
	$descripcion = strtolower($descripcion);
	// Eliminando espacios inecesarios 

	$asunto = formatearTextArea($descripcion, 1500);
	$asunto = text2pdf($asunto);
	
	//Verifica los datos que lleguen por POST
	if (empty($cc_documento_us1) ||
		empty($nombre_us1) || 
		empty($prim_apel_us1) || 
		empty($seg_apel_us1) ||
		empty($telefono_us1) ||
		empty($direccion_us1) ||
		empty($asunto[0])) {
		echo "Algunos campos no se encuentran diligenciados";
		exit();
	}
	
	//Sentencia para contar el numero de radicaciones web para el usuario
	$sqlCont  = "SELECT COUNT(rad.RADI_FECH_RADI) TOTAL_RAD
				FROM SGD_DIR_DRECCIONES dir, 
					RADICADO rad
				WHERE dir.SGD_DIR_DOC = '" . $cc_documento_us1 . "' AND
					rad.RADI_NUME_RADI = dir.RADI_NUME_RADI AND
					rad.RA_ASUN LIKE '%SuperIntendencia%' AND
					TO_CHAR(rad.RADI_FECH_RADI,'DD/MM/YYYY') = '" . date('d/n/Y') . "'";
	
	$rs = $db->conn->Execute($sqlCont);
	if (!$rs->EOF) {
		$numTotalDia = $rs->fields["TOTAL_RAD"];
	}
	
	// Si sobre pasa el numero maximo no deja radicar mas y redirecciona a una pagina de error
	if ($numTotalDia >= $numMaxRadDia) {
		$tpl->loadTemplatefile("errorRadWeb.tpl");
		$tpl->setVariable("RUTA_RAIZ",$ruta_raiz . "/");
		$tpl->setVariable("TITULO_PAGINA",$tituloPagina);
		$tpl->setVariable("ESTILOS_RADICADO",$estilosRadicacion);
		$error = "Ha radicado un total de $numTotalDia que es el maximo permitido diario,
			 por favor si tiene mas quejas, reclamos o sugerencias, realice la radicacion el dia de  ma&ntilde;ana";
		$tpl->setVariable("DESCRIPCION_ERROR",$error);
		$tpl->show();
		exit();
	}
	
	// Colombia
	$idPais = 170;
	//$dptoCodi = 11;
	//$muniCodi = 1;
	// D.C.
	$codep_us1 = $idPais . "-" . $dptoCodi;		// Pais-departamento
	// Bogota
	//$muni_us1 = "170-11-1";			// Pais-departamento-municipio
	$muni_us1 	= $codep_us1 . "-" . $muniCodi;	// Pais-departamento-municipio
	$documento_us2 	= $documento_us1;	// Codigo remitente y predio van a ser el mismo
	$cc_documento_us2 = $cc_documento_us1;	// Cedula remitente y predio van a ser el mismo
	$tipo_emp_us2 	= $tipo_emp_us1;	// Tipo empresa va a ser iguales para los dos casos
	$nombre_us2 	= $nombre_us1;		// Nombre van a ser igual para ambos
	$prim_apel_us2 	= $prim_apel_us1;	// Apellido van a ser igual para ambos
	$seg_apel_us2 	= $seg_apel_us1;	// Apellido2 van a ser igual para ambos
	$telefono_us2 	= $telefono_us1;	// Telefono va hacer el mismo
	$direccion_us2 	= $direccion_us1;	// Direccion del predio y del remitente va a ser el mismo
	$mail_us2 	= $mail_us1;		// Correo electronico el mismo
	$otro_us2 	= $otro_us2;		// Lo mismo para ambos
	$idcont2 	= $idcont1;		// Lo mismo para ambos
	$idpais2 	= $idpais1;		// El mismo pais para remitente y predio
	$codep_us2 	= $codep_us1;		// El mismo departamento para remitente y predio
	$muni_us2 	= $muni_us1;		// El mismo municipio para remitente y predio
	$documento_us3 	= '';			// Supersuperservicios
	$cc_documento_us3 = '';			// Nit de superservicios
	$tipo_emp_us3 	= 0;			// Campo por determinar
	$nombre_us3 	= 'SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS';	// Nombre de la empresa
	$prim_apel_us3 	= 'SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS';	// De nuevo en nombre de la super
	$seg_apel_us3 	= '';			// Nombre de la persona contactada
	$telefono_us3 	= '';			// Numero de la persona contactada
	$direccion_us3 	= 'Cra 18 No 84 - 35';	// Direccion de la superservicios
	$mail_us3 	= '';			// Correo electronico de la superservicios
	$idcont3 	= 3;			// Campo por verificar 
	$idpais3 	= $idpais1;		// Direccion de la ciudad donde se esta haciendo el reclamo
	$codep_us3 	= $codep_us1;		// lo mismo para departamento
	$muni_us2 	= $muni_us1;		// Lo mismo para municipio
	$codiUsuaActual = 1;			// Codigo del usuario al cual va a llegar.
	$_SESSION['dependencia'] = $depDestino;
	$_SESSION['usua_doc'] 	= $usuaDestino;
	//$_SESSION['codusuario'] = 1;
	$_SESSION['codusuario'] = $usuaCodi;
	$_SESSION['nivelus'] 	= 5;
	global $krd;
	$krd = "USUARIOWEB";
	
	include_once($ruta_raiz . "/include/db/ConnectionHandler.php");
	require_once($pearLib 	. "HTML/Template/IT.php");
	require_once($ruta_raiz . "/include/tx/Radicacion.php");
	require_once($ruta_raiz . "/class_control/Municipio.php");
	require_once($ruta_raiz . "/include/tx/Historico.php");
	//include_once($ruta_raiz . "/radicacion/buscar_usuario.php");
	$tpl 	= new HTML_Template_IT($ruta_raiz . "/tpl");
	$unMunicipio = new Municipio($db);
	$db 	= new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	// Proceso de busqueda del ciudadano
	$sql = "SELECT SGD_CIU_CEDULA,SGD_CIU_CODIGO 
			FROM SGD_CIU_CIUDADANO 
			WHERE SGD_CIU_CEDULA = '$cc_documento_us1'";
	$sql2 	= $sql;
	$rs 	= $db->conn->Execute($sql);
	$flagOperacion = false;
	
	if (!$rs->EOF)	{ 
		$cedula = $rs->fields["SGD_CIU_CEDULA"];
		$documento_us1 = $rs->fields["SGD_CIU_CODIGO"];
		$updateEmail = (!empty($mail_us1)) ? "SGD_CIU_EMAIL = '" . $mail_us1 . "',": "";
		$updateTel = (!empty($telefono_us1)) ? "SGD_CIU_EMAIL = '" . $telefono_us1 . "',": "";
		$sql = "UPDATE SGD_CIU_CIUDADANO SET  SGD_CIU_NOMBRE = '" .$nombre_us1 . "',
		 					SGD_CIU_DIRECCION = '" . $direccion_us1 . "',
		 					SGD_CIU_APELL1 = '" . $prim_apel_us1 . "',
							SGD_CIU_APELL2 = '" . $seg_apel_us1 . "',
							$updateEmail
							$updateTel
							MUNI_CODI = '" . $muniCodi . "',
							DPTO_CODI = '" . $dptoCodi . "'
						 WHERE SGD_CIU_CEDULA = '" . $cedula . "'";
		$rs = $db->conn->Execute($sql);
		$flagOperacion = true;
	} else { // Si no esta lo inserta
		$sql = "SELECT SGD_CIU_CODIGO 
				FROM SGD_CIU_CIUDADANO 
				WHERE ROWNUM = 1 
				ORDER BY SGD_CIU_CODIGO DESC";
		$rs = $db->conn->Execute($sql);
		if(!$rs->EOF) {
			$ciuCodigo = $rs->fields["SGD_CIU_CODIGO"];
			$ciuCodigo++;
			$documento_us1 = $ciuCodigo;
		}
		// Si ejecuto consulta entonces insert el usuario
		if (!empty($ciuCodigo)) {
			$sql = "INSERT INTO SGD_CIU_CIUDADANO (SGD_CIU_CODIGO,
							SGD_CIU_CEDULA,
		 					SGD_CIU_NOMBRE,
		 					SGD_CIU_DIRECCION,
		 					SGD_CIU_APELL1, 
							SGD_CIU_APELL2,
							SGD_CIU_EMAIL,
							SGD_CIU_TELEFONO,
							MUNI_CODI,
							DPTO_CODI,
							ID_PAIS) 
					VALUES ('" . $ciuCodigo . "',
						'" . $cc_documento_us1 . "',
						'" . $nombre_us1 . "',
						'" . $direccion_us1 . "',
						'" . $prim_apel_us1 . "',
						'" . $seg_apel_us1 . "',
						'" . $mail_us1 . "',
						'" . $telefono_us1 . "',
						'" . $muniCodi . "',
						'" . $dptoCodi . "',
						'" . $idPais . "')";
			$db->conn->Execute($sql);
			$flagOperacion = true;
		}
	} // Fin del proceso de busqueda, actualizacion o inserccion de datos de ciudadano

	if (empty($documento_us1)){
		echo "Hubo algun problema en la generacion del codigo del usuario";
		exit();
	}
	
	$tipoRadicado 		= 2;
	$rad->radiUsuaActu 	= $usuaDestino;
	$rad->radiDepeActu 	= $depDestino;
	// Creando el radicado
	$rad = new Radicacion($db);
	$rad->radiTipoDeri 	= $tpRadicado;
	$rad->radiCuentai	= "null";
	$rad->eespCodi 		= "null";	//$documento_us3;
	$rad->mrecCodi 		= 3;		// Codigo para ver por que medio llego a la super 3 es internet
	$fecha_gen_doc_YMD 	= substr($fecha_gen_doc,6 ,4)."-".substr($fecha_gen_doc,3 ,2)."-".substr($fecha_gen_doc,0 ,2);
	$rad->radiFechOfic 	= date("d/m/Y");//$fecha_gen_doc_YMD;
	if(!$radicadopadre) 	$radicadopadre = null;
	$rad->radiNumeDeri 	= "null";	//trim($radicadopadre);
	$rad->radiPais 		= 170;		//$tmp_mun->get_pais_codi();
	$rad->descAnex 		= ""; //"Descripcion de Anexo";	//
	$rad->raAsun 		= $arregloTipo[$tipoQRS] . " a la SuperIntendencia"; // Asunto del radicado;
	$rad->radiDepeActu 	= $depDestino;	//$coddepe;
	$rad->radiDepeRadi 	= $depDestino;	//$coddepe;
	$rad->radiUsuaActu 	= $usuaCodi;	//$radi_usua_actu;
	$rad->trteCodi 		= 0;		//$tip_rem;
	$rad->tdocCodi 		= 0;		//$tdoc;	Suegerencia no tiene codigo Queja = 286 Reclamo = 399 
	$rad->tdidCodi 		= 0;		//$tip_doc;
	$rad->carpCodi 		= 0;		//$carp_codi;
	$rad->carPer 		= "null";	//$carp_per;
	$rad->trteCodi 		= 0;		//$tip_rem;
	$rad->ra_asun 		= $arregloTipo[$tipoQRS] . "radicado(a) via WEB";	
								// HLP Este si sirve? Para radicar se utiliza la variable $rad->raAsun )
	$rad->radiPath 		= 'null';			//
	$aplintegra 		= "0";				// Por defecto aplicaciones integradas Cero
	$rad->sgd_apli_codi 	= $aplintegra;
	$codTx 			= 2;
	$flag 			= 1;
	// Genera PDF
	
	$noRad 			= $rad->newRadicado($tipoRadicado,$depDestino);	//$rad->newRadicado($ent, $tpDepeRad[$ent]);
	if ($noRad=="-1") {
		die("<hr><b><font color=red><center>Error no genero un Numero de Secuencia o Inserto el radicado<br>SQL </center></font></b><hr>");
	}
	
	if(!empty($noRad) && $noRad!="-1") {
		$radPathPdf = "/" . substr($noRad, 0, 4) . 
				"/" . substr($noRad, 4, 3) .
				"/". $noRad . ".pdf";
		$sql = "UPDATE RADICADO SET /*ID_PAIS = '$idPais',*/
					MUNI_CODI = '$muniCodi', 
					DPTO_CODI ='$dptoCodi' 
				WHERE RADI_NUME_RADI = $noRad";
		$db->conn->Execute($sql);
	}
	$radicadosSel[0] = $noRad;
	$dependencia 	= $depDestino;
	$codusuario 	= $usuaCodi;
	$coddepe 	= $depDestino;
	$radi_usua_actu = $usuaCodi;
	$observacion 	= "Radicacion de QRS por WEB";
	$hist 		= new Historico($db);
	$hist->insertarHistorico($radicadosSel, 
					$dependencia,
					$codusuario,
					$coddepe,
					$radi_usua_actu,
					$observacion,
					$codTx);
	$conexion 	= $db;
	
	// Si actualizo o inserto el usuario en la tabla de sgd_ciu_ciudadano entonces realice la radicacion
	if ($flagOperacion) {
		//if(true) {
		//	include_once($ruta_raiz . "/radicacion/grb_direcciones.php");	
		//}
		// Si ya posee un radicado entonces inserta las direcciones
		if(!empty($noRad) && $noRad !="-1"){
			$nurad = $noRad;
			$barnumber = $noRad;
			include_once($ruta_raiz . "/radicacion/grb_direcciones.php");
			include_once($ruta_raiz . "/include/barcode/index.php");
		}
	}
	$direccionSuper = "Cra. 18 No. 84-35";
	$apellidos 	= $prim_apel_us1 . " " . $seg_apel_us1;
	$ciudadDefecto 	= "Bogota";
	$ciudadLargo 	= $ciudadDefecto . " D.C.";
	$estilo 	= "estilosQrs.css";
	$pathEstilos 	= "../css/";
	$telefono 	= "PBX. 6913005 Ext. 2238";
	$estilosRadicacion = $pathEstilos . $estilo;
	$nombreCorto 	= "SSPD";
	$tituloPagina 	= "Radicado via Web";
	$urlImagen 	= "../img/";
	$imgEscudo 	= "logoEscudo.gif";
	$rutaEscudo 	= $urlImagen . $imgEscudo;
	$mesPalabra 	= mesNo2Caracter(date("n"));
	$fechaRadicacionLarga = date("j") . " de " . $mesPalabra . " de " . date("Y"); 
	include("./crearRadicadoPdf.class.php");
	
	if ($generoPdf) {
		$sqlRad = $rad->updateRadicado($noRad,$radPathPdf);
		if (!copy($radicadoPdftmp,$ruta_raiz ."/bodega" . $radPathPdf)){
			echo "Error al copiar el archivo $radicadoPdftmp";
		} else {
			chmod($ruta_raiz ."/bodega" . $radPathPdf, 0755);
		}
	}
	
	$tpl->loadTemplatefile("mostrarRadicadoGenerado.tpl");
	$tpl->setVariable("RUTA_RAIZ",$ruta_raiz . "/");
	$tpl->setVariable("TITULO_PAGINA",$tituloPagina);
	$tpl->setVariable("ESTILOS_RADICADO",$estilosRadicacion);
	$tpl->setVariable("CIUDAD_LARGO",$ciudadLargo);
	$tpl->setVariable("FECHA_LARGA",$fechaRadicacionLarga);
	$tpl->setVariable("FECHA_CORTA",$fechaRadicacion);
	$tpl->setVariable("ESCUDO",$rutaEscudo);
	$tpl->setVariable("CODIGO_BARRAS",$file);
	$tpl->setVariable("ENTIDAD_CORTO",$nombreCorto);
	$tpl->setVariable("NUMERO_RADICADO",$noRad);
	$tpl->setVariable("DESCRIPCION",$descripcion);
	$tpl->setVariable("NOMBRE",$nombre_us1);
	$tpl->setVariable("SUPERSERVICIOS",$nombre_us3);
	$tpl->setVariable("TELEFONO_SUPER",$telefono);
	$tpl->setVariable("DIRECCION_SUPER",$direccionSuper);
	$tpl->setVariable("CIUDAD_CORTO",$ciudadDefecto);
	$tpl->setVariable("TIPO_ASUNTO",$arregloTipo[$tipoQRS]);
	$tpl->setVariable("APELLIDOS",$apellidos);
	$tpl->setVariable("DOC_ID",$cc_documento_us1);
	$tpl->setVariable("DIRECCION",$direccion_us1);
	$tpl->setVariable("TELEFONO",$telefono_us1);
	$tpl->setVariable("CORREOE",$mail_us1);
	$tpl->setVariable("ARCHIVO_PDF",$radicadoPdftmp);
	$tpl->show();
?>
