<?php
class Radicacion {
    /**
   * Clase maneja la generacion de nuevos Radicacos en El sistema
   * @autor Modificacon 12/2009 para ver. 3.8 por Fundacion Correlibre.org
   *        Modificacion 07/2010 Jairo Losada a DNP.
   * @param int Dependencia Dependencia de Territorial que Anula
   * @db Objeto conexion
   * @access public
   */

	// VARIABLES DE DATOS PARA LOS RADICADOS
	var $db;
	var $tipRad;
	var $radiTipoDeri;
	var $radiCuentai;
	var $eespCodi;
	var $mrecCodi;
	var $radiFechOfic;
	var $radiNumeDeri;
	var $tdidCodiRadi;
	var $descAnex;
	var $radiNumeHoja;
	var $radiPais;
    var $despla; #estadisticas de desplazamiento pqr
    /**
   * @raAsun varcahrt texto con el asunto del radicado que se esta tratando. Se guarda en la Tabla Radicado
   **/
	var $raAsun;
	var $radiDepeRadi;
	var $radiUsuaActu;
	var $radiDepeActu;
	var $carpCodi;
  /**
   * @radiNumeRadi number Numero de radicado que se esta trabajando, para el metodo de newRadicado es el que se genera. generarlmente se graba en el campo RADI_NUME_RADI y es porpiedad de la tabla RADICADO.
   **/
	var $radiNumeRadi;
	var $trteCodi;
	var $radiNumeIden;
	var $radiFechRadi;
	var $sgd_apli_codi;
	var $tdocCodi;
	var $estaCodi;
	var $radiPath;

	// VARIABLES DEL USUARIO ACTUAL
	var $dependencia;
	var $usuaDoc;
	var $usuaLogin;
	var $usuaCodi;
	var $codiNivel;
	var $noDigitosRad;
  var $errorNewRadicado;
  
  //Variables adicionales para
  /**
   * @param $trdCodigo integer Variable que guarda codigo de matriz tipificacon del anexo.
   **/
  var $trdCodigo=0;
  /**
   * @param $grbNombresUs string  Variable que contiene Nombre de usuario a grabar en radicado
   **/
  var $grbNombresUs;
  /**
   * @param $cc_documento integer Documento del usuario/Empresa a grabar en dir_drecciones, el el valor que se graba en el campo SGD_DIR_DOC de la tabla sgd_dir_dreccines
   **/
  var $ccDocumento;
  /**
   * @param $muniCodi integer Variable qeu guarda el codigo de Municipio MUNI_CODI.
   */
  var $muniCodi;
  /**
   * @param $dptoCodi integer Guarda el codigo del departamento / Provincia se graba en el campo DPTO_CODI
   */
  var $dptoCodi;
  /**
   * @param $idPais integer Guarda el codigo del pais se graba en el campo ID_PAIS que proviene de la tabla SGD_DEF_PAIS
   */
  var $idPais;
  /**
   * @param $idCont integer Guarda el codigo del continete se graba en el campo ID_CONT que proviene de la tabla SGD_DEF_CONTINENTE
   */
  var $idCont;
  /**
   * @param $funCodigo integer Si viene esta variable el remitente/destinarario es un usuario del sistema grabado en la Tabla Usuario se graba en SGD_DOC_FUN
   */
  var $funCodigo;
  /**
   * @param $oemCodigo integer Si viene esta variable el remitente/destinarario es una empresa normal qeu se graba en orfeo como Otras Empresas del sistema grabado en la Tabla sgd_oem_oempresasa se graba en SGD_OEM_CODIGO
   */
  var $oemCodigo=0;
  /**
   * @param $espCodigo integer Si viene esta variable el remitente/destinarario es Otra Empresa. Proviene de la BODEGA_EMPRESAS se graba en SGD_ESP_CODI.  es de anotar que esta tabla no es editable por el usuario radicador ya que el objetivo es que no exista duplicidad de datos.  Ojo BODEGA_EMPRESAS son las empresas clientes de la empresa donde funciona Orfeo.
   */
  var $espCodigo=0;
  /**
   * @param $ciuCodigo integer Si viene esta variable el remitente/destinarario es ciudadano normal que se graba en orfeo en la Tabla sgd_ciu_ciudadano se graba en SGD_CIU_CODIGO
   */
  var $ciuCodigo=0;
  /**
   * @param $direccion string Variable que contiene la direccion el Remitente/Destinatario del radicado que se esta trabajando.  Se graba en el campo SGD_DIR_DIRECCION
   */  
  var $direccion;
  /**
   * @param $codpostal integer Variable que contiene el codigo postal de la direccion el Remitente/Destinatario del radicado que se esta trabajando.  Se graba en el campo SGD_DIR_CODPOSTAL
   */
  var $codpostal;
  /**
   * @param $dirTelefono string Variable que contiene el telefono del Remitente/Destinatario del radicado que se esta trabajando.  Se graba en el campo SGD_DIR_TELEFONO
   */
  var $dirTelefono;
  /**
   * @param $dirMail string Variable que contiene el e-mail del Remitente/Destinatario del radicado que se esta trabajando.  Se graba en el campo SGD_DIR_MAIL
   */
  var $dirMail;
  /**
   * @param $dirTipo integer Codigo que indica que lugar ocupa el Remitente/Destino en el radicado 1,2,3 . . . .  En orfeo este orden es el de las pestañas en el formulario de Radicacion.  Se graga en la tabla SGD_DIR_DRECCIOENES en el campo SGD_DIR_TIPO Ej. 1. Remitente/Destino 2. Remitente2/Entidad/Predio
   */
  var $dirTipo;
  /**
   * @param $dirCodigo integer Codigo secuencial de la tabla SGD_DIR_DRECCIONES.  Usa la secuencia SEC_DIRECCIONES.
   */
  var $dirCodigo;
  /**
   * @param $dirNombre string nobmre de remitente/Destino
   */
  var $dirNombre;
  /**
   * @param $fenvCodi int codigo medio de envio
   */
  var $fenvCodi;
        

  function __construct($db) {
	/**
  * Constructor de la clase Historico
	* @param var $db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
	*
	*/
	global $_SERVER,
            $PHP_SELF,
            $_SESSION,
            $_GET,$krd;
	//global $HTTP_GET_VARS;
	$this->db = $db;

	  $this->noDigitosRad = 6;
		$curr_page = $id.'_curr_page';
		$this->dependencia= $_SESSION['dependencia'];
		$this->usuaDoc    = $_SESSION['usua_doc'];
		//$this->usuaDoc    =$_SESSION['nivelus'];
		$this->usuaLogin  = $krd;
		$this->usuaCodi   = $_SESSION['codusuario'];
		isset($_GET['nivelus']) ? $this->codiNivel = $_GET['nivelus'] : $this->codiNivel = $_SESSION['nivelus'];
 }
 
	function newRadicado($tpRad, $tpDepeRad) {
	    /** FUNCION QUE INSERTA UN RADICADO NUEVO
	     * Busca el Nivel de Base de datos.
	     **/
		$whereNivel = "";
		$sql = "SELECT USUA.CODI_NIVEL 
                    FROM USUARIO USUA,
                            SGD_USD_USUADEPE USD
                    WHERE USUA.USUA_CODI = " . $this->radiUsuaActu . " AND
                            USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                            USUA.USUA_DOC = USD.USUA_DOC AND
                            USD.DEPE_CODI = " . $this->radiDepeActu;
		// Busca el usuairo Origen para luego traer sus datos.
    //return "--->$sql";
		$rs = $this->db->conn->Execute($sql); # Ejecuta la busqueda
		$usNivel = $rs->fields["CODI_NIVEL"];
		//$usLogin = $rs->fields["CODI_NIVEL"];
		// Busca el usuario Origen para luego traer sus datos.
		$SecName = "SECR_TP$tpRad"."_".$tpDepeRad;
		$secNew = $this->db->nextId($SecName);
		if($secNew == 0) {
			$secNew=$this->db->nextId($SecName);
			if($secNew==0)
                die("<hr>\n
                        <b>\n
                            <font color='red'>\n
                                <center>\n
                                    Error no genero un Numero de Secuencia<br>SQL: $secNew
                                </center>\n
                            </font>\n
                        </b>\n
                    </hr>");
		}
		//$this->db->conn->debug = True;
		$newRadicado = date("Y") . $this->dependencia . str_pad($secNew,$this->noDigitosRad,"0", STR_PAD_LEFT) . $tpRad;
		if(!$this->radiTipoDeri) {
		    $recordR["radi_tipo_deri"]= "0";
		} else {
			$recordR["radi_tipo_deri"]= $this->radiTipoDeri;
		}
		if(!$this->carpCodi) $this->carpCodi = 2;
		if(!$this->radiNumeDeri) $this->radiNumeDeri = 0;
		$recordR["RADI_CUENTAI"] =  $this->radiCuentai;
		$recordR["EESP_CODI"]    =	$this->eespCodi?$this->eespCodi:0;
		if ($tpRad == 2) {
		    if ($this->mrecCodi != "")
		        $recordR["MREC_CODI"] = $this->mrecCodi;
		} else {
		    if ($this->fenvCodi != "")
		        $recordR["SGD_FENV_CODIGO"] = $this->fenvCodi;
		}
		//$recordR["RADI_FECH_OFIC"]=	$this->db->conn->DBDate($this->radiFechOfic);
		$recordR["RADI_FECH_OFIC"]=	$this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
		//$recordR["radi_tipo_deri"]=$this->radiTipoDeri;
		$recordR["RADI_NUME_DERI"]=	$this->radiNumeDeri;
		$recordR["RADI_USUA_RADI"]=	$this->usuaCodi;
		$recordR["RADI_DESPLA"]   =	(int)$this->despla;
		$recordR["RADI_PAIS"]     =	"'".$this->radiPais."'";
		//$recordR["RA_ASUN"]       = $this->db->conn->qstr($this->raAsun);
		$recordR["RA_ASUN"]       = iconv(mb_detect_encoding($this->raAsun,"UTF-8, ISO-8859-1, ISO-8859-15, WINDOWS-1252", true), "UTF-8//IGNORE", $this->db->conn->qstr($this->raAsun, get_magic_quotes_gpc()));
		$recordR["RADI_DESC_ANEX"]	= $this->db->conn->qstr($this->descAnex);
		$recordR["RADI_DEPE_RADI"]= $this->radiDepeRadi;
		$recordR["RADI_USUA_ACTU"]=$this->radiUsuaActu;
		$recordR["CARP_CODI"] = $this->carpCodi;
		$recordR["CARP_PER"]  = 0;
		$recordR["RADI_NUME_RADI"] = $newRadicado;
		$recordR["TRTE_CODI"] = $this->trteCodi;
		$recordR["RADI_FECH_RADI"] = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
		$recordR["RADI_DEPE_ACTU"] = $this->radiDepeActu;
		$recordR["TDOC_CODI"] = $this->tdocCodi;
		$recordR["TDID_CODI"] = $this->tdidCodi;
		if(!$usNivel) $usNivel=1;
		$recordR["CODI_NIVEL"]= $usNivel;
		if(trim($this->radiPath) and trim($this->radiPath)!='NULL' and trim($this->radiPath)!='null' ){
		 $recordR["RADI_PATH"] = "'".$this->radiPath ."'";
		}
		$recordR["USUA_DOC"] = $this->usuaDoc;
		
		$whereNivel = "";
        //Comentariado por HLP.
		$insertSQL = $this->db->insert("RADICADO", $recordR, "true");
		/*$insertSQL = $this->db->conn->Replace("RADICADO",
                                                $recordR,
                                                "RADI_NUME_RADI",
												false); */
 
    	$errorInsert = "";    	
		if(!$insertSQL) {
			//echo "ErrorSQL:". htmlentities($this->db->querySql) ." ------ ".$this->db->ExecuteSql." -- ";
			//echo " alert('<!-- ". $this->db->querySql ." -->'); ";
			$this->errorNewRadicado = "Error Sql:". $this->db->querySql ."";
			$newRadicado = "-1" ; //$this->db->querySql;
		}
		//$this->db->conn->CommitTrans();
		//$this->db->conn->debug = False;
		return $newRadicado;
	}

  function updateRadicado($radicado, $radPathUpdate = null) {
		if($this->radiCuentai)  $recordR["RADI_CUENTAI"] = $this->radiCuentai;
		if($this->eespCodi)     $recordR["EESP_CODI"] 	 = $this->eespCodi;
		if($this->mrecCodi)     $recordR["MREC_CODI"] 	 = $this->mrecCodi;
		if($this->radiFechOfic) $recordR["RADI_FECH_OFIC"] = $this->radiFechOfic;
		if($this->radiPais)     $recordR["RADI_PAIS"]    = "'".$this->radiPais."'";
		//if($this->raAsun)       $recordR["RA_ASUN"]      = $this->db->conn->qstr($this->raAsun);
		if($this->raAsun)       $recordR["RA_ASUN"]      = iconv(mb_detect_encoding($this->raAsun,"UTF-8, ISO-8859-1, ISO-8859-15, WINDOWS-1252", true), "ISO-8859-1//IGNORE", $this->db->conn->qstr($this->raAsun, get_magic_quotes_gpc()));
		if($this->descAnex)     $recordR["RADI_DESC_ANEX"]= $this->db->conn->qstr($this->descAnex);
		if($this->trteCodi)     $recordR["TRTE_CODI"]	 = $this->trteCodi;
		if($this->tdidCodi)     $recordR["TDID_CODI"]	 = $this->tdidCodi;
		$recordR["RADI_NUME_RADI"] = $radicado;
		//$recordR["SGD_APLI_CODI"]= $this->sgd_apli_codi;
        if($this->usuaDoc)      $recordR["USUA_DOC"]     = $this->usuaDoc;

		// Linea para realizar radicacion Web de archivos pdf
		if(!empty($radPathUpdate) && $radPathUpdate != ""){
			$archivoPath = explode(".", $radPathUpdate);
			// Sacando la extension del archivo
			$extension = array_pop($archivoPath);
			if($extension == "pdf"){
				$recordR["radi_path"] = "'" . $radPathUpdate . "'";
			}
		}
		//$this->db->conn->debug = true;
		$insertSQL = $this->db->conn->Replace("RADICADO", $recordR, "RADI_NUME_RADI", false);
		return $insertSQL;
  }

    /** FUNCION DATOS DE UN RADICADO
    * Busca los datos de un radicado.
    * @param $radicado int Contiene el numero de radicado a Buscar
    * @return array con los datos del radicado
    * Fecha de creacion: 29-Agosto-2006
    * Creador: Supersolidaria
    * Fecha de modificaci?n:
    * Modificador:
    */
    function getDatosRad( $radicado ) {
        $query  = 'SELECT RAD.RADI_FECH_RADI, RAD.RADI_PATH, TPR.SGD_TPR_DESCRIP,';
        $query .= ' RAD.RA_ASUN, RAD.TDOC_CODI';
        $query .= ' FROM RADICADO RAD';
        $query .= ' LEFT JOIN SGD_TPR_TPDCUMENTO TPR ON TPR.SGD_TPR_CODIGO = RAD.TDOC_CODI';
        $query .= ' WHERE RAD.RADI_NUME_RADI = '.$radicado;
        // print $query;
        $rs = $this->db->conn->Execute( $query );

        $arrDatosRad['fechaRadicacion'] = $rs->fields['RADI_FECH_RADI'];
        $arrDatosRad['ruta'] = $rs->fields['RADI_PATH'];
        $arrDatosRad['tipoDocumento'] = $rs->fields['SGD_TPR_DESCRIP'];
        $arrDatosRad['asunto'] = $rs->fields['RA_ASUN'];
	$arrDatosRad['codtdoc'] = $rs->fields['TDOC_CODI'];
        return $arrDatosRad;
    }
    /**
     * Metodo que inserta direcciones de un radicado.
     * Usa la tabla SGD_DIR_DRECCIONES
     * @autor 12/2009 Fundacion Correlibre
     *        07/2009 adaptacion DNP por Jairo Losada
     * @version Orfeo 3.8.0
     * @param $tipoAccion integer Indica 0--> es un parametro de Radicado Nuevo o 1-> Que es una modificacion a la Existente.
     **/
   function insertDireccion($radiNumeRadi, $dirTipo,$tipoAccion){
      if($tipoAccion==0) {
       $nextval = $this->db->conn->nextId("SEC_DIR_DIRECCIONES");
       $this->dirCodigo = $nextval;
      }
      $this->dirTipo = $dirTipo;
      $tpRad = substr($radiNumeRadi, -1);
      $record = array();
      if($this->trdCodigo) $record['SGD_TRD_CODIGO'] = $this->trdCodigo;
      if($this->grbNombresUs) $record['SGD_DIR_NOMREMDES'] = $this->grbNombresUs;
      if($this->ccDocumento) $record['SGD_DIR_DOC']    = $this->ccDocumento;
      if($this->muniCodi) $record['MUNI_CODI']      = $this->muniCodi;
      if($this->dpto_tmp1) $record['DPTO_CODI']      = $this->dpto_tmp1;
      if($this->idPais) $record['ID_PAIS']        = $this->idPais;
      if($this->idCont) $record['ID_CONT']        = $this->idCont;
      if($this->funCodigo) $record['SGD_DOC_FUN']    = $this->funCodigo;
      if($this->oemCodigo) $record['SGD_OEM_CODIGO'] = $this->oemCodigo;
      if($this->ciuCodigo)$record['SGD_CIU_CODIGO'] = $this->ciuCodigo;
      if($this->espCodigo) $record['SGD_ESP_CODI']   = $this->espCodigo;
      $record['RADI_NUME_RADI'] = $radiNumeRadi;
      //$record['SGD_SEC_CODIGO'] = 0;
      if($this->direccion) $record['SGD_DIR_DIRECCION'] = $this->direccion;
      if($this->codpostal) $record['SGD_DIR_CODPOSTAL'] = $this->codpostal;
      if($this->dirTelefono) $record['SGD_DIR_TELEFONO'] = $this->dirTelefono;
      if($this->dirMail) $record['SGD_DIR_MAIL']   = $this->dirMail;
      if($this->dirTipo and $tipoAccion==0) $record['SGD_DIR_TIPO']   = $this->dirTipo;
      if($this->dirCodigo) $record['SGD_DIR_CODIGO'] = $this->dirCodigo;
      if($this->dirNombre) $record['SGD_DIR_NOMBRE'] = $this->dirNombre;
      if ($tpRad == 2) {
          if ($this->dirFrmEnvio)
              $record['MREC_CODI'] = $this->dirFrmEnvio;
      } else {
          if ($this->dirFrmEnvio)
              $record['SGD_FENV_CODIGO'] = $this->dirFrmEnvio;
      }
      $ADODB_COUNTRECS = true;
      //$db->conn->debug = true;
      //$insertSQL = $this->db->insert("SGD_DIR_DRECCIONES", $record, "true");
	  if($tipoAccion==0){
		$insertSQL = $this->db->conn->Replace(	"SGD_DIR_DRECCIONES",
                                                $record,
                                                array('RADI_NUME_RADI','SGD_DIR_TIPO'),
                                                $autoquote = false);
		$insertSQL = "ddddddddd ddccccwww ";
	  }else{
	  	$recordWhere['RADI_NUME_RADI'] = $radiNumeRadi;	
		$recordWhere['SGD_DIR_TIPO']   = $dirTipo;	
		$insertSQL = $this->db->update("SGD_DIR_DRECCIONES",
                                                $record,
                                                $recordWhere);
	  }
	  
      if($insertSQL == 0) {
			  $this->errorNewRadicado .= "<hr><b><font color=red>Error no se inserto sobre sgd_dir_drecciones<br>SQL:". $this->db->querySql .">> $insertSQL </font></b><hr>";
			  $insertSQL =-1;
		  }else{
			  $this->errorNewRadicado .= "<hr><b><font color=green>0: Ok </font></b><hr>";
			  $insertSQL =1;
		  }
		  
      return $insertSQL;
    } 
    
    
	

	function getRadicadoSuifp($radiNumeRadi){
            
            $query  = ' SELECT RAD.RADI_NUME_SOLICITUD AS SOL1, RAD1.RADI_NUME_SOLICITUD AS SOL2';
            $query .= ' FROM RADICADO RAD';
            $query .= ' LEFT JOIN RADICADO RAD1 ON RAD1.RADI_NUME_RADI = RAD.RADI_NUME_DERI and RAD1.RADI_NUME_SOLICITUD is not null';
            $query .= ' WHERE RAD.RADI_NUME_RADI = '.$radiNumeRadi;
            // print $query;
            $rs = $this->db->conn->Execute( $query );
            if($rs->fields['SOL1'] || $rs->fields['SOL2'] )
                return true;
            else 
                return false;
        }

} // Fin de Class Radicacion
?>
