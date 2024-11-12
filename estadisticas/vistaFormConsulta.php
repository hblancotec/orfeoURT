	<?php
set_time_limit(0);
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

$krdOld = $krd;
$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
if(!$tipoCarpOld) $tipoCarpOld= $tipo_carpt;
$ruta_raiz = "..";

if(!$krd) { 
    $krd=$krdOsld;
    include "$ruta_raiz/rec_session.php";
}


//Paremetros get y pos enviados desde la apliacion origen
//import_request_variables("gP", "");	
	
if(!$tipoEstadistica)	$tipoEstadistica =1;
if(!$dependencia_busq)	$dependencia_busq =$dependencia;
if(!$serie_busq)		$serie_busq = 22222;
if(!$subSerie_busq)		$subSerie_busq = 33333;
if(!$fechAno_busq)		$fechAno_busq  = 55555;	
	
/** DEFINICION DE VARIABLES ESTADISTICA
* var $tituloE String array  Almacena el titulo de la Estadistica Actual
* var $subtituloE String array  Contiene el subtitulo de la estadistica
* var $helpE String Almacena array Almacena la descripcion de la Estadistica.
*/
$tituloE[1] = "REPORTE - CONSULTA DE RADICADOS POR USUARIO";
$tituloE[2] = "REPORTE - ESTADISTICAS POR MEDIO DE RECEPCION";
$tituloE[3] = "REPORTE - MEDIO DE ENVIO DE DOCUMENTOS";
$tituloE[4] = "REPORTE - ESTADISTICAS DE DIGITALIZACION DE DOCUMENTOS";
$tituloE[5] = "REPORTE - RADICADOS DE ENTRADA RECIBIDOS DEL AREA DE CORRESPONDENCIA";
$tituloE[6] = "REPORTE - RADICADOS ACTUALES EN LA DEPENDENCIA";
//$tituloE[7] = "REPORTE - ESTADISTICAS DE NUMERO DE DOCUMENTOS ENVIADOS";
//$tituloE[8] = "REPORTE DE VENCIMIENTOS";
$tituloE[9] = "REPORTE - SEGUIMIENTO A RADICADOS DE ENTRADA";
//$tituloE[10] = "REPORTE - ASIGNACION RADICADOS";
$tituloE[11] = "REPORTE - ESTADISTICAS DE DIGITALIZACION Y ASOCIACION DE IMAGENES";
//$tituloE[12] = "REPORTE - DOCUMENTOS RETIPIFICADOS POR TRD";
$tituloE[13] = "REPORTE - EXPEDIENTES POR DEPENDENCIA";
//$tituloE[14] = "REPORTE DE RADICADOS ASIGNADOS DETALLADOS (CARPETAS PERSONALES)";
$tituloE[15] = "REPORTE - REASIGNACION DE DOCUMENTO A OTRO USUARIO";
//$tituloE[16] = "REPORTE DE RESPUESTA A RADICADOS";
$tituloE[17] = "REPORTE - INTERNET";
$tituloE[18] = "REPORTE - SEGUIMIENTO A TR&Aacute;MITES";
//$tituloE[19] = "REPORTE - PQR'S PARA SITIO WEB DNP";
$tituloE[20] = "REPORTE - PRESTAMOS";
//$tituloE[21] = "REPORTE - INTERNET - RADICADOS PENDIENTES DE TRAMITE";
$tituloE[22] = "REPORTE - CAMBIOS DE TRD";
//$tituloE[23] = "REPORTE - RESPUESTAS PQRSD INTERNET";

$subtituloE[1] = "ORFEO - Generada el: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[2] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[3] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[4] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[5] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[6] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
//$subtituloE[8] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";

$helpE[1] = "Este reporte genera la cantidad de radicados por usuario. Se puede discriminar por tipo de radicaci&oacute;n. ";
$helpE[2] = "Este reporte genera la cantidad de radicados de acuerdo al medio de recepci&oacute;n o envio realizado al momento de la radicaci&oacute;n.";
$helpE[3] = "Este reporte genera la cantidad de radicados enviados a su destino final por el &aacute;rea.  " ;
$helpE[4] = "Este reporte genera la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n." ;
$helpE[5] = "Este reporte genera la cantidad de documentos de entrada dirigidos a una dependencia. " ;
$helpE[6] = "Esta estadistica trae la cantidad de radicados \n generados por usuario, se puede discriminar por tipo de Radicacion. " ;
//$helpE[8] = "Este reporte genera la cantidad de radicados de entrada cuyo vencimiento esta dentro de las fechas seleccionadas. " ;
$helpE[9] = "Este reporte muestra el proceso que han tenido los radicados tipo 2 que ingresaron durante las fechas seleccionadas. ";
//$helpE[10] = "Este reporte muestra cuantos radicados de entrada han sido asignados a cada dependencia. ";
$helpE[11] = "Muestra la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n y la fecha de digitalizaci&oacute;n." ;
//$helpE[12] = "Muestra los radicados que ten&iacute;an asignados un tipo documental(TRD) y han sido modificados";
$helpE[13] = "Muestra todos los expedientes agrupados por dependencia, serie, subserie con el n&uacute;mero de radicados totales";
//$helpE[14] = "Muestra el total de radicados que tiene un usuario y el detalle del radicado con respecto al Remitente(Detalle), Predio(Detalle), ESP(Detalle) ";
$helpE[15] = "Muestra los usuario a los cuales se le ha asignado un documento que el usuario actual tenia";
//$helpE[16] = "Muestra los radicados que se han pasado por un usuario y que se le han dado respuesta";
$helpE[17] = "Muestra los radicados que son de PQRSD ";
$helpE[18] = "Muestra los radicados que tienen asignado tr&aacute;mites";
//$helpE[19] = "Reporte periodico solicitado para publicar en la pagina web del DNP";
$helpE[20] = "Muestra los radicados y/o expedientes que se encuentran en solicitud de pr�stamo y est�n prestados";

//$helpE[21] = "Muestra los radicados que se encuentran pendientes de tr�mite";
$helpE[22] = "Muestra los radicados que cambiaron de TRD";
//$helpE[23] = "Muestra los radicados que son de PQRSD y tienen respuesta ";
$generarOrfeo = $_POST['generarOrfeo'];
?>

<html>
 <head>
  <title>principal</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="../estilos/orfeo.css">
  <link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
  <script>
	function adicionarOp (forma,combo,desc,val,posicion){
		o = new Array;
		o[0]=new Option(desc,val );
		eval(forma.elements[combo].options[posicion]=o[0]);
		//alert ("Adiciona " +val+"-"+desc );
	}
  </script>
  <script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
  <script language="javascript">
	<!--

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
	$fecha_busq = date("Y/m/d") ;
	if(!$fecha_fin) 
		$fecha_fin = $fecha_busq;
?>

	var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formulario", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formulario", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);
	//-->
  </script>
 </head>

<?php
	include "$ruta_raiz/envios/paEncabeza.php";
?>

 <table> <tr> <td> </td> </tr> </table>

<?php
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	include("$ruta_raiz/class_control/usuario.php");
	include("$ruta_raiz/class_control/Dependencia.php");
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$objUsuario = new Usuario($db);
	//$db->conn->debug = true;
?>

 <body bgcolor="#ffffff" topmargin="0">
  <div id="spiffycalendar" class="text"></div>
  <form name="formulario"  method=post action='./vistaFormConsulta.php?<?=session_name()."=".trim(session_id())."&krd=$krd&fechah=$fechah"?>'>
   <table width="100%"  border="0" cellpadding="0" cellspacing="5" class="borde_tab">
	<tr class="titulos2">
	 <td colspan="2" >POR RADICADOS  -
	  - <A href='vistaFormProc.php?<?=session_name()."=".trim(session_id())."&krd=$krd&fechah=$fechah"?>' style="color: #FFFFCC">POR PROCESOS </A> 
	 </td>
	</tr>
	<tr>
	 <td colspan="2" class="titulos3">
	  <span class="cal-TextBox"><?=$helpE[$tipoEstadistica]?></span>
	 </td>
	</tr>
	<tr>
	 <td width="30%" class="titulos2">Tipo de Consulta / Estadistica</td>
	 <td class="listado2" align="left">
	  <select name=tipoEstadistica  class="select" onChange="formulario.submit();">

<?php
	foreach($tituloE as $key=>$value) {
		if($tipoEstadistica==$key) $selectE = " selected "; else $selectE = "";
		echo "<option value='$key' $selectE>$tituloE[$key]</option>\n\t";
	}
?>

	  </select>
	 </td>
	</tr>

<?php
	if($tipoEstadistica!=16 && $tipoEstadistica!=19 ){
?>

	<tr>
	 <td width="30%" class="titulos2">Dependencia</td>
	 <td class="listado2">

<?php
if($tipoEstadistica != 17 && $tipoEstadistica != 21 && $tipoEstadistica != 23){
			print "<select name='dependencia_busq'  class='select'  onChange='formulario.submit();'>";
		}
		else{
			print "<select name='dependencia_busq'  class='select'>";
		}
		// Consulta de estadisticas para los usuario que tenga privilegio de reasignar
		$getUsuario = "	SELECT	USUARIO_REASIGNAR
						FROM 	USUARIO
						WHERE	USUA_LOGIN = '$krd'";
		$rsUsuario = $db->conn->Execute($getUsuario);
		$permisoReasignar = "";
		if (!$rsUsuario->EOF){
			$permisoReasignar = $rsUsuario->fields["USUARIO_REASIGNAR"];
			$permisoReasignar = (!empty($permisoReasignar) && $permisoReasignar != 0) ? true : false;
		}
		if($usua_perm_estadistica>1 || (($tipoEstadistica == 15 || $tipoEstadistica == 16) && $permisoReasignar)
		    || $tipoEstadistica == 17 || $tipoEstadistica == 21 || $tipoEstadistica == 23){
			if($dependencia_busq==99999){
				$datoss= " selected ";
			}
			echo "<option value='99999' $datoss>-- Todas las Dependencias --</option>\n";
		}
	    $whereDepSelect=" DEPE_CODI = $dependencia ";
    	if ($usua_perm_estadistica==1){
        	$whereDepSelect=" $whereDepSelect or depe_codi_padre = $dependencia ";
    	}
    	if ($usua_perm_estadistica==2 || $tipoEstadistica == 17 || $tipoEstadistica == 21 || $tipoEstadistica == 23) {
			$isqlus = "	SELECT	a.DEPE_CODI,
								a.DEPE_NOMB,
								a.DEPE_CODI_PADRE
						FROM	DEPENDENCIA a
						ORDER BY a.DEPE_NOMB";
    	}
		else {
			$whereDepSelect=
			$isqlus = "	SELECT	a.DEPE_CODI,
								a.DEPE_NOMB,
								a.DEPE_CODI_PADRE
						FROM	DEPENDENCIA a
						WHERE	$whereDepSelect ";
    	}
    	$rs1 = $db->conn->Execute($isqlus);
		do{
			$codigo = $rs1->fields["DEPE_CODI"];
			$vecDeps[]=$codigo;
			$depnombre = $rs1->fields["DEPE_NOMB"];
			$datoss="";
			if($dependencia_busq==$codigo){
				$datoss= " selected ";
			}
			echo "<option value='$codigo'  $datoss>$codigo - $depnombre</option>\n";
			$rs1->MoveNext();
    	}
		while(!$rs1->EOF);
?>

	 </td>
	</tr>

<?php
	}
	else {
		echo "<input type='hidden' name='dependencia_busq' value='$codigo'>";
	}
	
	// MOSTRAR MEDIO DE RECEPCION PARA EL REPORTE DE INTERNET (17)
	if($tipoEstadistica == 17 || $tipoEstadistica == 21 || $tipoEstadistica == 23) {
	    print '<tr><td class="titulos2"> Medio de recepci&oacute;n </td> <td class="listado2">';
	    //Consultas para filtros de seleccion
	    //medio de recepcion
	    $medSQL = " SELECT	MREC_DESC
							,MREC_CODI
					FROM	MEDIO_RECEPCION
					ORDER BY MREC_DESC";
	    $cons_medRecp 	= $db->conn->Execute($medSQL);
	    print $cons_medRecp->GetMenu2('med_rec2'
	        ,$med_rec2
	        ,'0: [-Todos los medios-]'
	        ,true
	        ,5
	        , 'id="med_rec" class="select" ');
	    print '</td></tr>';
	    print '<tr><td class="titulos2"> Temas </td> <td class="listado2">';
	    //Consulta para filtros de seleccion
	    //Temas
	    $sqlPqr = "	SELECT	SGD_DCAU_DESCRIP
							,SGD_DCAU_CODIGO
                	FROM	SGD_DCAU_CAUSAL
					WHERE	SGD_DCAU_CODIGO >=1 
					ORDER BY SGD_DCAU_DESCRIP";
	    
	    $cons_temas = $db->conn->Execute($sqlPqr);
	    $temaSelect = (empty($temaSelect))? 0 : $temaSelect;
	    $datoss 	= ($temaSelect=="99999")? "selected" : '';
	    print "<select name='temaSelect'>
				<OPTION value='99999' $datoss> -- Solo Radicados Con Temas --</OPTION>";
	    $datoss = ($temaSelect=='0')? "selected" : '';
	    print "<OPTION value=0 $datoss> -- Todos los Radicados --</OPTION>";
	    while(!$cons_temas->EOF){
	        $registro 	= $cons_temas->fields["SGD_DCAU_CODIGO"];
	        $temaDesc 	= $cons_temas->fields["SGD_DCAU_DESCRIP"];
	        $datoss	 	= ($temaSelect == $registro)? 'selected' : '';
	        print "<OPTION value='$registro' $datoss>$temaDesc</OPTION>";
	        $cons_temas->MoveNext();
	    }
	    print '</select></td></tr>';
	    $datoss  = ($SelPqr)? 'checked' : '';
	    print "<tr><td class='titulos2'> Tipo Documento </td>
			  <td class='listado2'><input type='checkbox' name='SelPqr' $datoss>pqr</td></tr>";
	    
	    $Es_Anonimo = "checked";
	    $Tipo_Tercero = "checked";
	    $Tipo_de_Documento = "checked";
	    $Inf_Poblacional = "checked";
	    $Tema1 = "checked";
	    $Fecha_Radicado = "checked";
	    $Fecha_Vence = "checked";
	    $Fecha_Respuesta = "checked";
	    $Dias_Vencimiento = "checked";
	    $Dias_respuesta = "checked";
	    $Respuesta_1 = "checked";
	    $Dep_Respuesta = "checked";
	    $Tipo_Respuesta = "checked";
	    $Responsable_1 = "checked";
	    $Respuesta_2 = "checked";
	    $Respuesta_3 = "checked";
	    $Imagen_Respuesta = "checked";
	    $Asunto = "checked";
	    $Medio_de_Recepcion = "checked";
	    $Medio_Respuesta_solicitado = "checked";
	    $Pqr_Verbal = "checked";
	    $Departamento = "checked";
	    $Municipio = "checked";
	    $Dependencia_Actual = "checked";
	    $Usuario_Actual = "checked";
	    $Dependencia_anterior = "checked";
	    $Historico_1 = "checked";
	    $Historico_2 = "checked";
	    $Expediente = "checked";
	    $Nombre_Expediente = "checked";
	    $Remitente = "checked";
	    $Documento = "checked";
	    $DiasTermino = "checked";
	    $telefono = "checked";
	    $email = "checked";
	    $radcuentai = "checked";
	    $Producto = "checked";
	    
	    iF ($_POST['campos'] == null) {
	        $campos = array();
	    } else {
	        $campos = $_POST['campos'];
	        
	        $Es_Anonimo = "";
	        $Tipo_Tercero = "";
	        $Tipo_de_Documento = "";
	        $Inf_Poblacional = "";
	        $Tema1 = "";
	        $Fecha_Radicado = "";
	        $Fecha_Vence = "";
	        $Fecha_Respuesta = "";
	        $Dias_Vencimiento = "";
	        $Dias_respuesta = "";
	        $Respuesta_1 = "";
	        $Dep_Respuesta = "";
	        $Tipo_Respuesta = "";
	        $Responsable_1 = "";
	        $Respuesta_2 = "";
	        $Respuesta_3 = "";
	        $Imagen_Respuesta = "";
	        $Asunto = "";
	        $Medio_de_Recepcion = "";
	        $Medio_Respuesta_solicitado = "";
	        $Pqr_Verbal = "";
	        $Departamento = "";
	        $Municipio = "";
	        $Dependencia_Actual = "";
	        $Usuario_Actual = "";
	        $Dependencia_anterior = "";
	        $Historico_1 = "";
	        $Historico_2 = "";
	        $Expediente = "";
	        $Nombre_Expediente = "";
	        $Remitente = "";
	        $Documento = "";
	        $DiasTermino = "";
	        $telefono = "";
	        $radcuentai = "";
	        $email = "";
	        $Producto = "";
	    }
	    for($i = 0; $i < count($campos); $i++)
	    {
	        if ($campos[$i] == "Es_Anonimo") { $Es_Anonimo = "checked"; continue; } 
	        if ($campos[$i] == "Tipo_Tercero") { $Tipo_Tercero = "checked"; continue; }
	        if ($campos[$i] == "Tipo_de_Documento") { $Tipo_de_Documento = "checked"; continue; }
	        if ($campos[$i] == "Inf_Poblacional") { $Inf_Poblacional = "checked"; continue; }
	        if ($campos[$i] == "Tema1") { $Tema1 = "checked"; continue; }
	        if ($campos[$i] == "Fecha_Radicado") { $Fecha_Radicado = "checked"; continue; }
	        if ($campos[$i] == "Fecha_Vence") { $Fecha_Vence = "checked"; continue; }
	        if ($campos[$i] == "Fecha_Respuesta") { $Fecha_Respuesta = "checked"; continue; }
	        if ($campos[$i] == "Dias_Vencimiento") { $Dias_Vencimiento = "checked"; continue; }
	        if ($campos[$i] == "Dias_respuesta") { $Dias_respuesta = "checked"; continue; }
	        if ($campos[$i] == "Respuesta_1") { $Respuesta_1 = "checked"; continue; }
	        if ($campos[$i] == "Dep_Respuesta") { $Dep_Respuesta = "checked"; continue; }
	        if ($campos[$i] == "Tipo_Respuesta") { $Tipo_Respuesta = "checked"; continue; }
	        if ($campos[$i] == "Responsable_1") { $Responsable_1 = "checked"; continue; }
	        if ($campos[$i] == "Respuesta_2") { $Respuesta_2 = "checked"; continue; }
	        if ($campos[$i] == "Respuesta_3") { $Respuesta_3 = "checked"; continue; }
	        if ($campos[$i] == "Imagen_Respuesta") { $Imagen_Respuesta = "checked"; continue; }
	        if ($campos[$i] == "Asunto") { $Asunto = "checked"; continue; }
	        if ($campos[$i] == "Medio_de_Recepcion") { $Medio_de_Recepcion = "checked"; continue; }
	        if ($campos[$i] == "Medio_Respuesta_solicitado") { $Medio_Respuesta_solicitado = "checked"; continue; }
	        if ($campos[$i] == "Pqr_Verbal") { $Pqr_Verbal = "checked"; continue; }
	        if ($campos[$i] == "Departamento") { $Departamento = "checked"; continue; }
	        if ($campos[$i] == "Municipio") { $Municipio = "checked"; continue; }
	        if ($campos[$i] == "Dependencia_Actual") { $Dependencia_Actual = "checked"; continue; }
	        if ($campos[$i] == "Usuario_Actual") { $Usuario_Actual = "checked"; continue; }
	        if ($campos[$i] == "Dependencia_anterior") { $Dependencia_anterior = "checked"; continue; }
	        if ($campos[$i] == "Historico_1") { $Historico_1 = "checked"; continue; }
	        if ($campos[$i] == "Historico_2") { $Historico_2 = "checked"; continue; }
	        if ($campos[$i] == "Expediente") { $Expediente = "checked";continue; }
	        if ($campos[$i] == "Nombre_Expediente") { $Nombre_Expediente = "checked"; continue; }
	        if ($campos[$i] == "Remitente") { $Remitente = "checked"; continue; }
	        if ($campos[$i] == "Documento") { $Documento = "checked"; continue; }
	        if ($campos[$i] == "DiasTermino") { $DiasTermino = "checked"; continue; }
	        if ($campos[$i] == "Telefono") { $telefono = "checked"; continue; }
	        if ($campos[$i] == "Email") { $email = "checked"; continue; }
	        if ($campos[$i] == "Referencia") { $radcuentai = "checked"; continue; }
	        if ($campos[$i] == "Producto") { $Producto = "checked"; continue; }
	    }
	    
	    ?>
	    <tr><td class='titulos2'> Campos a incluir </td>
			  <td class='listado2'>
                <table>
                    <tr>
                        <td class='listado2'>
                            <input type='checkbox' value='Es_Anonimo' name='campos[]' <?php echo $Es_Anonimo ?> >Anonimo
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Tipo_Tercero' name='campos[]' <?php echo $Tipo_Tercero ?> >Tipo Tercero
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Tipo_de_Documento' name='campos[]' <?php echo $Tipo_de_Documento ?> >Tipo Documento
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Inf_Poblacional' name='campos[]' <?php echo $Inf_Poblacional ?> >Inf Poblacional
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Tema1' name='campos[]' <?php echo $Tema1 ?> >Tema 1
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Fecha_Radicado' name='campos[]' <?php echo $Fecha_Radicado ?> >Fecha Radicado
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Fecha_Vence' name='campos[]' <?php echo $Fecha_Vence ?> >Fecha Vencimiento
                        </td>
                    </tr>
                    <tr>
                        <td class='listado2'>
                            <input type='checkbox' value='Fecha_Respuesta' name='campos[]' <?php echo $Fecha_Respuesta ?> >Fecha Respuesta
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Dias_Vencimiento' name='campos[]' <?php echo $Dias_Vencimiento ?> >D&iacute;as Vencimiento
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Dias_respuesta' name='campos[]' <?php echo $Dias_respuesta ?> >D&iacute;as Respuesta
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Respuesta_1' name='campos[]' <?php echo $Respuesta_1 ?> >Respuesta 1
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Dep_Respuesta' name='campos[]' <?php echo $Dep_Respuesta ?> >Dependencia Respuesta
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Tipo_Respuesta' name='campos[]' <?php echo $Tipo_Respuesta ?> >Tipo Respuesta
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Responsable_1' name='campos[]' <?php echo $Responsable_1 ?> >Responsable 1
                        </td>
                    </tr>
                    <tr>
                        <td class='listado2'>
                            <input type='checkbox' value='Respuesta_2' name='campos[]' <?php echo $Respuesta_2 ?> >Respuesta 2
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Respuesta_3' name='campos[]' <?php echo $Respuesta_3 ?> >Respuesta 3
                        </td>  
                        <td class='listado2'>
                            <input type='checkbox' value='Imagen_Respuesta' name='campos[]' <?php echo $Imagen_Respuesta ?> >Imagen Respuesta
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Asunto' name='campos[]' <?php echo $Asunto ?> >Asunto
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Medio_de_Recepcion' name='campos[]' <?php echo $Medio_de_Recepcion ?> >Medio Recepci&oacute;n
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Medio_Respuesta_solicitado' name='campos[]' <?php echo $Medio_Respuesta_solicitado ?> >Medio Respuesta Solicitado
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Pqr_Verbal' name='campos[]' <?php echo $Pqr_Verbal ?> >Pqr Verbal
                        </td>
                    </tr>
                    <tr>
                        <td class='listado2'>
                            <input type='checkbox' value='Departamento' name='campos[]' <?php echo $Departamento ?> >Departamento
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Municipio' name='campos[]' <?php echo $Municipio ?> >Municipio
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Dependencia_Actual' name='campos[]' <?php echo $Dependencia_Actual ?> >Dependencia Actual
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Usuario_Actual' name='campos[]' <?php echo $Usuario_Actual ?> >Usuario Actual
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Dependencia_anterior' name='campos[]' <?php echo $Dependencia_anterior ?> >Dependencia Anterior
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Historico_1' name='campos[]' <?php echo $Historico_1 ?> >Historico 1
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Historico_2' name='campos[]' <?php echo $Historico_2 ?> >Historico 2
                        </td>
                    </tr>
                    <tr>
                        <td class='listado2'>
                            <input type='checkbox' value='Expediente' name='campos[]' <?php echo $Expediente ?> >Expediente
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Nombre_Expediente' name='campos[]' <?php echo $Nombre_Expediente ?> >Nombre Expediente
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Remitente' name='campos[]' <?php echo $Remitente ?> >Remitente
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Documento' name='campos[]' <?php echo $Documento ?> >Documento
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='DiasTermino' name='campos[]' <?php echo $DiasTermino ?> >D&iacute;as Termino
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Telefono' name='campos[]' <?php echo $telefono ?> >Tel&eacute;fono
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Email' name='campos[]' <?php echo $email ?> >E-mail
                        </td>
                    </tr>
                    <tr>   
                        <td class='listado2'>
                            <input type='checkbox' value='Referencia' name='campos[]' <?php echo $radcuentai ?>>Referencia
                        </td>
                        <td class='listado2'>
                            <input type='checkbox' value='Producto' name='campos[]' <?php echo $Producto ?>>Producto
                        </td> 
                    </tr>
                </table>
              </td></tr>
	<?php    
	}
	    
    if($generarOrfeo && $tipoEstadistica == 17) {
        include_once 'estadistica17F.php';
        $generarOrfeo = false;
        echo "<script language='javascript'>alert('El informe se enviar\u00e1 a su correo electr\u00f3nico en unos minutos.');</script>";
    }
    
    if ($generarOrfeo && $tipoEstadistica == 21) {
	    include_once 'estadistica21.php';
	    $generarOrfeo = false;
	}
	
	if ($generarOrfeo && $tipoEstadistica == 23) {
	    include_once 'estadistica23.php';
	    $generarOrfeo = false;
	}
?>

<!-- +++++++++++++++ INICIO SERIE, FECHA Y SUBSERIE EXPEDIENTES +++++++++++++++++++ -->
<!--INICIO SERIE -->
<?php
//$vars = get_defined_vars(); print_r($vars["_POST"]);
if($tipoEstadistica == 13){
?>
<tr>
  <td width="30%" class="titulos2"> Serie
  	<br/>
	<?php
	$datoss = "";
	if($srdOn) {
		$datoss = " checked ";
	}
	?>
	&nbsp; Solo Inactivas
	<input name="srdOn" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
  </td>
  <td class="listado2">
	<select name=serie_busq  class="select"  onChange="formulario.submit();">
	<?php
	$whereSrdOn = (!isset($_POST['srdOn']) )? "and m.sgd_mrd_esta = 1":"and m.sgd_mrd_esta = 0";
	
	// Consulta de la serie con la Dependencia seleccionada
	$fecha_hoy 		= Date("d-m-Y");
	$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
	$depeConsulta	= '';
	$datoss			= '';
	if($dependencia_busq != 99999)  {
		$depeConsulta =	'M.DEPE_CODI ='.$dependencia_busq. 'AND';
	}
	if($serie_busq == 22222)  {
		$datoss	= " selected ";
	}
	$whereSrdOff = (!isset($_POST['srdOn']) )? "":"AND m.SGD_SRD_CODIGO NOT IN 
	(SELECT DISTINCT m.SGD_SRD_CODIGO FROM SGD_MRD_MATRIRD m WHERE $depeConsulta m.SGD_MRD_ESTA = 1)";
	
	echo "<option value='22222' $datoss>-- Todas las Series --</option>\n";
	$getSerie =	"SELECT	DISTINCT (CONVERT(CHAR(4),S.SGD_SRD_CODIGO,0)+'-'+S.SGD_SRD_DESCRIP) AS DETALLE,
						s.SGD_SRD_CODIGO AS CODIGO
				FROM	SGD_MRD_MATRIRD m,
						SGD_SRD_SERIESRD s
				WHERE	$depeConsulta
						s.SGD_SRD_CODIGO = m.SGD_SRD_CODIGO
						$whereSrdOn
						and GETDATE() BETWEEN s.SGD_SRD_FECHINI AND s.SGD_SRD_FECHFIN
						$whereSrdOff
				ORDER BY detalle";
	$rsSerie = $db->conn->Execute($getSerie);
	
	$dnp_codigoSerie = "";
	while(!$rsSerie->EOF)  {
	    $dnp_codigoSerie .= $rsSerie->fields["CODIGO"] . ";";
		$detalle 	= $rsSerie->fields["DETALLE"];
		$codigoSer 	= $rsSerie->fields["CODIGO"];
		$datoss 	= ($serie_busq == $codigoSer)? $datoss= " selected ":"";
		echo "<option value='$codigoSer' $datoss>$detalle</option>";
		$rsSerie->MoveNext();
	};
	echo '<input type="hidden" name="itCodigoSerie" value="'.$dnp_codigoSerie.'">';
	?>
	</select>
  </td>
</tr>
<?php
}
?>
<!--FIN SERIE -->

<!--INICIO SUB-SERIE -->
<?php
if(($tipoEstadistica == 13) && ($serie_busq != 22222)){
?>
<tr>
  <td width="30%" class="titulos2">SubSerie
 	
 	<!-- *** Adicionado por CECG 02-02-12 *** -->
	<br/>
	<?php
	$datossb = "";
	if($sbrdOn)
	{	$datossb = " checked ";
	}
	?>
	&nbsp; Solo Inactivas
	<input name="sbrdOn" type="checkbox" class="select" <?=$datossb?> onChange="formulario.submit();">
  </td>
  <!-- *** Fin adicionado por CECG 02-02-12 *** -->
  
  <td class="listado2">
    <select name=subSerie_busq  class="select"  onChange="formulario.submit();">
	<?php
		
	// Consulta de la serie con la Dependencia seleccionada
	$fecha_hoy 		= Date("d-m-Y");
	$sqlFechaHoy	= $db->conn->DBDate($fecha_hoy);
	$depeConsulta	= '';
	$datoss			= '';
	$datossb		= '';
	if($dependencia_busq != 99999)  
	{	$depeConsulta  = 'M.DEPE_CODI ='.$dependencia_busq. 'AND';
	};
	if($subSerie_busq == 33333)  {
	$datossb		= " selected ";
	};
	
	//Agregado por CECG 02-02-12
	//Entra si NO se marca Series Inactivas
	if (!isset($_POST['srdOn']))
	{	//Entra si NO se marca Sub-Series Inactivas
		if (!isset($_POST['sbrdOn']) )
		{	$whereSbrdOn = "AND m.SGD_MRD_ESTA=1";
			$whereSbrdOff = "";
		}
		else
		{	$whereSbrdOn = "AND m.SGD_MRD_ESTA=0";
			$whereSbrdOff = "AND m.SGD_SBRD_CODIGO NOT IN (SELECT DISTINCT m.SGD_SBRD_CODIGO 
														  FROM	 SGD_MRD_MATRIRD m 
														  WHERE	 $depeConsulta
														 		 m.SGD_MRD_ESTA = 1)";
		}
	}
	//Entra SI se marco la opcion de Series Inactivas
	else 
	{	$whereSbrdOn = 	"AND m.SGD_MRD_ESTA=0";
		$whereSbrdOff = "AND m.SGD_SBRD_CODIGO NOT IN (	SELECT	DISTINCT m.SGD_SBRD_CODIGO 
				 										FROM 	SGD_MRD_MATRIRD m 
				 										WHERE	$depeConsulta
				 												m.SGD_MRD_ESTA = 1)";
	}
	//Fin Agregado por CECG 02-02-12	
	
	echo "<option value='33333' $datoss>-- Todas las SubSeries --</option>\n";
	$querySub =	"SELECT	DISTINCT (CONVERT(CHAR(4),SU.SGD_SBRD_CODIGO,0)+'- '+SU.SGD_SBRD_DESCRIP) AS DETALLE,
						SU.SGD_SBRD_CODIGO AS CODIGO
				 FROM	SGD_MRD_MATRIRD M INNER JOIN 
						SGD_SBRD_SUBSERIERD SU ON M.SGD_SBRD_CODIGO = SU.SGD_SBRD_CODIGO
				 WHERE	$depeConsulta
						M.SGD_SRD_CODIGO   		= '$serie_busq'
						AND SU.SGD_SRD_CODIGO  	= '$serie_busq'
						$whereSbrdOn
						AND GETDATE() BETWEEN SU.SGD_SBRD_FECHINI AND SU.SGD_SBRD_FECHFIN
						$whereSbrdOff
				 ORDER BY DETALLE";
	$rsSub=$db->conn->Execute($querySub);
	$dnp_codigoSerie = "";
	//$db->conn->debug = true;
	while(!$rsSub->EOF)  {
	    $dnp_codigoSerie .= $rsSub->fields["CODIGO"] . ";";
		$detalleSub	= $rsSub->fields["DETALLE"];
		$codigoSub 	= $rsSub->fields["CODIGO"];
		$datossSub 	= ($subSerie_busq == $codigoSub)? $datossSub = " selected ":"";
		echo "<option value='$codigoSub' $datossSub>$detalleSub</option>";
		$rsSub->MoveNext();
	};
	echo '<input type="hidden" name="itCodigoSerie" value="'.$dnp_codigoSerie.'">';
	?>
	</select>
  </td>
</tr>
<?php
//echo "query Sub ".$querySub;
}
?>
<!--FIN SUB-SERIE -->

<!--INICIO FECHA -->
<?php
if($tipoEstadistica == 13){
?>
<tr>
  <td width="30%" class="titulos2">A&ntilde;o</td>
  <td class="listado2">
	<select name=fechAno_busq  class="select"  onChange="formulario.submit();">
	<?php
	// Genera el rango de a�os para seleccionar
	if($fechAno_busq == 55555) {
		$datoss		= " selected ";
	};
	echo "<option value='55555' $datoss>-- Todos los A&ntilde;os --</option>\n";
	for($i = Date("Y"); $i > 1994; $i-- ){
		$datossFec = ($fechAno_busq == $i)? $datossFec = " selected ":"";
		echo "<option value='$i' $datossFec>$i</option>";
	}
	?>
	</select>
  </td>
</tr>
<?php
}
?>
<!--FIN FECHA -->
<!-- +++++++++++++++ FIN SERIE, FECHA Y SUBSERIE EXPEDIENTES ++++++++++++++++++++ -->

<?php
if ($dependencia_busq != 99999)  {
	$whereDependencia = " AND USD.DEPE_CODI=$dependencia_busq ";
}

if( $tipoEstadistica == 1  or $tipoEstadistica == 2  or $tipoEstadistica == 3  or
	$tipoEstadistica == 4  or $tipoEstadistica == 5  or $tipoEstadistica == 6  or
	$tipoEstadistica == 7  or $tipoEstadistica == 11 or $tipoEstadistica == 12 or
	$tipoEstadistica == 13 or $tipoEstadistica == 15){
?>
<tr id="cUsuario">
  <td width="30%" class="titulos2">Usuario
	<br/>
	<?php
	$datoss = "";
	if($usActivos) {
		$datoss = " checked ";
	}
	?>
	&nbsp;
	Incluir Usuarios Inactivos
	<input name="usActivos" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
	
  </td>
  <td class="listado2">
    <select name="codus"  class="select"  onChange="formulario.submit();">
	<?php
	if ($usua_perm_estadistica > 0){
    ?>
		<option value="0"> -- Agrupar por todos los usuarios --</option>
	<?php
	}
	$whereUsSelect = (!isset($_POST['usActivos']) )? "and u.USUA_ESTA = 1 ":"";
	$whereUsSelect = ($usua_perm_estadistica < 1)?
		(($whereUsSelect!="")? $whereUsSelect . "and u.USUA_LOGIN='$krd' ":"and u.USUA_LOGIN='$krd'"):$whereUsSelect;
	if($dependencia_busq != 99999)  {
		$whereUsSelect=($whereUsSelect=="") ? substr($whereDependencia,4):$whereUsSelect.$whereDependencia;
		$isqlus = "	SELECT
					u.USUA_NOMB,
                    u.USUA_CODI,
                    u.USUA_ESTA
                FROM
                   	USUARIO u,
                    SGD_USD_USUADEPE USD
                WHERE
                   	u.usua_login = usd.usua_login and
                    USD.SGD_USD_SESSACT = 1
                    $whereUsSelect
				ORDER BY
				  	u.USUA_NOMB";
		$rs1 = $db->conn->Execute($isqlus);
		while(!$rs1->EOF)  {
			$codigo = $rs1->fields["USUA_CODI"];
			$vecDeps[]=$codigo;
			$usNombre = $rs1->fields["USUA_NOMB"];
			$datoss = ($codus==$codigo)?$datoss= " selected ":"";
			echo "<option value='$codigo' $datoss>$usNombre</option>";
			$rs1->MoveNext();
		}
	}
	?>
	</select>
	&nbsp;
  </td>
</tr>
<?php
}

  if(   $tipoEstadistica == 1 or $tipoEstadistica == 2 or $tipoEstadistica == 3 or
  		$tipoEstadistica == 4 or $tipoEstadistica == 6 or $tipoEstadistica ==11 or
  		$tipoEstadistica ==12 or $tipoEstadistica ==15 or $tipoEstadistica==16) {
?>
<tr>
	<td width="30%" height="40" class="titulos2">Tipo de Radicado </td>
	<td class="listado2">
<?php
        $sqlQuery = "SELECT	SGD_TRAD_DESCR,
                        	SGD_TRAD_CODIGO
                     FROM  	SGD_TRAD_TIPORAD
                     ORDER BY SGD_TRAD_CODIGO";
		$rs = $db->conn->Execute($sqlQuery);
		$nmenu = "tipoRadicado";
		$valor = "";
		$default_str=$tipoRadicado;
		$itemBlanco = " -- Agrupar por Todos los Tipos de Radicado -- ";
		print $rs->GetMenu2($nmenu, $default_str, $blank1stItem = "$valor:$itemBlanco",false,0,'class=select');
		?>&nbsp;</td>
</tr>
<?php
  }
  if($tipoEstadistica== 1 or $tipoEstadistica == 6 or $tipoEstadistica == 10 or
	 $tipoEstadistica==12 or $tipoEstadistica ==14 or $tipoEstadistica == 15) {
?>
  <tr>
    <td width="30%" height="40" class="titulos2">Agrupar por Tipo de Documento </td>
    <td class="listado2">
	<select name=tipoDocumento  class="select" >
<?php
 		$isqlTD = "	SELECT	SGD_TPR_DESCRIP
 							,SGD_TPR_CODIGO
					FROM	SGD_TPR_TPDCUMENTO
					WHERE	SGD_TPR_CODIGO<>0
				    ORDER BY SGD_TPR_DESCRIP";
	    //if($codusuario!=1) $isqlus .= " and a.usua_codi=$codusuario ";
		//echo "--->".$isqlus;
		$rs1=$db->conn->Execute($isqlTD);
		$datoss = "";

		if($tipoDocumento!='9998'){
			$datoss= " selected ";
			$selecUs = " b.USUA_NOMB USUARIO, ";
			$groupUs = " b.USUA_NOMB, ";
		}

		if($tipoDocumento=='9999'){
			$datoss= " selected ";
		}
?>
		<option value='9999'  <?=$datoss?>>-- No Agrupar Por Tipo de Documento</option>
<?php
        $datoss = "";
		if($tipoDocumento=='9998'){
			$datoss= " selected ";
		}
?>
		<option value='9998'  <?=$datoss?>>-- Agrupar Por Tipo de Documento</option>
<?php
		$datoss = "";
		if($tipoDocumento=='9997'){
			$datoss= " selected ";
		}
?>
		<option value='9997'  <?=$datoss?>>-- Tipos Documentales No Definidos</option>
<?php
		do{
			$codigo = $rs1->fields["SGD_TPR_CODIGO"];
			$vecDeps[]=$codigo;
			$selNombre = $rs1->fields["SGD_TPR_DESCRIP"];
			$datoss="";
		if($tipoDocumento==$codigo){
				$datoss= " selected ";
			}
			echo "<option value=$codigo  $datoss>$selNombre</option>";
			$rs1->MoveNext();
		}while(!$rs1->EOF);
?>
		</select>

	  </td>
  </tr>
<?php
}

if($tipoEstadistica == 18){
	$sql = "SELECT SGD_NOMBR_TRAM, SGD_ID_TRAM, SGD_DEPFI_TRAM,".
			"(case when SGD_TRAD1_TRAM=1 then '1' else '' end + case when SGD_TRAD2_TRAM=1 then '2' else '' end +
					case when SGD_TRAD3_TRAM=1 then '3' else '' end + case when SGD_TRAD4_TRAM=1 then '4' else '' end +
					case when SGD_TRAD5_TRAM=1 then '5' else '' end + case when SGD_TRAD6_TRAM=1 then '6' else '' end +
					case when SGD_TRAD7_TRAM=1 then '7' else '' end + case when SGD_TRAD8_TRAM=1 then '8' else '' end +
					case when SGD_TRAD9_TRAM=1 then '9' else '' end) as RAD_DETIENE
			FROM SGD_TRAMITES ORDER BY SGD_NOMBR_TRAM";
	$rs_tram = $db->conn->Execute($sql);
	$slcTram = $rs_tram->GetMenu2('cmb_tram', $cmb_tram, false, false, 0, "class='select'");
?>
<tr>
	<td width="30%" class="titulos2">Tr&aacute;mite</td>
	<td class="listado2">
		<?php echo $slcTram; ?>
	</td>
</tr>
<?php
}

if($tipoEstadistica !=13 && $tipoEstadistica !=6){
?>
		<tr>
		    <td width="30%" class="titulos2">Desde fecha (aaaa/mm/dd) </td>
		    <td class="listado2">
				<script language="javascript">
				dateAvailable.writeControl();
				dateAvailable.dateFormat="yyyy/MM/dd";
				</script>
				&nbsp;
	  		</td>
	  	</tr>
	  <tr>
	    <td width="30%" class="titulos2">Hasta  fecha (aaaa/mm/dd) </td>
	    <td class="listado2">
			<script language="javascript">
			dateAvailable2.writeControl();
			dateAvailable2.dateFormat="yyyy/MM/dd";
			</script>&nbsp;
		</td>
	  </tr>
	<?php
	}

    if($tipoEstadistica == 22) {
        
        if ($tipoCambio == 1)
            $selec1 = "selected";
        if ($tipoCambio == 2)
            $selec2 = "selected";
        if ($tipoCambio == 3)
            $selec3 = "selected";
?>
		<tr>
		    <td width="30%" class="titulos2">Tipo Cambio </td>
		    <td class="listado2">
				<select id="tipoCambio" name="tipoCambio" class="select">
					<option value="0"> Seleccione </option>
					<option value="1" <?=$selec1?>> Cambio tipo documental con misma serie </option>
					<option value="2" <?=$selec2?>> Cambio serie documental </option>
					<option value="3" <?=$selec3?>> No tipificados como PQR </option>
				</select>
	  		</td>
	  	</tr>
	<?php
	}
?>
<tr>
	    <td colspan="2" class="titulos2">
			<center>
				<input name="Submit" type="submit" class="botones_funcion" value="Limpiar">
				<input type="submit" class="botones_funcion" value="Generar" name="generarOrfeo">
			</center>
			</td>
		</tr>
<?		if($generarOrfeo && ($tipoEstadistica == 17 || $tipoEstadistica == 21 || $tipoEstadistica == 23)){
		print"
		<tr class='tablas'>					
			<td colspan='2' class='titulos2'  align='center'>
				Para obtener el archivo guarde el destino del
				siguiente v&iacute;nculo al archivo: 
				<a href='$archivo' target='_blank'>GENERADO</a>
			</td>			
		<tr>";
		die;
		}		
?>	</table>
</form>
<?
$datosaenviar = "fechaf=$fechaf" .
				"&tipoEstadistica=$tipoEstadistica" .
				"&codus=$codus" .
				"&krd=$krd" .
				"&dependencia_busq=$dependencia_busq" .
				"&ruta_raiz=$ruta_raiz" .
				"&fecha_ini=$fecha_ini" .
				"&fecha_fin=$fecha_fin" .
				"&tipoRadicado=$tipoRadicado" .
				"&tipoDocumento=$tipoDocumento" .
				"&serie_busq=$serie_busq" .
				"&subSerie_busq=$subSerie_busq" .
				"&tipoCambio=$tipoCambio";

//$db->conn->debug = true;
if (isset($generarOrfeo) && $tipoEstadistica == 12) {
	global $orderby;
	$orderby = 'ORDER BY NOMBRE';
	$whereDep = ($dependencia_busq != 99999) ? "AND h.DEPE_CODI = " . $dependencia_busq : '';
	$isqlus = "	SELECT	u.USUA_NOMB NOMBRE
						,u.USUA_DOC
						,d.DEPE_CODI
						,COUNT(r.RADI_NUME_RADI) TOTAL_MODIFICADOS
				FROM	USUARIO u,
			            RADICADO r,
			            HIST_EVENTOS h,
			            DEPENDENCIA d,
			            SGD_TPR_TPDCUMENTO s
				WHERE	u.USUA_DOC = h.USUA_DOC
			            AND h.SGD_TTR_CODIGO = 32
			            AND h.HIST_OBSE LIKE '*Modificado TRD*%'
			            AND h.DEPE_CODI = d.DEPE_CODI
			            $whereDep
			            AND s.SGD_TPR_CODIGO = r.TDOC_CODI
			            AND r.RADI_NUME_RADI = h.RADI_NUME_RADI
			            AND " .$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin'
				GROUP BY u.USUA_NOMB, u.USUA_DOC, d.DEPE_CODI $orderby";
	$rs1 = $db->conn->Execute($isqlus);
	while(!$rs1->EOF)  {
		$usuadoc[] = $rs1->fields["USUA_DOC"];
		$dependencias[] = $rs1->fields["DEPE_CODI"];
		$rs1->MoveNext();
	}
}

if($generarOrfeo) {
   include "genEstadistica.php";
}

?>
</body>
</html>
<table  border="0" cellspace="2" cellpad="2" WIDTH="100%" class="borde_tab" align="center">
 <form name="jh"> 
  <input type="hidden" name="jj" value="0"> 
  <input type="hidden" name="dS" value="0">
 </form>
</table>