<?php
/**
 * CombinaError es la clase encargada de gestionar los mensajes de errores presentadas al tratar de combinar un documento
 * @author      Sixto Angel Pinzon
 * @version     1.0
 */
class AplExternaError extends Error {	     	
		
	/** 
	* Constructor encargado de inicializar el codigo de error
	* @param integer $code	Es el codigo del error
	* @return   void
	*/
   function __construct($code = 0) {
       parent::Error($code);
       parent::setMessage($this->tipoError($code));
	}

   /** 
	* Funcion encargada de obtener el mensaje de error de acuerdo al codigo del error
	* @param   integer	$code	Es el codigo del error
	* @return   void
	*/
    function tipoError() {
    	$error.="<BR> <input type=button  name=Regresar value=Regresar  class='botones' onClick='history.go(-1);'>";
	    return $error;
    }
}


?>