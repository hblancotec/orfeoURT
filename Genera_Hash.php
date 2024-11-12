<?php
ini_set('set_time_limit', 0);
ini_set('display_errors', 1);
echo "iniciamos .. ".date('Ymd H:i:s'). "<br/>";
$ruta_raiz = ".";
include_once $ruta_raiz.'/config.php';
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");

function debug($filename, $data)
{
    file_put_contents($filename, $data, FILE_APPEND);
}

function getNumPagesPdf($filepath) { 
    $fp = @fopen(preg_replace("/\[(.*?)\]/i", "",$filepath),"r"); 
    $max=0; 
    while(!feof($fp)) { 
        $line = fgets($fp,255); 
        if (preg_match('/\/Count [0-9]+/', $line, $matches)) { 
            preg_match('/[0-9]+/',$matches[0], $matches2); if ($max<$matches2[0]) $max=$matches2[0]; 
        } 
    } 
    fclose($fp); 
    if($max==0) { 
        $im = new imagick($filepath); 
        $max=$im->getNumberImages(); 
    } 
    return $max; 
}

//Fuente: https://www.iteramos.com/pregunta/51683/contar-el-numero-de-paginas-en-un-pdf-en-solo-php

$fyh = BODEGAPATH . "debug_" . date('Ymd_His') . ".html";
debug($fyh, "inicio script" . "\n");
/*$sql = "SELECT [secuencia]
      ,[id_anexo]
      ,[hash]
      ,[folios]
	  ,A.ANEX_NOMB_ARCHIVO
  FROM METADATOS_DOCUMENTO M inner join ANEXOS A ON M.id_anexo = A.ANEX_CODIGO
  WHERE M.hash IS NULL ";*/

$sql = "SELECT [ANEXOS_EXP_ID], [SGD_EXP_NUMERO], [ANEXOS_EXP_PATH], [DEPE_CODI], ANEXOS_EXP_FECH_CREA
  FROM [GdOrfeo].[dbo].[SGD_ANEXOS_EXP]
  WHERE ANEXOS_EXP_FECH_CREA BETWEEN '20220101' AND '20221231'
  ORDER BY ANEXOS_EXP_FECH_CREA";

/*$sql = "SELECT A.ANEX_RADI_NUME, A.ANEX_CODIGO, A.ANEX_NOMB_ARCHIVO, A.ANEX_RADI_FECH
      FROM ANEXOS A
      WHERE A.ANEX_RADI_FECH BETWEEN '20221101' AND '20221231'
      ORDER BY A.ANEX_RADI_NUME"; */

/*$sql = "SELECT R.RADI_NUME_RADI, R.RADI_PATH, R.RADI_FECH_RADI
        FROM RADICADO R
        WHERE R.RADI_FECH_RADI BETWEEN '20221101' AND '20221231'
        ORDER BY R.RADI_FECH_RADI";*/
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->conn->Execute($sql);
//echo "Radicado,Fecha_Radicacion,Asunto,Ruta". "<br/>";
while ($arr = $rs->FetchRow()) {
	
    $coddocu = $arr['ANEXOS_EXP_ID'];
    //$rutaOr = "./".$carpetaBodega.substr(trim($coddocu),0,4)."/".substr(trim($coddocu),4,3)."/docs/".$arr['ANEX_NOMB_ARCHIVO'];
    //$rutaOr = "./".$carpetaBodega."/".$arr['RADI_PATH'];
    $rutaOr = "./".$carpetaBodega.$arr['ANEXOS_EXP_PATH'];
    if (file_exists($rutaOr)) {
        
        $hash = hash_file('sha1', $rutaOr);
        
        $selM = "SELECT id_anexo FROM METADATOS_DOCUMENTO WHERE id_anexo = '" . $coddocu . "'";
        $rsM = $db->conn->Execute($selM);
        if ($rsM && !$rsM->EOF) {
            $sqlUA 	= "	UPDATE METADATOS_DOCUMENTO SET hash = '$hash' WHERE	id_anexo = " . $coddocu;
            $rsUp = $db->conn->Execute($sqlUA);
            
            debug($fyh, "ACTUALIZAR METADATOS_DOCUMENTO: $rsUp - $sqlUA \n");
        } else {

            //$im = new Imagick($rutaOr);
            //$folios = getNumPagesPdf($rutaOr);  
            //$im->getnumberimages();
            //$im->destroy();
            
            $im = new imagick($filepath);
            $folios = $im->getNumberImages(); 
            
            $sqlIns = "INSERT INTO METADATOS_DOCUMENTO ([id_anexo], [id_tipo_anexo], [hash], [funcion_hash], [folios]
                    ,[nombre_proyector], [nombre_revisor], [nombre_firma], [palabras_clave], [codigo_tipoDocumental], [fecha_produccion]) ".
                "VALUES ($coddocu, 0,'$hash', 'sha1', '$folios', '', '', '', '', NULL, '".$arr['ANEX_RADI_FECH']."')";        
            $rsIns = $db->conn->Execute( $sqlIns);
            
            debug($fyh, "ACTUALIZAR TRD: $rsIns - $sqlIns \n");
        } 
        
        
        //$sql = "UPDATE METADATOS_DOCUMENTO SET hash = '$hash' WHERE secuencia = " . $arr['SECUENCIA'];
        //$resp = $db->conn->Execute($sql);
        
    } else {
        echo 'No existe el fichero: ' . $rutaOr . "<br/>";
    }
	
    //echo $arr['radi_nume_radi'].",". $arr['radi_fech_radi'].",".$arr['radi_path'].",". $esRespRapi.",".$esMasiva .",". $esGesproy.",".$esSuiffp.",".$esEnviado.",".$formaEnvio.",".$haReasigna.",".$anulacion.",".$dependencia.",".$usrRadicador."<br/>";
}
echo "Finalizamos .. ".date('Ymd H:i:s'). "<br/>";
?>