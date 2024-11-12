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
require_once($ruta_raiz . "/" . "_conf/constantes.php");
require_once(ORFEOPATH . "include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);



if(!isset($_SESSION['dependencia']) or !isset($_SESSION['nivelus']))   include "../rec_session.php";
include "common.php";
$fechah = date("ymd") . "_" . time("hms");
$sFileName = "busquedaTexto.php";
$usu = $krd;
$nivelus = $niv = $_SESSION['nivelus'];

if (strlen($niv)){
	set_session("UserID",$usu);
	set_session("krd",$krd);
	set_session("Nivel",$niv);
}


$sAction          = get_param("FormAction");
$sForm            = get_param("FormName");
$flds_ciudadano   = get_param("s_ciudadano");
$flds_empresaESP  = get_param("s_empresaESP");
$flds_oEmpresa    = get_param("s_oEmpresa");
$flds_FUNCIONARIO = get_param("s_FUNCIONARIO");

//Proceso de vinculacion al vuelo
$indiVinculo = get_param("indiVinculo");
$verrad      = get_param("verrad");
$carpAnt     = get_param("carpAnt");
$nomcarpeta  = get_param("nomcarpeta");
$Busqueda = get_param("Busqueda");
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css" type="text/css">
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/buttons.dataTables.min.css">
		<script type="text/javascript" src="../js/jquery-3.5.1.js"></script>
		<script src="../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
		
	</head>
	<script>
    	$(document).ready(function() {
    	    $('#table_elastic').DataTable();
    	} );
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
					<a class="vinculos" href="../busqueda/busquedaPiloto.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "&fechah=$fechah&primera=1&ent=2"; ?>">Busqueda clasica</a><br>
					<a class="vinculos" href="busquedaHist.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd" ?>">B&uacute;squeda por hist&oacute;rico</a><br>
					<a class="vinculos" href="busquedaUsuActu.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd" ?>">Reporte por Usuarios</a><br>
					<a class="vinculos" href="../busqueda/busquedaExp.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php ECHO "&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda Expediente</a><br>
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
$s_Listado="VerListado";
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
		//getSearchElastic($nivelus,9,$whereFlds);
		getSearchElastic($nivelus,1,1);
?>
				</td>
			</tr>

<?php
	} 
	else {
		if (!$etapa)
			if($flds_ciudadano=="CIU" || (!strlen($flds_ciudadano) && !strlen($flds_empresaESP) && !strlen($flds_oEmpresa) && !strlen($flds_FUNCIONARIO))) {
				echo "-2-";
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

function getSearchElastic($n,$x,$y){
    $dnp_radicado = trim(strtolower(get_param("s_RADI_NOMB")));
    if( $dnp_radicado == "" ){
        echo "<script> alert('debe ingresar un caracter mínimo'); </script>";
    }else{
        $defaults = array(
            CURLOPT_POST => 0,
            CURLOPT_HEADEROPT => 0,
            CURLOPT_URL => 'https://10.10.21.35:5001/Search?query='. tourl($dnp_radicado),
            CURLOPT_FRESH_CONNECT => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 0,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        );
        // create a new cURL resource
        $ch = curl_init();
        //set URL and other appropriate options
        curl_setopt_array($ch, ($defaults));
        // grab URL and pass it to the browser
        
        $content = curl_exec( $ch );
        curl_close( $ch );
         
        $dnp_json = json_decode($content, true);
        
        if( isset($dnp_json["items"]) ){
           $_POST[ "s_RADI_NOMB" ] = "";
           $_POST[ "s_RADI_NUME_RADI" ] = "999";
           $_POST[ "s_DNP_RADICADOS" ] = $dnp_json["items"];
           
           if( count( $_POST[ "s_DNP_RADICADOS" ] ) > 0 ){
           // 
           ?>
           	<H1>Buscador en ElasticSearch</H1>
           	<table id="table_elastic" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Radicado</th>
                        <th>Contenido del archivo</th>
                        <th>Páginas</th>
                        <th>Preview</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $_POST[ "s_RADI_NUME_RADI" ] = "";
                foreach ($_POST[ "s_DNP_RADICADOS" ] as $item) {
                    $_POST[ "s_RADI_NUME_RADI" ] .= $item["radicadoId"].",";
                  //  $_POST[ "s_RADI_NUME_RADI" ] = $item["radicadoId"];
                    echo "<tr>";
                    echo "<td>".$item["radicadoId"]."</td>";
                    echo "<td>".$item["highlight"]."</td>";
                    echo "<td>".$item["pages"]."</td>";
                    echo '<td><a href="'.$item["filePath"].'" target= "_blank"> Visualizar </a></td>';
                    echo "</tr>"; 
                }
                ?>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th>Radicado</th>
                        <th>Contenido del archivo</th>
                        <th>Páginas</th>
                        <th>Preview</th>
                    </tr>
                </tfoot>
            </table>
            <br><br>
           <?php
           $_POST[ "s_RADI_NOMB" ] = "";
           Ciudadano_show($n,$x,$y);
           }else{
               echo "<script> alert('NO se encontró ningún registro en el ElasticSearch'); </script>";
           }
        }else{
            echo "<script> alert('El sistema Elastic Search se encuentra en mantenimiento.'); </script>";
        }
     }
    
}

function Search_show() 
{
	global $db;
	global $styles;
	global $db2;
	global $db3;
	global $sForm;
	$sFormTitle = "Busqueda de contenido ElasticSearch";
	$sActionFileName = "busquedaTexto.php";
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
			
			<tr style="display:none;">
				<td class="titulos5">Radicado</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_RADI_NUME_RADI" maxlength="" value="<?=tohtml($flds_RADI_NUME_RADI) ?>" size="" >
				</td>
			</tr>
			
			<tr style="display:none;">
				<td class="titulos5">Identificaci&oacute;n (T.I.,C.C.,Nit) *</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_DOCTO" maxlength="" value="<?=tohtml($flds_DOCTO) ?>" size="" >
				</td>
			</tr>
    
			<tr style="display:none;">
				<td class="titulos5">Expediente</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_SGD_EXP_SUBEXPEDIENTE" maxlength="" value="<?=tohtml($flds_SGD_EXP_SUBEXPEDIENTE) ?>" size="" >
				</td>
			</tr>

			<tr style="display:none;">
				<td class="titulos5">Bpin</td>
				<td class="listado5">
					<input class="tex_area" type="text" name="s_bpin" maxlength="" value="<?=tohtml($flds_bpin) ?>" size="" >
				</td>
			</tr>

			<tr>
				<td class="titulos5">
					<input type="radio" NAME="s_solo_nomb" value="All" CHECKED <?php if($flds_solo_nomb=="All"){ echo ("CHECKED");} ?> />
					Buscar Por<br>
					<!--<INPUT type="radio" NAME="s_solo_nomb" value="Any" <?php if($flds_solo_nomb=="Any"){echo ("CHECKED");} ?>><br>-->
				</td>
				<td class="listado5">
				  <input class="tex_area" type="text" name="s_RADI_NOMB" maxlength="70" value="<?=tohtml($flds_RADI_NOMB) ?>" size="70" >
				</td>
			</tr>
    
			<tr style="display:none;">
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
			<tr style="display:none;">
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

			<tr style="display:none;">
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

			<tr style="display:none;">
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
					<!--  <input class="botones" type="submit" name="Busqueda" value="B&uacute;squeda"> -->
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
    			<th class="titulos5">Fecha Radicación</th>
    			<th class="titulos5">Expediente</th>
    			<th class="titulos5">Nombre Expediente</th>
    			<th class="titulos5">Asunto</th>
    			<th class="titulos5">Tipo de Documento</th>
    			<th class="titulos5">Tipo</th>
    			<th class="titulos5">Numero de Hojas</th>
    			<th class="titulos5">Dirección contacto</th>
    			<th class="titulos5">Teléfono contacto</th>
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
		$fechaInicio = get_param("s_desde_ano")."-".str_pad(get_param("s_desde_mes"), 2, "0", STR_PAD_LEFT)."-".str_pad(get_param("s_desde_dia"), 2, "0", STR_PAD_LEFT)." "."00:00:00AM";
		$fechaFin = get_param("s_hasta_ano")."-".str_pad(get_param("s_hasta_mes"), 2, "0", STR_PAD_LEFT)."-".str_pad(get_param("s_hasta_dia"), 2, "0", STR_PAD_LEFT)." "."23:59:59PM";
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
		$linkInfGeneral = "<a class='vinculos' href='../verradicado.php?verrad=$fldRADI_NUME_RADI&".session_name()."=".session_id()."&krd=$krd&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0'>".tohtml($fldRADI_FECH_RADI)."</a>";


		if(strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) == 0 || $nombreExpParam1  == ''){
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
                      { "width": "80px", "targets": 0 },
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
                        "lengthMenu": "Mostrando _MENU_ registros por página",
                        "zeroRecords": "No hay registros",
                        "info": "Mostrando página _PAGE_ de _PAGES_",
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
  	$sFormTitle = "Radicados encontrados. $tpRemDesNombre";

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
		$tok = $ps_RADI_NOMB;
		$where.=" AND ((UPPER(dir.sgd_dir_nomremdes) LIKE '%".implode("%' AND UPPER(dir.sgd_dir_nomremdes) LIKE '%",$tok)."%') ";
		$where .="OR ( UPPER(dir.sgd_dir_nombre) LIKE '%".implode("%' AND UPPER(dir.sgd_dir_nombre) LIKE '%",$tok)."%')";
		$where .= " OR (".$concatenacion.implode("%' AND ".$concatenacion,$tok)."%'))";
	}
  
	//-------------------------------
	// Build base SQL statement
	//-------------------------------
	include("{$ruta_raiz}/include/query/busqueda/busquedaTexto1.php");
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
