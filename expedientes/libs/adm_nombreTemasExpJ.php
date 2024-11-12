<?php
session_start();
$ruta_raiz = "../..";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/include/tx/Historico.php";
include_once "$ruta_raiz/include/tx/Expediente.php";
require_once "$ruta_raiz/FirePHPCore/fb.php";

$db = new ConnectionHandler("$ruta_raiz");
$Historico = new Historico($db);
$expediente = new Expediente($db);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

header("Content-Type: application/json");
require_once "$ruta_raiz/FirePHPCore/fb.php";

// variables que se llegan desde el archivo adm_nombreTemasExp.js
$depe = $_POST['depe'];
$depe_hist = $_POST['depeCodiUsua']; // Se utiliza para registrar las acciones en el historico
$cod_usua = $_POST["codUsua"]; // Codigo del usuario para crear historico de expediente

$evento = $_POST['evento'];
$nomAnter = $_POST['nomb_anterior'];
$nomb_coment_3 = $_POST['nomb_coment_3'];

$numExpedi = $_POST['numExpSess'];
$numExpedi2 = $_POST['numExpedi']; // Accion para activar o inactivar caso 8 y 13
$query = $_POST['query']; // Mensaje enviado desde el autocompletar
$ano_post = $_POST['ano_busq']; // se requiere para buscar expedientes es obligatoria
$selectSerie = $_POST['selectSerie']; // se requiere para filtrar la busqueda del expedientes
$selectSubSerie = $_POST['selectSubSerie']; // se requiere para filtrar la busqueda del expedientes

$nomb_Expe_300 = $_POST['nomb_Expe_300']; // Nuevo nombre para el expediente
$accion_Exp = $_POST['accion']; // caso 8 si es 0 Activa un expediente si es 1 lo inactiva
$comentario = $_POST['comentario']; // Comentario enviado para registrar en el historico de los casos 8
$rang_ini = str_pad($_POST['rang_ini'], 5, 0, STR_PAD_LEFT); // Rang_Incio secuencia de expedientes caso 8
$rang_fin = str_pad($_POST['rang_fin'], 5, 0, STR_PAD_LEFT); // Rang_fin secuencia de expedientes caso 8

$filtro_busq = $_POST['filtro_busq']; // Filtro para buscar expedientes por inactivos o activos

$rad_num = $_POST['rad_num']; // Caso numero 7 busqueda de exp y radi para excluir
$excluRad = $_POST['excluRad']; // Caso 10 envio de radicados a excluir
$activRad = $_POST['activRad']; // Caso 8 variable con expedietes para activar o inactivar

$todos = (empty($_POST['todos'])) ? "SE.SGD_SEXP_ESTADO = 0 AND" : ''; // Filtra los expedientes activos

$textoEtiq = $_POST['texto'];
$nomProye1 = $_POST['nomb_proyecto1'];
$nomProye2 = $_POST['nomb_proyecto2'];

$mensaje0 = "Dependencia y acción desconocida";
$mensaje1 = "Nombre de proyecto sin datos";
$mensaje2 = "No se pudo crear el registro \n :: Puede estar duplicado ::";
$mensaje3 = "Se inserto el nombre: ";
$mensaje4 = "No se realizó el cambio de nombre";
$mensaje6 = "Se cambió el nombre: ";
$mensaje7 = "No se cambió el nombre \n ::Puede estar duplicado ::";
$mensaje8 = "Se eliminó el nombre de proyecto ";
$mensaje9 = "No se eliminó el nombre";
$mensaje10 = "No se encontró el No. del expediente";
$mensaje11 = "No se realizó el cambio \n datos en blanco";

$mensaje81 = "El comentario esta sin datos";
$mensaje82 = "No se realizó el cambio. Compruebe que \n el expediente no tenga radicados activos";

$mensaje83 = "Rango no es correcto o \n no selecciono un expediente";

$mensaje91 = "No existe número de Radicado o de Expediente";
$mensaje92 = "No existe ningún Registro o el expediente no \n tiene ningún radicado incluido";

$mensaje101 = "No se excluyó el radicado del expediente";
$mensaje102 = "Se excluyeron los radicados seleccionados del (os) expediente (s)";
$mensaje103 = "No se seleccionó un radicado \n o el comentario esta sin datos";
$mensaje104 = "Se incluyeron los radicados seleccionados al expediente ";
$mensaje105 = "No se incluyó el radicado del expediente";
$mensaje111 = "No existe ningún Registro \n o el expediente tiene radicados activos.";

$observa = "Cambio de nombre del expediente por el de: ";
$observa2 = "Excluir radicado de Expediente: ";

// Funcion: Error para ser leido por adm_nombreTemasExp.js
function salirError($mensaje)
{
    $accion = array(
        'respuesta' => false,
        'mensaje' => $mensaje
    );
    print_r(json_encode($accion));
}

// Filtrar caracteres extranos en textos
function strValido($string)
{
    $arr = array(
        '/[^\w:()\sáéíóúÁÉÍÓÚ=#\-,.;ñÒ]+/',
        '/[\s]+/'
    );
    $asu = preg_replace($arr[0], '', $string);
    return strtoupper(preg_replace($arr[1], ' ', $asu));
}

// Consulta si el radicado esta incluido en el expediente.
function validaExisteEnExp($expediente,$radicado,$numExpe)
{
    $existeEnExp = $expediente->expedientesRadicado($radicado);
    foreach ($existeEnExp as $value){
        if ($value == $numExpe){
            return true; 	//existe en el expediente
        }
    }
    return false; //No existe en el expediente
}	

// Seleccionar numero del expediente y texto cuando se realiza
// la busqueda de un expediente
$query = strValido($query);

$var1 = strspn($query, "1234567890eE");
$nuExpediente = trim(substr($query, 0, $var1));

// Filtros para buscar y cambiar nombres de expedientes
$busq_Serie = (empty($selectSerie)) ? '' : "SE.SGD_SRD_CODIGO  =  $selectSerie AND";
$busq_SubSe = (empty($selectSubSerie)) ? '' : "SE.SGD_SBRD_CODIGO =  $selectSubSerie AND";
$ano_busq = (empty($ano_post)) ? '' : "SE.SGD_SEXP_ANO 	  =  $ano_post 	AND";

if ($filtro_busq == 0) {
    $filtro_busq = "";
} elseif ($filtro_busq == 1) {
    $filtro_busq = "SE.SGD_SEXP_ESTADO = 0 AND";
} elseif ($filtro_busq == 2) {
    $filtro_busq = "SE.SGD_SEXP_ESTADO = 1 AND";
}

// validar variables para iniciar proceso
if (empty($depe) || empty($evento)) {
    salirError($mensaje0);
    return;
}

// Ejecuta acciones que llegan desde adm_nombreTemasExp.js
switch ($evento) {
    case 1: // Buscar expedientes
        
        $query = preg_replace('/^\s/', '', $query);
        if (strlen($query) == 0)
            return;
        
        $sqlE = "SELECT top 30 convert(varchar(20),SE.SGD_EXP_NUMERO)
		+ ' ' + SE.SGD_SEXP_PAREXP1
		+ ' ' + SE.SGD_SEXP_PAREXP2
		+ ' ' + SE.SGD_SEXP_PAREXP3
		+ ' ' + SE.SGD_SEXP_PAREXP4
		+ ' ' + SE.SGD_SEXP_PAREXP5
		AS EXPEDIENTE
	FROM SGD_SEXP_SECEXPEDIENTES SE
	WHERE
		$todos
		$busq_Serie						
		$busq_SubSe			
		$ano_busq				
		SE.DEPE_CODI = $depe												
			AND (  SE.SGD_SEXP_PAREXP1 LIKE '%$query%'
				OR SE.SGD_SEXP_PAREXP2 LIKE '%$query%'
				OR SE.SGD_SEXP_PAREXP3 LIKE '%$query%'
				OR SE.SGD_SEXP_PAREXP4 LIKE '%$query%'
				OR SE.SGD_SEXP_PAREXP5 LIKE '%$query%')						
	ORDER BY 1";
        
        $salida = $db->conn->Execute($sqlE);
        $result = array();
        while (! $salida->EOF && ! empty($salida)) {
            
            $nombExp = preg_replace('/\s/', ' ', str_replace("-", "", $salida->fields["EXPEDIENTE"]));
            
            if (! empty($nombExp)) {
                $result[] = $nombExp;
            }
            $salida->MoveNext();
        }
        if (count($result) > 0)
        for ($i = 0; $i < count($result); $i ++) {
            print "$result[$i]\n";
        }
        
        break;
    
    case 2: // Cambiar nombre expedientes
        
        if (empty($nuExpediente) || empty($nomb_Expe_300)) {
            salirError($mensaje11);
            return;
        }
        
        // Comprobar si el numero del expediente enviado existe
        $sqlCam = " SELECT 
							COUNT(*) AS TOTAL 
						FROM 
							SGD_SEXP_SECEXPEDIENTES SE
						WHERE			
							$todos				
							$busq_Serie							
							$busq_SubSe			
							$ano_busq					
							SE.DEPE_CODI = $depe							
							AND SE.SGD_EXP_NUMERO like '$nuExpediente'";
        
        $rs = $db->conn->query($sqlCam);
        
        if (! empty($rs->EOF)) {
            salirError($mensaje10);
            return;
        } else {
            $unique = $rs->fields("TOTAL");
        }
        $salida = strValido($nomb_Expe_300);
        if ($unique === 1) {
            $insercioNomExp = $expediente->insert_ExpedienteNomb($nuExpediente, strtoupper($nomb_Expe_300));
            
            $tipoTx = 67;
            $radicados[] = 0;
            
            $Historico->insertarHistoricoExp($nuExpediente, $radicados, $depe_hist, $cod_usua, $observa . $nomb_Expe_300, $tipoTx, 0);
            
            $accion = array(
                'respuesta' => true,
                'mensaje' => $mensaje3 . $nomProye1
            );
            print_r(json_encode($accion));
        } else {
            salirError($mensaje4);
            return;
        }
        break;
    
    case 3: // Crear proyecto
        $nomProye1 = substr(trim(strValido($nomProye1)), 0, 150);
        $nomProye2 = substr(trim(strValido($nomProye2)), 0, 150);
        if (empty($nomProye1) || (strlen($nomProye1) < 4) || empty($nomProye2) || (strlen($nomProye2) < 4)) {
            salirError($mensaje1);
            return;
        } else {
            $sql_insert = "INSERT INTO 
								SGD_EPRY_EPROYECTO (SGD_EPRY_NOMBRE, SGD_EPRY_NOMBRE_CORTO, DEPE_CODI)
							VALUES(
								'$nomProye1', '$nomProye2','$depe')";
            $out = $db->conn->Execute($sql_insert);
            if ($out->EOF) {
                $accion = array(
                    'respuesta' => true,
                    'mensaje' => $mensaje3 . $nomProye1
                );
                print_r(json_encode($accion));
            } else {
                salirError($mensaje2);
                return;
            }
        }
        break;
    
    case 4: // Modificar proyecto
        $nomProye1 = substr(trim(strValido($nomProye1)), 0, 150);
        $nomProye2 = substr(trim(strValido($nomProye2)), 0, 150);
        $nomAnter = strValido($nomAnter);
        if (empty($nomAnter)) {
            salirError($mensaje4);
            return;
        }
        ;
        
        if (empty($nomProye1) || (strlen($nomProye1) < 4) || empty($nomProye2) || (strlen($nomProye2) < 4)) {
            salirError($mensaje1);
            return;
        } else {
            $sql_cambiar = "	UPDATE 
										SGD_EPRY_EPROYECTO
									SET 
										SGD_EPRY_NOMBRE = '$nomProye1',
										SGD_EPRY_NOMBRE_CORTO = '$nomProye2'
									WHERE 
										SGD_EPRY_CODIGO = '$nomAnter'
										AND DEPE_CODI 	= '$depe'";
            
            $out = $db->conn->Execute($sql_cambiar);
            if ($out->EOF) {
                $accion = array(
                    'respuesta' => true,
                    'mensaje' => $mensaje6 . $nomProye1
                );
                print_r(json_encode($accion));
            } else {
                salirError($mensaje7);
                return;
            }
        }
        
        break;
    
    case 5: // borrar proyecto
        $nomAnter = strValido($nomAnter);
        if (empty($nomAnter)) {
            salirError($mensaje4);
            return;
        }
        
        $sql_valid = " SELECT count(e.SGD_EPRY_CODIGO) as CANTIDAD
			FROM 
				SGD_SEXP_SECEXPEDIENTES SE e
			WHERE
				e.SGD_EPRY_CODIGO = $nomAnter";
        $regExist = $db->conn->Execute($sql_valid);
        $existeReg = $regExist->fields["CANTIDAD"];
        
        if (empty($existeReg)) {
            $sql_borrar = "	DELETE FROM SGD_EPRY_EPROYECTO
				WHERE SGD_EPRY_CODIGO = '$nomAnter'
					AND DEPE_CODI 	= '$depe'";
            
            $out = $db->conn->Execute($sql_borrar);
            if ($out->EOF) {
                $accion = array(
                    'respuesta' => true,
                    'mensaje' => $mensaje8
                );
                print_r(json_encode($accion));
            } else {
                salirError($mensaje9);
                return;
            }
        } else {
            salirError($mensaje16);
            return;
        }
        
        break;
    
    case 6: // buscar nombres a partir del numero del expediente
        
        if (empty($nuExpediente))
            return;
        
        $sqlE = "
					SELECT      SE.SGD_SEXP_PAREXP1
						+ ' ' + SE.SGD_SEXP_PAREXP2
						+ ' ' + SE.SGD_SEXP_PAREXP3
						+ ' ' + SE.SGD_SEXP_PAREXP4
						+ ' ' + SE.SGD_SEXP_PAREXP5
						AS EXPEDIENTE
					FROM SGD_SEXP_SECEXPEDIENTES SE
					WHERE
						$todos
						$busq_Serie						
						$busq_SubSe			
						$ano_busq					
						SE.DEPE_CODI = $depe						
						AND SE.SGD_EXP_NUMERO LIKE '$nuExpediente'";
        
        $salida = $db->conn->Execute($sqlE);
        if (empty($salida->EOF)) {
            $nombExp = trim(preg_replace('/\s/', ' ', str_replace("-", "", $salida->fields["EXPEDIENTE"])));
            $detalle = (strlen($nombExp) > 4) ? $nombExp : 'No tiene nombre asignado';
            
            $accion = array(
                'respuesta' => true,
                'mensaje' => $detalle
            );
            print_r(json_encode($accion));
        } else {
            salirError($mensaje10);
            return;
        }
        
        break;
    
    case 7: // Cambiar el nombre del expediente desde panel de expediente
        $insercioNomExp = $expediente->insert_ExpedienteNomb($numExpedi, strValido($nomb_Expe_300));
        
        $tipoTx = 67;
        $radicados[] = 0;
        $Historico->insertarHistoricoExp($numExpedi, $radicados, $depe, $cod_usua, $observa . $nomb_Expe_300, $tipoTx, 0);
        
        $accion = array(
            'respuesta' => true
        );
        print_r(json_encode($accion));
        break;
    
    case 8: // Activar un expediente
        
        $comentario = strValido($comentario);
        $numExpedi = strValido($numExpedi2);
        $tipoTx = empty($accion_Exp) ? 68 : 66;
        
        if (empty($comentario)) {
            salirError($mensaje81);
            return;
        }
        
        if (empty($numExpedi)) {
            // validar datos de rango
            $cantExp = $rang_fin - $rang_ini;
            $exp_error = null; // para validar en javascript al retornar respuesta
            if (empty($depe) || empty($ano_post) || empty($selectSerie) || empty($selectSubSerie) || empty($rang_ini) || empty($rang_fin) || empty($activRad) || empty($cantExp)) {
                
                salirError($mensaje83);
                return;
            } else {
                // Con los expedientes seleccionamos en $activRad
                foreach ($activRad as $value) {
                    
                    $val_acti = $expediente->estado_Expediente($value, $accion_Exp);
                    
                    if ($val_acti == 1) {
                        $radicados[] = 0;
                        $Historico->insertarHistoricoExp($value, $radicados, $depe_hist, $cod_usua, $comentario, $tipoTx, 0);
                    } else {
                        $exp_error .= $numExpedi . "\n";
                    }
                }
                
                $accion = array(
                    'respuesta' => true,
                    'mensaje' => '',
                    'norealizados' => $exp_error
                );
                
                print_r(json_encode($accion));
                return;
            }
        } else {
            
            $val_acti = $expediente->estado_Expediente($numExpedi, $accion_Exp);
            
            if ($val_acti == 1) {
                $radicados[] = 0;
                $Historico->insertarHistoricoExp($numExpedi, $radicados, $depe_hist, $cod_usua, $comentario, $tipoTx, 0);
                
                $accion = array(
                    'respuesta' => true,
                    'mensaje' => $numExpedi,
                    'norealizados' => $exp_error
                );
                print_r(json_encode($accion));
            } else {
                salirError($mensaje82);
                return;
            }
        }
        break;
    
    case 9: // Buscar expedientes y radicados
        
        $numExpedi = strValido($numExpedi2);
        $rad_num = strValido($rad_num);
        
        if (empty($numExpedi) && empty($rad_num)) {
            salirError($mensaje91);
            return;
        }
        
        $sql_numBus = empty($numExpedi) ? "EX.RADI_NUME_RADI = $rad_num AND" : "EX.SGD_EXP_NUMERO LIKE '$numExpedi' AND";
        
        $sql_bus = "	SELECT 
								SE.SGD_EXP_NUMERO AS NUMERO_EXPE,
			                                SE.SGD_SEXP_PAREXP1
									+ ' ' + SE.SGD_SEXP_PAREXP2
									+ ' ' + SE.SGD_SEXP_PAREXP3
									+ ' ' + SE.SGD_SEXP_PAREXP4
									+ ' ' + SE.SGD_SEXP_PAREXP5
								AS NOMB_EXPEDIENTE,
       							EX.RADI_NUME_RADI AS NUMERO_RADI

							FROM 
								SGD_EXP_EXPEDIENTE EX,
							    SGD_SEXP_SECEXPEDIENTES SE 
     
							WHERE
								$todos
								$busq_Serie								
								$busq_SubSe	
								$sql_numBus
								$ano_busq	
								SE.DEPE_CODI 		= $depe 		AND
								EX.SGD_EXP_ESTADO	= 0				AND
     							SE.SGD_EXP_NUMERO = EX.SGD_EXP_NUMERO";
        
        $result = $db->conn->Execute($sql_bus);
        
        if (! $result->EOF) {
            while (! $result->EOF) {
                $nombExp = preg_replace('/\s/', ' ', str_replace("-", "", $result->fields["NOMB_EXPEDIENTE"]));
                $numRad = $result->fields["NUMERO_RADI"];
                $numExp = $result->fields["NUMERO_EXPE"];
                
                $arrSalida[] = array(
                    'nombExp' => trim($nombExp),
                    'numRad' => $numRad,
                    'numExp' => $numExp
                );
                
                $result->MoveNext();
            }
        } else {
            salirError($mensaje92);
            return;
        }
        
        $accion = array(
            'respuesta' => true,
            'mensaje' => $arrSalida
        );
        
        print_r(json_encode($accion));
        break;
    
    case 10: // Excluir radicado de expedientes
        
        if (empty($excluRad)) {
            salirError($mensaje103);
            return;
        }
        
        foreach ($excluRad as $value) {
            $datos[] = preg_split('/_/', $value, - 1, PREG_SPLIT_NO_EMPTY);
        }
        
        $nomb_coment_3 = strValido($nomb_coment_3);
        $cant_datos = count($datos);
        
        if (empty($cant_datos) || empty($nomb_coment_3)) {
            salirError($mensaje103);
            return;
        }
        
        for ($i = 0; count($datos) > $i; $i ++) {
            $resultadoExp = $expediente->excluirExpediente($datos[$i][1], $datos[$i][0]);
            
            if ($resultadoExp == 1) {
                $radicados[] = $datos[$i][1];
                $tipoTx = 52;
                $Historico->insertarHistoricoExp($datos[$i][0], $radicados, $depe_hist, $cod_usua, $observa2 . $nomb_coment_3, $tipoTx, 0);
            } else {
                salirError($mensaje105);
                return;
            }
        }
        $accion = array(
            'respuesta' => true,
            'mensaje' => $mensaje102
        );
        print_r(json_encode($accion));
        
        break;
    
    case 11: // buscar por rango los expedientes que no tiene
        {
            if (empty($depe) || empty($ano_post) || empty($selectSerie) || empty($selectSubSerie) || empty($rang_ini) || empty($rang_fin)) {
                salirError($mensaje83);
                return;
            }
            
            $incNumExp = strValido($rang_ini);
            $finNumExp = strValido($rang_fin);
            
            $numExpedini = $ano_post . $depe . $selectSerie . $selectSubSerie . $incNumExp . "E";
            $numExpedfin = $ano_post . $depe . $selectSerie . $selectSubSerie . $finNumExp . "E";
            
            $sql_bus = "	SELECT
      							SE.SGD_EXP_NUMERO AS NUMERO_EXPE,
						      	(MIN(SE.SGD_SEXP_PAREXP1) + ' ' + 
								MIN(SE.SGD_SEXP_PAREXP2) + ' ' + 
								MIN(SE.SGD_SEXP_PAREXP3)  + ' ' + 
								MIN(SE.SGD_SEXP_PAREXP4) + ' ' + 
								MIN(SE.SGD_SEXP_PAREXP5)) AS NOMB_EXPEDIENTE,
								(MIN(CASE SE.SGD_SEXP_ESTADO WHEN 0 THEN 'Activo' WHEN 1 THEN 'Inactivo' END)) AS ESTADO_EXP,
						      	SUM(EX.RADI_NUME_RADI) AS RAD_INCLUIDOS
								
							FROM
    							SGD_SEXP_SECEXPEDIENTES SE LEFT OUTER JOIN SGD_EXP_EXPEDIENTE EX
    							ON (SE.SGD_EXP_NUMERO = EX.SGD_EXP_NUMERO AND EX.SGD_EXP_ESTADO <> 2)
								     
							WHERE															
								$ano_busq
								$busq_Serie								
								$busq_SubSe
								$filtro_busq															
								SE.DEPE_CODI 	  = $depe 		AND								
								SE.SGD_EXP_NUMERO BETWEEN '$numExpedini' and '$numExpedfin'
								
								GROUP BY SE.SGD_EXP_NUMERO";
            
            $result = $db->conn->Execute($sql_bus);
            
            if (! $result->EOF) {
                while (! $result->EOF) {
                    $numeroExp = $result->fields["NUMERO_EXPE"];
                    $nombreExp = preg_replace('/\s/', ' ', str_replace("-", "", $result->fields["NOMB_EXPEDIENTE"]));
                    $estadoExp = $result->fields["ESTADO_EXP"];
                    $radInclu = $result->fields["RAD_INCLUIDOS"];
                    
                    if (empty($radInclu)) {
                        $arrSalida[] = array(
                            'numExp' => $numeroExp,
                            'nombExp' => trim($nombreExp),
                            'estadoExp' => $estadoExp
                        );
                    }
                    $result->MoveNext();
                }
                
                $accion = array(
                    'respuesta' => true,
                    'mensaje' => $arrSalida
                );
                
                print_r(json_encode($accion));
            } else {
                salirError($mensaje111);
                return;
            }
        }
        break;
    case 12: // Buscar radicados para despues incluirlos en expediente.
        {
            $numExpedi = strValido($numExpedi2);
            $rad_num = strValido($rad_num);
            
            if (empty($numExpedi) || empty($rad_num)) {
                salirError($mensaje91);
                return;
            }
            
            $sql_bus = "	SELECT RADI_NUME_RADI, RA_ASUN
							FROM RADICADO     
							WHERE RADI_NUME_RADI IN ($rad_num)";
            
            $result = $db->conn->Execute($sql_bus);
            
            if (! $result->EOF) {
                while (! $result->EOF) {
                    $nomRad = preg_replace('/\s/', ' ', str_replace("-", "", $result->fields["RA_ASUN"]));
                    $numRad = $result->fields["RADI_NUME_RADI"];
                    $arrSalida[] = array(
                        'nomRad' => trim($nomRad),
                        'numRad' => $numRad
                    );
                    $result->MoveNext();
                }
            } else {
                salirError($mensaje92);
                return;
            }
            $accion = array(
                'respuesta' => true,
                'mensaje' => $arrSalida
            );
            print_r(json_encode($accion));
        }
        break;
    case 13: // incluir Radicados en expediente
        {
            if (empty($incluRad5)) {
                salirError($mensaje103);
                return;
            }
            
            foreach ($incluRad5 as $value) {
                $datos[] = preg_split('/_/', $value, - 1, PREG_SPLIT_NO_EMPTY);
            }
            
            $nomb_coment_5 = strValido($nomb_coment_5);
            $cant_datos = count($datos);
            
            if (empty($cant_datos) || empty($nomb_coment_5) || empty($numExpedi2)) {
                salirError($mensaje103);
                return;
            }
            include_once ORFEOPATH . "class_control/TipoDocumental.php";
            $trd = new TipoDocumental($db);
            $destCorreosPARA = array();
            $yaexiste = array();
            for ($i = 0; count($datos) > $i; $i ++) {
                $actual= $datos[$i][0];
                $existeEn = validaExisteEnExp($expediente, $actual, $numExpedi2);
                if ($existeEn == false) {
                    
                    //Para los radicados que ya están incluidos en expedientes con la correspondiente tipificación y se incluyan en otro expediente con series y
                    //tipificación diferente este debe generar una notificación a la persona del Grupo de Biblioteca y Archivo (Sandra Arango),
                    //(en realidad es a quien tenga el permiso USUA_NOTIF_RADEXP) informando sobre esta actividad "radicado incluido en otro expediente"
                    $vecExisteRadEnExp = $expediente->expedientesRadicado($actual);
                    if (  $vecExisteRadEnExp[0] <> 0 )  {
                        //Realizamos lógica de envio de notificación.
                        $sql = "SELECT USUA_EMAIL FROM usuario WHERE USUA_NOTIF_RADEXP=1";
                        $ADODB_COUNTRECS = TRUE;
                        $tmpRs = $db->conn->CacheExecute(15, $sql);
                        if ($tmpRs->recordCount() > 0) {
                            foreach($tmpRs as $x => $fila) {
                                $destCorreosPARA[] = $fila['USUA_EMAIL'];
                            }
                            $asunto = "SGD Orfeo. Radicado $actual incluido en otro Expediente $numExpedi2";
                            $cuerpo = "<table width='80%'><th><tr><td><img src='https://orfeo.dnp.gov.co/img/escudo.jpg'></td><td><b>Comunicaci&oacute;n Oficial.</b></td>
								<tr><td colspan='2' style='font-family: verdana; font-size: 75%'><br/><br/>
								Estimado(a) lectores:<br/><br/><br/>El radicado $actual se incluy&oacute; en el expediente $numExpedi2, .
								</td><tr>
								<tr><td colspan='2'><b>***Importante: Por favor no responda a este correo electr&oacute;nico. Esta cuenta no permite recibir correo.</b></td></tr>
								</table>";
                            $enviarCorreo = $objCorreo->enviarCorreo($destCorreosPARA, $destCorreosCC, $cco, $asunto, $cuerpo);
                        }
                    }
                    
                    $rad_histo = array();
                    $saliExp = $expediente->insertar_expediente($numExpedi2, $actual, $depe_hist, $_SESSION['codusuario'], $_SESSION['usua_doc']);
                    if ($saliExp == 1){
                        $rad_histo[] = $actual;
                    }
                    
                    $sql = "update radicado set tdoc_codi=".$_POST['codTdoc']." where radi_nume_radi=".$actual;
                    $db->conn->Execute($sql);
                    
                    $isqlTRD = "SELECT	SGD_MRD_CODIGO FROM SGD_MRD_MATRIRD WHERE DEPE_CODI = ".$_POST['depe'].
                    " AND SGD_SRD_CODIGO 	= ".$_POST['selectSerie'].
                    " AND SGD_SBRD_CODIGO = ".$_POST['selectSubSerie'].
                    " AND SGD_TPR_CODIGO 	= ".$_POST['codTdoc'];
                    
                    $rsTRD = $db->conn->Execute($isqlTRD);
                    $j = 0;
                    
                    while(!$rsTRD->EOF) {
                        $codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
                        $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
                        $j++;
                        $rsTRD->MoveNext();
                    }
                    $radicados = $trd->insertarTRD($codiTRDS, $codiTRD, $actual, $depe_hist, $_POST['codUsua']);
                    
                    $codiRegH = array();
                    $sqlH ="SELECT	$radi_nume_radi RADI_NUME_RADI
							FROM	SGD_RDF_RETDOCF r
							WHERE	r.RADI_NUME_RADI = $actual
									AND r.SGD_MRD_CODIGO = $codiTRD";
                    $rsH = $db->conn->Execute($sqlH);
                    $j = 0;
                    while(!$rsH->EOF) {
                        $codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
                        $j++;
                        $rsH->MoveNext();
                    }
                    
                    //obtenemos el nombre del tipo documental actual
                    $coditrdx ="SELECT	s.SGD_TPR_DESCRIP as TPRDESCRIP
								FROM	RADICADO r,
										SGD_TPR_TPDCUMENTO s
								WHERE	r.TDOC_CODI = s.SGD_TPR_CODIGO AND
										r.RADI_NUME_RADI = $actual";
                    $res_coditrdx = $db->conn->Execute($coditrdx);
                    $TDCactu = $res_coditrdx->fields['TPRDESCRIP'];
                    
                    $Historico = new Historico($db);
                    $observa   = "Tipo documental anterior: ". $TDCactu;
                    $radiModi  = $Historico->insertarHistorico($codiRegH,
                        $depe_hist,
                        $_POST['codUsua'],
                        $depe_hist,
                        $_POST['codUsua'],
                        $observa,
                        32);
                    $codiRegH = null;
                    
                    //Se guarda el registro en el historico de TRD
                    $queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC( SGD_HMTD_FECHA,
										RADI_NUME_RADI, USUA_CODI, USUA_DOC,
										DEPE_CODI, SGD_HMTD_OBSE, SGD_MRD_CODIGO, SGD_TTR_CODIGO)
										VALUES(	".$db->conn->sysTimeStamp.",
											$actual, $codUsua, $usuaDoc, $depe, 'Se inserta TRD', $codiTRD, 32)";
					$ejecutarQuerey	= $db->conn->Execute($queryGrabar);
											
					$ADODB_COUNTRECS = FALSE;
                } else {
                    $yaexiste[] = $actual;
                }
            }

            $realizados = (count($rad_histo)>0) ? $mensaje104 . "(" . implode(',', $rad_histo) .")." : "";
            $noRealizados = (count($yaexiste)>0) ? " Radicado(s) ".implode(',', $yaexiste). " ya estaba(n) en el expediente." : "";
            $accion = array(
                'respuesta' => true,
                'mensaje' => $realizados . $noRealizados
            );
            print_r(json_encode($accion));
        }break;
	case 14:
	{	//Buscar Expediente por código
		$band = false;
		$tmpNombExp= " Expediente $numExpedi no encontrado.";
		
		$sql = "select SGD_SEXP_PAREXP1 + ' ' + SGD_SEXP_PAREXP2 as nexp from SGD_SEXP_SECEXPEDIENTES WHERE LTRIM(SGD_EXP_NUMERO) = '$numExpedi'";
		$rs = $db->conn->Execute($sql);
		
		if ($rs && !$rs->EOF) {
			$band = true;
			$tmpNombExp =  $rs->fields['nexp'];
		}
		$accion= array( 'respuesta' => $band,
						'mensaje'	=> $tmpNombExp );
		print_r(json_encode($accion));
	}break;
	case 15:
	{	//Migrar Expedientes
		$db->conn->StartTrans();
		//Iniciamos con radicados
		$cnt = 0;
		$sqlr="select radi_nume_radi from sgd_exp_expediente where sgd_exp_numero='". $_POST['expori']. "'";	// $_POST['expdes'] 
		$rs = $db->conn->Execute($sqlr);
		if ($rs)
		while ($arr = $rs->FetchRow()) {
		    $cnt ++;
			$existeEn = validaExisteEnExp($expediente, $arr['radi_nume_radi'], $_POST['expdes']);
			if ($existeEn == false) {
				//realizamos la inserción en el nuevo expediente
				$insExp = $expediente->insertar_expediente( $_POST['expdes'], $arr['radi_nume_radi'], $_POST['depe'], $_POST['codUsua'], $_POST['usuaDoc']);
				if ($insExp == 1) {
					$rad_insertados[] = $arr['radi_nume_radi'];
				}
				//realizamos la exclusión del radicado en el expediente
				$excExp = $expediente->excluirExpediente($arr['radi_nume_radi'], $_POST['expori']);
				if ($excExp == 1) {
				    $rad_excluidos[] = $arr['radi_nume_radi'];
				}
			} else {
			    $rad_existente[] = $arr['radi_nume_radi'];
			    //realizamos la exclusión del radicado en el expediente
			    $excExp = $expediente->excluirExpediente($arr['radi_nume_radi'], $_POST['expori']);
			    if ($excExp == 1) {
			        $rad_excluidos[] = $arr['radi_nume_radi'];
			    }
			}
		}
		
		//si existen radicados insertados ... les registramos en el histórico la transaccion de migración
		if(!empty($rad_insertados)) {
			$tipoTx = 53;			
			$okri = $Historico->insertarHistoricoExp($_POST['expdes'], $rad_insertados, $depe, $cod_usua, $_POST['comen'], $tipoTx, 0);		         				
		}
		//si existen radicados excluidos ... les registramos en el histórico la transaccion de migración
		if(!empty($rad_excluidos)) {
			$tipoTx = 52;
			$pkree = $Historico->insertarHistoricoExp($_POST['expori'], $rad_excluidos, $depe, $cod_usua, $_POST['comen'], $tipoTx, 0);
			$pkre = $Historico->insertarHistorico($rad_excluidos, $depe, $cod_usua, $depe, $cod_usua, "Expediente origen " .$_POST['expori'].". ". $_POST['comen'], $tipoTx);
		}
		//Se inactiva el expediente migrado.
		$expediente->estado_Expediente($_POST['expori'], 1);

		//continuamos con los anexos
		$sqla="update sgd_anexos_exp set sgd_exp_numero='" . $_POST['expdes'] . "' where sgd_exp_numero='". $_POST['expori']. "'";
		$okae = $db->conn->Execute($sqla);
		
		//Actualizamos expediente con la migración
		//Creamos el anexo explicatorio de la migración.
		require "$ruta_raiz/class_control/usuario.php";
		$objUsr = new Usuario($db);
		$objUsr->usuarioDependecina($depe, $cod_usua);
		
		$contenido =  "En fecha ".date('Y-m-d H:m:i'). " el usuario ".$objUsr->usua_login." de la dependencia ".$_SESSION['depe_nomb']." realizo la migracion de ".
		  		"contenido del expediente ".$_POST['expori']. " al expediente ".$_POST['expdes']. " con el siguiente ".
		  		"comentario: ".$_POST['comen'];
		$sqlm="update sgd_sexp_secexpedientes set sgd_sexp_migradoestado=1, sgd_sexp_migradodescri='". $contenido. "' where sgd_exp_numero='".$_POST['expori']."'";
		$okce = $db->conn->Execute($sqlm);
		
		if ($db->conn->CompleteTrans()) {
		    $accion = array( 'respuesta' => true,
		        'mensaje'	=> 'Expediente '. $_POST['expori']. ' migrado correctamente.' );
		} else {
		    $accion = array( 'respuesta' => false,
		        'mensaje'	=> 'Expediente '.$_POST['expori']. ' no migrado.' );
		}
		print_r(json_encode($accion));
	}break;
}
?>