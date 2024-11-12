<?php

class Departamento_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Obtiene los datos del departamento dados el codigo del pais y departemento.
     * @param int $idPais. Codigo del pais.
     * @param int $idDpto. Codigo del Departamento.
     * @return Array Associativo con datos del departamento.
     */
    public function getDataDepartamentoById($idPais, $idDpto) {
        $sql = "select * from departamento where id_pais=? and dpto_codi=?";
        $rs = $this->db->select($sql, array($idPais, $idDpto), true);
        if ($rs->RecordCount() > 0) {
            return $rs->fields;
        } else {
            return false;
        }
    }

}

?>
