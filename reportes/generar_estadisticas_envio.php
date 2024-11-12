<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit;
}
else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
$anoActual = date("Y");
if(!$fecha_busq)	$fecha_busq=date("d-m-Y");
if(!$fecha_busq2)	$fecha_busq2=date("d-m-Y");
if (!isset($_SESSION['dependencia']) and !isset($_SESSION['depe_codi_territorial']))
	include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug = true;
if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
?>
<html>
	<head>
		<link rel="stylesheet" href="../estilos/orfeo.css">
		<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
	</head>
	<BODY>
		<div id="spiffycalendar" class="text"></div>
		<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
		<script language="javascript">
			<!--
			var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
			var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "new_product", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
			//-->
		</script>
		<TABLE width="100%" class='borde_tab' cellspacing="5">
			<TR>
				<TD height="30" valign="middle"   class='titulos5' align="center">
					LISTADO DE DOCUMENTOS ENVIADOS POR AGENCIA DE CORREO
				</TD>
			</TR>
		</TABLE>
		<table><tr><td></td></tr></table>
		<form name="new_product"  action='../reportes/generar_estadisticas_envio.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2"?>' method='post'>
				<TABLE align="center" width="550" class='borde_tab'>
					<!--DWLayoutTable-->
					<TR>
						<td width="150" height="21"  class='titulos5'> 
							Fecha desde<br> <?php echo "($fecha_busq)";	?>
						</td>
						<td width="415" align="left" valign="top">
							<script language="javascript">
								dateAvailable.date = "05-08-2003";
								dateAvailable.writeControl();
								dateAvailable.dateFormat="dd-MM-yyyy";
							</script>
						</td>
					</TR>
					<tr>
						<TD width="150" height="21"  class='titulos5'>
							Fecha Hasta<br> <?php echo "($fecha_busq2)"; ?>
						</TD>
						<td width="415" align="left" valign="top">
							<script language="javascript">
								dateAvailable2.date = "05-08-2003";
								dateAvailable2.writeControl();
								dateAvailable2.dateFormat="dd-MM-yyyy";
							</script>
						</td>
					</tr>
					<tr>
						<td height="26" class='titulos5'>Tipo de Salida</td>
						<td valign="top" align="left">
							<?php
								$ss_RADI_DEPE_ACTUDisplayValue = "--- TODOS LOS TIPOS ---";
								$valor = '';
								include "../include/query/reportes/querytipo_envio.php";
								$sqlTS = "	SELECT	$sqlConcat,
													SGD_FENV_CODIGO 
											FROM	SGD_FENV_FRMENVIO 
											ORDER BY SGD_FENV_CODIGO";
								$rsTs = $db->conn->Execute($sqlTS);
								print $rsTs->GetMenu2("tipo_envio",
													$_POST['tipo_envio'],
													$blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue",
													false,
													0,
													" class='select'");
							?>
						</td>
					</tr>
					<tr>
						<td height="26" colspan="2" valign="top" class='titulos5'> 
							<center>
								<INPUT TYPE=SUBMIT name="generar_informe" value=' Generar Informe ' class="botones_mediano">
							</center>
						</td>
					</tr>
				</TABLE>
				
				<?php
					if(!$fecha_busq) 
						$fecha_busq = date("d-m-Y");
					if(isset($_POST['generar_informe'])) {
						if ($_POST['tipo_envio'] == '') { 
							$where_tipo = ""; 
						}
						else { 
							$where_tipo = " and a.SGD_FENV_CODIGO = $tipo_envio ";
						}
						
						if (isset($_POST['dep_sel'])) {
							$lista_depcod = implode(',', $_POST['dep_sel']);
						}
						else {
							$lista_depcod = "";
						}
						
						//Se limita la consulta al substring del numero de radicado de salida 27092005
						include "../include/query/reportes/querydepe_selecc.php";
						if (empty($lista_depcod))
							$where_depe = "";
						$generar_informe = 'generar_informe';
						$fecha_ini = $fecha_busq;
						$fecha_fin = $fecha_busq2;
						//$fecha_ini = mktime(00,00,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
						//$fecha_fin = mktime(23,59,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));
						$guion = "' '";
						$select102 = "";
						$inner102 = "";
						$campos_align = array("C","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
						$campos_tabla = array("DEPENDENCIA","RADICADO","DESTINATARIO","DIRECCION","EMAIL","MUNICIPIO_DESTINO","DEPARTAMENTO_DESTINO","FECHA_DE_ENVIO","FORMA_DE_ENVIO","USUARIO");
						$campos_vista = array("Dependencia","Radicado","Destinatario","Direccion","Email","Municipio","Departamento","Fecha de envio","Forma de envio","Usuario");
						$campos_width = array (200          ,110        ,280           ,300        ,200        ,80            ,80          ,80          , 80           ,100);
						switch ($tipo_envio) {
							case 101:
							case 108:
							case 109:
								$where_isql.= " AND a.SGD_RENV_PLANILLA IS NOT NULL 
												AND a.SGD_RENV_PLANILLA != '00'";
								break;
							case 102:	//interrapidisimo
								$select102 = "	x.dpto_nomb as DEPARTAMENTO_ORIGEN, 
												m.muni_nomb as MUNICIPIO_ORIGEN, 
												a.sgd_renv_telefono as TELEFONO,
												a.sgd_renv_observa as OBSERVACION,";
								$inner102 = " inner join departamento x on 
												x.id_cont = c.id_cont and 
												x.id_pais = c.id_pais and 
												x.dpto_codi = c.dpto_codi ".
											" inner join municipio m on 
												m.id_cont = c.id_cont and 
												m.id_pais = c.id_pais and 
												m.dpto_codi = c.dpto_codi and
												m.muni_codi = c.muni_codi ";
								$campos_align = array("C","L","L","L","L","L","L","L","L","L","L","L","L","L");
								$campos_tabla = array("DEPENDENCIA","RADICADO","DESTINATARIO","DIRECCION","EMAIL","TELEFONO","OBSERVACION","MUNICIPIO_DESTINO","DEPARTAMENTO_DESTINO", "MUNICIPIO_ORIGEN", "DEPARTAMENTO_ORIGEN","FECHA_DE_ENVIO","FORMA_DE_ENVIO, USUARIO");
								$campos_vista = array("Dependencia","Radicado","Destinatario","Direccion","Email","Telefono","Observacion","Municipio(D)"     ,"Departamento(D)"     ,"Municipio(O)"       ,"Departamento(O)"   ,"Fecha de envio","Forma de envio, USUARIO");
								$campos_width = array(200          ,110            ,230              ,200        , 200 ,     60        ,100          ,80            ,80               ,80                   ,80                 ,80              ,80             , 100);
								break;
							default:{
								$where_isql.= "	and ((a.sgd_fenv_codigo != '101' 
												and	a.sgd_fenv_codigo != '108'
												and a.sgd_fenv_codigo != '109'
												and a.sgd_fenv_codigo != '102' ) or (a.sgd_renv_planilla is not null 
												and a.sgd_renv_planilla != '00'))";
							}
							break;
						}
						include "$ruta_raiz/include/query/reportes/querygenerar_estadisticas_envio.php";
						$query_t = $query;
						$ruta_raiz = "..";
						if (!defined('ADODB_FETCH_NUM')) 
							define('ADODB_FETCH_NUM',1);
						
						$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
						require "../anulacion/class_control_anu.php";
						$db->conn->SetFetchMode(ADODB_FETCH_NUM);
						$btt = new CONTROL_ORFEO($db);
						$btt->campos_align = $campos_align;
						$btt->campos_tabla = $campos_tabla;
						$btt->campos_vista = $campos_vista;
						$btt->campos_width = $campos_width;
				?>
			<span class="etextomenu">
			<b>Listado de documentos Enviados</b><br>
			Fecha Inicial <?=$fecha_busq .  "  00:00:00" ?> <br>
			Fecha Final   <?=$fecha_busq2 . "  23:59:59" ?> <br>
			Fecha Generado <?php echo date("dmY - H:i:s"); ?> 
			
				<?php
						//define('FPDF_FONTPATH','../fpdf/font/');
						require "fpdf/html_table.php";
						$pdf = new PDF("L","mm","Legal");
						$pdf->AddPage();
						$pdf->SetFont('Arial','',7);
						$entidad = $db->entidad;
						$encabezado = "<table border=1>
								<tr> 
									<td width=".array_sum($campos_width)." height=30>$entidad </td> 
								</tr>
								<tr>
									<td width=".array_sum($campos_width)." height=30>REPORTE DE DOCUMENTOS ENVIADOS ENTRE $fecha_busq   00:00:00  y $fecha_busq2   23:59:59 </td> 
								</tr>
							</table>";
						$fin = "<table border=1 bgcolor='#FFFFFF'>
									<tr> 
										<td width=".array_sum($campos_width)." height=60 bgcolor='#CCCCCC'>FUNCIONARIO CORRESPONDENCIA</td>
									</tr>
									<tr> 
										<td width=".array_sum($campos_width)." height=60> </td> 
									</tr>
								</table>";
						$pdf->WriteHTML($encabezado . $html . $fin);
						$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
						$rs = $db->conn->Execute($query_t);

						if ($rs) {
						    $path = "tmp/Envio".$tipo_envio."_".$tipo_envio."_".date("dmYh").time("his").".csv";
						    $fp = fopen(BODEGAPATH.$path, "w");
							if ($fp) {
								include_once "adodb/toexport.inc.php";
								rs2csvfile($rs, $fp); # write to file (there is also an rs2tabfile function)
								fclose($fp);
							}
							$rs2 = $db->conn->Execute($sqlSipost);
							if ($rs2) {
								$path2 = "tmp/Envio".$tipo_envio."_".$tipo_envio."_".date("dmYh").time("his")."sipost.csv";
								$sp = fopen(BODEGAPATH.$path2, "w");
								if ($sp) {
									include_once "adodb/toexport.inc.php";
									rs2csvfile($rs2, $sp); # write to file (there is also an rs2tabfile function)
									fclose($sp);
								}
								//$a = mb_detect_encoding($sp, "ASCII, UTF8");
							}
						}
						else
							$resultado = 'Hubo un error en craci�n del archivo csv.';
						$arpdf_tmp = "pdfs/planillas/envios/$dependencia_$krd". date("Ymd_hms") . "_envio.pdf";
						$pdf->Output('F', BODEGAPATH.$arpdf_tmp, false);
						echo "<br>
								<a href='".BODEGAURL.$arpdf_tmp."' target='".date("dmYh").time("his")."'>
									<img src='../imagenes/pdf.png' border='0'>
								</a> &nbsp; &nbsp; &nbsp;".
								"<a href='".BODEGAURL.$path."' target='_blank'>
									<img src='../imagenes/csv.png' border='0'>
								</a> &nbsp; &nbsp; &nbsp;".
								"<a href='".BODEGAURL.$path2."' target='_blank'>
									<img src='../imagenes/sipost-csv.png' border='0'>
								</a>";

						$btt->tabla_sql($query_t);
						$html= $btt->tabla_html;
					}
				?>
			</center>
		</form>
	</BODY>
</html>