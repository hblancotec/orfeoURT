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

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

    require ("../../config.php");
    require ("HTML/Template/IT.php");
    require (ORFEOPATH . "include/db/ConnectionHandler.php");
    // Expresion regular para encontrar las tablas de secuencias
    $patronReg  = '/^SECR_TP[[:digit:]]_[[:digit:]]/';
    $anoFinal   = date("Y") + 5;
    $anoRef     = $anoInicialCreaDir;
    $selectAnos = array();
    $titulo     = 'CREACI&Oacute;N  DE DIRECTORIOS Y INICIALIZACI&Oacute;N DE SECUENCIAS';
    
    // Solo mostrara en el select los directorios con los anos no creados
    while ($anoRef <= $anoFinal) {
        $exiteDir = is_dir (BODEGAPATH . $anoRef);
        if (!$exiteDir) {
            $selectAnos[] = $anoRef;
        }
        $anoRef++;
    }
    
	// Crea una instancia de para el manejo de plantilla
	$tpl = new HTML_Template_IT(TPLPATH);
	//session_start();
	
	//if(!isset($_SESSION['dependencia']) && !isset($_SESSION['cod_local'])) 
    //    include (ORFEOPATH . "rec_session.php");
	$db = new ConnectionHandler(ORFEOPATH);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $tablas = $db->conn->MetaTables('TABLES');
    // Capturando tablas de secuencias para inicializarlas
    switch ($db->driver) {
        case 'oci8' : 
            break;
        case 'mssqlnative' :
            foreach ($tablas as $tabla) {
                $tabla = trim($tabla);
                $encontroSec = preg_match($patronReg, $tabla);
                if ($encontroSec) {
                    $secuencias[] = $tabla;
                }
            }
    }
    
    $tpl->loadTemplatefile("menuIniciarSec.tpl");
    $sqlSec = '';
    $contador = 1;

    foreach ($secuencias as $secuencia) {
        $sqlSec = "SELECT ID FROM $secuencia";
	//$db->conn->debug = true;
        $rsSec = $db->conn->Execute($sqlSec);
        if ($rsSec === false) {
            //exit();
        } else {
            $valorSec = $rsSec->fields['ID'];
        }
        $tpl->setVariable('NOMBRE_SEC',"$contador . $secuencia");
        $tpl->setVariable('VALOR_SEC',$valorSec);
        $contador++;
        $tpl->parse("row");
    }
    // Creo que estoy casado de escribir anos jeje
    foreach ($selectAnos as $year) {
        $tpl->setCurrentBlock('directorio');
        $tpl->setVariable('SELECT_ANOS',$year);
        $tpl->parseCurrentBlock('directorio');
    }
    
    $tpl->setVariable('TITULO', $titulo);
    $tpl->show();
?>
