<?php

class Envio_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Se encarga de retornar el listado de radicados para enviar de una dependencia especifica
     * @param int $depecodi  Codigo de las dependencia
     */
    function getDataGrillaRadicadosEnviosJson() {
        //define("RUTA_LOG", "../messages.log");
        $page = ($_GET['page']) ? $_GET['page'] : 1;                    // get the requested page
        $limit = ($_GET['limit']) ? ($_GET['limit']) : 25;               // get how many rows we want to have into the grid
        $sidx = '';   // get index row - i.e. user click to sort
        $sord = ($_GET['dir']) ? $_GET['dir'] : 'ASC';                  // get the direction
        $depe = (empty($_REQUEST['depe'])) ? $_SESSION['dependencia'] : $_REQUEST['depe'];
        $vgr = (empty($_REQUEST['vgr']) ? false : (($_REQUEST['vgr'] == 'false') ? false : true));

        try {
            $anho= date('Y');
            if (!$vgr) {
                $sidx = 'radi_nume_salida';
                $copia = $this->db->conn->substr . '(convert(char(3),a.sgd_dir_tipo),2,2)';
                //$idr = $this->db->conn->Concat('a.radi_nume_salida', "'_'", 'a.sgd_dir_tipo');
                $idr = "d.sgd_dir_codigo";$wmezquita = ' and year(c.radi_fech_radi) in ('.($anho-1).','.$anho.')';
                //$tmpDiv = $this->db->conn->concat('n.id_cont', "'_'", 'p.id_pais', "'_'", 't.dpto_codi', "'_'", 'm.muni_codi');
                //$tmpDiv = $this->db->conn->concat('m.muni_nomb', "'_'", 't.dpto_nomb', "'_'", 'p.nombre_pais', "'_'", 'n.nombre_cont');
                $tmpDiv = "m.muni_nomb + ' ' + t.dpto_nomb + ' ' + p.nombre_pais + ' ' + n.nombre_cont";
                
                $sqlcount = "select	count(*) as NUM
		            from ANEXOS a inner join radicado c on a.radi_nume_salida = c.radi_nume_radi and c.radi_depe_radi=$depe 
		        	inner join sgd_dir_drecciones d on a.radi_nume_salida=d.radi_nume_radi and a.sgd_dir_tipo = d.sgd_dir_tipo
                    left join sgd_def_continentes n on n.id_cont=d.id_cont
		        	left join sgd_def_paises p on n.id_cont=p.id_cont and d.id_pais=p.id_pais
		        	left join departamento t on d.id_cont=t.id_cont and d.id_pais=t.id_pais and d.dpto_codi=t.dpto_codi
		        	left join municipio m on d.id_cont=m.id_cont and d.id_pais=m.id_pais and d.dpto_codi=m.dpto_codi and d.muni_codi=m.muni_codi
		        where a.anex_estado=3 and a.anex_borrado='N' and 
		        	(	( a.sgd_deve_codigo <=0 and a.sgd_deve_codigo <=99 ) OR a.sgd_deve_codigo IS NULL )
		        			and (	(c.sgd_eanu_codigo <> 2 and c.sgd_eanu_codigo <> 1) or c.sgd_eanu_codigo IS NULL) $wmezquita ";
                
                $sql = "select	$idr as idr, a.radi_nume_salida as radicado, $copia as copia, d.sgd_dir_nomremdes as destinatario, d.sgd_dir_nombre as destinatario2,
				d.sgd_dir_direccion as direccion, d.sgd_dir_codpostal as codpostal, d.sgd_dir_mail as email, 
				m.muni_nomb as municipio, t.dpto_nomb as departamento, p.nombre_pais as pais, c.RADI_PATH as ruta,
		        	d.sgd_dir_codigo as dircodigo, '' as medioenvio, '' as peso, '' as valor, '' as observacion, '' as masiva,
		        	d.sgd_dir_telefono as telefono, $tmpDiv as divipola  
		            from ANEXOS a 
		                inner join radicado c on a.radi_nume_salida = c.radi_nume_radi and c.radi_depe_radi=$depe 
		        	inner join sgd_dir_drecciones d on a.radi_nume_salida=d.radi_nume_radi and a.sgd_dir_tipo = d.sgd_dir_tipo
                    left join sgd_def_continentes n on n.id_cont=d.id_cont
		        	left join sgd_def_paises p on n.id_cont=p.id_cont and d.id_pais=p.id_pais
		        	left join departamento t on d.id_cont=t.id_cont and d.id_pais=t.id_pais and d.dpto_codi=t.dpto_codi
		        	left join municipio m on d.id_cont=m.id_cont and d.id_pais=m.id_pais and d.dpto_codi=m.dpto_codi and d.muni_codi=m.muni_codi
		        where a.anex_estado=3 and a.anex_borrado='N' and 
		        	(	( a.sgd_deve_codigo <=0 and a.sgd_deve_codigo <=99 ) OR a.sgd_deve_codigo IS NULL )
		        			and (	(c.sgd_eanu_codigo <> 2 and c.sgd_eanu_codigo <> 1) or c.sgd_eanu_codigo IS NULL) $wmezquita ";
            } else {
                $sidx = 'RADI_NUME_GRUPO, radicado';
                $wmezquita = ' and year(rd.radi_fech_radi) in ('.($anho-1).','.$anho.')';
                //$idr = "CAST(RADI_NUME_SAL as varchar) + '_00'";
                $idr = "RADI_NUME_SAL";
                
                $sqlcount = "select count(*) as NUM
                             from sgd_renv_regenvio r inner join radicado rd on rd.RADI_NUME_RADI= r.RADI_NUME_SAL
                             where r.sgd_renv_planilla = '00' and r.sgd_renv_tipo = 1 and r.sgd_depe_genera = $depe $wmezquita ";
                
                $sql = "select $idr as idr, RADI_NUME_SAL as radicado, '' as copia,
                                    SGD_RENV_NOMBRE as destinatario, SGD_RENV_DIR as direccion, SGD_RENV_CODPOSTAL as codpostal,
                                    SGD_RENV_MAIL as email, SGD_RENV_MPIO as municipio, SGD_RENV_DEPTO as departamento,
                                    SGD_RENV_PAIS as pais, RADI_PATH as ruta, SGD_RENV_CODIGO as dircodigo,
                                    r.SGD_FENV_CODIGO as medioenvio, SGD_RENV_PESO as peso, SGD_RENV_VALOR as valor, SGD_RENV_TELEFONO as telefono, 
                                    SGD_RENV_OBSERVA as masiva, '' as divipola
                                from sgd_renv_regenvio  r
                                        inner join radicado rd on rd.RADI_NUME_RADI= r.RADI_NUME_SAL
                                where r.sgd_renv_planilla = '00' and r.sgd_renv_tipo = 1 and r.sgd_depe_genera = $depe $wmezquita ";
            }
            
            //$result = $this->write_log("Antes del query $sql ", RUTA_LOG);
            $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $rsc = $this->db->conn->Execute($sqlcount);
            $count = $rsc->fields['NUM'];
            //$count = $rsc->RecordCount();

            if ($count > 0) {
                $total_pages = ceil($count / $limit);
            } else {
                $total_pages = 1;
            }

            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;

            $sql .= " order by $sidx $sord OFFSET $start Rows Fetch NEXT $limit ROWS ONLY";
            $rs = $this->db->conn->Execute($sql);
            //$result = $this->write_log("Ejecutados los querys ", RUTA_LOG);
            $i = 0;
            $tmpDestinatario = "";
            $data = array();
            foreach ($rs as $key => $row) {
                //$data[$i]['id'] = (substr(trim($row['idr']), -1) == '_') ? trim($row['idr']) . "00" : trim($row['idr']);
                $data[$i]['id'] = $row['idr'];
                $data[$i]['radicado'] = $row['radicado'];
                $data[$i]['copia'] = trim($row['copia']);
                if (trim($row['destinatario'])=="") $tmpDestinatario = $row['destinatario2']; else $tmpDestinatario = $row['destinatario'];
                if ($tmpDestinatario == "<ESPACIO>") $tmpDestinatario = "";
                $data[$i]['destinatario'] = iconv('ISO-8859-1', 'UTF-8//IGNORE//TRANSLIT', $tmpDestinatario);
                if ($row['direccion'] == "<ESPACIO>") $tmpDireccion = ""; else $tmpDireccion = $row['direccion'];
                $data[$i]['direccion'] = iconv('ISO-8859-1', 'UTF-8//IGNORE//TRANSLIT', $tmpDireccion);
                $data[$i]['codpostal'] = iconv('ISO-8859-1', 'UTF-8', trim($row['codpostal']));
                $data[$i]['correoelectronico'] = iconv('ISO-8859-1', 'UTF-8', trim($row['email']));
                $data[$i]['municipio'] = iconv('ISO-8859-1', 'UTF-8', $row['municipio']);
                $data[$i]['departamento'] = iconv('ISO-8859-1', 'UTF-8', $row['departamento']);
                $data[$i]['pais'] = iconv('ISO-8859-1', 'UTF-8', $row['pais']);
                $data[$i]['ruta'] = BODEGAURL . $row['ruta'];
                $data[$i]['dircodigo'] = $row['dircodigo'];
                $data[$i]['medioenvio'] = $row['medioenvio'];
                $data[$i]['peso'] = $row['peso'];
                $data[$i]['valor'] = $row['valor'];
                $data[$i]['observacion'] = $row['observacion'];
                $data[$i]['telefono'] = iconv('ISO-8859-1', 'UTF-8//IGNORE//TRANSLIT', trim($row['telefono']));
                $data[$i]['masiva'] = iconv('ISO-8859-1', 'UTF-8//IGNORE//TRANSLIT', trim($row['masiva']));
                $data[$i]['divipola'] = iconv('ISO-8859-1', 'UTF-8', $row['divipola']);
                $i++;
            }
            $datos['NoRegistrosPagina'] = $count;
            $datos['datosGenerales'] = $data;
            //$result = $this->write_log("Despues del for ", RUTA_LOG);
        } catch (ADODB_Exception $ex) {
            echo "Error:" . $ex->getMessage();
        }

        //$objCodificacionEspecial = new CodificacionEspecial();
        
        if (isset($_GET['callback'])) {
            return $_GET['callback'] . "(" . json_encode($datos) . ");";
        } else {
            return json_encode($datos);
        }
    }

    function setEnviosRadicados() {
        //$arrDatEnvio = json_decode($_POST['datosGenerales']);
        $clase = "Envio" .$_POST['valFE']; //$clase = "Envio" . $arrDatEnvio->medioenvio;
        if (!class_exists($clase, TRUE)) {
            $arrRes = array("success" => FALSE, "message" => "No existe logica asociada para este tipo de envio. $clase", "id" => $arrDatEnvio->id);
        } else {
            try {
                
                $objEnvio = new $clase;
                $arrRes = $objEnvio->enviarRadicado();
            } catch (Exception $e) {
                $arrRes = array("success" => false, "message" => $e->getMessage());
            }
        }
        return json_encode($arrRes);
    }

    /**
     * Retorna el listado de Formas de Envio en formato JSON.
     * @param bool|int $soloActivas  1=Activas 0=Todas
     * @return mixed
     */
    function getDataComboFormasDeEnvioJson($soloActivas) {
        try {
            //$cat = $this->db->conn->Concat("sgd_fenv_codigo","' '","sgd_fenv_descrip");
            $cat = "sgd_fenv_descrip";
            $act = ($soloActivas == 1) ? " where sgd_fenv_estado=1" : "";
            $sql = "select sgd_fenv_codigo as codigo, $cat as nombre, "
                    . "sgd_fenv_estado as estado, sgd_fenv_exigepeso as peso from sgd_fenv_frmenvio $act order by nombre";
            $rs = $this->db->conn->Execute($sql);
            $i = 0;
            foreach ($rs as $key => $row) {
                $data[$i]['id'] = $row['codigo'];
                $data[$i]['descripcion'] = $row['nombre'];
                $data[$i]['estado'] = $row['estado'];
                $data[$i]['exigepeso'] = $row['peso'];
                $i++;
            }
            $datos['datosGenerales'] = $data;
        } catch (ADODB_Exception $ex) {
            return "Error:" . $ex->getMessage();
        }
        $objCodificacionEspecial = new CodificacionEspecial();
        if (isset($_GET['callback'])) {
            return $_GET['callback'] . "(" . $objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos)) . ");";
        } else {
            return $objCodificacionEspecial->jsonRemoveUnicodeSequences(json_encode($datos));
        }
    }

    /**
     * Retorna datos de formas de envio proporcionando el id.
     * @param int $id.
     * @return mixed. False=>   array assoc =>
     */
    function getDataFormasDeEnvioById($id = NULL) {
        $sql = "SELECT * FROM SGD_FENV_FRMENVIO WHERE SGD_FENV_CODIGO = ?";
        $rs = $this->db->select($sql, array($id), true);
        if ($rs->RecordCount() > 0) {
            return $rs->fields;
        } else {
            return false;
        }
    }
    
    function write_log($message, $logfile='') {
        // Determine log file
        if($logfile == '') {
            // checking if the constant for the log file is defined
            if (defined(DEFAULT_LOG) == TRUE) {
                $logfile = DEFAULT_LOG;
            }
            // the constant is not defined and there is no log file given as input
            else {
                error_log('No log file defined!',0);
                return array(status => false, message => 'No log file defined!');
            }
        }
        
        // Get time of request
        if( ($time = $_SERVER['REQUEST_TIME']) == '') {
            $time = time();
        }
        
        // Get IP address
        if( ($remote_addr = $_SERVER['REMOTE_ADDR']) == '') {
            $remote_addr = "REMOTE_ADDR_UNKNOWN";
        }
        
        // Get requested script
        if( ($request_uri = $_SERVER['REQUEST_URI']) == '') {
            $request_uri = "REQUEST_URI_UNKNOWN";
        }
        
        // Format the date and time
        $t = explode(" ",microtime());
        $date = date("Y-m-d H:i:s",$t[1]).substr((string)$t[0],1,4);
        
        // Append to the log file
        if($fd = @fopen($logfile, "a")) {
            $result = fputcsv($fd, array($date, $remote_addr, $request_uri, $message));
            fclose($fd);
            
            if($result > 0)
                return array(status => true);
                else
                    return array(status => false, message => 'Unable to write to '.$logfile.'!');
        }
        else {
            return array(status => false, message => 'Unable to open log '.$logfile.'!');
        }
    }

}

?>
