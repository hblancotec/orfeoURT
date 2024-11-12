<?php

/**
 * Clase abstracta que debe implementar TODAS las formas de Envio. 
 * Iniciaremos con Certim@il. 28-04-2014
 */
abstract class Envios extends Model {

    /**
     * Almacena el código identificador de envio (sgd_dir_codigo/)
     * @var type 
     */
    var $idE;
    var $cedula;
    var $formaEnvio;

    /**
     * Tipo de envio. 1=Original, 701=Copia 01, etc
     * @var int 
     */
    var $dirTipo;

    /**
     * Numero del radicado a enviar.
     * @var long
     */
    var $radicado;

    /**
     * 
     * Nombre del destinatario.
     * @var String(30);
     */
    var $nombre;

    /**
     * 
     * Nombre de la ciudad destino.
     * @var String(150)
     */
    var $destino;

    /**
     * Direccion fisica del envio.
     * @var string(100)
     */
    var $direccion;

    /**
     * 
     * Telefono del destinatario.
     * @var String(50).
     */
    var $telefono;

    /**
     * 
     * Correo Electronico del destinataro.
     * @var string(150).
     */
    var $correoe;

    /**
     * 
     * Peso del envio. Puede ser MB, Kilos, etc.
     * @var float
     */
    var $pesoEnvio;

    /**
     * 
     * Valor del envio.
     * @var integer
     */
    var $valorEnvio;

    /**
     * Valor de SGD_RENV_REGENVIO.SGD_DIR_CODIGO.
     * @var integer 
     */
    var $codEnvio;

    /**
     * Codigo de dependencia del usuario que genera la transaccion.
     * @var integer
     */
    var $dependencia;

    /**
     * Codigo de la planilla donde se enviara el radicado segun forma de envio.
     * @var string(8) 
     */
    var $planilla;

    /**
     * Nombre del pais destino del envio.
     * @var string(30)
     */
    var $pais;

    /**
     * Nombre del departamento destino del envio.
     * @var string(30)
     */
    var $departamento;

    /**
     * Nombre del municipio destino del envio.
     * @var string(30)
     */
    var $municipio;

    /**
     * Observacion realizada al hacer el envio.
     * @var string(200)
     */
    var $observacion;

    /**
     * Numero de guia del envio.
     * @var string(15)
     */
    var $numguia;

    /**
     * Codigo postal del destinatario.
     * @var string(8)
     */
    var $codpostal;
    
    /**
     * Debe almacenar la preferencia de envio del radicado padre.
     * @var Char. V/F. 
     */
    var $preferenciaEnvio;

    /**
     * 
     */
    public function __construct() {
        parent::__construct();
    }

    public function validarDatos() {
        $result = NULL;
        if (empty($this->municipio) || empty($this->departamento) || empty($this->pais) ||
                empty($this->direccion) || empty($this->nombre)) {
            $result = FALSE;
        } else {
            $result = TRUE;
        }
        return $result;
    }

    /**
     * Funcion que inicializa todos los valores para iniciar gestion de envio
     * @param int $dirCodigo. 
     */
    public function setDatos() {
        $this->cedula = $_SESSION['usua_doc'];
        $this->dependencia = $_SESSION['dependencia'];
        $this->nombre = str_replace("'", "", substr($this->valDataFromView($_POST['valDS']), 0, 30));
        $this->pesoEnvio = $this->valDataFromView($_POST['valPE']);
        $this->codEnvio = $this->valDataFromView($_POST['valDC']);
        $this->direccion = str_replace("'", "", substr($this->valDataFromView($_POST['valDD']), 0, 100));
        $this->formaEnvio = $this->valDataFromView($_POST['valFE']);
        $this->correoe = $this->valDataFromView($_POST['valDE']);
        $this->telefono = $this->valDataFromView($_POST['valDT']);
        $this->observacion = $this->valDataFromView($_POST['valDO']);
        $this->numguia = $this->valDataFromView($_POST['valDG']);
        $this->codpostal = $this->valDataFromView($_POST['valDP']);
        if ($this->valDataFromView($_POST['valEM'])) {
            $tmpRadi = $this->valDataFromView($_POST['valRD']);

            $sql = "SELECT  RADI_NUME_SAL as RADI_NUME_RADI, SGD_RENV_NOMBRE as SGD_DIR_NOMREMDES,
                            SGD_RENV_DEPTO as DPTO_NOMB, SGD_RENV_PAIS as NOMBRE_PAIS,
                            SGD_RENV_MPIO as MUNI_NOMB, SGD_DIR_TIPO
                    FROM SGD_RENV_REGENVIO WHERE SGD_RENV_CODIGO = " . $this->codEnvio . " and RADI_NUME_SAL=" . $tmpRadi;
            $rs = $this->db->conn->Execute($sql);
            $arr = $rs->FetchRow();
            $tmpPais = $arr['NOMBRE_PAIS'];
            $tmpDpto = $arr['DPTO_NOMB'];
            $tmpMpio = $arr['MUNI_NOMB'];
            $tmpPlanilla = $this->valDataFromView($_POST['valDG']);
            $tmpGuia = '';
            $this->idE = $tmpRadi;
        } else {

            $sql = "SELECT * FROM SGD_DIR_DRECCIONES WHERE SGD_DIR_CODIGO = " . $this->codEnvio;
            $rs = $this->db->conn->Execute($sql);
            $arr = $rs->FetchRow();

            require 'models/pais_model.php';
            $objPais = new Pais_Model();
            $datPais = $objPais->getDataPaisById($arr['ID_PAIS']);

            require 'models/departamento_model.php';
            $objDpto = new Departamento_Model();
            $datDpto = $objDpto->getDataDepartamentoById($arr['ID_PAIS'], $arr['DPTO_CODI']);

            require 'models/municipio_model.php';
            $objMcpio = new Municipio_Model();
            $datMcpio = $objMcpio->getDataMunicipioById($arr['ID_PAIS'], $arr['DPTO_CODI'], $arr['MUNI_CODI']);

            $tmpRadi = $arr['RADI_NUME_RADI'];
            $tmpPais = $datPais['NOMBRE_PAIS'];
            $tmpDpto = $datDpto['DPTO_NOMB'];
            $tmpMpio = $datMcpio['MUNI_NOMB'];
            $tmpPlanilla = '';
            $tmpGuia = $this->valDataFromView($_POST['valDG']);

            $this->idE = $arr['SGD_DIR_CODIGO'];
        }

        $this->radicado = $tmpRadi;
        $this->departamento = $tmpDpto;
        $this->municipio = $tmpMpio;
        $this->pais = $tmpPais;
        $this->destino = $tmpMpio;
        $this->planilla = (empty($tmpPlanilla) ? '' : $tmpPlanilla);
        $this->dirTipo = $arr['SGD_DIR_TIPO'];
    }

    /**
     * Realiza el envio del radicado, en este caso es solo persistencia a BD.
     * @return array. Retorna vector asociativo .. array('success'=>BOLEANO, 'message'=>'XXXXXXX')
     */
    public function enviarRadicado() {
        $retorno = array();
        $this->setDatos();
        if ($this->validarDatos()) {
            //Validamos que el radicado exista
            require 'models/Radicado_model.php';
            $objRadicado = new Radicado_Model();
            $datosRadicado = $objRadicado->datosRadicado($this->radicado);
            if ($datosRadicado && is_array($datosRadicado)) {
                //Validamos que tenga imagen Y que sea tif/tiff/pdf
                $ext = pathinfo($datosRadicado['RADI_PATH'], PATHINFO_EXTENSION);
                if (isset($datosRadicado['RADI_PATH']) && strlen($datosRadicado['RADI_PATH']) > 0 && (strtolower($ext) == 'pdf' || strtolower(substr($ext, 0, 3)) == 'tif')) {
                    //Validamos si el radicado a enviar cumple logica de preferencia de envio
                    $cumplelogica = $this->validarPreferenciaEnvio();
                    if ($cumplelogica) {
                        try {
                            $valorEnvio = $this->devolverValorUnitario(json_decode($_POST['valPE']), json_decode($_POST['valFE']), json_decode($_POST['valRE']), $_POST['valEM']);
                            $this->db->conn->StartTrans();
                            //Validamos si el envio es tipo normal o masivo
                            //Si es masiva
                            if ($_POST['valEM']) {

                                $sql = "SELECT MIN( RADI_NUME_SAL ) FROM SGD_RENV_REGENVIO WHERE SGD_RENV_CODIGO = " . $this->codEnvio . " GROUP BY SGD_RENV_CODIGO";
                                $radMin = $this->db->conn->GetOne($sql);

                                $sql = "UPDATE SGD_RENV_REGENVIO SET SGD_RENV_PLANILLA='" . $this->planilla . "',
                                        SGD_RENV_TIPO = 2, DEPE_CODI=" . $this->dependencia . ",
                                        SGD_FENV_CODIGO='" . $this->formaEnvio . "'
                                    WHERE SGD_RENV_PLANILLA = '00' AND SGD_RENV_TIPO = 1 AND RADI_NUME_GRUPO = $radMin AND RADI_NUME_SAL NOT IN 
                                        (select sgd_rmr_radi  from sgd_rmr_radmasivre  where sgd_rmr_grupo=$radMin)";
                                $rsi = $this->db->conn->Execute($sql);

                                $nextval = $this->db->conn->GetOne("Select max(SGD_RENV_CODIGO) as VLRMAX FROM SGD_RENV_REGENVIO");
                                $nextval++;

                                $sql = "update RADICADO set SGD_EANU_CODIGO=9 where RADI_NUME_RADI = " . $this->radicado;
                                $rse = $this->db->conn->Execute($sql);

                                $sql = "INSERT INTO SGD_RENV_REGENVIO(  USUA_DOC, SGD_RENV_CODIGO, SGD_FENV_CODIGO,
                                    SGD_RENV_FECH, RADI_NUME_SAL, SGD_RENV_DESTINO,
                                    SGD_RENV_TELEFONO, SGD_RENV_MAIL, SGD_RENV_PESO,
                                    SGD_RENV_VALOR, SGD_RENV_CERTIFICADO, SGD_RENV_ESTADO,
                                    SGD_RENV_NOMBRE, SGD_DIR_CODIGO, DEPE_CODI,
                                    SGD_DIR_TIPO, RADI_NUME_GRUPO, SGD_RENV_PLANILLA,
                                    SGD_RENV_DIR, SGD_RENV_DEPTO, SGD_RENV_MPIO,
                                    SGD_RENV_PAIS, SGD_RENV_OBSERVA, SGD_RENV_CANTIDAD,
                                    SGD_RENV_NUMGUIA, SGD_RENV_CODPOSTAL) VALUES('";
                                $sql .= $this->cedula . "', $nextval, " . $this->formaEnvio . ", ";
                                $sql .= $this->db->conn->sysTimeStamp . ", " . $this->radicado . ", '" . $this->destino . "', '";
                                $sql .= $this->telefono . "', '" . $this->correoe . "', '" . $this->pesoEnvio . "', '";
                                $sql .= $valorEnvio . "', 0, 1, '";
                                $sql .= $this->nombre . "', '" . $this->codEnvio . "', " . $this->dependencia . ", '";
                                $sql .= "1', " . $this->radicado . ", '" . $this->planilla . "', '";
                                $sql .= $this->direccion . "', '" . $this->departamento . "', '" . $this->municipio . "', '";
                                $sql .= $this->pais . "', '" . $this->observacion . "', 1, '";
                                $sql .= $this->numguia . "', '" . $this->codpostal . "')";
                                $rsi = $this->db->conn->Execute($sql);
                                //$dataId = $this->radicado. "_". ($this->dirTipo=='' ? "00" : $this->dirTipo); 
                                $dataId = $this->idE;
                            } else {
                                $nextval = $this->db->conn->GetOne("Select max(SGD_RENV_CODIGO) as VLRMAX FROM SGD_RENV_REGENVIO");
                                $nextval++;

                                $sql = "update ANEXOS set ANEX_ESTADO=4, ANEX_FECH_ENVIO= " . $this->db->conn->sysTimeStamp . " where RADI_NUME_SALIDA =" . $this->radicado . " and SGD_DIR_TIPO <>7 and SGD_DIR_TIPO ";
                                //$sql .= ($this->dirTipo == NULL) ? " is null " : (($this->dirTipo != 1) ? " =7".str_pad($this->dirTipo, 2, 0, STR_PAD_LEFT) : "=1");
                                $sql .= ($this->dirTipo == NULL) ? " is null " : (($this->dirTipo != 1) ? " =" . $this->dirTipo : "=1");
                                $rsu = $this->db->conn->Execute($sql);

                                $sql = "update RADICADO set SGD_EANU_CODIGO=9 where RADI_NUME_RADI = " . $this->radicado;
                                $rse = $this->db->conn->Execute($sql);

                                $sql = "INSERT INTO SGD_RENV_REGENVIO(  USUA_DOC, SGD_RENV_CODIGO, SGD_FENV_CODIGO,
                                    SGD_RENV_FECH, RADI_NUME_SAL, SGD_RENV_DESTINO,
                                    SGD_RENV_TELEFONO, SGD_RENV_MAIL, SGD_RENV_PESO,
                                    SGD_RENV_VALOR, SGD_RENV_CERTIFICADO, SGD_RENV_ESTADO,
                                    SGD_RENV_NOMBRE, SGD_DIR_CODIGO, DEPE_CODI,
                                    SGD_DIR_TIPO, RADI_NUME_GRUPO, SGD_RENV_PLANILLA,
                                    SGD_RENV_DIR, SGD_RENV_DEPTO, SGD_RENV_MPIO,
                                    SGD_RENV_PAIS, SGD_RENV_OBSERVA, SGD_RENV_CANTIDAD,
                                    SGD_RENV_NUMGUIA, SGD_RENV_CODPOSTAL) VALUES('";
                                $sql .= $this->cedula . "', $nextval, " . $this->formaEnvio . ", ";
                                $sql .= $this->db->conn->sysTimeStamp . ", " . $this->radicado . ", '" . $this->destino . "', '";
                                $sql .= $this->telefono . "', '" . $this->correoe . "', '" . $this->pesoEnvio . "', '";
                                $sql .= $valorEnvio . "', 0, 1, '";
                                $sql .= $this->nombre . "', '" . $this->codEnvio . "', " . $this->dependencia . ", '";
                                $sql .= $this->dirTipo . "', " . $this->radicado . ", '" . $this->planilla . "', '";
                                $sql .= $this->direccion . "', '" . $this->departamento . "', '" . $this->municipio . "', '";
                                $sql .= $this->pais . "', '" . $this->observacion . "', 1, '";
                                $sql .= $this->numguia . "', '" . $this->codpostal . "')";
                                $rsi = $this->db->conn->Execute($sql);
                                //$dataId = $this->radicado. "_". ($this->dirTipo=='' ? "00" : $this->dirTipo); 
                                $dataId = $this->idE;
                            }
                            if ($this->db->conn->CompleteTrans())
                                $retorno = array('success' => TRUE, 'message' => "Radicado " . $this->radicado . " enviado y registrado en sistema.", "id" => $dataId);
                            else
                                $retorno = array('success' => TRUE, 'message' => "Radicado " . $this->radicado . " enviado pero hubo fallas al registrar en sistema.", "id" => $dataId);
                        } catch (Exception $exc) {
                            $this->db->conn->FailTrans();
                            $this->db->conn->CompleteTrans();
                            $retorno = array('success' => FALSE, 'message' => $exc->getMessage());
                        }
                    } else
                        $retorno = array('success' => FALSE, 'message' => iconv('ISO-8859-1', 'UTF-8', "El radicado" . $this->radicado . " tiene preferencia de envio " . ($this->preferenciaEnvio=='V' ? 'Virtual' : 'F&iacute;sico') ) );
                } else
                    $retorno = array('success' => FALSE, 'message' => "El radicado " . $this->radicado . " no tiene asociado un pdf o tiff como imagen principal.");
            } else
                $retorno = array('success' => FALSE, 'message' => "El radicado " . $this->radicado . " no existe.");
        } else
            $retorno = array('success' => FALSE, 'message' => iconv('ISO-8859-1', 'UTF-8', "El radicado " . $this->radicado . " requiere Destinatario, Direcci&oacute;n, C&oacute;d. postal e Internacionalizaci&oacute;n."));

        return $retorno;
    }

    public function devolverValorUnitario($pe, $fe, $re, $em) {
        $retorno = -1;
        try {
            $destino472 = $this->retornaDestino472($pe, $fe, $re, $em);
            $codSer = ($destino472 > 0 && $destino472 < 5) ? 1 : 2;
            $sql = "SELECT SGD_TAR_CODIGO, SGD_CLTA_DESCRIP FROM SGD_CLTA_CLSTARIF "
                    . "WHERE SGD_CLTA_CODSER=$codSer AND SGD_FENV_CODIGO=$fe AND "
                    . " $pe BETWEEN SGD_CLTA_PESDES AND SGD_CLTA_PESHAST";
            $rs = $this->db->conn->Execute($sql);
            $count = $rs->RecordCount();
            if ($count == 0) {
                throw new Exception("No existe tabulacion para forma de envio, peso y destinatario(s) dado.");
            } else {
                switch ($destino472) {
                    case '1':
                    case '5': {
                            $varTar = 'SGD_TAR_VALENV1';
                        } break;
                    case '2':
                    case '6': {
                            $varTar = 'SGD_TAR_VALENV2';
                        }break;
                    case '3':
                    case '7': {
                            $varTar = 'SGD_TAR_VALENV1G1';
                        }break;
                    case '4': $varTar = 'SGD_TAR_VALENV2G2';
                        break;
                }
                $rs->Close();
                $sql = "SELECT $varTar as VALOR FROM SGD_TAR_TARIFAS "
                        . "WHERE SGD_CLTA_CODSER=$codSer AND SGD_FENV_CODIGO=$fe AND SGD_TAR_CODIGO=" . $rs->fields('SGD_TAR_CODIGO');
                $rs = $this->db->conn->Execute($sql);
                if ($count == 0) {
                    throw new Exception("No existe tarifa para forma de envio, peso y destinatario(s) dado.");
                } else {
                    $retorno = $rs->fields('VALOR');
                }
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
        return $retorno;
    }

    private function valDataFromView($data) {
        return (trim(json_decode($data)) == '') ? '' : iconv('UTF-8', 'ISO-8859-1', trim(json_decode($data)));
    }

    /**
     * Valida si todos los registros a enviar van para el mismo tipo de destino.
     * @param decimal $pe  Peso del envio para todos los radicados.
     * @param integer $fe.odigo de la forma de envio.
     * @param array key int $re. Son los sgd_dir_codigo en sgd_dir_drecciones
     * @return int. Tipo de destino 472. Urbano/Regional/Nacional/Tray.Especiales/IntZ1/IntZ2/IntZ3
     * @throws Exception
     */
    public function retornaDestino472($pe = 0, $fe = 0, $re = array(), $em) {
        if (($this->getExigePeso($fe) && empty($pe)) || empty($fe) || empty($re)) {
            throw new Exception("Obtener un tarifa requiere peso, forma de envio y destinatario.");
        } else {
            try {
                if ($em) {
                    if (isset($this->codEnvio) and isset($this->radicado)) {
                        $sql = "SELECT m472.ID_CONT, m472.ID_PAIS, m472.DPTO_CODI, m472.MUNI_CODI, m472.DEST472 FROM SGD_MUNICIPIO_472 m472 
                            INNER JOIN
                                    (SELECT p.ID_CONT, p.ID_PAIS, d.DPTO_CODI, m.MUNI_CODI FROM SGD_RENV_REGENVIO AS e 
                                    INNER JOIN SGD_DEF_PAISES p ON p.NOMBRE_PAIS collate SQL_Latin1_General_CP1_CI_AI =e.SGD_RENV_PAIS
                                    INNER JOIN DEPARTAMENTO d ON d.DPTO_NOMB collate SQL_Latin1_General_CP1_CI_AI =e.SGD_RENV_DEPTO
                                    INNER JOIN MUNICIPIO m ON m.MUNI_NOMB collate SQL_Latin1_General_CP1_CI_AI =e.SGD_RENV_MPIO
                                    WHERE e.SGD_RENV_CODIGO = " . $this->codEnvio . " and e.RADI_NUME_SAL = " . $this->radicado . ") as envio
                             ON m472.ID_CONT=envio.ID_CONT AND m472.ID_PAIS=envio.ID_PAIS AND m472.DPTO_CODI=envio.DPTO_CODI AND m472.MUNI_CODI=envio.MUNI_CODI";
                    } else {
                        throw new Exception("En vista masiva no puede previsualizarse la tarifa de envio.");
                    }
                } else {
                    $sql = "select m.ID_CONT, m.ID_PAIS, m.DPTO_CODI, m.MUNI_CODI, m.DEST472
                        from sgd_municipio_472 m
                        inner join sgd_dir_drecciones d on d.ID_CONT=m.ID_CONT and d.ID_PAIS=m.ID_PAIS and d.DPTO_CODI=m.DPTO_CODI and d.MUNI_CODI=m.MUNI_CODI and d.SGD_DIR_CODIGO in (" . implode(',', $re) . ")
                        group by m.ID_CONT, m.ID_PAIS, m.DPTO_CODI, m.MUNI_CODI, m.DEST472 ";
                }
                $rs = $this->db->conn->Execute($sql);
                $count = $rs->RecordCount();
                //Validación comentariada. Dado que AHORA no importa que todos tengan un mismo tipo destino 472.
                //if ($count > 1 ) throw new Exception("Los destinatarios deben tener mismo tipo de destino. $count");
                switch ($count) {
                    case 0 : throw new Exception("No pudo obtenerse registro de env&iacute;o.");
                        break;
                    default : $count = $rs->fields('DEST472');
                        break;
                }
            } catch (Exception $exc) {
                throw new Exception($exc->getMessage());
            }
        }
        return $count;
    }

    protected function validarPreferenciaEnvio() {
        $resultado = FALSE;
        $sql = "select ANEX_RADI_NUME as RAD_PAPA from anexos where RADI_NUME_SALIDA= " . $this->radicado . " and anex_salida=1 
                union 
                select RADI_NUME_DERI as RAD_PAPA from radicado where RADI_NUME_RADI= " . $this->radicado;
        $radpapa = $this->db->conn->GetOne($sql);
        if ($radpapa > 0) {
            //Tiene papá. Entonces, validamos si ese papa tiene preferencia de envio pqr por el ciudadano
            $sql = "select SGD_FENV_MODALIDAD from SGD_PQR_METADATA where RADI_NUME_RADI=$radpapa";
            $this->preferenciaEnvio = $this->db->conn->GetOne($sql);
            if ($this->preferenciaEnvio == null) {
                $resultado = TRUE;
            } else if ($this->preferenciaEnvio == "A"){
                $resultado = TRUE;
            } else if ($this->preferenciaEnvio == $this->getModalidadEnvio())
                $resultado = TRUE;
        } else {
            $resultado = TRUE;
        }
        return $resultado;
    }

    /**
     * Obtiene la modalidad de envío según su código de forma de envío.
     * @return char V/F. V=Virtual  F=Fisico.
     */
    protected function getModalidadEnvio() {
        $sql = "Select SGD_FENV_MODALIDAD from SGD_FENV_FRMENVIO where SGD_FENV_CODIGO=" . $this->formaEnvio;
        $algo = $this->db->conn->GetOne($sql);
        return $algo;
    }

    /**
     * 
     * @return type
     */
    protected function getExigePeso() {
        $fenvio = 0;
        if (func_num_args() == 0) {
            $fenvio = $this->formaEnvio;
        } else {
            $args = func_get_args();
            $arg1 = $args[0];
            $fenvio = $arg1;
        }
        $sql = "Select SGD_FENV_EXIGEPESO from SGD_FENV_FRMENVIO where SGD_FENV_CODIGO=" . $fenvio;
        $algo = $this->db->conn->GetOne($sql);
        return $algo;
    }
}

?>