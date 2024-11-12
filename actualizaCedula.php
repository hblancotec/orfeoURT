<?php
$ruta_raiz = ".";
include_once ("config.php");
include 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
$conn = NewADOConnection($dsn);

// Cedula anterior
$ca = "66924253";
//cedula nueva
$cn = "669242531";

if ($conn)
{	$conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$conn->debug = true;
	$conn->StartTrans();
	$conn->Execute("UPDATE CARPETA_PER SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_APLUS_PLICUSUA SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");	//Tabla sin datos
	$conn->Execute("UPDATE SGD_RENV_REGENVIO SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_ADMIN_USUA_HISTORICO SET USUARIO_DOCUMENTO_ADMINISTRADOR = '$cn' WHERE USUARIO_DOCUMENTO_ADMINISTRADOR = '$ca' ");
	$conn->Execute("UPDATE SGD_ADMIN_USUA_HISTORICO SET USUARIO_DOCUMENTO_MODIFICADO = '$cn' WHERE USUARIO_DOCUMENTO_MODIFICADO = '$ca' ");
	$conn->Execute("UPDATE SGD_HFLD_HISTFLUJODOC SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_USH_USUHISTORICO SET SGD_USH_ADMDOC = '$cn' WHERE SGD_USH_ADMDOC = '$ca' ");
	$conn->Execute("UPDATE SGD_USH_USUHISTORICO SET SGD_USH_USUCOD = '$cn' WHERE SGD_USH_USUCOD = '$ca' ");
	$conn->Execute("UPDATE SGD_ANU_ANULADOS SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_ANU_ANULADOS SET USUA_DOC_ANU = '$cn' WHERE USUA_DOC_ANU = '$ca' ");
	$conn->Execute("UPDATE SGD_INFDIR_INFORMADOSDIR SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE ENCUESTA SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");	//Tabla sin datos
	$conn->Execute("UPDATE SGD_FIRRAD_FIRMARADS SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");	//Tabla sin datos
	$conn->Execute("UPDATE SGD_AGEN_AGENDADOS SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE RADICADO SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_CAUX_CAUSALES SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE ANEXOS SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_HMTD_HISMATDOC SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_RDF_RETDOCF SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE HIST_EVENTOS SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE HIST_EVENTOS SET USUA_DOC_OLD = '$cn' WHERE USUA_DOC_OLD = '$ca' ");
	$conn->Execute("UPDATE SGD_EXP_EXPEDIENTE SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE PRESTAMO SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE INFORMADOS SET INFO_CODI = '$cn' WHERE INFO_CODI = '$ca' ");
	$conn->Execute("UPDATE INFORMADOS SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_DIR_DRECCIONES SET SGD_DOC_FUN = '$cn' WHERE SGD_DOC_FUN = '$ca' ");
	$conn->Execute("UPDATE SGD_SEXP_SECEXPEDIENTES SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_SEXP_SECEXPEDIENTES SET USUA_DOC_RESPONSABLE = '$cn' WHERE USUA_DOC_RESPONSABLE = '$ca' ");
	$conn->Execute("ALTER TABLE SGD_USD_USUADEPE DROP CONSTRAINT FK_SGD_USD_USUADEPE_USUARIO1");
	$conn->Execute("ALTER TABLE USUARIO DROP CONSTRAINT USUARIO_PK");
	$conn->Execute("UPDATE USUARIO SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("UPDATE SGD_USD_USUADEPE SET USUA_DOC = '$cn' WHERE USUA_DOC = '$ca' ");
	$conn->Execute("ALTER TABLE USUARIO ADD CONSTRAINT USUARIO_PK PRIMARY KEY (USUA_LOGIN, USUA_DOC)");
	$conn->Execute("ALTER TABLE SGD_USD_USUADEPE ADD CONSTRAINT FK_SGD_USD_USUADEPE_USUARIO1 FOREIGN KEY (USUA_LOGIN, USUA_DOC) REFERENCES USUARIO(USUA_LOGIN, USUA_DOC)");
	$ok = $conn->CompleteTrans();
	$msg = ($ok) ? "C&eacute;dula $ca modificada a $cn exit&oacute;samente.<br/>" : "Error al generar transacci&oacute;n";
	echo $msg;
} else {
	echo "<b>No hay conexi&oacute;n a BD</b>";
}
?>