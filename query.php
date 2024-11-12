<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "./sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "./sinpermiso.php");
	exit;
}

if (isset ($_POST['btn_query'])){
	if (!$ruta_raiz) $ruta_raiz=".";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	include_once "adodb/toexport.inc.php";
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	if (stripos(strtolower($_POST['txt_query']), "update")  ||
		stripos(strtolower($_POST['txt_query']), "delete")  ||
		stripos(strtolower($_POST['txt_query']), "insert")  ||
		stripos(strtolower($_POST['txt_query']), "alter table")
	   ) {
	   	$resultado = "El query contine palabras prohibidas (insert, delete, update, alter table, etc)";
	} else {
		if (isset($_POST['btn_check']))	$db->conn->debug = true;
		$rs = $db->conn->CacheExecute(1, $_POST['txt_query']);
		if ($rs) {
			$resultado = rs2html($db, $rs,'border=2 cellpadding=3', false, false, false, false, false, false, false, false, false, false, false);
			
			$path = "bodega/tmp/query.csv";
			$rs->MoveFirst();
			$fp = fopen($path, "w");
			if ($fp) {
				rs2csvfile($rs, $fp); # write to file (there is also an rs2tabfile function)
				fclose($fp);
			}
		} else $resultado = 'Hubo un error en la ejecuci&oacute;n del query.';
	}
} else $resultado = '';
?>
<html>
<head>
<link href="estilos/orfeo.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" name="frm_quey">
Digite sentencia a ejecutar. Solamente puede ejecutar "select". <br>
<input type="checkbox" name="btn_check" id="btn_check"> Habilitar debug<br>
<textarea rows="4" cols="50" name="txt_query" id="txt_query"><?php echo $txt_query; ?></textarea> <br>
<input type="submit" name="btn_query" value="Ejecutar!!!" class="botones" />
</form>
<?php 
if (!empty($path)) echo "<a href=$path target='_blank'><img src='imagenes/csv.png'></a>";
echo $resultado;
?>
</body>
</html>