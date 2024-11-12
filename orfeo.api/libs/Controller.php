<?php

class Controller {

    var $error;
    
    function __construct() {
        //echo 'Main controller<br />';
        $this->view = new View();
    }
    
    /**
     * 
     * @param string $name Name of the model
     * @param string $path Location of the models
     */
    public function loadModel($name, $modelPath = 'models/', $viewPath='views/') {
        
        $path = $modelPath . $name.'_model.php';
        
        if (file_exists($path)) {
            require $modelPath .$name.'_model.php';
            
            $modelName = $name . '_Model';
            $this->model = new $modelName();
            $this->view->setViewPath($viewPath);
        }
        if($this->model && $this->model->error)
        {
            $this->view->setMsg($this->model->error);
        }
    }

}