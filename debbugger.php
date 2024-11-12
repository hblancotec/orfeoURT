<?php

//libreria de funciones para hacer depuracion
//La depuracion se inicia inmediatamente se llama a este archivo, para controlarla debe definir las siguientes variables:
//      -noEjecutarDepuracionAutomatica, para no ejecutar
//      -terminarEjecucion, para, en caso de si ejecutar automaticamente, terminar la ejecucion
// Si desea ver las variables del servidor llame a la funcion mostrarVariablesServidor();
// Si desea ver la tabla de parametros una vez mas (o por primera vez si no ejecuto automaticamente la depuracion) llame a la funcion mostrarParametros();
// Si solo quiere cualquier arreglo (como los son $_GET o$_POST) llame a la funcion escribirArregloDepuracion(arreglo,nombre); con "nombre" un nombre cualquiera para mostrar
//debugger.php v2.1
//Miguel Angel Torres Rodriguez

function mostrarParametros() {
	echo "<table border=1 bgcolor='#99CC66'><tr><td>";
	escribirArregloDepuracion($_GET, "Parametros GET");
	escribirArregloDepuracion($_POST, "Parametros POST");
	escribirArregloDepuracion($_FILES, "Archivos");
	escribirArregloDepuracion($_COOKIE, "Cookies");
	if (isset ($_SESSION))
		escribirArregloDepuracion($_SESSION, "Sesion");
	else
		echo "Session: No iniciada a&uacute;n<br>";
	if (isset ($GLOBALS))
		escribirArregloDepuracion($GLOBALS, "Variables globales");
	echo "</td></tr></table>";
};

function escribirArregloDepuracion($arreglo, $nombre) {
	echo $nombre . ": <blockquote>";
	foreach ($arreglo as $clave => $valor)
		escribirLineaDepuracion($clave, $valor);
	echo "</blockquote>";
}

function escribirLineaDepuracion($clave, $valor) {
	echo $clave . ": ";
	if (is_array($valor))
		print_r($valor);
	else
		echo $valor;
	echo "<br>";
}

function mostrarVariablesServidor() {
	echo "<table border=1 bgcolor='#99CC66'><tr><td>";
	echo "Parametros ENV: <blockquote>";
	foreach ($_ENV as $clave => $valor)
		echo $clave . ": " . $valor . "<br>";
	echo "</blockquote>";
	echo "Parametros del servidor: <blockquote>";
	foreach ($_SERVER as $clave => $valor)
		echo $clave . ": " . $valor . "<br>";
	echo "</blockquote>";
	echo "</td></tr></table>";
}

error_reporting(E_ALL);
if (!isset ($noEjecutarDepuracionAutomatica)) {
	echo "<a href='#' onclick=\"var s=document.getElementById('span_depurador'); if(s.style.display=='none') s.style.display='block'; else s.style.display='none'; \">Mostrar/Ocultar depurador (patente pendiente jeje)</a><span id='span_depurador' style='display:none'>";
	mostrarParametros();
	echo "</span>";
	//mostrarVariablesServidor();
	if (isset ($terminarEjecucion)) {
		die("<p>Depuracion automatica habilitada. Fin de la ejecucion del archivo. <a href='javascript:mostrarSexpdep()' style='font-size:11px'>(*)</a></p>  <script>function mostrarSexpdep(){ var sp=document.getElementById('sexpdep'); if(sp.style.display=='none') sp.style.display=''; else sp.style.display='none'; }</script><p id='sexpdep' style='display:none'><font size=-1>Si desea deshabilitar la depuracion automatica defina \"\$noEjecutarDepuracionAutomatica=true\".<br>Si desea que se ejecute la depuracion automatica pero seguir trabajando en el script defina \"\$noTerminarEjecucion=true\".</font></p>");
	}
}
?>

