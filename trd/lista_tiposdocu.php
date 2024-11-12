<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_perm_trd'] != 1){
	die(include "../sinpermiso.php");
	exit;
}

if (!$ruta_raiz) $ruta_raiz= "..";
$sqlFechaDocto =  $db->conn->SQLDate("Y-m-D H:i:s A","mf.sgd_rdf_fech");
$sqlSubstDescS =  $db->conn->substr."(SGD_TPR_DESCRIP, 0, 75)";
$isqlC = 'select
			  SGD_TPR_CODIGO          AS "CODIGO",
			'. $sqlSubstDescS .  '    AS "TIPOD",
			  SGD_TPR_TERMINO		  as "TERMINO",
			  SGD_TPR_TP1   		  as "SALIDA",
			  SGD_TPR_TP2   		  as "ENTRADA",
			  SGD_TPR_TP3   		  as "MEMORANDO",
			  SGD_TPR_TP4   		  as "CIRCULARES EX/AC",
			  SGD_TPR_TP5   		  as "RESOLUCION",
			  SGD_TPR_TP6			  as "CONCEPTOS",
			  SGD_TPR_TP7   		  as "EDICTOS",
			  SGD_TPR_TP8			  as "CIRCULARES",
			  SGD_TPR_TP9   		  as "AUTOS/ACTOS" ,
			  SGD_TPR_ESTADO 	          as "ESTADO",
			  SGD_TPR_ALERTA 	          as "ALERTA",
			  SGD_TPR_NOTIFICA 	          as "NOTIFICA"
			from
				SGD_TPR_TPDCUMENTO
				'.$whereBusqueda.'
			order by  '. $sqlSubstDescS;
     error_reporting(7);
?>
<br>
<TABLE width="100%" class="borde_tab" cellspacing="2">
  <tr class=tpar>
   <td class=titulos3 align=center>Codigo </td>
   <td class=titulos3 align=center>Descripcion </td>
   <td class=titulos3 align=center>Termino </td>
   <td class=titulos3 align=center>Salida </td>
   <td class=titulos3 align=center>Entrada </td>
   <td class=titulos3 align=center>Memorando </td>
   <td class=titulos3 align=center>Circulares Ex/Ac </td>
   <td class=titulos3 align=center>Resolucion </td>
   <td class=titulos3 align=center>Conceptos </td>
   <td class=titulos3 align=center>Edictos </td>
   <td class=titulos3 align=center>Circulares </td>
   <td class=titulos3 align=center>Autos / Actos </td>
   <td class=titulos3 align=center>Estado </td>
   <td class=titulos3 align=center>Alerta </td>
   <td class=titulos3 align=center>Notifica</td>
  </tr>
  	<?php
	 	$rsC=$db->conn->Execute($isqlC);
   		while(!$rsC->EOF)
			{
      			$codserie  =$rsC->fields["CODIGO"];
	  			$dtipod    = $rsC->fields["TIPOD"];
				$vtermi    = $rsC->fields["TERMINO"];
				$vsalida   = $rsC->fields["SALIDA"];
				$ventrad   = $rsC->fields["ENTRADA"];
				$vmemo     = $rsC->fields["MEMORANDO"];
				$vcircula  = $rsC->fields["CIRCULARES EX/AC"];
				$vreso     = $rsC->fields["RESOLUCION"];
				$vconcept  = $rsC->fields["CONCEPTOS"];
				$vedictos  = $rsC->fields["EDICTOS"];
				$vcirexac  = $rsC->fields["CIRCULARES"];
				$vautoacto = $rsC->fields["AUTOS/ACTOS"];
				$vestado   = $rsC->fields["ESTADO"];
				$valerta   = $rsC->fields["ALERTA"];
				$vnotifica = $rsC->fields["NOTIFICA"];
		?>
    		  <tr class=paginacion>
				<td> <?=$codserie?></td>
				<td> <?=$dtipod?> </td>
				<td> <?=$vtermi?> </td>
				<td> <?=$vsalida?> </td>
				<td> <?=$ventrad?> </td>
				<td> <?=$vmemo?> </td>
				<td> <?=$vcircula?> </td>
				<td> <?=$vreso?> </td>
				<td> <?=$vconcept?> </td>
				<td> <?=$vedictos?> </td>
				<td> <?=$vcirexac?> </td>
				<td> <?=$vautoacto?> </td>
				<td> <?=$vestado?> </td>
				<td> <?=$valerta?> </td>
		        <td> <?=$vnotifica?> </td>
		 	  </tr>
	<?php
				$rsC->MoveNext();
  		}
		//<font face="Arial, Helvetica, sans-serif" class="etextomenu">
		 ?>
   </table>