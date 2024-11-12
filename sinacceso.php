<?php
## SE IDENTIFICA QUE ARCHIVO ES EL QUE ESTA INVOCANDO A ESTE ARCHIVO Y SE CALCULA LA RAIZ DE ORFEO
$raiz = substr_count($_SERVER['PHP_SELF'], '/');

switch ($raiz){
	case 1:	$ruta = "./";
			break;
	case 2:	$ruta = "../";
			break;
	case 3:	$ruta = "../../";
			break;
	case 4:	$ruta = "../../../";
			break;
	case 5:	$ruta = "../../../../";
			break;
	case 6:	$ruta = "../../../../../";
			break;
}

echo "<center> <IMG SRC='" .$ruta. "imagenes/sin_acceso.png' ALT='Pagina no encontrada'> </center>";
?>