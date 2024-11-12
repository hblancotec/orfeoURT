<?php
// echo $encabezado1;
$krdOld = $krd;
session_start();
if (! $krd)
    $krd = $krdOld;
$ruta_raiz = "..";
require_once "$ruta_raiz/_conf/constantes.php";
if (empty($_SESSION['dependencia'])) {
    include (ORFEOPATH . "rec_session.php");
}
$usua_doc = (! empty($_SESSION['usua_doc'])) ? $_SESSION['usua_doc'] : null;
$krd = $_SESSION['krd'];
$codusua = $codusuario = $_SESSION['codusuario'];
$coddepe = $dependencia = $_SESSION['dependencia'];
$retipifica = "0";
$retipifica = ($_SESSION['retipificatrd'] == null ? "0" : $_SESSION['retipificatrd']);

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

if (empty($usua_doc)) {
    echo "Error en Session del usuario";
    exit();
}

if (! $nurad) {
    $nurad = $rad;
}
if (! $_GET['cerrar']) {
    $cerrar = 0;
} else {
    $cerrar = 1;
}

if ($nurad) {
    $ent = substr($nurad, - 1);
}

if ($_GET['just']) {
    $just = $_GET['just'];
}
include_once ORFEOPATH . "include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
if (! defined('ADODB_FETCH_ASSOC'))
    define('ADODB_FETCH_ASSOC', 2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

include_once ORFEOPATH . "include/tx/Historico.php";
include_once ORFEOPATH . "class_control/TipoDocumental.php";
include_once ORFEOPATH . "include/tx/Expediente.php";
$coddepe = $dependencia;
$codusua = $codusuario;

$usuaModifica1 = "";
$usuaModifica2 = "";
$sqlus1 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TRD = 1 ";
$rsus1 = $db->conn->Execute($sqlus1);
if ($rsus1 && ! $rsus1->EOF) {
    $usuaModifica1 = $rsus1->fields['USUA_NOMB'];
}
$sqlus2 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TIPODOC = 1 ";
$rsus2 = $db->conn->Execute($sqlus2);
if ($rsus2 && ! $rsus2->EOF) {
    $usuaModifica2 = $rsus2->fields['USUA_NOMB'];
}

$cambio = '';
$coditrdx = "SELECT S.SGD_TPR_DESCRIP as TPRDESCRIP, R.RADI_DEPE_ACTU, R.RADI_USUA_ACTU, R.SGD_CAMBIO_TRD
            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO S ON R.TDOC_CODI = S.SGD_TPR_CODIGO
            WHERE R.RADI_NUME_RADI = $nurad";
$res_coditrdx = $db->conn->Execute($coditrdx);
if ($res_coditrdx && ! $res_coditrdx->EOF) {
    $TDCactu = $res_coditrdx->fields['TPRDESCRIP'];
    $usuactu = $res_coditrdx->fields['RADI_USUA_ACTU'];
    $depeactu = $res_coditrdx->fields['RADI_DEPE_ACTU'];
    $cambio = ($res_coditrdx->fields['SGD_CAMBIO_TRD'] == null ? '0' : $res_coditrdx->fields['SGD_CAMBIO_TRD']);
}

$serieactu = '';
$subseactu = '';
$docuactu = '';
$sqlDt = "SELECT R.SGD_MRD_CODIGO, R.RADI_NUME_RADI, M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO
            FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
            WHERE RADI_NUME_RADI = $nurad AND R.DEPE_CODI = $coddepe ORDER BY R.SGD_RDF_FECH DESC ";
$rsDt = $db->conn->Execute($sqlDt);
if ($rsDt && ! $rsDt->EOF) {
    $serieactu = $rsDt->fields["SGD_SRD_CODIGO"];
    $subseactu = $rsDt->fields["SGD_SBRD_CODIGO"];
    $docuactu = $rsDt->fields["SGD_TPR_CODIGO"];
}
$tiporad = substr($nurad, - 1);

$pqr = '0';
$sqlPqr = "SELECT D.SGD_TPR_NOTIFICA AS PQR FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO D ON R.TDOC_CODI = D.SGD_TPR_CODIGO
            WHERE R.RADI_NUME_RADI = $nurad ";
$rsPqr = $db->conn->Execute($sqlPqr);
if ($rsPqr && ! $rsPqr->EOF) {
    $pqr = ($rsPqr->fields["PQR"] == null ? '0' : $rsPqr->fields["PQR"]);
}

$depex = $_SESSION["depe_nomb"];
$usuax = $_SESSION["usua_nomb"];

$trd = new TipoDocumental($db);
$encabezadol = "tipificar_documento.php?" . session_name() . "=" . session_id();
$encabezadol .= "&krd=$krd&nurad=$nurad&coddepe=$coddepe";
$encabezadol .= "&codusuario=$codusua&codusua=$codusua";
$encabezadol .= "&codusuario=$codusuario&depende=$depende";
$encabezadol .= "&ent=$ent&tdoc=$tdoc&codiTRDModi=$codiTRDModi";
$encabezadol .= "&codiTRDEli=$codiTRDEli&codserie=$codserie";
$encabezadol .= "&tsub=$tsub&ind_ProcAnex=$ind_ProcAnex&texp=$texp&just=$just";

$trdExp = new Expediente($db);
$numExpediente = $trdExp->consulta_exp("$nurad");
$mrdCodigo = $trdExp->consultaTipoExpediente("$numExpediente");
$trdExpediente = $trdExp->descSerie . " / " . $trdExp->descSubSerie;
$descPExpediente = $trdExp->descTipoExp;
$descFldExp = $trdExp->descFldExp;
$codigoFldExp = $trdExp->codigoFldExp;
$expUsuaDoc = $trdExp->expUsuaDoc;

// PARTE DE CODIGO DONDE SE IMPLEMENTA EL CAMBIO DE ESTADO AUTOMATICO AL TIPIFICAR.
include ORFEOPATH . "include/tx/Flujo.php";
$objFlujo = new Flujo($db, $texp, $usua_doc);
$expEstadoActual = $objFlujo->actualNodoExpediente($numExpediente);
$arrayAristas = $objFlujo->aristasSiguiente($expEstadoActual);
$aristaSRD = $objFlujo->aristaSRD;
$aristaSBRD = $objFlujo->aristaSBRD;
$aristaTDoc = $objFlujo->aristaTDoc;
$aristaTRad = $objFlujo->aristaTRad;
$arrayNodos = $objFlujo->nodosSig;
$aristaAutomatica = $objFlujo->aristaAutomatico;
$aristaTDoc = $objFlujo->aristaTDoc;
if ($arrayNodos) {
    $i = 0;
    foreach ($arrayNodos as $value) {
        $nodo = $value;
        $arAutomatica = $aristaAutomatica[$i];
        $aristaActual = $arrayAristas[$i];
        $arSRD = $aristaSRD[$i];
        $arSBRD = $aristaSBRD[$i];
        $arTDoc = $aristaTDoc[$i];
        $arTRad = $aristaTRad[$i];
        $nombreNodo = $objFlujo->getNombreNodo($nodo, $texp);
        if ($arAutomatica == 1 and $arSRD == $codserie and $arSBRD == $tsub and $arTDoc == $tdoc and $arTRad == $ent) {
            if ($insertar_registro) {
                $objFlujo->cambioNodoExpediente($numExpediente, $nurad, $nodo, $aristaActual, 1, "Cambio de Estado Automatico.", $texp);
                $codiTRDS = $codiTRD;
                $i ++;
                $TRD = $codiTRD;
                $observa = "*TRD*" . $codserie . "/" . $codiSBRD . " (Creacion de Expediente.)";
                include_once (ORFEOPATH . "include/tx/Historico.php");
                $radicados[] = $nurad;
                $tipoTx = 51;
                $Historico = new Historico($db);
                $rs = $db->conn->Execute($sql);
                $mensaje = "SE REALIZ&Oacute; CAMBIO DE ESTADO AUTOMATICAMENTE AL EXPEDIENTE No. < $numExpediente >
							<BR> EL NUEVO ESTADO DEL EXPEDIENTE ES  <<< $nombreNodo >>>";
            } else {
                $mensaje = "SI ESCOGE ESTE TIPO DOCUMENTAL EL ESTADO DEL EXPEDIENTE  < $numExpediente >
							 CAMBIARA EL ESTADO AUTOMATICAMENTE A <BR> <<< $nombreNodo >>>";
            }

            echo "<table width=100% class=borde_tab>
						<tr><td align=center>
						<span class=titulosError align=center>
						$mensaje
						</span>
						</td></tr>
						</table><table><tr><td></td></tr></table>";
        }
        $i ++;
    }
}
?>
<html>
<head>

<meta http-equiv="Expires" content="0">
<meta http-equiv="Last-Modified" content="0">
<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
<meta http-equiv="Pragma" content="no-cache">
  
<title>Tipificar Documento</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery.blockUI.js"></script>

<script>
    $(document).ready(function () {

    	var parametros = {
    	    	"tipo" : 6,
    	    	"rad" : <?php echo $nurad;?>
    	    };
    	    			
    	    $.ajax({
    	    	url: '../class_control/ModificaTRD.php',
    	    	type: 'POST',
    	    	cache: false,
    	    	async: false,
    	    	data:  parametros,
    	    	success: function(resultado) {
    	    		var grid = document.getElementById('bodyClasifica');
                    if (grid != null) {
                        grid.innerHTML = "";
                        grid.innerHTML = resultado;
                    }
    	    	},
    	    	error: function(text) { alert('Se ha producido un error ' + text); }
    	    });
    	    
    });

	function regresar(){
		document.TipoDocu.submit();
	}
	
	function cargaSubserie()
	{
		debugger;
		var serie = $("#codserie").val();
		var subseactu = "";
		if ($("#subseactu").val() != "")
			subseactu = $("#subseactu").val();

		var parametros = {
	    	"tipo" : 3,
	    	"serie" : serie,
	    	"subseactu" : subseactu
	    };
	    			
	    $.ajax({
	    	url: '../class_control/ModificaTRD.php',
	    	type: 'POST',
	    	cache: false,
	    	async: false,
	    	data:  parametros,
	    	success: function(resultado) {
	    		debugger;
            	document.getElementById("tsub").options.length = 0;
                $('#tsub').append(resultado);

                cargaTipoDoc();
	   		},
	    	error: function(text) { alert('Se ha producido un error ' + text); }
	    });
	}

	function cargaTipoDoc()
	{
		debugger;
		var serie = $("#codserie").val();
		var subserie = $("#tsub").val();
		var nurad = $("#nurad").val();
		var docuactu = "";
		if ($("#docuactu").val() != "")
			docuactu = $("#docuactu").val();
		 
		var parametros = {
	    	"tipo" : 4,
	    	"serie" : serie,
	    	"subserie" : subserie,
	    	"nurad" : nurad,
	    	"docuactu" : docuactu
	    };
	    			
	    $.ajax({
	    	url: '../class_control/ModificaTRD.php',
	    	type: 'POST',
	    	cache: false,
	    	async: false,
	    	data:  parametros,
	    	success: function(resultado) {
	    		debugger;
	    		document.getElementById("tdoc").options.length = 0;
                $('#tdoc').append(resultado);
	    	},
	    	error: function(text) { alert('Se ha producido un error ' + text); }
	    });
	}

	function insertarTrd(pqr, cambio, retipifica, tiporad, tdocactu, serieactu, subseractu)
	{
		debugger;
		$.blockUI({
		      message: 'Espere Un Momento ...',
		      css: {
		        border: 'none',
		        padding: '15px',
		        backgroundColor: '#000',
		        '-webkit-border-radius': '10px',
		        '-moz-border-radius': '10px',
		        opacity: '.5',
		        color: '#fff',
		        fontSize: '18px',
		        fontFamily: 'Verdana,Arial',
		        fontWeight: 200 } });
        
		var serie = $("#codserie").val();
		var subserie = $("#tsub").val();
		var nurad = $("#nurad").val();
		var tdocu = $("#tdoc").val();
		var deperad = nurad.toString().substr(4, 3);

		var usuModifica1 = $("#usuModifica1").val();
		var usuModifica2 = $("#usuModifica2").val();
		var usactu = document.TipoDocu.usuactu.value;
		var depactu = document.TipoDocu.depeactu.value;

		if (serie == 0 || tdocu == 0) {
			alert("Seleccione todos los datos !!");
			return false;
		}
		
		var valida = 0;
		if (pqr == '1') {
			if ((cambio == '' || cambio == '0') && (deperad != 663) && (serieactu != '') && (serie != '176' && serie != '999') && (tiporad == '2') && (retipifica == '0')) {
				if (tdocactu != tdocu) {

    				var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica1 + " del Grupo de Gesti\u00f3n Documental y Biblioteca, desea enviar notificaci\u00f3n al \u00e1rea ? ");
	                if (opcion == true) {
	                	var rad = <?php echo $nurad; ?>;

							var parametros = {
	                    		"rad"  : rad,
	                    		"tipo" : 1,
	                    		"usua" : usactu,
	                    		"depe" : depactu,
	                    		"notifica" : 1,
	                    		"seractu" : serieactu,
	                    		"subseractu": subseractu,
	                    		"tdocactu" : tdocactu,
	                    		"serie" : serie,
	                    		"tsub" : subserie,
	                    		"tdoc" : tdocu
	                    	};
	                    			
	                    	$.ajax({
	                    		url: '../class_control/ModificaTRD.php',
	                    		type: 'POST',
	                    		cache: false,
	                    		async: false,
	                    		data:  parametros,
	                    		success: function(text) {
	                    			debugger;
	                    			if(text != '1') {
	                					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema. " + text);
	                    			} else {
	                    				//alert("Error en el proceso, consulte el administrador del sistema." + text);
	                    			} 
	                    			opener.regresar();
	                    			window.close();
	                    		},
	                    		error: function(text) { alert('Se ha producido un error ' + text); }
	                    	});
	                } else {
	                	valida = 0;
	                }
    			} else {
        			valida = 1;
    			}
			}
			else if ((cambio == '' || cambio == '0') && (deperad != 663) && (serieactu == '') && (serie == '176' || serie == '999') && (tiporad == '2') && (retipifica == '0')) {
    			if (tdocactu != tdocu) {

    				var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica2 + " del Grupo de Relacionamiento al Ciudadano, desea enviar notificaci\u00f3n al \u00e1rea ? ");
	                if (opcion == true) {
	                	var rad = <?php echo $nurad; ?>;

							var parametros = {
	                    		"rad"  : rad,
	                    		"tipo" : 1,
	                    		"usua" : usactu,
	                    		"depe" : depactu,
	                    		"notifica" : 2,
	                    		"seractu" : serieactu,
	                    		"subseractu": subseractu,
	                    		"tdocactu" : tdocactu,
	                    		"serie" : serie,
	                    		"tsub" : subserie,
	                    		"tdoc" : tdocu
	                    	};
	                    			
	                    	$.ajax({
	                    		url: '../class_control/ModificaTRD.php',
	                    		type: 'POST',
	                    		cache: false,
	                    		async: false,
	                    		data:  parametros,
	                    		success: function(text) {
	                    			debugger;
	                    			if(text != '1') {
	                					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
	                    			} else {
	                    				//alert("Error en el proceso, consulte el administrador del sistema." + text);
	                    			} 
	                    			opener.regresar();
	                    			window.close();
	                    		},
	                    		error: function(text) { alert('Se ha producido un error ' + text); }
	                    	});
	                } else {
	                	valida = 0;
	                }
    			} else {
        			valida = 1;
    			}
    		} 
    		else if (cambio == '1' || cambio == '3') {
    			alert('No se permite insertar la TRD, est\u00e1 pendiente de aprobaci\u00f3n para cambio de tipo documental !!');
    		}
    		else if (cambio == '2') {
    			valida = 1;
    		}
    		else if (cambio == '4') {
    			valida = 1;
    		}
    		else {
    			valida = 1;
    		}
		} else {
			valida = 1;
		}

		if (valida == 1) {
			
			var parametros = {
    	    	"tipo" : 8,
    	    	"rad" : nurad,
    	    	"serie" : serie,
    	    	"subserie" : subserie,
    	    	"tipodoc" : tdocu
    	    };
	    			
	    	$.ajax({
    	    	url: '../class_control/ModificaTRD.php',
    	    	type: 'POST',
    	    	cache: false,
    	    	async: false,
    	    	data:  parametros,
    	    	success: function(text) {
    	    		if (text == 1) {
        				alert("Registro Insertado !!");
    
        				var parametros = {
        			  		"tipo" : 6,
        			    	"rad" : nurad
        			    };
        			    	    			
        			    $.ajax({
        			    	url: '../class_control/ModificaTRD.php',
        			    	type: 'POST',
        			    	cache: false,
        			    	async: false,
        			    	data:  parametros,
        			    	success: function(resultado) {
        			    	var grid = document.getElementById('bodyClasifica');
        			        if (grid != null) {
        			        	grid.innerHTML = "";
        			            grid.innerHTML = resultado;
        			        }
        			    },
        			    	error: function(text) { alert('Se ha producido un error ' + text); }
        			  	});
        				
            		} else if (text == 2) {
                		alert("Ya existe una Clasificaci\u00F3n para este radicado.");
            		} else {
            			alert("Error en el proceso, consulte el administrador del sistema." + text);
            		}
            	},
    	    	error: function(text) { alert('Se ha producido un error ' + text); }
    	    });
		}

		$.unblockUI();
	}

		function borrarArchivo(anexo, linkarch){
			if (confirm('Esta seguro de borrar este Registro ?')) {
				var rad = <?php echo $nurad; ?>;
				var parametros = {
                	"rad"  : rad,
                	"tipo" : 7,
                	"coditrd" : anexo
                };
                			
                $.ajax({
                	url: '../class_control/ModificaTRD.php',
                	type: 'POST',
                	cache: false,
                	async: false,
                	data:  parametros,
                	success: function(text) {
                		if(text == 1) {
            				alert("Registro Eliminado");

            				var parametros = {
            			    	"tipo" : 6,
            			    	"rad" : rad
            			    };
            			    	    			
            			    $.ajax({
            			    	url: '../class_control/ModificaTRD.php',
            			    	type: 'POST',
            			    	cache: false,
            			    	async: false,
            			    	data:  parametros,
            			    	success: function(resultado) {
            			    		var grid = document.getElementById('bodyClasifica');
            			            if (grid != null) {
            			            	grid.innerHTML = "";
            			                grid.innerHTML = resultado;
            			            }
            			    	},
            			    	    error: function(text) { alert('Se ha producido un error ' + text); }
            			  		});
            			  		
                		} else {
                			alert("Error en el proceso, consulte el administrador del sistema." + text);
                		} 
                	},
                		error: function(text) { alert('Se ha producido un error ' + text); }
                	});
				
				//nombreventana="ventanaBorrarR1";
				//url="tipificar_documentos_transacciones.php?sessid=<?=session_id()?>&krd=<?=$krd?>&borrar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&nurad=<?=$nurad?>&depex=<?=$depex?>&usux=<?=$usux?>&codiTRDEli="+anexo+"&linkarchivo="+linkarch;
				//window.open(url,nombreventana,'height=250,width=300');
			}
			return;
		}

		//<!-- Funcion que modifica la trd existente-->
		function procModificar(seractu, tiporad, cambio, tdocactu, retipifica, pqr, subseractu) {

			debugger;
			        
			var usuModifica1 = $("#usuModifica1").val();
			var usuModifica2 = $("#usuModifica2").val();
			var tdocu = document.TipoDocu.tdoc.value;
			var serie = document.TipoDocu.codserie.value;
			var tsub = document.TipoDocu.tsub.value;
			var usactu = document.TipoDocu.usuactu.value;
			var depactu = document.TipoDocu.depeactu.value;

			if (serie == 0 || tdocu == 0) {
				alert("Seleccione todos los datos !!");
				return false;
			}
			
			if ((cambio == '' || cambio == '0') && (seractu == '') && (tdocactu != tdocu) && tiporad == '2' && pqr == 1 && (retipifica == '0')) {

				var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica2 + " del Grupo de Relacionamiento al Ciudadano, desea enviar notificaci\u00f3n al \u00e1rea ? ");
                if (opcion == true) {

                	$.blockUI({
      			      message: 'Espere Un Momento ...',
      			      css: {
      			        border: 'none',
      			        padding: '15px',
      			        backgroundColor: '#000',
      			        '-webkit-border-radius': '10px',
      			        '-moz-border-radius': '10px',
      			        opacity: '.5',
      			        color: '#fff',
      			        fontSize: '18px',
      			        fontFamily: 'Verdana,Arial',
      			        fontWeight: 200 } 
      	        	});
          	        
                	var rad = <?php echo $nurad; ?>;

						var parametros = {
                    		"rad"  : rad,
                    		"tipo" : 1,
                    		"usua" : usactu,
                    		"depe" : depactu,
                    		"notifica" : 2,
                    		"seractu" : seractu,
                    		"subseractu": subseractu,
                    		"tdocactu" : tdocactu,
                    		"serie" : serie,
                    		"tsub" : tsub,
                    		"tdoc" : tdocu
                    	};
                    			
                    	$.ajax({
                    		url: '../class_control/ModificaTRD.php',
                    		type: 'POST',
                    		cache: false,
                    		async: false,
                    		data:  parametros,
                    		success: function(text) {
                    			debugger;
                    			if(text != '1') {
                					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
                    			} else {
                    				//alert("Error en el proceso, consulte el administrador del sistema." + text);
                    			} 
                    			$.unblockUI();
                    			opener.regresar();
                    			window.close();
                    		},
                    		error: function(text) { alert('Se ha producido un error ' + text); }
                    	});
                } else {
                	$.unblockUI();
                	opener.regresar();
                	window.close();
                }
				
			}
			else if ((cambio == '' || cambio == '0') && (seractu == '176' || seractu == '999') && (serie == '176' || serie == '999') && (tiporad == '2') && (retipifica == '0')) {
				if (tdocactu != tdocu) {

					var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica2 + " del Grupo de Relacionamiento al Ciudadano, desea enviar notificaci\u00f3n al \u00e1rea ? ");
	                if (opcion == true) {
	                	$.blockUI({
	      			      message: 'Espere Un Momento ...',
	      			      css: {
	      			        border: 'none',
	      			        padding: '15px',
	      			        backgroundColor: '#000',
	      			        '-webkit-border-radius': '10px',
	      			        '-moz-border-radius': '10px',
	      			        opacity: '.5',
	      			        color: '#fff',
	      			        fontSize: '18px',
	      			        fontFamily: 'Verdana,Arial',
	      			        fontWeight: 200 } 
	      	        });
		      	        
	                	var rad = <?php echo $nurad; ?>;

							var parametros = {
	                    		"rad"  : rad,
	                    		"tipo" : 1,
	                    		"usua" : usactu,
	                    		"depe" : depactu,
	                    		"notifica" : 2,
	                    		"seractu" : seractu,
	                    		"subseractu": subseractu,
	                    		"tdocactu" : tdocactu,
	                    		"serie" : serie,
	                    		"tsub" : tsub,
	                    		"tdoc" : tdocu
	                    	};
	                    			
	                    	$.ajax({
	                    		url: '../class_control/ModificaTRD.php',
	                    		type: 'POST',
	                    		cache: false,
	                    		async: false,
	                    		data:  parametros,
	                    		success: function(text) {
	                    			debugger;
	                    			if(text != '1') {
	                					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
	                    			} else {
	                    				//alert("Error en el proceso, consulte el administrador del sistema." + text);
	                    			} 
	                    			$.unblockUI();
	                    			opener.regresar();
	                    			window.close();
	                    		},
	                    		error: function(text) { alert('Se ha producido un error ' + text); }
	                    	});
	                } else {
	                	$.unblockUI();
	                	opener.regresar();
	                	window.close();
	                }
					
				}
			} 
			else if ((cambio == '' || cambio == '0') && (seractu == '176' || seractu == '999') && (serie != '176' || serie != '999') && (tiporad == '2') && (retipifica == '0')) {

				if (tdocactu != tdocu) {

					var opcion = confirm("Por ser documento tipificado como PQRSD, para su cambio comun\u00edquese con " + usuModifica1 + " del Grupo de Biblioteca y archivo, desea enviar notificaci\u00f3n al \u00e1rea ? ");
	                if (opcion == true) {

	                	$.blockUI({
	      			      message: 'Espere Un Momento ...',
	      			      css: {
	      			        border: 'none',
	      			        padding: '15px',
	      			        backgroundColor: '#000',
	      			        '-webkit-border-radius': '10px',
	      			        '-moz-border-radius': '10px',
	      			        opacity: '.5',
	      			        color: '#fff',
	      			        fontSize: '18px',
	      			        fontFamily: 'Verdana,Arial',
	      			        fontWeight: 200 } 
	      	        });
		      	        
	                	var rad = <?php echo $nurad; ?>;

							var parametros = {
	                    		"rad"  : rad,
	                    		"tipo" : 1,
	                    		"usua" : usactu,
	                    		"depe" : depactu,
	                    		"notifica" : 1,
	                    		"seractu" : seractu,
	                    		"subseractu": subseractu,
	                    		"tdocactu" : tdocactu,
	                    		"serie" : serie,
	                    		"tsub" : tsub,
	                    		"tdoc" : tdocu
	                    	};
	                    			
	                    	$.ajax({
	                    		url: '../class_control/ModificaTRD.php',
	                    		type: 'POST',
	                    		cache: false,
	                    		async: false,
	                    		data:  parametros,
	                    		success: function(text) {
	                    			
	                    			if(text != '1') {
	                					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
	                    			} else {
	                    				//alert("Error en el proceso, consulte el administrador del sistema." + text);
	                    			} 
	                    			$.unblockUI();
	                    			opener.regresar();
	                    			window.close();
	                    		},
	                    		error: function(text) { alert('Se ha producido un error ' + text); }
	                    	});
	                } else {
	                	$.unblockUI();
	                	opener.regresar();
	                	window.close();
	                }
					
				}
				
			} else {
			
				if (document.TipoDocu.tdoc.value != 0 && document.TipoDocu.codserie.value != 0 && document.TipoDocu.justificacion.value != '') {

					if (cambio == '1') {
						alert('No se permite crear el expediente, est\u00e1 pendiente de aprobaci\u00f3n para cambio de TRD !!');
					}
					else if (cambio == '3') {
						alert('No se permite crear el expediente, est\u00e1 pendiente de aprobaci\u00f3n para cambio de Tipo Documental !!');
					}
					else if (cambio == '2' && seractu == 176 && serie == 176 && tiporad == '2') {
						alert('Se aprob\u00f3 el cambio de la TRD, por lo tanto debe cambiarlo');
					}
					else if (cambio == '4' && seractu == 176 && serie != 176 && tiporad == '2') {
						alert('Se aprob\u00f3 solo el cambio del Tipo Documental, por lo tanto no debe camabiar la TRD completa');
					}
					else if ((cambio == '2' || cambio == '4')  && tiporad == '2') {
						if (tdocactu != tdocu) {

							$.blockUI({
							      message: 'Espere Un Momento ...',
							      css: {
							        border: 'none',
							        padding: '15px',
							        backgroundColor: '#000',
							        '-webkit-border-radius': '10px',
							        '-moz-border-radius': '10px',
							        opacity: '.5',
							        color: '#fff',
							        fontSize: '18px',
							        fontFamily: 'Verdana,Arial',
							        fontWeight: 200 } 
					        });
					        
							var rad = <?php echo $nurad; ?>;

							var parametros = {
	                    		"rad"  : rad,
	                    		"tipo" : 5,
	                    		"cambio" : cambio,
	                    		"just" : document.TipoDocu.justificacion.value,
	                    		"tdoc" : document.TipoDocu.tdoc.value,
	                    		"serie" : document.TipoDocu.codserie.value,
	                    		"subser" : document.TipoDocu.tsub.value
	                    	};
	                    			
	                    	$.ajax({
	                    		url: '../class_control/ModificaTRD.php',
	                    		type: 'POST',
	                    		cache: false,
	                    		async: false,
	                    		data:  parametros,
	                    		success: function(text) {

	                    			$.unblockUI();
	                    			
	                    			if(text == 1) {
	                					alert("Registro Modificado");

	                					var parametros = {
	                			    	    "tipo" : 6,
	                			    	    "rad" : rad
	                			    	};
	                			    	    			
	                			    	$.ajax({
	                			    		url: '../class_control/ModificaTRD.php',
	                			    	    type: 'POST',
	                			    	    cache: false,
	                			    	    async: false,
	                			    	    data:  parametros,
	                			    	    success: function(resultado) {
	                			    	    	var grid = document.getElementById('bodyClasifica');
	                			                if (grid != null) {
	                			                	grid.innerHTML = "";
	                			                    grid.innerHTML = resultado;
	                			                }
	                			    	    },
	                			    	    error: function(text) { alert('Se ha producido un error ' + text); }
	                			  		});
	                    			} else {
	                    				alert("Error en el proceso, consulte el administrador del sistema.");
	                    			} 
	                    		},
	                    		error: function(text) { 
	                    			$.unblockUI();
	                    			alert('Se ha producido un error ' + text); 
	                    		}
	                    	});
						}
					}
					else {
    					if (seractu == '' && tdocactu == '') {
    						alert("No existe Registro para Modificar ");
    					} else {
    						var agree = confirm('Esta Seguro de Modificar el Registro de su Dependencia ?');
							if (agree == true) {

								$.blockUI({
								      message: 'Espere Un Momento ...',
								      css: {
								        border: 'none',
								        padding: '15px',
								        backgroundColor: '#000',
								        '-webkit-border-radius': '10px',
								        '-moz-border-radius': '10px',
								        opacity: '.5',
								        color: '#fff',
								        fontSize: '18px',
								        fontFamily: 'Verdana,Arial',
								        fontWeight: 200 } 
						        });
						        
								var rad = <?php echo $nurad; ?>;
								
								var parametros = {
			                    		"rad"  : rad,
			                    		"tipo" : 5,
			                    		"cambio" : cambio,
			                    		"just" : document.TipoDocu.justificacion.value,
			                    		"tdoc" : document.TipoDocu.tdoc.value,
			                    		"serie" : document.TipoDocu.codserie.value,
			                    		"subser" : document.TipoDocu.tsub.value
			                    	};
			                    			
			                    	$.ajax({
			                    		url: '../class_control/ModificaTRD.php',
			                    		type: 'POST',
			                    		cache: false,
			                    		async: false,
			                    		data:  parametros,
			                    		success: function(text) {

			                    			$.unblockUI();
			                    			
			                    			if(text == 1) {
			                					alert("Registro Modificado");

			                					var parametros = {
			                			    	    "tipo" : 6,
			                			    	    "rad" : rad
			                			    	};
			                			    	    			
			                			    	$.ajax({
			                			    		url: '../class_control/ModificaTRD.php',
			                			    	    type: 'POST',
			                			    	    cache: false,
			                			    	    async: false,
			                			    	    data:  parametros,
			                			    	    success: function(resultado) {
			                			    	    	var grid = document.getElementById('bodyClasifica');
			                			                if (grid != null) {
			                			                	grid.innerHTML = "";
			                			                    grid.innerHTML = resultado;
			                			                }
			                			    	    },
			                			    	    error: function(text) { alert('Se ha producido un error ' + text); }
			                			  		});
			                    			} else {
			                    				alert("Error en el proceso, consulte el administrador del sistema.");
			                    			} 
			                    		},
			                    		error: function(text) { 
			                    			$.unblockUI();
			                    			alert('Se ha producido un error ' + text); 
			                    		}
			                    	});
								
								/*nombreventana = "ventanaModiR1";
								url = "tipificar_documentos_transacciones.php?sessid=<?=session_id()?>&krd=<?=$krd?>&modificar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&tdoc=<?=$tdoc?>&tsub=<?=$tsub?>&codserie=<?=$codserie?>&coddepe=<?=$coddepe?>&codusuario=<?=$codusuario?>&depex=<?=$depex?>&usuax=<?=$usuax?>&dependencia=<?=$dependencia?>&nurad=<?=$nurad?>&just="+document.TipoDocu.justificacion.value;
								window.open(url, nombreventana, 'height=200,width=300');*/
							}
    					}
					}
				}
				else {
				  alert("Seleccione todos los campos y d\u00edgte la observaci\u00f3n");
				}
			}
			return;
		}

		function cerrar() 
		{
			var valVentana = document.getElementById('valVentana').value;
			if (valVentana == 0) {
    			opener.regresar();
    			window.close();
			} else {
				window.close();
			}
		}
</script>
</head>
<body bgcolor="#FFFFFF">
	<form method="post" action="<?=$encabezadol?>" name="TipoDocu">
		<input type="hidden" id="valVentana" name="valVentana" value="<?php echo $cerrar;?>">
		<input type="hidden" id="nurad" name="nurad"
			value="<?php echo $nurad;?>"> <input type="hidden" id="serieactu"
			name="serieactu" value="<?php echo $serieactu;?>"> <input
			type="hidden" id="subseactu" name="subseactu"
			value="<?php echo $subseactu;?>"> <input type="hidden" id="docuactu"
			name="docuactu" value="<?php echo $docuactu;?>"> <input type="hidden"
			id="usuactu" name="usuactu" value="<?php echo $usuactu;?>"> <input
			type="hidden" id="depeactu" name="depeactu"
			value="<?php echo $depeactu;?>"> <input type="hidden" id="retipifica"
			name="retipifica" value="<?php echo $_SESSION["retipificatrd"];?>"> <input
			type="hidden" id="usuModifica1" name="usuModifica1"
			value="<?php echo $usuaModifica1;?>"> <input type="hidden"
			id="usuModifica2" name="usuModifica2"
			value="<?php echo $usuaModifica2;?>">
<?php

/*
 * if ($insertar_registro && $tdoc != 0 && $tsub != 0 && $codserie != 0) {
 * include_once (ORFEOPATH . "include/query/busqueda/busquedaPiloto1.php");
 *
 * $radiNumero = "";
 * $sql = "SELECT $radi_nume_radi AS RADI_NUME_RADI
 * FROM SGD_RDF_RETDOCF r
 * WHERE RADI_NUME_RADI = $nurad";
 * $rs = $db->conn->Execute($sql);
 * if ($rs && ! $rs->EOF) {
 * $radiNumero = $rs->fields["RADI_NUME_RADI"];
 * }
 *
 * if ($radiNumero != "") {
 * $codserie = 0;
 * $tsub = 0;
 * $tdoc = 0;
 * $mensaje_err = "<HR> <center> <B> <font color='RED'>
 * Ya existe una Clasificaci&oacute;n para este radicado <$coddepe>
 * <BR> VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO
 * </font> </B> </center> <HR>";
 * } else {
 * $isqlTRD = "SELECT SGD_MRD_CODIGO
 * FROM SGD_MRD_MATRIRD
 * WHERE DEPE_CODI = $dependencia
 * AND SGD_SRD_CODIGO = $codserie
 * AND SGD_SBRD_CODIGO = $tsub
 * AND SGD_TPR_CODIGO = $tdoc";
 *
 * $rsTRD = $db->conn->Execute($isqlTRD);
 * $i = 0;
 *
 * while (! $rsTRD->EOF) {
 * $codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
 * $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
 * $i ++;
 * $rsTRD->MoveNext();
 * }
 *
 * $radicados = $trd->insertarTRD($codiTRDS, $codiTRD, $nurad, $coddepe, $codusua);
 *
 * $TRD = $codiTRD;
 * include (ORFEOPATH . "radicacion/detalle_clasificacionTRD.php");
 * $sqlH = "SELECT $radi_nume_radi RADI_NUME_RADI
 * FROM SGD_RDF_RETDOCF r
 * WHERE r.RADI_NUME_RADI = $nurad
 * AND r.SGD_MRD_CODIGO = $codiTRD";
 * $rsH = $db->conn->Execute($sqlH);
 * $i = 0;
 * while (! $rsH->EOF) {
 * $codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
 * $i ++;
 * $rsH->MoveNext();
 * }
 *
 * $Historico = new Historico($db);
 *
 * $transac = 0;
 * if ($radiNumero == "") {
 * $transac = 105;
 * } else {
 * $transac = 34;
 * }
 * $observa = "Tipo documental anterior: " . $TDCactu;
 * $radiModi = $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, $transac);
 *
 * // Se guarda el registro en el historico de TRD
 * $queryGrabar = "INSERT INTO SGD_HMTD_HISMATDOC( SGD_HMTD_FECHA,
 * RADI_NUME_RADI,
 * USUA_CODI,
 * USUA_DOC,
 * DEPE_CODI,
 * SGD_HMTD_OBSE,
 * SGD_MRD_CODIGO,
 * SGD_TTR_CODIGO)
 * VALUES( " . $db->conn->OffsetDate(0, $db->conn->sysTimeStamp) . ",
 * $nurad,
 * $codusua,
 * $usua_doc,
 * $dependencia,
 * 'Se inserta TRD',
 * '$codiTRD',
 * 32)";
 *
 * $ejecutarQuerey = $db->conn->Execute($queryGrabar);
 *
 * if (empty($ejecutarQuerey)) {
 * echo 'No se guardo el registro en historico documental';
 * }
 *
 * // Actualiza el campo tdoc_codi de la tabla Radicados
 * $radiUp = $trd->actualizarTRD($codiRegH, $tdoc);
 * $codserie = 0;
 * $tsub = 0;
 * $tdoc = 0;
 * }
 * }
 */
?>
		<table border=0 width="90%" align="center" class="borde_tab"
			cellspacing="0">
			<tr align="center">
				<td height="10" class="titulos4">APLICACI&Oacute;N DE LA TRD PARA EL RADICADO # <?php echo $nurad; ?></td>
			</tr>
		</table>
		<br>

		<table width="90%" border="0" cellspacing="8" cellpadding="2"
			align="center" class="borde_tab">
			<tr>
				<td class="titulos2">SERIE:</td>
				<td class="listado1">

                    <?php
                    if (! $tdoc)
                        $tdoc = 0;
                    if (! $codserie)
                        $codserie = 0;
                    if (! $tsub)
                        $tsub = 0;
                    $fechah = date("dmy") . " " . time("h_m_s");
                    $fecha_hoy = Date("d-m-Y");
                    $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
                    $check = 1;
                    $fechaf = date("dmy") . "_" . time("hms");
                    $num_car = 4;
                    $nomb_varc = "s.sgd_srd_codigo";
                    $nomb_varde = "s.sgd_srd_descrip";
                    include ORFEOPATH . "include/query/trd/queryCodiDetalle.php";

                    $querySerie = "	SELECT	distinct ($sqlConcat) as detalle,
                        						s.sgd_srd_codigo
                        				FROM	sgd_mrd_matrird m, 
                        						sgd_srd_seriesrd s
                        				WHERE	m.depe_codi = $dependencia
                        						and s.sgd_srd_codigo = m.sgd_srd_codigo and
                        						m.sgd_mrd_esta = '1' and
                        						'$fecha_hoy' between s.sgd_srd_fechini and s.sgd_srd_fechfin
                        				ORDER BY detalle";
                    $rsD = $db->conn->Execute($querySerie);

                    $comentarioDev = "Muestra las Series Docuementales";
                    include ORFEOPATH . "include/tx/ComentarioTx.php";
                    print $rsD->GetMenu2("codserie", $serieactu, "0:-- Seleccione --", false, "", "id='codserie' onChange='cargaSubserie()' class='select'");
                    ?>

					</td>
			</tr>
			<tr>
				<td class="titulos2">SUB-SERIE:</td>
				<td class="listado1"><select id="tsub" name="tsub" class='select'
					onChange='cargaTipoDoc()'>
						<option value='0'>-- Seleccione --</option>
				</select></td>
			</tr>
			<tr>
				<td class="titulos2">TIPO DE DOCUMENTO:</td>
				<td class="listado1"><select id="tdoc" name="tdoc" class='select'>
						<option value='0'>-- Seleccione --</option>
				</select></td>
			</tr>

<?php
if ($TDCactu != 'NO DEFINIDO') {
    ?>
		
				<tr>
				<td class="titulos2">OBSERVACI&Oacute;N:</td>
				<td class="listado1"><input type="text" name="justificacion"
					size="65"></td>
			</tr>
		
<?php
}
?>
		
			</table>

		<table
			style="border: thin solid #FFFFFF; width: 100%; text-align: center; cellspacing: 8; cellpadding: 2"
			class="borde_tab">
			<tr>
				<td width="33%" height="25" align="center">
					<?php 
					if ($serieactu == "") {
					?>
    					<input name="insertar" type="button" class="botones_funcion" value=" Insertar "
    					onClick="insertarTrd('<?php echo $pqr;?>', '<?php echo $cambio;?>', '<?php echo $retipifica;?>', '<?php echo $tiporad;?>', '<?php echo $docuactu;?>', '<?php echo $serieactu;?>', '<?php echo $subseactu;?>');">
					<?php 
					}
					?>
				</td>
				<td width="33%" height="25">
					<?php 
					if ($serieactu != "") {
					?>
    					<input name="actualizar" type="button" class="botones_funcion" id="envia23"
    					onClick="procModificar('<?php echo $serieactu;?>', '<?php echo $tiporad;?>', '<?php echo $cambio;?>', '<?php echo $docuactu;?>', '<?php echo $retipifica;?>', '<?php echo $pqr;?>', '<?php echo $subseactu;?>');"
    					value=" Modificar ">
					<?php 
					}
					?>
				</td>
				<td width="33%" height="25">
					<input name="Cerrar" type="button" class="botones_funcion" id="envia22"	onClick="cerrar();" value="Cerrar">
				</td>
			</tr>
		</table>
		<br>

		<table style="width: 100%;">
			<thead>
				<tr class="titulo5" align="center">
					<td width="10%" class="titulos4">C&Oacute;DIGO</td>
					<td width="20%" class="titulos4">SERIE</td>
					<td width="20%" class="titulos4">SUBSERIE</td>
					<td width="20%" class="titulos4">TIPO DE DOCUMENTO</td>
					<td width="20%" class="titulos4">DEPENDENCIA</td>
					<td width="20%" class="titulos4">ACCI&Oacute;N</td>
				</tr>
			</thead>
			<tbody id="bodyClasifica">
			</tbody>
		</table>
<?php
// include_once $ruta_raiz . "/radicacion/lista_tiposAsignados.php";
if ($ind_ProcAnex == "S") {
    echo " <input type='button' value='Cerrar' class='botones_largo' onclick='cerrar();'> ";
}
?>
	<script type="text/javascript">

	if (<?php echo $serieactu;?>) {
		cargaSubserie();
    }
    if (<?php echo $subseactu;?>) {
    	cargaTipoDoc();
    }
	
	</script>
	</form>
</body>
</html>