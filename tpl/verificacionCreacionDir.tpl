<html>
    <head><title>{TITULO_FORM}</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <link rel="stylesheet" href="estilos/orfeo.css">
    </head>
    <body bgcolor="#FFFFFF">
        <form name="form1" method="post" action="{ACTION_FORM}" onSubmit="return validar_nombre();">
            <input type="hidden" name="PHPSESSID" value="{PHPSESS_ID}"/>
            <table  width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
                <tr>
                    <td width="3%" class="listado2">
                        <a href="{HREF_BORRADO}">
                            <img src="./iconos/carpeta_azul_eliminar.gif" border="0" Alt="Borrar Carpetas">Borrar Carpeta Informados
                        </a>
                    </td>
                    <td width="97%" class="titulos4" align="center">{TITULO_INF}</td>
                </tr>
            </table>
            <br>
            <table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
                <tr>
                    <td class="listado2_center">
                        La creacion de la carpeta <b>{CARPETA_INF}</b> ha sido EXITOSA por favor recargue el menu del lado izquierdo para verla.
                    </td>
                </tr>
            </table>
        </form>
        <table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
            <tr>
                <td class="listado2_center" height="25" >
La descripci&oacute;n de la carpeta le recordara el destino final de la misma. Esto se puede ver pasando el mouse sobre cada una de las carpetas.
                </td>
            </tr>
        </table>
    </body>
</html>
