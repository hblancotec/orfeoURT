<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}

require_once ("include/db/ConnectionHandler.php");
include_once ("include/combos.php");
include_once ("class_control/usuario.php");
$encabezado = "krd=$krd";

if (! $db) {
    $db = new ConnectionHandler('.');
}


$expedienteIn = htmlspecialchars($_GET['expediente']);

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objUsuario = new Usuario($db);

$db2 = new ConnectionHandler('.');

if (! $dependencia) {
    include "./rec_session.php";
}
$depelist = $_SESSION['dependencia'];
$textoSeleccionadoDefecto = "----- seleccione -----";
$valorSelecionadoDefecto = "null";
if (strlen($asesor)) {
    $tipDocAsesor = sacarConsecutivoDelRus($asesor);
    $docAsesor = sacarYearDelRus($asesor);
    $asesor2 = traerNombreUsuarioDocto($tipDocAsesor, $docAsesor);
} else {
    $asesor = $valorSelecionadoDefecto;
    $asesor2 = $textoSeleccionadoDefecto;
} 

if ($db2) {
    // query y procedimiento para comprobar que existe El indice
    $querySelectIndice = "SELECT ANEXOS_EXP_ID, ANEXOS_EXP_PATH
                            FROM SGD_ANEXOS_EXP
                            WHERE SGD_EXP_NUMERO = '" . $expedienteIn . "'
                            AND ANEXOS_EXP_NOMBRE like '%indice_%'
                            AND ANEXOS_EXP_DESC like '%Indice ElectrOnico, Exp: %'
                            AND ANEX_TIPO_CODI = 7 ";

    $idIndice = $db2->conn->Execute($querySelectIndice)->fields['0'];
    $pathIndice = $db2->conn->Execute($querySelectIndice)->fields['1'];   
    
    if($idIndice){
        ?>
<html>
<head>
<title>Solicitud Firmas - ORFEO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../estilos/orfeo.css">
<script language="JavaScript" type="text/JavaScript">
	    <?php
        $objUsuario->comboUsDepsWhr($deps, " and usuario.USUA_ESTA = 1 and usuario.USUA_PERM_FIRMA in (1,3) ");
        ?>
        function fYaseleccionado(valor) {
        	n=0;
        	swSeleccionado = false;
        	for (n=0;n<document.formFirmantes.elements['firmas[]'].length;n++ ) {
        		if (document.formFirmantes.elements['firmas[]'][n].value == valor)
        			swSeleccionado = true;
        	}
        	return swSeleccionado;
        }

        function fAgregar (opcion) {
        	o = new Array;
        	j=0;
        	
        	if (opcion=='AGREGAR_TODO') {
        		for (i=0;i<document.formFirmantes.elements['usuarios[]'].length;i++ ) {
        			document.formFirmantes.elements['usuarios[]'][i].selected = true;
        		}
        	}
        	
        	for (i=0;i<document.formFirmantes.elements['usuarios[]'].length;i++ ) {
        		if (document.formFirmantes.elements['usuarios[]'][i].selected) {
        			if (!fYaseleccionado(document.formFirmantes.elements['usuarios[]'][i].value)) {
        				o[j]=new Option(document.formFirmantes.elements['usuarios[]'][i].text, document.formFirmantes.elements['usuarios[]'][i].value);
        				j++;
        			}
        		}
        	}
        	largestwidth=0; 
        	if (document.formFirmantes.elements['firmas[]'][0].value =='null'){
        		longFirmantes = 0;
        		for (i=0; i < o.length; i++) {
            		eval(document.formFirmantes.elements['firmas[]'].options[longFirmantes]=o[i]);	
            		if (o[i].text.length > largestwidth) {
            			largestwidth=o[i].text.length;
            		}
            		longFirmantes++;
            	}
            	document.formFirmantes.elements['firmas[]'].length=longFirmantes;
            	evaluarRemover();
        	}else{
            	alert("El usuario firmante ya fue seleccionado.");
        	}      	
        }

        function fRemover (opcion) {
        	o = new Array;
        	j=0;
        	if (opcion=='REMOVER_TODO') {
        		for (i=0;i<document.formFirmantes.elements['firmas[]'].length;i++ ) {
        			document.formFirmantes.elements['firmas[]'][i].selected = true;
        		}
        	}
        	
        	for (i=0;i<document.formFirmantes.elements['firmas[]'].length;i++ ) {
        		if (!document.formFirmantes.elements['firmas[]'][i].selected) {
        			o[j]=new Option(document.formFirmantes.elements['firmas[]'][i].text, document.formFirmantes.elements['usuarios[]'][i].value);
        			j++;
        		}
        	}
        	largestwidth=0; 
        	longFirmantes = 0;
        	
        	if (o.length==0){
            	o[longFirmantes]=new Option('----- Sin datos -----', 'null');
            }
        			
        	for (i=0; i < o.length; i++) {
        		eval(document.formFirmantes.elements['firmas[]'].options[longFirmantes]=o[i]);	
        		if (o[i].text.length > largestwidth) {
        			largestwidth=o[i].text.length;
        		}
        		longFirmantes++;
        	}
        	document.formFirmantes.elements['firmas[]'].length=longFirmantes;
        	evaluarRemover();	
        }

        function evaluarRemover () {
         	j=0;
         	for (i=0;i<document.formFirmantes.elements['firmas[]'].length;i++ ) {
        		if (document.formFirmantes.elements['firmas[]'][i].selected && document.formFirmantes.elements['firmas[]'][i].value !='null')
        			j++;
        	}
        	if (j>0) 
        		document.formFirmantes.elements['remover'].disabled = false; 
        	else
        		document.formFirmantes.elements['remover'].disabled = true;
        		
        	if (document.formFirmantes.elements['firmas[]'][0].value !='null') 
        		document.formFirmantes.elements['removerTodo'].disabled = false;
        	else
        		document.formFirmantes.elements['removerTodo'].disabled = true;
        }

        function seleccionados(){
        	k=0;
        	for (i=0;i<document.formFirmantes.elements['usuarios[]'].length;i++ ) {
        		if (document.formFirmantes.elements['usuarios[]'][i].selected && document.formFirmantes.elements['usuarios[]'][i].value !='null')
        			k++;
        	}
        	return (k);
        }

        function firmantes(){
        	k=0;
        	for (i=0;i<document.formFirmantes.elements['firmas[]'].length;i++ ) {
        		if (document.formFirmantes.elements['firmas[]'][i].selected && document.formFirmantes.elements['firmas[]'][i].value !='null')
        			k++;
        	}
        	return (k);
        }


        function evaluarAgregar () {
        	j = seleccionados();
        	if (j>0) 
        		document.formFirmantes.elements['agregar'].disabled = false; 
        	else
        		document.formFirmantes.elements['agregar'].disabled = true;

        	
        	if (document.formFirmantes.elements['usuarios[]'][0].value !='null')
        		document.formFirmantes.elements['agregarTodo'].disabled = false;
        	else
        		document.formFirmantes.elements['agregarTodo'].disabled = true;   
        }



        function blanquearBotones() {
            evaluarRemover();
            document.formFirmantes.agregarTodo.disabled = true;
            document.formFirmantes.remover.disabled = true;
            document.formFirmantes.agregar.disabled = true;
            if (document.formFirmantes.elements['firmas[]'].length == 1 && document.formFirmantes.elements['firmas[]'].options[0].value=='null') 
            	document.formFirmantes.removerTodo.disabled = true;
            else
            	document.formFirmantes.removerTodo.disabled = false;
            if (document.formFirmantes.elements['usuarios[]'].length == 1 && document.formFirmantes.elements['usuarios[]'].options[0].value=='null') 
            	document.formFirmantes.agregarTodo.disabled = true;
            else if (document.formFirmantes.asesor.value!='null')
            	document.formFirmantes.agregarTodo.disabled = false;
        }


        function enviar() {
        	for (i=0;i<document.formFirmantes.elements['firmas[]'].length;i++ ) {
        		document.formFirmantes.elements['firmas[]'][i].selected = true;
        	}
        	
        	j=firmantes();
        	if (j==0){
        		alert ('Debe seleccionar uno o mas usuarios firmantes');	
        		return;
        	}
        	document.formFirmantes.submit();
        }
        </script>
</head>
<body>
	<form
		action="/firmantesRegistroIndice.php?ruta_raiz=<?=$encabezado?>&anexIndice=<?=$idIndice?>"
		method="post" name="formFirmantes" id="formFirmantes">
		<input type="hidden" name="radicados" value="<?=$idIndice?>">
		<input type="hidden" name="path" value="<?=$pathIndice?>">
		<table border="0" cellpadding="0" cellspacing="2">
			<tr align="center">
				<td colspan="4" class="titulos4">SOLICITUD DE FIRMA PARA INDICE ELETRÓNICO</td>
			</tr>
			<tr>
				<td colspan="4" class="titulos2" align="center" height="24"> EXPEDIENTE: <?=$expedienteIn?></td>
			</tr>
			<tr>
				<td class="titulos4" >Dependencia</td>
				<td  ><select name="depelist" style="width:255px" onChange="comboUsuarioDependencia(document.formFirmantes,this.value,'usuarios[]');blanquearBotones();">
          <?php
        $a = new combo($db);
        $s = "select * from dependencia where dependencia_estado=2 order by DEPE_NOMB asc ";
        $r = "DEPE_CODI";
        $t = "DEPE_NOMB";
        $sim = 0;
        $v = $depelist;
        $a->conectar($s, $r, $t, $v, $sim, $sim);
        ?>
        			</select></td>
				<td>&nbsp;</td>
				<td align="center" class="titulos4">Solicitar Firmas de...</td>
			</tr>
			<tr>
				<td rowspan="4" align="center" class="titulos4">Usuarios</td>
				<td rowspan="4"><select name="usuarios[]" size="10" multiple
					class="select" id="select3" onChange="evaluarAgregar();">
						<option value="null">----------------- Sin datos -----------------</option>
				</select></td>
				<td align="left"><input name="agregar" type="button" disabled="true"
					class="botones_mediano" id="agregar"
					onClick="fAgregar('AGREGAR_DETALLE');"
					value="Agregar  &gt;" accept=""></td>
				<td width="202" rowspan="4"><font color="#0000ff"> <select
						name="firmas[]" size="10" multiple class="select" id="select4"
						onChange="evaluarRemover();">
							<option value="null">----------------- Sin datos -----------------</option>
					</select>
				</font></td>
			</tr>
			<tr>
				<td height="30" align="left" width="118">
					<input name="remover" type="button" disabled="true" class="botones_mediano" id="remover" onClick="fRemover('REMOVER_TODO');" value="Remover   &lt;        ">
				</td>
			</tr>				
		</table>
		<br>
		<table border="0"  align="center">
		
				<tr>
				<td height="12" colspan="4" align="center"><input name="Actualizar"
					type="button" class="botones" id="removerTodo3" onClick="enviar();"
					value="Actualizar ">&nbsp;&nbsp;&nbsp;&nbsp; <input name="Cancelar"
					type="button" class="botones" id="Cancelar"
					onClick="window.close();" value="Cancelar "></td>
				</tr>
		</table>
	</form>
	<script language="JavaScript" type="text/JavaScript">
		comboUsuarioDependencia(document.formFirmantes,<?=$depelist?>,'usuarios[]');
		evaluarAgregar();
	</script>
</body>
</html>
<?php
    } else {
        echo ("Este expediente no tiene Indice Electrónico.");
    }
}