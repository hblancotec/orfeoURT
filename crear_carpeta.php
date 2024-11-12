<?php
session_start();
if (count($_SESSION) == 0) {
    die(include "./sinacceso.php");
    exit();
} else {
    extract($_GET, EXTR_SKIP);
    extract($_POST, EXTR_OVERWRITE);
    extract($_SESSION, EXTR_OVERWRITE);
}
require_once "./_conf/constantes.php";

if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php";
$usuaDoc = $_SESSION['usua_doc'];
$verrad = "";
define('ADODB_ASSOC_CASE', 1);
include_once "./include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);	 
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;
?>
<html>
<head>
<title>Crear Carpeta Personal</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link rel="stylesheet" href="estilos/orfeo.css">
</head>
<script language="javascript">
//Esta función de Javascript valida el texto introducido por el usuario y evita que este ingrese carácteres especiales
//Evitando de este modo el error que por esto se esta presentando
//Realizado por: Brayan Gabriel Plazas Riaño - DNP
//Fecha: 13 de Julio de 2005
function validar_nombre() {
    campos = document.form1.nombcarp.value.length;
var iChars = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚabcdefghijklmnñopqrstuvwxyzáéíóú_-1234567890";
  for (var i = 0; i < campos; i++) {
  	if ((iChars.indexOf(document.form1.nombcarp.value.charAt(i)) == -1)) {
  	alert ("El nombre de la carpeta tiene signos especiales. \n Por favor remueva estos signos especiales e intentelo de nuevo. Solamente puede contener Letras y Números.");
	document.form1.nombcarp.focus();
  	return false;
  	}
  }
}
</script>
<body bgcolor="#FFFFFF">
<form name='form1' method='post' action='crear_carpeta.php?<?=session_name()."=".trim(session_id())?>&krd=<?=$krd?>' <?php if(!$crear) echo "onSubmit='return validar_nombre()'" ?>>
<?php 
    session_start();
    if (!$_SESSION['dependencia']) {
        include "./rec_session.php";
    }
?>
	<table width="98%" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
		<tr>
		<td width='3%' class="listado2">
		  <a href='eliminar_carpeta.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>'>
		  <img src='iconos/carpeta_azul_eliminar.gif' border="0" Alt='Eliminar Carpetas'>Borrar Carpeta</A>
		</td>
		<td width='97%' class="titulos4" align="center">
		CREACI&Oacute;N DE CARPETAS
		</td>
		</tr>
		</table>
	<br>
<?php
  $nombcarp = trim($nombcarp);
  if(!$nombcarp and $crear) {
     echo "<center>DEBE ESCRIBIR UN NOMBRE DE CARPETA</CENTER>";
     $crear = "";
  }
  if(!$crear ) {
  echo "<table width='98%' border='0' cellpadding='0' cellspacing='5' class='borde_tab'>";
  echo "<tr> ";
  echo "      <td  class='titulos2' align='right'> ";
  echo "Nombre de carpeta</strong></td>";
  echo "      <td class='listado2' > ";
  echo "        <input name='nombcarp' type='text' class='tex_area' size='25' maxlength='10'></td>";
  echo "    </tr>";
   echo "   <tr> ";
  echo "      <td class='titulos2' align='right'>Descripci&oacute;n</td>";
  echo "      <td class='listado2'><input name='desccarp' type='text' class='tex_area' size='25' maxlength='30'></td>";
  echo "    </tr>";
  echo "    <tr> ";
  echo "      <td colspan='2'> ";
  echo "<div align='center'> ";
  echo "          <input type='Submit' class='botones' value='Crear Ahora!' name=crear>";
  echo "          <input type='hidden' value='$krd' name=krd>";  
  echo "          <input type='hidden' value='$contrax' name=contrax>";    
  echo "        </div></td>";
  echo "    </tr>";
  echo "  </table>";
  } else {
	 $isql = "SELECT CODI_CARP
                FROM carpeta_per 
                WHERE depe_codi=$dependencia AND
                        usua_codi=$codusuario AND
                        codi_carp!=99
                ORDER BY codi_carp desc ";
	 $rs=$db->conn->Execute($isql);
	 $isql = "SELECT CODI_CARP
                FROM carpeta_per
                WHERE depe_codi=$dependencia AND
                        usua_codi=$codusuario AND
                        codi_carp!=99 AND
                        nomb_carp='$nombcarp'
                ORDER BY codi_carp desc";
	 $rs1=$db->conn->Execute($isql);	 
	 $codigocarpeta = (intval($rs->fields["CODI_CARP"]) + 1);
    if ($codigocarpeta==99) {
        $codigocarpeta=100;
    } 
	 if ($rs1->EOF) {
		$isql = "INSERT INTO CARPETA_PER(codi_carp,
                                            depe_codi,
                                            usua_codi,
                                            nomb_carp,
                                            desc_carp,
                                            usua_doc)
	                          VALUES ($codigocarpeta,
                                        $dependencia,
                                        $codusuario,
                                        '$nombcarp',
                                        '$desccarp',
                                        '$usuaDoc')";
		$rs = $db->conn->Execute($isql);
		if($rs==-1)
            die("<center>No se ha podido crear la carpeta, Por favor intente mas tarde");
     	echo "<center></b><span class='info'>Creacion de la carpeta <b>$nombcarp</b> con exito</span> ";
		}
	else
		echo "<center><span class='alarmas'>No se ha podido crear la carpeta por Nombres Duplicados</span>";
  }
?>
</form> 
<table width='98%' border='0' cellpadding='0' cellspacing='5' class='borde_tab'>
<tr> 
    <td class="listado2_center" height="25">La descripci&oacute;n de la carpeta le recordara 
      el destino final de la misma. Esto se puede ver pasando el mouse sobre cada 
      una de las carpetas.
    </td>
  </tr>
</table>
</body>
</html>
