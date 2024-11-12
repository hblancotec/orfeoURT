<?php

class radicacionmasiva extends Controller {

    public function __construct() {
        parent::__construct();
        //Auth::handleLogin();
    }

    /**
     * Index
     */
    function index() {
        $this->view->css = Array("../public/js/libs/ext/packages/ext-theme-" . ESTILO . "/build/resources/ext-theme-" . ESTILO . "-all.css");
        $this->view->js = Array(
            "../public/js/libs/ext/build/ext-all.js",
            "../public/js/libs/ext/packages/ext-theme-" . ESTILO . "/build/ext-theme-" . ESTILO . ".js",
            "../public/js/libs/app/appRadicacionMasiva.js"
        );
        $this->view->db = $this->model->db;
        $this->view->title = 'Modulo de Envios';
        $this->view->render('radicacionmasiva/index');
    }

    function datosLlenos($file) {
        $data = $this->model->setFileDataEnGrilla($file);
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }

    function datosVacios() {
        $this->view->render('radicacionmasiva/datosVacios');
    }

    function cargarDataFile() {
        $data = $this->model->uploadFileData();
        $this->view->data = $data;
        $this->view->render('returnDataText');
    }

    function combinarPlantillaWcf() {
        ini_set('default_socket_timeout', 25200); // => Configuramos un timeout de 6 horas....
        //set_time_limit(0); =>Esto no sirve en la nueva forma de programación.
        $data = $this->model->mergeDocDataWcf();
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }
    
    function combinarPlantillaZip() {
        $data = $this->model->mergeDocDataZip();
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }
    
    function descargarZip() {
        $data = $this->model->downloadZipMasiva();
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }
    
    function cargarZipPlantillas() {
        $data = $this->model->uploadZipPlantillas();
        $this->view->data = $data;
        $this->view->render('returnDataText');
    }

}