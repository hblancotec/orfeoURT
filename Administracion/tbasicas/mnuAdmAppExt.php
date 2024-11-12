<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz = "../..";

if (!isset($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";
    $phpsession = session_name() . "=" . session_id();
?>
<html>
    <head>
        <title>Adm Aplicativos</title>
        <link rel="stylesheet" href="../../estilos/orfeo.css">
    </head>
    <body>
        <table width="31%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
            <tr bordercolor="#FFFFFF">
                <td colspan="2" class="titulos4"><div align="center"><strong>M&Oacute;DULO DE ADMINISTRACI&Oacute;N SERVICIOS WEB</strong></div></td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2" width="48%">
                    <a href='./adm_eaplicativos.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">
		1. Administrador Aplicativos Externos</a>
                </td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2" width="48%">
                    <a href='./AppExtTbasicas.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">
		2. Tablas B&aacute;sicas</a>
                </td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2" width="48%">
                    <a href='./campos_homologos_app.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">
		3. Homologación de Campos</a>
                </td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2" width="48%">
                    <a href='./accionesExternas.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">
		4. Tabla de acciones Externas</a>
                </td>
            </tr>
        </table>
    </body>
</html>