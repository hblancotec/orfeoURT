<?php
//header('Content-Type: text/html; charset=utf-8');
//header('content-type:application/json;charset=utf-8');

session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
}
include_once("../config.php");

if(isset($_FILES["upload"])) {
    
    $filename = $_FILES["upload"]["name"];

    $directorio = BODEGAPATH ."tmp\\".$filename;
    
    $json = array();
    try {
        $success = move_uploaded_file($_FILES["upload"]["tmp_name"], $directorio);
        if($success){
            $json["uploaded"] = true;
            $json["url"] = $filename;
        } else  {
            $json["uploaded"] = false;
            $json["error"] = "Error Uploaded";
        }
    }
    catch (Exception $e)
    {
        $json["uploaded"] = false;
        $json["error"] = $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($json);
}


/*$config['upload_path'] = './assets/admin/img/uploads';

$config['allowed_types'] = 'gif|jpg|png|jpg';
$config['max_size'] = 2000;
$new_name = 'blog-'.date("Y-m-d").'-'.time();
$config['file_name'] = $new_name;
$this->load->library('upload', $config);

if(!$this->upload->do_upload('upload')){
    echo json_encode(array('error'=> $this->upload->display_errors()));
} else {
    $uploadData = $this->upload->data();
    echo json_encode(array('file_name'=> $uploadData['file_name']));
}*/