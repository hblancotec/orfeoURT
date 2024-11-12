<?php
session_start();
$ruta_raiz = ".";
if (count($_SESSION) == 0) {
    die(include $ruta_raiz."/sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}

/**
 * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 *
 * @param char $var
 * @return numeric
 */
include_once $ruta_raiz.'/_conf/constantes.php';
    
function getNumPagesInPDF($file) 
{ 
    //http://www.hotscripts.com/forums/php/23533-how-now-get-number-pages-one-document-pdf.html 
    if(!file_exists($file)) return null; 
    if (!$fp = @fopen($file,"r")) return null; 
    $max=0; 
    while(!feof($fp)) 
    { 
        $line = fgets($fp,255); 
        if (preg_match('/\/Count [0-9]+/', $line, $matches))
        { 
            preg_match('/[0-9]+/',$matches[0], $matches2); 
            if ($max<$matches2[0]) $max=$matches2[0]; 
        } 
    } 
    fclose($fp); 
    return (int)$max; 
} 

function return_bytes($val) {
    $val = trim($val);
    $ultimo = strtolower($val{strlen($val)-1});
    switch($ultimo) {
        // El modificador 'G' se encuentra disponible desde PHP 5.1.0
        case 'g':	$val *= 1024;
        case 'm':	$val *= 1024;
        case 'k':	$val *= 1024;
    }
    return $val;
}


//La configuracion de upload_max_filesize debe estar en Bytes. Si tiene 15728640 es porque equivale a 15MB
if ((!$codigo && $_FILES['userfile1']['size']==0) ||
        ($codigo && $_FILES['userfile1']['size']>=ini_get('upload_max_filesize'))) {
    die("<table>\n
            <tr>\n
                <td>El tama&ntilde;o del archivo no es correcto.</td>\n
            </tr>\n
            <tr>\n
                <td>\n
                    <li>se permiten archivos de " . ini_get('upload_max_filesize') . " m&aacute;ximo.</li>
                </td>\n
            </tr>\n
            <tr>\n
                <td>\n
                    <input type='button' value='cerrar' onclick='opener.regresar();window.close();'>
                </td>\n
            </tr>\n
        </table>\n");
}

$fechaHoy = Date("Y-m-d");
if (!$ruta_raiz) $ruta_raiz= ".";
include_once ORFEOPATH . "class_control/anexo.php";
include_once ORFEOPATH . "class_control/anex_tipo.php";
    
include("$ruta_raiz/config.php");
if (!isset($_SESSION['dependencia']))	include "./rec_session.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;
$sqlFechaHoy = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
//$db->conn->debug = true;
$anex = new Anexo($db);
$anexTip = new Anex_tipo($db);

if (!$aplinteg)
    $aplinteg = 'null';
if (!$tpradic)
    $tpradic  = 'null';
    
if(!$cc) {
	if($codigo)
        $nuevo="no";
    else
        $nuevo="si";
    if ($sololect)
        $auxsololect="S";
    else
        $auxsololect="N";
       
    //$db->conn->BeginTrans();
	//$db->conn->debug = true;
    if($nuevo=="si") {
        $auxnumero = $anex->obtenerMaximoNumeroAnexo($radi);
        do {
            $auxnumero+=1;
            $codigo=trim($radi).trim(str_pad($auxnumero,5,"0",STR_PAD_LEFT));
        } while ($anex->existeAnexo($codigo));
    } else {
        $bien = true;
        $auxnumero = substr($codigo,-4);
        $codigo = trim($radi) . trim(str_pad($auxnumero,5,"0",STR_PAD_LEFT));
    }
        
    if($radicado_salida) {
        $anex_salida = 1;
    } else {
        $anex_salida = 0;
    }
        
    $bien = "si";
    if ($bien and $tipo) {
        $anexTip->anex_tipo_codigo($tipo);
        $ext = $anexTip->get_anex_tipo_ext();
        $ext = strtolower($ext);
        $auxnumero = str_pad($auxnumero,5,"0",STR_PAD_LEFT);
        
        $date = date_create(date('Y-m-d h:m:s'));
        $date = date_format($date,"YmdHis");
        
        $random = mt_rand(1, 99999);
        $archivo = trim($radi)."_".$date."_".trim($random)."_".trim($auxnumero).".".trim($ext);
        $archivoconversion= "1$archivo";
    }

    if(empty($radicado_rem))
        $radicado_rem = 1;
    if($_FILES['userfile1']['size']>0)
        $tamano = (filesize($_FILES['userfile1']['tmp_name'])/1000);
	include_once "$ruta_raiz/clasesComunes/ConsultasSQL.php";
    $objSql = new ConsultasSQL();
    if ($nuevo == "si") {
        include "$ruta_raiz/include/query/queryUpload2.php";
		include_once "$ruta_raiz/clasesComunes/ConsultasSQL.php";
        $objSql = new ConsultasSQL();
        if ($expIncluidoAnexo) {
            $expAnexo = $expIncluidoAnexo;
        } else {
            $expAnexo = null;
        }
        $id_ttr = 20;
        
        ##IBISCOM 2018-10-22 INICIO
        if ($ocultaDocElectronico == 1) {
            ($fechaProduccion == '') ? $fechaProduccionIng = date("Y")."-".date("m")."-".date("d") : $fechaProduccionIng = $fechaProduccion;
            $numeroFoliosPad = "SELECT TOP (1) folios AS folios FROM METADATOS_DOCUMENTO WHERE id_anexo LIKE '%$radi%'";
            $numeroFoliosTX = $db->conn->Execute($numeroFoliosPad)->fields['folios'];
            if(empty($folios)) {//IBISCOM 2019-05-09
                $folios = 1;
            }
                  
            if(empty($numeroFoliosTX)) {
                $foliosI = 0;
            } else {
                $foliosI = $numeroFoliosTX  + $folios;
            }
            
            $funcionHash = 'sha1';
            $rutaFile = $_FILES['userfile1']['tmp_name'];
            $hash = hash_file($funcionHash, $rutaFile);
            $nombre_proyector =  $nombreProyector;
            $nombre_revisor   =  $nombreRevisor;
            $codigo_tipoDocumental = $tipoDocumentalAnexo;
            $nombre_firma="";
            $palabras_claveI=$palabrasClave;// Variable que llega de la URL
            $id_tipo=0;//aplica si es para un anexo al radicado(0) o un anexo al expediente(1)
                  
            $insertMetadatos = "INSERT INTO METADATOS_DOCUMENTO ".// "si se van a ingresar en el mismo orden que estan definidos en la tabla no hace falta mencionar los campos"(id_anexo,id_tipo_anexo,hash,funcion_hash,folios,nombre_proyector,nombre_revisor,nombre_firma,palabras_clave)
                    "VALUES ('$codigo',$id_tipo,'$hash','$funcionHash','$foliosI','$nombre_proyector','$nombre_revisor','$nombre_firma','$palabras_claveI','$codigo_tipoDocumental','$fechaProduccionIng', 0)";
        }
        ##IBISCOM 2018-10-22 FIN
                
        $isql = "insert into anexos(SGD_REM_DESTINO, ANEX_RADI_NUME, ANEX_CODIGO,
                                    ANEX_TIPO, ANEX_TAMANO, ANEX_SOLO_LECT,
									ANEX_CREADOR, ANEX_DESC, ANEX_NUMERO,
									ANEX_NOMB_ARCHIVO, ANEX_BORRADO, ANEX_SALIDA,
									SGD_DIR_TIPO, ANEX_DEPE_CREADOR, SGD_TPR_CODIGO,
									ANEX_FECH_ANEX, SGD_APLI_CODI, SGD_TRAD_CODIGO, SGD_EXP_NUMERO,
                                    SGD_PRIORIDAD)
                values ($radicado_rem, $radi, '$codigo',
                        $tipo, $tamano, '$auxsololect',
						'$usua', ".$objSql->prepararValorSql($descr).", $auxnumero,
						'$archivoconversion', 'N', $anex_salida,
						$radicado_rem, $dependencia, null,
                        $sqlFechaHoy, $aplinteg, $tpradic, '$expAnexo', $selPrioridad)";
        $subir_archivo= "yes hhhhhhh";
	} else {
		$subir_archivo = "";
        $id_ttr = 84;
        	
        if(($_FILES['userfile1']['size']>0)) {
            $subir_archivo = " anex_nomb_archivo = '1$archivo', anex_tamano = $tamano, anex_tipo = $tipo,";
            $id_ttr = 29;
        }
            
        $isql = "UPDATE ANEXOS set $subir_archivo
                        ANEX_SALIDA     = $anex_salida,
                        SGD_REM_DESTINO = $radicado_rem,
                        SGD_DIR_TIPO    = $radicado_rem,
                        ANEX_DESC       = ".$objSql->prepararValorSql($descr).",
                        SGD_TRAD_CODIGO = $tpradic,
                        SGD_APLI_CODI   = $aplinteg,
                        SGD_PRIORIDAD   = $selPrioridad
                    WHERE ANEX_CODIGO   = '$codigo'";
            
        if (!empty($codigo)) {
            $selectRad = "SELECT CONVERT(VARCHAR(15),RADI_NUME_SALIDA) AS RADI_NUME_SALIDA 
                            FROM ANEXOS
                            WHERE ANEX_CODIGO = '$codigo'";
            $exiteRad = $db->conn->Execute($selectRad);
            $errorMsgRad = "<b>Error en la actualizaci&oacute;n del Asunto o descripcion del radicado</b>\n";
               
            if ($exiteRad) {
                // Solo lo actualiza si exite radicado del anexo
                if (!empty($exiteRad->fields["RADI_NUME_SALIDA"])) {
                    $sqlUpRad = "UPDATE RADICADO SET RA_ASUN = '$descr' 
                                    WHERE RADI_NUME_RADI = " . $exiteRad->fields["RADI_NUME_SALIDA"];
                    $rsRad = $db->conn->Execute($sqlUpRad);
                      
                    if ($rsRad === false) {
                        echo $errorMsgRad;
                        echo $db->conn->ErrorMsg();
                    }
                }
            }
        }
    }
        
    $db->conn->StartTrans();
    
    $sqlh = "insert into hist_eventos_anexos (ANEX_RADI_NUME, ANEX_CODIGO, SGD_TTR_CODIGO, HIST_FECH, USUA_DOC)
    		values ($radi, '$codigo', $id_ttr, $sqlFechaHoy, '".$_SESSION['usua_doc']."')";
    
	$bien = $db->conn->Execute($isql) && $db->conn->Execute($sqlh);
    // Si actualizo BD correctamente
    if ($bien) {
                
        $isql = "update RADICADO set SGD_PRIORIDAD = $selPrioridad where RADI_NUME_RADI = $radi";
        $rsPrio = $db->conn->Execute($isql);
        
        $respUpdate="OK";
        
        #IBISCOM 2018-10-20 INICIO
        if ($ocultaDocElectronico == 1) {
            if($agregaMetadatos){
                //$stateTX = $db->conn->Execute($insertMetadatos);
                //HACER UPDATE para modificar los folios
                if ($nuevo == "si") {
                    $stateTX = $db->conn->Execute($insertMetadatos);
                } else {
                    $updateMeSQL = "UPDATE METADATOS_DOCUMENTO ".
                        " SET  folios =  Cast(folios as INT) $folios WHERE id_anexo LIKE '%$radi%' AND id_tipo_anexo = '0'";
                    $UpMetadatos	= $db->conn->Execute($updateMeSQL);
                }
            }
        }
        ##IBISCOM 2018-10-20 FIN
        
        // Si existe esta variable es porque se está anexando una imagen ... sea nueva o para modificar.
        if($_FILES['userfile1']['size']>0) {
            	 
         	//Inserta histórico de imagen
          	$sql_hima = "INSERT INTO SGD_HIST_IMG_ANEX_RAD ( ANEX_RADI_NUME
            	,ANEX_CODIGO
            	,RUTA
            	,USUA_DOC
            	,USUA_LOGIN
            	,FECHA
            	,ID_TTR_HIAN)
            	VALUES
            	($radi
            	,'$codigo'
            	,'$archivoconversion'
            	,'".$_SESSION['usua_doc']."'
            	,'$usua'
            	,$sqlFechaHoy
            	,$id_ttr)";
            	$db->conn->Execute($sql_hima);
            }
        $bien2 = false;
        if ($subir_archivo) {
            if (strlen($codigo) == 19) {
                $directorio = "./bodega/".substr(trim($codigo),0,4)."/".substr(trim($codigo),4,3)."/docs/";
            } elseif (strlen($codigo) == 20) {
                $strDep = ltrim(substr(trim($codigo),4,4), '0');
                $directorio = "./bodega/".substr(trim($codigo),0,4)."/".$strDep."/docs/";
            }
            $bien2 = move_uploaded_file($_FILES['userfile1']['tmp_name'],$directorio.trim(strtolower($archivoconversion)));
            //Si intento anexar archivo y Subio correctamente
            if ($bien2) {
                
                /*if( $_FILES['userfile1']['type']=='application/pdf')
                {
                    $arch = "E:\\OI_OrfeoPHP7_64\\orfeo\\bodega\\".substr(trim($archivo),0,4)."\\".substr(trim($archivo),4,3)."\\docs\\".trim(($archivoconversion));
                    
                    $folios = getNumPagesInPDF($arch);
                    
                    $pdftext = file_get_contents($arch); 
                    $folios = preg_match_all("/\/Page\W/", $pdftext, $dummy); 
                    
                    $im = new Imagick();
                    $im->pingImage($arch); 
                    //$im = new Imagick($arch);
                    $foliosI = $im->getNumberImages();
                    $im->destroy();
                    
                    $updateSQLme = "UPDATE METADATOS_DOCUMENTO ".
                        " SET  folios =  Cast(folios as INT) $folios WHERE id_anexo LIKE '%$radi%' AND id_tipo_anexo = '0'";
                    $OKME = $db->conn->Execute($updateSQLme);
                }*/
                
                $resp1 = "OK";
                //$db->conn->CommitTrans();
            } else {
                $resp1 = "ERROR";
                //$db->conn->FailTrans();
                //$db->conn->RollbackTrans();
            }
        } else {
            //$db->conn->CommitTrans();
        }
    } else {
        //$db->conn->FailTrans();
        //$db->conn->RollbackTrans();
    }
}
// Se elimina la conexion a la base de datos
$transCompleta = $db->conn->CompleteTrans();
$sqlAnex = '';
    
if ($codigo) {
    $sqlAnex = "SELECT ANEXT.ANEX_TIPO_DESC,
                    ANEX.ANEX_DESC,
                    TRAD.SGD_TRAD_DESCR
                FROM ANEXOS ANEX,
                     ANEXOS_TIPO ANEXT,
                     SGD_TRAD_TIPORAD TRAD
                WHERE ANEX.ANEX_CODIGO = '$codigo' AND 
                      ANEX.SGD_TRAD_CODIGO = TRAD.SGD_TRAD_CODIGO AND
                      ANEX.ANEX_TIPO = ANEXT.ANEX_TIPO_CODI";
}
    
$rsAnex = $db->conn->Execute($sqlAnex);

if (!$rsAnex->EOF) {
    $tipoDocumento = $rsAnex->fields['ANEX_TIPO_DESC'];
    $tipoRadicado = $rsAnex->fields['SGD_TRAD_DESCR'];
    $anexDesc = $rsAnex->fields['ANEX_DESC'];
}
include_once "./modificarArchivo.php";
?>