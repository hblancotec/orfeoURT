<?php
//require 'util/referenciaServicios/OrfeoServiceWCF/OrfeoServiceWCFClient.class.php';
class _Anexo_Model extends Model
{
     public function __construct()
    {
        parent::__construct();
    }
    
    
    function obtenerExtensionTiposAnexoJS()
    {
        $sql="select ANEX_TIPO_EXT as ext from anexos_tipo where anex_tipo_codi>0";
        $rs=$this->db->conn->GetArray($sql);
        $return="";
        if($rs && is_array($rs)){
            foreach ($rs as $i => $value){
                
                $return .= "'".strtolower($value['ext'])."',";
            }
            
            $return = "var  fileAcepts= [".$return."]";
            $return=str_replace(",]", "]", $return);
            echo $return;
        }else{
            return false;
        }
    }
    
    function obtenerTipoAnexo($ext)
    {
        $rs=$this->db->select("select * from anexos_tipo where anex_tipo_ext=?",Array($ext));
        if($rs && is_array($rs->fields)){
           return $rs->fields;
        }else{
            return false;
        }
    }
    
    function obtenerAnexoSalida($noRadicado){
        
        $sql="select * from ANEXOS where radi_nume_salida=?";
        $rs=$this->db->select($sql,Array($noRadicado));
        $return="";
        if($rs && is_array($rs->fields)){
            return $rs->fields;
        }else{
            return false;
        }
    }
    
    
    function actualizaDatosAnexo($datos,$where){
        
         if(is_array($datos) && count($datos)){

            $rs=$this->db->update("ANEXOS", $datos, $where);
            if($rs){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    } 
    
    /**
    * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
    * @param char $var
    * @return numeric 
    * */
   function return_bytes($val)
   {	$val = trim($val);
           $ultimo = strtolower($val{strlen($val)-1});
           switch($ultimo)  {	// El modificador 'G' se encuentra disponible desde PHP 5.1.0
                   case 'g':	$val *= 1024;
                   case 'm':	$val *= 1024;
                   case 'k':	$val *= 1024;
           }
           return $val;
   }
   
   
   /**
     * Busca el maximo numero de anexo adicionado a un radicado, entre los radicados base, no las copias
     * @param $radicacion  es el codigo del radicado a analizar
     * @return   string
     */
    function obtenerMaximoNumeroAnexo($radicado){

        $sw = 0;
        $rs = $this->db->select("select max(anex_codigo) as NUM from anexos  where anex_radi_nume =?",Array($radicado));

        if  ($rs && !$rs->EOF){
            if($rs->fields["NUM"]){
                $auxnumero = $rs->fields["NUM"];
                $auxnumero = substr ($auxnumero, strlen($auxnumero)-4, 4);
            }else{
                $auxnumero = 1;
            }
        }
        else{
            $auxnumero = 1;
        }

        while ($sw==0) {
            $uxnumeroSig = $auxnumero + 1;
            $rs=$this->db->conn->Execute("select anex_codigo as NUM from anexos where anex_radi_nume=$radicado and anex_codigo like '%$uxnumeroSig'");

            if (!$rs || $rs->EOF){
                $sw=1;
            }
            $auxnumero = $auxnumero+1;
        }
        return($auxnumero);
    }
   
    public function insertarAnexo($datos)
    {
        $rs=$this->db->insert("ANEXOS",$datos);
        if($rs){
            return true;
        }else{
            return false;
        }
    }
   
}
?>
