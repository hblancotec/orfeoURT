<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1) {
    die(include "../../sinpermiso.php");
    exit();
}

$ruta_raiz = "../..";
if (!isset($_SESSION['dependencia']) || !isset($_SESSION['tpDepeRad']))
    include "$ruta_raiz/rec_session.php";
$phpsession = session_name() . "=" . session_id();

include_once ("../../_conf/constantes.php");
require_once ("HTML/Template/IT.php");

$tpl = new HTML_Template_IT(TPLPATH);
$tpl->loadTemplatefile('formMenuUsuario.tpl');

$opcionMenu = array();
$cont = 1;
$archivoAction = '../formAdministracion.php';
$tituloForm = 'ADMINISTRACI&Oacute;N DE USUARIOS Y PERFILES';
$opcionMenu[0]["TITULO"] = 'Crear Usuario';
$opcionMenu[0]["ENLACE"] = './adm_usuarios.php' . '?usModo=1&';
$opcionMenu[1]["TITULO"] = 'Editar Usuario';
$opcionMenu[1]["ENLACE"] = './cuerpoEdicion.php' . '?usModo=2&';
$opcionMenu[2]["TITULO"] = 'Consultar Usuario';
$opcionMenu[2]["ENLACE"] = './cuerpoConsulta.php?';
$varEnviar = $phpsession . '&krd=' . $krd;
$actionForm = $archivoAction . "?" . session_name() . "=" . session_id() . "&krd=" . $krd;

$tpl->setVariable('TITULO_FORM', $tituloForm);
$tpl->setVariable('ACTION_FORM', $actionForm);

foreach ($opcionMenu as $opcion) {
    $tpl->setVariable('TITULO', $cont . '.' . $opcion['TITULO']);
    $tpl->setVariable('ENLACE', $opcion['ENLACE'] . $varEnviar);
    $cont ++;
    $tpl->parse('row');
}

$tpl->show();
?>