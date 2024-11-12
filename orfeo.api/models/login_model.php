<?php

class Login_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        try
        {
    
            $sql="SELECT usua_login userid, usua_nuevo role FROM usuario WHERE 
                                    usua_login = ? AND usua_pasw  = ?";
            $rs=$this->db->select($sql,array( $_POST['login'],substr(Hash::create('MD5', $_POST['password']),1,26)),true);        
            if($rs){
                $data = $rs->fields;
                $count =  $rs->RecordCount();

                if ($count > 0) {
                    // login
                    Session::init();
                    Session::set('role', $data['role']);
                    Session::set('loggedIn', true);
                    Session::set('userid', $data['userid']);
                    header('location: ../index');
                } 
                else 
                {
                    header('location: ../login');
                }
            }
            else
            {
                echo "Error al ejecutar la sentencia.";
            }
        }
        catch(Exception $ex)
        {
            
            $this->view->msg = 'error!';
        }
    }
    
}