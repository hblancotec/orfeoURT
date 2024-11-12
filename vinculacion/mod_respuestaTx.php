<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET);
    extract($_POST);
    extract($_SESSION);
}

if (!$ruta_raiz) {
	$ruta_raiz= "..";
}

include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
include_once "$ruta_raiz/include/tx/Historico.php";
$objHistorico= new Historico($db);
$arrayRad = array();
$arrayRad[]=$verrad;
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
if (is_array($recordSet) && count($recordSet)>0) {
	array_splice($recordSet, 0);
}

if (is_array($recordWhere) && count($recordWhere)>0){
	array_splice($recordWhere, 0);
}

if ($borrar) {		
	$isqlM ="SELECT RADI_NUME_RADI,
					RADI_RESPUESTA 
			FROM 	RADICADO
			WHERE 	RADI_NUME_RADI = $verrad";
	$rsM=$db->conn->Execute($isqlM);
	$numRadiBusq = $rsM->fields["RADI_NUME_RADI"];
	if($numRadiBusq != ''){
		$radiDeriAnte = $rsM->fields["RADI_RESPUESTA"];
		
		if (is_array($recordSet) && (count($recordSet)>0) )
			 array_splice($recordSet, 0);  		
	   	
		if (is_array($recordWhere) && (count($recordWhere)>0) ) {
			array_splice($recordWhere, 0);
		}
		$recordSet["RADI_RESPUESTA"] = "NULL";
		
		$recordWhere["RADI_NUME_RADI"] = $verrad;	  
	    $ok = $db->update("RADICADO", $recordSet,$recordWhere);
	    array_splice($recordSet, 0);  		
	    array_splice($recordWhere, 0);	  
	    
		if ($tipVinDocto==0) {
			$detaTipoVin = "Anexo de";
		}
		
		if ($tipVinDocto==2){
			$detaTipoVin = "Asociado de";
		}
		  
	    if($ok){   
			$mensaje = "<hr><center><b><span class=info>respuesta Eliminada</span></center></b></hr>";
			$observa = "Se elimino la respuesta vinculada Radicado No. $radiDeriAnte";
			$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);	
		    $objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 99);
		}
		else{
			$mensaje = "<hr><center><b>
				<span class=info>No se Pudo Eliminar la Vinculaci&oacute;n Documento </span>
			</center></b></hr>";
		}
	}
	else {
		$mensaje = "<hr><center><b><span class=info>N&uacute;mero de Radicado Inexistente</span></center></b></hr>";
	}
}
?>
	</script>
	<body bgcolor="#FFFFFF" topmargin="0">
		<br>
		<div align="center"> 
			<p> <?=$mensaje?> </p>
			<input type='button' value='Cerrar' class='botones_largo' onclick='opener.regresar();window.close();'>
		</div>
	</body>
</html>