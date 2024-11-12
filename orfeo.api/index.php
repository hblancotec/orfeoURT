<?php

require "config.php";
require "util/Auth.php";

// Also spl_autoload_register (Take a look at it if you like)
//function __autoload($class) {
spl_autoload_register(function($class) {
    try
    {
        if(file_exists(LIBS . $class .".php"))
        {
            include_once LIBS . $class .".php";
        }
        else if(function_exists("DOMPDF_autoload")){
            DOMPDF_autoload($class);
        }
        else{
            throw new Exception("No se encuentra la Clase :".$class);
        }
    }  catch(Exception $ex) 
    {
        throw new Exception("Error al cargar la Clase :".$class." -  ".$ex->getMessage());
    }
});
if(function_exists('xdebug_disable')){ xdebug_disable(); };
// Carga Bootstrap!
$bootstrap = new Bootstrap();

$bootstrap->init();