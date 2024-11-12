<?php
session_start();
error_reporting(0);
$ruta_raiz = "..";
if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad']) include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

if(!$tipo_archivo) $tipo_archivo = 0;
function fnc_date_calcm($this_date,$num_month){
    $my_time = strtotime ($this_date); //converts date string to UNIX timestamp
    $timestamp = $my_time - ($num_month * 2678400 ); //calculates # of days passed ($num_days) * # seconds in a day (86400)
    $return_date = date("Y-m-d",$timestamp);  //puts the UNIX timestamp back into string format
    return $return_date;//exit function and return string
}

function fnc_date_calc($this_date,$num_day){
    $my_time = strtotime ($this_date); //converts date string to UNIX timestamp
    $timestamp = $my_time + ($num_day * 86400 ); //calculates # of days passed ($num_days) * # seconds in a day (86400)
    $return_date = date("Y-m-d",$timestamp);  //puts the UNIX timestamp back into string format
    return $return_date;//exit function and return string
}
if( $_GET['excluir'] == 1 && $_GET['radExcluido'] != "" && $_GET['expedienteExcluir'] != "" ) {
    include "$ruta_raiz/include/query/expediente/queryExcluirRadicado.php";
    $rsExcluirRadicado = $db->conn->Execute( $sqlExcluirRadicado );
}
?>

<html>
<head>
  <meta http-equiv="Cache-Control" content="cache">
  <meta http-equiv="Pragma" content="public">
  <link rel="stylesheet" type="text/css" href="../estilos/reset.css" />

<?php
if(!$estado_sal)   {$estado_sal=2;}
if(!$estado_sal_max) $estado_sal_max=3;
if(!$Buscar) $Buscar=1;
if($tipo_archivo==0){
}
if ($dep_sel==0){
    $dep_sel = "%";
}else{
    $dependencia_busq1=  "and SUBSTRING(CONVERT(varchar(19), d.sgd_exp_numero), 5 , 3 ) = $dep_sel";
} 
$accion_sal = "Marcar como Archivado Fisicamente";
$pagina_sig = "envio.php";

$buscar_exp = trim($buscar_exp);
$buscar_rad = trim($buscar_rad);
$fechf=fnc_date_calc($fechafi,1);
if($tipo_archivo==0){
    $dependencia_busq1.= " and (d.sgd_exp_estado=$tipo_archivo or d.sgd_exp_carpeta='0' or d.sgd_exp_carpeta='' or d.sgd_exp_carpeta is null)
        and d.depe_codi like '$dep_sel'  
        and (upper(d.sgd_exp_numero) like '%$buscar_exp%' 
        and upper(d.RADI_NUME_RADI) like '%$buscar_rad%')";
}else{
    $dependencia_busq1.= " and d.sgd_exp_estado=$tipo_archivo 
        and d.depe_codi like '$dep_sel'  
        and (upper(d.sgd_exp_numero) like '%$buscar_exp%' 
        and upper(d.RADI_NUME_RADI) like '%$buscar_rad%')";
}
$dependencia_busq1.=" and d.depe_codi!=909 and d.depe_codi!=905";
$dependencia_busq1.=" and d.sgd_exp_fech <= '$fechf' and d.sgd_exp_fech >= '$fechai' ";

if(!empty($codserie) || !empty($tsub)){
    $dependencia_busq1 .="and g.sgd_exp_numero = d.sgd_exp_numero "; 
}

if(!empty($codserie)){
    $dependencia_busq1 .="and g.sgd_srd_codigo = $codserie ";
	$filtro = "sgd_sexp_secexpedientes g,"; 
    
}

if(!empty($tsub)){
    $dependencia_busq1 .="and g.sgd_sbrd_codigo = $tsub ";
	$filtro = "sgd_sexp_secexpedientes g,"; 
}

$fechah=date("dmy") . "_". time("h_m_s");
$encabezado = session_name()."=".session_id()."&buscar_exp=$buscar_exp&buscar_rad=$buscar_rad&krd=$krd&tipo_archivo=$tipo_archivo&nomcarpeta=$nomcarpeta&fechai=$fechai&fechafi=$fechafi";
$tbbordes = "#CEDFC6";
$tbfondo = "#FFFFCC";
if(!$orno){$orno=1;}
$imagen="flechadesc.gif";
?>
<script>

function sel_dependencia()
{
    document.write('<form name=forma_b_correspondencia action="cuerpo_exp.php?<?=$encabezado?>"  method=post>');
    depsel = form1.dep_sel.value ;

    document.write("<input type=hidden name=depsel value="+depsel+">");
    document.write("<input type=hidden name=estado_sal  value=3>");
    document.write("<input type=hidden name=estado_sal_max  value=3>");
    document.write("<input type=hidden name=fechah value='<?=$fechah?>'>");
    document.write("</form>");
    forma_b_correspondencia.submit();
}

<!--
    /*
     *  Modificado: 02-Octubre-2006 Supersolidaria
     *  Funcion para confirmar la exclusi� de radicados.
     */
    -->
    function confirmaExcluir( radicado, expediente )
    {
        confirma = confirm( 'Confirma que el radicado ' + radicado + ' ya fue excluido fisicamente del expediente ' + expediente + '?' );
        if( confirma )
        {
            document.form1.action = "cuerpo_exp.php?<?=$encabezado?>&radExcluido="+radicado+"&expedienteExcluir="+expediente+"&excluir=1";
            document.form1.submit();
        }
    }
</script>
<link rel="stylesheet" href="../estilos/orfeo.css">
<body bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<script>
<!-- Esta funcion esconde el combo de las dependencia e inforados Se activan cuando el menu envie una seal de cambio.-->
    function window_onload()
    {
        form1.depsel.style.display = '';
        form1.enviara.style.display = '';
        form1.depsel8.style.display = 'none';
        form1.carpper.style.display = 'none';
        setVariables();
        setupDescriptions();
    }
<!-- Cuando existe una sean de cambio el program ejecuta esta funcion mostrando el combo seleccionado -->
    function changedepesel()
    {
        form1.depsel.style.display = 'none';
        form1.carpper.style.display = 'none';
        form1.depsel8.style.display = 'none';
        if(form1.enviara.value==10)
        {
            form1.depsel.style.display = 'none';
            form1.carpper.style.display = '';
            form1.depsel8.style.display = 'none';
        }
        if(form1.enviara.value==9 )
        {
            form1.depsel.style.display = '';
            form1.carpper.style.display = 'none';
            form1.depsel8.style.display = 'none';
        }
        if(form1.enviara.value==8 )
        {
            form1.depsel.style.display = 'none';
            form1.depsel8.style.display = '';
            form1.carpper.style.display = 'none';
        }
    }

<!-- Funcion que activa el sistema de marcar o desmarcar todos los check  -->
    function markAll()
    {
        if(form1.marcartodos.checked==1)
            for(i=4;i<form1.elements.length;i++)
                form1.elements[i].checked=1;
        else
            for(i=4;i<form1.elements.length;i++)
                form1.elements[i].checked=0;
    }
<?php
//include "libjs.php";
function tohtml($strValue)
{
    return htmlspecialchars($strValue);
}
?>
</script>
<style type="text/css">
<!--
.textoOpcion {  font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #000000; text-decoration: underline}
-->
</style>
</head>

<body bgcolor="#FFFFFF" topmargin="0" >
<div id="object1" style="position:absolute; visibility:show; left:10px; top:-50px; width=80%; z-index:2" >
  <p>Cuadro de Historico</p>
</div>
<?php
 /*
 PARA EL FUNCIONAMIENTO CORRECTO DE ESTA PAGINA SE NECESITAN UNAS VARIABLE QUE DEBEN VENIR
 carpeta  "Codigo de la carpeta a abrir"
 nomcarpeta "Nombre de la Carpeta"
 tipocarpeta "Tipo de Carpeta  (0,1)(Generales,Personales)"


 seleccionar todos los checkboxes
  */

$img1="";$img2="";$img3="";$img4="";$img5="";$img6="";$img7="";$img8="";$img9="";
IF($ordcambio){IF($ascdesc=="DESC" ){$ascdesc="";	$imagen="flechaasc.gif";}else{$ascdesc="DESC";$imagen="flechadesc.gif";}}
if($orno==1){$order=" d.sgd_exp_numero $ascdesc";$img1="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==2){$order=" a.radi_nume_radi $ascdesc";$img2="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==3){$order=" a.radi_fech_radi $ascdesc";$img3="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==4){$order=" a.ra_asun $ascdesc";$img4="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==5){$order=" d.sgd_exp_estado $ascdesc,a.radi_nume_radi ";$img5="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==6){$order=" f.usua_nomb $ascdesc";$img6="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==7){$order2=" RADI_USUA_ARCH $ascdesc";$order="a.radi_nume_radi ";$img5="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==8){$order2=" SGD_EXP_EDIFICIO $ascdesc";$order="a.radi_nume_radi ";$img5="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==9){$order=" f.usua_nomb $ascdesc";$img9="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==11){$order=" d.sgd_exp_fech $ascdesc";$img11="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($orno==12){$order=" d.sgd_exp_fech_arch $ascdesc";$img12="<img src='../iconos/$imagen' border=0 alt='$data'>";}
if($tipo_archivo==0){$img7=" <img src='../iconos/flechanoleidos.gif' border=0 alt='$data'> ";}

if($tipo_archivo==1){$img7=" <img src='../iconos/flechanoleidos.gif' border=0 alt='$data'> ";}
if($tipo_archivo==2){$img7=" <img src='../iconos/flechanoleidos.gif' border=0 alt='$data'> ";}

$datosaenviar = "buscar_exp=$buscar_exp&buscar_rad=$buscar_rad&fechaf=$fechaf&tipo_carp=$tipo_carp&ascdesc=$ascdesc&orno=$orno&fechai=$fechai&fechafi=$fechafi";
$encabezado = session_name()."=".session_id()."&buscar_exp=$buscar_exp&buscar_rad=$buscar_rad&krd=$krd&fechah=$fechah&ascdesc=$ascdesc&dep_sel=$dep_sel&tipo_archivo=$tipo_archivo&nomcarpeta=$nomcarpeta&fechai=$fechai&fechafi=$fechafi&sel=$sel&Buscar=$Buscar&orno=";
$encabezado2 = session_name()."=".session_id()."&buscar_exp=$buscar_exp&buscar_rad=$buscar_rad&krd=$krd&fechah=$fechah&ascdesc=$ascdesc&dep_sel=$dep_sel&tipo_archivo=$tipo_archivo&nomcarpeta=$nomcarpeta&fechai=$fechai&fechafi=$fechafi&sel=$sel&Buscar=$Buscar&orno=";
$fechah=date("dmy") . "_". time("h_m_s");

$check=1;
$fechaf=date("dmy") . "_" . time("hms");
$numeroa=0;$numero=0;$numeros=0;$numerot=0;$numerop=0;$numeroh=0;
?>
<br>
<?php
/** Instruccion que realiza la consulta de radicados segun criterios
 * Tambien observamos que se encuentra la varialbe $carpetaenviar que maneja la carpeta 11.
 */
$limit = "";
$sqlfecha = $db->conn->SQLDate("d-m-Y H:i A","a.RADI_FECH_RADI");
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
include "$ruta_raiz/include/query/expediente/queryCuerpo_exp.php";
$rs=$db->conn->Execute($isql);
$nombusuario = $rs->fields["usua_nomb"];
$dependencianomb = $rs->fields["depe_nomb"];
$carpeta=200;
$nomcarpeta = "Expedientes";
include "../envios/paEncabeza.php";
?>
<table  WIDTH='100%' class='borde_tab' valign='top' cellspacing="0">
<tr class="tablas">
<form name='form1' action='cuerpo_exp.php?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&$encabezado&$orno" ?>' method="post">

<td  width="340px" align="left" >
Expediente
<input type=text name=buscar_exp value='<?=$buscar_exp?>' class="tex_area"><br>
 &nbsp;Radicado &nbsp&nbsp&nbsp&nbsp;
<input type=text name=buscar_rad value='<?=$buscar_rad?>' class="tex_area"><br>
&nbsp;Fecha Inclusion Expediente <br>
&nbsp;Inicial &nbsp;&nbsp;
<?php
if(!$fechai) $fechai=fnc_date_calcm(date('Y-m-d'),'1');

?>
<script language="javascript">

var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "form1", "fechai","btnDate1","<?=$fechai?>",scBTNMODE_CUSTOMBLUE);
</script>

<script language="javascript">
dateAvailable1.date = "<?=date('Y-m-d');?>";
dateAvailable1.writeControl();
dateAvailable1.dateFormat="yyyy-MM-dd";
</script>


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Final&nbsp;&nbsp;&nbsp;

<script language="javascript">
<?php
if(!$fechafi) $fechafi = date('Y-m-d');

?>
var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "form1", "fechafi","btnDate2","<?=$fechafi?>",scBTNMODE_CUSTOMBLUE);
</script>

<script language="javascript">
dateAvailable2.date = "<?=date('Y-m-d');?>";
dateAvailable2.writeControl();
dateAvailable2.dateFormat="yyyy-MM-dd";
</script>
</td>
<td >

    <?php  
    if($tipo_archivo==1){
    ?>

    <br> &nbsp;<a href='../archivo/busqueda_archivo.php?<?=session_name()."=".session_id()."&dep_sel=$dep_sel&krd=$krd&fechah=$fechah&$orno&adodb_next_page&nomcarpeta&tipo_archivo=$tipo_archivo&carpeta'" ?>"> Busqueda Avanzada</a><br><br>
    <?php }?>
        <b>Dependencia</b><br />
    <?php
			include_once "$ruta_raiz/include/query/envios/queryPaencabeza.php";
 			$sqlConcat = $db->conn->Concat($db->conn->substr."($conversion,1,5) ", "'-'",$db->conn->substr."(depe_nomb,1,30) ");
			$sql = "select $sqlConcat ,depe_codi from dependencia
							order by depe_codi";
			$rsDep = $db->conn->Execute($sql);
			print $rsDep->GetMenu2("dep_sel","$dep_sel","0:-- Seleccione --", false, 0," onChange='submit();' class='select'");
    ?>
    <br /><b>Serie</b><br />
    <?php
        include "$ruta_raiz/trd/actu_matritrd.php";
        if(!$codserie) $codserie = 0;
        if($dep_sel === '%') $dep_sel = 0;
        $fechah=date("dmy") . " ". time("h_m_s");
        $fecha_hoy = Date("Y-m-d");
        $sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
        $check=1;
        $fechaf=date("dmy") . "_" . time("hms");
        $num_car = 4;
        $nomb_varc = "s.sgd_srd_codigo";
        $nomb_varde = "s.sgd_srd_descrip";
        include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
        $querySerie = "select distinct ($sqlConcat) as detalle, s.sgd_srd_codigo
              from sgd_srd_seriesrd s,sgd_mrd_matrird m
              where s.sgd_srd_codigo = m.sgd_srd_codigo
              and m.depe_codi = '$dep_sel'
              order by detalle
              ";
        $rsD=$db->conn->Execute($querySerie);
        $comentarioDev = "Muestra las Series Docuementales";
        include "$ruta_raiz/include/tx/ComentarioTx.php";
        print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
    ?>
    <br /><b>Sub-Serie</b> <br/>
    <?php    
        $nomb_varc = "sgd_sbrd_codigo";
        $nomb_varde = "sgd_sbrd_descrip";
        include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
        $querySub = "select distinct ($sqlConcat) as detalle, sgd_sbrd_codigo
                 from sgd_sbrd_subserierd
                 where sgd_srd_codigo = '$codserie'
                       and ".$sqlFechaHoy." between sgd_sbrd_fechini and sgd_sbrd_fechfin
                 order by detalle
                  ";
        $rsSub=$db->conn->Execute($querySub);
        include "$ruta_raiz/include/tx/ComentarioTx.php";
        print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
    ?>
    </td>
    <td width='110px' align="right">
        <input type=submit value='Buscar' name=Buscar valign='middle' class='botones'>
    </td>
    </form>
</TR>

</TABLE>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
  <table width='100%' class='borde_tab'>
  <tr>
  <td  align='left' height="40" class=titulos5 >Listar Por:
    <a href='cuerpo_exp.php?<?=$encabezado.$orno?>&tipo_archivo=0' alt='Ordenar Por Leidos'><span class='leidos'>
<?php
    if ($tipo_archivo==0) echo  "$img7";
?>
    Por Archivar</span></a>
    <?php if ($tipo_archivo==1)  echo "$img7"; ?> <a href='cuerpo_exp.php?<?=$encabezado.$orno?>&tipo_archivo=1' class="no_leidos" alt='Ordenar Por Leidos'><span class='tpar'>
<?php
    if ($tipo_archivo==1)  echo "<b>"; else echo "</b>";
?>
    Archivados</span></a><span class='tparr'>
    <!--
    /*
     *  Modificado: 21-Septiembre-2006 Supersolidaria
     *  Ajuste para ver los radicados excluidos de un expediente.
     */
    -->
    <a href='cuerpo_exp.php?<?=$encabezado.$orno?>&tipo_archivo=2' alt='Ordenar Por Leidos'><span class='porExcluir'>
<?php
    if ($tipo_archivo==2) echo  "$img7";
?>
    Por Excluir</span></a>
    <br>
    </td>
    </tr>
    </table>


    </td>
  </tr>
  <tr>
    <td class="grisCCCCCC">
        <table cellspacing="3"  WIDTH=100% class='borde_tab' align='center' >
        <tr  class="titulos5">
    <td  align="center">
  <a href='cuerpo_exp.php?<?=$encabezado2 ?>1&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>                    </a><a href='cuerpo_exp.php?<?=$encabezado ?>2&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>
    <?=$img2 ?>
Radicado Entrada</a><a href='cuerpo_exp.php?<?=$encabezado2 ?>1&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>
</a></td>
    <td  width='18%' align="center"><a href='cuerpo_exp.php?<?=$encabezado2 ?>3&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>
    <?=$img3 ?> Fecha Radicado</a></td>
    <td width='10%' align="center"> <a href='cuerpo_exp.php?<?=$encabezado2 ?>2&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>
    </a><a href='cuerpo_exp.php?<?=$encabezado2 ?>1&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>
    <?=$img1 ?>
Expediente</a> </td>
<TD align=center>Dependencia Incluye</TD>  
<td width='10%' align="center"> <a href='cuerpo_exp.php?<?=$encabezado2 ?>2&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>
    </a><a href='cuerpo_exp.php?<?=$encabezado2 ?>11&ordcambio=1' class='textoOpcion' alt='Seleccione una busqueda'>
    <?=$img11 ?>
    Fecha Inclucion </a> </td>
    <td  width='20%' align="center"> <a href='cuerpo_exp.php?<?=$encabezado2 ?>4&ordcambio=1' class='textoOpcion'  alt='Seleccione una busqueda'>
    <?=$img4 ?> Tipo Documental </a></td>
<?php
    if( $tipo_archivo == 0){
?>
    <td  width='15%' align="center">  <a href='cuerpo_exp.php?<?=$encabezado2 ?>5&ordcambio=1' class='textoOpcion'  alt='Seleccione una busqueda'>
    <?=$img5 ?>
    Archivado </a></td>
<?}
if($tipo_archivo==1){
?>
    <td  width='15%' align="center">  <a href='cuerpo_exp.php?<?=$encabezado2 ?>7&ordcambio=1' class='textoOpcion'  alt='Seleccione una busqueda'>
    <?=$img5 ?>
    Archivado Por: </a></td>
<?}
if($tipo_archivo==2){
?>
    <td  width='15%' align="center">  <a href='cuerpo_exp.php?<?=$encabezado2 ?>8&ordcambio=1' class='textoOpcion'  alt='Seleccione una busqueda'>
    <?=$img5 ?>
    Ubicacion :</a></td>
<?}
if($tipo_archivo==0){
?>
        <td  width='15%' align="center">  <a href='cuerpo_exp.php?<?=$encabezado2 ?>8&ordcambio=1' class='textoOpcion'  alt='Seleccione una busqueda'>
    <?=$img12 ?>
        Ubicacion </a></td>

<?php
}
if($tipo_archivo==1 or $tipo_archivo ==2){
?>
    <td  width='15%' align="center">  <a href='cuerpo_exp.php?<?=$encabezado2 ?>12&ordcambio=1' class='textoOpcion'  alt='Seleccione una busqueda'>
    <?=$img12 ?>
        Fecha de Archivo </a></td>
    <?}?>
    <!--
    /*
     *  Modificado: 22-Septiembre-2006 Supersolidaria
     *  Ajuste para incluir un bot� que permite confirmar la exclusi� de un radicado.
     */
    -->
<?php
    if( $tipo_archivo == 2 )
    {
?>
    <td  width='15%' align="center">
        EXCLUIR
    </td>
<?php
    }
?>
</tr>
<?php
    if($Buscar=='Buscar'){
        $row = array();
        $i = 1;
        $ki=0;
        // Comienza el ciclo para mostrar los documentos de la carpeta predeterminada.
        $registro=$pagina*100;
        while(!$rs->EOF)
        {
            if($ki>=$registro and $ki<($registro+100)){
                $data = trim($rs->fields["RADI_NUME_RADI"]);
                $numdata =  trim($rs->fields["CARP_CODI"]);
                $plg_codi = $rs->fields["PLG_CODI"];
                $plt_codi = $rs->fields["PLT_CODI"];
                $num_expediente = $rs->fields["SGD_EXP_NUMERO"];
                $imagen_rad = $rs->fields["RADI_PATH"];
                $usuario_actual = $rs->fields["USUA_NOMB"];
                $dependencia_actual = $rs->fields["DEPE_NOMB"];
                $estado = $rs->fields["SGD_EXP_ESTADO"];
                $fecha_archivo = $rs->fields["SGD_EXP_FECH_ARCH"];
                $fecha_clasificacion = $rs->fields["SGD_EXP_FECH"];

                $redondeo = $db->conn->round(((radi_fech_radi+(b.sgd_tpr_termino * 7/5))-$sqlFechaHoy));
                if($estado==2)$isqlCount.=" and d.SGD_EXP_FECH_ARCH !='' ";

                $sqlr="select m.sgd_tpr_descrip from sgd_tpr_tpdcumento m,radicado r where
                    r.radi_nume_radi = $data and m.sgd_tpr_codigo=r.tdoc_codi";
                $sqle="select SGD_EXP_CAJA,SGD_EXP_ESTANTE,RADI_USUA_ARCH,SGD_EXP_ENTREPA,SGD_EXP_EDIFICIO,SGD_EXP_ISLA from
                    SGD_EXP_EXPEDIENTE where SGD_EXP_NUMERO like '$num_expediente' order by '$order2'";

                $tm  = "select sgd_eit_sigla from sgd_eit_items where sgd_eit_codigo = '$edifi'";
                $tm1 = "select sgd_eit_sigla from sgd_eit_items where sgd_eit_codigo = '$estan'";
                $tm2 = "select sgd_eit_sigla from sgd_eit_items where sgd_eit_codigo = '$entre'";
                $tm3 = "select sgd_eit_sigla from sgd_eit_items where sgd_eit_codigo = '$carro'";
                $tm4 = "select sgd_eit_sigla from sgd_eit_items where sgd_eit_codigo = '$zona'";
                $tm5 = "select sgd_eit_sigla from sgd_eit_items where sgd_eit_codigo = '$caja'";
                $tm6 = "select sgd_eit_sigla from sgd_eit_items where sgd_eit_codigo = '$piso'";

                $rse    =$db->conn->Execute($sqle);
                $estan       = $rse->fields["SGD_EXP_ESTANTE"];
                $entre       = $rse->fields["SGD_EXP_ENTREPA"];
                $caja        = $rse->fields["SGD_EXP_CAJA"];
                $piso        = $rse->fields["SGD_EXP_ISLA"];
                $edifi       = $rse->fields["SGD_EXP_EDIFICIO"];
                $zona        = $rse->fields["SGD_EXP_UFISICA"];
                $carro       = $rse->fields["SGD_EXP_CARRO"];
                $depeIncluyo = $rs->fields["DEPE_CODI"];
                $usua_arch   = $rse->fields["RADI_USUA_ARCH"];

                if($tm){
                    $rs1=$db->conn->Execute($tm);
                    $ed=$rs1->fields['SGD_EIT_SIGLA'];
                }


                if($edifi=="" and $estan=="" and $entre=="" and $caja=="" and $carro=="" and $zona=="")$ubicacion="";
                else{
                    $ubicacion=$ed."-".$pi;
                    $rs5=$db->conn->Execute($tm4);
                    if($zona!=""){$zo=$rs5->fields['SGD_EIT_SIGLA'];$ubicacion.="-".$zo;}
                    $rs4=$db->conn->Execute($tm3);
                    if($carro!=""){$ca=$rs4->fields['SGD_EIT_SIGLA'];$ubicacion.="-".$ca;}
                    $rs2=$db->conn->Execute($tm1);
                    if($estan!=""){$es=$rs2->fields['SGD_EIT_SIGLA'];$ubicacion.="-".$es;}
                    $rs3=$db->conn->Execute($tm2);
                    if($entre!=""){$et=$rs3->fields['SGD_EIT_SIGLA'];$ubicacion.="-".$et;}
                    $rs6=$db->conn->Execute($tm5);
                    if($caja!=""){$cj=$rs6->fields['SGD_EIT_SIGLA'];$ubicacion.="-".$cj;}
                }
                include "$ruta_raiz/include/query/expediente/queryCuerpo_exp.php";
                $rsd=$db->conn->Execute($sqlr);
                $tipoDoc=$rsd->fields['SGD_TPR_DESCRIP'];
                /*
                 *  Modificado: 22-Septiembre-2006 Supersolidaria
                 *  Ajuste para determinar si un radicado hab� sido archivado antes de ser excluido de
                 *  un expediente.
                 */
                if( $estado == 0 )
                {
                    $estado_nomb = "No";
                }
                else if( $estado == 2 && $fecha_archivo != "" )
                {
                    $estado_nomb = "Si";
                }
                else if( $estado == 2 && $fecha_archivo == "" )
                {
                    $estado_nomb = "No";
                }
                else
                {
                    $estado_nomb = "Si";
                }

                if($plt_codi==2){$img_estado = "<img src='../imagenes/docRadicado.gif'  border=0>"; }
                    if($plt_codi==3){$img_estado = "<img src='../imagenes/docImpreso.gif'  border=0>"; }
                        if($plt_codi==4){$img_estado = "<img src='../imagenes/docEnviado.gif ' border=0>"; }
                            if($rs->fields["SGD_TPR_CODIGO"]==9999)
                            {
                                if($plt_codi==2){$img_estado = "<img src=../imagenes/docRecibido.gif  border=0>"; }
                                    if($plt_codi==2){$img_estado = "<img src=../imagenes/docRadicado.gif  border=0>"; }
                                        if($plt_codi==3){$img_estado = "<img src=../imagenes/docImpreso.gif  border=0>"; }
                                            if($plt_codi==4){$img_estado = "<img src=../imagenes/docEnviado.gif  border=0>"; }

                                                $dep_radicado = substr($rs->fields["RADI_NUME_RADI"],4,3);
                                $ano_radicado = substr($rs->fields["RADI_NUME_RADI"],0,4);

                                $ref_pdf = "bodega/$ano_radicado/$dep_radicado/docs/$ref_pdf";
                                $tipo_sal = "Archivo";
                                $ref_pdf_salida = "<a href='../bodega/$ano_radicado/$dep_radicado/docs/$ref_pdf' alt='Radicado de Salida $rad_salida'>$img_estado</a>";
                            }
                            else
                            {
                                $tipo_sal = "Plantilla";
                                $ref_pdf_salida = "<a href='../$ref_pdf' alt='Radicado de Salida $rad_salida'>$img_estado</a>";
                            }

                //$ref_pdf_salida = "<a href='imprimir_pdf_frame?".session_name()."=".session_id() . "&ref_pdf=$ref_pdf&numrad=$numrad'>$img_estado </a>";
                if($data =="") $data = "NULL";
                error_reporting(0);
                $numerot = $row1["num"];
                if($estado==0){$leido="";} else {$leido="";}
                if($i==1){
                    $leido ="listado1";
                    $i=2;
                }else{
                    $leido ="listado2";
                    $i=1;
                }
                /*
                 *  Modificado: 22-Septiembre-2006 Supersolidaria
                 *  Ajuste para identifiar con otro color los radicados excluidos de un expediene.
                 */
                // Por Archivar
                if( $estado == 0 )
                {
                    $class = "leidos";
                }
                else if( $estado == 1 )
                {
                    $class = "no_leidos";
                }
                // Por Excluir
                else if( $estado == 2 )
                {
                    $class = "porExcluir";
                }
                if($estado==2 and $estado_nomb=="Si"){
                    $urlimagen = "<a href='../bodega".$rs->fields["RADI_PATH"]."?fechah=$fechah'><span class='".$class."'>$data</span></a>";
?>
        <tr class='<?=$leido?>'>
<?php
                    $radi_tipo_deri = $rs->fields["RADI_TIPO_DERI"];
                    $radi_nume_deri = $rs->fields["RADI_NUME_DERI"];
?>
        <td class='<?=$leido ?>' align="right" width="12%"><span class='<?php print $class; ?>'><?=$urlimagen?></span>
<?php
                    $radi_nomb=$rs->fields["NOMBRES"] ;
?>

</td>
<td class='<?=$leido ?>' width="10%"  align="center"><?php $ruta_raiz="..";?>
<span class='<?php print $class; ?>'>
<a href='../verradicado.php?<?=$encabezado."&num_expediente=$num_expediente&verrad=$data&carpeta_per=0&carpeta=8&nombcarpeta=Expedientes"?>' >
<span class='<?php print $class; ?>'>
  <?=$rs->fields["FECHA"]?></span>
</a></span> </td>
<td class='<?=$leido?>' width="18%">
<span class='<?php print $class; ?>'>
<a href='../verradicado.php?<?=$encabezado."&num_expediente=$num_expediente&verrad=$data&carpeta_per=0&carpeta=8&nombcarpeta=Expedientes"?>' > </a></span>
<span class='<?php print $class; ?>'>
<?=$num_expediente?></span>
</td>
<td class='<?=$leido ?>' width="10%"  align="center">
<span class='<?php print $class; ?>'>
  <?=$depeIncluyo?>
</span>
</td> 
<td class='<?=$leido ?>' width="20%"> <span class='<?php print $class; ?>'><?=$fecha_clasificacion?></span></td>
<td class='<?=$leido ?>' width="20%"> <span class='<?php print $class; ?>'><?=$tipoDoc?> </span>
</td>
<td class='<?=$leido ?>' width="20%">
<span class='<?php print $class; ?>'>
<?=$ubicacion?> </span>
</td>
<td  class='<?=$leido ?>' width="15%" align="center">

<center>
<?php
                    if($fecha_archivo){
                        $datoLink = $fecha_archivo;
                    }else{
                        $datoLink = "Sin Carpeta";
                    }
?>
<a href='../archivo/datos_expediente.php?<?=$encabezado."&num_expediente=$num_expediente&ent=1&nurad=$data"?>' class='vinculos'>
<span class='<?php print $class; ?>'><?=$datoLink?></span>
</a>
</center>

</td>
<td class='<?=$leido ?>' width="20%">
<span class='<?php print $class; ?>'>
<div align="center">
  <a href="javascript:confirmaExcluir( '<?=$data?>', '<?=$num_expediente?>' );">
    <img src="<?php print $ruta_raiz; ?>/iconos/rad_excluido.png" border="0" height="14" width="25">
  </a>
</div>
</span>
</td>

<?php
                    if($check<=20){
                        $check=$check+1;
                    }
                }
                elseif ($estado==1) {
                    $urlimagen = "<a href='../bodega".$rs->fields["RADI_PATH"]."?fechah=$fechah'><span class='".$class."'>$data</span></a>";
?>
        <tr class='<?=$leido?>'>
<?php
                    $radi_tipo_deri = $rs->fields["RADI_TIPO_DERI"];
                    $radi_nume_deri = $rs->fields["RADI_NUME_DERI"];
?>
        <td class='<?=$leido ?>' align="right" width="12%"><span class='<?php print $class; ?>'><?=$urlimagen?></span>
<?php
                    $isql3 ="select H.HIST_FECH AS HIST_FECH1,H.HIST_OBSE,U.USUA_LOGIN
                        from hist_eventos H, usuario u where H.radi_nume_radi='$data' and sgd_ttr_codigo = 90 and H.depe_codi<>999
                        and
                        H.USUA_DOC=u.USUA_DOC
                        order by HIST_FECH desc ";
                    $radi_nomb=$rs->fields["NOMBRES"] ;
                    $rsHist = $db->conn->Execute($isql3);
                    $usUltimoTx=$rsHist->fields["USUA_LOGIN"];
?>

</td>
<td class='<?=$leido ?>' width="10%"  align="center"><?php $ruta_raiz="..";?>
<span class='<?php print $class; ?>'>
<a href='../verradicado.php?<?=$encabezado."&num_expediente=$num_expediente&verrad=$data&carpeta_per=0&carpeta=8&nombcarpeta=Expedientes"?>' >
<span class='<?php print $class; ?>'>
    <?=$rs->fields["FECHA"]?></span>
</a></span>
</td>
<td class='<?=$leido?>' width="18%">
<span class='<?php print $class; ?>'>
<a href='../verradicado.php?<?=$encabezado."&num_expediente=$num_expediente&verrad=$data&carpeta_per=0&carpeta=8&nombcarpeta=Expedientes"?>' > </a></span>
<span class='<?php print $class; ?>'>
<?=$num_expediente?></span> </td>
<TD>
<span class='<?php print $class; ?>'>
    <?=$depeIncluyo?>
</span>
</TD>
<td class='<?=$leido ?>' width="20%"> <span class='<?php print $class; ?>'><?=$fecha_clasificacion?></span></td>
<td class='<?=$leido ?>' width="20%"> <span class='<?php print $class; ?>'><?=$tipoDoc?> </span>
</td>
<td class='<?=$leido ?>' width="20%">
<span class='<?php print $class; ?>'>
 <?=$usUltimoTx?></span>
</td>
<td  class='<?=$leido ?>' width="15%" align="center">

<center>
<?php
                    if($fecha_archivo){
                        $datoLink = $fecha_archivo;
                    }else{
                        $datoLink = "Sin Carpeta";
                    }
?>
<a href='../archivo/datos_expediente.php?<?=$encabezado."&num_expediente=$num_expediente&ent=1&nurad=$data&car=$car"?>' class='vinculos'>
<span class='<?php print $class; ?>'><?=$datoLink?></span>
</a>
</center>

</td>
<td class='<?=$leido ?>' width="20%">
<span class='<?php print $class; ?>'>
<?php
                    echo $ubicacion - "$caja";
?> </span>
</td>
<?php
                    if($check<=20){
                        $check=$check+1;
                    }
                }
                elseif ($estado==0){
                    $urlimagen = "<a href='../bodega".$rs->fields["RADI_PATH"]."?fechah=$fechah'><span class='".$class."'>$data</span></a>";
?>
        <tr class='<?=$leido?>'>
<?php
                    $radi_tipo_deri = $rs->fields["RADI_TIPO_DERI"];
                    $radi_nume_deri = $rs->fields["RADI_NUME_DERI"];
?>
        <td class='<?=$leido ?>' align="right" width="12%"><span class='<?php print $class; ?>'><?=$urlimagen?></span>
<?php
                    $isql3 ="select to_char(HIST_FECH,'DD/MM/YY HH12:MI:SSam')as HIST_FECH,HIST_FECH AS HIST_FECH1,HIST_OBSE,USUA_DOC
                        from hist_eventos where radi_nume_radi='$data' and depe_codi<>999
                        order by HIST_FECH1 desc ";
                    $radi_nomb=$rs->fields["NOMBRES"] ;
                    $rsHist = $db->conn->Execute($isql3);
                    $usUltimoTx=$rsHist->fields["USUA_DOC"];
?>

</td>
<td class='<?=$leido ?>' width="10%"  align="center"><?php $ruta_raiz="..";?>
<span class='<?php print $class; ?>'>
<a href='../verradicado.php?<?=$encabezado."&num_expediente=$num_expediente&verrad=$data&carpeta_per=0&carpeta=8&nombcarpeta=Expedientes"?>' >
<span class='<?php print $class; ?>'>
    <?=$rs->fields["FECHA"]?></span>
</a></span> </td>
    <td class='<?=$leido?>' width="18%">
<span class='<?php print $class; ?>'>
<a href='../verradicado.php?<?=$encabezado."&num_expediente=$num_expediente&verrad=$data&carpeta_per=0&carpeta=8&nombcarpeta=Expedientes"?>' > </a></span>
<span class='<?php print $class; ?>'>
<?=$num_expediente?></span> </td>
<TD>
<span class='<?php print $class; ?>'>
    <?=$depeIncluyo?>
</span>
</TD>
<td class='<?=$leido ?>' width="20%"> <span class='<?php print $class; ?>'><?=$fecha_clasificacion?></span></td>
<td class='<?=$leido ?>' width="20%"> <span class='<?php print $class; ?>'><?=$tipoDoc?> </span>
</td>
<td  class='<?=$leido ?>' width="15%" align="center">
<center>
<a href='../archivo/datos_expediente.php?<?=$encabezado."&num_expediente=$num_expediente&ent=1&nurad=$data"?>' class='vinculos'>
<span class='<?php print $class; ?>'><?=$estado_nomb?></span>
</a>
</center>

</td>
<td class='<?=$leido ?>' width="20%">
<span class='<?php print $class; ?>'>
<?=$ubicacion?> </span>
</td>

<?php
                    if($check<=20){
                        $check=$check+1;
                    }
                }
?> </tr>
<?php
            }
            $ki=$ki+1;
            $rs->MoveNext();
        }
?>
        </table>
    </TD>
    </tr>
</TABLE>
</form>
<table border=0 cellspace=2 cellpad=2 WIDTH=100% class='borde_tab' align='center'>
<tr align="center">
<td> <?php

        // Se calcula el numero de | a mostrar
        $rs=$db->conn->Execute($isqlCount);
        $numerot = $rs->fields["CONTADOR"];
        $paginas = ($numerot / 100);
        ?><span class='vinculos'>Paginas </span> <?php
        if(intval($paginas)<=$paginas)
        {$paginas=$paginas;}else{$paginas=$paginas-1;}
        // Se imprime el numero de Paginas.
        for($ii=0;$ii<$paginas;$ii++)
        {
            if($pagina==$ii){$letrapg="<font color=green size=3>";}else{$letrapg="";}
                echo " <a href='cuerpo_exp.php?pagina=$ii&$encabezado2$orno'><span class=leidos>$letrapg".($ii+1)."</span></font></a>\n";
        }
        echo "<input type=hidden name=check value=$check>";
    }
?> </td>
</tr></table>
<form name=jh >
 <input type=hidDEN name=jj value=0>
  <input type=hidDEN name=dS value=0>
 </form>
</body>
</html>
