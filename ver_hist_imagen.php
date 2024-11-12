<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "sinacceso.php");
    exit();
}
$radicado = htmlspecialchars($_GET['rad']);
$anexo = htmlspecialchars($_GET['id']);
$tipo = htmlspecialchars($_GET['type']);
include "adodb/tohtml.inc.php";
require_once "include/db/ConnectionHandler.php";
if (! $db)
    $db = new ConnectionHandler('.');

switch ($tipo) {
    case 'a':
        { // anexo
            $sql = "SELECT  H.FECHA,
                            U1.USUA_NOMB AS NOMBRE,
                            T.SGD_TTR_DESCRIP AS DESCR,
                            RUTA
                    FROM SGD_HIST_IMG_ANEX_RAD H 
                        INNER JOIN USUARIO U1 ON H.USUA_DOC = U1.USUA_DOC AND  H.USUA_LOGIN = U1.USUA_LOGIN
                        INNER JOIN SGD_TTR_TRANSACCION T ON H.ID_TTR_HIAN=T.SGD_TTR_CODIGO
                    WHERE ANEX_RADI_NUME = $radicado AND ANEX_CODIGO = '$anexo'";
            $title = "Hist&oacute;rico de imagen de anexo $anexo al radicado $radicado.";
        }
        break;
    case 'r':
        { // radicado
            $sql = "SELECT  H.FECHA,
                            U1.USUA_NOMB AS NOMBRE,
                            T.SGD_TTR_DESCRIP AS DESCR,
                            RUTA
                    FROM SGD_HIST_IMG_RAD H 
                            INNER JOIN USUARIO U1 ON H.USUA_DOC = U1.USUA_DOC AND  H.USUA_LOGIN = U1.USUA_LOGIN
                            INNER JOIN SGD_TTR_TRANSACCION T ON H.ID_TTR_HIAN=T.SGD_TTR_CODIGO
                    WHERE RADI_NUME_RADI = $radicado ORDER BY H.FECHA DESC";
            $title = "Hist&oacute;rico de imagen de radicado $radicado.";
        }
        break;
    case 'e':
        { // expediente
            $sql = "SELECT  H.FECHA,
                            U1.USUA_NOMB AS NOMBRE,
                            T.SGD_TTR_DESCRIP AS DESCR,
                            RUTA
                    FROM SGD_HIST_IMG_ANEX_EXP H 
                        INNER JOIN USUARIO U1 ON H.USUA_DOC = U1.USUA_DOC AND  H.USUA_LOGIN = U1.USUA_LOGIN
                        INNER JOIN SGD_TTR_TRANSACCION T ON H.ID_TTR_HIAN=T.SGD_TTR_CODIGO
                    WHERE ANEXOS_EXP_ID = '$anexo'";
            $title = "Hist&oacute;rico de imagen de anexo $anexo.";
        }
        break;
    case 'c':
        { // certimail
            $sql = "SELECT  H.FECHA,
                            U1.USUA_NOMB AS NOMBRE,
                            T.SGD_TTR_DESCRIP AS DESCR,
                            RUTA
                    FROM SGD_HIST_CERTIMAIL H 
                        INNER JOIN USUARIO U1 ON H.USUA_DOC = U1.USUA_DOC AND  H.USUA_LOGIN = U1.USUA_LOGIN
                        INNER JOIN SGD_TTR_TRANSACCION T ON H.ID_TTR_HCTM=T.SGD_TTR_CODIGO
                    WHERE RADI_NUME_RADI = $radicado";
            $title = "Hist&oacute;rico de acuses Correo Electr&oacute;nico radicado $radicado.";
        }
        break;
}

if ($db) {
    // $db->conn->debug = true;
    $rs = $db->conn->Execute($sql);
}
?>
<HTML>
<HEAD>
<TITLE><?php echo $title ?></TITLE>
<link rel="stylesheet" href="estilos/orfeo.css">
</HEAD>
<BODY>
	<Form>
		<Table border='1' cellpanding='2' cellspacing='0' class='borde_tab'
			valign='top' align='center' width='90%' scroll='yes'>
			<tr>
				<th class='titulos3'>Fecha Transacci&oacute;n</th>
				<th class='titulos3'>Funcionario</th>
				<th class='titulos3'>Transacci&oacute;n</th>
				<th class='titulos3'>Imagen</th>
			</tr>

                <?php
                if ($rs && ! $rs->EOF) {
                    $css = 1;
                    while ($arr = $rs->FetchRow()) {
                        ?>

                    <tr class='listado<?php $css ?>'
				style="font: normal 11px Arial;">
				<td> <?php echo $arr[0]; ?> </td>
				<td> <?php echo $arr[1]; ?> </td>
				<td> <?php echo $arr[2]; ?> </td>
				<td> <?php
                        if ($tipo == 'a') { // anexo a radicado
                            $cad = "bodega/" . substr($radicado, 0, 4) . "/" . substr($radicado, 4, 3) . "/docs/" . $arr[3];
                            $enlace = $arr[3];
                        }
                        if ($tipo == 'e') { // expediente
                            $cad = "bodega/" . $arr[3];
                            $enlace = explode("/", $arr[3]);
                            $enlace = $enlace[count($enlace) - 1];
                        }
                        if ($tipo == 'r') { // radicado
                            $cad = "bodega/" . $arr[3];
                            $enlace = explode("/", $arr[3]);
                            $enlace = $enlace[count($enlace) - 1];
                        }
                        if ($tipo == 'c') { // certimail
                            $cad = "bodega/" . $arr[3];
                            $enlace = explode("/", $arr[3]);
                            $enlace = $enlace[count($enlace) - 1];
                        }
                        echo "<a href='" . $cad . "' target='_blank'>" . $enlace . "</a>";
                        ?> 
                        </td>
			</tr>

                    <?php
                        $css = ($css == 1) ? 2 : 1;
                    }
                }
                ?>

            </Table>
		<Table align="center">
			<tr>
				<td><input align="center" name="button" type="button"
					class="botones_largo" onClick="window.close()" value="CERRAR"></td>
			</tr>
		</Table>
	</Form>
</BODY>
</HTML>