<?php
set_time_limit(0);
session_start();
$ruta_raiz = "../..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once("$ruta_raiz/class_control/CombinaError.php");
if (!isset($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";
(!$db) ? $conexion = new ConnectionHandler($ruta_raiz) : $conexion = $db;
//$conexion->conn->debug = true;
$conexion->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$hora = date("H") . "_" . date("i") . "_" . date("s");
// var que almacena el dia de la fecha
$ddate = date('d');
// var que almacena el mes de la fecha
$mdate = date('m');
// var que almacena el ano de la fecha
$adate = date('Y');
// var que almacena  la fecha formateada
$fecha = $adate . "_" . $mdate . "_" . $ddate;

//Almacena la extesion del archivo entrante
$extension = trim(substr($archivoPlantilla_name, strpos($archivoPlantilla_name, ".") + 1, strlen($archivoPlantilla_name) - strpos($archivoPlantilla_name, ".")));
//var que almacena el nombre que tendr� la pantilla
$arcPlantilla = $usua_doc . "_" . $fecha . "_" . $hora . ".$extension";
//var que almacena el nombre que tendr� el CSV
$arcCsv = $usua_doc . "_" . $fecha . "_" . $hora . ".csv";
//var que almacena el path hacia el PDF final
$arcPDF = "$ruta_raiz/bodega/masiva/" . "tmp_" . $usua_doc . "_" . $fecha . "_" . $hora . ".pdf";
$phpsession = session_name() . "=" . session_id();

//var que almacena los par�metros de sesion
$params = $phpsession . "&krd=$krd&dependencia=$dependencia&codiTRD=$codiTRD&depe_codi_territorial=$depe_codi_territorial&usua_nomb=$usua_nomb&tipo=$tipo&"
        . "depe_nomb=$depe_nomb&usua_doc=$usua_doc&codusuario=$codusuario";

//Función que calcula el tiempo transcurrido
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}
?>
<html>
    <head>
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" href="../../estilos/orfeo.css">
    <script>
        /**
         * Confirma la generacion definitiva
         */
        function enviar() {

            if ( confirm ('Confirma la generacion de un radicado por cada registro del archivo CSV?'))
                document.formDefinitivo.submit();
        }

        function regresar() {
            document.formDefinitivo.action="menu_masiva.php?"+'<?= $params ?>';
            document.formDefinitivo.submit();
        }

        /**
         * Env�a el formulario, a consultar divipola
         */
        function divipola() {
            document.formDefinitivo.action="consulta_depmuni.php?"+ document.formDefinitivo.params.value;
            document.formDefinitivo.submit();
        }

        /**
         * Cancela el proceso y devuelve el control a menu masiva
         */
        function cancelar(){
            document.formDefinitivo.action='menu_masiva.php?'+ document.formDefinitivo.params.value;
            document.formDefinitivo.submit();
        }

        function abrirArchivoaux(url){
            nombreventana='Documento';
            window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
            return;
        }


    </script>
</head>
<body>
    <form action="adjuntar_defint.php?<?= $params ?>" method="post" enctype="multipart/form-data" name="formDefinitivo">
        <input type=hidden name=pNodo value='<?= $pNodo ?>'>
        <input type=hidden name=codProceso value='<?= $codProceso ?>'>
        <input type=hidden name=tipoRad value='<?= $tipoRad ?>'>
        <?php
        $time_start = microtime_float();

        if ($archivoPlantilla_size >= 10000000 || $archivoCsv_size >= 10000000) {
            echo "el tama&nacute;o de los archivos no es correcto. <br><br><table><tr><td><li>se permiten archivos de 100 Kb m&aacute;ximo.</td></tr></table>";
        } else {

            $dirActual = getcwd();
            if (!copy($archivoPlantilla, "../../bodega/masiva/" . $arcPlantilla)) {
                echo "error al copiar Plantilla: $archivoPlantilla en ../../bodega/masiva/" . $arcPlantilla . "<br/>";
            } elseif (!copy($archivoCsv, "../../bodega/masiva/" . $arcCsv)) {
                echo "error al copiar CSV: $archivoCsv en ../../bodega/masiva/" . $arcCsv . "<br/>";
                ;
            } else {
                echo "<center><span class=etextomenu align=left>";
                echo "<TABLE border=0 width 60% cellpadding='0' cellspacing='5' class='borde_tab'>
      <TR ALIGN=LEFT><TD width=20% class='titulos2' >DEPENDENCIA :</td><td class='listado2'> " . $_SESSION['depe_nomb'] . "</TD>	<TR ALIGN=LEFT><TD class='titulos2' >USUARIO RESPONSABLE :</td><td class='listado2'>" . $_SESSION['usua_nomb'] . "</TD>
      <TR ALIGN=LEFT><TD class='titulos2' >FECHA :</td><td class='listado2'>" . date("d-m-Y - h:mi:s") . "</TD></TR></TABLE>";
                require "$ruta_raiz/jhrtf/jhrtf.php";
                $ano = date("Y");
                $archivoFinal = "./bodega/$ano/$dependencia/docs/$usua_doc" . "_$fecha" . "_$hora" . ".". $extension;
                $archivoTmp = "./bodega/masiva/tmp_$usua_doc" . "_$fecha" . "_$hora" . $extension;

                $ruta_raiz = "../..";
                $definitivo = "no";
                $nombrebase = ORFEOCFG . "\\bodega\\masiva\\" . "tmp_" . $usua_doc;
                $rutaPlantilla = ORFEOCFG . "\\bodega\\masiva\\" . $arcPlantilla;
                $rutabase = "tmp_" . $usua_doc . "_" . $fecha . "_" . $hora . ".zip";
                //$rutabase		 = $archivoFinal;
                $rutaZipFile = ORFEOCFG . "\\bodega\\masiva\\" . $rutabase;

                $rutabasedoc = "tmp_" . $usua_doc . "_" . $fecha . "_" . $hora . "." . $extension;
                $rutaDocFile = ORFEOCFG . "\\bodega\\masiva\\" . $rutabasedoc;

                $rutaZipFileWA = $ruta_raiz . "/bodega/masiva/" . $rutabase;
                $rutaZipFileW = "'" . $ruta_raiz . "/bodega/masiva/" . $rutabase . "'";

                $archInsumo = "tmp_" . $usua_doc . "_" . $fecha . "_" . $hora;

                $fp = fopen("$ruta_raiz/bodega/masiva/$archInsumo", 'w');
                if ($fp) {
                    fputs($fp, "plantilla=$arcPlantilla" . "\n");
                    fputs($fp, "csv=$arcCsv" . "\n");
                    fputs($fp, "archFinal=$rutaZipFile" . "\n");
                    fputs($fp, "rutaBase=$rutabase" . "\n");
                    fputs($fp, "rutaZipFileW=$rutaZipFileWA" . "\n");
                    fputs($fp, "archTmp=$archivoTmp" . "\n");
                    fclose($fp);
                } else {
                    exit("No hay acceso para crear el archivo $ruta_raiz/bodega/masiva/$archInsumo");
                }

                // Se crea el objeto de masiva
                $masiva = new jhrtf($archInsumo, $ruta_raiz, $arcPDF, $conexion);
                $masiva->cargar_csv();
                $masiva->validarArchs();
                if ($masiva->hayError()) {
                    $masiva->mostrarError();
                } else {
                    $masiva->setTipoDocto($tipo);
                    $_SESSION["masiva"] = $masiva;

                    // nombre de archivo de salida
                    $nombrebaseF = ORFEOCFG . "\\bodega\\" . $ano . "\\" . $dependencia . "\\docs\\";
                    $nombreF = $fecha . "_" . $hora . "_final" . "." . $extension;
                    //$nombreF       = $fecha . "_" . $hora . "_final" . ".doc";
                    $archivoFinalF = $nombrebaseF . $nombreF;
                    $linkfile = "../../bodega/" . $ano . "/" . $dependencia . "/docs/" . $nombreF;
                    //echo "Masiva linkfile: ". $linkfile . "<br/>";
                    $masiva->linkfile = $linkfile;

                    echo "<center><span class=info><br>Se ha realizado la combinaci&oacute;n de correspondencia como una prueba.<br> ";
                    $masiva->combinar_csv($dependencia, $codusuario, $usua_doc, $usua_nomb, $depe_codi_territorial, $codiTRD, $tipoRad);
                    error_reporting(0);
                    include("$ruta_raiz/config.php");
                    //El include del servlet hace que se altere el valor de la variable  $estadoTransaccion como 0 si se pudo procesar el documento, -1 de lo
                    // contrario
                    //$estadoTransaccion = 0;
                    $masiva->wsdlOffice = $wsdlOffice;
                    $vecArch2Comprimir = $masiva->generaOfficeFiles($nombrebase, $rutaPlantilla, $dependencia, $archivoFinalF, True);
                    if (count($vecArch2Comprimir) > 0) {
                        $zip = new ZipArchive;
                        $zipFile = "tmp/" . date('YmdHis') . ".zip";
                        if ($zip->open(BODEGAPATH . $zipFile, ZipArchive::CREATE) === TRUE) {
                            foreach ($vecArch2Comprimir as $key => $value) {
                                $zip->addFile($value, basename($value));
                            }
                            $zip->close();
                            echo ("<BR><span class='info'> Por favor guarde el archivo y verifique que los datos de combinacion  esten correctos <br>");
                            echo ("<a class='vinculos' href=javascript:abrirArchivoaux('" . BODEGAURL . $zipFile . "')>Guardar Archivo </a></span> ");
                            echo ("<br><br>");
                            echo( "<br><input name='enviaDef' type='button'  class='botones' id='envia22'  onClick='enviar()' value='Generar Definitivo'>");
                            echo( "<input name='cancel' type='button'  class='botones' id='envia22'  onClick='cancelar()' value='Cancelar'>");
                        } else {
                            echo 'failed';
                        }
                    }
                }
            }
            //Contabilizamos tiempo final
            $time_end = microtime_float();
            $time = $time_end - $time_start;
            echo "<br><b>Se demor&oacute;: $time segundos la Operaci&oacute;n total.</b>";
        }
        ?>
        <input name='archivo' type='hidden' value='<?= $archivoFinal ?>'>
        <input name='arcPDF' type='hidden'  value='<?= $arcPDF ?>'>
        <input name='tipoRad' type='hidden' value='<?= $tipoRad ?>'>
        <input name='pNodo' type='hidden' value='<?= $pNodo ?>'>
        <input name='params' type='hidden'  value="<?= $params ?>">
        <input name='archInsumo' type='hidden'  value="<?= $archInsumo ?>">
        <input name='extension' type='hidden'  value="<?= $extension ?>">
        <input name='arcPlantilla' type='hidden' value='<?= $arcPlantilla ?>'>

    </form>
</body>
</html>
