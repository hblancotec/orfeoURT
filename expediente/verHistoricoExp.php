<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include "$ruta_raiz/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
$dependencia = $_SESSION['dependencia'];
$codusuario = $_SESSION['codusuario'];
if (!isset($_SESSION['dependencia']))  include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);	 
/** PAGINA QUE DESPLIEGA EL HISTORICO DE DE UN EXPEDIENTE
	* Esta pagina necesita que llegue el Numero de expediente en la variable $numeroExpediente
	* @version ORFEO 3.5
	* @autor JAIRO LOSADA - SUPERSERVICIOS
	* @fecha Marzo de 2006
	* @licencia GPL. Software Libre.
	* @param $numeroExpediente Integer Numero de Expediente
	*
	**/

/** TRAER DTOS DE EXPEDIENTE
	* @param $trdExp objecto Objeto que trae funciones del expediente
	* @param $tSub  int Almacena Codigo de la Subserie.
  * @param $codSerie int Almacena Codigo de la Serie Documental.
	* @param $descFldExp String Guarda Descripcion del estado del Flujo del Expediente Actual.
	* @param $expTerminos Int   Guarda los terminos o Dias Habiles del proceso Actual.
	* @param $expFechaCreacion date Fecha de Creacion del expediete.
	* 
	**/
	include_once ("$ruta_raiz/include/tx/Expediente.php");
	$trdExp = new Expediente($db);
	$mrdCodigo = $trdExp->consultaTipoExpediente($numeroExpediente);
	$trdExpediente= $trdExp->descSerie." / ".$trdExp->descSubSerie;
	$descPExpediente = $trdExp->descTipoExp;
	$codSerie = $trdExp->codigoSerie;
	$cosSub = $trdExp->codigoSubSerie;
	$tdoc = $trdExp->codigoTipoDoc;
	$codigoTipoExp = $trdExp->codigoTipoExp;
	$codigoFldExp = $trdExp->codigoFldExp;
	$expFechaCrea = $trdExp->expFechaCrea;
	$descTipoExp = $trdExp->descTipoExp;
	$no_tipo = false;
	
	include_once "$ruta_raiz/tx/diasHabiles.php";
	$a = new FechaHabil($db);
?>
<html>
<head>

<title>HISTORICO EXPEDIENTE <?=$numeroExpediente?></title>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body >
<table width="1024" border="0" cellpadding="0" cellspacing="0" align="center">
  <tr bgcolor="#006699">
    <td class="titulos4" colspan="6" ><center>HISTORICO DEL EXPEDIENTE <?=$numeroExpediente?> </center></td>
	 </tr>
</table>

<?php
	require_once("$ruta_raiz/class_control/Transaccion.php");
	require_once("$ruta_raiz/class_control/Dependencia.php");
	require_once("$ruta_raiz/class_control/usuario.php");
	$trans = new Transaccion($db);
	$objDep = new Dependencia($db);
	$objUs = new Usuario($db);
	$isql = "select USUA_NOMB from usuario where depe_codi=$dependencia and usua_codi=$codusuario";
	$rs = $db->conn->Execute($isql);			      	   
	$usuario_actual = $rs->fields["USUA_NOMB"];
//include_once "$ruta_raiz/flujoGrafico.php";
?> 
<table  width="1024" align="center" border="0" cellpadding="0" cellspacing="1" class="borde_tab" >
  <tr   align="center">
    <td width=100 class="titulos2" height="24">DEPENDENCIA </td>
    <td  width=100 class="titulos2" height="24">FECHA</td>
     <td  width=100 class="titulos2" height="24">TRANSACCION </td>  
    <td  width=100 class="titulos2" height="24" >USUARIO</td>
		<td  width=100 class="titulos2" height="24" >RADICADO</td>
    <td  width=200 height="24" class="titulos2">COMENTARIO</td>
	<?php
		/** FLUJO GRAFICO DE LOS ESTADOS POR EL CUAL PASA EL EXPEDIENTE
			*/
 	$isql = "select 
					fe.SGD_FEXP_DESCRIP
					,fe.SGD_FEXP_TERMINOS
					,fe.SGD_FEXP_CODIGO
					,fe.SGD_FEXP_ORDEN
			from SGD_FEXP_FLUJOEXPEDIENTES fe
		 where 
			fe.SGD_PEXP_CODIGO ='$codigoTipoExp'
			order by fe.SGD_FEXP_ORDEN  ";  
	$rs = $db->conn->Execute($isql);
	$terminosTotales = 0;
if($rs)
{
	while(!$rs->EOF) {
		$etapaFlujo = $rs->fields["SGD_FEXP_DESCRIP"];
		$etapaFlujoTerminos = $rs->fields["SGD_FEXP_TERMINOS"];
		$terminosTotales = $terminosTotales + $etapaFlujoTerminos;
		$codFlujo = $rs->fields["SGD_FEXP_CODIGO"];
		$codOrden = $rs->fields["SGD_FEXP_ORDEN"];
		$flujoCodigo[$codFlujo] = $codOrden;
		$flujoTerminosReal[$codFlujo] = $terminosTotales;
?>
		<td  width=300 height="24" class="titulos2"><?=$etapaFlujo?> (<?=$etapaFlujoTerminos?> / <?=$flujoTerminosReal[$codFlujo]?> Dias)</td>
		<?php
		$rs->MoveNext();
	}
}
	?>
  </tr>
  <?php
	$radiNumeRadi = "he.RADI_NUME_RADI";
	// si esta trabajando con MSSQL
	if ($db->driver == "mssqlnative"){
		$radiNumeRadi = "CONVERT(VARCHAR(15), he.RADI_NUME_RADI) AS RADI_NUME_RADI";
	}
	$sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","he.SGD_HFLD_FECH");
	$sqlFecha = $db->conn->SQLDate("Y-m-d","he.SGD_HFLD_FECH");
 	$isql = "SELECT $sqlFecha HIST_FECH,
			he.DEPE_CODI,
			he.USUA_CODI,
			$radiNumeRadi ,
			he.SGD_HFLD_OBSERVA HIST_OBSERVA,
			he.USUA_DOC,
			he.SGD_TTR_CODIGO,
			$radiNumeRadi,
			he.SGD_FEXP_CODIGO,
			$sqlFecha FECHA
		FROM SGD_HFLD_HISTFLUJODOC he
		WHERE 	he.SGD_EXP_NUMERO ='$numeroExpediente'
		ORDER BY he.SGD_HFLD_FECH DESC";  
	$i=1;
	$rs = $db->conn->Execute($isql);
if($rs) {
    while(!$rs->EOF) {
		$usua_doc_dest = "";
		$usua_doc_hist = "";
		$usua_nomb_historico = "";
		$usua_destino = "";
		$numdata =  trim($rs->fields["CARP_CODI"]);
		if($data =="") $rs1->fields["USUA_NOMB"];
	   		$data = "NULL";
		$numerot = $rs->fields["NUM"];
		$usua_doc_hist = $rs->fields["USUA_DOC"];
		$usua_codi_dest = $rs->fields["USUA_CODI_DEST"];
		$usua_dest=intval(substr($usua_codi_dest,3,3));
		$depe_dest=intval(substr($usua_codi_dest,0,3));
		$usua_codi = $rs->fields["USUA_CODI"];
		$depe_codi = $rs->fields["DEPE_CODI"];
		$codTransac = $rs->fields["SGD_TTR_CODIGO"];
		$descTransaccion = $rs->fields["SGD_TTR_DESCRIP"];
    if(!$codTransac) $codTransac = "0";
		$trans->Transaccion_codigo($codTransac);
		$objUs->usuarioDocto($usua_doc_hist);
		$objDep->Dependencia_codigo($depe_codi);

		if($carpeta==$numdata)
			{
			$imagen="usuarios.gif";
			}
		else
			{
			$imagen="usuarios.gif";
			}
		if($i==1)
			{
		?>
  <tr class='tpar'> <?php  
		    $i=1;
			}
			 ?>
    <td class="listado2" >
	<?=$objDep->getDepe_nomb()?></td>
    <td class="listado2">
	<?php
			$expFechaHist = $rs->fields["HIST_FECH"];
			echo $expFechaHist;
	?>
 </td>
<td class="listado2"  >
  <?=$trans->getDescripcion()?>
</td>
<td class="listado2"  >
   <?=$objUs->get_usua_nomb()?>
</td>
<td class="listado2"  >
   <?=$rs->fields["RADI_NUME_RADI"]?>
</td>
		<?php
		 /**
			 *  Campo qque se limino de forma Temporal USUARIO - DESTINO 
			 * <td class="celdaGris"  >
			 * <?=$usua_destino?> </td> 
			 */
		?>
		<td class="listado2" width="200"><?=$rs->fields["HIST_OBSERVA"]?></td>
	<?php 
			$flujoCodigoActual = $flujoCodigo[$rs->fields["SGD_FEXP_CODIGO"]];
			
			for($i=0;$i<=$flujoCodigoActual-1;$i++)
			{	$counter = 0;
				
				foreach ($flujoCodigo as $line_num => $line) {
					if($line == $flujoCodigoActual){
						break;
					}else {
						$counter++;
					}
				}
					if($counter==$i)
						{
								$classMostrar="titulos4";
							//$fondoImg = "$ruta_raiz/imagenes/internas/moverA.gif";
						}else
						{
							  $classMostrar="titulosError";
								$fondoImg = "";
						}
				?>
						<td class="<?=$classMostrar?>" width="100" background="<?=$fondoImg?>" align="top">
						<?php
								if($counter==$i)
								{
								 $tReal = $a->diasHabiles($expFechaCrea,$rs->fields["FECHA"]);
								 echo "$tReal Dias.";
								}
						?>
						</td>
				<?php
			}
			
	?>
  </tr>
  <?php
	$rs->MoveNext();
  	}
}
  // Finaliza Historicos
	?>
</table>

</body>
</html>
