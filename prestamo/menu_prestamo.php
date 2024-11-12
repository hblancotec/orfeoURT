<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_perm_prestamo'] != 1){
	die(include "../sinpermiso.php");
	exit;
}

$krdOld = $krd;

    if(!$krd) $krd=$krdOsld;
    $ruta_raiz = "..";
    include ($ruta_raiz . "/" . "_conf/constantes.php");
    include ("HTML/Template/IT.php");
    $arregloMenu = array();
    $arregloMenu[]['TITULO'] = "PRESTAMO DE DOCUMENTOS";
    $arregloMenu[]['TITULO'] = "DEVOLUCION DE DOCUMENTOS";
    $arregloMenu[]['TITULO'] = "CANCELAR SOLICITUDES";
    $arregloMenu[]['TITULO'] = "GENERACION DE REPORTES";
    $arregloMenu[]['TITULO'] = "MODIFICAR REGISTRO";

    if(!$_SESSION['dependencia'] or !$_SESSION['tpDepeRad']) include "$ruta_raiz/rec_session.php";
    if(!$carpeta) {
      $carpeta = $carpetaOld;
      $tipo_carp = $tipoCarpOld;
    }

    $tpl = new HTML_Template_IT(TPLPATH);
    $tpl->loadTemplatefile('menuPrestamos.tpl');
    $verrad = "";
    include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
    $db = new ConnectionHandler($ruta_raiz);	 
    
    /********************************************************************************
     *       Filename: menu_prestamo.php                                            *
     *       Modificado:                                                            *
     *          1/3/2006  IIAC  Menu del modulo de prestamos. Carga e inicializa los*
     *                          formularios.                                        *
     ********************************************************************************/
    
    // prestamo CustomIncludes begin
    include ("common.php");   
    // Save Page and File Name available into variables
    $sFileName = "menu_prestamo.php";
    // Variables de control   
    $opcionMenu = strip(get_param("opcionMenu"));
    $tpl->setVariable('OPCION_MENU', $opcionMenu);
    $tpl->setVariable('FILE_NAME', $sFileName);
    $tpl->setVariable('USUA_LOGIN', $krd);
    
    foreach ($arregloMenu as $key => $opcion) {
        $tpl->setVariable('TITULO', $opcion['TITULO']);
        $tpl->setVariable('OPCION', ++$key);
        $tpl->parse('row');
    }
    $tpl->show();
?>
