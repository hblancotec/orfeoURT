<?


//notificacion      resolucion,tipoNotificacion,fechaNotificacion,fechaFijacion,fechaDesfijacion,numeroEdicto,notificador,notificado,accion
function notificar($resolucion,$tipoNotificacion=Null,$fechaNotificacion=Null,$fechaFijacion=Null,$fechaDesfijacion=Null,$numeroEdicto=Null,$notificador,$notificado,$accion){
	if (strlen( $resolucion) == "14" && substr($resolucion, -1, 1) == "5" ) {
        	global $ruta_raiz;       
		if($notificador==Null) return "ERROR: falta el notificador";
		if($notificado==Null) return "ERROR: falta el notificado";
		//consulta si el radicado existe
		$consultaRad ="select radi_nume_radi,radi_usua_actu, radi_depe_actu,radi_fech_radi  from radicado where radi_nume_radi=".$resolucion;
		$db = new ConnectionHandler($ruta_raiz,'WS');
		$rs1=$db->conn->Execute( $consultaRad );
		$codDepe=$rs1->fields[2];
		$radi_usua_actu=$rs1->fields[1];
		$fecha_radicado=$rs1->fields[3];
		if(!$rs1->EOF){
			$sqlExiste="select * from SGD_NTRD_NOTIFRAD where radi_nume_radi = $resolucion";
			if($accion=="N"){
			require_once("$ruta_raiz/class_control/Notificacion.php");
				//P->personal 1 ;E-> edicto 3 ;C-> conducta concluyente 4
				//numero edicto fecha de des y fijacion
				if($tipoNotificacion=="P" || $tipoNotificacion=="C"){ 
					if($tipoNotificacion=="P") $codNot=1;
					if($tipoNotificacion=="C") $codNot=4;
//return $fecha_radicado." ". $fechaNotificacion." ".compararFecha($fecha_radicado, $fechaNotificacion);
					if($tipoNotificacion==Null) return "ERROR: falta la fecha de notificacion";
					if(compararFecha($fecha_radicado, $fechaNotificacion)=="Radi") return "ERROR: la fecha de notifijacion es menor a la de radicacion" ;
					$sql="insert into SGD_NTRD_NOTIFRAD (radi_nume_radi,sgd_not_codi,SGD_NTRD_NOTIFICADOR, SGD_NTRD_NOTIFICADO,SGD_NTRD_FECHA_NOT,SGD_NTRD_OBSERVACIONES) values (".$resolucion.",".$codNot.",'".$notificador."','".$notificado."',TO_DATE('".$fechaNotificacion."','DD-MM-YYYY'),'Noficado webservices JBPM')";  
				}
				if($tipoNotificacion=="E"){ 
					$codNot=3;
					if($fechaDesfijacion==Null) return "ERROR: falta la fecha de desfijacion";
					if($fechaFijacion==Null) return "ERROR: falta la fecha de fijacion";
					if(compararFecha($fecha_radicado, $fechaFijacion)=="Radi") return "ERROR: la fecha de fijacion es menor a la de radicacion" ;
					if(compararFecha($fecha_radicado, $fechaDesfijacion)=="Radi") return "ERROR: la fecha de desfijacion es menor a la de radicacion" ;
					if($numeroEdicto==Null) return "ERROR: falta el numero de edicto";
					$sql="insert into SGD_NTRD_NOTIFRAD (radi_nume_radi,sgd_not_codi,SGD_NTRD_NOTIFICADOR,SGD_NTRD_NOTIFICADO, SGD_NTRD_FECHA_FIJA,SGD_NTRD_FECHA_DESFIJA,SGD_NTRD_OBSERVACIONES,SGD_NTRD_NUM_EDICTO)
					values (".$resolucion.",".$codNot.",'".$notificador."','".$notificado."',TO_DATE('".$fechaFijacion."','DD-MM-YYYY'),TO_DATE('".$fechaDesfijacion."','DD-MM-YYYY'),'Noficado webservices JBPM',".$numeroEdicto.")";
				}
				$rs=$db->conn->Execute($sqlExiste);
				//No se ha insertado notificacion todavia
				if (!$rs || $rs->EOF){
					$swInsertado = true;

					$rs4=$db->conn->Execute($sql);
					if (!$rs4){
						  return "ERROR: TRATANDO DE INSERTAR EL REGISTRO DE NOTIFICACION ";
					}else{
						$notifDesc="Notifico";
					}
				}else{
					return "ERROR: Ya ha sido Notificado";
				}
			}
			elseif($accion=="R"){
			//ojo  el  historico.....
				$rs=$db->conn->Execute($sqlExiste);
				if (!$rs || $rs->EOF){	return "ERROR: No esta notificado";	}
				ELSE{
					$consultaRad ="delete from sgd_ntrd_notifrad where radi_nume_radi=".$resolucion;
					$rs3=$db->conn->Execute( $consultaRad );
					if($rs3->EOF){	 $notifDesc=" Reverso la notificacion";  } 
					else {	return "ERROR: No se reverso "; }
				}
			}
			else {  return "ERROR:  debe definir la accion N = Notificar o R = Reversar";  }
				include_once ($ruta_raiz.'include/tx/Historico.php');
				$hist = new Historico($db) ;
				$radinums[0]=$resolucion;
				$hist->insertarHistorico($radinums,$codDepe,$radi_usua_actu,$codDepe,$radi_usua_actu,"$notifDesc", 36);
				return "OK";
		}
		else{ return "ERROR: El radicado no existe."; }
	}
	else{ return "ERROR: El numero de radicado es encuentra incompleto o No es una resolucion "; }
}

//comparar fecha para definir si se puede notificar noti-> notifica iguales->notifica Radi -> no deja notificar

function compararFecha($fechaRad, $fechaNot){
$fechaA = explode("-", $fechaRad);
$fechaB = explode("/", $fechaNot);
$fechaC=mktime(0, 0, 0, $fechaA[1], $fechaA[2], $fechaA[0]);
$fechaD=mktime(0, 0, 0, $fechaB[1], $fechaB[0], $fechaB[2]);
if($fechaC==$fechaD) return "iguales";
if($fechaC>$fechaD) return "Radi";
if($fechaC<$fechaD) return "Noti";

}

?>
