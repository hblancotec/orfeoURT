<?php
/**
  * CLASE EXPEDIENTE
  * @author JAIRO LOSADA
  * @copyLeft SuperIntendencia de Servicios Publicos
	* @Licencia GPL licencia publica General
  * @version Orfeo 3.5
  * @param $query    String Variable Usada para almacenar consultas SQL
  * @param $expTerminos Int Almacena los terminos o Dias habiles para ejecucion de un proceso.
  */
class Expediente {
   var $num_expediente;     // Almacena el nume del expediente
   var $estado_expediente;  // Almacena el estado 0 para organizacion y 1
                            // para indicar ke ya esta clasificado fisicamente en archivo
   var $descSerie;
   var $descSubSerie;
   var $descTipoExp;
   var $descFldExp;
   var $codigoFldExp;
   var $exp_titulo;
   var $exp_asunto;
   var $exp_ufisica;
   var $exp_isla;
   var $exp_caja;
   var $exp_estante;
   var $exp_carpeta;
   var $exp_unicon;
   var $exp_archivo;
   var $exp_fechaIni;
   var $exp_fechaFin;
   var $exp_num_carpetas;
   var $expUsuaDoc;
   var $codiSRD;
   var $codiSBRD;
   var $exp_nombresubexp;
   var $db;
   var $descError;
   var $error;
   var $migraEstado;
   var $migraDescri;


/** Variable que ALmacena los dias Habiles de un Proceso
  *	@param $codigoTipoExp int Codigo del tipo de expediente o Tipo de Proceso.
  */

   var $codigoTipoExp;

/** Variable que ALmacena los dias Habiles de un Proceso
  *	@param $expTerminos int Contiene los dias Habiles de Un Proceso.
  */
	var $expTerminos;

/** Variable que ALmacena los dias Habiles de una Etapa Perteneciente a Un proceso
  *	@param $expTerminosP int Contiene los terminos en dias Habiles de la Etapa Actual del Proceso del expediente.
  */
	var $expTerminosP;

/** Variable
  *	@param $expFechaCrea int Almacena Fecha de Creacion del expediente
  */
	// var $expTerminos;
    var $expFechaCrea;

 /** Variable
   * @param $numSubexpediente int Almacena el numero de subexpediente;
   */
    var $numSubexpediente;
/** Variable
   * @param $pAutomatico int Dice si el expediente se puede manejar de forma manual (Los cambios de estado);
   */

    var $pAutomatico;

/**
 * Variable
 * @var array las fases de archivo
 */
 var $faseArchivo = Array(""=>"Archivo de Gesti&oacute;n",0=>"Archivo de Gesti&oacute;n",1=>"Transferencia primaria",2=>"Eliminaci&oacute;n");
    
 /**
 * Variable
 * @var array las fases de archivo
 */
 var $estadoExpediente = Array(""=>"Abierto",0=>"Abierto",1=>"Cerrado");

/** CONSTRUTOR
  * Inicializa la Clase.
  *	@param $db variable contenedora del Cursor. Esta tiene que ser  enviada en el construtor
  */

	function __construct($db) {
		$this->db = $db;
		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	}
  
/** FUNCION QUE RETORNA LA DESCRIPCION DEL ERROR
  * @autor JAIRO H LOSADA CORRELIBRE.ORG
  * @return string Descripcion del error generado
  * 
  */
 
    function getDescError() {
    	return $this->descError;
	}

  
/** FUNCION QUE RETORNA EL CODIGO DE ERROR
 * * @autor JAIRO H LOSADA CORRELIBRE.ORG
 * @return string Descripcion del error generado
 */
	function getError() {
   		return $this->error;
  	}


/** FUNCION CONSULTA EXPEDIENTE
  * Inicializa la Clase.
  *	@param $radicado int Contiene el numero de radicado a Buscar
  * @return string Numero de Expediente que posee el radicado
  */
	function consulta_exp($radicado,$depeCodi=null) {

		switch ($this->db->driver) {
            case 'mssqlnative':
                $radi_nume_radi="convert(varchar(15), e.radi_nume_radi)";
				break;
			default:
                $radi_nume_radi="e.radi_nume_radi";
		}
        // Modificado 15-Agosto-2006 Supersolidaria
        // No tiene en cuenta los expedientes de los que ha sido excluido el radicado (SGD_EXP_ESTADO = 2).
        $filtro = '';
		if(!empty($depeCodi)){
			$filtro	=	"and e.sgd_exp_numero not in
         					(select
	         					a.sgd_exp_numero
	           				from
	           					SGD_SEXP_SECEXPEDIENTES a,
	           					SGD_EXP_EXPEDIENTE b
	           				where
	                			b.radi_nume_radi = $radicado
								and a.SGD_SEXP_ESTADO = 0
	                			and a.sgd_exp_numero = b.sgd_exp_numero
	     						and a.depe_codi <> $depeCodi
	                			and SGD_SEXP_PRIVADO = 1  )";
		}
		$query = "select e.SGD_EXP_NUMERO,
                    e.SGD_EXP_ESTADO,
                    $radi_nume_radi RADI_NUME_RADI
				from SGD_EXP_EXPEDIENTE e INNER JOIN SGD_SEXP_SECEXPEDIENTES b ON e.sgd_exp_numero = b.sgd_exp_numero
				where e.RADI_NUME_RADI = $radicado AND
                     e.SGD_EXP_ESTADO <> 2
                     and b.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas')
                     $filtro"; 	//se utiliza para no mostrar los expedientes privados en
                     			//ver_datosrad.php linea 500
		$rs = $this->db->conn->Execute($query);
		if ($rs->EOF){
		    #echo 'No tiene un Numero de expediente<br>';
			$this->num_expediente = 0;
		 }else{
		   if (!$rs->EOF){
			 $this->num_expediente = $rs->fields['SGD_EXP_NUMERO'];
			 $this->estado_expediente = $rs->fields['SGD_EXP_ESTADO'];
		   }
		}
		return $this->num_expediente;
	 }

//Agregado por CEC 09-02-2012
/** FUNCION CONSULTA NOMBRE DE EXPEDIENTE
  * @param string $nomb_Exp, CAMPO DIGITADO POR EL USUARIO PARA CREAR EL EXPEDIENTE
  * @param integer $depen, CODIGO DE LA DEP DONDE SE CREARA EL EXPEDIENTE
  * @param integer $serie, SERIE EN LA QUE SE VA A CREAR EL EXPEDIENTE
  * @param integer $subSerie, SUB-SERIE EN LA QUE SE VA A CREAR EL EXPEDIENTE
  * @param integer $ano_Exp, ANHO EN EL QUE SE VA A CREAR EL EXPEDIENTE
  * @return integer  1 SI EXISTE UN EXP CON EL MISMO NOMBRE O SI NO EXISTE UNO CON IGUAL NOMBRE 0
  */
	function consulta_nombexp($nomb_Exp,$depen,$serie,$subSerie,$ano_Exp) 
	{	$query ="SELECT	SGD_SEXP_PAREXP1 AS EXPE
				 FROM	SGD_SEXP_SECEXPEDIENTES
				 WHERE	DEPE_CODI = $depen
				 		AND SGD_SRD_CODIGO = $serie
				 		AND SGD_SBRD_CODIGO = $subSerie
				 		AND SGD_SEXP_ANO = $ano_Exp
				 		AND SGD_SEXP_PAREXP1 = '$nomb_Exp'
						AND SGD_SEXP_ESTADO = 'False'";
		$rs = $this->db->conn->Execute($query);
		$exp = $rs->fields['EXPE'];
		(!$exp)? $nombExp=0 : $nombExp=1;
		return $nombExp;
	}	 
//Fin Agregado por CEC 09-02-2012

/**
  * Inserta un Numero de radicado en un Expediete
  * Inicializa la Clase.
  *	@param  $radicado int Contiene el numero de radicado a Buscar
  * @param  $usua_doc String Documento de identificacion de Usuario que realiza la insercion-
  * @param  $usua_codi String Codigo en Orfeo del Usuario que realiza la insercion-
  * @param  $depe_codi String Codigo en Orfeo de la dependencia del  Usuario que realiza la insercion.
  * @param  integer $radicado  Numero de Radicado a Relacionar con el Expediente.
  * @param  $radicado String Numero de Expediente a Relacionar con el Radicado.
  * @param	$expManual String que Contien 1 o 0. "0 es genera secuencia y 1 deja el consecutivo que el usuario ha colocado.
  *	@return string Numero de Expediente que posee el radicado
  */
 
 
 function insertar_expediente($num_expediente,$radicado,$depe_codi,$usua_codi,$usua_doc){	
	
	$query = "	SELECT 
					COUNT(*) AS TOTAL 
				FROM 
					SGD_SEXP_SECEXPEDIENTES e
                WHERE 
					e.SGD_EXP_NUMERO='$num_expediente'
					and e.SGD_SEXP_ESTADO = '0'";

	$rs 	= $this->db->conn->Execute($query);
	$total	= $rs->fields['TOTAL'];	
	
	if ($total>0) {							
		$fecha_hoy 		= Date("d-m-Y");
		$sqlFechaHoy 	= $this->db->conn->DBDate($fecha_hoy);
		$query 			= "INSERT 
								INTO SGD_EXP_EXPEDIENTE 
									(SGD_EXP_NUMERO,
                                    RADI_NUME_RADI,
                                    SGD_EXP_FECH,
                                    DEPE_CODI,
                                    USUA_CODI,
                                    USUA_DOC,
                                    SGD_EXP_ESTADO)
	                    		VALUES 
									('$num_expediente',
	                                '$radicado',
	                                ".$fecha_hoy.",
	                                '$depe_codi',
	                                '$usua_codi',
	                                '$usua_doc',
	                                '0')";		
		if ($this->db->conn->Execute($query)){
			$this->descError .= "[Se incluyo $radicado bien en  $num_expediente Retorna 1)] ";
      		$this->error = "0";
			return 1;
		}
		else{
			$q_excluido  = "SELECT 
								SGD_EXP_ESTADO
	        				FROM 
								SGD_EXP_EXPEDIENTE
	        				WHERE 
								SGD_EXP_NUMERO 		= '$num_expediente'
								AND RADI_NUME_RADI 	= '$radicado'";
	        
	        $rs_excluido = $this->db->conn->Execute($q_excluido);
	       
		    if( $rs_excluido->fields["SGD_EXP_ESTADO"] == 2 ) {
		    	
	            $q_update  = "	UPDATE 
									SGD_EXP_EXPEDIENTE
	             				SET 
									SGD_EXP_ESTADO = 0
								WHERE 
									SGD_EXP_NUMERO 		= '$num_expediente'
									AND RADI_NUME_RADI 	= '$radicado'
	            					AND SGD_EXP_ESTADO 	= 2";
									
				$rs_update = $this->db->conn->Execute($q_update);
									
	            if (!$rs_update){
	            	$this->descError = "[No se incluye Radicado. No se actulizo y algo paso $num_expediente] ";
      				$this->error = "3";	            	
	                return 0;
	            } else {
	                return 1;
	            }
	        }
		}
	}else{
		$this->descError = "[No se incluye Radicado. No existe el Expediente $num_expediente] ";
      	$this->error = "2";
		return 0;
	}		
}





/**
  * Crea un Numero de radicado en un Expediete
  * Crea un expediente y anade un numero de radicado a este. Ademas inserta en el historico
  * La transcacion realizada, para esto verifica que el digito de Chequeo.
  *
  *	@param $radicado int Contiene el numero de radicado a Buscar
  * @param  $usua_doc String Documento de identificacion de Usuario que realiza la insercion-
  * @param  $usua_codi String Codigo en Orfeo del Usuario que realiza la insercion-
  * @param  $depe_codi String Codigo en Orfeo de la dependencia del  Usuario que realiza la insercion.
  * @param  $radicado Numeric Numero de Radicado a Relacionar con el Expediente.
  * @param  $expOld   String Si esta en False Indica eque es un expediente Normal, True es del numeracin Antigua.
  * @param  $radicado String Numero de Expediente a Relacionar con el Radicado.
  * @return Numero de Expediente que posee el radicado
  *
  * Modificacion: 09-Junio-2006 Supersoldiaria
  * @param  $codiPROC Numeric Codigo del proceso asociado al expediente
  * @param  $arrParametro Array Arreglo que contiene los parametros asociados al expediente
  *         indexado con el orden.
  */
function crearExpediente($numExpediente,$radicado,$depe_codi,$usua_codi,$usua_doc,$usuaDocExp,$codiSRD,$codiSBRD,$expOld=null,$fechaExp=null, $codiPROC=null, $arrParametro=null , $privPubli=null, $transacc = null, $nombre = "", $nomproyecto = 0) {
	
    $p = 1;
    // Valida que $arrParametro contenga un arreglo
    if( is_array( $arrParametro ) ) {
    	//echo "<br> ingrese a parametros";
        foreach ( $arrParametro as $orden => $datoParametro ) {
            $coma = ", ";
            if ( $p == count( $arrParametro ) ) {
                $coma = "";
            }
            if ($p == 5) {
                $campoParametro .= "SGD_SEXP_NOMBRE".$coma;
            } else {
                $campoParametro .= "SGD_SEXP_PAREXP".$orden.$coma;
            }
            $valorParametro .= "'".$datoParametro."'".$coma;
            $p++;
        }
    }
	$estado_expediente =0;
	$query = "select SGD_EXP_NUMERO
			from SGD_SEXP_SECEXPEDIENTES
			WHERE SGD_EXP_NUMERO='$numExpediente'";

	//echo "<br> query de busqueda: ".$query;
	if($expOld=="false") {
	$rs = $this->db->conn->Execute($query);
	$trdExp = substr("00".$codiSRD,-3) . substr("00".$codiSBRD,-3);
	$anoExp = substr($numExpediente,0,4);
	if($expManual==1) {
		$secExp = $this->secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp);
	} else {
		$secExp = substr($numExpediente,14,5);
	}
		$consecutivoExp = substr("00000".$secExp,-5);
		$numeroExpediente = $anoExp . $dependencia . $trdExp . $consecutivoExp;
	} else {
	 $secExp = "0";
	 $consecutivoExp = "00000";
	 $anoExp = substr($numExpediente,0,4);
	}
	if ($rs->fields["SGD_EXP_NUMERO"]==$numExpediente) {
		return 0;
	} else {
	$fecha_hoy = Date("d-m-Y");
	if(!$fechaExp) $fechaExp = $fecha_hoy;
	$sqlFechaHoy = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
	if(!$privPubli){
		$privPubli = 'Null';
	}
	if(!$codiPROC) {$codiPROC = "0";
	  $etapa=0;
	}else{
		$consultaPrimerEtapa="SELECT SGD_FEXP_CODIGO FROM SGD_FEXP_FLUJOEXPEDIENTES
					WHERE
					SGD_FEXP_ORDEN=(SELECT MIN(SGD_FEXP_ORDEN) FROM SGD_FEXP_FLUJOEXPEDIENTES
         where SGD_PEXP_CODIGO={$codiPROC}) AND SGD_PEXP_CODIGO={$codiPROC}";
		$rs=$this->db->query($consultaPrimerEtapa);
		$etapa=($rs)?$rs->fields['SGD_FEXP_CODIGO']:0;
	}
    if(!$secExp) $secExp =1;
	//$queryDel = "DELETE FROM SGD_SEXP_SECEXPEDIENTES WHERE SGD_EXP_NUMERO='$numExpediente'";
	//$this->db->conn->Execute($queryDel);

	$query = "insert into SGD_SEXP_SECEXPEDIENTES(SGD_EXP_NUMERO,
                                                    SGD_SEXP_FECH,
                                                    DEPE_CODI,
                                                    USUA_DOC,
                                                    SGD_FEXP_CODIGO,
                                                    SGD_SRD_CODIGO,
                                                    SGD_SBRD_CODIGO,
                                                    SGD_SEXP_SECUENCIA,
                                                    SGD_SEXP_ANO,
                                                    USUA_DOC_RESPONSABLE,
                                                    SGD_PEXP_CODIGO,
                                                    SGD_SEXP_PRIVADO,
                                                    SGD_SEXP_PAREXP1,
                                                    SGD_EPRY_CODIGO ";
    if( $campoParametro != "" ) {
        $query .= ", $campoParametro";
    }
    $query .= " )";
    	//echo ".." . $numExpediente;
	$secExp = str_replace("E","",$secExp);
	$query .= " VALUES ('$numExpediente',"
    					. $fechaExp
    					." ,'$depe_codi'
    					, '$usua_doc'
    					, {$etapa}
    					, $codiSRD
    					, $codiSBRD
    					, '$secExp'
    					, $anoExp
    					, $usuaDocExp
    					, $codiPROC
    					, $privPubli
                        , '$nombre'
                        , $nomproyecto ";
    					
    if( $valorParametro != "" )
    {
        $query .= " , $valorParametro";
    }
    $query .= " )";
    //echo "<br> query a insertar". $query;
        if (!$rs = $this->db->conn->Execute($query)){
			//echo '<br>Lo siento no pudo agregar el expediente<br>';
			//echo "No se ha podido insertar el Expediente";
            return 0;
		}else{
			//echo "<br>Expediente Grabado Correctamente<br>";
		return $numExpediente;
		}
	}
}

	/**
	 * MODIFICA EL TRD DE UN EXPEDIENTE
	 *
	 * @param unknown_type $radicado
	 * @param unknown_type $num_expediente
	 * @param unknown_type $exp_titulo
	 * @param unknown_type $exp_asunto
	 * @param unknown_type $exp_ufisica
	 * @param unknown_type $exp_isla
	 * @param unknown_type $exp_caja
	 * @param unknown_type $exp_estante
	 * @param unknown_type $exp_carpeta
	 */

	 function modificarTRDExpediente($radicado,$numExpediente,$codiSrd=null,$codiSbrd=null,$codiProceso=null,$arrParametro=null,$usuaDoc=null) {
	if($codiProceso==0)$codiProceso="";

	if( is_array( $arrParametro ) )
    	{
        	foreach ( $arrParametro as $orden => $datoParametro )
        	{
            	$campoParametro = "SGD_SEXP_PAREXP".$orden;
            	$valorParametro = "'".$datoParametro."'";
            	$p++;
		$fecha_hoy = Date("Y-m-d");
		$sqlFechaHoy=$this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
		$query="update sgd_sexp_secexpedientes set
				SGD_SRD_CODIGO='$codiSrd'
				,SGD_SBRD_CODIGO='$codiSbrd',
				SGD_PEXP_CODIGO='$codiProceso',
				USUA_DOC_RESPONSABLE='$usuaDoc'";
		if($valorParametro != "''"  )
    		{
			if($campoParametro != ""){
       			 $query .= ", $campoParametro";
	    	        $query .= " = $valorParametro";
			}
    		}
    		$query .= " WHERE SGD_EXP_NUMERO = '$numExpediente'";
		$rs = $this->db->conn->Execute($query);
 		}
    	}

	else{
	$fecha_hoy = Date("Y-m-d");
		$sqlFechaHoy=$this->db->conn->DBDate($fecha_hoy);
	    $query="update sgd_sexp_secexpedientes set
				SGD_SRD_CODIGO='$codiSrd'
				,SGD_SBRD_CODIGO='$codiSbrd',
				SGD_PEXP_CODIGO='$codiProceso',
				USUA_DOC_RESPONSABLE='$usuaDoc'
				WHERE SGD_EXP_NUMERO = '$numExpediente'";
		$rs = $this->db->conn->Execute($query);
		}
		if (!$rs){
		//
		echo '<br>Lo siento no pudo Actualizar los datos del expediente<br>';
		return $numExpediente;
		}else{
		//echo "<br>Datos de expediente Grabados Correctamente<br>";
		return 0;
		}

}

/**
  * Modifica un Numero de radicado en un Expediete
  * Modifica un expediente y anade un numero de radicado a este. Ademas inserta en el historico
  * La transcacion realizada, para esto verifica que el digito de Chequeo.
  *
  *	@param $radicado int Contiene el numero de radicado a Buscar
  * @param  $usua_doc String Documento de identificacion de Usuario que realiza la insercion-
  * @param  $usua_codi String Codigo en Orfeo del Usuario que realiza la insercion-
  * @param  $depe_codi String Codigo en Orfeo de la dependencia del  Usuario que realiza la insercion.
  * @param  $radicado Numeric Numero de Radicado a Relacionar con el Expediente.
  * @param  $radicado String Numero de Expediente a Relacionar con el Radicado.
  * @return Numero de Expediente que posee el radicado
  */
	 function modificar_expediente($radicado,
                                    $num_expediente,
                                    $exp_titulo,
                                    $exp_asunto,
                                    $exp_ufisica,
                                    $exp_isla,
                                    $exp_caja,
                                    $exp_estante,
                                    $exp_carpeta,
                                    $exp_archivo,
                                    $exp_unicon,
                                    $exp_fechaIni,
                                    $exp_fechaFin) {
		$fecha_hoy = Date("Y-m-d");
		$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
	    $query="update sgd_exp_expediente set SGD_EXP_NUMERO='$num_expediente',
                        SGD_EXP_TITULO='$exp_titulo',
                        SGD_EXP_ASUNTO='$exp_asunto',
                        SGD_EXP_UFISICA='$exp_ufisica',
                        SGD_EXP_ISLA='$exp_isla',
                        SGD_EXP_CAJA='$exp_caja',
                        SGD_EXP_ESTANTE='$exp_estante',
                        SGD_EXP_CARPETA='$exp_carpeta',
                        SGD_EXP_ESTADO='1',
                        SGD_EXP_ARCHIVO='$exp_archivo',
                        SGD_EXP_UNICON='$exp_unicon',
                        SGD_EXP_FECH_ARCH=".$sqlFechaHoy.",
                        SGD_EXP_FECH=".$exp_fechaIni.",
                        SGD_EXP_FECHFIN=".$exp_fechaFin."
                    WHERE RADI_NUME_RADI = $radicado";
		if (!$rs = $this->db->conn->Execute($query)){
		echo '<br>Lo siento no pudo Actualizar los datos del expediente<br>';
		}else{
		echo "<br>Datos de expediente Grabados Correctamente<br>";
		}

		}
		
		
	 function datos_expediente($radicado,$num_expediente) {
	    $query="select max(SGD_EXP_CARPETA) tt
				from sgd_exp_expediente
				WHERE SGD_EXP_NUMERO='$num_expediente'
				group by SGD_EXP_NUMERO ";
	    $rs = $this->db->conn->Execute($query);
		if (!$rs){
		    echo 'No tiene un Numero de expediente<br>';
		 }else{
		    if ($rs)
                $this->exp_num_carpetas = $this->rs->fields['tt'];
		}
	    $query="select SGD_EXP_TITULO,
                        SGD_EXP_ASUNTO,
                        SGD_EXP_UFISICA,
                        SGD_EXP_ISLA,
                        SGD_EXP_CAJA,
                        SGD_EXP_ESTANTE,
                        SGD_EXP_CARPETA,
                        SGD_EXP_ARCHIVO,
                        SGD_EXP_UNICON,
                        SGD_EXP_FECH,
                        SGD_EXP_FECHFIN,
                        SGD_EXP_NOMBRESUBEXP
                    FROM sgd_exp_expediente
				WHERE SGD_EXP_NUMERO='$num_expediente' AND
                    RADI_NUME_RADI = $radicado AND
                    SGD_EXP_ESTADO='1'";
		$rs = $this->db->conn->Execute($query);
		if ($rs){
            $this->exp_titulo   = "'" .$this->rs->fields['sgd_exp_titulo']."'";
            $this->exp_asunto   = "'" . $this->rs->fields['sgd_exp_asunto'] ."'";
            $this->exp_ufisica  = "'" .$this->rs->fields['sgd_exp_ufisica'] ."'";
            $this->exp_isla     = $this->rs->fields['sgd_exp_isla'] ;
            $this->exp_caja     = $this->rs->fields['sgd_exp_caja'] ;
            $this->exp_estante  = $this->rs->fields['sgd_exp_estante'] ;
            $this->exp_carpeta  = $this->rs->fields['sgd_exp_carpeta'] ;
            $this->exp_archivo  = $this->rs->fields['sgd_exp_archivo'] ;
            $this->exp_unicon   = $this->rs->fields['sgd_exp_unicon'] ;
            $this->exp_fechaIni = $this->rs->fields['SGD_EXP_FECH'];
            $this->exp_fechaFin = $this->rs->fields['SGD_EXP_FECHFIN'];
            $this->exp_nombresubexp = $this->rs->fields['SGD_EXP_NOMBRESUBEXP'];
			 return 1;
		}else{
		   echo "<br>No se encontraron datos del expediente<br>";
			 return 0;
		}
	}
	
	
	function consultaTipoExpediente($numExpediente) {
		$query="select se.SGD_EXP_NUMERO,
                        sb.SGD_SRD_CODIGO,
                        sr.SGD_SRD_DESCRIP,
                        sb.SGD_SBRD_CODIGO,
                        sb.SGD_SBRD_DESCRIP,
                        se.SGD_FEXP_CODIGO,
                        se.SGD_SEXP_FECH,
                        se.USUA_DOC_RESPONSABLE,
                        se.SGD_PEXP_CODIGO
                    from SGD_SEXP_SECEXPEDIENTES se,
                        SGD_SBRD_SUBSERIERD sb,
                        SGD_SRD_SERIESRD sr
			WHERE SGD_EXP_NUMERO='$numExpediente' AND
					se.SGD_SEXP_ESTADO = 0 AND
                    se.SGD_SRD_CODIGO=sr.SGD_SRD_CODIGO AND
                    se.SGD_SRD_CODIGO=sb.SGD_SRD_CODIGO AND
                    se.SGD_SBRD_CODIGO=sb.SGD_SBRD_CODIGO
                order by se.SGD_SEXP_FECH DESC";
//		$cuentaEtapas=-1;
	  $rs = $this->db->conn->Execute($query);
		$numExpediente = $rs->fields["SGD_EXP_NUMERO"];
	  if($numExpediente) {
	    $this->db->conn->Execute($query);
			$this->descSerie=$rs->fields["SGD_SRD_DESCRIP"];
			$this->descSubSerie=$rs->fields["SGD_SBRD_DESCRIP"];
			$this->codiSRD=$rs->fields["SGD_SRD_CODIGO"];
			$this->codiSBRD=$rs->fields["SGD_SBRD_CODIGO"];
			$this->codigoFldExp=$rs->fields["SGD_FEXP_CODIGO"];
			$this->expFechaCrea=$rs->fields["SGD_SEXP_FECH"];
            $this->expUsuaDoc=$rs->fields["USUA_DOC_RESPONSABLE"];
            $this->codigoTipoExp=$rs->fields["SGD_PEXP_CODIGO"];

			/** EN ESTA CONSULTA TRAEMOS EL TIPO DE PROCESO
				*/
			$query = "SELECT SGD_PEXP_DESCRIP
									,SGD_PEXP_CODIGO
									,SGD_PEXP_TERMINOS
									,SGD_PEXP_AUTOMATICO
									FROM SGD_PEXP_PROCEXPEDIENTES
									WHERE SGD_SRD_CODIGO= ".$this->codiSRD."
									AND SGD_SBRD_CODIGO=".$this->codiSBRD;
			if($this->codigoTipoExp)
			{
				$query .= " and SGD_PEXP_CODIGO=".$this->codigoTipoExp;
			}
	    	$rs = $this->db->conn->Execute($query);
			//$this->codigoTipoExp=$rs->fields["SGD_PEXP_CODIGO"];
			$this->descTipoExp=$rs->fields["SGD_PEXP_DESCRIP"];
			$this->expTerminos = $rs->fields["SGD_PEXP_TERMINOS"];
			$this->pAutomatico = $rs->fields['SGD_PEXP_AUTOMATICO'];
			/** EN ESTA CONSULTA TRAEMOS EL ESTADO DEL PROCESO
				*/
			IF($this->codigoFldExp!=0)
			{
			$query = "SELECT SGD_FEXP_DESCRIP
									, SGD_FEXP_CODIGO
									, SGD_FEXP_TERMINOS
									FROM SGD_FEXP_FLUJOEXPEDIENTES
									WHERE SGD_FEXP_CODIGO= ".$this->codigoFldExp.""
								;

			$rs = $this->db->conn->Execute($query);
				$cuentaEtapas = -1;
            	$cuentaEtapas = $rs->FieldCount();
//			die("Cuenta 1: $cuentaEtapas");
//			die("Cuenta 1: " . $rs->FieldCount()  );
	    	if( $cuentaEtapas > 0 ){
				$this->codigoFldExp=$rs->fields["SGD_FEXP_CODIGO"];
				$this->descFldExp=$rs->fields["SGD_FEXP_DESCRIP"];
				$this->expTerminosP =  $rs->fields["SGD_FEXP_TERMINOS"];
			}else {
				$this->descFldExp = "El proceso a&uacute;n no tiene etapas.";
			}
			} else {
                $query = "SELECT SGD_FEXP_DESCRIP,
                                SGD_FEXP_CODIGO,
                                SGD_FEXP_TERMINOS
                            FROM SGD_FEXP_FLUJOEXPEDIENTES
                            WHERE SGD_PEXP_CODIGO= ".$this->codigoTipoExp. "
					ORDER BY SGD_FEXP_ORDEN ";

                $rs = $this->db->conn->Execute($query);
				$sqlCont = "SELECT COUNT(SGD_FEXP_CODIGO) TOTAL_ETAPAS
                                FROM SGD_FEXP_FLUJOEXPEDIENTES
                                WHERE SGD_FEXP_CODIGO = " .$this->codigoTipoExp;
                $cuentaEtapas = -1;
                $rsCont = $this->db->conn->Execute($sqlCont);
                $cuentaEtapas = (!$rsCont->EOF) ? $rsCont->fields["TOTAL_ETAPAS"] : $cuentaEtapas;
            	//$cuentaEtapas = $rs->FieldCount();
                //die("Cuenta 2: " . $rs->FieldCount()  );

            	if( $cuentaEtapas > 0 ){
                        $this->codigoFldExp=$rs->fields["SGD_FEXP_CODIGO"];
                        $this->descFldExp=$rs->fields["SGD_FEXP_DESCRIP"];
                        $this->expTerminosP =  $rs->fields["SGD_FEXP_TERMINOS"];
                }else {
					$this->descFldExp = "El proceso a&uacute;n no tiene etapas.";
                }
			}
            return $numExpediente;;
        } else {
			return 0;
        }
	}
		/**  FUNCION QUE CALCULA SECUENCIA SEGUN PARAMETROS DEPENDENCIA, SERIE, SUBSERIE
			* Esta funcion Devuelve la secuencia manual cogiendo el valor mayor en le campo SGD_SEXP_SECUENCIA
			* y le incrementa 1.
			*	@param $dependencia int Codigo de la Dependencia.
			* @param $codiSrd int Codigo de la Serie documental que es enviada por el Usuario.
			* @param $codiSBRD int Codigo de la subserie documental enviada por el Usuario.
			* @param $query String Cadena de uso temporal para guardar consultas SQL.
			* @return  Esta funcion Rerna el valor incrementado en Uno del la secuencia correspondiente.
			*/
	function secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp)
	{
		$query="select se.SGD_EXP_NUMERO,
                        se.SGD_FEXP_CODIGO,
                        se.SGD_SEXP_SECUENCIA
			from SGD_SEXP_SECEXPEDIENTES se
			WHERE SGD_SRD_CODIGO = $codiSRD AND
                    SGD_SBRD_CODIGO = $codiSBRD AND
                    SGD_SEXP_ANO = $anoExp AND
                    DEPE_CODI = $dependencia AND
                    SGD_SEXP_SECUENCIA > 0 AND
                    SGD_SEXP_SECUENCIA IS NOT NULL
			ORDER BY
				SGD_SEXP_SECUENCIA DESC";

	        $rs = $this->db->conn->Execute($query);
		$numExpediente = $rs->fields["SGD_EXP_NUMERO"];
		$secExp = $rs->fields["SGD_SEXP_SECUENCIA"];
		if(empty($secExp))
		{
			$secExp= 1;
		}
		else
		{
 			$secExp = $secExp + 1;
		}
		return $secExp;
	}

  /** Descripcinn: FUNCION QUE CONSULTA EL TRD DEL EXPEDIENTE SEGUN PARAMETROS SERIE, SUBSERIE, PROCESO Y EXPEDIENTE
    *              Esta funcion devuelve los datos de serie, subserie y proceso asociados al expediente.
    * Parametros:
    * @param $numExp String Numero del expediente.
    * @param $codiSrd int Codigo de la serie documental.
    * @param $codiSbrd int Codigo de la subserie documental.
    * @param $codiProc int Codigo del proceso.
    * Retorna:
    * @return $arrTRDExp Arreglo con los datos de serie, subserie y proceso asociados al expediente.
    * Fecha de creacion: 13-Junio-2006
    * Creador: Supersolidaria
    * Fecha de modificacion:
    * Modificador:
    */
	function getTRDExp( $numExp, $codiSrd, $codiSbrd, $codiProc )
	{
		$q_TRDExp  = "SELECT SRD.SGD_SRD_CODIGO, SRD.SGD_SRD_DESCRIP,";
        $q_TRDExp .= " SBRD.SGD_SBRD_CODIGO, SBRD.SGD_SBRD_DESCRIP,";
        $q_TRDExp .= " PEXP.SGD_PEXP_DESCRIP,";
        $q_TRDExp .= " PEXP.SGD_PEXP_TERMINOS,";
        $q_TRDExp .= " SEXP.SGD_SEXP_FECH,";
        $q_TRDExp .= " FEXP.SGD_FEXP_CODIGO, FEXP.SGD_FEXP_DESCRIP";
        $q_TRDExp .= " FROM SGD_SRD_SERIESRD SRD, SGD_SBRD_SUBSERIERD SBRD,";
        $q_TRDExp .= " SGD_PEXP_PROCEXPEDIENTES PEXP";
        $q_TRDExp .= " RIGHT JOIN SGD_SEXP_SECEXPEDIENTES SEXP";
        $q_TRDExp .= " ON SEXP.SGD_PEXP_CODIGO = PEXP.SGD_PEXP_CODIGO";
        $q_TRDExp .= " LEFT JOIN SGD_FEXP_FLUJOEXPEDIENTES FEXP";
        $q_TRDExp .= " ON SEXP.SGD_FEXP_CODIGO = FEXP.SGD_FEXP_CODIGO";
        $q_TRDExp .= " WHERE SEXP.SGD_SRD_CODIGO = SRD.SGD_SRD_CODIGO";
        $q_TRDExp .= " AND SEXP.SGD_SBRD_CODIGO = SBRD.SGD_SBRD_CODIGO";

        // $q_TRDExp .= " AND PEXP.SGD_SRD_CODIGO = SRD.SGD_SRD_CODIGO";
        // $q_TRDExp .= " AND PEXP.SGD_SBRD_CODIGO = SBRD.SGD_SBRD_CODIGO";

        $q_TRDExp .= " AND SRD.SGD_SRD_CODIGO = SBRD.SGD_SRD_CODIGO";
        if ( $codiSrd != "" )
        {
            $q_TRDExp .= " AND SRD.SGD_SRD_CODIGO = ".$codiSrd;
        }
        if ( $codiSbrd != "" )
        {
            $q_TRDExp .= " AND SBRD.SGD_SBRD_CODIGO = ".$codiSbrd;
        }
        if ( $codiProc != "" && $codiProc != 0 )
        {
            $q_TRDExp .= " AND PEXP.SGD_PEXP_CODIGO = ".$codiProc;
        }
        if ( $numExp != "" )
        {
            $q_TRDExp .= " AND SEXP.SGD_EXP_NUMERO = '".$numExp."'";
        }
        $q_TRDExp .="ORDER BY SEXP.SGD_SEXP_FECH desc"	;
        // print $q_TRDExp;
        $rs_TRDExp = $this->db->conn->Execute( $q_TRDExp );

        $arrTRDExp['serie'] = $rs_TRDExp->fields['SGD_SRD_CODIGO']."-".$rs_TRDExp->fields['SGD_SRD_DESCRIP'];
        $arrTRDExp['subserie'] = $rs_TRDExp->fields['SGD_SBRD_CODIGO']."-".$rs_TRDExp->fields['SGD_SBRD_DESCRIP'];
        $arrTRDExp['proceso'] = $rs_TRDExp->fields['SGD_PEXP_DESCRIP'];
        $arrTRDExp['terminoProceso'] = $rs_TRDExp->fields['SGD_PEXP_TERMINOS']." Dias Calendario de Termino Total";
        $arrTRDExp['fecha'] = $rs_TRDExp->fields['SGD_SEXP_FECH'];
        $arrTRDExp['estado'] = $rs_TRDExp->fields['SGD_FEXP_DESCRIP'];

		return $arrTRDExp;
	}

    /**
     * 
     * @param object $numExp
     * @return 
     */
	function getDatosParamExp($numExp) {
		$isql ="SELECT 
					e.SGD_SEXP_PAREXP1 as NomEXP1
			       ,e.SGD_SEXP_PAREXP2 as NomEXP2
			       ,e.SGD_SEXP_PAREXP3 as NomEXP3
			       ,e.SGD_SEXP_PAREXP4 as NomEXP4
			       ,e.SGD_SEXP_PAREXP5 as NomEXP5
				   ,p.SGD_EPRY_NOMBRE_CORTO  as Proyecto
				FROM
					SGD_SEXP_SECEXPEDIENTES e
					LEFT OUTER JOIN SGD_EPRY_EPROYECTO p
						ON e.SGD_EPRY_CODIGO = p.SGD_EPRY_CODIGO
				WHERE 
					e.SGD_SEXP_ESTADO = 0 and					
					e.SGD_EXP_NUMERO = '$numExp'";					
			
       	$rst_isql = $this->db->conn->Execute($isql);
       	$arrDatosParametro[] = $rst_isql->fields['NomEXP1'];
       	$arrDatosParametro[] = $rst_isql->fields['NomEXP2'];
	   	$arrDatosParametro[] = $rst_isql->fields['NomEXP3'];
	   	$arrDatosParametro[] = $rst_isql->fields['NomEXP4'];
	   	$arrDatosParametro[] = $rst_isql->fields['NomEXP5'];
		$arrDatosParametro[] = $rst_isql->fields['Proyecto'];
		

		return $arrDatosParametro;
    }

  /** FUNCION EXISTE EXPEDIENTE
    * Determina si existe o no el expediente al cual se le va a incluir el radicado.
    * @param $numExpediente String Numero de Expediente a buscar.
    * @return 0 si no existe el Expediente y el numero del Expediente en caso contrario.
    * Fecha de creacion: 21-Junio-2006
    * Creador: Supersolidaria
    * Fecha de modificacion:
    * Modificador:
    */
	function existeExpediente($numExpediente) {
        $numExpediente = trim($numExpediente);
        $arregloExpSimilares = array();
        $contExp = 0;
        // Patron para diferenciar el numero de expediente del nombre
        $patron = "^[[:digit:]]*(e|E)$";
        if (eregi($patron, $numExpediente)) {
            $query  = "SELECT SGD_EXP_NUMERO
                            FROM SGD_SEXP_SECEXPEDIENTES
                            WHERE SGD_SEXP_ESTADO = 0 AND SGD_EXP_NUMERO = '$numExpediente'";

            $rs = $this->db->conn->Execute($query);
            if ( $rs->EOF ) {
                $q_exp_expediente  = "SELECT SGD_EXP_NUMERO";
                $q_exp_expediente .= " FROM SGD_EXP_EXPEDIENTE";
                $q_exp_expediente .= " WHERE SGD_EXP_NUMERO = '$numExpediente'";

                $rs_exp_expediente = $this->db->conn->Execute( $q_exp_expediente );
                if ( $rs_exp_expediente->EOF ){
                    $this->num_expediente = 0;
                } else {
                    $this->num_expediente = $rs_exp_expediente->fields['SGD_EXP_NUMERO'];
                }
            } else {
                $this->num_expediente = $rs->fields['SGD_EXP_NUMERO'];
            }
        } else {
            $sqlExp = "SELECT SGD_EXP_NUMERO,
                                SGD_SEXP_PAREXP1,
                                SGD_SEXP_PAREXP2,
                                SGD_SEXP_PAREXP3,
                                SGD_SEXP_PAREXP4,
                                SGD_SEXP_PAREXP5,
                                SGD_SEXP_NOMBRE
                            FROM SGD_SEXP_SECEXPEDIENTES
                            WHERE 
									SGD_SEXP_ESTADO = 0 AND(
									SGD_SEXP_PAREXP1 LIKE '%$numExpediente%' OR
                                    SGD_SEXP_PAREXP2 LIKE '%$numExpediente%' OR
                                    SGD_SEXP_PAREXP3 LIKE '%$numExpediente%' OR
                                    SGD_SEXP_PAREXP4 LIKE '%$numExpediente%' OR
                                    SGD_SEXP_PAREXP5 LIKE '%$numExpediente%' OR
                                    SGD_SEXP_NOMBRE LIKE '%$numExpediente%')";
            $rsExp = $this->db->conn->Execute ($sqlExp);

            if ($sqlExp === false) {
                echo $this->db->conn->ErrorMsg();
                return false;
            } else {
                while (!$rsExp->EOF) {
                    $arregloExpSimilares[$contExp]["SGD_EXP_NUMERO"]   = $rsExp->fields["SGD_EXP_NUMERO"];
                    $arregloExpSimilares[$contExp]["SGD_SEXP_PAREXP1"] = $rsExp->fields["SGD_SEXP_PAREXP1"];
                    $arregloExpSimilares[$contExp]["SGD_SEXP_PAREXP2"] = $rsExp->fields["SGD_SEXP_PAREXP2"];
                    $arregloExpSimilares[$contExp]["SGD_SEXP_PAREXP3"] = $rsExp->fields["SGD_SEXP_PAREXP3"];
                    $arregloExpSimilares[$contExp]["SGD_SEXP_PAREXP4"] = $rsExp->fields["SGD_SEXP_PAREXP4"];
                    $arregloExpSimilares[$contExp]["SGD_SEXP_PAREXP5"] = $rsExp->fields["SGD_SEXP_PAREXP5"];
                    $arregloExpSimilares[$contExp]["SGD_SEXP_NOMBRE"]  = $rsExp->fields["SGD_SEXP_NOMBRE"];
                    $contExp++;
                    $rsExp->MoveNext();
                }
                return $arregloExpSimilares;
            }
        }
		return $this->num_expediente;
    }

  /** Funcion expedientes radicado
    * Busca los expedientes a los que pertenece un radicado.
    * @param $radicado int Contiene el numero de radicado a Buscar
    * @return Arreglo con los Nombres de Expediente a los que pertenece el radicado
    * Fecha de creacion: 21-Junio-2006
    * Creador: Supersolidaria
    * Fecha de modificacion:
    * Modificador:
    */
	function expedientesRadicado($radicado) {
	    $query = "SELECT A.SGD_EXP_NUMERO
        	    FROM SGD_EXP_EXPEDIENTE A JOIN SGD_SEXP_SECEXPEDIENTES S ON S.SGD_EXP_NUMERO = A.SGD_EXP_NUMERO
        	    WHERE A.RADI_NUME_RADI = $radicado
        	    AND A.SGD_EXP_ESTADO <> 2
        	    and S.SGD_SEXP_PAREXP1 not in ('Comunicaciones Recibidas', 'Comunicaciones Enviadas') ";
		$rs = $this->db->conn->Execute($query);
		if ($rs->EOF) {
			$arrExpedientes[0] = 0;
		} else {            
            while( $rs && !$rs->EOF ) {
                $arrExpedientes[] = $rs->fields['SGD_EXP_NUMERO'];                
                $rs->MoveNext();
            }
		}
		return $arrExpedientes;
	}

    /** FUNCION EXPEDIENTE ARCHIVADO
    * Busca los datos de archivo de un radicado.
    * @param $radicado int Numero del radicado incluido en el expediente a buscar.
    * @param $numExpediente int Numero del expediente a buscar.
    * @return Arreglo con los datos de archivo del Expediente.
    * Fecha de creacion: 21-Junio-2006
    * Creador: Supersolidaria
    * Fecha de modificacion:
    * Modificador:
    */
	function expedienteArchivado( $radicado, $numExpediente ) {
		$query  = "SELECT SGD_EXP_ESTADO, SGD_EXP_TITULO, SGD_EXP_ASUNTO,";
        $query .= " SGD_EXP_CARPETA, SGD_EXP_UFISICA, SGD_EXP_ISLA,";
        $query .= " SGD_EXP_ESTANTE, SGD_EXP_CAJA, SGD_EXP_FECH_ARCH";
		$query .= " FROM SGD_EXP_EXPEDIENTE";
		$query .= "	WHERE RADI_NUME_RADI = '".$radicado."'";
        $query .= " AND SGD_EXP_NUMERO = '".$numExpediente."'";
        // print $query;
		$rs = $this->db->conn->Execute( $query );
		if( !$rs->EOF ) {
            $arrExpArchivo['estado'] = $rs->fields['SGD_EXP_ESTADO'];
            $arrExpArchivo['titulo'] = $rs->fields['SGD_EXP_TITULO'];
            $arrExpArchivo['asunto'] = $rs->fields['SGD_EXP_ASUNTO'];
            $arrExpArchivo['carpeta'] = $rs->fields['SGD_EXP_CARPETA'];
            $arrExpArchivo['uFisica'] = $rs->fields['SGD_EXP_UFISICA'];
            $arrExpArchivo['isla'] = $rs->fields['SGD_EXP_ISLA'];
            $arrExpArchivo['estante'] = $rs->fields['SGD_EXP_ESTANTE'];
            $arrExpArchivo['caja'] = $rs->fields['SGD_EXP_CAJA'];
            $arrExpArchivo['fArchivo'] = $rs->fields['SGD_EXP_FECH_ARCH'];
            $this->estado_expediente = $arrExpArchivo['estado'];
        }

		return $arrExpArchivo;
	}

  /**
    * FUNCION EXCLUIR EXPEDIENTE
    * Excluye un Numero de radicado de un Expediete
    * @param  $radicado int Contiene el numero de radicado a Buscar
    * @param  $numExpediente String Numero del Expediente del que se desea excluir el radicado.
    * @return 1 si el radicado fue excluido del expediente y 0 en caso contrario.
    * Fecha de creacion: 23-Junio-2006
    * Creador: Supersolidaria
    * Fecha de modificacion:
    * Modificador:
    */
    function excluirExpediente( $radicado, $numExpediente ) {
        $query  = "UPDATE SGD_EXP_EXPEDIENTE";
        $query .= " SET SGD_EXP_ESTADO = 2";
        $query .= " WHERE SGD_EXP_NUMERO = '".$numExpediente."'";
        $query .= " AND RADI_NUME_RADI = '".$radicado."'";
		$query;
        if ( $this->db->conn->Execute( $query ) ) {
            $excluido = 1;
        } else {
            $excluido = 0;
        }

        return $excluido;
    }

    /**
    * FUNCION RADICADO PADRE, ANEXOS y ASOCIADOS
    * Consulta el radicado padre, los anexos y los asociados de un radicado
    * @param  $radicado int Contiene el numero de radicado a Buscar
    * @return arrAnexoAsociado Arreglo con el radicado padre, los anexos y los asociados del radicado.
    * Fecha de creacion: 27-Junio-2006
    * Creador: Supersolidaria
    * Fecha de modificacion:
    * Modificador:
    */
    function expedienteAnexoAsociado( $radicado ) {
        $driver = $this->db->driver;
        $convert = 'RAD.RADI_NUME_DERI AS "RADPADRE"';

        if ($driver == 'mssqlnative') {
            $convert = 'CONVERT(VARCHAR(15), RAD.RADI_NUME_DERI) AS "RADPADRE"';
        }

        $query  = 'SELECT ' . $convert . ', RAD.SGD_SPUB_CODIGO AS "PRIVADO",';
        $query .= ' CASE WHEN RAD.RADI_TIPO_DERI = 0 THEN RAD.RADI_NUME_RADI';
        $query .= ' END AS "ANEXO",';
        $query .= ' CASE WHEN RAD.RADI_TIPO_DERI = 2 THEN RAD.RADI_NUME_RADI';
        $query .= ' END AS "ASOCIADO",';
        $query .= ' RAD.RADI_FECH_RADI, RAD.RADI_PATH, TPR.SGD_TPR_DESCRIP, RAD.RA_ASUN';
        $query .= ' FROM RADICADO RAD';
        $query .= ' LEFT JOIN SGD_TPR_TPDCUMENTO TPR ON TPR.SGD_TPR_CODIGO = RAD.TDOC_CODI';
        $query .= ' WHERE 1 = 1';
        $query .= ' AND ( RAD.RADI_NUME_DERI = '.$radicado;
        $query .= ' OR ( RAD.RADI_NUME_RADI = '.$radicado;
        $query .= ' AND RAD.RADI_NUME_DERI IS NOT NULL';
        $query .= ' AND RAD.RADI_TIPO_DERI = 0 )';
        $query .= ' )';
        $query .= ' AND RADI_TIPO_DERI <> 1';
        $query .= ' AND RAD.RADI_NUME_RADI NOT IN (';
        $query .= '   SELECT EXP.RADI_NUME_RADI FROM SGD_EXP_EXPEDIENTE EXP WHERE EXP.RADI_NUME_RADI <> '.$radicado;
        $query .= ' )';
        $query .= ' AND RAD.RADI_NUME_DERI NOT IN (';
        $query .= '   SELECT EXP.RADI_NUME_RADI FROM SGD_EXP_EXPEDIENTE EXP WHERE EXP.RADI_NUME_RADI <> '.$radicado;
        $query .= ' )';
        $query .= ' AND RAD.RADI_NUME_RADI NOT IN (';
        $query .= '   SELECT RADI_NUME_SALIDA FROM ANEXOS';
        $query .= '   WHERE ANEX_RADI_NUME = '.$radicado;
        $query .= '   AND RADI_NUME_SALIDA <> '.$radicado;
        $query .= ' )';

        $rs = $this->db->conn->Execute($query);
        $a = 0;
        while( !$rs->EOF ) {
            $arrAnexoAsociado[ $a ]['radPadre'] = $rs->fields['RADPADRE'];
            $arrAnexoAsociado[ $a ]['anexo'] = $rs->fields['ANEXO'];
            $arrAnexoAsociado[ $a ]['asociado'] = $rs->fields['ASOCIADO'];
            $arrAnexoAsociado[ $a ]['fechaRadicacion'] = $rs->fields['RADI_FECH_RADI'];
            $arrAnexoAsociado[ $a ]['ruta'] = $rs->fields['RADI_PATH'];
            $arrAnexoAsociado[ $a ]['tipoDocumento'] = $rs->fields['SGD_TPR_DESCRIP'];
            $arrAnexoAsociado[ $a ]['asunto'] = $rs->fields['RA_ASUN'];
            $arrAnexoAsociado[ $a ]['privado'] = $rs->fields['PRIVADO'];
            $a++;
            $rs->MoveNext();
        }

        return $arrAnexoAsociado;
    }

    /**
    * FUNCION GRABAR SUBEXPEDIENTE
    * Almacena el numero del subexpediente asociado a un expediente y a un radicado.
    * @param  $radicado int Contiene el numero de radicado
    * @param  $numExpediente int Contiene el numero del expediente
    * @param  $numSubexpediente int Contiene el numero del Subexpediente
    * @return 1 si se grabo correctamente el subexpediente y 0 en caso contrario.
    * Fecha de creacion: 29-Junio-2006
    * Creador: Supersolidaria
    * Fecha de modificacion:
    * Modificador:
    */
    function grabarSubexpediente( $radicado, $numExpediente, $Subexpediente, $nombreSubExp ) {
        $query  = "UPDATE SGD_EXP_EXPEDIENTE
                            SET SGD_EXP_SUBEXPEDIENTE = $Subexpediente,
                                SGD_EXP_NOMBRESUBEXP = '$nombreSubExp'
                        WHERE SGD_EXP_NUMERO = '$numExpediente' AND
                                RADI_NUME_RADI = $radicado";

        if ( $this->db->conn->Execute( $query ) ) {
            $grabado = 1;
        } else {
            $grabado = 0;
        }

        return $grabado;
    }
	
	/**
		* Cambia el nombre asignado al expediente.
		* @param  $num_expediente String Numero del expedientes al que se le cambiar el nombre en el campo 5
		* @param  $nomb_Expe_300  String Con tama?e 300 caracteres. Nombre asignado al expediente
		* @return 1 si la consulta se ejecuto con exito
	*/

	function insert_ExpedienteNomb($num_expediente,$nomb_Expe_300){
	 	$sqlUpdate = "	UPDATE 
							SGD_SEXP_SECEXPEDIENTES 
						SET 
							SGD_SEXP_PAREXP5 =  ''
							,SGD_SEXP_PAREXP4 = ''
							,SGD_SEXP_PAREXP3 = ''
							,SGD_SEXP_PAREXP2 = ''
							,SGD_SEXP_PAREXP1 = '$nomb_Expe_300'
							 
						WHERE 
							SGD_EXP_NUMERO = '$num_expediente'";			
	 	return $this->db->conn->Execute($sqlUpdate);
	 }

	/**
		* Cambia el nombre asignado al expediente.
		* @param  $num_expediente String Numero del expedientes al que se le cambiar el nombre en el campo 5
		* @param  $nomb_Expe_300  String Con tamano de 300 caracteres. Nombre asignado al expediente
		* @return 1 si la consulta se ejecuto con exito
	*/

	function insert_ProyNomb($num_expediente,$codig_Proyec){
	 	$sql_cambiar	= "	UPDATE 
								SGD_SEXP_SECEXPEDIENTES
							SET 
								SGD_EPRY_CODIGO = $codig_Proyec  
							WHERE 
								SGD_EXP_NUMERO LIKE '$num_expediente'";			
		return $this->db->conn->Execute($sql_cambiar);
	 }
	 
	/**
		* Asigna el numero de bpin, empres, sector de suifp a un expediente 
		* @param  $num_expediente String Numero del expedientes al que se le cambiar el nombre en el campo 5
		* @param  $nombpin String Con tamano de 300 caracteres. Nombre asignado al expediente
		* @return 1 si la consulta se ejecuto con exito
	*/

	function insert_suifp($num_expediente, $nombpin, $sector, $empresa){
            if(!empty ($nombpin))
                $setBpin=",SEXP_BPIN = '$nombpin'";
            
	    $sql_cambiar	= "	UPDATE
								SGD_SEXP_SECEXPEDIENTES
							SET
								EXP_SECT_ID = '$sector'
								,SGD_EMP_ID  = '$empresa'
                                                                $setBpin
							WHERE
								SGD_EXP_NUMERO = '$num_expediente'";	

		$resul = $this->db->conn->Execute($sql_cambiar);
        return $resul;
	}

 	/**
	* Cambia el estado del expediente si esta activo lo pasa a null y lo contrario.
	* @param  $num_expediente String Numero del expedientes al que se le cambiar el estado
	* @param  $accion	Boolean Si activamos enviamos true de lo contrario enviamos false		
	* @return 1 si la consulta se ejecuto con exito
	*/

	function estado_Expediente($num_expediente,$accion = 0){
		//accion por defecto activar 
		$cambio = 0;
		
		//Consultamos si el expediente tiene radicados activos
		$sql_radi_exp	="	SELECT 
								COUNT (SEP.RADI_NUME_RADI) AS TOTAL
							FROM 
								SGD_EXP_EXPEDIENTE SEP
							WHERE 
								SEP.SGD_EXP_ESTADO = 0
      							AND SEP.SGD_EXP_NUMERO like '$num_expediente'";
		
		$resul			= $this->db->conn->Execute($sql_radi_exp);		
		//Si el resultado es 0 podemos Inactivar el expediente
		$val_inactivar	= $resul->fields['TOTAL'];
		
		if(!empty($accion) && !empty($val_inactivar)){
			return	$grabado = 0;
		}		
				
		$sql_consultar	="	SELECT 
								SGD_SEXP_ESTADO AS ESTADO
							FROM 
								SGD_SEXP_SECEXPEDIENTES SG
							WHERE 
								SG.SGD_EXP_NUMERO LIKE '$num_expediente'";
		
		$resul2			= $this->db->conn->Execute($sql_consultar);
		//Verificamos el estado actual del expediente
		$val_estado	= $resul2->fields['ESTADO'];
		
		//inactivar el expediente
		if(!empty($accion) 
			&& empty($val_inactivar)){
				$cambio = 1;
			}	
		
		$sql_accion		="	UPDATE 
							SGD_SEXP_SECEXPEDIENTES
						SET 
							SGD_SEXP_ESTADO = $cambio 
						WHERE 
							SGD_EXP_NUMERO LIKE '$num_expediente'";
				
		//regresamos 1 si se realizo la accion 0 lo contrario
		if ( $this->db->conn->Execute($sql_accion) ) {
            $grabado = 1;
        } else {
            $grabado = 0;
        }
		return $grabado;
	 }
         
         function expedienteRadicadoNSolicitud($radicado) {
		$query  = "select EX.SGD_EXP_NUMERO
                           from  SGD_EXP_EXPEDIENTE EX 
                           LEFT JOIN SGD_SEXP_SECEXPEDIENTES SE ON SE.sgd_exp_numero=EX.SGD_EXP_NUMERO
                           JOIN RADICADO R ON R.RADI_NUME_RADI=EX.RADI_NUME_RADI
                           where SE.SGD_SEXP_PAREXP1 LIKE '%'+ISNULL(R.radi_nume_solicitud,'X')+'%' 
                                 AND R.RADI_NUME_RADI=$radicado 
                                 AND EX.SGD_EXP_ESTADO <> 2";
		$rs = $this->db->conn->Execute($query);
		if ($rs->EOF) {
			$arrExpedientes[0] = 0;
		} else {            
            while( $rs && !$rs->EOF ) {
                $arrExpedientes[] = $rs->fields['SGD_EXP_NUMERO'];                
                $rs->MoveNext();
            }
		}
		return $arrExpedientes;
	}
	/**
    * Funcion getExpediente
    * configura los datos del expediente para ser utilizados
    * @param  $radicado int Contiene el numero de radicado
    * Fecha de creacion: 20-10-2009
    */
    function getExpediente($exp)
    {
		$sql= "	SELECT	S.*, 
						D.DEPE_NOMB, 
						D.DEPE_CODI_TERRITORIAL, 
						U.USUA_NOMB,
						SR.SGD_SRD_DESCRIP,
						SB.SGD_SBRD_DESCRIP, 
						U.DEPE_CODI DEPENDENCIA_RESPONSABLE
				FROM	SGD_SEXP_SECEXPEDIENTES AS S
						JOIN DEPENDENCIA AS D ON 
							D.DEPE_CODI = S.DEPE_CODI
						LEFT JOIN USUARIO AS U ON 
							U.USUA_DOC = S.USUA_DOC_RESPONSABLE
						JOIN SGD_SRD_SERIESRD SR ON
							SR.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
						JOIN SGD_SBRD_SUBSERIERD SB ON
						SB.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO AND
							SB.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
				WHERE	S.SGD_EXP_NUMERO = '$exp'";
		
    	$rs = $this->db->conn->Execute($sql);
    	if($rs && !$rs->EOF)
    	{
            $this->tipExp		= $rs->fields['SGD_SEXP_TIPOEXP'];
            $this->nombreExp	= $rs->fields['SGD_SEXP_PAREXP1'];
            $this->asuntoExp	= $rs->fields['SGD_SEXP_ASUNTO'];
            $this->depCodi		= $rs->fields['DEPE_CODI'];
            $this->fecha        = $rs->fields["SGD_SEXP_FECH"];
            $this->fechaCierre  = $rs->fields["SGD_SEXP_FECHACIERRE"];
			$this->privado		= $rs->fields["SGD_SEXP_PRIVADO"];
			$this->estado		= $rs->fields["SGD_SEXP_ESTADO"];
			$this->serie		= $rs->fields["SGD_SRD_DESCRIP"];
			$this->subserie		= $rs->fields["SGD_SBRD_DESCRIP"];
            $this->fecExp = $rs->fields['SGD_SEXP_FECHAINICIO'];
            $this->fecExpFin = $rs->fields['SGD_SEXP_FECHAFIN'];
            $this->faseExpDisplay =  $this->faseArchivo[$rs->fields['SGD_SEXP_FASE']];
            $this->faseExp =  $rs->fields['SGD_SEXP_FASE'];
            $this->estadoExpDisplay = $this->estadoExpediente[$rs->fields['SGD_SEXP_CERRADO']];
            $this->estadoExp = !$rs->fields['SGD_SEXP_CERRADO']?0:$rs->fields['SGD_SEXP_CERRADO'];
            $this->depeNomb     = $rs->fields["DEPE_NOMB"];
			$this->responsable	= $rs->fields["USUA_DOC_RESPONSABLE"];
            $this->depeResponsable	= $rs->fields["DEPENDENCIA_RESPONSABLE"];
            $this->responsableNom = $rs->fields["USUA_NOMB"];
            $this->permiteReabrir = $rs->fields["SGD_SBRD_REABRIREXPEDIENTE"];
			//$this->estado		= $rs->fields["SGD_SEXP_CERRADO"];
            //$this->fase		= $rs->fields["SGD_SEXP_FASEEXP"];
			//$this->nivelExp	= $rs->fields["SGD_SEXP_NIVELSEG"];
            $this->migraEstado  = $rs->fields["SGD_SEXP_MIGRADOESTADO"];
            $this->migraDescri  = $rs->fields["SGD_SEXP_MIGRADODESCRI"];
    	}
    }
}
?>
