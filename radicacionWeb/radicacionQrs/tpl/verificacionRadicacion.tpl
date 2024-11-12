<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{TITULO_PAGINA}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #FFFFFF;
}
-->
</style>
<link href="{ESTILOS_RADICADO}" rel="stylesheet" type="text/css">
<script type="text/javascript" src="expandingMenu.js"></script>
<script language="javascript">
	vecSubseccionE = new Array ({ARREGLOJS});
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
		var idFinal = 0;

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
	
		/* 
		 * Funcion que segun la opcion de la categoria, 
		 * arma el combo de la seccion con los datos que tienen como padre dicha categoria.
		 */
		function cambiar_seccion(elselect) {	
			var j = 1;
			limpiar_todo();
			indice = elselect.selectedIndex;
			id = elselect.options[indice].value;
			nombre = elselect.options[indice].text;
			for (i=0; i<vecSubseccionE.length;i++) {
				if (vecSubseccionE[i].id_categoria==id) {
					document.formaqrs.municipio.options[j] = new Option(vecSubseccionE[i].nombre,vecSubseccionE[i].id);
				j ++;
			}
		}
		if(j==1){
			//document.formaqrs.causal_new.options[0] = new Option('No aplica.',0);
			document.formaqrs.municipio.options[0] = new Option('No aplica.',0);
		}
		idFinal = id;
		nombreFinal = nombre;
	}
	
	/* 
	 * Funcion que segun la opcion de la seccion, 
	 * arma el combo de la subseccion con los datos que tienen como padre dicha seccion.
	 */
	function cambiar_subseccion(elselect) {
		limpiar_subseccion();
		indice = elselect.selectedIndex;
		id = elselect.options[indice].value;
		nombre = elselect.options[indice].text;
		var j = 1;
		for (i=0; i<vecSubseccionE.length;i++) {
			if (vecSubseccionE[i].id_seccion==id) {
				document.formaqrs.municipio.options[j] = new Option(vecSubseccionE[i].nombre,vecSubseccionE[i].id);
				j ++;
			}	
		}
		if(j==1){
			document.formaqrs.deta_causal.options[0] = new Option('Seleccione un Municipio',1);
		}
		idFinal = id;
		nombreFinal = nombre;
	}

	//Funciones que borran los datos de los combos y los deja con un solo valor 0.
	function limpiar_todo(){
		document.formaqrs.municipio.options[0]= new Option('Seleccione un Municipio',1);
		var tamsubsec = document.formaqrs.municipio.options.length;
		for (j=1;j<tamsubsec;j++) {
			document.formaqrs.municipio.options[1] = null;
		}
	}

	function limpiar_subseccion(){
		document.formaqrs.municipio.options[0]= new Option('Seleccione un Municipio',1);
		var tamsubsec = document.formaqrs.municipio.options.length;
		alert(document.formaqrs.municipio.options[0]);
		for (j=1; j<tamsubsec ; j++) {
			document.formaqrs.municipio.options[1] = null;
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
		indice = document.formaqrs.categoria.selectedIndex;
		if (document.formaqrs.categoria.options[indice].value == 0) {
			alert("Escoja un Departamento");
			return (false);
		}  else if ( idFinal == 18 || idFinal == 16 ) {
			alert("Escoja una seccion");
			return (false);
		}  else if ( idFinal == 26 || idFinal == 27 || idFinal == 28 || idFinal == 29 ) {
			alert("Escoja una Subseccion");
			return (false);
		} else {
			document.formaqrs.target = "";
			document.formaqrs.action = "resultados_empleo.php";
			if (idFinal != "") {
				document.formaqrs.id.value = idFinal;
				document.formaqrs.nombre.value = nombreFinal;
			}	
			return (true); 
		}
	}

	/*
	 * Funcion que valida los campos y pasa a la pagina 
	 * siguiente despues de hacer click en el boton de buscar
	 */
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
		document.formaqrs.submit();
	}
		
</script>
<script language="JavaScript" type="text/JavaScript">
function confirmar(form)
{
	if(form.tipoQRS[0].checked == false && form.tipoQRS[1].checked == false && form.tipoQRS[2].checked == false)
	{
	  alert("Debe seleccionar el tipo");
	  return false;
	}
	if(form.apellido.value == null || form.apellido.value == "")
	{
		alert("Debe escribir sus apellidos");
		form.apellido.focus();
		return false;
	}
	if(form.nombre.value == "" )
	{
		alert("Debe escribir su nombre o el de la Empresa");
		form.nombre.focus();
		return false;
	}
	if(form.nit.value == "" || isNaN(form.nit.value) )
	{
		alert("Debe escribir su cedula o nit");
		form.nit.focus();
		return false;
	}

	if(form.direccion.value == "" )
	{
		alert("Debe escribir la direcci&oacute;n env&iacute;o de correspondencia");
		form.direccion.focus();
		return false;
	}
	if(form.ciudad.value == 0 )
	{
		alert("Debe escoger la ciudad");
		form.ciudad.focus();
		return false;
	}
	if(form.departamento.value == 0 )
	{
		alert("Debe escoger el departamento");
		form.departamento.focus();
		return false;
	}
	if(form.asunto.value == "" )
	{
		alert("Debe escribir el asunto de su solicitud");
		form.asunto.focus();
		return false;
	}
	
	return true;
}

function enviaURL(form){
	//traer el ordenamiento
	var tipo= null;
	if(form.tipoQRS[0].checked) tipo = form.tipoQRS[0].value;
	if(form.tipoQRS[1].checked) tipo = form.tipoQRS[1].value;
	if(form.tipoQRS[2].checked) tipo = form.tipoQRS[2].value;
	var apellido = form.apellido.value;
	alert("para irse");
	window.location = "{ARCHIVO_EXEC}?tipo=" + tipo + "&apellido=" + apellido;
}
</script>
<style type="text/css">
<!--
.style1 {color: #333333}
.style2 {color: #000000}
-->
</style>
</head>
<body>
<table width="550" height="294" border="0">
<tr> 
    <td class="tituloener style1" height="34" valign="top">
      <span class="tituloaaa"><br>
      Formato para usuarios &gt; Confirmaci&oacute;n de datos</span></td>
  </tr>
  <tr> 
    <td class="textoContenido" height="119" valign="top"> <blockquote>
  <blockquote><strong><span class="style3">FAVOR REVISE SUS DATOS:</span> Si los datos est&aacute;n correctos digite el c&oacute;digo de seguridad, que est&aacute; en la parte inferior de &eacute;ste formulario, dentro del campo IMAGEN DE SEGURIDAD y haga click en ENVIAR FORMA. Si no, por favor corregir antes de enviar este formulario.</strong></blockquote>  </tr>
  <tr> 
    <td height="158" class="textoContenido"> <blockquote> 
            <form name="formaqrs" action="{ARCHIVO_EXEC}" onSubmit="return confirmar(this);enviarURL(this)" method="post">
              <table width="100%"  border="0" cellpadding="3" cellspacing="2" bgcolor="#CCCCCC">
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td width="25%"><strong>TIPO </strong></td>
                  <td width="75%" align="center" valign="middle" class="textoContenido"><strong> Queja
                    <input name="tipoQRS" type="radio" value="Q" {QUEJA_CHECKED}>
                    &nbsp;&nbsp;Reclamo
                    <input name="tipoQRS" type="radio" value="R" {RECLAMO_CHECKED}>
                    &nbsp;&nbsp;Sugerencia 
                    <input name="tipoQRS" type="radio" value="S" {SUGERENCIA_CHECKED}> 
                    </strong></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Nombres * </td>
                  <td class="textoContenido">
		   <input name="nombre" type="text" class="textoContenido" id="nombre" size="40" value="{VNOMBRES}"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Primer apellido * </td>
                  <td class="textoContenido">
		   <input name="apellido" type="text" class="textoContenido" id="apellido" size="40" value="{VPAPELLIDO}"></td>
                </tr>
		<tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Segundo apellido </td>
                  <td class="textoContenido">
		   <input name="apellido2" type="text" class="textoContenido" id="apellido2" size="40" value="{VSAPELLIDO}"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>C&eacute;dula o NIT * </td>
                  <td class="textoContenido">
		   <input name="nit" type="text" class="textoContenido" id="nit" size="40" value="{VCEDULA}"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Tel&eacute;fono </td>
                  <td class="textoContenido">
		   <input name="telefono" type="text" class="textoContenido" id="telefono" size="40" value="{VTELEFONO}"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Direcci&oacute;n * </td>
                  <td class="textoContenido">
		   <input name="direccion" type="text" class="textoContenido" id="direccion" size="40" value="{VDIRECCION}"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Departamento * </td>
                  <td class="textoContenido">{DEPARTAMENTO_SELECT}</td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Ciudad * </td>
                  <td class="textoContenido">{MUNICIPIO_SELECT}</td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Correo electr&oacute;nico</td>
                  <td class="textoContenido">
		  <input name="email" type="text" class="textoContenido" id="email" size="40" value="{VEMAIL}"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td><p>Asunto *<br>(m&aacute;ximo 1500 caracteres)</p></td>
                  <td class="textoContenido">
		   <textarea name="asunto" cols="50" rows="16" maxlength="1500" id="asunto" onBlur="enviaURL();">{DESCRIPCION}</textarea>		  </td>
                </tr>
		<tr class="textoContenido" bgcolor="#ffffff">
                  <td colspan="2" align="center">
			{CAPTCHA_IMG}                  </td>
                </tr>
                </tr>
              </table>
            </form>
          </div>
        </div>
        </blockquote>
     
      <table width="93%" border="0" align="center">
        <tbody>
          <tr valign="middle" align="left"> 
            <td width="2%"><em><font size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></em></td>
            <td width="98%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
		<a href="#"><strong>Arriba</strong></a></font></td>
          </tr>
        </tbody>
      </table>
      <p align="justify">&nbsp; </p></td>
  </tr>
</table>
</body>
</html>
