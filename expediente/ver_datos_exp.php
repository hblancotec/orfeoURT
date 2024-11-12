<?php
$ruta_raiz = ".."; 
session_start();
$dependencia = $_SESSION['dependencia'];
$codusuario = $_SESSION['codusuario'];
if (!isset($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once ("$ruta_raiz/include/tx/Expediente.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$db->conn->debug = false;
$expediente = new Expediente($db);

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);

if ($_POST['numExp']){
	$numeroExpediente = $_POST['numExp'];
}
if ($numeroExpediente) {
    $expediente->getExpediente($numeroExpediente);
}
if($_POST['Actualizar']){
	
	### COMPARA LOS DATOS QUE TENIA EL EXPEDIENTE VS LOS DATOS QUE VIENEN DEL FORMULARIO
	$cons = "SELECT	SGD_SEXP_PAREXP1,
					SGD_SEXP_PRIVADO,
                        SGD_SEXP_FECH,
                        USUA_DOC_RESPONSABLE,
                        SGD_SEXP_CERRADO
			 FROM	SGD_SEXP_SECEXPEDIENTES
			 WHERE  SGD_EXP_NUMERO = '".$_POST['numExp']."'";
	$rsCon = $db->conn->Execute($cons);
	
	$observa = "Se actualizan los siguientes datos: ";
	
	### SE CONSULTA SI EL NOMBRE CAMBIA
	if ($rsCon->fields['SGD_SEXP_PAREXP1'] != $_POST['nomExp']){
        $set[] = "SGD_SEXP_PAREXP1 = '" . $_POST['nomExp'] . "'";
		$observa .= "Nombre [".$_POST['nomExp']."]; ";
	}
	
    ### SE CONSULTA SI EL RESPONSABLE CAMBIA
    if(isset($_POST['slcUsua']))
    {
        if ($rsCon->fields['USUA_DOC_RESPONSABLE'] != $_POST['slcUsua']) {
            $set[] = "USUA_DOC_RESPONSABLE = '" . $_POST['slcUsua'] . "'";
            $observa .= "Responsable [" . $_POST['slcUsua'] . "]; ";
        }
    }
    
     ### SE CONSULTA SI EL ESTADO CAMBIA
    if ($rsCon->fields['SGD_SEXP_CERRADO'] != $_POST['chkCierre']) {
        $estado=(!$_POST['chkCierre'])?0:$_POST['chkCierre'];
        $set[] = "SGD_SEXP_CERRADO = " .$estado . "";
        $set[] = "SGD_SEXP_FECHACIERRE = " . $db->conn->DBDate(Date('Y-m-d')) . "";
        if($_POST['chkCierre']==1)
        {
            $observa .= "Estado Expediente [Cerrado]; ";
            //Se valida si el expediente no tiene radicados por ubicar fisicamente.
            
            
            
            
            
        }
        else{
            $observa .= "Estado Expediente [Abierto]; ";
        }
    }
    
	### SE CONSULTA SI EL ESTADO DE PRIVACIDAD CAMBIA
	if ($rsCon->fields['SGD_SEXP_PRIVADO'] != $_POST['seguridad']){
        $set[] = "SGD_SEXP_PRIVADO = " . $_POST['seguridad'];
		if($_POST['seguridad'] == 1){
			$est = "Privado";
        } else {
			$est = "Publico";
		}
		$observa .= "Estado [".$est."]; ";
	}
	
    if (count($set) > 0) {
	### SE ACTULIZAN LOS DATOS DEL EXPEDIENTE
	$sqlUp = "	UPDATE	SGD_SEXP_SECEXPEDIENTES
                        SET	" . implode(",", $set) . "
				WHERE	SGD_EXP_NUMERO = '".$_POST['numExp']."'";
	$rsUp = $db->conn->Execute($sqlUp);
	if ($rsUp){
		
		$fec = date("Y/m/d H:i:s");
		### SE REGISTRA EN EL HISTORICO DEL EXPEDIENTE
		$slqIns = "	INSERT INTO SGD_HFLD_HISTFLUJODOC
						(	SGD_FEXP_CODIGO,
							SGD_HFLD_FECH,
							SGD_EXP_NUMERO,
							USUA_DOC,
							USUA_CODI,
							DEPE_CODI,
							SGD_TTR_CODIGO,
							SGD_HFLD_OBSERVA)
					VALUES
						(	0,
							'".$fec."',
							'".$numExp."',
							'".$_SESSION['usua_doc']."',
							".$_SESSION['codusuario'].",
							".$_SESSION['dependencia'].",
							98,
							'".$observa."')";
		
		$rsIns = $db->conn->Execute($slqIns);
	}
}
}
if ($numeroExpediente) {
    $expediente->getExpediente($numeroExpediente);
}

$sqlE = "SELECT	S.SGD_SEXP_PAREXP1,
				S.SGD_SEXP_FECH,
				S.SGD_SEXP_PRIVADO
		FROM	SGD_SEXP_SECEXPEDIENTES S
		WHERE	S.SGD_EXP_NUMERO = '".$numeroExpediente."'";
$rsE = $db->conn->Execute($sqlE);

if($rsE){
	$nomExp = $rsE->fields['SGD_SEXP_PAREXP1'];
	$fecha = $rsE->fields['SGD_SEXP_FECH'];
	if($rsE->fields['SGD_SEXP_PRIVADO'] == 1){
		$pri = "Selected";
		$pub = "";
    } else {
		$pri = "";
		$pub = "Selected";
	}
}
//Se validan los permisos para asignar responsable del usuario que modifica los datos del Expdiente.
$depResponsable = $_SESSION['dependencia'];
$usuaResponsable = $expediente->responsable;
if ($_SESSION["usuaPermExpediente"] == 3) {
    $depResponsable = $expediente->depeResponsable;
    //crea combo de dependencias
    $cad = $db->conn->Concat("CAST(DEPE_CODI as VARCHAR)", "'-'", "DEPE_NOMB");
    $sql = "select $cad , DEPE_CODI from dependencia d where dependencia_estado=2 ORDER BY 1";
    $rs = $db->conn->Execute($sql);
    $selectDep = $rs->GetMenu2('selectDependencia', $depResponsable, false, false, false, " id='selectDependencia' class='select' onchange=\"pedirComboUsu('$ruta_raiz/cCombos.php','divUsu','usuarios',this.value,'');\"");
} else if($_SESSION["usuaPermExpediente"] <= 1){
    $wUsuario= " and USUA_DOC='".$_SESSION['usua_doc']."'";
}

//crea combo de Usuarios
$sql = "select USUA_NOMB AS USUARIO, USUA_DOC AS CODIGO from USUARIO where depe_codi=" . $depResponsable . " $wUsuario ORDER BY 1";
$rs = $db->conn->Execute($sql);
$selectUsua = $rs->GetMenu2('slcUsua', $usuaResponsable, "0:Seleccione un Usuario", false, false, " id='slcUsua' class='select' onChange='verUsuarios();' ");
?>

<html>
	<head>
		<title>MODIFICAR DATOS DEL EXPEDIENTE <?=$exp?></title>
		<link rel="stylesheet" href="../estilos/orfeo.css">
        <script type="text/javascript" src="<?php echo $ruta_raiz ?>/js/ajax.js"></script>
		
		<script language="javascript">
			<?php
				$fecha_busq = date("Y/m/d") ;
				if(!$fecha) { 
					$fecha = $fecha_busq;
				}
			?>
           
		</script>
		
		<script>
            var estadoExp=<?php echo $expediente->estadoExp?>;
            
			function regresar(){
				window.location.reload();
				window.close();
			}
            function pedirComboUsu(fuenteDatos, divID, tipo, dep, usu)
            {
                if (xmlHttp)
                {
                    // obtain a reference to the <div> element on the page
                    divAutilizar = document.getElementById(divID);
                    try
                    {
                        xmlHttp.open("GET", fuenteDatos + "?tipo=" + tipo + "&dep=" + dep + "&usu=" + usu + "&ruta_fuente=<?php echo $ruta_raiz ?>&campoValue=1");
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
                if (xmlResponse && divAutilizar)
                    divAutilizar.innerHTML = xmlResponse;

            }
            
            function validar(){
                
                var band=true;
                var msg="";
                if(document.getElementById('slcUsua').value==0){
                   band=false;
                   msg="Debe seleccionar un usuario responsable!";
                }
                if((!estadoExp || estadoExp==0) && document.getElementById('chkCierre').checked==true){
                    
                    if(!confirm("\xBFEst\xE1 seguro de cerrar el Expediente?")){
                        band=false;
                        msg="Si no desea cerrar el Expediente porfavor desmarque la casilla!";
                    }
                }
                if((estadoExp==1) && document.getElementById('chkCierre').checked==false){

                    if(!confirm("\xBFEst\xE1 seguro de reabrir el Expediente?")){
                        band=false;
                        msg="Si no desea reabrir el Expediente porfavor marque la casilla!";
                    }
                }
                if(!band){
                    alert(msg);
                }
                return band;
            }
            
            function cerrarExp(){
                <?php if(!$expediente->permiteReabrir){?> 
                if(document.getElementById('chkCierre').checked==false){
                    document.getElementById('chkCierre').checked=true;
                    alert("No se permite reabir el Expediente, comuniquese con el Administrado.");
                }
                <?php }?>
                if((!estadoExp || estadoExp==0) && document.getElementById('chkCierre').checked==true){
                    
                    alert("El Expediente se cerrar\xE1 y no podr\xE1 reabrirse si la TRD no lo permite.");
                }
                if((estadoExp==1) && document.getElementById('chkCierre').checked==false){
                    
                    alert("El Expediente se reabrir\xE1.");
                }
            }
		</script>
		
	</head>
	<body>
		<form name="formulario" method=post action='./ver_datos_exp.php?<?=session_name()."=".trim(session_id())."&krd=$krd"?>'>
			<input type="hidden" name='numExp' id='numExp' value="<?=$numeroExpediente?>" >
			<table width="100%" border="0" cellpadding="0" cellspacing="3" align="center">
				<tr bgcolor="#006699">
					<td class="titulos4" colspan="2" >
						<center> MODIFICAR DATOS DEL EXPEDIENTE <?=$numeroExpediente?>  </center>
					</td>
				</tr>
				
				<tr>
					<td class="titulos2" width="20%"> Nombre del Expediente: </td>
					<td class="listado2" width="80%"> <input type="text" name="nomExp" id="nomExp" value="<?php echo $nomExp; ?>" size="100" maxlenght="300" required>  </td>
				</tr>
<?php if ($selectDep) { ?>
				<tr>
                        <td class="titulos2" >Dependencia:</td>
                        <td class="listado2"><?php echo $selectDep; ?></td>
                    </tr>
<?php } ?>
                <tr>
                    <td class="titulos2" >Responsable:</td>
                    <td class="listado2"> <div id="divUsu"><?php echo $selectUsua; ?></div></td>
                </tr>
                <tr>
                    <td class="titulos2" >Cerrar Expediente:</td>
                    <td class="listado2"><input  type='checkbox' name='chkCierre' id='chkCierre' value='1' <?php if($expediente->estadoExp){ echo "checked";}else{ echo "";} ?> onclick="cerrarExp();">
                    <?php if($expediente->estadoExp) echo "<b>Fecha de cierre</b>: ". $expediente->fechaCierre;?>
					</td>
				</tr>

				<tr>
					<td class="titulos2" width="20%"> Nivel de seguridad: </td>
					<td class="listado2" width="80%">  

						<select name="seguridad" id="seguridad" class="select">
							<option value="0" <?= $pub ?>>P&uacute;blico</option>
							<option value="1" <?= $pri ?>>Privado</option>
						</select>

					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
                        <input type="submit" class="botones_funcion" name="Actualizar" value="Actualizar" onclick="return validar();"> &nbsp; &nbsp; &nbsp;
                        <input name="Cerrar" type="button" class="botones_funcion" id="envia22" onClick="opener.regresar();
                window.close();" value="Cerrar">
					</td>
				</tr>
				
				
<?php
	if ($rsUp){
?>		
				<tr>
					<td colspan="2" align="center" style="color: #ff0000;">
						Registro actualizado correctamente
					</td>
				</tr>
<?php		
	}
?>
			</table>
		</form>
	</body>
</html>