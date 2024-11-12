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

$ruta_raiz = "../..";
include('../../config.php'); 			// incluir configuracion.
$error = 0;
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
if ($db) {
	//$db->conn->debug = true;
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    if (isset($_POST['btn_accion'])) {
        switch ($_POST['btn_accion']) {
            Case 'Agregar': {
            	$tabla = "SGD_TRAMITES";
            	$var = "val_tr";
            	for ($i = 1; $i < 10; $i++) {
            		${$var.$i} = 0;
            	}
            	foreach ($_POST['slc_trad'] as $k => $v){
            		${"val_tr".$v} = 1;
            	}
            	$campos['SGD_NOMBR_TRAM'] = $_POST['txt_name'];
            	$campos['SGD_TRAD1_TRAM'] = $val_tr1;
            	$campos['SGD_TRAD2_TRAM'] = $val_tr2;
            	$campos['SGD_TRAD3_TRAM'] = $val_tr3;
            	$campos['SGD_TRAD4_TRAM'] = $val_tr4;
            	$campos['SGD_TRAD5_TRAM'] = $val_tr5;
            	$campos['SGD_TRAD6_TRAM'] = $val_tr6;
            	$campos['SGD_TRAD7_TRAM'] = $val_tr7;
            	$campos['SGD_TRAD8_TRAM'] = $val_tr8;
            	$campos['SGD_TRAD9_TRAM'] = $val_tr9;
            	$campos['SGD_DEPRE_TRAM'] = $_POST['slc_DepResp'];
            	$campos['SGD_DEPFI_TRAM'] = $_POST['slc_DepFin'];
				$sql = $db->conn->GetInsertSQL($tabla, $campos, $magicq=true, $force_type=false);
				$ok = $db->conn->Execute($sql);
                $ok ? $error = 3 : $error = 2;
                }break;
            Case 'Modificar': {
                $tabla = "SGD_TRAMITES";
            	$var = "val_tr";
            	for ($i = 1; $i < 10; $i++){
            		${$var.$i} = 0;
            	}
            	foreach ($_POST['slc_trad'] as $k => $v){
            		${"val_tr".$v} = 1;
            	}
            	$campos['SGD_ID_TRAM'] = $_POST['slc_tram'];
            	$campos['SGD_NOMBR_TRAM'] = $_POST['txt_name'];
            	$campos['SGD_TRAD1_TRAM'] = $val_tr1;
            	$campos['SGD_TRAD2_TRAM'] = $val_tr2;
            	$campos['SGD_TRAD3_TRAM'] = $val_tr3;
            	$campos['SGD_TRAD4_TRAM'] = $val_tr4;
            	$campos['SGD_TRAD5_TRAM'] = $val_tr5;
            	$campos['SGD_TRAD6_TRAM'] = $val_tr6;
            	$campos['SGD_TRAD7_TRAM'] = $val_tr7;
            	$campos['SGD_TRAD8_TRAM'] = $val_tr8;
            	$campos['SGD_TRAD9_TRAM'] = $val_tr9;
            	$campos['SGD_DEPRE_TRAM'] = $_POST['slc_DepResp'];
            	$campos['SGD_DEPFI_TRAM'] = $_POST['slc_DepFin'];
				$ok = $db->conn->Replace($tabla, $campos, 'SGD_ID_TRAM', $autoquote = true);
                $ok ? $error = 4 : $error = 2;
                }break;
            Case 'Eliminar': {
            	$sql = "SELECT COUNT(RADI_NUME_RADI) AS CONTA FROM RADICADO WHERE SGD_ID_TRAM=".$_POST['slc_tram'];
            	$cnt = $db->conn->GetOne($sql);
            	if ($cnt > 0) {
            		$error = 5;
            	} else {
	            	$sql = "DELETE FROM SGD_TRAMITES WHERE SGD_ID_TRAM=".$_POST['slc_tram'];
	                $db->conn->Execute($sql);
	                $ok = $db->conn->Affected_Rows();
	                ($ok == 1) ? $error = 6 : $error = 2;
	                }
            }break;
            Default: break;
        }
        $txt_name = '';
       	$slc_DepResp = '';
       	$slc_DepFin = '';
       	unset($slc_trad);
    }
    
    $sql0 =	"SELECT SGD_NOMBR_TRAM, SGD_ID_TRAM, SGD_DEPRE_TRAM, SGD_DEPFI_TRAM, ".
    		"SGD_TRAD1_TRAM, SGD_TRAD2_TRAM, SGD_TRAD3_TRAM, SGD_TRAD4_TRAM, SGD_TRAD5_TRAM, ".
			"SGD_TRAD6_TRAM, SGD_TRAD7_TRAM, SGD_TRAD8_TRAM, SGD_TRAD9_TRAM FROM SGD_TRAMITES ";
    $sql2 = "ORDER BY SGD_NOMBR_TRAM";
    $rs_tram = $db->conn->Execute($sql0.$sql2);
    if($_POST['slc_tram']){
    	unset($slc_trad);
    	$rs_tram2 = $db->conn->Execute($sql0." WHERE SGD_ID_TRAM=".$_POST['slc_tram']);
    	for ($i = 1; $i < 10; $i++){
    		$idk = "SGD_TRAD".$i."_TRAM";
			if ($rs_tram2->fields[$idk]==1) {
				$slc_trad[] = $i; 
			}
    	}
    	//$txt_id = $rs_aler2->fields['SGD_ID_ALER'];
    	$slc_tram = $_POST['slc_tram'];
    	$slc_DepResp = $rs_tram2->fields['SGD_DEPRE_TRAM'];
    	$slc_DepFin = $rs_tram2->fields['SGD_DEPFI_TRAM'];
    	$txt_name = $rs_tram2->fields['SGD_NOMBR_TRAM'];
    } else {
    	$txt_name = '';
       	$slc_DepResp = '';
       	$slc_DepFin = '';
       	unset($slc_trad);
    }
    $cmb_tram = $rs_tram->GetMenu2('slc_tram', $slc_tram, ":&lt;&lt;SELECCIONE&gt;&gt;" , false, 0, 'id="slc_tram" class="select" onchange="this.form.submit();"');
    
    $sql = "SELECT D.DEPE_NOMB, D.DEPE_CODI FROM DEPENDENCIA D ORDER BY D.DEPE_CODI";
    $rs_depe = $db->conn->CacheExecute(1, $sql);
    $slc_DepResp = $rs_depe->GetMenu2('slc_DepResp', $slc_DepResp, ":&lt;&lt;SELECCIONE&gt;&gt;", false, false, 'id="slc_DepResp" class="select" required');
    $rs_depe->Move(0);
    $slc_DepFin = $rs_depe->GetMenu2('slc_DepFin', $slc_DepFin, ":&lt;&lt;SELECCIONE&gt;&gt;", false, false, 'id="slc_DepFin" class="select" required');
    
    $sql = "SELECT SGD_TRAD_DESCR, SGD_TRAD_CODIGO FROM SGD_TRAD_TIPORAD ORDER BY SGD_TRAD_CODIGO";
    $rs_trad = $db->conn->Execute($sql);
    $cmb_trad = $rs_trad->GetMenu2('slc_trad[]', $slc_trad, false, true, 10, 'id="slc_trad" class="select" required');   
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
            $msg .= "El registro se encuentra relacionado con otros registros!!.";
            break;
        case 6: //ELIMINAR REGISTRO
            $msg .= "Eliminaci&oacute;n exitosa!!.";
            break;
    }
    $msg .= '</td></tr>';
}
?>
<html>
    <head>
        <script language="JavaScript" type="text/javascript" >
            function rightTrim(sString)
            {	while (sString.substring(sString.length-1, sString.length) == ' ')
                {	sString = sString.substring(0,sString.length-1);  }
                return sString;
            }

            function ver_listado()
            {
                window.open('listados.php?var=tmt','','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
            }
        </script>

        <title>.: Orfeo :. Admor de Tr&aacute;mites.</title>
        <link href="<?= $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form name="frm_tramites" id="frm_tramites" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="hdBandera" value="">
			
			<TABLE WIDTH="85%" BORDER="1"  cellspacing="0" CLASS="tablas" ALIGN="center">
			<TR bordercolor="#FFFFFF">
                <TD COLSPAN="4" HEIGHT="40" ALIGN="center" CLASS="titulos4" VALIGN="middle">
                    <b><span class=etexto>ADMINISTRADOR DE TR&Aacute;MITES</span></b>
                </TD>
            </TR>
			<TR align="center" class="titulos2">
				<TD WIDTH="2%">
					1
				</TD>
				<TD WIDTH="28">
					Seleccione
				</TD>
				<TD COLSPAN=2 CLASS="listado2">
					<?php echo $cmb_tram ?>
				</TD>
			</TR>
			<TR align="center" class="titulos2">
				<TD ROWSPAN=4>
					2
				</TD>
				<TD ROWSPAN=4>
					Ingrese o modifique datos
				</TD>
				<TD WIDTH="25%">
					Nombre
				</TD>
				<TD align="left" class="listado2">
                    <input type="text" name="txt_name" id="txt_name" maxlength="60" size="60" value="<?=$txt_name?>" required></input>
                </TD>
			</TR>
			<TR align="center" class="titulos2">
				<TD>
					Dependencia Responsable
				</TD>
				<TD align="left" class="listado2">
					<?php echo $slc_DepResp ?>
				</TD>
			</TR>
			<TR align="center" class="titulos2">
				<TD>
					<P>Dependencia Finaliza</P>
				</TD>
				<TD align="left" class="listado2">
					<?php echo $slc_DepFin ?>
				</TD>
			</TR>
			<TR align="center" class="titulos2">
				<TD>
					Tipo Radicado Finaliza
				</TD>
				<TD align="left" class="listado2">
					<?php
                        echo $cmb_trad;
                    ?>
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

        <script type="text/javascript" LANGUAGE="JavaScript">
            <!--
            function ValidarInformacion(opc)
            {	var strMensaje = "Por favor ingrese las datos.";
                var bandOK = true;
                
                if ( (opc == "Agregar") || (opc == "Modificar") )
                {
                    if(rightTrim(document.frm_tramites.txt_name.value) == "")
                    {	strMensaje = strMensaje + "\nDebe ingresar Nombre del tramite.";
						document.frm_tramites.txt_name.focus();
						bandOK = false;
                    }
                    if (document.frm_tramites.slc_DepResp.value == '')
                    {   strMensaje = strMensaje + "\nDebe seleccionar dependencia responsable del tramite.";
	                    document.frm_tramites.slc_DepResp.focus();
	                    bandOK = false;
	                }
                    if (document.frm_tramites.slc_DepFin.value == '')
                    {   strMensaje = strMensaje + "\nDebe seleccionar dependencia que finaliza el tramite.";
	                    document.frm_tramites.slc_DepFin.focus();
	                    bandOK = false;
	                }
                    if (document.frm_tramites['slc_trad[]'].selectedIndex == -1)
                    {   strMensaje = strMensaje + "\nDebe seleccionar tipo de radicado que finaliza tramite.";
	                    document.frm_tramites['slc_trad[]'].focus();
	                    bandOK = false;
	                }
                    if (bandOK)	document.frm_tramites.submit();
                }
                else if(opc == "Eliminar")
                {	if(confirm("Esta seguro de borrar este registro ?\n"))
                    {	document.frm_tramites.submit();	}
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
