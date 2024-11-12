<?php 
//ob_start();
?>
<html>
<head>
<title>.: Expedientes orfeo :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
.borde_tab {
	border: thin solid #377584;
}

.titulos4 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	line-height: 10px;
	font-weight: bolder;
	color: #FFFFFF;
	background-color: #006699;
	text-indent: 5pt;
	text-transform: uppercase;
	height: 30px;
}

.titulos5 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	color: #000000;
	background-color: #e0e6e7;
	text-indent: 5pt;
	vertical-align: middle;
}

.listado1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	text-transform: none;
	color: #000000;
	text-decoration: none;
	background-color: #FFFFFF;
	text-align: left;
	vertical-align: middle;
	height: 30px;
}

.leidos2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: normal;
	color: #006690;
	text-decoration: none;
}

.listado5 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	text-transform: none;
	color: #000000;
	text-decoration: none;
	background-color: #e3e8ec;
	text-align: left;
	text-indent: 5pt;
	vertical-align: middle;
}

.leidos {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 10px;
		font-weight: bolder;
		color: #006699;
		text-decoration: none;
}

</style>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
	<table border="0" width="100%" class='borde_tab' align="center" cellspacing="1">
		<tr class='timparr'>
			<td colspan="6" class="titulos4" align="center">
				<span class="titulos4">Documentos Pertenecientes al expediente <?php echo $_GET['exp'] ?></span>
			</td>
		</tr>
		<tr class='timparr'>
			<td>
				<table border="0" width="100%" class="borde_tab" align="center" cellpadding="0" cellspacing="0">
					<tr class="listado5" style="height: 25px;">
						<td>&nbsp;</td>
						<td class="titulos5" align="center">Radicado</td>
						<td class="titulos5" align="center"></td>
						<td class="titulos5" align="center" width="15%">Fecha Radicaci&oacute;n</td>
						<td class="titulos5" align="center">Tipo Documental</td>
						<td class="titulos5" align="center">Asunto</td>
						<td class="titulos5"></td>
					</tr>
<?php
$numExpediente = $_GET['exp'];
$expedienteSeleccionado = $numExpediente;
$ruta_raiz = "..";
require $ruta_raiz . "/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
if (!mkdir($numExpediente)) die("No se pudo crear carpeta ".$numExpediente);

$fp = fopen($numExpediente."/index.html", 'w');
fwrite($fp, "");
fclose($fp);

// ## <!-- INICIO MOSTRAR DOCUMENTOS ELECTRONICOS -->
// ## consulta: documentos electronicos adjuntos al expediente
$consulta = "SELECT	a.ANEXOS_EXP_NOMBRE AS NOMBRE,
				convert(varchar(10), a.ANEXOS_EXP_FECH_CREA,103) AS TIEMPO,
				b.SGD_TPR_DESCRIP AS TD,
				a.ANEXOS_EXP_DESC AS DESCRIPT,
				a.ANEXOS_EXP_PATH AS RUTA,
				a.ANEXOS_EXP_ID	AS IDEA,
				a.ANEXOS_EXP_ORDEN
		FROM	SGD_ANEXOS_EXP a,
				SGD_TPR_TPDCUMENTO b
		WHERE	SGD_EXP_NUMERO = '$numExpediente'
				AND a.SGD_TPR_CODIGO = b.SGD_TPR_CODIGO
				AND a.ANEXOS_EXP_ESTADO <> 1
		ORDER BY a.ANEXOS_EXP_FECH_CREA, a.ANEXOS_EXP_ORDEN, a.ANEXOS_EXP_ID";

$adjun = $db->conn->Execute($consulta);
$htmlPrintIni1 = '<tr class="listado1"  class="tpar">';
$htmlPrintIni2 = '<td style="margin-left: 10px; margin-right: 10px; ' . 'padding-bottom: 5px;' . 'padding-left: 10px; padding-right: 10px;">';
$htmlPrintFin2 = '</td>';
$htmlPrintFin1 = '</tr>';
$contador = 1;

while (! $adjun->EOF and ! empty($adjun)) {
    $nombre = $adjun->fields['NOMBRE'];
    $tiempo = $adjun->fields['TIEMPO'];
    $td = $adjun->fields['TD'];
    $descript = $adjun->fields['DESCRIPT'];
    $ruta = $adjun->fields['RUTA'];
    
    $ruta = str_replace("/", "\\", $ruta);
    $ini = "\\\\VORFEOBOD\\bodega\\$ruta";
    $info = pathinfo($ini);
    $des = str_replace("/", "\\", dirname(__FILE__)."/".$numExpediente."/").$info['basename'];
    if (!copy( $ini, $des) ) {
        $errors= error_get_last();
        echo "COPY ERROR: ".$errors['type'] . " ".$errors['message']."<br/>";
        //die("No pude copiar $ini a $des .");
        $color = "style='font-color:RED'";
    } else $color = "class='leidos'";
    $ruta = "<a href='./".$info['basename']."' target='_blank' ><span $color>$nombre</span></a>";    
    $icono = '<img name="imgAdjuntos"  src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEZSURBVCjPY/jPgB9iEVoqPefllFPdNk2GWBUsVpz9ctL1rkcNW/v+59VhKFgkPfP+xI0dF+uC/jPkWCR/Q1MwX2TGvf7Nretr/UG8BO2I5ygK5olP/dCzpWV+dVAhd+bB+JawrT7ICubIT3nbvaFpVkVqgVDa0diO4CneN91E4Qpmq0560jW/YXp5XB5nyq2YrqCFno9cJeG+mKk48UHHjLruMu8czuSbkfUBizxeucrDw2GGev/71uW1jMVrsq4nPIto8F/g8caFDymgetxbHlVLgDjxnWExPjPdb7sIoYRkk17FywJRECdY1Xux201nMbSgLufO25qyJUY1yNrzsus9JxkscZHMG+kVcN7jqWueowARkUWiAgBEUvolGfpITwAAAABJRU5ErkJggg==" border="0">';

    
    $rutaHistImg = '';
    list ($diaa, $mess, $anno) = explode('/', $tiempo);
    $fecha_operar = mktime(0, 0, 0, $mess, $diaa, $anno);
    
    $htmlRe = $htmlPrintIni1 . $htmlPrintIni2 . $icono . $htmlPrintFin2 . $htmlPrintIni2 . $ruta . $htmlPrintFin2 . $htmlPrintIni2 . $rutaHistImg . $htmlPrintFin2 . $htmlPrintIni2 . '<center>' . $tiempo . '</center>' . $htmlPrintFin2 . $htmlPrintIni2 . $td . $htmlPrintFin2 . $htmlPrintIni2 . $descript . $htmlPrintFin2 . $htmlPrintIni2 . '       ' . $htmlPrintFin2 . $htmlPrintFin1;
    
    $adjArr[] = array(
        'fecha' => $fecha_operar,
        'htmlRe' => $htmlRe
    );
    $contador ++;
    $adjun->MoveNext();
}

if ($expedienteSeleccionado) {
    include_once ($ruta_raiz . '/include/query/queryver_datosrad.php');
    $fecha = $db->conn->SQLDate("d-m-Y H:i A", "a.RADI_FECH_RADI");
    
    $isql = "SELECT T.SGD_TPR_DESCRIP,
				CONVERT(VARCHAR(10),A.RADI_FECH_RADI,103) AS FECHA_RAD,
				A.RADI_CUENTAI,
				A.RA_ASUN,
				A.RADI_PATH,
				$radi_nume_radi AS RADI_NUME_RADI,
				A.RADI_USUA_ACTU,
				A.RADI_DEPE_ACTU,
				E.SGD_EXP_CARPETA,
				0 AS PERMISO
		FROM	SGD_EXP_EXPEDIENTE E
				JOIN RADICADO A ON
					A.RADI_NUME_RADI = E.RADI_NUME_RADI
					JOIN SGD_TPR_TPDCUMENTO T ON
					T.SGD_TPR_CODIGO = A.TDOC_CODI
		WHERE	E.SGD_EXP_NUMERO = '$expedienteSeleccionado' AND
				E.SGD_EXP_ESTADO != 2
		ORDER BY A.RADI_FECH_RADI";
    
    $rs = $db->conn->Execute($isql);
    $i = 0;
    
    // ********************* INICIO DEL WHILE MOSTRAR ANEXOS, ADJUNTOS***************
    while (! $rs->EOF) {
        $radicado_d = "";
        $radicado_path = "";
        $radicado_fech = "";
        $radi_cuentai = "";
        $rad_asun = "";
        $tipo_documento_desc = "";
        
        $radicado_d = $rs->fields["RADI_NUME_RADI"];
        $radicado_path = $rs->fields["RADI_PATH"];
        $radicado_fech = $rs->fields["FECHA_RAD"];
        $radi_cuentai = $rs->fields["RADI_CUENTAI"];
        $rad_asun = $rs->fields["RA_ASUN"];
        $subexpediente = $rs->fields["SGD_EXP_CARPETA"];
        $nombreSubExp = $rs->fields["SGD_EXP_NOMBRESUBEXP"];
        $usu_cod = $rs->fields["RADI_USUA_ACTU"];
        $radi_depe = $rs->fields["RADI_DEPE_ACTU"];
        $permiso = $rs->fields["PERMISO"];
        $tipo_documento_desc = $rs->fields["SGD_TPR_DESCRIP"];
        
        list ($diaa, $mess, $anno) = explode('/', $radicado_fech);
        
        $fecha_operar = mktime(0, 0, 0, $mess, $diaa, $anno);
        
        if (! empty($adjArr)) {
            foreach ($adjArr as $k => $v) {
                if ($v['fecha'] < $fecha_operar) {
                    echo $adjArr[$k]['htmlRe'];
                    unset($adjArr[$k]);
                }
            }
        }
        
        $radicado_fech = "<span class=leidos> $radicado_fech </span>";
        if (strlen(trim($radicado_path)) > 0) {
            $radicado_path = str_replace("/", "\\", $radicado_path);
            $ini = "\\\\VORFEOBOD\\bodega\\$radicado_path"; 
            $info = pathinfo($ini);
            $des = str_replace("/", "\\", dirname(__FILE__)."/".$numExpediente."/").$info['basename'];
            if (!copy( $ini, $des) ) {
                $errors= error_get_last();
                echo "COPY ERROR: ".$errors['type'];
                echo "<br/>\n".$errors['message']."<br/>";
                //die("No pude copiar $ini a $des .");
                $color = "style='font-color:RED'";
            } else $color = "class='leidos'";
            $ref_radicado = "<a href='./".$info['basename']."' target='_blank' ><span $color>$radicado_d </span></a>";
        } else {
            $ref_radicado = "$radicado_d";
        }
        ?>

				<tr class='tpar'>
						<td valign="baseline" class='listado1'>

<?php
        if (! isset($_POST['verBorrados'])) {
            if (($_POST['anexosRadicado'] != $radicado_d)) {
                ?>
					<img name="imgVerAnexos_<?php print $radicado_d; ?>" src="data:image/gif;base64,R0lGODlhDwASANUAAAU9OsLP2LnOx623ue79+jZgcODm5ERzbXeSjerx98LNydTU1r7Mzf/7+LTJwuTm5QE6Ts3W1f7690pzdY+aoMzMzNbh3Qk+Tvfy9lBqd8TIx/f//9Tr5crd28TOzb7NyBM6QcDS1tvh3/Dw8P///7vHxQk6SU5uaXmVlvD59gtKU9/t7b3WzsXUz/b/+Pn59/3z/NLj3UdyeAg6QsXO3tXZ3BBCQsXV1Ac9Vf///wAAAAAAAAAAAAAAAAAAAAAAACH5BAUUADkALAAAAAAPABIAAAaPQAZDQSQKj8ikcllsOp+RSLGE1AhChgZpu4poPmDwLUF6bc2YRSn8GW0lA4qLBBt5BMTKHEaa2DYbZRF4CjESGygHKhAyCIAiHpEcDQ0HABc4MycEJCJFNQ0bHR0FJgEdgBYtRS+AGxkggBIkHg5FFmazW1sPCiVEeA9mZVsGHmtsHxEGKSMiC0/R0tPU00EAOw==" border="0">
<?php
            } else if (($_POST['anexosRadicado'] == $radicado_d)) {
                ?>
					<img name="imgVerAnexos_<?php print $radicado_d; ?>" src=" data:image/gif;base64,R0lGODlhDwASANUAAAU9OsLP2LnOx623ue79+jZgcODm5ERzbXeSjerx98LNydTU1r7Mzf/7+LTJwuTm5QE6Ts3W1f7690pzdY+aoMzMzNbh3Qk+Tvfy9lBqd8TIx/f//9Tr5crd28TOzb7NyBM6QcDS1tvh3/Dw8P///7vHxQk6SU5uaXmVlvD59gtKU9/t7b3WzsXUz/b/+Pn59/3z/NLj3UdyeAg6QsXO3tXZ3BBCQsXV1Ac9Vf///wAAAAAAAAAAAAAAAAAAAAAAACH5BAEAADkALAAAAAAPABIAAAaHQAZDQSQKj8ikcllsOp+RSLGE1AhChgZpu4poPmDwLUF6bc2YRSn8GW0lrjgJNvIIiBXXnLTpb8oRdwoxEhsoByoQMgh9Ih6PHA0NBwAXODMnBCQiRTUNhogmi30WLUUvfqkSJB4ORRZmq1tbDwolRHcPZmVbBh5rbB8RBikjIgtPycrLzMtBADs=" border="0">
<?php
            }
        }
        if (isset($_POST['verBorrados'])) {
            if (($_POST['verBorrados'] == $radicado_d)) {
                ?>
					<img name="imgVerAnexos_<?php print $radicado_d; ?>" src=" data:image/gif;base64,R0lGODlhDwASANUAAAU9OsLP2LnOx623ue79+jZgcODm5ERzbXeSjerx98LNydTU1r7Mzf/7+LTJwuTm5QE6Ts3W1f7690pzdY+aoMzMzNbh3Qk+Tvfy9lBqd8TIx/f//9Tr5crd28TOzb7NyBM6QcDS1tvh3/Dw8P///7vHxQk6SU5uaXmVlvD59gtKU9/t7b3WzsXUz/b/+Pn59/3z/NLj3UdyeAg6QsXO3tXZ3BBCQsXV1Ac9Vf///wAAAAAAAAAAAAAAAAAAAAAAACH5BAEAADkALAAAAAAPABIAAAaHQAZDQSQKj8ikcllsOp+RSLGE1AhChgZpu4poPmDwLUF6bc2YRSn8GW0lrjgJNvIIiBXXnLTpb8oRdwoxEhsoByoQMgh9Ih6PHA0NBwAXODMnBCQiRTUNhogmi30WLUUvfqkSJB4ORRZmq1tbDwolRHcPZmVbBh5rbB8RBikjIgtPycrLzMtBADs=" border="0">
<?php
            } else if (($_POST['verBorrados'] != $radicado_d)) {
                ?>
					<img name="imgVerAnexos_<?php print $radicado_d; ?>" src="data:image/gif;base64,R0lGODlhDwASANUAAAU9OsLP2LnOx623ue79+jZgcODm5ERzbXeSjerx98LNydTU1r7Mzf/7+LTJwuTm5QE6Ts3W1f7690pzdY+aoMzMzNbh3Qk+Tvfy9lBqd8TIx/f//9Tr5crd28TOzb7NyBM6QcDS1tvh3/Dw8P///7vHxQk6SU5uaXmVlvD59gtKU9/t7b3WzsXUz/b/+Pn59/3z/NLj3UdyeAg6QsXO3tXZ3BBCQsXV1Ac9Vf///wAAAAAAAAAAAAAAAAAAAAAAACH5BAUUADkALAAAAAAPABIAAAaPQAZDQSQKj8ikcllsOp+RSLGE1AhChgZpu4poPmDwLUF6bc2YRSn8GW0lA4qLBBt5BMTKHEaa2DYbZRF4CjESGygHKhAyCIAiHpEcDQ0HABc4MycEJCJFNQ0bHR0FJgEdgBYtRS+AGxkggBIkHg5FFmazW1sPCiVEeA9mZVsGHmtsHxEGKSMiC0/R0tPU00EAOw==" border="0">
<?php
            }
        }
        ?>
								
							</td>
						<td valign="baseline" class='listado1'><span class="leidos"><?=$ref_radicado ?></span></td>
						<td valign="baseline" class='listado1'>&nbsp;</td>
						<td valign="baseline" class='listado1'><p style="text-align: center;"><?=$radicado_fech ?></p></td>
						<td valign="baseline" class='listado1'><span class="leidos2"><?=$tipo_documento_desc ?></span></td>
						<td valign="baseline" class='listado1'><span class="leidos2"><?=$rad_asun ?></span></td>
						<td valign="baseline" class='listado1'><span class="leidos2"><?=$subexpediente ?></span></td>
					</tr>	
<?php
        /*
         * Carga los anexos del radicado indicado en la variable $radicado_d
         * incluye la clase anexo.php
         */
        
        include_once "$ruta_raiz/class_control/anexo.php";
        include_once "$ruta_raiz/class_control/TipoDocumento.php";
        $a = new Anexo($db->conn);
        $tp_doc = new TipoDocumento($db->conn);
        
        /*
         * Modificacion: 15-Julio-2006 Mostrar los anexos del radicado seleccionado.
         * Modificado: 23-Agosto-2006 Supersolidaria
         * Muestra todos los anexos de un radicado al ingresar a la pestaï¿½ de EXPEDIENTES.
         */
        $num_anexos = $a->anexosRadicado($radicado_d);
        $anexos_radicado = $a->anexos;
        /*
         * Modificado: 23-Agosto-2006 Supersolidaria
         * Muestra los anexos borrados de un radicado al ingresar a la pestaï¿½ de EXPEDIENTES.
         */
        
        if (isset($_POST['verBorrados'])) {
            $num_anexos = $a->anexosRadicado($radicado_d, true);
        }
        
        if ($num_anexos >= 1) {
            for ($i = 0; $i <= $num_anexos; $i ++) {
                $anex = $a;
                $codigo_anexo = $a->codi_anexos[$i];
                if ($codigo_anexo and substr($anexDirTipo, 0, 1) != '7') {
                    $tipo_documento_desc = "";
                    $fechaDocumento = "";
                    $anex_desc = "";
                    // $anex = new Anexo;
                    $anex->anexoRadicado($radicado_d, $codigo_anexo);
                    // $anex=$a;
                    $secuenciaDocto = $anex->get_doc_secuencia_formato($dependencia);
                    $fechaDocumento = $anex->get_sgd_fech_doc();
                    $anex_nomb_archivo = $anex->get_anex_nomb_archivo();
                    $anex_desc = $anex->get_anex_desc();
                    $dependencia_creadora = substr($codigo_anexo, 4, 3);
                    $ano_creado = substr($codigo_anexo, 0, 4);
                    $sgd_tpr_codigo = $anex->get_sgd_tpr_codigo();
                    // Trae la descripcion del tipo de Documento del anexo
                    if ($sgd_tpr_codigo) {
                        // $tp_doc = new TipoDocumento($db->conn);
                        $tp_doc->TipoDocumento_codigo($sgd_tpr_codigo);
                        $tipo_documento_desc = $tp_doc->get_sgd_tpr_descrip();
                    }
                    $anexBorrado = $anex->anex_borrado;
                    $anexSalida = $anex->get_radi_anex_salida();
                    $ext = substr($anex_nomb_archivo, - 3);
                    if (trim($anex_nomb_archivo) and $anexSalida != 1) {
                        // if($ext!="doc") {
                        ?>						
							
						<tr class='timpar'>
						<td class='listado5'></td>
						<td valign="baseline" class='listado5'>
								
<?php
                        if ($anexBorrado == "S") {
                            ?>
								
								<img src="data:image/gif;base64,R0lGODlhGAAVAPcAAP/////6+vr6+/n5+f/39//29v/09Pf29P/z8/Dx8f/r6+/u8f/q6v/o6Ojp6+Xo6Onn6f/g4Obl6P/c3O7f4P/a2v/X1+TY39na4drb3dja3fLP0f/MzNTU2dLT3tLT0M/Q2d3L0+DL0c3O2fzAwfK/wMbJ3P+5udnBxsXE2cPEyMzAx8LCycDA3MHCxP+vr/+urv+trbK827u8wru7vv+oqPinp9qus/+jo/+goPOkprWyyLGzwv+fn7OzvLayufmhorKyu/6enqex1f+cnKivu/uamvmamveYmKurxe+an6Gqzv+RkfeRkv6Pj+uTlf+MjP2Li6OjrX6m9/+Li/+Kiv+Hh/+GhveIif+BgeaHjueGh42au/9+fpWXpv94eP93d6GNl/9ycoaPuf9xcf9wcP9tbf9qav9paf9oaHaIw/poaf9nZ4SFjP9lZf9jY/9kZP9iYv9hYXKDrP9gYP9fX/9eXv9dXf9cXP9aWv9bW/9XV1ptpWhqbl5gZlZaXUFRjF9KTkw1OC8wM////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAUUAIQALAAAAAAYABUAAAj/AAkJJDRooMGDCBMqXMiwoUOGO6R44YOw4EAGDQgcTMFlzpgTMCwshFOHioGBJqRMUaNFjpwqC9+8yUOEhU0ADj4oqcMzxsIcaejoEdLBBw0IRtjYscOkgEGLA3vAgbMHx4wfR6bC6YGgYYAtbNjseYJkKh0nEh62geKmrRueUVZgqIjQCwEsWuE0oQAAQBEZDZMcAFJn6hkdA/pmGNKwhQ00b/TogYOGRIYEAJYwBPCCzZs7VbLckSOGgwAAXp4efBGUjpULIta4JHMDQJCFNdDwtBJihIcNYPDE+TJBQ8zPVxQsOA0gghk9dbp0VUjSSpi+2AFUKBMHDwrVBAkxFRBUAFD2vg9KuAwEHmGfP354gEAYEAA7">
								
<?php
                        } else if ($anexBorrado == "N") {
                            ?>								
								
									<img src="data:image/gif;base64,R0lGODlhGAAVAOcZAM3S5+rr6z1ZtNna4cbG47Kyu9HR72KG6bCwypumw66uyHN2hLW1zbbE6L2+1tXT0ZWXpuPh9ZWex5OVmF9wucvMyYWVvdLS8cTL4////6OjrdXR4x4ub9rb3bKyzKSs02J0ucHCxM/Q2aqqxs3N1uHe8VJyy6ysxk1rwnaV6ZGdvLi40LGzwsrJ6L2+26ystKWtxcTC1PDx8aex1WhqbsnJ0V+C4vn5+WN0ts3O2WqFzauyxsC/x1p83Iyaw8zM6ra20XB+uqGqzi8wM8nK1MXE2Y2au5ukytLP44eJlubk+9Ta7X2JwdXV8naBvKqurbK82+Lj5srI18LCycbGzff288nJ5d3d9q6xuGx5ulZsv2eG22l7qVl2y22BtHKDrH2BiYSQxcnG28/P7mR6vtjb7N/i8k5nvLm1yERiwkFcs8rK3tfZ3Epqyb2+x7q60rq9ycXEyMDA2dXW2Le2yk1ktNra9aurxdPT6tLU2ubn6c7K38bD29LS2Udku1R00MzNz7CwyPz8/GF9ybWyyJ2nv5ahu9LT3ru71VZaXYOKvtzZ7URFSs/P8crI276/yL2+w1h617Gxy19us2h3ua2tyK+vyc3N687O7N/g47m50enn6by706e44+/u8aivu9fX9F5lk8TDyniEsrm7zGuH2tre7sbJ3Ojp6/79/f//+Yaf4LOzzYGX0bOzz9PO38bP6MvM09LSzaKkyJKk1G2R8auryKivx62tzff49/78/5OgwqalxpCgyoWJjLa3vlt6zUVlyEpkwX6m98jM2I2Rv19+xEFRjOTn56+tycTE4MPDzMPEyMTEzYeTqvr6+8jJz5as4rKwysLC3drZ8d7f+dnd8LGvybG3yru7vvf4+Vd723mQy6KqwPjx/lptpYWZy7zC3MfH4s7L4M/M4ba5ydPV33eLunSS33yKvLu9zbizwr3Av9jX3rGwzrq6wbu8wl5gZru8xpmguWWD0tbU6mqG1m6K2FZot+Pi4L+/z9zf8Nrd68XDzP///////yH+KUNyZWF0ZWQgd2l0aCBUaGUgR0lNUApQb3Igamhsb3NhZGEgLSBTU1BEACH5BAEAAP8ALAAAAAAYABUAQAjlAP8J/DdkoMGDCA8WPDghg4Y48F5QgQYoQ4aECcFkyJHj0IBmeixmYGNwIUaBRYx88SLgpME7VVAVeJQnSrsbFjvMKImRRqJ4LES4HEoUYwhPGpiFyICqQqgpVYpq7ENiCiQ9sWq4GVD035MMj0iQGDA2w7MMEIguEMlWZJ6ucIsS0gDhW1yBpzQMM8ZhQdEnuSbwKJBND6AKOKVmyDQF2pRHbqAty4TQ5EAIGaLUmNOPRAFUFj9BGepiTluzHWRkEMITYUMih0QMyBPrBY8MBdRm8HT29MWBC4P/O3Ya2QsaQxgFBAA7">
									
<?php
                        }
                        if ($permiso == 0) {
                            $path = str_replace("/", "\\", $ano_creado."/$dependencia_creadora/docs/$anex_nomb_archivo");
                            $ini = "\\\\VORFEOBOD\\bodega\\$path";
                            $info = pathinfo($ini);
                            $des = str_replace("/", "\\", dirname(__FILE__)."/".$numExpediente."/").$info['basename'];
                            if (!copy( $ini, $des) ) {
                                $errors= error_get_last();
                                echo "COPY ERROR: ".$errors['type'];
                                echo "<br/>\n".$errors['message']."<br/>";
                                //die("No pude copiar $ini a $des .");
                                $color = "style='font-color:RED'";
                            } else $color = "class='leidos'";
                            ?>
                            	<a href='./<?=$info['basename']?>' target='_blank' ><span <?php echo $color?>><?=substr($codigo_anexo,-4)?> </span></a>
									
<?php
                        } else {
                            ?>									
									
									<?=substr($codigo_anexo,-4) ?>
									
<?php
                        }
                        ?>
									
							</td>
						<td valign="baseline" class='listado5'>&nbsp;</td>
						<td valign="baseline" class='listado5'><?=$fechaDocumento ?></td>
						<td valign="baseline" class='listado5'><?=$tipo_documento_desc ?></td>
						<td valign="baseline" class='listado5'><?=$anex_desc ?></td>
						<td valign="baseline" class='listado5'></td>
						<td valign="baseline" class='listado5'></td>
					</tr>
						
<?php
                    } // Fin del if que busca si hay link de archivo para mostrar o no el doc anexo
                }
            } // Fin del For que recorre la matriz de los anexos de cada radicado perteneciente al expediente
        }
        $rs->MoveNext();
    }
    if (! empty($adjArr)) {
        foreach ($adjArr as $k => $v) {
            echo $v['htmlRe'];
        }
    }
    // ********************* FIN DEL WHILE MOSTRAR ANEXOS, ADJUNTOS***************
}
// Fin del While que Recorre los documentos de un expediente.
?>						
						
					</table>
			</td>
		</tr>
	</table>
	</body>
</html>
<?php // CREATE index.html
/* PERFORM COMLEX QUERY, ECHO RESULTS, ETC.
$page = ob_get_contents();
ob_end_clean();
$cwd = getcwd();
$file = "$cwd" ."/$numExpediente/". "index.html";
@chmod($file,0755);
$fw = fopen($file, "w");
fputs($fw,$page, strlen($page));
fclose($fw);
die(); */
?>