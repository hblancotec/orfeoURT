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

$ruta_raiz = "../..";
include($ruta_raiz . '/config.php');   
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php'; 
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);
if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    //$conn->debug=true;
    //Creamos un vector con las opciones
    $vec_ppal[0] = array("&lt; Seleccione &gt;", "", "", "");
    $vec_ppal[1] = array("Metodos publicados", "METODOS_WS", "COD_METODO", "NOMBRE", "ESTADO");
    $vec_ppal[2] = array("Acciones Externas", "SGD_ACCIONES_EXTERNAS", "SGD_ACCION_CODIGO", "SGD_ACCION_DESCRIPCION", "SGD_ACCION_ESTADO","SGD_APLI_CODIGO");
    $vec_ppal[3] = array("Campos Externos", "SGD_CAMPOS_APPEXT", "SGD_COD_CAMPOEXT", "SGD_NOMBRE_CAMPO", "SGD_ESTADO_CAMPO","SGD_APLI_CODIGO");
    $opc_cmb = "";
    foreach ($vec_ppal as $key => $vlr) {
        ($_POST['slc_ppal'] == $key) ? $slc = 'selected' : $slc = '';
        $opc_cmb .= "<option value='$key' $slc>" . $vlr[0] . "</option>";
    }

    if (isset($_POST['btn_accion'])) {
        switch ($_POST['btn_accion']) {
            Case 'Agregar': {
                    $sql = "insert into " . $vec_ppal[$_POST['slc_ppal']][1] . "(" . $vec_ppal[$_POST['slc_ppal']][2] . "," . $vec_ppal[$_POST['slc_ppal']][3]. "," . $vec_ppal[$_POST['slc_ppal']][4] .(($_POST['slc_ppal']>1)? ",".$vec_ppal[$_POST['slc_ppal']][5]:""). ") ";
                    $sql.= "values (" . $_POST['txtId'] . ",'" . $_POST['txtModelo'] . "' , ". $_POST['cmb_estado'].(($_POST['slc_ppal']>1)? ",".$_POST['app_codigo']:"") .")";
                    $conn->Execute($sql) ? $error = 3 : $error = 2;
                }break;
            Case 'Modificar': {
                    $sql = "update " . $vec_ppal[$_POST['slc_ppal']][1] . " set " . $vec_ppal[$_POST['slc_ppal']][3] . " = '" . $_POST['txtModelo'] . "', ";
                    $sql.= $vec_ppal[$_POST['slc_ppal']][4] . "=" . $_POST['cmb_estado']. (($_POST['slc_ppal']>1)? ", SGD_APLI_CODIGO=".$vec_ppal[$_POST['slc_ppal']][5]:"");
                    $sql.= " where " . $vec_ppal[$_POST['slc_ppal']][2] . "=" . $_POST['txtId'];
                    $conn->Execute($sql) ? $error = 4 : $error = 2;
                }break;
            Case 'Eliminar': {
                    $sql = "delete from ". $vec_ppal[$_POST['slc_ppal']][1];
                    $sql.= " where " . $vec_ppal[$_POST['slc_ppal']][2] . "=" . $_POST['txtId'];
                    $conn->Execute($sql) ? $error = 6 : $error = 5;
                }break;
        }
    }
    unset($record);
    if(isset($_POST['slc_ppal'])){
        include($ruta_raiz . '/include/class/enlaceAplicativos.class.php');
        $obj_tmp=new enlaceAplicativos($conn);
        switch ($_POST['slc_ppal']) {
            case 1: {
                    $slc_tmp = $obj_tmp->getComboMetodos(true,true);
                    $vec_tmp = $obj_tmp->getArrayMetodos();
                    $ver = 'metodosWS';
                }break;
            case 2: {
                    $opc_aplic= $obj_tmp->getAplicaciones(true, true,$_POST['app_codigo']);
                    if(isset($_POST['app_codigo'])){
                        $slc_tmp = $obj_tmp->getComboAccionesExt(true, true,$_POST['app_codigo']);
                        $vec_tmp = $obj_tmp->getArrayAccionesExt($_POST['app_codigo']);
                        
                    }$ver = 'accionesext';
                }break;
             case 3: {
                    $opc_aplic= $obj_tmp->getAplicaciones(true, true,$_POST['app_codigo']);
                    if(isset($_POST['app_codigo'])){
                        $slc_tmp = $obj_tmp->getComboCamposExt(true, true,$_POST['app_codigo']);
                        $vec_tmp = $obj_tmp->getArrayCamposExt($_POST['app_codigo']);
                        
                    }$ver = 'camposext';
                }break;
                
              default: {
                
                $ver = false;
             }break;  
           }
        
    }
 ($slc_tmp)?0:   $slc_tmp = "<select name='slc_cmb2' id='slc_cmb2' class='select' ><option value='0' selected>&lt;&lt; Seleccione la Tabla &gt;&gt;</option></select>";
    
} else {
    $error = 1;
}

/*
 * 	Funcion que convierte un valor de PHP a un valor Javascript.
 */

function valueToJsValue($value, $encoding = false) {
    if (!is_numeric($value)) {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('"', '\"', $value);
        $value = '"' . $value . '"';
    }
    if ($encoding) {
        switch ($encoding) {
            case 'utf8' : return iconv("ISO-8859-2", "UTF-8", $value);
                break;
        }
    } else {
        return $value;
    }
    return;
}

/*
 * 	Funcion que convierte un vector de PHP a un vector Javascript.
 * 	Utiliza a su vez la funcion valueToJsValue.
 */

function arrayToJsArray($array, $name, $nl = "\n", $encoding = false) {
    if (is_array($array)) {
        $jsArray = $name . ' = new Array();' . $nl;
        foreach ($array as $key => $value) {
            switch (gettype($value)) {
                case 'unknown type':
                case 'resource':
                case 'object': break;
                case 'array': $jsArray .= arrayToJsArray($value, $name . '[' . valueToJsValue($key, $encoding) . ']', $nl);
                    break;
                case 'NULL': $jsArray .= $name . '[' . valueToJsValue($key, $encoding) . '] = null;' . $nl;
                    break;
                case 'boolean': $jsArray .= $name . '[' . valueToJsValue($key, $encoding) . '] = ' . ($value ? 'true' : 'false') . ';' . $nl;
                    break;
                case 'string': $jsArray .= $name . '[' . valueToJsValue($key, $encoding) . '] = ' . valueToJsValue($value, $encoding) . ';' . $nl;
                    break;
                case 'double':
                case 'integer': $jsArray .= $name . '[' . valueToJsValue($key, $encoding) . '] = ' . $value . ';' . $nl;
                    break;
                default: trigger_error('Hoppa, egy j t�us a PHP-ben?' . __CLASS__ . '::' . __FUNCTION__ . '()!', E_USER_WARNING);
            }
        }
        return $jsArray;
    } else {
        return false;
    }
}

if ($error) {
    $msg = '<tr bordercolor="#FFFFFF">
			<td width="3%" align="center" class="titulosError" colspan="3" bgcolor="#FFFFFF">';
    switch ($error) {
        case 1: //NO CONECCION A BD
            $msg .= "Error al conectar a BD, comun&iacute;quese con el Administrador de sistema !!";
            break;
        case 2: //ERROR EJECUCCION SQL
            $msg .= "Error al gestionar datos, comun&iacute;quese con el Administrador de sistema !!";
            break;
        case 3: //INSERCION REALIZADA
            $msg .= "Creaci&oacute;n exitosa!";
            break;
        case 4: //MODIFICACION REALIZADA
            $msg .= "Registro actualizado satisfactoriamente!!";
            break;
        case 5: //IMPOSIBILIDAD DE ELIMINAR REGISTRO
            $msg .= "No se puede eliminar registro, tiene dependencias internas relacionadas.";
            break;
        case 6: //IMPOSIBILIDAD DE ELIMINAR REGISTRO
            $msg .= "El registro se ha eliminado correctamente.";
            break;
    }
    $msg .= '</td></tr>';
}
?>
<html>
    <head>
        <script language="JavaScript" type="text/javascript">
            <!--
            function Actual()
            {
                var Obj = document.getElementById('slc_cmb2');
                var i = Obj.selectedIndex;
                if (Obj.value == '')
                {
                    document.getElementById('txtModelo').value = '';
                    document.getElementById('txtId').value = '';
                    document.getElementById('cmb_estado').value = '';
                }
                else
                {
                    document.getElementById('txtModelo').value = vt[i-1]['NOMBRE'];
                    document.getElementById('txtId').value = Obj.value;
                    document.getElementById('cmb_estado').value = vt[i-1]['ESTADO'];
                }
            }

            function rightTrim(sString)
            {	while (sString.substring(sString.length-1, sString.length) == ' ')
                {	sString = sString.substring(0,sString.length-1);  }
                return sString;
            }

            function ver_listado()
            {
<?php
if ($ver) {
    ?>
            window.open('listados.php?var=<?= $ver ?>','','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
    <?php
} else {
    echo "alert('Debe seleccionar una Opcion.');";
}
?>
    }

<?php
echo arrayToJsArray($vec_tmp, 'vt');
?>
    //-->
        </script>

        <title>.: Orfeo :. Admor de tablas sencillas.</title>
        <link href="<?= $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form name="form1" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="hdBandera" id="hdBandera" value="">
            <table width="75%" border="1" align="center" cellspacing="0" class="tablas">
                <tr bordercolor="#FFFFFF">
                    <td colspan="3" height="40" align="center" class="titulos4" valign="middle"><b><span class=etexto>2. TABLAS B&Aacute;SICAS</span></b></td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td width="3%" align="center" class="titulos2"><b>1.</b></td>
                    <td width="25%" align="left" class="titulos2"><b>&nbsp;Seleccione la tabla</b></td>
                    <td width="72%" class="listado2">
                        <SELECT name="slc_ppal" id="slc_ppal" class="select" onchange="this.form.submit();">
                            <?= $opc_cmb ?>
                        </SELECT>
                    </td>
                </tr>
                <?php if($_POST['slc_ppal']>1){?>
                <tr bordercolor="#FFFFFF">
                    
                    <td width="25%" colspan="2" align="left" class="titulos2"><b>&nbsp;Seleccione aplicativo externo</b></td>
                    <td width="72%" class="listado2">
                            <?= $opc_aplic ?>
                    </td>
                </tr>
                <?php }?>
                <tr bordercolor="#FFFFFF">
                    <td align="center" class="titulos2"><b>2.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Registro</b></td>
                    <td align="left" class="listado2">
                        <?= $slc_tmp ?>
                    </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td rowspan="3" valign="middle" class="titulos2">3.</td>
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese c&oacute;digo</b></td>
                    <td class="listado2"><input name="txtId" id="txtId" type="text" size="10" maxlength="2"></td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese nombre</b></td>
                    <td class="listado2"><input name="txtModelo" id="txtModelo" type="text" size="50" maxlength="50"></td>
                </tr>
                <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;Seleccione Estado</b></td>
                <td class="listado2">
                	<select name="cmb_estado" id="cmb_estado" class="select" requiered>
					<option value="" selected>&lt; seleccione &gt;</option>
					<option value="1">ACTIVO</option>
					<option value="0">INACTIVO</option>
				</select>
				</td>
                </tr>
                <?php
                echo $msg;
                ?>
            </table>
            <table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas">
                <tr bordercolor="#FFFFFF">
                    <td width="10%" class="listado2">&nbsp;</td>
                    <td width="20%"  class="listado2">
                        <span class="e_texto1"><center>
                        <input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado();">
                        </center></span>
                    </td>
                    <td width="20%" class="listado2" style="text-align: center; margin-left: auto; margin-right: auto">
                        <span class="e_texto1">
                            <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Agregar" onClick="ValidarInformacion(this.value);">
                        </span>
                    </td>
                    <td width="20%" class="listado2" style="text-align: center; margin-left: auto; margin-right: auto">
                        <span class="e_texto1">
                            <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Modificar" onClick="ValidarInformacion(this.value);">
                        </span>
                    </td>
                    <td width="20%" class="listado2" style="text-align: center; margin-left: auto; margin-right: auto">
                        <span class="e_texto1">
                            <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Eliminar" onClick="ValidarInformacion(this.value);">
                        </span>
                    </td>
                    <td width="10%" class="listado2">&nbsp;</td>
                </tr>
            </table>
        </form>

        <script ID="clientEventHandlersJS" type="text/javascript" LANGUAGE="JavaScript">
            <!--
            function ValidarInformacion(valor)
            {	
                var strMensaje = "Por favor ingrese las datos.";
                document.getElementById('hdBandera').value = valor.subtr(0,1);

                if (document.form1.slc_ppal.value == "0")
                {	alert("Debe seleccionar el registro.\n" + strMensaje);
                    document.form1.idcont.focus();
                    return false;
                }

                if ( rightTrim(document.form1.txtId.value) <= "0")
                {	alert("Debe ingresar el Codigo.\n" + strMensaje);
                    document.form1.txtIdPais.focus();
                    return false;
                }
                else if(isNaN(document.form1.txtId.value))
                {	alert("El Codigo debe ser numerico.\n" + strMensaje);
                    document.form1.txtIdPais.select();
                    document.form1.txtIdPais.focus();
                    return false;
                }

                if (document.form1.hdBandera.value == "A")
                {	if(rightTrim(document.form1.txtModelo.value) == "")
                    {	alert("Debe ingresar Nombre.\n" + strMensaje);
                        document.form1.txtModelo.focus();
                        return false;
                    }else if (rightTrim(document.form1.cmb_estado.value) == "")
		                {	alert("Debe seleccionar Estado.\n" + strMensaje);
		                    document.form1.txtModelo.focus();
		                    return false;
		                } else {
			                document.form1.submit();
		                }
                }
                else if(document.form1.hdBandera.value == "M")
                {	if(rightTrim(document.form1.txtModelo.value) == "")
                    {	alert("Primero debe seleccionar el registro a modificar.\n" + strMensaje);
                        return false;
                    }
                    else if(document.form1.txtId.value != document.form1.slc_cmb2.value)
                    {
                        alert('No se puede modificar el codigo');
                        document.form1.txtId.focus();
                        return false;
                    }
                    else
                    {	document.form1.submit();
                    }
                }
                else if(document.form1.hdBandera.value == "E")
                {	if(confirm("Esta seguro de borrar este registro ?\n"))
                    {	document.form1.submit();	}
                    else
                    {	return false;	}
                }
            }
            //-->
        </script>
    </body>
</html>
