<?php
require 'libs/CorreoElectronico.php';
require_once 'util/referenciaServicios/OrfeoServiceWCF/OrfeoServiceWCFClient.class.php';
class Notificacion_Model extends Model
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public  function enviarCorreoRespuestaRapida()
    {	
        if(isset($_POST['NoRadicadoSalida'])){
            $NoRadicadoSalida=$_POST['NoRadicadoSalida'];
        }
        if(isset($_POST['NoRadicadoPadre'])){
            $NoRadicadoPadre=$_POST['NoRadicadoPadre'];
        }
    	//$tmpCodEnvio = 114;	//correo electrónico normal
    	$tmpCodEnvio = 112;	//correo electrónico certificado
    	if ($_POST['nremitente'] == CORREO_MAILFE) {
            $objCorreo = new CorreoElectronico(RUTARAIZ, false, true);
            $asunto = "(" . $_POST['NoRadicadoSalida'] . "_1) Respuesta del Departamento Nacional de Planeacional DNP a su solicitud No. " . $_POST['NoRadicadoPadre'];
            $objCorreo->AddCC('correo@certificado.4-72.com.co');            
            $cuerpo = "<table width=\"100%\"><tr><td>El Departamento Nacional de Planeaci&oacute;n ha dado respuesta a su radicación de factura electrónica " . 
						"No. $NoRadicadoPadre mediante el oficio de salida No. $NoRadicadoSalida el cual se encuentra anexo.</p><br /><b><center>Para dar contestación a través de notas débito o crédito utilice el formulario de factura electrónica en: <br />" . 
						"<a href=\"https://www.dnp.gov.co\">" . "https://www.dnp.gov.co</a><br> DNP </b></center></td></tr><tr><td></td></tr><tr><td>Favor <b>NO RESPONDER</b> a esta cuenta de correo, se utiliza solo para notificar aspectos relacionados a factura elecrtónica y no es monitoreada. Muchas gracias !!</td></tr></table>";
			//$tmpCodEnvio = 112;	//correo electrónico certificado
        } else {
            $objCorreo = new CorreoElectronico(RUTARAIZ, false, false);
            //$objCorreo->AddCC('correo@certificado.4-72.com.co'); 
            $cuerpo = "<table width=\"100%\"><tr><td>El Departamento Nacional de Planeaci&oacute;n ha dado respuesta a su solicitud " . "No. $NoRadicadoPadre mediante el oficio de salida No. $NoRadicadoSalida, el cual tambi&eacute;n puede ser " . "consultado en el portal Web del DNP.</p><br /><b><center>Si no puede visualizar el correo, " . "o los archivos adjuntos $msgFileSolicitud, puede consultarlos tambi&eacute;n en la siguiente direcci&oacute;n: <br />" . "<a href=\"https://pqrsd.dnp.gov.co/consulta.php?rad=$NoRadicadoPadre\">" . "https://pqrsd.dnp.gov.co/consulta.php </a><br> DNP </b></center></td></tr></table>";
            //$asunto = "Respuesta del Departamento Nacional de Planeacional DNP a su solicitud No. " . $NoRadicadoPadre;
            $asunto = "(" . $_POST['NoRadicadoSalida'] . "_1) Respuesta del Departamento Nacional de Planeacional DNP a su solicitud No. " . $_POST['NoRadicadoPadre'];
        }
        try {
            if(isset($_POST))
            {
                if(isset($_POST['pathsAttachments'])) {
                    $path = "";
                    $pathsAttachments = json_decode($_POST['pathsAttachments'],true);
                    if(is_array($pathsAttachments) && count($pathsAttachments)){
                        foreach ($pathsAttachments as $key => $value) {
                            $rutabod = strpos($value["original"], "bodega");
                            if ($rutabod === false) {
                                $path = BODEGAPATH.$value["original"];
                            } else {
                                $path = BODEGAPATH.str_replace("../bodega/", "", $value["original"]);
                            }
                            $strFile =  file_get_contents($path);
                            $strFileEncoded64 = base64_encode($strFile);  
                            $datosAttachment['Attachments'][]= Array("fileBase64Binary"=>$strFileEncoded64, "nombreArchivo"=>$value["nameAttachment"]);
                            $objCorreo->agregarAdjunto($path, $value["nameAttachment"]);
                        }
                    }
                }
                if(isset($_POST)){
                    $datos= $_POST;
                    
                    if(isset($datos['destinatario']) && strlen($datos['destinatario'])){
                        $destinatarios = explode(";", $datos['destinatario']);
                    }
                    if(isset($datos['cc']) && strlen($datos['cc'])){
                        $cc = explode(";", $datos['cc']);
                    }
                    if(isset($datos['cco']) && strlen($datos['cco'])){
                        $cco = explode(";", $datos['cco']);
                    }
                    
                    $cuerpm = str_replace('XYX', $cuerpo, $objCorreo->Body);
					
                    $retorno = $objCorreo->enviarCorreo($destinatarios, $cc, $cco, $asunto, $cuerpm);
                    $fyh = BODEGAPATH . "debugEnv_" . date('Ymd_His') . ".html";
         
					if ($retorno[0] === TRUE) {
                        $usuarioEnvia['UsuarioTXDT'][]= Array("login"=>'ENVIOSWEB');
                        $datosEnvio['datosEnviarRadicado'][]= Array("NoRadicado"=>$NoRadicadoSalida,
                                                                   "NoCopia"=>0,
                                                                   "codigoEmpresaEnvio"=>$tmpCodEnvio,
                                                                   "peso"=>0,
                                                                   "NoGuia"=>0,
                                                                   "NoPlanilla"=>0,
                                                                   "observacion"=>"Destino: ".$datos['destinatario']." copia:".$datos['cc'].$datos['cco']);
                                                
                        $this->ClienteOrfeoServiceWCF = new OrfeoServiceWCFClient();
                        $response = $this->ClienteOrfeoServiceWCF->enviarRadicadoJSON(json_encode($usuarioEnvia),json_encode($datosEnvio));
                        file_put_contents($fyh, $response["RespuestaEstado"][0]['mensaje'], FILE_APPEND);
                        try{
                           	$response = (array)json_decode($response,true);
                           	$retorno = $retorno . $response["RespuestaEstado"][0]['mensaje'];
                           	$resp = array('success' => true, 'message' => $retorno.'');
                        }
	                    catch (Exception $ex)
    	                {
    	                	$resp = array('success' => false, 'message' => $ex->getMessage());
            	        }
					} else {
						//Crear log para envio posterior de correos
					    file_put_contents($fyh, $NoRadicadoSalida."-".$tmpCodEnvio."-".$datos['destinatario']."-".$datos['cc']."-".$datos['cco'], FILE_APPEND);
						$sql = "insert into sgd_correonoenviado (radi_nume_radi, radi_nume_sal, remitente, asunto, cuerpo , para, cc, cco, anexosbase64json) values ".
								"(".$NoRadicadoPadre.", ".$NoRadicadoSalida.", '".$datos['nremitente']."', '".$asunto."', '". addslashes($cuerpm) ."', '".$datos['destinatario']."',".
								"'".$datos['cc']."', '".$datos['cco']."', '".base64_encode($datos['pathsAttachments'])."')";
						$retorno = $this->db->conn->Execute($sql);
						$resp = $retorno ? array('success' => false, 'message' => 'No pudo enviar correo pero el sistema lo enviara en unos minutos.') : array('success' => false, 'message' => 'No pudo enviar correo ni guardar log en BD.');
					}
                }
            }
        }
        catch (Exception $ex)
        {
        	$resp = array('success' => false, 'message' => $ex->getMessage());
        }
        return json_encode($resp);
    }
    
    private function obtenerPasswordWEBEMAILS($correo){
        $WEBMAILS = json_decode(WEBEMAILS,true);
        foreach ($WEBMAILS as $key => $value) {
            if(strtolower($value['correo'])==strtolower($correo)){
                return $value['password'];
            }
        }
        return false;
    }
}