<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}
if ($_SESSION['usua_admin_sistema'] != 1) {
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz="../..";
include('../../config.php'); 		// incluir configuracion.
include 'adodb/adodb.inc.php';
$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
$ADODB_COUNTRECS = false;

$error = 0;
include "$ruta_raiz/include/db/ConnectionHandler.php";
$db 	= new ConnectionHandler("$ruta_raiz");

function valueToJsValue($value, $encoding = false)
{	if (!is_numeric($value))
	{	$value = str_replace('\\', '\\\\', $value);
		$value = str_replace('"', '\"', $value);
		$value = '"'.$value.'"';
	}
	if ($encoding)
	{	switch ($encoding)
		{	case 'utf8' :	return iconv("ISO-8859-2", "UTF-8", $value);
							break;
		}
	}
	else
	{	return $value;	}
}

function arrayToJsArray( $array, $name, $nl = "\n", $encoding = false )
{	if (is_array($array))
	{	$jsArray = $name . ' = new Array();'.$nl;
		foreach($array as $key => $value)
		{	switch (gettype($value))
			{	case 'unknown type':
				case 'resource':
				case 'object':	break;
				case 'array':	$jsArray .= arrayToJsArray($value,$name.'['.valueToJsValue($key, $encoding).']', $nl);
								break;
				case 'NULL':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = null;'.$nl;
								break;
				case 'boolean':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.($value ? 'true' : 'false').';'.$nl;
								break;
				case 'string':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.valueToJsValue($value, $encoding).';'.$nl;
								break;
				case 'double':
				case 'integer':	$jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.$value.';'.$nl;
								break;
				default:	trigger_error('Hoppa, egy új típus a PHP-ben?'.__CLASS__.'::'.__FUNCTION__.'()!', E_USER_WARNING);
			}
		}
		return $jsArray;
	}
	else
	{	return false;	}
}

if ($db->conn)
{	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

	if (isset($_POST['btn_accion']))
	{	$record = array();
		$record['SGD_FENV_CODIGO'] = $_POST['id_fenv'];		//forma de envio.
		$record['SGD_TAR_CODIGO'] =  $_POST['txt_idTar'];	//cdigo de la tarifa.
		$record['SGD_CLTA_CODSER'] = $_POST['slc_TipoTar'];	//tipo de tarifa.
		$record['SGD_CLTA_DESCRIP'] = $_POST['txt_desc'];	//descripcion de tarifa
		$record['SGD_CLTA_PESDES'] = $_POST['txt_lim1'];	//lmite inferior peso
		$record['SGD_CLTA_PESHAST'] = $_POST['txt_lim2'];	//lmite superioir peso
		switch($_POST['btn_accion'])
		{	Case 'Agregar':
			Case 'Modificar':
				{	$db->conn->BeginTrans();
					$ok = $db->conn->Replace('SGD_CLTA_CLSTARIF',$record,array('SGD_FENV_CODIGO','SGD_TAR_CODIGO','SGD_CLTA_CODSER'),$autoquote = true);
					if ($ok)
					{	$record = array_slice($record,0,3,true);
						if ($_POST['slc_TipoTar']==1)
						{	$record['SGD_TAR_VALENV1'] = $_POST['txt_v1'];		//valor envio (Urbano/Zona1)
							$record['SGD_TAR_VALENV2'] = $_POST['txt_v2'];		//valor envio (Regional/Zona2)
							$record['SGD_TAR_VALENV1G1'] = $_POST['txt_v3'];	//valor envio (Nacional/Zona3)
							$record['SGD_TAR_VALENV2G2'] = $_POST['txt_v4'];    //valor envio (Especial) 
						}
						else
						{	$record['SGD_TAR_VALENV1'] = $_POST['txt_v1'];		//valor envio (Urbano/Zona1)
							$record['SGD_TAR_VALENV2'] = $_POST['txt_v2'];		//valor envio (Regional/Zona2)
							$record['SGD_TAR_VALENV1G1'] = $_POST['txt_v3'];	//valor envio (Nacional/Zona3)
							$record['SGD_TAR_VALENV2G2'] = 0;                   //valor envio (grupo2)
						}
						$ok = $db->conn->Replace('SGD_TAR_TARIFAS',$record,array('SGD_FENV_CODIGO','SGD_TAR_CODIGO','SGD_CLTA_CODSER'),$autoquote = true);
					}
					if ($ok)
					{	$db->conn->CommitTrans();
						$error = $ok;
					}
					else
					{	$db->conn->RollbackTrans();
						$error = 3;
					}
				}break;
			Case 'Eliminar':{	$record = array_slice($record, 0, 3);
								$db->conn->BeginTrans();
								$ok = $db->conn->Execute('DELETE FROM SGD_CLTA_CLSTARIF WHERE SGD_FENV_CODIGO='.$record['SGD_FENV_CODIGO'].' AND SGD_TAR_CODIGO='.$record['SGD_TAR_CODIGO'].' AND SGD_CLTA_CODSER='.$record['SGD_CLTA_CODSER']);
								if ($ok) $ok = $db->conn->Execute('DELETE FROM SGD_TAR_TARIFAS WHERE SGD_FENV_CODIGO='.$record['SGD_FENV_CODIGO'].' AND SGD_TAR_CODIGO='.$record['SGD_TAR_CODIGO'].' AND SGD_CLTA_CODSER='.$record['SGD_CLTA_CODSER']);
								if ($ok)
								{	$db->conn->CommitTrans();
									$error = 4;
								}
								else
								{	$db->conn->RollbackTrans();
									$error = 3;
								}
					 		}break;
			Default: break;
		}
		unset($record);
	}

	$sql_fenv = "SELECT SGD_FENV_DESCRIP, SGD_FENV_CODIGO FROM SGD_FENV_FRMENVIO ORDER BY SGD_FENV_DESCRIP";
	$Rs_fenv = $db->conn->Execute($sql_fenv);
	if (!($Rs_fenv)) {	$error = 3;	$nomTabla = "Formas de Envio";	}


	if ($_POST['id_fenv'] and $_POST['slc_TipoTar'])
	{	
		$sql_clta = "SELECT SGD_CLTA_CLSTARIF.SGD_CLTA_DESCRIP AS DESCCONSTAR, SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO AS IDCONSTAR, ".
					"SGD_CLTA_CLSTARIF.SGD_CLTA_PESDES AS LIMPESOINF, SGD_CLTA_CLSTARIF.SGD_CLTA_PESHAST AS LIMPESOSUP, ".
					"SGD_TAR_TARIFAS.SGD_TAR_VALENV1 AS VAL1, SGD_TAR_TARIFAS.SGD_TAR_VALENV2 AS VAL2, ".
					"SGD_TAR_TARIFAS.SGD_TAR_VALENV1G1 AS VAL3, SGD_TAR_TARIFAS.SGD_TAR_VALENV2G2 AS VAL4 ".
					"FROM  SGD_CLTA_CLSTARIF, SGD_TAR_TARIFAS ".
					"WHERE SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO = SGD_TAR_TARIFAS.SGD_FENV_CODIGO AND ".
					"SGD_CLTA_CLSTARIF.SGD_TAR_CODIGO = SGD_TAR_TARIFAS.SGD_TAR_CODIGO AND ".
					"SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER = SGD_TAR_TARIFAS.SGD_CLTA_CODSER AND ".
					"SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO = ".$_POST['id_fenv']." AND SGD_CLTA_CLSTARIF.SGD_CLTA_CODSER = ".$_POST['slc_TipoTar'].
					"ORDER BY SGD_CLTA_CLSTARIF.SGD_CLTA_DESCRIP, SGD_CLTA_CLSTARIF.SGD_FENV_CODIGO";
		$rsclta = $db->conn->Execute($sql_clta);
		if ($rsclta)
		{	
		    $it = 1;
			$vcltav = array();
			while (!$rsclta->EOF)
			{	
			    $vcltav[$it]['IdConsTar'] = $rsclta->fields['IDCONSTAR'];
    			$vcltav[$it]['DescConsTar'] = $rsclta->fields['DESCCONSTAR'];
    			$vcltav[$it]['LimPesoInf'] = $rsclta->fields['LIMPESOINF'];
    			$vcltav[$it]['LimPesoSup'] = $rsclta->fields['LIMPESOSUP'];
    			$vcltav[$it]['Val1'] = $rsclta->fields['VAL1'];
    			$vcltav[$it]['Val2'] = $rsclta->fields['VAL2'];
    			$vcltav[$it]['Val3'] = $rsclta->fields['VAL3'];
				$vcltav[$it]['Val4'] = $rsclta->fields['VAL4'];
				$it += 1;
				$rsclta->MoveNext();
			}
			//$rsclta->Move(0);
			$rsclta = $db->conn->Execute($sql_clta);
		}
		else
		{	$error = 3;	$nomTabla = "Clasificacin de tarifas";	}
	}
}
else
{	$error = 3;
}
?>
<html>
<script language="JavaScript">
<!--
function Actualiza()
{
var Obj = document.getElementById('id_clta');
var i = Obj.selectedIndex;
if (i>0)
{	document.getElementById('txt_idTar').value = vp[i]['IdConsTar'];
	document.getElementById('txt_desc').value = vp[i]['DescConsTar'];
	document.getElementById('txt_lim1').value = vp[i]['LimPesoInf'];
	document.getElementById('txt_lim2').value = vp[i]['LimPesoSup'];
	document.getElementById('txt_v1').value = vp[i]['Val1'];
	document.getElementById('txt_v2').value = vp[i]['Val2'];
	document.getElementById('txt_v3').value = vp[i]['Val3'];
	document.getElementById('txt_v4').value = vp[i]['Val4'];
}
else
{	document.getElementById('txt_idTar').value = '';
	document.getElementById('txt_desc').value = '';
	document.getElementById('txt_lim1').value = '';
	document.getElementById('txt_lim2').value = '';
	document.getElementById('txt_v1').value = '';
	document.getElementById('txt_v2').value = '';
	document.getElementById('txt_v3').value = '';
	document.getElementById('txt_v4').value = '';
}
}

function rightTrim(sString)
{	while (sString.substring(sString.length-1, sString.length) == ' ')
	{	sString = sString.substring(0,sString.length-1);  }
	return sString;
}

function addOpt(oCntrl, iPos, sTxt, sVal)
{	var selOpcion=new Option(sTxt, sVal);
	eval(oCntrl.options[iPos]=selOpcion);
}

function cambia(oCntrl)
{	while (oCntrl.length)
	{	oCntrl.remove(0);	}
	$indice = 0;
	addOpt(oCntrl, $indice, "<< Seleccione Tarifa >>", $indice);
	for ($x=0; $x < vp.length; $x++)
	{	if (vp[$x]["IdConsTar"] == document.form1.id_fenv.options[document.form1.id_fenv.selectedIndex].value)
		{	$indice += 1;
			addOpt(oCntrl, $indice, vp[$x]["DescConsTar"], vp[$x]["id_pais"]);
		}
	}
}

function validarinfo(form)
{	for(i=0;i<form.length;i++)
  	{	switch (form.elements[i].type)
  		{	case 'text':
  			case 'textarea':
  			case 'select-multiple':
  				{	if (rightTrim(form.elements[i].value) == '')
					{	alert("Por favor complete todos los campos del registro");
						form.elements[i].focus();
						return false;
					}
					if ((form.elements[i].name != 'txt_desc') && ((parseInt(form.elements[i].value) < 0) || isNaN(parseInt(form.elements[i].value)) || (parseInt(form.elements[i].value) > 9999999)))
					{	alert ("Digite cantidad numerica");
						form.elements[i].focus();
						return false;
					}
				}break;
	    	case 'checkbox':
	    		{	alert(form.elements[i].checked);
	    		}break;
	    	case 'select-one':
	    		{  	if ( (form.elements[i].name !='id_clta') && (form.elements[i].value == '0'))
					{	alert("Por favor complete todos los campos del registro");
						form.elements[i].focus();
						return false;
					}
	    		}break;
  		}
  	}
  	form.submit();
}

function ver_listado(que)
{
	window.open('listados.php?var=tar','','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
}

function anula_todo()
{
	document.form1.slc_TipoTar.value = 0;
	document.form1.id_clta.value = 0;
	Actualiza();
}

function anula_datos()
{
	document.form1.txt_idTar.value="";
	document.form1.txt_lim1.value="";
	document.form1.txt_lim2.value="";
	document.form1.txt_desc.value="";
	document.form1.txt_v1.value="";
	document.form1.txt_v2.value="";
}

<?php echo arrayToJsArray($vcltav, 'vp'); ?>
//-->
</script>
<head>
<title>Orfeo - Admor de Tarifas.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../estilos/orfeo.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="form1" id="form1" method="post" action="<?= $_SERVER['PHP_SELF']?>">
<input type="hidden" id="hdBandera" name="hdBandera" value="">
<table width="75%" align="center" border="1" cellspacing="0" class="tablas">
	<tr bordercolor="#FFFFFF">
		<td colspan="6" height="40" class="titulos4" valign="middle" align="center">Administraci&oacute;n de tarifas.</td>
	</tr>
	<tr bordercolor="#FFFFFF">
    	<td align="center" valign="middle" class="titulos2">1.</td>
    	<td align="left" class="titulos2">Forma del envio</td>
    	<td colspan="4" class="listado2">
<?		// Listamos los continentes.
    		echo $Rs_fenv->GetMenu2('id_fenv',$_POST['id_fenv'],"0:&lt;&lt; SELECCIONE &gt;&gt;",false,0," id=\"id_fenv\" class='select' Onchange='anula_todo();' ");
	    	$Rs_fenv->Close();
?>
		</td>
	</tr>
	<tr bordercolor="#FFFFFF">
    	<td width="5%" align="center" valign="middle" class="titulos2">2.</td>
	    <td width="26%" align="left" class="titulos2">Localizaci&oacute;n del Env&iacute;o </td>
	    <td colspan="4" class="listado2">
	    	<select name="slc_TipoTar" class="select" id="slc_TipoTar" onChange="if (id_fenv.value == 0) {alert ('Seleccione Forma de Envio'); slc_TipoTar.value=0;} else this.form.submit()">
      			<option value="0">&lt;&lt; seleccione &gt;&gt;</option>
      			<option value="1" <?php ($_POST['slc_TipoTar'] == '1')? print "selected" : print "" ?>>Nacional</option>
      			<option value="2" <?php ($_POST['slc_TipoTar'] == '2')? print "selected" : print "" ?>>Internacional</option>
    		</select>
    	</td>
	</tr>
	<tr bordercolor="#FFFFFF">
	    <td align="center" valign="middle" class="titulos2">3.</td>
	    <td align="left" class="titulos2">Seleccione Tarifa </td>
	    <td colspan="4" class="listado2">
<?		// Listamos las tarifas.

		if ($_POST['slc_TipoTar'] > 0)
		{	
		    if ($rsclta && !$rsclta->EOF) {
    		    echo $rsclta->GetMenu2('id_clta',false,"0:&lt;&lt; SELECCIONE &gt;&gt;",false,0," id=\"id_clta\" onchange=\"Actualiza()\" class='select'");
    		    $rsclta->Close();
		    }
		}
		else echo "<select name='id_clta' id='id_clta' class='select'></select>";
?>
		</td>
	</tr>
	<tr bordercolor="#FFFFFF">
	    <td width="5%" rowspan="5" align="center" valign="middle" class="titulos2">4.</td>
	    <td width="26%" class="titulos2">C&oacute;digo</td>
	    <td colspan="4" align="center" class="listado2">
			<input name="txt_idTar" id="txt_idTar" type="text" size="5" maxlength="5">
	    </td>
	</tr>
	<tr bordercolor="#FFFFFF">
	    <td width="26%" rowspan="2" class="titulos2">Peso</td>
	    <td width="16%" align="center" class="titulos2"> L&iacute;mite Inferior</td>
	    <td width="16%" align="center" class="titulos2">L&iacute;mite Superior </td>
	    <td colspan="2" align="center" class="titulos2">Descripci&oacute;n</td>
	</tr>
	<tr bordercolor="#FFFFFF">
		<td width="16%" class="listado2">
			<font style='text-align: center'><input name="txt_lim1" id="txt_lim1" type="text" size="5" maxlength="5" required></font>
		</td>
		<td class="listado2">
			<font style='text-align: center'><input name="txt_lim2" id="txt_lim2" type="text" size="5" maxlength="5" required></font>
		</td>
		<td colspan="2" align="center" class="listado2">
			<input name="txt_desc" id="txt_desc" type="text" size="50" maxlength="150" required>
		</td>
	</tr>
	<tr bordercolor="#FFFFFF">
		<td width="26%" rowspan="2" class="titulos2">Valor Envio</td>
		<td align="center" class="titulos2">Urbano / Zona 1</td>
		<td align="center" class="titulos2">Regional / Zona 2</td>
		<td align="center" class="titulos2">Nacional / Zona 3</td>
		<td align="center" class="titulos2">Especiales / --</td>
	</tr>
	<tr bordercolor="#FFFFFF">
		<td class="listado2">
			<center><input name="txt_v1" id="txt_v1" type="text" size="6" maxlength="6"></center>
		</td>
		<td class="listado2">
			<center><input name="txt_v2" id="txt_v2" type="text" size="6" maxlength="6"></center>
		</td>
		<td class="listado2">
			<center><input name="txt_v3" id="txt_v3" type="text" size="6" maxlength="6"></center>
		</td>
		<td class="listado2">
			<center><input name="txt_v4" id="txt_v4" type="text" size="6" maxlength="6"></center>
		</td>
	</tr>
	<tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
		<td colspan="6"><font color="Gray">
			<b>NOTA: </b> El valor del Envio es relacional al punto 2, Si &eacute;ste es a nivel Nacional entonces los valores ser&aacute;n
			Local y Nacional; sino (internacional) Grupo 1 y Grupo 2 se refiere al valor en caso que el pa&iacute;s destino se encuentre o no
			en Am&eacute;rica respectivamente.
			</font></td>
	</tr>
<?	if ($error)
	{	echo("<tr><td colspan='5'>");
		// Implementado por si desean mostrar errores o mensajes personalizados.
		switch ($error) {
		case 1:	echo "Informaci&oacute;n actualizada!!";break;													//ACUTALIZACION REALIZADA
		case 2:	echo "Tarifa creada satisfactoriamente!!";break;										//INSERCION REALIZADA
		case 3:	echo "Error al gestionar datos, comun&iacute;quese con el Administrador de sistema !!";break;	//ERROR EJECUCCI?N SQL
		case 4: echo "<blink>Tarifa eliminada exitosamente</blink>";break;								// EXITO EN LA ELIMINACIN
		}
		echo("</td></tr>");
	}
	?>
</table>
<table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas">
<tr bordercolor="#FFFFFF">
	<td width="10%" class="listado2">&nbsp;</td>
	<td width="20%"  class="listado2">
		<span class="celdaGris"><center>
		<input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado('tarifas');">
		</center></span>	</td>
	<td width="20%" class="listado2">
		<span class="e_texto1"><center>
		<input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Agregar" onClick="document.form1.hdBandera.value='A'; return validarinfo(this.form);">
		</center></span>	</td>
	<td width="20%" class="listado2">
		<span class="e_texto1"><center>
		<input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Modificar" onClick="document.form1.hdBandera.value='M'; return validarinfo(this.form);">
		</center></span>	</td>
	<td width="20%" class="listado2">
		<span class="e_texto1"><center>
		  <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Eliminar" onClick="document.form1.hdBandera.value='E'; return validarinfo(this.form);">
		  </center></span>	</td>
	<td width="10%" class="listado2">&nbsp;</td>
</tr>
</table>
</form>
</body>
</html>