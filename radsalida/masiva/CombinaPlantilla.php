<?php

class CombinaPlantilla {

    public function odt2merge($filename, $arrVar) {
        return $this->mergeZippedXML($filename, $arrVar, array("content.xml"));
    }

    public function docx2merge($filename, $arrVar) {
        return $this->mergeZippedXML($filename, $arrVar, array("word/document.xml","word/header2.xml"));
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
     * @param string $plantilla. Ruta física donde se encuentra la plantilla a combinar.
     * @param array $arrVar. Vector con la infomación a combinar, array('var1'=>'valor1', 'var2'='valor2' ... 'n'=>'valorn')
     * @param int $delFile. Indica si se borra el archivo $plantilla al culminar la combinación. 
     * @param array<string> $dataFiles. ARchivos donde buscar y reemplazar.
     * @return type
     */
    private function mergeZippedXML($plantilla, $arrVar, array $dataFiles) {
        // Create new ZIP archive
        $zip = new ZipArchive;
        // Open received archive file
        if (true === $zip->open($plantilla)) {
			foreach($dataFiles as $dataFile) {
				// If done, search for the data file in the archive
				if (($index = $zip->locateName($dataFile)) !== false) {
					// If found, read it to the string
					$data = $zip->getFromIndex($index);
					//loop content file for search and replace data
					foreach ($arrVar as $key => $value) {
						if ($key !== 'id') {
							$value = iconv($this->codificacion($value),'UTF-8',$value);
							$data = str_replace($key, $value, $data);
						}
					}
					$zip->addFromString($dataFile, $data);
				}
			}
        }
        // Close archive file
        $zip->close();
    }
    
    public function docx2search($filename, $arrVar) {
        return $this->searchInXML($filename, $arrVar, array("word/document.xml","word/header2.xml"));
    }
    
    private function searchInXML($plantilla, $arrVar, array $dataFiles) {
        // Create new ZIP archive
        $zip = new ZipArchive;
        // Open received archive file
        $response = false;
        if (true === $zip->open($plantilla)) {
            foreach($dataFiles as $dataFile) {
                // If done, search for the data file in the archive
                if (($index = $zip->locateName($dataFile)) !== false) {
                    // If found, read it to the string
                    $data = $zip->getFromIndex($index);
                    //loop content file for search and replace data
                    foreach ($arrVar as $key => $value) {
                        if ($key !== 'id') {
                            if (strpos($data, $value))
                            {
                                $response = true;
                            }
                        }
                    }
                }
            }
        }
        // Close archive file
        $zip->close();
        
        return $response;
    }

    function codificacion($texto)
	{ 
	     $c = 0;
	     $ascii = true;
	     for ($i = 0;$i<strlen($texto);$i++) 
	     {
	         $byte = ord($texto[$i]);
	         if ($c>0) 
	         {
	             if (($byte>>6) != 0x2)
	             {
	                 return 'ISO-8859-1';
	             }
	             else
	             {
	                 $c--;
	             }
	         } 
	         elseif ($byte&0x80) 
	         {
	             $ascii = false;
	             if (($byte>>5) == 0x6)
	             {
	                  $c = 1;
	             }
	             elseif (($byte>>4) == 0xE)
	             {
	                 $c = 2;
	             }
	             elseif (($byte>>3) == 0x1E)
	             {
	                 $c = 3;
	             }
	             else 
	             {
	                 return 'ISO-8859-1';
	             }
	         }
	     }
	     return ($ascii) ? 'ISO-8859-1' : 'UTF-8';
	}
}

?>
