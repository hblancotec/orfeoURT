<?php
/**
  * Clase que se encarga de buscar en las tablas
  * U Usuario
  * O Otras Empresas
  * E Entidad o Esp o ....
  * F Funcionario
  * @autor JAIRO LOSADA - DNP 09/2009
  *  Modificaciones www.correlibre.org 12/2009
  * @lienceia GNU/GPL v3
  * 
  */


class ConsultasUOEF{
 /** Objeto que contiene la base de datos
  *type object recorset
  */
 
 var $db;
 var $codEsp;
 var $codOEM;
 var $codUsuario;
 var $codCiu;
 var $noDocumento;
 var $nombre;
 var $direccion;
 var $apell1;
 var $apell2;
 var $telefono;
 var $muniCodi;
 var $dptoCodi;
 var $idPais;
 var $idCont;
 
 function ConsultasUOEF($db){
    $this->db = $db;   
 }
 function ConsutlaXemail($eMail){
    $iSql = "select * from SGD_CIU_CIUDADANO
            where SGD_CIU_EMAIL like '%$eMail%'";
    $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs = $this->db->conn->Execute($iSql);
    if($rs->fields["SGD_CIU_CODIGO"]){
        $this->codCiu = $rs->fields["SGD_CIU_CODIGO"];
        $this->noDocumento = $rs->fields["SGD_CIU_CEDULA"];
        $this->nombre = $rs->fields["SGD_CIU_NOMBRE"];
        $this->direccion = $rs->fields["SGD_CIU_DIRECCION"];
        $this->apell1 = $rs->fields["SGD_CIU_APELL1"];
        $this->apell2 = $rs->fields["SGD_CIU_APELL2"];
        $this->telefono = $rs->fields["SGD_CIU_TELEFONO"];
        $this->muniCodi = $rs->fields["MUNI_CODI"];
        $this->dptoCodi = $rs->fields["DPTO_CODI"];
        $this->idPais = $rs->fields["ID_PAIS"];
        $this->idCont = $rs->fields["ID_CONT"];
        return 1;
    }else{
        return -1;
    }
 }
 
    
}
?>