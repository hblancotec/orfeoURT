<?php

class CombinaPlantilla {

    public function odt2merge($filename, $arrVar) {
        return $this->mergeZippedXML($filename, $arrVar, "content.xml");
    }

    public function docx2merge($filename, $arrVar) {
        return $this->mergeZippedXML($filename, $arrVar, "word/document.xml");
    }

    public function xml2merge($stringXML, $arrVar) {
        $xmlDataFile = simplexml_load_string($stringXML);
        $xml = DOMDocument::loadXML($stringXML, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
        //loop content file for search and replace data
        foreach ($arrVar as $key => $value) {
            $stringXML = str_replace($key, $value, $stringXML);
        }
        return $stringXML;
    }

    /**
     * 
     * @param string $plantilla. Ruta fisica donde se encuentra la plantilla a combinar.
     * @param array $arrVar. Vector con la infomacion a combinar, array('var1'=>'valor1', 'var2'='valor2' ... 'n'=>'valorn')
     * @param int $delFile. Indica si se borra el archivo $plantilla al culminar la combinacion. 
     * @param string $dataFile. 
     * @return 
     */
    private function mergeZippedXML($plantilla, $arrVar, $dataFile) {
        // Create new ZIP archive
        $zip = new ZipArchive;
        // Open received archive file
        if (true === $zip->open($plantilla)) {
            // If done, search for the data file in the archive
            if (($index = $zip->locateName($dataFile)) !== false) {
                // If found, read it to the string
                $data = $zip->getFromIndex($index);
                //loop content file for search and replace data
                foreach ($arrVar as $key => $value) {
                    if ($key !== 'id')
                        $data = str_replace($key, $value, $data);
                }
                $zip->addFromString($dataFile, $data);
            }
        }
        // Close archive file
        $zip->close();
    }

}

?>
