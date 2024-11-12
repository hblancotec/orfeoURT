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
$krdOld = $krd;

if (! $krd)
    $krd = $krdOsld;
$ruta_raiz = "..";

if (! $carpeta) {
    $carpeta = $carpetaOld;
    $tipo_carp = $tipoCarpOld;
}
$verrad = "";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug = true;
// echo ">>>> $s_EXPEDIENTE";
if (! $tipo_archivo)
    $tipo_archivo = 0; // Para la consulta a archivados

/**
 * *******************************************************************************
 * Filename: prestamo.php
 * Modificado:
 * 1/3/2006 IIAC Basado en pedido.php.
 * Facilita la b�squeda de los
 * registros de pr�stamo.
 * *******************************************************************************
 */

// ===============================
// prestamo begin
// ===============================
// Inicializa, oculta o presenta los par�metros de b�squeda dependiendo de la opcion del menu de pr�stamos seleccionada

// prestamo CustomIncludes begin
include "common.php";
// Save Page and File Name available into variables
$sFileName = "prestamo.php";
// Variables de control
$opcionMenu = strip(get_param("opcionMenu")); // opcion seleccionada del menu
$pageAnt = strip(get_param("sFileName"));
$ver = get_param("s_sql"); // consulta
                           // HTML Page layout
?>
<html>
<head>
<title>Pr&eacute;stamos ORFEO</title>
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css"
	type="text/css">
	
<link rel="stylesheet" href="../estilos/theme.default.min.css" type="text/css">
<link rel="stylesheet" href="../estilos/addons/pager/jquery.tablesorter.pager.css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-3.4.1.js"></script>
<script type="text/javascript" src="../js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="../js/jquery.tablesorter.widgets.js"></script>
<script src="../estilos/addons/pager/jquery.tablesorter.pager.js"></script>
	
<!--Necesario para hacer visible el calendario -->
<script src="<?=$ruta_raiz?>/js/popcalendar.js"></script>
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css"
	href="<?=$ruta_raiz?>/js/spiffyCal/spiffyCal_v2_1.css">
</head>
<body class="PageBODY">
	<div align="center">
		<table>
			<tr>
				<td valign="top"><?php Search_Show(); ?></td>
			</tr>
		</table>
		<table>
			<tr>
				<td valign="top"><?php if($ver=="") Pedidos_Show(); ?></td>
			</tr>
		</table>
	</div>
</body>
</html>
<?php

// ===============================
// prestamo end
// ===============================
// ===============================
// Search_Show begin
// ===============================
function Search_Show()
{
    // De sesion
    global $db;
    global $krd; // usuario actual
    global $dependencia; // dependencia del usuario actual
    global $ruta_raiz;
    // Control de visualizacion
    global $sFileName;
    global $opcionMenu;
    // Valores
    global $fechaFinal;
    global $fechaInicial;
    global $s_EXPEDIENTE;
    global $reserva;
    // Inicializacion de la fecha a partir de la cual se cancelan las solicitudes
    
    $fechaInicial = strip(get_param("fechaInicial"));
    $fechaFinal = strip(get_param("fechaFinal"));
    
    if ($fechaInicial == "") {
        $hastaXDias = strtotime("-30 day");
        $fechaInicial = date("d-m-Y", $hastaXDias);
    }
    if ($fechaFinal == "") {
        if ($opcionMenu == 3) {
            $query = "select PARAM_VALOR,PARAM_NOMB from SGD_PARAMETRO where PARAM_NOMB='PRESTAMO_DIAS_CANC'";
            $rs = $db->conn->Execute($query);
            if (! $rs->EOF) {
                $x = $rs->fields("PARAM_VALOR"); // dias por defecto
                $haceXDias = strtotime("-" . $x . " day");
                $fechaFinal = date("d-m-Y", $haceXDias);
            }
            if ($pageAnt != $sFileName) { // inicializacion del tiempo
                $v_hora_limite = date("h");
                $v_minuto_limite = date("i");
                $v_meridiano = date("A");
            }
        } else {
            $fechaFinal = date("d-m-Y");
        }
    }
    // Set variables with search parameters
    $flds_PRES_ESTADO = strip(get_param("s_PRES_ESTADO"));
    $fechaHoy = $db->conn->OffsetDate(0);
    $flds_RADI_NUME_RADI = strip(get_param("s_RADI_NUME_RADI"));
    $flds_EXPEDIENTE = strip($_POST["s_EXPEDIENTE"]);
    // ECHO "**".$flds_EXPEDIENTE;
    $flds_USUA_LOGIN = strip(get_param("s_USUA_LOGIN"));
    // Inicializa el usuario para el caso en que el ingresa por la opcion de SOLICITADOS
    /*if ($opcionMenu == 4) {
        $flds_USUA_LOGIN = $krd;
    }*/
    $flds_DEPE_NOMB = strip(get_param("s_DEPE_NOMB"));
    $flds_USUA_NOMB = strip(get_param("s_USUA_NOMB"));
    // $flds_PRES_REQUERIMIENTO=strip(get_param("s_PRES_REQUERIMIENTO"));
    if ($v_hora_limite == "") {
        $v_hora_limite = strip(get_param("s_hora_limite"));
    }
    if ($v_minuto_limite == "") {
        $v_minuto_limite = strip(get_param("s_minuto_limite"));
    }
    if ($v_meridiano == "") {
        $v_meridiano = strip(get_param("s_meridiano"));
    }
    // Inicializa el titulo y la visibilidad de los criterios de b�squeda
    include_once "inicializarForm.inc";
    // Form display
    ?>
<form method="post" action="prestamo.php" name="busqueda">
	<!-- de sesion !-->
	<input type="hidden" value='<?=$krd?>' name="krd"> <input type="hidden"
		value=" " name="radicado"> <input type="hidden" value="" name="s_sql">
	<!-- control de visualizacion !-->
	<input type="hidden" name="opcionMenu" value="<?= $opcionMenu ?>"> <input
		type="hidden" name="sFileName" value="">
	<!-- orden de presentacion del resultado !-->
	<input type="hidden" name="FormPedidos_Sorting" value="1"> <input
		type="hidden" name="FormPedidos_Sorted" value="0"> <input
		type="hidden" name="s_Direction" value=" DESC ">
	<!-- control de paginacion !-->
	<input type="hidden" name="FormPedidos_Page" value="1"> <input
		type="hidden" name="FormStarPage" value="1"> <input type="hidden"
		name="FormSiguiente" value="0"> <input type="hidden"
		value="<?php echo $reserva ?>" name="reserva">
	<script>
//Inicializa el formulario
function limpiar() {
 document.busqueda.action="menu_prestamo.php";			   			   
 document.busqueda.submit();
} 	  
//Presenta los usuarios segun la dependencia seleccionada
var codUsuaSel="<?=$flds_USUA_NOMB?>";
</script>
	<!--Calendario-->
	<script language="JavaScript"
		src="<?=$ruta_raiz?>/js/spiffyCal/spiffyCal_v2_1.js"></script>
	<script language="javascript">
 setRutaRaiz ('<?=$ruta_raiz?>');				
</script>

	<table border=0 cellpadding=0 cellspacing=2 class='borde_tab'>
		<tr>
			<td class="titulos4" colspan="2"><a name="Search"><?=$sFormTitle[$opcionMenu]; ?></a></td>
		</tr>
		<tr id="b0" style="display:<?= $tipoBusqueda[$opcionMenu][0]; ?>">
			<td class="titulos3"><p align="left">Radicado</p></td>
     <?php
    if (! $flds_RADI_NUME_RADI)
        $flds_RADI_NUME_RADI = $_POST["radicado"];
    ?>
         <td class="listado5"><input type="text" name="s_RADI_NUME_RADI"
				maxlength="15" value="<?=trim($flds_RADI_NUME_RADI)?>" size="25"
				class="tex_area"></td>
		</tr>
		<tr id="b0" style="display:<?= $tipoBusqueda[$opcionMenu][0]; ?>">
			<td class="titulos3"><p align="left">Expediente</p></td>
     <?php
    if (! $flds_EXPEDIENTE)
        $flds_EXPEDIENTE = $_POST["expediente"];
    ?>
         <td class="listado5"><input type="text" name="s_EXPEDIENTE"
				maxlength="21" value="<?=trim($_POST["s_EXPEDIENTE"])?>" size="25"
				class="tex_area"></td>
		</tr>
		<tr id="b1" style="display:<?= $tipoBusqueda[$opcionMenu][1];?>">
			<td class="titulos3"><p align="left">Login de Usuario</p></td>
			<td class="listado5"><input type="text" name="s_USUA_LOGIN"
				maxlength="30" value="<?=trim($flds_USUA_LOGIN)?>" size="25"
				class="tex_area"></td>
		</tr>
		<tr id="b2" style="display:<?= $tipoBusqueda[$opcionMenu][2]; ?>">
			<td class="titulos3"><p align="left">Dependencia</p></td>
			<td class="listado5"><select name="s_DEPE_NOMB" class="select"
				onChange=" document.busqueda.s_sql.value='no'; document.busqueda.submit(); ">
					<option value="">- TODAS LAS DEPENDENCIAS -</option>			
<?php
    $lookup_s = db_fill_array("select DEPE_CODI,DEPE_NOMB from DEPENDENCIA order by 1");
    if (is_array($lookup_s)) {
        reset($lookup_s);
        foreach ($lookup_s as $key => $value) {
            if ($key == $flds_DEPE_NOMB) {
                $option = "SELECTED";
            } else {
                $option = "";
            }
            echo "<option $option value=\"$key\">" . $key . " - " . strtoupper($value) . "</option>";
        }
    }
    ?>
 </select></td>
		</tr>
		<tr id="b3" style="display:<?= $tipoBusqueda[$opcionMenu][3]; ?>">
			<td class="titulos3"><p align="left">Usuario</p></td>
			<td class="listado5"><select name="s_USUA_NOMB" class=select>
					<option value="">- TODOS LOS USUARIOS -</option>
<?php
    $validUsuaActiv = "";
    if ($opcionMenu == 1) {
        $validUsuaActiv = " USUA.USUA_LOGIN IS NOT NULL "; //USUA_ESTA=1
    } else {
        $validUsuaActiv = " USUA.USUA_LOGIN IS NOT NULL ";
    } // Verifica que el usuario se encuentre activo para hacer el pr�stamo
    if ($flds_DEPE_NOMB != "") {
        $usuaDepe = ", SGD_USD_USUADEPE USD";
        $tmp = "AND USUA.USUA_LOGIN = USD.USUA_LOGIN AND
          USUA.USUA_DOC = USD.USUA_DOC AND
          USD.DEPE_CODI= " . $flds_DEPE_NOMB;
    } else
        $tmp = "";
    $sqlUsd = "select USUA.USUA_LOGIN,USUA.USUA_NOMB
             from USUARIO USUA $usuaDepe
             where " . $validUsuaActiv . $tmp;
    $lookup_s = db_fill_array($sqlUsd);

    if (is_array($lookup_s)) {
        reset($lookup_s);
        foreach ($lookup_s as $key => $value) {
            if ($key == $flds_USUA_NOMB) {
                $option = "SELECTED";
            } else {
                $option = "";
            }
            echo "<option $option value=\"$key\">" . strtoupper($value) . "</option>";
        }
    }
    ?>
</select></td>
		</tr>
		<tr id="b5" style="display:<?= $tipoBusqueda[$opcionMenu][5]; ?>">
			<td class="titulos3"><p align="left">Estado</p></td>
			<td class="listado5"><select name="s_PRES_ESTADO" class=select>
					<option value="">- TODOS LOS ESTADOS -</option>			
<?php
    if ($reserva == 1) {
        $lookup_s = db_fill_array("select PARAM_CODI,PARAM_VALOR from SGD_PARAMETRO where PARAM_NOMB='PRESTAMO_ESTADO'
                AND (PARAM_CODI = 1 OR PARAM_CODI = 2) order by PARAM_VALOR");
    } else {
        $lookup_s = db_fill_array("select PARAM_CODI,PARAM_VALOR from SGD_PARAMETRO where PARAM_NOMB='PRESTAMO_ESTADO' order by PARAM_VALOR");
        ?><option value="">- TODOS LOS ESTADOS -</option>
<?php
    }
    if (is_array($lookup_s)) {
        reset($lookup_s);
        foreach ($lookup_s as $key => $value) {
            if ($key == $flds_PRES_ESTADO) {
                $option = "SELECTED";
            } else {
                $option = "";
            }
            echo "<option $option value=\"$key\">" . strtoupper($value) . "</option>";
        }
    }
    if ($flds_PRES_ESTADO == - 1) {
        $option = "SELECTED";
    } else {
        $option = "";
    }
    echo "<option $option value=\"-1\">VENCIDO</option>";
    ?>								                                        											 							 
  </select>
  <?php
    if (! $fechaFinal or $fechaFinal == "31-12-1969")
        $fechaFinal = date("d-m-Y");
    ?>
  </td>
		</tr>
		<tr id="b6" style="display:<?= $tipoBusqueda[$opcionMenu][6]; ?>">
			<td class="titulos3"><p align="left">
					Fecha inicial<br>&nbsp;&nbsp;(dd-mm-aaaa)
				</p></td>
			<td class="listado5"><script language="javascript">
	
    var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "busqueda","fechaInicial","btnDate1","<?=$fechaInicial?>",scBTNMODE_CUSTOMBLUE);
    dateAvailable1.writeControl();
    dateAvailable1.dateFormat="dd-MM-yyyy";
   </script></td>
		</tr>
		<tr id="b7" style="display:<?= $tipoBusqueda[$opcionMenu][7]; ?>">
			<td class="titulos3"><p align="left">
					Fecha final<br>&nbsp;&nbsp;(dd-mm-aaaa)
				</p></td>
			<td class="listado5"><script language="javascript">
    var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "busqueda","fechaFinal","btnDate2","<?=$fechaFinal?>",scBTNMODE_CUSTOMBLUE);
    dateAvailable2.writeControl();
    dateAvailable2.dateFormat="dd-MM-yyyy";
   </script></td>
		</tr>
		<tr id="b8" style="display:<?= $tipoBusqueda[$opcionMenu][8]; ?>">
			<td class="titulos3"><p align="left">
					Hora l&iacute;mite<br>&nbsp;&nbsp;(hh:mm m)
				</p></td>
			<td class="listado5"><select name="s_hora_limite" class=select>
<?php
    for ($i = 1; $i <= 12; $i ++) {
        if ($i <= 9) {
            $h = "0" . $i;
        } else {
            $h = "" . $i;
        }
        $seleccion = "";
        if ($h == $v_hora_limite) {
            $seleccion = "SELECTED";
        }
        ?>
   <option <?= $seleccion; ?> value="<?= $h;?>"><?= $h;?></option>
<?php                               }   ?>										 
  </select>&nbsp;:&nbsp; <select name="s_minuto_limite" class=select>
<?php
    for ($i = 0; $i <= 59; $i ++) {
        if ($i <= 9) {
            $h = "0" . $i;
        } else {
            $h = "" . $i;
        }
        $seleccion = "";
        if ($h == $v_minuto_limite) {
            $seleccion = "SELECTED";
        }
        ?>
   <option <?= $seleccion; ?> value="<?= $h;?>"> <?= $h;?></option>
<?php                               }   ?>										 
   </select>&nbsp;:&nbsp; <select name="s_meridiano" class=select>
<?php
    if ($v_meridiano == "AM") {
        ?>								 
      <option value="AM" selected>am</option>
					<option value="PM">pm</option>									
<?php

} else {
        ?>								 
  <option value="AM">am</option>
					<option value="PM" selected>pm</option>									
<?php                                } ?>
   </select>
		
		</tr>
		<tr>
			<td class="titulos3" colspan="2">
<?php
    if ($opcionMenu == 0 || $opcionMenu == 4) {
        ?>			
   <input type="reset" class='botones' value="Limpiar"
				onClick="javascript: limpiar();"> <input type="submit"
				class='botones' value="Generar">
<?php
    } else {
        ?>			
<input type="submit" class='botones' value="Buscar">
<?php
    }
    ?>			
   </td>
		</tr>
	</table>
</form>
<?php
}

// end function
// ===============================
// Search_Show end
// ===============================
// ===============================
// Pedidos_Show begin
// ===============================
function Pedidos_Show()
{
    // De sesion
    global $db;
    global $krd; // usuario actual
    global $dependencia; // dependencia del usuario actual
    global $ruta_raiz;
    // Control de visualizacion
    global $sFileName;
    global $opcionMenu;
    global $pageAnt; // Pagina de la cual viene
                     // Valores
    global $fechaFinal;
    global $fechaInicial;
    global $s_EXPEDIENTE;
    // Set variables with search parameters
    $ps_PRES_ESTADO = strip(get_param("s_PRES_ESTADO"));
    $ps_RADI_NUME_RADI = strip(trim(get_param("s_RADI_NUME_RADI")));
    $ps_EXPEDIENTE = strip(trim(get_param("s_EXPEDIENTE")));
    $ps_USUA_LOGIN = strip(trim(get_param("s_USUA_LOGIN")));
    $ps_DEPE_NOMB = strip(get_param("s_DEPE_NOMB"));
    $ps_USUA_NOMB = strip(get_param("s_USUA_NOMB"));
    $ps_hora_limite = strip(get_param("s_hora_limite"));
    $ps_minuto_limite = strip(get_param("s_minuto_limite"));
    $ps_meridiano = strip(get_param("s_meridiano"));
    $ps_PRES_REQUERIMIENTO = strip(get_param("s_PRES_REQUERIMIENTO"));
    if (strlen($pageAnt) == 0) {
        // Build SQL
        include_once $ruta_raiz . "/include/query/prestamo/builtSQL1.inc";
        include_once $ruta_raiz . "/include/query/prestamo/builtSQL2.inc";
        include_once $ruta_raiz . "/include/query/prestamo/builtSQL3.inc";
        // Build ORDER statement
        $iSort = strip(get_param("FormPedidos_Sorting"));
        $iSorted = strip(get_param("FormPedidos_Sorted"));
        $sDirection = strip(get_param("s_Direction"));
        if ($iSorted != $iSort) {
            $sDirection = " DESC ";
        } else {
            if (strcasecmp($sDirection, " DESC ") == 0) {
                $sDirection = " ASC ";
            } else {
                $sDirection = " DESC ";
            }
        }
        $sOrder = " order by " . $iSort . $sDirection . ",PRESTAMO_ID";
        // Inicializa el titulo y la visibilidad de los resultados
        Include_once "inicializarRTA.inc";
        // Execute SQL statement
        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $db->conn->Execute($sSQL . $sOrder);
        $db->conn->SetFetchMode(ADODB_FETCH_NUM);
        //echo $sSQL . $sOrder;

        // Process empty recordset
        if (! $rs || $rs->EOF) {
            ?>
<p align="center" class="titulosError2">NO HAY REGISTROS SELECCIONADOS</p>
<?php

return;
        }
        // Build parameters for order
        $form_params_search = "s_RADI_NUME_RADI=" . tourl($ps_RADI_NUME_RADI) . "&s_EXPEDIENTE=" . tourl($ps_EXPEDIENTE) . "&s_USUA_LOGIN=" . tourl($ps_USUA_LOGIN) . "&s_DEPE_NOMB=" . tourl($ps_DEPE_NOMB) . "&s_USUA_NOMB=" . tourl($ps_USUA_NOMB) . "&s_PRES_REQUERIMIENTO=" . tourl($ps_PRES_REQUERIMIENTO) . "&s_PRES_ESTADO=" . tourl($ps_PRES_ESTADO) . "&fechaInicial=" . tourl($fechaInicial) . "&fechaFinal=" . tourl($fechaFinal) . "&s_hora_limite=" . tourl($ps_hora_limite) . "&s_minuto_limite=" . tourl($ps_minuto_limite) . "&s_meridiano=" . tourl($ps_meridiano);

        $form_params_page = "&FormPedidos_Page=1&FormStarPage=1&FormSiguiente=0";
        $form_params = $form_params_search . $form_params_page . "&opcionMenu=" . tourl($opcionMenu) . "&krd=" . tourl($krd) . "&FormPedidos_Sorted=" . tourl($iSort) . "&s_Direction=" . tourl($sDirection) . "&FormPedidos_Sorting=";
        // HTML column prestamo headers
        ?>
<form method="post" action="prestamo.php" name="rta">
	<input type="hidden" value='<?=$krd?>' name="krd"> <input type="hidden"
		value=" " name="radicado"> <input type="hidden" value=""
		name="prestado"> <input type="hidden" name="opcionMenu"
		value="<?= $opcionMenu ?>">
	<!-- orden de presentacion del resultado en el formulario de envio !-->
	<input type="hidden" name="FormPedidos_Sorting" value="<?=$iSort?>"> <input
		type="hidden" name="FormPedidos_Sorted" value="<?=$iSorted?>"> <input
		type="hidden" name="s_Direction" value="<?=$sDirection?>">
	<div align="center">
	<table style="width: 100%;" id="myTable" class="tablesorter" >
		<!-- <tr>
			<td class="titulos4" colspan="<?=$numCol?>"><a name="Search"><?= $tituloRespuesta[$opcionMenu]?></a></td>
		</tr> -->		   	  	  		 		 		 	  
<?PHP 
// Titulos de las columnas
        include_once "inicializarTabla.inc";
        // ----------------------
        // Process page scroller
        // ----------------------
        // Initialize records per page
        /*$iRecordsPerPage = 100;
        // Inicializa el valor de la pagina actual
        $iPage = intval(get_param("FormPedidos_Page"));
        // Inicializa los registros a presentar seg�n la pagina actual
        $iCounter = 0;
        $ant = "";
        if ($iPage > 1) {
            do {
                $new = $rs->fields["PRESTAMO_ID"];
                if ($new != $ant) {
                    $iCounter ++;
                    $ant = $new;
                }
                $rs->MoveNext();
            } while ($iCounter < ($iPage - 1) * $iRecordsPerPage && ! $rs->EOF);
        }
        $iCounterIni = $iCounter;*/
        // Display grid based on recordset
        $y = 0; // Cantidad de registros presentados
        include_once "getRtaSQLAntIn.inc"; // Une en un solo campo los expedientes
        ?>
        <tbody>
        <?php 
        while ($rs && ! $rs->EOF) {
            // Inicializa las variables con los resultados
            include "getRtaSQL.inc";
            if ($antfldPRESTAMO_ID != $fldPRESTAMO_ID) { // Une en un solo campo los expedientes
                if ($y != 0) {
                    include "cuerpoTabla.inc";
                } // Fila de la tabla con los resultados
                include "getRtaSQLAnt.inc";
                $y ++;
            } else {
                if ($antfldEXP != "") {
                    $antfldEXP .= "<br>";
                    $antfldARCH .= "<br>";
                }
                $antfldEXP .= $fldEXP;
                if ($fldARCH == 'SI') {
                    $encabARCH = session_name() . "=" . session_id() . "&buscar_exp=" . tourl($fldEXP) . "&krd=$krd&tipo_archivo=&nomcarpeta=";
                    $antfldARCH .= "<a href='" . $ruta_raiz . "/expediente/datos_expediente.php?" . $encabARCH . "&num_expediente=" . tourl($fldEXP) . "&nurad=" . tourl($antfldRADICADO) . "' class='vinculos'>" . $fldARCH . "</a>";
                } else {
                    $antfldARCH .= $fldARCH;
                }
            }
            $rs->MoveNext();
        }
        ?>
        </tbody>
        <?php
        if ($y != 0) {
            include "cuerpoTabla.inc"; // Fila de la tabla con lso resultados
            $y ++;
        }
        $cantRegPorPagina = $y;
        $iCounter = $iCounter + $y;
        ?>
   <script>
  // Inicializa el arreglo con los radicados a procesar
  var cantRegPorPagina=<?=$cantRegPorPagina-1?>;	   		   
  // Marca todas las casillas si la del titulo es marcada
  function seleccionarRta() {
     valor=document.rta.rta_.checked;
<?php
        for ($j = 0; $j < $cantRegPorPagina; $j ++) {
            ?>
      document.rta.rta_<?=$j?>.checked=valor;			  
<?php } ?>
   }
   // Valida y envia el formulario
   function enviar() {			  
   var cant=0;
    for (i=0; i<cantRegPorPagina; i++) {
       if (eval('document.rta.rta_'+i+'.checked')==true){ 
        cant=1;
      break;
    }
    }
    if (cant==0) { alert("Debe seleccionar al menos un radicado"); }
    else { 
     document.rta.prestado.value=cantRegPorPagina;			  			  
     document.rta.action="formEnvio.php";
     document.rta.submit();
    }
   }
   // Regresa al menu de prestamos
   function regresar() {
    document.rta.opcionMenu.value="";
    document.rta.action="menu_prestamo.php";			   			   
    document.rta.submit();
   }		
    </script>  
<?
        // Build parameters for page
        /*if (strcasecmp($sDirection, " DESC ") == 0) {
            $sDirectionPages = " ASC ";
        } else {
            $sDirectionPages = " DESC ";
        }
        $form_params_page = $form_params_search . "&opcionMenu=" . tourl($opcionMenu) . "&FormPedidos_Sorted=" . tourl($iSort) . "&s_Direction=" . tourl($sDirectionPages) . "&krd=" . tourl($krd) . "&FormPedidos_Sorting=" . tourl($iSort);
        // Numero total de registros
        $ant = $antfldPRESTAMO_ID;

        while ($rs && ! $rs->EOF) {
            $new = $rs->fields["PRESTAMO_ID"]; // para el manejo de expedientes
            if ($new != $ant) {
                $ant = $new;
                $iCounter ++;
            }
            $rs->MoveNext();
        }
        $iCounter --;
        // Inicializa paginas visualizables
        $iNumberOfPages = 10;
        // Inicializa cantidad de paginas
        $iHasPages = intval($iCounter / $iRecordsPerPage);
        if ($iCounter % $iRecordsPerPage != 0) {
            $iHasPages ++;
        }
        // Determina la pagina inicial del intervalo
        $iStartPages = 1;
        $FormSiguiente = get_param("FormSiguiente"); // Indica si (1) el numero de paginas es mayor al visualizable
        if ($FormSiguiente == 0) {
            $iStartPages = get_param("FormStarPage");
        } elseif ($FormSiguiente == - 1) {
            $iStartPages = $iPage;
        } else {
            if ($iPage > $iNumberOfPages) {
                $iStartPages = $iPage - $iNumberOfPages + 1;
            }
        }
        // Genera las paginas visualizables
        $sPages = "";
        if ($iHasPages > $iNumberOfPages) {
            if ($iStartPages == 1) {
                $sPages .= "|<  <<   ";
            } else {
                $sPages .= "<a href=\"$sFileName?$form_params_page&FormPedidos_Page=1&FormStarPage=1&FormSiguiente=0&\">
              <font class=\"ColumnFONT\" title=\"Ver la primera p&aacute;gina\">|<</font></a>&nbsp;";
                $sPages .= "&nbsp;<a href=\"$sFileName?$form_params_page&FormPedidos_Page=" . tourl($iStartPages - 1) . "&FormStarPage=" . tourl($iStartPages - 1) . "&FormSiguiente=-1&\"><font class=\"ColumnFONT\" title=\"Ver la p&aacute;gina " . ($iStartPages - 1) . "\"><<</font></a>&nbsp;&nbsp;&nbsp;";
            }
        }
        for ($iPageCount = $iStartPages; $iPageCount < ($iStartPages + $iNumberOfPages); $iPageCount ++) {
            if ($iPageCount <= $iHasPages) {
                $sPages .= "<a href=\"$sFileName?$form_params_page&FormPedidos_Page=$iPageCount&FormStarPage=" . tourl($iStartPages) . "&FormSiguiente=0&\">
             <font class=\"ColumnFONT\" title=\"Ver la p&aacute;gina " . $iPageCount . "\">" . $iPageCount . "</font></a>&nbsp;";
            } else {
                break;
            }
        }
        if ($iHasPages > $iNumberOfPages) {
            if ($iPageCount - 1 < $iHasPages) {
                $sPages .= "...&nbsp;&nbsp;<a href=\"$sFileName?$form_params_page&FormPedidos_Page=$iPageCount&FormStarPage=" . tourl($iStartPages) . "&FormSiguiente=1&\"><font class=\"ColumnFONT\" title=\"Ver la p&aacute;gina " . $iPageCount . "\">>></font></a>&nbsp;&nbsp;";
                $sPages .= "&nbsp;<a href=\"$sFileName?$form_params_page&FormPedidos_Page=$iHasPages&FormStarPage=tourl($iStartPages)
               &FormSiguiente=1&\"><font class=\"ColumnFONT\" title=\"Ver la &uacute;ltima p&aacute;gina\">>|</font></a>";
            } else {
                $sPages .= "   >>  >|";
            }
        }*/
        ?>
   <!-- <tr class="titulos5" align="center">
			<td class="leidos" colspan="<?=($numCol+1);?>"><br><?=$sPages?><br>
			<br>P&aacute;gina <?=$iPage?>/<?=$iHasPages?><br>
       Total de Registros: <?=$iCounter?><br>&nbsp;</td>
		</tr>-->						 
   </table>
   </br>
  	 	<div class="pager">
        	<img src="../estilos/addons/pager/icons/first.png" class="first" alt="First" />
        	<img src="../estilos/addons/pager/icons/prev.png" class="prev" alt="Prev" />
        	<span class="pagedisplay" data-pager-output-filtered="{startRow:input} - {endRow} / {filteredRows} of {totalRows} total rows"></span>
        	<img src="../estilos/addons/pager/icons/next.png" class="next" alt="Next" />
        	<img src="../estilos/addons/pager/icons/last.png" class="last" alt="Last" />
        	<select class="pagesize" title="Select page size">
        		<option value="10">10</option>
        		<option value="20">20</option>
        		<option value="30">30</option>
        		<option value="all">Todos</option>
        	</select>
        	<select class="gotoPage" title="Select page number"></select>
        </div>
   </div>
   
   <div>
   <?php 
// Botones para procesar
        if ($tipoRespuesta[$opcionMenu][$numRtaMax] == "") {
            ?>						
   <tr class="titulos4" align="center">
			<td class="listado1" colspan="<?=($numCol+1);?>"
				style="text-align: center;"><input type="button" class='botones'
				value="<?=$tituloSubmitRta[$opcionMenu]?>"
				onClick="javascript:enviar();"> <input type="button" class='botones'
				value="Cancelar"
				title="Regresa al men&uacute; de pr&eacute;stamo y control de documentos"
				onClick="javascript:regresar();"></td>
		</tr>	
<?php       }?>	
   </div>
   
   <script>
   var pagerOptions = {

		    container: $(".pager"),
		    ajaxUrl: null,
		    customAjaxUrl: function(table, url) { return url; },
		    ajaxError: null,
		    ajaxObject: { dataType: 'json' },
		    ajaxProcessing: null,
		    processAjaxOnInit: true,
		    output: '{startRow:input} - {endRow} / {totalRows}',
		    updateArrows: true,
		    page: 0,
		    size: 10,
		    savePages : true,
		    storageKey:'tablesorter-pager',
		    pageReset: 0,
		    fixedHeight: false,
		    removeRows: false,
		    countChildRows: false,
		    cssNext: '.next', // next page arrow
		    cssPrev: '.prev', // previous page arrow
		    cssFirst: '.first', // go to first page arrow
		    cssLast: '.last', // go to last page arrow
		    cssGoto: '.gotoPage', // select dropdown to allow choosing a page
		    cssPageDisplay: '.pagedisplay', // location of where the "output" is displayed
		    cssPageSize: '.pagesize', // page size selector - select dropdown that sets the "size" option
		    cssDisabled: 'disabled', // Note there is no period "." in front of this class name
		    cssErrorRow: 'tablesorter-errorRow' // ajax error information row

		  };
	  
        $("#myTable").tablesorter({
            theme: 'blue',
            widthFixed: true,
            widgets: ['zebra']
        }).tablesorterPager(pagerOptions);
    </script>
</form>
<?php
    } // fin if
} // fin function
  // ===============================
  // Pedidos_Show end
  // ===============================
?>
