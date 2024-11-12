<?php

class Dependencia extends Controller {

    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }

    /**
     * Index
     */
    function index()
    {
        
    }

    /**
     * @POST
     * Funcionalidad insumo para grilla de Carpeta,
     * Retorna o imprime en Formato JSON la lista de registros a gestionar envios
     * teniendo en cuenta los parametros recibidos por el POST
     */
    function getDataComboDependenciasJson($soloActivas=1)
    {
        $data = $this->model->getDataComboDependenciasJson($soloActivas);
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }

}

?>