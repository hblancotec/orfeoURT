<?php
/**
 * 
 * Se construye esta Clase con el fin de dar solución a los diferentes inconvenientes de codificacion que se presentan en el acceso a Informacion.
 */
class CodificacionEspecial
{
    public function __construct()
    {
       
    }
    /**
     * Este metodo retorna en codificacion HTML la codificacion UNICODE ej. \u0093
     * @param type $struct
     * @return type
     */
    public function jsonRemoveUnicodeSequences($struct) {
        return preg_replace('/\\\\U0*([0-9A-F]{2})/i', '&#x\1;', $struct);
    }
    
    
    /**
     * Este metodo Identifica la codificacion de una cadena.
     * @param type $texto
     * @return string
     */
    public function codificacion($texto)
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
