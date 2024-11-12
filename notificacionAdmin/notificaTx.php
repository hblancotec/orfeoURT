<?php
	session_start();
	$ruta_raiz = "..";
	include '../config.php';
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$sqlFechaHoy = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	
	/*if (!$_POST){
		die();
	}*/
	if (count($_SESSION) == 0) {
	    die(include "../sinacceso.php");
	    exit;
	}
	else if (isset($_SESSION['krd'])) {
	    $krd = $_SESSION["login"];
	}
	else {
	    $krd = $_REQUEST['krd'];
	}
	if (!$_SESSION['dependencia'] and !$_SESSION['depe_codi_territorial'])
	    include "../rec_session.php";
	###########################################################################	
	### SE CAPTURAN LAS VARIABLES QUE VIENEN POR $_POST
	
	$rad  = $_POST['rad'];
	$obs  = $_POST['txtObs'];
	
	### VALORES CARGADOS EN EL FORMULARIO
	$nom  = $_POST['txtNom'];
	$doc  = $_POST['txtDoc'];
	$fij  = $_POST['fecha_ini'];
	$des  = $_POST['fecha_fin'];
	
	### VALORES ANTERIORES
	$nomO  = $_POST['nomHid'];
	$docO  = $_POST['docHid'];
	$fijO  = $_POST['fijHid'];
	$desO  = $_POST['desHid'];
	###########################################################################	
	
	
	
	###########################################################################
	### SE VERIFICA SI CAMBIAN LOS DATOS
	if ($nomO != $nom){
		$setD = "SGD_DIR_NOMREMDES = '".$nom."',";
		$comentarioD = "el nombre, ";
	}
	
	if ($docO != $doc){
		$setD .= "SGD_DIR_DOC = '".$doc."'";
		$comentarioD .= " documento,";
	}
	
	if ($fijO != $fij){
		$setR = "RADI_NOTIF_FIJACION = '".$fij."',";
		$comentarioR .= " fecha de fijacion, ";
	}
		
	if ($desO != $des){
		$setR .= "RADI_NOTIF_DESFIJACION = '".$des."'";
		$comentarioR .= " fecha de desfijacion";
	}
	###########################################################################	
	
	
	
	###########################################################################	
	### SE VALIDA SI LAS CADENAS TERMINAN EN COMA (,) PARA ELIMINARLA
	if(substr($setR,-1)==",") 
		$setR = substr($setR,0,strlen($setR)-1);
	
	if(substr($setD,-1)==",") 
		$setD = substr($setD,0,strlen($setD)-1);
	
	if(substr($comentarioR,-1)==",") 
		$comentario = substr($comentarioR,0,strlen($comentarioR)-1);
	
	if(substr($comentarioD,-1)==",") 
		$comentario = substr($comentarioD,0,strlen($comentarioD)-1);
	###########################################################################	
	
	
	
	###########################################################################	
	### SE ACTUALIZAN DATOS EN LAS TABLAS RADICADO Y/O DIRECCIONES
	if ($setR){
		$sqlUpR = "	UPDATE	RADICADO
					SET		$setR
					WHERE	RADI_NUME_RADI = $rad";
		$rsR = $db->conn->Execute($sqlUpR);
		if (!$rsR){
			?>
			<script language="javascript">
				alert ('No se pudieron actualizar las fechas');
				window.location.href = 'index.php?<?=session_name()."=".session_id()."&krd=".$krd?>';
			</script>
			<?php
			exit();
		}
	}
	
	if ($setD){
		$sqlUpD = "	UPDATE	SGD_DIR_DRECCIONES
					SET		$setD
					WHERE	RADI_NUME_RADI = $rad";
		$rsD = $db->conn->Execute($sqlUpD);
		if (!$rsD){
			?>
			<script language="javascript">
				alert ('No se pudo actualizar nombre / documento');
				window.location.href = 'index.php?<?=session_name()."=".session_id()."&krd=".$krd?>';
			</script>
			<?php
			exit();
		}
	}
	
	$sqlUp = "UPDATE RADICADO SET RADI_NOTIFICADO = 1 WHERE RADI_NUME_RADI = '$rad'";
	$rs = $db->conn->Execute($sqlUp);
	if (!$rs) {
		?>
		<script language="javascript">
			alert ('No se pudo actualizar el campo de notificado');
			window.location.href = 'index.php?<?=session_name()."=".session_id()."&krd=".$krd?>';
		</script>
		<?php
		exit();
	}
	###########################################################################	
	
	
	
	
	###########################################################################
	###	ASOCIAR ARCHIVO ADJUNTO (Notificacion) AL RADICADO SELECCIONADO
	$year = substr($rad, 0, 4);
	$dep  = substr($rad, 4, 3);
	$aux  = $year. "/" .$dep. "/docs/";
	$ruta = trim(str_replace(" ","", BODEGAPATH));
	$adjunto = str_replace("/","\\",$ruta.$aux);
	$tamanoMax	= 15 * 1024 * 1024;
	
	### VALIDA ARCHIVOS ADJUNTOS
	if(!empty($_FILES["file"]["name"][0])) {
		
		### CONSULTA DE TIPOS DE ANEXOS
		$sql1 ="SELECT	ANEX_TIPO_CODI AS CODIGO,
						ANEX_TIPO_EXT AS EXT,
						ANEX_TIPO_MIME AS MIME
				FROM	ANEXOS_TIPO";
		$exte = $db->conn->Execute($sql1);

		while(!$exte->EOF) {
			$codigo = $exte->fields["CODIGO"];
			$ext	= $exte->fields["EXT"];
			$mime1	= $exte->fields["MIME"];
			$mime2	= explode(",",$mime1);
		
			## ARREGLO PARA CAPTURAR LAS EXTENCIONES DE ARCHIVO
			$exts[".".$ext]	= array ('codigo' => $codigo, 'mime' => $mime2);
			$exte->MoveNext();
		}

		### SE CAPTURAN LOS DATOS DEL ARCHIVO SELECCIONADO
		$nombre = strtolower(trim($_FILES["file"]["name"]));
		$type	= trim($_FILES["file"]["type"]);
		$tamano	= trim($_FILES["file"]["size"]);
		$tmporal= trim($_FILES["file"]["tmp_name"]);
		$ext	= strrchr($nombre,'.');
		

		### SE CONSULTA SI YA EXISTE UNA NOTIFICACION ANEXA AL RADICADO
		$aSql ="SELECT	ANEX_CODIGO,
						ANEX_DESC
				FROM	ANEXOS
				WHERE	ANEX_RADI_NUME = " .$rad. "AND
						SGD_TPR_CODIGO = 324 AND
						ANEX_SALIDA = 0";
		$rsA = $db->conn->Execute($aSql);
		
		$anexCod = $rsA->fields['ANEX_CODIGO'];
		
		
		### SI NO EXISTE LA CARPETA SE CREA.
		if(!is_dir($adjunto)){
			$rs	= mkdir($adjunto, 0700);
			if(empty($rs)){
				$error .= " No se pudo crear el directorio ";
			}
		}
		
					
		if (is_array($exts[$ext])){
			foreach ($exts[$ext]['mime'] as $value){
			    if(strcmp($type,$value)){
					$bandera = true;
					
					### SE VALIDA EL TAMAÑO DEL ARCHIVO
					if($tamano < $tamanoMax){
						
						### SE RECORTA EL NOMBRE
						if(strlen($nombre) > 60){
							$nombre	= substr($nombre, '-60:');
						}
						
						### NO EXISTE ANEXO DE NOTIFICACION PARA EL RADICADO SELECCIONADO
						if (!$anexCod) {
							
							include_once($ruta_raiz."/class_control/anexo.php");
							$anexo = new Anexo($db);
		
							
							### GRABAR EL REGISTRO EN LA BD
							$anexo->anex_radi_nume		= $rad;
							$anexo->usuaCodi			= $_SESSION['codusuario'];
							$anexo->depe_codi			= $_SESSION['dependencia'];
							$anexo->anex_solo_lect		= "'S'";
							$anexo->anex_tamano			= $tamano;
							$anexo->anex_creador		= "'".$_SESSION['krd']."'";
							$anexo->anex_desc			= "Notificacion: ". $nombre;
							$anexo->anex_nomb_archivo	= $nombre;
							$anexo->sgd_tpr_codigo		= 324;

							$auxnumero	 = $anexo->obtenerMaximoNumeroAnexo($nurad);
							$anexoCodigo = $anexo->anexarFilaRadicado($auxnumero);
							$nomFinal	 = $anexo->get_anex_nomb_archivo();
							
							### GUARDAR EL ARCHIVO EN LA CARPETA RESPECTIVA
							$Grabar_path = $adjunto.$nomFinal;
							$upOk = move_uploaded_file($tmporal, $Grabar_path);
							if (!$upOk) {
								$error .= " No se pudo almacenar el archivo, por favor intente de nuevo";
							}
						
						} //FIN -NO EXISTE ANEXO DE NOTIFICACION PARA EL RADICADO SELECCIONADO
						else {
							$auxCod = substr($anexCod,-4);
							$auxCod = str_pad($auxCod,5,"0",STR_PAD_LEFT);
							$random = mt_rand(1, 99999);
							$archivo = trim($rad)."_".trim($random)."_".trim($auxCod).trim($ext);
							$archivo = "1$archivo";
							
							$upAnex = "	UPDATE	ANEXOS 
										SET		ANEX_NOMB_ARCHIVO = '$archivo',
												ANEX_TAMANO	= $tamano,
												ANEX_TIPO	= 7,
												ANEX_DESC	= 'Notificacion: $obs'
										WHERE	ANEX_CODIGO	= '$anexCod'";
							$rsAnex = $db->conn->Execute($upAnex);
							if ($rsAnex) {
    							$insHist = "INSERT INTO HIST_EVENTOS_ANEXOS
    											(ANEX_RADI_NUME, ANEX_CODIGO, SGD_TTR_CODIGO, HIST_FECH, USUA_DOC)
    										VALUES
    											($rad, '$anexCod', 84, $sqlFechaHoy,'".$_SESSION['usua_doc']."')";
    							$rsHist = $db->conn->Execute($insHist);
    							
    							//Inserta histórico de imagen
    							$insLog = "INSERT INTO SGD_HIST_IMG_ANEX_RAD 
    											(ANEX_RADI_NUME, ANEX_CODIGO, RUTA, USUA_DOC, USUA_LOGIN, FECHA, ID_TTR_HIAN)
    										VALUES
    											($rad, '$anexCod', '$archivo', '".$_SESSION['usua_doc']."', '".$_SESSION['krd']."', $sqlFechaHoy, 23)";
    							$rsLog = $db->conn->Execute($insLog);
    							
    							### GUARDAR EL ARCHIVO EN LA CARPETA RESPECTIVA
    							$Grabar_path = $adjunto.$archivo;
    							$upOk = move_uploaded_file($tmporal, $Grabar_path);
    							if (!$upOk) {
    								$error .= " No se pudo almacenar el archivo, por favor intente de nuevo";
    							}
							} else {
							    $error .= " No se pudo actualizar el archivo en BD, por favor intente de nuevo";
							}
						}
						
					}//FIN -SE VALIDA EL TAMAÑO DEL ARCHIVO
					else{
						$error .= " El archivo supera el tama&ntilde;o max. permitido";
					}
				}
			}
			if(empty($bandera)){
				$error .= " No es permitido el tipo de archivo seleccionado";
			}
		}
		else {
			$error .= " No es permitido el tipo de archivo seleccionado";
		}
	}
	###########################################################################
	
	
	
	###########################################################################
	### SE REGISTRA EN EL HISTORICO LA NOTIFICACION
	include_once($ruta_raiz."/include/tx/Historico.php");
	$hist = new Historico($db);
	
	if ($rsD)
		$observa .= $comentarioD;
	
	if ($rsR)
		$observa .= $comentarioR;
	
	if ($rsD || $rsR) {
		$observa = "Se registra Notificación en la página Web DNP. <br/>
					Se actualizaron los siguientes datos: ".$observa." <br/> 
					Se Notifica al Sr. (a) ".$nom." - ".$obs;
		$radicadosSel[0] = $rad;
		
		$hist->insertarHistorico(	$radicadosSel,
									$_SESSION['dependencia'],
									$_SESSION['codusuario'],
									$_SESSION['dependencia'],
									$_SESSION['codusuario'],
									$observa,
									36);
	}
	###########################################################################
	
?>

<html>
	<head>
		<title> Notificaciones Administrativas </title>
	</head>
	<body>
		<script>
			window.location.href = 'index.php?<?=session_name()."=".session_id()."&krd=".$krd?>';
		</script>
	</body>
</html>