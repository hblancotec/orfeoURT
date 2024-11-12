<?php

class Expediente_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    
    function listadoTransferenciasJSON(){
        
        
        
        
        if($_REQUEST['codigoSerie']){
            
        }
        if($_REQUEST['codigoSubSerie']){
            
        }
        if($_REQUEST['codigoDependencia']){
            
        }
        if($_REQUEST['nombre']){
            
        }
        
        
        $sql="";
        
        
        $data->success=true;
        $data->total=7;
        $data->root[]=Array('NoExpediente'=>'2014000000000E','nombre'=>'Expediente 1', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000000E','nombre'=>'Expediente 1', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000000E','nombre'=>'Expediente 1', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000000E','nombre'=>'Expediente 1', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000000E','nombre'=>'Expediente 1', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000000E','nombre'=>'Expediente 1', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000001E','nombre'=>'Expediente 2', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000001E','nombre'=>'Expediente 2', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000001E','nombre'=>'Expediente 2', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000002E','nombre'=>'Expediente 3', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000003E','nombre'=>'Expediente 4', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000004E','nombre'=>'Expediente 5', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000005E','nombre'=>'Expediente 6', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000006E','nombre'=>'Expediente 7', 'fecha'=>Date('Y-m-d') );
        $data->root[]=Array('NoExpediente'=>'2014000000006E','nombre'=>'Expediente 7', 'fecha'=>Date('Y-m-d') );
        
        return json_encode($data);
    }
     
}
?>
