<?php
session_start();
$ruta_raiz = "..";

require_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once "$ruta_raiz/include/combos.php";
require_once "$ruta_raiz/webServices/appClient/class/Aplicativo.php";
require_once "$ruta_raiz/webServices/appClient/modulosCliente/FaxServerCliente.php";
require_once "$ruta_raiz/webServices/appClient/class/Funcionalidades.php";
include_once "$ruta_raiz/include/class/DatoContacto.php";
include_once "$ruta_raiz/include/tx/Historico.php";
if (! $db)
    $db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

if (! $_SESSION['dependencia'] || ! $_SESSION['usua_nomb'])
    include "../rec_session.php";

// variable con la fecha formateada
$fechah = date("dmy") . "_" . time("h_m_s");
// variable con elementos de sesion
$encabezado = session_name() . "=" . session_id() . "&krd=$krd";
// le pone valor a la variable que maneja el criterio de ordenamiento inicial
if (! $orno)
    $orno = 1;

// include "libjs.php";
function tohtml($strValue)
{
    return htmlspecialchars($strValue);
}

$encabezado = session_name() . "=" . session_id();
$encabezado .= "&krd=$krd&";
$fechah = date("dmy") . "_" . time("h_m_s");
$faxId = array();
if ($_REQUEST['radSel']) {
    
    $radSel = $_REQUEST['radSel'];
    $objClienteFax = new FaxServerCliente($db->conn, $ruta_raiz);
    $objFuncionalidades = new Funcionalidades($db->conn);
    $objHist = new Historico($db);
    $objDir = new DatoContacto($db->conn);
    $tableResult = "<table class='borde_tab' width='100%' cellspacing='5'>
                        <tr><td class='titulos5' align='center' valign='middle'><B>Env&iacute;o de Documentos por FAX</B></td></tr>
                        </table>    
                                <table border=0 width=100% class=borde_tab cellspacing='5'>
                                <tr><td colspan='5' align='center'><center><a href='$ruta_raiz/envios/cuerpoEnviofax.php?krd=" . $krd . "' class='botones'>Regresar al listado</a></center></td></tr>
                                <tr class='titulos2'>
                                <td >Estado</td>
                                <td >Radicado</td>
                                <td >Copia</td>
                                <td >Destinatario</td>
                                <td >Observac&ioacute;n</td>
                        </tr>";
    $cont = 0;
    foreach ($radSel as $j => $val) {
        
        $vecRad = explode("-", $val);
        $vecDir = $objDir->obtieneDatosDir(false, $vecRad[0], $vecRad[1]);
        $rsView = $objClienteFax->obtenerDatosView("vFaxMsgs", $vecDir[0]["ID_FAX"]);
        $refRadicado['nRadicado'] = $vecRad[0];
        $usuarioEnvia['documento'] = $_SESSION["usua_doc"];
        $usuarioEnvia['dependencia'] = $_SESSION["dependencia"];
        $nCopia = $vecRad[1];
        if ($vecRad[1] > 1) {
            $copia = substr($vecRad[1], 2, 2);
            $txtCC = ", Copia:" . $copia;
        } else {
            $copia = "";
            $txtCC = "";
        }
        if ($cont % 2 == 0) {
            $estilo = "class=listado2";
        } else {
            $estilo = "class=listado1";
        }
        $pesoGramos = 0;
        $empresaEnvio = 104;
        if ($rsView) {
            switch ($rsView['msg']['State']) {
                
                case 0:
                    $tableResult .= "<tr $estilo>
                                     <td><span class='titulosError'>Pendiente!</span></td>
                                     <td>$vecRad[0]</td>
                                     <td>$copia</td>
                                     <td>" . $vecDir[0]['NOMBRE'] . " " . $vecDir[0]['APELLIDO'] . "</td>
                                     <td>El estado actual del envío al FAX No:" . $rsView['msg']['fTo'] . " es Pendiente, favor intentar verificar mas tarde!</td>
                                     </tr>";
                    break;
                case 1:
                    $radExito[] = $vecRad[0];
                    $objFuncionalidades->EnviarRadicado($refRadicado, $usuarioEnvia, $nCopia, $pesoGramos, $empresaEnvio, "Envío confirmado por el servidor de FAX, estado OK! al No de FAX:" . $rsView['msg']['fTo']);
                    $objDir->actualizaDatosDirEnvioFax(false, $vecRad[0], $vecRad[1], false, 1, false, false, 1);
                    $tableResult .= "<tr $estilo>
                                     <td>OK</td>
                                     <td>$vecRad[0]</td>
                                     <td>$copia</td>
                                     <td>" . $vecDir[0]['NOMBRE'] . " " . $vecDir[0]['APELLIDO'] . "</td>
                                     <td>Se ha enviado satisfactoriamente el FAX al No:" . $rsView['msg']['fTo'] . "</td>
                                     </tr>";
                    break;
                case - 1:
                    $objHist->insertarHistorico(Array(
                        0 => $vecRad[0]
                    ), $_SESSION["dependencia"], $_SESSION["codusuario"], $_SESSION["dependencia"], $_SESSION["codusuario"], "Ha fallado el envío de Fax al número marcado:" . $rsView['msg']['fTo'] . " y destinatario:" . $rsView['msg']['fToName'] . ", favor reintentar desde el m&oacute;dulo impresi&oacute;n ", 87);
                    $objDir->actualizaDatosDirEnvioFax(false, $vecRad[0], $vecRad[1], false, - 1, false, false, 1);
                    $tableResult .= "<tr $estilo>
                                     <td><span class='titulosError'>Falla</span></td>
                                     <td>$vecRad[0]</td>
                                     <td>$copia</td>
                                     <td>" . $vecDir[0]['NOMBRE'] . " " . $vecDir[0]['APELLIDO'] . "</td>
                                     <td>Falla en anv&iacute;o del FAX al No: " . $rsView['msg']['fTo'] . ", favor reintentar desde el m&oacute;dulo impresi&oacute;n</td>
                                     </tr>";
                    break;
                case - 2:
                    $objHist->insertarHistorico(Array(
                        0 => $vecRad[0]
                    ), $_SESSION["dependencia"], $_SESSION["codusuario"], $_SESSION["dependencia"], $_SESSION["codusuario"], "Ha fallado el envío de Fax al número marcado:" . $rsView['msg']['fTo'] . " y destinatario:" . $rsView['msg']['fToName'] . ", favor reintentar desde el m&oacute;dulo impresi&oacute;n ", 87);
                    $objDir->actualizaDatosDirEnvioFax(false, $vecRad[0], $vecRad[1], false, - 2, false, false, 1);
                    $tableResult .= "<tr $estilo>
                                     <td><span class='titulosError'>Falla</span></td>
                                     <td>$vecRad[0]</td>
                                     <td>$copia</td>
                                     <td>" . $vecDir[0]['NOMBRE'] . " " . $vecDir[0]['APELLIDO'] . "</td>
                                     <td>Falla en anv&iacute;o del FAX al No: " . $rsView['msg']['fTo'] . ", favor reintentar desde el m&oacute;dulo impresi&oacute;n</td>
                                    </tr>";
                    break;
            }
        }
        $cont ++;
    }
    $tableResult .= "</table>";
}
?>

<html>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<script>
function back() {
    history.go(-1);
}

function verificarSeleccion() {

    marcados = 0;

    for(i=0;i<document.form1.elements.length;i++)
    {
        if(document.form1.elements[i].checked==1)
        {
            marcados++;
        }
    }
    if(marcados==0) {
       alert("Debe seleccionar al menos un radicado");
       return false;
    }
    document.form1.submit();
}
</script>
</head>
<?php
if ($_REQUEST['radSel']) {
    die($tableResult);
} else {
    ?>
        
	<body>
	<br>
	<table width='100%' align='center'>
		<tr>
			<td height="20">
					
					<?php
    include "$ruta_raiz/include/query/envios/queryCuerpoEnvioFax.php";
    $rs = $db->conn->Execute($isql);
    ?>
					
					<table cellspacing='5' width=100% valign='top' align='center'
					class="borde_tab">
					<tr>
						<td width='35%'>
							<table width='100%' border='0' cellspacing='1' cellpadding='0'>
								<tr>
									<td width="30%" class='titulos2'>Listado de</td>
									<td width="30%" class='titulos2' height="20">Usuario</td>
									<td width="40%" class='titulos2' height="20"></td>
								</tr>
								<tr>
									<td height="20" class="listado2"><span class="etextomenu"
										style="color: red; font-size: 11px"> Radicados envíados por
											Fax </span></td>
									<td class='listado2' style="font-size: 9px"> 
											<?=$_SESSION['usua_nomb']?> 
										</td>
									<td width='40%' align="center" class='titulos5'><input
										type="button" value='Confirmar envío' name='Enviar'
										valign='middle' class='botones_largo'
										onClick="verificarSeleccion();"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table> <br>
				<form name='form1'
					action='cuerpoEnvioFax.php?<?=session_name()."=".session_id()."&krd=$krd"?>'
					method=post>
					<table width="100%" align="center">
						<tr>
							<td class="grisCCCCCC">
								<table border=0 cellspacing='5' WIDTH=100% class='borde_tab'
									align='center'>
									<tr bgcolor="#cccccc" class='titulos3'>
										<td width='7%' align="center">Radicado</td>
										<td width='4%' align="center">Copia</td>
										<td width='8%' align="center">Fecha</td>
										<td width='20%' align="center">Descripción</td>
										<td width='15%' align="center">Generado por</td>
										<td width='7%' align="center">No. Fax</td>
										<td width='7%' align="center">Estado</td>
										<td width='2%' align="center">Marcar</td>
									</tr>
<?php
    $ki = 0;
    $css = 1;
    
    $registro = $pagina * 20;
    while ($rs && ! $rs->EOF) {
        // echo 'pagina: '.$pagina;
        // echo '<br>registro: '.$registro;
        // echo '<br>voy acá 0';
        // echo '<pre>'.$rs;
        if ($ki >= $registro or $ki < ($registro + 20)) {
            // echo '<br>voy acá 1';
            $css = ($css == 1) ? 2 : 1;
            ?>

									<tr>
										<td align='center' class='listado<?php echo $css;?>'><span><?=$rs->fields['RADICADO']; ?></span></td>
										<td align='center' class='listado<?php echo $css;?>'><?=$rs->fields['COPIA'];?></td>
										<td class='listado<?php echo $css;?>'><?=$rs->fields['FECHA'];?></td>
										<td class='listado<?php echo $css;?>'><?=$rs->fields['DESCRIPCION'];?></td>
										<td class='listado<?php echo $css;?>'><?=$rs->fields['USUARIO'];?></td>
										<td class='listado<?php echo $css;?>'><?=$rs->fields['FAX'];?></td>
										<td align='center' class='listado<?php echo $css;?>'><?=$rs->fields['ESTADO'];?></td>
										<td align='center' class='listado<?php echo $css;?>'>
											<center>
												<input type="checkbox"
													name='radSel[<?=$rs->fields['RADICADO'].$rs->fields['SGD_DIR_TIPO']?>]'
													id='radSel[<?=$rs->fields['RADICADO'].$rs->fields['SGD_DIR_TIPO']?>]'
													value='<?=$rs->fields['RADICADO'].'-'.$rs->fields['SGD_DIR_TIPO'];?>'>
											</center>
										</td>
									</tr>
			<?php
        }
        
        $ki = $ki + 1;
        $rs->MoveNext();
    }
    ?>
								</table>
							</td>
						</tr>
					</table>
				</form>
				<table border=0 cellspacing="5" WIDTH=100% class='borde_tab'
					align='center'>
					<tr align="center">
						<td>
	<?php
    $numerot = $ki;
    // Se calcula el numero de | a mostrar
    $paginas = ($numerot / 20);
    ?>
						<span class='leidos'> P&aacute;ginas</span> 
	<?php
    if (intval($paginas) <= $paginas) {
        $paginas = $paginas;
    } else {
        $paginas = $paginas - 1;
    }
    // Se imprime el numero de Paginas.
    for ($ii = 0; $ii < $paginas; $ii ++) {
        if ($pagina == $ii) {
            $letrapg = "<font color=green size=3>";
        } else {
            $letrapg = "<font  class=leidos size=2>";
        }
        echo " <a href='cuerpoEnvioFax.php?pagina=$ii&$encabezado$orno'>$letrapg" . ($ii + 1) . "</font></a>\n";
    }
    ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br>
<?php
}
?>
	</body>
</html>