<?php

class Radicado extends Controller {

   
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    
    public function index() 
    {   
        $this->view->css= Array("radicado/css/default.css");
        $this->view->js= Array("radicado/js/default.js");
        $this->view->title = 'Radicacion de Documentos';
        $this->view->render('header');
        $this->view->render('radicado/index');
        $this->view->render('footer');
    }
    /**
     * Metodo de recepcion de parametros es POST
     * @param type $isSOA
     */
    public function radicar($isSOA=1)
    {
        if($isSOA==1){
        $this->model->radicacionSOA();
        }else{
             $this->model->radicacion();//Pendiente implementacion con persistencia directa a BD
        }
        //header('location:' . URL . 'radicacion');
    }
    
    function buscarRadicadosSOA()
    {
        $this->model->buscarRadicadosSOA();
    }
    
    function listado()
    {
        $this->view->title = 'Radicacion de Documentos';
        $this->view->render('header');
        $this->view->render('radicado/listado');
        $this->view->render('footer');
    }
    
    /**
     * 
     * Metodo @POST
     */
    function respuestaRapida(){

        $this->model->respuestaRapida();
    }
}