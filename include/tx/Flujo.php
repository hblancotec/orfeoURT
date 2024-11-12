<?php
/**
 * Esta clase realiza todos las transacciones relacionadas con los Flujos de Trabajo.
 * Para esto se crean funciones que encuentra cuales son las aristas adyacentes 
 * a un nodo determinado.
 * Ademas actualiza y cosulta el estado actual de un expediente.
 * @author Jairo Losada 
 * @version 3.5.1 2006
 * 
 */
class Flujo{
	var $codProceso;
	var $db;
	var $nodosSig; // Array de Nodos siguientes
	var $aristasNombresSig; // Array de Nombres de aristas Siguientes
	var $aristaTipoDoc;
	var $aristaSRD;
	var $aristaSBRD;   // Indica la Serie y subserie a la cual esta fijada dicha arista.
	var $aristaAutomatico;  // Array Indica si el Flujo por dicha arista es automatico (0) o manual (1). 
		
	// VARIABLES DE USUARIO QUE INTANCIO LA LASE
	var $usuaDoc; // Documento de Usuario que esta realizando el proceso
	var $usuaCodi; // Codigo de usuario que realiza el proceso
	var $depeCodi; // Dependencia en la cual se encuentra el usuario que realiza el proceso.
	
	var $codNodoProx; // Siguiente Nodo.
	var $codArista; // Codigo de Atista 
	var $descArista;  // Descripcion arista

	/**
	 * Enter description here...
	 *
	 * @param object $db
	 * @param integer $codProceso
	 * @param string $usuaDoc
	 * @return Flujo
	 */
	function __construct($db,$codProceso, $usuaDoc){
		$this->codProceso = $codProceso;
		$this->db = $db;

		$query = "SELECT USUA.USUA_CODI,
                            USD.DEPE_CODI
                    FROM USUARIO USUA,
                            SGD_USD_USUADEPE USD
                    WHERE USUA.USUA_DOC = '$usuaDoc' AND
                            USUA.USUA_DOC = USD.USUA_DOC AND
                            USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                            USD.SGD_USD_SESSACT = 1";
		$rs = $this->db->conn->Execute($query);
		$this->usuaDoc = $usuaDoc;
		if($rs) {
			$this->usuaCodi = $rs->fields["USUA_CODI"];
			$this->depeCodi = $rs->fields["DEPE_CODI"];
	    }
	}
	/**
	 * 
	 * Enter description here...
	 *
	 * @param number $aristaActual  //Codigo de la arista actual
	 * @return array   // retorna el arreglo de aristas posibles.
	 */
	function aristasSiguiente($estadoActualExp){
        if (!empty($this->codProceso)) {
            if($estadoActualExp==1 or !$estadoActualExp) {
                $query = "SELECT SGD_FEXP_CODIGO
                            FROM SGD_FEXP_FLUJOEXPEDIENTES 
                            WHERE SGD_FEXP_ORDEN = 1 AND
                                    SGD_PEXP_CODIGO = $this->codProceso";
                $rs = $this->db->conn->Execute($query);
                $estadoActualExp = $rs->fields["SGD_FEXP_CODIGO"];
            }
            $query = "SELECT * FROM SGD_FARS_FARISTAS 
                        WHERE SGD_FEXP_CODIGOINI=$estadoActualExp 
                        ORDER BY SGD_FEXP_CODIGOINI,SGD_FEXP_CODIGOFIN";	
            $rs = $this->db->conn->Execute($query);
            $aristasArray = "";
            if($rs) {
                while(!$rs->EOF) {
                    $aristasArray[] = $rs->fields["SGD_FARS_CODIGO"];
                    $nodosSig[]     = $rs->fields["SGD_FEXP_CODIGOFIN"];
                    $aristasNombresSig[] = $rs->fields["SGD_FARS_DESC"];
                    $aristaTRad[]   = $rs->fields["SGD_TRAD_CODIGO"];
                    $aristaTDoc[]   = $rs->fields["SGD_TPR_CODIGO"];
                    $aristaSRD[]    = $rs->fields["SGD_SRD_CODIGO"];
                    $aristaSBRD[]   = $rs->fields["SGD_SBRD_CODIGO"];
                    $aristaAutomatico[] = $rs->fields["SGD_FARS_AUTOMATICO"];
                    $rs->MoveNext();
                }
                $this->nodosSig = $nodosSig;
                $this->aristasNombresSig = $aristasNombresSig;
                $this->aristaAutomatico = $aristaAutomatico;
                $this->aristaSBRD = $aristaSBRD;
                $this->aristaSRD = $aristaSRD;
                $this->aristaTDoc = $aristaTDoc;
                $this->aristaTRad = $aristaTRad;
            }
		    return $aristasArray;
        } else {
            return null;
        }
	}
	/**
	 * METODO devuelve el nombre de un nodo
	 *
	 * @param number $nodo
	 * @param number $tProc
	 * @return string retorna el nombre del nodo buscado.
	 */
	function getNombreNodo($nodo,$tProc){
	$query = "SELECT * FROM SGD_FROL_ROLUS rus, SGD_FROLA_FROLARISTA fra,
			  SGD_FARS_FARISTAS fars
			 where rus.sgd_frol_codigo = fra.sgd_frol_codigo and
                    fars.SGD_FARS_CODIGO=fra.SGD_FARS_CODIGO and
                    rus.usua_doc='".$this->usuaDoc."'";
		$query = "SELECT * 
					FROM SGD_FEXP_FLUJOEXPEDIENTES 
					WHERE SGD_FEXP_CODIGO=$nodo";// and SGD_PEXP_CODIGO=$tProc";
				//Se pone comentario a esta parte porque ese valor no estaba llegando, además no es necesario para obtener el nombre de la etapa
				//porque el codigo de la etapa es único.
//		$this->db->conn->debug = true;
		$rs = $this->db->conn->Execute($query);
		if($rs){
				$nodoNombre = $rs->fields["SGD_FEXP_DESCRIP"];
		}
		return $nodoNombre;
	}	
	function aristasPrevia($aristaActual){
		return $aristasArray;
	}
	/**
	 * Encuentra el nodo en el cual se encuentra un Expediente.
	 *
	 * @param string $expNume Numero de expediente que busca.
	 * @nodoActual number  retorna el numero del nodo en el que se encuentra el expediente buscado.
	 * @rs object RecordSet con consulta realizada.
	 * @return number  retorna el numero del nodo en el que se encuentra el expediente buscado.
	 */
	function actualNodoExpediente($expNume) {
        $nodoActual = 0;
        if (!empty($expNume)) {
            $query = "SELECT * FROM SGD_SEXP_SECEXPEDIENTES
                        WHERE SGD_EXP_NUMERO='$expNume' AND
                                SGD_PEXP_CODIGO = $this->codProceso";
            $rs = $this->db->conn->Execute($query);
            
            if($rs) {
                $nodoActual = $rs->fields["SGD_FEXP_CODIGO"];
            }
            
            if(!$nodoActual) {
                $query = "SELECT * FROM SGD_FEXP_FLUJOEXPEDIENTES 
                           where sgd_pexp_codigo = ".$this->codProceso."
                          ORDER BY SGD_FEXP_ORDEN";
                $rs = $this->db->conn->Execute($query);				
                $nodoActual = $rs->fields["SGD_FEXP_CODIGO"];
            }
        }
        if ($nodoActual == 0) {
            unset($nodoActual);
        }
		return $nodoActual;
	}
	/**
	 * Metodo que cambia el estado (Nodo) de un expediente. 
	 * Ademas genera un historico en el expediente.
	 *
	 * @param string $expNume  Numero de expediente a modificar
	 * @param number $radiNume  Numero de radicado en el cual se realiza el cambio de estado
	 * @param number $nodoNuevo Nodo al cual pasa el proceso
	 * @param number $nodoActual Nodo Actual del Proceso
	 * @param number $aristaCodi Arista utilizada para realizar el cambio de Estado o Nodo.
	 * @param number $fldAutomatico Indica con 1 si el flujo fue automatico.
	 * @param number $tProc Codigo del Proceso a utilizar
	 * @param number $usuaDoc Documento del usuario que realiza el movimiento
	 * @param number $usuaCodi Codigo del usuario
	 * @param string $observa Observacion escrita por el usuario para el cambio de estado
         * @param string $proc proceso del al cual se reailza el cambio 
	 * @return number  retorna o si fallo y 1 si ejecuto bien la operacion.
	 */
	function cambioNodoExpediente($expNume,$radiNume,$nodoNuevo,$aristaCodi,$fldAutomatico,$observa,$proc = null){
//		$this->db->conn->debug = true;
		$recordSet2["SGD_FEXP_CODIGO"] = $nodoNuevo;		
		$recordWhere2["SGD_EXP_NUMERO"] = "'".$expNume."'";					
		$recordWhere2["SGD_PEXP_CODIGO"] = $proc;
                $rs=$this->db->update("SGD_SEXP_SECEXPEDIENTES", $recordSet2,$recordWhere2);	
                $realizado = '0';
		if($rs) {
			//$observa = "*Cambio Estado*  ";
			$flujo_nombre = $this->getNombreNodo($nodoNuevo,$this->codProceso);
			$observa .= " ($flujo_nombre)";
			$arrayRad = array();
			$arrayRad[]=$verradEntra;
			$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);
			$record["SGD_FEXP_CODIGO"] = $nodoNuevo;
			$record["SGD_EXP_FECHFLUJOANT"] = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
			$record["SGD_HFLD_FECH"] = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
			$record["SGD_EXP_NUMERO"] = "'".$expNume."'";
			$record["RADI_NUME_RADI"] = $radiNume;
			$record["USUA_DOC"] = "'".$this->usuaDoc."'";
			$record["DEPE_CODI"] = $this->depeCodi;
			$record["USUA_CODI"] = $this->usuaCodi;
			$record["SGD_TTR_CODIGO"] = 50;
			$record["SGD_HFLD_OBSERVA"] = "'".$observa."'";
			if(!$aristaCodi) $aristaCodi = "0";
			$record["SGD_FARS_CODIGO"] = $aristaCodi;
			$record["SGD_HFLD_AUTOMATICO"] = $fldAutomatico;
			$insertSQL = $this->db->insert("SGD_HFLD_HISTFLUJODOC", $record, "true");
			$realizado='1';
		}		
		return $realizado;
	}
	
		function getAristaTipoDoc($tipoDoc, $codProceso,$codSerie,$codSbrd){
//		$this->db->conn->debug = true;
		$query = "SELECT * FROM SGD_FARS_FARISTAS 
		WHERE 
		SGD_TPR_CODIGO=$tipoDoc 
		AND SGD_PEXP_CODIGO=$codProceso
		AND SGD_SRD_CODIGO=$codSerie
		AND SGD_SBRD_CODIGO=$codSbrd
		AND SGD_FARS_AUTOMATICO=1
		ORDER BY SGD_FEXP_CODIGOINI,SGD_FEXP_CODIGOFIN";	
		$rs = $this->db->conn->Execute($query);
		$aristasArray = "";
		if($rs)
		{
			
			$this->codArista = $rs->fields["SGD_FARS_CODIGO"];
			$this->descArista = $rs->fields["SGD_FARS_DESC"];
			$this->codNodoSiguiente =  $rs->fields["SGD_FEXP_CODIGOFIN"];
		}
		return $this->codArista;
	}
		function getMenuProximaArista($tipoDoc, $codProceso,$codSerie,$codSbrd,$tRad,$name,$default=null,$atributos=''){
		$query = "SELECT * FROM SGD_FARS_FARISTAS 
		WHERE 
		SGD_TPR_CODIGO=$tipoDoc 
		AND SGD_PEXP_CODIGO=$codProceso
		AND SGD_SRD_CODIGO=$codSerie
		AND SGD_SBRD_CODIGO=$codSbrd
		AND SGD_TRAD_CODIGO=$tRad
		AND SGD_FARS_AUTOMATICO=1
		ORDER BY SGD_FEXP_CODIGOINI,SGD_FEXP_CODIGOFIN";	
		$rs = $this->db->conn->Execute($query);
		$aristasArray = "";
		$menu = "<SELECT NAME='$name' $atributos>";
		$menu .= "<option value='0'> - Ningun Estado -</option>";
		if($rs)
		{
			while(!$rs->EOF)	
			{
			 $this->codArista = $rs->fields["SGD_FARS_CODIGO"];
			 $this->descArista = $rs->fields["SGD_FARS_DESC"];
			 $this->codNodoSiguiente =  $rs->fields["SGD_FEXP_CODIGOFIN"];
			 $valor = "".$this->codNodoSiguiente."-".$this->codArista ."";
			 if($default==$valor) $datoss = " selected "; else $datoss = " ";
			 
			 $menu .=  "<option value='$valor' $datoss>".$this->descArista."</option>";
			 $rs->MoveNext();
			}
		}
		$menu .="</select>";
		return $menu;
		}
	
}
?>
