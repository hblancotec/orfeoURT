<?php

require_once 'libs/CombinaPlantilla.php';

class radicacionmasiva_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function uploadFileData() {
        try {
        	$_FILES['FfdDatosFrmRadicacionmasiva']['name'] = str_replace(" ", "", $_FILES['FfdDatosFrmRadicacionmasiva']['name']);
            $this->cargarArchivoMasiva($_FILES['FfdDatosFrmRadicacionmasiva'], array('csv', 'xls', 'xlsx', 'ods'));
            $tmp = json_decode($this->setFileDataEnGrilla($_FILES['FfdDatosFrmRadicacionmasiva']['name']));
            if (isset($tmp->success) && ($tmp->success == FALSE))
                throw new Exception($tmp->message);
            $this->cargarArchivoMasiva($_FILES['FfdPlantillaFrmRadicacionmasiva'], array('odt', 'docx'));
            $response = array('success' => TRUE, 'message' => "Archivos cargados exitosamente.");
        } catch (Exception $exce) {
            $response = array('success' => FALSE, 'message' => $exce->getMessage());
        }
        return json_encode($response);
    }

    public function setFileDataEnGrilla($archivo) {
        $archivo = BODEGAPATH . 'tmp/' . $archivo;
        $ext = pathinfo($archivo, PATHINFO_EXTENSION);
        try {
            switch (strtolower($ext)) {
                case 'xml': {
                        $response = xml2Json($archivo);
                    }break;
                case 'csv': {
                        $response = $this->csv2associativeArray($archivo, ";");
                        //$response = $this->hojaDeCalculo($archivo, ";");
                    }break;
                default: {
                        $response = array('success' => FALSE, 'message' => "Extension " . strtolower($ext) . " no permitida.");
                    } break;
            }
        } catch (Exception $exc) {
            $response = array('success' => FALSE, 'message' => $exc->getMessage());
        }
        return json_encode($response);
    }

    public function mergeDocDataWCF() {
        $fileOrg = str_replace("\\", "/", BODEGAPATH . "tmp" . DIRECTORY_SEPARATOR . $_POST['fileDoc']);
        $path_parts = pathinfo($fileOrg);
        $extension = $path_parts['extension'];
        $arrData = json_decode($_POST['gridRows']);
        $fileDst = $path_parts['filename'] . "_" . uniqid() . "." . $path_parts['extension'];
        $fileDst1 = BODEGAPATH . "tmp" . DIRECTORY_SEPARATOR . $fileDst;
        $fileDst2 = BODEGAURL . "tmp/" . $fileDst;
        $objCombinaPlantilla = new CombinaPlantilla();
        switch ($extension) {
            case 'docx': {
                    $client = new SoapClient(URLOFFICEWCF, array(
                        'trace' => 1,
                        'exceptions' => true,
                        'cache_wsdl' => WSDL_CACHE_NONE,
                        'soap_version' => SOAP_1_1
                    ));
                    foreach ($arrData as $k1 => $row) {
                        $cad = array();
                        foreach ($row as $k2 => $var) {
                            if ($k2 !== 'id') {
                                $cad[] = $k2;
                                $cad[] = $var;
                            }
                        }
                        $rowSend[] = $cad;
                    }
                    $arregloDatos = array('rutaOrigen' => $fileOrg,
                        'rutaDestino' => $fileDst1,
                        'variables' => $rowSend
                    );
                    $result = $client->combinaPlantilla($arregloDatos);
                    switch ($result->combinaPlantillaResult) {
                        case 1: {
                                $response = array(
                                    'success' => true,
                                    'data' => $fileDst2,
                                    'msg' => $fileDst2
                                );
                            } break;
                        default: {
                                $response = array(
                                    'success' => false,
                                    'data' => '',
                                    'msg' => ''
                                );
                            } break;
                    }
                }break;
            case 'odt': {
                    $response = array(
                        'success' => false,
                        'data' => 'Lógica no implementada.',
                        'msg' => ''
                    );
                }break;
            case 'xml': {
                    $response = array(
                        'success' => false,
                        'data' => 'Lógica no implementada.',
                        'msg' => ''
                    );
                }break;
            default: {
                    $response = array(
                        'success' => false,
                        'data' => array('name' => $file_name, 'size' => $file_size),
                        'msg' => 'Extensión no permitida'
                    );
                }break;
        }
        return json_encode($response);
    }

    public function mergeDocDataZip() {
        $response = array('success' => FALSE, 'message' => "Respuesta desde el modelo backEnd.");
        $fileOrg = BODEGAPATH . "tmp" . DIRECTORY_SEPARATOR . $_POST['fileDoc'];
        $path_parts = pathinfo($fileOrg);
        $extension = $path_parts['extension'];
        $arrData = json_decode($_POST['gridRows']);
        $dirTmp = "tmp" . json_decode($_POST['dirTmp']);
        $v1 = '*RAD_S*';
        $fileDst = BODEGAPATH . "tmp" . DIRECTORY_SEPARATOR . $dirTmp . DIRECTORY_SEPARATOR . $arrData->{$v1} . "." . $extension;

        $objCombinaPlantilla = new CombinaPlantilla();
        switch ($extension) {
            case 'docx': {
                    if (!file_exists(BODEGAPATH . "tmp" . DIRECTORY_SEPARATOR . $dirTmp))
                        mkdir(BODEGAPATH . "tmp" . DIRECTORY_SEPARATOR . $dirTmp);
                    if (copy($fileOrg, $fileDst)) {
                        $RtaData = $objCombinaPlantilla->docx2merge($fileDst, $arrData);
                        $response = array(
                            'success' => true,
                            'data' => '',
                            'msg' => 'El archivo es ' . basename($fileDst)
                        );
                    }
                }break;
            case 'odt': {
                    $RtaData = $objCombinaPlantilla->odt2merge($fileDoc, $arrData);
                    $response = array(
                        'success' => true,
                        'data' => array('name' => $file_name, 'size' => $file_size),
                        'msg' => 'El archivo es ' . $xmlData
                    );
                }break;
            case 'xml': {
                    $RtaData = $objCombinaPlantilla->docx2merge($fileDoc, $arrData);
                    $response = array(
                        'success' => false,
                        'data' => array('name' => $file_name, 'size' => $file_size),
                        'msg' => 'Lógica no implementada para archivo xml'
                    );
                }break;
            default: {
                    $response = array(
                        'success' => false,
                        'data' => array('name' => $file_name, 'size' => $file_size),
                        'msg' => 'Extensión no permitida'
                    );
                }break;
        }
        return json_encode($response);
    }

    public function downloadZipMasiva() {
        $dirTmp = BODEGAPATH . "tmp" . DIRECTORY_SEPARATOR . "tmp" . json_decode($_POST['dirTmp']);
        $dirTmp2 = BODEGAURL . "tmp/" . "tmp" . json_decode($_POST['dirTmp']);
        $zip = new ZipArchive;

        if ($zip->open($dirTmp . '.zip', ZIPARCHIVE::CREATE) === true) {

            $itDir = new RecursiveDirectoryIterator($dirTmp);
            $itFil = new RecursiveIteratorIterator($itDir);
            foreach ($itFil as $file => $value) {
                if (substr($file, -1) !== '.') {
                    $fz = str_replace("\\", "/", $file);
                    $zip->addFile($fz, basename($fz));
                }
            }
            $zip->close();
            foreach ($itFil as $key => $value) {
                unlink($key);
            }
            rmdir($dirTmp);

//            header('Content-type: "application/zip"');
//            header('Content-Disposition: attachment; filename="' . $dirTmp2 . '.zip' . '"');
//            header('Cache-Control: max-age=0');
//            readfile($dirTmp . '.zip');
//            unlink($dirTmp . '.zip');

            $response = array('success' => true,
                'data' => $dirTmp2 . ".zip",
                'msg' => 'Archivo zip creado exitosamente'
            );
        } else {
            $response = array('success' => FALSE,
                'data' => '',
                'msg' => 'Imposible crear archivo zip temporal.'
            );
        }
        echo json_encode($response);
    }

    public function uploadZipPlantillas() {
        try {

            $client = new SoapClient(URLOFFICEWCF, array(
                'trace' => 1,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'soap_version' => SOAP_1_1
            ));
            //persistimos el zip de plantillas. Con ello validamos que no hubo error en carga y que es un archivo .zip.
            $this->cargarArchivoMasiva($_FILES['FfdZipPlantillaFrmRadicacionmasiva'], array('zip'));
            $archZipPlan = BODEGAPATH . 'tmp'. DIRECTORY_SEPARATOR . $_FILES['FfdZipPlantillaFrmRadicacionmasiva']['name'];
            $fileZipPdfs = 'tmp'. DIRECTORY_SEPARATOR . uniqid() . ".zip";
            $archZipPdfs = BODEGAPATH . $fileZipPdfs;
            $archUrlZipPdfs = BODEGAURL . $fileZipPdfs;
            // abrimos el zip de las plantillas
            $zipPlan = zip_open($archZipPlan);
            // validamos que al abrirlo esté bien... puede venir con contraseña, etc y retorna un código de de error. 
            if (is_resource($zipPlan)) {
                //creamos zip para ir metiendo los pdf creados.
                $zipPdfs = new ZipArchive;
                $zipPdfs->open($archZipPdfs, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                while ($zip_entry = zip_read($zipPlan)) {
                    $archPlant = zip_entry_name($zip_entry);
                    $infoArchPlant = pathinfo($archPlant);

                    if (!empty($infoArchPlant['filename']) && in_array($infoArchPlant['extension'], array('docx', 'odt', 'xml')) && zip_entry_open($zipPlan, $zip_entry)) {
                        $contents = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                        $result = $client->convertirDocSerializadoToPdfSerializado(
                                array('archivoOrigen' => $contents,
                                    'nombreArchivo' => $infoArchPlant['basename']
                                )
                        );
                        if (!is_numeric($result->convertirDocSerializadoToPdfSerializadoResult)) {
                            $zipPdfs->addFromString($infoArchPlant['filename'] . '.pdf', $result->convertirDocSerializadoToPdfSerializadoResult);
                        }
                        zip_entry_close($zip_entry);
                    }
                }
                // Close archive file
                zip_close($zipPlan);
                $zipPdfs->close();
                //borramos el archivo zip original de plantillas.
                unlink($archZipPlan);
                $tmpArchZipPdfs = basename($archZipPdfs);
                //then send the headers to foce download the zip file
//                header("Content-type: application/zip");
//                header("Content-Disposition: attachment; filename=$tmpArchZipPdfs");
//                header("Pragma: no-cache");
//                header("Expires: 0");
//                readfile("$archZipPdfs");
//                exit;
                $response = array('success' => TRUE, 'message' => $archUrlZipPdfs);
            } else {
                $response = array('success' => FALSE, 'message' => 'Fallo al abrir el archivo zip.');
            }
        } catch (Exception $exc) {
            $response = array('success' => FALSE, 'message' => $exc->getMessage());
        }
        return json_encode($response);
    }

    private function hojaDeCalculo($file, $delimiter) {
        require "libs/PHPExcel/Classes/PHPExcel.php";
        require "libs/PHPExcel/Classes/PHPExcel/IOFactory.php";
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            echo "<br>The worksheet " . $worksheetTitle . " has ";
            echo $nrColumns . ' columns (A-' . $highestColumn . ') ';
            echo ' and ' . $highestRow . ' row.';
            echo '<br>Data: <table border="1"><tr>';
            for ($row = 1; $row <= $highestRow; ++$row) {
                echo '<tr>';
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                    echo '<td>' . $val . '<br>(Typ ' . $dataType . ')</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    // Function to convert CSV into associative array
    private function csv2associativeArray($file, $delimiter) {

        $csv = explode("\n", file_get_contents($file));
        $arrKey = explode($delimiter, $csv[0]);
        //validemos que exista UNA y solo UNA columna *RAD_S*
        if (count(array_keys($arrKey, "*RAD_S*", TRUE)) === 1) {
            foreach ($csv as $key => $filaCsv) {
                if (strlen($filaCsv) > 0) {
                    $fila = array();
                    $filaData = str_getcsv($filaCsv, "$delimiter"); //parse the rows 
                    foreach ($arrKey as $key => $value) {
                        $fila[trim($value)] = utf8_encode($filaData[$key]);
                    }
                    $reg[] = $fila;
                }
            }
            return array_slice($reg, 1);
        } else {
            throw new Exception("La columna *RAD_S* no existe o existe mas de una vez.");
        }
    }

    private function cargarArchivoMasiva($archivo, $arrExtPermitidas) {
        if ($archivo["error"] > 0) {
            $error = $archivo["error"];
            switch ($error) {
              case '1': $msgerr = 'El fichero subido excede la directiva upload_max_filesize';break;
              case '2': $msgerr = 'El fichero subido excede la directiva MAX_FILE_SIZE';break;
              case '3': $msgerr = 'El fichero fue sólo parcialmente subido';break;
              case '4': $msgerr = 'No se subió ningún fichero';break;
              case '6': $msgerr = 'Falta la carpeta temporal';break;
              case '7': $msgerr = 'No se pudo escribir el fichero en el disco';break;
              case '8': $msgerr = 'Una extensión de PHP detuvo la subida de ficheros';break;
              default : $msgerr = 'Error desconocido';break;
            }
            throw new Exception('Error al cargar el archivo ' . $archivo["name"] . ', mensaje: '. $msgerr . '.' );
        } else {
            //$file_name = str_replace(" ", "", $archivo["name"]);  //Antes quitabamos los espacios. El error era por el archivo de datos cuando tenia espacios en blanco. Se soluciona en el frontend - control fileupload del zip de datos.
            $ext = pathinfo($archivo["name"], PATHINFO_EXTENSION);
            if (!in_array($ext, $arrExtPermitidas)) {
                throw new Exception("Error al cargar el archivo. Extensión no permitida, solo " . implode(',', $arrExtPermitidas) . ".");
            } else {
                $destino = BODEGAPATH . 'tmp' . DIRECTORY_SEPARATOR;
                if (!move_uploaded_file($archivo["tmp_name"], $destino .  $archivo["name"])) {
                    throw new Exception("Hubo un error guardando el archivo " . $archivo['name'] . ".");
                }
            }
        }
    }

}

?>