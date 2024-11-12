<?php

/**
 * 
 */
class Historico_Model extends Model
{
    //put your code here
    
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function insertarHistoricoRadicado($noRadicado,$codigo_dependenciatx,$codigo_usuariotx,$documento_usuariotx,$codigo_dependenciadestino,$codigo_usuariodestino,$documento_usuariodestino,$comentario,$idTx)
    {

        $datos=Array("RADI_NUME_RADI"=>$noRadicado,
                    "DEPE_CODI"=>$codigo_dependenciatx,
                    "USUA_CODI"=>$codigo_usuariotx,
                    "USUA_DOC"=>$documento_usuariotx,
                    "DEPE_CODI_DEST"=>$codigo_dependenciadestino,
                    "USUA_CODI_DEST"=>$codigo_usuariodestino,
                    "HIST_DOC_DEST"=>$documento_usuariodestino,
                    "SGD_TTR_CODIGO"=>$idTx,
                    "HIST_OBSE"=>$comentario,
                    "HIST_FECH"=>$this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp)
                    );
        $rs=$this->db->insert("HIST_EVENTOS",$datos);
        if($rs){
            return true;
        }else{
            return false;
        }
    }
    
    public function insertarHistoricoImagenRadicado($noRadicado,$path,$usuaDoc,$usuaLogin,$idTx){
        
        $datos=Array("RADI_NUME_RADI"=>$noRadicado,
                    "RUTA"=>$path,
                    "USUA_DOC"=>$usuaDoc,
                    "USUA_LOGIN"=>$usuaLogin,
                    "FECHA"=>$this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp),
                    "ID_TTR_HIAN"=>$idTx
                    );
        $rs=$this->db->insert("SGD_HIST_IMG_RAD",$datos);
        if($rs){
            return true;
        }else{
            return false;
        }
    } 
    
    public function insertarHistoricoImagenAnexo($noRadicadoPadre,$codigoAnexo,$ruta,$docUsuario,$loginUsuario,$idTx){
        
        $datos=Array("ANEX_RADI_NUME"=>$noRadicadoPadre,
                    "ANEX_CODIGO"=>$codigoAnexo,
                    "RUTA"=>$ruta,
                    "USUA_DOC"=>$docUsuario,
                    "USUA_LOGIN"=>$loginUsuario,
                    "FECHA"=>$this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp),
                    "ID_TTR_HIAN"=>$idTx
                    );
        $rs=$this->db->insert("SGD_HIST_IMG_ANEX_RAD",$datos);
        if($rs){
            return true;
        }else{
            return false;
        }
    } 
}

?>
