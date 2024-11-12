<?php

//header('content-type:text/html;charset=utf-8');

session_start();
if (count($_SESSION) == 0) {
    die(include "./sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

include_once './_conf/constantes.php';
$permitirModSubExp = false; // Permite opcion de menu de SubExpediente
$verradicado = $verrad;
$carpetaOld = $carpeta;

$krdOld = $krd;
$menu_ver_tmpOld = $menu_ver_tmp;
$menu_ver_Old = $menu_ver;

$ruta_raiz = '';

if (! isset($_SESSION['dependencia']))
    include_once ("./rec_session.php");
if (! $ent)
    $ent = substr($verradicado, - 1);
if (! $carpeta)
    $carpeta = $carpetaOld;
if (! $menu_ver_tmp)
    $menu_ver_tmp = $menu_ver_tmpOld;
if (! $menu_ver)
    $menu_ver = $menu_ver_Old;
if (! $krd)
    $krd = $krdOld;
if (! $menu_ver)
    $menu_ver = 3;
if ($menu_ver_tmp)
    $menu_ver = $menu_ver_tmp;
if (! defined('ADODB_ASSOC_CASE'))
    define('ADODB_ASSOC_CASE', 1);

include_once "./include/db/ConnectionHandler.php";

if ($verradicado)
    $verrad = $verradicado;
if (! $ruta_raiz)
    $ruta_raiz = ".";

$numrad = $verrad;
$db = new ConnectionHandler(ORFEOPATH);
$db->conn->SetFetchMode(3);
//$db->conn->debug = true;

$usuaModifica1 = "";
$usuaModifica2 = "";
$sqlus1 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TRD = 1  ";
$rsus1 = $db->conn->Execute($sqlus1);
if ($rsus1 && ! $rsus1->EOF) {
    $usuaModifica1 = $rsus1->fields['USUA_NOMB'];
}
$sqlus2 = "select top 1 USUA_CODI, DEPE_CODI, USUA_NOMB, USUA_EMAIL from USUARIO where USUA_MODIFICA_TIPODOC = 1 ";
$rsus2 = $db->conn->Execute($sqlus2);
if ($rsus2 && ! $rsus2->EOF) {
    $usuaModifica2 = $rsus2->fields['USUA_NOMB'];
}

$sqlPerm = "SELECT dbo.VALIDAR_ACCESO_RADEXP ($verradicado, '', '$krd') AS PERMISO";
$permiso = $db->conn->Getone($sqlPerm);

if ($_SESSION["modificatrd"] == 1 || $_SESSION["modificaTipodoc"] ==1) {
    $permiso = 0;
}

$tiporad = substr($numrad, - 1);
if ($permiso == 0) {
    // ## Se incluye este archivo para almacenar en BD el log de consulta de radicado
    //include_once "./logConsultas.php";

    $coditrdx = "SELECT	s.SGD_TPR_DESCRIP as TPRDESCRIP
				FROM	RADICADO r,
						SGD_TPR_TPDCUMENTO s
				WHERE	r.TDOC_CODI = s.SGD_TPR_CODIGO AND
						r.RADI_NUME_RADI = $verrad";
    $res_coditrdx = $db->conn->Execute($coditrdx);

    $TDCactu = $res_coditrdx->fields['TPRDESCRIP'];

    if ($tiporad == 2) {
        $recepcion = "SELECT a.mrec_desc as MEDIO_RECEPCION
				FROM RADICADO r, MEDIO_RECEPCION a
				WHERE a.MREC_CODI = r.MREC_CODI AND r.RADI_NUME_RADI = '$verrad'";
        $titulo_mediorecp = "MEDIO DE RECEPCIÓN";
    } else {
        $recepcion = "SELECT a.SGD_FENV_DESCRIP as MEDIO_RECEPCION
                    FROM RADICADO r, SGD_FENV_FRMENVIO a
                    WHERE a.SGD_FENV_CODIGO = r.SGD_FENV_CODIGO AND r.RADI_NUME_RADI = '$verrad'";
        $titulo_mediorecp = "MEDIO DE ENVÍO";
    }
    $medio_recep = $db->conn->Execute($recepcion);

    $desc_mediorecp = $medio_recep->fields['MEDIO_RECEPCION'];

    if ($carpeta == 8) {
        $info = 8;
        $nombcarpeta = "Informados";
    }

    // verificacion si el radicado se encuentra en el usuario Actual
    include (ORFEOPATH . "/tx/verifSession.php");
    if (! isset($_SESSION['dependencia']))
        include (ORFEOPATH . "rec_session.php");
    $depeCodi = $_SESSION['dependencia'];
    ?>

<html>
<head>
<title>.: Modulo total :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta charset="UTF-8">
<link rel="stylesheet" href="estilos/orfeo.css">
<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<!-- seleccionar todos los checkboxes-->
<script language="JavaScript">
			var contadorVentanas = 0;
			function datosBasicos() {
					window.location = 'radicacion/NEW.PHP?krd=<?=$krd?>&<?=session_name()."=".session_id()?>&<?="nurad=$verrad&fechah=$fechah&ent=$ent&Buscar=Buscar Radicado&carpeta=$carpeta&nomcarpeta=$nomcarpeta"; ?>';
			}

			function mostrar(nombreCapa) {
				document.getElementById(nombreCapa).style.display="";
			}

			function ocultar(nombreCapa) {
				if (document.getElementById(nombreCapa)) {
					document.getElementById(nombreCapa).style.display="none";
				}
			}

			function regresar() {   	
				document.form2.submit();
			}
		</script>
	<?php

    // echo $verradPermisos . ":" . $datoVer . "</br>";

    $winLoadJs = "\n\t" . 'function  window_onload() {' . "\n";
    $changeDepe = 'function changedepesel(xx) {';
    // if($verradPermisos == "Full" or $datoVer=="985") {
    if (True) {
        if ($datoVer == "985") {
            if ($verradPermisos == "Full" or $datoVer == "985") {
                $winLoadJs .= "\t" . 'window_onload2();' . "\n";
            }
        }
        include ("./pestanas.js");
    } else {
        $winLoadJs = $changeDepe;
    }

    $winLoadJs .= '}' . "\n";
    echo '<script language="JavaScript">' . "\n";
    echo $winLoadJs;
    ?>
		function window_onload2() {
	<?php
    if ($menu_ver == 3) {
        echo "ocultar_mod(); ";
        if ($ver_tipodoc) {
            echo "ver_tipodocumento();";
        }
        if ($ver_causal) {
            echo "ver_causales();";
        }
        if ($ver_tema) {
            echo "ver_temas();";
        }
        if ($ver_sectores) {
            echo "ver_sector();";
        }
        if ($ver_flujo) {
            echo "ver_flujo();";
        }
        if ($ver_subtipo) {
            echo "verSubtipoDocto();";
        }
        if ($ver_VinAnexo) {
            echo "verVinculoDocto();";
        }
        if ($ver_VinResp) {
            echo "verVinculoResp();";
        }
    }
    ?>
		}

		function verNotificacion() {
		   mostrar("mod_notificacion");
		   ocultar("tb_general");
		   ocultar("mod_causales");
		   ocultar("mod_tipodocumento");
		   ocultar("mod_temas");
		   ocultar("mod_sector");
		   ocultar("mod_flujo");
		   ocultar("mod_resolucion");
		   ocultar("mod_decision");
		}

		function ver_datos() {
		   mostrar("tb_general");
		   ocultar("mod_causales");
		   ocultar("mod_tipodocumento");
		   ocultar("mod_temas");
		   ocultar("mod_sector");
		   ocultar("mod_flujo");
		   ocultar("mod_resolucion");
		   ocultar("mod_notificacion");
		   ocultar("mod_decision");
		}

		function ocultar_mod() {
		   ocultar("mod_causales");
		   ocultar("mod_tipodocumento");
		   ocultar("mod_temas");
		   ocultar("mod_sector");
		   ocultar("mod_flujo");
		   ocultar("mod_resolucion");
		   ocultar("mod_notificacion");
		   ocultar("mod_decision");
		}

		function ver_tipodocumental() {
	<?php
    if ($menu_ver_tmp != 2) {
        ?>
		   ocultar("tb_general");
		   ocultar("mod_causales");
		   ocultar("mod_temas");
		   ocultar("mod_flujo");
		   ocultar("mod_resolucion");
		   ocultar("mod_tipodocumento");
		   ocultar("mod_notificacion");
	<?php
    }
    ?>
		}

		function ver_tipodocumento() {
		   ocultar("tb_general");
		   ocultar("mod_causales");
		   ocultar("mod_temas");
		   ocultar("mod_flujo");
		   ocultar("mod_resolucion");
		   mostrar("mod_tipodocumento");
		   ocultar("mod_notificacion");
		   ocultar("mod_decision");
		}

		function verDecision() {
		   ocultar("tb_general");
		   ocultar("mod_causales");
		   ocultar("mod_temas");
		   ocultar("mod_flujo");
		   ocultar("mod_resolucion");
		   ocultar("mod_tipodocumento");
		   mostrar("mod_decision");
		   ocultar("mod_notificacion");
		}

		

		function verVinculoDocto() {
	<?php
    echo "ver_tipodocumental(); ";
    ?>
	  window.open("./vinculacion/mod_vinculacion.php?verrad=<?=$verrad?>&krd=<?=$krd?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>","Vinculacion_Documento","height=500,width=750,scrollbars=yes");
		}

		function verVinculoResp() {
			<?php
    // echo "ver_tipodocumental(); ";
    ?>
			window.open("./vinculacion/mod_respuesta.php?verrad=<?=$verrad?>&krd=<?=$krd?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>","Vinculacion_Respuesta","height=500,width=750,scrollbars=yes");
		}	
		
		
		function ver_despla() {
	  window.open("./radicacion/mod_despla.php?<?=session_name()."=".session_id()?>&verrad=<?=$verrad?>&krd=<?=$krd?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>","Vinculacion_Documento","height=150,width=200,scrollbars=yes");
		}

		function verResolucion() {
		   ocultar("tb_general");
		   ocultar("mod_causales");
		   ocultar("mod_temas");
		   ocultar("mod_flujo");
		   ocultar("mod_tipodocumento");
		   mostrar("mod_resolucion");
		   ocultar("mod_notificacion");
		}

		function ver_temas() {
		   ocultar("tb_general");
		   ocultar("mod_tipodocumento");
		   ocultar("mod_causales");
		   ocultar("mod_sector");
		   ocultar("mod_flujo");
		   ocultar("mod_tipodocumento");
		   mostrar("mod_temas");
		   ocultar("mod_resolucion");
		   ocultar("mod_notificacion");
		}

		function ver_flujo() {
		   mostrar("mod_flujo");
		   ocultar("tb_general");
		   ocultar("mod_tipodocumento");
		   ocultar("mod_causales");
		   ocultar("mod_temas");
		   ocultar("mod_sector");
		   mostrar("mod_flujo");
		   ocultar("mod_resolucion");
		   ocultar("mod_notificacion");
		}

		function ver_tipodocuTRD(codserie,tsub, tdoc, retipifica) {
			debugger;
    	<?php
            echo "ver_tipodocumental();\n";
            $isqlDepR = "SELECT RADI_DEPE_ACTU, RADI_USUA_ACTU, SGD_CAMBIO_TRD
        			     FROM RADICADO WHERE RADI_NUME_RADI = $numrad";
            $rsDepR = $db->conn->Execute($isqlDepR);
            if ($rsDepR === false) {
                echo "Error de Consulta";
                echo $db->conn->ErrorMsg();
                exit(1);
            }
        
            $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
            $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
            $cambio = $rsDepR->fields['SGD_CAMBIO_TRD'];
            $ind_ProcAnex = "N";
            
            /*$var = "<script> document.writeln(tdoc); </script>";
            $sqlPqr = "	SELECT SGD_TPR_CODIGO FROM SGD_TEMAS_TIPOSDOC WHERE	SGD_TPR_CODIGO = $var ";
            $rsPqr = $db->conn->Execute($sqlPqr);
            $pqr = $rsPqr->fields['SGD_TPR_CODIGO'];*/
        ?>
        	/*var pqr = '<?php echo $pqr; ?>';
        	var cambio = '<?php echo $cambio; ?>';
        	var usua = '<?php echo $codusua; ?>';
        	var depe = '<?php echo $coddepe; ?>';
        	var tiporad = '<?php echo $tiporad; ?>';
        	if ((cambio == '' || cambio == '0') && (codserie == 176 || tdoc == 2 || pqr != '') && tiporad == 2 && retipifica == 0) {
        		var opcion = confirm("Por ser documento tipificado como PQRSD para su cambio comun\u00edquese con XXXXXX del Grupo de Biblioteca y Archivo, desea enviar notificaci\u00f3n al \u00e1rea ? ");
                if (opcion == true) {
    				debugger;
                	var rad = <?php echo $numrad; ?>;

						var parametros = {
                    		"rad"  : rad,
                    		"tipo" : 1,
                    		"usua" : usua,
                    		"depe" : depe
                    	};
                    			
                    	$.ajax({
                    		url: './class_control/ModificaTRD.php',
                    		type: 'POST',
                    		cache: false,
                    		async: false,
                    		data:  parametros,
                    		success: function(text) {
                    			debugger;
                    			if(text != '1') {
                					alert("Error al enviar la notificaci\u00f3n, por favor comuníquese con el administrador del sistema");
                    			} else {
                    				//alert("Error en el proceso, consulte el administrador del sistema." + text);
                    			} 
                    			document.form2.submit();
                    		},
                    		error: function(text) { alert('Se ha producido un error ' + text); }
                    	});
                } else {
                	document.form2.submit();
                }
            } else {*/
       			window.open("./radicacion/tipificar_documento.php?nurad=<?=$verrad?>&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&krd=<?=$krd?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&tsub="+tsub+"&codserie="+codserie,"Tipificacion_Documento","height=380,width=750,scrollbars=yes");
       		//}
    	}
	
		function hidden_tipodocumento() {
	<?php
    if (! $ver_tipodoc) {
        echo "\t" . '//ocultar_mod();' . "\n";
    }
    ?>
		}
		/**
		  * FUNCION DE JAVA SCRIPT DE LAS PESTANAS
		  * Esta funcion es la que produce el efecto de pertanas de mover a,
		  * Reasignar, Informar, Devolver, Vobo y Archivar
		  **/
	</script>
<div id="spiffycalendar" class="text"></div>
<script language="JavaScript" src="js/spiffyCal/spiffyCal_v2_1.js"></script>
<link rel="stylesheet" type="text/css"
	href="js/spiffyCal/spiffyCal_v2_1.css">
</head>
	<?php
    // Modificado Supersolidaria
    if (isset($_POST['ordenarPor']) && $_POST['ordenarPor'] != "") {
        $body = "document.location.href='#t1';";
    }
    ?>
	<body bgcolor="#FFFFFF" topmargin="0" data-editor="ClassicEditor" data-collaboration="false" onLoad="window_onload();<?php print $body; ?>">
	<?php
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $fechah = date("dmy_h_m_s") . " " . time("h_m_s");
    $check = 1;
    $numeroa = 0;
    $numero = 0;
    $numeros = 0;
    $numerot = 0;
    $numerop = 0;
    $numeroh = 0;
    
    include ("./ver_datosrad.php");
    // if($verradPermisos == "Full" or $datoVer=="985") {
    if (True) {} else {
        $numRad = $verrad;
        if ($nivelRad == 1)
            include (ORFEOPATH . "seguridad/sinPermisoRadicado.php");
        if ($nivelRad == 1)
            die("-");
    }
    ?>
		
	<table border='0' width='100%' cellpadding='0' cellspacing='5'
		class='borde_tab'>
		<tr>
			<td class='titulos2' width='10%' align="center"><a class=vinculos
				HREF='javascript:history.back();'> PAGINA ANTERIOR </a></td>
			<td class="titulos2" width='70%'>
				
				<?php
    if ($krd) {
        $isql = "SELECT *
								FROM usuario
								WHERE USUA_LOGIN ='$krd' AND
								USUA_SESION ='" . substr(session_id(), 0, 30) . "'";
        $rs = $db->conn->Execute($isql);
        // Validacion de Usuario y COntrasena MD5
        // echo "** $krd *** $drde";
        if (trim($rs->fields["USUA_LOGIN"]) == trim($krd)) {
            // $iusuario = " and us_usuario='$krd'";
            // $isql = "select a.* from radicado a where radi_depe_actu=$dependencia and radi_nume_radi=$verrad";
            echo "\tDATOS DEL RADICADO No\n";
            $hrefRad = "./radicacion/NEW.php?nurad=$verrad&Buscar=BuscarDocModUS";
            $hrefRad .= "&krd=$krd&" . session_name() . "=" . session_id();

            $hrefRad .= "&Submit3=ModificarDocumentos&Buscar1=BuscarOrfeo78956jkgf";

            if ($mostrar_opc_envio == 0 and $carpeta != 8 and ! $agendado) {
                $ent = substr($verrad, - 1);
                echo "<a title='Click para modificar el Documento' href='$hrefRad' notborder>$verrad</a>";
            } else {
                echo $verrad;
            }
            ?>
				
			</td>
			<td class="titulos5" align="center">
				
				<?php
            $fechaHoy = $db->conn->OffsetDate(0);
            $iSql = "	SELECT	PRES_ESTADO, 
										USUA_LOGIN_ACTU, 
										PRES_FECH_VENC,
										cast((PRES_FECH_VENC - $fechaHoy) as numeric) as DIAS_VENC
								FROM	PRESTAMO
								WHERE	RADI_NUME_RADI=" . $verrad . " AND PRES_ESTADO<>4
								ORDER BY PRES_ESTADO";

            $rsPrestamo = $db->conn->Execute($iSql);

            $estadoPrestamo = $rsPrestamo->fields["PRES_ESTADO"];
            $usPrestamo = $rsPrestamo->fields["USUA_LOGIN_ACTU"];
            $fechaPrestamo = $rsPrestamo->fields["PRES_FECH_VENC"];
            $diasVenc = $rsPrestamo->fields["DIAS_VENC"];

            if ($estadoPrestamo == 2 and $diasVenc <= 8) {
                if ($usPrestamo == $krd) {
                    ?>
				
					<a class="vinculos"
				href='./solicitar/Reservar.php?radicado=<?="$verrad&Accion=Renovar&usuario=$krd&dependencia=$dependencia&krd=$krd"?>'>Renovar
					Prestamo</a> <font size=1>Vencimiento en<?=$diasVenc?> Dias</font>

				<?php
                }
            }
            ?>
					
			</td>
			<td class="titulos5" align="center"><a class="vinculos"
				href='./solicitar/Reservas.php?radicado=<?="$verrad&usuario=$krd&dependencia=$dependencia&krd=$krd"?>'>Solicitados</a>
			</td>
			<td class="titulos5" align="center">
		
				<?php
            if ($usPrestamo == $krd and ($estadoPrestamo == 1 or $estadoPrestamo == 2)) {
                if ($estadoPrestamo == 2) {
                    echo "<font size='1' color='red'>Fecha Entrega: " . substr($fechaPrestamo, 0, 10) . "</font>";
                }
            } elseif ($estadoPrestamo != 1 && $estadoPrestamo != 5 && $estadoPrestamo != 6 && $estadoPrestamo != 7) {
                ?>
				
				<a class="vinculos"
				href='./solicitar/Reservar.php?radicado=<?="$verrad&usuario=$krd&dependencia=$dependencia&krd=$krd&FormAction=solicitar"?>'>Solicitar
					F&iacute;sico</a>
		
				<?php
            } else {
                echo "";
            }
            ?>
			</td>
		</tr>
	</table>
	<table width=100% class='t_bordeGris'>
		<tr class='t_bordeGris'>
			<td width='33%' height="6">
				<table width='100%' border='0' cellspacing='0' cellpadding='0'>
					<tr class="celdaGris">
	<?php
            $datosaenviar = "fechaf=$fechaf&mostrar_opc_envio=$mostrar_opc_envio";
            $datosaenviar .= "&tipo_carp=$tipo_carp&carpeta=$carpeta";
            $datosaenviar .= "&nomcarpeta=$nomcarpeta&datoVer=$datoVer";
            $datosaenviar .= "&ascdesc=$ascdesc&orno=$orno";
            ?>
		<td height="20" class="titulos2">LISTADO DE:</td>
					</tr>
					<tr>
						<td height="20" class="info"><?=$nomcarpeta ?>
				</td>
					</tr>
				</table>
			</td>
			<td width='33%' height="6">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="10%" class="titulos2" height="20">USUARIO:</td>
					</tr>
					<tr>
						<td width="90%" height="20" class="info"><?=$_SESSION['usua_nomb'] ?></td>
					</tr>
				</table>
			</td>
			<td height="6" width="33%">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="16%" class="titulos2" height="20">DEPENDENCIA:</td>
					</tr>
					<tr>
						<td width="84%" height="20" class="info"><?=$_SESSION['depe_nomb'] ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<form name="form1" id="form1"
		action="<?=$ruta_raiz?>/tx/formEnvio.php?krd=<?=$krd?>&<?=session_name()?>=<?=session_id()?>"
		method="post">

	<?php include_once (ORFEOPATH . "tx/txOrfeo.php"); ?>

	<input type="hidden" name='checkValue[<?=$verrad?>]' value='CHKANULAR'>
		<input type="hidden" name=enviara value='9'>

	<?php
            if (($codusuario != $radi_usua_actu) || ($depeCodi != $coddepe)) {
                echo "<input type=hidden name='noraiz' id='noraiz' value=$verrad>";
            } else {
                echo "<input type=hidden name='noraiz' id='noraiz' value=''>";
            }
            ?>
	</form>
	<table border=0 align='center' cellpadding="0" cellspacing="0"
		width="100%">
		<form
			action='verradicado.php?<?=session_name()?>=<?=trim(session_id())?>&krd=<?=$krd?>&verrad=<?=$verrad?>&datoVer=<?=$datoVer?>&chk1=<?=$verrad."&carpeta=$carpeta&nomcarpeta=$nomcarpeta"?>'
			method=post name='form2'>
	<?php
            echo "<input type='hidden' name='fechah' value='$fechah'>";

            if ($flag == 2) {
                echo "<center>NO SE HA PODIDO REALIZAR LA CONSULTA</center>";
            } else {
                $row = array();
                $rowd = array();
                $row1 = array();
                $derv = array();

                if ($info) {
                    $row["INFO_LEIDO"] = 1;
                    $row1["DEPE_CODI"] = $dependencia;
                    $row1["USUA_CODI"] = $codusuario;
                    $row1["RADI_NUME_RADI"] = $verrad;
                    $rs = $db->update("informados", $row, $row1);
                } elseif (($leido != "no" or ! $leido) and $datoVer != 985) {
                    $row["RADI_LEIDO"] = 1;
                    $row1["radi_depe_actu"] = $dependencia;
                    $row1["radi_usua_actu"] = $codusuario;
                    $row1["radi_nume_radi"] = $verrad;
                    $rs = $db->update("radicado", $row, $row1);

                    // Se comprueba si el radicado derivado esta leido
                    $rsq1 = "SELECT 
								count(RADI_NUME_RADI) as existe
							 FROM  
								SGD_RG_MULTIPLE 
							 WHERE 
								radi_nume_radi = $verrad
								and usuario    = $codusuario
								and area       = $dependencia
								and radi_leido != 1";

                    $res = $db->conn->Execute($rsq1);
                    $existe = $res->fields['existe'];

                    if ($existe) {
                        $rowd["RADI_LEIDO"] = 1;
                        $derv["AREA"] = $dependencia;
                        $derv["USUARIO"] = $codusuario;
                        $derv["RADI_NUME_RADI"] = $verrad;
                        $rsq = $db->update("SGD_RG_MULTIPLE", $rowd, $derv);
                    }
                }
            }

            include ("./ver_datosrad.php");
            include ("./ver_datosgeo.php");
            $tipo_documento .= "<input type=hidden name=menu_ver value='$menu_ver'>";
            $hdatos = session_name() . "=" . session_id();
            $hdatos .= "&leido=$leido&nomcarpeta=$nomcarpeta";
            $hdatos .= "&tipo_carp=$tipo_carp&carpeta=$carpeta";
            $hdatos .= "&krd=$krd&verrad=$verrad&datoVer=$datoVer";
            $hdatos .= "&fechah=fechah&menu_ver_tmp=";
            ?>
		<tr>
				<td height="100%" rowspan="4" width="3%" valign="top"
					class="listado2">&nbsp;</td>
				<td height="8" width="94%" class="listado2">
			   <?php
            $datos1 = "";
            $datos2 = "";
            $datos3 = "";
            $datos4 = "";
            $datos5 = "";
            if ($menu_ver == 5) {
                $datos5 = "_R";
            }
            if ($menu_ver == 1) {
                $datos1 = "_R";
            }
            if ($menu_ver == 2) {
                $datos2 = "_R";
            }
            if ($menu_ver == 3) {
                $datos3 = "_R";
            }
            if ($menu_ver == 4) {
                $datos4 = "_R";
            }
            ?>
			<table border="0" width="69%" cellpadding="0" cellspacing="0">
						<tr>
							<td width="13%" valign="bottom" class=""><a
								href='verradicado.php?<?=$hdatos ?>3'><img
									src='./imagenes/infoGeneral<?=$datos3?>.gif' alt='' border=0
									width="110" height="25"></a></td>
							<td width="13%" valign="bottom" class=""><a
								href='verradicado.php?<?=$hdatos ?>1'><img
									src='./imagenes/historico<?=$datos1?>.gif' alt='' border=0
									width="110" height="25"></a></td>
							<td width="13%" valign="bottom" class=""><a
								href='verradicado.php?<?=$hdatos ?>2'><img
									src='./imagenes/documentos<?=$datos2?>.gif' alt='' border=0
									width="110" height="25"></a></td>
							<td width="61%" valign="bottom" class=""><a
								href='verradicado.php?<?=$hdatos ?>4'><img
									src='./imagenes/expediente<?=$datos4?>.gif' alt='' border=0
									width="110" height="25"></a></td>
							<td width="61%" valign="bottom" class="">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td height="100%" rowspan="4" width="3%" valign="top"
					class="listado2">&nbsp;</td>
			</tr>
			<tr>
				<td bgcolor="" width="94%" height="100">
	<?php
            switch ($menu_ver) {
                case 1:
                    include "./ver_historico.php";
                    break;
                case 2:
                    include "./lista_anexos.php";
                    break;
                case 3:
                    include "./lista_general.php";
                    break;
                case 4:
                    include "./expediente/lista_expediente.php";
                    break;
                case 5:
                    include "./plantilla.php";
            }
            ?>
		  </td>
			</tr>
			<input type='hidden' name='menu_ver' value='<?=$menu_ver ?>'>
			<tr>
				<td height="17" width="94%" class="celdaGris"> <?php
        } else {
            ?>  </td>
			</tr>
	
	</table>
	<form name='form1' action='enviar.php' method='post'>
		<input type="hidden" name="depsel"> <input type="hidden"
			name="depsel8"> <input type="hidden" name="carpper">
		<center>
			<span class='titulosError'>SU SESION HA TERMINADO O HA SIDO INICIADA
				EN OTRO EQUIPO</span><BR> <span class='eerrores'>
		
		</center>
	</form>
		<?php
        }
    } else {
        echo "<center><b><span class='eerrores'>NO TIENE AUTORIZACION PARA INGRESAR</span><BR><span class='eerrores'><a href='login.php' target=_parent>Por Favor intente validarse de nuevo. Presione aca!</span></a>";
    }
    ?>
</td>
	</tr>
	<tr>
		<td height="15" width="94%" class="listado2">&nbsp;</td>
	</tr>
	</form>
	</table>
</body>
</html>

<?php
} elseif ($permiso != 0) {
    // header("Location: busqueda/busquedaPiloto.php");
    // include "$ruta_raiz/seguridad/sinPermisoRadicado.php";
    /*
     * echo "<center>Se ha presentado un inconveniente con este radicado, por favor Comuniquese con el administrador del sistema <br>
     * (Ext.: 4043-4054-4070-4071-4074-4077) e indiquele de esta situación con el número de radicado consultado. <br>
     * Muchas gracias por su colaboración </center>";
     */
    echo "<script> alert ('NO TIENE PERMISOS PARA ACCEDER AL RADICADO No. $verradicado'); window.location='busqueda/busquedaPiloto.php';</script>";
    //header("Location: busqueda/busquedaPiloto.php");
    
	//die;
}

?>