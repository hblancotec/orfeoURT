<?php

class TipoIdentificacion extends Controller {

    public function __construct() {
        parent::__construct();
        //Auth::handleLogin();
    }

    /**
     * Index
     */
    function index()
    {
        
    }

    /**
     * @POST
     * Retorna en formato JSON una lista de los tipos de identificación.
     */
    function getDataComboTiposIdentificacionJson()
    {
        $data = $this->model->getDataComboTipoIdentificacionJson();
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }

}

?>