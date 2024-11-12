
<?php
session_start();
$ruta_raiz = "../../..";
 
$verrad = $_GET['verrad'];
if (!$verrad || $verrad == 0) {
    $verrad = $_POST[0];
}
    
$codusuario = $_SESSION['codusuario'];
    
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
include ("$ruta_raiz/config.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$ADODB_COUNTRECS = true;

include_once("$ruta_raiz/orfeo.api/libs/CodificacionEspecial.php");
$objCodificacionEspecial = new CodificacionEspecial();

function convertToReadableSize($size){
    $base = log($size) / log(1024);
    $suffix = array("", "KB", "MB", "GB", "TB");
    $f_base = floor($base);
    return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}

$emailRemitente = array("");
$emailRemitenteDefault = "";
if ($_SESSION["usua_email"]) {
    if (strpos(strtolower($_SESSION["usua_email"]), "web") !== false) {
        $emailRemitenteDefault = strtolower($_SESSION["usua_email"]);
        $emailRemitente[] = strtolower($_SESSION["usua_email"]);
    }
}
if ($_SESSION["usua_email_1"]) {
    //if (strpos(strtolower($_SESSION["usua_email_1"]), "web") !== false) {
    if (filter_var($_SESSION["usua_email_1"], FILTER_VALIDATE_EMAIL)) {
        $emailRemitenteDefault = strtolower($_SESSION["usua_email_1"]);
        $emailRemitente[] = strtolower($_SESSION["usua_email_1"]);
    }
}

if ($_SESSION["usua_email_2"]) {
    //if(strpos(strtolower($_SESSION["usua_email_2"]), "web")!==false){
    if (filter_var($_SESSION["usua_email_2"], FILTER_VALIDATE_EMAIL)) {
        $emailRemitenteDefault = strtolower($_SESSION["usua_email_2"]);
        $emailRemitente[] = strtolower($_SESSION["usua_email_2"]);
    }
}
$emailRemitente[] = $_SESSION["usua_email_fe"];
$emailRemitenteDefault = "";

$datosRadicado = Array();
try 
{
    $st = "declare @fech1 datetime
          declare @fech2 datetime
          DECLARE @return_value int
                                               
          EXEC	@return_value = [dbo].[RADICADO_SGD_DIR_DRECCIONESConsultarConFiltro]
                @NoRadicado = '".$verrad."',
                @nombreContacto = '',
                @tipoRadicado = 2,
                @fechaInicio = '',
                @fechaFin = '',
                @codigoTipoDocumental = 0,
                @codigoDepenendenciaActual = 0,
                @NoPaginaResultado = 1,
                @campoOrden = 'RADI_FECH_RADI',
                @tipoOrden = 'DESC',
                @NoRegistrosPorPagina = 10,
                @usuarioActual = '$krd',
                @codigoCarpeta = 0,
                @carpetaPersonal = 0";
    $rs = $db->conn->GetArray($st);
    if($rs && is_array($rs) && count($rs)>0)
    {
       
        $rs[0]['idCiudadano']=$rs[0]['idCiudadano'];
        $rs[0]['idEmpresa']=$rs[0]['idEmpresa'];
        $rs[0]['idEntidad']=$rs[0]['idEntidad'];
        $rs[0]['loginFuncionario'] = iconv("iso-8859-1", "UTF-8",$rs[0]['loginFuncionario']);
        $tmpChasetAsun = $objCodificacionEspecial->codificacion($rs[0]['RA_ASUN']);
        $rs[0]['asunto'] = iconv($tmpChasetAsun, "UTF-8",$tmpChasetAsun);
        $rs[0]['dirmail'] = iconv($objCodificacionEspecial->codificacion($rs[0]['SGD_DIR_MAIL']), "UTF-8", $rs[0]['SGD_DIR_MAIL']);
        $rs[0]['nombrecontacto'] = iconv($objCodificacionEspecial->codificacion($rs[0]['SGD_DIR_NOMREMDES']),"UTF-8",$rs[0]['SGD_DIR_NOMREMDES']);
        $rs[0]['RADI_NUME_RADI'] = $rs[0]['RADI_NUME_RADI'];
        $rs[0]['RA_ASUN'] = iconv($tmpChasetAsun, "UTF-8",$rs[0]['RA_ASUN']);
        $rs[0]['SGD_TPR_DESCRIP'] = iconv($objCodificacionEspecial->codificacion($rs[0]['SGD_TPR_DESCRIP']),"UTF-8",$rs[0]['SGD_TPR_DESCRIP']);
        $rs[0]['SGD_DIR_MAIL']= iconv($objCodificacionEspecial->codificacion($rs[0]['SGD_DIR_MAIL']), "UTF-8", $rs[0]['SGD_DIR_MAIL']);
        $rs[0]['SGD_DIR_DIRECCION']= iconv($objCodificacionEspecial->codificacion($rs[0]['SGD_DIR_DIRECCION']), "UTF-8", $rs[0]['SGD_DIR_DIRECCION']);
        
        $datosRadicado = $rs;
    }
    
} catch (ADODB_Exception $ex)
{
    echo "Error:". $ex->getMessage();
}
?>

<html lang="es" dir="ltr">
	<head>		
    	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>RESPUESTA RAPIDA</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="styles.css">
		
		<script type="text/javascript" src="../../../js/jquery-3.5.1.js"></script>
    	<script type="text/javascript" src="../../../js/jquery-ui.js"></script>
    	<script type="text/javascript" src="../../../js/jquery-3.4.1.min.js"></script>
    	<script type="text/javascript" src="../../../js/jquery.blockUI.js"></script>
    	<script type="text/javascript" src="../../..//js/jquery.MultiFile.js"></script>
    	<!-- <script src="https://cdn.ckeditor.com/ckeditor5/29.0.0/classic/ckeditor.js"></script>  -->
    	 <script src="../build/ckeditor.js"></script> 
		<script src="../build/imageupload.js"></script>
    	
    	<style>       
                .ck-editor__editable_inline {
                	max-height: 350px;
                	min-height: 350px;
                	min-width: 920px;
                	overflow: auto;
                }
                
                #divEditor {
                  position: absolute;
                  z-index: 9;
                  background-color: #f1f1f1;
                  text-align: center;
                  border: 1px solid #d3d3d3;
                  display: none; 
                  width: 970px; 
                  heigth: 350px;
                  z-index: 1;
                }
                
                #divEditorheader {
                  padding: 1px;
                  cursor: move;
                  z-index: 10;
                  background-color: #006699;
                  color: #fff;
                }
                         
                .popup-wrapper {
                    background: rgba(0, 0, 0, 0.5);
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    display: none;
                    z-index: 3;
                }
                 
                .popup {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    text-align: center;
                    width: 100%;
                    max-width: 300px;
                    background: white;
                    margin: 10% auto;
                    padding: 20px;
                    position: relative;
                }
        </style>
        
        <script type="text/javascript">
    	    
    		var storedFiles = [];
    		
			<?php
    		  $js_array = json_encode($datosRadicado);
              echo "var javascript_array = ". $js_array . ";\n";
            ?>
            
        	$( document ).ready(function() {
        		
        		let editor;
    		
    			$("#myfiles").on("change", handleFileSelect);

    			ClassicEditor
				.create( document.querySelector( '.editor' ), {
				toolbar: {
					items: [
						'heading',
						'|',
						'bold',
						'underline',
						'italic',
						'link',
						'|',
						'fontBackgroundColor',
						'fontColor',
						'fontSize',
						'fontFamily',
						'|',
						'bulletedList',
						'numberedList',
						'|',
						'outdent',
						'indent',
						'|',
						'imageInsert',
						'blockQuote',
						'insertTable',
						'mediaEmbed',
						'undo',
						'redo',
						'findAndReplace'
					]
				},
				language: 'es',
				image: {
					toolbar: [
						'imageTextAlternative',
						'imageStyle:inline',
						'imageStyle:block',
						'imageStyle:side',
						'linkImage',
						'toggleImageCaption', 
						'imageTextAlternative'
					]
				},
				image: {
		            toolbar: [ 
			            'imageStyle:block',
		                'imageStyle:side',
		                '|',
		                'toggleImageCaption',
		                'imageTextAlternative',
		                '|',
		                'linkImage'
		           	]
		        },
				table: {
					contentToolbar: [
						'tableColumn',
						'tableRow',
						'mergeTableCells',
						'tableCellProperties',
						'tableProperties'
					]
				},
					licenseKey: '',

				} )
				.then( editor => {

					editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
	                    return new UploadAdapter(loader);
	                };
		             
					window.editor = editor;

				} )
				.catch( error => {
					console.error( 'Oops, something went wrong!' );
					console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
					console.warn( 'Build id: 43bpx16dud5w-ggaz9r4vz531' );
					console.error( error );
				} );

    			var destino = document.getElementById('destinatario');
                if (destino) {
                    destino.value = '<? echo $_POST[1]?>';
                }
    
                var varRad = document.getElementById('lbTitulo');
                if (varRad) {
                	$('#lbTitulo').text("RESPUESTA RAPIDA PARA EL RADICADO No. " + <? echo $_POST[0]?>);
                }
   			
        	});

        	function handleFileSelect(e) {

				var files = e.target.files;
				var filesArr = Array.prototype.slice.call(files);
				filesArr.forEach(function(f) {	

					/*if(!f.type.match("image.*")) {
						return;
					}*/
					storedFiles.push(f);
					
					var reader = new FileReader();
					reader.onload = function (e) {
						var html = "<div><img src=\"" + e.target.result + "\" data-file='"+f.name+"' class='selFile' title='Click to remove'>" + f.name + "<br clear=\"left\"/></div>";
						selDiv.append(html);
						
					}
					reader.readAsDataURL(f); 	
				});
			}

        	function cancelar() {

        		document.getElementById('nremitente').value = '0';
        		document.getElementById('destinatario').value = '';
        		document.getElementById('cc').value = '';
        		document.getElementById('cco').value = '';

        		editor.setData('');

        		window.opener.regresar();
        		window.close();
        	}

        	function plantilla(valor, sel)
            {       
                if (valor == 1) {
            		editor.setData( 'Se&ntilde;or(a)<br><strong>' + <?="'".$_POST[3]."'"?> + '</strong><br />' + <?="'".$_POST[1]."'"?> + '<br /><br /><br />'+
            				<?="'".$_SESSION['depe_nomb']."'"?> + '<br />' + '' );
                }
                else if (valor == 2) {
                	editor.setData( 'Se&ntilde;ores (es):<br />' +
            				'' + <?="'".$_POST[3]."'"?> + '<br />' +
            				'CORREO ELECTR&Oacute;NICO: ' + <?="'".$_POST[1]."'"?> + '<br />' +
            				'<br />' +
            				'<b>Asunto: Devoluci&oacute;n Factura Electr&oacute;nica y/o Nota Cr&eacute;dito/Nota D&eacute;bito.</b><br />' +
            				'<br />' +
            				'En calidad de supervisor(a) del Contrato No. XXXX de fecha _____, remitida al DNP con radicado No. '+ <?="'".$_POST[0]."'"?> +' el d&iacute;a '+
                        	'___________, dentro del tiempo de oportunidad establecido en el Art&iacute;culo 86 de la ley 1676 del 2013, devuelvo la ' +
                        	'Factura Electr&oacute;nica No. __________ de fecha _______ por las siguientes razones:'+
                        	'<br />' +
                        	'<ol><li>Raz&oacute;n uno.</li><li>Raz&oacute;n dos.</li><li>Raz&oacute;n tres.</li></ol>' +
            				'<br />' +
            				'Atentamente,<br />' +
            				'<br />' +
            				'<?=$_SESSION['usua_nomb'] ?>.<br />' +
            				'Supervisor.<br/>' +
            				'<br />' );
                }
                else {
                	editor.setData('');
                }
            }

        	function alertaTras5seg() {
        		setTimeout(mostrarAlerta, 5000);
        	}

        	function mostrarAlerta() {
        		alert('Han pasado 5 segundos desde la carga de la página');
        	}

        		
        	function enviarRespuesta()
        	{
            	debugger;
            	
        		$.ajaxSetup({ async :true});
        		
        		$.blockUI({
          	      	message: $('#divEspera'),
          	      	css: {
          	      		top: '60px',
          	        	left: '120px',
          	      		width: '980px',
              	        border: 'none',
              	        padding: '5px',
              	        backgroundColor: '#DFE9F6',
              	        opacity: '10',
              	        color: '#000',
              	        fontSize: '12px',
              	        fontFamily: 'Verdana,Arial',
              	        fontWeight: 100,
              	      	cursor: null 
              		},
              		overlayCSS:  { backgroundColor: '#FFFFFF',opacity:0.0,cursor:'wait'},
              		ignoreIfBlocked: false
          		});

        		alertaTras5seg();
        		
        		var parametros = {
                		"consulta" : 1
                	};

                var formdata = new FormData();

                formdata.append("respuesta", 1);
                var sel = document.getElementById("nremitente");
                var text = sel.options[sel.selectedIndex].text;
               	let correos = [text, document.getElementById("destinatario").value, document.getElementById("cc").value, document.getElementById("cco").value];

                formdata.append("correos", correos);

                var i = 0;
                for(i=0; i < storedFiles.length; i++) {
                	formdata.append('file' + i, storedFiles[i]);	
        		}
        		
                var editorData = editor.getData();
                var cont = editorData.replace(/&nbsp;/gi, ' ');
                cont = cont.replace(/&/g, "%26");
              	formdata.append('content', cont);

              	var passedArray = javascript_array;
              	var jsonData =  JSON.stringify(passedArray);
            	formdata.append('data', jsonData);

    	        $.ajax({
                		url: '../../../class_control/respuestaRapida.php',
                		type: 'POST',
                		cache: false,
                		async: false,
                		data:  formdata,
                		contentType: false,
                	    enctype: 'multipart/form-data',
                	    processData: false,
                		success: function(text) {
                			$.unblockUI();
                			debugger;
                      		var myObj = JSON.parse(text);
                			if (myObj) {
                				if (myObj['success'] == true) {
                    				var radicado = myObj['msg'];
                        			alert("	Su solicitud ha sido radicada con el n\u00FAmero " + radicado);
                        			tipificar(radicado);
                				}
                				else {
                					alert(myObj['msg']);
                				}
                			} else {
                				alert("Error en el proceso, consulte el administrador del sistema.");
                			} 
                			$.unblockUI();
                			cancelar();
                		},
                		error: function(text) { 
                 			$.unblockUI();
                 			cancelar();
                    		alert('Se ha producido un error ' + text); 
                    	}
                	});
        	}

        	function previsualizar()
        	{
        		$.blockUI({
          	      	message: $('#divPdf'),
          	      	css: {
          	      		top: '60px',
          	        	left: '120px',
          	      		width: '980px',
              	        border: 'none',
              	        padding: '5px',
              	        backgroundColor: '#DFE9F6',
              	        opacity: '10',
              	        color: '#000',
              	        fontSize: '12px',
              	        fontFamily: 'Verdana,Arial',
              	        fontWeight: 100,
              	      	cursor: null 
              		},
              		overlayCSS:  { backgroundColor: '#FFFFFF',opacity:0.0,cursor:'wait'},
              		ignoreIfBlocked: false
          		});

        		var parametros = {
                		"pdf" : 1
                	};

        		var formdata = new FormData();

                formdata.append("respuesta", 1);
                var sel = document.getElementById("nremitente");
                var text = sel.options[sel.selectedIndex].text;
               	let correos = [text, document.getElementById("destinatario").value, document.getElementById("cc").value, document.getElementById("cco").value];

                formdata.append("correos", correos);

                var i = 0;
                for(i=0; i < storedFiles.length; i++) {
                	formdata.append('file' + i, storedFiles[i]);	
        		}
        
                var editorData = editor.getData();
                var cont = editorData.replace(/&nbsp;/gi, ' ');
                cont = cont.replace(/&/g, "%26");
              	formdata.append('content', cont);

              	var passedArray = javascript_array;
              	var jsonData =  JSON.stringify(passedArray);
            	formdata.append('data', jsonData);

            	formdata.append('pdf', 1);
            	
        		$.ajax({
            		url: '../../../class_control/respuestaRapida.php',
            		type: 'POST',
            		cache: false,
            		async: false,
            		data:  formdata,
            		contentType: false,
            	    enctype: 'multipart/form-data',
            	    processData: false,
            		success: function(text) {
            			$.unblockUI();
            			debugger;
                  		var myObj = JSON.parse(text);
            			if (myObj) {
            				if (myObj['success'] == true) {
            					var dats = myObj['msg'].split("-");
            					window.open(dats[0], '_blank');
            					//download(dats[0], dats[1]);
            				}
            				else {
            					alert(myObj['msg']);
            				}
            			} else {
            				alert("Error en el proceso, consulte el administrador del sistema.");
            			} 
            			$.unblockUI();
            		},
            		error: function(text) { 
             			$.unblockUI();
             			cancelar();
                		alert('Se ha producido un error ' + text); 
                	}
            	});
        	}

        	function download(textInput, filename) {

                var element = document.createElement('a');
                element.setAttribute('href','data:application/pdf;charset=utf-8,' + encodeURIComponent(textInput));
                //element.setAttribute('download', filename);
                element.setAttribute('target','_blank');
                document.body.appendChild(element);
                element.click();
                //document.body.removeChild(element);
          	}
    		
        	function tipificar(noradicado) {

                var left = (screen.width/2) - (750/2);
                var top = (screen.height/2) - (500/2);
                window.open("<?= $ruta_raiz ?>/radicacion/tipificar_documento.php?krd=<?= $_SESSION['krd'] ?>&nurad=" + noradicado + "&ind_ProcAnex=<?= $ind_ProcAnex ?>&codusua=<?= $codusua ?>&coddepe=<?= $coddepe ?>&tsub=" + 1 + "&codserie=" + 0 + "&texp=<?= $texp ?>", "Tipificacion_Documento_Anexos", "height=500,width=750,scrollbars=yes, top=" + top + ', left=' + left);

            }
        </script>
	</head>
	<body data-editor="ClassicEditor" data-collaboration="false">
		<header>
			<div>
				
			</div>
		</header>
		
	
		<div id="divEditorheader">
			<table style="width: 100%; background-color: #006699;">
				<tr>
					<td style="text-align: center; width: 95%;" colspan="4"><label
						id="lbTitulo"
						style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-style: normal; font-weight: bolder; color: #FFF; background-color: #069; text-transform: uppercase; text-align: center;">RESPUESTA R&Aacute;PIDA PARA EL RADICADO No. <?php print $verrad;?> </label>
					</td>
				</tr>
			</table>
		</div>
		
		<table id="tbrepuesta" style="width: 100%;">
    		<tr>
    			<td style="background-color: #E0E6E7; " colspan="4">
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 11px;font-style: normal;line-height: 15px;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">Para enviar a m&uacute;ltiples correos Separe con ";" </label>
    			</td>
    		</tr>
    		<tr style="width: 100%">
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 11px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">Remitente:</label>
    			</td>
    			<td>
    				<select id="nremitente" name="nremitente" onchange="plantilla(this.value, this);" style="width: 200px;font-size: 10px;">
    					<?php 
    					foreach ($emailRemitente as $clave=>$valor) {
    					    echo "<option value=$clave>$valor</option>";
    					} 
    				    ?>
    				</select>
    			</td>
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 11px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">Destinatario:</label>
    			</td>
    			<td style="width: 300px;">
    				<input type="text" id="destinatario" name="destinatario" maxlength="256" style="width: 500px;font-size: 10px;"/>
    			</td>
    		</tr>
    		<tr>
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 11px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">CC:</label>
    			</td>
    			<td>
    				<input type="text" id="cc" name="cc" maxlength="256" style="width: 200px;font-size: 10px;"/>
    			</td>
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 11px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">CCO:</label>
    			</td>
    			<td style="width: 300px;">
    				<input type="text" id="cco" name="cco" maxlength="256" style="width: 500px;font-size: 10px;"/>
    			</td>
    		</tr>
    		<tr>
    			<td style="text-align: center; background-color: #006699;" colspan="4">
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 10px;font-style: normal;font-weight: bolder;color: #FFF;background-color: #069;text-indent: 5pt;text-transform: uppercase;text-align: center; ">El tama&ntilde;o m&aacute;ximo permitido para anexar archivos es de <?php echo convertToReadableSize(ini_get('upload_max_filesize')); ?> </label>
    			</td>
    		</tr>
    		<tr>
    			<td>
    			
    			</td>
    		</tr>
    	</table>
		
		<div class="centered">
    		<div class="row row-editor">
    			<div class="editor-container">
    				<div class="editor"></div>
    			</div>
    		</div>
		</div>
	
		<table id="tbrepuesta" style="width: 100%">
        	<tr>
        		<td style="text-align: left; width: 10%">
        			Adjuntos :
        		</td>
        		<td style="text-align: left; width: 60%">
                    <input id="myfiles" name="files[]" type="file" class="multi" accept="tif|odt|pdf|PDF|doc|docx|tiff|avi|jpg|jpeg|txt|gif|png|csv|xls|xlsx|eml|ppt|pptx|zip|msg|html|htm|rtf" maxlength="15" />
                     <div id="myfiles-list"></div>
				</td>
        		<td style="text-align: right">
        			<input type="button" value="Enviar" style="font-size: 14px;" onclick="enviarRespuesta();" />&nbsp;&nbsp;
        			<input type="button" value="Previsualizar" style="font-size: 14px;" onclick="previsualizar();" />&nbsp;&nbsp;
        			<input type="button" value="Cancelar" style="font-size: 14px;" onclick="cancelar();" />
        		</td>
        	</tr>
        </table>
        <div id="divEspera" class="popup-wrapper">
            <div class="popup">
                <div class="popup-content">
                	<img src="../../../imagenes/loading.gif" />
                    <h3>POR FAVOR ESPERE...</h3>
                    <p>&#161; Se est&aacute; generando la respuesta r&aacute;pida &#33;</p>
                </div>
            </div>
        </div>
        
        <div id="divPdf" class="popup-wrapper">
            <div class="popup">
                <div class="popup-content">
                	<img src="../../../imagenes/loading.gif" />
                    <h3>POR FAVOR ESPERE...</h3>
                    <p>&#161; Se est&aacute; generando el archivo pdf &#33;</p>
                </div>
            </div>
        </div>
		<footer>

		</footer>
		 
		<script>	
			
            
		</script>
	</body>
</html>