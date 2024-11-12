<?php
try{
include_once "$ruta_raiz/webServices/Cliente/WSClient.php";
require_once ($ruta_raiz . "/include/log.php");
logError ( "============== Cliente Radicacion Entrada = $nurad = Fecha:" . date ( 'd-m-Y H:i:s' ) . "===================== ","WsClientOrfeoRadEnt");
$numeroRadicado = $nurad;
//$fechaHoraRadicacion = date ( 'd-M-Y h:m.s' );
$fechaHoraRadicacion = date ( 'd/m/Y H:i:s' );
$usuarioRadicador = $krd;
$medioRecepcion = $med;
$processDefinition = "Recepcion Documental";
$dependenciaDestino = $coddepe;
$parametros = array ('numeroRadicado' => $numeroRadicado, 'usuarioRadicador' => $usuarioRadicador, 'fechaHoraRadicacion' => $fechaHoraRadicacion, 'dependenciaDestino' => $dependenciaDestino, 'processDefinition' => $processDefinition );
$parametrosBusqueda = array ('MEDIORECEPCION' => $medioRecepcion );
$parametrosDesicion = array ();
$manejadorClientes = new WSClientManager ( );
$manejadorClientes->callWSClients ( 'radicacionEnt', $parametros, $parametrosBusqueda, $parametrosDesicion );
}
catch (Exception  $e){
	//echo  'fallo  Del  cLIENTE servicio  web';
	logError ( "== Cliente Radicacion Entrada == ERROR : Fallo  ejecucion de  Cliente == Fecha:" . date ( 'd-m-Y H:i:s' ) ." \n ".$e->getMessage() ."\n===========================================" ,"WsClientOrfeoRadEnt");
	
}
?>