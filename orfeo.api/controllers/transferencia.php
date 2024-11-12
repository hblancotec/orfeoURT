<?php

class Transferencia extends Controller {
    
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    
    /**
     * Index
     */
    function index() 
    {
       $this->view->css= Array("../public/js/libs/ext-4.2.1/resources/css/ext-all.css"
                                );
       $this->view->js= Array("../public/js/libs/app/util/listaCarpeta.config.js.php?carpeta=".$_GET['carpeta']."&tipo_carpt=".$_GET['tipo_carpt']."&pathMVC=/orfeo.api/"."&NoRadicado=".$_GET['NoRadicado'],
                              "../public/js/libs/ext-4.2.1/ext-all.js",
                              "../public/js/libs/app/appTransferencias.js"
                         
                             );
       $this->view->db = $this->model->db;
       $this->view->title = 'Transferencias';
       $this->view->render('transferencia/index');
    }


}

?>