<?php
session_start();
$ruta_raiz = "..";
require_once($ruta_raiz . "/" . "_conf/constantes.php");
require_once(ORFEOPATH . "include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$krdAnt = $krd;
$ruta_raiz="..";
if(!$krd)  $krd = $krdAnt;
if(!isset($_SESSION['dependencia']) or !isset($_SESSION['nivelus']))   include "../rec_session.php";
include ("common.php");
$fechah = date("ymd") . "_" . time("hms");
$sFileName = "busquedaPiloto.php";
$usu = $krd;
$niv = $nivelus;
if (strlen($niv)){
	set_session("UserID",$usu);
	set_session("krd",$krd);
	set_session("Nivel",$niv);
}

$sAction          = $_POST["FormAction"];
$krd              = get_param("krd");
$sForm            = $_POST["FormName"];
$flds_ciudadano   = $_POST["s_ciudadano"];
$flds_empresaESP  = $_POST["s_empresaESP"];
$flds_oEmpresa    = $_POST["s_oEmpresa"];
$flds_FUNCIONARIO = $_POST["s_FUNCIONARIO"];

//Proceso de vinculacion al vuelo
$indiVinculo = get_param("indiVinculo");
$verrad      = get_param("verrad");
$carpAnt     = get_param("carpAnt");
$nomcarpeta  = get_param("nomcarpeta");
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css" type="text/css">
	</head>
	<script>
		function limpiar() {
			document.Search.elements['s_RADI_NUME_RADI'].value = "";
			document.Search.elements['s_RADI_NOMB'].value = "";
			document.Search.elements['s_RADI_DEPE_ACTU'].value = "";
			document.Search.elements['s_TDOC_CODI'].value = "9999";
			/**
			  * Limpia el campo expediente
			  * Fecha de modificacion: 30-Junio-2006
			  * Modificador: Supersolidaria
			  */
			document.Search.elements['s_SGD_EXP_SUBEXPEDIENTE'].value = "";
			document.Search.elements['s_bpin'].value = "";

<?php
$dia = intval(date("d"));
$mes = intval(date("m"));
$ano = intval(date("Y"));
?>

			document.Search.elements['s_desde_dia'].value= "<?=$dia?>";
			document.Search.elements['s_hasta_dia'].value= "<?=$dia?>";
			document.Search.elements['s_desde_mes'].value= "<?=($mes-1)?>";
			document.Search.elements['s_hasta_mes'].value= "<?=$mes?>";
			document.Search.elements['s_desde_ano'].value= "<?=$ano?>";
			document.Search.elements['s_hasta_ano'].value= "<?=$ano?>";
			for(i=4;i<document.Search.elements.length;i++)
				document.Search.elements[i].checked=1;
		}

		function selTodas() {
			if(document.Search.elements['s_Listado'].checked==true) {
				document.Search.elements['s_ciudadano'].checked= false;
				document.Search.elements['s_empresaESP'].checked= false;
				document.Search.elements['s_oEmpresa'].checked= false;
				document.Search.elements['s_FUNCIONARIO'].checked= false;
			} else {
				document.Search.elements['s_ciudadano'].checked= true;
				document.Search.elements['s_empresaESP'].checked= false;
				document.Search.elements['s_oEmpresa'].checked= false;
				document.Search.elements['s_FUNCIONARIO'].checked= false;
			}
		}

		function delTodas() {
			document.Search.elements['s_Listado'].checked=false;
			document.Search.elements['s_ciudadano'].checked= false;
			document.Search.elements['s_empresaESP'].checked= false;
			document.Search.elements['s_oEmpresa'].checked= false;
			document.Search.elements['s_FUNCIONARIO'].checked= false;
		}

		function selListado() {
			if(document.Search.elements['s_ciudadano'].checked== true ||
				document.Search.elements['s_empresaESP'].checked== true ||
				document.Search.elements['s_oEmpresa'].checked== true ||
				document.Search.elements['s_FUNCIONARIO'].checked== true) {
				document.Search.elements['s_Listado'].checked=false;
			}
		}

		function noPermiso(){
			alert ("No tiene permiso para acceder");
		}

		function pasar_datos(fecha) {

<?php
echo " opener.document.VincDocu.numRadi.value = fecha\n";
echo "opener.focus(); window.close();\n";
?>

		}
	</script>

	<body class="PageBODY">
		<table>
			<tr>
				<td valign="top" width="80%"> <?php Search_show() ?> </td>
		
<?php
$query = "	SELECT	SGD_ROL_CODIGO
			FROM	USUARIO
			WHERE	USUA_LOGIN = '$krd'";
$rol = $db->conn->Getone($query);
		
## Se valida si el usuario logeado no tiene ROL Auditor (3)
if ($rol != 3){
?>
		
				<td valign="top">
					<a class="vinculos" href="busquedaHist.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd" ?>">Busqueda por historico</a><br>
					<a class="vinculos" href="busquedaUsuActu.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd" ?>">Reporte por Usuarios</a><br>
					<a class="vinculos" href="../busqueda/busquedaExp.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php ECHO "&fechah=$fechah&primera=1&ent=2"; ?>">Busqueda Expediente</a>
				</td>
		
<?php
}
?>
		
			</tr>
		</table>
		<table>
			<tr>
				<td valign="top">

<?php
if($Busqueda or $s_entrada) {
	if($s_Listado=="VerListado") {
?>
			<tr>
				<td valign="top">

<?php
		if($flds_ciudadano=="CIU")	 $whereFlds .= "1,";
		if($flds_empresaESP=="ESP")  $whereFlds .= "2,";
		if($flds_oEmpresa=="OEM")	 $whereFlds .= "3,";
		if($flds_FUNCIONARIO=="FUN") $whereFlds .= "4,";
		$whereFlds .= "0";
		Ciudadano_show($nivelus,9,$whereFlds)
?>
				</td>
			</tr>

<?php
	} 
	else {
		if (!$etapa)
			if($flds_ciudadano=="CIU" || (!strlen($flds_ciudadano) && !strlen($flds_empresaESP) && !strlen($flds_oEmpresa) && !strlen($flds_FUNCIONARIO))) {
				Ciudadano_show($nivelus,1,1);
            }
?>
				<!--</td>
			</tr>-->
			<tr> <td valign="top"> <?php if($flds_empresaESP=="ESP") Ciudadano_show($nivelus,3,3); ?> </td> </tr>
			<tr> <td valign="top"> <?php if($flds_oEmpresa=="OEM") Ciudadano_show($nivelus,2,2); ?> </td> </tr>
			<tr> <td valign="top"> <?php if($flds_FUNCIONARIO=="FUN") Ciudadano_show($nivelus,4,4); ?> </td> </tr>

<?php
	}
}
?>

		</table>
	</body>
<!--</html>-->

<?php
function Search_show() 
{
	global $db;
	global $styles;
	global $db2;
	global $db3;
	global $sForm;
	$sFormTitle = "Busqueda Clasica";
	$sActionFileName = "busquedaPiloto.php";
	$ss_desde_RADI_FECH_RADIDisplayValue = "";
	$ss_hasta_RADI_FECH_RADIDisplayValue = "";
	$ss_TDOC_CODIDisplayValue = "Todos los Tipos";
	$ss_TRAD_CODIDisplayValue = "Todos los Tipos (-1,-2,-3,-5, . . .)";
	$ss_RADI_DEPE_ACTUDisplayValue = "Todas las Dependencias";
    //Con esta variable se determina si la busqueda corresponde a vinculacion documentos
	$indiVinculo = get_param("indiVinculo");
	$verrad = get_param("verrad");
	$carpeAnt = get_param("carpeAnt");
	$nomcarpeta = get_param("nomcarpeta");
	
	if ($indiVinculo == 1) { $sFormTitle = $sFormTitle . "  Anexo  al Vuelo "; }
	$flds_RADI_NUME_RADI = strip(get_param("s_RADI_NUME_RADI"));
	$flds_DOCTO     = strip(get_param("s_DOCTO"));
	$flds_RADI_NOMB = strip(get_param("s_RADI_NOMB"));
	$krd            = get_param("krd");
	$flds_ciudadano = strip(get_param("s_ciudadano"));
	if($flds_ciudadano) $checkCIU = "checked";
	$flds_empresaESP = strip(get_param("s_empresaESP"));
	if($flds_empresaESP) $checkESP = "checked";
	$flds_oEmpresa  = strip(get_param("s_oEmpresa"));
	if($flds_oEmpresa) $checkOEM = "checked";
	$flds_FUNCIONARIO = strip(get_param("s_FUNCIONARIO"));
	if($flds_FUNCIONARIO) $checkFUN = "checked";
	$flds_entrada   = strip(get_param("s_entrada"));
	$flds_salida    = strip(get_param("s_salida"));
	$flds_solo_nomb = strip(get_param("s_solo_nomb"));
	$flds_bpin 		= strip(get_param("s_bpin"));
	$Busqueda       = strip(get_param("Busqueda"));
	$flds_desde_dia = strip(get_param("s_desde_dia"));
	$flds_hasta_dia = strip(get_param("s_hasta_dia"));
	$flds_desde_mes = strip(get_param("s_desde_mes"));
	$flds_hasta_mes = strip(get_param("s_hasta_mes"));
	$flds_desde_ano = strip(get_param("s_desde_ano"));
	$flds_hasta_ano = strip(get_param("s_hasta_ano"));
	$flds_TDOC_CODI = strip(get_param("s_TDOC_CODI"));
	$s_Listado = strip(get_param("s_Listado"));
	$flds_RADI_DEPE_ACTU = strip(get_param("s_RADI_DEPE_ACTU"));	

    /**
      * Busqueda por expediente
      * Fecha de modificacion: 30-Junio-2006
      * Modificador: Supersolidaria
      */
	$flds_SGD_EXP_SUBEXPEDIENTE = strip( get_param( "s_SGD_EXP_SUBEXPEDIENTE" ) );

	if (strlen($flds_desde_dia) && strlen($flds_hasta_dia) &&
		strlen($flds_desde_mes) && strlen($flds_hasta_mes) &&
		strlen($flds_desde_ano) && strlen($flds_hasta_ano) ) {
			$desdeTimestamp = mktime(0,0,0,$flds_desde_mes, $flds_desde_dia, $flds_desde_ano);
			$hastaTimestamp = mktime(0,0,0,$flds_hasta_mes, $flds_hasta_dia, $flds_hasta_ano);
			$flds_desde_dia = Date('d',$desdeTimestamp);
			$flds_hasta_dia = Date('d',$hastaTimestamp);
			$flds_desde_mes = Date('m',$desdeTimestamp);
			$flds_hasta_mes = Date('m',$hastaTimestamp);
			$flds_desde_ano = Date('Y',$desdeTimestamp);
			$flds_hasta_ano = Date('Y',$hastaTimestamp);
	} 
	else { /*DESDE HACE UN MES HASTA HOY */
		$desdeTimestamp = mktime(0,0,0, Date('m')-1,  Date('d'),  Date('Y'));
		$flds_desde_dia = Date('d', $desdeTimestamp);
		$flds_hasta_dia = Date('d');
		$flds_desde_mes = Date('m', $desdeTimestamp);
		$flds_hasta_mes = Date('m');
		$flds_desde_ano = Date('Y', $desdeTimestamp);
		$flds_hasta_ano = Date('Y');
	}
?>

	<form method="post" action="<?= $sActionFileName ?>?<?=session_name()."=".session_id()?>&indiVinculo=<?=$indiVinculo?>&verrad=<?=$verrad?>&carpeAnt=<?=$carpeAnt?>&nomcarpeta=<?=$nomcarpeta?>&dependencia=<?=$dependencia?>&krd=<?=$krd?>" name="Search">
		<input type="hidden" name=<?=session_name()?> value=<?=session_id()?>>
		<input type="hidden" name=krd value=<?=$krd?>>
		<input type="hidden" name="FormName" value="Search">
		<input type="hidden" name="FormAction" value="search">
		<table  border=0 cellpadding=0 cellspacing=2 class='borde_tab'>
			<tr>
				<td  class="titulos4" colspan="13"><a name="Search"> <?=$sFormTitle?> </td>
			</tr>
			
			<tr>
				<td class="titulos5">Radicado</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_RADI_NUME_RADI" maxlength="" value="<?=tohtml($flds_RADI_NUME_RADI) ?>" size="" >
				</td>
			</tr>
			
			<tr>
				<td class="titulos5">Identificacion (T.I.,C.C.,Nit) *</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_DOCTO" maxlength="" value="<?=tohtml($flds_DOCTO) ?>" size="" >
				</td>
			</tr>
    
			<tr>
				<td class="titulos5">Expediente</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_SGD_EXP_SUBEXPEDIENTE" maxlength="" value="<?=tohtml($flds_SGD_EXP_SUBEXPEDIENTE) ?>" size="" >
				</td>
			</tr>

			<tr>
				<td class="titulos5">Bpin</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_bpin" maxlength="" value="<?=tohtml($flds_bpin) ?>" size="" >
				</td>
			</tr>

			<tr>
				<td class="titulos5">
					<input type="radio" NAME="s_solo_nomb" value="All" CHECKED <?if($flds_solo_nomb=="All"){ echo ("CHECKED");} ?>>
					Buscar Por<br>
					<!--<INPUT type="radio" NAME="s_solo_nomb" value="Any" <?php if($flds_solo_nomb=="Any"){echo ("CHECKED");} ?>><br>-->
				</td>
				<td class="listado5">
				  <input class="tex_area" type="text" name="s_RADI_NOMB" maxlength="70" value="<?=tohtml($flds_RADI_NOMB) ?>" size="70" >
				</td>
			</tr>
    
			<tr>
				<td colspan="2" class="FieldCaptionTD">
					<table>
						<tr>
							<td class="titulos5" width="15%">

<?php
	if($s_Listado=="VerListado") {
		$listadoView = " checked=checked";
	}
?>

								<input type="checkbox" NAME="s_Listado" value="VerListado" <?=$listadoView?> onClick="delTodas();document.Search.elements['s_Listado'].checked=true;">
								Ver en Listado 
							</td>
							<td  class="titulos5" width="15%">
								<INPUT type="checkbox" NAME="s_ciudadano" value="CIU" onClick="/*delTodas();*/document.Search.elements['s_ciudadano'].checked=true;" <?=$checkCIU?> >
								Buscar Ciudadanos
							</td>
							<td class="titulos5" width="20%">
								<INPUT type="checkbox" NAME="s_empresaESP" value="ESP" onClick="/*delTodas();*/document.Search.elements['s_empresaESP'].checked= true;" <?=$checkESP?> >
								Buscar en ESP's
							</td>
							<td class="titulos5" width="20%">
								<INPUT type="checkbox" NAME="s_oEmpresa" value="OEM" onClick="/*delTodas();*/document.Search.elements['s_oEmpresa'].checked=true;" <?=$checkOEM?> >
								Buscar en Empresas
							</td>
							<td width="20%"  class="titulos5">
								<INPUT type="checkbox" NAME="s_FUNCIONARIO" value="FUN" onClick="/*delTodas();*/document.Search.elements['s_FUNCIONARIO'].checked=true;" <?=$checkFUN?> >
								Buscar Funcionarios
							</td>
						</tr>
					
						<tr>
							<td colspan="5" class="titulos5"> </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="titulos5"> Buscar en Radicados de </td>
				<td class="listado5"> 
					<select class="select" name="s_entrada" >
			  
<?php
	if(!$s_Listado) $s_Listado="VerListado";
	if ($flds_entrada==0) $flds_entrada="9999";
	echo "<option value=\"9999\">" . $ss_TRAD_CODIDisplayValue . "</option>";
	$lookup_s_entrada = db_fill_array("select SGD_TRAD_CODIGO, SGD_TRAD_DESCR from SGD_TRAD_TIPORAD order by 2");

	if(is_array($lookup_s_entrada)) {
		reset($lookup_s_entrada);
		while(list($key, $value) = each($lookup_s_entrada)) {
			if($key == $flds_entrada) 
				$option="<option SELECTED value=\"$key\">$value</option>";
			else 
				$option="<option value=\"$key\">$value</option>";
			echo $option;
		}
	}
?>

					</select>
				</td>
			</tr>

			<tr>
				<td class="titulos5">Desde Fecha (dd/mm/yyyy)</td>
				<td class="listado5">
					<select class="select" name="s_desde_dia">
	
<?php
	for($i = 1; $i <= 31; $i++) {
		if($i == $flds_desde_dia) 
			$option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
		else 
			$option="<option value=\"" . $i . "\">" . $i . "</option>";
		echo $option;
	}
?>

					</select>
					<select class="select" name="s_desde_mes">
			  
<?php
	for($i = 1; $i <= 12; $i++) {
		if($i == $flds_desde_mes) 
			$option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
		else 
			$option="<option value=\"" . $i . "\">" . $i . "</option>";
		echo $option;
	}
?>

					</select>
					<select class="select" name="s_desde_ano">
			  
<?php
	$agnoactual=Date('Y');
	for($i = 1990; $i <= $agnoactual; $i++) {
		if($i == $flds_desde_ano) 
			$option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
		else 
			$option="<option value=\"" . $i . "\">" . $i . "</option>";
		echo $option;
	}
?>

					</select>
				</td>
			</tr>

			<tr>
				<td class="titulos5">Hasta Fecha (dd/mm/yyyy)</td>
				<td class="listado5">
					<select class="select" name="s_hasta_dia">
					
<?php
	for($i = 1; $i <= 31; $i++) {
		if($i == $flds_hasta_dia) 
			$option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
		else 
			$option="<option value=\"" . $i . "\">" . $i . "</option>";
		echo $option;
	}
?>

					</select>
					<select class="select" name="s_hasta_mes">
			  
<?php
	for($i = 1; $i <= 12; $i++) {
		if($i == $flds_hasta_mes) 
			$option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
		else 
			$option="<option value=\"" . $i . "\">" . $i . "</option>";
		echo $option;
	}
?>

					</select>
					<select class="select" name="s_hasta_ano">
			  
<?php
	for($i = 1990; $i <= $agnoactual; $i++) {
		if($i == $flds_hasta_ano) 
			$option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
		else 
			$option="<option value=\"" . $i . "\">" . $i . "</option>";
		echo $option;
	}
?>

					</select>
				</td>
			</tr>

			<tr>
				<td class="titulos5"><font class="FieldCaptionFONT">Tipo de Documento</td>
				<td class="listado5">
					<select class="select" name="s_TDOC_CODI">
			  
<?php
	if ($flds_TDOC_CODI==0) 
		$flds_TDOC_CODI="9999";
	echo "<option value=\"9999\">" . $ss_TDOC_CODIDisplayValue . "</option>";
	$lookup_s_TDOC_CODI = db_fill_array("select SGD_TPR_CODIGO, SGD_TPR_DESCRIP from SGD_TPR_TPDCUMENTO order by 2");

	if(is_array($lookup_s_TDOC_CODI)) {
		reset($lookup_s_TDOC_CODI);
		while(list($key, $value) = each($lookup_s_TDOC_CODI)) {
			if ($key == $flds_TDOC_CODI) 
				$option="<option SELECTED value=\"$key\">$value</option>";
			else 
				$option="<option value=\"$key\">$value</option>";
			echo $option;
		}
	}
?>

					</select>
				</td>
			</tr>

			<tr>
				<td class="titulos5">Dependencia Actual</td>
				<td class="listado5">
					<select class="select" name="s_RADI_DEPE_ACTU">

<?php
	$l= strlen($flds_RADI_DEPE_ACTU);
	if ($l==0){
		echo "<option value=\"\" SELECTED>" . $ss_RADI_DEPE_ACTUDisplayValue . "</option>";
	}
	else{
		echo "<option value=\"\">" . $ss_RADI_DEPE_ACTUDisplayValue . "</option>";
	}
	$lookup_s_RADI_DEPE_ACTU = db_fill_array("select DEPE_CODI, DEPE_NOMB from DEPENDENCIA order by DEPE_NOMB");

	if(is_array($lookup_s_RADI_DEPE_ACTU)) {
		reset($lookup_s_RADI_DEPE_ACTU);
		while(list($key, $value) = each($lookup_s_RADI_DEPE_ACTU)) {
			if ($l>0 && $key == $flds_RADI_DEPE_ACTU) 
				$option="<option SELECTED value=\"$key\">$value</option>";
			else 
				$option="<option value=\"$key\">$value</option>";
			echo $option;
		}
	}
?>

					</select>
				</td>
			</tr>

			<tr>
				<td align="right" colspan="3" class="titulos5">
					<input class="botones" type="button" value="Limpiar" onclick="limpiar();">
					<input class="botones" type="submit" name="Busqueda" value="B&uacute;squeda">
				</td>
			</tr>
		</table>
	</form>

<?php
}

function Ciudadano_show($nivelus, $tpRemDes, $whereFlds) 
{
	//-------------------------------
	// Initialize variables
	//-------------------------------
	global $db2;
	global $db3;
	global $sRADICADOErr;
	global $sFileName;
	global $styles;
	global $ruta_raiz;
	$sWhere = "";
	$sOrder = "";
	$sSQL = "";
	$db = new ConnectionHandler($ruta_raiz);
	//$db->conn->debug= true;
  
	if($tpRemDes==1) { $tpRemDesNombre = "Por Ciudadano"; }
	if($tpRemDes==2) { $tpRemDesNombre = "Por Otras Empresas"; }
	if($tpRemDes==3) { $tpRemDesNombre = "Por Esp"; }
	if($tpRemDes==4) { $tpRemDesNombre = "Por Funcionario"; }
	
	if($tpRemDes==9) {
		$tpRemDesNombre = "";
		$whereTrd = "   ";
	} 
	else {
		$whereTrd = " and dir.sgd_trd_codigo = $whereFlds  ";
	}
	
	$sFormTitle = "Radicados encontrados $tpRemDesNombre";
	$HasParam = false;
	$iRecordsPerPage = 25;
	$iCounter = 0;
	$iPage = 0;
	$bEof = false;
	$iSort = "";
	$iSorted = "";
	$sDirection = "";
	$sSortParams = "";
	$iTmpI = 0;
	$iTmpJ = 0;
	$sCountSQL = "";
	$transit_params = "";
	//Proceso de Vinculacion documentos
	$indiVinculo = get_param("indiVinculo");
	$verrad = get_param("verrad");
	$carpeAnt = get_param("carpeAnt");
	$nomcarpeta = get_param("nomcarpeta");
	//$sOrder = " order by r.RADI_NUME_RADI ";
	$sOrder = " order by r.radi_fech_radi ";
	$iSort = get_param("FormCIUDADANO_Sorting");
	$iSorted = get_param("FormCIUDADANO_Sorted");
	$krd = get_param("krd");
    $form_params = trim(session_name())."=".trim(session_id());
    $form_params .= "&krd=$krd&verrad=$verrad&indiVinculo=$indiVinculo";
    $form_params .= "&carpeAnt=$carpeAnt&nomcarpeta=$nomcarpeta";
    $form_params .= "&s_RADI_DEPE_ACTU=" . tourl(get_param("s_RADI_DEPE_ACTU"));
    $form_params .= "&s_RADI_NOMB=" . tourl(get_param("s_RADI_NOMB"));
    $form_params .= "&s_RADI_NUME_RADI=" . tourl(get_param("s_RADI_NUME_RADI"));
    $form_params .= "&s_TDOC_CODI=" . tourl(get_param("s_TDOC_CODI"));
    $form_params .= "&s_desde_dia=" . tourl(get_param("s_desde_dia"));
    $form_params .= "&s_desde_mes=" . tourl(get_param("s_desde_mes"));
    $form_params .= "&s_desde_ano=" . tourl(get_param("s_desde_ano"));
    $form_params .= "&s_hasta_dia=" . tourl(get_param("s_hasta_dia"));
    $form_params .= "&s_hasta_mes=" . tourl(get_param("s_hasta_mes"));
    $form_params .= "&s_hasta_ano=" . tourl(get_param("s_hasta_ano"));
    $form_params .= "&s_bpin=" . tourl(get_param("s_bpin"));
    $form_params .= "&s_solo_nomb=" . tourl(get_param("s_solo_nomb"));
    $form_params .= "&s_ciudadano=" . tourl(get_param("s_ciudadano"));
    $form_params .= "&s_empresaESP=" . tourl(get_param("s_empresaESP"));
    $form_params .= "&s_oEmpresa=" . tourl(get_param("s_oEmpresa"));
    $form_params .= "&s_FUNCIONARIO=" . tourl(get_param("s_FUNCIONARIO"));
    $form_params .= "&s_entrada=" . tourl(get_param("s_entrada"));
    $form_params .= "&s_salida=" . tourl(get_param("s_salida"));
    $form_params .= "&nivelus=$nivelus&s_Listado=".get_param("s_Listado");
    $form_params .= "&s_SGD_EXP_SUBEXPEDIENTE=".get_param("s_SGD_EXP_SUBEXPEDIENTE")."&";
	// s_Listado s_ciudadano s_empresaESP s_FUNCIONARIO
  
	if(!$iSort) {
		$form_sorting = "";
	} 
	else {
		if($iSort == $iSorted) {
			$form_sorting = "";
			$sDirection = " DESC ";
			$sSortParams = "FormCIUDADANO_Sorting=" . $iSort . "&FormCIUDADANO_Sorted=" . $iSort . "&";
		} 
		else {
			$form_sorting = $iSort;
			$sDirection = "  ";
			$sSortParams = "FormCIUDADANO_Sorting=" . $iSort . "&FormCIUDADANO_Sorted=" . "&";
		}
		switch ($iSort){
			case  1: $sOrder = " order by r.radi_nume_radi" . $sDirection; break;
			case  2: $sOrder = " order by r.radi_fech_radi" . $sDirection; break;
			case  3: $sOrder = " order by r.ra_asun" . $sDirection; break;
			case  4: $sOrder = " order by td.sgd_tpr_descrip" . $sDirection; break;
			case  5: $sOrder = " order by r.radi_nume_hoja" . $sDirection; break;
			case  6: $sOrder = " order by dir.sgd_dir_direccion" . $sDirection; break;
			case  7: $sOrder = " order by dir.sgd_dir_telefono" . $sDirection; break;
			case  8: $sOrder = " order by dir.sgd_dir_mail" . $sDirection; break;
			case  9: $sOrder = " order by dir.sgd_dir_nombre" . $sDirection; break;
			case 12: $sOrder = " order by dir.sgd_dir_telefono" . $sDirection; break;
			case 13: $sOrder = " order by dir.sgd_dir_direccion" . $sDirection; break;
			case 14: $sOrder = " order by dir.sgd_dir_doc" . $sDirection; break;
			case 17: $sOrder = " order by r.radi_usu_ante" . $sDirection; break;
			case 20: $sOrder = " order by r.radi_pais" . $sDirection; break;
			case 21: $sOrder = " order by diasr" . $sDirection; break;
			case 22: $sOrder = " order by dir.sgd_dir_nombre" . $sDirection; break;
			case 23: $sOrder = " order by dir.sgd_dir_nombre" . $sDirection; break;
			case 24: $sOrder = " order by dir.sgd_dir_nombre" . $sDirection; break;
		}
	}

	//-------------------------------
	// Encabezados HTML de las Columnas
	//-------------------------------
?>

	<table width="2000" border="0" cellpadding="0" cellspacing="0" class="borde_tab">
		<tr>
			<td class="titulos4" colspan="20"><a name="RADICADO"><?=$sFormTitle?></a></td>
		</tr>
		<tr>

<?php
	if ($indiVinculo == 1) {
		echo '<td class="titulos5"><font class="ColumnFONT"></font></td>';
	}
?>

			<td class="titulos5"><a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=1&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Radicado</a></td>
			<td class="titulos5"><a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=2&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Fecha Radicacion</a></td>
			<td class="titulos5"> <font class="ColumnFONT">Expediente</td>
			<td class="titulos5"> <font class="ColumnFONT">Nombre Expediente</td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=3&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Asunto</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=4&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Tipo de Documento</a></td>
			<td class="titulos5"> <font class="ColumnFONT">Tipo</td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=5&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Numero de Hojas</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=6&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Direccion contacto</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=7&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Telefono contacto</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=8&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Mail Contacto</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=23&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Dignatario</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=9&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Nombre </a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=14&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Documento</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=15&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Usuario Actual</a></td>
			<td class="titulos5"> <font class="ColumnFONT">Dependencia Actual</td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=20&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Pais</a></td>
			<td class="titulos5"> <a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=21&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Dias Restantes</a></td>
		</tr>

<?php
	// Se crea la $ps_desde_RADI_FECH_RADI con los datos ingresados.
	//------------------------------------
	$ps_desde_RADI_FECH_RADI = mktime(0,0,0,get_param("s_desde_mes"),get_param("s_desde_dia"),get_param("s_desde_ano"));
	$ps_hasta_RADI_FECH_RADI = mktime(23,59,59,get_param("s_hasta_mes"),get_param("s_hasta_dia"),get_param("s_hasta_ano"));

	if(strlen($ps_desde_RADI_FECH_RADI) && strlen($ps_hasta_RADI_FECH_RADI)) {
		$HasParam = true;
		$sWhere = $sWhere . "r.radi_fech_radi>=".$db->conn->DBTimeStamp($ps_desde_RADI_FECH_RADI) ;
		$sWhere .= " and ";
		$sWhere = $sWhere . "r.radi_fech_radi<=".$db->conn->DBTimeStamp($ps_hasta_RADI_FECH_RADI);
	}	

	/* Se recibe la dependencia actual para bsqueda */
	$ps_RADI_DEPE_ACTU = get_param("s_RADI_DEPE_ACTU");
	if(is_number($ps_RADI_DEPE_ACTU) && strlen($ps_RADI_DEPE_ACTU))
		$ps_RADI_DEPE_ACTU = tosql($ps_RADI_DEPE_ACTU, "Number");
	else
		$ps_RADI_DEPE_ACTU = "";

	if(strlen($ps_RADI_DEPE_ACTU)) {
		if($sWhere != "")
			$sWhere .= " and ";
		$HasParam = true;
		$sWhere = $sWhere . "r.radi_depe_actu=" . $ps_RADI_DEPE_ACTU;
	}

	/* Se recibe el numero del radicado para bsqueda */
	$ps_RADI_NUME_RADI = get_param("s_RADI_NUME_RADI");
	$ps_DOCTO =  get_param("s_DOCTO");
	if(strlen($ps_RADI_NUME_RADI)) {
		if($sWhere != "")
			$sWhere .= " and ";
		$HasParam = true;
		$sWhere = $sWhere . "r.radi_nume_radi like " . tosql("%".trim($ps_RADI_NUME_RADI) ."%", "Text");
	}

	if(strlen($ps_DOCTO)) {
		if($sWhere != "")
			$sWhere .= " and ";
		$HasParam = true;
		$sWhere = $sWhere . " dir.SGD_DIR_DOC = '$ps_DOCTO' " ;
	}

	$ps_SGD_EXP_SUBEXPEDIENTE = get_param( "s_SGD_EXP_SUBEXPEDIENTE" );
	if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 ) {
		if( $sWhere != "" ) {
			$sWhere .= " and ";
		}
		$HasParam = true;
		$sWhere = $sWhere . " R.RADI_NUME_RADI = EXP.RADI_NUME_RADI";
		$sWhere = $sWhere . " AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO";
		$sWhere = $sWhere . " AND EXP.SGD_EXP_ESTADO <> 2";
		$sWhere = $sWhere . " AND ( EXP.SGD_EXP_NUMERO LIKE '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%'";
		$sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP1 LIKE UPPER( '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' )";
		$sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP2 LIKE UPPER( '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' )";
		$sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP3 LIKE UPPER( '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' )";
		$sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP4 LIKE UPPER( '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' )";
		$sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP5 LIKE UPPER( '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' )";
		$sWhere = $sWhere . " )";
	}

	$ps_bpin = get_param( "s_bpin" );
	if( strlen( $ps_bpin ) != 0 ) {
		if( $sWhere != "" ) {
			$sWhere .= " and ";
		}
		$HasParam = true;
		$sWhere = $sWhere . " R.RADI_NUME_RADI = EXP.RADI_NUME_RADI";
		$sWhere = $sWhere . " AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO";
		$sWhere = $sWhere . " AND EXP.SGD_EXP_ESTADO <> 2";
        $sWhere = $sWhere . " AND ( SEXP.SEXP_BPIN LIKE '%".str_replace( '\'', '', tosql( trim( $ps_bpin ), "Text" ) )."%'";
        $sWhere = $sWhere . " )";
	}
	/* Se decide si busca en radicado de entrada o de salida o ambos */
	$ps_entrada = strip(get_param("s_entrada"));
	$eLen = strlen($ps_entrada);
	$ps_salida = strip(get_param("s_salida"));
	$sLen = strlen($ps_salida);

	if($ps_entrada!="9999" ){
		if($sWhere != "")
			$sWhere .= " and ";
		$HasParam = true;
		$sWhere = $sWhere . "(r.radi_nume_radi like " . tosql("%".trim($ps_entrada), "Text").")";
	}

	/* Se recibe el tipo de documento para la bsqueda */
	$ps_TDOC_CODI = get_param("s_TDOC_CODI");
	if(is_number($ps_TDOC_CODI) && strlen($ps_TDOC_CODI) && $ps_TDOC_CODI != "9999")
		$ps_TDOC_CODI = tosql($ps_TDOC_CODI, "Number");
	else
		$ps_TDOC_CODI = "";
	if(strlen($ps_TDOC_CODI)) {
		if($sWhere != "")
			$sWhere .= " and ";
		$HasParam = true;
		$sWhere = $sWhere . "r.tdoc_codi=" . $ps_TDOC_CODI;
	}
	
	/* Se recibe la caadena a buscar y el tipo de busqueda (All) (Any) */
	$ps_RADI_NOMB = strip(get_param("s_RADI_NOMB"));
	$ps_solo_nomb = get_param("s_solo_nomb");
	$yaentro=false;

	function splitToLevenshtein($data, $size) 
	{
		$searchName   = array();
		foreach ($data as $key => $value) {
			$fields = explode(" ", $key);
			foreach ($fields as $fk => $fv) {
				if (strlen($fv) > $size) {
					$fv = strtoupper($fv);
					if (!array_key_exists($fv, $searchName)) {
						$searchName[$fv] = array();
					}
					$searchName[$fv][] = $value;
				}
			}

			$fields = explode("-", $key);
			foreach ($fields as $fk => $fv) {
				if (strlen($fv) > $size) {
					$fv = strtoupper($fv);
					if (!array_key_exists($fv, $searchName)) {
						$searchName[$fv] = array();
					}
					$searchName[$fv][] = $value;
				}
			}
		}
		ksort($searchName, SORT_STRING);
		reset($searchName);
		return $searchName;
	}

	$maxLevel =  2;
	
	function makeLevenshtein($keyGlobal, $keySearch, $keySave, &$arrSave) 
	{
		$fields = explode(" ", $keySearch);
		foreach ($keyGlobal as $key => $value) {
			foreach ($fields as $fk => $fv) {
				$lev = levenshtein(strtoupper($fv), $key);
				if (($lev > 0) && ($lev <= 1)){
					$arrSave[$lev][$keySave][$key] = $value;
				};
			}
		}
	}

	$levenshtein = array();
	$lString     = "";

	if(strlen($ps_RADI_NOMB)) {
		$HasParam=true;
		if($sWhere != "")	$sWhere .= " and ( dir.sgd_dir_nomremdes LIKE '%".$ps_RADI_NOMB."%' OR dir.sgd_dir_nombre LIKE '%".$ps_RADI_NOMB."%' OR ".$db->conn->Concat("r.ra_asun","r.radi_cuentai","dir.sgd_dir_telefono","dir.sgd_dir_direccion") . " LIKE '%".$ps_RADI_NOMB."%' )";
	}
	
	/*
	if(strlen($ps_RADI_NOMB)) {
		if($sWhere != "")
			$sWhere .= " and (";
		$HasParam=true;
		$sWhere .= " ";
		$ps_RADI_NOMB = strtoupper($ps_RADI_NOMB);
		$tok = strtok($ps_RADI_NOMB," ");
		$sWhere .= "(";
		
		while ($tok) {
			$sWhere .= "";
			if ($yaentro == true ) {
				$sWhere .= " and ";
			}
			$sWhere .= "UPPER(dir.sgd_dir_nomremdes) LIKE '%".$tok."%' ";
			$tok = strtok(" ");
			$yaentro=true;
		}
		$sWhere .=") or (";
		$tok = strtok($ps_RADI_NOMB," ");
		$yaentro=false;
		
		while ($tok) {
			$sWhere .= "";
			if ($yaentro == true ) {
				$sWhere .= " and ";
			}
			$sWhere .= "UPPER(dir.sgd_dir_nombre) LIKE '%".$tok."%' ";
			$tok = strtok(" ");
			$yaentro=true;
		}
		
		$sWhere .= ") or (";
		$yaentro=false;
		$tok = strtok($ps_RADI_NOMB," ");
		if ($yaentro == true ) $sWhere .= " and (";
			$sWhere .= "UPPER(".$db->conn->Concat("r.ra_asun","r.radi_cuentai","dir.sgd_dir_telefono","dir.sgd_dir_direccion") . ") LIKE '%".$ps_RADI_NOMB."%' ";
		$tok = strtok(" ");
		if ($yaentro == true ) 
			$sWhere .= ")";
		$yaentro=true;

		if ($lString != "") {
			$sWhere .= ") or (";
			$sWhere .= $lString;
		};

		if ($asunString != "") {
			$sWhere .= ") or (";
			$sWhere .= $asunString;
		};

		$sWhere .="))";

	}
	*/	
	if(strlen($ps_RADI_NOMB) && $ps_solo_nomb == "AllTTT") {
		if($sWhere != "")
			$sWhere .= " AND (";
		$HasParam=true;
		$sWhere .= " ";
		$ps_RADI_NOMB = strtoupper($ps_RADI_NOMB);
		$tok = strtok($ps_RADI_NOMB," ");
		$sWhere .= "(";
		$sWhere .= "";
		if ($yaentro == true ) {
			$sWhere .= " AND ";
		}
		$sWhere .= "UPPER(dir.sgd_dir_nomremdes) LIKE '%".$ps_RADI_NOMB."%' ";
		$tok = strtok(" ");
		$yaentro=true;
		$sWhere .=") OR (";
		$tok = strtok($ps_RADI_NOMB," ");
		$yaentro=false;
		$sWhere .= "";
		if ($yaentro == true ) {
			$sWhere .= " AND ";
		}
		$sWhere .= "UPPER(dir.sgd_dir_nombre) LIKE '%".$ps_RADI_NOMB."%' ";
		$tok = strtok(" ");
		$yaentro=true;
		$sWhere .= ") OR (";
		$yaentro=false;
		$tok = strtok($ps_RADI_NOMB," ");
		if ($yaentro == true ) $sWhere .= " AND (";
		$sWhere .= "UPPER(".$db->conn->Concat("r.ra_asun","r.radi_cuentai","dir.sgd_dir_telefono","dir.sgd_dir_direccion").") LIKE '%".$ps_RADI_NOMB."%' ";
		$tok = strtok(" ");
		if ($yaentro == true ) $sWhere .= ")";
		$yaentro=true;
		$sWhere .="))";
	}

	if($HasParam)
		$sWhere = " AND (" . $sWhere . ") ";


	//-------------------------------
	// Build base SQL statement
	//-------------------------------
	require_once("../include/query/busqueda/busquedaPiloto1.php");

	$sSQL ="SELECT ".$radi_nume_radi." AS RADI_NUME_RADI,
					R.RADI_FECH_RADI,
					R.RA_ASUN, 
					TD.sgd_tpr_descrip, 
					".$redondeo." as diasr,
					R.RADI_NUME_HOJA, 
					R.RADI_PATH, 
					DIR.SGD_DIR_DIRECCION, 
					DIR.SGD_DIR_MAIL,
					DIR.SGD_DIR_NOMREMDES, 
					DIR.SGD_DIR_TELEFONO, 
					DIR.SGD_DIR_DIRECCION,
					DIR.SGD_DIR_DOC, 
					R.RADI_USU_ANTE, 
					R.RADI_PAIS, 
					DIR.SGD_DIR_NOMBRE,
					DIR.SGD_TRD_CODIGO, 
					R.RADI_DEPE_ACTU, 
					R.RADI_USUA_ACTU, 
					R.CODI_NIVEL,
					R.SGD_SPUB_CODIGO,
					dbo.VALIDAR_ACCESO_RADEXP (R.RADI_NUME_RADI, '', '$krd') PERMISO";

	if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 ) {
		$sSQL .= " ,EXP.SGD_EXP_NUMERO, 
					SEXP.SGD_SEXP_PAREXP1,
					SEXP.SGD_SEXP_PAREXP2";
	}

	$sSQL .= " FROM SGD_DIR_DRECCIONES DIR, 
					RADICADO R,
					SGD_TPR_TPDCUMENTO TD";

	if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE || $ps_bpin ) != 0 ) {
		$sSQL .= ", SGD_EXP_EXPEDIENTE EXP, SGD_SEXP_SECEXPEDIENTES SEXP";
	}

	$sSQL .= "	WHERE	DIR.SGD_DIR_TIPO = 1 AND 
						DIR.RADI_NUME_RADI = R.RADI_NUME_RADI AND 
						R.TDOC_CODI = TD.SGD_TPR_CODIGO ";

	if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 ) {
		$sSQL .= " AND EXP.SGD_EXP_ESTADO <> 2";    
	}
	
	$query = "	SELECT	SGD_ROL_CODIGO
				FROM	USUARIO
				WHERE	USUA_LOGIN = '$krd'";
	$rol = $db->conn->Getone($query);
	
	## SE VALIDA SI EL USUARIO QUE REALIZA LA CONSULTA TIENE ROL = 3 (AUDITOR)
	if ( $rol == 3) {
					
		## SE CAPTURAN LOS A�OS EN LOS QUE PUEDE REALIZAR BUSQUEDAS EL USUARIO
		$sqlAno = "	SELECT 	CAST (SGD_URA_ANO AS VARCHAR(4)) AS ANO
					FROM	SGD_USUAROL_ANO
					WHERE	SGD_URA_LOGIN = '$krd'";
		$rsAno = $db->conn->Execute($sqlAno);
			
		while (!$rsAno->EOF){
			$anos .= $rsAno->fields[0].',';
			$rsAno->MoveNext();
		}
		if(substr($anos,-1) == ','){
			$anos = substr($anos,0, strlen($anos)-1);
		}
		## FIN- SE VALIDA Y COMPARA EN QUE A�OS PUEDE REALIZAR BUSQUEDAS EL USUARIO
		
		
		## SE CAPTURAN LAS DEPENDENCIAS EN LAS CUALES PUEDE REALIZAR BUSQUEDAS EL USUARIO
		$sqlDep = "	SELECT 	SGD_URD_DEPE_CODI
					FROM	SGD_USUAROL_DEPENDENCIA
					WHERE	SGD_URD_LOGIN = '$krd'";
		$rsDep = $db->conn->Execute($sqlDep);
		
		while (!$rsDep->EOF){
			$depes .= $rsDep->fields[0].',';
			$rsDep->MoveNext();
		}
		if(substr($depes,-1) == ','){
			$depes = substr($depes,0, strlen($depes)-1);
		}
		
		$sSQL .= "	AND R.RADI_DEPE_RADI IN ($depes) AND LEFT(R.RADI_NUME_RADI,4) IN ($anos)";
	}
	## FIN- SE VALIDA SI EL USUARIO QUE REALIZA LA CONSULTA TIENE ROL = 3 (AUDITOR)
		
		
	$sSQL .= $sWhere . $whereTrd . $sOrder;

	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	echo "<!-- $sSQL -->";
	$rs = $db->conn->Execute($sSQL);
	$db->conn->SetFetchMode(ADODB_FETCH_NUM);

	if($rs->EOF || !$rs) {
?>
		
		<tr>
			<td colspan="20" class="alarmas">No hay resultados</td>
		</tr>
		<tr>
			<td colspan="20" class="ColumnTD"><font class="ColumnFONT">
		</tr>	
	</table>
	<table>
		<td>

<?php
		return;
	}

	$iCounter = 0;
	$iPage = get_param("FormCIUDADANO_Page");
	if(strlen(trim($iPage))==0)
		$iPage = 1;
	else {
		if($iPage == "last") {
			$db_count = get_db_value($sCountSQL);
			$dResult = intval($db_count) / $iRecordsPerPage;
			$iPage = intval($dResult);
			if($iPage < $dResult) $iPage++;
		}
		else 
			$iPage = intval($iPage);
	}

	if(($iPage - 1) * $iRecordsPerPage != 0) {
		do {
			$iCounter++;
			$rs->MoveNext();
		} while ($iCounter < ($iPage - 1) * $iRecordsPerPage && (!$rs->EOF && $rs));
	}

	$iCounter = 0;
	$i=1;

	while((!$rs->EOF && $rs)  && $iCounter < $iRecordsPerPage) {
		$linkDocto = '';
		$linkInfGeneral = '';
		$fldsSGD_EXP_SUBEXPEDIENTE = $rs->fields['SGD_EXP_NUMERO'];
		$fldRADI_NUME_RADI = $rs->fields['RADI_NUME_RADI'];
		$fldRADI_FECH_RADI = $rs->fields['RADI_FECH_RADI'];
		$fldASUNTO			= $rs->fields['RA_ASUN'];
		$fldTIPO_DOC		= $rs->fields['SGD_TPR_DESCRIP'];
		$fldNUME_HOJAS		= $rs->fields['RADI_NUME_HOJA'];
		$fldRADI_PATH		= $rs->fields['RADI_PATH'];
		$fldDIRECCION_C		= $rs->fields['SGD_DIR_DIRECCION'];
		$fldDIGNATARIO		= $rs->fields['SGD_DIR_NOMBRE'];
		$fldTELEFONO_C		= $rs->fields['SGD_DIR_TELEFONO'];
		$fldMAIL_C			= $rs->fields['SGD_DIR_MAIL'];
		$fldNOMBRE			= $rs->fields['SGD_DIR_NOMREMDES'];
		$fldCEDULA			= $rs->fields['SGD_DIR_DOC'];
		$aRADI_DEPE_ACTU	= $rs->fields['RADI_DEPE_ACTU'];
		$aRADI_USUA_ACTU	= $rs->fields['RADI_USUA_ACTU'];
		$fldPAIS			= $rs->fields['RADI_PAIS'];
		$fldDIASR			= $rs->fields['DIASR'];
		$tipoReg			= $rs->fields['SGD_TRD_CODIGO'];
		$nombreExpParam1	= $rs->fields['SGD_SEXP_PAREXP1'];
		$nombreExpParam2	= $rs->fields['SGD_SEXP_PAREXP2'];
		$permiso			= $rs->fields['PERMISO'];
		$nombreExpediente = '';
	
		if ( $nombreExpParam1  != ' ' ){
			$nombreExpediente .= $nombreExpParam1;
		}

		if ( $nombreExpParam2  != ' ' ) {
			$nombreExpediente .= $nombreExpParam2;
		}

		if($tipoReg==1) $tipoRegDesc = "Ciudadano";
		if($tipoReg==2) $tipoRegDesc = "Empresa";
		if($tipoReg==3) $tipoRegDesc = "ESP";
		if($tipoReg==4) $tipoRegDesc = "Funcionario";

		$fldNOMBRE = str_replace($ps_RADI_NOMB,"<font color=green><b>$ps_RADI_NOMB</b>",tohtml($fldNOMBRE));
		$fldASUNTO = str_replace($ps_RADI_NOMB,"<font color=green><b>$ps_RADI_NOMB</b>",tohtml($fldASUNTO));

		$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

		$queryDep ="SELECT	DEPE_NOMB 
					FROM	DEPENDENCIA
					WHERE	DEPE_CODI = $aRADI_DEPE_ACTU";
		$rs2=$db->conn->Execute($queryDep);
		$fldDEPE_ACTU = $rs2->fields['DEPE_NOMB'];
		
		$queryUsu ="SELECT	USUA_NOMB 
					FROM	USUARIO 
					WHERE	DEPE_CODI = $aRADI_DEPE_ACTU AND
							USUA_CODI = $aRADI_USUA_ACTU";
		$rs2=$db->conn->Execute($queryUsu);
		$fldUSUA_ACTU = $rs2->fields['USUA_NOMB'];
		

		if  ($permiso == 0){
			$linkDocto = "<a class='vinculos' href='../bodega/$fldRADI_PATH' target='Imagen$iii'>";
			$linkInfGeneral = "<a class='vinculos' href='../verradicado.php?verrad=$fldRADI_NUME_RADI&".session_name()."=".session_id()."&krd=$krd&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0'>";
		}

		if(strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) == 0 ){
			$consultaExpediente = "	SELECT	E.SGD_EXP_NUMERO,
											s.SGD_SEXP_PAREXP1
									FROM	SGD_EXP_EXPEDIENTE E
											JOIN SGD_SEXP_SECEXPEDIENTES S ON
												S.SGD_EXP_NUMERO = E.SGD_EXP_NUMERO
									WHERE	E.RADI_NUME_RADI = $fldRADI_NUME_RADI AND
											SGD_EXP_ESTADO <> 2";
			$rsE=$db->conn->Execute($consultaExpediente);
			$fldsSGD_EXP_SUBEXPEDIENTE = $rsE->fields['SGD_EXP_NUMERO'];
			$nombreExpediente = $rsE->fields['SGD_SEXP_PAREXP1'];
		}

		// Process the HTML controls
		if($i==1){
			$formato ="listado1";
			$i=2;
		}
		else{
			$formato ="listado2";
			$i=1;
		}
?>
	
		<tr class="<?=$formato?>">

<?php
		if ($indiVinculo == 1) {
?>

			<td class="leidos" align="center" width="70">
				<a href="javascript:pasar_datos('<?=$fldRADI_NUME_RADI?>');" >Vincular</a>
			</td>
				
<?php
		}
?>
			<td class="leidos">
		
<?php
		if (strlen($fldRADI_PATH)) {
			$iii = $iii +1;
			echo ($linkDocto);
		}
		echo $fldRADI_NUME_RADI;
		if (strlen($fldRADI_PATH)){
?>
				</a>
					
<?php
		}		
?>

				&nbsp;
			</td>
			<td class="leidos"><?=$linkInfGeneral?> <?= tohtml($fldRADI_FECH_RADI) ?>&nbsp;</a></td>
			<td class="leidos"> <?= $fldsSGD_EXP_SUBEXPEDIENTE ?>&nbsp;</td>
			<td class="leidos"> &nbsp; <?= $nombreExpediente ?>&nbsp;</td>
			<td class="leidos"> <?= $fldASUNTO ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldTIPO_DOC) ?>&nbsp;</td>
			<td class="leidos"> <?=$tipoRegDesc; ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldNUME_HOJAS) ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldDIRECCION_C) ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldTELEFONO_C) ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldMAIL_C) ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldDIGNATARIO) ?>&nbsp;</td>
			<td class="leidos"> <?= $fldNOMBRE ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldCEDULA) ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldUSUA_ACTU) ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldDEPE_ACTU) ?>&nbsp;</td>
			<td class="leidos"> <?= tohtml($fldPAIS); ?>&nbsp;</td>
			<td class="leidos"> <?php if ($fldRADI_DEPE_ACTU!=999){ echo tohtml($fldDIASR);} else {echo "Sal";} ?>&nbsp;</td>
		</tr>
	  
<?php
		
		$iCounter++;
		$rs->MoveNext();
	}
?>
		
		<tr>
			<td colspan="20" class="ColumnTD"> 
				<font class="ColumnFONT">

<?php
	// Navigation begin
	$bEof = $rs;
	if(($bEof && !$bEof->EOF) || $iPage != 1) {
		$iCounter = 1;
		$iHasPages = $iPage;
		$sPages = "";
		$iDisplayPages = 0;
		$iNumberOfPages = 30; /* El numero de paginas que apareceran en el navegador al pie de la pagina */
		while((!$rs->EOF && $rs) && $iHasPages < $iPage + $iNumberOfPages) {
			if($iCounter == $iRecordsPerPage) {
				$iCounter = 0;
				$iHasPages = $iHasPages + 1;
			}
			$iCounter++;
			$rs->MoveNext();
		}
		if(($rs->EOF || !$rs) && $iCounter > 1) 
			$iHasPages++;
		if (($iHasPages - $iPage) < intval($iNumberOfPages / 2))
			$iStartPage = $iHasPages - $iNumberOfPages;
		else
			$iStartPage = $iPage - $iNumberOfPages + intval($iNumberOfPages / 2);

		if($iStartPage < 0) 
			$iStartPage = 0;
		for($iPageCount = $iPageCount + 1;  $iPageCount <= $iPage - 1; $iPageCount++) {
			$sPages .=  "<a href=" . $sFileName . "?" . $form_params . $sSortParams . "FormCIUDADANO_Page=" . $iPageCount . "#RADICADO\"><font " . "class=\"ColumnFONT\"" . ">" . $iPageCount . "</a>&nbsp;";
			$iDisplayPages++;
		}

		$sPages .= "<font " . "class=\"paginacion\"" . "><b>" . $iPage . "</b>&nbsp;";
		$iDisplayPages++;
		$iPageCount = $iPage + 1;

		while ($iDisplayPages < $iNumberOfPages && $iStartPage + $iDisplayPages < $iHasPages) {
			$sPages .= "<a href=\"" . $sFileName . "?" . $form_params . $sSortParams . "FormCIUDADANO_Page=" . $iPageCount . "#RADICADO\"><font " . "class=\"ColumnFONT\"" . ">" . $iPageCount . "</a>&nbsp;";
			$iDisplayPages++;
			$iPageCount++;
		}
		if ($iPage == 1) {
?>

				<font class="paginacion">Primero</font>
				<font class="paginacion">Anterior</font>

<?php
		} 
		else {
?>
				
				<a href="<?=$sFileName?>?<?=$form_params?><?=$sSortParams?>FormCIUDADANO_Page=1#RADICADO"><font class="paginacion">Primero</a>
				<a href="<?=$sFileName?>?<?=$form_params?><?=$sSortParams?>FormCIUDADANO_Page=<?=$iPage - 1?>#RADICADO"><font class="paginacion">Anterior</a>

<?php
		}
		echo "&nbsp;[&nbsp;" . $sPages . "]&nbsp;";
		if ($rs->EOF) {
			echo '<font class="ColumnFONT">Siguiente</font>';
			echo '<font class="ColumnFONT">Ultimo</font>';
		} 
		else {
?>
			
				<a href="<?=$sFileName?>?<?=$form_params?><?=$sSortParams?>FormCIUDADANO_Page=<?=$iPage + 1?>#RADICADO"><font class="ColumnFONT">Siguiente</a>
				<!--<a href="<?=$sFileName?>?<?=$form_params?><?=$sSortParams?>FormCIUDADANO_Page=last#RADICADO"><font class="ColumnFONT">Ultimo</a>-->

<?php
		}
	}
?>
	
			</td>
		</tr>
	</table>

<?php
}

function EmpresaESP_show($nivelus) {}
function OtrasEmpresas_show($nivelus) {}
function FUNCIONARIO_show($nivelus) {}

function resolverTipoCodigo($tipo)
{
	$salida;
	switch ($tipo){
		case 1:
			$salida  = "Ciudadano";
			break;
		case 2:
			$salida = "Empresa";
			break;
		case 3:
			$salida= "ESP";
			break;
		case 4:
			$salida= "Funcionario";
			break;
	}
	return  $salida;
}

function resalaltarTokens(&$tkens,$busqueda)
{
	$salida=$busqueda;
	$tok=explode(" ",$tkens);
	foreach ($tok as $valor){
		$salida=eregi_replace($valor,"<font color=\"green\"><b>".strtoupper($valor)."</b></font>",$salida);
	}
	return $salida;
}

function pintarResultadoConsultas(&$fila,$indice,$numColumna)
{
	global $ruta_raiz,$ps_RADI_NOMB;
	$ps_RADI_NOMB = trim(strip(get_param("s_RADI_NOMB")));
	$verImg=($fila['SGD_SPUB_CODIGO']==1)?($fila['USUARIO_ACTUAL']!=$_SESSION['usua_nomb']?false:true):($fila['USUA_NIVEL']>$_SESSION['nivelus']?false:true);
	$verImg=$verImg && !($fila['SGD_EXP_PRIVADO']==1);
   	$salida="<span class=\"leidos\">";
	switch ($numColumna){
		case 0 :
			$salida=$indice;
			break;
		case 1 :
			if($fila['RADI_PATH'] && $verImg)
				$salida="<a class=\"vinculos\" href=\"{$ruta_raiz}bodega".$fila['RADI_PATH']."\" target=\"imagen".(strlen($fila['RADI_PATH'])+1)."\">".$fila['RADI_NUME_RADI']."</a>";
			else
				$salida.=$fila['RADI_NUME_RADI'];
			break;
		case 2:
			if($verImg)
				$salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADI_NUME_RADI']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['RADI_FECH_RADI']."</a>";
			else
				$salida="<a class=\"vinculos\" href=\"#\" onclick=\"noPermiso();\">".$fila['RADI_FECH_RADI']."</a>";
			break;
		case 3:
			$salida.=$fila['SGD_EXP_NUMERO'];
			break;
		case 4:
			if($ps_RADI_NOMB)
				$salida.=resalaltarTokens($ps_RADI_NOMB,$fila['RA_ASUN']);
			else
				$salida.=htmlentities($fila['RA_ASUN']);
		   	break;
		case 5:
		   	$salida.=tohtml($fila ['SGD_TPR_DESCRIP']);  //resolverTipoDocumento($fila['TD']);
		   	break;
		case 6:
			$salida.=resolverTipoCodigo($fila['SGD_TRD_CODIGO']);
		   	break;
		case 7:
		   $salida.=tohtml($fila['RADI_NUME_HOJA']);
		   break;
		case 8:
			if($ps_RADI_NOMB)
		   		$salida.=resalaltarTokens($ps_RADI_NOMB,$fila['SGD_DIR_DIRECCION']);
		   	else
		   		$salida.=htmlentities($fila['SGD_DIR_DIRECCION']);
		   	break;
		case 9:
			$salida.=  tohtml($fila['SGD_DIR_TELEFONO']);
			break;
		case 10:
			$salida.=tohtml($fila['SGD_DIR_MAIL']);
		   	break;
		case 11:
		   	if($ps_RADI_NOMB)
		   		$salida.= resalaltarTokens($ps_RADI_NOMB,$fila['SGD_DIR_NOMBRE']);
		   	else
		   		$salida.= tohtml($fila['SGD_DIR_NOMBRE']);
		   	break;
		case 12:
		   	if($ps_RADI_NOMB)
		   		$salida.= resalaltarTokens($ps_RADI_NOMB,$fila['SGD_DIR_NOMREMDES']);
		   	else
		   		$salida.= tohtml($fila['SGD_DIR_NOMREMDES']);
		   	break;
		case 13:
			$salida.= tohtml($fila['SGD_DIR_DOC']);
		   	break;
		case 14: 
			if($ps_RADI_NOMB)
				$salida.= resalaltarTokens($ps_RADI_NOMB,$fila['USUARIO_ACTUAL']);
			else
				$salida.=tohtml($fila['USUARIO_ACTUAL']);
			break;
		case 15: 
				$salida.=tohtml($fila['DEPE_NOMB']);
				break;
		case 16: if($ps_RADI_NOMB)
		   		$salida.= resalaltarTokens($ps_RADI_NOMB,$fila['USUARIO_ANTERIOR']);
		   	else
		   		$salida.=htmlentities(tohtml($fila['USUARIO_ANTERIOR']));
		   	break;
		case 17: $salida.=tohtml($fila['RADI_PAIS']);
		   	break;
		case 18: $salida.=($fila['RADI_DEPE_ACTU']!=999)?tohtml($fila['DIASR']):"Sal";
		break;
	}
	return $salida."</span>";
}

function buscar_prueba($nivelus, $tpRemDes, $whereFlds)
{
	global $ruta_raiz;
	$db=new ConnectionHandler($ruta_raiz);
	//constrimos las  condiciones dependiendo de los parametros de busqueda seleccionados
	$ps_desde_RADI_FECH_RADI = mktime(0,0,0,get_param("s_desde_mes"),get_param("s_desde_dia"),get_param("s_desde_ano"));
    $ps_hasta_RADI_FECH_RADI = mktime(23,59,59,get_param("s_hasta_mes"),get_param("s_hasta_dia"),get_param("s_hasta_ano"));

    $where=" AND (R.RADI_FECH_RADI BETWEEN ".$db->conn->DBDate($ps_desde_RADI_FECH_RADI)." AND ".$db->conn->DBDate($ps_hasta_RADI_FECH_RADI).")";
	// se rescantan los parametros de busqueda
	$ps_RADI_NUME_RADI = trim(get_param("s_RADI_NUME_RADI"));
	$ps_DOCTO =  trim(get_param("s_DOCTO"));
	$ps_RADI_DEPE_ACTU =get_param("s_RADI_DEPE_ACTU");
	$ps_SGD_EXP_SUBEXPEDIENTE =trim(get_param("s_SGD_EXP_SUBEXPEDIENTE" ));
	$ps_bpin =trim(get_param("s_bpin" ));
  	$ps_solo_nomb = get_param("s_solo_nomb");
	$ps_RADI_NOMB = trim(strip(get_param("s_RADI_NOMB")));
  	$ps_entrada = strip(get_param("s_entrada"));
  	$ps_TDOC_CODI = get_param("s_TDOC_CODI");
  	$ps_salida = strip(get_param("s_salida"));
  	$sFormTitle = "Radicados encontrados $tpRemDesNombre";

	$ps_RADI_DEPE_ACTU =(is_number($ps_RADI_DEPE_ACTU) && strlen($ps_RADI_DEPE_ACTU))?tosql($ps_RADI_DEPE_ACTU, "Number"):"";
	$where =(strlen($ps_RADI_DEPE_ACTU) > 0)?$where." AND R.RADI_DEPE_ACTU = ".$ps_RADI_DEPE_ACTU:$where;
	$where = (strlen($ps_RADI_NUME_RADI))?$where." AND R.RADI_NUME_RADI  LIKE " . tosql("%".trim($ps_RADI_NUME_RADI) ."%", "Text"):$where;

	switch ($tpRemDes){
		case 1:	$tpRemDesNombre = "Por Ciudadano";
				$where.= " and dir.sgd_trd_codigo = $whereFlds  ";
		break;
		case 2: $tpRemDesNombre = "Por Otras Empresas";
				$where.= " and dir.sgd_trd_codigo = $whereFlds  ";
		break;
		case 3; $tpRemDesNombre = "Por Esp";
				$where.= " and dir.sgd_trd_codigo = $whereFlds  ";
		break;
		case 4: $tpRemDesNombre = "Por Funcionario";
				$where.= " and dir.sgd_trd_codigo = $whereFlds  ";
		break;
		case 9: $tpRemDesNombre = "";
	}


	$where=(strlen($ps_DOCTO))?" AND  DIR.SGD_DIR_DOC = '$ps_DOCTO' ":$where;
    if(strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 ){
    	$min="INNER JOIN SGD_EXP_EXPEDIENTE MINEXP ON R.RADI_NUME_RADI=MINEXP.RADI_NUME_RADI";
        $where = $where. " AND MINEXP.SGD_EXP_ESTADO <> 2";
        $where = $where . " AND (
					SEXP.SGD_EXP_NUMERO LIKE '%".str_replace( '\'', '', tosql($ps_SGD_EXP_SUBEXPEDIENTE , "Text" ))."%'
					OR SEXP.SGD_SEXP_PAREXP1 LIKE UPPER( '%".str_replace( '\'', '', tosql($ps_SGD_EXP_SUBEXPEDIENTE,"Text"))."%')
					OR SEXP.SGD_SEXP_PAREXP2 LIKE UPPER( '%".str_replace( '\'', '', tosql($ps_SGD_EXP_SUBEXPEDIENTE,"Text"))."%')
					OR SEXP.SGD_SEXP_PAREXP3 LIKE UPPER( '%".str_replace( '\'', '', tosql($ps_SGD_EXP_SUBEXPEDIENTE,"Text"))."%')
					OR SEXP.SGD_SEXP_PAREXP4 LIKE UPPER( '%".str_replace( '\'', '', tosql($ps_SGD_EXP_SUBEXPEDIENTE,"Text"))."%')
					OR SEXP.SGD_SEXP_PAREXP5 LIKE UPPER( '%".str_replace( '\'', '', tosql($ps_SGD_EXP_SUBEXPEDIENTE,"Text"))."%'))";
    }
	else{
    	$min="LEFT JOIN
    	(SELECT RADI_NUME_RADI,MIN(SGD_EXP_FECH) FECHA FROM SGD_EXP_EXPEDIENTE GROUP BY SGD_EXP_NUMERO, RADI_NUME_RADI)
    	 MINE ON MINE.RADI_NUME_RADI=R.RADI_NUME_RADI LEFT JOIN SGD_EXP_EXPEDIENTE MINEXP ON (MINE.RADI_NUME_RADI=MINEXP.RADI_NUME_RADI AND MINE.FECHA=MINEXP.SGD_EXP_FECH)";
    }

    $where=($ps_entrada!="9999" )? $where." AND R.RADI_NUME_RADI like " .tosql("%".trim($ps_entrada), "Text").")":$where;
	/* Se decide si busca en radicado de entrada o de salida o ambos */
	$eLen = strlen($ps_entrada);
	$sLen = strlen($ps_salida);

	$where=(is_number($ps_TDOC_CODI) && strlen($ps_TDOC_CODI) && $ps_TDOC_CODI != "9999")?$where." AND R.TDOC_CODI=".tosql($ps_TDOC_CODI, "Number"):$where;
	/* Se recibe la caadena a buscar y el tipo de busqueda (All) (Any) */

	if(strlen($ps_RADI_NOMB)) { //&& $ps_solo_nomb == "Any")
		$ps_RADI_NOMB = strtoupper($ps_RADI_NOMB);
		$concatenacion="UPPER(".$db->conn->Concat("R.RA_ASUN","R.RADI_CUENTAI","DIR.SGD_DIR_TELEFONO","DIR.SGD_DIR_DIRECCION") . ") LIKE '%";
		$tok= explode(" ",$ps_RADI_NOMB);
		$where.=" AND ((UPPER(dir.sgd_dir_nomremdes) LIKE '%".implode("%' AND UPPER(dir.sgd_dir_nomremdes) LIKE '%",$tok)."%') ";
		$where .="OR ( UPPER(dir.sgd_dir_nombre) LIKE '%".implode("%' AND UPPER(dir.sgd_dir_nombre) LIKE '%",$tok)."%')";
		$where .= " OR (".$concatenacion.implode("%' AND ".$concatenacion,$tok)."%'))";
	}
  
	//-------------------------------
	// Build base SQL statement
	//-------------------------------
	include("{$ruta_raiz}/include/query/busqueda/busquedaPiloto1.php");
	require_once("{$ruta_raiz}/include/myPaginador.inc.php");

	$titulos=array("#","1#RADICADO","3#FECHA RADICACION","2#EXPEDIENTE","4#ASUNTO","14#TIPO DE DIOCUMENTO","21#TIPO","7#NO DE HOJAS","15#DIRECCION CONTACTO","18#TELEFONO CONTACTO","16#MAIL CONTACTO ","20#DIGNATARIO","17#NOMBRE","19#DOCUMENTO","22#USUARIO ACTUAL","10#DEPENDENCIA ACTUAL","23#USUARIO ANTERIOR","11#PAIS","13#DIAS RESTANTES");

	$sSQL="SELECT	R.RADI_NUME_RADI,MINEXP.SGD_EXP_NUMERO,R.RADI_FECH_RADI,R.RA_ASUN,
					R.RADI_NUME_HOJA,R.RADI_PATH,R.RADI_USUA_ACTU,R.CODI_NIVEL,
					R.SGD_SPUB_CODIGO,R.RADI_DEPE_ACTU,R.RADI_PAIS,D.DEPE_NOMB,
					{$redondeo} AS DIASR,TD.SGD_TPR_DESCRIP,DIR.SGD_DIR_DIRECCION, DIR.SGD_DIR_MAIL,
					DIR.SGD_DIR_NOMREMDES,DIR.SGD_DIR_TELEFONO,DIR.SGD_DIR_DOC,DIR.SGD_DIR_NOMBRE,
					DIR.SGD_TRD_CODIGO, U.USUA_NOMB USUARIO_ACTUAL, AL.USUA_NOMB USUARIO_ANTERIOR,
					U.CODI_NIVEL USUA_NIVEL,SGD_EXP_PRIVADO
			FROM	RADICADO R INNER JOIN SGD_DIR_DRECCIONES DIR ON R.RADI_NUME_RADI=DIR.RADI_NUME_RADI
					INNER JOIN SGD_TPR_TPDCUMENTO TD ON R.TDOC_CODI=TD.SGD_TPR_CODIGO
					INNER JOIN USUARIO U ON R.RADI_USUA_ACTU=U.USUA_CODI AND R.RADI_DEPE_ACTU=U.DEPE_CODI
					LEFT JOIN USUARIO AL ON R.RADI_USU_ANTE=AL.USUA_LOGIN
					LEFT JOIN DEPENDENCIA D ON D.DEPE_CODI=R.RADI_DEPE_ACTU
					{$min}
					LEFT JOIN SGD_SEXP_SECEXPEDIENTES SEXP ON MINEXP.SGD_EXP_NUMERO=SEXP.SGD_EXP_NUMERO
			WHERE	DIR.SGD_DIR_TIPO = 1
					".$where;

	echo"<table> <tr> <td class=\"titulos4\" colspan=\"20\" width=\"2000\" > <a name=\"RADICADO\">$sFormTitle </a> </td> </tr> </table>";
	$paginador=new myPaginador($db,strtoupper($sSQL),null,"",25);
    $paginador->setImagenASC($ruta_raiz."iconos/flechaasc.gif");
    $paginador->setImagenDESC($ruta_raiz."iconos/flechadesc.gif");
    $paginador->setFuncionFilas("pintarResultadoConsultas");
    $paginador->setpropiedadesTabla(array('width'=>"2000", 'border'=>'0', 'cellpadding'=>'5', 'cellspacing'=>'5' ,'class'=>'borde_tab'));
	$paginador->setPie($pie);
	echo $paginador->generarPagina($titulos,"titulos3");
}

function buscar($nivelus, $tpRemDes, $whereFlds)
{
	Ciudadano_show($nivelus, $tpRemDes, $whereFlds);
	//buscar_prueba($nivelus, $tpRemDes, $whereFlds);
}
?>
