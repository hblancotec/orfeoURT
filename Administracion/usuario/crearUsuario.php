<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1) {
	die(include "../../sinpermiso.php");
	exit;
}
    
    require ("../../config.php");
	require_once (ORFEOPATH . "include/db/ConnectionHandler.php");
    require_once ("HTML/Template/IT.php");
    
    $archivoExec = './asignarPermisos.php';
    $archivoExecCancel = '../formAdministracion.php';
    
    session_start();
    if(!$krd) $krd  = $krdOld;
    if(!isset($_SESSION['dependencia'])) include (ORFEOPATH . "rec_session.php");
    
	$encabezado      = "&krd=$krd&dep_sel=$dep_sel&usModo=$usModo";
    $encabezado     .= "&perfil=$perfil&perfilOrig=$perfilOrig&cedula=$cedula";
    $encabezado     .= "&dia=$dia&mes=$mes&ano=$ano&ubicacion=$ubicacion";
    $encabezado     .= "&piso=$piso&extension=$extension&email=$email";
    $carpetaOld     = $carpeta;
    $tipoCarpOld    = $tipo_carp;
    $sessionName    = session_name();
    $sessionId      = session_id();
    $entrada        = 0;
    $modificaciones = 0;
    $salida         = 0;
    $tituloForm     = 'Formulario de Creaci&oacute;n de Usuario';
    $hrefCancel      = $archivoExecCancel . '?' . $sessionName . '=' . $sessionId;
    $hrefCancel     .= $encabezado;
    if(!$fecha_busq) $fecha_busq = date("Y-m-d");
    
    $tpl = new HTML_Template_IT(TPLPATH);
    $tpl->loadTemplatefile('formCrearUsuario2.tpl');
    $db = new ConnectionHandler(ORFEOPATH);
    
    for($i = 0; $i <= 31; $i++) {
        if ($i == 0) {
            $mostrarDias .= "<option value=''>"."". "</option>\n";
        } else {
            if ($i == $dia)	{
                $mostrarDias .= "<option value='$i' selected>$i</option>\n";
            } else $mostrarDias .= "<option value='$i'>$i</option>\n";
        }
    }
    
    $meses = array( 0   =>  "Mes",
                    1   =>  "Enero",
                    2   =>  "Febrero",
                    3   =>  "Marzo",
                    4   =>  "Abril",
                    5   =>  "Mayo",
                    6   =>  "Junio",
                    7   =>  "Julio",
                    8   =>  "Agosto",
                    9   =>  "Septiembre",
                    10  =>  "Octubre",
                    11  =>  "Noviembre",
                    12  =>  "Diciembre");
    
    for($i = 0; $i <= 12; $i++) {
        if ($i == 0) {
            $mostrarMeses .= "<option value=" . "0". ">"."Mes". "</option>\n";
        } else {
            if ($i < 10) $datos = "0".$i;
            else $datos = $i;
            if ($datos == $mes) {
                $mostrarMeses .= "<option value='$i' 'selected'>".$meses[$i]."</option>\n";
            } else $mostrarMeses .= "<option value='$i'>".$meses[$i]."</option>\n";
        }
    }
    
    $tpl->setVariable('TITULO_FORM',    $tituloForm);
    $tpl->setVariable('MOSTRAR_DIAS',   $mostrarDias);
    $tpl->setVariable('MOSTRAR_MESES',  $mostrarMeses);
    $tpl->setVariable('ENLACE_CANCELAR',$hrefCancel);
    $tpl->show();
?>
