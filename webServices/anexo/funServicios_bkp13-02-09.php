<?php
/**
* funcion que crea un Anexo, y ademas decodifica el anexo enviasdo en base 64
*
* @param string $radiNume numero del radicado al cual se adiciona el anexo
* @param base64 $file archivo codificado en base64
* @param string $filename nombre original del anexo, con extension
* @param string $correo correo electronico del usuario que adiciona el anexo
* @param string $descripcion descripcion del anexo
* @return string mensaje de error en caso de fallo o el numero del anexo en caso de exito
*/
function anexoRadicadoToRadicado($radiNume,$file,$filename,$correo,$descripcion,$radiSalida,$estadoAnexo){
	global $ruta_raiz;
	// Modificado SSPD 24-Noviembre-2008
	// Se agregó include( $ruta_raiz_subdirectorios."include/db/ConnectionHandler.php" )
	include_once( RUTA_RAIZ."include/db/ConnectionHandler.php" );
	$db = new ConnectionHandler($ruta_raiz);
	$usuario=getUsuarioCorreo($correo);
	$error=(isset($usuario['error']))?true:false;
	$ruta=RUTA_RAIZ."bodega/".substr($radiNume,0,4)."/".substr($radiNume,4,3)."/docs/";
	$numAnexos=numeroAnexos($radiNume,$db)+1;
	$maxAnexos=maxRadicados($radiNume,$db)+1;
	//PARAMETROS
	if (is_null($estadoAnexo) || strlen($estadoAnexo)<1){
		$estadoAnexo =0;
	}
	$origenAnexo=0;
	$soloLectura ='n';
	$fechaRadicado='null';
	$sgdDirTipo=1;
	$numAnexo=($numAnexos > $maxAnexos)?$numAnexos:$maxAnexos;
	$nombreAnexo=$radiNume.substr("00000".$numAnexo,-5);
	$nombreAnexoExtension = $nombreAnexo;
	if (is_null($radiSalida)|| strlen($radiSalida)!=14){
		$extension=substr($filename,strrpos($filename,".")+1);
		$subirArchivo=subirArchivo($ruta,$file,$nombreAnexo.".".$extension);
		$tamanoAnexo = $subirArchivo / 1024; //tamano en kilobytes
		$error=($error && !$subirArchivo)?true:false;
		$desc=$nombreAnexo;
		$radiSalida = 'null';
	}else{

		/* PROCEDIMIENTO CUANDO SE ENVIA EL PARAMETRO DE RADICADO DE SALIDA */
		$fechaRadicado = $db->conn->SQLDate("d-m-Y H:i A","RADI_FECH_RADI");
		$sql = "select $fechaRadicado as Fecha, r.radi_path from radicado r where r.radi_nume_radi=$radiSalida";
		$desc=$radiSalida;

		$rs = $db->conn->Execute($sql);
		while (!$rs->EOF){
			$fechaRadicado  = $rs->fields['FECHA'];
			$radiSalidaPath  =  $rs->fields['RADI_PATH'];

			break;
		}
		$fechaRadicado =  $db->conn->DBTimestamp($fechaRadicado);
		//BUSCA EL PATH DE LA IMAGEN DEL RADICADO
		if (!is_null($radiSalidaPath) ||  strlen($radiSalidaPath) > 0 ){
			$rutaSalida = RUTA_RAIZ."bodega".$radiSalidaPath;

			if (file_exists($rutaSalida)){
				$tamanoAnexo = filesize($rutaSalida)/1024;
			}else{
				return "ERROR: La imagen no existe en la Bodega de Orfeo ".$rutaSalida;

			}
			$extension=substr($rutaSalida,strrpos($rutaSalida,".")+1);
			$nombreAnexoExtension = $radiSalida;
			if ($estadoAnexo==0){
				$estadoAnexo = 2;//COMO YA ESTA RADICADO
			}
		}else{
			return "ERROR: La imagen no existe en la Bodega de Orfeo";

		}
	}
	if ($extension=='pdf'){
		$soloLectura='s';
		$origenAnexo=1;
	}
	$fechaAnexado= $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	$tipoAnexo=tipoAnexo($extension,$db);
	if(!$error){
		$tipoAnexo=($tipoAnexo)?$tipoAnexo:"NULL";
		$consulta= "INSERT INTO ANEXOS (ANEX_CODIGO,ANEX_RADI_NUME,ANEX_TIPO,ANEX_TAMANO,ANEX_SOLO_LECT,ANEX_CREADOR,
                  ANEX_DESC,ANEX_NUMERO,ANEX_NOMB_ARCHIVO,ANEX_ESTADO,SGD_REM_DESTINO,ANEX_FECH_ANEX, ANEX_BORRADO, RADI_NUME_SALIDA,ANEX_ORIGEN,ANEX_RADI_FECH,USUA_DOC, SGD_DIR_TIPO)
                  VALUES('$nombreAnexo',$radiNume,$tipoAnexo,$tamanoAnexo,'$soloLectura','".$usuario['login']."','$descripcion'
                  ,$numAnexo,'$nombreAnexoExtension.$extension','$estadoAnexo',1,$fechaAnexado, 'N',$radiSalida,$origenAnexo,$fechaRadicado,".$usuario['documento'].",$sgdDirTipo)";
		if ($db->conn->Execute($consulta)){

			$recordSet["RADI_NUME_DERI"] = $radiSalida;
			$recordSet["RADI_TIPO_DERI"] = 0;
			$recordWhere["RADI_NUME_RADI"] = $radiNume;
			$ok = $db->update("RADICADO", $recordSet,$recordWhere);
			include_once (RUTA_RAIZ.'include/tx/Historico.php');
			$Historico = new Historico($db);
			$radicadosSel[0] = $radiNume;
			$Historico->insertarHistorico($radicadosSel,
                              $usuario['dependencia'],
                              $usuario['codusuario'],
                              $usuario['dependencia'],
                              $usuario['codusuario'],
                              "Archivo anexado mediante servicio Web $desc" ,
                              64);
			return $nombreAnexo;
		}else{
			return "ERROR: al insertar anexo";
		}
	}else{
		return "ERROR";
	}
}
?>