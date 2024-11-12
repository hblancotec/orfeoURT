<?php
   if (!$ruta_raiz) $ruta_raiz=".";
	
   require_once (ORFEOPATH . "include/db/ConnectionHandler.php");
	
	if (!$db)
		$db = new ConnectionHandler(ORFEOPATH); 
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    include (ORFEOPATH . "jh_class/funciones_sgd.php");

    $a = new LOCALIZACION($codep_us1,$muni_us1,$db); 
    $dpto_nombre_us1 = $a->departamento;
    $muni_nombre_us1 = $a->municipio;
    if (isset($codep_us2)) {
        $a = new LOCALIZACION($codep_us2,$muni_us2,$db);
        $dpto_nombre_us2 = $a->departamento; 
        $muni_nombre_us2 = $a->municipio; 
    }
    if (isset($codep_us3)) {
        $a = new LOCALIZACION($codep_us3,$muni_us3,$db);
        $dpto_nombre_us3 = $a->departamento; 
        $muni_nombre_us3 = $a->municipio; 
    }
?>
