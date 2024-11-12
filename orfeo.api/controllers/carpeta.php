<?php

class Carpeta extends Controller {
    
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
       $this->view->js= Array("../public/js/libs/app/util/listaCarpeta.config.js.php?carpeta=".$_GET['carpeta']."&tipo_carpt=".$_GET['tipo_carpt']."&pathMVC=".$_GET['pathMVC']."&NoRadicado=".$_GET['NoRadicado'],
                              "../public/js/libs/ext-4.2.1/ext-all.js",
                              "../public/js/libs/app/app.js",
                              "../public/js/libs/ext-4.2.1/ux/Ext.ux.form.HtmlLintEditor.js",
                              "../public/js/libs/ext-4.2.1/ux/Ext.ux.form.Multiupload.js"
                         
                             );
       $this->view->db = $this->model->db;
       $this->view->title = 'Radicacion de Documentos';
       $this->view->render('carpeta/index');
    }
    
    /**
     * @POST
     * Funcionalidad insumo para grilla de Carpeta,
     * Retorna o imprime en Formato JSON la lista de registros correspondientes a la carpeta seleccionada 
     * teniendo en cuenta los parametros recibidos por el POST
     */
   function listadoRadicadosEnCarpetaJSON()
   {
       $this->view->data =  $this->model->listadoRadicadosEnCarpetaJSON();
       $this->view->render('carpeta/listadoRadicadosEnCarpetaJSON');
   }

}

?>