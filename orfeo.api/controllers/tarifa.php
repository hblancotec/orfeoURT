<?php

class Tarifa extends Controller {

    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    
    function getTarifaJson(){
        $data = $this->model->getTarifaJson();
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }
}
?>