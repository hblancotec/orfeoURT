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

if ($_SESSION['usua_admin_sistema'] != '1')
    die("Mal acceso al m&oacute;dulo.");
$ruta_raiz = "../..";
include($ruta_raiz . '/config.php');                      // incluir configuracion.
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);
if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    //$conn->debug=true;
    include($ruta_raiz . '/include/class/mime.class.php');
    $obj_tmp = new Mime($conn);
    $ver = 'mime';

    if (isset($_POST['btn_accion'])) {
        switch ($_POST['btn_accion']) {
            Case 'Agregar': {
                    $sql = "insert into ANEXOS_TIPO(ANEX_TIPO_CODI, ANEX_TIPO_DESC, ANEX_TIPO_EXT, ANEX_TIPO_PQR, ANEX_PERM_TIPIF_ANEXO) ";
                    $sql.= "values (" . $_POST['txtId'] . ",'" . $_POST['txtDesc'] . "','" . $_POST['txtUsua'] . "'," . $_POST['slcHPQR'] . "," .$_POST['slcORDANEX']. ")";
                    $conn->Execute($sql) ? $error = 3 : $error = 2;
                }break;
            Case 'Modificar': {
                    $sql = "update ANEXOS_TIPO set ANEX_TIPO_DESC = '" . $_POST['txtDesc'] . "', ANEX_TIPO_EXT = '" . $_POST['txtUsua'] . 
                            "', ANEX_TIPO_PQR = " . $_POST['slcHPQR'].", ANEX_PERM_TIPIF_ANEXO=". $_POST['slcORDANEX'];
                    $sql.= " where ANEX_TIPO_CODI = " . $_POST['txtId'];
                    $conn->Execute($sql) ? $error = 4 : $error = 2;
                }break;
            Case 'Eliminar': {
                    $ok = $obj_tmp->SetDelDatos($_POST['slc_cmb2']);
                    ($ok == 0) ? $error = 5 : (($ok) ? $error = null : $error = 2);
                }break;
            Default: break;
        }
        unset($record);
    }
    $slc_tmp = $obj_tmp->Get_ComboOpc(true, true);
    $vec_tmp = $obj_tmp->Get_ArrayDatos();
} else {
    $error = 1;
}

/**
 *       Funcion que convierte un valor de PHP a un valor Javascript.
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

/**
 *       Funcion que convierte un vector de PHP a un vector Javascript.
 *       Utiliza a su vez la funcion valueToJsValue.
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
    }
    $msg .= '</td></tr>';
}
?>
<html>
    <head>
        <script type="text/javascript" language="JavaScript">
        <!--
            function Actual()
            {
                var Obj = document.getElementById('slc_cmb2');
                var i = Obj.selectedIndex;
                var chk = Obj.value;

                document.getElementById('txtDesc').value = vt[i]["NOMBRE"];
                document.getElementById('txtId').value = vt[i]["ID"];
                document.getElementById('txtUsua').value = vt[i]["USUARIO"];
                document.getElementById('slcHPQR').value = vt[i]["PQR"];
                document.getElementById('slcORDANEX').value = vt[i]["ORDANEX"];
            }

            function rightTrim(sString)
            {
                while (sString.substring(sString.length - 1, sString.length) == ' ')
                {
                    sString = sString.substring(0, sString.length - 1);
                }
                return sString;
            }

            function ver_listado()
            {
<?php
if ($ver) {
    ?>
                    window.open('../tbasicas/listados.php?var=<?= $ver ?>', '', 'scrollbars=yes,menubar=no,height=550,width=500,resizable=yes,toolbar=no,location=no,status=no');
    <?php
} else {
    echo "alert('Debe seleccionar una Opcion.');";
}
?>
            }
<?php
echo arrayToJsArray($vec_tmp, 'vt');
?>
            function ValidarInformacion()
            {
                if (document.form1.hdBandera.value == "A")
                {
                    if (rightTrim(document.form1.txtId.value) > "0")
                    {
                        if (isNaN(document.form1.txtId.value))
                        {
                            alert("El Codigo debe ser numerico.");
                            return false;
                        }
                        else if (rightTrim(document.form1.txtDesc.value) == "")
                        {
                            alert("Debe ingresar descripcion.");
                            return false;
                        }
                        else if (rightTrim(document.form1.txtUsua.value) == "")
                        {
                            alert("Debe ingresar Usuario.");
                            return false;
                        }
                        else
                            document.form1.submit();
                    }
                    else
                    {
                        alert("Debe ingresar el Codigo.");
                        return false;
                    }
                }
                else if (document.form1.hdBandera.value == "M")
                {
                    if (rightTrim(document.form1.txtDesc.value) == "")
                    {
                        alert("Primero debe seleccionar el registro a modificar.");
                        return false;
                    }
                    else if (document.form1.txtId.value != document.form1.slc_cmb2.value)
                    {
                        alert('No se puede modificar el codigo');
                        return false;
                    }
                    else
                    {
                        document.form1.submit();
                    }
                }
                else if (document.form1.hdBandera.value == "E")
                {
                    if (document.form1.slc_cmb2.value != "0")
                    {
                        if (confirm("Esta seguro de borrar este registro ?\n"))
                        {
                            document.form1.submit();
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else {
                        alert('Debe seleccionar registro');
                        return false;
                    }
                }
            }
        //-->
        </script>
        <title>Orfeo - Admor Tipos de Archivo.</title>
        <link href="<?= $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form name="form1" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="hdBandera" value="">
            <table width="75%" border="1" align="center" cellspacing="0" class="tablas">
                <tr bordercolor="#FFFFFF">
                    <td colspan="7" height="40" align="center" class="titulos4" valign="middle"><b><span class=etexto>ADMINISTRADOR TIPOS DE ARCHIVO</span></b></td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="center" class="titulos2"><b>1.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Registro</b></td>
                    <td align="left" class="listado2">
<?= $slc_tmp ?>
                    </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td rowspan="5" valign="middle" class="titulos2" align="center">2.</td>
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese c&oacute;digo</b></td>
                    <td align="center" class="listado2"><input name="txtId" id="txtId" type="text" size="5" maxlength="2" required></td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese Descripci&oacute;n</b></td>
                    <td class="listado2""><input name="txtDesc" id="txtDesc" type="text" size="50" maxlength="50" required></td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese Extensi&oacute;n</b></td>
                    <td class="listado2"><input name="txtUsua" id="txtUsua" type="text" size="5" maxlength="5" required></td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="left" class="titulos2"><b>&nbsp;Habilitado PQR ?</b></td>
                    <td class="listado2">
                        <select name="slcHPQR" id="slcHPQR" required>
                            <option value="">&nbsp;</option>
                            <option value="1">S I</option>
                            <option value="0">N O</option>
                        </select>
                    </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="left" class="titulos2"><b>&nbsp;Permite Tipificar/Borrar como Anexo ?</b></td>
                    <td class="listado2">
                        <select name="slcORDANEX" id="slcORDANEX" required>
                            <option value="">&nbsp;</option>
                            <option value="1">S I</option>
                            <option value="0">N O</option>
                        </select>
                    </td>
                </tr>
            </table>
            <table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas">
                <tr bordercolor="#FFFFFF">
                    <td width="10%" class="listado2">&nbsp;</td>
                    <td width="20%"  class="listado2" align="center">
                        <span class="celdaGris"> <span class="e_texto1">
                                <input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado();">
                            </span></span>
                    </td>
                    <td width="20%" class="listado2" align="center">
                        <span class="e_texto1">
                            <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Agregar" onClick="document.form1.hdBandera.value = 'A';
        return ValidarInformacion();">
                        </span>
                    </td>
                    <td width="20%" class="listado2" align="center">
                        <span class="e_texto1">
                            <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Modificar" onClick="document.form1.hdBandera.value = 'M';
        return ValidarInformacion();">
                        </span>
                    </td>
                    <td width="20%" class="listado2" align="center">
                        <span class="e_texto1">
                            <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Eliminar" onClick="document.form1.hdBandera.value = 'E';
        return ValidarInformacion();">
                        </span>
                    </td>
                    <td width="10%" class="listado2">&nbsp;</td>
                </tr>
            </table>
            <table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas"><?php echo $msg ?></table>
        </form>
    </body>
</html>