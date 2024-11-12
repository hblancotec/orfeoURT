<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Aplicativo
 *
 * @author omalagon
 */
class Aplicativo {
    
    var $id;
    var $ipAcceso;
    var $nombre;
    var $estado;
    var $depResponsable;
    var $usuaResponsable;
    
    
    function __construct($db)
    {
            $this->cnn = $db;
            $this->cnn->SetFetchMode(ADODB_FETCH_ASSOC);
    }
    
    function getDatosApp(){
        
    }
    
    function validarSeguridad($codApp,$dep,$login,$passwdMD5, $codMetodo){
        
        $band=true;
        //$msg[0]="error";
        if(!is_numeric($codApp)){
            $band=false;
            $msg[]="Codigo Aplicativo no es entero! del tipo de dato complejo \"seguridad\"";
        }
        if(!is_numeric($dep)){
            $band=false;
            $msg[]="Codigo de dependencia no es numerico! del tipo de dato complejo \"seguridad\"";
        }
        if(strlen(trim($login))==0){
            $band=false;
            $msg[]="Login de usuario no puede ser vacio! del tipo de dato complejo \"seguridad\"";
        }
        if(strlen(trim($passwdMD5))==0){
            $band=false;
            $msg[]="El password no puede ser vacio! del tipo de dato complejo \"seguridad\"";
        }
        if($band){
            $sql="SELECT * FROM SGD_APLICACIONES APP 
                  JOIN USUARIO U ON U.USUA_LOGIN=APP.USUA_LOGIN
                  WHERE APP.SGD_APLI_CODIGO=$codApp AND APP.SGD_APLI_DEPE=$dep AND APP.USUA_LOGIN='$login' AND U.USUA_PASW='".SUBSTR($passwdMD5,1,26)."'";
            $ADODB_COUNTRECS=TRUE;
            $rs=$this->cnn->Execute($sql);
            $ADODB_COUNTRECS=FALSE;
            if($rs && $rs->RecordCount()>0){
                $band=true;
                //$msg[]="Datos Seguridad Ok!";
                $sql="SELECT * FROM PERFIL_APLICATIVOS_ENLACE P WHERE SGD_APLI_CODIGO=$codApp AND COD_METODO=$codMetodo";
                $ADODB_COUNTRECS=TRUE;
                $rs=$this->cnn->Execute($sql);
                $ADODB_COUNTRECS=FALSE;
                if($rs && $rs->RecordCount()==0){
                    $band=false;
                    $msg[]="Metodo No permitido, Comuniquese con el administrador de Orfeo!";
                }
            }
            else{
                $band=false;
                $msg[]="Datos Seguridad no validos, Comuniquese con el administrador de Orfeo!";
            }
        }
        if(count($msg))
            $datos['msg']=$msg;
        $datos['band']=$band;
        return $datos;
            
    }
    
    function permiteIP($ip,$codMetodo){
       $band=false; 
       $sql="SELECT * FROM SGD_APLICACIONES APP 
                  JOIN PERFIL_APLICATIVOS_ENLACE P ON P.SGD_APLI_CODIGO=APP.SGD_APLI_CODIGO
                  WHERE APP.IP_ACCESO='$ip' AND P.COD_METODO=$codMetodo";
        $ADODB_COUNTRECS=TRUE;
        $rs=$this->cnn->Execute($sql);
        $ADODB_COUNTRECS=FALSE;
        if($rs && $rs->RecordCount()>0)
            $band=true;
        else
            $band=false;
        return $band;
    }
    
    function retornaUrlConexionWS($codApp){
            $sql="SELECT * FROM SGD_APLICACIONES APP WHERE APP.SGD_APLI_CODIGO=$codApp ";
            $ADODB_COUNTRECS=TRUE;
            $rs=$this->cnn->Execute($sql);
            $ADODB_COUNTRECS=FALSE;
            if($rs && !$rs->EOF){
                $band=true;
                $datos['URL']=$rs->fields['CLIENTE_WS_URLWSDL'];
                $datos['USUARIO']=$rs->fields['CLIENTE_WS_USUARIO'];
                $datos['PASSWORD']=$rs->fields['CLIENTE_WS_PASSWORD'];
            }
            else{
                $datos=false;
            }
            return $datos;
    }
    
    function retornaDatosConexionClienteBD($codApp){
            $sql="SELECT * FROM SGD_APLICACIONES APP WHERE APP.SGD_APLI_CODIGO=$codApp ";
            $ADODB_COUNTRECS=TRUE;
            $rs=$this->cnn->Execute($sql);
            $ADODB_COUNTRECS=FALSE;
            if($rs && !$rs->EOF){
                $band=true;
                $datos['CLIENTE_BD_SERVER']=$rs->fields['CLIENTE_BD_SERVER'];
                $datos['CLIENTE_BD_DATABASE']=$rs->fields['CLIENTE_BD_DATABASE'];
                $datos['CLIENTE_BD_DRIVER']=$rs->fields['CLIENTE_BD_DRIVER'];
                $datos['CLIENTE_BD_USUARIO']=$rs->fields['CLIENTE_BD_USUARIO'];
                $datos['CLIENTE_BD_PASSWORD']=$rs->fields['CLIENTE_BD_PASSWORD'];
            }
            else{
                $datos=false;
            }
            return $datos;
    }
    
}

?>
