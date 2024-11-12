<?php
###############################
## Editar con encoding UTF-8 ##
###############################
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
if (!isset($_SESSION['dependencia']) && !isset($_SESSION['depe_codi_territorial'])){
	include "../rec_session.php";
}
    
if(!$_POST['fecha_busq']){
	$fecha_busq = date("d-m-Y");
}
    
if(!$_POST['fecha_busq2'])	{
	$fecha_busq2 = date("d-m-Y");
}	

include($ruta_raiz.'/config.php');
include_once "Anulacion.php";
include_once "$ruta_raiz/include/tx/Historico.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
if (isset($_POST['cancelarAnular'])) {
	$aceptarAnular = "";
	$actaNo = "";
}
?>
<html>
	<head>
		<link rel="stylesheet" href="<?php echo $ruta_raiz; ?>/estilos/orfeo.css">
		<script>
			function soloNumeros() {
				jh =  document.new_product.actaNo.value;
				if(jh) {
					var1 = parseInt(jh);
					if(var1 != jh) {
						//document.forma.submit();
						return false;
					} else {
						document.new_product.anular.value = true;
						document.new_product.submit();
					}
				} else {
					document.new_product.anular.value = true;
					document.new_product.submit();
				}
			}
		</script>
	</head>
	<body>
	<div id="spiffycalendar" class="text"></div>
	<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css" >
	<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>	
	<script language="javascript">
	<!--
		var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
		var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "new_product", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
	//-->
	</script>
	
	<p>
	<table width="100%" class='borde_tab' cellspacing="5">
		<tr>
			<td height="30" valign="middle"   class='titulos5' align="center">
				Anulaci&oacute;n de Radicados por Dependencia
			</td>
		</tr>
	</table>

	<form name="new_product"  action='anularRadicados.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah"?>' method="post">
			<center>
			<table width="550" class='borde_tab' cellspacing='5'>
				<!--DWLayoutTable-->
				<tr>
					<td width="125" height="21"  class='titulos2'> Fecha desde<br>

<?php
echo "($fecha_busq)";
?>

					</td>
					<td width="500" align="right" valign="top" class="listado2">
						<script language="javascript">
							dateAvailable.date = "05-08-2003";
							dateAvailable.writeControl();
							dateAvailable.dateFormat="dd-MM-yyyy";
						</script>
					</td>
				</tr>
				<tr>
					<td width="125" height="21"  class='titulos2'> Fecha Hasta<br>

<?php
echo "($fecha_busq2)";
?>

					</td>
					<td width="500" align="right" valign="top"  class='listado2'>
						<script language="javascript">
							 dateAvailable2.date = "05-08-2003";
							 dateAvailable2.writeControl();
							 dateAvailable2.dateFormat="dd-MM-yyyy";
						</script>
					</td>
				</tr>
				<tr>
					<td height="26" class='titulos2'>Tipo Radicacion</td>
					<td valign="top" align="left"  class='listado2'>

<?php
$sqlTR ="SELECT UPPER(sgd_trad_descr),
				sgd_trad_codigo FROM sgd_trad_tiporad 
		WHERE	sgd_trad_codigo != 2
        ORDER BY sgd_trad_codigo";
$rsTR = $db->conn->Execute($sqlTR);
    
print $rsTR->GetMenu2("tipoRadicado","$tipoRadicado",false, false, 0," class='select'");
?>    

					</td>
				</tr>
				<tr>
					<td height="26" class='titulos2'>Dependencia</td>
					<td valign="top" align="left"  class='listado2'>

<?php
$sqlD ="SELECT	depe_nomb,
				depe_codi
		FROM	dependencia 
		WHERE	depe_codi_territorial = ".$_SESSION['depe_codi_territorial'].
		" ORDER BY depe_codi";
$rsD = $db->conn->Execute($sqlD);

print $rsD->GetMenu2("depeBuscada", "$depeBuscada", false, false, 0, " class='select'>\n <option value='0'>--- TODAS LAS DEPENDENCIAS --- </option>\n");
    //if(!$depeBuscada) $depeBuscada=$dependencia;
?>    
					</td>
				</tr>
				<tr>
					<td height="26" colspan="2" valign="top" class='titulos2'> 
						<center>
							<input type="submit" name="generar_informe" value="Ver Documentos En Solicitud" class="botones_funcion">
						</center>
					</td>
				</tr>
			</table>
			<hr>
			
<?php
if(!$fecha_busq) { 
	$fecha_busq = date("d-m-Y");
}

if($aceptar1 and !$actaNo and !$cancelarAnular){
	die ("<font color='red'>\n
                <span class='etextomenu'>\n
                    Debe colocal el Numero de acta para poder anular los radicados
                </span>\n
            </font>\n");
}

if(($generar_informe or $aceptarAnular) and !$cancelarAnular) {
    if($depeBuscada and $depeBuscada != 0) {
	    $whereDependencia = " b.DEPE_CODI = $depeBuscada AND";
	}
    
	include_once("../include/query/busqueda/busquedaPiloto1.php");
	include "$ruta_raiz/include/query/anulacion/queryanularRadicados.php";
	$fecha_ini = $fecha_busq;
    $fecha_fin = $fecha_busq2;
    $fecha_ini = mktime(00,00,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
    $fecha_fin = mktime(23,59,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));
    $query = "	SELECT	$radi_nume_radi radi_nume_radi,
				        r.radi_fech_radi,
                        r.ra_asun,
                        r.radi_usua_actu,
                        r.radi_depe_actu,
                        r.radi_usu_ante,
                        c.depe_nomb,
                        b.sgd_anu_sol_fech,
                        ".$db->conn->substr."(b.sgd_anu_desc, 21,62) sgd_anu_desc
                 FROM	radicado r,
                        sgd_anu_anulados b,
                        dependencia c";
	$fecha_mes = substr($fecha_ini,0,7);
        
    // Si la variable $generar_listado_existente viene entonces este if genera la planilla existente
    $where_isql = " WHERE $whereDependencia	b.sgd_anu_sol_fecH BETWEEN " .
                          $db->conn->DBTimeStamp($fecha_ini) . " AND " .
                          $db->conn->DBTimeStamp($fecha_fin) .
                          " AND SGD_EANU_CODI = 1 $whereTipoRadi AND
                               r.radi_nume_radi = b.radi_nume_radi AND
                               b.depe_codi = c.depe_codi";
    $order_isql = " ORDER BY b.depe_codi, b.SGD_ANU_SOL_FECH";
    $query_t = $query . $where_isql . $order_isql ;
        
    // Verifica el ultimo numero de acta del tipo de radicado
    $queryk ="SELECT	MAX (usua_anu_acta)
              FROM		sgd_anu_anulados
              WHERE		sgd_eanu_codi=2 and
                        sgd_trad_codigo = $tipoRadicado	";
            
	$c = $db->conn->Execute($queryk);
	$rsk = $db->conn->Execute($queryk);
        
	//require "$ruta_raiz/class_control/class_control.php";
    require "../anulacion/class_control_anu.php";
	$db->conn->SetFetchMode(ADODB_FETCH_NUM);
	$btt = new CONTROL_ORFEO($db);
	$campos_align = array("C","L","L","L","L","L","L","L","L","L","L","L");
	$campos_tabla = array("depe_nomb","radi_nume_radi","sgd_anu_sol_fech", "sgd_anu_desc");
	$campos_vista = array ("Dependencia","Radicado","Fecha de Solicitud", "Observacion Solicitante");
	$campos_width = array (200          ,100        ,280           ,300       );
	$btt->campos_align = $campos_align;
	$btt->campos_tabla = $campos_tabla;
	$btt->campos_vista = $campos_vista;
	$btt->campos_width = $campos_width;
?>
		<table width="100%" class="borde_tab" cellspacing="3">
			<tr>
				<td height="30" valign="middle" class='titulos5' align="center" colspan="2">
					Documentos con solicitud de Anulaci&oacute;n
				</td>
			</tr>
			<tr>
				<td width="16%" class='titulos5'>Fecha Inicial</td>
				<td width="84%" class='listado5'><?=$fecha_busq ?></td>
			</tr>
			<tr>
				<td class='titulos5'>Fecha Final</td>
				<td class='listado5'><?=$fecha_busq2 ?></td>
			</tr>
			<tr>
				<td class='titulos5'>Fecha Generado</td>
				<td class='listado5'><?php echo date("Ymd - H:i:s"); ?></td>
			</tr>
		</table>
<?php
	$numRegistros = $btt->tabla_sql($query_t);
	$html = $btt->tabla_html;
	$radAnular = $btt->radicadosEnv;
	$radObserva = $btt->radicadosObserva;
}

if(isset($_POST['generar_informe'])) {
?>
		<center>
			<span  class="listado2"> <br>
				Si est&aacute; seguro de Anular estos documentos por favor escriba el numero de acta y presione aceptar.
				<br> <br> &Uacute;ltima acta generada de este tipo de radicado es la No. 
				<b>

<?php
	echo (!empty($rsk->fields["0"])) ? $rsk->fields["0"] : "";
?>
					
				</b> <br> <br> Acta No.

<?php
    $noActaSig = 0;
    $noActaSig = (!empty($rsk->fields["0"]) && ($rsk->fields["0"] >= 0)) ? $rsk->fields["0"] + 1 : 1;
?>
    
				<input type="text" name="actaNo" class="tex_area" value="<?php echo $noActaSig; ?>"> <br>
				<table class="borde_tab" align="center">
					<tr>
						<td>
							<input type="button" name="AcepAnular" value="Aceptar" class="botones" onClick="soloNumeros();">
							<input type="hidden" name="anular" value="false">
							<input type="hidden" name="aceptarAnular" value="Aceptar" class="ebutton" onClick="soloNumeros();"> 
						</td>
						<td>
							<input type="submit" name="cancelarAnular" value="Cancelar" class="botones">
						</td>
					</tr>
				</table>
			</span>
		</center>
		
<?php
}

//Se le asigna a actaNo el No. de acta que debe seguir
if($anular == "true" and $actaNo) {
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    $db = new ConnectionHandler("$ruta_raiz");
    // inclusion territorial
    
	if ($depeBuscada == 0 ) {
		$sqlD = "SELECT depe_nomb,depe_codi
                 FROM	dependencia
                 WHERE	depe_codi_territorial = ". $_SESSION['depe_codi_territorial'].
                 " ORDER BY depe_codi";
		$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$rsD = $db->conn->Execute($sqlD);
		while(!$rsD->EOF) {
			$depcod = $rsD->fields["DEPE_CODI"];
			$lista_depcod .= " $depcod,";
			$rsD->MoveNext();
		}
		
        $lista_depcod .= "0";
	} 
	else {
		$lista_depcod = $depeBuscada;
	}
	
	$where_depe = " and (depe_codi) in ($lista_depcod )";
    
	// fin inclusion
    // Variables que manejan el tipo de Radicacion
    $isqlTR = 'SELECT	sgd_trad_descr,sgd_trad_codigo
               FROM		sgd_trad_tiporad
               WHERE	sgd_trad_codigo = '. $tipoRadicado;
        
	$rsTR = $db->conn->Execute($isqlTR);
    if ($rsTR) {
		$TituloActam = $rsTR->fields["SGD_TRAD_DESCR"];
	}
	else {
		$TituloActam = "sin titulo ";
	}
	
	$dbSel = new ConnectionHandler("$ruta_raiz");
	$dbSel->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$rsSel = $dbSel->conn->Execute($query_t);
	$i = 0;
	$radAnularE = array();
	
	if ($rsSel && !$rsSel->EOF) {
    	while(!$rsSel->EOF) {
    		$radAnularE[$i] = $rsSel->fields['RADI_NUME_RADI'];
    		$radObservaE[$i]= $rsSel->fields['SGD_ANU_DESC'];
    		$i++;
    		$rsSel->MoveNext();	
    	}
	}
	
    if(empty($radAnularE[0])) {
		die("<p>
                    <span class='etextomenu'>
                        <center>
                            <font color='red'>NO HAY RADICADOS PARA ANULAR</font>
                        </center>
                    </span>
                </p>");
	}
	else {
		// $where_TipoRadicado incluido 03082005 para filtrar por tipo radicacion del anulado
		$where_TipoRadicado = " and sgd_trad_codigo = " . $tipoRadicado;
		$Anulacion = new Anulacion($db);
		$observa   = "Radicado Anulado. (Acta No $actaNo)";
		$noArchivo = "pdfs/planillas/ActaAnul_$dependencia"."_"."$tipoRadicado"."_"."$actaNo.pdf";
		$radicados = $Anulacion->genAnulacion(  $radAnularE,
                                                $dependencia,
                                                $usua_doc,
                                                $observaE,
                                                $codusuario,
                                                $actaNo,
                                                $noArchivo,
                                                $where_depe,
                                                $where_TipoRadicado,
                                                $tipoRadicado,
                                                $rsk->fields["0"]);
		$Historico = new Historico($db);
		
		$radicados = $Historico->insertarHistorico( $radAnularE,
                                                    $dependencia,
                                                    $codusuario,
                                                    $depe_codi_territorial,
                                                    1,
                                                    $observa,
                                                    26); 
		//define('FPDF_FONTPATH','../fpdf/font/');
		$radAnulados = join(",", $radAnularE);
		$radicadosPdf = '<tr><td width="20%" align="center"><b>Radicado</b></td><td width="80%" align="center"><b>Observacion Solicitante</b></td></tr>';
		
		foreach($radAnularE as $id=>$noRadicado) {
		    $radicadosPdf .= '<tr><td align="center">'.$radAnularE[$id].'</td><td align="justify">'.iconv('ISO-8859-1', 'UTF-8', $radObservaE[$id]).'</td></tr>';
		}
		
		
		$anoActual = date("Y");
		$ruta_raiz = "..";
		
		$fecha = date("d-m-Y");
		$fecha_hoy_corto = date("d-m-Y");
		
		include "$ruta_raiz/class_control/class_gen.php";
		$b = new CLASS_GEN();
		$date =  date("m/d/Y");
		$fecha_hoy =  $b->traducefecha($date);
		
		
		$hoy = "Se firma la presente el ".$fecha_hoy;
		$ruta_raiz = "..";
		
		
		#############################################################################
		###	CREACION DE LA IMAGEN DEL RADICADO RESPUESTA - PDF
	
		require_once "tcpdf/tcpdf.php";
		
		class MYPDF extends TCPDF 
		{
			## Encabezado de la pagina
			public function Header() {
				## Logo
				$this->Image('../img/banerPDF.JPG',30,10,167,'','JPG','','T',false,300,'',false,false,0,false,false,false);
			}

			## Pie de pagina
			public function Footer() {
				## Posicion a 15 mm de la parte inferior
				$this->SetY(-20);
				## Numero de pagina
				$txt = "Calle 26 # 13-19 C&oacute;digo Postal 110311 Bogot&aacute;, D.C., Colombia PBX 381 5000 www.dnp.gov.co";
				$this->writeHTMLCell($w=0,$h=3,$x='20',$y='',$txt,$border=0,$ln=1,$fill=0,$reseth=true,'C');
			}
		}
		
		## Crea el documento en PDF
		$pdf = new MYPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
		## Informacion general del PDF
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Sistema de Gestion Documental Orfeo');
		$pdf->SetTitle('Acta de anulacion de radicados');
		$pdf->SetSubject('Departamento Nacional de Planeacion');
		$pdf->SetKeywords('dnp, acta, radicados, anulados');

		## Se establecen datos para la cabecera
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		## Se establecen Fuentes para el encabezado y pie de pagina
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		## Se define la fuente predeterminada
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		## Se establecen margenes
		$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		## Se establecen saltos de pagina automaticos
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		## Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		## Set some language-dependent strings
		$pdf->setLanguageArray($l);

		## Set default font subsetting mode
		$pdf->setFontSubsetting(true);
			
		## Adiciona una nueva pagina
		## This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
    $asu = '<table>
            <tr><th align="center" colspan="2">ACTA DE ANULACI&Oacute;N No. '.$actaNo.'</th></tr>
            <tr><td colspan="2"></td></tr>
            <tr><td align="justify" colspan="2">
                En cumplimiento a lo establecido en el Acuerdo No. 060 del 30 de octubre de 2001
                expedido por el Archivo General de la Naci&oacute;n, en el cual se establecen pautas para la
                administraci&oacute;n de las comunicaciones oficiales en las entidades p&uacute;blicas y privadas que
                cumplen funciones  p&uacute;blicas, y con base especialmente en el par&aacute;grafo del Art&iacute;culo
                Quinto, el cual establece que cuando existan errores en la radicaci&oacute;n y se anulen los
                n&uacute;meros, se debe dejar constancia por escrito, con la respectiva justificaci&oacute;n y firma del 
                Jefe de la unidad de correspondencia.
            </td></tr>
            <tr><td align="justify" colspan="2">
                El Coordinador del Grupo de Correspondencia del Departamento Nacional de 
                Planeaci&oacute;n procede a anular los siguientes n&uacute;meros de radicaci&oacute;n de '.
                strtolower($TituloActam).' que no fueron tramitados por las dependencias radicadoras:
            </td></tr>
            <tr><td colspan="2"></td></tr>
            <tr><td align="justify" colspan="2">1. N&uacute;meros de radicaci&oacute;n de '.strtolower($TituloActam).' a anular:</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>'.
            $radicadosPdf.
            '<tr><td colspan="2">&nbsp;</td></tr>
            <tr><td align="justify" colspan="2">2. Se deja copia de la presente acta en el archivo central de la Entidad para el tr&aacute;mite 
            respectivo de la organizaci&oacute;n f&iacute;sica de los archivos.</td></tr>
            <tr><td colspan="2"></td></tr>
            <tr><td colspan="2" align="justify">'.$hoy.'</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2" align="left">'.iconv('ISO-8859-1', 'UTF-8', $usua_nomb).'</td></tr>
            <tr><td colspan="2" align="left">Coordinador Grupo de Correspondencia.</td></tr>
            </table>';
		## Salida del contenido HTML
		$okw = $pdf->writeHTML($asu, true, false, true, false, '');
		
		// Cierra el documento PDF
		// This method has several options, check the source code documentation for more information.		
		$okf = $pdf->Output(BODEGAPATH.$noArchivo, 'F');

        
		echo '<table width="100%"><tr><td class="titulos5" align="center">Ver Acta <a href="' . BODEGAURL.$noArchivo .'">
				<span class="no_leidos">Acta No ' . $actaNo .
				'</span></a></center></td></tr></table>' . "\n";
		exit();
	}
}
?>
		</center>
		</form>
	</body>
</html>
