<?PHP
/**
 * Clase donde gestionamos informacion referente a los Medios de Envío.
 * @copyright Sistema de Gestion Documental ORFEO
 * @author Desarrollado por Ing. Carlos Eduardo Campos.
 *  
 * Adaptado para el Departamento Nacional de Planeación - DNP. 12-11-2013 
 */

class MedEnvio
{
private $cnn;	//Conexion a la BD.
private $flag;	//Bandera para usos varios.
private $vector;//Vector con los datos.

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
 * Agrega un nuevo Motivo de Devolucion.
 *
 * @param array $datos  Vector asociativo con todos los campos y sus valores.
 * @return boolean $flag False on Error /
 */
function SetInsDatos($datos)
{
	return $this->flag;
}

/**
 * Modifica datos a un Motivo de devolucion.
 *
 * @param array $datos  Vector asociativo con todos los campos y sus valores.
 * @return boolean $flag False on Error /
 */
function SetModDatos($datos)
{	
	return $this->flag;
}

/**
 * Elimina un Motivo de Devolucion.
 *
 * @param  int $dato  Id del Motivo de Devolucion a eliminar.
 * @return boolean $flag False on Error /
 */
function SetDelDatos($dato)
{
	$sql = "SELECT COUNT(*) FROM SGD_RENV_REGENVIO WHERE SGD_FENV_CODIGO =".$dato;
	if ($this->cnn->GetOne($sql) > 0)
	{
		$this->flag = 0;
	}
	else 
	{
		$this->cnn->BeginTrans();
		$ok = $this->cnn->Execute('DELETE FROM SGD_FENV_FRMENVIO WHERE SGD_FENV_CODIGO='.$dato);
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
	return $this->flag;
}

/**
 * Retorna un combo con las opciones de la tabla vector todos los tipos de radicados.
 * 
 * @param  boolean Habilita/Deshabilita la 1a opcion SELECCIONE.
 * @param  boolean Habilita/Deshabilita la validacion Onchange hacia una funcion llamada Actual().
 * @return string Cadena con el combo - False on Error.
 */
function Get_ComboOpc($dato1, $dato2)
{	
	$sql = "SELECT SGD_FENV_DESCRIP AS DESCRIP, SGD_FENV_CODIGO AS ID  FROM SGD_FENV_FRMENVIO ORDER BY 1";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->flag = false;
	else
	{
		($dato1) ? $tmp1=":&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
		($dato2) ? $tmp2="Onchange='Actual()'" : $tmp2 = '';
		$this->flag = $rs->GetMenu('slc_cmb2',false,$tmp1,false,false,"id='slc_cmb2' class='select' $tmp2 ");
		unset($rs); unset($tmp1); unset($tmp2);
	}
	return $this->flag;
}

/**
 * Retorna un vector.
 *
 * @return Array Vector numÃ©rico con los datos - False on error.
 */
function Get_ArrayDatos()
{	
	$sql = "SELECT SGD_FENV_DESCRIP AS DESCRIP, SGD_FENV_CODIGO AS ID, SGD_FENV_ESTADO AS ESTADO FROM SGD_FENV_FRMENVIO ORDER BY 1";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->vector = false;
	else
	{	$it = 0;
		while (!$rs->EOF)
		{	$vdptosv[$it]['ID'] = $rs->fields['ID'];
			$vdptosv[$it]['NOMBRE'] = $rs->fields['DESCRIP'];
			$vdptosv[$it]['ESTADO'] = $rs->fields['ESTADO'];
			$it += 1;
			$rs->MoveNext();
		}
		$rs->Close();
		$this->vector = $vdptosv;
		unset($rs); unset($sql);
	}
	return $this->vector;
}
}
?>