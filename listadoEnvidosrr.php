<?php
set_time_limit(0);
$ruta_raiz = ".";
require $ruta_raiz."/config.php";
require $ruta_raiz."/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$year2val = $_GET['anho'];
$month2val  = $_GET['mes'];
echo date('Y-m-d H:i:s')."<br />";
$sqlr = "select RADI_NUME_RADI as radi_salida, r.radi_fech_radi as fecha_radicacion, d.SGD_TPR_DESCRIP as tipo_documental, ".
		"RA_ASUN as asunto, r.radi_nume_deri as radi_padre ".
		"from radicado r ".
		"left join SGD_TPR_TPDCUMENTO d on r.TDOC_CODI=d.SGD_TPR_CODIGO ".
		"where radi_tiporad=1 and RA_ASUN like 'Respuesta al radicado No.%' and year(r.radi_fech_radi)=$year2val and month(r.radi_fech_radi)=$month2val ".
		"order by radi_fech_radi";
$ADODB_COUNTRECS=true;
$rsr = $db->conn->Execute($sqlr);

//Data Source=vsinergiadb;Database=Configuracion;User ID=sinergiaweb;Password=M(tBQ3Ary1Z;
$se= "vsinergiadb";$be="Configuracion";$ue="sinergiaweb";$pe="M(tBQ3Ary1Z";
$dsn = 'mssqlnative://sinergiaweb:M(tBQ3Ary1Z@vsinergiadb/Configuracion?persist';  # persist is optional
$conx = ADONewConnection($dsn);  # no need for Connect/PConnect
$conx->SetFetchMode(ADODB_FETCH_ASSOC);

if ($rsr && $rsr->RecordCount()>0) {
	while ($row_r = $rsr->FetchRow()) {
	    $sqle = "SELECT * FROM [Configuracion].[dbo].[EMAIL_AUDITORIA] where USUARIO = 'ORFEOUser' and parametros like '%".$row_r['radi_salida']."%' order by fecha; ";
	    $rse = $conx->Execute($sqle);
	    $cntEnvios = $rse->RecordCount();
	    echo $row_r['radi_padre']."|".$row_r['fecha_radicacion']."|".$row_r['tipo_documental']."|".$row_r['radi_salida']."|".$cntEnvios."<br />";
	}
} else {
    echo "Registros no hayados para el a&ntilde;o $year2val";
}

$ADODB_COUNTRECS=false;
echo date('Y-m-d H:i:s')."<br />";
?>