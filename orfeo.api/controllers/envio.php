<?php

class Envio extends Controller {

    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }

    /**
     * Index
     */
    function index() {
		$this->view->css = Array("../public/js/libs/ext/packages/ext-theme-".ESTILO."/build/resources/ext-theme-".ESTILO."-all.css");
        $this->view->js = Array(
			"../public/js/libs/app/util/Envios.config.js.php?dep=".$_SESSION['dependencia']."&usr=".$_SESSION['usua_nomb']."&SERVIDOR=".SERVIDOR."&krd=".$_GET['krd'],
			"../public/js/libs/ext/build/ext-all.js",
			"../public/js/libs/ext/packages/ext-theme-".ESTILO."/build/ext-theme-".ESTILO.".js",
			"../public/js/libs/app/appModuloEnvios.js"
        );
        $this->view->db = $this->model->db;
        $this->view->title = 'Modulo de Envios';
        $this->view->render('envio/index');
    }

    /**
     * Funcionalidad insumo para la grilla de radicados para envios.
     * Retorna o imprime en Formato JSON la lista de formas de envios
     * @param int $dependencia Codigo de la dependencia
     */
    function getDataGrillaRadicadosEnviosJson() {
        //define("RUTA_LOG", "../messages.log");
        //$result = $this->model->write_log("Entra a Funcion getDataGrillaRadicadosEnviosJson ", RUTA_LOG);
        $data = $this->model->getDataGrillaRadicadosEnviosJson();
        //$result = $this->model->write_log("Sale Funcion getDataGrillaRadicadosEnviosJson ", RUTA_LOG);
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }

    /**
     * Funcionalidad insumo para combo de Formas de Envio,
     * Retorna o imprime en Formato JSON la lista de formas de envios
     * teniendo en cuenta los parametros recibidos por el POST
     */
    function getDataComboFormasDeEnvioJson($soloActivas) {
        $data = $this->model->getDataComboFormasDeEnvioJson($soloActivas);
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }

    function setEnviosRadicadosJson() {
        $data = $this->model->setEnviosRadicados();
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }

}

?>