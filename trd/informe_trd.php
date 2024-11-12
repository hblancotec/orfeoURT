<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
if ($_SESSION['usua_perm_trd'] != 1) {
    die(include $ruta_raiz . "/sinpermiso.php");
    exit();
}

$anoActual = date("Y");
if (! $fecha_busq) {
    $fecha_busq = date("Y-m-d");
}
if (! $fecha_busq2) {
    $fecha_busq2 = date("Y-m-d");
}
if ($version) {
    $version = strtoupper(trim($version));
} else {
    $version = 0;
}

$ruta_raiz = "..";
if (! $_SESSION['dependencia'] and ! $_SESSION['depe_codi_territorial']) {
    include "../rec_session.php";
}
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
if (! defined('ADODB_FETCH_ASSOC')) {
    define('ADODB_FETCH_ASSOC', 2);
}
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

if ($_POST['generar_informe'] || $_POST['generar_pdf']) {
    if ($_POST['dep_sel'] == 0) {
        $where_depe = '';
    } else {
        $where_depe = " and m.depe_codi = " . $_POST['dep_sel'];
    }
    $generar_informe = 'generar_informe';
    error_reporting(7);
    $tabla = "";
    $tablaPdf = "";
    include "$ruta_raiz/include/query/trd/queryinforme_trd.php";
    $query_t = $query;
    $ruta_raiz = "..";

    $rs = $db->conn->Execute($query_t);

    $nSRD_ant = "";
    $nSBRD_ant = "";
    $nTDoc_ant = "";
    $depTDR_ant = "";
    $tabla .= "<br><div style='margin: auto; justify-content: center; text-align: center; width: 100%; display: flex;'>
            <table class='borde_tab' style='font-family:arial;width:80%;'>
			<tr class='titulos5'>
				<td colspan='3' align='center'>C&oacute;digo</td>
				<td align='center' rowspan='2'>Series Y Tipos Documentales</td>
				<td colspan='2' align='center'>Retenci&oacute;n<br/> A&ntilde;os</td>
				<td colspan='4' align='center'>Disposici&oacute;n<br/>Final</td>
				<td colspan='3' align='center'>Soporte</td>
				<td rowspan='2' align='center' width='30%'>Procedimiento</td>
			</tr>
			<tr class='titulos5'>
				<td align='center'>D</td>
				<td align='center'>S</td>
				<td align='center'>Sb</td>
				<td align='center'>AG</td>
				<td align='center'>AC</td>
				<td align='center'>CT</td>
				<td align='center'>E</td>
				<td align='center'>I</td>
				<td align='center'>S</td>
				<td align='center'>P</td>
				<td align='center'>EL</td>
				<td align='center'>O</td>
			</tr>";

    while (! $rs->EOF and $rs) {

        $nSRD = strtoupper($rs->fields['SGD_SRD_DESCRIP']); // Nombre Serie
        $depTDR = $rs->fields['CODI_HOMOLOGA']; // $rs->fields ['DEPE_CODI']; // Dependencia
        $nSBRD = $rs->fields['SGD_SBRD_DESCRIP']; // Nombre SubSerie
        $cSRD = $rs->fields['SGD_SRD_CODIGO']; // Codigo Serie
        $cSBRD = $rs->fields['SGD_SBRD_CODIGO']; // Codigo Subserie
        $codTDoc = $rs->fields['SGD_TPR_CODIGO'];
        $nTDoc = ucfirst(strtolower($rs->fields['SGD_TPR_DESCRIP'])); // Nombre Tipo Documental
        if ($depTDR != $depTDR_ant) {
            $tabla .= "<tr class='listado5'><td colspan='14' align='center'>$depTDR " . $rs->fields['DEPE_NOMB'] . " (" . $rs->fields['DEPE_CODI'] . ") </td></tr>";
        }
        if ($nSRD != $nSRD_ant) {
            $tabla .= "<tr class='listado5'><td>$depTDR</td>
			<td>&nbsp;$cSRD</td>
			<td>&nbsp;</td><td colspan='11'>$nSRD</td></tr>";
        }

        if (($nSBRD != $nSBRD_ant) || ($nSRD != $nSRD_ant)) {
            $conserv = strtoupper(substr(trim($rs->fields['DISPOSICION']), 0, 1));
            $soporte = strtoupper(substr(trim($rs->fields['SGD_SBRD_SOPORTE']), 0, 1));

            $conservCT = ($conserv == "C") ? "X" : "&nbsp;";
            $conservE = ($conserv == "E") ? "X" : "&nbsp;";
            $conservI = ($conserv == "I") ? "X" : "&nbsp;";
            $conservS = ($conserv == "M") ? "X" : "&nbsp;";

            $soporteP = ($soporte == "P") ? "X" : "&nbsp;";
            $soporteEl = ($soporte == "E") ? "X" : "&nbsp;";
            $soporteO = ($soporte == "O") ? "X" : "&nbsp;";

            $tiemag = $rs->fields['SGD_SBRD_TIEMAG'];
            $tiemac = $rs->fields['SGD_SBRD_TIEMAC'];
            $nObservacion = $rs->fields['SGD_SBRD_PROCEDI'];

            $tabla .= "<tr valign='top' class='leidos'>
			<td>$depTDR</td>
			<td>&nbsp;$cSRD</td>
			<td>.$cSBRD</td>
			<td>$nSBRD</td>";

            $conservacion = "<td align='center'>$conservCT</td><td align='center'>$conservE</td><td align='center'>$conservI</td><td align='center'>$conservS</td>";
            $soporte = "<td align='center'>$soporteP</td><td align='center'>$soporteEl</td><td align='center'>$soporteO</td>";
            $tabla .= "<td align='center'>$tiemag</td><td align='center'>$tiemac</td>$conservacion $soporte
			<td rowspan='" . ($rs->fields['cnt_tdoc'] + 1) . "'>&nbsp;" . ucfirst($nObservacion) . "</td></tr>";
        }

        $tabla .= "<tr><td colspan='3'>&nbsp;</td><td valign='top' colspan='10' style='font-size:10px;width:80%;'>&nbsp;&nbsp;&nbsp;&nbsp; $codTDoc - $nTDoc</td></tr>";
        $depTDR_ant = $depTDR;
        $nSRD_ant = $nSRD;
        $nSBRD_ant = $nSBRD;
        $rs->MoveNext();
    }
    $tabla .= "</table></div>";

    if ($_POST['generar_pdf']) {

        require ORFEOPATH . '/lib/html2pdf-4.4.0/html2pdf.class.php';
        try {
            $ruta = "tmp/tmp_trd.pdf";
            $html2pdf = new HTML2PDF('L', 'A4', 'es');
            // $html2pdf->setModeDebug();
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->writeHTML($tabla, false);
            $html2pdf->Output(BODEGAPATH . $ruta, 'D');
            $tabla = "<a href='" . BODEGAURL . $ruta . "' target='_blank'>Descargar</a>";
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit();
        }

        // require 'trdpdf.class.php';
        // $ruta = "tmp/tmp_trd.pdf";
        // $pdf = new TRDPDF( 'L', 'mm', 'A4' );
        // $pdf->WriteHTML ( $tabla );
        // $pdf->Output ( BODEGAPATH . $ruta, 'F' );
        // $tabla = "<a href='" . BODEGAURL . $ruta . "' target='_blank'>Descargar</a>";
    }
}
?>
<html>
<head>
	<link rel="stylesheet" href="../estilos/orfeo.css">
	<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
	<script language="javascript">
		function selectVersion() {
			document.getElementById("inf_trd").submit();
		}
	</script>
</head>
<body>
	<form id="inf_trd" name="inf_trd" action='../trd/informe_trd.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah"?>' method="post">
		<TABLE width="80%" class='borde_tab' align="center">
			<tr>
				<td height="30" valign="middle" class='titulos5' align="center"
					colspan="4">INFORME TABLAS DE RETENCI&Oacute;N DOCUMENTAL
				</td>
			</tr>
			<tr>
				<td width="95" height="21" class='titulos5'>
    				Versi&oacute;n
    			</td>
    			<td class='titulos5'>
    				<select name="version" id="version" class="select" onChange="selectVersion();">
    					<option value="">--Seleccione--</option>
    					<?php 
    						$sqlVer = "SELECT [ID], [VERSION], [NOMBRE], [OBSERVACION], [FECHA_INICIO], [FECHA_FIN]
                                            FROM [VERSIONES]";
                            $rsVer = $db->conn->Execute($sqlVer);
                            if ($rsVer) {
                                while (!$rsVer->EOF) {
                                    $idver = $rsVer->fields["ID"];
                                    $versi = $rsVer->fields["VERSION"];
                                    $nombre = $rsVer->fields["NOMBRE"];
                                    
                                    $selected = "";
                                    if ($idver == $version) {
                                        $selected = " selected ";
                                    }
                                    ?>
                                	<option value="<?=$idver?>" <?=$selected?>><?=$versi . " - " . $nombre?></option>
                                <?php 
                                $rsVer->MoveNext();
                            }
                        }                           
                        ?>
                    </select>
    			</td>	
				<td height="26" class='titulos5'>Dependencia</td>
				<td valign="middle" class='titulos5'>
                    <?php
                    error_reporting(7);
                    $ss_RADI_DEPE_ACTUDisplayValue = "--- TODAS LAS DEPENDENCIAS ---";
                    $valor = 0;
                    include "$ruta_raiz/include/query/devolucion/querydependencia.php";
                    $sqlD = "select $sqlConcat ,depe_codi from dependencia where id_version = $version order by depe_codi";
                    $rsDep = $db->conn->Execute($sqlD);
                    echo $rsDep->GetMenu2("dep_sel", "$dep_sel", $blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0, " onChange='submit();' class='select'");
                    ?>
				</td>
			</tr>
			<tr>
				<td height="26" colspan="4" valign="top" class='titulos5' align="center">
					<input type="submit" name='generar_informe' value=' Generar Listado ' class='botones_mediano' /> 
					<!-- <input type='submit' name='generar_pdf' value=' Generar PDF ' class='botones_mediano' /> -->
				</td>
			</tr>
		</TABLE>

<?php
if ($_POST['generar_informe']) {
    echo $tabla;
}
?>
</form>
</body>
</html>