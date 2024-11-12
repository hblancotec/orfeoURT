<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    $krd = $_SESSION["login"];
} else {
    $krd = $_REQUEST['krd'];
}

$verrad = "";
$ruta_raiz = "..";

include_once ("$ruta_raiz/_conf/constantes.php");
require_once (ORFEOPATH . "include/db/ConnectionHandler.php");

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$proyec = (empty($_POST['proyec'])) ? $_GET['proyec'] : $_POST['proyec'];
$fecha_ini = (empty($_POST['fecha_ini'])) ? $_GET['fecha_ini'] : $_POST['fecha_ini'];
$fecha_fin = (empty($_POST['fecha_fin'])) ? $_GET['fecha_fin'] : $_POST['fecha_fin'];
$orderTipo = $_GET['orderTipo'];

$ano_ini = date("Y");
$mes_ini = substr("00" . (date("m") - 1), - 2);

if ($mes_ini == 0) {
    $ano_ini == $ano_ini - 1;
    $mes_ini = "12";
}

$dia_ini = date("d");
if (! $fecha_ini)
    $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";

$fecha_busq = date("Y/m/d");
if (! $fecha_fin) {
    $fecha_fin = $fecha_busq;
}

/*if (trim($orderTipo) == "")
    $orderTipo = "ASC";
if ($orden_cambio == 1) {
    if (trim($orderTipo) != "DESC") {
        $orderTipo = "DESC";
    } else {
        $orderTipo = "ASC";
    }
}

if (strlen($orderNo) == 0) {
    $orderNo = "1";
    $order = 1;
} else {
    $order = substr($orderNo, - 1) + 1;
}*/
?>
<html>
<head>
    <title>Consultas</title>
    <link rel="stylesheet" href="Site.css" type="text/css">
    <link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
	<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
	<script type="text/javascript" src="../js/jquery-3.5.1.js"></script>
		<script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="../js/jquery.blockUI.js"></script> 
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/fixedColumns.dataTables.min.css">
		<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/dataTables.fixedColumns.min.js"></script>

		<style type="text/css" class="init">
	
        	div.dataTables_wrapper {
                width: 1200px;
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
	<script language="javascript">
            <!--
            var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "Search", "fecha_ini", "btnDate1", "<?=$fecha_ini?>", scBTNMODE_CUSTOMBLUE);
            var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "Search", "fecha_fin", "btnDate2", "<?=$fecha_fin?>", scBTNMODE_CUSTOMBLUE);
            //-->
            
            
    	function busqueda() {
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
						
				var proyecto = document.getElementById('proyec').value;
				var fechaini = document.getElementById('fecha_ini').value;
				var fechafin = document.getElementById('fecha_fin').value;
					
				var parametros = {
					'proyecto': proyecto,
					'fechaini': fechaini,
					'fechafin': fechafin,
					'busquedaExp': 1
				};
						
				$.ajax({
					url: 'listadoBusqueda.php',
					type: 'POST',
					cache: false,
					data: parametros,
					success: function(text) {
						debugger;
						if(text.length > 1) {
							divGri.innerHTML = '';
							divGri.innerHTML = text;

							var table = "";
							if ( $.fn.dataTable.isDataTable( '#grid' ) ) {
								table = $('#grid').DataTable();
								table.destroy();
								genDatatable(table);
							} else {
								genDatatable(table);
							}
						} else {
							divGri.innerHTML = '';
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

    	function genDatatable(table) 
    	{
    		table = $('#grid').removeAttr('width').DataTable( {
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
    	function seguridad(radicado) {
			debugger;

			var parametros = {
					'radicado': radicado.toString()
				};
						
				$.ajax({
					url: '../validarSeguridad.php',
					type: 'POST',
					cache: false,
					data: parametros,
					success: function(text) {
						debugger;
						if(text.length > 1) {
							window.location=text;							
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
<body >
	<div id="spiffycalendar" class="text"></div>
	<form name="Search" action="<?=$encabezado?>" method="post">
		<table>
			<tr>
				<td class="titulos4" colspan="2" valign="top">Expedientes y proyectos</td>
				<td valign="top">
    				<a class="vinculos" href="../busqueda/busquedaPiloto.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda Cl&aacute;sica</a><br> 
    				<a class="vinculos" href="../busqueda/busquedaUsuActu.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "&fechah=$fechah&primera=1&ent=2"; ?>">Reporte por Usuarios</a><br>
    				<a class="vinculos" href="../busqueda/busquedaExp.php?<?=$phpsession ?>&krd=<?=$krd?>&<?php echo "&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda Expediente</a>
				</td>
			</tr>
			<tr>
				<td class="titulos5">Buqueda por Proyectos</td>
				<td class="listado5">
                        <?php
                        $isqlt = "	SELECT P.SGD_EPRY_NOMBRE_CORTO, P.SGD_EPRY_CODIGO FROM SGD_EPRY_EPROYECTO P order by 1";
                        $rs = $db->conn->execute($isqlt);
                        print $rs->GetMenu2('proyec', $proyec, '0: [-Todos los proyectos-]', FALSE, 5, 'id="proyec" class="select"');
                        ?>                                        	              
                    </td>
                <td>&nbsp;</td>
			</tr>
			<tr>
				<td class="titulos5">Desde Fecha (yyyy/mm/dd)</td>
				<td class="listado5"><script language="javascript">
                            dateAvailable.writeControl();
                            dateAvailable.dateFormat = "yyyy/MM/dd";
                        </script></td>
                <td>&nbsp;</td>
			</tr>
			<tr>
				<td class="titulos5">Hasta Fecha (yyyy/mm/dd)</td>
				<td class="listado5"><script language="javascript">
                            dateAvailable1.writeControl();
                            dateAvailable1.dateFormat = "yyyy/MM/dd";
                        </script></td>
                <td>&nbsp;</td>
			</tr>
			<tr>
				<td class="titulos5" colspan="2" align="right">
				<input class="botones" id="Busqueda" name="Busqueda" value="Busqueda" type="button" onclick="busqueda();"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="titulos5" colspan="3" >
					<table id="grid" class="display stripe row-border order-column">
                		<thead>
                    		<tr>
                    			<th class=titulos3>Numero Expediente</th>
                    			<th class=titulos3>TRD</th>
                    			<th class=titulos3>Fecha Expediente</th>
                    			<th class=titulos3>Radicado Origen</th>
                    			<th class=titulos3>Nombre 1</th>
                    			<th class=titulos3>Nombre 2</th>
                    			<th class=titulos3>Nombre 3</th>
                    			<th class=titulos3>Nombre 4</th>
                    			<th class=titulos3>Nombre 5</th>
                    		</tr>
                		</thead>
                		<tbody id="bodyList">
                							
                		</tbody>
                	</table>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>