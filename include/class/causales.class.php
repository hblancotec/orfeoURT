<?PHP
/**
 * Clase donde gestionamos informacion referente a las Tablas Tematicas.
 *
 * @copyright Sistema de Gestion Documental ORFEO
 * @version 1.0
 * @author Desarrollado por Ing. Hollman Ladino Paredes.
 *  
 * Auspiciado por el Instituto de Desarrollo Urbano - IDU.
 * Adaptado para el Departamento Nacional de Planeación - DNP. 27-03-2012 
 */

class Causales
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
 * Agrega un nuevo tipo de radicado.
 *
 * @param array $datos  Vector asociativo con todos los campos y sus valores.
 * @return boolean $flag False on Error /
 */
function SetInsDatos($datos)
{
	return $this->flag;
}

/**
 * Modifica datos a un tipo de radicado.
 *
 * @param array $datos  Vector asociativo con todos los campos y sus valores.
 * @return boolean $flag False on Error /
 */
function SetModDatos($datos)
{	
	return $this->flag;
}

/**
 * Elimina una causal.
 *
 * @param  int $dato  Id del causal a eliminar.
 * @return boolean $flag False on Error /
 */
function SetDelDatos($dato)
{
	$sql = "SELECT COUNT(SGD_CAU_CODIGO) FROM SGD_DCAU_CAUSAL WHERE SGD_CAU_CODIGO=$dato";
	if ($this->cnn->GetOne($sql) > 0)
	{
		$this->flag = 0;
	}
	else 
	{
		$this->cnn->BeginTrans();
		$ok = $this->cnn->Execute('DELETE FROM SGD_CAU_CAUSAL WHERE SGD_CAU_CODIGO='.$dato);
		if ($ok)
		{	$this->cnn->CommitTrans();
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
	$sql = "SELECT SGD_CAU_DESCRIP AS DESCRIP, SGD_CAU_CODIGO AS ID FROM SGD_CAU_CAUSAL ORDER BY 1";
	$rs = $this->cnn->Execute($sql);
	if (!$rs)
		$this->flag = false;
	else
	{
		($dato1) ? $tmp1=":&lt;&lt;SELECCIONE&gt;&gt;" : $tmp1 = false;
		($dato2) ? $tmp2="Onchange='Actual(1)'" : $tmp2 = '';
		$this->flag = $rs->GetMenu('slc_cmb2',false,$tmp1,false,false,"id='slc_cmb2' class='select' $tmp2");
		unset($rs); unset($tmp1); unset($tmp2);
	}
	return $this->flag;
}

/**
 * Retorna un vector Enter description here...
 *
 * @return Array Vector numerico con los datos - False on error.
 */
function Get_ArrayDatos()
{	
	$sql = "SELECT SGD_CAU_DESCRIP AS DESCRIP, SGD_CAU_CODIGO AS ID, SGD_CAU_ESTADO AS ESTADO FROM SGD_CAU_CAUSAL ORDER BY 1";
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