<?


function validaRadicado( $expnum, $radinum ){
        
	if (strlen( $radinum) == "14") {
        	global $ruta_raiz;       
		//consulta si el expediente existe
		$consultaExp ="select sgd_exp_numero from sgd_sexp_secexpedientes where sgd_exp_numero='$expnum'";
		//consulta si el radicado existe
		$consultaRad ="select radi_nume_radi,sgd_eanu_codigo from radicado where radi_nume_radi=$radinum";
		//consulta si tiene relacion
		$consultaExpxRad = "select radi_nume_radi, sgd_exp_numero, sgd_exp_estado from sgd_exp_expediente where radi_nume_radi=$radinum  and sgd_exp_numero='$expnum'";
		$db = new ConnectionHandler($ruta_raiz,'WS');
		$rs=$db->conn->Execute( $consultaExp );
		if($rs->EOF) $error.=" - No existe el Expediente -";

		$rs1=$db->conn->Execute( $consultaRad );
		if($rs1->EOF){
			$error.=" - No existe el radicado - ";
		}
 		else { $radiEstado=$rs->fields['sgd_eanu_codigo'];
			if ($radiEstado==1  ) $error.=" - El radicado Esta en solicitud de Anulacion -";
			elseif ($radiEstado==2  ) $error.=" - El radicado Esta  Anulado -";
			elseif(!$rs1->EOF && !$rs->EOF ){
			$rs2=$db->conn->Execute( $consultaExpxRad );
			    if(!$rs2->EOF) { $respuesta="OK"; }
			    else{ $error.="- El numero de radicado no esta contenido en el expediente suministrado -"; }
			}
			else {  $error.=" - No se puede consultar relacion -";     }
		}
	}
	else{  $error=" El numero de radicado esta incompleto";  }
	if($respuesta !="OK" ){ $respuesta="ERROR: ".$error; }
	return $respuesta;
}

?>
