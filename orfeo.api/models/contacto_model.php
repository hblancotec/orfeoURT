<?php
require 'util/referenciaServicios/OrfeoServiceWCF/OrfeoServiceWCFClient.class.php';
class Contacto_Model extends Model
{
     public function __construct()
    {
        parent::__construct();
    }
    
    
    function buscarContactoCiudadanoSOA()
    {
        $this->ClienteOrfeoServiceWCF = new OrfeoServiceWCFClient();
        $json1 = "{UsuarioTXDT:".json_encode(Array(Array("documento"=>"800975021","login"=>"omalagon")))."}";
        $json2 = "{DatosConsultarContactoCiudadano:".json_encode(Array(Array("nombre"=>"JAMES")))."}";
        $response = $this->ClienteOrfeoServiceWCF->consultarContactoCiudadanoJSON($json1, $json2);
        var_dump(json_decode($response,true));
        
    }
}

?>
