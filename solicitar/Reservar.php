<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "../sinacceso.php");
    exit;
}
else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

$ruta_raiz = "..";
$verrad = "";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$db->conn->debug=false;

$hoy = date('d-m-Y');

## SE INVOCA LA FUNCION SUMADIASHABILES, PARA DETERMINAR LA FECHA DE VENCIMIENTO QUE SON 8 DIAS HABILES
$sqlFec	= "SELECT dbo.sumadiashabiles('$hoy', 8)";
$diasTermino8	= $db->conn->getone($sqlFec);


## FECHA DE VENCIMIENTO CUANDO EL PRESTAMO ES INDEFINIDO ()
$diasTermino10anos = "";


/* * ******************************false*************************************************
 *       Filename: Reservar.php
 *       Modificado: 
 *          1/3/2006  IIAC  Facilita la interfaz para que el usuario cancele o
 *                          solicite un documento fisico y realiza las 
 *                          actualizaciones relacionadas con el modulo de 
 *                          prestamos en la base de datos.
 * ******************************************************************************* */
// Reservar CustomIncludes begin
foreach ($_POST as $nombre_campo => $valor) {
    if (substr($nombre_campo, 0, 5) == "Hojas") {
        if ($valor)
            $noHojas[str_replace("Hojas_", "", $nombre_campo)] = $valor;
        $radicadosPP[] = str_replace("Hojas_", "", $nombre_campo); // trae los Numeros de radicados  
    }
}

include "common.php";
// Save Page and File Name available into variables
$sFileName = "Reservar.php";
// Save the name of the form and type of action into the variables
$sAction = get_param("FormAction");
//$sAction = get_param("Accion");
$sPRESTAMOErr = ""; //Mensaje de error
PRESTAMO_action($sAction); //insert, cancel, update
?>
<html>
    <head>
        <title>Pr&eacute;stamos ORFEO</title>
        <link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
    </head>
    <body class="PageBODY">
        <table align="center">
        	<?php if ($sAction == "modifica") {
        	?>
        	<tr>
        	    <td valign="top">
        	<?php 
        	   echo "<script> alert(\" Se registr\u00F3 el cambio del pr\u00E9stamo\"); </script>";
        	   echo ".."; // dejar esto para que el navegador deje hacer el submit
        	   echo "<form name='Atras' action='../prestamo/menu_prestamo.php?krd=$krd' method='post'> </form>";
        	   echo "<script>document.Atras.submit();</script>";
        	?></td>
            </tr>
        	<?php } else {
        	?>
            <tr>
                <td valign="top"><?php ESTADO_PRESTAMO_show() ?></td>
            </tr>
            <tr>
                <td valign="top"><?php PRESTAMO_show($estadoTx) ?></td>
            </tr>
            <?php 
        	}
            ?>
        </table>
    </body>
</html>
<?php

function verMensaje($nombTx, $fecha) {
    global $sPRESTAMOErr;
    global $usua_nomb;
    global $depe_nomb;
    if (isset($_GET["radicado"])) {
        $nomRad = $_GET["radicado"];
    } else {
        $nomRad = $_POST["radicado"];
    }
    
    if (strlen($nomRad) > 17) {
        $nomRad = str_replace(",", "<br>", $nomRad);
    }
    //para que solo se haga visible esta funcion
    $sPRESTAMOErr = "no presentar";
    ?>
    <table border="0" cellspace="2" cellpad="2" WIDTH="50%"  class="t_bordeGris" id="tb_general" align="left">
        <tr>
            <td colspan="2" class="titulos4">ACCI&Oacute;N REQUERIDA COMPLETADA</td>
        </tr>
        <tr>
            <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">ACCI&Oacute;N REQUERIDA:</td>
            <td  width="65%" height="25" class="listado2_no_identa"><?= $nombTx ?></td>
        </tr>
        <tr>
            <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">RADICADOS INVOLUCRADOS:</td>
            <td  width="65%" height="25" class="listado2_no_identa"><?= $nomRad ?></td>
        </tr>
        <tr>
            <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">FECHA:</td>
            <td  width="65%" height="25" class="listado2_no_identa"><?= $fecha ?></td>
        </tr>	  
        <tr>
            <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">USUARIO ORIGEN:</td>
            <td  width="65%" height="25" class="listado2_no_identa"><?= $usua_nomb ?></td>
        </tr>
        <tr>
            <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">DEPENDENCIA ORIGEN:</td>
            <td  width="65%" height="25" class="listado2_no_identa"><?= $depe_nomb ?></td>
        </tr>	
    </table>
    <?php
}

// PRESTAMO_action begin
// Actualiza la base de datos con las acciones realiadas (solicitar,prestar,etc.)
function PRESTAMO_action($sAction) {
    global $db;
    global $krd; //usuario actual
    global $dependencia; //dependencia del usuario actual
    global $noHojas;
    global $radicadosPP;
    global $diasTermino10anos;
    global $diasTermino8;
    global $diasTermino3;
    global $Accion;
    $nomRad = get_param("radicado");
    if (strlen($nomRad) > 17) {
        $nomRad = str_replace(",", "<br>", $nomRad);
    }

    $fldradicado = get_param("radicado");
    $solicitarDocs = $_POST["solicitarDocs"];
    include_once("../include/tx/Historico.php");
    $hist = new Historico($db);
    
    $condicion = "";
    if ($sAction == "devolucion") {
        $condicion = " and PRES_ESTADO in (2, 5, 6 ,7) ";
    }
    
    
    $sqlEstado = "SELECT p.RADI_NUME_RADI, PRES_ESTADO
				    FROM prestamo P
                    WHERE p.PRES_FECH_DEVO is null and p.radi_nume_radi in (" . trim($fldradicado) . ") $condicion
                    order by p.PRES_FECH_PEDI desc";
    $rsEst = $db->conn->Execute($sqlEstado);
    if ($rsEst && !$rsEst->EOF) {
        $estadoActual = $rsEst->fields["PRES_ESTADO"];
    } else {
        $estadoActual = 0;
    }
    
    /* * *******************************************************************************
     *      ESTADOS PRESTAMO
     *      1 - SOLICITADO
     *      2 - PRESTADO DEFINIDO
     *      3 - DEVUELTO
     *      4 - SOLICITUD CANCELADA
     *      5 - PRESTADO INDEFINIDO (No se usa)
     *      6 - PRESTADO PARA ARCHIVO DEP
     *      7 - PRESTADO PARA TRASLADO
     * ******************************************************************************* */
    
    // Regresa al menu del radicado
    if ($sAction == "cancelar") {
        if ($estadoActual == 1 || $estadoActual == 5) {
            echo ".."; // dejar esto para que el navegador deje hacer el submit
            echo "<form name='Atras' action='../verradicado.php?krd=$krd&verrad=$fldradicado' method='post'> </form>";
            echo "<script>document.Atras.submit();</script>";
        } else {
            echo "<script> alert(\"El registro no esta solicitado en prastamo\"); </script>";
        }
    } 
	elseif ($sAction == "insert") {  // Registro de una nueva solicitud
	    
	    if ($estadoActual == 0 || $estadoActual == 3 || $estadoActual == 4) {
	        
	    
        $tipoPrestamo = get_param("tipoPrestamo");
        if ($tipoPrestamo == 1){
            $fldPRES_FECH_PEDI = "";
		}
        $fldPRES_FECH_PEDI = date('d-m-Y'); //$db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
        
		// Obtiene la ubicacioen fisica de los documentos	  
        $fldPRES_DEPE_ARCH = substr($fldradicado, 4, 4);
        $query = "	SELECT	UBIC_DEPE_ARCH
                    FROM	UBICACION_FISICA
                    WHERE	UBIC_DEPE_RADI=" . $fldPRES_DEPE_ARCH;
        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $db->conn->Execute($query);
        if (!$rs->EOF) {
            $fldPRES_DEPE_ARCH = $rs->fields['UBIC_DEPE_ARCH'];
        }
        if ($Accion == "Renovar") {
            $estadoPrestamoR = 2;
            $codTx = 73;
        } else {
            $estadoPrestamo = 1;
            $codTx = 72;
        }

        if (strlen($solicitarDocs) >= 10) {
            
            if (strlen($solicitarDocs) >= 19) {
                $sqlExpediente = "SELECT DEPE_CODI FROM SGD_SEXP_SECEXPEDIENTES WHERE SGD_EXP_NUMERO = '$solicitarDocs'";
                $rsExp = $db->conn->Execute($sqlExpediente);
                if ($rsExp && !$rsExp->EOF) {
                    $depeCodi = $rsExp->fields['DEPE_CODI'];
                    if ($dependencia <> $depeCodi) {
                        echo "<table class=borde_tab width=100% align=center>
        				        <tr><td class=titulos2><FONT COLOR=RED SIZE=4>";
                        echo "El expediente es de otra dependencia por favor dir&iacute;gase al grupo de Biblioteca y Archivo
                                para que la solicitud sea escalada al jefe del &aacute;rea.</td><td class=listado2>
                                </FONT></td></tr></table>";
                        return;
                    }
                }
            }
        
            $docsYaPrestados = "";
            $sqlPrestamo = "SELECT	exp.RADI_NUME_RADI 
							FROM	sgd_exp_expediente exp
							WHERE	exp.sgd_exp_numero = '" . trim($solicitarDocs) . "'";
            
            $rsP = $db->conn->Execute($sqlPrestamo);
            while (!$rsP->EOF) {
                $radicadoP = $rsP->fields["RADI_NUME_RADI"];
                if ($Accion == "Renovar") {
                    $estadoPrestamoR = 2;
                    $codTx = 76;
                } else {
                    /*$estadoPrestamoR = '1,3';
                    if ($tipoPrestamo == 1)
                        $estadoPrestamoR = '1';*/
                    $codTx = 72;
                }
                $estadoPrestamoR = '1,5,2,6';
                $iK = 0;
                $ij = 0;
                $sqlPrestamo = "SELECT	p.RADI_NUME_RADI, 
										PRES_ESTADO 
								FROM	prestamo P
                                WHERE	p.radi_nume_radi = " . trim($radicadoP) . " AND 
										p.PRES_ESTADO IN($estadoPrestamoR)";         
                $rsPres = $db->conn->Execute($sqlPrestamo);
                if ($rsPres && !$rsPres->EOF) {
                    if ($rsPres->fields['PRES_ESTADO'] == 2 || $rsPres->fields['PRES_ESTADO'] == 6) {
                        $iK++;
                        if ($iK == 7) {
                            $separador = "<br>";
                            $iK = 0;
                        } else {
                            $separador = ", ";
                        }
                        $docsYaPrestados .= $radicadoP . "$separador ";
                    } else {
                        $ij++;
                        if ($ij == 7) {
                            $separador1 = "<br>";
                            $ij = 0;
                        } else {
                            $separador1 = ", ";
                        }
                        $docsYaSolicitados .= $radicadoP . "$separador1 ";
                    }
                } else {
                    // Genera PRES_ID
                    $sec = $db->conn->nextId('SEC_PRESTAMO');
                    $usuaDoc = $_SESSION['usua_doc'];
                    
                    $fechaDev = "";
                    if ($_POST["tipoPrestamo"] == 1) {
                        $fechaDev = $db->conn->OffsetDate(10);
						$presTipo = 1;
                        $observaAdd = "Prestamo por tiempo definido";
                    } else {
                        $observaAdd = "Prestamo por tiempo indefinido";
                        $fldPRES_REQUERIMIENTO = "Para Archivo en Dep.";
						$presTipo = 5;
                    }
                    if ($Accion != "Renovar") {
                        $numHojas = $noHojas[$radicadoP];
                        if (!$numHojas)
                            $numHojas = 0;
                        if ($_POST["tipoPrestamo"] == 1) {
                            $fechaDev = $db->conn->OffsetDate(10);
							$presTipo = 1;
                            $observaAdd = "Prestamo por tiempo definido";
                        } else {
                            $observaAdd = "Prestamo por tiempo indefinido";
                            $fldPRES_REQUERIMIENTO = "Para Archivo en Dep.";
							$presTipo = 5;
                        }

                        $sSQL = "INSERT INTO PRESTAMO(	PRES_ID,
														RADI_NUME_RADI,
														USUA_LOGIN_ACTU,
														DEPE_CODI,
														PRES_FECH_PEDI,				  
														PRES_DEPE_ARCH,
														PRES_ESTADO,
														PRES_REQUERIMIENTO,
														USUA_DOC,
														PRES_FECH_VENC,
														PRES_HOJAS )
                                VALUES (" .	tosql($sec, "Number") . "," .
											tosql($radicadoP, "Text") . "," .
											tosql($krd, "Text") . "," .
											tosql($dependencia, "Number") . "," .
											$fldPRES_FECH_PEDI . "," .
											tosql($fldPRES_DEPE_ARCH, "Number") . ",
											" . $presTipo . ", 
											1,"
											. $usuaDoc . ",'" .
											"$fechaDev" . "'," .
											$numHojas . ")";
                        $observa = "Solicitud Expediente $solicitarDocs $observaAdd";
                    } else {
                        
                        $sSQL = "	UPDATE 	PRESTAMO
									SET 	PRES_FECH_VENC = '$diasTermino8'
											$sqlHojas
									WHERE 	RADI_NUME_RADI=$radicadoP
											AND PRES_ESTADO=2";
                        $observa = "Renovacion $solicitarDocs $observaAdd";
                        $codTx = 76;
                    }
                    // Execute SQL statement
                    
                    if ($_POST["HOJAS_" . $radicadoP]) {
                        $numHojas = $noHojas[$radicadoP];
                        $sqlHojas .= " , PRES_HOJAS = $numHojas ";
                        $observa .=" (" . $_POST["HOJAS_" . $radicadoP] . " Folios)";
                    } else {
                        $sqlHojas = "";
                    }
                    if ($db->conn->Execute($sSQL)) {
                        $codUsOrigen = $_SESSION["codusuario"];
                        $codUsDestino = $codUsOrigen;
                        $depDestino = $dependencia;
                        $radicadosP[0] = $radicadoP;
                        $observa = iconv('iso-8859-1', 'utf-8', $observa);
                        $hist->insertarHistorico($radicadosP, $dependencia, $codUsOrigen, $depDestino, $codUsDestino, $observa, $codTx);
                    } 
					else {
                        echo "<script> alert(\"El registro no pudo ser renovado\"); </script>";
                    }
                }

                $rsP->MoveNext();
            }
			if ($docsYaPrestados) {
				echo "<table class=borde_tab width=100%>
				        <tr><td class=titulos2>";
                echo "Estos Documentos ya se encuentran Prestado<br>
                        Por lo tanto se excluyen de esta Solicitud.:</td><td class=listado2> <FONT COLOR=RED>" . $docsYaPrestados .
                       "</FONT></td></tr></table>";
            }
            if ($docsYaSolicitados) {
                echo "<table class=borde_tab width=100%>
    				        <tr><td class=titulos2>";
                echo "Estos Documentos ya se encuentran Solicitados<br>
                            Por lo tanto se excluyen de esta Solicitud.:</td><td class=listado2> <FONT COLOR=RED>" . $docsYaSolicitados .
                            "</FONT></td></tr></table>";
            }
        } 

            // Genera PRES_ID
            $sec = $db->conn->nextId('SEC_PRESTAMO');
            $usuaDoc = $_SESSION['usua_doc'];
        
            if ($Accion != "Renovar") {
                //if ($_POST["tipoPrestamo"] == 1) {
                    $fechaDev = $diasTermino8;
                    $observaAdd = "de Prestamo";
					$presTipo = 1;
                /*} 
				else {
                    $observaAdd = "de Préstamo para archivo de la dependencia";
                    $fldPRES_REQUERIMIENTO = 5;
                    $fechaDev = $diasTermino10anos;
					$presTipo = 5;
                }*/
                $numHojas = $noHojas[$radicadoP];
                if (!$numHojas)
                    $numHojas = 0;
                $fldPRES_REQUERIMIENTO = 1;
                $sSQL = "INSERT INTO PRESTAMO(	PRES_ID,
												RADI_NUME_RADI,
												USUA_LOGIN_ACTU,
												DEPE_CODI,
												PRES_FECH_PEDI,				  
												PRES_DEPE_ARCH,
												PRES_ESTADO,
												PRES_REQUERIMIENTO,
												USUA_DOC,
												PRES_FECH_VENC,
												PRES_HOJAS)
								VALUES (" .	tosql($sec, "Number") . "," .
											tosql($fldradicado, "Text") . "," .
											tosql($krd, "Text") . "," .
											tosql($dependencia, "Number") . "," .
											$fldPRES_FECH_PEDI . "," .
											tosql($fldPRES_DEPE_ARCH, "Number") . ",". 
											$presTipo . "," .
											tosql($fldPRES_REQUERIMIENTO, "Number") . ",'"
											. $usuaDoc . "','"
											. $fechaDev . "',"
											. $numHojas . ")";
            }else {
                if ($numHojas) {
                    $numHojas = $noHojas[$fldradicado];
                    $sqlHojas .= " , PRES_HOJAS = $numHojas ";
                } else {
                    $sqlHojas = "";
                }

                $sSQL = "	UPDATE 	PRESTAMO
							SET 	PRES_FECH_VENC = $diasTermino8
									$sqlHojas
							WHERE 	RADI_NUME_RADI=$fldradicado
									AND PRES_ESTADO=2";
				$codTx = 76;
                $observa = "Renovacion $observaAdd";
            }
            // Execute SQL statement  
            if ($db->conn->Execute($sSQL)) {
                $observa = "Solicitud $observaAdd";
                if ($numHojas) {
                    $observa .= " (Folios $numHojas)";
				}	
				$observa = iconv('iso-8859-1', 'utf-8', $observa);
                $codUsOrigen = $_SESSION["codusuario"];
                $codUsDestino = $codUsOrigen;
                $depDestino = $dependencia;
                $radicadosP[0] = $fldradicado;
                $hist->insertarHistorico($radicadosP, $dependencia, $codUsOrigen, $depDestino, $codUsDestino, $observa, $codTx);
            } else {
                echo "<script> alert(\"El registro no pudo ser renovado \"); </script>";
            }
	    }
	    else {
	        echo "<script> alert(\"El radicado no puede ser solicitado\"); </script>";
	    }
    } 
	elseif ($sAction == "prestamo" || $sAction == "prestamoIndefinido" || $sAction == "delete" || $sAction == "devolucion") {
        // Cancelacion, prestamo o devolucion de un documento
        //elseif ($sAction=="prestamo" || $sAction=="prestamoIndefinido" || $sAction=="delete" || $sAction=="devolucion") {    tipoPrestamo
        // Inicializa parametros para SQL
	    $valida = false;
        $fldPRES_FECH = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
        $fldPRES_ID = get_param("s_PRES_ID");
        $sfldPRES_ID = str_replace("'", "", "" . tosql($fldPRES_ID, "Text"));  // identifiador de los registros	         
        $estadoOld = "=1";				
			// Prestamo  
        if ($sAction == "prestamoIndefinido" || $sAction == "prestamo") {
            
            if ($estadoActual == 1 || $estadoActual == 5) {
                $fldDESC = tosql(get_param("observa"), "Text");
                $setFecha = "PRES_FECH_PRES=" . $fldPRES_FECH . ", PRES_DESC=" . $fldDESC . ", USUA_LOGIN_PRES='" . $krd . "' ";
                $nombTx = "Prestar Documento";
                $codTx = 73;
                if ($_POST['tipoPrestamo'] == "2") {
                    $estadoNew = 6;
                    $titError = "El registro del prestamo indefinido no pudo ser realizado";
                    $observa = "Prestado para archivo de la dependencia: ". $_POST['observa'];
                } elseif ($_POST['tipoPrestamo'] == "1") {
                    $estadoNew = 2;
                    $sqlFechaVenc = $diasTermino8;
                    $setFecha.=",PRES_FECH_VENC = '" . $sqlFechaVenc . "' ";
                    $observa = "Prestado por tiempo definido: ". $_POST['observa'];
                    $titError = "El registro del prestamo no pudo ser realizado";
                    $valida = true;
                } elseif ($_POST['tipoPrestamo'] == "3") {
                    $estadoNew = 7;
                    $titError = "El registro del prestamo para traslado no pudo ser realizado";
                    $observa = "Prestado para traslado: ". $_POST['observa'];
                }
                $radicadosP = $radicadosPP;
                /*if ($sAction == "prestamoIndefinido") {
                 $estadoNew = 6;
                 $titError = "El registro del pr&eacute;stamo indefinido no pudo ser realizado";
                 $observa = "Préstado para archivo de la dependencia: ". $_POST['observa'];
                 } else {
                 $estadoNew = 2;
                 $sqlFechaVenc = $diasTermino8;
                 $setFecha.=",PRES_FECH_VENC = '" . $sqlFechaVenc . "' ";
                 $observa = "Préstado por tiempo definido: ". $_POST['observa'];
                 $titError = "El registro del pr&eacute;stamo no pudo ser realizado";
                 $valida = true;
                 }*/
            } else {
                echo "<script> alert(\"El radicado no est\u00E1 solicitado en prestamo\"); </script>";
            }
        }
			// Cancelacion de solicitud	  
			elseif ($sAction == "delete") {
			    if ($estadoActual == 1 || $estadoActual == 5) {
			        $fldDESC = tosql(get_param("observa"), "Text");
			        $fldDESC = "Cancelacion realizada por el Usuario.";
			        $estadoNew = 4;
			        $setFecha = "PRES_FECH_CANC=" . $fldPRES_FECH . ", USUA_LOGIN_CANC='" . $krd . "' ";
			        $nombTx = "Cancelar Solicitud de Pr&eacute;stamo";
			        $codTx = 74;
			        $fldradicado = get_param("radicado");
			        $radicadosP =  explode(",", $fldradicado);
			        $titError = "El registro de la cancelacion no pudo ser realizado";
			        $observa = $nombTx;
			    } else {
			        echo "<script> alert(\"El radicado no est\u00E1 solicitado en prestamo\"); </script>";
			    }
			}

			// Devolucion	  
			elseif ($sAction == "devolucion") {
			    if ($estadoActual == 2 || $estadoActual == 5 || $estadoActual == 6 || $estadoActual == 7) {
			        $estadoNew = 3;
			        $fldDESC = tosql(get_param("observa"), "Text");
			        $setFecha = "PRES_FECH_DEVO=" . $fldPRES_FECH . ", DEV_DESC=" . $fldDESC . ", USUA_LOGIN_RX='" . $krd . "' ";
			        $nombTx = "Devolver Documento";
			        $titError = "El registro de la devolucion no pudo ser realizado";
			        $estadoOld = "in (2,5)";
			        $fldradicado = get_param("radicado");
			        $radicadosP = $radicadosPP;
			        $fldDESC = str_replace("'", "", $fldDESC);
			        $codTx = 75;
			        $observa = $nombTx . " : " . $fldDESC;
			    } else {
			        echo "<script> alert(\"El radicado no est\u00E1 prestado\"); </script>";
			    }
			}
			$fecha = date("d-m-Y  h:i A");
			// Create SQL statement
			$sSQL = "	UPDATE	PRESTAMO 
						SET		" . $setFecha . ",PRES_ESTADO=" . $estadoNew . " 
						WHERE	PRES_ID in (" . $sfldPRES_ID . ") "; // and PRES_ESTADO ".$estadoOld;			   
			// Execute SQL statement
			if ($db->conn->query($sSQL)) {

				if ($noHojas) {
					unset($radicadosP);
					foreach ($noHojas as $noRadicado => $valor) {
						if ($valor >= 1) {
							if ($valor >= 1) {
								$pDesc = $db->conn->Concat("PRES_DESC", "'(Folios $valor)'");
								$sqlHojas = ", PRES_DESC=$pDesc";    
							}
							else {
								$sqlHojas = "";
							}	
							$sSQL = "	UPDATE	PRESTAMO 
										SET		PRES_HOJAS=$valor
												$sqlHojas
										WHERE	RADI_NUME_RADI=$noRadicado AND 
												PRES_ID in (" . $sfldPRES_ID . ") ";

							if ($sAction == "devolucion" or $sAction == "prestamo" or $sAction == "prestamoIndefinido") {
								$db->conn->Execute($sSQL);
							}

							if ($noRadicado)
								$radicadosP[] = $noRadicado;
						}
					}
				}
				if ($codTx) {
					//$observa = str_replace("'", "", $fldDESC);
					$codUsOrigen = $_SESSION["codusuario"];
					$codUsDestino = $codUsOrigen;
					$depDestino = $dependencia;
					$observa = iconv('iso-8859-1', 'utf-8', $observa);
					$hist->insertarHistorico($radicadosP, $dependencia, $codUsOrigen, $depDestino, $codUsDestino, $observa, $codTx);
					
					if ($valida) {
					    require '../class_control/correoElectronico.php';
					    $objMail = new correoElectronico("..", false, true);
					    
					    $isql = "SELECT P.RADI_NUME_RADI, P.PRES_FECH_PRES, P.PRES_FECH_VENC, U.USUA_EMAIL, U.USUA_NOMB, D.DEPE_NOMB
                                FROM PRESTAMO P INNER JOIN USUARIO U ON P.USUA_LOGIN_ACTU = U.USUA_LOGIN AND P.DEPE_CODI = U.DEPE_CODI
	                            INNER JOIN DEPENDENCIA D ON D.DEPE_CODI = U.DEPE_CODI
                                WHERE PRES_ID = $sfldPRES_ID ";
					    $rsUsu = $db->query($isql);
					    if ($rsUsu) {
					        while (! $rsUsu->EOF) {
					            $usuaEmail = $rsUsu->fields["USUA_EMAIL"];
					            $usuaNomb = $rsUsu->fields["USUA_NOMB"];
					            $radicado = $rsUsu->fields["RADI_NUME_RADI"];
					            $fechPres = $rsUsu->fields["PRES_FECH_PRES"];
					            $fechVenc = $rsUsu->fields["PRES_FECH_VENC"];
					            
					            try {
					                //$objMail->FromName = "Notificaciones DNP (Orfeo)";
					                
					                $para = array($usuaNomb => $usuaEmail);
					                // $cc = array('jzabala@dnp.gov.co');
					                
					                $asunto = "Notificación del Departamento Nacional de Planeacional DNP para trámite";
					                $cuerpo = "<table width='80%'>
                                                	<tr>
                                                		<td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td>
                                                		<td><b>Comunicaci&oacute;n Oficial.</b></td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>&nbsp;</td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>El Sistema de Gesti&oacute;n Doumental ORFEO notifica que:</td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>&nbsp;</td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2'>Señor $usuaNomb : actualmente el radicado Nro. $radicado (Documento Físico) se encuentra prestado y esta a su nombre.</td>
                                                	</tr>
                                                    <tr>
                                                		<td colspan='2'>El documento fue prestado el día: $fechPres.</td>
                                                	</tr>
                                                    <tr>
                                                		<td colspan='2'>El préstamo vence (io) el día: $fechVenc.</td>
                                                	</tr>
                                                    <tr>
                                                		<td colspan='2' align='center'><b>Por favor cerciórese de devolver el documento al grupo de Archivo o renueve el préstamo del documento</b></td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2' align='center'><b>&nbsp;</b></td>
                                                	</tr>
                                                	<tr>
                                                		<td colspan='2' align='center'><b>DNP</b></td>
                                                	</tr>
                                                </table>";
					                $objMail->Body = "";
					                //if ($objMail->enviarCorreo($para, $cc, $cco, $asunto, $cuerpo))
					                    echo "<span class=titulosError> Se envió notificación al usuario: " . $usuaNomb . " para el radicado " . $radicado . "</span></br>";
					                //    else
					                //        echo "No se envió la notificación " . "</br>";
					            } catch (Exception $e) {
					                echo 'Excepcion capturada: ', $e->getMessage(), "\n";
					            }
					            
					            $rsUsu->MoveNext();
					        }
					        $rsUsu->Close();
					    }
					}
				}
				verMensaje($nombTx, $fecha);
			} 
			else {
				echo "<script> alert(" . $titError . "); </script>";
			}	
    }
    elseif ($sAction == "modifica") {
        
        $valida = false;
        $fldPRES_FECH = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);
        $fldPRES_ID = get_param("s_PRES_ID");
        $sfldPRES_ID = str_replace("'", "", "" . tosql($fldPRES_ID, "Text"));  // identifiador de los registros
        $pobserva = iconv('utf-8', 'iso-8859-1', $_POST['observa']);
        
        $fldDESC = tosql(get_param("observa"), "Text");
        $setFecha = "PRES_FECH_PRES = GETDATE(), PRES_DESC =" . $fldDESC . ", USUA_LOGIN_PRES ='" . $krd . "' ";
        $nombTx = "Modificación prestamo";
        $titError = "El registro de la devolucion no pudo ser realizado";
        $fldradicado = get_param("radicado");
        $fldDESC = str_replace("'", "", $fldDESC);
        $observa = $nombTx . " : " . $fldDESC;
        
        if ($_POST['tipoPrestamo'] == "2") {
            $estadoNew = 6;
            $titError = "El registro del pr&eacute;stamo indefinido no pudo ser realizado";
            $observa = "Préstado para archivo de la dependencia: ". $pobserva;
        } elseif ($_POST['tipoPrestamo'] == "1") {
            $estadoNew = 2;
            $sqlFechaVenc = $diasTermino8;
            $setFecha.=",PRES_FECH_VENC = '" . $sqlFechaVenc . "' ";
            $observa = "Prestado por tiempo definido: ". $pobserva;
            $titError = "El registro del pr&eacute;stamo no pudo ser realizado";
            $valida = true;
        } elseif ($_POST['tipoPrestamo'] == "3") {
            $estadoNew = 7;
            $titError = "El registro del pr&eacute;stamo para traslado no pudo ser realizado";
            $observa = "Prestado para traslado: ". $pobserva;
        } 
        $fldPRES_REQUERIMIENTO = "Para Archivo en Dep.";
                  
        $sSQL = "UPDATE PRESTAMO
		        SET  $setFecha , PRES_ESTADO = $estadoNew
				WHERE PRES_ID in (" . $sfldPRES_ID . ") ";
        $exec = $db->conn->Execute($sSQL);
        
        if ($exec) {
            $codTx = 79;
            $codUsOrigen = $_SESSION["codusuario"];
            $codUsDestino = $codUsOrigen;
            $depDestino = $dependencia;
            $observa = iconv('iso-8859-1', 'utf-8', $observa);
            $hist->insertarHistorico($radicadosPP, $dependencia, $codUsOrigen, $depDestino, $codUsDestino, $observa, $codTx);
            
        } else {
            echo "<script> alert(\"El registro no pudo ser modificado\"); </script>";
        }
    }
}

// ESTADO_PRESTAMO_show begin
// Presenta el estado del documento y sus anexos.
function ESTADO_PRESTAMO_show() {
    global $db;
    global $sFileName;
    global $krd;
    global $sPRESTAMOErr;

    $fldradicado = get_param("radicado");
    include_once("../include/query/busqueda/busquedaPiloto1.php");
    if ($sPRESTAMOErr == "") {
        // Build SQL statement   
        $sqlPRES_FECH_PEDI = $db->conn->SQLDate("d-m-Y H:i A", "r.PRES_FECH_PEDI");
        $sqlPRES_FECH_CANC = $db->conn->SQLDate("d-m-Y H:i A", "r.PRES_FECH_CANC");
        $sqlPRES_FECH_DEVO = $db->conn->SQLDate("d-m-Y H:i A", "r.PRES_FECH_DEVO");
        $sqlPRES_FECH_PRES = $db->conn->SQLDate("d-m-Y H:i A", "r.PRES_FECH_PRES");
        $sqlPRES_FECH_VENC = $db->conn->SQLDate("d-m-Y H:i A", "r.PRES_FECH_VENC");
        $solicitarDocs = $_POST["solicitarDocs"];
        if (strlen($solicitarDocs) >= 10) {
            $sSQL = "SELECT	r.PRES_ID as PRESTAMO_ID,
							$radi_nume_radi as RADICADO,
							r.USUA_LOGIN_ACTU as LOGIN,
							D.DEPE_NOMB as DEPENDENCIA," .
							$sqlPRES_FECH_PEDI . " as F_SOLICITUD," .
							$sqlPRES_FECH_VENC . " as F_VENCIMIENTO," .
							$sqlPRES_FECH_CANC . " as F_CANCELACION," .
							$sqlPRES_FECH_PRES . " as F_PRESTAMO," .
							$sqlPRES_FECH_DEVO . " as F_DEVOLUCION,
							E.PARAM_VALOR as ESTADO,
							r.PRES_ESTADO as ID_ESTADO,
							(select top 1 ep.PARAM_VALOR from SGD_PARAMETRO EP WHERE ep.param_codi=r.pres_requerimiento ) as TIPO_REQUERIMIENTO
					FROM	PRESTAMO r,
							DEPENDENCIA D,
							SGD_PARAMETRO E,
							SGD_EXP_EXPEDIENTE exp
					WHERE	exp.SGD_EXP_NUMERO='" . $solicitarDocs . "' and
							exp.RADI_NUME_RADI=r.RADI_NUME_RADI and
							exp.SGD_EXP_ESTADO<>2 and
							r.PRES_ESTADO in (1,2,5) and
							D.DEPE_CODI=R.DEPE_CODI and
							E.PARAM_NOMB='PRESTAMO_ESTADO' and
							E.PARAM_CODI=R.PRES_ESTADO  ";
        } 
		else {
		    $sSQL = "	SELECT	r.PRES_ID as PRESTAMO_ID,
								$radi_nume_radi as RADICADO,
								r.USUA_LOGIN_ACTU as LOGIN,
								D.DEPE_NOMB as DEPENDENCIA," .
								$sqlPRES_FECH_PEDI . " as F_SOLICITUD," .
								$sqlPRES_FECH_VENC . " as F_VENCIMIENTO," .
								$sqlPRES_FECH_CANC . " as F_CANCELACION," .
								$sqlPRES_FECH_PRES . " as F_PRESTAMO," .
								$sqlPRES_FECH_DEVO . " as F_DEVOLUCION,
								E.PARAM_VALOR as ESTADO,
								r.PRES_ESTADO as ID_ESTADO,
								(select top 1 ep.PARAM_VALOR from SGD_PARAMETRO EP WHERE ep.param_codi=r.pres_requerimiento ) as TIPO_REQUERIMIENTO
						FROM	PRESTAMO r,
								DEPENDENCIA D,
								SGD_PARAMETRO E
						WHERE	r.RADI_NUME_RADI=$fldradicado and
								r.PRES_ESTADO in (1,2,5) and
								D.DEPE_CODI=R.DEPE_CODI and
								E.PARAM_NOMB='PRESTAMO_ESTADO' and
								E.PARAM_CODI=R.PRES_ESTADO  ";
        }
        // Execute SQL statement	    
        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $db->conn->Execute($sSQL);
        // Process empty recordset
        if ($rs && !$rs->EOF) {
            ?>   
            <script>
                /*Adecua el formulario para que se cancele la solicitud*/					 
                function cancelar(i) {
                    document.Prestados.FormAction.value="delete";						
                    document.Prestados.s_PRES_ID.value=i;												
                    document.Prestados.submit();
                }
            </script>					    
            <form method="POST" action="<?= $sFileName ?>" name="Prestados">
                <input type="hidden"  value='<?= $krd ?>' name="krd">
                <input type="hidden" value="cancelar" name="FormAction">
                <input type="hidden" value="" name="s_PRES_ID">					 					 		 
                <input type="hidden" value="<?= $fldradicado ?>" name="radicado">  
                <table border=0 cellpadding=0 cellspacing=2 class='borde_tab'>
                    <tr>
                        <td class="titulos2" colspan="8">Estado de Reservas <font class="menu_princ"><?= $fldradicado ?>
            <?php
            if (strlen($solicitarDocs) >= 10) {
                echo "Expediente : " . $_POST["solicitarDocs"];
            }
            ?>
                            </font></td>
                    </tr>
                    <tr class="titulos3" align="center" valign="middle">
                        <td><a href=''><font class="titulos3">Radicado</font></a></td>		 
                        <td><a href=''><font class="titulos3">Login</font></a></td>		 
                        <td><a href=''><font class="titulos3">Dependencia</font></a></td>		 
                        <td><a href=''><font class="titulos3">Fecha<br>Solicitud</font></a></td>		 
                        <!-- <td><a href=''><font class="titulos3">Fecha<br>Vencimiento</font></a></td>		 
                        <td><a href=''><font class="titulos3">Requerimiento</font></a></td>	-->	 						
                        <td><a href=''><font class="titulos3">Estado</font></a></td>		 
                        <td><a href=''><font class="titulos3">Accion</font></a></td>		 
                    </tr>    
            <?php
            $iCounter = 0;
            // Display result
            while ($rs && !$rs->EOF) {
                $iCounter++;
                // Create field variables based on database fields		  
                $fldPRES_ID = $rs->fields["PRESTAMO_ID"];
                $fldRADICADO = $rs->fields["RADICADO"];
                $fldLOGIN = $rs->fields["LOGIN"];
                $fldDEPENDENCIA = $rs->fields["DEPENDENCIA"];
                $fldPRES_FECH_PEDI = $rs->fields["F_SOLICITUD"];
                $fldPRES_FECH_VENC = $rs->fields["F_VENCIMIENTO"];
                $fldPRES_FECH_CANC = $rs->fields["F_CANCELACION"];
                $fldPRES_FECH_PRES = $rs->fields["F_PRESTAMO"];
                $fldPRES_FECH_DEV = $rs->fields["F_DEVOLUCION"];
                $fldPRES_REQUERIMIENTO = $rs->fields["REQUERIMIENTO"];
                $fldTipoPrestamo = $rs->fields["TIPO_REQUERIMIENTO"];
                $fldPRES_ESTADO = $rs->fields["ESTADO"];
                $fldID_ESTADO = $rs->fields["ID_ESTADO"];
                $accion = "";
                //if ($fldTipoPrestamo != "Solicitado")
                    //$fldPRES_ESTADO .= " - " . $fldTipoPrestamo;
                if (strcasecmp($krd, $fldLOGIN) == 0 && $fldID_ESTADO == 1) {
                    $accion = "<a href=\"javascript: cancelar($fldPRES_ID); \">Cancelar Solicitud</a>";
                }
                $rs->MoveNext();
                // Indica el estilo de la fila
                if ($iCounter % 2 == 0) {
                    $tipoListado = "class=\"listado2\"";
                } else {
                    $tipoListado = "class=\"listado1\"";
                }
                // HTML prestamo show begin	  
                ?>	
                        <tr <?php echo $tipoListado; ?> align="center">
                            <td class="leidos"><?= tohtml($fldRADICADO); ?></td>	 
                            <td class="leidos"><?= tohtml($fldLOGIN); ?></td>	 
                            <td class="leidos"><?= tohtml($fldDEPENDENCIA); ?></td>	 
                            <td class="leidos"><?= tohtml($fldPRES_FECH_PEDI); ?></td>	 
                            <!-- <td class="leidos"><?= tohtml($fldPRES_FECH_VENC); ?></td> 
                            <td class="leidos"><?= tohtml($fldPRES_REQUERIMIENTO); ?></td>	-->	  							 						
                            <td class="leidos"><?= tohtml($fldPRES_ESTADO); ?></td>	 
                            <td class="leidos"><?= $accion ?></td>	 
                        </tr>  			
            <?php } ?>
                    <tr  align="center">
                        <td class="titulos3" colspan="8" align="center"><input type="submit" class='botones' value="Regresar"></td>
                    </tr>	  
                </table>					 
                <br>
            </form>  				   
        <?php
        }
    }
}

//-------------------------------
//===============================
// PRESTAMO_SHOW begin
//               Presenta el formulario para que 
//               el usuario haga la solicitud de los 
//               fisicos.
//-------------------------------
function PRESTAMO_show() {
    global $db;
    global $sFileName;
    global $krd; //usuario actual
    global $dependencia; //dependencia del usuario actual
    global $sPRESTAMOErr;
    $sFormTitle = "Solicitud de Prestamos";
    $fldradicado = get_param("radicado");
    $historicos = 999;
    $solicitarDocs = $_POST['solicitarDocs'];
    if ($sPRESTAMOErr == "") {

        // SQL que verifica la existencia de anexos para el radicado   
        $sSQL1 = "	SELECT	R.RADI_DESC_ANEX as ANEXO, 
							R.RADI_DEPE_ACTU as DEPE_RADICADO, 
							U.USUA_LOGIN as USUARIO_RADICADO 
					FROM	RADICADO R, 
							USUARIO U,
							SGD_USD_USUADEPE USD
					WHERE	R.RADI_NUME_RADI=$fldradicado and 
							U.USUA_CODI=R.RADI_USUA_ACTU and
							U.USUA_LOGIN = USD.USUA_LOGIN AND
							U.USUA_DOC = USD.USUA_DOC AND
							USD.SGD_USD_SESSACT = 1 AND
							USD.DEPE_CODI=R.RADI_DEPE_ACTU";
        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs1 = $db->conn->Execute($sSQL1);
        // Inicializacion de la variable que indica si el usuario puede o no hacer solicitudes
        if (!$rs1->EOF) {
            $fldANEXO = $rs1->fields["ANEXO"]; //campo que indica la existencia de anexos para el radicado
            $fldUSUARIO_RADICADO = $rs1->fields["USUARIO_RADICADO"];
            $fldDEPE_RADICADO = $rs1->fields["DEPE_RADICADO"];
            if ($fldDEPE_RADICADO != $historicos && $fldUSUARIO_RADICADO != $krd) {
                $sPRESTAMOErr = " solo puede ser solicitado y prestado al usuario $fldUSUARIO_RADICADO";
            }
        }
        // SQL con los tipos de requerimientos que se pueden realizar
        $sSQL = "select P.PRES_REQUERIMIENTO as REQUERIMIENTO from PRESTAMO P where P.RADI_NUME_RADI=$fldradicado and ";
        if ($_GET["Accion"] == "Renovar") {
            $sSQL .= " P.PRES_ESTADO in (1,5) ";
        } else {
            $sSQL .= " P.PRES_ESTADO in (1,2,5) ";
        }
        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $db->conn->Execute($sSQL);
        $iCounter = 0;
        while ($rs && !$rs->EOF) {
            $i[$iCounter] = $rs->fields["REQUERIMIENTO"];
            $iCounter++;
            $rs->MoveNext();
        }
        $reqPrestados = 0;
        for ($j = 0; $j < $iCounter; $j++) {
            $reqPrestados = $reqPrestados + $i[$j];
        }
        $sqlReq = "";
        if ($iCounter == 0) {
            if (strlen(trim($fldANEXO)) == 0) {
                $sqlReq = " =1 ";
            } else {
                $sqlReq = " in (1,2,3) ";
            }
        } else if ($iCounter == 1 && $reqPrestados < 3 && $reqPrestados > 0) {
            if (strlen(trim($fldANEXO)) != 0) {
                $sqlReq = " =" . (3 - $reqPrestados);
            }
        }
        if (strlen($sqlReq) != 0) {
            $sqlReq = "select PARAM_CODI,PARAM_VALOR from SGD_PARAMETRO where PARAM_NOMB='PRESTAMO_REQUERIMIENTO' and PARAM_CODI" .
                    $sqlReq . " order by PARAM_VALOR desc";
            // Show form field 			   
            ?>
            <script>
                /*Adecua el formulario para que la pagina regrese a la anterior*/
                function regresar() {
                    document.Prestamo.FormAction.value="cancelar";
                    document.Prestamo.submit();
                }
				
				function validar(){
					document.Prestamo.submit();
				}
            </script>
            <form method="POST" action="<?= $sFileName ?>" name="Prestamo">
                <input type="hidden" value="<?= $krd ?>" name="krd">
                <input type="hidden" value="insert" name="FormAction">
                <input type="hidden" value="<?= $dependencia ?>" name="dependencia">
                <input type="hidden" value="<?= $_GET["Accion"] ?>" name="Accion">			                    				  				  
                <input type="hidden" name="radicado" value="<?= tohtml($fldradicado) ?>">
                <?php
                // Usuario que no puede solicitar
                if ($sPRESTAMOErr != "") {
                    $lookup_s = db_fill_array($sqlReq);
                    $s = "";
                    if (is_array($lookup_s)) {
                        reset($lookup_s);
						foreach($lookup_s as $key=>$value){
							$s = ucfirst(strtolower($value));
                            if ($key == 3) {
                                $sPRESTAMOErr = " solo pueden ser solicitados y prestados al usuario $fldUSUARIO_RADICADO<br><br>";
                                break;
                            }
						}
                    }
                    $sPRESTAMOErr = "El " . $s . $sPRESTAMOErr;
                    ?>
                    <p align="center"><font class="titulosError2"><?= $sPRESTAMOErr ?></font><br>
                        <input type="submit" class="botones" value="Regresar" onClick="javascript: regresar();"></p>
                </form>
                <?php } else { ?>  
                <table class="borde_tab" align="center" width='110%'>
                    <tr>
                        <td class="titulos4" colspan="2"> <center> <?= $sFormTitle ?> </center> </td>
					</tr>
					<tr>
						<td class='titulos2' width='25%'>Radicado:</td>
						<td class='listado2' width='70%'><?= $fldradicado ?></td>
					</tr>
					<tr>
						<td class='titulos2' width='25%'>Usuario:</td>
						<td class='listado2' width='70%'><?= $krd ?></td>
					</tr>
					<tr>
						<td class='titulos2' width='25%'>Dependencia:</td>
						<td class='listado2' width='70%'><?= $dependencia ?></td>
					</tr>
					<tr>
						<td class='titulos2' width='25%'>Fecha Pedido:</td>						
						<td class='listado2' width='70%'><?= Date("d-m-Y") ?></td>
					</tr>
					<tr>
						<td class="listado5" colspan="2" style="text-align: center;">
							<input type="button" class="botones" value="Solicitar" onClick="validar();">&nbsp;&nbsp;
							<input type="button" class="botones" value="Regresar" onClick="javascript: regresar();">
						</td>
					</tr>
                </table>
                <?php
            }
        }
    }
}
?>