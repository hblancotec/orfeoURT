<?php


/**
 * Esta calse se crea con la necesidad de validar todas las entradas SQL para evitar posibles ataques de sql Ijeccion.
 *
 * @author omalagon
 */
class ConsultasSQL {
    
    
/**
 * validar datos 
 * @param type $data
 * @return type 
 */    
function escape($data) {
    if ( !isset($data) or empty($data) ) return '';
    if ( is_numeric($data) ) return $data;

    $non_displayables = array(
        '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
        '/%1[0-9a-f]/',             // url encoded 16-31
        '/[\x00-\x08]/',            // 00-08
        '/\x0b/',                   // 11
        '/\x0c/',                   // 12
        '/[\x0e-\x1f]/'             // 14-31
    );
    foreach ( $non_displayables as $regex )
        $data = preg_replace( $regex, '', $data );
    $data = str_replace("'", "''", $data );
    return $data;
}

// 
/**
 * Funcion para eliminar "/" y anadir comillas
 * @param type $valor Valor a validar 
 * @return string 
 */
function comillas_inteligentes($valor){
    // Retirar las barras
    if (get_magic_quotes_gpc()) {
        $valor = stripslashes($valor);
    }
    // Colocar comillas si no es entero
    if (!is_numeric($valor)) {
        $valor = "'" . $this->escape($valor) . "'";
    }
    return $valor;
}

/**
 * Prepara un valor especifico como entrada sql Valido.
 * @param type $cadena
 * @return type 
 */
function prepararValorSql($cadena) {
   
    return $this->comillas_inteligentes($cadena);
    
}

}
?>
