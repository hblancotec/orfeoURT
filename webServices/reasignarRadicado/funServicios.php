<?php 
//echo reasignarRadicado($resolucion,$tipoNotificacion,$fechaNotificacion,$fechaFijacion );

function reasignarRadicado($numeroRadicado,$usuarioOrigen,$usuarioDestino,$comentario ){
	if($numeroRadicado==Null) return "ERROR: hace falta de numero de radicado";
	if($usuarioOrigen==Null) return "ERROR: hace falta de usuario Origen";
	if($usuarioDestino==Null) return "ERROR: hace falta de usuario destino";
	if($comentario==Null) return "ERROR: hace falta de comentario";
	if (strlen( $numeroRadicado) == "14") {
        	global $ruta_raiz;       
		//consulta si el radicado existe
		$consultaRad ="select radi_nume_radi,radi_path,radi_usua_actu, radi_depe_actu  from radicado where radi_nume_radi=".$numeroRadicado;
		$db = new ConnectionHandler($ruta_raiz,'WS'); 
//$db->conn->debug=true;
		$rs2=$db->conn->Execute( $consultaRad );
		if(!$rs2->EOF){	
			include "../include/tx/Tx.php";
			//valida si posee trd
			$anoRad = substr($numeroRadicado,0,4);
			$isqlTRDP = "select radi_nume_radi as RADI_NUME_RADI from SGD_RDF_RETDOCF r where r.RADI_NUME_RADI='$numeroRadicado'";
 			$rsTRDP = $db->conn->Execute($isqlTRDP);
			$radiNumero = $rsTRDP->fields[0];
			if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")  && strlen (trim($radiNumero)==0))
				{	return "ERROR: FALTA CLASIFICACION TRD";	}
			$consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_login='".$usuarioOrigen."'";
			$rs1=$db->conn->Execute( $consultaUsuarioOrigen );
			$dependenciaOrigen=$rs1->fields[1];
			$codusuarioOrigen=$rs1->fields[0];
			if ($rs2->fields[2]!=$rs1->fields[0]){ return "ERROR: El Radicado No Pertence a Esta Usuario. "; }
				$consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_login='".$usuarioDestino."'";
				$rs5=$db->conn->Execute( $consultaUsuarioOrigen );
				$dependenciaDestino=$rs5->fields[1];
				$codusuarioDestino=$rs5->fields[0];
				$codTx=9;
				$carp_codi=0;
				$rs4 = new Tx($db);
		        	$radinums[0]=$numeroRadicado;
				//return " destino= $dependenciaDestino origen= $dependenciaOrigen usuarioOrigen = $codusuarioOrigen destino $codusuarioDestino";
				if(($dependenciaDestino!=$dependenciaOrigen && $codusuarioOrigen!=1) || ($codusuarioDestino!=1 && $dependenciaDestino!=$dependenciaOrigen))
					{ return "ERROR : la reasignacion no se realiza. estan en dependecias diferentes "; }
				$usCodDestino = $rs4->reasignar( $radinums, $usuarioOrigen,$dependenciaDestino,$dependenciaOrigen, $codusuarioDestino,$codusuarioOrigen,"no",$comentario,$codTx,$carp_codi,"ok");
				$consultaUsuario ="select radi_nume_radi  from radicado where radi_nume_radi=".$numeroRadicado." and radi_depe_actu =".$dependenciaDestino." and radi_usua_actu =".$codusuarioDestino." and  carp_codi=".$carp_codi;
			$rs3=$db->conn->Execute( $consultaUsuario );
			if(!$rs3->EOF){	
			  return "OK";
			}
			else{
			    return "ERROR: Fallo el cambio de carpetas, intetar de nuevo.";
			}
		}
		else{
			return "ERROR: El radicado no existe.";
		}
	}
	else{
	    return "ERROR: El numero de radicado es encuentra incompleto. ";
	}
}


function reasignarRadicadoCarp($numeroRadicado,$usuarioOrigen,$usuarioDestino,$comentario,$carpeta ){
	if($numeroRadicado==Null) return "ERROR: hace falta de numero de radicado";
	if($usuarioOrigen==Null) return "ERROR: hace falta de usuario Origen";
	if($usuarioDestino==Null) return "ERROR: hace falta de usuario destino";
	if($comentario==Null) return "ERROR: hace falta de comentario";
	if($carpeta==Null) return "ERROR: hace falta de la carpeta";
	// $carp2=$carp;
	if (strlen( $numeroRadicado) == "14") {
        	global $ruta_raiz;       
		//consulta si el radicado existe
		$consultaRad ="select radi_nume_radi,radi_path,radi_usua_actu, radi_depe_actu  from radicado where radi_nume_radi=".$numeroRadicado;
		$db = new ConnectionHandler($ruta_raiz,'WS');
//$db->conn->debug=true;
		$rs2=$db->conn->Execute( $consultaRad );
		if(!$rs2->EOF){	
			include "../include/tx/Tx.php";
			//valida si posee trd
			$anoRad = substr($numeroRadicado,0,4);
			$isqlTRDP = "select radi_nume_radi as RADI_NUME_RADI from SGD_RDF_RETDOCF r where r.RADI_NUME_RADI='$numeroRadicado'";
 			$rsTRDP = $db->conn->Execute($isqlTRDP);
			$radiNumero = $rsTRDP->fields[0];
			if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")  && strlen (trim($radiNumero)==0))
				{	return "ERROR: FALTA CLASIFICACION TRD";	}
			$consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_login='".$usuarioOrigen."'";
			$rs1=$db->conn->Execute( $consultaUsuarioOrigen );
			$dependenciaOrigen=$rs1->fields[1];
			$codusuarioOrigen=$rs1->fields[0];
			//return $rs2->fields[2].'!='.$rs1->fields[0];
			if ($rs2->fields[2]!=$rs1->fields[0]){ return "ERROR: El Radicado No Pertence a Esta Usuario. "; }
				$consultaUsuarioOrigen ="select  usua_codi,depe_codi,usua_esta from usuario where usua_login='".$usuarioDestino."'";
				$rs5=$db->conn->Execute( $consultaUsuarioOrigen );
		        if($rs5->fields[2]==0){ return "ERROR : El usuario esta inactivo"; }
				$dependenciaDestino=$rs5->fields[1];
				$codusuarioDestino=$rs5->fields[0];
				$codTx=9;
		        $sqlcarp="select carp_codi from carpeta where upper(carp_desc)=upper('".$carpeta."')";
                $rscarp=$db->conn->Execute( $sqlcarp );
				//  	$carp_codi=$rscarp->fields['CARP_CODI'];
                if(!$rscarp->EOF){ 
                 	$carp_codi=$rscarp->fields[0];
                }
                else{
                	 return "ERROR: La carpeta  no Existe";
                }
				$rs4 = new Tx($db);
		        	$radinums[0]=$numeroRadicado;
				//return " destino= $dependenciaDestino origen= $dependenciaOrigen usuarioOrigen = $codusuarioOrigen destino $codusuarioDestino";
				if(($dependenciaDestino!=$dependenciaOrigen && $codusuarioOrigen!=1) || ($codusuarioDestino!=1 && $dependenciaDestino!=$dependenciaOrigen))
					{ return "ERROR : la reasignacion no se realiza. estan en dependecias diferentes "; }
				$usCodDestino = $rs4->reasignar( $radinums, $usuarioOrigen,$dependenciaDestino,$dependenciaOrigen, $codusuarioDestino,$codusuarioOrigen,"no",$comentario,$codTx,$carp_codi,"ok");
				$consultaUsuario ="select radi_nume_radi  from radicado where radi_nume_radi=".$numeroRadicado." and radi_depe_actu =".$dependenciaDestino." and radi_usua_actu =".$codusuarioDestino." and  carp_codi=".$carp_codi;
			$rs3=$db->conn->Execute( $consultaUsuario );
			if(!$rs3->EOF){	
			  return "OK";
			}
			else{
			    return "ERROR: Fallo el cambio de carpetas, intetar de nuevo.";
			}
		}
		else{
			return "ERROR: El radicado no existe.";
		}
	}
	else{
	    return "ERROR: El numero de radicado es encuentra incompleto. ";
	}
}

function reasignarRadicadoXDoc($numeroRadicado,$docUsuarioOrigen,$docUsuarioDestino,$comentario ){
	if($numeroRadicado==Null) return "ERROR: hace falta de numero de radicado";
	if($docUsuarioOrigen==Null) return "ERROR: hace falta de usuario Origen";
	if($docUsuarioDestino==Null) return "ERROR: hace falta de usuario destino";
	if($comentario==Null) return "ERROR: hace falta de comentario";
	$servidorIP = $_SERVER['REMOTE_ADDR'];
	$comentario =  $comentario . "(Desde IP $servidorIP)";
	if (strlen( $numeroRadicado) == "14") {
        	global $ruta_raiz;       
		//consulta si el radicado existe
		$consultaRad ="select radi_nume_radi,radi_path,radi_usua_actu, radi_depe_actu  from radicado where radi_nume_radi=".$numeroRadicado;
		$db = new ConnectionHandler($ruta_raiz,'WS'); 
//$db->conn->debug=true;
		$rs2=$db->conn->Execute( $consultaRad );
		if(!$rs2->EOF){	
			include "../include/tx/Tx.php";
			//valida si posee trd
			$anoRad = substr($numeroRadicado,0,4);
			$isqlTRDP = "select radi_nume_radi as RADI_NUME_RADI from SGD_RDF_RETDOCF r where r.RADI_NUME_RADI='$numeroRadicado'";
 			$rsTRDP = $db->conn->Execute($isqlTRDP);
			$radiNumero = $rsTRDP->fields[0];
			//if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")  && strlen (trim($radiNumero)==0))
			//	{	return "ERROR: FALTA CLASIFICACION TRD";	}
			$consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_doc='".$docUsuarioOrigen."'";
			$rs1=$db->conn->Execute( $consultaUsuarioOrigen );
			$dependenciaOrigen=$rs1->fields[1];
			$codusuarioOrigen=$rs1->fields[0];
			if ($rs2->fields[2]!=$rs1->fields[0]){ return "ERROR: El Radicado No Pertence a Esta Usuario. "; }
				$consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_doc='".$docUsuarioDestino."'";
				$rs5=$db->conn->Execute( $consultaUsuarioOrigen );
				$dependenciaDestino=$rs5->fields[1];
				$codusuarioDestino=$rs5->fields[0];
				$codTx=63;
				$carp_codi=0;
				$rs4 = new Tx($db);
		        	$radinums[0]=$numeroRadicado;
				//return " destino= $dependenciaDestino origen= $dependenciaOrigen usuarioOrigen = $codusuarioOrigen destino $codusuarioDestino";
				//if(($dependenciaDestino!=$dependenciaOrigen && $codusuarioOrigen!=1) || ($codusuarioDestino!=1 && $dependenciaDestino!=$dependenciaOrigen))
				//	{ return "ERROR : la reasignacion no se realiza. estan en dependecias diferentes "; }
				$usCodDestino = $rs4->reasignar( $radinums, $usuarioOrigen,$dependenciaDestino,$dependenciaOrigen, $codusuarioDestino,$codusuarioOrigen,"no",$comentario,$codTx,$carp_codi,"ok");
				$consultaUsuario ="select radi_nume_radi  from radicado where radi_nume_radi=".$numeroRadicado." and radi_depe_actu =".$dependenciaDestino." and radi_usua_actu =".$codusuarioDestino." and  carp_codi=".$carp_codi;
			$rs3=$db->conn->Execute( $consultaUsuario );
			if(!$rs3->EOF){	
			  return "OK";
			}
			else{
			    return "ERROR: Fallo el cambio de carpetas, intetar de nuevo.";
			}
		}
		else{
			return "ERROR: El radicado no existe.";
		}
	}
	else{
	    return "ERROR: El numero de radicado es encuentra incompleto. ";
	}
}
function reasignarMRadicadoXDoc($numeroRadicado,$docUsuarioOrigen,$arregloDocDestinatarios,$comentario ){
        $docDestinatarios = explode(",",$arregloDocDestinatarios);
        $numeroRadicado[0] = $docDestinatarios[0];
        if($numeroRadicado==Null) return "ERROR: hace falta de numero de radicado";
	if($docUsuarioOrigen==Null) return "ERROR: hace falta de usuario Origen";
	if(!$arregloDocDestinatarios) return "ERROR: hace falta Destinatarios";
	if($comentario==Null) return "ERROR: hace falta de comentario";
	$servidorIP = $_SERVER['REMOTE_ADDR'];
	$comentario =  $comentario . "(Desde IP $servidorIP)";
	if (strlen( $numeroRadicado) == "14") {
        global $ruta_raiz;
        //return $arregloDocDestinatarios;
        //consulta si el radicado existe
        $consultaRad ="select radi_nume_radi,radi_path,radi_usua_actu, radi_depe_actu  from radicado where radi_nume_radi=".$numeroRadicado;
        $db = new ConnectionHandler($ruta_raiz,'WS'); 
        //$db->conn->debug=true;
        $rs2=$db->conn->Execute( $consultaRad );
        if(!$rs2->EOF){	
                include "../include/tx/Tx.php";
                //valida si posee trd
                $anoRad = substr($numeroRadicado,0,4);
                
                $consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_doc='".$docUsuarioOrigen."'";
                $rs1=$db->conn->Execute( $consultaUsuarioOrigen );
                $dependenciaOrigen=$rs1->fields[1];
                $codusuarioOrigen=$rs1->fields[0];
                $docUsuarioDestino = $docDestinatarios[0];
                //return $docUsuarioDestino;
                //if ($rs2->fields[2]!=$rs1->fields[0]){ return "ERROR: El Radicado No Pertence a Esta Usuario. "; }
                        $consultaUsuarioOrigen ="select  usua_codi,depe_codi, usua_login from usuario where usua_doc='".$docUsuarioDestino."'";
                        $rs5=$db->conn->Execute( $consultaUsuarioOrigen );
                        $dependenciaDestino=$rs5->fields[1];
                        $codusuarioDestino=$rs5->fields[0];
                        $usuaLoginR=$rs5->fields[2];
                        $codTx=63;
                        $carp_codi=0;
                        $rs4 = new Tx($db);
                        $radinums[0]=$numeroRadicado;
                        //return " destino= $dependenciaDestino origen= $dependenciaOrigen usuarioOrigen = $codusuarioOrigen destino $codusuarioDestino";
                        //if(($dependenciaDestino!=$dependenciaOrigen && $codusuarioOrigen!=1) || ($codusuarioDestino!=1 && $dependenciaDestino!=$dependenciaOrigen))
                        //	{ return "ERROR : la reasignacion no se realiza. estan en dependecias diferentes "; }
                        //$usCodDestino = $rs4->reasignar( $radinums, $usuarioOrigen,$dependenciaDestino,$dependenciaOrigen, $codusuarioDestino,$codusuarioOrigen,"no",$comentario . "(txOk)",$codTx,$carp_codi,"ok");
                        //$consultaUsuario ="select radi_nume_radi  from radicado where radi_nume_radi=".$numeroRadicado." and radi_depe_actu =".$dependenciaDestino." and radi_usua_actu =".$codusuarioDestino." and  carp_codi=".$carp_codi;
                $rs3=$db->conn->Execute( $consultaUsuario );
                foreach($docDestinatarios as $key=>$docUsuarioDestino){
                        if($key!=0)
                        {
                          $consultaUsuarioDestino ="select  usua_codi,depe_codi, usua_login from usuario where usua_doc='".$docUsuarioDestino."'";
                          
                          $rs5=$db->conn->Execute( $consultaUsuarioDestino );
                          
                          $depeCodDestino=$rs5->fields["DEPE_CODI"];
                          //return $depeCodDestino;
                          $usuaCodDestino=$rs5->fields["USUA_CODI"];
                          $usuaLogin=$rs5->fields["USUA_LOGIN"];
                          $sqlMultiple = "INSERT INTO SGD_RG_MULTIPLE (RADI_NUME_RADI, usuario, area, estatus,  fechainicio)
                         VALUES ($numeroRadicado, $usuaCodDestino,$depeCodDestino, 'ACTIVO', getdate())";
                         //return $sqlMultiple;
                          $rs5=$db->conn->Execute( $sqlMultiple );
                          if(!$rs5)
                          {
                                return "ERROR: Fallo al Insertar $docUsuarioDestino ($tkOk)";
                          }else{
                                $txOk .= " - $usuaLogin ";
                          }
                          
                        }
                   }
                  if($txOk) $comentario = $comentario . " <br> Adicionalmente se Envia a ($txOk)";
                  $usCodDestino = $rs4->reasignar( $radinums, $usuarioOrigen,$dependenciaDestino,$dependenciaOrigen, $codusuarioDestino,$codusuarioOrigen,"no",$comentario,$codTx,$carp_codi,"ok");
                  return "Ok Reasignacion a $usuaLoginR ($txOk)";
                }
		else{
			return "ERROR: El radicado no existe.";
		}
	}
	else{
	    return "ERROR: El numero de radicado es encuentra incompleto. ";
	}
}

?>
