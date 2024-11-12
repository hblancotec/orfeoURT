<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FaxServerCliente
 *
 * @author omalagon
 */
class FaxServerCliente {
    //put your code here
    
    public $parametrosSendFax= Array('nombreArchivo'=>""
                                    ,'ArchivoSerializado'=>""
                                    ,'NFaxDestino'=>""
                                    ,'NombreDestinatario'=>""
                                    ,'NFaxRemitente'=>""
                                    ,'NombreRemitente'=>""
                                    ,'Asunto'=>""
                                    );
    
    public function __construct($conn,$rutaRaiz) {
        include_once($rutaRaiz."/webServices/appClient/class/Aplicativo.php");
        define('RUTA_RAIZ',$rutaRaiz);
        $this->conn=$conn;
    }
    
    function SendFax(){

        $respuesta['band']=true;
        if(!$this->parametrosSendFax['nombreArchivo'])
        {
            $respuesta['band']=false;
            $respuesta['msg'][]="El nombre del archivo no puede ir vacio";
        }
        if(!$this->parametrosSendFax['ArchivoSerializado'])
        {
            $respuesta['band']=false;
            $respuesta['msg'][]="se debe enviar el archivo serializado";
        }
        if(!$this->parametrosSendFax['NFaxDestino'])
        {
            $respuesta['band']=false;
            $respuesta['msg'][]="Debe enviar el No de fax destino";
        }
        if(!$this->parametrosSendFax['NombreDestinatario'])
        {
            $respuesta['band']=false;
            $respuesta['msg'][]="Debe enviar el nombre del destinatario ";
        }
        if(!$this->parametrosSendFax['NFaxRemitente'])
        {
            $respuesta['band']=false;
            $respuesta['msg'][]="Debe enviar el No de fax remitente";
        }
        if(!$this->parametrosSendFax['NombreRemitente'])
        {
            $respuesta['band']=false;
            $respuesta['msg'][]="Debe enviar el nombre del remitente";
        }
        if(!$this->parametrosSendFax['Asunto'])
        {
            $respuesta['band']=false;
            $respuesta['msg'][]="El nombre del archivo no puede ir vacio";
        }
        if( $respuesta['band']==true)
        {
        
            $objApp= new Aplicativo($this->conn);
            $datosWS= $objApp->retornaUrlConexionWS(2);
            if($datosWS && is_array($datosWS))
            {
                require_once(RUTA_RAIZ.'/lib/nusoap/lib/nusoap.php');
                $url=$datosWS['URL'];
                $l_oClient = new nusoap_client($url,'wsdl');
                $l_oClient->debugLevel = 1;
                $datos=Array("strFileName"=>$this->parametrosSendFax['nombreArchivo'],
                            "bFile"=>$this->parametrosSendFax['ArchivoSerializado'],
                            "strToNumber"=>$this->parametrosSendFax['NFaxDestino'],
                            "strToName"=>  substr($this->parametrosSendFax['NombreDestinatario'],0,50),
                            "strFromNumber"=>$this->parametrosSendFax['NFaxRemitente'],
                            "strFromName"=>$this->parametrosSendFax['NombreRemitente'],
                            "strSubject"=>$this->parametrosSendFax['Asunto']
                            );
                $metodo="SendFax";
                $resultado = $l_oClient->call($metodo, $datos);
                if (!$l_oClient->getError())
                {
                    $respuesta['band']=true;
                    $respuesta['msg']=$resultado;
                    return $respuesta;
                }
                else{
                    $respuesta['band']=false;
                    $respuesta['msg'][]=$l_oClient->getError();
                    return $respuesta;
                }
           }
           else
           {
               $respuesta['band']=false;
                $respuesta['msg'][]="No se pueden obtener datos del aplicativo con ID 2";
                return $respuesta;
           }
        }
        else
           {
                return $respuesta;
           }
    }
    
    function obtenerDatosView($view, $idFax=false){
        $respuesta['band']=true;
        if($idFax!==false){
            $where=" where idFax=$idFax";
        }
        $objApp= new Aplicativo($this->conn);
        $datosWS= $objApp->retornaDatosConexionClienteBD(2);
        if($datosWS){
            require(RUTA_RAIZ."/config.php");
            define('ADODB_ASSOC_CASE', 1);
            require "adodb/adodb.inc.php";
            $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
            $dsn =trim($datosWS['CLIENTE_BD_DRIVER'])."://".trim($datosWS['CLIENTE_BD_USUARIO']).":".trim($datosWS['CLIENTE_BD_PASSWORD'])."@".trim($datosWS['CLIENTE_BD_SERVER'])."/".trim($datosWS['CLIENTE_BD_DATABASE']);
            $conn = NewADOConnection($dsn); 
            if($conn){
                $conn->SetFetchMode(ADODB_FETCH_ASSOC);
                $sql="select * from $view $where ";
                $rs=$conn->Execute($sql);
                if($rs && !$rs->EOF){
                    $respuesta['band']=true;
                    $respuesta['msg']=$rs->fields;
                }
            }
            else 
            {
                $respuesta['band']=false;
                $respuesta['msg'][]="Error en conexion a la BD de Fax Server";
            }
        }
        else{
           $respuesta['band']=false;
           $respuesta['msg'][]="No se pueden obtener datos del aplicativo con ID 2";
        }
        
        return $respuesta;
    }
    
}

?>
