<?php
/*********************************************************************************
 *       Filename: Reporte Asignacion de Radicados
 *		 @autor LUCIA OJEDA ACOSTA - CRA
 *		 @version ORFEO 3.5
 *       PHP 4.0 build 22-Feb-2006
 * 
 * Optimizado por HLP. En este archivo trat�de generar las sentencias a estandar de ADODB para que puediesen ejecutar
 * en cualquier BD. En caso de no llegar a funcionar mover el contenido en tre las lineas 26 y 75 a la seccion MSSQL y 
 * descomentariar el switch.
 *
 *********************************************************************************/

$coltp3Esp = '"'.$tip3Nombre[3][2].'"';	
if(!$orno) $orno=1;
$orderE = "	ORDER BY $orno $ascdesc ";

$desde = $fecha_ini . " ". "00:00:00";
$hasta = $fecha_fin . " ". "23:59:59";

$sWhereFec =  " and ".$db->conn->SQLDate('Y/m/d H:i:s', 'R.RADI_FECH_RADI')." >= '$desde'
				and ".$db->conn->SQLDate('Y/m/d H:i:s', 'R.RADI_FECH_RADI')." <= '$hasta'";

if ( $dependencia_busq != 99999)  $condicionE = " AND d.depe_codi=$dependencia_busq ";
if($tipoDocumento=='9999') {
    $queryE = "SELECT count(h.depe_codi_dest) Asignados,
                        d.depe_nomb as Dependencia
			   FROM hist_eventos h,
                        radicado r,
                        dependencia d
			   WHERE h.sgd_ttr_codigo=2 AND r.radi_tiporad = 2 AND 
			   		 r.radi_nume_radi = h.radi_nume_radi AND 
			   		 h.depe_codi_dest = d.depe_codi
			   		 $condicionE $sWhereFec
			   GROUP BY d.depe_nomb, h.depe_codi_dest";
} else {
    if($tipoDocumento!='9998')	$condicionE .= " AND t.SGD_TPR_CODIGO = $tipoDocumento ";
	$queryE = "SELECT MIN(t.sgd_tpr_descrip) TIPO,
                count(h.depe_codi_dest) Asignados, 
			    d.depe_nomb as DEPENDENCIA,
                SGD_TPR_CODIGO HID_TPR_CODIGO
		FROM hist_eventos h,
                radicado r,
                sgd_tpr_tpdcumento t,
                dependencia d
		WHERE h.sgd_ttr_codigo = 2 AND
                r.radi_tiporad = 2 AND
		        r.radi_nume_radi = h.radi_nume_radi AND
		        r.tdoc_codi = t.sgd_tpr_codigo AND
                h.depe_codi_dest = d.depe_codi
			  $sWhereFec $condicionE
		GROUP BY t.sgd_tpr_codigo";
}
//-------------------------------
// Assemble full SQL statement
//-------------------------------
/** CONSULTA PARA VER DETALLES 

$condicionE = "";
//if($tipoDocumento!='9999')	$condicionE = " AND t.SGD_TPR_CODIGO = $tipoDOCumento "; 
if(!is_null($tipoDOCumento))	$condicionE = " AND t.SGD_TPR_CODIGO = $tipoDOCumento "; 
if ($dependencia_busq != 99999)  $condicionE .= " AND $tmp_substr(rtrim(h.usua_codi_dest),1,3)=$dependencia_busq ";
		
$queryEDetalle = "
	SELECT r.radi_nume_radi 	RADICADO, 
		r.radi_fech_radi FECH_RAD, 
		t.sgd_tpr_descrip TIPO,
		r.RADI_PATH HID_RADI_PATH
	FROM hist_eventos h, radicado r, sgd_tpr_tpdcumento t
	WHERE h.sgd_ttr_codigo = 2
		AND r.radi_tiporad = 2 
		AND r.radi_nume_radi = h.radi_nume_radi 
		AND r.tdoc_codi = t.sgd_tpr_codigo
		$sWhereFec";
$queryE .= $orderE;
$queryEDetalle .= $condicionE . $orderE;

$queryE = str_replace('substr','substring',$queryE);
$queryEDetalle = str_replace('substr','substring',$queryEDetalle);

/* carlos */

/*** krlox   if($tipoDocumento=='9999')
{	$queryE = "
		SELECT count(r.radi_nume_radi) Asignados
		FROM dependencia d, hist_eventos h, radicado r
		WHERE h.hist_obse = 'Rad.'
			AND r.radi_tiporad = 2 
			AND r.radi_nume_radi = h.radi_nume_radi 
			AND substr(rtrim(h.usua_codi_dest),1,3) = d.depe_codi 
			$condicionE $sWhereFec 
		GROUP BY d.depe_codi";
}
else
{	if($tipoDocumento!='9998')	$condicionE .= " AND t.SGD_TPR_CODIGO = $tipoDocumento ";
	$queryE = "
		SELECT MIN(t.sgd_tpr_descrip) TIPO, 
			count(r.radi_nume_radi) Asignados, 
			SGD_TPR_CODIGO HID_TPR_CODIGO
		FROM dependencia d, hist_eventos h, radicado r, sgd_tpr_tpdcumento t
		WHERE h.hist_obse = 'Rad.' 
			AND r.radi_tiporad = 2 
			AND r.radi_nume_radi = h.radi_nume_radi 
			AND substr(rtrim(h.usua_codi_dest),1,3) = d.depe_codi
			AND r.tdoc_codi = t.sgd_tpr_codigo 
			$sWhereFec $condicionE
		GROUP BY t.sgd_tpr_codigo";
}
//-------------------------------
// Assemble full SQL statement
//-------------------------------

/** CONSULTA PARA VER DETALLES 

$condicionE = "";
//if($tipoDocumento!='9999')	$condicionE = " AND t.SGD_TPR_CODIGO = $tipoDOCumento "; 
if(!is_null($tipoDOCumento))	$condicionE = " AND t.SGD_TPR_CODIGO = $tipoDOCumento "; 
if ($dependencia_busq != 99999)  $condicionE .= " AND $tmp_substr(rtrim(h.usua_codi_dest),1,3)=$dependencia_busq ";
		
$queryEDetalle = "
	SELECT r.radi_nume_radi 	RADICADO, 
		r.radi_fech_radi FECH_RAD, 
		t.sgd_tpr_descrip TIPO,
		r.RADI_PATH HID_RADI_PATH
	FROM hist_eventos h, radicado r, sgd_tpr_tpdcumento t
	WHERE h.hist_obse = 'Rad.'
		AND r.radi_tiporad = 2 
		AND r.radi_nume_radi = h.radi_nume_radi 
		AND r.tdoc_codi = t.sgd_tpr_codigo
		$sWhereFec";
$queryE .= $orderE;
$queryEDetalle .= $condicionE . $orderE;

$queryE = str_replace('substr','substring',$queryE);
$queryEDetalle = str_replace('substr','substring',$queryEDetalle);   krlox *** */




/*
switch($db->driver)
{	case 'mssqlnative':
		{	
		}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
		{	$sWhereFec =  " and R.RADI_FECH_RADI >= to_date('" . $desde . "','yyyy/mm/dd HH24:MI:ss')
    			    		and R.RADI_FECH_RADI <= to_date('" . $hasta . "','yyyy/mm/dd HH24:MI:ss')";
			if ( $dependencia_busq != 99999)  $condicionE = "	AND d.depe_codi=$dependencia_busq ";
			if($tipoDocumento=='9999')
			{	$queryE = "
					SELECT count(r.radi_nume_radi) 	Asignados
					FROM dependencia d, hist_eventos h, radicado r
					WHERE hist_obse = 'Rad.' 
						AND r.radi_tiporad = 2 
						AND r.radi_nume_radi = h.radi_nume_radi 
						AND substr(h.usua_codi_dest,1,3) = d.depe_codi 
						$condicionE $sWhereFec 
					GROUP BY d.depe_codi";
	
			}
			else
			{	if($tipoDocumento!='9998')	$condicionE .= " AND t.SGD_TPR_CODIGO = $tipoDocumento ";
				$queryE = "
					SELECT MIN(t.sgd_tpr_descrip)	TIPO, 
						count(r.radi_nume_radi)	Asignados, 
						SGD_TPR_CODIGO			HID_TPR_CODIGO
					FROM dependencia d, hist_eventos h, radicado r, sgd_tpr_tpdcumento t
					WHERE h.hist_obse = 'Rad.' 
						AND r.radi_tiporad = 2 
						AND r.radi_nume_radi = h.radi_nume_radi 
						AND substr(h.usua_codi_dest,1,3) = d.depe_codi
						AND r.tdoc_codi = t.sgd_tpr_codigo 
						$sWhereFec $condicionE
					GROUP BY t.sgd_tpr_codigo";
			}
			//-------------------------------
			// Assemble full SQL statement
			//-------------------------------
		
			// CONSULTA PARA VER DETALLES 
			$condicionE = "";
			if($tipoDocumento!='9999')	$condicionE = " AND t.SGD_TPR_CODIGO = $tipoDOCumento "; 
			if ($dependencia_busq != 99999)  $condicionE .= " AND substr(h.usua_codi_dest,1,3)=$dependencia_busq ";
		
			$queryEDetalle = "
				SELECT r.radi_nume_radi 	RADICADO, 
					r.radi_fech_radi		FECH_RAD, 
					t.sgd_tpr_descrip 		TIPO,
					r.RADI_PATH 			HID_RADI_PATH
				FROM hist_eventos h, radicado r, sgd_tpr_tpdcumento t
				WHERE h.hist_obse = 'Rad.' 
					AND r.radi_tiporad = 2 
					AND r.radi_nume_radi = h.radi_nume_radi 
					AND r.tdoc_codi = t.sgd_tpr_codigo
					$sWhereFec";
			$queryE .= $orderE;
			$queryEDetalle .= $condicionE . $orderE;
		}break;
}
*/
?>
