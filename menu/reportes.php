<table border="0" cellpadding="0" cellspacing="0" width="160">
<tr>
	<td><img src="imagenes/spacer.gif" width="10" height="1" border="0" alt=""></td>
	<td><img src="imagenes/spacer.gif" width="150" height="1" border="0" alt=""></td>
	<td><img src="imagenes/spacer.gif" width="1" height="1" border="0" alt=""></td>
</tr>
<tr>
	<td colspan="2"><img name="menu_r3_c1" src="imagenes/menu_r3_c2.gif" width="148" height="31" border="0" alt=""></td>
	<td><img src="imagenes/spacer.gif" width="1" height="25" border="0" alt=""></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td valign="top">
		<table width="150" border="0" cellpadding="0" cellspacing="0" bgcolor="c0ccca">
<?php
if ($_SESSION["dependencia"]==900 || $_SESSION["dependencia"]==261 || $_SESSION["dependencia"]==265 || $_SESSION["dependencia"]==262 || $_SESSION["dependencia"]==264 || $_SESSION["dependencia"]==260 || $_SESSION["dependencia"]==120 || $_SESSION["dependencia"]==265 || $_SESSION["dependencia"]==262 || $_SESSION["dependencia"]==230) {
?>
		<tr>
			<td valign="top">
				<table width="150"  border="0" cellpadding="0" cellspacing="3" bgcolor="#C0CCCA">
				<tr valign="middle">
					<td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td>
					<td width="125">
						<a onclick="cambioMenu(<?=$i?>);" href='reportes/procesos.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=2&depende=$dependencia"; ?>' alt='Reporte Procesos' target='mainFrame' class="menu_princ">Reporte Procesos</a>
					</td>
				</tr>
				</table>
			</td>
		</tr>
<?php
	$i++;
}
if($_SESSION["dependencia"]==600 || $_SESSION["dependencia"]==900 || $_SESSION["dependencia"]==260 || $_SESSION["dependencia"]==120) {
?>
		<!--
		<tr valign="middle">
			<td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td>
			<td width="125">
				<a  onclick="cambioMenu(<?=$i?>);" href='reportes/temas.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=2&depende=$dependencia"; ?>' alt='Reporte Por Tema' target='mainFrame' class="menu_princ">Reporte Por Tema</a>
			</td>
		</tr>	
		-->
		<tr>
			<td valign="top">
				<table width="150"  border="0" cellpadding="0" cellspacing="3" bgcolor="#C0CCCA">
				<tr valign="middle">
					<td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td>
					<td width="125">
						<a onclick="cambioMenu(<?=$i?>);" href='reportes/estadisticaspqr.php?<?=$phpsession?>' alt='Reporte Internet' target='mainFrame' class="menu_princ">Reporte Internet</a>
					</td>
				</tr>
				</table>
			</td>
		</tr>
<?php
	$i++;
}
?>
		</table>
	</td>
</tr>
</table>