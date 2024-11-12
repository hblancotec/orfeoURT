<?php
function tipificarDocumento ($nurad, $usuario, $codiSRD, $codiSBRD, $codiTPR){

	//VALIDACIONES DATOS DE ENTRADA
	if(is_null($nurad) ||  strlen($nurad)!=14){
		return "ERROR: El Numero de Radicado no  puede ser Nulo o de longitud diferente a 14";
	}
	if(is_null($codiSBRD) ||  strlen($codiSBRD)<1){
		return "ERROR: El Numero de Subserie no  puede ser Nulo o de longitud 0";
	}
	if(is_null($codiSRD) ||  strlen($codiSRD)<1){
		return "ERROR: El Numero de Serie no  puede ser Nulo o de longitud 0";
	}
	if(is_null($codiTPR) ||  strlen($codiTPR)<1){
		return "ERROR: El Numero de Tipo de Retencion Documental no  puede ser Nulo o de longitud 0";
	}
	//VALIDAR SI EL DOCUMENTO YA ESTA TIPIFICADO
	if(isDocumentoTipificado($nurad)){
		return "ERROR: El documento YA esta Tipificado";
	}
	global  $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);

	//BUSCAR USUARIO HABILITADO
	$sql= "select USUA_CODI,DEPE_CODI,USUA_DOC from usuario where
	usua_email = '".strtoupper($usuario)."@superservicios.gov.co' or
	usua_email = '".strtolower($usuario)."@superservicios.gov.co'
	and usua_esta = 1";       $rs = $db->conn->Execute($sql);
	while (!$rs->EOF){
		$codusuario  = $rs->fields['USUA_CODI'];
		$dependencia = $rs->fields['DEPE_CODI'];
		$usua_doc =  $rs->fields['USUA_DOC'];
		$usuaDocExp = $usua_doc;
		$rs->MoveNext();
	}     //VALIDAR SI EL USUARIO EXISTE O NO ESTA HABILITADO
	if(is_null($codusuario)){
		return "ERROR: El usuario NO EXISTE o NO ESTA HABILITADO en ORFEO";
	}
	//VALIDAR SERIE Y SUBSERIE 
	if (!$descSerie = isSerieHabilitada($codiSRD,$codiSBRD,null)){
		return "ERROR: La serie $codiSRD  o la subserie $codiSBRD no esta Habilitida para este periodo";
	}

	//VALIDAR TIPO DOCUMENTAL PARA TIPO DE RADICADO
	if (!$descTpr = esTipoDocumental($nurad,$codiTPR)){
	return "ERROR: El tipo documental no esta habilitado para este tipo de Readicado";
	}
	$sql=  "select sgd_mrd_codigo from sgd_mrd_matrird m where m.SGD_SRD_CODIGO = $codiSRD";
	$sql.=" and m.SGD_SBRD_CODIGO = $codiSBRD and m.DEPE_CODI = $dependencia and m.SGD_TPR_CODIGO = $codiTPR";
	$rs = $db->conn->Execute($sql);
	while (!$rs->EOF){
		$codigoMrd  = $rs->fields['SGD_MRD_CODIGO'];
		$rs->MoveNext();
	}
	if (is_null($codigoMrd) || strlen($codigoMrd) < 1){
		return "ERROR: No se encontro el Tipo de Retenci&oacute;n documental";
	}
	$fecha=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	try{
		$db->conn->BeginTrans();
		$sql =    "insert into sgd_rdf_retdocf (sgd_mrd_codigo,radi_nume_radi,depe_codi,usua_codi,usua_doc,";
		$sql .= " sgd_rdf_fech)";
		$sql .= " values ($codigoMrd,$nurad,$dependencia,$codusuario,'$usua_doc',$fecha)";
		if(!$db->conn->Execute($sql)){
			return "ERROR: Al insertar el tipo documental";
		}
		include_once (RUTA_RAIZ.'include/tx/Historico.php');
		$Historico = new Historico($db);
		$radicadosSel[0]=$nurad;
		$Historico->insertarHistorico($radicadosSel,
				      $dependencia,
				      $codusuario,
				      $dependencia,
				      $codusuario,
				      "*TRD*".$descSerie."/".$descTpr,
				      32);
					    $db->conn->CommitTrans();
	}catch(Exception $e){
		return $e->getMessage();
	}

	return "OK";
}

function esTipoDocumental ($radicado, $trd){
  global  $ruta_raiz;
  $db = new ConnectionHandler($ruta_raiz);
    $sql = "select * from SGD_TPR_TPDCUMENTO where SGD_TPR_ESTADO = 1 ";
  $sql.= " and SGD_TPR_TP".substr($radicado,strlen($radicado)-1,strlen($radicado))." = 1 ";
  $sql.= " and SGD_TPR_CODIGO = $trd";
    $rs = $db->conn->Execute($sql);
    if (!$rs->EOF){
      return $rs->fields['SGD_TPR_DESCRIP'];         }else{         return false;
  }
}

function isSerieHabilitada ($serie, $subserie, $fechaHabilitada){
  global  $ruta_raiz;
  $db = new ConnectionHandler($ruta_raiz);
  $retorno = false;
    if(is_null($fechaHabilitada)){
      $fechaHabilitada=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
  }
    $sql = "select * from SGD_SRD_SERIESRD ";     $sql.= "where SGD_SRD_FECHINI <= $fechaHabilitada ";
  $sql.= "and $fechaHabilitada < SGD_SRD_FECHFIN ";
  $sql.= "and SGD_SRD_CODIGO = $serie";

  $rs = $db->conn->Execute($sql);
    if (!$rs->EOF){
      if (is_null($subserie)){
          return $rs->fields['SGD_SRD_DESCRIP'];        //SI NO ENVIAN SUBSERIE SOLO VALIDA SERIE
      }else{             $retorno = $rs->fields['SGD_SRD_DESCRIP'];           }
  }else{         return false;    //SI NO ESTA HABILITADA LA SERIE
  }
    $sql = "select * from SGD_SBRD_SUBSERIERD ";     $sql.= "where SGD_SBRD_FECHINI <= $fechaHabilitada ";
  $sql.= "and $fechaHabilitada < SGD_SBRD_FECHFIN ";
  $sql.= "and SGD_SRD_CODIGO = $serie and SGD_SBRD_CODIGO = $subserie";
    $rs = $db->conn->Execute($sql);
    if (!$rs->EOF){
      return $retorno."/".$rs->fields['SGD_SBRD_DESCRIP'];
  }else{         return false;    //SI NO ESTA HABILITADA LA SUBSERIE
  }
}

function isDocumentoTipificado ($radicado){
  global  $ruta_raiz;
  $db = new ConnectionHandler($ruta_raiz);     $sql = "select * from sgd_rdf_retdocf where radi_nume_radi = $radicado";
  $rs = $db->conn->Execute($sql);
    if (!$rs->EOF){
      return true;
  }     return false;
}

function  tiposDocumentales ($serie,$dependencia){
// Modificado SSPD 01-Diciembre-2008
// Cambió TPR.SGD_TPR_ESTADO = 1 por (TPR.SGD_TPR_ESTADO = 1 OR TPR.SGD_TPR_ESTADO IS NULL)
	$sql =
	"SELECT  DISTINCT MRD.DEPE_CODI , MRD.SGD_SRD_CODIGO, SER.SGD_SRD_DESCRIP,SBR.SGD_SBRD_CODIGO AS SBRD, SBR.SGD_SBRD_DESCRIP AS SBRD_DES,TPR.SGD_TPR_CODIGO AS TPR, TPR.SGD_TPR_DESCRIP AS TPR_DES
	FROM    SGD_MRD_MATRIRD MRD,
		SGD_TPR_TPDCUMENTO TPR,
		SGD_SRD_SERIESRD SER,
		SGD_SBRD_SUBSERIERD SBR
	WHERE   TPR.SGD_TPR_CODIGO = MRD.SGD_TPR_CODIGO AND
		MRD.SGD_MRD_ESTA = 1 AND
		SER.SGD_SRD_FECHFIN > SYSDATE AND
		SER.SGD_SRD_CODIGO = MRD.SGD_SRD_CODIGO AND
		SBR.SGD_SRD_CODIGO = SER.SGD_SRD_CODIGO AND
		SBR.SGD_SBRD_CODIGO = MRD.SGD_SBRD_CODIGO AND
		SBR.SGD_SBRD_FECHFIN > SYSDATE AND
		MRD.SGD_SRD_CODIGO = $serie AND MRD.DEPE_CODI = $dependencia AND 
		(TPR.SGD_TPR_ESTADO = 1 OR TPR.SGD_TPR_ESTADO IS NULL)";
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	$rs = $db->conn->Execute($sql);
	$indice = 0;
	$mat=array();

	while (!$rs->EOF){
		$mat[$indice]= $rs->fields['SBRD']."#".$rs->fields['SBRD_DES']."#".$rs->fields['TPR']."#".$rs->fields['TPR_DES'];
		$indice++;
		$rs->MoveNext();
	}
	return $mat;
}
?>