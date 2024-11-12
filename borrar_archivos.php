<?php	 
if (!$ruta_raiz) 
$ruta_raiz= ".";
require_once("$ruta_raiz/include/db/ConnectionHandler.php");

if (!$db)
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	
$db->conn->BeginTrans();
$isql ="SELECT	USUA_LOGIN,
				USUA_PASW,
				CODI_NIVEL,
				USUA_NOMB,
				USUA_DOC
		FROM	USUARIO
		WHERE	USUA_LOGIN ='$usua'";
$rs = $db->conn->Execute($isql);
if  ($rs && !$rs->EOF){
	$secur=$rs->fields['CODI_NIVEL'];
	//Traigo el nombre del usuario para ponerlo en la descripcion del historico
	$nombreUsuario = $rs->fields['USUA_NOMB'];
	$doc_usuario = $rs->fields['USUA_DOC'];
}
if (!$secur){
	$mensaje="No tiene permisos para borrar el documento";        
}
if ($secur) {
	$isql = "SELECT	CODI_NIVEL,
					ANEX_SOLO_LECT,
					ANEX_CREADOR,
					ANEX_DESC,
					ANEX_TIPO_EXT,
					ANEX_NUMERO,
					ANEX_NOMB_ARCHIVO". 
			" FROM	ANEXOS,
					ANEXOS_TIPO,
					RADICADO".
			" WHERE	ANEX_CODIGO = '$anexo' 
					AND ANEX_RADI_NUME = RADI_NUME_RADI
					AND ANEX_TIPO = ANEX_TIPO_CODI";

	$rs=$db->conn->Execute($isql);
	if  ($rs && !$rs->EOF){
		$docunivel=$rs->fields['CODI_NIVEL']; 
		$sololect=($rs->fields['ANEX_SOLO_LECT']=="S");
		$extension=$rs->fields['ANEX_TIPO_EXT']; 
		$usua_creador=($rs->fields['ANEX_CREADOR']==$usua); 
		$nombrearchivo=strtoupper($rs->fields['ANEX_NOMB_ARCHIVO']);
	if ($docunivel>$nivel)
		$secur=0;
	}
	else{
		$mensaje="El archivo que desea borrar no existe: Por favor consulte al administrador del sistema";	
	}
}
   
//$bien=unlink(trim($linkarchivo));
$bien=true;
if ($bien){
	$isql ="UPDATE	ANEXOS
     		SET		ANEX_BORRADO = 'S',
					ANEX_SALIDA = NULL".
           " WHERE	ANEX_CODIGO = '$anexo'";
	$bien1= $db->conn->Execute($isql); 
	$isql ="INSERT	INTO HIST_EVENTOS_ANEXOS
     				(ANEX_RADI_NUME, ANEX_CODIGO, SGD_TTR_CODIGO, USUA_DOC)
			VALUES	($numrad, '$anexo', 31, '$doc_usuario')";
	$bien2= $db->conn->Execute($isql);
}  
if ($bien1 && $bien2){ 
	include "$ruta_raiz/include/tx/Historico.php";
	$hist = new Historico($db);
	$anexBorrado = array();
	$anexBorrado[] = $numrad;
	$observa = "Se Elimina Anexo Digitalizado con Codigo: $anexo. Eliminado por: $nombreUsuario.";
	$codTx = 31; //Codigo correspondiente a la eliminación de anexos
	$hist->insertarHistorico($anexBorrado,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
	$mensaje="<span class='info'>Archivo eliminado<span><br> ";
	$db->conn->CommitTrans(); 
}
else {
	$mensaje="<span class='alarmas'>No fue posible eliminar Archivo<span></br>";
	$db->conn->RollbackTrans();
}
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Informacion de Anexos</title>
  <link rel="stylesheet" href="estilos/orfeo.css">
 </head>
 <script language="javascript">

	function actualizar()
	{
		archivo=document.forma.userfile.value;
		if (archivo==""){
			if (document.forma.sololect.checked!=true)
				alert("Por favor escoja un archivo");
			else	
				document.forma.submit();
		}
		else if (archivo.toUpperCase().substring(archivo.length-<?=strlen(trim($nombrearchivo))?>,archivo.length)!="<?=trim($nombrearchivo)?>") {
			if (confirm("Al parecer va a modificar un archivo diferente del original. Esta seguro?"))
				document.forma.submit();
		}
		else {
			document.forma.submit();
		}
	}
 </script>
 <body bgcolor="#FFFFFF" topmargin="0"> <br>
  <div align="center">
   <p> <?=$mensaje?> </p>
   <input type='button' class="botones" value='cerrar' onclick='opener.regresar();window.close();'>
  </div>
 </body>
</html>