<?php
require_once 'HTML/AJAX/Action.php';
/**
 * Clase Radicacion desde AJAX
 *
 * Permite generar un numero de radicado
 *
 * @autor Jairo Losada 2009 - Correlibre.org, OrfeoGPL.org
 *                          -> Modificacion para DNP 10/2009  Tomada de Version Original
 *                          de Correlibre.org y OrfeoGPL.org
 *                          Basada en Ejemplo de la Libreria HTML_AJAX
 *         
 * @Copyright GNU/GPL v3
 * @param object $db Objeto conexion a la base de Datos de Orfeo
 *
 * @package OrfeoGPL
 * Require the action class
 *
 * * @autor Modificado Jairo Losada Correlibre.org - 2009
 *          Adaptado por  DNP 2010 - jlosada
 */

include "../../tx/Radicacion.php";

class radicacionAjax extends Radicacion
{
	// variable con Conexion de OrfeoGPL
	var $db;
	/*
	 * Esta variable indica en que sitio se encuenta la Raiz de Orfeo, es
	 * Una ruta relativa ... que muestra cuantos directorios debe devolverse para encontrar
	 * la raiz
	 * @var strig $ruta_raiz Esta variable indica en que sitio se encuenta la Raiz de Orfeo, es Una ruta relativa ... que muestra cuantos directorios debe devolverse para encontrar la raiz
	 * @access private
	 */	
	var $ruta_raiz;

	/*
	 * Variable en el cual se almacenan la dependencia y Usuario
	 * @var strig $ruta_raiz Esta variable indica en que sitio se encuenta la Raiz de Orfeo, es Una ruta relativa ... que muestra cuantos directorios debe devolverse para encontrar la raiz
	 * @access private
	 */		
	
	var $depeCodi;
	var $usuaCodi;
	/*
	 * Metodo constructor de la Clase
	 *
	 * Metodo que funciona como costructor e inicializa la Clase recibiendo la conexion a Orfeo
	 * en la Variable $db
	 *
	 * @autor Jairo Losada -  2009 - DNP
	 * @Copyright GNU/GPL v3
	 * @param object $db Objeto conexion a la base de Datos de Orfeo
	 * 
	  */


	function __construct($db,$ruta_raiz)
	{
		$this->db = $db;
		$this->ruta_raiz = $ruta_raiz; 
	}
	/*
	 * Metodo que Carga la Variable $ruta_raiz
     * @param var $ruta_raiz Esta variable indica en que sitio se encuenta la Raiz de 
     * Orfeo, es Una ruta relativa ... que muestra cuantos directorios debe devolverse para encontrar la raiz
	 * @access public
	 * @return string Retorna la Rua raiz de Orfeo
	 */	
	
	function updateClassName() {
		$response = new HTML_AJAX_Action();

		$response->assignAttr('test','className','test');

		return $response;
	}

	function greenText($id) {
		$response = new HTML_AJAX_Action();
		$response->assignAttr($id,'style','color: green');
		return $response;
	}

	function highlight($id) {
		$response = new HTML_AJAX_Action();
		$response->assignAttr($id,'style','background-color: yellow');
		return $response;
	}
    /**
	 *  trae los Usuarios de una dependencia
	 *  @txAccion bool Si es true entonces carga solo los jefe. txAccion es la accion a realizar Informar o reasignar
	 **/

	function newRadicadoAjax($idObjetoHtml,$asunto,$tipoRadicado,$radiDepeRadi,$radiDepeActu
			                ,$dependenciaSecuencia,$radiUsuaRadi,$radiUsuaActu,$usuaDoc
							,$cuentai, $docUs3=0,$mRecCodi=0
							,$radiFechOficio, $radicadoPadre,$radPais,$tipoDocumento=0
							,$carpetaPer=0, $carpetaCodi,$tDidCodi=0, $tipoRemitente=0
							,$ane='',$radiPath='', $tema) 
	{
		$response = new HTML_AJAX_Action();
		$this->radiTipoDeri = 0;
		$this->radiCuentai = "".trim($cuentai)."";
		$this->eespCodi =  $docUs3;
		$this->mrecCodi =  $mRecCodi; 
		$this->fenvCodi =  $mRecCodi;
		$fecha_gen_doc_YMD = substr($radiFechOficio,6 ,4)."-".substr($radiFechOficio,3 ,2)."-".substr($radiFechOficio,0 ,2);
		$this->radiFechOfic =  $fecha_gen_doc_YMD;
		$this->radiNumeDeri = trim($radicadoPadre);
		$this->radiPais  =  $radPais;
		$this->descAnex  = $ane;
		$this->raAsun    = $asunto;
		$this->radiDepeActu = $radiDepeActu ;
		$this->radiDepeRadi = $radiDepeRadi ;
		$this->radiUsuaActu = $radiUsuaActu;
		$this->radiUsuaRadi = $radiUsuaRadi;
		$this->usuaCodi = $radiUsuaRadi;
		$this->eespCodi = $docUs3;
		$this->trteCodi  =  $tipoRemitente;
		$this->tdocCodi  = $tipoDocumento;
		$this->tdidCodi  = $tDidCodi;
		$this->carpCodi  = $carpetaCodi;
		$this->carPer    = $carpetaPer;
		$this->usuaDoc    = $usuaDoc;
		$this->noDigitosRad = 6;
		$this->dependencia = $radiDepeRadi;
		if($radiPath) $this->radiPath    = $radiPath;
		$noRad = $this->newRadicado($tipoRadicado,$dependenciaSecuencia);
		if($noRad<=1){
			$errorNewRadicado = "<font size=1 color=red>Error al Generar el Radicado.". $this->errorNewRadicado . "</font>";
        } else {
			$cadena = "seleccion = document.getElementById('$idObjetoHtml'); ";
			$cadena1 = "document.getElementById('numeroRadicado').value=".$noRad."; ";
			$cadena1 .= "document.getElementById('Submit33').style.visibility='hidden'; ";
			$cadena1 .= "document.getElementById('grabarDir').style.visibility='visible'; ";
			$cadena1 .= "grabarDirecciones(document.getElementById('numeroRadicado').value); verDatosRad(".$noRad."); ";
			$cadena .= 'valor="<table width=\'50%\'><tr class=titulos1><td><center><font size=4>Radicado No '.$noRad.htmlspecialchars($errorNewRadicado).'</font></center></td></tr></table>";';
			
			include_once "../../tx/Historico.php"; 
			$historico = new Historico($this->db);
			$radicados[] = $noRad;
			$resHistorico =$historico->insertarHistorico($radicados, $radiDepeRadi,$radiUsuaRadi,$radiDepeActu,$radiUsuaActu,'',2 );
			
			if ($tema != "") {
    			$sql = "SELECT MAX(SGD_CAUX_CODIGO)+1 AS CONTEO FROM SGD_CAUX_CAUSALES";
    			$cntCaux = $this->db->conn->GetOne($sql);
    			$datos_c = array();
    			$datos_c['SGD_CAUX_CODIGO'] = $cntCaux;
    			$datos_c['RADI_NUME_RADI'] = $noRad;
    			$datos_c['SGD_DCAU_CODIGO'] = $tema;
    			$datos_c['SGD_CAUX_FECHA'] = $this->db->conn->OffsetDate(0, $this->db->conn->sysTimeStamp);
    			$ok_c = $this->db->conn->Replace("SGD_CAUX_CAUSALES", $datos_c, false, false);
			}
			
			$alertaVence = "";
			if( (substr($noRad, -1)== 1) || (substr($noRad, -1)== 3)) {
				include_once "../../../class_control/class_gen.php";
				$sql="select dbo.sumadiashabiles(".$this->db->conn->sysTimeStamp.", 6)";//obtenemos el 6 dia siguiente hábil para disclaimer. Sera el dia de la anulacion.
				$fecVence = $this->db->conn->GetOne($sql);
				$objCC = new CLASS_GEN();
				$txtFechaVence = $objCC->traducefecha($fecVence);
				$alertaVence = 'alertaVence('.$noRad.',"'.$txtFechaVence.'");';
			}
			$cadena .= " $cadena1 seleccion.innerHTML=valor; window.setTimeout(\"remoteRad.enviarCorreo('$noRad')\",1000); "; 
            //$alertaVence
            
			$response->insertScript($cadena);
			//$this->enviarCorreo($noRad);
			
			### FUCIONALIDAD PARA ASOCIAR RADICADOS DE SALIDA COMO LA RESPUESTA DE RADICADOS DE ENTRADA, EN EL CAMPO RESPUESTA
			if (substr($noRad,-1,1) == 1){
				$this->asociarResp($noRad); 
			}
        }
		return $response;
	}
	
	
	### FUCIONALIDAD PARA ASOCIAR RADICADOS DE SALIDA COMO LA RESPUESTA DE RADICADOS DE ENTRADA, EN EL CAMPO RESPUESTA
	function asociarResp($noRad){
		//require ( $this->ruta_raiz."/config.php" );
		include_once "../../../include/db/ConnectionHandler.php";
		$db = new ConnectionHandler("$ruta_raiz");
		$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

		 $sql = "SELECT  RADI_NUME_DERI
				 FROM    RADICADO
				 WHERE   RADI_NUME_RADI = ".$noRad;
		$rs = $this->db->conn->Execute($sql);
		$padre = $rs->fields["RADI_NUME_DERI"];
		if (substr($padre,-1,1) == 2){
			$actu = "UPDATE  RADICADO
					SET     RADI_RESPUESTA = ".$noRad."
					WHERE   RADI_NUME_RADI = ".$padre;
			$this->db->conn->Execute($actu);
		}
		return;
	}
	
	
	
	function insertDireccionAjax(	$radiNumeRadi, 
									$dirTipo, 
									$tipoAccion,	 
									$grbNombresUs, 
									$ccDocumento, 
									$muniCodi, 
									$dpto_tmp1, 
									$idPais, 
									$idCont, 
									$funCodigo, 
									$oemCodigo, 
									$ciuCodigo, 
									$espCodigo, 
									$direccion="",
									$codpostal,
									$dirTelefono, 
									$dirMail="",
									$dirNombre="",
									$asunto="",
									$cuentai="",
									$fechaOficio="",
									$med=0,
									$ane="")
	{

		if($asunto || $mRecCodi || $ane || $cuentai){
			if($asunto) $this->raAsun=$asunto;
			$this->mrecCodi =  $mRecCodi;
			$this->descAnex  = $ane;
			$this->radiCuentai = "".trim($cuentai)."";
			if($fechaOficio) { 
			  $fecha_gen_doc_YMD = substr($radiFechOficio,6 ,4)."-".substr($radiFechOficio,3 ,2)."-".substr($radiFechOficio,0 ,2);
			  $this->radiFechOfic =  $fecha_gen_doc_YMD;
			}
			$respuestaUpdate = $this->updateRadicado($radiNumeRadi, $radPathUpdate = null);
			
			//return "Entro.".$respuestaUpdate . ">". $this->db->conn->query;
		}

		$this->radiNumeRadi =	$radiNumeRadi 	;
		$this->dirTipo 		=	$dirTipo 	;
		$this->tipoAccion	=	$tipoAccion	;
		$this->trdCodigo	=	0 	;
		$this->grbNombresUs	=	"'".$grbNombresUs."'";
		$this->ccDocumento	=	"'".$ccDocumento."'";
		$this->muniCodi		=	$muniCodi 	;
		$this->dpto_tmp1	=	$dpto_tmp1 	;
		$this->idPais		=	$idPais 	;
		$this->idCont		=	$idCont 	;
		$this->funCodigo	=	$funCodigo 	;
		$this->oemCodigo	=	$oemCodigo 	;
		$this->ciuCodigo	=	$ciuCodigo 	;
		$this->espCodigo	=	$espCodigo 	;
		$this->direccion	=	"'". $direccion ."'" 	;
		$this->codpostal	=	$codpostal 	;
		$this->dirTelefono	=	"'". $dirTelefono ."'" 	;
		$this->dirMail		=	"'". $dirMail . "'";
		$this->dirNombre	=	"'" . $dirNombre ."'";
		$this->dirFrmEnvio  =   $med;

		$respuestaInsert = $this->insertDireccion($radiNumeRadi,$dirTipo,$tipoAccion);
		return  $respuestaInsert;
	}
	
	function insertVarsDireccionAjax( $dirTipo,	
									$trdCodigo, 
									$grbNombresUs, 
									$ccDocumento, 
									$muniCodi, 
									$dpto_tmp1, 
									$idPais, 
									$idCont, 
									$funCodigo, 
									$oemCodigo, 
									$ciuCodigo, 
									$espCodigo, 
									$direccion,
									$dirTelefono, 
									$dirMail,
									$dirCodigo,
									$dirNombre)
	{	
		$this->dirTipo 		=	$dirTipo 	;
		$this->trdCodigo	=	$trdCodigo 	;
		$this->grbNombresUs	=	$grbNombresUs 	;
		$this->ccDocumento	=	$ccDocumento 	;
		$this->muniCodi		=	$muniCodi 	;
		$this->dpto_tmp1	=	$dpto_tmp1 	;
		$this->idPais		=	$idPais 	;
		$this->idCont		=	$idCont 	;
		$this->funCodigo	=	$funCodigo 	;
		$this->oemCodigo	=	$oemCodigo 	;
		$this->ciuCodigo	=	$ciuCodigo 	;
		$this->espCodigo	=	$espCodigo 	;
		$this->direccion	=	$direccion 	;
		$this->dirTelefono	=	$dirTelefono 	;
		$this->dirMail		=	$dirMail 	;
		$this->dirCodigo	=	$dirCodigo 	;
		$this->dirNombre	=	$dirNombre	;
		$respuestaVar = 1;
		return  $respuestaVar;

	}

	function enviarCorreo($radiNumeRadi)
	{
		$result = "-1";
		require $this->ruta_raiz."/config.php";
		require "../../../class_control/correoElectronico.php";
		//Correo electronico de la persona que se le asigno el radicado de entrada (-2).
		$tipRad = substr($radiNumeRadi, -1);  // Devuelve el tipo de radicado
		if($tipRad == 2){
			$sql = "SELECT USUA_EMAIL, RA_ASUN FROM RADICADO R INNER JOIN USUARIO U ON R.RADI_USUA_ACTU=U.USUA_CODI AND R.RADI_DEPE_ACTU=U.DEPE_CODI WHERE RADI_NUME_RADI=$radiNumeRadi";
			$rsf = $this->db->conn->Execute($sql);
			$correoDes = trim($rsf->fields["USUA_EMAIL"]);
			$asuntoRad = trim($rsf->fields["RA_ASUN"]);
						
			$asunto = "Nuevo radicado de Entrada en Orfeo URT: ".$radiNumeRadi;
			$cuerpo = "<tr><td colspan='2' style='font-family: verdana; font-size: 75%; text-align: justify'>
                    El Sistema de Gesti&oacute;n Documental <b>Orfeo</b> le informa que 
                    se ha generado un nuevo radicado de Entrada. El N&uacute;mero es <b>$radiNumeRadi</b> con 
					asunto <b>$asuntoRad</b>.<br/><br/> Por favor consulte su bandeja de entrada.
                    </td><tr>
                    <tr><td colspan='2' style='text-align: center'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
                    </table>";
			$cuerpoMail = str_replace("XYX", $cuerpo, $cuerpoMail);
			$objMail = new correoElectronico($this->ruta_raiz);
			$objMail->FromName = "Notificaciones";
			$result = $objMail->enviarCorreo(array($correoDes), null, null, $asunto, $cuerpo);
		}
		return $result;
	}
}
?>