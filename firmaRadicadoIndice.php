<?php

/**
 * Esp es la clase encargada de gestionar las operaciones y los datos basicos referentes a la firma digital de un radicado
 * @author	Sixto Angel Pinzon
 * @version	1.0
 */

class firmaRadicadoIndice {
    
    /**
     * Gestor de las transacciones con la base de datos
     * @var ConnectionHandler
     * @access public
     */
    var $cursor;
    
    
    /**
     * Constructor encargado de obtener la conexion
     * @param	$db	ConnectionHandler es el objeto conexion
     * @return   void
     */
    function firmaRadicadoIndice($db) {
        $this->cursor = $db;
    }
    
    
    /**
     * Consulta si se ha solicitado firma digital de un usuario para un radicado especifico
     * @param	$radicado	string	Radicado a consultar
     * @param	$usuaDocto	string	Documento del usuario
     * @return   boolean
     */
    function existeFirma($radicado,$usuaDocto) {
        $retorno = false;
        $sql="select * from SGD_FIRRAD_FIRMARADS where USUA_DOC='$usuaDocto' and RADI_NUME_RADI=$radicado";
        $rs=$this->cursor->query($sql);
        
        if  ($rs && !$rs->EOF){
            $retorno=true;
        }
        return $retorno;
    }
    
    
    /**
     * Consulta si se ha solicitado firma digital para un radicado, si ya se ha firmado completamente retorna COMPLETA, si no retorna INCOMPLETA, si no se ha solicitado retorna NO_SOLICITADA.
     * @param	$radicado	string	Radicado a consultar
     * @return   string
     */
    function firmaCompleta($radicado) {
        $sql="select top 1 estado from SGD_CICLOFIRMADOMASTER where RADI_NUME_RADI=$radicado order by sgd_ciclo_fechasol desc";
        $est = $this->cursor->conn->GetOne($sql);
        switch ($est) {
            case 1: {
                $retorno = "INCOMPLETA";
            } break;
            case 2: {
                $retorno = "MODIFICACION";
            } break;
            case 3:{
                $retorno = "COMPLETA";
            } break;
            case 4:{
                $retorno = "RECHAZADA";
            } break;
            default: {
                $retorno = "NO_SOLICITADA";
            }break;
        }
        return $retorno;
    }
    
    
    /**
     * Consulta una solicitud de firma de acuerdo a un id de solicitud
     * @param	$id	string	Id de firma
     * @return   Array con toda la informacion de la firma
     */
    function firmaId($id) {
        $retorno = true;
        $sql="select * from SGD_FIRRAD_FIRMARADS where SGD_FIRRAD_ID = $id";
        $rs=$this->cursor->query($sql);
        $retorno = array();
        if  ($rs && !$rs->EOF){
            $retorno['RADI_NUME_RADI'] = $rs->fields['RADI_NUME_RADI'];
            $retorno['USUA_DOC'] = $rs->fields['USUA_DOC'];
            $retorno['SGD_FIRRAD_FIRMA'] = $rs->fields['SGD_FIRRAD_FIRMA'];
            $retorno['SGD_FIRRAD_FECHA'] = $rs->fields['SGD_FIRRAD_FECHA'];
            $retorno['SGD_FIRRAD_DOCSOLIC'] = $rs->fields['SGD_FIRRAD_DOCSOLIC'];
            $retorno['SGD_FIRRAD_FECHSOLIC'] = $rs->fields['SGD_FIRRAD_FECHSOLIC'];
        }
        return $retorno;
    }
    
    
    /**
     * Consulta y devuelve los nombres de los usuarios que han firmado un documento
     * @param	$radicado	string	Radicado a consultar
     */
    function nombresFirmsRad($radicado) {
        
        $sql="select f.SGD_FIRRAD_FIRMA, u.USUA_NOMB from SGD_FIRRAD_FIRMARADS f, USUARIO u  where f.RADI_NUME_RADI=$radicado
			 AND f.USUA_DOC = u.USUA_DOC";
        $rs=$this->cursor->query($sql);
        $retorno="";
        while  ($rs && !$rs->EOF){
            if (strlen(trim($rs->fields['SGD_FIRRAD_FIRMA']))>=0){
                $retorno = $retorno . strtoupper(trim($rs->fields['USUA_NOMB'])) . "<BR>";
            }
            $rs->MoveNext();
        }
        
        return $retorno;
    }
    
    /**
     * Anula las firmas previamente realizadas sobre un radicado
     * @param	$radicado	string	Radicado cuya firma ha de anularse
     */
    function anularFirmaRad($radicado) {
        
        $sql="update SGD_FIRRAD_FIRMARADS set SGD_FIRRAD_FIRMA=null,SGD_FIRRAD_FECHA=null where RADI_NUME_RADI=$radicado ";
        $rs=$this->cursor->query($sql);
        
        if  (!$rs){
            echo ("<BR>No se pudo actualizar la tabla de firmas ($sql) <BR>");
        }
    }
    
    /**
     * Retorna la información del ciclo de firmado del radicado envido. Si no tiene ciclo retorna FALSE.
     * @param integer $radicado
     */
    function obtenerCicloPorRadicado($radicado){
        $sql = "select top 1 m.estado, m.idcf, m.rutapdf as ruta, r.ANEXOS_EXP_PATH
                from sgd_ciclofirmadomaster m 
                    inner join SGD_ANEXOS_EXP r on r.ANEXOS_EXP_ID=m.anex_indice
                where m.anex_indice='$radicado' order by m.sgd_ciclo_fechasol desc";
        $rsx = $this->cursor->conn->Execute($sql);
        if ($rsx && !$rsx->EOF) {
            $ciclo['estado'] = $rsx->fields['estado'];
            $ciclo['idcf'] = $rsx->fields['idcf'];
            $ciclo['rutaciclo'] = $rsx->fields['ruta'];
            $ciclo['rutaradi'] = $rsx->fields['radi_path'];
        } else return false;
        $rsx->Close();
        return $ciclo;
    }
    
    /**
     * Retorna la información del ciclo de firmado según su id envido. Si no tiene ciclo retorna FALSE.
     * @param integer $idcf
     */
    function obtenerCicloPorIdcf($idcf){
        $sql = "select idcf, radi_nume_radi as radicado, sgd_ciclo_fechasol as fecha_solicitud, rutapdf as ruta, estado, ".
            "usua_login as login_solicitante, usua_doc as documento_solicitante, usua_nomb as nombre_solicitante ".
            "from sgd_ciclofirmadomaster m left join usuario u on u.usua_login=m.usua_login and u.usua_doc=m.usua_doc ".
            "where idcf=$idcf";
        $ADODB_COUNTRECS = true;
        $rsx = $this->cursor->conn->Execute($sql);
        $ADODB_COUNTRECS = false;
        if ($rsx->RecordCount() == 0) return false;
        $ciclo['estado'] = $rsx->fields['estado'];
        $ciclo['idcf'] = $rsx->fields['idcf'];
        $rsx->Close();
        return $ciclo;
    }
    
    /**
     *
     * @param integer $radicado
     * @return boolean
     */
    function crearCiclo($radicado,$ruta) {
        $filePdfOk = false;
        try {
            $info = pathinfo($ruta);
            $filePdf = $info['filename'].'.pdf';
            switch ($info['extension']) {
                case 'doc':
                case 'docx': {
                   $ciclo = false;
                } break;
                case  'pdf':{
                    if (file_exists(BODEGAPATH.$ruta)) {
                        $carpeta = BODEGAPATH.'dav/'.date('Y');
                        if (!file_exists($carpeta)) {
                            mkdir($carpeta, 0777, true);
                        }                        
                        if (copy(BODEGAPATH.$ruta, BODEGAPATH.'dav/'.date('Y').'/'.$info['filename'].'.'.$info['extension'])){
                            $sql = "insert into sgd_ciclofirmadomaster (usua_login,usua_doc, estado, rutapdf, anex_indice) values ( '".$_SESSION['login']."', '".$_SESSION['usua_doc']."', 1, 'dav/".date('Y').'/'.$info['filename'].'.'.$info['extension']."', '".$radicado."')";
                            $rs = $this->cursor->conn->Execute($sql);
                            $ciclo = $this->cursor->conn->Insert_ID();
                            $rs->Close();
                        } else $ciclo = false;
                    } else $ciclo = false;
                } break;
                default: {
                    $ciclo = false;
                } break;
            }
        } catch (Exception $e) {
            $ciclo = false;
        }
        return $ciclo;
    }
    
    /**
     *
     * @param integer $ciclo
     * @param string $cedula
     * @param string $login
     * @return boolean
     */
    function agregarSolicitud($ciclo, $cedula, $login) {
        $return = false;
        try {
            //El indice unico de ciclofirmante en sgd_ciclofirmadodetalle no permite duplicar registro.
            $sql = "insert into sgd_ciclofirmadodetalle (idcf, usua_login, usua_doc, estado) values (".$ciclo.", '".$login."', '".$cedula."', 0)";
            $return = $this->cursor->conn->Execute($sql);
        } catch (Exception $e) {
            $return = false;
        }
        return $return;
    }
    
    /**
     *
     * @param integer $ciclo
     * @param string $cedula
     * @param string $login
     * @param integer $opcMod
     * @param string $obs
     * @return boolean
     */
    function solicitudModificacionCiclo($ciclo, $cedula, $login, $opcMod, $obs) {
        $return = array('return' => false, 'mensaje' => '');
        try {
            $this->cursor->conn->StartTrans();
            $sql = "select m.idcf from sgd_ciclofirmadomaster m ".
                "inner join usuario s on s.usua_doc=m.usua_doc and s.usua_login=m.usua_login ".
                "inner join sgd_ciclofirmadodetalle d on d.idcf=m.idcf ".
                "left join usuario f on f.usua_doc=d.usua_doc and f.usua_login=d.usua_login ".
                "where m.idcf=$ciclo ".
                "and ((s.usua_login='$login' and s.usua_doc='$cedula') or (f.usua_login='$login' and f.usua_doc='$cedula') )";
            $ADODB_COUNTRECS = true;
            $rs = $this->cursor->conn->Execute($sql);
            $ADODB_COUNTRECS = false;
            if ($rs->RecordCount() == 0) {
                return array('return' => false, 'mensaje' => 'Error. El usuario actual no pertenece al ciclo de firmado.');
            } else {
                //Un firmante está solicitando cambios
                if (($opcMod == 2) || ($opcMod == 1) )  {
                    //validamos que solo los firmantes puedan colocar modificaciones de tipo firmante.
                    $sql="select idcf from sgd_ciclofirmadodetalle where usua_doc='$cedula' and usua_login='$login' and idcf=$ciclo";
                    $esSolicitante=$this->cursor->conn->GetOne($sql);
                    if (is_integer($esSolicitante)) {
                        $ttr = 47;
                        $sql = "update sgd_ciclofirmadomaster set estado=2 where idcf=$ciclo";
                        $this->cursor->conn->Execute($sql);
                        $sql = "insert into sgd_hist_ciclofirmado (idcf, usua_login, usua_doc, sgd_ttr_codigo, codigo_rechazo, detalle) values ($ciclo, '".$login."', '".$cedula."', $ttr, $opcMod, '$obs')";
                        $this->cursor->conn->Execute($sql);
                    } else {
                        return array('return' => false, 'mensaje' => 'Error. El usuario actual no es firmante.');
                    }
                }
                //Si opcion es habilitar el firmado... osea se hicieron correcciones o inicio de cero
                if ($opcMod == 3 || $opcMod == 4) {
                    if ($opcMod == 3) {
                        $ttr = 39;
                        $estado = 1;
                    } else {
                        $ttr = 47;
                        $estado = 4;
                    }
                    
                    $sql = "update sgd_ciclofirmadomaster set estado=$estado where idcf=$ciclo";
                    $this->cursor->conn->Execute($sql);
                    $sql = "insert into sgd_hist_ciclofirmado (idcf, usua_login, usua_doc, sgd_ttr_codigo, codigo_rechazo, detalle) values ($ciclo, '".$login."', '".$cedula."', $ttr, $opcMod, '$obs')";
                    $this->cursor->conn->Execute($sql);
                }
            }
            if ($this->cursor->conn->CompleteTrans()){
                $return = array('return' => true, 'mensaje' => 'Registro actualizado!!');
            } else {
                $return = array('return' => false, 'mensaje' => 'Error. No pudo actualizar registro.');
            }
        } catch (Exception $e) {
            $return = array('return' => false, 'mensaje' => 'Error. '.$e->getMessage());
        }
        return $return;
    }
    
    /**
     *
     * @param integer $ciclo
     * @param string $cedula
     * @param string $login
     */
    function eliminaSolicitud($ciclo, $cedula, $login){
        $return = false;
        $sql= "delete from sgd_ciclofirmadodetalle where idcf=$ciclo and usua_login='$login' and usua_doc='$cedula'";
        $this->cursor->conn->Execute($sql);
        //contamos los firmantes. vs los solicitados a firmar. Si están todos cerramos el ciclo de firmado
        // y actualizamos el pdf con el firmado digitalmente
        $cntSolicitados = $this->cursor->conn->GetOne("select count(*) from sgd_ciclofirmadodetalle where idcf=$ciclo");
        $cntFirmantes = $this->cursor->conn->GetOne("select count(*) from sgd_ciclofirmadodetalle where idcf=$ciclo and estado=1");
        if ($cntFirmantes == $cntSolicitados){
            $this->cursor->conn->Execute("update sgd_ciclofirmadomaster set estado=2 where idcf=$ciclo");
        }
    }
    
    function firmaIdDetalle($id) {
        $return = false;
        try {
            $sql = "select m.idcf, m.radi_nume_radi ".
                "from SGD_CICLOFIRMADOMASTER m ".
                "inner join SGD_CICLOFIRMADODETALLE d on d.idcf=m.idcf and d.estado=0 ".
                "where d.iddcf=$id";
            $ADODB_COUNTRECS = true;
            $rs = $this->cursor->conn->Execute($sql);
            $ADODB_COUNTRECS = false;
            if ($rs->RecordCount() == 0) {
                $return = array('RADI_NUME_RADI' => $rs->fields['RADI_NUME_RADI']);
                
                $retorno['RADI_NUME_RADI'] = $rs->fields['RADI_NUME_RADI'];
                $retorno['USUA_DOC'] = $rs->fields['USUA_DOC'];
                $retorno['SGD_FIRRAD_FIRMA'] = $rs->fields['SGD_FIRRAD_FIRMA'];
                $retorno['SGD_FIRRAD_FECHA'] = $rs->fields['SGD_FIRRAD_FECHA'];
                $retorno['SGD_FIRRAD_DOCSOLIC'] = $rs->fields['SGD_FIRRAD_DOCSOLIC'];
                $retorno['SGD_FIRRAD_FECHSOLIC'] = $rs->fields['SGD_FIRRAD_FECHSOLIC'];
            }
            $rs->Close();
        } catch (Exception $e) {
            $return = false;
        }
        return $return;
    }
    
}

?>