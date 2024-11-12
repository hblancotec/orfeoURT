<?php

$ruta_raiz = "..";
include "$ruta_raiz/config.php";   
define('ADODB_ASSOC_CASE', 1);
include 'adodb/adodb.inc.php'; 
$dsn = $driver . "://" . $usuario . ":" . $contrasena . "@" . $servidor . "/" . $db;
$conn = NewADOConnection($dsn);

if ($conn) {
    $conn->SetFetchMode(ADODB_FETCH_ASSOC);

    $respuesta = "";
    
    if($_POST['filtrar']) {
        
        $tipoDoc = "";
        $subSerie = "";
        $serie = "";
        $sqlMRD = "SELECT M.SGD_SRD_CODIGO, M.SGD_SBRD_CODIGO, M.SGD_TPR_CODIGO
                FROM SGD_RDF_RETDOCF R INNER JOIN SGD_MRD_MATRIRD M ON R.SGD_MRD_CODIGO = M.SGD_MRD_CODIGO
                WHERE R.RADI_NUME_RADI = " . $_POST['radicado'] ;
        $rsMRD = $conn->Execute($sqlMRD);
        if ($rsMRD && !$rsMRD->EOF) {
            $serie = $rsMRD->fields["SGD_SRD_CODIGO"];
            $subSerie = $rsMRD->fields["SGD_SBRD_CODIGO"];
            $tipoDoc = $rsMRD->fields["SGD_TPR_CODIGO"];
        }
        
        $pqr = "";
        $sqlPqr = "	SELECT	SGD_TPR_CODIGO FROM	SGD_TEMAS_TIPOSDOC WHERE SGD_TPR_CODIGO = $tipoDoc";
        $rsPqr = $conn->Execute($sqlPqr);
        if ($rsPqr && !$rsPqr->EOF) {
            $pqr = $rsPqr->fields['SGD_TPR_CODIGO'];
        }
        
        $path = "";
        $cambio = '0';
        $sql =	"SELECT RADI_PATH, SGD_CAMBIO_TRD FROM RADICADO WHERE RADI_NUME_RADI = ". $_POST['radicado'];
        $rs = $conn->query($sql);
        if ($rs && !$rs->EOF) {
            $path = $rs->fields['RADI_PATH'];
            $cambio = $rs->fields['SGD_CAMBIO_TRD'];
        }
                       
        if ($path == "") {
            $respuesta = json_encode(1);
        } elseif ($cambio == 2) {
            $respuesta = json_encode(3);
        } elseif ($cambio == '1') {
            $respuesta = json_encode(4);
        }elseif (($cambio == '' || $cambio == '0') && ($serie == 176 || $pqr != null)) {
            $respuesta = json_encode(2);
        } else {
            $respuesta = json_encode(0);
        }
    }
    elseif($_POST['anexoidexp']) {
        
        $anexoIdExp = $_POST['anexoidexp'];
        $tipo = $_POST['tipo'];
        
        if ($tipo == '1') {
            $querySelectId = "SELECT A.ANEXOS_EXP_NOMBRE AS NOMBRE, A.ANEXOS_EXP_PATH AS RUTA, A.ANEXOS_EXP_ID, M.hash AS HS 
                        FROM SGD_ANEXOS_EXP A LEFT JOIN METADATOS_DOCUMENTO M ON M.id_anexo = convert(varchar(20),a.ANEXOS_EXP_ID)
                        WHERE A.ANEXOS_EXP_ID  = '$anexoIdExp'";
        } elseif ($tipo == '2') {
            $querySelectId = "SELECT R.RADI_NUME_RADI, R.RADI_PATH AS RUTA, M.hash AS HS 
                        FROM RADICADO R LEFT JOIN METADATOS_DOCUMENTO M ON M.id_anexo = convert(varchar(20), R.RADI_NUME_RADI)
                        WHERE R.RADI_NUME_RADI = '$anexoIdExp'";
        } elseif ($tipo == '3') {
            $querySelectId = "SELECT A.ANEX_NOMB_ARCHIVO AS NOMBRE, SUBSTRING(A.ANEX_CODIGO, 1, 4) + '/' + SUBSTRING(A.ANEX_CODIGO, 5, 3) + '/docs/' + A.ANEX_NOMB_ARCHIVO AS RUTA, 
	                    A.ANEX_CODIGO, M.hash AS HS 
                        FROM ANEXOS A LEFT JOIN METADATOS_DOCUMENTO M ON M.id_anexo = convert(varchar(20), A.ANEX_CODIGO)
                        WHERE A.ANEX_CODIGO = '$anexoIdExp'";
        }
        
        $rsh = $conn->Execute($querySelectId);
        $hashSave = $rsh->fields['HS'];
        $ruta = $rsh->fields['RUTA'];
        $ruta = str_replace("\\","/",$ruta);
        $rutahash = BODEGAPATH.$ruta;
        $funcion_hash = "sha1";
        $hashCalculo = "";
        if (file_exists($rutahash)) {
            $hashCalculo = hash_file($funcion_hash, $rutahash);
            if ($hashSave===$hashCalculo) {
                $respuesta = "Validación Exitosa.";
            } else {
                $respuesta = "Documento Cambiado.";
            } 
        } else {
            $respuesta = "Documento No Existe.";
        }
    }
    else {
        $respuesta = "Faltan datos bÃ¡sicos";
    }
} else {
    $respuesta = "No hay conexion con la base de datos";
}

echo $respuesta;

?>