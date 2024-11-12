<?php
session_start();
if (!$ruta_raiz) {
	$ruta_raiz= "..";
}

$isqlC ='SELECT	RADI_RESPUESTA  AS "RESPUESTA"
		 FROM	RADICADO
		 WHERE	RADI_NUME_RADI = '. $verrad;
error_reporting(7);
?>
    <br>
	<br>
	<table class="borde_tab">
		<tr>
			<td>
				RESPUESTA VINCULADA AL RADICADO No. <?=$verrad ?>
			</td>
		</tr>
	</table>
	<br>
	<table class=borde_tab width="100%" cellpadding="0" cellspacing="5">
		<tr class="titulo4" align="center">
			<td width="30%"  class="titulos4">RESPUESTA VINCULADA</td>
			<td width="30%"  class="titulos4">ACCI&Oacute;N</td>
  		</tr>
		<tr>
<?php
$rsC=$db->conn->Execute($isqlC);
$numRadiVin  =$rsC->fields["RESPUESTA"];
if($numRadiVin  != '') {
	$numRadiVin  =$rsC->fields["RESPUESTA"];
?> 
				
			<td class="listado4" align="center"> <?=$numRadiVin?> </td>
	 		<td  <?php if (!$rsC->fields["RESPUESTA"]) echo " class='celdaGris ' "; else echo " class='e_tablas ' "; ?>  >
<?php 
	echo "<a href=javascript:borrarArchivo('$verrad','si')><span class='botones_largo'>Borrar Respuesta</a> ";
?> 
		 
			</td>
		</tr>
<?php
}
?>
	</table>