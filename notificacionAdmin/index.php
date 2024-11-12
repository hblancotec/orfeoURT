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
if ($_SESSION['usua_perm_notifAdmin'] != 1){
	die(include "../sinpermiso.php");
	exit;
}

	$ruta_raiz = "..";
	extract($_GET, EXTR_SKIP);extract($_POST, EXTR_OVERWRITE);
	
	if (!$_SESSION['dependencia'] and !$_SESSION['depe_codi_territorial'])
		include "../rec_session.php";
    
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	//$db->conn->debug = true;
	
	### SE INCLUYE ARCHIVO EN DONDE SE ARMAN LOS REPORTES EN EXCEL
	require ("$ruta_raiz/include/rs2xml.php");
	$obj = new rs2xml();
	
	if(!$dep_sel) 
		$dep_sel = $dependencia;
	$depeBuscada = $dep_sel;
	
	if (!$dep_sel) 
		$dep_sel = $dependencia;
    
	$depeBuscada	= $dep_sel;
	$dependencia	= $_SESSION["dependencia"];
	$buscarRad		= $_POST['busqRadicados'];
	$fecha1			= $_POST['fecha1'];
	
	if($_GET['ob']){
		echo $_GET['ob'];
	}
	
	if ($_GET['er']){
		echo $_GET['er'];
	}
?>

<html>
	<head>
		<title>Notificaciones Administrativas</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../estilos/orfeo.css">
		<script language="javascript">
			<!-- Funcion que activa el sistema de marcar o desmarcar todos los check  -->
		</script>
	</head>
	<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
		<div id="spiffycalendar" class="text"></div>
		<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
		<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
		<script language="javascript">
			<?php
				if ($_GET['sin'] == 9){
				?>	
					alert("El radicado no tiene imagen asociada y no se puede continuar con la notificación!");
				<?php
				}
				if (!$_POST['fecha1']){
					$ano_ini = date("Y");
					$mes_ini = substr("00".(date("m")-1),-2);
					if ($mes_ini=="00") {
						$ano_ini=$ano_ini-1;
						$mes_ini="12";
					}
					$dia_ini = date("d");
					if(!$fecha_ini) 
						$fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
					$fecha_busq = date("Y/m/d");
					if(!$fecha_fin)
						$fecha_fin = $fecha_busq;
				}
				else {
					$fecha_ini = $_POST['fecha1'];
				}
			?>
			var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formulario", "fecha1","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
		</script>

<?php
	$encabezado  = session_name()."=".session_id();
	$encabezado .= "&krd=$krd&filtroSelect=$filtroSelect";
	$encabezado .= "&accion_sal=$accion_sal&dep_sel=$dep_sel";
	$linkPagina  = "$PHP_SELF?$encabezado&accion_sal=$accion_sal";
	include "../envios/paEncabeza.php";
?>

		<form name=formulario action='<?=$pagina_actual?>?<?=session_name()."=".session_id()."&krd=$krd"?>&pagina_sig=<?=$pagina_sig?>' method=post>
			<br>
			<table align="center" cellspacing="5" width="25%" class="borde_tab">
				<tr>
					<td class="titulos4" colspan=2 align="center"> Notificaciones Administrativas </td>
				</tr>
				<tr>
					<td class="listado2" style="font-size: 12"> No. radicado: </td>
					<td> <input name="busqRadicados" type="text" size="20" class="tex_area" value="<?=$busqRadicados?>"> </td>
				</tr>
				<tr>
					<td class="listado2" style="font-size: 12"> Fecha de filtro inicial</td>
					<td>
						<script language="javascript">
							dateAvailable.writeControl();
							dateAvailable.dateFormat="yyyy/MM/dd";
						</script>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td class="etextomenu" colspan="2" align="center">
						<input type=submit value='Buscar ' name=Buscar valign='middle' class='botones'>
					</td>
				</tr>
			</table>
		</form>

<?php
	### CONSULTA DE LOS RADICADOS DEVUELTOS
	$sqlDev = "	SELECT	D.DEPE_NOMB AS DEPENDENCIA,
						V.RADI_NUME_SAL AS RADICADO,
						V.SGD_DEVE_FECH AS FECHA_DEV,
						V.SGD_RENV_NOMBRE AS DESTINATARIO,
						V.SGD_RENV_DIR AS DIRECCION,
						V.SGD_RENV_DEPTO AS DEPARTAMENTO,
						V.SGD_RENV_MPIO AS MUNICIPIO
				FROM	SGD_RENV_REGENVIO AS V
						JOIN DEPENDENCIA AS D ON
							D.DEPE_CODI = V.SGD_DEPE_GENERA
				WHERE	V.SGD_DEVE_CODIGO > 0 AND
						V.SGD_DEVE_FECH > '".$fecha1."'
				ORDER BY V.SGD_DEVE_FECH";
	$rsDev = $db->conn->Execute($sqlDev);
	
	if ($rsDev) {
		$path = "../bodega/tmp/devueltos".date("dmYh").time("his").".csv";
		
		$Rs2Xml = $obj->getXML($rsDev);
		$arch = $krd."_".rand(10000, 20000);
		$ruta = "../bodega/tmp/$arch.xls";
		$fpDev = fopen($ruta, "w");
		if ($fpDev) {
			fwrite($fpDev, $Rs2Xml);
			fclose($fpDev);
		}
	}
	else
		$resultado = 'Hubo un error en cración del reporte de devoluciones.';
	
	echo "&nbsp; <a href='$ruta' target='_blank'> Reporte Devoluciones </a>";
	### FIN - CONSULTA DE LOS RADICADOS DEVUELTOS
	
	
	### SE VERIFICA SI EL USUARIO DIGITO UN No. DE RADICADO
	if ($buscarRad){
		$where = 'R.RADI_NUME_RADI ='. $buscarRad;
	}
	else {
		$where = "R.RADI_NOTIFICADO = 1 AND R.RADI_NOTIF_FIJACION >= '".$fecha1."'";
	}
	
	
	### CONSULTA DE LOS RADICADOS MARCADOS PARA NOTIFICAR
	$isql ="SELECT	R.RADI_NUME_RADI AS RADICADO,
					D.DEPE_NOMB AS DEPENDENCIA,
					DR.SGD_DIR_NOMREMDES AS DESTINATARIO,
					DR.SGD_DIR_DOC AS IDENTIFICACION,
					convert(varchar(10),R.RADI_NOTIF_FIJACION, 103) AS FECHA_FIJACION,
					convert(varchar(10),R.RADI_NOTIF_DESFIJACION, 103) AS FECHA_DESFIJACION,
					convert(varchar(15),R.RADI_NUME_RADI) AS CHK_RAD
			FROM	RADICADO R
					INNER JOIN DEPENDENCIA D ON
						D.DEPE_CODI = R.RADI_DEPE_RADI
					INNER JOIN SGD_DIR_DRECCIONES DR ON
						DR.RADI_NUME_RADI = R.RADI_NUME_RADI AND
						DR.SGD_DIR_TIPO = 1
			WHERE	$where
			ORDER BY FECHA_FIJACION, RADICADO";
	$rs = $db->conn->Execute($isql);
	
	if ($rs) {
		$path = "../bodega/tmp/notificacion".date("dmYh").time("his").".csv";
		unset($rs->fields['CHK_RAD']);
		$txtRs2Xml = $obj->getXML($rs);
		$archivo = $krd."_".rand(10000, 20000);
		$path = "../bodega/tmp/$archivo.xls";
		$fp = fopen($path, "w");
		if ($fp) {
			fwrite($fp, $txtRs2Xml);
			fclose($fp);
		}
	}
	else
		$resultado = 'Hubo un error en cración del archivo csv.';
	
	echo "&nbsp; &nbsp; &nbsp; <a href='$path' target='_blank'> Reporte Notificados </a> <br/>";
	### FIN - CONSULTA DE LOS RADICADOS MARCADOS PARA NOTIFICAR
	
	
	$pagina_actual = "../notificacionAdmin/index.php";
	$varBuscada = "radi_nume_radi";
	$pagina_sig = "../notificacionAdmin/notificar.php";
	$accion_sal="Editar";
	include "../envios/paOpciones.php";
?>

		<form name=formEnviar action='../notificacionAdmin/notificar.php?<?=session_name()."=".session_id()."&krd=$krd" ?>&depeBuscada=<?=$depeBuscada?>&pagina_sig=<?=$pagina_sig?>' method=post>
 
<?php
	//$db->conn->debug=true;
	$encabezado = "".session_name()."=".session_id()."&krd=$krd&depeBuscada=$depeBuscada&accion_sal=$accion_sal&filtroSelect=$filtroSelect&dep_sel=$dep_sel";
	$linkPagina = "$PHP_SELF?$encabezado";
	$pager = new ADODB_Paginacion($db,$isql,'adodb', true,$orderNo,$orderTipo);
	$pager->checkAll = false;
	$pager->checkTitulo = true;
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	$pager->Render(20,$linkPagina,'chkEnviar');
 ?>
		
		</form>
	</body>
</html>