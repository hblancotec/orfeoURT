<html>
    <head><title>{TITULO_FORM}</title>
    <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
    <link rel="stylesheet" href="estilos/orfeo.css">
    </head>
    <script language="javascript">
    /** Esta funcion de Javascript valida el texto introducido 
      * por el usuario y evita que este ingrese carácteres especiales 
      * evitando de este modo el error que por esto se esta presentando
      * Realizado por: Brayan Gabriel Plazas Riaño - DNP
      * Fecha: 13 de Julio de 2005*/
    function validar_nombre() {
        var iChars = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚabcdefghijklmnñopqrstuvwxyzáéíóú_-1234567890";
        var msg = "El nombre de la carpeta tiene signos especiales. \n";
        msg += "Por favor remueva estos signos especiales e intentelo de nuevo."; 
        msg += " Solamente puede contener Letras y Numeros.";
        for (var i = 0; i < document.form1.nombcarp.value.length; i++) {
            if ((iChars.indexOf(document.form1.nombcarp.value.charAt(i)) == -1)) {
                alert (msg);
                document.form1.nombcarp.focus();
                return false;
            }
        }
    }
    </script>
    <body bgcolor="#FFFFFF">
        <form name="form1" method="post" action="{ACTION_FORM}" onSubmit="return validar_nombre();">
            <input type="hidden" name="PHPSESSID" value="{PHPSESS_ID}"/>
            <table  width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
                <tr>
                    <td width="3%" class="listado2">
                        <a href="{HREF_BORRADO}">
                            <img src="./iconos/carpeta_azul_eliminar.gif" border="0" Alt="Borrar Carpetas">Borrar Subcarpeta Informados
                        </a>
                    </td>
                    <td width="97%" class="titulos4" align="center">{TITULO_INF}</td>
                </tr>
            </table>
            <br>
            <center><font class="titulos4"><b>{ERROR_CREACION}</b></font></center>
            <table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
                <tr>
                    <td class="titulos2" align="right">Nombre de carpeta :</td>
                    <td class="listado2">
                        <input name="carpetaInf[nombre]" id="nombcarp" type="text" class="tex_area" size="25" maxlength='10'>
                    </td>
                </tr>
                <tr>
                    <td class="titulos2" align="right">Descripci&oacute;n :</td>
                    <td class="listado2">
                        <input name="carpetaInf[descripcion]" type="text" class="tex_area" size="25" maxlength="30">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div align="center">
                            <input type="submit" class="botones" value="Crear Ahora!" name="crear">
                            <input type="hidden" value="{KRD}" name="krd">
                        </div>
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
