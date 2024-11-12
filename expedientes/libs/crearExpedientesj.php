<?php
$ruta_raiz = "../..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/include/tx/Historico.php";
include_once "$ruta_raiz/include/tx/Expediente.php";
include_once "$ruta_raiz/include/tx/Radicacion.php";
include_once "$ruta_raiz/class_control/TipoDocumental.php";
require "$ruta_raiz/class_control/correoElectronico.php";
$db = new ConnectionHandler("$ruta_raiz");

$objRadicado = new Radicacion($db);
$objCorreo = new correoElectronico($ruta_raiz, false, true);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
// $db->conn->debug = true;

header("Content-Type: application/json");
require_once ('../../FirePHPCore/fb.php');

// variables que se llegan desde el archivo adm_nombreTemasExp.js
$evento = $_POST['evento']; // Selecciona la accion a realizar crear buscar comporbar...
$query = $_POST['query']; // Texto digitado por el usuario cuando digita el nombre para el expediente.
$serie = $_POST['selectSerie']; // Serie del expediente
$selDependencia = $_POST['selectDependencia']; // Dependencia seleccionada
$subSerie = $_POST['selectSubSerie']; // Subserie del expediente que sera accinado
$tipoDocu = $_POST['selectTipoDocumental']; // Tipo documental del radicado dentro del expediente que sera asignado
$numExp_Ano = $_POST['numExp_Ano']; // Ano del expediente con el cual se creara
$selectProyecto = $_POST['selectProyecto']; // Proyecto al que se asocia el expediente
$veractivos = isset($_POST['veractivos']) ? $_POST['veractivos'] : 0; // 0 = ver todas las dependencias y series (Activas e inactivas). 1=Ver SOLO dependencias y series activas.
$auto = $_POST['auto']; // Identifica si el numero del expediente es creado por el usuario o es automatico
$nomb_Expe_300 = $_POST['nomb_Expe_300']; // Nombres de los expedientes
$numExpe = $_POST['numExpe']; // Numero del expediente con el que sera creado
$nurad = $_POST['nurad']; // Radicados que se quieren incluir en el expediente cuando este se cree
$publ_priv = $_POST['publ_priv']; // Da permisos al expediente
$usuario_Res = $_POST['selectUsuario']; // Usuario seleccionado que aparecera como autor del expediente
$cambio = $_POST["cambio"];
$codusua = $_POST["codUsua"]; // Codigo del usuario que realiza la accion
$documento_Usua = $_POST["docUsua"]; // Numero de identificacion del usuario cedula etc.
$depen = $_POST['depeCodiUsua']; // Dependencia del usuario que realiza la accion
$depeInput = empty($_POST['depeInput']) ? $depen : $_POST['depeInput']; // Dependencian selecionada por el usuario para buscar la serie y subserie caso 5
$ano = date('Y'); // Anho cuando el expediente es automatico
                  // $ano = date_default_timezone_get('Y'); //Anho cuando el expediente es automatico
$rad_anex = explode(",", $_POST['rad_anex']); // Radicados adicionales que se incluiran en el expediente
$expanho = substr($numExpe, 0, 4);
$tipoRad = substr($nurad, - 1);

$mensaje0 = "Parametros Incorrectos"; // Mensaje de informacion
$mensaje1 = "Parametros Incorrectos buscando secuencia"; // Mensaje de informacion
$mensaje2 = "No existe la subSerie Seleccionada"; // Mensaje de informacion
$mensaje3 = "No existe el usuario seleccionado"; // Mensaje de informacion
$mensaje4 = "No se inserto ningun radicado en el expediente"; // Mensaje de informacion
$mensaje5 = "El expediente esta inactivo o no existe";
$mensaje6 = "TRD: "; // Mensaje de informacion
$mensaje7 = "El expediente que intento crear ya existe."; // Mensaje de informacion
$mensaje8 = "No existe el expediente"; // Mensaje de informacion
$mensaje9 = "Formato del expediente erroneo"; // Mensaje de informacion
$mensaje10 = "Incluir radicado en Expediente"; // Mensaje de informacion
$mensaje11 = "El documento privado o publico genero Error"; // Mensaje de informacion
$mensaje12 = "No existe la serie seleccionada"; // Mensaje de informacion
$mensaje13 = "No existe una subserie para esta serie"; // Mensaje de informacion
$mensaje14 = "No existe una serie para esta dependencia"; // Mensaje de informacion
$mensaje15 = "Ya existe un nombre de expediente igual, para la misma Dependencia, Serie, Sub-serie y Vigencia, por favor verifique e intente de nuevo.";
$mensaje16 = "No existen usuarios activos en esta dependencia!"; // Mensaje de informacion
$mensaje17 = "No existen tipos documentales para esta TRD!";
$mensaje18 = "Tipo documental exigido !";
$mensaje19 = "Se modifico la TRD exitosamente!";

// Filtrar caracteres extraÃ±os enviados por
function strValido($string)
{
    $arr = array(
        '/[^\w:()\sáéíóúÁÉÍÓÚ=#°\-,.;ñÑ]+/',
        '/[\s]+/'
    );
    $asu = preg_replace($arr[0], '', $string);
    return strtoupper(preg_replace($arr[1], ' ', $asu));
}

// Funcion: Error para ser leido por crearExpedientes.js
function salirError($mensaje)
{
    $accion = array(
        'respuesta' => false,
        'mensaje' => $mensaje
    );
    print_r(json_encode($accion));
    return;
}

// Consulta si el radicado esta incluido en el expediente.
function validaExisteEnExp($expediente, $radicado, $numExpe)
{
    $existeEnExp = $expediente->expedientesRadicado($radicado);
    foreach ($existeEnExp as $value) {
        if ($value == $numExpe) {
            return true; // existe en el expediente
        }
    }
    return false; // No existe en el expediente
}

// IBISCOM 2018-10-24 INICIO
// FunciÃ³n que inserta un registro en la tabla de metadatos para un anexo a un expediente
function insertarMetadatosAnexoExp($db, $codigo, $hash, $folios, $nombre_proyector, $nombre_revisor, $nombre_firma, $palabras_clave, $fechaProduccion)
{
    $funcionHash = 'sha1';
    $id_tipo = 0; // aplica si es para un anexo al radicado(0) o un anexo al expediente(1)

    $validaSQL = "SELECT COUNT('$codigo') as NumMetad FROM METADATOS_DOCUMENTO WHERE id_anexo =  '$codigo'"; // si ya existe no se agrega metadatos //IBISCOM 2018-12-11
    $resultadoContador = $db->conn->Execute($validaSQL)->fields["NumMetad"];

    if ($resultadoContador == 0) {

        $insertSQL = "INSERT INTO METADATOS_DOCUMENTO " . // (id_anexo,id_tipo_anexo,hash,funcion_hash,folios,nombre_proyector,nombre_revisor,nombre_firma,palabras_clave)
        "VALUES ('$codigo',$id_tipo,'$hash','$funcionHash','$folios','$nombre_proyector','$nombre_revisor','$nombre_firma','$palabras_clave',NULL,'$fechaProduccion', 0)";

        $insertMetadatos = $db->conn->Execute($insertSQL);
    }
}
;
// IBISCOM 2018-10-24 INICIO

// Ejecuta acciones que llegan desde crearExpedientes.js
switch ($evento) {
    case 1:
        { // Retornar subSerie
          // si alguno de los siguientes parametros no esta, salga.
            if (empty($depeInput) || empty($serie)) {
                salirError($mensaje0);
                return;
            }

            /**
             * Consular en la base de datos las respectivas
             * sebseries que pertenecen a esta dependencia
             */
            $fecha_hoy = Date("d-m-Y");
            $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
            $wva = ($veractivos == 1) ? " AND MR.SGD_MRD_ESTA=1 " : "";
            $sql1 = "SELECT	DISTINCT (RIGHT('000' + convert(varchar,SU.SGD_SBRD_CODIGO),3)+' - '+SU.SGD_SBRD_DESCRIP + ' - ' + 
							CASE WHEN MR.SGD_MRD_ESTA = 0 THEN '(INACTIVA)' ELSE '(ACTIVA)' END)AS DETALLE,
							convert(varchar,SU.SGD_SBRD_CODIGO) AS CODIGO_SUBSERIE,
							MR.SGD_MRD_ESTA
					FROM	SGD_MRD_MATRIRD MR
							JOIN SGD_SBRD_SUBSERIERD SU ON SU.SGD_SBRD_CODIGO = MR.SGD_SBRD_CODIGO AND SU.SGD_SRD_CODIGO = MR.SGD_SRD_CODIGO
					WHERE	MR.DEPE_CODI = '$depeInput' AND
							MR.SGD_SRD_CODIGO = '$serie' AND
							MR.SGD_MRD_CODIGO =(SELECT	TOP(1) M2.SGD_MRD_CODIGO 
												FROM	SGD_MRD_MATRIRD M2 
												WHERE	M2.DEPE_CODI = '$depeInput' AND
														M2.SGD_SRD_CODIGO = '$serie' AND
														M2.SGD_SBRD_CODIGO = MR.SGD_SBRD_CODIGO
												ORDER BY M2.SGD_MRD_ESTA DESC)
					$wva 
					ORDER BY MR.SGD_MRD_ESTA DESC, DETALLE";
            $salida = $db->conn->Execute($sql1);
            while (! $salida->EOF) {
                $result[] = array(
                    "codigo" => $salida->fields["CODIGO_SUBSERIE"],
                    "nombre" => $salida->fields["DETALLE"]
                );
                $salida->MoveNext();
            }

            if ($result) {
                $accion = array(
                    'respuesta' => true,
                    'mensaje' => $result
                );
                print_r(json_encode($accion));
            } else {
                salirError($mensaje13);
            }
        }
        break;
    case 2: // secuencia de expedientes

        $expediente = new Expediente($db);

        $subSerie = str_pad($subSerie, 3, "0", STR_PAD_LEFT);
        $serie = str_pad($serie, 3, "0", STR_PAD_LEFT);
        $secExp = $expediente->secExpediente($depen, $serie, $subSerie, $numExp_Ano);
        if (! empty($secExp)) {
            /*while (strlen($secExp) < 5) {
                $secExp = '0' . $secExp;
            }*/
            $secExp = str_pad($secExp, 5, "0", STR_PAD_LEFT);
        }
        $accion = array(
            'respuesta' => true,
            'mensaje' => $secExp
        );
        print_r(json_encode($accion));
        break;

    case 3: // crea y modifica expediente
             // Valida si el nombre digitado ya existe para la misma Dep.
             // para el mismo anho, misma serie y Sub-serie.

        $db->conn->BeginTrans();

        $expediente = new Expediente($db);
        $Historico = new Historico($db);
        $trd = new TipoDocumental($db);

        $subSerie = str_pad($subSerie, 3, "0", STR_PAD_LEFT);
        $serie = str_pad($serie, 3, "0", STR_PAD_LEFT);
        $depenNum = str_pad($depen, 4, "0", STR_PAD_LEFT);   /// ojo para dependencia 4 digitos
        $nomb_Expe_300 = str_replace('?', '', $nomb_Expe_300);
        $nombExpExiste = $expediente->consulta_nombexp($nomb_Expe_300, $depen, $serie, $subSerie, $expanho);
        if ($nombExpExiste == 0) {
            $r = $depen * $serie * $subSerie * $codusua;
            //(empty($r)) ? salirError($mensaje0) : '';
            $date1 = Date("d-m-Y");

            // Colocar el expediente como publico o privado
            // enviamos a la base de datos el valor de 1 o null
            $publ_priv = ($publ_priv == "Publico") ? null : 1;

            // validar formato del numero de Expediente
            if ($auto == 'false') {
                $db->conn->RollbackTrans();
                if (! preg_match('/' . "^[0-9]{19}[A-Z]{1}" . '/i', $numExpe)) {
                    salirError($mensaje9);
                    return;
                }
                /*
                 * if ( !eregi("^[0-9]{18}[A-Z]{1}", $numExpe) ) {
                 * salirError($mensaje9);
                 * return;
                 * }
                 */
            } else {
                $secExp = $expediente->secExpediente($depen, $serie, $subSerie, $ano);
                if (! empty($secExp)) {
                   /* while (strlen($secExp) < 5) {
                        $secExp = '0' . $secExp;
                    }*/
                    $secExp = str_pad($secExp, 5, "0", STR_PAD_LEFT);
                }

                // Numero del expediente si es automatico
                $numExpe = $ano . $depenNum . $serie . $subSerie . $secExp . 'E';
            }

            /*
             * Se llama la funcion para crear el expediente despues de
             * tener los valores verificados.
             */
            if (empty($selectProyecto)) {
                $selectProyecto = 0;
            }
            $numeroExpedienteE = $expediente->crearExpediente($numExpe, $nurad, $depen, $codusua, $documento_Usua, $usuario_Res, $serie, $subSerie, 'false', "'$date1'", 0, null, $publ_priv, null, $nomb_Expe_300, $selectProyecto);

            if ($numeroExpedienteE == 0) {
                $db->conn->RollbackTrans();
                $accion = array(
                    'respuesta' => false,
                    'mensaje' => $mensaje7 . ": " . $numeroExpedienteE
                );
                print_r(json_encode($accion));
                return;
            } else {
                // Cambiar nombre del proyecto
                /*
                 * if(!empty($selectProyecto)){
                 * $insetNomb = $expediente->insert_ProyNomb($numExpe,$selectProyecto);
                 * if($insetNomb) {
                 * $db->conn->RollbackTrans();
                 * $accion = array( 'respuesta' => false,
                 * 'mensaje' => "Error insertando proyecto nombre");
                 * print_r(json_encode($accion));
                 * return;
                 * }
                 * }
                 */

                // cambiar el nombre del expediente
                /*
                 * $insercioNomExp = $expediente->insert_ExpedienteNomb($numExpe,strtoupper($nomb_Expe_300));
                 * if(!$insercioNomExp) {
                 * $db->conn->RollbackTrans();
                 * $accion = array( 'respuesta' => false,
                 * 'mensaje' => "Error insertando nombre");
                 * print_r(json_encode($accion));
                 * return;
                 * }
                 */
                // Al crear solo vamos a guardar el historico que indica que el
                // expediente entra a la primera etapa del proceso

                $observa = $mensaje6 . $serie . "/" . $subSerie . "/ Nombre: " . $nomb_Expe_300;
                $radicados[] = $nurad;
                $tipoTx = 51;
                $insHisRad = $Historico->insertarHistorico($radicados, $depen, $codusua, $depen, $codusua, "Creacion de expediente: $numExpe ", $tipoTx);
                $insertHis = $Historico->insertarHistoricoExp($numExpe, $radicados, $depen, $codusua, $observa, $tipoTx, 0);
                if (! $insertHis) {
                    $db->conn->RollbackTrans();
                    $accion = array(
                        'respuesta' => false,
                        'mensaje' => "Error insertando historico expediente"
                    );
                    print_r(json_encode($accion));
                    return;
                }

                $isqlTRD = "SELECT	SGD_MRD_CODIGO FROM	SGD_MRD_MATRIRD
					                   WHERE DEPE_CODI = $depen AND SGD_SRD_CODIGO = $serie
							           AND SGD_SBRD_CODIGO = $subSerie	AND SGD_TPR_CODIGO 	= $tipoDocu";
                $rsTRD = $db->conn->Execute($isqlTRD);
                $i = 0;
                if ($rsTRD && ! $rsTRD->EOF) {
                    while (! $rsTRD->EOF) {
                        $codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
                        $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
                        $i ++;
                        $rsTRD->MoveNext();
                    }
                } else {
                    $db->conn->RollbackTrans();
                    $accion = array(
                        'respuesta' => false,
                        'mensaje' => "No existe matriz: " . $depen . "-" . $serie . "-" . $subSerie . "-" . $tipoDocu
                    );
                    print_r(json_encode($accion));
                    return;
                }

                $serieactu = "";
                $subseactu = "";
                $docuactu = "";
                $sqlDt = "SELECT R.SGD_MRD_CODIGO, R.RADI_NUME_RADI, M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO,
                                        S.SGD_SRD_DESCRIP, B.SGD_SBRD_DESCRIP, T.SGD_TPR_DESCRIP
                                    FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
                                    	INNER JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
                                    	INNER JOIN SGD_SBRD_SUBSERIERD B ON B.SGD_SBRD_CODIGO = M.SGD_SBRD_CODIGO AND B.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
                                    	INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
                                    WHERE R.RADI_NUME_RADI = $nurad ";
                $rsDt = $db->conn->Execute($sqlDt);
                if ($rsDt && ! $rsDt->EOF) {
                    $serieactu = $rsDt->fields["SGD_SRD_DESCRIP"];
                    $subseactu = $rsDt->fields["SGD_SBRD_DESCRIP"];
                    $docuactu = $rsDt->fields["SGD_TPR_DESCRIP"];
                } 
                /*else {
                    $db->conn->RollbackTrans();
                    $accion = array(
                        'respuesta' => false,
                        'mensaje' => "Error seleccionando la TRD del radicado"
                    );
                    print_r(json_encode($accion));
                    return;
                }*/

                $transac = 32;
                if ($tipoRad == 2) {
                    $transac = 34;
                    if ($cambio == 4)
                        $transac = 107;
                }

                $observa = "Asignacion Tipo documental por creacion de expdiente - TRD Anterior: " . $serieactu . "/" . $subseactu . "/" . $docuactu;
                $radiModi = $Historico->insertarHistorico(array($nurad), $depen, $codusua, $depen, $codusua, $observa, $transac);
                if (! $radiModi) {
                    $db->conn->RollbackTrans();
                    $accion = array(
                        'respuesta' => false,
                        'mensaje' => "Error insertando el historico de la TRD al radicado"
                    );
                    print_r(json_encode($accion));
                    return;
                }
                
                $sqlTRD = "";
                
                $TRD = $codiTRD;
                $sqlH = "SELECT	RADI_NUME_RADI, DEPE_CODI FROM SGD_RDF_RETDOCF
				                    WHERE RADI_NUME_RADI = $nurad AND DEPE_CODI = $depen ";
                $rsH = $db->conn->Execute($sqlH);
                $i = 0;
                if ($rsH && ! $rsH->EOF) {
                    while (! $rsH->EOF) {
                        $codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
                        $depCod = $rsH->fields['DEPE_CODI'];
                        
                        $sqlUA = "	UPDATE SGD_RDF_RETDOCF SET SGD_MRD_CODIGO = '$codiTRD',
                							USUA_CODI = '$codusua', USUA_DOC = '$documento_Usua'
                	      			  WHERE	RADI_NUME_RADI = $nurad AND DEPE_CODI = $depCod ";
                        $rsUp = $db->conn->Execute($sqlUA);
                        if (! $rsUp) {
                            $db->conn->RollbackTrans();
                            $accion = array(
                                'respuesta' => false,
                                'mensaje' => "Error actualizando la TRD al radicado"
                            );
                            print_r(json_encode($accion));
                            return;
                        }
                        
                        $i ++;
                        $rsH->MoveNext();
                    }
                } else {
                    $radicados = $trd->insertarTRD($codiTRDS, $codiTRD, $nurad, $depen, $codusua);
                    if (! $radicados) {
                        $db->conn->RollbackTrans();
                        $accion = array(
                            'respuesta' => false,
                            'mensaje' => "Error insertando la TRD al radicado"
                        );
                        print_r(json_encode($accion));
                        return;
                    }
                }
                
                //Se guarda el registro en el historico de TRD
                $queryGrabarN = "INSERT INTO SGD_HMTD_HISMATDOC(SGD_HMTD_FECHA, RADI_NUME_RADI, USUA_CODI, SGD_HMTD_OBSE, USUA_DOC, DEPE_CODI, 
                                SGD_MRD_CODIGO, SGD_TTR_CODIGO) 
                                VALUES(	".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
						          $nurad, $codusua, 'Cambio de TRD en creacion de expediente', $documento_Usua, $depen, '$codiTRD', 34)";
		        $ejecutarQuerey	= $db->conn->Execute($queryGrabarN);
                
                // Actualiza el campo tdoc_codi de la tabla Radicados
                $sqlUPTdoc = "UPDATE RADICADO SET TDOC_CODI = $tipoDocu WHERE RADI_NUME_RADI = $nurad ";
                $radiUp = $db->conn->Execute($sqlUPTdoc);
                // $radiUp = $trd->actualizarTRD(array($nurad), $tipoDocu);
                if (! $radiUp) {
                    $db->conn->RollbackTrans();
                    $accion = array(
                        'respuesta' => false,
                        'mensaje' => "Error actualizando el Tipo documental al radicado"
                    );
                    print_r(json_encode($accion));
                    return;
                }

                // Si existe radicados en la variable $rad_anex los Insertamos
                // en el expediente
                $rad_anex[] = $nurad;
                $rad_anex = array_filter($rad_anex);

                foreach ($rad_anex as $actual) {
                    $existeEn = validaExisteEnExp($expediente, $actual, $numExpe);
                    if ($existeEn == false) {

                        // Para los radicados que ya están incluidos en expedientes con la correspondiente tipificación y se incluyan en otro expediente con series y
                        // tipificación diferente este debe generar una notificación a la persona del Grupo de Biblioteca y Archivo (Sandra Arango),
                        // (en realidad es a quien tenga el permiso USUA_NOTIF_RADEXP) informando sobre esta actividad "radicado incluido en otro expediente"
                        $vecExisteRadEnExp = $expediente->expedientesRadicado($actual);
                        if (count($vecExisteRadEnExp) > 0) {
                            // Realizamos lógica de envio de notificación.
                            $sql = "SELECT USUA_EMAIL FROM usuario WHERE USUA_NOTIF_RADEXP=1";
                            $ADODB_COUNTRECS = TRUE;
                            $tmpRs = $db->conn->CacheExecute(15, $sql);
                            if ($tmpRs->recordCount() > 0) {
                                foreach ($tmpRs as $x => $fila) {
                                    $destCorreosPARA[] = $fila['USUA_EMAIL'];
                                }
                                $asunto = "SGD Orfeo. Radicado $actual incluido en otro Expediente $numExpe";
                                $cuerpo = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
									<tr><td colspan='2' style='font-family: verdana; font-size: 75%'><br/><br/>
									Estimado(a) lectores:<br/><br/><br/>El radicado $actual se incluy&oacute; en el expediente $numExpe, .
									</td><tr>
									<tr><td colspan='2'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
									</table>";
                                $enviarCorreo = $objCorreo->enviarCorreo($destCorreosPARA, $destCorreosCC, $cco, $asunto, $cuerpo);
                            }
                        }

                        $saliExp = $expediente->insertar_expediente($numExpe, $actual, $depen, $codusua, $documento_Usua);
                        if ($saliExp == 1) {
                            $rad_histo[] = $actual;
                        } else {
                            $db->conn->RollbackTrans();
                            $accion = array(
                                'respuesta' => false,
                                'mensaje' => "Error insertando el radicado al expediente"
                            );
                            print_r(json_encode($accion));
                            return;
                        }
                    } else {
                        $yaexiste[] = $actual;
                    }
                }

                // si existen algun radicado grabado lo registramos
                // en el historico
                if (! empty($rad_histo)) {
                    $observa = $mensaje10;
                    $tipoTx = 53;
                    $insHis = $Historico->insertarHistoricoExp($numExpe, $rad_histo, $depen, $codusua, $observa, $tipoTx, 0);
                    if (! $insHis) {
                        $db->conn->RollbackTrans();
                        $accion = array(
                            'respuesta' => false,
                            'mensaje' => "Error insertando el historico al expediente"
                        );
                        print_r(json_encode($accion));
                        return;
                    }
                }
            }

            //calcular fecha de venciiento
            $sqlRad = "SELECT ".$db->conn->SQLDate('d-m-Y H:i:s', 'R.RADI_FECH_RADI')." AS FECHA,
                                FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1, R.RADI_FECHA_VENCE AS FECHA_VENCE,
                                T.SGD_TPR_TERMINO, S.FECHA_VENCE AS PRORROGA
                            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO T ON R.TDOC_CODI = T.SGD_TPR_CODIGO
		                          LEFT JOIN SGD_PRORROGAS S ON R.RADI_NUME_RADI = S.RADI_NUME_RADI
                            WHERE R.RADI_NUME_RADI = " . $nurad;
            $rsPadre = $db->conn->Execute($sqlRad);
            if ($rsPadre && !$rsPadre->EOF) {
                $fechaRad = $rsPadre->fields["FECHA"];
                $fecha1 = $rsPadre->fields['FECHA1'];
                $fechaVenci = $rsPadre->fields['FECHA_VENCE'];
                $termino = intval($rsPadre->fields['SGD_TPR_TERMINO']);
                $d = new DateTime($fechaVenci);
                $format_date = $d->format('Y-m-d');
                
                if ($termino > 0) {
                    $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                    $diasTram = ( $db->conn->getone($sqlDiasV) );
                    
                    $termino1 = $termino - 1;
                    $sqlFec	= "SELECT dbo.sumadiasfecha($termino1, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                    $fecVence	= $db->conn->getone($sqlFec);
                    $fecVence = substr($fecVence,0,10);
                    $diasRest = $termino - $diasTram;
                    
                    $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '$fecVence', RADI_DIAS_VENCE = $diasRest WHERE RADI_NUME_RADI = " . $nurad;
                    $ok = $db->conn->Execute($sqlUpd);
                }
            }
            
            $sqlUpd = "UPDATE RADICADO SET SGD_CAMBIO_TRD = 0 WHERE RADI_NUME_RADI = " . $nurad;
            $ok = $db->conn->Execute($sqlUpd);
            if (! $ok) {
                $db->conn->RollbackTrans();
                $accion = array(
                    'respuesta' => false,
                    'mensaje' => "Error actualizando el cambio de TRD al radicado"
                );
                print_r(json_encode($accion));
                return;
            }

            // tener encuenta -- no existe
            /*
             * $st = "exec SGD_SEXP_SECEXPEDIENTES_ACTUALIZA_FechasExtremas @NoExpediente='$numeroExpedienteE'";
             * $rs = $db->conn->Execute($st);
             * if(!$rs) {
             * $db->conn->RollbackTrans();
             *
             * $accion = array( 'respuesta' => false,
             * 'mensaje' => "Error actualizando fechas extremas al expediente");
             * print_r(json_encode($accion));
             * return;
             * }
             */
            $db->conn->CommitTrans();

            $accion = array(
                'respuesta' => true
            );
            print_r(json_encode($accion));
            return;
        } else {
            $accion = array(
                'respuesta' => false,
                'mensaje' => $mensaje15
            );
            print_r(json_encode($accion));
            unset($nomb_Expe_300);
            return;
        }

        break;

    case 4: // busca nomb de expedientes
        $sqlE = "	SELECT	top 30 SGD_SEXP_PAREXP1
								+ ' ' + SGD_SEXP_PAREXP2
								+ ' ' + SGD_SEXP_PAREXP3
								+ ' ' + SGD_SEXP_PAREXP4
								+ ' ' + SGD_SEXP_PAREXP5
								AS EXPEDIENTE
						FROM 	SGD_SEXP_SECEXPEDIENTES
						WHERE	DEPE_CODI = $depen
								AND	( SGD_SEXP_PAREXP1 LIKE '%$query%'
										OR SGD_SEXP_PAREXP2 LIKE '%$query%'
										OR SGD_SEXP_PAREXP3 LIKE '%$query%'
										OR SGD_SEXP_PAREXP4 LIKE '%$query%'
										OR SGD_SEXP_PAREXP5 LIKE '%$query%') ORDER BY 1";

        $salida = $db->conn->Execute($sqlE);
        while (! $salida->EOF && ! empty($salida)) {
            $nombExp = preg_replace('/\s/', ' ', $salida->fields["EXPEDIENTE"]);
            if (! empty($nombExp)) {
                $result[] = $nombExp;
            }
            $salida->MoveNext();
        }
        if (is_array($result))
            for ($i = 0; $i < count($result); $i ++) {
                print "$result[$i]\n";
            }

        break;

    case 5: // Incluir radicados en expediente
        {
            $expediente = new Expediente($db);

            // IBISCOM 2018-10-27 Inicio
            // Busca documentos anexos al numero de radicado
            $selectCountAnex = "SELECT count(A.ANEX_RADI_NUME)	as NumAnex
				        FROM ANEXOS A
				        WHERE A.ANEX_RADI_NUME 	 = $nurad";
            // AND RADI_NUME_SALIDA IS NOT NULL";
            $countAnex = $db->conn->Execute($selectCountAnex)->fields["NumAnex"];
            if ($countAnex == 0) {
                $selectCountAsocia = "SELECT count(A.ANEX_RADI_NUME)	as NumAnex
				        FROM SGD_HIST_IMG_ANEX_RAD A
				        WHERE A.ANEX_RADI_NUME 	 = $nurad";

                $countAnex = $db->conn->Execute($selectCountAsocia)->fields["NumAnex"];
                if ($countAnex == 0) {
                    $selectCountRadi = "SELECT count (A.RADI_NUME_RADI) as NumRadi
                                    FROM RADICADO A
                                    WHERE A.RADI_NUME_RADI = $nurad
                                    AND A.RADI_PATH IS NOT NULL";
                    $countAnex = $db->conn->Execute($selectCountRadi)->fields["NumRadi"];
                    if ($countAnex == 0) {
                        salirError("No se incluyo en el expediente, el radicado no tiene documento asociado");
                        // print_r(json_encode("No se guardó el documento, el radicado no tiene documento asociado"));
                        return;
                    }
                }
            }
            // IBISCOM 2018-10-27 Fin
            
            if ($_POST['codTdoc'] == 0) {
                salirError("No se incluyo en el expediente, debe seleccionar un tipo documental");
                return;
            }

            if ($_POST['codTdoc'] > 0) {
                include_once ORFEOPATH . "class_control/TipoDocumental.php";
                // include_once ORFEOPATH . "include/tx/Historico.php";
                $trd = new TipoDocumental($db);
                // Si existe radicados en la variable $rad_anex los Insertamos
                // en el expediente
                $rad_anex[] = $nurad;
                $rad_anex = array_filter($rad_anex);

                // obtenemos el nombre del tipo documental actual
                $coditrdx = "SELECT	s.SGD_TPR_DESCRIP as TPRDESCRIP FROM RADICADO r, SGD_TPR_TPDCUMENTO s WHERE	r.TDOC_CODI = s.SGD_TPR_CODIGO AND
			             r.RADI_NUME_RADI = $nurad";
                $res_coditrdx = $db->conn->Execute($coditrdx);
                $TDCactu = $res_coditrdx->fields['TPRDESCRIP'];

                // ACTUALIZAR TRS DE RADICADO PADRE
                $sql = "update radicado set tdoc_codi=" . $_POST['codTdoc'] . " where radi_nume_radi=" . $nurad;
                $db->conn->Execute($sql);

                $isqlTRD = "SELECT	SGD_MRD_CODIGO FROM SGD_MRD_MATRIRD 
                            WHERE DEPE_CODI = " . $_POST['depen'] . " AND SGD_SRD_CODIGO 	= " . $_POST['selectSerie'] . " 
                            AND SGD_SBRD_CODIGO = " . $_POST['selectSubSerie'] . " AND SGD_TPR_CODIGO 	= " . $_POST['codTdoc'];
                $rsTRD = $db->conn->Execute($isqlTRD);
                $j = 0;
                while (! $rsTRD->EOF) {
                    $codiTRDS[$j] = $rsTRD->fields['SGD_MRD_CODIGO'];
                    $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
                    $j ++;
                    $rsTRD->MoveNext();
                }

                // $radicados = $trd->insertarTRD($codiTRDS, $codiTRD, $nurad, $depen, $codusua);
                $sqlH = "SELECT	RADI_NUME_RADI FROM	SGD_RDF_RETDOCF r WHERE	r.RADI_NUME_RADI = $nurad 
			                 AND r.DEPE_CODI = $depen ";
                $rsH = $db->conn->Execute($sqlH);
                $j = 0;
                if ($rsH && ! $rsH->EOF) {
                    while ($rsH && ! $rsH->EOF) {
                        $codiRegH[$j] = $rsH->fields['RADI_NUME_RADI'];
                        $j ++;
                        $rsH->MoveNext();
                    }

                    $sqlUA = "	UPDATE SGD_RDF_RETDOCF SET SGD_MRD_CODIGO = '$codiTRD',
							USUA_CODI = '$codusua', DEPE_CODI =  $depen, USUA_DOC = '$documento_Usua'
	      			  WHERE	RADI_NUME_RADI = $nurad ";
                    $rsUp = $db->conn->Execute($sqlUA);
                } else {
                    $radicados = $trd->insertarTRD($codiTRDS, $codiTRD, $nurad, $depen, $codusua);
                }

                $serieactu = "";
                $subseactu = "";
                $docuactu = "";
                $sqlDt = "SELECT R.SGD_MRD_CODIGO, R.RADI_NUME_RADI, M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO,
                    S.SGD_SRD_DESCRIP, B.SGD_SBRD_DESCRIP, T.SGD_TPR_DESCRIP
                FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
                	INNER JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
                	INNER JOIN SGD_SBRD_SUBSERIERD B ON B.SGD_SBRD_CODIGO = M.SGD_SBRD_CODIGO AND B.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
                	INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
                WHERE R.RADI_NUME_RADI = $nurad ";
                $rsDt = $db->conn->Execute($sqlDt);
                if ($rsDt && ! $rsDt->EOF) {
                    $serieactu = $rsDt->fields["SGD_SRD_DESCRIP"];
                    $subseactu = $rsDt->fields["SGD_SBRD_DESCRIP"];
                    $docuactu = $rsDt->fields["SGD_TPR_DESCRIP"];
                }

                $transac = 32;
                if ($tipoRad == 2) {
                    $transac = 34;
                    if ($cambio == 4)
                        $transac = 107;
                }

                $Historico = new Historico($db);
                $observa = "Asignacion Tipo documental por inclusion de expdiente - TRD Anterior: " . $serieactu . "/" . $subseactu . "/" . $docuactu;
                $radiModi = $Historico->insertarHistorico($codiRegH, $depen, $codusua, $depen, $codusua, $observa, $transac);

                // Se guarda el registro en el historico de TRD
                $queryGrabar = "INSERT INTO SGD_HMTD_HISMATDOC( SGD_HMTD_FECHA,
                			 RADI_NUME_RADI, USUA_CODI, USUA_DOC,
                			 DEPE_CODI, SGD_HMTD_OBSE, SGD_MRD_CODIGO, SGD_TTR_CODIGO)
                			 VALUES(	" . $db->conn->OffsetDate(0, $db->conn->sysTimeStamp) . ",
                			 $nurad, $codusua, $documento_Usua, $depen, 'Se inserta TRD', $codiTRD, $transac)";
                $ejecutarQuerey = $db->conn->Execute($queryGrabar);

                // FIN ACTUALIZAR TRS DE RADICADO PADRE

                // Verificamos que el expediente destino exista y estÃ© abierto
                $sqlBus = "	SELECT COUNT(*) AS TOTAL FROM SGD_SEXP_SECEXPEDIENTES SE
							WHERE SE.SGD_EXP_NUMERO = '$numExpe' AND SE.SGD_SEXP_ESTADO = 0";
                $salida = $db->conn->Execute($sqlBus);

                if (! $salida->EOF) {
                    if ($salida->fields["TOTAL"] == 0) {
                        salirError($mensaje5);
                        return;
                    }
                }

                // IBISCOM 2018-12-13 Inicio
                $updateSQLr = "UPDATE RADICADO SET TDOC_CODI = '" . $_POST['codTdoc'] . "'	WHERE RADI_NUME_RADI = $nurad";
                $resultadoTXU = $db->conn->Execute($updateSQLr);
                // IBISCOM 2018-12-13 Fin

                $destCorreosPARA = array();
                foreach ($rad_anex as $actual) {
                    $existeEn = validaExisteEnExp($expediente, $actual, $numExpe);
                    if ($existeEn == false) {

                        // Para los radicados que ya están incluidos en expedientes con la correspondiente tipificación y se incluyan en otro expediente con series y
                        // tipificación diferente este debe generar una notificación a la persona del Grupo de Biblioteca y Archivo (Sandra Arango),
                        // (en realidad es a quien tenga el permiso USUA_NOTIF_RADEXP) informando sobre esta actividad "radicado incluido en otro expediente"
                        $vecExisteRadEnExp = $expediente->expedientesRadicado($actual);
                        if ($vecExisteRadEnExp[0] != 0) {
                            // Realizamos lógica de envio de notificación.
                            $sql = "SELECT USUA_EMAIL FROM usuario WHERE USUA_NOTIF_RADEXP=1";
                            $ADODB_COUNTRECS = TRUE;
                            $tmpRs = $db->conn->CacheExecute(15, $sql);
                            if ($tmpRs->recordCount() > 0) {
                                foreach ($tmpRs as $x => $fila) {
                                    $destCorreosPARA[] = $fila['USUA_EMAIL'];
                                }
                                $asunto = "SGD Orfeo. Radicado $actual incluido en otro Expediente $numExpe";
                                $cuerpo = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
								<tr><td colspan='2' style='font-family: verdana; font-size: 75%'><br/><br/>
								Estimado(a) lectores:<br/><br/><br/>El radicado $actual se incluy&oacute; en el expediente $numExpe, .
								</td><tr>
								<tr><td colspan='2'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
								</table>";
                                $enviarCorreo = $objCorreo->enviarCorreo($destCorreosPARA, $destCorreosCC, $cco, $asunto, $cuerpo);
                            }
                        }

                        $saliExp = $expediente->insertar_expediente($numExpe, $actual, $depen, $codusua, $documento_Usua);
                        
                        // Ibis: Calculo e insert de hash
                        $funcion_hash = "sha1";
                        $querySelec = "SELECT   ANEX_CODIGO AS ID, ANEX_NOMB_ARCHIVO AS FILENAME
                                     FROM     ANEXOS
                                     WHERE    ANEX_RADI_NUME ='$actual'";

                        $exec = $db->conn->Execute($querySelec);

                        // $saliExp = $expediente->insertar_expediente($numExpe, $actual, $depen, $codusua, $documento_Usua);
                        // IBISCOM 2018-10-24

                        // IBISCOM 2018-10-24
                        $IdDoc = '';
                        while ($execTemp = $exec->fetchRow()) {
                            $IdDoc = $execTemp['ID'];
                            $nameDoc = $execTemp['FILENAME'];

                            $hash = "";
                            $ruta = (string) '../../bodega/' . substr(trim($IdDoc), 0, 4) . '/' . substr(trim($IdDoc), 4, 3) . '/docs/' . $nameDoc;
                            if (file_exists($ruta)) {
                                $hash = hash_file($funcion_hash, $ruta);
                            }

                            $nombre_firma = "";
                            $fechaProduccion = date("Y") . "-" . date("m") . "-" . date("d");
                            $resultMetadatos = insertarMetadatosAnexoExp($db, $IdDoc, $hash, $folios, $nombreProyector, $nombreRevisor, $nombre_firma, $palabrasClave, $fechaProduccion);
                        }
                        if ($exec->EOF) { // Aplica para documento principal cargado desde digtalizador
                            $sqlMeta = "SELECT TOP (1)	R.RADI_PATH as PATH
							FROM	SGD_HIST_IMG_RAD A
							INNER JOIN RADICADO R ON A.RADI_NUME_RADI=R.RADI_NUME_RADI
							WHERE	A.RADI_NUME_RADI = $actual
							ORDER BY A.fecha DESC";

                            $resultPath = $db->conn->Execute($sqlMeta)->fields['PATH'];

                            $hash = "";
                            $ruta = BODEGAPATH . $resultPath;
                            if (file_exists($ruta)) {
                                $hash = hash_file($funcion_hash, $ruta);
                            }

                            $nombre_firma = "";
                            $fechaProduccion = date("Y") . "-" . date("m") . "-" . date("d");
                            $resultMetadatos = insertarMetadatosAnexoExp($db, $actual, $hash, $folios, $nombreProyector, $nombreRevisor, $nombre_firma, $palabrasClave, $fechaProduccion);
                        }
                        
                        // IBISCOM 2018-10-24
                        if ($saliExp == 1) {
                            $rad_histo[] = $actual;
                        }

                        $ADODB_COUNTRECS = FALSE;
                    } else {
                        $yaexiste[] = $actual;
                    }
                }

                //calcular fecha de vencimiento               
                $sqlRad = "SELECT ".$db->conn->SQLDate('d-m-Y H:i:s', 'R.RADI_FECH_RADI')." AS FECHA,
                                FORMAT(R.RADI_FECH_RADI, 'yyyyMMdd') AS FECHA1, R.RADI_FECHA_VENCE AS FECHA_VENCE,
                                T.SGD_TPR_TERMINO, S.FECHA_VENCE AS PRORROGA
                            FROM RADICADO R INNER JOIN SGD_TPR_TPDCUMENTO T ON R.TDOC_CODI = T.SGD_TPR_CODIGO
		                          LEFT JOIN SGD_PRORROGAS S ON R.RADI_NUME_RADI = S.RADI_NUME_RADI
                            WHERE R.RADI_NUME_RADI = " . $nurad;
                $rsPadre = $db->conn->Execute($sqlRad);
                if ($rsPadre && !$rsPadre->EOF) {
                    $fechaRad = $rsPadre->fields["FECHA"];
                    $fecha1 = $rsPadre->fields['FECHA1'];
                    $fechaVenci = $rsPadre->fields['FECHA_VENCE'];
                    $termino = intval($rsPadre->fields['SGD_TPR_TERMINO']);
                    $prorroga = $rsPadre->fields['PRORROGA'];
                    $d = new DateTime($fechaVenci);
                    $format_date = $d->format('Y-m-d');
                    
                    if ($termino > 0) {
                        $sqlDiasV = "SELECT dbo.diashabilestramite(CONVERT (date, '$fechaRad'), CONVERT (date, GETDATE()) )";
                        $diasTram = $db->conn->getone($sqlDiasV);
                        
                        $termino1 = $termino - 1;
                        $sqlFec	= "SELECT dbo.sumadiasfecha($termino1, '$fecha1', 1, 1, 1, 1, 1, 0, 0, 0)";
                        $fecVence = $db->conn->getone($sqlFec);
                        $fecVence = substr($fecVence,0,10);
                        $diasRest = $termino - $diasTram;
                        
                        $sqlUpd = "UPDATE RADICADO SET RADI_FECHA_VENCE = '$fecVence', RADI_DIAS_VENCE = $diasRest WHERE RADI_NUME_RADI = " . $nurad;
                        $ok = $db->conn->Execute($sqlUpd);
                    }
                }
                               
                // si existen algun radicado grabado lo registramos
                // en el historico
                if (! empty($rad_histo)) {
                    $observa = $mensaje10;
                    $tipoTx = 53;
                    $Historico->insertarHistorico($rad_histo, $depen, $codusua, $depen, $codusua, "Incluir radicado a expediente: $numExpe ", $tipoTx);
                    $Historico->insertarHistoricoExp($numExpe, $rad_histo, $depen, $codusua, $observa, $tipoTx, 0);

                    $sqlUpd = "UPDATE RADICADO SET SGD_CAMBIO_TRD = 0 WHERE RADI_NUME_RADI = " . $nurad;
                    $ok = $db->conn->Execute($sqlUpd);

                    // retornamos el resultado
                    $accion = array(
                        'respuesta' => true,
                        'grabados' => $rad_histo,
                        'existen' => $yaexiste
                    );
                } else {
                    // retornamos el resultado
                    $accion = array(
                        'respuesta' => false,
                        'mensaje' => $mensaje4,
                        'existen' => $yaexiste
                    );
                }

                print_r(json_encode($accion));
            } else {
                salirError($mensaje18);
                return;
            }
        }
        break;

    case 6: // Retornar Serie

        // si alguno de los siguientes parametros no esta, salga.
        if (empty($depeInput)) {
            salirError($mensaje0);
            return;
        }
        $fecha_hoy = Date("d-m-Y");
        $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
        $wva = ($veractivos == 1) ? " AND M.SGD_MRD_ESTA=1 " : "";
        $sql1 = "SELECT	DISTINCT (RIGHT('000' + convert(varchar,S.SGD_SRD_CODIGO),3)+' - '+S.SGD_SRD_DESCRIP + ' - ' + CASE WHEN M.SGD_MRD_ESTA = 0 THEN '(INACTIVA)' WHEN M.SGD_MRD_ESTA = 1 THEN '(ACTIVA)' END)AS DETALLE,
								RIGHT('000' + convert(varchar,S.SGD_SRD_CODIGO),3) AS CODIGO_SERIE,
								M.SGD_MRD_ESTA
						 FROM	SGD_MRD_MATRIRD M
								JOIN SGD_SRD_SERIESRD S ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO AND S.SGD_SRD_CODIGO > 0
						 WHERE	M.DEPE_CODI = $depeInput AND
								M.SGD_SRD_CODIGO > 0 --AND m.sgd_mrd_esta = 1
						        and '" . $fecha_hoy . "' BETWEEN s.SGD_SRD_FECHINI AND s.SGD_SRD_FECHFIN AND
								M.SGD_MRD_CODIGO = (SELECT	TOP(1) M2.SGD_MRD_CODIGO 
													FROM	SGD_MRD_MATRIRD M2 
													WHERE	M2.DEPE_CODI = $depeInput AND 
															M2.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO
													ORDER BY M2.SGD_MRD_ESTA DESC) 
								$wva 
			      		 ORDER BY M.SGD_MRD_ESTA DESC, DETALLE";

        $salida = $db->conn->Execute($sql1);
        while (! $salida->EOF) {
            $result[] = array(
                "codigo" => $salida->fields["CODIGO_SERIE"],
                "nombre" => $salida->fields["DETALLE"]
            );
            $salida->MoveNext();
        }

        // consulta para retornar los usuarios que
        // pertenecen a esta depedencia
        $sql1 = "	SELECT
						     US.USUA_NOMB AS DETALLE
							--,RIGHT('0000' + convert(varchar,US.USUA_CODI), 4) AS CODIGO_USUARIO	
                            ,US.USUA_DOC AS CODIGO_USUARIO
						FROM
						    USUARIO US
						WHERE
						     US.DEPE_CODI = $depeInput
							 ORDER BY DETALLE";

        $salida = $db->conn->Execute($sql1);

        while (! $salida->EOF) {
            $usuario[] = array(
                "codigo" => $salida->fields["CODIGO_USUARIO"],
                "nombre" => $salida->fields["DETALLE"]
            );
            $salida->MoveNext();
        }

        if ($result) {
            $accion = array(
                'respuesta' => true,
                'mensaje' => $result,
                'usuario' => $usuario
            );
            print_r(json_encode($accion));
        } else {
            salirError($mensaje13);
        }

        break;

    case 7: // Retornar Usuarios

        // si alguno de los siguientes parametros no esta, salga.
        if (empty($selDependencia)) {
            salirError($mensaje0);
            return;
        }

        $sql2 = "SELECT 
				USUA.USUA_NOMB AS USUARIO
				,USUA.USUA_DOC AS CODIGO
                                FROM 
                                                   USUARIO USUA
                                                   ,SGD_USD_USUADEPE USD
                                WHERE
                                   USD.DEPE_CODI		= '$selDependencia'
                                   AND USUA.USUA_DOC 	= USD.USUA_DOC
                                   AND USUA.USUA_LOGIN = USD.USUA_LOGIN
                                   AND USUA_ESTA		= 1 
                                   ORDER BY USUA_NOMB";

        $rs2 = $db->conn->Execute($sql2);
        while (! $rs2->EOF) {
            $result[] = array(
                "codigo" => $rs2->fields["CODIGO"],
                "nombre" => $rs2->fields["USUARIO"]
            );
            $rs2->MoveNext();
        }

        if ($result) {
            $accion = array(
                'respuesta' => true,
                'mensaje' => $result
            );
            print_r(json_encode($accion));
        } else {
            salirError($mensaje16);
        }

        break;
    case 8: // Retornar tipos documentales
             // si alguno de los siguientes parametros no esta, salga.
        if (empty($depeInput) || empty($serie) || $subSerie == -1) {
            salirError($mensaje0);
            return;
        }

        /**
         * Consultar en la base de datos los respectivos
         * tipos documentales que pertenecen a esta dependencia-serie-subserie
         */
        $wtd = ($veractivos == 1) ? " AND MR.SGD_MRD_ESTA=1 " : "";
        $sql1 = "SELECT MR.SGD_TPR_CODIGO as codigo, 
                	RIGHT('0000' + convert(varchar,TP.SGD_TPR_CODIGO),4)+' - '+TP.SGD_TPR_DESCRIP as nombre 
                FROM SGD_MRD_MATRIRD MR 
                	INNER JOIN SGD_TPR_TPDCUMENTO TP ON TP.SGD_TPR_CODIGO=MR.SGD_TPR_CODIGO
                WHERE MR.DEPE_CODI=$depeInput AND MR.SGD_SRD_CODIGO=$serie AND MR.SGD_SBRD_CODIGO=$subSerie $wtd 
                ORDER BY TP.SGD_TPR_DESCRIP ";
        $salida = $db->conn->Execute($sql1);
        while (! $salida->EOF) {
            $result[] = array(
                "codigo" => $salida->fields["codigo"],
                "nombre" => $salida->fields["nombre"]
            );
            $salida->MoveNext();
        }

        if ($result) {
            $accion = array(
                'respuesta' => true,
                'mensaje' => $result
            );
            print_r(json_encode($accion));
        } else {
            salirError($mensaje17);
        }
        break;
    case 9:

        $expediente = new Expediente($db);
        $Historico = new Historico($db);
        $trd = new TipoDocumental($db);

        if (empty($rad_anex) || empty($_POST['tipoDoc'])) {
            salirError($mensaje0);
            return;
        }

        foreach ($rad_anex as $actual) {

            $db->conn->StartTrans();

            $sql = "update radicado set tdoc_codi=" . $_POST['tipoDoc'] . " where radi_nume_radi=" . $actual;
            $resp = $db->conn->Execute($sql);
            if (! $resp) {
                salirError("NO se actualizÃ³ el tipo documental del radicado");
                return;
            }
            $j = 0;
            $isqlTRD = "SELECT	SGD_MRD_CODIGO FROM SGD_MRD_MATRIRD WHERE DEPE_CODI = " . $_POST['depeInput'] . " AND SGD_SRD_CODIGO 	= " . $_POST['selectSerie'] . " AND SGD_SBRD_CODIGO = " . $_POST['selectSubSerie'] . " AND SGD_TPR_CODIGO 	= " . $_POST['tipoDoc'];

            $rsTRD = $db->conn->Execute($isqlTRD);
            if ($rsTRD) {
                while (! $rsTRD->EOF) {
                    $codiTRDS[$j] = $rsTRD->fields['SGD_MRD_CODIGO'];
                    $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
                    $j ++;
                    $rsTRD->MoveNext();
                }
            } else {
                salirError("NO existe la matriz de la TRD");
                return;
            }

            $sqlH = "SELECT RADI_NUME_RADI FROM SGD_RDF_RETDOCF r WHERE r.RADI_NUME_RADI = $actual ORDER BY SGD_RDF_FECH DESC ";
            $rsH = $db->conn->Execute($sqlH);
            $j = 0;
            if ($rsH && ! $rsH->EOF) {

                $sqlUA = " UPDATE SGD_RDF_RETDOCF SET SGD_MRD_CODIGO = $codiTRD,
							USUA_CODI = $codusua, DEPE_CODI = $depen, USUA_DOC = '$documento_Usua'
	      			  WHERE	RADI_NUME_RADI = $actual ";
                $rsUp = $db->conn->Execute($sqlUA);
            } else {
                $radicados = $trd->insertarTRD($codiTRDS, $codiTRD, $actual, $depen, $codusua);
            }

            $isqlTRD = "SELECT	S.SGD_SRD_DESCRIP, B.SGD_SBRD_DESCRIP, T.SGD_TPR_DESCRIP
                    FROM SGD_MRD_MATRIRD M INNER JOIN SGD_SRD_SERIESRD S ON M.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
                    	INNER JOIN SGD_SBRD_SUBSERIERD B ON S.SGD_SRD_CODIGO = M.SGD_SRD_CODIGO AND B.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
                    	INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = M.SGD_TPR_CODIGO
                    WHERE S.SGD_SRD_CODIGO = $serie AND B.SGD_SBRD_CODIGO = $subSerie AND T.SGD_TPR_CODIGO = " . $_POST['tipoDoc'] . " AND M.DEPE_CODI = $depen ";
            $rsTRD = $db->conn->Execute($isqlTRD);
            if ($rsTRD && ! $rsTRD->EOF) {
                $nomser = $rsTRD->fields['SGD_SRD_DESCRIP'];
                $nomsubser = $rsTRD->fields['SGD_SBRD_DESCRIP'];
                $nomtipo = $rsTRD->fields['SGD_TPR_DESCRIP'];
                $rsTRD->MoveNext();
            }

            $observa = $mensaje19 . " - " . $nomser . "/" . $nomsubser . "/" . $nomtipo;
            $radiModi = $Historico->insertarHistorico(array(
                $actual
            ), $depen, $codusua, $depen, $codusua, $observa, 32);

            // Se guarda el registro en el historico de TRD
            $queryGrabar = "INSERT INTO SGD_HMTD_HISMATDOC( SGD_HMTD_FECHA,
										RADI_NUME_RADI, USUA_CODI, USUA_DOC,
										DEPE_CODI, SGD_HMTD_OBSE, SGD_MRD_CODIGO, SGD_TTR_CODIGO)
										VALUES(	" . $db->conn->OffsetDate(0, $db->conn->sysTimeStamp) . ",
											$actual, $codusua, $documento_Usua, $depen, '$observa', $codiTRD, 32)";
            $ejecutaQuery = $db->conn->Execute($queryGrabar);
        }

        if ($db->conn->CompleteTrans()) {
            $accion = array(
                'respuesta' => true,
                'mensaje' => $mensaje19
            );
            print_r(json_encode($accion));
        } else {
            $accion = array(
                'respuesta' => false,
                'mensaje' => "Error insertando la TRD !!"
            );
            print_r(json_encode($accion));
        }

        break;
}
?>