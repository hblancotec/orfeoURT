<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

$ruta_raiz = "..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
// $db->conn->debug = true;
if (! $_SESSION['dependencia'])
    include "../rec_session.php";
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$verrad = "";
if ($orden_cambio == 1) {
    if (! $orderTipo) {
        $orderTipo = "desc";
    } else {
        $orderTipo = "";
    }
}
$encabezadol = $_SERVER['PHP_SELF'] . "?" . session_name() . "=" . session_id() . "&krd=$krd";
$linkPagina = $_SERVER['PHP_SELF'] . "?" . session_name() . "=" . session_id() . "&krd=$krd&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&fecha1=$fecha1&s_entrada=$s_entrada&s_salida=$s_salida&tipoDocumento=$tipoDocumento&tipoRadicado=$tipoRadicado&dependenciaSel=$dependenciaSel&s_ciudadano=$s_ciudadano&s_empresaESP=$s_empresaESP&s_oEmpresa=$s_oEmpresa&s_funcionario=$s_funcionario&palabra=$palabra&s_solo_nomb=$s_solo_nomb";
$encabezado = "" . session_name() . "=" . session_id() . "&krd=$krd";
$variables = "" . session_name() . "=" . session_id() . "&krd=$krd&n_nume_radi=$n_nume_radi&s_solo_nomb=$s_solo_nomb&s_entrada=$s_entrada&s_salida=$s_salida&tipoDocumento=$tipoDocumento&tipoRadicado=$tipoRadicado&dependenciaSel=$dependenciaSel&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&fecha1=$fecha1&orderTipo=$orderTipo&orderNo=";

$ss_TRAD_CODIDisplayValue = "Todos los Tipos (-1,-2,-3,-5, . . .)";
$ss_TDOC_CODIDisplayValue = "Todos los Tipos";
$ss_RADI_DEPE_ACTUDisplayValue = "Todas las Dependencias";
$HasParam = false;
$sWhere = "";
$usuario = $krd;
?>
<html>
<head>
    <title>Consultas</title>
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <link rel="stylesheet" href="Site.css" type="text/css">
    <link rel="stylesheet" href="../estilos/orfeo.css">
    <link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
	<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
	<script type="text/javascript" src="../js/jquery-3.5.1.js"></script>
	<script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>
	<!-- <script type="text/javascript" src="../js/jquery-ui.js"></script> -->
	<script type="text/javascript" src="../js/jquery.blockUI.js"></script> 
	<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/fixedColumns.dataTables.min.css">
	<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/dataTables.fixedColumns.min.js"></script>
		<style type="text/css" class="init">
	
        	div.dataTables_wrapper {
                width: 1000px;
                margin: 0 auto;
            }
            
        	th, td { white-space: nowrap; }
        	div.dataTables_wrapper {
        		margin: 0 auto;
        	}
        
        	div.container {
        		width: 80%;
        	}
    
    	</style>
<script>

  function limpiar()
		{
	   document.Search.elements['n_nume_radi'].value = "";
	   document.Search.elements['palabra'].value = "";
	   document.Search.elements['dependenciaSel'].value = "99999";
	   document.Search.elements['tipoDocumento'].value = "9999";
	   document.Search.elements['s_entrada'].checked=1;
       document.Search.elements['s_salida'].checked=1;	 
       document.Search.s_solo_nomb[0].checked = true; 	
 	   }

  function busqueda() {
	  	var tipoRadicado = document.getElementById('tipoRadicado').value;
		var tipoDocumento = document.getElementById('tipoDocumento').value;
		var dependencia = document.getElementById('dependenciaSel').value;
		var radicado = document.getElementById('n_nume_radi').value;
		var palabra = document.getElementById('palabra').value;
		var solo_nomb = document.Search.s_solo_nomb.value;
		var fechaini = document.getElementById('fecha_ini').value;
		var fechafin = document.getElementById('fecha_fin').value;
				
		var divGri = document.getElementById('bodyList');
		if (divGri != null) {
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
			
			var parametros = {
    			'tipoRadicado': tipoRadicado,
    			'tipoDocumento': tipoDocumento,
    			'dependencia': dependencia,
    			'radicado': radicado,
    			'palabra': palabra,
    			'solo_nomb': solo_nomb,
    			'fechaini': fechaini,
    			'fechafin': fechafin,
    			'busquedaHis': 1
    		};		
    		
			$.ajax({
				url: 'listadoBusqueda.php',
				type: 'POST',
				cache: false,
				data: parametros,
				success: function(text) {
					if(text.length > 1) {
						divGri.innerHTML = '';
						divGri.innerHTML = text;

						var table = "";
						if ( $.fn.dataTable.isDataTable( '#grid' ) ) {
							table = $('#grid').DataTable();
							table.destroy();
							genDatatable();
						} else {
							genDatatable();
						}
						
					} else {
						divGri.innerHTML = '';
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
				}
			});
		}

		var divGriH = document.getElementById('bodyListH');
		if (divGriH != null) {				
			var parametros = {
    			'tipoRadicado': tipoRadicado,
    			'tipoDocumento': tipoDocumento,
    			'dependencia': dependencia,
    			'radicado': radicado,
    			'palabra': palabra,
    			'solo_nomb': solo_nomb,
    			'fechaini': fechaini,
    			'fechafin': fechafin,
    			'busquedaHisU': 1
    		};		
    		
			$.ajax({
				url: 'listadoBusqueda.php',
				type: 'POST',
				cache: false,
				data: parametros,
				success: function(text) {
					if(text.length > 1) {
						divGriH.innerHTML = '';
						divGriH.innerHTML = text;

						var table = "";
						if ( $.fn.dataTable.isDataTable( '#gridH' ) ) {
							table = $('#gridH').DataTable();
							table.destroy();
							genDatatableH();
						} else {
							genDatatableH();
						}
					} else {
						divGriH.innerHTML = '';
					}
					$.unblockUI();
				},
				error: function (jqXHR, textStatus, errorThrown) {
					$.unblockUI();
					alert(jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
				}
			});
		}
	}

	function genDatatable() 
	{
		$('#grid').removeAttr('width').DataTable( {
          paging:   true,
          ordering: true,
          info:     true,
          scrollY:  "600px",
          scrollX: true,
          scrollCollapse: true,
          pagingType: "full_numbers",
          fixedColumns: false,
          columnDefs: [
              { width: 50, targets: 0 }
          ],
          language: {
              "lengthMenu": "Mostrando _MENU_ registros por página",
              "zeroRecords": "No hay registros",
              "info": "Mostrando página _PAGE_ de _PAGES_",
              "infoEmpty": "No hay registros disponibles",
              "infoFiltered": "(Filtrado de _MAX_ registros totales)",
              "search":         "Filtrar:",
              "paginate": {
                  "first":      "Primero",
                  "last":       "Último",
                  "next":       "Siguiente",
                  "previous":   "Anterior"
              }
          }
      } );
	}

	function genDatatableH() 
	{
		$('#gridH').removeAttr('width').DataTable( {
          paging:   true,
          ordering: true,
          info:     true,
          scrollY:  "600px",
          scrollX: true,
          scrollCollapse: true,
          pagingType: "full_numbers",
          fixedColumns: false,
          columnDefs: [
              { width: 50, targets: 0 }
          ],
          language: {
              "lengthMenu": "Mostrando _MENU_ registros por página",
              "zeroRecords": "No hay registros",
              "info": "Mostrando página _PAGE_ de _PAGES_",
              "infoEmpty": "No hay registros disponibles",
              "infoFiltered": "(Filtrado de _MAX_ registros totales)",
              "search":         "Filtrar:",
              "paginate": {
                  "first":      "Primero",
                  "last":       "Último",
                  "next":       "Siguiente",
                  "previous":   "Anterior"
              }
          }
      } );
	}
	
	function seguridad(radicado, ruta) {
		var parametros = {
				'radicado': radicado.toString(),
				'ruta': ruta
			};
					
			$.ajax({
				url: '../validarSeguridad.php',
				type: 'POST',
				cache: false,
				data: parametros,
				success: function(text) {
					if(text.length > 1) {

						var filename = text.split('\\').pop().split('/').pop(); 
						var link = document.createElement("a");
					    link.download = filename;
					    link.href = text;
					    link.click();
		
					} else if(text.length == 1) {	
						alert ("NO SE ENCUENTRA EL ARCHIVO PARA EL RADICADO No. " + radicado.toString());
					} else {
						alert ("NO TIENE PERMISOS PARA ACCEDER AL RADICADO No. " + radicado.toString());
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
				}
			});
		
	}
</script>
</head>
<body class="PageBODY" topmargin="0" onLoad="window_onload();">
	<div id="spiffycalendar" class="text"></div>
  <?php
        $ano_ini = date("Y");
        $mes_ini = substr("00" . (date("m") - 1), - 2);
        if ($mes_ini == 0) {
            $ano_ini == $ano_ini - 1;
            $mes_ini = "12";
        }
        $dia_ini = date("d");
        if (! $fecha_ini)
            $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
        if (! $fecha1)
            $fecha1 = $fecha_ini;
        $fecha_busq = date("Y/m/d");
        if (! $fecha_fin)
            $fecha_fin = $fecha_busq;
?>
<script language="javascript"><!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "Search", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
  var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "Search", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);
//--></script>
	<table>
		<tr>
			<td valign="top">
				<form method="post" action="<?=$encabezadol?>" name="Search">
					<input type="hidden" name="FormName" value="Search">
					<input type="hidden" name="FormAction" value="search">
					<table class="FormTABLE">
					<tr>
							<td class="titulos4" colspan="13"><a name="Search">Busqueda por Hist&oacute;rico</a></td>
					</tr>
					<tr>
						<td class="titulos5">Radicado</td>
						<td class="listado5"><input class="tex_area" type="text" id="n_nume_radi" name="n_nume_radi" maxlength="" value="<?=$n_nume_radi?>" size=""></td>
					</tr>
					<tr>
						<td class="titulos5">
							<INPUT type="radio" NAME="s_solo_nomb" value="All" CHECKED <?php if($s_solo_nomb=="All"){ echo ("CHECKED");} ?>>Todas las palabras (y)<br> 
							<INPUT type="radio" NAME="s_solo_nomb" value="Any" <?php if($s_solo_nomb=="Any"){echo ("CHECKED");} ?>>Cualquier palabra (o)<br>
						</td>
						<td class="titulos5">
							<input class="tex_area" type="text" id="palabra" name="palabra" maxlength="70" value="<?=$palabra?>" size="70">
						</td>
					</tr>
					<tr>
						<td colspan="2" class="titulos5">
							<table>
							<tbody>
							<tr>
								<td class="titulos5">Buscar en Radicados de</td>
								<td class="listado5">
                               	<?php
                                    $rs = $db->conn->Execute('select SGD_TRAD_DESCR, SGD_TRAD_CODIGO  from SGD_TRAD_TIPORAD order by 2');
                                    $nmenu = "tipoRadicado";
                                    $valor = "9999";
                                    $default_str = $tipoRadicado;
                                    print $rs->GetMenu2($nmenu, $default_str, $blank1stItem = "$valor:$ss_TRAD_CODIDisplayValue", false, 0, 'id="tipoRadicado" class="select"');
                                ?>
             					</td>
             				</tr>
							</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td class="titulos5">Desde Fecha (dd/mm/yyyy)</td>
						<td class="listado5">
							<script language="javascript">
		        				dateAvailable.writeControl();
			    				dateAvailable.dateFormat="yyyy/MM/dd";
    	  					</script>
    	  				</td>
					</tr>
					<tr>
						<td class="titulos5">Hasta Fecha (dd/mm/yyyy)</td>
						<td class="listado5">
							<script language="javascript">
		        				dateAvailable1.writeControl();
			    				dateAvailable1.dateFormat="yyyy/MM/dd";
    	  					</script>
    	  				</td>
					</tr>
					<tr>
						<td class="titulos5">Tipo de Documento</td>
						<td class="listado5">
            				<?php
                            $rs = $db->conn->Execute('select SGD_TPR_DESCRIP, SGD_TPR_CODIGO from SGD_TPR_TPDCUMENTO order by 1');
                            $nmenu = "tipoDocumento";
                            $valor = "9999";
                            $default_str = $tipoDocumento;
                            print $rs->GetMenu2($nmenu, $default_str, $blank1stItem = "$valor:$ss_TDOC_CODIDisplayValue", false, 0, 'id="tipoDocumento" class="select"');
                            ?>
		            	</td>
					</tr>
					<tr>
						<td class="titulos5">Dependencia Actual</td>
						<td class="listado5">
                           	<?php
                            $rs = $db->conn->Execute('select DEPE_NOMB , DEPE_CODI from DEPENDENCIA order by 1');
                            $nmenu = "dependenciaSel";
                            $valor = "99999";
                            $default_str = $dependenciaSel;
                            print $rs->GetMenu2($nmenu, $default_str, $blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0, 'id="dependenciaSel" class="select"');
                            ?>
             			</td>
					</tr>
					<tr>
						<td align="right" colspan="3">
							<input name="button" type="button" class="botones" onClick="limpiar();" value="Limpiar"> 
							<input name="submit" type="button" class="botones" value="Busqueda" onclick="busqueda();">
						</td>
					</tr>
					</table>
				</form>
			</td>
			<td valign="top">
				<a class="vinculos" href="../busqueda/busquedaPiloto.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda Cl&aacute;sica</a><br> 
				<a class="vinculos" href="../busqueda/busquedaUsuActu.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "&fechah=$fechah&primera=1&ent=2"; ?>">Reporte por Usuarios</a><br>
				<a class="vinculos" href="../busqueda/busquedaExp.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda Expediente</a>
			</td>
		</tr>
	</table>
	<table>
	<tr>
		<td valign="top">
			<table class="FormTABLE">
    			<tr>
    				<td class="titulos5" colspan="20"><a name="RADICADO">Radicados en los que aparezco como actual</a></td>
    			</tr>
			</table>
			<div align="center">
        		<table id="grid" class="display nowrap stripe row-border order-column">
            		<thead>
                		<tr>
                			<th class=titulos3>Radicado</th>
                			<th class=titulos3>Fecha Radicado</th>
                			<th class=titulos3>Nombre</th>
                			<th class=titulos3>Apellido 1</th>
                			<th class=titulos3>Apellido 2</th>
                			<th class=titulos3>Identificacion</th>
                		</tr>
            		</thead>
            		<tbody id="bodyList">
            							
            		</tbody>
            	</table>
    		</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top">
			<table class="FormTABLE">
				<tr>
					<td class="titulos5" colspan="20"><a name="RADICADO">Radicados en los que aparezco como Hist&oacute;rico</a></td>
				</tr>
			</table>
			<div align="center">
        		<table id="gridH" class="display nowrap stripe row-border order-column">
            		<thead>
                		<tr>
                			<th class=titulos3>Radicado</th>
                			<th class=titulos3>Fecha Radicado</th>
                			<th class=titulos3>Dependencia Actual</th>
                			<th class=titulos3>Nombre</th>
                			<th class=titulos3>Apellido 1</th>
                			<th class=titulos3>Apellido 2</th>
                			<th class=titulos3>Identificacion</th>
                		</tr>
            		</thead>
            		<tbody id="bodyListH">
            							
            		</tbody>
            	</table>
    		</div>
		</td>
	</tr>
	</table>
</body>
</html>