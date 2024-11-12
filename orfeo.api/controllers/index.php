<?php

class Index extends Controller {

    function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }
    
    function index() 
    {
        $this->view->title = 'Home';
        $this->view->render('header');
        $this->view->render('index/index');
        $this->view->render('footer');
    }
    
    function logout()
    {
        Session::destroy();
        header('location: ' . URL .  'login');
        exit;
    }
    
}