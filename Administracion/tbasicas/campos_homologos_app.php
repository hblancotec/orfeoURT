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
include($ruta_raiz.'/config.php');                      // incluir configuracion.
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
$conn = NewADOConnection($dsn);
if ($conn)
{       //$conn->debug=true;
        $conn->SetFetchMode(ADODB_FETCH_ASSOC);
        include($ruta_raiz.'/include/class/enlaceAplicativos.class.php');
        include($ruta_raiz.'/include/class/tipoRadicado.class.php');
        $obj_tmp = new enlaceAplicativos($conn);
        include_once("$ruta_raiz/include/db/ConnectionHandler.php");
        $conn1 = new ConnectionHandler("$ruta_raiz");
        $obj_trad= new TipRads($conn1);
        if (isset($_POST['btn_accion']))
        {       switch($_POST['btn_accion'])
                {       Case 'Guardar':
                        {
                            $sql="delete from SGD_CAMPOS_HOMOLOGOS where SGD_APLI_CODIGO=".$_POST['slc_cmb2'];
                            $conn->Execute($sql);
                            foreach($_POST['cmbCampExt'] as $i => $val){
                                if($val)
                                $obj_tmp->setInsCamposHomologos(array('cmbApliCodi'=>$_POST['slc_cmb2'],'cmbCampExt'=>$_POST['cmbCampExt'][$i],'cmbTiposRad'=>$_POST['cmbTiposRad'][$i],'cmbCamposOrfeo'=>$_POST['cmbCamposOrfeo'][$i],'todotipoR'=>$_POST['todotipoR'][$i]));
                            }
                            $ok ? $error = 3 : $error = 2;
                        }break;
                }
                unset($record);
        }
        $slc_tmp  = $obj_tmp->Get_ComboOpc(true,true, "onChange=cambia()",$_POST['slc_cmb2']);
        
        if(isset($_POST['slc_cmb2'])){
            $SQL=" select * from sgd_campos_homologos where  sgd_apli_codigo=".$_POST['slc_cmb2']." order by 3";
            $ADODB_COUNTRECS=true;
            $rs=$conn->Execute($SQL);
            $ADODB_COUNTRECS=false;
            $cont=1;
            if($rs && $rs->RecordCount()>0){
                while(!$rs->EOF){
                    
                    $slc_trad      = $obj_trad->getComboTipoRad(true, " ","cmbTiposRad[$cont]",$rs->fields['SGD_TRAD_CODIGO']);
                    $slc_camposapp =$obj_tmp->getComboCamposExt(true,false,$_POST['slc_cmb2'],"cmbCampExt[$cont]",$rs->fields['SGD_COD_CAMPOEXT']);
                    $slcCO         =$obj_tmp->getCamposOrfeo("cmbCamposOrfeo[$cont]", "0:<< Seleccione >>",$rs->fields['SGD_CAMPO_ORFEO'], "onchange='activa($cont)'" );
                    ($rs->fields['SGD_CAMPO_ORFEO']!=1)? $disp1="style=\"display: none\"":$disp1=" ";
                    $disp2=" ";
                    $chk=" ";
                    if($rs->fields['SGD_TIPORAD_ESPECIFICO']==1 || $rs->fields['SGD_CAMPO_ORFEO']!=1){
                        $disp2="style=\"display: none\"";
                        $chk="checked";
                    }
                    
                    //($rs->RecordCount()>$cont)?$disp3="style=\"display: none\"":$disp3=" ";
                    $TR.="<tr>
                            <td class=\"listado2\" valign=\"middle\">
                                $slc_camposapp
                            </td>
                            <td class=\"listado2\" valign=\"middle\">
                            <table>
                                <tr>
                                    <td>
                                        $slcCO
                                    </td>
                                    <td ><span id='chk[$cont]' $disp1><input  type=\"checkbox\" $chk value=\"1\" name=\"todotipoR[$cont]\" id=\"todotipoR[$cont]\" onchange='activaTPRAD($cont);'>Todos los Tipos?</span></td>
                                    <td ><span id='vtipoRad$cont' $disp2>Tipo Radicado:$slc_trad</span></td>
                                </tr>   
                            </table>
                            </td>
                            <td class=\"listado2\" >
                                <table><tr><td><span id='$cont' $disp3><button type='button' onclick='agregaLinea(".($rs->RecordCount()+1).");' class=\"botonmenos\"><img src=\"$ruta_raiz/img/silk/icons/add.png\" ></button></span>
                                </td><td><button type='button' onclick='eliminaLinea(this);' class=\"botonmenos\"><img src=\"$ruta_raiz/img/silk/icons/delete.png\" ></button ></td></tr></table>
                            </td>
                         </tr>";
                    $rs->MoveNext();
                    $cont++;
                }
            }
            else{
                
                    $slc_trad      = $obj_trad->getComboTipoRad(true, " ","cmbTiposRad[1]");
                    $slc_camposapp =$obj_tmp->getComboCamposExt(true,false,$_POST['slc_cmb2'],"cmbCampExt[1]");
                    $slcCO         =$obj_tmp->getCamposOrfeo("cmbCamposOrfeo[1]", "0:<< Seleccione >>",false, "onchange='activa(1)'" );
                    $TR.="<tr>
                            <td class=\"listado2\" valign=\"middle\">
                                $slc_camposapp
                            </td>
                            <td class=\"listado2\" valign=\"middle\">
                            <table>
                                <tr>
                                    <td>
                                        $slcCO
                                    </td>
                                    <td ><span id='chk[1]' style=\"display: none\"><input  type=\"checkbox\"  value=\"1\" name=\"todotipoR[1]\" id=\"todotipoR[1]\" onchange='activaTPRAD(1);'>Todos los Tipos?</span></td>
                                    <td ><span id='vtipoRad1' style=\"display: none\">Tipo Radicado:$slc_trad</span></td>
                                </tr>   
                            </table>
                            </td>
                            <td class=\"listado2\" >
                                <table><tr><td><span id='1' ><button type='button' onclick='agregaLinea(2);' class=\"botonmenos\"><img src=\"$ruta_raiz/img/silk/icons/add.png\" ></button></span>
                                </td><td><button type='button' onclick='eliminaLinea(this);' class=\"botonmenos\"><img src=\"$ruta_raiz/img/silk/icons/delete.png\" ></button ></td></tr></table>
                            </td>
                         </tr>";
            }
        }
}
else
{       $error = 1;
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
    <script type="text/javascript" src="<?=$ruta_raiz ?>/js/jquery.js"> </script>
<script>

function ValidarInformacion(opc)
{       var strMensaje = "Por favor ingrese las datos.";
        var bandOK=true;
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
       
    if (!bandOK)
    {
        alert(strMensaje);
        return bandOK;
    }
}

function cambia(){
    document.form1.submit();
}
var contLn=0;
function agregaLinea(idTD){
    idTD=document.getElementById("tablaPpal").rows.length;
    contLn=contLn+1;
    var tabla = document.getElementById("tablaPpal");
    var tbody = document.createElement("tbody");
    var tr = document.createElement("TR");
    var td1 = document.createElement("TD");
    td1.setAttribute("class","listado2");
    td1.innerHTML="<span id='camposapp"+idTD+"'></span>";
    var td2 = document.createElement("TD");
    td2.setAttribute("class","listado2");
    td2.innerHTML="<table><tr><td><span id='campoOrfeo"+idTD+"'></span></td><td><span id='chk["+idTD+"]' style=\"display: none\" ><input type=\"checkbox\" checked value=\"1\" name=\"todotipoR["+idTD+"]\" id=\"todotipoR["+idTD+"]\" onchange='activaTPRAD("+idTD+");'>Todos los Tipos?</span></td><td><span id='vtipoRad"+idTD+"' style=\"display:none\">Tipo Radicado: <span id='tipoRad"+idTD+"'></span></span></td></tr></table>";
    var td3 = document.createElement("TD");
    td3.setAttribute("class","listado2");
    td3.innerHTML="<table><tr><td><span id='"+idTD+"'> <button type='button' onclick='agregaLinea("+(idTD)+");' class=\"botonmenos\"><img src=\"<?php echo $ruta_raiz?>/img/silk/icons/add.png\" ></button></span></td><td><button type='button' onclick='eliminaLinea(this);' class=\"botonmenos\"><img src=\"<?php echo $ruta_raiz?>/img/silk/icons/delete.png\" ></button></td></tr></table>";
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tbody.appendChild(tr);
    tabla.appendChild(tbody);
    pedirCombo("controllerCombosAppExt.php", "camposapp"+idTD,"camposapp",idTD, document.getElementById('slc_cmb2').value);
    pedirCombo("controllerCombosAppExt.php", "tipoRad"+idTD,"tipoRad",idTD, document.getElementById('slc_cmb2').value);
    pedirCombo("controllerCombosAppExt.php", "campoOrfeo"+idTD,"campoOrfeo",idTD, document.getElementById('slc_cmb2').value);
}
var ps;
function eliminaLinea(t){
    
if(confirm("¿Est\xE1 seguro de eliminar este registro?")){
    var td1=t.parentNode;
    var tr1=td1.parentNode;
    var table1=tr1.parentNode.parentNode;
    var td=table1.parentNode;
    var tr=td.parentNode;
    var table=tr.parentNode;
    table.removeChild(tr);
    if(document.getElementById("tablaPpal").rows.length < 2){
        agregaLinea(1);
    }
}

}
function activa(id){
    document.getElementById('todotipoR['+id+']').checked=false;
    if(document.getElementById('cmbCamposOrfeo['+id+']').value=='1'){
        document.getElementById('chk['+id+']').style.display='';
        document.getElementById('todotipoR['+id+']').checked=true;
    }
    else{ 
        document.getElementById('chk['+id+']').style.display='none';
        document.getElementById('vtipoRad'+id).style.display='none';
    }
   
}
function activaTPRAD(id){
    document.getElementById('cmbTiposRad['+id+']').value=0;
     if(document.getElementById('todotipoR['+id+']').checked==true)
        document.getElementById('vtipoRad'+id).style.display='none';
    else
        document.getElementById('vtipoRad'+id).style.display='';
}
function pedirCombo(fuenteDatos, divID,tipo,id, app)
{
    $.ajax({
                url: fuenteDatos+"?"+"app="+app+"&tipo="+tipo+"&id="+id+"&ruta_fuente=<?php echo $ruta_raiz ?>",
                type:'GET',
		cache:false,
		dataType:'html',
                error: function(objeto, quepaso, otroobj){
                    alert("Estas viendo esto por que el Obj Ajax falla");
                    alert("Pasó lo siguiente: "+quepaso);
                },
                success: function(datos){
                    document.getElementById(divID).innerHTML =datos;
                }
            });
}
</script>
<title>.: Orfeo :. Campos Homologos.</title>
<link href="<?=$ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']?>">
<table width="75%" border="1" align="center" cellspacing="0" class="tablas">
<tr bordercolor="#FFFFFF">
    <td colspan="3" height="40" align="center" class="titulos4" valign="middle"><b><span class=etexto>3. HOMOLOGACI&Oacute;N DE CAMPOS</span></b></td>
</tr>
<tr bordercolor="#FFFFFF">
        <td align="left" class="titulos2"><b>Seleccione &nbsp;Aplicativo: </b></td>
            <td align="left" class="listado2">
                <?=$slc_tmp?>
        </td>
</tr>
</table>

<table width="75%" border="1" id="tablaPpal" align="center" cellspacing="0" class="tablas">
<tr bordercolor="#FFFFFF">
        <td class="titulos4" valign="middle">Campo Aplicativo</td>
        <td class="titulos4" valign="middle">Campo Orfeo</td>
        <td class="titulos4" valign="middle">Acci&oacute;n</td>
</tr>
<?php echo $TR?>
</table>

<table width="75%" border="1" align="center" cellpadding="0" cellspacing="0" class="tablas">
        <tr bordercolor="#FFFFFF">
                <td width="10%" class="listado2">&nbsp;</td>
                <td width="20%" class="listado2">
                        <span class="e_texto1"><center>
                        <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Guardar" onClick="return ValidarInformacion(this.value);">
                        </center></span>
                </td>
                <td width="10%" class="listado2">&nbsp;</td>
        </tr>
</table>
</form>
</body>
</html>