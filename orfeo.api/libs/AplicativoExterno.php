<?php

/**
 * Esta Clase permite gestionar los datos de configuración de los Aplicativos enlazados a Orfeo
 *
 * @author omalagon
 */
class AplicativoExterno {
    
    var $id;
    var $ipAcceso;
    var $nombre;
    var $estado;
    var $depResponsable;
    var $usuaResponsable;
    
    
    function __construct()
    {
        $model = new Model();    
        $this->cnn = $model->db->conn;
    }
    /**
     * Retorna los datos de conexión cliente del WS del aplicativo externo
     * @param type $codApp
     * @return boolean
     */
    function retornaUrlConexionWS($codApp){
            $sql="SELECT * FROM SGD_APLICACIONES APP WHERE APP.SGD_APLI_CODIGO=$codApp ";
            $ADODB_COUNTRECS=TRUE;
            $rs=$this->cnn->Execute($sql);
            $ADODB_COUNTRECS=FALSE;
            if($rs && !$rs->EOF){
                $band=true;
                $datos['URL']=trim($rs->fields['CLIENTE_WS_URLWSDL']);
                $datos['USUARIO']=trim($rs->fields['CLIENTE_WS_USUARIO']);
                $datos['PASSWORD']=trim($rs->fields['CLIENTE_WS_PASSWORD']);
            }
            else{
                $datos=false;
            }
            return $datos;
    }
    /**
     * Retorna los datos de conexión cliente a la BD del aplicativo externo
     * @param type $codApp
     * @return boolean
     */
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
