<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit;
}
else if (isset($_SESSION['krd'])) {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
$ruta_raiz = "..";	 
if(!$fecha_busq) $fecha_busq=date("d-m-Y");
if(!$fecha_busq2) $fecha_busq2=date("d-m-Y");
if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php";
  	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug = true;
define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
?>
<html>
	<head>
		<link rel="stylesheet" href="../estilos/orfeo.css">
	</head>
	<body>
		<div id="spiffycalendar" class="text"></div>
		<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
		<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"> </script>
		<script language="javascript">
			<!--
			var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formboton", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
			var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formboton", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
			//-->
		</script><P>
		<table width="100%" class='borde_tab' cellspacing="5">
			<tr>
				<td height="30" valign="middle"   class='titulos5' align="center">
					GENERACI&Oacute;N LISTADOS DE DOCUMENTOS DEVUELTOS POR AGENCIA DE CORREO
				</td>
			</tr>
		</table>
		<form name='formboton'  method='post'  action='generar_estadisticas.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2"?>'>
			<table width="550" class="borde_tab" align="center">
				<!--DWLayoutTable-->
				<tr>
					<TD width="125" height="21"  class='titulos5'>Fecha desde<br>
						<?php
						echo "($fecha_busq)";
						?>
					</TD>
					<TD width="415" align="right" valign="top" class='listado5'>
						<script language="javascript">
							dateAvailable.date = "05-08-2003";
							dateAvailable.writeControl();
							dateAvailable.dateFormat="dd-MM-yyyy";
						</script>
					</TD>
				</tr>
				<tr>
					<TD width="125" height="21"  class='titulos5'>Fecha Hasta<br>
						<?php
						echo "($fecha_busq2)";
						?>
					</TD>
					<TD width="415" align="right" valign="top" class='listado5'>
						<script language="javascript">
							dateAvailable2.date = "05-08-2003";
							dateAvailable2.writeControl();
							dateAvailable2.dateFormat="dd-MM-yyyy";
						</script>
					</TD>
				</tr>
				<tr>
					<TD height="26" class='titulos5'>Tipo de Salida</TD>
					<TD valign="top" align="left" class='listado5'>
						<?php
						$ss_RADI_DEPE_ACTUDisplayValue = "--- TODOS LOS TIPOS ---";
						$valor = '';
						include "../include/query/reportes/querytipo_envio.php";
						$sqlTS ="SELECT	$sqlConcat,
										SGD_FENV_CODIGO
								 FROM	SGD_FENV_FRMENVIO 
								 ORDER BY SGD_FENV_CODIGO";
						$rsTs = $db->conn->Execute($sqlTS);
						print $rsTs->GetMenu2("tipo_envio","$tipo_envio",$blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0," onChange='submit();' class='select'");	
						?>

				</tr>
				<tr>
					<TD height="26" class='titulos5'>Dependencia</TD>
					<TD valign="top" class='listado5'>
						<?php
						$ss_RADI_DEPE_ACTUDisplayValue = "--- TODAS LAS DEPENDENCIAS ---";
						$valor = 0;
						include "$ruta_raiz/include/query/devolucion/querydependencia.php";
						$sqlD ="SELECT  $sqlConcat,
										depe_codi
								FROM    dependencia 
								ORDER BY depe_codi";
								$rsDep = $db->conn->Execute($sqlD);
							   print $rsDep->GetMenu2("dep_sel","$dep_sel",$blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0," onChange='submit();' class='select'");	
						?>
				</tr>
				<tr>
					<td height="26" colspan="2" valign="top" class='titulos5' align="center">
						<input TYPE="submit" name='generar_informe' Value=' Generar Informe ' class='botones_mediano' />
					</td>
				</tr>
			</table>
		
<?php
	if(!$fecha_busq) $fecha_busq = date("d-m-Y");
	if($generar_informe) {
		if ($tipo_envio == 0) {
			$where_tipo = "";
		}
		else {
			$where_tipo = " and a.SGD_FENV_CODIGO = $tipo_envio ";
		}
		if ($dep_sel == 0) {
			//Seleccionar todas las dependencias de una territorial
			include "$ruta_raiz/include/query/devolucion/querydependencia.php";
			$sqlD ="SELECT	$sqlConcat,
							depe_codi
					FROM	dependencia 
					ORDER BY depe_codi";
			$rsDep = $db->conn->Execute($sqlD);
			while(!$rsDep->EOF) {
				$depcod = $rsDep->fields["DEPE_CODI"];
				$lista_depcod .= " $depcod,";
				$rsDep->MoveNext();
			}   
			$lista_depcod .= "0";
		}
		else {
			$lista_depcod = $dep_sel;
		}
		//Se limita la consulta al substring del numero de radicado de salida 27092005
		include "../include/query/reportes/querydepe_selecc.php";
		$generar_informe = 'generar_informe';
		$fecha_ini = $fecha_busq;
		$fecha_fin = $fecha_busq2;
		//$fecha_ini = mktime(00,00,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
		//$fecha_fin = mktime(23,59,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));
		$guion = "'-'";
		include "../include/query/reportes/querygenerar_estadisticas.php";

		if($tipo_envio=="101" or $tipo_envio=="108" or $tipo_envio=="109" ) {
			$where_isql.="and a.sgd_renv_planilla is not null 
						  and a.sgd_renv_planilla != '00' ";
		}
		if($tipo_envio==0) {
			$where_isql.=" and ((a.sgd_fenv_codigo != '101' 
								and a.sgd_fenv_codigo != '108' 
								and a.sgd_fenv_codigo != '109') 
						   or (a.sgd_renv_planilla is not null 
								and a.sgd_renv_planilla != '00'))";
		}
		
		$order_isql = " ORDER BY a.SGD_DEVE_FECH ASC";
		$query_t = $query . $where_isql . $where_depe . $where_tipo . $order_isql ;
		$ruta_raiz = "..";

		define('ADODB_FETCH_NUM',1);
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		require "../anulacion/class_control_anu.php"; 
		$db->conn->SetFetchMode(ADODB_FETCH_NUM);
		$btt = new CONTROL_ORFEO($db);
		$campos_align = array("L","C","L","L","L","L","L","L","L","L","L","L","L","L","L");
		$campos_tabla = array("sgd_fenv_descrip"  ,"depe_nomb","radi_nume_sal","sgd_renv_nombre","sgd_renv_dir","sgd_renv_mpio","sgd_renv_depto","sgd_renv_fech","sgd_deve_fech","sgd_deve_desc","firma");
		$campos_vista = array ("Forma Envio"      ,"Dependencia","Radicado","Destinatario","Direccion","Municipio","Departamento","Fecha de envio","Fecha Dev"         ,"Motivo Devolucion","Recibido");
		$campos_width = array (80           ,110          ,100        ,160           ,120        ,70         ,70           ,80            ,70                    ,150                ,110);	
		$btt->campos_align = $campos_align;
		$btt->campos_tabla = $campos_tabla;
		$btt->campos_vista = $campos_vista;
		$btt->campos_width = $campos_width;
?>
	
			</center>
			<table><tr><td></td></tr></table>
			<table><tr><td></td></tr></table>	
			<table class="borde_tab" width="100%"><tr class=titulos5><td colspan="2">
				Listado de documentos Devueltos
				</td></tr>
				<tr>
					<td width="15%" class="titulos5">Fecha Inicial </td>
					<td width="85%" class='listado5'> <?=$fecha_busq .  "  00:00:00" ?> </td>
				</tr>
				<tr> 
					<td class="titulos5">Fecha Final </td>
					<td class='listado5'><?=$fecha_busq2 . "  23:59:59" ?> </td>
				</tr>
				<tr>
					<td class="titulos5">Fecha Generado </td>
					<td class='listado5'><?php echo date("Ymd - H:i:s"); ?></td>
				</tr>
			</table>
		</form>	
		<table><tr><td></td></tr></table>
		<table><tr><td></td></tr></table>	
	</body>
</html>
		
<?php
		$btt->tabla_sql($query_t);
		$html= $btt->tabla_html;
		error_reporting(7);
		require "fpdf/html_table.php";
		error_reporting(7);
		$pdf = new PDF("L","mm","A4");
		$pdf->AddPage();
		$pdf->SetFont('Arial','',7);
		$entidad = $db->entidad;
		$encabezado = "<table border=1>
				<tr>
					<td width=1120 height=30>$entidad</td>
				</tr>
				<tr>
					<td width=1120 height=30>
						REPORTE DE DEVOLUCION DE DOCUMENTOS ENTRE $fecha_busq 00:00:00  y $fecha_busq2 23:59:59 
					</td>
				</tr>
			</table>";
		$fin = "<table border=1 bgcolor='#FFFFFF'>
				<tr>
					<td width=1120 height=60 bgcolor='#CCCCCC'>
						FUNCIONARIO CORRESPONDENCIA
					</td>
				</tr>
				<tr>
					<td width=1120 height=60> </td>
				</tr>
			</table>";
	
		$pdf->WriteHTML($encabezado . $html . $fin);
		$arpdf_tmp = "pdfs/planillas/dev/$dependencia_$krd_". date("Ymd_hms") . "_dev.pdf";
		$pdf->Output(BODEGAPATH.$arpdf_tmp, 'F');
		echo "<a href='".BODEGAURL.$arpdf_tmp."' target='".date("dmYh").time("his")."' class='vinculos'>Abrir Archivo Pdf</a>";
	}	
?>