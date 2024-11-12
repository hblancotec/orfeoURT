<?php 
/**
 * cambiarImagenRad es una funcion que permite almacenar cualquier tipo de archivo en el lado del servidor
 * @param $bytes 
 * @param $filename es el nombre del archivo con que queremos almacenar en el servidor
 * @author Donaldo Jinete
 * @author Hardy Nino
 * @return Retorna un String indicando si la operacion fue satisfactoria o no
 */

function cambiarImagenRad($radinum,$ext,$file,$hist='S'){
	if (strlen( $radinum) == "14") {
        	global $ruta_raiz;       
		$actualiza=Null;
		$rutaArch=Null;
		$year=substr($radinum,0,4);
		$depe=substr($radinum,4,3);
		//consulta si el radicado existe
		$radinums[0]=$radinum;
		$consultaRad ="select radi_nume_radi,radi_path,radi_usua_actu, radi_depe_actu,radi_nume_deri  from radicado where radi_nume_radi=".$radinum;
		$db = new ConnectionHandler($ruta_raiz,'WS');
		$rs1=$db->conn->Execute( $consultaRad );
		$coddepe=$rs1->fields[3];
		$radi_usua_actu=$rs1->fields[2];	//['radi_usua_actu'];
		//return $rs1->fields[1];;
		if($rs1->fields[4]!= null) {  $radi_nume_deri=$rs1->fields[4];}
		else { $radi_nume_deri=$radinum; }
		if(!$rs1->EOF){	
			$rutaArch=$rs1->fields[1];
			if($rutaArch){
				$rutaOld=$rutaArch;
				$extReg=substr($rutaArch,-1,3);
				$Backup="../".$rutaArch.".old".$extReg;
				exec("mv ../".$rutaArch." ".$Backup);
				$rutaArch="/$year/$depe/docs/$radinum.$ext";
				//if($ext==$extReg) 
				 $actualiza="si";
				$ruta="../bodega/".substr($radi_nume_deri,0,4)."/".substr($radi_nume_deri,4,3)."/docs/";
			}
			else{
				$rutaArch="/$year/$depe/docs/$radinum.$ext";
				$ruta="../bodega/".substr($radi_nume_deri,0,4)."/".substr($radi_nume_deri,4,3)."/docs/";
		     	$actualiza="si";
			}

			$validar=UploadFile($file,$radinum.".".$ext);
			if($validar=='exito'){
				$update="UPDATE RADICADO SET RADI_PATH='$rutaArch' where RADI_NUME_RADI=".$radinum;
			        if($actualiza=="si"){ $res=$db->conn->Execute($update);}
				include_once ($ruta_raiz.'include/tx/Historico.php');
				$hist = new Historico($db) ;
				if($hist=="S"){
			    	$radinums[0]=$radinum;
				$hist->insertarHistorico($radinums,  $coddepe , $radi_usua_actu, $coddepe, $radi_usua_actu,"Modificacion de Imagen webservice ", 23);
				}
			    $comando="cp -f ../bodega/$rutaArch $ruta/$radinum.$ext" ;
			    if($rs1->fields[4]!= null){
			         exec($comando);
			    }
				return "OK";
			}else{
				exec("mv  ".$Backup." ../".$rutaold);
				throw new Exception("ERROR: no se puede copiar el archivo");
			}
		}
		else{
			return "ERROR: El radicado no existe";
		}
		
	}
	else{
	    return "ERROR: El numero de radicado es encuentra incompleto. ";
	}

}

?>