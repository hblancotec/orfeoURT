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

if (! $fecha_busq)
    $fecha_busq = Date('d-m-Y');
if (! $fecha_busq2)
    $fecha_busq2 = Date('d-m-Y');

include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
define('ADODB_FETCH_ASSOC', 2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$encabezadol = $_SERVER['PHP_SELF'] . "?" . session_name() . "=" . session_id();
$encabezadol .= "&krd=$krd&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2&codserieI=$codserieI";
$encabezadol .= "&detaserie=$detaserie&codusua=$codusua&depende=$depende";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../estilos/orfeo.css">
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css" />
<script type="text/javascript">
function regresar(){   	
	document.adm_serie.submit();
}

function selectVersion() {
	debugger;
	//document.adm_serie.codserieI.value = '';
	//document.adm_serie.detaserie.value = '';

	document.getElementById("adm_serie").submit();
}
</script>
</head>
<body bgcolor="#FFFFFF">
	<div id="spiffycalendar" class="text"></div>
	<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
	<script language="javascript">
	<!--
	var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "adm_serie", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "adm_serie", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
	//-->
	</script>
	<table class=borde_tab width='100%' cellspacing="5">
		<tr>
			<td class=titulos2><center>SERIES DOCUMENTALES</center></td>
		</tr>
	</table>
	<table>
		<tr>
			<td></td>
		</tr>
	</table>
	<form method="post" action="<?=$encabezadol?>" name="adm_serie" id="adm_serie">
		<center>
			<table width="550" class="borde_tab" cellspacing="5">
				<tr>
					<td width="125" height="21" class='titulos2'>C&oacute;digo</td>
					<td valign="top" align="left" class='listado2'>
						<input type='text' name='codserieI' value='<?=$codserieI?>' class='tex_area' size='11' maxlength='7'>
					</td>
					<td width="125" height="21" class='titulos2'>Versi&oacute;n</td>
					<td>
						<select name="version" id="version" class="select" onChange="selectVersion();">
							<option value="">--Seleccione--</option>
						<?php 
						$sqlVer = "SELECT [ID], [VERSION], [NOMBRE], [OBSERVACION], [FECHA_INICIO], [FECHA_FIN]
                                        FROM [VERSIONES]";
                        $rsVer = $db->conn->Execute($sqlVer);
                        if ($rsVer) {
                            while (!$rsVer->EOF) {
                                //print $rsVer->GetMenu2("version", $version, ":-- Seleccione --", false, "", "onChange='selectVersion()' class='select'");
                                $idver = $rsVer->fields["ID"];
                                $versi = $rsVer->fields["VERSION"];
                                $nombre = $rsVer->fields["NOMBRE"];
                                
                                $selected = "";
                                if ($idver == $version) {
                                    $selected = " selected ";
                                }
                                ?>
                            	<option value="<?=$idver?>" <?=$selected?>><?=$versi . " - " . $nombre?></option>
                            <?php 
                            $rsVer->MoveNext();
                            }
                        }                           
                        ?>
                        </select>
					</td>
				</tr>
				<tr>
					<td height="26" class='titulos2'>Descripci&oacute;n</td>
					<td valign="top" align="left" class='listado2' colspan="3">
						<input type='text' name='detaserie' value='<?=$detaserie?>' class='tex_area' size=75 maxlength="125">
					</td>
				</tr>
				<tr>
					<td height="26" class='titulos2'>Fecha desde<br /></td>
					<td width="225" align="right" valign="top" class='listado2'>
						<script language="javascript">
		 					dateAvailable.date = "<?=date('d-m-Y');?>";
							dateAvailable.writeControl();
							dateAvailable.dateFormat="dd-MM-yyyy";
    					</script>
    				</td>
					<TD height="26" class='titulos2'>Fecha Hasta<br /></TD>
					<TD width="225" align="right" valign="top" class='listado2'>
						<script language="javascript">
    						dateAvailable2.date = "<?=date('d-m-Y');?>";
    						dateAvailable2.writeControl();
    						dateAvailable2.dateFormat="dd-MM-yyyy";
    				    </script>
				    </TD>
				</tr>
				<tr>
					<td height="26" colspan="4" valign="top" class="titulos2" align="center">
						<input type="submit" name="buscar_serie" value="Buscar" class="botones" />
						<input type="submit" name="insertar_serie" value="Insertar" class="botones" /> 
						<input type="submit" name="actua_serie" value="Modificar" class="botones" />
						<input type="reset" name="aceptar" class="botones" id="envia22" value="Cancelar" />
					</td>
				</tr>
			</table>
<?php
$date1 = date_create($fecha_busq);
$sqlFechaD = date_format($date1, 'Y-m-d');
$date2 = date_create($fecha_busq2);
$sqlFechaH = date_format($date2, 'Y-m-d');
//$sqlFechaD = $db->conn->DBDate($fecha_busq);
//$sqlFechaH = $db->conn->DBDate($fecha_busq2);
$detaserie = strtoupper(trim($detaserie));
$version = strtoupper(trim($version));
// Busca series que cumplen con el detalle

$whereBusqueda = " where ID_VERSION = '$version' ";

if ($buscar_serie && $detaserie != '') {
    $whereBusqueda = " where ID_VERSION = '$version' AND sgd_srd_descrip like '%$detaserie%'";
}
if ($insertar_serie && $codserieI != 0 && $detaserie != '' && $version != '') {
    $isqlB = "select * from sgd_srd_seriesrd where sgd_srd_codigo = $codserieI and id_version = $version ";
    
    // Selecciona el registro a actualizar
    $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
    $radiNumero = $rs->fields["SGD_SRD_CODIGO"];
    if ($radiNumero != '') {
        $mensaje_err = "<HR><center><B><FONT COLOR=RED>EL C&Oacute;DIGO < $codserieI > YA EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
    } else {
        $isqlB = "select * from sgd_srd_seriesrd where sgd_srd_descrip = '$detaserie' and id_version = $version ";
        $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
        $radiNumero = $rs->fields["SGD_SRD_DESCRIP"];
        if ($radiNumero != '') {
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>LA SERIE <$detaserie > YA EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
        } else {
            $query = "insert into SGD_SRD_SERIESRD(SGD_SRD_CODIGO, SGD_SRD_DESCRIP, SGD_SRD_FECHINI, SGD_SRD_FECHFIN, ID_VERSION )
						VALUES ($codserieI, '$detaserie', '" . $sqlFechaD . "', '" . $sqlFechaH . "', $version )";
            $rsIN = $db->conn->Execute($query);
            $codserieI = 0;
            $detaserie = '';
?>
	<script type="text/javascript">
		document.adm_serie.codserieI.value ='';
		document.adm_serie.detaserie.value ='';
	</script>
<?php
        }
    }
}

if ($actua_serie && $codserieI != 0 && $detaserie != '') {
    $isqlB = "select * from sgd_srd_seriesrd where sgd_srd_codigo = $codserieI and id_version = $version ";
    
    // Selecciona el registro a actualizar
    $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
    $radiNumero = $rs->fields["SGD_SRD_CODIGO"];
    if ($radiNumero == '') {
        $mensaje_err = "<HR><center><B><FONT COLOR=RED>EL C&Oacute;DIGO < $codserieI > NO EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
    } else {
        $isqlB = "select * from sgd_srd_seriesrd where sgd_srd_descrip = '$detaserie' and sgd_srd_codigo != $codserieI and id_version = $version ";
        $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
        $radiNumero = $rs->fields["SGD_SRD_CODIGO"];
        if ($radiNumero != '') {
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>LA SERIE <$detaserie > YA EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
        } else {
            
            $isqlUp = "update sgd_srd_seriesrd set SGD_SRD_DESCRIP = '$detaserie', SGD_SRD_FECHINI = '$sqlFechaD',
                        SGD_SRD_FECHFIN = '$sqlFechaH' where sgd_srd_codigo = $codserieI and ID_VERSION = $version ";
            $rsUp = $db->conn->Execute($isqlUp);
            $codserieI = 0;
            $detaserie = '';
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>SE MODIFIC&Oacute; LA SERIE</FONT></B></center><HR>";
            ?>
            	<script type="text/javascript">
            		document.adm_serie.codserieI.value ='';
            		document.adm_serie.detaserie.value ='';
            	</script>
            <?php
        }
    }
}
include_once "$ruta_raiz/trd/lista_series.php";
?>	
	</form>
	<p>
<?=$mensaje_err?>
</p>
</body>
</html>