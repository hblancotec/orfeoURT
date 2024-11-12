<?php
include_once($ruta_raiz . "/_conf/constantes.php");
include_once(ORFEOPATH . "include/tx/Historico.php");

class Tx extends Historico {
  /** Aggregations: */
  /** Compositions: */
   /*** Attributes: ***/
	 /** 
   * Clase que maneja los Historicos de los documentos
   *
   * @param int     Dependencia Dependencia de Territorial que Anula
   * @param number  usuaDocB    Documento de Usuario
   * @param number  depeCodiB   Dependencia de Usuario Buscado
   * @param varchar usuaNombB   Nombre de Usuario Buscado
   * @param varcahr usuaLogin   Login de Usuario Buscado
   * @param number	usNivelB	Nivel de un Ususairo Buscado..
   * @db 	Objeto  conexion
   * @access public
   */
 var  $db;
/**
  * Variable que guarda el documento del que hace la transaccion
  */ 
 var $usuaDoc;
  function __construct($db) {
/**
  * Constructor de la clase Historico
	* @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
    1
	*
	*/
	$this->db = $db;
	$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
 }
 /**
  * Metodo que trae los datos principales de un usuario a partir del codigo y la dependencia
  *
  * @param number $codUsuario
  * @param number $depeCodi
  *
  */
 function datosUs($codUsuario,$depeCodi) {
 		$sql = "SELECT USUA.USUA_DOC,
                        USUA.USUA_LOGIN,
                        USUA.CODI_NIVEL,
                        USUA.USUA_NOMB
                FROM USUARIO USUA,
                        SGD_USD_USUADEPE USD
                WHERE USD.DEPE_CODI=$depeCodi AND
                        USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                        USUA.USUA_DOC = USD.USUA_DOC AND
                        USUA_CODI=$codUsuario";
	// Busca el usuario Origen para luego traer sus datos.
	$rs = $this->db->conn->Execute($sql);
	//$usNivel = $rs->fields["CODI_NIVEL"];
	//$nombreUsuario = $rs->fields["USUA_NOMB"];
	$this->usNivelB = $rs->fields['CODI_NIVEL'];
	$this->usuaNombB = $rs->fields['USUA_NOMB'];
	$this->usuaDocB = $rs->fields['USUA_DOC'];
        $this->usuaDoc = $rs->fields['USUA_DOC'];
 }

    function informar($radicados,
                    $loginOrigen,
                    $depDestino,
                    $depOrigen,
                    $codUsDestino,
                    $codUsOrigen,
                    $observa,
                    $idenviador = null) {
	$whereNivel = "";
	$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
 		$sql = "SELECT USUA.USUA_DOC,
                        USUA.USUA_LOGIN,
                        USUA.CODI_NIVEL,
                        USUA.USUA_NOMB
                FROM USUARIO USUA,
                        SGD_USD_USUADEPE USD
                WHERE USD.DEPE_CODI=$depDestino AND
                        USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                        USUA.USUA_DOC = USD.USUA_DOC AND
                        USUA.USUA_CODI = $codUsDestino";

	// Busca el usuario Origen para luego traer sus datos.
	$rs         = $this->db->conn->Execute($sql);
	$usNivel    = $rs->fields["CODI_NIVEL"];
	$usLoginDestino = $rs->fields["USUA_LOGIN"];
	$nombreUsuario  = $rs->fields["USUA_NOMB"];
	$docUsuarioDest = $rs->fields["USUA_DOC"];
	$codTx = 8;
        
        $this->datosUs($codUsOrigen,$depOrigen);
        $usuaDoc = $this->usuaDoc;
	if($tomarNivel=="si") {
		$whereNivel = ",CODI_NIVEL = $usNivel";
	}
	$codTx = 8;
	$observa = "A: $usLoginDestino - $observa";
	if(!$observacion) $observacion = $observa;

	$tmp_rad = array();
	$informaSql = true;
	
	foreach($radicados as $k=>$noRadicado) {
		if (strstr($noRadicado,'-'))	$tmp = explode('-',$noRadicado);
		else $tmp = $noRadicado;
		if (is_array($tmp))
			$record["RADI_NUME_RADI"] = $tmp[1];
		else
			$record["RADI_NUME_RADI"] = $noRadicado;
		# Asignar el valor de los campos en el registro
		# Observa que el nombre de los campos pueden ser mayusculas o minusculas
		$record["DEPE_CODI"] = $depDestino;
		$record["USUA_CODI"] = $codUsDestino;
		if($idenviador) $record["INFO_CODI"] = $idenviador;
                if(!$idenviador and $usuaDoc) $record["INFO_CODI"] = $usuaDoc;
		$record["INFO_DESC"] = "'$observacion '";
		$record["USUA_DOC"] = "'$docUsuarioDest'";
		$record["INFO_FECH"] = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);

		# Mandar como parametro el recordset vacio y el arreglo conteniendo los datos a insertar
		# a la funcion GetInsertSQL. Esta procesara los datos y regresara un enunciado SQL
		# para procesar el INSERT.
		$informaSql = $this->db->conn->Replace("INFORMADOS",
                                            $record,
                                            array('RADI_NUME_RADI', 'INFO_CODI', 'USUA_DOC'),
                                            false);
		$tmp_rad[] = $record["RADI_NUME_RADI"];
		if ($informaSql == 0)	break;
	}

	//print_r($tmp_rad);
	$this->insertarHistorico($tmp_rad,
                                $depOrigen,
                                $codUsOrigen,
                                $depDestino,
                                $codUsDestino,
                                $observa,
                                $codTx);
	return $nombreUsuario;
}

function borrarInformado($radicados,
                            $loginOrigen,
                            $depDestino,
                            $depOrigen,
                            $codUsDestino,
                            $codUsOrigen,
                            $observa) {
    $tmp_rad = array();
	$deleteSQL = true;
	foreach($radicados as $k=>$noRadicado){
		
		// Borrar el informado seleccionado
		$tmp = explode('-',$noRadicado);
		$record["RADI_NUME_RADI"] = $tmp[1];
		$record["USUA_CODI"] = $codUsOrigen;
		$record["DEPE_CODI"] = $depOrigen;
        $sql = "DELETE FROM INFORMADOS
                WHERE RADI_NUME_RADI=".$tmp[1]." and 
                    USUA_CODI=".$codUsOrigen." and 
                    DEPE_CODI=".$depOrigen . $wtmp;
		$deleteSQL = $this->db->conn->Execute("DELETE FROM INFORMADOS
                WHERE RADI_NUME_RADI=".$tmp[1]." and 
                    USUA_CODI=".$codUsOrigen." and 
                    DEPE_CODI=".$depOrigen . $wtmp);
		if ($deleteSQL)	$tmp_rad[] = $record["RADI_NUME_RADI"];
		else break;
	}

	$codTx = 7;
	if ($deleteSQL)
	{	$this->insertarHistorico($tmp_rad,
                                    $depDestino,
                                    $codUsDestino,
                                    $depOrigen,
                                    $codUsOrigen,
                                    $observa,
                                    $codTx,
                                    $observa);
		return $tmp_rad;
	}
	else return $deleteSQL;
}


  function cambioCarpeta( $radicados, $usuaLogin,$carpetaDestino,$carpetaTipo,$tomarNivel,$observa) {
 		$whereNivel = "";
		$sql = "SELECT b.USUA_DOC,
                        b.USUA_LOGIN,
                        b.CODI_NIVEL,
                        USD.DEPE_CODI,
                        b.USUA_CODI,
                        b.USUA_NOMB
				FROM USUARIO b, SGD_USD_USUADEPE USD
				WHERE b.USUA_LOGIN = '$usuaLogin' AND 
                        b.USUA_LOGIN = USD.USUA_LOGIN AND
                        b.USUA_DOC = USD.USUA_DOC AND
                        USD.SGD_USD_SESSACT = 1";
		// Busca el usuario Origen para luego traer sus datos.
        // Ejecuta la busqueda
		$rs = $this->db->conn->Execute($sql);
		$usNivel    = $rs->fields['CODI_NIVEL'];
		$depOrigen  = $rs->fields['DEPE_CODI'];
		$codUsOrigen= $rs->fields['USUA_CODI'];
		$nombOringen= $rs->fields['USUA_NOMB'];
        
		if($tomarNivel=="si") {
			$whereNivel = ",CODI_NIVEL=$usNivel";
		}
		
        $codTx = "10";
		$radicadosIn = join(",",$radicados);
		$sql = "update radicado
					set CARP_CODI = $carpetaDestino,
                        CARP_PER = $carpetaTipo,
                        radi_fech_agend = null,
                        radi_agend = null
					  $whereNivel
				 where RADI_NUME_RADI in($radicadosIn)";

		$rs = $this->db->conn->Execute($sql);
		$retorna = 1;
		if(!$rs) {
			echo "<center><font color=red>Error en el Movimiento ... A ocurrido un error y no se ha podido realizar la Transaccion</font> <!-- $sql -->";
			$retorna = -1;
		}
		if($retorna!=-1) {
			$this->insertarHistorico($radicados,
                                        $depOrigen,
                                        $codUsOrigen,
                                        $depOrigen,
                                        $codUsOrigen,
                                        $observa,
                                        $codTx);
		}
		return $retorna;
  }

  function reasignar($radicados,
                        $loginOrigen,
                        $depDestino,
                        $depOrigen,
                        $codUsDestino,
                        $codUsOrigen,
                        $tomarNivel,
                        $observa,
                        $codTx,
                        $carp_codi) {
	$whereNivel = "";
    $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $sql = "SELECT 
                USUA.USUA_DOC,
                USUA.USUA_LOGIN,
                USUA.CODI_NIVEL,
                USUA.USUA_NOMB
            FROM 
                USUARIO USUA,
                SGD_USD_USUADEPE USD
            WHERE 
                USD.DEPE_CODI   = $depDestino AND
                USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                USUA.USUA_DOC   = USD.USUA_DOC AND
                USUA.USUA_CODI  = $codUsDestino";
    
	// Busca el usuario Origen para luego traer sus datos.
	$rs = $this->db->conn->Execute($sql);

    $usNivel       = $rs->fields['CODI_NIVEL'];
    $nombreUsuario = $rs->fields['USUA_NOMB'];
    $docUsuaDest   = $rs->fields['USUA_DOC'];
    
	if ($tomarNivel=="si")	{
		$whereNivel = ",CODI_NIVEL=$usNivel";
	}
    
	$radicadosIn = join(",",$radicados);
	$proccarp    = "Reasignar";
	$carp_per    = 0;

	$isql  = "UPDATE
                    RADICADO SET
                      RADI_USU_ANTE   = '$loginOrigen',
                      RADI_DEPE_ACTU  = $depDestino,
                      RADI_USUA_ACTU  = $codUsDestino,
                      CARP_CODI       = $carp_codi,
                      CARP_PER        = $carp_per,
                      RADI_LEIDO      = 0,
                      radi_fech_agend = null,
                      radi_agend      = null
                      $whereNivel
                   WHERE
                       RADI_DEPE_ACTU     = $depOrigen
                       AND radi_usua_actu = $codUsOrigen
                       AND RADI_NUME_RADI in($radicadosIn)";
                      
    $this->db->conn->Execute($isql);        
       
	// Busca el usuario DESTINO!!! para luego traer sus datos.
    $sql = "SELECT 
               USUA.USUA_NOMB
            FROM USUARIO USUA,
               SGD_USD_USUADEPE USD
            WHERE 
               USD.DEPE_CODI=$depDestino AND
               USUA.USUA_LOGIN = USD.USUA_LOGIN AND
               USUA.USUA_DOC = USD.USUA_DOC AND
               USUA.USUA_CODI = $codUsDestino";

	$rs            = $this->db->conn->Execute($sql);
	$nombreUsuario = $rs->fields["USUA_NOMB"];                

    $observa = "! Reasigna ! - " . "A: " . $nombreUsuario . " - " . $observa;
        
 	$this->insertarHistorico($radicados,
                                $depOrigen,
                                $codUsOrigen,
                                $depDestino,
                                $codUsDestino,
                                $observa,
                                $codTx);
 	return $nombreUsuario;
  }
  
  function reasignarOtros($radicados,
                        $loginOrigen,
                        $depDestino,
                        $depOrigen,
                        $codUsDestino,
                        $codUsOrigen,
                        $tomarNivel,
                        $observa,
                        $codTx,
                        $carp_codi) {
	$whereNivel = "";
    $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

 	$sql = "SELECT USUA.USUA_DOC,
                        USUA.USUA_LOGIN,
                        USUA.CODI_NIVEL,
                        USUA.USUA_NOMB
                FROM USUARIO USUA,
                        SGD_USD_USUADEPE USD
			    WHERE USD.DEPE_CODI = $depDestino AND
                        USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                        USUA.USUA_DOC = USD.USUA_DOC AND
                        USUA.USUA_CODI= $codUsDestino";
    
	// Busca el usuario Origen para luego traer sus datos.
	$rs = $this->db->conn->Execute($sql);
	//$usNivel = $rs->fields["CODI_NIVEL"];
	//$nombreUsuario = $rs->fields["USUA_NOMB"];
	$usNivel = $rs->fields['CODI_NIVEL'];
	$nombreUsuario = $rs->fields['USUA_NOMB'];
	$docUsuaDest = $rs->fields['USUA_DOC'];
    
	if ($tomarNivel=="si")	{
		$whereNivel = ",CODI_NIVEL=$usNivel";
	}
    
	$radicadosIn = join(",",$radicados);
	$proccarp = "Reasignar";
	$carp_per = 0;
	$isql = "UPDATE radicado set
				  RADI_USU_ANTE = '$loginOrigen',
                  RADI_DEPE_ACTU = $depDestino,
                  RADI_USUA_ACTU = $codUsDestino,
                  CARP_CODI = $carp_codi,
                  CARP_PER = $carp_per,
                  RADI_LEIDO = 0,
                  radi_fech_agend = null,
                  radi_agend = null
				  $whereNivel
			 where radi_depe_actu=$depOrigen
			 	   AND radi_usua_actu=$codUsOrigen
				   AND RADI_NUME_RADI in($radicadosIn)";
	$this->db->conn->Execute($isql);
 	$this->insertarHistorico($radicados,
                                $depOrigen,
                                $codUsOrigen,
                                $depDestino,
                                $codUsDestino,
                                $observa,
                                $codTx);
 	return $nombreUsuario;
  }
  
//Modificado por Fabian Mauricio Losada
function archivar( $radicados, $loginOrigen,$depOrigen,$codUsOrigen,$observa) {
	//$whereNivel = "";
	$falg = 0;
	### CONSULTA PARA SABER QUIEN PRIVATIZO EL RADICADO Y QUIEN LO PUEDE DESPRIVATIZAR
	$sql = "SELECT	R.SGD_SPUB_CODIGO,
					R.RADI_USUA_PRIVADO,
					U.DEPE_CODI
			FROM	RADICADO R
					JOIN USUARIO U ON U.USUA_DOC = R.RADI_USUA_PRIVADO
			WHERE RADI_NUME_RADI = $radicados[0]";
	$rs = $this->db->conn->Execute($sql);
		
	$estado = $rs->fields['SGD_SPUB_CODIGO']; //0-Publico  1-Privado para la Dependencia   2-Privado para el usuario
	$privatizo = $rs->fields['RADI_USUA_PRIVADO'];
	$depPriv = $rs->fields['DEPE_CODI'];
	
	if ($_POST['desprivatiza'] == 1){ //El radicado viene con la bandera de desprivatizar
		if($estado == 2){ // El radicado es privado para el usuario y solo el que privatizo puede desprivatizar
			if($_SESSION['usua_doc'] == $privatizo){
				$flag = 1;
			}
		}
		elseif ($estado == 1){ // El radicado es privado para la dependencia y solo usuarios de la misma dependencia que privatizo, pueden desprivatizar.
			if($_SESSION['dependencia'] == $depPriv){
				$flag = 1;
			}
		} 
	}
	  
	$tipoRad = substr($radicados[0], -1);
	if ($flag == 1 or $tipoRad == 9 ){ // Si cumple las condiciones anteriores o el radicado es tipo 9
		$whereSpub = ", SGD_SPUB_CODIGO = 0";
		$observa = $observa . "- Se cambia el estado de privacidad del radicado a P�blico";
	}
	else{
		$whereSpub = "";
	}
			
	$radicadosIn = join(",",$radicados);
	$carp_codi=substr($depOrigen,0,2);
	$carp_per = 0;
	$carp_codi = 2;
	$isql ="UPDATE	RADICADO SET
					RADI_USU_ANTE = '$loginOrigen',
					RADI_DEPE_ACTU = 999,
					RADI_USUA_ACTU = 1,
					CARP_CODI = $carp_codi,
					CARP_PER = $carp_per,
					RADI_LEIDO = 0,
					radi_fech_agend = null,
					radi_agend = null,
					CODI_NIVEL = 1
					$whereSpub
			WHERE	RADI_DEPE_ACTU = $depOrigen
					AND RADI_USUA_ACTU = $codUsOrigen
					AND RADI_NUME_RADI in ($radicadosIn)";
	$this->db->conn->Execute($isql); // Ejecuta la busqueda
	$this->insertarHistorico($radicados,  $depOrigen , $codUsOrigen, 999, 1, $observa, 13);
	return $isql;
}
  // Hecho por Fabian Mauricio Losada
   function nrr( $radicados, $loginOrigen,$depOrigen,$codUsOrigen,$observa) {
  		$whereNivel = "";
		$radicadosIn = join(",",$radicados);
		$carp_codi=substr($depOrigen,0,2);
		$carp_per = 0;
		$carp_codi = 2;
		$isql = "update radicado
					set RADI_USU_ANTE='$loginOrigen',
                        RADI_DEPE_ACTU=999,
                        RADI_USUA_ACTU=1,
                        CARP_CODI=$carp_codi,
                        CARP_PER=$carp_per,
                        RADI_LEIDO=0,
                        radi_fech_agend=null,
                        radi_agend=null,
                        CODI_NIVEL=1,
                        SGD_SPUB_CODIGO=0,
                        RADI_NRR=1
				 where radi_depe_actu=$depOrigen
				 	   AND radi_usua_actu=$codUsOrigen
					   AND RADI_NUME_RADI in($radicadosIn)";
		$this->db->conn->Execute($isql);
		$this->insertarHistorico($radicados,  $depOrigen , $codUsOrigen, 999, 1, $observa, 13);
		return $isql;
  }
  /**
   * Nueva Funcion para agendar.
   * Este metodo permite programar un radicado para una fecha especifica, el arreglo con la version anterior
   * , es que no se borra el agendado cuando el radicado sale del usuario actual.
   *
   * @author JAIRO LOSADA JUNIO 2006
   * @version 3.5.1
   *
   * @param array int $radicados
   * @param string $loginOrigen
   * @param integer $depOrigen
   * @param integer $codUsOrigen
   * @param string $observa
   * @param date $fechaAgend
   * @return boolean
   */
  function agendar($radicados, $loginOrigen,$depOrigen,$codUsOrigen,$observa, $fechaAgend) {
	$whereNivel = "";
	$radicadosIn = join(",",$radicados);
	$carp_codi = substr($depOrigen,0,2);
	$carp_per = 1;
	$sqlFechaAgenda = $this->db->conn->DBDate($fechaAgend);
	$this->datosUs($codUsOrigen,$depOrigen);
	$usuaDocAgen = $this->usuaDocB;
	foreach($radicados as $noRadicado) {
		// Busca el usuario Origen para luego traer sus datos.
		$rad = array();
		$observa = "Agendado para el $fechaAgend - " . $observa;
		if($usuaDocAgen) {
			$record["RADI_NUME_RADI"] = $noRadicado;
			$record["DEPE_CODI"] = $depOrigen;
			$record["SGD_AGEN_OBSERVACION"] = "'$observa '";
			$record["USUA_DOC"] = "'$usuaDocAgen'";
			$record["SGD_AGEN_FECH"] = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
			$record["SGD_AGEN_FECHPLAZO"] = $sqlFechaAgenda;
			$record["SGD_AGEN_ACTIVO"] = 1;
			$insertSQL = $this->db->insert("SGD_AGEN_AGENDADOS", $record, "true");
			$this->insertarHistorico($radicados,
                                        $depOrigen,
                                        $codUsOrigen,
                                        $depOrigen,
                                        $codUsOrigen,
                                        $observa,
                                        14);
		}
	}
		return $isql;
  }
  /**
   * Metodo que sirve para sacar uno o varios radicados de agendado
   *
   * @param array $radicados
   * @param string $loginOrigen
   * @param integer $depOrigen
   * @param integer $codUsOrigen
   * @param string $observa
   * @return string
   */
  function noAgendar( $radicados, $loginOrigen,$depOrigen,$codUsOrigen,$observa) {
  		$this->datosUs($codUsOrigen,$depOrigen);
		$usuaDocAgen = $this->usuaDocB;
  		$whereNivel = "";
		$radicadosIn = join(",",$radicados);
		$carp_codi=substr($depOrigen,0,2);
		$isql = "update sgd_agen_agendados
					set SGD_AGEN_ACTIVO = 0
				 where RADI_NUME_RADI in($radicadosIn)
				   AND USUA_DOC=".$usuaDocAgen;
		$this->db->conn->Execute($isql); // Ejecuta la busqueda
		$this->insertarHistorico($radicados,$depOrigen,$codUsOrigen,$depOrigen,$codUsOrigen, $observa, 15);
		return $isql;
  }

  function devolver( $radicados, $loginOrigen,$depOrigen, $codUsOrigen,$tomarNivel, $observa) {
  	$whereNivel = "";
  	$retorno="";
  	$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	foreach($radicados as $noRadicado) {
		$sql = "SELECT b.USUA_DOC,
                        b.USUA_LOGIN,
                        b.CODI_NIVEL,
                        USD.DEPE_CODI,
                        b.USUA_CODI,
                        b.USUA_NOMB
				FROM RADICADO a,
                        USUARIO b,
                        SGD_USD_USUADEPE USD
				WHERE a.RADI_USU_ANTE = b.USUA_LOGIN AND
                        b.USUA_LOGIN = USD.USUA_LOGIN AND
                        b.USUA_DOC = USD.USUA_DOC AND
                        USD.SGD_USD_SESSACT = 1 AND
                        a.RADI_NUME_RADI = $noRadicado";
		// Busca el usuario Origen para luego traer sus datos.
		$rs = $this->db->conn->Execute($sql);

        if ($rs === false) {
            echo "Error en sentencia SQL";
            echo $this->db->conn->ErrorMsg();
        }
		
		$usNivel = $rs->fields['CODI_NIVEL'];
		$depDestino = $rs->fields['DEPE_CODI'];
		$codUsDestino = $rs->fields['USUA_CODI'];
		$nombDestino = $rs->fields['USUA_NOMB'];
		$rad = array();
		if($codUsDestino) {
			if($tomarNivel=="si") {
				$whereNivel = ",CODI_NIVEL=$usNivel";
			}
			$radicadosIn = join(",",$radicados);
			$proccarp= "Dev. ";
			$carp_codi= 2;
			$carp_per = 0;
			$isql = "update radicado
						set RADI_USU_ANTE='$loginOrigen',
                                RADI_DEPE_ACTU=$depDestino,
                                RADI_USUA_ACTU=$codUsDestino,
                                CARP_CODI=$carp_codi,
                                CARP_PER=$carp_per,
                                RADI_LEIDO=0,
                                radi_fech_agend=null,
                                radi_agend=null
						  $whereNivel
					 where radi_depe_actu=$depOrigen AND
                            radi_usua_actu=$codUsOrigen AND
                            RADI_NUME_RADI = $noRadicado";
			$this->db->conn->Execute($isql);
			$rad[]=$noRadicado;
			$this->insertarHistorico($rad,
                                        $depOrigen,
                                        $codUsOrigen,
                                        $depDestino,
                                        $codUsDestino,
                                        $observa,
                                        12);
			array_splice($rad, 0);
			$retorno=$retorno . "$noRadicado ---> $nombDestino <br>";
		} else {
		    $retorno = $retorno . 
                        "<font color='red'>$noRadicado --> Usuario Anterior no se encuentra o esta inactivo</font><br>";
		}
	}
    return $retorno;
  }

 // manejo de varias asignaciones
 function crea_derivado($radicados,
                        $depDestino,
                        $depOrigen,
                        $codUsDestino,
                        $codUsOrigen,
                        $observa,
                        $codTx) {
     foreach ($radicados as $key=>$radicado) {      
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
 	$sql = "SELECT USUA.USUA_DOC,
                       USUA.USUA_LOGIN,
                       USUA.CODI_NIVEL,
                       USUA.USUA_NOMB
                FROM USUARIO USUA,
                       SGD_USD_USUADEPE USD
                WHERE USD.DEPE_CODI=$depDestino AND
                       USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                       USUA.USUA_DOC = USD.USUA_DOC AND
                       USUA.USUA_CODI = $codUsDestino";

	// Busca el usuario DESTINO!!! para luego traer sus datos.
	
	$rs            = $this->db->conn->Execute($sql);
	$nombreUsuario = $rs->fields["USUA_NOMB"];	
      
   	$record = array();
	$record["RADI_NUME_RADI"] = $radicado;
	$record["area"]           = $depDestino;
	$record["radi_leido"]     = '0';
	$record["usuario"]        = $codUsDestino;
	$record["estatus"]        = "'ACTIVO'";
	$record["fechainicio"]    = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
	$insertSQL = $this->db->insert("SGD_RG_MULTIPLE", $record, "true");
	$observa = "! Derivado ! - " . "A: " . $nombreUsuario . " - " . $observa;
	$this->insertarHistorico($radicados,
                                 $depOrigen,
                                 $codUsOrigen,
                                 $depDestino,
                                 $codUsDestino,
                                 $observa,
                                 $codTx);
    }
    //$this->db->conn->debug=true;
	return $nombreUsuario;
  }
 
function busca_asignados_raiz($radicados, $depDestino, $codUsDestino) {
	$band = false;
    $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $asignar = array();
	foreach($radicados as $temp=>$recordid) {
		if ($recordid) {
	    	$sql = "SELECT id, convert(char(15), RADI_NUME_RADI) as 'Radicado' FROM   radicado 
                    WHERE  RADI_NUME_RADI = $recordid AND RADI_USUA_ACTU = $codUsDestino AND RADI_DEPE_ACTU = $depDestino";
            $result = $this->db->conn->Execute($sql);
			while($result && !$result->EOF) {
	        	$asignar[] = $result->fields["Radicado"];
	        	$band= true;
				$result->MoveNext(); 
	    	}
		}
	}
    return $band;
  }
  
  function reasigna_derivado($radicados,
                             $depDestino,
                             $depOrigen,
                             $codUsDestino,
                             $codUsOrigen,
                             $observa,
                             $codTx) {
      $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
      $radicadosIn = join(",",$radicados);
      $leido = 0;
      
      $okReplace = $this->db->conn->Replace('SGD_RG_MULTIPLE',array('area'=>$depDestino,'usuario'=>$codUsDestino,'radi_leido'=>$leido,'radi_nume_radi'=>$radicadosIn, 'estatus'=>'\'ACTIVO\''),array('area','usuario','radi_nume_radi'),false);

      $sql = "SELECT USUA.USUA_NOMB
                FROM USUARIO USUA,
                       SGD_USD_USUADEPE USD
                WHERE USD.DEPE_CODI=$depDestino AND
                       USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                       USUA.USUA_DOC = USD.USUA_DOC AND
                       USUA.USUA_CODI = $codUsDestino";

	// Busca el usuario DESTINO!!! para luego traer sus datos.
	$rs            = $this->db->conn->Execute($sql);
	$nombreUsuario = $rs->fields["USUA_NOMB"];
        
        
        $observa = "! Reasigna Derivado ! - " . "A: " . $nombreUsuario . " - " . $observa;
 	$this->insertarHistorico($radicados,
                                 $depOrigen,
                                 $codUsOrigen,
                                 $depDestino,
                                 $codUsDestino,
                                 $observa,
                                 $codTx);
        
 	return $nombreUsuario;
  }
  
  function reasigna_derivado_crea($radicados,
      $depDestino,
      $depOrigen,
      $codUsDestino,
      $codUsOrigen,
      $observa,
      $codTx) {
          foreach ($radicados as $key=>$radicado) {
              $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
              $sql = "SELECT USUA.USUA_DOC,
                       USUA.USUA_LOGIN,
                       USUA.CODI_NIVEL,
                       USUA.USUA_NOMB
                FROM USUARIO USUA,
                       SGD_USD_USUADEPE USD
                WHERE USD.DEPE_CODI=$depDestino AND
                       USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                       USUA.USUA_DOC = USD.USUA_DOC AND
                       USUA.USUA_CODI = $codUsDestino";
              
              // Busca el usuario DESTINO!!! para luego traer sus datos.
              
              $rs            = $this->db->conn->Execute($sql);
              $nombreUsuario = $rs->fields["USUA_NOMB"];
              
              $record = array();
              $record["RADI_NUME_RADI"] = $radicado;
              $record["area"]           = $depDestino;
              $record["radi_leido"]     = '0';
              $record["usuario"]        = $codUsDestino;
              $record["estatus"]        = "'ACTIVO'";
              $record["fechainicio"]    = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
              $insertSQL = $this->db->insert("SGD_RG_MULTIPLE", $record, "true");
              $observa = "! Reasigna Derivado ! - " . "A: " . $nombreUsuario . " - " . $observa;
              $this->insertarHistorico($radicados,
                  $depOrigen,
                  $codUsOrigen,
                  $depDestino,
                  $codUsDestino,
                  $observa,
                  $codTx);
          }
          //$this->db->conn->debug=true;
          return $nombreUsuario;
  }
  
  function finaliza_derivado($radicados,
                             $depDestino,
                             $depOrigen,
                             $codUsDestino,
                             $codUsOrigen,
                             $observa,
                             $codTx) {
      $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
      $radicadosIn = join(",",$radicados);
      $isql = "UPDATE SGD_RG_MULTIPLE set				 				  
                      estatus = 'FINALIZADO'      
               where  area    =  $depDestino   AND 
		      usuario =  $codUsDestino AND 		      
		      RADI_NUME_RADI in($radicadosIn)";
	$this->db->conn->Execute($isql);

	$sql = "SELECT USUA.USUA_NOMB
			FROM USUARIO USUA,
				   SGD_USD_USUADEPE USD
			WHERE USD.DEPE_CODI=$depDestino AND
				   USUA.USUA_LOGIN = USD.USUA_LOGIN AND
				   USUA.USUA_DOC = USD.USUA_DOC AND
				   USUA.USUA_CODI = $codUsDestino";

	// Busca el usuario DESTINO!!! para luego traer sus datos.
	$rs            = $this->db->conn->Execute($sql);
	$nombreUsuario = $rs->fields["USUA_NOMB"];

    $obs = "Finaliza derivado en: " . $nombreUsuario. " por que el usuario ya tenia uno. ".$observa;

	if ($codTx == 13){
		$obs = "Finaliza derivado: " .$observa;
	}
	
 	$this->insertarHistorico($radicados,
                                 $depOrigen,
                                 $codUsOrigen,
                                 $depDestino,
                                 $codUsDestino,
                                 $obs,
                                 $codTx);        
 	return $nombreUsuario;
  }
 
  function finaliza_derivado_crea($radicados,
      $depDestino,
      $depOrigen,
      $codUsDestino,
      $codUsOrigen,
      $observa,
      $codTx) {
          $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
          $radicadosIn = join(",",$radicados);
          $isql = "UPDATE SGD_RG_MULTIPLE set
                      estatus = 'FINALIZADO'
               where  area    =  $depDestino   AND
		      usuario =  $codUsDestino AND
		      RADI_NUME_RADI in($radicadosIn)";
          $this->db->conn->Execute($isql);
          
          $sql = "SELECT USUA.USUA_NOMB
			FROM USUARIO USUA,
				   SGD_USD_USUADEPE USD
			WHERE USD.DEPE_CODI=$depDestino AND
				   USUA.USUA_LOGIN = USD.USUA_LOGIN AND
				   USUA.USUA_DOC = USD.USUA_DOC AND
				   USUA.USUA_CODI = $codUsDestino";
          
          // Busca el usuario DESTINO!!! para luego traer sus datos.
          $rs            = $this->db->conn->Execute($sql);
          $nombreUsuario = $rs->fields["USUA_NOMB"];
          
          $obs = "Finaliza derivado en: " . $nombreUsuario. " por que el usuario ya tenia uno. ".$observa;
          
          if ($codTx == 13){
              $obs = "Finaliza derivado: " .$observa;
          }
          
          $this->insertarHistorico($radicados,
              $depOrigen,
              $codUsOrigen,
              $depDestino,
              $codUsDestino,
              $obs,
              $codTx);
          
          foreach ($radicados as $key=>$radicado) {
              $record = array();
              $record["RADI_NUME_RADI"] = $radicado;
              $record["area"]           = $depDestino;
              $record["radi_leido"]     = '0';
              $record["usuario"]        = $codUsDestino;
              $record["estatus"]        = "'ACTIVO'";
              $record["fechainicio"]    = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
              $insertSQL = $this->db->insert("SGD_RG_MULTIPLE", $record, "true");
          }
          
          return $nombreUsuario;
  }
  
  function hist_derivado($radicados,
                             $depDestino,
                             $depOrigen,
                             $codUsDestino,
                             $codUsOrigen,
                             $observa,
                             $codTx) {

    $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $radicadosIn = join(",",$radicados);

    $isql = "UPDATE 
                SGD_RG_MULTIPLE set				 				  
                radi_leido = 0
              where 
		      usuario = $codUsDestino AND 		      
		      RADI_NUME_RADI in($radicadosIn)";

	$this->db->conn->Execute($isql);

    $sql = "SELECT 
               USUA.USUA_NOMB
            FROM USUARIO USUA,
               SGD_USD_USUADEPE USD
            WHERE 
               USD.DEPE_CODI=$depDestino AND
               USUA.USUA_LOGIN = USD.USUA_LOGIN AND
               USUA.USUA_DOC = USD.USUA_DOC AND
               USUA.USUA_CODI = $codUsDestino";

	$rs            = $this->db->conn->Execute($sql);
	$nombreUsuario = $rs->fields["USUA_NOMB"];                

    $observa = "! Reasigna derivado ! - " . "A: " . $nombreUsuario . " - " . $observa;

      $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
      $this->insertarHistorico($radicados,
                                 $depOrigen,
                                 $codUsOrigen,
                                 $depDestino,
                                 $codUsDestino,
                                 $observa,
                                 $codTx);
  }

function busca_asignados_derivado($radicados, $depDestino, $codUsDestino) {
	$band = false;
	$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$asignar = array();
	foreach($radicados as $temp=>$recordid) {
		if ($recordid) {
	    	$sql = "SELECT id, convert(char(15), RADI_NUME_RADI) as 'Radicado'
                    FROM   SGD_RG_MULTIPLE
                    WHERE  RADI_NUME_RADI = $recordid AND area = $depDestino AND
			   		usuario = $codUsDestino AND estatus <> 'FINALIZADO' ";
            $result = $this->db->conn->Execute($sql);
	    	while($result && !$result->EOF) {
	    		$asignar[] = $result->fields["Radicado"];
	    		$band = true;
	    		$result->MoveNext(); 
	    	}
		}
	}
	return $band;
}
  
  
  function busca_derivado($radicado, $codUsDestino, $depDestino) {
    $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $existe = "";
    $sql = "SELECT id,
	    convert(char(15), RADI_NUME_RADI) as 'Radicado'
            FROM   SGD_RG_MULTIPLE
            WHERE  RADI_NUME_RADI  = $radicado   AND
	    area            = $depDestino   AND
	    usuario         = $codUsDestino AND
            estatus        <> 'FINALIZADO' ";
            
            $result = $this->db->conn->Execute($sql);
	    
	    while($result && !$result->EOF) {
	        $existe = "ok";
		$result->MoveNext();
	    }
	    
    return $existe;  
  }   
  
}

?>
