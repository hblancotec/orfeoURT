<?php
require_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once "$ruta_raiz/class_control/Departamento.php";
require_once "$ruta_raiz/class_control/Municipio.php";
require_once "$ruta_raiz/class_control/Esp.php";

/**
 * Radicado es la clase encargada de gestionar la informacion referente a un radicado
 * @author      Sixto Angel Pinzon
 * @version     1.0
 */
class Radicado{
/**
   * Gestor de las transacciones con la base de datos
   * @var ConnectionHandler
   * @access public
   */
	var $cursor;
 /**
   * Variable que se corresponde con su par, uno de los campos de la tabla Radicado
   * @var integer
   * @access public
   */
	var $tdoc_codi;
/**
   * Variable que se corresponde con su par, uno de los campos de la tabla Radicado
   * @var string
   * @access public
   */
	var $radi_fech_radi;
/**
   * Variable que se corresponde con su par, uno de los campos de la tabla Radicado
   * @var integer
   * @access public
   */
	var $radi_nume_radi;
/**
   * Variable que se corresponde con su par, uno de los campos de la tabla Radicado
   * @var integer
   * @access public
   */
	var $tdid_codi;
/**
   * Variable que se corresponde con su par, uno de los campos de la tabla Radicado
   * @var string
   * @access public
   */
	var $radi_path;
/**
   * Variable que se corresponde con su par, uno de los campos de la tabla Radicado
   * @var string
   * @access public
   */
	var $radi_usua_radi;


/** 
* Constructor encargado de obtener la conexion
* @param	$db	ConnectionHandler es el objeto conexion
* @return   void
*/
	function __construct($db){
		$this->cursor = $db;
	}


/** 
* Carga los atributos de la clase con los datos del radicado enviado como par�metro, si existen datos retorna true, de lo contrario false
* @param	$codigo	string	es el codigo del radicado 
* @return   boolean
*/	
	function radicado_codigo($codigo){
	//almacena el query
	     $sqlFecha = $this->cursor->conn->SQLDate("Y/m/d h:i:s a","r.radi_fech_radi");
	     $db = &$this->cursor;
	     include ($this->cursor->rutaRaiz."/include/query/class_control/queryRadicado.php");
	     $rs=$this->cursor->query($qeryRadicado_codigo);

		//Si existen resultados
		if  (!$rs->EOF){
			$this->tdid_codi=$rs->fields['TDID_CODI'];
			$this->tdoc_codi=$rs->fields['TDOC_CODI']; 
			$this->radi_fech_radi=$rs->fields['FECDOC'];
			$this->radi_nume_radi = $rs->fields['RADNUM']; 
			$this->radi_path = $rs->fields['RADI_PATH']; 
			$this->radi_usua_radi = $rs->fields['RADI_USUA_RADI']; 
			return true;
		}else{
			$this->tdid_codi="";
			$this->tdoc_codi=""; 
			$this->radi_fech_radi="";
			$this->radi_nume_radi = ""; 
			$this->radi_path = ""; 
			$this->radi_usua_radi="";
			return false;
		}
		
	}


/** 
* Retorna un array con los datos del remitente de un radicado, este vector contiene los �ndices 'nombre','direccion','deptoNombre','muniNombre','deptoCodi','muniCodi'; antes de invocar esta funcion, se debe llamar a  radicado_codigo()
* @return   array
*/
	function getDatosRemitente(){
  	//almacena el query
		$q="select *  from sgd_dir_drecciones where radi_nume_radi =".$this->radi_nume_radi;
		$rs=$this->cursor->query($q);
		//Agregada por Johnny debido a solicitud de usuarios
		$direccion = $rs->fields['SGD_DIR_DIRECCION']; 
		$deptoCodi = $rs->fields['DPTO_CODI']; 
		$muniCodi = $rs->fields['MUNI_CODI'];
    $idCont = $rs->fields['ID_CONT'];
    $idPais = $rs->fields['ID_PAIS']; 
		//Agregada por Johnny debido a solicitud de usuarios
		$nombre = $rs->fields['SGD_DIR_NOMREMDES']; 
		$dep = new Departamento($this->cursor);
		$mun = new Municipio($this->cursor); 
		$dep->departamento_codigo($deptoCodi);
		$mun->municipio_codigo($deptoCodi,$muniCodi);		
  
		//Si existen resultados del query  
		/** anulado por JAIRO LOSADA
		 *  YA QUE los datos se sacan directamente dir_direcciones
		 * 
		   
		 if  (!$rs->EOF){
			$sgd_esp_codi=$rs->fields['SGD_ESP_CODI'];
			$sgd_ciu_codigo=$rs->fields['SGD_CIU_CODIGO']; 
			$sgd_oem_codigo=$rs->fields['SGD_OEM_CODIGO'];
 
    	//Si se trata de una ESP
			if (strlen($sgd_esp_codi)>0&&$sgd_esp_codi!=0 ){
			
				$q="select *  from bodega_empresas where identificador_empresa =".$sgd_esp_codi;
	    	$rs2=$this->cursor->query($q);
			
				if  (!$rs2->EOF){
					$nombre = $rs2->fields['NOMBRE_DE_LA_EMPRESA'];
					$direccion = $rs->fields['SGD_DIR_DIRECCION']; 
					$deptoCodi = $rs->fields['DPTO_CODI']; 
					$muniCodi = $rs->fields['MUNI_CODI']; 
					$dep= new Departamento($this->cursor);
					$mun = new Municipio($this->cursor); 
					$dep->departamento_codigo($deptoCodi);
					$mun->municipio_codigo($deptoCodi,$muniCodi);
				
				}
		
			}

		//Si se trata de otra empresa
		if (strlen($sgd_oem_codigo)>0&&$sgd_oem_codigo!=0){
			//almacena el query
			$q="select *  from sgd_oem_oempresas where sgd_oem_codigo =".$sgd_oem_codigo;
			$rs=$this->cursor->query($q);
			
			if  (!$rs->EOF){
				$nombre = $rs->fields['SGD_OEM_OEMPRESA'];
				$direccion = $rs->fields['SGD_OEM_DIRECCION']; 
				$deptoCodi = $rs->fields['DPTO_CODI']; 
				$muniCodi = $rs->fields['MUNI_CODI']; 
				$dep= new Departamento($this->cursor);
				$mun = new Municipio($this->cursor); 
				$dep->departamento_codigo($deptoCodi);
				$mun->municipio_codigo($deptoCodi,$muniCodi);
			}
		}

		//Si se trata de una persona natural
		if (strlen($sgd_ciu_codigo)>0&&$sgd_ciu_codigo!=0 ){  
			//almacena el query
			$q="select *  from sgd_ciu_ciudadano where sgd_ciu_codigo =".$sgd_ciu_codigo;
			$rs=$this->cursor->query($q);
			
			if  (!$rs->EOF) {
				$nombre = $rs->fields['SGD_CIU_NOMBRE'] . " " . $rs->fields['SGD_CIU_APELL1'] . " " . $rs->fields['SGD_CIU_APELL2'];
				$direccion = $rs->fields['SGD_CIU_DIRECCION']; 
				$deptoCodi = $rs->fields['DPTO_CODI']; 
				$muniCodi = $rs->fields['MUNI_CODI']; 
				$dep= new Departamento($this->cursor);
				$mun = new Municipio($this->cursor); 
				$dep->departamento_codigo($deptoCodi);
				$mun->municipio_codigo($deptoCodi,$muniCodi);
			}
		}

	} */
	
	//Si se hallaron datos del remitente
	if ($dep){
	
		$vecDatos["nombre"]=$nombre;
		$vecDatos["direccion"]=$direccion;
		$vecDatos["deptoNombre"]=$dep->get_dpto_nomb();
		$vecDatos["muniNombre"]=$mun->get_muni_nomb();
		$vecDatos["deptoCodi"]=$deptoCodi;
		$vecDatos["muniCodi"]=$muniCodi;
		
	}
	
	return ($vecDatos);
}


/** 
* Retorna un string  con el dato correspondiente a la fecha de radicacion;  antes de invocar esta funcion, se debe llamar a  radicado_codigo()
* @return   string
*/
	function getRadi_fech_radi($formato = null){
		if (!empty($formato)) {
			// en la pos0 es el ano, pos1 mes, pos2 dia
			$arregloFecha = explode("/",$this->radi_fech_radi);
      //print_r($arregloFecha);
      if($arregloFecha[1]){
			return date($formato, mktime(0, 0, 0, 
						$arregloFecha[1],
						$arregloFecha[2],
						$arregloFecha[0]));
      }
		}
		return($this->radi_fech_radi);
	}


/** 
* Retorna un string  con el dato correspondiente al path de la imagen digitalizada del radicado
* @return   string
*/
	function getRadi_path(){
		return($this->radi_path);
	}


/** 
* Retorna un string  con el dato correspondiente al codigo del tipo de documento que es el radicado
* @return   string
*/
	function getTdocCodi(){
		return($this->tdoc_codi);
	}
	
/** 
* Retorna un string  con el dato correspondiente al codigo del usuario radicador
* @return   string
*/
	function getUsuaRad(){
		return($this->radi_usua_radi);
	}


	
	

}

?>
