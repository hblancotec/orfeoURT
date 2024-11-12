<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{TITULO_PAGINA}</title>
<link href="{ESTILOS_RADICADO}" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
-->
</style></head>
<body>
  <table width="550" border="0" bgcolor="#FFFFFF">
    <tr>
      <td height="19" colspan="3" class="textoContenido"><p>{CIUDAD_LARGO}, {FECHA_LARGA} </p>
      <p> </p></td>
    </tr>
    <tr>
      <td width="153"></td>
      <td width="252" align="center"><div align="left"><span class="textoFecha">Al contestar cite el n&uacute;mero de radicado de este documento </span><span class="tituloIntervenida"><br />
        </span><span class="subtitulointervenida">{ENTIDAD_CORTO} N&uacute;mero de Radicado: </span><span class="enlaceIntervenidas"><br />
        </span><span class="tituloaaa">{NUMERO_RADICADO}</span><span class="tituloIntervenida"><br />
        </span><span class="subtitulointervenida">Fecha: {FECHA_CORTA} </span><span class="tituloIntervenida"><br />
        </span><span class="textoContenido">{OTROS}</span><span class="tituloIntervenida"><br />
        </span><br />
      </div></td>
      <td width="131"><div align="left"><img src="{ESCUDO}" alt="escudo colombia" width="75" height="95" /></div></td>
    </tr>
    <tr>
      <td> </td>
      <td colspan="2" align="center" class="tituloIntervenida"><div align="left"><img src="{CODIGO_BARRAS}" alt="Numero de radicado" /></div></td>
    </tr>
</table>
  <table width="550" border="0" bgcolor="#FFFFFF">
    <tr>
      <td width="561" class="textoContenido"><p></p>
        <p>SE&Ntilde;ORES:<br/>
          {SUPERSERVICIOS}<br/>
          {DIRECCION_SUPER}<br/>
	  {TELEFONO_SUPER}<br/>
	  CIUDAD</p>
    </tr>
    <tr>
      <td> </td>
    </tr>
    <tr>
      <td class="celdaMenuPrincipal">Asunto: {TIPO_ASUNTO}</td>
    </tr>
    <tr>
      <td> </td>
    </tr>
    <tr>
      <td class="textoContenido"><p>Cordial saludo, </p>
    </tr>
    <tr>
      <td> </td>
    </tr>
    <tr>
      <td class="textoContenido">La presente es con el fin de informarles:</td>
    </tr>
    <tr>
      <td class="textoContenido">{DESCRIPCION}</td>
    </tr>
    <tr>
      <td class="textoDocumentoHome">
<p> </p>
   <p>Cordialmente <br/>
         {NOMBRE} {APELLIDOS}<br/>
         Documento de identidad: {DOC_ID}<br/>
         Direcci&oacute;n: {DIRECCION}<br/>
         Tel&eacute;fono: {TELEFONO}<br/>
        Correo electr&oacute;nico: {CORREOE}</p>
   <p><br/>
   </p>
      </tr>
    <tr>
      <td class="textoDocumentoHome"> </td>
    </tr>
    <tr>
      <td class="celdaMenuPrincipalB"><div align="center" class="celdaMenuPrincipal">
        <p><a href="{ARCHIVO_PDF}">DESCARGAR RADICADO EN FORMATO PDF</a></p>
        <p>&nbsp;</p>
      </div></td>
    </tr>
</table>
</body>
</html>
