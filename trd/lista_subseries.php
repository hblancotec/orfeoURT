<?php
session_start();
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
$sqlFechaDocto = $db->conn->SQLDate("D-m-Y H:i:s A", "mf.sgd_rdf_fech");
$sqlSubstDescS = $db->conn->substr . "(SGD_SBRD_DESCRIP, 0, 40)";
$sqlFechaD = $db->conn->SQLDate("d-m-Y H:i A", "SGD_SBRD_FECHINI");
$sqlFechaH = $db->conn->SQLDate("d-m-Y H:i A", "SGD_SBRD_FECHFIN");
$isqlC = 'select 
			  SGD_SBRD_CODIGO          AS "CODIGO",
			' . $sqlSubstDescS . '    AS "SUBSERIE",
			' . $sqlFechaD . ' 			  as "DESDE",
			' . $sqlFechaH . ' 			  as "HASTA" 
			from 
				SGD_SBRD_SUBSERIERD
			where
			   SGD_SRD_CODIGO = ' . $codserie . $whereBusqueda . '
			order by  ' . $sqlSubstDescS;
error_reporting(7);
?>
</br>
</br>
<div style="margin: auto; justify-content: center; text-align: center; width: 100%; display: flex;">
	<table style="width:850;" class="borde_tab" cellspacing="5">
	<tr class=tpar>
		<td class=titulos3 align=center>CODIGO</td>
		<td class=titulos3 align=center>DESCRIPCION</td>
		<td class=titulos3 align=center>DESDE</td>
		<td class=titulos3 align=center>HASTA</td>
	</tr>
  	<?php
$rsC = $db->conn->Execute($isqlC);
while (! $rsC->EOF) {
    $tsub = $rsC->fields["CODIGO"];
    $dsubserie = $rsC->fields["SUBSERIE"];
    $fini = $rsC->fields["DESDE"];
    $ffin = $rsC->fields["HASTA"];
    ?> 
		      <tr class=paginacion>
		<td> <?=$tsub?> </td>
		<td align=left><?=$dsubserie?> </td>
		<td> <?=$fini?> </td>
		<td> <?=$ffin?></td>
	</tr>
	<?php
    $rsC->MoveNext();
}
?>
   </table>
</div>
   