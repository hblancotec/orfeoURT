<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "sinacceso.php");
    exit;
}
$anexo = htmlspecialchars($_GET['anexo']);
$numExpediente = htmlspecialchars($_GET['expediente']);

include("adodb/tohtml.inc.php");
require_once("include/db/ConnectionHandler.php");
if (!$db)
    $db = new ConnectionHandler('.');
    if ($db) {
        $sql ="SELECT			convert(varchar(10), A.ANEXOS_EXP_FECH_CREA,103) AS FECHA_CREACION,
                                A.USUA_LOGIN_CREA AS PROPIETARIO,
                                A.ANEXOS_EXP_PATH AS PATH,
								A.ANEXOS_EXP_DESC 	AS TITULO
						FROM	SGD_ANEXOS_EXP A
						WHERE	A.SGD_EXP_NUMERO = '$numExpediente'
								AND A.ANEXOS_EXP_ID = '$anexo'";
        $resultInfoAnexoR = $db->conn->Execute($sql);
        
        
        $queryTipoDocumental ="SELECT concat(SGD_TPR_CODIGO ,' - ',SGD_TPR_DESCRIP) AS ' '
                        	FROM SGD_TPR_TPDCUMENTO AS Tpd
                        	WHERE Tpd.SGD_TPR_CODIGO =(
                        	    SELECT SGD_TPR_CODIGO
                        	    FROM SGD_ANEXOS_EXP
                        	    WHERE ANEXOS_EXP_ID =  '$anexo')";
        $resultTipoDocumental = $db->conn->Execute($queryTipoDocumental);
        
        
        
        $selectSQL = "SELECT folios,palabras_clave,hash,funcion_hash,nombre_proyector,nombre_revisor,fecha_produccion". //id_anexo,id_tipo_anexo,hash,funcion_hash,folios,nombre_proyector,nombre_revisor,nombre_firma,palabras_clave
            " FROM METADATOS_DOCUMENTO
                  WHERE id_anexo = '$anexo'";
        $resultMetadatos = $db->conn->Execute($selectSQL);
        if ($resultMetadatos && !$resultMetadatos->EOF) {
            $nombreProyector= $resultMetadatos->fields[4];
            $nombreRevisor = $resultMetadatos->fields[5];
            $fechaProduccion = $resultMetadatos->fields[6];
        }
        
        $queryS= "	SELECT	S.SGD_SEXP_PAREXP1,	D.DEPE_NOMB,
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
				WHERE	S.SGD_EXP_NUMERO = '$numExpediente'";
        
        $resultInforExp = $db->conn->Execute($queryS);
    }
    
    ?>

<html>
 <head>
  <title>Metadatos del Anexo Expediente</title>
  <link rel="stylesheet" href="estilos/orfeo.css">
 </head>
 <body>
  <form>

  <table border='1' cellpanding='2' cellspacing='0' class='borde_tab' valign='top' align='center' width='90%' scroll='yes'>
   <tr>
	<th class='titulos3'>Fecha entrada</th>
	<th class='titulos3'>Usuario</th>
	<th class='titulos3'>Tamaño (KB) </th>
	<th class='titulos3'>Titulo </th>	
   </tr>
   
<?php 
if ($resultInfoAnexoR !== false) {    
while ($atribut = $resultInfoAnexoR->fetchRow()) {
    $filesize = "";
    if (file_exists(BODEGAPATH.$atribut[2]))
        $filesize = filesize(BODEGAPATH.$atribut[2])/1000;
?>
   <tr class='listado' style="font:normal 11px Arial;">
    <td> <?php echo $atribut[0]; ?> </td>
	<td> <?php echo $atribut[1]; ?> </td>
	<td> <?php echo $filesize ?> </td>
	<td> <?php echo $atribut[3]; ?> </td>
   </tr>
<?php	   
	}
}
?>
	<tr>
	<th class='titulos3'>Expediente</th>
	<th class='titulos3'>Entidad productora </th>
	<th class='titulos3'>Serie </th>
	<th class='titulos3'>SubSerie </th>	
   </tr>
<?php 
while ($campo = $resultInforExp->fetchRow()) { 
?>
   <tr class='listado' style="font:normal 11px Arial;">
    <td> <?php echo $campo[6] ."<br>".$campo[0]; ?> </td>
	<td> <?php echo $campo[1]; ?> </td>
	<td> <?php echo $campo[2] ." - ". $campo[3]; ?> </td>
	<td> <?php echo $campo[4] ." - ". $campo[5]; ?> </td>
   </tr>
<?php	   
	}
?>
   <tr>
	<th class='titulos3'>Folios </th>
	<th class='titulos3'>Palabras Clave </th>	
	<th class='titulos3'>Hash </th>
	<th class='titulos3'>Funcion Hash </th>
	
	
   </tr>
   
<?php 
if ($resultMetadatos !== false) 
while ($atrib = $resultMetadatos->fetchRow()) { 
?>

   <tr class='listado' style="font:normal 11px Arial;">
    <td> <?php echo $atrib[0]; ?> </td>
	<td> <?php echo $atrib[1]; ?> </td>	
	<td> <?php echo $atrib[2]; ?> </td>
	<td> <?php echo $atrib[3]; ?> </td>
	
   </tr>
<?php	   
	}
?>
	<tr>
	<th class='titulos3'>Nombre proyector </th>
	<th class='titulos3'>Nombre revisor </th>
	<th class='titulos3'>Tipo documental </th>
	<th class='titulos3'>Código del documento </th>	
   </tr>
	<tr class='listado' style="font:normal 11px Arial;">
	<td> <?php echo $nombreProyector; ?> </td>
	<td> <?php echo $nombreRevisor; ?> </td>
    <td> <?php echo $resultTipoDocumental; ?> </td>	
     <td> <?php echo $anexo; ?> </td>	 
    
   </tr>
   
   <tr>
   <th class='titulos3'>Fecha Producci&oacute;n</th>
   </tr>
   <tr class='listado' style="font:normal 11px Arial;"> 
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