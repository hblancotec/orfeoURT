<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
header('Content-type: text/html; charset=utf-8');
$ruta_raiz = "..";
include "$ruta_raiz/config.php";
if (!isset($_SESSION['dependencia'])) include "$ruta_raiz/rec_session.php";

	/**
	 * Los combobox y demas elementos de presentacion
	 * se encuentran en el archivo adm_nombreTemasExp.js
	 * y en la plantilla asociada a este script
	 * (adm_nombreTemasExp.tpl)
	 */
	
	// Parametros de configuracion de smarty	
	include_once "confSmarty.php";	
	// conexion a la base de datos
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";	
	
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;

	$perm_temas_exp 	= $_SESSION['usua_perm_tem_exp'];	
	$depe_cod 			= $_SESSION['depecodi'];
	$cod_usua			= $_SESSION["codusuario"];
	$usua_doc			= $_SESSION["usua_doc"];
	$depe_nom	 		= strtoupper($_SESSION['depe_nomb']);
	$ano_busq			= date("Y");
	$select_depen 		= 'false';
	
	/**
	 * Permisos para seleccionar una dependencia o varias
	 * 1 = solo la dependencia
	 * 2 = dependencia padre
	 * 3 = todas las dependencias
	 */		
	 
	if($perm_temas_exp > 1){		
		if($perm_temas_exp == 2){
			$searh_busq	= "	WHERE	DE.DEP_CENTRAL = (	SELECT	DE.DEP_CENTRAL
														FROM	DEPENDENCIA DE
														WHERE	DE.DEPE_CODI = $depe_cod)"; 
    	
		}elseif($perm_temas_exp == 3){
			$searh_busq	= "";
		}		
		
		$sql_depe = "SELECT	DE.DEPE_CODI AS CODIGO, RIGHT('0000' + convert(varchar,DE.DEPE_CODI), 4)+ ' - ' + DE.DEPE_NOMB AS NOMBRE
					 FROM	DEPENDENCIA DE $searh_busq";
					
		$result_dep = $db->conn->Execute($sql_depe);
				
		//$depeArray[0] = 'Seleccione';
		while(!$result_dep->EOF){
			$depeArray[$result_dep->fields["CODIGO"]]=
				trim($result_dep->fields["NOMBRE"]);							
			$result_dep->MoveNext();			
		}
		$select_depen = 'true';
				
	}elseif($perm_temas_exp == 1){
		$depeArray = $depe_nom;
	}elseif($perm_temas_exp == 0){
		die;
	}	
	
	/**
	 * Mostar el select para proyectos si el
	 * usuario tiene permisos
	 */	
	
	$sql3	= "	SELECT	P.SGD_EPRY_CODIGO as CODIGO, 
						P.SGD_EPRY_NOMBRE +' *** 
						'+ SGD_EPRY_NOMBRE_CORTO as NOMBRES_PROY
				FROM	SGD_EPRY_EPROYECTO P
				WHERE	P.DEPE_CODI = $depe_cod
				ORDER BY 1";
			
	$result = $db->conn->Execute($sql3);
				
	while($result && !$result->EOF){
		$proyArray[$result->fields["CODIGO"]]=
			trim($result->fields["NOMBRES_PROY"]);							
		$result->MoveNext();
	}
	
	$mosProy = empty($proyArray)?  'false' : 'true';	
	
	/**
	 * @var $ano_busq select de años para filtrar busqueda
	 * se realiza desde el año actual hasta 10 años antes  
	 */
	
	for($i=0; $i< 62; $i++){
		$anoArray[$ano_busq - $i]= $ano_busq - $i;		
	}
	
	/**
	 * Consulta de la serie
	 * Con la selecion de una serie se buscaran las respectivas 
	 * subserie y tipo documental para crear el expediente.
	 * Esta busqueda inicia con el filtro realizado por la dependencia
	 * 
	 */
	
	$fecha_hoy 		= 	Date("d-m-Y");
	$sqlFechaHoy	=	$db->conn->DBDate($fecha_hoy);

	$sql1			=	"SELECT	DISTINCT (RIGHT('0000' + convert(varchar,S.SGD_SRD_CODIGO), 4)+' - '+S.SGD_SRD_DESCRIP + ' - ' + CASE WHEN M.SGD_MRD_ESTA = 0 THEN '(INACTIVA)' WHEN M.SGD_MRD_ESTA = 1 THEN '(ACTIVA)' END)AS DETALLE,
								RIGHT('0000' + convert(varchar,S.SGD_SRD_CODIGO), 4) AS CODIGO_SERIE,
								M.SGD_MRD_ESTA
						 FROM	SGD_MRD_MATRIRD M
								JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO AND S.SGD_SRD_CODIGO > 0
						 WHERE	M.DEPE_CODI = '$depe_cod' AND
								M.SGD_SRD_CODIGO > 0 --AND M.SGD_MRD_ESTA = 1  
                                and ".$sqlFechaHoy." BETWEEN s.SGD_SRD_FECHINI AND s.SGD_SRD_FECHFIN AND
								M.SGD_MRD_CODIGO = (SELECT	TOP(1) M2.SGD_MRD_CODIGO 
													FROM	SGD_MRD_MATRIRD M2 
													WHERE	M2.DEPE_CODI = '$depe_cod' AND 
															M2.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
													ORDER BY M2.SGD_MRD_ESTA DESC)
			      		 ORDER BY M.SGD_MRD_ESTA DESC, DETALLE";

	$rs				=	$db->conn->Execute($sql1);
	while($rs && !$rs->EOF){
		$serieArray[$rs->fields["CODIGO_SERIE"]] = $rs->fields["DETALLE"];
		$rs->MoveNext();				
	}	
	
	//consulta para retornar los usuarios que  
	//pertenecen a esta depedencia
	$sql1 = "	SELECT	US.USUA_NOMB AS DETALLE,
						US.USUA_DOC AS CODIGO_USUARIO	
				FROM	USUARIO US
				WHERE	US.DEPE_CODI = '$depe_cod'
				ORDER BY DETALLE";
	
	$salida=$db->conn->Execute($sql1);
				
	while($salida && !$salida->EOF) {				
		$usuarios[$salida->fields["CODIGO_USUARIO"]]= $salida->fields["DETALLE"];
		$salida->MoveNext();
	}	
	
	$smarty->assign("sid"			,SID);					//Envio de session por get	
	$smarty->assign("anoArray"		,$anoArray);			//Año actual
	$smarty->assign("serieArray"	,$serieArray);			//Seleccionar una serie
	$smarty->assign("select_depen"	,$select_depen);		//Informa a javascript si existe el select o no	
	$smarty->assign("depe_cod"		,$depe_cod);			//Codigo de la dependencia que pertenece el usuario
	$smarty->assign("depe_nom"		,$depe_nom);			//Nombre de la dependencia a la que pertenece el usuario
	$smarty->assign("perm_temas_exp",$perm_temas_exp);		//permisos para mostrar dependencia 						
	$smarty->assign("mosProy"		,$mosProy);				//permisos para que mostrar o no proyectos
	$smarty->assign("depeArray"		,$depeArray); 			//dependencia a la cual se le editaran los expedientes
	$smarty->assign("proyArray"		,$proyArray); 			//proyectos para seleccionar
	$smarty->assign("veractivos"	,0);					//Ver TODAS las dependencias y series.
	$smarty->assign("usuarios"		,$usuarios); 			//proyectos para seleccionar
	$smarty->assign("usuadoc"		,$usua_doc); 			//cedula del usuario
	$smarty->assign("codusua"		,$cod_usua); 			//codigo del usuario
	$smarty->assign("depecodi"		,$depe_cod);			//dependencia del usuario	
	
	$smarty->assign("krd"		,$krd);			//recarga de session con el krd 

	$smarty->display('adm_Expedientes.tpl');
?>