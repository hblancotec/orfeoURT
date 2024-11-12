<html>
<head>
<title>.: Expedientes orfeo :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
.borde_tab {
	border: thin solid #377584;
}

.titulos4 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	line-height: 10px;
	font-weight: bolder;
	color: #FFFFFF;
	background-color: #006699;
	text-indent: 5pt;
	text-transform: uppercase;
	height: 30px;
}

.titulos5 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	color: #000000;
	background-color: #e0e6e7;
	text-indent: 5pt;
	vertical-align: middle;
}

.listado1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	text-transform: none;
	color: #000000;
	text-decoration: none;
	background-color: #FFFFFF;
	text-align: left;
	vertical-align: middle;
	height: 30px;
}

.leidos2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: normal;
	color: #006690;
	text-decoration: none;
}

.listado5 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	text-transform: none;
	color: #000000;
	text-decoration: none;
	background-color: #e3e8ec;
	text-align: left;
	text-indent: 5pt;
	vertical-align: middle;
}

.leidos {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 10px;
		font-weight: bolder;
		color: #006699;
		text-decoration: none;
}

</style>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
	<table border="0" width="100%" class='borde_tab' align="center" cellspacing="1">
		<tr class='timparr'>
			<td colspan="6" class="titulos4" align="center">
				<span class="titulos4">&Iacute;ndide de radicados.</span>
			</td>
		</tr>
		<tr class='timparr'>
			<td>
				<table border="0" width="85%" class="borde_tab" align="center" cellpadding="0" cellspacing="0">
					<tr class="listado5" style="height: 25px;">
						<td class="titulos5" align="center"></td>
						<td class="titulos5" align="center">Radicado</td>
						<td class="titulos5" align="center" width="15%">Fecha Radicaci&oacute;n</td>
						<td class="titulos5" align="center">Tipo Documental</td>
						<td class="titulos5" align="center">Asunto</td>
					</tr>
<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_time_limit ( 0 );
$expedienteSeleccionado = $numExpediente = 'RESOLUCIONES';
$ruta_raiz = "..";
require $ruta_raiz . "/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
if (!mkdir($numExpediente)) die("No se pudo crear carpeta ".$numExpediente);
// ## <!-- INICIO MOSTRAR DOCUMENTOS ELECTRONICOS -->
// ## consulta: documentos electronicos adjuntos al expediente
$consulta = "select radi_nume_radi as RADICADO, radi_fech_radi as TIEMPO, radi_path as RUTA, t.SGD_TPR_DESCRIP as TD, r.RA_ASUN as DESCRIPT 
from RADICADO r left join SGD_TPR_TPDCUMENTO t on r.TDOC_CODI=t.SGD_TPR_CODIGO 
where RADI_NUME_RADI in (20112000119173,20166630263202,20086630312782 ,20126000002242 ,20136630497932 ,20143600098763 ,20143600108903 ,20143601086961 ,20143601087071 ,20143601087081 ,20143601087101 ,20146630612122 ,20146630623582 ,20146630641522 ,20156630005952 ,20156630016052 ,20153600028851 ,20153600135521 ,20153600154561 ,20153600577341 ,20153600581991 ,20173600258601 ,20095010460881,20096630268782,20095010615971,20106630047012,20102300021463,20102820029903,20103020030693,20106630096932,20102300244511,20106630115002,20106630118092,20102300035283,20106630139452,20106630158662,20106630168902,20102300047803,20102300343141,20106630242582,20102300069883,20102820504751,20106630281602,20103020078553,20102300592751,20126630047332,20122300024993,20122820031863,20126630093992,20122300273661,20122300036753,20126630518382,20122300814831,20156630380642,20166630501172,20165500769921,20176630156802,20176630226702,20176630238452,20176630245042,20186630163962,20096630268782 ,20095010615971 ,20106630139452 ,20106630158662 ,20106630242582 ,20102300592751 ,20095010460881 ,20102300021463 ,20102820029903 ,20103020030693 ,20102300035283 ,20102300047803 ,20102300343141 ,20102300069883 ,20103020078553 ,20122820031863 ,20122300273661 ,20122300036753 ,20096630126772 ,20095010460881 ,20096630268782 ,20095010615971 ,20106630157582 ,20095010460881 ,20106630096932 ,20102300244511 ,20106630115002 ,20106630118092 ,20102300035283 ,20106630139452 ,20106630168902 ,20102300047803 ,20102300343141 ,20106630242582 ,20102300069883 ,20102820504751 ,20103020078553 ,20102300084413 ,20102300592751 ,20126630047332 ,20122300024993 ,20122820031863 ,20123020032183 ,20126630093992 ,20122300273661 ,20122300036753 ,20126630518382 ,20122300814831 ,20176630238452 ,20176630245042 ,20106630158662 ,20106630281602 ,20115010157351 ,20146630281042 ,20106630047012 ,20102300021463 ,20102820029903 ,20103020030693 ,20106630007032 ,20105010324441 ,20115010157351 ,20076630112032 ,20076630182612 ,20076630195232 ,20075010323991 ,20076630213252 ,20076630217622 ,20072820331141 ,20075010087543 ,20075010351271 ,20076630238202 ,20076630238332 ,20076630252952 ,20076630256692 ,20076630267932 ,20076630268152 ,20076630307922 ,20076630307942 ,20086630004762 ,20086630004772 ,20086630021842 ,20086630040422 ,20086630040432 ,20086630066972 ,20086630072142 ,20082820241611 ,20086630115012 ,20082300368731 ,20086630312782 ,20086630345572 ,20096630317622 ,20096630402882 ,20106630213992 ,20116630113892 ,20111330162943 ,20126000002242 ,20122300061531 ,20126630529542 ,20136630169752 ,20136630199372 ,20136630252472 ,20136630400852 ,20136630497932 ,20136630557882 ,20136630575572 ,20143600098763 ,20146630541202 ,20146630576902 ,20146630623582 ,20146630641522 ,20153600178001 ,20155200448211 ,20166630596832 ,20176630140012 ,20176630149562 ,20176630152262 ,20173600258601 ,20186630170092) 
order by radi_fech_radi";
$db->conn->debug = false;
$adjun = $db->conn->Execute($consulta);
$htmlPrintIni1 = '<tr class="listado1"  class="tpar">';
$htmlPrintIni2 = '<td style="margin-left: 10px; margin-right: 10px; ' . 'padding-bottom: 5px;' . 'padding-left: 10px; padding-right: 10px;">';
$htmlPrintFin2 = '</td>';
$htmlPrintFin1 = '</tr>';
$cnt = 1;
while (!$adjun->EOF and !empty($adjun)) {
    $tiempo = $adjun->fields['TIEMPO'];
    $td = $adjun->fields['TD'];
    $descript = $adjun->fields['DESCRIPT'];
    $ruta = $adjun->fields['RUTA'];
    $rad = $adjun->fields['RADICADO'];
    $ruta = str_replace("/", "\\", $ruta);
    $ini = "\\\\VORFEOBOD\\bodega\\$ruta";
    $info = pathinfo($ini);
    $des = str_replace("/", "\\", dirname(__FILE__)."/".$numExpediente."/").$info['basename'];
    if (!copy( $ini, $des) ) {
        $errors= error_get_last();
        echo "COPY ERROR: ".$errors['type'];
        echo "<br/>\n".$errors['message']."<br/>";
        die("No pude copiar $ini a $des .");
    }
    $ruta = "<a href='./".$info['basename']."' target='_blank' ><span class=leidos>$rad</span></a>";    
    $icono = '<img name="imgAdjuntos"  src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEZSURBVCjPY/jPgB9iEVoqPefllFPdNk2GWBUsVpz9ctL1rkcNW/v+59VhKFgkPfP+xI0dF+uC/jPkWCR/Q1MwX2TGvf7Nretr/UG8BO2I5ygK5olP/dCzpWV+dVAhd+bB+JawrT7ICubIT3nbvaFpVkVqgVDa0diO4CneN91E4Qpmq0560jW/YXp5XB5nyq2YrqCFno9cJeG+mKk48UHHjLruMu8czuSbkfUBizxeucrDw2GGev/71uW1jMVrsq4nPIto8F/g8caFDymgetxbHlVLgDjxnWExPjPdb7sIoYRkk17FywJRECdY1Xux201nMbSgLufO25qyJUY1yNrzsus9JxkscZHMG+kVcN7jqWueowARkUWiAgBEUvolGfpITwAAAABJRU5ErkJggg==" border="0">';
    
    echo $htmlPrintIni1 . $htmlPrintIni2. $cnt. $htmlPrintFin2. $htmlPrintIni2 . $icono . $ruta .$htmlPrintFin2 . $htmlPrintIni2 . '<center>' . $tiempo . '</center>' . $htmlPrintFin2 . $htmlPrintIni2 . $td . $htmlPrintFin2 . $htmlPrintIni2 .  $descript . $htmlPrintFin2 . $htmlPrintFin1;
    
    $cnt ++;
    $adjun->MoveNext();
}
// Fin del While que Recorre los documentos de un expediente.
?>						
						
					</table>
			</td>
		</tr>
	</table>
	</body>
</html>