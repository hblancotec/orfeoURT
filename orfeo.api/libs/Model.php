<?php

class Model {

    var $db;
    
    var $error;
    
    function __construct() {
        try{
            $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
            if(!$this->db)
            {
                $this->error= "Error en el Objeto de conexion". $this->db->error;
                unset($this->db->error);
            }
        }
        catch (Exception $ex)
        {
            $this->error= $ex->getMessage();
        }
    }

}