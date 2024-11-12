<html>
    <head>
        <title>Eliminar Subcarpetas Informados</title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
        <link rel="stylesheet" href="./estilos/orfeo.css">
    </head>
    <body>
        <table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab" align="center">
            <tr>
                <td width="97%" class="titulos4" align="center">
                    ELIMINAR SUBCARPETAS INFORMADOS
                </td>
            </tr>
        </table>
        <br>
        <table>
            <tbody>
                <tr>
                    <td align="center"><div align="center" class="info"><b>{CARPETA_ELIMINADA}</b></div></td>
                </tr>
            </tbody>
        </table>
        <form name="form1" method="post" action="{ACTION_FORM}">
            <input type="hidden" name="PHPSESSID" value="{PHPSESS_ID}">
            <input type="hidden" name="krd" value="{USUA_LOGIN}">
            <input type="hidden" name="usua_doc" value="{USUA_DOC}">
            <input type="hidden" name="depe_codi" value="{DEPE_CODI}">
            <table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab" align="center">
                <td width='97%' class="listado2_center"> 
                    <p align='center' class='etextomenu'>Solo se pueden eliminar las carpertas que se encuentren vacias</p>
                    <p align='center' class='etextomenu'>Estas son las carpetas que usted tiene vacias en este momento:</p>
                </td>
                </tr>
            </table>
            <table width="98%" border="0" cellpadding="0" cellspacing="5" align="center">
                <tr>
                    <td align="center">
                        <select name="carpetaCodigo" onChange='procEst(formulario,18, )' class='select'>
                            <option value="N">-- Seleccione la subcarpeta informados --</option>
                            <!-- BEGIN subcarpetas -->
                            <option value="{VALUE_CARPETA}">{NOMBRE_CARPETA}</option>
                            <!-- END subcarpetas -->
                        </select>
                        <br><br>
                        <input type="submit" name="BorrarCarp" value="Borrar Carpeta" class="botones">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
