<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php 
   include_once "./include/db/ConnectionHandler.php";
	$db = new ConnectionHandler(".");
   $isql = "Select * from  usuario where usua_login like '%JH%'";
   $pager = new ADODB_Pager($db->conn,$isql,'adodb', true,$orderNo,$orderTipo);
   $pager->toRefLinks = $linkPagina;
   $pager->Render($rows_per_page=75,$linkPagina,$checkbox=chkAnulados);
?>
</body>
</html>
