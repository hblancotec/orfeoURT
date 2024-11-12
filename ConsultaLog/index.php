<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit;
}
else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    $krd = $_SESSION["login"];
}

$ruta_raiz = "..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;

### SE INCLUYE ARCHIVO EN DONDE SE ARMAN LOS REPORTES EN EXCEL
require ("$ruta_raiz/include/rs2xml.php");
$obj = new rs2xml();

if($_POST['tipo'] && $_POST['num']){
	$tipo = $_POST['tipo'];
	$nume = $_POST['num'];
	$fecIni = $_POST['fecha_ini'];
	
	$fecFin = strtotime ( '+1 day' , strtotime ($_POST['fecha_fin']) ) ;
	$fecFin = date ( 'Y-m-j' , $fecFin );
}

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Orfeo - Log de Consultas.</title>
		<link rel="stylesheet" type="text/css" href="../estilos/orfeo.css">
		
		<div id="spiffycalendar" class="text"></div>
		<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
		<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
		
		<script type="text/javascript" src="../js/jquery-3.5.1.js"></script>
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="../lib/DataTables/DataTables-1.10.21/css/fixedColumns.dataTables.min.css">
		<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" charset="utf8" src="../lib/DataTables/DataTables-1.10.21/js/dataTables.fixedColumns.min.js"></script>
		
		<style type="text/css">
	
	       .tabla {
              margin: 0 auto;
              width: 100%;
              clear: both;
              border-collapse: collapse;
              table-layout: fixed;
              word-wrap:break-word;
              text-align: center;
            }
            
            
        	div.dataTables_wrapper {
                width: 1300px;
                margin: 0 auto;
            }
            
            th.dt-center, td.dt-center { text-align: center; }
        	/*th, td { white-space: nowrap; }*/
        	div.dataTables_wrapper {
        		margin: 0 auto;
        	}
        
        	div.container {
        		width: 80%;
        	}
    
    	</style>
		
		<script language="javascript">
			<?php
				$mesActual = date("m");
				$ano_ini = ($mesActual == 1) ? date("Y") - 1 : date("Y");
				$mes_ini = substr("00".(date("m")-1),-2);
				if ($mes_ini==0){
					$ano_ini==$ano_ini-1;
					$mes_ini="12";
				}
				$dia_ini = date("d");
				if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
					$fecha_busq = date("Y/m/d");
				if(!$fecha_fin) 
					$fecha_fin = $fecha_busq;
			?>

			var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formLog", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
			var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formLog", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);
		</script>
		
		<script>
			function validarConsulta(){
				
				//SE VALIDA QUE EN EL CAMPO TIPO, SE HALLA SELECCIONADO UNA OPCION
				if (document.formLog.tipo.value == 0){
					alert("Debe seleccionar el tipo de consulta")
					document.formLog.tipo.focus()
					return 0;
				}
				
				//SE VALIDA QUE EL NUMERO DIGITADO SEA DE 14 DIGITOS SI SELECCIONO RADICADO
				//O DE 19 DIGITOS SI SELECCIONO EXPEDIENTE
				if (document.formLog.tipo.value == 1){	
					if (document.formLog.num.value.length != 14){
						alert("La longitud valida de radicados son 14 digitos, por favor verifique e intente de nuevo")
						document.formLog.num.focus()
						return 0;
					}
				}	
				else if(document.formLog.tipo.value == 2){	
					if(document.formLog.num.value.length != 19){
						alert("La longitud valida para expedientes es de 19 digitos, por favor verifique e intente de nuevo")
						document.formLog.num.focus()
						return 0;
					}
				}
				
				document.formLog.submit();
			}
		</script>
	</head>
	<body>
		<form name="formLog" method="post" action="index.php">
			<table width="40%" border="0" align="center" cellspacing="5" class="borde_tab">
				<tr>
					<td colspan="2" height="40" align="center" class="titulos4" style="font-size: 14">
						<b> LOG DE CONSULTAS </b>
					</td>
				</tr>
				
				<tr> 
					<td width="35%" align="left" class="listado2" style="font-size: 12">
						1. Seleccione tipo de consulta
					</td>
					<td>
						<select name="tipo" id="tipo" > 
							<option value="0"> Seleccione el tipo ---</option> 
							<option value="1"> Por Radicado </option> 
							<option value="2"> Por Expediente </option>  
						</select>
					</td>
				</tr>
				
				<tr bordercolor="#FFFFFF"> 
					<td width="35%" align="left" class="listado2" style="font-size: 12">
						2. Digite el n&uacute;mero
					</td>
					<td>
						<input name="num" id="num" type="text" size="21" maxlength="19">
					</td>
				</tr>
				
				<tr> 
					<td width="35%" align="left" class="listado2" style="font-size: 12">
						3. Seleccione rango de fecha
					</td>
					<td>
						<script language="javascript">
							dateAvailable.writeControl();
							dateAvailable.dateFormat="yyyy/MM/dd";
						</script>
							&nbsp;&nbsp;&nbsp;
						<script language="javascript">
							dateAvailable2.writeControl();
							dateAvailable2.dateFormat="yyyy/MM/dd";
						</script>
					</td>
				</tr>
				<tr>
					<td class="etextomenu" colspan="2" align="center">
						<input type="button" class="botones" value="Consultar" onclick="validarConsulta()">
					</td>
				</tr>
			</table>
		</form>
		

<?php
if($tipo == 1 or $tipo == 2){
	
	### CONSULTA DE RADICADO
	if($tipo == 1){
		$consulta ="SELECT	L.FECHA AS FECHA,
							L.RADI_NUME_RADI AS RADICADO,
							CASE U.SGD_ROL_CODIGO 
								WHEN 1 THEN 'JEFE'
								WHEN 2 THEN 'JEFE ENCARGADO'
								WHEN 3 THEN 'AUDITOR'
								ELSE 'NORMAL' END AS ROL,
							U.USUA_NOMB AS USUARIO,
							D.DEPE_NOMB AS DEPENDENCIA,
							CASE 
								WHEN L.HIST_CON_MODULO LIKE '%verradicado.php?%' THEN 'Cambio de pestana'
								WHEN L.HIST_CON_MODULO LIKE '%cuerpo.php?%' THEN 'Carpetas'
								WHEN L.HIST_CON_MODULO LIKE '%busquedaPiloto.php?%' THEN 'Consultas'
								WHEN L.HIST_CON_MODULO LIKE '%genEstadistica.php?%' THEN 'Estadisticas'
								ELSE 'Otro' END AS MODULO
					FROM	SGD_HIST_CONSULTAS L
							JOIN USUARIO U ON
								U.USUA_LOGIN = L.USUA_LOGIN AND U.USUA_DOC = L.USUA_DOC
							JOIN DEPENDENCIA D ON
								D.DEPE_CODI = U.DEPE_CODI
					WHERE	L.RADI_NUME_RADI = $nume AND
							L.FECHA BETWEEN '$fecha_ini' AND '$fecha_fin'
					ORDER BY L.FECHA";
	}
	### CONSULTA DE EXPEDIENTE
	elseif($tipo == 2){
		$consulta ="SELECT	L.FECHA AS FECHA,
							L.SGD_EXP_NUMERO AS EXPEDIENTE,
							L.RADI_NUME_RADI AS RADICADO,
							CASE U.SGD_ROL_CODIGO 
								WHEN 1 THEN 'JEFE'
								WHEN 2 THEN 'JEFE ENCARGADO'
								WHEN 3 THEN 'AUDITOR'
								ELSE 'NORMAL' END AS ROL,
							U.USUA_NOMB AS USUARIO,
							D.DEPE_NOMB AS DEPENDENCIA,
							CASE 
								WHEN L.HIST_CON_MODULO LIKE '%verradicado.php?%' THEN 'Cambio de pestana'
								WHEN L.HIST_CON_MODULO LIKE '%cuerpo.php?%' THEN 'Carpetas'
								WHEN L.HIST_CON_MODULO LIKE '%busquedaPiloto.php?%' THEN 'Consultas'
								WHEN L.HIST_CON_MODULO LIKE '%genEstadistica.php?%' THEN 'Estadisticas'
								WHEN L.HIST_CON_MODULO LIKE '%consultaExp.php?%' THEN 'Boton Expediente'
								WHEN L.HIST_CON_MODULO LIKE '%consultaExpOcad.php?%' THEN 'Boton Expediente Ocad'
								WHEN L.HIST_CON_MODULO LIKE '%consultaExpCont.php?%' THEN 'Boton Expediente Contratos'
								ELSE 'Otro' END AS MODULO
					FROM	SGD_HIST_CONSULTAS L
							JOIN USUARIO U ON
								U.USUA_LOGIN = L.USUA_LOGIN AND U.USUA_DOC = L.USUA_DOC
							JOIN DEPENDENCIA D ON
								D.DEPE_CODI = U.DEPE_CODI
					WHERE	L.SGD_EXP_NUMERO = '$nume' AND
							L.FECHA BETWEEN '$fecha_ini' AND '$fecha_fin'
					ORDER BY L.FECHA";
	}
	
	$rs = $db->conn->Execute($consulta);
	
	if ($rs) {
		//$path = "../bodega/tmp/logConsultaRad".date("dmYh").time("his").".csv";

		$Rs2Xml = $obj->getXML($rs);
		$archivo = $krd."_".rand(10000, 20000);
		$path = "../bodega/tmp/$archivo.xls";
		$fp = fopen($path, "w");
		if ($fp) {
			fwrite($fp, $Rs2Xml);
			fclose($fp);
		}
		
		$rs = $db->conn->Execute($consulta);
	}
	else{
		$resultado = 'Hubo un error en cración del reporte.';
	}
	
	echo "<br><hr>";
	echo "&nbsp; <a href='$path' target='_blank'> Exportar reporte </a> ";	
		
?>
		<br><br>
		<table width="100%" border="0" align="center" cellspacing="5" class="borde_tab">
			<tr>
				<td colspan="2" height="40" align="center" class="titulos4" style="font-size: 14">
					<b> RESULTADO DE LA B&Uacute;SQUEDA </b>
				</td>
			</tr>
		</table>

	<table id="grid" style="width:100%" class="tabla hover stripe order-column cell-border compact">
		<thead>
    		<tr>
     			<th class="titulos5">FECHA</th>
    			<th class="titulos5">RADICADO</th>
    			<th class="titulos5">ROL</th>
    			<th class="titulos5">USUARIO</th>
    			<th class="titulos5">DEPENDENCIA</th>
    			<th class="titulos5">MODULO</th>
    		</tr>
		</thead>
		<tbody>
		<?php 
	    while(!$rs->EOF && $rs) {
	    ?>
	        <tr>
	        	<td class="leidos"><?= $rs->fields['FECHA']; ?></td>
    			<td class="leidos"> <?= $rs->fields['RADICADO']; ?></td>
    			<td class="leidos">  <?= $rs->fields['ROL']; ?></td>
    			<td class="leidos"> <?= $rs->fields['USUARIO']; ?></td>
    			<td class="leidos"> <?= $rs->fields['DEPENDENCIA']; ?></td>
    			<td class="leidos"> <?= $rs->fields['MODULO']; ?></td>
	        </tr>
	    <?php
	       $rs->MoveNext();
	    }
		?>
		</tbody>
	</table>
	
	<script languaje="JavaScript">

        	$(document).ready(function() {
        		var table = $('#grid').removeAttr('width').DataTable( {
                    paging:   true,
                    ordering: true,
                    info:     true,
                    scrollY:  "600px",
                    scrollX: true,
                    scrollCollapse: true,
                    autoWidth: false,
                    fixedColumns: true,
                    fixedHeader: {
                        "header": true,
                        "footer": false
                    },
                    columnDefs: [
                      { "width": "120px", "targets": 0 },
                      { "width": "110px", "targets": 1 },
                      { "width": "70px", "targets": 2 },
                      { "width": "170px", "targets": 3 },
                      { "width": "220px", "targets": 4 },
                      { "width": "100px", "targets": 5 }
                    ],
                    language: {
                        "lengthMenu": "Mostrando _MENU_ registros por p\u00E1gina",
                        "zeroRecords": "No hay registros",
                        "info": "Mostrando p\u00E1gina _PAGE_ de _PAGES_",
                        "infoEmpty": "No hay registros disponibles",
                        "infoFiltered": "(Filtrado de _MAX_ registros totales)",
                        "search":         "Filtrar:",
                        "paginate": {
                            "first":      "Primero",
                            "last":       "\u00DAltimo",
                            "next":       "Siguiente",
                            "previous":   "Anterior"
                        }
                    }
                } );
        	} );

    </script>
<?php

    //require_once "adodb/adodb-paginacion.inc.php";
	/*$pager = new ADODB_Paginacion($db, $consulta, 'adodb', true, 1);
	$pager->checkAll = false;
	$pager->checkTitulo = false;
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	$pager->Render(20, null);*/
}
?>
	</body>
</html>