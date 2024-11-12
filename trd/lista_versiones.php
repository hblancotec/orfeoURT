<?php
// session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_perm_trd'] != 1) {
    die(include "../sinpermiso.php");
    exit();
}

if (! $ruta_raiz)
    $ruta_raiz = "..";
/*
 * $sqlFechaDocto = $db->conn->SQLDate("D-m-Y H:i:s A","mf.sgd_rdf_fech");
 * $sqlSubstDescS = $db->conn->substr."(SGD_SRD_DESCRIP, 0, 40)";
 * $sqlFechaD = $db->conn->SQLDate("d-m-Y H:i A","SGD_SRD_FECHINI");
 * $sqlFechaH = $db->conn->SQLDate("d-m-Y H:i A","SGD_SRD_FECHFIN");
 */
$isqlC = 'select ID, VERSION, NOMBRE, OBSERVACION, FECHA_INICIO, FECHA_FIN
		  from VERSIONES '.$whereBusqueda.' order by 1';
?>

<br>
<div style="margin: auto; justify-content: center; text-align: center; width: 100%; display: flex;">
	<TABLE style="width:850px;" class="borde_tab" cellspacing="5">
		<tr class=tpar>
			<td class=titulos3 align=center>VERSI&Oacute;N</td>
			<td class=titulos3 align=center>NOMBRE</td>
			<td class=titulos3 align=center>OBSERVACI&Oacute;N</td>
			<td class=titulos3 align=center>DESDE</td>
			<td class=titulos3 align=center>HASTA</td>
		</tr>
  	<?php
$rsC = $db->conn->Execute($isqlC);
while ($rsC && ! $rsC->EOF) {
    $version = $rsC->fields["VERSION"];
    $nombre = $rsC->fields["NOMBRE"];
    $descripcion = $rsC->fields["OBSERVACION"];
    $fini = $rsC->fields["FECHA_INICIO"];
    $ffin = $rsC->fields["FECHA_FIN"];
    
    $date1 = date_create($fini);
    $date2 = date_create($ffin);
    
    ?> 
    		  <tr class=paginacion>
			<td> <?=$version?></td>
			<td align=left> <?=$nombre?> </td>
			<td align=left> <?=$descripcion?> </td>
			<td> <?=date_format($date1,"d/m/Y")?> </td>
			<td> <?=date_format($date2,"d/m/Y")?> </td>
		</tr>
	<?php
    $rsC->MoveNext();
}
// <font face="Arial, Helvetica, sans-serif" class="etextomenu">
?>
   </table>
</div>