<?php


session_start();
if (count($_SESSION) == 0) {
    die(include "sinacceso.php");
    exit();
} else {
    $dependencia = $_SESSION["dependencia"];
}

require_once ("include/db/ConnectionHandler.php");
use Asika\Pdf2text;
include 'Pdf2text.php';

if (! $db) {
    $db = new ConnectionHandler('.');
}

$radicadoIn = htmlspecialchars($_GET['numRadicado']);
$anexoIn = htmlspecialchars($_GET['idAnexo']);
$expedienteIn = htmlspecialchars($_GET['expediente']);
$radicado = explode(",", $radicadoIn);
$anexo = explode(",", $anexoIn);
$data = array();

//Ibis: Captura de Información en BD para comparación
if ($db) {
    //query y procedimiento para comprobar que existe El indice
    $querySelectIndice ="SELECT ANEXOS_EXP_ID, ANEXOS_EXP_PATH
                            FROM SGD_ANEXOS_EXP
                            WHERE SGD_EXP_NUMERO = '".$expedienteIn."'
                            AND ANEXOS_EXP_NOMBRE like '%indice_%'
                            AND ANEX_TIPO_CODI = 7 ";
    $rsindice = $db->conn->Execute($querySelectIndice);
    if ($rsindice && !$rsindice->EOF) {
        $idIndice = $rsindice->fields['0'];
        $pathIndice = $rsindice->fields['1'];
    }
    
    if($idIndice){       
        ///Parte OCR
        $reader = new Pdf2text();
        $output = $reader->decode(BODEGAPATH.$pathIndice);
        $output = nl2br($output);
        
        $arrayInfo0 = explode("<br />", $output);
        $arrayFinal = array();
        $arrayTemp = array();
        $flag = 0;
        
        //echo var_dump($arrayInfo0);
        //exit;
        for ($i = 0; $i < count($arrayInfo0); $i ++) {
            if (strpos($arrayInfo0[1], "Dependencia: ") !== false) {
                if ($i > 24) {
                    if ($flag < 14) {
                        if (strpos($arrayInfo0[$i], "Dependencia: ")) {
                            $flag = 15;
                            $j = 0;
                        } else {
                            array_push($arrayTemp, $arrayInfo0[$i]);
                            $flag += 1;
                        }
                    }
                    if ($flag === 13) {
                        array_push($arrayFinal, $arrayTemp);
                        unset($arrayTemp);
                        $arrayTemp = array();
                        $flag = 0;
                    }
                    
                    /*if ($flag === 15) {
                        $j += 1;
                        if ($j === 6) {
                            $flag = 0;
                            $j = 0;
                        }
                    }*/
                }
            } else {
                echo "<script type='text/javascript'>alert('Formato de Indice Electrónico Erroneo.');
                                            window.close();</script>";
            }
        }
        
        for ($p = 0; $p < count($arrayFinal); $p ++) {
            unset($arrayFinal[$p][0]);
            unset($arrayFinal[$p][3]);
            for ($h = 5; $h < 7; $h ++) {
                unset($arrayFinal[$p][$h]);
            }
            for ($h = 8; $h < 13; $h ++) {
                unset($arrayFinal[$p][$h]);
            }
        }
        
        for ($i = 0; $i < count($radicado); $i ++) {
            // Ibis: Info Radicado Principal
           
            $sql = "SELECT 	R.RADI_FECH_RADI, U.USUA_LOGIN, R.RA_ASUN, R.RADI_PATH, R.RADI_NUME_HOJA, M.MREC_ORIGEN, R.RADI_FECH_OFIC, MT.hash, MT.funcion_hash, T.SGD_TPR_DESCRIP
                        FROM RADICADO R INNER JOIN USUARIO U ON R.RADI_USUA_RADI = U.USUA_CODI
    		                INNER JOIN DEPENDENCIA D ON R.RADI_DEPE_RADI = D.DEPE_CODI AND U.DEPE_CODI = D.DEPE_CODI
                            LEFT JOIN MEDIO_RECEPCION M ON M.MREC_CODI = R.MREC_CODI
                            LEFT JOIN METADATOS_DOCUMENTO MT ON MT.id_anexo = CAST(R.RADI_NUME_RADI AS VARCHAR)
                            LEFT JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = R.TDOC_CODI
                        WHERE R.RADI_NUME_RADI = $radicado[$i] ";
            $rsRad = $db->conn->Execute($sql);
            
            if ($rsRad && !$rsRad->EOF) {
                while ($atribut = $rsRad->fetchRow()) {
                    $v1 = $atribut[0];
                    $v2 = $atribut[1];
                    $v3 = $atribut[2];
                    $v4 = $atribut[3];
                    if ($v4) {
                        $rutaRadPrincipal = BODEGAPATH.$v4;
                        $funcion_hash = "sha1";
                        
                        $hashCalculo = " ";
                        if (file_exists($rutaRadPrincipal))
                            $hashCalculo = hash_file($funcion_hash, $rutaRadPrincipal);
                    }
                    $v5 = $atribut[4];
                    $v6 = $atribut[5];
                    $v7 = $atribut[6];
                    $v8 = $atribut[7];
                    $v9 = $atribut[8];
                    $tipoDoc = $atribut[9];
                }
            }
            $date = date_create($v1);
            $v1 = date_format($date,"Y-m-d H:i:s");
            
            $queryHistorico = "SELECT SGD_HFLD_FECH FROM SGD_HFLD_HISTFLUJODOC
                            WHERE SGD_EXP_NUMERO = '".$expedienteIn."' AND RADI_NUME_RADI = ".$radicado[$i]."
                            AND SGD_HFLD_OBSERVA = 'Incluir radicado en Expediente'";
            
            $fechaRad = $db->conn->Execute($queryHistorico)->fields['0'];
            
            $dataTemp = array();
            array_push($dataTemp, $radicado[$i]);
            array_push($dataTemp, $hashCalculo);
            array_push($dataTemp, $tipoDoc);
            array_push($dataTemp, $v1);
            array_push($dataTemp, $v1);
            
            array_push($data, $dataTemp);
            // Ibis: Fin Info Radicado Principal
            
            // Ibis: Info Anexos radicado
            for ($j = 0; $j < count($anexo); $j ++) {
                $rad = $radicado[$i];
                $anex = $anexo[$j]; 
                $cal = strpos($anex, $rad);
                if ($cal === false) {} else {
                    $sql2 = "SELECT A.ANEX_FECH_ANEX, A.ANEX_NOMB_ARCHIVO, D.SGD_TPR_DESCRIP, A.ANEX_CODIGO, A.ANEX_BORRADO 
                             FROM ANEXOS A LEFT JOIN METADATOS_DOCUMENTO M ON A.ANEX_CODIGO = M.id_anexo 
	                               LEFT JOIN SGD_TPR_TPDCUMENTO D ON D.SGD_TPR_CODIGO = M.codigo_tipoDocumental
			                 WHERE	ANEX_RADI_NUME = ".$radicado[$i]." AND ANEX_CODIGO = ".$anexo[$j];
                    $resultInfoAnexo2 = $db->conn->Execute($sql2);
                    
                    while ($atribut2 = $resultInfoAnexo2->fetchRow()) {
                        $fechaAnexRad = $fechaRad;
                        $nombreAnex = $atribut2[1];
                        $tipoDocAnex = $atribut2[2];
                        $codAnex = $atribut2[3];
                        $anexBorra = $atribut2[4];
                    }                
                    
                    $rutaRadAnex = substr($codAnex, 0,4);
                    if (strlen($radicado[$i]) == 14 ) {
                        $depAnex = substr($codAnex, 4, 3);
                    } if (strlen($radicado[$i]) == 15 ) {
                        $depAnex = ltrim(substr($codAnex, 4, 4), "0");
                    }
                    $rutaRadAnex = BODEGAPATH.$rutaRadAnex."/".$depAnex."/docs/".$nombreAnex;                    
                    $funcion_hash = "sha1";
                    $hashCalculo2 = " ";
                    if (file_exists($rutaRadAnex))
                        $hashCalculo2 = hash_file($funcion_hash, $rutaRadAnex);
                    
                    $dataTemp2 = array();
                 
                    array_push($dataTemp2, $anexo[$j]);
                    if ($anexBorra == 'S') {
                        array_push($dataTemp2, $hashCalculo2."1");
                    } else {
                        array_push($dataTemp2, $hashCalculo2);
                    }
                    array_push($dataTemp2, $tipoDocAnex);
                    array_push($dataTemp2, $fechaAnexRad);
                    array_push($dataTemp2, $v1);
                    
                    array_push($data, $dataTemp2);
                }
            }
        }
        // ibis: Fin Info Anex radicado
        
        $queryIdAnexExp = "SELECT A.ANEXOS_EXP_ID, A.ANEXOS_EXP_FECH_CREA, A.ANEXOS_EXP_PATH, D.SGD_TPR_DESCRIP 
                            FROM SGD_ANEXOS_EXP A LEFT JOIN SGD_TPR_TPDCUMENTO D ON D.SGD_TPR_CODIGO = A.SGD_TPR_CODIGO
                            WHERE SGD_EXP_NUMERO = '$expedienteIn' AND ANEXOS_EXP_DESC NOT LIKE '%Indice Electronico, Exp: %'
                                AND ANEXOS_EXP_NOMBRE NOT LIKE '%indice_%' AND ANEXOS_EXP_ESTADO != 1 ";
        $resultId = $db->conn->Execute($queryIdAnexExp);
        while ($execTemp = $resultId->fetchRow()) {
            $IdDoc = $execTemp['0'];
                //$fechaAnexExp = $atribut3[0];
               /*  $fechaAnexExp = str_replace('/', '-', $fechaAnexExp);
                $fechaAnexExp = strtotime($fechaAnexExp);
                $fechaAnexExp = date('Y-m-d', $fechaAnexExp); */
            $rutaAnexExp = $execTemp[2];                
            $tipoDocAnexExp = $execTemp[3];
            
            $queryFechaAnexExp = "SELECT fecha_produccion FROM METADATOS_DOCUMENTO WHERE ID_ANEXO = '$IdDoc'";
            $resultadoFechaAneExp = $db->conn->Execute($queryFechaAnexExp);            
            while ($temAtr = $resultadoFechaAneExp->fetchRow()) {
                $fechaAnexExp = $temAtr[0];
            }
                        
            $rutaAnexExp = str_replace('\\', '//', $rutaAnexExp);
            $rutaAnexExp = BODEGAPATH . $rutaAnexExp;
            $funcion_hash = "sha1";
            $hashCalculo3 = " ";
            if (file_exists($rutaAnexExp)) {
                $hashCalculo3 = hash_file($funcion_hash, $rutaAnexExp);
            }
            
            
            $dataTemp3 = array();            
            array_push($dataTemp3, $IdDoc);            
            array_push($dataTemp3, $hashCalculo3);
            array_push($dataTemp3, $tipoDocAnexExp);
            array_push($dataTemp3, $fechaAnexExp);
            array_push($dataTemp3, $fechaAnexExp);
            
            array_push($data, $dataTemp3);
        }
        
        foreach ($data as $clave => $fila) {
            $var1[$clave] = $fila[0];
            $var2[$clave] = $fila[1];
            $var3[$clave] = $fila[2];
            $var4[$clave] = $fila[3];
            $var5[$clave] = $fila[4];
        }
        
        array_multisort($var5, SORT_ASC, $data); //ordena por fecha   
        
        for ($p = 0; $p < count($data); $p ++) {
            unset($data[$p][4]);
        }
        
        $arrayFinal = array_values($arrayFinal);
        for ($k=0;$k<count($arrayFinal);$k++){
            $arrayFinal[$k]=array_values($arrayFinal[$k]);
        }
        //Ibis: Fin Captura de Información en BD para comparación
    }else{
        echo "<script type='text/javascript'>alert('Este expediente no tiene Indice Electronico.');
                                            </script>";
    }   
}

if (count($arrayFinal)===count($data)){
    $varValida = 0;
}else{
    $varValida = 2;
}

for ($p = 0; $p < count($data); $p ++) {
    unset($data[$p][3]);
}
for ($p = 0; $p < count($arrayFinal); $p ++) {
    unset($arrayFinal[$p][3]);
}

if ($varValida === 2){
    $resultado = "Cantidad de documentos en Indice no corresponde con la actual.";
} else {
    for($i=0; $i < count($arrayFinal); $i++) {
        for($j=0; $j < 2; $j++) {
            $cadena1 = (string)$arrayFinal[$i][$j];
            $cadena2 = (string)$data[$i][$j];
            $cadena1 = trim($cadena1);
            $cadena2 = trim($cadena2);
            
            if ($j == 0) {
                if($cadena1 != $cadena2){
                    if (strlen($resultado1) > 3) $resultado1 .= ", ";
                    $resultado1 .= $cadena1;
                    $varValida = 1;
                    //break;
                } else {
                    $resultado = "Validaci&oacute;n Exitosa.";
                }
            } elseif ($j == 1) {
                if($cadena1 != $cadena2){
                    if (strlen($resultado1) > 3) $resultado1 .= "<br>\n";
                    $resultado1 .= (string)$arrayFinal[$i][0] . " - " . $arrayFinal[$i][2];
                    $varValida = 1;
                    //break;
                } else {
                    $resultado = "Validaci&oacute;n Exitosa.";
                }
            }
        }
        if ($varValida == 1) {
            $resultado = "Estos documentos cambiaron en este Expediente." . "<br>\n" . $resultado1;
            //break;
        }
    }
}
?>

<html>
 <head>
  <title>Validación Indice Electronico</title>
  <link rel="stylesheet" href="estilos/orfeo.css">
 </head>
 <body>
  <form>
  <table border='1' cellpanding='2' cellspacing='0' class='borde_tab' valign='top' align='center' width='90%' scroll='yes'>
   <tr>
	<th class='titulos3'>Indice Electrónico: <?php echo $expedienteIn ?> </th>
   </tr>
   <tr class='listado' style="font:normal 11px Arial;">
    <td align="center"> <?php echo $resultado ?> </td>	
   </tr>	
  </table>
  <table align="center">
   <tr>
	<td>
	 <input align="center" name="button" type="button" class="botones_largo" onClick="window.close()" value="CERRAR">
	</td>
   </tr>
  </table>
 </form>
 </body>
</html>
