<?php
ini_set('set_time_limit', 0);
ini_set('display_errors', 1);
echo "iniciamos .. ".date('Ymd H:i:s'). "<br/>";
$ruta_raiz = ".";
include_once $ruta_raiz.'/config.php';
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");

/*$sql = "SELECT DISTINCT R.RADI_NUME_RADI AS RADICADO, R.RADI_FECH_RADI AS FECHA_RADICADO, R.RA_ASUN AS ASUNTO, R.RADI_PATH
FROM RADICADO R INNER JOIN SGD_CICLOFIRMADOMASTER CM ON R.RADI_NUME_RADI = CM.radi_nume_radi
	INNER JOIN SGD_CICLOFIRMADODETALLE CD ON CD.idcf = CM.idcf
WHERE R.RADI_FECH_RADI BETWEEN '2021-03-01 00:00:00' AND '2022-06-28'
	AND CD.usua_doc IN ('1018437628', '10184376281', '10184376282', '10184376283')
	AND CD.estado = 1
ORDER BY R.RADI_FECH_RADI";*/

/*$sql = "SELECT R.RADI_NUME_RADI, R.RADI_FECH_RADI, R.RA_ASUN, R.RADI_PATH 
FROM RADICADO R INNER JOIN HIST_EVENTOS H ON R.RADI_NUME_RADI = H.RADI_NUME_RADI
		INNER JOIN USUARIO U ON U.USUA_CODI = H.USUA_CODI_DEST AND U.DEPE_CODI = H.DEPE_CODI_DEST
		INNER JOIN SGD_DIR_DRECCIONES DR ON DR.RADI_NUME_RADI = R.RADI_NUME_RADI
WHERE (H.SGD_TTR_CODIGO = 2 OR H.SGD_TTR_CODIGO = 9) AND H.DEPE_CODI_DEST IN (652)
	AND R.RADI_FECH_RADI BETWEEN '20210801' AND '20220718'
	AND R.RADI_TIPORAD = 5 AND H.HIST_FECH = (select Max(E.HIST_FECH) AS FECHA from HIST_EVENTOS E WHERE E.RADI_NUME_RADI = H.RADI_NUME_RADI AND (E.SGD_TTR_CODIGO = 2 OR E.SGD_TTR_CODIGO = 9))
ORDER BY R.RADI_FECH_RADI";*/

$sql = "SELECT R.RADI_NUME_RADI, R.RADI_FECH_RADI, R.TDOC_CODI, D.sgd_firma_detalle, SUBSTRING(cast(R.RADI_NUME_RADI as varchar(15)), 14, 1) as tipo,
	R.RADI_PATH as RADI_PATH, RIGHT(M.rutapdf, CHARINDEX('/', REVERSE(M.rutapdf)) -1) as FILE_NAME
FROM RADICADO R INNER JOIN SGD_CICLOFIRMADOMASTER M ON R.RADI_NUME_RADI = M.radi_nume_radi
	INNER JOIN SGD_CICLOFIRMADODETALLE D ON M.idcf = D.idcf
WHERE R.RADI_FECH_RADI BETWEEN '20230101' AND '20241231' AND D.ESTADO = 1 AND D.usua_login in ( 'PAULA.VILLA')
order by R.RADI_FECH_RADI";

// expediente completo
/*$sql = "SELECT S.sgd_exp_numero, R.RADI_PATH AS RUTA
        FROM SGD_SEXP_SECEXPEDIENTES S INNER JOIN SGD_EXP_EXPEDIENTE E ON S.sgd_exp_numero = E.SGD_EXP_NUMERO
        	INNER JOIN RADICADO R ON R.RADI_NUME_RADI = E.RADI_NUME_RADI
        WHERE S.sgd_exp_numero = '200965109099800012E'
        UNION ALL
        SELECT A.SGD_EXP_NUMERO, A.ANEXOS_EXP_PATH AS RUTA
        FROM SGD_ANEXOS_EXP A 
        WHERE A.SGD_EXP_NUMERO = '200965109099800012E'";*/

/*$sql = "  SELECT C.radi_nume_radi, D.sgd_firma_detalle, c.rutapdf AS RADI_PATH, RIGHT(c.rutapdf, CHARINDEX('/', REVERSE(c.rutapdf)) -1) as FILE_NAME
  FROM SGD_CICLOFIRMADOMASTER C INNER JOIN SGD_CICLOFIRMADODETALLE D ON C.idcf = D.idcf
  WHERE D.usua_login = 'IESLAVA1' AND d.sgd_firma_detalle BETWEEN '20230201' AND '20230228'
	and C.estado != 4"; */

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->conn->Execute($sql);
//echo "Radicado,Fecha_Radicacion,Asunto,Ruta". "<br/>";
while ($arr = $rs->FetchRow()) {
	
    $rutaOr = "./".$carpetaBodega.$arr['RADI_PATH'];
    $rutaDe = "./".$carpetaBodega."PAULAVILLA/".basename($rutaOr);
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
	
    //echo $arr['radi_nume_radi'].",". $arr['radi_fech_radi'].",".$arr['radi_path'].",". $esRespRapi.",".$esMasiva .",". $esGesproy.",".$esSuiffp.",".$esEnviado.",".$formaEnvio.",".$haReasigna.",".$anulacion.",".$dependencia.",".$usrRadicador."<br/>";
}
echo "Finalizamos .. ".date('Ymd H:i:s'). "<br/>";
?>