<?php
session_start();
$ruta_raiz = "../..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

if ($_SESSION['usua_admin_sistema'] != 1){
	die(include "../../sinpermiso.php");
	exit;
}

$ADODB_COUNTRECS = false;

$ruta_raiz = "../..";
include_once($ruta_raiz . '/config.php');

if (!isset($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";

include_once($ruta_raiz . "/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
include_once($ruta_raiz . "/include/query/busqueda/busquedaPiloto1.php");
//$db->conn->debug = true;

$perfil = $_GET['Item'];
$usModo = $_GET['usModo'];
$valRadio = $_POST['valRadio'];
################################################
### ENTRA SI VIEN POR EDICIÓN DE USUARIOS
if ($usModo == 2) {
    if ($valRadio) {
        $usuario_mat = explode("-", $valRadio, 2);
        $usuDocSel = $usuario_mat[0];
        $usuLoginSel = $usuario_mat[1];

        include ("./traePermisos.php");
		$envios = 0;
		$enviosExt = 0;
		if ($env_correo == 1){
			$envios = 1;
		}
		elseif ($env_correo == 2) {
			$enviosExt = 1;
		}
		elseif ($env_correo == 3) {
			$envios = 1;
			$enviosExt = 1;
		}
    }
} else {
    $usua_activo = 1;
    $usua_nuevoM = 0;
	$notifReasignacion = 1;
    $usua_Archivar = 1;
    $autenticaLDAP = 1;
    $perm_servweb = 0;
    $nivel = 1;
    $cedula = '';
}

################################################
?>

<html>
    <head>
        <title> Orfeo - Administrador de Usuarios </title>
        <link href="<?php echo $ruta_raiz ?>/estilos/orfeo.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo $ruta_raiz ?>/estilos/tabber.css" TYPE="text/css" MEDIA="screen">
        <script src="../../js/prototype.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $ruta_raiz ?>/js/tabber.js"></script>
        <script type="text/javascript" src="<?php echo $ruta_raiz ?>/js/jquery-1.8.2.js"></script>
        <script type="text/javascript" src="<?php echo $ruta_raiz ?>/js/jquery-ui.js"></script>
        <script type="text/javascript" language="JavaScript">
            document.write('<style type="text/css">.tabber{display:none;}<\/style>');
            var tabberOptions =
                    {
                        /* Optional: instead of letting tabber run during the onload event,
                         we'll start it up manually. This can be useful because the onload
                         even runs after all the images have finished loading, and we can
                         run tabber at the bottom of our page to start it up faster. See the
                         bottom of this page for more info. Note: this variable must be set
                         BEFORE you include tabber.js.
                         */
                        'manualStartup': true,
                        /* Optional: code to run after each tabber object has initialized */
                        'onLoad': function(argsObj)
                        {
                            /* Display an alert only after tab2
                             if (argsObj.tabber.id == 'tab1')
                             {       crea_var_idlugar_defa('<?= $muni_us1 ?>');  }*/
                        },
                        /* Optional: set an ID for each tab navigation link */
                        'addLinkId': true
                    };

            function ValidarInformacion(accion)
            {
            	debugger;
            	
            	var modo = document.getElementById("usModo").value;
            	if (modo == 1)  { 
            	
    				var login = document.getElementById("usuLogin").value;
    				var cedula = document.getElementById("cedula").value;
    				
    				var valida = false;
    				
    				var parametros = {
                    	"username" : login,
                    	"usuaDoc" : cedula
                    };
                        	
                    $.ajax({
                    	url: './validaNew.php',
                        type: 'POST',
                        cache: false,
                        async: false,
                        data:  parametros,
                        success: function(resultado) {
                        	debugger;
                        	if (resultado == "Login ya existe ") {
                            	alert(resultado);
                            	valida = true;
                            }
                            if (resultado == "Documento ya existe ") {
                            	alert(resultado);
                            	valida = true;
                            }  
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
            				alert('Se ha producido un error ' + jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
            			}
                    });
    				
    				if (valida) {
    					return false;
    				}
    			} 
				
                if (document.formAdmUsr.perfil.value == '') {
                    alert("Debe seleccionar un Perfil para el usuario");
                    document.formAdmUsr.perfil.focus();
                    return false;
                }

                else if (document.formAdmUsr.cedula.value == '') {
                    alert("El documento de identificacion es obligatorio");
                    document.formAdmUsr.cedula.focus();
                    return false;
                }

                else if (document.formAdmUsr.nombre.value == '') {
                    alert("Debe digitar el nombre del usuario");
                    document.formAdmUsr.nombre.focus();
                    return false;
                }

                else if (document.formAdmUsr.usuLogin.value == '') {
                    alert("Debe digitar un Login para el usuario");
                    document.formAdmUsr.usuLogin.focus();
                    return false;
                }

                else if (document.formAdmUsr.perfil.value == 'auditor') {
                    if (document.formAdmUsr.Slc_ano.value == '') {
                        alert("Debe marcar al menos un ano permitido");
                        document.formAdmUsr.Slc_ano.focus();
                        return false;
                    }
                    if (document.formAdmUsr.Slc_deps.value == '') {
                        alert("Debe marcar al menos una dependencia permitida");
                        document.formAdmUsr.Slc_deps.focus();
                        return false;
                    }
                }

                else if (document.formAdmUsr.perfil.value == 'Jefe' && document.formAdmUsr.usua_activo.value == 2) {
                    if (document.formAdmUsr.Slc_enc.value == '') {
                        document.formAdmUsr.Slc_enc.focus();
                        return false;
                    }
                }

                else if (document.formAdmUsr.respuesta.checked) {
                    if (document.formAdmUsr.email1.value == '') {
                        alert("Debe digitar una cuenta de correo en el campo Mail 2");
                        document.formAdmUsr.email1.focus();
                        return false;
                    }
                }

                else {
                    document.formAdmUsr.submit()
                }
            }

            function cancelar()
            {
                window.location.href = "./mnuUsuarios.php?<?= session_name() . '=' . session_id() . '&krd=$krd' ?>";
            }

            function comprobarLogin(login)
            {			
				var parametros = {
                	"username" : login
                };
                    	
                $.ajax({
                	url: './validaNew.php',
                    type: 'POST',
                    cache: false,
                    async: false,
                    data:  parametros,
                    success: function(resultado) {
                    	debugger;
                    	if (resultado == "Login ya existe ") {
                        	$('#comprobar_mensaje').html("Login ya existe");
                        } else {
                        	$('#comprobar_mensaje').html("");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
        				alert('Se ha producido un error ' + jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
        			}
                });
								
				/*var url = './validaNew.php';
				var pars = ('username=' + login);
				var myAjax = new Ajax.Updater('comprobar_mensaje', url, {method: 'get', parameters: pars});*/
            }
			

            function comprobarCedula(cedula)
            {
            	var parametros = {
                	"usuaDoc" : cedula
                };
                    	
                $.ajax({
                	url: './validaNew.php',
                    type: 'POST',
                    cache: false,
                    async: false,
                    data:  parametros,
                    success: function(resultado) {
                    	debugger;
                    	if (resultado == "Documento ya existe ") {
                        	$('#comprobar').html("Documento ya existe");
                        } else {
                        	$('#comprobar').html("");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
        				alert('Se ha producido un error ' + jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
        			}
                });
                
                /*var url = './validaNew.php';
                var pars = ('usuaDoc=' + cedula);
                var myAjax = new Ajax.Updater('comprobar', url, {method: 'get', parameters: pars});*/
            }

            function fperfil(perfil, login)
            {
            	debugger;
            	var parametros = {
                	"perfil" : perfil,
                	"login" : login
                };
                    	
                $.ajax({
                	url: './validaNew.php',
                    type: 'POST',
                    cache: false,
                    async: false,
                    data:  parametros,
                    success: function(resultado) {
                    	debugger;
                       	$('#permitidas').html(resultado);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
        				alert('Se ha producido un error ' + jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
        			}
                });
                
                /*var url = './validaNew.php';
                var pars = ('perfil=' + perfil + '&login=' + login);
                var myAjax = new Ajax.Updater('permitidas', url, {method: 'get', parameters: pars});*/
            }

            function festado(estado)
            {
            	debugger;
            	var rol = document.formAdmUsr.perfil.value;
            	var depeCodi = document.formAdmUsr.dep_sel.value;
            	
            	var parametros = {
                	"estado" : estado,
                	"rol" : rol,
                	"depeCodi" : depeCodi
                };
                    	
                $.ajax({
                	url: './validaNew.php',
                    type: 'POST',
                    cache: false,
                    async: false,
                    data:  parametros,
                    success: function(resultado) {
                    	debugger;
                       	$('#encargado').html(resultado);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
        				alert('Se ha producido un error ' + jqXHR.responseText + " | " + textStatus + " | " + errorThrown);
        			}
                });
                
                /*var url = './validaNew.php';
                var pars = ('estado=' + estado + '&rol=' + rol + '&depeCodi=' + depeCodi);
                var myAjax = new Ajax.Updater('encargado', url, {method: 'get', parameters: pars});*/
            }
			
        </script>

<?php
################################################
### Se define el ESTADO del usuario seleccionado
$ac = '';
$in = '';
$va = '';

if ($usua_activo == 2) {
    $va = 'selected'; //Vacaciones
    echo "<script languaje='javascript'> ";
    echo "festado('" . $usua_activo . "','" . $perfil . "','" . $dep_sel . "')";
    echo "</script>";
} elseif ($usua_activo == 0) {
    $in = 'selected'; //Inactivo
} elseif ($usua_activo == 1) {
    $ac = 'selected'; //Activo
}
################################################
################################################
### Se define el PERFIL del usuario seleccionado
$n = '';
$j = '';
$a = '';

if ($perfil == 'Auditor') {
    $a = 'selected';
    echo "<script languaje='javascript'> ";
    echo "fperfil('" . $perfil . "','" . $usuLoginSel . "')";
    echo "</script>";
} elseif ($perfil == 'Jefe') {
    $j = 'selected';
} elseif ($perfil == 'Normal') {
    $n = 'selected';
}
################################################
?>

    </head>
    <body>
        <form name="formAdmUsr" id="formAdmUsr" method="post" action="./grabar.php">

            <table width="100%" border="1" align="center" class="t_bordeGris">
                <tr>
                    <td width="100%" colspan="2" height="40" align="center" class="titulos4">
                        <b> ADMINISTRADOR DE USUARIOS</b>
                    </td>
                </tr>
            </table>

            <input name="PHPSESSID" type="hidden" value='<?= session_id() ?>'>
            <input id="krd" name="krd" type="hidden" value='<?= $krd ?>'>
            <input id="usuaCodi" name="usuaCodi" type="hidden" value='<?= $usuaCodi ?>'>
            <input id="usModo" name="usModo" type="hidden" value='<?= $usModo ?>'>
            <input name="perfilOrig" type="hidden" value='<?= $perfil ?>'> 

            <div class="tabber" id="tab1">

                <!-- ### SECCION DE INFORMACION GENERAL DEL USUARIO -->
                <div class="tabbertab" title="Inf. General">
                    <br>
                    <table width="100%" border="1" align="center" class="t_bordeGris" cellspacing="5">
                        <tr class=timparr>
                            <td class="titulos2" width="11%"> <b> Perfil: </b> </td>
                            <td class="listado2" width="22%">
                                <select required name="perfil" id="perfil" class='select' onChange="fperfil(this.value, document.formAdmUsr.usuLogin.value)">
                                    <option value="" selected>&lt; Seleccione &gt;</option>
                                    <option value="Normal" <?= $n ?>>Normal</option>
                                    <option value="Jefe" <?= $j ?>>Jefe</option>
                                    <option value="Auditor" <?= $a ?>>Auditor</option>
                                </select>
                            </td>

                            <td class="titulos2" width="11%"><b> Dependencia: </b></td>
                            <td class="listado2" colspan="3">

<?php
$sql = "SELECT	CAST(DEPE_CODI AS char(4))" . $db->conn->concat_operator . "' - '" . $db->conn->concat_operator . "DEPE_NOMB AS DEPE_NOMB,
				DEPE_CODI AS DEPE_CODI
		FROM	DEPENDENCIA
		ORDER BY DEPE_NOMB";

print "<select name='dep_sel'  class='select'>";
$rs = $db->conn->Execute($sql);
do {
    $codigo = $rs->fields["DEPE_CODI"];
    $depnombre = $rs->fields["DEPE_NOMB"];
    $datoss = "";
    if ($dep_sel == $codigo) {
        $datoss = " selected ";
    }
    echo "<option value='$codigo' $datoss> $depnombre </option>\n";
    $rs->MoveNext();
} while (!$rs->EOF);
?>

                            </td>
                        </tr>
                    </table> 	

                    <div id="permitidas"> </div>

                    <table width="100%" border="1" align="center" class="t_bordeGris" cellspacing="5">
                        <tr class="timparr">
                            <input name="nombreJefe" type="hidden" value="{NOMBRE_JEFE}">
                            <input name="cedulaYa" type="hidden" value="{CEDULA_YA}">

                            <td class="titulos2" width="10%"> No. C&eacute;dula: </td>
                            <td class="listado2" width="20%"> 
                                <input type="text" name="cedula" id="cedula" value="<?php echo $cedula; ?>" 
    								<?php if ($usModo == 2) { ?> Readonly <?php } ?> 
    								size="20" maxlenght="14" required onKeyUp="comprobarCedula(this.value)">
    								<?php if ($usModo != 2) { ?> <span id="comprobar" style="color: #FF0000; font-size: 10pt"> </span> <?php } ?>
                            </td>
    
                            <td class="titulos2" width="10%"> Nombre: </td>
                            <td class="listado2" width="15%" > 
                                <input type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" size="70" maxlenght="35" required /> 
                            </td>
    
    						<td class="titulos2" width="10%"> No. C&eacute;dula Firma:</td>
                            <td class="listado2" width="15%" > 
                                <input type="text" name="cedulafirma" id="cedulafirma" value="<?php echo $cedulafirma; ?>" size="20" maxlenght="14" /> 
                            </td>
                        
                        </tr>

                        <tr>
                            <td class="titulos2" width="10%"> Usuario: </td>
                            <td class="listado2" width="20%"> 
                                <input type="text" name="usuLogin" id="usuLogin" value="<?php echo $usuLogin; ?>" <?php if ($usModo == 2) { ?> Readonly <?php } ?> size="20" maxlenght="15" required  onKeyUp="comprobarLogin(this.value)"> 
<?php if ($usModo != 2) { ?>
                                    <span id="comprobar_mensaje" style="color: #FF0000; font-size: 10pt"> </span>
<?php } ?>
                            </td>

                            <td class="titulos2" width="10%"> Estado: 
<?php
if ($estado == 2 && $usuaCodi == 1) {
    echo "<br>&nbsp; Encargado:";
}
?>
                            </td>
                            <td class="listado2" width="20%"> 
                                <select name="usua_activo" id="usua_activo" class="select" onChange="festado(this.value)">
                                    <option value="1" <?= $ac ?>>Activo</option>
                                    <option value="0" <?= $in ?>>Inactivo</option>
                                    <option value="2" <?= $va ?>>Inactivo por vacaciones</option>
                                </select>

                                <div id="encargado"> </div>

                            </td>

                            <td class="titulos2" width="15%"> Nivel de Seguridad: </td>
                            <td class="listado2" width="15%"> 
<?php
$contador = 1;
while ($contador <= 5) {
    echo "<input name='nivel' type='radio' value=$contador ";
    if ($nivel == $contador)
        echo "checked";
    else
        echo "";
    echo " >" . $contador;
    $contador = $contador + 1;
}
?>
                            </td>
                        </tr>
                        <tr>
                            <td class="titulos2" width="10%"> Mail 1&nbsp;: </td>
                            <td class="listado2" width="20%"> <input type="text" name="email" id="email" value="<?php echo $email; ?>" size="30"> </td>

                            <td class="titulos2" width="10%"> Mail 2&nbsp;: </td>
                            <td class="listado2" width="15%"> <input type="text" name="email1" id="email1" value="<?php echo $email1; ?>" size="30"> </td>

                            <td class="titulos2" width="10%"> Mail 3&nbsp;: </td>
                            <td class="listado2" width="15%"> <input type="text" name="email2" id="mail3" value="<?php echo $email2; ?>" size="30"> </td>
                        </tr>

                        <tr>
                            <td colspan="6">
                                <table width="100%" border="0" align="center" class="t_bordeGris" cellspacing="2">
                                    <tr>
                                        <td class="titulos2" width="5%"> Piso: </td>
                                        <td class="listado2" width="10%"> 
											<input type="text" name="piso" id="piso" value="<?php echo $piso; ?>" size="10"> 
										</td>

                                        <td class="titulos2" width="5%"> Extensi&oacute;n: </td>
                                        <td class="listado2" width="10%"> 
											<input type="text" name="extension" id="ext" value="<?php echo $extension; ?>" size="10"> 
										</td>

                                        <td class="titulos2" width="8%"> Usuario P&uacute;blico: </td>
                                        <td class="listado2" width="10%"> 
                                            <input type="checkbox" name="usua_publico" value="$usua_publico" 
												<?php if ($usua_publico) echo "checked"; else echo ""; ?>>
                                        </td>

                                        <td class="titulos2" width="8%"> Usuario Nuevo: </td>
                                        <td class="listado2" width="10%"> 
                                            <input type="checkbox" name="usua_nuevoM" value="$usua_nuevoM" 
												<?php if ($usua_nuevoM) echo "checked"; else echo ""; ?>>
                                        </td>
										
										<td class="titulos2" width="15%"> Recibe notificaciones de las reasignaciones de radicados: </td>
                                        <td class="listado2" width="10%"> 
                                            <input type="checkbox" name="notifReasignacion" value="$notifReasignacion" 
												<?php if ($notifReasignacion) echo "checked"; else echo ""; ?>>
                                        </td>
                                    </tr>
                                </table>	
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- ### SECCION DE PERMISOS DE RADICACION -->
                <div class="tabbertab" title="Tipos de radicaci&oacute;n">
                    <table width="80%" border="1" align="center" class="t_bordeGris" cellspacing="10">
                        <br>

<?php
$sql = "SELECT	SGD_TRAD_CODIGO, 
										SGD_TRAD_DESCR
								FROM	SGD_TRAD_TIPORAD
								ORDER BY SGD_TRAD_CODIGO";

$permRadicados = array();
$permRadicados[] = 'Sin permisos';
$permRadicados[] = 'Permiso para radicaci&oacute;n men&uacute; principal';
$permRadicados[] = 'Permiso para radicaci&oacute;n al anexar';
$permRadicados[] = 'Todos los permisos';

$ADODB_COUNTRECS = true;
$nombreRadicado = '';
$codigoRadicado = 0;

$rs_trad = $db->conn->Execute($sql);
if ($rs_trad->RecordCount() >= 0) {
    $i = 1;
    $cad = "perm_tp";
    while ($arr = $rs_trad->FetchRow()) {
        $nombreRadicado = $arr['SGD_TRAD_DESCR'];
        $codigoRadicado = $arr['SGD_TRAD_CODIGO'];

        //(is_int($i / 3)) ? print ""  : print "<tr align='left'>\n";
        if ($i == 1 OR $i == 4 OR $i == 7)
            echo "<tr align='left'>\n";
        else
            echo "";
        echo "<td $colSpan height='26' width='30%' class='listado2'>\n";
        echo "<table width='100%'>\n";
        $x = 0;
        echo "<tr>
										<td class='titulos4'>
											($codigoRadicado). $nombreRadicado
										</td>
									  </tr>\n";

        while ($x < 4) {
            $chk = ($x == ${$cad . $arr['SGD_TRAD_CODIGO']}) ? "checked" : "";
            echo "</tr><td class='listado2'><input type='radio' name='" . $cad . $arr['SGD_TRAD_CODIGO'] .
            "' id='" . $cad . $arr['SGD_TRAD_CODIGO'] . "' value='$x' $chk>" . $permRadicados[$x] .
            "</td></tr>\n";
            $x++;
        }
        echo "</table>";
        echo "</td>\n";

        (is_int($i / 3)) ? print "</tr>"  : print "";
        $i += 1;
    }
}
else
    echo "<tr><td align='center'> NO SE HAN GESTIONADO TIPOS DE RADICADOS</td></tr>";
$ADODB_COUNTRECS = false;
?>


                    </table>
                </div>


                <!-- ### SECCION DE PERMISOS DE ADMINISTRACION -->
                <div class="tabbertab" title="Administraci&oacute;n">
                    <table width="80%" border="1" align="center" class="t_bordeGris" cellspacing="10">
                        <br>
                        <tr class=timparr>

                            <td width="25%" height="26" class="listado2">
                                <table width='100%'>
                                    <tr> 
                                        <td class="titulos4"> <b> Expedientes </b>
                                        </td> 
                                    </tr>

<?php
echo $htmlDependencias;
$contador = 0;
$permTemasExp = array();
$permTemasExp[] = 'Sin Permisos';
$permTemasExp[] = 'Solo la Dependencia';
$permTemasExp[] = 'Dep. Padre';
$permTemasExp[] = 'Todas las dep.';
$permTemasExp[] = 'Migrar Expedientes';
while ($contador <= 4) {
    echo "<tr>\n";
    echo "<td class='listado2'><input name='exp_temas' type='radio' value='$contador'";
    if ($exp_temas == $contador)
        echo "checked";
    else
        echo "";
    echo " >" . $permTemasExp[$contador] . "</td>\n";
    echo "</tr>";
    $contador = $contador + 1;
}
?>
                                </table>
                            </td>

                            <td>
                                <table cellspacing="2">
                                    <tr>
                                        <td class="titulos2" width="15%"> 
                                            <input type="checkbox" name="tablas" value="$tablas" <?php if ($tablas) echo "checked";
                                    else echo ""; ?>>
                                            <b> T. R. D. </b> 
                                        </td>

                                        <td class="listado2" width="15%"> &nbsp;
                                            <input type="checkbox" name="prestamo" value="$prestamo" <?php if ($prestamo) echo "checked";
                                    else echo ""; ?>>
                                            <b> Pr&eacute;stamos </b>
                                        </td>
                                    </tr>

                                    <tr>	
                                        <td class="listado2" width="15%"> &nbsp;
                                            <input type="checkbox" name="adm_sistema" value="$adm_sistema" <?php if ($adm_sistema) echo "checked";
                                    else echo ""; ?>>
                                            <b> Sistema </b>
                                        </td>

										<td class="titulos2" width="15%"> 
                                            <input type="checkbox" name="notifAdm" value="$notifAdm" <?php if ($notifAdm) echo "checked";
                                    else echo ""; ?>>
                                            <b> Notificaciones Administrativas </b>
                                        </td>
                                        
                                    </tr>

                                    <tr>	
                                        <td class="titulos2" width="15%">
                                            <input type="checkbox" name="perm_servweb" value="$perm_servweb" <?php if ($perm_servweb) echo "checked";
                                    else echo ""; ?>>
                                            <b> Servicios Web </b>
                                        </td>

                                        <td class="listado2" width="15%"> &nbsp;
                                            <input type="checkbox" name="anulaciones" value="$anulaciones" <?php if ($anulaciones) echo "checked";
                                    else echo ""; ?>>
                                            <b> Anulaci&oacute;n </b> 
                                        </td>
                                    </tr> 

                                    <tr>

                                        <td class="listado2" width="15%"> &nbsp; 
                                            <input type="checkbox" name="perm_adminflujos" value="$perm_adminflujos" <?php if ($perm_adminflujos) echo "checked";
                                    else echo ""; ?>>
                                            <b> Editor de Flujos </b> 
                                        </td>

                                        <td class="titulos2" width="15%">  
                                            <input type="checkbox" name="envios" value="$envios" <?php if ($envios) echo "checked";
                                    else echo ""; ?>>
                                            <b> Env&iacute;os </b> 
                                        </td>
										
                                    </tr>

                                    <tr>	
                                        <td class="titulos2" width="15%">
                                            <input type="checkbox" name="adm_archivo" value="$adm_archivo" <?php if ($adm_archivo) echo "checked";
                                    else echo ""; ?>>
                                            <b> Archivo </b>
                                        </td>
                                        <td class="listado2" width="15%"> &nbsp;
                                            <input type="checkbox" name="enviosExt" value="$enviosExt" <?php if ($enviosExt) echo "checked";
                                    else echo ""; ?>>
                                            <b> Env&iacute;os (Nuevo) </b> 
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- ### SECCION DE PERMISOS VARIOS -->
                <div class="tabbertab" title="Otros permisos">
                    <table width="80%" border="1" align="center" class="t_bordeGris" cellspacing="10">
                        <br>
                        <tr class=timparr>
                            <td class="titulos2" width="16%"> 
                                <table width='100%'>
                                    <tr> 
                                        <td class="titulos4"> Crear Expedientes </td> 
                                    </tr>
                                    <?php
                                    $contador = 0;
                                    $permExpe = array();
                                    $permExpe[] = 'Sin permisos';
                                    $permExpe[] = 'Creaci&oacute;n';
                                    $permExpe[] = 'Creaci&oacute;n + Asig. Responsable (Dep)';
                                    $permExpe[] = 'Creaci&oacute;n + Asig. Responsable (Todos)';
                                    while ($contador <= 3) {
                                        echo "<tr>";
                                        echo "<td class='titulos2'><input name='usua_permexp' type='radio' value=$contador ";
                                        if ($usua_permexp == $contador)
                                            echo "checked";
                                        else
                                            echo "";
                                        echo " >" . $permExpe[$contador] . "</td>\n";
                                        echo "</tr>\n";
                                        $contador = $contador + 1;
                                    }
                                    ?>
                                </table>
                            </td>


                            <td class="listado2" width="16%"> 
                                <table width='100%' vertical-align="TOP">
                                    <tr> <td class="titulos4">Estad&iacute;sticas.</td> </tr>
                                    <?php
                                    $contador = 0;
                                    $permEsta = array();
                                    $permEsta[] = 'Solo del usuario';
                                    $permEsta[] = 'Solo la dependencia';
                                    $permEsta[] = 'Todas las dependencias';
                                    while ($contador <= 2) {
                                        echo "<tr>";
                                        echo "<td class='listado2'><input name='estadisticas' type='radio' value=$contador ";
                                        if ($estadisticas == $contador)
                                            echo "checked";
                                        else
                                            echo "";
                                        echo " >" . $permEsta[$contador] . "</td>";
                                        echo "</tr>\n";
                                        $contador = $contador + 1;
                                    }
                                    ?>
                                </table>
								<br><br>
                            </td>

                            <td class="titulos2" width="16%" valign="top"> 
                                <table width='100%' valign="top">
                                    <tr>
                                        <td class="titulos4"> <b> Marcar como Impreso </b>  </td> 
                                    </tr>
                                    <?php
                                    echo $htmlDependencias;
                                    $contador = 0;
                                    $permImpresion = array();
                                    $permImpresion[] = 'Sin Permisos';
                                    $permImpresion[] = 'De la dependencia';
                                    $permImpresion[] = 'Todas los dependencias';
                                    while ($contador <= 2) {
                                        echo "<tr>\n";
                                        echo "<td class='titulos2'><input name='impresion' type='radio' value='$contador'";
                                        if ($impresion == $contador)
                                            echo "checked";
                                        else
                                            echo "";
                                        echo " >" . $permImpresion[$contador] . "</td>\n";
                                        echo "</tr>";
                                        $contador = $contador + 1;
                                    }
                                    ?>
                                </table>
								<br><br>
                            </td>

                            <td class="listado2" width="16%">
                                <table width='100%' valign='top'>
                                    <tr>
                                        <td class="titulos4"> Acciones Masivas </td>
                                    </tr>
                                    <tr> 
                                        <td class="listado2" valign='top'>
                                            <input type="checkbox" name="accMasiva_trd" value="$accMasiva_trd" 
                                                   <?php if ($accMasiva_trd) echo "checked";
                                                   else echo ""; ?>> Masiva TRD
                                            <br><br>
                                            <input type="checkbox" name="accMasiva_incluir" value="$accMasiva_incluir" 
                                                   <?php if ($accMasiva_incluir) echo "checked";
                                                   else echo ""; ?>> Masiva Incluir	
                                            <br><br>
                                            <input type="checkbox" name="accMasiva_prestamo" value="$accMasiva_prestamo"
												   <?php if ($accMasiva_prestamo) echo "checked"; else echo ""; ?>> 
											Masiva Prestamo
                                            <br><br>
                                            <input type="checkbox" name="accMasiva_temas" value="$accMasiva_temas" 
												<?php if ($accMasiva_temas) echo "checked"; else echo ""; ?>> 
											Masiva Temas<br/> 
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>

                        <tr class="timparr">

                            <td class="titulos2" width="16%"> 
                                <input type="checkbox" name="digitaliza" value="$digitaliza" 
									<?php if ($digitaliza) echo "checked"; else echo ""; ?>>
                                Digitalizaci&oacute;	n de Documentos
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="reasigna" value="$reasigna" 
									<?php if ($reasigna) echo "checked"; else echo ""; ?>>
                                Reasignar Radicados
                            </td>

                            <td class="titulos2" width="16%"> 
                                <input type="checkbox" name="s_anulaciones" value="$s_anulaciones" 
									<?php if ($s_anulaciones) echo "checked"; else echo ""; ?>>
                                Solicitar Anulaciones
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="permArchivar" value="$permArchivar" 
									<?php if ($permArchivar || $usua_Archivar == 1) echo "checked"; else echo ""; ?>>
                                Archivar Documentos
                            </td>
                        </tr>
                        <tr>
                            <td class="titulos2" width="16%"> 
                                <input type="checkbox" name="autenticaLDAP" value="$autenticaLDAP" 
									<?php if ($autenticaLDAP || $autenticaLDAP == 1) echo "checked"; else echo ""; ?>>
                                Se autentica por LDAP
                            </td>


                            <td class="listado2" width="16%"> 
                                <input name="modificaciones" type="checkbox" value="$modificaciones" 
									<?php if ($modificaciones) echo "checked"; else echo ""; ?>>
                                Modificar Radicados
                            </td>

                            <td class="titulos2" width="16%"> 
                                <input type="checkbox" name="alertaDP" value="$alertaDP" 
									<?php if ($alertaDP) echo "checked"; else echo ""; ?>>
                                Carpeta Control de Pqrs
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="permBorraAnexos" value="$permBorraAnexos" 
									<?php if ($permBorraAnexos) echo "checked"; else echo ""; ?>>
                                Borrar anexos
                            </td>
                        </tr>

                        <tr>
                            <td class="titulos2" width="15%">
                                <input type="checkbox" name="respuesta" value="$respuesta" 
									<?php if ($respuesta) echo "checked"; else echo ""; ?> >
                                Respuesta R&aacute;pida
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="medios" value="$medios" 
									<?php if ($medios) echo "checked"; else echo ""; ?>>
                                Cambia medio de recepci&oacute;n
                            </td>

                            <td class="titulos2" width="15%"> 
                                <input type="checkbox" name="ccalarmas" value="$ccalarmas" 
									<?php if ($ccalarmas) echo "checked"; else echo ""; ?>>
                                Copia de alertas PQR y/o <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Radicado no reporta respuesta
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="permRadMail" 
									<?php if ($permRadMail == 1) echo "checked"; else echo ""; ?>>
                                <span class="etexto">Radicacion de e-mail (<?= $email ?>)</span>
                            </td>
                        </tr>

                        <tr>
                            <td class="titulos2" width="15%"> 
                                <input type="checkbox" name="no_trd" 
									<?php if ($no_trd == True) echo "checked"; else echo ""; ?>>
                                Usuario NO tipifica
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="ordena" 
									<?php if ($ordena == True) echo "checked"; else echo ""; ?>>
                                Ordenar anexos
                            </td>

                            <td class="titulos2" width="16%"> 
                                <input type="checkbox" name="masiva" value="$masiva" 
									<?php if ($masiva) echo "checked"; else echo ""; ?>>
                                Radicaci&oacute;n Masiva
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="permTipificaAnexos" value="$permTipificaAnexos" 
									<?php if ($permTipificaAnexos) echo "checked"; else echo ""; ?>>
                                Tipificar anexos
                            </td>
                        </tr>
                        <tr>
                            <td class="titulos2" width="16%">
                                Firma Digital
<?php
$contador = 0;
while ($contador <= 3) {
    echo "<input name='firma' type='radio' value='$contador'";
    if ($firma == $contador)
        echo "checked";
    else
        echo "";
    echo " >" . $contador;
    $contador = $contador + 1;
}
?>
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="temas" value="$temas" 
									<?php if ($temas) echo "checked"; else echo ""; ?>>
                                Modificaci&oacute;n Temas
                            </td>

                            <td class="titulos2" width="15%"> 
                                <input type="checkbox" name="permDespla" value=1 
									<?php if ($permDespla) echo "checked"; else echo ""; ?>>
                                Estado Desplazado
                            </td>

                            <td class="listado2" width="16%"> 
                                <input type="checkbox" name="repMailCert" value="$repMailCert" 
									<?php if ($repMailCert) echo "checked"; else echo ""; ?>>
                                Reporte de Correo Certificado
                            </td>

                        </tr>
                        <tr>
                            <td class="titulos2" width="15%">
                                <input type="checkbox" name="repMailNoHabil" value="$repMailNoHabil" 
									<?php if ($repMailNoHabil) echo "checked"; else echo ""; ?>>
                                Copia Mail Institucional Horario no h&aacute;bil
                            </td>
                            <td class="listado2" width="16%">
								<input type="checkbox" name="pqrVerbal" value="$pqrVerbal" 
									<?php if ($pqrVerbal) echo "checked"; else echo ""; ?>>
                                Radicar PQRS Verbales
                            </td>
                            <td class="titulos2" width="15%"> 
                                <input type="checkbox" name="permRecRadEnt" value="$permRecRadEnt" 
									<?php if ($permRecRadEnt) echo "checked"; else echo ""; ?>>
                                Puede recibir radicados Entrada
                            </td>
                            <td class="listado2" width="16%">
								<input type="checkbox" name="devCorreo" value="$devCorreo" 
									<?php if ($devCorreo) echo "checked"; else echo ""; ?>>
                                Carpeta Dev. Correo
                            </td>
                        </tr>
                        <tr>
                            <td class="titulos2" width="15%">
                                <input type="checkbox" name="repNotifCorreo" value="$repNotifCorreo" 
                                <?php if ($repNotifCorreo) echo "checked"; else echo ""; ?>>
                                Recibe notificaci&oacute;n de Env&iacute;o Correo Electr&oacute;nico Certificado 4-72
                            </td>
                            <td class="titulos2" width="16%">
								<input type="checkbox" name="repVencPrestamo" value="$repVencPrestamo" 
                                <?php if ($repVencPrestamo) echo "checked"; else echo ""; ?>>
                                Recibe notificaci&oacute;n de vencimiento de Pr&eacute;stamos 
                            </td>
                            <td class="listado2" width="16%">
                            	<input type="checkbox" id="modificarTRD" name="modificarTRD" value="$modificarTRD" 
                            	<?php if ($modificarTRD) echo "checked"; else echo ""; ?>>
                            	Modificar TRD PQRSD
                            </td>
                            <td class="listado2" width="16%">
                            	<input type="checkbox" id="modificarTipoDoc" name="modificarTipoDoc" value="$modificarTRD" 
                            	<?php if ($modificarTipoDoc) echo "checked"; else echo ""; ?>>
                            	Modificar tipo documento PQRSD
                            </td>
                        </tr>
                        <tr>
                        	<td class="listado2" width="16%">
                            	<input type="checkbox" id="retipificarTRD" name="retipificarTRD" value="$retipificarTRD" 
                            	<?php if ($retipificarTRD) echo "checked"; else echo ""; ?>>
                            	Retipificar TRD PQRSD
                            </td>
                            <td class="titulos2" width="15%">
                                <input type="checkbox" name="anexarCorreo" value="$anexarCorreo" 
                                <?php if ($anexarCorreo) echo "checked"; else echo ""; ?>>
                                Anexar Correo Electr&oacute;nico
                            </td>
                            <td class="titulos2" width="15%">
                            </td>
                            <td class="titulos2" width="15%">
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
            <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" class="listado2">
                <tr>
                    <td width="20%" align="center">
                        <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Grabar" onClick="return ValidarInformacion(this.value);" accesskey="A">
                        <!--<input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Grabar">-->
                    </td>
                    <td width="20%" align="center">
                        <!--<input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Cancelar" onClick="return ValidarInformacion(this.value);" accesskey="C">-->
                        <input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Cancelar">
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
            /*	Since we specified manualStartup=true, tabber will not run after the onload event. Instead let's run it now, to prevent any delay while images load. */
            tabberAutomatic(tabberOptions);
            </script>
        </form>
    </body>
</html>	