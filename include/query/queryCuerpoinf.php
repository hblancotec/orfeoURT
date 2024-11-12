<?php
    
$radi_nume_radi = "convert(varchar(15),a.RADI_NUME_RADI)";
$tmp_cad1 = "convert(varchar,".$db->conn->concat("'0'","'-'",$radi_nume_radi).")";
$tmp_cad2 = "convert(varchar,".$db->conn->concat('c.info_codi',"'-'",$radi_nume_radi).")";
$redondeo = "dbo.diashabilestramite($sqlOffset, $systemDate)";
$concatenar = "CAST(DEPE_CODI AS VARCHAR(10))";
            
$isql ="SELECT " .$radi_nume_radi. " AS 'IMG_NUMERO RADICADO',
				A.RADI_PATH			AS 'HID_RADI_PATH',
				" .$sqlFecha. "		AS 'DAT_FECHA RADICADO',
				" .$radi_nume_radi."AS 'HID_RADI_NUME_RADI',
				" .$sqlFecha. "		AS 'HID_FECHA RADICADO',
				C.info_desc			AS 'ASUNTO',
				B.sgd_tpr_descrip	AS 'TIPO DOCUMENTO',
				" .$redondeo. "		AS 'DIAS RESTANTES',
				D.usua_nomb			AS 'INFORMADOR',
				" .$tmp_cad2. "		AS 'CHK_CHECKVALUE',
				C.INFO_LEIDO		AS 'HID_RADI_LEIDO'
		FROM	INFORMADOS C
				JOIN RADICADO A ON
					A.RADI_NUME_RADI = C.RADI_NUME_RADI
                JOIN SGD_TPR_TPDCUMENTO B ON
					b.SGD_TPR_CODIGO = A.TDOC_CODI
                JOIN USUARIO D ON
					D.USUA_DOC = C.INFO_CODI
		WHERE	C.USUA_CODI = ". $codusuario ." AND
				C.DEPE_CODI = ". $dependencia ." AND 
				C.SGD_INFDIR_CODIGO = 0
		ORDER BY ". $order ." ". $orderTipo;
?>