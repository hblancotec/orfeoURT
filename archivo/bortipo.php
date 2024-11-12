<?php
$krdOld = $krd;
session_start();

if(!$krd) $krd = $krdOld;
if (!$ruta_raiz) $ruta_raiz = "..";
include "$ruta_raiz/rec_session.php";
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once "$ruta_raiz/include/tx/Historico.php";
include_once "$ruta_raiz/include/tx/Expediente.php";
$db = new ConnectionHandler( "$ruta_raiz" );
//$db->conn->debug = true;
$encabezadol = $_SERVER['PHP_SELF']."?".session_name()."=".session_id()."&dependencia=$dependencia&krd=$krd&cod=$cod&tipo=3";
$encabezado2 = $_SERVER['PHP_SELF']."?".session_name()."=".session_id()."&dependencia=$dependencia&krd=$krd&cod=$cod&tipo=4";
$encabezado3 = $_SERVER['PHP_SELF']."?".session_name()."=".session_id()."&dependencia=$dependencia&krd=$krd&cod=$cod&tipo=6";
?>
<html>
<head>
<title>BORRAR TIPOS</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body bgcolor="#FFFFFF">
<?php
if($tipo==1){
?>
<form name="borrar" action="<?=$encabezadol?>" method="POST" >
<table width="90%" align="center" >
<tr><td class="titulos5">Esta seguro de borrar este edificio, con toda su informacion?</td></tr>
<tr><td><input type="submit" name="borrar" value="Borrar" align="middle" class="botones">
</td></tr>
</table>
<?php
}
if($tipo==3){
$pru=$db->conn->Execute("select sgd_exp_edificio from sgd_exp_expediente where sgd_exp_edificio like '$cod'");
if($pru->RecordCount()==0){
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$cod'";
//$db->conn->debug=true;
$rs=$db->conn->Execute($sql);
$sqli="select sgd_eit_codigo from sgd_eit_items where sgd_eit_cod_padre like '$cod'";
$rsi=$db->conn->Execute($sqli);
while(!$rsi->EOF){
$codi=$rsi->fields['SGD_EIT_CODIGO'];
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$codi'";
$rs=$db->conn->Execute($sql);
$sqli2="select sgd_eit_codigo from sgd_eit_items where sgd_eit_cod_padre like '$codi'";
$rsi2=$db->conn->Execute($sqli2);
while(!$rsi2->EOF){
$codi2=$rsi2->fields['SGD_EIT_CODIGO'];
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$codi2'";
$rs=$db->conn->Execute($sql);
$sqli3="select sgd_eit_codigo from sgd_eit_items where sgd_eit_cod_padre like '$codi2'";
$rsi3=$db->conn->Execute($sqli3);
while(!$rsi3->EOF){
$codi3=$rsi3->fields['SGD_EIT_CODIGO'];
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$codi3'";
$rs=$db->conn->Execute($sql);
$sqli4="select sgd_eit_codigo from sgd_eit_items where sgd_eit_cod_padre like '$codi3'";
$rsi4=$db->conn->Execute($sqli4);
while(!$rsi4->EOF){
$codi4=$rsi4->fields['SGD_EIT_CODIGO'];
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$codi4'";
$rs=$db->conn->Execute($sql);
$sqli5="select sgd_eit_codigo from sgd_eit_items where sgd_eit_cod_padre like '$codi4'";
$rsi5=$db->conn->Execute($sqli5);
while(!$rsi5->EOF){
$codi5=$rsi5->fields['SGD_EIT_CODIGO'];
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$codi5'";
$rs=$db->conn->Execute($sql);
$sqli6="select sgd_eit_codigo from sgd_eit_items where sgd_eit_cod_padre like '$codi5'";
$rsi6=$db->conn->Execute($sqli6);
while(!$rsi6->EOF){
$codi6=$rsi6->fields['SGD_EIT_CODIGO'];
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$codi6'";
$rs=$db->conn->Execute($sql);
$sqli7="select sgd_eit_codigo from sgd_eit_items where sgd_eit_cod_padre like '$codi6'";
$rsi6=$db->conn->Execute($sqli7);
while(!$rsi7->EOF){
$codi7=$rsi7->fields['SGD_EIT_CODIGO'];
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$codi7'";
$rs=$db->conn->Execute($sql);
$rsi7->MoveNext();
}
$rsi6->MoveNext();
}
$rsi5->MoveNext();
}
$rsi4->MoveNext();
}
$rsi3->MoveNext();
}
$rsi2->MoveNext();
}
$rsi->MoveNext();
}
echo "Toda la informacion del edificio fue borrada";
}
else echo "Existen registros en la tabla sgd_exp_expediente por lo tanto no se puede borrar este edificio";
?>
<input name="Cerrar" type="button" class="botones" id="envia22" onClick="opener.regresar();window.close();" value=" Cerrar " >
<?php
}
?>
</form>
<?php
if($tipo==2){
?>
<form name="borrar2" action="<?=$encabezado2?>" method="POST" >
<table width="90%" align="center" >
<tr><td class="titulos5">Esta seguro de borrar este tipo?</td></tr>
<tr><td><input type="submit" name="borrar" value="Borrar" align="middle" class="botones">
</td></tr>
</table>
<?php
}
if($tipo==4){
$pru=$db->conn->Execute("select sgd_exp_edificio from sgd_exp_expediente where sgd_exp_entrepa like '$cod' or sgd_exp_caja like '$cod'");
if($pru->RecordCount()==0){
$sql="delete from sgd_eit_items where sgd_eit_codigo like '$cod'";
$rs=$db->conn->Execute($sql);
echo "Registro borrado";
}
else echo "Existen registros en la tabla sgd_exp_expediente por lo tanto no se puede borrar este item";
?>
<input name="Cerrar" type="button" class="botones" id="envia22" onClick="opener.regresar();window.close();" value=" Cerrar " >
<?php
}
if($tipo==5){
?>
<form name="borrar2" action="<?=$encabezado3?>" method="POST" >
<table width="90%" align="center" >
<tr><td class="titulos5">Esta seguro de borrar esta relacion?</td></tr>
<tr><td><input type="submit" name="borrar" value="Borrar" align="middle" class="botones">
</td></tr>
</table>
<?php
}
if($tipo==6){
$sql="delete from sgd_arch_depe where sgd_arch_id like '$cod'";
$rs=$db->conn->Execute($sql);
echo "Registro borrado";
?>
<input name="Cerrar" type="button" class="botones" id="envia22" onClick="opener.regresar();window.close();" value=" Cerrar " >
<?php
}
?>
</form>