<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

include ("../../config.php");
$dep_sel = '';
$krdOld = $krd;

$ruta_raiz = "../..";
$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
if (!$tipoCarpOld)
	$tipoCarpOld = $tipo_carpt;
if (!$krd)
	$krd = $krdOld;
if (!isset($_SESSION['dependencia']))
	include (ORFEOPATH . "rec_session.php");
$errorValida = "";
include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
$db = new ConnectionHandler(ORFEOPATH);
//$db->conn->debug = true;
if (!defined('ADODB_FETCH_ASSOC'))
	define('ADODB_FETCH_ASSOC', 2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$isql = "SELECT  DEPE_NOMB,
					DEPE_CODI
			FROM	DEPENDENCIA 
			ORDER BY DEPE_NOMB";
$rs_dep = $db->conn->Execute($isql);
$dependencias = $_POST['dependencias'];
$htmlDependencias = '';

foreach ($dependencias as $depeRecorrer) {
	if (!empty($depeRecorrer['default'])) {
		$dep_sel = $depeRecorrer['default'];
		$htmlDependencias .= '<input type="hidden" name="dependencias[][' . $depeRecorrer['default'] . ']"';
		$htmlDependencias .= ' value="1">' . "\n";
	} else {
		$htmlDependencias .= '<input type="hidden" name="dependencias[][' . $depeRecorrer['check'] . ']"';
		$htmlDependencias .= ' value="0">' . "\n";
	}
}
// No selecciono ninguna dependencia por defecto
if (empty($dep_sel)) {
	var_dump("Error es no ha asignado dependencia por defecto");
	exit();
}
// Creamos la variable $arrdepsel que contienen las dependencias
// que pueden ver la dependencia del usuario actual.
$isql = "SELECT DEPENDENCIA_OBSERVA
             FROM   DEPENDENCIA_VISIBILIDAD
             WHERE  DEPENDENCIA_VISIBLE = $dep_sel";
//$db->conn->debug = true;
$rs_depvis = $db->conn->Execute($isql);
$arrDepSel = array();
$i = 0;
while ($tmp = $rs_depvis->FetchRow()) {
	$arrDepSel[$i] = $tmp['DEPENDENCIA_OBSERVA'];
	$i += 1;
}
$tPermis = ($usModo == 1) ? "Asignar Permisos" : "Editar Permisos";
?>
<html>
	<head>
		<script language="Javascript">
			function mensaje(vari) {
				alert("evento lanzado: " + vari);
			}
        
			function usuarioVacas(seleccionInactivo){  
				if(seleccionInactivo==0){
					document.getElementById('usuarioEncargado').value = 0;
					document.getElementById('usuarioEncargado').style.display = "none";
				}
				else{
					document.getElementById('usuarioEncargado').value = 1;
					document.getElementById('usuarioEncargado').style.display = "";
				}
			}
    
			function grabarUs(){
<?php
if ($perfil == "Jefe") {
	?>
				if(document.getElementById('usua_activoE').checked==true && document.getElementById('usuarioEncargado').value==0){
					alert("Debe seleccionar un usuario Activo");
				}
				else{
					document.frmPermisos.submit();
				}
	<?php
} else {
	?>
				document.frmPermisos.submit();
	<?php
}
?>
	}
		</script>
		<title>Creacion de Usuario</title>
		<link rel="stylesheet" href="../../estilos/orfeo.css">
	</head>
	<body>
		<?php
		/** Valida que la dependencia no tenga ya JEFE * */
		$isql = "SELECT	USUA_NOMB, 
					USUA_LOGIN
			FROM	USUARIO
			WHERE	DEPE_CODI = $dep_sel AND
					USUA_CODI = 1";
		$rs = $db->conn->Execute($isql);
		$nombreJefe = $rs->fields["USUA_NOMB"];
		if ($nombreJefe && $perfil == "Jefe") {
			if ($usuLogin != $rs->fields["USUA_LOGIN"]) {
				$errorValida = "SI";
				?>
			<center> <p> <span class=etexto> <B>
							<?= "En la dependencia " . $dep_sel . ", ya existe un usuario jefe, 
				" . $nombreJefe . ", por favor verifique o realice los cambios
				necesarios para poder continuar con este proceso" ?> </B>
				</p> </center>
			<?php
		}
	}

	/** Valida que la cedula NO EXISTA ya en la base de usuario * */
	if (($usuDocSel != $cedula && $usModo == 2) || $usModo == 1) {
		$isql = "SELECT USUA_DOC FROM USUARIO WHERE USUA_DOC = " . "'" . $cedula . "'";
		$rsCedula = $db->conn->Execute($isql);
		$cedulaEncon = $rsCedula->fields["USUA_DOC"];
		if ($cedulaEncon) {
			//$errorValida = "SI";
			?> 

			<center> <p> <span class="etexto"> <b>
							El numero de cedula ya existe en la tabla de usuario, por favor verifique
						</b> </span> </p> </center>
			<?php
		}
	}

// Valida que el LOGIN NO EXISTA ya en la base de usuario
	if ($usuLoginSel != $usuLogin && $usModo == 1) {
		$isql = "SELECT	USUA_LOGIN
				FROM	USUARIO
				WHERE	USUA_LOGIN = " . "'" . $usuLogin . "'";
		$rsLogin = $db->conn->Execute($isql);
		$LoginEncon = $rsLogin->fields["USUA_LOGIN"];
		if ($LoginEncon) {
			$errorValida = "SI";
			?>

			<center> <p> <span class=etexto> <b>
							El Login que desea asignar ya existe, por favor verifique.
						</b> </span> </p> </center>
			<?php
		}
	}
	$encabezado = "krd=" . $krd . "&usModo=" . $usModo;
	$encabezado .= "&perfil=$perfil&dep_sel=$dep_sel&cedula=$cedula";
	$encabezado .= "&usuLogin=$usuLogin&nombre=$nombre&dia=$dia&mes=$mes";
	$encabezado .= "&ano=$ano&ubicacion=$ubicacion&piso=$piso&extension=$extension";
	$encabezado .= "&email=$email&email1=$email1&email2=$email2&usuDocSel=$usuDocSel&usuLoginSel=$usuLoginSel";

	if ($errorValida == "SI") {
		?>

		<span class=etexto> <center> 
				<a href='crear.php? <?= session_name() . "=" . session_id() . "&$encabezado" ?>'>Volver a Formulario Anterior</a>
			</center> </span>
		<?php
	} else {
		$encabezado = "krd=$krd&usModo=$usModo&perfil=$perfil";
		$encabezado .= "&perfilOrig=$perfilOrig&dep_sel=$dep_sel&cedula=$cedula";
		$encabezado .= "&usuLogin=$usuLogin&nombre=$nombre&dia=$dia&mes=$mes";
		$encabezado .= "&ano=$ano&ubicacion=$ubicacion&piso=$piso&extension=$extension";
		$encabezado .= "&email=$email&email1=$email1&email2=$email2&usuDocSel=$usuDocSel&usuLoginSel=$usuLoginSel";
		?>

		<center>
			<form name="frmPermisos" action='./grabar.php?<?= session_name() . "=" . session_id() . "&$encabezado" ?>' method="POST">
				<table border=1 width=80% class=t_bordeGris>
					<tr> 
						<td colspan="2" class="titulos4"> 
					<center> <p> <B> <span class=etexto> 
									ADMINISTRACI&Oacute;N DE USUARIOS Y PERFILES
								</span> </B> </p> 
						<?php
						echo $tPermis;
						?>
					</center>
					</td>
					</tr>
					<?php
					if ($usModo == 2) {
						include ("./traePermisos.php");
					} else {
						$usua_activo = 1;
						$usua_nuevoM = 1;
						$usua_Archivar = 1;
						$autenticaLDAP = 1;
						$perm_servweb = 0;
					}
					?>
					<tr>
						<td width="40%" height="26" class="listado2"> <input type="checkbox" name="digitaliza" value="$digitaliza" <?php if ($digitaliza) echo "checked"; else echo ""; ?>>
							Digitalizaci&oacute;n de Documentos</td>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="tablas" value="$tablas" <?php if ($tablas) echo "checked"; else echo ""; ?>>
							Tablas de Retenci&oacute;n Documental</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2">
							<input name="modificaciones" type="checkbox" value="$modificaciones" <?php if ($modificaciones) echo "checked"; else echo ""; ?>>
							Modificaciones
						</td>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="masiva" value="$masiva" <?php if ($masiva) echo "checked"; else echo ""; ?>>
							Radicaci&oacute;n Masiva
						</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2">
							<table width='100%'>
								<tr>
									<td class="titulos4">Impresi&oacute;n</td> 
								</tr>
								<?php
								echo $htmlDependencias;
								$contador = 0;
								$permImpresion = array();
								$permImpresion[] = 'Sin Permisos';
								$permImpresion[] = 'Mostrar radicados de la dependencia';
								$permImpresion[] = 'Todos los privilegios';
								while ($contador <= 2) {
									echo "<tr>\n";
									echo "<td class='listado2'><input name='impresion' type='radio' value='$contador'";
									if ($impresion == $contador)
										echo "checked";
									else
										echo "";
									echo " >" . $permImpresion[$contador] . "</td>\n";
									echo "</tr>";
									$contador = $contador + 1;
								}
								?>
							</table>
						</td>
						<td width="40%" height="26" class="listado2">
							<table width='100%'>
								<tr> <td class="titulos4">Administrador de Archivo.</td> </tr>
								<?php
								$contador = 0;
								$permAdmFiles = array();
								$permAdmFiles[] = 'Sin permisos';
								$permAdmFiles[] = 'Permiso de consultas';
								$permAdmFiles[] = 'Permiso de Administrador';
								while ($contador <= 2) {
									echo "<tr>";
									echo "<td class='listado2'><input name='adm_archivo' type='radio' value=$contador ";
									if ($adm_archivo == $contador)
										echo "checked"; else
										echo "";
									echo " >" . $permAdmFiles[$contador] . "</td>\n";
									echo "</tr>";
									$contador = $contador + 1;
								}
								?>
							</table>
						</td>
					</tr>
					<!-- Inicio de administracion de Temas para expedientes -->
					<tr>
						<td width="40%" height="26" class="listado2">
							<table width='100%'>
								<tr> <td class="titulos4">Administraci&oacute;n de Expedientes</td> </tr>
								<?php
								echo $htmlDependencias;
								$contador = 0;
								$permTemasExp = array();
								$permTemasExp[] = 'Sin Permisos';
								$permTemasExp[] = 'Solo la Dependencia';
								$permTemasExp[] = 'Dep. Padre';
								$permTemasExp[] = 'Todas las dep.';
								while ($contador <= 3) {
									echo "<tr>\n";
									echo "<td class='listado2'><input name='exp_temas' type='radio' value='$contador'";
									if ($exp_temas == $contador)
										echo "checked";
									else
										echo "";
									echo " >" . $permTemasExp[$contador] . "</td>\n";
									echo "</tr>";
									$contador = $contador + 1;
								}
								?>
							</table>
						</td>
						<!-- Inicio Acciones masivas-->
						<td height="26" class="listado2" >
							<table width='100%' valign='top'>
								<tr>
									<td class="titulos4"> 	Acciones Masivas </td>
								</tr>
								<tr> 
									<td class='listado2' valign='top'>
										<input type="checkbox" name="accMasiva_trd" value="$accMasiva_trd" 
											<?php if ($accMasiva_trd) echo "checked"; else echo ""; ?>>
										Masiva TRD*** </td>
								</tr>
								<tr> 
									<td class='listado2' valign='top'>
										<input type="checkbox" name="accMasiva_incluir" value="$accMasiva_incluir" 
											<?php if ($accMasiva_incluir) echo "checked"; else echo ""; ?>>
										Masiva Incluir	
									</td>
								</tr>
								<tr> 
									<td class='listado2' valign='top'>
										<input type="checkbox" name="accMasiva_prestamo" value="$accMasiva_prestamo" 
											<?php if ($accMasiva_prestamo) echo "checked"; else echo ""; ?>>
										Masiva Prestamo
									</td>
								</tr>
								<tr> <td class='listado2' valign='top'>
										<input type="checkbox" name="accMasiva_temas" value="$accMasiva_temas" 
											<?php if ($accMasiva_temas) echo "checked"; else echo ""; ?>>
										Masiva Temas<br/> 
									</td>				
								</tr>
							</table>		
						</td>
						<!-- FIN Acciones masivas-->
					</tr>
					<!-- Fin de administracion de Temas para expedientes -->
					<tr>
						<td width="40%" height="26" class="listado2">
							<table width='100%'>
								<tr> <td class="titulos4">Estad&iacute;sticas.</td> </tr>
								<?php
								$contador = 0;
								$permEsta = array();
								$permEsta[] = 'Ver estad&iacute;sticas del usuario';
								$permEsta[] = 'Ver estad&iacute;sticas de la dependencia';
								$permEsta[] = 'Ver estad&iacute;sticas todas las dependencias';
								while ($contador <= 2) {
									echo "<tr>";
									echo "<td class='listado2'><input name='estadisticas' type='radio' value=$contador ";
									if ($estadisticas == $contador)
										echo "checked"; else
										echo "";
									echo " >" . $permEsta[$contador] . "</td>\n";
									echo "</tr>\n";
									$contador = $contador + 1;
								}
								?>
							</table>
						</td>
						<td width="40%" height="26" class="listado2">
							<table width='100%'>
								<tr> 
									<td class="titulos4">Creaci&oacute;n de expedientes.
									</td> 
								</tr>
								<?php
								$contador = 0;
								$permExpe = array();
								$permExpe[] = 'Sin permisos de creacion de expedientes';
								$permExpe[] = 'Permiso para creaci&oacute;n';
								$permExpe[] = 'Maximo permiso para creaci&oacute;n';
								while ($contador <= 2) {
									echo "<tr>";
									echo "<td class='listado2'><input name='usua_permexp' type='radio' value=$contador ";
									if ($usua_permexp == $contador)
										echo "checked"; else
										echo "";
									echo " >" . $permExpe[$contador] . "</td>\n";
									echo "</tr>\n";
									$contador = $contador + 1;
								}
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="prestamo" value="$prestamo" <?php if ($prestamo) echo "checked"; else echo ""; ?>>
							Prestamo de Documentos. 
						</td>
						<td width="40%" height="26" class="listado2"> <input type="checkbox" name="dev_correo" value="$dev_correo" <?php if ($dev_correo) echo "checked"; else echo ""; ?>>
							Devoluciones de Correo.
						</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="adm_sistema" value="$adm_sistema" <?php if ($adm_sistema) echo "checked"; else echo ""; ?>>
							Administrador del Sistema.
						</td>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="env_correo" value="$env_correo" <?php if ($env_correo) echo "checked"; else echo ""; ?>>
							Envios de Correo.
						</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="reasigna" value="$reasigna" <?php if ($reasigna) echo "checked"; else echo ""; ?>>
							Usuario Reasigna.
						</td>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="s_anulaciones" value="$s_anulaciones" <?php if ($s_anulaciones) echo "checked"; else echo ""; ?>>
							Solicitud de Anulaciones.
						</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2" rowspan=3>
							<input type="radio" name="usua_activo" value=1 <?php if ($usua_activo == 1) echo "checked"; else echo ""; ?> onClick="usuarioVacas(0);">
							Usuario Activo. <?= $usuaRolCodigo ?><br>
							<?php
							if ($usuaRolCodigo != 2) {
								?>
								<input type="radio" name="usua_activo" value=0 <?php if ($usua_activo == 0) echo "checked"; else echo ""; ?> onClick="usuarioVacas(0);">
								Usuario Inactivo Definitivo.<br>
								<input type="radio" name="usua_activo" id="usua_activoE"
									   value=2 <?php if ($usua_activo == 2) echo "checked"; else echo ""; ?>
									   onClick="usuarioVacas(1);">
								Usuario Inactivo por Vacaciones o Licencia.
								<?php
								$query ="SELECT	u.USUA_NOMB,
												u.USUA_LOGIN,
												u.USUA_CODI,
												u.SGD_ROL_CODIGO
										FROM	USUARIO u
										WHERE	u.DEPE_CODI=$dep_sel 
												and u.USUA_ESTA=1
												and u.USUA_CODI<>1
										ORDER BY u.sgd_rol_codigo desc, u.usua_NOMB ";
								//$db->conn->debug = true;
								$rs = $db->conn->Execute($query);
								$codigoRol = $rs->fields["SGD_ROL_CODIGO"];
								if ($codigoRol == 2)
									$usuarioEncargado = $rs->fields["USUA_LOGIN"];
								if ($perfil == "Jefe") {
									print $rs->GetMenu2("usuarioEncargado", $usuarioEncargado, "0: ---- Seleccione un Usuario ----", false, false, " id=usuarioEncargado class='select'");
								} else {
									?>
									<br>
									<select name=usuarioEncargado id=usuarioEncargado class=select> </select>
									<?php
								}
								if ($usua_activo <= 1) {
									?>
									<SCRIPT> usuarioVacas(0); </SCRIPT>
									<?php
								}
								?>
								<?php
							} else {
								echo "<font color=red>Este Usuario No se Puede inactivar ya
										que el Usuario Actual esta encargado del Area.</Font><br>
										Debe seleccionar otro usuario Encargado, 
										editando el Jefe Actual de la Dependencia.<br>";
							}
							?>
						</td>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="anulaciones" value="$anulaciones" <?php if ($anulaciones) echo "checked"; else echo ""; ?>>
							Anulaciones.
						</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2"> <input type="checkbox" name="usua_nuevoM" value="$usua_nuevoM" <?php if ($usua_nuevoM == '0') echo "checked"; else echo ""; ?>>
							Usuario Nuevo.
						</td>
					</tr>
					<tr>
						<td width="40%" height="26" class="listado2">Firma Digital.
							<?php
							$contador = 0;
							while ($contador <= 3) {
								echo "<input name='firma' type='radio' value=$contador ";
								if ($firma == $contador)
									echo "checked"; else
									echo "";
								echo " >" . $contador;
								$contador = $contador + 1;
							}
							?>
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="permArchivar" value="$permArchivar" <?php if ($permArchivar || $usua_Archivar == 1) echo "checked"; else echo ""; ?>>
							Puede Archivar Documentos
						</td>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="notifica" value="$notifica" <?php if ($notifica) echo "checked"; else echo ""; ?>>
							Notificaci&oacute;n de Resoluciones.
						</td>
					</tr>
					<td width="40%" height="26" class="listado2">Nivel de Seguridad.
						<?php
						$contador = 1;
						while ($contador <= 5) {
							echo "<input name='nivel' type='radio' value=$contador ";
							if ($nivel == $contador)
								echo "checked"; else
								echo "";
							echo " >" . $contador;
							$contador = $contador + 1;
						}
						?>
					</td>
					<td width="39%" height="26" class="listado2" colspan="2">
						<input type="checkbox" name="usua_publico" value="$usua_publico" <?php if ($usua_publico) echo "checked"; else echo ""; ?> >
						Usuario P&uacute;blico.
					</td>
					<tr>
				</table>
				<table border=1 width=80% class=t_bordeGris>
					<tr>
						<td colspan="2" class="titulos4" align="center">
							<p> <b> <span class="etexto">Permisos Tipos de Radicados</span> </b> </p>
						</td>
					</tr>
					<?php
					$sql = "SELECT SGD_TRAD_CODIGO, SGD_TRAD_DESCR
					FROM SGD_TRAD_TIPORAD
					ORDER BY SGD_TRAD_CODIGO";
					$permRadicados = array();
					$permRadicados[] = 'Sin permisos';
					$permRadicados[] = 'Permiso para radicaci&oacute;n menu principal';
					$permRadicados[] = 'Permiso para radicaci&oacute;n al anexar';
					$permRadicados[] = 'Todos los permisos';
					$ADODB_COUNTRECS = true;
					$nombreRadicado = '';
					$codigoRadicado = 0;
					$rs_trad = $db->conn->Execute($sql);
					if ($rs_trad->RecordCount() >= 0) {
						$i = 1;
						$cad = "perm_tp";
						while ($arr = $rs_trad->FetchRow()) {
							$nombreRadicado = $arr['SGD_TRAD_DESCR'];
							$codigoRadicado = $arr['SGD_TRAD_CODIGO'];
							if ($codigoRadicado == 9) {
								$colSpan = 'colspan="2"';
							}
							(is_int($i / 2)) ? print ""  : print "<tr align='left'>\n";
							echo "<td $colSpan height='26' width='40%' class='listado2'>\n";
							echo "<table width='100%'>\n";
							$x = 0;
							echo "<tr><td class='titulos4'>($codigoRadicado). $nombreRadicado</td></tr>\n";
							//echo "&nbsp;" . "(".$arr['SGD_TRAD_CODIGO'].")&nbsp;".$arr['SGD_TRAD_DESCR']."&nbsp;&nbsp;";
							while ($x < 4) {
								$chk = ($x == ${$cad . $arr['SGD_TRAD_CODIGO']}) ? "checked" : "";
								echo "</tr><td class='listado2'><input type='radio' name='" . $cad . $arr['SGD_TRAD_CODIGO'] .
								"' id='" . $cad . $arr['SGD_TRAD_CODIGO'] . "' value='$x' $chk>" . $permRadicados[$x] .
								"</td></tr>\n";
								$x++;
							}
							echo "</table>";
							echo "</td>\n";

							(is_int($i / 2)) ? print "</tr>"  : print "";
							$i += 1;
						}
					}
					else
						echo "<tr><td align='center'> NO SE HAN GESTIONADO TIPOS DE RADICADOS</td></tr>";
					$ADODB_COUNTRECS = false;
					?>
				</table>
				<table border=1 width=80% class=t_bordeGris>
					<tr>
						<td colspan="2" class="titulos4" align="center">
							<p><B><span class=etexto>Otros Permisos Especiales</span></B></p>
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="permBorraAnexos" value="$permBorraAnexos" <?php if ($permBorraAnexos) echo "checked"; else echo ""; ?>>
							Borra Anexos (tif/pdf).
						</td>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="permTipificaAnexos" value="$permTipificaAnexos" <?php if ($permTipificaAnexos) echo "checked"; else echo ""; ?>>
							Tipificar Anexos (tif/pdf).
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="autenticaLDAP" value="$autenticaLDAP" <?php if ($autenticaLDAP || $autenticaLDAP == 1) echo "checked"; else echo ""; ?>>
							Se autentica por medio de LDAP
						</td>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="perm_adminflujos" value="$perm_adminflujos" <?php if ($perm_adminflujos) echo "checked"; else echo ""; ?>>
							Utiliza el editor de Flujos
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="alertaDP" value="$alertaDP" <?php if ($alertaDP) echo "checked"; else echo ""; ?>>
							Carpeta Control de Documentos
						</td>
						<td width="40%" height="26" class="listado2">
							<input type="checkbox" name="temas" value="$temas" <?php if ($temas) echo "checked"; else echo ""; ?>>
							Temas de Seguimiento.
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="respuesta" value="$respuesta" <?php if ($respuesta) echo "checked"; else echo ""; ?>>
							Envia Respuesta Rapida
						</td>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="coinfo" value="$coinfo" <?php if ($coinfo) echo "checked"; else echo ""; ?>>
							Alerta de Documentos COINFO
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="medios" value="$medios" <?php if ($medios) echo "checked"; else echo ""; ?>>
							Cambia Medios de Recepcion
						</td>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="ccalarmas" value="$ccalarmas" <?php if ($ccalarmas) echo "checked"; else echo ""; ?>>
							Copia de alertas diarias de PQR's
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="permRadMail" <?php if ($permRadMail == 1) echo "checked"; else echo ""; ?>>
							<span class="etexto">Radicacion de e-mail (<?= $email1 ?>)</span>
						</td>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="permDespla" value=1 <?php if ($permDespla == 1) echo "checked"; else echo ""; ?>>
							<span class="etexto">Desplazado Estado</span>
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="no_trd" <?php if ($no_trd == True) echo "checked"; else echo ""; ?>>
							<span class="etexto">Usuario No Tipifica</span>
						</td>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="ordena" <?php if ($ordena == True) echo "checked"; else echo ""; ?>>
							<span class="etexto">Ordenar anexos</span>
						</td>
					</tr>
					<tr>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="perm_servweb" <?php if ($perm_servweb != True && $perm_servweb == 0) echo ""; else echo "checked"; ?>>
							<span class="etexto">Administrador Servicios Web</span>
						</td>
						<td height='26' width='40%' class='listado2'>
							<input type="checkbox" name="notifAdm" <?php if ($notifAdm == True) echo "checked"; else echo ""; ?>>
							<span class="etexto">Notificaciones administrativas</span>
						</td>
					</tr>
				</table>
				<table border="1" width="80%" class="t_bordeGris">
					<tr>
						<td height="30" colspan="2" class="listado2">
							<input name="login" type="hidden" value='<?= $usuLogin ?>'>
							<input name="PHPSESSID" type="hidden" value='<?= session_id() ?>'>
							<input name="krd" type="hidden" value='<?= $krd ?>'>
							<input name="nusua_codi" type="hidden" value='<?= $nusua_codi ?>'>
							<input name="cedula" type="hidden" value='<?= $cedula ?>'>
					<center>
						<input class="botones" type="button" name="Submit3" value="Grabar" onClick=grabarUs();></center>
					</td>
					<td height="30" colspan="2" class="listado2">
					<center> <a href='../formAdministracion.php?<?= session_name() . "=" . session_id() . "&$encabezado" ?>'>
							<input class="botones" type="reset" name="Submit4" value="Cancelar"></a></center>
					</td>
					</tr>
				</table>
				</td>
				</tr>
				<?php
				$encabezado = "&krd=$krd&dep_sel=$dep_sel&usModo=$usModo&perfil=$perfil&cedula=$cedula&dia=$dia&mes=$mes&ano=$ano&ubicacion=$ubicacion&piso=$piso&extension=$extension&email=$email&email1=$email1&email2=$email2";
				?>
			</form>
			<?php
		}
		?>
	</body>
</html>
