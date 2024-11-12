<?php
echo date("Ymd H:i:s");
$ruta_raiz = ".";
require "$ruta_raiz/config.php"; 		// incluir configuracion.
require 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
$conn = NewADOConnection($dsn);
$msg = "";

if ($conn){
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $sql = "select	m.id_cont as idc, c.nombre_cont as nombrec, m.id_pais as idp, p.nombre_pais as nombrep,
		m.dpto_codi as idd, d.dpto_nomb as nombred, m.muni_codi as idm, m.muni_nomb as nombrem
        from municipio m
        inner join departamento d on m.dpto_codi=d.dpto_codi and m.id_pais=d.id_pais
        inner join sgd_def_paises p on d.id_pais=p.id_pais and d.id_cont=p.id_cont
        inner join sgd_def_continentes c on p.id_cont=c.id_cont
        order by nombrec, nombrep, nombred, nombrem";
    $rs = $conn->CacheExecute(300, $sql);
    $vcontiv = array();
    $vpaisesv = array();
    $vdptosv = array();
    $vmcposv = array();
    while ($row = $rs->FetchRow()) {
        if (!array_key_exists($row["idc"], $vcontiv)) $vcontiv[$row["idc"]] = $row["nombrec"];
        if (searchForId($row["idp"], $vpaisesv) === NULL) {
            $vpaisesv[] = array('NOMBRE'=>$row["nombrep"], 'ID0'=>$row["idc"], 'ID1'=>$row["idp"]);
        }
        if (searchForId($row["idp"]."-".$row["idd"], $vdptosv) === NULL) {
            $vdptosv[] = array('NOMBRE'=>$row["nombred"], 'ID0'=>$row["idp"], 'ID1'=> $row["idp"]."-".$row["idd"]);
        }
        if (searchForId($row["idp"]."-".$row["idd"]."-".$row["idm"], $vmcposv) === NULL) {
            $vmcposv[] = array('NOMBRE'=>$row["nombrem"], 'ID0'=>$row["idc"], 'ID'=> $row["idp"], 'ID1'=>$row["idp"]."-".$row["idd"]."-".$row["idm"]);
        }
    }
} else {
    
}

function searchForId($id, $array) {
    foreach ($array as $key => $val) {
        if ($val['ID1'] === $id) {
            return $key;
        }
    }
    return null;
}

echo date("Ymd H:i:s");
var_dump($vcontiv);var_dump($vpaisesv);var_dump($vdptosv);var_dump($vmcposv);
?>