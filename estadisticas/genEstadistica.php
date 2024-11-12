<?php
session_start();
$ruta_raiz = '..';
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
/*
$dependencia_busq = 900;
$codus = 131;
$codUs = $codus;
*/

if(!$db) {
	$ruta_raiz = "..";
	//include "$ruta_raiz/rec_session.php";
	include "$ruta_raiz/envios/paEncabeza.php";
?>

	<br>

<?php
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	require_once("$ruta_raiz/class_control/Mensaje.php");
	include("$ruta_raiz/class_control/usuario.php");
	
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	$objUsuario = new Usuario($db);
    
	$datosaenviar = "fechaf=$fechaf" .
					"&genDetalle=$genDetalle" .
					"&tipoEstadistica=$tipoEstadistica" .
					"&codus=$codus" .
					"&krd=$krd" .
					"&dependencia_busq=$dependencia_busq" .
					"&ruta_raiz=$ruta_raiz" .
					"&fecha_ini=$fecha_ini" .
					"&fecha_fin=$fecha_fin" .
					"&tipoRadicado=$tipoRadicado" .
					"&tipoDocumento=$tipoDocumento" .
					"&codUs=$codUs" .
					"&fecSel=$fecSel" .
					"&serie_busq=$serie_busq" .
					"&subSerie_busq=$subSerie_busq" .
					"&fechAno_busq=$fechAno_busq";
}


###################################################################################################
###	SE CAPTURA EL TIPO DE RADICADO SELECCIONADO
if($tipoRadicado) {
	$wTipoRad = " AND R.RADI_TIPORAD = $tipoRadicado ";
}



###################################################################################################
###	SE CAPTURA EL TIPO DOCUMENTAL SELECCIONADO
if($tipoDocumento and ($tipoDocumento!='9999' and $tipoDocumento!='9998' and $tipoDocumento!='9997')) {
	$wTipoDoc = " AND T.SGD_TPR_CODIGO = $tipoDocumento";
}
elseif ($tipoDocumento=="9997")	{
	$wTipoDoc = " AND T.SGD_TPR_CODIGO = 0";
}



###################################################################################################
###	SE CAPTURA LA DEPENDENCIA SELECCIONADA
$wDepe = "";
if ($dependencia_busq && $dependencia_busq != '99999') {
    $wDepe = " AND DEP.DEPE_CODI = $dependencia_busq ";
	$wDepe2 = " AND R.RADI_DEPE_RADI = $dependencia_busq ";
	$wDepe3 = " AND D.DEPE_CODI = $dependencia_busq ";
	$wDepe4 = " AND U.DEPE_CODI = $dependencia_busq ";
}


###################################################################################################
###	SE CAPTURA EL USUARIO SELECCIONADO
if ($codUs > 0){
	$wUsua = " AND U.USUA_CODI = $codUs ";
}
if ($codus > 0){
	$wUsua = " AND U.USUA_CODI = $codus ";
}

if ($_GET['usuDoc']){
	$wUsua = $wUsua . "AND U.USUA_DOC = '". $_GET['usuDoc'] . "'";
}



###################################################################################################
###	SE CAPTURA LA FORMA DE ENVIO SELECCIONADA
if ($fenvCodi){
	$wFormEnv = " AND C.SGD_RENV_CODIGO = $fenvCodi";
}



###################################################################################################
###	SE CAPTURA LA SERIE SELECCIONADA
if ($serie_busq && $serie_busq != 22222){
	$wSerie = " AND SEXP.SGD_SRD_CODIGO = $serie_busq";
}



###################################################################################################
###	SE CAPTURA LA SUB-SERIE SELECCIONADA
if ($subSerie_busq && $subSerie_busq != 33333){
	$wSubSerie = " AND SEXP.SGD_SBRD_CODIGO = $subSerie_busq";
}



###################################################################################################
###	SE CAPTURA EL ANO SELECCIONADO
if ($fechAno_busq && $fechAno_busq != 55555){
	$wAno = " AND SEXP.SGD_SEXP_ANO = $fechAno_busq";
}

if ($exp){
    $expediente = " AND E.SGD_EXP_NUMERO = '$exp'";
}

###################################################################################################
###	SE LLAMAN EL QUERY RESPECTIVO PARA CADA REPORTE
include_once($ruta_raiz."/include/query/busqueda/busquedaPiloto1.php");
switch($tipoEstadistica) {
	case "1";
		include "$ruta_raiz/include/query/estadisticas/consulta001.php";
		$generar = "ok";
		break;
	case "2";
		include "$ruta_raiz/include/query/estadisticas/consulta002.php";
		$generar = "ok";
		break;
	case "3";
		include "$ruta_raiz/include/query/estadisticas/consulta003.php";
		$generar = "ok";
		break;
	case "4";
		include "$ruta_raiz/include/query/estadisticas/consulta004.php";
		$generar = "ok";
		break;
	case "5";
		include "$ruta_raiz/include/query/estadisticas/consulta005.php";
		$generar = "ok";
		break;
	case "6";
		include "$ruta_raiz/include/query/estadisticas/consulta006.php";
		$generar = "ok";
		break;
	case "7";
		include "$ruta_raiz/include/query/estadisticas/consulta007.php";
		//$generar = "ok";
		break;
	case "8";
		include "$ruta_raiz/include/query/estadisticas/consulta008.php";
		//$generar = "ok";
		break;
	case "9";
		include "$ruta_raiz/include/query/estadisticas/consulta009.php";
		$generar = "ok";
		break;
	case "10";
		include "$ruta_raiz/include/query/estadisticas/consulta010.php";
		//$generar = "ok";
		break;
	case "11";
		include "$ruta_raiz/include/query/estadisticas/consulta011.php";
		$generar = "ok";
		break;
	case "12";
		include "$ruta_raiz/include/query/estadisticas/consulta012.php";
		//$generar = "ok";
		break;
	case "13":{
		$sql = "select SGD_SEXP_MIGRADOESTADO, SGD_SEXP_MIGRADODESCRI from sgd_sexp_secexpedientes where sgd_exp_numero='$expediente'";
		$rs13 = $db->conn->Execute($sql);
		if ($rs13) {
			while (!$rs13->EOF) {
				$expAct = $rs13->fields['SGD_SEXP_MIGRADOESTADO'];
				$expMsg = $rs13->fields['SGD_SEXP_MIGRADODESCRI'];
				$rs13->MoveNext();
			}
			if ($expAct == 1){
				echo "<font color='red'>".$expMsg."</font>";
			}
		}
		include "$ruta_raiz/include/query/estadisticas/consulta013.php";
		$generar = "ok";
	}break;
	case "14";
		include "$ruta_raiz/include/query/estadisticas/consulta014.php";
		//$generar = "ok";
		break;
	case "15";
		include "$ruta_raiz/include/query/estadisticas/consulta015.php";
		$generar = "ok";
		break;
	case "16";
		include "$ruta_raiz/include/query/estadisticas/consulta016.php";
		//$generar = "ok";
	case "18";
		//include "$ruta_raiz/include/query/estadisticas/consulta018.php";
	case "19";
		//include "$ruta_raiz/include/query/estadisticas/consulta019.php";
		$generar = "ok";
		break;
	case "20";
    	include "$ruta_raiz/include/query/estadisticas/consulta020.php";
    	$generar = "ok";
    	break;
	case "22";
    	include "$ruta_raiz/include/query/estadisticas/consulta022.php";
    	$generar = "ok";
    	break;
}


if($tipoEstadistica == 19){
	include ("$ruta_raiz/include/pdf/class.ezpdf.inc");
	$ruta   = "estadisticas19_".rand(10000, 99999)."_".time().".pdf"; //ruta anexos
	$ruta2  = "$ruta_raiz/bodega/tmp/$ruta"; //donde se guarda el archivo
	$data   = array();
	$pdf    = new Cezpdf("A4","landscape");
	$pdf->ezSetCmMargins(1,1,1,1);
	$pdf->selectFont("$ruta_raiz/include/pdf/fonts/Times-Roman.afm");
	$pdf->ezText("REPORTE DE PQR'S URT\n".date("Y M j G:i:s")."\n",10,array("justification"=>"left"));
	$salid = $db->conn->Execute($queryE);
	//echo $queryE;
	while(!$salid->EOF){	
		$radpat = $salid->fields['RADI_PATH'];
		if(empty($radpat)){
			$enlace = $salid->fields['RADICADO'];
		}
		else{
			$radica = "/bodega/".$radpat;
			$enlace = "<c:alink:http://".$_SERVER['SERVER_NAME']."$radica>".$salid->fields['RADICADO']."</c:alink>";
		}
		$compac[] = array(	'radicado'       =>$enlace,
                            'fecha_radicado' =>$salid->fields['FECHA_RADICADO'],
                            'tpr'            =>$salid->fields['TIPO_RADICADO'],
                            'tpr_tiempo'     =>$salid->fields['TIEMPO_LEGAL'],
                            'asunto'         =>$salid->fields['ASUNTO'],
                            'depen_actu'     =>$salid->fields['DEP_ACTUAL'],
                            'depen_ante'     =>$salid->fields['DEP_ANTERIOR'],
                            'radicado_sal'   =>$salid->fields['RESPUESTA'],
                            'fecha_hist'     =>$salid->fields['FECHA_RESPUESTA']
                        );
		//$data = array_merge($data, $compac);
		$salid->MoveNext();
	};
    $data = $compac; 
	$pdf->ezTable($data, array(	'radicado'		 => 'RADICADO', 
					            'fecha_radicado' => 'FECHA RADICADO',
					            'tpr'            => 'TIPO RADICADO',
					            'tpr_tiempo'     => 'TIEMPO LEGAL',
					            'asunto'		 => 'ASUNTO',
					            'depen_actu'	 => 'DEPENDENCIA ACTUAL',
					            'depen_ante'	 => 'DEPENDENCIA ANTERIOR',
					            'radicado_sal'	 => 'RESPUESTA',
					            'fecha_hist'	 => 'FECHA RESPUESTA'
					),'',array('fontSize' => 9, 'maxWidth' => 785));
	$pdfcode = $pdf->ezOutput();
	$fp      = fopen("$ruta2",'wb');
	fwrite($fp,$pdfcode);
	fclose($fp);
        
	echo "<table class='borde_tab' width='100%' cellspacing='5' cellpadding='0' border='0'>
		   <tbody>
			<td class='titulos2' colspan='2'>
			 <center>
			  <a href='$ruta2'>Reporte en pdf</a>
			 </center>
			</td>
		   </tbody>
		  </table>";
	die;
}


if ($tipoEstadistica == 3 or $tipoEstadistica == 9){
	//$db->conn->debug = true;
    $rsQuery = $db->conn->Execute($queryE);
	
	### SE INCLUYE ARCHIVO EN DONDE SE ARMAN LOS REPORTES EN EXCEL
	require ("$ruta_raiz/include/rs2xml.php");
	$obj = new rs2xml();
	
	if ($rsQuery) {
		$path = "../bodega/tmp/consulta".date("dmYh").time("his").".csv";
		
		$Rs2Xml = $obj->getXML($rsQuery);
		$arch = $krd."_".rand(10000, 20000);
		$ruta = "../bodega/tmp/$arch.xls";
		$fpDev = fopen($ruta, "w");
		if ($fpDev) {
			fwrite($fpDev, $Rs2Xml);
			fclose($fpDev);
		}
	}
	else
		$resultado = 'Hubo un error en creacion del reporte de devoluciones.';
	
	echo "<table class='borde_tab' width='100%' cellspacing='5' cellpadding='0' border='0'>
		   <tbody>
			<td class='titulos2' colspan='2'>
			 <center>
			  <a href='$ruta'>Descargar Reporte</a>
			 </center>
			</td>
		   </tbody>
		  </table>";
	
	//echo "&nbsp; <a href='$ruta' target='_blank'> Descargar Reporte </a>";
}

if($generar == "ok") {
	//$db->conn->debug = true;
	if($genDetalle==1) $queryE = $queryEDetalle;
	if($genTodosDetalle==1) { $queryE = $queryETodosDetalle; }
	$rsE = $db->conn->Execute($queryE);
	if ($tipoEstadistica == 15) {
	    $rsA = $db->conn->Execute($queryE);
	}
		
	unset($_SESSION['nombUs']);
	unset($_SESSION['data1y']);
	include "gptablaHtml.php";
}
if (is_array($matriz_rad)){
	$cnt = 0;
	$css = 1;
	foreach ($matriz_rad as $row => $col) {
		if (count($col) > $cnt ){
			$cnt = count($col);
			$fila_como_titulo = $row;
		}
		$cuerpo .= "<tr class='listado$css'>"; 
		foreach ($col as $dato) {
			$cuerpo .= "<td>$dato</td>"; 
		}
		$css = ($css==1) ? 2 : 1;
		$cuerpo .= "</tr>";
	}
	$cuerpo .= "</table>";
	$titulo = "<table border='1' cellpading='2' cellspacing='0' class='borde_tab' valign='top' align='center'>";
	$titulo .= "<tr>";
	foreach ($matriz_rad[$fila_como_titulo] as $k=>$d) {
		$titulo .= "<th class='titulos3'>$k</th>";
	}
	$titulo .= "</tr>";
	echo $titulo.$cuerpo;
	include "funciones_varias.php";
	$hora=date("H").date("i").date("s");
	$archivo = "../bodega/tmp/Nomb"."_$fecha"."$hora" .".xls";			
	$fp=fopen($archivo,"wb");
	fputs($fp,matriz_to_xml($matriz_rad));
	fclose($fp);
	echo "<a href='$archivo'>XML</a>";
	die("");
}
?>