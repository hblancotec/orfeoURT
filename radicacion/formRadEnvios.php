<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit;
}
else {
    extract($_SESSION);
}

if ($_SESSION['usua_perm_envios'] == 0){
    die(include "../sinpermiso.php");
    exit;
}
$ruta_raiz = "..";
if(!$_SESSION['dependencia'])	
	include "$ruta_raiz/rec_session.php";
?>

<html>
	<head>
		<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<table width="100%" border="0" cellspacing="5" class="borde_tab">
			<tr class='titulos2'>
				<td colspan="7">
					<img src='../imagenes/correo.gif'> Envio de Correspondencia &nbsp;
				</td>
			</tr>
			<tr class='listado2'>
				<td class='listado2'>
					<a href='../envios/cuerpoEnvioNormal.php?<?=$datos_enviar?>&estado_sal=3&estado_sal_max=3&krd=<?=$krd?>&nomcarpeta=Radicados Para Envio' class='vinculos'>
						Normal
					</a>
				</td>
				<td class='listado2'>
					<a href='../envios2/certimail.php?<?=$datos_enviar?>&estado_sal=3&estado_sal_max=3&krd=<?=$krd?>&nomcarpeta=Radicados Para Envio' class='vinculos'>
						Certimail
					</a>
				</td>
				<td class='listado2'>
					<a href='../envios/cuerpoModifEnvio.php?<?=$datos_enviar?>&estado_sal=4&estado_sal_max=4&devolucion=3&krd=<?=$krd?>' class='vinculos'>
						Modificacion Registro de Envio
					</a>
				</td>
				<td class='listado2'>
					<a href='../envios/cuerpoEnviofax.php?krd=<?=$krd?>' class='vinculos'>
						Fax
					</a>
				</td>
				<td class='listado2'>
					<a href='../radsalida/cuerpo_masiva.php?<?=$datos_enviar?>&krd=<?=$krd?>&estado_sal=3&estado_sal_max=3' class='vinculos'>
						Masiva
					</a>
				</td>
				<td class='listado2'>
					<a href='../radsalida/generar_envio.php?<?=$datos_enviar?>&krd=<?=$krd?>' class='vinculos'>
						Generacion de Planillas y Guias 
					</a>
				</td>
				<td class='listado2'>
					<a href='../radsalida/cargar_envio.php?<?=$datos_enviar?>&krd=<?=$krd?>' class='vinculos'>
						Cargar No. Guias de envio
					</a>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="100%" border="0" cellspacing="5" class="borde_tab">
			<tr class='titulos2'>
				<td>
					<img src='../imagenes/devoluciones.gif'> Devoluciones
				</td>
      </tr>
		</table>
		<table width="100%" border="0" cellspacing="5" class="borde_tab">
			<tr>
				<td class='listado2'>
					<a href='../devolucion/dev_corresp.php?<?=$datos_enviar?>&estado_sal=4&estado_sal_max=4&krd=<?=$krd?>' class='vinculos'>
						Por exceder  tiempo de espera
					</a>
				</td>
				<td class='listado2'>
					<a href='../devolucion/cuerpoDevOtras.php?<?=$datos_enviar?>&estado_sal=4&estado_sal_max=4&devolucion=1&krd=<?=$krd?>' class='vinculos'>
						Otras Devoluciones
					</a>
				</td>
			</tr>
		</table>
		<p></p>
		<table width="100%" border="0" cellspacing="5" class="borde_tab">
			<tr class='titulos2'>
				<td>
					<img src='../iconos/anulacionRad.gif'> Anulaciones
				</td>
			</tr>
			<tr>
				<td class='listado2'>
					<a href='../anulacion/anularRadicados.php?<?=$datos_enviar?>&estado_sal=4&tpAnulacion=2&krd=<?=$krd?>' class="vinculos">
						Anular Radicados
					</a>
				</td>
			</tr>	
		</table>
		<p></p>
		<table width="100%" border="0" cellspacing="5" class="borde_tab">
			<tr class='titulos2'>
				<td colspan="3">
					<img src='../imagenes/estadisticas_icono.gif'> Reportes 
				</td>
			</tr>
			<tr>
				<td class='listado2'>
					<a href='../reportes/generar_estadisticas_envio.php?<?=$datos_enviar?>&estado_sal=4&estado_sal_max=4&krd=<?=$krd?>' class='vinculos'>
						Envio de Correo
					</a>
				</td>
				<td class='listado2'>
					<a href='../reportes/generar_estadisticas.php?<?=$datos_enviar?>&estado_sal=4&estado_sal_max=4&krd=<?=$krd?>' class='vinculos'>
						Devoluciones
					</a>
				</td>
				<td class='listado2'>
					<a href='../anulacion/cuerpo_RepAnula.php?<?=$datos_enviar?>&estado_sal=4&tpAnulacion=2&krd=<?=$krd?>' class='vinculos'>
						Anulaciones
					</a>
				</td>
			</tr>
		</table>
	</body>
</html>
