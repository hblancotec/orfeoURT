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

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

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
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css" type="text/css">
		  
		<script type="text/javascript" src="../js/jquery-3.5.1.js"></script>
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/fixedColumns.dataTables.min.css">
		<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/dataTables.fixedColumns.min.js"></script>
		
		<style type="text/css">
	
	       .tabla {
              margin: 0 auto;
              width: 100%;
              clear: both;
              border-collapse: collapse;
              table-layout: fixed;
              word-wrap:break-word;
              text-align: center;
            }
            
            
        	div.dataTables_wrapper {
                width: 1300px;
                margin: 0 auto;
            }
            
            th.dt-center, td.dt-center { text-align: center; }
        	/*th, td { white-space: nowrap; }*/
        	div.dataTables_wrapper {
        		margin: 0 auto;
        	}
        
        	div.container {
        		width: 80%;
        	}
    
    	</style>
	</head>
	
	<script>
		function limpiar() {
			debugger;
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

		function seguridad(radicado, usuario, ruta) {
			var parametros = {
					'radicado': radicado.toString(),
					'ruta': ruta
				};
						
				$.ajax({
					url: '../validarSeguridad.php',
					type: 'POST',
					cache: false,
					data: parametros,
					success: function(text) {
						debugger;
						if(text.length > 1) {
							//text = btoa(text);
							window.open("seguridadImagen.php?ruta="+text, "MyFile", "location=no,width=600,height=800,scrollbars=yes,Menubar=no,toolbar=no,Titlebar=no,resizable=no,top=100,left=100");			
						} else if(text.length == 1) {	
							alert ("NO SE ENCUENTRA EL ARCHIVO PARA EL RADICADO No. " + radicado.toString());
						} else {
							alert ("NO TIENE PERMISOS PARA ACCEDER AL RADICADO No. " + radicado.toString());
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
					}
				});
			
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
					<a class="vinculos" href="../busqueda/busquedaExp.php?<?=$phpsession ?>&krd=<?=$krd?>&<? ECHO "&fechah=$fechah&primera=1&ent=2"; ?>">Busqueda Expediente</a><br>
					<a class="vinculos" href="../busqueda/busquedaTexto.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php ECHO "&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda Texto</a><br>
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
					<!--<INPUT type="radio" NAME="s_solo_nomb" value="Any" <? if($flds_solo_nomb=="Any"){echo ("CHECKED");} ?>><br>-->
				</td>
				<td class="listado5">
				  <input class="tex_area" type="text" name="s_RADI_NOMB" maxlength="70" value="<?=tohtml($flds_RADI_NOMB) ?>" size="70" >
				</td>
			</tr>
    
			<tr>
				<td colspan="2" class="FieldCaptionTD">
					<!-- <table>
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
					</table>  -->
				</td>
			</tr>
			<tr>
				<td class="titulos5"> Buscar en Radicados de </td>
				<td class="listado5"> 
					<select class="select" name="s_entrada" >
			  
<?
	if(!$s_Listado) $s_Listado="VerListado";
	if ($flds_entrada==0) $flds_entrada="9999";
	echo "<option value=\"9999\">" . $ss_TRAD_CODIDisplayValue . "</option>";
	$lookup_s_entrada = db_fill_array("select SGD_TRAD_CODIGO, SGD_TRAD_DESCR from SGD_TRAD_TIPORAD order by 2");

	if(is_array($lookup_s_entrada)) {
		reset($lookup_s_entrada);
		foreach($lookup_s_entrada as $key => $value) {
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
	
<?
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
			  
<?
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
			  
<?
	$agnoactual=Date('Y');
	for($i = 2006; $i <= $agnoactual; $i++) {
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
					
<?
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
			  
<?
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
			  
<?
	for($i = 2006; $i <= $agnoactual; $i++) {
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
			  
<?
	if ($flds_TDOC_CODI==0) 
		$flds_TDOC_CODI="9999";
	echo "<option value=\"9999\">" . $ss_TDOC_CODIDisplayValue . "</option>";
	$lookup_s_TDOC_CODI = db_fill_array("select SGD_TPR_CODIGO, SGD_TPR_DESCRIP from SGD_TPR_TPDCUMENTO order by 2");

	if(is_array($lookup_s_TDOC_CODI)) {
		reset($lookup_s_TDOC_CODI);
		foreach($lookup_s_TDOC_CODI as $key => $value) {
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

<?
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
		foreach($lookup_s_RADI_DEPE_ACTU as $key => $value) {
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
					<input class="botones" type="submit" id="Busqueda" name="Busqueda" value="B&uacute;squeda">
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
	$db = new ConnectionHandler($ruta_raiz);
	
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
	$sOrder = " order by radi_fech_radi ";
	$iSort = get_param("FormCIUDADANO_Sorting");
	$iSorted = get_param("FormCIUDADANO_Sorted");
	$krd = get_param("krd");

	//-------------------------------
	// Encabezados HTML de las Columnas
	//-------------------------------
?>	
   	
	<table id="grid" style="width:100%" class="tabla hover stripe order-column cell-border compact">
		<thead>
    		<tr>
     			<th class="titulos5">Radicado</th>
    			<th class="titulos5">Fecha Radicaci&oacute;n</th>
    			<th class="titulos5">Expediente</th>
    			<th class="titulos5">Nombre Expediente</th>
    			<th class="titulos5">Asunto</th>
    			<th class="titulos5">Tipo de Documento</th>
    			<th class="titulos5">Tipo</th>
    			<th class="titulos5">Numero de Hojas</th>
    			<th class="titulos5">Direcci&oacute;n contacto</th>
    			<th class="titulos5">Tel&eacute;fono contacto</th>
    			<th class="titulos5">Mail Contacto</th>
    			<th class="titulos5">Dignatario</th>
    			<th class="titulos5">Nombre</th>
    			<th class="titulos5">Documento</th>
    			<th class="titulos5">Usuario Actual</th>
    			<th class="titulos5">Dependencia Actual</th>
    		</tr>
		</thead>
		<tbody>
<?php
	// Se crea la $ps_desde_RADI_FECH_RADI con los datos ingresados.
	//------------------------------------
	$ps_desde_RADI_FECH_RADI = mktime(0,0,0,get_param("s_desde_mes"),get_param("s_desde_dia"),get_param("s_desde_ano"));
	$ps_hasta_RADI_FECH_RADI = mktime(23,59,59,get_param("s_hasta_mes"),get_param("s_hasta_dia"),get_param("s_hasta_ano"));

	if(strlen($ps_desde_RADI_FECH_RADI) && strlen($ps_hasta_RADI_FECH_RADI)) {	
	    $fechaInicio = str_pad(get_param("s_desde_dia"), 2, "0", STR_PAD_LEFT)."-".str_pad(get_param("s_desde_mes"), 2, "0", STR_PAD_LEFT)."-".get_param("s_desde_ano")." "."00:00:00AM";
	    $fechaFin = str_pad(get_param("s_hasta_dia"), 2, "0", STR_PAD_LEFT)."-".str_pad(get_param("s_hasta_mes"), 2, "0", STR_PAD_LEFT)."-".get_param("s_hasta_ano")." "."23:59:59PM";
	}	

	/* Se recibe la dependencia actual para bsqueda */
	$codigoDependencia = 0;
	$ps_RADI_DEPE_ACTU = get_param("s_RADI_DEPE_ACTU");
	if(strlen($ps_RADI_DEPE_ACTU)) {	
		$codigoDependencia = $ps_RADI_DEPE_ACTU;
	}

	/* Se recibe el nmero del radicado para bsqueda */
	$numeroradicado = '';
	$ps_RADI_NUME_RADI = trim(get_param("s_RADI_NUME_RADI"));
	$ps_DOCTO =  get_param("s_DOCTO");
	if(strlen($ps_RADI_NUME_RADI)) {
		$numeroradicado = $ps_RADI_NUME_RADI;
	}

	$documento = '';
	if(strlen($ps_DOCTO)) {	
		$documento = $ps_DOCTO;
	}

	$numeroExpediente = "";
	$ps_SGD_EXP_SUBEXPEDIENTE = get_param( "s_SGD_EXP_SUBEXPEDIENTE" );
	if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 ) {		
		$numeroExpediente = $ps_SGD_EXP_SUBEXPEDIENTE;
	}

	$ps_bpin = get_param( "s_bpin" );
	if( strlen( $ps_bpin ) != 0 ) {
		$sWhere = $sWhere . " R.RADI_NUME_RADI = EXP.RADI_NUME_RADI";
		$sWhere = $sWhere . " AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO";
		$sWhere = $sWhere . " AND EXP.SGD_EXP_ESTADO <> 2";
        $sWhere = $sWhere . " AND ( SEXP.SEXP_BPIN LIKE '%".str_replace( '\'', '', tosql( trim( $ps_bpin ), "Text" ) )."%'";
        $sWhere = $sWhere . " )";
	}
	/* Se decide si busca en radicado de entrada o de salida o ambos */
	$ps_entrada = strip(get_param("s_entrada"));
	$ps_salida = strip(get_param("s_salida"));
	$tipoRadicado = 0;
	if($ps_entrada!="9999" ){	
		$tipoRadicado = trim($ps_entrada);
	}

	/* Se recibe el tipo de documento para la bsqueda */
	$tipoDocumento = 0;
	$ps_TDOC_CODI = get_param("s_TDOC_CODI");
	if(strlen($ps_TDOC_CODI) > 0 && $ps_TDOC_CODI != 9999) {	
		$tipoDocumento = $ps_TDOC_CODI;
	}
	
	/* Se recibe la caadena a buscar y el tipo de busqueda (All) (Any) */
	$ps_RADI_NOMB = strip(get_param("s_RADI_NOMB"));
	$ps_solo_nomb = get_param("s_solo_nomb");
	$yaentro=false;

	$datosBusca = '';
	if(strlen($ps_RADI_NOMB)) {
		$ps_RADI_NOMB = strtoupper($ps_RADI_NOMB);
		$datosBusca = $ps_RADI_NOMB;
	}
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	$st = " declare @fech1 datetime
            declare @fech2 datetime
            DECLARE @return_value int
					
            set @fech1 = cast('" . $fechaInicio . "' as datetime)
            set @fech2 = cast('" . $fechaFin . "' as datetime)
					    
            EXEC @return_value = [dbo].[BUSQUEDA_Consultas]
                                 @NoRadicado = '$numeroradicado',
                                 @documento = '$ps_DOCTO',
                                 @tipoRadicado = $tipoRadicado,
                                 @fechaInicio = @fech1,
                                 @fechaFin = @fech2,
                                 @codigoTipoDocumental = $tipoDocumento,
                                 @codigoDepenendenciaActual = ".$codigoDependencia.",
                                 @datosBusca = '$datosBusca',
                                 @numeroExpediente = '$numeroExpediente',
                                 @loginusuario = '".$krd."'";
	
	//echo "$st";
	$rs = $db->conn->Execute($st);
	$db->conn->SetFetchMode(ADODB_FETCH_NUM);

	if($rs->EOF || !$rs) {
?>
		
		<tr>
			<td colspan="20" class="alarmas">No hay resultados</td>
		</tr>
		<tr>
			<td colspan="20" class="ColumnTD"><font class="ColumnFONT">
		</tr>	

<?
		return;
	}

	$i=1;

	while(!$rs->EOF && $rs) {
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
	
		if ( $nombreExpParam1  != '' ){
			$nombreExpediente .= $nombreExpParam1;
		}

		if ( $nombreExpParam2  != '' ) {
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
		
    	$linkDocto = "<a class='vinculos' href='javascript:void(0)' onclick='seguridad($fldRADI_NUME_RADI, \"$krd\", \"".base64_encode($fldRADI_PATH)."\");'>$fldRADI_NUME_RADI</a>";
    	if ($krd == "SARANGO" ||  $krd == "JOSCAMACHO") {
    	    $linkInfGeneral = "<a class='vinculos' href='../verradicado.php?verrad=$fldRADI_NUME_RADI&".session_name()."=".session_id()."&krd=$krd&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0'>".tohtml($fldRADI_FECH_RADI)."</a>";
    	} else {
        	if ($permiso == 0) {	
        		$linkInfGeneral = "<a class='vinculos' href='../verradicado.php?verrad=$fldRADI_NUME_RADI&".session_name()."=".session_id()."&krd=$krd&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0'>".tohtml($fldRADI_FECH_RADI)."</a>";
        	} else {
        	    $linkInfGeneral = "".tohtml($fldRADI_FECH_RADI)."";
        	}
    	}

		if(strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) == 0 || $nombreExpParam1  == ''){
			$consultaExpediente = "	SELECT	E.SGD_EXP_NUMERO,
											s.SGD_SEXP_PAREXP1
									FROM	SGD_EXP_EXPEDIENTE E
											JOIN SGD_SEXP_SECEXPEDIENTES S ON
												S.SGD_EXP_NUMERO = E.SGD_EXP_NUMERO
									WHERE	E.RADI_NUME_RADI = $fldRADI_NUME_RADI AND
											SGD_EXP_ESTADO <> 2
											and S.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas') ";
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
				
<?
		}
?>
			<td class="leidos">
		
<?php
		if (strlen($fldRADI_PATH)) {
			$iii = $iii +1;
			echo ($linkDocto);
		} else {
		  echo $fldRADI_NUME_RADI;		
		}
?>
			</td>
			<td class="leidos"><?=$linkInfGeneral?></td>
			<td class="leidos"> <?= $fldsSGD_EXP_SUBEXPEDIENTE ?></td>
			<td class="leidos">  <?= $nombreExpediente ?></td>
			<td class="leidos"> <?= $fldASUNTO ?></td>
			<td class="leidos"> <?= tohtml($fldTIPO_DOC) ?></td>
			<td class="leidos"> <?=$tipoRegDesc; ?></td>
			<td class="leidos"> <?= tohtml($fldNUME_HOJAS) ?></td>
			<td class="leidos"> <?= tohtml($fldDIRECCION_C) ?></td>
			<td class="leidos"> <?= tohtml($fldTELEFONO_C) ?></td>
			<td class="leidos"> <?= tohtml($fldMAIL_C) ?></td>
			<td class="leidos"> <?= tohtml($fldDIGNATARIO) ?></td>
			<td class="leidos"> <?= $fldNOMBRE ?></td>
			<td class="leidos"> <?= tohtml($fldCEDULA) ?></td>
			<td class="leidos"> <?= tohtml($fldUSUA_ACTU) ?></td>
			<td class="leidos"> <?= tohtml($fldDEPE_ACTU) ?></td>
		</tr>
	  
<?
		$rs->MoveNext();
	}
?>
	</tbody>
	</table>

	<script languaje="JavaScript">

        	$(document).ready(function() {
        		var table = $('#grid').removeAttr('width').DataTable( {
                    paging:   true,
                    ordering: true,
                    info:     true,
                    scrollY:  "600px",
                    scrollX: true,
                    scrollCollapse: true,
                    autoWidth: false,
                    fixedColumns: true,
                    fixedHeader: {
                        "header": true,
                        "footer": false
                    },
                    columnDefs: [
                      { "width": "90px", "targets": 0 },
                      { "width": "110px", "targets": 1 },
                      { "width": "115px", "targets": 2 },
                      { "width": "300px", "targets": 3 },
                      { "width": "400px", "targets": 4 },
                      { "width": "100px", "targets": 5 },
                      { "width": "70px", "targets": 6 },
                      { "width": "90px", "targets": 7 },
                      { "width": "200px", "targets": 8 },
                      { "width": "200px", "targets": 9 },
                      { "width": "200px", "targets": 10 },
                      { "width": "200px", "targets": 11 },
                      { "width": "200px", "targets": 12 },
                      { "width": "100px", "targets": 13 },
                      { "width": "300px", "targets": 14 },
                      { "width": "300px", "targets": 15 }
                    ],
                    language: {
                        "lengthMenu": "Mostrando _MENU_ registros por p&aacute;gina",
                        "zeroRecords": "No hay registros",
                        "info": "Mostrando p&aacute;gina _PAGE_ de _PAGES_",
                        "infoEmpty": "No hay registros disponibles",
                        "infoFiltered": "(Filtrado de _MAX_ registros totales)",
                        "search":         "Filtrar:",
                        "paginate": {
                            "first":      "Primero",
                            "last":       "Último",
                            "next":       "Siguiente",
                            "previous":   "Anterior"
                        }
                    }
                } );
        	} );

			
    </script>
<?php
}

?>
