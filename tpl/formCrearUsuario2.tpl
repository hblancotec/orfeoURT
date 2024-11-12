<html>
    <head>
    <title>Creaci&oacute;n de Usuario</title>
    <link rel="stylesheet" href="../../estilos/orfeo.css">
    <script language="JavaScript" src="../../js/formchek.js"></script>
    <script language="javascript">
        // funcion para solo aceptar caracteres numericos en la cedula
        function numerico (inputText) {
        }
        
        function envio_datos() {
            if(!isPositiveInteger(document.forms[0].cedula.value)) {
                alert("No se ha diligenciado el Numero de la Cedula del Usuario, o a diligenciado un valor no numerico.");
                document.forms[0].cedula.focus();
                return false;
            }
            
            if(isWhitespace(document.forms[0].usuLogin.value)) {
                alert("El campo Login del Usuario no ha sido diligenciado.");
                document.forms[0].usuLogin.focus();
                return false;
            }
            
            if (!isPositiveInteger (document.forms[0].piso.value,true)) {
                alert("El campo Piso del Usuario debe ser numérico.");
                document.forms[0].piso.focus();
                return false;
            }
            
            if (!isPositiveInteger(document.forms[0].extension.value,true)) {
                alert("El campo Extension del Usuario debe ser numérico.");
                document.forms[0].extension.focus();
                return false;
            }
            
            if (!isEmail(document.forms[0].email.value,true)) {
                alert("El campo mail del Usuario no tiene formato correcto.");
                document.forms[0].email.focus();
                return false;
            }
            
            if (!isYear(document.forms[0].ano.value ,true)){
                alert("El campo ano del Usuario no tiene formato correcto.");
                document.forms[0].ano.focus();
                return false;
            }

            if(isWhitespace(document.forms[0].nombre.value)) {
                alert("El campo de Nombres y Apellidos no ha sido diligenciado.");
                document.forms[0].nombre.focus();
                return false;
            } else {
                document.forms[0].submit();
                return true;
            }
        }
    </script>
    </head>
    <body>
    <form name="frmCrear" action="{ACTION_FORM}" method="post">
    <table width="93%"  border="1" align="center">
        <tr bordercolor="#FFFFFF">
        <td colspan="2" class="titulos4">
            <center>
                <p><b><span class="etexto">ADMINISTRACION DE USUARIOS Y PERFILES</span></b></p>
                <p><b><span class="etexto">{TITULO_FORM}</span></b> </p>
            </center>
        </td>
        </tr>
    </table>
    <table border="1" width=93% class="t_bordeGris" align="center">
        <tr class="timparr">
            <td class="titulos2" height="26">DOCUMENTO DE IDENTIDAD:&nbsp;</td>
            <td class="titulos2" height="26">
                <input {LECTURA} type="text" name="cedula" id="cedula" value="{CEDULA}" size="15" maxlenght="14">
            </td>
        </tr>
        <tr class="timparr">
            <td class="titulos2" height="26">LOGIN:&nbsp;</td>
            <td class="titulos2" height="26">
                <input {LECTURA} type="text" name="usuLogin" id="usuLogin" value="{USUA_LOGIN}" size="20" maxlenght="15">
            </td>
        </tr>
        <tr class="timparr">
            <td width="40%" height="26" class="titulos2">MAIL:&nbsp;</td>
            <td width="60%" height="26" class="titulos2">
                <input type="text" name="email" id="email" value="{EMAIL}" size="40">
            </td>
        </tr>
    </table>
    <table border="1" width="93%" class="t_bordeGris" align="center">
    <tr class="timparr">
        <td width="40%" height="26" class="titulos2">NOMBRES Y APELLIDOS:&nbsp;</td>
        <td width="60%" height="26" class="titulos2">
            <input type="text" name="nombre" id="nombre" value="{NOMBRE}" size="50" maxlenght="45">
        </td>
    </tr>
    <tr class="timparr">
        <td width="40%" class="titulos2" height="26">FECHA DE NACIMIENTO:&nbsp;</td>
        <td width="60%" class="titulos2">
            <select name="dia" id="select">
            {MOSTRAR_DIAS}
            </select>
            <select name="mes" id="select2">
            {MOSTRAR_MESES}
            </select>
            <input name="ano" type="text" id="ano" size="4" maxlength="4" value="{ANO}">&nbsp;(dd/mm/yyyy)
        </td>
    </tr>
    </table>
    <table border=1 width=93% class="t_bordeGris" align="center">
        <tr class="timparr">
            <td width="40%" height="26" class="titulos2">UBICACION AT:&nbsp;</td>
            <td width="60%" height="26" class="titulos2">
            <input type="text" name="ubicacion" id="ubicacion" value="{UBICACION}" size="20">
            </td>
        </tr>
        <tr class="timparr">
            <td width="40%" height="26" class="titulos2">PISO:&nbsp;</td>
            <td width="60%" height="26" class="titulos2">
                <input type="text" name="piso" id="piso" value="{PISO}" size="10">
            </td>
        </tr>
        <tr class="timparr">
            <td width="40%" height="26" class="titulos2">EXTENSION:&nbsp;</td>
            <td width="60%" height="26" class="titulos2">
                <input type="text" name="extension" id="extension" value="{EXTENSION}" size="10">
            </td>
        </tr>
    </table>
    <table border="1" width="93%" class="t_bordeGris" align="center">
    </table>
    <table border="1" width=93% class="t_bordeGris" align="center">
        <tr class="timparr">
            <td height="30" colspan="2" class="listado2">
                <span class="celdaGris">
                    <span class="e_texto1">
                    <center>
                        <input class="botones" type="button" name="reg_crear" id="Continuar_button" Value="Continuar" onClick="envio_datos();">
                    </center>
                    </span>
                </span>
                </td>
                <td height="30" colspan="2" class="listado2"><span class="celdaGris"> <span class="e_texto1">
                        <center>
                            <a href="{ENLACE_CANCELAR}">
                                <input class="botones" type="button" name="Cancelar" id="Cancelar" Value="Cancelar">
                            </a>
                        </center>
                    </span>
                </span>
            </td>
        </tr>
    </table>
    </form>
    </body>
</html>
