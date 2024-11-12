<?php

class View {

    function __construct() {
        //echo 'this is the view';
    }

    private $msg;
    private $_viewPath='views/';
    
    public function render($name, $noInclude = false)
    {
        $file = $this->_viewPath . $name . '.php';
        if (file_exists($file))
        {
            require $file;
        }
        else 
        {
            echo "No existe el archivo $name.php en la ruta: views/";
        }
           
    }
    
    public function setViewPath($path)
    {
         $this->_viewPath=$path;
    }
    
    public function setMsg($msg)
    {
        $this->msg=$msg;
    }
    
    public function getMsg()
    {
        return $this->msg;
    }

}