<?php
/**
 * 
 */
class Auth
{
    
    public static function handleLogin()
    {
        @session_start();
        $logged = isset($_SESSION['loggedIn']) || isset($_SESSION["login"])? true: false;
        if ($logged == false || !isset($logged)) {
            session_destroy();
            //$this->view->msg="Debe iniciar sesion";
            header('location: '.URLLOGIN);
            exit;
        }
    }
    
}