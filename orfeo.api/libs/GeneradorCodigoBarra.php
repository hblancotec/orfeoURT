<?php

/*
 * Esta Clase Sirve para generar codigo de barras a partir de la librería barcodephp v5.2.0
 * Versión Actual al momento de la implementación v5.2.0 http://www.barcodephp.com/
 * El Objetivo es permitir Implementar cualquier tipo de Codigo de Barras en toda la aplicación aprovechando las ventajas que ofrece esta libreria.
 */

/**
 * Description of GeneradorCodigoBarra
 *
 * @author omalagon
 */
// Including all required classes
require_once('barcodegen/class/BCGFontFile.php');
require_once('barcodegen/class/BCGColor.php');
require_once('barcodegen/class/BCGDrawing.php');

class GeneradorCodigoBarra {

    var $tipo;

    /**
     * Nota: Se debe completar su implementaión si se requiere utilizar otros tipos de Codigos de barras.
     * @param String $tipo: Corresponde al tipo de codigo de Barras posibles valores<br>code39,etc... 
     * @param String $valor
     */
    public function __construct($tipo,$valor) {
       $this->tipo = $tipo;
       $this->generar($valor);
    }



    /**
     * 
     * @param type $text
     */
    private function generar($text) {

        try {
            //Se valida el tipo de codigo de barras a generar.-
            switch ($this->tipo) {
                
                case 'code39':
                    // Including the barcode technology
                    require_once('barcodegen/class/BCGcode39.barcode.php');


                    // Loading Font
                    $font = new BCGFontFile('libs/barcodegen/font/Arial.ttf', 26);

                    // Don't forget to sanitize user inputs
                    $text = isset($text) ? $text : 'HELLO';

                    // The arguments are R, G, B for color.
                    $color_black = new BCGColor(0, 0, 0);
                    $color_white = new BCGColor(255, 255, 255);
                    $drawException = null;
                    $code = new BCGcode39();
                    $code->setScale(2); // Resolution
                    $code->setThickness(30); // Thickness
                    $code->setForegroundColor($color_black); // Color of bars
                    $code->setBackgroundColor($color_white); // Color of spaces
                    $code->setFont($font); // Font (or 0)
                    $code->parse($text); // Text
                    /* Here is the list of the arguments
                      1 - Filename (empty : display on screen)
                      2 - Background color */
                    $drawing = new BCGDrawing('', $color_white);
                    if ($drawException) {
                        $drawing->drawException($drawException);
                    } else {
                        $drawing->setBarcode($code);
                        $drawing->draw();
                    }
                    ob_start();
                    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
                    $this->img = ob_get_clean();
                    break;
            }
        } catch (Exception $exception) {
            throw new Exception("Error al generar el codigo de barras, " . $exception->getMessage());
        }
    }
    
    /**
     * 
     * @return String Imagen codificada en base64 
     */
    function retornarImgBase64(){
            return base64_encode($this->img);
    }

}

?>
