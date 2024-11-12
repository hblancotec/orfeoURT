	<?php
set_time_limit(0);
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

$krdOld = $krd;
$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
if(!$tipoCarpOld) $tipoCarpOld= $tipo_carpt;
$ruta_raiz = "..";

if(!$krd) $krd=$krdOsld;
include "$ruta_raiz/rec_session.php";

//Paremetros get y pos enviados desde la apliacion origen
//import_request_variables("gP", "");	
	
if(!$tipoEstadistica)	$tipoEstadistica =1;
if(!$dependencia_busq)	$dependencia_busq =$dependencia;
if(!$serie_busq)		$serie_busq = 22222;
if(!$subSerie_busq)		$subSerie_busq = 33333;
if(!$fechAno_busq)		$fechAno_busq  = 55555;	
	
/** DEFINICION DE VARIABLES ESTADISTICA
* var $tituloE String array  Almacena el titulo de la Estadistica Actual
* var $subtituloE String array  Contiene el subtitulo de la estadistica
* var $helpE String Almacena array Almacena la descripcion de la Estadistica.
*/
$tituloE[1] = "REPORTE - CONSULTA DE RADICADOS POR USUARIO";
$tituloE[2] = "REPORTE - ESTADISTICAS POR MEDIO DE RECEPCION";
$tituloE[3] = "REPORTE - MEDIO DE ENVIO DE DOCUMENTOS";
$tituloE[4] = "REPORTE - ESTADISTICAS DE DIGITALIZACION DE DOCUMENTOS";
$tituloE[5] = "REPORTE - RADICADOS DE ENTRADA RECIBIDOS DEL AREA DE CORRESPONDENCIA";
$tituloE[6] = "REPORTE - RADICADOS ACTUALES EN LA DEPENDENCIA";
//$tituloE[7] = "REPORTE - ESTADISTICAS DE NUMERO DE DOCUMENTOS ENVIADOS";
//$tituloE[8] = "REPORTE DE VENCIMIENTOS";
$tituloE[9] = "REPORTE - SEGUIMIENTO A RADICADOS DE ENTRADA";
//$tituloE[10] = "REPORTE - ASIGNACION RADICADOS";
$tituloE[11] = "REPORTE - ESTADISTICAS DE DIGITALIZACION Y ASOCIACION DE IMAGENES";
//$tituloE[12] = "REPORTE - DOCUMENTOS RETIPIFICADOS POR TRD";
$tituloE[13] = "REPORTE - EXPEDIENTES POR DEPENDENCIA";
//$tituloE[14] = "REPORTE DE RADICADOS ASIGNADOS DETALLADOS (CARPETAS PERSONALES)";
//$tituloE[15] = "REPORTE - REASIGNACION DE DOCUMENTO A OTRO USUARIO"; //US-31172
//$tituloE[16] = "REPORTE DE RESPUESTA A RADICADOS";
//$tituloE[17] = "REPORTE - INTERNET";
$tituloE[18] = "REPORTE - SEGUIMIENTO A TR&Aacute;MITES";
//$tituloE[19] = "REPORTE - PQR'S PARA SITIO WEB DNP";
//$tituloE[20] = "REPORTE - PRÉSTAMOS";//US-31172
//$tituloE[21] = "REPORTE - INTERNET - RADICADOS PENDIENTES DE TRÁMITE";//US-31172

$subtituloE[1] = "ORFEO - Generada el: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[2] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[3] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[4] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[5] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[6] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
//$subtituloE[8] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";

$helpE[1] = "Este reporte genera la cantidad de radicados por usuario. Se puede discriminar por tipo de radicaci&oacute;n. ";
$helpE[2] = "Este reporte genera la cantidad de radicados de acuerdo al medio de recepci&oacute;n o envio realizado al momento de la radicaci&oacute;n.";
$helpE[3] = "Este reporte genera la cantidad de radicados enviados a su destino final por el &aacute;rea.  " ;
$helpE[4] = "Este reporte genera la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n." ;
$helpE[5] = "Este reporte genera la cantidad de documentos de entrada dirigidos a una dependencia. " ;
$helpE[6] = "Esta estadistica trae la cantidad de radicados \n generados por usuario, se puede discriminar por tipo de Radicacion. " ;
//$helpE[8] = "Este reporte genera la cantidad de radicados de entrada cuyo vencimiento esta dentro de las fechas seleccionadas. " ;
$helpE[9] = "Este reporte muestra el proceso que han tenido los radicados tipo 2 que ingresaron durante las fechas seleccionadas. ";
//$helpE[10] = "Este reporte muestra cuantos radicados de entrada han sido asignados a cada dependencia. ";
$helpE[11] = "Muestra la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n y la fecha de digitalizaci&oacute;n." ;
//$helpE[12] = "Muestra los radicados que ten&iacute;an asignados un tipo documental(TRD) y han sido modificados";
$helpE[13] = "Muestra todos los expedientes agrupados por dependencia, serie, subserie con el n&uacute;mero de radicados totales";
//$helpE[14] = "Muestra el total de radicados que tiene un usuario y el detalle del radicado con respecto al Remitente(Detalle), Predio(Detalle), ESP(Detalle) ";
$helpE[15] = "Muestra los usuario a los cuales se le ha asignado un documento que el usuario actual tenia";
//$helpE[16] = "Muestra los radicados que se han pasado por un usuario y que se le han dado respuesta";
$helpE[17] = "Muestra los radicados que estan catalogados para el envio de alarmas";
$helpE[18] = "Muestra los radicados que tienen asignado tr&aacute;mites";
//$helpE[19] = "Reporte periodico solicitado para publicar en la pagina web del DNP";
$helpE[20] = "Muestra los radicados y/o expedientes que se encuentran en solicitud de préstamo y están prestados";

$helpE[21] = "Muestra los radicados que se encuentran pendientes de trámite";
$generarOrfeo = $_POST['generarOrfeo'];
?>

<html>
 <head>
  <title>principal</title>
  <link rel="stylesheet" href="../estilos/orfeo.css">
  <link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
  <script>
	function adicionarOp (forma,combo,desc,val,posicion){
		o = new Array;
		o[0]=new Option(desc,val );
		eval(forma.elements[combo].options[posicion]=o[0]);
		//alert ("Adiciona " +val+"-"+desc );
	}
  </script>
  <script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
  <script language="javascript">
	<!--

<?php
$mesActual = date("m");
$ano_ini = ($mesActual == 1) ? date("Y") - 1 : date("Y");
$mes_ini = substr("00".(date("m")-1),-2);
if ($mes_ini==0){
	$ano_ini==$ano_ini-1;
	$mes_ini="12";
}
$dia_ini = date("d");
if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
	$fecha_busq = date("Y/m/d") ;
	if(!$fecha_fin) 
		$fecha_fin = $fecha_busq;
?>

	var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formulario", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formulario", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);
	//-->
  </script>
 </head>

<?php
	include "$ruta_raiz/envios/paEncabeza.php";
?>

 <table> <tr> <td> </td> </tr> </table>

<?php
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	include("$ruta_raiz/class_control/usuario.php");
	include("$ruta_raiz/class_control/Dependencia.php");
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$objUsuario = new Usuario($db);
	//$db->conn->debug = true;
?>

 <body bgcolor="#ffffff" topmargin="0">
  <div id="spiffycalendar" class="text"></div>
  <form name="formulario"  method=post action='./gpvistaFormConsulta.php?<?=session_name()."=".trim(session_id())."&krd=$krd&fechah=$fechah"?>'>
   <table width="100%"  border="0" cellpadding="0" cellspacing="5" class="borde_tab">
	<tr class="titulos2">
	 <td colspan="2" >POR RADICADOS..  -
	  - <A href='vistaFormProc.php?<?=session_name()."=".trim(session_id())."&krd=$krd&fechah=$fechah"?>' style="color: #FFFFCC">POR PROCESOS </A> 
	 </td>
	</tr>
	<tr>
	 <td colspan="2" class="titulos3">
	  <span class="cal-TextBox"><?=$helpE[$tipoEstadistica]?></span>
	 </td>
	</tr>
	<tr>
	 <td width="30%" class="titulos2">Tipo de Consulta / Estadistica</td>
	 <td class="listado2" align="left">
	  <select name=tipoEstadistica  class="select" onChange="formulario.submit();">

<?php
	foreach($tituloE as $key=>$value) {
		if($tipoEstadistica==$key) $selectE = " selected "; else $selectE = "";
		echo "<option value='$key' $selectE>$tituloE[$key]</option>\n\t";
	}
?>

	  </select>
	 </td>
	</tr>

<?php
	if($tipoEstadistica!=16 && $tipoEstadistica!=19 ){
?>

	<tr>
	 <td width="30%" class="titulos2">Dependencia</td>
	 <td class="listado2">

<?php
		if($tipoEstadistica != 17){
			print "<select name='dependencia_busq'  class='select'  onChange='formulario.submit();'>";
		}
		else{
			print "<select name='dependencia_busq'  class='select'>";
		}
		// Consulta de estadisticas para los usuario que tenga privilegio de reasignar
		$getUsuario = "	SELECT	USUARIO_REASIGNAR
						FROM 	USUARIO
						WHERE	USUA_LOGIN = '$krd'";
		$rsUsuario = $db->conn->Execute($getUsuario);
		$permisoReasignar = "";
		if (!$rsUsuario->EOF){
			$permisoReasignar = $rsUsuario->fields["USUARIO_REASIGNAR"];
			$permisoReasignar = (!empty($permisoReasignar) && $permisoReasignar != 0) ? true : false;
		}
		if($usua_perm_estadistica>1 || (($tipoEstadistica == 15 || $tipoEstadistica == 16) && $permisoReasignar)
		    || $tipoEstadistica == 17 || $tipoEstadistica == 21){
			if($dependencia_busq==99999){
				$datoss= " selected ";
			}
			echo "<option value='99999' $datoss>-- Todas las Dependencias --</option>\n";
		}
	    $whereDepSelect=" DEPE_CODI = $dependencia ";
    	if ($usua_perm_estadistica==1){
        	$whereDepSelect=" $whereDepSelect or depe_codi_padre = $dependencia ";
    	}
    	if ($usua_perm_estadistica==2 || $tipoEstadistica == 17 || $tipoEstadistica == 21) {
			$isqlus = "	SELECT	a.DEPE_CODI,
								a.DEPE_NOMB,
								a.DEPE_CODI_PADRE
						FROM	DEPENDENCIA a
						ORDER BY a.DEPE_NOMB";
    	}
		else {
			$whereDepSelect=
			$isqlus = "	SELECT	a.DEPE_CODI,
								a.DEPE_NOMB,
								a.DEPE_CODI_PADRE
						FROM	DEPENDENCIA a
						WHERE	$whereDepSelect ";
    	}
    	$rs1 = $db->conn->Execute($isqlus);
		do{
			$codigo = $rs1->fields["DEPE_CODI"];
			$vecDeps[]=$codigo;
			$depnombre = $rs1->fields["DEPE_NOMB"];
			$datoss="";
			if($dependencia_busq==$codigo){
				$datoss= " selected ";
			}
			echo "<option value='$codigo'  $datoss>$codigo - $depnombre</option>\n";
			$rs1->MoveNext();
    	}
		while(!$rs1->EOF);
?>

	 </td>
	</tr>

<?php
	}
	else {
		echo "<input type='hidden' name='dependencia_busq' value='$codigo'>";
	}
	
	// MOSTRAR MEDIO DE RECEPCION PARA EL REPORTE DE INTERNET (17)
	if($tipoEstadistica == 17 || $tipoEstadistica == 21) {
	    print '<tr><td class="titulos2"> Medio de recepci&oacute;n </td> <td class="listado2">';
	    //Consultas para filtros de seleccion
	    //medio de recepcion
	    $medSQL = " SELECT	MREC_DESC
							,MREC_CODI
					FROM	MEDIO_RECEPCION
					ORDER BY MREC_DESC";
	    $cons_medRecp 	= $db->conn->Execute($medSQL);
	    print $cons_medRecp->GetMenu2('med_rec2'
	        ,$med_rec2
	        ,'0: [-Todos los medios-]'
	        ,true
	        ,5
	        , 'id="med_rec" class="select" ');
	    print '</td></tr>';
	    print '<tr><td class="titulos2"> Temas </td> <td class="listado2">';
	    //Consulta para filtros de seleccion
	    //Temas
	    $sqlPqr = "	SELECT	SGD_DCAU_DESCRIP
							,SGD_DCAU_CODIGO
                	FROM	SGD_DCAU_CAUSAL
					WHERE	SGD_DCAU_CODIGO >=1
					ORDER BY SGD_DCAU_DESCRIP";
	    
	    $cons_temas = $db->conn->Execute($sqlPqr);
	    $temaSelect = (empty($temaSelect))? 0 : $temaSelect;
	    $datoss 	= ($temaSelect=="99999")? "selected" : '';
	    print "<select name='temaSelect'>
				<OPTION value='99999' $datoss> -- Solo Radicados Con Temas --</OPTION>";
	    $datoss = ($temaSelect=='0')? "selected" : '';
	    print "<OPTION value=0 $datoss> -- Todos los Radicados --</OPTION>";
	    while(!$cons_temas->EOF){
	        $registro 	= $cons_temas->fields["SGD_DCAU_CODIGO"];
	        $temaDesc 	= $cons_temas->fields["SGD_DCAU_DESCRIP"];
	        $datoss	 	= ($temaSelect == $registro)? 'selected' : '';
	        print "<OPTION value='$registro' $datoss>$temaDesc</OPTION>";
	        $cons_temas->MoveNext();
	    }
	    print '</select></td></tr>';
	    $datoss  = ($SelPqr)? 'checked' : '';
	    print "<tr><td class='titulos2'> Tipo Documento </td>
			  <td class='listado2'><input type='checkbox' name='SelPqr' $datoss>pqr</td></tr>";
	}
	    
    if($generarOrfeo && $tipoEstadistica == 17) {
	    $fechafiltro = " and r.RADI_FECH_RADI BETWEEN ". $db->conn->DBTimeStamp($fecha_ini. " 00:00:01")." and ". $db->conn->DBTimeStamp($fecha_fin." 23:59:59");
	    if($dependencia_busq == 99999){
	        $dependencia_busq2 = "";
	    }
	    else{
	        $dependencia_busq2 = " and r.RADI_DEPE_ACTU IN ($dependencia_busq)";
	    }
	    
	    if($SelPqr)
	        $wherePqr = "AND s.SGD_TPR_REPORT1 = 1 ";
	        else
	            $wherePqr = " ";
	            
	            $resulta	= (empty($med_rec2))? "" : implode( ',', $med_rec2);
	            $medio_busq	= (empty($resulta))? "" : " and r.MREC_CODI IN ($resulta)";
	            $temasFrom  = "LEFT OUTER JOIN dbo.SGD_CAUX_CAUSALES caux
						ON (r.RADI_NUME_RADI=caux.RADI_NUME_RADI)";
	            // $temasSelect  0 Todos los radicados, 99999 Solo los que poseen temas
	            if($temaSelect=="99999"){
	                $temasWhere .= " AND caux.SGD_DCAU_CODIGO>=1 ";
	            }
	            elseif($temaSelect==0) {
	                $temasWhere = "";
	            }
	            else{
	                $temasWhere .= " AND caux.SGD_DCAU_CODIGO=$temaSelect ";
	            }
	            if($temasWhere){
	                $temasWhere = "AND r.RADI_NUME_RADI IN (SELECT caux.radi_nume_radi from SGD_CAUX_CAUSALES caux  WHERE (r.RADI_NUME_RADI=caux.RADI_NUME_RADI $temasWhere  ))";
	            }
	            $radiFechradi1 = $db->conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI');
	            $radiFechradi2 = $db->conn->Concat( $db->conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI') , "' 00:00:00'");
	            //echo "temas... ".$temasWhere;
	            $histFech = $db->conn->SQLDate('Y-m-d', 'h.HIST_FECH') . $db->conn->concat_operator."' 00:00:00'";
	            $fechResp1 = "(	SELECT	TOP 1 ".$db->conn->SQLDate('Y-m-d', 'h.HIST_FECH')." AS FECHA_RESP
						FROM	RADICADO RA
								,ANEXOS A
								,HIST_EVENTOS H
						WHERE	A.ANEX_RADI_NUME = RA.RADI_NUME_RADI
								AND (convert(varchar(15), A.RADI_NUME_SALIDA) LIKE '%[136]')
								AND A.RADI_NUME_SALIDA = H.RADI_NUME_RADI
								AND H.SGD_TTR_CODIGO IN (42,22,23)
								AND RA.RADI_NUME_RADI = R.RADI_NUME_RADI
						ORDER BY FECHA_RESP)";
	            
	            $fechResp2 = "	SELECT	TOP 1 ".$db->conn->SQLDate('Y-m-d', 'h.HIST_FECH')."+' 00:00:00 ' AS FECHA_RESP
						FROM	RADICADO RA
								,ANEXOS A
								,HIST_EVENTOS H
						WHERE	A.ANEX_RADI_NUME = RA.RADI_NUME_RADI
								AND (CONVERT(varchar(15), A.RADI_NUME_SALIDA) LIKE '%[136]')
								AND A.RADI_NUME_SALIDA = H.RADI_NUME_RADI
								AND H.SGD_TTR_CODIGO IN (42,22)
								AND RA.RADI_NUME_RADI = R.RADI_NUME_RADI
						ORDER BY FECHA_RESP";
	            						
		         $query = " SELECT	R.RADI_NUME_RADI AS RADICADO, ".
										$radiFechradi1." AS FECHA_RAD,".
										$db->conn->SQLDate('Y-m-d', 'r.RADI_FECHA_VENCE')." AS FECH_VENCE
						, R.RADI_DIAS_VENCE
                        , R.RADI_ANONIMO AS ANONIMO
						, S.SGD_TPR_DESCRIP AS TIPO_DOC
						, M.MREC_DESC AS M_RECEP
                        , E.DEPE_NOMB AS DEPE_ACTU
						, UA.USUA_NOMB AS USUA_ACTUAL
                        , R.RA_ASUN
						, ME.SGD_FENV_MODALIDAD AS M_RESPUESTA_SOL
						, (	SELECT	TOP 1 H1.HIST_OBSE
							FROM	HIST_EVENTOS H1
							WHERE	R.RADI_NUME_RADI = H1.RADI_NUME_RADI
							ORDER BY H1.HIST_FECH DESC) AS HIST2
						, (	SELECT	TOP 1 H2.HIST_OBSE
							FROM	HIST_EVENTOS H2
							WHERE	R.RADI_NUME_RADI = H2.RADI_NUME_RADI
									and H2.HIST_OBSE <> (SELECT	TOP 1 H1.HIST_OBSE
														FROM	HIST_EVENTOS H1
														WHERE	R.RADI_NUME_RADI = H1.RADI_NUME_RADI
														ORDER BY H1.HIST_FECH DESC)
							ORDER BY H2.HIST_FECH DESC) AS HIST1
						, D.SGD_DIR_NOMREMDES as REMITENTE
                        , D.SGD_DIR_DOC as DOCUMENTO
						, DP.DPTO_NOMB AS DEPARTAMENTO
						, MP.MUNI_NOMB AS MUNICIPIO
						, IP.SGD_INFPOB_DESC AS INF_POBLACIONAL
						, (CASE WHEN ME.PQRVERBAL = 1 THEN 'SI' ELSE 'NO' END) AS PQR_VERBAL
                        , D.SGD_OEM_CODIGO, D.SGD_CIU_CODIGO, D.SGD_ESP_CODI, D.SGD_DOC_FUN
				FROM	RADICADO R LEFT JOIN USUARIO U ON R.RADI_USU_ANTE = U.USUA_LOGIN
						LEFT JOIN SGD_DIR_DRECCIONES D ON R.radi_nume_radi = D.radi_nume_radi AND D.SGD_DIR_TIPO = 1
						LEFT JOIN RADICADO AS R2 ON R2.RADI_NUME_RADI = R.RADI_NUME_DERI
						LEFT JOIN USUARIO U2 ON U2.DEPE_CODI = R2.RADI_DEPE_RADI AND U2.USUA_CODI = R2.RADI_USUA_RADI
						LEFT JOIN USUARIO UA ON UA.DEPE_CODI = R.RADI_DEPE_ACTU AND UA.USUA_CODI = R.RADI_USUA_ACTU
						LEFT JOIN DEPARTAMENTO DP ON DP.ID_PAIS = 170 AND DP.DPTO_CODI = D.DPTO_CODI
						LEFT JOIN MUNICIPIO MP ON MP.MUNI_CODI = D.MUNI_CODI AND MP.DPTO_CODI = D.DPTO_CODI AND MP.ID_PAIS = 170
						LEFT JOIN SGD_PQR_METADATA AS ME ON ME.RADI_NUME_RADI = R.RADI_NUME_RADI
						LEFT JOIN SGD_INF_INFPOB AS IP ON IP.ID_INFPOB = ME.ID_INFPOB
						INNER JOIN SGD_TPR_TPDCUMENTO AS S ON R.TDOC_CODI = S.SGD_TPR_CODIGO
						INNER JOIN MEDIO_RECEPCION AS M ON R.MREC_CODI = M.MREC_CODI
						INNER JOIN dbo.DEPENDENCIA AS E ON R.RADI_DEPE_ACTU = E.DEPE_CODI
				WHERE	r.RADI_TIPORAD = 2
                        $temasWhere
						$wherePqr
						$medio_busq
						$fechafiltro
						$dependencia_busq2
				ORDER BY 2 ";
						
						function carga_radicados(&$mydata, &$result,$db) {
						    
						    while($result && !$result->EOF)	{
						        $nroradicado = $result->fields["RADICADO"];//******** radicado *****//
						        
						        //Mostrar dependencia anterior
						        $isqlq 	= "	SELECT	D.DEPE_NOMB AS DEPE_ANTE
							FROM	RADICADO R,
									DEPENDENCIA D,
									USUARIO U
							WHERE	D.DEPE_CODI		 = U.DEPE_CODI AND
									R.RADI_USU_ANTE  = U.USUA_LOGIN AND
									R.RADI_NUME_RADI = $nroradicado";
						        
						        $resulDepe = $db->conn->Execute($isqlq);
						        
						        
						        
						        unset($temas);
						        
						        $sqlSelect ="SELECT	CAUX.SGD_CAUX_CODIGO,
									CAU.SGD_CAU_DESCRIP as SECTOR,
									DCAU.SGD_DCAU_DESCRIP as CAUSAL,
									CAUX.SGD_CAUX_CODIGO,
									CAUX.RADI_NUME_RADI COUNT_RADI,
									CAUX.SGD_CAUX_FECHA
							FROM	SGD_CAUX_CAUSALES CAUX,
									SGD_CAU_CAUSAL CAU,
									SGD_DCAU_CAUSAL DCAU
							WHERE	RADI_NUME_RADI = $nroradicado
									AND CAUX.SGD_DCAU_CODIGO=dcau.sgd_dcau_codigo
									AND DCAU.SGD_CAU_CODIGO=cau.SGD_CAU_CODIGO";
						        
						        $rs = $db->conn->Execute($sqlSelect);
						        while (!$rs->EOF)  {
						            $temaRad = $rs->fields["SECTOR"] ." / " .$rs->fields["CAUSAL"]; //***Tema
						            $temas[] = $temaRad;
						            $rs->MoveNext();
						        }
						        
			     $radiFechradi2 = $db->conn->Concat( $db->conn->SQLDate('Y-m-d', 'r.RADI_FECH_RADI') , "' 00:00:00'");
				 $fechResp1 = "(	SELECT	TOP 1 ".$db->conn->SQLDate('Y-m-d', 'h.HIST_FECH')." AS FECHA_RESP
						FROM	RADICADO RA
								,ANEXOS AN
								,HIST_EVENTOS H
						WHERE	A.ANEX_RADI_NUME = RA.RADI_NUME_RADI
								AND A.RADI_NUME_SALIDA = H.RADI_NUME_RADI
								AND H.SGD_TTR_CODIGO IN (42,22,23)
								AND AN.RADI_NUME_SALIDA = A.RADI_NUME_SALIDA
						ORDER BY FECHA_RESP)";
						        
						        $mediRespSol = ""; $respuesta = ""; $tipoResp = ""; $responsable = "";
						        $fechaResp = ""; $imageResp = ""; $imageAnex = ""; $enviCorres = "";
						        $expedient = ""; $nomExpedi = ""; $diasResp = ""; $pregunta1 = "";
						        $pregunta2 = ""; $pregunta3 = ""; $pregunta4 = ""; $depResponde = "";

                $sqlExpdien = " SELECT R.RADI_NUME_RADI AS RADICADO, EX.SGD_EXP_NUMERO AS EXPEDIENTE, SEXP.SGD_SEXP_PAREXP1 AS NOMEXP
                            FROM RADICADO R LEFT JOIN SGD_EXP_EXPEDIENTE AS EX ON R.RADI_NUME_RADI = EX.RADI_NUME_RADI
		                      LEFT JOIN SGD_SEXP_SECEXPEDIENTES AS SEXP ON SEXP.SGD_EXP_NUMERO = EX.SGD_EXP_NUMERO
                            WHERE R.RADI_NUME_RADI = $nroradicado ";
                $rsExp = $db->conn->Execute($sqlExpdien);
                if ($rsExp && ! $rsExp->EOF) {
                    while (! $rsExp->EOF) {
                        if (strlen($expedient) > 1)
                            $expedient .= " - ";
                        $expedient .= $rsExp->fields["EXPEDIENTE"];
                        if (strlen($nomExpedi) > 1)
                            $nomExpedi .= " -- ";
                        $nomExpedi .= $rsExp->fields["NOMEXP"];
                        
                        $rsExp->MoveNext();
                    }
                }
                
			     $sqlAnexs = " SELECT R.RADI_NUME_RADI AS RADICADO
						, ME.SGD_FENV_MODALIDAD AS M_RESPUESTA_SOL
						, R.RADI_NUME_DERI AS PADRE
						, U2.USUA_NOMB AS RESP_PADRE
						, A.RADI_NUME_SALIDA AS RESPUESTA
						, TIP.SGD_TPR_DESCRIP AS TIPO_RESP
						, U1.USUA_NOMB AS RESPONSABLE
						, (	SELECT RE.RADI_PATH FROM RADICADO RE WHERE RE.RADI_NUME_RADI = A.RADI_NUME_SALIDA) AS IMAGEN_RESP
						, A.ANEX_NOMB_ARCHIVO AS IMAGEN_ANEXO
						, ( CASE  WHEN a.ANEX_ESTADO = 4 THEN ('CORRESPONDENCIA') ELSE NULL END) AS ENVI_CORRES
						, $fechResp1 as FECHA_RESP
						, dbo.diashabilestramite(($radiFechradi2), ($fechResp1)) as DIAS_RESP
						, ENC.RADI_PREGUNTA1 AS PREGUNTA1
						, ENC.RADI_PREGUNTA2 AS PREGUNTA2
						, ENC.RADI_PREGUNTA3 AS PREGUNTA3
						, ENC.RADI_PREGUNTA4 AS PREGUNTA4
						, DEP.DEPE_NOMB AS DEP_RESPONDE
				FROM	RADICADO R LEFT JOIN ANEXOS A ON A.ANEX_RADI_NUME = R.RADI_NUME_RADI AND A.ANEX_SALIDA = 1 AND A.anex_radi_nume <> A.radi_nume_salida AND A.anex_borrado = 'N'
						LEFT JOIN USUARIO U1 ON U1.USUA_LOGIN = A.ANEX_CREADOR
						LEFT JOIN RADICADO AS R2 ON R2.RADI_NUME_RADI = R.RADI_NUME_DERI
						LEFT JOIN USUARIO U2 ON U2.DEPE_CODI = R2.RADI_DEPE_RADI AND U2.USUA_CODI = R2.RADI_USUA_RADI
						LEFT JOIN RADICADO RR ON RR.RADI_NUME_RADI = A.RADI_NUME_SALIDA
						LEFT JOIN SGD_TPR_TPDCUMENTO TIP ON TIP.SGD_TPR_CODIGO = RR.TDOC_CODI
						LEFT JOIN SGD_ENCUESTA ENC ON ENC.RADI_NUME_RADI = A.RADI_NUME_SALIDA
						LEFT JOIN SGD_PQR_METADATA AS ME ON ME.RADI_NUME_RADI = R.RADI_NUME_RADI
						LEFT JOIN SGD_INF_INFPOB AS IP ON IP.ID_INFPOB = ME.ID_INFPOB
						LEFT JOIN DEPENDENCIA AS DEP ON DEP.DEPE_CODI = RR.RADI_DEPE_RADI
				WHERE r.RADI_TIPORAD = 2 and R.RADI_NUME_RADI = $nroradicado
				ORDER BY 2";
            $rsA = $db->conn->Execute($sqlAnexs);
            if ($rsA && ! $rsA->EOF) {
                while (! $rsA->EOF) {
                    if (strlen($mediRespSol) > 1)
                        $mediRespSol .= " - ";
                    $mediRespSol .= $rsA->fields["M_RESPUESTA_SOL"];
                    if (strlen($respuesta) > 1)
                        $respuesta .= " - ";
                    $respuesta .= $rsA->fields["RESPUESTA"];
                    if (strlen($tipoResp) > 1)
                        $tipoResp .= " - ";
                    $tipoResp .= $rsA->fields["TIPO_RESP"];
                    if (strlen($responsable) > 1)
                        $responsable .= " - ";
                    $responsable .= $rsA->fields["RESPONSABLE"];
                    if (strlen($fechaResp) > 1)
                        $fechaResp .= " - ";
                    $fechaResp .= $rsA->fields["FECHA_RESP"];
                    if (strlen($imageResp) > 1)
                        $imageResp .= " - ";
                    $imageResp .= $rsA->fields["IMAGEN_RESP"];
                    if (strlen($imageAnex) > 1)
                        $imageAnex .= " - ";
                    $imageAnex .= $rsA->fields["IMAGEN_ANEXO"];
                    if (strlen($enviCorres) > 1)
                        $enviCorres .= " - ";
                    $enviCorres .= $rsA->fields["ENVI_CORRES"];
                    if (strlen($diasResp) > 0)
                        $diasResp .= " - ";
                    $diasResp .= $rsA->fields["DIAS_RESP"];
                    if (strlen($pregunta1) > 1)
                        $pregunta1 .= " - ";
                    $pregunta1 .= $rsA->fields["PREGUNTA1"];
                    if (strlen($pregunta2) > 1)
                        $pregunta2 .= " - ";
                    $pregunta2 .= $rsA->fields["PREGUNTA2"];
                    if (strlen($pregunta3) > 1)
                        $pregunta3 .= " - ";
                    $pregunta3 .= $rsA->fields["PREGUNTA3"];
                    if (strlen($pregunta4) > 1)
                        $pregunta4 .= " - ";
                    $pregunta4 .= $rsA->fields["PREGUNTA4"];
                    if (strlen($depResponde) > 1)
                        $depResponde .= " - ";
                    $depResponde .= $rsA->fields["DEP_RESPONDE"];
                    $rsA->MoveNext();
                }
            }
						        
						        $fechaRadicado 	= $result->fields["FECHA_RAD"];		//*** Fecha radicado
						        $tiporad 		= $result->fields["TIPO_DOC"];		//*** Tipo radicado
						        $asunto 		= $result->fields["RA_ASUN"];		//*** Tipo radicado
						        $medRecepcio 	= $result->fields["M_RECEP"];		//*** Medio de recepcion
						        $medRespuesol	= $mediRespSol;                     //*** Medio de respuesta solicitado
						        $pqrVerbal	 	= $result->fields["PQR_VERBAL"];	//*** Pqr Verbal
						        $depeactu		= $result->fields["DEPE_ACTU"];		//*** Dependencia actual
						        $usuaactu		= $result->fields["USUA_ACTUAL"];	//*** Usuario actual
						        $depeante		= $resulDepe->fields["DEPE_ANTE"];	//*** Dependencia anterior
						        $padre		 	= $result->fields["PADRE"];			//*** Radicado padre - posible respuesta2
						        $resPadre	 	= $result->fields["RESP_PADRE"];	//*** Usuario que responde2
						        $hist1			= $result->fields["HIST1"];			//*** Historico1
						        $hist2			= $result->fields["HIST2"];			//*** Historico2
						        $fechVence		= $result->fields["FECH_VENCE"];	//*** Fecha creada desde alarmas
						        $diasVence		= $result->fields["RADI_DIAS_VENCE"];	//*** Dias pendiente para dar respuesta
						        $Resp			= $respuesta;		                //*** Respuesta
						        $TipoResp		= $tipoResp;		                //*** Tipo Documental de la Respuesta
						        $responsable	= $responsable;	                    //*** Usuario que Responde
						        $fechaResp		= $fechaResp;	                    //*** Fecha de respuesta
						        $imaResp		= $imageResp;	                    //*** Img respuesta
						        $anexo			= $imageAnex;	                    //*** Anexo
						        $envioCorr		= $enviCorres;	                    //*** Correspondencia
						        $expedient		= $expedient;	                    //*** Expediente
						        $nomExp			= $nomExpedi;		                //*** Nombre de Expediente
						        $esAnonimo		= $result->fields["ANONIMO"] == 0 ? "No" : "Si";
						        $pregunta1		= $pregunta1;
						        $pregunta2		= $pregunta2;
						        $pregunta3		= $pregunta3;
						        $pregunta4		= $pregunta4;
						        $departamento	= $result->fields["DEPARTAMENTO"];
						        $municipio		= $result->fields["MUNICIPIO"];
						        $infPoblacional	= $result->fields["INF_POBLACIONAL"];
						        $depResponde	= $depResponde;
						        $remitente		= htmlspecialchars( $result->fields["REMITENTE"]);
						        $docremite		= $result->fields["DOCUMENTO"];
						        $rem_oem		= $result->fields["SGD_OEM_CODIGO"];
						        $rem_ciu		= $result->fields["SGD_CIU_CODIGO"];
						        $rem_esp		= $result->fields["SGD_ESP_CODI"];
						        $rem_fun		= $result->fields["SGD_DOC_FUN"];
						        $rem_final 		= ($rem_oem) ? "Empresa" : ( $rem_ciu ? "Ciudadano" : ( $rem_esp ? "Entidad" : "Funcionario" ) );
						        $esAnonimo		= $result->fields["ANONIMO"] == 0 ? "No" : "Si";
						        $myData[] = array(	$nroradicado,
						            $fechaRadicado,
						            $tiporad,
						            $medRecepcio,
						            $depeactu,
						            $depeante,
						            $padre,
						            $hist1,
						            $hist2,
						            $Resp,
						            $fechaResp,
						            $imaResp,
						            $anexo,
						            $envioCorr,
						            $expedient,
						            $temas,
						            $diasResp,
						            $fechVence,
						            $asunto,
						            $diasVence,
						            $remitente,
						            $docremite,
						            $responsable,
						            $resPadre,
						            $usuaactu,
						            $TipoResp,
						            $nomExp,
						            $pregunta1,
						            $pregunta2,
						            $pregunta3,
						            $pregunta4,
						            $departamento,
						            $municipio,
						            $infPoblacional,
						            $depResponde,
						            $pqrVerbal,
						            $medRespuesol,
						            $rem_final,
						            $esAnonimo
						        );
						        unset($diasdeResp);
						        $nroradicadoAnt = $nroradicado;
						        $result->MoveNext();
						    }
						    return $myData;
						}
						
						$db->conn->Execute("SET DATEFIRST 1");
						//$result	  =$db->conn->Execute($isql);
						$result	  =$db->conn->Execute($query);
						$myData   = array();
						$myData   = carga_radicados($mydata, $result, $db);
						
						
						if ($generarOrfeo) {
						    $contenido = "";
						    $contenido .= '<?xml version="1.0" encoding="iso-8859-1"?>';
						    $contenido .= "\n<Pendientes>\n ";
						    
						    if($myData != null){
						        
						        $contenido .= "	<Radicado>\n ";
						        $contenido .= "	<Nro_Radicado></Nro_Radicado>\n";
						        $contenido .= "	<Es_Anonimo></Es_Anonimo>\n";
						        $contenido .= "	<Tipo_Tercero></Tipo_Tercero>\n";
						        $contenido .= "	<Tipo_de_Documento></Tipo_de_Documento>\n";
						        $contenido .= "	<Inf_Poblacional></Inf_Poblacional>\n";
						        $contenido .= "	<Tema1></Tema1>\n";
						        $contenido .= "	<Fecha_Radicado></Fecha_Radicado>\n";
						        $contenido .= "	<fecha_vence></fecha_vence>\n";
						        $contenido .= "	<Fecha_Respuesta></Fecha_Respuesta>\n";
						        $contenido .= "	<Dias_vencimiento></Dias_vencimiento>\n";
						        $contenido .= "	<Dias_respuesta></Dias_respuesta>\n";
						        $contenido .= "	<Respuesta_1></Respuesta_1>\n";
						        $contenido .= "	<Dep_Respuesta></Dep_Respuesta>\n";
						        $contenido .= " <Tipo_Respuesta></Tipo_Respuesta>\n";
						        $contenido .= "	<Responsable1></Responsable1>\n";
						        $contenido .= "	<Respuesta_2></Respuesta_2>\n";
						        $contenido .= "	<Responsable2></Responsable2>\n";
						        $contenido .= "	<Respuesta_3></Respuesta_3>\n";
						        $contenido .= "	<Imagen_Respuesta></Imagen_Respuesta>\n";
						        $contenido .= "	<Asunto></Asunto>\n";
						        $contenido .= "	<Medio_de_Recepcion></Medio_de_Recepcion>\n";
						        $contenido .= "	<Medio_Respuesta_solicitado></Medio_Respuesta_solicitado>\n";
						        $contenido .= "	<Pqr_Verbal></Pqr_Verbal>\n";
						        $contenido .= "	<Departamento></Departamento>\n";
						        $contenido .= "	<Municipio></Municipio>\n";
						        $contenido .= "	<Dependencia_actual></Dependencia_actual>\n";
						        $contenido .= "	<Usuario_actual></Usuario_actual>\n";
						        $contenido .= "	<Dependencia_anterior></Dependencia_anterior>\n";
						        $contenido .= "	<Historico_1></Historico_1>\n";
						        $contenido .= "	<Historico_2></Historico_2>\n";
						        $contenido .= "	<Expediente></Expediente>\n";
						        $contenido .= "	<NombreExp></NombreExp>\n";
						        $contenido .= "	<Remitente></Remitente>\n";
						        $contenido .= "	<Documento></Documento>\n";
						        $contenido .= "	<Pregunta1></Pregunta1>\n";
						        $contenido .= "	<Pregunta2></Pregunta2>\n";
						        $contenido .= "	<Pregunta3></Pregunta3>\n";
						        $contenido .= "	<Pregunta4></Pregunta4>\n";
						        $contenido .= "	</Radicado>\n";
						        
						        
						        foreach ($myData as $item) {
						            $contenido .= "	<Radicado>\n";
						            
						            $contenido .= "	<Nro_Radicado>" 		. trim($item[0])  . "</Nro_Radicado>\n ";
						            $contenido .= "	<Es_Anonimo>" 			. trim($item[38])  . "</Es_Anonimo>\n ";
						            $contenido .= "	<Tipo_Tercero>" 		. trim($item[37])  . "</Tipo_Tercero>\n ";
						            $contenido .= "	<Tipo_de_Documento>"	. trim($item[2])  . "</Tipo_de_Documento>\n ";
						            $contenido .= "	<Inf_Poblacional>"		. trim($item[33])  . "</Inf_Poblacional>\n ";
						            $i=0;
						            if($item[15]){
						                foreach($item[15] as $itemTema){
						                    $i++;
						                    $contenido .= "	<Tema$i>"		. trim($itemTema) . "</Tema$i>\n ";
						                }
						            }
						            $contenido .= "	<Fecha_Radicado>" 		. trim($item[1])   . "</Fecha_Radicado>\n ";
						            $contenido .= "	<fecha_vence>"		    . trim($item[17])  . "</fecha_vence>\n ";
						            $contenido .= "	<Fecha_Respuesta>"		. trim($item[10])  . "</Fecha_Respuesta>\n ";
						            $contenido .= "	<Dias_vencimiento>"     . trim($item[19])  . "</Dias_vencimiento>\n ";
						            $contenido .= "	<Dias_respuesta>"		. trim($item[16])  . "</Dias_respuesta>\n ";
						            $contenido .= "	<Respuesta_1>"			. htmlspecialchars(trim($item[9]))   . "</Respuesta_1>\n ";
						            $contenido .= "	<Dep_Respuesta>"		. trim($item[34])  . "</Dep_Respuesta>\n ";
						            $contenido .= "	<Tipo_Respuesta>"		. trim($item[25])  . "</Tipo_Respuesta>\n ";
						            $contenido .= "	<Responsable1>"			. trim($item[22])  . "</Responsable1>\n ";
						            $contenido .= "	<Respuesta_2>"		    . trim($item[6])   . "</Respuesta_2>\n ";
						            $contenido .= "	<Responsable2>"		    . trim($item[23])  . "</Responsable2>\n ";
						            $contenido .= "	<Respuesta_3>"			. trim($item[12])  . "</Respuesta_3>\n ";
						            $contenido .= "	<Imagen_Respuesta>"		. trim($item[11])  . "</Imagen_Respuesta>\n ";
						            $contenido .= "	<Asunto>"            	. htmlspecialchars(trim($item[18])) . "</Asunto>\n ";
						            $contenido .= "	<Medio_de_Recepcion>"   . trim($item[3])   . "</Medio_de_Recepcion>\n ";
						            $contenido .= "	<Medio_Respuesta_solicitado>"   . trim($item[35])   . "</Medio_Respuesta_solicitado>\n ";
						            $contenido .= "	<Pqr_Verbal>"			. trim($item[35])  . "</Pqr_Verbal>\n ";
						            $contenido .= "	<Departamento>"			. trim($item[31])  . "</Departamento>\n ";
						            $contenido .= "	<Municipio>"			. trim($item[32])  . "</Municipio>\n ";
						            $contenido .= "	<Dependencia_actual>"	. trim($item[4])   . "</Dependencia_actual>\n ";
						            $contenido .= "	<Usuario_actual>"		. trim($item[24])  . "</Usuario_actual>\n ";
						            $contenido .= "	<Dependencia_anterior>" . trim($item[5])   . "</Dependencia_anterior>\n ";
						            $contenido .= "	<Historico_1>"			. htmlspecialchars(trim($item[7]))  . "</Historico_1>\n ";
						            $contenido .= "	<Historico_2>"			. htmlspecialchars(trim($item[8]))  . "</Historico_2>\n ";
						            $contenido .= "	<Expediente>"			. trim($item[14])  . "</Expediente>\n ";
						            $contenido .= "	<NombreExp>"			. htmlspecialchars(trim($item[26]))  . "</NombreExp>\n ";
						            $contenido .= "	<Remitente>"			. htmlspecialchars(trim($item[20]))  . "</Remitente>\n ";
						            $contenido .= "	<Documento>"			. trim($item[21])  . "</Documento>\n ";
						            $contenido .= "	<Pregunta1>"			. trim($item[27])  . "</Pregunta1>\n ";
						            $contenido .= "	<Pregunta2>"			. trim($item[28])  . "</Pregunta2>\n ";
						            $contenido .= "	<Pregunta3>"			. trim($item[29])  . "</Pregunta3>\n ";
						            $contenido .= "	<Pregunta4>"			. trim($item[30])  . "</Pregunta4>\n ";
						            $contenido .= "	</Radicado>\n ";
						        }
						    }
						    $contenido .= "</Pendientes>\n ";
						    unset($item);
						    
						    $hora=date("H").date("i").date("s");
						    // var que almacena el dia de la fecha
						    $ddate=date('d');
						    // var que almacena el mes de la fecha
						    $mdate=date('m');
						    // var que almacena el aÃ±o de la fecha
						    $adate=date('Y');
						    // var que almacena  la fecha formateada
						    $fecha=$adate. $mdate . $ddate;
						    // guarda el path del archivo generado
						    $archivo = "../bodega/tmp/Nomb"."_$fecha"."$hora" .".xls";
						    $fp=fopen($archivo,"wb");
						    fputs($fp,$contenido);
						    fclose($fp);
						}	 
	}
	if ($tipoEstadistica == 21) {
	    include_once 'estadistica21.php';
	}
?>

<!-- +++++++++++++++ INICIO SERIE, FECHA Y SUBSERIE EXPEDIENTES +++++++++++++++++++ -->
<!--INICIO SERIE -->
<?php
//$vars = get_defined_vars(); print_r($vars["_POST"]);
if($tipoEstadistica == 13){
?>
<tr>
  <td width="30%" class="titulos2"> Serie
  	<br/>
	<?php
	$datoss = "";
	if($srdOn) {
		$datoss = " checked ";
	}
	?>
	&nbsp; Solo Inactivas
	<input name="srdOn" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
  </td>
  <td class="listado2">
	<select name=serie_busq  class="select"  onChange="formulario.submit();">
	<?php
	$whereSrdOn = (!isset($_POST['srdOn']) )? "and m.sgd_mrd_esta = 1":"and m.sgd_mrd_esta = 0";
	
	// Consulta de la serie con la Dependencia seleccionada
	$fecha_hoy 		= Date("Y-m-d");
	$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
	$depeConsulta	= '';
	$datoss			= '';
	if($dependencia_busq != 99999)  {
		$depeConsulta =	'M.DEPE_CODI ='.$dependencia_busq. 'AND';
	}
	if($serie_busq == 22222)  {
		$datoss	= " selected ";
	}
	$whereSrdOff = (!isset($_POST['srdOn']) )? "":"AND m.SGD_SRD_CODIGO NOT IN 
	(SELECT DISTINCT m.SGD_SRD_CODIGO FROM SGD_MRD_MATRIRD m WHERE $depeConsulta m.SGD_MRD_ESTA = 1)";
	
	echo "<option value='22222' $datoss>-- Todas las Series --</option>\n";
	$getSerie =	"SELECT	DISTINCT (CONVERT(CHAR(4),S.SGD_SRD_CODIGO,0)+'-'+S.SGD_SRD_DESCRIP) AS DETALLE,
						s.SGD_SRD_CODIGO AS CODIGO
				FROM	SGD_MRD_MATRIRD m,
						SGD_SRD_SERIESRD s
				WHERE	$depeConsulta
						s.SGD_SRD_CODIGO = m.SGD_SRD_CODIGO
						$whereSrdOn
						and ".$sqlFechaHoy." BETWEEN s.SGD_SRD_FECHINI AND s.SGD_SRD_FECHFIN
						$whereSrdOff
				ORDER BY detalle";
	$rsSerie = $db->conn->Execute($getSerie);
	
	while(!$rsSerie->EOF)  {
		$detalle 	= $rsSerie->fields["DETALLE"];
		$codigoSer 	= $rsSerie->fields["CODIGO"];
		$datoss 	= ($serie_busq == $codigoSer)? $datoss= " selected ":"";
		echo "<option value='$codigoSer' $datoss>$detalle</option>";
		$rsSerie->MoveNext();
	};
	
	?>
	</select>
  </td>
</tr>
<?php
}
?>
<!--FIN SERIE -->

<!--INICIO SUB-SERIE -->
<?php
if(($tipoEstadistica == 13) && ($serie_busq != 22222)){
?>
<tr>
  <td width="30%" class="titulos2">SubSerie
 	
 	<!-- *** Adicionado por CECG 02-02-12 *** -->
	<br/>
	<?php
	$datossb = "";
	if($sbrdOn)
	{	$datossb = " checked ";
	}
	?>
	&nbsp; Solo Inactivas
	<input name="sbrdOn" type="checkbox" class="select" <?=$datossb?> onChange="formulario.submit();">
  </td>
  <!-- *** Fin adicionado por CECG 02-02-12 *** -->
  
  <td class="listado2">
    <select name=subSerie_busq  class="select"  onChange="formulario.submit();">
	<?php
		
	// Consulta de la serie con la Dependencia seleccionada
	$fecha_hoy 		= Date("Y-m-d");
	$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
	$depeConsulta	= '';
	$datoss			= '';
	$datossb		= '';
	if($dependencia_busq != 99999)  
	{	$depeConsulta  = 'M.DEPE_CODI ='.$dependencia_busq. 'AND';
	};
	if($subSerie_busq == 33333)  {
	$datossb		= " selected ";
	};
	
	//Agregado por CECG 02-02-12
	//Entra si NO se marca Series Inactivas
	if (!isset($_POST['srdOn']))
	{	//Entra si NO se marca Sub-Series Inactivas
		if (!isset($_POST['sbrdOn']) )
		{	$whereSbrdOn = "AND m.SGD_MRD_ESTA=1";
			$whereSbrdOff = "";
		}
		else
		{	$whereSbrdOn = "AND m.SGD_MRD_ESTA=0";
			$whereSbrdOff = "AND m.SGD_SBRD_CODIGO NOT IN (SELECT DISTINCT m.SGD_SBRD_CODIGO 
														  FROM	 SGD_MRD_MATRIRD m 
														  WHERE	 $depeConsulta
														 		 m.SGD_MRD_ESTA = 1)";
		}
	}
	//Entra SI se marco la opcion de Series Inactivas
	else 
	{	$whereSbrdOn = 	"AND m.SGD_MRD_ESTA=0";
		$whereSbrdOff = "AND m.SGD_SBRD_CODIGO NOT IN (	SELECT	DISTINCT m.SGD_SBRD_CODIGO 
				 										FROM 	SGD_MRD_MATRIRD m 
				 										WHERE	$depeConsulta
				 												m.SGD_MRD_ESTA = 1)";
	}
	//Fin Agregado por CECG 02-02-12	
	
	echo "<option value='33333' $datoss>-- Todas las SubSeries --</option>\n";
	$querySub =	"SELECT	DISTINCT (CONVERT(CHAR(4),SU.SGD_SBRD_CODIGO,0)+'- '+SU.SGD_SBRD_DESCRIP) AS DETALLE,
						SU.SGD_SBRD_CODIGO AS CODIGO
				 FROM	SGD_MRD_MATRIRD M INNER JOIN 
						SGD_SBRD_SUBSERIERD SU ON M.SGD_SBRD_CODIGO = SU.SGD_SBRD_CODIGO
				 WHERE	$depeConsulta
						M.SGD_SRD_CODIGO   		= '$serie_busq'
						AND SU.SGD_SRD_CODIGO  	= '$serie_busq'
						$whereSbrdOn
						AND ".$sqlFechaHoy." BETWEEN SU.SGD_SBRD_FECHINI AND SU.SGD_SBRD_FECHFIN
						$whereSbrdOff
				 ORDER BY DETALLE";
	$rsSub=$db->conn->Execute($querySub);
	//$db->conn->debug = true;
	while(!$rsSub->EOF)  {
		$detalleSub	= $rsSub->fields["DETALLE"];
		$codigoSub 	= $rsSub->fields["CODIGO"];
		$datossSub 	= ($subSerie_busq == $codigoSub)? $datossSub = " selected ":"";
		echo "<option value='$codigoSub' $datossSub>$detalleSub</option>";
		$rsSub->MoveNext();
	};
	?>
	</select>
  </td>
</tr>
<?php
//echo "query Sub ".$querySub;
}
?>
<!--FIN SUB-SERIE -->

<!--INICIO FECHA -->
<?php
if($tipoEstadistica == 13){
?>
<tr>
  <td width="30%" class="titulos2">Aï¿½o</td>
  <td class="listado2">
	<select name=fechAno_busq  class="select"  onChange="formulario.submit();">
	<?php
	// Genera el rango de aï¿½os para seleccionar
	if($fechAno_busq == 55555) {
		$datoss		= " selected ";
	};
	echo "<option value='55555' $datoss>-- Todas los Aï¿½os --</option>\n";
	for($i = Date("Y"); $i > 1994; $i-- ){
		$datossFec = ($fechAno_busq == $i)? $datossFec = " selected ":"";
		echo "<option value='$i' $datossFec>$i</option>";
	}
	?>
	</select>
  </td>
</tr>
<?php
}
?>
<!--FIN FECHA -->
<!-- +++++++++++++++ FIN SERIE, FECHA Y SUBSERIE EXPEDIENTES ++++++++++++++++++++ -->

<?php
if ($dependencia_busq != 99999)  {
	$whereDependencia = " AND USD.DEPE_CODI=$dependencia_busq ";
}

if( $tipoEstadistica == 1  or $tipoEstadistica == 2  or $tipoEstadistica == 3  or
	$tipoEstadistica == 4  or $tipoEstadistica == 5  or $tipoEstadistica == 6  or
	$tipoEstadistica == 7  or $tipoEstadistica == 11 or $tipoEstadistica == 12 or
	$tipoEstadistica == 13 or $tipoEstadistica == 15){
?>
<tr id="cUsuario">
  <td width="30%" class="titulos2">Usuario
	<br/>
	<?php
	$datoss = "";
	if($usActivos) {
		$datoss = " checked ";
	}
	?>
	&nbsp;
	Incluir Usuarios Inactivos
	<input name="usActivos" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
	
  </td>
  <td class="listado2">
    <select name="codus"  class="select"  onChange="formulario.submit();">
	<?php
	if ($usua_perm_estadistica > 0){
    ?>
		<option value="0"> -- Agrupar por todos los usuarios --</option>
	<?php
	}
	$whereUsSelect = (!isset($_POST['usActivos']) )? "and u.USUA_ESTA = 1 ":"";
	$whereUsSelect = ($usua_perm_estadistica < 1)?
		(($whereUsSelect!="")? $whereUsSelect . "and u.USUA_LOGIN='$krd' ":"and u.USUA_LOGIN='$krd'"):$whereUsSelect;
	if($dependencia_busq != 99999)  {
		$whereUsSelect=($whereUsSelect=="") ? substr($whereDependencia,4):$whereUsSelect.$whereDependencia;
		$isqlus = "	SELECT
					u.USUA_NOMB,
                    u.USUA_CODI,
                    u.USUA_ESTA
                FROM
                   	USUARIO u,
                    SGD_USD_USUADEPE USD
                WHERE
                   	u.usua_login = usd.usua_login and
                    USD.SGD_USD_SESSACT = 1
                    $whereUsSelect
				ORDER BY
				  	u.USUA_NOMB";
		$rs1 = $db->conn->Execute($isqlus);
		while(!$rs1->EOF)  {
			$codigo = $rs1->fields["USUA_CODI"];
			$vecDeps[]=$codigo;
			$usNombre = $rs1->fields["USUA_NOMB"];
			$datoss = ($codus==$codigo)?$datoss= " selected ":"";
			echo "<option value='$codigo' $datoss>$usNombre</option>";
			$rs1->MoveNext();
		}
	}
	?>
	</select>
	&nbsp;
  </td>
</tr>
<?php
}

  if(   $tipoEstadistica == 1 or $tipoEstadistica == 2 or $tipoEstadistica == 3 or
  		$tipoEstadistica == 4 or $tipoEstadistica == 6 or $tipoEstadistica ==11 or
  		$tipoEstadistica ==12 or $tipoEstadistica ==15 or $tipoEstadistica==16) {
?>
<tr>
	<td width="30%" height="40" class="titulos2">Tipo de Radicado </td>
	<td class="listado2">
<?php
        $sqlQuery = "SELECT	SGD_TRAD_DESCR,
                        	SGD_TRAD_CODIGO
                     FROM  	SGD_TRAD_TIPORAD
                     ORDER BY SGD_TRAD_CODIGO";
		$rs = $db->conn->Execute($sqlQuery);
		$nmenu = "tipoRadicado";
		$valor = "";
		$default_str=$tipoRadicado;
		$itemBlanco = " -- Agrupar por Todos los Tipos de Radicado -- ";
		print $rs->GetMenu2($nmenu, $default_str, $blank1stItem = "$valor:$itemBlanco",false,0,'class=select');
		?>&nbsp;</td>
</tr>
<?php
  }
  if($tipoEstadistica== 1 or $tipoEstadistica == 6 or $tipoEstadistica == 10 or
	 $tipoEstadistica==12 or $tipoEstadistica ==14 or $tipoEstadistica == 15) {
?>
  <tr>
    <td width="30%" height="40" class="titulos2">Agrupar por Tipo de Documento </td>
    <td class="listado2">
	<select name=tipoDocumento  class="select" >
<?php
 		$isqlTD = "	SELECT	SGD_TPR_DESCRIP
 							,SGD_TPR_CODIGO
					FROM	SGD_TPR_TPDCUMENTO
					WHERE	SGD_TPR_CODIGO<>0
				    ORDER BY SGD_TPR_DESCRIP";
	    //if($codusuario!=1) $isqlus .= " and a.usua_codi=$codusuario ";
		//echo "--->".$isqlus;
		$rs1=$db->conn->Execute($isqlTD);
		$datoss = "";

		if($tipoDocumento!='9998'){
			$datoss= " selected ";
			$selecUs = " b.USUA_NOMB USUARIO, ";
			$groupUs = " b.USUA_NOMB, ";
		}

		if($tipoDocumento=='9999'){
			$datoss= " selected ";
		}
?>
		<option value='9999'  <?=$datoss?>>-- No Agrupar Por Tipo de Documento</option>
<?php
        $datoss = "";
		if($tipoDocumento=='9998'){
			$datoss= " selected ";
		}
?>
		<option value='9998'  <?=$datoss?>>-- Agrupar Por Tipo de Documento</option>
<?php
		$datoss = "";
		if($tipoDocumento=='9997'){
			$datoss= " selected ";
		}
?>
		<option value='9997'  <?=$datoss?>>-- Tipos Documentales No Definidos</option>
<?php
		do{
			$codigo = $rs1->fields["SGD_TPR_CODIGO"];
			$vecDeps[]=$codigo;
			$selNombre = $rs1->fields["SGD_TPR_DESCRIP"];
			$datoss="";
		if($tipoDocumento==$codigo){
				$datoss= " selected ";
			}
			echo "<option value=$codigo  $datoss>$selNombre</option>";
			$rs1->MoveNext();
		}while(!$rs1->EOF);
?>
		</select>

	  </td>
  </tr>
<?php
}

if($tipoEstadistica == 18){
	$sql = "SELECT SGD_NOMBR_TRAM, SGD_ID_TRAM, SGD_DEPFI_TRAM,".
			"(case when SGD_TRAD1_TRAM=1 then '1' else '' end + case when SGD_TRAD2_TRAM=1 then '2' else '' end +
					case when SGD_TRAD3_TRAM=1 then '3' else '' end + case when SGD_TRAD4_TRAM=1 then '4' else '' end +
					case when SGD_TRAD5_TRAM=1 then '5' else '' end + case when SGD_TRAD6_TRAM=1 then '6' else '' end +
					case when SGD_TRAD7_TRAM=1 then '7' else '' end + case when SGD_TRAD8_TRAM=1 then '8' else '' end +
					case when SGD_TRAD9_TRAM=1 then '9' else '' end) as RAD_DETIENE
			FROM SGD_TRAMITES ORDER BY SGD_NOMBR_TRAM";
	$rs_tram = $db->conn->Execute($sql);
	$slcTram = $rs_tram->GetMenu2('cmb_tram', $cmb_tram, false, false, 0, "class='select'");
?>
<tr>
	<td width="30%" class="titulos2">Tr&aacute;mite</td>
	<td class="listado2">
		<?php echo $slcTram; ?>
	</td>
</tr>
<?php
}

if($tipoEstadistica !=13 && $tipoEstadistica !=6){
?>
		<tr>
		    <td width="30%" class="titulos2">Desde fecha (aaaa/mm/dd) </td>
		    <td class="listado2">
				<script language="javascript">
				dateAvailable.writeControl();
				dateAvailable.dateFormat="yyyy/MM/dd";
				</script>
				&nbsp;
	  		</td>
	  	</tr>
	  <tr>
	    <td width="30%" class="titulos2">Hasta  fecha (aaaa/mm/dd) </td>
	    <td class="listado2">
			<script language="javascript">
			dateAvailable2.writeControl();
			dateAvailable2.dateFormat="yyyy/MM/dd";
			</script>&nbsp;
		</td>
	  </tr>
	<?php
	}
?>
<tr>
	    <td colspan="2" class="titulos2">
			<center>
				<input name="Submit" type="submit" class="botones_funcion" value="Limpiar">
				<input type="submit" class="botones_funcion" value="Generar" name="generarOrfeo">
			</center>
			</td>
		</tr>
<?		if($generarOrfeo && ($tipoEstadistica == 17 || $tipoEstadistica == 21)){
		print"
		<tr class='tablas'>					
			<td colspan='2' class='titulos2'  align='center'>
				Para obtener el archivo guarde el destino del
				siguiente v&iacute;nculo al archivo: 
				<a href='$archivo' target='_blank'>GENERADO</a>
			</td>			
		<tr>";
		die;
		}		
?>	</table>
</form>
<?
$datosaenviar = "fechaf=$fechaf" .
				"&tipoEstadistica=$tipoEstadistica" .
				"&codus=$codus" .
				"&krd=$krd" .
				"&dependencia_busq=$dependencia_busq" .
				"&ruta_raiz=$ruta_raiz" .
				"&fecha_ini=$fecha_ini" .
				"&fecha_fin=$fecha_fin" .
				"&tipoRadicado=$tipoRadicado" .
				"&tipoDocumento=$tipoDocumento" .
				"&serie_busq=$serie_busq" .
				"&subSerie_busq=$subSerie_busq";

//$db->conn->debug = true;
if (isset($generarOrfeo) && $tipoEstadistica == 12) {
	global $orderby;
	$orderby = 'ORDER BY NOMBRE';
	$whereDep = ($dependencia_busq != 99999) ? "AND h.DEPE_CODI = " . $dependencia_busq : '';
	$isqlus = "	SELECT	u.USUA_NOMB NOMBRE
						,u.USUA_DOC
						,d.DEPE_CODI
						,COUNT(r.RADI_NUME_RADI) TOTAL_MODIFICADOS
				FROM	USUARIO u,
			            RADICADO r,
			            HIST_EVENTOS h,
			            DEPENDENCIA d,
			            SGD_TPR_TPDCUMENTO s
				WHERE	u.USUA_DOC = h.USUA_DOC
			            AND h.SGD_TTR_CODIGO = 32
			            AND h.HIST_OBSE LIKE '*Modificado TRD*%'
			            AND h.DEPE_CODI = d.DEPE_CODI
			            $whereDep
			            AND s.SGD_TPR_CODIGO = r.TDOC_CODI
			            AND r.RADI_NUME_RADI = h.RADI_NUME_RADI
			            AND " .$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin'
				GROUP BY u.USUA_NOMB, u.USUA_DOC, d.DEPE_CODI $orderby";
	$rs1 = $db->conn->Execute($isqlus);
	while(!$rs1->EOF)  {
		$usuadoc[] = $rs1->fields["USUA_DOC"];
		$dependencias[] = $rs1->fields["DEPE_CODI"];
		$rs1->MoveNext();
	}
}

if($generarOrfeo) {
   include "gpgenEstadistica.php";
}

?>
</body>
</html>
<table  border="0" cellspace="2" cellpad="2" WIDTH="100%" class="borde_tab" align="center">
 <form name="jh"> 
  <input type="hidden" name="jj" value="0"> 
  <input type="hidden" name="dS" value="0">
 </form>
</table>