<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug = true;
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;
//if ($deta_causal and $sector) {
	$isql = "SELECT dcau.SGD_DCAU_CODIGO,dcau.SGD_CAU_CODIGO, dcau.SGD_DCAU_DESCRIP
        FROM  SGD_DCAU_CAUSAL dcau, SGD_CAU_CAUSAL cau
				WHERE dcau.SGD_DCAU_ESTADO=1 AND dcau.SGD_DCAU_DESCRIP like '%$deta_causal%'
        AND cau.SGD_CAU_CODIGO=dcau.SGD_CAU_CODIGO
        AND cau.SGD_CAU_ESTADO=1
        ORDER BY dcau.SGD_DCAU_DESCRIP ";
	$rs = $db->conn->Execute($isql);
	if ($rs && !$rs->EOF) {
	?>
	<?
  $i=1;
  do {
    $codigo_dcau =  $rs->fields[0];
    $codigo_cau =  $rs->fields[1];
    $nombre_dcau =  $rs->fields[2];
    if($ddca_causal==$codigo_ddcau) {
      $datoss = " selected ";
    } else {
      $datoss = " ";
    }
    $temas[$codigo_dcau.'-'.$codigo_cau] = $nombre_dcau;
    $rs->MoveNext();
  }while(!$rs->EOF);
  //}
  }
 echo json_encode($temas);
?>