<?php 
/**
 * CONSULTA VERIFICACION PREVIA A LA RADICACION
*/
$sqlConcatC = $tmp_hlp;
$condicion  = $tmp_hlp;
switch($db->driver)
{
	case 'mssqlnative':
		{
			$nombre = "convert(char(15),a.radi_nume_sal)";
			$nombre2 = $db->conn->Concat("convert(char(15),radi_nume_sal,0)","'-'","cast(sgd_renv_codigo as varchar)");
		}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
		{
			$nombre = "(a.radi_nume_sal)";
			$nombre2 = $db->conn->Concat("radi_nume_sal","'-'","sgd_renv_codigo");
		}break;
}
?>