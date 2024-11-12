<?php
$paramsTRD=$phpsession."&krd=$krd&codiTRD=$codiTRD&tsub=$tsub&codserie=$codserie&tipo=$tipo&dependencia=$dependencia&depe_codi_territorial=$depe_codi_territorial&usua_nomb=$usua_nomb&"
				."depe_nomb=$depe_nomb&usua_doc=$usua_doc&codusuario=$codusuario";

?>
<form name = formaTRD action="upload2.php?<?=$paramsTRD?>" method="post">
<table width="70%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
	<tr align="center">
		<td height="35" colspan="2" class="titulos4">APLICACI&Oacute;N DE LA TRD</td>
	</tr>
	<tr align="center">
		<td width="36%" class="titulos2">SERIE</td>
		<td width="64%" height="35" class="listado2">
<?php
//echo "<hr>$tipoRad";
$coddepe=$_SESSION['dependencia'];
if($codserie!=0 and $tipo !=0 and $tsub !=0)
{	$queryTRD = "select SGD_MRD_CODIGO AS CLASETRD from sgd_mrd_matrird m
				where m.depe_codi = '$coddepe'
					and m.sgd_srd_codigo = '$codserie' and m.sgd_sbrd_codigo = '$tsub' and m.sgd_tpr_codigo = '$tipo'";
	$rsTRD=$db->conn->Execute($queryTRD);
	if($rsTRD)
   	{	$codiTRD = $rsTRD->fields['CLASETRD'];	}
}

//$coddepe=$dependencia;
if ($coddepe != 0 && $tipo != 0 && $tsub != 0)
if(!$tipo) $tipo = 0;
if(!$codserie) $codserie = 0;
if(!$tsub) $tsub = 0;
$fechah=date("dmy") . " ". time("h_m_s");
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
$num_car = 4;
$nomb_varc = "s.sgd_srd_codigo";
$nomb_varde = "s.sgd_srd_descrip";
include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
$querySerie = "	SELECT	DISTINCT ($sqlConcat) as DETALLE,
						s.SGD_SRD_CODIGO
				FROM	SGD_MRD_MATRIRD m INNER JOIN
						SGD_SRD_SERIESRD s ON s.SGD_SRD_CODIGO = m.SGD_SRD_CODIGO
				WHERE 	m.DEPE_CODI = '$coddepe'
						AND ".$sqlFechaHoy." between s.SGD_SRD_FECHINI AND s.SGD_SRD_FECHFIN
						AND m.SGD_MRD_ESTA = 1
				ORDER BY DETALLE";
$rsD=$db->conn->Execute($querySerie);
$comentarioDev = "Muestra las Series Docuementales";
include "$ruta_raiz/include/tx/ComentarioTx.php";
print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
?>
		</td>
 	<tr align="center">
		<td width="36%" class="titulos2">SUBSERIE</td>
		<td width="64%" height="35" class="listado2">
<?php
$nomb_varc = "su.sgd_sbrd_codigo";
$nomb_varde = "su.sgd_sbrd_descrip";
include("$ruta_raiz/include/query/trd/queryCodiDetalle.php");
$querySub =	"SELECT	DISTINCT ($sqlConcat) AS DETALLE,
					su.SGD_SBRD_CODIGO
			 FROM	SGD_MRD_MATRIRD m INNER JOIN
			 		SGD_SBRD_SUBSERIERD su ON su.SGD_SBRD_CODIGO = m.SGD_SBRD_CODIGO
			 WHERE	m.DEPE_CODI = '$coddepe'
			       	AND m.SGD_SRD_CODIGO = '$codserie'
				    AND su.SGD_SRD_CODIGO = '$codserie'
 			        AND ".$sqlFechaHoy." BETSEEN su.SGD_SBRD_FECHINI AND su.SGD_SBRD_FECHFIN
 			        AND m.SGD_MRD_ESTA = 1
			 ORDER BY DETALLE";
$rsSub=$db->conn->Execute($querySub);
include "$ruta_raiz/include/tx/ComentarioTx.php";
		print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
?>
		</td>
	</tr>
  	<tr align="center">
		<td width="36%" class="titulos2">TIPO DE DOCUMENTO</td>
		<td width="64%" height="35" class="listado2">
<?php
$ent = 1;
$nomb_varc = "t.sgd_tpr_codigo";
$nomb_varde = "t.sgd_tpr_descrip";
include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
$queryTip = "select distinct ($sqlConcat) as detalle, t.sgd_tpr_codigo
	         from sgd_mrd_matrird m, sgd_tpr_tpdcumento t
			 where m.depe_codi = '$coddepe'
 			       and m.sgd_srd_codigo = '$codserie'
			       and m.sgd_sbrd_codigo = '$tsub'
 			       and t.sgd_tpr_codigo = m.sgd_tpr_codigo
				   and t.sgd_tpr_tp$ent='1'
			 order by detalle";
$rsTip=$db->conn->Execute($queryTip);
include "$ruta_raiz/include/tx/ComentarioTx.php";
print $rsTip->GetMenu2("tipo", $tipo, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
?>
</tr>
</table>

<table><tr><td></td></tr></table>
<table width="31%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
	<tr align="center">
		<td height="25" colspan="2" class="titulos4">TIPO DE RADICACI&oacute;N</td>
	</tr>
	<tr align="center">
		<td width="16%" class="titulos2">Seleccione: </td>
		<td width="84%" height="30" class="listado2">
			<?php
				$cad = "USUA_PRAD_TP";
				// Creacion del combo de Tipos de radicado habilitados seg�n permisos
				$sql = "SELECT SGD_TRAD_CODIGO,SGD_TRAD_DESCR FROM SGD_TRAD_TIPORAD WHERE SGD_TRAD_GENRADSAL > 0";	//Buscamos los TRAD En la entidad
				$Vec_Trad = $db->conn->GetAssoc($sql);
				$Vec_Perm = array();
				while (list($id, $val) = each($Vec_Trad))
				{	$sql = "SELECT ".$cad.$id." FROM USUARIO WHERE USUA_LOGIN='".$krd."'";
					$rs2 = $db->conn->Execute($sql);
					if  ($rs2->fields[$cad.$id] > 0)
					{	$Vec_Perm[$id] = $val;
					}
				}
				//print_r($Vec_Perm);
				reset($Vec_Perm);
			?>
			<select name="tipoRad" id="Slc_Trd" class="select" onchange="submit();">
				<?php
				while (list($id, $val) = each($Vec_Perm))
				{
					if($tipoRad==$id) $datoss = " selected "; else $datoss="";
					echo "<option value=".$id." $datoss>$val</option>";
				}
				?>
			</select>
		</td>
	</tr>
</table><br/>
<?php
$queryProc = "select SGD_PEXP_DESCRIP,SGD_PEXP_CODIGO
         from SGD_PEXP_PROCEXPEDIENTES
         WHERE SGD_SRD_CODIGO=$codserie
         AND SGD_SBRD_CODIGO=$tsub";
        $rs=$db->conn->Execute($queryProc);
$codTmpProc = $rs->fields["SGD_PEXP_CODIGO"];
if($codTmpProc)
{
?>

<table width="31%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
	<tr class="titulos5">
      <td>VINCULAR A PROCESO </td>
      <td>
        <?php

        print $rs->GetMenu2("codProceso", $codProceso, "0:-- Ningun Proceso --", false,""," class='select'  onchange='submit();'" );
		include ("$ruta_raiz/include/tx/Flujo.php");
		$objFlujo = new Flujo($db, $codProceso,$usua_doc);
		echo $objFlujo->getMenuProximaArista($tipo, $codProceso,$codserie,$tsub,$tipoRad,'pNodo',$pNodo," class='select' onChange='submit();'");
        ?>
       </td>
	</tr>
</table>
<?php
}
?>
</form>
