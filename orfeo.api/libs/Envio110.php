<?php

class Envio110 extends Envios {
    public function __construct() {
        parent::__construct();
    }

    public function validarDatos() {
        return parent::validarDatos();
    }
    
    public function enviarRadicado() {
        return parent::enviarRadicado();
    }
    
    public function retornaDestino472($pe, $fe, $re, $em) {
        return parent::retornaDestino472($pe, $fe, $re, $em);
    }
    
    public function devolverValorUnitario($pe, $fe, $re, $em) {
        return parent::devolverValorUnitario($pe, $fe, $re, $em);
    }
}

?>
