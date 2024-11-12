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
$ADODB_COUNTRECS = false;
if ($_SESSION['usua_admin_sistema'] != 1)	die(include "$ruta_raiz/sinacceso.php");

include("$ruta_raiz/config.php"); 			// incluir configuracion.
include 'adodb/adodb.inc.php';
$error = 0;
$dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
$conn = NewADOConnection($dsn);
$msg = "";

if ($conn)
{	
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
	//Botones Pestaña Básicos.
	if (isset($_POST['btn_accion']))
	{	$record = array();
		$record['SGD_CAU_CODIGO'] = $_POST['idSector'];
		$record['SGD_DCAU_CODIGO'] = $_POST['txtIdTema'];
		$txtModelo = $_POST['txtModelo'];
		$record['SGD_DCAU_DESCRIP'] = $txtModelo;
		$record['SGD_DCAU_ESTADO'] = $_POST['Slc_destado'];
		$record['SGD_DCAU_PQR'] = $_POST['Slc_hPQR'];
		$txtPqrLabel = $_POST['txtPqrLabel'];
		//$txtPqrDistri = iconv("iso-8859-1", "utf-8", $Rs_tema->fields['SGD_DCAU_DISTRIBUCION']);
		$record['SGD_DCAU_PQRDESC'] = $txtPqrLabel;
		switch($_POST['btn_accion'])
		{	Case 'Agregar': {
				$res = $conn->AutoExecute('SGD_DCAU_CAUSAL',$record, 'INSERT', false, false, true);
				($res) ? ($res == 1 ? $error = 3 : $error = 4 ) : $error = 2;
			}break;
			Case 'Modificar': {
					$conn->StartTrans();
					if ($_POST['Slc_destado'] == '0') {
						$_POST['Slc_hPQR'] = '0';
					}
					if ($_POST['Slc_hPQR'] == '0'){
						$sql = "DELETE FROM SGD_PQR_TEMAUSU WHERE SGD_DCAU_CODIGO=".$_POST['idTema'];
						$ok1 = $conn->Execute($sql);
						$sql = "DELETE FROM SGD_TEMAS_TIPOSDOC WHERE SGD_DCAU_CODIGO=".$_POST['idTema'];
						$ok2 = $conn->Execute($sql);
						$record['SGD_DCAU_PQR'] = 0;
						$record['SGD_DCAU_PQRDESC'] = "";
					}
					$res = $conn->AutoExecute('SGD_DCAU_CAUSAL',$record, 'UPDATE', "SGD_CAU_CODIGO=".$_POST['idSector']." AND SGD_DCAU_CODIGO=".$_POST['idTema'], false, true);
					if ($conn->CompleteTrans()) {
						$msg = "Tema modificado exitosamente.";
					}
				}break;
			Case 'Eliminar': {
					$ADODB_COUNTRECS = true;
					$sql = "SELECT RADI_NUME_RADI FROM SGD_CAUX_CAUSALES WHERE SGD_DCAU_CODIGO = ".$_POST['idTema'];
					$rs = $conn->Execute($sql);
					$ADODB_COUNTRECS = false;
					if ($rs->RecordCount() > 0) {
						//Existe dependencia.. mejor se inhabilita
						$msg = "Existen dependencias ligadas a el tema seleccionado. Se recomienda inactivarlo.";
					} else {
						$conn->StartTrans();
						$ok = $conn->Execute("DELETE FROM SGD_DCAU_CAUSAL WHERE SGD_DCAU_CODIGO = ".$_POST['idTema']);
						if ($conn->CompleteTrans()) {
							$msg = "Tema eliminado exitosamente.";
						}
					}
				}break;
			Default: break;
		}
		unset($record);
	}
	
	//Boton Agregar Tipo Documental
	if ( isset($_POST['btn_addTdoc2']) && ($_POST['btn_addTdoc2'] == 1) ) {
		$tabla = "SGD_TEMAS_TIPOSDOC";
		$record['SGD_DCAU_CODIGO'] = $_POST['idTema'];	
		$record['SGD_TPR_CODIGO'] = $_POST['idTdoc'];	
		$record['SGD_TEMTDOC_ESTADO'] = 1;
		$ok_TemTdoc = $conn->Replace($tabla, $record, array('SGD_DCAU_CODIGO','SGD_TPR_CODIGO'), $autoQuote=false);
		$ok_UdtTdoc = $conn->Replace("SGD_TPR_TPDCUMENTO", array("SGD_PQR_LABEL"=>$_POST['txtEtiquetaTdoc2'],"SGD_PQR_DESCRIP"=>$_POST['txtDescrTdoc2'], "SGD_TPR_CODIGO"=>$_POST['idTdoc']), array('SGD_TPR_CODIGO'), $autoQuote=true);
		if ($ok_TemTdoc && $ok_UdtTdoc) $msg = "Tipo Documental actualizado exitosamente.";
	}
	
	//Boton Eliminar Tipo Documental
	if ( isset($_POST['btn_delTdoc']) && (is_array($_POST['idTdocTema'])) ) {
		$tabla = "SGD_TEMAS_TIPOSDOC";
		foreach ($_POST['idTdocTema'] as $key => $value) {
			$conn->StartTrans();
			$record['SGD_DCAU_CODIGO'] = $_POST['idTema'];
			$record['SGD_TPR_CODIGO'] = $value;
			$record['SGD_TEMTDOC_ESTADO'] = 0;
			$conn->Replace($tabla, $record, array('SGD_DCAU_CODIGO','SGD_TPR_CODIGO'), $autoQuote=false);
			if ($conn->CompleteTrans()) {
				$msg = "Tipo Documental desasociado exitosamente.";
			}
		}
	}
	
	//Boton Actualizar Usuario
	/*if ( isset($_POST['btn_udtUsr'])){
		if ( ($_POST['idDepe'] != '') && ($_POST['idUser'] != '') ) {
		    
		    $sql = "DELETE FROM SGD_PQR_TEMAUSU WHERE SGD_DCAU_CODIGO = ".$_POST['idTema'];
		    if ($conn->Execute($sql)) {
		    
		        if (is_array($_POST['idUser'])) {
		            foreach ($_POST['idUser'] as &$valor) {
            			$tabla = "SGD_PQR_TEMAUSU";
            			$record['SGD_DCAU_CODIGO'] = $_POST['idTema'];
            			$record['DEPE_CODI'] = $_POST['idDepe'][0];
            			$record['USUA_CODI'] = $valor;
            			$ok_Tdoc = $conn->Replace($tabla, $record, array('SGD_PQR_CODIGO'), $autoQuote=false);
		            }
		        }
		        
    			if ($ok_Tdoc) 
    			    $msg = "Usuarios PQR asignados exitosamente.";
		    }
		} else if ( ($_POST['idDepe'] != '') && ($_POST['idUser'] == '') ) {
		    
		    $sql = "DELETE FROM SGD_PQR_TEMAUSU WHERE SGD_DCAU_CODIGO=".$_POST['idTema']." AND DEPE_CODI = " . $_POST['idDepe'][0];
			if ($conn->Execute($sql))	$msg = "Usuario PQR Desasignado exitosamente.";
		}
	}*/
}
else
{	$error = 1;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Orfeo- Admor de Temas.</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta charset="utf-8">
	<link href="<?php echo $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?php echo $ruta_raiz ?>/pqr2/js/themes/base/jquery.ui.all.css">
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/jquery-1.7.1.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.core.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.widget.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.mouse.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.button.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.draggable.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.tabs.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.position.js"></script>
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/ui/jquery.ui.dialog.js"></script>	
	<script src="<?php echo $ruta_raiz ?>/pqr2/js/jquery-1.12.4.js"></script>
  	<script src="<?php echo $ruta_raiz ?>/pqr2/js/jquery-ui.js"></script>
	<script>
    	$(function () {
        	
    	    $('.droptrue').on('click', 'li', function () {
    	        $(this).toggleClass('selected');
    	    });
    
    	    $("ul.droptrue").sortable({
    	        connectWith: 'ul.droptrue',
    	        opacity: 0.6,
    	        revert: true,
    	        helper: function (e, item) {
    	            console.log('parent-helper');
    	            console.log(item);
    	            if(!item.hasClass('selected'))
    	               item.addClass('selected');
    	            var elements = $('.selected').not('.ui-sortable-placeholder').clone();
    	            var helper = $('<ul/>');
    	            item.siblings('.selected').addClass('hidden');
    	            return helper.append(elements);
    	        },
    	        start: function (e, ui) {
    	            var elements = ui.item.siblings('.selected.hidden').not('.ui-sortable-placeholder');
    	            ui.item.data('items', elements);
    	        },
    	        receive: function (e, ui) {
    	            ui.item.before(ui.item.data('items'));
    	        },
    	        stop: function (e, ui) {
    	            ui.item.siblings('.selected').removeClass('hidden');
    	            $('.selected').removeClass('selected');
    	        },
    	        update: updatePostOrder
    	    });
    
    	    $("#sortable1, #sortable2").disableSelection();
    	    $("#sortable1, #sortable2").css('minHeight', $("#sortable1").height() + "px");
    	    updatePostOrder();
    	});
    
    	function updatePostOrder() {
    		debugger;
    	    var arr = [];
    	    $("#sortable2 li").each(function () {
    	        arr.push($(this).attr('id'));
    	    });
    	    $('#postOrder').val(arr.join(','));
    	}

        /*$( function() {
            $( "ul.droptrue" ).sortable({
              connectWith: "ul"
            });
         
            $( "ul.dropfalse" ).sortable({
              connectWith: "ul"
            });
         
            //$( "#sortable2" ).disableSelection();
          } );*/
    </script>
	
	<script type="text/javascript">
	
	$(document).ready(function() {

		$("#tabs").tabs();

		if ($("#Slc_hPQR").val() != 1) {
			$("#tabs").tabs({ disabled: [1] });
			$("#txtPqrLabel").attr("readonly", true);
			$("#txtPqrLabel").val('');
		}

		$('#Slc_hPQR').change(function()
			{
				if ($(this).val() == 1) {
					//$( "#tabs" ).tabs('enable', 1);
					$( "#txtPqrLabel" ).attr("readonly", false);
					$( "#txtPqrLabel" ).focus(); 
				} else {
					//$( "#tabs" ).tabs({ disabled: [1] });
					$( "#txtPqrLabel" ).attr("readonly", true);
					$( "#txtPqrLabel" ).val(''); 
				}
			});

		$("#btn_AddAction, #btn_UdtAction").click(function ()
			{	
				debugger;
				var msg = 'Los siguientes datos son requeridos:\n';
				var todoOK = true;
				if (($.trim($('#txtIdTema').val()) == '')){
					todoOK = false;
					msg = msg + "Campo c\u00FEdigo.\n";
				} else {
					if (!$.isNumeric($('#txtIdTema').val())) {
						todoOK = false;
						msg = msg + "Campo c\u00F3digo debe ser num\u00E9rico.\n";
					}
				}

				if (($.trim($('#txtModelo').val()) == '')){
					todoOK = false;
					msg = msg + "Campo Nombre.\n";
				}
				
				if ($('#Slc_destado').val() == '') {
					todoOK = false;
					msg = msg + "Campo Estado.\n";
				}

				if ($('#Slc_hPQR').val() == '') {
					todoOK = false;
					msg = msg + "Campo Habilitado para PQR.\n";
				}
				
				if ( ($('#Slc_hPQR').val() == 1) && ($.trim($('#txtPqrLabel').val()) == '') ) {
					todoOK = false;
					msg = msg + "Campo Etiqueta.\n";
				}
				
				if (!todoOK) alert(msg);
				return todoOK;
			}); 
		
		$("#btn_DelAction").click(function ()
			{	var msg = 'Los siguientes datos son requeridos:\n';
				var todoOK = true;
				if (($('#idTema').val() == '')) {
					todoOK = false;
					msg = msg + "No ha seleccionado el Tema.\n";
				}
				if (!todoOK) alert(msg);
				return todoOK;
			});

		$("#btn_addTdoc").click(function ()
			{	var msg = 'Los siguientes datos son requeridos:\n';
				var todoOK = true;
				if ($('#idTdoc').val() == '') {
					todoOK = false;
					msg = msg + "No ha seleccionado Tipo Documental.\n";
					alert(msg);
					return todoOK;
					
				} else {
					$.get("../../pqr2/scripts/cargar-info-PQR-tdoc.php?",{idTdoc:$("#idTdoc").val()}, verDatosTdoc);
					$('#dialog_form').dialog('open');
					return false;
				}
			});

		$("#btn_delTdoc").click(function ()
				{	var msg = 'Los siguientes datos son requeridos:\n';
					var todoOK = true;
					if ($('#idTdocTema option:selected').length == 0) {
						todoOK = false;
						msg = msg + "No ha seleccionado Tipo Documental a Eliminar.\n";
					}
					if (!todoOK) alert(msg);
					return todoOK;
				});
		
		$("#idDepe").change(function(){cargar_usuarios();});

		$("#Slc_destado").change(function(){

			});

		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
		
		var txtEtiquetaTdoc = $( "#txtEtiquetaTdoc" ),
			txtDescrTdoc = $( "#txtDescrTdoc" ),
			allFields = $( [] ).add( txtEtiquetaTdoc ).add( txtDescrTdoc ),
			tips = $( ".validateTips" );

		function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}
		
		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				updateTips( "Longitud de " + n + " debe estar entre " + min + " y " + max + " caracteres." );
				return false;
			} else {
				return true;
			}
		}
		
		$( "#dialog_form" ).dialog({
			draggable: true,
			autoOpen: false,
			height: 240,
			width: 650,
			position: 'bottom',
			modal: true,
			buttons: {
				"Actualizar": function() {
					var bValid = true;
					allFields.removeClass( "ui-state-error" );

					bValid = bValid && checkLength( txtEtiquetaTdoc, "Etiqueta" , 3, 50 );
					//bValid = bValid && checkLength( txtDescrTdoc, "Descripcion", 3, 450 );

					if ( bValid ) {
						$("#txtEtiquetaTdoc2").val($( "#txtEtiquetaTdoc" ).val());
						$("#txtDescrTdoc2").val($( "#txtDescrTdoc" ).val());
						$("#btn_addTdoc2").val(1);
						$( this ).dialog( "close" );
						$("#formSeleccion").submit();
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFields.removeClass( "ui-state-error" );
			}
		});
	});

	function verDatosTdoc(datos){
		var idx = datos.search('cP@r@d0R');
		if (idx ==  0) {
			$("#txtEtiquetaTdoc").attr("value", '');
			$("#txtDescrTdoc").attr("value", '');
		} else {
			$("#txtEtiquetaTdoc").attr("value", datos.substr(0,idx));
			$("#txtDescrTdoc").attr("value", datos.substr(idx+8));
		}
			
	}
	
	function cargar_usuarios() {
		var code = $("#idDepe").val();
		var tema = $("#idTema").val();
		if (code == 0)
			$("#idUser").children().remove();
		else {
			$.get("../../pqr2/scripts/cargar-usuarios.php?", { code: code, tema: tema, tipo: '1' }, function(resultado) {
				if(resultado == false) {
					alert("Error al cargar los usuarios, por favor comuníquese con el administrador del sistema");
				} else {				
					$("#sortable1").empty();
					$("#sortable1").append(resultado);	
				}
			});
		}
	}

	function guardarUsuarios() {
		var code = $("#postOrder").val();
		var tema = $("#idTema").val();
		var cant = $("#txtCantidad").val();
		$.get("../../pqr2/scripts/cargar-usuarios.php?", { code: code, tema: tema, tipo: '2', cantidad: cant }, function(resultado) {
			debugger;
			if(resultado == false) {
				alert("Error al guardar los usuarios, por favor comuníquese con el administrador del sistema");
			} else {				
				alert("Usuarios guardados !!");
			}
		});
	}
	
	function cargarUsuariosParam(usuarios) {
    	debugger;
    	var vars = usuarios.split("$");
    	var datos = "";
    	var i;
    	for (i = 0; i < vars.length; i++) {
    		datos = vars[i].split("-");
    		$("#sortable2").append("<li id='" + datos[0] + "-" + datos[1] + "' >" + datos[2] + "</li>");
    	}
    }
    
	function ver_listado1() {
		var tema = $("#idTema").val();
        window.open('listados.php?var=usuaPqr&tema='+tema,'_blank','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
    }

	function ver_listado() {
        window.open('listados.php?var=tmas', '_blank','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
    }
	</script>
	<style>
		body { font-size: 62.5%; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.validateTips { border: 1px solid transparent; padding: 0.3em; }
		        
        .listBlock {
            float: left;
        }
        #sortable1, #sortable2 {
            list-style-type: none;
            margin: 0;
            padding: 0;
            margin-right: 100px;
            background: #eee;
            padding: 5px;
            width: 300px;
            border: 1px solid black;
        }
        #sortable1 li, #sortable2 li {
            color:black;
            cursor: move;
            margin: 5px;
            padding: 5px;
            font-size: 1.2em;
            width: 250px;
            background: none;
            background-color: white;
        }
        .selected {
            background:red !important;
        }
        .hidden {
            display:none !important;
        }
        ul {
            list-style-type: none;
        }
	</style>
</head>
<body>

<?php 

//Gestionamos lista de Sectores
$sql_cont = "SELECT SGD_CAU_DESCRIP, SGD_CAU_CODIGO FROM SGD_CAU_CAUSAL WHERE SGD_CAU_ESTADO = 1 ORDER BY SGD_CAU_DESCRIP";
$Rs_sector = $conn->Execute($sql_cont);
$slc_sector = $Rs_sector->GetMenu2('idSector', $_POST['idSector'], ":&lt;&lt; SELECCIONE &gt;&gt;",false,false, " id='idSector' class='select'  onChange='this.form.submit();' ");
if (!($Rs_sector)) $error = 2;

//Gestionamos lista de Temas
if (isset($_POST['idSector']) and $_POST['idSector'] <> '') {
    $sql_tema = "SELECT SGD_DCAU_DESCRIP, SGD_DCAU_CODIGO FROM SGD_DCAU_CAUSAL WHERE SGD_CAU_CODIGO=".$_POST['idSector']." ORDER BY SGD_DCAU_DESCRIP";
    $Rs_tema = $conn->Execute($sql_tema);
    $slc_tema =  $Rs_tema->GetMenu2('idTema', $_POST['idTema'], ":&lt;&lt; SELECCIONE TEMA &gt;&gt;",false,false, " id='idTema' class='select' onChange='this.form.submit();' ");
    if (!($Rs_tema)) $error = 2;
} else {
    $slc_tema = "<select name='idTema' id='idTema' class='select'><option value=''>&lt;&lt; SELECCIONE SECTOR &gt;&gt;</option></select>";
}

//Si ha seleccionado un Sector y un Tema.. Traemos los datos de dicho Tema.
if ( (isset($_POST['idSector'])) && ($_POST['idSector'] != '') && (isset($_POST['idTema'])) && ($_POST['idTema'] != '') ) {
    
    $sql_tema = "SELECT SGD_DCAU_DESCRIP, SGD_DCAU_ESTADO, SGD_DCAU_PQR, SGD_DCAU_PQRDESC, SGD_DCAU_DISTRIBUCION FROM SGD_DCAU_CAUSAL WHERE SGD_DCAU_CODIGO=".$_POST['idTema']." ";
    $Rs_tema = $conn->Execute($sql_tema);
    $txtIdTema = $_POST['idTema'];
    $txtModelo = $Rs_tema->fields['SGD_DCAU_DESCRIP'];
    if ($Rs_tema->fields['SGD_DCAU_ESTADO'] == 0) {
        $offE = " selected ";
        $onE = "";
    } else {
        $offE = "";
        $onE = " selected ";
    }
    if ($Rs_tema->fields['SGD_DCAU_PQR'] == 0) {
        $offP = " selected ";
        $onP = "";
    } else {
        $offP = "";
        $onP = " selected ";
    }
    
    $txtPqrLabel = $Rs_tema->fields['SGD_DCAU_PQRDESC'];
    $txtPqrDistri = $Rs_tema->fields['SGD_DCAU_DISTRIBUCION'];
    
    $deps = array();
    $usus = array();
    $depusus = "";
    $sql_tusu = "SELECT T.USUA_CODI, T.DEPE_CODI, U.USUA_LOGIN, U.USUA_NOMB
                FROM SGD_PQR_TEMAUSU T INNER JOIN USUARIO U ON T.DEPE_CODI = U.DEPE_CODI AND T.USUA_CODI = U.USUA_CODI 
                WHERE SGD_DCAU_CODIGO = ".$_POST['idTema']." ";
    $Rs_tusu = $conn->Execute($sql_tusu);
    while ( !$Rs_tusu->EOF) {
        
        if (strlen($depusus) > 1) {
            $depusus .= "$";
        }
            
        $depusus .= $Rs_tusu->fields['DEPE_CODI']."-".$Rs_tusu->fields['USUA_CODI']."-".$Rs_tusu->fields['USUA_NOMB'];
        $deps[] = $Rs_tusu->fields['DEPE_CODI'];
        $usus[] = $Rs_tusu->fields['USUA_CODI'];
        
        $Rs_tusu->MoveNext();
    }
    
    if (count($deps) == 0 && count($usus) == 0) {
        $deps[] = 600;
        $usus[] = 0;
    }
        
    $sql_depe = "SELECT DEPE_NOMB, DEPE_CODI FROM DEPENDENCIA WHERE DEPENDENCIA_ESTADO=2 ORDER BY DEPE_NOMB";
    $Rs_depe = $conn->Execute($sql_depe);
    //$slc_depe = $Rs_depe->GetMenu2('idDepe', $deps[0], "", false, false, " id='idDepe' class='select' ");
    
    $slc_depe = "<select id='idDepe' name='idDepe' class='select'>";
    //$slc_depe .= "<option value='0'>-- Seleccione --</option>";
    $selec = "";
    if ($Rs_depe && !$Rs_depe->EOF) {
        while (!$Rs_depe->EOF)
        {
            if ($_POST['idTdoc'] == $Rs_depe->fields['SGD_TPR_CODIGO']) {
                $selec = 'selected';
            }
            $slc_depe .= "<option value='".$Rs_depe->fields['DEPE_CODI']."' $selec>".iconv("iso-8859-1", "utf-8", $Rs_depe->fields['DEPE_NOMB'])."</option>";
            $selec = "";
            $Rs_depe->MoveNext();
        }
    }
    $slc_depe .= "</select>";
    
    //$sql_user = "SELECT USUA_NOMB, USUA_CODI FROM USUARIO WHERE DEPE_CODI=$deps[0] AND USUA_ESTA=1 ORDER BY USUA_NOMB";
    //$Rs_user = $conn->Execute($sql_user);
    //$slc_user = $Rs_user->GetMenu2('idUser', $usus, "",true,'10px', " id='idUser' class='select' ");
    
    $sql_tdoc = "SELECT TD.SGD_TPR_DESCRIP, TD.SGD_TPR_CODIGO FROM SGD_TPR_TPDCUMENTO TD WHERE TD.SGD_TPR_CODIGO IN (SELECT TX.SGD_TPR_CODIGO FROM SGD_TEMAS_TIPOSDOC TX WHERE SGD_DCAU_CODIGO=".$_POST['idTema']." AND SGD_TEMTDOC_ESTADO=1) ";
    $Rs_tdoc = $conn->Execute($sql_tdoc);
    //$slc_tdocTema = $Rs_tdoc->GetMenu('idTdocTema[]', false, false, true, 5, " id='idTdocTema' class='select' ");
    $slc_tdocTema = "<select id='idTdocTema' name='idTdocTema' multiple class='select'>";
    if ($Rs_tdoc && !$Rs_tdoc->EOF) {
        while (!$Rs_tdoc->EOF)
        {
            $slc_tdocTema .= "<option value='".$Rs_tdoc->fields['SGD_TPR_CODIGO']."' $selec>".iconv("iso-8859-1", "utf-8", $Rs_tdoc->fields['SGD_TPR_DESCRIP'])."</option>";
            $Rs_tdoc->MoveNext();
        }
    }
    $slc_tdocTema .= "</select>";
    
    
    //$sql_tdoc = "SELECT SGD_TPR_DESCRIP, SGD_TPR_CODIGO FROM SGD_TPR_TPDCUMENTO WHERE SGD_TPR_ESTADO = 1 ORDER BY SGD_TPR_DESCRIP";
    $sql_tdoc = "SELECT SGD_TPR_DESCRIP, SGD_TPR_CODIGO FROM SGD_TPR_TPDCUMENTO ORDER BY SGD_TPR_DESCRIP";
    $Rs_tdoc = $conn->Execute($sql_tdoc);
    
    $slc_tdoc = "<select id='idTdoc' name='idTdoc' class='select'>";
    $slc_tdoc .= "<option value='0'>-- Seleccione --</option>";
    $selec = "";
    if ($Rs_tdoc && !$Rs_tdoc->EOF) {
        while (!$Rs_tdoc->EOF)
        {
            if ($_POST['idTdoc'] == $Rs_tdoc->fields['SGD_TPR_CODIGO']) {
                $selec = 'selected';
            }
            $slc_tdoc .= "<option value='".$Rs_tdoc->fields['SGD_TPR_CODIGO']."' $selec>".iconv("iso-8859-1", "utf-8", $Rs_tdoc->fields['SGD_TPR_DESCRIP'])."</option>";
            $selec = "";
            $Rs_tdoc->MoveNext();
        }
    }
    $slc_tdoc .= "</select>";
    //$slc_tdoc = $Rs_tdoc->GetMenu2('idTdoc', $_POST['idTdoc'], ":&lt;&lt; SELECCIONE &gt;&gt;",false,false, " id='idTdoc' class='select' ");
    
} else {
    $idSector = "";
    $idTdoc = "";
    $idDepe = "";
    $idUser = "";
    $idTema = "";
    $txtModelo = "";
    $offE = "";
    $onE = "";
    $onP = "";
    $offP = "";
    $slc_depe="<select name='idDepe' id='idDepe' class='select'><option value=''>&lt;&lt; SELECCIONE TEMA &gt;&gt;</option></select>";
    $slc_user="<select name='idUser' id='idUser' class='select'><option value=''>&lt;&lt; SELECCIONE TEMA &gt;&gt;</option></select>";
    $txtPqrLabel = "";
    $slc_tdocTema = "";
    $slc_tdoc = "<select name='idTdoc' id='idTdoc' class='select'><option value=''>&lt;&lt; SELECCIONE TEMA &gt;&gt;</option></select>";
}

?>

<form name="formSeleccion" id="formSeleccion" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
<table width="90%" border="1" align="center" class="t_bordeGris">
<tr>
	<td colspan="2" height="40" align="center" class="titulos4"><b>ADMINISTRADOR DE TEMAS</b></td>
</tr>
<tr class=timparr>
	<td width="15%" align="left" class="titulos2"><b>&nbsp;Seleccione Sector</b></td>
	<td width="85%" class="listado2">
	<?php
		echo $slc_sector;
	?>
	</td>
</tr>
<tr class=timparr>
	<td width="25%" align="left" class="titulos2"><b>&nbsp;Seleccione Tema</b></td>
	<td width="75%" class="listado2">
	<?php
		echo $slc_tema;
	?>
	</td>
</tr>
</table>
<div id="dialog_form" title="Actualizar datos en formulario PQR">
	<p class="validateTips">Ambos campos son obligatiorios.</p>
	<table width="100%" border="1" align="center" class="t_bordeGris">
		<tr class=timparr>
			<td width="15%" align="left" class="titulos2"><b>&nbsp;Digite Etiqueta.</b></td>
			<td class="listado2">
				<input type="text" name="txtEtiquetaTdoc" id="txtEtiquetaTdoc" size="40" maxlength="50" value="" />
			</td>
		</tr>
		<tr>
			<td align="left" class="titulos2"><b>&nbsp;Digite Descripci&oacute;n.</b></td>
			<td class="listado2">
				<input type="text" name="txtDescrTdoc" id="txtDescrTdoc" size="100" maxlength="450" value=" " />
			</td>
		</tr>
	</table>
</div>
<div class="demo" style="margin-right:auto; margin-left:auto; width: 90 %;">
<div id="tabs">
	<ul style="background-image: url('../../pqr2/js/themes/base/images/ui-bg_highlight-soft_75_eeeeee_1x100.png'); background-repeat: repeat-x; background-color: #EEEEEE;">
		<li><a href="#tabs-1"><font style="font-family: 'verdana'; font-size: 10px; font-style: normal; font-weight:bold;">B&aacute;sicos.</font></a></li>
		<li><a href="#tabs-2"><font style="font-family: 'verdana'; font-size: 10px; font-style: normal; font-weight:bold;">P Q R</font></a></li>
	</ul>
	<div id="tabs-1" style="padding: 0;">
		<table width="100%" border="1" align="center" class="t_bordeGris">
		<tr class=timparr>
			<td width="15%" align="left" class="titulos2"><b>&nbsp;Ingrese c&oacute;digo.</b></td>
			<td colspan="3" class="listado2"><input name="txtIdTema" id="txtIdTema" type="text" size="3" maxlength="3" value="<?= $txtIdTema ?>"></td>
		</tr>
		<tr>
			<td align="left" class="titulos2"><b>&nbsp;Ingrese nombre.</b></td>
			<td colspan="3" class="listado2"><input name="txtModelo" id="txtModelo" type="text" size="50" maxlength="70" value="<?= $txtModelo ?>"></td>
		</tr>
		<tr>
			<td class="titulos2"><b>&nbsp;Seleccione Estado</b></td>
			<td class="listado2">
				<select name="Slc_destado" id="Slc_destado" class="select">
					<option value="" selected>&lt; seleccione &gt;</option>
					<option value="0" <?= $offE ?>>Inactiva</option>
					<option value="1" <?= $onE ?>>Activa</option>
				</select>
			</td>
			<td class="titulos2"><b>&nbsp;Habilita PQR?</b></td>
			<td class="listado2">
				<select name="Slc_hPQR" id="Slc_hPQR" class="select">
					<option value="" selected>&lt; seleccione &gt;</option>
					<option value="1" <?=$onP ?>>SI</option>
					<option value="0" <?=$offP ?>>NO</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left" class="titulos2"><b>&nbsp;Ingrese Etiqueta.</b></td>
			<td colspan="3" class="listado2"><input name="txtPqrLabel" id="txtPqrLabel" type="text" size="110" maxlength="400" value="<?=$txtPqrLabel ?>"></td>
		</tr>
		</table>
		<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" class="listado2">
			<tr>
				<td width="10%">&nbsp;</td>
				<td width="20%" align="center"><input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado();" accesskey="L" alt="Alt + L"></td>
				<td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_AddAction" value="Agregar" accesskey="A"></td>
				<td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_UdtAction" value="Modificar" accesskey="M"></td>
				<td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_DelAction" value="Eliminar" accesskey="E"></td>
				<td width="10%">&nbsp;</td>
			</tr>
		</table>
	</div>
	<div id="tabs-2" style="padding: 0;">
		<table width="100%" border="0" align="center" class="t_bordeGris">
			<tr>
    			<td width="15%" align="left" class="titulos2" rowspan="1"><b>&nbsp;Seleccione Usuarios.</b></td>
    			<td class="listado2" colspan="3">
    				<table style="width: 100%; border: 1px solid black;" border="1">
    					<tr>
    							<td style="width: 50%; text-align: center;"><?php echo $slc_depe;?>
    								<div class="listBlock">              
                                        <ul id="sortable1" class='droptrue' style="height: 200px; overflow: auto;">
                    
                                        </ul>
                                    </div>
    							</td>
    							
                                <td style="width: 50%; text-align: center;">
                                	<input type="hidden" id="postOrder" name="postOrder" value="" size="30" />
                                    <div class="listBlock">               
                                        <ul id="sortable2" class='droptrue'>
                                        </ul>
                                    </div>
                                </td>
    					</tr>
    				</table>
    			</td>
    		</tr>
    		<tr>
    			<td align="left" class="titulos2"><b>&nbsp;Cantidad de radicados para distribución.</b></td>
    			<td class="listado2" colspan="2">
    				<input type="text" id="txtCantidad" name="txtCantidad" value="<?php echo $txtPqrDistri; ?>" />
    			</td>
    		</tr>
    		<tr>
    			<td colspan="4" style="text-align: center;" class="listado1">
    				<input type="button" name="btn_udtUsr" id="btn_udtUsr" value="Guardar" class="botones" onclick="guardarUsuarios();" />
    				&nbsp;
    				<input type="button" name="btn_listado" id="btn_listado" value="Listado" class="botones" onclick="ver_listado1();" />
    			</td>
    		</tr>
    		<tr>
    			<td align="left" class="titulos2"><b>&nbsp;Seleccione Tipos Documentales Asociados.</b></td>
    			<td class="listado2">
    				<?php echo $slc_tdoc;?><br /><?php echo $slc_tdocTema;?>
    			</td>
    			<td class="listado2" valign="top">
    				<input type="button" name="btn_addTdoc" id="btn_addTdoc" value="Agregar" class="botones" /><br />
    				<input type="submit" name="btn_delTdoc" id="btn_delTdoc" value="Eliminar" class="botones" />
    			</td>
    		</tr>
		</table>
	</div>
	<input type="hidden" id="txtEtiquetaTdoc2" name="txtEtiquetaTdoc2" />
	<input type="hidden" id="txtDescrTdoc2" name="txtDescrTdoc2" />
	<input type="hidden" id="btn_addTdoc2" name="btn_addTdoc2" value="0" />
</div>
</div>
</form>
<?php 

    if (!empty($msg))	echo "<p align='center'  style='color: #000000; font-size: 10pt; font-weight:bold; font-family:verdana'>$msg</p>"; 

    if ($depusus) {
        //foreach ($depusus as &$valor) {
            //$vars = explode("-", $valor);
            //if ($vars[0] == $deps[0]) {
                echo "<script type='text/javascript'>cargar_usuarios(); cargarUsuariosParam('".$depusus."');</script>";
            //}
        //}
    }
?>
</body>
</html>