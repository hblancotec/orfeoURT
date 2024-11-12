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

if (! $_SESSION['dependencia']) {
    include "$ruta_raiz/rec_session.php";
}
if (! $coddepe) {
    $coddepe = $dependencia;
}
if (! $tsub) {
    $tsub = 0;
}
if (! $codserie) {
    $codserie = 0;
}

if ($version) {
    $version = strtoupper(trim($version));
} else {
    $version = 0;
}

$fecha_fin = date("Y/m/d");
$where_fecha = "";
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" href="../estilos/orfeo.css">
    <link rel="stylesheet" type="text/css" href="js/spiffyCal/spiffyCal_v2_1.css">
    <script language="javascript">
		function selectVersion() {
			document.getElementById("formEnviar").submit();
		}
	</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
	<div id="spiffycalendar" class="text"></div>
	
    <?php
    $ruta_raiz = "..";
    include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    $db = new ConnectionHandler("$ruta_raiz");
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    if (!defined('ADODB_FETCH_ASSOC')){
        define('ADODB_FETCH_ASSOC', 2);
    }
    // $db->conn->debug = true;
    
    $encabezado = "" . session_name() . "=" . session_id() . "&krd=$krd&filtroSelect=$filtroSelect&accion_sal=$accion_sal&dependencia=$dependencia&tpAnulacion=$tpAnulacion&orderNo=";
    $linkPagina = "$PHP_SELF?$encabezado&accion_sal=$accion_sal&orderTipo=$orderTipo&orderNo=$orderNo";
    /*
     * GENERACION LISTADO DE RADICADOS
     * Aqui utilizamos la clase adodb para generar el listado de los radicados
     * Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
     * el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
     */
    error_reporting(7);
    
    ?>
  <form name="formEnviar" id="formEnviar" action="../trd/procModTrdArea.php?<?=session_name()."=".session_id()."&krd=$krd" ?>&estado_sal=<?=$estado_sal?>&estado_sal_max=<?=$estado_sal_max?>&pagina_sig=<?=$pagina_sig?>&dep_sel=<?=$dep_sel?>&nomcarpeta=<?=$nomcarpeta?>&orderNo=<?=$orderNo?>" method="post">
     <?php
    if ($activar_trda) {
        $valCambio = '1';
    }
    if ($desactivar_trda) {
        $valCambio = '0';
    }
    
    if ($activar_trda or $desactivar_trda) {
        if ($codserie != 0) {
            $var_where = " and sgd_srd_codigo = '$codserie'";
            if ($tsub != 0) {
                $var_where = $var_where . " and sgd_sbrd_codigo = '$tsub'";
                if ($tdoc != 0) {
                    $var_where = $var_where . " and sgd_tpr_codigo = '$tdoc'";
                }
            }
            $bien = true;
            if ($bien) {
                $isqlActi = "update SGD_MRD_MATRIRD set SGD_MRD_ESTA='$valCambio' " . "where depe_codi = '$coddepe'" . $var_where;
                $bien = $db->conn->Execute($isqlActi);
            }
            if ($bien) {
                $mensaje = "Modificado el Estado de la Relacion segun los parametros seleccionados<br> ";
                $db->conn->CommitTrans();
            } else {
                $mensaje = "No fue posible Activar la Relacion segun los parametros</br>";
                $db->conn->RollbackTrans();
            }
        } else {
            echo "<script>alert('Debe seleccionar por lo menos la Serie');</script>";
        }
    }
    ?>
 		<table class="borde_tab" style="width:100%" cellspacing="5">
			<tr>
				<td class="titulos2" style="text-align: center;">MODIFICACION RELACION TRD</td>
			</tr>
		</table>
		<br>
		<div style="margin: auto; justify-content: center; text-align: center; width: 100%; display: flex;">
		<table width="550" class="borde_tab" cellspacing="5">
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
				<td width="125" height="21" class='titulos2'>DEPENDENCIA</td>
				<td colspan="3" class="listado5">
                    <?php
                    include_once "$ruta_raiz/include/query/envios/queryPaencabeza.php";
                    $sqlConcat = $db->conn->Concat($db->conn->substr . "($conversion,1,5) ", "'-'", $db->conn->substr . "(depe_nomb,1,30) ");
                    $sql = "select $sqlConcat ,depe_codi from dependencia where id_version = $version order by depe_codi ";
                    $rsDep = $db->conn->Execute($sql);
                    if (! $depeBuscada)
                        $depeBuscada = $dependencia;
                    print $rsDep->GetMenu2("coddepe", "$coddepe", false, false, 0, " onChange='submit();' class='select'");
                    ?>
            	</td>
			</tr>
			<tr>
				<td width="125" height="21" class='titulos2'>SERIE</td>
				<td colspan="3" class="listado5">
                <?php
                    include "$ruta_raiz/trd/actu_matritrd.php";
                    if (! $codserie)
                        $codserie = 0;
                    $fechah = date("dmy") . " " . time("h_m_s");
                    $fecha_hoy = Date("Y-m-d");
                    $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
                    $check = 1;
                    $fechaf = date("dmy") . "_" . time("hms");
                    $num_car = 4;
                    $nomb_varc = "s.sgd_srd_codigo";
                    $nomb_varde = "s.sgd_srd_descrip";
                    include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
                    $querySerie = "select distinct ($sqlConcat) as detalle, s.sgd_srd_codigo
                    	      from sgd_srd_seriesrd s,sgd_mrd_matrird m
                    		  where s.sgd_srd_codigo = m.sgd_srd_codigo and m.depe_codi = '$coddepe' and id_version = $version 
                    		  order by detalle ";
                    $rsD = $db->conn->Execute($querySerie);
                    $comentarioDev = "Muestra las Series Docuementales";
                    include "$ruta_raiz/include/tx/ComentarioTx.php";
                    print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false, "", "onChange='submit()' class='select'");
                ?>
            </tr>
			<tr>
				<td width="125" height="21" class='titulos2'>SUBSERIE</td>
				<td colspan="3" class="listado5">
            	<?php
                    $nomb_varc = "su.sgd_sbrd_codigo";
                    $nomb_varde = "su.sgd_sbrd_descrip";
                    include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
                    $querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo
                    	      from sgd_sbrd_subserierd su, sgd_mrd_matrird m
                    		  where su.sgd_srd_codigo = '$codserie' and '" . $fecha_hoy . "' between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
                    		  and m.depe_codi = '$coddepe' and su.sgd_srd_codigo = m.sgd_srd_codigo and su.id_version = $version 
                    			 order by detalle
                    			  ";
                    $rsSub = $db->conn->Execute($querySub);
                    include "$ruta_raiz/include/tx/ComentarioTx.php";
                    print $rsSub->GetMenu2("tsub", $tsub, "0:-- Todas las subseries documentales --", false, "", "onChange='submit()' class='select'");
            
                ?>
              	</td>
            </tr>
			<tr>
				<td width="125" height="21" class='titulos2'>TIPO DOCUMENTAL</td>
				<td colspan="3" class="listado5">
                <?php
                    $nomb_varc = "t.sgd_tpr_codigo";
                    $nomb_varde = "t.sgd_tpr_descrip";
                    include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
                    $queryTipDcto = "select distinct ($sqlConcat) as detalle, t.sgd_tpr_codigo
                    	          from sgd_tpr_tpdcumento t, sgd_mrd_matrird m ,sgd_sbrd_subserierd su
                    			  where m.depe_codi = '$coddepe'
                    			  and m.sgd_srd_codigo = '$codserie'
                    			  and m.sgd_sbrd_codigo = '$tsub'
                    			  and m.sgd_tpr_codigo = t.sgd_tpr_codigo
                     			  and '" . $fecha_hoy . "' between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
                    		      and su.sgd_srd_codigo = m.sgd_srd_codigo
                     		      and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
                                  and t.id_version = $version   
                    			 order by detalle
                    			  ";
                    $rsTipDcto = $db->conn->Execute($queryTipDcto);
                    include "$ruta_raiz/include/tx/ComentarioTx.php";
                    print $rsTipDcto->GetMenu2("tdoc", $tdoc, "0:-- Todos los tipos documentales --", false, "", "onChange='submit()' class='select'");
                ?>
  				</td>
			</tr>
			<tr>
				<td height="26" colspan="4" valign="top" class="titulos2" style="text-align: center;">
					<input type=submit name=activar_trda value='Activar' class=botones_funcion> 
					<input type=submit name=desactivar_trda value='Desactivar' class=botones_funcion>
				</td>
			</tr>
		</table>
		</div>
		<br>
 <?php echo "<hr><center><b><span class='alarmas'>$mensaje</span></center></b></hr>"; ?>
  </form>
</body>
</html>