<html>
    <head>
        <title>Actualizaci&oacute;n Anexo</title>
        <script language="javascript">
            function f_close(){
                opener.regresar();
                window.close();
            }

            function regresar(){
                f_close();
            }
        </script>
        <link rel="stylesheet" href="./estilos/orfeo.css">
    </head>
    <body>
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
        <tr>
            <td height="25" align="center" class="titulos4" colspan="4">ANEXO ({TIPO_OPERACION})</td>
        </tr>
        <tr>
            <td height="23" align="left" colspan="1" class="listado2">Tipo de Anexo:</td>
            <td height="23" align="left" colspan="3" class="listado2">{TIPO_ANEXO}</td>
        </tr>
        <tr>
            <td height="23" align="left" colspan="1" class="listado2">Tipo Radicado:</td>
            <td height="23" align="left" colspan="3" class="listado2">{TIPO_RADICADO}</td>
        </tr>
        <!--<tr>
            <td height="25" align="center" class="titulos4" colspan="4">Destinatario(s):</td>
        </tr>
        <tr>
            <td height="25" align="center" class="titulos4">NOMBRE</td>
            <td height="25" align="center" class="titulos4">TIPO</td>
            <td height="25" align="center" class="titulos4">MEDIO DE ENVIO</td>
            <td height="25" align="center" class="titulos4">DIRECCI&Oacute;N</td>
        </tr>
        <tr>
            <td height="23" align="left" class="listado2">{TIPO_DESTINARIO}</td>
            <td height="23" align="left" class="listado2">{NOMBRE_DESTINATARIO}</td>
            <td height="23" align="left" class="listado2">{MEDIO_ENVIO}</td>
            <td height="23" align="left" class="listado2">{DIRECCION_DESTINATARIO}</td>
        </tr>
        -->
        <tr>
            <td height="25" align="center" class="titulos4" colspan="4">Descripcion:</td>
        </tr>
        <tr>
            <td height="23" align="left" class="listado2" class="titulos4" colspan="4">(Esta descripci&oacute;n tambien sera parte del asunto del radicado en el caso que el Anexo se radique)</td>
        </tr>
        <tr>
            <td height="23" align="left" class="listado2" class="titulos4" colspan="4">{DESCRIPCION_ANEXO}</td>
        </tr>
        <tr>
            <td class="celdaGris" height="25" align="center" colspan="4">
                <span class="etextomenu">
                    <input type='button' class ='botones' value='cerrar' onclick='f_close()'>
                </span>
            </td>
        </tr>
    </table>
    </body>
</html>
