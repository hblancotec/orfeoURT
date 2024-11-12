<?PHP
/**
 * Clase donde gestionamos informacion referente a los aplicativos que enlazan
 * con Orfeo.
 *
 * @copyright Sistema de Gestion Documental ORFEO
 * @version 1.0
 * @author Grupo Iyunxi Ltda
 * @modificado por Oscar Malagon implementacion en DNP.
 */
class enlaceAplicativos
{
private $cnn;   //Conexion a la BD.
private $flag;  //Bandera para usos varios.
private $vector;//Vector con los datos.
var $camposOrfeo = array(1=>'Radicado',2=>'Expediente',3=>'Anexo');
/**
 * Constructor de la classe.
 *
 * @param ConnectionHandler $db
 */
function __construct($db)
{
        $this->cnn = $db;
        $this->cnn->SetFetchMode(ADODB_FETCH_ASSOC);
}

/**
 * Agrega un nuevo tipo de aplicativo.
 *
 * @param array $datos  Vector asociativo con todos los campos y sus valores.
 * @return boolean $flag False on Error /
 * , 'txtURLWSDL'=> , 'txtUsuarioWS'=>, 'txtPasswordWS'=> , 'slcDriverBD'=> , 'txtServerBD'=> , 'txtDataBaseBD'=> , 'txtUsuarioBD'=> , 'txtPasswordBD'=> )
 */
function SetInsDatos($datos)
{
    if ( count($datos) <5 || !is_int((integer)$datos['txtId']) ||
        !is_int((integer)$datos['slcEstado']) || strlen($datos['txtModelo']>30) )
        $this->flag = false;
    else
    {
        $sql = "insert into SGD_APLICACIONES (SGD_APLI_CODIGO, SGD_APLI_DESCRIP, SGD_APLI_ESTADO, SGD_APLI_DEPE, IP_ACCESO,USUA_LOGIN,CLIENTE_WS_URLWSDL,CLIENTE_WS_USUARIO,CLIENTE_WS_PASSWORD,CLIENTE_BD_DRIVER,CLIENTE_BD_SERVER,CLIENTE_BD_DATABASE,CLIENTE_BD_USUARIO,CLIENTE_BD_PASSWORD) ";
        $sql.= "values (".$datos['txtId'].",'".$datos['txtModelo']."',".$datos['slcEstado'].",".$datos['slcDepe'].",'".$datos['txtIpAcceso']."','".$datos['slcUsua']."','".$datos['txtURLWSDL']."','". $datos['txtUsuarioWS']."','".$datos['txtPasswordWS']."','".$datos['slcDriverBD']."','".$datos['txtServerBD']."','".$datos['txtDataBaseBD']."','".$datos['txtUsuarioBD']."','".$datos['txtPasswordBD']."')";
        $this->flag = $this->cnn->Execute($sql);
    }
        return $this->flag;
}

/**
 * Modifica datos a un tipo de aplicativo.
 *
 * @param array $datos  Vector asociativo con todos los campos y sus valores.
 * @return boolean $flag False on Error /
 */
function SetModDatos($datos)
{      
    if ( count($datos) <5 || !is_int((integer)$datos['txtId']) ||
        !is_int((integer)$datos['slcEstado']) || strlen($datos['txtModelo']>30) )
        $this->flag = false;
    else
    {
        $sql =  "update SGD_APLICACIONES set SGD_APLI_DESCRIP = '".$datos['txtModelo']."', ";
                $sql.=  "SGD_APLI_ESTADO = ".$datos['slcEstado'].",SGD_APLI_DEPE = ".$datos['slcDepe'].", IP_ACCESO='".$datos['txtIpAcceso']."',USUA_LOGIN='".$datos['slcUsua']."',CLIENTE_WS_URLWSDL='".$datos['txtURLWSDL']."',CLIENTE_WS_USUARIO='".$datos['txtUsuarioWS']."',CLIENTE_WS_PASSWORD='".$datos['txtPasswordWS']."',CLIENTE_BD_DRIVER='".$datos['slcDriverBD']."',CLIENTE_BD_SERVER='".$datos['txtServerBD']."',CLIENTE_BD_DATABASE='".$datos['txtDataBaseBD']."',CLIENTE_BD_USUARIO='".$datos['txtUsuarioBD']."',CLIENTE_BD_PASSWORD='".$datos['txtPasswordBD']."' ".
                " where SGD_APLI_CODIGO=".$datos['txtId'];
        $this->flag = $this->cnn->Execute($sql);
    }
        return $this->flag;
}

/**
 * Elimina un aplicativo, siempre y cuando no haya asociaciones a él.
 *
 * @param  int $dato  Id del causal a eliminar.
 * @return boolean $flag False on Error /
 */
function SetDelDatos($dato)
{
    if (is_int((integer)$dato))
    {
        $sql = "SELECT COUNT(*) FROM RADICADO WHERE SGD_APLI_CODIGO = $dato";
        if ($this->cnn->GetOne($sql) > 0)
        {
            $this->flag = false;
        }
        else
        {
            $this->cnn->BeginTrans();
            $ok = $this->cnn->Execute('DELETE FROM PERFIL_APLICATIVOS_ENLACE WHERE SGD_APLI_CODIGO='.$dato);
            if($ok)$ok = $this->cnn->Execute('DELETE FROM SGD_APLICACIONES WHERE SGD_APLI_CODIGO='.$dato);
            if($ok)
            {
                $this->cnn->CommitTrans();
                $this->flag = true;
            }
            else
            {
                $this->cnn->RollbackTrans() ;
                $this->flag = false;
            }
        }
    }
    else
        $this->flag = false;
        return $this->flag;
}

/**
 * Retorna un combo con las opciones de la tabla enlaceAplicativos.
 *
 * @param $dato1 boolean Habilita/Deshabilita la 1a opcion SELECCIONE.
 * @param $dato2 boolean Muestra SOLO los registros activos ?.
 * @return string Cadena con el combo - False on Error.
 */
function Get_ComboOpc($dato1=true, $dato2=true, $dato3="onChange=Actual()", $dato4=false)
{
    ($dato2) ? $tmp="WHERE SGD_APLI_ESTADO=1" : $tmp = "";
        $sql = "SELECT SGD_APLI_DESCRIP AS DESCRIP,
                    SGD_APLI_CODIGO AS ID,
                    SGD_APLI_ESTADO AS ESTADO,
                    SGD_APLI_DEPE AS DEPENDENCIA
            FROM SGD_APLICACIONES $tmp ORDER BY 1";
        $rs = $this->cnn->Execute($sql);
        if (!$rs)
                $this->flag = false;
        else
        {
                ($dato1) ? $tmp1="0:&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
                $this->flag = $rs->GetMenu2('slc_cmb2',$dato4,$tmp1,false,false,"id='slc_cmb2' class='select' $dato3");
                unset($rs); unset($tmp1); unset($tmp2);
        }
        return $this->flag;
}

/**
 * Retorna un vector
 *
 * @return Array Vector numerico con los datos - False on error.
 */
function Get_ArrayDatos()
{      
        $sql = "SELECT SGD_APLI_DESCRIP AS DESCRIP,
                    SGD_APLI_CODIGO AS ID,
                    SGD_APLI_ESTADO AS ESTADO,
                    SGD_APLI_DEPE AS DEPENDENCIA,
                    IP_ACCESO,
                    USUA_LOGIN,
                    CLIENTE_WS_URLWSDL ,
                    CLIENTE_WS_USUARIO ,
                    CLIENTE_WS_PASSWORD ,
                    CLIENTE_BD_SERVER ,
                    CLIENTE_BD_DATABASE ,
                    CLIENTE_BD_DRIVER ,
                    CLIENTE_BD_USUARIO ,
                    CLIENTE_BD_PASSWORD 
            FROM SGD_APLICACIONES ORDER BY 1";
        $rs = $this->cnn->Execute($sql);
        if (!$rs)
                $this->vector = false;
        else
        {       $it = 0;
                while (!$rs->EOF)
                {       $vdptosv[$it]['ID'] = $rs->fields['ID'];
                        $vdptosv[$it]['NOMBRE'] = $rs->fields['DESCRIP'];
                        $vdptosv[$it]['ESTADO'] = $rs->fields['ESTADO'];
                        $vdptosv[$it]['DEPENDENCIA'] = $rs->fields['DEPENDENCIA'];
                        $vdptosv[$it]['IP_ACCESO'] = $rs->fields['IP_ACCESO'];
                        $vdptosv[$it]['USUA_LOGIN'] = $rs->fields['USUA_LOGIN'];
                        $vdptosv[$it]['CLIENTE_WS_URLWSDL'] = $rs->fields['CLIENTE_WS_URLWSDL'];
                        $vdptosv[$it]['CLIENTE_WS_USUARIO'] = $rs->fields['CLIENTE_WS_USUARIO'];
                        $vdptosv[$it]['CLIENTE_WS_PASSWORD'] = $rs->fields['CLIENTE_WS_PASSWORD'];
                        $vdptosv[$it]['CLIENTE_BD_SERVER'] = $rs->fields['CLIENTE_BD_SERVER'];
                        $vdptosv[$it]['CLIENTE_BD_DATABASE'] = $rs->fields['CLIENTE_BD_DATABASE'];
                        $vdptosv[$it]['CLIENTE_BD_DRIVER'] = $rs->fields['CLIENTE_BD_DRIVER'];
                        $vdptosv[$it]['CLIENTE_BD_USUARIO'] = $rs->fields['CLIENTE_BD_USUARIO'];
                        $vdptosv[$it]['CLIENTE_BD_PASSWORD'] = $rs->fields['CLIENTE_BD_PASSWORD'];
                        $it += 1;
                        $rs->MoveNext();
                }
                $rs->Close();
                $this->vector = $vdptosv;
                unset($rs); unset($sql);
        }
        return $this->vector;
}
/**
 *
 * @return type 
 */
function get_Metodos(){
    
    $sql = "SELECT COD_METODO ,
                    NOMBRE ,
                    ESTADO ,
                    DESCRIPCION
            FROM METODOS_WS ORDER BY 1";
        $rs = $this->cnn->Execute($sql);
        if (!$rs)
                $this->vector = false;
        else
        {       $it = 0;
                while (!$rs->EOF)
                {       $vdptosv[$it]['COD_METODO'] = $rs->fields['COD_METODO'];
                        $vdptosv[$it]['NOMBRE'] = $rs->fields['NOMBRE'];
                        $vdptosv[$it]['ESTADO'] = $rs->fields['ESTADO'];
                        $vdptosv[$it]['DESCRIPCION'] = $rs->fields['DESCRIPCION'];
                        $it += 1;
                        $rs->MoveNext();
                }
                $rs->Close();
                $this->vector = $vdptosv;
                unset($rs); unset($sql);
        }
        return $this->vector;
}
/**
 *
 * @param type $cod_apl
 * @return type 
 */
function get_MetodosPermitidos($cod_apl=""){
    $w= !$cod_apl? "": " where SGD_APLI_CODIGO=$cod_apl  ";
    $sql = "SELECT COD_METODO,SGD_APLI_CODIGO FROM PERFIL_APLICATIVOS_ENLACE $w ORDER BY 2";
        $rs = $this->cnn->Execute($sql);
        if (!$rs)
                $this->vector = false;
        else
        {       $it = 0;
                while (!$rs->EOF)
                {       $vdptosv[$rs->fields['SGD_APLI_CODIGO']][$rs->fields['COD_METODO']] = $rs->fields['COD_METODO'];
                        $it += 1;
                        $rs->MoveNext();
                }
                $rs->Close();
                $this->vector = $vdptosv;
                unset($rs); unset($sql);
        }
        return $this->vector;
}
/**
 *
 * @param type $arrayMetodos
 * @param type $codAplicativo
 * @return type 
 */
function setMetodosPermitidos($arrayMetodos,$codAplicativo){

    if (!is_array($arrayMetodos)) $this->flag = false;
    else
    {
        foreach ($arrayMetodos as $i=>$val){
            
            $sql="select max(ID_PERFIL_WS) as ID from PERFIL_APLICATIVOS_ENLACE";
            $rs=$this->cnn->Execute($sql);
            if($rs)$ID=($rs->fields['ID']+1);
            $sql = "SELECT COUNT(*) FROM PERFIL_APLICATIVOS_ENLACE WHERE SGD_APLI_CODIGO = $codAplicativo and COD_METODO=$val";
            if ($this->cnn->GetOne($sql) == 0)
            {
                $sql = "insert into PERFIL_APLICATIVOS_ENLACE (SGD_APLI_CODIGO , COD_METODO , ID_PERFIL_WS) ";
                $sql.= "values ($codAplicativo,".$val.",$ID)";
                $this->flag = $this->cnn->Execute($sql);
            }
        }
        $sql= "delete from PERFIL_APLICATIVOS_ENLACE where SGD_APLI_CODIGO=$codAplicativo and COD_METODO not in (". implode(",",$arrayMetodos ).")";
        $this->flag = $this->cnn->Execute($sql);
    }
    return $this->flag;
}
/**
 *
 * @return type 
 */
function getArrayMetodos(){
    
    $sql = "SELECT COD_METODO, NOMBRE, ESTADO, DESCRIPCION FROM METODOS_WS ORDER BY 1";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->vector = false;
	else
	{	$it = 0;
		while (!$rs->EOF)
		{	$vdptosv[$it]['ID'] = $rs->fields['COD_METODO'];
			$vdptosv[$it]['NOMBRE'] = $rs->fields['NOMBRE'];
			$vdptosv[$it]['ESTADO'] = $rs->fields['ESTADO'];
                        $vdptosv[$it]['DESCRIPCION'] = $rs->fields['DESCRIPCION'];
			$it += 1;
			$rs->MoveNext();
		}
		$rs->Close();
		$this->vector = $vdptosv;
		unset($rs); unset($sql);
	}
	return $this->vector;
    
}
/**
 *
 * @param type $dato1
 * @param type $dato2
 * @return type 
 */
function getComboMetodos($dato1=true, $dato2=true,$name='slc_cmb2', $def=false,$app=false ){
    
    if($app)$join=" join PERFIL_APLICATIVOS_ENLACE pm on pm.cod_metodo=mw.cod_metodo and pm.sgd_apli_codigo=".$app;
    $sql = "SELECT  mw.NOMBRE,mw.COD_METODO FROM METODOS_WS mw $join ORDER BY 2 ";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->flag = false;
	else
	{
		($dato1) ? $tmp1=":&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
		($dato2) ? $tmp2="Onchange='Actual()'" : $tmp2 = '';
		$this->flag = $rs->GetMenu2($name,$def,$tmp1,false,false,"id='$name' class='select' $tmp2");
		unset($rs); unset($tmp1); unset($tmp2);
	}
	return $this->flag;
}
/**
 *
 * @param type $apli_codigo
 * @return type 
 */
function getArrayAccionesExt($apli_codigo=0){
    $sql = "SELECT SGD_ACCION_DESCRIPCION AS DESCRIP, SGD_ACCION_CODIGO AS ID, SGD_ACCION_ESTADO AS ESTADO, SGD_APLI_CODIGO FROM SGD_ACCIONES_EXTERNAS where SGD_APLI_CODIGO=$apli_codigo ORDER BY 1";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->vector = false;
	else
	{	$it = 0;
		while (!$rs->EOF)
		{	$vdptosv[$it]['ID'] = $rs->fields['ID'];
			$vdptosv[$it]['NOMBRE'] = $rs->fields['DESCRIP'];
			$vdptosv[$it]['ESTADO'] = $rs->fields['ESTADO'];
                        $vdptosv[$it]['SGD_APLI_CODIGO'] = $rs->fields['SGD_APLI_CODIGO'];
			$it += 1;
			$rs->MoveNext();
		}
		$rs->Close();
		$this->vector = $vdptosv;
		unset($rs); unset($sql);
	}
	return $this->vector;
}

function getComboAccionesExt($dato1=true, $dato2=true,$apli_codigo=0, $name='slc_cmb2', $dato3=false){
    
    $sql = "SELECT SGD_ACCION_DESCRIPCION AS DESCRIP, SGD_ACCION_CODIGO AS ID FROM SGD_ACCIONES_EXTERNAS where SGD_APLI_CODIGO=$apli_codigo ORDER BY 1";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->flag = false;
	else
	{
		($dato1) ? $tmp1=":&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
		($dato2) ? $tmp2="Onchange='Actual()'" : $tmp2 = '';
		$this->flag = $rs->GetMenu2($name ,$dato3,$tmp1,false,false,"id='$name' class='select' $tmp2");
		unset($rs); unset($tmp1); unset($tmp2);
	}
	return $this->flag;
}

/**
 * Retorna un combo con las opciones de la tabla enlaceAplicativos.
 *
 * @param $dato1 boolean Habilita/Deshabilita la 1a opcion SELECCIONE.
 * @param $dato2 boolean Muestra SOLO los registros activos ?.
 * @return string Cadena con el combo - False on Error.
 */
function getAplicaciones($dato1=true, $dato2=true,$default)
{
    ($dato2) ? $tmp="WHERE SGD_APLI_ESTADO=1" : $tmp = "";
        $sql = "SELECT SGD_APLI_DESCRIP AS DESCRIP,
                    SGD_APLI_CODIGO AS ID,
                    SGD_APLI_ESTADO AS ESTADO,
                    SGD_APLI_DEPE AS DEPENDENCIA
            FROM SGD_APLICACIONES $tmp ORDER BY 1";
        $rs = $this->cnn->Execute($sql);
        if (!$rs)
                $this->flag = false;
        else
        {
                ($dato1) ? $tmp1="0:&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
                $this->flag = $rs->GetMenu2('app_codigo',$default,$tmp1,false,false,"id='app_codigo' class='select' onChange='this.form.submit();'");
                unset($rs); unset($tmp1); unset($tmp2);
        }
        return $this->flag;
}
/**
 *
 * @param type $apli_codigo
 * @return type 
 */
function getArrayCamposExt($apli_codigo=0){
    $sql = "SELECT SGD_NOMBRE_CAMPO AS DESCRIP, SGD_COD_CAMPOEXT AS ID, SGD_ESTADO_CAMPO AS ESTADO, SGD_APLI_CODIGO FROM SGD_CAMPOS_APPEXT where SGD_APLI_CODIGO=$apli_codigo ORDER BY 1";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->vector = false;
	else
	{	$it = 0;
		while (!$rs->EOF)
		{	$vdptosv[$it]['ID'] = $rs->fields['ID'];
			$vdptosv[$it]['NOMBRE'] = $rs->fields['DESCRIP'];
			$vdptosv[$it]['ESTADO'] = $rs->fields['ESTADO'];
                        $vdptosv[$it]['SGD_APLI_CODIGO'] = $rs->fields['SGD_APLI_CODIGO'];
			$it += 1;
			$rs->MoveNext();
		}
		$rs->Close();
		$this->vector = $vdptosv;
		unset($rs); unset($sql);
	}
	return $this->vector;
}
/**
 *
 * @param type $dato1
 * @param type $dato2
 * @param type $apli_codigo
 * @return type 
 */
function getComboCamposExt($dato1=true, $dato2=true,$apli_codigo=0,$tmp4="slc_cmb2",$valDefault=false){
    
    if(!$tmp4)$tmp4="slc_cmb2";
    $sql = "SELECT SGD_NOMBRE_CAMPO AS DESCRIP, SGD_COD_CAMPOEXT AS ID FROM SGD_CAMPOS_APPEXT where SGD_APLI_CODIGO=$apli_codigo ORDER BY 1";
    $rs = $this->cnn->Execute($sql);
    if (!$rs)
            $this->flag = false;
    else
    {
            ($dato1) ? $tmp1=":&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
            ($dato2) ? $tmp2="Onchange='Actual()'" : $tmp2 = '';
            $this->flag = $rs->GetMenu2("$tmp4",$valDefault,$tmp1,false,false,"id='$tmp4' class='select' $tmp2");
            unset($rs); unset($tmp1); unset($tmp2);
    }
    return $this->flag;
}

function getCamposOrfeo($nombre='camposOrfeo',$opcTodos=true,$opcDefault=false, $dato1=''){
     $opcArray = explode(':',$opcTodos);
        //die(count($opcArray));
    if ($opcTodos && (count($opcArray) <> 2)) return false;
    $tmp="<select name='$nombre' id='$nombre' class='select' $dato1>";
    if ($opcTodos) $tmp .= "<option value='".$opcArray[0]."'>".$opcArray[1]."</option>";
    foreach ($this->camposOrfeo as $key => $valor)
    {
        $sel = ($opcDefault == $key) ? 'selected' : '';
        $tmp .= "<option value='$key' $sel>$valor</option>";
    }
    $tmp .="</select> ";
    return $tmp;
}

function setInsCamposHomologos($datos)
{
    if ( count($datos) <5 || !is_int((integer)$datos['cmbCampExt']) ||
        !is_int((integer)$datos['cmbApliCodi'])  )
        $this->flag = false;
    else
    {   
        $sql="select max(SGD_COD_CAMPOHOM) as ID from SGD_CAMPOS_HOMOLOGOS";
        $rs=$this->cnn->Execute($sql);
        if($rs){
            $ID=($rs->fields['ID']+1);
            if($datos['cmbCamposOrfeo']==1){
                ($datos['todotipoR'])? $ctpr=$datos['todotipoR']:$ctpr=0;
                ($datos['cmbTiposRad'])? $cmbTiposRad=$datos['cmbTiposRad']:$cmbTiposRad="null";
            }else{
                $ctpr='0';
                $cmbTiposRad='null';
            }
            $sql = "insert into SGD_CAMPOS_HOMOLOGOS 
                           (SGD_COD_CAMPOHOM, SGD_APLI_CODIGO, SGD_COD_CAMPOEXT, SGD_CAMPO_ORFEO, SGD_TIPORAD_ESPECIFICO,SGD_TRAD_CODIGO) ";
            $sql.= "values (".$ID.",".$datos['cmbApliCodi'].",".$datos['cmbCampExt'].",".$datos['cmbCamposOrfeo'].",".$ctpr.",".$cmbTiposRad.")";
            $this->flag = $this->cnn->Execute($sql);
        }
    }
        return $this->flag;
}

function setInsAccionesExt($datos)
{
    if ( count($datos) <3 || !is_int((integer)$datos['accionExt']) ||
        !is_int((integer)$datos['cmbApliCodi'])  )
        $this->flag = false;
    else
    {   
        $sql="select max(SGD_COD_RELACION) as ID from SGD_RELACION_ACCIONES";
        $rs=$this->cnn->Execute($sql);
        if($rs){
            $ID=($rs->fields['ID']+1);
            $sql = "insert into SGD_RELACION_ACCIONES 
                           (SGD_COD_RELACION, SGD_APLI_CODIGO, SGD_ACCION_CODIGO, SGD_COD_METODO) ";
            $sql.= "values (".$ID.",".$datos['cmbApliCodi'].",".$datos['accionExt'].",".$datos['metodoOrfeo'].")";
            $this->flag = $this->cnn->Execute($sql);
        }
    }
        return $this->flag;
}

}
?>
