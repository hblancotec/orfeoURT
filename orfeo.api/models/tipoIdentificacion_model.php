<?php

class TipoIdentificacion_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Se encarga de retornar el listado de radicados de un Usuario especifico y una carpeta especifica
     * @POST
     */
    function getDataComboTipoIdentificacionJson()
    {
        try{
            $sql = "select TDID_CODI as codigo, TDID_DESC as nombre from TIPO_DOC_IDENTIFICACION order by TDID_CODI";
            $rs = $this->db->conn->Execute($sql);
            $i=0;
            foreach ($rs as $key => $row) {
                $data[$i]['id'] = $row['codigo'];
                $data[$i]['descripcion'] = $row['nombre'];
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
