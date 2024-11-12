<?php
class _Anexo extends Controller {
    
    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    
    function index() 
    {
        $this->view->title = 'Error de acceso!';
        $this->view->setMsg('Esta pagina no tiene funcionalidades asociadas.');
        $this->view->render('error/inc/header');
        $this->view->render('error/index');
        $this->view->render('error/inc/footer');
    }
    
    function obtenerExtensionTiposAnexoJS()
    {
        $this->model->obtenerExtensionTiposAnexoJS();
    }

}
?>
