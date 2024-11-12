<?php
error_reporting(7);
$krdold = $krd;
session_start();
$ruta_raiz = "..";      
if(!$krd) $krd = $krdold;
error_reporting(7);
define('ADODB_FETCH_ASSOC',2);
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
include_once "$ruta_raiz/include/tx/Historico.php";
if ($db){
    $numRad = $_GET['numRad'];
        if(isset($_POST['btn_proc']))   {
               
                $Historico = new Historico($db);                 
                $codiRegE[0] = $numRad;
				
				$sql = "SELECT SGD_NOMBR_TRAM FROM SGD_TRAMITES WHERE SGD_ID_TRAM=".$_POST['slc_tram'];
				$nombre_tram = $db->conn->GetOne($sql);
                                               
                if ($_POST['btn_proc'] == "Borrar") {
                        $codiRegH = 82;
                        $observacion = "Eliminación tramite asignado ($nombre_tram).";
                       
                        $query = "UPDATE RADICADO SET SGD_ID_TRAM = null WHERE RADI_NUME_RADI=$numRad";
                        if($db->conn->Execute($query)) {
                                $msj = "<span class=leidos>La eliminaci&oacute;n del tr&aacute;mite se realiz&oacute; correctamente.";
                        }
                }
                if ($_POST['btn_proc'] == "Asignar") {
						
						$codiRegH = 81;
                        $observacion = "Actualizacion tramite ($nombre_tram)";
						
						$query = "UPDATE RADICADO SET SGD_ID_TRAM=".$_POST['slc_tram']." WHERE RADI_NUME_RADI=$numRad";
						if($db->conn->Execute($query))
						{
							$msj = "<span class=leidos>La asignaci&oacute;n del tr&aacute;mite se realiz&oacute; correctamente.";
						} else {
							$msj = "<span class=titulosError> !No se pudo actualizar el tr&aacute;mite!";
                        }
                }
                $radiModi = $Historico->insertarHistorico($codiRegE, $_SESSION['dependencia'], $_SESSION['codusuario'], $_SESSION['dependencia'], $_SESSION['codusuario'], $observacion, $codiRegH);
        }
       
        $ADODB_COUNTRECS = true;
		//$db->conn->debug = true;
        $sqlt = "SELECT SGD_ID_TRAM AS SGD_ID_TRAM FROM RADICADO WHERE RADI_NUME_RADI = $numRad";
        $rs_rad = $db->conn->Execute($sqlt);
        
        if($rs_rad->RecordCount() > 0) {
                $slcTram = $rs_rad->fields["SGD_ID_TRAM"];
                $ojoX = $slcTram . " $sqlt ";
                $tieneProc = ($slcTram) ? true : false;       
        } else {
                $msj = "<span class=titulosError> !Radicado no Existe!";
        }
       
        //crea combo de procesos
        $sql = "SELECT SGD_NOMBR_TRAM AS DESCRIP, SGD_ID_TRAM AS ID FROM SGD_TRAMITES WHERE SGD_DEPRE_TRAM=".$_SESSION['dependencia']."  ORDER BY 1";
        $rs_tram = $db->conn->Execute($sql);
        if ($rs_tram->RecordCount() > 0) {
                $slcTram = $rs_tram->GetMenu2('slc_tram', $slcTram, ":&gt;&gt; SELECCIONE &lt;&lt;", false, 0, " class='select' onchange='actualiza(this.value);' required ");
        }
        $ADODB_COUNTRECS = false;
} else {
        $msj = "<span class=titulosError> !No hay conexion a BD de Orfeo!";
}
?>
<html>
<head>
<title>Asignaci&oacute;n Tr&aacute;mite</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
<script>

function regresar(){    
        document.TipoDocu.submit();
}

function valValor(){
        if (document.frm_tramites.slc_tram.value=='') {
                alert('Debe seleccionar el tramite.');
                return false;
        }
}
</script>
</head>
<body bgcolor="#FFFFFF">
<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?krd=$krd&numRad=$numRad" ?>" name="frm_tramites" id="frm_tramites">
<table border=0 width=100% align="center" class="borde_tab" cellspacing="0">
<tr align="center" class="titulos2">
        <td height="15" class="titulos2" colspan="2" >APLICACI&Oacute;N TR&Aacute;MITE</td>
</tr>
<tr >
        <td class="titulos5" >Proceso</td>
        <td class=listado5 >
                <?=$slcTram?>
        </td>
</tr>
<tr>
        <td class="titulos5" colspan="2" >
                <center>
                        <?=$msj ?>
                </center>
        </td>
</tr>
<tr  align="center">
        <td class=listado5  align="left" colspan="2">
                <center>
                <input type="submit" class="botones" name="btn_proc" value="Asignar" onclick="return valValor();" title="Actualiza el radicado con el tramite gestionado.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                if ($tieneProc) {
                        echo "<input type='submit' class='botones' name='btn_proc' value='Borrar' title='Elimina el tramite asignado actualmente.'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                ?>
                <input name="Cerrar" type="button" class="botones" id="envia22" onClick="opener.regresar();window.close();"value="Cerrar">
                </center>
        </td>
</tr>
</table>
</form>
</body>
</html>