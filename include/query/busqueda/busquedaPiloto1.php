<?php
if (!$db->driver){
    $db = $this->db;
}	//Esto sirve para cuando se llama este archivo dentro de clases donde no se conoce $db.

switch($db->driver) {
    case 'mssqlnative':

				$tmp_substr = $db->conn->substr;
				$systemDate = $db->conn->sysTimeStamp;
				$radi_nume_radi		= " r.radi_nume_radi ";
				$radi_nume_deri		= " r.radi_nume_deri ";
				$usua_doc_c			= " convert(varchar(8), c.USUA_DOC) ";
				$radi_nume_salida	= " RADI_NUME_SALIDA ";
				$radi_nume_sal		= " RADI_NUME_SAL ";
				$anex_radi_nume     = " r.anex_radi_nume ";
				$redondeo = "dbo.diashabilestramite(r.radi_fech_radi, $systemDate)";
				$redondeo2 = "dbo.diashabilestramite(agen.SGD_AGEN_FECHPLAZO, $systemDate)";
				$diasf              = " convert(int,".$systemDate."-r.sgd_fech_impres)";
		break;
	case 'oracle':
		 case 'oci8':
		case 'oci805':
		case 'ocipo':
		{	$radi_nume_radi = " r.RADI_NUME_RADI ";
			$usua_doc_c			= " convert(varchar(8), c.USUA_DOC) ";
			$radi_nume_salida = " RADI_NUME_SALIDA ";
			$radi_nume_sal = " RADI_NUME_SAL ";
			$systemDate = $db->conn->sysTimeStamp;
			$redondeo = "round(((r.RADI_FECH_RADI+(td.SGD_TPR_TERMINO * 7/5))-".$systemDate."))";
		}break;
		case 'postgresql':
			$radi_nume_radi		= "cast(r.radi_nume_radi as varchar(15)) ";
			$usua_doc_c			= "cast(c.USUA_DOC as varchar(8)) ";
			$radi_nume_salida	= "cast(RADI_NUME_SALIDA as varchar(15)) ";
			$radi_nume_sal		= "cast(RADI_NUME_SAL as varchar(15)) ";
			$systemDate = $db->conn->sysTimeStamp;
			$redondeo = $db->conn->round("(r.radi_fech_radi+(td.sgd_tpr_termino * 7/5))"."-".$systemDate);
			break;
}
?>
