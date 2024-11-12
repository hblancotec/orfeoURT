<?php 

/**
 * 
 * Enter description here ...
 * @param unknown_type $matriz
 */
function matriz_to_xml($matriz) {
	reset($matriz);
	$cnt = 0;
	
	foreach ($matriz as $fila => $columna) {
		$cuerpo_xml .= "<registro>";
		if (count($columna) > $cnt ){
			$cnt = count($columna);
			$fila_como_titulo = $fila;
		}
		foreach ($columna as $key => $value) {
			$cuerpo_xml .= "<$key>".htmlspecialchars($value)."</$key>";
		}
		$cuerpo_xml .= "</registro>";
	}
	
	$vec_tit = $matriz[$fila_como_titulo];
	
	$titulos_xml .= "<registro>";
	foreach ($vec_tit as $key => $value) {
		$titulos_xml .= "<$key></$key>";
	}
	$titulos_xml .= "</registro> ";
	
	$xml = "<?xml version='1.0' encoding='iso-8859-1'?><root>".$titulos_xml.$cuerpo_xml."</root>";
	return $xml;
}

?>