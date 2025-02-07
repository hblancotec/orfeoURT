<?php
	$krdold = $krd;
 	session_start();
	$ruta_raiz = "..";
	if(!$krd) $krd = $krdold;
	if (!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";
	if (!$nurad) $nurad= $rad;
	if($nurad) {
		$ent = substr($nurad,-1);
	}
    include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	include_once ("$ruta_raiz/include/tx/Historico.php");
	include_once ("$ruta_raiz/class_control/TipoDocumental.php");
	include_once ("$ruta_raiz/include/tx/Expediente.php");
	$trd = new TipoDocumental($db);
	$encabezadol  = $_SERVER['PHP_SELF']."?".session_name()."=".session_id();
    $encabezadol .= "&opcionExp=$opcionExp&numeroExpediente=$numeroExpediente";
    $encabezadol .= "&dependencia=$dependencia&krd=$krd&nurad=$nurad";
    $encabezadol .= "&coddepe=$coddepe&codusua=$codusua&depende=$depende";
    $encabezadol .= "&ent=$ent&tdoc=$tdoc&codiTRDModi=$codiTRDModi";
    $encabezadol .= "&codiTRDEli=$codiTRDEli&codserie=$codserie";
    $encabezadol .= "&tsub=$tsub&ind_ProcAnex=$ind_ProcAnex";
?>
<html>
<head>
<title>Tipificar Expediente</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css"><script>

function regresar(){
	document.TipoDocu.submit();
}

function Start(URL, WIDTH, HEIGHT) {
    windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width="+WIDTH+",height="+HEIGHT;
    preview = window.open(URL , "preview", windowprops);
}
</script><style type="text/css">
<!--
.style1 {font-size: 14px}
-->
</style>
</head>
<body bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
 <link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
 <script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<form method="post" action="<?=$encabezadol?>" name="TipoDocu">
<?php
    // Adicion nuevo Registro
    if ($Actualizar && $tsub !=0 && $codserie !=0 ) {
    if(!$digCheck) {
		$digCheck = "E";
	}
       $codiSRD = $codserie;
	$codiSBRD = $tsub;
	$trdExp = substr("00".$codiSRD,-3) . substr("00".$codiSBRD,-3);
	$expediente = new Expediente($db);
	if(!$expManual) {
		$secExp = $expediente->secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp);
	} else {
		$secExp = $consecutivoExp;
	}
	$consecutivoExp = substr("00000".$secExp,-5);
	$numeroExpediente = $anoExp . $dependencia . $trdExp . $consecutivoExp . $digCheck;

    /*
     *  Modificado: 09-Junio-2006 Supersolidaria
     *  Arreglo con los parametros del expediente.
	 */
    foreach ( $_POST as $elementos => $valor ) {
        if ( strncmp ( $elementos, 'parExp_', 7) == 0 ) {
            $indice = ( int ) substr ( $elementos, 7 );
            $arrParametro[ $indice ] = $valor;
        }
    }
    $valor;

	/**  Procedimiento que Crea el Numero de  Expediente
	  *  @param $numeroExpediente String  Numero Tentativo del expediente,
      *          hay que recordar que en la creacion busca la ultima secuencia creada.
	  *  @param $nurad  Numeric Numero de radicado que se insertara en un expediente.
      *  Modificado: 09-Junio-2006 Supersolidaria
      *  La funcion crearExpediente() recibe los parametros $codiPROC y $arrParametro
	  */
		$numeroExpedienteE = $expediente->crearExpediente($numeroExpediente,
                                                            $nurad,
                                                            $dependencia,
                                                            $codusuario,
                                                            $usua_doc,
                                                            $usuaDocExp,
                                                            $codiSRD,
                                                            $codiSBRD,
                                                            'false',
                                                            $fechaExp,
                                                            $_POST['codProc'],
                                                            $arrParametro);
		if($numeroExpedienteE==0) {
			echo "<center>
                        <table class='borde_tab'>
                            <tr>
                                <td class='titulosError'>EL EXPEDIENTE QUE INTENTO CREAR YA EXISTE.</td>
                            </tr>
                        </table>
                </center>";
		} else {
			/**  Procedimiento que Inserta el Radicado en el Expediente
			  *  @param $insercionExp Numeric  Devuelve 1 si inserto el expediente correctamente 0 si Fallo.
			  */
                       "a:" . $numeroExpedienteE . "<br/>";
			$insercionExp = $expediente->insertar_expediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc);
                       "b:" . $insercionExp . "<br/>";
		}
			$codiTRDS = $codiTRD;
			$i++;
            $TRD = $codiTRD;
			$observa = "*TRD*".$codserie."/".$codiSBRD." (Creacion de Expediente.)";
			include_once "$ruta_raiz/include/tx/Historico.php";
			$radicados[] = $nurad;
			$tipoTx = 51;
			//Al crear solo vamos a guardar el historico que indica que el expediente entra a la primera etapa del proceso
//			$Historico = new Historico($db);
//			$Historico->insertarHistoricoExp($numeroExpediente,$radicados, $dependencia,$codusuario, $observa, $tipoTx,0);
			include ("$ruta_raiz/include/tx/Flujo.php");
			$objFlujo = new Flujo($db, $_POST['codProc'],$usua_doc);
			$expEstadoActual = $objFlujo->actualNodoExpediente($numeroExpediente);
			$arrayAristas =$objFlujo->aristasSiguiente($expEstadoActual);
			$aristaActual = $arrayAristas[0];
			$objFlujo->cambioNodoExpediente($numeroExpediente,
                                                $nurad,
                                                $expEstadoActual,
                                                $aristaActual,
                                                1,
                                                "Creacion Expediente",
                                                $_POST['codProc']);
  }
?>
<table border="0" width="100%" align="center" class="borde_tab" cellspacing="1" cellpadding="0">
<?php
    if($numeroExpedienteE != 0) {
?>
	<tr align="center" class="listado2">
    <td width="33%" height="25" align="center" colspan="2">
        <font color="#CC0000" face="arial" size="2">
          Se ha creado el Expediente No.
        </font>
    <b>
          <font color="#000000" face="arial" size="2">
            <?php print $numeroExpedienteE; ?>
        </b>
        <font color="#CC0000" face="arial" size="2">
          con la siguiente informaci&oacute;n:
        </font>
		</center>
	  </td>
	</tr>
<?php
    }
?>
    <tr align="center" class="titulos2">
        <td height="15" class="titulos2" colspan="2">APLICACION DE LA TRD EL EXPEDIENTE</td>
    </tr>
<?php
    if( $numeroExpedienteE != 0 ) {
        $arrTRDExp = $expediente->getTRDExp( $numeroExpediente, $codserie, $tsub, $codProc );
?>
    <tr class="titulos5">
      <td>SERIE</td>
      <td>
        <?php print $arrTRDExp['serie']; ?>
	  </td>
	  </tr>
	<tr class="titulos5">
	  <td>SUBSERIE</td>
	  <td>
        <?php print $arrTRDExp['subserie']; ?>
	  </td>
    </tr>
	<tr class="titulos5">
      <td>PROCESO</td>
      <td>
        <?php print $arrTRDExp['proceso']; ?>
       </td>
	</tr>
<?php
    }
?>
</table>
<?php
if ( !isset( $Actualizar ) ) //Inicio if( $Actualizar )
{
?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
<tr>
<td width="26%" class="titulos5">SERIE</td>
<td width="64%" class=listado5>
<?php
	if(!$tdoc) $tdoc = 0;
	if(!$codserie) $codserie = 0;
	if(!$tsub) $tsub = 0;
	$fechah=date("dmy") . " ". time("h_m_s");
	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
	$check=1;
	$fechaf=date("dmy") . "_" . time("hms");
	$num_car = 4;
	$nomb_varc = "s.sgd_srd_codigo";
	$nomb_varde = "s.sgd_srd_descrip";
	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
	$querySerie = "select distinct ($sqlConcat) as detalle, s.sgd_srd_codigo
		from sgd_mrd_matrird m, sgd_srd_seriesrd s
		where m.depe_codi = '$coddepe'
			and s.sgd_srd_codigo = m.sgd_srd_codigo
			and m.sgd_mrd_esta = 1
			and ".$sqlFechaHoy." between s.sgd_srd_fechini and s.sgd_srd_fechfin
		order by detalle";

	$rsD=$db->conn->Execute($querySerie);
	$comentarioDev = "Muestra las Series Docuementales";
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
 ?>
	</td>
	</tr>
	<tr>
		<td class="titulos5" >SUBSERIE</td>
	<td class=listado5 >
	<?php
	$nomb_varc = "su.sgd_sbrd_codigo";
	$nomb_varde = "su.sgd_sbrd_descrip";
	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
	$querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo
		from sgd_mrd_matrird m, sgd_sbrd_subserierd su
		where m.depe_codi = '$coddepe'
			and m.sgd_srd_codigo = '$codserie'
			and su.sgd_srd_codigo = '$codserie'
			and m.sgd_mrd_esta = 1
			and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
			and ".$sqlFechaHoy." between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
		order by detalle
		";
	$rsSub=$db->conn->Execute($querySub);
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
	if(!$codiSRD)
	{
		$codiSRD = $codserie;
		$codiSBRD = $tsub;
	}
    /**********************************************/
    // Modificacion: 22-Mayo-2006
    // Selecciona el proceso y el codigo correspondiente segun la combinacion
    // Serie-Subserie
   	// $queryPEXP = "select SGD_PEXP_DESCRIP,SGD_PEXP_TERMINOS FROM
   	$queryPEXP = "select SGD_PEXP_DESCRIP,SGD_PEXP_CODIGO FROM
			SGD_PEXP_PROCEXPEDIENTES
			WHERE
				SGD_SRD_CODIGO=$codiSRD
				AND SGD_SBRD_CODIGO=$codiSBRD
			";
	$rs=$db->conn->Execute($queryPEXP);
	$texp = $rs->fields["SGD_PEXP_CODIGO"];
    /*
	$expTerminos = $rs->fields["SGD_PEXP_TERMINOS"];
    if ($expTerminos)
    {
    $expDesc = " $expTerminos Dias Calendario de Termino Total";
    }
    */
    /**********************************************/
?>
		</td>
		</tr>
	<tr>
        <!--Modificacion: 22-Mayo-2006
        Combo para seleccionar el proceso segun la combinacion Serie-Subserie
        -->
        <!--
		<td class="titulos5" colspan="2" ><center>&nbsp;<?=$descTipoExpediente?> - <?=$expDesc?></center></td>
        -->
        <td class="titulos5" >PROCESO</td>
        <td class="listado5" colspan="2" >
          <?php
            $comentarioDev = "Muestra los procesos segun la combinacion Serie-Subserie";
            include "$ruta_raiz/include/tx/ComentarioTx.php";

            print $rs->GetMenu2("codProc", $codProc, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );

            /*
             *  Modificado: 17-Agosto-2006 Supersolidaria
             *  Se crea un arreglo con los procesos asociados a serie-subserie.
             */
            $rs->MoveFirst();
            while ( !$rs->EOF )
            {
                $arrProceso[ $rs->fields["SGD_PEXP_CODIGO"] ] = $rs->fields["SGD_PEXP_DESCRIP"];
                $rs->MoveNext();
            }

            // Si se selecciono Serie-Subserie-Proceso
            if( $codProc != "" && $codProc != 0 && $codserie != "" && $codserie != 0 && $tsub != "" && $tsub != 0 )
            {
                // Termino del proceso seleccionado
                $queryPEXP  = "select SGD_PEXP_TERMINOS";
                $queryPEXP .= " FROM SGD_PEXP_PROCEXPEDIENTES";
                $queryPEXP .= " WHERE SGD_PEXP_CODIGO  = ".$codProc;
                // print $queryPEXP;
                $rs=$db->conn->Execute($queryPEXP);

                $expTerminos = $rs->fields["SGD_PEXP_TERMINOS"];
                if ( $expTerminos != "" )
                {
                    $expDesc = " $expTerminos Dias Calendario de Termino Total";
                }
            }
            print "&nbsp;".$expDesc;
          ?>
        </td>
	</tr>
</table>
<br>
<table border="0" width="100%" align="center" class="borde_tab">
 <tr align="center">
	<td width="13%" height="25" class="titulos5" align="center">
	Nombre de Expediente</TD>
<?php
	if(!$digCheck){
		$digCheck = "E";
	}
	$expediente = new Expediente($db);
	if(!$expManual) {
		if(!$anoExp) $anoExp = date("Y");
		$secExp = $expediente->secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp);
	} else {
		$secExp = $consecutivoExp;
	}
	$trdExp = substr("00".$codiSRD,-3) . substr("00".$codiSBRD,-3); //$$$$$
	$consecutivoExp = substr("00000".$secExp,-5);
    if(!$anoExp) $anoExp = date("Y");
?>
	<td width="33%" class="listado2" height="25">
	<p>
	<select name=anoExp  class=select onChange="submit();">
		<?php
			if($anoExp==(date('Y'))) $datoss = " selected "; else $datoss= "";
		?>
		<option value='<?=(date('Y'))?>' <?=$datoss?>>
		<?=date('Y')?>
		</option>
		<?php
			if($anoExp==(date('Y')-1)) $datoss = " selected "; else $datoss= "";
		?>
		<option value='<?=(date('Y')-1)?>' <?=$datoss?>>
		<?=(date('Y')-1)?>
		</option>
		<?php
			if($anoExp==(date('Y')-2)) $datoss = " selected "; else $datoss= "";
		?>
		<option value='<?=(date('Y')-2)?>' <?=$datoss?>>
		<?=(date('Y')-2)?>
		</option>
		<?php
			if($anoExp==(date('Y')-3)) $datoss = " selected "; else $datoss= "";
		?>
		<option value='<?=(date('Y')-3)?>' <?=$datoss?>>
		<?=(date('Y')-3)?>
		<?php
			if($anoExp==(date('Y')-4)) $datoss = " selected "; else $datoss= "";
		?>
		<option value='<?=(date('Y')-4)?>' <?=$datoss?>>
		<?=(date('Y')-4)?>
		</option>
                <?php
			if($anoExp==(date('Y')-5)) $datoss = " selected "; else $datoss= "";
		?>
		<option value='<?=(date('Y')-5)?>' <?=$datoss?>>
		<?=(date('Y')-5)?>
		</option>

		</option>

		</select>
	<input type=text name=depExp value='<?=$dependencia?>' class=select maxlength="3" size="2">
	<input type=text name=trdExp value='<?=$trdExp?>' class=select maxlength="6" size="6">
	<input type=text name=consecutivoExp value='<?=$consecutivoExp?>'  class=select maxlength="5" size=4>
	<input type=text name=digCheckExp value='<?=$digCheck?>' class=select maxlength="1" size="1">
	<?php
	$numeroExpediente = $anoExp . $dependencia . $trdExp . $consecutivoExp . $digCheck;
	?>
	</center>


	<br>
			A&ntilde;o-Dependencia-Serie Subserie-Consecutivo-E<br>
			El consecutivo "<?=$consecutivoExp?>" es temporal y puede cambiar en el momento de crear el expediente.
		<br>
	<?=$numeroExpediente?>
	</TD>
	</tr>
 <tr align="center">
	<td width="13%" height="25" class="titulos5" align="center">
	Consecutivo de Expediente Manual</TD>
	<td class=listado2>
	<?php
			if($expManual) $datoss=" checked "; else $datoss = "";
	?>
	<input type=checkbox name=expManual <?=$datoss?>  >
	</td>
	</tr>
  <?php
    $sqlParExp  = "SELECT SGD_PAREXP_ETIQUETA, SGD_PAREXP_ORDEN";
    $sqlParExp .= " FROM SGD_PAREXP_PARAMEXPEDIENTE PE";
    $sqlParExp .= " WHERE PE.DEPE_CODI = ".$dependencia;
    $sqlParExp .= " ORDER BY SGD_PAREXP_ORDEN";
    $rsParExp = $db->conn->Execute($sqlParExp);
    while(!$rsParExp->EOF) {
  ?>
    <tr align="center">
      <td width="13%" height="25" class="titulos5" align="left">
  <?php
        print $rsParExp->fields['SGD_PAREXP_ETIQUETA'];
  ?>
      </td>
      <td width="13%" height="25" class="titulos5" align="left">
        <input type="text" name="parExp_<?php print $rsParExp->fields['SGD_PAREXP_ORDEN']; ?>" value="<?php print $_POST[ 'parExp_'.$rsParExp->fields['SGD_PAREXP_ORDEN'] ]; ?>" size="60" readonly>
      </td>
    </tr>
  <?php
        $rsParExp->MoveNext();
    }
  ?>

  <tr align="center">
    <td width="13%" height="25" class="titulos5" align="center" colspan="2">
      <input type="button" name="Button" value="BUSCAR" class="botones" onClick="Start('buscarParametro.php?busq_salida=<?=$busq_salida?>&krd=<?=$krd?>',1024,420);">
    </td>
  </tr>

  <tr>
	<td class="titulos5">
		Fecha de Inicio del Proceso.
	</td>
	<td class="listado2">
  <script language="javascript">
  <?php
    $fechaArreglo = explode(' ',$fechaExp);
    $fechaExp = $fechaArreglo[0];
	 if(!$fechaExp) $fechaExp = date("d/m/Y");
  ?>
   var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "TipoDocu", "fechaExp","btnDate1","<?=$fechaExp?>",scBTNMODE_CUSTOMBLUE);
  </script>
			<script language="javascript">
				dateAvailable1.date = "<?=date('Y-m-d');?>";
				dateAvailable1.writeControl();
				dateAvailable1.dateFormat="dd-MM-yyyy";
			</script>
  </td>
 </tr>
<TD class=titulos5>
		Usuario Responsable del Proceso
	</TD>
	<td class=listado2>
<?php
	$queryUs = "select USUA.usua_nomb,
                            USUA.usua_doc
                        from usuario USUA, SGD_USD_USUADEPE USD
                        where USD.depe_codi=$dependencia AND
                            USUA.USUA_DOC = USD.USUA_DOC AND
                            USUA.USUA_LOGIN = USD.USUA_LOGIN AND
                            USUA_ESTA=1
						order by usua_nomb";
	$rsUs = $db->conn->Execute($queryUs);
	print $rsUs->GetMenu2("usuaDocExp", "$usuaDocExp", "0:-- Seleccione --", false,""," class='select' onChange='submit()'");
?>
	</td>
</tr>
</table>
<br>
<?php
if($_POST['crearExpediente']) {
?>
<table border="0" width="100%" align="center" class="borde_tab">
		<tr align="center">
		<td width="33%" height="25" class="listado2" align="center">
		<center class="titulosError2">
		ESTA SEGURO DE CREAR EL EXPEDIENTE ? <BR>
		EL EXPEDIENTE QUE VA HA CREAR ES EL :
		</center><B><center class="style1"><?=$numeroExpediente?></center>
		</B>
		<div align="justify"><br>

		  <strong><b>Recuerde:</b>No podr&aacute; modificar el numero de expediente si hay un error en el expediente, mas adelante tendr&aacute; que excluir este radicado del expediente y si es el caso solicitar la anulaci&oacute;n del mismo. Ademas debe tener en cuenta que apenas coloca un nombre de expediente, en Archivo crean una carpeta f&iacute;sica en el cual empezaran a incluir los documentos pertenecientes al mismo. </strong>
		  </div></TD>
	</tr>
  </table>
<?php
}
?>

<?php
}// Fin if( $Actualizar )
?>
<table border=0 width=100% align="center" class="borde_tab">
	<tr align="center">
	<td width="33%" height="25" class="listado2" align="center">
	<center>
<?php
    /*
     *  Modificado: 11-Agosto-2006 Supersolidaria
     *  No se tiene en cuenta la seleccion de algun proceso para crear un expediente.
	 */
    if($tsub and $codserie && !$Actualizar and $usuaDocExp) {
		if(!$crearExpediente) {
            /*
             *  Modificado: 17-Agosto-2006 Supersolidaria
             *  Si hay procesos asociados, muestra un mensaje indicando que se debe seleccionar alguno.
             */
            if( is_array( $arrProceso ) && $codProc == 0 )
            {
			?>
			<input name="crearExpediente" type="button" class="botones_funcion" value=" Crear Expediente " onClick="alert('Por favor seleccione un proceso.'); document.TipoDocu.codProc.focus();">
            <?php
            } else {
                echo "\t" . '<input name="crearExpediente" type=submit class="botones_funcion" value=" Crear Expediente ">' . "\n";
            }
        }
        else
        {
			?>
			<input name="Actualizar" type=submit class="botones_funcion" value=" Confirmacion Creacion de Expediente ">
			<?php
		}
	}
	?>
	</center></TD>
	<td width="33%" class="listado2" height="25">
	<center><input name="cerrar" type="button" class="botones_funcion" id="envia22" onClick="opener.regresar(); window.close();" value=" Cerrar "></center></TD>
	</tr>
</table><script>
function borrarArchivo(anexo,linkarch){
	if (confirm('Esta seguro de borrar este Registro ?'))
	{
		nombreventana="ventanaBorrarR1";
		url="tipificar_documentos_transacciones.php?borrar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&nurad=<?=$nurad?>&codiTRDEli="+anexo+"&linkarchivo="+linkarch;
		window.open(url,nombreventana,'height=250,width=300');
	}
return;
}
function procModificar()
{
if (document.TipoDocu.tdoc.value != 0 &&  document.TipoDocu.codserie.value != 0 &&  document.TipoDocu.tsub.value != 0)
  {
  <?php
      $sql = "SELECT RADI_NUME_RADI
					FROM SGD_RDF_RETDOCF
					WHERE RADI_NUME_RADI = $nurad
				    AND  DEPE_CODI =  $coddepe";
		$rs=$db->conn->Execute($sql);
		$radiNumero = $rs->fields["RADI_NUME_RADI"];
		if ($radiNumero !='') {
			?>
			if (confirm('Esta Seguro de Modificar el Registro de su Dependencia ?'))
				{
					nombreventana="ventanaModiR1";
					url="tipificar_documentos_transacciones.php?modificar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&tdoc=<?=$tdoc?>&tsub=<?=$tsub?>&codserie=<?=$codserie?>&coddepe=<?=$coddepe?>&nurad=<?=$nurad?>";
					window.open(url,nombreventana,'height=200,width=300');
				}
			<?php
	 		}else
			{
			?>
			alert("No existe Registro para Modificar ");
			<?php
			}
       ?>
     }
   else
   {
    alert("Campos obligatorios ");
   }
return;
}

</script>
</form>
</span>
<p>
<?=$mensaje_err?>
</p>
</span>
</body>
</html>
