<?PHP

/**
 * Clase donde gestionamos informacion referente a los tipos de radicados.
 *
 * @copyright Sistema de Gestion Documental ORFEO
 * @version 1.0
 * @author Ing. Hollman Ladino Paredes(DNP).
 *
 */
class TipRads {

    private $cnn; //Conexion a la BD.
    private $flag; //Bandera para usos varios.
    private $add; //Conexion al Adodb Data Dictionary.

    /**
     * Constructor de la classe.
     *
     * @param ConnectionHandler $db
     */

    function __construct($db) {
        $this->cnn = $db;
        $this->cnn->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $this->add = NewDataDictionary($this->cnn->conn);
    }

    /**
     * Agrega un nuevo tipo de radicado.
     *
     * @param array $datos  Vector asociativo con todos los campos y sus valores.
     * @return boolean $flag False on Error /
     */
    function SetInsDatosTipRad($datos) {
        $this->cnn->conn->BeginTrans();

        $ok1 = $this->cnn->conn->Replace('SGD_TRAD_TIPORAD', $datos, 'SGD_TRAD_CODIGO', true);

        $sql = $this->add->AddColumnSQL('DEPENDENCIA', "DEPE_RAD_TP$datos[SGD_TRAD_CODIGO] I4");
        $ok2 = $this->cnn->conn->Execute($sql[0]);
        $sql = $this->add->AddColumnSQL('SGD_TPR_TPDCUMENTO', "SGD_TPR_TP$datos[SGD_TRAD_CODIGO] I1");
        $ok3 = $this->cnn->conn->Execute($sql[0]);
        $sql = $this->add->AddColumnSQL('USUARIO', "USUA_PRAD_TP$datos[SGD_TRAD_CODIGO] I1");
        $ok4 = $this->cnn->conn->Execute($sql[0]);
        $ok5 = $this->cnn->conn->Replace('CARPETA', array('CARP_CODI' => $datos['SGD_TRAD_CODIGO'], 'CARP_DESC' => $datos['SGD_TRAD_DESCR']), 'CARP_CODI', true);
        $sql = $this->add->AddColumnSQL('SGD_TIP3_TIPOTERCERO', "SGD_TPR_TP$datos[SGD_TRAD_CODIGO] I1");
        $ok6 = $this->cnn->conn->Execute($sql[0]);

        if ($ok1 && $ok2 && $ok3 && $ok4 && $ok5 && $ok6) {
            $this->cnn->conn->CommitTrans();
            $this->flag = 4;
        } else {
            $this->cnn->conn->RollbackTrans();
            $this->flag = 2;
        }
        return $this->flag;
    }

    /**
     * Modifica datos a un tipo de radicado.
     *
     * @param array $datos  Vector asociativo con todos los campos y sus valores.
     * @return boolean $flag False on Error /
     */
    function SetModDatosTipRad($datos) {
        $this->cnn->conn->BeginTrans();
        $flag = $this->cnn->conn->Replace('CARPETA',
                        array('CARP_CODI' => $datos['SGD_TRAD_CODIGO'],
                            'CARP_DESC' => $datos['SGD_TRAD_DESCR']),
                        'CARP_CODI');
        if ($flag)
            $flag = $this->cnn->conn->Replace('SGD_TRAD_TIPORAD', $datos, 'SGD_TRAD_CODIGO');
        if ($flag) {
            $this->cnn->conn->CommitTrans();
            $this->flag = 3;
        } else {
            $this->cnn->conn->RollbackTrans();
            $this->flag = 2;
        }
        return $this->flag;
    }

    /**
     * Elimina un tipo de radicado.
     *
     * @param  int $dato  Id del T.R. a eliminar.
     * @return boolean $flag False on Error /
     */
    function SetDelDatosTipRad($dato) {
        $this->cnn->conn->BeginTrans();
        $sql = "SELECT count(RADI_NUME_RADI) as CNT FROM RADICADO WHERE " .
                $this->cnn->conn->substr . "(CAST(RADI_NUME_RADI as varchar),14,1) =" . $dato;
        $rs = $this->cnn->conn->Execute($sql);
        if ($rs->Fields('CNT') > 0)
            $this->flag = 5;
        else {
            $ok1 = $this->cnn->conn->Execute("DELETE FROM SGD_TRAD_TIPORAD WHERE SGD_TRAD_CODIGO=" . $dato);
            $sql = $this->add->DropColumnSQL('DEPENDENCIA', 'DEPE_RAD_TP' . $dato);
            $ok2 = $this->cnn->conn->Execute($sql[0]);
            $sql = $this->add->DropColumnSQL('SGD_TPR_TPDCUMENTO', 'SGD_TPR_TP' . $dato);
            $ok3 = $this->cnn->conn->Execute($sql[0]);
            $sql = $this->add->DropColumnSQL('USUARIO', 'USUA_PRAD_TP' . $dato);
            $ok4 = $this->cnn->conn->Execute($sql[0]);
            $ok5 = $this->cnn->conn->Execute("DELETE FROM CARPETA WHERE CARP_CODI=" . $dato);
            $sql = $this->add->DropColumnSQL('SGD_TIP3_TIPOTERCERO', 'SGD_TPR_TP' . $dato);
            $ok6 = $this->cnn->conn->Execute($sql[0]);
            //$ok7 = $this->add->DropTableSQL($tabname);	//Eliminacion de las tablas secuenciales.
            //$sql = "SELECT DEPE_RAD_TP".$dato." FROM DEPENDENCIA GROUP BY DEPE_RAD_TP".$dato;
        }

        if ($ok1 && $ok2 && $ok3 && $ok4 && $ok5 && $ok6) {
            $this->cnn->conn->CommitTrans();
            $this->flag = 0;
        } else {
            $this->cnn->conn->RollbackTrans();
            $this->flag = 5;
        }
        return $this->flag;
    }

    /**
     * Retorna un vector todos los tipos de radicados.
     *
     * @return array $vector False on Error.
     */
    function GetArrayIdTipRad() {
        $sql = "SELECT SGD_TRAD_CODIGO as ID FROM SGD_TRAD_TIPORAD ORDER BY 1";
        $rs = $this->cnn->conn->Execute($sql);
        if (!$rs)
            $this->flag = 5;
        else {
            $this->flag = $this->cnn->conn->GetAll($sql);
        }
        return $this->flag;
    }

    function getComboTipoRad($dato1=true,$tmp2="onchange='actual()'",$name="cmbTRad",$valDef=false){
        
        $sql = "SELECT SGD_TRAD_DESCR AS DESCRIP, SGD_TRAD_CODIGO AS ID FROM SGD_TRAD_TIPORAD ORDER BY 1";
	$rs = $this->cnn->conn->Execute($sql);
	if (!$rs)
		$this->flag = false;
	else
	{
		($dato1) ? $tmp1=":&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
		$this->flag = $rs->GetMenu2($name,$valDef,$tmp1,false,false,"id='$name' class='select' $tmp2");
		unset($rs); unset($tmp1); unset($tmp2);
	}
	return $this->flag;  
    }
}

?>
