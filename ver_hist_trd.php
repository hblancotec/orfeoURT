<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "sinacceso.php");
	exit;
}
$radicado = htmlspecialchars($_GET['rad']);
$anexo = htmlspecialchars($_GET['id']);
$tipo = htmlspecialchars($_GET['type']);
include "adodb/tohtml.inc.php";
require_once "include/db/ConnectionHandler.php";

$sql = "SELECT	H.SGD_HMTD_FECHA,
				D.DEPE_NOMB,
		        U.USUA_NOMB,
                T.SGD_TTR_DESCRIP,
				H.SGD_HMTD_OBSE,
                S.SGD_SRD_DESCRIP,
				SB.SGD_SBRD_DESCRIP,
				TP.SGD_TPR_DESCRIP
		FROM	SGD_HMTD_HISMATDOC H
				JOIN USUARIO U ON U.USUA_DOC = H.USUA_DOC
				JOIN DEPENDENCIA D ON D.DEPE_CODI = H.DEPE_CODI
				JOIN SGD_TTR_TRANSACCION T ON T.SGD_TTR_CODIGO = H.SGD_TTR_CODIGO
				LEFT JOIN SGD_MRD_MATRIRD M ON M.SGD_MRD_CODIGO = H.SGD_MRD_CODIGO
				LEFT JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
				LEFT JOIN SGD_SBRD_SUBSERIERD SB ON SB.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO AND SB.SGD_SBRD_CODIGO = M.SGD_SBRD_CODIGO
				LEFT JOIN SGD_TPR_TPDCUMENTO TP ON TP.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
		WHERE	H.RADI_NUME_RADI = $radicado
		ORDER BY 1 DESC";
            
if (!$db)
    $db = new ConnectionHandler('.');
if ($db) {
    $db->conn->debug = false;
    $rs = $db->conn->Execute($sql);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
    <HEAD>
        <TITLE> Hist&oacute;rico de TRD del radicado  <?php echo $radicado; ?> </TITLE>
        <link rel="stylesheet" href="estilos/orfeo.css">
    </HEAD>
    <BODY>
        <Form>
			<Table border='1' cellpanding='2' cellspacing='0' class='borde_tab' valign='top' align='center' width='95%' scroll='yes'>
				<tr>
					<td class="titulos4" align='center'>
						Historial TRD para el radicado No. <?php echo $radicado; ?>
					</td>
				</tr>
			</Table>
			<br>
            <Table border='1' cellpanding='2' cellspacing='0' class='borde_tab' valign='top' align='center' width='95%' scroll='yes'>
				<tr>
                    <th class='titulos3'>Fecha Transacci&oacute;n </th>
					<th class='titulos3'>Dependencia </th>
                    <th class='titulos3'>Funcionario </th>
                    <th class='titulos3'>Transacci&oacute;n</th>
					<th class='titulos3'>Observación</th>
                    <th class='titulos3'>TRD</th>
                </tr>

                <?php
                $css = 1;
                while ($arr = $rs->FetchRow()) {
                    ?>

                    <tr class='listado<?php $css ?>' style="font:normal 11px Arial;">
                        <td> <?php echo $arr[0]; ?> </td>
                        <td> <?php echo $arr[1]; ?> </td>
                        <td> <?php echo $arr[2]; ?> </td>
                        <td> <?php echo $arr[3]; ?> </td>
						<td> <?php echo $arr[4]; ?> </td>
						<td> <?php echo $arr[5]; ?> - <?php echo $arr[6]; ?> - <?php echo $arr[7]; ?> </td>
						
                    </tr>

                    <?php
                    $css = ($css == 1) ? 2 : 1;
                }
                ?>

            </Table>
            <Table align="center">
                <tr>
                    <td>
                        <input align="center" name="button" type="button" class="botones_largo" onClick="window.close()" value="CERRAR">
                    </td>
                </tr>
            </Table>
        </Form>
    </BODY>
</HTML>