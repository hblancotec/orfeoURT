<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "../../sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}

if ($_SESSION['usua_admin_sistema'] != 1) {
	die(include "../../sinpermiso.php");
	exit;
}

$ruta_raiz = "../..";
include "$ruta_raiz/config.php";   // incluir configuracion.
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php';
$error = 0;
$msg = "";
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);

    //Creamos un vector con las opciones
    $vec_ppal[0] = array("&lt; Seleccione &gt;", "", "", "");
    $vec_ppal[1] = array("Sectores", "SGD_CAU_CAUSAL", "SGD_CAU_CODIGO", "SGD_CAU_DESCRIP", "SGD_CAU_ESTADO");
    $vec_ppal[2] = array("Motivos de Devoluci&oacute;n", "SGD_DEVE_DEV_ENVIO", "SGD_DEVE_CODIGO", "SGD_DEVE_DESC", "SGD_DEVE_ESTADO");
    $vec_ppal[3] = array("Info. Poblacional", "SGD_INF_INFPOB", "ID_INFPOB", "SGD_INFPOB_DESC", "SGD_INFPOB_ACTIVO");

    //  $vec_ppal[6]= array("Notificacion","SGD_NOT_DESCRIP","SGD_NOT_CODI","SGD_NOT_NOTIFICACION");
     
    //Generamos el combo a mostrar
    $opc_cmb = "";
    foreach ($vec_ppal as $key => $vlr) {
        ($_POST['slc_ppal'] == $key) ? $slc = 'selected' : $slc = '';
        $opc_cmb .= "<option value='$key' $slc>" . $vlr[0] . "</option>";
    }

    switch ($_POST['slc_ppal']) {
        case 1: {
                include($ruta_raiz . '/include/class/causales.class.php');
                $obj_tmp = new Causales($conn);
            }break;
        case 2: {
                include($ruta_raiz . '/include/class/medioDevoluciones.class.php');
                $obj_tmp = new MedDevolucion($conn);
            }break;
        case 3: {
                include $ruta_raiz . '/include/class/InformacionPoblacional.class.php';
                $obj_tmp = new InformacionPoblacional($conn);
            }
            break;
        default:
            break;
    }

    if (isset($_POST['btn_accion'])) {
        switch ($_POST['btn_accion']) {
            Case 'Agregar': {
                    $sql = "insert into " . $vec_ppal[$_POST['slc_ppal']][1] . "(" . $vec_ppal[$_POST['slc_ppal']][2] . "," . $vec_ppal[$_POST['slc_ppal']][3] . "," . $vec_ppal[$_POST['slc_ppal']][4] . ") ";
                    $sql.= "values (" . $_POST['txtId'] . ",'" . $_POST['txtModelo'] . "' , " . $_POST['cmb_estado'] . ")";
                    $conn->Execute($sql) ? $error = 3 : $error = 2;
                }break;
            Case 'Modificar': {
                    $sql = "update " . $vec_ppal[$_POST['slc_ppal']][1] . " set " . $vec_ppal[$_POST['slc_ppal']][3] . " = '" . $_POST['txtModelo'] . "', ";
                    $sql.= $vec_ppal[$_POST['slc_ppal']][4] . "=" . $_POST['cmb_estado'];
                    $sql.= " where " . $vec_ppal[$_POST['slc_ppal']][2] . "=" . $_POST['txtId'];
                    $conn->Execute($sql) ? $error = 4 : $error = 2;
                }break;
            Case 'Eliminar': {
                    $ok = $obj_tmp->SetDelDatos($_POST['slc_cmb2']);
                    ($ok == 0) ? $error = 5 : (($ok) ? $error = null : $error = 2);
                }break;
        }
    }
    unset($record);

    switch ($_POST['slc_ppal']) {
        case 1: {
                $slc_tmp = $obj_tmp->Get_ComboOpc(true, true);
                //$vec_tmp = $obj_tmp->Get_ArrayDatos();
                $ver = 'cau';
            }break;
        case 2: {
                $slc_tmp = $obj_tmp->Get_ComboOpc(true, true, false);
                //$vec_tmp = $obj_tmp->Get_ArrayDatos();
                $ver = 'mdd';
            }break;
        case 3: {
                $slc_tmp = $obj_tmp->Get_ComboOpc(true, true);
                //$vec_tmp = $obj_tmp->Get_ArrayDatos();
                $ver = 'raza';
            }break;
        default: {
                $slc_tmp = "<select name='slc_cmb2' id='slc_cmb2' class='select' ><option value='0' selected>&lt;&lt; Seleccione la Tabla &gt;&gt;</option></select>";
                $ver = false;
            }break;
    }
} else {
    $error = 1;
}

if ($error) {
    $msg = '<tr bordercolor="#FFFFFF" id="trMessage" name="trMessage">
			<td width="100%" align="center" class="titulosError" colspan="6" bgcolor="#FFFFFF">';
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
    	<script language="JavaScript" src="../../pqr2/js/jquery-1.7.1.js"></script>
        <script language="JavaScript" type="text/javascript">

        function Filtrar()
        {
        	var trMessage = document.getElementById('trMessage');
			if (trMessage) {
				trMessage.style.display = 'none';
			}

			document.getElementById('txtModelo').value = "";
            document.getElementById('txtId').value = "";
            document.getElementById('cmb_estado').value = "";
            document.getElementById('txtDesc').value = "";

            var cmbTipo = document.getElementById('selTipo');
            if (cmbTipo) {
            	for(var i = 0; i < cmbTipo.length; i++) {
					cmbTipo.options[i].selected = false;
				}
            }
            
			var Obj = document.getElementById('cmb_origen');
            var i = Obj.selectedIndex;
            if (Obj)
            {
            	var parametros = {
                		"filtrar" : 1,
                    	"origen" : Obj.value
                	};

            	$.ajax({
            		url: 'consulta.php',
            		type: 'POST',
            		cache: false,
            		async: false,
            		data:  parametros,
            		success: function(text) {
            			var tdc = document.getElementById('tdregistro');
            			if (tdc) {
            				$("#tdregistro").html(text);
            			}
            		},
            		error: function(text) { alert('Se ha producido un error ' + text); }
            	});
            }
        }
        
            function Actual(idtabla)
            {
				var trMessage = document.getElementById('trMessage');
				if (trMessage) {
					trMessage.style.display = 'none';
				}
                
                var Obj = document.getElementById('slc_cmb2');
                var i = Obj.selectedIndex;               
                if (Obj.value == '')
                {
                    document.getElementById('txtModelo').value = '';
                    document.getElementById('txtId').value = '';
                    document.getElementById('cmb_estado').value = '';
                    document.getElementById('txtDesc').value = '';
                }
                else
                {
                    var parametros = {
                    	"datos" : 1,
                        "codigo" : Obj.value,
                        "idtabla" : idtabla
                    };

                	$.ajax({
                		url: 'consulta.php',
                		type: 'POST',
                		cache: false,
                		async: false,
                		data:  parametros,
                		success: function(text) {
                			var myObj = JSON.parse(text);
                			if (myObj.length > 0) {
                				document.getElementById('txtModelo').value = myObj[0]['NOMBRE'];
                                document.getElementById('txtId').value = myObj[0]['ID'];
                                document.getElementById('cmb_estado').value = myObj[0]['ESTADO'];
                                document.getElementById('txtDesc').value = myObj[0]['DESCRIPCION'];
                			} else {
                				alert("Error en el proceso, consulte el administrador del sistema." + text);
                			} 
                		},
                		error: function(text) { alert('Se ha producido un error ' + text); }
                	});
                	
                    /*document.getElementById('txtModelo').value = vt[i - 1]['NOMBRE'];
                    document.getElementById('txtId').value = Obj.value;
                    document.getElementById('cmb_estado').value = vt[i - 1]['ESTADO'];
                    document.getElementById('txtDesc').value = vt[i - 1]['DESCRIPCION'];*/

                    var cmbTipo = document.getElementById('selTipo');
                    if (cmbTipo) {
                    	for(var i = 0; i < cmbTipo.length; i++) {
							cmbTipo.options[i].selected = false;
						}
                        
                        var parametros = {
                    		"consulta" : 1,
                        	"mrec_codi" : Obj.value
                    	};
                    			
                    	$.ajax({
                    		url: 'consulta.php',
                    		type: 'POST',
                    		cache: false,
                    		async: false,
                    		data:  parametros,
                    		success: function(text) {
                    			var myObj = JSON.parse(text);
                    			if (myObj.length > 0) {
                    				for(var j = 0; j < myObj.length; j++) {
                        				for(var i = 0; i < cmbTipo.length; i++) {
        									if(cmbTipo.options[i].value.charAt(0) == myObj[j]) {
        										cmbTipo.options[i].selected = true;
        									}
      									}
                    				}
                    			} else {
                    				alert("Error en el proceso, consulte el administrador del sistema." + text);
                    			} 
                    		},
                    		error: function(text) { alert('Se ha producido un error ' + text); }
                    	});
                    }
                }
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
                    window.open('listados.php?var=<?= $ver ?>', '', 'scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
    <?php
} else {
    echo "alert('Debe seleccionar una Opcion.');";
}
?>
            }

//-->
        </script>

        <title>.: Orfeo :. Admon de tablas sencillas.</title>
        <link href="<?= $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form name="form1" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="hdBandera" id="hdBandera" value="">
            <table width="75%" border="1" align="center" cellspacing="0" class="tablas">
                <tr bordercolor="#FFFFFF">
                    <td colspan="3" height="40" align="center" class="titulos4" valign="middle"><b><span class=etexto>ADMINISTRADOR DE TABLAS SENCILLAS</span></b></td>
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
                        <td class="listado2"><input name="txtId" id="txtId" type="text" size="10" maxlength="4"></td>
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
            </table>
            <table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas">
                <tr bordercolor="#FFFFFF">
                    <td width="10%" class="listado2">&nbsp;</td>
                    <td width="20%"  class="listado2" style="text-align: center; margin-left: auto; margin-right: auto">
                        <span class="e_texto1">
                            <input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado();">
                        </span>
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
               <?php echo $msg; ?>
            </table>
            <?php 
            
            ?>
            
        </form>

        <script ID="clientEventHandlersJS" type="text/javascript" LANGUAGE="JavaScript">
            <!--
            function ValidarInformacion(valor)
            {
                var strMensaje = "Por favor ingrese las datos.";
                //alert("Hola valor " + valor);
                document.getElementById('hdBandera').value = valor.substr(0, 1);
                //alert("Bandera " + hdBandera);

                if (document.form1.slc_ppal.value == "0")
                {
                    alert("Debe seleccionar el registro.\n" + strMensaje);
                    document.form1.idcont.focus();
                    return false;
                }

                if (rightTrim(document.form1.txtId.value) <= "0")
                {
                    alert("Debe ingresar el Codigo.\n" + strMensaje);
                    document.form1.txtIdPais.focus();
                    return false;
                }
                else if (isNaN(document.form1.txtId.value))
                {
                    alert("El Codigo debe ser numerico.\n" + strMensaje);
                    document.form1.txtIdPais.select();
                    document.form1.txtIdPais.focus();
                    return false;
                }

                if (document.form1.hdBandera.value == "A")
                {
                    if (rightTrim(document.form1.txtModelo.value) == "")
                    {
                        alert("Debe ingresar Nombre.\n" + strMensaje);
                        document.form1.txtModelo.focus();
                        return false;
                    } else if (rightTrim(document.form1.cmb_estado.value) == "")
                    {
                        alert("Debe seleccionar Estado.\n" + strMensaje);
                        document.form1.txtModelo.focus();
                        return false;
                    } else {
                        document.form1.submit();
                    }
                }
                else if (document.form1.hdBandera.value == "M")
                {
                    if (rightTrim(document.form1.txtModelo.value) == "")
                    {
                        alert("Primero debe seleccionar el registro a modificar.\n" + strMensaje);
                        return false;
                    }
                    else if (document.form1.txtId.value != document.form1.slc_cmb2.value)
                    {
                        alert('No se puede modificar el codigo');
                        document.form1.txtId.focus();
                        return false;
                    }
                    else
                    {
                        document.form1.submit();
                    }
                }
                else if (document.form1.hdBandera.value == "E")
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
            }
//-->
        </script>
    </body>
</html>