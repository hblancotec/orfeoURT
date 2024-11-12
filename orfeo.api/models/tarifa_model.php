<?php

class tarifa_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * R
     * @param decimal $pe Peso del envio
     * @param int $pe Peso
     * @param int $fe Codigo de la Forma de Envio
     * @param int[] $re Registros seleccionados
     * @param int $em Estado Botón masiva.
     * @return array('success'=> BOOLEAN, 'mensaje'=>'XXXXXXXX')
     */
    private function getTarifa($pe, $fe, $re, $em) {
        try {
            $clase = "Envio" . $fe;
            if (!class_exists($clase, TRUE)) {
                $arrRes = array("success" => FALSE, "message" => "No existe logica asociada para este tipo de envio. $clase", "id" => $arrDatEnvio->id);
            } else {
                try {
                    //Cada clase del tipo de envio valida sus datos.
                    $objTipoEnvio = new $clase;
                    $tmpValUnit = $objTipoEnvio->devolverValorUnitario($pe, $fe, $re, $em);
                    require_once 'envio_model.php';
                    $objEnvioModel = new Envio_Model();
                    $dataFe = $objEnvioModel->getDataFormasDeEnvioById($fe);
                    if ($dataFe['SGD_FENV_EXIGEPESO'] == 1) {
                        $ct = $objTipoEnvio->retornaDestino472($pe, $fe, $re, $em);
                        $ct = ($ct >0 && $ct<5) ? 1 : 2;
                        $sql = "SELECT SGD_TAR_CODIGO, SGD_CLTA_DESCRIP FROM SGD_CLTA_CLSTARIF "
                                . "WHERE SGD_CLTA_CODSER=$ct AND SGD_FENV_CODIGO=$fe AND "
                                . " $pe BETWEEN SGD_CLTA_PESDES AND SGD_CLTA_PESHAST";
                        $rs = $this->db->conn->Execute($sql);
                        $desTar = $rs->fields('SGD_CLTA_DESCRIP');

                        $arrRes['success'] = TRUE;
                        $arrRes['message'] = "Rango: $desTar. Valor: $tmpValUnit";
                    } else {
                        $arrRes['success'] = TRUE;
                        $arrRes['message'] = "Valor: $tmpValUnit";
                    }
                } catch (Exception $e) {
                    $arrRes = array("success" => false, "message" => $e->getMessage());
                }
            }
        } catch (Exception $exc) {
            $arrRes['success'] = FALSE;
            $arrRes['message'] = $exc->getMessage();
        }
        return $arrRes;
    }

    /**
     * Se encarga de retornar el valor de un envio en formato JSON para en front-end.
     * @POST
     */
    function getTarifaJson() {
        $pe = json_decode($_POST['valPE']);
        $fe = json_decode($_POST['valFE']);
        $re = json_decode($_POST['valRE']);
        $em = json_decode($_POST['valEM']);
        $datos = $this->getTarifa($pe, $fe, $re, $em);   //guarda los datos a devolver via JSON
        $objCodificacionEspecial = new CodificacionEspecial();
        if (isset($_GET['callback'])) {
            return $_GET['callback'] . "(" . $objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos)) . ");";
        } else {
            return $objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos));
        }
    }

}

?>