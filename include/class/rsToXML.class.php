<?php

/**
 * Esta clase genera un archivo XML apartir de un recordset
 */
class rsToXML extends XMLWriter {
	
	/**
	 * Enter description here ...
	 * @param double $version
	 * @param string $charset
	 */
	function __construct($version = '1.0', $charset = 'utf-8') {
       $this->openMemory();
        $this->setIndent(true);
        $this->setIndentString(' ');
        $this->startDocument($version, $charset);
        $this->startElement('root'); 
   }
   
   /**
    * Enter description here ...
    */
	function __destruct() {
       $this->endElement();
   }
   
}