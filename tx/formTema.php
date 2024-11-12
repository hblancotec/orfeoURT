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
function validarTRDRadicado($numRadicado, $tema){
	$ruta_raiz = "../";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$serie_grb = -1;
	$subserie_grb = -1;
	$isql = "SELECT r.RADI_NUME_RADI ,
					m.SGD_SRD_CODIGO,
					s.SGD_SRD_CODIGO,
					s.SGD_SRD_DESCRIP,
					su.SGD_SBRD_CODIGO,
					su.SGD_SBRD_DESCRIP,
					t.SGD_TPR_CODIGO,
					t.SGD_TPR_DESCRIP
			 	FROM sgd_rdf_retdocf r,
					sgd_mrd_matrird m,
					sgd_srd_seriesrd s,
					sgd_sbrd_subserierd su,
					sgd_tpr_tpdcumento t
			  	WHERE r.sgd_mrd_codigo = m.sgd_mrd_codigo and
					r.RADI_NUME_RADI = $numRadicado and
					s.sgd_srd_codigo = m.sgd_srd_codigo and
					su.sgd_srd_codigo = m.sgd_srd_codigo and
					su.sgd_sbrd_codigo = m.sgd_sbrd_codigo and
					t.sgd_tpr_codigo = m.sgd_tpr_codigo";

	$sgd = $db->conn->Execute($isql);
	if (!$sgd->EOF) {
		//$cod_guardado = $sgd->fields["SGD_SRD_CODIGO"];
		$serie_grb = $sgd->fields["SGD_SRD_CODIGO"];
		$subserie_grb = $sgd->fields["SGD_SBRD_CODIGO"];
	}
	$status = "";
	if($serie_grb != -1 && $subserie_grb != -1){
		$sqlInf = "UPDATE RADICADO SET RADI_TEMA_ID = " . $tema . " WHERE RADI_NUME_RADI = " . $numRadicado;
    	$rsInf = $db->conn->Execute($sqlInf);
    	$URL = $_SERVER['HTTP_REFERER'];
    	$status = "Se le asigno tema a los radicados seleccionados.";
    	$_SESSION['mensaje']= $status;
    	echo "<script>location='{$URL}'</script>";
    	return true;
	}else{
		$status = "No se le asigno tema al radicado " .$numRadicado . ". Falta asignar TRD.";
		$_SESSION['mensaje']= $status;
	 	$URL = $_SERVER['HTTP_REFERER'];
		echo "<script>location='{$URL}'</script>";
		return false;
	}

 }
$tema = $_POST['usTemaSelect'];
foreach( $keys as $key => $value ) {
	$validacion = validarTRDRadicado($value, $tema);
	if (!$validacion){
		break;
	}
};
?>
</body>
</html>
