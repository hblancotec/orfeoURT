<?php
session_start();
if (is_array($_SESSION) and count($_SESSION)>0){
	if ($_SESSION["tpPerRad"][2] < 1) {
		die('No tiene acceso a este m&oacute;dulo.');
	}
} else {
	die('Sesion expirada o acceso prohibido.');
}
/**
 *
 * Funcion que retorna el numero de byte de una cantidad expresada en Kb, Mb, Gb. P.E. 5K 6M 7G
 * @param integer $val
 */
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/*
 * Se pre-crea el combo de temas debido a que debe estar dibujado a primera 
 * instancia en HTML para poder preseleccionar la opcion de Regalias.
 */

require 'scripts/clases/class.sqlsrv.php';
$objMssql = new SQLSRV("..");
$rs = $objMssql->consulta("SELECT SGD_DCAU_DESCRIP, SGD_DCAU_CODIGO, SGD_DCAU_PQRDESC FROM SGD_DCAU_CAUSAL WHERE SGD_DCAU_PQR=1 ORDER BY SGD_DCAU_CODIGO");
$helpTemas = "";
$cmbTemas = "<select name='cmbTema' id='cmbTema' required>";
$cmbTemas.= "<option value=''>Seleccione Tema</option>";
while ($vector = $objMssql->fetch_assoc($rs)) {
    $cmbTemas .= "<option value='" . $vector['SGD_DCAU_CODIGO'] . "'>" . utf8_encode($vector['SGD_DCAU_DESCRIP']) . "</option>";
    $helpTemas .= "<b>" . utf8_encode($vector['SGD_DCAU_DESCRIP']) . "</b><br/>" . utf8_encode($vector['SGD_DCAU_PQRDESC']) . "<br/>";
}
$cmbTemas .= "</select>";

/**
 * Traemos los tipos de archivos permitidos para cargar
 */
$opcExt = "";
$rs = $objMssql->consulta("SELECT ANEX_TIPO_EXT FROM ANEXOS_TIPO WHERE ANEX_TIPO_PQR=1 ORDER BY ANEX_TIPO_EXT");
while ($vector = $objMssql->fetch_assoc($rs)) {
    $opcExt .= $vector['ANEX_TIPO_EXT'] . "|";
}
$opcExt = substr($opcExt, 0, strlen($opcExt) - 1);

/**
 * Aprovechando el desorden ... precargamos los departamentos.
 */
$rs = $objMssql->consulta("SELECT DPTO_NOMB, DPTO_CODI FROM DEPARTAMENTO WHERE ID_CONT=1 AND ID_PAIS=170 ORDER BY DPTO_NOMB");
$cmbDptos = "<select name='cmbDpto' id='cmbDpto' required>";
$cmbDptos.= "<option value=''>Seleccione Departamento</option>";
while ($vector = $objMssql->fetch_assoc($rs)) {
    $cmbDptos .= "<option value='" . $vector['DPTO_CODI'] . "'>" . utf8_encode($vector['DPTO_NOMB']) . "</option>";
}
$cmbDptos .= "</select>";

/**
 * y los tipos de identificacion
 */
$rs = $objMssql->consulta("SELECT TDID_DESC, TDID_CODI FROM TIPO_DOC_IDENTIFICACION ORDER BY TDID_DESC");
$cmbtipoDoc = "<select name='tipoDoc' id='tipoDoc' required >";
$cmbtipoDoc.= "<option value=''>Seleccione Tipo Documento</option>";
while ($vector = $objMssql->fetch_assoc($rs)) {
    $cmbtipoDoc .= "<option value='" . $vector['TDID_CODI'] . "'>" . utf8_encode($vector['TDID_DESC']) . "</option>";
}
$cmbtipoDoc .= "</select>";

/**
 * y las dependencias
 */
$rs = $objMssql->consulta("SELECT DEPE_NOMB, DEPE_CODI FROM DEPENDENCIA WHERE DEPENDENCIA_ESTADO = 2 ORDER BY DEPE_NOMB");
$cmbDependencia = "<select name='cmbDependencia' id='cmbDependencia' required >";
$cmbDependencia.= "<option value=''>Seleccione Dependencia</option>";
while ($vector = $objMssql->fetch_assoc($rs)) {
    $cmbDependencia .= "<option value='" . $vector['DEPE_CODI'] . "'>" . utf8_encode($vector['DEPE_NOMB']) . "</option>";
}
$cmbDependencia .= "</select>";

/**
 * La informacion poblacional.
 * ALERTA!!!! Se ordena por codigo dado que se necesita un orden específico para mostrar los registros
 * Y repito Y como no se guarda relacion radicado-informacion poblacional entonces no hay problema en
 * ordenar por codigo.
*/
$rs = $objMssql->consulta("SELECT SGD_INFPOB_DESC, ID_INFPOB FROM SGD_INF_INFPOB WHERE SGD_INFPOB_ACTIVO=1 ORDER BY ID_INFPOB");
$cmbRazas = "<select name='cmbRazas' id='cmbRazas' required>";
$cmbRazas.= "<option value=''>Seleccione Informacion Poblacional</option>";
while ($vector = $objMssql->fetch_assoc($rs)) {
    $cmbRazas .= "<option value='" . $vector['ID_INFPOB'] . "'>" . utf8_encode($vector['SGD_INFPOB_DESC']) . "</option>";
}
$cmbRazas .= "</select>";

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>.: PQR :. Departamento Nacional de Planeaci&oacute;n.</title>
        <noscript lang="JavaScript">
            <META HTTP-EQUIV="Refresh" CONTENT="0;URL=index_nojs.php">
        </noscript>

        <!-- CSS del propio sitio -->
        <link href="css/pqr.css" rel="stylesheet" type="text/css" />
		<style type="text/css">
			<!--
			fieldset .content {
				display: none;
			}
			-->
		</style>

        <link rel="stylesheet" href="js/themes/redmond/jquery-ui-custom.css"/>
        <link rel="stylesheet" href="css/jquery.alerts.css" type="text/css" media="screen" />
        <script type="text/javascript" src="js/jquery-1.7.1.js"></script>
        <script type="text/javascript" src="js/jquery.maxlength-min.js"></script>
        <script type="text/javascript" src="js/jquery.tooltip.js"></script>
        <script type="text/javascript" src="js/jquery.alerts.js"></script>
        <script type="text/javascript" src="js/ui/jquery-ui.js"></script>
        <script type="text/javascript" src="js/ui/datepicker-es.js"></script>
        <script type="text/javascript" src="js/vendor/jquery.ui.widget.js" ></script>
        <script type="text/javascript" src="js/jquery.MultiFile.js"></script>
        <script type="text/javascript" src="../js/formchek.js"></script>
  
        <script type="text/javascript" src="/pqrverbal/js/RecordRTC.js"></script>
		<script type="text/javascript" src="/pqrverbal/js/gif-recorder.js"></script>
		<script type="text/javascript" src="/pqrverbal/js/getScreenId.js"></script>
		<!-- for Edige/FF/Chrome/Opera/etc. getUserMedia support -->
		<script type="text/javascript" src="/pqrverbal/js/gumadapter.js"></script>
        
        <script type="text/javascript" language="javascript">
            $(document).ready(function() {

                $("#cmbDpto").change(function() {
                cargar_ciudades();
                });
                $("#cmbTema").change(function() {
                cargar_solicitudes();
                });
                $("#cmbDependencia").change(function() {
                	cargar_usuarios();
                });
                    $("#txtCorreo").blur(function() {
                validar_email(true);
                });
                $("#tbusqueda").change(function() {
                changeLabels();
                });

                $("#txtFechExpDoc").datepicker({changeMonth: true, changeYear: true});

                $("#MyFileUpload").MultiFile({
                list: '#listaAnexos',
                max: 5,
                accept: '<?php echo $opcExt; ?>',
                STRING: {
                    remove: '<img src="./images/del2.png" height="16" width="16" alt="x" border="0"/>',
                    selected: 'Selecionado: $file',
                    denied: 'La extensi\xf3n $ext no es permitida!',
                    file: '<em title="Clic para eliminar" onclick="$(this).parent().prev().click()">$file</em>',
                    duplicate: 'Archivo $file ha sido previamente seleccionado!'
                }
                });

                $("#cmbDpto").each(function() {
                var i = 0;
                var sel = this;
                for (i = 0; i < sel.length; i++) {
                    sel.options[i].title = "";
                    sel.options[i].title = sel.options[i].text;
                }
                });

                $("#txtComentario").maxlength(
                    {
                        events: [], // Array of events to be triggerd   
                        maxCharacters: 280, // Characters limit  
                        status: true, // True to show status indicator below the element   
                        statusClass: "status", // The class on the status div 
                        statusText: "caracteres faltan", // The status text 
                        notificationClass: "notification", // Will be added when maxlength is reached 
                        showAlert: true, // True to show a regular alert message   
                        alertText: "Haz llegado al l\xedmite.", // Text in alert message  
                        slider: true // True Use counter slider   
                    }
                );

                $("#cmbDpto").attr('onmouseover', function() {
                this.title = this.options[this.selectedIndex].text;
                });

                $("#respCE").click(function() {
                    $("#txtCorreo").prop('required', true);
						$("#cmbDpto").prop('required', false);
						$("#cmbMcpio").prop('required', false);
						$("#txtDireccion").prop('required', false);
						$("#cmbMcpio").prop('required', false);
                });

                $("#respDF").click(function() {
                    $("#txtCorreo").prop('required', false);
					$("#cmbDpto").prop('required', true);
					$("#cmbMcpio").prop('required', true);
					$("#txtDireccion").prop('required', true);
                });

                $("#frm_pqr").submit(function() {
                    var exito = true;
                    var msg = "";

                    //validacion campo Nombre
                    if (isWhitespace($('#txtNombre').get(0).value)) {
                        exito = false;
                        msg = msg + "Campo Nombre no diligenciado.\n";
                    }

                        //validacion campo Apellidos
                        if (isWhitespace($('#txtApellido').get(0).value)) {
                            exito = false;
                            msg = msg + "Campo Apellidos no diligenciado.\n";
                        }

                        //validacion campo Documento
                        if (isWhitespace($('#txtDocumento').get(0).value)) {
                            exito = false;
                            msg = msg + "Campo No. Documento no diligenciado.\n";
                        }

                      	//validacion campo Departamento
                        if ($('#cmbDpto option:selected').val() == '') {
                            exito = false;
                            msg = msg + "Campo Departamento no diligenciado.\n";
                        }

                      	//validacion campo Municipio
                        if ($('#cmbMcpio option:selected').val() == '') {
                            exito = false;
                            msg = msg + "Campo Municipio no diligenciado.\n";
                        }
                        
                        //validacion campo inf. Poblacional
                        if ($('#cmbRazas option:selected').val() == '') {
                            exito = false;
                            msg = msg + "Campo Informaci\xf3n Poblacional no diligenciado.\n";
                        }
                        //validacion campo Tema
                        if ($('#cmbTema option:selected').val() == '') {
                            exito = false;
                            msg = msg + "Campo Tema no diligenciado.\n";
                        }
                        //validacion campo Tipo Solicitud
                        if ($('#cmbSolictud option:selected').val() == '') {
                            exito = false;
                            msg = msg + "Campo Tipo Solicitud no diligenciado.\n";
                        }
                        //validacion campo Comentario
                        if (isWhitespace($('#txtComentario').get(0).value)) {
                            exito = false;
                            msg = msg + "Campo Comentario no diligenciado.\n";
                        }

                        //Validacion prefencia forma envio.
                        if (!$('#respCE').is(':checked') && !$('#respDF').is(':checked')) {
                            exito = false;
                            msg = msg + "Debe seleccionar preferencia en forma de env\xedo para la respuesta.\n";
                        }

                        if (exito == false) {
                            alert(msg);
                        }
                        
                        return exito;
                    });
                });

                function cargar_usuarios() {
                    var code = $("#cmbDependencia").val();
                    if (code == 0)
                        $("#cmbUsuario").children().remove();
                    else {
                        $.get("scripts/cargar-usuarios.php?", {code: code}, function(resultado) {
                            if (resultado == false) {
                                alert("Error");
                            } else {
                                document.getElementById("cmbUsuario").options.length = 0;
                                $('#cmbUsuario').append(resultado);
                            }
                        });
                    }
                }

                function cargar_ciudades() {
                    debugger;
                    var code = $("#cmbDpto").val();
                    if (code == 0)
                        $("#cmbMcpio").children().remove();
                    else {

                    	var parametros = {
                        	"code" : code
                        };
                    	
                    	$.ajax({
                    		url: '../pqrverbal/scripts/cargar-ciudades.php',
                    		type: 'POST',
                    		cache: false,
                    		async: false,
                    		data:  parametros,
                    		success: function(resultado) {
                    			debugger;
                    			if (resultado == false) {
                                    alert("Error");
                                } else {
                                    document.getElementById("cmbMcpio").options.length = 0;
                                    $('#cmbMcpio').append(resultado);
                                }
                    		},
                    		error: function (jqXHR, textStatus, errorThrown) {
        						alert('Se ha producido un error ' + jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
        					}
                    	});
                        /*$.get("scripts/cargar-ciudades.php?", {code: code}, function(resultado) {
                        	debugger;
                            if (resultado == false) {
                                alert("Error");
                            } else {
                                document.getElementById("cmbMcpio").options.length = 0;
                                $('#cmbMcpio').append(resultado);
                            }
                        });*/
                    }
                }

                function cargar_solicitudes() {
                    var code = $("#cmbTema").val();
                    if (code == '')
                        $("#cmbSolictud").children().remove();
                    else {
                        $.get("scripts/cargar-solicitudes.php?", {code: code}, function(resultado) {
                            if (resultado == false) {
                                alert("Error");
                            } else {
                                document.getElementById("cmbSolictud").options.length = 0;
                                $('#cmbSolictud').append(resultado);
                            }
                        });
                        $.get("scripts/cargar-descrip-solicitudes.php?", {code: code}, function(resultado) {
                            if (resultado == false) {
                                alert("Error");
                            } else {
                                $('#toggleTextTdoc').html("");
                                $('#toggleTextTdoc').append(resultado);
                            }
                        });
                    }
                }

                function validar_email(band) {
                    var r = isEmail($('#txtCorreo').get(0).value, false);
                    if (band == true) {
                        if (!r)
                            alert("Correo no diligenciado o sin formato correcto.");
                    }
                    return r;
                }

                function toggle(cualId) {
                    var ok = true;
                    if (cualId == 'idTema') {
                        var ele = document.getElementById("toggleTextTema");
                        ele.innerHTML = "<?php echo $helpTemas; ?>";
                    }
                    if (cualId == 'idTdoc') {
                        var ele = document.getElementById("toggleTextTdoc");
                        //validacion campo Tema
                        if ($('#cmbTema option:selected').val() == '') {
                            ok = false;
                            var msg = "Visualizaci\xf3n de detalles de los diferentes tipos de solicitud.\n" +
                                    "Seleccione primero un Tema y active nuevamente esta opci\xf3n.";
                            alert(msg);
                        }
                    }

                    if (ok) {
                        if (ele.style.display == "block") {
                            ele.style.display = "none";
                        } else {
                            ele.style.display = "block";
                        }
                    }
                }

                function hayAnexos() {
                    var band = false;
                    if ($("em").length > 0)
                        band = true;
                    return band;
                }

                function validaMultimedia() {
                    var band = true;
                    var em = document.getElementById('txtMultimedia').value;
                    if (em.length == 0) {
                    	band = confirm('Validar eliminacion del archivo \n No se ha detectado grabaci\xf3n y/o carga de archivo multimedia. Desea radicar sin \xe9l ?');
                    }
                    return band;
                }
            </script>
    </head>
    <body>
        <form action="respuesta.php" method="post" name="frm_pqr" id="frm_pqr" enctype="multipart/form-data">
        <fieldset >
        	<legend>Datos del Peticionario</legend>
        	<table border="0">
                <tr>
                    <td>&nbsp;</td>
                    <td class="textoEnGris"><label for="txtNombre" id="labeltxtNombre"><u>N</u>ombre(*):</label></td>
                    <td>
                        <input type="text" name="txtNombre" id="txtNombre" size="25" maxlength="80" class="NormalTextBox" required /> 
                    </td>
                    <td>&nbsp;</td>
                    <td class="textoEnGris"><label for="txtApellido" id="labeltxtApellido">A<u>p</u>ellidos(*):</label></td>
                    <td><input type="text" name="txtApellido" id="txtApellido" size="25" maxlength="145" accesskey="P" required /> 
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="textoEnGris"><label for="tipoDoc" id="labeltipoDoc">Tipo Documento(*):</label></td>
                    <td>
                        <?php echo $cmbtipoDoc; ?>
                        
                    </td>
                    <td>&nbsp;</td>
                    <td class="textoEnGris">No. Documento(*):</td>
                    <td>
                        <input type="text" name="txtDocumento" id="txtDocumento" size="25" maxlength="30" accesskey="P" required pattern="[a-zA-Z0-9-]*" placeholder="Solo letras, numeros y guion" title="Solo letras, numeros y guion" /> 
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="textoEnGris">Direcci&oacute;n:</td>
                    <td><input name="txtDireccion" id="txtDireccion" type="text" size="25" maxlength="100" />
                        
                    </td>
                    <td>&nbsp;</td>
                    <td class="textoEnGris">Tel&eacute;fono:</td>
                    <td><input name="txtTelefono" id="txtTelefono" type="text" size="25" maxlength="50" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="textoEnGris">Departamento:</td>
                    <td>
                        <?php echo $cmbDptos; ?> 
                    </td>
                    <td>&nbsp;</td>
                    <td class="textoEnGris">Municipio:</td>
                    <td>
                        <select name="cmbMcpio" id="cmbMcpio" required><option value=''>Seleccione Ciudad</option></select> 
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="textoEnGris"><u>C</u>orreo Electr&oacute;nico:</td>
                    <td colspan="4"><input type="email" name="txtCorreo" id="txtCorreo" size="75" maxlength="50" />
                    </td>
                </tr>
                </table>
        </fieldset>
		<fieldset>
			<legend>Informaci&oacute;n Petici&oacute;n Verbal</legend>
            <table width="525px" border="0" class="efect">
            	<tr>
            		<td>&nbsp;</td>
					<td class="textoEnGris">Autorizo respuesta v&iacute;a(*):</td>
                    <td>
                        <input type="radio" name="tipoResp" id="respCE" value="V" required />Correo Electr&oacute;nico
                        <input type="radio" name="tipoResp" id="respDF" value="F" />Direcci&oacute;n F&iacute;sica
                    </td>
                </tr>
				<tr>
					<td>&nbsp;</td>
					<td class="textoEnGris">Tipo Radicaci&oacute;n Verbal(*):</td>
					<td>
						<input type="radio" name="mrecCodi" value="6" required /> Telef&oacute;nica
						<input type="radio" name="mrecCodi" value="9" /> Presencial
					</td>
				</tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="textoEnGris">Informaci&oacute;n Poblacional(*):</td>
                    <td>
                        <?php echo $cmbRazas; ?> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <a id="idTema" href="javascript:toggle('idTema');" accesskey="T" title="<?php echo preg_replace("[</?b>]", "", str_replace("<br/>", " ", $helpTemas)); ?>">
                            <image style="border: none;" src="images/Ayuda.png" />
                        </a>
                    </td>
                    <td class="textoEnGris">
                        <u>T</u>ema(*):&nbsp;&nbsp;
                    </td>
                    <td>
                        <?php echo $cmbTemas; ?>
                        
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <div id="toggleTextTema" style="display: none; text-align: justify"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a id="idTdoc" href="javascript:toggle('idTdoc');" accesskey="D">
                            <image style="border: none;" src="images/Ayuda.png" />
                        </a>
                    </td>
                    <td class="textoEnGris">Tipo Solicitu<u>d</u>(*):</td>
                    <td>
                        <select name="cmbSolictud" id="cmbSolictud" required>
                        </select>
                        
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <div id="toggleTextTdoc" style="display: none; text-align: justify"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">Comentario (*)<br/>
                        <textarea name="txtComentario" id="txtComentario" cols="58" rows="3" maxlength="280" required ></textarea>
                        
                    </td>
                </tr>
                </table>
				</fieldset>	
                <fieldset>
                <legend>Anexos</legend>
                <table>
                <tr>
                    <td class="textoEnGris textoEnNegrilla">Archivos (5 m&aacute;ximo)</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="textoEnGris textoEnNegrilla">Multimedia</td>
                </tr>
                <tr>
                    <td>
                        Su archivo no debe superar <?php echo ini_get('post_max_size'); ?>. <br />
                        <input type="hidden"  name="MAX_FILE_SIZE" value="<?php echo return_bytes(ini_get('post_max_size')); ?>" />
                        <input type="file" name="MyFileUpload[]" size="60" id="MyFileUpload" accesskey="X" /> <br />
                        <div id="listaAnexos"></div>
                    </td>
                    <td></td>
                    <td>
                    	<section class="experiment recordrtc">
				            <h2 class="header">
				                <select class="recording-media">
				                    <option value="record-video">Video</option>
				                    <option value="record-audio">Audio</option>
				                    <option value="record-screen">Screen</option>
				                </select>
				
				                into
				                <select class="media-container-format">
				                    <option>WebM</option>
				                    <option disabled>Mp4</option>
				                    <option disabled>WAV</option>
				                    <option disabled>Ogg</option>
				                    <option>Gif</option>
				                </select>
				
				                <input type="button" id="btnrtv" name="btnrtv" value="Start Recording" />
				            </h2>
				
				            <div style="text-align: center; display: none;">
				                <input type="button" id="save-to-disk" value="Save To Disk">
				                <input type="button" id="open-new-tab" value="Open New Tab">
				                <input type="button" id="upload-to-server" value="Upload To Server">
				            </div>
				
				            <br>
				
				            <video controls muted></video>
				        </section>
                    	<script type="text/javascript">
						(function() {
						    var params = {},
						        r = /([^&=]+)=?([^&]*)/g;
						    function d(s) {
						        return decodeURIComponent(s.replace(/\+/g, ' '));
						    }
						    var match, search = window.location.search;
						    while (match = r.exec(search.substring(1))) {
						        params[d(match[1])] = d(match[2]);
						        if(d(match[2]) === 'true' || d(match[2]) === 'false') {
						            params[d(match[1])] = d(match[2]) === 'true' ? true : false;
						        }
						    }
						    window.params = params;
						})();
						</script>
						
						<script type="text/javascript">
						var recordingDIV = document.querySelector('.recordrtc');
						var recordingMedia = recordingDIV.querySelector('.recording-media');
						var recordingPlayer = recordingDIV.querySelector('video');
						var mediaContainerFormat = recordingDIV.querySelector('.media-container-format');
						recordingDIV.querySelector('#btnrtv').onclick = function() {
						    var button = this;
						    if(button.value === 'Stop Recording') {
						        button.disabled = true;
						        button.disableStateWaiting = true;
						        setTimeout(function() {
						            button.disabled = false;
						            button.disableStateWaiting = false;
						        }, 2 * 1000);
						        button.value = 'Start Recording';
						        function stopStream() {
						            if(button.stream && button.stream.stop) {
						                button.stream.stop();
						                button.stream = null;
						            }
						        }
						        if(button.recordRTC) {
						            if(button.recordRTC.length) {
						                button.recordRTC[0].stopRecording(function(url) {
						                    if(!button.recordRTC[1]) {
						                        button.recordingEndedCallback(url);
						                        stopStream();
						                        saveToDiskOrOpenNewTab(button.recordRTC[0]);
						                        return;
						                    }
						                    button.recordRTC[1].stopRecording(function(url) {
						                        button.recordingEndedCallback(url);
						                        stopStream();
						                    });
						                });
						            }
						            else {
						                button.recordRTC.stopRecording(function(url) {
						                    button.recordingEndedCallback(url);
						                    stopStream();
						                    saveToDiskOrOpenNewTab(button.recordRTC);
						                });
						            }
						        }
						        return;
						    }
						    button.disabled = true;
						    var commonConfig = {
						        onMediaCaptured: function(stream) {
						            button.stream = stream;
						            if(button.mediaCapturedCallback) {
						                button.mediaCapturedCallback();
						            }
						            button.value = 'Stop Recording';
						            button.disabled = false;
						        },
						        onMediaStopped: function() {
						            button.value = 'Start Recording';
						            if(!button.disableStateWaiting) {
						                button.disabled = false;
						            }
						        },
						        onMediaCapturingFailed: function(error) {
						            if(error.name === 'PermissionDeniedError' && !!navigator.mozGetUserMedia) {
						                InstallTrigger.install({
						                    'Foo': {
						                        // https://addons.mozilla.org/firefox/downloads/latest/655146/addon-655146-latest.xpi?src=dp-btn-primary
						                        URL: 'https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/',
						                        toString: function () {
						                            return this.URL;
						                        }
						                    }
						                });
						            }
						            commonConfig.onMediaStopped();
						        }
						    };
						    if(recordingMedia.value === 'record-video') {
						        captureVideo(commonConfig);
						        button.mediaCapturedCallback = function() {
						            button.recordRTC = RecordRTC(button.stream, {
						                type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
						                disableLogs: params.disableLogs || false,
						                canvas: {
						                    width: params.canvas_width || 320,
						                    height: params.canvas_height || 240
						                },
						                frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
						            });
						            button.recordingEndedCallback = function(url) {
						                recordingPlayer.src = null;
						                recordingPlayer.srcObject = null;
						                if(mediaContainerFormat.value === 'Gif') {
						                    recordingPlayer.pause();
						                    recordingPlayer.poster = url;
						                    recordingPlayer.onended = function() {
						                        recordingPlayer.pause();
						                        recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
						                    };
						                    return;
						                }
						                recordingPlayer.src = url;
						                recordingPlayer.play();
						                recordingPlayer.onended = function() {
						                    recordingPlayer.pause();
						                    recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
						                };
						            };
						            button.recordRTC.startRecording();
						        };
						    }
						    if(recordingMedia.value === 'record-audio') {
						        captureAudio(commonConfig);
						        button.mediaCapturedCallback = function() {
						            button.recordRTC = RecordRTC(button.stream, {
						                type: 'audio',
						                bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
						                sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
						                leftChannel: params.leftChannel || false,
						                disableLogs: params.disableLogs || false,
						                recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
						            });
						            button.recordingEndedCallback = function(url) {
						                var audio = new Audio();
						                audio.src = url;
						                audio.controls = true;
						                recordingPlayer.parentNode.appendChild(document.createElement('hr'));
						                recordingPlayer.parentNode.appendChild(audio);
						                if(audio.paused) audio.play();
						                audio.onended = function() {
						                    audio.pause();
						                    audio.src = URL.createObjectURL(button.recordRTC.blob);
						                };
						            };
						            button.recordRTC.startRecording();
						        };
						    }
						    if(recordingMedia.value === 'record-audio-plus-video') {
						        captureAudioPlusVideo(commonConfig);
						        button.mediaCapturedCallback = function() {
						            if(webrtcDetectedBrowser !== 'firefox') { // opera or chrome etc.
						                button.recordRTC = [];
						                if(!params.bufferSize) {
						                    // it fixes audio issues whilst recording 720p
						                    params.bufferSize = 16384;
						                }
						                var audioRecorder = RecordRTC(button.stream, {
						                    type: 'audio',
						                    bufferSize: typeof params.bufferSize == 'undefined' ? 0 : parseInt(params.bufferSize),
						                    sampleRate: typeof params.sampleRate == 'undefined' ? 44100 : parseInt(params.sampleRate),
						                    leftChannel: params.leftChannel || false,
						                    disableLogs: params.disableLogs || false,
						                    recorderType: webrtcDetectedBrowser === 'edge' ? StereoAudioRecorder : null
						                });
						                var videoRecorder = RecordRTC(button.stream, {
						                    type: 'video',
						                    disableLogs: params.disableLogs || false,
						                    canvas: {
						                        width: params.canvas_width || 320,
						                        height: params.canvas_height || 240
						                    },
						                    frameInterval: typeof params.frameInterval !== 'undefined' ? parseInt(params.frameInterval) : 20 // minimum time between pushing frames to Whammy (in milliseconds)
						                });
						                // to sync audio/video playbacks in browser!
						                videoRecorder.initRecorder(function() {
						                    audioRecorder.initRecorder(function() {
						                        audioRecorder.startRecording();
						                        videoRecorder.startRecording();
						                    });
						                });
						                button.recordRTC.push(audioRecorder, videoRecorder);
						                button.recordingEndedCallback = function() {
						                    var audio = new Audio();
						                    audio.src = audioRecorder.toURL();
						                    audio.controls = true;
						                    audio.autoplay = true;
						                    audio.onloadedmetadata = function() {
						                        recordingPlayer.src = videoRecorder.toURL();
						                        recordingPlayer.play();
						                    };
						                    recordingPlayer.parentNode.appendChild(document.createElement('hr'));
						                    recordingPlayer.parentNode.appendChild(audio);
						                    if(audio.paused) audio.play();
						                };
						                return;
						            }
						            button.recordRTC = RecordRTC(button.stream, {
						                type: 'video',
						                disableLogs: params.disableLogs || false,
						                // we can't pass bitrates or framerates here
						                // Firefox MediaRecorder API lakes these features
						            });
						            button.recordingEndedCallback = function(url) {
						                recordingPlayer.srcObject = null;
						                recordingPlayer.muted = false;
						                recordingPlayer.src = url;
						                recordingPlayer.play();
						                recordingPlayer.onended = function() {
						                    recordingPlayer.pause();
						                    recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
						                };
						            };
						            button.recordRTC.startRecording();
						        };
						    }
						    if(recordingMedia.value === 'record-screen') {
						        captureScreen(commonConfig);
						        button.mediaCapturedCallback = function() {
						            button.recordRTC = RecordRTC(button.stream, {
						                type: mediaContainerFormat.value === 'Gif' ? 'gif' : 'video',
						                disableLogs: params.disableLogs || false,
						                canvas: {
						                    width: params.canvas_width || 320,
						                    height: params.canvas_height || 240
						                }
						            });
						            button.recordingEndedCallback = function(url) {
						                recordingPlayer.src = null;
						                recordingPlayer.srcObject = null;
						                if(mediaContainerFormat.value === 'Gif') {
						                    recordingPlayer.pause();
						                    recordingPlayer.poster = url;
						                    recordingPlayer.onended = function() {
						                        recordingPlayer.pause();
						                        recordingPlayer.poster = URL.createObjectURL(button.recordRTC.blob);
						                    };
						                    return;
						                }
						                recordingPlayer.src = url;
						                recordingPlayer.play();
						            };
						            button.recordRTC.startRecording();
						        };
						    }
						    if(recordingMedia.value === 'record-audio-plus-screen') {
						        captureAudioPlusScreen(commonConfig);
						        button.mediaCapturedCallback = function() {
						            button.recordRTC = RecordRTC(button.stream, {
						                type: 'video',
						                disableLogs: params.disableLogs || false,
						                // we can't pass bitrates or framerates here
						                // Firefox MediaRecorder API lakes these features
						            });
						            button.recordingEndedCallback = function(url) {
						                recordingPlayer.srcObject = null;
						                recordingPlayer.muted = false;
						                recordingPlayer.src = url;
						                recordingPlayer.play();
						                recordingPlayer.onended = function() {
						                    recordingPlayer.pause();
						                    recordingPlayer.src = URL.createObjectURL(button.recordRTC.blob);
						                };
						            };
						            button.recordRTC.startRecording();
						        };
						    }
						};
						function captureVideo(config) {
						    captureUserMedia({video: true}, function(videoStream) {
						        recordingPlayer.srcObject = videoStream;
						        recordingPlayer.play();
						        config.onMediaCaptured(videoStream);
						        videoStream.onended = function() {
						            config.onMediaStopped();
						        };
						    }, function(error) {
						        config.onMediaCapturingFailed(error);
						    });
						}
						function captureAudio(config) {
						    captureUserMedia({audio: true}, function(audioStream) {
						        recordingPlayer.srcObject = audioStream;
						        recordingPlayer.play();
						        config.onMediaCaptured(audioStream);
						        audioStream.onended = function() {
						            config.onMediaStopped();
						        };
						    }, function(error) {
						        config.onMediaCapturingFailed(error);
						    });
						}
						function captureAudioPlusVideo(config) {
						    captureUserMedia({video: true, audio: true}, function(audioVideoStream) {
						        recordingPlayer.srcObject = audioVideoStream;
						        recordingPlayer.play();
						        config.onMediaCaptured(audioVideoStream);
						        audioVideoStream.onended = function() {
						            config.onMediaStopped();
						        };
						    }, function(error) {
						        config.onMediaCapturingFailed(error);
						    });
						}
						function captureScreen(config) {
						    getScreenId(function(error, sourceId, screenConstraints) {
						        if (error === 'not-installed') {
						            document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
						        }
						        if (error === 'permission-denied') {
						            alert('Screen capturing permission is denied.');
						        }
						        if (error === 'installed-disabled') {
						            alert('Please enable chrome screen capturing extension.');
						        }
						        if(error) {
						            config.onMediaCapturingFailed(error);
						            return;
						        }
						        captureUserMedia(screenConstraints, function(screenStream) {
						            recordingPlayer.srcObject = screenStream;
						            recordingPlayer.play();
						            config.onMediaCaptured(screenStream);
						            screenStream.onended = function() {
						                config.onMediaStopped();
						            };
						        }, function(error) {
						            config.onMediaCapturingFailed(error);
						        });
						    });
						}
						function captureAudioPlusScreen(config) {
						    getScreenId(function(error, sourceId, screenConstraints) {
						        if (error === 'not-installed') {
						            document.write('<h1><a target="_blank" href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk">Please install this chrome extension then reload the page.</a></h1>');
						        }
						        if (error === 'permission-denied') {
						            alert('Screen capturing permission is denied.');
						        }
						        if (error === 'installed-disabled') {
						            alert('Please enable chrome screen capturing extension.');
						        }
						        if(error) {
						            config.onMediaCapturingFailed(error);
						            return;
						        }
						        screenConstraints.audio = true;
						        captureUserMedia(screenConstraints, function(screenStream) {
						            recordingPlayer.srcObject = screenStream;
						            recordingPlayer.play();
						            config.onMediaCaptured(screenStream);
						            screenStream.onended = function() {
						                config.onMediaStopped();
						            };
						        }, function(error) {
						            config.onMediaCapturingFailed(error);
						        });
						    });
						}
						function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
						    navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
						}
						function setMediaContainerFormat(arrayOfOptionsSupported) {
						    var options = Array.prototype.slice.call(
						        mediaContainerFormat.querySelectorAll('option')
						    );
						    var selectedItem;
						    options.forEach(function(option) {
						        option.disabled = true;
						        if(arrayOfOptionsSupported.indexOf(option.value) !== -1) {
						            option.disabled = false;
						            if(!selectedItem) {
						                option.selected = true;
						                selectedItem = option;
						            }
						        }
						    });
						}
						recordingMedia.onchange = function() {
						    if(this.value === 'record-audio') {
						        setMediaContainerFormat(['WAV', 'Ogg']);
						        return;
						    }
						    setMediaContainerFormat(['WebM', /*'Mp4',*/ 'Gif']);
						};
						if(webrtcDetectedBrowser === 'edge') {
						    // webp isn't supported in Microsoft Edge
						    // neither MediaRecorder API
						    // so lets disable both video/screen recording options
						    console.warn('Neither MediaRecorder API nor webp is supported in Microsoft Edge. You cam merely record audio.');
						    recordingMedia.innerHTML = '<option value="record-audio">Audio</option>';
						    setMediaContainerFormat(['WAV']);
						}
						if(webrtcDetectedBrowser === 'firefox') {
						    // Firefox implemented both MediaRecorder API as well as WebAudio API
						    // Their MediaRecorder implementation supports both audio/video recording in single container format
						    // Remember, we can't currently pass bit-rates or frame-rates values over MediaRecorder API (their implementation lakes these features)
						    recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
						                                + '<option value="record-audio-plus-screen">Audio+Screen</option>'
						                                + recordingMedia.innerHTML;
						}
						// disabling this option because currently this demo
						// doesn't supports publishing two blobs.
						// todo: add support of uploading both WAV/WebM to server.
						if(false && webrtcDetectedBrowser === 'chrome') {
						    recordingMedia.innerHTML = '<option value="record-audio-plus-video">Audio+Video</option>'
						                                + recordingMedia.innerHTML;
						    console.info('This RecordRTC demo merely tries to playback recorded audio/video sync inside the browser. It still generates two separate files (WAV/WebM).');
						}
						function saveToDiskOrOpenNewTab(recordRTC) {
							alert('inicia guardado');
							var filemedia = "";
						    var now = new Date();
						    													
						    recordingDIV.querySelector('#save-to-disk').parentNode.style.display = 'block';
						    recordingDIV.querySelector('#save-to-disk').onclick = function() {
						        if(!recordRTC) return alert('No recording found.');
						        //if(recordingDIV.querySelector('.recording-media').value === 'record-audio') {
						        //	filemedia = now.toISOString(); + recordingDIV.querySelector('.media-container-format').value.toLowerCase();
							    //}
						        //recordRTC.save(filemedia);
						        recordRTC.save();
						    };
						    recordingDIV.querySelector('#open-new-tab').onclick = function() {
						        if(!recordRTC) return alert('No recording found.');
						        window.open(recordRTC.toURL());
						    };
						    recordingDIV.querySelector('#upload-to-server').disabled = false;
						    recordingDIV.querySelector('#upload-to-server').onclick = function() {
						        if(!recordRTC) return alert('No recording found.');
						        this.disabled = true;
						        var button = this;
						        uploadToServer(recordRTC, function(progress, fileURL) {
						            if(progress === 'ended') {
						                button.disabled = false;
						                button.value = 'Click to download from server';
						                button.onclick = function() {
						                    window.open(fileURL);
						                };
						                return;
						            }
						            button.value = progress;
						        });
						    };
						}
						var listOfFilesUploaded = [];
						function uploadToServer(recordRTC, callback) {
						    var blob = recordRTC instanceof Blob ? recordRTC : recordRTC.blob;
						    var fileType = blob.type.split('/')[0] || 'audio';
						    var fileName = (Math.random() * 1000).toString().replace('.', '');
						    if (fileType === 'audio') {
						        fileName += '.' + (!!navigator.mozGetUserMedia ? 'ogg' : 'wav');
						    } else {
						        fileName += '.webm';
						    }
						    // create FormData
						    var formData = new FormData();
						    formData.append(fileType + '-filename', fileName);
						    formData.append(fileType + '-blob', blob);
						    callback('Uploading ' + fileType + ' recording to server.');
						    makeXMLHttpRequest('save.php', formData, function(progress) {
						        if (progress !== 'upload-ended') {
						            callback(progress);
						            return;
						        }
						        var initialURL = location.href.replace(location.href.split('/').pop(), '') + 'uploads/';
						        callback('ended', initialURL + fileName);
						        // to make sure we can delete as soon as visitor leaves
						        listOfFilesUploaded.push(initialURL + fileName);
						        document.getElementById('txtMultimedia').value = fileName;
						    });
						}
						function makeXMLHttpRequest(url, data, callback) {
						    var request = new XMLHttpRequest();
						    request.onreadystatechange = function() {
						        if (request.readyState == 4 && request.status == 200) {
						            callback('upload-ended');
						        }
						    };
						    request.upload.onloadstart = function() {
						        callback('Upload started...');
						    };
						    request.upload.onprogress = function(event) {
						        callback('Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%");
						    };
						    request.upload.onload = function() {
						        callback('progress-about-to-end');
						    };
						    request.upload.onload = function() {
						        callback('progress-ended');
						    };
						    request.upload.onerror = function(error) {
						        callback('Failed to upload to server');
						        console.error('XMLHttpRequest failed', error);
						    };
						    request.upload.onabort = function(error) {
						        callback('Upload aborted.');
						        console.error('XMLHttpRequest aborted', error);
						    };
						    request.open('POST', url);
						    request.send(data);
						}
						window.onbeforeunload = function() {
						    recordingDIV.querySelector('button').disabled = false;
						    recordingMedia.disabled = false;
						    mediaContainerFormat.disabled = false;
						    if(!listOfFilesUploaded.length) return;
						    listOfFilesUploaded.forEach(function(fileURL) {
						        var request = new XMLHttpRequest();
						        request.onreadystatechange = function() {
						            if (request.readyState == 4 && request.status == 200) {
						                if(this.responseText === ' problem deleting files.') {
						                    alert('Failed to delete ' + fileURL + ' from the server.');
						                    return;
						                }
						                listOfFilesUploaded = [];
						                alert('You can leave now. Your files are removed from the server.');
						            }
						        };
						        request.open('POST', 'delete.php');
						        var formData = new FormData();
						        formData.append('delete-file', fileURL.split('/').pop());
						        request.send(formData);
						    });
						    return 'Please wait few seconds before your recordings are deleted from the server.';
						};
						</script>
                    <td>
                </tr>
                </table>
                </fieldset>
                <table>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>		
				<tr>
                    <td colspan="3" class="textoEnGris textoEnNegrilla">Destino.</td>
                </tr>
                <tr>
	                <td></td>
					<td class="textoEnGris">Dependencia(*)</td>
					<td>
	                    <?php echo $cmbDependencia; ?>
	                    
	                </td>
	            </tr>
				<tr>
	                <td></td>
					<td class="textoEnGris">Usuario(*)</td>
					<td>
	                    <select name="cmbUsuario" id="cmbUsuario" required>
							<option value=''>Seleccione Dependencia</option>
	                    </select>
	                </td>
	            </tr>
                <tr>
                    <td colspan="3">
                        <table width="100%" border="0">
                            <tr valign="middle" align="center">
                                <td>
                                    <input type="submit" value="Radicar" name="btn_radicar" id="btn_radicar" alt="Radicar" onclick="return validaMultimedia();" />
                                    <input type="reset" value="Limpiar" name="btn_reset" id="btn_reset" alt="Limpiar" />
                                    <input type="hidden" name="txtCodPostal" id="txtCodPostal" size="7" maxlength="7" />
                                    <input type="hidden" name="txtMultimedia" id="txtMultimedia" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>