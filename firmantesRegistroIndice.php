<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "./sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}

if (!$dependencia || !$usua_doc )   
	include "./rec_session.php";

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);
	
require './config.php';
require './class_control/correoElectronico.php';
include_once "./firmaRadicadoIndice.php";
require_once "./include/db/ConnectionHandler.php";
include_once "./class_control/usuario.php";
?>
<html>
<head>
	<title>Registro de Solicitud de Firma</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="estilos/orfeo.css">
</head>
<body>
<?php
//Se crea la conexion con la b ase de datos
$db = new ConnectionHandler('.');
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug=true;
$db->conn->StartTrans();

//Se crea el objeto de analisis de firmas
$objFirma = new  firmaRadicadoIndice($db);
//Se crea el objeto usuario para traer los nombres.
$objUsuario =  new Usuario($db);
$sqlFechaHoy=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
$fecha_hoy = Date("Y-m-d");
//Var que almacena el numero de firmas seleccionadas
$num = 0;
if (is_array($firmas))
    $num = count($firmas);
//Contador de bucle
$i = 0; 
$arrRads = explode(",",$radicados);
$pathIndice = $path;
//Contiene los radicados cuya firma se solicito efectivamente
$radsActs="";

//Almacena los nombres de quenes habran de firmar
$nombFirmas = "";

foreach ($arrRads as $radicado) {
    	$cicloRad = $objFirma->obtenerCicloPorRadicado($radicado);
    	if ( ($cicloRad === false) ) {
            $cicloRad = $objFirma->crearCiclo($radicado,$pathIndice);
        }
        if ($cicloRad === false){
            $radMalos[] = $radicado;
        } else {
            foreach ($firmas as $cedula){
                $objUsuario->usuarioDocto($cedula);
                if (!$objFirma->agregarSolicitud($cicloRad, $cedula, $objUsuario->get_usua_login())) {
                    //Se llena el string con los nombres de los firmantes
                    $errorMsg[] = $objUsuario->get_usua_nomb(). " al radicado $radicado";
                } else {
                    $radBuenos[] = $radicado;
                    $msgNombre .= $objUsuario->get_usua_nomb()."(".$objUsuario->get_usua_login()."), ";
                    $msgCorreo .= $objUsuario->get_usua_mail().",";
                }
            }
        }
}
$db->conn->CompleteTrans();
//Genera el texto de la opetacion efectuada, si es necesario

if (is_array($radBuenos) && count($radBuenos)>0) {
    $objMail = new correoElectronico('.', false, true);
    if ($objMail) {
        $cuerpo ="<table>".
            "<tr><td colspan='2'>El usuario <b>".$_SESSION['usua_nomb']."</b> ha solicitado para el Índice electrónico <b>".implode(',', $radBuenos)."</b> la firma digital a <b>".substr($msgNombre, 0, strlen($msgNombre)-2)."</b></td></tr>".
            "<tr><td colspan='2'>&nbsp;</td></tr></table>";
        $cuerpm = str_replace('XYX', $cuerpo, $cuerpoMail);
        $objMail->enviarCorreo(explode(',',substr($msgCorreo, 0, strlen($msgCorreo)-1)), null, null, "Solicitud Firmar Digitalmente Radicados.", $cuerpm);
    }
    
?>
<table border='0' cellspace='2' cellpad='2' WIDTH='50%'  class='t_bordeGris' id='tb_general' align='left'>
	<tr>
		<td colspan="2" class="titulos4">ACCI&Oacute;N REQUERIDA </td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">ACCI&Oacute;N REQUERIDA :</td>
		<td  width="65%" height="25" class="listado2_no_identa">SOLICITUD DE FIRMA</td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">ID INDICE ELECTRÓNICO :</td>
		<td width="65%" height="25" class="listado2_no_identa"><?= implode(',', $radBuenos) ?></td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">USUARIO :</td>
		<td  width="65%" height="25" class="listado2_no_identa"><?=$usua_nomb?></td>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">DEPENDENCIA :</td>
		<td  width="65%" height="25" class="listado2_no_identa"><?=$depe_nomb?></td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">FIRMAS SOLICITADAS :</td>
		<td  width="65%" height="25" class="listado2_no_identa"><?=substr($msgNombre, 0, strlen($msgNombre)-1)?></td>
	</tr>
	<tr>
		<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">FECHA Y HORA :</td>
		<td  width="65%" height="25" class="listado2_no_identa"><?=$fecha_hoy?></td>
	</tr>
<?php
switch (count($radMalos)) {
    case 0:break;
    case 1:{
        echo "<tr><td bgcolor='#ffdada' height='25' class='titulos2'><b>ALERTA :<b/></td><td height='25' class='listado2_no_identa'>El radicado ".implode(',', $radMalos)." no pudo gestionarse. Verifique que tenga una plantilla docx asociada.</td></tr>";
    } break;   
    default:{
        echo "<tr><td bgcolor='#ffdada' height='25' class='titulos2'><b>ALERTA :<b/></td><td height='25' class='listado2_no_identa'>los radicados ".implode(',', $radMalos)." no pudieron gestionarse. Verifique que tengan una plantilla docx asociada.</td></tr>";
    } break;
}
?>
	</table>
	<?
}else{
    echo "<table border='0' cellspace='2' cellpad='2' WIDTH='50%'  class='t_bordeGris' id='tb_general' align='left'>";
	echo "<span class=tituloListado>NO HUBO CAMBIOS PARA EFECTUAR </span> ";
	switch (count($radMalos)) {
	    case 1:{
	        echo "<tr><td bgcolor='#ffdada' height='25' class='titulos2'><b>ALERTA :<b/></td><td height='25' class='listado2_no_identa'>El radicado ".implode(',', $radMalos)." no pudo gestionarse. Verifique que tenga una plantilla docx asociada.</td></tr>";
	    } break;
	    default:{
	        echo "<tr><td bgcolor='#ffdada' height='25' class='titulos2'><b>ALERTA :<b/></td><td height='25' class='listado2_no_identa'>los radicados ".implode(',', $radMalos)." no pudieron gestionarse. Verifique que tengan una plantilla docx asociada.</td></tr>";
	    } break;
	};
	echo  "</table>";
}

?> 
<br /> 
<input name="envia" type="button"  class="botones" id="envia"   value="Aceptar" onclick="window.close();">
</body>
</html>