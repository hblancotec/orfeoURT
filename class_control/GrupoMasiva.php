<?php
require_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/class_control/Radicado.php"; 
/**  Esta clase gestiona las operaciones que es posible realizar sobre un grupo de documentos de correspondencia masiva
* @author Sixto Angel Pinzón López
*	@version     1.0
*/  
class GrupoMasiva 
{

  /**
   * Vector que almacena el conjunto de radicados sacados de un grupo de masiva
   * @var array
   * @access public
   */
	var $grupoSacado;
/**
   * Gestor de las transacciones con la base de datos
   * @var ConnectionHandler
   * @access public
   */
  var $cursor;
/**
   * Vector que almacena el conjunto de radicados de un grupo de masiva
   * @var string
   * @access public
   */
	var $vecRads;
/**
   * Guarda el primer radicado local
   * @var string
   * @access public
   */
	var $radPrimLocal;
/**
   * Guarda el primer radicado nacional
   * @var string
   * @access public
   */
	var $radPrimNacional;
/**
   * Guarda sgd_renv_codigo del query
   * @var string
   * @access public
   */
	var $sgd_renv_codigo;

	
/** 
* Constructor encargado de obtener la conexion
* @param	$db	ConnectionHandler es el objeto conexion
* @return   void
*/
	function __construct($db) {
	
		$this->cursor = & $db;
	
	}
	
	
/** 
* Realiza la transaccción de sacar un radicado del grupo de envío de masiva
* @param $grupo	string	es el código del radicado del grupo
* @param $codRadicado	string	es el código del radicado a sacar
*/
	function sacarDeGrupo($grupo,$codRadicado)  {
		
		//Arreglo que almacena los valores que ha de tomar cada columna
		$values["sgd_rmr_radi"] = $codRadicado;
		$values["sgd_rmr_grupo"] = $grupo;
		$rs=$this->cursor->insert("sgd_rmr_radmasivre",$values);
		if (!$rs){
			$this->cursor->conn->RollbackTrans();
			die ("<span class='etextomenu'>No se ha podido actualizar sgd_rmr_radmasivre "); 
		}
  }
	
	
/** 
* Realiza la transaccción de incluir nuevamente un radicado en su grupo de envío de masiva
* @param	$grupo	string	es el código del radicado del grupo
* @param $codRadicado	string	es el código del radicado a incluir
*/	
 function incluirEnGrupo($grupo,$codRadicado)  {
		$values["sgd_rmr_radi"] = $codRadicado;
		$values["sgd_rmr_grupo"] = $grupo;
		$rs=$this->cursor->delete("sgd_rmr_radmasivre",$values);
		if (!$rs){
			$this->cursor->conn->RollbackTrans();
			die ("<span class='etextomenu'>No se ha podido borrar de  sgd_rmr_radmasivre "); 
		}
		
  }


/** 
* Obtiene los radicados de un grupo de masiva y pone esta información en los atributos  vecRads y sgd_renv_codigo
* @param $dependencia	string	es la dependencia del grupo
* @param $grupo	string	es el código del radicado del grupo
* @param $filtro	string	es un subconjunto de radicados, en caso de tratarse de de una búsqueda específica
* @return   array
*/	
 function obtenerGrupo($dependencia,$grupo,$filtro) 
 {  $this->vecRads = array();
  	if (strlen($filtro)>0){
		$qFiltro="and radi_nume_sal in ($filtro)"; 
	}else{
		$qFiltro="";
	}
	//almacena el query
	$db = &$this->cursor;
	include ($this->cursor->rutaRaiz."/include/query/class_control/queryGrupoMasiva.php");
	//print ("---->".$qeryObtenerGrupo);		
	$rs=$this->cursor->query($qeryObtenerGrupo);
	
	//Recorre el resultado de la búsqueda
	while   ($rs && !$rs->EOF){
		$this->vecRads[]=$rs->fields['RADI_NUME_SAL'];
		$this->sgd_renv_codigo=$rs->fields['SGD_RENV_CODIGO'];
		$rs->MoveNext();
	}
	return($this->vecRads);
}

	
/** 
* Obtiene los radicados de un grupo de masiva que han dido sacados de este y pone esta información en el atributo grupoSacado
* @param $grp	string	es el código del radicado del grupo
* @return   array
*/		
function setGrupoSacado($grp)
  {
		//almacena el query
  	$q= "select *  from sgd_rmr_radmasivre  where sgd_rmr_grupo=$grp";
		$rs=$this->cursor->query($q);
		
		//Recorre el resultado de la búsqueda
		while   ($rs && !$rs->EOF){
			$this->grupoSacado[]=$rs->fields['SGD_RMR_RADI'];
			$rs->MoveNext();
		}
  }

	
/** 
* Obtiene el número de radicados de un grupo de masiva que han dido sacados de este; antes deinvocar esta función debe invocarse setGrupoSacado()
* @return	int
*/			
function getNumeroSacados(){
    return (is_array($this->grupoSacado) ? count($this->grupoSacado) : 0);	
}


/** 
* Limpia el arreglo que contiene los radicados sacados de un grupo de masiva
*/
function limpiarGrupoSacado(){
    if (is_array($this->grupoSacado) and count($this->grupoSacado)>0)
		array_splice($this->grupoSacado,0);
}

	
/** 
* Retorna verdadero si un radicado ha sido retirado de un grupo de masiva, de lo contrario falso
* @param $grupo	string	es el código del radicado del grupo
* @param $radicado	string	es el radicado a analizar
* @return boolean
*/		
function radicadoRetirado($grupo,$radicado){
		//almacena el query
		$q= "select *  from sgd_rmr_radmasivre  where sgd_rmr_grupo=$grupo and sgd_rmr_radi=$radicado";
		$rs=$this->cursor->query($q);

		//Si fué retirado el radicado
		if   ($rs && !$rs->EOF){
			return true;
		}else{
		 	return false;
		}
	}

	
/** 
* Obtiene los radicados limite de un grupo de masiva en la posición 0 se encuentra el límite inferior y en la 1 el superior. Antes de invocar esta función debe llamarse a obtenerGrupo()
* @return   array
*/	
function getRadsLimite(){
		$limite[0]=$this->vecRads[0];
		$limite[1]=$this->vecRads[count($this->vecRads)-1];
	  return ($limite);
}


/** 
* Obtiene el número de radicados nacionales y locales que existen en un grupo de masiva
* en los índices 'local' y 'nacional'
* @param $depe_dpto_cod	int	es código del departamento de la dependencia actual
* @param $depe_muni_cod	int	es el código del municipio de la dependencia actual
* @return   array
*/		
	function getNumNacionalesLocales($depe_dpto_cod,$depe_muni_cod){
		$rad = new Radicado($this->cursor);
		$num = count($this->vecRads);
		$i = 0;
		$local=0;
		$nacional=0;
		
		// Recorre el vector del grupo de radicados	
		while ($i < $num) {	
			$rad->radicado_codigo($this->vecRads[$i]);
			
			//Si el radicado no ha sido retirado
			if (!$this->radicadoRetirado($this->vecRads[0],$this->vecRads[$i])){
				$datosRad=$rad->getDatosRemitente();
				
				//Mira si los datos del radicado se corresponden con los datos locales
				if (($depe_dpto_cod==$datosRad["deptoCodi"] && $datosRad["muniCodi"]==$depe_muni_cod)  || 
				($depe_dpto_cod==68 && ($datosRad["muniCodi"]==547||$datosRad["muniCodi"]==276||$datosRad["muniCodi"]==307) )) {
					$local++;
					if	($local==1){
						$this->radPrimLocal=$this->vecRads[$i];
					}		
				}else{
			 		$nacional++;
					if	($nacional==1){
						$this->radPrimNacional=$this->vecRads[$i];
					}
					
				}
			}	
			$i++; 
		}
	  $resultado["local"] = $local;
		$resultado["nacional"] = $nacional;
		return ($resultado);
	}

	
/** 
* Escribe la función de calcular el precio de un grupo de radicados a enviar
* @return   string
*/		
	function javascriptCalcularPrecio(){
	
		echo "function calcular_precio(empresa_envio,envio_peso,valor_gr,numLocal,numNacional)";
  	echo "{";
 		$no_tipo="true";
  	$isql = "	SELECT a.SGD_FENV_CODIGO,a.SGD_CLTA_DESCRIP,a.SGD_CLTA_PESDES,a.SGD_CLTA_PESHAST,b.SGD_TAR_VALENV1,b.SGD_TAR_VALENV2 FROM SGD_CLTA_CLSTARIF a,SGD_TAR_TARIFAS b WHERE a.SGD_FENV_CODIGO=b.SGD_FENV_CODIGO
	            AND a.SGD_TAR_CODIGO=b.SGD_TAR_CODIGO";
		$rs=$this->cursor->query($isql);						
		//$empresas_envio = ora_fetch_into($cursor,$row, ORA_FETCHINTO_NULLS|ORA_FETCHINTO_ASSOC);
		echo "\n";
		echo "if (parseFloat(document.getElementById(envio_peso).value)>=0){";
		
		//Recorre el resultado de la búsqueda
	  while  ($rs && !$rs->EOF){
	 	 $valor_local = $rs->fields['SGD_TAR_VALENV1'];
	   $valor_fuera = $rs->fields['SGD_TAR_VALENV2'];
	   $valor_certificado = $rs->fields['SGD_TAR_VALENV1'];
	   $rango = $rs->fields['SGD_CLTA_DESCRIP']; 
	   $fenvio = $rs->fields['SGD_FENV_CODIGO']; 
		
       echo "if(document.getElementById(empresa_envio).value==$fenvio)
	            {
					//	alert (document.getElementById(envio_peso).value);	
				   if(parseFloat(document.getElementById(envio_peso).value)>=".$rs->fields['SGD_CLTA_PESDES']." &&  parseFloat(document.getElementById(envio_peso).value)<=".$rs->fields['SGD_CLTA_PESHAST'] .") \n
	                  {
					     document.getElementById(valor_gr).value = '$rango';
						   valor_local = $valor_local + 0;
						 	 valor_fuera = $valor_fuera +0 ;
						    
						 } 
						 
					  }
			";
			$rs->MoveNext();
	 }


   echo "peso = document.getElementById('envio_peso').value+0;";
   echo "document.getElementById('valor_unit_local').value = valor_local;";
   echo "document.getElementById('valor_unit_nacional').value = valor_fuera;";
	 echo "valor_local = valor_local*document.getElementById(numLocal).value;";
	 echo "valor_fuera = valor_fuera * document.getElementById(numNacional).value;";
   echo "document.getElementById('valor_total_local').value = valor_local;";
   echo "document.getElementById('valor_total_nacional').value = valor_fuera;";
	 echo "document.getElementById('valor_total').value = valor_local + valor_fuera;";
	 echo	"} else { ";
	 echo " alert('Debe suminstrar el peso de los documentos'); ";
	 echo "}";
	 echo "}";
	}
	

/** 
* Obtiene el primer radicado local de un grupo. Antes de invocar esta función debe llamarse a obtenerGrupo()  y a getNumNacionalesLocales()
* @return   string
*/			
	function getPrimerRadicadoLocal(){
		return ($this->radPrimLocal);
	}	


/** 
* Obtiene el primer radicado nacional de un grupo. Antes de invocar esta función debe llamarse a obtenerGrupo()  y a getNumNacionalesLocales()
* @return   string
*/		
	function getPrimerRadicadoNacional(){
		return($this->radPrimNacional);
	}	
		

/** 
* Obtiene sgd_renv_codigo() de un grupo. Antes de invocar esta función debe llamarse a obtenerGrupo()  
* @return   string
*/	
	function getSgd_renv_codigo(){
		return ($this->sgd_renv_codigo);
	}

} 


?>