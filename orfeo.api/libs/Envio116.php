<?php

class Envio116 extends Envios {

    public function __construct() {
        parent::__construct();
    }

    public function validarDatos() {
        return true;
    }

    /**
     * Realiza el marcado como enviado del radicado y despues persistencia a BD.
     * @return int 1=Registro bd OK, 0=Registro bd ERROR.
     */
    public function enviarRadicado() {
        parent::setDatos();
        $retorno = array();
        if ($this->validarDatos()) {
            require_once 'models/Radicado_model.php';
            $objRadicado = new Radicado_Model();
            $datosRadicado = $objRadicado->datosRadicado($this->radicado);
            $ext = pathinfo($datosRadicado['RADI_PATH'], PATHINFO_EXTENSION);

            //Validamos que el radicado exista
            if ($datosRadicado && is_array($datosRadicado)) {

                //Validamos que tenga imagen Y que sea tif/tiff/pdf
                if (isset($datosRadicado['RADI_PATH']) && strlen($datosRadicado['RADI_PATH']) > 0 && (strtolower($ext) == 'pdf')) {
                    //Validamos si el radicado a enviar cumple logica de preferencia de envio
                    $cumplelogica = false;
					if (!(empty($datosRadicado['RADI_NUME_DERI']))) {
						$cumplelogica = $this->db->conn->GetOne("SELECT RADI_ANONIMO FROM RADICADO WHERE RADI_NUME_RADI=".$datosRadicado['RADI_NUME_DERI']);
					}
                    if ($cumplelogica == 1) {
                        $tmpRuta = BODEGAPATH . $datosRadicado['RADI_PATH'];
                        $strFile = file_get_contents($tmpRuta);
                        if ($strFile) {

                            $this->db->conn->StartTrans();
                            try {
                                $nextval = $this->db->conn->GetOne("Select max(SGD_RENV_CODIGO) as VLRMAX FROM SGD_RENV_REGENVIO");
                                $nextval++;
                                $pesoArchivo = filesize($tmpRuta) / (1024 * 1024);
                                list($intPeso, $decPeso) = sscanf($pesoArchivo, '%d.%03d');
                                $this->pesoEnvio = ($pesoArchivo == false) ? 0 : "$intPeso.$decPeso";

                                $valorEnvio = $this->devolverValorUnitario($pesoArchivo, NULL, json_decode($_POST['valRE']), json_decode($_POST['valEM']));

                                $sql = "update ANEXOS set ANEX_ESTADO=4, ANEX_FECH_ENVIO= " . $this->db->conn->sysTimeStamp . " where RADI_NUME_SALIDA =" . $this->radicado . " and SGD_DIR_TIPO <>7 and SGD_DIR_TIPO ";
                                //$sql .= ($this->dirTipo == NULL) ? " is null " : (($this->dirTipo != 1) ? " =7".str_pad($this->dirTipo, 2, 0, STR_PAD_LEFT) : "=1");
                                $sql .= ($this->dirTipo == NULL) ? " is null " : (($this->dirTipo != 1) ? " =" . $this->dirTipo : "=1");
                                $rsu = $this->db->conn->Execute($sql);

                                $sql = "INSERT INTO SGD_RENV_REGENVIO(  USUA_DOC, SGD_RENV_CODIGO, SGD_FENV_CODIGO,
                                    SGD_RENV_FECH, RADI_NUME_SAL, SGD_RENV_DESTINO,
                                    SGD_RENV_TELEFONO, SGD_RENV_MAIL, SGD_RENV_PESO,
                                    SGD_RENV_VALOR,	SGD_RENV_CERTIFICADO,	SGD_RENV_ESTADO,
                                    SGD_RENV_NOMBRE, SGD_DIR_CODIGO, DEPE_CODI,
                                    SGD_DIR_TIPO, RADI_NUME_GRUPO, SGD_RENV_PLANILLA,
                                    SGD_RENV_DIR, SGD_RENV_DEPTO, SGD_RENV_MPIO,
                                    SGD_RENV_PAIS, SGD_RENV_OBSERVA, SGD_RENV_CANTIDAD,
                                    SGD_RENV_NUMGUIA, SGD_RENV_CODPOSTAL) VALUES('";
                                $sql .= $this->cedula . "', $nextval, " . $this->formaEnvio . ", ";
                                $sql .= $this->db->conn->sysTimeStamp . ", " . $this->radicado . ", '" . $this->destino . "', '";
                                $sql .= $this->telefono . "', '" . $this->correoe . "', '" . $this->pesoEnvio . "', '";
                                $sql .= $valorEnvio . "', 0, 1, '";
                                $sql .= $this->nombre . "', '" . $this->codEnvio . "', " . $this->dependencia . ", ";
                                $sql .= $this->dirTipo . ", " . $this->radicado . ", '" . $this->planilla . "', '";
                                $sql .= $this->direccion . "', '" . $this->departamento . "', '" . $this->municipio . "', '";
                                $sql .= $this->pais . "', '" . $this->observacion . "', 1, '";
                                $sql .= $this->numguia . "', '" . $this->codpostal . "')";
                                $rsi = $this->db->conn->Execute($sql);
 
                                $this->db->conn->CompleteTrans();
                                $retorno = array('success' => TRUE, 'message' => "Radicado " . $this->radicado . " enviado y registrado en sistema.", "id" => $dataId);
                            } catch (Exception $exc) {
                                $this->db->conn->FailTrans();
                                $retorno = array('success' => FALSE, 'message' => $exc->getMessage());
                            }
                        } else
                            $retorno = array('success' => FALSE, 'message' => "No hay acceso a la imagen del radicado " . $this->radicado . ".");
                    } else
                        $retorno = array('success' => FALSE, 'message' => iconv('ISO-8859-1', 'UTF-8', "El radicado" . $this->radicado . " no tiene radicado padre anonimo."));
                } else
                    $retorno = array('success' => FALSE, 'message' => "El radicado " . $this->radicado . " no tiene asociado un pdf o tiff como imagen principal.");
            } else
                $retorno = array('success' => FALSE, 'message' => "El radicado " . $this->radicado . " no existe.");
        } else
            $retorno = array('success' => FALSE, 'message' => iconv('ISO-8859-1', 'UTF-8', "El correo electr&oacute;nico destino del radicado " . $this->radicado . " no es est&aacute;ndar."));
        return $retorno;
    }

    /**
     * Realiza funcionalidad interna para retornar valor unitario segun forma de envio.
     * Para el calculo tipo Correo electronico es necesario que el radicado tenga un PDF/tiff como imagen principal... reglas de negocio.
     * @param $pe int. Peso del envio.
     * @param $fe int. Forma de Envio.
     * @param $re int. Registros seleccionados.
     * @param $em int. Estado boton masiva.
     * @return int. Devuelve el valor correspondiente.
     * @throw Exception
     */
    public function devolverValorUnitario($pe, $fe, $re, $em) {
        //Validamos si el envio es tipo normal o masivo
        //Si es masiva
        if ($em) {
            $sql = "SELECT RADI_NUME_SAL FROM SGD_RENV_REGENVIO WHERE SGD_RENV_CODIGO = " . $re[0];
        } else {
            $sql = "SELECT RADI_NUME_RADI FROM SGD_DIR_DRECCIONES WHERE SGD_DIR_CODIGO = " . $re[0];
        }
        $radicado = $this->db->conn->GetOne($sql);
        if ($radicado) {
            $val = 0;
            return $val;
        } else
            throw new Exception("No existe el radicado.");
    }
}

?>