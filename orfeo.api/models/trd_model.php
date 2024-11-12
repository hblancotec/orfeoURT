<?php

class Trd_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    
    function listadoSeriesJSON(){
        
        $data->success=true;
        $data->total=7;
        $data->root[]=Array('codigoSerie'=>'1','descripcion'=>'Serie 1');
        $data->root[]=Array('codigoSerie'=>'2','descripcion'=>'Serie 2');
        $data->root[]=Array('codigoSerie'=>'3','descripcion'=>'Serie 3');
        $data->root[]=Array('codigoSerie'=>'4','descripcion'=>'Serie 4');
        $data->root[]=Array('codigoSerie'=>'5','descripcion'=>'Serie 5');
        $data->root[]=Array('codigoSerie'=>'6','descripcion'=>'Serie 6');
        $data->root[]=Array('codigoSerie'=>'7','descripcion'=>'Serie 7');
        
        return json_encode($data);
    }
    
    function listadoSubSeriesJSON(){
        $data->success=false;
        $data->total=0;
        if($_REQUEST['codigoSerie'])
        {
            $data->success=true;
            $data->total=7;
            $data->root[]=Array('codigoSerie'=>'1','descripcion'=>'Sub Serie 1');
            $data->root[]=Array('codigoSerie'=>'2','descripcion'=>'Sub Serie 2');
            $data->root[]=Array('codigoSerie'=>'3','descripcion'=>'Sub Serie 3');
            $data->root[]=Array('codigoSerie'=>'4','descripcion'=>'Sub Serie 4');
            $data->root[]=Array('codigoSerie'=>'5','descripcion'=>'Sub Serie 5');
            $data->root[]=Array('codigoSerie'=>'6','descripcion'=>'Sub Serie 6');
            $data->root[]=Array('codigoSerie'=>'7','descripcion'=>'Sub Serie 7');
        }
        
        return json_encode($data);
    }
     
}
?>
