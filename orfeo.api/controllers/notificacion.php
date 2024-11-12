<?php

class Notificacion extends Controller {

   
    public function __construct() {
        parent::__construct();
       // Auth::handleLogin();
    }
    
    public function index() 
    {   
        $this->view->title = 'Error de acceso!';
        $this->view->setMsg('Esta pagina no tiene funcionalidades asociadas.');
        $this->view->render('error/inc/header');
        $this->view->render('error/index');
        $this->view->render('error/inc/footer');
    }
    
    public function enviarCorreoRespuestaRapida(){
    	$data = $this->model->enviarCorreoRespuestaRapida();
        $this->view->data = $data;
        $this->view->render('returnDataJson');
    }
    
}