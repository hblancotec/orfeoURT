<?php
ini_set('display_errors','1');
error_reporting(-1);

define('ADODB_ASSOC_CASE', 1);
$ADODB_COUNTRECS = true;
$y=0;

require "./config.php";
include 'adodb/adodb.inc.php';
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$sql = "select ANEX_RADI_NUME from ANEXOS where anex_desc='Archivo cargado via Web.' group by ANEX_RADI_NUME";
	$rs1 = $conn->Execute($sql);
	while(!$rs1->EOF) {
		$rad = $rs1->fields['ANEX_RADI_NUME'];
		$sql = "select ANEX_CODIGO from ANEXOS where ANEX_RADI_NUME = $rad order by ANEX_FECH_ANEX desc";
		echo $y++."<br>";
		$rs2 = $conn->Execute($sql);
		$cnt = $rs2->RecordCount();
		while(!$rs2->EOF) {
		    $conn->StartTrans();
			$sql1 = "UPDATE ANEXOS SET ANEX_SOLO_LECT=UPPER(ANEX_SOLO_LECT),ANEX_CODIGO=LEFT(ANEX_CODIGO,14)+'".str_pad($cnt, 5, 0, STR_PAD_LEFT)."', ANEX_NUMERO=".$cnt." WHERE ANEX_CODIGO='".$rs2->fields['ANEX_CODIGO']."'";
			$conn->Execute($sql1);
			$sql2 = "UPDATE HIST_EVENTOS_ANEXOS SET ANEX_CODIGO=LEFT(ANEX_CODIGO,14)+'".str_pad($cnt, 5, 0, STR_PAD_LEFT)."' WHERE ANEX_CODIGO='".$rs2->fields['ANEX_CODIGO']."'";
			$conn->Execute($sql2);
			$sql3 = "UPDATE SGD_HIST_IMG_ANEX_RAD SET ANEX_CODIGO=LEFT(ANEX_CODIGO,14)+'".str_pad($cnt, 5, 0, STR_PAD_LEFT)."' WHERE ANEX_CODIGO='".$rs2->fields['ANEX_CODIGO']."'";
			$conn->Execute($sql3);
			$ok = $conn->CompleteTrans(true) ? "<font color=green>SIP</font>" : "<font color=red>NO</font>";
			
			echo "$sql1 <br> $sql2 <br> $sql3 $ok <br/>";
			$rs2->MoveNext();
			$cnt--;
		}
		$rs1->MoveNext();
	}
	
} else {
	echo "No hay ocnexion a BD";
}
?>