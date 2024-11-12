<?php
session_start();

$directorio = "../bodega/tmp";
require "funciones.php";

//creamos vector con las extensiones permitidas
$vectorExt = array();
require 'scripts/clases/class.sqlsrv.php';
$objMssql = new SQLSRV("..");
$rs = $objMssql->consulta("SELECT ANEX_TIPO_EXT FROM ANEXOS_TIPO WHERE ANEX_TIPO_PQR=1 ORDER BY ANEX_TIPO_EXT");
while ($vector = $objMssql->fetch_assoc($rs)) {
    $vectorExt[] = $vector['ANEX_TIPO_EXT'];
}

if (isset($_SESSION)) {

	if (!empty($_POST['cmbMcpio']) and !empty($_POST['cmbDpto']) ) {
        $cmbMcpio = "170-" . $_POST['cmbDpto'] . "-" . $_POST['cmbMcpio'];
        $Internacionalizacion = "1-" . $cmbMcpio;
    } else
        $Internacionalizacion = null;

    $FechaOficioRadicado = date('d-m-Y H:i:s');

    $CodPostal = empty($_POST['txtCodPostal']) ? str_pad($_POST['cmbDpto'], 2, "0", STR_PAD_LEFT) . "0000" : $txtCodPostal;

    $radicado = CrearRadicado($_SESSION['login'], 1, $_POST['txtNombre'], $_POST['txtApellido'], $_POST['tipoDoc'], $_POST['txtDocumento'], $_POST['txtDireccion'], $_POST['txtTelefono'], $_POST['txtCorreo'], $Internacionalizacion, $_POST['cmbSolictud'], $_POST['cmbTema'], $_POST['txtAsunto'] . " - " . $_POST['txtComentario'], $FechaOficioRadicado, $_POST['mrecCodi'], $CodPostal, $_POST['cmbDependencia'], $_POST['cmbUsuario']);

    if (is_array($radicado)) {
        $mensaje = $radicado;
    } else {
        //Tipificamos el radicado "automaticamente"
        $isqlTRD = "SELECT SGD_MRD_CODIGO FROM SGD_MRD_MATRIRD WHERE DEPE_CODI = ".$_SESSION['dependencia']." AND SGD_TPR_CODIGO = ".$_POST['cmbSolictud']." AND SGD_MRD_ESTA=1";
        $rs = $objMssql->consulta($isqlTRD);
        $row = $objMssql->fetch_assoc($rs);
        $codiTRD = $row['SGD_MRD_CODIGO'];
        include_once "../class_control/TipoDocumental.php";
        include_once "../include/db/ConnectionHandler.php";
        $db = new ConnectionHandler("../");
        $trd = new TipoDocumental($db);
        $radicados = $trd->insertarTRD(array($codiTRD),$codiTRD,$radicado,$_SESSION['dependencia'], $_SESSION['codusuario']);
        
        // Creamos el PDF soporte del radicado
        require "crea_pdf.php";
        
        //Agregamos registro de Forma de Envio preferida por el peticionario.
        $addIR = AgregarMetadataPQR($radicado, $_POST['tipoResp'], $_POST['cmbRazas'], 1);
        //Subimos los anexos. Se necesitan para construir el PDF del radicado. 
        $hayAlgunError = false;
        if (isset($_FILES['MyFileUpload'])) {
            foreach ($_FILES['MyFileUpload']['error'] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    //echo "$error_codes[$error]";
                    //validar que $_FILES["MyFileUpload"]["name"][$key] su extension sea valida para PQR.
                    if (in_array(pathinfo(strtolower($_FILES["MyFileUpload"]["name"][$key]), PATHINFO_EXTENSION), $vectorExt)) {
                        if (!move_uploaded_file($_FILES["MyFileUpload"]["tmp_name"][$key], "$directorio/$radicado" . $_FILES["MyFileUpload"]["name"][$key])) {
                            echo ("Ocurrio un problema al intentar subir el archivo.");
                            if (!$hayAlgunError) {
                                $hayAlgunError = true;
                                $archivosConError[] = $_FILES["MyFileUpload"]["name"][$key];
                            }
                        }
                    } else {
                        $hayAlgunError = true;
                        $archivosConError[] = $_FILES["MyFileUpload"]["name"][$key];
                    }
                } else {
                    $hayAlgunError = true;
                }
            }
        }

        $listaAnexos = find_all_files("$directorio/", $radicado);
        
        $mensaje = "<br>Su solicitud ha sido radicada con el n&uacute;mero ";

        $actualizaImagenPpalRadicado = anexarArchivos("$directorio/$radicado.pdf", $radicado, $_SESSION['login'], true);
        
        if (is_array($actualizaImagenPpalRadicado)) {
            $mensaje = "$radicado. Se hayaron los sgtes errores: " . implode("<br/>", $actualizaImagenPpalRadicado);
        } else {

            if (strlen($radicado) == 14 ) {
                $ruta = "../bodega/" . substr($radicado, 0, 4) . "/" . substr($radicado, 4, 3) . "/";
            }
            elseif (strlen($radicado) == 15 ) {
                $ruta = "../bodega/" . substr($radicado, 0, 4) . "/" . ltrim(substr($radicado, 4, 4), "0") . "/";
            }
            
            $mensaje .= "<a href='" . $ruta . "$radicado.pdf' target='_blank'>$radicado</a>.<br><br> " .
            "<center><a href='" . $ruta . "$radicado.pdf' target='_blank'><img alt='Descargar' src='images/descarga2.gif' height='45' border='0'></a></center><br>" .
                    "Necesita un visor de archivos PDF para poder visualizar el documento.";
            if (is_array($listaAnexos)) {
                for ($i = 0; $i < count($listaAnexos); $i++) {
                    anexarArchivos($listaAnexos[$i], $radicado, $_SESSION['login'], false);
                }
            }
        }
        
        enviarCorreo($radicado);
    }
} else {
    die("Archivo accesado incorrectamente");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title></title>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <link rel="stylesheet" href="../estilos/orfeo.css" type="text/css"/>
    </head>
    <body>
        <table align="left" class="" width="380" border="1">
            <tr class="titulos4" align="center"><td><b>Estimado usuario:</b></td></tr>
            <tr class='listado2' align="justify"><td>
                    <?php
                    if (isset($mensaje))
                        echo (is_array($mensaje)) ? var_dump($mensaje) : $mensaje;
                    ?>
                    <br/>
                </td></tr>
            <tr align="center"><td>
                    <a href="index.php" class="alarmas">
                        volver
                    </a>
                </td></tr>
        </table>
    </body>
</html>