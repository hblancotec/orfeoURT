<?php
    require_once ('./_conf/constantes.php');
    require_once (PEARPATH . 'HTML/Template/IT.php');
    
    if (!isset($_SESSION['dependencia'])) include (ORFEOPATH . "rec_session.php");
    
    $krd        = $_POST['krd'];
    $carpetaInf = $POST['carpetaInf'];
    $usuaDoc    = $_SESSION['usua_doc'];
    $depeCodi   = $_SESSION['dependencia'];
    $usuaLogin  = $krd;
    $tituloForm = 'CARPETA DE INFORMADOS';

    $krdOld     = $krd;
    $carpetaOld = $carpeta;
    $tipoCarpOld= $tipo_carp;
    session_start();
    if(!$krd) $krd = $krdOsld;
    
    $ADODB_COUNTRECS = false;
    require_once(ORFEOPATH . "include/db/ConnectionHandler.php");
    require_once(ORFEOPATH . "include/combos.php");
    if(!$carpeta) $carpeta = $carpetaOld;
    $ADODB_COUNTRECS = false;
    $db = new ConnectionHandler(ORFEOPATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
    $tpl = new HTML_Template_IT(TPLPATH);
    $tpl->loadTemplatefile('listaCarpetaInf.tpl');
    $tpl->setVariable('TITULO_FORM',$tituloForm);
    $tpl->show();
?>
