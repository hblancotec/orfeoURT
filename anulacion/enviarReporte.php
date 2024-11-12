<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit();
} else if (isset($_SESSION['krd'])) {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
$ruta_raiz = "..";
if (! $_SESSION['dependencia'] or ! $_SESSION['codusuario'])
    include_once "$ruta_raiz/rec_session.php";
// Variable para de vigencia del radicado
$vigente = true;
$expedientes = array(); // Array que almacena los expedientes
$k = 0; // Variable utilizada para recorrer el array que almacena los expedientes en que se incluyen los radicados.
$depeAnula = $dependencia;
?>
<html>
<head>
<title>Enviar Datos</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>

<style type="text/css">
<!--
.textoOpcion {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8pt;
	color: #000000;
	text-decoration: underline
}
-->
</style>

<body bgcolor="#FFFFFF" topmargin="0">

<?php
/*
 * RADICADOS SELECCIONADOS
 * @$setFiltroSelect Contiene los valores digitados por el usuario separados por coma.
 * @$filtroSelect Si SetfiltoSelect contiene algun valor la siguiente rutina
 * realiza el arreglo de la condificacion para la consulta a la base de datos y lo almacena en whereFiltro.
 * @$whereFiltro Si filtroSelect trae valor la rutina del where para este filtro es almacenado aqui.
 */
$radicadosXAnular = "";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");

if ($checkValue) {
    $num = count($checkValue);
    $i = 0;
    
    while ($i < $num) {
        $estaRad = false;
        $record_id = key($checkValue);
        
        // Consulta para verificar el estado del radicado del radicado en sancionados
        $querySancionados = "SELECT	ESTADO 
									FROM	SANCIONADOS.SAN_RESOLUCIONES 
									WHERE	nro_resol = '$record_id'";
        $rs = $db->conn->Execute($querySancionados);
        
        // Si esta el radicado
        if (! $rs->EOF) {
            $estado = $rs->fields["ESTADO"];
            if ($estado != "V") {
                $vigente = false;
            }
            $estaRad = true;
        }
        
        // Si esta el radicado entonces verificar vigencia
        if ($estaRad) {
            // Si se encuentra vigente entonces no se puede anular
            if ($vigente) {
                $arregloVigentes[] = $record_id;
            } else {
                $setFiltroSelect .= $record_id;
                $radicadosSel[] = $record_id;
                $radicadosXAnular .= "'" . $record_id . "'";
            }
        } else {
            $setFiltroSelect .= $record_id;
            $radicadosSel[] = $record_id;
        }
        
        if ($i <= ($num - 2)) {
            if (! $vigente || ! $estaRad) {
                $setFiltroSelect .= ",";
            }
            if ($estaRad && ! empty($radicadosXAnular)) {
                $radicadosXAnular .= ",";
            }
        }
        next($checkValue);
        $i ++;
        // Inicializando los valores de comprobacion
        $estaRad = false;
        $vigente = true;
    }
    if ($radicadosSel) {
        $whereFiltro = " and b.radi_nume_radi in($setFiltroSelect)";
    }
}
$systemDate = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
include '../config.php';
include_once "Anulacion.php";
include_once "$ruta_raiz/include/tx/Historico.php";
// Se vuelve crear el objeto por que saca un error con el anterior
$db = new ConnectionHandler("$ruta_raiz");
$Anulacion = new Anulacion($db);
$observa = "Solicitud Anulacion: " . $observa;

/*
 * Sentencia para consultar en sancionados el estado en que se encuentra el radicado
 * A = Anulado, V = Vigente, B = Estado temporal
 * Si el estado del radicado en sancionados es diferente de V puede realizar la sancion
 */
// Si por lo menos hay un radicado por anular
if (! empty($radicadosSel[0])) {
    $radicados = $Anulacion->solAnulacion($radicadosSel, $depeAnula, $usua_doc, $observa, $codusuario, $systemDate);
    if (! empty($radicadosXAnular)) {
        $sqlSancionados = "	UPDATE	SGD_APLMEN_APLIMENS 
								SET		SGD_APLMEN_DESDEORFEO = 2 
								WHERE	APLMEN_REF in($radicadosXAnular)";
        $rs = $db->conn->Execute($sqlSancionados);
    }
    
    $fecha_hoy = date("d-m-Y");
    $dateReplace = $db->conn->SQLDate("d-m-Y", "$fecha_hoy");
    $Historico = new Historico($db);
    
    $radicados = $Historico->insertarHistorico($radicadosSel, $depeAnula, $codusuario, $depe_codi_territorial, 1, $observa, 25);
    
    // SE OBTIENE LA CANTIDAD DE RADICADOS A ANULAR
    $cant = count($radicadosSel);
    $i = 0;
    $rad = array();
    $flag1 = 0;
    $flag2 = 0;
    
    // ## SE ASIGNA TRD DE ANULADO A LOS RADICADOS ###
    
    // CONSULTA SI EXISTE LA TRD PARA LA DEPENDENCIA
    $mrd = "	SELECT	SGD_MRD_CODIGO
					FROM	SGD_MRD_MATRIRD
					WHERE	DEPE_CODI = " . $depeAnula . "AND
							SGD_SRD_CODIGO = 999 AND
							SGD_SBRD_CODIGO = 998 AND
							SGD_TPR_CODIGO = 1366";
    $rsMrd = $db->conn->Getone($mrd);
    
    if ($rsMrd) {
        while ($i < $cant) {
            $rad[0] = $radicadosSel[$i];
            
            // CONSULTA SI EL RADICADO YA TIENE TRD
            $trdDep = "	SELECT	RADI_NUME_RADI
							FROM	SGD_RDF_RETDOCF
							WHERE	RADI_NUME_RADI = " . $rad[0];
            $rsTrdDep = $db->conn->Getone($trdDep);
            
            if ($rsTrdDep) {
                
                // ## SE ACTUALIZA TRD
                $Trd = "UPDATE	SGD_RDF_RETDOCF
							SET		SGD_MRD_CODIGO = " . $rsMrd . ",
									DEPE_CODI = " . $depeAnula . ",
									USUA_CODI = " . $_SESSION['codusuario'] . ",
									USUA_DOC = '" . $_SESSION['usua_doc'] . "',
									SGD_RDF_FECH = " . $systemDate . "
							WHERE	RADI_NUME_RADI = " . $rad[0];
                
                $codTx = 34;
            } else {
                
                // ## SE INSERTA TRD
                $codTx = 32;
                $Trd = "INSERT INTO	SGD_RDF_RETDOCF (	SGD_MRD_CODIGO,
															DEPE_CODI,
															USUA_CODI,
															USUA_DOC,
															SGD_RDF_FECH,
															RADI_NUME_RADI )
											VALUES	(	" . $rsMrd . ",
														" . $depeAnula . ",
														" . $_SESSION['codusuario'] . ",
														'" . $_SESSION['usua_doc'] . "',
														" . $systemDate . ",
														" . $rad[0] . " )";
            }
            
            $rsTrd = $db->conn->Execute($Trd);
            
            if ($rsTrd == true) {
                $flag1 = 1;
                $msg1 = "Se actualizo la TRD, correctamente";
                
                // ## SE REGISTRA EN EL HISOTRICO DEL RADICADO
                $radHist = $Historico->insertarHistorico($rad, $depeAnula, $_SESSION['codusuario'], $depeAnula, $_SESSION['codusuario'], "Se actualiza la TRD automaticamente, por anulación del radicado", $codTx);
                
                // ## SE REGISTRA EN EL HISTORICO DE TRD
                $trdHist = "INSERT INTO SGD_HMTD_HISMATDOC (	SGD_HMTD_FECHA,
																	RADI_NUME_RADI,
																	USUA_CODI,
																	SGD_HMTD_OBSE,
																	USUA_DOC,
																	DEPE_CODI,
																	SGD_TTR_CODIGO,
																	SGD_MRD_CODIGO )
													VALUES	(	" . $systemDate . ",
																" . $rad[0] . ",
																" . $_SESSION['codusuario'] . ",
																'Por anulación del radicado, se actualiza la TRD automaticamente',
																" . $_SESSION['usua_doc'] . ",
																" . $depeAnula . ",
																" . $codTx . ",
																" . $rsMrd . " )";
                $rsTrdHist = $db->conn->Execute($trdHist);
            } else {
                $msg1 = "No se pudo actualizar la TRD";
            }
            
            // #####################################################################################
            // ## SE INCLUYE EN EXPEDIENTE ANULADOS
            
            $tipoRad = substr($rad[0], - 1);
            $ano = substr($rad[0], 0, 4);
            
            // ## SI EL RADICADO ES DE SALIDA (1) SE DEBE INCLUIR EN EL EXPEDIENTE DE LA DEPENDENCIA 663
            if ($tipoRad == 1) {
                $depeAnula = 3003;
            }
            
            $exp = "SELECT	SGD_EXP_NUMERO,
								SGD_SEXP_SECUENCIA
						FROM	SGD_SEXP_SECEXPEDIENTES
						WHERE	DEPE_CODI = $depeAnula AND
								SGD_SRD_CODIGO = 999 AND
								SGD_SBRD_CODIGO = 998 AND
								SGD_SEXP_ANO = $ano AND
								SGD_SEXP_NOMBRE = 'ANULADOS' AND
								SGD_SEXP_ESTADO = 'False'";
            $rsExp = $db->conn->Execute($exp);
            
            $existeExp = $rsExp->fields['SGD_EXP_NUMERO'];
            $secExp = $rsExp->fields['SGD_SEXP_SECUENCIA'];
            
            // ## SI EL EXPEDIENTE DE ANULADOS NO ESTA CREADO, SE PROCEDE A CREARLO
            if (! $existeExp) {
                $expAct = "SELECT	sgd_sexp_ano, depe_codi, sgd_srd_codigo, sgd_sbrd_codigo, max(SGD_SEXP_SECUENCIA) as SGD_SEXP_SECUENCIA 
								FROM	SGD_SEXP_SECEXPEDIENTES
								WHERE	DEPE_CODI = $depeAnula AND
										SGD_SRD_CODIGO = 999 AND
										SGD_SBRD_CODIGO = 998 AND
										SGD_SEXP_ANO = $ano 
                                GROUP BY sgd_sexp_ano, depe_codi, sgd_srd_codigo, sgd_sbrd_codigo";
                $rsExpAct = $db->conn->Execute($expAct);
                
                $numExpAct = $rsExpAct->fields['SGD_EXP_NUMERO'];
                $secExp = $rsExpAct->fields['SGD_SEXP_SECUENCIA'];
                
                $secExp ++;
                $sec = str_pad($secExp, 5, "0", STR_PAD_LEFT);
                $numExp = $ano . $depeAnula . "999998" . $sec . "E";
                
                $newExp = "	INSERT	INTO SGD_SEXP_SECEXPEDIENTES (	SGD_EXP_NUMERO,
																		SGD_SRD_CODIGO,
																		SGD_SBRD_CODIGO, 
																		SGD_SEXP_SECUENCIA,
																		DEPE_CODI,
																		USUA_DOC, 
																		SGD_SEXP_FECH,
																		SGD_SEXP_ANO,
																		USUA_DOC_RESPONSABLE,
																		SGD_PEXP_CODIGO,
																		SGD_SEXP_PAREXP1,
																		SGD_SEXP_NOMBRE, 
																		SGD_SEXP_ESTADO)
									VALUES	(	'$numExp',
												999,
												998,
												$secExp,
												$depeAnula,
												'111111111',
												" . $systemDate . ",
												$ano,
												'111111111',
												0,
												'ANULADOS',
												'ANULADOS',
												0)";
                $rsNewExp = $db->conn->Execute($newExp);
                
                // ## SI EL EXPEDIENTE SE CREO CORRECTAMENTE, SE ASIGNA EL NUMERO DEL EXPEDIENTE CREADO A LA VARIABLE $rsExp
                if ($rsNewExp) {
                    $existeExp = $numExp;
                } else {
                    $msg2 = 'No se pudo crear el expediente para incluir el radicado';
                }
            }
            
            if ($existeExp) {
                $incExp = "INSERT INTO SGD_EXP_EXPEDIENTE (	SGD_EXP_NUMERO, 
																RADI_NUME_RADI, 
																SGD_EXP_FECH, 
																DEPE_CODI, 
																USUA_CODI, 
																USUA_DOC, 
																SGD_EXP_ESTADO
																)
												VALUES (	'" . $existeExp . "',
															" . $rad[0] . ",
															" . $systemDate . ",
															" . $depeAnula . ",
															" . $_SESSION['codusuario'] . ",
															" . $_SESSION['usua_doc'] . ",
															0)";
                $rsIncExp = $db->conn->Execute($incExp);
                
                if ($rsIncExp) {
                    $flag2 = 1;
                    $msg2 = "Se incluyo en el expediente";
                    
                    // ## SE ALMACENAN LOS DIFERENTES # DE EXPEDIENTES PARA LUEGO PINTARLOS EN EL FORMULARIO FINAL
                    if (in_array($existeExp, $expedientes)) {
                        // SI EL RADICADO YA ESTA EN EL ARREGLO, NO SE HACE NADA
                    } else {
                        $expedientes[$k] = $existeExp;
                        $k ++;
                    }
                    
                    // ## SE REGISTRA EN EL HISTORICO DEL EXPEDIENTE LA INCLUCIÓN DEL RADICADO
                    $incHisExp = "INSERT INTO SGD_HFLD_HISTFLUJODOC (	SGD_FEXP_CODIGO,
																			SGD_HFLD_FECH, 
																			SGD_EXP_NUMERO, 
																			RADI_NUME_RADI, 
																			USUA_DOC, 
																			USUA_CODI, 
																			DEPE_CODI, 
																			SGD_TTR_CODIGO, 
																			SGD_HFLD_OBSERVA
																)
												VALUES (	0,
															" . $systemDate . ",
															'" . $existeExp . "',
															" . $rad[0] . ",
															" . $_SESSION['usua_doc'] . ",
															" . $_SESSION['codusuario'] . ",
															" . $depeAnula . ",
															53,
															'Se incluye automaticamente el radicado en el Expediente de Anulados')";
                    $rsIncHistExp = $db->conn->Execute($incHisExp);
                }
            } else {
                $msg3 = "No existe el expediente de ANULADOS";
            }
            
            // ## SE ARCHIVAN LOS RADICADOS
            // $flag1=1 indica que al radicado se le asigno TRD correctamente
            // $flag2=1 indica que al radicado se incluyo en expediente
            
            if ($flag1 == 1 and $flag2 == 1) {
                
                // ## SE ACTUALIZA EL USUARIO Y DEPENDENCIA ACTUAL DEL RADICADO
                $arch = "UPDATE	RADICADO
							SET		RADI_DEPE_ACTU = 999,
									RADI_USUA_ACTU = 1,
									TDOC_CODI = 1366
							WHERE	RADI_NUME_RADI = " . $rad[0];
                $rsArch = $db->conn->Execute($arch);
                
                // ## SE REGISTRA EN EL HISOTRICO DEL RADICADO EL ARCHIVO
                $radHist = $Historico->insertarHistorico($rad, $depeAnula, $_SESSION['codusuario'], 999, 1, "Se archiva automaticamente, por anulación del radicado", 13);
                
                if ($radHist) {
                    $msg4 = "Se archivo el radicado";
                }
            }
            //next($rads);
            $i++;
        }
    } else {
        $msg = "La dependencia no tiene asignada la TRD correspondiente";
    }
}

?>

		<table border="0">
		<TR>
			<TD></TD>
		</TR>
	</table>
	<table class='borde_tab' width=60% cellpadding="0" cellspacing="5">
		<form
			action='enviardatos.php<?=session_name()."=".session_id()."&krd=$krd" ?>'
			method=post name=formulario>
			<tr>
				<td class="titulos4" colspan="3">ACCI&Oacute;N REQUERIDA COMPLETADA
				</td>
			</tr>
			<tr>
				<td class="titulos2">ACCI&Oacute;N REQUERIDA :</td>
				<td class="listado2"><span class=leidos>Solicitud de
						Anulaci&oacute;n de Radicados </span> <br> <span class=leidos>Inclusi&oacute;n en Expedientes: 
							<?php
    for ($j = 0; $j < $k; $j ++) {
        echo $expedientes[$j] . ", ";
    }
    ?>  </span> <br></td>
			</tr>
			<tr>
				<td class="titulos2">RADICADOS INVOLUCRADOS</td>
				<td class="listado2"><span class=leidos>
	
<?php
if (! empty($radicados[0])) {
    foreach ($radicados as $noRadicado) {
        echo "<br>$noRadicado";
    }
}

if (! empty($arregloVigentes[0]) && $arregloVigentes[0] != "") {
    echo '<p>
				<font color="red">
					Lista de Radicados que No se pueden Anular ya que se encuentran vigentes en sancionados
				</font>
			  </p>';
    
    echo '<font color="red">';
    
    foreach ($arregloVigentes as $radicado) {
        echo "<br>$radicado";
    }
    echo '</font>';
}
?>

						</span></td>
			</tr>
			<tr>
				<td class="titulos2">USUARIO DESTINO:</td>
				<td class="listado2"><span class=leidos> DSALIDA (Archivo) </span></td>
			</tr>
			<tr>
				<td class="titulos2">FECHA Y HORA</td>
				<td class="listado2"><span class=leidos> <?=date("d-m-Y h:i:s")?> </span>
				</td>
			</tr>
			<tr>
				<TD width=30% class="titulos2">USUARIO ORIGEN</TD>
				<td class="listado2"><span class=leidos><?=$usua_nomb?></span></td>
			</tr>
			<tr>
				<td class="titulos2">DEPENDENCIA ORIGEN</td>
				<td class="listado2"><span class=leidos><?=$depe_nomb?></span></TD>
			</tr>
		</form>
	</table>
</body>
</HTML>
