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
    <!-- <script type="text/javascript" src="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/ext-4.2.1/ux/Ext.ux.form.HtmlLintEditor.js"></script>  
    <script type="text/javascript" src="<?php echo SERVIDOR . "/" . PATHMVC ?>views/../public/js/libs/ext-4.2.1/ux/Ext.ux.form.Multiupload.js"></script>--> 
    
	
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
            if (grid.getSelectionModel().getSelection()[0].data.nroradicado.toString().length == 14) {
                if (grid.getSelectionModel().getSelection()[0].data.nroradicado.toString().substring(13, 14) != "2")
                {
                    alert("Solo puede dar respuesta a Radicados de entrada!");
                    return;
                }
            }
            if (grid.getSelectionModel().getSelection()[0].data.nroradicado.toString().length == 15) {
                if (grid.getSelectionModel().getSelection()[0].data.nroradicado.toString().substring(14, 15) != "2")
                {
                    alert("Solo puede dar respuesta a Radicados de entrada!");
                    return;
                }
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
		    		
            OpenWindowWithPost("../lib/ckeditor/sample/index.php?<?= session_name() ?>=<?= session_id() ?>&krd=<?=$krd?>&verrad=<?=$verrad?>", 
            "width=920,height=650,left=100,top=100,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes", 
            "Respuesta", rad);
            
        } catch (error) {
                alert('Hay un error al generar la respuesta. ' + error);
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
    
    function sleep(milliseconds) {
  		var start = new Date().getTime();
    	for (var i = 0; i < 1e7; i++) {
    		if ((new Date().getTime() - start) > milliseconds) {
    	   		break;
    	  	}
    	}
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
    	if (document.getElementById('depsel')) {
        	document.getElementById('depsel').style.display = 'none';
        }
        // document.getElementById('Enviara').style.display = '';
        if (document.getElementById('depsel8')) {
        	document.getElementById('depsel8').style.display = 'none';
        }
        if (document.getElementById('carpper'))
        {
            document.getElementById('carpper').style.display = 'none';
        }
        if (document.getElementById('carpper')) {
        	document.getElementById('Enviar').style.display = 'none';
        }
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
         
        <div class="popup-wrapper">
            <div class="popup">
                <div class="popup-content">
                	<img src="../imagenes/loading.gif" />
                    <h3>POR FAVOR ESPERE...</h3>
                    <p>&#161; Se est&aacute; generando la respuesta r&aacute;pida &#33;</p>
                </div>
            </div>
        </div>