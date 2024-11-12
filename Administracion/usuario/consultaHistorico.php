<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	extract($_GET);
	extract($_SESSION);
}
else {
	$krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz = "../..";
if (!$_SESSION['dependencia'])   include "../../rec_session.php";

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$nomcarpeta = "Consulta Historico";

if ($orden_cambio==1)  {
    if (!$orderTipo)  {
        $orderTipo="desc";
    }else  {
        $orderTipo="";
    }
}

$encabezado = "".session_name()."=".session_id()."&krd=$krd&pagina_sig=$pagina_sig&usuLogin=$usuLogin&nombre=$nombre&dependencia=$dependencia&dep_sel=$dep_sel&selecdoc=$selecdoc&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=";
$linkPagina = "$PHP_SELF?$encabezado&usuLogin=$usuLogin&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=$orderNo";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../estilos/orfeo.css">
</head>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
<center>
<table border=1 width=98% class=t_bordeGris>
<tr>
    <td colspan="2" class="titulos4">
<p><center><B><span class=etexto>ADMINISTRACI&Oacute;N DE USUARIOS Y PERFILES</span></B></center> </p>
<p><center><B><span class=etexto>Consulta de Usuario</span></B></center> </p> 
</td>
</tr>
</table>
<table border=1 width=98% class=t_bordeGris>
<td align="left" class="titulos4" width="40%">
Datos Hist&oacute;ricos
</td>
</table>
</center>
<table align="center" border=1 width=98% class=t_bordeGris>
<tr>
<td align="left" class="titulos2" width="40%">
Usuario: <?=$usuLogin?>
</td>
<td align="left" class="titulos2" width="40%">
Nombre: <?=$nombre?>
</td>
</tr>
</table>
  <form name=formHistorico action='consultaHistorico.php?<?=$encabezado?>' method=post>
<?php
    $orderNo = $_GET['orderNo'];
    if ($orderNo==98 or $orderNo==99) {
       $order=1; 
	   if ($orderNo==98)   $orderTipo="desc";

       if ($orderNo==99)   $orderTipo="";
	}  
    else  {
	   if (!$orderNo)  {
  		  $orderNo=0;
	   }
	   $order = $orderNo + 1;
    }
//	$sqlChar = $db->conn->SQLDate("d-m-Y H:i A","SGD_RENV_FECH");
	include "$ruta_raiz/include/query/administracion/queryConsultaHistorico.php";

    $rs=$db->conn->Execute($isql);

	$nregis = $rs->fields["ADMINISTRADOR"];		
	if (!$nregis)  {
		echo "<hr><center><b>NO se encontro nada con el criterio de busqueda</center></b></hr>";}
	else  {
		$pager = new ADODB_Paginacion($db,$isql,'adodb', true,$orderNo,$orderTipo);
		$pager->toRefLinks = $linkPagina;
		$pager->toRefVars = $encabezado;
		$pager->Render($rows_per_page=20,$linkPagina,$checkbox='chkEnviar');
	}
 $encabezado = "".session_name()."=".session_id()."&krd=$krd&pagina_sig=$pagina_sig&usuLogin=$usuLogin&dependencia=$dependencia&dep_sel=$dep_sel&selecdoc=$selecdoc&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=";
 ?>

	</form>
</body>

</html>

