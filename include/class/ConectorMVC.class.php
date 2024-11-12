<?php

/**
 * Esta Clase permite conectar Orfeo con el nuevo diseño en MVC
 *
 * @author omalagon
 */
class ConectorMVC
{

    private $_url = Array(
        ""
    );

    public function __construct()
    {
        ;
    }

    /**
     *
     * @param string $path
     *            es la ruta de la funcionalidad sin incluir la URL completa.
     */
    public function setPathFuncionalidad($path)
    {
        // $url = isset($_GET['url']) ? $_GET['url'] : null;
        $url = rtrim($path, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $this->_url = explode('/', $url);
    }

    public function init()
    {
        require_once PATHMVC . 'config.php';
        require_once PATHMVC . 'util/Auth.php';
        spl_autoload_register(function ($class) {
            require_once PATHMVC . "libs/" . $class . ".php";
        });
        $obj = new Bootstrap();
        $obj->_setUrl($this->_url);
        $obj->setControllerPath(PATHMVC . "controllers/");
        $obj->setModelPath(PATHMVC . "models/");
        $obj->setViewPath(PATHMVC . "views/");
        $obj->_loadExistingController();
        $obj->_callControllerMethod();
    }
}

?>
