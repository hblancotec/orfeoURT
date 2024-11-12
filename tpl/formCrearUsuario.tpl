<html>
    <head>
    <title>Creaci&oacute;n de Usuario</title>
    <link rel="stylesheet" href="../../estilos/orfeo.css">
    <script language="JavaScript" src="../../js/formchek.js"></script>
    <script language="javascript">
        function envio_datos() {
            if(document.forms[0].perfil.value == "Jefe") {
                if(document.forms[0].nombreJefe.value == "") {
                } else {
                    alert("En la dependencia " + document.forms[0].dep_sel.value +
                            ", ya existe un usuario jefe, " + document.forms[0].nombreJefe.value +
                            ", por favor verifique o realice los cambios necesarios para poder continuar con este proceso");
                    document.forms[0].perfil.focus();
                    return false;
                }
            }

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
                alert("El campo a~no del Usuario no tiene formato correcto.");
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
        <p><b><span class="etexto">{TITULO_FORM}</span></b> </p></center>
        </td>
        </tr>
    </table>
    <table border="1" width="93%" class="t_bordeGris" align="center">
        <tr class="timparr">
            <td class="titulos2" height="26">Perfil</td>
            <td class="listado2" height="1">
                <select name="perfil" class="select">
                    <option value="{PERF_1}">{PERF_1}</option>
                    <option value="{PERF_2}">{PERF_2}</option>
                </select>
            </td>
            <td class="titulos2" height="26">Dependencias</td>
            <td class="listado2" height="1" align="center">
                <div name="tituloTablas" id="tituloTablas">
                    <table class="borde_tab">
                        <tr>
                            <th align="center" width="240px"><span class="titulos3">NOMBRE DENPENDENCIA</span></th>
                            <th align="center" width="80px"><span class="titulos3">PERTENECE A</span></th>
                            <th align="center" width="80px"><span class="titulos3">POR DEFECTO</span></th>
                        </tr>
                    </table>
                </div>
                <div name="dependencias" id="dependencias">
                    <table>
                        <!-- BEGIN row -->
                        <tr class="{ESTILO_FILA}">
                            <td width="240px">{DEPE_NOMBRE}</td>
                            <td align="center" width="80px">
                                <input type="checkbox" name="dependencias[][check]" value="{CHECK}" {CHEQUEAR}>
                            </td>
                            <td align="center" width="80px">
                                <input type="radio" name="dependencias[][default]" value="{DEFAULT}" {DEFECTO}>
                            </td>
                        </tr>
                        <!-- END row -->
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <table border="1" width=93% class="t_bordeGris" align="center">
        <tr class="timparr">
            <input name="nombreJefe" type="hidden" value="{NOMBRE_JEFE}">
            <input name="cedulaYa" type="hidden" value="{CEDULA_YA}">
            <td class="titulos2" height="26">
                Nro Cedula <input {LECTURA} type="text" name="cedula" id="cedula" value="{CEDULA}" size="15" maxlenght="14" >
            </td>
            <td class="titulos2" height="26">Usuario <input {LECTURA} type="text" name="usuLogin" id="usuLogin" value="{USUA_LOGIN}" size="20" maxlenght="15">
            </td>
        </tr>
    </table>
    <table border="1" width="93%" class="t_bordeGris" align="center">
        <tr class="timparr">
        <td width="46%" height="26" class="titulos2">Nombres y Apellidos <input type="text" name="nombre" id="nombre" value="{NOMBRE}" size="50" maxlenght="45">
        </td>
        <td class="titulos2" height="26">Fecha de Nacimiento</td>
        <td width="80%" class="titulos2">
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
    <table border=1 width=93% class=t_bordeGris align="center">
        <tr class=timparr>
            <td width="40%" height="26" class="titulos2">Ubicacion AT <input type="text" name="ubicacion" id="ubicacion" value="{UBICACION}" size="20">
            </td>
            <td width="32%" height="26" class="titulos2">Piso <input type="text" name="piso" id="piso" value="{PISO}" size="10">
            </td>
            <td width="28%" height="26" class="titulos2">Extension <input type="text" name="extension" id="extension" value="{EXTENSION}" size="10">
            </td>
        </tr>
    </table>
    <table border=1 width=93% class="t_bordeGris" align="center">
    <tr class="timparr">
    <td width="40%" height="26" class="titulos2">
        Mail&nbsp;<input type="text" name="email" id="email" value="{EMAIL}" size="40">
    </td>
    <td width="40%" height="26" class="titulos2">
        Mail 2&nbsp;<input type="text" name="email1" id="email1" value="{EMAIL1}" size="40">
    </td>
    <td width="40%" height="26" class="titulos2">
        Mail 3&nbsp;<input type="text" name="email2" id="email2" value="{EMAIL2}" size="40">
    </td>
    <td width="60%" height="26" class="listado2"></td>
    <input type="hidden" name="entrada" 		id="entrada" value='{ENTRADA}'>
    <input type="hidden" name="modificaciones" 	id="modificaciones" value='{MODIFICACIONES}'>
    <input type="hidden" name="masiva" 			id="masiva" value='{MASIVA}'>
    <input type="hidden" name="impresion" 		id="impresion" value='{IMPRESION}'>
    <input type="hidden" name="exp_temas" 		id="exp_temas" value='{TEMAS_EXPEDIENTES}'>
	<input type="hidden" name="$ccalarmas" 		id="$ccalarmas" value='{CC_ALARMAS}'>
	<input type="hidden" name="permDespla" 		id="permDespla" value='{PERMDESPLA}'>
	<input type="hidden" name="no_trd" 			id="no_trd" value='{NO_TRD}'>
    <input type="hidden" name="s_anulaciones" 	id="s_anulaciones" value='{S_ANULACIONES}'>
    <input type="hidden" name="anulaciones" 	id="anulaciones" value='{ANULACIONES}'>
    <input type="hidden" name="adm_archivo" 	id="adm_archivo" value='{ADM_ARCHIVO}'>
    <input type="hidden" name="dev_correo" 		id="dev_correo" value='{DEV_CORREO}'>
    <input type="hidden" name="adm_sistema" 	id="adm_sistema" value='{ADM_SISTEMA}'>
    <input type="hidden" name="env_correo" 		id="env_correo" value='{ENV_CORREO}'>
    <input type="hidden" name="reasigna" 		id="reasigna" value='{REASIGNAR}'>
    <input type="hidden" name="estadisticas" 	id="estadisticas" value='{ESTADISTICAS}'>
    <input type="hidden" name="usua_activo" 	id="usua_activo" value='{USUA_ACTIVO}'>
    <input type="hidden" name="usua_nuevoM" 	id="usua_nuevoM" value='{USUA_NUEVOM}'>
    <input type="hidden" name="nivel" 			id="nivel" value='{NIVEL}'>
    <input type="hidden" name="usuDocSel" 		id="usuDocSel" value='{USUDOCSEL}'>
    <input type="hidden" name="usuLoginSel" 	id="usuLoginSel" value='{USULOGINSEL}'>
    <input type="hidden" name="perfilOrig" 		id="perfilOrig" value='{PERFILORIG}'>
    <input type="hidden" name="nusua_codi" 		id="nusua_codi" value='{NUSUA_CODI}'>
		
		<!--Inicio Permisos para Acciones masivas panel principal cuerpo.php-->
		<input type="hidden" name="accMasiva_trd" 		id="accMasiva_trd" 		value='{ACCMASIVA_TRD}'>
		<input type="hidden" name="accMasiva_incluir" 	id="accMasiva_incluir" 	value='{ACCMASIVA_INCLUIR}'>
		<input type="hidden" name="accMasiva_prestamo" 	id="accMasiva_prestamo" value='{ACCMASIVA_PRESTAMO}'>		
		<input type="hidden" name="accMasiva_temas" 	id="accMasiva_temas" 	value='{ACCMASIVA_TEMAS}'>			
		<!--Fin Permisos para Acciones masivas panel principal cuerpo.php-->
		
        </tr>
    </table>
    <table border=1 width=93% class="t_bordeGris" align="center">
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
