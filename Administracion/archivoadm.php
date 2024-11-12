<?php
	$ruta_raiz = "..";
	session_start();
	if(!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";	
	$phpsession = session_name()."=".session_id();
?>
<html>
<head>
<title>Documento  sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body>
  <table width="51%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
  <tr bordercolor="#FFFFFF">
    <td colspan="2" class="titulos4"><div align="center"><strong>M&Oacute;DULO DE ADMINISTRACI&Oacute;N ARCHIVO</strong></div></td>
  </tr>
  <tr bordercolor="#FFFFFF">    
      <td align="center" class="listado2" width="48%">
	<a href='http://localhost:8080/ExpedientePorDependencia_manejo?<?=$phpsession ?>&krd=<?=$krd?>&mode=<?=orfeo?> ' target='mainFrame' class="vinculos">1. TABLA DE EXPEDIENTES POR DEPENDENCIA</a>
      </td>
  </tr>
  <tr bordercolor="#FFFFFF">    
      <td align="center" class="listado2" width="48%">
	<a href='http://localhost:8080/RadicadoPorExpediente_manejo?<?=$phpsession ?>&krd=<?=$krd?>&mode=<?=orfeo?> ' target='mainFrame' class="vinculos">2. TABLA DE RADICADOS POR EXPEDIENTES</a>
      </td>
  </tr>
  <tr bordercolor="#FFFFFF">    
      <td align="center" class="listado2" width="48%">
	<a href='http://localhost:8080/CampoExpediente_manejo?<?=$phpsession ?>&krd=<?=$krd?>&mode=<?=orfeo?> ' target='mainFrame' class="vinculos">3. TABLA DE CAMPOS Y ETIQUETAS PARA EXPEDIENTES</a>
      </td>
  </tr>
  <tr bordercolor="#FFFFFF">    
      <td align="center" class="listado2" width="48%">
	<a href='http://localhost:8080/EtiquetaDependencia_manejo?<?=$phpsession ?>&krd=<?=$krd?>&mode=<?=orfeo?> ' target='mainFrame' class="vinculos">4. TABLA DE ETIQUETAS PARA EXPEDIENTES POR DEPENDENCIA</a>
      </td>
  </tr>
  <tr bordercolor="#FFFFFF">    
      <td align="center" class="listado2" width="48%">
	<a href='http://localhost:8080/TiposDocumentales_manejo?<?=$phpsession ?>&krd=<?=$krd?>&mode=<?=orfeo?> ' target='mainFrame' class="vinculos">5. TABLA DE TIPOS DOCUMENTALES</a>
      </td>
  </tr>
   <tr bordercolor="#FFFFFF">    
      <td align="center" class="listado2" width="48%">
	<a href='http://localhost:8080/TiposPqr_manejo?<?=$phpsession ?>&krd=<?=$krd?>&mode=<?=orfeo?> ' target='mainFrame' class="vinculos">6. TABLA TIPOS PARA PQR</a>
      </td>
  </tr>  
</table>
</body>
</html>
