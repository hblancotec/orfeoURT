<?php
class OrfeoServiceWCFClient
{
    /**
     * 
     * Constructor del Cliente del Servicio de Orfeo WCF
     */
    public function __construct() {
        
        
        try
        {
            $objApp= new AplicativoExterno();
            $this->datosWS= $objApp->retornaUrlConexionWS(1);
            if($this->datosWS && is_array($this->datosWS))
            {
            $options = array( 
                //'soap_version'    => SOAP_1_2, 
                'exceptions'      => true, 
                //'trace'           => 1//, 
                //'wdsl_local_copy' => true
                "connection_timeout"=>30,
                'trace'=>true,
                'keep_alive'=>false,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS
                );
                $this->_conSoap = new SoapClient($this->datosWS['URL'], $options);
            $this->setCredenciales();
        }
            else
            {
                throw new Exception("<h2>Error no se pudieron Obtener las credenciales para el WSDL!</h2></b>");
            }
        }
        catch (SoapFault $ex)
        {
            throw new Exception("Error de cliente de Servicio:".$ex->getMessage()); 
        }
        catch (Exception $ex){
            throw new Exception("Error de cliente de Servicio:".$ex->getMessage()); 
        }
    }
    
    /**
     * Configura las credenciales en el HEADER SOAP
     */
    public function setCredenciales()
    {     
        $this->auth->username = $this->datosWS['USUARIO'];
        $this->auth->password = Hash::create('MD5',$this->datosWS['PASSWORD']);
        $wsse_header_auth = new WsAuthHeader($this->auth->username, $this->auth->password);  
        $this->_conSoap->__setSoapHeaders(array($wsse_header_auth));
    }

    /**
     * 
     * @param string $usuarioRadicaJSON
     * @param string $usuarioDestinoJSON
     * @param array $datosContactoJSON
     * @param array $datosRadicacionJSON
     * @return array
     */
    public function radicarDocumentoJSON($usuarioRadicaJSON=null, $usuarioDestinoJSON=null,$datosContactoJSON=null, $datosRadicacionJSON=null) 
    {
        try{
            $respuesta=$this->_conSoap->radicarDocumentoJSON(Array("usuarioRadica"=>$usuarioRadicaJSON,"usuarioDestino"=>$usuarioDestinoJSON,"datosContacto"=>$datosContactoJSON,"datosRadicacion"=>$datosRadicacionJSON));
            return $respuesta->radicarDocumentoJSONResult;
        }
        catch (SoapFault $ex)
        {
            $data["RespuestaRadicadoDT"][0]['estado'] =false;
            $data["RespuestaRadicadoDT"][0]['mensaje']="<h2>Exception Error!</h2></b>".$ex->getMessage();
            return json_encode($data);
        }
    }   
    
    /**
     * 
     * @param string $usuarioConsultaJSON
     * @param array $datosConsultaContactoCiudadanoJSON
     * @return array
     */
    public function consultarContactoCiudadanoJSON($usuarioConsultaJSON=null, $datosConsultaContactoCiudadanoJSON=null)
    {
        try{
            $respuesta=$this->_conSoap->consultarContactoCiudadanoJSON(Array("usuarioConsulta"=>$usuarioConsultaJSON,"datosConsultaContactoCiudadano"=>$datosConsultaContactoCiudadanoJSON));
            return $respuesta->consultarContactoCiudadanoJSONResult;
        }
        catch (SoapFault $ex)
        {
            $data["RespuestaEstado"][0]['estado']=false;
            $data["RespuestaEstado"][0]['mensaje']="<h2>Exception Error!</h2></b>".$ex->getMessage();
            return json_encode($data);
        }
    }
    
    /**
     * 
     * @param string $usuarioConsultaJSON
     * @param array $datosConsultaJSON
     * @return array
     */
     public function consultarRadicadoJSON($usuarioConsultaJSON=null, $datosConsultaJSON=null)
    {
         //echo $usuarioConsultaJSON;
        try{
            $respuesta=$this->_conSoap->consultarRadicadoJSON(Array("usuarioConsulta"=>$usuarioConsultaJSON,"datosConsulta"=>$datosConsultaJSON));
            return $respuesta->consultarRadicadoJSONResult;
        }
        catch (SoapFault $ex)
        {
            $data["respuestaEstado"][0]['estado']=false;
            $data["respuestaEstado"][0]['mensaje']="<h2>Exception Error!</h2></b>".$ex->getMessage();
            return json_encode($data);
        }
    }
    
    /**
     * 
     * @param string $usuarioEnvia
     * @param array $datosEnvio
     * @return array
     */
    public function enviarRadicadoJSON($usuarioEnvia=null, $datosEnvio=null){
        
        try{
            $respuesta=$this->_conSoap->enviarRadicadoJSON(Array("usuarioEnvia"=>$usuarioEnvia,"datosEnvio"=>$datosEnvio));
            return $respuesta->enviarRadicadoJSONResult;
        }
        catch (SoapFault $ex)
        {
            $data["RespuestaEstado"][0]['estado']=false;
            $data["RespuestaEstado"][0]['mensaje']="<h2>Exception Error!</h2></b>".$ex->getMessage();
            return json_encode($data);
        }
    }
    
    
    public function consultarDivipolaJSON(){
        
        try{
            $respuesta=$this->_conSoap->consultarDivipolaJSON(Array("usuarioEnvia"=>$usuarioEnvia,"datosEnvio"=>$datosEnvio));
            return $respuesta->consultarDivipolaJSONResult;
        }
        catch (SoapFault $ex)
        {
            $data["RespuestaEstado"][0]['estado']=false;
            $data["RespuestaEstado"][0]['mensaje']="<h2>Exception Error!</h2></b>".$ex->getMessage();
            return json_encode($data);
        }
    }
    

}


?>
