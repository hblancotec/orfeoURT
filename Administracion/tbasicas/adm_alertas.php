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
if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

if ($_SESSION['usua_admin_sistema'] != 1)	die(include "$ruta_raiz/sinacceso.php");
$ruta_raiz = "../..";
include('../../config.php'); 			// incluir configuracion.
$error = 0;
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
if ($db) {
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    if (isset($_POST['btn_accion'])) {
        switch ($_POST['btn_accion']) {
            Case 'Agregar': {
            	$tabla = "SGD_ALERTAS";
            	$varg = "val_trg";
            	$vard = "val_trd";
            	for ($i = 1; $i < 10; $i++) {
            		${$varg.$i} = 0;
            		${$vard.$i} = 0;
            	}
            	foreach ($_POST['slc_tradgen'] as $k => $v){
            		${"val_trg".$v} = 1;
            	}
            	foreach ($_POST['slc_traddet'] as $k => $v){
            		${"val_trd".$v} = 1;
            	}
            	$campos['SGD_NOMBR_ALER'] = $_POST['txt_name'];
            	$campos['SGD_TDOC_ALER'] = $_POST['slc_tdoc'];
            	$campos['SGD_ESTADO_ALER'] = $_POST['slcEstado'];
            	$campos['SGD_DIASTER_ALER'] = $_POST['txt_diast'];
            	$campos['SGD_DIASANT_ALER'] = $_POST['txt_diasa'];
            	$campos['SGD_DIASDES_ALER'] = $_POST['txt_diasd'];
            	$campos['SGD_USUADOC_ALER'] = $_POST['slc_user'];
            	//$campos['SGD_DEPE_ALER'] = $_POST[''];
            	$campos['SGD_TRAD1G_ALER'] = $val_trg1;
            	$campos['SGD_TRAD2G_ALER'] = $val_trg2;
            	$campos['SGD_TRAD3G_ALER'] = $val_trg3;
            	$campos['SGD_TRAD4G_ALER'] = $val_trg4;
            	$campos['SGD_TRAD5G_ALER'] = $val_trg5;
            	$campos['SGD_TRAD6G_ALER'] = $val_trg6;
            	$campos['SGD_TRAD7G_ALER'] = $val_trg7;
            	$campos['SGD_TRAD8G_ALER'] = $val_trg8;
            	$campos['SGD_TRAD9G_ALER'] = $val_trg9;
            	$campos['SGD_TRAD1D_ALER'] = $val_trd1;
            	$campos['SGD_TRAD2D_ALER'] = $val_trd2;
            	$campos['SGD_TRAD3D_ALER'] = $val_trd3;
            	$campos['SGD_TRAD4D_ALER'] = $val_trd4;
            	$campos['SGD_TRAD5D_ALER'] = $val_trd5;
            	$campos['SGD_TRAD6D_ALER'] = $val_trd6;
            	$campos['SGD_TRAD7D_ALER'] = $val_trd7;
            	$campos['SGD_TRAD8D_ALER'] = $val_trd8;
            	$campos['SGD_TRAD9D_ALER'] = $val_trd9;
				$sql = $db->conn->GetInsertSQL($tabla, $campos, $magicq=true, $force_type=false);
				$ok = $db->conn->Execute($sql);
                $ok ? $error = 3 : $error = 2;
                }break;
            Case 'Modificar': {
                $tabla = "SGD_ALERTAS";
            	$varg = "val_trg";
            	$vard = "val_trd";
            	for ($i = 1; $i < 10; $i++){
            		${$varg.$i} = 0;
            		${$vard.$i} = 0;
            	}
            foreach ($_POST['slc_tradgen'] as $k => $v){
            		${"val_trg".$v} = 1;
            	}
            	foreach ($_POST['slc_traddet'] as $k => $v){
            		${"val_trd".$v} = 1;
            	}
            	$campos['SGD_ID_ALER'] = $_POST['slc_aler'];
            	$campos['SGD_NOMBR_ALER'] = $_POST['txt_name'];
            	$campos['SGD_TDOC_ALER'] = $_POST['slc_tdoc'];
            	$campos['SGD_ESTADO_ALER'] = $_POST['slcEstado'];
            	$campos['SGD_DIASTER_ALER'] = $_POST['txt_diast'];
            	$campos['SGD_DIASANT_ALER'] = $_POST['txt_diasa'];
            	$campos['SGD_DIASDES_ALER'] = $_POST['txt_diasd'];
            	$campos['SGD_USUADOC_ALER'] = $_POST['slc_user'];
            	//$campos['SGD_DEPE_ALER'] = $_POST[''];
            	$campos['SGD_TRAD1G_ALER'] = $val_trg1;
            	$campos['SGD_TRAD2G_ALER'] = $val_trg2;
            	$campos['SGD_TRAD3G_ALER'] = $val_trg3;
            	$campos['SGD_TRAD4G_ALER'] = $val_trg4;
            	$campos['SGD_TRAD5G_ALER'] = $val_trg5;
            	$campos['SGD_TRAD6G_ALER'] = $val_trg6;
            	$campos['SGD_TRAD7G_ALER'] = $val_trg7;
            	$campos['SGD_TRAD8G_ALER'] = $val_trg8;
            	$campos['SGD_TRAD9G_ALER'] = $val_trg9;
            	$campos['SGD_TRAD1D_ALER'] = $val_trd1;
            	$campos['SGD_TRAD2D_ALER'] = $val_trd2;
            	$campos['SGD_TRAD3D_ALER'] = $val_trd3;
            	$campos['SGD_TRAD4D_ALER'] = $val_trd4;
            	$campos['SGD_TRAD5D_ALER'] = $val_trd5;
            	$campos['SGD_TRAD6D_ALER'] = $val_trd6;
            	$campos['SGD_TRAD7D_ALER'] = $val_trd7;
            	$campos['SGD_TRAD8D_ALER'] = $val_trd8;
            	$campos['SGD_TRAD9D_ALER'] = $val_trd9;
				$ok = $db->conn->Replace($tabla, $campos, 'SGD_ID_ALER', $autoquote = true);
                $ok ? $error = 4 : $error = 2;
                }break;
            Case 'Eliminar': {
            	$sql = "DELETE FROM SGD_ALERTAS WHERE SGD_ID_ALER=".$_POST['slc_aler'];
                $db->conn->Execute($sql);
                $ok = $db->conn->Affected_Rows();
                ($ok == 1) ? $error = 5 : $error = 2;
                }break;
            Default: break;
        }
        $txt_name = '';
       	$slc_tdoc = '';
       	$slcEstado = '';
       	$txt_diast = '';
       	$txt_diasa = '';
       	$txt_diasd = '';
       	$slc_user = '';
       	unset($slc_tradgen);
       	unset($slc_traddet);
    }
    
    $sql0 =	"SELECT SGD_NOMBR_ALER, SGD_ID_ALER, SGD_TDOC_ALER, SGD_ESTADO_ALER, SGD_DIASTER_ALER, ".
    		"SGD_DIASANT_ALER, SGD_DIASDES_ALER, SGD_USUADOC_ALER,".
    		"SGD_TRAD1G_ALER, SGD_TRAD2G_ALER, SGD_TRAD3G_ALER, SGD_TRAD4G_ALER, SGD_TRAD5G_ALER, ".
			"SGD_TRAD6G_ALER, SGD_TRAD7G_ALER, SGD_TRAD8G_ALER, SGD_TRAD9G_ALER, ".
		    "SGD_TRAD1D_ALER, SGD_TRAD2D_ALER, SGD_TRAD3D_ALER, SGD_TRAD4D_ALER, SGD_TRAD5D_ALER, ".
			"SGD_TRAD6D_ALER, SGD_TRAD7D_ALER, SGD_TRAD8D_ALER, SGD_TRAD9D_ALER FROM SGD_ALERTAS ";
    $sql2 = "ORDER BY SGD_NOMBR_ALER";
    $rs_aler = $db->conn->Execute($sql0.$sql2);
    if($_POST['slc_aler']){
    	unset($slc_tradgen);
       	unset($slc_traddet);
    	$rs_aler2 = $db->conn->Execute($sql0." WHERE SGD_ID_ALER=".$_POST['slc_aler']);
    	for ($i = 1; $i < 10; $i++){
    		$idk = "SGD_TRAD".$i."G_ALER";
    		$idh = "SGD_TRAD".$i."D_ALER";
			if ($rs_aler2->fields[$idk]==1) {
				$slc_tradgen[] = $i; 
			}
    		if ($rs_aler2->fields[$idh]==1) {
				$slc_traddet[] = $i; 
			}
    	}
    	//$txt_id = $rs_aler2->fields['SGD_ID_ALER'];
    	$slc_aler = $_POST['slc_aler'];
    	$slc_tdoc = $rs_aler2->fields['SGD_TDOC_ALER'];
    	$slc_user = $rs_aler2->fields['SGD_USUADOC_ALER'];
    	$txt_name = $rs_aler2->fields['SGD_NOMBR_ALER'];
    	$txt_diast= $rs_aler2->fields['SGD_DIASTER_ALER'];
    	$txt_diasa= $rs_aler2->fields['SGD_DIASANT_ALER'];
    	$txt_diasd= $rs_aler2->fields['SGD_DIASDES_ALER'];
    	if ($rs_aler2->fields['SGD_ESTADO_ALER']==0){
    		$off='selected'; $on='';
    	} else {
    		$off=''; $on='selected';
    	}
    } else {
    	$txt_name = '';
       	$slc_tdoc = '';
       	$slcEstado = '';
       	$txt_diast = '';
       	$txt_diasa = '';
       	$txt_diasd = '';
       	$slc_user = '';
       	unset($slc_tradgen);
       	unset($slc_traddet);
    }
    $cmb_aler = $rs_aler->GetMenu2('slc_aler', $slc_aler, ":&lt;&lt;SELECCIONE&gt;&gt;" , false, 0, 'id="slc_aler" class="select" onchange="this.form.submit();"');
    
    $sql = "SELECT SGD_TRAD_DESCR, SGD_TRAD_CODIGO FROM SGD_TRAD_TIPORAD ORDER BY SGD_TRAD_CODIGO";
    $rs_trad = $db->conn->cacheExecute(30, $sql);
    $cmb_tradg = $rs_trad->GetMenu2('slc_tradgen[]', $slc_tradgen, false, true, 10, 'id="slc_tradgen" class="select" required');
    $rs_trad->Move(0);
    $cmb_tradd = $rs_trad->GetMenu2('slc_traddet[]', $slc_traddet, false, true, 10, 'id="slc_traddet" class="select" required');
    
    $sql = "SELECT SGD_TPR_DESCRIP, SGD_TPR_CODIGO FROM SGD_TPR_TPDCUMENTO ORDER BY SGD_TPR_DESCRIP";
    $rs_tdoc = $db->conn->Execute($sql);
    $cmb_tdoc = $rs_tdoc->GetMenu2('slc_tdoc', $slc_tdoc, ":&lt;&lt;SELECCIONE&gt;&gt;" , false, 0, 'id="slc_tdoc" class="select" required');
    
    $sql = "SELECT D.DEPE_NOMB, D.DEPE_CODI FROM DEPENDENCIA D ORDER BY D.DEPE_CODI";
    $rs_depe = $db->conn->Execute($sql);
    $cmb_depe = $rs_depe->GetMenu2('slc_depe', false, ":&lt;&lt;SELECCIONE&gt;&gt;", false, false, 'id="slc_depe" class="select" required');
    
    //if ($_POST['slc_depe'] > 0) 
        $sql = "SELECT USUA_NOMB + ' (' + USUA_LOGIN + ')' AS USUA_NOMB, USUA_DOC FROM USUARIO WHERE USUA_ESTA=1";
        $rs_usu = $db->conn->Execute($sql);
        $cmb_user = $rs_usu->GetMenu2('slc_user', $slc_user, ":&lt;&lt;SELECCIONE&gt;&gt;", false, false, 'id="slc_user" class="select" required');
    //} else {
    //    $cmb_user = "<SELECT CLASS='select' required><OPTION VALUE=''>&lt;&lt;SELECCIONE DEPENDENCIA&gt;&gt;</OPTION></SELECT>";
    //}

    
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
			<td align="center" class="titulosError" colspan="6" bgcolor="#FFFFFF">';
    switch ($error) {
        case 1: //NO CONECCION A BD
            $msg .= "Error al conectar a BD, comun&iacute;quese con el Administrador de sistema !! $dsn";
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
            $msg .= "Eliminaci&oacute;n exitosa!!.";
            break;
    }
    $msg .= '</td></tr>';
}
?>
<html>
    <head>
        <script language="JavaScript">
            <!--
            function rightTrim(sString)
            {	while (sString.substring(sString.length-1, sString.length) == ' ')
                {	sString = sString.substring(0,sString.length-1);  }
                return sString;
            }

            function ver_listado()
            {
                window.open('listados.php?var=alt','','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
            }
            //-->
        </script>

        <title>.: Orfeo :. Admor de Aplicativos enlace a Orfeo.</title>
        <link href="<?= $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form name="frm_alertas" id="frm_alertas" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="hdBandera" value="">

            <TABLE WIDTH="85%" BORDER="1"  cellspacing="0" CLASS="tablas" ALIGN="center">
                <TR bordercolor="#FFFFFF">
                    <TD COLSPAN="6" HEIGHT="40" ALIGN="center" CLASS="titulos4" VALIGN="middle">
                        <b><span class=etexto>ADMINISTRADOR DE ALERTAS</span></b>
                    </TD>
                </TR>
                <TR align="center" class="titulos2">
                    <TD WIDTH="2%">1</TD>
                    <TD WIDTH="28">Seleccione Alerta</TD>
                    <TD COLSPAN=4 CLASS="listado2">
                        <?php echo $cmb_aler ?>
                    </TD>
                </TR>
                <TR align="center" class="titulos2">
                    <TD ROWSPAN=8>2</TD>
                    <TD ROWSPAN=8>Ingrese o Modifique Datos</TD>
                    <TD  WIDTH="20%">Nombre de la alerta</TD>
                    <TD COLSPAN=3  align="left" class="listado2">
                        <input type="text" name="txt_name" id="txt_name" maxlength="60" size="60" value="<?=$txt_name?>" required></input>
                    </TD>
                </TR>
                <TR>
                    <TD align="center" class="titulos2">
                        Tipo Documental que genera la alerta
                    </TD>
                    <TD COLSPAN=3 align="left" class="listado2">
                        <?php echo $cmb_tdoc ?>
                    </TD>
                </TR>
                <TR>
                    <TD align="center" class="titulos2">
                        Tipo de Radicado que genera la alerta
                    </TD>
                    <TD WIDTH="20%" align="center" class="titulos2">
                        Estado
                    </TD>
                    <TD WIDTH="20%" align="center" class="titulos2">
                        D&iacute;as T&eacute;rmino
                    </TD>
                    <TD WIDTH="20%" align="center" class="titulos2">
                        Tipo de Radicado que detiene la alerta
                    </TD>
                </TR>
                <TR>
                    <TD ROWSPAN=5  align="left" class="listado2">
                        <?php
                            echo $cmb_tradg;
                        ?>
                    </TD>
                    <TD align="left" class="listado2">
                        <select class="select" name="slcEstado" id="slcEstado" required>
                            <option value="" selected>&lt; seleccione &gt;</option>
							<option value="0" <?=$off ?>>Inactiva</option>
							<option value="1" <?=$on ?>>Activa</option>
                        </select>
                    </TD>
                    <TD WIDTH="20%" class="listado2" align="CENTER">
                        <input name="txt_diast" id="txt_diast" type="number" maxlength="2" size="2" min="0" max="99" value="<?=$txt_diast ?>" required></input>
                    </TD>
                    <TD ROWSPAN=5 align="left" class="listado2">
                        <?php
                            echo $cmb_tradd;
                        ?>
                    </TD>
                </TR>
                <TR>
                    <TD align="center" class="titulos2">
                        <P ALIGN=CENTER>D&iacute;as Antes</P>
                    </TD>
                    <TD align="center" class="titulos2">
                        <P ALIGN=CENTER>D&iacute;as Despu&eacute;s</P>
                    </TD>
                </TR>
                <TR>
                    <TD align="center" class="listado2">
                        <input name="txt_diasa" id="txt_diasa" type="number" maxlength="2" size="2" min="0" max="99" value="<?=$txt_diasa ?>" required></input>
                    </TD>
                    <TD class="listado2" align="center">
                        <input name="txt_diasd" id="txt_diasd" type="number" maxlength="2" size="2" min="0" max="99" value="<?=$txt_diasd ?>" required></input>
                    </TD>
                </TR>
                <!-- 
                <TR>
                    <TD COLSPAN=2 align="center" class="titulos2">
                        <P>Dependencia</P>
                    </TD>
                </TR>
                <TR>
                    <TD COLSPAN=2 align="left" class="listado2">
                        <?= $cmb_depe ?>
                    </TD>
                </TR>
                 -->
                <TR>
                    <TD COLSPAN=2 align="center" class="titulos2">
                        Usuario
                    </TD>
                </TR>
                <TR>
                    <TD COLSPAN=2 align="left" class="listado2">
                        <?= $cmb_user ?>
                    </TD>
                </TR>
                <?php
                echo $msg;
                ?>
            </TABLE>
            <table width="85%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas">
                <tr bordercolor="#FFFFFF">
                    <td width="10%" class="listado2">&nbsp;</td>
                    <td width="20%"  class="listado2">
                        <span class="e_texto1"><center>
                                <input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado();">
                            </center></span>
                    </td>
                    <td width="20%" class="listado2">
                        <span class="e_texto1"><center>
                                <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Agregar" onClick="return ValidarInformacion(this.value);">
                            </center></span>
                    </td>
                    <td width="20%" class="listado2">
                        <span class="e_texto1"><center>
                                <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Modificar" onClick="return ValidarInformacion(this.value);">
                            </center></span>
                    </td>
                    <td width="20%" class="listado2">
                        <span class="e_texto1"><center>
                                <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Eliminar" onClick="return ValidarInformacion(this.value);">
                            </center></span>
                    </td>
                    <td width="10%" class="listado2">&nbsp;</td>
                </tr>
            </table>
        </form>

        <script ID="clientEventHandlersJS" LANGUAGE="JavaScript">
            <!--
            function ValidarInformacion(opc)
            {	var strMensaje = "Por favor ingrese las datos.";
                var bandOK = true;
                
                if ( (opc == "Agregar") || (opc == "Modificar") )
                {
                    if(rightTrim(document.frm_alertas.txt_name.value) == "")
                    {	strMensaje = strMensaje + "\nDebe ingresar Nombre de la alerta.";
						document.frm_alertas.txt_name.focus();
						bandOK = false;
                    }
                    if (document.frm_alertas.slcEstado.value=='')
                    {   strMensaje = strMensaje + "\nDebe seleccionar Estado.";
                        document.frm_alertas.slcEstado.focus();
                        bandOK = false;
                    }
                    if (document.frm_alertas.slc_tdoc.value=='')
                    {   strMensaje = strMensaje + "\nDebe seleccionar Tipo Documental.";
                        document.frm_alertas.slc_tdoc.focus();
                        bandOK = false;
                    }
                    if (document.frm_alertas.txt_diast.value=='')
                    {   strMensaje = strMensaje + "\nDebe seleccionar D\xedas T\xe9rmino.";
                        document.frm_alertas.txt_diast.focus();
                        bandOK = false;
                    }
                    if (document.frm_alertas.txt_diasa.value=='')
                    {   strMensaje = strMensaje + "\nDebe seleccionar D\xedas Antes de generaci\xf3n de la alerta.";
                        document.frm_alertas.txt_diasa.focus();
                        bandOK = false;
                    }
                    if (document.frm_alertas.txt_diasd.value=='')
                    {   strMensaje = strMensaje + "\nDebe seleccionar D\xedas Despu\xe9s de generaci\xf3n de la alerta.";
                        document.frm_alertas.txt_diasd.focus();
                        bandOK = false;
                    }
                    if (parseInt(document.frm_alertas.txt_diasa.value) > parseInt(document.frm_alertas.txt_diast.value))
                    {   strMensaje = strMensaje + "\nEl valor D\xedas Antes no debe ser mayor a D\xedas T\xe9rmino.";
                        document.frm_alertas.txt_diasa.focus();
                        bandOK = false;
                    }
                    if (document.frm_alertas.slc_user.value=='')
                    {   strMensaje = strMensaje + "\nDebe seleccionar el usuario a quien llegar\xe1 copia de la alerta.";
                        document.frm_alertas.slc_user.focus();
                        bandOK = false;
                    }
                    if (document.frm_alertas['slc_tradgen[]'].selectedIndex == -1)
                    {   strMensaje = strMensaje + "\nDebe seleccionar tipo de radicado que genera la alerta.";
	                    document.frm_alertas['slc_tradgen[]'].focus();
	                    bandOK = false;
	                }
                    if (document.frm_alertas['slc_traddet[]'].selectedIndex == -1)
                    {   strMensaje = strMensaje + "\nDebe seleccionar tipo de radicado que detiene la alerta.";
	                    document.frm_alertas['slc_traddet[]'].focus();
	                    bandOK = false;
	                }
                    if (bandOK)	document.frm_alertas.submit();
                }
                else if(opc == "Eliminar")
                {	if(confirm("Esta seguro de borrar este registro ?\n"))
                    {	document.frm_alertas.submit();	}
                    else
                    {	return false;	}
                }

                if (!bandOK)
                {
                    alert(strMensaje);
                    return bandOK;
                }
            }
            //-->
        </script>
    </body>
</html>
