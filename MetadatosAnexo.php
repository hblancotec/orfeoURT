<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "sinacceso.php");
    exit;
}
$radicado = htmlspecialchars($_GET['numRadicado']);
$anexo = htmlspecialchars($_GET['idAnexo']);
include("adodb/tohtml.inc.php");
require_once("include/db/ConnectionHandler.php");

if (!$db)
    $db = new ConnectionHandler('.');
    if ($db) {
        if($anexo == ''){ //aplica para el digitalizador
            $anexo =  $radicado;
            $sql = "SELECT TOP (1)	A.fecha ,U.USUA_NOMB,'Digitalizador',R.RADI_PATH
        			FROM	SGD_HIST_IMG_ANEX_RAD A
					INNER JOIN USUARIO U ON A.USUA_LOGIN = U.USUA_LOGIN
                    INNER JOIN RADICADO R ON A.ANEX_RADI_NUME=R.RADI_NUME_RADI
			WHERE	A.ANEX_RADI_NUME = $radicado
			AND     A.ID_TTR_HIAN = '23' 
            ORDER BY A.fecha ASC";
            
            $resultInfoAnexo = $db->conn->Execute($sql);
            
            if($resultInfoAnexo->EOF){
                $sql = "SELECT TOP (1)	A.fecha ,U.USUA_NOMB,'Digitalizador',R.RADI_PATH
        			FROM	SGD_HIST_IMG_RAD A
					INNER JOIN USUARIO U ON A.USUA_LOGIN = U.USUA_LOGIN
                    INNER JOIN RADICADO R ON A.RADI_NUME_RADI=R.RADI_NUME_RADI
        			WHERE	A.RADI_NUME_RADI = $radicado        			
                    ORDER BY A.fecha DESC";
                
                $resultInfoAnexo = $db->conn->Execute($sql);
            }          
                
        }else{
            $sql = "SELECT 	A.ANEX_FECH_ANEX ,		U.USUA_NOMB ,
                    A.ANEX_DESC,           A.ANEX_TAMANO
			FROM	ANEXOS A
					INNER JOIN USUARIO U ON A.ANEX_CREADOR=U.USUA_LOGIN
			WHERE	ANEX_RADI_NUME=$radicado
					AND ANEX_CODIGO=$anexo";
            $resultInfoAnexo = $db->conn->Execute($sql);
        }
        $selectSQL = "SELECT TOP (1) folios,palabras_clave,hash,funcion_hash,nombre_proyector,codigo_tipoDocumental,fecha_produccion,nombre_revisor". //id_anexo,id_tipo_anexo,hash,funcion_hash,folios,nombre_proyector,nombre_revisor,nombre_firma,palabras_clave
                    " FROM METADATOS_DOCUMENTO
            	   	 WHERE id_anexo = '$anexo' order by secuencia DESC ";
        $resultMetadatos = $db->conn->Execute($selectSQL);
        if ($resultMetadatos && !$resultMetadatos->EOF) {
            $folios = $resultMetadatos->fields[0];
            $palClave = $resultMetadatos->fields[1];
            $hash = $resultMetadatos->fields[2];
            $funhash = $resultMetadatos->fields[3];
            $nombreProyector= $resultMetadatos->fields[4]; // aplica para cuando se necesita un sola columna
            $tipoDocumentalTemporal = $resultMetadatos->fields[5];
            $fechaProduccion = $resultMetadatos->fields[6];
            $nombreRevisor = $resultMetadatos->fields[7];
            
            $selectSQL = "SELECT folios,palabras_clave,hash,funcion_hash,nombre_proyector,fecha_produccion,nombre_revisor".
                " FROM METADATOS_DOCUMENTO
            	   	     WHERE id_anexo = '$radicado'";
            $resultMetadatos1 = $db->conn->Execute($selectSQL);
            if ($resultMetadatos1 && !$resultMetadatos1->EOF) {
                $folios = $resultMetadatos->fields[0];
                $palClave = $resultMetadatos->fields[1];
                $hash = $resultMetadatos->fields[2];
                $funhash = $resultMetadatos->fields[3];
                $nombreProyector= $resultMetadatos1->fields[4];
                $fechaProduccion = $resultMetadatos1->fields[5];
                $nombreRevisor = $resultMetadatos1->fields[6];
            }
        }
        
        //if($resultMetadatos->EOF){ // Para el caso del anexo PADRE al radicado el id del anexo es el numero de radicado
            
        //}
        
        $queryExpdi= "	SELECT	S.SGD_SEXP_PAREXP1,	D.DEPE_NOMB,
						SR.SGD_SRD_CODIGO, SR.SGD_SRD_DESCRIP, SB.SGD_SBRD_CODIGO,SB.SGD_SBRD_DESCRIP,
                        SGD_EXP_NUMERO
				FROM	SGD_SEXP_SECEXPEDIENTES AS S
						JOIN DEPENDENCIA AS D ON
							D.DEPE_CODI = S.DEPE_CODI
						LEFT JOIN USUARIO AS U ON
							U.USUA_DOC = S.USUA_DOC_RESPONSABLE
						JOIN SGD_SRD_SERIESRD SR ON
							SR.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
						JOIN SGD_SBRD_SUBSERIERD SB ON
						SB.SGD_SBRD_CODIGO = S.SGD_SBRD_CODIGO
                        AND	SB.SGD_SRD_CODIGO = S.SGD_SRD_CODIGO
				WHERE	S.SGD_EXP_NUMERO = (SELECT SGD_EXP_NUMERO
			                                 FROM	SGD_EXP_EXPEDIENTE
			                                 WHERE	RADI_NUME_RADI='$radicado')";
        
        $resultInfoExpe = $db->conn->Execute($queryExpdi);
        
        if($tipoDocumentalTemporal == ''){
            /**
             La consulta se tomó de la opcion tipificar del archivo "lista_tiposAsignados.php"
             */
            $queryTpD = "	SELECT	concat(t.SGD_TPR_CODIGO ,' - ',t.SGD_TPR_DESCRIP) AS ' '
    			FROM	SGD_RDF_RETDOCF mf,				SGD_MRD_MATRIRD m,
    					DEPENDENCIA d,					SGD_SRD_SERIESRD s,
    					SGD_SBRD_SUBSERIERD su,			SGD_TPR_TPDCUMENTO t
    	   		WHERE	d.depe_codi = mf.depe_codi
    					and s.sgd_srd_codigo  = m.sgd_srd_codigo
    					and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
    					and su.sgd_srd_codigo = m.sgd_srd_codigo
    					and t.sgd_tpr_codigo  = m.sgd_tpr_codigo
    					and mf.sgd_mrd_codigo = m.sgd_mrd_codigo
    					and mf.radi_nume_radi = '$radicado'";
            $resultadoTipoDoc = $db->conn->Execute($queryTpD);
        }else{
            $queryTpD = " SELECT	concat(t.SGD_TPR_CODIGO ,' - ',t.SGD_TPR_DESCRIP) AS ' '
                        FROM	SGD_TPR_TPDCUMENTO t where t.SGD_TPR_CODIGO = $tipoDocumentalTemporal";
            $resultadoTipoDoc = $db->conn->Execute($queryTpD);
        }
        // VER QUIEN FIRMO UN RADICADO                               CD.estado   --0=Solicitado 1=Firmado 2=Modificacion 3=Rechazado 4=FinalizadoPorRechazo
      # $radicadoTEMP ="20189000000043"; // caso puntual para un radicado de un memorando que actualmente se Firma
        $radicadoTEMP = $radicado;
        $queryFirmadoRad = "SELECT DISTINCT(CD.usua_login), CM.radi_nume_radi,  CD.estado
	                    FROM SGD_CICLOFIRMADOMASTER AS CM,SGD_CICLOFIRMADODETALLE AS CD
	                    WHERE CM.idcf = CD.idcf AND CM.radi_nume_radi = '$radicadoTEMP' AND CD.estado = 1";
        
        $resultadoFirmadoRad = $db->conn->Execute($queryFirmadoRad);
        $nombreFirma= $resultadoFirmadoRad->fields[0];
    }
    ?>

<html>
 <head>
  <title>Metadatos del Anexo</title>
  <link rel="stylesheet" href="estilos/orfeo.css">
 </head>
 <body>
  <form>

  <table border='1' cellpanding='2' cellspacing='0' class='borde_tab' valign='top' align='center' width='90%' scroll='yes'>
   <tr>
	<th class='titulos3'>Fecha de entrada </th>
	<th class='titulos3'>Usuario </th>
	<th class='titulos3'>Titulo </th>
	<th class='titulos3'>Tamaño (KB) </th>
   </tr>
   
<?php
while ($atribut = $resultInfoAnexo->fetchRow()) { 
?>

   <tr class='listado' style="font:normal 11px Arial;">
    <td> <?php echo $atribut[0]; ?> </td>
	<td> <?php echo $atribut[1]; ?> </td>
	<td> <?php echo $atribut[2]=='Digitalizador'?"Digitalizador ".$radicado:$atribut[2];  ?> </td>
	<td> <?php echo strpos($atribut[3], "/")?filesize(BODEGAPATH.$atribut[3])/1000 : $atribut[3]; ?> </td>
   </tr>

<?php
	}
?>

	<tr>	
	<th class='titulos3'>Expediente</th>
	<th class='titulos3'>Entidad productora </th>
	<th class='titulos3'>Serie </th>
	<th class='titulos3'>SubSerie </th>	
   </tr>
<?php 
if ($resultInfoExpe) {
while ($campo = $resultInfoExpe->fetchRow()) { 
?>
   <tr class='listado' style="font:normal 11px Arial;"> 
    <td> <?php echo $campo[6] ."<br>". $campo[0]; ?> </td>
	<td> <?php echo $campo[1]; ?> </td>
	<td> <?php echo $campo[2] ." - ". $campo[3]; ?> </td>
	<td> <?php echo $campo[4] ." - ". $campo[5]; ?> </td>
   </tr>
<?php	   
	}
}
?>
   <tr>
	<th class='titulos3'>Folios </th>
	<th class='titulos3'>Palabras Clave </th>
	<th class='titulos3'>Hash </th>
	<th class='titulos3'>Funcion Hash </th>
	
   </tr>
   
	<tr class='listado' style="font:normal 11px Arial;">
    	<td> <?php echo $folios; ?> </td>
        <td> <?php echo $palClave; ?> </td>
        <td> <?php echo $hash; ?> </td>
        <td> <?php echo $funhash; ?> </td>
    </tr>
    
    <tr>
    		<th class='titulos3'>Nombre proyector </th>
			<th class='titulos3'>Tipo documental </th>
			<th class='titulos3'>Código del documento </th>
			<th class='titulos3'>Nombre firma </th>
   		</tr>
    	<tr class='listado' style="font:normal 11px Arial;">
    		<td> <?php echo $nombreProyector ?> </td>
            <td> <?php echo $resultadoTipoDoc; ?> </td>	
            <td> <?php echo $anexo; ?> </td>
            <td> <?php echo $nombreFirma; ?> </td>	
       </tr>
       <tr>
      		<th class='titulos3'>Nombre revisor </th>       
    		<th class='titulos3'>Fecha Producci&oacute;n</th>
    	</tr>
    	<tr class='listado' style="font:normal 11px Arial;">
    		<td> <?php echo $nombreRevisor; ?> </td>
    		<td> <?php echo $fechaProduccion ?> </td>
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