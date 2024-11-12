<html>
    <head>
        <title>CREACI&Oacute;N  DE DIRECTORIOS Y INICIALIZACI&Oacute;N DE SECUENCIAS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="stylesheet" href="../../estilos/orfeo.css">
    </head>
    <body>
        <form name="frmDirSec" action="./ejecutarIniciarSecDir.php" method="post">
            <table width="32%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
                <tr bordercolor="#FFFFFF">
                    <td colspan="2" class="titulos4">
                        <div align="center">
                            <strong>{TITULO}</strong>
                        </div>
                    </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="center" class="listado2" width="98%">
						Directorio a crear:
                        <select name="directorio">
                            <!-- BEGIN directorio -->
                            <option value="{SELECT_ANOS}">
                                {SELECT_ANOS}
                            </option>
                            <!-- END directorio -->
                        </select>
                    </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="center" class="listado2" width="98%">
                    	VALOR DE SECUENCIAS DE DOCUMENTOS 
                    </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="center" class="listado2" width="98%">
						Inicializar secuencias: Si 
						 <input name="iniciarSec" type="radio" value="1">
					  No
					  	<input name="iniciarSec" type="radio" value="0" checked>
                </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                <td>
                     <table>
                <!-- BEGIN row -->
                     <tr>
                     <td align="center" class="menu_princ" width="60%" bgcolor="c0ccca">
                        {NOMBRE_SEC} :
                     <td>
                     <td align="rigth" class="vinculos" width="40%">
                        {VALOR_SEC}
                     </td>
                     </tr>
                <!-- END row -->
                    </table>
                </td>
                </tr>
                <tr bordercolor="#FFFFFF">
                    <td align="center" class="listado2">
                        <center>
                            <input align="middle" class="botones" type="submit" name="Submit" value="EJECUTAR">
                        </center>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
