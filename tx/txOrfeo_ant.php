<?php
//header('content-type:text/html;charset=utf-8');

if (!$ruta_raiz)
    $ruta_raiz = "..";
$verrad = $_GET['verrad'];
$codusuario = $_SESSION['codusuario'];
function convertToReadableSize($size){
	$base = log($size) / log(1024);
	$suffix = array("", "KB", "MB", "GB", "TB");
	$f_base = floor($base);
	return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}

?>
<meta charset="UTF-8">
<!-- <link rel="stylesheet" type="text/css" href="styles.css">  -->
<link rel="stylesheet" type="text/css" href="<?= $ruta_raiz ?>/js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="<?= $ruta_raiz ?>/js/spiffyCal/spiffyCal_v2_1.js"></script>
<?php
if ($verrad) {
    define('PATHMVC', './orfeo.api/');
    $_GET['pathMVC'] = PATHMVC;
    include_once 'include/class/ConectorMVC.class.php';
    $obj = new ConectorMVC();
    ?>
    <div id="div-listado" style="display: none"> </div>
    <script type="text/javascript">
    <?php
    $obj->setPathFuncionalidad("_anexo/obtenerExtensionTiposAnexoJS");
    $obj->init();
    ?>
    </script>
    
    <!--<link rel="stylesheet" href="../estilos/bootstrap/bootstrap.min.css" />-->

    <link rel="stylesheet" href="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/ext-4.2.1/resources/css/ext-all.css" />
    <script type="text/javascript" src="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/app/util/listaCarpeta.config.js.php?carpeta=0&tipo_carpt=0&pathMVC=./orfeo.api/&NoRadicado=<?php echo $verrad ?>"></script>
    <script type="text/javascript" src="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/ext-4.2.1/ext-all.js"></script>
    <script type="text/javascript" src="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/app/app.js"></script>
    <script type="text/javascript" src="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/ext-4.2.1/ux/Ext.ux.form.HtmlLintEditor.js"></script>  
    <script type="text/javascript" src="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/ext-4.2.1/ux/Ext.ux.form.Multiupload.js"></script> 
    
	
<?php } ?>

	<!-- <script type="text/javascript" src="../js/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/jquery.blockUI.js"></script>
      <script src='../js/jquery.MultiFile.js' type="text/javascript" language="javascript"></script>  -->
    <!-- <script src="https://cdn.ckeditor.com/ckeditor5/29.0.0/classic/ckeditor.js"></script> 
    <script src="../lib/ckeditor/build/ckeditor.js"></script> 
    <script src="../lib/ckeditor/build/imageupload.js"></script>  -->
    <!-- <script type="text/javascript" src="../lib/ckeditor5/uploadadapter.js"></script>
    <script type="text/javascript" src="../lib/ckeditor5/utils.js"></script>
     <script type="text/javascript" src="../lib/ckeditor5/Ext.ux.form.CKEditor.js"></script>  -->
    
    <!-- <script type="text/javascript" src="../js/jquery.MultiFile.js"></script>
     <script type="text/javascript" src="../js/jquery.MultiFile.min.js"></script> -->
    
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
<!-- Funcion que activa el sistema de marcar o desmarcar todos los check  -->
    setRutaRaiz('<?= $ruta_raiz ?>');

    var selDiv = "";
	var storedFiles = [];
	
    $( document ).ready(function() {
    	let editor;

    	///var myEditor = null;
    	
    	/*ClassicEditor
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
						'imageUpload',
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
				} );*/
		
        //editor.setData(''); 

    	$("#myfiles").on("change", handleFileSelect);

    });

    function handleFileSelect(e) {
        debugger;
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
	
    function formatBytes(from){
    	var number = from.substring(0, from.length-1);
    	switch(from.toUpperCase(from.substring(from.length-1))){
    		case "KB":
    			return number*1024;
    		case "MB":
    			return number*Math.pow(1024,2);
    		case "GB":
    			return number*Math.pow(1024,3);
    		case "TB":
    			return number*Math.pow(1024,4);
    		case "PB":
    			return number*Math.pow(1024,5);
    		default:
    			return from;
    	}
    }
    
    function markAll() {

        if (document.form1.elements['checkAll'].checked)
            for (i = 1; i < document.form1.elements.length; i++)
                document.form1.elements[i].checked = 1;
        else
            for (i = 1; i < document.form1.elements.length; i++)
                document.form1.elements[i].checked = 0;
    }
<?php
// print ("El control agenda en tx($controlAgenda");
$ano_ini = date("Y");
$mes_ini = substr("00" . (date("m") - 1), -2);
if ($mes_ini == 0) {
    $ano_ini == $ano_ini - 1;
    $mes_ini = "12";
}
$dia_ini = date("d");
if (!$fecha_ini)
    $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
$fecha_busq = date("Y/m/d");
if (!$fecha_fin)
    $fecha_fin = $fecha_busq;

$emailRemitente = array("");
$emailRemitenteDefault = "";
if ($_SESSION["usua_email"]) {
    if (strpos(strtolower($_SESSION["usua_email"]), "web") !== false) {
        $emailRemitenteDefault = strtolower($_SESSION["usua_email"]);
        $emailRemitente[] = strtolower($_SESSION["usua_email"]);
    }
}
if ($_SESSION["usua_email_1"]) {
    if (strpos(strtolower($_SESSION["usua_email_1"]), "web") !== false) {
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
?>
</script>
<?php
require_once (ORFEOPATH . "pestanas.js");
/**  TRANSACCIONES DE DOCUMENTOS
 *  @depsel number  contiene el codigo de la dependcia en caso de reasignacion de documentos
 *  @depsel8 number Contiene el Codigo de la dependencia en caso de Informar el documento
 *  @carpper number Indica codigo de la carpeta a la cual se va a mover el documento.
 *  @codTx   number Indica la transaccion a Trabajar. 8->Informat, 9->Reasignar, 21->Devlver
 */
?>
<script language="JavaScript" type="text/JavaScript">
    // Variable que guarda la ultima opcion de la barra de herramientas de funcionalidades seleccionada
    seleccionBarra = -1;<!--
    function MM_swapImgRestore() { //v3.0
    var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
    }
    function MM_preloadImages() { //v3.0
    var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
    }
    function MM_findObj(n, d) { //v4.01
    var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
    if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
    for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
    if(!x && d.getElementById) x=d.getElementById(n); return x;
    }
    function MM_swapImage() { //v3.0
    var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
    if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
    }
    //-->
</script>
<script>
    if (typeof String.prototype.trim !== 'function') {
        String.prototype.trim = function() {
            //Your implementation here. Might be worth looking at perf comparison at
            //http://blog.stevenlevithan.com/archives/faster-trim-javascript
            //
            //The most common one is perhaps this:
            return this.replace(/^\s+|\s+$/g, '');
        }
    }
    function vistoBueno() {
        changedepesel(9);
        document.getElementById('EnviaraV').value = 'VoBo';
        envioTx();
    }

    function devolver() {
        changedepesel(12);
        envioTx();
    }

    function txAgendar() {
        if (!validaAgendar('SI'))
            return;
        changedepesel(14);
        envioTx();
    }

    function txNoAgendar() {
        changedepesel(15);
        envioTx();
    }

    function archivar() {
        changedepesel(13);
        envioTx();
    }

    function nrr() {
        changedepesel(16);
        envioTx();
    }

    function masivaTRD() {
        try {
            var list = new Array();
            if ((grid.getSelectionModel().getSelection().length < 1)) {
                alert("Debe seleccionar almenos 1 radicado");
                return;
            }
            ;

            for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
                list.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);
            }
            ;

            window.open("accionesMasivas/masivaAsignarTrd.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + list, "Masiva_Asignacion_TRD", "height=650,width=750,scrollbars=yes");
        } catch (error) {
            window.open("accionesMasivas/masivaAsignarTrd.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + '<?= print_r($verrad) ?>', "Masiva_Asignacion_TRD", "height=650,width=750,scrollbars=yes");
        }
        ;
    }
    ;


    function masivaPrestamo() {
        try {
            var list = new Array();
            if ((grid.getSelectionModel().getSelection().length < 1)) {
                alert("Debe seleccionar almenos 1 radicado");
                return;
            }
            ;

            for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
                list.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);
            }
            ;

            window.open("accionesMasivas/masivaPrestamoRad.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + list, "Masiva_Prestamo_Rad", "height=650,width=750,scrollbars=yes");
        } catch (error) {
            window.open("accionesMasivas/masivaPrestamoRad.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + '<?= print_r($verrad) ?>', "Masiva_Prestamo_Rad", "height=650,width=750,scrollbars=yes");
        }
        ;
    }
    ;


    function masivaTemaSector() {
        try {
            var list = new Array();
            if ((grid.getSelectionModel().getSelection().length < 1)) {
                alert("Debe seleccionar almenos 1 radicado");
                return;
            }
            ;

            for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
                list.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);
            }
            ;

            window.open("accionesMasivas/masivaTemaSector.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + list, "Masiva_Asignar_Tema_Sector", "height=650,width=750,scrollbars=yes");
        } catch (error) {
            window.open("accionesMasivas/masivaTemaSector.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + '<?= print_r($verrad) ?>', "Masiva_Prestamo_Rad", "height=650,width=750,scrollbars=yes");
        }
        ;
    }
    ;


    function masivaIncluir() {
        try {
            var list = new Array();
            if ((grid.getSelectionModel().getSelection().length < 1)) {
                alert("Debe seleccionar almenos 1 radicado");
                return;
            }
            ;

            for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
                list.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);
            }
            ;

            window.open("accionesMasivas/masivaIncluirExp.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + list, "Masiva_IncluirExp", "height=650,width=750,scrollbars=yes");
        } catch (error) {
            window.open("accionesMasivas/masivaIncluirExp.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + '<?= print_r($verrad) ?>', "Masiva_IncluirExp", "height=650,width=750,scrollbars=yes");
        }
        ;
    }
    ;


    function envioTx() {
        sw = 0;
<?php
if (!$verrad) {
    ?>
            if (grid.getSelectionModel().getSelection().length == 0) {
                alert("Debe seleccionar uno o mas radicados");
                return;
            }
            ;
            var list = new Array();
            var listNoRaiz = new Array();
            var listNoRaizId = new Array();
            for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
                list.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);
                if (grid.getSelectionModel().getSelection()[i].data.raiz != 'raiz')
                    listNoRaiz.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);

                if (grid.getSelectionModel().getSelection()[i].data.raiz != 'raiz')
                    listNoRaizId.push(grid.getSelectionModel().getSelection()[i].data.id);
            }

            // radicados seleccionados
            var seleccionados = document.getElementById('seleccionados');
            var noraiz = document.getElementById('noraiz');
            var noidraiz = document.getElementById('noidraiz');

            seleccionados.value = list.join(",").trim();
            // radicados seleccionados que son derivados
            noraiz.value = listNoRaiz.join(",").trim();
            // id de los derivados
            noidraiz.value = listNoRaizId.join(",").trim();
    <?php
}
?>
        document.form1.submit();
    }

    function respuestaTx() {
        
    	//window.open("../lib/ckeditor/sample/index.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + '<?= print_r($verrad) ?>', "Respuesta Rápida", "height=650,width=750,scrollbars=yes");
        
        try {
    <?php 
            if (!$verrad) { ?>
                if ((grid.getSelectionModel().getSelection().length != 1)) {
                    alert("Debe seleccionar UN(1) radicado");
                    return;
                }
                if (grid.getSelectionModel().getSelection()[0].data.modenvio == 'F') {
                    alert("El radicado tiene preferencia de modalidad de envio fisico!!\nGestione el tramite de la forma tradicional.");
                    return;
                }
    <?php
            }
    ?>
            formRespuestaRapida();      

           /*$.blockUI({
      	      	message: $('#divEditor'),
      	      	css: {
      	      		top: '60px',
      	        	left: '120px',
      	      		width: '980px',
          	        border: 'none',
          	        padding: '5px',
          	        backgroundColor: '#DFE9F6',
          	        '-webkit-border-radius': '10px',
          	        '-moz-border-radius': '10px',
          	        opacity: '10',
          	        color: '#000',
          	        fontSize: '12px',
          	        fontFamily: 'Verdana,Arial',
          	        fontWeight: 100,
          	      	cursor: null 
          		} 
      		});

            var JSON = JSON || {};

            // implement JSON.stringify serialization
            JSON.stringify = JSON.stringify || function(obj) {

                var t = typeof (obj);
                if (t != "object" || obj === null) {

                    // simple data type
                    if (t == "string")
                        obj = '"' + obj + '"';
                    return String(obj);

                }
                else {

                    // recurse array or object
                    var n, v, json = [], arr = (obj && obj.constructor == Array);

                    for (n in obj) {
                        v = obj[n];
                        t = typeof(v);

                        if (t == "string")
                            v = '"' + v + '"';
                        else if (t == "object" && v !== null)
                            v = JSON.stringify(v);

                        json.push((arr ? "" : '"' + n + '":') + String(v));
                    }

                    return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
                }
            };
            var rad = new Array();
            <?php if ($verrad) { ?>
            	if (grid.store.data.items[0])
                grid.selModel.doSelect(grid.store.data.items[0])
            <?php } ?>
            if (grid.getSelectionModel().getSelection()[0].data.nroradicado.toString().substring(13, 14) != "2")
            {
                alert("Solo puede dar respuesta a Radicados de entrada!");
                return;
            }
            if (grid.getSelectionModel().getSelection()[0].data.preferenciaEnvio != null &&
                    grid.getSelectionModel().getSelection()[0].data.preferenciaEnvio.toString() == "F")
            {
                alert("Debe gestionar respuesta y env\xedo por medio f\xedsico");
                return;
            }
            rad.push(grid.getSelectionModel().getSelection()[0].data.nroradicado);
            if (grid.getSelectionModel().getSelection()[0].data.dirmail != null)
            {
                rad.push(grid.getSelectionModel().getSelection()[0].data.dirmail.toString().trim());
            } else {
                rad.push("");
            }
            if (grid.getSelectionModel().getSelection()[0].data.asunto != null) {
                rad.push(grid.getSelectionModel().getSelection()[0].data.asunto.toString().trim());
            }
            else {
                rad.push("");
            }
            if (grid.getSelectionModel().getSelection()[0].data.nombrecontacto) {
                rad.push(grid.getSelectionModel().getSelection()[0].data.nombrecontacto.toString().trim());
            } else {
                rad.push("");
            }

            var destino = document.getElementById('destinatario');
            if (destino) {
                destino.value = rad[1];
            }

            var varRad = document.getElementById('lbTitulo');
            if (varRad) {
            	$('#lbTitulo').text("RESPUESTA RÁPIDA PARA EL RADICADO No. " + rad[0]);
            }

            /*var w = window.open("../lib/ckeditor/sample/index.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>&radicados=" + '<?= print_r($verrad) ?>', "Respuesta Rápida", "height=650,width=750,scrollbars=yes");
            w.document.open();
            w.document.write(rad);
            w.document.close();*/

            /*var param = { 'uid' : '1234'};		    		
            OpenWindowWithPost("../lib/ckeditor/sample/index.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?=$krd?>&verrad=<?=$verrad?>", 
            "width=920,height=650,left=100,top=100,menubar=no,location=no,resizable=yes,scrollbars=yes,status=yes", 
            "Respuesta", rad);	*/
            
        } catch (error) {
                alert('Hay un error al generar la respuesta. ' + error)

        }
    }

    function OpenWindowWithPost(url, windowoption, name, params)
    {
             var form = document.createElement("form");
             form.setAttribute("method", "post");
             form.setAttribute("action", url);
             form.setAttribute("target", name);

             for (var i in params) {
                 if (params.hasOwnProperty(i)) {
                     var input = document.createElement('input');
                     input.type = 'hidden';
                     input.name = i;
                     input.value = params[i];
                     form.appendChild(input);
                 }
             }
             
             document.body.appendChild(form);
             
             //note I am using a post.htm page since I did not want to make double request to the page 
            //it might have some Page_Load call which might screw things up.
             let newWin = window.open("post.htm", name, windowoption);
             
             form.submit();
             
             document.body.removeChild(form);
     }
    
    function plantilla(valor, sel)
    {
    	var JSON = JSON || {};

        // implement JSON.stringify serialization
        JSON.stringify = JSON.stringify || function(obj) {

            var t = typeof (obj);
            if (t != "object" || obj === null) {

                // simple data type
                if (t == "string")
                    obj = '"' + obj + '"';
                return String(obj);

            }
            else {

                // recurse array or object
                var n, v, json = [], arr = (obj && obj.constructor == Array);

                for (n in obj) {
                    v = obj[n];
                    t = typeof(v);

                    if (t == "string")
                        v = '"' + v + '"';
                    else if (t == "object" && v !== null)
                        v = JSON.stringify(v);

                    json.push((arr ? "" : '"' + n + '":') + String(v));
                }

                return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
            }
        };
        var rad = new Array();
        <?php if ($verrad) { ?>
        	if (grid.store.data.items[0])
            grid.selModel.doSelect(grid.store.data.items[0])
        <?php } ?>
        if (grid.getSelectionModel().getSelection()[0].data.nroradicado.toString().substring(13, 14) != "2")
        {
            alert("Solo puede dar respuesta a Radicados de entrada!");
            return;
        }
        if (grid.getSelectionModel().getSelection()[0].data.preferenciaEnvio != null &&
                grid.getSelectionModel().getSelection()[0].data.preferenciaEnvio.toString() == "F")
        {
            alert("Debe gestionar respuesta y env\xedo por medio f\xedsico");
            return;
        }
        rad.push(grid.getSelectionModel().getSelection()[0].data.nroradicado);
        if (grid.getSelectionModel().getSelection()[0].data.dirmail != null)
        {
            rad.push(grid.getSelectionModel().getSelection()[0].data.dirmail.toString().trim());
        } else {
            rad.push("");
        }
        if (grid.getSelectionModel().getSelection()[0].data.asunto != null) {
            rad.push(grid.getSelectionModel().getSelection()[0].data.asunto.toString().trim());
        }
        else {
            rad.push("");
        }
        if (grid.getSelectionModel().getSelection()[0].data.nombrecontacto) {
            rad.push(grid.getSelectionModel().getSelection()[0].data.nombrecontacto.toString().trim());
        } else {
            rad.push("");
        }
        
        if (valor == 1) {
    		editor.setData( 'Se&ntilde;or(a)<br><strong>' + rad[3] + '</strong><br/>' + rad[1] + '<br/><br/><br/>'+
    				<?="'".$_SESSION['depe_nomb']."'"?> + '<br>' + sel.options[sel.selectedIndex].text );
        }
        else if (valor == 2) {
        	editor.setData( 'Se&ntilde;ores (es):<br/>' +
    				'' + rad[3] + '<br/>' +
    				'CORREO ELECTR&Oacute;NICO: ' + rad[1] + '<br/>' +
    				'<br/>' +
    				'<b>Asunto: Devoluci&oacute;n Factura Electr&oacute;nica y/o Nota Cr&eacute;dito/Nota D&eacute;bito.</b><br/>' +
    				'<br/>' +
    				'En calidad de supervisor(a) del Contrato No. XXXX de fecha _____, remitida al DNP con radicado No. '+rad[0]+' el d&iacute;a '+
                	'___________, dentro del tiempo de oportunidad establecido en el Art&iacute;culo 86 de la ley 1676 del 2013, devuelvo la ' +
                	'Factura Electr&oacute;nica No. __________ de fecha _______ por las siguientes razones:'+
                	'<br/>' +
                	'<ol><li>Raz&oacute;n uno.</li><li>Raz&oacute;n dos.</li><li>Raz&oacute;n tres.</li></ol>' +
    				'<br/>' +
    				'Atentamente,<br/>' +
    				'<br/>' +
    				'<?=$_SESSION['usua_nomb'] ?>.<br/>' +
    				'Supervisor.<br/>' +
    				'<br/>' );
        }
        else {
        	editor.setData('');
        }
    }

    function sleep(milliseconds) {
  		var start = new Date().getTime();
    	for (var i = 0; i < 1e7; i++) {
    		if ((new Date().getTime() - start) > milliseconds) {
    	   		break;
    	  	}
    	}
    }
	
	function enviarRespuesta()
	{
        const popup = document.querySelector('.popup-wrapper');
        popup.style.display = 'block';
        
		var parametros = {
        		"consulta" : 1
        	};

        debugger;
		var myfiles = document.getElementById("myfiles");
        //var files = myfiles.files;
        //var files = $("#myfiles").val();

        var formdata = new FormData();

        formdata.append("respuesta", 1);
        var sel = document.getElementById("nremitente");
        var text = sel.options[sel.selectedIndex].text;
       	let correos = [text, document.getElementById("destinatario").value, document.getElementById("cc").value, document.getElementById("cco").value];

        formdata.append("correos", correos);

        for(var i=0, len=storedFiles.length; i<len; i++) {
        	formdata.append('files', storedFiles[i]);	
		}
		
        /*for (i = 0; i < files.length; i++) {
        	formdata.append('file' + i, files[i]);
        }*/
        //var input = $('#myfiles')[0];
        //formdata.append('files', input.val());
        
        var editorData = editor.getData();
        var cont = editorData.replace(/&nbsp;/gi, ' ');
        cont = cont.replace(/&/g, "%26");
      	formdata.append('content', cont);
        
		//var formData = new FormData();
		//formData.append('file', $('#archivos'));

		/*var $input = document.getElementById('form1');
		var formdata = new FormData($input);
		//formdata.append('file', document.getElementById('archivos'));
	    
		var ins = document.getElementById('archivos').files.length;
		for (var x = 0; x < ins; x++) {
			formdata.append("files[]", document.getElementById('archivos').files[x]);
		}
		var arch = document.getElementById('archivos').files
		for (let i = 0; i < arch.length; i++) {
			formdata.append(i, arch[i])
		}*/
        
		var store = storeListaCarpeta;
        var selection = grid.getSelectionModel().getSelection();
        if (selection.length > 0) {
        	var jsonData =  JSON.stringify(pluck(selection, 'data'));
        	formdata.append('data', jsonData);
        }
		
        	$.ajax({
        		url: '../class_control/respuestaRapida.php',
        		type: 'POST',
        		cache: false,
        		async: false,
        		data:  formdata,
        		contentType: false,
        	    enctype: 'multipart/form-data',
        	    processData: false,
        		success: function(text) {
              		popup.style.display = 'none';
              		
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
        		},
        		error: function(text) { 
        			popup.style.display = 'none';
        			$.unblockUI();
            		alert('Se ha producido un error ' + text); 
            	}
        	});

    		//$("#divMensaje").css('display', 'none');
	}

	function cancelar() {

		document.getElementById('nremitente').value = '0';
		document.getElementById('destinatario').value = '';
		document.getElementById('cc').value = '';
		document.getElementById('cco').value = '';
		/*var archiv = document.getElementById('archivos');
		if (archiv) {
			var $input = $("#archivos");
			$input.replaceWith($input.val('').clone(true));
		}*/
		editor.setData('');
		$.unblockUI();
	}

	function pluck(list, propertyName) {
	    return list.map(function (v) { return v[propertyName]; })
	}
	
    function displayStatus( editor ) {
        const pendingActions = editor.plugins.get( 'PendingActions' );
        const statusIndicator = document.querySelector( '#editor-status' );

        pendingActions.on( 'change:hasAny', ( evt, propertyName, newValue ) => {
            if ( newValue ) {
                statusIndicator.classList.add( 'busy' );
            } else {
                statusIndicator.classList.remove( 'busy' );
            }
        } );
    }

    function limpiarInputfile(id) {
    	try {
    	    ctrl.value = null;
    	} catch(ex) { }
    	if (ctrl.value) {
    	    ctrl.parentNode.replaceChild(ctrl.cloneNode(true), ctrl);
    	}
    }
    
    function formRespuestaRapida() {

        try {
            var JSON = JSON || {};

            // implement JSON.stringify serialization
            JSON.stringify = JSON.stringify || function(obj) {

                var t = typeof (obj);
                if (t != "object" || obj === null) {

                    // simple data type
                    if (t == "string")
                        obj = '"' + obj + '"';
                    return String(obj);

                }
                else {

                    // recurse array or object
                    var n, v, json = [], arr = (obj && obj.constructor == Array);

                    for (n in obj) {
                        v = obj[n];
                        t = typeof(v);

                        if (t == "string")
                            v = '"' + v + '"';
                        else if (t == "object" && v !== null)
                            v = JSON.stringify(v);

                        json.push((arr ? "" : '"' + n + '":') + String(v));
                    }

                    return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
                }
            };
            //var list = new Array();
            var rad = new Array();
<?php if ($verrad) { ?>
                if (grid.store.data.items[0])
                    grid.selModel.doSelect(grid.store.data.items[0])
<?php } ?>
            //var files = new Array();
            //for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
            if (grid.getSelectionModel().getSelection()[0].data.nroradicado.toString().substring(13, 14) != "2")
            {
                alert("Solo puede dar respuesta a Radicados de entrada!");
                return;
            }
            if (grid.getSelectionModel().getSelection()[0].data.preferenciaEnvio != null &&
                    grid.getSelectionModel().getSelection()[0].data.preferenciaEnvio.toString() == "F")
            {
                alert("Debe gestionar respuesta y env\xedo por medio f\xedsico");
                return;
            }
            rad.push(grid.getSelectionModel().getSelection()[0].data.nroradicado);
            if (grid.getSelectionModel().getSelection()[0].data.dirmail != null)
            {
                rad.push(grid.getSelectionModel().getSelection()[0].data.dirmail.toString().trim());
            } else {
                rad.push("");
            }
            if (grid.getSelectionModel().getSelection()[0].data.asunto != null) {
                rad.push(grid.getSelectionModel().getSelection()[0].data.asunto.toString().trim());
            }
            else {
                rad.push("");
            }
            if (grid.getSelectionModel().getSelection()[0].data.nombrecontacto) {
                rad.push(grid.getSelectionModel().getSelection()[0].data.nombrecontacto.toString().trim());
            } else {
                rad.push("");
            }


            /*Ext.onReady(function() {
                var ckEditorForm;
                var ckEditorMinimalForm;
                var ckEditorWin;
                var dlg = new Ext.Window({
                    title: "Window",
                    width: 950,
                    height: 500,
                    minWidth: 100,
                    minHeight: 100,
                    layout: "fit",
                    closeAction: "hide",
                    items: [
                        ckEditorWin = new Ext.ux.form.CKEditor({
                            CKConfig: {
                                language: "en",
                                allowedContent: true,
                                removePlugins: "elementspath,autogrow",
                                uiColor: "#EEEEEE",
                                removeButtons: '',
                                toolbar: [ // http://ckeditor.com/latest/samples/plugins/toolbar/toolbar.html
                                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
                                    { name: 'paragraph',items: [ 'NumberedList', 'BulletedList' ] },
                                    { name: 'links', items: [ 'Link', 'Unlink' ] },
                                    { name: 'insert', items: [ 'Table', 'SpecialChar' ] },
                                    { name: 'clipboard', items: [ 'PasteText', 'PasteFromWord' ] },
                                    { name: 'document', items: [ 'Source' ] }

                                    , '/'
                                    , { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] }
                                ]
                            },
                            value: '<form method="post" name="testform" action="">' +
                                '<table class="mceItemTable">' +
                                '<tbody><tr><td>&nbsp;</td><td>&nbsp;</td></tr>' +
                                '<tr><td>Select 1</td><td><select id="select1" name="select1"><option>option 1</option><option>option 2</option><option>option 3</option></select></td></tr>' +
                                '<tr><td>Radiobutton 1</td><td><input name="radio1" type="radio" class="radio" value="radio1" id="radio1radio1" /><label id="radio1radio1_label" for="radio1radio1">Radiobutton 1</label></td></tr>' +
                                '<tr><td>Checkbox 1</td><td><input name="check1" type="checkbox" value="X" class="checkbox val:mandatory," id="check1" /><label id="check1_label" for="check1">Checkbox 1</label></td></tr>' +
                                '<tr><td>Text 1</td><td><input id="text1" value="text1" name="text1" type="text"/></td></tr>' +
                                '<tr><td>Textarea 1</td><td><textarea id="textarea1" class="val:mandatory," name="textarea1">Text</textarea></td></tr>' +
                                '<tr><td>File 1</td><td><input name="file1" type="file" id="file1" /></td></tr>' +
                                '<tr><td><input class="button" value="Absenden" name="submit" type="submit" /></td>' +
                                '<td><input class="button" value="Zur&uuml;cksetzen" type="reset" /></td></tr></tbody></table></form>'
                          })
                      ]
                });

                new Ext.form.FormPanel({
                    title: "Form",
                    frame: true,
                    items: [
                        new Ext.form.TextField({
                            fieldLabel: "Text"
                        }),
                        ckEditorMinimalForm = new Ext.ux.form.CKEditor({
                            fieldLabel: "Minimal HTML",
                            CKConfig: {
                                removeButtons: '',
                                removePlugins: "elementspath,autogrow",
                                toolbar: [
                                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
                                    { name: 'paragraph',items: [ 'NumberedList', 'BulletedList' ] }
                                ]
                            }
                        }),
                        ckEditorForm = new Ext.ux.form.CKEditor({
                            fieldLabel: "Full Featured HTML",
                            CKConfig: {
                            }
                        })
                      ],
                      buttonAlign: "left",
                      buttons: [
                          new Ext.Button({
                              text: "Open Editor Window",
                              handler: function() {
                                  dlg.show();
                              }
                          }),
                          new Ext.Button({
                              text: "Set Value",
                              handler: function() {
                                  ckEditorForm.setValue("<p>my text</p>");
                                  ckEditorForm.setValue("<p>my text</p>");
                              }
                          }),
                          new Ext.Button({
                              text: "Show Value",
                              handler: function() {
                                  document.getElementById("content").innerHTML = ckEditorForm.getValue();
                              }
                          })
                      ],
                      renderTo: "form"
                });

                Ext.util.Observable.capture(ckEditorForm, function (evname) {
                    if (console) console.log(evname, arguments);
                });
            });*/
            

            Ext.apply(Ext.form.VTypes, {
                'multiemail': function(v) {
                    var array = v.split(';');
                    var valid = true;
                    Ext.each(array, function(value) {
                        if (!this.email(value)) {
                            valid = false;
                            return false;
                        }
                        ;
                    }, this);
                    return valid;
                },
                'multiemailText': 'Este campo solo puede contener direcciones de correo electronico, o una lista de correos electronicos separados por(;) en el formato: "user@domain.com;test@test.com"',
                'multiemailMask': /[a-z0-9_\.\-@\;]/i
            });

            var js_arrayRemitente = [];

            var combo = new Ext.form.ComboBox({
                name: 'perpage',
                width: 300,
                store: new Ext.data.ArrayStore({
                    fields: ['id'],
                    data: [<?php echo '[\'' . implode('\'],[\'', $emailRemitente) . '\']' ?>]
                }),
                mode: 'local',
                value: '<?php echo $emailRemitenteDefault; ?>',
                //listWidth     : 140,
                triggerAction: 'all',
                displayField: 'id',
                valueField: 'id',
                editable: false,
                forceSelection: true,
                fieldLabel: 'Remitente',
                allowBlank: false,
                name: 'nremitente',
                vtype: 'email',
                listeners:{
                    scope: this,
                    'select': function(e) {
				    	var fldTxtArea = Ext.ComponentQuery.query('HtmlLintEditor')[0];
                    	if (e.value==<?= "'".$_SESSION["usua_email_fe"]."'" ?>) {
                    		fldTxtArea.setValue(
                    			'<table style=\'width: 100%;\'>' +
                				'<tr><td>Se&ntilde;ores (es):</td></tr>' +
                				'<tr><td>' + rad[3] + '</td></tr>' +
                				'<tr><td>CORREO ELECTR&Oacute;NICO: ' + rad[1] + '<br /></td></tr>' +
                				'<tr><td>&nbsp;</td></tr>' +
                				'<tr><td><b>Asunto: Devoluci&oacute;n Factura Electr&oacute;nica y/o Nota Cr&eacute;dito/Nota D&eacute;bito.</b><br /></td></tr>' +
                				'<tr><td>&nbsp;</td></tr>' +
                				'<tr><td style=\'text-align:justify;\'>'+
                				'En calidad de supervisor(a) del Contrato No. XXXX de fecha _____, remitida al DNP con radicado No. '+rad[0]+' el d&iacute;a '+
                            	'___________, dentro del tiempo de oportunidad establecido en el Art&iacute;culo 86 de la ley 1676 del 2013, devuelvo la ' +
                            	'Factura Electr&oacute;nica No. __________ de fecha _______ por las siguientes razones:</td></tr>'+
                            	'<tr><td>&nbsp;</td></tr>' +
                            	'<tr><td><ol><li>Raz&oacute;n uno.</li><li>Raz&oacute;n dos.</li><li>Raz&oacute;n tres.</li></ol></td></tr>' +
                				'<tr><td>&nbsp;</td></tr>' +
                				'<tr><td>Atentamente,</td></tr>' +
                				'<tr><td>&nbsp;</td></tr>' +
                				'<tr><td><?=$_SESSION['usua_nomb'] ?>.</td></tr>' +
                				'<tr><td>Supervisor.</td></tr>' +
                				'<tr><td>&nbsp;</td></tr>' +
                            	'</table>');
                    	} else {
                    		fldTxtArea.setValue(
                    				'Se&ntilde;or(a)<br><strong>' + rad[3] + '</strong><br/>' + rad[1] + '<br/><br/><br/>'+
                    				<?="'".$_SESSION['depe_nomb']."'"?> + '<br>' + e.value);
                    	}
                    }
               }
            });

            var maskingAjax = new Ext.data.Connection({
                listeners: {
                    'beforerequest': {
                        fn: function (con, opt) {
                            Ext.get('multiColumnForm').mask('Paso 2 de 2. Enviando Correo Electr\xf3nico ...');
                        },
                        scope: this
                    },
                    'requestcomplete': {
                        fn: function (con, res, opt) {
                            Ext.get('multiColumnForm').unmask();
                        },
                        scope: this
                    },
                    'requestexception': {
                        fn: function (con, res, opt) {
                            Ext.get('multiColumnForsm').unmask();
                        },
                        scope: this
                    }
                }
            });
            
            var form = Ext.widget({
                xtype: 'form',
                id: 'multiColumnForm',
                frame: true,
                bodyPadding: '5 5 0',
                anchor: '95%',
                fieldDefaults: {
                    labelAlign: 'right',
                    msgTarget: 'side'
                },
                items: [
                    {
                        xtype: 'box',
                        autoEl: {cn: 'RESPUESTA R&Aacute;PIDA PARA EL RADICADO No. ' + rad[0]},
                        style: 'font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 10px;font-style: normal;line-height: 25px;font-weight: bolder;color: #FFF;background-color: #069;text-indent: 5pt;text-transform: uppercase;height: 30px;text-align: right;text-align: center;',
                        height: 25
                    },
                    {
                        xtype: 'box',
                        autoEl: {cn: 'Para enviar a m&uacute;ltiples correos Separe con ";"'},
                        style: 'font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 9px;font-style: normal;line-height: 25px;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;height: 30px;text-align: right;text-align: left;',
                        height: 25
                    }
                    ,
                    {
                        xtype: 'container',
                        anchor: '100%',
                        layout: 'hbox',
                        items: [{
                                xtype: 'container',
                                items: [
                                    combo
                                            , {
                                        xtype: 'textfield',
                                        fieldLabel: 'CC',
                                        allowBlank: true,
                                        name: 'cc',
                                        vtype: 'multiemail',
                                        anchor: '100%',
                                        maxLength: 256,
                                        width: 300
                                    }]
                            }, {
                                xtype: 'container',
                                flex: 1,
                                layout: 'anchor',
                                items: [{
                                        xtype: 'textfield',
                                        fieldLabel: 'Destinatario',
                                        allowBlank: false,
                                        name: 'destinatario',
                                        vtype: 'multiemail',
                                        anchor: '100%',
                                        maxLength: 256,
                                        value: rad[1]
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: 'CCO',
                                        allowBlank: true,
                                        name: 'cco',
                                        vtype: 'multiemail',
                                        anchor: '100%',
                                        maxLength: 256
                                    }]
                            }
                        ]},
                    {
                        xtype: 'box',
                        autoEl: {cn: 'El tama&ntilde;o m&aacute;ximo permitido para anexar archivos es de <?php echo convertToReadableSize(ini_get('upload_max_filesize')); ?>'},
                        style: 'font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 10px;font-style: normal;line-height: 25px;font-weight: bolder;color: #FFF;background-color: #069;text-indent: 5pt;text-transform: uppercase;height: 30px;text-align: right;text-align: center;',
                        height: 25
                    },
                    {
                        xtype: 'HtmlLintEditor',
                        name: 'mensaje',
                        height: 300,
                        anchor: '100%',
						value: ' '																						   
                    },
                    {
                        xtype: 'multiupload',
                        name: 'adjuntos'
                    }
                ],
                buttons: [{
                        text: 'Enviar',
                        handler: function() {
                            if (this.up('form').getForm().isValid())
                            {	var acumSize = 0;
                                var filefields = Ext.ComponentQuery.query('filefield');
                                if (filefields.length) {
                                	for (var i = 0; i < filefields.length; i++) {
                                	    var filefield = filefields[i];
                                	    acumSize = acumSize + (filefield.fileInputEl.dom.files.length > 0 ? filefield.fileInputEl.dom.files[0].size : 0);
                                	}
                                }
                                if ( acumSize >= <?php echo ini_get('upload_max_filesize'); ?> ) {
                                	Ext.MessageBox.show({  
                                        title: 'Error',  
                                        msg: 'La suma de los anexos excede el tama&ntilde;o permitido.',  
                                        buttons: Ext.MessageBox.OK,  
                                        fn: Ext.emptyFn,  
                                        icon: Ext.window.MessageBox.ERROR
                                      });
								} else {
	                                this.up('form').el.mask('Paso 1 de 2. Generando radicado...','x-mask-loading');
	                                var store = storeListaCarpeta;
	                                var selection = grid.getSelectionModel().getSelection();
	                                if (selection.length > 0) {
	                                    var jsonData = Ext.encode(Ext.Array.pluck(selection, 'data'));
	                                    this.up('form').getForm().submit({
	                                        url: './' + (config.pathMVC ? config.pathMVC : '') + 'radicado/respuestaRapida',
	                                        //waitMsg: 'Paso 1 de 2. Generando radicado...',
	                                        params: {data: jsonData},
	                                        success: function(r, action) {
	                                        	action.form.owner.el.unmask();
	                                            var msg = '';
	                                            if (action.result.msg) {
	                                                msg = action.result.msg;
	                                            } else
	                                            {
	                                                msg = action.response.responseText;
	                                            }
	                                            var msgEmail = '';
												//Notificacion sincrona
	                                            maskingAjax.request({
	                                                url: './' + (config.pathMVC ? config.pathMVC : '') + 'notificacion/enviarCorreoRespuestaRapida',
	                                                params: {
	                                                    "pathsAttachments": JSON.stringify(action.result.pathsAttachment),
	                                                    "NoRadicadoSalida": action.result.NoRadicado,
	                                                    "NoRadicadoPadre": rad[0],
	                                                    "nremitente": form.getForm().findField('nremitente').value,
	                                                    "destinatario": form.getForm().findField('destinatario').value,
	                                                    "cc": form.getForm().findField('cc').value,
	                                                    "cco": form.getForm().findField('cco').value
	                                                },
	                                                async: false,
	                                                timeout: 100,
	                                                scope: this,
	                                                success: function(response, opts) {
	                                                	var obj = Ext.decode(response.responseText);
	                                                	if (obj.success == true) {
	                                                		msgEmail = 'Correo enviado con \xE9xito.';
	                                                    } else
	                                                    {
	                                                    	msgEmail = obj.message;
	                                                    }
	                                                },
	                                                failure: function(response, opts) {
	                                                	Ext.Msg.alert('Error', response.status, Ext.emptyFn);
	                                                }
	                                            });
	
	                                            Ext.Msg.show({
	                                                title: 'Estado Respuesta R&aacute;pida',
	                                                msg: msg + '<br/>' +msgEmail,
	                                                buttons: Ext.Msg.OK,
	                                                icon: Ext.Msg.INFO,
	                                                fn: function(btn) {
	                                                    tipificar(action.result.NoRadicado);
	                                                    form.items.each(function(item, index, len) {
	                                                        if (item.id.toString().indexOf("multiupload") > -1) {
	                                                            mainformFiles = item;
	                                                            while (mainformFiles.fileslist.length > 0) {
	                                                                mainformFiles.fileslist.pop();
	                                                            }
	                                                        }
	                                                        w.destroy();
	                                                    });
	                                                },
	                                                modal: true
	                                            });
	
	                                        },
	                                        failure: function(r, action) {
	                                        	action.form.owner.el.unmask();
	                                            var msg = '';
	                                            if (action.result.msg) {
	                                                msg = action.result.msg;
	                                            } else
	                                            {
	                                                msg = action.response.responseText;
	                                            }
	
	
	                                            Ext.Msg.show({
	                                                title: 'Respuesta R&aacute;pida Falla',
	                                                msg: msg + ', Por favor vuelva a intentarlo!',
	                                                buttons: Ext.Msg.OK,
	                                                icon: Ext.Msg.ERROR,
	                                                fn: function(btn) {
	
	                                                    form.items.each(function(item, index, len) {
	                                                        if (item.id.toString().indexOf("multiupload") > -1) {
	                                                            mainformFiles = item;
	                                                            while (mainformFiles.fileslist.length > 0) {
	                                                                mainformFiles.fileslist.pop();
	                                                            }
	                                                        }
	                                                        w.destroy();
	                                                    });
	                                                },
	                                                modal: true
	                                            });
	
	                                        }
	                                    });
	                                }
								}
                            }//Fin form.isvalid()
                        }
                    }, {
                        text: 'Cancelar',
                        handler: function() {
                            form.items.each(function(item, index, len) {
                                if (item.id.toString().indexOf("multiupload") > -1) {
                                    mainformFiles = item;
                                    while (mainformFiles.fileslist.length > 0) {
                                        mainformFiles.fileslist.pop();
                                    }
                                }
                            });
                            w.destroy();
                        }
                    }]
            });

            var w = new Ext.Window({
                width: 1000,
                //height:,
                x: 200,
                y: 50,
                modal: true,
                plain: true,
                html: '',
                items: [form],
                listeners: {
                    close: function() {
                        form.items.each(function(item, index, len) {
                            if (item.id.toString().indexOf("multiupload") > -1) {
                                mainformFiles = item;
                                while (mainformFiles.fileslist.length > 0) {
                                    mainformFiles.fileslist.pop();
                                }
                            }
                        });
                        w.destroy();
                    }
                }
            });
            w.show();
        } catch (error) {

            alert("Error: " + error);
        }
    }
    
    function regresar() {
        location.reload();
}

    function tipificar(NoRadicado) {

        var left = (screen.width/2) - (750/2);
        var top = (screen.height/2) - (500/2);
        window.open("<?= $ruta_raiz ?>/radicacion/tipificar_documento.php?krd=<?= $_SESSION['krd'] ?>&nurad=" + NoRadicado + "&ind_ProcAnex=<?= $ind_ProcAnex ?>&codusua=<?= $codusua ?>&coddepe=<?= $coddepe ?>&tsub=" + 1 + "&codserie=" + 0 + "&texp=<?= $texp ?>", "Tipificacion_Documento_Anexos", "height=500,width=750,scrollbars=yes, top=" + top + ', left=' + left);

    }

    function temaTx() {
        sw = 0;
<?php
if (!$verrad) {
    ?>
            if (grid.getSelectionModel().getSelection().length == 0) {
                alert("Debe seleccionar uno o mas radicados");
                return;
            }
            ;
            var list = new Array();
            for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
                list.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);
            }
            // radicados seleccionados
            seleccionados.value = list.join(",").trim();
            //alert(document.form1.usTemaSelect.value+':'+seleccionados.value+':'+document.form1.action);
    <?php
}

// INFORMACION DE SERIES Y SUBSERIES POR RADICADO
?>
        document.form1.action = "./tx/formTema.php?<?= $encabezado ?>";
        //alert(document.form1.action);
        document.form1.submit();
    }

    function medioTx() {
        sw = 0;
<?php
if (!$verrad) {
    ?>
            if (grid.getSelectionModel().getSelection().length == 0) {
                alert("Debe seleccionar uno o mas radicados");
                return;
            }
            ;
            var list = new Array();
            for (i = 0; i < grid.getSelectionModel().getSelection().length; i++) {
                list.push(grid.getSelectionModel().getSelection()[i].data.nroradicado);
            }
            // radicados seleccionados
            seleccionados.value = list.join(",").trim();
            //alert(document.form1.usTemaSelect.value+':'+seleccionados.value+':'+document.form1.action);
    <?php
}

// INFORMACION DE SERIES Y SUBSERIES POR RADICADO
?>
        document.form1.action = "./tx/formMedio.php?<?= $encabezado ?>";
        //alert(document.form1.action);
        document.form1.submit();
    }

    function window_onload() {
        document.getElementById('depsel').style.display = 'none';
        // document.getElementById('Enviara').style.display = '';
        document.getElementById('depsel8').style.display = 'none';
        if (document.getElementById('carpper'))
        {
            document.getElementById('carpper').style.display = 'none';
        }
        document.getElementById('Enviar').style.display = 'none';
<?php
if (!$verrad) {
    
} else {
    echo 'window_onload2();' . "\n";
}
if ($carpeta == 11 and $_SESSION['codusuario'] == 1) {
    echo "document.getElementById('salida').style.display = ''; ";
    echo "document.getElementById('enviara').style.display = ''; ";
    echo "document.getElementById('Enviar').style.display = ''; ";
} else {
    echo " ";
}
if ($carpeta == 11 and $_SESSION['codusuario'] != 1) {
    echo "document.getElementById('enviara').style.display = 'none'; ";
    echo "document.getElementById('Enviar').style.display = 'none'; ";
}
?>
    }
</script>
<body onload="MM_preloadImages('<?= $ruta_raiz ?>/imagenes/internas/overVobo.gif', '<?= $ruta_raiz ?>/imagenes/internas/overNRR.gif', '<?= $ruta_raiz ?>/imagenes/internas/overMoverA.gif', '<?= $ruta_raiz ?>/imagenes/internas/overReasignar.gif', '<?= $ruta_raiz ?>/imagenes/internas/overInformar.gif', '<?= $ruta_raiz ?>/imagenes/internas/overDevolver.gif', '<?= $ruta_raiz ?>/imagenes/internas/overArchivar.gif');">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <!--DWLayoutTable-->
<?php
// Si esta en la Carpeta de Visto Bueno no muesta las opciones de reenviar
if (($mostrar_opc_envio == 0) || ($_SESSION['codusuario'] == $radi_usua_actu && $_SESSION['dependencia'] == $radi_depe_actu)) {

    $sql = "SELECT 
	PERM_ARCHI
	, PERM_VOBO
	, USUA_PERM_TEMAS
	, USUA_PERM_MEDIO
	, USUA_PERM_RESPUESTA 
	, USUA_MASIVA_TEMAS
	, USUA_MASIVA_PRESTAMO
	, USUA_MASIVA_INCLUIR
	, USUA_MASIVA_TRD
			
FROM 
	USUARIO
WHERE 
	USUA_CODI = " . $_SESSION['codusuario'] . "	 
	AND DEPE_CODI = " . $_SESSION['dependencia'];

    $rs = $db->conn->Execute($sql);

    if (!$rs->EOF) {
        $permArchi = $rs->fields["PERM_ARCHI"];
        $permVobo = $rs->fields["PERM_VOBO"];
        $permTemas = $rs->fields["USUA_PERM_TEMAS"];
        $permRecepcion = $rs->fields["USUA_PERM_MEDIO"];
        $permRespuesta = $rs->fields["USUA_PERM_RESPUESTA"];
        $accMasiva_trd = $rs->fields["USUA_MASIVA_TRD"];
        $accMasiva_incluir = $rs->fields["USUA_MASIVA_INCLUIR"];
        $accMasiva_prestamo = $rs->fields["USUA_MASIVA_PRESTAMO"];
        $accMasiva_temas = $rs->fields["USUA_MASIVA_TEMAS"];
    }
    ?>

            <tr>
            	<td>
            		<?php 
            		if (strlen($_GET['carpeta']) > 0) {
            		?>
            		<img name="principal_r4_c3" src="<?= $ruta_raiz ?>/imagenes/Convenciones.png" border="0" alt="">
            		<?php 
            		}
            		?>
				</td>
                <td colspan="2">
                    <table align="right" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="25" valign="bottom">
                                <img name="principal_r4_c3" src="<?= $ruta_raiz ?>/imagenes/internas/principal_r4_c3.gif" width="25" height="51" border="0" alt="">
                            </td>				
    <?php if (($permRespuesta == 1 && isset($verrad) && substr($verrad, -1) == "2") || ($permRespuesta == 1 && !$verrad)) { ?>
                                <td width="63" valign="bottom">
                                    <a href="#" onMouseOut="MM_swapImgRestore()" onClick="respuestaTx()" onMouseOver="MM_swapImage('Image7', '', '<?= $ruta_raiz ?>/imagenes/internas/overRespuestaRapida.gif', 1)"><img src="<?= $ruta_raiz ?>/imagenes/internas/RespuestaRapida.gif" name="Image7" width="63" height="51" border="0"></a>
                                </td>
                            <?php } ?>
                            <!-- INICIO Permisos de acciones masivas  -->
    <?php
    if ($accMasiva_trd == 1 && !$verrad) {
        echo '<td width="63" valign="bottom">
			<a href= "#" title="Asignar TRD" onmouseOver="document.ejemplo1.src=\'' . $ruta_raiz . '/imagenes/internas/masTRDO.gif\';" onClick="masivaTRD();" onmouseOut="document.ejemplo1.src=\'' . $ruta_raiz . '/imagenes/internas/masTRD.gif\';"><img name="ejemplo1"  alt="Asignar Trd masiva" src=\'' . $ruta_raiz . '/imagenes/internas/masTRD.gif\' width="63" height="51" border="0"></a>
		 </td>	
	';
    }
    if ($accMasiva_prestamo == 1 && !$verrad) {
        echo '<td width="63" valign="bottom">
			<a href= "#" title="Solicitud prestamo" onmouseOver="document.ejemplo2.src=\'' . $ruta_raiz . '/imagenes/internas/masPrestO.gif\';" onClick="masivaPrestamo();" onmouseOut="document.ejemplo2.src=\'' . $ruta_raiz . '/imagenes/internas/masPrest.gif\';"><img name="ejemplo2" src=\'' . $ruta_raiz . '/imagenes/internas/masPrest.gif\' width="63" height="51" border="0"></a>
		  </td>	
	';
    }

    if ($accMasiva_temas == 1 && !$verrad) {
        echo '<td width="63" valign="bottom">
 			<a href= "#" title="Asignar Sector y Tema" onmouseOver="document.ejemplo3.src=\'' . $ruta_raiz . '/imagenes/internas/masTemaO.gif\';" onClick="masivaTemaSector();" onmouseOut="document.ejemplo3.src=\'' . $ruta_raiz . '/imagenes/internas/masTema.gif\';"><img name="ejemplo3" src=\'' . $ruta_raiz . '/imagenes/internas/masTema.gif\' width="63" height="51" border="0"></a>
	  </td>	
	';
    }
    if ($accMasiva_incluir == 1 && !$verrad) {
        echo '<td width="63" valign="bottom">
 			<a href= "#" title="Incluir radicado en expediente" onmouseOver="document.ejemplo4.src=\'' . $ruta_raiz . '/imagenes/internas/masInclO.gif\';" onClick="masivaIncluir();" onmouseOut="document.ejemplo4.src=\'' . $ruta_raiz . '/imagenes/internas/masIncl.gif\';"><img name="ejemplo4" src=\'' . $ruta_raiz . '/imagenes/internas/masIncl.gif\' width="63" height="51" border="0"></a>
		 </td>	
	';
    }
    ?>
                            <!-- FIN Permisos de acciones masivas  -->                    
                            <?php
                            if (!$agendado) {
                                ?>                    
                                <td width="63" valign="bottom">
                                    <a href="#" onMouseOut="MM_swapImgRestore()" onClick="seleccionBarra = 10;
                changedepesel(10);" onMouseOver="MM_swapImage('Image8', '', '<?= $ruta_raiz ?>/imagenes/internas/overMoverA.gif', 1)"><img src="<?= $ruta_raiz ?>/imagenes/internas/moverA.gif" name="Image8" width="63" height="51" border="0"></a>
                                </td>
                                <td width="64" valign="bottom">
                                    <a href="#" onMouseOut="MM_swapImgRestore()" onClick="seleccionBarra = 9;
                changedepesel(9);" onMouseOver="MM_swapImage('Image9', '', '<?= $ruta_raiz ?>/imagenes/internas/overReasignar.gif', 1)"><img src="<?= $ruta_raiz ?>/imagenes/internas/reasignar.gif" name="Image9" width="64" height="51" border="0"></a>
                                </td>
                                <td width="66" valign="bottom">
                                    <a href="#" onMouseOut="MM_swapImgRestore()" onClick="seleccionBarra = 8;
                changedepesel(8);" onMouseOver="MM_swapImage('Image10', '', '<?= $ruta_raiz ?>/imagenes/internas/overInformar.gif', 1)"><img src="<?= $ruta_raiz ?>/imagenes/internas/informar.gif" name="Image10" width="66" height="51" border="0"></a>
                                </td>
                                <td width="58" valign="bottom">
                                    <a href="#" onMouseOut="MM_swapImgRestore()" onClick="seleccionBarra = 12;
                changedepesel(12);" onMouseOver="MM_swapImage('Image11', '', '<?= $ruta_raiz ?>/imagenes/internas/overDevolver.gif', 1)"><img src="<?= $ruta_raiz ?>/imagenes/internas/devolver.gif" name="Image11" width="58" height="51" border="0"></a>
                                </td>
        <?php
        if (($_SESSION['depe_codi_padre'] and $_SESSION['codusuario'] == 1) or $_SESSION['codusuario'] != 1) {
            if (!empty($permVobo) && $permVobo != 0) {
                ?>
                                        <td width="55" valign="bottom">
                                            <a href="#" onmouseout="MM_swapImgRestore()" onclick="seleccionBarra = 14;
                        vistoBueno();" onmouseover="MM_swapImage('Image12', '', '<?= $ruta_raiz ?>/imagenes/internas/overVobo.gif', 1)"><img src="<?= $ruta_raiz ?>/imagenes/internas/vobo.gif" name="Image12" width="55" height="51" border="0" /></a>
                                        </td>
                                        <?php
                                    }
                                }
                                ?>
                                    <?php
                                    if (!empty($permArchi) && $permArchi != 0) {
                                        ?>
                                    <td width="61" valign="bottom">
                                        <a href="#" onMouseOut="MM_swapImgRestore()" onClick="seleccionBarra = 13;
                    changedepesel(13);" onMouseOver="MM_swapImage('Image13', '', '<?= $ruta_raiz ?>/imagenes/internas/overArchivar.gif', 1)"><img src="<?= $ruta_raiz ?>/imagenes/internas/archivar.gif" name="Image13" width="61" height="51" border="0"></a>
                                    </td>

                                    <?php
                                }
                                ?>
                                <!--
                                                        <td width="61" valign="bottom">
                                                          <a href="#" onmouseout="MM_swapImgRestore()" onclick="seleccionBarra = 14;changedepesel(16);" onmouseover="MM_swapImage('Image14','','<?= $ruta_raiz ?>/imagenes/internas/overNRR.gif',1)">
                                                          <img src="<?= $ruta_raiz ?>/imagenes/internas/NRR.gif" name="Image14" width="61" height="51" border="0" /></a>
                                                        </td>
                                -->			
                                <?php
                            }
                            ?>

                        </tr>
                    </table>
                </td>
            </tr>
                            <?php
                        }
                        // Final de opcion de enviar para carpetas que no son 11 y 0(VoBo)
                        ?>        
        <tr height="auto">
            <td colspan="2">
                <table border="0" width="100%" align='center' class="borde_tab" bgcolor="a8bac6">
                    <tr>
        <?php if ($controlAgenda == 1 || $permRespuesta == 1) { ?>  

                            <td width="25%" class="titulos2">
    <?php
    if ($permRecepcion == 1 && $controlAgenda == 1) {
        if ($carpeta == 0) {
            $sql = "select MREC_DESC, MREC_CODI from MEDIO_RECEPCION where MREC_ESTADO = 1 order by 1";
            print "Medio de Recepci&oacute;n:";
        } else {
            $sql = "select SGD_FENV_DESCRIP, SGD_FENV_CODIGO from SGD_FENV_FRMENVIO where SGD_FENV_ESTADO = 1 order by 1";
            print "Medio de Env&iacute;o:";
        }
        $rs = $db->conn->Execute($sql);
        print $rs->GetMenu2('usMedioSelect', '', false, false, 1, " id='usMedioSelect' class='select'");
        ?>
                                </td>
                                <td width='100%' class="titulos2">
                                    <input type="button" value="CAMB MEDIO." onClick="medioTx()" name="asignamedio" align="bottom" class="botones" id="asignamedio">
                                <?php } ?>
                            </td>
                            <td width="20%" class="titulos2">
                                <?php
                                if ($permTemas == 1 && $controlAgenda == 1) {
                                    $sql = "select SGD_TEMA_NOMBRE, id from SGD_TEM_NOMBRES WHERE SGD_TEMA_ACTIVO = 'SI'";
                                    $rs = $db->conn->Execute($sql);
                                    print "Temas:";
                                    print $rs->GetMenu2('usTemaSelect', '', false, false, 1, " id='usTemaSelect' class='select'");
                                    ?>
                                </td>
                                <td width='8' class="titulos2">
                                    <input type="button" value="Asignar Tema." onClick="temaTx()" name="asignatem" align="bottom" class="botonesNew" id="asignatem">
                                </td>    <?php } ?>

                            <td width='30%'class="titulos2">

                            </td>				

<?php
}
if (($mostrar_opc_envio == 0) || ($_SESSION['codusuario'] == $radi_usua_actu && $_SESSION['dependencia'] == $radi_depe_actu)) {
    ?>
                            <!--si esta en la Carpeta de Visto Bueno no muesta las opciones de reenviar-->
                            <td align="right">
    <?php
    $row1 = array();
    // Combo en el que se muestran las dependencias, en el caso  de que el usuario escoja reasignar.
    $dependencia = $_SESSION['depecodi'];

    $dependencianomb = substr($dependencianomb, 0, 35);
    $subDependencia = $db->conn->Concat(" CAST(DEPE_CODI as VARCHAR)", "' - '", $db->conn->substr . "(depe_nomb,0,50) ");
    $whereReasignar = "";
    if ($_SESSION["codusuario"] != 1 && $_SESSION["usuario_reasignacion"] != 1) {
        $whereReasignar = " WHERE DEPENDENCIA_ESTADO=2 AND depe_codi = $dependencia";
    } else {
        if ($_SESSION["codusuario"] == 1 || $_SESSION["usuario_reasignacion"] == 1) {
            $whereReasignar = " where DEPENDENCIA_ESTADO=2 ";
        }
    }

    //echo $whereReasignar . " - reasignar<br/>";
    $sql = "select $subDependencia, depe_codi from DEPENDENCIA $whereReasignar  ORDER BY DEPE_NOMB";

    $rs = $db->conn->Execute($sql);
    print $rs->GetMenu2('depsel', $dependencia, false, true, 0, " id=depsel class=select ");

    // genera las dependencias para informar
    $row1 = array();

    $dependencianomb = substr($dependencianomb, 0, 35);
    $subDependencia = $db->conn->substr . "(depe_nomb,0,50)";
    $sql = "select $subDependencia, depe_codi from DEPENDENCIA WHERE DEPENDENCIA_ESTADO=2 ORDER BY DEPE_NOMB";
    $rs = $db->conn->Execute($sql);

    if ($rs === false) {
        echo "Error en consulta";
        $db->conn->ErrorMsg();
        exit(1);
    }

    print $rs->GetMenu2('depsel8[]', $dependencia, false, true, 5, " id='depsel8' class='select' ");
    // Aqui se muestran las carpetas Personales
    $dependencianomb = substr($dependencianomb, 0, 35);
    $datoPersonal = "(Personal)";
    $nombreCarpeta = $db->conn->Concat("' $datoPersonal'", 'nomb_carp');
    $codigoCarpetaGen = $db->conn->Concat("10000", "carp_codi");
    $codigoCarpetaPer = $db->conn->Concat("11000", "codi_carp");

    ### QUERY PARA IDENTIFICAR LOS PERMISOS DE RADICACION QUE TIENE HABILITADO EL USUARIO
    ### Y ASI SABER QUE CARPETAS SE DEBEN MOSTRAR EN EL COMBO.
    $sqlTpRad = "SELECT	USUA_PRAD_TP1,
												USUA_PRAD_TP3, 
												USUA_PRAD_TP4,
												USUA_PRAD_TP5, 
												USUA_PRAD_TP6, 
												USUA_PRAD_TP7, 
												USUA_PRAD_TP8,
												USUA_PRAD_TP9 
										FROM	USUARIO
										WHERE	USUA_CODI = $codusuario and
			                			        DEPE_CODI = $dependencia";
    $rsTpRad = $db->conn->Execute($sqlTpRad);

    $tp = "0,";

    if ($rsTpRad->fields['USUA_PRAD_TP1'] > 0) {
        $tp .= "1,";
    }
    if ($rsTpRad->fields['USUA_PRAD_TP3'] > 0) {
        $tp .= "3,";
    }
    if ($rsTpRad->fields['USUA_PRAD_TP4'] > 0) {
        $tp .= "4,";
    }
    if ($rsTpRad->fields['USUA_PRAD_TP5'] > 0) {
        $tp .= "5,";
    }
    if ($rsTpRad->fields['USUA_PRAD_TP6'] > 0) {
        $tp .= "6,";
    }
    if ($rsTpRad->fields['USUA_PRAD_TP7'] > 0) {
        $tp .= "7,";
    }
    if ($rsTpRad->fields['USUA_PRAD_TP8'] > 0) {
        $tp .= "8,";
    }
    if ($rsTpRad->fields['USUA_PRAD_TP9'] > 0) {
        $tp .= "9";
    }

    if (substr($tp, -1) == ",")
        $tp = substr($tp, 0, strlen($tp) - 1);

    $where = "CARP_CODI IN (" . $tp . ")";

    $sql = "SELECT	CARP_DESC AS NOMB_CARP,
											$codigoCarpetaGen AS CARP_CODI,
											0 AS ORDEN
									FROM	CARPETA
									WHERE	$where
									UNION
									SELECT	$nombreCarpeta AS NOMB_CARP,
											$codigoCarpetaPer AS CARP_CODI,
											1 AS ORDEN
									FROM	CARPETA_PER
									WHERE	USUA_CODI = $codusuario AND
											DEPE_CODI = $dependencia
									ORDER BY ORDEN, CARP_CODI";
    $rs = $db->conn->Execute($sql);
    print $rs->GetMenu2('carpSel', 1, false, false, 0, " id=carpper class=select ");
    ?>
                            </td>
                            <td width='5%' align="right">
                                <input type="hidden" name="enviara" value="9"><input type="hidden" name="EnviaraV" id="EnviaraV" value=''>
                                <input type="hidden" name="codTx" value="9">
                                <input type="button" value='>>' name=Enviar id=Enviar valign='middle' class='botones_2' onClick="envioTx();">
                            </td>
                                <?php
                                // Fin no mostrar opc_envio
                            }
                            ?>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <!-- <div id="divEditor">
    	<div id="divEditorheader">
    		<table style="width: 100%; background-color: #006699;">
        		<tr>
        			<td style="text-align: left; width: 5%;">
        				<img src="../img/icons/cancel16.png" onclick="cancelar();" style="cursor: pointer;">
        			</td>
        			<td style="text-align: center; width: 95%;" colspan="4">
        				<label id="lbTitulo" style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 12px;font-style: normal;font-weight: bolder;color: #FFF;background-color: #069;text-transform: uppercase;text-align: center; ">RESPUESTA R&Aacute;PIDA PARA EL RADICADO No. <?php print $verrad;?> </label>
        			</td>
        		</tr>
    		</table>
		</div>
    	<table id="tbrepuesta" style="width: 100%;">
    		<tr>
    			<td style="background-color: #E0E6E7; " colspan="4">
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 9px;font-style: normal;line-height: 15px;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">Para enviar a m&uacute;ltiples correos Separe con ";" </label>
    			</td>
    		</tr>
    		<tr style="width: 100%">
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 9px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">Remitente:</label>
    			</td>
    			<td>
    				<select id="nremitente" name="nremitente" onchange="plantilla(this.value, this);" style="width: 200px;font-size: 10px;">
    					<?php 
    					foreach ($emailRemitente as $clave=>$valor) {
    					    echo "<option value=$clave>$valor</option>";
    					} ?>
    				</select>
    			</td>
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 9px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">Destinatario:</label>
    			</td>
    			<td style="width: 300px;">
    				<input type="text" id="destinatario" name="destinatario" maxlength="256" style="width: 500px;font-size: 10px;"/>
    			</td>
    		</tr>
    		<tr>
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 9px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">CC:</label>
    			</td>
    			<td>
    				<input type="text" id="cc" name="cc" maxlength="256" style="width: 200px;font-size: 10px;"/>
    			</td>
    			<td>
    				<label style="font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 9px;font-style: normal;font-weight: bolder;color: #000;background-color: #E0E6E7;text-indent: 5pt;text-transform: uppercase;text-align: left; ">CCO:</label>
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
        <div class="editor">
		
		</div>
        <table id="tbrepuesta" style="width: 100%">
        	<tr>
        		<td>
                    <input id="myfiles" name="files[]" type="file" class="multi" accept="tif|odt|pdf|PDF|doc|docx|tiff|avi|jpg|txt|gif|png|csv|xls|xlsx|eml|ppt|pptx|zip|msg|html|htm|rtf" maxlength="3" />
                     <div id="myfiles-list"></div>
				</td>
        		<td style="text-align: right;">
        			<input type="button" value="Enviar" style="font-size: 12px;" onclick="enviarRespuesta();" />&nbsp;&nbsp;
        			<input type="button" value="Cancelar" style="font-size: 12px;" onclick="cancelar();" />
        		</td>
        	</tr>
        </table>
        <div class="popup-wrapper">
            <div class="popup">
                <div class="popup-content">
                	<img src="../imagenes/loading.gif" />
                    <h3>POR FAVOR ESPERE...</h3>
                    <p>&#161; Se est&aacute; generando la respuesta r&aacute;pida &#33;</p>
                </div>
            </div>
        </div>
    </div>  -->

	<script>
    //Make the DIV element draggagle:
    //dragElement(document.getElementById("divEditor"));
    
    function dragElement(elmnt) {
      var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
      if (document.getElementById(elmnt.id + "header")) {
        /* if present, the header is where you move the DIV from:*/
        document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
      } else {
        /* otherwise, move the DIV from anywhere inside the DIV:*/
        elmnt.onmousedown = dragMouseDown;
      }
    
      function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
      }
    
      function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
      }
    
      function closeDragElement() {
        /* stop moving when mouse button is released:*/
        document.onmouseup = null;
        document.onmousemove = null;
      }
    }
</script>