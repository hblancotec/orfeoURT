<?php
if ( date('N') >=6 ) exit();	//No se ejecuta los fines de semana asi se ejecute vía Web.
##
## La idea es anular los radicados que no hayan concluido su tramite pasados 5 dias desde su radicacion.
## Salidas (1) sin envios o Memorandos (3) sin reasignacion.
##
ini_set('set_time_limit', 0);
ini_set('display_errors', 1);
$dia = 6;
$ruta_raiz = ".";
include_once $ruta_raiz.'/config.php';
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$ADODB_COUNTRECS = true;

// Validamos que no sea festivo el dia de su ejecucion. formato 2016/11/28
$sql = "select count(SGD_FESTIVO) from SGD_DIAS_FESTIVOS where SGD_FESTIVO='".date('Y/m/d')."'";
$ef = $db->conn->GetOne($sql);
if ($ef > 0) exit();

// Obtenemos datos para un futuro historico
$usr = 'SISTEMA';
$sql = "select depe_codi, usua_codi, usua_doc from usuario where usua_login='$usr'";
$rs = $db->conn->Execute($sql);
$duh = $rs->fields('depe_codi');
$uuh = $rs->fields('usua_codi');
$cuh = $rs->fields('usua_doc');

// El query debe hacer los calculo y traer los registros que se van a anular automaticamente
$sql = "select	r.radi_nume_radi as radicado, r.radi_fech_radi, dbo.diashabilestramite(radi_fech_radi, GETDATE()) as dias,
		u.usua_email as mail, u.usua_nomb as nombre, u.usua_login as login, u.depe_codi as depe, u.usua_codi as codi, 
		r.sgd_eanu_codigo
from radicado r
	left join USUARIO u on r.radi_usua_actu=u.USUA_CODI and r.radi_depe_actu=u.depe_codi
	left join sgd_anu_anulados a on a.radi_nume_radi=r.radi_nume_radi 
where
	r.radi_nume_radi not in (select distinct radi_nume_sal from sgd_renv_regenvio) --Que no se haya enviado
	and r.radi_depe_radi not in (900,640) --Que no sea de pruebas ni Contratación
	and r.radi_depe_actu <> 999	--Que no este archivado
	and r.radi_tiporad = 1		--Que sea radicado de salida
	and a.radi_nume_radi is null --Que no este anulado
	and  DATEDIFF(DAY, GETDATE(), r.radi_fech_radi) >= -11
	and  DATEDIFF(DAY, GETDATE(), r.radi_fech_radi) <= -5
UNION
select	r.radi_nume_radi as radicado, r.radi_fech_radi , dbo.diashabilestramite(radi_fech_radi, GETDATE()) as dias,
		u.usua_email as mail, u.usua_nomb as nombre, u.usua_login as login, u.depe_codi as depe, u.usua_codi as codi, 
		r.sgd_eanu_codigo
from radicado r
	left join USUARIO u on r.radi_usua_actu=u.usua_codi and r.radi_depe_actu=u.depe_codi
	left join HIST_EVENTOS h on r.radi_nume_radi=h.radi_nume_radi and h.sgd_ttr_codigo=9
	left join sgd_anu_anulados a on a.radi_nume_radi=r.radi_nume_radi 
where 	r.radi_tiporad = 3	--Que sea memorando
	r.radi_depe_radi not in (900,640) --Que no sea de pruebas ni Contratación
	and r.radi_depe_actu <> 999	--Que no este archivado
	and h.radi_nume_radi is null --Que no haya sido reasignado (and h.sgd_ttr_codigo=9) arriba
	and a.radi_nume_radi is null --Que no este anulado
	and  DATEDIFF(DAY, GETDATE(), r.radi_fech_radi) >= -11
	and  DATEDIFF(DAY, GETDATE(), r.radi_fech_radi) <= -5
order by dias asc, mail, login";
$rs = $db->conn->Execute($sql);
$ADODB_COUNTRECS = false;
if ($rs->RecordCount()>0) {	
	//$db->conn->StartTrans();	// Realizamos un check point.. para efectos de poder probar varias veces la anulacion.
	require_once $ruta_raiz."/anulacion/Anulacion.php";
													 
	$objAnula = new Anulacion($db);
	include_once $ruta_raiz."/include/tx/Historico.php";
	$objHist = new Historico($db);
	
	include_once $ruta_raiz."/include/tx/Expediente.php";
	$objExp = new Expediente( $db );
	
	require_once $ruta_raiz."/envioEmail.php";
	$salir = false;
	
	while(!$rs->EOF) {
		if ( ($rs->fields('dias') == $dia) or ($rs->fields('dias') == ($dia-1)) ) {
			$login = $rs->fields('login');
			$name = $rs->fields('nombre');
			$depe = $rs->fields('depe');
			$codi = $rs->fields('codi');
			$mail = $rs->fields('mail');
			
			$radsAnulados[$login]['nombre'] = $rs->fields('nombre');
			$radsAnulados[$login]['depe'] = $rs->fields('depe');
			$radsAnulados[$login]['codi'] = $rs->fields('codi');
			$radsAnulados[$login]['mail'] = $rs->fields('mail');
			
			if ($rs->fields('dias') == $dia) {
			    $radsAnulados[$login][0][] = $rs->fields('radicado');
			} else {
			    $radsAnulados[$login][1][] = $rs->fields('radicado');
			}
		}
		$rs->MoveNext();
	}
	foreach ($radsAnulados as $login=>$data) {
	    //Vamos generando cuerpo del correo
	    $tmpBody = "Estimado(a) <b>".$radsAnulados[$login]['nombre']."</b> ($login).<br/><br/>";
	    
	    if (count($data[0]) > 0) {
	       // Solicitamos la anulacion de todos los radicados de ese destinatario
	       $objAnula->solAnulacion( $data[0], $duh, $cuh, "Solicitud automática en tarea programada.", $uuh, $db->conn->sysTimeStamp);
	       // Gestionamos historico
	       $objHist->insertarHistorico($data[0], $duh, $uuh, $data['depe'], $data['codi'], 'Anulación automática de radicados en tarea programada.', 26);
	       //Sacamos los radicados anulados de los expedientes donde se encuentre activo.
	       $sql = "select sgd_exp_numero, radi_nume_radi from sgd_exp_expediente where radi_nume_radi in (".implode(',',$data[0]).") and sgd_exp_estado=0";
	       $rse = $db->conn->Execute($sql);
	       while(!$rse->EOF){
	           $resultadoExp = $objExp->excluirExpediente( $rse->fields('radi_nume_radi'), $rse->fields('sgd_exp_numero'));
	           if( $resultadoExp == 1 ) {
	               $observa = "Excluir automaticamente radicado de Expediente.";
	               $tipoTx = 52;
	               $objHist->insertarHistoricoExp( $rse->fields('sgd_exp_numero'), array($rse->fields('radi_nume_radi')), $duh, $uuh, $observa, $tipoTx, 0 );
	           }
	           $rse->MoveNext();
	       }
	       //Ahora aprobamos nosotros mismos automaticamente la solicitud de anulacion
	       $objAnula->apruebaAnulacion($data[0], $duh, $uuh, "Aprobación automática de la anulación de radicado.", $uuh, $objHist);
	       //Creamos el cuerpo del mensaje según haya (uno o varios) o no radicados vencidos.
	       switch (count($data[0])) {
	           case 0: {
	               //Hay el caso en que al usuario actual NO se le vencieron radicados pero SI se levan avencer hoy alguno(s).
	           };break;
	           case 1: {
	               //Se le venció solo un radicado.
	               $tmpBody .= "Fue anulado el radicado ".$data[0][0]." debido a que no fue enviado (para radicado de salida) o reasignado (para memorando) pasado ".($dia-1)." días desde su radicación.<br/><br/>";
	           };break;
	           default: {
	               //Se le vencieron varios radicados.
	               $tmpBody .= "Fueron anulados los radicados ".implode(', ', $data[0])." debido a que no fueron enviados (para los radicados de salida) o reasignados (para los memorandos) pasados ".($dia-1)." días desde su radicación.<br/><br/>";
	           };break;
	        }
	    }
	    if (count(count($data[1]))>0) {
	        //Creamos el cuerpo del mensaje seg&uacute;n haya (uno o varios) o no radicados a vencer el dia de hoy.
	        switch (count($data[1])) {
	            case 0: {
	                //Hay el caso en que al usuario actual NO se le vencieron radicados pero SI se levan avencer hoy alguno(s).
	            };break;
	            case 1: {
	                //Se le vencio solo un radicado.
	                $tmpBody .= "El pr&oacute;ximo d&iacute;a h&aacute;bil ser&aacute; anulado el radicado ".$data[1][0]." sino da tr&aacute;mite el d&iacute;a de hoy.<br/>";
	            };break;
	            default: {
	                //Se le vencieran varios radicados.
	                $tmpBody .= "El pr&oacute;ximo d&iacute;a h&aacute;bil ser&aacute;n anulados los radicados ".implode(', ', $data[1])." sino da tr&aacute;mite el d&iacute;a de hoy.<br/>";
	            };break;
	        }
	    }
	    $cuerpo = str_replace('XYX', $tmpBody, $cuerpoMail);
	    $destino = array($data['mail']);
	    enviarCorreo(null, $destino, null, $cuerpo, "Anulación automática de radicados.");
	}
	//$db->conn->FailTrans();	//Dejamos todo como estaba
	//$db->conn->CompleteTrans();
} else {
}