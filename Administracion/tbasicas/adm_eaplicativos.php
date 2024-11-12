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
include($ruta_raiz.'/config.php');	// incluir configuracion.
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
$conn = NewADOConnection($dsn);
if ($conn)
{       $conn->SetFetchMode(ADODB_FETCH_ASSOC);
        include($ruta_raiz.'/include/class/enlaceAplicativos.class.php');
        $obj_tmp = new enlaceAplicativos($conn);
        if (isset($_POST['btn_accion']))
        {       switch($_POST['btn_accion'])
                {       Case 'Agregar':
                        {
                            $ok = $obj_tmp->SetInsDatos(array('txtId'=>$_POST['txtId'], 'txtModelo' => $_POST['txtModelo'], 'slcEstado' => $_POST['slcEstado'],'slcDepe' => $_POST['slcDepe'], 'txtIpAcceso'=> $_POST['txtIpAcceso'], 'slcUsua'=> $_POST['slcUsua']
                                                            , 'txtURLWSDL'=> $_POST['txtURLWSDL'], 'txtUsuarioWS'=> $_POST['txtUsuarioWS'], 'txtPasswordWS'=> $_POST['txtPasswordWS'], 'slcDriverBD'=> $_POST['slcDriverBD'], 'txtServerBD'=> $_POST['txtServerBD'], 'txtDataBaseBD'=> $_POST['txtDataBaseBD'], 'txtUsuarioBD'=> $_POST['txtUsuarioBD'], 'txtPasswordBD'=> $_POST['txtPasswordBD']));
                            if($ok) $obj_tmp->setMetodosPermitidos ($metodo, $_POST['txtId']);  
                            $ok ? $error = 3 : $error = 2;
                        }break;
                        Case 'Modificar':
                        {
                            if(!is_array($metodo))$metodo[]='null';
                            $ok = $obj_tmp->SetModDatos(array('txtId'=>$_POST['txtId'], 'txtModelo' => $_POST['txtModelo'], 'slcEstado' => $_POST['slcEstado'],'slcDepe' => $_POST['slcDepe'], 'txtIpAcceso'=> $_POST['txtIpAcceso'], 'slcUsua'=> $_POST['slcUsua']
                                                        , 'txtURLWSDL'=> $_POST['txtURLWSDL'], 'txtUsuarioWS'=> $_POST['txtUsuarioWS'], 'txtPasswordWS'=> $_POST['txtPasswordWS'], 'slcDriverBD'=> $_POST['slcDriverBD'], 'txtServerBD'=> $_POST['txtServerBD'], 'txtDataBaseBD'=> $_POST['txtDataBaseBD'], 'txtUsuarioBD'=> $_POST['txtUsuarioBD'], 'txtPasswordBD'=> $_POST['txtPasswordBD']));
                            if($ok) $obj_tmp->setMetodosPermitidos ($metodo, $_POST['txtId']); 
                            $ok ? $error = 4 : $error = 2;
                        }break;
                        Case 'Eliminar':
                        {
                            $ok = $obj_tmp->SetDelDatos($_POST['slc_cmb2']);
                                ($ok == 0) ? $error = 5 : (($ok) ? $error = null : $error = 2);
                        }break;
                        Default: break;
                }
                unset($record);
        }
    $slc_tmp = $obj_tmp->Get_ComboOpc(true,false);
    $sql = "SELECT ".$conn->concat("D.DEPE_CODI","'-'","D.DEPE_NOMB").", D.DEPE_CODI FROM DEPENDENCIA D
            INNER JOIN USUARIO U ON D.DEPE_CODI = U.DEPE_CODI
            WHERE U.USUA_CODI=1";
    $rs_depe = $conn->Execute($sql);
    $slc_depe = $rs_depe->GetMenu2('slcDepe',false,"0:&lt;&lt;SELECCIONE",false,false,'id="slcDepe" class="select" onchange="pedirComboUsu(\''.$ruta_raiz.'/cCombos.php\',\'divUsu\',\'usuarios\',this.value,\'\')"');
    $vec_tmp = $obj_tmp->Get_ArrayDatos();
    $vec_m= $obj_tmp->get_MetodosPermitidos();
    $vec_metodos = $obj_tmp->get_Metodos();
    $tblMetodos="<table width='100%'>";
    $cont=0;
    foreach($vec_metodos as $i=>$dat){
        $tr=$trC="";
        (($cont%2)==0)?$tr="<tr>":$trC="</tr>";
        $tblMetodos.=$tr."<td class='listado2' height='25' >
				<input name=\"metodo['".$dat["COD_METODO"]."']\" value='".$dat["COD_METODO"]."' type='checkbox'>
				".$dat["NOMBRE"]."
			  </td>".$trC;
        $cont++;
    }
    $tblMetodos.="</table>";
}
else
{       $error = 1;
}

/*
*       Funcion que convierte un valor de PHP a un valor Javascript.
*/
function valueToJsValue($value, $encoding = false)
{       if (!is_numeric($value))
        {       $value = str_replace('\\', '\\\\', $value);
                $value = str_replace('"', '\"', $value);
                $value = '"'.$value.'"';
        }
        if ($encoding)
        {       switch ($encoding)
                {       case 'utf8' :   return iconv("ISO-8859-2", "UTF-8", $value);
                                                        break;
                }
        }
        else
        {       return $value;  }
        return ;
}

/*
*       Funcion que convierte un vector de PHP a un vector Javascript.
*       Utiliza a su vez la funcion valueToJsValue.
*/
function arrayToJsArray( $array, $name, $nl = "\n", $encoding = false )
{       if (is_array($array))
        {       $jsArray = $name . ' = new Array();'.$nl;
                foreach($array as $key => $value)
                {       switch (gettype($value))
                        {       case 'unknown type':
                                case 'resource':
                                case 'object':  break;
                                case 'array':   $jsArray .= arrayToJsArray($value,$name.'['.valueToJsValue($key, $encoding).']', $nl);
                                                                break;
                                case 'NULL':    $jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = null;'.$nl;
                                                                break;
                                case 'boolean': $jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.($value ? 'true' : 'false').';'.$nl;
                                                                break;
                                case 'string':  $jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.valueToJsValue($value, $encoding).';'.$nl;
                                                                break;
                                case 'double':
                                case 'integer': $jsArray .= $name.'['.valueToJsValue($key,$encoding).'] = '.$value.';'.$nl;
                                                                break;
                                default:        trigger_error('Hoppa, egy j t�us a PHP-ben?'.__CLASS__.'::'.__FUNCTION__.'()!', E_USER_WARNING);
                        }
                }
                return $jsArray;
        }
        else
        {       return false;   }
}
if ($error)
{       $msg = '<tr bordercolor="#FFFFFF">
                        <td width="3%" align="center" class="titulosError" colspan="3" bgcolor="#FFFFFF">';
        switch ($error)
        {       case 1: //NO CONECCION A BD
                                $msg .= "Error al conectar a BD, comun&iacute;quese con el Administrador de sistema !!";
                                break;
                case 2: //ERROR EJECUCCION SQL
                                $msg .=  "Error al gestionar datos, comun&iacute;quese con el Administrador de sistema !!";
                                break;
                case 3: //INSERCION REALIZADA
                                $msg .=  "Creaci&oacute;n exitosa!";break;
                case 4: //MODIFICACION REALIZADA
                                $msg .=  "Registro actualizado satisfactoriamente!!";break;
                case 5: //IMPOSIBILIDAD DE ELIMINAR REGISTRO
                                $msg .=  "No se puede eliminar registro, tiene dependencias internas relacionadas.";break;
        }
        $msg .=  '</td></tr>';
}
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $ruta_raiz ?>/estilos/tabber.css" TYPE="text/css" MEDIA="screen">
<script type="text/javascript" language="JavaScript">
            document.write('<style type="text/css">.tabber{display:none;}<\/style>');
            var tabberOptions =
                {
                /* Optional: instead of letting tabber run during the onload event,
                we'll start it up manually. This can be useful because the onload
                even runs after all the images have finished loading, and we can
                run tabber at the bottom of our page to start it up faster. See the
                bottom of this page for more info. Note: this variable must be set
                BEFORE you include tabber.js.
                 */
                'manualStartup':true,
                /* Optional: code to run after each tabber object has initialized */
                'onLoad': function(argsObj)
                {
                    /* Display an alert only after tab2
                        if (argsObj.tabber.id == 'tab1')
                        {       crea_var_idlugar_defa('<?= $muni_us1 ?>');  }*/
                },
                /* Optional: set an ID for each tab navigation link */
                'addLinkId': true
            };
</script>
<script type="text/javascript" src="<?php echo $ruta_raiz ?>/js/tabber.js"></script>
<script type="text/javascript" language="JavaScript" src="<?php echo $ruta_raiz ?>/js/ajax.js"></script>
<script language="JavaScript">
<?php
echo arrayToJsArray($vec_tmp, 'vt');
echo arrayToJsArray($vec_m, 'vm');
?>
<!--
function Actual()
{
    var Obj = document.getElementById('slc_cmb2');
    var i = Obj.selectedIndex;
    if (parseInt(Obj.value) == 0)
    {
        document.getElementById('txtModelo').value = '';
        document.getElementById('txtId').value = '';
        document.getElementById('slcEstado').value = '';
        document.getElementById('slcDepe').value = '';
        document.getElementById('txtIpAcceso').value = '';
        document.getElementById('txtURLWSDL').value = '';
        document.getElementById('txtUsuarioWS').value = '';
        document.getElementById('txtPasswordWS').value = '';
        document.getElementById('slcDriverBD').value = 0;
        document.getElementById('txtServerBD').value = '';
        document.getElementById('txtDataBaseBD').value = '';
        document.getElementById('txtUsuarioBD').value = '';
        document.getElementById('txtPasswordBD').value ='';
    }
    else
    {   //alert(Obj.value);
        for (x=0; x < vt.length; x++)
        {
            if (vt[x]['ID'] == Obj.value) break;
        }
        document.getElementById('txtModelo').value = vt[x]['NOMBRE'];
        document.getElementById('txtId').value = vt[x]['ID'];
        document.getElementById('slcEstado').value = vt[x]['ESTADO'];
        document.getElementById('slcDepe').value = vt[x]['DEPENDENCIA'];
        document.getElementById('txtIpAcceso').value = vt[x]['IP_ACCESO'];
        document.getElementById('txtURLWSDL').value = vt[x]['CLIENTE_WS_URLWSDL'];
        document.getElementById('txtUsuarioWS').value = vt[x]['CLIENTE_WS_USUARIO'];
        document.getElementById('txtPasswordWS').value = vt[x]['CLIENTE_WS_PASSWORD'];
        document.getElementById('slcDriverBD').value = vt[x]['CLIENTE_BD_DRIVER'];
        document.getElementById('txtServerBD').value = vt[x]['CLIENTE_BD_SERVER'];
        document.getElementById('txtDataBaseBD').value = vt[x]['CLIENTE_BD_DATABASE'];
        document.getElementById('txtUsuarioBD').value = vt[x]['CLIENTE_BD_USUARIO'];
        document.getElementById('txtPasswordBD').value = vt[x]['CLIENTE_BD_PASSWORD'];
        pedirComboUsu('<?php echo $ruta_raiz ?>/cCombos.php','divUsu','usuarios',vt[x]['DEPENDENCIA'],vt[x]['USUA_LOGIN'])
    }
    var lim=9;
    for(i=0;i<document.form1.elements.length;i++) {
        
        if(document.form1.elements[i].name.slice(0,6)=="metodo")
        {   
            document.form1.elements[i].checked=0;
            for (x=0; x < vm.length; x++)
            {
                
                if(document.form1.elements[i].name.length==12)
                    lim=10;
                else if(document.form1.elements[i].name.length==12)
                     lim=11;
                if (vm[document.getElementById('txtId').value] && vm[document.getElementById('txtId').value][document.form1.elements[i].name.slice(8,lim)]){
                    document.form1.elements[i].checked=1;
                    break;
                }
            }
        }
    }
}

function rightTrim(sString)
{       while (sString.substring(sString.length-1, sString.length) == ' ')
        {       sString = sString.substring(0,sString.length-1);  }
        return sString;
}

function ver_listado()
{
        window.open('listados.php?var=eap','','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
}


//-->
</script>
<script ID="clientEventHandlersJS" LANGUAGE="JavaScript">
<!--
function ValidarInformacion(opc)
{       var strMensaje = "Por favor ingrese las datos.";
        var bandOK = true;
        if ( rightTrim(document.form1.txtId.value) <= "0")
        {       strMensaje = strMensaje + "\nDebe ingresar el C\xf3digo." ;
                document.form1.txtId.focus();
                bandOK = false;
        }
        else if(isNaN(document.form1.txtId.value))
        {       strMensaje = strMensaje + "\nEl C\xf3digo debe ser num\xe9rico.";
                document.form1.txtId.select();
                document.form1.txtId.focus();
                bandOK = false;
        }
        if(rightTrim(document.form1.txtIpAcceso.value) == "")
        {       strMensaje = strMensaje + "\nDebe ingresar la ip de acceso del aplicativo.";
                //document.form1.txtIpAcceso.focus();
                bandOK = false;
        }
        if (opc == "Agregar")
        {       if(rightTrim(document.form1.txtModelo.value) == "")
                {       strMensaje = strMensaje + "\nDebe ingresar Nombre del aplicativo.";
                        document.form1.txtModelo.focus();
                        bandOK = false;
                }
                if (document.form1.slcEstado.value=='')
                {   strMensaje = strMensaje + "\nDebe seleccionar Estado.";
                    document.form1.slcEstado.focus();
                    bandOK = false;
                }
        }
        else if(opc == "Modificar")
        {
                if(rightTrim(document.form1.txtModelo.value) == "")
                {       strMensaje = strMensaje + "\nDebe ingresar Nombre del aplicativo.";
                        document.form1.txtModelo.focus();
                        bandOK = false;
                }
                if (document.form1.slcEstado.value=='')
                {   strMensaje = strMensaje + "\nDebe seleccionar Estado.";
                    document.form1.slcEstado.focus();
                    bandOK = false;
                }
                else if(parseInt(document.form1.txtId.value) != parseInt(document.form1.slc_cmb2.value))
                {
                        strMensaje = strMensaje + "\nNo se puede modificar el C\xf3digo.'";
                        document.form1.txtId.focus();
                        bandOK = false;
                }
                else
                {       document.form1.submit();
                }
        }
        else if(opc == "ELiminar")
        {       if(confirm("Esta seguro de borrar este registro ?\n"))
                {       
                    document.form1.submit();        
                }
                else
                {       
                    return false;   
                }
        }

    if (!bandOK)
    {
        alert(strMensaje);
        return bandOK;
    }
}
function pedirComboUsu(fuenteDatos, divID, tipo, dep, usu)
{
    if(xmlHttp)
    {
        // obtain a reference to the <div> element on the page
        divAutilizar = document.getElementById(divID);
        try
        {
            xmlHttp.open("GET", fuenteDatos+"?tipo="+tipo+"&dep="+dep+"&usu="+usu);
            xmlHttp.onreadystatechange = handleRequestStateChange;
            xmlHttp.send(null);
        }
        //display the error in case of failure
        catch (e)
        {
            alert("AJAX:Can't connect to server:\n" + e.toString());
        }
    }
}
//handles the response received from the server
function readResponse()
{
    // read the message from the server
    var xmlResponse = xmlHttp.responseText;
    // display the HTML output
    if(xmlResponse && divAutilizar)
        divAutilizar.innerHTML = xmlResponse;

}
//-->
</script>
<title>.: Orfeo :. Admor de Aplicativos enlace a Orfeo.</title>
<link href="<?=$ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']?>">
<input type="hidden" name="hdBandera" value="">
<table width="75%" border="1" align="center" cellspacing="0" class="tablas">
<tr bordercolor="#FFFFFF">
        <td colspan="3" height="40" align="center" class="titulos4" valign="middle"><b><span class=etexto>1. ADMINISTRADOR DE APLICATIVOS EXTERNOS</span></b></td>
</tr>
<tr bordercolor="#FFFFFF">
        <td align="left" class="titulos2"><b>Seleccione &nbsp;Aplicativo: </b></td>
    <td align="left" class="listado2">
                <?=$slc_tmp     ?>
        </td>
</tr>
</table>
<table width="75%" border="1" align="center" >
    <tr><td>
<div class="tabber" id="tab1" >
    <div class="tabbertab" title="Servidor" >
        <table  width="100%" border="1" cellpadding="0" cellspacing="0" class="tablas">
        <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;C&oacute;digo&nbsp;:</b></td>
                <td class="listado2"><input name="txtId" id="txtId" type="text" size="10" maxlength="2"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;Nombre&nbsp;:</b></td>
                <td class="listado2"><input name="txtModelo" id="txtModelo" type="text" size="50" maxlength="30"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;Estado&nbsp;:</b></td>
                <td class="listado2">
                <select class="select" name="slcEstado" id="slcEstado">
                <option value="">&lt;&lt;SELECCIONE&gt;&gt;</option>
                <option value="1">Activa</option>
                <option value="0">Inactiva</option>
                </select>
            </td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;Dependencia Responsable&nbsp;:</b></td>
                <td class="listado2">
                <?= $slc_depe ?>
            </td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;Usuario Responsable&nbsp;:</b></td>
                <td class="listado2">
                    <div id="divUsu"><select class="select" name="slcUsua" id="slcEstado">
                                    <option value="">&lt;&lt;SELECCIONE&gt;&gt;</option>
                                    </select></div>
            </td>
        </tr>

        <tr>
        <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;Direcci&oacute;n Ip de Acceso:</b></td>
                <td class="listado2"><input name="txtIpAcceso" id="txtIpAcceso" type="text" size="15" maxlength="30"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td align="left" class="titulos2"><b>&nbsp;Metodos Permitidos:</b></td>
                <td class="listado2"><?php echo $tblMetodos?></td>
        </tr>
        <?php
                echo $msg;
        ?>
        </table>
        
        </div>
    <div class="tabbertab" title="Cliente"  align="center">
        <fieldset><legend>Conexi&oacute;n WebServices</legend>
        <table width="100%" border="1" cellpadding="0" cellspacing="0" class="tablas">
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">1.</td>
                <td align="left" class="titulos2"><b>Url WSDL</b></td>
                <td class="listado2"><input name="txtURLWSDL" id="txtURLWSDL" type="text" size="100" maxlength="200"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">2.</td>
                <td align="left" class="titulos2"><b>Usuario</b></td>
                <td class="listado2"><input name="txtUsuarioWS" id="txtUsuarioWS" type="text" size="50" maxlength="30"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">3.</td>
                <td align="left" class="titulos2"><b>Pasaword</b></td>
                <td class="listado2"><input name="txtPasswordWS" id="txtPasswordWS" type="password" size="50" maxlength="30"></td>
        </tr>
        <tr>
        </table>
        </fieldset>
        <fieldset><legend>Conexi&oacute;n BD</legend>
        <table width="100%" border="1" cellpadding="0" cellspacing="0" class="tablas">
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">1.</td>
                <td align="left" class="titulos2"><b>Driver</b></td>
                <td class="listado2"><select class="select" name="slcDriverBD" id="slcDriverBD">
                <option value="">&lt;&lt;SELECCIONE&gt;&gt;</option>
                <option value="mssql">SqlServer</option>
                <option value="oci8">Oracle</option>
                <option value="postgres">Postgres</option>
                </select>
                </td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">2.</td>
                <td align="left" class="titulos2"><b>Servidor</b></td>
                <td class="listado2"><input name="txtServerBD" id="txtServerBD" type="text" size="15" maxlength="30"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">3.</td>
                <td align="left" class="titulos2"><b>DataBase</b></td>
                <td class="listado2"><input name="txtDataBaseBD" id="txtDataBaseBD" type="text" size="15" maxlength="30"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">4.</td>
                <td align="left" class="titulos2"><b>Usuario</b></td>
                <td class="listado2"><input name="txtUsuarioBD" id="txtUsuarioBD" type="text" size="50" maxlength="30"></td>
        </tr>
        <tr bordercolor="#FFFFFF">
                <td valign="middle" class="titulos2">5.</td>
                <td align="left" class="titulos2"><b>Password</b></td>
                <td class="listado2"><input name="txtPasswordBD" id="txtPasswordBD" type="password" size="50" maxlength="30"></td>
        </tr>
        </table>
        </fieldset>
        </div>
    </div>
    </td></tr>
    </table>
        <table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas">
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
 <script type="text/javascript">tabberAutomatic(tabberOptions);</script>
</form>
</body>
</html>