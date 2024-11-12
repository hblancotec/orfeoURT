<?php
    $ruta_raiz      = "..";
    require_once ($ruta_raiz . '/_conf/constantes.php');
    require_once (PEARPATH .  'HTML/Template/IT.php');
    require_once (ORFEOPATH . 'class_control/Dependencia.php');

    $cont       = 0
    $krdOld     = $krd;
    $carpetaOld = $carpeta;
    $tipoCarpOld= $tipo_carp;
    $moverSelect= 0;
    $usuaDoc    = 0;
    $moverInformados = $_POST['moverInfEstado'];
    $informadosUpd = '';
    $informadoTmp = '';
    $informadosMover = array();
    $informadosMover = (is_array($_POST['checkValue'])) ? array_keys($_POST['checkValue']): null;
    $carpetaNuevaInf = $_POST['moverSelect'];
    $moverSelect = $_POST['moverSelect'];
    session_start();
    $krd = (!$krd) ? $krdOsld : $krd;
    $mensaje_error  = false;
    $subCarpDest    = '';
    if(!isset($_SESSION['dependencia']))  include (ORFEOPATH . "rec_session.php");

    $depeCodi = $_SESSION['dependencia'];
    // Inclusion de archivos para utilizar la libreria ADODB
    require_once (ORFEOPATH . "include/db/ConnectionHandler.php");
    if (!isset($db)) $db = new ConnectionHandler(ORFEOPATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $tpl    = new HTML_Template_IT(TPLPATH);
    $objDep = new Dependencia($db);
    /*
     * Genreamos el encabezado que envia las variable a la paginas siguientes.
     * Por problemas en las sesiones enviamos el usuario.
     * @$encabezado  Incluye las variables que deben enviarse a la singuiente pagina.
     * @$linkPagina  Link en caso de recarga de esta pagina.
     */
    $encabezado  = session_name()."=".session_id();
    $encabezado .= "&krd=$krd&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
    $linkPagina  = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=";

    if (!empty($moverInformados) && $moverInformados == "true") {
        if ($moverSelect != 'N') {
            $moverSelect= $_POST['moverSelect'];
            $usuaDoc    = $_SESSION['usua_doc'];
            $usuaCodi   = $_SESSION['codusuario'];

            if (is_array($informadosMover) || !empty($informadosMover)) {
                $cont = count($informadosMover);
                foreach ($informadosMover as $informado) {
                    $cont--;
                    $informadoTmp = explode('-', $informado);
                    $informadosUpd .= $informadoTmp[1];
                    if ($cont) {
                        $informadosUpd .= ',';
                    }
                }
            } else {
                // No coloco ningun informado para mover
                $tpl->loadTemplatefile('errorMovimientoInf.tpl');
                $tpl->setVariable('ERROR_MSG', "No ha seleccionado ningun informado!!!");
                $tpl->setVariable('ERROR_MSG_DESC', "Por favor seleccione informado(s) que desee mover!!!");
                $tpl->show();
                exit();
            }

            $sqlInf = "UPDATE INFORMADOS
                            SET SGD_INFDIR_CODIGO = $carpetaNuevaInf
                            WHERE RADI_NUME_RADI IN($informadosUpd) AND
                                    USUA_DOC='$usuaDoc' AND USUA_CODI ='$usuaCodi'";

            $rsInf = $db->conn->Execute($sqlInf);
            if ($rsInf === false) {
                $tpl->loadTemplatefile('errorMovimientoInf.tpl');
                $tpl->setVariable('ERROR_MSG', "Error en la actualizacion del informado!!!");
                $tpl->setVariable('ERROR_MSG_DESC', $db->conn->ErrorMsg());
                $tpl->show();
                exit();
            }
            $sqlInfCarp = "SELECT SGD_INFDIR_NOMBRE
                            FROM SGD_INFDIR_INFORMADOSDIR
                            WHERE SGD_INFDIR_CODIGO = $carpetaNuevaInf";

            $rsInfCarp = $db->conn->Execute($sqlInfCarp);
            if (!$rsInfCarp->EOF) {
                $subCarpDest = $rsInfCarp->fields['SGD_INFDIR_NOMBRE'];
            }

            $tpl->loadTemplatefile('mostrarMovimientoInformados.tpl');
            $tpl->setVariable('USUA_LOGIN', $usuario);
            $tpl->setVariable('INFORMADOS', $informadosUpd);
            $tpl->setVariable('DEPE_NOMB',  $depe_nomb);
            $tpl->setVariable('SUB_CARPETA',$subCarpDest);
            $tpl->setVariable('USUA_LOGIN_DEST',  $krd);
            $tpl->show();
            exit();
        } else {
            // No ha seleccionado una carpeta destino
            $errorCarpNo = "No ha seleccionado la carpeta destino donde van a mover los informados";
            $tpl->loadTemplatefile('errorMovimientoInf.tpl');
            $tpl->setVariable('ERROR_MSG', "Movimiento de informado!!!");
            $tpl->setVariable('ERROR_MSG_DESC', $errorCarpNo);
            $tpl->show();
            exit();
        }
    }
    // filtro de datos

    if(isset($_POST['checkValue'])) {
        $num = count($_POST['checkValue']);
        reset($_POST['checkValue']);
        $i = 0;
        $jglCounter = 0;
        $resultadoJGL = "";
        while (list($recordid,$tmp) = each($_POST['checkValue'])) {
            $record_id = $recordid;
            switch ($codTx) {
                case  7:
                case  8:
                        if (strpos($record_id,'-')) {
                            //Si trae el informador concatena el informador con el radicado sino solo concatena los radicados.
                            $tmp = explode('-',$record_id);
                            if ($tmp[0]) {
                                $whereFiltro .= ' (b.radi_nume_radi = '.$tmp[1].' and i.info_codi='.$tmp[0].') or';
                                $tmp_arr_id=2;
                            } else {
                                $whereFiltro .= ' b.radi_nume_radi = '.$tmp[1].' or';
                                $tmp_arr_id=1;
                            }
                        } else {
                            $whereFiltro .= ' b.radi_nume_radi = '.$record_id.' or';
                            $tmp_arr_id=0;
                        }
                        $record_id = $tmp[1];
                    break;
                case  9:
                case 12:
                case 13:
                        $condicionAnexBorrados =  " and anex_borrado = 'N'";
                        /*
                        * Se crea condicion de obligatoriedad clasificacion TRD  and ($dependencia!=100 and $codusuario != 1)
                        */
                        if ((($codTx == 9 or $codTx == 12)  and $codusuario != 1) or $codTx == 16 or $codTx == 13) {
                            include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
                            $db = new ConnectionHandler(ORFEOPATH);
                            include_once(ORFEOPATH . "include/query/busqueda/busquedaPiloto1.php");
                            /*
                            * Condicion Radicado Padre
                            */
                            $anoRad = substr($record_id,0,4);
                            $isqlTRDP = "SELECT $radi_nume_radi as RADI_NUME_RADI
                                            FROM SGD_RDF_RETDOCF r
                                            WHERE r.RADI_NUME_RADI = '$record_id'";

                            if($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003") $pasaFiltro = "Si";
                            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                            $rsTRDP = $db->conn->Execute($isqlTRDP);
                            $radiNumero = $rsTRDP->fields["RADI_NUME_RADI"];

                            if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")  &&
                                    strlen (trim($radiNumero)==0)) {
                                $pasaFiltro = "No";
                                $setFiltroSinTRD .= $record_id ;
                                if($i<=($num)) {
                                    $setFiltroSinTRD .= ",";
                                }
                                break;
                            }

                           // Condicion Anexos Radicados
                           $isqlTRDA = "select $radi_nume_salida as RADI_NUME_SALIDA
                                            from anexos
                                            where ANEX_RADI_NUME = '$record_id' and
                                                RADI_NUME_SALIDA != 0 and
                                                RADI_NUME_SALIDA not in(select RADI_NUME_RADI from SGD_RDF_RETDOCF)";

                           if($codTx == 12 || $codTx == 13 || $codTx ==9) {
                                $isqlTRDA  .= $condicionAnexBorrados ;
                           }
                           $rsTRDA = $db->conn->Execute($isqlTRDA);

                           while($rsTRDA && !$rsTRDA->EOF && $pasaFiltro!="No") {
                                $radiNumero = $rsTRDA->fields["RADI_NUME_SALIDA"];
                                $anoRadsal=substr($radiNumero,0,4);

                                if ($radiNumero !='' &&
                                        !($anoRadsal == "2005" or $anoRadsal == "2004" or $anoRadsal == "2003")) {
                                    $pasaFiltro="No";
                                    $setFiltroSinTRD .= $record_id ;
                                    if($i<=($num)) {
                                        $setFiltroSinTRD .= ",";
                                    }
                                    break;
                                }
                                $rsTRDA->MoveNext();
                            }
                            $i++;
                        }
                        $whereFiltro.= ' b.radi_nume_radi = '.$record_id.' or';

                        /**
                         * Modificaciones Febrero de 2007, por SSPD para el DNP
                         * Archivar:
                         * Se verifica si el radicado se encuentra o no en un expediente,
                         * si es negativa la verificacion, ese radicado no se puede archivar
                         */
                        if ( $codTx == 13 && $archivado_requiere_exp ) {
                            include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
                            $db = new ConnectionHandler(ORFEOPATH);

                            $anoRad1 = substr($record_id,0,4);

                            $isqlExp = "select SGD_EXP_NUMERO as NumExpediente from SGD_EXP_EXPEDIENTE
                            where RADI_NUME_RADI = '$record_id'";
                            $rsExp = $db->conn->Execute($isqlExp);
                            $resultadoJGL .= "CONSULTA: $isqlExp ";

                            if  ( $anoRad1 == "2005" || $anoRad1 == "2006" )
                            {
                            }
                            else {

                              if ( $rsExp && !$rsExp->EOF ) {
                                  $expNumero = $rsExp->fields[0];
                                  if ( $expNumero =='' || $expNumero == null ) {
                                      $setFiltroSinEXP .= $record_id ;
                                      if ($jglCounter<=($num)) {
                                          $setFiltroSinEXP .= ",";
                                      }
                                      break;
                                  }
                                   $rsExp->MoveNext();
                                } else {
                                  $setFiltroSinEXP .= $record_id ;
                                      if($jglCounter<=($num)) {
                                          $setFiltroSinEXP .= ",";
                                      }
                               }

                             };



                            $jglCounter++;
                            //$setFiltroSinEXP = "";
                        }
                    break;
                case 16:
                        // Se crea condicion de obligatoriedad clasificacion TRD
                            include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
                            $db = new ConnectionHandler(ORFEOPATH);
                            include_once(ORFEOPATH . "include/query/busqueda/busquedaPiloto1.php");

                            // Condicion Radicado Padre
                            $anoRad = substr($record_id,0,4);
                            $isqlTRDP = "select $radi_nume_radi as RADI_NUME_RADI
                                            from SGD_RDF_RETDOCF r
                                            where r.RADI_NUME_RADI = '$record_id'";
                            if($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003") $pasaFiltro = "Si";
                            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                            $rsTRDP = $db->conn->Execute($isqlTRDP);
                            $radiNumero = $rsTRDP->fields["RADI_NUME_RADI"];

                            if( !($anoRad == "2005" or
                                    $anoRad == "2004" or
                                    $anoRad == "2003") && strlen (trim($radiNumero)==0)) {
                                $pasaFiltro = "No";
                                $setFiltroSinTRD .= $record_id ;
                                if($i<=($num)) {
                                    $setFiltroSinTRD .= ",";
                                }
                                break;
                            }

                            // Condicion Anexos Radicados
                            $isqlTRDA = "select $radi_nume_salida as RADI_NUME_SALIDA
                                            from anexos
                                            where ANEX_RADI_NUME = '$record_id' and
                                                    RADI_NUME_SALIDA != 0 and
                                                    RADI_NUME_SALIDA not in(select RADI_NUME_RADI from SGD_RDF_RETDOCF)";
                                            $condicionAnexBorrados =  " and anex_borrado = 'N'";
                            $isqlTRDA  .= $condicionAnexBorrados ;
                            $rsTRDA = $db->conn->Execute($isqlTRDA);

                            while($rsTRDA && !$rsTRDA->EOF && $pasaFiltro!="No") {
                                $radiNumero = $rsTRDA->fields["RADI_NUME_SALIDA"];
                                $anoRadsal=substr($radiNumero,0,4);

                                if ($radiNumero !='' &&
                                        !($anoRadsal == "2005" or $anoRadsal == "2004" or $anoRadsal == "2003")) {
                                    $pasaFiltro="No";
                                    $setFiltroSinTRD .= $record_id ;
                                    if($i<=($num)) {
                                        $setFiltroSinTRD .= ",";
                                    }break;
                                }
                                $rsTRDA->MoveNext();
                            }
                            $i++;

                        $whereFiltro.= ' b.radi_nume_radi = '.$record_id.' or';

                        /**
                         * Modificaciones Febrero de 2007, por SSPD para el DNP
                         * Archivar:
                         * Se verifica si el radicado se encuentra o no en un expediente,
                         * si es negativa la verificacion, ese radicado no se puede archivar
                         */
                        //echo $codTx;&& $archivado_requiere_exp

                            include_once (ORFEOPATH . "include/db/ConnectionHandler.php");
                            $db = new ConnectionHandler(ORFEOPATH);

                           $isqlExp = "select SGD_EXP_NUMERO as NumExpediente from SGD_EXP_EXPEDIENTE
                            where RADI_NUME_RADI = '$record_id'";
                            $rsExp = $db->conn->Execute($isqlExp);
                            $resultadoJGL .= "CONSULTA: $isqlExp ";
                            if ( $rsExp && !$rsExp->EOF ) {
                                $expNumero = $rsExp->fields[0];
                                if ( $expNumero =='' || $expNumero == null ) {
                                    $setFiltroSinEXP .= $record_id ;
                                    if($jglCounter<=($num)) {
                                        $setFiltroSinEXP .= ",";
                                    }
                                    break;
                                }
                                $rsExp->MoveNext();
                            }else {
                                $setFiltroSinEXP .= $record_id ;
                                    if($jglCounter<=($num)) {
                                        $setFiltroSinEXP .= ",";
                                    }
                            }
                            $jglCounter++;
                    break;
                default:
                        $whereFiltro.= ' b.radi_nume_radi = '.$record_id.' or';
                        break;
            }
            $setFiltroSelect .= "$record_id,";
        }
        if($setFiltroSinTRD and $pasaFiltro=="No") {
            $mensaje_error = "NO SE PERMITE ESTA OPERACION PARA LOS RADICADOS <BR>
                                < $setFiltroSinTRD > <BR> FALTA CLASIFICACION TRD PARA ESTOS O PARA SUS ANEXOS";
        }

        /**
         * Modificaciones Febrero de 2007, por SSPD para el DNP
         * Archivar:
         * si la variable $setFiltroSinEXP tiene algo, es porque algun radicado no esta en expediente
         */
        if ( $setFiltroSinEXP ) {
                $mensaje_errorEXP = "<br>NO SE PERMITE ESTA OPERACION PARA LOS RADICADOS <BR> < $setFiltroSinEXP > <BR> PORQUE NO SE ENCUENTRAN EN NING&Uacute;N EXPEDIENTE";
        }

        if(substr($whereFiltro,-2)=="or") {
            $whereFiltro = substr($whereFiltro,0,strlen($whereFiltro)-2);
        }
        if(trim($whereFiltro)) {
            $whereFiltro = "and ( $whereFiltro ) ";
        }
    } else {
        $mensaje_error="NO HAY REGISTROS SELECCIONADOS";
    }
?>
<html>
<head>
<title>Enviar Datos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$ruta_raiz; ?>/estilos/orfeo.css">
<script language="javascript">
function notSupported() {
    alert('Su browser no soporta las funciones Javascript de esta pagina.');
}

function setSel(start,end) {
    document.realizarTx.observa.focus();
	var t=document.realizarTx.observa;
	if(t.setSelectionRange)
	{	t.setSelectionRange(start,end);
    	t.focus();
  		//f.t.value = t.value.substr(t.selectionStart,t.selectionEnd-t.selectionStart);
  	}
  	else notSupported();
}

function valMaxChars(maxchars) {
    document.realizarTx.observa.focus();
 	if(document.realizarTx.observa.value.length > maxchars) {
        /*  alert('Demasiados caracteres en el texto ! Por favor borre '+
    	(document.realizarTx.observa.value.length - maxchars)+ ' caracteres pues solo se permiten '+ maxchars);*/
 		alert('Demasiados caracteres en el texto, solo se permiten '+ maxchars);
 		setSel(maxchars,document.realizarTx.observa.value.length);
   	return false;
 	}
 	else	return true;
}

/*
 * OPERACIONES EN JAVASCRIPT
 * @marcados Esta variable almacena el numeo de chaeck seleccionados.
 * @document.realizarTx  Este subNombre de variable me indica el formulario principal del listado generado.
 * @tipoAnulacion Define si es una solicitud de anulacion  o la Anulacion Final del Radicado.
 *
 * Funciones o Metodos EN JAVA SCRIPT
 * Anular()  Anula o solicita esta dependiendo del tipo de anulacin.
 * Previamente verifica que este seleccionado algun  radicado.
 * markAll() Marca o desmarca los check de la pagina.
 *
 */

function Anular(tipoAnulacion) {
	marcados = 0;
	for(i=0;i<document.realizarTx.elements.length;i++)
	{	if(document.realizarTx.elements[i].checked==1 )
		{
			marcados++;
	}	}
<?php
	if($codusuario==1 || $usuario_reasignacion==1) {
        echo "if(document.realizarTx.chkNivel.checked==1)	 marcados = marcados -1 \n";
	}
?>
	if(marcados>=1) {
	  return 1;
	} else {
		alert("Debe marcar un elemento");
		return 0;
	}
}

function markAll(noRad) {
	if(document.realizarTx.elements.check_titulo.checked || noRad >=1) {
		for(i=3;i<document.realizarTx.elements.length;i++) {
			document.realizarTx.elements[i].checked=1;
		}
	} else {
		for(i=3;i<document.realizarTx.elements.length;i++) {
			document.realizarTx.elements[i].checked=0;
		}
	}
}

function okTx() {
	valCheck = Anular(0);
	if(valCheck==0) return 0;
	numCaracteres = document.realizarTx.observa.value.length;
	if(numCaracteres>=6) {
        //alert(document.realizarTx.usCodSelect.options.selected);
		if (valMaxChars(550))
			document.realizarTx.submit();
	} else {
		alert("Atencion: El numero de Caracteres minimo el la Observacion es de 6. (Digito :"+numCaracteres+")");
	}
}
</script>
</head>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="markAll(1);">
<?php
	/**
	 * Modificaciones Febrero de 2007, por SSPD para el DNP
	 * Archivar:
	 * Hay algun error, ya sea por tipificacion o por Expediente, luego se muestra mensaje
	 * donde se indica que no se puede archivar el(los) radicado(s)
	 */
if ($mensaje_errorEXP || $mensaje_error ) {
	DIE ("<center><table class='borde_tab' width=100% CELSPACING=5><tr class=titulosError><td align='center'>$mensaje_errorEXP <br> $mensaje_error</td></tr></table></CENTER>");
} else {
?>
<table border=0 width=100% cellpadding="0" cellspacing="0">
	<tr>
		<td width=100%>
		<br>
		<form action='realizarTx.php?<?=$encabezado?>' method=post name="realizarTx" id="realizarTx">
			<input type='hidden' name="depsel8" value='<?=implode($depsel8,',')?>'>
			<input type='hidden' name="codTx" value='<?=$codTx?>'>
			<input type='hidden' name="EnviaraV" value='<?=$EnviaraV?>'>
			<input type='hidden' name="fechaAgenda" value='<?=$fechaAgenda?>'>
			<table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
			<TR>
				<TD width="30%" class="titulos4">USUARIO:<br><br><?=$_SESSION['usua_nomb']?> </TD>
				<TD width='30%' class="titulos4">DEPENDENCIA::::<br><br><?=$_SESSION['depe_nomb']?><br></TD>
				<td class="titulos4">
<?php
switch ($codTx) {
    case 7:
		print "Borrar Informados ";
		echo "<input type='hidden' name='info_doc' value='".$tmp_arr_id."'>";
		break;
    case 8:	$usDefault = 1;
			$cad = $db->conn->Concat("RTRIM(USD.depe_codi)","'-'","RTRIM(u.usua_codi)");
			$cad2 = $db->conn->Concat($db->conn->IfNull("d.DEP_SIGLA", "'N.N.'"),"'-'","RTRIM(u.usua_nomb)");
			$sql = "select $cad2 as usua_nomb,
                            $cad as usua_codi
                        from usuario u,
                                dependencia d, SGD_USD_USUADEPE USD
                        where USD.depe_codi in(".implode($depsel8,',').")
                                $whereReasignar and
                                u.USUA_ESTA = 1 and
                                USD.depe_codi = d.depe_codi AND
                                u.USUA_LOGIN = USD.USUA_LOGIN AND
                                u.USUA_DOC = USD.USUA_DOC
                        ORDER BY usua_nomb";
			$rs = $db->conn->Execute($sql);
			$usuario = $codUsuario;
			print "Informados";
			print $rs->GetMenu2('usCodSelect[]',$usDefault,false,true,10," id='usCodSelect' class='select' ");
			break;
	case 9:
            $whereDep = "and USD.depe_codi=$depsel ";
			if($dependencia==$depsel) {
                $usDefault = $codusuario;
            }
			// Esta seccion selecciona las dependencias que se deben visualizar a partir de otras
			// By Brayan Plazas
			// 18/10/2005
			$sql = "SELECT DEPENDENCIA_OBSERVA,
                            DEPENDENCIA_VISIBLE
                        FROM DEPENDENCIA_VISIBILIDAD
                        WHERE DEPENDENCIA_OBSERVA=$dependencia and
                        DEPENDENCIA_VISIBLE = " . $depsel;
			$rs1 = $db->conn->Execute($sql);
			$usuario_publico = "";
			if (!$rs1->EOF) {
                //Se adicionan las dependencias que puedan ver a otras en la consulta
				$usuario_publico = "or (u.USUA_LOGIN = USD.USUA_LOGIN AND
                                        u.USUA_DOC = USD.USUA_DOC AND
                                            USD.DEPE_CODI in (";
				while(!$rs1->EOF) {
                    $usuario_publico = $usuario_publico .$rs1->fields["DEPENDENCIA_VISIBLE"].",";
					$rs1->MoveNext();
				}
				$usuario_publico = substr($usuario_publico,
                                            0,
                                            strlen($usuario_publico) - 1) . ") AND u.USUARIO_PUBLICO = 1)";
			}
			//Fin Modificacion
			if((($codusuario==1 || $usuario_reasignacion==1) &&
                    $dependencia!=$depsel ) || ($dependencia==$depsel &&
                    ($codusuario!=1 || $usuario_reasignacion !=1) &&
                    $EnviaraV=="VoBo")) {
                $whereReasignar = " and u.usua_codi=1";
				$usDefault = 1;
			}

			if(($codusuario==1 || $usuario_reasignacion == 1) &&
                    $dependencia==$depsel && $EnviaraV=="VoBo" ) {
                if ($objDep->Dependencia_codigo($dependencia)) {
                    $depPadre=$objDep->getDepe_codi_padre();
				}
				print ("La dependencia  padre ...($depPadre)");

				$whereDep =  " AND USD.depe_codi=$depPadre AND u.usua_codi=1 ";
				$depsel = $depPadre;
			}

			if($EnviaraV=="VoBo") {
                $proccarp = "Visto Bueno";
				$usuario_publico = "";}
			$cad = $db->conn->Concat("RTRIM(USD.depe_codi)","'-'","RTRIM(u.usua_codi)");
			$sql = "select u.USUA_NOMB,
                            $cad as USUA_COD,
                            USD.DEPE_CODI
				        from usuario u, SGD_USD_USUADEPE USD
				    where u.USUA_ESTA=1 AND
                        u.USUA_DOC = USD.USUA_DOC AND
                        u.USUA_LOGIN = USD.USUA_LOGIN
				$whereReasignar
				$whereDep
				$usuario_publico
				ORDER BY u.USUA_CODI,USUA_NOMB";
				$rs = $db->conn->Execute($sql);
				$usuario = $codUsuario;
				?>
				<select name="usCodSelect" class="select">
		<?php
				while(!$rs->EOF) {
					$depCodiP = $rs->fields["DEPE_CODI"];
					$usuNombP = $rs->fields["USUA_NOMB"];
					$usuCodiP = $rs->fields["USUA_COD"];
					$valOptionP = "";
					$valOptionP =$usuNombP;
					$class = "";
					if($depCodiP!=$dependencia) {
                        $sql = "select DEPE_NOMB from dependencia where depe_codi=$depCodiP";
                        $rs2 = $db->conn->Execute($sql);
                        $depNombP = $rs2->fields["DEPE_NOMB"];
                        $valOptionP .= " [ ".$depNombP."] ";
                        $class = " class='leidos'";
					}
		?>
				<option <?=$class?>  value="<?=$usuCodiP?>"><?=$valOptionP?></option>
				<?php
				$rs->MoveNext();
				}
				?>
				</select>
				<?php
				print "Reasignar $proccarp";
				break;
	case 10:
			   $carpetaTipo = substr($carpSel,1,1);
			   $carpetaCodigo = intval(substr($carpSel,-3));
			   if($carpetaTipo==1) {
			   	  $sql = "select NOMB_CARP as carp_desc
                            from CARPETA_PER
					        where codi_carp=$carpetaCodigo and
                                    usua_codi=$codusuario and
                                    depe_codi=$dependencia";
				} else {
				   $sql = "select carp_desc from carpeta where carp_codi=$carpetaCodigo";
				}
				$rs = $db->conn->Execute($sql); # Ejecuta la busqueda y obtiene el recordset vacio
				$carpetaNombre = $rs->fields['carp_desc'];
				print "Movimiento a Carpeta <b>$carpetaNombre</b>
				<input type=hidden name='carpetaCodigo' value=$carpetaCodigo>
				<input type=hidden name='carpetaTipo' value=$carpetaTipo>
				<input type=hidden name='carpetaNombre' value=$carpetaNombre>
				";
			   break;
		   case 12:
				print "Devolver documentos a Usuario Anterior ";
				break;
		   case 13:
		   	    print "Archivo de Documentos";
				break;
			case 16:
		   	    print "Archivo de NRR";
				break;
		}
		?>
		<BR>
		</td>
		<td width='5' class="grisCCCCCC">
			<input type="button" value="REALIZAR" onClick="okTx();" name="enviardoc" align="bottom" class="botones" id="REALIZAR">
		</td>
	</TR>
	<tr align="center">
	<td colspan="4" class="celdaGris" align=center>
		<br>
<?php
		if(($codusuario==1) || ($usuario_reasignacion == 1)) {
?>
        <input type="checkbox" name="chkNivel" checked class="ebutton">
		<span class="info">El documento tomara el nivel del usuario destino.</span><br>
<?php
		}
?>
		<center>
		<table width="500"  border=0 align="center" bgcolor="White">
		<TR bgcolor="White"><TD width="100">
				<center>
				<img src="<?=$ruta_raiz?>/iconos/tuxTx.gif" alt="Tux Transaccion" title="Tux Transaccion">
				</center>
		</td><TD align="left">
        <span class="etextomenu">
        </span>
		        <textarea name="observa" cols="70" rows="3"  class="ecajasfecha"></textarea>
			</TD></TR>
		</center>
		<input type="hidden" name="enviar" value="enviarsi">
		<input type="hidden" name="enviara" value='9'>
		<input type="hidden" name="carpeta" value="12">
		<input type="hidden" name="carpper" value="10001">
	</td>
	</tr>
</table>
	<br>
<?php
	/*  GENERACION LISTADO DE RADICADOS
	 *  Aqui utilizamos la clase adodb para generar el listado de los radicados
	 *  Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
	 *  el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
	 */
	if(!$orderNo)  $orderNo=0;
	$order = $orderNo + 1;

	$sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","b.RADI_FECH_RADI");
	include_once (ORFEOPATH . "include/query/tx/queryFormEnvio.php");
	switch ($codTx) {
        case 12:
			$isql = str_replace("Enviado Por" ,"Devolver a",$isql);
			break;
		default:break;
	}
	$pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	$pager->checkAll = true;
	$pager->checkTitulo = true;
	$pager->Render($rows_per_page=20,$linkPagina,$checkbox=chkAnulados);
?>
<input type='hidden' name=depsel value='<?=$depsel?>'>
</form>
<?php
}
?>
</body>
</html>
