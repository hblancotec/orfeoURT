<html>
	<head>
		<title> </title>
	</head> 
	<body>
		<table border="0" cellpadding="0" cellspacing="0" width="160">
			<tr>
				<td><img src="imagenes/spacer.gif" width="10" height="1" border="0" alt=""></td>
				<td><img src="imagenes/spacer.gif" width="150" height="1" border="0" alt=""></td>
				<td><img src="imagenes/spacer.gif" width="1" height="1" border="0" alt=""></td>
			</tr>
			<tr>
				<td colspan="2"><img name="menu_r1_c1" src="imagenes/menu_r1_c1.gif" width="160" height="25" border="0" alt=""></td>
				<td><img src="imagenes/spacer.gif" width="1" height="25" border="0" alt=""></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td valign="top">
					<table width="150"  border="0" cellpadding="0" cellspacing="0" bgcolor="c0ccca">
						<tr>
							<td valign="top">
								<table width="150"  border="0" cellpadding="0" cellspacing="3" bgcolor="#C0CCCA">

									<?php
									if($_SESSION["usua_admin_sistema"]==1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<a href="Administracion/formAdministracion.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=1"; ?>" target='mainFrame' class="menu_princ">Administracion</a>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["perm_servweb"]==1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<a href="Administracion/tbasicas/mnuAdmAppExt.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=1"; ?>" target='mainFrame' class="menu_princ">Administracion de Servicios Web</a>
										</td>
									</tr>
									<?php
									}
									if ($_SESSION["usua_perm_adminflujos"]==1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<a href="Administracion/flujos/texto_version2/mnuFlujosBasico.php?<?= $phpsession ?>&krd=<?= $krd ?>" class="menu_princ" target='mainFrame'>Editor Flujos</a>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_envios"] == 1 or $_SESSION["usua_perm_envios"] == 3) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<a href="radicacion/formRadEnvios.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=1"; ?>" target='mainFrame' class="menu_princ">Envios</a>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_envios"] == 2 or $_SESSION["usua_perm_envios"] == 3) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<a href="orfeo.api/Envio/index?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=1"; ?>" target='mainFrame' class="menu_princ">Envios Extjs</a>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_modifica"] >=1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="radicacion/edtradicado.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo '&fechah=$fechah&primera=1&ent=2'; ?>" target='mainFrame'  class="menu_princ">Modificaci&oacute;n</a></span>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_firma"]> 0) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="firma/index.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechaf=$fechah&carpeta=8&nomcarpeta=Documentos Para Firma Digital&orderTipo=desc&orderNo=3"; ?>" target='mainFrame' class="menu_princ">Firma Digital</a></span>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_intergapps"]==1 ) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="aplintegra/cuerpoApLIntegradas.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechaf=$fechah&carpeta=8&nomcarpeta=Aplicaciones integradas&orderTipo=desc&orderNo=3"; ?>" target='mainFrame' class="menu_princ">Aplicaciones integradas</a></span>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_impresion"] >= 1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="envios/cuerpoMarcaEnviar.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechaf=$fechah&usua_perm_impresion=".$_SESSION['usua_perm_impresion']."&carpeta=8&nomcarpeta=Documentos Para Impresion&orderTipo=desc&orderNo=3"; ?>" target='mainFrame' class="menu_princ">Impresi&oacute;n</a></span>
										</td>
									</tr>
									<?php
									}
									if ($_SESSION["usua_perm_anu"]==3 or $_SESSION["usua_perm_anu"]==1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="anulacion/cuerpo_anulacion.php?<?= $phpsession ?>&krd=<?= $krd ?>&krd=<?= $krd ?>&tpAnulacion=1&<?php echo "fechah=$fechah"; ?>" target='mainFrame' class="menu_princ">Anulaci&oacute;n</a></span>
										</td>
									</tr>
									<?php
									}
									if ($_SESSION["usua_perm_trd"]==1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="trd/menu_trd.php?<?= $phpsession ?>&krd=<?= $krd ?>&krd=<?= $krd ?>&<?php echo "fechah=$fechah"; ?>" target='mainFrame' class="menu_princ">Tablas Retenci&oacute;n Documental</a></span>
										</td>
									</tr>
									<?php
									}
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="busqueda/busquedaPiloto.php?<?= $phpsession ?>&etapa=1&&s_Listado=VerListado&krd=<?= $krd ?>&<?php echo "fechah=$fechah"; ?>" target='mainFrame' class="menu_princ">Consultas</a></span>
										</td>
									</tr>
									<?php
									/**
									 *  $usua_admin_archivo Viene del campo con el mismo nombre
									 *  en usuario y Establece permiso para ver informaci&oacute;n de
									 *  documentos que tienen que bicarse fisicamente en Archivo
									 *  (Por. Jh 20031101)
									 * */
									if($_SESSION["usua_admin_archivo"]>=1) {
										$isql ="select	count(*) as CONTADOR
												from	SGD_EXP_EXPEDIENTE
												where	sgd_exp_estado=0 ";
										$rs = $db->conn->Execute($isql);
										$num_exp = $rs->fields['CONTADOR'];
										
										### NUEVO ADMINISTRADOR DE ARCHIVO
										/*$isql2 ="SELECT	count(*) as CONTADOR
												FROM	SGD_EXP_EXPEDIENTE
												WHERE	SGD_EXP_ESTADO = 0 AND
														RADI_NUME_RADI > 20130000000000";
										$rs2 = $db->conn->Execute($isql2);
										$num_exp2 = $rs2->fields['CONTADOR'];
										*/
									?>
									<!--
									<tr>
										<td>
											<img src='imagenes/menu.gif' alt='Documentos para archivar' title='Documentos para archivar' border=0 align='absmiddle'>
										</td>
										<td>
											<span class="Estilo12">
												<a href='archivo/archivo.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?= "fechaf=$fechah&carpeta=8&nomcarpeta=Expedientes&orno=1&adodb_next_page=1"; ?>' target='mainFrame' class="menu_princ"><b>Archivo (<?= $num_exp ?>) </a>
											</span>
										</td>
									</tr>
									-->
									<!-- BOTÓ NUEVA OPCIÓ DEL MODULO DE ARCHIVO -->
									<tr>
										<td>
											<img src='imagenes/menu.gif' alt='Documentos para archivar' title='Documentos para archivar' border=0 align='absmiddle'>
										</td>
										<td>
											<span class="Estilo12"><a href='AdmArchivo/index.php?krd=<?= $krd ?>&<?= "fechaf=$fechah&carpeta=8&nomcarpeta=Expedientes&orno=1&adodb_next_page=1"; ?>' target='mainFrame' class="menu_princ"><b>Admin. de Archivo </a>
											</span>
										</td>
									</tr>
									<?php
									}
									if ($_SESSION["usua_perm_prestamo"]==1) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="prestamo/menu_prestamo.php?<?= $phpsession ?>&etapa=1&&s_Listado=VerListado&krd=<?= $krd ?>&<?php echo "fechah=$fechah"; ?>" target='mainFrame' class="menu_princ">Prestamo</a></span>
										</td>
									</tr>
									<?php
									}
									/**
									 *  $usua_perm_dev  Permiso de ver documentos de devolucion de documentos enviados.
									 *  (Por. Jh)
									 */
									if($_SESSION["usua_perm_dev"]==1) {
									?>
									<tr>
										<td>
											<img src='imagenes/menu.gif' alt='Documentos para archivar' title='Documentos para archivar' border=0 align='absmiddle'>
										</td>
										<TD>
											<span class="Estilo12">
												<a href='devolucion/cuerpoDevCorreo.php?<?= $phpsession ?>&krd=<?= $krd ?>&<?php echo "fechaf=$fechah&carpeta=8&devolucion=2&estado_sal=4&nomcarpeta=Documentos Para Impresion&orno=1&adodb_next_page=1"; ?>' target='mainFrame' class="menu_princ" >Dev Correo</span></a>
										</td>
									</tr>
									<?php
									}
									if ( $entidad == 'CRA' || $entidad == 'C.R.A' ) {
									?>
									<tr valign="middle">
										<td width="25"><img src="imagenes/menu.gif" width="15" height="18"></td>
										<td width="125">
											<span class="Estilo12"><a href="reportesCRA/menu.php?<?= $phpsession ?>&etapa=1&&s_Listado=VerListado&krd=<?= $krd ?>&<?php echo "fechah=$fechah"; ?>" target='mainFrame' class="menu_princ">Reportes CRA</a></span>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_tem_exp"] > 0) {
									?>
									<tr>
										<td>
											<img src='imagenes/menu.gif' alt='Administrar temas para Expedientes' title='Temas para Expedientes' border=0 align='absmiddle'>
										</td>
										<TD>
											<span class="Estilo12">
												<a href='expedientes/adm_Expedientes.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="menu_princ" >Expedientes</span></a>
										</td>
									</tr>
									<?php
									}
									if($_SESSION["usua_perm_notifAdmin"] == 1) {
									?>
									<tr>
										<td>
											<img src='imagenes/menu.gif' alt='Notificaciones Administrativas' title='Notificaciones Administrativas' border=0 align='absmiddle'>
										</td>
										<td>
											<span class="Estilo12">
												<a href='notificacionAdmin/index.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="menu_princ" >Notificaciones Admin.</span></a>
										</td>
									</tr>
									<?php
									}
	
									if($_SESSION["usua_admin_archivo"]>=1) {
									?>
									<tr>
										<td>
											<img src='imagenes/menu.gif' alt='Notificaciones Administrativas' title='Notificaciones Administrativas' border=0 align='absmiddle'>
										</td>
										<td>
											<span class="Estilo12">
												<a href='/orfeo.api/transferencia?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="menu_princ" >Transferencias Archivo.</span></a>
										</td>
									</tr>
									<?php
									}
									?>
                                                                        
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>