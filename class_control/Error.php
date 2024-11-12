<?php
/**
 * Error es la clase basica encargada de gestionar los mensajes de error presentados durante la ejecucion de un proceso
 * @author      Sixto Angel Pinzon
 * @version     1.0
 */

class Error {	 

  /**
   * Variable que indica el codigo del error
   * @var integer
   * @access public
   */
	var $code;
	
  /**
   * Variable que indica el mensaje asociado con el tipo de error
   * @var integer
   * @access public
   */
	var $message;    	

   /** 
	* Constructor encargado de inicializar el código de error
	* @param	integer $code	Es el código del error
	* @return   void
	*/
    function __construct($code = 0) {
    	$this->code=$code;
    }
	
    /** 
     * Retorna el valor string correspondiente al atributo texto del error
     * @return   string
     */
    function getMessage() {
    	return $this->message;
   	}
   	
   	/** 
	* Funcion encargada de asignar el texto del mensaje de error
	* @param	string $mess	Es el texto del error
	* @return   void
	*/
   	function setMessage($mess){
   		$this->message=$mess;
   	}
   	
   	/** 
	* Funcion encargada de asignar el texto del mensaje de error aÃ±adiendo el parametro de entrada al valor actual del mensaje
	* @param	string $mess	Es el texto del error
	* @return   void
	*/
   	function setMessageAdd($mess){
   		$this->message="<span class='alarmas'>".$mess."</span>".$this->message;
   	}
}

?>