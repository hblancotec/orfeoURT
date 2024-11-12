<?php
    $ruta_raiz      = "..";
    require_once $ruta_raiz . '/_conf/constantes.php';
    require_once 'HTML/Template/IT.php';
    require_once ORFEOPATH . 'class_control/Dependencia.php';

    $cont       = 0;
    $krdOld     = $krd;
    $carpetaOld = $carpeta;
    $tipoCarpOld= $tipo_carp;
    $moverSelect= 0;
    $usuaDoc    = 0;

    // si llegan datos del grid de cuerpo, lista de numeros de radicados
    // separados por coma.
    include_once(ORFEOPATH . "php-ext/php-ext/php-ext.php");
    include_once(NS_PHP_EXTJS_CORE);
    include_once(NS_PHP_EXTJS_DATA);
    include_once(NS_PHP_EXTJS_GRID);

    //reemplaza el post por los seleccinados del grid
    if (isset($_POST['seleccionados'])) {
	$keys = explode(",", $_POST['seleccionados']);
	$seleccionados = array();
	while (list($temp, $recordid) = each($keys)) {
	    $seleccionados[$recordid] = $temp;
	}
    };

    session_start();
    $krd = (!$krd) ? $krdOsld : $krd;
    $mensaje_error  = false;
    $subCarpDest    = '';
    if(!isset($_SESSION['dependencia']))  include (ORFEOPATH . "rec_session.php");
    $depeCodi = $_SESSION['dependencia'];

    // Inclusion de archivos para utilizar la libreria ADODB
    require_once (ORFEOPATH . "include/db/ConnectionHandler.php");
    if (!isset($db)) $db = new ConnectionHandler(ORFEOPATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $tpl    = new HTML_Template_IT(TPLPATH);
    $objDep = new Dependencia($db);
    /*
     * Genreamos el encabezado que envia las variable a la paginas siguientes.
     * Por problemas en las sesiones enviamos el usuario.
     * @$encabezado  Incluye las variables que deben enviarse a la singuiente pagina.
     * @$linkPagina  Link en caso de recarga de esta pagina.
     */
    $encabezado  = session_name()."=".session_id();
    $encabezado .= "&krd=$krd&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
    $linkPagina  = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=";
 ?>

<html>
<head>
<title>Enviar Datos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="<?=$ruta_raiz;?>/extjs/resources/css/ext-all.css">
<script src="<?=$ruta_raiz;?>/extjs/adapter/ext/ext-base.js"></script>
<script src="<?=$ruta_raiz;?>/extjs/ext-all.js"></script>
<link rel="stylesheet" href="<?=$ruta_raiz;?>/estilos/orfeo.css">
<script language="javascript">
</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
<?php
function actualizarMedioRecepcion($numRadicado, $tema){
	$ruta_raiz = "../";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$sqlInf = "UPDATE RADICADO SET MREC_CODI = " . $tema . " WHERE RADI_NUME_RADI = " . $numRadicado;
   	$rsInf = $db->conn->Execute($sqlInf);
   	$URL = $_SERVER['HTTP_REFERER'];
   	$status = "Se actualizo el medio de recepcion de los radicados seleccionados.";
   	$_SESSION['mensaje']= $status;
   	echo "<script>location='{$URL}'</script>";
   	return true;
 }
$tema = $_POST['usMedioSelect'];
foreach( $keys as $key => $value ) {
	$validacion = actualizarMedioRecepcion($value, $tema);
	if (!$validacion){
		break;
	}
};
?>
</body>
</html>
