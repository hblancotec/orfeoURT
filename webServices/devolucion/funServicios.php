<?php

function devolucion($comentario,$radinume,$usuario){
	if (strlen($radinume) == "14") {     
        	global $ruta_raiz;       
		//consulta si el radicado existe
		$consultaRad ="select radi_nume_radi,radi_path,radi_usua_actu, radi_depe_actu  from radicado where radi_nume_radi=".$radinume;
		$db = new ConnectionHandler($ruta_raiz,'WS');
		$rs2=$db->conn->Execute( $consultaRad );
		if(!$rs2->EOF){	
			include "../include/tx/Tx.php";
			  //valida si posee trd
			$anoRad = substr($radinume,0,4);
			$isqlTRDP = "select radi_nume_radi as RADI_NUME_RADI from SGD_RDF_RETDOCF r where r.RADI_NUME_RADI = $radinume";
 			$rsTRDP = $db->conn->Execute($isqlTRDP);
			$radiNumero = $rsTRDP->fields[0];
			if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")  && strlen (trim($radiNumero)==0))
			{	return "ERROR: NO SE PERMITE LA OPERACION FALTA CLASIFICACION TRD";	}
			$isqlw="select b.RADI_USU_ANTE as RADI_USU_ANTE  from radicado  b, usuario u where b.radi_nume_radi = ".$record_id." AND b.RADI_USU_ANTE=u.USUA_LOGIN and  u.usua_esta=0";
			$UsuIn  = $db->conn->Execute($isqlw);										 			$usuInAct=$UsuIn->fields[0];	
					if ($usuInAct != null)
		    	{	return "ERROR: NO SE PERMITE LA OPERACION FALTA CLASIFICACION TRD";	}

			$consultaUsuario ="select  usua_codi,depe_codi from usuario where usua_login='".$usuario."'";
			$rs1=$db->conn->Execute( $consultaUsuario );
			$codTx=16;
			$carp_codi=11;
			$dependencia=$rs1->fields[1];
			$codusuario=$rs1->fields[0];
 //return " - ".$consultaUsuario." -".$rs2->fields[2]." -".$rs1->fields[0]." x ".$consultaRad."  c $ruta_raiz/include/tx/Tx.php";
			if ($rs2->fields[2]!=$rs1->fields[0]){ return "ERROR: El Radicado No Pertence a Esta Usuario. "; }
			$rs4 = new Tx($db);
			$radinums[0]=$radinume;
		          $usCodDestino = $rs4->devolver( $radinums, $usuario,$dependencia, $codusuario,"no", $comentario);
			$consultaUsuario ="select radi_nume_radi  from radicado where radi_nume_radi=".$radinume." and radi_depe_actu =".$depedencia." and radi_usua_actu =".$codusuario." and  carp_codi=".$carp_codi;
			$rs3=$db->conn->Execute( $consultaUsuario );
			if(!$rs3->EOF){	
			  return "OK";
			}
			else{
			    return "ERROR: Fallo el cambio de carpetas intetar de nuevo.";
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


?>
