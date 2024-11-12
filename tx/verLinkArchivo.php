<?php

/**
 * verLinkArchivo es la clase encargada de
 * validar los permisos de acceso a un documento (imagen informacion)
 * @author Liliana Gomez Velasquez
 * @version     1.0
 * @licencia GNU/GPL v3
 * @fecha  09 sep 2009
 *
 * Modificacion Jairo Losada - 2009/10 DNP
 * http://www.correlibre.org
 * 
 * 
 */
class verLinkArchivo
{

    /**
     * Variable que se corresponde con su par, uno de los campos de la tabla SGD_TDEC_TIPODECISION
     * @db Objeto conexion
     *
     * @access public
     */
    var $db;

    /**
     * Vector que almacena el resultado de la validacion
     *
     * @var string
     * @access public
     */
    var $vecRads;

    /**
     * Vector que almacena el resultado de la validacion
     * de un Anexo
     *
     * @var string
     * @access public
     */
    var $vecRadsA;

    /**
     * Constructor encargado de obtener la conexion
     *
     * @param $db ConnectionHandler
     *            es el objeto conexion
     * @return void
     *
     */
    function verLinkArchivo($db)
    {
        /**
         * Constructor de la clase
         * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
         */
        $this->db = $db;
    }

    /**
     * Retorna el valor correspondiente al
     * resultado de la validacion
     * @numrad Numero del Radicado a validar
     *
     * @return array $vecRads resultado de la operacion de validacion
     *        
     */
    function valPermisoRadi($numradi, $radpadre = NULL)
    {
        
        // Busca el Documento del usuario Origen
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $verImg = "NO";
        
        $isql = "select r.RADI_PATH, r.SGD_SPUB_CODIGO, u.CODI_NIVEL, u.USUA_NOMB,u.USUA_DOC,
         r.RADI_USU_ANTE, r.RADI_DEPE_ACTU, r.RADI_NUME_DERI 
         from RADICADO r, USUARIO u
         where r.RADI_NUME_RADI='$numradi'
         and r.RADI_USUA_ACTU= u.USUA_CODI
         and r.RADI_DEPE_ACTU= u.DEPE_CODI";
        // $rs=$this->$db->conn->Execute($isql);
        $rs = $this->db->conn->Execute($isql);
        
        // echo "Valores sesion usua_doc".$_SESSION["usua_doc"]."krd ".$_SESSION[ 'krd' ]. "nivel".$_SESSION["nivelus"] . $krd;
        
        $consultaExpediente = "SELECT SGD_EXP_NUMERO  FROM SGD_EXP_EXPEDIENTE 
				     WHERE radi_nume_radi= $numradi AND sgd_exp_fech=(SELECT MIN(SGD_EXP_FECH) minFech  
				     from sgd_exp_expediente where radi_nume_radi= $numradi  and sgd_exp_estado<>2)  
				     and sgd_exp_estado<>2";
        
        $rsE = $this->db->conn->Execute($consultaExpediente);
        if (! $rsE->EOF) {
            $fldsSGD_EXP_SUBEXPEDIENTE = $rsE->fields["SGD_EXP_NUMERO"];
        } else {
            $fldsSGD_EXP_SUBEXPEDIENTE = "";
        }
        
        if (! $rs->EOF) {
            $seguridadRadicado = $rs->fields["SGD_SPUB_CODIGO"];
            $nivelRadicado = $rs->fields["CODI_NIVEL"];
            $USUA_ACTU_R = $rs->fields["USUA_DOC"];
            $USUA_ANTE = $rs->fields["RADI_USU_ANTE"];
            $DEPE_ACTU_R = $rs->fields["RADI_DEPE_ACTU"];
            $pathImagen = $rs->fields['RADI_PATH'];
            if ($USUA_ACTU_R == $_SESSION["usua_doc"]) {
                $verImg = "SI";
            } elseif (isset($fldsSGD_EXP_SUBEXPEDIENTE)) {
                // Consultamos el documento del usuario responsable del expediente
                $consultaDuenoExp = "SELECT USUA_DOC_RESPONSABLE	FROM SGD_SEXP_SECEXPEDIENTES 
				         WHERE SGD_EXP_NUMERO = '$fldsSGD_EXP_SUBEXPEDIENTE'";
                $rsExpDueno = $this->db->conn->Execute($consultaDuenoExp);
                $duenoExpediente = $rsExpDueno->fields["USUA_DOC_RESPONSABLE"];
                // echo "dueno del expediente es:".$duenoExpediente. " ".$fldsSGD_EXP_SUBEXPEDIENTE;
                // Si el usuario que consulta es: usuario actual o responsable del expediente puede ver el Radicado
                if ($duenoExpediente == $_SESSION['usua_doc']) {
                    $verImg = "SI";
                } elseif ($seguridadRadicado == 1) {
                    if ($DEPE_ACTU_R == '999' && $USUA_ANTE == $_SESSION['krd']) {
                        $verImg = "SI";
                    }
                } elseif ($_SESSION["nivelus"] >= $nivelRadicado) {
                    $verImg = "SI";
                }
            } elseif ($seguridadRadicado == 1) {
                if ($DEPE_ACTU_R == '999' && $USUA_ANTE == $_SESSION['krd']) {
                    $verImg = "SI";
                }
            } elseif ($_SESSION["nivelus"] >= $nivelRadicado) {
                $verImg = "SI";
            } elseif ($rs->fields["RADI_NUME_DERI"] == $radpadre && substr($numradi, 14, 1) == 1) {
                $verImg = "SI";
            }
        } else {
            $verImg = "NO SE ENCONTRO INFORMACION DEL RADICADO";
        }
        
        // $this->$vecRads['verImg']= $verImg;
        // $this->$vecRads['pathImagen']= $pathImagen;
        $vecRadsD['verImg'] = $verImg;
        $vecRadsD['pathImagen'] = $pathImagen;
        $vecRadsD['numExpe'] = $fldsSGD_EXP_SUBEXPEDIENTE;
        // echo "valores ".$vecRadsD['verImg'];
        // echo " valores ".$vecRadsD['verImg'].$vecRadsD['pathImagen']. " es".$vecRadsD['numExpe']. " ";
        return $vecRadsD;
    }

    /**
     * Retorna el valor correspondiente al
     * resultado de la validacion
     * @numrad Numero del Anexo a validar
     *
     * @return array $vecRadsA resultado de la operacion de validacion
     *        
     */
    function valPermisoAnex($numAnex)
    {
        
        // Busca el Documento del usuario Origen
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $verImg = "SI";
        $pathImagen = "";
        $isqlAnex = "select ANEX_NOMB_ARCHIVO
         from ANEXOS
         where ANEX_CODIGO = '$numAnex'";
        // $this->db->conn->debug = true;
        $rsAnex = $this->db->conn->Execute($isqlAnex);
        if (! $rsAnex->EOF) {
            $pathImagen = trim($rsAnex->fields["ANEX_NOMB_ARCHIVO"]);
        } else {
            $verImg = "NO SE ENCONTRO INFORMACION DEL RADICADO";
        }
        $vecRadsA['verImg'] = $verImg;
        $vecRadsA['pathImagen'] = $pathImagen;
        
        return $vecRadsA;
    }
}
?>
  
  