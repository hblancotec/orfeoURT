<?php

ini_set('set_time_limit', 0);
ini_set('display_errors', 1);
echo "iniciamos .. ".date('Ymd H:i:s'). "<br/>";
$ruta_raiz = ".";
include_once $ruta_raiz.'/config.php';
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");

$archivo = fopen("SGR.csv", "r");
while (($datos = fgetcsv($archivo, 0, ";")) == true) {
    $num = count($datos);
    $expediente = "";
    $nombre = "";
    
    for ($columna = 0; $columna < $num; $columna ++) {
        if ($columna == 0) {
            $nombre = trim($datos[$columna]);
        }
        if ($columna == 1) {
            $expediente = $datos[$columna];
        }
    }
    
    if ($expediente != "") {
        
        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $myData = array();
        
        $sql = "SELECT S.sgd_exp_numero AS NUMERO, R.RADI_PATH AS RUTA, T.SGD_TPR_DESCRIP, R.RADI_NUME_RADI, 
                CASE WHEN T.SGD_TPR_DESCRIP IS NULL THEN CONVERT(nvarchar(30), R.RADI_FECH_RADI, 105) + '_' + SUBSTRING(R.RA_ASUN, 1, 50) + '_' + CAST(R.RADI_NUME_RADI AS VARCHAR)
                     ELSE CONVERT(nvarchar(30), R.RADI_FECH_RADI, 105) + '_' + T.SGD_TPR_DESCRIP + '_' + CAST(R.RADI_NUME_RADI AS VARCHAR) 
                 END AS NOMBRE
                FROM SGD_SEXP_SECEXPEDIENTES S INNER JOIN SGD_EXP_EXPEDIENTE E ON S.sgd_exp_numero = E.SGD_EXP_NUMERO
        	   INNER JOIN RADICADO R ON R.RADI_NUME_RADI = E.RADI_NUME_RADI
			     INNER JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = R.TDOC_CODI
                WHERE S.sgd_exp_numero = '$expediente' ";
        $rs = $db->conn->Execute($sql);
        while ($rs && ! $rs->EOF) {
            
            $myData[] = array($rs->fields['NUMERO'], $rs->fields['RUTA'], $rs->fields['SGD_TPR_DESCRIP'], $rs->fields['NOMBRE']);
            
            $sqlanex = "SELECT CAST(A.ANEX_RADI_NUME AS VARCHAR) AS NUMERO, 
                    	CASE WHEN SUBSTRING(A.ANEX_NOMB_ARCHIVO, 1, 1) = 1 
                    		THEN SUBSTRING(A.ANEX_NOMB_ARCHIVO, 2, 4) + '/' + SUBSTRING(A.ANEX_NOMB_ARCHIVO, 6, 3) + '/docs/' + A.ANEX_NOMB_ARCHIVO 
                    	ELSE SUBSTRING(A.ANEX_NOMB_ARCHIVO, 1, 4) + '/' + SUBSTRING(A.ANEX_NOMB_ARCHIVO, 5, 3) + '/docs/' + A.ANEX_NOMB_ARCHIVO 
                    	END AS RUTA
                    	, T.SGD_TPR_DESCRIP
                    	, CASE WHEN T.SGD_TPR_DESCRIP IS NULL THEN CONVERT(nvarchar(30), R.RADI_FECH_RADI, 105) + '_' +  SUBSTRING(A.ANEX_DESC, 1 , 50) + '_' + CAST(A.ANEX_NUMERO AS VARCHAR)
                             ELSE CONVERT(nvarchar(30), R.RADI_FECH_RADI, 105) + '_' + T.SGD_TPR_DESCRIP + '_' + CAST(A.ANEX_NUMERO AS VARCHAR)
                         END AS NOMBRE
                        FROM ANEXOS A INNER JOIN RADICADO R ON A.ANEX_RADI_NUME = R.RADI_NUME_RADI
                        	LEFT JOIN SGD_TPR_TPDCUMENTO T ON T.SGD_TPR_CODIGO = A.SGD_TPR_CODIGO
                        WHERE R.RADI_NUME_RADI = " . $rs->fields['RADI_NUME_RADI']. " AND A.ANEX_BORRADO = 'N' ";
            $rsanex = $db->conn->Execute($sqlanex);
            while ($rsanex && ! $rsanex->EOF) {
                
                $myData[] = array($rsanex->fields['NUMERO'], $rsanex->fields['RUTA'], $rsanex->fields['SGD_TPR_DESCRIP'], $rsanex->fields['NOMBRE']);
                
                $rsanex->MoveNext();
            }
            
            $rs->MoveNext();
        }
                
        $sql1 = "SELECT A.SGD_EXP_NUMERO AS NUMERO, A.ANEXOS_EXP_PATH AS RUTA, T.SGD_TPR_DESCRIP, 
                CASE WHEN T.SGD_TPR_DESCRIP IS NULL THEN CONVERT(nvarchar(30), A.ANEXOS_EXP_FECH_CREA, 105) + '_' +  SUBSTRING(A.ANEXOS_EXP_DESC, 1 , 50) + '_' + CAST(A.ANEXOS_EXP_ID AS VARCHAR)
                     ELSE CONVERT(nvarchar(30), A.ANEXOS_EXP_FECH_CREA, 105) + '_' + T.SGD_TPR_DESCRIP + '_' + CAST(A.ANEXOS_EXP_ID AS VARCHAR)
                 END AS NOMBRE
                FROM SGD_ANEXOS_EXP A INNER JOIN SGD_TPR_TPDCUMENTO T ON A.SGD_TPR_CODIGO = T.SGD_TPR_CODIGO
                WHERE A.SGD_EXP_NUMERO = '$expediente'";
        $rs1 = $db->conn->Execute($sql1);
        while ($rs1 && ! $rs1->EOF) {
            
            $myData[] = array($rs1->fields['NUMERO'], $rs1->fields['RUTA'], $rs1->fields['SGD_TPR_DESCRIP'], $rs1->fields['NOMBRE']);
            
            $rs1->MoveNext();
        }
        
        //while ($arr = $rs->FetchRow()) {
        
        foreach ($myData as $arr) {
            
            $micarpeta = "./".$carpetaBodega."SGR/".$nombre;
            if (!file_exists($micarpeta)) {
                mkdir($micarpeta, 0777, true);
            }
            
            $rutaOr = "./".$carpetaBodega.$arr[1];
            
            $extension = pathinfo($rutaOr, PATHINFO_EXTENSION);
            
            $rutaDe = "./".$carpetaBodega."SGR/".$nombre."/".$arr[3].".".$extension;
            if (file_exists($rutaOr)) {
                if (copy($rutaOr, $rutaDe)) {
                    echo 'Se ha copiado el archivo corretamente' . "<br/>";
                }
                else {
                    echo 'Se produjo un error al copiar el fichero' . "<br/>";
                }
            } else {
                echo 'No existe el fichero: ' . $rutaOr . "<br/>";
            }
        }
    }
}
echo "Finalizamos .. ".date('Ymd H:i:s'). "<br/>";
?>