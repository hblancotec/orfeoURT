<?php
session_start();
$ruta_raiz = "..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz . "/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
if ($_SESSION['usua_perm_trd'] != 1) {
    die(include $ruta_raiz . "/sinpermiso.php");
    exit();
}

if (!$fecha_busq) {
    $fecha_busq = Date('Y-m-d');
}
if (!$fecha_busq2) {
    $fecha_busq2 = Date('Y-m-d');
}

include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
define('ADODB_FETCH_ASSOC', 2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$encabezadol = $_SERVER['PHP_SELF'] . "?" . session_name() . "=" . session_id();
$encabezadol .= "&krd=$krd&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2&version=$version";
$encabezadol .= "&nombre=$nombre&descrip=$descripcion";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../estilos/orfeo.css">
<link rel="stylesheet" type="text/css"
	href="../js/spiffyCal/spiffyCal_v2_1.css" />
<script>
function regresar(){   	
	document.adm_version.submit();
}
</script>
</head>
<body bgcolor="#FFFFFF">
	<div id="spiffycalendar" class="text"></div>
	<script type="" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
	<script language="javascript">
	<!--
	var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "adm_version", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "adm_version", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
	//-->
	</script>
	<table class=borde_tab width='100%' cellspacing="5">
		<tr>
			<td class=titulos2 style="text-align: center;">VERSIONAMIENTO TRD</td>
		</tr>
	</table>
	<table>
		<tr>
			<td></td>
		</tr>
	</table>
	<form method="post" action="<?=$encabezadol?>" name="adm_version">
		<div style="margin: auto; justify-content: center; text-align: center; width: 100%; display: flex;">
			<table style="width: 550px; align-self: center;" class="borde_tab"
				cellspacing="5">
				<tr>
					<td width="125" height="21" class='titulos2'>Versi&oacute;n</td>
					<td valign="top" align="left" class='listado2'>
						<input type='text' name='txtVersion' id="txtVersion" value='<?=$version?>' class='tex_area' size='11' maxlength='25'>
					</td>
					<td width="125" height="21" class='titulos2'>Nombre</td>
					<td>
						<input type='text' name='txtNombre' id="txtNombre" value='<?=$nombre?>' class='tex_area' size='50' maxlength='100'>
					</td>
				</tr>
				<tr>
					<td height="26" class='titulos2'>Descripci&oacute;n</td>
					<td valign="top" align="left" class='listado2' colspan="3">
						<input type='text' name='txtDescripcion' id='txtDescripcion' value='<?=$descripcion?>' class='tex_area' size=75 maxlength="50">
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<table>
							<tr>
								<td height="26" class='titulos2'>Fecha desde<br /></td>
								<td width="225" align="right" valign="top" class='listado2'>
									<script language="javascript">
            		 					dateAvailable.date = "<?=date('Y-m-d');?>";
            							dateAvailable.writeControl();
            							dateAvailable.dateFormat="yyyy-MM-dd";
                					</script></td>
								<TD height="26" class='titulos2'>Fecha Hasta<br /></TD>
								<TD width="225" align="right" valign="top" class='listado2'>
									<script language="javascript">
                						dateAvailable2.date = "<?=date('Y-m-d');?>";
                						dateAvailable2.writeControl();
                						dateAvailable2.dateFormat="yyyy-MM-dd";
                				    </script></TD>
							</tr>
						</table>
					<td>
				</tr>
				<tr>
					<td height="26" colspan="4" valign="top" class="titulos2" align="center">
						<input type="submit" name="buscar_version" value="Buscar" class="botones" />
						<input type="submit" name="insertar_version" value="Insertar" class="botones" />
						<input type="submit" name="actua_version" value="Modificar" class="botones" />
						<input type="reset" name="cancelar" class="botones" id="envia22" value="Cancelar" />
					</td>
				</tr>
			</table>
		</div>
<?php
$sqlFechaD = $db->conn->DBDate($fecha_busq);
$sqlFechaH = $db->conn->DBDate($fecha_busq2);
$detaserie = strtoupper(trim($detaserie));
$version = $_POST['txtVersion'];
$nombre = $_POST['txtNombre'];
$descripcion = $_POST['txtDescripcion'];

if ($buscar_version && $version != '') {
    $whereBusqueda = " where VERSION like '%$version%'";
}

if ($insertar_version && $version != '' && $nombre != '' && $descripcion != '') 
{
    $isqlB = "select * from VERSIONES where VERSION = '$version' ";
    $rs = $db->conn->Execute($isqlB); 
    $idversion = $rs->fields["ID"];
    if ($idversion != '') {
        $mensaje_err = "<HR><center><B><FONT COLOR=RED>La Versi&oacute;n < $version > YA EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
    } else {
        $isqlB = "select * from VERSIONES where NOMBRE = '$nombre' ";
        $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
        $idversion = $rs->fields["ID"];
        if ($idversion != '') {
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>La Versi&oacute;n < $version > YA EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
        } else {
            $query = "insert into VERSIONES (VERSION, NOMBRE, OBSERVACION, FECHA_INICIO, FECHA_FIN )
						VALUES ('$version', '$nombre', '$descripcion', '" . $fecha_busq . "', '" . $fecha_busq2 . "')";
            $rsIN = $db->conn->Execute($query);
            if ($rsIN) {
                $version = "";
                $nombre = "";
                $descripcion = "";
                
                ?>
            	<script language="javascript">
            		document.adm_version.txtVersion.value = '';
            		document.adm_version.txtNombre.value = '';
            		document.adm_version.txtDescripcion.value = '';
            	</script>
    			<?php
            } else {
                $mensaje_err = "<HR><center><B><FONT COLOR=RED>Error en el registro de la Versi&oacute;n < $version > : $query. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
            }
        }
    }
}

if ($actua_version && $version != '') {
    $isqlB = "select * from VERSIONES where VERSION = $version ";
    $rs = $db->conn->Execute($isqlB);
    $radiNumero = $rs->fields["ID"];
    if ($radiNumero == '') {
        $mensaje_err = "<HR><center><B><FONT COLOR=RED>La Versi&oacute;n < $version > NO EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
    } else {
        $isqlB = "select * from VERSIONES where VERSION = '$version' and NOMBRE = $nombre";
        $rs = $db->conn->Execute($isqlB);
        $radiNumero = $rs->fields["ID"];
        if ($radiNumero != '') {
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>La Versi&oacute;n <$nombre > YA EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
        } else {

            $isqlUp = "update VERSIONES set NOMBRE = '$nombre', OBSERVACION = '$descripcion', FECHA_INICIO = '$fecha_busq', FECHA_FIN = '$fecha_busq2'
                        where VERSION = '$version' ";
            $rsUp = $db->conn->Execute($isqlUp);
            if ($rsUp) {
                $version = "";
                $nombre = "";
                $descripcion = "";
                $mensaje_err = "<HR><center><B><FONT COLOR=RED>SE MODIFIC&Oacute; LA VERSION</FONT></B></center><HR>";
                ?>
            	<script language="javascript">
                	document.adm_version.txtVersion.value = '';
            		document.adm_version.txtNombre.value = '';
            		document.adm_version.txtDescripcion.value = '';
            	</script>
				<?php
            }
        }
    }
}
include_once "$ruta_raiz/trd/lista_versiones.php";
?>	
	</form>
	<p>
<?=$mensaje_err?>
</p>
</body>
</html>