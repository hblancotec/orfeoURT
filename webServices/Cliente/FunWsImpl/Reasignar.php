<?php
try {
require_once ($ruta_raiz . "/include/log.php");
logError ( "\n============== Cliente Reasignar = $nurad = Fecha:" . date ( 'd-m-Y H:i:s' ) . "===================== ","WsClientOrfeoReasigna");
$parametrosEnvio = array ();
$parametrosBusqueda = array ();
$parametrosDesicion = array ();
$i = 0;
include_once "$ruta_raiz/webServices/Cliente/WSClient.php";
WHILE ( $i < count ( $radicadosSel ) ) {
	$radicado = $radicadosSel [$i];
    logError ( "Radicado = $radicado \n Dep Dest = $depsel || Dep Origen = $dependencia \n UsuarioDestino = $usCodSelect|| UsuarioOrigen = $codusuario\n","WsClientOrfeoReasigna");
	$dependenciaDestino = $depsel;
	$dependenciaOrigen = $dependencia;
	//$dependenciaOrigen = 529;
	$sqlenvio = "select mrec_codi from radicado where radi_nume_radi=" . $radicadosSel [$i];
	$respuesta = $db->query ( $sqlenvio );
	$medioRecepcion = $respuesta->fields ['MREC_CODI'];
	//$medioRecepcion = 3;
	$processDefinition = "Recepcion Documental";
	$codUsuarioDestino = $usCodSelect;
	$codUsuarioOrigen = $codusuario;
	$comentario = $observa;
	//$parametros = array ('radicado' => $radicado, 'dependenciaDestino' => $dependenciaDestino,'processDefinition' => $processDefinition , 'usuarioOrigen'=> $codUsuarioOrigen ,'usuarioDestino'=> $codUsuarioDestino , 'comentario' => $comentario, 'dependenciaOrigen'=>$dependenciaOrigen);
	$parametros = array ('radicado' => $radicado, 'dependenciaDestino' => $dependenciaDestino, 'processDefinition' => $processDefinition, 'comentario' => $comentario, 'dependenciaOrigen' => $dependenciaOrigen );
	$paramBusqueda = array ('DEPORIGEN' => $dependenciaOrigen, 'DEPDESTINO' => $dependenciaDestino, 'USUORIGEN' => $codUsuarioOrigen, 'USUDESTINO' => $codUsuarioDestino );
	$parametrosDesicion = array ('medioRecepcion' => $medioRecepcion, 'dependenciaOrigen' => $dependenciaOrigen );
	$i ++;
	array_push ( $parametrosEnvio, $parametros );
	array_push ( $parametrosBusqueda, $paramBusqueda );
	//array_push($parametrosDesicion, $paramDesicion);
}
$manejadorClientes = new WSClientManager ( );
$manejadorClientes->callWSClients ( 'reasignar', $parametrosEnvio, $parametrosBusqueda, $parametrosDesicion );
}
catch (Exception $e){
//	echo "Fallo  el  servicio";
logError ( "== Cliente  Reasignar == ERROR : Fallo  ejecucion de  Cliente == Fecha:" . date ( 'd-m-Y H:i:s' ) ." \n ".$e->getMessage() ."\n===========================================" ,"WsClientOrfeoReasigna");	
}
?>

 