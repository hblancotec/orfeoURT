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

$ADODB_COUNTRECS = false;

$ruta_raiz = "../..";
include_once($ruta_raiz . '/config.php');    // incluir configuracion.
include_once($ruta_raiz . "/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
if ($db) {
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $error = 0;
    if (isset($_POST['btn_accion'])) {
        $dpto_tmp = explode("-", $_POST['codep_us1']);
        $dpto_tmp = $dpto_tmp[1];
        $record = array();
        $record['DPTO_CODI'] = $dpto_tmp;
        $record['MUNI_CODI'] = $_POST['txtIdMcpio'];
        $record['ID_PAIS'] = $_POST['idpais1'];
        $dptoCodi = $dpto_tmp;
        $muniCodi = $_POST['txtIdMcpio'];
        $paisCodi = $_POST['idpais1'];
        $record['ID_CONT'] = $_POST['idcont1'];
        $record['MUNI_NOMB'] = $_POST['txtModelo'];
        $record['HOMOLOGA_MUNI'] = $_POST['Slc_defa'];
        if ($_POST['Slc_defa']) {
            $record['HOMOLOGA_IDMUNI'] = $_POST['idcont2'] . '-' . $_POST['muni_us2'];
        } else {
            if (!defined('ADODB_FORCE_NULLS'))
                define('ADODB_FORCE_NULLS', 1);
            $ADODB_FORCE_TYPE = ADODB_FORCE_NULL;
            $record['HOMOLOGA_IDMUNI'] = 'null';
        }
        switch ($_POST['btn_accion']) {
            Case 'Agregar':
            Case 'Modificar': {
                    $ok = $db->conn->Replace('MUNICIPIO', $record, array('DPTO_CODI', 'MUNI_CODI', 'ID_PAIS', 'ID_CONT'), $autoquote = true);
                    unset($record['MUNI_NOMB']);unset($record['HOMOLOGA_IDMUNI']);unset($record['HOMOLOGA_MUNI']);$record['DEST472'] = $_POST['Slc_defa'];
                    $ok2 = $db->conn->Replace('SGD_MUNICIPIO_472', $record, array('DPTO_CODI', 'MUNI_CODI', 'ID_PAIS', 'ID_CONT'), $autoquote = true);
                    ($ok && $ok2) ? $error = $ok : $error = 4;
                }break;
            Case 'Eliminar': {
                    $ADODB_COUNTRECS = true;
                    $record = array_slice($record, 0, 3);
                    $db->conn->StartTrans();

                    $db->conn->Execute("DELETE FROM SGD_MUNICIPIO_472 WHERE DPTO_CODI=$dptoCodi AND MUNI_CODI=$muniCodi AND ID_PAIS=$paisCodi");
                    $db->conn->Execute("DELETE FROM MUNICIPIO WHERE DPTO_CODI=$dptoCodi AND MUNI_CODI=$muniCodi AND ID_PAIS=$paisCodi");
                        
                    if ($db->conn->CompleteTrans()) {
                        ;
                    } else {
                    	$error = 5;
                    	$db->conn->RollbackTrans();
                    }
                }break;
        }
        unset($record);
    }
    include "../../radicacion/crea_combos_universales.php";
}
else {
    $error = 3;
}
?>
<html>
    <head>
        <title>Orfeo- Admor de Municipios.</title>
        <link rel="stylesheet" href="../../estilos/orfeo.css">
        <script language="JavaScript" src="../../js/crea_combos_2.js"></script>
        <script language="JavaScript">
            <!--
            function Actual()
            {
                var Obj = document.getElementById('muni_us1');
                var i = Obj.selectedIndex;
                var x = 0;
                var y = 0;
                var found = true;
                var str = "";
                while(found)
                {	if (vm[x]['ID1'] == Obj.options[i].value)	break;
                    x += 1;
                }
                str = vm[x]['ID1'];
                str = str.split('-');
                document.getElementById('txtModelo').value = vm[x]['NOMBRE'];
                document.getElementById('txtIdMcpio').value = str[2];
                document.getElementById('Slc_defa').value = vm[x]['DEST472'];
            }

            function borra_datos()
            {
                document.getElementById('txtIdMcpio').value = "";
                document.getElementById('txtModelo').value = "";
                document.getElementById('Slc_defa').value = "";
            }

            function ver_listado()
            {
                window.open('listados.php?var=mcp','','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
            }
<?php
// Convertimos los vectores de los paises, dptos y municipios creados en crea_combos_universales.php a vectores en JavaScript.
echo arrayToJsArray($vcontiv, 'vc');
echo arrayToJsArray($vpaisesv, 'vp');
echo arrayToJsArray($vdptosv, 'vd');
echo arrayToJsArray($vmcposv, 'vm');
?>
    //-->
        </script>
    </head>
    <body>
        <form name="form1" method="post" id="form1" action="<?= $_SERVER['PHP_SELF'] ?>">  
            <input type="hidden" name="hdBandera" value="">
            <table width="75%" border="1" align="center" cellspacing="0">
                <tr bordercolor="#FFFFFF">
                    <td colspan="3" align="center" valign="middle" class="titulos4"><b>ADMINISTRADOR DE MUNICIPIOS</b></td>
                </tr>
                <tr> 
                    <td width="3%" align="center" class="titulos2"><b>1.</b></td>
                    <td width="25%" align="left" class="titulos2"><b>&nbsp;Seleccione Continente</b></td>
                    <td width="72%" class="listado2">
                        <?php
                        // Listamos los continentes.
                        $i = 1;
                        echo "<SELECT NAME=\"idcont$i\" ID=\"idcont$i\" CLASS=\"select\" onchange=\"cambia(this.form, 'idpais$i', 'idcont$i')\">";
                        echo "<option value='0'>&lt;&lt; seleccione &gt;&gt;</option>";
                        foreach ($vcontiv as $key => $value) {
                        	echo "<option value='".$key."'>".$value."</option>";
                        }
                        echo "</SELECT>";
                        ?>	</td>
                </tr>
                <tr>
                    <td align="center" class="titulos2"><b>2.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Pa&iacute;s</b></td>
                    <td align="left" class="listado2">
                        <select name="idpais1" id="idpais1" class="select" onChange="borra_datos();cambia(this.form, 'codep_us1', 'idpais1')">
                            <option value="0" selected>&lt;&lt; Seleccione Continente &gt;&gt;</option>
                        </select>
                    </td>
                </tr>
                <tr> 
                    <td align="center" class="titulos2"><b>3.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Dpto.</b></td>
                    <td align="left" class="listado2">
                        <select name='codep_us1' id ="codep_us1" class='select' onChange="borra_datos();cambia(this.form, 'muni_us1', 'codep_us1')" ><option value='0' selected>&lt;&lt; Seleccione Pa&iacute;s &gt;&gt;</option></select>
                    </td>
                </tr>
                <tr> 
                    <td align="center" class="titulos2"><b>4.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Municipio.</b></td>
                    <td align="left" class="listado2">
                        <select name='muni_us1' id="muni_us1" class='select' onchange="borra_datos();Actual()" ><option value='0' selected>&lt;&lt; Seleccione Dpto &gt;&gt;</option></select>
                    </td>
                </tr>

                <tr> 
                    <td rowspan="4" align="center" class="titulos2"><b>5.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese c&oacute;digo del Municipio.</b></td>
                    <td class="listado2"><input name="txtIdMcpio" id="txtIdMcpio" type="text" size="10" maxlength="3"></td>
                </tr>
                <tr> 
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese nombre del Municipio.</b></td>
                    <td class="listado2"><input name="txtModelo" id="txtModelo" type="text" size="50" maxlength="70"></td>
                </tr>
                <tr> 
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione tipo de destino 4-72</b></td>
                    <td class="listado2">
                        <select name="Slc_defa" class="select" id="Slc_defa">
                            <option value="" selected>&lt; seleccione &gt;</option>
                            <option value="1"> Urbano </option>
                            <option value="2"> Regional </option>
                            <option value="3"> Nacional </option>
                            <option value="4"> T. Especial </option>
                            <option value="5"> Zona 1 </option>
                            <option value="6"> Zona 2 </option>
                            <option value="7"> Zona 3 </option>
                        </select>
                    </td>
                </tr>
                <?php
                if ($error) {
                    echo '<tr bordercolor="#FFFFFF"> 
			<td width="3%" align="center" class="titulosError" colspan="3" bgcolor="#FFFFFF">';
                    switch ($error) {
                        case 1: echo "Informaci&oacute;n actualizada!!";
                            break;  //ACUTALIZACION REALIZADA
                        case 2: echo "Municipio creado satisfactoriamente!!";
                            break;  //INSERCION REALIZADA
                        case 3: echo "Error al conectar a BD, comun&iacute;quese con el Administrador de sistema !!";
                            break;  //NO CONECCION A BD
                        case 4: echo "Error al gestionar datos, comun&iacute;quese con el Administrador de sistema !!";
                            break;  //ERROR EJECUCCION SQL
                        case 5: echo "No se puede eliminar municipio, se encuentra ligado a hist&oacute;ricos.";
                            break;  //IMPOSIBILIDAD DE ELIMINAR MUNICIPIO, ESTA LIGADO CON HISTORICOS.
                    }
                    echo '</td></tr>';
                }
                ?>
            </table>
            <table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="listado2">
                <tr>
                    <td width="10%">&nbsp;</td>
                    <td width="20%" align="center"><input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado();" accesskey="L" alt="Alt + L"></td>
                    <td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Agregar" onClick="document.form1.hdBandera.value='A'; return ValidarInformacion();" accesskey="A"></td>
                    <td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Modificar" onClick="document.form1.hdBandera.value='M'; return ValidarInformacion();" accesskey="M"></td>
                    <td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Eliminar" onClick="document.form1.hdBandera.value='E'; return ValidarInformacion();" accesskey="E"></td>
                    <td width="10%">&nbsp;</td>
                </tr>
            </table>
        </form>
    </body>
</html>

<script ID="clientEventHandlersJS" LANGUAGE="JavaScript">
    <!--
    function ValidarInformacion()
    {	var strMensaje = "Por favor ingrese las datos.";

	if(document.form1.idcont1.value == "0") 
	{	alert("Debe seleccionar el continente.\n" + strMensaje);
            document.form1.idcont1.focus();
            return false;
	}
	
	if(document.form1.idpais1.value == "0") 
	{	alert("Debe seleccionar el pais.\n" + strMensaje);
            document.form1.idpais1.focus();
            return false;
	}
	
	if(document.form1.codep_us1.value == "0")
	{	alert("Debe seleccionar el departamento.\n" + strMensaje);
            document.form1.codep_us1.focus();
            return false;
	}
	
	if(document.form1.txtIdMcpio.value <= "0") 
	{	alert("Debe ingresar el Codigo del Municipio.\n" + strMensaje);
        document.form1.txtIdMcpio.focus();
        return false;
	}
	else if(isNaN(document.form1.txtIdMcpio.value))
	{	alert("El Codigo del Municipio debe ser numerico.\n" + strMensaje);
        document.form1.txtIdMcpio.select();
        document.form1.txtIdMcpio.focus();
        return false; 
	}
	
	if(document.form1.hdBandera.value == "A" || document.form1.hdBandera.value == "M")
	{	if(document.form1.txtModelo.value == "")
        {	alert("Debe ingresar nombre del Municipio.\n" + strMensaje);
            document.form1.txtModelo.focus();
            return false; 
        }
        if(!isNaN(document.form1.txtModelo.value))
        {	alert("El nombre del Municipio no debe ser numerico.\n" + strMensaje);
            document.form1.txtModelo.select();
            document.form1.txtModelo.focus();
            return false; 
        }
        if(document.form1.Slc_defa.value == "")
        {	alert("Debe seleccionar Destino 4-72.\n" + strMensaje);
            document.form1.txtModelo.focus();
            return false; 
        }            
	}
	if(document.form1.hdBandera.value == "E")
	{	if(confirm("Esta seguro de borrar el registro ?"))
            {	document.form1.submit();	}
            else
            {	return false;	}
	}
	document.form1.submit();
    }
    //-->
</script>
