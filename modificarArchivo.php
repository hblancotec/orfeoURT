<?php
session_start();

extract($_GET, EXTR_SKIP);
extract($_POST, EXTR_OVERWRITE);
extract($_SESSION, EXTR_OVERWRITE);

    $krd        = ($_POST['krd']) ? $_POST['krd'] : null;
    $ent        = ($_GET['ent']) ? $_GET['ent'] : null;
    $ruta_raiz  = ($_POST['ruta_raiz']) ? $_POST['ruta_raiz'] : null;
    $krdOld     = $krd;
    $radicado_rem = ($_POST['radicado_rem']) ? $_POST['radicado_rem'] : null;
    if(!$krd) $krd = $krdOld;
    if (!$ruta_raiz) $ruta_raiz= ".";
   
    if (!isset($_SESSION['dependencia']))	include ("./rec_session.php");
    if (!$usua)	$usua = $krd;
    include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
    if (!$ent)	$ent = substr(trim($numrad),strlen($numrad)-1,1);
    $nombreTp3 = $tip3Nombre[3][$ent];
    if (!$db) $db = new ConnectionHandler($ruta_raiz);
    $dbMun = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $conexion =& $dbMun;

    //Subir Anexos por Julian Rolon
    //Da detalles de archivo y permite cargar y modificar
    $rowar = array();
    $mensaje = "";
    $tipoDocumento = explode("-", $tipoLista);
    $tipoDocumentoSeleccionado = $tipoDocumento[1];

    $isql = "SELECT USUA_LOGIN,
                    USUA_PASW,
                    CODI_NIVEL
                FROM usuario
                WHERE (usua_login ='$usua')";
    $rs = $db->conn->Execute($isql);

    if ($rs->EOF) {
        $mensaje = "No tiene permisos para ver el documento";
    } else {
        $nivel = $rs->fields["CODI_NIVEL"];
        $psql = ($tipo==0) ? " where  anex_tipo_codi<20 " : " ";
        $psql = '';
        $isql = "SELECT ANEX_TIPO_CODI,
                        ANEX_TIPO_DESC,
                        ANEX_TIPO_EXT
                FROM anexos_tipo $psql
                ORDER BY anex_tipo_desc desc";
        $rs = $db->conn->Execute($isql);
    }

    if ($resp1=="OK") {
        $mensaje = ($subir_archivo) ? "<span class='info'>Archivo anexado correctamente</span></br>\n\t" :
                        "Anexo Modificado Correctamente<br>No se anex&oacute; ning&uacute;n archivo</br>";
    } else if ($resp1=="ERROR") {
        $mensaje="<span class='alarmas'>Error al anexar archivos</span></br>\n\t";
    }
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Informaci&oacute;n de Anexos</title>
        <link rel="stylesheet" href="estilos/orfeo.css">
        <script language="javaScript" src="js/crea_combos_2.js"></script>
        <script language="javascript">
            function mostrar(nombreCapa) {
                document.getElementById(nombreCapa).style.display = "";
            }

            function continuar_grabar() {
                document.formulario.tpradic.disabled=false;
                document.formulario.action=document.formulario.action+"&cc=GrabarDestinatario";
                document.formulario.submit();
            }

            function mostrarNombre(nombreCapa) {
                document.formulario.elements[nombreCapa].style.display="";
            }

            function ocultarNombre(nombreCapa) {
                document.formulario.elements[nombreCapa].style.display="none";
            }

            function ocultar(nombreCapa) {
                document.getElementById(nombreCapa).style.display="none";
            }

            function procEst(dato1,dato2,valor) {
            }

            function Start(URL, WIDTH, HEIGHT) {
                windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=1020,height=500";
                preview = window.open(URL , "preview", windowprops);
            }

            function doc_radicado() {
                mostrarForm();
                swSelRadic = 0;
                for (n=1;n<document.formulario.tpradic.length;n++ )
                    if (document.formulario.tpradic.options[n].selected ) {
                        swSelRadic=1;
                    }

                if (!document.formulario.radicado_salida.checked){
                    document.formulario.tpradic.disabled=false;

                    eval(document.formulario.elements['tpradic'].options[0]=new Option('- Tipos de Radicacion -','null' ));
                    document.formulario.elements['tpradic'].options[0].selected=true;
                    document.formulario.elements['tpradic'].disabled=true;
                    comboRadiAplintegra(document.formulario,'null','aplinteg');
                } else {
                    document.formulario.tpradic.disabled  = false;
                if (swSelRadic==0) {
                    for (n=0;n<document.formulario.tpradic.length;n++ ) {
                        if (document.formulario.tpradic.options[n].value == 1 ){
                            document.formulario.tpradic.options[n].selected = true;
                        }
                    }
                }
                if (swSelRadic==0) {
                    for (n=0;n<document.formulario.tpradic.length;n++ ) {
                        if (document.formulario.tpradic.options[n].value == 1 ){
                            document.formulario.tpradic.options[n].selected=true;
                        }
                    }
                }
<?php
	if (($verunico==1) && ($ent!=2)) {
?>
			eval(document.formulario.elements['tpradic'].options[0] = new Option('Salida','1' ));
			document.formulario.elements['tpradic'].options[0].selected = true;
			document.formulario.elements['tpradic'].disabled = true;
<?php
        }
?>
                }
            }

            function f_close(){
                opener.regresar();
                window.close();
            }

            function regresar(){
                f_close();
            }

            function william() {
                if(document.getElementById('radicado_salida').checked) {
                    document.getElementById('tipo').disabled = false;
                } else {
                    document.getElementById('tipo').disabled = true;
                }
            }

            function escogio_archivo() {
                var largo;
                var valor;
                archivo_up = document.getElementById('userfile').value;
                valor=0;
                largo = archivo_up.length - 3;
                extension = archivo_up.substr(archivo_up.length-3,3);
<?php
	while (!$rs->EOF) {
        echo "\t\tif (extension=='".$rs->fields["ANEX_TIPO_EXT"]."') {
                    valor = ".$rs->fields["ANEX_TIPO_CODI"].";
                }\n";
		$rs->MoveNext();
	}
    $anexos_isql = $isql;
?>
//                document.getElementById('tipo_clase').value = valor;
//                if(document.getElementById('radicado_salida').checked==true &&
//                    valor!=1 && valor!=14 && valor!=16) {
//                    alert("Atenci\363n. Si el archivo no es DOC, ODT o XML no podr\341 realizar combinaci\363n de correspondencia. \n\n otros archivos no facilitan su acceso");
//                }
            }

            function validarGenerico() {
                if (document.formulario.radicado_salida.checked && document.formulario.tpradic.value=='null') {
                    alert ("Debe seleccionar el tipo de radicacion");
                    return false;
                }

                archivo=document.getElementById('userfile').value;
                if (archivo=="") {
<?php
    $tipo = (!empty($_POST['tipo'])) ? $_POST['tipo'] : null;
    if (empty($codigo)) {
        $codigo = ($_POST['codigo']) ? $_POST['codigo'] : ($_GET['codigo']) ? $_GET['codigo'] : null;
    }
    if($tipo == 0 && !$codigo){
        echo "alert('Por favor escoja un archivo'); return false;";
    } else {
        echo "return true;";
    }
?>
                }

                copias = document.getElementById('i_copias').value;

                if  (copias==0 && document.getElementById('radicado_salida').checked==true &&
                        document.getElementById('rotro').checked==true  ) {
                    document.getElementById('radicado_salida').checked=false;
                }
                return true;
            }

            function actualizar(){
                if (!validarGenerico())
                    return;
              		document.formulario.radicado_salida.disabled=false;
              		document.formulario.tpradic.disabled=false;
                	document.formulario.submit();

            }

            function mostrarForm() {
                 var tipifica = document.formulario.radicado_salida.checked;
                 if(tipifica)
                    document.getElementById( "anexaExp" ).style.display = 'block';
                else
                    document.getElementById( "anexaExp" ).style.display = 'none';
            }

            function trim(cadena) {
            	for(i=0; i<cadena.length; ) {
            		if(cadena.charAt(i)==" ")
            			cadena=cadena.substring(i+1, cadena.length);
            		else
            			break;
            	}

            	for(i=cadena.length-1; i>=0; i=cadena.length-1) {
            		if(cadena.charAt(i)==" ")
            			cadena=cadena.substring(0,i);
            		else
            			break;
            	}
            	return cadena;
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF">
<?php
	$esAnonimo = $db->conn->GetOne("SELECT RADI_ANONIMO FROM RADICADO WHERE RADI_NUME_RADI=$numrad");
    $i_copias = 0;
    if ($codigo) {
        $isql = "select RAD.CODI_NIVEL,
                        ANE.ANEX_SOLO_LECT,
                        ANE.ANEX_CREADOR,
                        ANE.ANEX_DESC,
                        ANET.ANEX_TIPO_EXT,
                        ANE.ANEX_NUMERO,
                        ANE.ANEX_NOMB_ARCHIVO nombre,
                        ANE.ANEX_SALIDA,
                        ANE.ANEX_ESTADO,
                        ANE.SGD_DIR_TIPO,
                        ANE.RADI_NUME_SALIDA,
                        ANE.SGD_DIR_DIRECCION
                    from anexos ANE,
                        anexos_tipo ANET,
                        radicado RAD
                    where ANE.anex_codigo = '$codigo' and
                            ANE.anex_radi_nume = RAD.radi_nume_radi and
                            ANE.anex_tipo = ANET.anex_tipo_codi";
        $rs = $db->conn->Execute($isql);
        if (!$rs->EOF){
            $docunivel  = $rs->fields["CODI_NIVEL"];
            $sololect   = $rs->fields["ANEX_SOLO_LECT"];
            $remitente  = $rs->fields["SGD_DIR_TIPO"];
            $extension  = $rs->fields["ANEX_TIPO_EXT"];
            $radicado_salida = $rs->fields["ANEX_SALIDA"];
            $anex_estado = $rs->fields["ANEX_ESTADO"];
            $descr      = $rs->fields["ANEX_DESC"];
            $radsalida  = $rs->fields["RADI_NUME_SALIDA"];
            $direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
        }
    }
?>
<script type="text/javascript">var extAnex = '<?php if (isset($_GET["extAnex"])) echo $_GET["extAnex"]; else echo $extension; ?>'; </script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
<?php
	$datos_envio  = "&extAnex=$extension";
	$datos_envio .= "&otro_us11=$otro_us11&codigo=$codigo";
    $datos_envio .= "&dpto_nombre_us11=$dpto_nombre_us11";
    $datos_envio .= "&direccion_us11=".urlencode($direccion_us11);
    $datos_envio .= "&codpostal_us11=".urlencode($codpostal_us11);
    $datos_envio .= "&muni_nombre_us11=$muni_nombre_us11&nombret_us11=$nombret_us11";
	$datos_envio .= "&otro_us2=$otro_us2&dpto_nombre_us2=$dpto_nombre_us2";
    $datos_envio .= "&muni_nombre_us2=$muni_nombre_us2&direccion_us2=".urlencode($direccion_us2);
    $datos_envio .= "&nombret_us2=$nombret_us2";
	$datos_envio .= "&dpto_nombre_us3=$dpto_nombre_us3";
    $datos_envio .= "&muni_nombre_us3=$muni_nombre_us3";
    $datos_envio .= "&direccion_us3=".urlencode($direccion_us3);
    $datos_envio .= "&nombret_us3=$nombret_us3";
	$datos_envio .= "&dpto_nombre_us7=$dpto_nombre_us7";
    $datos_envio .= "&muni_nombre_us7=$muni_nombre_us7";
    $datos_envio .= "&direccion_us7=".urlencode($direccion_us7);
    $datos_envio .= "&nombret_us7=$nombret_us7";
    $variables    = "ent=$ent&radi=$radi&krd=$krd&";
    $variables   .= session_name()."=".trim(session_id());
    $variables   .= "&usua=$krd&contra=$drde&tipo=$tipo";
    $variables   .= "&ent=$ent&codigo=$codigo"."$datos_envio&numrad=$numrad";
?>
<form enctype="multipart/form-data" method="post" name="formulario" id="formulario" action="upload2.php?<?=$variables?>">
<input type="hidden" name="usua" value="<?=$usua?>">
<input type="hidden" name="contra" value="<?=$contra?>">
<input type="hidden" name="anex_origen" value="<?=$tipo?>">
<input type="hidden" name="tipo" value="<?=$tipo?>">
<input type="hidden" name="tipoLista" value="<?=$tipoLista?>">
<input type="hidden" name="krd" value="<?=$krd?>">
<input type="hidden" name="tipoDocumentoSeleccionado" value="<?php echo $tipoDocumentoSeleccionado ?>">
<div align="center">
	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
		<tr>
			<td height="25" align="center" class="titulos4" colspan="2">DESCRIPCION DEL DOCUMENTO</td>
		</tr>
		<tr>
			<td class="titulos2" height="25" align="left" colspan="2">ATRIBUTOS</td>
		</tr>
		<tr>
            <td colspan="2">
                <table border="0" width="100%" class="borde_tab">
                <!--DWLayoutTable-->
                <tr>
                    <td height="23" align="left" colspan="3" class="listado2">
                        Tipo de Anexo:
                        <select name="tipo" class="select" id="tipo_clase">
<?php
		$rs = $db->conn->Execute($anexos_isql);
        $viewTipoAnex = '';
		while ( !$rs->EOF) {
            if (($extension == 'odt' && $rs->fields[0]==14) ||
                    ($extension == 'doc' && $rs->fields[0]==1) ||
                    ($extension == 'xml' && $rs->fields[0]==16)) {
                    $datoss = "selected";
                } else {
                    $datoss = "";
                }
                $viewTipoAnex .= '<option value="' . $rs->fields['ANEX_TIPO_CODI'] . '" ' . $datoss . '>';
                $viewTipoAnex .= $rs->fields['ANEX_TIPO_DESC'] . '</option>' . "\n\t";
                $rs->MoveNext();
		}
        echo $viewTipoAnex;
        echo "</select>\n";
        $sololect = ($_POST['sololect']) ? $_POST['sololect'] : null;
        $onlyRead = (!empty($sololect) && $sololect != 'N') ? $sololect : null;
?>
        </td>
	</tr>
	<tr>
		<td height="23" colspan="3" class="listado2">
			<input type="checkbox" class="select" name="sololect" <?php  if(!empty($sololect)){echo "checked";}  ?> id="sololect">Solo lectura
        </td>
	</tr>
	<tr>
		<td height="23" colspan="3" class="listado2">
		<table border="1" width="100%">
		<td width="50%" class="listado2">
<?php
    $us_1   = "";
    $us_2   = "";
    $us_3   = "";
    $us_7   = "";
	$datoss = "";

	if ($esAnonimo) {
	    $us_1 = "si";$usuar=1;if($remiten==1)$datoss1=" checked " ;
	} else {
		if($nombret_us11 and $direccion_us11 and $dpto_nombre_us11 and $muni_nombre_us11) {
			$us_1 = "si";
			$usuar=1;
			if($remitente==1) {
				$datoss1=" checked " ;
			}
		} else {
			$datoss1=" disabled " ;
		}

		$datoss = "";
		if($nombret_us2 and $direccion_us2 and $dpto_nombre_us2 and $muni_nombre_us2) {
			$us_2 = "si";
			$predi= 1;
			if($remitente==2){
				$datoss2=" checked  ";
			}
		} else {
			$datoss2=" disabled ";
		}
		$datoss = "";
		if($nombret_us3 and $direccion_us3 and $dpto_nombre_us3 and $muni_nombre_us3) {
			$us_3 = "si";
			$empre=1;
			if($remitente==3) {
				$datoss3=" checked  ";
			}
		} else {
			$datoss3=" disabled ";
		}
		 $datoss = "";
		if($nombret_us7 and $direccion_us7 and $dpto_nombre_us7 and $muni_nombre_us7) {
			$us_7 = "si";
			$dpto_nombre_us11=$dpto_nombre_us7;
			$direccion_us11=$direccion_us7;
			$muni_nombre_us11=$muni_nombre_us7;
			$nombret_us11=$nombret_us7;
			$datoss1=" checked  ";
		}
	}

    if( $us_1 or $us_2 or $us_3 or $us_7 or $esAnonimo ) {
        $us_7 = "si";
        $dpto_nombre_us11=$dpto_nombre_us7;
        $direccion_us11=$direccion_us7;
        $muni_nombre_us11=$muni_nombre_us7;
        $nombret_us11=$nombret_us7;
        $datoss1=" checked  ";
    }

    if( $us_1 or $us_2 or $us_3 or $us_7 ) {
        if ($radicado_salida) $datoss = " checked ";
        else $datoss = " ";

        $swDischekRad="";
        if (strlen(trim($radsalida))>0)
            $swDischekRad = ' disabled="true" ';
        $datoss = $datoss . $swDischekRad;
        echo "\t\t\t";
        $mostrarInput  = '<input type="checkbox" class="select" name="radicado_salida" ';
        $mostrarInput .= $datoss;
        $mostrarInput .= 'value="radsalida"';

        if(!$radicado_salida and $ent==1)
            $radicado_salida=1;
        if($radicado_salida==1) {
            if (empty($datoss)) {
                $mostrarInput .= " checked ";
            }
        }

        $mostrarInput .= ' onClick="doc_radicado()" id="radicado_salida">';
        echo $mostrarInput;
        echo 'Este documento ser&aacute; radicado' . "\n";
	} else {
?>
		Este documento no puede ser radicado ya que faltan datos.<br>
		(Para envio son obligatorios Nombre, Direccion, Departamento,
		Municipio)
<?php
	}
?>
		</td>
		<td class="listado2">
<?php
		$comboRadOps    = "";
	    if ($ent!=1 ) {
	    	$deshab=" disabled=true ";
	    }
	    if (strlen(trim($swDischekRad)) > 0)
	    	$deshab = $swDischekRad;
		$comboRad="<select name='tpradic' class='select' $deshab $eventoIntegra>";
		$comboRadSelecc = "<option selected value='null'>- Tipos de Radicaci&oacute;n -</option>";
        $sel="";
		foreach ($tpNumRad as $key => $valueTp) {
            if(strcmp(trim($tpradic),trim($valueTp))==0) {
		            	$sel="selected";
						$comboIntSwSel=1;
			}
			//Si se definicion prioridad en algun tipo de radicacion
			$valueDesc = $tpDescRad[$key];

			if($tpPerRad[$valueTp]==2 or $tpPerRad[$valueTp]==3) {
				$comboRadOps =$comboRadOps . "<option value='".$valueTp."' $sel>".$valueDesc."</option>\n";
			}
			$sel="";
		}
		$comboRad = $comboRad.$comboRadSelecc.$comboRadOps."</select>";
?>
		Radicaci&oacute;n  <?=$comboRad?> <br>
<?php
	    if (strlen(trim($swDischekRad)) > 0){
	    	echo ("<script>");
	    	//echo ("document.formulario.radicado_salida.checked=true;");
	    	echo ("document.formulario.tpradic.disabled=true;");
	    	echo ("</script>");
	    }
?>
		</td>
	</table>
	</td>
	</tr>
	<tr>
    <td>
		<table width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab" id="anexaExp" style="display:none">
			<tr>
				<td class="titulos2"  width="50%">Guardar en Expediente:</td>
				<td valign="top" class="listado2">
                    <table class="borde_tab" align="center">
                        <tr class="titulos2">
<?php
    $q_exp  = "SELECT  SGD_EXP_NUMERO as valor,
                        SGD_EXP_NUMERO as etiqueta,
                        SGD_EXP_FECH as fecha";
    $q_exp .= " FROM SGD_EXP_EXPEDIENTE ";
    $q_exp .= " WHERE RADI_NUME_RADI = " . $numrad;
    $q_exp .= " AND SGD_EXP_ESTADO <> 2";
    $q_exp .= " ORDER BY fecha desc";
    $rs_exp = $db->conn->Execute($q_exp);

    if($rs_exp->RecordCount() == 0) {
        $mostrarAlerta = '<td align="center" class="titulos2">' . "\n\t\t";
        $mostrarAlerta .= '<span class="leidos2" class="titulos2" align="center">' . "\n\t\t";
        $mostrarAlerta .= '<b>EL RADICADO PADRE NO ESTA INCLUIDO EN UN EXPEDIENTE.</b>' . "\n\t\t";
        $mostrarAlerta .= '</span></td>' . "\n\t\t";
        $sqlt = "SELECT RADI_USUA_ACTU,
                        RADI_DEPE_ACTU
                    FROM RADICADO
                    WHERE RADI_NUME_RADI = $numrad";
        $rsE  = $db->conn->Execute($sqlt);
        $depe = $rsE->fields['RADI_DEPE_ACTU'];
        $usua = $rsE->fields['RADI_USUA_ACTU'];
        echo $mostrarAlerta;
    } else {
        echo '<td align="center" width="50%">' . "\n\t\t";
        echo '<span class="leidos2" align="center">' . "\n\t\t";
        print $rs_exp->GetMenu( 'expIncluidoAnexo',
                                        $expIncluidoAnexo,
                                        false,
                                        false,
                                        0);
        echo '</span>' . "\n\t\t";
        echo '</td>' . "\n\t\t";
    }
?>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="titulos2" colspan="2">Destinatario</td>
	</tr>
	<tr valign="top">
		<td  valign="top" class="listado2">
<?php
    $varUsuario = '<input type="radio" name="radicado_rem" value="1" id="rusuario" ' . $datoss1 . ' ';
	if (!$radicado_rem and $ent==1 and $radicado_rem != 7) $radicado_rem=1;
	if ($radicado_rem==1 and $remitente != 7) {
        // Se coloco este if para que no se tire el html ATTE CMAURICIO
        if ($datoss1 != " checked ") {
            $varUsuario .= 'checked';
        }
    }

    $varUsuario .= '>';
    echo $varUsuario . "\n";
    echo $tip3Nombre[1][$ent];
?>
		<br>
		<?=$otro_us11." - ".substr($nombret_us11,0,35)?>
		<br>
		<?=$direccion_us11?>
		<br>
		<?="$dpto_nombre_us11/$muni_nombre_us11"?>
		</td>
		<td  valign="top" class="listado2">
			<input type="radio" name="radicado_rem" id="rempre" value="3" <?=$datoss3?> '<?php  if($radicado_rem==3){echo " checked ";}  ?> '>
			<?=$tip3Nombre[3][$ent]?>
			<br>
			<?=substr($nombret_us3,0,35)?>
			<br>
			<?=$direccion_us3?>
			<br>
			<?="$dpto_nombre_us3/$muni_nombre_us3"?>
			<br>
			Notificacion a:
			( <span class="titulosError">
			<input type="text" name="direccionAlterna" value="<?=$direccionAlterna?>" size="18" readonly="readonly"></span> )
			<?php
				if(!empty($codigo)){
					$anexo = $codigo;
			?>
			<input name="modificarDireccion" value="Modificar Datos" class="botones" onclick="window.open('./mostrarDireccion.php?<?=session_name()?>=<?=session_id()?>&krd=<?=$krd?>&anexo=<?=$anexo?>&dptoCodi=<?=$codep_us1?>','Tipificacion_Documento','height=200,width=450,scrollbars=no')" type="button">
			<?php
				}
			?>
            </td>
		</tr>
		<tr valign="top">
			<td  valign="top" class="listado2">
				<input type="radio" name="radicado_rem" id="rpredi" value=2 <?=$datoss2?> '<?php  if($radicado_rem==2){echo " checked ";}  ?> '>
				<?=$tip3Nombre[2][$ent]?><br>
				<?=$otro_us2." - ".substr($nombret_us2,0,35)?>
				<br>
				<?=$direccion_us2?>
				<br>
				<?="$dpto_nombre_us2/$muni_nombre_us2"?>
			</td>
			<td  valign="top" class="listado2">
				<input type="radio" name="radicado_rem" value="7" <?=$datoss4?> '<?php  if($radicado_rem==7){echo " checked ";}  ?> ' id="rotro">
				Otro
			</td>
		</tr>
		<tr valign="top" >
			<td  class='titulos2' valign="top" colspan="2">Descripcion</td>
		</tr>
		<tr valign="top">
			<td  valign="top" colspan="2" height="66" class="listado2"  >
				(Es el asunto en el caso de que sea un anexo documento a Radicar)<br>
				<textarea name="descr" cols="35" rows="4" class="tex_area" id="descr"><?=$descr?></textarea>
				</b><input name="usuar" type="hidden" id="usuar" value="<?php echo $usuar ?>"><br>
				<input name="predi" type="hidden" id="predi" value="<?php echo $predi ?>">
				<input name="empre" type="hidden" id="empre" value="<?php echo $empre ?>">
<?php
	 if($tipo==999999) {
        echo "
			<div align='left'>
			<font size='1' color='#000000'><b>Ubicaci&oacute;n F&iacute;sica:</b></font>
			<input type='text' name='anex_ubic' value='$anex_ubic'>";
	  }
?>
			</span></font>
                </td>
			</tr>
		</table>
	</td>
</tr>
<?php
    if($codigo) {
        $busq_salida="true";
?>
	<tr>
		<td width="100%" class="titulos2">
            <font size="1" class="etextomenu">Otro Destinatario</font>
		</td>
		<td width="25%">
			<input type="button" name="Button" value="BUSCAR" class="botones" onClick="Start('<?=$ruta_raiz?>/radicacion/buscar_usuario.php?busq_salida=<?=$busq_salida?>&nombreTp3=<?=$nombreTp3?>&krd=<?=$krd?>',1024,500);">
		</td>
	</tr>
	<tr>
		<td class='celdaGris'  colspan="2"><font size="1">
			<table  width="100%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
				<tr align="center">
					<td width="203" class="titulos2" >DOCUMENTO</td>
					<td class="titulos2">NOMBRE</td>
					<td class="titulos2">Dirigido a:</td>
					<td class="titulos2" width="103">DIRECCION</td>
					<td class="titulos2" width="68">EMAIL</td>
					<td class="titulos2" width="68"></td>
				</tr>
				<tr class="<?=$grilla ?>">
					<td align="center" class="listado2">
						<input type="hidden" name="telefono_us1" value='' class="tex_area" size="10">
						<input type="hidden" name="tipo_emp_us1" class="tex_area" size="3" value="<?=$tipo_emp_us1?>">
						<input type="hidden" name="documento_us1" class="tex_area" size="3" value="<?=$documento_us1?>">
						<input type="hidden" name="idcont1" id="idcont1" value="<?=$idcont1 ?>" class="e_cajas" size="4">
						<input type="hidden" name="idpais1" id="idpais1" value="<?=$idpais1 ?>" class="e_cajas" size="4">
						<input type="hidden" name="codep_us1" id="codep_us1" value="<?=$codep_us1 ?>" class="e_cajas" size="4">
						<input type="hidden" name="muni_us1" id="muni_us1"  value="<?=$muni_us1 ?>" class="e_cajas" size="4">
						<input type="text" name="cc_documento_us1" value="<?=$cc_documento_us1 ?>" class="e_cajas" size="8">
				   </td>
				<td width="329" align="center" class="listado2">
					<input type="text" name="nombre_us1" value="" size="3" class="tex_area">
					<input type="text" name="prim_apel_us1" value="" size="3" class="tex_area">
					<input type="text" name="seg_apel_us1" value="" size="3" class="tex_area">
				</td>
				<td width="140" align="center" class="listado2">
					<input type="text" name="otro_us7" value="" class="tex_area" size="20" maxlength="45">
				</td>
				<td align="center" class="listado2">
					<input type="text" name="direccion_us1" value='' class="tex_area" size="6">
				</td>
				<td width="68" align="center" class="listado2" colspan="2">
					<input type="text" name="mail_us1" value='' class="tex_area" size="11">
					<input type="hidden" name="codpostal_us1" value=''>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="listado2" align="center">
					<input type="button" name="cc" value="Grabar Destinatario" class="botones_mediano" onClick="continuar_grabar()">
				</td>
				<td colspan="3" class="listado2" align="center">
<?php
    // Si viene la variable cc(Boton de destino copia) envia al modulo de grabacion de datos
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    if(!empty($cc)) {
		if(($nombre_us1 || $prim_apel_us1 || $seg_apel_us2) and
                $direccion_us1 and $muni_us1 and $codep_us1) {
			$isql = "select sgd_dir_tipo NUM
					from sgd_dir_drecciones
					where sgd_dir_tipo like '7%' and sgd_anex_codigo='$codigo'
					order by sgd_dir_tipo desc";
			$rs = $db->conn->Execute($isql);
			if (!$rs->EOF)
				$num_anexos = substr($rs->fields["NUM"],1,2);
		     $nurad = $radi;
		     include ("$ruta_raiz/radicacion/grb_direcciones.php");
        } else {
	        echo '<font size="1">No se pudo guardar el documento,ya que faltan datos.
                        (Los datos m&iacute;nimos de envio so Nombre,
                            direccion, departamento, municipio)</font>';
		}
	}
?>
	</td>
	</tr>
	<tr class='<?=$grilla ?>'>
<?php
    if($borrar) {
        $isql = "delete from sgd_dir_drecciones
                    where sgd_anex_codigo='$codigo' and
                            sgd_dir_tipo = $borrar ";
        $rs = $db->conn->Execute($isql);
    }

	include_once ("$ruta_raiz/include/query/queryNuevo_archivo.php");
    $isql = $query1;

    $rs = $db->conn->Execute($isql);
	// $i_copias  Indica cuantas copias se han anadido
	$i_copias = 0;
	while($rs && !$rs->EOF) {
        $i_copias++;
        $sgd_ciu_codigo = ""; $sgd_esp_codi= ""; $sgd_oem_codi= "";
        $sgd_ciu_codi = $rs->fields["SGD_CIU_CODIGO"];
        $sgd_esp_codi = $rs->fields["SGD_ESP_CODI"];
        $sgd_oem_codi = $rs->fields["SGD_OEM_CODIGO"];
        $sgd_dir_tipo = $rs->fields["SGD_DIR_TIPO"];
        $sgd_doc_fun  = $rs->fields["SGD_DOC_FUN"];

        if($sgd_ciu_codi>0) {
	     $isql = "select SGD_CIU_NOMBRE NOMBRE,
                        SGD_CIU_APELL1 APELL1,
                        SGD_CIU_APELL2 APELL2,
                        SGD_CIU_CEDULA IDENTIFICADOR,
                        SGD_CIU_EMAIL MAIL,
                        SGD_CIU_DIRECCION  DIRECCION
                    from sgd_ciu_ciudadano
                    where sgd_ciu_codigo = $sgd_ciu_codi";
	   }
	   if($sgd_esp_codi>0) {
	     $isql = "select nombre_de_la_empresa NOMBRE,
                        identificador_empresa IDENTIFICADOR,
                        EMAIL MAIL,
                        DIRECCION DIRECCION
                    from bodega_empresas
                    where identificador_empresa = $sgd_esp_codi";
	   }

	   if($sgd_oem_codi>0) {
	     $isql = "select sgd_oem_oempresa NOMBRE,
                        SGD_OEM_DIRECCION DIRECCION,
                        sgd_oem_codigo IDENTIFICADOR
                    from sgd_oem_oempresas
                    where sgd_oem_codigo = $sgd_oem_codi";
	   }

	   if($sgd_doc_fun > 0) {
	     $isql = "select u.usua_nomb NOMBRE,
                        d.depe_nomb DIRECCION,
                        u.usua_doc IDENTIFICADOR,
                        u.usua_email MAIL
                    from usuario u,
                        dependencia d,
                        SGD_USD_USUADEPE USD
                    where U.usua_doc = '$sgd_doc_fun' AND
                            U.USUA_DOC = USD.USUA_DOC AND
                            U.USUA_LOGIN = USD.USUA_LOGIN AND
                            USD.SGD_USD_DEFAULT = 1 AND
                            USD.DEPE_CODI = d.DEPE_CODI ";
	   }

    $rs2 = $db->conn->Execute($isql);
    $nombre_otros = "";
	if($rs2 && !$rs2->EOF)
		$nombre_otros =$rs2->fields["NOMBRE"]."".$rs2->fields["APELL1"]." ".$rs2->fields["APELL2"];
?>
		<td align="center" class="listado2">
            <font size="1">
			    <?=$rs2->fields["IDENTIFICADOR"];?>
            </font>
        </td>
		<td align="center" class="listado2" colspan="1">&nbsp;
            <font size="1">
                <?=$nombre_otros?>
            </font>
		</td>
		<td align="center" class="listado2" colspan="1">&nbsp;
            <font size="1">
			    <?=$rs->fields["SGD_DIR_NOMBRE"];?>
            </font>
			&nbsp;</td>
		<td align="center" class="listado2">&nbsp;
            <font size="1">
                <?=$rs2->fields["DIRECCION"];?>
            </font>
		</td>
		<td width="68" align="center" class="listado2">&nbsp;
            <font size="1">
                <?=$rs2->fields["MAIL"];?>
            </font>
		</td>
		<td width="68" align="center" class="listado2">&nbsp;
            <font size="1">
                <a href='nuevo_archivo.php?<?=$variables?>&borrar=<?=$sgd_dir_tipo?>&tpradic=<?=$tpradic?>&aplinteg=<?=$aplinteg?>'>Borrar</a>
            </font>
        </td>
	</tr>
<?php
	$rs->MoveNext();
}
?>
			</table>
			</font>
        </td>
	</tr>
<?php
    }
?>
		<tr>
			<td class="celdaGris" align="center" colspan="2">
                <font size="1">&nbsp;</font>
            </td>
		</tr>
	</table>
   </div>
	<table><tr><td></td></tr></table>
	<table width="100%"  border="0" cellpadding="0" cellspacing="5" class="borde_tab">
		<tr align="center">
			<td width="18%" class="titulos2">PRIORIDAD</td>
			<td width="82%" height="25" class="titulos2">
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ini_get('upload_max_filesize'); ?>">
				ADJUNTAR ARCHIVO
            </td>
		</tr>
		<tr align="center">
			<td width="20%">
				<select id="selPrioridad" name="selPrioridad">
					<option value="0">Seleccione</option>
					<option value="1" <?php if ($selPrioridad == 1 ) echo "selected" ?> >Traslado</option>
					<option value="2" <?php if ($selPrioridad == 2 ) echo "selected" ?> >Congreso</option>
					<option value="3" <?php if ($selPrioridad == 3 ) echo "selected" ?> >Prorroga</option>
				</select>
			</td>
			<td>
				<p align="left">
				<input name="userfile1" type="file" class="tex_area" onClick="escogio_archivo();" id="userfile" >
				<label>
				    <input name="button" type="button" class="botones_largo" onClick="actualizar()" value="ACTUALIZAR <?=$codigo?>">
				</label>
                </p>
			</td>
		</tr>
	</table>
		<input type="hidden" name="i_copias" value="<?=$i_copias?>" id="i_copias">
    </form>
	</td>
  </tr>
</table>
    <span class="etextomenu" style="text-align: center;">
        <table width="95%" border="0" cellspacing="1" cellpadding="0" align="center" class="t_bordeGris">
                <tr align="center">
                    <td class="celdaGris" height="25"> <span class="etextomenu">
<?php
    if($radicado_rem==7 and $i_copias==0) {
        echo " $mensaje <br><b><span  class='alarmas' >No puede generar envio, No ha anexado destinatario </span></b>";
	} else {
        echo "  $mensaje <input type='button' class ='botones' value='cerrar' onclick='f_close()'> ";
	}
	?>
                </td>
            </tr>
        </table>
    </span>
</body>
</html>
