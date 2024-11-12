<?php
/**
 * 
 * Enter description here ...
 * @author hladino
 *
 */
class rs2xml {
	
	function creaFila($vector){
		$s = "<FILA>";
		foreach ($vector as $k => $v) {
			if (substr($k, 0, 4) <> 'HID_') {
				$s .= "<$k>".str_replace(array('<','>','&'),'',$v)."</$k>";
			}
		}
		$s .= "</FILA>";
		return $s;
	}
	
	function getXML($rs) {
		$first = true;
		$s =	"<?xml version='1.0' encoding='ISO-8859-1' standalone='yes'?>
				<ROOT xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>";
		while ($arr = $rs->FetchRow()) {
			if ($first) {
				$s .= "<FILA>";
				foreach ($arr as $k => $v) {
					if (substr($k, 0, 4) <> 'HID_') {
						$s .= "<$k/>";
					}
				}
				$s .= "</FILA>";
				$first = false;
			}
			$s .= $this->creaFila($arr);
		}
		$s .= "</ROOT>";
		return $s;
	}
}
?>