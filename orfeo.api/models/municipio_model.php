<?php

class Municipio_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Obtiene los datos del municipio dado el codigo del pais, departemento y municipio.
     * @param int $idPais. Codigo del pais.
     * @param int $idDpto. Codigo del Departamento.
     * @param int $idMpio. Codigo del Municipio.
     * @return Array Associativo con datos del Municipio.
     */
    public function getDataMunicipioById($idPais, $idDpto, $idMpio) {
        $sql = "select * from municipio where id_pais=? and dpto_codi=? and muni_codi=?";
        $rs = $this->db->select($sql, array($idPais, $idDpto, $idMpio), true);
        if ($rs->RecordCount() > 0) {
            return $rs->fields;
        } else {
            return false;
        }
    }
	
	public function getDataMunicipioPorCadena(){
		try{
            $sql = "select	CONCAT(M.ID_CONT,'_', M.ID_PAIS,'_', M.DPTO_CODI,'_', M.MUNI_CODI) as codigo, 
							CONCAT(C.NOMBRE_CONT, ' - ', P.NOMBRE_PAIS, ' - ', DPTO_NOMB, ' - ' ,MUNI_NOMB) as lugar 
				from MUNICIPIO M
					inner join DEPARTAMENTO D ON D.ID_CONT=M.ID_CONT AND D.ID_PAIS=M.ID_PAIS AND D.DPTO_CODI=M.DPTO_CODI
					inner join SGD_DEF_PAISES P ON P.ID_CONT=M.ID_CONT AND P.ID_PAIS=M.ID_PAIS
					inner join SGD_DEF_CONTINENTES C ON C.ID_CONT=M.ID_CONT
				order by M.ID_CONT, M.ID_PAIS, M.DPTO_CODI, M.MUNI_CODI";
            $rs = $this->db->conn->Execute($sql);
            $i=0;
            foreach ($rs as $key => $row) {
                $data[$i]['id'] = $row['codigo'];
                $data[$i]['descripcion'] = iconv('ISO-8859-1', 'UTF-8//IGNORE', $row['lugar']);
                $i++;
            }
            $datos['datosGenerales']=$data;
        }
        catch (ADODB_Exception $ex)
        {
            return "Error:". $ex->getMessage();
        }
        $objCodificacionEspecial = new CodificacionEspecial();
        if(isset($_GET['callback']))
        {
            //header('Content-Type: application/javascript');
            return $_GET['callback']."(".$objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos)).");";
        }
        else
        {
            return $objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos));
        }
	
	}

}

?>
