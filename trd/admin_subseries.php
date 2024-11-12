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
error_reporting(7);
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
if (! defined('ADODB_FETCH_ASSOC'))
    define('ADODB_FETCH_ASSOC', 2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
if (! $fecha_busq) {
    $fecha_busq = Date('d-m-Y');
}
if (! $fecha_busq2) {
    $fecha_busq2 = Date('d-m-Y');
}
if ($version) {
    $version = strtoupper(trim($version));
} else {
    $version = 0;
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="../estilos/orfeo.css">
	<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
</head>
<body bgcolor="#FFFFFF">
	<div id="spiffycalendar" class="text"></div>
	
	<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
	<script type="text/javascript" src="../js/jquery-3.5.1.js"></script>
	<script language="javascript">
    	var dateAvailable  = new ctlSpiffyCalendarBox("dateAvailable", "adm_subserie", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
		var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "adm_subserie", "fecha_busq2","btnDate2","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);

		function selectVersion() {
			//document.adm_serie.codserieI.value = '';
			//document.adm_serie.detaserie.value = '';

			document.getElementById("adm_subserie").submit();
		}
	</script>
	<form name="adm_subserie" id='adm_subserie' method='post'
		action='admin_subseries.php?<?=session_name()."=".session_id()."&krd=$krd&tiem_ac=$tiem_ac&tiem_ag=$tiem_ag&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2&codserie=$codserie&tsub=$tsub&detasub=$detasub"?>'>
		<table class=borde_tab width='100%' cellspacing="5">
			<tr>
				<td class="titulos2" style="text-align: center;">SUBSERIES DOCUMENTALES</td>
			</tr>
		</table>
		<table>
			<tr>
				<td></td>
			</tr>
		</table>
		<div style="margin: auto; justify-content: center; text-align: center; width: 100%; display: flex;">
		<TABLE style="width:550" class="borde_tab" cellspacing="5">
			<tr>
				<td width="95" height="21" class='titulos2'>
					Versi&oacute;n
				</td>
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
				<td width="95" height="21" class='titulos2'>C&oacute;digo Serie</td>
				<td colspan="3" class="listado5"> 
                      <?php
                    if (! $codserie)
                        $codserie = '';
                    $fechah = date("dmy") . " " . time("h_m_s");
                    $fecha_hoy = Date("d-m-Y");
                    $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
                    $check = 1;
                    $fechaf = date("dmy") . "_" . time("hms");
                    $num_car = 4;
                    $nomb_varc = "sgd_srd_codigo";
                    $nomb_varde = "sgd_srd_descrip";
                    include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
                    $querySerie = "select distinct ($sqlConcat) as detalle, sgd_srd_codigo 
                                   from sgd_srd_seriesrd 
                                   where ID_VERSION = $version 
                    	           order by detalle";
                    $rsD = $db->conn->Execute($querySerie);
                    $comentarioDev = "Muestra las Series Docuementales";
                    include "$ruta_raiz/include/tx/ComentarioTx.php";
                    print $rsD->GetMenu2("codserie", $codserie, ":-- Seleccione --", false, "", "onChange='selectSerie()' class='select'");
                    
                    require_once ($ruta_raiz . "/radsalida/masiva/OpenDocText.class.php");
                    $odt = new OpenDocText();
                    ?>
				</td>
                 <?php
                if ($_POST['actua_subserie'] && $_POST['tsub']) {
                    ?>
                 <td width="35" height="21">
                 	<input type="submit" name="modi_subserie" value="Grabar Modificacion" class="botones_largo">
                 </td>
                 <?php
                }
                ?>
            </tr>
			<tr>
				<td width="125" height="21" class='titulos2'>C&oacute;digo Subserie</td>
				<td width="125" valign="top" align="left" class='listado2'>
					<input id="tsub" name="tsub" type="text" size="20" class="tex_area" value="<?=$tsub?>">
					<p id="log"></p>
				</td>
				<td width="125" height="21" class='titulos2'>Descripci&oacute;n</td>
				<td valign="top" align="left" class='listado2'>
					<input name="detasub" type="text" size="75" class="tex_area" maxlength="199" value="<?=$detasub?>">
				</td>
			</tr>
			<tr>
				<td height="26" class='titulos2'>Fecha desde<br></td>
				<td width="225" valign="top" class='listado2'>
					<script language="javascript">
                	  	dateAvailable.dateFormat="dd-MM-yyyy";
                	    dateAvailable.date ="<?=$fecha_busq?>";
                		dateAvailable.writeControl();
                    </script>
                </td>
				<td height="26" class='titulos2'>Fecha Hasta<br></td>
				<td width="225" align="right" valign="top" class='listado2'>
					<script language="javascript">
                		dateAvailable2.dateFormat="dd-MM-yyyy";
                		dateAvailable2.date ="<?=$fecha_busq2?>";
                		dateAvailable2.writeControl();
                    </script>
            	</td>
			</tr>
			<tr>
				<td width="125" height="21" class='titulos2'>Tiempo Archivo de Gesti&oacute;n</td>
				<td valign="top" align="left" class='listado2'>
					<input name="tiem_ag" type="text" size="20" class="tex_area" value="<?=$tiem_ag?>">
				</td>
				<td width="125" height="21" class='titulos2'>Tiempo Archivo Central</td>
				<td valign="top" align="left" class='listado2'>
					<input name="tiem_ac" type="text" size="20" class="tex_area" value="<?=$tiem_ac?>">
				</td>
			</tr>
			<tr>
				<td width="125" height="21" class='titulos2'>Soporte</td>
				<td valign="top" align="left" class='listado2'>
					<input name="soporte" type="text" size="20" class="tex_area" value="<?=$soporte?>"></td>
				<td width="125" height="21" class='titulos2'>Disposici&oacute;n Final</td>
				<td valign="top" align="left" class='listado2'>
					<select name='med' class='select'>
                    	<?php
                    if ($med == 1) {
                        $datosel = " selected ";
                    } else {
                        $datosel = " ";
                    }
                    echo "<option value='1' $datosel><font>CONSERVACION TOTAL</font></option>";
                    if ($med == 2) {
                        $datosel = " selected ";
                    } else {
                        $datosel = " ";
                    }
                    echo "<option value='2' $datosel><font>ELIMINACION</font></option>";
                    if ($med == 3) {
                        $datosel = " selected ";
                    } else {
                        $datosel = " ";
                    }
                    echo "<option value='3' $datosel><font>MEDIO TECNICO</font></option>";
                    if ($med == 4) {
                        $datosel = " selected ";
                    } else {
                        $datosel = " ";
                    }
                    echo "<option value='4' $datosel><font>SELECCION O MUESTREO</font></option>";
                    ?>
                    </select>
           		</td>
			</tr>
			<tr>
				<td class="titulos5" width="25%" align="right"><font color="" face="Arial, Helvetica, sans-serif" class="etextomenu">Observaciones</font></td>
				<td width="75%" class="listado5" colspan="3">
					<textarea name="asu" cols="70" class="tex_area" rows="2" maxlength="199"><?=trim($asu)?></textarea>
				</td>
			</tr>
			<tr>
				<td height="26" colspan="4" valign="top" class='titulos2' align="center">
					<input type="submit" name="buscar_subserie" value="Buscar" class="botones"> 
					<input type="submit" name="insertar_subserie" value="Insertar" class="botones"> 
					<input type="submit" name="actua_subserie" value="Modificar" class="botones"> 
					<input type="reset" name="aceptar" class="botones" id="envia22" value="Cancelar">
				</td>
			</tr>
		</table>
		</div>
		
<?php
if ($tiem_ag == '')
    $tiem_ag = 0;
if ($tiem_ac == '')
    $tiem_ac = 0;
$detasub = strtoupper(trim($detasub));
// $asu = iconv($odt->codificacion($asu), 'UTF-8', $asu);
//$sqlFechaD = $db->conn->DBDate($fecha_busq);
//$sqlFechaH = $db->conn->DBDate($fecha_busq2);
$date1 = date_create($fecha_busq);
$sqlFechaD = date_format($date1, 'Y-m-d');
$date2 = date_create($fecha_busq2);
$sqlFechaH = date_format($date2, 'Y-m-d');
// Buscar detalle subserie
if ($buscar_subserie && $detasub != '') {
    if ($codserie != 0) {
        $detasub = iconv($odt->codificacion(strtoupper(trim($detasub))), 'UTF-8', strtoupper(trim($detasub)));
        $whereBusqueda = " and sgd_sbrd_descrip like '%$detasub%' and id_version = $version ";
    } else {
        echo "<script>alert('Debe seleccionar la Serie');</script>";
    }
} else {
    $whereBusqueda = " and id_version = $version ";
}

if ($insertar_subserie) {
    if ($tsub != '' && $codserie != 0 && $detasub != '' && $version != '') {
        $isqlB = "select * from sgd_sbrd_subserierd where sgd_srd_codigo = '$codserie'
					  and sgd_sbrd_codigo = $tsub and id_version = $version ";

        // Selecciona el registro a actualizar
        $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
        $radiNumero = $rs->fields["SGD_SRD_CODIGO"];
        if ($radiNumero != '') {
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>EL CODIGO < $codserieI > YA EXISTE. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
        } else {
            $isqlB = "select * from sgd_sbrd_subserierd where sgd_srd_codigo = $codserie
						        and sgd_sbrd_codigo = $tsub and id_version = $version ";
            $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
            $radiNumero = $rs->fields["SGD_SRD_CODIGO"];
            if ($radiNumero != '') {
                $mensaje_err = "<HR><center><B><FONT COLOR=RED>LA SERIE <$detasub > YA EXISTE. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
            } else {
                $query = "insert into SGD_SBRD_SUBSERIERD (SGD_SRD_CODIGO, SGD_SBRD_CODIGO, SGD_SBRD_DESCRIP, SGD_SBRD_FECHINI, SGD_SBRD_FECHFIN, 
                            SGD_SBRD_TIEMAG, SGD_SBRD_TIEMAC, SGD_SBRD_DISPFIN, SGD_SBRD_SOPORTE, SGD_SBRD_PROCEDI, ID_VERSION)
						VALUES ($codserie, $tsub, '" . iconv($odt->codificacion($detasub), 'UTF-8', $detasub) . "', '" . $sqlFechaD . "', '" . $sqlFechaH . "', 
                            $tiem_ag, $tiem_ac, '$med', '$soporte', '" . iconv($odt->codificacion($asu), 'UTF-8', $asu) . "', $version)";
                $rsIN = $db->conn->Execute($query);
                $tsub = '';
                $detasub = '';
                $tiem_ag = '';
                $tiem_ac = '';
                $soporte = '';
                ?>
						<script language="javascript">
						    document.adm_subserie.elements['detasub'].value= '';
							document.adm_subserie.elements['tsub'].value= '';
						    document.adm_subserie.elements['asu'].value= '';
						    document.adm_subserie.elements['soporte'].value= '';
							document.adm_subserie.elements['tiem_ag'].value= '';
							document.adm_subserie.elements['tiem_ac'].value= '';

					</script>
						<?php
            }
        }
    } else {
        echo "<script>alert('Los campos Serie, Subserie y Detalle son OBLIGATORIOS');</script>";
    }
}

if (! $_POST['modi_subserie'] && $_POST['tsub'] && ! $_POST['insertar_subserie']) {
    if ($codserie != 0 && $tsub != '') {
        $isqlB = "select * from sgd_sbrd_subserierd where sgd_srd_codigo = $codserie and sgd_sbrd_codigo = $tsub and id_version = $version ";
        // Selecciona el registro a actualizar
        $rs = $db->conn->Execute($isqlB); // Executa la busqueda y obtiene el registro a actualizar.
        $radiNumero = $rs->fields["SGD_SRD_CODIGO"];
        if ($radiNumero == '') {
            // $mensaje_err = "<HR><center><B><FONT COLOR=RED>EL C&Oacute;DIGO < $codserie >< $tsub > NO EXISTE. <BR> VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
        } else {
            // Carga Valores actuales
            $detasub = $rs->fields["SGD_SBRD_DESCRIP"];
            $sqlFechaD = $rs->fields["SGD_SBRD_FECHINI"];
            $sqlFechaH = $rs->fields["SGD_SBRD_FECHFIN"];
            $tiem_ag = $rs->fields["SGD_SBRD_TIEMAG"];
            $tiem_ac = $rs->fields["SGD_SBRD_TIEMAC"];
            $med = $rs->fields["SGD_SBRD_DISPFIN"];
            $soporte = $rs->fields["SGD_SBRD_SOPORTE"];
            $asu = $rs->fields["SGD_SBRD_PROCEDI"];
            // $fecha_busq = substr($sqlFechaD, 0, 10);
            // $fecha_busq2 = substr($sqlFechaH, 0, 10);
            $varFechaD = $fecha_busq;

            ?>
					<script language="javascript">
					document.adm_subserie.elements['detasub'].value= "<?=$detasub?>";
					document.adm_subserie.elements['tsub'].value= "<?=$tsub?>";
					document.adm_subserie.elements['asu'].value= "<?=$asu?>";
					document.adm_subserie.elements['tiem_ag'].value= "<?=$tiem_ag?>";
					document.adm_subserie.elements['tiem_ac'].value= "<?=$tiem_ac?>";
					document.adm_subserie.elements['soporte'].value= "<?=$soporte?>";
					document.adm_subserie.elements['med'].value= "<?=$med?>";
					document.adm_subserie.elements['fecha_busq'].value= "<?=$fecha_busq?>";
					document.adm_subserie.elements['fecha_busq2'].value= "<?=$fecha_busq2?>";
					dateAvailable.setSelectedDate("<?=$fecha_busq?>");
					dateAvailable2.setSelectedDate("<?=$fecha_busq2?>");	
					</script>
				<?php
        }
    } else {
        echo "<script>alert('Debe seleccionar la Serie y la Subserie');</script>";
    }
} else {
    ?>
 	<script language="javascript">

        document.adm_subserie.elements['detasub'].value= '';
		document.adm_subserie.elements['tsub'].value= '';
	    document.adm_subserie.elements['asu'].value= '';
	    document.adm_subserie.elements['soporte'].value= '';
		document.adm_subserie.elements['tiem_ag'].value= '';
		document.adm_subserie.elements['tiem_ac'].value= '';
		
	</script>
	<?php
}

// Selecciono Grabar Cambios
if ($_POST['modi_subserie']) {
    if ($codserie != 0 && $tsub != '' && $detasub != '') {
        $isqlB = "select * from sgd_sbrd_subserierd where sgd_srd_codigo = $codserie and sgd_sbrd_codigo = $tsub and id_version = $version ";
        $rs = $db->conn->Execute($isqlB);
        if ($rs && ! $rs->EOF) {

            $isqlUp = "update sgd_sbrd_subserierd
					   			set SGD_SBRD_DESCRIP = '" . $detasub . "'
						  			,SGD_SBRD_FECHINI = '$sqlFechaD'
						  			,SGD_SBRD_FECHFIN = '$sqlFechaH'
						  			,SGD_SBRD_TIEMAG =  $tiem_ag
 						  			,SGD_SBRD_TIEMAC =  $tiem_ac
 						  			,SGD_SBRD_DISPFIN = '$med'
						  			,SGD_SBRD_SOPORTE = '$soporte'
						  			,SGD_SBRD_PROCEDI = '" . $asu . "'
                        		where sgd_srd_codigo = $codserie
									and sgd_sbrd_codigo = $tsub
                                    and id_version = $version
								";
            $rsUp = $db->conn->Execute($isqlUp);
            $tsub = '';
            $detasub = '';
            $tiem_ag = '';
            $tiem_ac = '';
            $soporte = '';
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>SE MODIFICO LA SUBSERIE</FONT></B></center><HR>";
            ?>
					<script language="javascript">
				        document.adm_subserie.elements['detasub'].value= '';
						document.adm_subserie.elements['tsub'].value= '';
	    				document.adm_subserie.elements['asu'].value= '';
	    				document.adm_subserie.elements['soporte'].value= '';
						document.adm_subserie.elements['tiem_ag'].value= '';
						document.adm_subserie.elements['tiem_ac'].value= '';
					</script>
					<?php
        } else {
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>LA SUBSERIE <$detasub > YA EXISTE. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
        }
    } else {
        echo "<script>alert('La Serie, la Subserie y el Detalle son OBLIGATORIOS');</script>";
    }
}
if ($codserie > 0)
    include_once "$ruta_raiz/trd/lista_subseries.php";
?>
	<script type="text/javascript">

    	/*const input = document.getElementById('tsub');
    	input.addEventListener('change', updateValue);
    
    	function updateValue(e) {
    		document.getElementById("adm_subserie").submit();
    		//log.textContent = e.target.value;
    	}*/

    	function selectSerie() {
    		document.adm_subserie.elements['detasub'].value= "";
    		document.adm_subserie.elements['tsub'].value= "";
    		document.adm_subserie.elements['asu'].value= "";
    		document.adm_subserie.elements['tiem_ag'].value= "";
    		document.adm_subserie.elements['tiem_ac'].value= "";
    		document.adm_subserie.elements['soporte'].value= "";
    		document.adm_subserie.elements['med'].value= "";
    		document.adm_subserie.elements['fecha_busq'].value= "";
    		document.adm_subserie.elements['fecha_busq2'].value= "";
    	
    		document.getElementById("adm_subserie").submit();
    	}
	</script>

	</form>
	<p>
<?=$mensaje_err?>
</p>
</body>
</html>