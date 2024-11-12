<?php
    $krdold = $krd;
    session_start();
    $ruta_raiz = "..";
    
    if(!$krd) {
        $krd = $krdold;
    }
    
    if (!$nurad) {
        $nurad = $rad;
    }

    include "$ruta_raiz/rec_session.php";	
    include_once( "$ruta_raiz/include/db/ConnectionHandler.php" );
    $db = new ConnectionHandler( "$ruta_raiz" );
    include_once( "$ruta_raiz/include/tx/Historico.php" );
    include_once( "$ruta_raiz/include/tx/Expediente.php" );
    
    $encabezado  = $_SERVER['PHP_SELF']."?".session_name()."=".session_id();
    $encabezado .= "&opcionExp=$opcionExp&numeroExpediente=$numeroExpediente";
    $encabezado .= "&dependencia=$dependencia&krd=$krd&nurad=$nurad";
    $encabezado .= "&coddepe=$coddepe&codusua=$codusua&depende=$depende";
    $encabezado .= "&ent=$ent&tdoc=$tdoc&codiTRDModi=$codiTRDModi";
    $encabezado .= "&codiTRDEli=$codiTRDEli&codserie=$codserie";
    $encabezado .= "&tsub=$tsub&ind_ProcAnex=$ind_ProcAnex";
    $expediente  = new Expediente($db);
    
    // Inserta el radicado en el expediente
    if($funExpediente == "INSERT_EXP") { 
        // Consulta si el radicado esta incluido en el expediente.
        $arrExpedientes = $expediente->expedientesRadicado($_GET['nurad']);
        /* Si el radicado esta incluido en el expediente digitado por el usuario.
         * != No identico no se puede poner !== por que la funcion array_search 
         * tambien arroja 0 o "" vacio al ver que un expediente no se encuentra
         */ 
        foreach ( $arrExpedientes as $line_num => $line){
            if ($line === $_POST['numeroExpediente']) {
                  echo '<center><hr><font color="red">El radicado ya est&aacute; incluido en el expediente.</font><hr></center>';
            } else {
                  $resultadoExp = $expediente->insertar_expediente($_POST['numeroExpediente'],
                                                                    $_GET['nurad'],
                                                                    $dependencia,
                                                                    $codusuario,
                                                                    $usua_doc);
                if( $resultadoExp == 1 ) {
                    $observa = "Incluir radicado en Expediente";
                    include_once "$ruta_raiz/include/tx/Historico.php";
                    $radicados[] = $_GET['nurad'];
                    $tipoTx = 53;
                    $Historico = new Historico( $db );
                    $Historico->insertarHistoricoExp($_POST['numeroExpediente'],
                                                        $radicados,
                                                        $dependencia,
                                                        $codusuario,
                                                        $observa,
                                                        $tipoTx,
                                                        0);
?>
            <script language="JavaScript">
              opener.regresar();
              window.close();
            </script>  
<?php
                } else {
                    echo '<hr><font color=red>No se anexo este radicado al expediente. Verifique que el numero del expediente exista e intente de nuevo.</font><hr>';	    
                }
            }
        }
    }
    
    // Consulta si existe o no el expediente.
    $esNumero         = 0;
    $existeExpediente = '';
    $patron           = "/^[[:digit:]]*(e|E)$/";
    $numeroExpediente = (!empty($_POST['numeroExpediente'])) ? $_POST['numeroExpediente'] : null;
    $numeroExpediente = ($_POST['buscarNombre']) ? $_POST['nombreExpediente'] : $numeroExpediente;
    $numeroExpediente = trim($numeroExpediente);
    $esNumero         = preg_match($patron, $numeroExpediente);
    $mostrarNumExp    = (($esNumero && $numeroExpediente) || (!$esNumero && !$numeroExpediente))
                        ? '<input type="text" name="numeroExpediente" id="numeroExpediente" value="'.$numeroExpediente.'" size="30">'
                        : '<input type="text" name="nombreExpediente" id="nombreExpediente" value="'.$numeroExpediente.'" size="30">';
?>
<html>
<head>
<title>Incluir en Expediente</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
    function validarNumExpediente() {
        numExpediente = document.getElementById( 'numeroExpediente' ).value;
        
        // Valida que se haya digitado el nombre del expediente
        // ano dependencia serie subserie consecutivo E
        if( numExpediente.length != 0 && numExpediente != "" ) {
            insertarExpedienteVal = true;
        }
        
        else if( numExpediente.length == 0 || numExpediente == "" ) {
            alert( "Error. Debe especificar el nombre de un expediente." );
            document.getElementById( 'numeroExpediente' ).focus();
            insertarExpedienteVal = false;
        }
        
        if( insertarExpedienteVal == true ) {
            document.insExp.submit();
        }
    }

    function confirmaIncluir() {
        document.getElementById( 'funExpediente' ).value = "INSERT_EXP";
        document.insExp.submit();
    }
</script>
</head>
<body bgcolor="#FFFFFF" onLoad="document.insExp.numeroExpediente.focus();">
<form method="post" action="<?php echo $encabezado; ?>" name="insExp">
<input type="hidden" name="funExpediente" id="funExpediente" value="">
<input type="hidden" name="confirmaIncluirExp" id="confirmaIncluirExp" value="">
  <table border=0 width=70% align="center" class="borde_tab" cellspacing="1" cellpadding="0">        
    <tr align="center" class="titulos2">
      <td height="15" class="titulos2" colspan="2">INCLUIR EN  EL EXPEDIENTE</td>
    </tr>
  </table>
<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
</table>
<table border="0" width="70%" align="center" class="borde_tab">
    <tr align="center">
      <td class="titulos5" align="left" nowrap>Nombre del Expediente</td>
      <td class="titulos5" align="left"><?php echo $mostrarNumExp; ?></td>
    </tr>
</table>
<table border=0 width=70% align="center" class="borde_tab">
	<tr align="center">
	<td width="33%" height="25" class="listado2" align="center">
	<center>
	  <input name="btnIncluirExp" type="button" class="botones_funcion" id="btnIncluirExp" onClick="validarNumExpediente();" value="Incluir en Exp">
		</center></td>
	<td width="33%" class="listado2" height="25">
        <center>
            <input name="btnCerrar" type="button" class="botones_funcion" id="btnCerrar" onClick="opener.regresar(); window.close();" value="Cerrar">
        </center>
    </td>
	</tr>
</table>
<?php
    if (!empty($numeroExpediente)) {
        $existeExpediente = $expediente->existeExpediente($numeroExpediente);
    }
    
    if (!empty($existeExpediente)) {
        if ($esNumero) {
?>
<table border="0" width="70%" align="center" class="borde_tab">
  <tr align="center">
    <td width="33%" height="25" class="listado2" align="center">
      <center class="titulosError2">ESTA SEGURO DE INCLUIR ESTE RADICADO EN EL EXPEDIENTE:</center>
      <b>
        <center class="style1"><b><?php echo $numeroExpediente; ?></b></center>
      </b>
      <div align="justify"><br>
        <strong><b>Recuerde:</b>No podr&aacute; modificar el numero de expediente si hay
        un error en el expediente, m&aacute;s adelante tendr&aacute; que excluir este radicado del
        expediente y si es el caso solicitar la anulaci&oacute;n del mismo. Adem&aacute;s debe
        tener en cuenta que tan pronto coloca un nombre de expediente, en Archivo crean
        una carpeta f&iacute;sica en el cual empezaran a incluir los documentos
        pertenecientes al mismo.
        </strong>
      </div>
    </td>
  </tr>
</table>
<table border="0" width="70%" align="center" class="borde_tab">
  <tr align="center">
    <td width="33%" height="25" class="listado2" align="center">
	  <center>
	    <input name="btnConfirmar" type="button" onClick="confirmaIncluir();" class="botones_funcion" value="Confirmar">
	  </center>
    </td>
	<td width="33%" class="listado2" height="25">
	<center><input name="cerrar" type="button" class="botones_funcion" id="envia22" onClick="opener.regresar(); window.close();" value=" Cerrar "></center></td>
	</tr>
</table>
<?php
        } else {
            // Buscar por nombre del expediente
?>
<input name="buscarNombre" type="hidden" value="1">
<table border="0" width="70%" align="center" class="borde_tab">
  <tr align="center">
    <td width="33%" height="25" class="listado2" align="center">
      <center class="titulosError2">
        DEBE ESTAR SEGURO DE INCLUIR ESTE RADICADO EN EL EXPEDIENTE SELECCIONADO DE LA LISTA MOSTRADA: 
      </center>
      <div align="justify"><br>
        <strong><b>Recuerde:</b>No podr&aacute; modificar el numero de expediente si hay
        un error en el expediente, m&aacute;s adelante tendr&aacute; que excluir este radicado del
        expediente y si es el caso solicitar la anulaci&oacute;n del mismo. Adem&aacute;s debe
        tener en cuenta que tan pronto coloca un nombre de expediente, en Archivo crean
        una carpeta f&iacute;sica en el cual empezaran a incluir los documentos
        pertenecientes al mismo.
        </strong>
      </div>
    </td>
  </tr>
  <tr class="tpar">
    <td class="tpar">
        <table>
            <tr>
                <th class="titulos3"><span class="titulos3">N&uacute;mero Expediente</span></td>
                <th class="titulos3"><span class="titulos3">Nombre Expediente</span></td>
                <th class="titulos3"><span class="titulos3">Parametro 1</span></td>
                <th class="titulos3"><span class="titulos3">Parametro 2</span></td>
                <th class="titulos3"><span class="titulos3">Parametro 3</span></td>
                <th class="titulos3"><span class="titulos3">Parametro 4</span></td>
                <th class="titulos3"><span class="titulos3">Parametro 5</span></td>
                <th class="titulos3"><span class="titulos3">&nbsp;</span></td>
            </tr>
<?php
        $estilos   = array();
        $contExp   = 0;
        $estilos[] = "listado2";
        $estilos[] = "listado1";
        $palabras  = array();
        $encontro  = 0;
        $patronBusqueda = "";
        if (is_array($existeExpediente)) {
            foreach ($existeExpediente as $datosExpediente) {
                $estiloFila = $estilos[$contExp%2];
                echo "\t\t<tr class='$estiloFila'>\n\t\t\t";
                foreach ($datosExpediente as $campo) {
                    $encontro = eregi($numeroExpediente, $campo, $palabras);
                    if ($encontro) {
                        $palabraRep = '<span class="no_leidos"><font color="#0040FF"><b><i>' . $palabras[0] . '</i></b></font></span>';
                        $patronBusqueda = $palabras[0];
                        $campo = preg_replace ($patronBusqueda, $palabraRep, $campo);
                        $encontro = 0;
                    }
                    $campo = (!empty($campo)) ? $campo : "&nbsp;";
                    echo "<td>$campo</td>\n\t\t\t";
                }
                echo '<td><input name="numeroExpediente" id="numeroExpediente" type="radio" value="' . $datosExpediente["SGD_EXP_NUMERO"] ."\"><td>\n\t\t";
                echo "</tr>\n";
                $contExp++;
            }
        }
?>
        </table>
    </td>
  </tr>
</table>
<table border="0" width="70%" align="center" class="borde_tab">
  <tr align="center">
    <td width="33%" height="25" class="listado2" align="center">
	  <center>
	    <input name="btnConfirmar" type="button" onClick="confirmaIncluir();" class="botones_funcion" value="Confirmar">
	  </center>
    </td>
	<td width="33%" class="listado2" height="25">
	<center><input name="cerrar" type="button" class="botones_funcion" id="envia22" onClick="opener.regresar(); window.close();" value=" Cerrar "></center></td>
	</tr>
</table>
<?php
        }
    } else if ($_POST['numeroExpediente'] != "" &&
                ($expediente->existeExpediente($numeroExpediente) === 0 ) ) {
?>
    <script language="JavaScript">
      alert( "Error. El nombre del Expediente en el que desea incluir este radicado \n\r no existe en el sistema. Por favor verifique e intente de nuevo." );
      document.getElementById( 'numeroExpediente' ).focus();
    </script>
<?php
    }
?>
</form>
</body>
</html>
