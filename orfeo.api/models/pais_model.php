<?php

class Pais_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @param int $idPais. Codigo del pais. No se necesita el codigo del continente ya que el codigo del pais no se repite por continente.
     * @return Array Associativo con datos del pais.
     */
    public function getDataPaisById($idPais) {
        $sql = "select * from sgd_def_paises where id_pais=?";
        $rs = $this->db->select($sql, array($idPais), true);
        if ($rs->RecordCount() > 0) {
            return $rs->fields;
        } else {
            return false;
        }
    }

}

?>
