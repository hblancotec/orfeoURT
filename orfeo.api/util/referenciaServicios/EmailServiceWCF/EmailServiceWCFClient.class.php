<?php

class EmailServiceWCFClient {

    
     /**
     * 
     * Constructor del Cliente del Servicio de Email WCF
     */
    public function __construct() {
        
        try
        {   $srv =explode(":", SERVER_MAIL);
            $this->server_mail =$srv[0];
            $this->server_port_mail = $srv[1];
            $this->usuario_mail = CORREO_MAIL ;
            $this->passwd_mail = PASSWORD_MAIL;
            $this->tls_mail = TLS_MAIL;
            $this->auth_mail = REQUIRE_AUTH;
            $objApp= new AplicativoExterno();
            $this->datosWS= $objApp->retornaUrlConexionWS(4);
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
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_WAIT_ONE_WAY_CALLS
                );
                $this->_conSoap = new SoapClient($this->datosWS['URL'], $options);
            }
        }
        catch (SoapFault $ex)
        {
           return "<h2>Exception error!</h2></b>".$e->getMessage();
        }
    }
    
    /**
     * 
     * @param type $SMTPHost
     * @param type $SMTPPort
     * @param type $SMTPUSer
     * @param type $SMTPPassword
     * @param type $de
     * @param type $para
     * @param type $asunto
     * @param type $mensaje
     * @param type $CC
     * @param type $sslEnabled
     * @return type
     */
    public function enviarCorreo($de,$deNombre,$para,$asunto,$mensaje,$CC,$CCO,$attachmentsJSON) 
    {
        try{
            $respuesta=$this->_conSoap->enviarCorreo(Array("usuarioServicioEnvioCorreo"=> $this->datosWS['USUARIO'],
                                                           "contrasenaServicioEnvioCorreo"=>$this->datosWS['PASSWORD'], 
                                                           "SMTPHost"=>$this->server_mail, 
                                                           "SMTPPort"=>$this->server_port_mail, 
                                                           "SMTPUSer"=>$this->correo_mail, 
                                                           "SMTPPassword"=>$this->passwd_mail, 
                                                           "de"=>$de, 
                                                           "deNombre"=>$deNombre,
                                                           "para"=>$para, 
                                                           "asunto"=>$asunto, 
                                                           "mensaje"=>$mensaje, 
                                                           "CC"=>$CC,
                                                           "CCO"=>$CCO, 
                                                           "sslEnabled"=>$this->tls_mail,
                                                           "requireAuth"=>$this->auth_mail, 
                                                           "attachmentsJSON"=>$attachmentsJSON 
                                                            ));
            return $respuesta->enviarCorreoResult;
        }
        catch (SoapFault $ex)
        {
            return "<h2>Exception error!</h2></b>".$ex->getMessage();
        }
    }   
}

?>
