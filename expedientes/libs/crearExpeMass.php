<?php
session_start();
	set_time_limit(880);
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
	
	$fecha_hoy 		= Date("Y-m-d");
	$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
		
	//Funcion error : Retorna valor para ser leido por el javascript	
	function salirError ($mensaje) {
		$accion		= 	array( 'respuesta' 	=> false,
							   'mensaje'	=> $mensaje);
		print_r(json_encode($accion));
		return;
	}	
	
	//Filtrar caracteres extraños en textos	
	function strValido($string){
		$arr 	= array('/[^\w:()\sáéíóúÁÉÍÓÚ=#\-,.;ñÑ]+/', '/[\s]+/');
		$asu 	= preg_replace($arr[0], '',$string);		
		return    trim(strtoupper(preg_replace($arr[1], ' ',$asu)));		
	}
 
	$mensaje1	= "Faltan parametros para crear los expedientes";
	$mensaje2	= "El expediente que intento crear ya existe.";//Mensaje de informacion	
	$mensaje3	= "Creacion Masiva TRD: ";//Mensaje de informacion
	$mensaje4	= "Se crearon los expedientes";
	$mensaje5	= "Error creando los siguiente expedientes";
	$mensaje6	= "Recargue el contador \nEl primer numero del rango ya existe ";
	
					
	/**
	 *  Crear expedientes de manera masiva
	 */  
	 
	if (empty($nombs_Exp_4) 
		|| empty($rang_ExpeCrear)
		|| empty($nom_depe_4)
		|| empty($rang_iniExpe)
		|| empty($selectSubSerie_4)){
			salirError($mensaje1);
	}		
	
	//cambio del formato de fecha		
	$date1	= Date("d-m-Y");				
	
	//Colocar el expediente como publico o privado
	//enviamos a la base de datos el valor de 1 o null	
	$publ_priv = empty($radPrivado4)? null : 1;
	
	$nombresExp	= explode(";",$nombs_Exp_4);
	$nombresExp = array_filter($nombresExp);
	$selectSubSerie_4 = str_pad($selectSubSerie_4, 3, "0", STR_PAD_LEFT );
	
	foreach ($nombresExp as $nomb){
		
		//busca la secuencia actual con el año seleccionado
		$secExp		= $expediente->secExpediente($nom_depe_4
												,$selectSerie_4
												,$selectSubSerie_4
												,$ano_busq_4); 
		if(!empty($secExp)){
			while(strlen($secExp) < 5){	$secExp = '0'.$secExp;}
		}		
		
		//Numero del expediente si es automatico
		$numExpe = $ano_busq_4.$nom_depe_4.$selectSerie_4.$selectSubSerie_4.$secExp.'E';
		
		
		//Creamos el primer numero envia de expediente.
		//Si no lo crea salimos y mostramos error
		
		if($rang_iniExpe){
			$numExpe = $rang_iniExpe;			
		}
		
		//Expediente,radicado,dependencia seleccionada,documento del 
		//usario que crea el expediente,codigo del responsable, serie,subserie,
		//si el expediente ya existia,fecha seleccionada,no se usa,no se usa, publico privado
		//
		$numeroExpedienteE =
				$expediente->crearExpediente(	$numExpe,
												0,
												$nom_depe_4,
												$codusuario,
												$usua_doc,
												$selectResponsable_4,
												$selectSerie_4,
												$selectSubSerie_4,
												'false',
												"'$date1'",
												0,
												null,
												$publ_priv);
		
		if($rang_iniExpe  && ($numeroExpedienteE==0)){
			salirError($mensaje6.$rang_iniExpe);
			die;
		}else{
			unset($rang_iniExpe);
		}
		
		if($numeroExpedienteE==0) {
			$exp_error 		.= empty($exp_error)? $numExpe : ",$numExpe";						
		} else {				
			
			//Cambiar nombre del proyecto
			
			if(!empty($selectProyecto)){
				$expediente->insert_ProyNomb($numExpe,$selectProyecto);					
			}												
			
			//cambiar el nombre del expediente				
			$insercioNomExp = $expediente->insert_ExpedienteNomb($numExpe, trim(mb_strtoupper($nomb,'utf-8')));	
													
			//Al crear solo vamos a guardar el historico que indica que el
			//expediente entra a la primera etapa del proceso
		
			$observa = $mensaje3.$selectSerie_4."/".$selectSubSerie_4 ."/ Nombre: ".trim(mb_strtoupper($nomb,'utf-8'));
			$radicados[] = 0;
			$tipoTx = 51;
			$Historico->insertarHistoricoExp(	  $numExpe
												, $radicados
												, $depenUsua
												, $codusuario
												, $observa
												, $tipoTx
												, 0);
		}
		fb($i + 1,'var');
	} 
	//si se creo el expediente y sus procesos retornamos el resultado
	if(empty($exp_error)){
		$accion= array( 'respuesta' => true,
						'mensaje'	=> $mensaje4);								
		print_r(json_encode($accion));
	}else{
		salirError($mensaje5.$exp_error);
	}	
?>