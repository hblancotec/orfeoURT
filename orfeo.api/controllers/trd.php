<?php

class Trd extends Controller {
    
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
   function listadoSeriesJSON()
   {
       $this->view->data = $this->model->listadoSeriesJSON();
       $this->view->render('trd/listadoSeriesJSON');
   }
   
   
   function listadoSubSeriesJSON()
   {
       $this->view->data = $this->model->listadoSubSeriesJSON();
       $this->view->render('trd/listadoSubSeriesJSON');
   }

}

?>