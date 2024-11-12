<?php
include_once ("./include/db/ConnectionHandler.php");
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(3);

$usDoc	 = $_SESSION['usua_doc'];
$usLogin = $_SESSION['login'];
$numRad  = (isset($_SESSION['verrad']))? $_SESSION['verrad'] : $_REQUEST['verrad'];
$dir = substr($_SERVER['HTTP_REFERER'], 0, 140);


$sql = "INSERT INTO 
			SGD_HIST_CONSULTAS (	USUA_DOC,
									USUA_LOGIN,
									SGD_TTR_CODIGO,
									RADI_NUME_RADI,
									HIST_CON_MODULO)
			VALUES	(	'".$usDoc."',
						'".$usLogin."',
						97,
						".$numRad.",
						'".$dir."' )";

$rs = $db->conn->Execute($sql);
?>