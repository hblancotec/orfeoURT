<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if (!$ruta_raiz) {
	$ruta_raiz = "..";
}
extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

include("$ruta_raiz/config.php");

if (!isset($_SESSION['dependencia'])) {
	include "$ruta_raiz/rec_session.php";
}

	/**
	 * Created on 30/07/2009
	 *
	 * Los elementos de interaccion con el usuario se encuentran
	 * en el archivo incluirExpedientes.js y en la plantilla
	 * asociada a este script estan los elementos de presentacion
	 * (insertarExpedientes.tpl)
	 */

	 /**@filesource confSmarty.php incluye la ruta raiz definida como
	 * $ruta_raiz = "../";
	 */

	include_once	("confSmarty.php");
	include_once	("$ruta_raiz/include/db/ConnectionHandler.php");
	
	//Inicia adodb
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	$fecha_hoy 	= Date("d-m-Y");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
	
	//Confirmar existencia de session

	$dependencia 	= trim($_SESSION['depecodi']);
	
	/**
	 * Variables enviadas por _GET
	 */

	$usua_login	= trim($_GET['krd']);
	$num_exped	= trim($_GET['num_expediente']);
	$numrad		= trim($_GET['numrad']);

	// variable para convertir y mostrar el tipo documental
	$ent = substr($numrad,-1);


	/**
	 * tipos documentales que pertenecen al expediente
	 * se filtran y se pasan a un variable para ser mostradas
	 * como un select en la presentacion.
	 */


	$query2 = "	SELECT	SGD_SRD_CODIGO 	as serieExp,
						SGD_SBRD_CODIGO as subSerie,
						DEPE_CODI		as depenExp
				FROM	SGD_SEXP_SECEXPEDIENTES
				WHERE	SGD_EXP_NUMERO like '$num_exped'";

	$rsQuery2	= $db->conn->Execute($query2);
	$serieExp	= $rsQuery2->fields["serieExp"];
	$subSerie	= $rsQuery2->fields["subSerie"];
	$depenExp	= $rsQuery2->fields["depenExp"];



	$queryTD	= "	SELECT	distinct (t.sgd_tpr_descrip) as nombre
							, t.sgd_tpr_codigo as codigo
      				FROM	sgd_mrd_matrird m ,
							sgd_tpr_tpdcumento t,
							sgd_sbrd_subserierd su
      				WHERE	m.depe_codi = '$depenExp'
							and m.sgd_srd_codigo = '$serieExp'
							and m.sgd_sbrd_codigo = '$subSerie'
							and m.sgd_tpr_codigo = t.sgd_tpr_codigo
							and m.sgd_mrd_esta = 1
							and GETDATE() between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
							and su.sgd_srd_codigo = m.sgd_srd_codigo
							and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo order by nombre";

	$rsTip	= $db->conn->Execute($queryTD);

	while(!$rsTip->EOF) {
		$codigo 					= $rsTip->fields["codigo"];
		$nombre 					= $rsTip->fields["nombre"];
		$tipoDocumental[$codigo]	= htmlentities($codigo." - ".$nombre);
		$rsTip->MoveNext();
	}

	$salir = (empty($tipoDocumental[$codigo]))? 1 : 0 ;
	
	
	//Mostrar los adjuntos actuales y permitir seleccionar y borrar
	//aquellos que el usuario creador quiera
	
	$consulta = "select
      				a.ANEXOS_EXP_NOMBRE 	AS NOMBRE,
					a.ANEXOS_EXP_ID 		AS ID,	
					a.USUA_LOGIN_CREA		AS LOGIN_CREA,				
					(CASE  WHEN a.ANEXOS_EXP_ESTADO = 1 THEN ('INACTIVO') ELSE ('ACTIVO') END) AS ESTADO					
				FROM
    				SGD_ANEXOS_EXP a,
    				SGD_TPR_TPDCUMENTO b
				WHERE
     				SGD_EXP_NUMERO = '$num_expediente'
     				AND a.SGD_TPR_CODIGO = b.SGD_TPR_CODIGO					
     			order by a.ANEXOS_EXP_ID, a.ANEXOS_EXP_FECH_CREA";

	$adjun 	=	$db->conn->Execute($consulta);
	
	
	while(!$adjun->EOF){		
		$nombre		 	= $adjun->fields['NOMBRE'];
		$id			 	= $adjun->fields['ID'];
		$estado		 	= $adjun->fields['ESTADO'];
		$login_crea	 	= $adjun->fields['LOGIN_CREA'];
		
		if($usua_login == $login_crea){
			$adj_exp_edit[$id]	= array('nombre' => $nombre,'estado' => $estado);
		}else{
			$adj_exp_bloc[$id]	= array('nombre' => $nombre,'estado' => $estado,'login' => $login_crea);
		}
				
		$adjun->MoveNext();		
	};
	
	if ($ocultaDocElectronico == 1) {
	    $oculta = "";
	} else {
	    $oculta = "none";
	}
	/**
	 * El resultados de la logica se coloca en las variables
	 * de la plantilla adjuntarArchivo.tpl
	 */

	$smarty->assign("salir"	               , $salir			    );
	$smarty->assign("dependencia"		   , $dependencia		);
	$smarty->assign("usua_login"		   , $usua_login		);
	$smarty->assign("num_exped"			   , $num_exped		    );
	$smarty->assign("numrad"			   , $numrad			);
	$smarty->assign("tipoDocumental"	   , $tipoDocumental	);
	$smarty->assign("adj_exp_edit"		   , $adj_exp_edit		);
	$smarty->assign("adj_exp_bloc"		   , $adj_exp_bloc		);
	$smarty->assign("oculta"               , $oculta );
	
	
	$smarty->display('adjuntarArchivo.tpl');

?>

