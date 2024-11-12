<?php

class Expediente extends Controller {
    
    public function __construct() {
        parent::__construct();
       //Auth::handleLogin();
    }
    
   
    
    /**
     * @POST
     * Funcionalidad insumo para grilla de Carpeta,
     * Retorna o imprime en Formato JSON la lista de registros correspondientes a la carpeta seleccionada 
     * teniendo en cuenta los parametros recibidos por el POST
     */
   function listadoTransferenciasJSON()
   {
       $this->view->data = $this->model->listadoTransferenciasJSON();
       $this->view->render('expediente/listadoTransferenciasJSON');
   }

}

?>