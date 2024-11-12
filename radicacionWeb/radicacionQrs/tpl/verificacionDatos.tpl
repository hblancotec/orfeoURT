<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{TITULO_PAGINA}</title>
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
<link href="{ESTILOS}" rel="stylesheet" type="text/css">
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
		alert("Debe escribir su cedula o nit sin puntos ni comas");
		form.nit.focus();
		return false;
	}
	
	if(form.telefono.value == "" )
	{
		alert("Debe escribir un telefono para contactarlo");
		form.direccion.focus();
		return false;
	}

	if(form.direccion.value == "" )
	{
		alert("Debe escribir la direccion envio de correspondencia");
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
<script type="text/javascript" src="expandingMenu.js"></script>
<style type="text/css">
	<!--
	.style1 {color: #333333}
	.style2 {color: #000000}
	.style3 {color: #CC3300}
	-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body>
<table width="610" height="999" border="0">
  <tr> 
    <!--<td height="34" valign="top" class="tituloener style1">QUEJAS, RECLAMOS Y SUGERENCIAS <br>-->
      <span class="tituloaaa"><br>
      Formato para usuarios &gt; Confirmaci&oacute;n de datos</span></td>
  </tr>

  <tr> 
    <td height="158" valign="top" class="textoContenido"> <blockquote>

      <blockquote><strong><span class="style3">FAVOR REVISE SUS DATOS:</span> Si los datos est&aacute;n correctos digite el c&oacute;digo de seguridad, que est&aacute; en la parte inferior de &eacute;ste formulario, dentro del campo IMAGEN DE SEGURIDAD y haga click en ENVIAR FORMA. Si no, por favor corregir antes de enviar este formulario.</strong></blockquote>
        <div align="justify"><div>
          <form name="formato" method="post" action="http://172.16.0.147:81/~cmauricio/br3.6.0/radicacionWeb/radicacionQrs/radicarSugerencia.php" onSubmit="return validar(this);">
              <table width="100%"  border="0" cellpadding="3" cellspacing="2" bgcolor="#CCCCCC">
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td width="25%"><strong>TIPO * </strong></td>
                  <td width="75%" align="center" valign="middle" class="textoContenido"><strong> Queja
		    <input type="hidden" name="formRadinicio" value="false">
                    <input name="tipoQRS" type="radio" value="Q" checked> 
                    &nbsp;&nbsp;Reclamo
                    <input name="tipoQRS" type="radio" value="R" > 
                    &nbsp;&nbsp;Sugerencia 
                    <input name="tipoQRS" type="radio" value="S" > 
                    </strong></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Primer apellido * </td>
                  <td class="textoContenido"><input name="apellido" type="text" class="textoContenido" id="apellido" size="40"></td>
                </tr>
		<tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Segundo apellido </td>
                  <td class="textoContenido"><input name="apellido2" type="text" class="textoContenido" id="apellido2" size="40"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Nombres * </td>
                  <td class="textoContenido"><input name="nombre" type="text" class="textoContenido" id="nombre" size="40"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>C&eacute;dula o NIT *</td>
                  <td class="textoContenido"><input name="nit" type="text" class="textoContenido" id="nit" size="40"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Tel&eacute;fono </td>
                  <td class="textoContenido"><input name="telefono" type="text" class="textoContenido" id="telefono" size="40"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Direcci&oacute;n * </td>
                  <td class="textoContenido"><input name="direccion" type="text" class="textoContenido" id="direccion" size="40"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Ciudad * </td>
                  <td class="textoContenido"><select name="ciudad" id="ciudad">
                    <option value="1" >Bogot&aacute;</option>
                    <option value="0" selected>seleccione una</option>
                  </select></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Departamento * </td>
                  <td class="textoContenido"><select name="departamento" id="departamento">
                    <option value="1" >Cundinamarca</option>
                    <option value="2" >Caldas</option>
                    <option value="3" >Risaralda</option>
                    <option value="4" >Casanare</option>
                    <option value="0" selected>Seleccione uno</option>
                    </select></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Correo electr&oacute;nico</td>
                  <td class="textoContenido"><input name="email" type="text" class="textoContenido" id="email" size="40"></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td><p>Asunto * <br>
                    (m&aacute;ximo 1500 caracteres)</p>                  </td>
                  <td class="textoContenido"><textarea name="asunto" cols="50" rows="10" id="asunto"></textarea></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td colspan="2">&nbsp;</td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td colspan="2" align="center">
                      <img
                                  src="PassImageServlet/VjI2c09PRW9uU0M5YUR6Wk1DY0phcjVyNXZJcWlHQzJETU5yTjgzRVJGdG1Sc2ZielFxU3RBRHlVbXVoNTBLb0x6WjVuTlhYMFJFPQ=="
                                  border="0">		</td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>Verificaci&oacute;n * </td>
                  <td class="textoContenido"><input name="skewImg" type="text" class="textoContenido" id="skewImg"></td>                  
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td colspan="2"><div align="center"></div></td>
                </tr>
                <tr bgcolor="#FFFFFF" class="textoContenido">
                  <td>&nbsp;</td>
                  <td align="center">
                    <input type="submit" name="Submit" value="ENVIAR FORMULARIO">                  </td>
                </tr>
              </table>
              <p>&nbsp;</p>
            </form>
            <p>&nbsp;</p>
          </div>
        </div>
        </blockquote>
      <p align="justify">&nbsp; </p></td>
  </tr>
</table>
</body>
</html>
