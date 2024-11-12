<?php
/*************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			             */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                     */
/* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador             */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */ 
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                      */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*************************************************************************************/
	$verrad = $verradicado;
	$krdOld = $krd;
	session_start();
	$ruta_raiz = "..";
	include "../rec_session.php";
	if(!$krd) $krd=$krdOld;
  define('ADODB_ASSOC_CASE', 1);
	include_once ("$ruta_raiz/include/db/ConnectionHandler.php");	
	if(!$verrad) $verrad = $verradicado;
	//$causal_new=$_POST["causal_new"];
  //if(!$_POST["causal_new"]) $causal_new =0; 
?>
<html>
<head>
<title> ;-)Modificacion de Sector / Temas ;-) </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
<script src="js/popcalendar.js"></script>
<script src="js/mensajeria.js"></script>
<script language="javascript">
	vecSubseccionE = new Array (
<?php
	// For para el javascript
	// new subseccionE ( id, Nombre, SeccionQuePertenece)
	//$db->conn->debug = true;
	$rs = $db->conn->Execute("SELECT * FROM SGD_DCAU_CAUSAL WHERE SGD_DCAU_ESTADO=1 ORDER BY SGD_DCAU_DESCRIP");
	$cont = 0;
   function strValido($string){
    $arr = array('/[^\w:()\sáéíóúÁÉÍÓÚ=#\-,.;ñÑ]+/', '/[\s]+/');
    $asu = preg_replace($arr[0], '',$string);
    return    strtoupper(preg_replace($arr[1], ' ',$asu));
  } 
	while(!$rs->EOF) {
		$coma = ($cont == 0) ? '': ',';
    $dcauDescrip = strValido($rs->fields["SGD_DCAU_DESCRIP"]);
		echo $coma . 'new seccionE ("' .  $rs->fields["SGD_DCAU_CODIGO"] ."-".$rs->fields["SGD_CAU_CODIGO"]. '",' .
									'"'	. $dcauDescrip . '",' .
									'"' . $rs->fields["SGD_CAU_CODIGO"] . '")' . "\n";
		$cont++;
		$rs->MoveNext();
	}
?>
);

	vecSeccionE = new Array ();
	vecCategoriaE = new Array ();
	
	//Inicializo las variables isNav, isIE dependiendo del navegador
	var isNav, isIE

	if (parseInt(navigator.appVersion) >= 4) {
		if (navigator.appName == "Netscape" ) {
			isNav = true;
		} else{
			isIE = true;
		}	
	}

	//Variable que va a tener el valor de la opcion seleccionada para hacer la busqueda.
	var idFinal=0 ;  

	//Estructuras para almacenar la informacion de las tablas de categorias, seccion y subseccion de la base de datos.
	function categoriaE (id, nombre) {
		this.id = id;
		this.nombre = nombre;
	}
	
	function seccionE (id, nombre, id_categoria) {
		this.id = id;
		this.nombre = nombre;
		this.id_categoria = id_categoria;
	}
	
	function subseccionE (id, nombre, id_seccion) {
		this.id = id;
		this.nombre = nombre;
		this.id_seccion = id_seccion;
	}
	
	// Funcion que segun la opcion de la categoria, arma el combo de la seccion con los datos que tienen como padre dicha categoria.
	function cambiar_seccion(elselect) {
  
		var j =0;
		limpiar_todo();
		indice = elselect.selectedIndex;
		id = elselect.options[indice].value;
		nombre = elselect.options[indice].text;
		for(i=0;i<vecSubseccionE.length;i++) {
			if (vecSubseccionE[i].id_categoria==id) {
				document.form_causales.deta_causal.options[j] = new Option(vecSubseccionE[i].nombre,vecSubseccionE[i].id);
				j ++;
			}
		}
		if(j==1){
		   //document.form_causales.causal_new.options[0] = new Option('No aplica.',0);
		   //document.form_causales.deta_causal.options[0] = new Option('No aplica.',0);
		}
		idFinal = id;
		nombreFinal = nombre;
	}

	// Funcion que segun la opcion de la seccion, arma el combo de la subseccion con los datos que tienen como padre dicha seccion.
	function cambiar_subseccion(elselect) {
		limpiar_subseccion();
		indice = elselect.selectedIndex;
		id = elselect.options[indice].value;
		nombre = elselect.options[indice].text;
		var j =1;
		for (i=0; i<vecSubseccionE.length;i++) {
			if (vecSubseccionE[i].id_seccion==id) {
				document.form_causales.deta_causal.options[j] = new Option(vecSubseccionE[i].nombre,vecSubseccionE[i].id);
				j ++;
			}	
		}
		if(j==1){
			document.form_causales.deta_causal.options[0] = new Option('----',0);
		}
		idFinal = id;
		nombreFinal = nombre;
	}

	//Funciones que borran los datos de los combos y los deja con un solo valor 0.
	function limpiar_todo(){
		//document.form_causales.sector.options[0]= new Option('Escoja',0);
		//document.form_causales.deta_causal.options[0]= new Option('----',0);
		//var tamsec = document.form_causales.sector.options.length;
		var tamsubsec = document.form_causales.deta_causal.options.length;
		for (j=1; j<tamsubsec ; j++) {
			document.form_causales.deta_causal.options[1] = null;
		}
	}

	function limpiar_subseccion(){
		document.form_causales.deta_causal.options[0]= new Option('---',0);
		var tamsubsec = document.form_causales.deta_causal.options.length;
		alert(document.form_causales.deta_causal.options[0]);
		for (j=1; j<tamsubsec ; j++) {
		  document.form_causales.deta_causal.options[1] = null;
		}
	}

	//Funcion que actualiza el idFinal
	function cambiar_idFinal(elselect){
		indice = elselect.selectedIndex;
		id = elselect.options[indice].value;
		nombre = elselect.options[indice].text;
		idFinal = id ;
		nombreFinal = nombre;
	}
	
	//Funcion que valida los campos y pasa a la pagina siguiente despues de hacer enter en el campo palabra
	function cambiar_pagina(){
		indice = document.form_causales.categoria.selectedIndex;     
		if (document.form_causales.categoria.options[indice].value == 0) {
			alert("Escoja una categoria");
			return (false);
		}  else if ( idFinal == 18 || idFinal == 16 ) {
			alert("Escoja una seccion");
			return (false);
		}  else if ( idFinal == 26 || idFinal == 27 || idFinal == 28 || idFinal == 29 ) {
			alert("Escoja una Subseccion");
			return (false);
		} else {
			document.form_causales.target = "";
			document.form_causales.action = "resultados_empleo.php";
			if (idFinal != "") {
				document.form_causales.id.value = idFinal;
				document.form_causales.nombre.value = nombreFinal;
			}	
			return (true); 
		}
	}

	//Funcion que valida los campos y pasa a la pagina siguiente despues de hacer click en el boton de buscar
	function cambiar_pagina_buscar(){
		//Obtengo la fecha que le interesa buscar al usuario
		//document.form_causales.historico.value = document.form_causales.fechas_historico.value;
		
		//Obtengo el indice de la fecha
		//indice_fecha = document.form_causales.fechas_historico.selectedIndex;
		
		//Obtengo el valor de la fecha completa
		//document.form_causales.fecha_completa.value = document.form_causales.fechas_historico.options[indice_fecha].text;
	
		indice = document.form_causales.categoria.selectedIndex;     
		if (document.form_causales.categoria.options[indice].value == 0) {
			alert("Escoja una categoria");
		} else if ( idFinal == 18 || idFinal == 16 ) {
			alert("Escoja una seccion");
		} else if ( idFinal == 26 || idFinal == 27 || idFinal == 28 || idFinal == 29 ) {
			alert("Escoja una Subseccion");
		} else {
			document.form_causales.target = "";
			document.form_causales.action = "resultados_empleo.php";
			if (idFinal != "") {
				document.form_causales.id.value = idFinal;
				document.form_causales.nombre.value = nombreFinal;
			}
			document.form_causales.submit();
		}
	}
	
	function verificacionCampos() {
		/*detalleCasual = document.form_causales.deta_causal.value;
		mensaje = '';
		
		if (detalleCasual==0) {
			if (detalleCasual == 0) {
				mensaje += 'Falta por asignar Detalle de Causal\n';
			}
		}
		
		if (mensaje!=''){
			alert(mensaje);
			return false;
		} else {*/
		document.form_causales.submit();
		//}
	}
	
	function cerrar() {
		opener.regresar();
		window.close();
	}
</script>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
  function cargarTemas(event){
  
  valorTema = document.getElementById("buscarCausal").value;
  $.getJSON("../include/tx/json/getinfoCausal.php", { deta_causal: valorTema}, function(Temas){
  
   if(valorTema){
     document.getElementById("deta_causal").length=0;
     i=0;
      for(var obj in Temas){
       b = typeof Temas[obj];
        document.getElementById("deta_causal").options[i] = new Option(Temas[obj],obj);
        seleccionado = document.getElementById("deta_causal").selectedIndex =0;
        codigosCau = document.getElementById("deta_causal").options[i].value.split("-",2);
        //alert (codigosCau[0] + "-->"+codigosCau[1]);
        document.getElementById("causal_new").value=codigosCau[1];
        i++;
      }
   }

  
  
 });
 }
  function cambiarSector(){
    
  seleccionado = document.getElementById("deta_causal").selectedIndex;
  
  if(seleccionado<=0) seleccionado=0
  codigosCau = document.getElementById("deta_causal").options[seleccionado].value.split("-",2);
  //alert (seleccionado + "--->"+ codigosCau[1]);
  document.getElementById("causal_new").value=codigosCau[1];

 }
</script>
<div id="spiffycalendar" class="text"></div>
</head>
<BODY onLoad="javascript:cambiarSector(this)";>
<center>
<form name=form_causales  method="post" action="<?=$ruta_raiz?>/causales/mod_causal.php?<?=session_name()?>=<?=trim(session_id())?>&krd=<?=$krd?>&verrad=<?=$verradicado?>&verradicado=<?=$verradicado?><?="&datoVer=$datoVer&mostrar_opc_envio=$mostrar_opc_envio&nomcarpeta=$nomcarpeta"?>">
<table border=0 width 100%  cellpadding="0" cellspacing="5" class="borde_tab" >
  <tr><th colspan=4>MODIFICACION DE SECTOR /TEMA</th></tr>
  <tr>
    <td class="titulos2"> Sector
<?php
  
	if (!$ruta_raiz) $ruta_raiz="..";
	include_once($ruta_raiz."/include/tx/Historico.php");
	$objHistorico= new Historico($db);
	if (is_array($recordSet) && (count($recordSet)>0) )
	array_splice($recordSet, 0);  		
	if (is_array($recordWhere) && (count($recordWhere)>0) )
	array_splice($recordWhere, 0);  
	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);     
	$arrayRad = array();
	$arrayRad[]=$verradicado;
	$actualizo = 0;
	$actualizoFlag = false;
	$insertoFlag = false;
	
	if ($grabar_causal ) {
		/** Intenta actualizar causal
		 *  Si esta no esta entonces simplemente le inserte
		 */
		if($causal==0) {
			$ddca_causal="0"; 
			$data_causa ="0";
		}
		if(!$ddca_causal) {
			$ddca_causal="";
		}
		if(!$deta_causal) {
		   $data_causa ="";
		}
		//$db->conn->debug = true;
		if($accionCausal=="Eliminar" && $cauxCodigoAccion)
		{
      $iSql = "select dcau.SGD_DCAU_DESCRIP, cau.SGD_CAU_DESCRIP
          from sgd_caux_causales caux, sgd_cau_causal cau, sgd_dcau_causal dcau
          where caux.sgd_caux_codigo=$cauxCodigoAccion
            and dcau.sgd_cau_codigo=cau.sgd_cau_codigo 
            and caux.sgd_dcau_codigo=dcau.sgd_dcau_codigo";
      $rs = $db->conn->Execute($iSql);
      $causalEliminada = $rs->fields["SGD_CAU_DESCRIP"];
      $dcausalEliminada = $rs->fields["SGD_DCAU_DESCRIP"];
      $recordSet["RADI_NUME_RADI"] = $verradicado;
      $recordSet["SGD_CAUX_CODIGO"] = $cauxCodigoAccion;
      if($db->delete("SGD_CAUX_CAUSALES", $recordSet)){
			$mensajeAccion = "Eliminado Sector/Tema ";
			$actualizo = "Ok";
      if ($actualizo != null) {
			echo "Causal Actualizada";
			$observa = "$mensajeAccion ($causalEliminada / $dcausalEliminada)";
			$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);
			$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 17);
			//$actualizoFlag = true;
      $actualizo = "";
		}
		 }else{
			$mensajeAccion = "No se ha podido elimiar el Sector / Tema Indicado";
		 }
		}
		if($deta_causal!=0)
		{
		if($accionCausal=="Modificar" && $cauxCodigoAccion)
		{
      $deta_causalCodigo = explode("-",$deta_causal);
			$sqlSelect = "SELECT SGD_CAUX_CODIGO,RADI_NUME_RADI as COUNT_RADI
						FROM SGD_CAUX_CAUSALES 
						WHERE RADI_NUME_RADI = $verradicado
						AND SGD_DCAU_CODIGO = ". $deta_causalCodigo[0];
			//$db->conn->debug = true;
			$rs = $db->conn->Execute($sqlSelect);
			if (!$rs->EOF )
			{
				$mensajeYaExiste = "";
				$mensajeAccion =  "<font color=red>Ya existe para el radicado $verradicado</font>";
			}else{
     $iSql = "select dcau.SGD_DCAU_DESCRIP, cau.SGD_CAU_DESCRIP
       from sgd_caux_causales caux, sgd_cau_causal cau, sgd_dcau_causal dcau
       where caux.sgd_caux_codigo=$cauxCodigoAccion
         and dcau.sgd_cau_codigo=cau.sgd_cau_codigo 
         and caux.sgd_dcau_codigo=dcau.sgd_dcau_codigo";
     $rs = $db->conn->Execute($iSql);
     $causalModificada = $rs->fields["SGD_CAU_DESCRIP"];
     $dcausalModificada = $rs->fields["SGD_DCAU_DESCRIP"]; 
      
     $deta_causalCodigo =  explode("-",$deta_causal);
		 $recordSet["SGD_DCAU_CODIGO"] = "'".$deta_causalCodigo[0]."'";
		 $recordWhere["RADI_NUME_RADI"] = $verradicado;
		 $recordWhere["SGD_CAUX_CODIGO"] = $cauxCodigoAccion;
     //$db->conn->debug = true;
		 if($db->update("SGD_CAUX_CAUSALES", $recordSet,$recordWhere)){
			$mensajeAccion = "Modificacion Sector/Tema";
			$actualizo = "Ok";
      $observa = "$mensajeAccion ($causalModificada / $dcausalModificada)";
		 }else{
			$mensajeAccion = "No se ha podido Actualizar el Sector/tema indicado";
		 }
    }
		}

		//$db->conn->debug = true;

		array_splice($recordSet, 0);
		array_splice($recordWhere, 0);
		$causal_nombre_grb = ($causal_nombre != '') ? $causal_nombre: 'Sin Tipificar' ;
		$dcausal_nombre_grb = ($dcausal_nombre != '') ? $dcausal_nombre : 'Sin Tipificar' ;
		
		if ($actualizo != null) {
			echo "Causal Actualizada";
			//$observa = "$mensajeAccion ($causal_nombre_grb / $dcausal_nombre_grb)";
			$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);
			$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 17);
			$actualizoFlag = true;
		}
			$flag = 0;
			if($accionCausal=='Agregar')
			{
      $deta_causalCodigo = explode("-",$deta_causal);
			$sqlSelect = "SELECT SGD_CAUX_CODIGO,RADI_NUME_RADI as COUNT_RADI
						FROM SGD_CAUX_CAUSALES 
						WHERE RADI_NUME_RADI = $verradicado 
						AND SGD_DCAU_CODIGO = ". $deta_causalCodigo[0];
			//$db->conn->debug = true;
			$rs = $db->conn->Execute($sqlSelect);
			if (!$rs->EOF )
			{
				$mensajeYaExiste = "";
				$mensajeAccion =  "<font color=red>Ya existe para el radicado $verradicado</font>";
			}else{
				$cod_caux++;
				if(!$ddca_causal) $ddca_causal='null';
        $deta_causalCodigo = explode("-",$deta_causal);
        $iSql = "select dcau.SGD_DCAU_DESCRIP, cau.SGD_CAU_DESCRIP
       from sgd_cau_causal cau, sgd_dcau_causal dcau
       where dcau.sgd_cau_codigo=cau.sgd_cau_codigo 
         and dcau.sgd_dcau_codigo=$deta_causalCodigo[0]";
        $rs = $db->conn->Execute($iSql);
        $causalInsertada = $rs->fields["SGD_CAU_DESCRIP"];
        $dcausalInsertada = $rs->fields["SGD_DCAU_DESCRIP"]; 
         
        $deta_causalCodigo = explode("-",$deta_causal);
        $recordSet["SGD_DCAU_CODIGO"] = "'".$deta_causalCodigo[0]."'";
        $recordWhere["RADI_NUME_RADI"] = $verradicado;
        $recordWhere["SGD_CAUX_CODIGO"] = $cauxCodigoAccion;
        
				$sqlCcondici = "Select (SGD_CAUX_CODIGO + 1) as NEXT_CODIGO
          from SGD_CAUX_CAUSALES ORDER BY SGD_CAUX_CODIGO DESC";
				$rsCauxCodigo = $db->conn->Execute($sqlCauxCodigoNuevo);
				
				$sqlCauxCodigoNuevo = "Select (SGD_CAUX_CODIGO + 1) as NEXT_CODIGO
                from SGD_CAUX_CAUSALES ORDER BY SGD_CAUX_CODIGO DESC";
				$rsCauxCodigo = $db->conn->Execute($sqlCauxCodigoNuevo);
				$nextCauxCodigo = $rsCauxCodigo->fields["NEXT_CODIGO"];
				$recordSet["SGD_CAUX_CODIGO"] = "'".$nextCauxCodigo."'";
        
				$recordSet["SGD_DCAU_CODIGO"] = "'".$deta_causalCodigo[0]."'";										
				$recordSet["RADI_NUME_RADI"] = $verradicado;										
				//$recordSet["SGD_DDCA_CODIGO"] = "".$ddca_causal."";
				$recordSet["SGD_CAUX_FECHA"] = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
				
				$rs = $db->insert("SGD_CAUX_CAUSALES", $recordSet);							
				array_splice($recordSet, 0);  	
				if ($rs){
					//echo "<span class=info>Causal Agregada</span>";
					$observa = "Se inserto ($causalInsertada / $dcausalInsertada) ";
					$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);
					$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 17);
					$insertoFlag = true;
					if($insertoFlag==true) $mensajeAccion = " Se agrego $Observa";
				} // Fin de actualizacion o insercion de casales
			}
				// Verifica banderas de actualizacion o de insercion para actulizar los nuevos datos
			}
		}
	 	if ($actualizoFlag || $insertoFlag) {
			$sqlSelect = "SELECT caux.SGD_CAUX_CODIGO, 
							dcau.SGD_DCAU_CODIGO,
							dcau.SGD_CAU_CODIGO,
							dcau.SGD_DCAU_DESCRIP,
							cau.SGD_CAU_DESCRIP
						FROM SGD_CAUX_CAUSALES caux,
								SGD_DCAU_CAUSAL dcau,
								SGD_CAU_CAUSAL cau
						WHERE caux.RADI_NUME_RADI = $verradicado AND
					          dcau.SGD_DCAU_CODIGO = caux.SGD_DCAU_CODIGO AND
					          cau.SGD_CAU_CODIGO = dcau.SGD_CAU_CODIGO";
			
			$rs = $db->conn->Execute($sqlSelect);
			
			if (!$rs->EOF){
				$causal_grb = $rs->fields["SGD_CAU_CODIGO"];
				$causal_nombre = $rs->fields["SGD_CAU_DESCRIP"];
				$deta_causal_grb = $rs->fields["SGD_DCAU_CODIGO"];
				$dcausal_nombre = $rs->fields["SGD_DCAU_DESCRIP"];
			}	
		}
   }
   ?>
      </td>
    <TD width="70%">
	<?php
	error_reporting(7);
	
	$isql = "SELECT SGD_CAU_CODIGO, SGD_CAU_DESCRIP
			FROM SGD_CAU_CAUSAL
			WHERE SGD_CAU_ESTADO=1 ORDER BY SGD_CAU_DESCRIP";
  //$db->conn->debug = true;
	$rs = $db->conn->Execute($isql);
	
	if($rs && !$rs->EOF) {
		if($causal_new == 0) {
			$causal = 0;
		} elseif ($causal_new) {
			$causal = $causal_new;
		}
	?>
	<SELECT name="causal_new" style="WIDTH:550;" id="causal_new" onChange="javascript:cambiar_seccion(this);"  class="select">
	<?php
		do {
			$codigo_cau = $rs->fields["SGD_CAU_CODIGO"];
			$nombre_cau = $rs->fields["SGD_CAU_DESCRIP"];
		  	if($codigo_cau==$causal) {
				$datoss = "selected";
			} else {
				$datoss = " ";
			}
			echo "<OPTION value=$codigo_cau $datoss>$nombre_cau</option>\n";
			$rs->MoveNext();
		} while(!$rs->EOF);
	?>
	</SELECT >
	<?php } ?>
      </TD>
<TR>
  <td class="titulos2" > Tema</td>
      <TD width="323">
        <?php
    $deta_causalCodigo = explode("-",$deta_causal);
    if(!$deta_causalCodigo[1]) $deta_causalCodigo[1] = 0;
		$isql = "SELECT * 
					FROM SGD_DCAU_CAUSAL
          WHERE sgd_dcau_estado=1 AND SGD_CAU_CODIGO = ".$deta_causalCodigo[1]
          ." ORDER BY SGD_DCAU_DESCRIP";
		//$db->conn->debug = true;
    $rs = $db->conn->Execute($isql);
    
		
	?>
      <TEXTAREA style="HEIGHT:20; WIDTH:550;" class="select" height="1" ROWS="1" COLS="62" name="buscarCausal" id="buscarCausal"  onkeyup="javascript:cargarTemas(this);"></TEXTAREA><br>
      <SELECT style="HEIGHT:80; WIDTH:550;" name=deta_causal id=deta_causal onChange="javascript:cambiarSector(this)"; class="select"  size="5">
        <?php
        if($rs && !$rs->EOF) {
        do {
            $codigo_dcau = $rs->fields["SGD_DCAU_CODIGO"]."-".$rs->fields["SGD_CAU_CODIGO"];
            $nombre_dcau = $rs->fields["SGD_DCAU_DESCRIP"];
              if($codigo_dcau==$deta_causal) {
              $datoss = "selected";
            } else {
              $datoss = " ";
            }
            echo "<OPTION value=$codigo_dcau $datoss>$nombre_dcau</option>\n";
            $rs->MoveNext();
          } while(!$rs->EOF);
        }
        ?>
      </SELECT>
      <?php
		
	?>
      </td>
</tr>

<tr>
<td colspan="2" align="center">
<table width="90%">
	<td align="center">
		<input type="submit" name=accionCausal value='Agregar'  class='botones'>
	</td>
	<td align="center">
		<input type="submit" name=accionCausal value='Modificar'  class='botones' >
	</td>	
	<td align="center">
		<input type="submit" name=accionCausal value='Eliminar'  class='botones'>
	</td>
	<td align="center">
		<input type="button" name=accionCausal value='Cerrar'  class='botones' onclick="cerrar();">
	</td>
</td>
</table>
</tr>
<input type=hidden name=ver_causal value="Si ver Causales">
<input type=hidden name="grabar_causal" value="1">
<input type=hidden name="$verrad" value="<?=$verradicado?>">
<input type=hidden name="sectorNombreAnt" value="<?=$sectorNombreAnt?>">
<input type=hidden name="sectorCodigoAnt" value="<?=$sectorCodigoAnt?>">
<input type=hidden name="causal_grb" value="<?=$causal_grb?>">
<input type=hidden name="causal_nombre" value="<?=$causal_nombre?>">
<input type=hidden name="deta_causal_grb" value="<?=$deta_causal_grb?>">
<input type=hidden name="dcausal_nombre" value="<?=$dcausal_nombre?>">

</table>

<?php
	$sqlSelect = "SELECT caux.SGD_CAUX_CODIGO,cau.SGD_CAU_DESCRIP as SECTOR
	    , dcau.SGD_DCAU_DESCRIP as CAUSAL,caux.SGD_CAUX_CODIGO
			,caux.RADI_NUME_RADI COUNT_RADI, caux.SGD_CAUX_FECHA
			FROM SGD_CAUX_CAUSALES caux, SGD_CAU_CAUSAL cau, SGD_DCAU_CAUSAL dcau
			WHERE RADI_NUME_RADI = $verradicado 
			and caux.SGD_DCAU_CODIGO=dcau.sgd_dcau_codigo
			and dcau.SGD_CAU_CODIGO=cau.SGD_CAU_CODIGO
			";
		//$db->conn->debug = true;
	$rs = $db->conn->Execute($sqlSelect);
	?>
	<table class='borde_tab'>
		<tr ><td>
			<?=$mensajeAccion?>
		</td></tr>
		</table>
	<table class='borde_tab' width="70%">
		<tr  class='titulos2'><th>Sector</th><th>Tema</th><th>Fecha</th><th>Accion</th></tr>
	<?php
	if($rs){
	while (!$rs->EOF)  {
		?>
		<tr><td class='listado2'><?=$rs->fields["SECTOR"]?></td>
		<td  class='listado2'><?=$rs->fields["CAUSAL"]?></td>
		<td  class='listado2'><?=$rs->fields["SGD_CAUX_FECHA"]?></td>
		<td  class='listado2' align="center">
 		 <input type='radio' name='cauxCodigoAccion' value='<?=$rs->fields["SGD_CAUX_CODIGO"]; ?>'>
		</td>
		<?php
		
		$rs->MoveNext();
	}
	}
	?>
	</table>
</form>


		</center>
	<?php
	$ruta_raiz = ".";
?>
</BODY>
</HTML>