<?php
ini_set('set_time_limit', 0);
ini_set('display_errors', 1);
echo "iniciamos Hollman .. ".date('Ymd H:i:s'). "<br/>";
$ruta_raiz = "..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");

$sql = 
"select r1.radi_nume_radi, r1.radi_fech_radi, iif(len(ltrim(r1.radi_path))>0,substring(r1.radi_path, charindex('.',r1.radi_path),len(r1.radi_path)),'-') as radi_path, r1.radi_tiporad, 
r1.radi_depe_radi, r1.radi_usua_radi, r1.sgd_eanu_codigo, d1.depe_nomb, u1.usua_nomb, ra_asun  
from RADICADO r1 left join DEPENDENCIA d1 on r1.radi_depe_radi=d1.depe_codi left join usuario u1 on r1.radi_depe_radi=u1.depe_codi and r1.radi_usua_radi=u1.usua_codi 
where r1.RADI_TIPORAD in (1,3,6) and year(r1.radi_fech_radi)=2019 and month(r1.radi_fech_radi)=11
order by 2";
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->conn->Execute($sql);
echo "Radicado,Fecha_Radicacion,Ruta,RespRapi,Masiva,Gesproy,Suifp,Envio,FormaEnvio,Reasignado,EstadoAnulacion,Dependencia,UsuarioRadicador". "<br/>";
while ($arr = $rs->FetchRow()) {
	switch($arr['radi_tiporad']){
    case 1: {
		$esRespRapi = (substr($arr['ra_asun'], 0, 25)=='Respuesta al radicado No.') ? 1 : 0;
		$haReasigna = 0;
		}break;
    case 3: {
		$sql = "select distinct(radi_nume_radi) from HIST_EVENTOS where radi_nume_radi=".$arr['radi_nume_radi']." and SGD_TTR_CODIGO in (6,9,63)";
		$haReasigna = ($db->conn->GetOne($sql) > 0) ? 1 : 0;
        $esRespRapi = 0;
		} break;
    case 9: {
        $esRespRapi = 0;
		$haReasigna = 0;
		}break;
	}
	$sql = "select iif(count(radi_nume_sal)>0, 1, 0) from SGD_RENV_REGENVIO where radi_nume_sal = ".$arr['radi_nume_radi']." and left(sgd_renv_observa,12)='Masiva grupo'";
        //echo $sql."<br/>";
	$esMasiva = ($db->conn->GetOne($sql) > 0) ? 1 : 0;
        
    $esGesproy = ($arr['radi_depe_radi']==444 && $arr['radi_usua_radi']==150) ? 1: 0;
	
    $sql = "select distinct(radi_nume_radi) from HIST_EVENTOS where radi_nume_radi=".$arr['radi_nume_radi']." and SGD_TTR_CODIGO=64 and HIST_OBSE like '%suiffp%'";

    $esSuiffp = ($db->conn->GetOne($sql) > 0) ? 1 : 0;
        
	$sql = "select top 1 fe.sgd_fenv_descrip from SGD_RENV_REGENVIO de left join sgd_fenv_frmenvio fe on de.sgd_fenv_codigo=fe.sgd_fenv_codigo where de.radi_nume_sal=".$arr['radi_nume_radi'];
	$tmpFE = $db->conn->GetOne($sql);
    $esEnviado = strlen(trim($tmpFE)) > 0 ? 1 : 0;
	$formaEnvio = strlen(trim($tmpFE)) > 0 ? trim($tmpFE) : '-' ;
		
	$anulacion = ($arr['sgd_eanu_codigo']) ? $arr['sgd_eanu_codigo'] : 0;
	$dependencia = ($arr['depe_nomb']) ? str_replace(',', ' ', $arr['depe_nomb']) : '-';
	$usrRadicador = ($arr['usua_nomb']) ? $arr['usua_nomb'] : '-';
	
	echo $arr['radi_nume_radi'].",". $arr['radi_fech_radi'].",".$arr['radi_path'].",". $esRespRapi.",".$esMasiva .",". $esGesproy.",".$esSuiffp.",".$esEnviado.",".$formaEnvio.",".$haReasigna.",".$anulacion.",".$dependencia.",".$usrRadicador."<br/>";
}
echo "Finalizamos Hollman .. ".date('Ymd H:i:s'). "<br/>";
?>