<?php
session_start();
	
	$ruta_raiz = "../..";

	//Paremetros get y pos enviados desde la aplicacion origen
	extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);	

	//Confirmar existencia de session
		if(!isset($_SESSION['dependencia']))
			include "$ruta_raiz/rec_session.php";

	include_once	("$ruta_raiz/include/db/ConnectionHandler.php");
	$db 			= new ConnectionHandler("$ruta_raiz");	
		
	include_once	("$ruta_raiz/include/tx/Historico.php");	
	include_once 	("$ruta_raiz/include/tx/Expediente.php");	
	require_once	("$ruta_raiz/FirePHPCore/fb.php");		
	
	
	$Historico 		= new Historico($db);	
	$expediente 	= new Expediente($db);   
	
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	

	header("Content-Type: application/json");
	require_once('../../FirePHPCore/fb.php');	
	
	//Declaracion de la variables comunes a lor archivos de este directorio
	$depenUsua		= $_SESSION['dependencia'];
	$codusuario		= $_SESSION["codusuario"];
	$usua_doc		= $_SESSION["usua_doc"];
	$usua_nomb		= $_SESSION["usua_nomb"];
	$login			= $_SESSION["krd"];
	
	$fecha_hoy 		= Date("Y-m-d");
	$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
		
	//Funcion error : Retorna valor para ser leido por el javascript	
	function salirError ($mensaje) {
		$accion		= 	array( 'respuesta' 	=> false,
							   'mensaje'	=> $mensaje);
		print_r(json_encode($accion));
		exit;
	}
	 
	$mensaje1	= "No selecciono ningun adjunto \n para cambiar el estado";
	$mensaje4	= "Se realizo el cambio";

	if (empty($excluRad)){
		salirError($mensaje1);
	}
	
	//convertir el array en un lista		
	$listAdj	=	implode(",", $excluRad);
	
	//cambiamos el estado					
	$sql1	=	"UPDATE
						SGD_ANEXOS_EXP
						SET ANEXOS_EXP_ESTADO = $accion
					WHERE
						 ANEXOS_EXP_ID in ($listAdj)";
	
	$db->conn->Execute($sql1);	
	
	$sql2	  	=	"select
						DISTINCT( an.SGD_EXP_NUMERO) as EXPEDIENTE
					from
					    SGD_ANEXOS_EXP an
					where
					    an.ANEXOS_EXP_ID in ($listAdj)";
	
	$result	  	= $db->conn->Execute($sql2);	
	
	$numExpe 	= $result->fields['EXPEDIENTE'];
	
	//Al crear solo vamos a guardar el historico que indica que el
	//expediente entra a la primera etapa del proceso
		
	$observa 		= "$usua_nomb cambio el estado del adjunto ($listAdj)";
	$radicados[] = 0;
	$tipoTx = 71;
	$Historico->insertarHistoricoExp(	  $numExpe
										, $radicados
										, $depenUsua
										, $codusuario
										, $observa
										, $tipoTx
										, 0);
											
	$accion= array( 'respuesta' => true,
					'mensaje'	=> $mensaje4);								
	print_r(json_encode($accion));
?>