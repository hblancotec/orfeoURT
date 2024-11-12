<?php

class Dependencia_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Se encarga de retornar el listado de radicados de un Usuario especifico y una carpeta especifica
     * @POST
     */
    function getDataComboDependenciasJson($soloActivas)
    {
        try{
            $cat = $this->db->conn->Concat("depe_codi","' '","depe_nomb");
            $act = ($soloActivas==1)? " where dependencia_estado=2" : "";
            $sql = "select depe_codi as codigo, $cat as nombre from dependencia $act order by depe_codi";
            $rs = $this->db->conn->Execute($sql);
            $i=0;
            foreach ($rs as $key => $row) {
                $data[$i]['id'] = $row['codigo'];
                $data[$i]['descripcion'] = iconv ( 'ISO-8859-1' , 'UTF-8//IGNORE//TRANSLIT' , $row['nombre'] );
                $i++;
            }
            $datos['datosGenerales']=$data;
        }
        catch (ADODB_Exception $ex)
        {
            return "Error:". $ex->getMessage();
        }
        $objCodificacionEspecial = new CodificacionEspecial();
        if(isset($_GET['callback']))
        {
            //header('Content-Type: application/javascript');
            return $_GET['callback']."(".$objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos)).");";
        }
        else
        {
            return $objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos));
        }       
    }
}
?>
