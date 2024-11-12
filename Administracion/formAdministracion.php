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

if ($_SESSION['usua_admin_sistema'] != 1) {
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz = "..";
if (!isset($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";
$phpsession = session_name() . "=" . session_id();
?>
<html>
    <head>
        <title>Documento  sin t&iacute;tulo</title>
        <link rel="stylesheet" href="../estilos/orfeo.css">
    </head>
    <body>
        <table width="50%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
            <tr bordercolor="#FFFFFF">
                <td colspan="3" class="titulos4"><div align="center"><strong>M&Oacute;DULO DE ADMINISTRACI&Oacute;N</strong></div></td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2" width="33%">
                    <a href='usuario/mnuUsuarios.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">1. USUARIOS Y PERFILES</a> <br>
                </td>
                <td align="center" class="listado2" width="33%">
                    <a href='tbasicas/adm_dependencias.php?<?= $phpsession ?>' target='mainFrame' class="vinculos">2. DEPENDENCIAS</a>
                </td>
                <td align="center" class="listado2" width="34%">
                	<a href="tbasicas/adm_alertas.php" class="vinculos" target='mainFrame'>3. ALERTAS</a>
                </td>
            </tr>
            <tr bordercolor="#FFFFFF">
				<td align="center" class="listado2"><a href="tbasicas/adm_mime.php"  class="vinculos" target='mainFrame'>4. TIPOS DE ARCHIVOS (Mime)</a> </td>
				<td align="center" class="listado2"><a href="tbasicas/dias_fest_index.php" class="vinculos" target='mainFrame'>5. MANEJO DE DIAS FESTIVOS</a></td>
                <td align="center" class="listado2"><a href="tbasicas/adm_trad.php?krd=<?= $krd ?>" class="vinculos" target='mainFrame'>6. TIPOS DE RADICACI&Oacute;N</a></td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2"><a href="tbasicas/adm_paises.php" class="vinculos" target='mainFrame'>7. PA&Iacute;SES</a></td>
                <td align="center" class="listado2"><a href="tbasicas/adm_dptos.php" class="vinculos" target='mainFrame'>8. DEPARTAMENTOS</a></td>
				<td align="center" class="listado2"><a href="tbasicas/adm_mcpios.php" class="vinculos" target='mainFrame'>9. MUNICIPIOS</a></td>
            </tr>
            <tr bordercolor="#FFFFFF">
            	<td align="center" class="listado2"><a href="tbasicas/adm_tarifas.php" class="vinculos" target='mainFrame'>10. TARIFAS</a></td>
                <td align="center" class="listado2"><a href="tbasicas/adm_contactos.php" class="vinculos" target='mainFrame'>11. CONTACTOS</a></td>
                <td align="center" class="listado2"><a href="tbasicas/adm_esp.php?krd=<?= $krd ?>&<?= $phpsession ?>" class="vinculos" target='mainFrame'>12. ESP</a></td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2">
                    <a href="adminDirSec/" class="vinculos" target='mainFrame'>13. CREAR DIRECTORIOS E INICIALIZAR SECUENCIAS</a>
                </td>
                <td align="center" class="listado2">
                    <a href='./tbasicas/adm_temas.php?<?= $phpsession ?>' target='mainFrame' class="vinculos">14. TEMAS</a>
                </td>
                <td align="center" class="listado2">
                    <a href='./tbasicas/adm_tsencillas.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">15. TABLAS SENCILLAS</a>
                </td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2">
                    <a href='./tbasicas/adm_tramites.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">16. TR&Aacute;MITES</a>
                </td>
                <td align="center" class="listado2">
                    <a href='./tbasicas/adm_plantillas.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">17. PLANTILLAS</a>
                </td>
				<td align="center" class="listado2">
                    <a href='../ConsultaLog/index.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">18. LOG DE CONSULTAS</a>
                </td>
            </tr>
            <tr bordercolor="#FFFFFF">
                <td align="center" class="listado2">
                    <a href='./tbasicas/adm_mediosRecepcion.php?<?= $phpsession ?>&krd=<?= $krd ?>' target='mainFrame' class="vinculos">19. MEDIOS DE RECEPCI&Oacute;N Y ENV&Iacute;O</a>
                </td>
                <td align="center" class="listado2">
                    <a href='/query.php' target='mainFrame' class="vinculos">20. SENTENCIAS</a>
                </td>
				<td align="center" class="listado2">
                    <a href='' target='mainFrame' class="vinculos"></a>
                </td>
            </tr>
        </table>
    </body>
</html>