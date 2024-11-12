<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "sinacceso.php");
    exit();
} else {
    $login = $_SESSION["login"];
    $dependencia = $_SESSION["dependencia"];
}

include ("adodb/tohtml.inc.php");
include_once ("./include/tx/Historico.php");
require_once ("./include/db/ConnectionHandler.php");

define('FPDF_FONTPATH', 'fpdf/font/');
require ('fpdf/fpdf.php');

if (! $db) {
    $db = new ConnectionHandler('.');
}

$radicadoIn = htmlspecialchars($_GET['numRadicado']);
$anexoIn = htmlspecialchars($_GET['idAnexo']);
$expedienteIn = htmlspecialchars($_GET['expediente']);
$radicado = explode(",", $radicadoIn);
$anexo = explode(",", $anexoIn);
$data = array();

$tipoExt = 7;
$fechaIndice = date("d-m-Y");
$fechaSave = date("m/d/Y");
$ano = date("Y");
$descr = "Indice Electronico, Exp: " . $expedienteIn . ", Fecha: " . $fechaIndice;
// Pendiente//$path =
// tipo documetal no defido por ahora
$tpD = 277;
$nombreDocumento = "indice_" . $expedienteIn . "_" . $fechaIndice . ".pdf";
//$rutaTemporal = "C:/Users/usrlocal/AppData/Local/Temp/";
$rutaTemporal = getTempFolder();
//$rutaTemporal = "C:/";

// variable para guardado
$ruta = trim($ano) . "\\" . trim($dependencia) . "\\" . trim($expedienteIn);
$adjuntos = trim(str_replace(" ", "", BODEGAPATH . $ruta));
$adjuntos = str_replace("/", "\\", $adjuntos);

$fechaNomb = Date("ymd_hi_");
$numramdon = rand(0, 100000);
$ext = ".pdf";
$nomFinal = "\\" . $fechaNomb . $numramdon . "0" . $ext;

$destino = $adjuntos . $nomFinal;

$nombreDocumentoXml = "indice_" . $expedienteIn . "_" . $fechaIndice . ".xml";
$numrandom = rand(0, 100000);
$nomFinalXml = "\\" . $fechaNomb . $numrandom . "0.xml";
$destinoXml = $adjuntos . $nomFinalXml;

global $expedienteIn;
global $fechaIndice;
global $fechaI;
global $fechaF;

if ($db) {
    //query y procedimiento para remplazo de indice electronico
    $idIndice = 0;
    $pathIndice = "";
    $querySelectIndice ="SELECT ANEXOS_EXP_ID, ANEXOS_EXP_PATH 
                            FROM SGD_ANEXOS_EXP
                            WHERE SGD_EXP_NUMERO = '".$expedienteIn."'
                            AND ANEXOS_EXP_NOMBRE like '%indice_%'
                            AND ANEXOS_EXP_DESC like '%Indice Electronico, Exp: %'
                            AND ESTADO_INDICE = 0  
                        ORDER BY ANEXOS_EXP_FECH_CREA, ANEXOS_EXP_ID ";
    $rsIndice = $db->conn->Execute($querySelectIndice);
    if ($rsIndice && !$rsIndice->EOF) {
        $idIndice = $rsIndice->fields['0'];
        $pathIndice = $rsIndice->fields['1'];
        
        //delete tabla anexo exp del indice electronico
        $queryDeleteAnex ="DELETE FROM SGD_ANEXOS_EXP
                            WHERE SGD_EXP_NUMERO = '".$expedienteIn."'
                            AND ANEXOS_EXP_NOMBRE like '%indice_%'
                            AND ANEXOS_EXP_DESC like '%Indice ElectrOnico, Exp: %'
                            AND ESTADO_INDICE = 0 ";
        $execDeleteAnex = $db->conn->Execute($queryDeleteAnex);
        
        //delete Metadatos Indice electronico
        $queryDeleteMeta ="DELETE FROM METADATOS_DOCUMENTO
                            WHERE id_anexo = '".$idIndice."'";
        
        $execDeleteMeta = $db->conn->Execute($queryDeleteMeta);
        
        if (file_exists(BODEGAPATH.$pathIndice)) {
            unlink(BODEGAPATH.$pathIndice);
        }
    }
    
    //fin procedimiento para remplazo de indice electronico
    $fechasFin = Array();
    if ($radicadoIn == "") {
        
        $sqlRads = "SELECT RADI_NUME_RADI FROM SGD_EXP_EXPEDIENTE WHERE SGD_EXP_NUMERO = '$expedienteIn' AND SGD_EXP_ESTADO <> 2 ";
        $rsRads = $db->conn->Execute($sqlRads);
        if ($rsRads) {
            while(!$rsRads->EOF) {
                
                $radicado = $rsRads->fields[0];

                // Ibis: Info Radicado Principal
                /*$querySelectRad = "SELECT ANEX_CODIGO  FROM ANEXOS
                                    WHERE ANEX_RADI_NUME = '$radicado'
                               ORDER BY ANEX_RADI_FECH";  //AND RADI_NUME_SALIDA IS NOT NULL
                
                $anexTofind = $db->conn->Execute($querySelectRad)->fields['0'];
                
                if($anexTofind){
                    $sql = "SELECT A.ANEX_FECH_ANEX , A.ANEX_CREADOR , A.ANEX_DESC,
                            A.ANEX_TAMANO, A.ANEX_TIPO
    			         FROM ANEXOS A
                        WHERE ANEX_RADI_NUME=$radicado AND ANEX_CODIGO=$anexTofind
                        ORDER BY A.ANEX_FECH_ANEX "
                        //INNER JOIN USUARIO U ON A.ANEX_CREADOR=U.USUA_LOGIN
                    ;
                    $resultInfoAnexo = $db->conn->Execute($sql);
                    
                    while ($atribut = $resultInfoAnexo->fetchRow()) {
                        $v1 = $atribut[0];
                        $v2 = $atribut[1];
                        $v3 = $atribut[2];
                        $v4 = formatSizeUnits($atribut[3]);
                        $v5 = $atribut[4];
                    }
                    
                    $folios = 0;
                    $querySelectId = "SELECT HASH, FOLIOS, fecha_produccion FROM METADATOS_DOCUMENTO WHERE ID_ANEXO = '$anexTofind'";
                    $hashSaveExec = $db->conn->Execute($querySelectId);
                    while($tempAtri = $hashSaveExec->fetchRow()){
                        $hashSave = $tempAtri[0];
                        $folios = $tempAtri[1];
                        
                        $date = date_create($tempAtri[2]);
                        $date = date_format($date,"Y-m-d H:i:s");
                        $fechaprod = $date;
                    }
                    
                    
                    $funcion_hash = "sha1";
                    
                    $queryTpD = "	SELECT t.SGD_TPR_DESCRIP
    			                 FROM SGD_TPR_TPDCUMENTO t, RADICADO r
    	   		                  WHERE	t.sgd_tpr_codigo  = r.TDOC_CODI
    					and r.radi_nume_radi = '$radicado'";
                    $resultadoTipoDoc = $db->conn->Execute($queryTpD)->fields['0'];
                    
                    $queryTipoFile = "SELECT ANEX_TIPO_EXT FROM ANEXOS_TIPO WHERE ANEX_TIPO_CODI = '$v5'";
                    $tipoFile = $db->conn->Execute($queryTipoFile)->fields['0'];
                    
                    $queryHistorico = "SELECT SGD_HFLD_FECH FROM SGD_HFLD_HISTFLUJODOC
                                WHERE SGD_EXP_NUMERO = '".$expedienteIn."' AND RADI_NUME_RADI = ".$radicado."
                                AND SGD_HFLD_OBSERVA = 'Incluir radicado en Expediente'";
                    
                    $fechHistorico = $db->conn->Execute($queryHistorico)->fields['0'];
                    
                    $dataTemp = array();
                    array_push($dataTemp, 1);
                    array_push($dataTemp, $radicado);
                    array_push($dataTemp, $hashSave);
                    array_push($dataTemp, $funcion_hash);
                    if(strlen($resultadoTipoDoc)>=25){
                        array_push($dataTemp, substr($resultadoTipoDoc,0,24));
                    }else{
                        array_push($dataTemp, $resultadoTipoDoc);
                    }
                    if (strlen($v2)>=20){
                     array_push($dataTemp, substr($v2,0,19));
                     }else{
                     array_push($dataTemp,$v2);
                     }
                     $date = date_create($v1);
                     $v1 = date_format($date,"Y-m-d H:i:s");
                    if(strlen($v1)>=20){
                        array_push($dataTemp, substr($v1,0,19));
                    }else{
                        array_push($dataTemp, $v1);
                    }
                    array_push($dataTemp, $fechaprod); //$v1 //$fechaprod
                    array_push($dataTemp, $fechHistorico);
                    //array_push($dataTemp, "1");
                    array_push($dataTemp, $folios);
                    array_push($dataTemp, $tipoFile);
                    array_push($dataTemp, $v4);
                    array_push($dataTemp, "DIG");
                    
                    array_push($data, $dataTemp);
                    // Ibis: Fin Info Radicado Principal
                } else {*/
                    $sql = "SELECT 	R.RADI_FECH_RADI, U.USUA_LOGIN, R.RA_ASUN, R.RADI_PATH, R.RADI_NUME_HOJA, M.MREC_ORIGEN, R.RADI_FECH_OFIC, 
                                MT.hash, MT.funcion_hash, MT.TAMANO, T.SGD_TPR_DESCRIP 
                        FROM RADICADO R INNER JOIN USUARIO U ON R.RADI_USUA_RADI = U.USUA_CODI
    		                 INNER JOIN DEPENDENCIA D ON R.RADI_DEPE_RADI = D.DEPE_CODI AND U.DEPE_CODI = D.DEPE_CODI
                             INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = R.TDOC_CODI
                             LEFT JOIN MEDIO_RECEPCION M ON M.MREC_CODI = R.MREC_CODI
                             LEFT JOIN METADATOS_DOCUMENTO MT ON MT.id_anexo = CAST(R.RADI_NUME_RADI AS VARCHAR)
                        WHERE R.RADI_NUME_RADI = $radicado ";
                    $rsRad = $db->conn->Execute($sql);
                    
                    if ($rsRad && !$rsRad->EOF) {
                        while ($atribut = $rsRad->fetchRow()) {
                            $v1 = $atribut[0];
                            $v2 = $atribut[1];
                            $v3 = $atribut[2];
                            $v4 = $atribut[3];
                            $v5 = $atribut[4];
                            $v6 = $atribut[5];
                            $v7 = $atribut[6];
                            $v8 = $atribut[7];
                            $funcion_hash = $atribut[8];
                            $v10 = $atribut[9];
                            $tipoDoc = iconv('utf-8', 'iso-8859-1', $atribut[10]);
                        }
                    }
                                       
                    $queryHistorico = "SELECT SGD_HFLD_FECH FROM SGD_HFLD_HISTFLUJODOC
                                WHERE SGD_EXP_NUMERO = '".$expedienteIn."' AND RADI_NUME_RADI = ".$radicado."
                                AND SGD_HFLD_OBSERVA = 'Incluir radicado en Expediente'";
                    
                    $fechHistorico = $db->conn->Execute($queryHistorico)->fields['0'];
                    $date = date_create($fechHistorico);
                    $fechHistorico = date_format($date,"Y-m-d H:i:s");
                    
                    $dataTemp = array();
                    array_push($dataTemp, 1);
                    array_push($dataTemp, $radicado);
                    array_push($dataTemp, $v8);
                    array_push($dataTemp, $funcion_hash);
                    if(strlen($tipoDoc)>=25){
                        array_push($dataTemp, substr($tipoDoc,0,24));
                    }else{
                        array_push($dataTemp, $tipoDoc);
                    }
                    if(strlen($v2)>=20){
                        array_push($dataTemp, substr($v2,0,19));
                    }else{
                        array_push($dataTemp, $v2);
                    }
                    $date = date_create($v1);
                    $v1 = date_format($date,"Y-m-d H:i:s");
                    /*if(strlen($v1)>=20){
                        array_push($dataTemp, substr($v1,0,19));
                    }else{
                        array_push($dataTemp, $v1);
                    }*/
                    $date = date_create($v7);
                    $v7 = date_format($date,"Y-m-d H:i:s");
                    $fecRadTemp = '';
                    if ($v7 == "") {
                        $fecRadTemp = $v1;
                        array_push($dataTemp, $v1);
                    } else {
                        $fecRadTemp = $v7;
                        array_push($dataTemp, $v7);
                    }
                    array_push($dataTemp, $fechHistorico);
                    array_push($dataTemp,"1");
                    array_push($dataTemp, $v5);
                    if (strlen($v4) > 4) {
                        $extension = end(explode(".", $v4));
                    } else {
                        $extension = "";
                    }
                    array_push($dataTemp, $extension);
                    array_push($dataTemp, $v10 . " kb");
                    if($v6 == 0) {
                        array_push($dataTemp, "DIG");
                    } else {
                        array_push($dataTemp, "ELEC");
                    }
                    array_push($dataTemp, $fecRadTemp);
                    
                    array_push($data, $dataTemp);
                    
                    array_push($fechasFin, $fecRadTemp);
                    
                //}
                
                // Ibis: Info Anexos radicado
                
                $querySelectRad = "SELECT A.ANEX_CODIGO, A.ANEX_FECH_ANEX, A.ANEX_CREADOR, A.ANEX_DESC, A.ANEX_TAMANO, A.ANEX_TIPO,
                                        M.HASH, M.FOLIOS, M.fecha_produccion, M.tamano, ANEX_TIPO_EXT, D.SGD_TPR_DESCRIP
                                    FROM ANEXOS A LEFT JOIN METADATOS_DOCUMENTO M ON A.ANEX_CODIGO = M.id_anexo 
                                        LEFT JOIN ANEXOS_TIPO T ON T.ANEX_TIPO_CODI = A.ANEX_TIPO
                                        LEFT JOIN SGD_TPR_TPDCUMENTO D ON D.SGD_TPR_CODIGO = M.codigo_tipoDocumental
                                    WHERE ANEX_RADI_NUME = '$radicado' AND ANEX_BORRADO = 'N' AND ANEX_SALIDA != 1 
                               ORDER BY ANEX_RADI_FECH"; 
                $rsanex = $db->conn->Execute($querySelectRad);
                if ($rsanex) {
                    while(!$rsanex->EOF) {
                        
                        $rad = $radicado;
                        //$anex = $anexo[$j];
                        $anexo = $rsanex->fields[0];                        
                        $v1_2 = $rsanex->fields[1];
                        $v2_2 = $rsanex->fields[2];
                        $v3_2 = $rsanex->fields[3];
                        //$v4_2 = ($atribut[3] == '') ? "0 bytes" : formatSizeUnits($atribut[3]);  
                        $v4_2 = ($rsanex->fields[4] == '') ? "0 kb" : $rsanex->fields[4] . " kb";
                        $fechaI = (strlen($data[count($data)-1][7]) > 0 ? $data[count($data)-1][7] : '');
                        $v5_2 = $rsanex->fields[5];

                        $folios2 = 0;
                        $hashSave2 = $rsanex->fields[6];
                        $folios2 = $rsanex->fields[7]; //cambio paginas
                        $fechaprod = $rsanex->fields[8];
                        $tamano = ($rsanex->fields[9] == '') ? "0 kb" : $rsanex->fields[9] . " kb";
                        $funcion_hash = "sha1";                           
                        $tipoFile2 = $rsanex->fields[10];
                            
                        $dataTemp2 = array();
                        array_push($dataTemp2, 1);
                        array_push($dataTemp2, $anexo);
                        array_push($dataTemp2, $hashSave2);
                        array_push($dataTemp2, $funcion_hash);
                        if (strlen($rsanex->fields[11])>=25){
                            array_push($dataTemp2, substr($rsanex->fields[11],0,24));
                        } else {
                            array_push($dataTemp2, $rsanex->fields[11]);
                        }
                            /*if (strlen($v3_2)>=20){
                             array_push($dataTemp2, substr($v3_2,0,19));
                             }else{
                             array_push($dataTemp2, $v3_2);
                             }*/
                        if (strlen($v2_2)>=20){
                            array_push($dataTemp2, substr($v2_2,0,19));
                        } else {
                            array_push($dataTemp2, $v2_2);
                        }
                        $date = date_create($v1_2);
                        $v1_2 = date_format($date,"Y-m-d H:i:s");
                        array_push($dataTemp2, $v1_2); //$v1_2  //$fechaprod
                        array_push($dataTemp2, $fechHistorico);
                        array_push($dataTemp2, $folios2);
                        array_push($dataTemp2, $folios2);
                        array_push($dataTemp2, $tipoFile2);
                        array_push($dataTemp2, $tamano);
                        array_push($dataTemp2, "DIG");
                        array_push($dataTemp2, $fecRadTemp);
                        
                        array_push($data, $dataTemp2);
                                                
                        $rsanex->MoveNext();
                    }
                }

                $rsRads->MoveNext();
            }
        }
        
        //for ($i = 0; $i < count($radicado); $i ++) {}
    }
    // Ibis: Fin Info Anexos radicado

    //Ibis: Info Anexos exp
    $queryIdAnexExp = "SELECT ANEXOS_EXP_ID, T.SGD_TPR_DESCRIP
                        FROM SGD_ANEXOS_EXP A INNER JOIN SGD_TPR_TPDCUMENTO T ON A.SGD_TPR_CODIGO = T.SGD_TPR_CODIGO 
                        WHERE SGD_EXP_NUMERO = '$expedienteIn' AND A.ANEXOS_EXP_ESTADO != 1 
                        ORDER BY ANEXOS_EXP_FECH_CREA, ANEXOS_EXP_ID ";
    $resultId = $db->conn->Execute($queryIdAnexExp);

    while ($execTemp = $resultId->fetchRow()) {
        $IdDoc = $execTemp['0'];
        $resultTipoDocumental = iconv('utf-8', 'iso-8859-1', $execTemp['1']);
        
        $folios3 = 0;
        $queryhashAnexExp = "SELECT HASH, FOLIOS, fecha_produccion, tamano FROM METADATOS_DOCUMENTO WHERE ID_ANEXO = '$IdDoc'";
        $hashSaveAnexExpExec = $db->conn->Execute($queryhashAnexExp);
        
        while($tempAtri = $hashSaveAnexExpExec->fetchRow()){
            $hashSaveAnexExp = $tempAtri[0];
            $folios3 = $tempAtri[1];
            $segRamdon = rand(10,59);
            $date = date_create($tempAtri[2]);
            $fechaDoc = date_format($date,"Y-m-d");
            $fechaDoc = $fechaDoc." 08:00:".$segRamdon;
            $tamano = $tempAtri[3];
        }
        
        $funcion_hash = "sha1";

        /*$queryTipoDocumental = "SELECT SGD_TPR_DESCRIP 
                                FROM SGD_TPR_TPDCUMENTO AS Tpd 
                                WHERE Tpd.SGD_TPR_CODIGO =(SELECT SGD_TPR_CODIGO 
                                                            FROM SGD_ANEXOS_EXP
                        	                                WHERE ANEXOS_EXP_ID =  '$IdDoc')";
        $resultTipoDocumental = $db->conn->Execute($queryTipoDocumental)->fields[0];*/

        $sqlDatosExpAnex = "SELECT A.ANEXOS_EXP_FECH_CREA, A.USUA_LOGIN_CREA, A.ANEXOS_EXP_DESC,
                                A.ANEX_TIPO_CODI, A.ANEXOS_EXP_PATH
						FROM	SGD_ANEXOS_EXP A
						WHERE	A.SGD_EXP_NUMERO = '$expedienteIn'
								AND A.ANEXOS_EXP_ID = '$IdDoc'
					   ORDER BY A.ANEXOS_EXP_FECH_CREA, A.ANEXOS_EXP_ID ";
        $resultInfoAnexoExp = $db->conn->Execute($sqlDatosExpAnex);

        while ($atribut3 = $resultInfoAnexoExp->fetchRow()) {
            
            $date = date_create($atribut3[0]);
            $v1_3 = date_format($date,"Y-m-d H:i:s");
            //$v1_3 = $atribut3[0];
           /*  $v1_3 = str_replace('/', '-', $v1_3);
            $v1_3 = strtotime( $v1_3 );
            $v1_3 = date( 'Y-m-d', $v1_3); */
            $v2_3 = $atribut3[1];
            $v3_3 = $atribut3[2];
            $v4_3 = $atribut3[3];
            $v5_3 = $atribut3[4];
        }

        $queryTipoFile3 = "SELECT ANEX_TIPO_EXT FROM ANEXOS_TIPO WHERE ANEX_TIPO_CODI = '$v4_3'";
        $tipoFile3 = $db->conn->Execute($queryTipoFile3)->fields['0'];

        /*$v5_3 = str_replace('\\', '//', $v5_3);
        $rutaFile = BODEGAPATH . $v5_3;
        if (file_exists($rutaFile)) {
            $tamano = round(filesize($rutaFile) / 1000);
        } else {
            $tamano = 0;
        }*/

        $dataTemp3 = array();
        array_push($dataTemp3, 1);
        array_push($dataTemp3, $IdDoc);
        array_push($dataTemp3, $hashSaveAnexExp);
        array_push($dataTemp3, $funcion_hash);
        if(strlen($resultTipoDocumental)>=25){
            array_push($dataTemp3, substr($resultTipoDocumental,0,24));
        }else{
            array_push($dataTemp3, $resultTipoDocumental);
        }        
        /*if (strlen($v3_3)>=20){
            array_push($dataTemp3, substr ($v3_3,0,19));
        }else{
            array_push($dataTemp3, $v3_3);
        }*/
        if (strlen($v2_3)>=20){
            array_push($dataTemp3, substr ($v2_3,0,19));
        }else{
            array_push($dataTemp3, $v2_3);
        }  
        array_push($dataTemp3, $fechaDoc);
        array_push($dataTemp3,$v1_3);
        array_push($dataTemp3,"1");
        array_push($dataTemp3,$folios3);
        array_push($dataTemp3, $tipoFile3);
        array_push($dataTemp3, $tamano . " Kb");
        array_push($dataTemp3, "DIG");
        array_push($dataTemp3, $fechaDoc);

        array_push($data, $dataTemp3);
        
        array_push($fechasFin, $fechaDoc);
    }
    
    foreach ($data as $clave => $fila) {
        $var1[$clave] = $fila[0];
        $var2[$clave] = $fila[1];
        $var3[$clave] = $fila[2];
        $var4[$clave] = $fila[3];
        $var5[$clave] = $fila[4];
        $var6[$clave] = $fila[5];
        $var7[$clave] = $fila[6];
        $var8[$clave] = $fila[7];
        $var9[$clave] = $fila[8];
        $var10[$clave] = $fila[9];
        $var11[$clave] = $fila[10];
        $var12[$clave] = $fila[11]; 
        $var13[$clave] = $fila[12]; 
        $var14[$clave] = $fila[13];
    }
    
    foreach ($fechasFin as $clave => $fila) {
        $varf1[$clave] = $fila;
    }
    
   // array_multisort($var9, SORT_DESC, $data); // ANTES 2019-07-31
    //array_multisort($var8, SORT_ASC, $data); // DESPUES 2019-07-31
    array_multisort($var14, SORT_ASC, $data); // DESPUES 2019-07-31
    
    array_multisort($varf1, SORT_ASC, $fechasFin);
    
    for ($i=0;$i<count($data);$i++){
        $data[$i][0] =$i+1;      
    }
    
    for ($p = 0; $p < count($data); $p ++) {
        unset($data[$p][13]);
    }
    /*for ($y = count($data) -1; $y > 0; $y--) {
        if (strlen($data[$y][7]) > 0) {
            $fechaI = $data[$y][7];
            break;
        }
    }*/
    
    for ($y = 0; $y <= count($data) -1; $y++) {
        if (strlen($data[$y][6]) > 0) {
            $fechaI = $data[$y][6];
            break;
        }
    }
    
    //$fechaI = (strlen($data[count($data)-1][7]) > 0 ? $data[count($data)-1][7] : '');
    //$fechaI = $data[count($data)-1][7];     // DESPUES 2019-07-31 Se toma la fecha ingresada o digitada para el documento, COLUMNA FECHA_DOC
  //$fechaI = $data[0][8]; // ANTES 2019-07-31
    
    $date = date_create($fechaI);
    $fechaI = date_format($date,"Y-m-d");
    
    //$fechaI = strtotime( $date );
    //$fechaI = date( 'Y-m-d', $fechaI);
    //$fechaF = $data[0][7];  // DESPUES 2019-07-31 Se toma la fecha ingresada o digitada para el documento, COLUMNA FECHA_DOC
    $fechaF = $fechasFin[count($fechasFin)-1]; // ANTES 2019-07-31
    $fechaF = strtotime( $fechaF );
    $fechaF = date( 'Y-m-d', $fechaF);
    
    $queryNomDepe = "SELECT DEPE_NOMB FROM DEPENDENCIA WHERE DEPE_CODI = '$dependencia'";
    $execDepe = $db->conn->Execute($queryNomDepe)->fields['0'];
    
    /*for($i=0;$i<count($data);$i++){
        if ($data[$i][10] === 0){
            $data[$i][9] = 0;
            $data[$i][10] = $data[$i-1][10];
        }else{
            $tempval = $data[$i][10];
            $data[$i][9] = $data[$i-1][10]+1;
            //$data[$i][10] = ($data[$i][9])+($data[$i][10]-1);
            $data[$i][10] = ($data[$i][9])+$tempval;
        }
        
    }*/
    
    for($i=0;$i<count($data);$i++){
        if ($data[$i][9] === 0){
            $data[$i][8] = 0;
            $data[$i][9] = $data[$i-1][9];
        }else{
            $tempval = $data[$i][9];
            $data[$i][8] = $data[$i-1][9]+1;
            //$data[$i][10] = ($data[$i][9])+($data[$i][10]-1);
            $data[$i][9] = ($data[$i][8])+$tempval;
        }
        
    }
}

function getTempFolder() {
    $tmp = tempnam ( null, '' );
    return dirname ( $tmp ) . '/';
}

// Títulos de las columnas
$header = array(
    'OR',
    'ID',
    'HASH',
    'FUN',
    'TIPO DOCUMENTO',
    //'NOMBRE',
    'CREADO POR',
    'FECHA DOC',
    'FECHA INCL',
    'P-I',
    'P-F',
    'Formato',
    'TAM',
    'Origen'
);
$tamanosCel = array(
    5,
    26,
    58,
    9,
    58,
    //0,
    28,
    23,
    23,
    8,
    8,
    15,
    12,
    12
);

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        global $dependencia;
        global $execDepe;
        global $expedienteIn;
        global $fechaIndice;
        global $fechaI;
        global $fechaF;
        global $header;
        global $tamanosCel;
        
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(54, 18, $this->Image('img/logoNuevo.jpg', $this->GetX(), $this->GetY() +5, 54), 1, 0, 'C');
        $this->Cell(200, 6, "Dependencia: ".$dependencia.' - '.$execDepe, 1, 0, 'C');
        $this->Cell(30, 6, '     Página '.$this->PageNo().' de {nb}', 1, 0, 'C');
        $this->Ln();
        $this->Cell(54, 18, ' ', 0, 0, 'C');
        $this->Cell(200, 6, 'INDICE ELECTRONICO', 0, 0, 'C');
        $this->Cell(30, 6, "Código: N/A", 1, 0, 'C');
        $this->Ln();
        $this->Cell(54, 18, '', 0, 0, 'C');
        $this->Cell(200, 6, ' ', 1, 0, 'C');
        $this->Cell(30, 6, "Versión: 1.0", 1, 0, 'C');
        $this->Ln(10);
        
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30,6,'Expediente: '.$expedienteIn);
        $this->Cell(190);
        $this->Cell(30,6,"Fecha Generación: ".$fechaIndice);
        $this->Ln();
        $this->Cell(30,6,"Fecha Inicial: ".$fechaI);
        $this->Cell(190);
        $this->Cell(30,6,"Fecha Final:   ".$fechaF);
        $this->Ln(10);
        
        $p = 0;
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(232, 232, 232);
        // Cabecera
        foreach ($header as $col) {
            // ancho,alto,dato,borde
            $this->Cell($tamanosCel[$p], 6, $col, 1, 0, 'C', True);
            $p += 1;
        }
        $this->Ln();
        $this->SetFont('Arial', '', 7);
    }
    
    // Tabla
    function BasicTable($data, $tamanosCel)
    {
        global $expedienteIn;
        global $fechaIndice;
        global $fechaI;
        global $fechaF;
        
        //$data = array_multisort($data[7], SORT_DESC, $data);
        
        //texto informativo encabezado
        /*$this->SetFont('Arial', 'B', 12);
        $this->Cell(30,6,'Expediente: '.$expedienteIn);
        $this->Cell(186);
        $this->Cell(30,6,"Fecha Generación: ".$fechaIndice);
        $this->Ln(10);
        $this->Cell(30,6,"Fecha Inicial: ".$fechaI);
        $this->Cell(186);
        $this->Cell(30,6,"Fecha Final:   ".$fechaF);
        $this->Ln(10);*/
        
        //$p = 0;
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(232, 232, 232);
        // Cabecera
        /*foreach ($header as $col) {
            // ancho,alto,dato,borde            
            $this->Cell($tamanosCel[$p], 6, $col, 1, 0, 'C', True);
            $p += 1;
        }*/
       // $this->Ln();
        $this->SetFont('Arial', '', 7);
        // Datos
        for ($j = 0; $j < count($data); $j ++) {
            for($k=0;$k<count($data); $k ++){
                if($data[$k][9]===0){
                    $data[$k][9]="-";
                    $data[$k][10]="-";
                }
            }
            
            for ($i = 0; $i < count($data[0]); $i ++) {
                $this->CellFitSpace($tamanosCel[$i], 7, $data[$j][$i], 1, 0, 'C');
            }
            $this->Ln();
        }
    }

    // ***** Aquí comienza código para ajustar texto *************
    // ***********************************************************
    function CellFit($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $scale = false, $force = true)
    {
        // Get string width
        $str_width = $this->GetStringWidth($txt);

        // Calculate ratio to fit cell
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        if ($str_width != 0) {
            $ratio = ($w - $this->cMargin * 2) / $str_width;
        } else { 
            $ratio = 0;
        }

        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit) {
            if ($scale) {
                // Calculate horizontal scaling
                $horiz_scale = $ratio * 100.0;
                // Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET', $horiz_scale));
            } else {
                // Calculate character spacing in points
                $char_space = ($w - $this->cMargin * 2 - $str_width) / max($this->MBGetStringLength($txt) - 1, 1) * $this->k;
                // Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET', $char_space));
            }
            // Override user alignment (since text will fill up cell)
            $align = '';
        }

        // Pass on to Cell method
        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);

        // Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT ' . ($scale ? '100 Tz' : '0 Tc') . ' ET');
    }

    function CellFitSpace($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $this->CellFit($w, $h, $txt, $border, $ln, $align, $fill, $link, false, false);
    }

    // Patch to also work with CJK double-byte text
    function MBGetStringLength($s)
    {
        if ($this->CurrentFont['type'] == 'Type0') {
            $len = 0;
            $nbbytes = strlen($s);
            for ($i = 0; $i < $nbbytes; $i ++) {
                if (ord($s[$i]) < 128)
                    $len ++;
                else {
                    $len ++;
                    $i ++;
                }
            }
            return $len;
        } else
            return strlen($s);
    }
    // ************** Fin del código para ajustar texto *****************
    // ******************************************************************
}

function convert($size, $unit)
{
    if($unit == "KB")
    {
        return $fileSize = round($size / 1024,4) . 'KB';
    }
    if($unit == "MB")
    {
        return $fileSize = round($size / 1024 / 1024,4) . 'MB';
    }
    if($unit == "GB")
    {
        return $fileSize = round($size / 1024 / 1024 / 1024,4) . 'GB';
    }
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }
    
    return $bytes;
}

// Funcion grabar en la base de datos
function grabarRegistro($db, $numExpe, $tipoExt, $usualogin, $fechaGrab, $descrip, $Grabar_path, $var2Value, $depeCodi, $nombre, $destino)//, $Historico)
{
    $queryGrabar = "INSERT INTO SGD_ANEXOS_EXP(
											SGD_EXP_NUMERO,
                                            ANEX_TIPO_CODI,
                                            USUA_LOGIN_CREA,
                                            ANEXOS_EXP_FECH_CREA,
                                            ANEXOS_EXP_DESC,
                                            ANEXOS_EXP_PATH,
                                            SGD_TPR_CODIGO,
                                            DEPE_CODI,
                                            ANEXOS_EXP_NOMBRE
                                            )";

    $queryGrabar .= " VALUES(
    						'$numExpe',
    						$tipoExt,
    						'$usualogin',
    						'$fechaGrab',
    						'$descrip',
    						'$Grabar_path',
    						$var2Value,
    						$depeCodi,
    						'$nombre')";
    $ejecutarQuerey = $db->conn->Execute($queryGrabar);
    // Ibis: Select para obtener id para insert hash
    $querySelec = "SELECT
                        ANEXOS_EXP_ID
                    FROM
                        SGD_ANEXOS_EXP e
                    WHERE
                        e.SGD_EXP_NUMERO ='$numExpe'
                        AND e.ANEX_TIPO_CODI = '$tipoExt'
                        AND e.USUA_LOGIN_CREA = '$usualogin'
                        AND e.ANEXOS_EXP_FECH_CREA = '$fechaGrab'
                        AND e.ANEXOS_EXP_DESC = '$descrip'
                        AND e.ANEXOS_EXP_PATH = '$Grabar_path'
                        AND e.SGD_TPR_CODIGO = '$var2Value'
                        AND e.DEPE_CODI = '$depeCodi'
                        AND e.ANEXOS_EXP_NOMBRE = '$nombre'";

    $exec = $db->conn->Execute($querySelec)->fields['0'];
    $id_doc = $exec;
    // Ibis: Fin Select para obtener id para insert hash

    if (empty($ejecutarQuerey)) {
        return false;
    } else {
        // Ibis: Insert funcion hash
        $funcion_hash = "sha1";
        if (file_exists($destino)) {
            $hash = hash_file($funcion_hash, $destino);
        } else {
            $hash = "";
        }

        $queryInsert = "INSERT
                        INTO METADATOS_DOCUMENTO
                        (ID_ANEXO,
                         ID_TIPO_ANEXO,
                         HASH,
                         FUNCION_HASH)
                         VALUES
                         ('$id_doc',
                         1,
                         '$hash',
                         '$funcion_hash')";

        $exec2 = $db->conn->Execute($queryInsert);
        // Ibis: Fin Insert funcion hash

        return true;
    }
}

function to_xml(SimpleXMLElement $object, array $data) {
    foreach ($data as $key => $value) {
        // if the key is an integer, it needs text with it to actually work.
        $valid_key  = is_numeric($key) ? "Reg_$key" : $key;
        $new_object = $object->addChild($valid_key, is_array($value) ? null : htmlspecialchars($value) );
        
        if (is_array($value)) {
            to_xml($new_object, $value);
        }
    }
}

$pdf = new PDF('L', 'mm', 'legal');
$pdf->SetMargins(35, 20, 30);
$pdf->SetAutoPageBreak(true, 20); 
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->AddPage();
$pdf->BasicTable($data, $tamanosCel, $dependencia,$execDepe);
$pdf->Output($rutaTemporal . $nombreDocumento, "F");
ob_clean();
flush();

$xml = new SimpleXMLElement('<Listado/>');
to_xml($xml, $data);
//print $xml->asXML();
$xml->asXML($destinoXml);

// Si no existe la carpeta se crea.
if (! is_dir($adjuntos)) {
    $rs = mkdir($adjuntos, 0700);
    if (empty($rs)) {
        echo "<h1> No se logrò Crear la carpeta del expediente $adjuntos. </h1>";
        return;
    }
}
$estadoOperacion = rename($rutaTemporal . $nombreDocumento, $destino);
if ($estadoOperacion) {
    $resultado = grabarRegistro($db, $expedienteIn, $tipoExt, $login, $fechaSave, $descr, $ruta . $nomFinal, $tpD, $dependencia, $nombreDocumento, $destino);//, $Historico);
    $resultado1 = grabarRegistro($db, $expedienteIn, 48, $login, $fechaSave, $descr, $ruta . $nomFinalXml, $tpD, $dependencia, $nombreDocumentoXml, $destinoXml);//, $Historico);
    if ($resultado) {       
        echo "<script>alert('Se generó el indice electrónico!!'); window.close();</script>";         
    } else {
        echo "<h1> No se logr&oacute; guardar el PDF del indice electr&oacute;nico en la carpeta del expediente. </h1>";
    }
}
?>