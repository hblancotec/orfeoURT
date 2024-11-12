<?php
/**
 * P�gina de firma digital
 *
 * @version 1.0
 * @author n-n
 *
 * @version 1.1
 * @author cesgomez
 */
//
// Editar este fuente en UTF-8 Encoding
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}
if (! $ruta_raiz) {
    $ruta_raiz = "..";
}
require $ruta_raiz . '/_conf/constantes.php';

if (! isset($_SESSION['dependencia'])) {
    include (ORFEOPATH . "rec_session.php");
}

/*die("	<table align='center'><tr><td><br/><br/><img src='../img/escudo.jpg' width='200px' alt='escudo' /><br/><br/><br/></td><tr><tr><td><span style='color:Red; font-weight:Bold'>Estimado Usuario:</span>
			<p>Nos encontramos en ventana de mantenimiento del proceso de firma entre el 14-08-2024 desde las 12:45 pm hasta el 14-08-2024 a 03:00 pm, por favor intentar despu&eacute;s de la ventana.</p>
			<p>Muchas gracias por su comprensi&oacute;n.</p>
			<p>Atte. Oficina de Tecnolog&iacute;a y Sistemas de Informaci&oacute;n.</p></td></tr>");*/

$array = array(
    "Identificacion" => $_SESSION["identificacion"],
    "Ciudad" => "Bogota",
    "Entidad" => "Departamento Nacional de Planeacion",
    "Direccion" => "Calle 26 No. 13-19",
    "Sistema" => "Sistema de Gestion Documental Orfeo"
);

$description = implode(',', array_keys($array)) . '/' . implode(',', array_values($array));

//
// Configuración de imágenes
// $backgroundSignImage = "E:/httpd-2.4.27-x86/htdocs/orfeo/orfeo-server/firmaHollmanDigitalizada.png";
$backgroundSignImage = " ";
$leftSignImage = " ";
?>
<html>
<head>
<title>Editar Firmantes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
<script src="<?php echo $ruta_raiz ?>/pqr2/js/jquery-1.7.1.js"></script>
<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.core.js"></script>
<script src="<?php echo $ruta_raiz ?>/pqr2/js/jquery-1.12.4.js"></script>
<script src="<?php echo $ruta_raiz ?>/pqr2/js/jquery-ui.js"></script>
<script src="../js/jquery.blockUI.js"></script>

<script type="text/javascript">

function actualizar(){location.reload(true);}

//setInterval("actualizar()",30000);

function verHistorico(idcf){
	window.open("./verHistorico.php?<?php echo session_name().'='.session_id() ?>&idcfirmante="+idcf,"Ver Historico","height=350,width=500,scrollbars=yes");
}

function regresar() {
    location.reload();
}

function cambio() {
	debugger;
	var numsel = 0;
	var idccf = 0;
	var cboxes = document.getElementsByName('checkValue[]');
    var len = cboxes.length;
    for (var i=0; i<len; i++) 
    {
    	if(cboxes[i].checked)
        {
        	idccf = cboxes[i].value;	
			numsel = numsel + 1;
    	}
    }
    if (numsel == 0) {
    	alert('No hay registros marcados para procesar.');
    } 
    else if (numsel == 1) {
        window.open("./cancelPeticion.php?<?php echo session_name().'='.session_id() ?>&idcfirmante="+idccf,"Solicitar Cambios","height=350,width=450,scrollbars=yes");
    }
    else if (numsel > 1) {
    	alert('La edici\xf3n de Cambios es por radicado, debe seleccionar uno.');
    }
}

function firmar() {
    
	var i = 0;
    var rutaPDF = '';
    var pagina = 0;
    var tipo = '';
    var msgError = '';
    var idcf = 0;
    var estado = true;
    var radicados_rechazados = "";
    var radicados_firmados   = "";
    var radicados_aprobados  = "";
            
    debugger;
	var cboxes = document.getElementsByName('checkValue[]');
    var len = cboxes.length;
    var idcfs = '';
    
    var valida = false;
    for (var i=0; i<len; i++) 
    {
    	if(cboxes[i].checked)
        {
        	valida = true;
        }
    }
    
    if (valida == false) {
    	alert('Debe seleccionar un radicado para firmar !!');
    	return;
    }
    
    for (var i=0; i<len; i++) 
    {
    	if(cboxes[i].checked)
        {       
        	var res = cboxes[i].value.split(";");
        	idcf = res[0];
       		
			if( res[1] == -1 ){
				document.getElementById("id_ajax_"+cboxes[i].value).style.display = "block";
				//document.getElementById("id_ajax_"+cboxes[i].value).style.display = "none";
				document.getElementById("mensaje_ajax_"+cboxes[i].value).innerHTML = "( Usted no puede firmar &eacute;ste documento )";
			}
			else
			{
				if (idcfs.length > 1) {
					idcfs += '|';
				}
				idcfs += idcf;
			}
		}
	}
	
	$.blockUI({ message: '<img src="../img/Procesando.gif" /> <h2> Espere un momento ...</h2>' });
				  
	$.ajax({
		url: 'validaCicloMaster.php',
	    type: 'POST',
	    async: true,
		cache: false,    				
	    data: { idCiclo: idcfs, 'krd' : '<?php echo $krd; ?>', '<?php echo session_name(); ?>' : '<?php echo session_id() ?>' },
	    success: function(respuesta) {
    					
	    	if( respuesta.estado == "-1" ){
	    		// Si no encuentra un archivo cambia el estado y agrega a la variable radicados_json que radicado generó problemas
	            estado = false;
	            alert("Error en el proceso, consulte el administrador del sistema." + respuesta.mensaje);
	        }
	    	if( respuesta.estado == "1" ){
	        	alert("Proceso finalizado correctamente !!");
			}
	    	if( respuesta.estado == "2" ){
	    		estado = false;
	    	}
	    	if( respuesta.estado == "11" ){
	    		alert(respuesta.mensaje);
	        }
	        			
	        $.unblockUI();
			window.location.reload();
	    },
	    error: function (jqXHR, textStatus, errorThrown) {
        	$.unblockUI();
			alert('Se ha producido un error' + ": " + jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
			window.location.reload();
		}
	});
}
//Ibiscom
function selecTable(){
	var x = document.getElementById("mySelect").value;
	var tabI = document.getElementById("i");
	var tabR = document.getElementById("r");
	var botonCambio = document.getElementById("btnCancelSign");
	if (x == 'radicados'){
		tabI.style.display = 'none';
		tabR.style.display = 'inline';
		botonCambio.style.display = 'inline';
	}
	if (x == 'indices'){
		tabI.style.display = 'inline';
		tabR.style.display = 'none';
		botonCambio.style.display = 'none';
	}	
	
}
</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
<?php

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
error_reporting(7);
$db = new ConnectionHandler("$ruta_raiz");
// $db->conn->debug = true;
$accion_sal = "Firmar Documentos";
$nomcarpeta = "Documentos pendientes de firma digital";
$pagina_sig = "firmarDocumentos.php";

if ($orden_cambio == 1) {
    if (! $orderTipo) {
        $orderTipo = " DESC";
    } else {
        $orderTipo = "";
    }
}

if (! $orderNo) {
    $orderNo = 0;
    $orderTipo = " desc ";
}

if (isset($_GET['busqRadicados']) && ! empty($_GET['busqRadicados'])) {
    $busqRadicados = trim($_GET['busqRadicados']);
    $textElements = explode(",", $busqRadicados);
    $newText = "";
    $dep_sel = $dependencia;
    foreach ($textElements as $item) {
        $item = trim($item);
        if (strlen($item) != 0) {
            $busqRadicadosTmp .= " r.radi_nume_radi like '%$item%' or";
        }
    }
    if (substr($busqRadicadosTmp, - 2) == "or") {
        $busqRadicadosTmp = substr($busqRadicadosTmp, 0, strlen($busqRadicadosTmp) - 2);
    }
    if (trim($busqRadicadosTmp)) {
        $whereFiltro .= "and ( $busqRadicadosTmp ) ";
    }
}

$encabezado = "" . session_name() . "=" . session_id() . "&ruta_raiz=$ruta_raiz&krd=$krd&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion&orderTipo=$orderTipo&radicado=$radicado&orderNo=";
$linkPagina = $_SERVER['PHP_SELF'] . "?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";
$carpeta = "nada";
include "../envios/paEncabeza.php";
$pagina_actual = $_SERVER['PHP_SELF'];
include "../envios/paBuscar.php";

// include "../envios/paOpciones.php";

/*
 * GENERACION LISTADO DE RADICADOS
 * Aqui utilizamos la clase adodb para generar el listado de los radicados
 * Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
 * el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
 */
// error_reporting(7);
?>
<form name='formEnviar'
		action='../firma/<?=$pagina_sig?>?<?=$encabezado?>' method='POST'>
		<table align="center">
			<tr>
				<td>
					<div align="left" class="titulo1">TIPOS DE DOCUMENTO:</div> <select
					id="mySelect" onChange="selecTable()">
						<option value="radicados" selected="selected">Radicados</option>
  						<?php
        if ($ocultaDocElectronico == 1) {
            ?>
  						    <option value="indices">Indices Electr&oacute;nicos</option>
  						<?php
        }
        ?>
					</select> &nbsp;&nbsp;&nbsp;&nbsp; <input type='button'
					value='Solicitar Cambio' name='btnCancelSign' id='btnCancelSign'
					onClick='cambio();' class='botones_largo' />
					&nbsp;&nbsp;&nbsp;&nbsp; <input type='button'
					value='Firmar Documentos' name='btnSignDocs' id='btnSignDocs'
					onClick='firmar();' class='botones_largo' />
				</td>
			</tr>
		</table>
<?php
include "$ruta_raiz/include/query/firma/queryCuerpoPendientesFirma.php";
//include "$ruta_raiz/include/query/firma/queryCuerpoPendientesFirmaIndice.php";
// $db->conn->debug = true;
$rs = $db->conn->Execute($query);
// ibiscom
//$rs2 = $db->conn->Execute($query2);
$i = 1;
$j = 1;
echo "<div id= 'r'>";
echo "<table width='100%' class='borde_tab' border='0'>";
echo "<tr><th>&nbsp;</th><th><input type='checkbox' id='selectAll' ></th><th class='titulos3'>Radicado</th><th class='titulos3'>Asunto</th><th class='titulos3'>Solicitante</th><th class='titulos3'>Firmante</th><th class='titulos3'>Fecha Solicitud</th><th class='titulos3'>Acciones</th></tr>";

if ($rs) {
    //foreach ($rs as $k => $row) {
    while ($rs && !$rs->EOF) {
        
        $estadofrima = 0;
        $display = "none";
        /*$sqlfirma = "SELECT l.[id], l.[estado] FROM log_firma l inner join SGD_CICLOFIRMADOMASTER m on l.radi_nume_radi = m.radi_nume_radi
                    WHERE l.[radi_nume_radi] = ".$rs->fields['Radicado']." and CHARINDEX(m.rutapdf, l.datos_send, 0) > 0 ";
        $rsf = $db->conn->Execute($sqlfirma);
        while ($rsf && !$rsf->EOF) {
            $estadofrima = $rsf->fields['estado'];
            $rsf->MoveNext();
        }
        if ($estadofrima == 1) {
            $display = "block";
            $texto = "Proceso Iniciado ";
            $color = "green";
        }
        elseif ($estadofrima == 2) {
            $display = "block";
            $texto = "Firma en proceso";
            $color = "red";
        }
        elseif ($estadofrima == 3) {
            $display = "block";
            $texto = "Finalizando Proceso";
            $color = "blue";
        }
        else {
            $display = "none";
        }*/
        
        $clase = ($i % 2 == 0) ? 'listado1' : 'listado2';
        if ($rs->fields['estado'] == 2)
            $clase = 'listado3';
        echo "<tr class='" . $clase . "'>" . "<td>" . $i . "</td>";
        if ($rs->fields['estado_detalle'] == 1) {
            echo "<td></td>";
        } else {
            $dnp_session = - 1;
            if ($_SESSION["login"] == $rs->fields['login']) {
                $dnp_session = 1;
            }
            echo "<td>";
            //if ($estadofrima == 1 || $estadofrima == 2 || $estadofrima == 3) {
            //    echo "";
            //} else {
                echo "<input type='checkbox' name='checkValue[]' id='" . $rs->fields['CHK_SOL_FIRMA'] . "' value='" . $rs->fields['CHK_SOL_FIRMA'] . ";" . $dnp_session . ";" . $rs->fields['Solicitante'] . ";" . $rs->fields['Firmante'] . "' />";
            //}
            echo "</td>";
        }

        echo "<td>" . $rs->fields['Radicado'] . "</td>";

        // ---------------------------------------
        // Creaci�n del elemento <span> con el id llamado = 'id_ajax_#' para visualizar el gif de procesando en el radicado especificado
        // Creaci�n del elemento <span> con el id llamado = 'mensaje_ajax_#' para agregar el texto al mensaje en el radicado especificado
        // # es el atributo CHK_SOL_FIRMA de la consulta generada
        // ---------------------------------------
        echo "<td>" . $rs->fields['Asunto'] . "<span id='id_ajax_" . $rs->fields['CHK_SOL_FIRMA'] . ";" .$dnp_session. ";".$rs->fields['Solicitante'].";".$rs->fields['Firmante'] . "' style='display:".$display."; color:".$color.";'> <img src='/imagenes/procesando.gif' title='procesando' />$texto</span><span style='color:red;' id='mensaje_ajax_" . $rs->fields['CHK_SOL_FIRMA'] . ";" .$dnp_session. ";".$rs->fields['Solicitante'].";".$rs->fields['Firmante'] . "'></span></td>";
        echo "<td>" . $rs->fields['Solicitante'] . "</td>";
        echo "<td>" . $rs->fields['Firmante'] . "</td>";
        echo "<td>" . $rs->fields['Fecha_Solicitud'] . "</td>";
        if (substr($rs->fields['HID_ruta'], - 4) == '.pdf') {
            $urlFile = "<a href='" . BODEGAURL . $rs->fields['HID_ruta'] . "' target='_blank' /><img src='/imagenes/pdf.png' title='Ver PDF' /></a>";
        } else {
            if ($_SESSION["login"] == $rs->fields['login']) {
                $urlFile = "<a href='ms-word:ofe|u|" . BODEGAURL . $rs->fields['HID_ruta'] . "' name='Editar' id='BtnEdit_" . $rs->fields['CHK_SOL_FIRMA'] . "' /><img src='/img/editPlantillaIcon.png' title='Editar' /></a>";
            } else {
                $urlFile = "<a href='" . BODEGAURL . $rs->fields['HID_ruta'] . "' name='Editar' id='BtnEdit_" . $rs->fields['CHK_SOL_FIRMA'] . "' /><img src='/img/editPlantillaIcon.png' title='Editar' /></a>";
            }
        }
        $histFile = "<img src='/imagenes/log.png' title='Hist&oacute;rico' onclick='verHistorico(" . $rs->fields['CHK_SOL_FIRMA'] . ");' /></a>";
        echo "<td>$urlFile&nbsp;$histFile</td>";
        echo "</tr>";
        ++ $i;
        
        $rs->MoveNext();
    }
}
echo "</table>";
echo "</div>";

/*if ($rs2 && ! $rs2->EOF) {
    echo "</br>";
    echo "<div id='i'>";
    echo "<table width='100%' class='borde_tab' border='0'>";
    echo "<tr><th colspan='7'>Indice Electr&oacute;nico</th></tr>";
    echo "<tr><th>&nbsp;</th><th></th><th class='titulos3'>Id Anexo</th><th class='titulos3'>Asunto</th><th class='titulos3'>Solicitante</th><th class='titulos3'>Firmante</th><th class='titulos3'>Fecha Solicitud</th><th class='titulos3'>Acciones</th></tr>";
    // Ibiscom
    foreach ($rs2 as $k => $row) {
        $clase = ($j % 2 == 0) ? 'listado1' : 'listado2';
        if ($row['estado'] == 2)
            $clase = 'listado3';
        $queryExp = "SELECT SGD_EXP_NUMERO AS e FROM SGD_ANEXOS_EXP WHERE ANEXOS_EXP_ID = " . $row['indice'] . " ";
        $execQuery = $db->conn->Execute($queryExp)->fields['e'];

        echo "<tr class='" . $clase . "'>" . "<td>" . $j . "</td>";
        echo "<td><input type='checkbox' name='checkValue' value='" . $row['CHK_SOL_FIRMA'] . "' /></td>";
        echo "<td>" . $row['indice'] . "</td>";
        echo "<td>" . "Indice Electr&oacute;nico, Expediente " . $execQuery . "</td>";
        echo "<td>" . $row['Solicitante'] . "</td>";
        echo "<td>" . $row['Firmante'] . "</td>";
        echo "<td>" . $row['Fecha_Solicitud'] . "</td>";
        if (substr($row['HID_ruta'], - 4) == '.pdf') {
            $urlFile = "<a href='" . BODEGAURL . $row['HID_ruta'] . "' target='_blank' /><img src='/imagenes/pdf.png' title='Ver PDF' /></a>";
        } else {
            $urlFile = "<a href='ms-word:ofe|u|" . BODEGAURL . $row['HID_ruta'] . "' name='Editar' id='BtnEdit_" . $row['CHK_SOL_FIRMA'] . "' /><img src='/img/editPlantillaIcon.png' title='Editar' /></a>";
        }
        $histFile = "<img src='/imagenes/log.png' title='Hist&oacute;rico' onclick='verHistorico(" . $row['CHK_SOL_FIRMA'] . ");' /></a>";
        echo "<td>$urlFile&nbsp;$histFile</td>";
        echo "</tr>";
        ++ $j;
    }
    echo "</table>";
    echo "</div>";
}*/
?>
</form>
</body>
</html>