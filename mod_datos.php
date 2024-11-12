<?php
session_start();
if (count($_SESSION) == 0) {
	die(include "./sinacceso.php");
	exit;
}
else if (isset($_SESSION['krd'])) {
	$krd = $_SESSION["login"];
}
else {
	$krd = $_REQUEST['krd'];
}

$usua_doc_tmp = $_SESSION['usua_doc'];
$usua_ext_tmp = $_SESSION['$usua_ext'];
$usua_piso_tmp = $_SESSION['$usua_piso'];
$usua_email_tmp = $_SESSION['$usua_email'];
$usua_at_tmp = $_SESSION['$usua_at'];
$ruta_raiz = ".";
if (!$_SESSION['dependencia'])
    include_once "./rec_session.php";
include_once "./include/db/ConnectionHandler.php";
$usua_nacim = substr($_SESSION["usua_nacim"], 0, 10);
$usua_dia_grb = substr($usua_nacim, -2);
$usua_mes_grb = substr($usua_nacim, 5, 2);
$usua_ano_grb = substr($usua_nacim, 0, 4);
if (!$db)
    $db = new ConnectionHandler(".");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
if ($usua_doc_tmp)
    $usua_doc = $usua_doc_tmp;
if ($usua_ext_tmp)
    $usua_ext = $usua_ext_tmp;
if ($usua_piso_tmp)
    $usua_piso = $usua_piso_tmp;
if ($usua_email_tmp)
    $usua_email = $usua_email_tmp;
if ($usua_at_tmp)
    $usua_at = $usua_at_tmp;
if (!$usua_dia)
    $usua_dia = $usua_dia_grb;
if (!$usua_mes)
    $usua_mes = $usua_mes_grb;
if (!$usua_ano)
    $usua_ano = $usua_ano_grb;
?>
<html>
    <head>
        <link rel="stylesheet" href="estilos/orfeo.css">
    </head>
    <body  onload="SetFocus();">
        <form name=datos_personales action="mod_datos.php?<?= session_name() . "=" . session_id() ?>&fechaf=<?= $fechaf ?>&krd=<?= $krd ?>" method=post> 
            <table WIDTH=98% align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab" >
                <tr valign="bottom">       
                    <td class="titulos4" height="54">INFORMACI&Oacute;N GENERAL <br/>
                    </td>
                </tr>
                <tr> <td>
                        <table width="780" border="0" cellspacing="0" cellpadding="0">
                            <tr align="center"> 
                                <td class="info" height="40"><b>La informaci&oacute;n aqu&iacute; 
                                        reportada se considera oficial y es indispensable para 
                                        iniciar el acceso al Sistema de Gesti&oacute;n Documental ORFEO</b></td>
                            </tr>
                        </table>
                    </td> 
                </tr>
            </table>
            <table  WIDTH=98% align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
                <!--DWLayoutTable--> 
                <tr> 
                    <td height="50" align="right" class="titulos2" width="24%" >
                        Documento C.C:
                    </td>
                    <td class='listado2' width="17%" > 
                        <?php
                        if ($info)
                            $info = "false";
                        else
                            $info = "true";
                        ?>
                        <input type=text name=usua_doc value='<?= TRIM($usua_doc) ?>' class=tex_area size=15 maxlength="20" readonly="<?= $info ?>"></td>
                    <td  align="right"  class="titulos2" width="15%">Fecha Nacimiento<br>
                        (aaaa-mm-dd)<?= $usua_nacim ?> </td>
                    <td class='listado2' width="24%" >
                        <?php
                        $ano_fin = date("Y");
                        $ano_fin++;
                        $ano_fin = $ano_fin - 10;
                        $ano_ini = $ano_fin - 80;
                        ?>
                        <select name=usua_dia class="select">
                            <option value=0>Dia</option>				
                            <?php
                            for ($i = 1; $i <= 31; $i++) {
                                if ($i == $usua_dia) {
                                    $datoss = " selected ";
                                } else {
                                    $datoss = "";
                                }
                                echo "<option value=$i  $datoss>$i</option>";
                            }
                            ?>
                        </select>
                        <select name=usua_mes class="select">
                            <option value=0>Mes</option>
                            <?php
                            if ($usua_mes == 1) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>
                            <option value=1  '<?= $datoss ?>'>Ene</option>
                            <?php
                            if ($usua_mes == 2) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>	
                            <option value=2  '<?= $datoss ?>'>Feb</option>
                            <?php
                            if ($usua_mes == 3) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=3  '<?= $datoss ?>'>Mar</option>
                            <?php
                            if ($usua_mes == 4) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=4  '<?= $datoss ?>'>Abr</option>
                            <?php
                            if ($usua_mes == 5) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=5  '<?= $datoss ?>'>May</option>
                            <?php
                            if ($usua_mes == 6) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=6  '<?= $datoss ?>'>Jun</option>
                            <?php
                            if ($usua_mes == 7) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=7  '<?= $datoss ?>'>Jul</option>
                            <?php
                            if ($usua_mes == 8) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=8  '<?= $datoss ?>'>Ago</option>
                            <?php
                            if ($usua_mes == 9) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=9  '<?= $datoss ?>'>Sep</option>
                            <?php
                            if ($usua_mes == 10) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=10  '<?= $datoss ?>'>Oct</option>
                            <?php
                            if ($usua_mes == 11) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=11  '<?= $datoss ?>'>Nov</option>
                            <?php
                            if ($usua_mes == 12) {
                                $datoss = " selected ";
                            } else {
                                $datoss = "";
                            }
                            ?>				
                            <option value=12  '<?= $datoss ?>'>Dic</option>
                        </select><select name=usua_ano class="select">
                            <option value=0>Año</option>
                            <?php
                            for ($i = 1; $i <= 80; $i++) {
                                $ano = ($ano_fin - $i);
                                if ($ano == $usua_ano) {
                                    $datoss = " selected ";
                                } else {
                                    $datoss = "";
                                }
                                echo "<option value='$ano' $datoss>$ano</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td   align="right" width="11%" class="titulos2">
                        Extension  </td>
                    <td class='listado2' width="9%"> 
                        <input type=text name=usua_ext value='<?= $usua_ext ?>' class=tex_area size=5 maxlength="4"> 
                    </td>
                </tr>
                <tr> 
                    <td align="right"  height="41" width="24%" class="titulos2">Correo Electr&oacute;nico<br></td>
                    <td class='listado2' width="17%" > 
                        <input type=text name=usua_email  value='<?= trim($usua_email) ?>' class=tex_area size=15 maxlength="50" readonly="<?= $info ?>">
                    </td>
                    <td align="right" class="titulos2" width="15%">Identificaci&oacute;n Equipo<br>
                        (ej, P-38650)</td>
                    <td class='listado2' width="24%" >&nbsp;&nbsp;<b>P-</b> 
                        <input type=text name=usua_at  value='<?= $usua_at ?>' class="tex_area" size="5" maxlength="5">
                    </td>
                    <td class="titulos2"  align="right" width="11%">Piso</td>
                    <td class='listado2' width="9%"> 
                        <input type=text name=usua_piso  value='<?= $usua_piso ?>' class="tex_area" size="5" maxlength="2">&nbsp;
                    </td>
                </tr>
                
                <tr align="center"> 
                    <td colspan="6"> 
                        <input type=submit name=grabar_datos_per class=botones_largo Value="Grabar Datos Personales">
                    </td>
                </tr>
            </table>
        </form>
        <?php
        if ($usua_doc and $usua_dia != 0 and $usua_ano != 0 and $usua_mes != 0 and $grabar_datos_per) {
            $fechaNacimiento = "" . $usua_ano . "-" . substr("0$usua_mes", -2) . "-" . substr("0$usua_dia", -2) . "";

            $record["USUA_EMAIL"] = "'" . $_POST['usua_email'] . "'";
            $usua_dia = substr("0$usua_dia", -2);

            $record["USUA_NACIM"] = $db->conn->DBDate($fechaNacimiento);
            $record["USUA_PISO"] = $_POST['usua_piso'];
            $record["USUA_EXT"] = $_POST['usua_ext'];
            $record["USUA_AT"] = "'" . $_POST['usua_at'] . "'";
            $record1["USUA_LOGIN"] = "'" . $_SESSION['krd'] . "'";
            $db->conn->BeginTrans();
            $ok = $db->update("USUARIO", $record, $record1);
            if ($ok) {
                $db->conn->CommitTrans();
                $msg = "Los datos han sido guardados, Por favor ingrese de modo normal al sistema.";
            }else{
                $db->conn->conn->RollbackTrans();
                $msg = "Error al guardar los datos. Comun&iacute;quese con el administrador del sistema.";
            }
            ?>
            <table border="0" width="100%">
                <tr><td class="etextomenu"><center><b><?=$msg?></b></center></td></tr>
            </table>
    <?php
} else {
    ?>
    <table border="0" width="100%">
        <tr><td class="listado2">
        <center><b><span class="alarmas">Todos los datos deben ser grabados correctamente.  De lo contrario no podra seguir navegando por el sistema.</span></center>
    </td></tr>
    </table>
    <?php
}
?>
</body>
</html>